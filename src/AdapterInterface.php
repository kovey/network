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

use Kovey\Network\Event;
use Swoole\Server;
use Kovey\Network\Handler;

interface AdapterInterface
{
    /**
     * @description pack type
     *
     * @var string
     */
    const PACK_TYPE = 'N';

    /**
     * @description header length
     *
     * @var int
     */
    const HEADER_LENGTH = 4;

    /**
     * @description max length
     *
     * @var int
     */
    const MAX_LENGTH = 2097152;

    /**
     * @description length offset
     *
     * @var int
     */
    const LENGTH_OFFSET = 0;

    /**
     * @description body offset
     *
     * @var int
     */
    const BODY_OFFSET = 4;

    public function send(PacketInterface $packet, int $fd) : bool;

    public function start() : void;

    public function close(int $fd) : bool;

    public function exist(int $fd) : bool;

    public function getClientIP(int $fd) : string;

    public function getServ() : Server;

    public function setConnect(Handler\ConnectInterface $connect) : self;

    public function setClose(Handler\CloseInterface $close) : self;

    public function setReceive(Handler\ReceiveInterface $receive) : self;

    public function setInitialize(Handler\InitializeInterface $initialize) : self;

    public function setConsole(Handler\ConsoleInterface $console) : self;
}
