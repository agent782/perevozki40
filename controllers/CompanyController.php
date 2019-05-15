<?php

namespace app\controllers;

use app\components\functions\functions;
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
    public function actionCreate($user_id = null, $redirect = 'index')
    {
        if(!$user_id) $user_id = Yii::$app->user->id;
        $modelCompany = new Company();
        $modelProfile = Profile::findOne($user_id);
        $XcompanyXprofile = new XprofileXcompany();
        if ($modelCompany->load(Yii::$app->request->post()) && $XcompanyXprofile->load(Yii::$app->request->post())){
//            return var_dump($modelCompany);
            if(!Company::find()->where(['inn' => $modelCompany->inn])->count()){
                if ($modelCompany->save()) {

                    $XcompanyXprofile->id_company = $modelCompany->id;
                    $XcompanyXprofile->id_profile = $user_id;
                    if(!$XcompanyXprofile->save()) {
                        return $this->redirect($redirect);
                    }
//                    $modelCompany->createDocument(Document::TYPE_CONTRACT_CLIENT);
                    functions::setFlashSuccess('Юр. лицо создано и добавлено в Ваш список.');
                    return $this->redirect($redirect);
                } else {
                    functions::setFlashWarning('Ошибка. Попробуйте позже.');
                    return $this->redirect($redirect);
                }
            }else {
                $modelCompany = Company::find()->where(['inn' => $modelCompany->inn])->one();
                if(XprofileXcompany::find()->where(['id_profile' => $user_id])->andWhere(['id_company' => $modelCompany->id])->count()){
                    functions::setFlashSuccess('Это юр. лицо уже есть в Вашем списке.');
//                    return 'Уже добавлено';
                    return $this->redirect([$redirect]);
                }else {
                    $modelCompany->link('profiles', $modelProfile);
                    functions::setFlashSuccess('Юр. лицо добавлено в Ваш список.');

//   //                 return 'Add company to Profile';
                    return $this->redirect($redirect);
                }
            }
        }
//        if($modelProfile->getMaxCompanies() <= $modelProfile->getCountCompanies()){
//            Yii::$app->session->setFlash('warning', FAQ::getFAQ(Yii::$app->user->getId(), FAQ::FAQ_MAX_COMPANIES));
//            return $this->redirect('index');
//        }
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

    public function actionDownloadDocument($idCompany, $type,$redirect = '/company/index')
    {
//       echo $idCompany;
        $modelCompany = Company::findOne($idCompany);
        if(!$modelCompany) $this->redirect($redirect);
       switch ($type){
           case Document::TYPE_CONTRACT_CLIENT:
               $document = Document::findOne(['id_company' => $idCompany, 'type' => $type]);

               if(!$document){
                   if (!$modelCompany -> createDocument($document->type)){
                       return $this->redirect($redirect);
                   }
               }

               if($document->url_download){
                   $path = Yii::getAlias('@client_contracts_forms/' .$document->url_download);

                   if(file_exists($path)) {
                       $document->delete();

                   }
                   $modelCompany->createDocument($type);
                   return Yii::$app->response->sendFile($path);
               }
                 else {
                   $document->delete();
                   $document = $modelCompany->createDocument($type);
                   $path = Yii::getAlias('@client_contracts_forms/' .$document->url_download);
                   return Yii::$app->response->sendFile($path);
               }
               return $this->redirect($redirect);
               break;
           default:
               return $this->redirect($redirect);
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

