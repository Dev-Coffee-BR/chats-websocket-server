<?php

$ws = new Swoole\WebSocket\Server('0.0.0.0', 8000);

$ws->on('Open', function ($ws, $request) {
    $ws->push($request->fd, "hello, welcome\n");
});

$ws->on('Message', function ($ws, $frame) {
    echo "Message: {$frame->data}\n";
    foreach ($ws->connections as $fd) {
        if ($frame->fd != $fd) {
            $ws->push($fd, "client-{$frame->fd}: {$frame->data}");
        }
    }
});

$ws->on('Close', function ($ws, $fd) {
    echo "client-{$fd} is closed\n";
});

$ws->start();
