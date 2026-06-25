<?php

declare(strict_types=1);

namespace SefinSdk\Support;

use DOMDocument;
use DOMXPath;
use SefinSdk\Exception\ValidationException;
use RobRichards\XMLSecLibs\XMLSecurityDSig;
use RobRichards\XMLSecLibs\XMLSecurityKey;

final class EventXmlSigner
{
    private const DSIG_NS = 'http://www.w3.org/2000/09/xmldsig#';

    /**
     * Assina o elemento <infPedReg> de um XML de pedido de registro de evento.
     *
     * @param non-empty-string $privateKeyPem
     * @param non-empty-string $certificatePem
     */
    public static function signInfPedReg(
        string $eventXml,
        string $privateKeyPem,
        string $certificatePem,
        ?string $privateKeyPassword = null
    ): string {
        $eventXml = trim($eventXml);
        if ($eventXml === '') {
            throw new ValidationException('Event XML is required to sign.');
        }

        if (trim($privateKeyPem) === '' || trim($certificatePem) === '') {
            throw new ValidationException('Private key and certificate PEM are required to sign event XML.');
        }

        $doc = new DOMDocument();
        $doc->preserveWhiteSpace = false;
        $doc->formatOutput = false;
        if ($doc->loadXML($eventXml) !== true) {
            throw new ValidationException('Invalid event XML (unable to parse).');
        }

        $xpath = new DOMXPath($doc);
        $xpath->registerNamespace('nfse', DpsXmlFactory::NFSE_NS);

        $infPedReg = $xpath->query('//nfse:pedRegEvento/nfse:infPedReg')->item(0);
        if (!$infPedReg instanceof \DOMElement) {
            throw new ValidationException('infPedReg element not found for signing.');
        }

        $id = (string) ($infPedReg->getAttribute('Id') ?: $infPedReg->getAttribute('id'));
        if (trim($id) === '') {
            throw new ValidationException('infPedReg must have an Id attribute to be signed.');
        }

        if (method_exists($infPedReg, 'setIdAttribute')) {
            $infPedReg->setIdAttribute('Id', true);
        }

        // Remove assinatura anterior caso exista.
        foreach ($xpath->query('//nfse:pedRegEvento/*[local-name()="Signature" and namespace-uri()="' . self::DSIG_NS . '"]') as $oldSig) {
            if ($oldSig instanceof \DOMNode && $oldSig->parentNode !== null) {
                $oldSig->parentNode->removeChild($oldSig);
            }
        }

        $dsig = new XMLSecurityDSig('');
        $dsig->setCanonicalMethod(XMLSecurityDSig::C14N);
        $dsig->idKeys = ['Id'];
        $dsig->idNS = [];

        $dsig->addReference(
            $infPedReg,
            XMLSecurityDSig::SHA1,
            ['http://www.w3.org/2000/09/xmldsig#enveloped-signature', XMLSecurityDSig::C14N],
            [
                'overwrite' => false,
                'id_name' => 'Id',
            ]
        );

        $key = new XMLSecurityKey(XMLSecurityKey::RSA_SHA1, ['type' => 'private']);
        $key->loadKey($privateKeyPem, false, false, $privateKeyPassword);

        $dsig->sign($key);
        $dsig->add509Cert($certificatePem, true, false, ['subjectName' => true]);

        $pedRegEvento = $xpath->query('//nfse:pedRegEvento')->item(0);
        if (!$pedRegEvento instanceof \DOMElement) {
            throw new ValidationException('pedRegEvento root element not found after parsing.');
        }

        $dsig->appendSignature($pedRegEvento);

        return $doc->saveXML($doc->documentElement) ?: '';
    }
}
