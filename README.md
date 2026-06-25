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
- Geração do **DANFSe em PDF** a partir do XML da NFS-e autorizada (integração com [andrevabo/danfse-nacional](https://github.com/andrevabo/danfse-nacional))

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

## Substituição de NFS-e

Para emitir uma NFS-e em substituição a outra já autorizada, inclua o campo `subst` dentro de `infDPS`, antes de `prest`:

```php
$payload = [
    'infDPS' => [
        // ... campos normais (tpAmb, dhEmi, serie, nDPS, etc.) ...

        'subst' => [
            'chSubstda' => '31062002250516724000160000000000002126046985535602', // chave da NFS-e a ser substituída
            'cMotivo'   => '1',  // 1 = Desenquadramento de NFS-e do Simples Nacional
                                 // 2 = Enquadramento de NFS-e no Simples Nacional
                                 // 3 = Inclusão Retroativa de Imunidade/Isenção
                                 // 4 = Exclusão Retroativa de Imunidade/Isenção
                                 // 5 = Rejeição de NFS-e pelo tomador/intermediário
                                 // 99 = Outros (xMotivo obrigatório)
            // 'xMotivo' => 'Descrição do motivo', // obrigatório apenas quando cMotivo = 99
        ],

        'prest' => [ /* ... */ ],
        // ...
    ],
];

$response = $sdk->submitNfseFromArray($payload);
```

> O campo `subst` é opcional (`0-1`). Quando `cMotivo = 99`, o campo `xMotivo` torna-se obrigatório (entre 15 e 255 caracteres).

## DANFSe em PDF (XML → PDF)

Para gerar o documento auxiliar da NFS-e em PDF a partir do XML retornado pela SEFIN (ou de um arquivo salvo), use `DanfsePdfGenerator`. A SDK normaliza o XML quando necessário para compatibilidade com o gerador (por exemplo, ajustes no bloco `totTrib`).

```php
<?php

declare(strict_types=1);

use SefinSdk\Support\DanfsePdfGenerator;

require __DIR__ . '/vendor/autoload.php';

$xml = file_get_contents(__DIR__ . '/nfse-autorizada.xml');

$pdf = (new DanfsePdfGenerator())->generateFromXml($xml);

header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="danfse.pdf"');
echo $pdf;
```

Opcionalmente é possível injetar uma instância de `DanfseConfig` da biblioteca `danfse-nacional` no construtor de `DanfsePdfGenerator` para ajustes avançados de layout.

## Testes

```bash
vendor/bin/phpunit
```
