$(document).ready(function(){
  try {
    new Chart($("#cambios_chart"), {
      type: 'line',
      borderColor: '#7CB5EC',

      data: {
        datasets: [{
          label: 'Ganancias acumuladas por cuota',
          data: window.cambios_chart,
          fill: false,
          lineTension: 0.1,
          backgroundColor: "#7FB5E9",
          borderColor: "#7FB5E9",
          borderCapStyle: 'butt',
          borderDash: [],
          borderDashOffset: 0.0,
          borderJoinStyle: 'miter',
          pointBorderColor: "#7FB5E9",
          pointBackgroundColor: "#fff",
          pointBorderWidth: 1,
          pointHoverRadius: 5,
          pointHoverBackgroundColor: "#7FB5E9",
          pointHoverBorderColor: "#7FB5E9",
          pointHoverBorderWidth: 2,
          pointRadius: 1,
          pointHitRadius: 10
        }]
      },
      options: {
        scales: {
          xAxes: [{
            display: false,
            type: 'linear',
            position: 'bottom'
          }]
        },
        tooltips: {
          callbacks: {
            title: function(data) {
              return window.cambios_labels[data[0].index];
            },
            label: function(data) {
              return '$' + data.yLabel + ' ($'+window.cambios_values[data.index]+')';
            }
          }
        }
      }
    });
  } catch(e) {
    console.log(e.message);
  }

  $(document).ready(function(){
    $('#changes_tabs a[href="'+window.location.hash+'"]').tab('show');
    
    $('#fecha').daterangepicker({
      showDropdowns: true,
      singleDatePicker: true,
      opens: "center",
      maxDate: moment(),
      locale: {
        format: 'YYYY-MM-DD',
        firstDay: 1
      }
    }, 
    function(start, end) {
      $('#fecha').val(start.format('YYYY-MM-DD'));
      console.log(end);
    });
  });

});

function agregarCambio() {
  if ($('#fecha').val() === '') {
    return false;
  }
  if ($('#desde').val() === '') {
    return false;
  }
  if ($('#hasta').val() === '') {
    return false;
  }
  
  return true;
}

function removeCambio(id) {
  $.post('/changes/remove', {id: id }, function(resp) {
    window.disableButtons();
    if (resp.response.ok) {
      $('#cambio-'+id).addClass("danger").fadeOut(1000);
      window.location.reload();
    } else {
      alert(resp.response.message);
    }
    window.enableButtons();
  }, 'json');
}