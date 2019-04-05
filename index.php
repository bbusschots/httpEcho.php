<?php

// DEBUGGING ONLY: enable error display
ini_set('display_errors', 1);

// load Composer-managed dependencies
require __DIR__ . '/vendor/autoload.php';

// workaround for the fact the NGINX + FPM does not provider a getallheaders() function
// Credit: https://www.popmartian.com/tipsntricks/2015/07/14/howto-use-php-getallheaders-under-fastcgi-php-fpm-nginx-etc/
if (!function_exists('getallheaders')) {
    function getallheaders() {
        $headers = [];
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }
        return $headers;
    }
}

// gather the data
$requestHeaders = getallheaders();
$requestHeaderNames = array_keys($requestHeaders);
$formData = $_GET + $_POST;
$formDataNames =  array_keys($formData);
$cookies = $_COOKIE;
$cookieNames =  array_keys($_COOKIE);
$echoData = (object)[
    'client' => (object)[
        'ip' => $_SERVER['REMOTE_ADDR'],
        'userAgent' => $_SERVER['HTTP_USER_AGENT']
    ],
    'request' => (object)[
        'url' => (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]",
        'httpVersion' => $_SERVER['SERVER_PROTOCOL'],
        'method' => $_SERVER['REQUEST_METHOD'],
        'rawHeaders' => $requestHeaders,
        'queryString' => $_SERVER['QUERY_STRING'] ? $_SERVER['QUERY_STRING'] : '',
        'rawFormData' => $formData,
        'rawCookies' => $cookies
    ],
    'server' => (object)[
        'ip' => $_SERVER['SERVER_ADDR'],
        'port' => $_SERVER['SERVER_PORT'],
        'name' => $_SERVER['SERVER_NAME'],
        'software' => $_SERVER['SERVER_SOFTWARE'],
        'cgiRevision' => $_SERVER['GATEWAY_INTERFACE']
    ]
];
$echoData->request->headerCount = sizeof($echoData->request->rawHeaders);
$echoData->request->headers = [];
foreach($requestHeaderNames as $headerName){
    $echoData->request->headers[] = (object)[
        'name' => $headerName,
        'value' => $requestHeaders[$headerName]
    ];
}
$echoData->request->formDataCount = sizeof($echoData->request->rawFormData);
$echoData->request->formData = [];
foreach($formDataNames as $formDataName){
    $echoData->request->formData[] = (object)[
        'name' => $formDataName,
        'value' => $formData[$formDataName]
    ];
}
$echoData->request->cookieCount = sizeof($echoData->request->rawCookies);
$echoData->request->cookies = [];
foreach($cookieNames as $cookieName){
    $echoData->request->cookies[] = (object)[
        'name' => $cookieName,
        'value' => $cookies[$cookieName]
    ];
}

// genereate the appropriate response based on the query string
if($_GET['want'] === 'json'){
    // JSON requested, so simply conevert and return the data object
    header('Content-Type: application/json');
    echo json_encode($echoData);
    exit(0);
}
if($_GET['want'] === 'text'){
    // plain text requested, so render data as text
    $m = new Mustache_Engine([
        'loader' => new Mustache_Loader_FilesystemLoader(dirname(__FILE__) . '/views')
    ]);
    header('Content-Type: text/plain');
    echo $m->render('text', $echoData);
    exit(0);
}
?>
<!DOCTYPE HTML>
<html>
<head>
  <meta charset="utf-8" />
  <title>HTTP Echo</title>
  
  <!-- Include Bootstrap CSS from CDN -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
