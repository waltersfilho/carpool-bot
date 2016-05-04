# Caronas Bot

## Sobre 

Bot para gerenciar caronas usado no [Telegram].

Funcionalidades:
  - Adicionar/Remover caronas
  - Exibir lista com as caronas

### Version
1.1

### Tech

Todo o código foi escrito em PHP.

### Desenvolvimento

Alterações feitas a partir do código original em https://github.com/henriquemaio/CaronasBot. Novas funcionalidades como o número de vagas disponíveis e o trajeto.
Adicione uma issue ou faça um pull request.

### Devs

 - [henriquemaio]
 - [leogoncalves]
 - [VBustamante]
 - [doravante]
 - [filipebarretto]


## Getting Started

Tutorial de como utilizar esse código para implementar um novo bot no [Telegram] com configurações próprias.

### 1 Criação de um Bot

Para criar um bot, você deve iniciar uma conversa com o [BotFather]. Para conhecer os comandos, utilize o comando:
```
/help
```

Para criar um novo Bot, utilize o comando:
```
/newbot
```

O BotFather vai solicitar que digite um nome e um username para o seu Bot. Ao finalizar, será retornado um token para utilizar a API.

### 2 Configuração do servidor PHP no [Heroku]

#### 2.1 Criação de conta no [Heroku]

Caso não possua conta, cria uma conta gratuita em https://signup.heroku.com/

#### 2.2 Criação do Banco de Dados

No canto superior esquerdo, ao lado de **Dashboard**, clique no botão de menu e selecione **Databases**. Na dela de Databases, clique em **Create Database**. Escolha a opção **Other plans: Dev Plan (Free)** e em **Add Database**.

Quando o Banco estiver **Available**, clique em seu nome. Nessa tela, guarde as informações de **host**, **database**, **password** e **user**.

#### 2.3 Configuração do Banco de Dados

Para acessar o Banco de Dados, você deve ter o [Postgres] instalado localmente.

No terminal, execute o comando, onde [db_name] é o nome e o [db_type] é um tipo definido pelo Heroku do Banco de Dados recém criado. O comando de cada base de dados pode ser visto em Psql dentro de suas **Connections Settings**:
```
heroku pg:psql --app [db_name] [db_type]
```

Ao logar, copie os comandos em database/script.sql e execute:
```
=> create table Caroneiros(
        id bigserial UNIQUE PRIMARY KEY,
        chat_id int NOT NULL,
        user_id int NOT NULL,
        username varchar(128),
        spots varchar(128),
        location varchar(128),
        travel_hour time,
        route bit not null
);
```

#### 2.4 Criação de aplicação

No dashboard, clique no sinal de + no canto superior direito para criar uma nova aplicação.

Digite o nome de sua escolha e clique em criar.

#### 2.5 Configuração das variáveis de ambiente

Acesse **Settings** e clique em **Reveal Config Vars**. Adicione:

 - KEY: API_KEY_TELEGRAM, VALUE: [token criado pelo BotFather]
 - KEY: BOT_NAME, VALUE: [nome definido para o BotFather]
 - KEY: DB_HOST, VALUE: [**host**]
 - KEY: DB_NAME, VALUE: [**database**]
 - KEY: DB_PASS, VALUE: [**password**]
 - KEY: DB_USER, VALUE: [**user**]

#### 2.6 Deploy

No menu superior, selecione **Deploy**. Selecione **GitHub** como Deployment Method e conecte ao repositório. Selecione o branch master e faça o deploy.

#### 2.7 Configuração de webhooks

Acesse no navegador a url: https://api.telegram.org/bot[token]/setwebhook?url=[api]
Onde [token] é o token fornecido pelo BotFather e [api] é o domínio da aplicação no Heroku. Você deve visualizar algo do tipo:
```
{
ok: true,
result: true,
description: "Webhook was set"
}
```

### 3 Configuração de comandos do Bot

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
ida - Cadastrar nova ida ou ver idas existentes
volta - Cadastrar nova volta ou ver idas existentes
remover - Remover ida ou volta
regras - Visualizar regras de uso do grupo
```

### 4 Comece a usar

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
   [BotFather]: <https://telegram.me/botfather>
   [Heroku]: <https://heroku.com>
   [Postgres]: <http://www.postgresql.org/download/>


