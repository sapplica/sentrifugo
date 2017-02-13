<?php
/********************************************************************************* 
 *  This file is part of Sentrifugo.
 *  Copyright (C) 2014 Sapplica
 *   
 *  Sentrifugo is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  Sentrifugo is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with Sentrifugo.  If not, see <http://www.gnu.org/licenses/>.
 *
 *  Sentrifugo Support <support@sentrifugo.com>
 ********************************************************************************/ 
?>

<?php 


/* Trigger structure for table `main_bgcheckdetails` */
$GLOBALS['qry1'] = "CREATE TRIGGER `main_agencylist_aft_upd` AFTER UPDATE ON `main_bgagencylist` FOR EACH ROW BEGIN
					if(old.agencyname != new.agencyname) then
					update main_bgchecks_summary set agencyname = new.agencyname,modifieddate = utc_timestamp() where agencyid = new.id and isactive = 1;
					end if;
				    END";

/* Trigger structure for table `main_bgcheckdetails` */
$GLOBALS['qry2'] = "CREATE TRIGGER `main_bgchecks_summary` AFTER INSERT ON `main_bgcheckdetails` FOR EACH ROW BEGIN
					declare detailid,specimen_id, specimen_name, emp_id, specimen_flag_name, agency_name, 
						screening_type, created_name, modified_name,createdbyname,
						modifiedbyname varchar(250);
					declare	specimen_flag_id,agency_id,screeningtype_id int(11);
					select id,flag,if(flag=1,'Employee','Candidate')
						into detailid,specimen_flag_id,specimen_flag_name
						from main_bgcheckdetails where id = new.id;
					if(specimen_flag_id = 1)then
						select userfullname,id, employeeId into specimen_name,specimen_id,emp_id from main_users where id = new.specimen_id;
					end if;
					if(specimen_flag_id = 2) then
						select candidate_name,id into specimen_name,specimen_id from main_candidatedetails where id = new.specimen_id;
					end if;
					select userfullname into createdbyname from main_users where id = new.createdby;
					select userfullname into modifiedbyname from main_users where id = new.modifiedby;
					select id,agencyname into agency_id,agency_name from main_bgagencylist where id = new.bgagency_id;
					select id,type into screeningtype_id,screening_type from main_bgchecktype where id = new.bgcheck_type;
					
					insert into main_bgchecks_summary 
					(detail_id,specimen_name,specimen_id, specimen_flag,specimen_flag_name,employee_id,screeningtypeid,screeningtype_name,
						agencyid,agencyname,process_status,month_name,year_year,createddate,modifieddate,createdby,createdname,modifiedby,modifiedname,
						isactive,isactive_text)
					values
					(detailid,specimen_name,specimen_id,specimen_flag_id,specimen_flag_name,emp_id,screeningtype_id,screening_type, 
					agency_id,agency_name,new.process_status,month(new.createddate),year(new.createddate),new.createddate,new.modifieddate,new.createdby,createdbyname,new.modifiedby,modifiedbyname,
					new.isactive,
					if(new.isactive = 0,'Process deleted',if(new.isactive = 1,'Active',if(new.isactive = 2,'Agency deleted',if(new.isactive = 3,'Agency User deleted',if(new.isactive = 4,'POC deleted','Active'))))));
					
				    END";	

/* Trigger structure for table `main_bgchecktype` */
$GLOBALS['qry3'] = "CREATE TRIGGER `main_bgchecks_aft_update` AFTER UPDATE ON `main_bgcheckdetails` FOR EACH ROW BEGIN
					declare detailid,specimen_id, specimen_name, emp_id, specimen_flag_name, agency_name, 
						screening_type, created_name, modified_name,createdbyname,
						modifiedbyname varchar(250);
					declare	specimen_flag_id,agency_id,screeningtype_id int(11);
					select id,flag,if(flag=1,'Employee','Candidate')
						into detailid,specimen_flag_id,specimen_flag_name
						from main_bgcheckdetails where id = new.id;
					if(specimen_flag_id = 1)then
						select userfullname,id, employeeId into specimen_name,specimen_id,emp_id from main_users where id = new.specimen_id;
					end if;
					if(specimen_flag_id = 2) then
						select candidate_name,id into specimen_name,specimen_id from main_candidatedetails where id = new.specimen_id;
					end if;
					select userfullname into createdbyname from main_users where id = new.createdby;
					select userfullname into modifiedbyname from main_users where id = new.modifiedby;
					select id,agencyname into agency_id,agency_name from main_bgagencylist where id = new.bgagency_id;
					select id,type into screeningtype_id,screening_type from main_bgchecktype where id = new.bgcheck_type;
					UPDATE  main_bgchecks_summary set	
					detail_id = new.id , 
					specimen_name = specimen_name , 
					specimen_id = specimen_id , 
					specimen_flag = specimen_flag_id , 
					specimen_flag_name = specimen_flag_name , 
					employee_id = emp_id , 
					screeningtypeid = screeningtype_id , 
					screeningtype_name = screening_type , 
					agencyid = agency_id , 
					agencyname = agency_name , 
					process_status = new.process_status , 
					modifieddate = new.modifieddate, 
					modifiedby = new.modifiedby , 
					modifiedname = modifiedbyname , 
					isactive = new.isactive , 
					isactive_text = if(new.isactive = 0,'Process deleted',if(new.isactive = 1,'Active',if(new.isactive = 2,'Agency deleted',if(new.isactive = 3,'Agency User deleted',if(new.isactive = 4,'POC deleted','Active')))))
					where
					detail_id = new.id ;
				    END ";

/* Trigger structure for table `main_bgchecktype` */
$GLOBALS['qry4'] = "CREATE TRIGGER `main_screeningtype_aft_upd` AFTER UPDATE ON `main_bgchecktype` FOR EACH ROW BEGIN
					if(old.type != new.type) then
					update main_bgchecks_summary set screeningtype_name = new.type,modifieddate = utc_timestamp() where screeningtypeid = new.id and isactive = 1;
					end if;
				    END ";

