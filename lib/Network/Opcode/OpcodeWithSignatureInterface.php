<?php


namespace Nicodinus\UmeyeApi\Network\Opcode;


use Nicodinus\UmeyeApi\Core\Network\Opcode\OpcodeInterface;
use Nicodinus\UmeyeApi\Network\SignatureInterface;

interface OpcodeWithSignatureInterface extends OpcodeInterface
{
    /**
     * @return SignatureInterface
     */
    public function getSignature(): SignatureInterface;
}