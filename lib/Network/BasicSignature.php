<?php


namespace Nicodinus\UmeyeApi\Network;


use Nicodinus\UmeyeApi\Core\ByteUtils\ByteBuffer;
use Nicodinus\UmeyeApi\Core\Network\Packet\PacketDataItemInterface;
use Nicodinus\UmeyeApi\Utils\Utils;

class BasicSignature implements SignatureInterface
{
    /**
     * @var mixed
     */
    protected $data;

    /**
     * @var ByteBuffer
     */
    protected ByteBuffer $bytes;

    /**
     * @param static $signature1
     * @param static $signature2
     * @return bool
     */
    public static function compare(self $signature1, self $signature2): bool {
        return (ByteBuffer::compareByteSequence(
            $signature1->getBytes(),
            $signature2->getBytes()
        ));
    }

    /**
     * @param SignatureInterface $signature
     * @return bool
     */
    public function equals(SignatureInterface $signature): bool {
        return static::compare($this, $signature);
    }

    /**
     * OpcodeSignature constructor.
     * @param mixed $data
     * @throws \PHPinnacle\Buffer\BufferOverflow
     * @throws \LogicException
     */
    public function __construct($data = null)
    {
        $this->data = $data;

        if (empty($data)) {
            $this->bytes = new ByteBuffer();
        } else if (Utils::compareClassname($data, PacketDataItemInterface::class)) {
            /** @var PacketDataItemInterface $data */
            $this->bytes = $data->mapDataToBuffer()->getBuffer();
        } else if ($data instanceof ByteBuffer) {
            $this->bytes = $data->chunk(0);
        } else if (is_string($data)) {
            if (strlen($data) > 2 && substr($data, 0, 2) == '0x') {
                $this->bytes = ByteBuffer::fromHexString(substr($data, 2));
            } else {
                $this->bytes = new ByteBuffer($data);
            }
        } else if (is_numeric($data)) {
            $this->bytes = ByteBuffer::fromNumber($data);
        } else if (is_array($data)) {
            $this->bytes = ByteBuffer::fromArray($data);
        } else {
            throw new \LogicException("Unsupported type!");
        }
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return int
     */
    public function getLength(): int
    {
        return $this->bytes->size();
    }

    /**
     * @return ByteBuffer
     */
    public function getBytes(): ByteBuffer
    {
        return $this->bytes;
    }
}