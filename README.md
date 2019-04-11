# Caronas Bot

## Sobre 

Bot para gerenciar caronas usado no [Telegram].

### Table of Contents

<!-- START doctoc generated TOC please keep comment here to allow auto update -->
<!-- DON'T EDIT THIS SECTION, INSTEAD RE-RUN doctoc TO UPDATE -->
**Table of Contents**  *generated with [DocToc](https://github.com/thlorenz/doctoc)*

- [Caronas Bot](#caronas-bot)
  - [Sobre](#sobre)
    - [Version](#version)
    - [Tecnologias](#tecnologias)
    - [Desenvolvimento](#desenvolvimento)
    - [Devs](#devs)
  - [Getting Started](#getting-started)
    - [Criação de um Bot]
    - [Configuração do servidor PHP no [Heroku]]
      - [Criação de conta no [Heroku]]
      - [Criação do Banco de Dados](#cria%C3%A7%C3%A3o-do-banco-de-dados)
      - [Configuração do Banco de Dados](#configura%C3%A7%C3%A3o-do-banco-de-dados)
      - [Criação de aplicação](#cria%C3%A7%C3%A3o-de-aplica%C3%A7%C3%A3o)
      - [Configuração das variáveis de ambiente](#configura%C3%A7%C3%A3o-das-vari%C3%A1veis-de-ambiente)
      - [Deploy](#deploy)
      - [Configuração de webhooks](#configura%C3%A7%C3%A3o-de-webhooks)
    - [Configuração de comandos do Bot](#configura%C3%A7%C3%A3o-de-comandos-do-bot)
    - [Comece a usar](#comece-a-usar)
  - [License](#license)

<!-- END doctoc generated TOC please keep comment here to allow auto update -->

### Funcionalidades:
  - Adicionar/Remover caronas com horário, número de vagas e origem/destino
  - Exibir lista com as caronas de ida e volta
  - Informar uso de aplicativos externos (PicPay e Wunder)

### Version
1.3.0

### Tecnologias

Todo o código foi escrito em PHP integrando com a Bot API do [Telegram].

### Desenvolvimento

Alterações feitas a partir do código original em https://github.com/henriquemaio/CaronasBot. Novas funcionalidades como o número de vagas disponíveis e o trajeto, informação de forma de pagamento e outras condições que foram atendidas conforme o cotidiano do grupo de caronas no qual participo.
Adicione uma issue ou faça um pull request.

### Devs

 - [henriquemaio]
 - [leogoncalves]
 - [VBustamante]
 - [doravante]
 - [filipebarretto]
 - [waltersfilho]


## Getting Started

Tutorial de como utilizar esse código para implementar um novo bot no [Telegram] com configurações próprias.

### Criação de um Bot

Para criar um bot, você deve iniciar uma conversa com o [BotFather]. Para conhecer os comandos, utilize o comando:
```
/help
```

Para criar um novo Bot, utilize o comando:
```
/newbot
```

O BotFather vai solicitar que digite um nome e um username para o seu Bot. Ao finalizar, será retornado um token para utilizar a API.

<img src="https://dl.dropboxusercontent.com/u/33812048/telegram-create-bot-tutorial.png" width=“48”>


### Configuração do servidor PHP no [Heroku]

####  Criação de conta no [Heroku]

Caso não possua conta, cria uma conta gratuita em https://signup.heroku.com/

#### Criação do Banco de Dados

No canto superior esquerdo, ao lado de **Dashboard**, clique no botão de menu e selecione **Databases**. Na dela de Databases, clique em **Create Database**. Escolha a opção **Other plans: Dev Plan (Free)** e em **Add Database**.

Quando o Banco estiver **Available**, clique em seu nome. Nessa tela, guarde as informações de **host**, **database**, **password** e **user**.

#### Configuração do Banco de Dados

Para acessar o Banco de Dados, você deve ter o [Postgres] instalado localmente.

No terminal, execute o comando, onde [db_name] é o nome e o [db_type] é um tipo definido pelo Heroku do Banco de Dados recém criado. O comando de cada base de dados pode ser visto em Psql dentro de suas **Connections Settings**:
```
heroku pg:psql --app [db_name] [db_type]
```

Ao logar, copie o comando em database/script.sql e execute:
```
=> create table Caroneiros(
	id bigserial UNIQUE PRIMARY KEY,
	chat_id varchar(255) NOT NULL,
	user_id int NOT NULL,
	username varchar(128),
	spots varchar(128),
	location varchar(128),
	travel_hour timestamp,
	route bit not null,
	expired bit DEFAULT 0::bit;
);

=> create table caroneiro_pagamento(
	id bigserial UNIQUE PRIMARY KEY,
	chat_id varchar(255) NOT NULL,
	user_id int NOT NULL,
	picpay bit DEFAULT 0::bit,
	wunder bit DEFAULT 0::bit
);
```

#### Criação de aplicação

No dashboard, clique no sinal de + no canto superior direito para criar uma nova aplicação.

Digite o nome de sua escolha e clique em criar.

#### Configuração das variáveis de ambiente

Acesse **Settings** e clique em **Reveal Config Vars**. Adicione:

 - KEY: API_KEY_TELEGRAM, VALUE: [token criado pelo BotFather]
 - KEY: BOT_NAME, VALUE: [nome definido para o BotFather]
 - KEY: DB_HOST, VALUE: [**host**]
 - KEY: DB_NAME, VALUE: [**database**]
 - KEY: DB_PASS, VALUE: [**password**]
 - KEY: DB_USER, VALUE: [**user**]
 - KEY: SOURCE, VALUE: Local das caronas (Ex: Fundão)

#### Deploy

No menu superior, selecione **Deploy**. Selecione **GitHub** como Deployment Method e conecte ao repositório. Selecione o branch master e faça o deploy.

#### Configuração de webhooks

Acesse no navegador a url: https://api.telegram.org/bot[token]/setwebhook?url=[api]
Onde [token] é o token fornecido pelo BotFather e [api] é o domínio da aplicação no Heroku. Você deve visualizar algo do tipo:
```
{
ok: true,
result: true,
description: "Webhook was set"
}
```

### Configuração de comandos do Bot

Abra novamente a conversa com o BotFather e execute o comando:
```
/setprivacy
```

Escolha o Bot e insira a opção ‘DISABLE’.

Depois, execute o comando:

```
/setcommands
```

Selecione o Bot desejado e envie a lista:
```
teste - Teste da Aplicação
help - Manual de como utilizar o Bot
ida - Cadastrar nova ida, atualizar ida ou ver idas existentes
volta - Cadastrar nova volta, atualizar volta ou ver idas existentes
remover - Remover ida ou volta
regras - Visualizar regras de uso do grupo
vagas - Atualiza o numero de vagas
caronas - Ver idas e voltas em uma única mensagem
picpay - Informe se aceita ou não PicPay
wunder - Informe se aceita ou não Wunder
```

### Comece a usar

Adicione o Bot no grupo e comece a usar

License
----

MIT


**Free Software, Hell Yeah!**

[//]: # (These are reference links used in the body of this note and get stripped out when the markdown processor does its job. There is no need to format nicely because it shouldn't be seen. Thanks SO - http://stackoverflow.com/questions/4823468/store-comments-in-markdown-syntax)

   [Telegram]: <https://telegram.org/>
   [henriquemaio]: <https://github.com/henriquemaio>
   [leogoncalves]: <https://github.com/leogoncalves>
   [VBustamante]: <https://github.com/VBustamante>
   [doravante]: <https://github.com/doravante>
   [filipebarretto]: <https://github.com/filipebarretto>
   [waltersfilho]: <https://github.com/waltersfilho>
   [BotFather]: <https://telegram.me/botfather>
   [Heroku]: <https://heroku.com>
   [Postgres]: <http://www.postgresql.org/download/>