</head>
<body>
<div class="container">
    <div class="row">
        <section class="col">
            <h1>Client</h1>
            
            <dl>
              <dt>IP</dt>
              <dd><code><?php echo($_SERVER['REMOTE_ADDR']); ?></code></dd>
              <dt>Browser (User Agent String)</dt>
              <dd><code><?php echo($_SERVER['HTTP_USER_AGENT']); ?></code></dd>
            </dl>
        </section>
    </div>
    <div class="row">
        <section class="col">
            <h1>HTTP Request</h1>
            
            <dl>
                <dt>URL</dt>
                <dd><code><?php echo((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"); ?></code></dd>
                <dt>Protocol Version</dt>
                <dd><code><?php echo($_SERVER['SERVER_PROTOCOL']); ?></code></dd>
                <dt>Method</dt>
                <dd><code><?php echo($_SERVER['REQUEST_METHOD']); ?></code></dd>
                <?php
                $requestHeaders = getallheaders();
                $requestHeaderNames = array_keys($requestHeaders);
                ?>
                <dt>Headers <span class="badge badge-pill badge-primary"><?php echo(sizeof($requestHeaderNames)); ?></span></dt>
                <dd>
                    <?php if(sizeof($requestHeaderNames) < 1){ ?>
                        <p class="text-muted">The request contained no headers.</p>
                    <?php }else{ ?>
                        <ul class="list-unstyled">
                          <?php foreach($requestHeaderNames as $headerName){ ?>
                              <li><code><?php echo($headerName); ?>: <?php echo($requestHeaders[$headerName]); ?></code></li>
                          <?php } ?>
                        </ul>
                    <?php } ?>
                </dd>
                <dt>Query String</dt>
                <dd>
                    <?php if($_SERVER['QUERY_STRING']){ ?>
                        <code><?php echo($_SERVER['QUERY_STRING']); ?></code>
                    <?php }else{ ?>
                        <p class="text-muted">The request did not contain a query string.</p>
                    <?php } ?>
                </dd>
                <?php
                $formData = $_GET + $_POST;
                $formDataNames =  array_keys($formData);
                ?>
                <dt>Form Data <span class="badge badge-pill badge-primary"><?php echo(sizeof($formDataNames)); ?></span></dt>
                <dd>
                    <?php if(sizeof($formDataNames) < 1){ ?>
                        <p class="text-muted">The request contained no form data.</p>
                    <?php }else{ ?>
                        <dl>
                          <?php foreach($formDataNames as $dataName){ ?>
                              <dt><code><?php echo($dataName); ?></code></dt>
                              <dd><code><?php echo($formData[$dataName]); ?></code></dd>
                          <?php } ?>
                        </dl>
                    <?php } ?>
                </dd>
                <?php
                $cookieNames =  array_keys($_COOKIE);
                ?>
                <dt>Cookies <span class="badge badge-pill badge-primary"><?php echo(sizeof($_COOKIE)); ?></span></dt>
                <dd>
                    <?php if(sizeof($cookieNames) < 1){ ?>
                        <p class="text-muted">The request contained no cookies.</p>
                    <?php }else{ ?>
                        <dl>
                          <?php foreach($cookieNames as $cookieName){ ?>
                              <dt><code><?php echo($cookieName); ?></code></dt>
                              <dd><code><?php echo($_COOKIE[$cookieName]); ?></code></dd>
                          <?php } ?>
                        </dl>
                    <?php } ?>
                </dd>
            </dl>
        </section>
    </div>
    <div class="row">
        <section class="col">
            <h1>Server</h1>
            
            <dl>
              <dt>IP:Port</dt>
              <dd><code><?php echo($_SERVER['SERVER_ADDR'].':'.$_SERVER['SERVER_PORT']); ?></code></dd>
              <dt>Name</dt>
              <dd><code><?php echo($_SERVER['SERVER_NAME']); ?></code></dd>
              <dt>Software</dt>
              <dd><code><?php echo($_SERVER['SERVER_SOFTWARE']); ?></code></dd>
              <dt>CGI Revision</dt>
              <dd><code><?php echo($_SERVER['GATEWAY_INTERFACE']); ?></code></dd>
            </dl>
        </section>
    </div>
</div>
</body>
</html>