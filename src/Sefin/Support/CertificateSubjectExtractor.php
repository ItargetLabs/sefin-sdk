<?php

declare(strict_types=1);

namespace SefinSdk\Support;

final class CertificateSubjectExtractor
{
    /**
     * @return array{cnpj: ?string, cpf: ?string}
     */
    public static function extract(string $certificatePem): array
    {
        $certificatePem = trim($certificatePem);
        if ($certificatePem === '') {
            return ['cnpj' => null, 'cpf' => null];
        }

        $x509 = openssl_x509_read($certificatePem);
        if ($x509 === false) {
            return ['cnpj' => null, 'cpf' => null];
        }

        $parsed = openssl_x509_parse($x509);
        if ($parsed === false) {
            return ['cnpj' => null, 'cpf' => null];
        }

        $subject = is_array($parsed['subject'] ?? null) ? $parsed['subject'] : [];
        $sources = [
            (string) ($subject['serialNumber'] ?? ''),
            (string) ($subject['CN'] ?? ''),
            (string) ($parsed['name'] ?? ''),
            (string) ($parsed['extensions']['subjectAltName'] ?? ''),
        ];

        foreach ($sources as $source) {
            $cnpj = self::matchCnpj($source);
            if ($cnpj !== null) {
                return ['cnpj' => $cnpj, 'cpf' => null];
            }
        }

        foreach ($sources as $source) {
            $cpf = self::matchCpf($source);
            if ($cpf !== null) {
                return ['cnpj' => null, 'cpf' => $cpf];
            }
        }

        return ['cnpj' => null, 'cpf' => null];
    }

    private static function matchCnpj(string $value): ?string
    {
        if (preg_match('/\b(\d{14})\b/', $value, $matches) === 1) {
            return $matches[1];
        }

        $digits = preg_replace('/\D+/', '', $value) ?: '';
        if (strlen($digits) === 14) {
            return $digits;
        }

        return null;
    }

    private static function matchCpf(string $value): ?string
    {
        if (preg_match('/\b(\d{11})\b/', $value, $matches) === 1) {
            return $matches[1];
        }

        $digits = preg_replace('/\D+/', '', $value) ?: '';
        if (strlen($digits) === 11) {
            return $digits;
        }

        return null;
    }
}
