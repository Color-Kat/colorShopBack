<?php namespace ColorCore;
    require_once('connect.php');
       
    class Render extends Connect
    {
        // получить соединение с бд
        protected $conn;
        protected $action;
        public function __construct($action){ 
            $this->action = $action;
        }

        // главная страница
        public function render () {
            $conn = parent::conn();
            $sel = $conn->query("SELECT 'name' FROM content WHERE name = '$this->action'");
            $out = array();
            if($sel->num_rows > 0){
                $out['data'] = $sel->fetch_assoc();
                $out['content'] = file_get_contents($out['data']['html']);
                echo json_encode($out);
            }else echo '0';
        }

        public function renderGoods ($start) {
            $conn = parent::conn();

            // user likes
            if(isset($_SESSION['authorization'])){
                $userId = $_SESSION['userId'];
                $likes = $conn->query("SELECT liked FROM users WHERE userId = '$userId'");

                $likes = $likes->fetch_assoc();
                $myLikes = explode(",", $likes['liked']);
            }else $myLikes[] = '0';

            //search without anything 
            if(!isset($_SESSION['search']) && !isset($_SESSION['cat'])){

                $sel = $conn->query("SELECT * FROM goods LIMIT $start, 12");
                if($sel->num_rows > 0){
                    $out = array();

                        while($row = $sel->fetch_assoc()){
                            if(in_array($row['id'], $myLikes))
                                $row['iLike'] = true;
                            else 
                                $row['iLike'] = false;
                            $out[] = $row;
                        }

                    echo json_encode($out);
                }else echo '0';
            }

            // have a categorie
            else if(isset($_SESSION['cat'])) {
                $searchCat = $_SESSION['cat'];
                $sel = $conn->query("SELECT * FROM goods WHERE categorie = '$searchCat' LIMIT $start, 12");
                if($sel->num_rows > 0){
                    $out = array();

                    // LIKES
                    while($row = $sel->fetch_assoc()){
                        if(in_array($row['id'], $myLikes))
                            $row['iLike'] = true;
                        else 
                            $row['iLike'] = false;
                        $out[] = $row;
                    }

                    echo json_encode($out);
                }else echo '0';
            }

            // have a search
            else{
                $search = $_SESSION['search'];
                $sel = $conn->query("SELECT * FROM goods WHERE goodName LIKE '%$search%' LIMIT $start, 12");
                if($sel->num_rows > 0){
                    $out = array();

                    // // show likes if the user is authorized
                    // if(isset($_SESSION['authorization'])){
                        while($row = $sel->fetch_assoc()){
                            if(in_array($row['id'], $myLikes))
                                $row['iLike'] = true;
                            else 
                                $row['iLike'] = false;
                            $out[] = $row;
                        }
                    // }
                    
                    echo json_encode($out);
                }else echo '0';
            }
        }

        public function renderAvatar() {
            $conn = parent::conn();
            $name = $_SESSION['userName'];
            $sel = $conn->query("SELECT `avatar` FROM `users` WHERE `name` = '$name'");
            if($sel->num_rows > 0){
                $avatar = $sel->fetch_assoc();
                echo json_encode($avatar);
            }else echo false;
        }
        
        public function renderMyOrd() {
            $conn = parent::conn();
            $id = $_SESSION['userId'];

            $sel = $conn->query("SELECT `myOrders` FROM `users` WHERE `userId` = '$id'");
            if($sel->num_rows > 0){
                $MyOrders = $sel->fetch_assoc();
                $MyOrders = explode(",", $MyOrders['myOrders']);
                if($MyOrders[0] == '') unset($MyOrders[0]);
                $out = array();
                $goods = array();
                foreach ($MyOrders as $key=>$value) {
                    $goods[$key] = $conn->query("SELECT * FROM goods WHERE id = '$value'");
                    if($goods[$key]->num_rows > 0){
                        $goods[$key] = $goods[$key]->fetch_assoc();
                    }else{
                        $goods[$key] = "Товар удален:(";
                    }
                }
                echo json_encode($goods);
            }else echo false;
        }
    }
?>