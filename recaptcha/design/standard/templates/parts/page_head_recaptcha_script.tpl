{* recaptcha v2 script in head part *}
{literal}
<script language="JavaScript" type="text/javascript">
<!--
var onloadCallback = function() {
  grecaptcha.render( 'recaptcha', {
    'sitekey' : {/literal}{concat( "'", get_public_key(), "'" )}{literal},
    'theme' : {/literal}{concat( "'", ezini( 'Display', 'Theme', 'recaptcha.ini.append.php' ), "'" )}{literal},
  });
};
//-->
</script>
{/literal}