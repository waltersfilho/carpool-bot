<?php

class PontoReferenciaMap
{
    private $prefixo = array(
        "gramado" => "o",
        "chinainbox" => "o",
        "center" => "o",
        "capenha" => "o",
        "planalto" => "o",
        "prezunic" => "o",
        "castelo" => "o",
    );

    public function prefixoPontoReferencia($pontoReferencia)
    {
        return array_key_exists($pontoReferencia, $this->prefixo) ? $this->prefixo[$pontoReferencia] : "a" ;
    }

}