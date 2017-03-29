<?php

$token = $_POST['token'];

// if($token != 'xxxxxxxxxxxxxx'){ #replace this with the token from your slash command configuration page
//   $msg = "The token for the slash command doesn't match. Check your script.";
//   die($msg);
//   echo $msg;
// }


ini_set('display_errors', 1);
require_once('TwitterAPIExchange.php');
require_once('common.php');



$reply = "<". $potuslink . "|" . "*POTUS*> " . $potuscount . "       <". $rdtlink . "|" . "*RDT*> " . $realDonaldTrumpcount . "       <". $vplink . "|" . "*VP*> " . $vpcount . "        <http://realcount.club/|more>";


$result = array('response_type' => 'in_channel', 'text' => $reply);


//return the json response :
header('Content-Type: application/json');  // <-- header declaration
echo json_encode($result, true);    // <--- encode


// Save the JSON data
$json_potusclose	= date("c", strtotime(date_format($potusclose,"Y/m/d H:i:s T")));
$json_rdtclose		= date("c", strtotime(date_format($rdtclose,"Y/m/d H:i:s T")));
$json_vpclose		= date("c", strtotime(date_format($vpclose,"Y/m/d H:i:s T")));

$data = array('potusnum'=> $potusnum, 'rdtnum' => $rdtnum, 'vpnum' => $vpnum, 'potuslink'=> $potuslink, 'rdtlink' => $rdtlink, 'vplink' => $vplink, 'potusclose'=> $json_potusclose, 'rdtclose' => $json_rdtclose, 'vpclose' => $json_vpclose);

$fp = fopen('data.json', 'w');
fwrite($fp, json_encode($data));
fclose($fp);

exit();


?>
