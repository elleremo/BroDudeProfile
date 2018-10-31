var $ = jQuery.noConflict();
(function ($) {

    var BroDudeProfile_favorite_add =
        {
            selector: '.single__content-header-favorite-wrap',
            run: function () {
                var this_class = this;
                this_class.send(this_class.selector)
            },
            send: function (selector) {
                var this_class = this;
                $(selector).on('click', function () {
                    var url = $(this).attr('data-action');

                    if (!$(this).hasClass('process')) {
                        $(this).addClass('process');
                        $(this).find("[data-text]").text('отправка...');
                        this_class.get_json(url, $(this));
                    }

                });
            },
            get_json: function (url, elem) {


                $.getJSON(url, function (data) {

                    if (true === data.success) {
                        elem.html(data.data.html);
                    }
                    elem.removeClass('process');
                });
            }
        };
    $(document).ready(function () {
        BroDudeProfile_favorite_add.run();
    });

})(jQuery);