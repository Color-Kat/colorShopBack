<?php namespace ColorCore;
    require_once('connect.php');

    class Chat extends Connect{
        // получить соединение с бд
        protected $conn;
        private $buyer;
        private $seller;
        private $goodId;

        public function openChat($buyer, $seller, $goodId) {
            $conn = parent::conn();
            $userId  = $_SESSION['userId'];//userId

            $chat = $conn->query("SELECT * FROM chats WHERE seller = '$seller' AND 
                                                            buyer = '$buyer' AND 
                                                            good = '$goodId'");
            $chat = $chat->fetch_assoc();

            // if there is no chat, then create new chat
            if ( $chat == null ) {
                // create new chat
                $conn->query("INSERT INTO chats (`good`, `seller`, `buyer`) VALUES ('$goodId', 
                                                                                    '$seller', 
                                                                                    '$buyer');");

                $result = array();
                $result['data']['chatId'] = mysqli_insert_id($conn);

                echo json_encode($result);

                // message is empty
                return true;
            }
            // chat exsist
            else{
                $chatId = $chat['chatId'];
                $messages = $conn->query("SELECT * FROM chats_messages WHERE chat_id = '$chatId'");
                $messages = $messages->fetch_assoc();

                $result['data']           = $chat;
                $result['message']        = $messages;
                $result['data']['chatId'] = $chatId;
                // return messages
                echo json_encode($result);  
            }
                
                

            // // name:id (voldemar:43)
            // $sellerName = $goods['seller'];
            // // id (43)
            // $sellerId = substr($sellerName, strpos($sellerName, ':') + 1, strlen($sellerName));
            
            
            // echo json_encode($goods);
        }

        public function issetChat($chatId) {
            $conn = parent::conn();
            $userId  = $_SESSION['userId'];//userId

            // get everything
            $chat = $conn->query("SELECT * FROM chats WHERE chatId = '$chatId'");
            $chat = $chat->fetch_assoc();

            if ($chat == null) { echo json_encode('null'); return; }

            // check if the user is in this chat
            if ($userId != $chat['buyer'] && $userId != $chat['seller']) { echo json_encode('belong'); return; }

            // if the user is in chat
            $cahtId = $chat['chatId'];

            $messages = $conn->query("SELECT * FROM chats_messages WHERE chat_id = '$cahtId'");

            while ($podcat = $messages->fetch_assoc()) {
                $result['message'][] = $podcat['message'];
                $result['messId'][]  = $podcat['messId'];
                $result['sender'][]  = $podcat['sender'];
                $result['date'][]    = $podcat['date'];
            }

            $result['me'] = $userId == $chat['seller'] ? '1' : '0';
            $result['data']['chatId'] = $chat['chatId'];

            // return messages
            echo json_encode($result);  

        }

        public function sendMessage($chatId, $message, $sender) {
            $conn = parent::conn();
            $userId  = $_SESSION['userId'];//userId

            
        }

        public function chatList($byId = false) {
            $conn = parent::conn();
            $userId  = $_SESSION['userId'];//userId

            // my chats as a string
            $chats = $conn->query("SELECT chatId FROM users WHERE userId = '$userId'");
            $chats = $chats->fetch_assoc();

            // my chats as a array
            $myChats = explode(",", $chats['chatId']);
            
            echo json_encode($myChats);  
        }
    }