/* Trigger structure for table `main_businessunits` */
$GLOBALS['qry5'] = 'CREATE TRIGGER `main_businessunits_main_requisition_summary` AFTER UPDATE ON `main_businessunits` FOR EACH ROW BEGIN
					UPDATE main_requisition_summary rs SET rs.businessunit_name = NEW.unitname, rs.modifiedon = utc_timestamp() WHERE (rs.businessunit_id = NEW.id 
					AND rs.businessunit_name != NEW.unitname);
				        UPDATE main_leaverequest_summary ls SET ls.buss_unit_name = if(NEW.unitcode != "000",concat(NEW.unitcode,"","-"),""), ls.modifieddate = utc_timestamp() 
				        WHERE (ls.bunit_id = NEW.id AND ls.isactive=1);
				        
				        update main_leavemanagement_summary lm set lm.businessunit_name = if(NEW.unitcode != "000",concat(NEW.unitcode,"","-"),""),lm.modifieddate = utc_timestamp() 
				        where lm.businessunit_id = new.id and lm.isactive = 1;
					#start of main_employees_summary
					update main_employees_summary set businessunit_name = new.unitname,modifieddate = utc_timestamp() where businessunit_id = new.id and isactive = 1;
					#end of main_employees_summary
				    END';																	

/* Trigger structure for table `main_candidatedetails` */
$GLOBALS['qry6'] = "CREATE TRIGGER `main_candidates_aft_upd` AFTER UPDATE ON `main_candidatedetails` FOR EACH ROW BEGIN
					if(old.candidate_name != new.candidate_name) then
					begin
					update main_bgchecks_summary set specimen_name = new.candidate_name,modifieddate = utc_timestamp() where specimen_id = new.id and specimen_flag = 2 and isactive = 1;
					update main_interviewrounds_summary set candidate_name = new.candidate_name,modified_date = utc_timestamp() where candidate_id = new.id and isactive = 1;
				        end;
					end if;
				    END";


/* Trigger structure for table `main_cities` */
$GLOBALS['qry7'] = "CREATE TRIGGER `main_cities_aft_upd` AFTER UPDATE ON `main_cities` FOR EACH ROW BEGIN
					if old.city != new.city then 
				        begin 
				           update main_interviewrounds_summary set interview_city_name = new.city,modified_date = utc_timestamp() where interview_city_id = new.city_org_id and isactive = 1;
				        end;
				        end if;
				    END";

/* Trigger structure for table `main_countries` */
$GLOBALS['qry8'] = "CREATE TRIGGER `main_countries_aft_upd` AFTER UPDATE ON `main_countries` FOR EACH ROW BEGIN
					if old.country != new.country then 
					begin 
					update main_interviewrounds_summary set interview_country_name = new.country,modified_date = utc_timestamp() where interview_country_id = new.country_id_org and isactive = 1;
					end;
					end if;
				    END";

/* Trigger structure for table `main_departments` */
$GLOBALS['qry9'] = 'CREATE TRIGGER `main_departments_main_requisition_summary` AFTER UPDATE ON `main_departments` FOR EACH ROW BEGIN
			        declare unit_code varchar(200);
				UPDATE main_requisition_summary rs SET rs.department_name = CASE WHEN NEW.isactive=1 then NEW.deptname ELSE NULL END, rs.modifiedon = utc_timestamp() 
	WHERE (rs.department_id = NEW.id);
			        update main_leaverequest_summary ls set ls.department_name = concat(new.deptname," (",new.deptcode,")"),ls.modifieddate = utc_timestamp() 
			        where ls.department_id = new.id and ls.isactive = 1;
			        update main_leavemanagement_summary lm set lm.department_name = concat(new.deptname," (",new.deptcode,")"),lm.modifieddate = utc_timestamp() 
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
				end;
				end if;
			        # End
			    END';

/* Trigger structure for table `main_employeeleavetypes` */
$GLOBALS['qry10'] = "CREATE TRIGGER `main_employeeleavetypes_aft_upd` AFTER UPDATE ON `main_employeeleavetypes` FOR EACH ROW BEGIN
				     update main_leaverequest_summary ls set ls.leavetype_name = new.leavetype,ls.modifieddate = utc_timestamp() 
				     where ls.leavetypeid = new.id and ls.isactive = 1;
				    END";

/* Trigger structure for table `main_employees_aft_ins` */
$GLOBALS['qry11'] = "CREATE TRIGGER `main_employees_aft_ins` AFTER INSERT ON `main_employees` 
				    FOR EACH ROW BEGIN
					declare user_id,fname,lname,username,role_name,rep_name,emp_status,bunit_name,dept_name,job_name,pos_name,prefix_name,
						createdbyname,holidaygrp,modifiedbyname,emailid,cnumber,bgstatus,empid,mode_entry,omode_entry,sel_date,
				                ref_by_name,img_src
						varchar(250);
					declare ref_by_id,role_id int(11);
					select firstname,lastname,userfullname,emailaddress,contactnumber,backgroundchk_status,employeeId,modeofentry,other_modeofentry,selecteddate,candidatereferredby,
				               profileimg,emprole  
						into fname,lname,username,emailid,cnumber,bgstatus,empid,mode_entry,omode_entry,sel_date,ref_by_id,img_src,role_id 
					from main_users where id = new.user_id;
					select userfullname into rep_name from main_users where id = new.reporting_manager;
				/*
					select employemnt_status into emp_status from tbl_employmentstatus where id = (select workcodename 
					from main_employmentstatus where id = new.emp_status_id);*/
					select employemnt_status into emp_status from tbl_employmentstatus where id = new.emp_status_id	;
					set user_id = new.user_id;
					set bunit_name = null;
					if new.businessunit_id is not null then
						select unitname into bunit_name from main_businessunits where id = new.businessunit_id;
					end if;
					set holidaygrp = null;
					if new.holiday_group is not null then
						select groupname into holidaygrp from main_holidaygroups where id = new.holiday_group;
					end if;
					select deptname into dept_name from main_departments where id = new.department_id;
					select jobtitlename into job_name from main_jobtitles where id = new.jobtitle_id;
					select positionname into pos_name from main_positions where id = new.position_id;
					select prefix into prefix_name from main_prefix where id = new.prefix_id;
					select userfullname into createdbyname from main_users where id = new.createdby;
					select rolename into role_name from main_roles where id = role_id;
					if (ref_by_id != '' and ref_by_id > 0) then 
				        begin 
					    select userfullname into ref_by_name from main_users where id = ref_by_id;
				        end;
				        end if;
				insert into main_employees_summary ( 
					user_id, date_of_joining, date_of_leaving, reporting_manager, reporting_manager_name, emp_status_id, 
					emp_status_name, businessunit_id, businessunit_name, department_id, department_name, jobtitle_id, 
					jobtitle_name, position_id, position_name, years_exp, holiday_group, holiday_group_name, 
					prefix_id, prefix_name, extension_number, office_number, office_faxnumber, emprole, 
					emprole_name, firstname,lastname,userfullname, emailaddress, contactnumber, backgroundchk_status, 	employeeId, 
					modeofentry, other_modeofentry, selecteddate, candidatereferredby, referer_name, profileimg, 
					createdby, createdby_name, modifiedby, createddate, modifieddate, isactive)
					values	(	
					new.user_id, new.date_of_joining, new.date_of_leaving,new.reporting_manager,rep_name,new.emp_status_id, 
					emp_status,new.businessunit_id,	bunit_name,new.department_id,dept_name,new.jobtitle_id, 
					job_name, new.position_id, pos_name,new.years_exp, new.holiday_group, holidaygrp, 
					new.prefix_id, 	prefix_name, new.extension_number, new.office_number, new.office_faxnumber,role_id, 
					role_name,fname,lname,username, emailid,cnumber,bgstatus,empid, 
					mode_entry,omode_entry,	sel_date, ref_by_id, ref_by_name,img_src, 
					new.createdby, 	createdbyname, new.modifiedby,new.createddate, new.modifieddate, new.isactive
					);
				    END";

