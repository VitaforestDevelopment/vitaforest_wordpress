jQuery(".wptn-options .wptn-options__header").click(function () {
    jQuery(this).parent().children(".wptn-options__body").slideToggle('fast');
    jQuery(this).children(".wptn-arrow").toggleClass('open');
});