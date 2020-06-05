INSERT INTO `wp_attachment` (`name`, `filepath`, `filename`, `orderlist`, `is_master`, `description`, `size`, `type`, `extname`, `info_table`, `info_field`, `info_id`, `in_use`, `created_at`, `source_url`, `extfield`) 
SELECT `name`, `filepath`, `filename`, `orderlist`, `is_master`, `description`, `size`, `type`, `extname`, `info_table`, `info_field`, `info_id`, `in_use`, `created_at`, `source_url`, `extfield` FROM `workbench_bench`.`wp_attachment` WHERE `id` >= 390


http://www.huihui.cn/;
https://www.dgtle.com/
https://www.kongfz.com/
http://www.juanpi.com/
http://www.shibeiw.com/
http://www.quanmama.com/
http://www.juniuhuo.com/
http://www.dazhetu.com/
https://www.fanli.com/
https://www.zhonghuasuan.com/
https://www.taofen8.com/
http://www.geihui.com/
http://www.shihuo.cn/
https://www.mgpyh.com/
http://www.showbagnow.com/
https://zhizhizhi.com/
https://www.sqkb.com/
http://www.wisenjoy.com/
http://zhidian.com.cn/
https://guangdiu.com/
https://www.meidebi.com/
http://manmanbuy.com/
http://yifen.com/
http://onlyas.com/


UPDATE `wp_goods_qrcode` SET `sku_id` = 3 WHERE `id` >= 68916 AND `id` <= 69570;
UPDATE `wp_goods_qrcode` SET `sku_id` = 2 WHERE `id` >= 65511 AND `id` <= 68915;
UPDATE `wp_goods_qrcode` SET `sku_id` = 1 WHERE `id` >= 62800 AND `id` <= 65300;
UPDATE `wp_goods_qrcode` SET `sku_id` = 1 WHERE `id` >= 67955 AND `id` <= 68016;


SELECT `q`.`id`, `g`.`name`, `q`.`sku_id`, `q`.`code`, `q`.`idcode`, `q`.`serial`, `q`.`batch_code`, CONCAT('http://m.bujie.ltd/verify-qrcode?code=', `q`.`code`)  FROM `workhealth_shop`.`ws_goods` AS `g`, `workhealth_cycle`.`wp_goods_qrcode` AS `q`, `workhealth_shop`.`ws_goods_sku` AS `gs` WHERE `gs`.`id` = `q`.`sku_id` AND `q`.`batch_code` = '20191206' AND `g`.`id` = `gs`.`goods_id` ORDER BY `q`.`id` ASC
SELECT `q`.`id`, `g`.`name`, `q`.`sku_id`, `q`.`code`, `q`.`idcode`, `q`.`serial`, `q`.`batch_code`, CONCAT('http://m.bujie.ltd/check-qrcode?code=', `q`.`code`)  FROM `workhealth_shop`.`ws_goods` AS `g`, `workhealth_cycle`.`wp_goods_qrcode` AS `q`, `workhealth_shop`.`ws_goods_sku` AS `gs` WHERE `gs`.`id` = `q`.`sku_id` AND `q`.`batch_code` = '20191212' AND `g`.`id` = `gs`.`goods_id` ORDER BY `q`.`id` ASC


SELECT `g`.`name`, `q`.`sku_id`, `q`.`code`, `q`.`idcode`, `q`.`serial`, `q`.`batch_code`, CONCAT('http://m.bujie.ltd/verify-qrcode?code=', `q`.`code`)  FROM `workhealth_shop`.`ws_goods` AS `g`, `workhealth_cycle`.`wp_goods_qrcode` AS `q`, `workhealth_shop`.`ws_goods_sku` AS `gs` WHERE `gs`.`id` = `q`.`sku_id` AND `q`.`batch_code` = '20191206' AND `g`.`id` = `gs`.`goods_id`;
SELECT `g`.`name`, `q`.`sku_id`, `q`.`code`, `q`.`idcode`, `q`.`serial`, `q`.`batch_code`, CONCAT('http://m.bujie.ltd/check-qrcode?code=', `q`.`code`)  FROM `workhealth_shop`.`ws_goods` AS `g`, `workhealth_cycle`.`wp_goods_qrcode` AS `q`, `workhealth_shop`.`ws_goods_sku` AS `gs` WHERE `gs`.`id` = `q`.`sku_id` AND `q`.`batch_code` = '20191212' AND `g`.`id` = `gs`.`goods_id`;
SELECT * FROM `workydd_shop`.`ws_goods` WHERE `id` IN (238);
UPDATE `ws_goods_sku` SET `price_origin` = 1199 WHERE `goods_id` IN (238);
UPDATE `workydd_shop`.`ws_goods` SET `price_origin` = 1199 WHERE `id` IN (238);

