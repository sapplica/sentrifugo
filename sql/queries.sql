ALTER TABLE `main_menu` add column `modulename` varchar(50) NULL after `isactive`;

ALTER TABLE `main_sd_configurations` ADD COLUMN `request_for` TINYINT(1) DEFAULT 1 NULL COMMENT '1=service request,2=asset request' AFTER `service_desk_flag`;

ALTER TABLE `main_sd_requests` ADD COLUMN `request_for` TINYINT(1) DEFAULT 1 NULL COMMENT '1=service request, 2= asset request' AFTER `id`, CHANGE `service_desk_id` `service_desk_id` BIGINT(20) UNSIGNED NULL COMMENT 'if request_for is equal to 2 then dump id from asset table', CHANGE `service_request_id` `service_request_id` BIGINT(20) UNSIGNED NULL COMMENT 'If request_for is equal to 2 then dump category from asset table';

ALTER TABLE `main_sd_requests_summary` ADD COLUMN `request_for` TINYINT(1) DEFAULT 1 NULL COMMENT '1=service request,2=asset request' AFTER `id`,CHANGE `service_desk_id` `service_desk_id` BIGINT(20) UNSIGNED NULL COMMENT 'If request_for equal to 2 then dump asset id from asset table', CHANGE `service_desk_name` `service_desk_name` VARCHAR(250) CHARSET latin1 COLLATE latin1_swedish_ci NULL COMMENT 'If request_for equal to 2 then dump asset name from asset table', CHANGE `service_request_name` `service_request_name` VARCHAR(250) CHARSET latin1 COLLATE latin1_swedish_ci NULL COMMENT 'If request_for equal to 2 then dump asset category from asset table', CHANGE `service_request_id` `service_request_id` BIGINT(20) UNSIGNED NULL COMMENT 'If request_for equal to 2 then dump asset name from asset_categories table';

alter table `main_empsalarydetails` change `salary` `salary` varchar(100) NULL;

update main_menu set isactive=0 where id IN(63,64,135);
update main_privileges set isactive=0 where object IN(63,64,135,175);
update `main_menu` set `menuName`='My Leaves' where `id`='62';
update `main_menu` set `url`='/#',`parent`='149',`isactive`='1' where `id`='175';
update `main_menu` set `parent`='0',`isactive`='0' where `id`='155';

