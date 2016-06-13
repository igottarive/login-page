/**
 * @file
 * JS file for the login system
 */
$(function() {
  $('.login').on('click', function() {
    var url = "template/login.html";
    if ($(this).hasClass('register')) url = "template/register.html";
    $('#popup').not('.open').load(url).addClass('open');
    return false;
  });

  $('body').parents().not('#popup, .login').on('click', function(e) {
    if($(e.target).closest('#popup').length == 0) {
      $('#popup').removeClass('open');
    }
  });
});
