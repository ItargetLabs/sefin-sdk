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

## Testes

```bash
vendor/bin/phpunit
```
