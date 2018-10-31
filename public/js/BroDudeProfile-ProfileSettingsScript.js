var $ = jQuery.noConflict();
(function ($) {

    var BroDudeProfileProfileSettingsScript =
        {
            run: function () {
                var this_class = this;
                this_class.submit('.profile__settings');
            },
            submit: function (selector) {
                $(selector).on(
                    'submit',
                    (
                        function (e) {
                            e.preventDefault();

                            var form_method = $(this).attr('method');
                            var form_action = $(this).attr('action');
                            var form_elem = $(this);
                            var form_data = new FormData(this);

                            $.ajax({
                                type: form_method,
                                url: form_action,
                                data: form_data,
                                contentType: false,
                                cache: false,
                                processData: false,

                                beforeSend: function (jqXHR, status) {
                                    form_elem.find("[type='submit']").attr('disabled', 'disabled');
                                },
                                success: function (json) {

                                    $(".profile__settings-row.message").html(json.data.html);
                                }
                                ,
                                complete: function (jqXHR, status) {
                                    form_elem.find("[type='submit']").removeAttr('disabled');
                                }
                            });
                        }
                    )
                );
            }
        };
    $(document).ready(function () {
        BroDudeProfileProfileSettingsScript.run();
    });

})(jQuery);