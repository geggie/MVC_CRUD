<?php


require_once './controller/crudController.php';

$table = isset($_REQUEST['table'])?$_REQUEST['table']:null;
$keyname = isset($_REQUEST['keyname'])?$_REQUEST['keyname']:null;
$controller = new CrudController($table, $keyname);
$controller->handleRequest();
?>
