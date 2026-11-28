<?php

namespace app\Models;

use app\Database;
use PDO;

class Placement
{
    private $conn;

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->connection();
    }

    /** All placements with pet and person names. */
    public function getAll()
    {
        $placements = $this->conn->query(
            "SELECT pl.*, p.name AS pet_name, p.photo_url, u.full_name, u.email,
                    r.full_name AS recorded_by_name,
                    (SELECT ap.appointment_datetime
                     FROM appointment ap
                     WHERE ap.userid = pl.userid AND ap.petid = pl.petid
                       AND (
                           (pl.adoptionid IS NOT NULL AND ap.adoptionid = pl.adoptionid)
                           OR (pl.fosterid IS NOT NULL AND ap.fosterid = pl.fosterid)
                           OR (pl.adoptionid IS NULL AND pl.fosterid IS NULL
                               AND ap.adoptionid IS NULL AND ap.fosterid IS NULL)
                       )
                     ORDER BY ap.appointmentid DESC LIMIT 1) AS appointment_datetime
             FROM placement pl
             INNER JOIN pet p ON pl.petid = p.petid
             INNER JOIN user u ON pl.userid = u.userid
             LEFT JOIN user r ON pl.recorded_by = r.userid
             ORDER BY pl.placementid DESC"
        )->fetchAll(PDO::FETCH_ASSOC);

        foreach($placements as &$placement){
            $placement["record_source"] = "placement";
        }
        unset($placement);

        $adoptions = $this->conn->query(
            "SELECT a.adoptionid, a.userid, a.petid, a.application_date,
                    (SELECT ap.appointment_datetime FROM appointment ap
                     WHERE ap.adoptionid = a.adoptionid
                     ORDER BY ap.appointmentid DESC LIMIT 1) AS appointment_datetime,
                    a.status, u.full_name, u.email, p.name AS pet_name,
                    p.photo_url, r.full_name AS recorded_by_name
             FROM adoption a
             INNER JOIN user u ON a.userid = u.userid
             INNER JOIN pet p ON a.petid = p.petid
             LEFT JOIN user r ON a.reviewed_by = r.userid
             WHERE LOWER(COALESCE(a.status, '')) IN ('approved', 'active', 'completed')
               AND NOT EXISTS (SELECT 1 FROM placement pl WHERE pl.adoptionid = a.adoptionid)"
        )->fetchAll(PDO::FETCH_ASSOC);

        foreach($adoptions as $adoption){
            $placements[] = [
                "placementid" => -(int)$adoption["adoptionid"],
                "userid" => (int)$adoption["userid"],
                "petid" => (int)$adoption["petid"],
                "placementtype" => "Adoption",
                "start_date" => $adoption["application_date"],
                "end_date" => null,
                "appointment_datetime" => $adoption["appointment_datetime"],
                "status" => $adoption["status"] ?: "Active",
                "pet_name" => $adoption["pet_name"],
                "photo_url" => $adoption["photo_url"],
                "full_name" => $adoption["full_name"],
                "email" => $adoption["email"],
                "recorded_by_name" => $adoption["recorded_by_name"],
                "record_source" => "adoption"
            ];
        }

        $fosters = $this->conn->query(
            "SELECT f.fosterid, f.userid, f.petid, f.application_date,
                    (SELECT ap.appointment_datetime FROM appointment ap
                     WHERE ap.fosterid = f.fosterid
                     ORDER BY ap.appointmentid DESC LIMIT 1) AS appointment_datetime,
                    f.planned_start_date, f.planned_end_date, f.status,
                    u.full_name, u.email, p.name AS pet_name, p.photo_url,
                    r.full_name AS recorded_by_name
             FROM foster f
             INNER JOIN user u ON f.userid = u.userid
             INNER JOIN pet p ON f.petid = p.petid
             LEFT JOIN user r ON f.reviewed_by = r.userid
             WHERE LOWER(COALESCE(f.status, '')) IN ('approved', 'active', 'completed')
               AND NOT EXISTS (SELECT 1 FROM placement pl WHERE pl.fosterid = f.fosterid)"
        )->fetchAll(PDO::FETCH_ASSOC);

        foreach($fosters as $foster){
            $placements[] = [
                "placementid" => -(int)$foster["fosterid"],
                "userid" => (int)$foster["userid"],
                "petid" => (int)$foster["petid"],
                "placementtype" => "Foster",
                "start_date" => $foster["planned_start_date"] ?: $foster["application_date"],
                "end_date" => $foster["planned_end_date"],
                "appointment_datetime" => $foster["appointment_datetime"],
                "status" => $foster["status"] ?: "Active",
                "pet_name" => $foster["pet_name"],
                "photo_url" => $foster["photo_url"],
                "full_name" => $foster["full_name"],
                "email" => $foster["email"],
                "recorded_by_name" => $foster["recorded_by_name"],
                "record_source" => "foster"
            ];
        }

        usort($placements, function($a, $b){
            return strcmp((string)($b["start_date"] ?: ""), (string)($a["start_date"] ?: ""));
        });

        return $placements;
    }

    /** Users + pets + approved adoptions/fosters, for the "new placement" dropdowns. */
    public function options()
    {
        $users = $this->conn->query("SELECT userid, full_name, email FROM user ORDER BY full_name")->fetchAll(PDO::FETCH_ASSOC);
        $pets  = $this->conn->query("SELECT petid, name, status FROM pet ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
        return ["users" => $users, "pets" => $pets];
    }

    /** Create a placement record. */
    public function insert($userid, $petid, $adoptionid, $fosterid, $placementtype, $start_date, $end_date, $status, $recorded_by, $appointment_datetime = null)
    {
        $this->conn->beginTransaction();
        try{
            if($adoptionid){
                $application = $this->conn->prepare(
                    "SELECT petid, status FROM adoption WHERE adoptionid=? FOR UPDATE"
                );
                $application->execute([$adoptionid]);
                $applicationRow = $application->fetch(PDO::FETCH_ASSOC);
                if(
                    !$applicationRow
                    || (int)$applicationRow["petid"] !== (int)$petid
                    || !in_array(strtolower(trim($applicationRow["status"] ?? "")), ["pending", "approved"], true)
                ){
                    throw new \RuntimeException("The adoption application is no longer available for placement.");
                }
                $existingPlacement = $this->conn->prepare("SELECT placementid FROM placement WHERE adoptionid=? LIMIT 1");
                $existingPlacement->execute([$adoptionid]);
                if($existingPlacement->fetchColumn()){
                    throw new \RuntimeException("A placement already exists for this application.");
                }
                $approve = $this->conn->prepare(
                    "UPDATE adoption SET status='Approved', reviewed_by=? WHERE adoptionid=?"
                );
                $approve->execute([$recorded_by, $adoptionid]);
                $this->conn->prepare("UPDATE pet SET status='Not Available' WHERE petid=?")->execute([$petid]);
            }elseif($fosterid){
                $application = $this->conn->prepare(
                    "SELECT petid, status FROM foster WHERE fosterid=? FOR UPDATE"
                );
                $application->execute([$fosterid]);
                $applicationRow = $application->fetch(PDO::FETCH_ASSOC);
                if(
                    !$applicationRow
                    || (int)$applicationRow["petid"] !== (int)$petid
                    || !in_array(strtolower(trim($applicationRow["status"] ?? "")), ["pending", "approved"], true)
                ){
                    throw new \RuntimeException("The foster application is no longer available for placement.");
                }
                $existingPlacement = $this->conn->prepare("SELECT placementid FROM placement WHERE fosterid=? LIMIT 1");
                $existingPlacement->execute([$fosterid]);
                if($existingPlacement->fetchColumn()){
                    throw new \RuntimeException("A placement already exists for this application.");
                }
                $approve = $this->conn->prepare(
                    "UPDATE foster SET status='Approved', reviewed_by=? WHERE fosterid=?"
                );
                $approve->execute([$recorded_by, $fosterid]);
                $this->conn->prepare("UPDATE pet SET status='Foster' WHERE petid=?")->execute([$petid]);
            }

            $sql = "INSERT INTO placement(userid,petid,adoptionid,fosterid,recorded_by,placementtype,start_date,end_date,status)
                    VALUES (?,?,?,?,?,?,?,?,?)";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                $userid,
                $petid,
                $adoptionid ?: null,
                $fosterid ?: null,
                $recorded_by,
                $placementtype,
                $start_date ?: null,
                $end_date ?: null,
                $status
            ]);

            if($appointment_datetime){
                $appointmentId = null;
                if($adoptionid){
                    $existingAppointment = $this->conn->prepare(
                        "SELECT appointmentid FROM appointment WHERE adoptionid=? ORDER BY appointmentid DESC LIMIT 1 FOR UPDATE"
                    );
                    $existingAppointment->execute([$adoptionid]);
                    $appointmentId = $existingAppointment->fetchColumn();
                }elseif($fosterid){
                    $existingAppointment = $this->conn->prepare(
                        "SELECT appointmentid FROM appointment WHERE fosterid=? ORDER BY appointmentid DESC LIMIT 1 FOR UPDATE"
                    );
                    $existingAppointment->execute([$fosterid]);
                    $appointmentId = $existingAppointment->fetchColumn();
                }

                if($appointmentId){
                    $appointment = $this->conn->prepare(
                        "UPDATE appointment
                         SET userid=?, petid=?, appointment_datetime=?, status='Scheduled'
                         WHERE appointmentid=?"
                    );
                    $appointment->execute([$userid, $petid, $appointment_datetime, $appointmentId]);
                }else{
                    $appointment = $this->conn->prepare(
                        "INSERT INTO appointment(userid,petid,adoptionid,fosterid,appointment_datetime,purpose,status)
                         VALUES (?,?,?,?,?,'Placement visit','Scheduled')"
                    );
                    $appointment->execute([
                        $userid,
                        $petid,
                        $adoptionid ?: null,
                        $fosterid ?: null,
                        $appointment_datetime
                    ]);
                }
            }

            $this->conn->commit();
        }catch(\Throwable $error){
            if($this->conn->inTransaction()){
                $this->conn->rollBack();
            }
            throw $error;
        }
        return "Placement recorded";
    }

    /** Update a placement record. */
    public function update($placementid, $placementtype, $start_date, $end_date, $status)
    {
        $sql = "UPDATE placement SET placementtype=?, start_date=?, end_date=?, status=? WHERE placementid=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$placementtype, $start_date ?: null, $end_date ?: null, $status, $placementid]);
        return "Placement updated";
    }

    public function delete($placementid)
    {
        $stmt = $this->conn->prepare("DELETE FROM placement WHERE placementid=?");
        $stmt->execute([$placementid]);
        return "Placement deleted";
    }

    /** All appointments with names. */
    public function appointments()
    {
        return $this->conn->query(
            "SELECT ap.*, p.name AS pet_name, u.full_name, u.email
             FROM appointment ap
             INNER JOIN pet p ON ap.petid = p.petid
             INNER JOIN user u ON ap.userid = u.userid
             ORDER BY ap.appointment_datetime DESC, ap.appointmentid DESC"
        )->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Create an appointment. */
    public function insertAppointment($userid, $petid, $adoptionid, $fosterid, $appointment_datetime, $purpose, $status)
    {
        $sql = "INSERT INTO appointment(userid,petid,adoptionid,fosterid,appointment_datetime,purpose,status)
                VALUES (?,?,?,?,?,?,?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            $userid,
            $petid,
            $adoptionid ?: null,
            $fosterid ?: null,
            $appointment_datetime,
            $purpose,
            $status
        ]);
        return "Appointment scheduled";
    }

    /** Update an appointment (datetime / purpose / status). */
    public function updateAppointment($appointmentid, $appointment_datetime, $purpose, $status)
    {
        $sql = "UPDATE appointment SET appointment_datetime=?, purpose=?, status=? WHERE appointmentid=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$appointment_datetime, $purpose, $status, $appointmentid]);
        return "Appointment updated";
    }

    public function deleteAppointment($appointmentid)
    {
        $stmt = $this->conn->prepare("DELETE FROM appointment WHERE appointmentid=?");
        $stmt->execute([$appointmentid]);
        return "Appointment deleted";
    }
}
