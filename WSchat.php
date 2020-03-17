<?php 
    // use Ratchet\Server\IoServer;
    // use Ratchet\Http\HttpServer;
    // use Ratchet\WebSocket\WsServer;
    // use Ratchet\MessageComponentInterface;
    // use Ratchet\ConnectionInterface;
    // use React\EventLoop\Factory;
    // use React\Socket\Server as Reactor;
    // use React\ZMQ\Context;
    // use ColorCore\SocketChat;

    // require  'vendor/autoload.php';

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


    use Ratchet\Server\IoServer;
    use Ratchet\Server\FlashPolicy;
    use Ratchet\Http\HttpServer;
    use Ratchet\WebSocket\WsServer;
    use Ratchet\Wamp\ServerProtocol;

    use React\EventLoop\Factory;
    use React\Socket\Server as Reactor;
    use React\ZMQ\Context;

    use ColorCore\Bot;
    use ColorCore\PortLogger;
    use ColorCore\ChatRoom;
    use Ratchet\Cookbook\NullComponent;
    use Ratchet\Cookbook\MessageLogger;

    use Monolog\Logger;
    use Monolog\Handler\StreamHandler;
    
    require  'vendor/autoload.php';

    // Composer: The greatest thing since sliced bread

    // Setup logging
    $stdout = new StreamHandler('php://stdout');
    $logout = new Logger('SockOut');
    $login  = new Logger('Sock-In');
    $login->pushHandler($stdout);
    $logout->pushHandler($stdout);

    // The all mighty event loop
    $loop = Factory::create();

    // This little thing is to check for connectivity...
    // As a case study, when people connect on port 80, we're having them
    //  also connect on port 9000 and increment a counter if they connect.
    // Later, we can publish the results and find out if WebSockets over 
    //  a port other than 80 is viable (theory is blocked by firewalls).
    $context = new Context($loop);
    $push = $context->getSocket(ZMQ::SOCKET_PUSH);
    $push->connect('tcp://127.0.0.1:8777');

    // Setup our Ratchet ChatRoom application
    $webSock = new Reactor('127.0.0.1:8777', $loop);

    // $webSock->listen(8777, '0.0.0.0');
    $webServer = new IoServer(           // Basic I/O with clients, aww yeah
        new HttpServer(                  // HTTP because reasons
            new WsServer(                    // Boom! WebSockets
                new PortLogger($push, 8777,    // Compare vs the almost over 9000 conns
                    new MessageLogger(       // Log events in case of "oh noes"
                        new ServerProtocol(  // WAMP; the new hotness sub-protocol
                            new Bot(         // People kept asking me if I was a bot, so I made one!
                                new ChatRoom // ...and DISCUSS!
                            )
                        )
                        , $login
                        , $logout
                    )
                )
            )
        )
        , $webSock
    );

    // Allow Flash sockets (Internet Explorer) to connect to our app
    $flashSock = new Reactor('127.0.0.1:8777', $loop);
    $policy = new FlashPolicy;
    $policy->addAllowedAccess('*', 80);
    $webServer = new IoServer($policy, $flashSock);

    $logSock = new Reactor('127.0.0.1:8777', $loop);
    
    $zLogger = new IoServer(
        new WsServer(
            new PortLogger($push, 9000, new NullComponent)
        )
        , $logSock
    );

    // GO GO GO!
    $loop->run();