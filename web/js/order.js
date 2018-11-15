/**
 * Created by Admin on 16.10.2018.
 */
$(document).ready(function () {
    $('#chkLoadingTypies input:checkbox:first').click(function () {
        var status = $('#chkLoadingTypies input:checkbox:first').is(':checked');
        $('#chkLoadingTypies input:checkbox').prop('checked', status);
        // $('#chkLoadingTypies input:checkbox').prop('disabled', status);
        $('#chkLoadingTypies input:checkbox:first').prop('disabled', false);
    });

    $('#chkLoadingTypies input:checkbox[value != "0"]').change(function (){
        if(!$(this).prop('checked')) $('#chkLoadingTypies input:checkbox:first').prop('checked', false);
    });

    $('#chkBodyTypies input:checkbox[value = "0"]').change(function () {
        var status = $('#chkBodyTypies input:checkbox[value = "0"]').is(':checked');
        $('#chkBodyTypies input:checkbox').prop('checked', status);
        // $('#chkBodyTypies input:checkbox').prop('disabled', status);
        $('#chkBodyTypies input:checkbox[value = "0"]').prop('disabled', false);
    });

    $('#chkBodyTypies input:checkbox[value != "0"]').change(function (){
        if(!$(this).prop('checked')) $('#chkBodyTypies input:checkbox[value = "0"]').prop('checked', false);
    });

});