/* Trigger structure for table `main_employees_aft_upd` */
$GLOBALS['qry12'] = "CREATE TRIGGER `main_employees_aft_upd` AFTER UPDATE ON `main_employees` 
				    FOR EACH ROW BEGIN
					declare fname,lname,username,role_name,rep_name,emp_status,bunit_name,dept_name,job_name,pos_name,prefixname,
						createdbyname,holidaygrp,modifiedbyname,emailid,cnumber,bgstatus,empid,mode_entry,omode_entry,sel_date,
				                ref_by_name,img_src
						varchar(250);
					declare ref_by_id,role_id int(11);
					select firstname,lastname,userfullname,emailaddress,contactnumber,backgroundchk_status,employeeId,modeofentry,other_modeofentry,selecteddate,candidatereferredby,
				               profileimg,emprole  
						into fname,lname,username,emailid,cnumber,bgstatus,empid,mode_entry,omode_entry,sel_date,ref_by_id,img_src,role_id 
					from main_users where id = new.user_id;
					select userfullname into rep_name from main_users where id = new.reporting_manager;
					/*select employemnt_status into emp_status from tbl_employmentstatus where id = (select workcodename 
					from main_employmentstatus where id = new.emp_status_id);*/
					select employemnt_status into emp_status from tbl_employmentstatus where id = new.emp_status_id	;
					set bunit_name = null;
					if new.businessunit_id is not null then
						select unitname into bunit_name from main_businessunits where id = new.businessunit_id;
					end if;
					set holidaygrp = null;
					if new.holiday_group is not null then
						select groupname into holidaygrp from main_holidaygroups where id = new.holiday_group;
					end if;
					select deptname into dept_name from main_departments where id = new.department_id;
					select jobtitlename into job_name from main_jobtitles where id = new.jobtitle_id;
					select positionname into pos_name from main_positions where id = new.position_id;
					select prefix into prefixname from main_prefix where id = new.prefix_id;
					select userfullname into createdbyname from main_users where id = new.createdby;
					select rolename into role_name from main_roles where id = role_id;
					if (ref_by_id != '' and ref_by_id > 0) then 
				        begin 
					    select userfullname into ref_by_name from main_users where id = ref_by_id;
				        end;
				        end if;
				        update main_employees_summary set  
					 date_of_joining = new.date_of_joining, date_of_leaving = new.date_of_leaving, reporting_manager = new.reporting_manager, 
				         reporting_manager_name = rep_name, emp_status_id = new.emp_status_id, 	emp_status_name = emp_status, 
					businessunit_id = new.businessunit_id, businessunit_name = bunit_name, department_id = new.department_id, 
				        department_name = dept_name, jobtitle_id = new.jobtitle_id,jobtitle_name = job_name, position_id = new.position_id, 
				        position_name = pos_name, years_exp = new.years_exp, holiday_group = new.holiday_group, holiday_group_name = holidaygrp, 
					prefix_id = new.prefix_id, prefix_name = prefixname, extension_number = new.extension_number, office_number = new.office_number, 
					office_faxnumber = new.office_faxnumber, emprole = role_id, emprole_name = role_name, firstname=fname, lastname=lname,userfullname = username, 
					emailaddress = emailid, contactnumber = cnumber, backgroundchk_status = bgstatus,employeeId = empid, 
					modeofentry = mode_entry, other_modeofentry = omode_entry, selecteddate = sel_date, candidatereferredby = ref_by_id,
					referer_name = ref_by_name, profileimg = img_src,  modifiedby = new.modifiedby, modifieddate = new.modifieddate, isactive = new.isactive
					
					 where user_id = new.user_id;
				    END";

/* Trigger structure for table `main_employmentstatus` */
$GLOBALS['qry13'] = "CREATE TRIGGER `main_employmentstatus_main_requisition_summary` AFTER UPDATE ON `main_employmentstatus` FOR EACH ROW BEGIN
					declare empt_name varchar(250);
					UPDATE main_requisition_summary rs 
					LEFT JOIN main_employmentstatus mes ON mes.workcodename = rs.emp_type
					LEFT JOIN tbl_employmentstatus tes ON tes.id = mes.workcodename
					SET rs.emp_type_name = tes.employemnt_status, rs.modifiedon = utc_timestamp()
					WHERE (rs.emp_type_name != tes.employemnt_status);
					select te.employemnt_status into empt_name from main_employmentstatus em 
				       inner join tbl_employmentstatus te on te.id = em.workcodename where em.id = new.id;
					#start of main_employees_summary
					update main_employees_summary set emp_status_name = empt_name,modifieddate = utc_timestamp() where emp_status_id = new.id and isactive = 1;
					#end of main_employees_summary
				    END";

/* Trigger structure for table `main_holidaygroups` */
$GLOBALS['qry14'] = "CREATE TRIGGER `main_holidaygroups_aft_ins` AFTER UPDATE ON `main_holidaygroups` FOR EACH ROW BEGIN
				    if old.groupname != new.groupname then 
				    begin 
					update main_employees_summary set holiday_group_name = new.groupname,modifieddate = utc_timestamp() where isactive = 1 and holiday_group = new.id;
				    end;
				    end if;
				    END";