UPDATE `workydd_shop`.`ws_website_goods` SET `price` = 1199, `price_discount` = 1200 WHERE `goods_id` IN (238);
UPDATE `workydd_shop`.`ws_website_sku` SET `price` = 1199, `price_discount` = 1200 WHERE `goods_id` IN (238);
UPDATE `workydd_shop`.`ws_scene_goods` SET `price` = 1199, `price_discount` = 1200 WHERE `goods_id` IN (238);
UPDATE `workydd_shop`.`ws_scene_sku` SET `price` = 1199, `price_discount` = 1200 WHERE `goods_id` IN (238);

SELECT `s`.`id`, `s`.`barcode`, `ss`.`sku_id` FROM `ws_goods_sku` AS `s`, `ws_scene_sku` AS `ss` WHERE (`s`.`barcode` LIKE '%BD-N878/1418/%' OR `s`.`barcode` LIKE '%BD-N571/03/%') AND `s`.`id` = `ss`.`sku_id`
UPDATE `ws_goods_sku` AS `s`, `ws_scene_sku` AS `ss` SET `ss`.`status` = 'stop' WHERE (`s`.`barcode` LIKE '%BD-N878/1418/%' OR `s`.`barcode` LIKE '%BD-N571/03/%') AND `s`.`id` = `ss`.`sku_id`



SELECT `g`.`serial_num` AS `序号`,`g`.`name` AS `款式`, `g`.`color_str` AS `颜色`, `mu`.`mobile` AS `手机号`, `mu`.`nickname` AS `姓名`, `sc`.`star` AS `评分`, `sc`.`recommend` AS `推荐`, `sc`.`favour` AS `收藏`
FROM `workydd_business`.`wp_scene_comment` AS `sc`, `workydd_shop`.`ws_goods` as `g`, `workydd_merchant`.`wm_user` AS `mu` WHERE
`g`.`id` = `sc`.`goods_id` AND
`sc`.`user_id` = `mu`.`id`



UPDATE `ws_goods` SET `orderlist` = 100000 - `serial_num` WHERE `serail_num` != '';

SELECT * FROM `wt_user_plat` WHERE `name` LIKE '%王灿%' OR `name` LIKE '%振苍%' OR `name` LIKE '%Tim%' OR `name` LIKE '%召%';

SELECT `wp`.`name`,`wp`.`openid`, `m`.`name`,`m`.`mobile`, `m`.`nickname` FROM `wt_user_plat` AS `wp`, `workydd_merchant`.`wm_user` AS `m` WHERE (`wp`.`name` LIKE '%王灿%' OR `wp`.`name` LIKE '%振苍%' OR `wp`.`name` LIKE '%Tim%' OR `wp`.`name` LIKE '%召%' OR `wp`.`name` LIKE '%向光%' OR `wp`.`name` LIKE '%贝邦尼%' OR `wp`.`name` LIKE '%小太阳%' ) AND `m`.`id` = `wp`.`muser_id`


UPDATE `wm_tmp` AS `t`, `wm_user` AS `u` SET `t`.`user_id` = `u`.`id` WHERE `u`.`mobile` = `t`.`mobile`;
SELECT `t`.`saleman_id`, `t`.`name`, `mu`.`saleman_id` FROM `wm_tmp` AS `t`, `wm_merchant_user` AS `mu` WHERE `t`.`user_id` > 0 AND `t`.`user_id` = `mu`.`user_id` AND `t`.`saleman_id` != `mu`.`saleman_id`;


