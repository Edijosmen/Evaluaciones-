<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();

define('BASE_PATH', realpath(__DIR__ . '/../'));

require BASE_PATH . '/config/config.php';
require BASE_PATH . '/app/Router.php';
(new Router())->run();
