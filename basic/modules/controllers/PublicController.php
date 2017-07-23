<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/23
 * Time: 0:57
 */
namespace  app\modules\controllers;

use yii\web\Controller;
use app\modules\models\Admin;
use Yii;

class PublicController extends  Controller
{
    public function actionLogin()
    {
        $this->layout = false;
        if(isset(Yii::$app->session['admin']['adminuser'])) {
            $this->redirect(['default/index']);
            Yii::$app->end();
        } else {
            $model = new Admin;
            if(Yii::$app->request->isPost) {
                $post = Yii::$app->request->post();
                if($model->login($post)) {
                    $this->redirect(['default/index']);
                    Yii::$app->end();
                }
            }
       }
        return $this->render('login',['model' => $model]);
    }
    public function actionLogout()
    {
        Yii::$app->session->removeAll();
        if(!isset(Yii::$app->session['admin']['adminuser'])) {
            $this->redirect(['public/login']);
            Yii::$app->end();
        }
        $this->goBack();
    }
    public function actionSeekpassword()
    {
        $this->layout = false;
        $model = new Admin;
        if(Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();
            if($model->seekpass($post)){
                Yii::$app->session->setFlash('info','电子邮件已发送成功，请注意查收');
                $this->redirect(['public/login']);
                Yii::$app->end();
            }
        }
        return $this->render('seekpassword',['model' => $model]);
    }
}