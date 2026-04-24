## Objetivo
Este documento descreve **todos os campos** (obrigatórios e opcionais) do XML de **DPS** conforme o anexo de leiaute do Sistema Nacional NFS-e.

## Fonte
- **Anexo de layout**: `ANEXO_I-SEFIN_ADN-DPS_NFSe-SNNFSe-v1.01-20260209` (aba **LEIAUTE DPS_NFS-e**)
- **Documentação técnica (Portal NFS-e)**: `https://www.gov.br/nfse/pt-br/biblioteca/documentacao-tecnica`
- **Manual (espelho acessível)**: `https://www.dinamicasistemas.com.br/upload/files/Manual%20Contribuintes%20Emissor%20P%C3%BAblico%20API%20-%20Sistema%20Nacional%20NFS-e%20v1_2%20out-2025.pdf`

## Como ler este documento
- **CAMINHO NO XML**: caminho do nó (como aparece no anexo).
- **CAMPO**: nome do elemento/atributo/grupo.
- **ELE**: tipo do item no anexo (E=elemento, A/CE=atributo, G=grupo, CG=grupo de escolha, ID=atributo ID).
- **OCOR.**: ocorrência (ex.: `1-1` obrigatório, `0-1` opcional, `1-1000` lista).
- **TIPO/TAM.**: tipo e tamanho conforme o anexo.

## Campos (na ordem do anexo)
### NFSe/infNFSe/DPS/versao
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `A` / `C` / `1-4V2`
- **Descrição**: Versão do leiaute da DPS.

### NFSe/infNFSe/DPS/infDPS
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `G` / `-` / `-`
- **Descrição**: Grupo de Informações da
Declaração de Prestação de Serviços - DPS

### NFSe/infNFSe/DPS/infDPS/id
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `ID` / `C` / `45`
- **Descrição**: O identificador da DPS é composto pela concatenação de campos que constam no leiaute da DPS.
A formação deste identificador considera o literal "DPS" associado a outras 42 posições numéricas, conforme descrito abaixo:

"DPS" + 
Cód.Mun. (7) + 
Tipo de Inscrição Federal (1) + 
Inscrição Federal (14 - CPF completar com 000 à esquerda) + 
Série DPS (5) + 
Núm. DPS (15)
- **Notas**:

  Tipo de inscrição Federal = 1 / Inscrição Federal = CPF emitente da DPS;
  Tipo de inscrição Federal = 2 / Inscrição Federal = CNPJ emitente da DPS;

### NFSe/infNFSe/DPS/infDPS/tpAmb
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `N` / `1`
- **Descrição**: Identificação do tipo de ambiente no Sistema Nacional NFS-e: 
1 - Produção; 
2 - Homologação;

### NFSe/infNFSe/DPS/infDPS/dhEmi
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `D` / `-`
- **Descrição**: Data e hora da emissão da DPS.
Data e hora no formato UTC (Universal Coordinated Time):
AAAA-MM-DDThh:mm:ssTZD

### NFSe/infNFSe/DPS/infDPS/verAplic
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `C` / `1-20`
- **Descrição**: Versão do aplicativo que gerou a DPS.

### NFSe/infNFSe/DPS/infDPS/serie
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `N` / `1-5`
- **Descrição**: Série da DPS.
- **Notas**:

  Faixas de utilização da série da DPS:
   00001 a 49999 - Emissão com aplicativo pŕoprio;
   50000 a 69999 - Emissor Móvel;
   70000 a 79999 - Emissor Web;
   80000 a 89999 - Emissão com *transcrição manual (Web);
   *O emitente deve informar o número de série (transcrever o número de série) que foi repassado ao não emitente da NFS-e.

### NFSe/infNFSe/DPS/infDPS/nDPS
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `N` / `1-15`
- **Descrição**: Número da DPS.
- **Notas**:

  1 até 999999999999999

### NFSe/infNFSe/DPS/infDPS/dCompet
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `D` / `-`
- **Descrição**: Data de competência da prestação do serviço.
Ano, Mês e Dia (AAAA-MM-DD)
- **Notas**:

  A data de competência deve ser única e ser a mesma que a data do fato gerador do tributo, ou seja, a data da prestação do serviço.

### NFSe/infNFSe/DPS/infDPS/tpEmit
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `N` / `1`
- **Descrição**: Emitente da DPS:

1 - Prestador;
2 - Tomador;
3 - Intermediário;

### NFSe/infNFSe/DPS/infDPS/cMotivoEmisTI
- **Ocorrência**: `0-1`
- **ELE/TIPO/TAM**: `E` / `N` / `1`
- **Descrição**: Motivo da Emissão da DPS pelo Tomador/Intermediário:

1 - Importação de Serviço;
2 - Tomador/Intermediário obrigado a emitir NFS-e por legislação municipal;
3 - Tomador/Intermediário emitindo NFS-e por recusa de emissão pelo prestador;
4 - Tomador/Intermediário emitindo por rejeitar a NFS-e emitida pelo prestador;
- **Notas**:

  Se o município de incidência não for o do tomador, o sistema deve rejeitar eventuais retenções.

### NFSe/infNFSe/DPS/infDPS/chNFSeRej
- **Ocorrência**: `0-1`
- **ELE/TIPO/TAM**: `E` / `N` / `50`
- **Descrição**: Chave de Acesso da NFS-e rejeitada pelo Tomador/Intermediário.
- **Notas**:

  O tomador deve referenciar neste campo a nota do prestador, utilizando-se da chave da NFS-e emitida pelo prestador e previamente rejeitada pelo tomador, ou seja, o Tomador/Intermediário antes de emitir sua NFS-e pelo motivo 4 do campo cMotivoEmisTI deverá emitir um Evento de Manifestação de NFS-e de rejeição para a NFS-e emitida pelo prestador, cuja chave de acesso será informada neste campo chNFSeRej.

### NFSe/infNFSe/DPS/infDPS/cLocEmi
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `N` / `7`
- **Descrição**: Código de 7 dígitos da localidade emissora da NFS-e.
- **Notas**:

  O campo cLocEmi (Código da Localidade de Emissão da DPS) sempre corresponderá a um município brasileiro e identificado pela tabela de códigos de municípios do IBGE ou um trecho de concessão de exploração de rodovia para a qual a NFS-e foi emiitida.
  O município emissor da NFS-e é aquele município em que o emitente da DPS está cadastrado e autorizado a "emitir uma NFS-e", ou seja, emitir uma DPS para que o sistema nacional valide as informações nela prestadas e gere a NFS-e correspondente para o emitente.
  Para que o sistema nacional emita a NFS-e o município emissor deve ser conveniado e estar ativo no sistema nacional. Além disso o convênio do município deve permitir que os contribuintes do município utilize os emissores públicos do Sistema Nacional NFS-e.

### NFSe/infNFSe/DPS/infDPS/subst
- **Ocorrência**: `0-1`
- **ELE/TIPO/TAM**: `G` / `-` / `-`
- **Descrição**: Grupo de informações relativas à NFS-e a ser substituída

### NFSe/infNFSe/DPS/infDPS/substchSubstda
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `C` / `50`
- **Descrição**: Chave de Acesso da NFS-e a ser substituída.
- **Notas**:

  O município conveniado ao Sistema Nacional NFS-e deverá parametrizar o prazo máximo permitido para que o emitente da NFS-e possa substituir uma NFS-e que o município tenha gerado.
  Prazo máximo parametrizável é 2 anos.
  O município conveniado ao Sistema Nacional NFS-e deverá parametrizar se impede ou não a substituição de nota caso a nota Substuída não tenha as informações do NI do tomador
  Um evento de bloqueio de ofício para qualquer outro tipo de evento é considerado vigente se não há um correspondente evento de desbloqueio de ofício que contemple o tipo de evento bloqueado.

### NFSe/infNFSe/DPS/infDPS/substcMotivo
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `N` / `1`
- **Descrição**: Código de justificativa para substituição de NFS-e:

1 - Desenquadramento de NFS-e do Simples Nacional;
2 - Enquadramento de NFS-e no Simples Nacional;
3 - Inclusão Retroativa de Imunidade/Isenção para NFS-e;
4 - Exclusão Retroativa de Imunidade/Isenção para NFS-e;
5 - Rejeição de NFS-e pelo tomador ou pelo intermediário se responsável pelo recolhimento do tributo;
99 - Outros;
- **Notas**:

  Rejeição de NFS-e pelo tomador ou pelo intermediário se responsável pelo recolhimento do tributo.

### NFSe/infNFSe/DPS/infDPS/substxMotivo
- **Ocorrência**: `0-1`
- **ELE/TIPO/TAM**: `E` / `C` / `15-255`
- **Descrição**: Descrição do motivo da substituição da NFS-e quando o emitente deve descrever o motivo da substituição para outros motivos (cMotivo = 99).

### NFSe/infNFSe/DPS/infDPS/prest
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `G` / `-` / `-`
- **Descrição**: Grupo de informações relativas ao prestador do serviço

### NFSe/infNFSe/DPS/infDPS/prest/CNPJ
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `CE` / `N` / `14`
- **Descrição**: Número da inscrição federal (CNPJ) do prestador do serviço.

### NFSe/infNFSe/DPS/infDPS/prest/CPF
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `CE` / `N` / `11`
- **Descrição**: Número da inscrição federal (CPF) do prestador do serviço.

### NFSe/infNFSe/DPS/infDPS/prest/NIF
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `CE` / `C` / `40`
- **Descrição**: Número de identificação fiscal fornecido por órgão de administração tributária no exterior.

### NFSe/infNFSe/DPS/infDPS/prest/cNaoNIF
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `CE` / `N` / `1`
- **Descrição**: Motivo para não informação do NIF:

0 - Não informado na nota de origem;
1 - Dispensado do NIF;
2 - Não exigência do NIF;
- **Notas**:

  O valor 0 deve ser utilizado como opção de preenchimento do campo somente no compartilhamento de NFS-e pelo municipio com o ADN NFS-e.

### NFSe/infNFSe/DPS/infDPS/prest/CAEPF
- **Ocorrência**: `0-1`
- **ELE/TIPO/TAM**: `E` / `N` / `14`
- **Descrição**: Número do Cadastro de Atividade Econômica da Pessoa Física (CAEPF).

### NFSe/infNFSe/DPS/infDPS/prest/IM
- **Ocorrência**: `0-1`
- **ELE/TIPO/TAM**: `E` / `C` / `15`
- **Descrição**: Número do indicador municipal do prestador do serviço.

### NFSe/infNFSe/DPS/infDPS/prest/xNome
- **Ocorrência**: `0-1`
- **ELE/TIPO/TAM**: `E` / `C` / `150`
- **Descrição**: Nome / Nome Empresarial do prestador.

### NFSe/infNFSe/DPS/infDPS/prest/end
- **Ocorrência**: `0-1`
- **ELE/TIPO/TAM**: `G` / `-` / `-`
- **Descrição**: Grupo de informações do endereço do prestador de serviço.

### NFSe/infNFSe/DPS/infDPS/prest/end/endNac
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `CG` / `-` / `-`
- **Descrição**: Grupo de informações do endereço nacional.

### NFSe/infNFSe/DPS/infDPS/prest/end/endNac/cMun
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `N` / `7`
- **Descrição**: Código do município do endereço do prestador do serviço.
 (Tabela do IBGE)

### NFSe/infNFSe/DPS/infDPS/prest/end/endNac/CEP
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `C` / `8`
- **Descrição**: Código numérico do Endereçamento Postal nacional (CEP) 
 do endereço do prestador do serviço.

### NFSe/infNFSe/DPS/infDPS/prest/end/endExt
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `CG` / `-` / `-`
- **Descrição**: Grupo de informações do endereço no exterior.

### NFSe/infNFSe/DPS/infDPS/prest/end/endExt/cPais
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `C` / `2`
- **Descrição**: Código do país do endereço do prestador do prestador do serviço.
 (Tabela de Países ISO)

### NFSe/infNFSe/DPS/infDPS/prest/end/endExt/cEndPost
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `C` / `1-11`
- **Descrição**: Código alfanumérico do Endereçamento Postal no exterior do prestador do serviço.

### NFSe/infNFSe/DPS/infDPS/prest/end/endExt/xCidade
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `C` / `1-60`
- **Descrição**: Nome da cidade no exterior do prestador do serviço.

### NFSe/infNFSe/DPS/infDPS/prest/end/endExt/xEstProvReg
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `C` / `1-60`
- **Descrição**: Estado, província ou região da cidade no exterior do prestador do serviço.

### NFSe/infNFSe/DPS/infDPS/prest/end/xLgr
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `C` / `1-255`
- **Descrição**: Tipo e nome do logradouro do endereço do prestador do serviço.

