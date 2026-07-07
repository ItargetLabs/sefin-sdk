<?php

declare(strict_types=1);

namespace SefinSdk;

use GuzzleHttp\Client;
use SefinSdk\Config\CertificateConfig;
use SefinSdk\Config\Environment;
use SefinSdk\Dto\DpsLookupResponse;
use SefinSdk\Dto\EventListResponse;
use SefinSdk\Dto\EventResponse;
use SefinSdk\Dto\NfseBypassRequest;
use SefinSdk\Dto\NfseLookupResponse;
use SefinSdk\Dto\NfseSubmissionRequest;
use SefinSdk\Dto\NfseSuccessResponse;
use SefinSdk\Dto\RegisterEventRequest;
use SefinSdk\Http\NfseClient;
use SefinSdk\Support\CertificateSubjectExtractor;
use SefinSdk\Support\DpsXmlFactory;
use SefinSdk\Support\DpsXmlSigner;
use SefinSdk\Support\EventXmlFactory;
use SefinSdk\Support\EventXmlSigner;

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

    /**
     * Cancela uma NFS-e emitindo um evento de cancelamento.
     *
     * Monta o XML de pedido de cancelamento, assina com o certificado do cliente
     * e submete para a SEFIN.
     *
     * @param array<string, mixed> $params {
     *   cMotivo: string,       // 1=Erro na emissão, 2=Serviço não prestado, 9=Outros
     *   xMotivo?: string,      // 15-255 chars; obrigatório quando cMotivo=9
     *   tpAmb?: int|string,    // padrão: ambiente configurado na SDK
     *   verAplic?: string,     // padrão: sefin-sdk
     *   dhEvento?: string,     // padrão: data/hora atual (America/Sao_Paulo)
     *   CNPJAutor?: string,    // padrão: extraído do certificado
     *   CPFAutor?: string,     // padrão: extraído do certificado
     *   cMotCancNFSe?: string, // alias legado de cMotivo (4 => 9)
     *   xMotCancNFSe?: string, // alias legado de xMotivo
     * }
     */
    public function cancelNfse(string $chaveAcesso, array $params): EventResponse
    {
        $params['chNFSe'] = $chaveAcesso;
        $params['tpAmb'] ??= (string) $this->environment->getType()->value;
        $params['verAplic'] ??= 'sefin-sdk';
        $params['dhEvento'] ??= (new \DateTimeImmutable('now', new \DateTimeZone('America/Sao_Paulo')))
            ->format('Y-m-d\TH:i:sP');

        if (!isset($params['CNPJAutor']) && !isset($params['CPFAutor'])) {
            $certPem = self::extractPemBlock(
                (string) file_get_contents($this->certificateConfig->getCertificatePath()),
                'CERTIFICATE'
            );
            $author = CertificateSubjectExtractor::extract($certPem);
            if ($author['cnpj'] !== null) {
                $params['CNPJAutor'] = $author['cnpj'];
            } elseif ($author['cpf'] !== null) {
                $params['CPFAutor'] = $author['cpf'];
            } else {
                throw new \SefinSdk\Exception\ValidationException(
                    'CNPJAutor or CPFAutor is required. Unable to extract document from certificate.'
                );
            }
        }

        $eventXml = EventXmlFactory::forCancellation($params);

        $privateKeyPath = $this->certificateConfig->getPrivateKeyPath();
        if ($privateKeyPath === null || trim($privateKeyPath) === '') {
            throw new \SefinSdk\Exception\ValidationException('privateKeyPath is required to sign event XML.');
        }

        $certPem = self::extractPemBlock((string) file_get_contents($this->certificateConfig->getCertificatePath()), 'CERTIFICATE');
        $keyPem = self::extractAnyPrivateKeyPem((string) file_get_contents($privateKeyPath));
        $password = $this->certificateConfig->getPrivateKeyPassword();

        $eventXmlSigned = EventXmlSigner::signInfPedReg($eventXml, $keyPem, $certPem, $password);

        return $this->registerEvent($chaveAcesso, RegisterEventRequest::fromXml($eventXmlSigned));
    }

    /**
     * Retorna todos os eventos vinculados a uma NFS-e pela sua chave de acesso.
     *
     * GET /nfse/{chaveAcesso}/eventos
     */
    public function getEventsByAccessKey(string $chaveAcesso): EventListResponse
    {
        return $this->client()->getEventsByAccessKey($chaveAcesso);
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
