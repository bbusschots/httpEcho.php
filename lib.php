<?php
if (!defined("MAIN")) die();

// DEBUGGING ONLY: enable error reporting and display
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

// load Composer-managed dependencies
require_once __DIR__.'/vendor/autoload.php';

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
$queryParameters = $_GET;
$queryParameterNames = array_keys($queryParameters);
$formData = $_POST;
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
        'rawQueryParameters' => $queryParameters,
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
$echoData->request->headerCount = count($echoData->request->rawHeaders);
$echoData->request->headers = [];
foreach($requestHeaderNames as $headerName){
    $echoData->request->headers[] = (object)[
        'name' => $headerName,
        'value' => $requestHeaders[$headerName]
    ];
}
$echoData->request->queryParameterCount = count($echoData->request->rawQueryParameters);
$echoData->request->queryParameters = [];
foreach($queryParameterNames as $queryParameterName){
    $echoData->request->queryParameters[] = (object)[
        'name' => $queryParameterName,
        'value' => $queryParameters[$queryParameterName]
    ];
}
$echoData->request->formDataCount = count($echoData->request->rawFormData);
$echoData->request->formData = [];
foreach($formDataNames as $formDataName){
    $echoData->request->formData[] = (object)[
        'name' => $formDataName,
        'value' => $formData[$formDataName]
    ];
}
$echoData->request->cookieCount = count($echoData->request->rawCookies);
$echoData->request->cookies = [];
foreach($cookieNames as $cookieName){
    $echoData->request->cookies[] = (object)[
        'name' => $cookieName,
        'value' => $cookies[$cookieName]
    ];
}