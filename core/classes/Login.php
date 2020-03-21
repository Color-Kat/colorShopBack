<?php namespace ColorCore;
    require_once('connect.php');

    class Login extends Connect{
        private $email;
        private $pass;

        public function __construct($emailLogin, $passLogin) {
            $this->email = $emailLogin;
            $this->pass = $passLogin;
        }
        public function login(){
            $conn = parent::conn();

            $userDB = $conn->query("SELECT mail FROM users WHERE mail = '$this->email'");
            if($userDB->num_rows == 1){
                $hash = $conn->query("SELECT password FROM users WHERE mail = '$this->email'");
                $hash = $hash->fetch_assoc();
                if(password_verify($this->pass, $hash['password'])){
                    $userId = $conn->query("SELECT userId FROM users WHERE mail = '$this->email'");
                    $userId = $userId->fetch_assoc();
                    $userName = $conn->query("SELECT name FROM users WHERE mail = '$this->email'");
                    $userName = $userName->fetch_assoc();
                    $_SESSION['userId'] = $userId['userId'];
                    $_SESSION['userName'] = $userName['name'];
                    $_SESSION['authorization'] = true;
                    return true;
                }else throw new \Exception('Неверный пароль');
            }else throw new \Exception('Такой пользователь не найден');
        }
    }
?>