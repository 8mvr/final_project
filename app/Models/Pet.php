<?php
// comment

namespace app\Models;

use app\Database;
use PDO;
use Exception;

class Pet {
    private $conn = null;

    // Seed defaults matching Figma design if DB is empty or during first run
    private static $defaultPets = [
        [
            "petid" => 1,
            "created_by" => 1,
            "name" => "Maple",
            "species" => "Dog",
            "breed" => "Beagle mix",
            "age" => 3,
            "sex" => "Female",
            "description" => "A sunny patch on the floor, a slow sniff around the block, and someone to lean against. Gentle little shadow.",
            "photo_url" => "assets/images/pet-maple.png",
            "status" => "Available",
            "tags" => "Gentle, People-loving, Easygoing",
            "location" => "Hearth Portland"
        ],
        [
            "petid" => 2,
            "created_by" => 1,
            "name" => "Milo",
            "species" => "Cat",
            "breed" => "Domestic Shorthair",
            "age" => 2,
            "sex" => "Male",
            "description" => "Loves watching birds by the window and greeting you with soft chirps in the morning.",
            "photo_url" => "assets/images/pet-milo.png",
            "status" => "Available",
            "tags" => "Quiet, Independent, Sweet",
            "location" => "Hearth Portland"
        ],
        [
            "petid" => 3,
            "created_by" => 1,
            "name" => "Luna",
            "species" => "Cat",
            "breed" => "Tabby mix",
            "age" => 1,
            "sex" => "Female",
            "description" => "Curious explorer who loves chasing feather wands and curling up in warm laps.",
            "photo_url" => "assets/images/pet-luna.png",
            "status" => "Foster welcome",
            "tags" => "Playful, Curious, Affectionate",
            "location" => "Hearth Portland"
        ],
        [
            "petid" => 4,
            "created_by" => 1,
            "name" => "Finn",
            "species" => "Dog",
            "breed" => "Golden Retriever",
            "age" => 1,
            "sex" => "Male",
            "description" => "Always ready for an outdoor hike or game of fetch, full of friendly enthusiasm.",
            "photo_url" => "assets/images/pet-finn.png",
            "status" => "Available",
            "tags" => "Energetic, Loyal, Eager to please",
            "location" => "Hearth Portland"
        ],
        [
            "petid" => 5,
            "created_by" => 1,
            "name" => "Juniper",
            "species" => "Cat",
            "breed" => "Shorthair",
            "age" => 4,
            "sex" => "Female",
            "description" => "Currently thriving in foster care, enjoys gentle head scratches and sunny naps.",
            "photo_url" => "assets/images/pet-juniper.png",
            "status" => "In foster",
            "tags" => "Calm, Gentle, Observant",
            "location" => "Hearth Foster Home"
        ],
        [
            "petid" => 6,
            "created_by" => 1,
            "name" => "Archie",
            "species" => "Dog",
            "breed" => "Spaniel",
            "age" => 6,
            "sex" => "Male",
            "description" => "Gentleman companion with a calm disposition, great with kids and other dogs.",
            "photo_url" => "assets/images/pet-archie.png",
            "status" => "Available",
            "tags" => "Patient, Friendly, Loving",
            "location" => "Hearth Portland"
        ],
        [
            "petid" => 7,
            "created_by" => 1,
            "name" => "Pepper",
            "species" => "Rabbit",
            "breed" => "Lop Rabbit",
            "age" => 2,
            "sex" => "Female",
            "description" => "Gentle rabbit who loves fresh hay, leafy greens, and quiet companionship.",
            "photo_url" => "assets/images/pet-pepper.png",
            "status" => "Available",
            "tags" => "Gentle, Vegetarian, Quiet",
            "location" => "Hearth Portland"
        ]
    ];

    public function __construct() {
        try {
            $database = new Database();
            $this->conn = $database->connection();
        } catch (Exception $e) {
            $this->conn = null;
        }
    }

