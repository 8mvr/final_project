<?php

namespace app\Models;

use app\Database;
use PDO;

class Application
{
    private $conn;

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->connection();
    }

    /**
     * Insert an adoption application into the existing `adoption` table.
     * ($petid, $userid, $reason) — application_date is set to today, status to 'Pending'.
     */
    public function insertAdoption($petid, $userid, $reason)
    {
        $sql = "INSERT INTO adoption(userid, petid, application_date, status, reason)
                VALUES (?, ?, CURDATE(), 'Pending', ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$userid, $petid, $reason]);
        return "Application submitted";
    }

    /**
     * Insert a foster application into the existing `foster` table.
     * ($petid, $userid, $planned_start_date, $planned_end_date)
     */
    public function insertFoster($petid, $userid, $planned_start_date, $planned_end_date)
    {
        $sql = "INSERT INTO foster(userid, petid, application_date, planned_start_date, planned_end_date, status)
                VALUES (?, ?, CURDATE(), ?, ?, 'Pending')";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$userid, $petid, $planned_start_date, $planned_end_date]);
        return "Application submitted";
    }

    /** All adoption applications of one user (joined with pet info). */
    public function getAdoptionsByUser($userid)
    {
        $stmt = $this->conn->prepare(
            "SELECT a.*, p.name AS pet_name, p.breed, p.photo_url
             FROM adoption a INNER JOIN pet p ON a.petid = p.petid
             WHERE a.userid = ? ORDER BY a.application_date DESC, a.adoptionid DESC"
        );
        $stmt->execute([$userid]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** All foster applications of one user (joined with pet info). */
    public function getFostersByUser($userid)
    {
        $stmt = $this->conn->prepare(
            "SELECT f.*, p.name AS pet_name, p.breed, p.photo_url
             FROM foster f INNER JOIN pet p ON f.petid = p.petid
             WHERE f.userid = ? ORDER BY f.application_date DESC, f.fosterid DESC"
        );
        $stmt->execute([$userid]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** All adoption applications for one pet, for the admin view. */
    public function getAdoptionsByPet($petid)
    {
        $stmt = $this->conn->prepare(
            "SELECT a.*, u.full_name, u.email
             FROM adoption a INNER JOIN user u ON a.userid = u.userid
             WHERE a.petid = ? ORDER BY a.application_date DESC"
        );
        $stmt->execute([$petid]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** All foster applications for one pet, for the admin view. */
    public function getFostersByPet($petid)
    {
        $stmt = $this->conn->prepare(
            "SELECT f.*, u.full_name, u.email
             FROM foster f INNER JOIN user u ON f.userid = u.userid
             WHERE f.petid = ? ORDER BY f.application_date DESC"
        );
        $stmt->execute([$petid]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* ==================== ADMIN REVIEW ==================== */

    /** All adoption applications, with applicant + pet info, for the admin review list. */
    public function allAdoptions()
    {
        return $this->conn->query(
            "SELECT a.*, u.full_name, u.email, p.name AS pet_name, p.photo_url,
                    r.full_name AS reviewed_by_name
             FROM adoption a
             INNER JOIN user u ON a.userid = u.userid
             INNER JOIN pet p ON a.petid = p.petid
             LEFT JOIN user r ON a.reviewed_by = r.userid
             ORDER BY a.application_date DESC, a.adoptionid DESC"
        )->fetchAll(PDO::FETCH_ASSOC);
    }

    /** All foster applications, with applicant + pet info, for the admin review list. */
    public function allFosters()
    {
        return $this->conn->query(
            "SELECT f.*, u.full_name, u.email, p.name AS pet_name, p.photo_url,
                    r.full_name AS reviewed_by_name
             FROM foster f
             INNER JOIN user u ON f.userid = u.userid
             INNER JOIN pet p ON f.petid = p.petid
             LEFT JOIN user r ON f.reviewed_by = r.userid
             ORDER BY f.application_date DESC, f.fosterid DESC"
        )->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Approve / reject an adoption application. */
    public function reviewAdoption($adoptionid, $status, $reviewed_by)
    {
        $stmt = $this->conn->prepare(
            "UPDATE adoption SET status=?, reviewed_by=? WHERE adoptionid=?"
        );
        $stmt->execute([$status, $reviewed_by, $adoptionid]);
        return "Adoption application $status";
    }

    /** Approve / reject a foster application. */
    public function reviewFoster($fosterid, $status, $reviewed_by)
    {
        $stmt = $this->conn->prepare(
            "UPDATE foster SET status=?, reviewed_by=? WHERE fosterid=?"
        );
        $stmt->execute([$status, $reviewed_by, $fosterid]);
        return "Foster application $status";
    }
}
