$(document).ready(function() {
	createChart();
	updateChart();
	window.Highcharts.setOptions({
		lang: {
			months: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
			weekdays: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado']
		}
	});
  $(window).resize(function() {
    $('#chart_container').highcharts().setSize($(window).width()-40,$(window).height()-210-($("button[data-type='afp']").length===0?-25:0), true);
  });
  
  $('#fechas').daterangepicker({
    opens: "center",
    maxDate: moment(),
    locale: {
      format: 'YYYY-MM-DD'
    }
  }, 
  function(start, end) {
    $('#desde').val(start.format('YYYY-MM-DD'));
    $('#hasta').val(end.format('YYYY-MM-DD'));
    updateChart();
  });
});

var updating = false;

function updateChart() {
  if (window.updating) {
    return false;
  }
  
  window.updating = true;

	var seriesOptions = [];
	createChart();
	$('#afp_name').html('');
	$('#periodo').html('');
	$('.box-rentabilidad').remove();
	$.get('/api/fondos', {
    afp: $('#afp_selected').val(),
    fondo : $('#fondo_selected').val(),
    from : $('#desde').val(),
    until: $('#hasta').val(),
    type: $('#type').val(),
    hideFDS:1/*+$('#ocultarFDS').val()*/
  }, function(response) {
    var r = response.response;
		var c=0;
		var count = 1;/*names.length;*/
		var sel_tipo = $('#type').val();
		for(var i in r.fondos){
			var fondo = r.fondos[i];
			var color = 'red';
			if (parseFloat(fondo['variacion_real'])>=0) {
				color = 'green';
			}
			seriesOptions[c++]={name:fondo.descripcion,data:fondo.data,type:'spline',shadow:true};
			if (sel_tipo === 'porcentaje') {
				$('.details').append('<div class="box-rentabilidad '+(count===1?'box-center':'')+'" >\n\
					<label class="bold">'+fondo['descripcion']+'</label> <span class="'+color+'">'+fondo['variacion_real']+'%</span>\n\
					<label>Desde '+fondo['fecha_valor_inicial']+'</label>\n\
					<label>Hasta '+fondo['fecha_valor_final']+'</label>\n\
				</div>');
			} else if (sel_tipo === 'patrimonio') {
				$('.details').append('<div class="box-rentabilidad '+(count===1?'box-center':'')+'" >\n\
					<label class="bold">'+fondo['descripcion']+'</label> <span class="'+color+'">$'+fondo['variacion_real']+' ('+fondo['variacion_porcentual']+' %)</span>\n\
					<label>BB $'+fondo['valor_inicial']+' el '+fondo['fecha_valor_inicial']+'</label>\n\
					<label>BB $'+fondo['valor_final']+' el '+fondo['fecha_valor_final']+'</label>\n\
				</div>');
			} else {
				$('.details').append('<div class="box-rentabilidad '+(count===1?'box-center':'')+'" >\n\
					<label class="bold">'+fondo['descripcion']+'</label> <span class="'+color+'">$'+fondo['variacion_real']+' ('+fondo['variacion_porcentual']+' %)</span>\n\
					<label>$'+fondo['valor_inicial']+' el '+fondo['fecha_valor_inicial']+'</label>\n\
					<label>$'+fondo['valor_final']+' el '+fondo['fecha_valor_final']+'</label>\n\
				</div>');
			}
			$('#afp_name').html(r.afp);
			$('#periodo').html(r.periodo);
		}
		refreshChart(seriesOptions,r.description);
    window.updating = false;
	}, 'json');
	return false;
}

// create the chart when all data is loaded
function createChart(seriesOptions) {
	$('#chart_container').highcharts('StockChart', {
		chart: {
			backgroundColor: {
			   linearGradient: { x1: 0, y1: 0, x2: 1, y2: 1 },
			   stops: [[0, 'rgb(255, 255, 255)'],[1, 'rgb(240, 240, 255)']]
			},
			borderWidth: 1,
			plotBackgroundColor: 'rgba(255, 255, 255, .9)',
			plotShadow: true,
			plotBorderWidth: 1,
      height: $(window).height()-210-($("button[data-type='afp']").length===0?-25:0)
		},
		rangeSelector: {
			buttons: [
				{type: 'month',count:1,text:'1m'}, 
				{type: 'month',count: 3,text: '3m'}, 
				{type: 'month',count: 6,text: '6m'}, 
				{type: 'year',count: 1,text: '1y'}, 
				{type: 'all', text: 'All'}
			],
			inputEnabled: false,
			selected: 2
		},

		yAxis: {
			minorTickInterval: 'auto',
			lineColor: '#000',
			lineWidth: 1,
			tickWidth: 1,
			tickColor: '#000',
			labels: {
				formatter: function() { return ($('#type').val()!=='porcentaje' ? '$':'%') + this.value;}
			}
		},
		title : {text : 'Fondos AFP - http://www.fondosafp.com'},
		tooltip: {
			xDateFormat: '%A %e de %B del %Y',
			pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>'+($('#type').val()!=='porcentaje' ? '$':'')+'{point.y}'+($('#type').val()==='porcentaje' ? '%':'')+'</b><br/>',
			valueDecimals: 2
		},
		series: seriesOptions
	});

	var chart = $('#chart_container').highcharts();
	chart.showLoading();
	return false;
}

function refreshChart(seriesOptions, subtitle){
	createChart(seriesOptions);
	var chart = $('#chart_container').highcharts();
	chart.setTitle(chart.options.title,{text:subtitle});
	chart.hideLoading();
	return false;
}

function selectAFP(id) {
  var afp = $('#afp_selected').val();
  if (id === afp) {
    return false;
  }
  $("button[data-type='afp']").removeClass('active');
  $("button[data-type='afp'][data-id='"+id+"']").addClass('active');
  $('#afp_selected').val(id);
  updateChart();
}

function selectFondo(id) {
  var fondos = $('#fondo_selected').val().split(",");
  if ($.inArray(id.toString(), fondos) !== -1) {
    if (fondos.length > 1) {
      $("button[data-type='fondo'][data-id='"+id+"']").removeClass('active');
      fondos.splice(fondos.indexOf(id.toString()), 1);
    }
  } else {
    $("button[data-type='fondo'][data-id='"+id+"']").addClass('active');
    fondos.push(id);
  }
  $('#fondo_selected').val(fondos.toString());
  updateChart();
}

function selectType(name) {
  var type = $('#type').val();
  if (name === type) {
    return false;
  }
  $("button[data-type='type']").removeClass('active');
  $("button[data-type='type'][data-name='"+name+"']").addClass('active');
  $('#type').val(name);
  updateChart();
}