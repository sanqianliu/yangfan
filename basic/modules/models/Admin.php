<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/25
 * Time: 20:38
 */
namespace app\modules\models;

use yii\db\ActiveRecord;
use Yii;
class Admin extends ActiveRecord
{
    public $rememberMe = true;

    public $repass;

    public  static function tableName()
    {
        return "{{%admin}}";
    }

    public function attributeLabels()
    {
        return [
            'adminuser' => '管理员帐号',
            'adminemail' => '管理员邮箱',
            'adminpass' => '管理员密码',
            'repass' => '确认密码'
        ];
    }

    public function rules()
    {
        return [
            ['adminuser', 'required', 'message' => '管理员帐号不能为空','on' => ['login','seekpass','changepass','adminadd','changeemail']],
            ['adminpass', 'required', 'message' => '管理员密码不能为空','on' => ['login','changepass','adminadd','changeemail']],
            ['rememberMe','boolean','on' => ['login']],
            ['adminpass', 'validatePass','on' => ['login','changeemail']],
            ['adminemail', 'required', 'message' => '管理员邮箱不能为空','on' =>['adminadd', 'seekpass','changeemail']],
            ['adminemail', 'email', 'message' => '管理员邮箱格式不正确','on' =>['seekpass','adminadd','changeemail']],
            ['adminemail', 'unique','message' => '管理员邮箱已被注册','on' =>['adminadd','changeemail']],
            ['adminuser', 'unique','message' => '管理员帐号已被注册','on' =>'adminadd'],
            ['adminemail', 'validateEmail','on' =>'seekpass'],
            ['repass', 'required', 'message' => '确认密码不能为空', 'on' => ['changepass', 'adminadd']],
            ['repass', 'compare', 'compareAttribute' => 'adminpass', 'message' => '两次密码输入不一致', 'on' => ['changepass', 'adminadd']],
        ];
    }
    public function validatePass()
    {
        if(!$this->hasErrors()) {
            $data = self::find()->where('adminuser = :user and adminpass = :pass',[':user' => $this->adminuser,':pass' => md5($this->adminpass)])->one();
            if(is_null($data)) {
                $this->addError('adminpass',"用户名或者密码错误") ;
            }
        }
    }
    public function login($data)
    {
        $this->scenario = 'login';
        if($this->load($data) && $this-> validate()) {
            $lifetime = $this->rememberMe ? 86400 : 0;
            $session = Yii::$app->session;
            session_set_cookie_params($lifetime);
            $session['admin'] = [
               'adminuser' => $this->adminuser,
               'isLogin' => true
            ];
            $this->updateAll(['logintime' => time(), 'loginip' => ip2long(Yii::$app->request->userIP)], 'adminuser =:user',['user'=> $this->adminuser]);
            return (bool)$session['admin']['isLogin'];
        }
        return false;
    }
    public function seekpass($data)
    {
        $this->scenario = 'seekpass';
        if($this->load($data) && $this->validate()){
            $time = time();
            $token = $this->createToken($data['Admin']['adminuser'],$time);
            $mailer = Yii::$app->mailer->compose('seekpass',['adminuser' => $data['Admin']['adminuser'],'time' => $time,'token' => $token]);
            $mailer ->setFrom('2037714260@qq.com');
            $mailer ->setTo($data['Admin']['adminemail']);
            $mailer ->setSubject('暗夜商城，找回密码');
            if($mailer->send()) {
                return true;
            }
        }
        return false;
    }
    public function validateEmail()
    {
        if(!$this->hasErrors()) {
            $data = self::find()->where('adminuser = :user and adminemail = :email',[':user'=> $this->adminuser,':email'=> $this->adminemail])->one();
            if(is_null($data)) {
                $this->addError('adminemail',"管理员邮箱不匹配");
            }
        }
    }
    public function createToken($adminuser,$time)
    {
        return  md5(md5($adminuser) .base64_encode(Yii::$app->request->userIP).md5($time));
    }

    public function changepass($data)
    {
        $this->scenario = 'changepass';
        if($this->load($data) && $this->validate()) {
            return (bool) $this->updateAll(['adminpass' => md5($this->adminpass)], 'adminuser = :user',[':user' => $this->adminuser]);
        }
        return false;
    }
    public  function  reg($data)
    {
        $this->scenario = 'adminadd';
        if($this->load($data) && $this->validate()) {
            $this->adminpass  = md5($this->adminpass);
            if($this->save(false)) {
                return true;
            }
                return false;
        }
        return true;
    }
    public function changeemail($data)
    {
        $this->scenario = 'changeemail';
        if($this->load($data) && $this->validate()) {
                return (bool)$this->updateAll(['adminemail'=> $this-> adminemail],'adminuser = :user',[':user'=>$this->adminuser]);
        }
        return false;
    }
}

