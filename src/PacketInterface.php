<?php
/**
 * @description
 *
 * @package
 *
 * @author kovey
 *
 * @time 2022-03-25 11:58:39
 *
 */
namespace Kovey\Network;

interface PacketInterface
{
    public function unserialize(string $data) : void;

    public function serialize() : string;
}
