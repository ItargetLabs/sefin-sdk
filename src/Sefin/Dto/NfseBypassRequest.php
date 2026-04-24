<?php

declare(strict_types=1);

namespace SefinSdk\Dto;

use SefinSdk\Support\XmlCompressor;
use SefinSdk\Support\XmlNormalizer;

final class NfseBypassRequest
{
    public function __construct(public readonly string $xmlGZipB64)
    {
    }

    public static function fromXml(string $xml): self
    {
        return new self(XmlCompressor::encode(XmlNormalizer::ensureUtf8($xml)));
    }

    /**
     * @return array{xmlGZipB64: string}
     */
    public function toArray(): array
    {
        return ['xmlGZipB64' => $this->xmlGZipB64];
    }
}
