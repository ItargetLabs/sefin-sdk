<?php

declare(strict_types=1);

namespace SefinSdk\Dto;

use SefinSdk\Support\XmlCompressor;

final class RegisterEventRequest
{
    public function __construct(public readonly string $pedidoRegistroEventoXmlGZipB64)
    {
    }

    public static function fromXml(string $xml): self
    {
        return new self(XmlCompressor::encode($xml));
    }

    /**
     * @return array{pedidoRegistroEventoXmlGZipB64: string}
     */
    public function toArray(): array
    {
        return ['pedidoRegistroEventoXmlGZipB64' => $this->pedidoRegistroEventoXmlGZipB64];
    }
}