### NFSe/infNFSe/DPS/infDPS/prest/end/nro
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `C` / `1-60`
- **Descrição**: Número no logradouro do endereço do prestador do serviço.

### NFSe/infNFSe/DPS/infDPS/prest/end/xCpl
- **Ocorrência**: `0-1`
- **ELE/TIPO/TAM**: `E` / `C` / `1-156`
- **Descrição**: Complemento do endereço do prestador do serviço.

### NFSe/infNFSe/DPS/infDPS/prest/end/xBairro
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `C` / `1-60`
- **Descrição**: Bairro do endereço do prestador do serviço.

### NFSe/infNFSe/DPS/infDPS/prest/fone
- **Ocorrência**: `0-1`
- **ELE/TIPO/TAM**: `E` / `N` / `6-20`
- **Descrição**: Número do telefone do prestador.
(Preencher com o Código DDD + número do telefone. 
Nas operações com exterior é permitido informar o código do país + código da localidade + número do telefone)

### NFSe/infNFSe/DPS/infDPS/prest/email
- **Ocorrência**: `0-1`
- **ELE/TIPO/TAM**: `E` / `C` / `1-80`
- **Descrição**: E-mail do prestador.

### NFSe/infNFSe/DPS/infDPS/prest/regTrib
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `G` / `-` / `-`
- **Descrição**: Grupo de informações relativas aos regimes de tributação do prestador de serviços

### NFSe/infNFSe/DPS/infDPS/prest/regTrib/opSimpNac
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `N` / `1`
- **Descrição**: Situação perante Simples Nacional:

1 - Não Optante;
   2 - Optante - Microempreendedor Individual (MEI);
   3 - Optante - Microempresa ou Empresa de Pequeno Porte (ME/EPP);

### NFSe/infNFSe/DPS/infDPS/prest/regTrib/regApTribSN
- **Ocorrência**: `0-1`
- **ELE/TIPO/TAM**: `E` / `N` / `1`
- **Descrição**: Regime de Apuração Tributária pelo Simples Nacional.

Opção para que o contribuinte optante pelo Simples Nacional ME/EPP (opSimpNac = 3) possa indicar, ao emitir o documento fiscal, em qual regime de apuração os tributos federais e municipal estão inseridos, caso tenha ultrapassado algum sublimite ou limite definido para o Simples Nacional.
 
1 – Regime de apuração dos tributos federais e municipal pelo SN;
2 – Regime de apuração dos tributos federais pelo SN e o ISSQN pela NFS-e conforme respectiva legislação municipal do tributo;
3 – Regime de apuração dos tributos federais e municipal pela NFS-e conforme respectivas legislações federal e municipal de cada tributo;
- **Notas**:

  1 - Um MEI, identificado como tal na data de competência informada na DPS após a verificação na base de dados do Simples Nacional, será tratado sempre como MEI no Sistema Nacional NFS-e, independentemente de quaisquer circustâncias que o próprio MEI tenha detectado que o descaracterize como MEI. A informação da situação do MEI sempre será aquela que for verificada no Simples Nacional na data de competência informada na DPS.
  2 - Uma ME/EPP deixará de apurar o ISSQN pelo Simples Nacional quando atribuir ao campo regAPTribSN os valores 2 ou 3, conforme leiaute DPS.

### NFSe/infNFSe/DPS/infDPS/prest/regTrib/regEspTrib
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `N` / `1`
- **Descrição**: Tipos de Regimes Especiais de Tributação Municipal:

0 - Nenhum;
1 - Ato Cooperado (Cooperativa);
2 - Estimativa;
3 - Microempresa Municipal;
4 - Notário ou Registrador;
5 - Profissional Autônomo;
6 - Sociedade de Profissionais;
9 - Outros;

### NFSe/infNFSe/DPS/infDPS/toma
- **Ocorrência**: `0-1`
- **ELE/TIPO/TAM**: `G` / `-` / `-`
- **Descrição**: Grupo de informações relativas ao tomador do serviço

### NFSe/infNFSe/DPS/infDPS/toma/CNPJ
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `CE` / `N` / `14`
- **Descrição**: Número da inscrição federal (CNPJ) do tomador de serviço.

### NFSe/infNFSe/DPS/infDPS/toma/CPF
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `CE` / `N` / `11`
- **Descrição**: Número da inscrição federal (CPF) do tomador do serviço.

### NFSe/infNFSe/DPS/infDPS/toma/NIF
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `CE` / `C` / `40`
- **Descrição**: Número de identificação fiscal fornecido por órgão de administração tributária no exterior.

### NFSe/infNFSe/DPS/infDPS/toma/cNaoNIF
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `CE` / `N` / `1`
- **Descrição**: Motivo para não informação do NIF:

0 - Não informado na nota de origem;
1 - Dispensado do NIF;
2 - Não exigência do NIF;
- **Notas**:

  O valor 0 deve ser utilizado como opção de preenchimento do campo somente no compartilhamento de NFS-e pelo municipio com o ADN NFS-e.

### NFSe/infNFSe/DPS/infDPS/toma/CAEPF
- **Ocorrência**: `0-1`
- **ELE/TIPO/TAM**: `E` / `N` / `14`
- **Descrição**: Número do Cadastro de Atividade Econômica da Pessoa Física (CAEPF).

### NFSe/infNFSe/DPS/infDPS/toma/IM
- **Ocorrência**: `0-1`
- **ELE/TIPO/TAM**: `E` / `C` / `15`
- **Descrição**: Número do indicador municipal do tomador do serviço.

### NFSe/infNFSe/DPS/infDPS/toma/xNome
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `C` / `150`
- **Descrição**: Nome / Nome Empresarial do tomador.

### NFSe/infNFSe/DPS/infDPS/toma/end
- **Ocorrência**: `0-1`
- **ELE/TIPO/TAM**: `G` / `-` / `-`
- **Descrição**: Grupo de informações do endereço do tomador do serviço.

### NFSe/infNFSe/DPS/infDPS/toma/end/endNac
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `CG` / `-` / `-`
- **Descrição**: Grupo de informações do endereço nacional.

### NFSe/infNFSe/DPS/infDPS/toma/end/endNac/cMun
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `N` / `7`
- **Descrição**: Código do município do endereço do tomador do serviço.
 (Tabela do IBGE)

### NFSe/infNFSe/DPS/infDPS/toma/end/endNac/CEP
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `C` / `8`
- **Descrição**: Código numérico do Endereçamento Postal nacional (CEP) 
 do endereço do tomador do serviço.

### NFSe/infNFSe/DPS/infDPS/toma/end/endExt
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `CG` / `-` / `-`
- **Descrição**: Grupo de informações do endereço no exterior.

### NFSe/infNFSe/DPS/infDPS/toma/end/endExt/cPais
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `C` / `2`
- **Descrição**: Código do país do endereço do prestador do tomador do serviço.
 (Tabela de Países ISO)

### NFSe/infNFSe/DPS/infDPS/toma/end/endExt/cEndPost
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `C` / `1-11`
- **Descrição**: Código alfanumérico do Endereçamento Postal no exterior do tomador do serviço.

### NFSe/infNFSe/DPS/infDPS/toma/end/endExt/xCidade
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `C` / `1-60`
- **Descrição**: Nome da cidade no exterior do tomador do serviço.

### NFSe/infNFSe/DPS/infDPS/toma/end/endExt/xEstProvReg
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `C` / `1-60`
- **Descrição**: Estado, província ou região da cidade no exterior do tomador do serviço.

### NFSe/infNFSe/DPS/infDPS/toma/end/xLgr
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `C` / `1-255`
- **Descrição**: Tipo e nome do logradouro do endereço do tomador do serviço.

### NFSe/infNFSe/DPS/infDPS/toma/end/nro
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `C` / `1-60`
- **Descrição**: Número no logradouro do endereço do tomador do serviço.

### NFSe/infNFSe/DPS/infDPS/toma/end/xCpl
- **Ocorrência**: `0-1`
- **ELE/TIPO/TAM**: `E` / `C` / `1-156`
- **Descrição**: Complemento do endereço do tomador do serviço.

### NFSe/infNFSe/DPS/infDPS/toma/end/xBairro
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `C` / `1-60`
- **Descrição**: Bairro do endereço do tomador do serviço.

### NFSe/infNFSe/DPS/infDPS/toma/fone
- **Ocorrência**: `0-1`
- **ELE/TIPO/TAM**: `E` / `N` / `6-20`
- **Descrição**: Número do telefone do tomador.
(Preencher com o Código DDD + número do telefone. 
Nas operações com exterior é permitido informar o código do país + código da localidade + número do telefone)

### NFSe/infNFSe/DPS/infDPS/toma/email
- **Ocorrência**: `0-1`
- **ELE/TIPO/TAM**: `E` / `C` / `1-80`
- **Descrição**: E-mail do tomador.

### NFSe/infNFSe/DPS/infDPS/interm
- **Ocorrência**: `0-1`
- **ELE/TIPO/TAM**: `G` / `-` / `-`
- **Descrição**: Grupo de informações relativas ao intermediário do serviço

### NFSe/infNFSe/DPS/infDPS/interm/CNPJ
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `CE` / `N` / `14`
- **Descrição**: Número da inscrição federal (CNPJ) do intermediário de serviço

### NFSe/infNFSe/DPS/infDPS/interm/CPF
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `CE` / `N` / `11`
- **Descrição**: Número da inscrição federal (CPF) do intermediário do serviço

### NFSe/infNFSe/DPS/infDPS/interm/NIF
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `CE` / `C` / `40`
- **Descrição**: Número de identificação fiscal fornecido por órgão de administração tributária no exterior

### NFSe/infNFSe/DPS/infDPS/interm/cNaoNIF
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `CE` / `N` / `1`
- **Descrição**: Motivo para não informação do NIF:

0 - Não informado na nota de origem;
1 - Dispensado do NIF;
2 - Não exigência do NIF;
- **Notas**:

  O valor 0 deve ser utilizado como opção de preenchimento do campo somente no compartilhamento de NFS-e pelo municipio com o ADN NFS-e.

### NFSe/infNFSe/DPS/infDPS/interm/CAEPF
- **Ocorrência**: `0-1`
- **ELE/TIPO/TAM**: `E` / `N` / `14`
- **Descrição**: Número do Cadastro de Atividade Econômica da Pessoa Física (CAEPF).

### NFSe/infNFSe/DPS/infDPS/interm/IM
- **Ocorrência**: `0-1`
- **ELE/TIPO/TAM**: `E` / `C` / `15`
- **Descrição**: Número do indicador municipal do intermediário do serviço

### NFSe/infNFSe/DPS/infDPS/interm/xNome
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `C` / `150`
- **Descrição**: Nome / Nomer Empresarial do intermediário

### NFSe/infNFSe/DPS/infDPS/interm/end
- **Ocorrência**: `0-1`
- **ELE/TIPO/TAM**: `G` / `-` / `-`
- **Descrição**: Grupo de informações do endereço do intermediário do serviço.

### NFSe/infNFSe/DPS/infDPS/interm/end/endNac
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `CG` / `-` / `-`
- **Descrição**: Grupo de informações do endereço nacional.

### NFSe/infNFSe/DPS/infDPS/interm/end/endNac/cMun
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `N` / `7`
- **Descrição**: Código do município do endereço do intermediário do serviço.
 (Tabela do IBGE)

### NFSe/infNFSe/DPS/infDPS/interm/end/endNac/CEP
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `C` / `8`
- **Descrição**: Código numérico do Endereçamento Postal nacional (CEP) 
 do endereço do intermediário do serviço.

### NFSe/infNFSe/DPS/infDPS/interm/end/endExt
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `CG` / `-` / `-`
- **Descrição**: Grupo de informações do endereço no exterior.

### NFSe/infNFSe/DPS/infDPS/interm/end/endExt/cPais
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `C` / `2`
- **Descrição**: Código do país do endereço do prestador do intermediário do serviço.
 (Tabela de Países ISO)

### NFSe/infNFSe/DPS/infDPS/interm/end/endExt/cEndPost
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `C` / `1-11`
- **Descrição**: Código alfanumérico do Endereçamento Postal no exterior do intermediário do serviço.

### NFSe/infNFSe/DPS/infDPS/interm/end/endExt/xCidade
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `C` / `1-60`
- **Descrição**: Nome da cidade no exterior do intermediário do serviço.

### NFSe/infNFSe/DPS/infDPS/interm/end/endExt/xEstProvReg
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `C` / `1-60`
- **Descrição**: Estado, província ou região da cidade no exterior do intermediário do serviço.

