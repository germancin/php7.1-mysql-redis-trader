<?php

namespace Bowhead\Console\Commands;

use Bowhead\Util\Console;
use Illuminate\Console\Command;
use Bowhead\Util;
use Bowhead\Traits\OHLC;

class CoinbaseNewWebsocketCommand extends Command
{
    use OHLC;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bowhead:websocket_new_coinbase';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Connect to the coinbase websocket and store OHLC data';

    /**
     * @var currency pairs
     */
    protected $instrument;

    /**
     * @var
     */
    protected $console;

    /**
     * @var current book
     */
    public $book;

    /**
     * @var array
     */
    public $channels = array();

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param $ch
     * @param $id
     *
     *  Channels are not always the same chan number
     */
    public function updChannels($ch, $id)
    {
        $this->channels[$id] = $ch;
    }

    /**
     * @param $key
     * @param $byhowmuch
     */
    public function increaseCacheKey($key, $byhowmuch)
    {
        if(\Cache::has($key)) {
            $size = \Cache::get($key);
            \Cache::add($key, $byhowmuch+$size, 5);
        } else {
            \Cache::add($key, $byhowmuch, 5);
        }
    }

    /**
     * @return array
     */
    public function bookAverage()
    {
        $lastPrice = $askSize = $bidSize = $askPrice = $bidPrice = 0;

        if(\Cache::has('coinbase::book::bidsize')) {
            $bidSize = \Cache::get('coinbase::book::bidsize');
        }
        if(\Cache::has('coinbase::book::asksize')) {
            $askSize = \Cache::get('coinbase::book::asksize');
        }

        if(\Cache::has('coinbase::book::ask')){
            $asks = \Cache::get('coinbase::book::ask');
            $asks = array_shift($asks);
            if (is_array($asks)) {
                list($price, $amt) = each($asks);
                $askPrice = (($askPrice > $price) ? $askPrice : $price);
                var_dump($askPrice);
            }else {
                echo "ASK error!\n";
            }
        }
        if(\Cache::has('coinbase::book::bid')){
            $bids = \Cache::get('coinbase::book::bid');
            $bids = array_shift($bids);
            if (is_array($bids)) {
                list($price, $amt) = each($bids);
                $bidPrice = (($bidPrice > $price) ? $bidPrice : $price);
            } else {
                echo "BID error!\n";
            }
        }
        if (\Cache::has('coinbase::ticker::last_price')) {
            $lastPrice = \Cache::get('coinbase::ticker::last_price');
        }
        $ret = [
            'price'     => $bidPrice
            ,'midspread' => $askPrice - round(($askPrice - $bidPrice)/2, 2)
            ,'askPrice'  => $askPrice
            ,'bidPrice'  => $bidPrice
            ,'spread'    => ($askPrice - $bidPrice)
            ,'askSize'   => abs($askSize)
            ,'bidSize'   => $bidSize
            ,'sizeDiff'  => abs((abs($askSize) - $bidSize))
            ,'lastPrice' => $lastPrice
        ];
        \Cache::put('coinbase::main', $ret, 5);
        return $ret;
    }

