<?php

namespace Util;


class RotasUtil 
{

    /*  //TRATAMENTO DAS ROTAS NA URL  //TRATAMENTO DAS ROTAS NA URL  //TRATAMENTO DAS ROTAS NA URL  //TRATAMENTO DAS ROTAS NA URL */


    public static function get_rotas(){ /* ROTEADOR */

        $urls = self::getUrls();

        $request = array();
        $request['rota'] = strtoupper($urls[0]); // PEGA A ROTA PRINCIPAL ex: (usuarios, cliente, perfil etc...)
        $request['recurso'] = $urls[1] ?? null;
        $request['id'] = $urls[2] ?? null;

        $request['metodo'] = $_SERVER['REQUEST_METHOD']; //pega o metodo da req (get, post, put , delete)


    }

    public static function getUrls() { //usado para pegar as rotas passadas

        $uri = str_replace('/' . DIR_PROJETO, '', $_SERVER['REQUEST_URI']);

        return explode('/', trim($uri, '/')); 
    }

}