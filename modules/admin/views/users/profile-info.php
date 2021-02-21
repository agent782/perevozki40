<?php
/**
 * Created by PhpStorm.
 * User: Denis
 * Date: 25.12.2019
 * Time: 10:54
 */
/* @var $profile \app\models\Profile
 *
 */
use app\components\functions\functions;
?>

<h4><?=
    functions::getHtmlLinkToPhone($profile->phone) . ' ';
    echo ($profile->phone2)? '(' . functions::getHtmlLinkToPhone($profile->phone2) . ')' : '';
    ?>

</h4>
