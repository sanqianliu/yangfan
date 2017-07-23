DROP TABLE IF EXISTS `shop_admin`;
create table if not exists  `shop_admin` (
  `adminid` int unsigned not null  AUTO_INCREMENT  COMMENT '主键ID',
  `adminuser` VARCHAR(50) NOT NULL DEFAULT '' COMMENT '管理员帐号',
  `adminpass` CHAR(32) NOT NULL DEFAULT '' COMMEnT '管理员密码',
  `adminemail` VARCHAR(50) NOT NULL DEFAULT '' COMMENT '管理员邮箱',
  `logintime` INT UNSIGNED  NOT NULL DEFAULT '0'COMMENT '登陆时间',
  `loginip`  BIGINT  NOT NULL DEFAULT '0' COMMENT '登录IP',
  `createtime` INT UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建时间',
   PRIMARY key(adminid),
   UNIQUE shop_admin_adminuser_adminpass(`adminuser`,`adminpass`),
   UNIQUE shop_admin_adminuser_adminemail(`adminuser`,`adminemail`)
   )ENGINE = InnoDB DEFAULT CHARSET = utf8;

INSERT INTO `shop_admin`(adminuser,adminpass,adminemail,createtime) VALUES ('admin',md5('admin'),'yangfan@163.com',UNIX_TIMESTAMP());

/**
 *会员表
 */
DROP TABLE IF EXISTS `shop_user`;
CREATE TABLE IF NOT EXISTS `shop_user`(
    `userid` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键ID',
    `username` VARCHAR(32) NOT NULL DEFAULT '',
    `userpass` CHAR(32) NOT NULL DEFAULT '',
    `useremail` VARCHAR(100) NOT NULL DEFAULT '',
    `createtime` INT UNSIGNED NOT NULL DEFAULT '0',
    UNIQUE shop_user_username_userpass(`username`,`userpass`),
    UNIQUE shop_user_useremail_userpass(`useremail`,`userpass`),
    PRIMARY KEY(`userid`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

/**
 *会员信息表
 */
DROP TABLE IF EXISTS `shop_profile`;
CREATE TABLE IF NOT EXISTS `shop_profile`(
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键ID',
    `truename` VARCHAR(32) NOT NULL DEFAULT '' COMMENT '真实姓名',
    `age` TINYINT UNSIGNED NOT NULL DEFAULT '0' COMMENT '年龄',
    `sex` ENUM('0','1','2') NOT NULL DEFAULT '0' COMMENT '性别',
    `birthday` date NOT NULL DEFAULT '2016-01-01' COMMENT '生日',
    `nickname` VARCHAR(32) NOT NULL DEFAULT '' COMMENT '昵称',
    `company` VARCHAR(100) NOT NULL DEFAULT '' COMMENT '公司',
    `userid` BIGINT UNSIGNED NOT NULL DEFAULT '0' COMMENT '用户的ID',
    `createtime` INT UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建时间',
    PRIMARY KEY(`id`),
    UNIQUE shop_profile_userid(`userid`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;


/**
 *商品分类表
 */
DROP TABLE IF EXISTS `shop_category`;
CREATE TABLE IF NOT EXISTS `shop_category`(
    `cateid` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `title` VARCHAR(32) NOT NULL DEFAULT '',
    `parentid` BIGINT UNSIGNED NOT NULL DEFAULT '0',
    `createtime` INT UNSIGNED NOT NULL DEFAULT '0',
    PRIMARY KEY(`cateid`),
    KEY shop_category_parentid(`parentid`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;