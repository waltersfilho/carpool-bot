<?php 

    class Carona{

        private $chat_id;
        private $user_id;
        private $username;
        private $travel_hour;
	    private $spots;
        private $route;
        private $picpay;
        private $wunder;

        public function __construct($data){
            $this->chat_id = $data["chat_id"];
			$this->user_id = $data["user_id"];
			$this->username = $data["username"];
			$this->travel_hour = $data["travel_hour"];
			$this->spots = $data["spots"];
			$this->location = $data["location"];
			$this->route = $data["route"];
			$this->picpay = $data["picpay"];
			$this->wunder = $data["wunder"];
        }
		public function __toString() {
            $horaFormatada = date( "G:i", strtotime('-3 hours', strtotime($this->travel_hour)));
			if (!empty($this->spots) && !empty($this->location)) {
				$plural = $this->spots > 1 ? "s" : "";
				$this->username .= $this->picpay ? "(p)" : "";
                $this->username .= $this->wunder ? "(w)" : "";
                if(!$this->route) {
					return  "@" . $this->username . " - " . $horaFormatada . " da " . $this->location . " (" . $this->spots . " vaga" . $plural . ")";
				} else {
					return "@" . $this->username . " - " . $horaFormatada . " até " . $this->location . " (" . $this->spots . " vaga" . $plural . ")";
				}
			} else {
				return "<i>" . "@" . $this->username . " - " . $horaFormatada . " (Lotado)</i>";
			}
		}
		
		public function ehIda(){
			return $this->route == 0;
		}

		public function getTravelHour(){
            return $this->travel_hour;
        }
    }

    
