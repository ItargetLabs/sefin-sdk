<?php

declare(strict_types=1);

namespace SefinSdk\Support;

use DOMDocument;
use DOMElement;
use SefinSdk\Exception\ValidationException;

final class EventXmlFactory
{
    /**
     * Motivos de cancelamento válidos:
     * 1 = Erro na emissão
     * 2 = Serviço não prestado
     * 3 = Duplicidade de NFS-e
     * 4 = Outros (xMotCancNFSe obrigatório)
     */
    public const TIPO_EVENTO_CANCELAMENTO = 1;

    private const MOTIVOS_CANCELAMENTO = ['1', '2', '3', '4'];

    /**
     * Monta o XML de pedido de registro de evento de cancelamento de NFS-e (layout 1.00).
     *
     * Estrutura esperada:
     * - chNFSe (string, 50 chars): chave de acesso da NFS-e a ser cancelada
     * - nSeqEvento (int, default 1): número sequencial do evento
     * - cMotCancNFSe (string): código do motivo — 1=Erro na emissão, 2=Serviço não prestado, 3=Duplicidade, 4=Outros
     * - xMotCancNFSe? (string, 15-255 chars): obrigatório quando cMotCancNFSe = 4
     *
     * @param array<string, mixed> $params
     */
    public static function forCancellation(array $params): string
    {
        $chNFSe = (string) ($params['chNFSe'] ?? '');
        if (trim($chNFSe) === '') {
            throw new ValidationException('chNFSe is required to build cancellation event XML.');
        }

        $nSeqEvento = (int) ($params['nSeqEvento'] ?? 1);
        if ($nSeqEvento <= 0) {
            throw new ValidationException('nSeqEvento must be greater than zero.');
        }

        $cMotCancNFSe = (string) ($params['cMotCancNFSe'] ?? '');
        if (trim($cMotCancNFSe) === '') {
            throw new ValidationException('cMotCancNFSe is required.');
        }

        if (!in_array($cMotCancNFSe, self::MOTIVOS_CANCELAMENTO, true)) {
            throw new ValidationException(
                sprintf(
                    'cMotCancNFSe "%s" is invalid. Allowed values: %s.',
                    $cMotCancNFSe,
                    implode(', ', self::MOTIVOS_CANCELAMENTO)
                )
            );
        }

        $xMotCancNFSe = (string) ($params['xMotCancNFSe'] ?? '');
        if ($cMotCancNFSe === '4' && trim($xMotCancNFSe) === '') {
            throw new ValidationException('xMotCancNFSe is required when cMotCancNFSe is 4 (Outros).');
        }

        $versao = (string) ($params['versao'] ?? '1.00');

        $doc = new DOMDocument('1.0', 'UTF-8');
        $doc->formatOutput = false;
        $doc->preserveWhiteSpace = false;

        $root = $doc->createElementNS(DpsXmlFactory::NFSE_NS, 'pedRegEvento');
        $root->setAttribute('versao', $versao);
        $doc->appendChild($root);

        $infPedReg = $doc->createElementNS(DpsXmlFactory::NFSE_NS, 'infPedReg');
        $infPedReg->setAttribute('Id', 'PRE' . $chNFSe);
        $root->appendChild($infPedReg);

        self::appendText($doc, $infPedReg, 'chNFSe', $chNFSe);
        self::appendText($doc, $infPedReg, 'tpEvento', (string) self::TIPO_EVENTO_CANCELAMENTO);
        self::appendText($doc, $infPedReg, 'nSeqEvento', (string) $nSeqEvento);
        self::appendText($doc, $infPedReg, 'verEvento', $versao);

        $detEvento = $doc->createElementNS(DpsXmlFactory::NFSE_NS, 'detEvento');
        $detEvento->setAttribute('versao', $versao);
        $infPedReg->appendChild($detEvento);

        self::appendText($doc, $detEvento, 'descEvento', 'Cancelamento NFS-e');
        self::appendText($doc, $detEvento, 'cMotCancNFSe', $cMotCancNFSe);

        if (trim($xMotCancNFSe) !== '') {
            self::appendText($doc, $detEvento, 'xMotCancNFSe', $xMotCancNFSe);
        }

        return $doc->saveXML($doc->documentElement) ?: '';
    }

    private static function appendText(DOMDocument $doc, DOMElement $parent, string $name, string $value): void
    {
        $el = $doc->createElementNS(DpsXmlFactory::NFSE_NS, $name);
        $el->appendChild($doc->createTextNode($value));
        $parent->appendChild($el);
    }
}
