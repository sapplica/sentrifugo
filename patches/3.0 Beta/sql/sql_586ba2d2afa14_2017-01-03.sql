/** START patch version queries **/

update main_patches_version set isactive=0;
insert into main_patches_version (version, createddate, modifieddate, isactive) values ("2.0.1", now(), now(),1);

/** END patch version queries **/
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
ALTER TABLE `tm_emp_ts_notes`
ADD `sun_reject_note` varchar(200) NULL after `sun_note`,
ADD `mon_reject_note` varchar(200) NULL after `mon_note`,
ADD `tue_reject_note` varchar(200) NULL after `tue_note`,
ADD `wed_reject_note` varchar(200) NULL after `wed_note`,
ADD `thu_reject_note` varchar(200) NULL after `thu_note`,
ADD `fri_reject_note` varchar(200) NULL after `fri_note`,
ADD `sat_reject_note` varchar(200) NULL after `sat_note`;

ALTER TABLE `tm_projects`
ADD `currency_id` int(10) unsigned NOT NULL after `client_id`,
ADD `project_type` enum('billable','non_billable','revenue') NOT NULL after `currency_id`,
ADD `lead_approve_ts` tinyint(1) NOT NULL after `project_type`, 
ADD `estimated_hrs` MEDIUMINT(5) DEFAULT NULL after `lead_approve_ts`,
ADD `start_date` date DEFAULT NULL after `estimated_hrs`,
ADD `end_date` date DEFAULT NULL after `start_date`, 
ADD `initiated_date` timestamp NULL DEFAULT NULL after `end_date`,
ADD `hold_date` timestamp NULL DEFAULT NULL after `initiated_date`, 
ADD `completed_date` timestamp NULL DEFAULT NULL after `hold_date`;

update tm_projects set currency_id = (select id from main_currency limit 0,1);

ALTER TABLE tm_projects
ADD CONSTRAINT `FK_tm_projects_currency`
FOREIGN KEY (`currency_id`) REFERENCES `main_currency` (`id`);

ALTER TABLE `tm_project_employees`
ADD `cost_rate` decimal(8,2) unsigned DEFAULT NULL after `emp_id`,
ADD `billable_rate` decimal(7,2) unsigned DEFAULT NULL after `cost_rate`;

ALTER TABLE `tm_project_tasks`
ADD `estimated_hrs` MEDIUMINT(5) unsigned DEFAULT NULL after `task_id`,
ADD `is_billable` tinyint(1) DEFAULT '0' after `estimated_hrs`,
ADD `billable_rate` decimal(25,2) DEFAULT NULL after `is_billable`;

ALTER TABLE `tm_ts_status`
ADD `sun_reject_note` varchar(200) NULL after `sun_status_date`,
ADD `mon_reject_note` varchar(200) NULL after `mon_status_date`,
ADD `tue_reject_note` varchar(200) NULL after `tue_status_date`,
ADD `wed_reject_note` varchar(200) NULL after `wed_status_date`,
ADD `thu_reject_note` varchar(200) NULL after `thu_status_date`,
ADD `fri_reject_note` varchar(200) NULL after `fri_status_date`,
ADD `sat_reject_note` varchar(200) NULL after `sat_status_date`;

