<?php

declare(strict_types=1);

namespace SefinSdk\Config;

use SefinSdk\Exception\ValidationException;

final class CertificateConfig
{
    public function __construct(
        private readonly string $certificatePath,
        private readonly ?string $privateKeyPath = null,
        private readonly ?string $privateKeyPassword = null,
        private readonly string|bool|null $verify = true,
        private readonly ?string $caBundlePath = null
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toGuzzleOptions(): array
    {
        $ext = strtolower((string) pathinfo($this->certificatePath, PATHINFO_EXTENSION));
        if (in_array($ext, ['pfx', 'p12'], true)) {
            throw new ValidationException(
                'Certificado PKCS#12 (.pfx/.p12) não é suportado. '
                . 'Forneça o certificado já extraído em PEM (SEFIN_CERT_PATH) e a chave privada em KEY/PEM (SEFIN_PRIVATE_KEY_PATH).'
            );
        }

        $options = [
            'cert' => $this->privateKeyPassword === null
                ? $this->certificatePath
                : [$this->certificatePath, $this->privateKeyPassword],
            'verify' => $this->caBundlePath ?? $this->verify,
        ];

        if ($this->privateKeyPath !== null) {
            $options['ssl_key'] = $this->privateKeyPassword === null
                ? $this->privateKeyPath
                : [$this->privateKeyPath, $this->privateKeyPassword];
        }

        return $options;
    }
}
