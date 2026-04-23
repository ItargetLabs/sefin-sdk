<?php

declare(strict_types=1);

namespace SefinSdk\Tests\Unit;

use PHPUnit\Framework\TestCase;
use SefinSdk\Config\CertificateConfig;

final class CertificateConfigTest extends TestCase
{
    public function testBuildsGuzzleOptionsWithSeparatedPrivateKey(): void
    {
        $config = new CertificateConfig(
            certificatePath: '/certs/client.pem',
            privateKeyPath: '/certs/client.key',
            privateKeyPassword: 'secret',
            caBundlePath: '/certs/ca.pem'
        );

        self::assertSame(
            [
                'cert' => ['/certs/client.pem', 'secret'],
                'verify' => '/certs/ca.pem',
                'ssl_key' => ['/certs/client.key', 'secret'],
            ],
            $config->toGuzzleOptions()
        );
    }
}
