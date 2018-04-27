var barEl = "demo-sparkline-bar";
var barValues = [40,32,65,53,62,55,24,67,45,70,45,56,34,67,76,32,65,53,62,55,24,67,45,70,45,56,70,45,56,34,67,76,32,65,53,62,55];
var barValueCount = barValues.length;
var barSpacing = 1;
var salesSparkline = function(element_id,values){
    $("#"+element_id).sparkline(values, {
        type: 'bar',
        height: 40,
        barWidth: Math.round(($("#"+element_id).parent().width() - ( values.length - 1 ) * barSpacing ) / values.length),
        barSpacing: barSpacing,
        zeroAxis: false,
        tooltipChartTitle: 'Daily Sales',
        tooltipSuffix: ' Sales',
        barColor: 'rgba(255,255,255,.7)'
    });
};
$(document).ready(function () {
    salesSparkline(barEl, barValues);
});