### NFSe/infNFSe/DPS/infDPS/interm/end/xLgr
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `C` / `1-255`
- **Descrição**: Tipo e nome do logradouro do endereço do intermediário do serviço.

### NFSe/infNFSe/DPS/infDPS/interm/end/nro
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `C` / `1-60`
- **Descrição**: Número no logradouro do endereço do intermediário do serviço.

### NFSe/infNFSe/DPS/infDPS/interm/end/xCpl
- **Ocorrência**: `0-1`
- **ELE/TIPO/TAM**: `E` / `C` / `1-156`
- **Descrição**: Complemento do endereço do intermediário do serviço.

### NFSe/infNFSe/DPS/infDPS/interm/end/xBairro
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `C` / `1-60`
- **Descrição**: Bairro do endereço do intermediário do serviço.

### NFSe/infNFSe/DPS/infDPS/interm/fone
- **Ocorrência**: `0-1`
- **ELE/TIPO/TAM**: `E` / `N` / `6-20`
- **Descrição**: Número do telefone do intermediário.
(Preencher com o Código DDD + número do telefone. 
Nas operações com exterior é permitido informar o código do país + código da localidade + número do telefone)

### NFSe/infNFSe/DPS/infDPS/interm/email
- **Ocorrência**: `0-1`
- **ELE/TIPO/TAM**: `E` / `C` / `1-80`
- **Descrição**: E-mail do intermediário.

### NFSe/infNFSe/DPS/infDPS/serv
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `G` / `-` / `-`
- **Descrição**: Grupo de informações relativas ao serviço prestado

### NFSe/infNFSe/DPS/infDPS/serv/locPrest
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `G` / `-` / `-`
- **Descrição**: Grupo de informações relativas ao local da prestação do serviço
- **Notas**:

  OBS: As operações de exploração de vias (ou rodovias) no campo de incidência do ISSQN (subitem 22.01 da lista de serviço do Sistema Nacional NFS-e) serão formalizadas pela "NFS-e Via", Nota Fiscal de Serviço eletrônica de Exploração de Via, que terá um layout específico a ser publicado em breve.
   Para atender o dispositivo do Art 3º, § 3º,
   (Considera-se ocorrido o fato gerador do imposto no local do estabelecimento prestador nos serviços executados em águas marítimas, excetuados os serviços descritos no subitem 20.01)
   o Sistema Nacional NFS-e "Águas Marítimas" como uma localidade de prestação de serviço, assim como qualquer município brasileiro.
   cLocPrestacao poderá assumir: qualquer código que represente um município da tabela de códigos de municípios do IBGE, qualquer código quer represente um trecho de concessão de exploração de rodovias do cadastro próprio do Sistema Nacional NFS-e ou 0000000, que representa "Águas Marítimas".

### NFSe/infNFSe/DPS/infDPS/serv/locPrest/cLocPrestacao
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `CE` / `N` / `7`
- **Descrição**: Código da localidade da prestação do serviço.

### NFSe/infNFSe/DPS/infDPS/serv/locPrest/cPaisPrestacao
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `CE` / `C` / `2`
- **Descrição**: Código do país onde ocorreu a prestação do serviço.
(Tabela de Países ISO)

### NFSe/infNFSe/DPS/infDPS/serv/cServ
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `G` / `-` / `-`
- **Descrição**: Grupo de informações relativas ao código do serviço prestado

### NFSe/infNFSe/DPS/infDPS/serv/cServ/cTribNac
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `N` / `6`
- **Descrição**: Código de tributação nacional do ISSQN, nos termos da LC 116/2003, Conforme aba MUN.INCID_INFO.SERV. do ANEXO I
- **Notas**:

  Para o caso de serviço prestado em "Águas Marítimas" o seviço informado nunca poderá ser 20.01

### NFSe/infNFSe/DPS/infDPS/serv/cServ/cTribMun
- **Ocorrência**: `0-1`
- **ELE/TIPO/TAM**: `E` / `N` / `3`
- **Descrição**: Código de tributação municipal do ISSQN.

### NFSe/infNFSe/DPS/infDPS/serv/cServ/xDescServ
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `C` / `1000`
- **Descrição**: Descrição completa do serviço prestado

### NFSe/infNFSe/DPS/infDPS/serv/cServ/cNBS
- **Ocorrência**: `0-1`
- **ELE/TIPO/TAM**: `E` / `N` / `9`
- **Descrição**: Código NBS correspondente ao serviço prestado, seguindo a versão 2.0, conforme Anexo B.
- **Notas**:

  NBS - Nomenclatura Brasileira de Serviços, Intangíveis e outras Operações que produzam Variações no Patrimônio

### NFSe/infNFSe/DPS/infDPS/serv/cServ/cIntContrib
- **Ocorrência**: `0-1`
- **ELE/TIPO/TAM**: `E` / `C` / `20`
- **Descrição**: Código interno do contribuinte
- **Notas**:

  Utilizado para identificação da DPS no Sistema interno do Contribuinte

### NFSe/infNFSe/DPS/infDPS/serv/comExt
- **Ocorrência**: `0-1`
- **ELE/TIPO/TAM**: `G` / `-` / `-`
- **Descrição**: Grupo de informações sobre transações entre residentes ou domiciliados no Brasil com residentes ou domiciliados no exterior

### NFSe/infNFSe/DPS/infDPS/serv/comExt/mdPrestacao
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `N` / `1`
- **Descrição**: Modo de Prestação:

0 - Desconhecido (tipo não informado na nota de origem);
1 - Transfronteiriço;
2 - Consumo no Brasil;
3 - Movimento Temporário de Pessoas Físicas;
4 - Consumo no Exterior;
- **Notas**:

  O valor 0 deve ser utilizado como opção de preenchimento do campo somente no compartilhamento de NFS-e pelo municipio com o ADN NFS-e.

### NFSe/infNFSe/DPS/infDPS/serv/comExt/vincPrest
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `N` / `1`
- **Descrição**: Vínculo entre as partes no negócio:

0 - Sem vínculo com o Tomador/Prestador
1 - Controlada;
2 - Controladora;
3 - Coligada;
4 - Matriz;
5 - Filial ou sucursal;
6 - Outro vínculo;
9 - Desconhecido (tipo não informado na nota de origem);
- **Notas**:

  Adicionar ao Hint do campo: O adquirente/ Prestador do serviço é pessoa considerada vinculada ao prestador/ adquirente, nos termos do art. 23 da Lei nº 9.430, de 1996

### NFSe/infNFSe/DPS/infDPS/serv/comExt/tpMoeda
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `N` / `3`
- **Descrição**: Identifica a moeda da transação comercial. 
O usuário deve informar o código da moeda.
- **Notas**:

  inclusão, no Emissor Público, da tabela de moedas do Banco Central do Brasil para seleção pelo emitente da NFS-e. 
  No Painel Nacional deverá haver função para atualização da tabela de Moedas.

### NFSe/infNFSe/DPS/infDPS/serv/comExt/vServMoeda
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `N` / `1-15V2`
- **Descrição**: Valor do serviço prestado expresso em moeda estrangeira especificada em tpmoeda.

### NFSe/infNFSe/DPS/infDPS/serv/comExt/mecAFComexP
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `N` / `2`
- **Descrição**: Mecanismo de apoio/fomento ao Comércio Exterior utilizado pelo prestador do serviço:

00 - Desconhecido (tipo não informado na nota de origem);
01 - Nenhum;
02 - ACC - Adiantamento sobre Contrato de Câmbio – Redução a Zero do IR e do IOF;
  03 - ACE – Adiantamento sobre Cambiais Entregues - Redução a Zero do IR e do IOF;
04 - BNDES-Exim Pós-Embarque – Serviços;
 05 - BNDES-Exim Pré-Embarque - Serviços;
  06 - FGE - Fundo de Garantia à Exportação;
07 - PROEX - EQUALIZAÇÃO
 08 - PROEX - Financiamento;
- **Notas**:

  Campo disponível na nota do prestador.
  O valor 0 deve ser utilizado como opção de preenchimento do campo somente no compartilhamento de NFS-e pelo municipio com o ADN NFS-e.

### NFSe/infNFSe/DPS/infDPS/serv/comExt/mecAFComexT
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `N` / `2`
- **Descrição**: Mecanismo de apoio/fomento ao Comércio Exterior utilizado pelo tomador do serviço:

00 - Desconhecido (tipo não informado na nota de origem);
01 - Nenhum;
02 - Adm. Pública e Repr. Internacional;
03 - Alugueis e Arrend. Mercantil de maquinas, equip., embarc. e aeronaves;
04 - Arrendamento Mercantil de aeronave para empresa de transporte aéreo público;
05 - Comissão a agentes externos na exportação;
06 - Despesas de armazenagem, mov. e transporte de carga no exterior;
07 - Eventos FIFA (subsidiária);
08 - Eventos FIFA;
09 - Fretes, arrendamentos de embarcações ou aeronaves e outros;
10 - Material Aeronáutico;
11 - Promoção de Bens no Exterior;
12 - Promoção de Dest. Turísticos Brasileiros;
13 - Promoção do Brasil no Exterior;
14 - Promoção Serviços no Exterior;
15 - RECINE;
16 - RECOPA;
17 - Registro e Manutenção de marcas, patentes e cultivares;
18 - REICOMP;
19 - REIDI;
20 - REPENEC;
21 - REPES;
22 - RETAERO; 
23 - RETID;
24 - Royalties, Assistência Técnica, Científica e Assemelhados;
25 - Serviços de avaliação da conformidade vinculados aos Acordos da OMC;
26 - ZPE;
- **Notas**:

  Campo disponível na nota do tomador.
  O valor 0 deve ser utilizado como opção de preenchimento do campo somente no compartilhamento de NFS-e pelo municipio com o ADN NFS-e.

### NFSe/infNFSe/DPS/infDPS/serv/comExt/movTempBens
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `N` / `1`
- **Descrição**: Vínculo da Operação à Movimentação Temporária de Bens:

0 - Desconhecido (tipo não informado na nota de origem);
1 - Não;
2 - Vinculada - Declaração de Importação;
3 - Vinculada - Declaração de Exportação;
- **Notas**:

  O valor 0 deve ser utilizado como opção de preenchimento do campo somente no compartilhamento de NFS-e pelo municipio com o ADN NFS-e.

### NFSe/infNFSe/DPS/infDPS/serv/comExt/nDI
- **Ocorrência**: `0-1`
- **ELE/TIPO/TAM**: `E` / `C` / `1-12`
- **Descrição**: Número da Declaração de Importação (DI/DSI/DA/DRI-E) averbado.

### NFSe/infNFSe/DPS/infDPS/serv/comExt/nRE
- **Ocorrência**: `0-1`
- **ELE/TIPO/TAM**: `E` / `C` / `12`
- **Descrição**: Número do Registro de Exportação (RE) averbado.

### NFSe/infNFSe/DPS/infDPS/serv/comExt/mdic
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `N` / `1`
- **Descrição**: Indicador se a NFS-e deverá ser disponibilizada ao MDIC.

0 - Não enviar para o MDIC;
1 - Enviar para o MDIC;

### NFSe/infNFSe/DPS/infDPS/servobra
- **Ocorrência**: `0-1`
- **ELE/TIPO/TAM**: `G` / `-` / `-`
- **Descrição**: Grupo de informações relativas à obras de construção civil e congêneres

### NFSe/infNFSe/DPS/infDPS/serv/obra/inscImobFisc
- **Ocorrência**: `0-1`
- **ELE/TIPO/TAM**: `E` / `C` / `1-30`
- **Descrição**: Inscrição imobiliária fiscal 
(código fornecido pela prefeitura para a identificação da obra ou para fins de recolhimento do IPTU)

### NFSe/infNFSe/DPS/infDPS/serv/obra/cObra
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `CE` / `C` / `1-30`
- **Descrição**: Número de identificação da obra.
Cadastro Nacional de Obras (CNO) ou Cadastro Específico do INSS (CEI).

### NFSe/infNFSe/DPS/infDPS/serv/obra/cCIB
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `CE` / `C` / `8`
- **Descrição**: Código do Cadastro Imobiliário Brasileiro - CIB

### NFSe/infNFSe/DPS/infDPS/serv/obra/end
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `CG` / `-` / `-`
- **Descrição**: Grupo de informações do endereço da obra.

### NFSe/infNFSe/DPS/infDPS/serv/obra/end/CEP
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `CE` / `C` / `8`
- **Descrição**: Código de Endereçamento Postal numérico do endereço nacional da obra.

