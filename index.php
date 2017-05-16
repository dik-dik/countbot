<?php
ini_set('display_errors', 1);
require_once('common.php');

?>
<html>

<head>
<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
<link rel="icon" type="image/png" href="/favicon-32x32.png" sizes="32x32">
<link rel="icon" type="image/png" href="/favicon-16x16.png" sizes="16x16">
<link rel="manifest" href="/manifest.json">
<link rel="mask-icon" href="/safari-pinned-tab.svg" color="#5bbad5">
<meta name="theme-color" content="#ffffff">
<meta name="viewport" content="width=device-width, initial-scale=1">  
<title><?php echo $title?></title>
<style>body{font-family:"Comic Sans MS",sans-serif;text-align:center}h2{color:#f441be}h3{color:green}a{text-decoration:none}a.title:link,a.title:visited{color:#4286f4}a.thecount:link,a.thecount:visited{color:green}a.thecount:hover,a.title:hover{color:#a8088d}div.blok{padding:0 0 10px;display:inline-block;width:15em}div#whole{text-align:center;float:center}.nomobile{display:none}@media screen and (min-width: 768px){.nomobile{clear:both;display:block}}</style>
</head>

<body>
	<h2 class="nomobile"><br/>"What's the count?"</h2>
<div>
<?php

foreach ($arr as $key => $value) {
	echo "<div class='blok'>
		<h3><a class='title' href='http://twitter.com/".$value['handle']."'>@".$key."</a></h3>"
		.date_format($value['close'],'l')."<h3><a class='thecount' href='".$value['link']."'>"
		.$value['count']."</a></h3><div class=nomobile>(".$value['total'].")<br/>&nbsp;</div>
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
