/**
 * Created by Admin on 17.06.2019.
 */
$(document).ready(function () {

    if($('#timer').text() > 0){

        $('#submit').attr('disabled', false);
        $('#send-sms').attr('disabled', true);

        var i = $('#timer').text();
        $('#time_mes').attr('hidden', false);
        var timer = setInterval(function () {
            --i;
            $('#timer').text(i)
            if(i==0) {
                clearInterval(timer);

                $('#time_mes').attr('hidden', true);
                $('#send-sms').attr('disabled', false);
            }
        }, 1000);
    }

});

$(document).on('click', '#send-sms', function () {
    $("#new_username").text($("#username").text());
    $("#username").inputmask("");

        $.pjax.reload({
        // url : "/user/change-phone",
        container: "#sms-code",
//                datatype: "json",
        type: "POST",
        data: {
            "timer" : $('#timer').text(),
            "username" : $('#username').val()
        },
    });

           var timer = setInterval(function () {
               $('#timer').text($('#timer').text() - 1);
               if($('#timer').text()==0) {
                   clearInterval(timer);
                   $('#time_mes').attr('hidden', true);
                   $('#send-sms').attr('disabled', false);
               }
           }, 1000);
});
