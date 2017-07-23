<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/1
 * Time: 20:01
 */
namespace app\modules\controllers;
use yii\base\Exception;
use yii\web\Controller;
use Yii;
use app\models\User;
use app\models\Profile;
use app\modules\controllers;
use yii\data\Pagination;
class UserController extends Controller
{
    /**
     * 用户列表
     * @return string
     */
    public  function  actionUsers()
    {
        $model = User::find()->joinWith('profile');
        $count = $model->count();
        $pageSize = Yii::$app->params['pageSize']['user'];
        $pager = new Pagination(['totalCount' => $count,'pageSize' => $pageSize]);
        $users = $model->offset($pager->offset)->limit($pager->limit)->all();
        $this->layout = "layout1";
        return $this->render('users',['users' => $users, 'pager' => $pager]);
    }
    /**
     * 添加用户
     * @return string
     */
    public function actionReg()
    {
        $this->layout = "layout1";
        $model = new User();
        if(Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();
            if($model->reg($post)) {
                Yii::$app->session->setFlash("info",'添加会员成功');
            }
        }
        $model->userpass = '';
        $model->repass = '';
        return $this->render("reg",['model' => $model]);
    }

    /**
     * 删除用户
     * @throws \yii\db\Exception
     */
    public function actionDel()
    {
        try {
            $userid = (int)Yii::$app->request->get('userid');
            if (empty($userid)) {
                throw new \Exception();
            }
            $trans = Yii::$app->db->beginTransaction();
            if ($obj = Profile::find()->where('userid = :id', [':id' => $userid])->one()) {
                $res = Profile::deleteAll('userid = :id', [':id' => $userid]);
                if (empty($res)) {
                    throw new \Exception();
                }
            }
            if (!User::deleteAll('userid =:id', [':id' => $userid])) {
                throw new \Exception();
            }
            $trans->commit();
        }catch(\Exception $e) {
            if(Yii::$app->db->getTransaction()){
                $trans->rollBack();
            }
        }
        $this->redirect(['user/users']);
    }
}