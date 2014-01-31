// This script is called once a minute to populate the DB with the latest data.

<?php
 
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

require_once 'phprpccall.php';
 
$dogecoin = new Dogecoin('user', 'password', '192.168.1.1', '22555');
// Dogecoin() is the function in 'phprpccall.php'

$dogehash = round(($dogecoin->getnetworkhashps() / 1000000000), 2);
$dogediff = round(($dogecoin->getdifficulty()), 2);
$dogeblock = $dogecoin->getblockcount();


//retrieve DB connection info from dbinfo.php
require("config.php");

/*
//dbinfo looks like this:
// <?php
// $host = 'localhost';
// $user = 'test';
// $password = ''password';
// $database = 'test';
//?> */

function get_data2($dogecoin_url)
{
    $ch2 = curl_init();
    $timeout2 = 0;
    curl_setopt($ch2,CURLOPT_URL,$dogecoin_url);
    curl_setopt($ch2,CURLOPT_RETURNTRANSFER,1);
    curl_setopt($ch2,CURLOPT_CONNECTTIMEOUT,$timeout2);
    $data2 = curl_exec($ch2);
    curl_close($ch2);
    return $data2;
}
function get_data3($coinbase_url)
{
    $ch3 = curl_init();
    $timeout3 = 10000;
    curl_setopt($ch3,CURLOPT_URL,$coinbase_url);
    curl_setopt($ch3, CURLOPT_SSL_VERIFYPEER, false); // Note: This is NOT secure
    curl_setopt($ch3,CURLOPT_RETURNTRANSFER,1);
    curl_setopt($ch3,CURLOPT_CONNECTTIMEOUT,$timeout3);
    $data3 = curl_exec($ch3);
    curl_close($ch3);
    return $data3;
}
    
$dogecoin_url='http://pubapi.cryptsy.com/api.php?method=singlemarketdata&marketid=132';//132 is the Doge market ID
$coinbase_url='https://coinbase.com/api/v1/prices/buy';

$cryptsy_doge = json_decode(get_data2($dogecoin_url), false); 
$coinbase_btc = json_decode(get_data3($coinbase_url), false);

$doge_price_raw = $cryptsy_doge->return->markets->DOGE->lasttradeprice;
$doge_price = $doge_price_raw * 100000000; // gives uBTC
$usd_price = round(($coinbase_btc->total->amount * $doge_price_raw * 1000), 3);


//connect to the database
$con=mysqli_connect($host,$user,$password,$database);
// Check connection
if (mysqli_connect_errno())
  {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
  }

/* If Crypsty is broken again, fill DogePrice with last known price from the DB*/
if ($doge_price == NULL){
$result = mysqli_query($con, "SELECT * FROM `history` ORDER BY `id` DESC LIMIT 1");
$resultarray = mysqli_fetch_row($result);
$doge_price = $resultarray[3];
$usd_price = $resultarray[4];

/* free result set */
mysqli_free_result($result);
}
  
// check if server is alive
if (!mysqli_ping($con)) {printf ("Error: %s\n", mysqli_error($con));}

if (!mysqli_query($con,"INSERT INTO `history`(`blockno`, `dogeprice`, `dogeusdk`, `dogenethash`, `dogenetdiff`) VALUES ('$dogeblock', '$doge_price', '$usd_price', '$dogehash', '$dogediff')"))
    {
    die('Error: ' . mysqli_error($con));
    }

mysqli_close($con);
?>
