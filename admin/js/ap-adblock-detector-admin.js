(function( $ ) {
	'use strict';

	/**
	* All of the code for your admin-facing JavaScript source
	* should reside in this file.
	*
	* Note: It has been assumed you will write jQuery code here, so the
	* $ function reference has been prepared for usage within the scope
	* of this function.
	*
	* This enables you to define handlers, for when the DOM is ready:
	*
	* $(function() {
	*
	* });
	*
	* When the window is loaded:
	*
	* $( window ).load(function() {
	*
	* });
	*
	* ...and/or other possibilities.
	*
	* Ideally, it is not considered best practise to attach more than a
	* single DOM-ready or window-load handler for a particular page.
	* Although scripts in the WordPress core, Plugins and Themes may be
	* practising this, we should strive to set a better example in our own work.
	*/

	function firstChartLoad() {
		$('#reload_button').on('click', function(){ refreshData(); });
		refreshData();
	}

	function refreshData() {
		if(jQuery('#ap-abd-from').datepicker('getDate') > jQuery('#ap-abd-to').datepicker('getDate')) {
			alert('Start date cannot be greater than end date');
			jQuery('#ap-abd-from').focus();
			return;
		}
		$('#reload_spinner').addClass('is-active');
		$('#reload_button').addClass('disabled').attr('disabled', 'disabled');
		$.post(ap_adblock_detector.ajax_url, {
			action: 'ap_adblock_detector_get_counts',
			period: $('#ap-abd-graph-period').val(),
			from: $('#ap-abd-from').val(),
			to: $('#ap-abd-to').val()
		}, function(data){
			$('#reload_spinner').removeClass('is-active');
			if(data) {
				$('#reload_button').removeClass('disabled').removeAttr('disabled');
				drawBasic(data);
				$('.no-data').hide();
				$('.linechart_material_div').show();
			}
			else {
				$('.linechart_material_div').hide();
				$('.no-data').show();
			}
		}, 'json');
	}

	var jqplotGraph;
	var _chart_data;
	function drawBasic(chart_data) {
		_chart_data = chart_data;

		var xAxisFormatString;

		var period = jQuery('#ap-abd-graph-period').val();

		switch(period) {
			case 'hourly' :
				xAxisFormatString = '%Y-%m-%d %Hh';
				break;
			case 'daily':
				xAxisFormatString = '%Y-%m-%d';
				break;
			case 'weekly' :
				xAxisFormatString = '%Y-%m-%d';
				break;
			case 'monthly' :
				xAxisFormatString = '%Y-%m';
				break;
		}

		if (typeof jqplotGraph !== 'undefined') {
			jqplotGraph.destroy();
		}

		jqplotGraph = $.jqplot('linechart_material', [_chart_data], {
			title:'PAGEVIEWS AFFECTED BY AD BLOCKER',
			axes:{
				xaxis:{
					label: 'DATE',
					labelOptions: {
						textColor: '#eee'
					},
					numberTicks: 5,
					renderer:$.jqplot.DateAxisRenderer,
					tickOptions: {
						showGridline : false,
						formatString: xAxisFormatString
					}
				},
				yaxis: {
					label: 'AFFECTED PAGEVIEWS',
					labelOptions: {
						textColor: '#eee'
					},
					min: 0,
					// numberTicks: 10,
					// tickInterval: 1,
					tickOptions: {
						formatString: '%d'
					},
					labelRenderer: $.jqplot.CanvasAxisLabelRenderer
				}
			},
			highlighter: {
				show: true,
				sizeAdjust: 7,
				useAxesFormatters: true,
				formatString: 'Date: %s<br>Pageviews: <span style="color:#EB4C51">%d</span>'
			},
			seriesDefaults: {
				rendererOptions: {
					smooth: true
				}
			},
			seriesColors: ["#EB4C51"],
			series:[
				{
					lineWidth: 4,
					markerOptions:{
						style:'filledCircle',
						size: 12
					}
				}
			],
			grid: {
				backgroundColor: '#383839'
			}
		});
	}

	function debounce(func, wait, immediate) {
		var timeout;
		return function() {
			var context = this, args = arguments;
			var later = function() {
				timeout = null;
				if (!immediate) func.apply(context, args);
			};
			var callNow = immediate && !timeout;
			clearTimeout(timeout);
			timeout = setTimeout(later, wait);
			if (callNow) func.apply(context, args);
		};
	};

	$(document).ready(function(){

		var effResize = debounce(function() {
			if(typeof jqplotGraph !== 'undefined') {
				// jqplot's replot had some odd behavior
				drawBasic(_chart_data);
			}
		}, 250);

		window.addEventListener('resize', effResize);

		var fromDate = jQuery('#ap-abd-from').datepicker({
			dateFormat : 'yy-mm-dd'
		});
		var toDate = jQuery('#ap-abd-to').datepicker({
			dateFormat : 'yy-mm-dd'
		});

		var curDate = new Date();
		var pastDate = new Date();

		var period = jQuery('#ap-abd-graph-period').val();
		switch(period) {
			case 'yearly':
				pastDate.setDate(curDate.getDate()-365);
				break;
			case 'montly':
				pastDate.setDate(curDate.getDate()-30);
				break;
			case 'weekly':
				pastDate.setDate(curDate.getDate()-7);
				break;
			default:
				pastDate.setDate(curDate.getDate()-1);
		}

		$(fromDate).datepicker('setDate', pastDate);
		$(toDate).datepicker('setDate', curDate);

		firstChartLoad();
	});


})( jQuery );
