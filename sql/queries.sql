ALTER TABLE `main_pa_initialization` ADD `performance_app_flag` TINYINT(1) UNSIGNED NOT NULL DEFAULT '1' COMMENT '1=bu wise,0=dept wise' AFTER `isactive`;

alter table `main_leaverequest` add column `approver_comments` text after `reason`;

alter table `main_leaverequest_summary` add column `approver_comments` text after `reason`;

alter table `main_wizard` add column `departments` tinyint(1) DEFAULT '1' NULL COMMENT '1=No,2=Yes' after `org_details`;

alter table `main_wizard` add column `servicerequest` tinyint(1) DEFAULT '1' NULL COMMENT '1=No,2=Yes' after `departments`;

ALTER TABLE `main_empjobhistory` ADD `client_id` INT NULL AFTER `active_company`, ADD `vendor` VARCHAR(200) NULL AFTER `client_id`, ADD `paid_amount` DECIMAL(25,2) NULL AFTER`vendor`, ADD `received_amount` DECIMAL(25,2) NULL AFTER `paid_amount`;

ALTER TABLE main_empvisadetails DROP INDEX unique_user_id;

CREATE TABLE `main_hr_wizard` (                                         
`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,                  
`leavetypes` tinyint(1) DEFAULT '1' COMMENT '1=No,2=Yes',      
`holidays` tinyint(1) DEFAULT '1' COMMENT '1=No,2=Yes',
`perf_appraisal` tinyint(1) DEFAULT '1' COMMENT '1=No,2=Yes',
`iscomplete` tinyint(1) DEFAULT '1' COMMENT '0=later,1=No,2=Yes',  
`createdby` bigint(20) unsigned DEFAULT NULL,                      
`modifiedby` bigint(20) unsigned DEFAULT NULL,                     
`createddate` datetime DEFAULT NULL,                               
`modifieddate` datetime DEFAULT NULL,                              
`isactive` tinyint(1) DEFAULT NULL,                                
PRIMARY KEY (`id`)                                                 
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;


insert into main_hr_wizard   (  leavetypes,   holidays,   iscomplete,   createdby,   modifiedby,   createddate,   modifieddate,   isactive  )  values  (   1,   1,   1,   1,   1,   now(),   now(),   1  );

insert  into `main_menu`(`id`,`menuName`,`url`,`helpText`,`toolTip`,`iconPath`,`parent`,`menuOrder`,`nav_ids`,`isactive`,`segment_flag`,`org_menuid`,`menufields`,`menuQuery`,`hasJoins`,`modelName`,`functionName`,`defaultOrderBy`) values
(175,'Appraisal History','/appraisalhistory','Appraisal History','Appraisal History','appraisal_history.jpg',149,7,',149,175,',1,2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(176,'Policy Documents','/#','Policy Documents','Policy Documents','policy_documents.jpg',1,7,',1,176,',1,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(177,'Exit Procedure','/#','Employee Exit Procedure','Employee Exit Procedure','exit_procedure.jpg',4,6,',4,177,',0,0,0,NULL,NULL,NULL,NULL,NULL,NULL),(178,'Settings','/exitprocsettings','Employee Exit Procedure Settings','Employee Exit Procedure Settings','exit_procedure.jpg',177,1,',4,177,178,',0,0,0,NULL,NULL,NULL,NULL,NULL,NULL),(179,'Exit Types','/exittypes','Exit Types','Exit Types','exit_types.jpg',177,2,',4,177,178,',0,0,0,NULL,NULL,NULL,NULL,NULL,NULL),(180,'Initiate/Check Status','/exitproc','Initiate exit proc or check status','Initiate exit proc or check status','initiate_exit_proc.jpg',177,3,',4,177,180,',0,0,0,NULL,NULL,NULL,NULL,NULL,NULL),(181,'All Exit Procedures','/allexitproc','All exit procedures','All exit procedures','all_exit_proc.jpg',177,4,',4,177,181,',0,0,0,NULL,NULL,NULL,NULL,NULL,NULL),(182,'Categories','/categories','Categories for Policy documents','Categories for Policy documents','pd_categories.jpg',176,1,',4,176,182,',1,0,0,NULL,NULL,NULL,NULL,NULL,NULL),(183,'View/Manage Policy Documents','/policydocuments','View or Manage Policy documents','View or Manage Policy documents','',176,2,',4,176,183,',1,0,0,NULL,NULL,NULL,NULL,NULL,NULL),(184,'Add Employee Leaves', '/addemployeeleaves', 'Add Employee Leaves', 'Add Employee Leaves', 'addemployeeleaves.jpg', 
17, 3, ',3,17,184', 1, 2, 302, NULL, NULL, NULL, NULL, NULL, NULL);

insert  into `main_privileges`(`role`,`group_id`,`object`,`addpermission`,`editpermission`,`deletepermission`,`viewpermission`,`uploadattachments`, `viewattachments`,`createdby`,`modifiedby`,`createddate`,`modifieddate`,`isactive`) values 
(1,(NULL),175,'No','No','No','Yes','No','No',1,1,now(),now(),1),
(NULL,1,175,'No','No','No','Yes','No','No',1,1,now(),now(),1),
(NULL,2,175,'No','No','No','Yes','No','No',1,1,now(),now(),1),
(NULL,3,175,'No','No','No','Yes','No','No',1,1,now(),now(),1),
(NULL,4,175,'No','No','No','Yes','No','No',1,1,now(),now(),1),
(NULL,6,175,'No','No','No','Yes','No','No',1,1,now(),now(),1),
(NULL,7,175,'No','No','No','Yes','No','No',1,1,now(),now(),1),
(1, NULL, 184, 'Yes', 'Yes', 'No', 'Yes', 'No', 'No', 1, 1, now(), now(), 1),
(NULL, 1, 184, 'Yes', 'Yes', 'No', 'Yes', 'No', 'No', 1, 1, now(), now(), 1),
(NULL, 3, 184, 'Yes', 'Yes', 'No', 'Yes', 'No', 'No', 1, 1, now(), now(), 1),
(1,NULL,176,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,now(),now(),1),
(NULL,1,176,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,now(),now(),1),
(NULL,2,176,'No','No','No','Yes','No','Yes',1,1,now(),now(),1),
(NULL,3,176,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,now(),now(),1),
(NULL,4,176,'No','No','No','Yes','No','Yes',NULL,NULL,now(),now(),1),
(NULL,5,176,'No','No','No','No','No','Yes',1,1,now(),now(),0),
(NULL,6,176,'Yes','Yes','Yes','Yes','Yes','Yes',NULL,NULL,now(),now(),1), 
(1,NULL,182,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,now(),now(),1),
(NULL,1,182,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,now(),now(),1),
(NULL,3,182,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,now(),now(),1),
(NULL,6,182,'Yes','Yes','No','Yes','Yes','Yes',NULL,NULL,now(),now(),1),
(1,NULL,183,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,now(),now(),1),
(NULL,1,183,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,now(),now(),1),
(NULL,2,183,'No','No','No','Yes','No','No',1,1,now(),now(),1),
(NULL,3,183,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,now(),now(),1),
(NULL,4,183,'No','No','No','Yes','No','No',1,1,now(),now(),1),
(NULL,6,183,'Yes','Yes','No','Yes','Yes','Yes',NULL,NULL,now(),now(),1);

update main_pa_initialization a
inner join main_pa_implementation b on a.pa_configured_id=b.id
set a.performance_app_flag=b.performance_app_flag;

UPDATE `main_menu` SET `parent`=NULL,`isactive` = '0' WHERE `main_menu`.`id` = 155 AND `main_menu`.`menuName` ='Appraisal Settings';

update main_privileges set isactive=0 where object=155;

UPDATE `main_menu` SET `parent` = '0', `menuOrder` = '16', `nav_ids` = ',130,',`url` = '/timemanagement' WHERE `main_menu`.`id` = 130;

update main_menu set menuName = 'Self Service',menuOrder=2 where id=4;
update main_menu set menuName = 'Service Request',menuOrder=4 where id=143;
update main_menu set menuName = 'HR',menuOrder=5 where id=3;
update main_menu set menuName = 'Appraisals',menuOrder=6 where id=149;
update main_menu set menuName = 'Talent Acquisition',menuOrder=7 where id=19;
update main_menu set menuName = 'Background Check',menuOrder=8 where id=5;
update main_menu set menuName = 'Site Config',menuOrder=11 where id=70;
update main_menu set menuName = 'Modules',menuOrder=12 where id=142;
update main_menu set menuName = 'Time',menuOrder=16,isactive=1 where id=130;
update main_menu set menuOrder=9 where id=1;
update main_menu set menuOrder=10 where id=8;

UPDATE `main_dateformat` SET `js_dateformat` = 'M-dd-yy' WHERE `main_dateformat`.`id` = 11;

UPDATE `tbl_states` SET `state_name` = 'British Columbia' WHERE `tbl_states`.`id` = 144;

CREATE TABLE `main_pd_categories` (                                     
`id` bigint(20) unsigned NOT NULL auto_increment,                     
`category` varchar(200) NOT NULL,                                     
`description` text,                                                   
`isused` tinyint(4) NOT NULL default '0' COMMENT '0-notused,1-used',  
`isactive` tinyint(4) NOT NULL default '1',                           
`modifiedby` bigint(20) unsigned NOT NULL,                            
`createdby` bigint(20) unsigned NOT NULL,                             
`modifieddate` datetime default NULL,                                 
`createddate` datetime default NULL,                                  
PRIMARY KEY  (`id`)                                                   
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `main_pd_documents` (                                        
`id` bigint(20) unsigned NOT NULL auto_increment,                       
`category_id` bigint(20) unsigned NOT NULL,                             
`subcategory_id` bigint(20) unsigned default NULL COMMENT 'not used ',  
`document_name` varchar(500) NOT NULL,                                  
`document_version` varchar(100) default NULL,                           
`description` text,                                                     
`file_name` text,                                                       
`isactive` tinyint(4) NOT NULL default '1',                             
`modifiedby` bigint(20) unsigned default NULL,                          
`createdby` bigint(20) unsigned default NULL,                           
`modifieddate` datetime default NULL,                                   
`createddate` datetime default NULL,                                    
`flag1` varchar(100) default NULL,                                      
`flag2` varchar(100) default NULL,                                      
PRIMARY KEY  (`id`)                                                     
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `tm_clients` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `client_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone_no` varchar(20) default NULL,
  `poc` varchar(100) NOT NULL,
  `address` varchar(200) default NULL,
  `country_id` bigint(20) unsigned default NULL,
  `state_id` bigint(20) unsigned default NULL,
  `fax` varchar(50) default NULL,
  `is_active` tinyint(1) unsigned NOT NULL default '1',
  `created_by` int(10) unsigned NOT NULL,
  `modified_by` int(10) unsigned default NULL,
  `created` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `modified` timestamp NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `FK_client_country` (`country_id`),
  KEY `FK_client_state` (`state_id`),
  CONSTRAINT `FK_client_country` FOREIGN KEY (`country_id`) REFERENCES `tbl_countries` (`id`),
  CONSTRAINT `FK_client_state` FOREIGN KEY (`state_id`) REFERENCES `tbl_states` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

CREATE TABLE `tm_projects` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `project_name` varchar(100) NOT NULL,
  `project_status` enum('initiated','draft','in-progress','hold','completed') NOT NULL,
  `base_project` bigint(20) default NULL,
  `description` varchar(500) default NULL,
  `client_id` bigint(20) unsigned NOT NULL,
  `created_by` int(10) unsigned NOT NULL,
  `modified_by` int(10) unsigned default NULL,
  `is_active` tinyint(1) unsigned NOT NULL,
  `created` timestamp NOT NULL default '0000-00-00 00:00:00',
  `modified` timestamp NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`),
  KEY `FK_tm_projects_client` (`client_id`),
  CONSTRAINT `FK_tm_projects_client` FOREIGN KEY (`client_id`) REFERENCES `tm_clients` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

