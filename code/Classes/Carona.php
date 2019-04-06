<?php 

    class Carona{

        private $chat_id;
        private $user_id;
        private $username;
        private $travel_hour;
	    private $spots;
        private $route;

        public function __construct($data){
            $this->chat_id = $data["chat_id"];
			$this->user_id = $data["user_id"];
			$this->username = $data["username"];
			$this->travel_hour = $data["travel_hour"];
			$this->spots = $data["spots"];
			$this->location = $data["location"];
			$this->route = $data["route"];
        }
		public function __toString() {
            $horaFormatada = date( "G:i", strtotime($this->travel_hour));
			if (!empty($this->spots) && !empty($this->location)) {
				$plural = $this->spots > 1 ? "s" : "";
				if(!$this->route) {
					return "\n" . "@" . $this->username . " - " . $horaFormatada . " da " . $this->location . " (" . $this->spots . " vaga" . $plural . ")";
				} else {
					return "\n" . "@" . $this->username . " - " . $horaFormatada . " até " . $this->location . " (" . $this->spots . " vaga" . $plural . ")";
				}
			} else {
				return "\n<i>" . "@" . $this->username . " - " . $horaFormatada . " (Lotado)</i>";
			}
		}
		
		public function ehIda(){
			return $this->route == 0;
		}

		public function getTravelHour(){
            return $this->travel_hour;
        }
    }

    
