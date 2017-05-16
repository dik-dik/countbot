<?php

$token = $_POST['token'];
$text = $_POST['text'];

if (isset($_GET['test']))
{
	$text = $_GET['test'];
}


// if($token != 'nK4MSN1RH2SxjoJhn18CkjzY'){ #replace this with the token from your slash command configuration page
//   $msg = "The token for the slash command doesn't match. Check your script.";
//   die($msg);
//   echo $msg;
// }


ini_set('display_errors', 1);


$pi_json_file = file_get_contents('https://www.predictit.org/api/marketdata/all');
$pi_data = json_decode($pi_json_file);

$reply = "Search results for _" . $text . "_:";


foreach($pi_data->Markets as $item) {
	foreach ($item->Contracts as $subitem){
		$subitem->Change = $subitem->LastTradePrice - $subitem->LastClosePrice;
	if (stripos($item->Name . $subitem->Name . $item->TickerSymbol . $subitem->TickerSymbol . $item->ShortName . $item->LongName, $text) !== false)
	{
		$reply = $reply . "\n<" . "https://www.predictit.org/Contract/".$subitem->ID . "/|" . $subitem->TickerSymbol . ">     Last: " . number_format ($subitem->LastTradePrice, 2)." ";
		if ($subitem->Change > 0) {
			$reply = $reply . "↑" . number_format($subitem->Change, 2);
		} elseif ($subitem->Change == 0 ) {
			$reply = $reply . "   NC  ";
		} elseif ($subitem->Change < 0 ) {
			$reply = $reply . "↓" . number_format(-1 * $subitem->Change, 2);
		}
		$reply = $reply . "     Buy Yes: ".number_format($subitem->BestBuyYesCost, 2)."     Sell Yes: ".number_format($subitem->BestSellYesCost, 2);
	}}
}

if ($reply == "Search results for _" . $text . "_:") {
	$reply = "No Results";
}


$result = array('response_type' => 'in_channel', 'text' => $reply);


//return the json response :
header('Content-Type: application/json');  // <-- header declaration
echo json_encode($result, true);    // <--- encode


exit();


?>
