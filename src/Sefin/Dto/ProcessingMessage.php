<?php

declare(strict_types=1);

namespace SefinSdk\Dto;

final class ProcessingMessage
{
    public function __construct(
        public readonly string $codigo,
        public readonly string $descricao,
        public readonly ?string $complemento = null
    ) {
    }

    /**
     * @param array<string, mixed> $payload
     */
    public static function fromArray(array $payload): self
    {
        return new self(
            codigo: (string) ($payload['codigo'] ?? ''),
            descricao: (string) ($payload['descricao'] ?? ''),
            complemento: isset($payload['complemento']) ? (string) $payload['complemento'] : null
        );
    }
}
