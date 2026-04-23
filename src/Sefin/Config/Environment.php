<?php

declare(strict_types=1);

namespace SefinSdk\Config;

final class Environment
{
    private function __construct(
        private readonly string $apiUrl,
        private readonly EnvironmentType $type
    ) {
    }

    public static function restrictedProduction(): self
    {
        return new self(
            'https://sefin.producaorestrita.nfse.gov.br/SefinNacional/',
            EnvironmentType::HOMOLOGATION
        );
    }

    public static function custom(string $apiUrl, EnvironmentType $type = EnvironmentType::HOMOLOGATION): self
    {
        return new self(rtrim($apiUrl, '/') . '/', $type);
    }

    public function getApiUrl(): string
    {
        return $this->apiUrl;
    }

    public function getType(): EnvironmentType
    {
        return $this->type;
    }
}
