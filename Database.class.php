<?php

require 'User.class.php';
require 'vendor/autoload.php';

date_default_timezone_set('America/Los_Angeles');

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

class Database
{

    private $connection;

    function __construct()
    {
        try {
            $host = $_ENV['DB_HOST'];
            $database   = $_ENV['DB_DATABASE'];
            $username = $_ENV['DB_USERNAME'];
            $password = $_ENV['DB_PASSWORD'];
            $this->connection = new PDO('mysql:host=' . $host . ';dbname=' . $database, $username, $password, array(
                PDO::ATTR_PERSISTENT => true
            ));
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo '<strong>PDO MySQL Error: ' . $e->getMessage() . '</strong><br />';
        }
    }

    /*
     * 
     */
    function validateLogin($username, $password)
    {
        try {
            $statement = $this->connection->prepare(
                'SELECT password FROM `user` WHERE `username` = :username'
            );
            $statement->execute(array('username' => $username));
            $statement->setFetchMode(PDO::FETCH_ASSOC);
            $result = $statement->fetch();
            $actualPassword = $result['password'];

            return password_verify($password, $actualPassword) == 1 ? true : false;
        } catch (PDOException $e) {
            echo '<strong>PDO MySQL Error:</strong><br /> ' . $e->getMessage() . '<br />';
        }
    }

    

    /*
     * All functions relating to users
     */
    function getUserId($username)
    {
        try {
            $statement = $this->connection->prepare(
                'SELECT id FROM `user` WHERE `username` = :username'
            );
            $statement->execute(array('username' => $username));
            $result = $statement->fetch();

            return $result['id'];
        } catch (PDOException $e) {
            echo '<strong>PDO MySQL Error:</strong><br /> ' . $e->getMessage() . '<br />';
        }
        return null;
    }

    function getUserData($id, $print = false)
    {
        try {
            $statement = $this->connection->prepare(
                'SELECT * FROM `user`
                 INNER JOIN `user_access_role` ON (`user`.access_role_id = `user_access_role`.id) 
                 WHERE `user`.id = :id'
            );
            $statement->execute(array('id' => $id));
            $statement->setFetchMode(PDO::FETCH_NAMED);
            $result = $statement->fetch();
            if ($statement->rowCount() === 0) {
                return null;
            }
            if ($print) {
                echo '<pre>';
                print_r($result);
                echo '</pre>';
            }
            $data = array(
                'id' => $result['id'][0],
                'email' => $result['email'],
                'username' => $result['username'],
                'password' => $result['password'],
                'accessRole' => $result['role'],
                'firstName' => $result['first_name'],
                'lastName' => $result['last_name'],
                'phoneNumber' => $result['phone_number']
            );
            $user = new User();
            $user->set(json_encode($data));
            return $user;
        } catch (PDOException $e) {
            echo '<strong>PDO MySQL Error:</strong><br /> ' . $e->getMessage() . '<br />';
        }
    }


    function createUser($email, $username, $password, $accessRoleId, $firstName, $lastName, $id = null)
    {
        try {
            $statement = $this->connection->prepare(
                'INSERT INTO `user`(`id`, `email`, `username`, `password`, `access_role_id`, `first_name`, `last_name`) 
                 VALUES(:id, :email, :username, :password, :access_role_id, :first_name, :last_name)'
            );
            $statement->execute(array(
                ':id' => $id,
                ':email' => $email,
                ':username' => $username,
                ':password' => $password,
                ':access_role_id' => $accessRoleId,
                ':first_name' => $firstName,
                ':last_name' => $lastName,
            ));
        } catch (PDOException $e) {
            echo '<strong>PDO MySQL Error:</strong><br /> ' . $e->getMessage() . '<br />';
        }
    }

    //TODO
    function updateUser($user)
    {
        if (!($user instanceof User)) {
            echo 'failed to update user, the specified user is invalid:<br />';
            echo $user . '<br />';
            return;
        }
        try {
            $statement = $this->connection->prepare(
                'UPDATE `user` SET `username` = :username, `password` = :password, `email` = :email WHERE `username` = :username'
            );
            $statement->bindParam(':username', $user->username);
            $statement->bindParam(':password', $user->password);
            $statement->bindParam(':email', $user->email);
            $statement->execute();
        } catch (PDOException $e) {
            echo '<strong>PDO MySQL Error:</strong><br /> ' . $e->getMessage() . '<br />';
        }
    }

