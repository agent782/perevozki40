<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 21.11.2017
 * Time: 10:15
 */
    use yii\widgets\ActiveForm;
    use yii\bootstrap\Html;
    use corpsepk\DaData\SuggestionsWidget;
?>

TEST2

<hr>
<?//= SuggestionsWidget::widget([
//    'name' => 'str',
//    'id' => 's',
//    'type' => 'PARTY',
//    'token' => 'c53e6a2d52a60943309905a9e6b83c9e5d392eae',
//]) ?>



<!--<input id="party" name="party" type="text" size="100"/>-->
<link href="https://cdn.jsdelivr.net/jquery.suggestions/16.10/css/suggestions.css" type="text/css" rel="stylesheet" />
<!--<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>-->
<!--[if lt IE 10]>-->
<!--<script type="text/javascript" src="http://cdnjs.cloudflare.com/ajax/libs/jquery-ajaxtransport-xdomainrequest/1.0.1/jquery.xdomainrequest.min.js"></script>-->
<!--<![endif]-->-->
<script type="text/javascript" src="https://cdn.jsdelivr.net/jquery.suggestions/16.10/js/jquery.suggestions.min.js"></script>
<script type="text/javascript">
<!--    $("#party").suggestions({-->
<!--        token: "c53e6a2d52a60943309905a9e6b83c9e5d392eae",-->
<!--        type: "PARTY",-->
<!--        /* Вызывается, когда пользователь выбирает одну из подсказок */-->
<!--        onSelect: function(suggestion) {-->
<!--            console.log(suggestion.data.inn);-->
<!--            var ticks = suggestion.data.state.registration_date;-->
<!--            var date = new Date(ticks);-->
<!--            alert(date);-->
<!--        }-->
<!--    });-->
<!--</script>-->
<section class="container">
    <h1>Гранулярные подсказки по адресу (регион, город, улица, дом)</h1>
    <label for="region">Регион / район:</label><input id="region" name="region" type="text" />
    <br/><br/>
    <label for="city">Город / населенный пункт:</label><input id="city" name="city" type="text" />
    <br/><br/>
    <label for="street">Улица:</label><input id="street" name="street" type="text" />
    <br/><br/>
    <label for="house">Дом:</label><input id="house" name="house" type="text" />
    <br/><br/>
</section>
<p id="res"></p>
<script>
    function join(arr /*, separator */) {
        var separator = arguments.length > 1 ? arguments[1] : ", ";
        return arr.filter(function(n){return n}).join(separator);
    }

    function formatCity(suggestion) {
        var address = suggestion.data;
        if (address.city_with_type === address.region_with_type) {
            return address.settlement_with_type || "";
        } else {
            return join([
                address.city_with_type,
                address.settlement_with_type]);
        }
    }

    var
        token = "c53e6a2d52a60943309905a9e6b83c9e5d392eae",
        type  = "ADDRESS",
        $region = $("#region"),
        $city   = $("#city"),
        $street = $("#street"),
        $house  = $("#house");

    // регион и район
    $region.suggestions({
        token: token,
        type: type,
        hint: false,
        bounds: "region-area"
    });

    // город и населенный пункт
    $city.suggestions({
        token: token,
        type: type,
        hint: false,
        bounds: "city-settlement",
        constraints: $region,
        formatSelected: formatCity
    });

    // улица
    $street.suggestions({
        token: token,
        type: type,
        hint: false,
        bounds: "street",
        constraints: $city
    });

    // дом
    $house.suggestions({
        token: token,
        type: type,
        hint: false,
        bounds: "house",
        constraints: $street,
        onSelect: function (suggestion) {
            $('#res').html(suggestion.data.city_type ? suggestion.data.city_with_type : suggestion.data.settlement_with_type);
        }
    });


    console.log($house.suggestions())

    function showFull() {
        alert(suggestion.value);
    }
</script>

<?php

    var_dump($str);
?>


