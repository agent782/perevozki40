/**
 * Created by Admin on 31.01.2018.
 */
$(document).ready(function () {
    $("#name").suggestions({
        token: "c53e6a2d52a60943309905a9e6b83c9e5d392eae",
        type: "PARTY",
        count: 5,
        /* Вызывается, когда пользователь выбирает одну из подсказок */
        onSelect: function(suggestion) {
            if(!$('#phone2').val()) {
                $('#phone2').hide();
                $('label[for="phone2"]').hide();
            }
            if(!$('#phone3').val()) {
                $('#phone3').hide();
                $('label[for="phone3"]').hide();
            }
            if(!$('#email2').val()) {
                $('#email2').hide();
                $('label[for="email2"]').hide();
            }
            if(!$('#email2').val()) {
                $('#email3').hide();
                $('label[for="email3"]').hide();
            }
            // console.log(suggestion);
            $('#formCompany').attr('hidden', false);
            $('#name').val(suggestion.value);
            $('#inn').val(suggestion.data.inn);
            if($('#kpp').val()) {
                $('#kpp').val(suggestion.data.kpp);
            } else $('#kpp').val('0');
            $('#address').val(suggestion.data.address.value);
            $('#address_real').val(suggestion.data.address.value);
            $('#address_post').val(suggestion.data.address.value);

            if(suggestion.data.management) {
                $('#management-name').val(suggestion.data.management.name);
                $('#management-post').val(suggestion.data.management.post);
                $('#FIO_contract').val(suggestion.data.management.name);
                $('#job_contract').val(suggestion.data.management.post);
            }
            if(suggestion.data.ogrn){
                $('#ogrn').val(suggestion.data.ogrn);
                // $('#ogrn').attr('disabled', true);
            }
            if(suggestion.data.ogrn_date){
                $('#ogrn_date').val(timeConverter(suggestion.data.ogrn_date));
                // $('#ogrn_date').attr('disabled', true);
            }
            $('#okved').val(suggestion.data.okved);
            $('#okpo').val(suggestion.data.okpo);
            $('#citizenship').val(suggestion.data.citizenship);
            $('#name-full').val(suggestion.data.name.full);
            $('#name.short').val(suggestion.data.name.short);
            $('#address-value').val(suggestion.data.address.value);
            $('#branch_type').val(suggestion.data.branch_type);
            $('#capital').val(suggestion.data.capital);
            $('#opf-short').val(suggestion.data.opf.short);
            if($('#state-actuality_date').val()) {
                $('#state-actuality_date').val(timeConverter(suggestion.data.state.actuality_date));
            }
            if($('#state-registration_date').val()) {
                $('#state-registration_date').val(timeConverter(suggestion.data.state.registration_date));
            }
            if($('#state-liquidation_date').val()) {
                $('#state-liquidation_date').val(timeConverter(suggestion.data.state.liquidation_date));
            }
            $('#state-status').val(suggestion.data.state.status);
            $('#data-type').val(suggestion.data.type);
            $('#value').val(suggestion.value);

            if(suggestion.data.type === 'LEGAL'){
                $('#basis_contract').val('Устава');
            } else {
                $('#management-name').hide();
                $('label[for="management-name"]').hide();
                $('#management-post').hide();
                $('label[for="management-post"]').hide();

                $('#FIO_contract').val(suggestion.data.name.full);
                $('#job_contract').val(suggestion.data.opf.short);
                $('#basis_contract').val('Свидетельства №  , от ');
            }

        }
    });

    $('#phone').on('change', function () {
        if($(this).val()){
            $('#phone2').show();
            $('label[for="phone2"]').show();
        } else {
            $('#phone2').hide();
            $('label[for="phone2"]').hide();
        }
    });
    $('#phone2').on('change', function () {
        if($(this).val()){
            $('#phone3').show();
            $('label[for="phone3"]').show();
        } else {
            $('#phone3').hide();
            $('label[for="phone3"]').hide();
        }
    });
    $('#email').on('change', function () {
        if($(this).val()){
            $('#email2').show();
            $('label[for="email2"]').show();
        } else {
            $('#email2').hide();
            $('label[for="email2"]').hide();
        }
    });
    $('#email2').on('change', function () {
        if($(this).val()){
            $('#email3').show();
            $('label[for="email3"]').show();
        } else {
            $('#email3').hide();
            $('label[for="email3"]').hide();
        }
    });

    //подсказка на адреса
    $("#address").suggestions({
        token: "c53e6a2d52a60943309905a9e6b83c9e5d392eae",
        type: "ADDRESS",
        count: 5,
        /* Вызывается, когда пользователь выбирает одну из подсказок */
        onSelect: function(suggestion) {
            if($(this).val(suggestion.data.postal_code)) {
                $(this).val(suggestion.data.postal_code + ', ' + suggestion.unrestricted_value)
            } else $(this).val(suggestion.unrestricted_value);
            // alert(suggestion);
        }
    });
    $("#address_real").suggestions({
        token: "c53e6a2d52a60943309905a9e6b83c9e5d392eae",
        type: "ADDRESS",
        count: 5,
        /* Вызывается, когда пользователь выбирает одну из подсказок */
        onSelect: function(suggestion) {
            if($(this).val(suggestion.data.postal_code)) {
                $(this).val(suggestion.data.postal_code + ', ' + suggestion.unrestricted_value)
            } else $(this).val(suggestion.unrestricted_value);
            // alert(suggestion);
        }
    });
    $("#address_post").suggestions({
        token: "c53e6a2d52a60943309905a9e6b83c9e5d392eae",
        type: "ADDRESS",
        count: 5,
        /* Вызывается, когда пользователь выбирает одну из подсказок */
        onSelect: function(suggestion) {
            if($(this).val(suggestion.data.postal_code)) {
                $(this).val(suggestion.data.postal_code + ', ' + suggestion.unrestricted_value)
            } else $(this).val(suggestion.unrestricted_value);
            // alert(suggestion);
        }
    });


//подключить библиотеку
    function timeConverter(UNIX_timestamp){
        return $.format.date(UNIX_timestamp, "dd.MM.yyyy");
    }


})