    //TODO
    function deleteUser($username)
    {
        try {
            $statement = $this->connection->prepare(
                'DELETE FROM `user` WHERE `username` = :username'
            );
            $statement->bindParam(':username', $username);
            $statement->execute();
        } catch (PDOException $e) {
            echo '<strong>PDO MySQL Error:</strong><br /> ' . $e->getMessage() . '<br />';
        }
    }

    /*
     * Retrieves the number of OS&D Claims currently in the database
     */
    function getOSDClaimCount($status = 0)
    {
        try {
            $statement = $this->connection->prepare(
                ($status == 0 ?
                'SELECT COUNT(*) FROM `claim`' :
                'SELECT COUNT(*) FROM `claim` WHERE `status_id` = :status_id')
            );
            $statement->bindParam(':status_id', $status);
            $statement->execute();
            $rowCount = $statement->fetchColumn();

            return $rowCount;
        } catch (PDOException $e) {
            echo '<strong>PDO MySQL Error:</strong><br /> ' . $e->getMessage() . '<br />';
        }
    }

    /*
     * Retrieves the number of OS&D Claims currently in the database
     */
    function getOSDClaimCountByTypeId($typeId)
    {
        try {
            $statement = $this->connection->prepare(
                'SELECT COUNT(*) FROM `claim` WHERE `type_id` = :type_id'
            );
            $statement->bindParam(':type_id', $typeId);
            $statement->execute();
            $rowCount = $statement->fetchColumn();

            return $rowCount;
        } catch (PDOException $e) {
            echo '<strong>PDO MySQL Error:</strong><br /> ' . $e->getMessage() . '<br />';
        }
    }

    /*
     * Retrieves the number of OS&D Claims currently in the database
     */
    function getOSDClaimCountByDriverId($typeId)
    {
        try {
            $statement = $this->connection->prepare(
                'SELECT COUNT(*) FROM `claim` WHERE `driver_id` = :type_id'
            );
            $statement->bindParam(':type_id', $typeId);
            $statement->execute();
            $rowCount = $statement->fetchColumn();

            return $rowCount;
        } catch (PDOException $e) {
            echo '<strong>PDO MySQL Error:</strong><br /> ' . $e->getMessage() . '<br />';
        }
    }

    /*
     * Retrieves the number of OS&D Claims currently in the database
     */
    function getOSDClaimCountByFacility($typeId)
    {
        try {
            $statement = $this->connection->prepare(
                'SELECT COUNT(*) FROM `claim` WHERE `facility_id` = :type_id'
            );
            $statement->bindParam(':type_id', $typeId);
            $statement->execute();
            $rowCount = $statement->fetchColumn();

            return $rowCount;
        } catch (PDOException $e) {
            echo '<strong>PDO MySQL Error:</strong><br /> ' . $e->getMessage() . '<br />';
        }
    }

    /*
     * Adds a claim to the OS&D Claims database
     */
    function addOSDClaim($date, $locationId, $driverId, $tripNumer, $fbNumber, $productCode, $trailerNumber, $cases, $typeId, $received, $statusId = 1)
    {
        try {
            $statement = $this->connection->prepare(
                'INSERT INTO `claim`(`date`, `location_id`, `driver_id`, `trip_number`, `freight_bill_number`, `product_code`, `trailer_number`, `cases`, `type_id`, `received`, `status_id`) 
                 VALUES(:date, :location_id, :driver_id, :trip_number, :freight_bill_number, :product_code, :trailer_number, :cases, :type_id, :received, :status_id)'
            );
            $statement->execute(array(
                ':date' => $date,
                ':location_id' => $locationId,
                ':driver_id' => $driverId,
                ':trip_number' => $tripNumer,
                ':freight_bill_number' => $fbNumber,
                ':product_code' => $productCode,
                ':trailer_number' => $trailerNumber,
                ':cases' => $cases,
                ':type' => $typeId,
                ':received' => $received,
                ':status_id' => $statusId
            ));
            echo 'Claim added successfully';
        } catch (PDOException $e) {
            echo '<strong>PDO MySQL Error:</strong><br /> ' . $e->getMessage() . '<br />';
        }
    }

    /*
     * Updates the status (Pending / Complete) of an OS&D Claim
     */
    function updateClaimStatus($id, $status)
    {
        try {
            $statement = $this->connection->prepare(
                'UPDATE `claim` SET `status_id` = :status WHERE `id` = :id'
            );
            $statement->execute(array(
                ':status' => $status,
                ':id' => $id
            ));
            echo 'Updated claim #' . $id . '<br />';
        } catch (PDOException $e) {
            echo '<strong>PDO MySQL Error:</strong><br /> ' . $e->getMessage() . '<br />';
        }
    }

