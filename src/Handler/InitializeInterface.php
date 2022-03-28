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

interface InitializeInterface
{
    public function initialize(Event\Initialize $event) : void;
}
