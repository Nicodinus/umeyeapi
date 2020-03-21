<?php


namespace Nicodinus\UmeyeApi\TcpServer\Network;


use Amp\Promise;
use Amp\Socket\Socket;
use Amp\Success;
use Nicodinus\UmeyeApi\Core\Network\BasicServerHandler;
use Nicodinus\UmeyeApi\Core\Network\ClientSessionInterface;

class ServerHandler extends BasicServerHandler
{
    /**
     * @param Socket $socket
     * @return Promise<ClientSessionInterface>
     */
    protected function createSessionInstance(Socket &$socket): Promise
    {
        $sessionInstance = new ClientSession($this, $socket);

        return new Success($sessionInstance);
    }
}