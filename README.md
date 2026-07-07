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

## Atividade/Evento (`atvEvento`)

Quando o `cTribNac` pertence ao **item 12** da LC 116/2003 (códigos que começam com `12`, como `120801` — feiras, exposições, congressos etc.), a SEFIN exige o grupo `serv/atvEvento`. Sem ele, a emissão é rejeitada com o erro **E0390**.

Inclua `atvEvento` dentro de `serv` no payload de `submitNfseFromArray()`:

```php
'serv' => [
    'locPrest' => ['cLocPrestacao' => '3304557'],
    'cServ' => [
        'cTribNac' => '120801',
        'cTribMun' => '001',
        'xDescServ' => 'INSCRIÇÃO PARA O 58° CONGRESSO BRASILEIRO DE PATOLOGIA CLÍNICA',
    ],
    'atvEvento' => [
        'xNome' => '58° CONGRESSO BRASILEIRO DE PATOLOGIA CLÍNICA',
        'dtIni' => '2026-09-01',
        'dtFim' => '2026-09-05',

        // Opção A: código do evento na Administração Tributária Municipal
        // 'idAtvEvt' => 'CODIGO-DO-EVENTO',

        // Opção B: endereço do evento (mutuamente exclusivo com idAtvEvt)
        'end' => [
            'CEP' => '20040020',
            'xLgr' => 'Av. Churchill',
            'nro' => '94',
            'xBairro' => 'Centro',
        ],
    ],
],
```

> **Regras:** `xNome`, `dtIni` e `dtFim` são obrigatórios. Informe **`idAtvEvt`** ou **`end`**, nunca os dois. Ao usar `end`, o CEP deve pertencer ao município de `cLocPrestacao` (regra **E0398**). Para eventos no exterior, use `end.endExt` em vez de `CEP`.

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

## Consultar todos os eventos de uma NFS-e

Use `getEventsByAccessKey()` para buscar todos os eventos vinculados a uma NFS-e pela chave de acesso:

```php
<?php

declare(strict_types=1);

use SefinSdk\Config\CertificateConfig;
use SefinSdk\Config\Environment;
use SefinSdk\Sefin;

require __DIR__ . '/vendor/autoload.php';

$sdk = new Sefin(
    Environment::restrictedProduction(),
    new CertificateConfig(
        certificatePath: __DIR__ . '/certs/client.pem',
        privateKeyPath:  __DIR__ . '/certs/client.key',
        privateKeyPassword: 'senha-do-certificado'
    )
);

$chaveAcesso = '31062002250516724000160000000000002126046985535602';

// GET /nfse/{chaveAcesso}/eventos
$response = $sdk->getEventsByAccessKey($chaveAcesso);

echo "Processado em: " . $response->dataHoraProcessamento->format('d/m/Y H:i:s') . PHP_EOL;
echo "Total de eventos: " . count($response->eventos) . PHP_EOL;

foreach ($response->eventos as $evento) {
    // decodedXml() descompacta o gzip+base64 e retorna o XML do evento
    echo $evento->decodedXml();
}
```

> Para buscar um evento específico pelo tipo e número sequencial, use `getEvent($chaveAcesso, $tipoEvento, $numSeqEvento)`.

## Cancelamento de NFS-e

O cancelamento usa o **layout de eventos v1.01** (Anexo II SEFIN/ADN). A SDK monta o XML conforme o schema atual da SEFIN, incluindo o `Id` no formato `PRE` + chave (50) + tipo de evento (`101101`).

### Forma simplificada (recomendada)

Use `cancelNfse()`. A SDK monta o XML, extrai o CNPJ/CPF do certificado, assina e envia para a SEFIN:

```php
<?php

declare(strict_types=1);

use SefinSdk\Config\CertificateConfig;
use SefinSdk\Config\Environment;
use SefinSdk\Sefin;

require __DIR__ . '/vendor/autoload.php';

$sdk = new Sefin(
    Environment::restrictedProduction(),
    new CertificateConfig(
        certificatePath: __DIR__ . '/certs/client.pem',
        privateKeyPath:  __DIR__ . '/certs/client.key',
        privateKeyPassword: 'senha-do-certificado' // null se a chave não for protegida por senha
    )
);

// Chave de acesso da NFS-e que você quer cancelar (50 caracteres).
$chaveAcesso = '31062002250516724000160000000000002126046985535602';

$response = $sdk->cancelNfse(
    chaveAcesso: $chaveAcesso,
    params: [
        'cMotivo' => '1',
        // Códigos aceitos:
        //   1 = Erro na emissão
        //   2 = Serviço não prestado
        //   9 = Outros → neste caso, 'xMotivo' passa a ser obrigatório (15-255 chars)

        // 'xMotivo' => 'Descrição detalhada do motivo do cancelamento.', // obrigatório quando cMotivo = 9
    ]
);

// O XML do evento registrado e autorizado pela SEFIN (descompactado):
echo $response->decodedXml();
```

