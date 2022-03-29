<?php
/**
 * @description
 *
 * @package
 *
 * @author kovey
 *
 * @time 2022-03-25 12:24:28
 *
 */
namespace Kovey\Network\Adapter;

use Kovey\Network\PacketInterface;
use Kovey\Network\HandlerInterface;
use Swoole\Server;
use Kovey\Network\Event;

class Socket extends Base
{
    private Server $server;

    protected function init()  : void
    {
        $this->server = new Server($this->config['host'], $this->config['port']);
        $this->server->set(array(
            'open_length_check' => true,
            'package_max_length' => $this->config['package_max_length'],
            'package_length_type' => $this->config['package_length_type'],
            'package_length_offset' => $this->config['package_length_offset'],
            'package_body_offset' => $this->config['package_body_offset'],
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

        $this->server->on('connect', array($this, 'onConnect'));
        $this->server->on('receive', array($this, 'onReceive'));
        $this->server->on('close', array($this, 'onClose'));
        $this->server->on('workerStart', array($this, 'workerStart'));
        $this->server->on('managerStart', array($this, 'managerStart'));
        $this->server->on('pipeMessage', array($this, 'pipeMessage'));
        $this->server->on('workerError', array($this, 'workerError'));
    }

    public function onConnect(Server $serv, \Swoole\Server\Event $event) : void
    {
        if (empty($this->connect)) {
            return;
        }
        $this->connect->connect(new Event\Connect($this, $event->fd));
    }

    public function onReceive(Server $serv, \Swoole\Server\Event $event) : void
    {
        if (empty($this->receive)) {
            return;
        }

        $body = substr($event->data, self::BODY_OFFSET);
        $this->receive->receive(new Event\Receive($this, $body, $event->fd));
    }

    public function onClose(Server $serv, \Swoole\Server\Event $event) : void
    {
        if (empty($this->close)) {
            return;
        }

        $this->close->close(new Event\Close($this, $event->fd));
    }

    public function send(PacketInterface $packet, int $fd) : bool
    {
        if (!$this->server->exist($fd)) {
            throw new CloseConnectionException('connect is not exist');
        }

        $data = $packet->serialize();
        if (!$data) {
            return false;
        }

        $data = pack(self::PACK_TYPE, strlen($data)) . $data;
        $len = strlen($data);
        if ($len <= $this->config['package_max_length']) {
            return $this->server->send($fd, $data);
        }

        $sendLen = 0;
        while ($sendLen < $len) {
            $this->server->send($fd, substr($data, $sendLen, $this->config['package_max_length']));
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
        if (!$this->server->exist($fd)) {
            return false;
        }

        return $this->server->close($fd);
    }

    public function exist(int $fd) : bool
    {
        return $this->server->exist($fd);
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
