<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 16.07.2019
 * Time: 12:44
 */
/* @var $this \yii\web\View
 *
 */
Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
Yii::$app->response->headers->add('Content-Type', 'text/html;charset=utf-8');
echo $this->renderFile(Yii::getAlias('@app') . '/web/documents/user_agreement.docx');