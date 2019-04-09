<?php
define("MAIN", "MAIN");

// load the common code
require_once __DIR__.'/lib.php';

// genereate the response
header('Content-Type: application/json');
echo json_encode($echoData);
