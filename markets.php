<?php
ini_set('display_errors', 1);

//Grab Market Data

$pi_json_file = file_get_contents('https://www.predictit.org/api/marketdata/all');
$pi_data = json_decode($pi_json_file);

?>
<html>

<head>
<script type="text/javascript" src="http://code.jquery.com/jquery-latest.min.js"></script>

<meta name="viewport" content="width=device-width, initial-scale=1">  
    <title>All PI Markets</title>
	
	<link rel="stylesheet" type="text/css" href="http://cdn.datatables.net/1.10.13/css/jquery.dataTables.css">
	
    <style>
        body {
            font-family: sans-serif;
        }
        
        a { text-decoration: none;}
        a.link, a.visited { color: #4286f4; }          
        a:hover { color: #a8088d; }       
        
        .nomobile { display: none; }
        
        @media screen and (min-width: 768px) {
    		.nomobile {
		        clear: both;
		        display: block;
    		}
	}
    </style>

	<script type="text/javascript" charset="utf8" src="http://cdn.datatables.net/1.10.13/js/jquery.dataTables.js"></script>
	<script type="text/javascript">
	$(document).ready(function(){
	    $('#table_id').DataTable({
	    	paging: false, "order": [[3, "desc"]]
	    });
	});</script>
	
</head>

<body>
<table  id="table_id" class="display">
<thead>
<tr>
<th>Market</th>
<th>Contract</th>
<th>Latest</th>
<th>Change</th>
<th>Buy Yes</th>
<th>Sell Yes</th>
<th>Buy No</th>
<th>Sell No</th>
<th>End Date</th>
</tr>
</thead>
<tbody>
<?php

foreach($pi_data->Markets as $item)
{
	foreach ($item->Contracts as $subitem)
		{	echo "<tr><td><a href=";
			echo $item->URL;
			echo ">";
			echo $item->ShortName;
			echo "</a></td>\n<td><a href=";
			echo $subitem->URL;
			echo ">";
			echo $subitem->Name;
			echo "</a></td>\n<td class='dt-right'>";
			echo number_format ($subitem->LastTradePrice, 2);
			echo "</td>\n<td class='dt-right'>";
			if ($subitem->LastClosePrice) {echo number_format($subitem->LastTradePrice - $subitem->LastClosePrice, 2);} else {echo 0.00;}
			echo "</td>\n<td class='dt-right'>";
			echo number_format($subitem->BestBuyYesCost, 2);
			echo "</td>\n<td class='dt-right'>";
			echo number_format($subitem->BestSellYesCost, 2);
			echo "</td>\n<td class='dt-right'>";
			echo number_format($subitem->BestBuyNoCost, 2);
			echo "</td>\n<td class='dt-right'>";
			echo number_format($subitem->BestSellNoCost, 2);
			echo "</td>\n<td>";
			echo $subitem->DateEnd;
			echo "</td></tr>\n";
		}
	echo "</tr>";
}
	
?>
</tbody>
</table>

</body>

</html>
