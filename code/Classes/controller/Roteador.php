<?php
require_once (__DIR__."/../config/Config.php");
require_once (__DIR__."/../config/TelegramConnect.php");
require_once (__DIR__."/../dao/CaronaDAO.php");
require_once (__DIR__."/../model/Carona.php");
require_once (__DIR__."/../util/PontoReferenciaMap.php");

class Roteador
{
    private $pontoReferenciaMap;

    /*Espera o objeto 'message' já como array*/
    private static function processData($data)
    {
        $processedData = array();

        /*TODO inicializar objeto telegramConnect com dados da mensagem*/
        $processedData['username'] = $data["message"]["from"]["username"];
        $processedData['chatId'] = $data["message"]["chat"]["id"];
        $processedData['userId'] = $data["message"]["from"]["id"];

        error_log(print_r($processedData, true));

        return $processedData;
    }

    private static function processCommand($stringComando, &$args)
    {
        /* Trata uma string que começa com '/', seguido por no maximo 32 numeros, letras ou '_', seguido ou não de '@nomeDoBot */
        $regexComando = '~^/(?P<comando>[\d\w_]{1,32})(?:@' . Config::getBotConfig('botName') . ')?~';
        $command = NULL;
        $args = NULL;

        if (preg_match($regexComando, $stringComando, $match)) {
            $command = $match['comando'];
            $stringComando = str_replace($match[0], "", $stringComando);

            if($command === 'aviso') {
                $args = $stringComando;
                return $command;
            }

            $args = explode(" ", $stringComando);

            if (count($args) == 5) {
                $args[3] = $args[3] . " " . $args[4];
            }
            unset($args[4]);
            error_log($args);
        }

        error_log(print_r($command, true));
        error_log(print_r($args, true));
        error_log(strlen($args[1]));
        return $command;
    }

