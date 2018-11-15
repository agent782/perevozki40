<?php
  $this->registerJs('
    ymaps.ready(init);
        var myMap,
            myPlacemark;

        function init(){
            myMap = new ymaps.Map("map", {
                center: [55.76, 37.64],
                zoom: 7
            });

            myPlacemark = new ymaps.Placemark([55.76, 37.64], {
                hintContent: \'Москва!\',
                balloonContent: \'Столица России\'
            });

            myMap.geoObjects.add(myPlacemark);
        }
  ')
?>
<h1>MAP</h1>
<div id="map" style="width: 600px; height: 400px"></div>
<h1>MAP</h1><h1>MAP</h1>


