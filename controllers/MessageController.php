<?php

namespace app\controllers;

use app\components\functions\functions;
use app\models\Order;
use Yii;
use app\models\Message;
use app\models\MessageSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use app\models\Review;
/**
 * MessageController implements the CRUD actions for Message model.
 */
class MessageController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@']
                    ]
                ],
            ]
        ];
    }

    /**
     * Lists all Message models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new MessageSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query
            ->where(['id_to_user' => Yii::$app->user->id])
            ->orFilterWhere(['id_from_user' => Yii::$app->user->id])
            ->andWhere(['not', ['status' => Message::STATUS_DELETE]])
        ;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Message model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $mes = $this->findModel($id);
        if($mes->status != $mes::STATUS_READ) {
            $mes->status = $mes::STATUS_READ;
            $mes->save();
        }

        $reviewModel = Review::find()->where(['id_message' => $id])->one();
        if(!$reviewModel) {
            $reviewModel = new Review();
        }
        if($reviewModel->load(Yii::$app->request->post())){
            $reviewModel->id_user_to = $mes->id_to_review;
            $reviewModel->id_user_from = $mes->id_from_review;
            if($mes->can_review_client) $reviewModel->type = $reviewModel::TYPE_TO_VEHICLE;
            if($mes->can_review_vehicle) $reviewModel->type = $reviewModel::TYPE_TO_CLIENT;
            if($mes->id_order) $mes->id_order=$reviewModel->id_order;
            $reviewModel->id_message = $id;

            if($reviewModel->save()) functions::setFlashSuccess('Ваш отзыв отправлен на проверку.');
            else functions::setFlashWarning('Ошибка на сервере. Попробуйте позже.');
            return $this->redirect('/message');
        }

        return $this->render('view', [
            'modelMessage' => $mes,
            'modelReview' => $reviewModel,
        ]);
    }

    /**
     * Creates a new Message model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Message([
            'id_to_user' => Yii::$app->user->id,
            'title' => 'TTTEST',
            'text' => 'TEST',
            'push_status' => Message::STATUS_NEED_TO_SEND,
            'email_status' => Message::STATUS_NEED_TO_SEND,
        ]);

        if ($model->load(Yii::$app->request->post())) {
            $model->save();
            $model->sendPush();
            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }
    /**
     * Updates an existing Message model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
//    public function actionUpdate($id)
//    {
//        $model = $this->findModel($id);
//
//        if ($model->load(Yii::$app->request->post()) && $model->save()) {
//            return $this->redirect(['view', 'id' => $model->id]);
//        }
//
//        return $this->render('update', [
//            'model' => $model,
//        ]);
//    }

    /**
     * Deletes an existing Message model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $mes = $this->findModel($id);
        $mes->changeStatus(Message::STATUS_DELETE);

        return $this->redirect(['index']);
    }

    /**
     * Finds the Message model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Message the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */

    protected function findModel($id)
    {
        if (($model = Message::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Сообщение не найдено.');
    }

    public function actionSetAllStatusRead(){
        $messages = Message::findAll(['id_to_user' => Yii::$app->user->id]);
        if($messages) {
            foreach ($messages as $message) {
                $message->status = $message::STATUS_READ;
                $message->save();
            }
        }
        functions::setFlashSuccess('Все сообщения помечены как прочитанные');
        $this->redirect('/message');
    }
}
