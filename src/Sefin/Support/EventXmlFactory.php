<?php

declare(strict_types=1);

namespace SefinSdk\Support;

use DOMDocument;
use DOMElement;
use SefinSdk\Exception\ValidationException;

final class EventXmlFactory
{
    public const TIPO_EVENTO_CANCELAMENTO = '101101';

    private const DESCRICAO_EVENTO_CANCELAMENTO = 'Cancelamento de NFS-e';

    private const MOTIVOS_CANCELAMENTO = ['1', '2', '9'];

    /**
     * Monta o XML de pedido de registro de evento de cancelamento de NFS-e (layout 1.01).
     *
     * Estrutura esperada:
     * - tpAmb (string|int): 1=Produção, 2=Homologação
     * - verAplic (string): versão do aplicativo emissor (1-20 chars)
     * - dhEvento (string): data/hora ISO8601 com fuso (ex.: 2026-07-07T09:30:00-03:00)
     * - CNPJAutor|CPFAutor (string): documento do autor do pedido
     * - chNFSe (string, 50 chars): chave de acesso da NFS-e a ser cancelada
     * - cMotivo (string): 1=Erro na emissão, 2=Serviço não prestado, 9=Outros
     * - xMotivo (string, 15-255 chars): descrição do motivo (obrigatório quando cMotivo=9)
     * - versao? (string): versão do leiaute (padrão: 1.00)
     *
     * Compatibilidade retroativa:
     * - cMotCancNFSe (alias de cMotivo; valor 4 é mapeado para 9)
     * - xMotCancNFSe (alias de xMotivo)
     *
     * @param array<string, mixed> $params
     */
    public static function forCancellation(array $params): string
    {
        $chNFSe = preg_replace('/\D+/', '', (string) ($params['chNFSe'] ?? '')) ?: '';
        if (strlen($chNFSe) !== 50) {
            throw new ValidationException('chNFSe must contain exactly 50 numeric characters.');
        }

        $tpAmb = (string) ($params['tpAmb'] ?? '');
        if (!in_array($tpAmb, ['1', '2'], true)) {
            throw new ValidationException('tpAmb is required and must be 1 (Production) or 2 (Homologation).');
        }

        $verAplic = trim((string) ($params['verAplic'] ?? ''));
        if ($verAplic === '') {
            throw new ValidationException('verAplic is required to build cancellation event XML.');
        }
        if (strlen($verAplic) > 20) {
            throw new ValidationException('verAplic must have at most 20 characters.');
        }

        $dhEvento = trim((string) ($params['dhEvento'] ?? ''));
        if ($dhEvento === '') {
            throw new ValidationException('dhEvento is required to build cancellation event XML.');
        }

        $cnpjAutor = preg_replace('/\D+/', '', (string) ($params['CNPJAutor'] ?? '')) ?: '';
        $cpfAutor = preg_replace('/\D+/', '', (string) ($params['CPFAutor'] ?? '')) ?: '';
        if ($cnpjAutor === '' && $cpfAutor === '') {
            throw new ValidationException('CNPJAutor or CPFAutor is required to build cancellation event XML.');
        }
        if ($cnpjAutor !== '' && $cpfAutor !== '') {
            throw new ValidationException('Provide only one of CNPJAutor or CPFAutor.');
        }
        if ($cnpjAutor !== '' && strlen($cnpjAutor) !== 14) {
            throw new ValidationException('CNPJAutor must contain exactly 14 numeric characters.');
        }
        if ($cpfAutor !== '' && strlen($cpfAutor) !== 11) {
            throw new ValidationException('CPFAutor must contain exactly 11 numeric characters.');
        }

        $cMotivo = self::resolveMotivo($params);
        $xMotivo = self::resolveDescricaoMotivo($params, $cMotivo);
        self::assertMotivoLength($xMotivo);

        $versao = (string) ($params['versao'] ?? '1.00');
        $id = 'PRE' . $chNFSe . self::TIPO_EVENTO_CANCELAMENTO;

        $doc = new DOMDocument('1.0', 'UTF-8');
        $doc->formatOutput = false;
        $doc->preserveWhiteSpace = false;

        $root = $doc->createElementNS(DpsXmlFactory::NFSE_NS, 'pedRegEvento');
        $root->setAttribute('versao', $versao);
        $doc->appendChild($root);

        $infPedReg = $doc->createElementNS(DpsXmlFactory::NFSE_NS, 'infPedReg');
        $infPedReg->setAttribute('Id', $id);
        $root->appendChild($infPedReg);

        self::appendText($doc, $infPedReg, 'tpAmb', $tpAmb);
        self::appendText($doc, $infPedReg, 'verAplic', $verAplic);
        self::appendText($doc, $infPedReg, 'dhEvento', $dhEvento);

        if ($cnpjAutor !== '') {
            self::appendText($doc, $infPedReg, 'CNPJAutor', $cnpjAutor);
        } else {
            self::appendText($doc, $infPedReg, 'CPFAutor', $cpfAutor);
        }

        self::appendText($doc, $infPedReg, 'chNFSe', $chNFSe);

        $e101101 = $doc->createElementNS(DpsXmlFactory::NFSE_NS, 'e101101');
        $infPedReg->appendChild($e101101);

        self::appendText($doc, $e101101, 'xDesc', self::DESCRICAO_EVENTO_CANCELAMENTO);
        self::appendText($doc, $e101101, 'cMotivo', $cMotivo);
        self::appendText($doc, $e101101, 'xMotivo', $xMotivo);

        return $doc->saveXML($doc->documentElement) ?: '';
    }

