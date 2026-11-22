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
                    r.full_name AS recorded_by_name
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
                    a.status, u.full_name, u.email, p.name AS pet_name,
                    p.photo_url, r.full_name AS recorded_by_name
             FROM adoption a
             INNER JOIN user u ON a.userid = u.userid
             INNER JOIN pet p ON a.petid = p.petid
             LEFT JOIN user r ON a.reviewed_by = r.userid
             WHERE LOWER(COALESCE(a.status, '')) IN ('approved', 'active', 'completed')"
        )->fetchAll(PDO::FETCH_ASSOC);

        foreach($adoptions as $adoption){
            $placements[] = [
                "placementid" => -(int)$adoption["adoptionid"],
                "userid" => (int)$adoption["userid"],
                "petid" => (int)$adoption["petid"],
                "placementtype" => "Adoption",
                "start_date" => $adoption["application_date"],
                "end_date" => null,
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
                    f.planned_start_date, f.planned_end_date, f.status,
                    u.full_name, u.email, p.name AS pet_name, p.photo_url,
                    r.full_name AS recorded_by_name
             FROM foster f
             INNER JOIN user u ON f.userid = u.userid
             INNER JOIN pet p ON f.petid = p.petid
             LEFT JOIN user r ON f.reviewed_by = r.userid
             WHERE LOWER(COALESCE(f.status, '')) IN ('approved', 'active', 'completed')"
        )->fetchAll(PDO::FETCH_ASSOC);

        foreach($fosters as $foster){
            $placements[] = [
                "placementid" => -(int)$foster["fosterid"],
                "userid" => (int)$foster["userid"],
                "petid" => (int)$foster["petid"],
                "placementtype" => "Foster",
                "start_date" => $foster["planned_start_date"] ?: $foster["application_date"],
                "end_date" => $foster["planned_end_date"],
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
    public function insert($userid, $petid, $adoptionid, $fosterid, $placementtype, $start_date, $end_date, $status, $recorded_by)
    {
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
