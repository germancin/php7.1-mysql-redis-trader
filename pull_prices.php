<?php
$rr = set_time_limit(0);

var_dump($rr);

echo "Runing ... bowhead:websocket_bitfinex. \n\n";
exec("php artisan bowhead:websocket_bitfinex > /dev/null &");

echo "Runing ... bowhead:websocket_bitfinex_eth. \n\n";
exec("php artisan bowhead:websocket_bitfinex_eth > /dev/null &");


?>
