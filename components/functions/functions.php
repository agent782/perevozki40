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

    static public function saveImage($model, string $attribute, string $savePath, string $filename) : string
    {
        $image = UploadedFile::getInstance($model, $attribute);
        if(!$image) {
//            \Yii::$app->session->setFlash('warning', 'Ошибка сохранения изображения. Фотография не выбрана.');
            return '';
        }

        self::createDirectory($savePath);
        $model->$attribute = $image;
        $filename = $filename . '.' . $image->extension;
        if ($image->saveAs($savePath . $filename)) {
            \Yii::$app->session->setFlash('success', 'Файл ' . $image . ' успешно сохранен.');
            return $filename;
        }
        \Yii::$app->session->setFlash('warning', 'Ошибка сохранения изображения.');
        return '';
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
                $mes->setTo($item)->send();
            }
            return;
        } else {
           return $mes ->
            setTo($to)
                ->send();
        }

    }
    static public function getModelsNames(){
        $names = scandir(Yii::getAlias('@app/models'));
        $res = [];
        foreach ($names as $name) {
            if($name != '.' && $name != '..'){
                $name = strstr($name, '.', true);
                $res [] = $name;
            }
        }
        return $res;
    }
    static public function getAttributesAndPublicAttributes($model){
        $attributes = get_class_vars(get_class($model));
        $attributes = array_merge($attributes, $model->getAttributes());
        $attributes = array_keys($attributes);
        return $attributes;
    }

    static public function getHtmlLinkToPhone(string $phone, $html =true){
        if(!$html) return $phone;
        return 'Телефон: <a href = "tel:'. '+7' . $phone . '">' . $phone . '</a>';
    }

    static public function DownloadFile(string $pathToFile, string $redirect){
        if(file_exists($pathToFile) && is_file($pathToFile)){
            return Yii::$app->response->sendFile($pathToFile);
        }
        self::setFlashWarning('Ошибка скачивания!');
        return Yii::$app->controller->redirect($redirect);
    }
}