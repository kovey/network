<?php
/**
 * @description
 *
 * @package
 *
 * @author kovey
 *
 * @time 2022-03-25 14:49:46
 *
 */
namespace Kovey\Network\Event;

use Kovey\Event\EventInterface;
use Kovey\Network\AdapterInterface;

class Close implements EventInterface
{
    private AdapterInterface $server;

    private int $fd;

    public function __construct(AdapterInterface $server, int $fd)
    {
        $this->server = $server;
        $this->fd = $fd;
    }

    public function getServer() : AdapterInterface
    {
        return $this->server;
    }

    public function getFd() : int
    {
        return $this->fd;
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
