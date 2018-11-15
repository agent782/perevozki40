<?Header('Content-Type: text/html; charset=utf-8'); ?>
<?php
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;
use nirvana\showloading\ShowLoadingAsset;


/* @var $this yii\web\View */
/* @var $model app\models\Order */
/* @var $form ActiveForm */
$this->registerCssFile("https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.7.5/css/bootstrap-select.min.css");
$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.7.5/js/bootstrap-select.min.js');
ShowLoadingAsset::register($this);
?>
<div class="order">



    <button class="btn btn-primary" id="button">TEST BUTTON</button>
   <p>
    <div class="container col-sm-12">
        <?php
            $vihicle_types = ArrayHelper::map (\app\models\VehicleType::find()->all(), 'id', 'type');
            $body_types = array();

            $form = ActiveForm::begin([
                    'options' => [
                        'enctype' => 'multipart/form-data',
                    ]
            ]);
        ?>
        <p>
        Тип транспорта:<br>
        <?= Html::dropDownList('id', 'type', $vihicle_types, [
            'id' => 'vehicle_type',
            'title' => 'Выберите из списка...',
            'class' => 'selectpicker',
            'multiple' => false,
            'data-width' => '75%',
        ]); ?>
        </p>
<!--        --><?//= Html::dropDownList('id', 'body', $body_types, [
//            'id' => 'body_type',
//            'title' => 'Выберите из списка...',
//            'multiple' => true,
//            'disabled' => true,
//            'class' => 'selectpicker',
//            'data-width' => '75%',
//            'data-selected-text-format' => "count > 2",
//        ]); ?>
        <p>
            Тип кузова: <br>
        <?=
            Html::checkboxList($typebodiess, $selections = null, [], [
                    'id' => 'body_type',
                    //'hidden' => 'true',
                ]);
        ?>
        </p>
        <div id="body_null">
        </div>

        <p>
            Тип погрузки: <br>

        <?=
            Html::checkboxList($loadingtypies, $selections = null, [], [
                'id' => 'loading_type',
                //'hidden' => 'true',
            ]);
        ?>
        <div id="load_null" style="width: 140px"></div>
        <br>
    <div id="the_load" hidden>
        <p>Размеры:</p>
        <a onclick="$('#dimensions').slideToggle('slow');" href="javascript://">Необходимые размеры кузова (в метрах)</a>

            <div id="dimensions" style="display: none;">
                <?= $form->field($order, 'long')->hint('Длинна')->label(false);?>
                <?= $form->field($order, 'width')->hint('Ширина')->label(false); ?>
                <?= $form->field($order, 'height')->hint('Высота')->label(false); ?>
            </div>
        <p>
            <br>
        <a onclick="$('#volume').slideToggle('slow');" href="javascript://">Необходимый объем кузова (м3)</a>

        <div id="volume" style="display: none;">
            <?= $form->field($order, 'volume')->hint('Объем')->label(false);?>

        </div>
    </div>
        <?php
        ActiveForm::end();
        ?>

    </div>

    <div id="test">
        <br>
    </div>
</div>

<script>
    $(document).ready(function () {
//        $('#CHKBOX').append('<label><input name=[] type="checkbox" value="4"/> TEST </label>').show().;

        $('#vehicle_type').change( function () {
            //$('#body_type').find('option').remove();
            //$('#body_type').prop('disabled', true).selectpicker('refresh');
            //$('#body_type').find('label br').remove();
            var data = $(this).val();
            $('#body_null').html('');
            var not_data = 0;
            $.ajax({
                type: 'POST',
                url: '/client/default/ajax',
                data: {'vehicle_type' : data},
                datatype: 'json',
                success: function (res) {
                    var data = eval(JSON.parse(res));
                    $('#body_type').find('label, br').remove();
                    for (var id in data) {
                        // $('#body_type').append('<option value =' + id + '>' + data[id] +'</option>').selectpicker('refresh');
                        $('#body_type').append('<label><input name=[] type="checkbox" value=' + id + '> ' + data[id] + '</label><br>');
                        not_data = 1;
                    }
                    if(!not_data){
                        $('#body_null').html('Ничего не найдено');
                    }
                },
                error: function () {
                    alert ('no');
                },
                beforeSend: function () {
                    $('#body_null').showLoading();
                },
                complete: function () {
                    $('#body_null').hideLoading();
                }
            })
//            $('#body_type').prop('disabled', false).selectpicker('refresh');
            $('#body_type').show();

        })

        $('#body_type').change(function () {
            var data = [];
            var chkd = $('#body_type').find('input[type=checkbox]:checked');
            chkd.each(function () {
                var id = this.value;
               // alert(id);
                data.push(id);
            })
            //alert(data);
            //var data = 4;
            $.ajax({
                type: 'POST',
                url: '/client/default/ajax2',
                data: {'idtypebodies' : data},
                datatype: 'json',
                success: function (res) {
                    $('#loading_type').find('label, br').remove();
                    $('#load_null').html('');
                    if(res) {
                        var data = eval(JSON.parse(res));
                        for (var id in data) {
                            // $('#body_type').append('<option value =' + id + '>' + data[id] +'</option>').selectpicker('refresh');
                            $('#loading_type').append('<label><input name=[] type="checkbox" value=' + id + '> ' + data[id] + '</label><br>');
                        }
//                        $('#the_load').show();
                    } else {
                        $('#load_null').html('Ничего не найдено');
                        $('#the_load').hide();
                    }
                },
                error: function (res) {
                    alert('no');
                },
                beforeSend: function () {
                    $('#load_null').showLoading();
                },
                complete: function () {
                    $('#load_null').hideLoading();
                }
            })
        });
        $('#loading_type').change (function () {
            $('#the_load').hide();
            var checked = [];
            //alert(checked.length);
            $(this).find('input:checkbox:checked').each(function() {
                checked.push($(this).val());
                $('#the_load').show();
            });

        });

        $('#button').on('click', function () {

        })

    })

</script>
