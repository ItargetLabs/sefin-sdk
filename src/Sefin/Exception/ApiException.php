<?php

declare(strict_types=1);

namespace SefinSdk\Exception;

use SefinSdk\Dto\ErrorResponse;
use SefinSdk\Dto\SubmissionErrorResponse;

final class ApiException extends SefinException
{
    /**
     * @param array<string, mixed> $payload
     */
    public function __construct(
        string $message,
        int $code = 0,
        private readonly array $payload = []
    ) {
        parent::__construct($message, $code);
    }

    /**
     * @return array<string, mixed>
     */
    public function getPayload(): array
    {
        return $this->payload;
    }

    /**
     * @param array<string, mixed> $payload
     */
    public static function fromResponse(int $statusCode, array $payload): self
    {
        if (isset($payload['erros']) && is_array($payload['erros'])) {
            $response = SubmissionErrorResponse::fromArray($payload);
            $message = implode(
                ' | ',
                array_map(
                    static function ($error): string {
                        $code = trim((string) ($error->codigo ?? ''));
                        $desc = trim((string) ($error->descricao ?? ''));
                        if ($code !== '' && $desc !== '') {
                            return $code . ': ' . $desc;
                        }
                        return $desc !== '' ? $desc : $code;
                    },
                    $response->erros
                )
            );

            return new self($message !== '' ? $message : 'Erro retornado pela API da SEFIN.', $statusCode, $payload);
        }

        if (isset($payload['erro']) && is_array($payload['erro'])) {
            $response = ErrorResponse::fromArray($payload);
            $desc = trim($response->erro->descricao);
            $code = trim($response->erro->codigo);
            $message = $desc !== '' && $code !== '' ? ($code . ': ' . $desc) : ($desc !== '' ? $desc : $code);

            return new self($message !== '' ? $message : 'Erro retornado pela API da SEFIN.', $statusCode, $payload);
        }

        return new self('Falha inesperada na API da SEFIN.', $statusCode, $payload);
    }
}
