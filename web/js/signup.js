/**
 * Created by Admin on 19.01.2018.
 */
$(document).ready(function () {
    // $('#selectPhoto').on('click', function () {
        var filesExt = ['jpg', 'gif', 'png', 'bmp']; // массив расширений
        $('#pathPhoto').change(function(){
            var parts = $(this).val().split('.');
            if(filesExt.join().search(parts[parts.length - 1]) != -1){
                // alert(this.val());
                readURL(this);
            }
            else {
                // $('#photoPreview').attr('src', '/img/noPhoto.jpg');
                // $('#pathPhoto').val('');
            }
        });

        $('#countryPassportDwnList').on('change', function () {
            if($(this).val() == 1){
                $("#passportMask").inputmask("9999-999999");
                $("#passportMask").attr('type', 'tel');
            }
            else {
                $("#passportMask").inputmask("");
                $("#passportMask").attr('type', 'text');
            }
        })

    function readURL(input) {

        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $('#photoPreview').attr('src', e.target.result);
            };

            reader.readAsDataURL(input.files[0]);
        }
    }
    // });
})
