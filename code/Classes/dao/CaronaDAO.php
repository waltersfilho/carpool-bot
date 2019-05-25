<?php
require_once (__DIR__."/../config/Connection.php");
require_once (__DIR__."/../model/Carona.php");


class CaronaDAO
{
    const QUERY_SETAR_TIMEZONE = "set timezone='America/Sao_Paulo'";

    const QUERY_CREATE_CARPOOL_WITH_DETAILS = "insert into public.caroneiros (chat_id, user_id, username, travel_hour, spots, location, route) values (:chat_id, :user_id, :username, to_timestamp(:travel_hour), :spots, :location, :route::bit(1))";

    const QUERY_UPDATE_CARPOOL_WITH_DETAILS = "update public.caroneiros set travel_hour = to_timestamp(:travel_hour), spots = :spots, location = :location where chat_id = :chat_id and user_id = :user_id and route = :route::bit(1) and expired = '0'::bit(1)";

    const QUERY_UPDATE_SPOTS = "update public.caroneiros set spots = :spots where chat_id = :chat_id and user_id = :user_id and route = :route::bit(1) and expired = '0'::bit(1) ";

    const QUERY_SEARCH = "select * from public.caroneiros where chat_id = :chat_id and user_id = :user_id and route = :route::bit(1) and expired = '0'::bit(1) ORDER BY travel_hour ASC;";

    const LISTA_QUERY_IDA_HOJE = "select p.picpay, p.wunder, c.* from public.caroneiros c inner join public.caroneiro_pagamento p on (c.user_id = p.user_id and c.chat_id = p.chat_id) where c.chat_id = :chat_id and route = '0'::bit(1) and expired = '0'::bit(1) and (SELECT EXTRACT(DAY FROM travel_hour)) = (SELECT EXTRACT(DAY FROM now())) ORDER BY travel_hour ASC;";
    const LISTA_QUERY_IDA_AMANHA = "select p.picpay, p.wunder, c.* from public.caroneiros c inner join public.caroneiro_pagamento p on (c.user_id = p.user_id and c.chat_id = p.chat_id) where c.chat_id = :chat_id and route = '0'::bit(1) and expired = '0'::bit(1) and (SELECT EXTRACT(DAY FROM travel_hour)) = (SELECT EXTRACT(DAY FROM now())) + 1 ORDER BY travel_hour ASC;";

    const LISTA_QUERY_VOLTA_HOJE = "select p.picpay, p.wunder, c.* from public.caroneiros c inner join public.caroneiro_pagamento p on (c.user_id = p.user_id and c.chat_id = p.chat_id) where c.chat_id = :chat_id and route = '1'::bit(1) and expired = '0'::bit(1) and (SELECT EXTRACT(DAY FROM travel_hour)) = (SELECT EXTRACT(DAY FROM now())) ORDER BY travel_hour ASC;";
    const LISTA_QUERY_VOLTA_AMANHA = "select p.picpay, p.wunder, c.* from public.caroneiros c inner join public.caroneiro_pagamento p on (c.user_id = p.user_id and c.chat_id = p.chat_id) where c.chat_id = :chat_id and route = '1'::bit(1) and expired = '0'::bit(1) and (SELECT EXTRACT(DAY FROM travel_hour)) = (SELECT EXTRACT(DAY FROM now())) + 1 ORDER BY travel_hour ASC;";

    const QUERY_REMOVE_CARPOOL = "delete from public.caroneiros where chat_id = :chat_id and user_id = :user_id and route = :route::bit(1)";

    const QUERY_SET_EXPIRED_CARPOOLS = "update public.caroneiros set expired = '1'::bit(1) where travel_hour + (30 ||' minutes')::interval < now()";

    const QUERY_INSERIR_ACEITA_PAGAMENTO = "insert into public.caroneiro_pagamento (chat_id, user_id) values (:chat_id, :user_id)";

    const QUERY_INSERIR_ACEITA_PICPAY = "insert into public.caroneiro_pagamento (chat_id, user_id, picpay) values (:chat_id, :user_id, :picpay)";

    const QUERY_UPDATE_ACEITA_PICPAY = "update public.caroneiro_pagamento set picpay = ~picpay where chat_id = :chat_id and user_id = :user_id";

    const QUERY_INSERIR_ACEITA_WUNDER = "insert into public.caroneiro_pagamento (chat_id, user_id, wunder) values (:chat_id, :user_id, :wunder)";

