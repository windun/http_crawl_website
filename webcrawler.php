<?php
header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1.
header('Pragma: no-cache'); // HTTP 1.0.
header('Expires: 0'); // Proxies.
//header("Content-type: text/plain");

	$INSTANCE = date('Y-m-d-H-i-s');
	$OPT=0;
	//$stdout = fopen('php://stdout', 'w');
	if(!empty($_POST['crawl_url'][0]))
	{
		$OPT = 1;
		$crawl_url = $_POST['crawl_url'][0];
		$crawl_depth = $_POST['crawl_url'][1];
		$cmd = "./crawl $crawl_url $crawl_depth > out".$INSTANCE.".txt 2>&1 &";
		exec("rm out*.txt out_graph.json; $cmd");
	}
	else if(!empty($_POST['query']))
	{
		$OPT = 2;
		$query = $_POST['query'][0];
		$query_format = $_POST['query'][1];
		$cmd = "./crawl -q \"$query\" \"$query_format\" > out".$INSTANCE.".txt 2>&1 &";
		exec("rm out*.txt out_graph.json; $cmd");
	}
	else if(!empty($_POST['s_values']))
	{
		$OPT = 4;
		$EDGENODE = $_POST['s_values'][0];
		$IDLABELPROPERTIES = $_POST['s_values'][1];
		$PROPERTY = $_POST['s_values'][2];
		$VALUE = $_POST['s_values'][3];
		$cmd = "rm out*.txt out_graph.json; ./crawl -pq $EDGENODE $IDLABELPROPERTIES '$PROPERTY' '$VALUE' > out".$INSTANCE.".txt 2>&1 &";
		exec($cmd);
	}
?>
<html>
<head>
<style type="text/css">
	#container {
		position: absolute;
		left: 330px;
		top: 0px;
		background-color: #ffffff;
		min-width: 700px;
		height: 100%;
		margin: auto;
	}
	BODY, TD, P, DIV {
		font-family: arial,helvetica,sans-serif;
		font-size: 10px;
		color: #333333;
	}
</style>	
</head>
<body style="background-color: #e9e9e9">
<table width="300px" id="side_panel">
	<tr>
		<div id="instance_value"><?php echo $INSTANCE; ?></div>
		<div id="instance_value_recv"></div>
		<td width="100%">
			<div id="raw_queries" style="padding: 2px; box-shadow: 0px 2px 5px #888888;">
				<form name="crawl_form" method="post" action="webcrawler.php">
					<input size="17" type="text" value="" name="crawl_url[]"/>
					<input size="4" type="text" value="" name="crawl_url[]"/>
					<input size="10" type="Submit" value="crawl" name="crawl_submit"/>
				</form>
				<form name="query_form" method="post" action="webcrawler.php">
					<input size="15" type="text" value="" name="query[]"/>
					<select name="query[]">
						<option value="row">row</option>
						<option value="graph">graph</option>
					</select>
					<input size="9" type="Submit" value="run query" name="query_submit"/>
				</form>
			</div>
			<br>
			<div id="pieced_search" style="padding: 2px;box-shadow: 0px 2px 5px #888888;">
				<form name="s_values" method="post" action="webcrawler.php">
					<select name="s_values[]">
						<option value="nodes">nodes</option>
						<option value="edges">edges</option>
					</select>
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					property &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					value
					<br>
					<select name="s_values[]">
						<option value="''"></option>
						<option value="id">id</option>
						<option value="label">label</option>
						<option value="properties">properties</option>
					</select>
					<input size="10" type="text" value="" name="s_values[]"/>
					<input size="10" type="text" value="" name="s_values[]"/>
					<br>
					<input size="10" type="Submit" value="run query" name="s_values_submit"/>
				</form>
			</div>
			<br>
			<div style="padding: 2px;border: 1px solid white; width: 300px;box-shadow: 0px 2px 5px #888888;">
				<p style="padding:1px;">
					<?php echo "[$OPT] $cmd"; ?>
				</p>
				<div id="content" style="padding: 2px;border: 1px solid white; background-color: #ffffff; overflow: scroll;">
					<?php 
						echo "[]";
						include_once('console.php'); 
					?>
				</div>
			</div>

		</td>	
	</tr>
	<tr>
		<td>
 			<button onclick="loadGraph()">REFRESH GRAPH</button> 
		</td>
	</tr>
</table>
<div id="container" style="box-shadow: 0px 10px 5px #888888;"></div>
<div id="right_panel" style="padding: 2px; position: absolute; left: 1042px; top: 8px;box-shadow: 0px 2px 5px #888888;">
	Selection Information
	<table width="300px">
		<tr>
			<div id="g_info" style="border: 1px solid white; background-color: #ffffff; width: 300px; height: 70px; overflow: scroll;">

			</div>
		</tr>
		<br>
		<button id="g_detail_graph" style="border: 1px solid white; max-width: 100px;">GRAPH</button><button id="g_detail_row" style="border: 1px solid white;max-width: 100px;">ROW</button>
		<tr>
			<div id="g_detail" style="border: 1px solid white; background-color: #ffffff; width: 300px; height: 500px; overflow: scroll;">

			</div>
		</tr>
	</table>
