<?php

declare(strict_types=1);

namespace SefinSdk\Dto;

use SefinSdk\Support\XmlCompressor;

final class NfseBypassRequest
{
    public function __construct(public readonly string $xmlGZipB64)
    {
    }

    public static function fromXml(string $xml): self
    {
        return new self(XmlCompressor::encode($xml));
    }

    /**
     * @return array{xmlGZipB64: string}
     */
    public function toArray(): array
    {
        return ['xmlGZipB64' => $this->xmlGZipB64];
    }
}
