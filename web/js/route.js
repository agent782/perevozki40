$(document).ready(function () {

    endLoading();

    startLoading();
    var lenRoute = 0;
    var nRoutes = 0;
    var myMap;
    var rBase = 'Обнинск, Курчвтова, 47';
    var rStart = $('#rStart');
    var r1 = $('#r1');
    var r2 = $('#r2');
    var r3 = $('#r3');
    var r4 = $('#r4');
    var r5 = $('#r5');
    var r6 = $('#r6');
    var r7 = $('#r7');
    var r8 = $('#r8');
    var rFinish = $('#rFinish');
    var adresses = [
        rBase, rStart.val(), r1.val(), r2.val(), r3.val(), r4.val(), r5.val(), r6.val(), r7.val(), r8.val(), rFinish.val(), rBase
    ];
    var Routes = [
        rStart, r1, r2, r3, r4, r5, r6, r7, r8, rFinish
    ];
    // var sugS, sugV1, sugV2, sugV3, sugV4, sugV5, sugV6, sugV7, sugV8, sugF;
    // var SuggestView = [sugS, sugV1,sugV2,sugV3,sugV4,sugV5,sugV6,sugV7,sugV8,sugF];
    // Дождёмся загрузки API и готовности DOM.
    ymaps.ready(init);

    function init() {
        // $('.hidescreen,.load_page').attr(display ='block');
        // startLoading();
        // $('#loader').click();
        var myMap = new ymaps.Map('map', {
            center: [55.118881, 36.624248],
            zoom: 11,
            controls: [],
        });
        var sugS, sugV1, sugV2, sugV3, sugV4, sugV5, sugV6, sugV7, sugV8, sugF;
        var SuggestView = [sugS, sugV1, sugV2, sugV3, sugV4, sugV5, sugV6, sugV7, sugV8, sugF];
        for (var i in SuggestView) {
            var r = Routes[i].attr('id');
            SuggestView[i] = new ymaps.SuggestView(r, {
                provider: {
                    suggest: function (request, options) {

                        return (SuggestView[i].state.get('open') ?
                            ymaps.suggest(request) : ymaps.vow.resolve([]))
                            .then(function (res) {
                                SuggestView[i].events.fire('requestsuccess', {
                                    target: SuggestView[i],
                                })

                                return res;

                            });
                    }
                }
            });
            SuggestView[i].state.set('open', true);
            SuggestView[i].events.add('select', function (e) {
                createRoute();
            });
            endLoading();
        }

        function alertObj(o){var s="";for(k in o){s+=k+": "+o[k]+"\r\n";}alert(s);}

        $('#but').on('click', function () {
            createRoute();
        });

        $('#addPoint').on('click', function () {

            if (nRoutes < 9) {
                var inputRoute = Routes[nRoutes + 1];
                inputRoute.attr({type: 'text'});
                nRoutes++;
            }
        });

        $('#clearAllPoint').on('click', function () {
            startLoading();
            if(!lenRoute && !nRoutes) return;

            for (var i in Routes) {
                Routes[i].val('');
            }
            $('#hiddenRoutes').find('input').attr({type: 'hidden'});
            myMap.geoObjects.removeAll();
            lenRoute = 0;
            nRoutes = 0;
            $('#len').text(lenRoute).trigger('change');
            $('#lengthRoute').val(lenRoute);
            endLoading();
        });


        $(".extremum-click").click(function () {
            $(this).siblings(".extremum-slide").slideToggle("slow");
            $('html, body').animate({scrollTop: ($('.extremum-click').offset().top) - 60}, 300);
        });
        /**
         * Created by Admin on 30.10.2017.
         */


        function createRoute() {


            myMap.geoObjects.removeAll();
            lenRoute = 0;
            $('#len').text(lenRoute);
            var adresses = [];
            adresses.push(rBase);
            $('#route').find('.points').each(function () {
                if ($(this).val() ) {
                    adresses.push($(this).val());
                }
            });
            adresses.push(rBase);
            /**
             * Тестовый массив с метками адресов
             */
            if (rStart.val() && rFinish.val()) {
                startLoading();
                var multiRouteModel = new ymaps.multiRouter.MultiRouteModel(adresses, {
                    //avoidTrafficJams: true,
                    //viaIndexes: [1]
                });

                // Создаем отображение мультимаршрута на основе модели.
                var multiRouteView = new ymaps.multiRouter.MultiRoute(multiRouteModel, {
                    boundsAutoApply: true
                });
                myMap.geoObjects.add(multiRouteView);

                // Подписываемся на события модели мультимаршрута.
                multiRouteView.model.events
                    .add("requestsuccess", function (event) {

                        var routes = event.get("target").getRoutes();
                        // console.log("Найдено маршрутов: " + routes.length);
                        // for (var i = 0, l = routes.length; i < l; i++) {
                        //     console.log("Длина маршрута " + (i + 1) + ": " + routes[i].properties.get("distance").text);
                        // }
                        lenRoute = (parseFloat(routes[0].properties.get("distance").value) / 1000);
                        (lenRoute < 1) ? lenRoute = lenRoute.toFixed(1) : lenRoute = lenRoute.toFixed(0);
                        $('#len').text(lenRoute).trigger('change');
                        $('#lengthRoute').val(lenRoute);
                        endLoading();


                    })
                    .add("requestfail", function (event) {
                        console.log("Ошибка: " + event.get("error").message);
                    });
                endLoading();


            }


        }




        function getRates() {
            $.ajax({
                type: 'POST',
                url: '/site/ajax-order',
                data: {
                    'key': 'rate',
                    'longlen': longlen,
                    'values': data,
                },

                datatype: 'json',
                success: function (res) {

                },
                error: function (res) {
                    alert('Ошибка');
                },
                beforeSend: function () {
                },
                complete: function () {
                }
            });
        }

        // ФУНКЦИИ ГЕТТЕРЫ ПОЛЕЙ, ЧЕКБОКСОВ и т.д.
        function getLoadTypes() {
            var ltypes = [];
            ($('#loadingtype').find('input:checkbox:checked').each(function () {
                ltypes.push(this.value);
            }));
            return ltypes;
        }

        function getLongLength() {
            var longlen = [];
            $('#longlenradio').find('input:radio:checked').each(function () {
                longlen.push(this.value);
            });
            return longlen[0];
        }

        function getBodyTypes() {
            var btypes = [];
            ($('#bodytype').find('input:checkbox:checked').each(function () {
                btypes.push(this.value);
            }));
            return btypes;
        }
        function arrayDataForAjax(key) {
           return {
                'key': key,
                'vehType' : $('#typeVehChk').val(),
                'loadTypes' :  getLoadTypes(),
                'longlen': getLongLength(),
                'bodyTypes': getBodyTypes(),
                'length' : $('#length').val(),
               'wigth' : $('#wigth').val(),
               'height' : $('#height').val(),
               'volume' : $('#volume').val(),
               'palletEP' : $('#palletEP').val(),
               'palletRP' : $('#palletRP').val(),
               'palletLP' : $('#palletLP').val(),
               'distance' : $('#lengthRoute').val(),
               'dateStart' : $('#dateStart').val(),
               'dateStartMax' : $('#dateStartMax').val(),
            };
        }



        $('#testBut').on('click', function () {
            alert(arrayDataForAjax());
        });


    }
})

// Тип транспорта
//     $('#typeVehChk').val()
// Тип погрузки
//      getLoadTypes()
// Длинномер 0 или 1
//      getLongLength()
// Тип кузова
//      getBodyTypes()