### NFSe/infNFSe/DPS/infDPS/serv/obra/end/endExt
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `CG` / `-` / `-`
- **Descrição**: Grupo de informações descritivas do endereço no exterior da obra.

### NFSe/infNFSe/DPS/infDPS/serv/obra/end/endExt/cEndPost
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `C` / `1-11`
- **Descrição**: Código de Endereçamento Postal alfanumérico do endereço no exterior da obra.

### NFSe/infNFSe/DPS/infDPS/serv/obra/end/endExt/xCidade
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `C` / `1-60`
- **Descrição**: Nome da cidade no exterior, local da obra.

### NFSe/infNFSe/DPS/infDPS/serv/obra/end/endExt/xEstProvReg
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `C` / `1-60`
- **Descrição**: Estado, província ou região da cidade no exterior, local da obra.

### NFSe/infNFSe/DPS/infDPS/serv/obra/end/xLgr
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `C` / `1-255`
- **Descrição**: Tipo e nome do logradouro do endereço da obra.

### NFSe/infNFSe/DPS/infDPS/serv/obra/end/nro
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `C` / `1-60`
- **Descrição**: Número no logradouro do endereço da obra.

### NFSe/infNFSe/DPS/infDPS/serv/obra/end/xCpl
- **Ocorrência**: `0-1`
- **ELE/TIPO/TAM**: `E` / `C` / `1-156`
- **Descrição**: Complemento do endereço da obra.

### NFSe/infNFSe/DPS/infDPS/serv/obra/end/xBairro
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `C` / `1-60`
- **Descrição**: Bairro do endereço da obra.

### NFSe/infNFSe/DPS/infDPS/serv/atvEvento
- **Ocorrência**: `0-1`
- **ELE/TIPO/TAM**: `G` / `-` / `-`
- **Descrição**: Grupo de informações relativas à atividades de eventos

### NFSe/infNFSe/DPS/infDPS/serv/atvEvento/xNome
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `C` / `255`
- **Descrição**: Nome do evento Artístico, Cultural, Esportivo, ...

### NFSe/infNFSe/DPS/infDPS/serv/atvEvento/dtIni
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `D` / `-`
- **Descrição**: Data de início da atividade de evento.
Ano, Mês e Dia (AAAA-MM-DD)

### NFSe/infNFSe/DPS/infDPS/serv/atvEvento/dtFim
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `D` / `-`
- **Descrição**: Data de fim da atividade de evento.
Ano, Mês e Dia (AAAA-MM-DD)

### NFSe/infNFSe/DPS/infDPS/serv/atvEvento/idAtvEvt
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `CE` / `C` / `1-30`
- **Descrição**: Identificação da Atividade de Evento 
(código identificador de evento determinado pela Administração Tributária Municipal)
- **Notas**:

  Choice com o grupo de endereço da atividade de evento

### NFSe/infNFSe/DPS/infDPS/serv/atvEvento/end
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `CG` / `-` / `-`
- **Descrição**: Grupo de informações do endereço da atividade de evento.

### NFSe/infNFSe/DPS/infDPS/serv/atvEvento/end/CEP
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `CE` / `C` / `8`
- **Descrição**: Código de Endereçamento Postal numérico do endereço nacional da atividade de evento.

### NFSe/infNFSe/DPS/infDPS/serv/atvEvento/end/endExt
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `CG` / `-` / `-`
- **Descrição**: Grupo de informações descritivas do endereço no exterior da atividade de evento.

### NFSe/infNFSe/DPS/infDPS/serv/atvEvento/end/endExt/cEndPost
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `C` / `11`
- **Descrição**: Código de Endereçamento Postal alfanumérico do endereço no exterior da atividade de evento.

### NFSe/infNFSe/DPS/infDPS/serv/atvEvento/end/endExt/xCidade
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `C` / `1-60`
- **Descrição**: Nome da cidade no exterior, local da atividade de evento.

### NFSe/infNFSe/DPS/infDPS/serv/atvEvento/end/endExt/xEstProvReg
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `C` / `1-60`
- **Descrição**: Estado, província ou região da cidade no exterior, local da atividade de evento.

### NFSe/infNFSe/DPS/infDPS/serv/atvEvento/end/xLgr
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `C` / `1-255`
- **Descrição**: Tipo e nome do logradouro do endereço da atividade de evento.

### NFSe/infNFSe/DPS/infDPS/serv/atvEvento/end/nro
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `C` / `1-60`
- **Descrição**: Número no logradouro do endereço da atividade de evento.

### NFSe/infNFSe/DPS/infDPS/serv/atvEvento/end/xCpl
- **Ocorrência**: `0-1`
- **ELE/TIPO/TAM**: `E` / `C` / `1-156`
- **Descrição**: Complemento do endereço da atividade de evento.

### NFSe/infNFSe/DPS/infDPS/serv/atvEvento/end/xBairro
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `C` / `1-60`
- **Descrição**: Bairro do endereço da atividade de evento.

### NFSe/infNFSe/DPS/infDPS/servinfoCompl
- **Ocorrência**: `0-1`
- **ELE/TIPO/TAM**: `G` / `-` / `-`
- **Descrição**: Grupo de informações complementares disponível para todos os serviços prestados
- **Notas**:

  Campos possíveis de preenchimento na DPS para todos os subitens da lista de serviços que forem prestados

### NFSe/infNFSe/DPS/infDPS/serv/infoCompl/idDocTec
- **Ocorrência**: `0-1`
- **ELE/TIPO/TAM**: `E` / `C` / `1-40`
- **Descrição**: Identificador de Documento de Responsabilidade Técnica:
ART, RRT, DRT, Outros.

### NFSe/infNFSe/DPS/infDPS/serv/infoCompl/docRef
- **Ocorrência**: `0-1`
- **ELE/TIPO/TAM**: `E` / `C` / `1-255`
- **Descrição**: Chave da nota, número identificador da nota, número do contrato ou outro identificador de documento emitido pelo prestador de serviços, que subsidia a emissão dessa nota pelo tomador do serviço ou intermediário (preenchimento obrigatório caso a nota esteja sendo emitida pelo Tomador ou intermediário do serviço).

### NFSe/infNFSe/DPS/infDPS/serv/infoCompl/xPed
- **Ocorrência**: `0-1`
- **ELE/TIPO/TAM**: `E` / `C` / `1-60`
- **Descrição**: Número do  pedido/ordem de compra/ordem de serviço/projeto que autorize a prestação do serviço em
operações B2B - Informação de interesse do tomador do serviço para controle e gestão da
Negociação

### NFSe/infNFSe/DPS/infDPS/serv/infoCompl/gItemPed
- **Ocorrência**: `0-1`
- **ELE/TIPO/TAM**: `G` / `-` / `-`
- **Descrição**: Grupo de itens do pedido/ordem de compra/ordem de serviço/projeto.

### NFSe/infNFSe/DPS/infDPS/serv/infoCompl/gItemPed/xItemPed
- **Ocorrência**: `1-99`
- **ELE/TIPO/TAM**: `E` / `C` / `1-60`
- **Descrição**: Número do item do  pedido/ordem de compra/ordem de serviço/projeto - Identificação do número do item do
pedido ou ordem de compra destacado e xPed

### NFSe/infNFSe/DPS/infDPS/serv/infoCompl/xInfComp
- **Ocorrência**: `0-1`
- **ELE/TIPO/TAM**: `E` / `C` / `2000`
- **Descrição**: Campo livre para preenchimento pelo contribuinte.

### NFSe/infNFSe/DPS/infDPS/valores
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `G` / `-` / `-`
- **Descrição**: Grupo de informações relativas à valores do serviço prestado

### NFSe/infNFSe/DPS/infDPS/valores/vServPrest
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `G` / `-` / `-`
- **Descrição**: Grupo de informações relativas aos valores do serviço prestado

### NFSe/infNFSe/DPS/infDPS/valores/vServPrest/vReceb
- **Ocorrência**: `0-1`
- **ELE/TIPO/TAM**: `E` / `N` / `1-15V2`
- **Descrição**: Valor monetário recebido pelo intermediário do serviço (R$).

### NFSe/infNFSe/DPS/infDPS/valores/vServPrest/vServ
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `N` / `1-15V2`
- **Descrição**: Valor monetário do serviço (R$).

### NFSe/infNFSe/DPS/infDPS/valores/vDescCondIncond
- **Ocorrência**: `0-1`
- **ELE/TIPO/TAM**: `G` / `-` / `-`
- **Descrição**: Grupo de informações relativas aos descontos condicionados e incondicionados

### NFSe/infNFSe/DPS/infDPS/valores/vDescCondIncond/vDescIncond
- **Ocorrência**: `0-1`
- **ELE/TIPO/TAM**: `E` / `N` / `1-15V2`
- **Descrição**: Valor monetário do desconto incondicionado (R$).

### NFSe/infNFSe/DPS/infDPS/valores/vDescCondIncond/vDescCond
- **Ocorrência**: `0-1`
- **ELE/TIPO/TAM**: `E` / `N` / `1-15V2`
- **Descrição**: Valor monetário do desconto condicionado (R$).

### NFSe/infNFSe/DPS/infDPS/valores/vDedRed
- **Ocorrência**: `0-1`
- **ELE/TIPO/TAM**: `G` / `-` / `-`
- **Descrição**: Grupo de informações relativas ao valores para dedução/redução do valor da base de cálculo (valor do serviço)
- **Notas**:

  Aqui referenciadas as deduções/reduções que serão consideradas apenas para a Base de Cálculo do ISSQN.

### NFSe/infNFSe/DPS/infDPS/valores/vDedRed/pDR
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `CE` / `N` / `1-3V2`
- **Descrição**: Valor percentual padrão para dedução/redução do valor do serviço.
- **Notas**:

  As três opções para informação de Dedução/Redução, caso exista, são:
  Valor, Percentual ou Documento;

### NFSe/infNFSe/DPS/infDPS/valores/vDedRed/vDR
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `CE` / `N` / `1-15V2`
- **Descrição**: Valor monetário padrão para dedução/redução do valor do serviço.

### NFSe/infNFSe/DPS/infDPS/valores/vDedRed/documentos
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `CG` / `-` / `-`
- **Descrição**: Grupo de informações de documento utilizado para dedução/redução do valor da base de cálculo (valor do serviço)

### NFSe/infNFSe/DPS/infDPS/valores/vDedRed/documentosdocDedRed
- **Ocorrência**: `1-1000`
- **ELE/TIPO/TAM**: `G` / `-` / `-`
- **Descrição**: Grupo de informações de documento utilizado para dedução/redução do valor da base de cálculo (valor do serviço)

### NFSe/infNFSe/DPS/infDPS/valores/vDedRed/documentos/docDedRed/chNFSe
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `CE` / `N` / `50`
- **Descrição**: Chave de acesso da NFS-e (padrão nacional).
- **Notas**:

  Para o caso de informação de documento para Dedução/Redução existem seis opções possíveis:
  NFS-e, 
  NF-e, 
  Outras NFS-e, 
  NFS/NFS (Modelo não eletrônico), 
  Outros documentos fiscais e
  Outros documentos;

### NFSe/infNFSe/DPS/infDPS/valores/vDedRed/documentos/docDedRed/chNFe
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `CE` / `N` / `44`
- **Descrição**: Chave de acesso da NF-e.

### NFSe/infNFSe/DPS/infDPS/valores/vDedRed/documentos/docDedRed/NFSeMun
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `CG` / `-` / `-`
- **Descrição**: Grupo de informações para outras notas eletrônicas municipais
(Nota eletrônica municipal emitida fora do padrão nacional)

### NFSe/infNFSe/DPS/infDPS/valores/vDedRed/documentos/docDedRed/NFSeMun/cMunNFSeMun
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `N` / `7`
- **Descrição**: Código Município emissor da nota eletrônica municipal.
(Tabela do IBGE)

### NFSe/infNFSe/DPS/infDPS/valores/vDedRed/documentos/docDedRed/NFSeMun/nNFSeMun
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `N` / `15`
- **Descrição**: Número da nota eletrônica municipal.

### NFSe/infNFSe/DPS/infDPS/valores/vDedRed/documentos/docDedRed/NFSeMun/cVerifNFSeMun
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `C` / `9`
- **Descrição**: Código de Verificação da nota eletrônica municipal.

### NFSe/infNFSe/DPS/infDPS/valores/vDedRed/documentos/docDedRed/NFNFS
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `CG` / `-` / `-`
- **Descrição**: Grupo de informações de NF ou NFS
(Modelo não eletrônico)

### NFSe/infNFSe/DPS/infDPS/valores/vDedRed/documentos/docDedRed/NFNFS/nNFS
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `N` / `7`
- **Descrição**: Número da Nota Fiscal NF ou NFS.

