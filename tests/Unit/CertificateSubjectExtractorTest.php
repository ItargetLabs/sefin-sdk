<?php

declare(strict_types=1);

namespace SefinSdk\Tests\Unit;

use PHPUnit\Framework\TestCase;
use SefinSdk\Support\CertificateSubjectExtractor;

final class CertificateSubjectExtractorTest extends TestCase
{
    public function testExtractsCnpjFromSerialNumberInCertificateSubject(): void
    {
        $certPem = $this->createSelfSignedCertificate([
            'serialNumber' => '34265017000192',
            'CN' => 'EMPRESA TESTE LTDA',
        ]);

        $result = CertificateSubjectExtractor::extract($certPem);

        self::assertSame('34265017000192', $result['cnpj']);
        self::assertNull($result['cpf']);
    }

    public function testExtractsCpfFromSerialNumberInCertificateSubject(): void
    {
        $certPem = $this->createSelfSignedCertificate([
            'serialNumber' => '12345678901',
            'CN' => 'PESSOA TESTE',
        ]);

        $result = CertificateSubjectExtractor::extract($certPem);

        self::assertNull($result['cnpj']);
        self::assertSame('12345678901', $result['cpf']);
    }

    /**
     * @param array<string, string> $subject
     */
    private function createSelfSignedCertificate(array $subject): string
    {
        $dn = array_merge([
            'countryName' => 'BR',
            'organizationName' => 'Test Org',
            'commonName' => 'Test Certificate',
        ], $subject);

        $privateKey = openssl_pkey_new([
            'private_key_bits' => 2048,
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
        ]);
        self::assertNotFalse($privateKey);

        $csr = openssl_csr_new($dn, $privateKey, ['digest_alg' => 'sha256']);
        self::assertNotFalse($csr);

        $x509 = openssl_csr_sign($csr, null, $privateKey, 1, ['digest_alg' => 'sha256']);
        self::assertNotFalse($x509);

        $exported = '';
        self::assertTrue(openssl_x509_export($x509, $exported));

        return $exported;
    }
}
