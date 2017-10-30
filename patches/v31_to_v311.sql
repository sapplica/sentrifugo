UPDATE `main_menu` SET `nav_ids`=',1,176,182,' WHERE `id`=182;
UPDATE `main_menu` SET menuName='Employee Leave'  WHERE id=65;        
UPDATE `main_menu` SET menuName='My Leave'  WHERE id=62;        
UPDATE `main_menu` SET menuName='Add Employee Leave'  WHERE id=184;
UPDATE `main_menu` SET menuName='Employee Leave Summary'  WHERE id=45;
UPDATE `main_employees_summary` es SET profileimg = (SELECT profileimg FROM main_users u WHERE u.id=es.user_id);

ALTER TABLE `main_leavemanagement` ADD COLUMN `hr_id` INT(11) AFTER `department_id`;
ALTER TABLE `main_leaverequest` ADD COLUMN `hr_id` INT(11) AFTER `rep_mang_id`;

ALTER TABLE `main_leaverequest_summary` ADD COLUMN `hr_id` INT(11) AFTER `rep_manager_name`;
ALTER TABLE `main_leaverequest_summary` ADD COLUMN `hr_name` VARCHAR(255) AFTER `hr_id`;

ALTER TABLE `main_oncallmanagement` ADD COLUMN `hr_id` INT(11) AFTER `department_id`;
ALTER TABLE `main_oncallrequest` ADD COLUMN `hr_id` INT(11) AFTER `rep_mang_id`;

ALTER TABLE `main_oncallrequest_summary` ADD COLUMN `hr_id` INT(11) AFTER `rep_manager_name`;
ALTER TABLE `main_oncallrequest_summary` ADD COLUMN `hr_name` VARCHAR(255) AFTER `hr_id`;

DELIMITER $$

DROP TRIGGER `main_leaverequest_aft_ins`$$

CREATE
   
    TRIGGER `main_leaverequest_aft_ins` AFTER INSERT ON `main_leaverequest` 
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
END;

DROP TRIGGER `main_oncallrequest_aft_ins`$$

CREATE
   
    TRIGGER `main_oncallrequest_aft_ins` AFTER INSERT ON `main_oncallrequest` 
    FOR EACH ROW BEGIN
DECLARE user_name,repmanager_name,dept_hr_name,oncall_type_name,dept_name,buss_unit_name VARCHAR(200);
DECLARE dept_id,bunit_id BIGINT(20);
SELECT userfullname INTO user_name FROM main_users WHERE id = new.user_id;
SELECT userfullname INTO repmanager_name FROM main_users WHERE id = new.rep_mang_id;
SELECT userfullname INTO dept_hr_name FROM main_users WHERE id = new.hr_id;
SELECT oncalltype INTO oncall_type_name FROM main_employeeoncalltypes WHERE id = new.oncalltypeid;
SELECT department_id INTO dept_id FROM main_employees WHERE user_id = new.user_id;
SELECT b.id,CONCAT(d.deptname," (",d.deptcode,")") ,
IF(b.unitcode != "000",CONCAT(b.unitcode,"","-"),"") INTO bunit_id,dept_name,buss_unit_name 
FROM `main_departments` AS `d` LEFT JOIN `main_businessunits` AS `b` ON b.id=d.unitid 
WHERE (d.isactive = 1 AND d.id = dept_id);
INSERT INTO main_oncallrequest_summary (oncall_req_id, user_id, user_name, department_id, 
department_name, bunit_id,buss_unit_name, reason, approver_comments, oncalltypeid, oncalltype_name, oncallday, from_date, to_date, oncallstatus, 
rep_mang_id, rep_manager_name, hr_id,hr_name,no_of_days, appliedoncallscount, is_sat_holiday, createdby, 
modifiedby, createddate, modifieddate, isactive)
VALUES(new.id,new.user_id, user_name, dept_id, dept_name,bunit_id,buss_unit_name,new.reason,new.approver_comments, 
new.oncalltypeid, oncall_type_name, new.oncallday, new.from_date, new.to_date, new.oncallstatus, 
new.rep_mang_id, repmanager_name,new.hr_id,dept_hr_name, new.no_of_days, new.appliedoncallscount, new.is_sat_holiday, 
new.createdby, new.modifiedby, new.createddate, new.modifieddate, new.isactive);
END;
$$
DELIMITER ;

UPDATE main_patches_version SET isactive=0;
INSERT INTO main_patches_version (version, createddate, modifieddate, isactive) VALUES ("3.1.1", now(), now(),1);