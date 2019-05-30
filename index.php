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
			["2015-12-25", 512.53],
			["2015-12-26", 511.83],
			["2015-12-27", 511.22],
			["2015-12-28", 510.35],
			["2015-12-29", 510.53],
			["2015-12-30", 511.43],
			["2015-12-31", 511.50],
			["2016-01-01", 511.32],
			["2016-01-02", 511.70],
		],[
			["2015-12-25", 514.88],
			["2015-12-26", 514.98],
			["2015-12-27", 515.30],
			["2015-12-28", 515.72],
			["2015-12-29", 515.86],
			["2015-12-30", 515.98],
			["2015-12-31", 515.33],
			["2016-01-01", 514.29],
			["2016-01-02", 514.87]
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