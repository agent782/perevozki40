
    function UpdatePriceZones() {
        startLoading();
        $('#loader').click();

        var id_vehicle = $('#id_vehicle').val();
        var veh_type = $('#vehtype');

        var tonnage = $('#tonnage').val();
        var length = $('#length').val();
        var volume = $('#volume').val();
        var radioLonglength = $('#longlength').find("input:checked").val();
        var body_type = $("#body_type").find("input:checked").val();

        var passengers = $("#passengers").val();
//        alert(tonnage + length + volume + radioLonglength + body_type);
        var tonnage_spec = $('#tonnage_spec').val();
        var length_spec = $('#length_spec').val();
        var volume_spec = $('#volume_spec').val();
        $.ajax({
            type:'POST',
            url:'/vehicle/update-pricezones',
            data:{
                // veh_type:veh_type,
                tonnage:tonnage,
                length:length,
                volume:volume,
                longlength:radioLonglength,
                body_type:body_type,
                id_vehicle:id_vehicle,
                passengers:passengers,
                tonnage_spec:tonnage_spec,
                length_spec:length_spec,
                volume_spec:volume_spec
            },
            dataType:'json',
            success:function (data) {
                // alert(data);
//                data = (data);
                $('#PriceZones').empty();
                // alert(data);
                for(var i in data){
                    var n = i;
                    n++;
                    var res = '<label>';
                    res += '<input type="checkbox" name="Vehicle[Price_zones][]"';
//                    res += ' checked';
                    res += ' value="' + data[i].id + '"' + ' >' + "\n";
                    res += '<i class="fa fa-square-o fa-2x"></i>' + "\n" +
                        '<i class="fa fa-check-square-o fa-2x"></i>' + "\n";
                    res += '<span style="font-size: x-small">' + data[i].name + '</span>' + "\n";
                    res += '</label>'
                    res += '<p style="font-size: x-small; font-style: italic">'
                    res += data[i].r_km + ' р/км '
                    res += ', '
                    res += data[i].r_h + ' р/час'
                    res += ' ...'
                    res += '<button class="btn" type="button" data-toggle="modal" data-target="#p' + n + '"><img src="/img/icons/help-25.png"></button>'
                    res += '</p>'
                    res += '<div tabindex="-1" class="fade modal" id="p' + n + '" role="dialog" style="display: none;">'
                    res += '<div class="modal-dialog ">'
                    res += '<div class="modal-content">'
                    res += '<div class="modal-header">'
                    res += '<button class="close" aria-hidden="true" type="button" data-dismiss="modal">×</button>'
                    res +='Информация'
                    res +='</div>'
                    res +='<div class="modal-body">'
                    res += data[i].helpMes

                    ;
                    $('#PriceZones').append(res);
                    endLoading();
                }

            },
            error:function () {
                alert('ERROR');
                endLoading();
            },

        });


    }

