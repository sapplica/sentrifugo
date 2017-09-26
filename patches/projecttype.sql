SET SQL_MODE='ALLOW_INVALID_DATES';

ALTER TABLE tm_projects 
MODIFY `project_type` varchar(255) NOT NULL;

/*Table structure for table `main_projecttype` */

DROP TABLE IF EXISTS `main_projecttype`;

CREATE TABLE `main_projecttype` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `projecttype` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `createdby` int(11) DEFAULT NULL,
  `modifiedby` int(11) DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `isactive` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `main_projecttype` */
insert  into `main_projecttype`
(`projecttype`,`description`,`createdby`,`modifiedby`,`createddate`,`modifieddate`,`isactive`)
values 
('Billable','Billable',NULL,NULL,NOW(),NOW(),'1'),
('Non billable','Non billable',NULL,NULL,NOW(),NOW(),'1'),
('Revenue','Revenue',NULL,NULL,NOW(),NOW(),'1');

insert  into `main_menu`(`id`,`menuName`,`url`,`helpText`,`toolTip`,`iconPath`,`parent`,`menuOrder`,`nav_ids`,`isactive`,`modulename`,`segment_flag`,`org_menuid`,`menufields`,`menuQuery`,`hasJoins`,`modelName`,`functionName`,`defaultOrderBy`) 
values
(920,'Project Type','/projecttype','','','1346855803_eeoc.jpg',113,51,',3,113,920',1,'default',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL);

insert  into `main_privileges`(`role`,`group_id`,`object`,`addpermission`,`editpermission`,`deletepermission`,`viewpermission`,`uploadattachments`,`viewattachments`,`createdby`,`modifiedby`,`createddate`,`modifieddate`,`isactive`) 
values 
(1,NULL,920,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(NULL,1,920,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(NULL,3,920,'Yes','Yes','No','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(2,1,920,'Yes','Yes','Yes','Yes','No','No',1,1,NOW(),NOW(),1),
(4,3,920,'Yes','Yes','No','Yes','No','No',1,1,NOW(),NOW(),1);