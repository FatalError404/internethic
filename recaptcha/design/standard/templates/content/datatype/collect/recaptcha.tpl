{* content datatype collect recaptcha template *}
{def $recaptcha_version = ezini( 'VersionSettings','Current','recaptcha.ini' )}
{if $recaptcha_version|eq( 1 )}
  <script src="https://www.google.com/recaptcha/api.js" async defer></script>
  {def $lang=ezini('Display','OverrideLang','recaptcha.ini')}
  {if $lang|eq('')}{set $lang=$attribute.language_code|extract_left(2)}{/if}
  <script type="text/javascript">
    var RecaptchaTheme='{ezini('Display','Theme','recaptcha.ini')}';
    var RecaptchaLang='{$lang}';
    {literal}
    var RecaptchaOptions = {
    theme: RecaptchaTheme,
    lang: RecaptchaLang
    };
    {/literal}
  </script>
  {recaptcha_get_html()}
{elseif $recaptcha_version|eq(2)}
  {* inclusion js recaptcha *}
  {include uri='design:parts/page_head_recaptcha_script.tpl'}
  <div id="recaptcha"></div>
  {include uri='design:parts/recaptcha_script.tpl'}
{/if}