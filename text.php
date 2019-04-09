<?php
define("MAIN", "MAIN");

// load the common code
require_once __DIR__.'/lib.php';

// genereate the response
$m = new Mustache_Engine([
    'loader' => new Mustache_Loader_FilesystemLoader(dirname(__FILE__).'/views')
]);
header('Content-Type: text/plain');
echo $m->render('text', $echoData);
