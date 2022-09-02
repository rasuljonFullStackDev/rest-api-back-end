<?php
    header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS, post, get');
    header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token');
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: *");
    header('Content-Type: application/json');
include_once __DIR__.'/src/api.php';