/* Trigger structure for table `main_identitycodes` */
$GLOBALS['qry15'] = "CREATE TRIGGER `main_identitycodes_aft_upd` AFTER UPDATE ON `main_identitycodes` 
				    FOR EACH ROW BEGIN
				    if old.employee_code != new.employee_code then 
				    begin
					update main_users set employeeId = replace(employeeId,SUBSTRING(employeeId,1,CHAR_LENGTH(old.employee_code)),new.employee_code),modifieddate = utc_timestamp() where SUBSTRING(employeeId,1,CHAR_LENGTH(old.employee_code)) = old.employee_code;
				    end;
				    end if;
				    if old.backgroundagency_code != new.backgroundagency_code then 
				    begin
					update main_users set employeeId = replace(employeeId,SUBSTRING(employeeId,1,CHAR_LENGTH(old.backgroundagency_code)),new.backgroundagency_code),modifieddate = utc_timestamp() where SUBSTRING(employeeId,1,CHAR_LENGTH(old.backgroundagency_code)) = old.backgroundagency_code;
				    end;
				    end if;
				    if old.users_code != new.users_code then 
				    begin
					update main_users set employeeId = replace(employeeId,SUBSTRING(employeeId,1,CHAR_LENGTH(old.users_code)),new.users_code),modifieddate = utc_timestamp() where SUBSTRING(employeeId,1,CHAR_LENGTH(old.users_code)) = old.users_code;
				    end;
				    end if;	
				    if old.requisition_code != new.requisition_code then 
				    begin
					update main_requisition r set r.requisition_code = replace(r.requisition_code,left(r.requisition_code,LOCATE('/',r.requisition_code)),CONCAT(new.requisition_code,'/')),r.modifiedon = utc_timestamp() where left(r.requisition_code,LOCATE('/',r.requisition_code)) = CONCAT(old.requisition_code,'/');
				    end;
				    end if;
				    END";

/* Trigger structure for table `main_interviewdetails` */
$GLOBALS['qry16'] = "CREATE TRIGGER `main_interviewdetails_aft_upd` AFTER UPDATE ON `main_interviewdetails` FOR EACH ROW BEGIN
					if old.interview_status != new.interview_status then 
				        begin 
					update main_interviewrounds_summary set interview_status = new.interview_status,modified_date = utc_timestamp() where interview_id = new.id and isactive = 1;
					end;
				        end if;
				    END";

/* Trigger structure for table `main_interviewrounddetails` */
$GLOBALS['qry17'] = "CREATE TRIGGER `main_interviewrounddetails_aft_ins` AFTER INSERT ON `main_interviewrounddetails` FOR EACH ROW BEGIN
					declare cand_name,cstatus,istatus,int_name,cityname,statename,countryname,created_name varchar(255);
					select candidate_name,cand_status into cand_name,cstatus from main_candidatedetails where id = new.candidate_id and isactive =1;
					select userfullname into int_name from main_users where id = new.interviewer_id and isactive =1;
					select userfullname into created_name from main_users where id = new.createdby and isactive =1;
					select interview_status into istatus from main_interviewdetails where id = new.interview_id and isactive =1;
					select city into cityname from main_cities where city_org_id = new.int_city and isactive =1;
					select state into statename from main_states where state_id_org = new.int_state and isactive =1;
					select country into countryname from main_countries where country_id_org = new.int_country and isactive =1;
					insert into main_interviewrounds_summary 
					(requisition_id, candidate_id, candidate_name,candidate_status, interview_status, interview_id, interviewround_id, 
					interviewer_id, interviewer_name, interview_time, interview_date, interview_mode, interview_round_number, 
					interview_round_name, interview_location, interview_city_id, interview_state_id, interview_city_name, 
					interview_state_name, interview_country_id, interview_country_name, created_by, created_by_name, 
					interview_feedback, interview_comments, round_status, modified_by, created_date, modified_date, 
					isactive)
					values
					( new.req_id, new.candidate_id,	cand_name,cstatus,istatus,new.interview_id,new.id, 	
					new.interviewer_id,int_name,new.interview_time,new.interview_date,new.interview_mode,new.interview_round_number, 
					new.interview_round,new.int_location, 	new.int_city,new.int_state,cityname, 
					statename,new.int_country,countryname,new.createdby,created_name, 
					new.interview_feedback, new.interview_comments,	new.round_status,new.modifiedby, new.createddate, new.modifieddate, 
					new.isactive
					);
				    END";


/* Trigger structure for table `main_interviewrounddetails` */
$GLOBALS['qry18'] = "CREATE TRIGGER `main_interviewrounddetails_aft_upd` AFTER UPDATE ON `main_interviewrounddetails` FOR EACH ROW BEGIN
					declare cand_name,cstatus,istatus,int_name,cityname,statename,countryname varchar(255);
					select candidate_name,cand_status into cand_name,cstatus from main_candidatedetails where id = new.candidate_id and isactive =1;
					select userfullname into int_name from main_users where id = new.interviewer_id and isactive =1;
					
					select interview_status into istatus from main_interviewdetails where id = new.interview_id and isactive =1;
					select city into cityname from main_cities where city_org_id = new.int_city and isactive =1;
					select state into statename from main_states where state_id_org = new.int_state and isactive =1;
					select country into countryname from main_countries where country_id_org = new.int_country and isactive =1;
					update main_interviewrounds_summary set
					 candidate_name = cand_name,candidate_status = cstatus, interview_status = istatus,  
					interviewer_id = new.interviewer_id, interviewer_name = int_name, interview_time = new.interview_time,
					interview_date = new.interview_date, interview_mode = new.interview_mode, interview_round_number = new.interview_round_number, 
					interview_round_name = new.interview_round, interview_location = new.int_location, interview_city_id = new.int_city,
					interview_state_id = new.int_state, interview_city_name = cityname,interview_state_name = statename,
					interview_country_id = new.int_country, interview_country_name = countryname, interview_feedback = new.interview_feedback, 
					interview_comments = new.interview_comments, round_status = new.round_status, modified_by = new.modifiedby, 
					modified_date = new.modifieddate,isactive = new.isactive
					
					 where interviewround_id = new.id;
				    END";

