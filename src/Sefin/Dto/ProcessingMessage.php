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
        // A API pode devolver chaves com casing diferente (ex.: Codigo/Descricao).
        $codigo = (string) ($payload['codigo'] ?? $payload['Codigo'] ?? '');
        $descricao = (string) ($payload['descricao'] ?? $payload['Descricao'] ?? '');
        $complemento = $payload['complemento'] ?? $payload['Complemento'] ?? null;

        return new self(
            codigo: $codigo,
            descricao: $descricao,
            complemento: $complemento !== null ? (string) $complemento : null
        );
    }
}
