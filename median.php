<?php
require_once('common.php');
ini_set('display_errors', 1);
date_default_timezone_set('America/New_York');

if(isset($_GET['tweets'])){
	$plustweets = $_GET['tweets'];
} else {
	$plustweets = 0;
}

if(isset($_GET['silence'])) {
	$silenthours = $_GET['silence'];
} elseif(isset($_GET['hours'])) {
	$silenthours = $_GET['hours'];
} else {
	$silenthours = 0;
}

$silenttime = clone $now;

$silentH = intval($silenthours);


$silentM = intval(($silenthours - intval($silenthours))*60);

$silentstring = "{$silentH} hours {$silentM} minutes";

$silenttime = $silenttime->add(DateInterval::createFromDateString($silentstring));

$title = "Median Model";
include('head.php');

?>

<body>
	<form action=""><br/><input size="1" type="number" step="1" value="<?php echo $plustweets;?>" name="tweets">more tweet<?php if($plustweets!=1) {echo s;}?> in<input step="0.1" size="1" type="number" min="0" max="84" value="<?php echo $silenthours ?>" name="hours" >hour<?php if($silenthours!=1){echo "s";}?> <input style="" type="submit" value="Go"></form>
	<h3><?php 
		echo date_format($silenttime,"D M d h:i A T");
		?></h3>
	<div class="whole">
<?php

function lookup($lookupValue,$array){

    foreach($array as $key => $val)
    {
        if($val[0] >= $lookupValue)
        {
        return $val[1];
        }
    }

    return null;
}

function factorial($number)
{
        if ($number < 2) {
                return 1;
        } else {
                return ($number * factorial($number-1));
        }
}

function poisson($chance, $occurrence)
{
        $e = exp(1);

        $a = pow($e, (-1 * $chance));
        $b = pow($chance,$occurrence);
        $c = factorial($occurrence);

        return $a * $b / $c;
}
//
// $arr['POTUS']['B1'] = 14;
// $arr['realDonaldTrump']['B1'] = 24;
// $arr['VP1']['B1'] = 49;
// $arr['VP2']['B1'] = 54;
//
// $arr['POTUS']['Blast'] = 35;
// $arr['realDonaldTrump']['Blast'] = 60;
// $arr['VP1']['Blast'] = 80;
// $arr['VP2']['Blast'] = 85;

foreach ($arr as $key => $value) {
	$nextcount = $value['count']+$plustweets;
	$medianmodel[$key] = array_map('str_getcsv', file($key.'_median.csv'));
	$remaining_count = lookup(($value['days']+$silenthours/24),$medianmodel[$key]);
	$next_hour = $remaining_count - lookup(((($value['days']+$silenthours/24)+$silenthours/24)+1/24),$medianmodel[$key]);
	$next_six = $remaining_count - lookup(((($value['days']+$silenthours/24)+$silenthours/24)+6/24),$medianmodel[$key]);
	$next_twelve = $remaining_count - lookup(((($value['days']+$silenthours/24)+$silenthours/24)+12/24),$medianmodel[$key]);
	$next_day = $remaining_count - lookup(((($value['days']+$silenthours/24)+$silenthours/24)+1),$medianmodel[$key]);
	
	$value['nexttargetmedian'] = $remaining_count+$nextcount;
	
	echo "<div class='blok'>
		<h3><a class='title' href='http://twitter.com/".$value['handle']."'>@".$key."</a></h3>"
		.date_format($value['close'],'l')."<h3><a class='thecount' href='".$value['link']."'>"
		.$nextcount."</a></h3>";
		if($plustweets > 0) {
			echo "(<b>".$value['count']."</b> + ".$plustweets.")<br/>&nbsp;";
		}
	echo "<div><a href='".$value['last_tweet_url']."'>Last tweet</a>: ".floor(getTotalHours($value['last_tweet_age'])).$value['last_tweet_age']->format(" h %i m")." ago</div>&nbsp;
			<br/>
		Target:&emsp;&emsp;&emsp;".number_format($value['nexttargetmedian'],1).
		"<br/>&nbsp;<center><table><tr><td>
		Expected tweets:</td><td align=right>".number_format($remaining_count,1).
		"</td></tr></table>&nbsp;<table>";
		
	$i = 1-$nextcount;
	$runningpoissonvalue = 0;
	$cumulativepoisson = 0;
	$k = 0;
	// echo ($value['days']+$silenthours/24)."\n";
	// echo $remaining_count;
	while ( $i <= $value['Blast']) {
		$j = $i+$nextcount;
		if($i>-1) {
			$poissonvalue = poisson($remaining_count,$i);
		}
		else {
			$poissonvalue = 0;
		}
		$runningpoissonvalue += $poissonvalue;
		if(($j+1)%5 ==0 && $j<$value['Blast'] && $j>=($value['B1'])){
				echo "<tr><td align=right>".$k."</td><td align=center>&dash;</td><td align=left>".$j."</td><td style='padding-left: 1em' align='right'>".number_format($runningpoissonvalue*100,0)."</td></tr>";
				$runningpoissonvalue = 0;
				$k = $j+1;
		}
		if($j==$value['Blast']){
				echo "<tr><td align=right>".$j."</td><td>+</td><td align=center></td></td><td style='padding-left: 1em' align='right'>".(100-number_format(($cumulativepoisson)*100,0))."</td></tr>";
				$runningpoissonvalue = 0;
				$k = $j+1;
		}
		$cumulativepoisson += $poissonvalue;
		$i++;
	}
	echo "</table></center>
		</div>";
}

$filename = 'data.txt';
finishHim($filename,$arr);
?>
</div>
<a style="float:right;" href="/mean.php">back to the regular model</a>
</body>
</html>