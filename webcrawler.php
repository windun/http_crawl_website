<?php
//header("Content-type: text/plain");

	$INSTANCE = 1;
	$stdout = fopen('php://stdout', 'w');
	if(isset($_POST['crawl_url'][0]))
	{
		$crawl_url = $_POST['crawl_url'][0];
		$crawl_depth = $_POST['crawl_url'][1];
		//exec("./crawl $crawl_url 0"." > out".$INSTANCE.".txt &");
		$cmd = "./crawl $crawl_url $crawl_depth > out".$INSTANCE.".txt &";
		exec($cmd);
	}
	else if(isset($_POST['query']))
	{
		$query = $_POST['query'];
		//exec("./crawl -c \"$query\" \"graph\""." > out".$INSTANCE.".txt &");
		$cmd = "./crawl -c \"$query\" \"graph\" > out".$INSTANCE.".txt &";
		exec($cmd);
	}

?>
<html>
<head>
<style type="text/css">
	#container {
		background-color: #212121;
		max-width: 100%;
		height: 80%;
		margin: auto;
	}
	BODY, TD, P {
		font-family: arial,helvetica,sans-serif;
		font-size: 14px;
		color: #ffffff;
	}
</style>	
</head>
<body style="background-color: #171717">
<div id="container"></div>
<script src="sigma.min.js"></script>
<script src="sigma.parsers.json.min.js"></script>
<script src="sigma.layout.forceAtlas2.min.js"></script>
<script>
	function scrollElementToEnd (element) {
		if (typeof element.scrollTop != 'undefined' &&
		typeof element.scrollHeight != 'undefined') {
			element.scrollTop = element.scrollHeight;
		}
	}
   // Create new Sigma instance in graph-container div (use your div name here) 
  	sigma.parsers.json(
		'out_graph.json', 
		{
			container: 'container',
			settings: 
			{
				defaultNodeColor: '#3febeb',
				defaultLabelColor: '#ffffff'
			}
		},
		function(s)
		{
			var i,
			nodes = s.graph.nodes(),
			len = nodes.length;

			for (i = 0; i < len; i++)
			{
				nodes[i].x = Math.random();
				nodes[i].y = Math.random();
				nodes[i].size = s.graph.degree(nodes[i].id);
				//nodes[i].color = nodes[i].id;//nodes[i].center ? '#333' : '#666#'
			}
			
			s.refresh();

			s.startForceAtlas2();
		}
	);
</script>
<table>
	<tr>
		<td width="30%">
			<form name="crawl_form" method="POST" action="webcrawler.php">
				<input width="60%" type="text" value="" name="crawl_url[]"/>
				<input width="60%" type="text" value="" name="crawl_url[]"/>
				<input width="20%" type="Submit" value="url ->" name="crawl_submit"/>
			</form>
			<form name="query_form" method="POST" action="webcrawler.php">
				<input type="text" value="" name="query"/>
				<input type="Submit" value="query ->" name="query_submit"/>
			</form>
			<select>
				<option value="1">1</option>
			</select>
		</td>
		<td width="70%">
			<textarea name="textareaName" rows="10" cols="80">
				<?php
	
					ob_implicit_flush(true);

					set_time_limit(0);

					$file_name = "out".$INSTANCE.".txt";
					$file = file($file_name);
					echo "Running: ".$cmd."\r\n";
					echo "Output[".count($file)."]:\n\n";

					// Print out the file
					for($i = 0/*count($file)-6*/; $i < count($file); $i++)
					{
						ob_start();
						echo $file[$i] . "\n";
						ob_end_flush();
						ob_flush();
						usleep(100000);
					}

				?>
			</textarea>
		</td>
	
	</tr>
</table>

</body>
</html>