### NFSe/infNFSe/DPS/infDPS/valores/vDedRed/documentos/docDedRed/NFNFS/modNFS
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `N` / `15`
- **Descrição**: Modelo da Nota Fiscal NF ou NFS.

### NFSe/infNFSe/DPS/infDPS/valores/vDedRed/documentos/docDedRed/NFNFS/serieNFS
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `C` / `9`
- **Descrição**: Série Nota Fiscal NF ou NFS.

### NFSe/infNFSe/DPS/infDPS/valores/vDedRed/documentos/docDedRed/nDocFisc
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `CE` / `C` / `255`
- **Descrição**: Identificador de documento fiscal diferente dos demais do grupo.

### NFSe/infNFSe/DPS/infDPS/valores/vDedRed/documentos/docDedRed/nDoc
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `CE` / `C` / `255`
- **Descrição**: Identificador de documento não fiscal diferente dos demais do grupo.

### NFSe/infNFSe/DPS/infDPS/valores/vDedRed/documentos/docDedRed/tpDedRed
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `N` / `2`
- **Descrição**: Tipo da Dedução/Redução:

01 – Alimentação e bebidas/frigobar; 
02 – Materiais;
03 - Produção Externa;
04 - Reembolso de despesas;
  05 – Repasse consorciado;
  06 – Repasse plano de saúde;
  07 – Serviços;
08 – Subempreitada de mão de obra;
99 – Outras deduções;
- **Notas**:

  O grupo de informações de documentos para dedução/redução pode não ter correspondência para Municípios que não utilizem o padrão ABRASF na versão 2.04.
   Nesse caso, sugere-se que o Município que vá utilizar a transcrição da NFS-e do padrão de seu emissor para o padrão nacional, para encaminhamento ao ADN, apenas para essa fase inicial de implantação do padrão, utilize nessa operação o valor obtido como dedução/redução e o informe como “valor monetário”, e não “documentos”.
   Nesse sentido, também o Painel Administrativo Municipal deve ser parametrizado como dedução por “valor monetário” para o código de serviço correspondente, até que seu emissor próprio passe a refletir na origem o padrão nacional.
  A partir de janeiro de 2026, o tipo de dedução/redução " 01 – Alimentação e bebidas/frigobar" não será mais permitido.

### NFSe/infNFSe/DPS/infDPS/valores/vDedRed/documentos/docDedRed/xDescOutDed
- **Ocorrência**: `0-1`
- **ELE/TIPO/TAM**: `E` / `C` / `150`
- **Descrição**: Descrição da Dedução/Redução quando a opção é "99 – Outras Deduções".

### NFSe/infNFSe/DPS/infDPS/valores/vDedRed/documentos/docDedRed/dtEmiDoc
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `D` / `-`
- **Descrição**: Data da emissão do documento dedutível.
Ano, mês e dia (AAAA-MM-DD)

### NFSe/infNFSe/DPS/infDPS/valores/vDedRed/documentos/docDedRed/vDedutivelRedutivel
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `N` / `1-15V2`
- **Descrição**: Valor monetário total dedutível/redutível no documento informado (R$).
Este é o valor total no documento informado que é passível de dedução/redução.

### NFSe/infNFSe/DPS/infDPS/valores/vDedRed/documentos/docDedRed/vDeducaoReducao
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `N` / `1-15V2`
- **Descrição**: Valor monetário utilizado para dedução/redução do valor do serviço da NFS-e que está sendo emitida (R$). 
Deve ser menor ou igual ao valor deduzível/redutível (vDedutivelRedutivel).

### NFSe/infNFSe/DPS/infDPS/valores/vDedRed/documentos/docDedRed/fornec
- **Ocorrência**: `0-1`
- **ELE/TIPO/TAM**: `G` / `-` / `-`
- **Descrição**: Grupo de informações do fornecedor do serviço prestado

### NFSe/infNFSe/DPS/infDPS/valores/vDedRed/documentos/docDedRed/fornec/CNPJ
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `CE` / `N` / `14`
- **Descrição**: Número da inscrição federal (CNPJ) do fornecedor de serviço.

### NFSe/infNFSe/DPS/infDPS/valores/vDedRed/documentos/docDedRed/fornec/CPF
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `CE` / `N` / `11`
- **Descrição**: Número da inscrição federal (CPF) do fornecedor do serviço.

### NFSe/infNFSe/DPS/infDPS/valores/vDedRed/documentos/docDedRed/fornec/NIF
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `CE` / `C` / `40`
- **Descrição**: Este elemento só deverá ser preenchido para fornecedores não residentes no Brasil.

### NFSe/infNFSe/DPS/infDPS/valores/vDedRed/documentos/docDedRed/fornec/cNaoNIF
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `CE` / `N` / `1`
- **Descrição**: Motivo para não informação do NIF:

0 - Não informado na nota de origem;
1 - Dispensado do NIF;
2 - Não exigência do NIF;
- **Notas**:

  O valor 0 deve ser utilizado como opção de preenchimento do campo somente no compartilhamento de NFS-e pelo municipio com o ADN NFS-e.

### NFSe/infNFSe/DPS/infDPS/valores/vDedRed/documentos/docDedRed/fornec/CAEPF
- **Ocorrência**: `0-1`
- **ELE/TIPO/TAM**: `E` / `N` / `14`
- **Descrição**: Número do Cadastro de Atividade Econômica da Pessoa Física (CAEPF).

### NFSe/infNFSe/DPS/infDPS/valores/vDedRed/documentos/docDedRed/fornec/IM
- **Ocorrência**: `0-1`
- **ELE/TIPO/TAM**: `E` / `C` / `15`
- **Descrição**: Número do indicador municipal do fornecedor.

### NFSe/infNFSe/DPS/infDPS/valores/vDedRed/documentos/docDedRed/fornec/xNome
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `C` / `150`
- **Descrição**: Nome / Razão Social do fornecedor.

### NFSe/infNFSe/DPS/infDPS/valores/vDedRed/documentos/docDedRed/fornec/end
- **Ocorrência**: `0-1`
- **ELE/TIPO/TAM**: `G` / `-` / `-`
- **Descrição**: Grupo de informações do endereço do fornecedor.

### NFSe/infNFSe/DPS/infDPS/valores/vDedRed/documentos/docDedRed/fornec/end/endNac
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `CG` / `-` / `-`
- **Descrição**: Grupo de informações do endereço nacional.

### NFSe/infNFSe/DPS/infDPS/valores/vDedRed/documentos/docDedRed/fornec/end/endNac/cMun
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `N` / `7`
- **Descrição**: Código do município do endereço do fornecedor.
 (Tabela do IBGE)

### NFSe/infNFSe/DPS/infDPS/valores/vDedRed/documentos/docDedRed/fornec/end/endNac/CEP
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `C` / `8`
- **Descrição**: Código numérico do Endereçamento Postal nacional (CEP) 
 do endereço do fornecedor.

### NFSe/infNFSe/DPS/infDPS/valores/vDedRed/documentos/docDedRed/fornec/end/endExt
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `CG` / `-` / `-`
- **Descrição**: Grupo de informações do endereço no exterior.

### NFSe/infNFSe/DPS/infDPS/valores/vDedRed/documentos/docDedRed/fornec/end/endExt/cPais
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `C` / `2`
- **Descrição**: Código do país do endereço do prestador do fornecedor.
 (Tabela de Países ISO)

### NFSe/infNFSe/DPS/infDPS/valores/vDedRed/documentos/docDedRed/fornec/end/endExt/cEndPost
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `C` / `1-11`
- **Descrição**: Código alfanumérico do Endereçamento Postal no exterior do fornecedor.

### NFSe/infNFSe/DPS/infDPS/valores/vDedRed/documentos/docDedRed/fornec/end/endExt/xCidade
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `C` / `1-60`
- **Descrição**: Nome da cidade no exterior do fornecedor.

### NFSe/infNFSe/DPS/infDPS/valores/vDedRed/documentos/docDedRed/fornec/end/endExt/xEstProvReg
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `C` / `1-60`
- **Descrição**: Estado, província ou região da cidade no exterior do fornecedor.

### NFSe/infNFSe/DPS/infDPS/valores/vDedRed/documentos/docDedRed/fornec/xLgr
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `C` / `1-255`
- **Descrição**: Tipo e nome do logradouro do endereço do fornecedor.

### NFSe/infNFSe/DPS/infDPS/valores/vDedRed/documentos/docDedRed/fornec/nro
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `C` / `1-60`
- **Descrição**: Número no logradouro do endereço do fornecedor.

### NFSe/infNFSe/DPS/infDPS/valores/vDedRed/documentos/docDedRed/fornec/xCpl
- **Ocorrência**: `0-1`
- **ELE/TIPO/TAM**: `E` / `C` / `1-156`
- **Descrição**: Complemento do endereço do fornecedor.

### NFSe/infNFSe/DPS/infDPS/valores/vDedRed/documentos/docDedRed/fornec/xBairro
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `C` / `1-60`
- **Descrição**: Bairro do endereço do fornecedor.

### NFSe/infNFSe/DPS/infDPS/valores/vDedRed/documentos/docDedRed/fornec/fone
- **Ocorrência**: `0-1`
- **ELE/TIPO/TAM**: `E` / `N` / `6-20`
- **Descrição**: Número do telefone do fornecedor.
(Preencher com o Código DDD + número do telefone. 
Nas operações com exterior é permitido informar o código do país + código da localidade + número do telefone)

### NFSe/infNFSe/DPS/infDPS/valores/vDedRed/documentos/docDedRed/fornec/email
- **Ocorrência**: `0-1`
- **ELE/TIPO/TAM**: `E` / `C` / `1-80`
- **Descrição**: E-mail do fornecedor.

### NFSe/infNFSe/DPS/infDPS/valores/trib
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `G` / `-` / `-`
- **Descrição**: Grupo de informações relacionados aos tributos relacionados ao serviço prestado

### NFSe/infNFSe/DPS/infDPS/valores/trib/tribMun
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `G` / `-` / `-`
- **Descrição**: Grupo de informações relacionados ao 
Imposto Sobre Serviços de Qualquer Natureza - ISSQN

### NFSe/infNFSe/DPS/infDPS/valores/trib/tribMun/tribISSQN
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `N` / `1`
- **Descrição**: Tributação do ISSQN sobre o serviço prestado:

1 - Operação tributável;
2 - Imunidade;
3 - Exportação de serviço;
4 - Não Incidência;

### NFSe/infNFSe/DPS/infDPS/valores/trib/tribMun/cPaisResult
- **Ocorrência**: `0-1`
- **ELE/TIPO/TAM**: `E` / `C` / `2`
- **Descrição**: Código do país onde ocorreu o resultado do serviço prestado.
(Tabela de Países ISO)
- **Notas**:

  Se houver indicação pelo emitente de exportação de serviço, mesmo não havendo nenhum elemento para a ocorrência de exportação, então o emitente deve indicar em qual país ocorreu o resultado do serviço prestado.

### NFSe/infNFSe/DPS/infDPS/valores/trib/tribMun/tpImunidade
- **Ocorrência**: `0-1`
- **ELE/TIPO/TAM**: `E` / `N` / `1`
- **Descrição**: Identificação da Imunidade do ISSQN – somente para o caso de Imunidade.

Tipos de Imunidades:

0 - Imunidade (tipo não informado na nota de origem);
1 - Patrimônio, renda ou serviços, uns dos outros (CF88, Art 150, VI, a);
2 - Entidades religiosas e templos de qualquer culto, inclusive suas organizações assistenciais e beneficentes (CF88, Art 150, VI, b);
3 - Patrimônio, renda ou serviços dos partidos políticos, inclusive suas fundações, das entidades sindicais dos trabalhadores, das instituições de educação e de assistência social, sem fins lucrativos, atendidos os requisitos da lei (CF88, Art 150, VI, c);
4 - Livros, jornais, periódicos e o papel destinado a sua impressão (CF88, Art 150, VI, d);
5 - Fonogramas e videofonogramas musicais produzidos no Brasil contendo obras musicais ou literomusicais de autores brasileiros e/ou obras em geral interpretadas por artistas brasileiros bem como os suportes materiais ou arquivos digitais que os contenham, salvo na etapa de replicação industrial de mídias ópticas de leitura a laser.   (CF88, Art 150, VI, e);
- **Notas**:

  O valor 0 deve ser utilizado como opção de preenchimento do campo somente no compartilhamento de NFS-e pelo municipio com o ADN NFS-e.