    /**
     * @param     $key
     * @param     $item
     * @param int $len
     */
    public function manageCacheArray($key, $item, $len=60)
    {
        $storeArr = array();
        $check_time = time() - $len;
        #list($msec, $sec) = explode(' ', microtime());
        $current_mtime = time(); # microtime(true); #time();
        if(\Cache::has($key)) {
            $value = \Cache::get($key);
            $value_arr = unserialize(base64_decode($value));
            #echo "----$key---\n";
            foreach($value_arr as $k => $v) {
                if (floatval($k) > floatval($check_time)) {
                    $storeArr["$k"] = $v;
                }
            }
            $storeArr["$current_mtime"] = $item;
            krsort($storeArr);
            if ($key =='coinbase::ticker::last_price_diff::array' || $key =='coinbase::ticker::last_price::array') {
                //print_r($storeArr);
            }
            \Cache::put($key, base64_encode(serialize($storeArr)), 5);
        } else {
            $value = array("$current_mtime" => $item);
            $value = base64_encode(serialize($value));
            \Cache::add($key, $value, 5);
        }
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->console = $util = new Console();
        #\Cache::flush();
        #\DB::insert("DELETE FROM orca_bitfinex_ohlc WHERE instrument = 'BTC/USD'");

        /**
         *  YOU CANNOT DO MULTIPLE SYMBOLS HERE.
         *  THEY DON'T COME IN TAGGED.
         */
        $this->instruments = ['BTCUSD'];
        $loop = \React\EventLoop\Factory::create();
        $connector = new \Ratchet\Client\Connector($loop);

        $connector('wss://api.bitfinex.com/ws')
            ->then(function(\Ratchet\Client\WebSocket $conn) {
                foreach($this->instruments as $ins) {
                    $conn->send('{"event": "subscribe","channel":"trades","pair": "' . $ins . '"}');
                    $conn->send('{"event": "subscribe","channel":"ticker","pair": "' . $ins . '"}');
                    $conn->send('{"event": "subscribe","channel":"book","pair": "' . $ins . '","prec":"R0","freq":"F0"}');
                }
                $conn->on('message', function(\Ratchet\RFC6455\Messaging\MessageInterface $msg) use ($conn) {
                    /**
                     *   DO ALL PROCESSING HERE
                     *   match up sequence and keep the book up to date.
                     */
                    \Cache::put('coinbase::running', time(), 1);

                    $data = json_decode($msg,1);
                    if ((!empty($data['event']) && $data['event'] == 'subscribed') ? true : false) {
                        if (!empty($data['channel'])) {
                            $this->updChannels($data['channel'], $data['chanId']);
                        }
                    }
                    if (!empty($data[0]) && $data[1] <> 'hb') {
                        $data[0] = $this->channels[$data[0]]; // set data[0] to book/ticker/trade

                        /** -------------- BOOK -------------- */
                        /** * Book:
                         * (
                         *  [0] => book
                         *  [1] => order ID
                         *  [2] => 2036.8 - price
                         *  [3] => 7.24   - amount of BTC
                         * )*/
                        // the first main book
                        if ($data[0] == 'book') {
                            if (is_array($data[1])) {
                                $set = $data[1];
                                foreach ($set as $thing) {
                                    if ($thing[2] > 0) {
                                        $price = $thing[1];
                                        $this->book['bid'][$thing[0]]["$price"] = $thing[2];
                                        if(\Cache::has('coinbase::book::bidsize')) {
                                            $size = \Cache::get('coinbase::book::bidsize');
                                            \Cache::put('coinbase::book::bidsize', $size + $thing[2], 5);
                                        } else {
                                            \Cache::put('coinbase::book::bidsize', $thing[2], 5);
                                        }
                                    } else {
                                        $price = $thing[1];
                                        $this->book['ask'][$thing[0]]["$price"] = $thing[2];
                                        if(\Cache::has('coinbase::book::asksize')) {
                                            $size = \Cache::get('coinbase::book::asksize');
                                            \Cache::put('coinbase::book::asksize', $size + $thing[2], 5);
                                        } else {
                                            \Cache::put('coinbase::book::asksize', $thing[2], 5);
                                        }
                                    }
                                }
                            }
                            if (!empty($data[2])) {
                                $price = $data[2];
                                if ($data[2] > 0) {
                                    /** ADD */
                                    if ($data[3] > 0) {
                                        $this->book['bid'][$data[1]]["$price"] = $data[3];
                                        $size = \Cache::get('coinbase::book::bidsize');
                                        \Cache::put('coinbase::book::bidsize',$size+$data[3] , 5);
                                    } else {
                                        $this->book['ask'][$data[1]]["$price"] = $data[3];
                                        $size = \Cache::get('coinbase::book::asksize');
                                        \Cache::put('coinbase::book::asksize',$size+$data[3] , 5);
                                    }
                                }
                            }
                            if (!is_array($data[1]) && empty($data[2])) {
                                /** REMOVE */
                                if ($data[3] > 0) {
                                    unset($this->book['bid'][$data[1]]);
                                    $size = \Cache::get('coinbase::book::bidsize');
                                    \Cache::put('coinbase::book::bidsize',$size-$data[3] , 5);
                                } else {
                                    unset($this->book['ask'][$data[1]]);
                                    $size = \Cache::get('coinbase::book::asksize');
                                    \Cache::put('coinbase::book::asksize',$size+$data[3] , 5);
                                }
                            }
                            #print_r($data);
                        }
                        /** -------------- TICKER -------------- */
                        /** Ticker
                         *(
                         *  [0] => ticker         - indicator
                         *  [1] => 2140.9         - bid
                         *  [2] => 7.25           - bid size
                         *  [3] => 2155.8         - ask
                         *  [4] => 1.292          - ask size
                         *  [5] => -124           - daily change
                         *  [6] => -0.0544        - daily change percent
                         *  [7] => 2156           - last price
                         *  [8] => 38438.38437943 - volume
                         *  [9] => 2477           - high
                         *  [10] => 2007          - low
                         * )*/
                        if ($data[0] == 'ticker') {
                            #echo "TICKER\n";
                            if (\Cache::has('coinbase::ticker::last_price')) {
                                $last = \Cache::get('coinbase::ticker::last_price');
                            } else {
                                $last = $data[7];
                            }

                            \Cache::put('coinbase::ticker::low',          $data[10], 5);
                            \Cache::put('coinbase::ticker::high',         $data[9], 5);
                            \Cache::put('coinbase::ticker::volume',       $data[8], 5);
                            \Cache::put('coinbase::ticker::last_price',   $data[7], 5);
                            \Cache::put('coinbase::ticker::daily_perc',   $data[6], 5);
                            \Cache::put('coinbase::ticker::daily_change', $data[5], 5);
                            \Cache::put('coinbase::ticker::ask_size',     $data[4], 5);
                            \Cache::put('coinbase::ticker::ask',          $data[3], 5);
                            \Cache::put('coinbase::ticker::bid_size',     $data[2], 5);
                            \Cache::put('coinbase::ticker::bid',          $data[1], 5);

                            $this->manageCacheArray('coinbase::ticker::last_price::array', $data[7]);
                            $this->manageCacheArray('coinbase::ticker::volume::array',     $data[8]);
                            $this->manageCacheArray('coinbase::ticker::ask::array',        $data[3]);
                            $this->manageCacheArray('coinbase::ticker::bid::array',        $data[1]);
                            #$this->manageCacheArray('coinbase::ticker::ask_size::array',   $data[4]);
                            #$this->manageCacheArray('coinbase::ticker::bid_size::array',   $data[2]);

                            $this->manageCacheArray('coinbase::ticker::last_price_diff::array', ($data[7]-$last));
                            #print_r($data);
                            $this->markOHLC($data, 1, 'BTC-USD');
                        }

                        /** -------------- TRADE -------------- */
                        /** * (
                         *  [0] => trades         - indicator
                         *  [1] => tu             - te then tu (final) pick one or the other
                         *  [2] => 6727327-BTCUSD -
                         *  [3] => 34317584       - seq (not present with te)
                         *  [4] => 1495850823     - ts
                         *  [5] => 2141.9         - price
                         *  [6] => -0.01          - amt (neg sold)
                         * ) */
                        if ($data[0] == 'trade'){
                            //print_r($data);
                        }
                        if ($data[0] == 'trade' && $data[1] == 'tu') {
                            #echo "TRADE\n";

                            \Cache::put('coinbase::trade::time',   $data[4], 5);
                            \Cache::put('coinbase::trade::price',  $data[5], 5);
                            \Cache::put('coinbase::trade::amount', $data[6], 5);

                            if (\Cache::has('coinbase::trade::amount::'.time())) {
                                $amt = \Cache::get('coinbase::trade::amount::'.time());
                                \Cache::put('coinbase::trade::amount::'.time(),$amt+=$data[6], 5);
                            } else {
                                \Cache::put('coinbase::trade::amount::'.time(), $data[6], 5);
                            }
                            $this->manageCacheArray('coinbase::trade::amount::array', $data[6]);
                            $this->manageCacheArray('coinbase::trade::price::array',  $data[5]);
                        }


                        /** -------------- UPDATE CACHE -------------- */
                        if ($data[0] == 'book'){
                            \Cache::put('coinbase::book::ask', $this->book['ask'], 5);
                            \Cache::put('coinbase::book::bid', $this->book['bid'], 5);
                        }
                    }
                    $averages =  $this->bookAverage();
                    #print_r($this->bookAverage());

                    #echo microtime(). ' '. $averages['lastPrice'] . "\n";
                    #echo "Received: {$msg}\n";

                });

                $conn->on('close', function($code = null, $reason = null) {
                    /** log errors here */
                    echo "Connection closed ({$code} - {$reason})\n";
                });

            }, function(\Exception $e) use ($loop) {
                /** hard error */
                echo "Could not connect: {$e->getMessage()}\n";
                $loop->stop();
            });

        $loop->run();
    }
}
