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
use Swoole\Http\Request;

class Connect implements EventInterface
{
    private AdapterInterface $server;

    private int $fd;

    private ?Request $request;

    public function __construct(AdapterInterface $server, int $fd, ?Request $request = null)
    {
        $this->server = $server;
        $this->fd = $fd;
        $this->request = $request;
    }

    public function getRequest() : ?Request
    {
        return $this->request;
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
