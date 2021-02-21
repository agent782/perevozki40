<?php
/* @var $profile \app\models\Profile;
 * Created by PhpStorm.
 * User: Denis
 * Date: 21.02.2020
 * Time: 8:36
 */
    use yii\bootstrap\Html;
    use yii\helpers\ArrayHelper;
?>

<?php
    echo Html::checkboxList('roles', $profile->user->getRoles(true),
        \app\models\auth_item::ArrayListRoles(),[
            'onchange' => '
                var roles = [];
                 $(this).find("input:checked").each(function(){
                    roles.push($(this).val());
                 })
                $.ajax({
                                                url: "/admin/users/select-roles",
                                                type: "POST",
                                                dataType: "json",
                                                data: {
                                                    id_user : ' . $profile->id_user . ',
                                                    selected_roles :  roles
                                                },
                                                
                                                success: function(data){
//                                                    alert(data);
                                                },
                                                error: function(){
                                                    alert("Ошибка на сервере!")
                                                }
                                         });
            '
        ])
?>
