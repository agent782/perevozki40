<?php
    namespace app\components\functions;
    use app\models\Message;
    use Yii;
    use app\models\User;
    use yii\imagine\Image;
    use yii\web\UploadedFile;
    use Symfony\Component\Process\Process;

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
            if($image->extension == 'jpg' || $image->extension == 'jpeg' || $image->extension == 'bmp') {
                Image::autorotate($savePath . $filename)->save();
            }

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
            mkdir($path, 0755, true);
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

    static public function sendEmail($to, $from, string $sub, array $params, $views = null,
                                     $layouts = null, array $files = []){
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

        if($files && is_array($files)){
            Yii::$app->mailer->compose();
            foreach ($files as $file){
                $mes->attach($file);
            }
        }

        if(is_array($to)){
            $count = 0;
            foreach ($to as $item){
                if($item)
                    if($mes->setTo($item)->send()) ++$count;
            }
            return $count;
        } else {
            if($to) return $mes ->setTo($to)->send();

        }

    }
    static public function getModelsNames(){
        $names = scandir(Yii::getAlias('@app/models'));
        $res = [];
        foreach ($names as $name) {
            if($name != '.' && $name != '..'){
                $name = strstr($name, '.', true);
                $res [$name] = $name;
            }
        }
        return $res;
    }

    static public function getAttributesAndPublicAttributes($model){
        $attributes = get_class_vars(get_class($model));
        $attributes = array_merge($attributes, $model->getAttributes());
        $attributes = array_keys($attributes);
        $res = [];
        foreach ($attributes as $attribute){
            $res[$attribute] = $attribute;
        }
        return $res;
        return $attributes;
    }

    static public function getHtmlLinkToPhone($phone, $html =true){
        if(!$html) return $phone;
        return '<a href = "tel:'. '+7' . $phone . '">' . $phone . '</a>';
    }

    static public function DownloadFile(string $pathToFile, $redirect){
        if(file_exists($pathToFile) && is_file($pathToFile)){
            return Yii::$app->response->sendFile($pathToFile);
        }
        self::setFlashWarning('Ошибка скачивания!');
        return Yii::$app->controller->redirect($redirect);
    }

    static public function translit($s) {
        $s = (string) $s; // преобразуем в строковое значение
        $s = strip_tags($s); // убираем HTML-теги
        $s = str_replace(array("\n", "\r"), " ", $s); // убираем перевод каретки
        $s = preg_replace("/\s+/", ' ', $s); // удаляем повторяющие пробелы
        $s = trim($s); // убираем пробелы в начале и конце строки
        $s = function_exists('mb_strtolower') ? mb_strtolower($s) : strtolower($s); // переводим строку в нижний регистр (иногда надо задать локаль)
        $s = strtr($s, array('а'=>'a','б'=>'b','в'=>'v','г'=>'g','д'=>'d','е'=>'e','ё'=>'e','ж'=>'j','з'=>'z','и'=>'i','й'=>'y','к'=>'k','л'=>'l','м'=>'m','н'=>'n','о'=>'o','п'=>'p','р'=>'r','с'=>'s','т'=>'t','у'=>'u','ф'=>'f','х'=>'h','ц'=>'c','ч'=>'ch','ш'=>'sh','щ'=>'shch','ы'=>'y','э'=>'e','ю'=>'yu','я'=>'ya','ъ'=>'','ь'=>''));
        $s = preg_replace("/[^0-9a-z-_ ]/i", "", $s); // очищаем строку от недопустимых символов
        $s = str_replace(" ", "-", $s); // заменяем пробелы знаком минус
        return $s; // возвращаем результат
    }

    static public function getLayout(){
        $layout = 'default2';

        $adminLayout = '@app/views/layouts/logist';
        $logistLayout = '@app/views/layouts/logist';
        $buhLayout = '@app/modules/finance/views/layouts/finance';

        if(Yii::$app->user->can('admin')) $layout = $adminLayout;
        if(Yii::$app->user->can('dispetcher')) $layout = $logistLayout;
        if(Yii::$app->user->can('buh')) $layout = $buhLayout;

        return $layout;
    }

    static function DayToStartUnixTime(string $date){
        $day = (24*3600);
        $reminder = strtotime($date) % $day;
        if($reminder > 43140 && $reminder <= 75540){
            return round (strtotime($date)/($day)) * $day - $day;
        } else {
            return round (strtotime($date)/($day)) * $day;
        }
    }

    static public function rus_date() {
// Перевод
        $translate = array(
            "am" => "дп",
            "pm" => "пп",
            "AM" => "ДП",
            "PM" => "ПП",
            "Monday" => "Понедельник",
            "Mon" => "Пн",
            "Tuesday" => "Вторник",
            "Tue" => "Вт",
            "Wednesday" => "Среда",
            "Wed" => "Ср",
            "Thursday" => "Четверг",
            "Thu" => "Чт",
            "Friday" => "Пятница",
            "Fri" => "Пт",
            "Saturday" => "Суббота",
            "Sat" => "Сб",
            "Sunday" => "Воскресенье",
            "Sun" => "Вс",
            "January" => "Января",
            "Jan" => "Янв",
            "February" => "Февраля",
            "Feb" => "Фев",
            "March" => "Марта",
            "Mar" => "Мар",
            "April" => "Апреля",
            "Apr" => "Апр",
            "May" => "Мая",
            "May" => "Мая",
            "June" => "Июня",
            "Jun" => "Июн",
            "July" => "Июля",
            "Jul" => "Июл",
            "August" => "Августа",
            "Aug" => "Авг",
            "September" => "Сентября",
            "Sep" => "Сен",
            "October" => "Октября",
            "Oct" => "Окт",
            "November" => "Ноября",
            "Nov" => "Ноя",
            "December" => "Декабря",
            "Dec" => "Дек",
            "st" => "ое",
            "nd" => "ое",
            "rd" => "е",
            "th" => "ое"
        );
        // если передали дату, то переводим ее
        if (func_num_args() > 1) {
            $timestamp = func_get_arg(1);
            return strtr(date(func_get_arg(0), $timestamp), $translate);
        } else {
// иначе текущую дату
            return strtr(date(func_get_arg(0)), $translate);
        }
    }

    static public function startCommand(string $command, $args = []){
        $arguments = '';
        if($args){
            foreach ($args as $arg){
                $arguments .= $arg . ' ';
            }
        }
        $proccess = new Process('php yii ' . $command . ' ' . $arguments, yii::getAlias('@app'));

        $proccess->start();
    }


}