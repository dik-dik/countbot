<?php
require_once('common.php');
ini_set('display_errors', 1);
date_default_timezone_set('America/New_York');


if(isset($_GET['POTUS'])){
	$expected['POTUS'] = $_GET['POTUS'];
} else {
	$expected['POTUS'] = 1;
}

if(isset($_GET['realDonaldTrump'])){
	$expected['realDonaldTrump'] = $_GET['realDonaldTrump'];
} else {
	$expected['realDonaldTrump'] = 1;
}

if(isset($_GET['VP-Fri'])){
	$expected['VP-Fri'] = $_GET['VP-Fri'];
} else {
	$expected['VP-Fri'] = 1;
}

if(isset($_GET['VP-Tue'])){
	$expected['VP-Tue'] = $_GET['VP-Tue'];
} else {
	$expected['VP-Tue'] = 1;
}

if(isset($_GET['VP'])){
	$expected['VP'] = $_GET['VP'];
} else {
	$expected['VP'] = 1;
}


$title = "Choose your own adventure";

include('head.php');


?>

<body>
	<h2>Choose your own adventure</h2>
	<h3><?php 
		echo date_format($now,"D M d h:i A T");
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
?>
<form  action='' class=expect>
	
<?php

foreach ($arr as $key => $value) {
	if(is_numeric($_GET[$value['marketID']])){
		$remaining_count = $_GET[$value['marketID']];
	} else {
		$model[$key] = array_map('str_getcsv', file($key.'_median.csv'));
		$remaining_count = number_format(lookup($value['days'],$model[$key]),0);
	}
	
	echo "<div class='blok'>
		<h3><a class='title' href='http://twitter.com/".$value['handle']."'>@".$key."</a></h3>"
		.date_format($value['close'],'l')."<h3><a class='thecount' href='".$value['link']."'>"
		.$value['count']."</a></h3>";
	echo "<div><a href='".$value['last_tweet_url']."'>Last tweet</a>: ".floor(getTotalHours($value['last_tweet_age'])).$value['last_tweet_age']->format(" h %i m")." ago</div><br/>&nbsp;<br/>
			Expected tweets: <input size=3 class=expect min=0 type=number value=".$remaining_count." name=".$value['marketID']."><br/>&nbsp;<br/><center><table>";
		
	$i = 0;
	$runningpoissonvalue = 0;
	$cumulativepoisson = 0;
	$k = 0;
	while ( $i <= $value['Blast']) {
		$j = $i+$value['count'];;
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
<input style="" type="submit" value="Go"></form>
<br/>
<a style="float:right;" href="/poisson.php">back to the regular model</a><br/>
<a style="float:right;" href="/median.php?tweets=0&hours=0">or use the median-based model instead</a>
</body>
</html>