CREATE TABLE `tm_tasks` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `task` varchar(200) NOT NULL,
  `is_default` tinyint(1) unsigned NOT NULL,
  `created_by` int(10) unsigned NOT NULL,
  `modified_by` int(10) unsigned default NULL,
  `is_active` tinyint(1) unsigned NOT NULL,
  `created` timestamp NOT NULL default '0000-00-00 00:00:00',
  `modified` timestamp NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

CREATE TABLE `tm_project_tasks` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `project_id` bigint(20) unsigned NOT NULL,
  `task_id` bigint(20) unsigned NOT NULL,
  `created_by` int(10) unsigned NOT NULL,
  `modified_by` int(10) unsigned default NULL,
  `is_active` tinyint(1) unsigned NOT NULL,
  `created` timestamp NOT NULL default '0000-00-00 00:00:00',
  `modified` timestamp NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`),
  KEY `FK_tm_project_tasks_project` (`project_id`),
  KEY `FK_tm_project_tasks_task` (`task_id`),
  CONSTRAINT `FK_tm_project_tasks_project` FOREIGN KEY (`project_id`) REFERENCES `tm_projects` (`id`),
  CONSTRAINT `FK_tm_project_tasks_task` FOREIGN KEY (`task_id`) REFERENCES `tm_tasks` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

CREATE TABLE `tm_project_task_employees` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `project_id` bigint(20) unsigned NOT NULL,
  `task_id` bigint(20) unsigned NOT NULL,
  `project_task_id` bigint(20) unsigned NOT NULL,
  `emp_id` int(10) unsigned NOT NULL,
  `created_by` int(10) unsigned NOT NULL,
  `modified_by` int(10) unsigned default NULL,
  `is_active` tinyint(1) unsigned NOT NULL,
  `created` timestamp NOT NULL default '0000-00-00 00:00:00',
  `modified` timestamp NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`),
  KEY `FK_tm_project_task_employees_project` (`project_id`),
  KEY `FK_tm_project_task_employees_task` (`task_id`),
  KEY `FK_tm_project_task_employees_proj_task` (`project_task_id`),
  KEY `FK_tm_project_task_employees_employee` (`emp_id`),
  CONSTRAINT `FK_tm_project_task_employees_employee` FOREIGN KEY (`emp_id`) REFERENCES `main_users` (`id`),
  CONSTRAINT `FK_tm_project_task_employees_proj_task` FOREIGN KEY (`project_task_id`) REFERENCES `tm_project_tasks` (`id`),
  CONSTRAINT `FK_tm_project_task_employees_project` FOREIGN KEY (`project_id`) REFERENCES `tm_projects` (`id`),
  CONSTRAINT `FK_tm_project_task_employees_task` FOREIGN KEY (`task_id`) REFERENCES `tm_tasks` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

