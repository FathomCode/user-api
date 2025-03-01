<?php

include_once '../Database.php';
include_once '../Api.php';

$database = new Database();
$db = $database->getConnection();


$request_method = $_SERVER["REQUEST_METHOD"];

 
class UserApi extends Api
{
    public $apiName = 'user';
 
    /**
     * Метод GET
     * GET User Info
     * @return string
     */
    public function getAction()
    {	
		$database = new Database();
		$db = $database->getConnection();

        if(isset($this->requestUri[0]) && is_numeric($this->requestUri[0])){

            $stmt = $db->prepare("SELECT username, email, first_name, last_name FROM users WHERE id = :id");
            $stmt->bindParam(':id', $this->requestUri[0]);
            $stmt->execute();

            $user_data = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($user_data) {
                return $this->response($user_data, 200);    
            } else {
                return $this->response('Incorrect data', 400);
            }
        }
        return $this->response('Incorrect data', 400);
    }

    /**
     * Метод POST
     * Register
     * @return string
     */
    public function createAction()
    {   
        $data = json_decode(file_get_contents("php://input"), true);

        $database = new Database();
        $db = $database->getConnection();
        if($this->method == 'POST') {
            $username = $data['username'] ?? '';
            $email = $data['email'] ?? '';
            $password = $data['password'] ?? '';
            $first_name = $data['first_name'] ?? '';
            $last_name = $data['last_name'] ?? '';

            if (empty($username)) $messages[] = 'username is required';
            if (empty($email)) $messages[] = 'email is required';
            if (empty($password)) $messages[] = 'password is required';
            if (empty($first_name)) $messages[] = 'first_name is required';
            if (empty($last_name)) $messages[] = 'last_name is required';


           if (empty($messages)) {
                $stmt = $db->prepare("SELECT * FROM users WHERE username = :username OR email = :email");
                $stmt->bindParam(':username', $username);
                $stmt->bindParam(':email', $email);
                $stmt->execute();

                if ($stmt->rowCount() > 0) {
                    return $this->response('Username or email already exists', 400);
                } else {
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $db->prepare("INSERT INTO users (username, password, email, first_name, last_name) VALUES (:username, :password, :email, :first_name, :last_name)");

                    $stmt->bindParam(':username', $username);
                    $stmt->bindParam(':password', $hashed_password);
                    $stmt->bindParam(':email', $email);
                    $stmt->bindParam(':first_name', $first_name);
                    $stmt->bindParam(':last_name', $last_name);
                    
                    if ($stmt->execute()) {
                        return $this->response('Register success', 200);
                    } else {
                        return $this->response('Register error', 400);
                    }
                    
                }
           } else {
                return $this->response('Error in data', 400);
           }
        } else {
            return $this->response('Error method', 400);
        }
    }

    /**
     * Метод POST
     * Auth
     * @return string
     */
    public function authAction()
    {   
        $data = json_decode(file_get_contents("php://input"), true);

        $database = new Database();
        $db = $database->getConnection();
        if($this->method == 'POST') {
            $username = $data['username'] ?? '';
            $password = $data['password'] ?? '';

            if (empty($username)) $messages[] = 'username is required';
            if (empty($password)) $messages[] = 'password is required';


           if (empty($messages)) {

                $stmt = $db->prepare("SELECT * FROM users WHERE username = :username");
                $stmt->bindParam(':username', $username);
                $stmt->execute();
                $user_data = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($user_data && password_verify($password, $user_data['password'])) {
                    $api_key = bin2hex(random_bytes(18));
                    $expired_at = date("Y-m-d H:i:s", strtotime("+1 days"));

                    $stmt = $db->prepare("UPDATE `users` SET api_key = :api_key, expired_at = :expired_at  WHERE `username` = :username");
                
                    $stmt->bindParam(':api_key', $api_key);
                    $stmt->bindParam(':expired_at', $expired_at);

                    $stmt->bindParam(':username', $username);
                    
                    if ($stmt->execute()) {
                        return $this->response([
                            'messages' => "Login success",
                            'user_id' => $user_data['id'],
                            'username' => $username,
                            'api_key' => $api_key,
                            'expired_at' => $expired_at
                        ], 200);
                    } else {
                        return $this->response('Login error', 400);
                    }
                } else {
                    return $this->response('Incorrect username or password', 400);
                }
           } else {
                return $this->response('Error in data', 400);
           }
        } else {
            return $this->response('Error method', 400);
        }
    }

    /**
     * Метод PUT
     * Change data
     * @return string
     */
    public function changeAction()
    {   
        $data = json_decode(file_get_contents("php://input"), true);

        $database = new Database();
        $db = $database->getConnection();
        if($this->method == 'PUT') {
            $api_key = $data['api_key'] ?? '';
            $username = $data['username'] ?? '';

            $first_name = $data['first_name'] ?? '';
            $last_name = $data['last_name'] ?? '';

            if (empty($api_key)) $messages[] = 'api_key is required';
            if (empty($username)) $messages[] = 'username is required';


           if (empty($messages)) {

                $stmt = $db->prepare("SELECT * FROM users WHERE username = :username");
                $stmt->bindParam(':username', $username);
                $stmt->execute();
                $user_data = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($user_data && $api_key = $user_data['api_key'] && strtotime($user_data['expired_at']) > time()) {

                    $stmt = $db->prepare("UPDATE `users` SET first_name = :first_name, last_name = :last_name  WHERE `username` = :username");
                
                    $stmt->bindParam(':first_name', $first_name);
                    $stmt->bindParam(':last_name', $last_name);

                    $stmt->bindParam(':username', $username);
                    
                    if ($stmt->execute()) {
                        return $this->response([
                            'messages' => "Data changing success",
                            'user_id' => $user_data['id'],
                            'username' => $username,
                            'first_name' => $first_name,
                            'last_name' => $last_name
                        ], 200);
                    } else {
                        return $this->response('Data changing error', 400);
                    }
                } else {
                    return $this->response('Need authorization', 400);
                }
           } else {
                return $this->response('Error in data', 400);
           }
        } else {
            return $this->response('Error method', 400);
        }
    }

    /**
     * Метод DELETE
     * DELETE user
     * @return string
     */
    public function deleteAction()
    {   
        $data = json_decode(file_get_contents("php://input"), true);

        $database = new Database();
        $db = $database->getConnection();
        if($this->method == 'DELETE') {
            $api_key = $data['api_key'] ?? '';
            $username = $data['username'] ?? '';

            if (empty($api_key)) $messages[] = 'api_key is required';
            if (empty($username)) $messages[] = 'username is required';


           if (empty($messages)) {

                $stmt = $db->prepare("SELECT * FROM users WHERE username = :username");
                $stmt->bindParam(':username', $username);
                $stmt->execute();
                $user_data = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($user_data && $api_key = $user_data['api_key'] && strtotime($user_data['expired_at']) > time()) {

                    $stmt = $db->prepare("DELETE FROM `users` WHERE `username` = :username");
                    $stmt->bindParam(':username', $username);
                    
                    if ($stmt->execute()) {
                        return $this->response($username." success deleted", 200);
                    } else {
                        return $this->response('Data changing error', 400);
                    }
                } else {
                    return $this->response('Need authorization', 400);
                }
           } else {
                return $this->response('Error in data', 400);
           }
        } else {
            return $this->response('Error method', 400);
        }
    }
 }
