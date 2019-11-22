<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 18.01.2018
 * Time: 10:22
 */
/* @var \yii\web\View $this
 * @var \app\models\Profile $profile
 * @var \app\models\User $user
 * */

use yii\widgets\DetailView;
use yii\bootstrap\Html;
use yii\bootstrap\Tabs;
use yii\helpers\Url;
use app\models\User;

$this->title = Html::encode('Информация о профиле');
?>
<br>
<?php
echo $profile->fioFull;

?>

<?= Tabs::widget([
    'items' => [
        [
            'label' => 'Баланс',
            'content' => $this->render('@app/views/user/balance', User::arrayBalanceParamsForRender($model->id))
        ]
    ]
])?>

