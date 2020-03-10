<?php namespace ColorCore;
    require_once('connect.php');
       
    class Like extends Connect{
        // получить соединение с бд
        protected $conn;

        public function addLike ($liked) {
            $conn = parent::conn();//db connection

            // add goodId to the users table
            $userId = $_SESSION['userId'];
            $conn->query("UPDATE users SET liked = CONCAT(liked, ',$liked') WHERE userId = '$userId'");
            $likeCount = $conn->query("SELECT likes FROM goods WHERE id = '$liked'");

            //increase the number of likes of good
            while($row = $likeCount->fetch_assoc()){
                $likes = $row['likes']+1;
                $conn->query("UPDATE goods SET likes = $likes WHERE id = '$liked'");
            }
            echo true;
        }

        public function delLike ($liked) {
            $conn = parent::conn();//db connection
            $userId  = $_SESSION['userId'];//userId

            // my likes as a string
            $likes   = $conn->query("SELECT liked FROM users WHERE userId = '$userId'");
            $likes   = $likes->fetch_assoc();

            // my likes as a array
            $myLikes = explode(",", $likes['liked']);
            foreach($myLikes as $key => $item){
                if ($item == $liked){
                    unset($myLikes[$key]);
                }
            }
            // my likes as a string
            $myLikes = implode(",", $myLikes);

            // refresh the list of like in the user table
            $conn->query("UPDATE users SET liked = '$myLikes' WHERE userId = '$userId'");

            // update the number of likes in the goods table
            $likeCount = $conn->query("SELECT likes FROM goods WHERE id = '$liked'");

            while($row = $likeCount->fetch_assoc()){
                $likes = $row['likes']-1;
                $conn->query("UPDATE goods SET likes = $likes WHERE id = '$liked'");
            }
            echo true;
        }

        public function echoLike () {
            $conn = parent::conn();

            if(isset($_SESSION['authorization'])){
                $userId = $_SESSION['userId'];

                $likes = $conn->query("SELECT liked FROM users WHERE userId = '$userId'");

                $likes = $likes->fetch_assoc();
                $myLikes = explode(",", $likes['liked']);
            }else return 'login';

            if(count($myLikes) != 0){
                if($myLikes[0] == '') unset($myLikes[0]);
                $out = array();
                $goods = array();
                foreach ($myLikes as $key=>$value) {
                    $goods[$key] = $conn->query("SELECT * FROM goods WHERE id = '$value'");
                    if($goods[$key]->num_rows > 0){
                        $goods[$key] = $goods[$key]->fetch_assoc();
                        $goods[$key]['del'] = false;
                    }else{
                        $goods[$key] = array();
                        $goods[$key]['del'] = true;
                    }
                }
                echo json_encode($goods);
            }else echo 'empty';
        }

        public function isAdded ($id) {
            $conn = parent::conn();//db connection
            $userId  = $_SESSION['userId'];//userId

            // my goods as a string
            $likes   = $conn->query("SELECT liked FROM users WHERE userId = '$userId'");
            $likes   = $likes->fetch_assoc();

            // my goods as a array
            $myLikes = explode(",", $likes['liked']);
            foreach($myLikes as $key => $item){
                // find item item
                if ($item == $id) return true;//found
            }
            // not found
            return false;
        }
    }
?>