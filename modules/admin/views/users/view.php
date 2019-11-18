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

$this->title = Html::encode('Информация о профиле');
?>
<br>
<?php
echo $profile->fioFull;

?>

<?= Tabs::widget([
    'items' => [
        [
            'label' => 1,
            'content' => 1,
        ],
        [
            'label' => 2,
            'content' => 2,
        ],
        [
            'label' => 'Баланс',
            'url' => Url::to(['/user/balance', 'id_user' => $model->id])
        ]
    ]
])?>

