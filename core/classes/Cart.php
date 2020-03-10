<?php namespace ColorCore;
    require_once('connect.php');
       
    class Cart extends Connect{
        // получить соединение с бд
        protected $conn;

        public function echoCart () {
            $conn = parent::conn();

            if(isset($_SESSION['authorization'])){
                $userId = $_SESSION['userId'];

                    $goods = $conn->query("SELECT cart FROM users WHERE userId = '$userId'");

                    $goods = $goods->fetch_assoc();
                    $myCart = explode(",", $goods['cart']);

                if(count($myCart) != 0){
                    if($myCart[0] == '') unset($myCart[0]);
                    $out = array();
                    $goodsItem = array();
                    foreach ($myCart as $key=>$value) {
                        $goodsItem[$key] = $conn->query("SELECT * FROM goods WHERE id = '$value'");
                        if($goodsItem[$key]->num_rows > 0){
                            $goodsItem[$key] = $goodsItem[$key]->fetch_assoc();
                            $goodsItem[$key]['del'] = false;
                        }else{
                            $goodsItem[$key] = array();
                            $goodsItem[$key]['del'] = true;
                        }
                    }
                    echo json_encode($goodsItem);
                }else echo 'empty';
            }else echo 'login';
        }

        public function addToCart ($added) {
            $conn = parent::conn();//db connection

            // add goodId to the users table
            $userId = $_SESSION['userId'];
            
            $conn->query("UPDATE users SET cart = CONCAT(cart, ',$added') WHERE userId = '$userId'");

            echo true;
        }

        public function deleteCartItem ($deltedId) {
            $conn = parent::conn();//db connection
            $userId  = $_SESSION['userId'];//userId

            // my goods as a string
            $goods   = $conn->query("SELECT cart FROM users WHERE userId = '$userId'");
            $goods   = $goods->fetch_assoc();

            // my goods as a array
            $myGoods = explode(",", $goods['cart']);
            foreach($myGoods as $key => $item){
                // delete the disired item
                if ($item == $deltedId){
                    unset($myGoods[$key]);
                }
            }
            // my goods as a string
            $myGoods = implode(",", $myGoods);

            // refresh the list of cart in the user table
            $conn->query("UPDATE users SET cart = '$myGoods' WHERE userId = '$userId'");
            echo true;
        }

        public function isAdded ($id) {
            $conn = parent::conn();//db connection
            $userId  = $_SESSION['userId'];//userId

            // my goods as a string
            $goods   = $conn->query("SELECT cart FROM users WHERE userId = '$userId'");
            $goods   = $goods->fetch_assoc();

            // my goods as a array
            $myGoods = explode(",", $goods['cart']);
            foreach($myGoods as $key => $item){
                // find item item
                if ($item == $id) return true;//found
            }
            // not found
            return false;
        }


        public function addCart () {
            $conn = parent::conn();
            $userId = $_SESSION['userId'];

            if(isset($_SESSION['authorization'])){
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
            }else echo false;
        }
    }
?>