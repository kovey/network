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

interface ConnectInterface
{
    public function connect(Event\Connect $event) : void;
}
