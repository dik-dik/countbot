<?php
require_once('common.php');
ini_set('display_errors', 1);

$pi_json_file = file_get_contents('https://www.predictit.org/api/marketdata/group/83');
$pi_data = json_decode($pi_json_file);

$arr['POTUS']['handle'] = "potus";
$arr['VP-Fri']['handle'] = "vp";
$arr['VP-Tue']['handle'] = "vp";

foreach($arr as $key => $value) {

	$handle =  $arr[$key]['handle'];
	
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
	print "\n";
}


?>