### NFSe/infNFSe/DPS/infDPS/valores/trib/tribMun/exigSusp
- **Ocorrência**: `0-1`
- **ELE/TIPO/TAM**: `G` / `-` / `-`
- **Descrição**: Informações para a suspensão da Exigibilidade do ISSQN

### NFSe/infNFSe/DPS/infDPS/valores/trib/tribMun/exigSusp/tpSusp
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `N` / `1`
- **Descrição**: Opção para Exigibilidade Suspensa:

1 - Exigibilidade do ISSQN Suspensa por Decisão Judicial;
2 - Exigibilidade do ISSQN Suspensa por Processo Administrativo;

### NFSe/infNFSe/DPS/infDPS/valores/trib/tribMun/exigSusp/nProcesso
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `C` / `30`
- **Descrição**: Número do processo judicial ou administrativo de suspensão da exigibilidade.

### NFSe/infNFSe/DPS/infDPS/valores/trib/tribMun/BM
- **Ocorrência**: `0-1`
- **ELE/TIPO/TAM**: `G` / `-` / `-`
- **Descrição**: Grupo de informações sobre o tipo do Benefício Municipal

### NFSe/infNFSe/DPS/infDPS/valores/trib/tribMun/BM/nBM
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `N` / `14`
- **Descrição**: Identificador do benefício parametrizado pelo município.

Trata-se de um identificador único que foi gerado pelo Sistema Nacional no momento em que o município de incidência do ISSQN incluiu o benefício no sistema.

Critério de formação do número de identificação de parâmetros municipais:

7 dígitos - posição 1 a 7: número identificador do Município, conforme código IBGE;
2 dígitos - posições 8 e 9 : número identificador do tipo de parametrização 
(01-legislação, 02-regimes especiais, 03-retenções, 04-outros benefícios);
5 dígitos - posição 10 a 14 : número sequencial definido pelo sistema quando do registro específico do parâmetro dentro do tipo de parametrização no sistema;
- **Notas**:

  Trata-se de um identificador único que foi gerado pelo Sistema Nacional no momento em que o município de incidência do ISSQN incluiu o benefício no sistema.
  Critério de formação do número de identificação de parâmetros municipais:
  7 dígitos - posição 1 a 7: número identificador do Município, conforme código IBGE;
  2 dígitos - posições 8 e 9 : número identificador do tipo de parametrização 
  (01-legislação, 02-regimes especiais, 03-retenções, 04-outros benefícios);
  5 dígitos - posição 10 a 14 : número sequencial definido pelo sistema quando do registro específico do parâmetro dentro do tipo de parametrização no sistema;
  O emitente poderá obter essa informação de parametrização do município através de API própria que dará publicidade às parametrizações dos municípios.

### NFSe/infNFSe/DPS/infDPS/valores/trib/tribMun/BM/vRedBCBM
- **Ocorrência**: `0-1`
- **ELE/TIPO/TAM**: `E` / `N` / `1-15V2`
- **Descrição**: Valor monetário (R$) informado pelo emitente para redução da base de cálculo (BC) do ISSQN devido a um Benefício Municipal (BM).

### NFSe/infNFSe/DPS/infDPS/valores/trib/tribMun/BM/pRedBCBM
- **Ocorrência**: `0-1`
- **ELE/TIPO/TAM**: `E` / `N` / `1-3V2`
- **Descrição**: Valor percentual (%) informado pelo emitente para redução da base de cálculo (BC) do ISSQN devido a um Benefício Municipal (BM).
- **Notas**:

  O limite para este valor percentual informado pelo emitente está previamente parametrizado pelo município de incidência no cadastro do benefício municipal.

### NFSe/infNFSe/DPS/infDPS/valores/trib/tribMun/tpRetISSQN
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `N` / `1`
- **Descrição**: Tipo de retencao do ISSQN:

1 - Não Retido;
2 - Retido pelo Tomador;
3 - Retido pelo Intermediario;

### NFSe/infNFSe/DPS/infDPS/valores/trib/tribMun/pAliq
- **Ocorrência**: `0-1`
- **ELE/TIPO/TAM**: `E` / `N` / `1V2`
- **Descrição**: Valor da alíquota (%) do serviço prestado relativo ao município sujeito ativo (município de incidência) do ISSQN.
- **Notas**:

  Se o município de incidência pertence ao Sistema Nacional NFS-e a alíquota estará parametrizada e, portanto, será fornecida pelo sistema.
  Se o município de incidência não pertence ao Sistema Nacional NFS-e a alíquota não estará parametrizada e, por isso, deverá ser fornecida pelo emitente.

### NFSe/infNFSe/DPS/infDPS/valores/trib/tribFed
- **Ocorrência**: `0-1`
- **ELE/TIPO/TAM**: `G` / `-` / `-`
- **Descrição**: Grupo de informações de outros tributos relacionados ao serviço prestado

### NFSe/infNFSe/DPS/infDPS/valores/trib/tribFed/piscofins
- **Ocorrência**: `0-1`
- **ELE/TIPO/TAM**: `G` / `-` / `-`
- **Descrição**: Grupo de informações dos tributos PIS/COFINS

### NFSe/infNFSe/DPS/infDPS/valores/trib/tribFed/piscofins/CST
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `N` / `2`
- **Descrição**: Código de Situação Tributária do PIS/COFINS (CST):
 
 00 - Nenhum; 
 01 - Operação Tributável com Alíquota Básica;
 02 - Operação Tributável com Alíquota Diferenciada;
 03 - Operação Tributável com Alíquota por Unidade de Medida de Produto;
 04 - Operação Tributável monofásica - Revenda a Alíquota Zero;
 05 - Operação Tributável por Substituição Tributária;
 06 - Operação Tributável a Alíquota Zero;
 07 - Operação Isenta da Contribuição;
 08 - Operação sem Incidência da Contribuição;
 09 - Operação com Suspensão da Contribuição;
49 - Outras Operações de Saída;
50 - Operação com Direito a Crédito – Vinculada Exclusivamente a Receita Tributada no Mercado Interno;
51 - Operação com Direito a Crédito – Vinculada Exclusivamente a Receita Não-Tributada no Mercado Interno;
52 - Operação com Direito a Crédito – Vinculada Exclusivamente a Receita de Exportação;
53 - Operação com Direito a Crédito – Vinculada a Receitas Tributadas e Não-Tributadas no Mercado Interno;
54 - Operação com Direito a Crédito – Vinculada a Receitas Tributadas no Mercado Interno e de Exportação;
55 - Operação com Direito a Crédito – Vinculada a Receitas Não Tributadas no Mercado Interno e de Exportação;
56 - Operação com Direito a Crédito – Vinculada a Receitas Tributadas e Não-Tributadas no Mercado Interno e de Exportação;
60 - Crédito Presumido – Operação de Aquisição Vinculada Exclusivamente a Receita Tributada no Mercado Interno;
61 - Crédito Presumido – Operação de Aquisição Vinculada Exclusivamente a Receita Não-Tributada no Mercado Interno;
62 - Crédito Presumido – Operação de Aquisição Vinculada Exclusivamente a Receita de Exportação;
63 - Crédito Presumido – Operação de Aquisição Vinculada a Receitas Tributadas e Não-Tributadas no Mercado Interno;
64 - Crédito Presumido – Operação de Aquisição Vinculada a Receitas Tributadas no Mercado Interno e de Exportação;
65 - Crédito Presumido – Operação de Aquisição Vinculada a Receitas Não-Tributadas no Mercado Interno e de Exportação;
66 - Crédito Presumido – Operação de Aquisição Vinculada a Receitas Tributadas e Não-Tributadas no Mercado Interno e de Exportação;
67 - Crédito Presumido – Outras Operações;
70 - Operação de Aquisição sem Direito a Crédito;
71 - Operação de Aquisição com Isenção;
72 - Operação de Aquisição com Suspensão;
73 - Operação de Aquisição a Alíquota Zero;
74 - Operação de Aquisição sem Incidência da Contribuição;
75 - Operação de Aquisição por Substituição Tributária;
98 - Outras Operações de Entrada;
99 - Outras Operações;
- **Notas**:

  Informar a CST relativa ao tipo de incidência tributária da apuração própria, Cumulativa ou Não Cumulativa, de acordo com o o regime de opção.
  Estes códigos não correspondem ao da último GUIA da EFD Contribuições, ver com Orlando e Guilherme se entendem ser necessário adequar

### NFSe/infNFSe/DPS/infDPS/valores/trib/tribFed/piscofins/vBCPisCofins
- **Ocorrência**: `0-1`
- **ELE/TIPO/TAM**: `E` / `N` / `1-15V2`
- **Descrição**: Valor da Base de Cálculo do PIS/COFINS, relativo à apuração própria (R$).

### NFSe/infNFSe/DPS/infDPS/valores/trib/tribFed/piscofins/pAliqPis
- **Ocorrência**: `0-1`
- **ELE/TIPO/TAM**: `E` / `N` / `1-2V2`
- **Descrição**: Alíquota do PIS, relativa à apuração própria (%).

### NFSe/infNFSe/DPS/infDPS/valores/trib/tribFed/piscofins/pAliqCofins
- **Ocorrência**: `0-1`
- **ELE/TIPO/TAM**: `E` / `N` / `1-2V2`
- **Descrição**: Alíquota da COFINS, relativa à apuração própria (%).

### NFSe/infNFSe/DPS/infDPS/valores/trib/tribFed/piscofins/vPis
- **Ocorrência**: `0-1`
- **ELE/TIPO/TAM**: `E` / `N` / `1-15V2`
- **Descrição**: Valor do débito de PIS apuração própria (R$).

### NFSe/infNFSe/DPS/infDPS/valores/trib/tribFed/piscofins/vCofins
- **Ocorrência**: `0-1`
- **ELE/TIPO/TAM**: `E` / `N` / `1-15V2`
- **Descrição**: Valor do débito de COFINS apuração própria (R$).

### NFSe/infNFSe/DPS/infDPS/valores/trib/tribFed/piscofins/tpRetPisCofins
- **Ocorrência**: `0-1`
- **ELE/TIPO/TAM**: `E` / `N` / `1`
- **Descrição**: Tipo de retenção PIS/COFINS e CSLL:

0 - PIS/COFINS/CSLL Não Retidos;
1 - PIS/COFINS Retido;
2 - PIS/COFINS Não Retido;
3 - PIS/COFINS/CSLL Retidos;
4 - PIS/COFINS Retidos, CSLL Não Retido;
5 - PIS Retido, COFINS/CSLL Não Retido;
6 - COFINS Retido, PIS/CSLL Não Retido;
7 - PIS Não Retido, COFINS/CSLL Retidos;
8 - PIS/COFINS Não Retidos, CSLL Retido;
9 - COFINS Não Retido, PIS/CSLL Retidos;
- **Notas**:

  Indica quais contribuições retidas na fonte compoem o campo vRetCSLL.

### NFSe/infNFSe/DPS/infDPS/valores/trib/tribFed/vRetCP
- **Ocorrência**: `0-1`
- **ELE/TIPO/TAM**: `E` / `N` / `1-15V2`
- **Descrição**: Valor monetário do CP(R$).

### NFSe/infNFSe/DPS/infDPS/valores/trib/tribFed/vRetIRRF
- **Ocorrência**: `0-1`
- **ELE/TIPO/TAM**: `E` / `N` / `1-15V2`
- **Descrição**: Valor monetário do IRRF (R$).

### NFSe/infNFSe/DPS/infDPS/valores/trib/tribFed/vRetCSLL
- **Ocorrência**: `0-1`
- **ELE/TIPO/TAM**: `E` / `N` / `1-15V2`
- **Descrição**: Valor monetário do CSLL (R$).

### NFSe/infNFSe/DPS/infDPS/valores/trib/totTrib
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `G` / `-` / `-`
- **Descrição**: Grupo de informações para totais aproximados dos tributos relacionados ao serviço prestado
- **Notas**:

  Os campos totalizadores deste grupo serão reavaliados em novas versões do layout proposto.

### NFSe/infNFSe/DPS/infDPS/valores/trib/totTrib/vTotTrib
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `CG` / `-` / `-`
- **Descrição**: Valor monetário total aproximado dos tributos,
em conformidade com o artigo 1o da Lei no 12.741/2012

### NFSe/infNFSe/DPS/infDPS/valores/trib/totTrib/vTotTrib/vTotTribFed
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `N` / `1-15V2`
- **Descrição**: Valor monetário total aproximado dos tributos federais (R$).

### NFSe/infNFSe/DPS/infDPS/valores/trib/totTrib/vTotTrib/vTotTribEst
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `N` / `1-15V2`
- **Descrição**: Valor monetário total aproximado dos tributos estaduais (R$).

