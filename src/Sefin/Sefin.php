<?php

declare(strict_types=1);

namespace SefinSdk;

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
use SefinSdk\Http\NfseClient;

final class Sefin
{
    public function __construct(
        private readonly Environment $environment,
        private readonly CertificateConfig $certificateConfig,
        private ?Client $httpClient = null
    ) {
    }

    public function submitNfse(NfseSubmissionRequest $request): NfseSuccessResponse
    {
        return $this->client()->submit($request);
    }

    public function submitJudicialDecisionNfse(NfseBypassRequest $request): NfseSuccessResponse
    {
        return $this->client()->submitJudicialDecision($request);
    }

    public function getNfseByAccessKey(string $chaveAcesso): NfseLookupResponse
    {
        return $this->client()->getByAccessKey($chaveAcesso);
    }

    public function findNfseByDpsId(string $id): DpsLookupResponse
    {
        return $this->client()->findByDpsId($id);
    }

    public function hasNfseForDpsId(string $id): bool
    {
        return $this->client()->existsForDpsId($id);
    }

    public function registerEvent(string $chaveAcesso, RegisterEventRequest $request): EventResponse
    {
        return $this->client()->registerEvent($chaveAcesso, $request);
    }

    public function getEvent(string $chaveAcesso, int $tipoEvento, int $numSeqEvento): EventResponse
    {
        return $this->client()->getEvent($chaveAcesso, $tipoEvento, $numSeqEvento);
    }

    private function client(): NfseClient
    {
        return new NfseClient($this->environment, $this->certificateConfig, $this->httpClient);
    }
}
