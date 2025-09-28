<?php

use Swoole\WebSocket\Server;
use Swoole\WebSocket\Frame;

$env = parse_ini_file(__DIR__ . '/../../.env');

$WS_HOST = $env['WS_HOST'];
$WS_PORT = $env['WS_PORT'];

// WebScoket is a HTTP, so both HTTP and WebSocket share the same host and port
$server = new Server($WS_HOST, $WS_PORT);

$server->on('start', function ($server) {
    echo "Server started at http://{$server->host}:{$server->port} and ws://{$server->host}:{$server->port}\n";
});

$server->on('close', function (Server $server, int $fd) {
    if (!array_key_exists($fd, $server->connectionsInfo)) return; // Ignore if the fd is not a WebSocket user connection

    $user = getUsernameByFd($server, $fd);

    echo "\nClient disconnected! ID: {$fd} / USERNAME: {$user}\n"; // log
    echo "Connected clients now: " . count($server->connectionsInfo) . "\n"; // log

    // Remove the user from connectionsInfo
    unset($server->connectionsInfo[$fd]);

    $leftMsg = "\"{$user}\" has left the chat!";
    broadcast($server, $fd, $leftMsg);
});

$server->connectionsInfo = []; // To store connection info like username by fd (file decriptor)

$server->on('message', function (Server $server, Frame $frame) {
    $msg = json_decode($frame->data);

    $userFd = $frame->fd;

    if ($msg->type === 'join') {
        // Assign username to connection
        $server->connectionsInfo[$userFd] = $msg->username;

        $user = getUsernameByFd($server, $userFd);

        echo "\nNew client connected! ID: {$frame->fd} / USERNAME: {$user}\n"; // log
        echo "Connected clients now: " . count($server->connections) . "\n"; // log

        $joinMsg = "\"{$user}\" has joined the chat!";
        broadcast($server, $userFd, $joinMsg);
    }

    if ($msg->type === 'chat') {
        $user = getUsernameByFd($server, $userFd);

        $chatMsg = "> {$user}: {$msg->message}";
        broadcast($server, $userFd, $chatMsg);
    }
});

// HTTP request event
$server->on('request', function (Swoole\Http\Request $request, Swoole\Http\Response $response) {
    $baseDir = __DIR__ . '/../../client';

    $path = $request->server['request_uri'];

    if ($path === '/' || $path === '/index.html') {
        $file = $baseDir . '/index.html';
        $response->header('Content-Type', 'text/html');
    } elseif ($path === '/index.js') {
        $file = $baseDir . '/index.js';
        $response->header('Content-Type', 'application/javascript');
    } else {
        $response->status(404);
        $response->end("Not Found");
        return;
    }

    if (file_exists($file)) {
        $response->end(file_get_contents($file));
    } else {
        $response->status(404);
        $response->end("File not found");
    }
});

$server->start();

function broadcast(Server $server, int $userFd, string $data)
{
    foreach ($server->connections as $fd) {
        // Skip the sender and only send to established connections
        if ($fd !== $userFd && $server->isEstablished($fd)) {
            $server->push($fd, $data);
        }
    }
}

function getUsernameByFd(Server $server, int $fd): ?string
{
    return $server->connectionsInfo[$fd] ?? null;
}