insert  into `main_menu`(`id`,`menuName`,`url`,`helpText`,`toolTip`,`iconPath`,`parent`,`menuOrder`,`nav_ids`,`isactive`,`modulename`,`segment_flag`,`org_menuid`,`menufields`,`menuQuery`,`hasJoins`,`modelName`,`functionName`,`defaultOrderBy`) values 
(185,'Expenses','/#','Add Employee Expenses','Add Employee Expenses',NULL,0,18,',185,',1,'expenses',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
(186,'Assets','/#','Add Company Assets','Add Company Assets',NULL,0,19,',186,',1,'assets',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
(187,'Vendors','/vendors','Add Vendor for Assets','Add Vendor for Assets',NULL,0,20,',187,',0,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
(188,'My Appraisal History','/appraisalhistoryself','My Appraisal History','My Appraisal History',NULL,175,1,',149,175,188,',1,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
(189,'Team Appraisal History','/appraisalhistoryteam','Team Appraisal History','Team Appraisal History',NULL,175,2,',149,175,189,',1,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
(190,'Asset Categories','/assets/assetcategories','Add category and sub cateegory for Assets','Add category and sub cateegory for Assets','',186,2,',186,190,',1,'assets',2,0,'','',0,'','',''),
(191,'Category','/expenses/expensecategories','Add category and sub cateegory for Expenses','Add category and sub cateegory for Expenses','',185,2,',185,191,',1,'expenses',2,0,'','',0,'','',''),
(192,'Payment Mode','/expenses/paymentmode','Add payment modes for Expenses','Add payment modes for Expenses','',185,3,',185,192,',1,'expenses',2,0,'','',0,'','',''),
(193,'Receipts','/expenses/receipts','Add receipts for Expenses','Add receipts for Expenses','',185,4,',185,193,',1,'expenses',2,0,'','',0,'','',''),
(194,'Trips','/expenses/trips','Add trips for Expenses','Add trips for Expenses','',185,5,',185,194,',1,'expenses',2,0,'','',0,'','',''),
(195,'Advances','/expenses/advances','Add advance for Employ','Add advance for Employ','',185,6,',185,195,',1,'expenses',2,0,'','',0,'','',''),
(196,'My Advances','/expenses/advances/myadvances','View list of my advances','View list of my advances','',195,7,',185,195,196,',1,'expenses',2,0,'','',0,'','',''),
(197,'Employee Advances','/expenses/employeeadvances','View list of Employee advances','View list of Employee advances','',195,7,',185,195,197,',1,'expenses',2,0,'','',0,'','',''),
(198,'Expenses','/expenses/expenses','Add Employee Expenses',NULL,NULL,185,1,',185,198,',1,'expenses',2,0,NULL,NULL,NULL,NULL,NULL,NULL),
(199,'My Employee Expenses','/expenses/myemployeeexpenses','Submitted Employee Expenses',NULL,NULL,185,9,',185,199,',1,'expenses',2,0,NULL,NULL,NULL,NULL,NULL,NULL),
(200,'Assets','/assets/assets','Assets',NULL,NULL,186,1,',186,200,',1,'assets',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL);


insert  into `main_privileges`(`role`,`group_id`,`object`,`addpermission`,`editpermission`,`deletepermission`,`viewpermission`,`uploadattachments`,`viewattachments`,`createdby`,`modifiedby`,`createddate`,`modifieddate`,`isactive`) values
(1,NULL,175,'No','No','No','Yes','No','No',1,1,now(),now(),1),
(NULL,1,175,'No','No','No','Yes','No','No',1,1,now(),now(),1),
(NULL,2,175,'No','No','No','Yes','No','No',1,1,now(),now(),1),
(NULL,3,175,'No','No','No','Yes','No','No',1,1,now(),now(),1),
(NULL,4,175,'No','No','No','Yes','No','No',1,1,now(),now(),1),
(NULL,6,175,'No','No','No','Yes','No','No',1,1,now(),now(),1),
(2,1,175,'No','No','No','Yes','No','No',1,1,now(),now(),1),
(3,2,175,'No','No','No','Yes','No','No',1,1,now(),now(),1),
(4,3,175,'No','No','No','Yes','No','No',1,1,now(),now(),1),
(5,4,175,'No','No','No','Yes','No','No',1,1,now(),now(),1),
(8,6,175,'No','No','No','Yes','No','No',1,1,now(),now(),1),
(9,4,175,'No','No','No','Yes','No','No',1,1,now(),now(),1),
(1,NULL,185,'Yes','Yes','Yes','Yes','No','No',1,1,NOW(),NOW(),1),
(NULL,1,185,'Yes','Yes','Yes','Yes','No','No',1,1,NOW(),NOW(),1),
(NULL,2,185,'Yes','Yes','Yes','Yes','No','No',1,1,NOW(),NOW(),1),
(NULL,3,185,'Yes','Yes','Yes','Yes','No','No',1,1,NOW(),NOW(),1),
(NULL,4,185,'Yes','Yes','Yes','Yes','No','No',1,1,NOW(),NOW(),1),
(NULL,6,185,'Yes','Yes','Yes','Yes','No','No',1,1,NOW(),NOW(),1),
(2,1,185,'Yes','Yes','Yes','Yes','No','No',1,1,NOW(),NOW(),1),
(3,2,185,'Yes','Yes','Yes','Yes','No','No',1,1,NOW(),NOW(),1),
(4,3,185,'Yes','Yes','Yes','Yes','No','No',1,1,NOW(),NOW(),1),
(5,4,185,'Yes','Yes','Yes','Yes','No','No',1,1,NOW(),NOW(),1),
(8,6,185,'Yes','Yes','Yes','Yes','No','No',1,1,NOW(),NOW(),1),
(9,4,185,'Yes','Yes','Yes','Yes','No','No',1,1,NOW(),NOW(),1),
(1,NULL,186,'Yes','Yes','Yes','Yes','No','No',1,1,NOW(),NOW(),1),
(NULL,1,186,'No','No','No','No','No','No',1,1,NOW(),NOW(),0),
(NULL,2,186,'No','No','No','No','No','No',1,1,NOW(),NOW(),0),
(NULL,3,186,'Yes','Yes','Yes','Yes','No','No',1,1,NOW(),NOW(),1),
(NULL,4,186,'No','No','No','No','No','No',1,1,NOW(),NOW(),0),
(NULL,6,186,'Yes','Yes','Yes','Yes','No','No',1,1,NOW(),NOW(),1),
(2,1,186,'No','No','No','No','No','No',1,1,NOW(),NOW(),0),
(3,2,186,'No','No','No','No','No','No',1,1,NOW(),NOW(),0),
(4,3,186,'Yes','Yes','Yes','Yes','No','No',1,1,NOW(),NOW(),1),
(5,4,186,'No','No','No','No','No','No',1,1,NOW(),NOW(),0),
(8,6,186,'Yes','Yes','Yes','Yes','No','No',1,1,NOW(),NOW(),1),
(9,4,186,'No','No','No','No','No','No',1,1,NOW(),NOW(),0),
(1,NULL,188,'No','No','No','Yes','No','No',1,1,now(),now(),1),
(NULL,1,188,'No','No','No','Yes','No','No',1,1,now(),now(),1),
(NULL,2,188,'No','No','No','Yes','No','No',1,1,now(),now(),1),
(NULL,3,188,'No','No','No','Yes','No','No',1,1,now(),now(),1),
(NULL,4,188,'No','No','No','Yes','No','No',1,1,now(),now(),1),
(NULL,6,188,'No','No','No','Yes','No','No',1,1,now(),now(),1),
(2,1,188,'No','No','No','Yes','No','No',1,1,now(),now(),1),
(3,2,188,'No','No','No','Yes','No','No',1,1,now(),now(),1),
(4,3,188,'No','No','No','Yes','No','No',1,1,now(),now(),1),
(5,4,188,'No','No','No','Yes','No','No',1,1,now(),now(),1),
(8,6,188,'No','No','No','Yes','No','No',1,1,now(),now(),1),
(9,4,188,'No','No','No','Yes','No','No',1,1,now(),now(),1),
(1,NULL,189,'No','No','No','Yes','No','No',1,1,now(),now(),1),
(NULL,1,189,'No','No','No','Yes','No','No',1,1,now(),now(),1),
(NULL,2,189,'No','No','No','Yes','No','No',1,1,now(),now(),1),
(NULL,3,189,'No','No','No','Yes','No','No',1,1,now(),now(),1),
(NULL,4,189,'No','No','No','Yes','No','No',1,1,now(),now(),1),
(NULL,6,189,'No','No','No','Yes','No','No',1,1,now(),now(),1),
(2,1,189,'No','No','No','Yes','No','No',1,1,now(),now(),1),
(3,2,189,'No','No','No','Yes','No','No',1,1,now(),now(),1),
(4,3,189,'No','No','No','Yes','No','No',1,1,now(),now(),1),
(5,4,189,'No','No','No','Yes','No','No',1,1,now(),now(),1),
(8,6,189,'No','No','No','Yes','No','No',1,1,now(),now(),1),
(9,4,189,'No','No','No','Yes','No','No',1,1,now(),now(),1),
(NULL,3,8,'No','No','No','No','No','No',1,1,now(),now(),1),
(4,3,8,'No','No','No','No','No','No',1,1,now(),now(),1),
(1,NULL,190,'Yes','Yes','Yes','Yes','No','No',1,1,NOW(),NOW(),1),
(NULL,1,190,'No','No','No','No','No','No',1,1,NOW(),NOW(),0),
(NULL,2,190,'No','No','No','No','No','No',1,1,NOW(),NOW(),0),
(NULL,3,190,'Yes','Yes','Yes','Yes','No','No',1,1,NOW(),NOW(),1),
(NULL,4,190,'No','No','No','No','No','No',1,1,NOW(),NOW(),0),
(NULL,6,190,'Yes','Yes','Yes','Yes','No','No',1,1,NOW(),NOW(),1),
(2,1,190,'No','No','No','No','No','No',1,1,NOW(),NOW(),0),
(3,2,190,'No','No','No','No','No','No',1,1,NOW(),NOW(),0),
(4,3,190,'Yes','Yes','Yes','Yes','No','No',1,1,NOW(),NOW(),1),
(5,4,190,'No','No','No','No','No','No',1,1,NOW(),NOW(),0),
(8,6,190,'Yes','Yes','Yes','Yes','No','No',1,1,NOW(),NOW(),1),
(9,4,190,'No','No','No','No','No','No',1,1,NOW(),NOW(),0),
(1,NULL,191,'Yes','Yes','Yes','Yes','No','No',1,1,NOW(),NOW(),1),
(NULL,1,191,'No','No','No','No','No','No',1,1,NOW(),NOW(),0),
(NULL,2,191,'No','No','No','No','No','No',1,1,NOW(),NOW(),0),
(NULL,3,191,'Yes','Yes','Yes','Yes','No','No',1,1,NOW(),NOW(),1),
(NULL,4,191,'No','No','No','No','No','No',1,1,NOW(),NOW(),0),
(NULL,6,191,'NO','No','No','No','No','No',1,1,NOW(),NOW(),0),
(2,1,191,'No','No','No','No','No','No',1,1,NOW(),NOW(),0),
(3,2,191,'No','No','No','No','No','No',1,1,NOW(),NOW(),0),
(4,3,191,'Yes','Yes','Yes','Yes','No','No',1,1,NOW(),NOW(),1),
(5,4,191,'No','No','No','No','No','No',1,1,NOW(),NOW(),0),
(8,6,191,'No','No','No','No','No','No',1,1,NOW(),NOW(),0),
(9,4,191,'No','No','No','No','No','No',1,1,NOW(),NOW(),0),
(1,NULL,192,'Yes','Yes','Yes','Yes','No','No',1,1,NOW(),NOW(),1),
(NULL,1,192,'No','No','No','No','No','No',1,1,NOW(),NOW(),0),
(NULL,2,192,'No','No','No','No','No','No',1,1,NOW(),NOW(),0),
(NULL,3,192,'Yes','Yes','Yes','Yes','No','No',1,1,NOW(),NOW(),1),
(NULL,4,192,'No','No','No','No','No','No',1,1,NOW(),NOW(),0),
(NULL,6,192,'NO','No','No','No','No','No',1,1,NOW(),NOW(),0),
(2,1,192,'No','No','No','No','No','No',1,1,NOW(),NOW(),0),
(3,2,192,'No','No','No','No','No','No',1,1,NOW(),NOW(),0),
(4,3,192,'Yes','Yes','Yes','Yes','No','No',1,1,NOW(),NOW(),1),
(5,4,192,'No','No','No','No','No','No',1,1,NOW(),NOW(),0),
(8,6,192,'No','No','No','No','No','No',1,1,NOW(),NOW(),0),
(9,4,192,'No','No','No','No','No','No',1,1,NOW(),NOW(),0),
(1,NULL,193,'No','No','No','No','No','No',1,1,NOW(),NOW(),1),
(NULL,1,193,'Yes','Yes','Yes','Yes','No','No',1,1,NOW(),NOW(),1),
(NULL,2,193,'Yes','Yes','Yes','Yes','No','No',1,1,NOW(),NOW(),1),
(NULL,3,193,'Yes','Yes','Yes','Yes','No','No',1,1,NOW(),NOW(),1),
(NULL,4,193,'Yes','Yes','Yes','Yes','No','No',1,1,NOW(),NOW(),1),
(NULL,6,193,'Yes','Yes','Yes','Yes','No','No',1,1,NOW(),NOW(),1),
(2,1,193,'Yes','Yes','Yes','Yes','No','No',1,1,NOW(),NOW(),1),
(3,2,193,'Yes','Yes','Yes','Yes','No','No',1,1,NOW(),NOW(),1),
(4,3,193,'Yes','Yes','Yes','Yes','No','No',1,1,NOW(),NOW(),1),
(5,4,193,'Yes','Yes','Yes','Yes','No','No',1,1,NOW(),NOW(),1),
(8,6,193,'Yes','Yes','Yes','Yes','No','No',1,1,NOW(),NOW(),1),
(9,4,193,'Yes','Yes','Yes','Yes','No','No',1,1,NOW(),NOW(),1),
(1,NULL,194,'No','No','No','No','No','No',1,1,NOW(),NOW(),1),
(NULL,1,194,'Yes','Yes','Yes','Yes','No','No',1,1,NOW(),NOW(),1),
(NULL,2,194,'Yes','Yes','Yes','Yes','No','No',1,1,NOW(),NOW(),1),
(NULL,3,194,'Yes','Yes','Yes','Yes','No','No',1,1,NOW(),NOW(),1),
(NULL,4,194,'Yes','Yes','Yes','Yes','No','No',1,1,NOW(),NOW(),1),
(NULL,6,194,'Yes','Yes','Yes','Yes','No','No',1,1,NOW(),NOW(),1),
(2,1,194,'Yes','Yes','Yes','Yes','No','No',1,1,NOW(),NOW(),1),
(3,2,194,'Yes','Yes','Yes','Yes','No','No',1,1,NOW(),NOW(),1),
(4,3,194,'Yes','Yes','Yes','Yes','No','No',1,1,NOW(),NOW(),1),
(5,4,194,'Yes','Yes','Yes','Yes','No','No',1,1,NOW(),NOW(),1),
(8,6,194,'Yes','Yes','Yes','Yes','No','No',1,1,NOW(),NOW(),1),
(9,4,194,'Yes','Yes','Yes','Yes','No','No',1,1,NOW(),NOW(),1),
(1,NULL,195,'No','No','No','No','No','No',1,1,NOW(),NOW(),1),
(NULL,1,195,'Yes','Yes','Yes','Yes','No','No',1,1,NOW(),NOW(),1),
(NULL,2,195,'Yes','Yes','Yes','Yes','No','No',1,1,NOW(),NOW(),1),
(NULL,3,195,'Yes','Yes','Yes','Yes','No','No',1,1,NOW(),NOW(),1),
(NULL,4,195,'Yes','Yes','Yes','Yes','No','No',1,1,NOW(),NOW(),1),
(NULL,6,195,'Yes','Yes','Yes','Yes','No','No',1,1,NOW(),NOW(),1),
(2,1,195,'Yes','Yes','Yes','Yes','No','No',1,1,NOW(),NOW(),1),
(3,2,195,'Yes','Yes','Yes','Yes','No','No',1,1,NOW(),NOW(),1),
(4,3,195,'Yes','Yes','Yes','Yes','No','No',1,1,NOW(),NOW(),1),
(5,4,195,'Yes','Yes','Yes','Yes','No','No',1,1,NOW(),NOW(),1),
(8,6,195,'Yes','Yes','Yes','Yes','No','No',1,1,NOW(),NOW(),1),
(9,4,195,'Yes','Yes','Yes','Yes','No','No',1,1,NOW(),NOW(),1),
(1,NULL,196,'No','No','No','No','No','No',1,1,NOW(),NOW(),1),
(NULL,1,196,'Yes','Yes','Yes','Yes','No','No',1,1,NOW(),NOW(),1),
(NULL,2,196,'Yes','Yes','Yes','Yes','No','No',1,1,NOW(),NOW(),1),
(NULL,3,196,'Yes','Yes','Yes','Yes','No','No',1,1,NOW(),NOW(),1),
(NULL,4,196,'Yes','Yes','Yes','Yes','No','No',1,1,NOW(),NOW(),1),
(NULL,6,196,'Yes','Yes','Yes','Yes','No','No',1,1,NOW(),NOW(),1),
(2,1,196,'Yes','Yes','Yes','Yes','No','No',1,1,NOW(),NOW(),1),
(3,2,196,'Yes','Yes','Yes','Yes','No','No',1,1,NOW(),NOW(),1),
(4,3,196,'Yes','Yes','Yes','Yes','No','No',1,1,NOW(),NOW(),1),
(5,4,196,'Yes','Yes','Yes','Yes','No','No',1,1,NOW(),NOW(),1),
(8,6,196,'Yes','Yes','Yes','Yes','No','No',1,1,NOW(),NOW(),1),
(9,4,196,'Yes','Yes','Yes','Yes','No','No',1,1,NOW(),NOW(),1),
(1,NULL,197,'No','No','No','No','No','No',1,1,NOW(),NOW(),1),
(NULL,1,197,'Yes','Yes','Yes','Yes','No','No',1,1,NOW(),NOW(),1),
(NULL,2,197,'Yes','Yes','Yes','Yes','No','No',1,1,NOW(),NOW(),1),
(NULL,3,197,'Yes','Yes','Yes','Yes','No','No',1,1,NOW(),NOW(),1),
(NULL,4,197,'Yes','Yes','Yes','Yes','No','No',1,1,NOW(),NOW(),1),
(NULL,6,197,'Yes','Yes','Yes','Yes','No','No',1,1,NOW(),NOW(),1),
(2,1,197,'Yes','Yes','Yes','Yes','No','No',1,1,NOW(),NOW(),1),
(3,2,197,'Yes','Yes','Yes','Yes','No','No',1,1,NOW(),NOW(),1),
(4,3,197,'Yes','Yes','Yes','Yes','No','No',1,1,NOW(),NOW(),1),
(5,4,197,'Yes','Yes','Yes','Yes','No','No',1,1,NOW(),NOW(),1),
(8,6,197,'Yes','Yes','Yes','Yes','No','No',1,1,NOW(),NOW(),1),
(9,4,197,'Yes','Yes','Yes','Yes','No','No',1,1,NOW(),NOW(),1),
(1,NULL,198,'No','No','No','No','No','No',1,1,NOW(),NOW(),1),
(NULL,1,198,'Yes','Yes','Yes','Yes','No','No',1,1,NOW(),NOW(),1),
(NULL,2,198,'Yes','Yes','Yes','Yes','No','No',1,1,NOW(),NOW(),1),
(NULL,3,198,'Yes','Yes','Yes','Yes','No','No',1,1,NOW(),NOW(),1),
(NULL,4,198,'Yes','Yes','Yes','Yes','No','No',1,1,NOW(),NOW(),1),
(NULL,6,198,'Yes','Yes','Yes','Yes','No','No',1,1,NOW(),NOW(),1),
(2,1,198,'Yes','Yes','Yes','Yes','No','No',1,1,NOW(),NOW(),1),
(3,2,198,'Yes','Yes','Yes','Yes','No','No',1,1,NOW(),NOW(),1),
(4,3,198,'Yes','Yes','Yes','Yes','No','No',1,1,NOW(),NOW(),1),
(5,4,198,'Yes','Yes','Yes','Yes','No','No',1,1,NOW(),NOW(),1),
(8,6,198,'Yes','Yes','Yes','Yes','No','No',1,1,NOW(),NOW(),1),
(9,4,198,'Yes','Yes','Yes','Yes','No','No',1,1,NOW(),NOW(),1),
(1,NULL,199,'No','No','No','No','No','No',1,1,NOW(),NOW(),1),
(NULL,1,199,'Yes','Yes','Yes','Yes','No','No',1,1,NOW(),NOW(),1),
(NULL,2,199,'Yes','Yes','Yes','Yes','No','No',1,1,NOW(),NOW(),1),
(NULL,3,199,'Yes','Yes','Yes','Yes','No','No',1,1,NOW(),NOW(),1),
(NULL,4,199,'Yes','Yes','Yes','Yes','No','No',1,1,NOW(),NOW(),1),
(NULL,6,199,'Yes','Yes','Yes','Yes','No','No',1,1,NOW(),NOW(),1),
(2,1,199,'Yes','Yes','Yes','Yes','No','No',1,1,NOW(),NOW(),1),
(3,2,199,'Yes','Yes','Yes','Yes','No','No',1,1,NOW(),NOW(),1),
(4,3,199,'Yes','Yes','Yes','Yes','No','No',1,1,NOW(),NOW(),1),
(5,4,199,'Yes','Yes','Yes','Yes','No','No',1,1,NOW(),NOW(),1),
(8,6,199,'Yes','Yes','Yes','Yes','No','No',1,1,NOW(),NOW(),1),
(9,4,199,'Yes','Yes','Yes','Yes','No','No',1,1,NOW(),NOW(),1),
(1,NULL,200,'Yes','Yes','Yes','Yes','No','No',1,1,NOW(),NOW(),1),
(NULL,1,200,'No','No','No','No','No','No',1,1,NOW(),NOW(),0),
(NULL,2,200,'No','No','No','No','No','No',1,1,NOW(),NOW(),0),
(NULL,3,200,'Yes','Yes','Yes','Yes','No','No',1,1,NOW(),NOW(),1),
(NULL,4,200,'No','No','No','No','No','No',1,1,NOW(),NOW(),0),
(NULL,6,200,'Yes','Yes','Yes','Yes','No','No',1,1,NOW(),NOW(),1),
(2,1,200,'No','No','No','No','No','No',1,1,NOW(),NOW(),0),
(3,2,200,'No','No','No','No','No','No',1,1,NOW(),NOW(),0),
(4,3,200,'Yes','Yes','Yes','Yes','No','No',1,1,NOW(),NOW(),1),
(5,4,200,'No','No','No','No','No','No',1,1,NOW(),NOW(),0),
(8,6,200,'Yes','Yes','Yes','Yes','No','No',1,1,NOW(),NOW(),1),
(9,4,200,'No','No','No','No','No','No',1,1,NOW(),NOW(),0),
(NULL,3,8,'No','No','No','No','No','No',1,1,now(),now(),1),
(4,3,8,'No','No','No','No','No','No',1,1,now(),now(),1);


CREATE TABLE `assets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category` int(50) NOT NULL,
  `sub_category` int(50) NOT NULL,
  `company_asset_code` int(50) NOT NULL,
  `name` varchar(50) NOT NULL,
  `location` varchar(15) NOT NULL,
  `allocated_to` int(11) DEFAULT NULL,
  `responsible_technician` int(50) NOT NULL,
  `vendor` int(11) NOT NULL,
  `asset_classification` varchar(50) NOT NULL,
  `purchase_date` date DEFAULT NULL,
  `invoice_number` varchar(11) NOT NULL,
  `manufacturer` varchar(50) NOT NULL,
  `key_number` varchar(11) NOT NULL,
  `warenty_status` enum('Yes','No') NOT NULL,
  `warenty_end_date` date DEFAULT NULL,
  `is_working` enum('No','Yes') NOT NULL,
  `notes` text,
  `image` text,
  `imagencrpname` text NOT NULL,
  `qr_image` text NOT NULL,
  `isactive` tinyint(4) NOT NULL,
  `created_by` int(50) NOT NULL,
  `modified_by` int(50) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `modified` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `assets_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET latin1 NOT NULL,
  `parent` int(11) NOT NULL,
  `is_active` tinyint(11) NOT NULL DEFAULT '1',
  `created_by` int(11) NOT NULL,
  `modified_by` varchar(11) CHARACTER SET latin1 NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `modified` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=198 DEFAULT CHARSET=utf8;

CREATE TABLE `assets_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `asset_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `history` varchar(500) DEFAULT NULL,
  `isactive` tinyint(1) DEFAULT NULL,
  `createdby` int(11) DEFAULT NULL,
  `modifiedby` int(11) DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `expense_advacne_summary` (
  `id` int(11) NOT NULL auto_increment,
  `employee_id` int(11) default NULL,
  `total` float(10,2) default NULL,
  `utilized` float(10,2) default NULL,
  `returned` float(10,2) default NULL,
  `balance` float(10,2) default NULL,
  `createdby` int(11) default NULL,
  `modifiedby` int(11) default NULL,
  `createddate` datetime default NULL,
  `modifieddate` datetime default NULL,
  `isactive` tinyint(1) default '1',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `UNIQUEEMP` (`employee_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `expense_advance` (
  `id` int(11) NOT NULL auto_increment,
  `type` enum('advance','return') default 'advance',
  `from_id` int(11) default NULL,
  `to_id` int(11) default NULL,
  `payment_ref_number` varchar(200) default NULL,
  `payment_mode_id` int(11) default NULL,
  `project_id` int(11) default NULL,
  `currency_id` int(11) default NULL,
  `amount` float(10,2) default NULL,
  `application_currency_id` int(11) default NULL,
  `application_amount` float(10,2) default NULL,
  `advance_conversion_rate` float(10,2) default NULL,
  `description` text,
  `createdby` int(11) default NULL,
  `modifiedby` int(11) default NULL,
  `createddate` datetime NOT NULL,
  `modifieddate` datetime default NULL,
  `isactive` tinyint(1) default '1',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `expense_categories` (
  `id` int(11) NOT NULL auto_increment,
  `expense_category_name` varchar(100) default NULL,
  `unit_price` varchar(50) default NULL,
  `unit_name` varchar(50) default NULL,
  `createdby` int(11) default NULL,
  `modifiedby` int(11) default NULL,
  `created_date` datetime default NULL,
  `modifieddate` datetime default NULL,
  `isactive` tinyint(1) default '1',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `expense_forward` (
  `id` int(11) NOT NULL auto_increment,
  `expense_id` int(11) default NULL,
  `trip_id` int(11) default NULL,
  `from_id` int(11) default NULL,
  `to_id` int(11) default NULL,
  `createdby` int(11) default NULL,
  `modifiedby` int(11) default NULL,
  `createddate` datetime default NULL,
  `modifieddate` datetime default NULL,
  `isactive` tinyint(1) default '1',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `expense_history` (
  `id` int(11) NOT NULL auto_increment,
  `expense_id` int(11) default NULL,
  `trip_id` int(11) default NULL,
  `history` varchar(500) default NULL,
  `createdby` int(11) default NULL,
  `modifiedby` int(11) default NULL,
  `createddate` datetime default NULL,
  `modifieddate` datetime default NULL,
  `isactive` tinyint(1) default '1',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `expense_notifications` (
  `id` int(11) NOT NULL auto_increment,
  `expense_id` int(11) default NULL,
  `trip_id` int(11) default NULL,
  `notification` varchar(500) default NULL,
  `link` varchar(200) default NULL,
  `createdby` int(11) default NULL,
  `modifiedby` int(11) default NULL,
  `createddate` datetime default NULL,
  `modifieddate` datetime default NULL,
  `isactive` tinyint(1) default '1',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `expense_payment_methods` (
  `id` int(11) NOT NULL auto_increment,
  `payment_method_name` varchar(100) default NULL,
  `createdby` int(11) default NULL,
  `modifiedby` int(11) default NULL,
  `created_date` datetime default NULL,
  `modifieddate` datetime default NULL,
  `isactive` tinyint(1) default '1',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `expense_receipts` (
  `id` int(11) NOT NULL auto_increment,
  `expense_id` int(11) default NULL,
  `trip_id` int(11) default NULL,
  `receipt_name` varchar(100) default NULL COMMENT 'orginal file name',
  `receipt_filename` varchar(100) default NULL COMMENT 'auto generated file name',
  `receipt_file_type` varchar(5) default NULL,
  `createdby` int(11) default NULL,
  `modifiedby` int(11) default NULL,
  `createddate` datetime default NULL,
  `modifieddate` datetime default NULL,
  `isactive` tinyint(1) default '1',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `expense_return_advance` (
  `id` int(11) NOT NULL auto_increment,
  `from_id` int(11) default NULL,
  `to_id` int(11) default NULL,
  `currency_id` int(11) default NULL,
  `returned_amount` float(10,2) default NULL,
  `createdby` int(11) default NULL,
  `modifiedby` int(11) default NULL,
  `createddate` datetime default NULL,
  `modifieddate` datetime default NULL,
  `isactive` tinyint(1) default '1',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `expense_trip_history` (
  `id` int(11) NOT NULL auto_increment,
  `trip_id` int(11) default NULL,
  `expense_id` int(11) default NULL,
  `history` varchar(500) default NULL,
  `createdby` int(11) default NULL,
  `modifiedby` int(11) default NULL,
  `createddate` datetime default NULL,
  `modifieddate` datetime default NULL,
  `isactive` tinyint(1) default '1',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `expense_trips` (
  `id` int(11) NOT NULL auto_increment,
  `manager_id` int(11) default NULL,
  `trip_name` varchar(100) default NULL,
  `from_date` date default NULL,
  `to_date` date default NULL,
  `description` text,
  `status` enum('NS','S','A','R') default 'NS' COMMENT 'NS-Notsubmitted,S-submitted,R-Rejected,A-Approved',
  `rejected_note` text,
  `createdby` int(11) default NULL,
  `modifiedby` int(11) default NULL,
  `createddate` datetime default NULL,
  `modifieddate` datetime default NULL,
  `isactive` tinyint(1) default '1',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `expenses` (
  `id` int(11) NOT NULL auto_increment,
  `expense_name` varchar(100) default NULL,
  `category_id` int(11) default NULL,
  `project_id` int(11) default NULL,
  `client_id` int(11) default NULL,
  `trip_id` int(11) default NULL,
  `manager_id` int(11) default NULL,
  `expense_date` date default NULL,
  `expense_currency_id` int(11) default NULL,
  `expense_amount` float(10,2) default NULL,
  `expense_conversion_rate` float(5,2) default NULL,
  `application_currency_id` int(11) default NULL,
  `application_amount` float(10,2) default NULL,
  `advance_amount` float(10,2) default NULL,
  `is_reimbursable` tinyint(1) default NULL,
  `is_from_advance` tinyint(1) default '0',
  `expense_payment_id` int(11) default NULL,
  `expense_payment_ref_no` varchar(200) default NULL,
  `description` text,
  `status` enum('saved','submitted','approved','rejected') default 'saved',
  `createdby` int(11) default NULL,
  `modifiedby` int(11) default NULL,
  `createddate` datetime default NULL,
  `modifieddate` datetime default NULL,
  `isactive` tinyint(1) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DELIMITER $$

DROP TRIGGER `main_sd_request_aft_ins`$$

CREATE
    TRIGGER `main_sd_request_aft_ins` AFTER INSERT ON `main_sd_requests` 
    FOR EACH ROW BEGIN
	DECLARE x_service_desk_name,x_service_request_name,x_raised_by_name,x_executor_name,
		x_reporting_manager_name,x_approver_1_name,x_approver_2_name,x_approver_3_name,raised_empid,raised_img
		VARCHAR(250);
	
	IF(new.request_for=1) THEN
	SELECT service_desk_name INTO x_service_desk_name FROM main_sd_depts WHERE id = new.service_desk_id;
	SELECT service_request_name INTO x_service_request_name FROM main_sd_reqtypes WHERE id = new.service_request_id;
	ELSE
	SELECT NAME INTO x_service_desk_name FROM assets WHERE id = new.service_desk_id;
	SELECT NAME INTO x_service_request_name FROM assets_categories WHERE id = new.service_request_id AND parent=0;
	END IF;
	SELECT userfullname,employeeId,profileimg INTO x_raised_by_name,raised_empid,raised_img FROM main_employees_summary WHERE user_id = new.raised_by;
	SELECT userfullname INTO x_executor_name FROM main_employees_summary WHERE user_id = new.executor_id;
	SELECT userfullname INTO x_reporting_manager_name FROM main_employees_summary WHERE user_id = new.reporting_manager_id;
	SELECT userfullname INTO x_approver_1_name FROM main_employees_summary WHERE user_id = new.approver_1;
	SELECT userfullname INTO x_approver_2_name FROM main_employees_summary WHERE user_id = new.approver_2;
	SELECT userfullname INTO x_approver_3_name FROM main_employees_summary WHERE user_id = new.approver_3;
	
	INSERT INTO main_sd_requests_summary (
	request_for,sd_requests_id, service_desk_id, service_desk_name, service_desk_conf_id, service_request_name, service_request_id,
	priority, description, attachment, STATUS, raised_by, raised_by_name, ticket_number, executor_id, executor_name, executor_comments,
	reporting_manager_id, reporting_manager_name, approver_status_1, approver_status_2, approver_status_3, reporting_manager_status,
	approver_1, approver_1_name, approver_2, approver_2_name, approver_3, approver_3_name, isactive, createdby, modifiedby,
	createddate, modifieddate,raised_by_empid,approver_1_comments,approver_2_comments,approver_3_comments,reporting_manager_comments,
	to_mgmt_comments,to_manager_comments
	)
	VALUES	(	
	new.request_for,new.id, new.service_desk_id, x_service_desk_name, new.service_desk_conf_id, x_service_request_name, new.service_request_id,
	new.priority, new.description, new.attachment, new.status, new.raised_by, x_raised_by_name, new.ticket_number, new.executor_id,
	x_executor_name, new.executor_comments,	new.reporting_manager_id, x_reporting_manager_name, new.approver_status_1,
	new.approver_status_2, new.approver_status_3, new.reporting_manager_status, new.approver_1, x_approver_1_name, new.approver_2,
	x_approver_2_name, new.approver_3, x_approver_3_name, new.isactive, new.createdby, new.modifiedby, new.createddate, new.modifieddate,
        raised_empid,new.approver_1_comments,new.approver_2_comments,new.approver_3_comments,new.reporting_manager_comments,
	new.to_mgmt_comments,new.to_manager_comments
	);
	INSERT INTO main_request_history(request_id,description,emp_id,emp_name,createdby,modifiedby,createddate,modifieddate,isactive,emp_profileimg)
	VALUE (new.id,CONCAT(CONCAT(UCASE(LEFT(x_service_desk_name, 1)), SUBSTRING(x_service_desk_name, 2)) ,' Request has been raised by '),new.raised_by,CONCAT(UCASE(LEFT(x_raised_by_name, 1)), SUBSTRING(x_raised_by_name, 2)),new.createdby,new.createdby,new.createddate,new.modifieddate,new.isactive,raised_img);
    END;
	
$$

DELIMITER ;

DELIMITER $$

DROP TRIGGER `main_sd_request_aft_upd`$$

CREATE
    TRIGGER `main_sd_request_aft_upd` AFTER UPDATE ON `main_sd_requests` 
    FOR EACH ROW BEGIN
	DECLARE x_service_desk_name,x_service_request_name,x_raised_by_name,x_executor_name,
		x_reporting_manager_name,x_approver_1_name,x_approver_2_name,x_approver_3_name
		VARCHAR(250);
	
	IF(new.request_for=1) THEN
	SELECT service_desk_name INTO x_service_desk_name FROM main_sd_depts WHERE id = new.service_desk_id;
	SELECT service_request_name INTO x_service_request_name FROM main_sd_reqtypes WHERE id = new.service_request_id;
	ELSE
	SELECT NAME INTO x_service_desk_name FROM assets WHERE id = new.service_desk_id;
	SELECT NAME INTO x_service_request_name FROM assets_categories WHERE id = new.service_request_id AND parent=0;
	END IF;
	SELECT userfullname INTO x_raised_by_name FROM main_employees_summary WHERE user_id = new.raised_by;
	SELECT userfullname INTO x_executor_name FROM main_employees_summary WHERE user_id = new.executor_id;
	SELECT userfullname INTO x_reporting_manager_name FROM main_employees_summary WHERE user_id = new.reporting_manager_id;
	SELECT userfullname INTO x_approver_1_name FROM main_employees_summary WHERE user_id = new.approver_1;
	SELECT userfullname INTO x_approver_2_name FROM main_employees_summary WHERE user_id = new.approver_2;
	SELECT userfullname INTO x_approver_3_name FROM main_employees_summary WHERE user_id = new.approver_3;
	
	UPDATE main_sd_requests_summary SET
	request_for=new.request_for,service_desk_id = new.service_desk_id, service_desk_name = x_service_desk_name, service_desk_conf_id = new.service_desk_conf_id,
	service_request_name = x_service_request_name, service_request_id = new.service_request_id, priority = new.priority,
	description = new.description, attachment = new.attachment, STATUS = new.status, raised_by = new.raised_by,
	raised_by_name = x_raised_by_name, ticket_number = new.ticket_number, executor_id = new.executor_id, executor_name = x_executor_name,
	executor_comments = new.executor_comments, reporting_manager_id = new.reporting_manager_id, reporting_manager_name = x_reporting_manager_name,
	approver_status_1 = new.approver_status_1, approver_status_2 = new.approver_status_2, approver_status_3 = new.approver_status_3,
	reporting_manager_status = new.reporting_manager_status, approver_1 = new.approver_1, approver_1_name = x_approver_1_name,
	approver_2 = new.approver_2, approver_2_name = x_approver_2_name, approver_3 = new.approver_3, approver_3_name = x_approver_3_name,
	isactive = new.isactive, createdby = new.createdby, modifiedby = new.modifiedby, createddate = new.createddate, modifieddate = new.modifieddate
	,approver_1_comments = new.approver_1_comments,approver_2_comments = new.approver_2_comments,approver_3_comments = new.approver_3_comments,reporting_manager_comments = new.reporting_manager_comments,
	to_mgmt_comments = new.to_mgmt_comments,to_manager_comments = new.to_manager_comments
	WHERE sd_requests_id = new.id;
    END;
	
$$

DELIMITER ;