UPDATE `wm_tmp` SET `saleman_id` = 5 WHERE `saleman` = '刘京';
UPDATE `wm_tmp` SET `saleman_id` = 1 WHERE `saleman` = '史云鹏';
UPDATE `wm_tmp` SET `saleman_id` = 2 WHERE `saleman` = '郇娇娇';
UPDATE `wm_tmp` SET `saleman_id` = 6 WHERE `saleman` = '百荣';
UPDATE `wm_tmp` SET `saleman_id` =  WHERE `saleman` = '';

TRUNCATE `wp_business_account`;
TRUNCATE `wp_business_address`;
TRUNCATE `wp_business_base`;
TRUNCATE `wp_business_exlog`;
TRUNCATE `wp_business_express`;
TRUNCATE `wp_business_exrecord`;
TRUNCATE `wp_business_exstatus`;
TRUNCATE `wp_business_goods`;
TRUNCATE `wp_business_info`;
TRUNCATE `wp_business_log`;
TRUNCATE `wp_business_status`;
TRUNCATE `wp_mfund`;
TRUNCATE `wp_mfund_status`;
TRUNCATE `wp_scene_comment`;
TRUNCATE `wp_scene_user`;
TRUNCATE `wp_visitor`;
TRUNCATE `wp_visitor_callback`;


DELETE  FROM `ws_scene_grade` WHERE `scene_id` = 7;
DELETE  FROM `ws_scene_goods` WHERE `scene_id` = 7;
DELETE  FROM `ws_scene_sku` WHERE `scene_id` = 7;
DELETE  FROM `ws_scene_sku` WHERE `scene_id` = 7;



SELECT * FROM `wt_user_plat` WHERE `name` LIKE '%王灿%' OR `name` LIKE '%振苍%' OR `name` LIKE '%Tim%' OR `name` LIKE '%召%';
SELECT * FROM `wm_user` WHERE `mobile` IN ('13811974106', '15210637967', '15211112222', '13720016185');



ALTER TABLE `wp_auth_menu` ADD `base_path` VARCHAR(100) NOT NULL DEFAULT '' COMMENT '基础路径' AFTER `orderlist`, ADD `num_get` TINYINT(1) NOT NULL DEFAULT '0' COMMENT 'GET方法数量' AFTER `base_path`, ADD `num_post` TINYINT(1) NOT NULL DEFAULT '0' COMMENT 'POST方法数量' AFTER `num_get`;
UPDATE `wp_auth_menu` SET `num_get` = 2 WHERE `method` IN ('listinfo', 'view');
UPDATE `wp_auth_menu` SET `num_post` = 1 WHERE `method` IN ('add', 'update', 'delete');




UPDATE `wp_business_exrecord` SET `website_id` = 0, `goods_id` = 0, `website_sku_id` = 0, `sku_id` = 0, `status` = 0 WHERE 1;
UPDATE `wp_business_express` SET `status_deal` = 0, `orderid` = '', `orderid_express` = '', `docno_out` = '', `warehouse_id` = 0, `warehouse_id_target` = 0 WHERE 1;

INSERT INTO `worksale_shop`.`ws_website_warehouse`(`website_id`, `warehouse_id`, `status`) SELECT 1, `warehouse_id`, 1 FROM `ws_warehouse_goods` WHERE 1 GROUP BY `warehouse_id`


UPDATE `wm_user` AS `u`, `wm_teamwork` AS `t` SET `t`.`mobile` = `u`.`mobile` WHERE `u`.`id` = `t`.`user_id` AND `u`.`mobile` != `t`.`mobile`;

UPDATE `workydd_restapp`.`wp_business_express` SET `docno_out` = '', `orderid` = '', `orderid_express` = '', `status_deal` = 1 WHERE 1;

DELETE FROM `wm_merchant` WHERE `id` > 2;
TRUNCATE `wm_merchant_supplier`;
TRUNCATE `wm_merchant_user`;
DELETE FROM `wm_teamwork` WHERE `merchant_id` != 1;
ALTER TABLE `wm_teamwork` auto_increment = 26;