### NFSe/infNFSe/DPS/infDPS/valores/trib/totTrib/vTotTrib/vTotTribMun
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `N` / `1-15V2`
- **Descrição**: Valor monetário total aproximado dos tributos municipais (R$).

### NFSe/infNFSe/DPS/infDPS/valorestrib/totTrib/pTotTrib
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `CG` / `-` / `-`
- **Descrição**: Valor percentual total aproximado dos tributos,
em conformidade com o artigo 1o da Lei no 12.741/2012

### NFSe/infNFSe/DPS/infDPS/valores/trib/totTrib/pTotTrib/pTotTribFed
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `N` / `1-2V2`
- **Descrição**: Valor percentual total aproximado dos tributos federais (%).

### NFSe/infNFSe/DPS/infDPS/valores/trib/totTrib/pTotTrib/pTotTribEst
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `N` / `1-2V2`
- **Descrição**: Valor percentual total aproximado dos tributos estaduais (%).

### NFSe/infNFSe/DPS/infDPS/valores/trib/totTrib/pTotTrib/pTotTribMun
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `N` / `1-2V2`
- **Descrição**: Valor percentual total aproximado dos tributos municipais (%).

### NFSe/infNFSe/DPS/infDPS/valores/trib/totTrib/indTotTrib
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `CE` / `N` / `1`
- **Descrição**: Indicador de informação de valor total de tributos.
Se informado indica que o emitente opta por não informar nenhum valor estimado para os Tributos
(Decreto 8.264/2014).

0 - Não;

### NFSe/infNFSe/DPS/infDPS/valores/trib/totTrib/pTotTribSN
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `CE` / `N` / `1-2V2`
- **Descrição**: Valor percentual aproximado do total dos tributos da alíquota do Simples Nacional (%).

### NFSe/infNFSe/DPS/infDPS/IBSCBS
- **Ocorrência**: `0-1`
- **ELE/TIPO/TAM**: `G` / `-` / `-`
- **Descrição**: Grupo de informações declaradas pelo emitente referentes ao IBS e à CBS
- **Notas**:

  Para optantes dos Simples Nacional, os grupos IBSCBS só serão obrigatórios a partir de 2027.

### NFSe/infNFSe/DPS/infDPS/IBSCBS/finNFSe
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `N` / `1`
- **Descrição**: Indicador da finalidade da emissão de NFS-e 

0 = NFS-e regular;

### NFSe/infNFSe/DPS/infDPS/IBSCBS/indFinal
- **Ocorrência**: `0-1`
- **ELE/TIPO/TAM**: `E` / `N` / `1`
- **Descrição**: Indica operação de uso ou consumo pessoal. (art. 57)

0=Não;
1=Sim;
- **Notas**:

  Esse campo será descontinuado a partir da implantação do layout da NT 005 que ocorrerá ao longo de 2026, em data a ser divulgada no portal da NFS-e.I327

### NFSe/infNFSe/DPS/infDPS/IBSCBS/cIndOp
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `N` / `6`
- **Descrição**: Código indicador da operação de fornecimento, conforme tabela “código indicador de operação”

### NFSe/infNFSe/DPS/infDPS/IBSCBS/tpOper
- **Ocorrência**: `0-1`
- **ELE/TIPO/TAM**: `E` / `N` / `1`
- **Descrição**: Tipo de Operação com Entes Governamentais ou outros serviços sobre bens imóveis:

1 – Fornecimento com pagamento posterior;
2 - Recebimento do pagamento com fornecimento já realizado;
3 – Fornecimento com pagamento já realizado;
4 – Recebimento do pagamento com fornecimento posterior;
5 – Fornecimento e recebimento do pagamento concomitantes;
- **Notas**:

  Campo deve ser informado para as seguintes situações previstas na LC 214/2025:
  Aquisição de serviços pela administração pública direta, por autarquias e por fundações públicas: Art. 10 §2º (Qualquer serviço);
  Cessão onerosa de bem imóvel: Art. 254 III (Serviço 25.05 da LC 116/2003);
  Arrendamento de bem imóvel: Art. 254 III (Serviço 15.09 da LC 116/2003);
  Administração de bem imóvel: Art. 254 IV (Serviço 17.12 da LC 116/2003);
  Intermediação de bem imóvel: Art. 254 IV (Serviço 10.05 da LC 116/2003).

### NFSe/infNFSe/DPS/infDPS/IBSCBS/gRefNFSe
- **Ocorrência**: `0-1`
- **ELE/TIPO/TAM**: `G` / `-` / `-`
- **Descrição**: Grupo de NFS-e referenciadas.
- **Notas**:

  Obrigatório para tpOper = 2 ou 3

### NFSe/infNFSe/DPS/infDPS/IBSCBS/gRefNFSe/refNFSe
- **Ocorrência**: `1-99`
- **ELE/TIPO/TAM**: `E` / `C` / `50`
- **Descrição**: Chave da NFS-e referenciada.

### NFSe/infNFSe/DPS/infDPS/IBSCBS/tpEnteGov
- **Ocorrência**: `0-1`
- **ELE/TIPO/TAM**: `E` / `N` / `1`
- **Descrição**: Tipo de ente governamental

Para administração pública direta e suas autarquias e fundações: 
1 = União;
2 = Estado;
3 = Distrito Federal; 
4 = Município;
- **Notas**:

  Campo só deve ser informado no caso de compras governamentais.

### NFSe/infNFSe/DPS/infDPS/IBSCBS/indDest
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `N` / `1`
- **Descrição**: A respeito do Destinatário dos serviços:

0 – o destinatário é o próprio tomador/adquirente identificado na NFS-e (tomador=adquirente=destinatário);
1 – o destinatário não é o próprio adquirente, podendo ser outra pessoa, física ou jurídica (ou equiparada), ou um estabelecimento diferente do indicado como tomador (tomador=adquirente≠destinatário);

### NFSe/infNFSe/DPS/infDPS/IBSCBS/dest
- **Ocorrência**: `0-1`
- **ELE/TIPO/TAM**: `G` / `-` / `-`
- **Descrição**: Grupo de informações relativas ao Destinatário

### NFSe/infNFSe/DPS/infDPS/IBSCBS/dest/CNPJ
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `CE` / `N` / `14`
- **Descrição**: Número da inscrição no Cadastro Nacional de Pessoa Jurídica (CNPJ) do destinatário de serviço

### NFSe/infNFSe/DPS/infDPS/IBSCBS/dest/CPF
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `CE` / `N` / `11`
- **Descrição**: Número da inscrição no Cadastro Nacional de Pessoa Física (CPF) do destinatário do serviço

### NFSe/infNFSe/DPS/infDPS/IBSCBS/dest/NIF
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `CE` / `C` / `40`
- **Descrição**: Número de identificação fiscal fornecido por órgão de administração tributária no exterior

### NFSe/infNFSe/DPS/infDPS/IBSCBS/dest/cNaoNIF
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `CE` / `N` / `1`
- **Descrição**: Motivo para não informação do NIF:
 
 0 - Não informado na nota de origem;
 1 - Dispensado do NIF;
 2 - Não exigência do NIF;

### NFSe/infNFSe/DPS/infDPS/IBSCBS/dest/xNome
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `C` / `1-150`
- **Descrição**: Nome / Nome Empresarial do destinatário

### NFSe/infNFSe/DPS/infDPS/IBSCBS/dest/end
- **Ocorrência**: `0-1`
- **ELE/TIPO/TAM**: `G` / `-` / `-`
- **Descrição**: Grupo de informações do endereço do destinatário do serviço.

### NFSe/infNFSe/DPS/infDPS/IBSCBS/dest/end/endNac
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `CG` / `-` / `-`
- **Descrição**: Grupo de informações do endereço nacional.

### NFSe/infNFSe/DPS/infDPS/IBSCBS/dest/end/endNac/cMun
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `N` / `7`
- **Descrição**: Código do município do endereço do destinatário do serviço.
  (Tabela do IBGE)

### NFSe/infNFSe/DPS/infDPS/IBSCBS/dest/end/endNac/CEP
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `N` / `8`
- **Descrição**: Código numérico do Endereçamento Postal nacional (CEP) do endereço do destinatário do serviço.
(Informar os zeros não significativos)

### NFSe/infNFSe/DPS/infDPS/IBSCBS/dest/end/endExt
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `CG` / `-` / `-`
- **Descrição**: Grupo de informações do endereço no exterior.

### NFSe/infNFSe/DPS/infDPS/IBSCBS/dest/end/endExt/cPais
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `C` / `2`
- **Descrição**: Código do país do endereço do destinatário do serviço.
  (Tabela de Países ISO)

### NFSe/infNFSe/DPS/infDPS/IBSCBS/dest/end/endExt/cEndPost
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `C` / `1-11`
- **Descrição**: Código alfanumérico do Endereçamento Postal no exterior do destinatário do serviço.

### NFSe/infNFSe/DPS/infDPS/IBSCBS/dest/end/endExt/xCidade
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `C` / `1-60`
- **Descrição**: Nome da cidade no exterior do destinatário do serviço.

### NFSe/infNFSe/DPS/infDPS/IBSCBS/dest/end/endExt/xEstProvReg
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `C` / `1-60`
- **Descrição**: Estado, província ou região da cidade no exterior do destinatário do serviço.

### NFSe/infNFSe/DPS/infDPS/IBSCBS/dest/end/xLgr
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `C` / `1-255`
- **Descrição**: Tipo e nome do logradouro do endereço do destinatário do serviço.

### NFSe/infNFSe/DPS/infDPS/IBSCBS/dest/end/nro
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `C` / `1-60`
- **Descrição**: Número no logradouro do endereço do destinatário do serviço.

### NFSe/infNFSe/DPS/infDPS/IBSCBS/dest/end/xCpl
- **Ocorrência**: `0-1`
- **ELE/TIPO/TAM**: `E` / `C` / `1-156`
- **Descrição**: Complemento do endereço do destinatário do serviço.

### NFSe/infNFSe/DPS/infDPS/IBSCBS/dest/end/xBairro
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `C` / `1-60`
- **Descrição**: Bairro do endereço do destinatário do serviço.

### NFSe/infNFSe/DPS/infDPS/IBSCBS/dest/fone
- **Ocorrência**: `0-1`
- **ELE/TIPO/TAM**: `E` / `N` / `6-20`
- **Descrição**: Número do telefone do destinatário.
 (Preencher com o Código DDD + número do telefone.  Nas operações com exterior é permitido informar o código do país + código da localidade + número do telefone)

### NFSe/infNFSe/DPS/infDPS/IBSCBS/dest/email
- **Ocorrência**: `0-1`
- **ELE/TIPO/TAM**: `E` / `C` / `1-80`
- **Descrição**: E-mail do destinatário.

### NFSe/infNFSe/DPS/infDPS/IBSCBS/imovel
- **Ocorrência**: `0-1`
- **ELE/TIPO/TAM**: `G` / `-` / `-`
- **Descrição**: Grupo de informações de operações relacionadas a bens imóveis, exceto obras.

### NFSe/infNFSe/DPS/infDPS/IBSCBS/imovel/inscImobFisc
- **Ocorrência**: `0-1`
- **ELE/TIPO/TAM**: `E` / `C` / `1-30`
- **Descrição**: Inscrição imobiliária fiscal 
 (código fornecido pela prefeitura para a identificação da obra ou para fins de recolhimento do IPTU)

### NFSe/infNFSe/DPS/infDPS/IBSCBS/imovel/cCIB
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `CE` / `C` / `8`
- **Descrição**: Código do Cadastro Imobiliário Brasileiro - CIB

### NFSe/infNFSe/DPS/infDPS/IBSCBS/imovel/end
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `CG` / `-` / `-`
- **Descrição**: Grupo de informações do endereço do imóvel.

### NFSe/infNFSe/DPS/infDPS/IBSCBS/imovel/end/CEP
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `CE` / `N` / `8`
- **Descrição**: Código numérico do Endereçamento Postal nacional (CEP) do endereço do imóvel.
(Informar os zeros não significativos)

### NFSe/infNFSe/DPS/infDPS/IBSCBS/imovel/end/endExt
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `CG` / `-` / `-`
- **Descrição**: Grupo de informações descritivas do endereço do imóvel no exterior.

### NFSe/infNFSe/DPS/infDPS/IBSCBS/imovel/end/endExt/cEndPost
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `C` / `1-11`
- **Descrição**: Código de Endereçamento Postal alfanumérico do endereço do imóvel no exterior.

### NFSe/infNFSe/DPS/infDPS/IBSCBS/imovel/end/endExt/xCidade
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `C` / `1-60`
- **Descrição**: Nome da cidade no exterior, local do imóvel.