CREATE TABLE `tm_project_employees` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `project_id` bigint(20) unsigned NOT NULL,
  `emp_id` int(10) unsigned NOT NULL,
  `created_by` int(10) unsigned NOT NULL,
  `modified_by` int(10) unsigned default NULL,
  `is_active` tinyint(1) unsigned NOT NULL,
  `created` timestamp NOT NULL default '0000-00-00 00:00:00',
  `modified` timestamp NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`),
  KEY `FK_tm_project_employees_project` (`project_id`),
  KEY `FK_tm_project_employees_employee` (`emp_id`),
  CONSTRAINT `FK_tm_project_employees_employee` FOREIGN KEY (`emp_id`) REFERENCES `main_users` (`id`),
  CONSTRAINT `FK_tm_project_employees_project` FOREIGN KEY (`project_id`) REFERENCES `tm_projects` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

CREATE TABLE `tm_emp_timesheets` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `emp_id` int(10) unsigned NOT NULL,
  `project_task_id` bigint(20) unsigned default NULL,
  `project_id` bigint(20) unsigned default NULL,
  `ts_year` smallint(4) unsigned NOT NULL,
  `ts_month` tinyint(2) unsigned default NULL,
  `ts_week` tinyint(1) unsigned default NULL,
  `cal_week` tinyint(2) unsigned NOT NULL,
  `sun_date` date default NULL,
  `sun_duration` varchar(6) default NULL,
  `mon_date` date default NULL,
  `mon_duration` varchar(6) default NULL,
  `tue_date` date default NULL,
  `tue_duration` varchar(6) default NULL,
  `wed_date` date default NULL,
  `wed_duration` varchar(6) default NULL,
  `thu_date` date default NULL,
  `thu_duration` varchar(6) default NULL,
  `fri_date` date default NULL,
  `fri_duration` varchar(6) default NULL,
  `sat_date` date default NULL,
  `sat_duration` varchar(6) default NULL,
  `week_duration` varchar(6) default NULL,
  `created_by` int(10) unsigned NOT NULL,
  `modified_by` int(10) default NULL,
  `is_active` tinyint(1) unsigned NOT NULL,
  `created` timestamp NOT NULL default '0000-00-00 00:00:00',
  `modified` timestamp NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `IDX_emp_task_time` (`emp_id`,`project_task_id`,`ts_year`,`ts_month`,`ts_week`,`cal_week`),
  KEY `FK_tm_emp_timesheets_proj_task` (`project_task_id`),
  KEY `FK_tm_emp_timesheets_project` (`project_id`),
  CONSTRAINT `FK_tm_emp_timesheets_employee` FOREIGN KEY (`emp_id`) REFERENCES `main_users` (`id`),
  CONSTRAINT `FK_tm_emp_timesheets_proj_task` FOREIGN KEY (`project_task_id`) REFERENCES `tm_project_tasks` (`id`),
  CONSTRAINT `FK_tm_emp_timesheets_project` FOREIGN KEY (`project_id`) REFERENCES `tm_projects` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

CREATE TABLE `tm_emp_ts_notes` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `emp_id` int(10) unsigned NOT NULL,
  `ts_year` smallint(4) NOT NULL,
  `ts_month` tinyint(2) default NULL,
  `ts_week` tinyint(1) default NULL,
  `cal_week` tinyint(2) unsigned NOT NULL,
  `sun_date` date NOT NULL,
  `sun_note` varchar(200) default NULL,
  `mon_date` date default NULL,
  `mon_note` varchar(200) default NULL,
  `tue_date` date default NULL,
  `tue_note` varchar(200) default NULL,
  `wed_date` date default NULL,
  `wed_note` varchar(200) default NULL,
  `thu_date` date default NULL,
  `thu_note` varchar(200) default NULL,
  `fri_date` date default NULL,
  `fri_note` varchar(200) default NULL,
  `sat_date` date default NULL,
  `sat_note` varchar(200) default NULL,
  `week_note` varchar(200) default NULL,
  `created_by` int(10) unsigned default NULL,
  `modified_by` int(10) unsigned default NULL,
  `is_active` tinyint(1) unsigned NOT NULL,
  `created` timestamp NOT NULL default '0000-00-00 00:00:00',
  `modified` timestamp NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `IDX_emp_ts_notes` (`emp_id`,`ts_year`,`ts_month`,`ts_week`,`cal_week`),
  CONSTRAINT `FK_tm_emp_ts_notes_employee` FOREIGN KEY (`emp_id`) REFERENCES `main_users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

