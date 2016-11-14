set global sql_mode = "STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION";
ALTER TABLE assets MODIFY purchase_date DATE;
ALTER TABLE assets MODIFY warenty_end_date DATE;
ALTER TABLE main_accountclasstype MODIFY createddate DATETIME;
ALTER TABLE main_accountclasstype MODIFY modifieddate DATETIME;
ALTER TABLE main_announcements MODIFY createddate DATETIME;
ALTER TABLE main_announcements MODIFY modifieddate DATETIME;
ALTER TABLE main_assignmententryreasoncode MODIFY createddate DATETIME;
ALTER TABLE main_assignmententryreasoncode MODIFY modifieddate DATETIME;
ALTER TABLE main_attendancestatuscode MODIFY createddate DATETIME;
ALTER TABLE main_attendancestatuscode MODIFY modifieddate DATETIME;
ALTER TABLE main_bankaccounttype MODIFY createddate DATETIME;
ALTER TABLE main_bankaccounttype MODIFY modifieddate DATETIME;
ALTER TABLE main_cities MODIFY createddate DATETIME;
ALTER TABLE main_cities MODIFY modifieddate DATETIME;
ALTER TABLE main_competencylevel MODIFY createddate DATETIME;
ALTER TABLE main_competencylevel MODIFY modifieddate DATETIME;
ALTER TABLE main_countries MODIFY createddate DATETIME;
ALTER TABLE main_countries MODIFY modifieddate DATETIME;
ALTER TABLE main_currency MODIFY createddate DATETIME;
ALTER TABLE main_currency MODIFY modifieddate DATETIME;
ALTER TABLE main_currencyconverter MODIFY createddate DATETIME;
ALTER TABLE main_currencyconverter MODIFY modifieddate DATETIME;
ALTER TABLE main_dateformat MODIFY createddate DATETIME;
ALTER TABLE main_dateformat MODIFY modifieddate DATETIME;
ALTER TABLE main_educationlevelcode MODIFY createddate DATETIME;
ALTER TABLE main_educationlevelcode MODIFY modifieddate DATETIME;
ALTER TABLE main_eeoccategory MODIFY createddate DATETIME;
ALTER TABLE main_eeoccategory MODIFY modifieddate DATETIME;
ALTER TABLE main_empadditionaldetails MODIFY createddate DATETIME;
ALTER TABLE main_empadditionaldetails MODIFY modifieddate DATETIME;
ALTER TABLE main_empcommunicationdetails MODIFY createddate DATETIME;
ALTER TABLE main_empcommunicationdetails MODIFY modifieddate DATETIME;
ALTER TABLE main_empeducationdetails MODIFY createddate DATETIME;
ALTER TABLE main_empeducationdetails MODIFY modifieddate DATETIME;
ALTER TABLE main_empexperiancedetails MODIFY createddate DATETIME;
ALTER TABLE main_empexperiancedetails MODIFY modifieddate DATETIME;
ALTER TABLE main_empholidays MODIFY createddate DATETIME;
ALTER TABLE main_empholidays MODIFY modifieddate DATETIME;
ALTER TABLE main_empjobhistory MODIFY createddate DATETIME;
ALTER TABLE main_empjobhistory MODIFY modifieddate DATETIME;
ALTER TABLE main_employeedocuments MODIFY createddate DATETIME;
ALTER TABLE main_employeedocuments MODIFY modifieddate DATETIME;
ALTER TABLE main_employeeleavetypes MODIFY createddate DATETIME;
ALTER TABLE main_employeeleavetypes MODIFY modifieddate DATETIME;
ALTER TABLE main_employmentstatus MODIFY createddate DATETIME;
ALTER TABLE main_employmentstatus MODIFY modifieddate DATETIME;
ALTER TABLE main_empmedicalclaims MODIFY createddate DATETIME;
ALTER TABLE main_empmedicalclaims MODIFY modifieddate DATETIME;
ALTER TABLE main_emppersonaldetails MODIFY createddate DATETIME;
ALTER TABLE main_emppersonaldetails MODIFY modifieddate DATETIME;
ALTER TABLE main_empsalarydetails MODIFY createddate DATETIME;
ALTER TABLE main_empsalarydetails MODIFY modifieddate DATETIME;
ALTER TABLE main_empskills MODIFY createddate DATETIME;
ALTER TABLE main_empskills MODIFY modifieddate DATETIME;
ALTER TABLE main_empworkeligibility MODIFY createddate DATETIME;
ALTER TABLE main_empworkeligibility MODIFY modifieddate DATETIME;
ALTER TABLE main_ethniccode MODIFY createddate DATETIME;
ALTER TABLE main_ethniccode MODIFY modifieddate DATETIME;
ALTER TABLE main_gender MODIFY createddate DATETIME;
ALTER TABLE main_gender MODIFY modifieddate DATETIME;
ALTER TABLE main_geographygroup MODIFY createddate DATETIME;
ALTER TABLE main_geographygroup MODIFY modifieddate DATETIME;
ALTER TABLE main_holidaygroups MODIFY createddate DATETIME;
ALTER TABLE main_holidaygroups MODIFY modifieddate DATETIME;
ALTER TABLE main_jobtitles MODIFY createddate DATETIME;
ALTER TABLE main_jobtitles MODIFY modifieddate DATETIME;
ALTER TABLE main_language MODIFY createddate DATETIME;
ALTER TABLE main_language MODIFY modifieddate DATETIME;
ALTER TABLE main_licensetype MODIFY createddate DATETIME;
ALTER TABLE main_licensetype MODIFY modifieddate DATETIME;
ALTER TABLE main_maritalstatus MODIFY createddate DATETIME;
ALTER TABLE main_maritalstatus MODIFY modifieddate DATETIME;
ALTER TABLE main_militaryservice MODIFY createddate DATETIME;
ALTER TABLE main_militaryservice MODIFY modifieddate DATETIME;
ALTER TABLE main_monthslist MODIFY createddate DATETIME;
ALTER TABLE main_monthslist MODIFY modifieddate DATETIME;
ALTER TABLE main_nationality MODIFY createddate DATETIME;
ALTER TABLE main_nationality MODIFY modifieddate DATETIME;
ALTER TABLE main_nationalitycontextcode MODIFY createddate DATETIME;
ALTER TABLE main_nationalitycontextcode MODIFY modifieddate DATETIME;
ALTER TABLE main_numberformats MODIFY createddate DATETIME;
ALTER TABLE main_numberformats MODIFY modifieddate DATETIME;
ALTER TABLE main_payfrequency MODIFY createddate DATETIME;
ALTER TABLE main_payfrequency MODIFY modifieddate DATETIME;
ALTER TABLE main_pa_category MODIFY createddate DATETIME;
ALTER TABLE main_pa_category MODIFY modifieddate DATETIME;
ALTER TABLE main_pa_employee_ratings MODIFY createddate DATETIME;
ALTER TABLE main_pa_employee_ratings MODIFY modifieddate DATETIME;
ALTER TABLE main_pa_ff_employee_ratings MODIFY createddate DATETIME;
ALTER TABLE main_pa_ff_employee_ratings MODIFY modifieddate DATETIME;
ALTER TABLE main_pa_manager_initialization MODIFY createddate DATETIME;
ALTER TABLE main_pa_manager_initialization MODIFY modifieddate DATETIME;
ALTER TABLE main_pa_ratings MODIFY createddate DATETIME;
ALTER TABLE main_pa_ratings MODIFY modifieddate DATETIME;
ALTER TABLE main_positions MODIFY createddate DATETIME;
ALTER TABLE main_positions MODIFY modifieddate DATETIME;
ALTER TABLE main_prefix MODIFY createddate DATETIME;
ALTER TABLE main_prefix MODIFY modifieddate DATETIME;
ALTER TABLE main_racecode MODIFY createddate DATETIME;
ALTER TABLE main_racecode MODIFY modifieddate DATETIME;
ALTER TABLE main_remunerationbasis MODIFY createddate DATETIME;
ALTER TABLE main_remunerationbasis MODIFY modifieddate DATETIME;
ALTER TABLE main_roles MODIFY createddate DATETIME;
ALTER TABLE main_roles MODIFY modifieddate DATETIME;
ALTER TABLE main_sd_configurations MODIFY createddate DATETIME;
ALTER TABLE main_sd_configurations MODIFY modifieddate DATETIME;
ALTER TABLE main_sd_depts MODIFY createddate DATETIME;
ALTER TABLE main_sd_depts MODIFY modifieddate DATETIME;
ALTER TABLE main_sd_reqtypes MODIFY createddate DATETIME;
ALTER TABLE main_sd_reqtypes MODIFY modifieddate DATETIME;
ALTER TABLE main_states MODIFY createddate DATETIME;
ALTER TABLE main_states MODIFY modifieddate DATETIME;
ALTER TABLE main_timeformat MODIFY createddate DATETIME;
ALTER TABLE main_timeformat MODIFY modifieddate DATETIME;
ALTER TABLE main_timezone MODIFY createddate DATETIME;
ALTER TABLE main_timezone MODIFY modifieddate DATETIME;
ALTER TABLE main_userloginlog MODIFY logindatetime DATETIME;
ALTER TABLE main_users MODIFY createddate DATETIME;
ALTER TABLE main_users MODIFY modifieddate DATETIME;
ALTER TABLE main_veteranstatus MODIFY createddate DATETIME;
ALTER TABLE main_veteranstatus MODIFY modifieddate DATETIME;
ALTER TABLE main_weekdays MODIFY createddate DATETIME;
ALTER TABLE main_weekdays MODIFY modifieddate DATETIME;
ALTER TABLE main_workeligibilitydoctypes MODIFY createddate DATETIME;
ALTER TABLE main_workeligibilitydoctypes MODIFY modifieddate DATETIME;
ALTER TABLE tbl_employmentstatus MODIFY createddate DATETIME;
ALTER TABLE tbl_employmentstatus MODIFY modifieddate DATETIME;
ALTER TABLE tbl_weeks MODIFY createddate DATETIME;
ALTER TABLE tbl_weeks MODIFY modifieddate DATETIME;
ALTER TABLE tm_emp_ts_notes MODIFY sun_date DATE;
ALTER TABLE assets MODIFY category int;
ALTER TABLE assets MODIFY sub_category int;
ALTER TABLE assets MODIFY company_asset_code int;
ALTER TABLE assets MODIFY allocated_to int;
ALTER TABLE assets MODIFY responsible_technician int;
ALTER TABLE assets MODIFY vendor int;
ALTER TABLE assets MODIFY created_by int;
ALTER TABLE assets MODIFY modified_by int;
ALTER TABLE assets_categories MODIFY parent int;
ALTER TABLE assets_categories MODIFY created_by int;
ALTER TABLE main_candidatedetails MODIFY requisition_id int;
ALTER TABLE main_cronstatus MODIFY cron_status int;
ALTER TABLE main_currencyconverter MODIFY basecurrency int;
ALTER TABLE main_currencyconverter MODIFY targetcurrency int;
ALTER TABLE main_emailcontacts MODIFY group_id int;
ALTER TABLE main_emailcontacts MODIFY business_unit_id int;
ALTER TABLE main_employees_summary MODIFY user_id int;
ALTER TABLE main_empworkdetails MODIFY user_id int;
ALTER TABLE main_hierarchylevels MODIFY level_number int;
ALTER TABLE main_interviewrounddetails MODIFY interview_round_number int;
ALTER TABLE main_mail_settings MODIFY port int;
ALTER TABLE main_requisition MODIFY position_id int;
ALTER TABLE main_requisition_summary MODIFY req_id int;
ALTER TABLE main_requisition_summary MODIFY position_id int;
ALTER TABLE main_roles MODIFY levelid int;
ALTER TABLE main_settings MODIFY userid int;
ALTER TABLE tm_clients MODIFY created_by int;        
ALTER TABLE tm_emp_timesheets MODIFY created_by int;        
ALTER TABLE tm_projects MODIFY created_by int;        
ALTER TABLE tm_project_employees MODIFY created_by int;
ALTER TABLE tm_project_tasks MODIFY created_by int;        
ALTER TABLE tm_project_task_employees MODIFY created_by int;
ALTER TABLE tm_tasks MODIFY created_by int;
ALTER TABLE main_empadditionaldetails MODIFY user_id bigint;
ALTER TABLE main_empcommunicationdetails MODIFY user_id bigint;
ALTER TABLE main_empcreditcarddetails MODIFY user_id bigint;
ALTER TABLE main_empdependencydetails MODIFY user_id bigint;
ALTER TABLE main_empdisabilitydetails MODIFY user_id bigint;
ALTER TABLE main_empeducationdetails MODIFY user_id bigint;
ALTER TABLE main_empexperiancedetails MODIFY user_id bigint;
ALTER TABLE main_empjobhistory MODIFY user_id bigint;
ALTER TABLE main_employeedocuments MODIFY user_id bigint;
ALTER TABLE main_employees MODIFY user_id bigint;
ALTER TABLE main_empmedicalclaims MODIFY user_id bigint;
ALTER TABLE main_emppersonaldetails MODIFY user_id bigint;
ALTER TABLE main_empsalarydetails MODIFY user_id bigint;
ALTER TABLE main_empvisadetails MODIFY user_id bigint;
ALTER TABLE main_empvisadetails MODIFY createdby bigint;
ALTER TABLE main_empvisadetails MODIFY modifiedby bigint;
ALTER TABLE main_empworkeligibility MODIFY user_id bigint;
ALTER TABLE main_emp_reporting MODIFY emp_id bigint;
ALTER TABLE main_emp_reporting MODIFY reporting_manager_id bigint;
ALTER TABLE main_hierarchylevels MODIFY parent bigint;
ALTER TABLE main_hierarchylevels MODIFY userid bigint;
ALTER TABLE main_logmanager MODIFY menuId bigint;
ALTER TABLE main_logmanagercron MODIFY menuId bigint;
ALTER TABLE main_pd_categories MODIFY modifiedby bigint;
ALTER TABLE main_pd_categories MODIFY createdby bigint;
ALTER TABLE main_pd_documents MODIFY category_id bigint;
ALTER TABLE main_sd_reqtypes MODIFY service_desk_id bigint;
ALTER TABLE main_sd_requests_summary MODIFY sd_requests_id bigint;
ALTER TABLE main_weekdays MODIFY day_name bigint;
ALTER TABLE main_candidatedetails MODIFY experience float;
ALTER TABLE assets_categories MODIFY modified_by varchar(11);
ALTER TABLE tm_projects MODIFY lead_approve_ts TINYINT;
ALTER TABLE tbl_countries Modify country_code CHAR(10) CHARSET utf8 COLLATE utf8_general_ci NOT NULL; 
ALTER TABLE assets MODIFY isactive TINYINT;
ALTER TABLE assets_categories MODIFY is_active TINYINT;
ALTER TABLE main_pa_initialization MODIFY performance_app_flag TINYINT;
ALTER TABLE main_pd_categories MODIFY isused TINYINT;
ALTER TABLE main_pd_categories MODIFY isactive TINYINT;
ALTER TABLE main_pd_documents MODIFY isactive TINYINT;
ALTER TABLE main_settings MODIFY flag TINYINT;
ALTER TABLE main_settings MODIFY isactive TINYINT;
ALTER TABLE tbl_cities MODIFY is_active TINYINT;
ALTER TABLE tbl_countries MODIFY is_active TINYINT;
ALTER TABLE tbl_states MODIFY isactive TINYINT;
ALTER TABLE tm_clients MODIFY is_active TINYINT;
ALTER TABLE tm_emp_timesheets MODIFY cal_week TINYINT;
ALTER TABLE tm_emp_timesheets MODIFY is_active TINYINT;
ALTER TABLE tm_emp_ts_notes MODIFY cal_week TINYINT;
ALTER TABLE tm_emp_ts_notes MODIFY is_active TINYINT;
ALTER TABLE tm_projects MODIFY is_active TINYINT;
ALTER TABLE tm_project_employees MODIFY is_active TINYINT;
ALTER TABLE tm_project_tasks MODIFY is_active TINYINT;
ALTER TABLE tm_project_task_employees MODIFY is_active TINYINT;
ALTER TABLE tm_tasks MODIFY is_default TINYINT;
ALTER TABLE tm_tasks MODIFY is_active TINYINT;
ALTER TABLE tm_ts_status MODIFY is_active TINYINT;
ALTER TABLE `assets` CHANGE `invoice_number` `invoice_number` VARCHAR(50) NULL;