    /**
     * @param array<string, mixed> $params
     */
    private static function resolveMotivo(array $params): string
    {
        $raw = (string) ($params['cMotivo'] ?? $params['cMotCancNFSe'] ?? '');
        if (trim($raw) === '') {
            throw new ValidationException('cMotivo is required.');
        }

        if ($raw === '4') {
            $raw = '9';
        }

        if ($raw === '3') {
            throw new ValidationException(
                'cMotivo "3" (Duplicidade de NFS-e) is no longer supported in event layout v1.01. Use "9" (Outros) with xMotivo.'
            );
        }

        if (!in_array($raw, self::MOTIVOS_CANCELAMENTO, true)) {
            throw new ValidationException(
                sprintf(
                    'cMotivo "%s" is invalid. Allowed values: %s.',
                    $raw,
                    implode(', ', self::MOTIVOS_CANCELAMENTO)
                )
            );
        }

        return $raw;
    }

    /**
     * @param array<string, mixed> $params
     */
    private static function resolveDescricaoMotivo(array $params, string $cMotivo): string
    {
        $xMotivo = trim((string) ($params['xMotivo'] ?? $params['xMotCancNFSe'] ?? ''));
        if ($xMotivo !== '') {
            return $xMotivo;
        }

        if ($cMotivo === '9') {
            throw new ValidationException('xMotivo is required when cMotivo is 9 (Outros).');
        }

        return match ($cMotivo) {
            '1' => 'Erro na emissão da NFS-e.',
            '2' => 'Serviço não foi prestado.',
            default => throw new ValidationException('xMotivo is required.'),
        };
    }

    private static function assertMotivoLength(string $xMotivo): void
    {
        $length = strlen($xMotivo);
        if ($length < 15 || $length > 255) {
            throw new ValidationException('xMotivo must contain between 15 and 255 characters.');
        }
    }

    private static function appendText(DOMDocument $doc, DOMElement $parent, string $name, string $value): void
    {
        $el = $doc->createElementNS(DpsXmlFactory::NFSE_NS, $name);
        $el->appendChild($doc->createTextNode($value));
        $parent->appendChild($el);
    }
}
