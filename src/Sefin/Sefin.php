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
use SefinSdk\Support\DpsXmlFactory;
use SefinSdk\Support\DpsXmlSigner;

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

    /**
     * Recebe os campos em JSON (array), monta o XML de DPS (versão 1.01),
     * assina o infDPS com o certificado/chave do cliente e submete para a SEFIN.
     *
     * @param array<string, mixed> $payload
     */
    public function submitNfseFromArray(array $payload): NfseSuccessResponse
    {
        $dpsXml = DpsXmlFactory::fromArray($payload);

        $privateKeyPath = $this->certificateConfig->getPrivateKeyPath();
        if ($privateKeyPath === null || trim($privateKeyPath) === '') {
            throw new \SefinSdk\Exception\ValidationException('privateKeyPath is required to sign DPS XML.');
        }

        $certPem = self::extractPemBlock((string) file_get_contents($this->certificateConfig->getCertificatePath()), 'CERTIFICATE');
        $keyPem = self::extractAnyPrivateKeyPem((string) file_get_contents($privateKeyPath));

        $password = $this->certificateConfig->getPrivateKeyPassword();
        if ($password !== null && trim($password) !== '') {
            // Se a chave estiver criptografada, o PEM pode requerer a senha no header.
            // O xmlseclibs delega ao OpenSSL; então garantimos que o key esteja em formato que o OpenSSL consiga ler.
            $res = openssl_pkey_get_private($keyPem, $password);
            if ($res === false) {
                throw new \SefinSdk\Exception\ValidationException('Unable to read private key with provided password.');
            }
        }

        $dpsXmlSigned = DpsXmlSigner::signInfDps($dpsXml, $keyPem, $certPem, $password);

        return $this->submitNfse(NfseSubmissionRequest::fromXml($dpsXmlSigned));
    }

    private static function extractPemBlock(string $input, string $label): string
    {
        $input = trim($input);
        if ($input === '') {
            return '';
        }

        $pattern = sprintf('~-----BEGIN\\s+%s-----[\\s\\S]*?-----END\\s+%s-----~', preg_quote($label, '~'), preg_quote($label, '~'));
        if (preg_match($pattern, $input, $m) === 1) {
            return (string) $m[0];
        }

        return $input;
    }

    private static function extractAnyPrivateKeyPem(string $input): string
    {
        $input = trim($input);
        if ($input === '') {
            return '';
        }

        foreach (['PRIVATE KEY', 'ENCRYPTED PRIVATE KEY', 'RSA PRIVATE KEY', 'EC PRIVATE KEY'] as $label) {
            $pem = self::extractPemBlock($input, $label);
            if (str_contains($pem, '-----BEGIN ' . $label . '-----')) {
                return $pem;
            }
        }

        return $input;
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
