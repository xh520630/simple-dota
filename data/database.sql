
-- 商家表
CREATE TABLE
IF NOT EXISTS `jf_seller`(
	`seller_id` INT (10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`account` VARCHAR (20) NOT NULL UNIQUE COMMENT '账号',
	`password` VARCHAR (60) NOT NULL COMMENT '账号密码',
	`avatar` VARCHAR (200) NOT NULL DEFAULT '' COMMENT '用户头像',
	`mobile` VARCHAR (20) NOT NULL DEFAULT '' COMMENT '联系手机',
	`status` TINYINT (1) NOT NULL DEFAULT '0' COMMENT '账号状态 0 未审核 1 正常 2 禁用 3 删除',
	`login_ip` VARCHAR (40) NOT NULL DEFAULT '0.0.0.0' COMMENT '上次登陆IP',
	`login_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '上次登录时间',
	`login_count` INT (10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '登陆次数',
	`seller_name` VARCHAR (60) NOT NULL DEFAULT '' COMMENT '商家名称',
	`country` SMALLINT (5) UNSIGNED NOT NULL DEFAULT '0' COMMENT '国家',
	`province` SMALLINT (5) UNSIGNED NOT NULL DEFAULT '0' COMMENT '省份',
	`city` SMALLINT (5) UNSIGNED NOT NULL DEFAULT '0' COMMENT '城市',
	`district` SMALLINT (5) UNSIGNED NOT NULL DEFAULT '0' COMMENT '地区',
	`address` VARCHAR (60) NOT NULL DEFAULT '' COMMENT '详细位置',
	`hotline` VARCHAR (60) NOT NULL DEFAULT '' COMMENT '热线电话',
	`longitude` VARCHAR (40) NOT NULL DEFAULT '0' COMMENT '经度',
	`latitude` VARCHAR (40) NOT NULL DEFAULT '0' COMMENT '纬度',
	`license_num` VARCHAR (255) NOT NULL DEFAULT '' COMMENT '营业执照',
	`license_pic` VARCHAR (255) NOT NULL DEFAULT '' COMMENT '营业执照图片',
	`register_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '注册时间',
	PRIMARY KEY (`seller_id`)
) ENGINE = INNODB DEFAULT CHARSET = utf8;



-- 菜单分类
CREATE TABLE
IF NOT EXISTS `jf_food_cat`(
  `cat_id` INT (10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `cat_name` VARCHAR(20) NOT NULL DEFAULT '' COMMENT '分类名称',
  `seller_id` INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '商家ID',
  `status` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '状态 0 正常 1 禁用 ',
  `sort_val` SMALLINT(3) NOT NULL DEFAULT '50' COMMENT '排序值 越大越前',
   PRIMARY KEY (cat_id)
) ENGINE = INNODB DEFAULT CHARSET = utf8;

ALTER TABLE `jf_food_cat` ADD COLUMN `sort_val` SMALLINT(3) NOT NULL DEFAULT '50' COMMENT '排序值 越大越前';

-- 菜单
CREATE TABLE
IF NOT EXISTS `jf_food_menu` (
	`food_id` INT (10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`food_name` VARCHAR (20) NOT NULL DEFAULT '' COMMENT '食品名称',
	`cat_id` INT (10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '分类ID',
	`seller_id` INT (10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '商家ID',
	`price` DECIMAL (8, 2) NOT NULL DEFAULT '0.00' COMMENT '单价',
	`stock` SMALLINT (3) NOT NULL DEFAULT '0' COMMENT '库存 - 1 不限',
	`cover` VARCHAR (255) NOT NULL DEFAULT '' COMMENT '菜单封面',
	`status` TINYINT (1) NOT NULL DEFAULT '0' COMMENT '状态 0 销售 1 下架',
	`description` VARCHAR (2000) NOT NULL DEFAULT '' COMMENT '描述',
	PRIMARY KEY (food_id)
) ENGINE = INNODB DEFAULT CHARSET = utf8;


-- 房型
CREATE TABLE
IF NOT EXISTS `jf_room_list` (
	`room_id` INT (10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`room_name` VARCHAR (20) NOT NULL DEFAULT '' COMMENT '房间名称',
	`seller_id` INT (10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '商家ID',
	`price` DECIMAL (8, 2) NOT NULL DEFAULT '0.00' COMMENT '单价',
	`stock` SMALLINT (3) NOT NULL DEFAULT '0' COMMENT '房间数量 - 1 不限',
	`cover` VARCHAR (255) NOT NULL DEFAULT '' COMMENT '房间封面',
	`status` TINYINT (1) NOT NULL DEFAULT '0' COMMENT '状态 0 可预订 1 下架',
	`description` VARCHAR (2000) NOT NULL DEFAULT '' COMMENT '描述',
	PRIMARY KEY (room_id)
) ENGINE = INNODB DEFAULT CHARSET = utf8;

-- 订房订单列表
CREATE TABLE
IF NOT EXISTS `jf_order_room` (
	`id` INT (10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`order_sn` VARCHAR (40)  NOT NULL DEFAULT '' COMMENT '订单SN',
	`user_id` VARCHAR (20) NOT NULL DEFAULT '' COMMENT '用户ID',
	`mobile` VARCHAR (12) NOT NULL DEFAULT '' COMMENT '联系电话',
	`seller_id` INT (10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '商家ID',
	`order_amount` DECIMAL (8, 2) NOT NULL DEFAULT '0.00' COMMENT '订单总价',
	`num` SMALLINT (3) NOT NULL DEFAULT '0' COMMENT '订房数量',
	`order_status` TINYINT (1) NOT NULL DEFAULT '0' COMMENT '订单状态 0 待确认 1 已确认,未支付 2 已取消 3 已支付 4 已完成 5 退单',
	`pay_status` TINYINT (1) NOT NULL DEFAULT '0' COMMENT '支付状态 0未支付 1已支付',
	`out_trade_no` VARCHAR (120) NOT NULL DEFAULT '0' COMMENT '支付流水号',
	`note` VARCHAR (2000) NOT NULL DEFAULT '' COMMENT '备注',
	`order_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '下单时间',
	PRIMARY KEY (id)
) ENGINE = INNODB DEFAULT CHARSET = utf8;

-- 订房订单详情
CREATE TABLE
IF NOT EXISTS `jf_order_room_info` (
	`id` INT (10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`order_id` INT (10)  NOT NULL DEFAULT '0' COMMENT '订单ID',
	`room_id` INT (10)  NOT NULL DEFAULT '0' COMMENT '房型ID',
	`book_num` SMALLINT (3)  NOT NULL DEFAULT '0' COMMENT '订房数量',
	`price` DECIMAL (8, 2) NOT NULL DEFAULT '0.00' COMMENT '即时单价',
  `live_date` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '入住时间',
  `leave_date` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '退房时间',
	PRIMARY KEY (id)
) ENGINE = INNODB DEFAULT CHARSET = utf8;


-- 房型图片
CREATE TABLE
IF NOT EXISTS `jf_room_gallery` (
	`img_id` INT (10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`room_id` INT (10) NOT NULL DEFAULT '0' COMMENT '房间ID',
	`img_url` varchar (255) NOT NULL DEFAULT '0' COMMENT '图片地址',
	`img_desc` varchar (255) NOT NULL DEFAULT '' COMMENT '图片描述',
	`thumb_url` varchar (255) NOT NULL DEFAULT '' COMMENT '微缩图片地址',
	PRIMARY KEY (img_id)
) ENGINE = INNODB DEFAULT CHARSET = utf8;


-- 商家配置
CREATE TABLE
IF NOT EXISTS `jf_seller_config` (
  `id` INT (10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `seller_id` INT (10) NOT NULL DEFAULT '0' COMMENT '商家ID',
  `domain` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '商家自定义域名',
  `shop_name` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '商家名称',
  `app_id` VARCHAR (255) not NULL DEFAULT '' comment '公众号APP ID',
  `app_secret` VARCHAR (255) not NULL DEFAULT '' comment '公众号密钥',
	PRIMARY KEY (id)
) ENGINE = INNODB DEFAULT CHARSET = utf8;


-- 酒店表
DROP TABLE IF EXISTS `jf_hotel_list`;
CREATE TABLE `jf_hotel_list` (
  `hotel_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `seller_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '对应seller_id',
  `hotel_name` varchar(60) NOT NULL DEFAULT '' COMMENT '酒店名称',
  `address` varchar(60) NOT NULL DEFAULT '' COMMENT '酒店地址',
  `star_level` tinyint(1) NOT NULL  DEFAULT '0' COMMENT '星级',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '酒店状态 0 未审核 1 已审核',
  `telephone` varchar(20) NOT NULL DEFAULT '' COMMENT '柜台电话',
  `thumb` varchar(200) NOT NULL DEFAULT '' COMMENT '缩略图',
  `images` varchar(1000) NOT NULL DEFAULT '' COMMENT '展示图片组',
  `province` int(10) NOT NULL DEFAULT '0' COMMENT '所在省会',
  `city` int(10) NOT NULL DEFAULT '0' COMMENT '所在城市',
  `area` int(10) NOT NULL DEFAULT '0' COMMENT '所在区域',
  `area_str` varchar(40) NOT NULL DEFAULT '' COMMENT '以逗号隔开的省市区中文',
  `location` varchar(40) NOT NULL DEFAULT '' COMMENT '酒店经纬度',
  `description` text NOT NULL DEFAULT '' COMMENT '酒店详情',
  `book_notice` varchar(2000) NOT NULL DEFAULT '' COMMENT '预定须知',
  `traffic` varchar(2000) NOT NULL DEFAULT '' COMMENT '交通指南',
  `create_time` datetime NOT NULL COMMENT DEFAULT '0000-00-00 00:00:00' '发布时间',
  `longitude` varchar(40) NOT NULL COMMENT DEFAULT '' '经度',
  `latitude` varchar(40) NOT NULL COMMENT DEFAULT '' '经度',
  PRIMARY KEY (`hotel_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;


-- 订单详情表
DROP TABLE IF EXISTS `jf_order_hotel`;
CREATE TABLE `jcy_order_hotel` (
  `ext_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(10) NOT NULL COMMENT '订单ID',
  `live_date` date NOT NULL COMMENT '入住日期',
  `leave_date` date NOT NULL COMMENT '退房日期',
  `contact_name` varchar(20) NOT NULL COMMENT '预定人姓名',
  `contact_mobile` varchar(11) NOT NULL COMMENT '联系人手机',
  `book_num` tinyint(1) NOT NULL COMMENT '预定客房数量',
  `hotel_id` int(10) NOT NULL COMMENT '酒店ID',
  `room_id` int(10) NOT NULL COMMENT '客房ID',
  `pay_status` tinyint(1) NOT NULL COMMENT '订单支付状态',
  `arrival_time` varchar(5) NOT NULL COMMENT '保留时间，最晚到达时间',
  PRIMARY KEY (`ext_id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8;


-- 订房订单表
DROP TABLE IF EXISTS `jf_order_hotel`;
CREATE TABLE `jf_order_hotel` (
  `order_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_sn` varchar(40) NOT NULL DEFAULT '' COMMENT '订单号',
  `status` tinyint(1) NOT NULL DEFAULT '0'  COMMENT '订单状态 0 待确认 1 已确认 2 已取消 3 已支付 4 已完成 5 已退款',
  `total_price` decimal(8,2) NOT NULL DEFAULT '0.00'  COMMENT '订单总价格',
  `pay_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '支付状态 0 未支付 1 已支付 ',
  `pay_method` tinyint(1) NOT NULL DEFAULT '0' COMMENT '支付方式 1 现场支付 2 微信支付 ',
  `pay_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '支付时间',
  `payment_amount` decimal(8,2) NOT NULL DEFAULT '0.00'  COMMENT '实际支付金额',
  `remark` varchar(100) NOT NULL DEFAULT '' COMMENT '订单备注',
  `user_id` int(10) NOT NULL DEFAULT '0' COMMENT '订单关联用户ID',
  `create_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '订单创建时间',
  PRIMARY KEY (`order_id`),
  UNIQUE KEY `order_sn` (`order_sn`)
) ENGINE = INNODB DEFAULT CHARSET = utf8;



-- 订单详情表
DROP TABLE IF EXISTS `jf_order_hotel_info`;
CREATE TABLE `jf_order_hotel_info` (
  `ext_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(10) NOT NULL DEFAULT '0' COMMENT '订单ID',
  `live_date` date NOT NULL DEFAULT '0000-00-00' COMMENT '入住日期',
  `leave_date` date NOT NULL DEFAULT '0000-00-00' COMMENT '退房日期',
  `contact_name` varchar(20) DEFAULT '' NOT NULL COMMENT '预定人姓名',
  `contact_mobile` varchar(11) DEFAULT '' NOT NULL COMMENT '联系人手机',
  `book_num` tinyint(1) NOT NULL DEFAULT '0' COMMENT '预定客房数量',
  `hotel_id` int(10) NOT NULL DEFAULT '0' COMMENT '酒店ID',
  `room_id` int(10) NOT NULL DEFAULT '0' COMMENT '客房ID',
  `pay_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '订单支付状态',
  `arrival_time` varchar(5) NOT NULL DEFAULT '' COMMENT '保留时间，最晚到达时间',
  PRIMARY KEY (`ext_id`)
) ENGINE = INNODB DEFAULT CHARSET = utf8;