    const QUERY_UPDATE_ACEITA_WUNDER = "update public.caroneiro_pagamento set wunder = ~wunder where chat_id = :chat_id and user_id = :user_id";

    const QUERY_SEARCH_PAGAMENTO = "select * from public.caroneiro_pagamento where chat_id = :chat_id and user_id = :user_id;";

    const QUERY_SEARCH_ULTIMA_CARONA = "select * from public.caroneiros where user_id=:user_id and chat_id=:chat_id order by travel_hour DESC;";

    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }


    /*
     * TODO
     * CREATE SINGLE FUNCTION TO LIST CARPOOL
     * WITH ROUTE AS PARAMETER
     */
    /*
    public function getCarpoolList($chat_id, $route) {
        return "";
    }
    */

    public function getListaIdaHoje($chat_id)
    {

        $this->removeExpiredCarpools();

        $this->db->query(CaronaDAO::LISTA_QUERY_IDA_HOJE);
        $this->db->bind(":chat_id", $chat_id);

        return $this->montaListaCaronas($this->db->resultSet());
    }

    public function getListaIdaAmanha($chat_id)
    {

        $this->removeExpiredCarpools();

        $this->db->query(CaronaDAO::LISTA_QUERY_IDA_AMANHA);
        $this->db->bind(":chat_id", $chat_id);

        return $this->montaListaCaronas($this->db->resultSet());
    }

    public function getListaVoltaHoje($chat_id)
    {

        $this->removeExpiredCarpools();

        $this->db->query(CaronaDAO::LISTA_QUERY_VOLTA_HOJE);
        $this->db->bind(":chat_id", $chat_id);

        return $this->montaListaCaronas($this->db->resultSet());
    }

    public function getListaVoltaAmanha($chat_id)
    {

        $this->removeExpiredCarpools();

        $this->db->query(CaronaDAO::LISTA_QUERY_VOLTA_AMANHA);
        $this->db->bind(":chat_id", $chat_id);

        return $this->montaListaCaronas($this->db->resultSet());
    }

    public function updateSpots($chat_id, $user_id, $spots, $route)
    {
        $this->db->query(CaronaDAO::QUERY_UPDATE_SPOTS);
        $this->db->bind(":chat_id", $chat_id);
        $this->db->bind(":user_id", $user_id);
        $this->db->bind(":spots", $spots);
        $this->db->bind(":route", $route);

        $this->db->execute();
        error_log("Erro: " . $this->db->getError());

    }

    public function insertMeioPagamento($chat_id, $user_id, $opcao)
    {
        $this->db->query(CaronaDAO::QUERY_SEARCH_PAGAMENTO);
        $this->db->bind(":chat_id", $chat_id);
        $this->db->bind(":user_id", $user_id);

        $this->db->execute();

        if (count($this->db->resultSet()) == 0) {
            if ($opcao === 'picpay') {
                $this->db->query(CaronaDAO::QUERY_INSERIR_ACEITA_PICPAY);
                $this->db->bind(":chat_id", $chat_id);
                $this->db->bind(":user_id", $user_id);
                $this->db->bind(":picpay", '1');
            } else {
                $this->db->query(CaronaDAO::QUERY_INSERIR_ACEITA_WUNDER);
                $this->db->bind(":chat_id", $chat_id);
                $this->db->bind(":user_id", $user_id);
                $this->db->bind(":wunder", '1');
            }

            $this->db->execute();
            error_log("Erro: " . $this->db->getError());
        } else {
            $this->updateMeioPagamento($chat_id, $user_id, $opcao);
        }

        $this->db->query(CaronaDAO::QUERY_SEARCH_PAGAMENTO);
        $this->db->bind(":chat_id", $chat_id);
        $this->db->bind(":user_id", $user_id);

        $this->db->execute();

        foreach ($this->db->resultSet() as $aceita) {
            error_log($aceita[$opcao]);
            return $aceita[$opcao];
        }
    }

    public function updateMeioPagamento($chat_id, $user_id, $opcao)
    {
        if ($opcao === 'picpay') {
            $this->db->query(CaronaDAO::QUERY_UPDATE_ACEITA_PICPAY);
            $this->db->bind(":chat_id", $chat_id);
            $this->db->bind(":user_id", $user_id);
        } else {
            $this->db->query(CaronaDAO::QUERY_UPDATE_ACEITA_WUNDER);
            $this->db->bind(":chat_id", $chat_id);
            $this->db->bind(":user_id", $user_id);
        }

        $this->db->execute();
        error_log("Erro: " . $this->db->getError());
    }


    /*
     * CREATES A NEW CARPOOL ON A SPECIFIC CHAT, OFFERED BY A SINGLE USER
     * LINKED TO HIS USER NAME ON A SPECIFIC TIME EITHER GOING OR RETURNING
     * WITH LOCATION AS REFERENCE AND NUMBER OF SPOTS
     */

    public function createCarpoolWithDetails($chat_id, $user_id, $username, $travel_hour, $timestamp, $spots, $location, $route)
    {

        error_log("create carpool with details");

        $this->db->query(CaronaDAO::QUERY_SEARCH);
        $this->db->bind(":chat_id", $chat_id);
        $this->db->bind(":user_id", $user_id);
        $this->db->bind(":route", $route);

        $this->db->execute();

        if (count($this->db->resultSet()) == 0) {
            error_log("insterting new carpool with details going");
            $this->db->query(CaronaDAO::QUERY_CREATE_CARPOOL_WITH_DETAILS);
            $this->db->bind(":chat_id", $chat_id);
            $this->db->bind(":user_id", $user_id);
            $this->db->bind(":username", $username);
            $this->db->bind(":travel_hour", $timestamp);
            $this->db->bind(":spots", $spots);
            $this->db->bind(":location", strtolower($location));
            $this->db->bind(":route", $route);

            $this->db->execute();
            error_log("Erro: " . $this->db->getError());

        } else {
            error_log("updating existing carpool with details going");

            $this->db->query(CaronaDAO::QUERY_UPDATE_CARPOOL_WITH_DETAILS);
            $this->db->bind(":chat_id", $chat_id);
            $this->db->bind(":user_id", $user_id);
            $this->db->bind(":travel_hour", $timestamp);
            $this->db->bind(":spots", $spots);
            $this->db->bind(":location", $location);
            $this->db->bind(":route", $route);

            $this->db->execute();
            error_log("Erro: " . $this->db->getError());
        }

        $this->db->query(CaronaDAO::QUERY_SEARCH_PAGAMENTO);
        $this->db->bind(":chat_id", $chat_id);
        $this->db->bind(":user_id", $user_id);

        $this->db->execute();

        if (count($this->db->resultSet()) == 0) {
            $this->db->query(CaronaDAO::QUERY_INSERIR_ACEITA_PAGAMENTO);
            $this->db->bind(":chat_id", $chat_id);
            $this->db->bind(":user_id", $user_id);

            $this->db->execute();
        }


    }


    public function removeCarpool($chat_id, $user_id, $route)
    {
        $this->db->query(CaronaDAO::QUERY_REMOVE_CARPOOL);
        $this->db->bind(":chat_id", $chat_id);
        $this->db->bind(":user_id", $user_id);
        $this->db->bind(":route", $route);

        $this->db->execute();
        error_log("Erro: " . $this->db->getError());
    }


    public function getUltimaCarona($user_id, $chat_id)
    {
        $this->db->query(CaronaDAO::QUERY_SEARCH_ULTIMA_CARONA);
        $this->db->bind(":chat_id", $chat_id);
        $this->db->bind(":user_id", $user_id);

        $this->db->execute();
        error_log("Erro: " . $this->db->getError());

        return $this->montaListaCaronas($this->db->resultSet());
    }


    /*
     * AUTOMATICALLY DELETES CARPOOLS EXPIRED
     * MORE THAN 30 MINUTES
     */
    private function removeExpiredCarpools()
    {
        $this->db->query(CaronaDAO::QUERY_SETAR_TIMEZONE);
        $this->db->execute();

        error_log("Erro: " . $this->db->getError());

        $this->db->query(CaronaDAO::QUERY_SET_EXPIRED_CARPOOLS);

        $this->db->execute();
        error_log("Erro: " . $this->db->getError());

    }

    private function montaListaCaronas($resultSet)
    {

        error_log("montaListaCaronas");

        $resultado = array();

        foreach ($resultSet as $entrada) {
            array_push($resultado, new Carona($entrada));
        }

        return $resultado;
    }
}

    
