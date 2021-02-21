<?php

namespace app\controllers;

use Yii;
use app\models\News;
use app\models\NewsSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * NewsController implements the CRUD actions for News model.
 */
class NewsController extends Controller
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
        ];
    }

    /**
     * Lists all News models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new NewsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $user = Yii::$app->user;
        if(!$user->can('admin')){
            if($user->isGuest){
                $dataProvider->query
                    ->andWhere(['id_category' => News::CATEGORY_FOR_ALL]);
            } else {
                if(!$user->can('car_owner')){
                    $dataProvider->query
                        ->andWhere(['in', 'id_category', [News::CATEGORY_FOR_ALL, News::CATEGORY_FOR_USER]]);
                }
            }

        }

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single News model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        $this->view->registerMetaTag([
            'name' => 'description',
            'content' => $model->description]);
        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Creates a new News model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new News();
        $model->create_at = date('d.m.Y');
        $model->rating_up = [];
        $model->rating_down = [];

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing News model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing News model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the News model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return News the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = News::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionUp(){
        $post = Yii::$app->request->post();
        $id_news = $post['id_news'];
        $news = News::findOne($id_news);
        if (!$news) return 0;
        $rating_up = (is_array($news->rating_up))?$news->rating_up:[];
        $rating_down = (is_array($news->rating_down))?$news->rating_down:[];

        $id_user = Yii::$app->user->id;

        if($id_user){
            if(is_array($rating_up)) {
                if (in_array($id_user, $rating_up)) {
                    return json_encode([$news->ratingUp, $news->ratingDown]);
                } else {
                    if (is_array($rating_down)) {
                        if (($delete_key = array_search($id_user, $rating_down)) !== false) {
                            unset($rating_down[$delete_key]);
                        }
                    }
                }
            }
            $rating_up[] = $id_user;
            $news->rating_up = (is_array($rating_up))?$rating_up:[];
            $news->rating_down = (is_array($rating_down))?$rating_down:[];
            $news->save();
            return json_encode([$news->ratingUp, $news->ratingDown]);

        } else {
            $ip = Yii::$app->request->userIP;
            if(is_array($rating_up)) {
                if (in_array($ip, $rating_up)) {
                    return json_encode([$news->ratingUp, $news->ratingDown]);
                } else {
                    if (is_array($rating_down)) {
                        if (($delete_key = array_search($ip, $rating_down)) !== false) {
                            unset($rating_down[$delete_key]);
                        }
                    }
                }
            }
            $rating_up[] = $ip;
            $news->rating_up = ($rating_up)?$rating_up:[];
            $news->rating_down = ($rating_down)?$rating_down:[];
            $news->save();
            return json_encode([$news->ratingUp, $news->ratingDown]);

        }

        return json_encode([$news->ratingUp, $news->ratingDown]);
    }
    public function actionDown(){
        $post = Yii::$app->request->post();
        $id_news = $post['id_news'];
        $news = News::findOne($id_news);
        if (!$news) return 0;
        $rating_up = (is_array($news->rating_up))?$news->rating_up:[];
        $rating_down = (is_array($news->rating_down))?$news->rating_down:[];

        $id_user = Yii::$app->user->id;
        if($id_user){

            if(is_array($rating_down)) {
                if (in_array($id_user, $rating_down)) {
                    return json_encode([$news->ratingUp, $news->ratingDown]);
                } else {
                    if (is_array($rating_up)) {
                        if (($delete_key = array_search($id_user, $rating_up)) !== false) {
                            unset($rating_up[$delete_key]);
                        }
                    }
                }
            }
            $rating_down[] = $id_user;
            $news->rating_up = ($rating_up)?$rating_up:[];
            $news->rating_down = ($rating_down)?$rating_down:[];

            $news->save();
            return json_encode([$news->ratingUp, $news->ratingDown]);

        } else {
            $ip = Yii::$app->request->userIP;
            if(is_array($rating_down)) {
                if (in_array($ip, $rating_down)) {
                    return json_encode([$news->ratingUp, $news->ratingDown]);
                } else {
                    if (is_array($rating_up)) {
                        if (($delete_key = array_search($ip, $rating_up)) !== false) {
                            unset($rating_up[$delete_key]);
                        }
                    }
                }
            }
            $rating_down[] = $ip;
            $news->rating_up = ($rating_up)?$rating_up:[];
            $news->rating_down = ($rating_down)?$rating_down:[];
            $news->save();
            return json_encode([$news->ratingUp, $news->ratingDown]);
        }

        return json_encode([$news->ratingUp, $news->ratingDown]);
    }
}
