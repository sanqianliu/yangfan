<?php
namespace app\controllers;
use yii\web\Controller;
use app\models\User;
use Yii;
class MemberController extends controller
{
    /**
     * 前台用户登录
     * @return string|\yii\web\Response
     */
    public function  actionAuth()
    {
        $this->layout = 'layout2';
        if (Yii::$app->request->isPost) {
            $url = Yii::$app->request->referrer;
            if (empty($url)) {
                $url = "/";
            }
            Yii::$app->session->setFlash('referrer', $url);
        }
        $model = new User();
        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();
            if ($model->login($post)) {
                $url = Yii::$app->session->getFlash('referrer');
                return $this->redirect($url);
            }
        }
        return $this->render("auth", ['model' => $model]);
    }

    /**
     * 退出登录
     * @return \yii\web\Response
     */
    public function actionLogout()
    {
        Yii::$app->session->remove('loginname');
        Yii::$app->session->remove('isLogin');
        if (!isset(Yii::$app->session['isLogin'])) {
            return $this->goBack(Yii::$app->request->referrer);
        }
    }

    /**
     *
     *邮箱注册
     * @return string
     */
    public function actionReg()
    {
        $model = new User();
        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();
            if ($model->regByMail($post)) {
                Yii::$app->session->setFlash('info', '电子邮件发送成功');
            }
        }
        $this->layout = 'layout2';
        return $this->render('auth', ['model' => $model]);
    }
}
