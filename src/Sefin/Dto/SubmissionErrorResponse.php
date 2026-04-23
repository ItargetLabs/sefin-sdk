<?php

declare(strict_types=1);

namespace SefinSdk\Dto;

use DateTimeImmutable;

final class SubmissionErrorResponse
{
    /**
     * @param list<ProcessingMessage> $erros
     */
    public function __construct(
        public readonly int $tipoAmbiente,
        public readonly string $versaoAplicativo,
        public readonly DateTimeImmutable $dataHoraProcessamento,
        public readonly ?string $idDps,
        public readonly array $erros
    ) {
    }

    /**
     * @param array<string, mixed> $payload
     */
    public static function fromArray(array $payload): self
    {
        $errors = [];

        foreach (($payload['erros'] ?? []) as $error) {
            if (is_array($error)) {
                $errors[] = ProcessingMessage::fromArray($error);
            }
        }

        return new self(
            tipoAmbiente: (int) ($payload['tipoAmbiente'] ?? 0),
            versaoAplicativo: (string) ($payload['versaoAplicativo'] ?? ''),
            dataHoraProcessamento: new DateTimeImmutable((string) ($payload['dataHoraProcessamento'] ?? 'now')),
            idDps: isset($payload['idDPS']) ? (string) $payload['idDPS'] : null,
            erros: $errors
        );
    }
}
