<?php

declare(strict_types=1);

namespace SefinSdk\Dto;

use DateTimeImmutable;

final class EventListResponse
{
    /**
     * @param EventResponse[] $eventos
     */
    public function __construct(
        public readonly int $tipoAmbiente,
        public readonly string $versaoAplicativo,
        public readonly DateTimeImmutable $dataHoraProcessamento,
        public readonly array $eventos
    ) {
    }

    /**
     * @param array<string, mixed> $payload
     */
    public static function fromArray(array $payload): self
    {
        $eventos = [];
        foreach ((array) ($payload['eventos'] ?? []) as $item) {
            if (is_array($item)) {
                $eventos[] = EventResponse::fromArray($item);
            }
        }

        return new self(
            tipoAmbiente: (int) ($payload['tipoAmbiente'] ?? 0),
            versaoAplicativo: (string) ($payload['versaoAplicativo'] ?? ''),
            dataHoraProcessamento: new DateTimeImmutable((string) ($payload['dataHoraProcessamento'] ?? 'now')),
            eventos: $eventos
        );
    }
}