insert  into `main_menu`(`id`,`menuName`,`url`,`helpText`,`toolTip`,`iconPath`,`parent`,`menuOrder`,`nav_ids`,`isactive`,`modulename`,`segment_flag`,`org_menuid`,`menufields`,`menuQuery`,`hasJoins`,`modelName`,`functionName`,`defaultOrderBy`) values(201,'Disciplinary','/#','','','',0,21,',21,',1,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
(202,'Violation Type','/disciplinaryviolation','','','',201,1,',201,202,',1,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
(203,'Raise An Incident','/disciplinaryincident','','','',201,2,',201,203,',1,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
(204,'My Incidents','/disciplinarymyincidents','','','',201,3,',201,204,',1,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
(205,'Team Incidents','/disciplinaryteamincidents','','','',201,4,',201,205,',1,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
(206,'All Incidents','/disciplinaryallincidents','','','',201,5,',201,206,',1,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL);



INSERT INTO `main_privileges` (`role`, `group_id`, `object`, `addpermission`, `editpermission`, `deletepermission`, `viewpermission`, `uploadattachments`, `viewattachments`, `createdby`, `modifiedby`, `createddate`, `modifieddate`, `isactive`) VALUES
(1, NULL, 201, 'Yes', 'Yes', 'Yes', 'Yes', 'Yes', 'Yes', 1, 1, NOW(), NOW(), 1),
(1, NULL, 202, 'Yes', 'Yes', 'Yes', 'Yes', 'Yes', 'Yes', 1, 1, NOW(), NOW(), 1),
(1, NULL, 203, 'Yes', 'Yes', 'Yes', 'Yes', 'Yes', 'Yes', 1, 1, NOW(), NOW(), 1),
(1, NULL, 204, 'No', 'Yes', 'No', 'Yes', 'No', 'No', 1, 1, NOW(), NOW(), 1),
(1, NULL, 205, 'No', 'No', 'No', 'Yes', 'No', 'No', 1, 1, NOW(), NOW(), 1),
(1, NULL, 206, 'No', 'No', 'No', 'Yes', 'No', 'No', 1, 1, NOW(), NOW(), 1),

(NULL,1,201,'Yes','Yes','Yes','Yes','No','No',1,1,NOW(),NOW(),1),
(NULL,1,202,'Yes','Yes','Yes','Yes','No','No',1,1,NOW(),NOW(),1),
(NULL,1,203,'Yes','Yes','Yes','Yes','No','No',1,1,NOW(),NOW(),1),
(NULL,1,204,'No','Yes','No','Yes','No','No',1,1,NOW(),NOW(),1),
(NULL,1,205,'No','No','No','Yes','No','No',1,1,NOW(),NOW(),1),
(NULL,1,206,'No','No','No','Yes','No','No',1,1,NOW(),NOW(),1),

(NULL,2,201,'Yes','Yes','Yes','Yes','No','No',1,1,NOW(),NOW(),1),
(NULL,2,204,'No','Yes','No','Yes','No','No',1,1,NOW(),NOW(),1),
(NULL,2,205,'No','No','No','Yes','No','No',1,1,NOW(),NOW(),1),

(NULL,3,201,'Yes','Yes','Yes','Yes','No','No',1,1,NOW(),NOW(),1),
(NULL,3,204,'No','Yes','No','Yes','No','No',1,1,NOW(),NOW(),1),
(NULL,3,205,'No','No','No','Yes','No','No',1,1,NOW(),NOW(),1),
(NULL,3,206,'No','No','No','Yes','No','No',1,1,NOW(),NOW(),1),

(NULL,4,201,'Yes','Yes','Yes','Yes','No','No',1,1,NOW(),NOW(),1),
(NULL,4,204,'No','Yes','No','Yes','No','No',1,1,NOW(),NOW(),1),
(NULL,4,205,'No','No','No','Yes','No','No',1,1,NOW(),NOW(),1),

(NULL,6,201,'Yes','Yes','Yes','Yes','No','No',1,1,NOW(),NOW(),1),
(NULL,6,204,'No','Yes','No','Yes','No','No',1,1,NOW(),NOW(),1),
(NULL,6,205,'No','No','No','Yes','No','No',1,1,NOW(),NOW(),1),

(2, 1, 201, 'Yes', 'Yes', 'Yes', 'Yes', 'Yes', 'Yes', 1, 1, NOW(), NOW(), 1),
(2, 1, 202, 'Yes', 'Yes', 'Yes', 'Yes', 'Yes', 'Yes', 1, 1, NOW(), NOW(), 1),
(2, 1, 203, 'Yes', 'Yes', 'Yes', 'Yes', 'Yes', 'Yes', 1, 1, NOW(), NOW(), 1),
(2, 1, 204, 'No', 'Yes', 'No', 'Yes', 'No', 'No', 1, 1, NOW(), NOW(), 1),
(2, 1, 205, 'No', 'No', 'No', 'Yes', 'No', 'No', 1, 1, NOW(), NOW(), 1),
(2, 1, 206, 'No', 'No', 'No', 'Yes', 'No', 'No', 1, 1, NOW(), NOW(), 1),

(3, 2, 201, 'Yes', 'Yes', 'Yes', 'Yes', 'Yes', 'Yes', 1, 1, NOW(), NOW(), 1),
(3, 2, 204, 'No', 'Yes', 'No', 'Yes', 'No', 'No', 1, 1, NOW(), NOW(), 1),
(3, 2, 205, 'No', 'No', 'No', 'Yes', 'No', 'No', 1, 1, NOW(), NOW(), 1),

(4, 3, 201, 'Yes', 'Yes', 'Yes', 'Yes', 'Yes', 'Yes', 1, 1, NOW(), NOW(), 1),
(4, 3, 204, 'No', 'Yes', 'No', 'Yes', 'No', 'No', 1, 1, NOW(), NOW(), 1),
(4, 3, 205, 'No', 'No', 'No', 'Yes', 'No', 'No', 1, 1, NOW(), NOW(), 1),
(4, 3, 206, 'No', 'No', 'No', 'Yes', 'No', 'No', 1, 1, NOW(), NOW(), 1),

(5, 4, 201, 'Yes', 'Yes', 'Yes', 'Yes', 'Yes', 'Yes', 1, 1, NOW(), NOW(), 1),
(5, 4, 204, 'No', 'Yes', 'No', 'Yes', 'No', 'No', 1, 1, NOW(), NOW(), 1),
(5, 4, 205, 'No', 'No', 'No', 'Yes', 'No', 'No', 1, 1, NOW(), NOW(), 1),

(8, 6, 201, 'Yes', 'Yes', 'Yes', 'Yes', 'Yes', 'Yes', 1, 1, NOW(), NOW(), 1),
(8, 6, 204, 'No', 'Yes', 'No', 'Yes', 'No', 'No', 1, 1, NOW(), NOW(), 1),
(8, 6, 205, 'No', 'No', 'No', 'Yes', 'No', 'No', 1, 1, NOW(), NOW(), 1),

(9, 4, 201, 'Yes', 'Yes', 'Yes', 'Yes', 'Yes', 'Yes', 1, 1, NOW(), NOW(), 1),
(9, 4, 204, 'No', 'Yes', 'No', 'Yes', 'No', 'No', 1, 1, NOW(), NOW(), 1),
(9, 4, 205, 'No', 'No', 'No', 'Yes', 'No', 'No', 1, 1, NOW(), NOW(), 1);

update `main_menu` set `parent`='0',`isactive`='0'where `id` IN(177,178,179,180,181);

CREATE TABLE `main_disciplinary_violation_types` (  
 `id` int(11) unsigned NOT NULL AUTO_INCREMENT,    
 `violationname` varchar(255) NOT NULL,            
 `description` varchar(255) DEFAULT NULL,          
 `createdby` int(11) unsigned DEFAULT NULL,        
 `modifiedby` int(11) unsigned DEFAULT NULL,       
 `createddate` datetime DEFAULT NULL,              
 `modifieddate` datetime DEFAULT NULL,             
 `isactive` tinyint(1) unsigned DEFAULT '1',       
 PRIMARY KEY (`id`)                                
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


CREATE TABLE `main_disciplinary_incident` (                                                                                  
`id` int(11) unsigned NOT NULL AUTO_INCREMENT,                                                                             
`incident_raised_by` bigint(20) unsigned DEFAULT NULL,                                                                     
`employee_bu_id` bigint(20) unsigned DEFAULT NULL,                                                                         
`employee_dept_id` bigint(20) unsigned DEFAULT NULL,                                                                       
`employee_id` bigint(20) unsigned DEFAULT NULL,                                                                            
`employee_rep_mang_id` bigint(20) unsigned DEFAULT NULL,

`date_of_occurrence` date DEFAULT NULL,
                                                                   
`violation_id` bigint(20) unsigned DEFAULT NULL,                                                                           
`violation_expiry` date DEFAULT NULL,                                                                                      
`employee_job_title_id` bigint(20) unsigned DEFAULT NULL,                                                                  
`employer_statement` text,                                                                                                 
`employee_appeal` tinyint(1) DEFAULT '1' COMMENT '1=Yes,2=No',                                                             
`employee_statement` text,                                                                                                 
`cao_verdict` tinyint(1) DEFAULT '1' COMMENT '1=guilty,2=not guilty',                                                      
`corrective_action` enum('Suspension With Pay','Suspension W/O Pay','Termination','Other') DEFAULT 'Suspension With Pay',  
`corrective_action_text` varchar(255) DEFAULT NULL,                                                                        
`incident_status` enum('Initiated','Appealed','Closed') DEFAULT 'Initiated',                                               
`createdby` bigint(20) unsigned DEFAULT NULL,                                                                              
`modifiedby` bigint(20) unsigned DEFAULT NULL,                                                                             
`createddate` datetime DEFAULT NULL,                                                                                       
`modifieddate` datetime DEFAULT NULL,                                                                                      
`isactive` tinyint(1) DEFAULT '1',                                                                                         
PRIMARY KEY (`id`)                                                                                                         
) ENGINE=MyISAM DEFAULT CHARSET=latin1;    


CREATE TABLE `main_disciplinary_history` (               
 `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,      
 `incident_id` bigint(20) unsigned DEFAULT NULL,        
 `description` varchar(300) DEFAULT NULL,               
 `action_emp_id` bigint(20) unsigned DEFAULT NULL,      
 `createdby` bigint(20) unsigned DEFAULT NULL,          
 `createddate` datetime DEFAULT NULL,                   
 PRIMARY KEY (`id`)                                     
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


DELIMITER $$

DROP TRIGGER `main_users_aft_upd`$$

CREATE
   
    TRIGGER `main_users_aft_upd` AFTER UPDATE ON `main_users` 
    FOR EACH ROW BEGIN
    declare groupid int(11);
    
    select group_id into groupid from main_roles where id = old.emprole;
    if old.userfullname != new.userfullname then
    begin 
    
    if (groupid != 5 or groupid is null) then 
    begin
	
        update main_leaverequest_summary set rep_manager_name = new.userfullname,modifieddate = utc_timestamp() where rep_mang_id = new.id and isactive = 1;
        update main_leaverequest_summary set user_name = new.userfullname,modifieddate = utc_timestamp() where user_id = new.id and isactive = 1; 
	
	
	update main_requisition_summary set reporting_manager_name = new.userfullname,modifiedon = utc_timestamp() where reporting_id = new.id and isactive = 1;
	update main_requisition_summary set approver1_name = new.userfullname,modifiedon = utc_timestamp() where approver1 = new.id and isactive = 1;
	update main_requisition_summary set approver2_name = new.userfullname,modifiedon = utc_timestamp() where approver2 = new.id and isactive = 1;
	update main_requisition_summary set approver3_name = new.userfullname,modifiedon = utc_timestamp() where approver3 = new.id and isactive = 1;
	update main_requisition_summary set createdby_name = new.userfullname,modifiedon = utc_timestamp() where createdby = new.id and isactive = 1;
	
	
	update main_employees_summary set reporting_manager_name = new.userfullname,modifieddate = utc_timestamp() where reporting_manager = new.id and isactive = 1;
	update main_employees_summary set referer_name = new.userfullname,modifieddate = utc_timestamp() where candidatereferredby = new.id and isactive = 1;
	update main_employees_summary set createdby_name = new.userfullname,modifieddate = utc_timestamp() where createdby = new.id and isactive = 1;
        update main_employees_summary set userfullname = new.userfullname,modifieddate = utc_timestamp() where user_id = new.id and isactive = 1;
	
	
	update main_bgchecks_summary set specimen_name = new.userfullname,modifieddate = utc_timestamp() where specimen_id = new.id and specimen_flag = 1 and isactive = 1;
	update main_bgchecks_summary set createdname = new.userfullname,modifieddate = utc_timestamp() where createdby = new.id and isactive = 1;
	update main_bgchecks_summary set modifiedname = new.userfullname,modifieddate = utc_timestamp() where modifiedby = new.id and isactive = 1;
	
	
	update main_interviewrounds_summary set interviewer_name = new.userfullname,modified_date = utc_timestamp() where interviewer_id = new.id and isactive = 1;
	update main_interviewrounds_summary set created_by_name = new.userfullname,modified_date = utc_timestamp() where created_by = new.id and isactive = 1;
	
	
	update main_userloginlog set userfullname = new.userfullname where userid = new.id;
	
	
	update main_sd_requests_summary set raised_by_name = new.userfullname,modifieddate = utc_timestamp() where raised_by = new.id;
	update main_sd_requests_summary set executor_name = new.userfullname,modifieddate = utc_timestamp() where executor_id = new.id;
	update main_sd_requests_summary set reporting_manager_name = new.userfullname,modifieddate = utc_timestamp() where reporting_manager_id = new.id;
	update main_sd_requests_summary set approver_1_name = new.userfullname,modifieddate = utc_timestamp() where approver_1 = new.id;	
	update main_sd_requests_summary set approver_2_name = new.userfullname,modifieddate = utc_timestamp() where approver_2 = new.id;
	update main_sd_requests_summary set approver_3_name = new.userfullname,modifieddate = utc_timestamp() where approver_3 = new.id;
	
    end;
    end if;
    end;
    end if;
    if old.employeeId != new.employeeId then 
    begin 
        if (groupid != 5 or groupid is null) then 
        begin
	    
            update main_employees_summary set employeeId = new.employeeId,modifieddate = utc_timestamp() where user_id = new.id; 
            
        end;
        end if;
    end;
    end if;
    if old.isactive != new.isactive then
    begin
	if (groupid != 5 or groupid is null) then 
        begin
	    
            update main_employees_summary set isactive = new.isactive,modifieddate = utc_timestamp() where user_id = new.id; 
            
        end;
        end if;
    end;
    end if; 
    if old.profileimg != new.profileimg then
    begin
	if (groupid != 5 or groupid is null) then 
        begin
	    
            update main_employees_summary set profileimg = new.profileimg,modifieddate = utc_timestamp() where user_id = new.id; 
            
	    
            update main_request_history set emp_profileimg = new.profileimg,modifieddate = utc_timestamp() where emp_id = new.id; 
            
        end;
        end if;
    end;
    end if; 
    if old.backgroundchk_status != new.backgroundchk_status then
    begin
	if (groupid != 5 or groupid is null) then 
        begin
	    
            update main_employees_summary set backgroundchk_status = new.backgroundchk_status,modifieddate = utc_timestamp() where user_id = new.id; 
            
        end;
        end if;
    end;
    end if;
if (old.contactnumber != new.contactnumber || new.contactnumber IS NOT NULL) then
    begin
	if (groupid != 5 or groupid is null) then 
        begin
	    
            update main_employees_summary set contactnumber = new.contactnumber,modifieddate = utc_timestamp() where user_id = new.id; 
            
        end;
        end if;
    end;
    end if;
    
    END;
$$

DELIMITER ;
  