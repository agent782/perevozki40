<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 08.10.2018
 * Time: 10:48
 */
?>
Добрый день, <?=$profile->name?>!
<br>
Статус Вашего ТС <?= $vehicle->brand . ' (' . $vehicle->regLicense->reg_number . ') ' ?> изменился.
<br>
Текущий статус: <?= $vehicle->statusText?>.
<br>
<?= $vehicle->error_mes ?>