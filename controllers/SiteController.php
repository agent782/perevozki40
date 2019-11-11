<?php

namespace app\controllers;
use app\models\BodyType;
use yii\helpers\ArrayHelper;
use yii\bootstrap\Modal;
use app\models\LoadingType;
use app\models\OrderOLD;
use app\models\Route;
use function Sodium\crypto_box_keypair_from_secretkey_and_publickey;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\User;
use app\models\auth_item;
use app\models\Ur;


class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
//    public function behaviors()
//    {
//        return [
//            'access' => [
//                'class' => AccessControl::className(),
//                'only' => ['logout'],
//                'rules' => [
//                    [
//                        'actions' => ['logout'],
//                        'allow' => true,
//                        'roles' => ['@'],
//                    ],
//                ],
//            ],
//
//            'verbs' => [
//                'class' => VerbFilter::className(),
//                'actions' => [
//                    'logout' => ['post'],
//                ],
//            ],
//        ];
//    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        $addRole = new auth_item();
        if($addRole->load(Yii::$app->request->post())) {

            if ($addRole->AddRole($addRole->name)) {
                Yii::$app->session->setFlash('access','SAVE OK');
                return $this->refresh();
            } else {

                Yii::$app->session->setFlash('error','NOT SAVE');
            }


        }
        return $this->render('index', compact('addRole'));

    }

    public function actionOrder()
    {
        $order = new OrderOLD();
        $route = new Route();
        return $this->render('order', compact(['order', 'route']));

    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
//        if(!Yii::$app->user->can('index')){
//            throw new ForbiddenHttpException('Access denied');
//        }
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }
//    public function actionAddAdmin() {
//        $model = User::find()->where(['username' => 'denis'])->one();
//        if (empty($model)) {
//            $user = new User();
//            $user->username = 'denis';
//            $user->email = 'agent782@yandex.ru';
//            $user->setPassword('250640');
//            $user->generateAuthKey();
//            if ($user->save()) {
//                echo 'good';
//            }
//        }
//    }
    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    public function actionMap(){
       return $this->render('map');
    }

    public function actionTest(){
//        $a = 'ERROR';
        //$order = Order::find()->one();
        //$order->bodytype = 1;
//            if($order -> save()){
//                $a = 'OK';
//            }else{
//                $a = 'ERROR';
//            }
        $order = new OrderOLD();
        $route = new Route();

//        if($route->load(Yii::$app->request->post()) && $order->load(Yii::$app->request->post())){
//            if (Yii::$app->request->post('submit') == 0) {
//
//                if ($order->load(Yii::$app->request->post()) && $route->load(Yii::$app->request->post())) {
//                    // $isValid = $route->validate();
//                    //$isValid = $profile->validate() && $isValid;
//                    //if ($isValid) {
//                    //  $route->id_order = 1;
//                    //  $route->save();
//                    $order->id_user = 1;
//                    $order->date_create = date('Y-m-d H:i:s');
//
////            $test =  $route->distance ;
////            $test =  var_dump($order->datetime_start;
////            if($order->save() && $route->save()) {
////                 foreach ($order->id_loadingTypes as $id){
////                     $loadtype = LoadingType::findone($id);
////                     $order->link('loadingTypes', $loadtype );
////                 }
////                foreach ($order->id_bodyTypes as $id){
////                    $bodytype = BodyType::findone($id);
////                    $order->link('bodytype', $bodytype );
////                }
//                    $order->link('route', $route);
////
////                $test = 'Saved';
////                 return $this->render('test', ['route' => $route, 'order' => $order,'test' => $test]);
////            }
//                    $session = Yii::$app->session;
//                    $session->set('order', $order);
//                    $session->set('route', $route);
//                    return $this->render('test2', ['route' => $route, 'order' => $order]);
//                }
                switch (Yii::$app->request->post('button')){
                    case '0':
                        if ($order->load(Yii::$app->request->post()) && $route->load(Yii::$app->request->post())){
                            $session = Yii::$app->session;
                            $session->set('order', $order);
                            $session->set('route', $route);
                            return $this->render('test2', ['route' => $route, 'order' => $order]);
                        }
                        break;
                    case 'back':
                        return $this->render('test', ['route' => $route, 'order' => $order]);
                        break;
                    case 'next':
                        $order = Yii::$app->session->get('order');
                        $route = Yii::$app->session->get('route');
                        $order->id_user = 1;
                        $order->date_create = date('Y-m-d H:i:s');

                        if($seachRoute = Route::find()->where(
                            [
                                'routeStart' => $route->routeStart,
                                'route1' => $route->route1,
                                'route2' => $route->route2,
                                'route3' => $route->route3,
                                'route4' => $route->route4,
                                'route5' => $route->route5,
                                'route6' => $route->route6,
                                'route7' => $route->route7,
                                'route8' => $route->route8,
                                'routeFinish' => $route->routeFinish,
                            ])->one())
                        {
                            $count = $seachRoute->count + 1;
                            $route = $seachRoute;
                            $route->count = $count;
                        }

                        if(
                            $order->save()
                            &&
                        $route->save()
                        )
                        {
                             foreach ($order->id_loadingTypes as $id){
                                 $loadtype = LoadingType::findone($id);
                                $order->link('loadingTypes', $loadtype );
                             }
                            foreach ($order->id_bodyTypes as $id){
                                $bodytype = BodyType::findone($id);
                                $order->link('bodytype', $bodytype );
                            }
                             $order->link('route', $route);

                    $test = 'Saved';
                 return $this->render('test3', ['route' => $route, 'order' => $order,'test' => $test]);
//            }
//                    $session = Yii::$app->session;
//                    $session->set('order', $order);
//                    $session->set('route', $route);
                }
                    $test = 'NO';
                        return $this->render('test3', ['route' => $route, 'order' => $order, 'test' => $test]);
                        break;
                    default:
                        break;
                }
//            }

        $test = Yii::$app->request->post();
        return $this->render('test', ['route' => $route, 'order' => $order, 'test' => $test]);

     }

    public function actionAjaxOrder(){
        if (\Yii::$app->request->isAjax){
            $res = array();
            $post = \Yii::$app->request->post();
            $key = $post['key'];
            $values = $post['values'];
            switch ($key){
                case 'typeVehChk':
                    //foreach ($values as $value) {

                        $res = \app\models\LoadingType::find()
                            ->joinWith(['vehicles' => function ($query) {
                                $query->joinWith(['vehicletype']);
                            },
                            ])
                            ->where(['vehicle_type.id' => $values])
                            ->asArray()->all();
                   // }
                    $res = ArrayHelper::map($res, 'id', 'type');
                    break;
                case 'loadingtype':
                    $longlen = $post['longlen'];
                    Yii::$app->session->set('longlen', $longlen);
                    if ($longlen) {
//                    foreach ($values as $value) {
                        $res = ArrayHelper::map(\app\models\BodyType::find()
//                                ->select(['id', 'body'])
                            ->joinWith([
                                'vehicle' => function ($query)use ($values, $longlen) {
                                    $query
                                        ->andWhere(['vehicles.longlength' => $longlen])
                                        ->joinWith(['loadingtype' => function ($query1) use ($values) {
                                            $query1
                                                ->andWhere(['loading_type.id' => $values]);
                                        }]);
                                    }
                                ])
                            ->asArray()
                            ->all(), 'id', 'body');

//                    }

                    }
                    else {
//                        foreach ($values as $value) {
                            $res = ArrayHelper::map(\app\models\BodyType::find()
//                                ->select(['id', 'body'])
                                ->joinWith(['vehicle' => function ($query) use($values, $longlen) {
                                        $query
                                            ->joinWith(['loadingtype'=> function ($query1) use ($values) {
                                                $query1
                                                    ->andWhere(['loading_type.id' => $values]);
                                            }
                                        ]);
                                     },
                                ])
//                                ->where(['vehicles.longlength' => $longlen ])
                                ->orderBy('id')
                                ->asArray()
                                ->all(), 'id', 'body');

//                    }
                    }
                    break;

                default:
                    $res = null;
                    break;

            }
            echo json_encode($res);
        }

    }

    public function actionTest2(){

        return $this->render('test2');
    }

    public function actionTestConfirm(){

    }
}

