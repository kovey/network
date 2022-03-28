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

interface ConsoleInterface
{
    public function console(Event\Console $event) : void;
}
