<?php
require './libs/NotORM.php';
include_once dirname(__FILE__) . '/Constant.php';

$dsn  = "mysql:host=localhost;dbname=sistem";
$connection  = new PDO($dsn, DB_USERNAME, DB_PASSWORD);
$db  = new NotORM($connection);
