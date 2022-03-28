## Socket And Websocket With PHP
## Description
### Library
### Usage:
    - composer require kovey/network
### Examples
```php
    use Kovey\Network\Server;
    use Kovey\Network\Handler;
    use Kovey\Network\Event;

    class Connect implements Handler\ConnectInterface
    {
        public function connect(Event\Connect $event) : void
        {
            echo sprintf('new connection, fd[%d]', $event->getFd()) . PHP_EOL;
        }
    }

    class Close implements Handler\CloseInterface
    {
        public function close(Event\Close $event) : void
        {
            echo sprintf('connection close, fd[%d]', $event->getFd()) . PHP_EOL;
        }
    }

    class Receive implements Handler\ReceiveInterface
    {
        public function receive(Event\Receive $event) : void
        {
            echo sprintf('data: %s, fd[%d]', $event->getPacket(),  $event->getFd()) . PHP_EOL;
        }
    }

    $serv = Server::factory(Server::ADAPTER_SOCKET, array(
        'host' => '127.0.0.1',
        'port' => 9911,
        'pid_file' => '/path/to/run/pid.pid',
        'worker_num' => 4,
        'max_co' => 30000,
        'name' => 'server',
        'run_docker' => 'Off',
        'logger_dir' => '/path/to/logs'
    ));

    $serv->setConnect(new Connect())
        ->setClose(new Close())
        ->setReceive(new Receive());

    $serv->start();

```
