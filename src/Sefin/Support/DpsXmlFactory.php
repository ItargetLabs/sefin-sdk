<?php

declare(strict_types=1);

namespace SefinSdk\Support;

use DOMDocument;
use DOMElement;
use SefinSdk\Exception\ValidationException;

final class DpsXmlFactory
{
    public const NFSE_NS = 'http://www.sped.fazenda.gov.br/nfse';

    /**
     * Monta um XML de DPS (layout 1.01) a partir de um payload em array.
     *
     * Estrutura esperada (baseada no XML fornecido pelo cliente):
     * - infDPS:
     *   - Id (string) ou será derivado de cLocEmi + prest + serie + nDPS
     *   - tpAmb (1|2), dhEmi (ISO8601), verAplic, serie, nDPS, dCompet (YYYY-MM-DD), tpEmit, cLocEmi
     *   - prest: { CNPJ|CPF, email?, regTrib: { opSimpNac, regEspTrib } }
     *   - toma?: { CNPJ|CPF, xNome?, end?: { endNac?: { cMun?, CEP? }, xLgr?, nro?, xCpl?, xBairro? }, fone?, email? }
     *   - serv: { locPrest: { cLocPrestacao }, cServ: { cTribNac, cTribMun?, xDescServ } }
     *   - valores: { vServPrest: { vServ }, trib: { tribMun: { tribISSQN, tpRetISSQN }, totTrib?: { vTotTrib?: { vTotTribFed, vTotTribEst, vTotTribMun } } } }
     *
     * @param array<string, mixed> $payload
     */
    public static function fromArray(array $payload): string
    {
        $inf = is_array($payload['infDPS'] ?? null) ? $payload['infDPS'] : $payload;

        $tpAmb = (string) ($inf['tpAmb'] ?? '');
        $dhEmi = (string) ($inf['dhEmi'] ?? '');
        $verAplic = (string) ($inf['verAplic'] ?? '');
        $serie = (string) ($inf['serie'] ?? '');
        $nDps = (string) ($inf['nDPS'] ?? '');
        $dCompet = (string) ($inf['dCompet'] ?? '');
        $tpEmit = (string) ($inf['tpEmit'] ?? '');
        $cLocEmi = (string) ($inf['cLocEmi'] ?? '');

        foreach ([
            'tpAmb' => $tpAmb,
            'dhEmi' => $dhEmi,
            'serie' => $serie,
            'nDPS' => $nDps,
            'dCompet' => $dCompet,
            'tpEmit' => $tpEmit,
            'cLocEmi' => $cLocEmi,
        ] as $field => $value) {
            if (trim($value) === '') {
                throw new ValidationException(sprintf('%s is required to build DPS XML.', $field));
            }
        }

        $prest = is_array($inf['prest'] ?? null) ? $inf['prest'] : null;
        if ($prest === null) {
            throw new ValidationException('prest is required to build DPS XML.');
        }

        $doc = new DOMDocument('1.0', 'UTF-8');
        $doc->formatOutput = false;
        $doc->preserveWhiteSpace = false;

        $dps = $doc->createElementNS(self::NFSE_NS, 'DPS');
        $dps->setAttribute('versao', (string) ($payload['versao'] ?? $inf['versao'] ?? '1.01'));
        $doc->appendChild($dps);

        $infDps = $doc->createElementNS(self::NFSE_NS, 'infDPS');
        $id = (string) ($inf['Id'] ?? $inf['id'] ?? '');
        if (trim($id) === '') {
            $id = self::deriveId($inf, $prest);
        }
        $infDps->setAttribute('Id', $id);
        $dps->appendChild($infDps);

        self::appendText($doc, $infDps, 'tpAmb', $tpAmb);
        self::appendText($doc, $infDps, 'dhEmi', $dhEmi);
        if (trim($verAplic) !== '') {
            self::appendText($doc, $infDps, 'verAplic', $verAplic);
        }
        self::appendText($doc, $infDps, 'serie', $serie);
        self::appendText($doc, $infDps, 'nDPS', $nDps);
        self::appendText($doc, $infDps, 'dCompet', $dCompet);
        self::appendText($doc, $infDps, 'tpEmit', $tpEmit);
        self::appendText($doc, $infDps, 'cLocEmi', $cLocEmi);

        self::appendPrest($doc, $infDps, $prest);

        $toma = is_array($inf['toma'] ?? null) ? $inf['toma'] : null;
        if ($toma !== null) {
            self::appendToma($doc, $infDps, $toma);
        }

        $serv = is_array($inf['serv'] ?? null) ? $inf['serv'] : null;
        if ($serv === null) {
            throw new ValidationException('serv is required to build DPS XML.');
        }
        self::appendServ($doc, $infDps, $serv);

        $valores = is_array($inf['valores'] ?? null) ? $inf['valores'] : null;
        if ($valores === null) {
            throw new ValidationException('valores is required to build DPS XML.');
        }
        self::appendValores($doc, $infDps, $valores);

        return $doc->saveXML($doc->documentElement) ?: '';
    }

