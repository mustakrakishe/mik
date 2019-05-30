<?php require 'php/common/head.php'; ?>
<script src='js/anychart/anychart-core.min.js'></script>
<script src='js/anychart/anychart-stock.min.js'></script>
<script src='js/anychart/anychart-exports.min.js'></script>

<style>
#container{
    width: 600px;
    height: 400px;
}
</style>

<h1>Главная</h1>

<div id="container"></div>

<script>
	anychart.onDocumentReady(function () {
		
		var arr = [[
			["2015-12-25 00:00:00", 512.53],
			["2015-12-25 00:00:01", 511.83],
			["2015-12-25 00:00:02", 511.22],
			["2015-12-25 00:00:03", 510.35],
			["2015-12-25 00:00:04", 510.53],
			["2015-12-25 00:00:05", 511.43],
			["2015-12-25 00:00:06", 511.50],
			["2015-12-25 00:00:07", 511.32],
			["2015-12-25 00:00:08", 511.70],
		],[
			["2015-12-25 00:00:00", 514.88],
			["2015-12-25 00:00:01", 514.98],
			["2015-12-25 00:00:02", 515.30],
			["2015-12-25 00:00:03", 515.72],
			["2015-12-25 00:00:04", 515.86],
			["2015-12-25 00:00:05", 515.98],
			["2015-12-25 00:00:06", 515.33],
			["2015-12-25 00:00:07", 514.29],
			["2015-12-25 00:00:08", 514.87]
		]];
		
		var chart = anychart.stock();
		chart.title('AnyStock Basic Sample');
		chart.container('container');
		chart.draw();

		var firstPlot = chart.plot(0);
		
		arr.forEach(function(channel, channelNum, channels){
			var dataTable = anychart.data.table(0);
			dataTable.addData(channels[channelNum]);
    		firstPlot.line(dataTable.mapAs({value: 1}));
		})
	});
</script>

<?php require 'php/common/foot.php'; ?>