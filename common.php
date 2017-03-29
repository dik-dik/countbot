<?php
ini_set('display_errors', 1);
require_once('TwitterAPIExchange.php');


//Grab the data
$json_file = file_get_contents('./data.json');
$json_data = json_decode($json_file,true);

$potusnum 	= $json_data['potusnum'];
$rdtnum 	= $json_data['rdtnum'];
$vpnum	 	= $json_data['vpnum'];

$potuslink 	= $json_data['potuslink'];
$rdtlink 	= $json_data['rdtlink'];
$vplink	 	= $json_data['vplink'];

$potusclose 	= date_create($json_data['potusclose']);
$rdtclose 		= date_create($json_data['rdtclose']);
$vpclose	 	= date_create($json_data['vpclose']);

$fp = fopen('pi_data.json', 'w');
fwrite($fp, json_encode($pi_data));
fclose($fp);

/** Set access tokens here - see: https://dev.twitter.com/apps/ **/
$settings = array(
    'consumer_key' => "xxxxxxxxxxxxxxxxxxxxx",
    'consumer_secret' => "xxxxxxxxxxxxxxxxxxxxx",
    'oauth_access_token' => "xxxxxxxxxxxxxxxxxxxxx-xxxxxxxxxxxxxxxxxxxxx",
    'oauth_access_token_secret' => "xxxxxxxxxxxxxxxxxxxxx"
);


//Grab the Twitter data
$url = 'https://api.twitter.com/1.1/statuses/user_timeline.json';
$vpgetfield = '?screen_name=vp&count=1';
$requestMethod = 'GET';
$twitter = new TwitterAPIExchange($settings);
$vpstring = json_decode($twitter->setGetfield($vpgetfield)
             ->buildOauth($url, $requestMethod)
             ->performRequest(),$assoc = TRUE);
$vptotal = $vpstring[0]['user']['statuses_count'];
settype($vptotal, "integer");
$vpcount = $vptotal - $vpnum;

$realDonaldTrumpgetfield = '?screen_name=realDonaldTrump&count=1';
$requestMethod = 'GET';
$twitter = new TwitterAPIExchange($settings);
$realDonaldTrumpstring = json_decode($twitter->setGetfield($realDonaldTrumpgetfield)
             ->buildOauth($url, $requestMethod)
             ->performRequest(),$assoc = TRUE);
$realDonaldTrumptotal = $realDonaldTrumpstring[0]['user']['statuses_count'];
settype($realDonaldTrumptotal, "integer");
$realDonaldTrumpcount = $realDonaldTrumptotal - $rdtnum;

$potusgetfield = '?screen_name=potus&count=1';
$requestMethod = 'GET';
$twitter = new TwitterAPIExchange($settings);
$potusstring = json_decode($twitter->setGetfield($potusgetfield)
             ->buildOauth($url, $requestMethod)
             ->performRequest(),$assoc = TRUE);
$potustotal = $potusstring[0]['user']['statuses_count'];
settype($potustotal, "integer");
$potuscount = $potustotal - $potusnum;


$now = new DateTime();

//Grab Market Data
$pi_json_file = file_get_contents('https://www.predictit.org/api/marketdata/group/83');
$pi_data = json_decode($pi_json_file);

//Check if we need to update times
if ($now > $potusclose) {
	$potusclose		= $potusclose->add(DateInterval::createFromDateString('7 days'));
	$potusnum		= $potustotal;
}

if ($now > $rdtclose) {
	$rdtclose		= $rdtclose->add(DateInterval::createFromDateString('7 days'));
	$rdtnum			= $rdttotal;
}

if ($now > $vpclose) {
	$vpclose		= $vpclose->add(DateInterval::createFromDateString('7 days'));
	$vpnum			= $vptotal;
}

foreach($pi_data->Markets as $item)
{
	if (strpos($item->TickerSymbol,'POTUSTWEETS') !== false)
	{
		$potuslink = $item->URL;
	}
	if (strpos($item->TickerSymbol,'VPTWEETS') !== false)
	{
		$vplink = $item->URL;
	}
	if (strpos($item->TickerSymbol,'TRUMPTWEETS') !== false)
	{
		$rdtlink = $item->URL;
	}
}


//Calculate open dates from close dates
$rdtopen = clone $rdtclose;
$rdtopen = $rdtopen->sub(DateInterval::createFromDateString('7 days'));

$potusopen = clone $potusclose;
$potusopen = $potusopen->sub(DateInterval::createFromDateString('7 days'));

$vpopen = clone $vpclose;
$vpopen = $vpopen->sub(DateInterval::createFromDateString('7 days'));



//Calculate intervals and elapsed time
$rdtinterval = $rdtclose->diff($now);
$rdtelapsed = $rdtopen->diff($now);

$potusinterval = $potusclose->diff($now);
$potuselapsed = $potusopen->diff($now);

$vpinterval = $vpclose->diff($now);
$vpelapsed = $vpopen->diff($now);



//Define a function to get the total hours
function getTotalHours(DateInterval $int){
    return ($int->d * 24) + $int->h + $int->i / 60;
}


$rdthours = getTotalHours($rdtelapsed);
$rdtrate = $realDonaldTrumpcount/$rdthours;
$rdttarget = $rdtrate * 168;

$potushours = getTotalHours($potuselapsed);
$potusrate = $potuscount/$potushours;
$potustarget = $potusrate * 168;

$vphours = getTotalHours($vpelapsed);
$vprate = $vpcount/$vphours;
$vptarget = $vprate * 168;




// Save the JSON data
$json_potusclose	= date("c", strtotime(date_format($potusclose,"Y/m/d H:i:s T")));
$json_rdtclose		= date("c", strtotime(date_format($rdtclose,"Y/m/d H:i:s T")));
$json_vpclose		= date("c", strtotime(date_format($vpclose,"Y/m/d H:i:s T")));

$data = array('potusnum'=> $potusnum, 'rdtnum' => $rdtnum, 'vpnum' => $vpnum, 'potuslink'=> $potuslink, 'rdtlink' => $rdtlink, 'vplink' => $vplink, 'potusclose'=> $json_potusclose, 'rdtclose' => $json_rdtclose, 'vpclose' => $json_vpclose);

$fp = fopen('data.json', 'w');
fwrite($fp, json_encode($data));
fclose($fp);



?>
