<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ERROR);

define('HOST', 'localhost');
define('BANCO', 'api_ivitalize');
define('USER', 'vitalize');
define('SENHA', '123');

define('DS', DIRECTORY_SEPARATOR);
define('DIR_APP', '\xampp\htdocs\iVitalize-API');

if (file_exists('autoload.php')) {
    include 'autoload.php';
} else {
    echo "Error: Arquivo Autoload.php nao encontrado e não carregou o bootstrap.";
    exit;
}
