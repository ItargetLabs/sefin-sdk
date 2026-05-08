<?php

declare(strict_types=1);

namespace SefinSdk\Support;

use DanfseNacional\Config\DanfseConfig;
use DanfseNacional\DanfseGenerator;

/**
 * Gera o PDF do DANFSe (NFS-e Padrão Nacional) a partir do XML autorizado,
 * utilizando {@see https://github.com/andrevabo/danfse-nacional andrevabo/danfse-nacional}.
 */
final class DanfsePdfGenerator
{
    private readonly DanfseConfig $config;

    public function __construct(?DanfseConfig $config = null)
    {
        $this->config = $config ?? new DanfseConfig();
    }

    /**
     * @return string Conteúdo binário do PDF (application/pdf).
     */
    public function generateFromXml(string $nfseXml): string
    {
        if (trim($nfseXml) === '') {
            throw new \InvalidArgumentException('NFS-e XML cannot be empty.');
        }

        $xmlForLib = DanfseXmlNormalizer::prepareForDanfseMapper($nfseXml);

        return (new DanfseGenerator($this->config))->generateFromXml($xmlForLib);
    }
}
