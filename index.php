<?php
ini_set('display_errors', 1);
require_once('common.php');

include('head.php');

?>
<body>
	<h2 class="nomobile"><br/>"What's the count?"</h2>
<div>
<?php

foreach ($arr as $key => $value) {
	echo "<div class='blok'>
		<h3><a class='title' href='http://twitter.com/".$value['handle']."'>@".$key."</a></h3>"
		.date_format($value['close'],'l')."<h3><a class='thecount' href='".$value['link']."'>"
		.$value['count']."</a></h3><div class=nomobile>(".$value['total'].")<br/>&nbsp;</div>
		<div><a href='".$value['last_tweet_url']."'>Last tweet</a>: ".floor(getTotalHours($value['last_tweet_age'])).$value['last_tweet_age']->format(" h %i m")." ago</div>&nbsp;<br/>
		<div>Rate: ".round($value['rate'],2)." per hour<br/>"
		.round(24*$value['rate'],2)." per day
		<br/>&nbsp;<br/>
		Pace: ".round($value['pace'],2)."
		<br/>&nbsp;<br/>
		+1 Tweet Pace: ".round($value['nextpace'],2)."
		<br/>&nbsp;<br/>
		Silent Hour Pace: ".round($value['silentpace'],2)."
		<br/>&nbsp;<br/>
		Silent 12-Hour Pace: ".round($value['silent12pace'],2)."
		<br/>&nbsp;<br/>
		Silent Day Pace: ".round($value['silent24pace'],2)."
		</div>
		<div class=nomobile>&nbsp;<br/>
		<b>Remaining: ".$value['remaining']->format("%a d %h h %i m")."</b>
		<br/>Elapsed: ".$value['elapsed']->format("%a d %h h %i m")."
		</div></div>";
}

$filename = 'data.txt';
finishHim($filename,$arr);
?>

</div>
&nbsp;<br/>&nbsp;
<div style="float:right;">
<a style="float:right;" href="/poisson.php">Dumb-as-rocks Poisson Model</a>
</div>
</body>
</html>