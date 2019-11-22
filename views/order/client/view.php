<?php
/**
 * Created by PhpStorm.
 * User: Denis
 * Date: 21.11.2019
 * Time: 8:31
 */
/* @var \app\models\Order $model
 *
 */

?>

<?= $model->PrintFinishCalculate()?>
<br>
<?= $model->getFullFinishInfo(true, null, true, true)?>

