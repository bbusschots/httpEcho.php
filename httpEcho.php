<?php ini_set('display_errors', 1); ?>
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
                $requestHeaders = getallheaders();
                $requestHeaderNames = array_keys($requestHeaders);
                ?>
                <dt>Headers <span class="badge badge-pill badge-primary"><?php echo(sizeof($requestHeaderNames)); ?></span></dt>
                <dd>
                    <?php if(sizeof($requestHeaderNames) < 1){ ?>
                        No headers were passed.
                    <?php }else{ ?>
                        <ul>
                            <?php foreach($requestHeaderNames as $headerName){ ?>
                                <li><code><?php echo($headerName); ?>: <?php echo($requestHeaders[$headerName]); ?></code></li>
                            <?php } ?>
                        </ul>
                    <?php } ?>
                </dd>
            </dl>
        </section>
    </div>
</div>
</body>
</html>