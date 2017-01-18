
/*Data for the table `main_menu` */
insert  into `main_menu`(`id`,`menuName`,`url`,`helpText`,`toolTip`,`iconPath`,`parent`,`menuOrder`,`nav_ids`,`isactive`,`modulename`,`segment_flag`,`org_menuid`,`menufields`,`menuQuery`,`hasJoins`,`modelName`,`functionName`,`defaultOrderBy`) values
(900,'On Call Management','/#','','','1346859254_vacation_main.jpg',3,50,',3,900,',1,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
(901,'On Call','/#','','','1346863688_vacation.jpg',4,50,',4,901,',1,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
(902,'On Call Management Options','/oncallmanagement','On Call Management Options','On Call Management Options','leave-management-options.jpg',900,1,',3,900,902,',1,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
(903,'Employee On Call Summary','/emponcallsummary','Employee On Call Summary','Employee On Call Summary','employee-leave-summary.jpg',900,2,',3,900,903,',1,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
(904,'On Call Request','/oncallrequest','On Call Request','On Call Request','1346863776_vacation_request.jpg',901,1,',4,901,904,',1,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
(905,'My On Call','/pendingoncalls','Pending On Call','Pending On Call','1346870194_pending-vacation-requests.png',901,2,',4,901,905,',1,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
(906,'Approved On Call','/approvedoncalls','Approved On Call','Approved On Call','1346863728_approved_vacations.jpg',901,3,',4,901,906,',0,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
(907,'Cancelled On Call','/canceloncalls','Cancel On Call','Cancel On Call','1346863749_cancel_vacation_history.jpg',901,4,',4,901,907,',0,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
(908,'Manager Employee On Call','/manageremployeevacations','Manager Employee Vacations','Manager Employee Vacations','1346863764_manager_employee_vacations.jpg',901,6,',4,901,908,',1,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
(909,'On Call Types','/employeeoncalltypes','','','leave-types.jpg',113,50,',3,113,909',1,'default',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
(910,'Rejected On Call','/rejectedoncalls','','','rejected-leaves.jpg',901,5,',4,901,910,',0,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
(911,'Add Employee On Call','/addemployeeoncalls','Add Employee On Call','Add Employee On Call','addemployeeleaves.jpg',900,3,',3,900,911',1,'default',2,302,NULL,NULL,NULL,NULL,NULL,NULL)
;

insert into `main_privileges`(`role`,`group_id`,`object`,`addpermission`,`editpermission`,`deletepermission`,`viewpermission`,`uploadattachments`,`viewattachments`,`createdby`,`modifiedby`,`createddate`,`modifieddate`,`isactive`)
values
(1,NULL,900,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(NULL,1,900,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(1,NULL,901,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(NULL,1,901,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(1,NULL,902,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(NULL,1,902,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(1,NULL,903,'No','No','No','Yes','No','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(NULL,1,903,'No','No','No','Yes','No','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(1,NULL,904,'Yes','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(1,NULL,905,'No','No','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(1,NULL,906,'No','No','No','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),
(1,NULL,907,'No','No','No','Yes','No','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),
(1,NULL,908,'No','Yes','No','Yes','No','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(1,NULL,909,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(1,NULL,910,'No','No','No','Yes','No','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),
(NULL,1,904,'Yes','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(NULL,1,905,'No','No','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(NULL,1,906,'No','No','No','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),
(NULL,1,907,'No','No','No','Yes','No','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),
(NULL,1,908,'No','Yes','No','Yes','No','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(NULL,1,909,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(NULL,1,910,'No','No','No','Yes','No','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),
(NULL,2,901,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(NULL,2,904,'Yes','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(NULL,2,905,'No','No','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(NULL,2,906,'No','No','No','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),
(NULL,2,907,'No','No','No','Yes','No','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),
(NULL,2,908,'No','Yes','No','Yes','No','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(NULL,2,910,'No','No','No','Yes','No','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),
(NULL,3,900,'Yes','Yes','No','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(NULL,3,901,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(NULL,3,902,'Yes','Yes','No','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(NULL,3,903,'No','No','No','Yes','No','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(NULL,3,904,'Yes','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(NULL,3,905,'No','No','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(NULL,3,906,'No','No','No','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),
(NULL,3,907,'No','No','No','Yes','No','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),
(NULL,3,908,'No','Yes','No','Yes','No','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(NULL,3,909,'Yes','Yes','No','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(NULL,3,910,'No','No','No','Yes','No','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),
(NULL,4,901,'Yes','Yes','Yes','Yes','Yes','Yes',NULL,NULL,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(NULL,4,904,'Yes','No','No','No','No','No',NULL,NULL,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(NULL,4,905,'No','No','Yes','Yes','Yes','Yes',NULL,NULL,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(NULL,4,906,'No','No','No','Yes','Yes','Yes',NULL,NULL,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),
(NULL,4,907,'No','No','No','Yes','No','Yes',NULL,NULL,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),
(NULL,4,908,'No','Yes','No','Yes','No','Yes',NULL,NULL,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(NULL,4,910,'No','No','No','Yes','No','Yes',NULL,NULL,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),
(NULL,6,901,'Yes','Yes','Yes','Yes','Yes','Yes',NULL,NULL,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(NULL,6,904,'Yes','No','No','No','No','No',NULL,NULL,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(NULL,6,905,'No','No','Yes','Yes','Yes','Yes',NULL,NULL,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(NULL,6,906,'No','No','No','Yes','Yes','Yes',NULL,NULL,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),
(NULL,6,907,'No','No','No','Yes','No','Yes',NULL,NULL,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),
(NULL,6,908,'No','Yes','No','Yes','No','Yes',NULL,NULL,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(NULL,6,910,'No','No','No','Yes','No','Yes',NULL,NULL,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),
(2,1,901,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(2,1,904,'Yes','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(2,1,905,'No','No','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(2,1,906,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),
(2,1,907,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),
(2,1,910,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),
(2,1,908,'No','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(2,1,900,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(2,1,902,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(2,1,903,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(3,2,901,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(3,2,904,'Yes','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(3,2,905,'No','No','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(3,2,906,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),
(3,2,907,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),
(3,2,910,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),
(3,2,908,'No','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(4,3,901,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(4,3,904,'Yes','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(4,3,905,'No','No','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(4,3,906,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),
(4,3,907,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),
(4,3,910,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),
(4,3,908,'No','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(4,3,900,'Yes','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(4,3,902,'Yes','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(4,3,903,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(5,4,901,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(5,4,904,'Yes','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(5,4,905,'No','No','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(5,4,906,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),
(5,4,907,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),
(5,4,910,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),
(5,4,908,'No','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(8,6,901,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(8,6,904,'Yes','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(8,6,905,'No','No','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(8,6,906,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),
(8,6,907,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),
(8,6,910,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),
(8,6,908,'No','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(9,4,901,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(9,4,904,'Yes','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(9,4,905,'No','No','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(9,4,906,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),
(9,4,907,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),
(9,4,910,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),
(9,4,908,'No','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(1,NULL,911,'Yes','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(NULL,1,911,'Yes','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(NULL,3,911,'Yes','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(2,1,911,'Yes','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(4,3,911,'Yes','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1)
;

/*Table structure for table `main_allottedoncalllog` */

DROP TABLE IF EXISTS `main_allottedoncalllog`;

CREATE TABLE `main_allottedoncalllog` (
  `id` bigint(11) unsigned NOT NULL AUTO_INCREMENT,
  `userid` bigint(11) unsigned DEFAULT NULL,
  `assignedoncall` int(5) DEFAULT NULL,
  `totaloncall` int(5) DEFAULT NULL,
  `year` int(5) DEFAULT NULL,
  `createdby` bigint(11) unsigned DEFAULT NULL,
  `modifiedby` bigint(11) unsigned DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `isactive` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `main_allottedoncalllog` */

/*Table structure for table `main_employeeoncall` */

DROP TABLE IF EXISTS `main_employeeoncall`;

CREATE TABLE `main_employeeoncall` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `emp_oncall_limit` float DEFAULT NULL,
  `used_oncall` float DEFAULT NULL,
  `alloted_year` year(4) DEFAULT NULL,
  `createdby` int(11) unsigned DEFAULT NULL,
  `modifiedby` int(11) unsigned DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `isactive` tinyint(1) DEFAULT '1',
  `isoncalltrasnferset` tinyint(1) DEFAULT '0' COMMENT '0-Notset,1-set',
  PRIMARY KEY (`id`),
  UNIQUE KEY `User_year` (`user_id`,`alloted_year`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `main_employeeoncall` */

/*Table structure for table `main_employeeoncalltypes` */

DROP TABLE IF EXISTS `main_employeeoncalltypes`;

CREATE TABLE `main_employeeoncalltypes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `oncalltype` varchar(255) DEFAULT NULL,
  `numberofdays` int(11) DEFAULT NULL,
  `oncallcode` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `oncallpreallocated` tinyint(4) DEFAULT NULL COMMENT '1-yes,2-No',
  `oncallpredeductable` tinyint(4) DEFAULT NULL COMMENT '1-yes,2-No',
  `createdby` int(11) unsigned DEFAULT NULL,
  `modifiedby` int(11) unsigned DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `isactive` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `main_employeeoncalltypes` */

/*Table structure for table `main_oncallmanagement` */

DROP TABLE IF EXISTS `main_oncallmanagement`;

CREATE TABLE `main_oncallmanagement` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `cal_startmonth` int(11) unsigned DEFAULT NULL,
  `weekend_startday` int(11) unsigned DEFAULT NULL,
  `weekend_endday` int(11) unsigned DEFAULT NULL,
  `businessunit_id` int(11) unsigned DEFAULT NULL,
  `department_id` int(11) unsigned DEFAULT '0',
  `hours_day` int(11) DEFAULT NULL,
  `is_satholiday` tinyint(1) DEFAULT NULL COMMENT '1-Yes,2-No',
  `is_halfday` tinyint(1) DEFAULT NULL COMMENT '1-Yes,2-No',
  `is_oncalltransfer` tinyint(1) DEFAULT NULL COMMENT '1-Yes,2-No',
  `is_skipholidays` tinyint(1) DEFAULT NULL COMMENT '1-Yes,2-No',
  `description` varchar(255) DEFAULT NULL,
  `createdby` int(11) DEFAULT NULL,
  `modifiedby` int(11) DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `isactive` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `main_oncallmanagement` */

/*Table structure for table `main_oncallmanagement_summary` */

DROP TABLE IF EXISTS `main_oncallmanagement_summary`;

CREATE TABLE `main_oncallmanagement_summary` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `oncallmgmt_id` bigint(20) unsigned DEFAULT NULL,
  `cal_startmonth` int(11) unsigned DEFAULT NULL,
  `cal_startmonthname` varchar(100) DEFAULT NULL,
  `weekend_startday` int(11) unsigned DEFAULT NULL,
  `weekend_startdayname` varchar(100) DEFAULT NULL,
  `weekend_endday` int(11) unsigned DEFAULT NULL,
  `weekend_enddayname` varchar(100) DEFAULT NULL,
  `businessunit_id` int(11) unsigned DEFAULT NULL,
  `businessunit_name` varchar(100) DEFAULT NULL,
  `department_id` int(11) unsigned DEFAULT NULL,
  `department_name` varchar(100) DEFAULT NULL,
  `hours_day` int(11) DEFAULT NULL,
  `is_satholiday` tinyint(1) DEFAULT NULL COMMENT '1-Yes,2-No',
  `is_halfday` tinyint(1) DEFAULT NULL COMMENT '1-Yes,2-No',
  `is_oncalltransfer` tinyint(1) DEFAULT NULL COMMENT '1-Yes,2-No',
  `is_skipholidays` tinyint(1) DEFAULT NULL COMMENT '1-Yes,2-No',
  `description` varchar(255) DEFAULT NULL,
  `createdby` int(11) DEFAULT NULL,
  `modifiedby` int(11) DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `isactive` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `main_oncallmanagement_summary` */

/*Table structure for table `main_oncallrequest` */

DROP TABLE IF EXISTS `main_oncallrequest`;

CREATE TABLE `main_oncallrequest` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `reason` text,
  `approver_comments` text,
  `oncalltypeid` int(11) unsigned DEFAULT NULL,
  `oncallday` tinyint(1) DEFAULT NULL COMMENT '1-full day,2-half day',
  `from_date` date DEFAULT NULL,
  `to_date` date DEFAULT NULL,
  `oncallstatus` enum('Pending for approval','Approved','Rejected','Cancel') DEFAULT 'Pending for approval',
  `rep_mang_id` int(11) unsigned DEFAULT NULL,
  `no_of_days` float unsigned DEFAULT NULL,
  `appliedoncallscount` float(4,1) unsigned DEFAULT NULL,
  `is_sat_holiday` tinyint(1) DEFAULT NULL COMMENT '1-yes,2-no',
  `createdby` int(11) unsigned DEFAULT NULL,
  `modifiedby` int(11) unsigned DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `isactive` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `main_oncallrequest` */

/*Table structure for table `main_oncallrequest_summary` */

DROP TABLE IF EXISTS `main_oncallrequest_summary`;

CREATE TABLE `main_oncallrequest_summary` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `oncall_req_id` bigint(20) unsigned DEFAULT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `user_name` varchar(255) DEFAULT NULL,
  `department_id` bigint(20) unsigned DEFAULT NULL,
  `department_name` varchar(255) DEFAULT NULL,
  `bunit_id` bigint(20) unsigned DEFAULT NULL,
  `buss_unit_name` varchar(255) DEFAULT NULL,
  `reason` text,
  `approver_comments` text,
  `oncalltypeid` int(11) unsigned DEFAULT NULL,
  `oncalltype_name` varchar(255) DEFAULT NULL,
  `oncallday` tinyint(1) DEFAULT NULL COMMENT '1-full day,2-half day',
  `from_date` date DEFAULT NULL,
  `to_date` date DEFAULT NULL,
  `oncallstatus` enum('Pending for approval','Approved','Rejected','Cancel') DEFAULT 'Pending for approval',
  `rep_mang_id` int(11) unsigned DEFAULT NULL,
  `rep_manager_name` varchar(255) DEFAULT NULL,
  `no_of_days` float unsigned DEFAULT NULL,
  `appliedoncallscount` float(4,1) unsigned DEFAULT NULL,
  `is_sat_holiday` tinyint(1) DEFAULT NULL COMMENT '1-yes,2-no',
  `createdby` int(11) unsigned DEFAULT NULL,
  `modifiedby` int(11) unsigned DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `isactive` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `main_oncallrequest_summary` */

/*Table structure for table `main_oncallrequest_history` */

DROP TABLE IF EXISTS `main_oncallrequest_history`;

CREATE TABLE `main_oncallrequest_history` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `oncallrequest_id` INT(20) DEFAULT NULL,
  `description` VARCHAR(500) DEFAULT NULL,
  `createdby` INT(11) DEFAULT NULL,
  `modifiedby` INT(11) DEFAULT NULL,
  `createddate` DATETIME DEFAULT NULL,
  `modifieddate` DATETIME DEFAULT NULL,
  `isactive` TINYINT(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DELIMITER $$

DROP TRIGGER `main_businessunits_main_requisition_summary`$$

CREATE TRIGGER `main_businessunits_main_requisition_summary` AFTER UPDATE ON `main_businessunits` FOR EACH ROW BEGIN
					UPDATE main_requisition_summary rs SET rs.businessunit_name = NEW.unitname, rs.modifiedon = utc_timestamp() WHERE (rs.businessunit_id = NEW.id
					AND rs.businessunit_name != NEW.unitname);
				        UPDATE main_leaverequest_summary ls SET ls.buss_unit_name = if(NEW.unitcode != "000",concat(NEW.unitcode,"","-"),""), ls.modifieddate = utc_timestamp()
				        WHERE (ls.bunit_id = NEW.id AND ls.isactive=1);
                UPDATE main_oncallrequest_summary ls SET ls.buss_unit_name = if(NEW.unitcode != "000",concat(NEW.unitcode,"","-"),""), ls.modifieddate = utc_timestamp()
				        WHERE (ls.bunit_id = NEW.id AND ls.isactive=1);

				        update main_leavemanagement_summary lm set lm.businessunit_name = if(NEW.unitcode != "000",concat(NEW.unitcode,"","-"),""),lm.modifieddate = utc_timestamp()
				        where lm.businessunit_id = new.id and lm.isactive = 1;
                update main_oncallmanagement_summary lm set lm.businessunit_name = if(NEW.unitcode != "000",concat(NEW.unitcode,"","-"),""),lm.modifieddate = utc_timestamp()
				        where lm.businessunit_id = new.id and lm.isactive = 1;
					update main_employees_summary set businessunit_name = new.unitname,modifieddate = utc_timestamp() where businessunit_id = new.id and isactive = 1;
				    END
$$

DROP TRIGGER `main_departments_main_requisition_summary`$$

CREATE TRIGGER `main_departments_main_requisition_summary` AFTER UPDATE ON `main_departments` FOR EACH ROW BEGIN
			        declare unit_code varchar(200);
				UPDATE main_requisition_summary rs SET rs.department_name = CASE WHEN NEW.isactive=1 then NEW.deptname ELSE NULL END, rs.modifiedon = utc_timestamp()
	WHERE (rs.department_id = NEW.id);
			        update main_leaverequest_summary ls set ls.department_name = concat(new.deptname," (",new.deptcode,")"),ls.modifieddate = utc_timestamp()
			        where ls.department_id = new.id and ls.isactive = 1;
			        update main_leavemanagement_summary lm set lm.department_name = concat(new.deptname," (",new.deptcode,")"),lm.modifieddate = utc_timestamp()
			        where lm.department_id = new.id and lm.isactive = 1;

              update main_oncallrequest_summary ls set ls.department_name = concat(new.deptname," (",new.deptcode,")"),ls.modifieddate = utc_timestamp()
			        where ls.department_id = new.id and ls.isactive = 1;
			        update main_oncallmanagement_summary lm set lm.department_name = concat(new.deptname," (",new.deptcode,")"),lm.modifieddate = utc_timestamp()
			        where lm.department_id = new.id and lm.isactive = 1;

				#start of main_employees_summary
				update main_employees_summary es set es.department_name = new.deptname,es.modifieddate = utc_timestamp()
			        where es.department_id = new.id and es.isactive = 1;
				#end of main_employees_summary

			        # Start Updating BusinessUnit Id and Name if business unit is 0
			        if new.unitid = 0 then
				begin
				       update main_leavemanagement_summary lm set lm.businessunit_id = 0,lm.businessunit_name = NULL,
			                 lm.modifieddate = utc_timestamp() where lm.department_id = new.id and lm.isactive = 1;
			               update main_leaverequest_summary ls set ls.bunit_id = 0,ls.buss_unit_name = NULL,
			               ls.modifieddate = utc_timestamp() where ls.department_id = new.id and ls.isactive = 1;
               update main_oncallmanagement_summary lm set lm.businessunit_id = 0,lm.businessunit_name = NULL,
      			           lm.modifieddate = utc_timestamp() where lm.department_id = new.id and lm.isactive = 1;
      			         update main_oncallrequest_summary ls set ls.bunit_id = 0,ls.buss_unit_name = NULL,
      			         ls.modifieddate = utc_timestamp() where ls.department_id = new.id and ls.isactive = 1;
				end;
				end if;
			        # End

			        # Start Updating BusinessUnit Id and Name if business unit is not 0
			        if new.unitid != 0 then
				begin
			               select unitcode into unit_code from main_businessunits where id = new.unitid;
				       update main_leavemanagement_summary lm set lm.businessunit_id = new.unitid,
			                lm.businessunit_name = concat(unit_code,"","-"),lm.modifieddate = utc_timestamp()
			                where lm.department_id = new.id and lm.isactive = 1;
			               update main_leaverequest_summary ls set ls.bunit_id = new.unitid,
			               ls.buss_unit_name = concat(unit_code,"","-"),ls.modifieddate = utc_timestamp()
			               where ls.department_id = new.id and ls.isactive = 1;

               update main_oncallmanagement_summary lm set lm.businessunit_id = new.unitid,
      			          lm.businessunit_name = concat(unit_code,"","-"),lm.modifieddate = utc_timestamp()
      			          where lm.department_id = new.id and lm.isactive = 1;
      			         update main_oncallrequest_summary ls set ls.bunit_id = new.unitid,
      			         ls.buss_unit_name = concat(unit_code,"","-"),ls.modifieddate = utc_timestamp()
      			         where ls.department_id = new.id and ls.isactive = 1;
				end;
				end if;
			        # End
			    END
$$

CREATE TRIGGER `main_employeeoncalltypes_aft_upd` AFTER UPDATE ON `main_employeeoncalltypes` FOR EACH ROW BEGIN
				     update main_oncallrequest_summary ls set ls.oncalltype_name = new.oncalltype,ls.modifieddate = utc_timestamp()
				     where ls.oncalltypeid = new.id and ls.isactive = 1;
				    END
$$

CREATE TRIGGER `main_oncallmanagement_aft_ins` AFTER INSERT ON `main_oncallmanagement` FOR EACH ROW BEGIN
				    declare calmonth_name,weekend_name1,weekend_name2,dept_name,buss_unit_name varchar(200);
				    declare dept_id,bunit_id bigint(20);
				    select month_name into calmonth_name from tbl_months where monthid = new.cal_startmonth;
				    select week_name into weekend_name1 from tbl_weeks where week_id = new.weekend_startday;
				    select week_name into weekend_name2 from tbl_weeks where week_id = new.weekend_endday;
				    #select department_id into dept_id from main_employees where user_id = new.user_id;
				    select b.id,concat(d.deptname," (",d.deptcode,")") ,
				    if(b.unitcode != "000",concat(b.unitcode,"","-"),"") into bunit_id,dept_name,buss_unit_name
				    FROM `main_departments` AS `d` LEFT JOIN `main_businessunits` AS `b` ON b.id=d.unitid
				    WHERE (d.isactive = 1 and d.id = new.department_id);
				    insert into main_oncallmanagement_summary (oncallmgmt_id, cal_startmonth, cal_startmonthname,
				    weekend_startday, weekend_startdayname, weekend_endday,weekend_enddayname, businessunit_id,
				    businessunit_name, department_id, department_name, hours_day, is_satholiday, is_halfday,
				    is_oncalltransfer, is_skipholidays, description, createdby, modifiedby, createddate,
				    modifieddate, isactive)
				    values(new.id,new.cal_startmonth, calmonth_name, new.weekend_startday, weekend_name1,
				    new.weekend_endday,weekend_name2,bunit_id, buss_unit_name, new.department_id,
				    dept_name, new.hours_day, new.is_satholiday, new.is_halfday, new.is_oncalltransfer,
				    new.is_skipholidays, new.description,  new.createdby, new.modifiedby, new.createddate,
				    new.modifieddate, new.isactive);
				    END

$$

CREATE TRIGGER `main_oncallmanagement_aft_upd` AFTER UPDATE ON `main_oncallmanagement` FOR EACH ROW BEGIN
				    declare calmonth_name,weekend_name1,weekend_name2,dept_name,buss_unit_name varchar(200);
				    declare bunit_id bigint(20);
				    select month_name into calmonth_name from tbl_months where monthid = new.cal_startmonth;
				    select week_name into weekend_name1 from tbl_weeks where week_id = new.weekend_startday;
				    select week_name into weekend_name2 from tbl_weeks where week_id = new.weekend_endday;
				    select b.id,concat(d.deptname," (",d.deptcode,")") ,
				    if(b.unitcode != "000",concat(b.unitcode,"","-"),"") into bunit_id,dept_name,buss_unit_name
				    FROM `main_departments` AS `d` LEFT JOIN `main_businessunits` AS `b` ON b.id=d.unitid
				    WHERE (d.isactive = 1 and d.id = new.department_id);
				    UPDATE  main_oncallmanagement_summary set
				    cal_startmonth = new.cal_startmonth,
				    cal_startmonthname = calmonth_name,
				    weekend_startday = new.weekend_startday,
				    weekend_startdayname = weekend_name1,
				    weekend_endday = new.weekend_endday,
				    weekend_enddayname = weekend_name2,
				    businessunit_id = bunit_id,
				    businessunit_name = buss_unit_name,
				    department_id = new.department_id,
				    department_name = dept_name,
				    hours_day = new.hours_day,
				    is_satholiday = new.is_satholiday,
				    is_halfday = new.is_halfday,
				    is_oncalltransfer = new.is_oncalltransfer,
				    is_skipholidays = new.is_skipholidays,
				    description = new.description,
				    createdby = new.createdby,
				    modifiedby = new.modifiedby,
				    createddate = new.createddate,
				    modifieddate = new.modifieddate,
				    isactive = new.isactive where oncallmgmt_id = new.id;
				    END

$$

CREATE TRIGGER `main_oncallrequest_aft_ins` AFTER INSERT ON `main_oncallrequest` FOR EACH ROW BEGIN
				    declare user_name,repmanager_name,oncall_type_name,dept_name,buss_unit_name varchar(200);
				    declare dept_id,bunit_id bigint(20);
				    select userfullname into user_name from main_users where id = new.user_id;
				    select userfullname into repmanager_name from main_users where id = new.rep_mang_id;
				    select oncalltype into oncall_type_name from main_employeeoncalltypes where id = new.oncalltypeid;
				    select department_id into dept_id from main_employees where user_id = new.user_id;
				    select b.id,concat(d.deptname," (",d.deptcode,")") ,
				    if(b.unitcode != "000",concat(b.unitcode,"","-"),"") into bunit_id,dept_name,buss_unit_name
				    FROM `main_departments` AS `d` LEFT JOIN `main_businessunits` AS `b` ON b.id=d.unitid
				    WHERE (d.isactive = 1 and d.id = dept_id);
				    insert into main_oncallrequest_summary (oncall_req_id, user_id, user_name, department_id,
				    department_name, bunit_id,buss_unit_name, reason, approver_comments, oncalltypeid, oncalltype_name, oncallday, from_date, to_date, oncallstatus,
				    rep_mang_id, rep_manager_name, no_of_days, appliedoncallscount, is_sat_holiday, createdby,
				    modifiedby, createddate, modifieddate, isactive)
				    values(new.id,new.user_id, user_name, dept_id, dept_name,bunit_id,buss_unit_name,new.reason,new.approver_comments,
				    new.oncalltypeid, oncall_type_name, new.oncallday, new.from_date, new.to_date, new.oncallstatus,
				    new.rep_mang_id, repmanager_name, new.no_of_days, new.appliedoncallscount, new.is_sat_holiday,
				    new.createdby, new.modifiedby, new.createddate, new.modifieddate, new.isactive);
				    END

$$

CREATE TRIGGER `main_oncallrequest_aft_upd` AFTER UPDATE ON `main_oncallrequest` FOR EACH ROW BEGIN
				    declare user_name,repmanager_name,oncall_type_name,dept_name,buss_unit_name varchar(200);
				    declare dept_id,bunit_id bigint(20);
				    #select userfullname into user_name from main_users where id = new.user_id;
				    #select userfullname into repmanager_name from main_users where id = new.rep_mang_id;
				    #select oncalltype into oncall_type_name from main_employeeoncalltypes where id = new.oncalltypeid;
				    select department_id into dept_id from main_employees where user_id = new.user_id;
				    select b.id,concat(d.deptname," (",d.deptcode,")") ,
				    if(b.unitcode != "000",concat(b.unitcode,"","-"),"") into bunit_id,dept_name,buss_unit_name
				    FROM `main_departments` AS `d` LEFT JOIN `main_businessunits` AS `b` ON b.id=d.unitid
				    WHERE (d.isactive = 1 and d.id = dept_id);
				    UPDATE  main_oncallrequest_summary set
				    user_id = new.user_id,
				    department_id = dept_id,
				    department_name = dept_name,
				    bunit_id = bunit_id,
				    buss_unit_name = buss_unit_name,
				    approver_comments = new.approver_comments,
				    oncallstatus = new.oncallstatus,
				    modifieddate = new.modifieddate,
				    isactive = new.isactive where oncall_req_id = new.id;
				    END

$$

DROP TRIGGER `main_monthlist_aftr_upd`$$

CREATE TRIGGER `main_monthlist_aftr_upd` AFTER UPDATE ON `main_monthslist` FOR EACH ROW BEGIN
			        declare calmonth_name varchar(200);
			        select month_name into calmonth_name from tbl_months where monthid = new.month_id;
			        UPDATE main_leavemanagement_summary lm SET lm.cal_startmonthname = calmonth_name, lm.modifieddate = utc_timestamp()
			        WHERE (lm.cal_startmonth = new.month_id AND lm.isactive=1);
              UPDATE main_oncallmanagement_summary lm SET lm.cal_startmonthname = calmonth_name, lm.modifieddate = utc_timestamp()
			        WHERE (lm.cal_startmonth = new.month_id AND lm.isactive=1);
			    	END

$$

DROP TRIGGER `main_users_aft_upd`$$

CREATE TRIGGER `main_users_aft_upd` AFTER UPDATE ON `main_users`
				    FOR EACH ROW BEGIN
				    declare groupid int(11);

				    select group_id into groupid from main_roles where id = old.emprole;
				    if old.userfullname != new.userfullname then
				    begin

				    if (groupid != 5 or groupid is null) then
				    begin
					#start of main_leaverequest_summary
				        update main_leaverequest_summary set rep_manager_name = new.userfullname,modifieddate = utc_timestamp() where rep_mang_id = new.id and isactive = 1;
				        update main_leaverequest_summary set user_name = new.userfullname,modifieddate = utc_timestamp() where user_id = new.id and isactive = 1;
					#end of main_leaverequest_summary
          #start of main_oncallrequest_summary
				        update main_oncallrequest_summary set rep_manager_name = new.userfullname,modifieddate = utc_timestamp() where rep_mang_id = new.id and isactive = 1;
				        update main_oncallrequest_summary set user_name = new.userfullname,modifieddate = utc_timestamp() where user_id = new.id and isactive = 1;
					#end of main_oncallrequest_summary
					#start of main_requisition_summary
					update main_requisition_summary set reporting_manager_name = new.userfullname,modifiedon = utc_timestamp() where reporting_id = new.id and isactive = 1;
					update main_requisition_summary set approver1_name = new.userfullname,modifiedon = utc_timestamp() where approver1 = new.id and isactive = 1;
					update main_requisition_summary set approver2_name = new.userfullname,modifiedon = utc_timestamp() where approver2 = new.id and isactive = 1;
					update main_requisition_summary set approver3_name = new.userfullname,modifiedon = utc_timestamp() where approver3 = new.id and isactive = 1;
					update main_requisition_summary set createdby_name = new.userfullname,modifiedon = utc_timestamp() where createdby = new.id and isactive = 1;
					#end of main_requisition_summary
					#start of main_employees_summary
					update main_employees_summary set reporting_manager_name = new.userfullname,modifieddate = utc_timestamp() where reporting_manager = new.id and isactive = 1;
					update main_employees_summary set referer_name = new.userfullname,modifieddate = utc_timestamp() where candidatereferredby = new.id and isactive = 1;
					update main_employees_summary set createdby_name = new.userfullname,modifieddate = utc_timestamp() where createdby = new.id and isactive = 1;
				        update main_employees_summary set userfullname = new.userfullname,modifieddate = utc_timestamp() where user_id = new.id and isactive = 1;
					#end of main_employees_summary
					#start of main_bgchecks_summary
					update main_bgchecks_summary set specimen_name = new.userfullname,modifieddate = utc_timestamp() where specimen_id = new.id and specimen_flag = 1 and isactive = 1;
					update main_bgchecks_summary set createdname = new.userfullname,modifieddate = utc_timestamp() where createdby = new.id and isactive = 1;
					update main_bgchecks_summary set modifiedname = new.userfullname,modifieddate = utc_timestamp() where modifiedby = new.id and isactive = 1;
					#end of main_bgchecks_summary
					# start of main_interviewrounddetails_summary
					update main_interviewrounds_summary set interviewer_name = new.userfullname,modified_date = utc_timestamp() where interviewer_id = new.id and isactive = 1;
					update main_interviewrounds_summary set created_by_name = new.userfullname,modified_date = utc_timestamp() where created_by = new.id and isactive = 1;
					# end of main_interviewrounddetails_summary
					# start of main_userloginlog
					update main_userloginlog set userfullname = new.userfullname where userid = new.id;
					# end of main_userloginlog
					#start of main_sdrequests_summary
					update main_sd_requests_summary set raised_by_name = new.userfullname,modifieddate = utc_timestamp() where raised_by = new.id;
					update main_sd_requests_summary set executor_name = new.userfullname,modifieddate = utc_timestamp() where executor_id = new.id;
					update main_sd_requests_summary set reporting_manager_name = new.userfullname,modifieddate = utc_timestamp() where reporting_manager_id = new.id;
					update main_sd_requests_summary set approver_1_name = new.userfullname,modifieddate = utc_timestamp() where approver_1 = new.id;
					update main_sd_requests_summary set approver_2_name = new.userfullname,modifieddate = utc_timestamp() where approver_2 = new.id;
					update main_sd_requests_summary set approver_3_name = new.userfullname,modifieddate = utc_timestamp() where approver_3 = new.id;
					# end of main_sdrequests_summary
				    end;
				    end if;
				    end;
				    end if;#end of if of user full name
				    if old.employeeId != new.employeeId then
				    begin
				        if (groupid != 5 or groupid is null) then
				        begin
					    #start of main_employees_summary
				            update main_employees_summary set employeeId = new.employeeId,modifieddate = utc_timestamp() where user_id = new.id;
				            #end of main_employees_summary
				        end;
				        end if;
				    end;
				    end if;#end of if of employeeId
				    if old.isactive != new.isactive then
				    begin
					if (groupid != 5 or groupid is null) then
				        begin
					    #start of main_employees_summary
				            update main_employees_summary set isactive = new.isactive,modifieddate = utc_timestamp() where user_id = new.id;
				            #end of main_employees_summary
				        end;
				        end if;
				    end;
				    end if; #end of if of isactive
				    if old.profileimg != new.profileimg then
				    begin
					if (groupid != 5 or groupid is null) then
				        begin
					    #start of main_employees_summary
				            update main_employees_summary set profileimg = new.profileimg,modifieddate = utc_timestamp() where user_id = new.id;
				            #end of main_employees_summary
					    #start of main_request_history
				            update main_request_history set emp_profileimg = new.profileimg,modifieddate = utc_timestamp() where emp_id = new.id;
				            #end of main_request_history
				        end;
				        end if;
				    end;
				    end if; #end of if of isactive
				    if old.backgroundchk_status != new.backgroundchk_status then
				    begin
					if (groupid != 5 or groupid is null) then
				        begin
					    #start of main_employees_summary
				            update main_employees_summary set backgroundchk_status = new.backgroundchk_status,modifieddate = utc_timestamp() where user_id = new.id;
				            #end of main_employees_summary
				        end;
				        end if;
				    end;
				    end if;#end of if of background check status
				if (old.contactnumber != new.contactnumber || new.contactnumber IS NOT NULL) then
				    begin
					if (groupid != 5 or groupid is null) then
				        begin
					    #start of main_employees_summary
				            update main_employees_summary set contactnumber = new.contactnumber,modifieddate = utc_timestamp() where user_id = new.id;
				            #end of main_employees_summary
				        end;
				        end if;
				    end;
				    end if;#end of if of contact number

				    END

$$

DROP TRIGGER `main_weekdays_aftr_upd`$$

CREATE TRIGGER `main_weekdays_aftr_upd` AFTER UPDATE ON `main_weekdays` FOR EACH ROW BEGIN
			        declare weekend_name varchar(200);
			        select week_name into weekend_name from tbl_weeks where week_id = new.day_name;
			        UPDATE main_leavemanagement_summary lm SET lm.weekend_startdayname = weekend_name, lm.modifieddate = utc_timestamp()
			        WHERE (lm.weekend_startday = new.day_name AND lm.isactive=1);
			        UPDATE main_leavemanagement_summary lm SET lm.weekend_enddayname = weekend_name, lm.modifieddate = utc_timestamp()
			        WHERE (lm.weekend_endday = new.day_name AND lm.isactive=1);
              UPDATE main_oncallmanagement_summary lm SET lm.weekend_startdayname = weekend_name, lm.modifieddate = utc_timestamp()
			        WHERE (lm.weekend_startday = new.day_name AND lm.isactive=1);
			        UPDATE main_oncallmanagement_summary lm SET lm.weekend_enddayname = weekend_name, lm.modifieddate = utc_timestamp()
			        WHERE (lm.weekend_endday = new.day_name AND lm.isactive=1);
			    	END

$$
