update main_privileges set isactive=0 where object IN (177,178,179,180,181);
update main_menu set isactive=1,parent=3,modulename='exit',nav_ids=",3,177," where id = 177;
update main_menu set isactive=1,parent=177,modulename='exit',url="/exit/exitprocsettings",nav_ids=",3,177,178,",menuOrder=3 where id =178;
update main_menu set isactive=1,parent=177,modulename='exit',url="/exit/exittypes",nav_ids=",3,177,179,",menuOrder=1 where id =179;
update main_menu set isactive=1,parent=177,modulename='exit',url="/exit/exitproc",nav_ids=",3,177,180,",menuOrder=4  where id =180;
update main_menu set isactive=1,parent=177,modulename='exit',url="/exit/allexitproc",nav_ids=",3,177,181,",menuOrder=5 where id =181;
UPDATE `main_dateformat` SET isactive=0 WHERE id=1;

insert into `main_menu`(`id`,`menuName`,`url`,`helpText`,`toolTip`,`iconPath`,`parent`,`menuOrder`,`nav_ids`,`isactive`,`modulename`,`segment_flag`,`org_menuid`,`menufields`,`menuQuery`,`hasJoins`,`modelName`,`functionName`,`defaultOrderBy`)
values 
( 210,'Exit Interview Questions','/exit/configureexitqs',NULL,NULL,NULL,'177','2',',3,177,210,','1','exit',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);

