<?php


namespace Nicodinus\UmeyeApi\Network\Opcode;


use Nicodinus\UmeyeApi\Core\ByteUtils\ByteBuffer;
use Nicodinus\UmeyeApi\Core\Network\Packet\PacketInterface;
use Nicodinus\UmeyeApi\Network\BasicSignature;
use Nicodinus\UmeyeApi\Network\SignatureInterface;

class UdpHeaderOpcode extends HeaderOpcodeWithSignature
{
    /** @var SignatureInterface */
    protected SignatureInterface $signature;

    /**
     * UdpHeaderOpcode constructor.
     * @param PacketInterface $packet
     * @param array $data
     * @throws \PHPinnacle\Buffer\BufferOverflow
     * @throws \LogicException
     */
    public function __construct(PacketInterface $packet, array $data = [])
    {
        $this->signature = new BasicSignature("0xC1EFABFF");

        parent::__construct($packet, $data);
    }

    /**
     * @return SignatureInterface
     */
    public function getSignature(): SignatureInterface
    {
        return $this->signature;
    }

    /**
     * @param ByteBuffer $buffer
     * @return static
     * @throws \ReflectionException
     * @throws \PHPinnacle\Buffer\BufferOverflow
     */
    public static function createFromBuffer(ByteBuffer &$buffer): self
    {
        // TODO: Implement createFromBuffer() method.
        $instance = static::createInstance();
        $instance->buffer = new ByteBuffer($buffer->consume($instance->getLength()));
        return $instance;
    }

    /**
     * @return static
     * @throws \ReflectionException
     * @throws \PHPinnacle\Buffer\BufferOverflow
     */
    public static function createInstance(): self
    {
        $ref = new \ReflectionClass(UdpHeaderOpcode::class);
        /** @var UdpHeaderOpcode $instance */
        $instance = $ref->newInstanceWithoutConstructor();
        unset($ref);

        $instance->signature = new BasicSignature("0xC1EFABFF");

        return $instance;
    }

    /**
     * @return bool
     */
    public function validateChecksum(): bool
    {
        // TODO: Implement validateChecksum() method.
        return true;
    }

    /**
     * @return static
     */
    public function calculateChecksum(): self
    {
        // TODO: Implement calculateChecksum() method.
        return $this;
    }

    /**
     * @return bool
     */
    public function isStaticLength(): bool
    {
        return true;
    }

    /**
     * @return int
     */
    public function getLength(): int
    {
        return 28;
    }

    /**
     * Generates output buffer and put it's to local variable
     * You should get buffer from method @return static
     * @link getBuffer()
     */
    public function mapDataToBuffer(): self
    {
        // TODO: Implement mapDataToBuffer() method.
        return $this;
    }

}