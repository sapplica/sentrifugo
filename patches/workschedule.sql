/*Table structure for table `main_work_schedule` */

DROP TABLE IF EXISTS `main_work_schedule`;

CREATE TABLE `main_work_schedule` (                                                         
`id` int(11) NOT NULL AUTO_INCREMENT,                                                     
`businessunit_id` int(11) NOT NULL,                                                       
`department_id` int(11) NOT NULL,                                                         
`startdate` date DEFAULT NULL,
`enddate` date DEFAULT NULL,
`createdby` int(11) NOT NULL,                                                             
`modifiedby` int(11) DEFAULT NULL,                                                        
`createddate` datetime NOT NULL,                                                          
`modifieddate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,  
`isactive` tinyint(4) NOT NULL DEFAULT '1' COMMENT '0-No,1-Yes',                          
PRIMARY KEY (`id`)                                                                        
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

insert  into `main_menu`(`id`,`menuName`,`url`,`helpText`,`toolTip`,`iconPath`,`parent`,`menuOrder`,`nav_ids`,`isactive`,`modulename`,`segment_flag`,`org_menuid`,`menufields`,`menuQuery`,`hasJoins`,`modelName`,`functionName`,`defaultOrderBy`) 
values
(930,'Work Schedule','/workschedule','','','1346855803_eeoc.jpg',113,52,',3,113,930',1,'default',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL);

insert  into `main_privileges`(`role`,`group_id`,`object`,`addpermission`,`editpermission`,`deletepermission`,`viewpermission`,`uploadattachments`,`viewattachments`,`createdby`,`modifiedby`,`createddate`,`modifieddate`,`isactive`) 
values 
(1,NULL,930,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(NULL,1,930,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(NULL,3,930,'Yes','Yes','No','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(2,1,930,'Yes','Yes','Yes','Yes','No','No',1,1,NOW(),NOW(),1),
(4,3,930,'Yes','Yes','No','Yes','No','No',1,1,NOW(),NOW(),1);