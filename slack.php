<?php
ini_set('display_errors', 1);
require_once('common.php');

$token = $_POST['token'];
$text = $_POST['text'];


if($token != $settings['token']){
  $msg = "The token for the slash command doesn't match. Check your script.";
  die($msg);
  echo $msg;
}


$reply ="";

foreach ($arr as $key => $value) {
	$reply = $reply."<".$value['link']."|*".$key."*> ".$value['count']."        ";
}

if ($text){

	$reply = "_counts:_ ".$reply."\n_".$text.":_   ";
	foreach ($arr as $key => $value) {
		$value['pace'] = round($value['pace'],2);
		$value['close'] = date_format($value['close'],'l');
		$value['remaining'] = $value['remaining']->format("%a d %h h %i m");
		$value['elapsed'] = $value['elapsed']->format("%a d %h h %i m");
		$reply = $reply."<".$value['link']."|*".$key."*> ".$value[$text]."   ";
	}
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
