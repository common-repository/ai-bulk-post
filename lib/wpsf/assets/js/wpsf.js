jQuery(document).ready(function($) {
    $('#wpsf-settings-tabs-wrapper .nav-tab').click(function() {
      // clear all active class
      $('.wpsf-settings-form-page').removeClass('active');

      // Get href value
      var targetTab = $(this).attr('href');
      
      // add active class
      $(targetTab).addClass('active');
      
      // Clear all active class
      $('#wpsf-settings-tabs-wrapper .nav-tab').removeClass('nav-tab-active');

      // Add active class
      $(this).addClass('nav-tab-active');
    });
});