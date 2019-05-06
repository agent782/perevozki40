<?php

namespace app\modules\finance\controllers;

use app\models\Document;
use yii\web\Controller;
use app\models\DocumentSearch;
use Yii;

/**
 * Default controller for the `finance` module
 */
class DefaultController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        $searchDocumentModel = new DocumentSearch();
        $dataDocumentProvider = $searchDocumentModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchDocumentModel,
            'dataProvider' => $dataDocumentProvider,
        ]);


    }

}
