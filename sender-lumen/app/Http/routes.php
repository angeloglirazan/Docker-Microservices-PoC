<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

// php -S localhost:8000 -t ./public

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$app->get('/', function () use ($app) {
  $connection = new AMQPStreamConnection('rabbitmq', 5672, 'guest', 'guest');
  $channel = $connection->channel();
  $channel->queue_declare('hello', false, false, false, false);
  $msg = new AMQPMessage('Hello from Lumen!');
  $channel->basic_publish($msg, '', 'hello');
  echo " [x] Sent 'Hello from Lumen!'\n";
  $channel->close();
  $connection->close();
});

$app->get('hello', function () use ($app) {
  echo "Hello World";
});
