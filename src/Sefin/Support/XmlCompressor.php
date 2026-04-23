<?php

declare(strict_types=1);

namespace SefinSdk\Support;

use SefinSdk\Exception\ValidationException;

final class XmlCompressor
{
    public static function encode(string $xml): string
    {
        if (trim($xml) === '') {
            throw new ValidationException('XML is required.');
        }

        $compressed = gzencode($xml, 9);

        if ($compressed === false) {
            throw new ValidationException('Unable to compress XML payload.');
        }

        return base64_encode($compressed);
    }

    public static function decode(string $base64GzipXml): string
    {
        if (trim($base64GzipXml) === '') {
            throw new ValidationException('Encoded XML payload is required.');
        }

        $binary = base64_decode($base64GzipXml, true);

        if ($binary === false) {
            throw new ValidationException('Encoded XML payload is not valid base64.');
        }

        $xml = gzdecode($binary);

        if ($xml === false) {
            throw new ValidationException('Encoded XML payload is not a valid gzip document.');
        }

        return $xml;
    }
}
