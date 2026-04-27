<?php

declare(strict_types=1);

namespace SefinSdk\Http;

use GuzzleHttp\Client;
use SefinSdk\Config\CertificateConfig;
use SefinSdk\Config\Environment;
use SefinSdk\Dto\DpsLookupResponse;
use SefinSdk\Dto\EventResponse;
use SefinSdk\Dto\NfseBypassRequest;
use SefinSdk\Dto\NfseLookupResponse;
use SefinSdk\Dto\NfseSubmissionRequest;
use SefinSdk\Dto\NfseSuccessResponse;
use SefinSdk\Dto\RegisterEventRequest;
use SefinSdk\Exception\ValidationException;

final class NfseClient extends SefinBaseClient
{
    public function __construct(Environment $environment, CertificateConfig $certificateConfig, ?Client $httpClient = null)
    {
        parent::__construct($environment, $certificateConfig, $httpClient);
    }

    public function submit(NfseSubmissionRequest $request): NfseSuccessResponse
    {
        $response = NfseSuccessResponse::fromArray($this->request(
            'POST',
            '/nfse',
            ['json' => $request->toArray()],
            201
        ));
        return $response;
    }

    public function submitJudicialDecision(NfseBypassRequest $request): NfseSuccessResponse
    {
        return NfseSuccessResponse::fromArray($this->request(
            'POST',
            '/decisao-judicial/nfse',
            ['json' => $request->toArray()],
            201
        ));
    }

    public function getByAccessKey(string $chaveAcesso): NfseLookupResponse
    {
        $this->assertNotEmpty($chaveAcesso, 'chaveAcesso');

        return NfseLookupResponse::fromArray($this->request(
            'GET',
            '/nfse/' . rawurlencode($chaveAcesso)
        ));
    }

    public function findByDpsId(string $id): DpsLookupResponse
    {
        $this->assertNotEmpty($id, 'id');

        return DpsLookupResponse::fromArray($this->request(
            'GET',
            '/dps/' . rawurlencode($id)
        ));
    }

    public function existsForDpsId(string $id): bool
    {
        $this->assertNotEmpty($id, 'id');

        return $this->requestHead('/dps/' . rawurlencode($id));
    }

    public function registerEvent(string $chaveAcesso, RegisterEventRequest $request): EventResponse
    {
        $this->assertNotEmpty($chaveAcesso, 'chaveAcesso');

        return EventResponse::fromArray($this->request(
            'POST',
            '/nfse/' . rawurlencode($chaveAcesso) . '/eventos',
            ['json' => $request->toArray()],
            201
        ));
    }

    public function getEvent(string $chaveAcesso, int $tipoEvento, int $numSeqEvento): EventResponse
    {
        $this->assertNotEmpty($chaveAcesso, 'chaveAcesso');

        if ($tipoEvento <= 0) {
            throw new ValidationException('tipoEvento must be greater than zero.');
        }

        if ($numSeqEvento <= 0) {
            throw new ValidationException('numSeqEvento must be greater than zero.');
        }

        return EventResponse::fromArray($this->request(
            'GET',
            sprintf(
                '/nfse/%s/eventos/%d/%d',
                rawurlencode($chaveAcesso),
                $tipoEvento,
                $numSeqEvento
            )
        ));
    }

    private function assertNotEmpty(string $value, string $field): void
    {
        if (trim($value) === '') {
            throw new ValidationException(sprintf('%s is required.', $field));
        }
    }
}
