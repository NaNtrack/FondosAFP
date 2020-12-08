$(document).ready(function(){
  $('[data-toggle="tooltip"]').tooltip();
  //$('select').selectmenu();
  $('#modal-afpfondo').modal({
    show: true,
    keyboard: false,
    backdrop: 'static'
  });
  $('#modal-email').modal({
    show: true,
    keyboard: false,
    backdrop: 'static'
  });
  function resizeElements() {
    $('.tab-content').css('min-height',($(window).height()-180));
  }

  $(window).resize(function() {
    resizeElements();
  });
  resizeElements();
});

window.disableButtons = function() {
  setTimeout(function() {
    $('button, input[type=submit]').attr('disabled', true);
  }, 10);
};
window.enableButtons = function() {
  setTimeout(function() {
    $('button, input[type=submit]').attr('disabled', false);
  }, 10);
};
  
function saveUserPreferences() {
  window.disableButtons();
  $.post('/users/save-preferences', {
      afp: $('#afp').val(),
      fondo: $('#fondo').val()
    }, function(resp) {
      if (resp.result.ok) {
        $('#modal-afpfondo').modal('hide');  
        window.location.reload();
      }
      else {
        alert(resp.result.message);
      }
      window.enableButtons();
    }, 'json');
}

function saveEmail() {
  window.disableButtons();
  $.post('/users/save-email', {
      email: $('#user_email').val()
    }, function(resp) {
      if (resp.result.ok) {
        $('#modal-email').modal('hide');  
        window.location.reload();
      }
      else {
        alert(resp.result.message);
      }
      window.enableButtons();
    }, 'json');
}