    /**
     * @param array<string, mixed> $inf
     * @param array<string, mixed> $prest
     */
    private static function deriveId(array $inf, array $prest): string
    {
        $cLocEmi = preg_replace('/\\D+/', '', (string) ($inf['cLocEmi'] ?? '')) ?: '';
        $serie = (string) ($inf['serie'] ?? '');
        $nDps = (string) ($inf['nDPS'] ?? '');
        $tpInsc = isset($prest['CPF']) ? '1' : '2';
        $insc = preg_replace('/\\D+/', '', (string) ($prest['CNPJ'] ?? $prest['CPF'] ?? '')) ?: '';

        if ($cLocEmi === '' || $insc === '' || trim($serie) === '' || trim($nDps) === '') {
            throw new ValidationException('Unable to derive infDPS Id. Provide infDPS.Id explicitly.');
        }

        // Segue o padrão comum observado: DPS + cMun(7) + tpInsc(1) + insc(padded) + serie(padded 5) + nDPS(padded 15)
        $cMun = str_pad($cLocEmi, 7, '0', STR_PAD_LEFT);
        $inscPad = str_pad($insc, $tpInsc === '1' ? 11 : 14, '0', STR_PAD_LEFT);
        $seriePad = str_pad($serie, 5, '0', STR_PAD_LEFT);
        $nPad = str_pad($nDps, 15, '0', STR_PAD_LEFT);

        return 'DPS' . $cMun . $tpInsc . $inscPad . $seriePad . $nPad;
    }

    private static function appendText(DOMDocument $doc, DOMElement $parent, string $name, string $value): void
    {
        $el = $doc->createElementNS(self::NFSE_NS, $name);
        $el->appendChild($doc->createTextNode($value));
        $parent->appendChild($el);
    }

    /**
     * @param array<string, mixed> $prest
     */
    private static function appendPrest(DOMDocument $doc, DOMElement $infDps, array $prest): void
    {
        $prestEl = $doc->createElementNS(self::NFSE_NS, 'prest');
        $infDps->appendChild($prestEl);

        $cpf = (string) ($prest['CPF'] ?? '');
        $cnpj = (string) ($prest['CNPJ'] ?? '');
        if (trim($cpf) !== '') {
            self::appendText($doc, $prestEl, 'CPF', $cpf);
        } elseif (trim($cnpj) !== '') {
            self::appendText($doc, $prestEl, 'CNPJ', $cnpj);
        } else {
            throw new ValidationException('prest.CPF or prest.CNPJ is required.');
        }

        $email = (string) ($prest['email'] ?? '');
        if (trim($email) !== '') {
            self::appendText($doc, $prestEl, 'email', $email);
        }

        $regTrib = is_array($prest['regTrib'] ?? null) ? $prest['regTrib'] : null;
        if ($regTrib === null) {
            throw new ValidationException('prest.regTrib is required.');
        }

        $regTribEl = $doc->createElementNS(self::NFSE_NS, 'regTrib');
        $prestEl->appendChild($regTribEl);
        self::appendText($doc, $regTribEl, 'opSimpNac', (string) ($regTrib['opSimpNac'] ?? ''));
        self::appendText($doc, $regTribEl, 'regEspTrib', (string) ($regTrib['regEspTrib'] ?? ''));
    }

    /**
     * @param array<string, mixed> $toma
     */
    private static function appendToma(DOMDocument $doc, DOMElement $infDps, array $toma): void
    {
        $tomaEl = $doc->createElementNS(self::NFSE_NS, 'toma');
        $infDps->appendChild($tomaEl);

        $cpf = (string) ($toma['CPF'] ?? '');
        $cnpj = (string) ($toma['CNPJ'] ?? '');
        if (trim($cpf) !== '') {
            self::appendText($doc, $tomaEl, 'CPF', $cpf);
        } elseif (trim($cnpj) !== '') {
            self::appendText($doc, $tomaEl, 'CNPJ', $cnpj);
        }

        $xNome = (string) ($toma['xNome'] ?? '');
        if (trim($xNome) !== '') {
            self::appendText($doc, $tomaEl, 'xNome', $xNome);
        }

        $end = is_array($toma['end'] ?? null) ? $toma['end'] : null;
        if ($end !== null) {
            $endEl = $doc->createElementNS(self::NFSE_NS, 'end');
            $tomaEl->appendChild($endEl);

            $endNac = is_array($end['endNac'] ?? null) ? $end['endNac'] : null;
            if ($endNac !== null) {
                $endNacEl = $doc->createElementNS(self::NFSE_NS, 'endNac');
                $endEl->appendChild($endNacEl);
                if (isset($endNac['cMun'])) {
                    self::appendText($doc, $endNacEl, 'cMun', (string) $endNac['cMun']);
                }
                if (isset($endNac['CEP'])) {
                    self::appendText($doc, $endNacEl, 'CEP', (string) $endNac['CEP']);
                }
            }

            foreach (['xLgr', 'nro', 'xCpl', 'xBairro'] as $field) {
                $v = (string) ($end[$field] ?? '');
                if (trim($v) !== '') {
                    self::appendText($doc, $endEl, $field, $v);
                }
            }
        }

        $fone = (string) ($toma['fone'] ?? '');
        if (trim($fone) !== '') {
            self::appendText($doc, $tomaEl, 'fone', $fone);
        }

        $email = (string) ($toma['email'] ?? '');
        if (trim($email) !== '') {
            self::appendText($doc, $tomaEl, 'email', $email);
        }
    }