ALTER TABLE `tm_ts_status` 
CHANGE `sun_project_status` `sun_project_status` enum('saved','submitted','approved','enabled','rejected','blocked','no_entry') character set utf8 collate utf8_general_ci NULL, 
CHANGE `sun_status` `sun_status` enum('saved','submitted','approved','enabled','rejected','blocked','no_entry') character set utf8 collate utf8_general_ci NULL, 
CHANGE `mon_project_status` `mon_project_status` enum('saved','submitted','approved','enabled','rejected','blocked','no_entry') character set utf8 collate utf8_general_ci NULL, 
CHANGE `mon_status` `mon_status` enum('saved','submitted','approved','enabled','rejected','blocked','no_entry') character set utf8 collate utf8_general_ci NULL, 
CHANGE `tue_project_status` `tue_project_status` enum('saved','submitted','approved','enabled','rejected','blocked','no_entry') character set utf8 collate utf8_general_ci NULL, 
CHANGE `tue_status` `tue_status` enum('saved','submitted','approved','enabled','rejected','blocked','no_entry') character set utf8 collate utf8_general_ci NULL, 
CHANGE `wed_project_status` `wed_project_status` enum('saved','submitted','approved','enabled','rejected','blocked','no_entry') character set utf8 collate utf8_general_ci NULL, 
CHANGE `wed_status` `wed_status` enum('saved','submitted','approved','enabled','rejected','blocked','no_entry') character set utf8 collate utf8_general_ci NULL, 
CHANGE `thu_project_status` `thu_project_status` enum('saved','submitted','approved','enabled','rejected','blocked','no_entry') character set utf8 collate utf8_general_ci NULL, 
CHANGE `thu_status` `thu_status` enum('saved','submitted','approved','enabled','rejected','blocked','no_entry') character set utf8 collate utf8_general_ci NULL, 
CHANGE `fri_project_status` `fri_project_status` enum('saved','submitted','approved','enabled','rejected','blocked','no_entry') character set utf8 collate utf8_general_ci NULL, 
CHANGE `fri_status` `fri_status` enum('saved','submitted','approved','enabled','rejected','blocked','no_entry') character set utf8 collate utf8_general_ci NULL, 
CHANGE `sat_project_status` `sat_project_status` enum('saved','submitted','approved','enabled','rejected','blocked','no_entry') character set utf8 collate utf8_general_ci NULL, 
CHANGE `sat_status` `sat_status` enum('saved','submitted','approved','enabled','rejected','blocked','no_entry') character set utf8 collate utf8_general_ci NULL, 
CHANGE `week_status` `week_status` enum('saved','submitted','approved','enabled','rejected','blocked','no_entry') character set utf8 collate utf8_general_ci NULL; 

/*Table structure for table `tm_configuration` */

DROP TABLE IF EXISTS `tm_configuration`;

CREATE TABLE `tm_configuration` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `ts_weekly_reminder_day` enum('sun','mon','tue','wed','thu','fri','sat') NOT NULL,
  `ts_block_dates_range` varchar(100) NOT NULL,
  `created_by` int(10) unsigned NOT NULL,
  `modified_by` int(10) unsigned DEFAULT NULL,
  `is_active` tinyint(1) unsigned NOT NULL,
  `created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified` timestamp NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

/*Table structure for table `tm_cronjob_status` */

DROP TABLE IF EXISTS `tm_cronjob_status`;

CREATE TABLE `tm_cronjob_status` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `cronjob_status` enum('running','stopped') DEFAULT NULL,
  `start_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `end_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

/*Table structure for table `tm_mailing_list` */

DROP TABLE IF EXISTS `tm_mailing_list`;

CREATE TABLE `tm_mailing_list` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `emp_id` int(10) unsigned DEFAULT NULL,
  `emp_full_name` varchar(150) DEFAULT NULL,
  `email` varchar(200) DEFAULT NULL,
  `mail_type` enum('submit_pending','reminder','block') DEFAULT NULL,
  `ts_dates` text,
  `ts_start_date` date DEFAULT NULL,
  `ts_end_date` date DEFAULT NULL,
  `mail_content` text,
  `is_mail_sent` tinyint(1) unsigned DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `FK_tm_mailing_list_employee` (`emp_id`),
  CONSTRAINT `FK_tm_mailing_list_employee` FOREIGN KEY (`emp_id`) REFERENCES `main_users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

/*Table structure for table `tm_process_updates` */

DROP TABLE IF EXISTS `tm_process_updates`;

CREATE TABLE `tm_process_updates` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `emp_id` int(10) unsigned NOT NULL,
  `ts_dates` text NOT NULL,
  `action_type` enum('edited','rejected','approved','enabled') NOT NULL,
  `note` varchar(200) DEFAULT NULL,
  `alert` enum('open','closed') DEFAULT NULL,
  `action_by` int(10) unsigned NOT NULL,
  `created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified` timestamp NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `FK_tm_process_updates_employee` (`emp_id`),
  CONSTRAINT `FK_tm_process_updates_employee` FOREIGN KEY (`emp_id`) REFERENCES `main_users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;
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

UPDATE main_patches_version SET isactive=0;
INSERT INTO main_patches_version (version, createddate, modifieddate, isactive) VALUES ("3.0 Beta", now(), now(),1);