CREATE TABLE `tm_ts_status` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `emp_id` int(10) unsigned NOT NULL,
  `project_id` bigint(20) unsigned default NULL,
  `ts_year` smallint(4) unsigned NOT NULL,
  `ts_month` tinyint(2) unsigned default NULL,
  `ts_week` tinyint(1) unsigned default NULL,
  `cal_week` tinyint(2) default NULL,
  `sun_date` date default NULL,
  `sun_project_status` enum('saved','submitted','no_entry') default NULL,
  `sun_status` enum('saved','submitted','no_entry') default NULL,
  `sun_status_date` timestamp NULL default NULL,
  `mon_date` date default NULL,
  `mon_project_status` enum('saved','submitted','no_entry') default NULL,
  `mon_status` enum('saved','submitted','no_entry') default NULL,
  `mon_status_date` timestamp NULL default NULL,
  `tue_date` date default NULL,
  `tue_project_status` enum('saved','submitted','no_entry') default NULL,
  `tue_status` enum('saved','submitted','no_entry') default NULL,
  `tue_status_date` timestamp NULL default NULL,
  `wed_date` date default NULL,
  `wed_project_status` enum('saved','submitted','no_entry') default NULL,
  `wed_status` enum('saved','submitted','no_entry') default NULL,
  `wed_status_date` timestamp NULL default NULL,
  `thu_date` date default NULL,
  `thu_project_status` enum('saved','submitted','no_entry') default NULL,
  `thu_status` enum('saved','submitted','no_entry') default NULL,
  `thu_status_date` timestamp NULL default NULL,
  `fri_date` date default NULL,
  `fri_project_status` enum('saved','submitted','no_entry') default NULL,
  `fri_status` enum('saved','submitted','no_entry') default NULL,
  `fri_status_date` timestamp NULL default NULL,
  `sat_date` date default NULL,
  `sat_project_status` enum('saved','submitted','no_entry') default NULL,
  `sat_status` enum('saved','submitted','no_entry') default NULL,
  `sat_status_date` timestamp NULL default NULL,
  `week_status` enum('saved','submitted','no_entry') default NULL,
  `created_by` int(10) unsigned default NULL,
  `modified_by` int(10) unsigned default NULL,
  `is_active` tinyint(1) unsigned NOT NULL,
  `created` timestamp NOT NULL default '0000-00-00 00:00:00',
  `modified` timestamp NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `IDX_emp_ts_project_status` (`emp_id`,`project_id`,`ts_year`,`ts_month`,`ts_week`,`cal_week`),
  KEY `FK_tm_ts_status_project` (`project_id`),
  CONSTRAINT `FK_tm_ts_status_employee` FOREIGN KEY (`emp_id`) REFERENCES `main_users` (`id`),
  CONSTRAINT `FK_tm_ts_status_project` FOREIGN KEY (`project_id`) REFERENCES `tm_projects` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;