    /**
     * @param array<string, mixed> $serv
     */
    private static function appendServ(DOMDocument $doc, DOMElement $infDps, array $serv): void
    {
        $servEl = $doc->createElementNS(self::NFSE_NS, 'serv');
        $infDps->appendChild($servEl);

        $locPrest = is_array($serv['locPrest'] ?? null) ? $serv['locPrest'] : null;
        if ($locPrest === null) {
            throw new ValidationException('serv.locPrest is required.');
        }
        $locPrestEl = $doc->createElementNS(self::NFSE_NS, 'locPrest');
        $servEl->appendChild($locPrestEl);
        self::appendText($doc, $locPrestEl, 'cLocPrestacao', (string) ($locPrest['cLocPrestacao'] ?? ''));

        $cServ = is_array($serv['cServ'] ?? null) ? $serv['cServ'] : null;
        if ($cServ === null) {
            throw new ValidationException('serv.cServ is required.');
        }
        $cServEl = $doc->createElementNS(self::NFSE_NS, 'cServ');
        $servEl->appendChild($cServEl);

        self::appendText($doc, $cServEl, 'cTribNac', (string) ($cServ['cTribNac'] ?? ''));
        $cTribMun = (string) ($cServ['cTribMun'] ?? '');
        if (trim($cTribMun) !== '') {
            self::appendText($doc, $cServEl, 'cTribMun', $cTribMun);
        }
        self::appendText($doc, $cServEl, 'xDescServ', (string) ($cServ['xDescServ'] ?? ''));
    }

    /**
     * @param array<string, mixed> $valores
     */
    private static function appendValores(DOMDocument $doc, DOMElement $infDps, array $valores): void
    {
        $valEl = $doc->createElementNS(self::NFSE_NS, 'valores');
        $infDps->appendChild($valEl);

        $vServPrest = is_array($valores['vServPrest'] ?? null) ? $valores['vServPrest'] : null;
        if ($vServPrest === null) {
            throw new ValidationException('valores.vServPrest is required.');
        }
        $vServPrestEl = $doc->createElementNS(self::NFSE_NS, 'vServPrest');
        $valEl->appendChild($vServPrestEl);
        self::appendText($doc, $vServPrestEl, 'vServ', (string) ($vServPrest['vServ'] ?? ''));

        $trib = is_array($valores['trib'] ?? null) ? $valores['trib'] : null;
        if ($trib === null) {
            throw new ValidationException('valores.trib is required.');
        }
        $tribEl = $doc->createElementNS(self::NFSE_NS, 'trib');
        $valEl->appendChild($tribEl);

        $tribMun = is_array($trib['tribMun'] ?? null) ? $trib['tribMun'] : null;
        if ($tribMun === null) {
            throw new ValidationException('valores.trib.tribMun is required.');
        }
        $tribMunEl = $doc->createElementNS(self::NFSE_NS, 'tribMun');
        $tribEl->appendChild($tribMunEl);
        self::appendText($doc, $tribMunEl, 'tribISSQN', (string) ($tribMun['tribISSQN'] ?? ''));
        self::appendText($doc, $tribMunEl, 'tpRetISSQN', (string) ($tribMun['tpRetISSQN'] ?? ''));

        $totTrib = is_array($trib['totTrib'] ?? null) ? $trib['totTrib'] : null;
        if ($totTrib !== null) {
            $totTribEl = $doc->createElementNS(self::NFSE_NS, 'totTrib');
            $tribEl->appendChild($totTribEl);

            $vTotTrib = is_array($totTrib['vTotTrib'] ?? null) ? $totTrib['vTotTrib'] : null;
            if ($vTotTrib !== null) {
                $vTotTribEl = $doc->createElementNS(self::NFSE_NS, 'vTotTrib');
                $totTribEl->appendChild($vTotTribEl);

                foreach (['vTotTribFed', 'vTotTribEst', 'vTotTribMun'] as $field) {
                    $v = (string) ($vTotTrib[$field] ?? '');
                    if (trim($v) !== '') {
                        self::appendText($doc, $vTotTribEl, $field, $v);
                    }
                }
            }
        }
    }
}

