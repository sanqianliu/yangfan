<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/1
 * Time: 20:10
 */
namespace  app\models;
use yii\db\ActiveRecord;
use YII;

class User extends  ActiveRecord
{
    public $rememberMe = true; //记住
    public $repass ; //确认密码
    public $loginname; //登录名

    public static  function tableName()
    {
        return "{{%user}}";
    }
    /**
     * 创建属性标签
     * @return array
     */
    public  function attributeLabels()
    {
        return [
            'username' => '用户名',
            'userpass' => '用户密码',
            'repass' => '确认密码',
            'useremail' => '电子邮箱',
            'loginname' => '用户名/电子邮箱',
        ];
    }

    /**
     *
     * 应用场景验证
     * @return array
     */
    public  function  rules()
    {
        return [
            ['loginname', 'required', 'message' => '登录用户名不能为空', 'on' => ['login']],
            ['username', 'required', 'message' => '用户名不能为空', 'on' => ['reg', 'regbymail']],
            ['username', 'unique', 'message' => '用户名已经被注册', 'on' => ['reg', 'regbymail']],
            ['useremail', 'required', 'message' => '电子邮箱不能为空', 'on' => ['reg', 'regbymail']],
            ['useremail', 'email', 'message' => '电子邮箱格式不正确', 'on' => ['reg', 'regbymail']],
            ['useremail', 'unique', 'message' => '电子邮箱已被注册', 'on' => ['reg', 'regbymail']],
            ['userpass', 'validatePass', 'on' => ['login']],
//            ['userpass', 'required', 'message' => '用户密码不能为空', 'on' => ['reg', 'login', 'regbymail']],
            ['userpass', 'required', 'message' => '用户密码不能为空', 'on' => ['reg', 'login', 'regbymail', 'qqreg']],
            ['repass', 'required', 'message' => '确认密码不能为空', 'on' => ['reg']],
            ['repass', 'compare', 'compareAttribute' => 'repass', 'message' => '两次密码输入不一致', 'on' => ['reg']],
        ];
    }
    /**
     * 前台登录验证密码
     */
    public function validatePass(){
        if(!$this->hasErrors()){
            $loginname = "username";
            if (preg_match('/@/', $this->loginname)){
                $loginname = "useremail";
            }
            $data = self::find()->where($loginname.' = :loginname and userpass = :pass', [':loginname' => $this->loginname, ':pass' => md5($this->userpass)])->one();
            if (is_null($data)){
                $this->addError("userpass", "用户名或密码错误");
            }
        }
    }
    /**
     * 后台添加新用户
     * @param type $data
     * @param type $scenario
     * @return boolean
     */
    public  function reg($data, $scenario = 'reg')
    {
        $this->scenario = $scenario;
        if($this->load($data) && $this->validate()){
            $this->createtime = time();
            $this->userpass = md5($this->userpass);
            if($this->save(false)) {
                return true;
            }
            return false;
        }
        return false;

    }
    /**
     * 根据用户表userid获取关联表
     */
    public function getProfile(){
        return $this->hasOne(Profile::className(), ['userid' => 'userid']);
    }

    /**
     * 前台用户通过邮箱注册，发送邮件告知用户名和密码
     * @param type $data
     * @return boolean
     */
    public function regByMail($data){
        $this->scenario = 'regbymail';
        $data['User']['username'] = 'imooc_'.uniqid();
        $data['User']['userpass'] = uniqid();
        if($this->load($data) && $this->validate()){
            $mailer = Yii::$app->mailer->compose('createuser', ['userpass' => $data['User']['userpass'], 'username' => $data['User']['username']]);
            $mailer->setFrom('2037714260@qq.com');
            $mailer->setTo($data['User']['useremail']);
            $mailer->setSubject('商城新建用户');
            if ($mailer->send() && $this->reg($data, 'regbymail')){//发送邮件，并把新生成的用户名和密码以及前台传过来的注册邮箱添加进用户表
                return true;
            }
        }
        return false;
    }

    /**
     *
     * 前台用户登录，信息写入SESSION
     * @param type $data
     * @return boolean*/

    public function login($data){
        $this->scenario = "login";
        if ($this->load($data) && $this->validate()){
            //把用户信息写入SESSION
            $lifetime = $this->rememberMe ? 24*3600 : 0;
            $session = Yii::$app->session;
            session_set_cookie_params($lifetime);
            $session['loginname'] = $this->loginname;
            $session['isLogin'] = 1;
            return (bool)$session['isLogin'];
        }
        return false;
    }
}