INSERT  INTO `main_privileges`(`role`,`group_id`,`object`,`addpermission`,`editpermission`,`deletepermission`,`viewpermission`,`uploadattachments`,`viewattachments`,`createdby`,`modifiedby`,`createddate`,`modifieddate`,`isactive`)
VALUES
(1,NULL,177,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(NULL,1,177,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(NULL,2,177,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(NULL,3,177,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(NULL,4,177,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(NULL,5,177,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(NULL,6,177,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(2,1,177,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(3,2,177,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(4,3,177,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(5,4,177,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(6,5,177,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(8,6,177,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,NOW(),NOW(),1),

(1,NULL,180,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(NULL,1,180,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(NULL,2,180,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(NULL,3,180,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(NULL,4,180,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(NULL,5,180,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(NULL,6,180,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(2,1,180,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(3,2,180,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(4,3,180,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(5,4,180,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(6,5,180,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(8,6,180,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,NOW(),NOW(),1),

(1,NULL,181,'Yes','Yes','Yes','No','Yes','Yes',1,1,NOW(),NOW(),1),
(NULL,1,181,'Yes','Yes','Yes','No','Yes','Yes',1,1,NOW(),NOW(),1),
(NULL,2,181,'Yes','Yes','Yes','No','Yes','Yes',1,1,NOW(),NOW(),1),
(NULL,3,181,'Yes','Yes','Yes','No','Yes','Yes',1,1,NOW(),NOW(),1),
(NULL,4,181,'Yes','Yes','Yes','No','Yes','Yes',1,1,NOW(),NOW(),1),
(NULL,5,181,'Yes','Yes','Yes','No','Yes','Yes',1,1,NOW(),NOW(),1),
(NULL,6,181,'Yes','Yes','Yes','No','Yes','Yes',1,1,NOW(),NOW(),1),
(2,1,181,'Yes','Yes','Yes','No','Yes','Yes',1,1,NOW(),NOW(),1),
(3,2,181,'Yes','Yes','Yes','No','Yes','Yes',1,1,NOW(),NOW(),1),
(4,3,181,'Yes','Yes','Yes','No','Yes','Yes',1,1,NOW(),NOW(),1),
(5,4,181,'Yes','Yes','Yes','No','Yes','Yes',1,1,NOW(),NOW(),1),
(6,5,181,'Yes','Yes','Yes','No','Yes','Yes',1,1,NOW(),NOW(),1),
(8,6,181,'Yes','Yes','Yes','No','Yes','Yes',1,1,NOW(),NOW(),1),

(1,NULL,178,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(NULL,1,178,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(NULL,3,178,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(2,1,178,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(4,3,178,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,NOW(),NOW(),1),

(1,NULL,179,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(NULL,1,179,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(NULL,3,179,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(2,1,179,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(4,3,179,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,NOW(),NOW(),1),

(1,NULL,210,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(NULL,1,210,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(NULL,3,210,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(2,1,210,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(4,3,210,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,NOW(),NOW(),1);



CREATE TABLE `main_exit_history` (                                                          
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `exit_request_id` int(11) NOT NULL,                                                       
 `description` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,                          
 `createdby` int(11) NOT NULL,                                                             
 `modifiedby` int(11) DEFAULT NULL,                                                        
 `createddate` datetime NOT NULL,                                                          
 `modifieddate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,  
 `isactive` tinyint(4) NOT NULL DEFAULT '1' COMMENT '0-No,1-Yes',                          
 PRIMARY KEY (`id`)                                                                        
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


CREATE TABLE `main_exit_process` (                                                                   
`id` int(11) NOT NULL AUTO_INCREMENT,                                                              
`employee_id` int(11) NOT NULL,                                                                    
`exit_type_id` int(11) NOT NULL,                                                                   
`exit_settings_id` int(11) NOT NULL,                                                               
`employee_comments` varchar(1000) COLLATE utf8_unicode_ci DEFAULT NULL,                            
`overall_status` enum('Pending','Approved','Rejected') COLLATE utf8_unicode_ci DEFAULT 'Pending',  
`overall_comments` varchar(1000) COLLATE utf8_unicode_ci DEFAULT NULL,                             
`l1_status` enum('Pending','Approved','Rejected') COLLATE utf8_unicode_ci DEFAULT NULL,            
`l1_comments` varchar(1000) COLLATE utf8_unicode_ci DEFAULT NULL,                                  
`l2_status` enum('Pending','Approved','Rejected') COLLATE utf8_unicode_ci DEFAULT NULL,            
`l2_comments` varchar(1000) COLLATE utf8_unicode_ci DEFAULT NULL,                                  
`hr_manager_status` enum('Pending','Approved','Rejected') COLLATE utf8_unicode_ci DEFAULT NULL,    
`hr_manager_comments` varchar(1000) COLLATE utf8_unicode_ci DEFAULT NULL,                          
`sys_admin_status` enum('Pending','Approved','Rejected') COLLATE utf8_unicode_ci DEFAULT NULL,     
`sys_admin_comments` varchar(1000) COLLATE utf8_unicode_ci DEFAULT NULL,                           
`gen_admin_status` enum('Pending','Approved','Rejected') COLLATE utf8_unicode_ci DEFAULT NULL,     
`gen_admin_comments` varchar(1000) COLLATE utf8_unicode_ci DEFAULT NULL,                           
`fin_admin_status` enum('Pending','Approved','Rejected') COLLATE utf8_unicode_ci DEFAULT NULL,     
`fin_admin_comments` varchar(1000) COLLATE utf8_unicode_ci DEFAULT NULL,                           
`relieving_date` date NOT NULL,                                                                    
`feedback_completed` tinyint(1) DEFAULT '0' COMMENT '0=Not completed,1=Completed',                 
`createdby` int(11) NOT NULL,                                                                      
`modifiedby` int(11) DEFAULT NULL,                                                                 
`createddate` datetime NOT NULL,                                                                   
`modifieddate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,           
PRIMARY KEY (`id`)                                                                                 
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


CREATE TABLE `main_exit_questions` (                                                        
`id` int(11) NOT NULL AUTO_INCREMENT,                                                     
`exit_type_id` int(11) NOT NULL,                                                          
`question` varchar(500) COLLATE utf8_unicode_ci NOT NULL,                                 
`description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,                          
`createdby` int(11) NOT NULL,                                                             
`modifiedby` int(11) DEFAULT NULL,                                                        
`createddate` datetime NOT NULL,                                                          
`modifieddate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,  
`isactive` tinyint(4) NOT NULL DEFAULT '1' COMMENT '0-No,1-Yes',                          
`isused` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0-No,1-Yes',                            
PRIMARY KEY (`id`)                                                                        
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


CREATE TABLE `main_exit_questions_response` (                                                                    
`id` int(11) NOT NULL AUTO_INCREMENT,                                  
`user_id` int(11) NOT NULL,                                                        
`exit_initiation_id` int(11) NOT NULL,                                 
`hr_qs` text COLLATE utf8_unicode_ci,                                  
`employee_response` text COLLATE utf8_unicode_ci COMMENT '{hr_qs_id_1:emp_comments,hr_qs_id_2:emp_comments}',  
`createdby` int(11) NOT NULL,                                                  
`modifiedby` int(11) DEFAULT NULL,                                                                             
`createddate` datetime NOT NULL,                                                                               
`modifieddate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,                       
`isactive` tinyint(4) NOT NULL DEFAULT '1' COMMENT '0-No,1-Yes',                                               
PRIMARY KEY (`id`)                                                                                             
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `main_exit_settings` (                                                         
`id` int(11) NOT NULL AUTO_INCREMENT,                                                     
`businessunit_id` int(11) NOT NULL,                                                       
`department_id` int(11) NOT NULL,                                                         
`l2_manager` int(11) DEFAULT NULL,                                                        
`hr_manager` int(11) DEFAULT NULL,                                                        
`sys_admin` int(11) DEFAULT NULL,                                                         
`general_admin` int(11) DEFAULT NULL,                                                     
`finance_manager` int(11) DEFAULT NULL,                                                   
`notice_period` int(11) DEFAULT NULL,                                                     
`createdby` int(11) NOT NULL,                                                             
`modifiedby` int(11) DEFAULT NULL,                                                        
`createddate` datetime NOT NULL,                                                          
`modifieddate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,  
`isactive` tinyint(4) NOT NULL DEFAULT '1' COMMENT '0-No,1-Yes',                          
PRIMARY KEY (`id`)                                                                        
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


CREATE TABLE `main_exit_types`(
`id` int(11) NOT NULL AUTO_INCREMENT,                                                     
`exit_type` varchar(50) COLLATE utf8_unicode_ci NOT NULL,                                 
`description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,                          
`createdby` int(11) NOT NULL,                                                             
`modifiedby` int(11) DEFAULT NULL,                                                        
`createddate` datetime NOT NULL,                                                          
`modifieddate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,  
`isactive` tinyint(4) NOT NULL DEFAULT '1' COMMENT '0-No,1-Yes',                          
`isused` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0-No,1-Yes',                            
PRIMARY KEY (`id`)                                                                        
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

 


