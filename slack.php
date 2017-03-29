<?php

$token = $_POST['token'];

// if($token != 'xxxxxxxxx'){ #replace this with the token from your slash command configuration page
//   $msg = "The token for the slash command doesn't match. Check your script.";
//   die($msg);
//   echo $msg;
// }


ini_set('display_errors', 1);
require_once('common.php');




$reply ="";

foreach ($arr as $key => $value) {
	$reply = $reply."<".$value['link']."|*".$key."*> ".$value['count']."       ";
}

$reply = $reply."<http://realcount.club/|more>";


$result = array('response_type' => 'in_channel', 'text' => $reply);

$result = str_replace("*realDonaldTrump*", "*RDT*", $result);


//return the json response :
header('Content-Type: application/json');  // <-- header declaration
echo json_encode($result, true);    // <--- encode


// Save the data
$filename = 'data.txt';
finishHim($filename,$arr);

exit();


?>