Campos preenchidos automaticamente por `cancelNfse()` quando omitidos:

| Campo | Padrão |
|-------|--------|
| `tpAmb` | ambiente configurado na SDK (`Environment`) |
| `verAplic` | `sefin-sdk` |
| `dhEvento` | data/hora atual (`America/Sao_Paulo`) |
| `CNPJAutor` / `CPFAutor` | extraído do certificado digital |

> **Aliases legados:** `cMotCancNFSe` e `xMotCancNFSe` ainda são aceitos. O valor `4` em `cMotCancNFSe` é mapeado para `cMotivo = 9`. O motivo `3` (Duplicidade) não existe mais no layout v1.01 — use `9` com `xMotivo`.

> Para consultar o evento registrado, use `getEvent($chaveAcesso, 101101, $numSeqEvento)`.

---

### Forma manual (controle total)

Caso precise inspecionar ou manipular o XML antes de enviar, use as classes de suporte diretamente:

```php
<?php

declare(strict_types=1);

use SefinSdk\Config\CertificateConfig;
use SefinSdk\Config\Environment;
use SefinSdk\Dto\RegisterEventRequest;
use SefinSdk\Sefin;
use SefinSdk\Support\EventXmlFactory;
use SefinSdk\Support\EventXmlSigner;

require __DIR__ . '/vendor/autoload.php';

// ─── 1. Parâmetros ──────────────────────────────────────────────────────────

$chaveAcesso   = '31062002250516724000160000000000002126046985535602';
$certPath      = __DIR__ . '/certs/client.pem';
$keyPath       = __DIR__ . '/certs/client.key';
$keyPassword   = 'senha-do-certificado'; // null se a chave não for protegida por senha

// ─── 2. Montar o XML de pedido de cancelamento ──────────────────────────────

$eventXml = EventXmlFactory::forCancellation([
    'tpAmb'      => '2',                              // 1=Produção, 2=Homologação
    'verAplic'   => 'meu-sistema/1.0',
    'dhEvento'   => '2026-07-07T09:30:00-03:00',
    'CNPJAutor'  => '12345678000190',                 // ou CPFAutor
    'chNFSe'     => $chaveAcesso,
    'cMotivo'    => '1',                              // 1, 2 ou 9
    // 'xMotivo' => 'Descrição do motivo...',         // obrigatório quando cMotivo = 9 (15-255 chars)
]);

// Neste ponto $eventXml é um XML não assinado, por exemplo:
//
// <pedRegEvento versao="1.00" xmlns="http://www.sped.fazenda.gov.br/nfse">
//   <infPedReg Id="PRE31062002250516724000160000000000002126046985535602101101">
//     <tpAmb>2</tpAmb>
//     <verAplic>meu-sistema/1.0</verAplic>
//     <dhEvento>2026-07-07T09:30:00-03:00</dhEvento>
//     <CNPJAutor>12345678000190</CNPJAutor>
//     <chNFSe>31062002250516724000160000000000002126046985535602</chNFSe>
//     <e101101>
//       <xDesc>Cancelamento de NFS-e</xDesc>
//       <cMotivo>1</cMotivo>
//       <xMotivo>Erro na emissão da NFS-e.</xMotivo>
//     </e101101>
//   </infPedReg>
// </pedRegEvento>

// ─── 3. Assinar o XML com o certificado do emitente ─────────────────────────

// Lê os arquivos de certificado e chave privada.
$certPem = file_get_contents($certPath);
$keyPem  = file_get_contents($keyPath);

// Assina o elemento <infPedReg> com RSA-SHA1 (padrão SEFIN).
// A assinatura é inserida como filho direto de <pedRegEvento>.
$eventXmlSigned = EventXmlSigner::signInfPedReg(
    eventXml:          $eventXml,
    privateKeyPem:     $keyPem,
    certificatePem:    $certPem,
    privateKeyPassword: $keyPassword // null se a chave não for protegida por senha
);

// ─── 4. Empacotar e enviar para a SEFIN ─────────────────────────────────────

// RegisterEventRequest comprime o XML em gzip+base64 conforme o contrato da API.
$request = RegisterEventRequest::fromXml($eventXmlSigned);

$sdk = new Sefin(
    Environment::restrictedProduction(),
    new CertificateConfig($certPath, $keyPath, $keyPassword)
);

// Envia para POST /nfse/{chaveAcesso}/eventos
$response = $sdk->registerEvent($chaveAcesso, $request);

// ─── 5. Tratar a resposta ───────────────────────────────────────────────────

echo "Evento registrado em: " . $response->dataHoraProcessamento->format('d/m/Y H:i:s') . PHP_EOL;
echo "XML do evento autorizado:" . PHP_EOL;
echo $response->decodedXml();
```

## Testes

```bash
vendor/bin/phpunit
```
