<?php
$rr = set_time_limit(0);
var_dump($rr);


exec("php artisan bowhead:websocket_bitfinex > /dev/null &");


?>
