<?php
    namespace app\components\functions;
    use Yii;
    use app\models\User;
    use yii\web\UploadedFile;

    /**
 * Created by PhpStorm.
 * User: Admin
 * Date: 16.01.2018
 * Time: 12:45
 */
class functions
{
    public function findUser($id)
    {
        return User::findOne($id);
    }

    public function findCurrentUser()
    {
        return User::findOne(\Yii::$app->user->identity->getId());
    }

    public function findAllUsers()
    {
        return User::find()->all();
    }

    static public function saveImage($model, string $attribute, string $savePath, string $filename)
    {
        $image = UploadedFile::getInstance($model, $attribute);
        if(!$image) {
//            \Yii::$app->session->setFlash('warning', 'Ошибка сохранения изображения. Фотография не выбрана.');
            return false;
        }
        self::createDirectory($savePath);
        $model->$attribute = $image;
        $filename = $filename . '.' . $image->extension;
        if ($image->saveAs($savePath . $filename)) {
            \Yii::$app->session->setFlash('warning', 'Файл ' . $image . ' успешно сохранен.');
            return $filename;
        }
        \Yii::$app->session->setFlash('warning', 'Ошибка сохранения изображения.');
        return false;
    }

    static public function createDirectory($path)
    {
        //$filename = "/folder/{$dirname}/";
        if (file_exists($path)) {
            //echo "The directory {$path} exists";
        } else {
            mkdir($path, 0775, true);
            //echo "The directory {$path} was successfully created.";
        }
    }

    static public function setFlashSuccess($mes){
        return Yii::$app->session->setFlash('success', $mes);
    }
    static public function setFlashWarning($mes){
        return Yii::$app->session->setFlash('warning', $mes);
    }
    static public function setFlashInfo($mes){
        return Yii::$app->session->setFlash('info', $mes);
    }

    static public function sendEmail($to, $from, string $sub, array $params, $views = null, $layouts = null){
        if(!$from) $from = Yii::$app->params['robotEmail'];
        if(!$views) $views = [
            'html' => 'views/empty_html',
            'text' => 'views/empty_text',
        ];
        if(!$layouts){
            $layouts = [
                'html' => 'layouts/html',
                'text' => 'layouts/text'
            ];
        }

        Yii::$app->mailer->htmlLayout = $layouts['html'];
        Yii::$app->mailer->textLayout = $layouts['text'];
        Yii::$app->mailer->getTransport()->setUsername($from['username']);
        Yii::$app->mailer->getTransport()->setPassword($from['password']);

        $mes = Yii::$app->mailer
            ->compose($views, $params)
            ->setFrom($from['email'])
            ->setSubject($sub);

        if(is_array($to)){
            foreach ($to as $item){
                $mes->setTo($item['email'])->send();
            }
            return;
        } else {
           return $mes ->
            setTo($to)
                ->send();
        }

    }
}