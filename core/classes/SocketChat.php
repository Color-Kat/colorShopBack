<?php namespace ColorCore;
require 'W:\domains\colorShop\vendor\autoload.php';

// use Ratchet\Server\IoServer;
// use Ratchet\Http\HttpServer;
// use Ratchet\WebSocket\WsServer;
// use Ratchet\MessageComponentInterface;
// use Ratchet\ConnectionInterface;

// class SocketChat implements MessageComponentInterface {
//     protected $clients;
//     protected $rooms;

//     public function __construct() {
//         $this->clients = new \SplObjectStorage;
//         $this->rooms = new \SplObjectStorage;
//     }

//     public function onOpen(ConnectionInterface $conn) {
//         // Store the new connection to send messages to later
//         $this->clients->attach($conn);

//         $chat = $conn->query("SELECT * FROM chats WHERE seller = '$seller' AND 
//                                                         buyer = '$buyer' AND 
//                                                         good = '$goodId'");
//         $chat = $chat->fetch_assoc();

//         $this->rooms->attach($conn);

//         echo "New connection! ({$conn->resourceId})\n";
//     }

//     public function onMessage(ConnectionInterface $from, $msg) {
//         // $msg = json_encode($msg);

//         $numRecv = count($this->clients) - 1;
//         echo sprintf('Connection %d sending message "%s" to %d other connection%s' . "\n"
//             , $from->resourceId, $msg, $numRecv, $numRecv == 1 ? '' : 's');

//         foreach ($this->clients as $client) {
//             // if ($from !== $client) {
//                 // The sender is not the receiver, send to each client connected
//                 $client->send($msg);
//                 // $client->send($msg);
//             // }
//         }
        
//     }

//     public function onClose(ConnectionInterface $conn) {
//         // The connection is closed, remove it, as we can no longer send it messages
//         $this->clients->detach($conn);

//         echo "Connection {$conn->resourceId} has disconnected\n";
//     }

//     public function onError(ConnectionInterface $conn, \Exception $e) {
//         echo "An error has occurred: {$e->getMessage()}\n";

//         $conn->close();
//     }
// }



// -------------------------------------------------------
