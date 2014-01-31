<?php
 
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);


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


//connect to the database
$con=mysqli_connect($host,$user,$password,$database);
// Check connection
if (mysqli_connect_errno())
  {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
  }

/* Crypsty broken again, fill DogePrice with last price */
$result = mysqli_query($con, "SELECT * FROM `history` ORDER BY `id` DESC LIMIT 1");
$resultarray = mysqli_fetch_row($result);
$dogeblock = $resultarray[2];
$doge_price = $resultarray[3];
$usd_price = $resultarray[4];
$dogehash = $resultarray[5];
$dogediff = $resultarray[6];

/* free result set */
mysqli_free_result($result);

$response = array(
	"network" => array(
		"block" => $dogeblock,
		"hashrate" => $dogehash,
		"difficulty" => $dogediff,
		),
	"price" => array(
		"dogebtc" => $doge_price,
		"dogeusdk" => $usd_price,
		)
	);
print json_encode($response);

mysqli_close($con);

?>