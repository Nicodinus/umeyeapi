<?php


namespace Nicodinus\UmeyeApi\UdpServer\Network;


use Amp\ByteStream\PendingReadError;
use Amp\Promise;
use Nicodinus\UmeyeApi\Core\Network\BasicClientSession;
use function Amp\call;

class ClientSession extends BasicClientSession
{
    /**
     * @return Promise
     * @throws PendingReadError
     */
    public function handleRx(): Promise
    {
        return call(function (self &$self) {

            $data = yield $self->socket->read();

            $self->lastActivity = microtime(true);

            dump($data);

        }, $this);
    }
}