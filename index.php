<?php
ini_set('display_errors', 1);
require_once('TwitterAPIExchange.php');
require_once('common.php');

?>
<html>

<head>
<meta name="viewport" content="width=device-width, initial-scale=1">  
    <title>"What's the count?"</title>
    <style>
        body {
            font-family: "Comic Sans MS", sans-serif;
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
    <div align="center" id="whole">
        <h2 class="nomobile"><br/>"What's the count?"</h2>


<div class="blok">
                    <h3><a class="title" href="http://twitter.com/potus">@POTUS</a></h3><?php echo date_format($potusclose,"l"); ?>
                    <h3><a class="thecount" href="<?php echo $potuslink; ?>"><?php echo $potuscount; ?></a></h3>
                    <div class="nomobile">(<?php echo $potustotal; ?>)<br/>&nbsp;</div>          
                Rate: <?php echo round($potusrate,2) ?> per hour
            	<br/><?php echo round(24*$potusrate,2) ?> per day<br/>&nbsp;<br/>
            	Pace: <?php echo round($potustarget,2) ?>
            	<div class="nomobile">&nbsp;<br/>
            	<!--Began: <?php echo date_format($potusopen,"Y/m/d H:i:s T"); ?> <br/>
            	End: <?php echo date_format($potusclose,"Y/m/d H:i:s T"); ?>
            	<br/>&nbsp;<br/>-->
            	<b>Remaining: <?php echo $potusinterval->format("%a d %h h %i m"); ?></b>
            	<br/>
            	Elapsed: <?php echo $potuselapsed->format("%a d %h h %i m"); ?>
            	</div>
</div>
        
<div class="blok">
                    <h3><a class="title" href="http://twitter.com/realDonaldTrump">@realDonaldTrump</a></h3><?php echo date_format($rdtclose,"l"); ?>
                    <h3><a class="thecount" href="<?php echo $rdtlink; ?>"><?php echo $realDonaldTrumpcount; ?></a></h3>
                                        <div class="nomobile">(<?php echo $realDonaldTrumptotal; ?>)<br/>&nbsp;</div>
                <div>Rate: <?php echo round($rdtrate,2) ?> per hour
            	<br/><?php echo round(24*$rdtrate,2) ?> per day<br/>&nbsp;<br/>
            	Pace: <?php echo round($rdttarget,2) ?>
            	<div class="nomobile">&nbsp;<br/>
            	<!--Began: <?php echo date_format($rdtopen,"Y/m/d H:i:s T"); ?> <br/>
            	End: <?php echo date_format($rdtclose,"Y/m/d H:i:s T"); ?>
            	<br/>&nbsp;<br/>-->
            	<b>Remaining: <?php echo $rdtinterval->format("%a d %h h %i m"); ?></b>
            	<br/>
            	Elapsed: <?php echo $rdtelapsed->format("%a d %h h %i m"); ?></div>
            	</div>
</div>

<div class="blok">
                    <h3><a class="title" href="http://twitter.com/vp">@VP</a></h3><?php echo date_format($vpclose,"l"); ?>
                    <h3><a class="thecount" href="<?php echo $vplink; ?>"><?php echo $vpcount; ?></a></h3>
                    <div class="nomobile">(<?php echo $vptotal; ?>)<br/>&nbsp;</div>
            	Rate: <?php echo round($vprate,2) ?> per hour
            	<br/><?php echo round(24*$vprate,2) ?> per day<br/>&nbsp;<br/>
            	Pace: <?php echo round($vptarget,2) ?>
            	<div class="nomobile">&nbsp;<br/>
            	<!--Began: <?php echo date_format($vpopen,"Y/m/d H:i:s T"); ?> <br/>
            	End: <?php echo date_format($vpclose,"Y/m/d H:i:s T"); ?>
            	<br/>&nbsp;<br/>-->
            	<b>Remaining: <?php echo $vpinterval->format("%a d %h h %i m"); ?></b>
            	<br/>
            	Elapsed: <?php echo $vpelapsed->format("%a d %h h %i m"); ?>
            	</div>
</div>

     
    </div>
</body>

</html>
