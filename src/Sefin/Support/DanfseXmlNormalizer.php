<?php

declare(strict_types=1);

namespace SefinSdk\Support;

/**
 * Ajustes no XML da NFS-e Nacional para o pacote andrevabo/danfse-nacional conseguir mapear com Valinor.
 *
 * No layout atual, totTrib/vTotTrib pode vir como elemento composto (vTotTribFed, vTotTribEst, …),
 * enquanto \DanfseNacional\Dto\TotTrib declara vTotTrib como string — o parse quebra.
 * O template DANFSe da biblioteca não usa esses totais; remover o bloco só afeta a geração do PDF, não a validade do XML original.
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

        $toRemove = [];
        $list = $dom->getElementsByTagNameNS(self::NFSE_NS, 'totTrib');
        foreach ($list as $element) {
            $toRemove[] = $element;
        }

        foreach ($toRemove as $element) {
            $element->parentNode?->removeChild($element);
        }

        $out = $dom->saveXML();

        return $out !== false && $out !== '' ? $out : $xml;
    }
}
