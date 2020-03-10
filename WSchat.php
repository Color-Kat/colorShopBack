<?php 
    use Ratchet\Server\IoServer;
    use Ratchet\Http\HttpServer;
    use Ratchet\WebSocket\WsServer;
    use Ratchet\MessageComponentInterface;
    use Ratchet\ConnectionInterface;
    use React\EventLoop\Factory;
    use React\Socket\Server as Reactor;
    use React\ZMQ\Context;
    use ColorCore\SocketChat;

    require  'vendor/autoload.php';

// $server = IoServer::factory(
//     new HttpServer(
//         new WsServer(
//             new SocketChat()
//         )
//     ),
//     8777
// );

// $server->run();


// --------------------------
