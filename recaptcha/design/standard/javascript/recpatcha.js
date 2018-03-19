var onloadCallback = function() {
  grecaptcha.render( 'recaptcha', {
    'sitekey' : 'your-public-site-key'
  });
};