<?php
require_once (__DIR__."/../util/PontoReferenciaMap.php");


class Carona
{

    private $chat_id;
    private $user_id;
    private $username;
    private $travel_hour;
    private $spots;
    private $route;
    private $picpay;
    private $carpool;
    private $pontoReferenciaMap;

    public function __construct($data)
    {
        $this->chat_id = $data["chat_id"];
        $this->user_id = $data["user_id"];
        $this->username = $data["username"];
        $this->travel_hour = $data["travel_hour"];
        $this->spots = $data["spots"];
        $this->location = $data["location"];
        $this->route = $data["route"];
        $this->picpay = $data["picpay"];
        $this->carpool = $data["carpool"];
    }

    public function __toString()
    {
        $this->pontoReferenciaMap = new PontoReferenciaMap();
        $this->username .= $this->picpay ? "(p)" : "";
        $this->username .= $this->carpool ? "(c)" : "";
        $horaFormatada = date("G:i", strtotime($this->travel_hour));
        if (!empty($this->spots) && !empty($this->location)) {
            $plural = $this->spots > 1 ? "s" : "";

            if (!$this->route) {
                return "@" . $this->username . " - " . $horaFormatada . " d" . $this->pontoReferenciaMap->prefixoPontoReferencia($this->location) . " " . $this->location . " (" . $this->spots . " vaga" . $plural . ")";
            } else {
                return "@" . $this->username . " - " . $horaFormatada . " até " . $this->pontoReferenciaMap->prefixoPontoReferencia($this->location) . " " . $this->location . " (" . $this->spots . " vaga" . $plural . ")";
            }
        } else {
            return "<i>" . "@ " . $this->username . " - " . $horaFormatada . " (Lotado)</i>";
        }
    }

    public function ehIda()
    {
        return $this->route == 0;
    }

    public function getTravelHour()
    {
        return $this->travel_hour;
    }
}

    
