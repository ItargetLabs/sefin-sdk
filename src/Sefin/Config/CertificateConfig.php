<?php

declare(strict_types=1);

namespace SefinSdk\Config;

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
