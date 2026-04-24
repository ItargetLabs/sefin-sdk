<?php

declare(strict_types=1);

namespace SefinSdk\Dto;

use SefinSdk\Support\XmlCompressor;
use SefinSdk\Support\XmlNormalizer;

final class NfseSubmissionRequest
{
    public function __construct(public readonly string $dpsXmlGZipB64)
    {
    }

    public static function fromXml(string $xml): self
    {
        return new self(XmlCompressor::encode(XmlNormalizer::ensureUtf8($xml)));
    }

    /**
     * @return array{dpsXmlGZipB64: string}
     */
    public function toArray(): array
    {
        return ['dpsXmlGZipB64' => $this->dpsXmlGZipB64];
    }
}
