<?php


namespace Nicodinus\UmeyeApi\UdpServer\Network;


use Amp\ByteStream\PendingReadError;
use Amp\Promise;
use Amp\Socket\Socket;
use Nicodinus\UmeyeApi\Core\ByteUtils\ByteBuffer;
use Nicodinus\UmeyeApi\Core\Network\BasicClientSession;
use Nicodinus\UmeyeApi\Core\Network\ServerHandlerInterface;
use Nicodinus\UmeyeApi\Network\Opcode\HeaderOpcodeWithSignatureInterface;
use Nicodinus\UmeyeApi\Network\Opcode\UdpHeaderOpcode;
use Nicodinus\UmeyeApi\Network\Packet\UdpAuthPacket;
use function Amp\call;

class ClientSession extends BasicClientSession
{
    /** @var HeaderOpcodeWithSignatureInterface */
    protected HeaderOpcodeWithSignatureInterface $dummyHeader;

    /** @var ByteBuffer */
    protected ByteBuffer $buffer;

    /** @var string */
    protected string $headerSignatureRaw;

    /**
     * ClientSession constructor.
     * @param ServerHandlerInterface $serverHandler
     * @param Socket $socket
     * @throws \PHPinnacle\Buffer\BufferOverflow
     * @throws \ReflectionException
     */
    public function __construct(ServerHandlerInterface &$serverHandler, Socket &$socket)
    {
        $this->dummyHeader = UdpHeaderOpcode::createInstance();
        $this->headerSignatureRaw = $this->dummyHeader->getSignature()->getBytes()->read($this->dummyHeader->getSignature()->getLength());

        $this->buffer = new ByteBuffer();

        parent::__construct($serverHandler, $socket);
    }

    /**
     * @return Promise
     * @throws PendingReadError
     */
    public function handleRx(): Promise
    {
        return call(function (self &$self) {

            $data = yield $self->socket->read();
            $self->lastActivity = microtime(true);

            $self->buffer->append(implode('', $data));

            while ($self->buffer->size() > 0)
            {
                $pos = strpos($self->buffer->read($self->buffer->size()), $self->headerSignatureRaw, 0);
                if ($pos === false) {
                    break;
                }

                $packet = UdpAuthPacket::createFromBuffer($self->buffer);
                dump($packet);
            }

        }, $this);
    }
}