DELIMITER $$

DROP TRIGGER `main_leaverequest_aft_ins`$$

CREATE
   
    TRIGGER `main_leaverequest_aft_ins` AFTER INSERT ON `main_leaverequest` 
    FOR EACH ROW BEGIN
				    declare user_name,repmanager_name,leave_type_name,dept_name,buss_unit_name varchar(200);
				    declare dept_id,bunit_id bigint(20);
				    select userfullname into user_name from main_users where id = new.user_id;
				    select userfullname into repmanager_name from main_users where id = new.rep_mang_id;
				    select leavetype into leave_type_name from main_employeeleavetypes where id = new.leavetypeid;
				    select department_id into dept_id from main_employees where user_id = new.user_id;
				    select b.id,concat(d.deptname," (",d.deptcode,")") ,
				    if(b.unitcode != "000",concat(b.unitcode,"","-"),"") into bunit_id,dept_name,buss_unit_name 
				    FROM `main_departments` AS `d` LEFT JOIN `main_businessunits` AS `b` ON b.id=d.unitid 
				    WHERE (d.isactive = 1 and d.id = dept_id);
				    insert into main_leaverequest_summary (leave_req_id, user_id, user_name, department_id, 
				    department_name, bunit_id,buss_unit_name, reason, approver_comments, leavetypeid, leavetype_name, leaveday, from_date, to_date, leavestatus, 
				    rep_mang_id, rep_manager_name, no_of_days, appliedleavescount, is_sat_holiday, createdby, 
				    modifiedby, createddate, modifieddate, isactive)
				    values(new.id,new.user_id, user_name, dept_id, dept_name,bunit_id,buss_unit_name,new.reason,new.approver_comments, 
				    new.leavetypeid, leave_type_name, new.leaveday, new.from_date, new.to_date, new.leavestatus, 
				    new.rep_mang_id, repmanager_name, new.no_of_days, new.appliedleavescount, new.is_sat_holiday, 
				    new.createdby, new.modifiedby, new.createddate, new.modifieddate, new.isactive);
				    END;
