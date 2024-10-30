var $ = jQuery.noConflict();

$('a.solution').on('click', function (e) {
  e.preventDefault();
  $('li#tab-link-problem').removeClass('active');
  $('#tab-panel-problem').css('display', 'none');

// Set our desired link/panel
  $('#tab-link-solution').addClass('active');
  $('#tab-panel-solution').css('display', 'block');

// Force click on the Help tab
  $('#tab-link-solution').click();
});