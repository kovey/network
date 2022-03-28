<?php
/**
 * @description
 *
 * @package
 *
 * @author kovey
 *
 * @time 2022-03-25 12:26:00
 *
 */
namespace Kovey\Network\Adapter;

use Kovey\Network\AdapterInterface;
use Kovey\Network\Event;
use Kovey\Network\Handler;
use Kovey\Logger\Logger;

abstract class Base
{
    protected Array $config;

    protected bool $isRunDocker = false;

    protected Handler\ConnectInterface $connect;

    protected Handler\ReceiveInterface $receive;

    protected Handler\CloseInterface $close;

    protected Handler\InitializeInterface $initialize;

    protected Handler\ConsoleInterface $console;

    final public function __construct(Array $config)
    {
        $this->config = $config;
        $this->isRunDocker = ($this->config['run_docker'] ?? 'Off') === 'On';
        if (empty($this->config['package_max_length'])) {
            $this->config['package_max_length'] = AdapterInterface::MAX_LENGTH;
        }

        if (empty($this->config['package_length_type'])) {
            $this->config['package_length_type'] = AdapterInterface::PACK_TYPE;
        }

        if (empty($this->config['package_length_offset'])) {
            $this->config['package_length_offset'] = AdapterInterface::LENGTH_OFFSET;
        }

        if (empty($this->config['package_body_offset'])) {
            $this->config['package_body_offset'] = AdapterInterface::BODY_OFFSET;
        }
    }

    public function setConnect(Handler\ConnectInterface $connect) : self
    {
        $this->connect = $connect;
    }

    public function setReceive(Handler\ReceiveInterface $receive) : self
    {
        $this->receive = $receive;
    }

    public function setClose(Handler\CloseInterface $close) : self
    {
        $this->close = $close;
    }

    public function setInitialize(Handler\InitializeInterface $initialize) : self
    {
        $this->initialize = $initialize;
    }

    public function setConsole(Handler\ConsoleInterface $console) : self
    {
        $this->console = $console;
    }

    public function workerError(\Swoole\Server $serv, \Swoole\Server\StatusInfo $info) : void
    {
        Logger::writeWarningLogSync(__LINE__, __FILE__, json_encode($info));
    }

    /**
     * @description Manager start event
     *
     * @param Swoole\Http\Server $serv
     *
     * @return void
     */
    public function managerStart(\Swoole\Server $serv) : void
    {
        ko_change_process_name($this->config['name'] . ' master');
    }

    /**
     * @description Worker start event
     *
     * @param Swoole\Http\Server $serv
     *
     * @param int $workerId
     *
     * @return void
     */
    public function workerStart(\Swoole\Server $serv, int $workerId) : void
    {
        ko_change_process_name($this->config['name'] . ' worker');
        if (empty($this->initialize)) {
            return;
        }

        $this->initialize->initialize(new Event\Initialize($this));
    }

    public function pipeMessage(\Swoole\Server $serv, PipeMessage $message) : void
    {
        if (empty($this->console)) {
            return;
        }

        $this->console->console(new Event\Console(
            $message->data['p'] ?? '', $message->data['m'] ?? '', $message->data['a'] ?? '', $message->data['t'] ?? '', $message->data['s'] ?? ''
        ));
    }

    abstract protected function init() : void;
}
