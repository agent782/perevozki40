<?php

namespace app\controllers;

use app\models\Document;
use app\models\DownloadPoaForm;
use app\models\FAQ;
use app\models\signUpClient\SignUpClientFormStart;
use app\models\XprofileXcompany;
use moonland\helpers\JSON;
use Yii;
use app\models\Company;
use app\models\CompanySearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\Profile;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\web\UploadedFile;

/**
 * CompanyController implements the CRUD actions for Company model.
 */
class CompanyController extends Controller
{
    /**
     * @inheritdoc
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
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions'=>['index', 'create', 'validate-add-company', 'validate-date'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
//                    //просмотр только своих юр лиц
                    [
                        'actions'=>['view'],
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
            //юр лицо в списке текущего пользователя
                            return Company::CompanyBelongsUser(Yii::$app->user->getId(), Yii::$app->request->get('id'));
                        },
                    ],
                    //скачивание договора только  своих юр лиц
                    [
                        'actions'=>['download-document'],
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            //юр лицо в списке текущего пользователя
                            return Company::CompanyBelongsUser(Yii::$app->user->getId(), Yii::$app->request->get('idCompany'));
                        },
                    ],
//                    // редактирование юр лица
                    [
                        'actions'=>['update','delete','download-document'],
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            return
                                //юр лицо в списке текущего пользователя
                                (Company::CompanyBelongsUser(Yii::$app->user->getId(), Yii::$app->request->get('id')))
                                    &&

                                (
                                    //и одно из трех
                                    //или число привязашшых профилей == 1, единственный профиль
                                    Company::countProfiles(Yii::$app->request->get('id'))==1
                                    ||
                                    //или проверена доверенность
                                    Company::CompanyBelongsUser(Yii::$app->user->getId(), Yii::$app->request->get('id'))->checked
                                    ||
                                    //или если не один профиль, и у других профелей не проверена доверенность
                                    (
                                        Company::countProfiles(Yii::$app->request->get('id'))>1
                                        &&
                                        !Company::checked(Yii::$app->request->get('id'))
                                    )
                                );

                        }
                    ],
                ],
            ],

        ];
    }

    /**
     * Lists all Company models.
     * @return mixed
     */
    public function actionIndex($idCompany = null, $completeRedirect = null)
    {
        $modelDocument = new Document();

        $searchModel = new CompanySearch();
        $dataProvider = $searchModel->searchProfileAll(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'modelDocument' => $modelDocument
        ]);
    }

    /**
     * Displays a single Company model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Company model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($user_id = null)
    {
        $modelCompany = new Company();
        $modelProfile = Profile::findOne(Yii::$app->user->getId());
        $XcompanyXprofile = new XprofileXcompany();
        if ($modelCompany->load(Yii::$app->request->post()) && $XcompanyXprofile->load(Yii::$app->request->post())){
            if(!Company::find()->where(['inn' => $modelCompany->inn])->count()){
                if ($modelCompany->save()) {

                    $XcompanyXprofile->id_company = $modelCompany->id;
                    $XcompanyXprofile->id_profile = Yii::$app->user->getId();
                    if($user_id) $XcompanyXprofile->id_profile = $user_id;
                    if(!$XcompanyXprofile->save()) {
                        return $this->redirect(['create']);
                    }
                    $modelCompany->createDocument(Document::TYPE_CONTRACT_CLIENT);
                    return $this->redirect(['index']);
                } else return $this->redirect('/');
            }else {
                $modelCompany = Company::find()->where(['inn' => $modelCompany->inn])->one();
                if(!$user_id) $$user_id = Yii::$app->user->id;
                if(XprofileXcompany::find()->where(['id_profile' => $user_id])->andWhere(['id_company' => $modelCompany->id])->count()){
//                    return 'Уже добавлено';
                    return $this->redirect(['index']);
                }else {
                    $modelCompany->link('profiles', $modelProfile);
//   //                 return 'Add company to Profile';
                    return $this->redirect(['index']);
                }
            }
        }
        if($modelProfile->getMaxCompanies() <= $modelProfile->getCountCompanies()){
            Yii::$app->session->setFlash('warning', FAQ::getFAQ(Yii::$app->user->getId(), FAQ::FAQ_MAX_COMPANIES));
            return $this->redirect('index');
        }
        return $this->render('create', compact(['modelCompany', 'XcompanyXprofile']));


    }

    /**
     * Updates an existing Company model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id, $idUser, $redirect)
    {
        $modelCompany = $this->findModel($id);
        $XcompanyXprofile = XprofileXcompany::find()->where(['id_profile' => $idUser, 'id_company' => $id])->one();

        if ($modelCompany->load(Yii::$app->request->post()) && $XcompanyXprofile->load(Yii::$app->request->post()) && $modelCompany->save()) {
            $XcompanyXprofile->save();
            return $this->redirect($redirect);
        } else {
            return $this->render('update', [
                'modelCompany' => $modelCompany,
                'XcompanyXprofile' => $XcompanyXprofile,
            ]);
        }
    }

    /**
     * Deletes an existing Company model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
//        $this->findModel($id)->delete();
        if(XprofileXcompany::find()
            ->where([
                'id_company' => $id,
                'id_profile' => Yii::$app->user->getId()
            ])
            ->one()
            ->delete())
        return $this->redirect(['index']);
//        return $this->render('view', ['m' => $this->findModel($id)]);
    }

    /**
     * Finds the Company model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Company the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Company::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    public function actionValidateAddCompany()
    {
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            $model = new Company();
            if($model->load(Yii::$app->request->post()))
                return \yii\widgets\ActiveForm::validate($model);
        }
        throw new \yii\web\BadRequestHttpException('Bad request!');

    }

    public function actionDownloadDocument($idCompany, $type)
    {
//       echo $idCompany;
        $modelCompany = Company::findOne($idCompany);
       switch ($type){
           case Document::TYPE_CONTRACT_CLIENT:
               $document = Document::findOne(['id_company' => $idCompany, 'type' => $type]);

               if(!$document){
                   if (!$modelCompany -> createDocument($document->type)){
                       return $this->redirect('/company/index');
                   }
               }

               if($document->url_download){
                   $path = Yii::getAlias('@client_contracts_forms/' .$document->url_download);

                   if(!file_exists($path)) {
                       $document->delete();
                       $modelCompany->createDocument($type);
                   }
                   return Yii::$app->response->sendFile($path);
               }
                 else {
                   $document->delete();
                   $modelCompany->createDocument($type);
                   $path = Yii::getAlias('@client_contracts_forms/' .$document->url_download);
                   return Yii::$app->response->sendFile($path);
               }
               return $this->redirect('/company/index');
               break;
           default:
               return $this->redirect('/company/index');
       }
    }
    public function actionValidateDate()
    {
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            $model = new XprofileXcompany();
            if($model->load(Yii::$app->request->post()))
                return \yii\widgets\ActiveForm::validate($model);
        }
        throw new \yii\web\BadRequestHttpException('Bad request!');
    }
}

