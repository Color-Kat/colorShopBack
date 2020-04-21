<?php namespace ColorCore;
    require_once('connect.php');
       
    class Good extends Connect{
        // получить соединение с бд
        protected $conn;

        public function open($openable) {
            $conn = parent::conn();

            $goods = $conn->query("SELECT * FROM goods WHERE id = '$openable'");
            $goods = $goods->fetch_assoc();

            $catENG = $goods['categorie'];

            // translete categorie
            $catRUS = $conn->query("SELECT rusName FROM categories WHERE cat_name = '$catENG'");
            $catRUS = $catRUS->fetch_assoc();

            $goods['categorie'] = $catRUS['rusName'];

            // name:id (voldemar:43)
            $sellerName = $goods['seller'];
            // id (43)
            $sellerId = substr($sellerName, strpos($sellerName, ':') + 1, strlen($sellerName));
            // get seller data
            if ( $sellerId ) 
            {
                // user data
                $sellerInfo = $conn->query("SELECT 'name', 'surname', 'userId'  FROM users WHERE userId = '$sellerId'");
                $sellerInfo = $sellerInfo->fetch_assoc();
                
                // mass['userIs] -> mass['sellerId]
                $sellerInfo['sellerId'] = $sellerInfo['userId'];
                unset($sellerInfo['userId']);

                $goods   = array_merge($goods, $sellerInfo);
                $goods['sellerId'] = $sellerId;
            }
            // get buyer name
            // $userName = $conn->query("SELECT name FROM users WHERE userId = '$userId'");
            // $userName = $userName->fetch_assoc();
            if (isset($_SESSION['userId'])) {
                $userId = $_SESSION['userId'];//userId
                $goods['myId'] = $userId;
            }

            echo json_encode($goods);
        }
    }
