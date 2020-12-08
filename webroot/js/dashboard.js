$(document).ready(function(){
  $('#dashboard_tabs a[href="'+window.location.hash+'"]').tab('show');
  
  $(window).on('hashchange', function() {
    $('#dashboard_tabs a[href="'+window.location.hash+'"]').tab('show');
  });
  
  $('#dashboard_tabs a').click(function(e){
    window.location = window.location.pathname+$(this).attr('href');
  });
  
  $('#afp_link').editable({
    title: 'Seleccione su AFP',
    value: afpId,    
    source: afps,
    savenochange: true,
    ajaxOptions: {
        dataType: 'json'
    }
  }).on('save', function(e, params) {
     $.post('/users/update-afp', {afpId: params.newValue }, function(resp) {
      if (resp.result.ok) {
        window.location.reload();
      } else {
        alert(resp.result.message);
      }
    }, 'json');
  });
});
