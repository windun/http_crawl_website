<?php
header("Cache-Control: no-cache, must-revalidate");
//header("Content-type: text/plain");

	$INSTANCE = 1;
	$OPT=0;
	//$stdout = fopen('php://stdout', 'w');
	if(!empty($_POST['crawl_url'][0]))
	{
		$OPT = 1;
		$crawl_url = $_POST['crawl_url'][0];
		$crawl_depth = $_POST['crawl_url'][1];
		$cmd = "./crawl $crawl_url $crawl_depth > out".$INSTANCE.".txt 2>&1 &";
		exec($cmd);
	}
	else if(!empty($_POST['query']))
	{
		$OPT = 2;
		$query = $_POST['query'];
		$cmd = "./crawl -c \"$query\" \"graph\" > out".$INSTANCE.".txt 2>&1 &";
		exec($cmd);
	}
	else if(!empty($_POST['delete_request']))
	{
		$OPT = 3;
		$cmd = "./crawl -c \"MATCH ()-[r]->(), (n) DELETE r,n\" \"graph\" > out".$INSTANCE.".txt 2>&1 &";
		exec($cmd);
	}
?>
<html>
<head>
<style type="text/css">
	#container {
		position: absolute;
		left: 300px;
		top: 0px;
		background-color: #ffffff;
		min-width: 700px;
		height: 100%;
		margin: auto;
	}
	BODY, TD, P {
		font-family: arial,helvetica,sans-serif;
		font-size: 10px;
		color: #333333;
	}
</style>	
</head>
<body style="background-color: #e9e9e9">
<table width="300px" id="side_panel">
	<tr>
		<td width="100%">
			<form name="crawl_form" method="post" action="webcrawler.php">
				<input size="17" type="text" value="" name="crawl_url[]"/>
				<input size="4" type="text" value="" name="crawl_url[]"/>
				<input size="10" type="Submit" value="crawl" name="crawl_submit"/>
			</form>
			<form name="query_form" method="post" action="webcrawler.php">
				<input size="20" type="text" value="" name="query"/>
				<input size="10" type="Submit" value="run query" name="query_submit"/>
			</form>
			<select>
				<option value="1">1</option>
			</select>
			<?php echo "[".$OPT."] ".$cmd; ?>
			<div id="content" style="border: 1px solid white; background-color: #ffffff; width: 280px; overflow: scroll;">
				<?php 
					include_once('console.php'); 
				?>
			</div>
		</td>	
	</tr>
	<tr>
		<td>
			<br>
			<form name="delete" method="post" action="webcrawler.php">
				<button name="delete_request" style="background-color: #ff6666"><font color="white"> DELETE </font></button>
			</form>
		</td>
	</tr>
</table>
<div id="container" style="box-shadow: 0px 10px 5px #888888;"></div>
</body>
<script src="jquery-2.1.1.min.js"></script>
<script src="sigma.min.js"></script>
<script src="sigma.parsers.json.min.js"></script>
<script src="sigma.layout.forceAtlas2.min.js"></script>
<script>

  	/*
		This section is for drawing the graph. It uses the Sigma.js 
		library.
	*/
  	sigma.parsers.json(
		'out_graph.json', 
		{
			container: 'container',
			settings: 
			{
				defaultNodeColor: '#3febeb',
				defaultLabelColor: '#333333'
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
	

	/*
		Set the size of the console. It doesn't like to behave
		so we have to set it this way.
	*/
	var graph_canvas = document.getElementById('container');
	var graph_height = graph_canvas.offsetHeight;	// get actual height using .offsetHeight
							// .height is undefined
	var contentDiv = document.getElementById('content');
	contentDiv.style.height = graph_height - 200;
	//contentDiv.scrollTop = contentDiv.scrollHeight;

	
	/*
		Auto scrolling - This will make sure the console moves
		as it is updated. However, we must make sure that it does
		not move when we are moused over.
	*/
	var hover_over = false;
	$('#content').mouseover(function () 
	{
		hover_over = true;
		$('#content').css("border", "1px solid green");
	});
	$('#content').mouseout(function () 
	{
		hover_over = false;
		$('#content').css("border", "1px solid white");
	});
	setInterval(refreshConsole, 50);
	function refreshConsole ()
	{
		if(!hover_over)
		{
			$('#content').load('console.php');
			//setTimeout("", 200);
			var objDiv = document.getElementById("content");
			objDiv.scrollTop = objDiv.scrollHeight;
		}
		else
		{

		}
		//document.write(hover_over);
	}
	//document.write(hover_over);
	
</script>
</html>