/* Trigger structure for table `main_jobtitles` */
$GLOBALS['qry19'] = "CREATE TRIGGER `main_jobtitles_aft_upd` AFTER UPDATE ON `main_jobtitles` FOR EACH ROW BEGIN
				    if old.jobtitlename != new.jobtitlename then 
				    begin 
					update main_requisition_summary set jobtitle_name = new.jobtitlename,modifiedon = utc_timestamp() where isactive = 1 and jobtitle = new.id;
					update main_employees_summary set jobtitle_name = new.jobtitlename,modifieddate = utc_timestamp() where isactive = 1 and jobtitle_id = new.id;
				    end;
				    end if;
				    
				    END";

/* Trigger structure for table `main_leavemanagement_aft_ins` */
$GLOBALS['qry20'] = 'CREATE TRIGGER `main_leavemanagement_aft_ins` AFTER INSERT ON `main_leavemanagement` FOR EACH ROW BEGIN
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
				    insert into main_leavemanagement_summary (leavemgmt_id, cal_startmonth, cal_startmonthname, 
				    weekend_startday, weekend_startdayname, weekend_endday,weekend_enddayname, businessunit_id, 
				    businessunit_name, department_id, department_name, hours_day, is_satholiday, is_halfday, 
				    is_leavetransfer, is_skipholidays, description, createdby, modifiedby, createddate, 
				    modifieddate, isactive)
				    values(new.id,new.cal_startmonth, calmonth_name, new.weekend_startday, weekend_name1,
				    new.weekend_endday,weekend_name2,bunit_id, buss_unit_name, new.department_id, 
				    dept_name, new.hours_day, new.is_satholiday, new.is_halfday, new.is_leavetransfer, 
				    new.is_skipholidays, new.description,  new.createdby, new.modifiedby, new.createddate, 
				    new.modifieddate, new.isactive);
				    END';

/* Trigger structure for table `main_leavemanagement_aft_upd` */
$GLOBALS['qry21'] = 'CREATE TRIGGER `main_leavemanagement_aft_upd` AFTER UPDATE ON `main_leavemanagement` FOR EACH ROW BEGIN
				    declare calmonth_name,weekend_name1,weekend_name2,dept_name,buss_unit_name varchar(200);
				    declare bunit_id bigint(20);
				    select month_name into calmonth_name from tbl_months where monthid = new.cal_startmonth;
				    select week_name into weekend_name1 from tbl_weeks where week_id = new.weekend_startday;
				    select week_name into weekend_name2 from tbl_weeks where week_id = new.weekend_endday;
				    select b.id,concat(d.deptname," (",d.deptcode,")") ,
				    if(b.unitcode != "000",concat(b.unitcode,"","-"),"") into bunit_id,dept_name,buss_unit_name 
				    FROM `main_departments` AS `d` LEFT JOIN `main_businessunits` AS `b` ON b.id=d.unitid 
				    WHERE (d.isactive = 1 and d.id = new.department_id);
				    UPDATE  main_leavemanagement_summary set
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
				    is_leavetransfer = new.is_leavetransfer, 
				    is_skipholidays = new.is_skipholidays, 
				    description = new.description, 
				    createdby = new.createdby, 
				    modifiedby = new.modifiedby, 
				    createddate = new.createddate, 
				    modifieddate = new.modifieddate, 
				    isactive = new.isactive where leavemgmt_id = new.id;
				    END';

/* Trigger structure for table `main_leaverequest_aft_ins` */
$GLOBALS['qry22'] = 'CREATE TRIGGER `main_leaverequest_aft_ins` AFTER INSERT ON `main_leaverequest` 
					FOR EACH ROW BEGIN
					DECLARE user_name,repmanager_name,dept_hr_name,leave_type_name,dept_name,buss_unit_name VARCHAR(200);
					DECLARE dept_id,bunit_id BIGINT(20);
					SELECT userfullname INTO user_name FROM main_users WHERE id = new.user_id;
					SELECT userfullname INTO repmanager_name FROM main_users WHERE id = new.rep_mang_id;
					SELECT userfullname INTO dept_hr_name FROM main_users WHERE id = new.hr_id;
					SELECT leavetype INTO leave_type_name FROM main_employeeleavetypes WHERE id = new.leavetypeid;
					SELECT department_id INTO dept_id FROM main_employees WHERE user_id = new.user_id;
					SELECT b.id,CONCAT(d.deptname," (",d.deptcode,")") ,
					IF(b.unitcode != "000",CONCAT(b.unitcode,"","-"),"") INTO bunit_id,dept_name,buss_unit_name 
					FROM `main_departments` AS `d` LEFT JOIN `main_businessunits` AS `b` ON b.id=d.unitid 
					WHERE (d.isactive = 1 AND d.id = dept_id);
					INSERT INTO main_leaverequest_summary (leave_req_id, user_id, user_name, department_id, 
					department_name, bunit_id,buss_unit_name, reason, approver_comments, leavetypeid, leavetype_name, leaveday, from_date, to_date, leavestatus, 
					rep_mang_id, rep_manager_name, hr_id,hr_name,no_of_days, appliedleavescount, is_sat_holiday, createdby, 
					modifiedby, createddate, modifieddate, isactive)
					VALUES(new.id,new.user_id, user_name, dept_id, dept_name,bunit_id,buss_unit_name,new.reason,new.approver_comments, 
					new.leavetypeid, leave_type_name, new.leaveday, new.from_date, new.to_date, new.leavestatus, 
					new.rep_mang_id, repmanager_name,new.hr_id,dept_hr_name, new.no_of_days, new.appliedleavescount, new.is_sat_holiday, 
					new.createdby, new.modifiedby, new.createddate, new.modifieddate, new.isactive);
					END';

/* Trigger structure for table `main_leaverequest_aft_upd` */
$GLOBALS['qry23'] = 'CREATE TRIGGER `main_leaverequest_aft_upd` AFTER UPDATE ON `main_leaverequest` FOR EACH ROW BEGIN
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
				    END';

/* Trigger structure for table `main_monthslist` */
$GLOBALS['qry24'] = "CREATE TRIGGER `main_monthlist_aftr_upd` AFTER UPDATE ON `main_monthslist` FOR EACH ROW BEGIN
			        declare calmonth_name varchar(200);
			        select month_name into calmonth_name from tbl_months where monthid = new.month_id;
			        UPDATE main_leavemanagement_summary lm SET lm.cal_startmonthname = calmonth_name, lm.modifieddate = utc_timestamp() 
			        WHERE (lm.cal_startmonth = new.month_id AND lm.isactive=1);
			    	END";

