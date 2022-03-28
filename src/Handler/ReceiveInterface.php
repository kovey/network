<?php
/**
 * @description
 *
 * @package
 *
 * @author kovey
 *
 * @time 2022-03-28 10:35:43
 *
 */
namespace Kovey\Network\Handler;

use Kovey\Network\Event;

interface ReceiveInterface
{
    public function receive(Event\Receive $event) : void;
}
