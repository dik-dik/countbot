<?php
require_once('common.php');
ini_set('display_errors', 1);
date_default_timezone_set('America/New_York');

include('head.php');
?>

<body>
	<h2 class="nomobile"><br/>Dumb-as-rocks&trade; Poisson Model</h2>
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

foreach ($arr as $key => $value) {
	$model[$key] = array_map('str_getcsv', file($key.'_tweet_model.csv'));
	$remaining_count = lookup($value['days'],$model[$key]);
	$next_hour = $remaining_count - lookup(($value['days']+1/24),$model[$key]);
	$next_six = $remaining_count - lookup(($value['days']+6/24),$model[$key]);
	$next_twelve = $remaining_count - lookup(($value['days']+12/24),$model[$key]);
	$next_day = $remaining_count - lookup(($value['days']+1),$model[$key]);
	
	$value['target'] = $remaining_count+$value['count'];
	
	echo "<div class='blok'>
		<h3><a class='title' href='".$value['link']."'>@".$key."</a></h3>"
		.date_format($value['close'],'l')."<h3><a class='thecount' href='".$value['link']."'>"
		.$value['count']."</a></h3>
		<div><a href='".$value['last_tweet_url']."'>Last tweet</a>: ".floor(getTotalHours($value['last_tweet_age'])).$value['last_tweet_age']->format(" h %i m")." ago</div>&nbsp;
		<br/>
		Target:&emsp;&emsp;&emsp;".number_format($value['target'],2).
		"<br/>&nbsp;<center>Expected tweets:<table><tr><td>
		Next Hour:</td><td align=right>".number_format($next_hour,1).
		"</td></tr><td>
		Next 6 Hours:</td><td align=right>".number_format($next_six,1).
		"</td></tr><td>
		Next 12 Hours:</td><td align=right>".number_format($next_twelve,1).
		"</td></tr><td>
		Next 24 Hours:</td><td style='padding-left: 1em' align=right>".number_format($next_day,1).
		"</td></tr></table>&nbsp;<table>";
	$i = 1-$arr[$key]['count'];
	$runningpoissonvalue = 0;
	$cumulativepoisson = 0;
	$k = 0;
	// echo $value['days']."\n";
	// echo $remaining_count;
	while ( $i <= $value['Blast']) {
		$j = $i+$arr[$key]['count'];
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
<br/>&nbsp;<br/>
<a style="float:right;" href="/mean.php?tweets=1&hours=0">+1 tweet right now</a><br/>
<a style="float:right;" href="/mean.php?tweets=0&hours=1">+1 hour of silence</a><br/>
<a style="float:right;" href="/median.php?tweets=0&hours=0">or use the median-based model instead</a>
</body>
</html>