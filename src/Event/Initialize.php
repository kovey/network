<?php
/**
 * @description
 *
 * @package
 *
 * @author kovey
 *
 * @time 2022-03-28 11:54:58
 *
 */
namespace Kovey\Network\Event;

use Kovey\Event\EventInterface;
use Kovey\Network\AdapterInterface;

class Initialize implements EventInterface
{
    private AdapterInterface $server;

    public function __construct(AdapterInterface $server)
    {
        $this->server = $server;
    }

    public function getServer() : AdapterInterface
    {
        return $this->server;
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
