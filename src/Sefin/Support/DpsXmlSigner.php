<?php

declare(strict_types=1);

namespace SefinSdk\Support;

use DOMDocument;
use DOMXPath;
use SefinSdk\Exception\ValidationException;

// xmlseclibs v3
use RobRichards\XMLSecLibs\XMLSecurityDSig;
use RobRichards\XMLSecLibs\XMLSecurityKey;

final class DpsXmlSigner
{
    private const DSIG_NS = 'http://www.w3.org/2000/09/xmldsig#';

    /**
     * Assina o elemento <infDPS> referenciado pelo atributo Id.
     *
     * @param non-empty-string $privateKeyPem
     * @param non-empty-string $certificatePem
     */
    public static function signInfDps(
        string $dpsXml,
        string $privateKeyPem,
        string $certificatePem,
        ?string $privateKeyPassword = null
    ): string
    {
        $dpsXml = trim($dpsXml);
        if ($dpsXml === '') {
            throw new ValidationException('DPS XML is required to sign.');
        }

        if (trim($privateKeyPem) === '' || trim($certificatePem) === '') {
            throw new ValidationException('Private key and certificate PEM are required to sign DPS XML.');
        }

        $doc = new DOMDocument();
        $doc->preserveWhiteSpace = false;
        $doc->formatOutput = false;
        if ($doc->loadXML($dpsXml) !== true) {
            throw new ValidationException('Invalid DPS XML (unable to parse).');
        }

        $xpath = new DOMXPath($doc);
        $xpath->registerNamespace('nfse', DpsXmlFactory::NFSE_NS);

        $infDps = $xpath->query('//nfse:DPS/nfse:infDPS')->item(0);
        if (!$infDps instanceof \DOMElement) {
            throw new ValidationException('infDPS element not found for signing.');
        }

        $id = (string) ($infDps->getAttribute('Id') ?: $infDps->getAttribute('id'));
        if (trim($id) === '') {
            throw new ValidationException('infDPS must have an Id attribute to be signed.');
        }

        // Garante que o DOM trate "Id" como atributo do tipo ID.
        // Isso evita que a lib gere/substitua o Id por um "pfx..." interno.
        if (method_exists($infDps, 'setIdAttribute')) {
            $infDps->setIdAttribute('Id', true);
        }

        // Remove Signature anterior (se houver) para evitar múltiplas assinaturas conflitantes.
        foreach ($xpath->query('//nfse:DPS/*[local-name()="Signature" and namespace-uri()="' . self::DSIG_NS . '"]') as $oldSig) {
            if ($oldSig instanceof \DOMNode && $oldSig->parentNode !== null) {
                $oldSig->parentNode->removeChild($oldSig);
            }
        }

        // Usa template base SEM prefixo (gera <Signature xmlns="..."> em vez de <ds:Signature ...>).
        // Isso evita que a SEFIN rejeite "prefixo de namespace" e, principalmente, evita mexer no XML após assinar.
        $dsig = new XMLSecurityDSig('');
        $dsig->setCanonicalMethod(XMLSecurityDSig::C14N);

        // Ajuda o xmlseclibs a identificar corretamente o atributo de ID do nó assinado,
        // evitando gerar/substituir por um "pfx..." interno.
        $dsig->idKeys = ['Id'];
        $dsig->idNS = [];

        // O exemplo do cliente usa rsa-sha1 no DPS.
        $dsig->addReference(
            $infDps,
            XMLSecurityDSig::SHA1,
            ['http://www.w3.org/2000/09/xmldsig#enveloped-signature', XMLSecurityDSig::C14N],
            [
                // Não sobrescreve o atributo Id existente (a SEFIN valida o pattern de TSIdDPS).
                'overwrite' => false,
                'id_name' => 'Id',
            ]
        );

        $key = new XMLSecurityKey(XMLSecurityKey::RSA_SHA1, ['type' => 'private']);
        // 3º parâmetro do loadKey é $isCert — para chave privada deve ser false.
        // O 4º parâmetro (quando suportado) é a senha da chave (se criptografada).
        $key->loadKey($privateKeyPem, false, false, $privateKeyPassword);

        $dsig->sign($key);
        $dsig->add509Cert($certificatePem, true, false, ['subjectName' => true]);

        $dps = $xpath->query('//nfse:DPS')->item(0);
        if (!$dps instanceof \DOMElement) {
            throw new ValidationException('DPS root element not found after parsing.');
        }

        // Anexa a assinatura como filho direto de <DPS>, como no XML fornecido.
        $dsig->appendSignature($dps);

        return $doc->saveXML($doc->documentElement) ?: '';
    }
}

