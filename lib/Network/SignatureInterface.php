<?php


namespace Nicodinus\UmeyeApi\Network;


use Nicodinus\UmeyeApi\Core\ByteUtils\ByteBuffer;

interface SignatureInterface
{
    /**
     * @param static $signature
     * @return bool
     */
    public function equals(self $signature): bool;

    /**
     * @return mixed
     */
    public function getData();

    /**
     * @return int
     */
    public function getLength(): int;

    /**
     * @return ByteBuffer
     */
    public function getBytes(): ByteBuffer;
}