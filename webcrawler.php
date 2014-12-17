<?php
	exec('./crawl -c "MATCH (s)-[r]->(t) RETURN s,r,t" "graph"');
?>
<html>
<head>
<style type="text/css">
  #container {
    max-width: 100%;
    height: 80%;
    margin: auto;
  }
</style>	
</head>
<body>
<div id="container"></div>
<script src="sigma.min.js"></script>
<script src="sigma.parsers.json.min.js"></script>
<script>
   // Create new Sigma instance in graph-container div (use your div name here) 
  	sigma.parsers.json(
		'out_graph.json', 
		{
			container: 'container',
			settings: 
			{
				defaultNodeColor: '#ec5148'
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
				nodes[i].color = nodes[i].center ? '#333' : '#666#'
			}
			
			s.refresh();

			s.startForceAtlas2();
		}
	);
</script>
<?php 
	echo "Hello";
?>
</body>
</html>
