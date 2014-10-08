DROP TABLE IF EXISTS `main_identitydocuments`;
CREATE TABLE `main_identitydocuments` (                            
                          `id` int(11) unsigned NOT NULL AUTO_INCREMENT,                   
                          `document_name` varchar(500) DEFAULT NULL COMMENT '1-Yes,2-No',  
                          `mandatory` tinyint(1) DEFAULT NULL COMMENT '1-Yes,2-No',        
                          `expiry` tinyint(1) DEFAULT NULL COMMENT '1-Yes,2-No',           
                          `description` varchar(500) DEFAULT NULL,                         
                          `createdby` bigint(20) unsigned DEFAULT NULL,                    
                          `modifiedby` bigint(20) unsigned DEFAULT NULL,                   
                          `createddate` datetime DEFAULT NULL,                             
                          `modifieddate` datetime DEFAULT NULL,                            
                          `isactive` tinyint(1) DEFAULT NULL,                              
                          PRIMARY KEY (`id`)                                               
                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE main_emppersonaldetails
DROP  COLUMN `passport`,
DROP  COLUMN `pancard_number`,
DROP  COLUMN `drivinglicence_number`,
DROP  COLUMN `SSN_number`,
DROP  COLUMN `adhar_number`,
DROP  COLUMN `otheridentity`,
ADD COLUMN `identity_documents` LONGTEXT NULL AFTER `bloodgroup` ;

alter table `main_cronstatus` change `cron_type` `cron_type` enum('General','Employee expiry','Requisition expiry','Approve leave','Inactive users','Emp docs expiry') character set utf8 collate utf8_general_ci default 'General' NULL;

update main_privileges set viewpermission = 'Yes',deletepermission='Yes' where role = 1 And object = 139;
update main_privileges set viewpermission = 'Yes',deletepermission='Yes' where group_id = 1 And object = 139;
update main_privileges set viewpermission = 'Yes' where group_id = 3 And object = 139;