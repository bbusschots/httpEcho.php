<?php
define("MAIN", "MAIN");

// load the common code
require_once __DIR__ . '/lib.php';

// genereate the appropriate response based on the query string
$want = isset($_GET['want']) ? $_GET['want'] : '';
if($want === 'json'){
    // JSON requested, so simply conevert and return the data object
    header('Content-Type: application/json');
    echo json_encode($echoData);
    exit(0);
}
if($want === 'jsonText'){
    // JSON text requested, so simply conevert and return the data object
    header('Content-Type: text/plain');
    echo json_encode($echoData, JSON_PRETTY_PRINT);
    exit(0);
}
$m = new Mustache_Engine([
    'loader' => new Mustache_Loader_FilesystemLoader(dirname(__FILE__) . '/views')
]);
if($want === 'text'){
    // plain text requested, so render data as text
    header('Content-Type: text/plain');
    echo $m->render('text', $echoData);
    exit(0);
}
// default to HTML
echo $m->render('html', $echoData);
?>
