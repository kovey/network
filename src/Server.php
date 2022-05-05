<?php
/**
 * @description
 *
 * @package
 *
 * @author kovey
 *
 * @time 2022-03-25 11:56:55
 *
 */
namespace Kovey\Network;

class Server
{
    const ADAPTER_WEBSOCKET = 'websocket';

    const ADAPTER_SOCKET = 'socket';

    public static function factory(string $type, Array $config) : AdapterInterface
    {
        return match($type) {
            self::ADAPTER_SOCKET => new Adapter\Socket($config),
            self::ADAPTER_WEBSOCKET => new Adapter\WebSocket($config)
        };
    }
}
