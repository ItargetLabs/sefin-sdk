<?php

declare(strict_types=1);

namespace SefinSdk\Support;

/**
 * Ajustes no XML da NFS-e Nacional para o pacote andrevabo/danfse-nacional conseguir mapear com Valinor.
 *
 * No layout atual, totTrib/vTotTrib pode vir como elemento composto (vTotTribFed, vTotTribEst, …),
 * enquanto \DanfseNacional\Dto\TotTrib declara vTotTrib como string — o parse quebra.
 * O template DANFSe da biblioteca não usa esses totais; remover o bloco só afeta a geração do PDF, não a validade do XML original.
 *
 * Além disso, a SEFIN calcula BC/alíquota/ISSQN em infNFSe/valores (vBC, pAliqAplic, vISSQN),
 * mas o template da biblioteca lê esses campos de DPS/.../tribMun (vBC, pAliq, vISSQN).
 * Quando tribMun não traz esses valores (caso típico com município no Sistema Nacional),
 * o PDF fica com "BC ISSQN", "Alíquota Aplicada" e "ISSQN Apurado" como "-".
 */
final class DanfseXmlNormalizer
{
    public const NFSE_NS = 'http://www.sped.fazenda.gov.br/nfse';

    public static function prepareForDanfseMapper(string $xml): string
    {
        if (trim($xml) === '') {
            return $xml;
        }

        $dom = new \DOMDocument();
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = false;

        if (@$dom->loadXML($xml, LIBXML_NONET) !== true) {
            return $xml;
        }

        self::removeTotTribNodes($dom);
        self::injectIssqnFromValoresNfse($dom);

        $out = $dom->saveXML();

        return $out !== false && $out !== '' ? $out : $xml;
    }

    private static function removeTotTribNodes(\DOMDocument $dom): void
    {
        $toRemove = [];
        $list = $dom->getElementsByTagNameNS(self::NFSE_NS, 'totTrib');
        foreach ($list as $element) {
            $toRemove[] = $element;
        }

        foreach ($toRemove as $element) {
            $element->parentNode?->removeChild($element);
        }
    }

    /**
     * Copia vBC / pAliqAplic / vISSQN de infNFSe/valores para tribMun (como vBC / pAliq / vISSQN)
     * quando tribMun ainda não os possui — workaround do mapeamento da lib danfse-nacional.
     */
    private static function injectIssqnFromValoresNfse(\DOMDocument $dom): void
    {
        $xpath = new \DOMXPath($dom);
        $xpath->registerNamespace('nfse', self::NFSE_NS);

        $valoresNfse = $xpath->query('/nfse:NFSe/nfse:infNFSe/nfse:valores')->item(0);
        if (!$valoresNfse instanceof \DOMElement) {
            // Alguns XMLs podem omitir o wrapper NFSe no xpath absoluto após load; fallback relativo.
            $valoresNfse = $xpath->query('//nfse:infNFSe/nfse:valores')->item(0);
        }
        if (!$valoresNfse instanceof \DOMElement) {
            return;
        }

        // Garante que não pegamos o valores de dentro do DPS (irmão de trib, sem vBC/pAliqAplic no layout).
        if ($valoresNfse->parentNode?->localName !== 'infNFSe') {
            return;
        }

        $vBC = self::firstChildText($valoresNfse, 'vBC');
        $pAliqAplic = self::firstChildText($valoresNfse, 'pAliqAplic');
        $vISSQN = self::firstChildText($valoresNfse, 'vISSQN');

        if ($vBC === '' && $pAliqAplic === '' && $vISSQN === '') {
            return;
        }

        $tribMunNodes = $xpath->query('//nfse:infDPS/nfse:valores/nfse:trib/nfse:tribMun');
        if ($tribMunNodes === false) {
            return;
        }

        foreach ($tribMunNodes as $tribMun) {
            if (!$tribMun instanceof \DOMElement) {
                continue;
            }

            if ($vBC !== '' && self::firstChildText($tribMun, 'vBC') === '') {
                self::appendChildText($dom, $tribMun, 'vBC', $vBC);
            }
            if ($pAliqAplic !== '' && self::firstChildText($tribMun, 'pAliq') === '') {
                self::appendChildText($dom, $tribMun, 'pAliq', $pAliqAplic);
            }
            if ($vISSQN !== '' && self::firstChildText($tribMun, 'vISSQN') === '') {
                self::appendChildText($dom, $tribMun, 'vISSQN', $vISSQN);
            }
        }
    }

    private static function firstChildText(\DOMElement $parent, string $localName): string
    {
        foreach ($parent->childNodes as $child) {
            if ($child instanceof \DOMElement && $child->localName === $localName) {
                return trim($child->textContent ?? '');
            }
        }

        return '';
    }

    private static function appendChildText(\DOMDocument $dom, \DOMElement $parent, string $localName, string $value): void
    {
        $el = $dom->createElementNS(self::NFSE_NS, $localName);
        $el->appendChild($dom->createTextNode($value));
        $parent->appendChild($el);
    }
}
