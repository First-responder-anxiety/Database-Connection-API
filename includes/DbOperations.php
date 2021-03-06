<?php 
    /*
        Author: Belal Khan
        Post: PHP Rest API Example using SLIM
    */

    class DbOperations{
        //the database connection variable
        private $con; 

        function __construct(){
            require_once dirname(__FILE__) . '/DbConnect.php';
            $db = new DbConnect; 
            $this->con = $db->connect(); 
        }


        /*  The Create Operation 
            The function will insert a new user in our database
        */
        public function createUser($username, $password, $f_name, $l_name){
           if(!$this->doesUserNameExist($username)){
                $stmt = $this->con->prepare("INSERT INTO user (user_name, password, first_name, last_name) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("ssss", $username, $password, $f_name, $l_name);
                if($stmt->execute()){
                    return USER_CREATED; 
                }else{
                    return USER_FAILURE;
                }
           }
           return USER_EXISTS; 
        }

        public function userLogin($user_name, $password){
            if($this->doesUserNameExist($user_name)){
                $hashed_password = $this->getPasswordByUserName($user_name);
                if(password_verify($password, $hashed_password)){
                    return USER_AUTHENTICATED;
                }else{
                    return USER_PASSWORD_DO_NOT_MATCH; 
                }
            } else {
                return USER_NOT_FOUND; 
            }
        }

        /*  
            The method is returning the password of a given user
            to verify the given password is correct or not
        */
        private function getPasswordByUserName($user_name){
            $stmt = $this->con->prepare("SELECT password FROM user WHERE user_name = ?");
            $stmt->bind_param("s", $user_name);
            $stmt->execute(); 
            $stmt->bind_result($password);
            $stmt->fetch(); 
            return $password; 
        }

        public function insertParent($user_name, $f_name, $l_name, $occupation) {
            if ($this->doesUserNameExist($user_name)) {
                $user_id = $this->getUserIdByUserName($user_name);

                $stmt = $this->con->prepare("INSERT INTO `parent`(`first_name`, `last_name`, `occupation`, `user_id`) 
                                             VALUES (?, ?, ?, ?)");
                $stmt->bind_param("sssi", $f_name, $l_name, $occupation, $user_id);
                if($stmt->execute()) {
                    return PARENT_CREATED;
                } else {
                    return PARENT_FAILURE;
                }

            }

            return USER_NOT_FOUND;
        }

        private function getUserIdByUserName($user_name) {
            $stmt = $this->con->prepare('SELECT user_id FROM user where user_name = ?');
            $stmt->bind_param("s", $user_name);
            $stmt->execute();
            $stmt->bind_result($user_id);
            $stmt->fetch();
            return $user_id;
        }

        public function getUserParents($user_name) {
            $user_id = $this->getUserIdByUserName($user_name);
            $stmt = $this->con->prepare('SELECT parent_id FROM parent WHERE user_id = ?');
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $stmt->bind_result($parent_id);
            $parent_ids = array();
            while($stmt->fetch()){ 
                $user = array(); 
                array_push($parent_ids, $parent_id);
            }    
            return $parent_ids;
        }

        public function insertShift($parent_id, $start_time, $end_time, $date) {
            if ($this->doesParentExist($parent_id)) {
                if (!$this->doesShiftPKExist($parent_id, $date)) {
                    $stmt = $this->con->prepare('INSERT INTO `shift`(`parent_id`, `start_time`, `end_time`, `date`) 
                                             VALUES (?, ?, ?, ?)');
                    $stmt->bind_param("isss", $parent_id, $start_time, $end_time, $date);
                    if ($stmt->execute()) {
                        return SHIFT_CREATED;
                    } else {
                        return SHIFT_FAILURE;
                    }
                } else {
                    return SHIFT_PK_EXISTS;
                }
            }
            return PARENT_NOT_EXISTS;
        }

        private function doesParentExist($parent_id) {
            $stmt = $this->con->prepare('SELECT * FROM parent WHERE parent_id = ?');
            $stmt->bind_param("i", $parent_id);
            $stmt->execute();
            $stmt->store_result();
            return $stmt->num_rows > 0;
        }

        private function doesShiftPKExist($parent_id, $date) {
            $stmt = $this->con->prepare('SELECT * FROM SHIFT where parent_id = ? AND date = ?');
            $stmt->bind_param("is", $parent_id, $date);
            $stmt->execute();
            $stmt->store_result();
            return $stmt->num_rows > 0;
        }

        public function insertData($user_name, $user_access_token, $fitness, $data_date) {
            if ($this->doesUserNameExist($user_name)) {
                $user_id = $this->getUserIdByUserName($user_name);

                $stmt = $this->con->prepare('INSERT INTO `user_data`(`user_id`, `user_access_token`, `fittness_age`, `data_date`) 
                                             VALUES (?, ?, ?, ?)');
                $stmt->bind_param("isis", $user_id, $user_access_token, $fitness, $data_date);
                if ($stmt->execute()) {
                    return DATA_CREATED;
                } else {
                    return DATA_ERROR;
                }
            }
            return USER_NOT_FOUND;
        }

        public function insertStressData($data_id, $duration, $average_stress_level, $max_stress_level, 
        $high_stress_duration, $medium_stress_duration, $low_stress_duration) {
            if ($this->doesDataExist($data_id)) {
                if (!$this->doesStressDataExist($data_id)) {
                    $stmt = $this->con->prepare('INSERT INTO `stress_data`(`data_id`, `duration`, `average_stress_level`, `max_stress_level`, `high_stress_duration`, `medium_stress_duration`, `low_stress_duration`) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)');
                    $stmt->bind_param("iiiiiii", $data_id, $duration, $average_stress_level, $max_stress_level, $high_stress_duration, $medium_stress_duration, $low_stress_duration);
                    if ($stmt->execute()) {
                        return DATA_CREATED;
                    } else {
                        return DATA_ERROR;
                    }
                } else {
                    return DATA_EXISTS;
                }
            }
            return DATA_NOT_FOUND;
        }

        private function doesDataExist($data_id) {
            $stmt = $this->con->prepare('SELECT * FROM user_data where data_id = ?');
            $stmt->bind_param("i", $data_id);
            $stmt->execute();
            $stmt->store_result();
            return $stmt->num_rows > 0;
        }

        private function doesStressDataExist($data_id) {
            $stmt = $this->con->prepare('SELECT * FROM stress_data where data_id = ?');
            $stmt->bind_param("i", $data_id);
            $stmt->execute();
            $stmt->store_result();
            return $stmt->num_rows > 0;
        }

        public function insertSleepData($data_id, $total_sleep_time, $deep_sleep_time, $light_sleep_time, $awake_time) {
            if ($this->doesDataExist($data_id)) {
                if (!$this->doesSleepDataExist($data_id)) {
                    $stmt = $this->con->prepare('INSERT INTO `sleep_data`(`data_id`, `total_sleep_time`, `deep_sleep_time`, `light_sleep_time`, `awake_time`) 
                                                 VALUES (?, ?, ?, ?, ?)');
                    $stmt->bind_param("iiiii", $data_id, $total_sleep_time, $deep_sleep_time, $light_sleep_time, $awake_time);
                    if ($stmt->execute()) {
                        return DATA_CREATED;
                    } else {
                        return DATA_ERROR;
                    }
                } else {
                    return DATA_EXISTS;
                }
            }
            return DATA_NOT_FOUND;
        }

        private function doesSleepDataExist($data_id) {
            $stmt = $this->con->prepare('SELECT * FROM sleep_data where data_id = ?');
            $stmt->bind_param("i", $data_id);
            $stmt->execute();
            $stmt->store_result();
            return $stmt->num_rows > 0;
        }

        public function insertHeartRateData($data_id, $average_heart_rate, $max_heart_rate, $min_heart_rate, $resting_heart_rate) {
            if ($this->doesDataExist($data_id)) {
                if (!$this->doesHeartRateDataExist($data_id)) {
                    $stmt = $this->con
                                 ->prepare('INSERT INTO `heart_rate_data`(`data_id`, `average_heart_rate`, `max_heart_rate`, `min_heart_rate`, `resting_heart_rate`) 
                                            VALUES (?, ?, ?, ?, ?)');
                    $stmt->bind_param("iiiii", $data_id, $average_heart_rate, $max_heart_rate, $min_heart_rate, $resting_heart_rate);
                    if ($stmt->execute()) {
                        return DATA_CREATED;
                    } else {
                        return DATA_ERROR;
                    }
                } else {
                    return DATA_EXISTS;
                }
            }
            return DATA_NOT_FOUND;
        }

        private function doesHeartRateDataExist($data_id) {
            $stmt = $this->con->prepare('SELECT * FROM heart_rate_data where data_id = ?');
            $stmt->bind_param("i", $data_id);
            $stmt->execute();
            $stmt->store_result();
            return $stmt->num_rows > 0;
        }

        public function getUserData($username) {
            $user_id = $this->getUserIdByUserName($username);

            $stmt = $this->con->prepare('SELECT data_id FROM user_data WHERE user_id = ?');
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $stmt->bind_result($parent_id);
            $data_ids = array();
            while($stmt->fetch()){ 
                $user = array(); 
                array_push($data_ids, $parent_id);
            }    
            return $data_ids;
        }


        /*
            The Read Operation
            Function is returning all the users from database
        */
        public function getAllUsers(){
            $stmt = $this->con->prepare("SELECT id, email, name, school FROM users;");
            $stmt->execute; 
            $stmt->bind_result($id, $email, $name, $school);
            $users = array(); 
            while($stmt->fetch()){ 
                $user = array(); 
                $user['id'] = $id; 
                $user['email']=$email; 
                $user['name'] = $name; 
                $user['school'] = $school; 
                array_push($users, $user);
            }             
            return $users; 
        }

        /*
            The Update Operation
            This function will update the password for a specified user
            NOTE: OUTDATED FUNCTION. DO NOT USE UNTILL UPDATED
        */
        public function updatePassword($currentpassword, $newpassword, $email){
            $hashed_password = $this->getPasswordByUserName($email);
            
            if(password_verify($currentpassword, $hashed_password)){
                
                $hash_password = password_hash($newpassword, PASSWORD_DEFAULT);
                $stmt = $this->con->prepare("UPDATE users SET password = ? WHERE email = ?");
                $stmt->bind_param("ss",$hash_password, $email);
                if($stmt->execute())
                    return PASSWORD_CHANGED;
                return PASSWORD_NOT_CHANGED;
            }else{
                return PASSWORD_DO_NOT_MATCH; 
            }
        }

        /*
            The Delete Operation
            This function will delete the user from database
        */
        public function deleteUser($id){
            $stmt = $this->con->prepare("DELETE FROM user WHERE user_id = ?");
            $stmt->bind_param("i", $id);
            if($stmt->execute())
                return true; 
            return false; 
        }

        /**
         * Check if the username is already taken
         */
        private function doesUserNameExist($user_name){
            $stmt = $this->con->prepare("SELECT * FROM user WHERE user_name = ?");
            $stmt->bind_param("s", $user_name);
            $stmt->execute();
            $stmt->store_result();
            return $stmt->num_rows > 0;
        }
    }