$$

DELIMITER ;

DELIMITER $$

DROP TRIGGER `main_leaverequest_aft_upd`$$

CREATE
    TRIGGER `main_leaverequest_aft_upd` AFTER UPDATE ON `main_leaverequest` 
    FOR EACH ROW BEGIN
				    declare user_name,repmanager_name,leave_type_name,dept_name,buss_unit_name varchar(200);
				    declare dept_id,bunit_id bigint(20);
				    #select userfullname into user_name from main_users where id = new.user_id;
				    #select userfullname into repmanager_name from main_users where id = new.rep_mang_id;
				    #select leavetype into leave_type_name from main_employeeleavetypes where id = new.leavetypeid;
				    select department_id into dept_id from main_employees where user_id = new.user_id;
				    select b.id,concat(d.deptname," (",d.deptcode,")") ,
				    if(b.unitcode != "000",concat(b.unitcode,"","-"),"") into bunit_id,dept_name,buss_unit_name 
				    FROM `main_departments` AS `d` LEFT JOIN `main_businessunits` AS `b` ON b.id=d.unitid 
				    WHERE (d.isactive = 1 and d.id = dept_id);
				    UPDATE  main_leaverequest_summary set
				    user_id = new.user_id, 
				    department_id = dept_id, 
				    department_name = dept_name, 
				    bunit_id = bunit_id,
				    buss_unit_name = buss_unit_name,
				    approver_comments = new.approver_comments, 
				    leavestatus = new.leavestatus, 
				    modifieddate = new.modifieddate, 
				    isactive = new.isactive where leave_req_id = new.id;
				    END;
$$

DELIMITER ;