/* Trigger structure for table `main_positions` */
$GLOBALS['qry25'] = "CREATE TRIGGER `main_positions_main_requisition_summary` AFTER UPDATE ON `main_positions` FOR EACH ROW BEGIN
					UPDATE main_requisition_summary rs SET rs.position_name = NEW.positionname, rs.modifiedon = utc_timestamp() WHERE (rs.position_id = NEW.id 
					AND rs.position_name != NEW.positionname);
					update main_employees_summary set position_name = new.positionname,modifieddate = utc_timestamp() where position_id = new.id and isactive = 1;
				    END";

/* Trigger structure for table `main_prefix` */
$GLOBALS['qry26'] = "CREATE TRIGGER `main_prefix_aft_upd` AFTER UPDATE ON `main_prefix` FOR EACH ROW BEGIN
				    if old.prefix != new.prefix then 
				    begin 
				      update main_employees_summary set prefix_name = new.prefix,modifieddate = utc_timestamp() where isactive = 1 and prefix_id = new.id;
				    end;
				    end if;
				    END";

/* Trigger structure for table `main_requisition_aft_ins` */
$GLOBALS['qry27'] = "CREATE
					TRIGGER `main_requisition_aft_ins` AFTER INSERT ON `main_requisition` 
					FOR EACH ROW BEGIN
						DECLARE pos_name,rep_name,bunit_name,dept_name,job_name,empt_name,app1_name,app2_name,app3_name,createdbyname VARCHAR(200);
						SELECT positionname INTO pos_name FROM main_positions WHERE id = new.position_id;
						SELECT userfullname INTO rep_name FROM main_users WHERE id = new.reporting_id;
						SELECT userfullname INTO app1_name FROM main_users WHERE id = new.approver1;
						SELECT userfullname INTO createdbyname FROM main_users WHERE id = new.createdby;
						SET app2_name = NULL;
						SET app3_name = NULL;
						IF new.approver2 IS NOT NULL THEN 
						SELECT userfullname INTO app2_name FROM main_users WHERE id = new.approver2;
						END IF;
						
						IF new.approver3 IS NOT NULL THEN 
						SELECT userfullname INTO app3_name FROM main_users WHERE id = new.approver3;
						END IF;
						SELECT unitname INTO bunit_name FROM main_businessunits WHERE id = new.businessunit_id;
						SELECT deptname INTO dept_name FROM main_departments WHERE id = new.department_id;
						SELECT jobtitlename INTO job_name FROM main_jobtitles WHERE id = new.jobtitle;
						SELECT te.employemnt_status INTO empt_name FROM main_employmentstatus em 
					   INNER JOIN tbl_employmentstatus te ON te.id = em.workcodename WHERE em.id = new.emp_type;
						INSERT INTO main_requisition_summary 
						(req_id, requisition_code, onboard_date, position_id, position_name, reporting_id, reporting_manager_name,businessunit_id, businessunit_name, department_id, department_name, jobtitle, jobtitle_name,req_no_positions, selected_members, filled_positions, jobdescription, req_skills, req_qualification,req_exp_years,emp_type, emp_type_name, req_priority, additional_info, req_status, approver1, approver1_name,approver2, approver2_name, approver3, approver3_name, appstatus1, appstatus2, appstatus3,recruiters,client_id, isactive,createdby, modifiedby,createdon, modifiedon,createdby_name
						)
						VALUES
						(new.id, 
						 
						new.requisition_code, 
						new.onboard_date, 
						new.position_id, 
						pos_name, 
						new.reporting_id, 
						rep_name, 
						new.businessunit_id, 
						bunit_name, 
						new.department_id, 
						dept_name, 
						new.jobtitle, 
						job_name, 
						new.req_no_positions, 
						new.selected_members, 
						new.filled_positions, 
						new.jobdescription, 
						new.req_skills, 
						new.req_qualification, 
						new.req_exp_years, 
						new.emp_type, 
						empt_name, 
						new.req_priority, 
						new.additional_info, 
						new.req_status, 
						new.approver1, 
						app1_name, 
						new.approver2, 
						app2_name, 
						new.approver3, 
						app3_name, 
						new.appstatus1, 
						new.appstatus2, 
						new.appstatus3, 
						new.recruiters,
						new.client_id,
						new.isactive, 
						new.createdby, 
						new.modifiedby, 
						new.createdon, 
						new.modifiedon,createdbyname
						);
					END";

/* Trigger structure for table `main_requisition_aft_upd` */
$GLOBALS['qry28'] = "CREATE TRIGGER `main_requisition_aft_upd` AFTER UPDATE ON `main_requisition` 
					FOR EACH ROW BEGIN
					DECLARE pos_name,rep_name,bunit_name,dept_name,job_name,empt_name,app1_name,app2_name,app3_name VARCHAR(200);
					SELECT positionname INTO pos_name FROM main_positions WHERE id = new.position_id;
					SELECT userfullname INTO rep_name FROM main_users WHERE id = new.reporting_id;
					SELECT userfullname INTO app1_name FROM main_users WHERE id = new.approver1;
					SET app2_name = NULL;
					SET app3_name = NULL;
					IF new.approver2 IS NOT NULL THEN 
						SELECT userfullname INTO app2_name FROM main_users WHERE id = new.approver2;
						END IF;
					
					IF new.approver3 IS NOT NULL THEN 
						SELECT userfullname INTO app3_name FROM main_users WHERE id = new.approver3;
						END IF;
					SELECT unitname INTO bunit_name FROM main_businessunits WHERE id = new.businessunit_id;
					SELECT deptname INTO dept_name FROM main_departments WHERE id = new.department_id;
					SELECT jobtitlename INTO job_name FROM main_jobtitles WHERE id = new.jobtitle;
					SELECT te.employemnt_status INTO empt_name FROM main_employmentstatus em 
					   INNER JOIN tbl_employmentstatus te ON te.id = em.workcodename WHERE em.id = new.emp_type;
					UPDATE main_requisition_summary SET
					 requisition_code = new.requisition_code,onboard_date = new.onboard_date, position_id = new.position_id, position_name = pos_name, 
					 reporting_id = new.reporting_id, reporting_manager_name = rep_name , 
					businessunit_id = new.businessunit_id, businessunit_name = bunit_name, 
					department_id = new.department_id, department_name = dept_name, 
					jobtitle = new.jobtitle, jobtitle_name = job_name,	req_no_positions = new.req_no_positions, 
					selected_members = new.selected_members, filled_positions = new.filled_positions, 
					jobdescription = new.jobdescription, req_skills = new.req_skills, req_qualification = new.req_qualification, 
					req_exp_years = new.req_exp_years, 	emp_type = new.emp_type, emp_type_name = empt_name, 
					req_priority = new.req_priority, additional_info = new.additional_info, req_status = new.req_status,
					 approver1 = new.approver1, approver1_name = app1_name,	approver2 = new.approver2, 
					 approver2_name = app2_name, approver3 = new.approver3, approver3_name = app3_name, 
					 appstatus1 = new.appstatus1, appstatus2 = new.appstatus2, appstatus3 = new.appstatus3,recruiters=new.recruiters,client_id=new.client_id, 
					 modifiedby = new.modifiedby, 	modifiedon = new.modifiedon,isactive = new.isactive WHERE req_id = new.id ;
					 
					END";