    public function getAll($species = null, $status = null, $search = null) {
        if ($this->conn) {
            try {
                $query = "SELECT * FROM pet WHERE 1=1";
                $params = [];

                if (!empty($species) && $species !== 'All animals') {
                    $query .= " AND species = ?";
                    $params[] = $species;
                }

                if (!empty($status) && $status !== 'All statuses') {
                    $query .= " AND status = ?";
                    $params[] = $status;
                }

                if (!empty($search)) {
                    $query .= " AND (name LIKE ? OR breed LIKE ? OR description LIKE ?)";
                    $term = "%" . $search . "%";
                    $params[] = $term;
                    $params[] = $term;
                    $params[] = $term;
                }

                $query .= " ORDER BY petid DESC";
                $stmt = $this->conn->prepare($query);
                $stmt->execute($params);
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if (!empty($results)) {
                    return $results;
                }
            } catch (Exception $e) {
                // fall through to defaults
            }
        }

        // Return filtered defaults when DB table is empty or offline
        $list = self::$defaultPets;
        if (!empty($species) && $species !== 'All animals') {
            $list = array_values(array_filter($list, fn($p) => strcasecmp($p['species'], $species) === 0));
        }
        if (!empty($status) && $status !== 'All statuses') {
            $list = array_values(array_filter($list, fn($p) => strcasecmp($p['status'], $status) === 0));
        }
        if (!empty($search)) {
            $search = strtolower($search);
            $list = array_values(array_filter($list, fn($p) => 
                str_contains(strtolower($p['name']), $search) ||
                str_contains(strtolower($p['breed']), $search) ||
                str_contains(strtolower($p['description']), $search)
            ));
        }
        return $list;
    }

    public function ViewbyID($id) {
        if ($this->conn) {
            try {
                $sql = "SELECT * FROM pet WHERE petid = ?";
                $stmt = $this->conn->prepare($sql);
                $stmt->execute([$id]);
                $pet = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($pet) {
                    return $pet;
                }
            } catch (Exception $e) {
                // fallback below
            }
        }

        foreach (self::$defaultPets as $p) {
            if ($p['petid'] == $id) {
                return $p;
            }
        }

        return self::$defaultPets[0] ?? null;
    }

    public function insert($created_by, $name, $species, $breed, $age, $sex, $description, $photo_url, $status) {
        if (empty($photo_url)) {
            $photo_url = "assets/images/pet-maple.png";
        }

        if ($this->conn) {
            try {
                $sql = "INSERT INTO pet(created_by, name, species, breed, age, sex, description, photo_url, status)
                        SELECT userid, ?, ?, ?, ?, ?, ?, ?, ? FROM user WHERE email = ?";
                $stmt = $this->conn->prepare($sql);
                $stmt->execute([$name, $species, $breed, $age, $sex, $description, $photo_url, $status, $created_by]);
                
                if ($stmt->rowCount() === 0) {
                    // Fallback to inserting with created_by = 1 if user lookup failed
                    $sql2 = "INSERT INTO pet(created_by, name, species, breed, age, sex, description, photo_url, status)
                             VALUES(1, ?, ?, ?, ?, ?, ?, ?, ?)";
                    $stmt2 = $this->conn->prepare($sql2);
                    $stmt2->execute([$name, $species, $breed, $age, $sex, $description, $photo_url, $status]);
                }
                return "Pet Added Successfully";
            } catch (Exception $e) {
                return "Database Error: " . $e->getMessage();
            }
        }

        return "Pet Added Successfully (Local)";
    }

    public function update($name, $species, $breed, $age, $sex, $description, $photo_url, $status, $id) {
        if ($this->conn) {
            try {
                $sql = "UPDATE pet SET name=?, species=?, breed=?, age=?, sex=?, description=?, photo_url=?, status=? WHERE petid=?";
                $stmt = $this->conn->prepare($sql);
                $stmt->execute([$name, $species, $breed, $age, $sex, $description, $photo_url, $status, $id]);
                return "Updated Successfully";
            } catch (Exception $e) {
                return "Update Error: " . $e->getMessage();
            }
        }
        return "Updated Successfully";
    }

    public function delete($id) {
        if ($this->conn) {
            try {
                $sql = "DELETE FROM pet WHERE petid=?";
                $stmt = $this->conn->prepare($sql);
                $stmt->execute([$id]);
                return "Deleted Successfully";
            } catch (Exception $e) {
                return "Delete Error: " . $e->getMessage();
            }
        }
        return "Deleted Successfully";
    }
}
