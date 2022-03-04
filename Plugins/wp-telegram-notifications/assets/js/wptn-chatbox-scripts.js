jQuery(document).ready(function ($) {
    // click event
    var clicked = 0;

    jQuery(".wptn-button-toggle").click(function () {
        jQuery(".wptn-chat-wrapper").toggleClass('open');
    });
    jQuery(".close-button").click(function () {
        jQuery(".wptn-chat-wrapper").removeClass('open');
    });
    jQuery(".submit-message").click(function (e) {

        e.preventDefault();

        var time = new Date();
        const message = jQuery("#message-input").val();
        const user_phone = jQuery(".wptn-user-info .user-info-form__body form #user-phone").val();
        const user_id = jQuery(".wptn-user-info .user-info-form__body form #user-id").val();

        if (message && clicked === 0) {
            clicked = 1;
            const message_object = '<div style="display: none;" class="message message--user">\n' +
                '                    <h6 class="name">' + wptn_chat_vars.chat_box_you + '</h6>\n' +
                '                    <p class="message-text">' + message + '</p>\n' +
                '                    <span class="date">' + time.getHours() + ':' + time.getMinutes() + '</span>\n' +
                '                </div>';

            data_message = Array();
            data_message['user_tg_id'] = user_id;
            data_message['user_tg_phone'] = user_phone;
            data_message['user_message'] = message;

            var data_obj = Object.assign({}, data_message);
            var ajax = $.ajax({
                type: 'POST',
                url: wptn_chat_vars.chat_box_ajaxurl,
                data: data_obj
            });
            ajax.fail(function (data) {

                var message = null;

                if (typeof (data.text) != "undefined" && data.text !== null) {
                    message = data.text;
                } else {
                    message = wptn_chat_vars.chat_box_unknown_err;
                }

                var time = new Date();

                const site_message_object = '<div style="display: none;" class="message">\n' +
                    '                    <h6 class="name">' + wptn_chat_vars.chat_box_title + '</h6>\n' +
                    '                    <p class="message-text">' + message + '</p>\n' +
                    '                    <span class="date">' + time.getHours() + ':' + time.getMinutes() + '</span>\n' +
                    '                </div>';
                jQuery(message_object).appendTo(".wptn-chat__body__messages").show('slow');
                jQuery(site_message_object).delay(1200).appendTo(".wptn-chat__body__messages").show('slow');
                jQuery(".simplebar-content-wrapper").delay(1250).animate({scrollTop: jQuery(".simplebar-content").height() + 100}, 500);

            });
            ajax.done(function (data) {

                if (data.error === "no") {
                    var time = new Date();

                    const site_message_object = '<div style="display: none;" class="message">\n' +
                        '                    <h6 class="name">' + wptn_chat_vars.chat_box_title + '</h6>\n' +
                        '                    <p class="message-text">' + data.text + '</p>\n' +
                        '                    <span class="date">' + time.getHours() + ':' + time.getMinutes() + '</span>\n' +
                        '                </div>';
                    jQuery(message_object).appendTo(".wptn-chat__body__messages").show('slow');
                    jQuery(site_message_object).delay(1200).appendTo(".wptn-chat__body__messages").show('slow');
                    jQuery("#message-input").attr("placeholder", "We will contact you.");
                    jQuery("#message-input").val('');
                    jQuery("#message-input").attr("disabled", "disabled");
                    jQuery(".submit-message").attr("disabled", "disabled");
                    jQuery(".wptn-emoji-area.wptn-form-controls").hide('slow');
                    jQuery(".simplebar-content-wrapper").delay(1250).animate({scrollTop: jQuery(".simplebar-content").height() + 100}, 500);
                } else {
                    clicked = 0;
                    var message = null;

                    if (typeof (data.text) != "undefined" && data.text !== null) {
                        message = data.text;
                    } else {
                        message = wptn_chat_vars.chat_box_unknown_err;
                    }

                    var time = new Date();

                    const site_message_object = '<div style="display: none;" class="message">\n' +
                        '                    <h6 class="name">' + wptn_chat_vars.chat_box_title + '</h6>\n' +
                        '                    <p class="message-text">' + message + '</p>\n' +
                        '                    <span class="date">' + time.getHours() + ':' + time.getMinutes() + '</span>\n' +
                        '                </div>';
                    jQuery(message_object).appendTo(".wptn-chat__body__messages").show('slow');
                    jQuery(site_message_object).delay(1200).appendTo(".wptn-chat__body__messages").show('slow');
                    jQuery("#message-input").val("");
                    jQuery(".simplebar-content-wrapper").delay(1250).animate({scrollTop: jQuery(".simplebar-content").height() + 100}, 500);

                }
            });

        }
    });
    jQuery(".emoji-button").click(function () {
        if (!jQuery('#message-input').prop('disabled')) {
            jQuery(".emojiPicker").css("bottom", "-56.2px");
        }
    });

    jQuery(".wptn-user-info .user-info-form__body form input[type='submit']").click(function (e) {
        e.preventDefault();

        const user_phone = jQuery(".wptn-user-info .user-info-form__body form #user-phone").val();
        const user_id = jQuery(".wptn-user-info .user-info-form__body form #user-id").val();
        if (user_id || user_phone) {

            jQuery(".wptn-user-info").fadeOut();
            var time = new Date();
            const message_object = '<div style="display: none;" class="message">\n' +
                '                    <h6 class="name">' + wptn_chat_vars.chat_box_title + '</h6>\n' +
                '                    <p class="message-text">' + wptn_chat_vars.chat_box_wlc_msg + '</p>\n' +
                '                    <span class="date">' + time.getHours() + ':' + time.getMinutes() + '</span>\n' +
                '                </div>';
            jQuery(message_object).delay(500).appendTo(".wptn-chat__body__messages").show('slow');
        }
    });
});