<?php



/**

 * @author kas-cor <kascorp@gmail.com>

 * @link http://github.com/kas-cor repositories

 */



namespace app\components;



use yii\base\Component;



class Smsru extends Component {



    /**

     * Access id API

     * @var string

     */

    public $api_id;



    public function init() {

        parent::init();

    }



    /**

     * Sending SMS message

     * @param string $to

     * @param string $message

     * @return array

     */

    public function send($to, $message) {

        $data = array(

            "to" => $to,

            "text" => $this->convertToUtf8($message),

            "test" => \Yii::$app->params['sendSmsTest'],

            "json" => 1

        );

        return $this->post("sms/send", $data);

    }



    /**

     * Getting status SMS

     * @param integer $id

     * @return array

     */

    public function status($id) {

        $data = array(

            "id" => $id,
            "json" => 1

        );

        return $this->post("sms/status", $data);

    }



    /**

     * Getting cost message

     * @param string $to

     * @param string $message

     * @return array

     */

    public function cost($to, $message) {

        $data = array(

            "to" => $to,

            "text" => $this->convertToUtf8($message),

            "json" => 1

        );

        return $this->post("sms/cost", $data);

    }



    /**

     * Getting accaunt balance

     * @return array

     */

    public function balance() {
        $data = array(
            "json" => 1
        );

        return $this->post("my/balance", $data);

    }



    /**

     * Getting limits to sending

     * @return array

     */

    public function limit() {

        return $this->post("my/limit");

    }



    /**

     * Getting senders

     * @return array

     */

    public function senders() {

        return $this->post("my/senders");

    }



    /**

     * Post data

     * @param string $method

     * @return array

     */

    private function post($method, $data = array()) {

        $ch = curl_init("http://sms.ru/" . $method);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        curl_setopt($ch, CURLOPT_POSTFIELDS, array_merge(array("api_id" => $this->api_id), $data));

        $res = curl_exec($ch);

        curl_close($ch);

        return json_decode($res);
        return explode("\n", $res);

    }



    /**

     * Convert text to utf-8

     * @param string $text

     */

    private function convertToUtf8($text) {

        if (!preg_match("//u", $text)) {

            return iconv("windows-1251", "utf-8", $text);

        } else {

            return $text;

        }

    }



}