<?php
/**
 * @description
 *
 * @package
 *
 * @author kovey
 *
 * @time 2022-03-25 14:48:12
 *
 */
namespace Kovey\Network\Event;

use Kovey\Event\EventInterface;
use Kovey\Network\AdapterInterface;

class Receive implements EventInterface
{
    private AdapterInterface $server;

    private int $fd;

    private string $data;

    public function __construct(AdapterInterface $server, string $data, int $fd)
    {
        $this->server = $server;
        $this->fd = $fd;
        $this->data = $data;
    }

    public function getServer() : AdapterInterface
    {
        return $this->server;
    }

    public function getFd() : int
    {
        return $this->fd;
    }

    public function getData() : string
    {
        return $this->data;
    }

    /**
     * @description propagation stopped
     *
     * @return bool
     */
    public function isPropagationStopped() : bool
    {
        return true;
    }

    /**
     * @description stop propagation
     *
     * @return EventInterface
     */
    public function stopPropagation() : EventInterface
    {
        return $this;
    }
}
