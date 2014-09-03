<?php
/**
 * Created by PhpStorm.
 * User: josbert
 * Date: 31/07/14
 * Time: 10:24
 */

require_once __DIR__ . '/../../../../sites/all/vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPConnection;

$connection = new AMQPConnection('10.181.138.165', 5672, 'guest', 'guest');
$channel = $connection->channel();

$channel->queue_declare('content_notification', false, false, false, false);

echo ' [*] Waiting for messages. To exit press CTRL+C', "\n";

$ch = curl_init('10.179.65.90/publish?channel=1');
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$counter = 0;
$callback = function($msg) {
  global $ch;
  static $counter = 0;
  $counter++;

  $data = array(
    'id' => $counter,
    'text' => $msg->body);
  $json_data = json_encode($data);

  curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
  $ret = curl_exec($ch);
};

$channel->basic_consume('content_notification', '', false, true, false, false, $callback);

while(count($channel->callbacks)) {
  $channel->wait();
}

curl_close($ch);
