var $ = jQuery.noConflict();
(function ($) {

    var BroDudeProfile_favorite_add_no_priv =
        {
            selector: '.single__content-header-favorite-wrap',
            run: function () {
                var this_class = this;
                this_class.action(this_class.selector)
            },
            action: function (elem) {
                $(elem).on('click', function () {
                    window.location.hash = 'login';
                    $(window).trigger('open_login-form');
                });
            }
        };
    $(document).ready(function () {
        BroDudeProfile_favorite_add_no_priv.run();
    });

})(jQuery);