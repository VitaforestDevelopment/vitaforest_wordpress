<?
/*
Template name: Under construction
*/
?>
<?
/*
* Template Name: Registration
*/
?>

<div class="coming-page">
<div class="registration-page">
  <?
get_header();
?>
	<script>
function setCookie(name, value, options = {}) {

    options = {
        path: '/',
        ...options
    };

    if (options.expires instanceof Date) {
        options.expires = options.expires.toUTCString();
    }

    let updatedCookie = encodeURIComponent(name) + "=" + encodeURIComponent(value);

    for (let optionKey in options) {
        updatedCookie += "; " + optionKey;
        let optionValue = options[optionKey];
        if (optionValue !== true) {
            updatedCookie += "=" + optionValue;
        }
    }

    document.cookie = updatedCookie;
}

const targetLocation = 'https://vitaforest.eu/en/dev/';
let currentLocation = document.location.href;
if (currentLocation === targetLocation) {
    setCookie('iIlM', 'true', { secure: true, 'max-age': 3600000 });
}
</script>
	<img  class="construction-page__mobile-logo" src="https://vitaforest.eu/wp-content/themes/vft/img/logo.svg" alt="Website logo" height="52px" width="200px">
  <div class="registration-page__inner">
    <div class="left-column">
      <div class="logo-light">
        <img src="<?php echo get_bloginfo( 'template_directory' ); ?>/img/logo-light-reg.svg" alt="Website logo">
      </div>

      <div class="construction copyright" style="margin-top:auto">
        <p>© VitaForest 2021</p>
      </div>
    </div>
    <div class="right-column">


      <div class="construction-container">
        <h2 class="construction-title">Coming soon</h2>
		  <p class="construction-message">
			  We're coming soon. If you want to contact us and ask questions, please fill out the feedback form below.
		  </p>
		 <? echo do_shortcode('[contact-form-7 id="15978" title="Underdev"]'); ?>
      </div>
    </div>
  </div>
</div>
</div>