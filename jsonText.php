<?php
// load the common code
require_once __DIR__.'/lib.php';

// genereate the response
header('Content-Type: text/plain');
echo json_encode($echoData, JSON_PRETTY_PRINT);