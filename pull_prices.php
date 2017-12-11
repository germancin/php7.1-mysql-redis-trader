<?php
set_time_limit(0);

date_default_timezone_set('US/Eastern');

function myLog($message){
    $file = 'messages.log';
// Open the file to get existing content
    $current = file_get_contents($file);
// Append a new person to the file
    $current .= $message;
// Write the contents back to the file
    file_put_contents($file, $current);
}

myLog(date('l jS \of F Y h:i:s A') . " \n");


echo "Runing ... bowhead:websocket_bitfinex. \n\n";

exec("/usr/bin/php7.1 artisan bowhead:websocket_bitfinex > /dev/null &");

myLog("Runing ... bowhead:websocket_bitfinex. \n");

echo "Runing ... bowhead:websocket_bitfinex_eth. \n\n";

exec("/usr/bin/php7.1 artisan bowhead:websocket_bitfinex_eth > /dev/null &");

myLog("Runing ... bowhead:websocket_bitfinex_eth. \n\n");

myLog("*********************************************\n\n");



?>
