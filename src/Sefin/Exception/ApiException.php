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
    public static function fromResponse(int $statusCode, array $payload): self
    {
        if (isset($payload['erros']) && is_array($payload['erros'])) {
            $response = SubmissionErrorResponse::fromArray($payload);
            $message = implode(
                ' | ',
                array_map(
                    static fn ($error): string => $error->descricao,
                    $response->erros
                )
            );

            return new self($message !== '' ? $message : 'Erro retornado pela API da SEFIN.', $statusCode);
        }

        if (isset($payload['erro']) && is_array($payload['erro'])) {
            $response = ErrorResponse::fromArray($payload);
            return new self($response->erro->descricao !== '' ? $response->erro->descricao : 'Erro retornado pela API da SEFIN.', $statusCode);
        }

        return new self('Falha inesperada na API da SEFIN.', $statusCode);
    }
}