/* Trigger structure for table `main_roles` */
$GLOBALS['qry29'] = "CREATE TRIGGER `main_roles_aft_upd` AFTER UPDATE ON `main_roles` FOR EACH ROW BEGIN
				    if old.rolename != new.rolename then 
				    begin 
					update main_employees_summary set emprole_name = new.rolename,modifieddate = utc_timestamp() where isactive = 1 and emprole = new.id;
				    end;
				    end if;
				    END";

/* Trigger structure for table `main_states` */
$GLOBALS['qry30'] = "CREATE TRIGGER `main_states_aft_upd` AFTER UPDATE ON `main_states` FOR EACH ROW BEGIN
					if old.state != new.state then 
					begin 
					   update main_interviewrounds_summary set interview_state_name = new.state,modified_date = utc_timestamp() where interview_state_id = new.state_id_org and isactive = 1;
					end;
					end if;
				    END";

/* Trigger structure for table `main_users_aft_upd` */
$GLOBALS['qry31'] = "CREATE TRIGGER `main_users_aft_upd` AFTER UPDATE ON `main_users` 
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
				    
				    END";

/* Trigger structure for table `main_weekdays` */
$GLOBALS['qry32'] = "CREATE TRIGGER `main_weekdays_aftr_upd` AFTER UPDATE ON `main_weekdays` FOR EACH ROW BEGIN
			        declare weekend_name varchar(200);
			        select week_name into weekend_name from tbl_weeks where week_id = new.day_name;
			        UPDATE main_leavemanagement_summary lm SET lm.weekend_startdayname = weekend_name, lm.modifieddate = utc_timestamp() 
			        WHERE (lm.weekend_startday = new.day_name AND lm.isactive=1);
			        UPDATE main_leavemanagement_summary lm SET lm.weekend_enddayname = weekend_name, lm.modifieddate = utc_timestamp() 
			        WHERE (lm.weekend_endday = new.day_name AND lm.isactive=1);
			    	END";

/* Trigger structure for table `main_sd_depts_aft_upd` */
$GLOBALS['qry33'] = "CREATE TRIGGER `main_sd_depts_aft_upd` AFTER UPDATE ON `main_sd_depts` FOR EACH ROW BEGIN
					if old.service_desk_name != new.service_desk_name then 
        			begin 
           				update main_sd_requests_summary set service_desk_name = new.service_desk_name,modifieddate = utc_timestamp() where service_desk_id = new.id;
        			end;
        			end if;
    				END";

/* Trigger structure for table `main_sd_reqtypes_aft_upd` */
$GLOBALS['qry34'] = "CREATE TRIGGER `main_sd_reqtypes_aft_upd` AFTER UPDATE ON `main_sd_reqtypes` FOR EACH ROW BEGIN
					if old.service_request_name != new.service_request_name then 
				        begin 
				           update main_sd_requests_summary set service_request_name = new.service_request_name,modifieddate = utc_timestamp() where service_request_id = new.id;
				        end;
				        end if;
				    END";

/* Trigger structure for table `main_sd_request_aft_ins` */
$GLOBALS['qry35'] = "CREATE
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
					END;";


/* Trigger structure for table `main_sd_request_aft_upd` */
$GLOBALS['qry36'] = "CREATE
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
					END";

$msgarray = array();
if(count($_POST) > 0)
{
	
	if(!empty($_POST))
	{
		$hostname = $_POST['host'];
		$username = $_POST['username'];
		$password = $_POST['password'];
		$dbname = $_POST['dbname'];
		
		if($hostname !='' && $username !='' && $dbname !='' )
		{
                    try{
                        $mysqlPDO = new PDO('mysql:host='.$hostname.';dbname='.$dbname.'',$username, $password);
							if (!$mysqlPDO)
							{
				                            $msgarray['error'] = 'Could not connect to specified database' ;
							}
							else
							{
									$query = "";
									$file_content = file('hrms.sql');
									foreach($file_content as $sql_line){
											if(trim($sql_line) != "" && strpos($sql_line, "--") === false)
											{
												$query .= $sql_line;
												if (substr(rtrim($query), -1) == ';'){
				                                                                $result = $mysqlPDO->query($query);
												$query = "";
												}
											}
										}
									/* We can use mysqli or PDO */	
									$check = 0;
									for($t=1;$t<=36;$t++)
									{
										$mysqlPDO->query($GLOBALS['qry'.$t]);
										$check++;
									}
										if(!$result)
										{
											$msgarray['error'] = 'Could not write tables to specified database' ;
										}else
										{
											if($check == 36)
											{
												$constantresult = writeDBconstants($hostname,$username,$password,$dbname);
												if($constantresult == 'true')
												{
				?>
													 <script type="text/javascript" language="javascript">
									                    window.location= "index.php?s=<?php echo sapp_Global::_encrypt(3);?>";
									                </script>
				<?php 									
				                                                                        
												}else
												{
													$msgarray['error'] = 'Some error occured. '.$constantresult ;
												}
											}
											else
											{
												$msgarray['error'] = 'Some error occured while installing triggers.' ;
											}
										}
					
							}
                    }
                    catch (PDOException $e)
                    {                        
                        $msgarray['error'] = "Some error occured. ".$e->getMessage();
                    }
		}
                else
		{
                    if($hostname == '')
                    {
                        $msgarray['host'] = 'Host cannot be empty';
                    }
                    if($username == '')
                    {
                        $msgarray['username'] = 'User Name cannot be empty';
                    }
                    if($dbname == '')
                    {
                        $msgarray['dbname'] = 'Database Name cannot be empty';
                    }			
		}
	}
}