    function getClaim($id)
    {
        try {
            $statement = $this->connection->prepare(
                'SELECT * FROM `claim` 
                 INNER JOIN `claim_status` ON (`claim`.status_id = `claim_status`.id) 
                 INNER JOIN `claim_type` ON (`claim`.type_id = `claim_type`.id) 
                 INNER JOIN `facility` ON (`claim`.facility_id = `facility`.id) 
                 WHERE `claim`.id = :id'
            );
            $statement->execute(array(':id' => $id));
            $result = $statement->fetch(PDO::FETCH_NAMED);
            return $result;
        } catch (PDOException $e) {
            echo '<strong>PDO MySQL Error:</strong><br /> ' . $e->getMessage() . '<br />';
        }
    }

    /*
     * Validates user login details against the database. If no results are found from given user and driver ID, login fails.
     */
    function getAllClaims()
    {
        try {
            $statement = $this->connection->prepare(
                'SELECT * FROM `claim` 
                 INNER JOIN `claim_status` ON (`claim`.status_id = `claim_status`.id) 
                 INNER JOIN `claim_type` ON (`claim`.type_id = `claim_type`.id) 
                 INNER JOIN `facility` ON (`claim`.facility_id = `facility`.id)'
            );
            $statement->execute();
            $result = $statement->fetchAll(PDO::FETCH_NAMED);
            return $result;
        } catch (PDOException $e) {
            echo '<strong>PDO MySQL Error:</strong><br /> ' . $e->getMessage() . '<br />';
        }
    }

    function getClaimsByStatusId($status) {
        try {
            $statement = $this->connection->prepare(
                'SELECT * FROM `claim` 
                 INNER JOIN `claim_status` ON (`claim`.status_id = `claim_status`.id) 
                 INNER JOIN `claim_type` ON (`claim`.type_id = `claim_type`.id) 
                 INNER JOIN `facility` ON (`claim`.facility_id = `facility`.id) 
                 WHERE `claim`.status_id = :status_id'
            );
            $statement->execute(array(':status_id' => $status));
            $result = $statement->fetchAll(PDO::FETCH_NAMED);
            return $result;
        } catch (PDOException $e) {
            echo '<strong>PDO MySQL Error:</strong><br /> ' . $e->getMessage() . '<br />';
        }
    }

    function getClaimsByDriverId($driver) {
        try {
            $statement = $this->connection->prepare(
                'SELECT * FROM `claim` 
                 INNER JOIN `claim_status` ON (`claim`.status_id = `claim_status`.id) 
                 INNER JOIN `claim_type` ON (`claim`.type_id = `claim_type`.id) 
                 INNER JOIN `facility` ON (`claim`.facility_id = `facility`.id) 
                 WHERE `claim`.driver_id = :driver_id'
            );
            $statement->execute(array(':driver_id' => $driver));
            $result = $statement->fetchAll(PDO::FETCH_NAMED);
            return $result;
        } catch (PDOException $e) {
            echo '<strong>PDO MySQL Error:</strong><br /> ' . $e->getMessage() . '<br />';
        }
    }

    function getClaimsByFacilityId($driver) {
        try {
            $statement = $this->connection->prepare(
                'SELECT * FROM `claim` 
                 INNER JOIN `claim_status` ON (`claim`.status_id = `claim_status`.id) 
                 INNER JOIN `claim_type` ON (`claim`.type_id = `claim_type`.id) 
                 INNER JOIN `facility` ON (`claim`.facility_id = `facility`.id) 
                 WHERE `claim`.facility_id = :driver_id'
            );
            $statement->execute(array(':driver_id' => $driver));
            $result = $statement->fetchAll(PDO::FETCH_NAMED);
            return $result;
        } catch (PDOException $e) {
            echo '<strong>PDO MySQL Error:</strong><br /> ' . $e->getMessage() . '<br />';
        }
    }

    function getFacility($id) {
        try {
            $statement = $this->connection->prepare(
                'SELECT * FROM `facility` 
                 WHERE `facility`.id = :id'
            );
            $statement->execute(array(':id' => $id));
            $result = $statement->fetch(PDO::FETCH_ASSOC);
            return $result;
        } catch (PDOException $e) {
            echo '<strong>PDO MySQL Error:</strong><br /> ' . $e->getMessage() . '<br />';
        }
    }
}
