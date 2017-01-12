
/*Data for the table `main_menu` */
insert  into `main_menu`(`id`,`menuName`,`url`,`helpText`,`toolTip`,`iconPath`,`parent`,`menuOrder`,`nav_ids`,`isactive`,`modulename`,`segment_flag`,`org_menuid`,`menufields`,`menuQuery`,`hasJoins`,`modelName`,`functionName`,`defaultOrderBy`) values
(17,'Leave Management','/#','','','1346859254_vacation_main.jpg',3,5,',3,17,',1,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
(31,'Leaves','/#','','','1346863688_vacation.jpg',4,1,',4,31,',1,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
(44,'Leave Management Options','/leavemanagement','Leave Management Options','Leave Management Options','leave-management-options.jpg',17,1,',3,17,44,',1,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
(45,'Employee Leaves Summary','/empleavesummary','Employee Leaves Summary','Employee Leaves Summary','employee-leaves-summary.jpg',17,2,',3,17,45,',1,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
(61,'Leave Request','/leaverequest','Leave Request','Leave Request','1346863776_vacation_request.jpg',31,1,',4,31,61,',1,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
(62,'My Leaves','/pendingleaves','Pending Leaves','Pending Leaves','1346870194_pending-vacation-requests.png',31,2,',4,31,62,',1,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
(63,'Approved Leaves','/approvedleaves','Approved Leaves','Approved Leaves','1346863728_approved_vacations.jpg',31,3,',4,31,63,',0,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
(64,'Cancelled Leaves','/cancelleaves','Cancel Leaves','Cancel Leaves','1346863749_cancel_vacation_history.jpg',31,4,',4,31,64,',0,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
(65,'Manager Employee Leaves','/manageremployeevacations','Manager Employee Vacations','Manager Employee Vacations','1346863764_manager_employee_vacations.jpg',31,6,',4,31,65,',1,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
(128,'Leave Types','/employeeleavetypes','','','leave-types.jpg',113,10,',3,113,128',1,'default',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
(135,'Rejected Leaves','/rejectedleaves','','','rejected-leaves.jpg',31,5,',4,31,135,',0,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
(184,'Add Employee Leaves','/addemployeeleaves','Add Employee Leaves','Add Employee Leaves','addemployeeleaves.jpg',17,3,',3,17,184',1,'default',2,302,NULL,NULL,NULL,NULL,NULL,NULL),

(900,'On Call Management','/#','','','1346859254_vacation_main.jpg',3,5,',3,17,',1,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
(901,'On Call','/#','','','1346863688_vacation.jpg',4,1,',4,31,',1,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
(902,'On Call Management Options','/oncallmanagement','On Call Management Options','On Call Management Options','leave-management-options.jpg',17,1,',3,17,44,',1,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
(903,'Employee On Call Summary','/emponcallsummary','Employee On Call Summary','Employee On Call Summary','employee-leave-summary.jpg',17,2,',3,17,45,',1,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
(904,'On Call Request','/oncallrequest','On Call Request','On Call Request','1346863776_vacation_request.jpg',31,1,',4,31,61,',1,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
(905,'My On Call','/pendingoncalls','Pending On Call','Pending On Call','1346870194_pending-vacation-requests.png',31,2,',4,31,62,',1,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
(906,'Approved On Call','/approvedoncalls','Approved On Call','Approved On Call','1346863728_approved_vacations.jpg',31,3,',4,31,63,',0,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
(907,'Cancelled On Call','/canceloncalls','Cancel On Call','Cancel On Call','1346863749_cancel_vacation_history.jpg',31,4,',4,31,64,',0,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
(908,'Manager Employee On Call','/manageremployeevacations','Manager Employee Vacations','Manager Employee Vacations','1346863764_manager_employee_vacations.jpg',31,6,',4,31,65,',1,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
(909,'On Call Types','/employeeoncalltypes','','','leave-types.jpg',113,10,',3,113,128',1,'default',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
(910,'Rejected On Call','/rejectedoncalls','','','rejected-leaves.jpg',31,5,',4,31,135,',0,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
(911,'Add Employee On Call','/addemployeeoncalls','Add Employee On Call','Add Employee On Call','addemployeeleaves.jpg',17,3,',3,17,184',1,'default',2,302,NULL,NULL,NULL,NULL,NULL,NULL),

insert  into `main_privileges`(`role`,`group_id`,`object`,`addpermission`,`editpermission`,`deletepermission`,`viewpermission`,`uploadattachments`,`viewattachments`,`createdby`,`modifiedby`,`createddate`,`modifieddate`,`isactive`) values (1,NULL,17,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(NULL,1,17,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(1,NULL,31,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(NULL,1,31,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(1,NULL,44,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(NULL,1,44,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(1,NULL,45,'No','No','No','Yes','No','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(NULL,1,45,'No','No','No','Yes','No','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(1,NULL,61,'Yes','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(1,NULL,62,'No','No','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(1,NULL,63,'No','No','No','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),
(1,NULL,64,'No','No','No','Yes','No','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),
(1,NULL,65,'No','Yes','No','Yes','No','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(1,NULL,128,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(1,NULL,135,'No','No','No','Yes','No','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),
(NULL,1,61,'Yes','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(NULL,1,62,'No','No','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(NULL,1,63,'No','No','No','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),
(NULL,1,64,'No','No','No','Yes','No','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),
(NULL,1,65,'No','Yes','No','Yes','No','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(NULL,1,128,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(NULL,1,135,'No','No','No','Yes','No','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),
(NULL,2,31,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(NULL,2,61,'Yes','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(NULL,2,62,'No','No','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(NULL,2,63,'No','No','No','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),
(NULL,2,64,'No','No','No','Yes','No','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),
(NULL,2,65,'No','Yes','No','Yes','No','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(NULL,2,135,'No','No','No','Yes','No','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),
(NULL,3,17,'Yes','Yes','No','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(NULL,3,31,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(NULL,3,44,'Yes','Yes','No','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(NULL,3,45,'No','No','No','Yes','No','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(NULL,3,61,'Yes','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(NULL,3,62,'No','No','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(NULL,3,63,'No','No','No','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),
(NULL,3,64,'No','No','No','Yes','No','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),
(NULL,3,65,'No','Yes','No','Yes','No','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(NULL,3,128,'Yes','Yes','No','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(NULL,3,135,'No','No','No','Yes','No','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),
(NULL,4,31,'Yes','Yes','Yes','Yes','Yes','Yes',NULL,NULL,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(NULL,4,61,'Yes','No','No','No','No','No',NULL,NULL,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(NULL,4,62,'No','No','Yes','Yes','Yes','Yes',NULL,NULL,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(NULL,4,63,'No','No','No','Yes','Yes','Yes',NULL,NULL,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),
(NULL,4,64,'No','No','No','Yes','No','Yes',NULL,NULL,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),
(NULL,4,65,'No','Yes','No','Yes','No','Yes',NULL,NULL,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(NULL,4,135,'No','No','No','Yes','No','Yes',NULL,NULL,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),
(NULL,6,31,'Yes','Yes','Yes','Yes','Yes','Yes',NULL,NULL,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(NULL,6,61,'Yes','No','No','No','No','No',NULL,NULL,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(NULL,6,62,'No','No','Yes','Yes','Yes','Yes',NULL,NULL,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(NULL,6,63,'No','No','No','Yes','Yes','Yes',NULL,NULL,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),
(NULL,6,64,'No','No','No','Yes','No','Yes',NULL,NULL,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),
(NULL,6,65,'No','Yes','No','Yes','No','Yes',NULL,NULL,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(NULL,6,135,'No','No','No','Yes','No','Yes',NULL,NULL,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),
(2,1,31,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(2,1,61,'Yes','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(2,1,62,'No','No','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(2,1,63,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),
(2,1,64,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),
(2,1,135,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),
(2,1,65,'No','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(2,1,17,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(2,1,44,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(2,1,45,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(3,2,31,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(3,2,61,'Yes','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(3,2,62,'No','No','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(3,2,63,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),
(3,2,64,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),
(3,2,135,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),
(3,2,65,'No','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(4,3,31,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(4,3,61,'Yes','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(4,3,62,'No','No','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(4,3,63,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),
(4,3,64,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),
(4,3,135,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),
(4,3,65,'No','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(4,3,17,'Yes','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(4,3,44,'Yes','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(4,3,45,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(5,4,31,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(5,4,61,'Yes','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(5,4,62,'No','No','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(5,4,63,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),
(5,4,64,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),
(5,4,135,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),
(5,4,65,'No','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(8,6,31,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(8,6,61,'Yes','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(8,6,62,'No','No','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(8,6,63,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),
(8,6,64,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),
(8,6,135,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),
(8,6,65,'No','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(9,4,31,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(9,4,61,'Yes','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(9,4,62,'No','No','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(9,4,63,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),
(9,4,64,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),
(9,4,135,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),
(9,4,65,'No','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(1,NULL,184,'Yes','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(NULL,1,184,'Yes','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(NULL,3,184,'Yes','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(2,1,184,'Yes','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),
(4,3,184,'Yes','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),

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

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
