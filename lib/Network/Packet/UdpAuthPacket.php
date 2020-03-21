<?php


namespace Nicodinus\UmeyeApi\Network\Packet;


use Nicodinus\UmeyeApi\Core\ByteUtils\ByteBuffer;
use Nicodinus\UmeyeApi\Core\Network\Packet\BasicPacket;
use Nicodinus\UmeyeApi\Network\Opcode\UdpHeaderOpcode;

class UdpAuthPacket extends BasicPacket
{
    protected int $length;

    /**
     * @param ByteBuffer $buffer
     * @return static
     * @throws \PHPinnacle\Buffer\BufferOverflow
     * @throws \ReflectionException
     */
    public static function createFromBuffer(ByteBuffer &$buffer): self
    {
        $instance = new static();
        $instance->header = UdpHeaderOpcode::createFromBuffer($buffer);
        $instance->recvTime = microtime(true);

        //$instance->buffer = $buffer->chunk(0);
        //$instance->length = $instance->buffer->size();
        //TODO: parse data

        return $instance;
    }

    /**
     * @return bool
     */
    public function isStaticLength(): bool
    {
        return false;
    }

    /**
     * @return int
     */
    public function getLength(): int
    {
        return $this->length;
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