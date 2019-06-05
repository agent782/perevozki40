/**
 * Created by Admin on 05.06.2019.
 */
$(document).ready(function () {
    $('#edit_surname').click(function () {
        $('#surname').attr('readonly', false).focus();
    });
    $('#surname').focusout(function () {
        $('#surname').attr('readonly', true);
    });


})
