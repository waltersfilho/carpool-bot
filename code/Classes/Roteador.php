<?php
	require_once "Config.php";
	require_once "TelegramConnect.php";
	require_once "CaronaDAO.php";
	require_once "Carona.php";

	class Roteador{

		/*Espera o objeto 'message' já como array*/
		private static function processData($data){
			$processedData = array();

			/*TODO inicializar objeto telegramConnect com dados da mensagem*/
			$processedData['username'] = $data["message"]["from"]["username"];
			$processedData['chatId'] = $data["message"]["chat"]["id"];
			$processedData['userId'] = $data["message"]["from"]["id"];

			error_log( print_r( $processedData, true ) );

			return $processedData;
		}

		private static function processCommand($stringComando, &$args){
			/* Trata uma string que começa com '/', seguido por no maximo 32 numeros, letras ou '_', seguido ou não de '@nomeDoBot */
			$regexComando = '~^/(?P<comando>[\d\w_]{1,32})(?:@'. Config::getBotConfig('botName') .')?~';
			$command = NULL;
			$args = NULL;

			if(preg_match($regexComando, $stringComando, $match)){
				$command = $match['comando'];
				$stringComando = str_replace($match[0], "", $stringComando);
				
				$args = explode(" ", $stringComando);
				
				if(count($args) == 5) {
				   $args[3] = $args[3] . " " . $args[4];
				}
				unset($args[4]);
				error_log($args);
			}

			error_log( print_r( $command, true ) );
			error_log( print_r( $args, true ) );
			error_log( strlen($args[1]) );
			return $command;
		}

		public static function direcionar($request){
			$args = array();
			$command = self::processCommand($request['message']['text'], $args);
			$dados = self::processData($request);

			$chat_id = $dados["chatId"];
			$user_id = $dados["userId"];
			$username = $dados['username'];

            $date = date('d-m');
			
			/*Dividir cada comando em seu controlador*/
			if($username){
				$dao = new CaronaDAO();

				switch (strtolower($command)) {
					/*comandos padrão*/
					case 'regras':
						$regras = "	 BEM VINDOS AO GRUPO DE CARONAS TAQUARA-FUNDÃO

								 REGRAS

								- Quem for dar carona é só colocar o nome na lista junto com o horário e local para IDA ou VOLTA do Fundão.
								 Local de encontro, trajeto e outras informações serão combinados preferincialmente no PRIVADO.

								- É obrigatório o envio de algum documento que comprove o vínculo com a UFRJ para algum adm. 

								- Evitar assuntos não relacionados às caronas no grupo, a menos que considere de interesse público.

								- Poste suas caronas sempre em ordem de horários, podendo indicar referências como local de saída ou trajeto. Colabore apagando as caronas antigas e criando as do próximo dia.

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
									(Inclui uma carona de ida às 10:00 com 2 vagas saindo do jardim)

								Caso não seja colocado o parâmetro do horário (Ex: /ida) o bot irá apresentar a lista com as caronas registradas para o trajeto.

								/volta [horario] [vagas] [local] --> Este comando serve para definir um horário que você está VOLTANDO para o SEU BAIRRO. 
									Ex: /volta 15:00 3 merck 
									(Inclui uma carona de volta às 15:00 com 3 vagas para o jardim)
								
								Caso não seja colocado o parâmetro do horário (Ex: /volta) o bot irá apresentar a lista com as caronas registradas para o trajeto.

								OBS --> Para o local utilize sempre letras minúsculas e composto por no MÁXIMO duas palavras. Para mais de um local siga o padrão : local01/local02 
									Ex: gramado/macembu/mananciais/guerenguê/rodrigues caldas

								/remover [ida|volta] --> Comando utilizado para remover a carona da lista. SEMPRE REMOVA a carona depois dela ter sido realizada. 
									Ex: /remover ida

								/vagas [ida|volta] [vagas] --> Este comando serve para atualizar o número de vagas de uma carona
									Ex: /vagas ida 2 
									(Altera o número de vagas da ida para 2)
									Ex: /vagas ida 0
									(Altera o número de vagas da ida para 0, ou seja, lotado)";
						
						TelegramConnect::sendMessage($chat_id, $help);
						break;
						
					case 'teste':
						error_log("teste");
						$texto = "Versão 1.2 - ChatId: $chat_id";

						TelegramConnect::sendMessage($chat_id, $texto);
						break;

					/*Comandos de viagem*/
					case 'ida':
						if (count($args) == 1) {

							$resultadoHoje = $dao->getListaIdaHoje($chat_id);
                            $caronasDiaAtual = array();
                            $caronasDiaSeguinte = array();
                            $textoHoje = "";
                            $textoAmanha = "";
                            $source = Config::getBotConfig("source");

							foreach ($resultadoHoje as $carona){
                                array_push($caronasDiaAtual, $carona);
							}

                            $resultadoAmanha = $dao->getListaIdaAmanha($chat_id);

                            foreach ($resultadoAmanha as $carona){
                                array_push($caronasDiaSeguinte, $carona);
                            }

							if(!empty($caronasDiaAtual)){
                                $textoHoje =  "\n<b>Ida para o " . $source . "</b>\n";
                                foreach ($caronasDiaAtual as $carona){
                                    $textoHoje .= (string)$carona . "\n";
                                }
							}
							if (!empty($caronasDiaSeguinte)){
                                $textoAmanha = "\n<b>Ida para o " . $source . "</b>\n";
                                foreach ($caronasDiaSeguinte as $carona){
                                    $textoAmanha .= (string)$carona . "\n";
                                }
                            }

                            $texto = $textoHoje . "\n" . $textoAmanha;

							TelegramConnect::sendMessage($chat_id, $texto);
						} elseif (count($args) == 4) {

							$horarioRaw = $args[1];
							$horarioRegex = '/^(?P<hora>[01]?\d|2[0-3])(?::(?P<minuto>[0-5]\d))?$/';

							$horarioValido = preg_match($horarioRegex, $horarioRaw, $resultado);

							$spots = $args[2];
							$location = $args[3];

							if ($horarioValido){
                                $timezone = new DateTimeZone("America/Sao_Paulo");
								$hora = $resultado['hora'];
								$minuto = isset($resultado['minuto']) ? $resultado['minuto'] : "00";

                                $dtime = DateTime::createFromFormat("G:i", $hora . ':' . $minuto, $timezone);

                                $date = new DateTime('NOW', $timezone);
                                error_log($date->getTimestamp() . "teste");

                                if($date->getTimestamp() > $dtime->getTimestamp())
                                {
                                    $dtime->modify('+1 day');
                                }

                                $timestamp = $dtime->getTimestamp();

                                error_log($timestamp);

								$travel_hour = $hora . ":" . $minuto;
				
								$dao->createCarpoolWithDetails($chat_id, $user_id, $username, $travel_hour, $timestamp, $spots, $location, '0');

								TelegramConnect::sendMessage($chat_id, "@" . $username . " oferece carona de ida às " . $travel_hour . " com " . $spots . " vagas saindo de " . $location);
							} else{
								TelegramConnect::sendMessage($chat_id, "Horário inválido.");
							}
						} else {
							TelegramConnect::sendMessage($chat_id, "Uso: /ida [horario] [vagas] [local] \nEx: /ida 10:00 2 mecembu");
						}
						break;

					case 'volta':
						if (count($args) == 1) {
							$resultado = $dao->getListaVolta($chat_id);

							$source = Config::getBotConfig("source");
							$texto = "<b>Volta do " . $source . "</b>\n";
							foreach ($resultado as $carona){
								$texto .= (string)$carona . "\n";
							}

							TelegramConnect::sendMessage($chat_id, $texto);

						

						} elseif (count($args) == 4) {

                            $horarioRaw = $args[1];
                            $horarioRegex = '/^(?P<hora>[01]?\d|2[0-3])(?::(?P<minuto>[0-5]\d))?$/';

                            $horarioValido = preg_match($horarioRegex, $horarioRaw, $resultado);

                            $spots = $args[2];
                            $location = $args[3];

                            if ($horarioValido){

                                $hora = $resultado['hora'];
                                $minuto = isset($resultado['minuto']) ? $resultado['minuto'] : "00";

                                $dtime = DateTime::createFromFormat("G:i", $hora . ':' . $minuto);

                                $date = new DateTime();
                                error_log($date->getTimestamp());

                                if($date->getTimestamp() < $dtime->getTimestamp())
                                {
                                    $dtime->modify('+1 day');
                                }

                                $timestamp = $dtime->getTimestamp();

                                error_log($timestamp);

                                $travel_hour = $hora . ":" . $minuto;

                                $dao->createCarpoolWithDetails($chat_id, $user_id, $username, $travel_hour, $timestamp, $spots, $location, '1');

								TelegramConnect::sendMessage($chat_id, "@" . $username . " oferece carona de volta às " . $travel_hour . " com " . $spots . " vagas indo até " . $location);

							}else{
								TelegramConnect::sendMessage($chat_id, "Horário inválido.");
							}
						} else {
							TelegramConnect::sendMessage($chat_id, "Uso: /volta [horario] [vagas] [local] \nEx: /volta 15:00 2 macembu");
						}
						break;
					      
					case 'caronas':
						
						$resultado = $dao->getListaIda($chat_id);

						$source = Config::getBotConfig("source");
						$texto = "<b>Ida para o " . $source . "</b>\n";
						foreach ($resultado as $carona){
							$texto .= (string)$carona . "\n";
						}
						
						$resultado = $dao->getListaVolta($chat_id);

						$source = Config::getBotConfig("source");
						$texto .= "\n<b>Volta do " . $source . "</b>\n";
						foreach ($resultado as $carona){
							$texto .= (string)$carona . "\n";
						}
						
						

						TelegramConnect::sendMessage($chat_id, $texto);
						break;

					case 'vagas':
						if (count($args) == 3) {
							$spots = $args[2];
							if($args[1] == 'ida') {
								$dao->updateSpots($chat_id, $user_id, $spots, '0');
								TelegramConnect::sendMessage($chat_id, "@".$username." atualizou o número de vagas de ida para " . $spots);
							} elseif ($args[1] == 'volta') {
								$dao->updateSpots($chat_id, $user_id, $spots, '1');
								TelegramConnect::sendMessage($chat_id, "@".$username." atualizou o número de vagas de volta para " . $spots);
							} else {
								TelegramConnect::sendMessage($chat_id, "Formato: /vagas [ida|volta] [vagas]\nEx: /volta ida 2");
							}
						} else {
							TelegramConnect::sendMessage($chat_id, "Formato: /vagas [ida|volta] [vagas]\nEx: /volta ida 2");
						}
						break;

					case 'remover':
						if (count($args) == 2) {
							if($args[1] == 'ida') {
								$dao->removeCarpool($chat_id, $user_id, '0');
								TelegramConnect::sendMessage($chat_id, "@".$username." removeu sua ida");
							} elseif ($args[1] == 'volta') {
								$dao->removeCarpool($chat_id, $user_id, '1');
								TelegramConnect::sendMessage($chat_id, "@".$username." removeu sua volta");
							} else {
								TelegramConnect::sendMessage($chat_id, "Formato: /remover [ida|volta]");
							}
						} else {
							TelegramConnect::sendMessage($chat_id, "Formato: /remover [ida|volta]");
						}

						break;
				}
			} else {
				TelegramConnect::sendMessage($chat_id, "Registre seu username nas configurações do Telegram para utilizar o Bot.");
			}
		}
	}
