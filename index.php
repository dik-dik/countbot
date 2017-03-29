<?php
ini_set('display_errors', 1);
require_once('common.php');

?>
<html>

<head>
<meta name="viewport" content="width=device-width, initial-scale=1">  
    <title>"What's the count?"</title>
    <style>
        body {
            font-family: "Comic Sans MS", sans-serif;
			text-align:center;
        }
        
        h2 {
            color: #f441be;
        }
        
        h3 {
            color: green;
        }
        
        a { text-decoration: none;}
        a.title:link, a.title:visited { color: #4286f4; }          
        a.thecount:link, a.thecount:visited { color: green; } 
        a.thecount:hover, a.title:hover { color: #a8088d; }       
        
        div.blok { padding: 0 0 10; display:inline-block; width: 20em;}
        div#whole { text-align:center; float: center;}
        
        .nomobile { display: none; }
        
        @media screen and (min-width: 768px) {
    		.nomobile {
		        clear: both;
		        display: block;
    		}
	}
    </style>
</head>

<body>
	<h2 class="nomobile"><br/>"What's the count?"</h2>
<?php

foreach ($arr as $key => $value) {
	echo "<div class='blok'>
		<h3><a class='title' href='http://twitter.com/".$key."'>@".$key."</a></h3>"
		.date_format($value['close'],'l')."<h3><a class='thecount' href='".$value['link']."'>"
		.$value['count']."</a></h3><div class=nomobile>(".$value['total'].")<br/>&nbsp;</div>
		<div>Rate: ".round($value['rate'],2)." per hour<br/>"
		.round(24*$value['rate'],2)." per day
		<br/>&nbsp;<br/>
		Pace: ".round($value['pace'],2)."</div>
		<div class=nomobile>&nbsp;<br/>
		<b>Remaining: ".$value['remaining']->format("%a d %h h %i m")."</b>
		<br/>Elapsed: ".$value['elapsed']->format("%a d %h h %i m")."
		</div></div>";
}

$filename = 'data.txt';
finishHim($filename,$arr);
?>

</body>
</html>
