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

interface CloseInterface
{
    public function close(Event\Close $event) : void;
}
