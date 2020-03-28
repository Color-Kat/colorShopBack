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

                $chatId = $userId.mysqli_insert_id($conn).',';
                $conn->query("UPDATE users SET chatId = '$chatId' WHERE userId = '$userId';");

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
                $result['data']['meId']   = $userId; 
            }

            $goodData = $conn->query("SELECT goodName, img FROM goods WHERE id = '$goodId'");
            $goodData = $goodData->fetch_assoc();
            $result['goodData'] = $goodData;

            // return messages
            echo json_encode($result); 
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

            $result['me']               = $userId == $chat['seller'] ? '1' : '0';
            $result['data']['chatId']   = $chat['chatId'];
            $result['data']['meId']     = $userId;
            $result['data']['sellerId'] = $chat['seller'];
            $result['data']['buyerId']  = $chat['buyer'];

            $goodData = $chat['good'];
            $goodData = $conn->query("SELECT goodName, img FROM goods WHERE id = '$goodData'");
            $goodData = $goodData->fetch_assoc();
            $result['goodData'] = $goodData;

            // return messages
            echo json_encode($result);  

        }

        public function getMyChats() {
            $conn = parent::conn();
            $userId = $_SESSION['userId'];

            $chats = $conn->query("SELECT chatId FROM users WHERE userId = '$userId'");
            $chats = $chats->fetch_assoc();
            
            // my chats as a array
            $myChats = explode(",", $chats['chatId']);
            array_pop($myChats);
            
            echo json_encode($myChats);  
        }

        public function chatList($byId = false) {
            $conn = parent::conn();
            $userId  = $_SESSION['userId'];//userId

            // my chats as a string
            $myChats = $conn->query("SELECT chatId FROM users WHERE userId = '$userId'");
            $myChats = $myChats->fetch_assoc();

            // my chats as a array
            $myChats = explode(",", $myChats['chatId']);
            array_pop($myChats);

            $result = Array();
            foreach ($myChats as $chatId) {
                // get chatId
                // userId->21 | 35<-chatId
                $chatId = $this->str_replace_once($userId, '', $chatId);

                // get chat data from db
                $chats = $conn->query("SELECT chatId, good FROM chats WHERE chatId = '$chatId'");
                $chats = $chats->fetch_assoc();

                $goodId = $chats['good'];
                $goodData = $conn->query("SELECT goodName, img FROM goods WHERE id = '$goodId'");
                $goodData = $goodData->fetch_assoc();

                $result[] = array_merge($chats, $goodData);
            }
            
            echo json_encode($result);  
        }

        private function str_replace_once($search, $replace, $text){
            return implode($replace, explode($search, $text, 2));
        }
    }
