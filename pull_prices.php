<?php
set_time_limit(0);

date_default_timezone_set('US/Eastern');

error_log("Runing at: " . date('l jS \of F Y h:i:s A') . " \n\n", 0);

echo "Runing ... bowhead:websocket_bitfinex. \n\n";

exec("php artisan bowhead:websocket_bitfinex > /dev/null &");

error_log("Runing ... bowhead:websocket_bitfinex. \n\n", 0);

echo "Runing ... bowhead:websocket_bitfinex_eth. \n\n";

exec("php artisan bowhead:websocket_bitfinex_eth > /dev/null &");

error_log("Runing ... bowhead:websocket_bitfinex_eth. \n", 0);

error_log("*********************************************\n\n", 0);

?>
