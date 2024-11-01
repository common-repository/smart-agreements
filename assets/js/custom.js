function selectText(containerid) {
    if (document.selection) {
        var range = document.body.createTextRange();
        range.moveToElementText(containerid);
        range.select();
    } else if (window.getSelection) {
        var range = document.createRange();
        range.selectNode(containerid);
        window.getSelection().removeAllRanges();
        window.getSelection().addRange(range);
        document.execCommand("copy");
    }
}
jQuery(document).ready(function ($) {
    $("#test_mail_div").hide();
    $("#test_mail_btn").click(function (e) {
        e.preventDefault();
        $("#test_mail_div").toggle();
    });
    $('#test_mail').click(function () {
        var testMailData = {
            to: $('#test_email').val(),
            subject: $('#test_subject').val(),
            msg_body: $('#test_msg_body').val(),
        };
        var ajaxurl = $(this).attr("data-action-url");
        $.ajax({
            url: ajaxurl,
            method: 'post',
            type: 'json',
            data: {
                'action': 'test_mail_check',
                'testMailData': testMailData,
            },
            success: function (data) {
                if (data == "success") {
                    $('#test_message').delay(3500).fadeOut(200);
                    $('#test_message').css("color", "green");
                    $('#test_message').html('Sent mail successfully.');
                } else {
                    $('#test_message').html('Please try again.');
                    $('#test_message').css("color", "red");
                    $('#test_message').delay(3500).fadeOut(200);
                }

            }
        });
    });
});
