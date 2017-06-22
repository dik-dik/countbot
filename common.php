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

function getTotalDays(DateInterval $int){
    return ($int->d) + $int->h/24 + $int->i / 1400;
}


//Grab the Twitter data
$url = 'https://api.twitter.com/1.1/statuses/user_timeline.json';

$links = array_column($arr, "link");

$arr['POTUS']['handle'] = "potus";
$arr['VP-Fri']['handle'] = "vp";
$arr['VP-Tue']['handle'] = "vp";

foreach ($arr as $key => $value) {
	$handle =  $arr[$key]['handle'];
#	$arr[$key]['updated'] = false;
	if ($arr[$key]['updated'] == false) {
		$pi_json_file = file_get_contents('https://www.predictit.org/api/marketdata/group/83');
		$pi_data = json_decode($pi_json_file);
		$pisearch = 'How many tweets will @'.$handle;
		foreach($pi_data->Markets as $item)
		{
			if (strpos(strtolower($item->Name), strtolower($pisearch)) !== false)
			{
				if (array_search($item->URL, $links) == false) {
					echo ("updated url for ").$key."\n";
					$arr[$key]['updated'] = true;
					$arr[$key]['marketID'] = $item->ID;
					$arr[$key]['link'] = $item->URL;
					$html = file_get_contents($item->URL);
					$pattern = "/shall exceed (\d+,*\d*)/";
					preg_match($pattern, $html, $matches);
					$exceed = str_replace(",", "", $matches[1]);
					$arr[$key]['num'] = (int)$exceed;
					
					foreach($item->Contracts as $MarketContract)
					{
						$arr[$key]['close'] = $MarketContract->DateEnd."-04:00";
						$arr[$key]['close'] = date_create($arr[$key]['close']);
					}
				}
				elseif ($item->Status == "Open" && $arr[$key]['marketID'] == $item->ID) {
					$arr[$key]['updated'] = true;
				}
				
			}
		}
	
	
	
	
	
		$searchstring = 'Will @'.$handle.' post ';

		foreach($pi_data->Markets as $item)
		{
			if($item->ID == $arr[$key]['marketID'])
			{
				foreach($item->Contracts as $MarketContract)
				{
					if(strpos($MarketContract->LongName,$searchstring)!== false)
					{
						if(strpos($MarketContract->LongName,"or fewer")!== false)
						{
							$pattern = "/post.(\d+).or.fewer/";
							preg_match($pattern,$MarketContract->LongName,$matches);
							$arr[$key]['B1']= $matches[1];
							print $key." B1: ";
							print $arr[$key]['B1'];
							print"\n";
						}
						if(strpos($MarketContract->LongName,"or more")!== false)
						{
							$pattern = "/post.(\d+).or.more/";
							preg_match($pattern,$MarketContract->LongName,$matches);
							$arr[$key]['Blast']= $matches[1];
							print $key." Blast: ";
							print $arr[$key]['Blast'];
							print"\n";
						}
					}
				}
			}

		}
	
	
	
	
	
	}
	$getfield = '?screen_name='.$handle.'&count=1';
	$requestMethod = 'GET';
	$twitter = new TwitterAPIExchange($settings);
	$string = json_decode($twitter->setGetfield($getfield)
	             ->buildOauth($url, $requestMethod)
	             ->performRequest(),$assoc = TRUE);
	if (isset($string[0]['user']['statuses_count'])){
		$arr[$key]['last_tweet_url'] = "https://twitter.com/".$handle."/status/".$string[0]['id_str'];
		$arr[$key]['last_tweet_time'] = date_create($string[0]['created_at']);
		$arr[$key]['total'] = $string[0]['user']['statuses_count'];
	}
	$arr[$key]['last_tweet_age'] = $now->diff($arr[$key]['last_tweet_time']);
#	$arr[$key]['close'] = date_create($value['close']);
	// Check if we need to reset the number
	if ($now > $arr[$key]['close']) {
		$arr[$key]['close'] = $arr[$key]['close']->add(DateInterval::createFromDateString('7 days'));
		$arr[$key]['num'] = $arr[$key]['total'];
		echo "@".$key." count was reset";
		$arr[$key]['updated'] = false;
	}
	$arr[$key]['count'] = $arr[$key]['total'] - $arr[$key]['num'];
	$arr[$key]['open'] = clone $arr[$key]['close'];
	$arr[$key]['open'] = $arr[$key]['open']->sub(DateInterval::createFromDateString('7 days'));
	$arr[$key]['elapsed'] = $arr[$key]['open']->diff($now);
	$arr[$key]['remaining'] = $arr[$key]['close']->diff($now);
	$arr[$key]['days'] = getTotalDays($arr[$key]['elapsed']);
	$arr[$key]['days_remaining'] = 7-$arr[$key]['days'];
	$arr[$key]['hours']	= getTotalHours($arr[$key]['elapsed']);
	$arr[$key]['hours']	= getTotalHours($arr[$key]['elapsed']);
	if($arr[$key]['hours']>0){
		$arr[$key]['rate'] = $arr[$key]['count']/$arr[$key]['hours'];
	}
	else{
		$arr[$key]['rate']=$arr[$key]['count']/168;
	}
	$arr[$key]['pace'] = $arr[$key]['rate'] * 168;
	$arr[$key]['nextpace'] = (($arr[$key]['rate']*$arr[$key]['hours'])+1)/$arr[$key]['hours']* 168;
	$arr[$key]['silentpace'] = ($arr[$key]['rate']*$arr[$key]['hours'])/($arr[$key]['hours']+min(1, getTotalHours($arr[$key]['remaining'])))* 168;
	$arr[$key]['silent12pace'] = ($arr[$key]['rate']*$arr[$key]['hours'])/($arr[$key]['hours']+min(12, getTotalHours($arr[$key]['remaining'])))* 168;
	$arr[$key]['silent24pace'] = ($arr[$key]['rate']*$arr[$key]['hours'])/($arr[$key]['hours']+min(24, getTotalHours($arr[$key]['remaining'])))* 168;
}

function finishHim($filename,$arr){
	
	$content = serialize($arr);

	$data = array('potusnum'=> $arr['POTUS']['num'], 'rdtnum' => $arr['realDonaldTrump']['num'], 'vpnum' => $arr['VP1']['num'], 'potuslink'=> $arr['POTUS']['link'], 'rdtlink' => $arr['realDonaldTrump']['link'], 'vplink' => $arr['VP1']['link'], 'potusclose'=> $arr['POTUS']['close'], 'rdtclose' => $arr['realDonaldTrump']['close'], 'vpclose' => $arr['VP1']['close']);
	
	$fp = fopen('data.json', 'w');
	fwrite($fp, json_encode($data));
	fclose($fp);

	$fp = fopen('alldata.json', 'w');
	fwrite($fp, json_encode($arr));
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

$title ="";

foreach ($arr as $key => $value) {
	$title= $title.$key." ".$value['count']." ";
	$title = str_replace("realDonaldTrump", "RDT", $title);
	$title = str_replace("POTUS", "P", $title);
}

?>