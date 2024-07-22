<?php

use Swoole\WebSocket\Server;
use Swoole\WebSocket\Frame;
use Swoole\Http\Request;

class Chat
{
    protected $clients;
    protected $messagesCount;
    protected $database;

    public function __construct() {
        $this->messagesCount = 0;
        $this->clients = [];

    }

    public function onOpen(Server $server, Request $request) {
        $fd = $request->fd;
        $request->room = $request->get['room'];
        $this->clients[$fd] = $request;
        
        $name = $request->header['cookie'] ?? "Anonimo{$fd}";
        $name = explode("@", $name);
        $name = isset($name[1]) ? $name[1] : $name[0];
    }

    public function onMessage(Server $server, Frame $frame) {
        try {
            $msg = json_decode($frame->data, true);
            $name = $msg['name'] ?? "Anonimo{$frame->fd}";
            $msg = [
                "name" => $name,
                "msg" => trim($msg['msg'])
            ];

            if (strlen($msg['msg']) > 100) {
                $msg['msg'] = "(Esta mensagem está indisponível)";
                $msg['type'] = "error";
            }

            if (strlen($msg['name']) > 100) {
                $msg['name'] = "Anonimo{$frame->fd}";
            }
            
            if (strlen($msg['msg']) === 0) {
                $msg['msg'] = "(Esta mensagem está indisponível)";
                $msg['type'] = "error";
            }

            foreach ($this->clients as $clientFd => $clientRequest) {
                if ($frame->fd !== $clientFd && $clientRequest->room == $this->clients[$frame->fd]->room) {
                    $server->push($clientFd, json_encode($msg));
                }
            }

        } catch (\Throwable $th) {
        }
    }

    public function onClose(Server $server, int $fd) {
        unset($this->clients[$fd]);
        $name = $this->clients[$fd]->header['cookie'] ?? "Anonimo{$fd}";
        $name = explode("@", $name);
        $name = isset($name[1]) ? $name[1] : $name[0];
        echo "Connection {$fd} has disconnected\n";
    }

    public function onError(Server $server, int $fd, \Throwable $e) {
        echo "An error has occurred: {$e->getMessage()}\n";
        $server->close($fd);
    }
}

$server = new Server('0.0.0.0', 8000);
$chat = new Chat();

$server->on('Open', [$chat, 'onOpen']);
$server->on('Message', [$chat, 'onMessage']);
$server->on('Close', [$chat, 'onClose']);
$server->on('Error', [$chat, 'onError']);

$server->start();