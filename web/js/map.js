var myMap;

// Дождёмся загрузки API и готовности DOM.
ymaps.ready(init);

function init () {
    var myMap = new ymaps.Map('map', {
        center: [55.118881, 36.624248],
        zoom: 11,
        controls: [],
    });
    var sugV1, sugV2, sugV3, sugV4, sugV5, sugV6, sugV7, sugV8;
    var SuggestView = [sugV1,sugV2,sugV3,sugV4,sugV5,sugV6,sugV7,sugV8];
    var suggestView_1 = new ymaps.SuggestView('rStart', {
        provider: {
            suggest: function (request, options) {

                return (suggestView_1.state.get('open') ?
                    ymaps.suggest(request) : ymaps.vow.resolve([]))
                    .then(function (res) {
                        suggestView_1.events.fire('requestsuccess', {
                            target: suggestView_1,
                        })
                        return res;
                    });
            }
        }
    });
    suggestView_1.state.set('open', true);
    var suggestView_2 = new ymaps.SuggestView('rFinish', {
        provider: {
            suggest: function (request, options) {

                return (suggestView_1.state.get('open') ?
                    ymaps.suggest(request) : ymaps.vow.resolve([]))
                    .then(function (res) {
                        suggestView_2.events.fire('requestsuccess', {
                            target: suggestView_2,
                        })

                        return res;
                    });
            }
        }
    });
    suggestView_2.state.set('open', true);

    $('#but').on('click', function () {
        myMap.geoObjects.removeAll();
    /**
     * Тестовый массив с метками адресов
     */
    var rBase =  'Россия, Калужская область, городской округ Обнинск, микрорайон Белкино, Борисоглебская улица, 88 ';
    // var r1 = $('#r1').val();
    // var r2 = $('#r2').val();
    // var r3 = $('#r3').val();
    // var r4 = $('#r4').val();
    // var r5 = $('#r5').val();
    // var r6 = $('#r6').val();
    // var r7 = $('#r7').val();
    // var r8 = $('#r8').val();

    // var adresses = [
    //     [rBase], [r1], [r2], [r3], [r4], [r5], [r6], [r7], [r8], [rBase]
    // ];
        var adresses = [];
        adresses.push(rBase);
        $('#form').find('input').each(function () {
            adresses.push($(this).val());

        });
        adresses.push(rBase);


        // referencePoints: adresses
        // boundsAutoApply: true
        //
    // Создаем карту с добавленными на нее кнопками.
    // var myMap = new ymaps.Map('map', {
    //     center: [55.750625, 37.626],
    //     zoom: 7,
    // }, {
    //     buttonMaxWidth: 300
    // });

    /// Создаем модель мультимаршрута.
    var multiRouteModel = new ymaps.multiRouter.MultiRouteModel(adresses, {
        //avoidTrafficJams: true,
        //viaIndexes: [1]
    });

// Создаем отображение мультимаршрута на основе модели.
    var multiRouteView = new ymaps.multiRouter.MultiRoute(multiRouteModel,{
        boundsAutoApply: true
    });
    myMap.geoObjects.add(multiRouteView);

// Подписываемся на события модели мультимаршрута.
    multiRouteView.model.events
        .add("requestsuccess", function (event) {
            var routes = event.get("target").getRoutes();
            console.log("Найдено маршрутов: " + routes.length);
            for (var i = 0, l = routes.length; i < l; i++) {
                console.log("Длина маршрута " + (i + 1) + ": " + routes[i].properties.get("distance").text);
            }
            var lenRoute = routes[0].properties.get("distance").text;
            $('#len').html(lenRoute);
        })
        .add("requestfail", function (event) {
            console.log("Ошибка: " + event.get("error").message);

        });

    });
    $(".extremum-click").click(function () {
        $(this).siblings(".extremum-slide").slideToggle("slow");
        $('html, body').animate({ scrollTop: ($('.extremum-click').offset().top) - 60}, 300);
    });

    $('#addPoint').on('click', function () {
        //var nInput = 0;
        var Inputs = [];
        $('#form').find('input').each(function () {
            Inputs.push($(this).val());
        });
        if(Inputs.length<10){
            $('#rFinish').before('<p><input type="text" id="r' + (Inputs.length - 1) + '"  style="width: 90%" placeholder="Промежуточная точка"></p>');
            var name = 'r' + (Inputs.length - 1);
            SuggestView[Inputs.length - 2] = new ymaps.SuggestView('r' + (Inputs.length - 1), {
                provider: {
                    suggest: function (request, options) {

                        return (SuggestView[Inputs.length - 2].state.get('open') ?
                            ymaps.suggest(request) : ymaps.vow.resolve([]))
                            .then(function (res) {
                                SuggestView[Inputs.length - 2].events.fire('requestsuccess', {
                                    target: SuggestView[Inputs.length - 2],
                                })
                                return res;
                            });
                    }
                }
            });
            SuggestView[Inputs.length - 2].state.set('open', true);
        }

    });

    $('#clearAllPoint').on('click', function () {
        $('#form').find('input').not('.currentPoint').remove();
        myMap.geoObjects.removeAll();
    });
}