function writeDBconstants($hostname,$username,$password,$dbname)
{
		$filename = '../public/db_constants.php';
		if(file_exists($filename))
		{
			$db_content = "<?php
	           defined('SENTRIFUGO_HOST') || define('SENTRIFUGO_HOST','".$hostname."');
	           defined('SENTRIFUGO_USERNAME') || define('SENTRIFUGO_USERNAME','".$username."');
	           defined('SENTRIFUGO_PASSWORD') || define('SENTRIFUGO_PASSWORD','".$password."');
	           defined('SENTRIFUGO_DBNAME') || define('SENTRIFUGO_DBNAME','".$dbname."');
	           
	         ?>";
			try{
				$handle = fopen($filename, "w+");
				fwrite($handle,trim($db_content));
				fclose($handle);
				return true;
			}
			catch (Exception $e)
			{
				return $e->getMessage();
			}
		}	
}

?>





<?php 	$prevurl = sapp_Global::_encrypt(1);
?>

<form method="post" action="index.php?s=<?php echo sapp_Global::_encrypt(2);?>" id="step2" name="step2" class="frm_install">
    <h3 class="page_title">Database Settings</h3>
    <div class="content_part">
     
           <span  class="error_info"><?php echo isset($msgarray['error'])?$msgarray['error']:'';?></span>
		    <div class="new-form-ui ">
			  <label class="required">Host<img src="images/help.png" title="IP address of your hosting account as your MySQL hostname." class="tooltip"></label>
				<div >
					<input type="text" maxlength="40" value="<?php if(!$_POST){ echo defined('SENTRIFUGO_HOST')?SENTRIFUGO_HOST:''; } else {echo $_POST['host']; }?>" id="host" name="host">
					<span><?php echo isset($msgarray['host'])?$msgarray['host']:'';?></span>
				</div>
			</div>
			
			<div class="new-form-ui ">
			  <label class="required">User Name<img src="images/help.png" title="Database Server username provided during MySQL account setup." class="tooltip"></label>
				<div>
					<input type="text" maxlength="50" value="<?php if(!$_POST){ echo defined('SENTRIFUGO_USERNAME')?SENTRIFUGO_USERNAME:''; } else {echo $_POST['username']; }?>" id="username" name="username">
					<span><?php echo isset($msgarray['username'])?$msgarray['username']:'';?></span>
				</div>
			</div>
			
			<div class="new-form-ui ">
			  <label >Password<img src="images/help.png" title="Database Server password provided during MySQL account setup." class="tooltip"></label>
				<div>
					<input type="password" maxlength="50" value="<?php if(!$_POST){ echo defined('SENTRIFUGO_PASSWORD')?SENTRIFUGO_PASSWORD:''; } else {echo $_POST['password']; }?>" id="password" name="password">
					<span><?php echo isset($msgarray['password'])?$msgarray['password']:'';?></span>
				</div>
			</div>
			
			<div class="new-form-ui ">
			  <label class="required">Database Name<img src="images/help.png" title="Create a database and provide the name of the database here." class="tooltip"></label>
				<div>
					<input type="text" maxlength="50" value="<?php if(!$_POST){ echo defined('SENTRIFUGO_DBNAME')?SENTRIFUGO_DBNAME:'';} else {echo $_POST['dbname']; }?>" id="dbname" name="dbname">
					<span><?php echo isset($msgarray['dbname'])?$msgarray['dbname']:'';?></span>
				</div>
			</div>						
			
		
		
			<input type="button" value="Confirm" id="submitbutton" name="submitbutton" class="save_button"></div>
		   <button name="previous" id="previous" class="previous_button" type="button" onclick="window.location='index.php?s=<?php echo sapp_Global::_encrypt(1);?>';">Previous</button>
		   	<?php if(defined('SENTRIFUGO_HOST') && defined('SENTRIFUGO_USERNAME') && defined('SENTRIFUGO_PASSWORD') && defined('SENTRIFUGO_DBNAME')){ ?>
		   	<button name="next"  id="next" type="button" onclick="window.location='index.php?s=<?php echo sapp_Global::_encrypt(3);?>';">Next</button>
		   	<?php }?>
		
		

</form>

<script type="text/javascript">
		$(document).ready(function(){
		
			$("#submitbutton").click(function(){
				jConfirm("If the application is already installed, all the data will be lost by clicking on Confirm. Do you want to continue?", "Confirmation", function(r) 
				{
					if(r === false)
					{
						$.unblockUI();
						return false;
					}
					else
					{
						$("#step2").submit();
					}
				});
			});
		
			$(".first_li").addClass('active');
			$(".first_icon").addClass('yes');
			
			<?php if(defined('SENTRIFUGO_HOST') && defined('SENTRIFUGO_USERNAME') && defined('SENTRIFUGO_PASSWORD') && defined('SENTRIFUGO_DBNAME')){ ?>
			$(".second_li").addClass('active');
			$(".second_icon").addClass('yes');
			<?php }else{?>
			$(".second_li").addClass('current');
			$(".second_icon").addClass('loding_icon');
			<?php }?>
			
			<?php if(defined('APPLICATION_NAME') && defined('SUPERADMIN_EMAIL') && constant('SUPERADMIN_EMAIL') !='') { ?>
			$(".third_li").addClass('active');
			$(".third_icon").addClass('yes');
			<?php }?>
			
			<?php if(defined('MAIL_SMTP') && defined('MAIL_USERNAME') && defined('MAIL_PASSWORD') && defined('MAIL_PORT') && defined('MAIL_AUTH') && defined('MAIL_TLS')){ ?>
			$(".fourth_li").addClass('active');
			$(".fourth_icon").addClass('yes');
			<?php }?>

			<?php if(defined('SENTRIFUGO_HOST') && defined('SENTRIFUGO_USERNAME') && defined('SENTRIFUGO_PASSWORD') && defined('SENTRIFUGO_DBNAME') && defined('APPLICATION_NAME') && defined('SUPERADMIN_EMAIL') && defined('MAIL_SMTP') && defined('MAIL_USERNAME') && defined('MAIL_PASSWORD') && defined('MAIL_PORT') && defined('MAIL_AUTH') && defined('MAIL_TLS')){ ?>
			$(".fifth_li").addClass('active');
			$(".fifth_icon").addClass('yes');
			<?php }?>	
		});
</script>