    public static function direcionar($request)
    {
        $args = array();
        $command = self::processCommand($request['message']['text'], $args);
        $dados = self::processData($request);
        $diasemana = array('Domingo', 'Segunda-Feira', 'Terça-Feira', 'Quarta-Feira', 'Quinta-Feira', 'Sexta-Feira', 'Sábado');
        $chat_id = $dados["chatId"];
        $chatInformations = $request['message']['chat'];
        $user_id = $dados["userId"];
        $username = $dados['username'];
        $timezone = new DateTimeZone("America/Bahia");

        $dataHoje = new DateTime('NOW', $timezone);
        $dataAmanha = new DateTime('NOW', $timezone);
        $dataAmanha->modify('+1 day');

        $dataHojeDia = $diasemana[$dataHoje->format('N')] . " - " . $dataHoje->format('d/m');
        $dataAmanhaDia = $diasemana[$dataAmanha->format('N')] . " - " . $dataAmanha->format('d/m');

        /*Dividir cada comando em seu controlador*/
        if ($username) {
            $dao = new CaronaDAO();
            $pontoReferenciaMap = new PontoReferenciaMap();

            switch (strtolower($command)) {
                /*comandos padrão*/
                case 'regras':
                    $regras = "	 BEM VINDOS AO GRUPO DE " . strtoupper($chatInformations['title']) . "

								 REGRAS

								- Para oferecer carona basta usar os comandos de nosso bot, caso tenha dúvida, use o comando /help.
								 Local de encontro, trajeto e outras informações serão combinados preferincialmente no PRIVADO.

								- É obrigatório o envio de algum documento que comprove o vínculo com a UFRJ para algum adm. 

								- Evitar assuntos não relacionados às caronas no grupo, a menos que considere de interesse público.

								- De forma a evitar eventuais transtornos, zele sempre pela integridade e segurança de ambas as partes. Atitudes que possam causar eventuais prejuízos a terceiros serão passíveis de remoção do grupo. Exemplos: direção ofensiva, crimes de trânsito, porte de drogas, documentação do veículo em dia, etc.

								- O não cumprimento das regras poderá acarretar na remoção do grupo.

								- Divulgue o grupo somente entre sua rede de pessoas conhecidas. Para adicionar novos integrantes, envie msg no privado para um dos adms.

								- Valor da carona: R$6,00.";

                    TelegramConnect::sendMessage($chat_id, $regras);
                    break;

                case 'help':
                    $help = "Utilize este Bot para agendar as caronas. A utilização é super simples e através de comandos:
								/caronas --> Este comando lista as caronas tanto de ida, quanto de volta do Fundão
								
								/ida [horario] [vagas] [local] --> Este comando serve para definir um horário que você está INDO para o FUNDÃO.
									Ex: /ida 10:00 2 merck
									(Inclui uma carona de ida às 10:00 com 2 vagas saindo do merck)

								Caso não seja colocado o parâmetro do horário (Ex: /ida) o bot irá apresentar a lista com as caronas registradas para o trajeto.

								/volta [horario] [vagas] [local] --> Este comando serve para definir um horário que você está VOLTANDO para o SEU BAIRRO. 
									Ex: /volta 15:00 3 merck 
									(Inclui uma carona de volta às 15:00 com 3 vagas para a merck)
								
								Caso não seja colocado o parâmetro do horário (Ex: /volta) o bot irá apresentar a lista com as caronas registradas para o trajeto.

								OBS --> Para o local utilize sempre letras minúsculas e composto por no MÁXIMO duas palavras. Para mais de um local siga o padrão : local01/local02 
									Ex: gramado/macembu/mananciais/guerenguê/rodrigues caldas

								/remover [ida|volta] --> Comando utilizado para remover a carona da lista. SEMPRE REMOVA a carona depois dela ter sido realizada. 
									Ex: /remover ida

								/vagas [ida|volta] [vagas] --> Este comando serve para atualizar o número de vagas de uma carona
									Ex: /vagas ida 2 
									(Altera o número de vagas da ida para 2)
									Ex: /vagas ida 0
									(Altera o número de vagas da ida para 0, ou seja, lotado)
								/picpay --> Este comando informa que você aceita PicPay, de forma permanente. Para passar a não aceitar, chame o comando novamente.
									Ex: Por padrão, todos os caroneiros não aceitam PicPay. Chamando o comando pela primeira vez, é cadastrado que você aceita PicPay.
								/carpool --> Este comando informa que você aceita Waze Carpool, de forma permanente. Para passar a não aceitar, chame o comando novamente.
									Ex: Assim como o PicPay, por padrão, todos os caroneiros não aceitam Waze Carpool. Chamando o comando pela primeira vez, é cadastrado que você aceita Waze Carpool.";

                    TelegramConnect::sendMessage($chat_id, $help);
                    break;

                case 'teste':
                    error_log("teste");
                    $texto = "Versão 1.3 - ChatId: $chat_id";

                    TelegramConnect::sendMessage($chat_id, $texto);
                    break;

                /*Comandos de viagem*/
                case 'ida':
                    if (count($args) == 1) {

                        $resultadoHoje = $dao->getListaIdaHoje($chat_id);
                        $caronasDiaAtual = array();
                        $caronasDiaSeguinte = array();
                        $source = Config::getBotConfig("source");

                        foreach ($resultadoHoje as $carona) {
                            array_push($caronasDiaAtual, $carona);
                        }

                        $resultadoAmanha = $dao->getListaIdaAmanha($chat_id);

                        foreach ($resultadoAmanha as $carona) {
                            array_push($caronasDiaSeguinte, $carona);
                        }

                        if (!empty($caronasDiaAtual)) {
                            $textoHoje = $dataHojeDia . "\n\n<b>Ida para o " . $source . "</b>\n";
                            foreach ($caronasDiaAtual as $carona) {
                                $textoHoje .= (string)$carona . "\n";
                            }
                        }
                        if (!empty($caronasDiaSeguinte)) {
                            $textoAmanha = $dataAmanhaDia . "\n\n<b>Ida para o " . $source . "</b>\n";
                            foreach ($caronasDiaSeguinte as $carona) {
                                $textoAmanha .= (string)$carona . "\n";
                            }
                        }

                        $texto = $dao->retornarAvisos($chat_id);
                        $texto .= isset($textoHoje) ? $textoHoje . "\n" : "";
                        $texto .= isset($textoAmanha) ? $textoAmanha : "";

                        $texto = empty(str_replace("\n", "", trim($texto))) ? "Não há ofertas de carona de ida :(" : $texto;

                        TelegramConnect::sendMessage($chat_id, $texto);
                    } elseif ((count($args) == 4) && !(strtolower($args[3]) === 'pechincha') && is_numeric($args[2])) {

                        $horarioRaw = $args[1];
                        $horarioRegex = '/^(?P<hora>[01]?\d|2[0-3])(?::(?P<minuto>[0-5]\d))?$/';

                        $horarioValido = preg_match($horarioRegex, $horarioRaw, $resultado);

                        $spots = $args[2];
                        $location = $args[3];

                        if ($horarioValido) {
                            $hora = $resultado['hora'];
                            $minuto = isset($resultado['minuto']) ? $resultado['minuto'] : "00";

                            $dtime = DateTime::createFromFormat("G:i", $hora . ':' . $minuto, $timezone);

                            $date = new DateTime('NOW', $timezone);

                            if ($dtime < $date) {
                                $dtime->modify('+1 day');
                            }

                            $timestamp = $dtime->getTimestamp();

                            $travel_hour = $hora . ":" . $minuto;

                            $dao->createCarpoolWithDetails($chat_id, $user_id, $username, $travel_hour, $timestamp, $spots, $location, '0');

                            TelegramConnect::sendMessage($chat_id, "@" . $username . " oferece carona de ida às " . $travel_hour . " com " . $spots . " vaga" . ($spots > 1 ? "s" : "") . " saindo d" . $pontoReferenciaMap->prefixoPontoReferencia($location) . " ". $location);
                        } else {
                            TelegramConnect::sendMessage($chat_id, "Horário inválido.");
                        }
                    } else {
                        TelegramConnect::sendMessage($chat_id, "Uso: /ida [horario] [vagas] [local] \nEx: /ida 10:00 2 macembu");
                    }
                    break;

                case 'volta':
                    if (count($args) == 1) {
                        $resultadoHoje = $dao->getListaVoltaHoje($chat_id);
                        $caronasDiaAtual = array();
                        $caronasDiaSeguinte = array();
                        $source = Config::getBotConfig("source");

                        foreach ($resultadoHoje as $carona) {
                            array_push($caronasDiaAtual, $carona);
                        }

                        $resultadoAmanha = $dao->getListaVoltaAmanha($chat_id);

                        foreach ($resultadoAmanha as $carona) {
                            array_push($caronasDiaSeguinte, $carona);
                        }

                        if (!empty($caronasDiaAtual)) {
                            $textoHoje = $dataHojeDia . "\n\n<b>Volta do " . $source . "</b>\n";
                            foreach ($caronasDiaAtual as $carona) {
                                $textoHoje .= (string)$carona . "\n";
                            }
                        }
                        if (!empty($caronasDiaSeguinte)) {
                            $textoAmanha = $dataAmanhaDia . "\n\n<b>Volta do " . $source . "</b>\n";
                            foreach ($caronasDiaSeguinte as $carona) {
                                $textoAmanha .= (string)$carona . "\n";
                            }
                        }

                        $texto = $dao->retornarAvisos($chat_id);
                        $texto .= isset($textoHoje) ? $textoHoje . "\n" : "";
                        $texto .= isset($textoAmanha) ? $textoAmanha : "";

                        $texto = empty(str_replace("\n", "", trim($texto))) ? "Não há ofertas de carona de volta :(" : $texto;

                        TelegramConnect::sendMessage($chat_id, $texto);


                    } elseif ((count($args) == 4) && !(strtolower($args[3]) === 'pechincha') && is_numeric($args[2])) {

                        $horarioRaw = $args[1];
                        $horarioRegex = '/^(?P<hora>[01]?\d|2[0-3])(?::(?P<minuto>[0-5]\d))?$/';

                        $horarioValido = preg_match($horarioRegex, $horarioRaw, $resultado);

                        $spots = $args[2];
                        $location = $args[3];

                        if ($horarioValido) {

                            $timezone = new DateTimeZone("America/Bahia");
                            $hora = $resultado['hora'];
                            $minuto = isset($resultado['minuto']) ? $resultado['minuto'] : "00";

                            $dtime = DateTime::createFromFormat("G:i", $hora . ':' . $minuto, $timezone);

                            $date = new DateTime('NOW', $timezone);

                            error_log($dtime->getTimestamp() . "horasetada");
                            error_log($date->getTimestamp() . "horaagora");

                            if ($dtime->getTimestamp() < $date->getTimestamp()) {
                                $dtime->modify('+1 day');
                            }

                            $timestamp = $dtime->getTimestamp();

                            $travel_hour = $hora . ":" . $minuto;

                            $dao->createCarpoolWithDetails($chat_id, $user_id, $username, $travel_hour, $timestamp, $spots, $location, '1');

                            TelegramConnect::sendMessage($chat_id, "@" . $username . " oferece carona de volta às " . $travel_hour . " com " . $spots . " vaga" . ($spots > 1 ? "s"  : "") . " indo até " . $pontoReferenciaMap->prefixoPontoReferencia($location) . " ". $location);

                        }
                    } elseif ((count($args) == 4) && !(strtolower($args[3]) === 'pechincha') && is_numeric($args[2])) {

                        $horarioRaw = $args[1];
                        $horarioRegex = '/^(?P<hora>[01]?\d|2[0-3])(?::(?P<minuto>[0-5]\d))?$/';

                        $horarioValido = preg_match($horarioRegex, $horarioRaw, $resultado);

                        $spots = $args[2];
                        $location = $args[3];

                        if ($horarioValido) {

                            $timezone = new DateTimeZone("America/Bahia");
                            $hora = $resultado['hora'];
                            $minuto = isset($resultado['minuto']) ? $resultado['minuto'] : "00";

                            $dtime = DateTime::createFromFormat("G:i", $hora . ':' . $minuto, $timezone);

                            $date = new DateTime('NOW', $timezone);

                            if ($dtime->getTimestamp() < $date->getTimestamp()) {
                                $dtime->modify('+1 day');
                            }

                            $timestamp = $dtime->getTimestamp();

                            $travel_hour = $hora . ":" . $minuto;

                            $dao->createCarpoolWithDetails($chat_id, $user_id, $username, $travel_hour, $timestamp, $spots, $location, '1');

                            TelegramConnect::sendMessage($chat_id, "@" . $username . " oferece carona de volta às " . $travel_hour . " com " . $spots . " vaga" . ($spots > 1 ? "s" : "") . " indo até " . $pontoReferenciaMap->prefixoPontoReferencia($location) . " ". $location);

                        } else {
                            TelegramConnect::sendMessage($chat_id, "Horário inválido.");
                        }
                    } else {
                        TelegramConnect::sendMessage($chat_id, "Uso: /volta [horario] [vagas] [local] \nEx: /volta 15:00 2 macembu");
                    }
                    break;

                case 'caronas':

                    $resultadoHoje = $dao->getListaIdaHoje($chat_id);
                    $caronasDiaAtual = array();
                    $caronasDiaSeguinte = array();
                    $source = Config::getBotConfig("source");

                    foreach ($resultadoHoje as $carona) {
                        array_push($caronasDiaAtual, $carona);
                    }

                    $resultadoAmanha = $dao->getListaIdaAmanha($chat_id);

                    foreach ($resultadoAmanha as $carona) {
                        array_push($caronasDiaSeguinte, $carona);
                    }

                    if (!empty($caronasDiaAtual)) {
                        $textoIdaHoje = "\n<b>Ida para o " . $source . "</b>\n";
                        foreach ($caronasDiaAtual as $carona) {
                            $textoIdaHoje .= (string)$carona . "\n";
                        }
                    }
                    if (!empty($caronasDiaSeguinte)) {
                        $textoIdaAmanha = "\n<b>Ida para o " . $source . "</b>\n";
                        foreach ($caronasDiaSeguinte as $carona) {
                            $textoIdaAmanha .= (string)$carona . "\n";
                        }
                    }

                    unset($caronasDiaAtual);
                    unset($caronasDiaSeguinte);

                    $caronasDiaAtual = array();
                    $caronasDiaSeguinte = array();

                    $resultadoHoje = $dao->getListaVoltaHoje($chat_id);

                    foreach ($resultadoHoje as $carona) {
                        array_push($caronasDiaAtual, $carona);
                    }

                    $resultadoAmanha = $dao->getListaVoltaAmanha($chat_id);

                    foreach ($resultadoAmanha as $carona) {
                        array_push($caronasDiaSeguinte, $carona);
                    }

                    if (!empty($caronasDiaAtual)) {
                        $textoVoltaHoje = "\n<b>Volta do " . $source . "</b>\n";
                        foreach ($caronasDiaAtual as $carona) {
                            $textoVoltaHoje .= (string)$carona . "\n";
                        }
                    }
                    if (!empty($caronasDiaSeguinte)) {
                        $textoVoltaAmanha = "\n<b>Volta do " . $source . "</b>\n";
                        foreach ($caronasDiaSeguinte as $carona) {
                            $textoVoltaAmanha .= (string)$carona . "\n";
                        }
                    }

                    $texto = $dao->retornarAvisos($chat_id);
                    $texto .= isset($textoIdaHoje) || isset($textoVoltaHoje) ? $dataHojeDia . "\n" : "";
                    $texto .= isset($textoIdaHoje) ? $textoIdaHoje . "\n" : "";
                    $texto .= isset($textoVoltaHoje) ? $textoVoltaHoje . "\n" : "";

                    $texto .= isset($textoIdaAmanha) || isset($textoVoltaAmanha) ? $dataAmanhaDia . "\n " : "";
                    $texto .= isset($textoIdaAmanha) ? $textoIdaAmanha . "\n" : "";
                    $texto .= isset($textoVoltaAmanha) ? $textoVoltaAmanha . "\n" : "";

                    $texto = empty(str_replace("\n", "", trim($texto))) ? "Não há ofertas de carona :(" : $texto;

                    TelegramConnect::sendMessage($chat_id, $texto);
                    break;

                case 'vagas':
                    if (count($args) == 3) {
                        $spots = $args[2];
                        if ($args[1] == 'ida') {
                            $dao->updateSpots($chat_id, $user_id, $spots, '0');
                            TelegramConnect::sendMessage($chat_id, "@" . $username . " atualizou o número de vagas de ida para " . $spots);
                        } elseif ($args[1] == 'volta') {
                            $dao->updateSpots($chat_id, $user_id, $spots, '1');
                            TelegramConnect::sendMessage($chat_id, "@" . $username . " atualizou o número de vagas de volta para " . $spots);
                        } else {
                            TelegramConnect::sendMessage($chat_id, "Formato: /vagas [ida|volta] [vagas]\nEx: /vagas ida 2");
                        }
                    } else {
                        TelegramConnect::sendMessage($chat_id, "Formato: /vagas [ida|volta] [vagas]\nEx: /vagas ida 2");
                    }
                    break;

                case 'remover':
                    if (count($args) == 2) {
                        if ($args[1] == 'ida') {
                            $dao->removeCarpool($chat_id, $user_id, '0');
                            TelegramConnect::sendMessage($chat_id, "@" . $username . " removeu a carona de ida");
                        } elseif ($args[1] == 'volta') {
                            $dao->removeCarpool($chat_id, $user_id, '1');
                            TelegramConnect::sendMessage($chat_id, "@" . $username . " removeu a carona de volta");
                        } elseif ($args[1] == 'aviso' && TelegramConnect::isAdmin($chat_id, $user_id)) {
                            $dao->removerAviso($chat_id);
                        } else {
                            TelegramConnect::sendMessage($chat_id, "Formato: /remover [ida|volta]");
                        }
                    } else {
                        TelegramConnect::sendMessage($chat_id, "Formato: /remover [ida|volta]");
                    }

                    break;
                case 'picpay':
                    if (count($args) == 1) {
                        $resultado = $dao->insertMeioPagamento($chat_id, $user_id, 'picpay');

                        error_log($resultado);

                        $texto = $resultado ? "@" . $username . " informou que aceita PicPay" : "@" . $username . " informou que <b>NÃO</b> aceita PicPay";
                        TelegramConnect::sendMessage($chat_id, $texto);
                    }
                    break;
                case 'carpool':
                    if (count($args) == 1) {
                        $resultado = $dao->insertMeioPagamento($chat_id, $user_id, 'carpool');

                        $texto = $resultado ? "@" . $username . " informou que aceita Waze Carpool" : "@" . $username . " informou que <b>NÃO</b> aceita Waze Carpool";
                        TelegramConnect::sendMessage($chat_id, $texto);
                    }
                    break;
		            case 'sobre':
                        if (count($args) == 1) {

                            $texto = "<a href='https://github.com/waltersfilho/carpool-bot'>Teste</a>";
                            TelegramConnect::sendMessage($chat_id, $texto);
                        }
                        break;

                    case 'aviso':
                        if(TelegramConnect::isAdmin($chat_id, $user_id)){
                            $dao->inserirAviso($chat_id, $args);
                        }
                        break;

            }


        } else {
            TelegramConnect::sendMessage($chat_id, "Registre seu username nas configurações do Telegram para utilizar o Bot.");
        }
    }
}
