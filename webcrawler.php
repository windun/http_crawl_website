<?php
//header("Content-type: text/plain");

	$INSTANCE = 1;
	$stdout = fopen('php://stdout', 'w');
	if(isset($_POST['crawl_url']))
	{
		$crawl_url = $_POST['crawl_url'];
		$crawl_depth = $_POST['crawl_depth'];
		//exec("./crawl $crawl_url 0"." > out".$INSTANCE.".txt &");
		$cmd = "./crawl $crawl_url $crawl_depth";
	}
	else if(isset($_POST['query']))
	{
		$query = $_POST['query'];
		//exec("./crawl -c \"$query\" \"graph\""." > out".$INSTANCE.".txt &");
		$cmd = "./crawl -c \"$query\" \"graph\"";
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
				<input width="60%" type="text" value="" name="crawl_url"/>
				<input width="60%" type="text" value="" name="crawl_depth"/>
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
		<?php
			// Turn off output buffering
			ini_set('output_buffering', 'off');
			// Turn off PHP output compression
			ini_set('zlib.output_compression', false);
		
			//Flush (send) the output buffer and turn off output buffering
			//ob_end_flush();
			while (@ob_end_flush());
		
			// Implicitly flush the buffer(s)
			ini_set('implicit_flush', true);
			ob_implicit_flush(true);

			//prevent apache from buffering it for deflate/gzip
			header("Content-type: text/plain");
			header('Cache-Control: no-cache'); // recommended to prevent caching of event data.

			for($i = 0; $i < 1000; $i++)
			{
				echo ' ';
			}
		
			ob_flush();
			flush();

			/// Now start the program output

			echo "Program Output";
			system($cmd);

			ob_flush();
			flush();

		?>
		</td>
	
	</tr>
</table>

</body>
</html>
