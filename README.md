# Sefin SDK

SDK PHP para integração com a API NFS-e da SEFIN Nacional, seguindo o contrato do `swagger.json` deste repositório.

## Recursos

- Emissão síncrona de NFS-e
- Emissão de NFS-e com decisão judicial
- Consulta por chave de acesso
- Consulta e verificação por identificador do DPS
- Registro e consulta de eventos
- Suporte a autenticação mTLS com certificado cliente
- DTOs tipados para requests e responses
- Utilitário para compactar/descompactar XML em `gzip + base64`

## Instalação

### Como dependência (Composer / Packagist)

O pacote está publicado no [Packagist](https://packagist.org). Em qualquer projeto PHP que use Composer, adicione a SDK com:

```bash
composer require devsitarget/sdk-sefin-nfse-php
```

Assim você passa a utilizar esta biblioteca como dependência declarada no `composer.json`, com resolução de versões e autoload gerenciados pelo Composer.

### Clonando este repositório

Para desenvolver ou rodar os testes a partir do código-fonte deste repositório:

```bash
composer install
```

## Uso com Docker

```bash
make build
make up
make install
make test
```

Para abrir um shell dentro do container:

```bash
make shell
```

## Exemplo rápido

```php
<?php

declare(strict_types=1);

use SefinSdk\Config\CertificateConfig;
use SefinSdk\Config\Environment;
use SefinSdk\Sefin;
use SefinSdk\Dto\NfseSubmissionRequest;

require __DIR__ . '/vendor/autoload.php';

$sdk = new Sefin(
    Environment::restrictedProduction(),
    new CertificateConfig(
        certificatePath: __DIR__ . '/certs/client.pem',
        privateKeyPath: __DIR__ . '/certs/client.key',
        privateKeyPassword: 'senha-do-certificado'
    )
);

$response = $sdk->submitNfse(
    NfseSubmissionRequest::fromXml(file_get_contents(__DIR__ . '/examples/dps.xml'))
);

echo $response->chaveAcesso;
```

## Enviar JSON e deixar a SDK montar o XML (DPS)

Se você prefere receber os campos como JSON no seu endpoint e delegar para a SDK a montagem do XML (DPS 1.01) + assinatura do `infDPS`, use `submitNfseFromArray()`:

```php
<?php

declare(strict_types=1);

use SefinSdk\Config\CertificateConfig;
use SefinSdk\Config\Environment;
use SefinSdk\Sefin;

$sdk = new Sefin(
    Environment::restrictedProduction(),
    new CertificateConfig(
        certificatePath: __DIR__ . '/certs/client.pem',
        privateKeyPath: __DIR__ . '/certs/client.key',
        privateKeyPassword: 'senha-do-certificado'
    )
);

$payload = [
    'infDPS' => [
        'tpAmb' => 2,
        'dhEmi' => '2026-01-15T10:00:00-03:00',
        'verAplic' => 'SEU_SISTEMA_1.0.0',
        'serie' => '1',
        'nDPS' => '1000',
        'dCompet' => '2026-01-15',
        'tpEmit' => 1,
        'cLocEmi' => '3550308',
        'prest' => [
            'CNPJ' => '12345678000190',
            'email' => 'financeiro@example.com',
            'regTrib' => ['opSimpNac' => 1, 'regEspTrib' => 0],
        ],
        'toma' => [
            'CPF' => '12345678901',
            'xNome' => 'Fulano de Tal',
            'end' => [
                'endNac' => ['cMun' => '3550308', 'CEP' => '01001000'],
                'xLgr' => 'RUA EXEMPLO',
                'nro' => '100',
                'xCpl' => 'SALA 10',
                'xBairro' => 'CENTRO',
            ],
            'fone' => '11999990000',
            'email' => 'cliente@example.com',
        ],
        'serv' => [
            'locPrest' => ['cLocPrestacao' => '3550308'],
            'cServ' => [
                'cTribNac' => '080201',
                'cTribMun' => '001',
                'xDescServ' => "Serviço de exemplo.\n\nValores e descrição fictícios para demonstração.",
            ],
        ],
        'valores' => [
            'vServPrest' => ['vServ' => '100.00'],
            'trib' => [
                'tribMun' => ['tribISSQN' => 1, 'tpRetISSQN' => 1],
                'totTrib' => [
                    'vTotTrib' => ['vTotTribFed' => '0.00', 'vTotTribEst' => '0.00', 'vTotTribMun' => '0.00'],
                ],
            ],
        ],
    ],
];

$response = $sdk->submitNfseFromArray($payload);

echo $response->chaveAcesso;
```

## Testes

```bash
vendor/bin/phpunit
```
