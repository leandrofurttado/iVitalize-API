<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ERROR);

define('HOST', 'localhost');
define('BANCO', 'id22021468_geniuz1');
define('USUARIO', 'id22021468_geniuzhm');
define('SENHA', 'Geniuz@157');

define('DS', DIRECTORY_SEPARATOR);
define('DIR_APP', __DIR__);
define('DIR_PROJETO', 'iVitalize-API');

if (file_exists('autoload.php')) {
    include 'autoload.php';
} else {
    echo "Error: Arquivo Autoload.php nao encontrado e não carregou o bootstrap.";
    exit;
}