### NFSe/infNFSe/DPS/infDPS/IBSCBS/imovel/end/endExt/xEstProvReg
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `C` / `1-60`
- **Descrição**: Estado, província ou região da cidade no exterior, local do imóvel.

### NFSe/infNFSe/DPS/infDPS/IBSCBS/imovel/end/xLgr
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `C` / `1-255`
- **Descrição**: Tipo e nome do logradouro do endereço do imóvel.

### NFSe/infNFSe/DPS/infDPS/IBSCBS/imovel/end/nro
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `C` / `1-60`
- **Descrição**: Número no logradouro do endereço do imóvel.

### NFSe/infNFSe/DPS/infDPS/IBSCBS/imovel/end/xCpl
- **Ocorrência**: `0-1`
- **ELE/TIPO/TAM**: `E` / `C` / `1-156`
- **Descrição**: Complemento do endereço do imóvel.

### NFSe/infNFSe/DPS/infDPS/IBSCBS/imovel/end/xBairro
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `C` / `1-60`
- **Descrição**: Bairro do endereço do imóvel.

### NFSe/infNFSe/DPS/infDPS/IBSCBS/valores
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `G` / `-` / `-`
- **Descrição**: Grupo de informações relativas aos valores do serviço prestado para IBS e CBS

### NFSe/infNFSe/DPS/infDPS/IBSCBS/valores/gReeRepRes
- **Ocorrência**: `0-1`
- **ELE/TIPO/TAM**: `G` / `-` / `-`
- **Descrição**: Grupo de informações relativas a valores incluídos neste documento e recebidos por motivo de estarem relacionadas a operações de terceiros, objeto de reembolso, repasse ou ressarcimento pelo recebedor, já tributados e aqui referenciados

### NFSe/infNFSe/DPS/infDPS/IBSCBS/valores/gReeRepRes/documentos
- **Ocorrência**: `1-1000`
- **ELE/TIPO/TAM**: `G` / `-` / `-`
- **Descrição**: Grupo relativo aos documentos referenciados nos casos de reembolso, repasse e ressarcimento que serão considerados na base de cálculo do ISSQN, do IBS e da CBS.

### NFSe/infNFSe/DPS/infDPS/IBSCBS/valores/gReeRepRes/documentos/dFeNacional
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `CG` / `-` / `-`
- **Descrição**: Grupo de informações de documentos fiscais eletrônicos que se encontram no repositório nacional.

### NFSe/infNFSe/DPS/infDPS/IBSCBS/valores/gReeRepRes/documentos/dFeNacional/tipoChaveDFe
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `N` / `1`
- **Descrição**: Documento fiscal a que se refere a chaveDfe que seja um dos documentos do Repositório Nacional:
1 = NFS-e;
2 = NF-e;
3 = CT-e;
9 = Outro;

### NFSe/infNFSe/DPS/infDPS/IBSCBS/valores/gReeRepRes/documentos/dFeNacional/xTipoChaveDFe
- **Ocorrência**: `0-1`
- **ELE/TIPO/TAM**: `E` / `C` / `1-255`
- **Descrição**: Descrição da DF-e a que se refere a chaveDfe que seja um dos documentos do Repositório Nacional. Deve ser preenchido apenas quando tipoChaveDFe = 9 (Outro).

### NFSe/infNFSe/DPS/infDPS/IBSCBS/valores/gReeRepRes/documentos/dFeNacional/chaveDFe
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `C` / `1-50`
- **Descrição**: Chave do Documento Fiscal eletrônico do repositório nacional referenciado para os casos de operações já tributadas.

### NFSe/infNFSe/DPS/infDPS/IBSCBS/valores/gReeRepRes/documentos/docFiscalOutro
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `CG` / `-` / `-`
- **Descrição**: Grupo de informações de documento fiscais, eletrônicos ou não, que não se encontram no repositório nacional.

### NFSe/infNFSe/DPS/infDPS/IBSCBS/valores/gReeRepRes/documentos/docFiscalOutro/cMunDocFiscal
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `N` / `7`
- **Descrição**: Código do município emissor do documento fiscal que não se encontra no repositório nacional

### NFSe/infNFSe/DPS/infDPS/IBSCBS/valores/gReeRepRes/documentos/docFiscalOutro/nDocFiscal
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `C` / `1-255`
- **Descrição**: Número do documento fiscal que não se encontra no repositório nacional

### NFSe/infNFSe/DPS/infDPS/IBSCBS/valores/gReeRepRes/documentos/docFiscalOutro/xDocFiscal
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `C` / `1-255`
- **Descrição**: Descrição do documento fiscal

### NFSe/infNFSe/DPS/infDPS/IBSCBS/valores/gReeRepRes/documentos/docOutro
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `CG` / `-` / `-`
- **Descrição**: Grupo de informações de documento não fiscal.

### NFSe/infNFSe/DPS/infDPS/IBSCBS/valores/gReeRepRes/documentos/docOutro/nDoc
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `C` / `1-255`
- **Descrição**: Número do documento não fiscal.

### NFSe/infNFSe/DPS/infDPS/IBSCBS/valores/gReeRepRes/documentos/docOutro/xDoc
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `C` / `1-255`
- **Descrição**: Descrição do documento não fiscal.

### NFSe/infNFSe/DPS/infDPS/IBSCBS/valores/gReeRepRes/documentos/fornec
- **Ocorrência**: `0-1`
- **ELE/TIPO/TAM**: `G` / `-` / `-`
- **Descrição**: Grupo de informações do fornecedor do documento referenciado

### NFSe/infNFSe/DPS/infDPS/IBSCBS/valores/gReeRepRes/documentos/fornec/CNPJ
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `CE` / `N` / `14`
- **Descrição**: Número da inscrição federal (CNPJ) do fornecedor.

### NFSe/infNFSe/DPS/infDPS/IBSCBS/valores/gReeRepRes/documentos/fornec/CPF
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `CE` / `N` / `11`
- **Descrição**: Número da inscrição federal (CPF) do fornecedor.

### NFSe/infNFSe/DPS/infDPS/IBSCBS/valores/gReeRepRes/documentos/fornec/NIF
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `CE` / `C` / `40`
- **Descrição**: Este elemento só deverá ser preenchido para fornecedores não residentes no Brasil.

### NFSe/infNFSe/DPS/infDPS/IBSCBS/valores/gReeRepRes/documentos/fornec/cNaoNIF
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `CE` / `N` / `1`
- **Descrição**: Motivo para não informação do NIF:

0 - Não informado na nota de origem;
1 - Dispensado do NIF;
2 - Não exigência do NIF;
- **Notas**:

  O valor 0 deve ser utilizado como opção de preenchimento do campo somente no compartilhamento de NFS-e pelo municipio com o ADN NFS-e.

### NFSe/infNFSe/DPS/infDPS/IBSCBS/valores/gReeRepRes/documentos/fornec/xNome
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `C` / `1-150`
- **Descrição**: Nome / Razão Social do fornecedor.

### NFSe/infNFSe/DPS/infDPS/IBSCBS/valores/gReeRepRes/documentos/dtEmiDoc
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `D` / `-`
- **Descrição**: Data da emissão do documento dedutível.
Ano, mês e dia (AAAA-MM-DD)

### NFSe/infNFSe/DPS/infDPS/IBSCBS/valores/gReeRepRes/documentos/dtCompDoc
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `D` / `-`
- **Descrição**: Data da competência do documento dedutível.
Ano, mês e dia (AAAA-MM-DD)

### NFSe/infNFSe/DPS/infDPS/IBSCBS/valores/gReeRepRes/documentos/tpReeRepRes
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `N` / `2`
- **Descrição**: Tipo de valor incluído neste documento, recebido por motivo de estarem relacionadas a operações de terceiros, objeto de reembolso, repasse ou ressarcimento pelo recebedor, já tributados e aqui referenciados

  01 = Repasse de remuneração por intermediação de imóveis a demais corretores 
           envolvidos na operação
  02 = Repasse de valores a fornecedor relativo a fornecimento intermediado por 
           agência de turismo
  03 = Reembolso ou ressarcimento recebido por agência de propaganda e 
           publicidade por valores pagos relativos a serviços de produção externa por conta                           
           e ordem de terceiro
  04 = Reembolso ou ressarcimento recebido por agência de propaganda e 
           publicidade por valores pagos relativos a serviços de mídia por conta                           
           e ordem de terceiro
  99 = Outros reembolsos ou ressarcimentos recebidos por valores pagos relativos a 
           operações por conta e ordem de terceiro

### NFSe/infNFSe/DPS/infDPS/IBSCBS/valores/gReeRepRes/documentos/xTpReeRepRes
- **Ocorrência**: `0-1`
- **ELE/TIPO/TAM**: `E` / `C` / `0-150`
- **Descrição**: Descrição do reembolso ou ressarcimento quando a opção é "99 – Outros reembolsos ou ressarcimentos recebidos por valores pagos relativos a operações por conta e ordem de terceiro".

### NFSe/infNFSe/DPS/infDPS/IBSCBS/valores/gReeRepRes/documentos/vlrReeRepRes
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `N` / `1-15V2`
- **Descrição**: Valor monetário (total ou parcial, conforme documento informado) utilizado para não inclusão na base de cálculo do ISS e do IBS e da CBS da NFS-e que está sendo emitida (R$).

### NFSe/infNFSe/DPS/infDPS/IBSCBS/valores/trib
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `G` / `-` / `-`
- **Descrição**: Grupo de informações relacionados aos tributos IBS e CBS

### NFSe/infNFSe/DPS/infDPS/IBSCBS/valores/trib/gIBSCBS
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `G` / `-` / `-`
- **Descrição**: Grupo de informações relacionadas ao IBS e à CBS

### NFSe/infNFSe/DPS/infDPS/IBSCBS/valores/trib/gIBSCBS/CST
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `N` / `3`
- **Descrição**: Código de Situação Tributária do 
IBS e da CBS

### NFSe/infNFSe/DPS/infDPS/IBSCBS/valores/trib/gIBSCBS/cClassTrib
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `N` / `6`
- **Descrição**: Código de Classificação Tributária 
do IBS e da CBS

### NFSe/infNFSe/DPS/infDPS/IBSCBS/valores/trib/gIBSCBS/cCredPres
- **Ocorrência**: `0-1`
- **ELE/TIPO/TAM**: `E` / `N` / `2`
- **Descrição**: Código e classificação do crédito presumido: IBS e CBS.

### NFSe/infNFSe/DPS/infDPS/IBSCBS/valores/trib/gIBSCBS/gTribRegular
- **Ocorrência**: `0-1`
- **ELE/TIPO/TAM**: `G` / `-` / `-`
- **Descrição**: Grupo de informações da Tributação Regular

### NFSe/infNFSe/DPS/infDPS/IBSCBS/valores/trib/gIBSCBS/gTribRegular/CSTReg
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `N` / `3`
- **Descrição**: Código de Situação Tributária do 
IBS e da CBS de tributação regular

### NFSe/infNFSe/DPS/infDPS/IBSCBS/valores/trib/gIBSCBS/gTribRegular/cClassTribReg
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `N` / `6`
- **Descrição**: Código da Classificação Tributária do 
IBS e da CBS de tributação regular

### NFSe/infNFSe/DPS/infDPS/IBSCBS/valores/trib/gIBSCBS/gDif
- **Ocorrência**: `0-1`
- **ELE/TIPO/TAM**: `G` / `-` / `-`
- **Descrição**: Grupo de informações relacionadas ao diferimento para IBS e CBS

### NFSe/infNFSe/DPS/infDPS/IBSCBS/valores/trib/gIBSCBS/gDif/pDifUF
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `N` / `1-3V2`
- **Descrição**: Percentual de diferimento para o IBS estadual.

### NFSe/infNFSe/DPS/infDPS/IBSCBS/valores/trib/gIBSCBS/gDif/pDifMun
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `N` / `1-3V2`
- **Descrição**: Percentual de diferimento para o IBS municipal.

### NFSe/infNFSe/DPS/infDPS/IBSCBS/valores/trib/gIBSCBS/gDif/pDifCBS
- **Ocorrência**: `1-1`
- **ELE/TIPO/TAM**: `E` / `N` / `1-3V2`
- **Descrição**: Percentual de diferimento para a CBS.

### NFSe/infNFSe/DPS/infDPS/Signature
- **Ocorrência**: `0-1`
- **ELE/TIPO/TAM**: `G` / `-` / `-`
- **Descrição**: Assinatura XML da NFS-e Segundo o Padrão XML Digital Signature
Obrigatório quando for enviado para API.
Demais casos poderão ser opcionais a serem definidos em regra de validação.

