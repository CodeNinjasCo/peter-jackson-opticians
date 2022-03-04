jQuery(document).ready(function ($) {

    setTimeout(function () {
        $topBarHeight = $('#Top_bar').height() + 'px';
        $('body').css('margin-top', $topBarHeight);
    }, 1000);

}(jQuery));