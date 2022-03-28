<?php
/**
 * @description
 *
 * @package
 *
 * @author kovey
 *
 * @time 2022-03-25 14:31:49
 *
 */
namespace Kovey\Network\Adapter;

use Kovey\Network\PacketInterface;
use Kovey\Network\HandlerInterface;
use Swoole\WebSocket\Server;
use Kovey\Network\Event;

class WebSocket extends Base
{
    protected Server $server;

    protected function init() : void
    {
        $this->server = new Server($this->config['host'], $this->config['port']);
        $this->server->set(array(
            'enable_coroutine' => true,
            'worker_num' => $this->config['worker_num'],
            'max_coroutine' => $this->config['max_co'] ?? 30000,
            'daemonize' => !$this->isRunDocker,
            'pid_file' => $this->config['pid_file'],
            'log_file' => $this->config['logger_dir'] . '/server/server.log',
            'event_object' => true,
            'log_rotation' => SWOOLE_LOG_ROTATION_DAILY,
            'log_date_format' => '%Y-%m-%d %H:%M:%S'
        ));

        $this->server->on('open', array($this, 'onOpen'));
        $this->server->on('message', array($this, 'onMessage'));
        $this->server->on('close', array($this, 'onClose'));
        $this->server->on('workerStart', array($this, 'workerStart'));
        $this->server->on('managerStart', array($this, 'managerStart'));
        $this->server->on('pipeMessage', array($this, 'pipeMessage'));
        $this->server->on('workerError', array($this, 'workerError'));
    }

    public function onOpen(Server $server, \Swoole\Http\Request $request) : void
    {
        if (empty($this->connect)) {
            return;
        }
        $this->connect->connect(new Event\Connect($this, $request->fd, $request));
    }

    public function onMessage(Server $server, \Swoole\WebSocket\Frame $frame) : void
    {
        if ($frame->opcode != SWOOLE_WEBSOCKET_OPCODE_BINARY) {
            $serv->disconnect($frame->fd, WebsocketCode::STREAM_ERROR, 'STREAM_ERROR');
            return;
        }

        if (empty($this->receive)) {
            return;
        }

        $this->receive->receive(new Event\Receive($this, $frame->data, $frame->fd));
    }

    public function onClose(Server $serv, \Swoole\Server\Event $event) : void
    {
        $this->handler->close(new Event\Close($this, $event->fd));
    }

    public function send(PacketInterface $packet, int $fd) : bool
    {
        if (!$this->server->exist($fd) || !$this->server->isEstablished($fd)) {
            return false;
        }

        $data = $packet->serialize();
        $len = strlen($data);
        if ($len <= $this->config['package_max_length']) {
            return $this->server->push($fd, $data, SWOOLE_WEBSOCKET_OPCODE_BINARY);
        }

        $sendLen = 0;
        while ($sendLen < $len) {
            $this->server->push(
                $fd, substr($data, $sendLen, $this->config['package_max_length']),
                SWOOLE_WEBSOCKET_OPCODE_BINARY, ($sendLen + $this->config['package_max_length']) >= $len
            );
            $sendLen += $this->config['package_max_length'];
        }

        return true;
    }

    public function start() : void
    {
        $this->server->start();
    }

    public function close(int $fd) : bool
    {
        if ($this->server->isEstablished($fd)) {
            return $this->server->disconnect($fd);
        }

        if (!$this->server->exist($fd)) {
            return false;
        }

        return $this->server->close($fd);
    }

    public function exist(int $fd) : bool
    {
        return $this->server->exist($fd) && $this->server->isEstablished($fd);
    }

    public function getClientIP(int $fd) : string
    {
        $info = $this->server->getClientInfo($fd);
        return $info['remote_ip'] ?? '';
    }

    public function getServ() : \Swoole\Server
    {
        return $this->server;
    }
}
