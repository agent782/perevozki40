/**
 * Created by Admin on 05.06.2019.
 */
$(document).ready(function () {
    $('#edit_profile').click(function () {
        $('#profile').find('input').attr('readonly', false);
        $('#surname').focus();
        $('button').attr('disabled', false);
    });
    $('#edit_passport').click(function () {
        $('#passport').find('input, textarea').attr('readonly', false);
        $('#passportMask').focus();
        $('button').attr('disabled', false);
    });



})
