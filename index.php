<?php
error_reporting(-1);
ini_set('display_errors', 'On');
include_once('db.php');
include_once('handler.php');

$file_inname = 'in.txt';
$file_outname = 'out.txt';
$db='test';

DatabaseHandler::SelectDB($db);
Handler::PrepareTables();
Handler::Run($file_inname,$file_outname);