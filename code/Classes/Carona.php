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

			if (!empty($this->spots) && !empty($this->location)) {
				if($this->route === true) {
					return "\n" . "@" . $this->username . " - " . substr($this->travel_hour, 0, -3) . " - da " . $this->location . "(" . $this->spots . " vagas . ")";
				} else {
					return "\n" . "@" . $this->username . " - " . substr($this->travel_hour, 0, -3) . " - até " . $this->location . "(" . $this->spots . " vagas . ")";
			} else {
				return "\n" . "@" . $this->username . " - " . substr($this->travel_hour, 0, -3)  . " (Lotado)";
			}
		}
		
		public function ehIda(){
			return $this->route == 0;
		}
    }

    
