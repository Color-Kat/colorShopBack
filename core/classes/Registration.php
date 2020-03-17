<?php namespace ColorCore;
    require_once('connect.php');

    class Registration extends Connect{
        public function __construct($email, $epass, $name, $surname){ 
            $this->email = $email;
            $this->pass = password_hash($epass, PASSWORD_BCRYPT, ["cost" => 8]);
            $this->name = $name;
            $this->surname = $surname;
        }
        public function checkUser(){
            $conn = parent::conn();
            $sel = $conn->query("SELECT * FROM users WHERE mail = '$this->email'");
            $out = array();
            if($sel->num_rows > 0){
                throw new \Exception('Такой Email уже зарегистрирован');
            }else{
                return true;
            }
        }
        public function register(){
            $conn = parent::conn();
            $sel = $conn->query("INSERT INTO users (`name`, `surname`, `mail`, `password`, `myOrders`, `liked`, `cart`, `location`, `chatID`) VALUES ('$this->name', '$this->surname', '$this->email', '$this->pass', '', '', '','','');");
            if($sel) return true;
            else throw new \Exception('Регистрация не выполнена');
        }
    }
?>