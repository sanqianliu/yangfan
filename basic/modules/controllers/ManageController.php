<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/1/14
 * Time: 12:06
 */
namespace  app\modules\controllers;
use yii\web\Controller;
use Yii;
use yii\data\Pagination;
use app\modules\models\Admin;


class ManageController extends  Controller
{

    public function  actionMailchangepass()
    {
        $this->layout = false;
        $token = Yii::$app->request->get('token');
        $adminuser = Yii::$app->request->get('adminuser');
        $time = Yii::$app->request->get('timestamp');
        $model = new Admin();
        $mytoken = $model->createToken($adminuser,$time);
        if($token != $mytoken) {
            $this->redirect('[Public/login]');
            Yii::$app->end();
        }
        if((time() - $time) >300) {
            $this->redirect('[Public/login]');
            Yii::$app->end();
        }
        if(Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();
            if($model->changepass($post)) {
                Yii::$app->session->setFlash('info','密码修改成功');
            }
        }
        $model->adminuser = $adminuser;
        return $this->render('mailchangepass',['model' => $model]);
    }
    public  function  actionManagers()
    {
        $this-> layout = false;
        $managers = Admin::find();
        $pageSizes = Yii::$app->params['pageSize']['manage'];
        $pages = new Pagination(['totalCount'=> $managers->count(),'pageSize'=> $pageSizes]);
        $managers = $managers->offset($pages->offset)->limit($pages->limit)->all();
        return $this->render('managers',['managers' => $managers,'pages'=>$pages]);
    }
    public function actionReg()
    {
        $this->layout = false;
        $model =new Admin();
        if(Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();
            if($model->reg($post)) {
                Yii::$app->session->setFlash('info','添加管理员成功');
            } else{
                Yii::$app->session->setFlash('info','添加管理员失败');
            }
        }
        return $this->render('reg',['model'=>$model]);
    }
    public function actionDelete()
    {
        $adminid = (int) Yii::$app->request->get('adminid');
        if(empty($adminid)) {
            $this->redirect(['manage/managers']);
        }
        $model = new Admin();
        if($model->deleteAll('adminid =:id',[':id'=> $adminid])) {
            Yii::$app->session->setFlash('info','删除管理员成功');
            $this->redirect(['manage/managers']);
        }
    }
    public function actionChangeemail()
    {
        $this->layout = false;
        $model = Admin::find()->where('adminuser = :user',[':user' => Yii::$app->session['admin']['adminuser']])->one();
        if(Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();
            if($model->changeemail($post)) {
                Yii::$app->session->setFlash('info','修改邮箱成功');
            }
        }
        return $this->render('changeemail' ,['model' => $model]);
    }

    public function actionChangepass()
    {
        $this->layout = false;
        $model = Admin::find()->where('adminuser = :user',[':user' => Yii::$app->session['admin']['adminuser']])->one();
        if(Yii::$app->request->isPost){
            $post = Yii::$app->request->post();
            if($model->changepass($post)){ 
                Yii::$app->session->setFlash('info','修改密码成功');
            }
        }
        $model-> adminpass = '';
        $model-> repass= '';
        return $this->render('changepass',['model'=> $model]);
    }
}