<?php
ini_set('display_errors', 1);
require_once('TwitterAPIExchange.php');

$filename = 'data.txt';
$settingsfile = 'settings.txt';

$arr = unserialize(file_get_contents($filename));

$settings = unserialize(file_get_contents($settingsfile));


$now = new DateTime();

//Define a function to convert DateInterval into hours
function getTotalHours(DateInterval $int){
    return ($int->d * 24) + $int->h + $int->i / 60;
}

//Grab the Twitter data
$url = 'https://api.twitter.com/1.1/statuses/user_timeline.json';

foreach ($arr as $key => $value) {
	$getfield = '?screen_name='.$key.'&count=1';
	$requestMethod = 'GET';
	$twitter = new TwitterAPIExchange($settings);
	$string = json_decode($twitter->setGetfield($getfield)
	             ->buildOauth($url, $requestMethod)
	             ->performRequest(),$assoc = TRUE);
	$arr[$key]['total'] = $string[0]['user']['statuses_count'];
	$arr[$key]['count'] = $arr[$key]['total'] - $arr[$key]['num'];
	$arr[$key]['close'] = date_create($value['close']);
	//Check if we need to reset the number
	if ($now > $arr[$key]['close']) {
		$arr[$key]['close'] = $arr[$key]['close']->add(DateInterval::createFromDateString('7 days'));
		$arr[$key]['num'] = $arr[$key]['total'];
		echo $key." count was reset";
	}
	$arr[$key]['open'] = clone $arr[$key]['close'];
	$arr[$key]['open'] = $arr[$key]['open']->sub(DateInterval::createFromDateString('7 days'));
	$arr[$key]['elapsed'] = $arr[$key]['open']->diff($now);
	$arr[$key]['remaining'] = $arr[$key]['close']->diff($now);
	$arr[$key]['hours']	= getTotalHours($arr[$key]['elapsed']);
	$arr[$key]['rate'] = $arr[$key]['count']/$arr[$key]['hours'];
	$arr[$key]['pace'] = $arr[$key]['rate'] * 168;
}

$pi_json_file = file_get_contents('https://www.predictit.org/api/marketdata/group/83');
$pi_data = json_decode($pi_json_file);
foreach($pi_data->Markets as $item)
{
	if (strpos($item->TickerSymbol,'POTUSTWEETS') !== false)
	{
		$arr['POTUS']['link'] = $item->URL;
	}
	if (strpos($item->TickerSymbol,'VPTWEETS') !== false)
	{
		$arr['VP']['link'] = $item->URL;
	}
	if (strpos($item->TickerSymbol,'TRUMPTWEETS') !== false)
	{
		$arr['realDonaldTrump']['link'] = $item->URL;
	}
}


function finishHim($filename,$arr){
	foreach ($arr as $key => $value) {
		#echo $value['close'];
		$arr[$key]['close'] = date("c", strtotime(date_format($arr[$key]['close'],"Y/m/d H:i:s T")));
	}
	$content = serialize($arr);

	$data = array('potusnum'=> $arr['POTUS']['num'], 'rdtnum' => $arr['realDonaldTrump']['num'], 'vpnum' => $arr['VP']['num'], 'potuslink'=> $arr['POTUS']['link'], 'rdtlink' => $arr['realDonaldTrump']['link'], 'vplink' => $arr['VP']['link'], 'potusclose'=> $arr['POTUS']['close'], 'rdtclose' => $arr['realDonaldTrump']['close'], 'vpclose' => $arr['VP']['close']);
	
	$fp = fopen('data.json', 'w');
	fwrite($fp, json_encode($data));
	fclose($fp);

	if (is_writable($filename)) {
	    if (!$handle = fopen($filename, 'w')) {
	         echo "Cannot open file ($filename)";
	         exit;
	    }

	    if (fwrite($handle, $content) === FALSE) {
	        echo "Cannot write to file ($filename)";
	        exit;
	    }

	    #echo "<!--Success, wrote to file ($filename)-->";
	    fclose($handle);
	} else {
	    echo "The file $filename is not writable";
	}
}

?>
