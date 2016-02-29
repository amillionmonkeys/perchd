// @codekit-prepend "../bower_components/jquery/dist/jquery.min.js", "../bower_components/responsive-nav/responsive-nav.js", "modules/modernizr-custom.js";

// @codekit-prepend "modules/form.js

if ($('.main-nav').length>0) {
    responsiveNav(".main-nav");
}

$('.widget--subscribe input').focus(function(){
	$(this).parent().siblings('.form-name').slideDown();
});