</div>
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
	var sigma_instance;
  	function loadGraph () 
	{
		//http://stackoverflow.com/questions/22543083/remove-all-the-instance-from-sigma-js
		var g = document.querySelector('#container');
		var p = g.parentNode;
		p.removeChild(g);
		var c = document.createElement('div');
		c.setAttribute('id', 'container');
		p.appendChild(c);

		//$('#container').empty();
		sigma.parsers.json(
			'out_graph.json', 	// must adjust permissions on this file,
			{			// or it will not load
				container: 'container',
				settings: 
				{
					defaultNodeColor: '#3febeb',
					defaultLabelColor: '#333333'
				}
			},
			function(s)
			{
				sigma_instance = s;
				var i,
				nodes = s.graph.nodes(),
				len = nodes.length;
				var force_on = true;

				for (i = 0; i < len; i++)
				{
					nodes[i].x = Math.random();
					nodes[i].y = Math.random();
					nodes[i].size = 1 + s.graph.degree(nodes[i].id);
					//nodes[i].color = nodes[i].id;//nodes[i].center ? '#333' : '#666#'
				}
			
				s.refresh();
				s.startForceAtlas2();

				s.bind('clickNode', function(e) {
					$('#g_info').html(
						"id: " + e.data.node.id + "<br>" +
						"labels:" + e.data.node.label);
				});

				s.bind('clickStage', function(e) {
					if (force_on)
					{
						force_on = false;
						s.stopForceAtlas2();
					}
					else
					{
						force_on = true;
						s.startForceAtlas2();
					}
				});
			}
		);
	}
	loadGraph();

	/*
		Set the size of the console. It doesn't like to behave
		so we have to set it this way.
	*/
	var graph_canvas = document.getElementById('container');
	var graph_height = graph_canvas.offsetHeight;	// get actual height using .offsetHeight
							// .height is undefined
	var contentDiv = document.getElementById('content');
	contentDiv.style.height = graph_height - 280;

	
	/*
		Auto scrolling - This will make sure the console moves
		as it is updated. However, we must make sure that it does
		not move when we are moused over.
	*/
	/*
	$('#container').mouseover(function()
	{
		sigma_instance.stopForceAtlas2();
	});*/
	var hover_over = false;
	$('#content').mouseover(function () 
	{
		hover_over = true;
		$('#content').css("border", "1px dashed green");
	});
	$('#content').mouseout(function () 
	{
		hover_over = false;
		$('#content').css("border", "1px solid white");
	});
	
	var g_detail_mode = "graph";
	var hover_over_g_detail = false;
	$('#g_detail').mouseover(function () 
	{
		hover_over_g_detail = true;
		$('#g_detail').css("border", "1px dashed black");
	});
	$('#g_detail').mouseout(function () 
	{
		hover_over_g_detail = false;
		$('#g_detail').css("border", "1px solid white");
	});

	$('#g_detail_graph').click(function () 
	{
		g_detail_mode = "graph";
		refreshGraphInfo();
		$('#g_detail_graph').css("border", "1px solid green");
		$('#g_detail_row').css("border", "1px solid white");
	});
	$('#g_detail_row').click(function () 
	{
		g_detail_mode = "row";
		refreshGraphInfo();
		$('#g_detail_graph').css("border", "1px solid white");
		$('#g_detail_row').css("border", "1px solid red");
	});

	setInterval(refreshInfo, 100);
	function refreshInfo ()
	{
		if(!hover_over){refreshConsole();}
		//if(!hover_over_g_detail){refreshGraphInfo();}
	}
	function refreshConsole ()
	{
		var instance_value = $('#instance_value').text();
		$('#content').load('console.php', {INSTANCE:instance_value});
		var objDiv = document.getElementById("content");
		objDiv.scrollTop = objDiv.scrollHeight;
	}

	function refreshGraphInfo ()
	{
		if(g_detail_mode == "graph")
		{
			$('#g_detail').load('out_graph.php?');
			$('#g_detail').css("border", "1px solid green");
		}
		else if (g_detail_mode == "row")
		{
			$('#g_detail').load('out_row.php');
			$('#g_detail').css("border", "1px solid red");
		}
		//var objDiv = document.getElementById("g_detail");
		//objDiv.scrollTop = objDiv.scrollHeight;
	}
	
	// http://stackoverflow.com/questions/16991341/js-json-parse-file-path
	
</script>
</html>
