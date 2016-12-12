<?php

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| First we need to get an application instance. This creates an instance
| of the application / container and bootstraps the application so it
| is ready to receive HTTP / Console requests from the environment.
|
*/

$app = require __DIR__.'/../bootstrap/app.php';

/*
|--------------------------------------------------------------------------
| Run The Application
|--------------------------------------------------------------------------
|
| Once we have the application, we can handle the incoming request
| through the kernel, and send the associated response back to
| the client's browser allowing them to enjoy the creative
| and wonderful application we have prepared for them.
|
*/

// below was patched wtf
/*
  http://stackoverflow.com/questions/29728973/notfoundhttpexception-with-lumen

  If you put your Lumen App inside a subfolder (relative to your web server),
  Lumen will fail because the getPathInfo method return wrong path.
  If you want to use real getPathInfo, you should add extra arguments in run() method.

  $app->make("request")
*/
$app->run();