/*

About file descriptors (fd)

    Each connection has a unique identifier, called fd (file descriptor).
    It is an integer number, starting from 1, and increasing by 1 for each new connection.
    When a connection is closed, its fd will not be reused.

    File descriptors (fds) in an application typically start with 3 or 4, depending on the operating system.
    The numbers 0, 1, and 2 are reserved for standard input, standard output, and standard error, respectively.

    The fd can be used to identify a connection, and can be used to send data to the client.

    The fd can be used in the following methods:

        $server->send($fd, $data); // for TCP/UDP
        $server->push($fd, $data); // for WebSocket
        $server->close($fd);



About the object "Swoole\WebSocket\Frame"

    A frame is a data packet sent by the client to the server in WebSocket protocol.
    The server receives the frame and processes it.
    
    The frame contains the following properties:

    [fd]
        The fd of the client connection, a unique identifier for each connection

    [data]
        The data received from the client in websocket frame

    [opcode]
        1: text
        2: binary
        8: close
        9: ping
        10/A: pong

    [finish]
        1: finished
        0: not finished - more frames will be sent later



Example of as "Swoole\WebSocket\Server" object on its creation (new Server)

    (
        [onStart:Swoole\Server:private] => 
        [onBeforeShutdown:Swoole\Server:private] => 
        [onShutdown:Swoole\Server:private] => 
        [onWorkerStart:Swoole\Server:private] => 
        [onWorkerStop:Swoole\Server:private] => 
        [onBeforeReload:Swoole\Server:private] => 
        [onAfterReload:Swoole\Server:private] => 
        [onWorkerExit:Swoole\Server:private] => 
        [onWorkerError:Swoole\Server:private] => 
        [onTask:Swoole\Server:private] => 
        [onFinish:Swoole\Server:private] => 
        [onManagerStart:Swoole\Server:private] => 
        [onManagerStop:Swoole\Server:private] => 
        [onPipeMessage:Swoole\Server:private] => 
        [setting] => 
        [connections] => Swoole\Connection\Iterator Object
            (
            )

        [host] => localhost
        [port] => 8081
        [type] => 1
        [ssl] => 
        [mode] => 1
        [ports] => Array
            (
                [0] => Swoole\Server\Port Object
                    (
                        [onConnect:Swoole\Server\Port:private] => 
                        [onReceive:Swoole\Server\Port:private] => 
                        [onClose:Swoole\Server\Port:private] => 
                        [onPacket:Swoole\Server\Port:private] => 
                        [onBufferFull:Swoole\Server\Port:private] => 
                        [onBufferEmpty:Swoole\Server\Port:private] => 
                        [onRequest:Swoole\Server\Port:private] => 
                        [onHandshake:Swoole\Server\Port:private] => 
                        [onOpen:Swoole\Server\Port:private] => 
                        [onMessage:Swoole\Server\Port:private] => 
                        [onDisconnect:Swoole\Server\Port:private] => 
                        [onBeforeHandshakeResponse:Swoole\Server\Port:private] => 
                        [host] => localhost
                        [port] => 8081
                        [type] => 1
                        [sock] => 4
                        [ssl] => 
                        [setting] => 
                        [connections] => Swoole\Connection\Iterator Object
                            (
                            )

                    )

            )

        [master_pid] => 0
        [manager_pid] => 0
        [worker_id] => -1
        [taskworker] => 
        [worker_pid] => 0
        [stats_timer] => 
        [admin_server] => 
    )


Example of an "Swoole\WebSocket\Server" object on "open" event

    (
        [onStart:Swoole\Server:private] => Closure Object
            (
                [name] => {closure:/home/rique/workspace/websocket/simple-websocket-chat/server/php/server.php:58}
                [file] => /home/rique/workspace/websocket/simple-websocket-chat/server/php/server.php
                [line] => 58
                [parameter] => Array
                    (
                        [$server] => <required>
                    )

            )

        [onBeforeShutdown:Swoole\Server:private] => 
        [onShutdown:Swoole\Server:private] => 
        [onWorkerStart:Swoole\Server:private] => 
        [onWorkerStop:Swoole\Server:private] => 
        [onBeforeReload:Swoole\Server:private] => 
        [onAfterReload:Swoole\Server:private] => 
        [onWorkerExit:Swoole\Server:private] => 
        [onWorkerError:Swoole\Server:private] => 
        [onTask:Swoole\Server:private] => 
        [onFinish:Swoole\Server:private] => 
        [onManagerStart:Swoole\Server:private] => 
        [onManagerStop:Swoole\Server:private] => 
        [onPipeMessage:Swoole\Server:private] => 
        [setting] => Array
            (
                [worker_num] => 1
                [task_worker_num] => 0
                [output_buffer_size] => 4294967295
                [max_connection] => 100000
                [open_http_protocol] => 1
                [open_mqtt_protocol] => 
                [open_eof_check] => 
                [open_length_check] => 
                [open_websocket_protocol] => 1
            )

        [connections] => Swoole\Connection\Iterator Object
            (
            )

        [host] => localhost
        [port] => 8081
        [type] => 1
        [ssl] => 
        [mode] => 1
        [ports] => Array
            (
                [0] => Swoole\Server\Port Object
                    (
                        [onConnect:Swoole\Server\Port:private] => 
                        [onReceive:Swoole\Server\Port:private] => 
                        [onClose:Swoole\Server\Port:private] => Closure Object
                            (
                                [name] => {closure:/home/rique/workspace/websocket/simple-websocket-chat/server/php/server.php:22}
                                [file] => /home/rique/workspace/websocket/simple-websocket-chat/server/php/server.php
                                [line] => 22
                                [parameter] => Array
                                    (
                                        [$ws] => <required>
                                        [$fd] => <required>
                                    )

                            )

                        [onPacket:Swoole\Server\Port:private] => 
                        [onBufferFull:Swoole\Server\Port:private] => 
                        [onBufferEmpty:Swoole\Server\Port:private] => 
                        [onRequest:Swoole\Server\Port:private] => Closure Object
                            (
                                [name] => {closure:/home/rique/workspace/websocket/simple-websocket-chat/server/php/server.php:32}
                                [file] => /home/rique/workspace/websocket/simple-websocket-chat/server/php/server.php
                                [line] => 32
                                [parameter] => Array
                                    (
                                        [$request] => <required>
                                        [$response] => <required>
                                    )

                            )

                        [onHandshake:Swoole\Server\Port:private] => 
                        [onOpen:Swoole\Server\Port:private] => Closure Object
                            (
                                [name] => {closure:/home/rique/workspace/websocket/simple-websocket-chat/server/php/server.php:16}
                                [file] => /home/rique/workspace/websocket/simple-websocket-chat/server/php/server.php
                                [line] => 16
                                [parameter] => Array
                                    (
                                        [$ws] => <required>
                                        [$request] => <required>
                                    )

                            )

                        [onMessage:Swoole\Server\Port:private] => Closure Object
                            (
                                [name] => {closure:/home/rique/workspace/websocket/simple-websocket-chat/server/php/server.php:26}
                                [file] => /home/rique/workspace/websocket/simple-websocket-chat/server/php/server.php
                                [line] => 26
                                [parameter] => Array
                                    (
                                        [$ws] => <required>
                                        [$frame] => <required>
                                    )

                            )

                        [onDisconnect:Swoole\Server\Port:private] => 
                        [onBeforeHandshakeResponse:Swoole\Server\Port:private] => 
                        [host] => localhost
                        [port] => 8081
                        [type] => 1
                        [sock] => 4
                        [ssl] => 
                        [setting] => 
                        [connections] => Swoole\Connection\Iterator Object
                            (
                            )

                    )

            )

        [master_pid] => 173974
        [manager_pid] => 0
        [worker_id] => 0
        [taskworker] => 
        [worker_pid] => 173974
        [stats_timer] => 
        [admin_server] => 
    )



Example of an "Swoole\Http\Request" object

    (
        [fd] => 4
        [streamId] => 0
        [header] => Array
        (
        [host] => localhost:8081
        [connection] => Upgrade
        [pragma] => no-cache
        [cache-control] => no-cache
        [user-agent] => Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36
        [upgrade] => websocket
        [origin] => http://localhost:8081
        [sec-websocket-version] => 13
        [accept-encoding] => gzip, deflate, br, zstd
        [accept-language] => pt-BR,pt;q=0.9,en;q=0.8
        [sec-websocket-key] => ENsHdv/Z3vXQzUeJ6+aV4w==
        [sec-websocket-extensions] => permessage-deflate; client_max_window_bits
        )
        
        [server] => Array
        (
            [request_method] => GET
            [request_uri] => /
            [path_info] => /
            [request_time] => 1759030635
            [request_time_float] => 1759030635.6172
            [server_protocol] => HTTP/1.1
            [server_port] => 8081
            [remote_port] => 56709
            [remote_addr] => 127.0.0.1
            [master_time] => 1759030635
        )
            
        [cookie] => Array
        (
        )
            
        [get] => 
        [files] => 
        [post] => 
        [tmpfiles] => 
    )
            
*/