/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

set global sql_mode = "STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION";

/*Table structure for table `assets` */

DROP TABLE IF EXISTS `assets`;

CREATE TABLE `assets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category` int(11) DEFAULT NULL,
  `sub_category` int(11) DEFAULT NULL,
  `company_asset_code` int(11) DEFAULT NULL,
  `name` varchar(50) NOT NULL,
  `location` varchar(15) NOT NULL,
  `allocated_to` int(11) DEFAULT NULL,
  `responsible_technician` int(11) DEFAULT NULL,
  `vendor` int(11) DEFAULT NULL,
  `asset_classification` varchar(50) NOT NULL,
  `purchase_date` date DEFAULT NULL,
  `invoice_number` varchar(50) DEFAULT NULL,
  `manufacturer` varchar(50) NOT NULL,
  `key_number` varchar(11) NOT NULL,
  `warenty_status` enum('Yes','No') NOT NULL,
  `warenty_end_date` date DEFAULT NULL,
  `is_working` enum('No','Yes') NOT NULL,
  `notes` text,
  `image` text,
  `imagencrpname` text NOT NULL,
  `qr_image` text NOT NULL,
  `isactive` tinyint(4) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `modified` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=63 DEFAULT CHARSET=utf8;

/*Data for the table `assets` */

/*Table structure for table `assets_categories` */

DROP TABLE IF EXISTS `assets_categories`;

CREATE TABLE `assets_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET latin1 NOT NULL,
  `parent` int(11) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified_by` varchar(11) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `modified` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=198 DEFAULT CHARSET=utf8;

/*Data for the table `assets_categories` */

/*Table structure for table `assets_history` */

DROP TABLE IF EXISTS `assets_history`;

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
) ENGINE=InnoDB AUTO_INCREMENT=233 DEFAULT CHARSET=utf8;

/*Data for the table `assets_history` */

/*Table structure for table `expense_advacne_summary` */

DROP TABLE IF EXISTS `expense_advacne_summary`;

CREATE TABLE `expense_advacne_summary` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) DEFAULT NULL,
  `total` float(10,2) DEFAULT NULL,
  `utilized` float(10,2) DEFAULT NULL,
  `returned` float(10,2) DEFAULT NULL,
  `balance` float(10,2) DEFAULT NULL,
  `createdby` int(11) DEFAULT NULL,
  `modifiedby` int(11) DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `isactive` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQUEEMP` (`employee_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `expense_advacne_summary` */

/*Table structure for table `expense_advance` */

DROP TABLE IF EXISTS `expense_advance`;

CREATE TABLE `expense_advance` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` enum('advance','return') DEFAULT 'advance',
  `from_id` int(11) DEFAULT NULL,
  `to_id` int(11) DEFAULT NULL,
  `payment_ref_number` varchar(200) DEFAULT NULL,
  `payment_mode_id` int(11) DEFAULT NULL,
  `project_id` int(11) DEFAULT NULL,
  `currency_id` int(11) DEFAULT NULL,
  `amount` float(10,2) DEFAULT NULL,
  `application_currency_id` int(11) DEFAULT NULL,
  `application_amount` float(10,2) DEFAULT NULL,
  `advance_conversion_rate` float(10,2) DEFAULT NULL,
  `description` text,
  `createdby` int(11) DEFAULT NULL,
  `modifiedby` int(11) DEFAULT NULL,
  `createddate` datetime NOT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `isactive` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `expense_advance` */

/*Table structure for table `expense_categories` */

DROP TABLE IF EXISTS `expense_categories`;

CREATE TABLE `expense_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `expense_category_name` varchar(100) DEFAULT NULL,
  `unit_price` varchar(50) DEFAULT NULL,
  `unit_name` varchar(50) DEFAULT NULL,
  `createdby` int(11) DEFAULT NULL,
  `modifiedby` int(11) DEFAULT NULL,
  `created_date` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `isactive` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `expense_categories` */

/*Table structure for table `expense_forward` */

DROP TABLE IF EXISTS `expense_forward`;

CREATE TABLE `expense_forward` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `expense_id` int(11) DEFAULT NULL,
  `trip_id` int(11) DEFAULT NULL,
  `from_id` int(11) DEFAULT NULL,
  `to_id` int(11) DEFAULT NULL,
  `createdby` int(11) DEFAULT NULL,
  `modifiedby` int(11) DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `isactive` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `expense_forward` */

/*Table structure for table `expense_history` */

DROP TABLE IF EXISTS `expense_history`;

CREATE TABLE `expense_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `expense_id` int(11) DEFAULT NULL,
  `trip_id` int(11) DEFAULT NULL,
  `history` varchar(500) DEFAULT NULL,
  `createdby` int(11) DEFAULT NULL,
  `modifiedby` int(11) DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `isactive` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `expense_history` */

/*Table structure for table `expense_notifications` */

DROP TABLE IF EXISTS `expense_notifications`;

CREATE TABLE `expense_notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `expense_id` int(11) DEFAULT NULL,
  `trip_id` int(11) DEFAULT NULL,
  `notification` varchar(500) DEFAULT NULL,
  `link` varchar(200) DEFAULT NULL,
  `createdby` int(11) DEFAULT NULL,
  `modifiedby` int(11) DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `isactive` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `expense_notifications` */

/*Table structure for table `expense_payment_methods` */

DROP TABLE IF EXISTS `expense_payment_methods`;

CREATE TABLE `expense_payment_methods` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `payment_method_name` varchar(100) DEFAULT NULL,
  `createdby` int(11) DEFAULT NULL,
  `modifiedby` int(11) DEFAULT NULL,
  `created_date` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `isactive` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `expense_payment_methods` */

/*Table structure for table `expense_receipts` */

DROP TABLE IF EXISTS `expense_receipts`;

CREATE TABLE `expense_receipts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `expense_id` int(11) DEFAULT NULL,
  `trip_id` int(11) DEFAULT NULL,
  `receipt_name` varchar(100) DEFAULT NULL COMMENT 'orginal file name',
  `receipt_filename` varchar(100) DEFAULT NULL COMMENT 'auto generated file name',
  `receipt_file_type` varchar(5) DEFAULT NULL,
  `createdby` int(11) DEFAULT NULL,
  `modifiedby` int(11) DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `isactive` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `expense_receipts` */

/*Table structure for table `expense_return_advance` */

DROP TABLE IF EXISTS `expense_return_advance`;

CREATE TABLE `expense_return_advance` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `from_id` int(11) DEFAULT NULL,
  `to_id` int(11) DEFAULT NULL,
  `currency_id` int(11) DEFAULT NULL,
  `returned_amount` float(10,2) DEFAULT NULL,
  `createdby` int(11) DEFAULT NULL,
  `modifiedby` int(11) DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `isactive` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `expense_return_advance` */

/*Table structure for table `expense_trip_history` */

DROP TABLE IF EXISTS `expense_trip_history`;

CREATE TABLE `expense_trip_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `trip_id` int(11) DEFAULT NULL,
  `expense_id` int(11) DEFAULT NULL,
  `history` varchar(500) DEFAULT NULL,
  `createdby` int(11) DEFAULT NULL,
  `modifiedby` int(11) DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `isactive` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `expense_trip_history` */

/*Table structure for table `expense_trips` */

DROP TABLE IF EXISTS `expense_trips`;

CREATE TABLE `expense_trips` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `manager_id` int(11) DEFAULT NULL,
  `trip_name` varchar(100) DEFAULT NULL,
  `from_date` date DEFAULT NULL,
  `to_date` date DEFAULT NULL,
  `description` text,
  `status` enum('NS','S','A','R') DEFAULT 'NS' COMMENT 'NS-Notsubmitted,S-submitted,R-Rejected,A-Approved',
  `rejected_note` text,
  `createdby` int(11) DEFAULT NULL,
  `modifiedby` int(11) DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `isactive` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `expense_trips` */

/*Table structure for table `expenses` */

DROP TABLE IF EXISTS `expenses`;

CREATE TABLE `expenses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `expense_name` varchar(100) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `project_id` int(11) DEFAULT NULL,
  `client_id` int(11) DEFAULT NULL,
  `trip_id` int(11) DEFAULT NULL,
  `manager_id` int(11) DEFAULT NULL,
  `expense_date` date DEFAULT NULL,
  `expense_currency_id` int(11) DEFAULT NULL,
  `expense_amount` float(10,2) DEFAULT NULL,
  `expense_conversion_rate` float(5,2) DEFAULT NULL,
  `application_currency_id` int(11) DEFAULT NULL,
  `application_amount` float(10,2) DEFAULT NULL,
  `advance_amount` float(10,2) DEFAULT NULL,
  `is_reimbursable` tinyint(1) DEFAULT NULL,
  `is_from_advance` tinyint(1) DEFAULT '0',
  `expense_payment_id` int(11) DEFAULT NULL,
  `expense_payment_ref_no` varchar(200) DEFAULT NULL,
  `description` text,
  `status` enum('saved','submitted','approved','rejected') DEFAULT 'saved',
  `createdby` int(11) DEFAULT NULL,
  `modifiedby` int(11) DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `isactive` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `expenses` */

/*Table structure for table `main_accountclasstype` */

DROP TABLE IF EXISTS `main_accountclasstype`;

CREATE TABLE `main_accountclasstype` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `accountclasstype` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `createdby` int(11) unsigned DEFAULT NULL,
  `modifiedby` int(11) unsigned DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `isactive` tinyint(1) unsigned DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `main_accountclasstype` */

/*Table structure for table `main_allottedleaveslog` */

DROP TABLE IF EXISTS `main_allottedleaveslog`;

CREATE TABLE `main_allottedleaveslog` (
  `id` bigint(11) unsigned NOT NULL AUTO_INCREMENT,
  `userid` bigint(11) unsigned DEFAULT NULL,
  `assignedleaves` int(5) DEFAULT NULL,
  `totalleaves` int(5) DEFAULT NULL,
  `year` int(5) DEFAULT NULL,
  `createdby` bigint(11) unsigned DEFAULT NULL,
  `modifiedby` bigint(11) unsigned DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `isactive` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `main_allottedleaveslog` */

/*Table structure for table `main_announcements` */

DROP TABLE IF EXISTS `main_announcements`;

CREATE TABLE `main_announcements` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `businessunit_id` text,
  `department_id` text,
  `title` varchar(255) DEFAULT NULL,
  `description` text,
  `attachments` text,
  `status` tinyint(1) DEFAULT NULL COMMENT '1-Save as draft, 2-Posted',
  `isactive` tinyint(1) DEFAULT NULL,
  `createdby` bigint(20) DEFAULT NULL,
  `createdby_role` bigint(20) DEFAULT NULL,
  `createdby_group` bigint(20) DEFAULT NULL,
  `modifiedby` bigint(20) DEFAULT NULL,
  `modifiedby_role` bigint(20) DEFAULT NULL,
  `modifiedby_group` bigint(20) DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `main_announcements` */

/*Table structure for table `main_assignmententryreasoncode` */

DROP TABLE IF EXISTS `main_assignmententryreasoncode`;

CREATE TABLE `main_assignmententryreasoncode` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `assignmententryreasoncode` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `createdby` int(11) unsigned DEFAULT NULL,
  `modifiedby` int(11) unsigned DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `isactive` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `main_assignmententryreasoncode` */

/*Table structure for table `main_attendancestatuscode` */

DROP TABLE IF EXISTS `main_attendancestatuscode`;

CREATE TABLE `main_attendancestatuscode` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `attendancestatuscode` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `createdby` int(11) unsigned DEFAULT NULL,
  `modifiedby` int(11) unsigned DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `isactive` tinyint(1) unsigned DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `main_attendancestatuscode` */

/*Table structure for table `main_bankaccounttype` */

DROP TABLE IF EXISTS `main_bankaccounttype`;

CREATE TABLE `main_bankaccounttype` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `bankaccounttype` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `createdby` int(11) unsigned DEFAULT NULL,
  `modifiedby` int(11) unsigned DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `isactive` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `main_bankaccounttype` */

/*Table structure for table `main_bgagencylist` */

DROP TABLE IF EXISTS `main_bgagencylist`;

CREATE TABLE `main_bgagencylist` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned DEFAULT NULL,
  `agencyname` varchar(255) NOT NULL,
  `primaryphone` varchar(100) NOT NULL,
  `secondaryphone` varchar(100) DEFAULT NULL,
  `address` text,
  `bg_checktype` varchar(255) NOT NULL,
  `website_url` varchar(255) DEFAULT NULL,
  `flag` tinyint(1) DEFAULT '1',
  `createdby` int(11) unsigned DEFAULT NULL,
  `modifiedby` int(11) unsigned DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `isactive` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `main_bgagencylist` */

/*Table structure for table `main_bgcheckcomments` */

DROP TABLE IF EXISTS `main_bgcheckcomments`;

CREATE TABLE `main_bgcheckcomments` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `bgdet_id` int(11) unsigned DEFAULT NULL,
  `comment` text,
  `from_id` int(11) unsigned DEFAULT NULL,
  `to_id` int(11) unsigned DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `isactive` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `main_bgcheckcomments` */

/*Table structure for table `main_bgcheckdetails` */

DROP TABLE IF EXISTS `main_bgcheckdetails`;

CREATE TABLE `main_bgcheckdetails` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `specimen_id` int(11) unsigned DEFAULT NULL,
  `flag` tinyint(1) DEFAULT '1' COMMENT '1 - employee, 2- candidate',
  `process_status` enum('In process','On hold','Complete') DEFAULT 'In process',
  `bgagency_id` int(11) unsigned DEFAULT NULL,
  `bgcheck_type` varchar(100) DEFAULT NULL,
  `bgagency_pocid` int(11) unsigned DEFAULT NULL,
  `bgcheck_status` enum('Yet to start','In process','On hold','Complete') DEFAULT 'In process',
  `explanation` text,
  `feedback_file` varchar(50) DEFAULT NULL,
  `feedback_deletedby` int(11) DEFAULT NULL,
  `createdby` int(11) unsigned DEFAULT NULL,
  `modifiedby` int(11) unsigned DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `isactive` tinyint(1) DEFAULT '1' COMMENT '0 - Process deleted, 1 - Active, 2 - Agency deleted',
  `recentlycommentedby` int(11) unsigned DEFAULT NULL,
  `recentlycommenteddate` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `main_bgcheckdetails` */

/*Table structure for table `main_bgchecks_summary` */

DROP TABLE IF EXISTS `main_bgchecks_summary`;

CREATE TABLE `main_bgchecks_summary` (
  `id` bigint(11) unsigned NOT NULL AUTO_INCREMENT,
  `detail_id` bigint(11) unsigned DEFAULT NULL,
  `specimen_name` varchar(200) DEFAULT NULL,
  `specimen_id` bigint(11) unsigned DEFAULT NULL,
  `specimen_flag` tinyint(1) DEFAULT '1',
  `specimen_flag_name` enum('Employee','Candidate') DEFAULT 'Employee',
  `employee_id` varchar(200) DEFAULT NULL,
  `screeningtypeid` bigint(11) unsigned DEFAULT NULL,
  `screeningtype_name` varchar(200) DEFAULT NULL,
  `agencyid` bigint(11) unsigned DEFAULT NULL,
  `agencyname` varchar(200) DEFAULT NULL,
  `process_status` enum('In process','On hold','Complete') DEFAULT 'In process',
  `month_name` varchar(200) DEFAULT NULL,
  `year_year` varchar(200) DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `createdby` bigint(11) unsigned DEFAULT NULL,
  `createdname` varchar(200) DEFAULT NULL,
  `modifiedby` bigint(11) unsigned DEFAULT NULL,
  `modifiedname` varchar(200) DEFAULT NULL,
  `isactive` tinyint(1) DEFAULT '1',
  `isactive_text` varchar(50) DEFAULT 'Active',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `main_bgchecks_summary` */

/*Table structure for table `main_bgchecktype` */

DROP TABLE IF EXISTS `main_bgchecktype`;

CREATE TABLE `main_bgchecktype` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(150) NOT NULL,
  `description` text,
  `createdby` int(11) unsigned DEFAULT NULL,
  `modifiedby` int(11) unsigned DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `isactive` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `main_bgchecktype` */

/*Table structure for table `main_bgpocdetails` */

DROP TABLE IF EXISTS `main_bgpocdetails`;

CREATE TABLE `main_bgpocdetails` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `bg_agencyid` int(11) DEFAULT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `contact_no` varchar(100) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `location` varchar(200) DEFAULT NULL,
  `city` int(11) unsigned DEFAULT NULL,
  `state` int(11) unsigned DEFAULT NULL,
  `country` int(11) unsigned DEFAULT NULL,
  `contact_type` tinyint(1) DEFAULT '1' COMMENT '1-primary, 2- secondary',
  `createdby` int(11) unsigned DEFAULT NULL,
  `modifiedby` int(11) unsigned DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `isactive` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `main_bgpocdetails` */

/*Table structure for table `main_businessunits` */

DROP TABLE IF EXISTS `main_businessunits`;

CREATE TABLE `main_businessunits` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `unitname` varchar(255) NOT NULL,
  `unitcode` varchar(50) DEFAULT NULL,
  `description` text,
  `startdate` date DEFAULT NULL,
  `country` int(11) unsigned DEFAULT NULL,
  `state` int(11) unsigned DEFAULT NULL,
  `city` int(11) unsigned DEFAULT NULL,
  `address1` text,
  `address2` text,
  `address3` text,
  `timezone` int(11) DEFAULT NULL,
  `unithead` varchar(255) DEFAULT NULL,
  `service_desk_flag` tinyint(1) unsigned DEFAULT '1' COMMENT '1=buwise,0=deptwise',
  `createdby` int(11) unsigned DEFAULT NULL,
  `modifiedby` int(11) unsigned DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `isactive` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `main_businessunits` */

insert  into `main_businessunits`(`id`,`unitname`,`unitcode`,`description`,`startdate`,`country`,`state`,`city`,`address1`,`address2`,`address3`,`timezone`,`unithead`,`service_desk_flag`,`createdby`,`modifiedby`,`createddate`,`modifieddate`,`isactive`) values (0,'No Business Unit','000',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,1,1,'2013-12-04 18:51:19','2013-12-04 18:51:19',1);

/*Table structure for table `main_candidatedetails` */

DROP TABLE IF EXISTS `main_candidatedetails`;

CREATE TABLE `main_candidatedetails` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `requisition_id` int(11) DEFAULT NULL,
  `candidate_firstname` varchar(50) DEFAULT NULL,
  `candidate_lastname` varchar(50) DEFAULT NULL,
  `candidate_name` varchar(100) NOT NULL,
  `emailid` varchar(70) DEFAULT NULL,
  `contact_number` varchar(15) DEFAULT NULL,
  `profileimg` varchar(100) DEFAULT NULL,
  `cand_resume` varchar(100) DEFAULT NULL COMMENT 'resume file location',
  `cand_resume_deletedby` int(11) DEFAULT NULL,
  `qualification` varchar(100) NOT NULL,
  `experience` float DEFAULT NULL,
  `skillset` text,
  `education_summary` text,
  `summary` text COMMENT 'instead of resume',
  `cand_status` enum('Shortlisted','Selected','Rejected','On hold','Disqualified','Scheduled','Not Scheduled','Recruited','Requisition Closed/Completed') NOT NULL,
  `backgroundchk_status` enum('In process','Completed','Not Applicable','Yet to start','On hold') DEFAULT 'Yet to start',
  `cand_location` varchar(150) DEFAULT NULL,
  `city` int(11) unsigned DEFAULT NULL,
  `state` int(11) unsigned DEFAULT NULL,
  `country` int(11) unsigned DEFAULT NULL,
  `pincode` varchar(15) DEFAULT NULL,
  `source` enum('Vendor','Website','Referal') DEFAULT NULL,
  `source_val` varchar(150) DEFAULT NULL,
  `createdby` int(11) unsigned DEFAULT NULL,
  `modifiedby` int(11) unsigned DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `isactive` tinyint(1) unsigned DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `main_candidatedetails` */

/*Table structure for table `main_candworkdetails` */

DROP TABLE IF EXISTS `main_candworkdetails`;

CREATE TABLE `main_candworkdetails` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `cand_id` bigint(20) unsigned DEFAULT NULL,
  `company_name` varchar(150) DEFAULT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `company_address` varchar(500) DEFAULT NULL,
  `company_website` varchar(100) DEFAULT NULL,
  `cand_designation` varchar(60) DEFAULT NULL,
  `cand_fromdate` date DEFAULT NULL,
  `cand_todate` date DEFAULT NULL,
  `createdby` int(11) unsigned DEFAULT NULL,
  `modifiedby` int(11) unsigned DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `isactive` tinyint(1) unsigned DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `NewIndex1` (`cand_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `main_candworkdetails` */

/*Table structure for table `main_cities` */

DROP TABLE IF EXISTS `main_cities`;

CREATE TABLE `main_cities` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `countryid` int(11) unsigned DEFAULT NULL,
  `state` int(11) unsigned DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `city_org_id` int(11) unsigned DEFAULT NULL,
  `createdby` int(11) DEFAULT NULL,
  `modifiedby` int(11) DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `isactive` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `state_city` (`state`,`city_org_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Data for the table `main_cities` */

/*Table structure for table `main_competencylevel` */

DROP TABLE IF EXISTS `main_competencylevel`;

CREATE TABLE `main_competencylevel` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `competencylevel` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `createdby` int(11) unsigned DEFAULT NULL,
  `modifiedby` int(11) unsigned DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `isactive` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `main_competencylevel` */

/*Table structure for table `main_countries` */

DROP TABLE IF EXISTS `main_countries`;

CREATE TABLE `main_countries` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `country` varchar(255) NOT NULL,
  `countrycode` varchar(255) DEFAULT NULL,
  `citizenship` varchar(255) DEFAULT NULL,
  `createdby` int(11) unsigned DEFAULT NULL,
  `modifiedby` int(11) unsigned DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `isactive` tinyint(1) unsigned DEFAULT '1',
  `country_id_org` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `main_countries` */

/*Table structure for table `main_cronstatus` */

DROP TABLE IF EXISTS `main_cronstatus`;

CREATE TABLE `main_cronstatus` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `cron_type` enum('General','Employee expiry','Requisition expiry','Approve leave','Inactive users','Emp docs expiry') DEFAULT 'General',
  `cron_status` int(11) DEFAULT NULL,
  `started_at` datetime DEFAULT NULL,
  `completed_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `main_cronstatus` */

/*Table structure for table `main_currency` */

DROP TABLE IF EXISTS `main_currency`;

CREATE TABLE `main_currency` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `currencyname` varchar(255) NOT NULL,
  `currencycode` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `createdby` int(11) unsigned DEFAULT NULL,
  `modifiedby` int(11) unsigned DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `isactive` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

/*Data for the table `main_currency` */

insert  into `main_currency`(`id`,`currencyname`,`currencycode`,`description`,`createdby`,`modifiedby`,`createddate`,`modifieddate`,`isactive`) values (1,'US Dollar','USD','',1,1,'2016-11-02 11:13:48','2016-11-02 11:13:48',1),(2,'European Euro','EUR','',1,1,'2016-11-02 11:13:48','2016-11-02 11:13:48',1),(3,'Pound Sterling','GBP','',1,1,'2016-11-02 11:13:48','2016-11-02 11:13:48',1),(4,'Indian Rupee','INR','',1,1,'2016-11-02 11:13:48','2016-11-02 11:13:48',1);

/*Table structure for table `main_currencyconverter` */

DROP TABLE IF EXISTS `main_currencyconverter`;

CREATE TABLE `main_currencyconverter` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `basecurrency` int(11) DEFAULT NULL,
  `targetcurrency` int(11) DEFAULT NULL,
  `basecurrtext` varchar(255) DEFAULT NULL,
  `targetcurrtext` varchar(255) DEFAULT NULL,
  `exchangerate` varchar(255) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `createdby` int(11) unsigned DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `modifiedby` int(11) unsigned DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `isactive` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `main_currencyconverter` */

/*Table structure for table `main_dateformat` */

DROP TABLE IF EXISTS `main_dateformat`;

CREATE TABLE `main_dateformat` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `mysql_dateformat` varchar(50) DEFAULT NULL COMMENT 'format for mysql',
  `js_dateformat` varchar(50) DEFAULT NULL COMMENT 'format for javascript',
  `dateformat` varchar(50) NOT NULL COMMENT 'for php',
  `example` varchar(60) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `createdby` int(11) unsigned DEFAULT NULL,
  `modifiedby` int(11) unsigned DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `isactive` tinyint(1) unsigned DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=latin1;

/*Data for the table `main_dateformat` */

insert  into `main_dateformat`(`id`,`mysql_dateformat`,`js_dateformat`,`dateformat`,`example`,`description`,`createdby`,`modifiedby`,`createddate`,`modifieddate`,`isactive`) values (1,'%m/%d/%y','mm/dd/y','m/d/y','10/01/13','American month, day and year(2 digits)',1,1,'2013-10-04 13:18:25','2013-10-05 11:29:37',0),(2,'%m/%d/%Y','mm/dd/yy','m/d/Y','01/01/2013','American month, day and year',1,1,'2013-10-04 13:22:25','2013-10-04 13:22:25',1),(3,'%Y/%m/%d','yy/mm/dd','Y/m/d','2013/10/01','Four digit year, month and day with slashes',1,1,'2013-10-04 13:23:46','2013-10-04 13:23:46',1),(4,'%Y-%m-%d','yy-mm-dd','Y-m-d','2013-03-31','Year, month and day with dashes',1,1,'2013-10-04 13:26:34','2013-10-04 13:26:34',1),(5,'%d.%m.%Y','dd.mm.yy','d.m.Y','10.01.2013','Day, month and four digit year with dots',1,1,'2013-10-04 13:30:29','2013-10-04 13:30:29',1),(6,'%d-%m-%Y','dd-mm-yy','d-m-Y','10-01-2013','Day, month and four digit year with dashes',1,1,'2013-10-04 13:30:55','2013-10-04 13:30:55',1),(9,'%d %M %Y','dd MM yy','d F Y','04 October 2013','Day, textual month and year',1,1,'2013-10-04 13:36:40','2013-10-04 13:37:35',1),(10,'%M %D, %Y','MM dd, yy','F jS, Y','July 1st, 2008','Textual month, day and year',1,1,'2013-10-04 13:41:36','2013-10-04 13:41:36',1),(11,'%b-%d-%Y','M-dd-yy','M-d-Y','Apr-17-2012','Month abbreviation, day and year',1,1,'2013-10-04 13:43:16','2013-10-04 13:43:16',1),(12,'%Y-%b-%d','yy-M-dd','Y-M-d','2013-Dec-22','Year, month abbreviation and day',1,1,'2013-10-04 13:44:27','2013-10-04 13:44:27',1);

/*Table structure for table `main_departments` */

DROP TABLE IF EXISTS `main_departments`;

CREATE TABLE `main_departments` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `deptname` varchar(150) NOT NULL,
  `deptcode` varchar(20) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `startdate` date DEFAULT NULL,
  `country` int(11) unsigned DEFAULT NULL,
  `state` int(11) unsigned DEFAULT NULL,
  `city` int(11) unsigned DEFAULT NULL,
  `address1` text NOT NULL,
  `address2` text,
  `address3` text,
  `timezone` int(11) DEFAULT NULL,
  `depthead` int(11) unsigned DEFAULT NULL,
  `unitid` int(11) DEFAULT NULL,
  `createdby` int(11) unsigned DEFAULT NULL,
  `modifiedby` int(11) unsigned DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `isactive` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `main_departments` */

/*Table structure for table `main_disciplinary_history` */

DROP TABLE IF EXISTS `main_disciplinary_history`;

CREATE TABLE `main_disciplinary_history` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `incident_id` bigint(20) unsigned DEFAULT NULL,
  `description` varchar(300) DEFAULT NULL,
  `action_emp_id` bigint(20) unsigned DEFAULT NULL,
  `createdby` bigint(20) unsigned DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `main_disciplinary_history` */

/*Table structure for table `main_disciplinary_incident` */

DROP TABLE IF EXISTS `main_disciplinary_incident`;

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

/*Data for the table `main_disciplinary_incident` */

/*Table structure for table `main_disciplinary_violation_types` */

DROP TABLE IF EXISTS `main_disciplinary_violation_types`;

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

/*Data for the table `main_disciplinary_violation_types` */

/*Table structure for table `main_educationlevelcode` */

DROP TABLE IF EXISTS `main_educationlevelcode`;

CREATE TABLE `main_educationlevelcode` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `educationlevelcode` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `createdby` int(11) unsigned DEFAULT NULL,
  `modifiedby` int(11) unsigned DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `isactive` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `main_educationlevelcode` */

/*Table structure for table `main_eeoccategory` */

DROP TABLE IF EXISTS `main_eeoccategory`;

CREATE TABLE `main_eeoccategory` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `eeoccategory` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `createdby` int(11) DEFAULT NULL,
  `modifiedby` int(11) DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `isactive` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `main_eeoccategory` */

/*Table structure for table `main_emailcontacts` */

DROP TABLE IF EXISTS `main_emailcontacts`;

CREATE TABLE `main_emailcontacts` (
  `id` tinyint(4) NOT NULL AUTO_INCREMENT,
  `group_id` int(11) DEFAULT NULL,
  `business_unit_id` int(11) DEFAULT NULL,
  `groupEmail` varchar(50) NOT NULL,
  `isactive` tinyint(4) unsigned DEFAULT '1',
  `createdBy` int(11) DEFAULT NULL,
  `modifiedBy` int(11) DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `main_emailcontacts` */

/*Table structure for table `main_emailgroups` */

DROP TABLE IF EXISTS `main_emailgroups`;

CREATE TABLE `main_emailgroups` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `group_name` varchar(100) NOT NULL,
  `group_code` varchar(50) DEFAULT NULL,
  `description` varchar(200) DEFAULT NULL,
  `isactive` tinyint(1) unsigned DEFAULT '1',
  `createdby` int(11) unsigned DEFAULT NULL,
  `modifiedby` int(11) unsigned DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

/*Data for the table `main_emailgroups` */

insert  into `main_emailgroups`(`id`,`group_name`,`group_code`,`description`,`isactive`,`createdby`,`modifiedby`,`createddate`,`modifieddate`) values (1,'Requisition - HR Group','REQ_HR','Used in resource requisition for HR',1,NULL,NULL,'2013-10-05 14:22:17','2013-10-05 14:22:17'),(2,'Leave Management','LV_HR','Used in leave management',1,NULL,NULL,'2013-10-05 14:22:17','2013-10-05 14:22:17'),(3,'Performance Appraisal','PER_APPRAISAL','Used in performance appraisal',1,NULL,NULL,'2013-10-05 14:22:17','2013-10-05 14:22:17'),(4,'Background Check - HR Group','BG_CHECKS_HR','Used in background check',1,NULL,NULL,'2013-10-05 14:22:17','2013-10-05 14:22:17'),(5,'Requisition - Management Group','REQ_MGMT','Used in resource requisition for Management',1,NULL,NULL,'2013-10-08 00:00:00','2013-10-08 00:00:00'),(6,'Background Check - Management Group','BG_CHECKS_MNGMNT','Background check management',1,NULL,NULL,'2013-10-10 14:16:31','2013-10-10 14:16:31');

/*Table structure for table `main_emaillogs` */

DROP TABLE IF EXISTS `main_emaillogs`;

CREATE TABLE `main_emaillogs` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `fromEmail` varchar(200) DEFAULT NULL,
  `toEmail` varchar(200) DEFAULT NULL,
  `toName` varchar(200) DEFAULT NULL,
  `cc` text,
  `bcc` text,
  `emailsubject` varchar(255) DEFAULT NULL,
  `header` varchar(255) DEFAULT NULL,
  `message` text,
  `is_sent` tinyint(1) unsigned DEFAULT '0' COMMENT '1=sent,0= not sent',
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `key1` varchar(50) DEFAULT NULL,
  `key2` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `main_emaillogs` */

/*Table structure for table `main_emp_reporting` */

DROP TABLE IF EXISTS `main_emp_reporting`;

CREATE TABLE `main_emp_reporting` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `emp_id` bigint(20) DEFAULT NULL,
  `reporting_manager_id` bigint(20) DEFAULT NULL,
  `isactive` tinyint(1) unsigned DEFAULT '1',
  `createdby` int(11) unsigned DEFAULT NULL,
  `modifiedby` int(11) unsigned DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `main_emp_reporting` */

/*Table structure for table `main_empadditionaldetails` */

DROP TABLE IF EXISTS `main_empadditionaldetails`;

CREATE TABLE `main_empadditionaldetails` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) DEFAULT NULL,
  `military_status` tinyint(1) DEFAULT NULL COMMENT '1-Yes,2-No',
  `countries_served` int(11) unsigned DEFAULT NULL,
  `branch_service` varchar(100) DEFAULT NULL,
  `rank_achieved` varchar(100) DEFAULT NULL,
  `from_date` date DEFAULT NULL,
  `to_date` date DEFAULT NULL,
  `special_training` text,
  `awards` text,
  `discharge_status` tinyint(1) DEFAULT NULL COMMENT '1-Honorable,2-Medical',
  `service_number` varchar(100) DEFAULT NULL,
  `rank` varchar(100) DEFAULT NULL,
  `verification_report` varchar(100) DEFAULT NULL,
  `military_servicetype` int(11) unsigned DEFAULT NULL,
  `veteran_status` int(11) unsigned DEFAULT NULL,
  `createdby` bigint(20) unsigned DEFAULT NULL,
  `modifiedby` bigint(20) unsigned DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `isactive` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `main_empadditionaldetails` */

/*Table structure for table `main_empcertificationdetails` */

DROP TABLE IF EXISTS `main_empcertificationdetails`;

CREATE TABLE `main_empcertificationdetails` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `course_name` varchar(100) DEFAULT NULL,
  `description` text,
  `course_level` varchar(100) DEFAULT NULL,
  `course_offered_by` varchar(100) DEFAULT NULL,
  `certification_name` varchar(100) DEFAULT NULL,
  `issued_date` date DEFAULT NULL,
  `createdby` bigint(20) DEFAULT NULL,
  `modifiedby` bigint(20) DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `isactive` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `main_empcertificationdetails` */

/*Table structure for table `main_empcommunicationdetails` */

DROP TABLE IF EXISTS `main_empcommunicationdetails`;

CREATE TABLE `main_empcommunicationdetails` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) DEFAULT NULL,
  `personalemail` varchar(100) DEFAULT NULL,
  `perm_streetaddress` varchar(200) DEFAULT NULL,
  `perm_country` bigint(20) DEFAULT NULL,
  `perm_state` bigint(20) DEFAULT NULL,
  `perm_city` bigint(20) DEFAULT NULL,
  `perm_pincode` varchar(15) DEFAULT NULL,
  `current_streetaddress` varchar(200) DEFAULT NULL,
  `current_country` bigint(20) DEFAULT NULL,
  `current_state` bigint(20) DEFAULT NULL,
  `current_city` bigint(20) DEFAULT NULL,
  `current_pincode` varchar(15) DEFAULT NULL,
  `emergency_number` varchar(100) DEFAULT NULL,
  `emergency_name` varchar(50) DEFAULT NULL,
  `emergency_email` varchar(100) DEFAULT NULL,
  `createdby` bigint(20) DEFAULT NULL,
  `modifiedby` bigint(20) DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `isactive` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `main_empcommunicationdetails` */

/*Table structure for table `main_empcreditcarddetails` */

DROP TABLE IF EXISTS `main_empcreditcarddetails`;

CREATE TABLE `main_empcreditcarddetails` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) DEFAULT NULL,
  `card_type` varchar(100) DEFAULT NULL,
  `card_number` bigint(20) unsigned DEFAULT NULL,
  `nameoncard` varchar(100) DEFAULT NULL,
  `card_expiration` date DEFAULT NULL,
  `card_issued_comp` varchar(255) DEFAULT NULL,
  `card_code` varchar(100) DEFAULT NULL,
  `createdby` bigint(20) unsigned DEFAULT NULL,
  `modifiedby` bigint(20) unsigned DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `isactive` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `main_empcreditcarddetails` */

/*Table structure for table `main_empdependencydetails` */

DROP TABLE IF EXISTS `main_empdependencydetails`;

CREATE TABLE `main_empdependencydetails` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) DEFAULT NULL,
  `dependent_name` varchar(100) DEFAULT NULL,
  `dependent_relation` varchar(100) DEFAULT NULL,
  `dependent_custody` varchar(100) DEFAULT NULL,
  `dependent_dob` date DEFAULT NULL,
  `dependent_age` int(11) unsigned DEFAULT NULL,
  `createdby` bigint(20) unsigned DEFAULT NULL,
  `modifiedby` bigint(20) unsigned DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `isactive` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `main_empdependencydetails` */

/*Table structure for table `main_empdisabilitydetails` */

DROP TABLE IF EXISTS `main_empdisabilitydetails`;

CREATE TABLE `main_empdisabilitydetails` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) DEFAULT NULL,
  `disability_name` varchar(50) DEFAULT NULL,
  `disability_type` varchar(100) DEFAULT NULL,
  `other_disability_type` varchar(100) DEFAULT NULL,
  `disability_description` text,
  `createdby` bigint(20) DEFAULT NULL,
  `modifiedby` bigint(20) DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `isactive` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `main_empdisabilitydetails` */

/*Table structure for table `main_empeducationdetails` */

DROP TABLE IF EXISTS `main_empeducationdetails`;

CREATE TABLE `main_empeducationdetails` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) DEFAULT NULL,
  `educationlevel` int(11) unsigned DEFAULT NULL,
  `institution_name` varchar(255) DEFAULT NULL,
  `course` varchar(100) DEFAULT NULL,
  `from_date` date DEFAULT NULL,
  `to_date` date DEFAULT NULL,
  `percentage` int(11) unsigned DEFAULT NULL,
  `createdby` bigint(20) unsigned DEFAULT NULL,
  `modifiedby` bigint(20) unsigned DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `isactive` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `main_empeducationdetails` */

/*Table structure for table `main_empexperiancedetails` */

DROP TABLE IF EXISTS `main_empexperiancedetails`;

CREATE TABLE `main_empexperiancedetails` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) DEFAULT NULL,
  `comp_name` varchar(100) DEFAULT NULL,
  `comp_website` varchar(255) DEFAULT NULL,
  `designation` varchar(100) DEFAULT NULL,
  `from_date` date DEFAULT NULL,
  `to_date` date DEFAULT NULL,
  `reason_for_leaving` text,
  `reference_name` varchar(100) DEFAULT NULL,
  `reference_contact` varchar(100) DEFAULT NULL COMMENT 'referrer contact data type is changed from bigint(20) to varchar',
  `reference_email` varchar(100) DEFAULT NULL,
  `createdby` bigint(20) DEFAULT NULL,
  `modifiedby` bigint(20) DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `isactive` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `main_empexperiancedetails` */

/*Table structure for table `main_empholidays` */

DROP TABLE IF EXISTS `main_empholidays`;

CREATE TABLE `main_empholidays` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `holiday_group_id` int(10) unsigned DEFAULT NULL,
  `createdby` bigint(20) unsigned DEFAULT NULL,
  `modifiedby` bigint(20) unsigned DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `isactive` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `main_empholidays` */

/*Table structure for table `main_empjobhistory` */

DROP TABLE IF EXISTS `main_empjobhistory`;

CREATE TABLE `main_empjobhistory` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) DEFAULT NULL,
  `positionheld` int(11) unsigned DEFAULT NULL,
  `department` int(11) unsigned DEFAULT NULL,
  `jobtitleid` int(11) unsigned DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `active_company` tinyint(1) DEFAULT NULL COMMENT '1-yes,2-No',
  `client_id` int(11) DEFAULT NULL,
  `vendor` varchar(200) DEFAULT NULL,
  `paid_amount` decimal(25,2) DEFAULT NULL,
  `received_amount` decimal(25,2) DEFAULT NULL,
  `createdby` bigint(20) unsigned DEFAULT NULL,
  `modifiedby` bigint(20) unsigned DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `isactive` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `main_empjobhistory` */

/*Table structure for table `main_employeedocuments` */

DROP TABLE IF EXISTS `main_employeedocuments`;

CREATE TABLE `main_employeedocuments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `attachments` text,
  `createdby` bigint(20) DEFAULT NULL,
  `modifiedby` bigint(20) DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `isactive` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/*Data for the table `main_employeedocuments` */

/*Table structure for table `main_employeeleaves` */

DROP TABLE IF EXISTS `main_employeeleaves`;

CREATE TABLE `main_employeeleaves` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `emp_leave_limit` float DEFAULT NULL,
  `used_leaves` float DEFAULT NULL,
  `alloted_year` year(4) DEFAULT NULL,
  `createdby` int(11) unsigned DEFAULT NULL,
  `modifiedby` int(11) unsigned DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `isactive` tinyint(1) DEFAULT '1',
  `isleavetrasnferset` tinyint(1) DEFAULT '0' COMMENT '0-Notset,1-set',
  PRIMARY KEY (`id`),
  UNIQUE KEY `User_year` (`user_id`,`alloted_year`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `main_employeeleaves` */

/*Table structure for table `main_employeeleavetypes` */

DROP TABLE IF EXISTS `main_employeeleavetypes`;

CREATE TABLE `main_employeeleavetypes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `leavetype` varchar(255) DEFAULT NULL,
  `numberofdays` int(11) DEFAULT NULL,
  `leavecode` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `leavepreallocated` tinyint(4) DEFAULT NULL COMMENT '1-yes,2-No',
  `leavepredeductable` tinyint(4) DEFAULT NULL COMMENT '1-yes,2-No',
  `createdby` int(11) unsigned DEFAULT NULL,
  `modifiedby` int(11) unsigned DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `isactive` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `main_employeeleavetypes` */

/*Table structure for table `main_employees` */

DROP TABLE IF EXISTS `main_employees`;

CREATE TABLE `main_employees` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) DEFAULT NULL,
  `date_of_joining` date DEFAULT '0000-00-00',
  `date_of_leaving` date DEFAULT '0000-00-00',
  `reporting_manager` bigint(20) unsigned DEFAULT NULL,
  `emp_status_id` int(11) unsigned DEFAULT NULL,
  `businessunit_id` int(11) unsigned DEFAULT NULL,
  `department_id` int(11) unsigned DEFAULT NULL,
  `jobtitle_id` int(11) unsigned DEFAULT NULL,
  `position_id` int(11) unsigned DEFAULT NULL,
  `years_exp` varchar(20) DEFAULT NULL COMMENT 'for numbers we are using varchar datatype',
  `holiday_group` int(11) unsigned DEFAULT NULL,
  `prefix_id` int(11) unsigned DEFAULT NULL,
  `extension_number` varchar(20) DEFAULT NULL COMMENT 'for numbers we are using varchar datatype',
  `office_number` varchar(100) DEFAULT NULL,
  `office_faxnumber` varchar(100) DEFAULT NULL,
  `createdby` int(11) unsigned DEFAULT NULL,
  `modifiedby` int(11) unsigned DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `isactive` tinyint(1) unsigned DEFAULT '1' COMMENT '1-active,5-deleted',
  `is_orghead` tinyint(1) unsigned DEFAULT '0' COMMENT '1=organisation head,0=normal employee',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `main_employees` */

/*Table structure for table `main_employees_summary` */

DROP TABLE IF EXISTS `main_employees_summary`;

CREATE TABLE `main_employees_summary` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `date_of_joining` date DEFAULT NULL,
  `date_of_leaving` date DEFAULT NULL,
  `reporting_manager` int(11) unsigned DEFAULT NULL,
  `reporting_manager_name` varchar(250) DEFAULT NULL,
  `emp_status_id` int(11) unsigned DEFAULT NULL,
  `emp_status_name` varchar(250) DEFAULT NULL,
  `businessunit_id` int(11) unsigned DEFAULT NULL,
  `businessunit_name` varchar(250) DEFAULT NULL,
  `department_id` int(11) unsigned DEFAULT NULL,
  `department_name` varchar(250) DEFAULT NULL,
  `jobtitle_id` int(11) unsigned DEFAULT NULL,
  `jobtitle_name` varchar(250) DEFAULT NULL,
  `position_id` int(11) unsigned DEFAULT NULL,
  `position_name` varchar(250) DEFAULT NULL,
  `years_exp` varchar(10) DEFAULT NULL,
  `holiday_group` int(11) unsigned DEFAULT NULL,
  `holiday_group_name` varchar(250) DEFAULT NULL,
  `prefix_id` int(11) unsigned DEFAULT NULL,
  `prefix_name` varchar(250) DEFAULT NULL,
  `extension_number` varchar(20) DEFAULT NULL,
  `office_number` varchar(20) DEFAULT NULL,
  `office_faxnumber` varchar(20) DEFAULT NULL,
  `emprole` int(11) unsigned DEFAULT NULL,
  `emprole_name` varchar(250) DEFAULT NULL,
  `firstname` varchar(255) DEFAULT NULL,
  `lastname` varchar(255) DEFAULT NULL,
  `userfullname` varchar(250) DEFAULT NULL,
  `emailaddress` varchar(100) DEFAULT NULL,
  `contactnumber` varchar(20) DEFAULT NULL,
  `backgroundchk_status` enum('In process','Completed','Not Applicable','Yet to start','On hold') DEFAULT NULL,
  `employeeId` varchar(20) DEFAULT NULL,
  `modeofentry` varchar(100) DEFAULT NULL,
  `other_modeofentry` varchar(100) DEFAULT NULL,
  `selecteddate` date DEFAULT NULL,
  `candidatereferredby` int(11) unsigned DEFAULT NULL,
  `referer_name` varchar(250) DEFAULT NULL,
  `profileimg` varchar(250) DEFAULT NULL,
  `createdby` int(11) unsigned DEFAULT NULL,
  `createdby_name` varchar(250) DEFAULT NULL,
  `modifiedby` int(11) unsigned DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `isactive` tinyint(2) unsigned DEFAULT NULL COMMENT '0=inactive,1-Active,2-resigned,3-left,4-suspended,5-deleted,',
  PRIMARY KEY (`id`),
  UNIQUE KEY `un1` (`user_id`),
  KEY `NewIndex1` (`user_id`),
  KEY `NewIndex2` (`reporting_manager`),
  KEY `NewIndex3` (`emp_status_id`),
  KEY `NewIndex4` (`businessunit_id`),
  KEY `NewIndex5` (`department_id`),
  KEY `NewIndex6` (`jobtitle_id`),
  KEY `NewIndex7` (`position_id`),
  KEY `NewIndex8` (`holiday_group`),
  KEY `NewIndex9` (`prefix_id`),
  KEY `NewIndex10` (`emprole`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `main_employees_summary` */

/*Table structure for table `main_employmentstatus` */

DROP TABLE IF EXISTS `main_employmentstatus`;

CREATE TABLE `main_employmentstatus` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `workcode` varchar(255) DEFAULT NULL,
  `workcodename` int(11) unsigned DEFAULT NULL,
  `default_leaves` int(11) DEFAULT '0',
  `description` varchar(255) DEFAULT NULL,
  `createdby` int(11) unsigned DEFAULT NULL,
  `modifiedby` int(11) unsigned DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `isactive` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `main_employmentstatus` */

/*Table structure for table `main_empmedicalclaims` */

DROP TABLE IF EXISTS `main_empmedicalclaims`;

CREATE TABLE `main_empmedicalclaims` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) DEFAULT NULL,
  `injury_type` tinyint(1) DEFAULT NULL COMMENT '1-injury,2-maternity,3-paternity,4-disablity',
  `injury_description` text,
  `injury_indicator` tinyint(1) DEFAULT NULL COMMENT '1-yes,2-no',
  `injured_date` date DEFAULT NULL,
  `injury_name` varchar(100) DEFAULT NULL,
  `injury_severity` tinyint(1) DEFAULT NULL COMMENT '1-Major,2-Minor',
  `disability_type` varchar(100) DEFAULT NULL,
  `other_disability_type` varchar(100) DEFAULT NULL,
  `disablity_approved` tinyint(1) DEFAULT '1' COMMENT '1-yes,2-no',
  `medical_insurer_name` varchar(100) DEFAULT NULL COMMENT 'Medical insurer name',
  `expected_date_join` date DEFAULT NULL,
  `leavebyemployeer_to_date` date DEFAULT NULL,
  `leavebyemployeer_from_date` date DEFAULT NULL,
  `leavebyemployeer_days` bigint(20) DEFAULT NULL,
  `leaveappliedbyemployee_to_date` date DEFAULT NULL,
  `leaveappliedbyemployee_from_date` date DEFAULT NULL,
  `leaveappliedbyemployee_days` bigint(20) DEFAULT NULL,
  `hospital_name` varchar(100) DEFAULT NULL,
  `hospital_address` text,
  `room_number` varchar(50) DEFAULT NULL,
  `concerned_physician_name` varchar(100) DEFAULT NULL,
  `treatment_details` text,
  `total_cost` bigint(20) DEFAULT NULL,
  `amount_claimed_for` bigint(20) DEFAULT NULL,
  `amount_approved` bigint(20) DEFAULT NULL,
  `createdby` bigint(20) DEFAULT NULL,
  `modifiedby` bigint(20) DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `isactive` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `unique_user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Data for the table `main_empmedicalclaims` */

/*Table structure for table `main_emppersonaldetails` */

DROP TABLE IF EXISTS `main_emppersonaldetails`;

CREATE TABLE `main_emppersonaldetails` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) DEFAULT NULL,
  `genderid` int(11) unsigned DEFAULT NULL,
  `maritalstatusid` int(11) unsigned DEFAULT NULL,
  `nationalityid` int(11) unsigned DEFAULT NULL,
  `ethniccodeid` int(11) unsigned DEFAULT NULL,
  `racecodeid` int(11) unsigned DEFAULT NULL,
  `languageid` int(11) unsigned DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `celebrated_dob` date DEFAULT NULL,
  `bloodgroup` varchar(100) DEFAULT NULL,
  `identity_documents` longtext,
  `createdby` bigint(20) unsigned DEFAULT NULL,
  `modifiedby` bigint(20) unsigned DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `isactive` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `main_emppersonaldetails` */

/*Table structure for table `main_empsalarydetails` */

DROP TABLE IF EXISTS `main_empsalarydetails`;

CREATE TABLE `main_empsalarydetails` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) DEFAULT NULL,
  `currencyid` int(11) unsigned DEFAULT NULL,
  `salarytype` int(11) unsigned DEFAULT NULL,
  `salary` varchar(100) DEFAULT NULL,
  `bankname` varchar(100) DEFAULT NULL,
  `accountholder_name` varchar(100) DEFAULT NULL,
  `accountholding` date DEFAULT NULL,
  `accountclasstypeid` int(11) unsigned DEFAULT NULL,
  `bankaccountid` int(11) unsigned DEFAULT NULL,
  `accountnumber` varchar(100) DEFAULT NULL,
  `createdby` bigint(20) unsigned DEFAULT NULL,
  `modifiedby` bigint(20) unsigned DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `isactive` tinyint(1) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `main_empsalarydetails` */

/*Table structure for table `main_empskills` */

DROP TABLE IF EXISTS `main_empskills`;

CREATE TABLE `main_empskills` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) DEFAULT NULL,
  `skillname` varchar(100) DEFAULT NULL,
  `yearsofexp` varchar(20) DEFAULT NULL,
  `competencylevelid` int(11) DEFAULT NULL,
  `year_skill_last_used` date DEFAULT NULL,
  `createdby` bigint(20) DEFAULT NULL,
  `modifiedby` bigint(20) DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `isactive` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `main_empskills` */

/*Table structure for table `main_empvisadetails` */

DROP TABLE IF EXISTS `main_empvisadetails`;

CREATE TABLE `main_empvisadetails` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) DEFAULT NULL,
  `passport_number` varchar(100) DEFAULT NULL,
  `passport_issue_date` date DEFAULT NULL,
  `passport_expiry_date` date DEFAULT NULL,
  `visa_number` varchar(100) DEFAULT NULL,
  `visa_type` varchar(100) DEFAULT NULL,
  `visa_issue_date` date DEFAULT NULL,
  `visa_expiry_date` date DEFAULT NULL,
  `inine_status` varchar(100) DEFAULT NULL,
  `inine_review_date` date DEFAULT NULL,
  `issuing_authority` varchar(100) DEFAULT NULL,
  `ininetyfour_status` varchar(100) DEFAULT NULL,
  `ininetyfour_expiry_date` date DEFAULT NULL,
  `createdby` bigint(20) DEFAULT NULL,
  `modifiedby` bigint(20) DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `isactive` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `main_empvisadetails` */

/*Table structure for table `main_empworkdetails` */

DROP TABLE IF EXISTS `main_empworkdetails`;

CREATE TABLE `main_empworkdetails` (
  `id` int(11) unsigned NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `company_name` varchar(255) DEFAULT NULL,
  `contact_number` varchar(15) DEFAULT NULL,
  `company_address` text,
  `company_website` varchar(200) DEFAULT NULL,
  `emp_designation` varchar(100) DEFAULT NULL,
  `emp_fromdate` datetime DEFAULT NULL,
  `emp_todate` datetime DEFAULT NULL,
  `createdby` int(10) unsigned DEFAULT NULL,
  `modifiedby` int(10) unsigned DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `isactive` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `main_empworkdetails` */

/*Table structure for table `main_empworkeligibility` */

DROP TABLE IF EXISTS `main_empworkeligibility`;

CREATE TABLE `main_empworkeligibility` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) DEFAULT NULL,
  `documenttype_id` int(11) unsigned DEFAULT NULL,
  `doc_issue_date` date DEFAULT NULL,
  `doc_expiry_date` date DEFAULT NULL,
  `issuingauth_name` varchar(100) DEFAULT NULL,
  `issuingauth_country` int(11) unsigned DEFAULT NULL,
  `issuingauth_state` int(11) unsigned DEFAULT NULL,
  `issuingauth_city` int(11) unsigned DEFAULT NULL,
  `issuingauth_postalcode` varchar(15) DEFAULT NULL,
  `createdby` bigint(20) DEFAULT NULL,
  `modifiedby` bigint(20) DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `isactive` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `main_empworkeligibility` */

/*Table structure for table `main_ethniccode` */

DROP TABLE IF EXISTS `main_ethniccode`;

CREATE TABLE `main_ethniccode` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ethniccode` varchar(255) NOT NULL,
  `ethnicname` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `createdby` int(11) unsigned DEFAULT NULL,
  `modifiedby` int(11) unsigned DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `isactive` tinyint(1) unsigned DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;

/*Data for the table `main_ethniccode` */

insert  into `main_ethniccode`(`id`,`ethniccode`,`ethnicname`,`description`,`createdby`,`modifiedby`,`createddate`,`modifieddate`,`isactive`) values (1,'','Arab','',1,1,'2016-11-02 11:14:00','2016-11-02 11:14:00',1),(2,'','African','',1,1,'2016-11-02 11:14:00','2016-11-02 11:14:00',1),(3,'','Caribbean','',1,1,'2016-11-02 11:14:00','2016-11-02 11:14:00',1),(4,'','Chinese  ','',1,1,'2016-11-02 11:14:00','2016-11-02 11:14:00',1),(5,'','Indian','',1,1,'2016-11-02 11:14:00','2016-11-02 11:14:00',1),(6,'','Irish','',1,1,'2016-11-02 11:14:00','2016-11-02 11:14:00',1),(7,'','Welsh/English/Scottish/Northern Irish/British ','',1,1,'2016-11-02 11:14:00','2016-11-02 11:14:00',1),(8,'','White and Asian ','',1,1,'2016-11-02 11:14:00','2016-11-02 11:14:00',1),(9,'','White and Black African ','',1,1,'2016-11-02 11:14:00','2016-11-02 11:14:00',1),(10,'','White and Black Caribbean ','',1,1,'2016-11-02 11:14:00','2016-11-02 11:14:00',1);

/*Table structure for table `main_exit_history` */

DROP TABLE IF EXISTS `main_exit_history`;

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

/*Table structure for table `main_exit_process` */

DROP TABLE IF EXISTS `main_exit_process`;

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

/*Table structure for table `main_exit_questions` */

DROP TABLE IF EXISTS `main_exit_questions`;

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

/*Table structure for table `main_exit_questions_response` */

DROP TABLE IF EXISTS `main_exit_questions_response`;

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

/*Table structure for table `main_exit_settings` */

DROP TABLE IF EXISTS `main_exit_settings`;

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

/*Table structure for table `main_exit_types` */

DROP TABLE IF EXISTS `main_exit_types`;

CREATE TABLE `main_exit_types` (                                                            
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

/*Data for the table `main_exit_types` */

insert into `main_exit_types` 
(exit_type, description, createdby, modifiedby, createddate, modifieddate, isactive, isused)
values
('Resign', 'Resign', '1', '1', now(), now(), 1, 0),
('Transfer', 'Transfer', '1', '1', now(), now(), 1, 0),
('Retirement', 'Retirement', '1', '1', now(), now(), 1, 0);

/*Table structure for table `main_gender` */

DROP TABLE IF EXISTS `main_gender`;

CREATE TABLE `main_gender` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `gendercode` varchar(255) NOT NULL,
  `gendername` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `createdby` int(11) unsigned DEFAULT NULL,
  `modifiedby` int(11) unsigned DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `isactive` tinyint(1) unsigned DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

/*Data for the table `main_gender` */

insert  into `main_gender`(`id`,`gendercode`,`gendername`,`description`,`createdby`,`modifiedby`,`createddate`,`modifieddate`,`isactive`) values (1,'M','Male','',1,1,'2016-11-02 11:14:01','2016-11-02 11:14:01',1),(2,'F','Female','',1,1,'2016-11-02 11:14:01','2016-11-02 11:14:01',1),(3,'O','Others','',1,1,'2016-11-02 11:14:01','2016-11-02 11:14:01',1);

/*Table structure for table `main_geographygroup` */

DROP TABLE IF EXISTS `main_geographygroup`;

CREATE TABLE `main_geographygroup` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `currency` int(11) DEFAULT NULL,
  `geographygroupname` varchar(255) DEFAULT NULL,
  `geographycode` varchar(255) NOT NULL,
  `geographyregion` varchar(255) DEFAULT NULL,
  `geographycityname` varchar(255) DEFAULT NULL,
  `defaultGeographyGroup` varchar(255) DEFAULT NULL,
  `createdby` int(11) unsigned DEFAULT NULL,
  `modifiedby` int(11) unsigned DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `isactive` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `main_geographygroup` */

/*Table structure for table `main_groups` */

DROP TABLE IF EXISTS `main_groups`;

CREATE TABLE `main_groups` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `group_name` varchar(60) NOT NULL,
  `description` varchar(150) DEFAULT NULL,
  `level` int(11) unsigned DEFAULT NULL,
  `isactive` tinyint(1) unsigned DEFAULT '1' COMMENT '1=active,0=inactive',
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `createdby` int(11) unsigned DEFAULT NULL,
  `modifiedby` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

/*Data for the table `main_groups` */

insert  into `main_groups`(`id`,`group_name`,`description`,`level`,`isactive`,`created`,`modified`,`createdby`,`modifiedby`) values (1,'Management','This is Management group.',1,1,'2013-08-19 11:51:14','2013-08-19 11:51:14',1,1),(2,'Manager','This is manager group.',2,1,'2013-08-19 11:51:14','2013-08-19 11:51:14',1,1),(3,'HR','This is hr group.',3,1,'2013-08-19 11:51:14','2013-08-19 11:51:14',1,1),(4,'Employees','This is employees group.',4,1,'2013-08-19 11:51:14','2013-08-19 11:51:14',1,1),(5,'External Users','This is user group.',5,1,'2013-08-19 16:29:14','2013-08-19 16:29:14',1,1),(6,'System Admin','This is the system administration group',6,1,'2013-08-19 16:29:14','2013-08-19 16:29:14',1,1);

/*Table structure for table `main_hierarchylevels` */

DROP TABLE IF EXISTS `main_hierarchylevels`;

CREATE TABLE `main_hierarchylevels` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `level_number` int(11) DEFAULT NULL,
  `parent` bigint(20) DEFAULT NULL,
  `userid` bigint(20) DEFAULT NULL,
  `createdby` bigint(11) DEFAULT NULL,
  `modifiedby` bigint(11) DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `isactive` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `userid_unique` (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `main_hierarchylevels` */

/*Table structure for table `main_holidaydates` */

DROP TABLE IF EXISTS `main_holidaydates`;

CREATE TABLE `main_holidaydates` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `holidayname` varchar(255) DEFAULT NULL,
  `groupid` int(11) DEFAULT NULL,
  `holidaydate` date DEFAULT NULL,
  `holidayyear` year(4) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `createdby` int(11) unsigned DEFAULT NULL,
  `modifiedby` int(11) unsigned DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `isactive` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `main_holidaydates` */

/*Table structure for table `main_holidaygroups` */

DROP TABLE IF EXISTS `main_holidaygroups`;

CREATE TABLE `main_holidaygroups` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `groupname` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `createdby` int(11) unsigned DEFAULT NULL,
  `modifiedby` int(11) unsigned DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `isactive` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `main_holidaygroups` */

/*Table structure for table `main_hr_wizard` */

DROP TABLE IF EXISTS `main_hr_wizard`;

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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

/*Data for the table `main_hr_wizard` */

insert  into `main_hr_wizard`(`id`,`leavetypes`,`holidays`,`perf_appraisal`,`iscomplete`,`createdby`,`modifiedby`,`createddate`,`modifieddate`,`isactive`) values (2,1,1,1,1,1,1,'2016-11-02 11:14:32','2016-11-02 11:14:32',1);

/*Table structure for table `main_identitycodes` */

DROP TABLE IF EXISTS `main_identitycodes`;

CREATE TABLE `main_identitycodes` (
  `id` tinyint(4) NOT NULL AUTO_INCREMENT,
  `employee_code` varchar(100) DEFAULT NULL,
  `backgroundagency_code` varchar(100) DEFAULT NULL,
  `vendors_code` varchar(100) DEFAULT NULL,
  `staffing_code` varchar(100) DEFAULT NULL,
  `users_code` varchar(10) DEFAULT NULL COMMENT 'for users',
  `requisition_code` varchar(10) DEFAULT NULL COMMENT 'for requisition',
  `createdBy` int(11) DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifiedBy` int(11) DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

/*Data for the table `main_identitycodes` */

insert  into `main_identitycodes`(`id`,`employee_code`,`backgroundagency_code`,`vendors_code`,`staffing_code`,`users_code`,`requisition_code`,`createdBy`,`createddate`,`modifiedBy`,`modifieddate`) values (1,'EMPP','BGCK','','','USER','REQ',1,'2015-03-05 11:46:44',1,'2015-03-05 11:46:44');

/*Table structure for table `main_identitydocuments` */

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

/*Data for the table `main_identitydocuments` */

/*Table structure for table `main_interviewdetails` */

DROP TABLE IF EXISTS `main_interviewdetails`;

CREATE TABLE `main_interviewdetails` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `req_id` int(11) unsigned DEFAULT NULL,
  `candidate_id` int(11) unsigned DEFAULT NULL,
  `interview_status` enum('In process','Completed','On hold','Requisition Closed/Completed') DEFAULT 'In process',
  `createdby` int(11) unsigned DEFAULT NULL,
  `modifiedby` int(11) unsigned DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `isactive` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `main_interviewdetails` */

/*Table structure for table `main_interviewrounddetails` */

DROP TABLE IF EXISTS `main_interviewrounddetails`;

CREATE TABLE `main_interviewrounddetails` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `interview_id` int(11) unsigned DEFAULT NULL,
  `req_id` int(11) unsigned DEFAULT NULL,
  `candidate_id` int(11) unsigned DEFAULT NULL,
  `interviewer_id` int(11) unsigned DEFAULT NULL,
  `interview_time` time DEFAULT NULL,
  `interview_date` date DEFAULT NULL,
  `interview_mode` enum('In person','Phone','Video conference') DEFAULT 'Phone',
  `interview_round_number` int(11) DEFAULT NULL,
  `interview_round` varchar(50) DEFAULT NULL,
  `interview_feedback` text,
  `interview_comments` text,
  `round_status` enum('Schedule for next round','Qualified','Selected','Disqualified','Decision pending','On hold','Incompetent','Ineligible','Candidate no show','Requisition Closed/Completed') DEFAULT NULL,
  `int_location` varchar(200) DEFAULT NULL,
  `int_city` int(11) unsigned DEFAULT NULL,
  `int_state` int(11) unsigned DEFAULT NULL,
  `int_country` int(11) unsigned DEFAULT NULL,
  `createdby` int(11) unsigned DEFAULT NULL,
  `modifiedby` int(11) unsigned DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `isactive` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `main_interviewrounddetails` */

/*Table structure for table `main_interviewrounds_summary` */

DROP TABLE IF EXISTS `main_interviewrounds_summary`;

CREATE TABLE `main_interviewrounds_summary` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `requisition_id` int(11) unsigned DEFAULT NULL,
  `candidate_id` bigint(20) unsigned DEFAULT NULL,
  `candidate_name` varchar(200) DEFAULT NULL,
  `candidate_status` enum('Shortlisted','Selected','Rejected','On hold','Disqualified','Scheduled','Not Scheduled','Recruited','Requisition Closed/Completed') DEFAULT NULL,
  `interview_status` enum('In process','Completed','On hold','Requisition Closed/Completed') DEFAULT NULL,
  `interview_id` int(11) unsigned DEFAULT NULL,
  `interviewround_id` int(11) unsigned DEFAULT NULL,
  `interviewer_id` bigint(20) unsigned DEFAULT NULL,
  `interviewer_name` varchar(255) DEFAULT NULL,
  `interview_time` time DEFAULT NULL,
  `interview_date` date DEFAULT NULL,
  `interview_mode` enum('In person','Phone','Video conference') DEFAULT NULL,
  `interview_round_number` int(11) unsigned DEFAULT NULL,
  `interview_round_name` varchar(200) DEFAULT NULL,
  `interview_location` varchar(200) DEFAULT NULL,
  `interview_city_id` int(11) unsigned DEFAULT NULL,
  `interview_state_id` int(11) unsigned DEFAULT NULL,
  `interview_city_name` varchar(255) DEFAULT NULL,
  `interview_state_name` varchar(255) DEFAULT NULL,
  `interview_country_id` int(11) unsigned DEFAULT NULL,
  `interview_country_name` varchar(255) DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `created_by_name` varchar(255) DEFAULT NULL,
  `interview_feedback` text,
  `interview_comments` text,
  `round_status` enum('Schedule for next round','Qualified','Selected','Disqualified','Decision pending','On hold','Incompetent','Ineligible','Candidate no show','Requisition Closed/Completed') DEFAULT NULL,
  `modified_by` bigint(20) unsigned DEFAULT NULL,
  `created_date` datetime DEFAULT NULL,
  `modified_date` datetime DEFAULT NULL,
  `isactive` tinyint(1) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `main_interviewrounds_summary` */

/*Table structure for table `main_jobtitles` */

DROP TABLE IF EXISTS `main_jobtitles`;

CREATE TABLE `main_jobtitles` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `jobtitlecode` varchar(255) DEFAULT NULL,
  `jobtitlename` varchar(255) DEFAULT NULL,
  `jobdescription` varchar(255) DEFAULT NULL,
  `minexperiencerequired` float DEFAULT NULL,
  `jobpaygradecode` varchar(255) DEFAULT NULL,
  `jobpayfrequency` varchar(255) DEFAULT NULL,
  `comments` varchar(255) DEFAULT NULL,
  `createdby` int(11) unsigned DEFAULT NULL,
  `modifiedby` int(11) unsigned DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `isactive` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `main_jobtitles` */

/*Table structure for table `main_language` */

DROP TABLE IF EXISTS `main_language`;

CREATE TABLE `main_language` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `languagename` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `createdby` int(11) unsigned DEFAULT NULL,
  `modifiedby` int(11) unsigned DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `isactive` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `main_language` */

/*Table structure for table `main_leavemanagement` */

DROP TABLE IF EXISTS `main_leavemanagement`;

CREATE TABLE `main_leavemanagement` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `cal_startmonth` int(11) unsigned DEFAULT NULL,
  `weekend_startday` int(11) unsigned DEFAULT NULL,
  `weekend_endday` int(11) unsigned DEFAULT NULL,
  `businessunit_id` int(11) unsigned DEFAULT NULL,
  `department_id` int(11) unsigned DEFAULT '0',
  `hr_id` int(11) DEFAULT NULL,
  `hours_day` int(11) DEFAULT NULL,
  `is_satholiday` tinyint(1) DEFAULT NULL COMMENT '1-Yes,2-No',
  `is_halfday` tinyint(1) DEFAULT NULL COMMENT '1-Yes,2-No',
  `is_leavetransfer` tinyint(1) DEFAULT NULL COMMENT '1-Yes,2-No',
  `is_skipholidays` tinyint(1) DEFAULT NULL COMMENT '1-Yes,2-No',
  `description` varchar(255) DEFAULT NULL,
  `createdby` int(11) DEFAULT NULL,
  `modifiedby` int(11) DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `isactive` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `main_leavemanagement` */

/*Table structure for table `main_leavemanagement_summary` */

DROP TABLE IF EXISTS `main_leavemanagement_summary`;

CREATE TABLE `main_leavemanagement_summary` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `leavemgmt_id` bigint(20) unsigned DEFAULT NULL,
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
  `is_leavetransfer` tinyint(1) DEFAULT NULL COMMENT '1-Yes,2-No',
  `is_skipholidays` tinyint(1) DEFAULT NULL COMMENT '1-Yes,2-No',
  `description` varchar(255) DEFAULT NULL,
  `createdby` int(11) DEFAULT NULL,
  `modifiedby` int(11) DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `isactive` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `main_leavemanagement_summary` */

/*Table structure for table `main_leaverequest` */

DROP TABLE IF EXISTS `main_leaverequest`;

CREATE TABLE `main_leaverequest` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `reason` text,
  `approver_comments` text,
  `leavetypeid` int(11) unsigned DEFAULT NULL,
  `leaveday` tinyint(1) DEFAULT NULL COMMENT '1-full day,2-half day',
  `from_date` date DEFAULT NULL,
  `to_date` date DEFAULT NULL,
  `leavestatus` enum('Pending for approval','Approved','Rejected','Cancel') DEFAULT 'Pending for approval',
  `rep_mang_id` int(11) unsigned DEFAULT NULL,
  `hr_id` int(11) DEFAULT NULL,
  `no_of_days` float unsigned DEFAULT NULL,
  `appliedleavescount` float(4,1) unsigned DEFAULT NULL,
  `is_sat_holiday` tinyint(1) DEFAULT NULL COMMENT '1-yes,2-no',
  `createdby` int(11) unsigned DEFAULT NULL,
  `modifiedby` int(11) unsigned DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `isactive` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `main_leaverequest` */

/*Table structure for table `main_leaverequest_summary` */

DROP TABLE IF EXISTS `main_leaverequest_summary`;

CREATE TABLE `main_leaverequest_summary` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `leave_req_id` bigint(20) unsigned DEFAULT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `user_name` varchar(255) DEFAULT NULL,
  `department_id` bigint(20) unsigned DEFAULT NULL,
  `department_name` varchar(255) DEFAULT NULL,
  `bunit_id` bigint(20) unsigned DEFAULT NULL,
  `buss_unit_name` varchar(255) DEFAULT NULL,
  `reason` text,
  `approver_comments` text,
  `leavetypeid` int(11) unsigned DEFAULT NULL,
  `leavetype_name` varchar(255) DEFAULT NULL,
  `leaveday` tinyint(1) DEFAULT NULL COMMENT '1-full day,2-half day',
  `from_date` date DEFAULT NULL,
  `to_date` date DEFAULT NULL,
  `leavestatus` enum('Pending for approval','Approved','Rejected','Cancel') DEFAULT 'Pending for approval',
  `rep_mang_id` int(11) unsigned DEFAULT NULL,
  `rep_manager_name` varchar(255) DEFAULT NULL,
  `hr_id` int(11) DEFAULT NULL,                                                                              
  `hr_name` varchar(255) DEFAULT NULL,
  `no_of_days` float unsigned DEFAULT NULL,
  `appliedleavescount` float(4,1) unsigned DEFAULT NULL,
  `is_sat_holiday` tinyint(1) DEFAULT NULL COMMENT '1-yes,2-no',
  `createdby` int(11) unsigned DEFAULT NULL,
  `modifiedby` int(11) unsigned DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `isactive` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `main_leaverequest_summary` */

/*Table structure for table `main_licensetype` */

DROP TABLE IF EXISTS `main_licensetype`;

CREATE TABLE `main_licensetype` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `licensetype` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `createdby` int(11) unsigned DEFAULT NULL,
  `modifiedby` int(11) unsigned DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `isactive` tinyint(1) unsigned DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `main_licensetype` */

/*Table structure for table `main_logmanager` */

DROP TABLE IF EXISTS `main_logmanager`;

CREATE TABLE `main_logmanager` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `menuId` bigint(20) DEFAULT NULL,
  `user_action` tinyint(4) unsigned DEFAULT NULL COMMENT '1-add,2-edit,3-delete,4-active/inactive,5-cancel',
  `log_details` text,
  `last_modifiedby` int(11) unsigned DEFAULT NULL,
  `last_modifieddate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `key_flag` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `obj_action` (`menuId`,`user_action`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `main_logmanager` */

/*Table structure for table `main_logmanagercron` */

DROP TABLE IF EXISTS `main_logmanagercron`;

CREATE TABLE `main_logmanagercron` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `menuId` bigint(20) DEFAULT NULL,
  `user_action` tinyint(4) unsigned DEFAULT NULL COMMENT '1-add,2-edit,3-delete,4-active/inactive',
  `log_details` text,
  `last_modifiedby` int(11) unsigned DEFAULT NULL,
  `last_modifieddate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `key_flag` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `main_logmanagercron` */

/*Table structure for table `main_mail_settings` */

DROP TABLE IF EXISTS `main_mail_settings`;

CREATE TABLE `main_mail_settings` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `tls` varchar(100) NOT NULL,
  `auth` varchar(100) NOT NULL,
  `port` int(11) DEFAULT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `server_name` varchar(255) NOT NULL,
  `createddate` timestamp NULL DEFAULT NULL,
  `modifieddate` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `main_mail_settings` */

/*Table structure for table `main_maritalstatus` */

DROP TABLE IF EXISTS `main_maritalstatus`;

CREATE TABLE `main_maritalstatus` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `maritalcode` varchar(255) NOT NULL,
  `maritalstatusname` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `createdby` int(11) unsigned DEFAULT NULL,
  `modifiedby` int(11) unsigned DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `isactive` tinyint(1) unsigned DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

/*Data for the table `main_maritalstatus` */

insert  into `main_maritalstatus`(`id`,`maritalcode`,`maritalstatusname`,`description`,`createdby`,`modifiedby`,`createddate`,`modifieddate`,`isactive`) values (1,'D','Divorced','',1,1,'2016-11-02 11:14:09','2016-11-02 11:14:09',1),(2,'M','Married','',1,1,'2016-11-02 11:14:09','2016-11-02 11:14:09',1),(3,'Sep','Separated','',1,1,'2016-11-02 11:14:09','2016-11-02 11:14:09',1),(4,'S','Single','',1,1,'2016-11-02 11:14:09','2016-11-02 11:14:09',1),(5,'W','Widow / Widower','',1,1,'2016-11-02 11:14:09','2016-11-02 11:14:09',1);

/*Table structure for table `main_menu` */

DROP TABLE IF EXISTS `main_menu`;

CREATE TABLE `main_menu` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `menuName` varchar(255) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `helpText` varchar(255) DEFAULT NULL,
  `toolTip` varchar(255) DEFAULT NULL,
  `iconPath` varchar(255) DEFAULT NULL,
  `parent` int(11) unsigned DEFAULT NULL,
  `menuOrder` int(11) unsigned DEFAULT NULL,
  `nav_ids` text,
  `isactive` tinyint(1) unsigned DEFAULT '1',
  `modulename` varchar(50) DEFAULT NULL,
  `segment_flag` tinyint(1) DEFAULT '2',
  `org_menuid` int(11) unsigned DEFAULT NULL,
  `menufields` text COMMENT 'select,insert,update references',
  `menuQuery` text,
  `hasJoins` tinyint(4) DEFAULT NULL,
  `modelName` varchar(255) DEFAULT NULL,
  `functionName` varchar(255) DEFAULT NULL,
  `defaultOrderBy` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=207 DEFAULT CHARSET=latin1;

/*Data for the table `main_menu` */

insert  into `main_menu`(`id`,`menuName`,`url`,`helpText`,`toolTip`,`iconPath`,`parent`,`menuOrder`,`nav_ids`,`isactive`,`modulename`,`segment_flag`,`org_menuid`,`menufields`,`menuQuery`,`hasJoins`,`modelName`,`functionName`,`defaultOrderBy`) values (1,'Organization','/#','','','1346765145_organization.png',0,9,',1,',1,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(2,'User Management','/#','','','1346857416_usermanagment.png',0,2,',3,2,',0,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(3,'HR','/#','','','human-resource.png',0,5,',3,',1,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(4,'Self Service','/#','','','es-1.png',0,2,',4,',1,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(5,'Background Check','/#','','','1346845958_background_checks.png',0,8,',5,',1,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(6,'Staffing','/#','','','1346847089_staffing.jpg',0,11,',6,',0,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(7,'Compliances','/#','','','1346871554_compliances_main_-_updated.jpg',0,12,',7,',0,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(8,'Analytics','/reports','','','reports.png',0,10,',8,',1,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(9,'Organization Info','/organisationinfo','','','1346765145_organization.png',1,1,',1,9,',1,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(10,'Business Units','/businessunits','','','1346847776_company.jpg',1,2,',1,10,',1,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(11,'Departments','/departments','','','department-icon.jpg',1,3,',1,11,',1,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(12,'Organization Structure','/structure','','','organization-structure.png',1,4,',1,12,',1,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(13,'Organization Hierarchy','/heirarchy','','','organization-hierarchy.jpg',1,5,',1,13,',1,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(14,'Employees','/employee','','','1347027462_all-employees.png',3,1,',3,14,',1,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(15,'Benefits','/#','','','1346870021_benefits_main.jpg',3,2,',3,15,',0,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(16,'Holiday Management','/#','','','1346848159_holiday_1.png',3,4,',3,16,',1,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(17,'Leave Management','/#','','','1346859254_vacation_main.jpg',3,5,',3,17,',1,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(18,'Performance Appraisal','/#','','','1346857167_performance.png',3,6,',3,18,',0,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(19,'Recruitments','/#','','','1346857974_recruitment_main.jpg',0,7,',19,',1,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(20,'Roles & Privileges','/roles','','','roles-privileges.jpg',3,2,',3,20,',1,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(21,'External Users','/usermanagement','','','manage-users.jpg',207,1,',3,207,21',1,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(22,'Configuration','/#','','','1346870282_configuration.jpg',5,1,',5,22,',1,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(23,'Employee/Candidate Screening','/empscreening','','','1346871964_emp_screening_-_updated.jpg',141,1,',5,141,23,',1,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(24,'Vendor Screening','/vendorscreening','','','',NULL,3,',5,24,',1,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(31,'Leaves','/#','','','1346863688_vacation.jpg',4,1,',4,31,',1,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(32,'My Details','/mydetails','My Details','My Details','my-details-done.jpg',4,2,',4,32,',1,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(33,'My Performance Appraisal','/myperformanceappraisal','My Performance Appraisal','My Performance Appraisal','1347390106_2.jpg',4,4,',4,33,',0,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(34,'My Team','/myemployees','My Employees','My Employees','my-team.jpg',4,5,',4,34,',1,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(35,'My Team Appraisal','','','','1347027817_my_team_performance_appraisal.jpg',0,0,'',0,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(38,'Saving Plan Enrollment','/benefitssavingplanenrollment','Saving Plan Enrollment','Saving Plan Enrollment','1346870040_savings_plan_enroll.jpg',15,1,',3,15,38,',0,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(39,'Employee Benefits US Enrollment','/empbenefitsusenrollment','Employee Benefits US Enrollment','Employee Benefits US Enrollment','1346870057_us_enrollment.jpg',15,2,',3,15,39,',0,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(41,'Manage Holiday Group','/holidaygroups','Manage Holiday Group','Manage Holiday Group','manage-holiday-group.jpg',16,1,',3,16,41,',1,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(42,'Manage Holidays','/holidaydates','Manage Holidays','Manager Holidays','manage-holiday-dates.jpg',16,2,',3,16,42,',1,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(43,'My Holiday Calendar','/myholidaycalendar','My Holiday Calendar','My Holiday Calendar','my-holiday-calendar.jpg',4,3,',4,43,',1,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(44,'Leave Management Options','/leavemanagement','Leave Management Options','Leave Management Options','leave-management-options.jpg',17,1,',3,17,44,',1,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(45,'Employee Leave Summary','/empleavesummary','Employee Leaves Summary','Employee Leaves Summary','employee-leaves-summary.jpg',17,2,',3,17,45,',1,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(47,'KPI List','/performancekips','KPI List','KPI List','1346858920_kpis2.png',18,3,',3,18,47,',0,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(48,'KRA List','/performancekras','KRA List','KRA List','1346858937_kra.png',18,4,',3,18,48,',0,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(49,'Performance Appraisal','/performanceappraisal','Performance Appraisal','Performance Appraisal','1347027566_performance.png',18,5,',3,18,49,',0,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(50,'Configuration','/performanceapprsetup','Configuration','Configuration','1346857550_configuration.jpg',18,2,',3,18,50,',0,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(51,'Initialize Appraisal','/appraisalinitialization','Initialize Appraisal','Initialize Appraisal','1346857856_initialize-appraisal.png',18,1,',3,18,51,',0,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(54,'Openings/Positions','/requisition','Openings/Positions','Openings/Positions','1346857416_openings.jpg',19,1,',19,54,',1,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(55,'Candidates','/candidatedetails','CV Management','CV Management','cv-management.jpg',19,4,',19,55,',1,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(56,'Shortlisted & Selected Candidates','/shortlistedcandidates','Shortlisted Candidates','Shortlisted Candidates','1346857722_shortlisted_candidates.jpg',19,6,',19,56,',1,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(57,'Interviews','/scheduleinterviews','Schedule Interviews','Schedule Interviews','schedule-interview.jpg',19,5,',19,57,',1,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(61,'Leave Request','/leaverequest','Leave Request','Leave Request','1346863776_vacation_request.jpg',31,1,',4,31,61,',1,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(62,'My Leave','/pendingleaves','Pending Leaves','Pending Leaves','1346870194_pending-vacation-requests.png',31,2,',4,31,62,',1,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(63,'Approved Leaves','/approvedleaves','Approved Leaves','Approved Leaves','1346863728_approved_vacations.jpg',31,3,',4,31,63,',0,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(64,'Cancelled Leaves','/cancelleaves','Cancel Leaves','Cancel Leaves','1346863749_cancel_vacation_history.jpg',31,4,',4,31,64,',0,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(65,'Employee Leave','/manageremployeevacations','Manager Employee Vacations','Manager Employee Vacations','1346863764_manager_employee_vacations.jpg',31,6,',4,31,65,',1,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(68,'Screening Types','/bgscreeningtype','Screening Type','Screening Type','1346871975_screening_type_-_updated.jpg',22,1,',5,22,68,',1,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(69,'Agencies','/agencylist',NULL,NULL,'agency-list.jpg',22,2,',5,22,69,',1,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(70,'Site Config','/#',NULL,'','1346764980_man_dbrown.png',0,11,',70,',1,'default',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(71,'Date & Time','/#','','','1346782927_date_and_time.jpg',70,1,',70,71,',0,'default',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(72,'General','/#','','','1346782906_configuration.jpg',70,2,',70,72,',1,'default',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(73,'Location','/#','','','1346782919_locations.jpg',70,3,',70,73,',1,'default',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(74,'Military Service','/#','','','1346782946_military.jpg',113,4,',3,113,74',0,'default',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(75,'Currency','/#','','','1346782936_currency.png',70,5,',70,75,',1,'default',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(78,'Date Format','/dateformat','Date Format','Date Format','1346865851_date_format.png',71,1,',70,71,78,',0,'default',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(79,'Time Format','/timeformat','Time Format','Time Format','1346866327_time_format.png',71,2,',70,71,79,',0,'default',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(80,'Time Zones','/timezone','Time Zone','Time Zone','1346866425_time_zone.jpg',72,3,',70,72,80,',1,'default',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(81,'Days List','/weekdays','Days List','Days List','1346866486_days.png',72,4,',70,72,81,',0,'default',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(82,'Months List','/monthslist','Months List','Months List','1346869824_months.png',72,5,',70,72,82,',0,'default',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(85,'Ethnic Codes','/ethniccode','Ethnic Codes','Ethnic Codes','ethnic-codes.jpg',72,1,',70,72,85,',1,'default',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(86,'Gender','/gender','Gender','Gender','1346863882_gender.png',72,2,',70,72,86,',1,'default',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(87,'Marital Status','/maritalstatus','Marital Status','Marital Status','marital_status.jpg',72,3,',70,72,87,',1,'default',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(88,'Prefixes','/prefix','Prefix','Prefix','prefix.jpg',72,4,',70,72,88,',1,'default',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(89,'Race Codes','/racecode','Race Codes','Race Codes','race-codes.png',72,5,',70,72,89,',1,'default',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(90,'Nationality Context Codes','/nationalitycontextcode','Nationality Context Code','Nationality Context Code','nationality-contex-code.jpg',72,6,',70,72,90,',1,'default',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(91,'Nationalities','/nationality','Nationality','Nationality','nationality.jpg',72,7,',70,72,91,',1,'default',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(92,'Account Class Types','/accountclasstype','Account Class Types','Account Class Types','account_class_types.jpg',72,8,',70,72,92,',1,'default',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(93,'License Types','/licensetype','License Type','License Type','licence-type.jpg',72,9,',70,72,93,',1,'default',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(100,'Countries','/countries','Countries','Countries','countries.jpg',73,1,',70,73,100,',1,'default',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(101,'States','/states','States','States','states.jpg',73,2,',70,73,101,',1,'default',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(102,'Cities','/cities','Cities','Cities','cities.jpg',73,3,',70,73,102,',1,'default',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(103,'Geo Groups','/geographygroup','Geo Groups','Geo Groups','geo-groups.jpg',73,4,',70,73,103,',1,'default',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(107,'Veteran Status','/veteranstatus','Veteran Status','Veteran Status','veteran-status.jpg',113,16,',3,113,107',1,'default',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(108,'Military Service Types','/militaryservice','Military Service Type','Military Service Type','military-service-type.jpg',113,17,',3,113,108',1,'default',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(110,'Currencies','/currency','Currencies','Currencies','1346866587_currency.png',75,1,',70,75,110,',1,'default',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(111,'Currency Conversions','/currencyconverter','Currency Conversions','Currency Conversions','1346933158_currency_converter.jpg',75,2,',70,75,111,',1,'default',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(113,'Employee Configuration','/#',NULL,NULL,'employee-configurations.png',3,7,',113,',1,'default',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(114,'Employment Status','/employmentstatus','Employment Status','Employment Status','employment-status.jpg',113,2,',3,113,114',1,'default',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(115,'EEOC Categories','/eeoccategory','','','1346855803_eeoc.jpg',113,14,',3,113,115',1,'default',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(116,'Job Titles','/jobtitles','Job Titles','Job Titles','1346869916_job-titlesb.jpg',113,5,',3,113,116',1,'default',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(117,'Pay Frequency','/payfrequency','Pay Frequency','Pay Frequency','1346856548_pay_freq_2.png',113,3,',3,113,117',1,'default',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(118,'Remuneration Basis','/remunerationbasis','Remuneration Basis','Remuneration Basis','remuneration_basis.jpg',113,4,',3,113,118',1,'default',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(119,'Departments','/departments','','','1346855770_department.png',113,6,',3,113,119',0,'default',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(120,'Positions','/positions','Positions','Positions','positions.jpg',113,6,',3,113,120',1,'default',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(121,'Languages','/language','Languages','Languages','languages.jpg',113,9,',3,113,121',1,'default',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(122,'Assignment Entry Reasons','/assignmententryreasoncode','Assignment Entry Reasons','Assignment Entry Reasons','1347027509_vacation_request_options.jpg',113,9,',3,113,122',0,'default',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(123,'Bank Account Types','/bankaccounttype','','','bank-account.jpg',113,12,',3,113,123',1,'default',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(124,'Competency Levels','/competencylevel','Competency Level','Competency Level','competency-level.jpg',113,7,',3,113,124',1,'default',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(125,'Education Levels','/educationlevelcode','Education Levels','Education Levels','1346855779_education.jpg',113,8,',3,113,125',1,'default',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(126,'Attendance Status','/attendancestatuscode','','','attendance-status.jpg',113,11,',3,113,126',1,'default',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(127,'Work Eligibility Document Types','/workeligibilitydoctypes','Work Eligibility Document Types','Work Eligibility Document Types','work-eligibility-document.jpg',113,15,',3,113,127',1,'default',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(128,'Leave Types','/employeeleavetypes','','','leave-types.jpg',113,10,',3,113,128',1,'default',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(130,'Time','/timemanagement',NULL,NULL,'time-management.jpg',0,16,',130,',1,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(131,'Site Preferences','/sitepreference','','','site-preferences.png',70,1,',70,131,',1,'default',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(132,'Number Formats','/numberformats','','','1346871311_number_format.jpg',72,12,',70,72,132,',1,'default',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(133,'Identity Codes','/identitycodes','','','identity-codes.jpg',72,1,',70,72,133,',1,'default',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(134,'Approved Requisitions','/approvedrequisitions','Approve Requisition','Approve Requisition','approved-requisitions.jpg',19,2,',19,134,',1,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(135,'Rejected Leaves','/rejectedleaves','','','rejected-leaves.jpg',31,5,',4,31,135,',0,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(136,'Email Contacts','/emailcontacts','','','email-contacts.jpg',72,11,',70,72,136,',1,'default',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(138,'Rejected Requisitions','/rejectedrequisitions','Rejected Requisitions','Rejected Requisitions','rejected-requisitions.jpg',19,3,',19,138,',1,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(139,'Identity Documents','/identitydocuments','','','identity-documents.jpg',113,13,',3,113,139',1,'default',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(140,'Employee Tabs','/empconfiguration','Configure Employee Tabs','Employee Tabs','employee-tabs.jpg',113,1,',3,113,140',1,'default',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(141,'Screening','/#','','','',5,2,',5,141,',1,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(142,'Modules','/managemenus','Manage Modules','Manage Modules','manage-module.jpg',0,12,',142,',1,'default',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(143,'Service Request','/#',NULL,NULL,'manage-module.jpg',0,4,',143,',1,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(144,'Categories','/servicedeskdepartment',NULL,NULL,'categories.jpg',147,1,',143,147,144,',1,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(145,'Request Types','/servicedeskrequest',NULL,NULL,'request-types.jpg',147,2,',143,147,145,',1,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(146,'Settings','/servicedeskconf',NULL,NULL,'settings.jpg',147,3,',143,147,146,',1,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(147,'Configuration','/#',NULL,NULL,NULL,143,1,',143,147,',1,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(148,'Service Request Transactions','/servicerequests',NULL,NULL,NULL,143,2,',143,148,',1,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(149,'Appraisals','/#',NULL,NULL,'perf_app.png',0,6,',149,',1,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(150,'Parameters','/appraisalcategory',NULL,NULL,NULL,162,2,',149,162,150,',1,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(151,'Skills','/appraisalskills',NULL,NULL,'skills.jpg',162,4,',149,162,151,',1,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(152,'Questions','/appraisalquestions',NULL,NULL,'questions.jpg',162,3,',149,162,152,',1,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(154,'Initialize Appraisal','/appraisalinit',NULL,NULL,'initialization.jpg',149,2,',149,154,',1,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(155,'Appraisal Settings','/appraisalconfig',NULL,NULL,'configurations.jpg',0,1,',149,162,155,',0,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(158,'Manager Status','/appraisalstatus/manager',NULL,NULL,NULL,149,4,',149,158,',1,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(159,'Employee Status','/appraisalstatus/employee',NULL,NULL,NULL,149,5,',149,159,',1,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(160,'Ratings','/appraisalratings',NULL,NULL,NULL,162,5,',149,162,160,',1,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(161,'Self Appraisal','/appraisalself',NULL,NULL,NULL,149,6,',149,161,',1,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(162,'Configuration','/#',NULL,NULL,NULL,149,1,',149,162,',1,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(163,'Feedforward','/#',NULL,NULL,NULL,149,8,',149,163,',1,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(166,'Questions','/feedforwardquestions',NULL,NULL,NULL,163,2,',149,163,166,',1,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(167,'Initialize Feedforward','/feedforwardinit',NULL,NULL,NULL,163,3,',149,163,167,',1,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(168,'Announcements','/announcements',NULL,NULL,NULL,1,6,',1,168,',1,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(169,'Manager Appraisal','/appraisalmanager',NULL,NULL,NULL,149,3,',149,169,',1,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(170,'Appraise Your Manager','/feedforwardemployee',NULL,NULL,NULL,163,3,',163,170,',1,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(171,'Manager Feedforward','/feedforwardmanager',NULL,NULL,NULL,163,4,',163,171,',1,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(172,'Employee Status','/feedforwardstatus',NULL,NULL,NULL,163,5,',163,172,',1,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(174,'My Team Appraisal','/myteamappraisal','My Team Appraisal','My Team Appraisal','1347027817_my_team_performance_appraisal.jpg',149,7,',149,174,',1,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(175,'Appraisal History','/#','Appraisal History','Appraisal History','appraisal_history.jpg',149,7,',149,175,',1,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(176,'Policy Documents','/#','Policy Documents','Policy Documents','policy_documents.jpg',1,7,',1,176,',1,'default',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(177,'Exit Procedure','/#','Employee Exit Procedure','Employee Exit Procedure','exit_procedure.jpg',3,6,',3,177,',1,'exit',0,0,NULL,NULL,NULL,NULL,NULL,NULL),(178,'Settings','/exit/exitprocsettings','Employee Exit Procedure Settings','Employee Exit Procedure Settings','exit_procedure.jpg',177,3,',3,177,178,',1,'exit',0,0,NULL,NULL,NULL,NULL,NULL,NULL),(179,'Exit Types','/exit/exittypes','Exit Types','Exit Types','exit_types.jpg',177,1,',3,177,179,',1,'exit',0,0,NULL,NULL,NULL,NULL,NULL,NULL),(180,'Initiate/Check Status','/exit/exitproc','Initiate exit proc or check status','Initiate exit proc or check status','initiate_exit_proc.jpg',177,4,',3,177,180,',1,'exit',0,0,NULL,NULL,NULL,NULL,NULL,NULL),(181,'All Exit Procedures','/exit/allexitproc','All exit procedures','All exit procedures','all_exit_proc.jpg',177,5,',3,177,181,',1,'exit',0,0,NULL,NULL,NULL,NULL,NULL,NULL),(182,'Manage Categories','/categories','Categories for Policy documents','Categories for Policy documents','pd_categories.jpg',176,1,',1,176,182,',1,'default',0,0,NULL,NULL,NULL,NULL,NULL,NULL),(183,'View/Manage Policy Documents','/policydocuments','View or Manage Policy documents','View or Manage Policy documents','',176,2,',4,176,183,',1,'default',0,0,NULL,NULL,NULL,NULL,NULL,NULL),(184,'Add Employee Leave','/addemployeeleaves','Add Employee Leaves','Add Employee Leaves','addemployeeleaves.jpg',17,3,',3,17,184',1,'default',2,302,NULL,NULL,NULL,NULL,NULL,NULL),(185,'Expenses','/#','Add Employee Expenses','Add Employee Expenses',NULL,0,18,',185,',1,'expenses',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(186,'Assets','/#','Add Company Assets','Add Company Assets',NULL,0,19,',186,',1,'assets',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(187,'Vendors','/vendors','Add Vendor for Assets','Add Vendor for Assets',NULL,207,2,',3,207,187',1,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(188,'My Appraisal History','/appraisalhistoryself','My Appraisal History','My Appraisal History',NULL,175,1,',149,175,188,',1,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(189,'Team Appraisal History','/appraisalhistoryteam','Team Appraisal History','Team Appraisal History',NULL,175,2,',149,175,189,',1,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(190,'Asset Categories','/assets/assetcategories','Add category and sub cateegory for Assets','Add category and sub cateegory for Assets','',186,2,',186,190,',1,'assets',2,0,'','',0,'','',''),(191,'Category','/expenses/expensecategories','Add category and sub cateegory for Expenses','Add category and sub cateegory for Expenses','',185,2,',185,191,',1,'expenses',2,0,'','',0,'','',''),(192,'Payment Mode','/expenses/paymentmode','Add payment modes for Expenses','Add payment modes for Expenses','',185,3,',185,192,',1,'expenses',2,0,'','',0,'','',''),(193,'Receipts','/expenses/receipts','Add receipts for Expenses','Add receipts for Expenses','',185,4,',185,193,',1,'expenses',2,0,'','',0,'','',''),(194,'Trips','/expenses/trips','Add trips for Expenses','Add trips for Expenses','',185,5,',185,194,',1,'expenses',2,0,'','',0,'','',''),(195,'Advances','/expenses/advances','Add advance for Employ','Add advance for Employ','',185,6,',185,195,',1,'expenses',2,0,'','',0,'','',''),(196,'My Advances','/expenses/advances/myadvances','View list of my advances','View list of my advances','',195,7,',185,195,196,',1,'expenses',2,0,'','',0,'','',''),(197,'Employee Advances','/expenses/employeeadvances','View list of Employee advances','View list of Employee advances','',195,7,',185,195,197,',1,'expenses',2,0,'','',0,'','',''),(198,'Expenses','/expenses/expenses','Add Employee Expenses',NULL,NULL,185,1,',185,198,',1,'expenses',2,0,NULL,NULL,NULL,NULL,NULL,NULL),(199,'My Employee Expenses','/expenses/myemployeeexpenses','Submitted Employee Expenses',NULL,NULL,185,9,',185,199,',1,'expenses',2,0,NULL,NULL,NULL,NULL,NULL,NULL),(200,'Assets','/assets/assets','Assets',NULL,NULL,186,1,',186,200,',1,'assets',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(201,'Disciplinary','/#','','','',0,21,',21,',1,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(202,'Violation Type','/disciplinaryviolation','','','',201,1,',201,202,',1,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(203,'Raise An Incident','/disciplinaryincident','','','',201,2,',201,203,',1,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(204,'My Incidents','/disciplinarymyincidents','','','',201,3,',201,204,',1,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(205,'Team Incidents','/disciplinaryteamincidents','','','',201,4,',201,205,',1,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(206,'All Incidents','/disciplinaryallincidents','','','',201,5,',201,206,',1,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(207,'Contacts','/#','','','',3,8,',3,207',1,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(208,'Clients','/clients','','','',207,3,',3,207,208',1,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(209,'Projects','/projects','','','',207,4,',3,207,209',1,'default',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),( 210,'Exit Interview Questions','/exit/configureexitqs',NULL,NULL,NULL,'177','2',',3,177,210,','1','exit',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);

/*Table structure for table `main_militaryservice` */

DROP TABLE IF EXISTS `main_militaryservice`;

CREATE TABLE `main_militaryservice` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `militaryservicetype` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `createdby` int(11) unsigned DEFAULT NULL,
  `modifiedby` int(11) unsigned DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `isactive` tinyint(1) unsigned DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `main_militaryservice` */

/*Table structure for table `main_monthslist` */

DROP TABLE IF EXISTS `main_monthslist`;

CREATE TABLE `main_monthslist` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `month_id` bigint(20) unsigned DEFAULT NULL,
  `monthcode` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `createdby` int(11) unsigned DEFAULT NULL,
  `modifiedby` int(11) unsigned DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `isactive` tinyint(1) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=latin1;

/*Data for the table `main_monthslist` */

insert  into `main_monthslist`(`id`,`month_id`,`monthcode`,`description`,`createdby`,`modifiedby`,`createddate`,`modifieddate`,`isactive`) values (1,1,'Jan','January',1,1,'2014-01-21 11:46:13','2014-01-21 11:46:13',1),(2,2,'Feb','February',1,1,'2014-01-21 11:46:44','2014-01-21 11:46:44',1),(3,3,'Mar','March',1,1,'2014-01-21 11:47:10','2014-01-21 11:47:10',1),(4,4,'April','April',1,1,'2014-01-21 11:47:24','2014-01-21 11:47:24',1),(5,5,'May','May',1,1,'2014-01-21 11:47:40','2014-01-21 11:47:40',1),(6,6,'June','June',1,1,'2014-01-21 11:47:53','2014-01-21 11:47:53',1),(7,7,'July','July',1,1,'2014-01-21 11:48:04','2014-01-21 11:48:04',1),(8,8,'Aug','August',1,1,'2014-01-21 11:48:16','2014-01-21 11:48:16',1),(9,9,'Sep','September',1,1,'2014-01-21 11:48:28','2014-01-21 11:48:28',1),(10,10,'Oct','October',1,1,'2014-01-21 11:48:43','2014-01-21 11:48:43',1),(11,11,'Nov','November',1,1,'2014-01-21 11:48:53','2014-01-21 11:48:53',1),(12,12,'Dec','December',1,1,'2014-01-21 11:49:06','2014-01-21 11:49:06',1);

/*Table structure for table `main_nationality` */

DROP TABLE IF EXISTS `main_nationality`;

CREATE TABLE `main_nationality` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `nationalitycode` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `createdby` int(11) unsigned DEFAULT NULL,
  `modifiedby` int(11) unsigned DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `isactive` tinyint(1) unsigned DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

/*Data for the table `main_nationality` */

insert  into `main_nationality`(`id`,`nationalitycode`,`description`,`createdby`,`modifiedby`,`createddate`,`modifieddate`,`isactive`) values (1,'Arab','',1,1,'2016-11-02 11:14:11','2016-11-02 11:14:11',1),(2,'American','',1,1,'2016-11-02 11:14:11','2016-11-02 11:14:11',1),(3,'British','',1,1,'2016-11-02 11:14:11','2016-11-02 11:14:11',1),(4,'Canadian','',1,1,'2016-11-02 11:14:11','2016-11-02 11:14:11',1),(5,'Indian','',1,1,'2016-11-02 11:14:11','2016-11-02 11:14:11',1);

/*Table structure for table `main_nationalitycontextcode` */

DROP TABLE IF EXISTS `main_nationalitycontextcode`;

CREATE TABLE `main_nationalitycontextcode` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `nationalitycontextcode` varchar(255) DEFAULT NULL,
  `description` varchar(255) NOT NULL,
  `createdby` int(11) unsigned DEFAULT NULL,
  `modifiedby` int(11) unsigned DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `isactive` tinyint(1) unsigned DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `main_nationalitycontextcode` */

/*Table structure for table `main_numberformats` */

DROP TABLE IF EXISTS `main_numberformats`;

CREATE TABLE `main_numberformats` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `numberformattype` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `createdby` int(11) unsigned DEFAULT NULL,
  `modifiedby` int(11) unsigned DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `isactive` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `main_numberformats` */

/*Table structure for table `main_organisationinfo` */

DROP TABLE IF EXISTS `main_organisationinfo`;

CREATE TABLE `main_organisationinfo` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `organisationname` varchar(255) DEFAULT NULL,
  `org_image` varchar(255) DEFAULT NULL,
  `domain` varchar(255) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `orgdescription` text,
  `totalemployees` int(11) unsigned DEFAULT NULL,
  `registration_number` varchar(255) DEFAULT NULL,
  `org_startdate` date DEFAULT NULL,
  `phonenumber` varchar(255) DEFAULT NULL,
  `secondaryphone` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `secondaryemail` varchar(255) DEFAULT NULL,
  `faxnumber` varchar(255) DEFAULT NULL,
  `country` int(11) unsigned DEFAULT NULL,
  `state` int(11) unsigned DEFAULT NULL,
  `city` int(11) unsigned DEFAULT NULL,
  `address1` text,
  `address2` text,
  `address3` text,
  `description` text,
  `orghead` varchar(255) DEFAULT NULL,
  `designation` varchar(255) DEFAULT NULL,
  `createdby` int(11) unsigned DEFAULT NULL,
  `modifiedby` int(11) unsigned DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `isactive` tinyint(1) unsigned DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `main_organisationinfo` */

/*Table structure for table `main_pa_appraisalhistory` */

DROP TABLE IF EXISTS `main_pa_appraisalhistory`;

CREATE TABLE `main_pa_appraisalhistory` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint(20) unsigned DEFAULT NULL,
  `pa_initialization_id` bigint(20) unsigned DEFAULT NULL,
  `description` varchar(300) DEFAULT NULL,
  `desc_emp_id` bigint(20) unsigned DEFAULT NULL,
  `desc_emp_name` varchar(100) DEFAULT NULL,
  `desc_emp_profileimg` varchar(150) DEFAULT NULL,
  `createdby` bigint(20) unsigned DEFAULT NULL,
  `modifiedby` bigint(20) unsigned DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `isactive` tinyint(1) unsigned DEFAULT '1' COMMENT '1=active,0=inactive',
  PRIMARY KEY (`id`),
  KEY `NewIndex1` (`employee_id`),
  KEY `NewIndex2` (`pa_initialization_id`),
  KEY `NewIndex3` (`desc_emp_id`),
  KEY `NewIndex4` (`createdby`),
  KEY `NewIndex5` (`modifiedby`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='gives history of each employee''s appraisal';

/*Data for the table `main_pa_appraisalhistory` */

/*Table structure for table `main_pa_category` */

DROP TABLE IF EXISTS `main_pa_category`;

CREATE TABLE `main_pa_category` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `category_name` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `createdby` bigint(20) unsigned DEFAULT NULL,
  `modifiedby` bigint(20) unsigned DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `isactive` tinyint(1) DEFAULT '1',
  `isused` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `NewIndex1` (`createdby`),
  KEY `NewIndex2` (`modifiedby`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='This table is used to add questions category(Questionaire)';

/*Data for the table `main_pa_category` */

insert  into `main_pa_category`(`id`,`category_name`,`description`,`createdby`,`modifiedby`,`createddate`,`modifieddate`,`isactive`,`isused`) values (1,'KRA','Key Result Area',1,1,'2016-11-02 11:14:33','2016-11-02 11:14:33',1,1),(2,'KPI','Key Performance Index',1,1,'2016-11-02 11:14:33','2016-11-02 11:14:33',1,1);

/*Table structure for table `main_pa_employee_ratings` */

DROP TABLE IF EXISTS `main_pa_employee_ratings`;

CREATE TABLE `main_pa_employee_ratings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `pa_initialization_id` bigint(20) unsigned DEFAULT NULL,
  `employee_id` bigint(20) unsigned DEFAULT NULL,
  `employee_response` text COMMENT '{''Q1'':{''Comment'':''good'',''Rating'':''rating_id''},''Q2'':{''Comment'':''excellent'',''Rating'':''rating_id''}}',
  `manager_response` text COMMENT '{''Q1'':{''Comment'':''good'',''Rating'':''rating_id''},''Q2'':{''Comment'':''excellent'',''Rating'':''rating_id''}}',
  `skill_response` text COMMENT '{''skill_id'':''rating_id''}',
  `line_manager_1` bigint(20) DEFAULT NULL,
  `line_manager_2` bigint(20) DEFAULT NULL,
  `line_manager_3` bigint(20) DEFAULT NULL,
  `line_manager_4` bigint(20) DEFAULT NULL,
  `line_manager_5` bigint(20) DEFAULT NULL,
  `line_comment_1` text COMMENT '{''rating_id'':''comment''}',
  `line_comment_2` text COMMENT '{''rating_id'':''comment''}',
  `line_comment_3` text COMMENT '{''rating_id'':''comment''}',
  `line_comment_4` text,
  `line_comment_5` text,
  `line_rating_1` int(11) unsigned DEFAULT NULL,
  `line_rating_2` int(11) unsigned DEFAULT NULL,
  `line_rating_3` int(11) unsigned DEFAULT NULL,
  `line_rating_4` int(11) DEFAULT NULL,
  `line_rating_5` int(11) DEFAULT NULL,
  `consolidated_rating` float(10,2) DEFAULT NULL COMMENT 'Consolidated rating.Need to be updated after each manager rating.',
  `appraisal_status` enum('Pending employee ratings','Pending L1 ratings','Pending L2 ratings','Pending L3 ratings','Pending L4 ratings','Pending L5 ratings','Completed') DEFAULT 'Pending employee ratings' COMMENT '1=Pending employee ratings,2=Pending L1 ratings,3=Pending L2 ratings,4=Pending L3 ratings,5=Pending L4 ratings,6=Pending L5 ratings,7=Completed,',
  `createdby` bigint(20) unsigned DEFAULT NULL,
  `createdby_role` bigint(20) unsigned DEFAULT NULL,
  `createdby_group` bigint(20) unsigned DEFAULT NULL,
  `modifiedby` bigint(20) unsigned DEFAULT NULL,
  `modifiedby_role` bigint(20) unsigned DEFAULT NULL,
  `modifiedby_group` bigint(20) unsigned DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `isactive` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `NewIndex1` (`pa_initialization_id`),
  KEY `NewIndex2` (`employee_id`),
  CONSTRAINT `FK_main_pa_employee_ratings` FOREIGN KEY (`pa_initialization_id`) REFERENCES `main_pa_initialization` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='Employee appraisal response is stored in this table';

/*Data for the table `main_pa_employee_ratings` */

/*Table structure for table `main_pa_ff_employee_ratings` */

DROP TABLE IF EXISTS `main_pa_ff_employee_ratings`;

CREATE TABLE `main_pa_ff_employee_ratings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ff_initialization_id` bigint(20) unsigned DEFAULT NULL,
  `manager_id` bigint(20) DEFAULT NULL,
  `employee_id` bigint(20) unsigned DEFAULT NULL,
  `question_ids` text,
  `employee_response` text COMMENT '{''Q1'':{''Comment'':''good'',''Rating'':''rating_id''},''Q2'':{''Comment'':''excellent'',''Rating'':''rating_id''}}',
  `ff_status` enum('Pending employee ratings','Completed') DEFAULT NULL,
  `consolidated_rating` float(10,2) DEFAULT NULL,
  `additional_comments` text,
  `createdby` bigint(20) unsigned DEFAULT NULL,
  `createdby_role` bigint(20) unsigned DEFAULT NULL,
  `createdby_group` bigint(20) unsigned DEFAULT NULL,
  `modifiedby` bigint(20) unsigned DEFAULT NULL,
  `modifiedby_role` bigint(20) unsigned DEFAULT NULL,
  `modifiedby_group` bigint(20) unsigned DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `isactive` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `main_pa_ff_employee_ratings` */

/*Table structure for table `main_pa_ff_history` */

DROP TABLE IF EXISTS `main_pa_ff_history`;

CREATE TABLE `main_pa_ff_history` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint(20) unsigned DEFAULT NULL,
  `pa_ff_initialization_id` bigint(20) DEFAULT NULL,
  `description` varchar(300) DEFAULT NULL,
  `desc_emp_id` bigint(20) unsigned DEFAULT NULL,
  `desc_emp_name` varchar(100) DEFAULT NULL,
  `desc_emp_profileimg` varchar(150) DEFAULT NULL,
  `createdby` bigint(20) unsigned DEFAULT NULL,
  `modifiedby` bigint(20) unsigned DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `isactive` tinyint(1) unsigned DEFAULT '1' COMMENT '1=active,0=inactive',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='gives step by step history of feedforward';

/*Data for the table `main_pa_ff_history` */

/*Table structure for table `main_pa_ff_initialization` */

DROP TABLE IF EXISTS `main_pa_ff_initialization`;

CREATE TABLE `main_pa_ff_initialization` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `pa_configured_id` bigint(20) unsigned DEFAULT NULL COMMENT 'Management configuration with module_flag=2',
  `businessunit_id` bigint(20) unsigned DEFAULT NULL,
  `department_id` bigint(20) unsigned DEFAULT NULL,
  `ff_mode` enum('Quarterly','Half-yearly','Yearly') DEFAULT NULL,
  `ff_period` tinyint(1) unsigned DEFAULT NULL COMMENT '1,2,3,4 for quaterly, 1.2 for half-yearly , 1 for yearly',
  `ff_from_year` int(11) DEFAULT NULL,
  `ff_to_year` int(11) DEFAULT NULL,
  `ff_due_date` date DEFAULT NULL,
  `appraisal_id` bigint(20) unsigned DEFAULT NULL COMMENT 'Latest appraisal Id',
  `employee_name_view` tinyint(1) unsigned DEFAULT NULL COMMENT '0=Hide,1=Show',
  `enable_to` tinyint(1) DEFAULT NULL COMMENT '0=Appraisal Employees,1=All Employees',
  `initialize_status` tinyint(1) unsigned DEFAULT NULL COMMENT '1=initlaize,2=initialize later',
  `status` tinyint(1) unsigned DEFAULT NULL COMMENT '1=open, 2= close ,Appraisal staus for the particular period',
  `questions` text,
  `qs_privileges` text,
  `isactive` tinyint(1) unsigned DEFAULT NULL COMMENT '1=active,0=inactive',
  `createdby` bigint(20) unsigned DEFAULT NULL,
  `createdby_role` bigint(20) unsigned DEFAULT NULL,
  `createdby_group` bigint(20) unsigned DEFAULT NULL,
  `modifiedby` bigint(20) unsigned DEFAULT NULL,
  `modifiedby_role` bigint(20) unsigned DEFAULT NULL,
  `modifiedby_group` bigint(20) unsigned DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='initialize feedforward by management';

/*Data for the table `main_pa_ff_initialization` */

/*Table structure for table `main_pa_groups` */

DROP TABLE IF EXISTS `main_pa_groups`;

CREATE TABLE `main_pa_groups` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `pa_initialization_id` bigint(20) unsigned DEFAULT NULL,
  `group_name` varchar(250) DEFAULT NULL,
  `isactive` tinyint(1) unsigned DEFAULT NULL COMMENT '1=active,0=inactive',
  `createdby` bigint(20) unsigned DEFAULT NULL,
  `createdby_role` bigint(20) unsigned DEFAULT NULL,
  `createdby_group` bigint(20) unsigned DEFAULT NULL,
  `modifiedby` bigint(20) unsigned DEFAULT NULL,
  `modifiedby_role` bigint(20) unsigned DEFAULT NULL,
  `modifiedby_group` bigint(20) unsigned DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='divide all employees into groups for easy identification for';

/*Data for the table `main_pa_groups` */

/*Table structure for table `main_pa_groups_employees` */

DROP TABLE IF EXISTS `main_pa_groups_employees`;

CREATE TABLE `main_pa_groups_employees` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `pa_initialization_id` bigint(20) unsigned DEFAULT NULL,
  `group_id` bigint(20) unsigned DEFAULT NULL,
  `employee_ids` text COMMENT 'comma separated employee ids',
  `isactive` tinyint(1) unsigned DEFAULT NULL COMMENT '1=active,0=inactive',
  `createdby` bigint(20) unsigned DEFAULT NULL,
  `createdby_role` bigint(20) unsigned DEFAULT NULL,
  `createdby_group` bigint(20) unsigned DEFAULT NULL,
  `modifiedby` bigint(20) unsigned DEFAULT NULL,
  `modifiedby_role` bigint(20) unsigned DEFAULT NULL,
  `modifiedby_group` bigint(20) unsigned DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `main_pa_groups_employees` */

/*Table structure for table `main_pa_groups_employees_temp` */

DROP TABLE IF EXISTS `main_pa_groups_employees_temp`;

CREATE TABLE `main_pa_groups_employees_temp` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `pa_initialization_id` bigint(20) unsigned DEFAULT NULL,
  `group_id` bigint(20) unsigned DEFAULT NULL,
  `employee_ids` text COMMENT 'comma separated employee ids',
  `isactive` tinyint(1) unsigned DEFAULT NULL COMMENT '1=active,0=inactive',
  `createdby` bigint(20) unsigned DEFAULT NULL,
  `createdby_role` bigint(20) unsigned DEFAULT NULL,
  `createdby_group` bigint(20) unsigned DEFAULT NULL,
  `modifiedby` bigint(20) unsigned DEFAULT NULL,
  `modifiedby_role` bigint(20) unsigned DEFAULT NULL,
  `modifiedby_group` bigint(20) unsigned DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `main_pa_groups_employees_temp` */

/*Table structure for table `main_pa_implementation` */

DROP TABLE IF EXISTS `main_pa_implementation`;

CREATE TABLE `main_pa_implementation` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `businessunit_id` bigint(20) unsigned DEFAULT NULL,
  `department_id` bigint(20) unsigned DEFAULT NULL,
  `performance_app_flag` tinyint(1) unsigned DEFAULT NULL COMMENT '1=bu wise,0=dept wise',
  `appraisal_mode` enum('Quarterly','Half-yearly','Yearly') DEFAULT NULL,
  `approval_selection` tinyint(1) unsigned DEFAULT NULL COMMENT '1=HR,2=Manager',
  `appraisal_ratings` tinyint(1) unsigned DEFAULT NULL COMMENT '1=1-5,2=1-10',
  `module_flag` tinyint(1) unsigned DEFAULT NULL COMMENT '1=Performance appraisals,2=Feedforward',
  `isactive` tinyint(1) unsigned DEFAULT NULL COMMENT '0=inactive,1=active,2=delete',
  `createdby` bigint(20) unsigned DEFAULT NULL,
  `modifiedby` bigint(20) unsigned DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='stores configuration of appraisal process';

/*Data for the table `main_pa_implementation` */

/*Table structure for table `main_pa_initialization` */

DROP TABLE IF EXISTS `main_pa_initialization`;

CREATE TABLE `main_pa_initialization` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'primary key',
  `pa_configured_id` bigint(20) unsigned DEFAULT NULL COMMENT 'id of main_pa_implementation',
  `businessunit_id` bigint(20) unsigned DEFAULT NULL COMMENT 'id of business unit',
  `department_id` bigint(20) unsigned DEFAULT NULL COMMENT 'id of department else null',
  `enable_step` tinyint(1) unsigned DEFAULT NULL COMMENT '0=No,1=Managers,2=Employees',
  `appraisal_mode` enum('Yearly','Half-yearly','Quarterly') DEFAULT NULL COMMENT 'mode of appraisal',
  `appraisal_period` tinyint(1) unsigned DEFAULT NULL COMMENT '1,2,3,4 for quaterly, 1.2 for half yearly , 1 for yearly',
  `eligibility` varchar(40) DEFAULT NULL COMMENT 'comma separated employment status ids',
  `from_year` int(11) unsigned DEFAULT NULL COMMENT 'financial year -start',
  `to_year` int(11) unsigned DEFAULT NULL COMMENT 'financial year-end',
  `managers_due_date` date DEFAULT NULL COMMENT 'due date for enable to managers',
  `employees_due_date` date DEFAULT NULL COMMENT 'due date for enable to employees',
  `category_id` varchar(250) DEFAULT NULL COMMENT 'comma separated question category ids',
  `initialize_status` tinyint(1) unsigned DEFAULT NULL COMMENT '1=initlaize,2=initialize later',
  `status` tinyint(1) unsigned DEFAULT NULL COMMENT '1=open, 2= close , 3= Force Close.Appraisal staus for the particular period',
  `pa_group_ids` text COMMENT 'Comma separated group ids',
  `manager_ids` text COMMENT 'Comma separated manager ids,Whenever manager completes initalization his id has to be appended',
  `manager_level_type` tinyint(1) unsigned DEFAULT NULL COMMENT '1=configure l1,2=use reporting mangers',
  `comments` text COMMENT 'To capture hr comments if forceful close of initialization',
  `group_settings` tinyint(1) DEFAULT '0' COMMENT '0=default screen,1= all, 2=groupwise',
  `employee_response` tinyint(1) DEFAULT '1' COMMENT '1-No response,2-Response',
  `appraisal_ratings` tinyint(1) unsigned DEFAULT NULL COMMENT 'same value as from implementation',
  `management_appraisal` tinyint(1) unsigned DEFAULT '0' COMMENT '1=management included,0=no management',
  `isactive` tinyint(1) unsigned DEFAULT NULL COMMENT '0=inactive,1=active',
  `performance_app_flag` tinyint(4) DEFAULT NULL,
  `createdby` bigint(20) unsigned DEFAULT NULL COMMENT 'id of created user',
  `createdby_role` bigint(20) unsigned DEFAULT NULL COMMENT 'role of created user',
  `createdby_group` bigint(20) unsigned DEFAULT NULL COMMENT 'group of created user',
  `modifiedby` bigint(20) unsigned DEFAULT NULL COMMENT 'id of user modified by',
  `modifiedby_role` bigint(20) unsigned DEFAULT NULL COMMENT 'role of user modified by',
  `modifiedby_group` bigint(20) unsigned DEFAULT NULL COMMENT 'group of user modified by',
  `createddate` datetime DEFAULT NULL COMMENT 'created date',
  `modifieddate` datetime DEFAULT NULL COMMENT 'modified date',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='initialize appriasal process';

/*Data for the table `main_pa_initialization` */

/*Table structure for table `main_pa_manager_initialization` */

DROP TABLE IF EXISTS `main_pa_manager_initialization`;

CREATE TABLE `main_pa_manager_initialization` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `pa_initialization_id` bigint(20) unsigned DEFAULT NULL,
  `selected_employee_ids` text COMMENT 'Comma separated employee ids selected during initialization',
  `approver_level` bigint(20) unsigned DEFAULT NULL,
  `approver_1_id` bigint(20) unsigned DEFAULT NULL,
  `approver_2_id` bigint(20) unsigned DEFAULT NULL,
  `approver_3_id` bigint(20) unsigned DEFAULT NULL,
  `createdby` bigint(20) unsigned DEFAULT NULL,
  `createdby_role` bigint(20) unsigned DEFAULT NULL,
  `createdby_group` bigint(20) unsigned DEFAULT NULL,
  `modifiedby` bigint(20) unsigned DEFAULT NULL,
  `modifiedby_role` bigint(20) unsigned DEFAULT NULL,
  `modifiedby_group` bigint(20) unsigned DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `isactive` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='Manager initialized data is stored in this table';

/*Data for the table `main_pa_manager_initialization` */

/*Table structure for table `main_pa_questions` */

DROP TABLE IF EXISTS `main_pa_questions`;

CREATE TABLE `main_pa_questions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `pa_category_id` bigint(20) unsigned DEFAULT NULL,
  `question` varchar(500) DEFAULT NULL,
  `description` varchar(500) DEFAULT NULL,
  `module_flag` tinyint(1) unsigned DEFAULT NULL COMMENT '1=Performance appraisals,2=Feedforward',
  `isactive` tinyint(1) unsigned DEFAULT NULL COMMENT '0=inactive,1=active',
  `createdby` bigint(20) unsigned DEFAULT NULL,
  `createdby_role` bigint(20) unsigned DEFAULT NULL,
  `createdby_group` bigint(20) unsigned DEFAULT NULL,
  `modifiedby` bigint(20) unsigned DEFAULT NULL,
  `modifiedby_role` bigint(20) unsigned DEFAULT NULL,
  `modifiedby_group` bigint(20) unsigned DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `isused` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='stores questions for appraisal and feedforward';

/*Data for the table `main_pa_questions` */

/*Table structure for table `main_pa_questions_privileges` */

DROP TABLE IF EXISTS `main_pa_questions_privileges`;

CREATE TABLE `main_pa_questions_privileges` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `pa_initialization_id` bigint(20) unsigned DEFAULT NULL COMMENT 'Appraisal initialization id for appraisal else feedforward initialization id for feedforward',
  `group_id` bigint(20) unsigned DEFAULT NULL,
  `employee_id` bigint(20) unsigned DEFAULT NULL COMMENT 'Null for feedforward else employee id',
  `hr_qs` text COMMENT 'Comma separated question ids created by hr',
  `hr_group_qs_privileges` text COMMENT '{''Q1'':{''Manager Comments'':1,''Employee Comments'':1,''Manager Ratings'':1,''Employee Ratings'':1}}',
  `manager_group_id` bigint(20) unsigned DEFAULT NULL COMMENT 'group id of employees created by manager',
  `manager_qs` text COMMENT 'Comma separeated question ids created by manager',
  `manager_qs_privileges` text COMMENT '{''Q1'':{''Manager Comments'':1,''Employee Comments'':1,''Manager Ratings'':1,''Employee Ratings'':1}}',
  `ff_qs` text COMMENT 'Comma separeated question ids created by management',
  `ff_qs_privileges` text COMMENT '{''Q1'':{''Employee Comments'':1,''Employee Ratings'':1}}',
  `module_flag` tinyint(1) DEFAULT NULL COMMENT '1=Performance appraisals,2=Feedforward',
  `line_manager_1` bigint(20) DEFAULT NULL COMMENT 'Line 1 manager id',
  `line_manager_2` bigint(20) DEFAULT NULL COMMENT 'Line 2 manager id',
  `line_manager_3` bigint(20) DEFAULT NULL COMMENT 'Line 3 manager id',
  `line_manager_4` bigint(20) DEFAULT NULL COMMENT 'Line 4 manager id',
  `line_manager_5` bigint(20) DEFAULT NULL COMMENT 'Line 5 manager id',
  `manager_levels` tinyint(1) unsigned DEFAULT NULL COMMENT 'no.of levels of appraisal',
  `createdby` bigint(20) unsigned DEFAULT NULL,
  `createdby_role` bigint(20) unsigned DEFAULT NULL,
  `createdby_group` bigint(20) unsigned DEFAULT NULL,
  `modifiedby` bigint(20) unsigned DEFAULT NULL,
  `modifiedby_role` bigint(20) unsigned DEFAULT NULL,
  `modifiedby_group` bigint(20) unsigned DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `isactive` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='Stores the privileges of questions for each employee in json';

/*Data for the table `main_pa_questions_privileges` */

/*Table structure for table `main_pa_questions_privileges_temp` */

DROP TABLE IF EXISTS `main_pa_questions_privileges_temp`;

CREATE TABLE `main_pa_questions_privileges_temp` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `pa_initialization_id` bigint(20) unsigned DEFAULT NULL COMMENT 'Appraisal initialization id for appraisal else feedforward initialization id for feedforward',
  `group_id` bigint(20) unsigned DEFAULT NULL,
  `employee_id` bigint(20) unsigned DEFAULT NULL COMMENT 'id of the employee',
  `hr_qs` text COMMENT 'Comma separated question ids created by hr',
  `hr_group_qs_privileges` text COMMENT '{''groupid:''{''Q1'':{''Manager Comments'':1,''Employee Comments'':1,''Manager Ratings'':1,''Employee Ratings'':1}}}',
  `line_manager_1` bigint(20) DEFAULT NULL COMMENT 'Line 1 reporting manager',
  `line_manager_2` bigint(20) DEFAULT NULL COMMENT 'Line 2 reporting manager',
  `line_manager_3` bigint(20) DEFAULT NULL COMMENT 'Line 3 reporting manager',
  `line_manager_4` bigint(20) DEFAULT NULL COMMENT 'Line 4 reporting manager',
  `line_manager_5` bigint(20) DEFAULT NULL COMMENT 'Line 5 reporting manager',
  `manager_levels` tinyint(1) unsigned DEFAULT NULL COMMENT 'no.of levels of appraisal',
  `module_flag` tinyint(1) unsigned DEFAULT NULL COMMENT '1=performance appraisal,2=feedforward',
  `createdby` bigint(20) unsigned DEFAULT NULL,
  `createdby_role` bigint(20) unsigned DEFAULT NULL,
  `createdby_group` bigint(20) unsigned DEFAULT NULL,
  `modifiedby` bigint(20) unsigned DEFAULT NULL,
  `modifiedby_role` bigint(20) unsigned DEFAULT NULL,
  `modifiedby_group` bigint(20) unsigned DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `isactive` tinyint(1) unsigned DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='Stores the questions, privileges and groups when initialized';

/*Data for the table `main_pa_questions_privileges_temp` */

/*Table structure for table `main_pa_ratings` */

DROP TABLE IF EXISTS `main_pa_ratings`;

CREATE TABLE `main_pa_ratings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `pa_configured_id` bigint(20) unsigned DEFAULT NULL,
  `pa_initialization_id` bigint(20) DEFAULT NULL,
  `rating_type` tinyint(1) DEFAULT '1' COMMENT '1=(1-5),2=(1-10)',
  `rating_value` int(11) unsigned DEFAULT NULL COMMENT 'Rating value',
  `rating_text` varchar(100) DEFAULT NULL COMMENT 'Rating text',
  `createdby` bigint(20) unsigned DEFAULT NULL,
  `createdby_role` bigint(20) unsigned DEFAULT NULL,
  `createdby_group` bigint(20) unsigned DEFAULT NULL,
  `modifiedby` bigint(20) unsigned DEFAULT NULL,
  `modifiedby_role` bigint(20) unsigned DEFAULT NULL,
  `modifiedby_group` bigint(20) unsigned DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `isactive` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='This table is used to add ratings text in json format.';

/*Data for the table `main_pa_ratings` */

/*Table structure for table `main_pa_skills` */

DROP TABLE IF EXISTS `main_pa_skills`;

CREATE TABLE `main_pa_skills` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `skill_name` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `createdby` bigint(20) unsigned DEFAULT NULL,
  `createdby_role` bigint(20) unsigned DEFAULT NULL,
  `createdby_group` bigint(20) unsigned DEFAULT NULL,
  `modifiedby` bigint(20) unsigned DEFAULT NULL,
  `modifiedby_role` bigint(20) unsigned DEFAULT NULL,
  `modifiedby_group` bigint(20) unsigned DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `isactive` tinyint(1) DEFAULT '1',
  `isused` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='This table is used to add skills.';

/*Data for the table `main_pa_skills` */

/*Table structure for table `main_patches_version` */

DROP TABLE IF EXISTS `main_patches_version`;

CREATE TABLE `main_patches_version` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` varchar(255) DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `isactive` tinyint(1) DEFAULT '1' COMMENT '1=latest versions,0=old versions',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

/*Data for the table `main_patches_version` */

insert  into `main_patches_version`(`id`,`version`,`createddate`,`modifieddate`,`isactive`) values (1,'3.2',NOW(),NOW(),1);

/*Table structure for table `main_payfrequency` */

DROP TABLE IF EXISTS `main_payfrequency`;

CREATE TABLE `main_payfrequency` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `freqtype` varchar(255) NOT NULL,
  `freqcode` varchar(100) DEFAULT NULL,
  `freqdescription` varchar(255) DEFAULT NULL,
  `createdby` int(11) unsigned DEFAULT NULL,
  `modifiedby` int(11) unsigned DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `isactive` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `main_payfrequency` */

/*Table structure for table `main_pd_categories` */

DROP TABLE IF EXISTS `main_pd_categories`;

CREATE TABLE `main_pd_categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `category` varchar(200) NOT NULL,
  `description` text,
  `isused` tinyint(4) DEFAULT NULL,
  `isactive` tinyint(4) DEFAULT NULL,
  `modifiedby` bigint(20) DEFAULT NULL,
  `createdby` bigint(20) DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `main_pd_categories` */

/*Table structure for table `main_pd_documents` */

DROP TABLE IF EXISTS `main_pd_documents`;

CREATE TABLE `main_pd_documents` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` bigint(20) DEFAULT NULL,
  `subcategory_id` bigint(20) unsigned DEFAULT NULL COMMENT 'not used ',
  `document_name` varchar(500) NOT NULL,
  `document_version` varchar(100) DEFAULT NULL,
  `description` text,
  `file_name` text,
  `isactive` tinyint(4) DEFAULT NULL,
  `modifiedby` bigint(20) unsigned DEFAULT NULL,
  `createdby` bigint(20) unsigned DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `flag1` varchar(100) DEFAULT NULL,
  `flag2` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `main_pd_documents` */

/*Table structure for table `main_positions` */

DROP TABLE IF EXISTS `main_positions`;

CREATE TABLE `main_positions` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `positionname` varchar(100) DEFAULT NULL,
  `jobtitleid` int(11) unsigned DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `createdby` int(11) DEFAULT NULL,
  `modifiedby` int(11) DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `isactive` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `main_positions` */

/*Table structure for table `main_prefix` */

DROP TABLE IF EXISTS `main_prefix`;

CREATE TABLE `main_prefix` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `prefix` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `createdby` int(11) unsigned DEFAULT NULL,
  `modifiedby` int(11) unsigned DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `isactive` tinyint(1) unsigned DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

/*Data for the table `main_prefix` */

insert  into `main_prefix`(`id`,`prefix`,`description`,`createdby`,`modifiedby`,`createddate`,`modifieddate`,`isactive`) values (1,'Mr','',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(2,'Ms','',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(3,'Mrs','',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1);

/*Table structure for table `main_privileges` */

DROP TABLE IF EXISTS `main_privileges`;

CREATE TABLE `main_privileges` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `role` int(11) unsigned DEFAULT NULL,
  `group_id` int(11) unsigned DEFAULT NULL,
  `object` int(11) unsigned DEFAULT NULL,
  `addpermission` varchar(10) DEFAULT NULL,
  `editpermission` varchar(10) DEFAULT NULL,
  `deletepermission` varchar(10) DEFAULT NULL,
  `viewpermission` varchar(10) DEFAULT NULL,
  `uploadattachments` varchar(10) DEFAULT NULL,
  `viewattachments` varchar(10) DEFAULT NULL,
  `createdby` int(11) unsigned DEFAULT NULL,
  `modifiedby` int(11) unsigned DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `isactive` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1184 DEFAULT CHARSET=latin1;

/*Data for the table `main_privileges` */

insert  into `main_privileges`(`id`,`role`,`group_id`,`object`,`addpermission`,`editpermission`,`deletepermission`,`viewpermission`,`uploadattachments`,`viewattachments`,`createdby`,`modifiedby`,`createddate`,`modifieddate`,`isactive`) values (1,1,NULL,1,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(2,1,NULL,2,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(3,1,NULL,3,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(4,1,NULL,4,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(5,1,NULL,5,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(6,1,NULL,8,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(7,1,NULL,9,'Yes','Yes','No','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(8,1,NULL,10,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(9,1,NULL,11,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(10,1,NULL,12,'No','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(11,1,NULL,13,'Yes','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(12,1,NULL,14,'Yes','Yes','No','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(13,1,NULL,16,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(14,1,NULL,17,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(15,1,NULL,19,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(16,1,NULL,20,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(17,1,NULL,21,'Yes','Yes','No','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(18,1,NULL,22,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(19,1,NULL,23,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(20,1,NULL,24,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(21,1,NULL,31,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(22,1,NULL,32,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(23,1,NULL,34,'No','No','No','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(24,1,NULL,41,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(25,1,NULL,42,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(26,1,NULL,43,'No','No','No','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(27,1,NULL,44,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(28,1,NULL,45,'No','No','No','Yes','No','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(29,1,NULL,54,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(30,1,NULL,55,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(31,1,NULL,56,'No','Yes','No','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(32,1,NULL,57,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(33,1,NULL,61,'Yes','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(34,1,NULL,62,'No','No','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(35,1,NULL,63,'No','No','No','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(36,1,NULL,64,'No','No','No','Yes','No','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(37,1,NULL,65,'No','Yes','No','Yes','No','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(38,1,NULL,68,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(39,1,NULL,69,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(40,1,NULL,70,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(41,1,NULL,72,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(42,1,NULL,73,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(43,1,NULL,75,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(44,1,NULL,80,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(45,1,NULL,85,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(46,1,NULL,86,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(47,1,NULL,87,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(48,1,NULL,88,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(49,1,NULL,89,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(50,1,NULL,90,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(51,1,NULL,91,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(52,1,NULL,92,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(53,1,NULL,93,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(54,1,NULL,100,'Yes','No','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(55,1,NULL,101,'Yes','No','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(56,1,NULL,102,'Yes','No','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(57,1,NULL,103,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(58,1,NULL,107,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(59,1,NULL,108,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(60,1,NULL,110,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(61,1,NULL,111,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(62,1,NULL,113,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(63,1,NULL,114,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(64,1,NULL,115,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(65,1,NULL,116,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(66,1,NULL,117,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(67,1,NULL,118,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(68,1,NULL,120,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(69,1,NULL,121,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(70,1,NULL,123,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(71,1,NULL,124,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(72,1,NULL,125,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(73,1,NULL,126,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(74,1,NULL,127,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(75,1,NULL,128,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(76,1,NULL,131,'Yes','Yes','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(77,1,NULL,132,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(78,1,NULL,133,'Yes','Yes','No','No','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(79,1,NULL,134,'No','Yes','No','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(80,1,NULL,135,'No','No','No','Yes','No','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(81,1,NULL,136,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(82,1,NULL,138,'No','No','No','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(83,1,NULL,139,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(84,1,NULL,140,'No','Yes','No','No','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(85,1,NULL,141,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(86,1,NULL,142,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(87,1,NULL,143,'Yes','Yes','Yes','Yes','Yes','Yes',0,0,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(88,1,NULL,144,'Yes','Yes','Yes','Yes','Yes','Yes',0,0,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(89,1,NULL,145,'Yes','Yes','Yes','Yes','Yes','Yes',0,0,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(90,1,NULL,146,'Yes','Yes','Yes','Yes','Yes','Yes',0,0,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(91,1,NULL,147,'Yes','Yes','Yes','Yes','Yes','Yes',0,0,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(92,1,NULL,149,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(93,1,NULL,150,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(94,1,NULL,151,'Yes','No','No','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(95,1,NULL,152,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(96,1,NULL,154,'Yes','Yes','No','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(97,1,NULL,155,'Yes','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(98,1,NULL,158,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(99,1,NULL,159,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(100,1,NULL,160,'Yes','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(101,1,NULL,161,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(102,1,NULL,162,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(103,1,NULL,163,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(104,1,NULL,166,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(105,1,NULL,167,'Yes','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(106,1,NULL,168,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(107,1,NULL,169,'No','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(108,1,NULL,170,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(109,1,NULL,171,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(110,1,NULL,172,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(111,1,NULL,174,'No','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(112,NULL,1,1,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(113,NULL,1,2,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(114,NULL,1,3,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(115,NULL,1,4,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(116,NULL,1,5,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(117,NULL,1,8,'No','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(118,NULL,1,9,'No','Yes','No','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(119,NULL,1,10,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(120,NULL,1,11,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(121,NULL,1,12,'No','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(122,NULL,1,13,'No','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(123,NULL,1,14,'Yes','Yes','No','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(124,NULL,1,16,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(125,NULL,1,17,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(126,NULL,1,19,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(127,NULL,1,20,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(128,NULL,1,21,'Yes','Yes','No','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(129,NULL,1,22,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(130,NULL,1,23,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(131,NULL,1,24,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(132,NULL,1,31,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(133,NULL,1,32,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(134,NULL,1,34,'No','No','No','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(135,NULL,1,41,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(136,NULL,1,42,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(137,NULL,1,43,'No','No','No','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(138,NULL,1,44,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(139,NULL,1,45,'No','No','No','Yes','No','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(140,NULL,1,54,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(141,NULL,1,55,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(142,NULL,1,56,'No','Yes','No','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(143,NULL,1,57,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(144,NULL,1,61,'Yes','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(145,NULL,1,62,'No','No','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(146,NULL,1,63,'No','No','No','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(147,NULL,1,64,'No','No','No','Yes','No','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(148,NULL,1,65,'No','Yes','No','Yes','No','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(149,NULL,1,68,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(150,NULL,1,69,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(151,NULL,1,70,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(152,NULL,1,72,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(153,NULL,1,73,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(154,NULL,1,75,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(155,NULL,1,80,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(156,NULL,1,85,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(157,NULL,1,86,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(158,NULL,1,87,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(159,NULL,1,88,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(160,NULL,1,89,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(161,NULL,1,90,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(162,NULL,1,91,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(163,NULL,1,92,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(164,NULL,1,93,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(165,NULL,1,100,'Yes','No','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(166,NULL,1,101,'Yes','No','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(167,NULL,1,102,'Yes','No','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(168,NULL,1,103,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(169,NULL,1,107,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(170,NULL,1,108,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(171,NULL,1,110,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(172,NULL,1,111,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(173,NULL,1,113,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(174,NULL,1,114,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(175,NULL,1,115,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(176,NULL,1,116,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(177,NULL,1,117,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(178,NULL,1,118,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(179,NULL,1,120,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(180,NULL,1,121,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(181,NULL,1,123,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(182,NULL,1,124,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(183,NULL,1,125,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(184,NULL,1,126,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(185,NULL,1,127,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(186,NULL,1,128,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(187,NULL,1,131,'Yes','Yes','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(188,NULL,1,132,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(189,NULL,1,133,'No','Yes','No','No','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(190,NULL,1,134,'No','Yes','No','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(191,NULL,1,135,'No','No','No','Yes','No','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(192,NULL,1,136,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(193,NULL,1,138,'No','No','No','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(194,NULL,1,139,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(195,NULL,1,140,'No','Yes','No','No','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(196,NULL,1,141,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(197,NULL,1,143,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(198,NULL,1,144,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(199,NULL,1,145,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(200,NULL,1,146,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(201,NULL,1,147,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(202,NULL,1,148,'No','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(203,NULL,1,149,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(204,NULL,1,150,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(205,NULL,1,151,'Yes','No','No','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(206,NULL,1,152,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(207,NULL,1,154,'Yes','Yes','No','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(208,NULL,1,155,'Yes','Yes','No','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(209,NULL,1,158,'No','No','No','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(210,NULL,1,159,'No','No','No','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(211,NULL,1,160,'Yes','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(212,NULL,1,161,'No','Yes','No','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(213,NULL,1,162,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(214,NULL,1,163,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(215,NULL,1,166,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(216,NULL,1,167,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(217,NULL,1,168,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(218,NULL,1,169,'No','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(219,NULL,1,170,'No','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(220,NULL,1,171,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(221,NULL,1,172,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(222,NULL,1,174,'No','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(223,NULL,2,1,'No','No','No','Yes','No','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(224,NULL,2,3,'No','No','No','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(225,NULL,2,4,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(226,NULL,2,9,'No','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(227,NULL,2,10,'No','No','No','Yes','No','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(228,NULL,2,11,'No','No','No','Yes','No','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(229,NULL,2,12,'No','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(230,NULL,2,13,'No','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(231,NULL,2,14,'No','No','No','Yes','No','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(232,NULL,2,19,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(233,NULL,2,31,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(234,NULL,2,32,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(235,NULL,2,34,'Yes','Yes','No','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(236,NULL,2,43,'No','No','No','Yes','No','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(237,NULL,2,54,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(238,NULL,2,55,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(239,NULL,2,56,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(240,NULL,2,57,'No','Yes','No','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(241,NULL,2,61,'Yes','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(242,NULL,2,62,'No','No','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(243,NULL,2,63,'No','No','No','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(244,NULL,2,64,'No','No','No','Yes','No','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(245,NULL,2,65,'No','Yes','No','Yes','No','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(246,NULL,2,134,'No','No','No','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(247,NULL,2,135,'No','No','No','Yes','No','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(248,NULL,2,138,'No','No','No','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(249,NULL,2,143,'No','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(250,NULL,2,148,'No','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(251,NULL,2,149,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(252,NULL,2,150,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(253,NULL,2,151,'Yes','No','No','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(254,NULL,2,152,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(255,NULL,2,161,'NO','Yes','NO','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(256,NULL,2,162,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(257,NULL,2,163,'No','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(258,NULL,2,168,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(259,NULL,2,169,'No','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(260,NULL,2,170,'No','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(261,NULL,2,174,'No','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(262,NULL,3,1,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(263,NULL,3,2,'Yes','Yes','No','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(264,NULL,3,3,'Yes','Yes','No','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(265,NULL,3,4,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(266,NULL,3,5,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(267,NULL,3,8,'No','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(268,NULL,3,9,'No','Yes','No','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(269,NULL,3,10,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(270,NULL,3,11,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(271,NULL,3,12,'No','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(272,NULL,3,13,'No','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(273,NULL,3,14,'Yes','Yes','No','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(274,NULL,3,16,'Yes','Yes','No','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(275,NULL,3,17,'Yes','Yes','No','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(276,NULL,3,19,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(277,NULL,3,21,'Yes','Yes','No','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(278,NULL,3,22,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(279,NULL,3,23,'Yes','Yes','No','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(280,NULL,3,31,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(281,NULL,3,32,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(282,NULL,3,34,'No','No','No','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(283,NULL,3,41,'Yes','Yes','No','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(284,NULL,3,42,'Yes','Yes','No','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(285,NULL,3,43,'No','No','No','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(286,NULL,3,44,'Yes','Yes','No','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(287,NULL,3,45,'No','No','No','Yes','No','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(288,NULL,3,54,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(289,NULL,3,55,'Yes','Yes','No','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(290,NULL,3,56,'No','No','No','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(291,NULL,3,57,'Yes','Yes','No','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(292,NULL,3,61,'Yes','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(293,NULL,3,62,'No','No','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(294,NULL,3,63,'No','No','No','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(295,NULL,3,64,'No','No','No','Yes','No','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(296,NULL,3,65,'No','Yes','No','Yes','No','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(297,NULL,3,68,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(298,NULL,3,69,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(299,NULL,3,113,'Yes','Yes','No','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(300,NULL,3,114,'Yes','Yes','No','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(301,NULL,3,115,'Yes','Yes','No','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(302,NULL,3,116,'Yes','Yes','No','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(303,NULL,3,117,'Yes','Yes','No','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(304,NULL,3,118,'Yes','Yes','No','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(305,NULL,3,120,'Yes','Yes','No','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(306,NULL,3,123,'Yes','Yes','No','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(307,NULL,3,124,'Yes','Yes','No','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(308,NULL,3,125,'Yes','Yes','No','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(309,NULL,3,126,'Yes','Yes','No','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(310,NULL,3,127,'Yes','Yes','No','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(311,NULL,3,128,'Yes','Yes','No','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(312,NULL,3,134,'No','Yes','No','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(313,NULL,3,135,'No','No','No','Yes','No','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(314,NULL,3,138,'No','No','No','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(315,NULL,3,139,'Yes','Yes','No','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(316,NULL,3,140,'No','Yes','No','No','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(317,NULL,3,141,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(318,NULL,3,143,'Yes','Yes','Yes','Yes','Yes','Yes',0,0,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(319,NULL,3,144,'Yes','Yes','Yes','Yes','Yes','Yes',0,0,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(320,NULL,3,145,'Yes','Yes','Yes','Yes','Yes','Yes',0,0,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(321,NULL,3,146,'Yes','Yes','Yes','Yes','Yes','Yes',0,0,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(322,NULL,3,147,'Yes','Yes','Yes','Yes','Yes','Yes',0,0,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(323,NULL,3,148,'No','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(324,NULL,3,149,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(325,NULL,3,150,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(326,NULL,3,151,'Yes','No','No','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(327,NULL,3,152,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(328,NULL,3,154,'Yes','Yes','No','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(329,NULL,3,155,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(330,NULL,3,158,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(331,NULL,3,159,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(332,NULL,3,160,'Yes','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(333,NULL,3,161,'NO','Yes','NO','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(334,NULL,3,162,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(335,NULL,3,163,'No','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(336,NULL,3,168,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(337,NULL,3,169,'No','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(338,NULL,3,170,'No','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(339,NULL,3,174,'No','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(340,NULL,4,1,'No','No','No','Yes','No','Yes',NULL,NULL,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(341,NULL,4,3,'No','No','No','Yes','No','Yes',NULL,NULL,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(342,NULL,4,4,'Yes','Yes','Yes','Yes','Yes','Yes',NULL,NULL,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(343,NULL,4,9,'No','No','No','No','No','Yes',NULL,NULL,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(344,NULL,4,10,'No','No','No','Yes','No','Yes',NULL,NULL,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(345,NULL,4,11,'No','No','No','Yes','No','Yes',NULL,NULL,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(346,NULL,4,12,'No','No','No','No','No','No',NULL,NULL,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(347,NULL,4,13,'No','No','No','No','No','No',NULL,NULL,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(348,NULL,4,14,'No','No','No','Yes','No','Yes',NULL,NULL,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(349,NULL,4,19,'No','Yes','No','Yes','No','No',NULL,NULL,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(350,NULL,4,31,'Yes','Yes','Yes','Yes','Yes','Yes',NULL,NULL,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(351,NULL,4,32,'Yes','Yes','Yes','Yes','Yes','Yes',NULL,NULL,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(352,NULL,4,34,'No','No','No','Yes','Yes','Yes',NULL,NULL,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(353,NULL,4,43,'No','No','No','Yes','No','Yes',NULL,NULL,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(354,NULL,4,57,'No','Yes','No','Yes','No','No',NULL,NULL,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(355,NULL,4,61,'Yes','No','No','No','No','No',NULL,NULL,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(356,NULL,4,62,'No','No','Yes','Yes','Yes','Yes',NULL,NULL,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(357,NULL,4,63,'No','No','No','Yes','Yes','Yes',NULL,NULL,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(358,NULL,4,64,'No','No','No','Yes','No','Yes',NULL,NULL,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(359,NULL,4,65,'No','Yes','No','Yes','No','Yes',NULL,NULL,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(360,NULL,4,135,'No','No','No','Yes','No','Yes',NULL,NULL,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(361,NULL,4,143,'No','No','No','No','No','No',NULL,NULL,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(362,NULL,4,148,'No','No','No','No','No','No',NULL,NULL,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(363,NULL,4,149,'No','Yes','No','Yes','No','No',NULL,NULL,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(364,NULL,4,161,'No','Yes','No','Yes','No','No',NULL,NULL,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(365,NULL,4,163,'No','Yes','No','Yes','No','No',NULL,NULL,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(366,NULL,4,168,'No','No','No','Yes','No','No',NULL,NULL,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(367,NULL,4,169,'No','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(368,NULL,4,170,'No','Yes','No','Yes','No','No',NULL,NULL,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(369,NULL,4,174,'No','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(370,NULL,5,1,'No','No','No','Yes','No','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(371,NULL,5,5,'No','Yes','No','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(372,NULL,5,9,'No','No','No','No','No','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(373,NULL,5,10,'No','No','No','Yes','No','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(374,NULL,5,11,'No','No','No','Yes','No','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(375,NULL,5,12,'No','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(376,NULL,5,13,'No','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(377,NULL,5,23,'No','Yes','No','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(378,NULL,5,141,'No','Yes','No','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(379,NULL,5,168,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(380,NULL,6,1,'No','No','No','Yes','No','Yes',NULL,NULL,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(381,NULL,6,2,'Yes','Yes','Yes','Yes','Yes','Yes',NULL,NULL,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(382,NULL,6,3,'No','No','No','Yes','No','Yes',NULL,NULL,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(383,NULL,6,4,'Yes','Yes','Yes','Yes','Yes','Yes',NULL,NULL,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(384,NULL,6,9,'No','No','No','No','No','No',NULL,NULL,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(385,NULL,6,10,'No','No','No','Yes','No','Yes',NULL,NULL,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(386,NULL,6,11,'No','No','No','Yes','No','Yes',NULL,NULL,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(387,NULL,6,12,'No','No','No','No','No','No',NULL,NULL,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(388,NULL,6,13,'No','No','No','No','No','No',NULL,NULL,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(389,NULL,6,14,'No','No','No','Yes','No','Yes',NULL,NULL,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(390,NULL,6,19,'No','Yes','No','Yes','No','Yes',NULL,NULL,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(391,NULL,6,20,'Yes','Yes','Yes','Yes','Yes','Yes',NULL,NULL,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(392,NULL,6,21,'Yes','Yes','No','Yes','Yes','Yes',NULL,NULL,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(393,NULL,6,31,'Yes','Yes','Yes','Yes','Yes','Yes',NULL,NULL,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(394,NULL,6,32,'Yes','Yes','Yes','Yes','Yes','Yes',NULL,NULL,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(395,NULL,6,34,'No','No','No','Yes','Yes','Yes',NULL,NULL,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(396,NULL,6,43,'No','No','No','Yes','No','Yes',NULL,NULL,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(397,NULL,6,57,'No','Yes','No','Yes','No','Yes',NULL,NULL,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(398,NULL,6,61,'Yes','No','No','No','No','No',NULL,NULL,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(399,NULL,6,62,'No','No','Yes','Yes','Yes','Yes',NULL,NULL,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(400,NULL,6,63,'No','No','No','Yes','Yes','Yes',NULL,NULL,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(401,NULL,6,64,'No','No','No','Yes','No','Yes',NULL,NULL,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(402,NULL,6,65,'No','Yes','No','Yes','No','Yes',NULL,NULL,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(403,NULL,6,70,'Yes','Yes','Yes','Yes','Yes','Yes',NULL,NULL,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(404,NULL,6,72,'Yes','Yes','Yes','Yes','Yes','Yes',NULL,NULL,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(405,NULL,6,73,'Yes','Yes','Yes','Yes','Yes','Yes',NULL,NULL,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(406,NULL,6,75,'Yes','Yes','Yes','Yes','Yes','Yes',NULL,NULL,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(407,NULL,6,80,'Yes','Yes','Yes','Yes','Yes','Yes',NULL,NULL,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(408,NULL,6,85,'Yes','Yes','Yes','Yes','Yes','Yes',NULL,NULL,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(409,NULL,6,86,'Yes','Yes','Yes','Yes','Yes','Yes',NULL,NULL,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(410,NULL,6,87,'Yes','Yes','Yes','Yes','Yes','Yes',NULL,NULL,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(411,NULL,6,88,'Yes','Yes','Yes','Yes','Yes','Yes',NULL,NULL,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(412,NULL,6,89,'Yes','Yes','Yes','Yes','Yes','Yes',NULL,NULL,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(413,NULL,6,90,'Yes','Yes','Yes','Yes','Yes','Yes',NULL,NULL,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(414,NULL,6,91,'Yes','Yes','Yes','Yes','Yes','Yes',NULL,NULL,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(415,NULL,6,92,'Yes','Yes','Yes','Yes','Yes','Yes',NULL,NULL,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(416,NULL,6,93,'Yes','Yes','Yes','Yes','Yes','Yes',NULL,NULL,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(417,NULL,6,100,'Yes','No','Yes','Yes','Yes','Yes',NULL,NULL,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(418,NULL,6,101,'Yes','No','Yes','Yes','Yes','Yes',NULL,NULL,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(419,NULL,6,102,'Yes','No','Yes','Yes','Yes','Yes',NULL,NULL,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(420,NULL,6,103,'Yes','Yes','Yes','Yes','Yes','Yes',NULL,NULL,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(421,NULL,6,107,'Yes','Yes','Yes','Yes','Yes','Yes',NULL,NULL,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(422,NULL,6,108,'Yes','Yes','Yes','Yes','Yes','Yes',NULL,NULL,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(423,NULL,6,110,'Yes','Yes','Yes','Yes','Yes','Yes',NULL,NULL,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(424,NULL,6,111,'Yes','Yes','Yes','Yes','Yes','Yes',NULL,NULL,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(425,NULL,6,131,'Yes','Yes','No','No','No','No',NULL,NULL,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(426,NULL,6,132,'Yes','Yes','Yes','Yes','Yes','Yes',NULL,NULL,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(427,NULL,6,133,'Yes','Yes','No','No','Yes','Yes',NULL,NULL,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(428,NULL,6,135,'No','No','No','Yes','No','Yes',NULL,NULL,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(429,NULL,6,136,'Yes','Yes','Yes','Yes','Yes','Yes',NULL,NULL,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(430,NULL,6,143,'No','No','No','No','No','No',NULL,NULL,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(431,NULL,6,148,'No','No','No','No','No','No',NULL,NULL,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(432,NULL,6,149,'No','Yes','No','Yes','No','No',NULL,NULL,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(433,NULL,6,161,'No','Yes','No','Yes','No','No',NULL,NULL,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(434,NULL,6,163,'No','Yes','No','Yes','No','No',NULL,NULL,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(435,NULL,6,168,'No','No','No','Yes','No','No',NULL,NULL,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(436,NULL,6,169,'No','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(437,NULL,6,170,'No','Yes','No','Yes','No','No',NULL,NULL,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(438,NULL,6,174,'No','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(439,2,1,4,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(440,2,1,32,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(441,2,1,31,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(442,2,1,61,'Yes','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(443,2,1,62,'No','No','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(444,2,1,63,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(445,2,1,64,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(446,2,1,135,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(447,2,1,65,'No','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(448,2,1,43,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(449,2,1,34,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(450,2,1,143,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(451,2,1,147,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(452,2,1,144,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(453,2,1,145,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(454,2,1,146,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(455,2,1,3,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(456,2,1,14,'Yes','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(457,2,1,2,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(458,2,1,20,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(459,2,1,21,'Yes','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(460,2,1,16,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(461,2,1,41,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(462,2,1,42,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(463,2,1,17,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(464,2,1,44,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(465,2,1,45,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(466,2,1,113,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(467,2,1,140,'No','Yes','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(468,2,1,114,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(469,2,1,117,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(470,2,1,118,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(471,2,1,116,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(472,2,1,120,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(473,2,1,124,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(474,2,1,125,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(475,2,1,121,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(476,2,1,128,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(477,2,1,126,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(478,2,1,123,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(479,2,1,139,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(480,2,1,115,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(481,2,1,127,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(482,2,1,107,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(483,2,1,108,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(484,2,1,149,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(485,2,1,162,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(486,2,1,155,'Yes','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(487,2,1,150,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(488,2,1,152,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(489,2,1,151,'Yes','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(490,2,1,160,'Yes','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(491,2,1,154,'Yes','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(492,2,1,169,'No','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(493,2,1,158,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(494,2,1,159,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(495,2,1,161,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(496,2,1,174,'No','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(497,2,1,163,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(498,2,1,166,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(499,2,1,167,'Yes','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(500,2,1,170,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(501,2,1,171,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(502,2,1,172,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(503,2,1,19,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(504,2,1,54,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(505,2,1,134,'No','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(506,2,1,138,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(507,2,1,55,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(508,2,1,57,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(509,2,1,56,'No','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(510,2,1,5,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(511,2,1,22,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(512,2,1,68,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(513,2,1,69,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(514,2,1,141,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(515,2,1,23,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(516,2,1,1,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(517,2,1,9,'Yes','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(518,2,1,10,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(519,2,1,11,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(520,2,1,12,'No','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(521,2,1,13,'Yes','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(522,2,1,168,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(523,2,1,8,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(524,2,1,70,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(525,2,1,131,'Yes','Yes','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(526,2,1,72,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(527,2,1,85,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(528,2,1,133,'Yes','Yes','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(529,2,1,86,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(530,2,1,80,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(531,2,1,87,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(532,2,1,88,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(533,2,1,89,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(534,2,1,90,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(535,2,1,91,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(536,2,1,92,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(537,2,1,93,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(538,2,1,136,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(539,2,1,132,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(540,2,1,73,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(541,2,1,100,'Yes','No','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(542,2,1,101,'Yes','No','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(543,2,1,102,'Yes','No','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(544,2,1,103,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(545,2,1,75,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(546,2,1,110,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(547,2,1,111,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(548,2,1,142,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(549,3,2,4,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(550,3,2,32,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(551,3,2,31,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(552,3,2,61,'Yes','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(553,3,2,62,'No','No','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(554,3,2,63,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(555,3,2,64,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(556,3,2,135,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(557,3,2,65,'No','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(558,3,2,43,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(559,3,2,34,'Yes','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(560,3,2,143,'No','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(561,3,2,148,'No','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(562,3,2,3,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(563,3,2,14,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(564,3,2,149,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(565,3,2,162,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(566,3,2,150,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(567,3,2,152,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(568,3,2,151,'Yes','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(569,3,2,169,'No','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(570,3,2,161,'No','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(571,3,2,174,'No','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(572,3,2,163,'No','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(573,3,2,170,'No','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(574,3,2,19,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(575,3,2,54,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(576,3,2,134,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(577,3,2,138,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(578,3,2,55,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(579,3,2,57,'No','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(580,3,2,56,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(581,3,2,1,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(582,3,2,9,'No','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(583,3,2,10,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(584,3,2,11,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(585,3,2,12,'No','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(586,3,2,13,'No','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(587,3,2,168,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(588,4,3,4,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(589,4,3,8,'No','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(590,4,3,32,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(591,4,3,31,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(592,4,3,61,'Yes','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(593,4,3,62,'No','No','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(594,4,3,63,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(595,4,3,64,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(596,4,3,135,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(597,4,3,65,'No','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(598,4,3,43,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(599,4,3,34,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(600,4,3,143,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(601,4,3,147,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(602,4,3,144,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(603,4,3,145,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(604,4,3,146,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(605,4,3,148,'No','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(606,4,3,3,'Yes','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(607,4,3,14,'Yes','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(608,4,3,2,'Yes','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(609,4,3,21,'Yes','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(610,4,3,16,'Yes','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(611,4,3,41,'Yes','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(612,4,3,42,'Yes','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(613,4,3,17,'Yes','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(614,4,3,44,'Yes','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(615,4,3,45,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(616,4,3,113,'Yes','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(617,4,3,140,'No','Yes','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(618,4,3,114,'Yes','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(619,4,3,117,'Yes','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(620,4,3,118,'Yes','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(621,4,3,116,'Yes','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(622,4,3,120,'Yes','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(623,4,3,124,'Yes','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(624,4,3,125,'Yes','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(625,4,3,128,'Yes','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(626,4,3,126,'Yes','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(627,4,3,123,'Yes','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(628,4,3,139,'Yes','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(629,4,3,115,'Yes','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(630,4,3,127,'Yes','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(631,4,3,149,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(632,4,3,162,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(633,4,3,155,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(634,4,3,150,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(635,4,3,152,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(636,4,3,151,'Yes','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(637,4,3,160,'Yes','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(638,4,3,154,'Yes','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(639,4,3,169,'No','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(640,4,3,158,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(641,4,3,159,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(642,4,3,161,'No','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(643,4,3,174,'No','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(644,4,3,163,'No','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(645,4,3,170,'No','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(646,4,3,19,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(647,4,3,54,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(648,4,3,134,'No','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(649,4,3,138,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(650,4,3,55,'Yes','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(651,4,3,57,'Yes','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(652,4,3,56,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(653,4,3,5,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(654,4,3,22,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(655,4,3,68,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(656,4,3,69,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(657,4,3,141,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(658,4,3,23,'Yes','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(659,4,3,1,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(660,4,3,9,'No','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(661,4,3,10,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(662,4,3,11,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(663,4,3,12,'No','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(664,4,3,13,'No','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(665,4,3,168,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(666,5,4,4,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(667,5,4,32,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(668,5,4,31,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(669,5,4,61,'Yes','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(670,5,4,62,'No','No','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(671,5,4,63,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(672,5,4,64,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(673,5,4,135,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(674,5,4,65,'No','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(675,5,4,43,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(676,5,4,34,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(677,5,4,143,'No','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(678,5,4,148,'No','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(679,5,4,3,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(680,5,4,14,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(681,5,4,149,'No','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(682,5,4,169,'No','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(683,5,4,161,'No','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(684,5,4,174,'No','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(685,5,4,163,'No','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(686,5,4,170,'No','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(687,5,4,19,'No','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(688,5,4,57,'No','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(689,5,4,1,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(690,5,4,9,'No','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(691,5,4,10,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(692,5,4,11,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(693,5,4,12,'No','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(694,5,4,13,'No','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(695,5,4,168,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(696,6,5,1,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(697,6,5,9,'No','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(698,6,5,10,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(699,6,5,11,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(700,6,5,12,'No','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(701,6,5,13,'No','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(702,6,5,168,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(703,7,5,5,'No','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(704,7,5,141,'No','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(705,7,5,23,'No','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(706,7,5,1,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(707,7,5,9,'No','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(708,7,5,10,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(709,7,5,11,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(710,7,5,12,'No','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(711,7,5,13,'No','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(712,7,5,168,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(713,8,6,4,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(714,8,6,32,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(715,8,6,31,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(716,8,6,61,'Yes','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(717,8,6,62,'No','No','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(718,8,6,63,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(719,8,6,64,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(720,8,6,135,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(721,8,6,65,'No','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(722,8,6,43,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(723,8,6,34,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(724,8,6,143,'No','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(725,8,6,148,'No','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(726,8,6,3,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(727,8,6,14,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(728,8,6,2,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(729,8,6,20,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(730,8,6,21,'Yes','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(731,8,6,149,'No','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(732,8,6,169,'No','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(733,8,6,161,'No','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(734,8,6,174,'No','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(735,8,6,163,'No','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(736,8,6,170,'No','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(737,8,6,19,'No','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(738,8,6,57,'No','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(739,8,6,1,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(740,8,6,9,'No','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(741,8,6,10,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(742,8,6,11,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(743,8,6,12,'No','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(744,8,6,13,'No','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(745,8,6,168,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(746,8,6,70,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(747,8,6,131,'Yes','Yes','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(748,8,6,72,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(749,8,6,85,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(750,8,6,133,'Yes','Yes','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(751,8,6,86,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(752,8,6,80,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(753,8,6,87,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(754,8,6,88,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(755,8,6,89,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(756,8,6,90,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(757,8,6,91,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(758,8,6,92,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(759,8,6,93,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(760,8,6,136,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(761,8,6,132,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(762,8,6,73,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(763,8,6,100,'Yes','No','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(764,8,6,101,'Yes','No','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(765,8,6,102,'Yes','No','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(766,8,6,103,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(767,8,6,75,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(768,8,6,110,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(769,8,6,111,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(770,9,4,4,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(771,9,4,32,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(772,9,4,31,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(773,9,4,61,'Yes','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(774,9,4,62,'No','No','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(775,9,4,63,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(776,9,4,64,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(777,9,4,135,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(778,9,4,65,'No','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(779,9,4,43,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(780,9,4,34,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(781,9,4,143,'No','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(782,9,4,148,'No','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(783,9,4,3,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(784,9,4,14,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(785,9,4,149,'No','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(786,9,4,169,'No','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(787,9,4,161,'No','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(788,9,4,174,'No','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(789,9,4,163,'No','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(790,9,4,170,'No','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(791,9,4,19,'No','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(792,9,4,57,'No','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(793,9,4,1,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(794,9,4,9,'No','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(795,9,4,10,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(796,9,4,11,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(797,9,4,12,'No','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(798,9,4,13,'No','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(799,9,4,168,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(800,1,NULL,176,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(801,NULL,1,176,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(802,NULL,2,176,'No','No','No','Yes','No','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(803,NULL,3,176,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(804,NULL,4,176,'No','No','No','Yes','No','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(805,NULL,6,176,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(806,9,4,176,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(807,8,6,176,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(808,5,4,176,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(809,4,3,176,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(810,3,2,176,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(811,2,1,176,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(812,1,NULL,177,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(813,NULL,1,177,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(814,NULL,2,177,'Yes','Yes','No','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(815,NULL,3,177,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(816,NULL,4,177,'Yes','Yes','No','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(817,NULL,6,177,'Yes','Yes','No','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(818,1,NULL,178,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(819,NULL,1,178,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(820,NULL,3,178,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(821,1,NULL,179,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(822,NULL,1,179,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(823,NULL,3,179,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(824,1,NULL,180,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(825,NULL,1,180,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(826,NULL,2,180,'Yes','Yes','No','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(827,NULL,3,180,'Yes','Yes','No','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(828,NULL,4,180,'Yes','Yes','No','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(829,NULL,6,180,'Yes','Yes','No','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(830,1,NULL,181,'No','Yes','No','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(831,NULL,1,181,'No','Yes','No','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(832,NULL,2,181,'No','Yes','No','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(833,NULL,3,181,'No','Yes','No','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(834,NULL,4,181,'No','Yes','No','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(835,NULL,6,181,'No','Yes','No','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(836,2,1,177,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(837,2,1,178,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(838,2,1,179,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(839,2,1,180,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(840,2,1,181,'No','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(841,3,2,177,'Yes','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(842,3,2,180,'Yes','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(843,3,2,181,'No','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(844,4,3,177,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(845,4,3,178,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(846,4,3,179,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(847,4,3,180,'Yes','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(848,4,3,181,'No','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(849,5,4,177,'Yes','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(850,5,4,180,'Yes','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(851,5,4,181,'No','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(852,8,6,177,'Yes','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(853,8,6,180,'Yes','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(854,8,6,181,'No','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(855,9,4,177,'Yes','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(856,9,4,180,'Yes','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(857,9,4,181,'No','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(858,1,NULL,182,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(859,NULL,1,182,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(860,NULL,3,182,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(861,NULL,6,182,'Yes','Yes','No','Yes','Yes','Yes',NULL,NULL,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(862,1,NULL,183,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(863,NULL,1,183,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(864,NULL,2,183,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(865,NULL,3,183,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(866,NULL,4,183,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(867,NULL,6,183,'Yes','Yes','No','Yes','Yes','Yes',NULL,NULL,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(868,2,1,182,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(869,2,1,183,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(870,3,2,183,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(871,9,4,183,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(872,8,6,182,'Yes','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(873,8,6,183,'Yes','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(874,5,4,183,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(875,4,3,182,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(876,4,3,183,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(877,1,NULL,184,'Yes','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(878,NULL,1,184,'Yes','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(879,NULL,3,184,'Yes','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(880,2,1,184,'Yes','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(881,4,3,184,'Yes','Yes','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(882,2,1,148,'No','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(883,1,NULL,175,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(884,NULL,1,175,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(885,NULL,2,175,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(886,NULL,3,175,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(887,NULL,4,175,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(888,NULL,6,175,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(889,NULL,7,175,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(890,2,1,175,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(891,3,2,175,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(892,4,3,175,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(893,5,4,175,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(894,8,6,175,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(895,9,4,175,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(896,1,NULL,175,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(897,NULL,1,175,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(898,NULL,2,175,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(899,NULL,3,175,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(900,NULL,4,175,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(901,NULL,6,175,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(902,2,1,175,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(903,3,2,175,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(904,4,3,175,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(905,5,4,175,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(906,8,6,175,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(907,9,4,175,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(908,1,NULL,185,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(909,NULL,1,185,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(910,NULL,2,185,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(911,NULL,3,185,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(912,NULL,4,185,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(913,NULL,6,185,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(914,2,1,185,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(915,3,2,185,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(916,4,3,185,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(917,5,4,185,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(918,8,6,185,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(919,9,4,185,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(920,1,NULL,186,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(921,NULL,1,186,'No','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(922,NULL,2,186,'No','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(923,NULL,3,186,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(924,NULL,4,186,'No','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(925,NULL,6,186,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(926,2,1,186,'No','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(927,3,2,186,'No','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(928,4,3,186,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(929,5,4,186,'No','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(930,8,6,186,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(931,9,4,186,'No','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(932,1,NULL,188,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(933,NULL,1,188,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(934,NULL,2,188,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(935,NULL,3,188,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(936,NULL,4,188,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(937,NULL,6,188,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(938,2,1,188,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(939,3,2,188,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(940,4,3,188,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(941,5,4,188,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(942,8,6,188,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(943,9,4,188,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(944,1,NULL,189,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(945,NULL,1,189,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(946,NULL,2,189,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(947,NULL,3,189,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(948,NULL,4,189,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(949,NULL,6,189,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(950,2,1,189,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(951,3,2,189,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(952,4,3,189,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(953,5,4,189,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(954,8,6,189,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(955,9,4,189,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(956,NULL,3,8,'No','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(957,4,3,8,'No','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(958,1,NULL,190,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(959,NULL,1,190,'No','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(960,NULL,2,190,'No','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(961,NULL,3,190,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(962,NULL,4,190,'No','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(963,NULL,6,190,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(964,2,1,190,'No','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(965,3,2,190,'No','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(966,4,3,190,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(967,5,4,190,'No','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(968,8,6,190,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(969,9,4,190,'No','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(970,1,NULL,191,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(971,NULL,1,191,'No','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(972,NULL,2,191,'No','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(973,NULL,3,191,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(974,NULL,4,191,'No','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(975,NULL,6,191,'NO','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(976,2,1,191,'No','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(977,3,2,191,'No','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(978,4,3,191,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(979,5,4,191,'No','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(980,8,6,191,'No','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(981,9,4,191,'No','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(982,1,NULL,192,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(983,NULL,1,192,'No','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(984,NULL,2,192,'No','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(985,NULL,3,192,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(986,NULL,4,192,'No','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(987,NULL,6,192,'NO','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(988,2,1,192,'No','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(989,3,2,192,'No','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(990,4,3,192,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(991,5,4,192,'No','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(992,8,6,192,'No','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(993,9,4,192,'No','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(994,1,NULL,193,'No','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(995,NULL,1,193,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(996,NULL,2,193,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(997,NULL,3,193,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(998,NULL,4,193,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(999,NULL,6,193,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(1000,2,1,193,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(1001,3,2,193,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(1002,4,3,193,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(1003,5,4,193,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(1004,8,6,193,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(1005,9,4,193,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(1006,1,NULL,194,'No','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(1007,NULL,1,194,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(1008,NULL,2,194,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(1009,NULL,3,194,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(1010,NULL,4,194,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(1011,NULL,6,194,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(1012,2,1,194,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(1013,3,2,194,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(1014,4,3,194,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(1015,5,4,194,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(1016,8,6,194,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(1017,9,4,194,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(1018,1,NULL,195,'No','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(1019,NULL,1,195,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(1020,NULL,2,195,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(1021,NULL,3,195,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(1022,NULL,4,195,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(1023,NULL,6,195,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(1024,2,1,195,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(1025,3,2,195,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(1026,4,3,195,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(1027,5,4,195,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(1028,8,6,195,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(1029,9,4,195,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(1030,1,NULL,196,'No','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(1031,NULL,1,196,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(1032,NULL,2,196,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(1033,NULL,3,196,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(1034,NULL,4,196,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(1035,NULL,6,196,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(1036,2,1,196,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(1037,3,2,196,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(1038,4,3,196,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(1039,5,4,196,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(1040,8,6,196,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(1041,9,4,196,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(1042,1,NULL,197,'No','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(1043,NULL,1,197,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(1044,NULL,2,197,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(1045,NULL,3,197,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(1046,NULL,4,197,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(1047,NULL,6,197,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(1048,2,1,197,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(1049,3,2,197,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(1050,4,3,197,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(1051,5,4,197,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(1052,8,6,197,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(1053,9,4,197,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(1054,1,NULL,198,'No','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(1055,NULL,1,198,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(1056,NULL,2,198,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(1057,NULL,3,198,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(1058,NULL,4,198,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(1059,NULL,6,198,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(1060,2,1,198,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(1061,3,2,198,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(1062,4,3,198,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(1063,5,4,198,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(1064,8,6,198,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(1065,9,4,198,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(1066,1,NULL,199,'No','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(1067,NULL,1,199,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(1068,NULL,2,199,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(1069,NULL,3,199,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(1070,NULL,4,199,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(1071,NULL,6,199,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(1072,2,1,199,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(1073,3,2,199,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(1074,4,3,199,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(1075,5,4,199,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(1076,8,6,199,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(1077,9,4,199,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(1078,1,NULL,200,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(1079,NULL,1,200,'No','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(1080,NULL,2,200,'No','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(1081,NULL,3,200,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(1082,NULL,4,200,'No','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(1083,NULL,6,200,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(1084,2,1,200,'No','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(1085,3,2,200,'No','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(1086,4,3,200,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(1087,5,4,200,'No','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(1088,8,6,200,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',1),(1089,9,4,200,'No','No','No','No','No','No',1,1,'2016-11-02 11:14:14','2016-11-02 11:14:14',0),(1090,1,NULL,201,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:22:11','2016-11-02 11:22:11',1),(1091,1,NULL,202,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:22:11','2016-11-02 11:22:11',1),(1092,1,NULL,203,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:22:11','2016-11-02 11:22:11',1),(1093,1,NULL,204,'No','Yes','No','Yes','No','No',1,1,'2016-11-02 11:22:11','2016-11-02 11:22:11',1),(1094,1,NULL,205,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:22:11','2016-11-02 11:22:11',1),(1095,1,NULL,206,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:22:11','2016-11-02 11:22:11',1),(1096,NULL,1,201,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:22:11','2016-11-02 11:22:11',1),(1097,NULL,1,202,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:22:11','2016-11-02 11:22:11',1),(1098,NULL,1,203,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:22:11','2016-11-02 11:22:11',1),(1099,NULL,1,204,'No','Yes','No','Yes','No','No',1,1,'2016-11-02 11:22:11','2016-11-02 11:22:11',1),(1100,NULL,1,205,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:22:11','2016-11-02 11:22:11',1),(1101,NULL,1,206,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:22:11','2016-11-02 11:22:11',1),(1102,NULL,2,201,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:22:11','2016-11-02 11:22:11',1),(1103,NULL,2,204,'No','Yes','No','Yes','No','No',1,1,'2016-11-02 11:22:11','2016-11-02 11:22:11',1),(1104,NULL,2,205,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:22:11','2016-11-02 11:22:11',1),(1105,NULL,3,201,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:22:11','2016-11-02 11:22:11',1),(1106,NULL,3,204,'No','Yes','No','Yes','No','No',1,1,'2016-11-02 11:22:11','2016-11-02 11:22:11',1),(1107,NULL,3,205,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:22:11','2016-11-02 11:22:11',1),(1108,NULL,3,206,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:22:11','2016-11-02 11:22:11',1),(1109,NULL,4,201,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:22:11','2016-11-02 11:22:11',1),(1110,NULL,4,204,'No','Yes','No','Yes','No','No',1,1,'2016-11-02 11:22:11','2016-11-02 11:22:11',1),(1111,NULL,4,205,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:22:11','2016-11-02 11:22:11',1),(1112,NULL,6,201,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-02 11:22:11','2016-11-02 11:22:11',1),(1113,NULL,6,204,'No','Yes','No','Yes','No','No',1,1,'2016-11-02 11:22:11','2016-11-02 11:22:11',1),(1114,NULL,6,205,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:22:11','2016-11-02 11:22:11',1),(1115,2,1,201,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:22:11','2016-11-02 11:22:11',1),(1116,2,1,202,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:22:11','2016-11-02 11:22:11',1),(1117,2,1,203,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:22:11','2016-11-02 11:22:11',1),(1118,2,1,204,'No','Yes','No','Yes','No','No',1,1,'2016-11-02 11:22:11','2016-11-02 11:22:11',1),(1119,2,1,205,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:22:11','2016-11-02 11:22:11',1),(1120,2,1,206,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:22:11','2016-11-02 11:22:11',1),(1121,3,2,201,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:22:11','2016-11-02 11:22:11',1),(1122,3,2,204,'No','Yes','No','Yes','No','No',1,1,'2016-11-02 11:22:11','2016-11-02 11:22:11',1),(1123,3,2,205,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:22:11','2016-11-02 11:22:11',1),(1124,4,3,201,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:22:11','2016-11-02 11:22:11',1),(1125,4,3,204,'No','Yes','No','Yes','No','No',1,1,'2016-11-02 11:22:11','2016-11-02 11:22:11',1),(1126,4,3,205,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:22:11','2016-11-02 11:22:11',1),(1127,4,3,206,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:22:11','2016-11-02 11:22:11',1),(1128,5,4,201,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:22:11','2016-11-02 11:22:11',1),(1129,5,4,204,'No','Yes','No','Yes','No','No',1,1,'2016-11-02 11:22:11','2016-11-02 11:22:11',1),(1130,5,4,205,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:22:11','2016-11-02 11:22:11',1),(1131,8,6,201,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:22:11','2016-11-02 11:22:11',1),(1132,8,6,204,'No','Yes','No','Yes','No','No',1,1,'2016-11-02 11:22:11','2016-11-02 11:22:11',1),(1133,8,6,205,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:22:11','2016-11-02 11:22:11',1),(1134,9,4,201,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-02 11:22:11','2016-11-02 11:22:11',1),(1135,9,4,204,'No','Yes','No','Yes','No','No',1,1,'2016-11-02 11:22:11','2016-11-02 11:22:11',1),(1136,9,4,205,'No','No','No','Yes','No','No',1,1,'2016-11-02 11:22:11','2016-11-02 11:22:11',1),(1137,1,NULL,201,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-08 10:19:54','2016-11-08 10:19:54',1),(1138,1,NULL,202,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-08 10:19:54','2016-11-08 10:19:54',1),(1139,1,NULL,203,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-08 10:19:54','2016-11-08 10:19:54',1),(1140,1,NULL,204,'No','Yes','No','Yes','No','No',1,1,'2016-11-08 10:19:54','2016-11-08 10:19:54',1),(1141,1,NULL,205,'No','No','No','Yes','No','No',1,1,'2016-11-08 10:19:54','2016-11-08 10:19:54',1),(1142,1,NULL,206,'No','No','No','Yes','No','No',1,1,'2016-11-08 10:19:54','2016-11-08 10:19:54',1),(1143,NULL,1,201,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-08 10:19:54','2016-11-08 10:19:54',1),(1144,NULL,1,202,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-08 10:19:54','2016-11-08 10:19:54',1),(1145,NULL,1,203,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-08 10:19:54','2016-11-08 10:19:54',1),(1146,NULL,1,204,'No','Yes','No','Yes','No','No',1,1,'2016-11-08 10:19:54','2016-11-08 10:19:54',1),(1147,NULL,1,205,'No','No','No','Yes','No','No',1,1,'2016-11-08 10:19:54','2016-11-08 10:19:54',1),(1148,NULL,1,206,'No','No','No','Yes','No','No',1,1,'2016-11-08 10:19:54','2016-11-08 10:19:54',1),(1149,NULL,2,201,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-08 10:19:54','2016-11-08 10:19:54',1),(1150,NULL,2,204,'No','Yes','No','Yes','No','No',1,1,'2016-11-08 10:19:54','2016-11-08 10:19:54',1),(1151,NULL,2,205,'No','No','No','Yes','No','No',1,1,'2016-11-08 10:19:54','2016-11-08 10:19:54',1),(1152,NULL,3,201,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-08 10:19:54','2016-11-08 10:19:54',1),(1153,NULL,3,204,'No','Yes','No','Yes','No','No',1,1,'2016-11-08 10:19:54','2016-11-08 10:19:54',1),(1154,NULL,3,205,'No','No','No','Yes','No','No',1,1,'2016-11-08 10:19:54','2016-11-08 10:19:54',1),(1155,NULL,3,206,'No','No','No','Yes','No','No',1,1,'2016-11-08 10:19:54','2016-11-08 10:19:54',1),(1156,NULL,4,201,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-08 10:19:54','2016-11-08 10:19:54',1),(1157,NULL,4,204,'No','Yes','No','Yes','No','No',1,1,'2016-11-08 10:19:54','2016-11-08 10:19:54',1),(1158,NULL,4,205,'No','No','No','Yes','No','No',1,1,'2016-11-08 10:19:54','2016-11-08 10:19:54',1),(1159,NULL,6,201,'Yes','Yes','Yes','Yes','No','No',1,1,'2016-11-08 10:19:54','2016-11-08 10:19:54',1),(1160,NULL,6,204,'No','Yes','No','Yes','No','No',1,1,'2016-11-08 10:19:54','2016-11-08 10:19:54',1),(1161,NULL,6,205,'No','No','No','Yes','No','No',1,1,'2016-11-08 10:19:54','2016-11-08 10:19:54',1),(1162,2,1,201,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-08 10:19:54','2016-11-08 10:19:54',1),(1163,2,1,202,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-08 10:19:54','2016-11-08 10:19:54',1),(1164,2,1,203,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-08 10:19:54','2016-11-08 10:19:54',1),(1165,2,1,204,'No','Yes','No','Yes','No','No',1,1,'2016-11-08 10:19:54','2016-11-08 10:19:54',1),(1166,2,1,205,'No','No','No','Yes','No','No',1,1,'2016-11-08 10:19:54','2016-11-08 10:19:54',1),(1167,2,1,206,'No','No','No','Yes','No','No',1,1,'2016-11-08 10:19:54','2016-11-08 10:19:54',1),(1168,3,2,201,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-08 10:19:54','2016-11-08 10:19:54',1),(1169,3,2,204,'No','Yes','No','Yes','No','No',1,1,'2016-11-08 10:19:54','2016-11-08 10:19:54',1),(1170,3,2,205,'No','No','No','Yes','No','No',1,1,'2016-11-08 10:19:54','2016-11-08 10:19:54',1),(1171,4,3,201,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-08 10:19:54','2016-11-08 10:19:54',1),(1172,4,3,204,'No','Yes','No','Yes','No','No',1,1,'2016-11-08 10:19:54','2016-11-08 10:19:54',1),(1173,4,3,205,'No','No','No','Yes','No','No',1,1,'2016-11-08 10:19:54','2016-11-08 10:19:54',1),(1174,4,3,206,'No','No','No','Yes','No','No',1,1,'2016-11-08 10:19:54','2016-11-08 10:19:54',1),(1175,5,4,201,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-08 10:19:54','2016-11-08 10:19:54',1),(1176,5,4,204,'No','Yes','No','Yes','No','No',1,1,'2016-11-08 10:19:54','2016-11-08 10:19:54',1),(1177,5,4,205,'No','No','No','Yes','No','No',1,1,'2016-11-08 10:19:54','2016-11-08 10:19:54',1),(1178,8,6,201,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-08 10:19:54','2016-11-08 10:19:54',1),(1179,8,6,204,'No','Yes','No','Yes','No','No',1,1,'2016-11-08 10:19:54','2016-11-08 10:19:54',1),(1180,8,6,205,'No','No','No','Yes','No','No',1,1,'2016-11-08 10:19:54','2016-11-08 10:19:54',1),(1181,9,4,201,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,'2016-11-08 10:19:54','2016-11-08 10:19:54',1),(1182,9,4,204,'No','Yes','No','Yes','No','No',1,1,'2016-11-08 10:19:54','2016-11-08 10:19:54',1),(1183,9,4,205,'No','No','No','Yes','No','No',1,1,'2016-11-08 10:19:54','2016-11-08 10:19:54',1),(1184,1,NULL,207,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,NOW(),NOW(),1),(1185,NULL,1,207,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(1186,NULL,2,207,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(1187,NULL,3,207,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(1188,NULL,4,207,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(1189,NULL,5,207,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(1190,NULL,6,207,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(1191,2,1,207,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(1192,3,2,207,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(1193,4,3,207,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(1194,5,4,207,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(1195,6,5,207,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(1196,8,6,207,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(1197,1,NULL,208,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(1198,NULL,1,208,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(1199,NULL,2,208,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(1200,NULL,3,208,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(1201,NULL,4,208,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(1202,NULL,5,208,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(1203,NULL,6,208,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(1204,2,1,208,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(1205,3,2,208,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(1206,4,3,208,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(1207,5,4,208,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(1208,6,5,208,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(1209,8,6,208,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(1210,1,NULL,187,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(1211,NULL,1,187,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(1212,NULL,3,187,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(1213,NULL,6,187,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(1214,2,1,187,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(1215,4,3,187,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(1216,8,6,187,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(1217,1,NULL,209,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(1218,NULL,1,209,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(1219,NULL,2,209,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(1220,NULL,3,209,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(1221,NULL,4,209,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(1222,NULL,5,209,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(1223,NULL,6,209,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(1224,2,1,209,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(1225,3,2,209,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(1226,4,3,209,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(1227,5,4,209,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(1228,6,5,209,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(1229,8,6,209,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(1230,1,NULL,177,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(1231,NULL,1,177,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(1232,NULL,2,177,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(1233,NULL,3,177,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(1234,NULL,4,177,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(1235,NULL,5,177,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(1236,NULL,6,177,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(1237,2,1,177,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(1238,3,2,177,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(1239,4,3,177,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(1240,5,4,177,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(1241,6,5,177,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(1242,8,6,177,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(1243,1,NULL,180,'Yes','Yes','No','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(1244,NULL,1,180,'Yes','Yes','No','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(1245,NULL,2,180,'Yes','Yes','No','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(1246,NULL,3,180,'Yes','Yes','No','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(1247,NULL,4,180,'Yes','Yes','No','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(1248,NULL,5,180,'Yes','Yes','No','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(1249,NULL,6,180,'Yes','Yes','No','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(1250,2,1,180,'Yes','Yes','No','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(1251,3,2,180,'Yes','Yes','No','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(1252,4,3,180,'Yes','Yes','No','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(1253,5,4,180,'Yes','Yes','No','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(1254,6,5,180,'Yes','Yes','No','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(1255,8,6,180,'Yes','Yes','No','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(1256,1,NULL,181,'Yes','Yes','No','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(1257,NULL,1,181,'Yes','Yes','No','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(1258,NULL,2,181,'Yes','Yes','No','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(1259,NULL,3,181,'Yes','Yes','No','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(1260,NULL,4,181,'Yes','Yes','No','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(1261,NULL,5,181,'Yes','Yes','No','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(1262,NULL,6,181,'Yes','Yes','No','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(1263,2,1,181,'Yes','Yes','No','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(1264,3,2,181,'Yes','Yes','No','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(1265,4,3,181,'Yes','Yes','No','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(1266,5,4,181,'Yes','Yes','No','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(1267,6,5,181,'Yes','Yes','No','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(1268,8,6,181,'Yes','Yes','No','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(1269,1,NULL,178,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(1270,NULL,1,178,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(1271,NULL,3,178,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(1272,2,1,178,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(1273,4,3,178,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(1274,1,NULL,179,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(1275,NULL,1,179,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(1276,NULL,3,179,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(1277,2,1,179,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(1278,4,3,179,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(1279,1,NULL,210,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(1280,NULL,1,210,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(1281,NULL,3,210,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(1282,2,1,210,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,NOW(),NOW(),1),
(1283,4,3,210,'Yes','Yes','Yes','Yes','Yes','Yes',1,1,NOW(),NOW(),1);

/*Table structure for table `main_racecode` */

DROP TABLE IF EXISTS `main_racecode`;

CREATE TABLE `main_racecode` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `racecode` varchar(255) NOT NULL,
  `racename` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `createdby` int(11) unsigned DEFAULT NULL,
  `modifiedby` int(11) unsigned DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `isactive` tinyint(1) unsigned DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

/*Data for the table `main_racecode` */

insert  into `main_racecode`(`id`,`racecode`,`racename`,`description`,`createdby`,`modifiedby`,`createddate`,`modifieddate`,`isactive`) values (1,'','Australoid','Australoid peoples ranged throughout Indonesia, Malaysia, Australia, New Guinea, Melanesia, the Andaman Islands and the Indian subcontinent,as well as parts of the Middle East.',1,1,'2016-11-02 11:14:16','2016-11-02 11:14:16',1),(2,'','Caucasoid','Caucasian race has been used to describe the physical or biological type of some or all of the populations of Europe, North Africa, the Horn of Africa, Western Asia, Central Asia, and South Asia.',1,1,'2016-11-02 11:14:16','2016-11-02 11:14:16',1),(3,'','Mongoloid','Mongoloid are the populations of East Asia, Central Asia, Southeast Asia, Eastern Russia, the Arctic, the Americas, parts of the Pacific Islands, and some northeastern parts of South Asia.',1,1,'2016-11-02 11:14:16','2016-11-02 11:14:16',1),(4,'','Negroid','Negroids are traditionally distinguished by physical characteristics such as brown to black skin and often tightly curled hairand including peoples indigenous to sub-Saharan Africa.',1,1,'2016-11-02 11:14:16','2016-11-02 11:14:16',1);

/*Table structure for table `main_remunerationbasis` */

DROP TABLE IF EXISTS `main_remunerationbasis`;

CREATE TABLE `main_remunerationbasis` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `remtype` varchar(255) DEFAULT NULL,
  `remdesc` varchar(255) DEFAULT NULL,
  `createdby` int(11) unsigned DEFAULT NULL,
  `modifiedby` int(11) unsigned DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `isactive` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `main_remunerationbasis` */

/*Table structure for table `main_request_history` */

DROP TABLE IF EXISTS `main_request_history`;

CREATE TABLE `main_request_history` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `request_id` bigint(20) unsigned DEFAULT NULL,
  `description` varchar(300) DEFAULT NULL,
  `emp_id` bigint(20) unsigned DEFAULT NULL,
  `emp_name` varchar(100) DEFAULT NULL,
  `emp_profileimg` varchar(150) DEFAULT NULL,
  `createdby` bigint(20) unsigned DEFAULT NULL,
  `modifiedby` bigint(20) unsigned DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `isactive` tinyint(1) unsigned DEFAULT '1' COMMENT '1=active,0=inactive',
  `comments` varchar(300) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='for request history';

/*Data for the table `main_request_history` */

/*Table structure for table `main_requisition` */

DROP TABLE IF EXISTS `main_requisition`;

CREATE TABLE `main_requisition` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `requisition_code` varchar(20) DEFAULT NULL,
  `onboard_date` date DEFAULT NULL,
  `position_id` int(11) DEFAULT NULL,
  `reporting_id` bigint(20) unsigned DEFAULT NULL,
  `businessunit_id` int(11) unsigned DEFAULT NULL,
  `department_id` int(11) unsigned DEFAULT NULL,
  `jobtitle` int(11) unsigned DEFAULT NULL,
  `req_no_positions` int(11) unsigned DEFAULT NULL,
  `selected_members` int(11) unsigned DEFAULT '0' COMMENT 'count of selected members',
  `filled_positions` int(11) unsigned DEFAULT '0',
  `jobdescription` text,
  `req_skills` text NOT NULL,
  `req_qualification` varchar(150) DEFAULT NULL,
  `req_exp_years` varchar(10) NOT NULL,
  `emp_type` int(11) unsigned DEFAULT NULL,
  `req_priority` tinyint(1) unsigned DEFAULT NULL COMMENT '1- High, 2- Medium,3- Low',
  `additional_info` text,
  `req_status` enum('Initiated','Approved','Rejected','Closed','On hold','Complete','In process') DEFAULT NULL,
  `approver1` bigint(20) unsigned DEFAULT NULL,
  `approver2` bigint(20) unsigned DEFAULT NULL,
  `approver3` bigint(20) unsigned DEFAULT NULL,
  `appstatus1` enum('Initiated','Approved','Rejected','Closed','On hold','Complete','In process') DEFAULT NULL,
  `appstatus2` enum('Initiated','Approved','Rejected','Closed','On hold','Complete','In process') DEFAULT NULL,
  `appstatus3` enum('Initiated','Approved','Rejected','Closed','On hold','Complete','In process') DEFAULT NULL,
  `recruiters` varchar(150) DEFAULT NULL,
  `client_id` int(10) DEFAULT NULL,
  `isactive` tinyint(1) unsigned DEFAULT NULL,
  `createdby` int(11) unsigned DEFAULT NULL,
  `modifiedby` int(11) unsigned DEFAULT NULL,
  `createdon` datetime DEFAULT NULL,
  `modifiedon` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `un1` (`requisition_code`),
  KEY `NewIndex1` (`position_id`),
  KEY `reporting` (`reporting_id`),
  KEY `emptype` (`emp_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `main_requisition` */

/*Table structure for table `main_requisition_summary` */

DROP TABLE IF EXISTS `main_requisition_summary`;

CREATE TABLE `main_requisition_summary` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `req_id` int(11) DEFAULT NULL,
  `requisition_code` varchar(20) DEFAULT NULL,
  `onboard_date` date DEFAULT NULL,
  `position_id` int(11) DEFAULT NULL,
  `position_name` varchar(200) DEFAULT NULL,
  `reporting_id` bigint(20) unsigned DEFAULT NULL,
  `reporting_manager_name` varchar(200) DEFAULT NULL,
  `businessunit_id` int(11) unsigned DEFAULT NULL,
  `businessunit_name` varchar(200) DEFAULT NULL,
  `department_id` int(11) unsigned DEFAULT NULL,
  `department_name` varchar(200) DEFAULT NULL,
  `jobtitle` int(11) unsigned DEFAULT NULL,
  `jobtitle_name` varchar(200) DEFAULT NULL,
  `req_no_positions` int(11) unsigned DEFAULT NULL,
  `selected_members` int(11) unsigned DEFAULT '0' COMMENT 'count of selected members',
  `filled_positions` int(11) unsigned DEFAULT '0',
  `jobdescription` text,
  `req_skills` text NOT NULL,
  `req_qualification` varchar(150) DEFAULT NULL,
  `req_exp_years` varchar(10) NOT NULL,
  `emp_type` int(11) unsigned DEFAULT NULL,
  `emp_type_name` varchar(200) DEFAULT NULL,
  `req_priority` tinyint(1) unsigned DEFAULT NULL COMMENT '1- High, 2- Medium,3- Low',
  `additional_info` text,
  `req_status` enum('Initiated','Approved','Rejected','Closed','On hold','Complete','In process') DEFAULT NULL,
  `approver1` bigint(20) unsigned DEFAULT NULL,
  `approver1_name` varchar(200) DEFAULT NULL,
  `approver2` bigint(20) unsigned DEFAULT NULL,
  `approver2_name` varchar(200) DEFAULT NULL,
  `approver3` bigint(20) unsigned DEFAULT NULL,
  `approver3_name` varchar(200) DEFAULT NULL,
  `appstatus1` enum('Initiated','Approved','Rejected','Closed','On hold','Complete','In process') DEFAULT NULL,
  `appstatus2` enum('Initiated','Approved','Rejected','Closed','On hold','Complete','In process') DEFAULT NULL,
  `appstatus3` enum('Initiated','Approved','Rejected','Closed','On hold','Complete','In process') DEFAULT NULL,
  `recruiters` varchar(150) DEFAULT NULL,
  `client_id` int(11) DEFAULT NULL,
  `isactive` tinyint(1) unsigned DEFAULT NULL,
  `createdby_name` varchar(200) DEFAULT NULL,
  `createdby` int(11) unsigned DEFAULT NULL,
  `modifiedby` int(11) unsigned DEFAULT NULL,
  `createdon` datetime DEFAULT NULL,
  `modifiedon` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `un1` (`requisition_code`),
  KEY `NewIndex1` (`position_id`),
  KEY `reporting` (`reporting_id`),
  KEY `emptype` (`emp_type`),
  KEY `NewIndex2` (`req_id`),
  KEY `NewIndex3` (`businessunit_id`),
  KEY `NewIndex4` (`department_id`),
  KEY `NewIndex5` (`jobtitle`),
  KEY `NewIndex6` (`approver1`),
  KEY `NewIndex7` (`approver2`),
  KEY `NewIndex8` (`approver3`),
  KEY `NewIndex9` (`createdby`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `main_requisition_summary` */

/*Table structure for table `main_roles` */

DROP TABLE IF EXISTS `main_roles`;

CREATE TABLE `main_roles` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `rolename` varchar(100) NOT NULL,
  `roletype` varchar(100) DEFAULT NULL,
  `roledescription` varchar(100) DEFAULT NULL,
  `group_id` int(11) unsigned DEFAULT NULL,
  `levelid` int(11) DEFAULT NULL,
  `createdby` int(11) unsigned DEFAULT NULL,
  `modifiedby` int(11) unsigned DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `isactive` tinyint(1) unsigned DEFAULT '1' COMMENT '1=active,0=inactive',
  PRIMARY KEY (`id`),
  KEY `NewIndex1` (`group_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;

/*Data for the table `main_roles` */

insert  into `main_roles`(`id`,`rolename`,`roletype`,`roledescription`,`group_id`,`levelid`,`createdby`,`modifiedby`,`createddate`,`modifieddate`,`isactive`) values (1,'Super Admin','admin',NULL,NULL,0,0,0,'2015-07-29 08:45:49','2015-07-29 08:45:49',1),(2,'Management','management','',1,1,1,1,'2015-07-29 08:45:01','2015-07-29 08:45:01',1),(3,'Manager','manager','',2,2,1,1,'2015-07-29 08:45:49','2015-07-29 08:45:49',1),(4,'HR Manager','hrmanager','',3,3,1,1,'2015-07-29 08:46:36','2015-07-29 08:46:36',1),(5,'Employee','employee','',4,4,1,1,'2015-07-29 08:47:54','2015-07-29 08:47:54',1),(6,'User','user','',5,5,1,1,'2015-07-29 09:02:21','2015-07-29 09:02:21',1),(7,'Agency user','agency','',5,5,1,1,'2015-07-29 09:03:19','2015-07-29 09:03:19',1),(8,'System Admin','sysadmin','',6,6,1,1,'2015-07-29 09:04:08','2015-07-29 09:04:08',1),(9,'Team Lead','lead','',4,4,1,1,'2015-07-29 09:05:02','2015-07-29 09:05:02',1);

/*Table structure for table `main_sd_configurations` */

DROP TABLE IF EXISTS `main_sd_configurations`;

CREATE TABLE `main_sd_configurations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `businessunit_id` bigint(20) unsigned DEFAULT NULL,
  `department_id` bigint(20) unsigned DEFAULT NULL,
  `service_desk_flag` tinyint(1) DEFAULT '1' COMMENT '1=businessunitwise,0=departmentwise',
  `request_for` tinyint(1) DEFAULT '1' COMMENT '1=service request,2=asset request',
  `service_desk_id` bigint(20) DEFAULT NULL,
  `request_recievers` text,
  `cc_mail_recievers` text,
  `approver_1` bigint(20) unsigned DEFAULT NULL,
  `approver_2` bigint(20) unsigned DEFAULT NULL,
  `approver_3` bigint(20) unsigned DEFAULT NULL,
  `attachment` tinyint(1) DEFAULT '0' COMMENT '1=yes,0=no',
  `description` varchar(255) DEFAULT NULL,
  `sd_category` varchar(255) DEFAULT NULL,
  `createdby` bigint(20) DEFAULT NULL,
  `modifiedby` bigint(20) DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `isactive` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `main_sd_configurations` */

/*Table structure for table `main_sd_depts` */

DROP TABLE IF EXISTS `main_sd_depts`;

CREATE TABLE `main_sd_depts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `service_desk_name` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `createdby` bigint(20) unsigned DEFAULT NULL,
  `modifiedby` bigint(20) unsigned DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `isactive` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `main_sd_depts` */

/*Table structure for table `main_sd_reqtypes` */

DROP TABLE IF EXISTS `main_sd_reqtypes`;

CREATE TABLE `main_sd_reqtypes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `service_desk_id` bigint(20) DEFAULT NULL,
  `service_request_name` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `createdby` bigint(20) DEFAULT NULL,
  `modifiedby` bigint(20) DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `isactive` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `main_sd_reqtypes` */

/*Table structure for table `main_sd_requests` */

DROP TABLE IF EXISTS `main_sd_requests`;

CREATE TABLE `main_sd_requests` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `request_for` tinyint(1) DEFAULT '1' COMMENT '1=service request, 2= asset request',
  `service_desk_id` bigint(20) unsigned DEFAULT NULL COMMENT 'if request_for is equal to 2 then dump id from asset table',
  `service_desk_conf_id` bigint(20) unsigned DEFAULT NULL,
  `service_request_id` bigint(20) unsigned DEFAULT NULL COMMENT 'If request_for is equal to 2 then dump category from asset table',
  `priority` tinyint(1) unsigned DEFAULT NULL COMMENT '1=low,2=medium,3=high',
  `description` varchar(250) DEFAULT NULL,
  `attachment` text,
  `status` enum('Open','Cancelled','Management approved','Management rejected','To management approve','Manager approved','Closed','Rejected','Manager rejected','To manager approve') DEFAULT NULL,
  `raised_by` bigint(20) unsigned DEFAULT NULL,
  `ticket_number` varchar(20) DEFAULT NULL,
  `executor_id` bigint(20) unsigned DEFAULT NULL,
  `executor_comments` varchar(250) DEFAULT NULL,
  `reporting_manager_id` bigint(20) unsigned DEFAULT NULL,
  `approver_status_1` enum('Approve','Reject','No answer') DEFAULT NULL,
  `approver_status_2` enum('Approve','Reject','No answer') DEFAULT NULL,
  `approver_status_3` enum('Approve','Reject','No answer') DEFAULT NULL,
  `reporting_manager_status` enum('Approve','Reject','No answer') DEFAULT NULL,
  `approver_1` bigint(20) unsigned DEFAULT NULL,
  `approver_2` bigint(20) unsigned DEFAULT NULL,
  `approver_3` bigint(20) unsigned DEFAULT NULL,
  `approver_1_comments` varchar(250) DEFAULT NULL,
  `approver_2_comments` varchar(250) DEFAULT NULL,
  `approver_3_comments` varchar(250) DEFAULT NULL,
  `reporting_manager_comments` varchar(250) DEFAULT NULL,
  `to_mgmt_comments` varchar(250) DEFAULT NULL,
  `to_manager_comments` varchar(250) DEFAULT NULL,
  `isactive` tinyint(1) unsigned DEFAULT NULL COMMENT '1= active,0=inactive',
  `createdby` bigint(20) unsigned DEFAULT NULL,
  `modifiedby` bigint(20) unsigned DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `main_sd_requests` */

/*Table structure for table `main_sd_requests_summary` */

DROP TABLE IF EXISTS `main_sd_requests_summary`;

CREATE TABLE `main_sd_requests_summary` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `request_for` tinyint(1) DEFAULT '1' COMMENT '1=service request,2=asset request',
  `sd_requests_id` bigint(20) DEFAULT NULL,
  `service_desk_id` bigint(20) unsigned DEFAULT NULL COMMENT 'If request_for equal to 2 then dump asset id from asset table',
  `service_desk_name` varchar(250) DEFAULT NULL COMMENT 'If request_for equal to 2 then dump asset name from asset table',
  `service_desk_conf_id` bigint(20) unsigned DEFAULT NULL,
  `service_request_name` varchar(250) DEFAULT NULL COMMENT 'If request_for equal to 2 then dump asset category from asset table',
  `service_request_id` bigint(20) unsigned DEFAULT NULL COMMENT 'If request_for equal to 2 then dump asset name from asset_categories table',
  `priority` tinyint(1) unsigned DEFAULT NULL COMMENT '1=low,2=medium,3=high',
  `description` varchar(250) DEFAULT NULL,
  `attachment` text,
  `status` varchar(35) DEFAULT NULL,
  `raised_by` bigint(20) unsigned DEFAULT NULL,
  `raised_by_name` varchar(250) DEFAULT NULL,
  `raised_by_empid` varchar(20) DEFAULT NULL,
  `ticket_number` varchar(20) DEFAULT NULL,
  `executor_id` bigint(20) unsigned DEFAULT NULL,
  `executor_name` varchar(250) DEFAULT NULL,
  `executor_comments` varchar(250) DEFAULT NULL,
  `reporting_manager_id` bigint(20) unsigned DEFAULT NULL,
  `reporting_manager_name` varchar(250) DEFAULT NULL,
  `approver_status_1` varchar(30) DEFAULT NULL,
  `approver_status_2` varchar(30) DEFAULT NULL,
  `approver_status_3` varchar(30) DEFAULT NULL,
  `reporting_manager_status` varchar(30) DEFAULT NULL,
  `approver_1` bigint(20) unsigned DEFAULT NULL,
  `approver_1_name` varchar(250) DEFAULT NULL,
  `approver_2` bigint(20) unsigned DEFAULT NULL,
  `approver_2_name` varchar(250) DEFAULT NULL,
  `approver_3` bigint(20) unsigned DEFAULT NULL,
  `approver_1_comments` varchar(250) DEFAULT NULL,
  `approver_2_comments` varchar(250) DEFAULT NULL,
  `approver_3_comments` varchar(250) DEFAULT NULL,
  `reporting_manager_comments` varchar(250) DEFAULT NULL,
  `to_mgmt_comments` varchar(250) DEFAULT NULL,
  `to_manager_comments` varchar(250) DEFAULT NULL,
  `approver_3_name` varchar(250) DEFAULT NULL,
  `isactive` tinyint(1) unsigned DEFAULT NULL COMMENT '1= active,0=inactive',
  `createdby` bigint(20) unsigned DEFAULT NULL,
  `modifiedby` bigint(20) unsigned DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `main_sd_requests_summary` */

/*Table structure for table `main_settings` */

DROP TABLE IF EXISTS `main_settings`;

CREATE TABLE `main_settings` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `menuid` varchar(100) NOT NULL,
  `userid` int(11) DEFAULT NULL,
  `flag` tinyint(4) DEFAULT NULL,
  `isactive` tinyint(4) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_userid_flag` (`userid`,`flag`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `main_settings` */

/*Table structure for table `main_sitepreference` */

DROP TABLE IF EXISTS `main_sitepreference`;

CREATE TABLE `main_sitepreference` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `nationalityid` int(11) unsigned DEFAULT NULL,
  `dateformatid` int(11) unsigned DEFAULT NULL,
  `timeformatid` int(11) unsigned DEFAULT NULL,
  `timezoneid` int(11) unsigned DEFAULT NULL,
  `currencyid` int(11) unsigned DEFAULT NULL,
  `passwordid` int(11) unsigned DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `createdby` int(11) unsigned DEFAULT NULL,
  `modifiedby` int(11) unsigned DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `isactive` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `main_sitepreference` */

/*Table structure for table `main_states` */

DROP TABLE IF EXISTS `main_states`;

CREATE TABLE `main_states` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `countryid` int(11) unsigned DEFAULT NULL,
  `state` varchar(255) NOT NULL,
  `statecode` varchar(255) DEFAULT NULL,
  `state_id_org` int(10) unsigned DEFAULT NULL,
  `createdby` int(11) unsigned DEFAULT NULL,
  `modifiedby` int(11) unsigned DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `isactive` tinyint(1) unsigned DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `NewIndex1` (`countryid`,`state_id_org`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `main_states` */

/*Table structure for table `main_timeformat` */

DROP TABLE IF EXISTS `main_timeformat`;

CREATE TABLE `main_timeformat` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `timeformat` varchar(60) NOT NULL,
  `mysql_timeformat` varchar(60) DEFAULT NULL,
  `js_timeformat` varchar(60) DEFAULT NULL,
  `example` varchar(60) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `createdby` int(11) unsigned DEFAULT NULL,
  `modifiedby` int(11) unsigned DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `isactive` tinyint(1) unsigned DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

/*Data for the table `main_timeformat` */

insert  into `main_timeformat`(`id`,`timeformat`,`mysql_timeformat`,`js_timeformat`,`example`,`description`,`createdby`,`modifiedby`,`createddate`,`modifieddate`,`isactive`) values (1,'h A',NULL,NULL,'9 AM','Hour only, with meridian',1,1,'2013-10-04 17:51:17','2013-10-04 17:51:17',1),(2,'h:i A',NULL,NULL,'9:10 AM','Hour and minutes, with meridian',1,1,'2013-10-04 17:51:17','2013-10-04 17:51:17',1),(3,'h:i:s A',NULL,NULL,'9:10:10 AM','Hour, minutes and seconds, with meridian',1,1,'2013-10-04 17:51:17','2013-10-04 17:51:17',1),(5,'H:i',NULL,NULL,'22:10','Hour and minutes, 24 Hours Notation',1,1,'2013-10-04 17:51:17','2013-10-04 17:51:17',1),(6,'H:i:s',NULL,NULL,'15:10:55','Hour, minutes and seconds, 24 Hours Notation',1,1,'2013-10-04 17:51:17','2013-10-04 17:51:17',1);

/*Table structure for table `main_timezone` */

DROP TABLE IF EXISTS `main_timezone`;

CREATE TABLE `main_timezone` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `actual_id` int(11) unsigned DEFAULT NULL,
  `timezone` varchar(255) NOT NULL,
  `timezone_abbr` varchar(10) DEFAULT NULL,
  `offet_value` varchar(100) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `createdby` int(11) unsigned DEFAULT NULL,
  `modifiedby` int(11) unsigned DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `isactive` tinyint(1) unsigned DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `main_timezone` */

/*Table structure for table `main_userloginlog` */

DROP TABLE IF EXISTS `main_userloginlog`;

CREATE TABLE `main_userloginlog` (
  `id` bigint(11) unsigned NOT NULL AUTO_INCREMENT,
  `userid` bigint(11) unsigned DEFAULT NULL,
  `emprole` int(11) unsigned DEFAULT NULL,
  `group_id` int(11) unsigned DEFAULT NULL,
  `employeeId` varchar(100) DEFAULT NULL,
  `emailaddress` varchar(200) DEFAULT NULL,
  `userfullname` varchar(100) DEFAULT NULL,
  `logindatetime` datetime DEFAULT NULL,
  `empipaddress` varchar(255) DEFAULT NULL,
  `profileimg` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `main_userloginlog` */

/*Table structure for table `main_users` */

DROP TABLE IF EXISTS `main_users`;

CREATE TABLE `main_users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `emprole` int(11) unsigned DEFAULT NULL,
  `userstatus` enum('new','old') DEFAULT 'new',
  `firstname` varchar(255) DEFAULT NULL,
  `lastname` varchar(255) DEFAULT NULL,
  `userfullname` varchar(255) DEFAULT NULL,
  `emailaddress` varchar(255) DEFAULT NULL,
  `contactnumber` varchar(15) DEFAULT NULL,
  `empipaddress` varchar(255) DEFAULT NULL,
  `backgroundchk_status` enum('In process','Completed','Not Applicable','Yet to start','On hold') DEFAULT 'Yet to start',
  `emptemplock` tinyint(1) unsigned DEFAULT '0',
  `empreasonlocked` varchar(255) DEFAULT NULL,
  `emplockeddate` date DEFAULT NULL,
  `emppassword` varchar(255) DEFAULT NULL,
  `createdby` int(11) unsigned DEFAULT NULL,
  `modifiedby` int(11) unsigned DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `isactive` smallint(6) unsigned DEFAULT '1' COMMENT '0=inactive,1-Active,2-resigned,3-left,4-suspended,5-deleted,',
  `employeeId` varchar(255) DEFAULT NULL,
  `modeofentry` varchar(255) DEFAULT NULL,
  `other_modeofentry` varchar(255) DEFAULT NULL,
  `entrycomments` varchar(255) DEFAULT NULL,
  `rccandidatename` int(11) unsigned DEFAULT NULL,
  `selecteddate` date DEFAULT NULL,
  `candidatereferredby` int(11) unsigned DEFAULT NULL,
  `company_id` int(11) unsigned DEFAULT NULL,
  `profileimg` varchar(255) DEFAULT NULL,
  `jobtitle_id` bigint(11) unsigned DEFAULT NULL,
  `tourflag` tinyint(1) unsigned DEFAULT '0' COMMENT '0=not seen,1=seen',
  `themes` enum('default','brown','gray','peacock','skyblue','green','orange') DEFAULT 'default',
  PRIMARY KEY (`id`),
  UNIQUE KEY `NewIndex1` (`employeeId`),
  KEY `IDX_4632B9B67F771501` (`emprole`),
  KEY `IDX_4632B9B6647385F4` (`rccandidatename`),
  KEY `IDX_4632B9B662E3F462` (`candidatereferredby`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

/*Data for the table `main_users` */

insert  into `main_users`(`id`,`emprole`,`userstatus`,`firstname`,`lastname`,`userfullname`,`emailaddress`,`contactnumber`,`empipaddress`,`backgroundchk_status`,`emptemplock`,`empreasonlocked`,`emplockeddate`,`emppassword`,`createdby`,`modifiedby`,`createddate`,`modifieddate`,`isactive`,`employeeId`,`modeofentry`,`other_modeofentry`,`entrycomments`,`rccandidatename`,`selecteddate`,`candidatereferredby`,`company_id`,`profileimg`,`jobtitle_id`,`tourflag`,`themes`) values (1,1,'old','Super','Admin','Super Admin','admin@example.com',NULL,NULL,'Not Applicable',0,NULL,NULL,'50b7deed0a684d599b1430fa7ae97d0d',NULL,NULL,'2013-11-21 00:00:00','2013-11-21 00:00:00',1,'EMPP0001',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,'default');

/*Table structure for table `main_veteranstatus` */

DROP TABLE IF EXISTS `main_veteranstatus`;

CREATE TABLE `main_veteranstatus` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `veteranstatus` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `createdby` int(11) unsigned DEFAULT NULL,
  `modifiedby` int(11) unsigned DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `isactive` tinyint(1) unsigned DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `main_veteranstatus` */

/*Table structure for table `main_weekdays` */

DROP TABLE IF EXISTS `main_weekdays`;

CREATE TABLE `main_weekdays` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `day_name` bigint(20) DEFAULT NULL,
  `dayshortcode` varchar(255) DEFAULT NULL,
  `daylongcode` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `createdby` int(11) unsigned DEFAULT NULL,
  `modifiedby` int(11) unsigned DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `isactive` tinyint(1) unsigned DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;

/*Data for the table `main_weekdays` */

insert  into `main_weekdays`(`id`,`day_name`,`dayshortcode`,`daylongcode`,`description`,`createdby`,`modifiedby`,`createddate`,`modifieddate`,`isactive`) values (1,0,'Su','Sun','Sunday',1,1,'2014-01-21 11:53:58','2014-01-21 11:53:58',1),(2,1,'Mo','Mon','Monday',1,1,'2014-01-21 11:54:22','2014-01-21 11:54:22',1),(3,2,'Tu','Tue','Tueday',1,1,'2014-01-21 11:54:39','2014-01-21 11:54:39',1),(4,3,'We','Wed','Wednesday',1,1,'2014-01-21 11:54:52','2014-01-21 11:54:52',1),(5,4,'Th','Thu','Thursday',1,1,'2014-01-21 11:55:24','2014-01-21 11:55:24',1),(6,5,'F','Fri','Friday',1,1,'2014-01-21 11:55:45','2014-01-21 11:55:45',1),(7,6,'Sa','Sat','Saturday',1,1,'2014-01-21 11:56:13','2014-01-21 11:56:13',1);

/*Table structure for table `main_wizard` */

DROP TABLE IF EXISTS `main_wizard`;

CREATE TABLE `main_wizard` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `manage_modules` tinyint(1) DEFAULT '1' COMMENT '1=No,2=Yes',
  `site_config` tinyint(1) DEFAULT '1' COMMENT '1=No,2=Yes',
  `org_details` tinyint(1) DEFAULT '1' COMMENT '1=No,2=Yes',
  `departments` tinyint(1) DEFAULT '1' COMMENT '1=No,2=Yes',
  `servicerequest` tinyint(1) DEFAULT '1' COMMENT '1=No,2=Yes',
  `country` bigint(20) unsigned DEFAULT NULL,
  `state` bigint(20) unsigned DEFAULT NULL,
  `city` bigint(20) unsigned DEFAULT NULL,
  `iscomplete` tinyint(1) DEFAULT '1' COMMENT '0=later,1=No,2=Yes',
  `createdby` bigint(20) unsigned DEFAULT NULL,
  `modifiedby` bigint(20) unsigned DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `isactive` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

/*Data for the table `main_wizard` */

insert  into `main_wizard`(`id`,`manage_modules`,`site_config`,`org_details`,`departments`,`servicerequest`,`country`,`state`,`city`,`iscomplete`,`createdby`,`modifiedby`,`createddate`,`modifieddate`,`isactive`) values (1,1,1,1,1,1,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL);

/*Table structure for table `main_workeligibilitydoctypes` */

DROP TABLE IF EXISTS `main_workeligibilitydoctypes`;

CREATE TABLE `main_workeligibilitydoctypes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `documenttype` varchar(255) DEFAULT NULL,
  `issuingauthority` tinyint(1) DEFAULT '1' COMMENT '1-country,2-state,3-city',
  `description` varchar(255) DEFAULT NULL,
  `createdby` int(11) unsigned DEFAULT NULL,
  `modifiedby` int(11) unsigned DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `isactive` tinyint(1) unsigned DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `main_workeligibilitydoctypes` */

/*Table structure for table `numbers` */

DROP TABLE IF EXISTS `numbers`;

CREATE TABLE `numbers` (
  `n` int(11) NOT NULL,
  PRIMARY KEY (`n`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `numbers` */

insert  into `numbers`(`n`) values (0),(1),(2),(3),(4),(5),(6),(7),(8),(9),(10),(11),(12),(13),(14),(15),(16),(17),(18),(19),(20),(21),(22),(23),(24),(25),(26),(27),(28),(29),(30),(31),(32),(33),(34),(35),(36),(37),(38),(39),(40),(41),(42),(43),(44),(45),(46),(47),(48),(49),(50),(51),(52),(53),(54),(55),(56),(57),(58),(59),(60),(61),(62),(63),(64),(65),(66),(67),(68),(69),(70),(71),(72),(73),(74),(75),(76),(77),(78),(79),(80),(81),(82),(83),(84),(85),(86),(87),(88),(89),(90),(91),(92),(93),(94),(95),(96),(97),(98),(99),(100),(101),(102),(103),(104),(105),(106),(107),(108),(109),(110),(111),(112),(113),(114),(115),(116),(117),(118),(119),(120),(121),(122),(123),(124),(125),(126),(127),(128),(129),(130),(131),(132),(133),(134),(135),(136),(137),(138),(139),(140),(141),(142),(143),(144),(145),(146),(147),(148),(149),(150),(151),(152),(153),(154),(155),(156),(157),(158),(159),(160),(161),(162),(163),(164),(165),(166),(167),(168),(169),(170),(171),(172),(173),(174),(175),(176),(177),(178),(179),(180),(181),(182),(183),(184),(185),(186),(187),(188),(189),(190),(191),(192),(193),(194),(195),(196),(197),(198),(199),(200),(201),(202),(203),(204),(205),(206),(207),(208),(209),(210),(211),(212),(213),(214),(215),(216),(217),(218),(219),(220),(221),(222),(223),(224),(225),(226),(227),(228),(229),(230),(231),(232),(233),(234),(235),(236),(237),(238),(239),(240),(241),(242),(243),(244),(245),(246),(247),(248),(249),(250),(251),(252),(253),(254),(255),(256),(257),(258),(259),(260),(261),(262),(263),(264),(265),(266),(267),(268),(269),(270),(271),(272),(273),(274),(275),(276),(277),(278),(279),(280),(281),(282),(283),(284),(285),(286),(287),(288),(289),(290),(291),(292),(293),(294),(295),(296),(297),(298),(299),(300),(301),(302),(303),(304),(305),(306),(307),(308),(309),(310),(311),(312),(313),(314),(315),(316),(317),(318),(319),(320),(321),(322),(323),(324),(325),(326),(327),(328),(329),(330),(331),(332),(333),(334),(335),(336),(337),(338),(339),(340),(341),(342),(343),(344),(345),(346),(347),(348),(349),(350),(351),(352),(353),(354),(355),(356),(357),(358),(359),(360),(361),(362),(363),(364),(365),(366),(367),(368),(369),(370),(371),(372),(373),(374),(375),(376),(377),(378),(379),(380),(381),(382),(383),(384),(385),(386),(387),(388),(389),(390),(391),(392),(393),(394),(395),(396),(397),(398),(399),(400);

/*Table structure for table `tbl_cities` */

DROP TABLE IF EXISTS `tbl_cities`;

CREATE TABLE `tbl_cities` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `state_id` bigint(20) unsigned NOT NULL,
  `city_name` varchar(30) NOT NULL,
  `is_active` tinyint(4) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `FK_tbl_cities_states` (`state_id`),
  CONSTRAINT `FK_tbl_cities_states` FOREIGN KEY (`state_id`) REFERENCES `tbl_states` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4080 DEFAULT CHARSET=utf8;

/*Data for the table `tbl_cities` */

insert  into `tbl_cities`(`id`,`state_id`,`city_name`,`is_active`,`created`,`modified`) values (1,4,'Kabul',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2,5,'Qandahar',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3,3,'Herat',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(4,2,'Mazar-e-Sharif',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(5,848,'Amsterdam',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(6,851,'Rotterdam',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(7,851,'Haag',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(8,850,'Utrecht',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(9,847,'Eindhoven',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(10,847,'Tilburg',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(11,845,'Groningen',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(12,847,'Breda',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(13,844,'Apeldoorn',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(14,844,'Nijmegen',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(15,849,'Enschede',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(16,848,'Haarlem',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(17,843,'Almere',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(18,844,'Arnhem',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(19,848,'Zaanstad',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(20,847,'Ã‚Â´s-Hertogenbosch',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(21,850,'Amersfoort',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(22,846,'Maastricht',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(23,851,'Dordrecht',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(24,851,'Leiden',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(25,848,'Haarlemmermeer',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(26,851,'Zoetermeer',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(27,842,'Emmen',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(28,849,'Zwolle',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(29,844,'Ede',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(30,851,'Delft',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(31,846,'Heerlen',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(32,848,'Alkmaar',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(33,13,'Willemstad',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(34,11,'Tirana',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(35,304,'Alger',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(36,315,'Oran',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(37,312,'Constantine',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(38,305,'Annaba',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(39,306,'Batna',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(40,316,'SÃƒÂ©tif',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(41,317,'Sidi Bel AbbÃƒÂ¨s',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(42,318,'Skikda',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(43,309,'Biskra',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(44,310,'Blida (el-Boulaida)',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(45,308,'BÃƒÂ©jaÃƒÂ¯a',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(46,314,'Mostaganem',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(47,319,'TÃƒÂ©bessa',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(48,321,'Tlemcen (Tilimsen)',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(49,307,'BÃƒÂ©char',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(50,320,'Tiaret',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(51,311,'Ech-Chleff (el-Asnam)',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(52,313,'GhardaÃƒÂ¯a',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(53,41,'Tafuna',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(54,41,'Fagatogo',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(55,12,'Andorra la Vella',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(56,8,'Luanda',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(57,7,'Huambo',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(58,6,'Lobito',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(59,6,'Benguela',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(60,9,'Namibe',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(61,10,'South Hill',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(62,10,'The Valley',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(63,42,'Saint JohnÃ‚Â´s',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(64,16,'Dubai',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(65,14,'Abu Dhabi',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(66,17,'Sharja',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(67,14,'al-Ayn',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(68,15,'Ajman',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(69,24,'Buenos Aires',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(70,18,'La Matanza',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(71,22,'CÃƒÂ³rdoba',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(72,35,'Rosario',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(73,18,'Lomas de Zamora',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(74,18,'Quilmes',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(75,18,'Almirante Brown',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(76,18,'La Plata',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(77,18,'Mar del Plata',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(78,37,'San Miguel de TucumÃƒÂ¡n',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(79,18,'LanÃƒÂºs',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(80,18,'Merlo',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(81,18,'General San MartÃƒÂ­n',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(82,32,'Salta',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(83,18,'Moreno',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(84,35,'Santa FÃƒÂ©',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(85,18,'Avellaneda',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(86,18,'Tres de Febrero',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(87,18,'MorÃƒÂ³n',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(88,18,'Florencio Varela',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(89,18,'San Isidro',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(90,18,'Tigre',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(91,18,'Malvinas Argentinas',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(92,18,'Vicente LÃƒÂ³pez',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(93,18,'Berazategui',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(94,23,'Corrientes',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(95,18,'San Miguel',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(96,18,'BahÃƒÂ­a Blanca',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(97,18,'Esteban EcheverrÃƒÂ­a',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(98,20,'Resistencia',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(99,18,'JosÃƒÂ© C. Paz',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(100,25,'ParanÃƒÂ¡',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(101,29,'Godoy Cruz',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(102,30,'Posadas',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(103,29,'GuaymallÃƒÂ©n',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(104,36,'Santiago del Estero',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(105,27,'San Salvador de Jujuy',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(106,18,'Hurlingham',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(107,31,'NeuquÃƒÂ©n',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(108,18,'ItuzaingÃƒÂ³',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(109,18,'San Fernando',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(110,26,'Formosa',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(111,29,'Las Heras',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(112,28,'La Rioja',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(113,19,'San Fernando del Valle de Cata',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(114,22,'RÃƒÂ­o Cuarto',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(115,21,'Comodoro Rivadavia',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(116,29,'Mendoza',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(117,18,'San NicolÃƒÂ¡s de los Arroyos',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(118,33,'San Juan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(119,18,'Escobar',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(120,25,'Concordia',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(121,18,'Pilar',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(122,34,'San Luis',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(123,18,'Ezeiza',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(124,29,'San Rafael',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(125,18,'Tandil',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(126,40,'Yerevan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(127,39,'Gjumri',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(128,38,'Vanadzor',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(129,1,'Oranjestad',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(130,44,'Sydney',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(131,48,'Melbourne',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(132,45,'Brisbane',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(133,49,'Perth',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(134,46,'Adelaide',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(135,43,'Canberra',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(136,45,'Gold Coast',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(137,44,'Newcastle',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(138,44,'Central Coast',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(139,44,'Wollongong',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(140,47,'Hobart',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(141,48,'Geelong',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(142,45,'Townsville',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(143,45,'Cairns',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(144,56,'Baku',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(145,57,'GÃƒÂ¤ncÃƒÂ¤',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(146,59,'Sumqayit',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(147,58,'MingÃƒÂ¤ÃƒÂ§evir',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(148,89,'Nassau',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(149,88,'al-Manama',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(150,77,'Dhaka',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(151,76,'Chittagong',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(152,78,'Khulna',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(153,79,'Rajshahi',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(154,77,'Narayanganj',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(155,79,'Rangpur',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(156,77,'Mymensingh',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(157,75,'Barisal',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(158,77,'Tungi',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(159,78,'Jessore',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(160,76,'Comilla',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(161,79,'Nawabganj',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(162,79,'Dinajpur',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(163,79,'Bogra',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(164,80,'Sylhet',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(165,76,'Brahmanbaria',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(166,77,'Tangail',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(167,77,'Jamalpur',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(168,79,'Pabna',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(169,79,'Naogaon',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(170,79,'Sirajganj',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(171,77,'Narsinghdi',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(172,79,'Saidpur',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(173,77,'Gazipur',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(174,137,'Bridgetown',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(175,61,'Antwerpen',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(176,63,'Gent',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(177,64,'Charleroi',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(178,65,'LiÃƒÂ¨ge',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(179,62,'Bruxelles [Brussel]',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(180,67,'Brugge',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(181,62,'Schaerbeek',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(182,66,'Namur',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(183,64,'Mons',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(184,99,'Belize City',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(185,100,'Belmopan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(186,69,'Cotonou',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(187,71,'Porto-Novo',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(188,68,'Djougou',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(189,70,'Parakou',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(190,102,'Saint George',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(191,101,'Hamilton',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(192,139,'Thimphu',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(193,108,'Santa Cruz de la Sierra',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(194,105,'La Paz',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(195,105,'El Alto',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(196,104,'Cochabamba',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(197,106,'Oruro',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(198,103,'Sucre',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(199,107,'PotosÃƒÂ­',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(200,109,'Tarija',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(201,90,'Sarajevo',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(202,91,'Banja Luka',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(203,90,'Zenica',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(204,141,'Gaborone',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(205,140,'Francistown',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(206,134,'SÃƒÂ£o Paulo',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(207,128,'Rio de Janeiro',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(208,114,'Salvador',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(209,122,'Belo Horizonte',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(210,115,'Fortaleza',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(211,116,'BrasÃƒÂ­lia',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(212,125,'Curitiba',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(213,126,'Recife',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(214,130,'Porto Alegre',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(215,113,'Manaus',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(216,123,'BelÃƒÂ©m',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(217,134,'Guarulhos',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(218,118,'GoiÃƒÂ¢nia',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(219,134,'Campinas',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(220,128,'SÃƒÂ£o GonÃƒÂ§alo',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(221,128,'Nova IguaÃƒÂ§u',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(222,119,'SÃƒÂ£o LuÃƒÂ­s',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(223,111,'MaceiÃƒÂ³',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(224,128,'Duque de Caxias',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(225,134,'SÃƒÂ£o Bernardo do Campo',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(226,127,'Teresina',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(227,129,'Natal',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(228,134,'Osasco',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(229,121,'Campo Grande',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(230,134,'Santo AndrÃƒÂ©',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(231,124,'JoÃƒÂ£o Pessoa',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(232,126,'JaboatÃƒÂ£o dos Guararapes',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(233,122,'Contagem',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(234,134,'SÃƒÂ£o JosÃƒÂ© dos Campos',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(235,122,'UberlÃƒÂ¢ndia',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(236,114,'Feira de Santana',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(237,134,'RibeirÃƒÂ£o Preto',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(238,134,'Sorocaba',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(239,128,'NiterÃƒÂ³i',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(240,120,'CuiabÃƒÂ¡',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(241,122,'Juiz de Fora',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(242,135,'Aracaju',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(243,128,'SÃƒÂ£o JoÃƒÂ£o de Meriti',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(244,125,'Londrina',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(245,133,'Joinville',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(246,128,'Belford Roxo',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(247,134,'Santos',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(248,123,'Ananindeua',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(249,128,'Campos dos Goytacazes',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(250,134,'MauÃƒÂ¡',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(251,134,'CarapicuÃƒÂ­ba',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(252,126,'Olinda',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(253,124,'Campina Grande',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(254,134,'SÃƒÂ£o JosÃƒÂ© do Rio Preto',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(255,130,'Caxias do Sul',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(256,134,'Moji das Cruzes',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(257,134,'Diadema',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(258,118,'Aparecida de GoiÃƒÂ¢nia',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(259,134,'Piracicaba',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(260,117,'Cariacica',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(261,117,'Vila Velha',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(262,130,'Pelotas',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(263,134,'Bauru',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(264,131,'Porto Velho',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(265,117,'Serra',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(266,122,'Betim',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(267,134,'JundÃƒÂ­aÃƒÂ­',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(268,130,'Canoas',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(269,134,'Franca',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(270,134,'SÃƒÂ£o Vicente',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(271,125,'MaringÃƒÂ¡',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(272,122,'Montes Claros',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(273,118,'AnÃƒÂ¡polis',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(274,133,'FlorianÃƒÂ³polis',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(275,128,'PetrÃƒÂ³polis',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(276,134,'Itaquaquecetuba',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(277,117,'VitÃƒÂ³ria',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(278,125,'Ponta Grossa',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(279,110,'Rio Branco',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(280,125,'Foz do IguaÃƒÂ§u',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(281,112,'MacapÃƒÂ¡',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(282,114,'IlhÃƒÂ©us',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(283,114,'VitÃƒÂ³ria da Conquista',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(284,122,'Uberaba',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(285,126,'Paulista',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(286,134,'Limeira',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(287,133,'Blumenau',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(288,126,'Caruaru',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(289,123,'SantarÃƒÂ©m',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(290,128,'Volta Redonda',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(291,130,'Novo Hamburgo',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(292,115,'Caucaia',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(293,130,'Santa Maria',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(294,125,'Cascavel',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(295,134,'GuarujÃƒÂ¡',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(296,122,'RibeirÃƒÂ£o das Neves',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(297,122,'Governador Valadares',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(298,134,'TaubatÃƒÂ©',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(299,119,'Imperatriz',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(300,130,'GravataÃƒÂ­',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(301,134,'Embu',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(302,129,'MossorÃƒÂ³',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(303,120,'VÃƒÂ¡rzea Grande',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(304,126,'Petrolina',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(305,134,'Barueri',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(306,130,'ViamÃƒÂ£o',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(307,122,'Ipatinga',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(308,114,'Juazeiro',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(309,115,'Juazeiro do Norte',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(310,134,'TaboÃƒÂ£o da Serra',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(311,125,'SÃƒÂ£o JosÃƒÂ© dos Pinhais',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(312,128,'MagÃƒÂ©',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(313,134,'Suzano',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(314,130,'SÃƒÂ£o Leopoldo',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(315,134,'MarÃƒÂ­lia',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(316,134,'SÃƒÂ£o Carlos',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(317,134,'SumarÃƒÂ©',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(318,134,'Presidente Prudente',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(319,122,'DivinÃƒÂ³polis',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(320,122,'Sete Lagoas',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(321,130,'Rio Grande',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(322,114,'Itabuna',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(323,114,'JequiÃƒÂ©',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(324,111,'Arapiraca',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(325,125,'Colombo',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(326,134,'Americana',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(327,130,'Alvorada',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(328,134,'Araraquara',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(329,128,'ItaboraÃƒÂ­',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(330,134,'Santa BÃƒÂ¡rbara dÃ‚Â´Oeste',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(331,128,'Nova Friburgo',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(332,134,'JacareÃƒÂ­',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(333,134,'AraÃƒÂ§atuba',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(334,128,'Barra Mansa',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(335,134,'Praia Grande',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(336,123,'MarabÃƒÂ¡',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(337,133,'CriciÃƒÂºma',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(338,132,'Boa Vista',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(339,130,'Passo Fundo',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(340,121,'Dourados',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(341,122,'Santa Luzia',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(342,134,'Rio Claro',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(343,115,'MaracanaÃƒÂº',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(344,125,'Guarapuava',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(345,120,'RondonÃƒÂ³polis',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(346,133,'SÃƒÂ£o JosÃƒÂ©',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(347,117,'Cachoeiro de Itapemirim',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(348,128,'NilÃƒÂ³polis',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(349,134,'Itapevi',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(350,126,'Cabo de Santo Agostinho',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(351,114,'CamaÃƒÂ§ari',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(352,115,'Sobral',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(353,133,'ItajaÃƒÂ­',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(354,133,'ChapecÃƒÂ³',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(355,134,'Cotia',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(356,133,'Lages',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(357,134,'Ferraz de Vasconcelos',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(358,134,'Indaiatuba',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(359,134,'HortolÃƒÂ¢ndia',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(360,119,'Caxias',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(361,134,'SÃƒÂ£o Caetano do Sul',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(362,134,'Itu',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(363,135,'Nossa Senhora do Socorro',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(364,127,'ParnaÃƒÂ­ba',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(365,122,'PoÃƒÂ§os de Caldas',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(366,128,'TeresÃƒÂ³polis',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(367,114,'Barreiras',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(368,123,'Castanhal',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(369,114,'Alagoinhas',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(370,134,'Itapecerica da Serra',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(371,130,'Uruguaiana',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(372,125,'ParanaguÃƒÂ¡',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(373,122,'IbiritÃƒÂ©',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(374,119,'Timon',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(375,118,'LuziÃƒÂ¢nia',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(376,128,'MacaÃƒÂ©',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(377,122,'TeÃƒÂ³filo Otoni',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(378,134,'Moji-GuaÃƒÂ§u',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(379,136,'Palmas',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(380,134,'Pindamonhangaba',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(381,134,'Francisco Morato',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(382,130,'BagÃƒÂ©',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(383,130,'Sapucaia do Sul',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(384,128,'Cabo Frio',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(385,134,'Itapetininga',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(386,122,'Patos de Minas',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(387,126,'Camaragibe',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(388,134,'BraganÃƒÂ§a Paulista',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(389,128,'Queimados',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(390,136,'AraguaÃƒÂ­na',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(391,126,'Garanhuns',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(392,126,'VitÃƒÂ³ria de Santo AntÃƒÂ£o',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(393,124,'Santa Rita',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(394,122,'Barbacena',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(395,123,'Abaetetuba',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(396,134,'JaÃƒÂº',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(397,114,'Lauro de Freitas',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(398,134,'Franco da Rocha',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(399,114,'Teixeira de Freitas',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(400,122,'Varginha',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(401,134,'RibeirÃƒÂ£o Pires',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(402,122,'SabarÃƒÂ¡',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(403,134,'Catanduva',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(404,118,'Rio Verde',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(405,134,'Botucatu',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(406,117,'Colatina',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(407,130,'Santa Cruz do Sul',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(408,117,'Linhares',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(409,125,'Apucarana',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(410,134,'Barretos',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(411,134,'GuaratinguetÃƒÂ¡',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(412,130,'Cachoeirinha',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(413,119,'CodÃƒÂ³',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(414,133,'JaraguÃƒÂ¡ do Sul',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(415,134,'CubatÃƒÂ£o',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(416,122,'Itabira',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(417,123,'Itaituba',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(418,134,'Araras',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(419,128,'Resende',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(420,134,'Atibaia',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(421,122,'Pouso Alegre',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(422,125,'Toledo',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(423,115,'Crato',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(424,122,'Passos',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(425,122,'Araguari',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(426,119,'SÃƒÂ£o JosÃƒÂ© de Ribamar',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(427,125,'Pinhais',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(428,134,'SertÃƒÂ£ozinho',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(429,122,'Conselheiro Lafaiete',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(430,114,'Paulo Afonso',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(431,128,'Angra dos Reis',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(432,114,'EunÃƒÂ¡polis',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(433,134,'Salto',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(434,134,'Ourinhos',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(435,129,'Parnamirim',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(436,114,'Jacobina',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(437,122,'Coronel Fabriciano',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(438,134,'Birigui',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(439,134,'TatuÃƒÂ­',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(440,131,'Ji-ParanÃƒÂ¡',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(441,119,'Bacabal',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(442,123,'CametÃƒÂ¡',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(443,130,'GuaÃƒÂ­ba',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(444,126,'SÃƒÂ£o LourenÃƒÂ§o da Mata',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(445,130,'Santana do Livramento',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(446,134,'Votorantim',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(447,125,'Campo Largo',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(448,124,'Patos',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(449,122,'Ituiutaba',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(450,121,'CorumbÃƒÂ¡',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(451,133,'PalhoÃƒÂ§a',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(452,128,'Barra do PiraÃƒÂ­',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(453,130,'Bento GonÃƒÂ§alves',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(454,134,'PoÃƒÂ¡',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(455,118,'Ãƒï¿½guas Lindas de GoiÃƒÂ¡s',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(456,412,'London',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(457,412,'Birmingham',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(458,415,'Glasgow',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(459,412,'Liverpool',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(460,415,'Edinburgh',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(461,412,'Sheffield',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(462,412,'Manchester',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(463,412,'Leeds',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(464,412,'Bristol',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(465,417,'Cardiff',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(466,412,'Coventry',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(467,412,'Leicester',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(468,412,'Bradford',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(469,414,'Belfast',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(470,412,'Nottingham',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(471,412,'Kingston upon Hull',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(472,412,'Plymouth',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(473,412,'Stoke-on-Trent',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(474,412,'Wolverhampton',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(475,412,'Derby',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(476,417,'Swansea',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(477,412,'Southampton',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(478,415,'Aberdeen',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(479,412,'Northampton',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(480,412,'Dudley',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(481,412,'Portsmouth',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(482,412,'Newcastle upon Tyne',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(483,412,'Sunderland',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(484,412,'Luton',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(485,412,'Swindon',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(486,412,'Southend-on-Sea',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(487,412,'Walsall',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(488,412,'Bournemouth',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(489,412,'Peterborough',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(490,412,'Brighton',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(491,412,'Blackpool',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(492,415,'Dundee',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(493,412,'West Bromwich',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(494,412,'Reading',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(495,412,'Oldbury/Smethwick (Warley)',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(496,412,'Middlesbrough',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(497,412,'Huddersfield',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(498,412,'Oxford',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(499,412,'Poole',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(500,412,'Bolton',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(501,412,'Blackburn',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(502,417,'Newport',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(503,412,'Preston',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(504,412,'Stockport',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(505,412,'Norwich',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(506,412,'Rotherham',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(507,412,'Cambridge',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(508,412,'Watford',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(509,412,'Ipswich',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(510,412,'Slough',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(511,412,'Exeter',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(512,412,'Cheltenham',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(513,412,'Gloucester',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(514,412,'Saint Helens',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(515,412,'Sutton Coldfield',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(516,412,'York',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(517,412,'Oldham',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(518,412,'Basildon',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(519,412,'Worthing',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(520,412,'Chelmsford',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(521,412,'Colchester',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(522,412,'Crawley',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(523,412,'Gillingham',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(524,412,'Solihull',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(525,412,'Rochdale',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(526,412,'Birkenhead',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(527,412,'Worcester',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(528,412,'Hartlepool',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(529,412,'Halifax',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(530,412,'Woking/Byfleet',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(531,412,'Southport',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(532,412,'Maidstone',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(533,412,'Eastbourne',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(534,412,'Grimsby',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(535,413,'Saint Helier',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(536,416,'Douglas',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(537,1362,'Road Town',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(538,138,'Bandar Seri Begawan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(539,82,'Sofija',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(540,85,'Plovdiv',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(541,87,'Varna',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(542,81,'Burgas',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(543,86,'Ruse',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(544,83,'Stara Zagora',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(545,84,'Pleven',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(546,81,'Sliven',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(547,87,'Dobric',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(548,87,'Ã…Â umen',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(549,74,'Ouagadougou',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(550,73,'Bobo-Dioulasso',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(551,72,'Koudougou',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(552,60,'Bujumbura',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(553,265,'George Town',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(554,167,'Santiago de Chile',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(555,167,'Puente Alto',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(556,169,'ViÃƒÂ±a del Mar',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(557,169,'ValparaÃƒÂ­so',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(558,160,'Talcahuano',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(559,158,'Antofagasta',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(560,167,'San Bernardo',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(561,162,'Temuco',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(562,160,'ConcepciÃƒÂ³n',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(563,166,'Rancagua',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(564,168,'Arica',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(565,165,'Talca',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(566,160,'ChillÃƒÂ¡n',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(567,168,'Iquique',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(568,160,'Los Angeles',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(569,163,'Puerto Montt',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(570,161,'Coquimbo',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(571,163,'Osorno',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(572,161,'La Serena',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(573,158,'Calama',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(574,163,'Valdivia',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(575,164,'Punta Arenas',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(576,159,'CopiapÃƒÂ³',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(577,169,'QuilpuÃƒÂ©',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(578,165,'CuricÃƒÂ³',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(579,161,'Ovalle',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(580,160,'Coronel',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(581,160,'San Pedro de la Paz',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(582,167,'Melipilla',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(583,224,'Avarua',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(584,250,'San JosÃƒÂ©',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(585,291,'Djibouti',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(586,292,'Roseau',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(587,298,'Santo Domingo de GuzmÃƒÂ¡n',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(588,303,'Santiago de los Caballeros',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(589,300,'La Romana',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(590,302,'San Pedro de MacorÃƒÂ­s',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(591,299,'San Francisco de MacorÃƒÂ­s',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(592,301,'San Felipe de Puerto Plata',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(593,326,'Guayaquil',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(594,331,'Quito',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(595,322,'Cuenca',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(596,324,'Machala',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(597,331,'Santo Domingo de los Colorados',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(598,330,'Portoviejo',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(599,332,'Ambato',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(600,330,'Manta',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(601,326,'Duran [Eloy Alfaro]',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(602,327,'Ibarra',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(603,329,'Quevedo',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(604,326,'Milagro',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(605,328,'Loja',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(606,323,'RÃƒÂ­obamba',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(607,325,'Esmeraldas',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(608,348,'Cairo',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(609,341,'Alexandria',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(610,345,'Giza',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(611,339,'Shubra al-Khayma',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(612,350,'Port Said',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(613,354,'Suez',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(614,336,'al-Mahallat al-Kubra',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(615,336,'Tanta',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(616,334,'al-Mansura',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(617,349,'Luxor',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(618,343,'Asyut',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(619,339,'Bahtim',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(620,340,'Zagazig',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(621,335,'al-Faiyum',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(622,346,'Ismailia',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(623,333,'Kafr al-Dawwar',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(624,342,'Assuan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(625,333,'Damanhur',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(626,338,'al-Minya',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(627,344,'Bani Suwayf',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(628,351,'Qina',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(629,352,'Sawhaj',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(630,337,'Shibin al-Kawm',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(631,345,'Bulaq al-Dakrur',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(632,339,'Banha',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(633,345,'Warraq al-Arab',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(634,347,'Kafr al-Shaykh',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(635,338,'Mallawi',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(636,340,'Bilbays',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(637,334,'Mit Ghamr',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(638,353,'al-Arish',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(639,334,'Talkha',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(640,339,'Qalyub',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(641,352,'Jirja',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(642,351,'Idfu',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(643,345,'al-Hawamidiya',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(644,347,'Disuq',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(645,1108,'San Salvador',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(646,1109,'Santa Ana',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(647,1108,'Mejicanos',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(648,1108,'Soyapango',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(649,1107,'San Miguel',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(650,1106,'Nueva San Salvador',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(651,1108,'Apopa',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(652,355,'Asmara',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(653,370,'Madrid',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(654,368,'Barcelona',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(655,373,'Valencia',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(656,357,'Sevilla',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(657,358,'Zaragoza',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(658,357,'MÃƒÂ¡laga',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(659,361,'Bilbao',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(660,362,'Las Palmas de Gran Canaria',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(661,371,'Murcia',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(662,360,'Palma de Mallorca',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(663,364,'Valladolid',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(664,357,'CÃƒÂ³rdoba',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(665,366,'Vigo',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(666,373,'Alicante [Alacant]',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(667,359,'GijÃƒÂ³n',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(668,368,'LÃ‚Â´Hospitalet de Llobregat',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(669,357,'Granada',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(670,366,'A CoruÃƒÂ±a (La CoruÃƒÂ±a)',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(671,361,'Vitoria-Gasteiz',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(672,362,'Santa Cruz de Tenerife',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(673,368,'Badalona',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(674,359,'Oviedo',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(675,370,'MÃƒÂ³stoles',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(676,373,'Elche [Elx]',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(677,368,'Sabadell',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(678,363,'Santander',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(679,357,'Jerez de la Frontera',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(680,372,'Pamplona [IruÃƒÂ±a]',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(681,361,'Donostia-San SebastiÃƒÂ¡n',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(682,371,'Cartagena',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(683,370,'LeganÃƒÂ©s',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(684,370,'Fuenlabrada',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(685,357,'AlmerÃƒÂ­a',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(686,368,'Terrassa',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(687,370,'AlcalÃƒÂ¡ de Henares',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(688,364,'Burgos',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(689,364,'Salamanca',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(690,367,'Albacete',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(691,370,'Getafe',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(692,357,'CÃƒÂ¡diz',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(693,370,'AlcorcÃƒÂ³n',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(694,357,'Huelva',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(695,364,'LeÃƒÂ³n',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(696,373,'CastellÃƒÂ³n de la Plana [Cast',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(697,365,'Badajoz',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(698,362,'[San CristÃƒÂ³bal de] la Lagun',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(699,369,'LogroÃƒÂ±o',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(700,368,'Santa Coloma de Gramenet',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(701,368,'Tarragona',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(702,368,'Lleida (LÃƒÂ©rida)',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(703,357,'JaÃƒÂ©n',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(704,366,'Ourense (Orense)',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(705,368,'MatarÃƒÂ³',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(706,357,'Algeciras',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(707,357,'Marbella',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(708,361,'Barakaldo',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(709,357,'Dos Hermanas',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(710,366,'Santiago de Compostela',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(711,370,'TorrejÃƒÂ³n de Ardoz',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(712,1405,'Cape Town',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(713,1400,'Soweto',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(714,1400,'Johannesburg',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(715,1398,'Port Elizabeth',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(716,1400,'Pretoria',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(717,1401,'Inanda',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(718,1401,'Durban',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(719,1400,'Vanderbijlpark',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(720,1400,'Kempton Park',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(721,1400,'Alberton',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(722,1401,'Pinetown',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(723,1401,'Pietermaritzburg',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(724,1400,'Benoni',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(725,1400,'Randburg',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(726,1401,'Umlazi',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(727,1399,'Bloemfontein',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(728,1400,'Vereeniging',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(729,1400,'Wonderboom',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(730,1400,'Roodepoort',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(731,1400,'Boksburg',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(732,1403,'Klerksdorp',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(733,1400,'Soshanguve',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(734,1401,'Newcastle',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(735,1398,'East London',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(736,1399,'Welkom',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(737,1404,'Kimberley',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(738,1398,'Uitenhage',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(739,1401,'Chatsworth',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(740,1398,'Mdantsane',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(741,1400,'Krugersdorp',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(742,1399,'Botshabelo',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(743,1400,'Brakpan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(744,1402,'Witbank',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(745,1400,'Oberholzer',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(746,1400,'Germiston',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(747,1400,'Springs',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(748,1400,'Westonaria',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(749,1400,'Randfontein',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(750,1405,'Paarl',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(751,1403,'Potchefstroom',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(752,1403,'Rustenburg',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(753,1400,'Nigel',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(754,1405,'George',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(755,1401,'Ladysmith',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(756,376,'Addis Abeba',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(757,378,'Dire Dawa',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(758,379,'Nazret',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(759,377,'Gonder',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(760,377,'Dese',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(761,380,'Mekele',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(762,377,'Bahir Dar',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(763,387,'Stanley',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(764,386,'Suva',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(765,905,'Quezon',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(766,905,'Manila',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(767,905,'Kalookan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(768,907,'Davao',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(769,902,'Cebu',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(770,909,'Zamboanga',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(771,905,'Pasig',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(772,905,'Valenzuela',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(773,905,'Las PiÃƒÂ±as',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(774,908,'Antipolo',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(775,905,'Taguig',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(776,906,'Cagayan de Oro',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(777,905,'ParaÃƒÂ±aque',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(778,905,'Makati',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(779,910,'Bacolod',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(780,907,'General Santos',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(781,905,'Marikina',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(782,908,'DasmariÃƒÂ±as',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(783,905,'Muntinlupa',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(784,910,'Iloilo',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(785,905,'Pasay',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(786,905,'Malabon',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(787,900,'San JosÃƒÂ© del Monte',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(788,908,'Bacoor',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(789,901,'Iligan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(790,908,'Calamba',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(791,905,'Mandaluyong',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(792,899,'Butuan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(793,900,'Angeles',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(794,900,'Tarlac',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(795,902,'Mandaue',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(796,898,'Baguio',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(797,908,'Batangas',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(798,908,'Cainta',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(799,908,'San Pedro',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(800,905,'Navotas',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(801,900,'Cabanatuan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(802,900,'San Fernando',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(803,908,'Lipa',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(804,902,'Lapu-Lapu',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(805,908,'San Pablo',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(806,908,'BiÃƒÂ±an',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(807,908,'Taytay',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(808,908,'Lucena',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(809,908,'Imus',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(810,900,'Olongapo',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(811,908,'Binangonan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(812,908,'Santa Rosa',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(813,907,'Tagum',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(814,903,'Tacloban',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(815,900,'Malolos',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(816,900,'Mabalacat',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(817,901,'Cotabato',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(818,900,'Meycauayan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(819,908,'Puerto Princesa',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(820,896,'Legazpi',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(821,908,'Silang',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(822,903,'Ormoc',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(823,904,'San Carlos',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(824,910,'Kabankalan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(825,902,'Talisay',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(826,906,'Valencia',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(827,903,'Calbayog',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(828,900,'Santa Maria',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(829,909,'Pagadian',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(830,910,'Cadiz',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(831,910,'Bago',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(832,902,'Toledo',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(833,896,'Naga',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(834,908,'San Mateo',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(835,907,'Panabo',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(836,907,'Koronadal',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(837,901,'Marawi',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(838,904,'Dagupan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(839,910,'Sagay',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(840,910,'Roxas',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(841,900,'Lubao',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(842,907,'Digos',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(843,900,'San Miguel',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(844,906,'Malaybalay',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(845,897,'Tuguegarao',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(846,897,'Ilagan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(847,900,'Baliuag',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(848,899,'Surigao',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(849,910,'San Carlos',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(850,905,'San Juan del Monte',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(851,908,'Tanauan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(852,900,'Concepcion',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(853,908,'Rodriguez (Montalban)',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(854,908,'Sariaya',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(855,904,'Malasiqui',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(856,908,'General Mariano Alvarez',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(857,904,'Urdaneta',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(858,900,'Hagonoy',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(859,908,'San Jose',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(860,907,'Polomolok',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(861,897,'Santiago',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(862,908,'Tanza',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(863,906,'Ozamis',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(864,900,'Mexico',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(865,900,'San Jose',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(866,910,'Silay',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(867,908,'General Trias',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(868,896,'Tabaco',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(869,908,'Cabuyao',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(870,908,'Calapan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(871,907,'Mati',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(872,901,'Midsayap',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(873,897,'Cauayan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(874,906,'Gingoog',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(875,902,'Dumaguete',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(876,904,'San Fernando',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(877,900,'Arayat',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(878,902,'Bayawan (Tulong)',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(879,901,'Kidapawan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(880,896,'Daraga (Locsin)',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(881,900,'Marilao',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(882,907,'Malita',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(883,909,'Dipolog',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(884,908,'Cavite',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(885,902,'Danao',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(886,899,'Bislig',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(887,900,'Talavera',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(888,900,'Guagua',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(889,904,'Bayambang',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(890,908,'Nasugbu',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(891,903,'Baybay',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(892,900,'Capas',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(893,895,'Sultan Kudarat',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(894,904,'Laoag',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(895,899,'Bayugan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(896,907,'Malungon',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(897,908,'Santa Cruz',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(898,896,'Sorsogon',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(899,908,'Candelaria',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(900,896,'Ligao',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(901,408,'TÃƒÂ³rshavn',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(902,411,'Libreville',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(903,432,'Serekunda',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(904,431,'Banjul',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(905,422,'Tbilisi',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(906,420,'Kutaisi',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(907,421,'Rustavi',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(908,419,'Batumi',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(909,418,'Sohumi',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(910,424,'Accra',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(911,423,'Kumasi',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(912,425,'Tamale',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(913,424,'Tema',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(914,426,'Sekondi-Takoradi',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(915,427,'Gibraltar',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(916,440,'Saint GeorgeÃ‚Â´s',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(917,441,'Nuuk',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(918,430,'Les Abymes',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(919,429,'Basse-Terre',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(920,445,'Tamuning',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(921,445,'AgaÃƒÂ±a',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(922,442,'Ciudad de Guatemala',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(923,442,'Mixco',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(924,442,'Villa Nueva',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(925,443,'Quetzaltenango',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(926,428,'Conakry',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(927,433,'Bissau',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(928,446,'Georgetown',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(929,457,'Port-au-Prince',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(930,457,'Carrefour',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(931,457,'Delmas',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(932,456,'Le-Cap-HaÃƒÂ¯tien',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(933,451,'Tegucigalpa',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(934,450,'San Pedro Sula',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(935,449,'La Ceiba',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(936,448,'Kowloon and New Kowloon',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(937,447,'Victoria',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(938,1103,'Longyearbyen',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(939,472,'Jakarta',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(940,471,'Surabaya',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(941,491,'Bandung',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(942,489,'Medan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(943,488,'Palembang',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(944,491,'Tangerang',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(945,470,'Semarang',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(946,483,'Ujung Pandang',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(947,471,'Malang',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(948,478,'Bandar Lampung',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(949,491,'Bekasi',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(950,487,'Padang',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(951,470,'Surakarta',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(952,475,'Banjarmasin',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(953,482,'Pekan Baru',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(954,468,'Denpasar',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(955,492,'Yogyakarta',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(956,474,'Pontianak',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(957,477,'Samarinda',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(958,473,'Jambi',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(959,491,'Depok',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(960,491,'Cimahi',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(961,477,'Balikpapan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(962,486,'Manado',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(963,480,'Mataram',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(964,470,'Pekalongan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(965,470,'Tegal',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(966,491,'Bogor',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(967,491,'Ciputat',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(968,491,'Pondokgede',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(969,491,'Cirebon',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(970,471,'Kediri',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(971,479,'Ambon',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(972,471,'Jember',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(973,470,'Cilacap',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(974,491,'Cimanggis',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(975,489,'Pematang Siantar',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(976,470,'Purwokerto',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(977,491,'Ciomas',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(978,491,'Tasikmalaya',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(979,471,'Madiun',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(980,469,'Bengkulu',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(981,491,'Karawang',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(982,467,'Banda Aceh',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(983,484,'Palu',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(984,471,'Pasuruan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(985,481,'Kupang',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(986,489,'Tebing Tinggi',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(987,489,'Percut Sei Tuan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(988,489,'Binjai',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(989,491,'Sukabumi',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(990,471,'Waru',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(991,488,'Pangkal Pinang',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(992,470,'Magelang',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(993,471,'Blitar',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(994,491,'Serang',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(995,471,'Probolinggo',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(996,491,'Cilegon',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(997,491,'Cianjur',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(998,491,'Ciparay',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(999,467,'Lhokseumawe',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1000,471,'Taman',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1001,492,'Depok',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1002,491,'Citeureup',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1003,470,'Pemalang',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1004,470,'Klaten',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1005,470,'Salatiga',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1006,491,'Cibinong',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1007,476,'Palangka Raya',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1008,471,'Mojokerto',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1009,491,'Purwakarta',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1010,491,'Garut',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1011,470,'Kudus',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1012,485,'Kendari',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1013,490,'Jaya Pura',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1014,486,'Gorontalo',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1015,491,'Majalaya',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1016,491,'Pondok Aren',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1017,471,'Jombang',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1018,489,'Sunggal',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1019,482,'Batam',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1020,489,'Padang Sidempuan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1021,491,'Sawangan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1022,471,'Banyuwangi',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1023,482,'Tanjung Pinang',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1024,506,'Mumbai (Bombay)',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1025,498,'Delhi',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1026,518,'Calcutta [Kolkata]',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1027,514,'Chennai (Madras)',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1028,493,'Hyderabad',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1029,499,'Ahmedabad',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1030,503,'Bangalore',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1031,516,'Kanpur',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1032,506,'Nagpur',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1033,516,'Lucknow',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1034,506,'Pune',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1035,499,'Surat',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1036,513,'Jaipur',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1037,505,'Indore',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1038,505,'Bhopal',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1039,512,'Ludhiana',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1040,499,'Vadodara (Baroda)',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1041,506,'Kalyan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1042,514,'Madurai',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1043,518,'Haora (Howrah)',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1044,516,'Varanasi (Benares)',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1045,495,'Patna',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1046,501,'Srinagar',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1047,516,'Agra',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1048,514,'Coimbatore',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1049,506,'Thane (Thana)',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1050,516,'Allahabad',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1051,516,'Meerut',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1052,493,'Vishakhapatnam',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1053,505,'Jabalpur',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1054,512,'Amritsar',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1055,500,'Faridabad',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1056,493,'Vijayawada',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1057,505,'Gwalior',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1058,513,'Jodhpur',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1059,506,'Nashik (Nasik)',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1060,503,'Hubli-Dharwad',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1061,506,'Solapur (Sholapur)',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1062,502,'Ranchi',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1063,516,'Bareilly',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1064,494,'Guwahati (Gauhati)',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1065,506,'Shambajinagar (Aurangabad)',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1066,504,'Cochin (Kochi)',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1067,499,'Rajkot',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1068,513,'Kota',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1069,504,'Thiruvananthapuram (Trivandrum',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1070,506,'Pimpri-Chinchwad',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1071,512,'Jalandhar (Jullundur)',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1072,516,'Gorakhpur',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1073,496,'Chandigarh',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1074,503,'Mysore',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1075,516,'Aligarh',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1076,493,'Guntur',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1077,502,'Jamshedpur',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1078,516,'Ghaziabad',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1079,493,'Warangal',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1080,497,'Raipur',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1081,516,'Moradabad',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1082,518,'Durgapur',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1083,506,'Amravati',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1084,504,'Calicut (Kozhikode)',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1085,513,'Bikaner',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1086,510,'Bhubaneswar',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1087,506,'Kolhapur',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1088,510,'Kataka (Cuttack)',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1089,513,'Ajmer',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1090,499,'Bhavnagar',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1091,514,'Tiruchirapalli',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1092,497,'Bhilai',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1093,506,'Bhiwandi',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1094,516,'Saharanpur',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1095,506,'Ulhasnagar',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1096,514,'Salem',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1097,505,'Ujjain',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1098,506,'Malegaon',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1099,499,'Jamnagar',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1100,502,'Bokaro Steel City',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1101,506,'Akola',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1102,503,'Belgaum',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1103,493,'Rajahmundry',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1104,493,'Nellore',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1105,513,'Udaipur',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1106,506,'New Bombay',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1107,518,'Bhatpara',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1108,503,'Gulbarga',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1109,498,'New Delhi',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1110,516,'Jhansi',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1111,495,'Gaya',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1112,493,'Kakinada',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1113,506,'Dhule (Dhulia)',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1114,518,'Panihati',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1115,506,'Nanded (Nander)',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1116,503,'Mangalore',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1117,517,'Dehra Dun',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1118,518,'Kamarhati',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1119,503,'Davangere',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1120,518,'Asansol',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1121,495,'Bhagalpur',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1122,503,'Bellary',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1123,518,'Barddhaman (Burdwan)',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1124,516,'Rampur',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1125,506,'Jalgaon',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1126,495,'Muzaffarpur',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1127,493,'Nizamabad',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1128,516,'Muzaffarnagar',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1129,512,'Patiala',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1130,516,'Shahjahanpur',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1131,493,'Kurnool',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1132,514,'Tiruppur (Tirupper)',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1133,500,'Rohtak',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1134,518,'South Dum Dum',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1135,516,'Mathura',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1136,506,'Chandrapur',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1137,518,'Barahanagar (Baranagar)',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1138,495,'Darbhanga',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1139,518,'Siliguri (Shiliguri)',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1140,510,'Raurkela',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1141,514,'Ambattur',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1142,500,'Panipat',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1143,516,'Firozabad',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1144,506,'Ichalkaranji',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1145,501,'Jammu',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1146,493,'Ramagundam',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1147,493,'Eluru',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1148,510,'Brahmapur',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1149,513,'Alwar',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1150,511,'Pondicherry',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1151,514,'Thanjavur',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1152,495,'Bihar Sharif',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1153,514,'Tuticorin',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1154,507,'Imphal',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1155,506,'Latur',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1156,505,'Sagar',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1157,516,'Farrukhabad-cum-Fatehgarh',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1158,506,'Sangli',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1159,506,'Parbhani',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1160,514,'Nagar Coil',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1161,503,'Bijapur',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1162,493,'Kukatpalle',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1163,518,'Bally',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1164,513,'Bhilwara',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1165,505,'Ratlam',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1166,514,'Avadi',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1167,514,'Dindigul',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1168,506,'Ahmadnagar',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1169,497,'Bilaspur',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1170,503,'Shimoga',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1171,518,'Kharagpur',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1172,506,'Mira Bhayandar',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1173,514,'Vellore',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1174,506,'Jalna',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1175,518,'Burnpur',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1176,493,'Anantapur',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1177,504,'Allappuzha (Alleppey)',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1178,493,'Tirupati',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1179,500,'Karnal',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1180,505,'Burhanpur',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1181,500,'Hisar (Hissar)',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1182,514,'Tiruvottiyur',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1183,516,'Mirzapur-cum-Vindhyachal',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1184,493,'Secunderabad',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1185,499,'Nadiad',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1186,505,'Dewas',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1187,505,'Murwara (Katni)',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1188,513,'Ganganagar',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1189,493,'Vizianagaram',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1190,514,'Erode',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1191,493,'Machilipatnam (Masulipatam)',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1192,512,'Bhatinda (Bathinda)',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1193,503,'Raichur',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1194,515,'Agartala',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1195,495,'Arrah (Ara)',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1196,505,'Satna',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1197,493,'Lalbahadur Nagar',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1198,509,'Aizawl',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1199,518,'Uluberia',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1200,495,'Katihar',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1201,514,'Cuddalore',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1202,518,'Hugli-Chinsurah',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1203,502,'Dhanbad',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1204,518,'Raiganj',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1205,516,'Sambhal',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1206,497,'Durg',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1207,495,'Munger (Monghyr)',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1208,514,'Kanchipuram',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1209,518,'North Dum Dum',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1210,493,'Karimnagar',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1211,513,'Bharatpur',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1212,513,'Sikar',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1213,517,'Hardwar (Haridwar)',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1214,518,'Dabgram',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1215,505,'Morena',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1216,516,'Noida',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1217,516,'Hapur',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1218,506,'Bhusawal',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1219,505,'Khandwa',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1220,500,'Yamuna Nagar',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1221,500,'Sonipat (Sonepat)',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1222,493,'Tenali',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1223,510,'Raurkela Civil Township',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1224,504,'Kollam (Quilon)',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1225,514,'Kumbakonam',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1226,518,'Ingraj Bazar (English Bazar)',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1227,503,'Timkur',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1228,516,'Amroha',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1229,518,'Serampore',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1230,495,'Chapra',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1231,513,'Pali',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1232,516,'Maunath Bhanjan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1233,493,'Adoni',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1234,516,'Jaunpur',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1235,514,'Tirunelveli',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1236,516,'Bahraich',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1237,503,'Gadag Betigeri',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1238,493,'Proddatur',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1239,493,'Chittoor',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1240,518,'Barrackpur',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1241,499,'Bharuch (Broach)',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1242,518,'Naihati',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1243,508,'Shillong',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1244,510,'Sambalpur',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1245,499,'Junagadh',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1246,516,'Rae Bareli',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1247,505,'Rewa',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1248,500,'Gurgaon',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1249,493,'Khammam',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1250,516,'Bulandshahr',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1251,499,'Navsari',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1252,493,'Malkajgiri',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1253,518,'Midnapore (Medinipur)',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1254,506,'Miraj',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1255,497,'Raj Nandgaon',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1256,514,'Alandur',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1257,510,'Puri',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1258,518,'Navadwip',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1259,500,'Sirsa',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1260,497,'Korba',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1261,516,'Faizabad',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1262,516,'Etawah',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1263,512,'Pathankot',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1264,499,'Gandhinagar',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1265,504,'Palghat (Palakkad)',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1266,499,'Veraval',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1267,512,'Hoshiarpur',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1268,500,'Ambala',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1269,516,'Sitapur',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1270,500,'Bhiwani',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1271,493,'Cuddapah',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1272,493,'Bhimavaram',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1273,518,'Krishnanagar',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1274,518,'Chandannagar',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1275,503,'Mandya',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1276,494,'Dibrugarh',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1277,493,'Nandyal',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1278,518,'Balurghat',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1279,514,'Neyveli',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1280,516,'Fatehpur',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1281,493,'Mahbubnagar',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1282,516,'Budaun',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1283,499,'Porbandar',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1284,494,'Silchar',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1285,518,'Berhampore (Baharampur)',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1286,502,'Purnea (Purnia)',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1287,518,'Bankura',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1288,514,'Rajapalaiyam',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1289,518,'Titagarh',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1290,518,'Halisahar',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1291,516,'Hathras',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1292,506,'Bhir (Bid)',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1293,514,'Pallavaram',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1294,499,'Anand',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1295,502,'Mango',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1296,518,'Santipur',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1297,505,'Bhind',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1298,506,'Gondiya',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1299,514,'Tiruvannamalai',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1300,506,'Yeotmal (Yavatmal)',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1301,518,'Kulti-Barakar',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1302,512,'Moga',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1303,505,'Shivapuri',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1304,503,'Bidar',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1305,493,'Guntakal',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1306,516,'Unnao',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1307,518,'Barasat',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1308,514,'Tambaram',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1309,512,'Abohar',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1310,516,'Pilibhit',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1311,514,'Valparai',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1312,516,'Gonda',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1313,499,'Surendranagar',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1314,493,'Qutubullapur',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1315,513,'Beawar',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1316,493,'Hindupur',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1317,499,'Gandhidham',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1318,517,'Haldwani-cum-Kathgodam',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1319,504,'Tellicherry (Thalassery)',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1320,506,'Wardha',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1321,518,'Rishra',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1322,499,'Bhuj',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1323,516,'Modinagar',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1324,493,'Gudivada',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1325,518,'Basirhat',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1326,518,'Uttarpara-Kotrung',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1327,493,'Ongole',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1328,518,'North Barrackpur',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1329,505,'Guna',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1330,518,'Haldia',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1331,518,'Habra',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1332,518,'Kanchrapara',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1333,513,'Tonk',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1334,518,'Champdani',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1335,516,'Orai',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1336,514,'Pudukkottai',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1337,495,'Sasaram',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1338,502,'Hazaribag',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1339,514,'Palayankottai',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1340,516,'Banda',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1341,499,'Godhra',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1342,503,'Hospet',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1343,518,'Ashoknagar-Kalyangarh',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1344,506,'Achalpur',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1345,499,'Patan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1346,505,'Mandasor',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1347,505,'Damoh',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1348,506,'Satara',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1349,516,'Meerut Cantonment',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1350,495,'Dehri',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1351,498,'Delhi Cantonment',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1352,505,'Chhindwara',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1353,518,'Bansberia',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1354,494,'Nagaon',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1355,516,'Kanpur Cantonment',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1356,505,'Vidisha',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1357,495,'Bettiah',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1358,502,'Purulia',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1359,503,'Hassan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1360,500,'Ambala Sadar',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1361,518,'Baidyabati',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1362,499,'Morvi',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1363,497,'Raigarh',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1364,499,'Vejalpur',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1365,554,'Baghdad',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1366,561,'Mosul',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1367,558,'Irbil',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1368,552,'Kirkuk',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1369,555,'Basra',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1370,551,'al-Sulaymaniya',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1371,549,'al-Najaf',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1372,559,'Karbala',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1373,553,'al-Hilla',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1374,556,'al-Nasiriya',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1375,560,'al-Amara',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1376,550,'al-Diwaniya',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1377,548,'al-Ramadi',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1378,562,'al-Kut',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1379,557,'Baquba',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1380,544,'Teheran',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1381,534,'Mashhad',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1382,525,'Esfahan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1383,524,'Tabriz',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1384,526,'Shiraz',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1385,544,'Karaj',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1386,535,'Ahvaz',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1387,541,'Qom',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1388,533,'Kermanshah',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1389,545,'Urmia',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1390,543,'Zahedan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1391,527,'Rasht',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1392,529,'Hamadan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1393,532,'Kerman',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1394,538,'Arak',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1395,521,'Ardebil',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1396,546,'Yazd',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1397,540,'Qazvin',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1398,547,'Zanjan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1399,536,'Sanandaj',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1400,530,'Bandar-e-Abbas',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1401,537,'Khorramabad',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1402,544,'Eslamshahr',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1403,537,'Borujerd',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1404,535,'Abadan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1405,535,'Dezful',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1406,525,'Kashan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1407,539,'Sari',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1408,528,'Gorgan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1409,525,'Najafabad',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1410,534,'Sabzevar',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1411,525,'Khomeynishahr',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1412,539,'Amol',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1413,534,'Neyshabur',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1414,539,'Babol',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1415,545,'Khoy',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1416,529,'Malayer',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1417,522,'Bushehr',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1418,539,'Qaemshahr',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1419,544,'Qarchak',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1420,544,'Qods',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1421,532,'Sirjan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1422,534,'Bojnurd',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1423,524,'Maragheh',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1424,534,'Birjand',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1425,531,'Ilam',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1426,545,'Bukan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1427,535,'Masjed-e-Soleyman',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1428,536,'Saqqez',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1429,539,'Gonbad-e Qabus',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1430,541,'Saveh',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1431,545,'Mahabad',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1432,544,'Varamin',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1433,535,'Andimeshk',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1434,535,'Khorramshahr',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1435,542,'Shahrud',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1436,526,'Marv Dasht',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1437,543,'Zabol',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1438,523,'Shahr-e Kord',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1439,527,'Bandar-e Anzali',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1440,532,'Rafsanjan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1441,524,'Marand',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1442,534,'Torbat-e Heydariyeh',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1443,526,'Jahrom',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1444,542,'Semnan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1445,545,'Miandoab',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1446,525,'Qomsheh',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1447,519,'Dublin',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1448,520,'Cork',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1449,563,'ReykjavÃƒÂ­k',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1450,567,'Jerusalem',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1451,568,'Tel Aviv-Jaffa',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1452,566,'Haifa',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1453,565,'Rishon Le Ziyyon',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1454,564,'Beerseba',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1455,568,'Holon',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1456,565,'Petah Tiqwa',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1457,564,'Ashdod',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1458,565,'Netanya',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1459,568,'Bat Yam',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1460,568,'Bene Beraq',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1461,568,'Ramat Gan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1462,564,'Ashqelon',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1463,565,'Rehovot',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1464,575,'Roma',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1465,577,'Milano',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1466,572,'Napoli',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1467,579,'Torino',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1468,581,'Palermo',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1469,576,'Genova',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1470,573,'Bologna',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1471,582,'Firenze',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1472,581,'Catania',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1473,570,'Bari',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1474,585,'Venezia',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1475,581,'Messina',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1476,585,'Verona',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1477,574,'Trieste',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1478,585,'Padova',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1479,570,'Taranto',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1480,577,'Brescia',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1481,571,'Reggio di Calabria',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1482,573,'Modena',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1483,582,'Prato',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1484,573,'Parma',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1485,580,'Cagliari',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1486,582,'Livorno',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1487,584,'Perugia',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1488,570,'Foggia',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1489,573,'Reggio nellÃ‚Â´ Emilia',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1490,572,'Salerno',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1491,573,'Ravenna',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1492,573,'Ferrara',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1493,573,'Rimini',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1494,581,'Syrakusa',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1495,580,'Sassari',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1496,577,'Monza',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1497,577,'Bergamo',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1498,569,'Pescara',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1499,575,'Latina',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1500,585,'Vicenza',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1501,584,'Terni',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1502,573,'ForlÃƒÂ¬',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1503,583,'Trento',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1504,579,'Novara',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1505,573,'Piacenza',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1506,578,'Ancona',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1507,570,'Lecce',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1508,583,'Bolzano',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1509,571,'Catanzaro',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1510,576,'La Spezia',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1511,574,'Udine',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1512,572,'Torre del Greco',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1513,570,'Andria',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1514,570,'Brindisi',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1515,572,'Giugliano in Campania',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1516,582,'Pisa',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1517,570,'Barletta',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1518,582,'Arezzo',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1519,579,'Alessandria',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1520,573,'Cesena',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1521,578,'Pesaro',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1522,1166,'Dili',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1523,55,'Wien',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1524,53,'Graz',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1525,51,'Linz',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1526,52,'Salzburg',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1527,54,'Innsbruck',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1528,50,'Klagenfurt',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1529,587,'Spanish Town',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1530,586,'Kingston',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1531,586,'Portmore',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1532,631,'Tokyo',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1533,609,'Jokohama [Yokohama]',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1534,623,'Osaka',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1535,591,'Nagoya',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1536,602,'Sapporo',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1537,612,'Kioto',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1538,603,'Kobe',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1539,597,'Fukuoka',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1540,609,'Kawasaki',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1541,601,'Hiroshima',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1542,597,'Kitakyushu',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1543,614,'Sendai',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1544,594,'Chiba',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1545,623,'Sakai',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1546,611,'Kumamoto',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1547,621,'Okayama',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1548,609,'Sagamihara',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1549,628,'Hamamatsu',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1550,608,'Kagoshima',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1551,594,'Funabashi',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1552,623,'Higashiosaka',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1553,631,'Hachioji',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1554,619,'Niigata',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1555,603,'Amagasaki',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1556,603,'Himeji',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1557,628,'Shizuoka',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1558,625,'Urawa',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1559,595,'Matsuyama',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1560,594,'Matsudo',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1561,605,'Kanazawa',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1562,625,'Kawaguchi',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1563,594,'Ichikawa',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1564,625,'Omiya',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1565,629,'Utsunomiya',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1566,620,'Oita',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1567,617,'Nagasaki',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1568,609,'Yokosuka',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1569,621,'Kurashiki',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1570,599,'Gifu',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1571,623,'Hirakata',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1572,603,'Nishinomiya',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1573,623,'Toyonaka',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1574,634,'Wakayama',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1575,601,'Fukuyama',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1576,609,'Fujisawa',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1577,602,'Asahikawa',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1578,631,'Machida',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1579,618,'Nara',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1580,623,'Takatsuki',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1581,598,'Iwaki',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1582,616,'Nagano',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1583,591,'Toyohashi',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1584,591,'Toyota',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1585,623,'Suita',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1586,607,'Takamatsu',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1587,598,'Koriyama',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1588,591,'Okazaki',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1589,625,'Kawagoe',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1590,625,'Tokorozawa',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1591,633,'Toyama',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1592,610,'Kochi',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1593,594,'Kashiwa',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1594,592,'Akita',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1595,615,'Miyazaki',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1596,625,'Koshigaya',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1597,622,'Naha',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1598,593,'Aomori',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1599,602,'Hakodate',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1600,603,'Akashi',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1601,613,'Yokkaichi',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1602,598,'Fukushima',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1603,606,'Morioka',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1604,600,'Maebashi',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1605,591,'Kasugai',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1606,626,'Otsu',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1607,594,'Ichihara',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1608,623,'Yao',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1609,591,'Ichinomiya',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1610,630,'Tokushima',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1611,603,'Kakogawa',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1612,623,'Ibaraki',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1613,623,'Neyagawa',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1614,636,'Shimonoseki',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1615,635,'Yamagata',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1616,596,'Fukui',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1617,609,'Hiratsuka',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1618,604,'Mito',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1619,617,'Sasebo',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1620,593,'Hachinohe',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1621,600,'Takasaki',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1622,628,'Shimizu',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1623,597,'Kurume',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1624,628,'Fuji',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1625,625,'Soka',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1626,631,'Fuchu',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1627,609,'Chigasaki',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1628,609,'Atsugi',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1629,628,'Numazu',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1630,625,'Ageo',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1631,609,'Yamato',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1632,616,'Matsumoto',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1633,601,'Kure',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1634,603,'Takarazuka',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1635,625,'Kasukabe',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1636,631,'Chofu',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1637,609,'Odawara',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1638,637,'Kofu',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1639,602,'Kushiro',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1640,623,'Kishiwada',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1641,604,'Hitachi',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1642,619,'Nagaoka',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1643,603,'Itami',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1644,612,'Uji',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1645,613,'Suzuka',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1646,593,'Hirosaki',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1647,636,'Ube',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1648,631,'Kodaira',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1649,633,'Takaoka',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1650,602,'Obihiro',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1651,602,'Tomakomai',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1652,624,'Saga',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1653,594,'Sakura',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1654,609,'Kamakura',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1655,631,'Mitaka',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1656,623,'Izumi',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1657,631,'Hino',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1658,609,'Hadano',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1659,629,'Ashikaga',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1660,613,'Tsu',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1661,625,'Sayama',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1662,594,'Yachiyo',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1663,604,'Tsukuba',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1664,631,'Tachikawa',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1665,625,'Kumagaya',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1666,623,'Moriguchi',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1667,602,'Otaru',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1668,591,'Anjo',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1669,594,'Narashino',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1670,629,'Oyama',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1671,599,'Ogaki',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1672,627,'Matsue',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1673,603,'Kawanishi',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1674,631,'Hitachinaka',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1675,625,'Niiza',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1676,594,'Nagareyama',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1677,632,'Tottori',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1678,604,'Tama',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1679,625,'Iruma',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1680,600,'Ota',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1681,597,'Omuta',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1682,591,'Komaki',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1683,631,'Ome',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1684,623,'Kadoma',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1685,636,'Yamaguchi',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1686,631,'Higashimurayama',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1687,632,'Yonago',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1688,623,'Matsubara',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1689,631,'Musashino',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1690,604,'Tsuchiura',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1691,619,'Joetsu',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1692,615,'Miyakonojo',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1693,625,'Misato',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1694,599,'Kakamigahara',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1695,623,'Daito',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1696,591,'Seto',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1697,591,'Kariya',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1698,594,'Urayasu',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1699,620,'Beppu',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1700,595,'Niihama',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1701,623,'Minoo',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1702,628,'Fujieda',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1703,594,'Abiko',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1704,615,'Nobeoka',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1705,623,'Tondabayashi',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1706,616,'Ueda',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1707,618,'Kashihara',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1708,613,'Matsusaka',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1709,600,'Isesaki',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1710,609,'Zama',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1711,594,'Kisarazu',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1712,594,'Noda',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1713,614,'Ishinomaki',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1714,628,'Fujinomiya',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1715,623,'Kawachinagano',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1716,595,'Imabari',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1717,598,'Aizuwakamatsu',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1718,601,'Higashihiroshima',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1719,623,'Habikino',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1720,602,'Ebetsu',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1721,636,'Hofu',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1722,600,'Kiryu',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1723,622,'Okinawa',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1724,628,'Yaizu',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1725,591,'Toyokawa',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1726,609,'Ebina',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1727,625,'Asaka',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1728,631,'Higashikurume',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1729,618,'Ikoma',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1730,602,'Kitami',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1731,631,'Koganei',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1732,625,'Iwatsuki',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1733,628,'Mishima',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1734,591,'Handa',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1735,602,'Muroran',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1736,605,'Komatsu',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1737,611,'Yatsushiro',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1738,616,'Iida',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1739,636,'Tokuyama',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1740,631,'Kokubunji',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1741,631,'Akishima',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1742,636,'Iwakuni',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1743,626,'Kusatsu',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1744,613,'Kuwana',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1745,603,'Sanda',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1746,626,'Hikone',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1747,625,'Toda',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1748,599,'Tajimi',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1749,623,'Ikeda',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1750,625,'Fukaya',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1751,613,'Ise',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1752,635,'Sakata',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1753,597,'Kasuga',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1754,594,'Kamagaya',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1755,635,'Tsuruoka',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1756,631,'Hoya',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1757,594,'Nishio',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1758,591,'Tokai',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1759,591,'Inazawa',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1760,625,'Sakado',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1761,609,'Isehara',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1762,603,'Takasago',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1763,625,'Fujimi',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1764,622,'Urasoe',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1765,635,'Yonezawa',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1766,591,'Konan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1767,618,'Yamatokoriyama',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1768,612,'Maizuru',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1769,601,'Onomichi',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1770,625,'Higashimatsuyama',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1771,594,'Kimitsu',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1772,617,'Isahaya',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1773,629,'Kanuma',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1774,623,'Izumisano',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1775,612,'Kameoka',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1776,594,'Mobara',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1777,594,'Narita',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1778,619,'Kashiwazaki',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1779,621,'Tsuyama',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1780,1392,'Sanaa',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1781,1388,'Aden',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1782,1393,'Taizz',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1783,1390,'Hodeida',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1784,1389,'al-Mukalla',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1785,1391,'Ibb',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1786,589,'Amman',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1787,588,'al-Zarqa',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1788,590,'Irbid',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1789,588,'al-Rusayfa',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1790,589,'Wadi al-Sir',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1791,264,'Flying Fish Cove',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1792,1394,'Beograd',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1793,1397,'Novi Sad',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1794,1394,'NiÃ…Â¡',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1795,1395,'PriÃ…Â¡tina',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1796,1394,'Kragujevac',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1797,1396,'Podgorica',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1798,1397,'Subotica',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1799,1395,'Prizren',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1800,662,'Phnom Penh',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1801,661,'Battambang',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1802,663,'Siem Reap',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1803,208,'Douala',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1804,206,'YaoundÃƒÂ©',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1805,209,'Garoua',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1806,207,'Maroua',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1807,210,'Bamenda',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1808,211,'Bafoussam',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1809,208,'Nkongsamba',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1810,149,'MontrÃƒÂ©al',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1811,143,'Calgary',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1812,148,'Toronto',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1813,148,'North York',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1814,145,'Winnipeg',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1815,143,'Edmonton',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1816,148,'Mississauga',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1817,148,'Scarborough',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1818,144,'Vancouver',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1819,148,'Etobicoke',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1820,148,'London',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1821,148,'Hamilton',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1822,148,'Ottawa',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1823,149,'Laval',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1824,144,'Surrey',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1825,148,'Brampton',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1826,148,'Windsor',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1827,150,'Saskatoon',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1828,148,'Kitchener',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1829,148,'Markham',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1830,150,'Regina',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1831,144,'Burnaby',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1832,149,'QuÃƒÂ©bec',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1833,148,'York',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1834,144,'Richmond',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1835,148,'Vaughan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1836,148,'Burlington',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1837,148,'Oshawa',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1838,148,'Oakville',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1839,148,'Saint Catharines',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1840,149,'Longueuil',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1841,148,'Richmond Hill',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1842,148,'Thunder Bay',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1843,148,'Nepean',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1844,147,'Cape Breton',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1845,148,'East York',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1846,147,'Halifax',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1847,148,'Cambridge',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1848,148,'Gloucester',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1849,144,'Abbotsford',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1850,148,'Guelph',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1851,146,'Saint JohnÃ‚Â´s',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1852,144,'Coquitlam',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1853,144,'Saanich',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1854,149,'Gatineau',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1855,144,'Delta',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1856,148,'Sudbury',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1857,144,'Kelowna',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1858,148,'Barrie',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1859,249,'Praia',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1860,639,'Almaty',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1861,647,'Qaraghandy',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1862,650,'Shymkent',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1863,651,'Taraz',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1864,641,'Astana',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1865,643,'Ãƒâ€“skemen',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1866,646,'Pavlodar',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1867,643,'Semey',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1868,640,'AqtÃƒÂ¶be',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1869,648,'Qostanay',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1870,645,'Petropavl',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1871,652,'Oral',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1872,647,'Temirtau',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1873,649,'Qyzylorda',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1874,644,'Aqtau',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1875,642,'Atyrau',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1876,646,'Ekibastuz',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1877,645,'KÃƒÂ¶kshetau',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1878,648,'Rudnyy',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1879,638,'Taldyqorghan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1880,647,'Zhezqazghan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1881,656,'Nairobi',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1882,654,'Mombasa',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1883,657,'Kisumu',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1884,658,'Nakuru',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1885,655,'Machakos',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1886,658,'Eldoret',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1887,655,'Meru',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1888,653,'Nyeri',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1889,142,'Bangui',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1890,193,'Shanghai',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1891,189,'Peking',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1892,171,'Chongqing',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1893,196,'Tianjin',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1894,181,'Wuhan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1895,179,'Harbin',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1896,187,'Shenyang',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1897,174,'Kanton [Guangzhou]',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1898,195,'Chengdu',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1899,184,'Nanking [Nanjing]',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1900,186,'Changchun',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1901,191,'XiÃ‚Â´an',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1902,187,'Dalian',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1903,192,'Qingdao',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1904,192,'Jinan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1905,200,'Hangzhou',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1906,180,'Zhengzhou',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1907,178,'Shijiazhuang',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1908,194,'Taiyuan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1909,199,'Kunming',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1910,182,'Changsha',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1911,185,'Nanchang',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1912,172,'Fuzhou',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1913,173,'Lanzhou',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1914,176,'Guiyang',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1915,200,'Ningbo',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1916,170,'Hefei',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1917,198,'UrumtÃ…Â¡i [ÃƒÅ“rÃƒÂ¼mqi]',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1918,187,'Anshan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1919,187,'Fushun',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1920,175,'Nanning',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1921,192,'Zibo',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1922,179,'Qiqihar',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1923,186,'Jilin',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1924,178,'Tangshan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1925,183,'Baotou',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1926,174,'Shenzhen',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1927,183,'Hohhot',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1928,178,'Handan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1929,184,'Wuxi',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1930,184,'Xuzhou',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1931,194,'Datong',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1932,179,'Yichun',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1933,187,'Benxi',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1934,180,'Luoyang',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1935,184,'Suzhou',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1936,190,'Xining',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1937,170,'Huainan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1938,179,'Jixi',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1939,179,'Daqing',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1940,187,'Fuxin',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1941,172,'Amoy [Xiamen]',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1942,175,'Liuzhou',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1943,174,'Shantou',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1944,187,'Jinzhou',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1945,179,'Mudanjiang',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1946,188,'Yinchuan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1947,184,'Changzhou',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1948,178,'Zhangjiakou',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1949,187,'Dandong',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1950,179,'Hegang',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1951,180,'Kaifeng',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1952,179,'Jiamusi',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1953,187,'Liaoyang',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1954,182,'Hengyang',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1955,178,'Baoding',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1956,186,'Hunjiang',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1957,180,'Xinxiang',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1958,181,'Huangshi',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1959,177,'Haikou',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1960,192,'Yantai',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1961,170,'Bengbu',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1962,182,'Xiangtan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1963,192,'Weifang',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1964,170,'Wuhu',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1965,185,'Pingxiang',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1966,187,'Yingkou',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1967,180,'Anyang',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1968,195,'Panzhihua',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1969,180,'Pingdingshan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1970,181,'Xiangfan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1971,182,'Zhuzhou',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1972,180,'Jiaozuo',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1973,200,'Wenzhou',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1974,174,'Zhangjiang',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1975,195,'Zigong',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1976,179,'Shuangyashan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1977,192,'Zaozhuang',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1978,183,'Yakeshi',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1979,181,'Yichang',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1980,184,'Zhenjiang',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1981,170,'Huaibei',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1982,178,'Qinhuangdao',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1983,175,'Guilin',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1984,176,'Liupanshui',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1985,187,'Panjin',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1986,194,'Yangquan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1987,187,'Jinxi',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1988,186,'Liaoyuan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1989,184,'Lianyungang',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1990,191,'Xianyang',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1991,192,'TaiÃ‚Â´an',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1992,183,'Chifeng',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1993,174,'Shaoguan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1994,184,'Nantong',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1995,195,'Leshan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1996,191,'Baoji',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1997,192,'Linyi',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1998,186,'Tonghua',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(1999,186,'Siping',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2000,194,'Changzhi',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2001,192,'Tengzhou',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2002,174,'Chaozhou',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2003,184,'Yangzhou',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2004,174,'Dongwan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2005,170,'MaÃ‚Â´anshan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2006,174,'Foshan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2007,182,'Yueyang',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2008,178,'Xingtai',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2009,182,'Changde',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2010,198,'Shihezi',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2011,184,'Yancheng',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2012,185,'Jiujiang',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2013,192,'Dongying',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2014,181,'Shashi',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2015,192,'Xintai',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2016,185,'Jingdezhen',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2017,191,'Tongchuan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2018,174,'Zhongshan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2019,181,'Shiyan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2020,179,'Tieli',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2021,192,'Jining',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2022,183,'Wuhai',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2023,195,'Mianyang',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2024,195,'Luzhou',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2025,176,'Zunyi',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2026,188,'Shizuishan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2027,195,'Neijiang',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2028,183,'Tongliao',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2029,187,'Tieling',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2030,187,'Wafangdian',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2031,170,'Anqing',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2032,182,'Shaoyang',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2033,192,'Laiwu',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2034,178,'Chengde',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2035,173,'Tianshui',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2036,180,'Nanyang',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2037,178,'Cangzhou',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2038,195,'Yibin',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2039,184,'Huaiyin',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2040,186,'Dunhua',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2041,186,'Yanji',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2042,174,'Jiangmen',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2043,170,'Tongling',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2044,179,'Suihua',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2045,186,'Gongziling',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2046,181,'Xiantao',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2047,187,'Chaoyang',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2048,185,'Ganzhou',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2049,200,'Huzhou',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2050,186,'Baicheng',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2051,179,'Shangzi',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2052,174,'Yangjiang',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2053,179,'Qitaihe',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2054,199,'Gejiu',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2055,184,'Jiangyin',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2056,180,'Hebi',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2057,200,'Jiaxing',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2058,175,'Wuzhou',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2059,186,'Meihekou',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2060,180,'Xuchang',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2061,192,'Liaocheng',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2062,187,'Haicheng',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2063,181,'Qianjiang',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2064,173,'Baiyin',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2065,179,'BeiÃ‚Â´an',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2066,184,'Yixing',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2067,192,'Laizhou',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2068,198,'Qaramay',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2069,179,'Acheng',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2070,192,'Dezhou',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2071,172,'Nanping',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2072,174,'Zhaoqing',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2073,187,'Beipiao',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2074,185,'Fengcheng',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2075,186,'Fuyu',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2076,180,'Xinyang',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2077,184,'Dongtai',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2078,194,'Yuci',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2079,181,'Honghu',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2080,181,'Ezhou',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2081,192,'Heze',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2082,195,'Daxian',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2083,194,'Linfen',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2084,181,'Tianmen',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2085,182,'Yiyang',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2086,172,'Quanzhou',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2087,192,'Rizhao',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2088,195,'Deyang',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2089,195,'Guangyuan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2090,184,'Changshu',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2091,172,'Zhangzhou',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2092,183,'Hailar',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2093,195,'Nanchong',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2094,186,'Jiutai',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2095,179,'Zhaodong',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2096,200,'Shaoxing',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2097,170,'Fuyang',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2098,174,'Maoming',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2099,199,'Qujing',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2100,198,'Ghulja',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2101,186,'Jiaohe',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2102,180,'Puyang',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2103,186,'Huadian',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2104,195,'Jiangyou',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2105,198,'Qashqar',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2106,176,'Anshun',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2107,195,'Fuling',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2108,185,'Xinyu',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2109,191,'Hanzhong',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2110,184,'Danyang',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2111,182,'Chenzhou',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2112,181,'Xiaogan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2113,180,'Shangqiu',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2114,174,'Zhuhai',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2115,174,'Qingyuan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2116,198,'Aqsu',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2117,183,'Jining',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2118,200,'Xiaoshan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2119,181,'Zaoyang',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2120,184,'Xinghua',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2121,198,'Hami',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2122,174,'Huizhou',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2123,181,'Jinmen',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2124,172,'Sanming',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2125,183,'Ulanhot',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2126,198,'Korla',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2127,195,'Wanxian',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2128,200,'RuiÃ‚Â´an',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2129,200,'Zhoushan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2130,192,'Liangcheng',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2131,192,'Jiaozhou',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2132,184,'Taizhou',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2133,170,'Suzhou',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2134,185,'Yichun',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2135,186,'Taonan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2136,192,'Pingdu',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2137,185,'JiÃ‚Â´an',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2138,192,'Longkou',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2139,178,'Langfang',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2140,180,'Zhoukou',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2141,195,'Suining',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2142,175,'Yulin',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2143,200,'Jinhua',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2144,170,'LiuÃ‚Â´an',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2145,179,'Shuangcheng',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2146,181,'Suizhou',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2147,191,'Ankang',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2148,191,'Weinan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2149,186,'Longjing',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2150,186,'DaÃ‚Â´an',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2151,182,'Lengshuijiang',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2152,192,'Laiyang',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2153,181,'Xianning',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2154,199,'Dali',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2155,179,'Anda',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2156,194,'Jincheng',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2157,172,'Longyan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2158,195,'Xichang',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2159,192,'Wendeng',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2160,179,'Hailun',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2161,192,'Binzhou',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2162,183,'Linhe',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2163,173,'Wuwei',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2164,176,'Duyun',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2165,179,'Mishan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2166,185,'Shangrao',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2167,198,'Changji',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2168,174,'Meixian',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2169,186,'Yushu',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2170,187,'Tiefa',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2171,184,'HuaiÃ‚Â´an',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2172,182,'Leiyang',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2173,183,'Zalantun',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2174,192,'Weihai',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2175,182,'Loudi',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2176,192,'Qingzhou',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2177,184,'Qidong',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2178,182,'Huaihua',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2179,180,'Luohe',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2180,170,'Chuzhou',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2181,187,'Kaiyuan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2182,192,'Linqing',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2183,170,'Chaohu',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2184,181,'Laohekou',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2185,195,'Dujiangyan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2186,180,'Zhumadian',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2187,185,'Linchuan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2188,192,'Jiaonan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2189,180,'Sanmenxia',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2190,174,'Heyuan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2191,183,'Manzhouli',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2192,197,'Lhasa',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2193,182,'Lianyuan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2194,198,'Kuytun',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2195,181,'Puqi',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2196,182,'Hongjiang',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2197,175,'Qinzhou',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2198,178,'Renqiu',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2199,200,'Yuyao',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2200,175,'Guigang',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2201,176,'Kaili',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2202,191,'YanÃ‚Â´an',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2203,175,'Beihai',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2204,170,'Xuangzhou',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2205,200,'Quzhou',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2206,172,'YongÃ‚Â´an',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2207,182,'Zixing',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2208,184,'Liyang',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2209,184,'Yizheng',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2210,173,'Yumen',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2211,182,'Liling',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2212,194,'Yuncheng',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2213,174,'Shanwei',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2214,200,'Cixi',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2215,182,'Yuanjiang',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2216,170,'Bozhou',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2217,173,'Jinchang',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2218,172,'FuÃ‚Â´an',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2219,184,'Suqian',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2220,181,'Shishou',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2221,178,'Hengshui',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2222,181,'Danjiangkou',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2223,179,'Fujin',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2224,177,'Sanya',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2225,181,'Guangshui',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2226,170,'Huangshan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2227,187,'Xingcheng',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2228,192,'Zhucheng',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2229,184,'Kunshan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2230,200,'Haining',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2231,173,'Pingliang',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2232,172,'Fuqing',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2233,194,'Xinzhou',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2234,174,'Jieyang',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2235,184,'Zhangjiagang',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2236,189,'Tong Xian',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2237,195,'YaÃ‚Â´an',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2238,187,'Jinzhou',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2239,195,'Emeishan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2240,181,'Enshi',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2241,175,'Bose',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2242,180,'Yuzhou',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2243,199,'Kaiyuan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2244,186,'Tumen',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2245,172,'Putian',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2246,200,'Linhai',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2247,183,'Xilin Hot',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2248,172,'Shaowu',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2249,192,'Junan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2250,195,'Huaying',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2251,192,'Pingyi',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2252,200,'Huangyan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2253,659,'Bishkek',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2254,660,'Osh',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2255,664,'Bikenibeu',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2256,664,'Bairiki',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2257,243,'SantafÃƒÂ© de BogotÃƒÂ¡',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2258,247,'Cali',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2259,225,'MedellÃƒÂ­n',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2260,226,'Barranquilla',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2261,227,'Cartagena',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2262,240,'CÃƒÂºcuta',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2263,244,'Bucaramanga',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2264,246,'IbaguÃƒÂ©',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2265,242,'Pereira',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2266,237,'Santa Marta',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2267,229,'Manizales',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2268,225,'Bello',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2269,239,'Pasto',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2270,235,'Neiva',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2271,226,'Soledad',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2272,241,'Armenia',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2273,238,'Villavicencio',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2274,234,'Soacha',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2275,232,'Valledupar',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2276,233,'MonterÃƒÂ­a',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2277,225,'ItagÃƒÂ¼ÃƒÂ­',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2278,247,'Palmira',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2279,247,'Buenaventura',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2280,244,'Floridablanca',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2281,245,'Sincelejo',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2282,231,'PopayÃƒÂ¡n',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2283,244,'Barrancabermeja',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2284,242,'Dos Quebradas',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2285,247,'TuluÃƒÂ¡',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2286,225,'Envigado',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2287,247,'Cartago',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2288,234,'Girardot',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2289,247,'Buga',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2290,228,'Tunja',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2291,230,'Florencia',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2292,236,'Maicao',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2293,228,'Sogamoso',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2294,244,'Giron',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2295,248,'Moroni',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2296,222,'Brazzaville',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2297,223,'Pointe-Noire',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2298,217,'Kinshasa',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2299,219,'Lubumbashi',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2300,214,'Mbuji-Mayi',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2301,219,'Kolwezi',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2302,216,'Kisangani',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2303,221,'Kananga',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2304,219,'Likasi',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2305,220,'Bukavu',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2306,212,'Kikwit',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2307,221,'Tshikapa',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2308,213,'Matadi',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2309,215,'Mbandaka',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2310,214,'Mwene-Ditu',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2311,213,'Boma',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2312,220,'Uvira',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2313,218,'Butembo',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2314,218,'Goma',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2315,219,'Kalemie',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2316,151,'Bantam',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2317,152,'West Island',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2318,948,'Pyongyang',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2319,939,'Hamhung',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2320,940,'Chongjin',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2321,945,'Nampo',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2322,947,'Sinuiju',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2323,944,'Wonsan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2324,946,'Phyongsong',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2325,942,'Sariwon',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2326,941,'Haeju',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2327,938,'Kanggye',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2328,940,'Kimchaek',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2329,949,'Hyesan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2330,943,'Kaesong',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2331,678,'Seoul',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2332,677,'Pusan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2333,671,'Inchon',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2334,679,'Taegu',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2335,680,'Taejon',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2336,673,'Kwangju',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2337,676,'Ulsan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2338,674,'Songnam',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2339,674,'Puchon',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2340,674,'Suwon',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2341,674,'Anyang',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2342,667,'Chonju',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2343,669,'Chongju',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2344,674,'Koyang',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2345,674,'Ansan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2346,675,'Pohang',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2347,676,'Chang-won',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2348,676,'Masan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2349,674,'Kwangmyong',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2350,670,'Chonan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2351,676,'Chinju',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2352,667,'Iksan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2353,674,'Pyongtaek',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2354,675,'Kumi',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2355,674,'Uijongbu',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2356,675,'Kyongju',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2357,667,'Kunsan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2358,666,'Cheju',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2359,676,'Kimhae',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2360,668,'Sunchon',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2361,668,'Mokpo',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2362,674,'Yong-in',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2363,672,'Wonju',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2364,674,'Kunpo',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2365,672,'Chunchon',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2366,674,'Namyangju',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2367,672,'Kangnung',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2368,669,'Chungju',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2369,675,'Andong',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2370,668,'Yosu',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2371,675,'Kyongsan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2372,674,'Paju',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2373,676,'Yangsan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2374,674,'Ichon',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2375,670,'Asan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2376,676,'Koje',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2377,675,'Kimchon',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2378,670,'Nonsan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2379,674,'Kuri',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2380,667,'Chong-up',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2381,669,'Chechon',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2382,670,'Sosan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2383,674,'Shihung',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2384,676,'Tong-yong',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2385,670,'Kongju',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2386,675,'Yongju',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2387,676,'Chinhae',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2388,675,'Sangju',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2389,670,'Poryong',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2390,668,'Kwang-yang',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2391,676,'Miryang',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2392,674,'Hanam',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2393,667,'Kimje',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2394,675,'Yongchon',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2395,676,'Sachon',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2396,674,'Uiwang',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2397,668,'Naju',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2398,667,'Namwon',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2399,672,'Tonghae',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2400,675,'Mun-gyong',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2401,435,'Athenai',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2402,436,'Thessaloniki',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2403,435,'Pireus',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2404,439,'Patras',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2405,435,'Peristerion',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2406,437,'Herakleion',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2407,435,'Kallithea',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2408,438,'Larisa',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2409,452,'Zagreb',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2410,455,'Split',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2411,454,'Rijeka',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2412,453,'Osijek',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2413,257,'La Habana',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2414,262,'Santiago de Cuba',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2415,251,'CamagÃƒÂ¼ey',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2416,256,'HolguÃƒÂ­n',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2417,263,'Santa Clara',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2418,255,'GuantÃƒÂ¡namo',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2419,260,'Pinar del RÃƒÂ­o',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2420,254,'Bayamo',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2421,253,'Cienfuegos',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2422,258,'Victoria de las Tunas',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2423,259,'Matanzas',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2424,254,'Manzanillo',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2425,261,'Sancti-SpÃƒÂ­ritus',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2426,252,'Ciego de Ãƒï¿½vila',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2427,682,'al-Salimiya',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2428,682,'Jalib al-Shuyukh',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2429,681,'Kuwait',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2430,267,'Nicosia',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2431,266,'Limassol',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2432,684,'Vientiane',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2433,683,'Savannakhet',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2434,707,'Riga',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2435,705,'Daugavpils',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2436,706,'Liepaja',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2437,698,'Maseru',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2438,686,'Beirut',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2439,685,'Tripoli',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2440,687,'Monrovia',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2441,691,'Tripoli',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2442,689,'Bengasi',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2443,690,'Misrata',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2444,688,'al-Zawiya',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2445,693,'Schaan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2446,694,'Vaduz',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2447,703,'Vilnius',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2448,699,'Kaunas',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2449,700,'Klaipeda',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2450,702,'Ã…Â iauliai',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2451,701,'Panevezys',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2452,704,'Luxembourg [Luxemburg/LÃƒÂ«tze',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2453,356,'El-AaiÃƒÂºn',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2454,708,'Macao',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2455,727,'Antananarivo',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2456,730,'Toamasina',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2457,727,'AntsirabÃƒÂ©',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2458,729,'Mahajanga',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2459,728,'Fianarantsoa',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2460,766,'Skopje',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2461,796,'Blantyre',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2462,797,'Lilongwe',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2463,731,'Male',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2464,809,'Kuala Lumpur',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2465,803,'Ipoh',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2466,798,'Johor Baharu',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2467,807,'Petaling Jaya',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2468,807,'Kelang',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2469,808,'Kuala Terengganu',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2470,804,'Pinang',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2471,800,'Kota Bharu',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2472,802,'Kuantan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2473,803,'Taiping',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2474,801,'Seremban',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2475,806,'Kuching',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2476,806,'Sibu',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2477,805,'Sandakan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2478,799,'Alor Setar',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2479,807,'Selayang Baru',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2480,799,'Sungai Petani',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2481,807,'Shah Alam',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2482,767,'Bamako',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2483,769,'Birkirkara',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2484,768,'Valletta',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2485,709,'Casablanca',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2486,717,'Rabat',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2487,714,'Marrakech',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2488,712,'FÃƒÂ¨s',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2489,720,'Tanger',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2490,717,'SalÃƒÂ©',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2491,715,'MeknÃƒÂ¨s',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2492,716,'Oujda',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2493,713,'KÃƒÂ©nitra',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2494,720,'TÃƒÂ©touan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2495,711,'Safi',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2496,718,'Agadir',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2497,709,'Mohammedia',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2498,710,'Khouribga',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2499,719,'Beni-Mellal',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2500,717,'TÃƒÂ©mara',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2501,711,'El Jadida',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2502,716,'Nador',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2503,720,'Ksar el Kebir',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2504,710,'Settat',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2505,721,'Taza',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2506,720,'El Araich',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2507,765,'Dalap-Uliga-Darrit',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2508,793,'Fort-de-France',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2509,791,'Nouakchott',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2510,790,'NouÃƒÂ¢dhibou',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2511,795,'Port-Louis',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2512,794,'Beau Bassin-Rose Hill',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2513,794,'Vacoas-Phoenix',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2514,810,'Mamoutzou',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2515,740,'Ciudad de MÃƒÂ©xico',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2516,745,'Guadalajara',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2517,746,'Ecatepec de Morelos',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2518,752,'Puebla',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2519,746,'NezahualcÃƒÂ³yotl',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2520,737,'JuÃƒÂ¡rez',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2521,733,'Tijuana',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2522,742,'LeÃƒÂ³n',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2523,750,'Monterrey',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2524,745,'Zapopan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2525,746,'Naucalpan de JuÃƒÂ¡rez',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2526,733,'Mexicali',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2527,757,'CuliacÃƒÂ¡n',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2528,743,'Acapulco de JuÃƒÂ¡rez',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2529,746,'Tlalnepantla de Baz',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2530,763,'MÃƒÂ©rida',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2531,737,'Chihuahua',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2532,756,'San Luis PotosÃƒÂ­',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2533,750,'Guadalupe',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2534,746,'Toluca',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2535,732,'Aguascalientes',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2536,754,'QuerÃƒÂ©taro',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2537,747,'Morelia',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2538,758,'Hermosillo',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2539,738,'Saltillo',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2540,738,'TorreÃƒÂ³n',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2541,759,'Centro (Villahermosa)',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2542,750,'San NicolÃƒÂ¡s de los Garza',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2543,741,'Durango',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2544,746,'ChimalhuacÃƒÂ¡n',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2545,745,'Tlaquepaque',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2546,746,'AtizapÃƒÂ¡n de Zaragoza',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2547,761,'Veracruz',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2548,746,'CuautitlÃƒÂ¡n Izcalli',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2549,742,'Irapuato',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2550,736,'Tuxtla GutiÃƒÂ©rrez',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2551,746,'TultitlÃƒÂ¡n',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2552,760,'Reynosa',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2553,755,'Benito JuÃƒÂ¡rez',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2554,760,'Matamoros',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2555,761,'Xalapa',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2556,742,'Celaya',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2557,757,'MazatlÃƒÂ¡n',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2558,733,'Ensenada',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2559,757,'Ahome',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2560,758,'Cajeme',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2561,748,'Cuernavaca',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2562,745,'TonalÃƒÂ¡',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2563,746,'Valle de Chalco Solidaridad',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2564,760,'Nuevo Laredo',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2565,749,'Tepic',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2566,760,'Tampico',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2567,746,'Ixtapaluca',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2568,750,'Apodaca',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2569,757,'Guasave',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2570,741,'GÃƒÂ³mez Palacio',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2571,736,'Tapachula',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2572,746,'NicolÃƒÂ¡s Romero',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2573,761,'Coatzacoalcos',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2574,747,'Uruapan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2575,760,'Victoria',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2576,751,'Oaxaca de JuÃƒÂ¡rez',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2577,746,'Coacalco de BerriozÃƒÂ¡bal',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2578,744,'Pachuca de Soto',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2579,750,'General Escobedo',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2580,742,'Salamanca',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2581,750,'Santa Catarina',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2582,752,'TehuacÃƒÂ¡n',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2583,746,'Chalco',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2584,759,'CÃƒÂ¡rdenas',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2585,735,'Campeche',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2586,746,'La Paz',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2587,755,'OthÃƒÂ³n P. Blanco (Chetumal)',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2588,746,'Texcoco',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2589,734,'La Paz',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2590,746,'Metepec',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2591,738,'Monclova',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2592,746,'Huixquilucan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2593,743,'Chilpancingo de los Bravo',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2594,745,'Puerto Vallarta',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2595,764,'Fresnillo',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2596,760,'Ciudad Madero',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2597,756,'Soledad de Graciano SÃƒÂ¡nchez',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2598,753,'San Juan del RÃƒÂ­o',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2599,746,'San Felipe del Progreso',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2600,761,'CÃƒÂ³rdoba',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2601,746,'TecÃƒÂ¡mac',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2602,736,'Ocosingo',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2603,735,'Carmen',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2604,747,'LÃƒÂ¡zaro CÃƒÂ¡rdenas',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2605,748,'Jiutepec',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2606,761,'Papantla',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2607,759,'Comalcalco',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2608,747,'Zamora',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2609,758,'Nogales',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2610,759,'Huimanguillo',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2611,748,'Cuautla',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2612,761,'MinatitlÃƒÂ¡n',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2613,761,'Poza Rica de Hidalgo',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2614,756,'Ciudad Valles',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2615,757,'Navolato',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2616,758,'San Luis RÃƒÂ­o Colorado',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2617,742,'PÃƒÂ©njamo',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2618,761,'San AndrÃƒÂ©s Tuxtla',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2619,742,'Guanajuato',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2620,758,'Navojoa',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2621,747,'ZitÃƒÂ¡cuaro',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2622,762,'Boca del RÃƒÂ­o',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2623,742,'Allende',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2624,742,'Silao',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2625,759,'Macuspana',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2626,751,'San Juan Bautista Tuxtepec',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2627,736,'San CristÃƒÂ³bal de las Casas',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2628,742,'Valle de Santiago',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2629,758,'Guaymas',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2630,739,'Colima',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2631,742,'Dolores Hidalgo',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2632,745,'Lagos de Moreno',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2633,738,'Piedras Negras',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2634,760,'Altamira',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2635,761,'TÃƒÂºxpam',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2636,750,'San Pedro Garza GarcÃƒÂ­a',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2637,737,'CuauhtÃƒÂ©moc',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2638,739,'Manzanillo',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2639,743,'Iguala de la Independencia',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2640,764,'Zacatecas',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2641,745,'Tlajomulco de ZÃƒÂºÃƒÂ±iga',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2642,744,'Tulancingo de Bravo',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2643,746,'Zinacantepec',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2644,752,'San MartÃƒÂ­n Texmelucan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2645,745,'TepatitlÃƒÂ¡n de Morelos',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2646,761,'MartÃƒÂ­nez de la Torre',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2647,761,'Orizaba',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2648,747,'ApatzingÃƒÂ¡n',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2649,752,'Atlixco',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2650,737,'Delicias',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2651,746,'Ixtlahuaca',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2652,760,'El Mante',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2653,741,'Lerdo',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2654,746,'Almoloya de JuÃƒÂ¡rez',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2655,742,'AcÃƒÂ¡mbaro',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2656,738,'AcuÃƒÂ±a',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2657,764,'Guadalupe',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2658,744,'Huejutla de Reyes',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2659,747,'Hidalgo',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2660,734,'Los Cabos',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2661,736,'ComitÃƒÂ¡n de DomÃƒÂ­nguez',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2662,759,'CunduacÃƒÂ¡n',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2663,760,'RÃƒÂ­o Bravo',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2664,761,'Temapache',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2665,743,'Chilapa de Alvarez',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2666,737,'Hidalgo del Parral',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2667,742,'San Francisco del RincÃƒÂ³n',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2668,743,'Taxco de AlarcÃƒÂ³n',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2669,746,'Zumpango',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2670,752,'San Pedro Cholula',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2671,746,'Lerma',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2672,739,'TecomÃƒÂ¡n',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2673,736,'Las Margaritas',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2674,761,'Cosoleacaque',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2675,742,'San Luis de la Paz',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2676,743,'JosÃƒÂ© Azueta',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2677,749,'Santiago Ixcuintla',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2678,742,'San Felipe',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2679,746,'Tejupilco',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2680,761,'Tantoyuca',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2681,742,'Salvatierra',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2682,746,'Tultepec',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2683,748,'Temixco',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2684,738,'Matamoros',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2685,761,'PÃƒÂ¡nuco',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2686,757,'El Fuerte',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2687,761,'Tierra Blanca',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2688,409,'Weno',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2689,410,'Palikir',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2690,725,'Chisinau',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2691,726,'Tiraspol',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2692,723,'Balti',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2693,724,'Bender (TÃƒÂ®ghina)',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2694,722,'Monte-Carlo',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2695,722,'Monaco-Ville',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2696,780,'Ulan Bator',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2697,792,'Plymouth',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2698,785,'Maputo',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2699,785,'Matola',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2700,787,'Beira',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2701,786,'Nampula',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2702,784,'Chimoio',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2703,786,'NaÃƒÂ§ala-Porto',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2704,789,'Quelimane',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2705,789,'Mocuba',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2706,788,'Tete',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2707,782,'Xai-Xai',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2708,789,'Gurue',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2709,783,'Maxixe',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2710,776,'Rangoon (Yangon)',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2711,772,'Mandalay',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2712,773,'Moulmein (Mawlamyine)',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2713,774,'Pegu (Bago)',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2714,770,'Bassein (Pathein)',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2715,777,'Monywa',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2716,775,'Sittwe (Akyab)',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2717,778,'Taunggyi (Taunggye)',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2718,772,'Meikhtila',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2719,779,'Mergui (Myeik)',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2720,778,'Lashio (Lasho)',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2721,774,'Prome (Pyay)',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2722,770,'Henzada (Hinthada)',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2723,772,'Myingyan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2724,779,'Tavoy (Dawei)',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2725,771,'Pagakku (Pakokku)',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2726,811,'Windhoek',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2727,860,'Yangor',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2728,860,'Yaren',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2729,857,'Kathmandu',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2730,858,'Biratnagar',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2731,859,'Pokhara',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2732,857,'Lalitapur',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2733,857,'Birgunj',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2734,839,'Managua',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2735,838,'LeÃƒÂ³n',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2736,837,'Chinandega',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2737,840,'Masaya',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2738,814,'Niamey',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2739,815,'Zinder',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2740,813,'Maradi',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2741,829,'Lagos',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2742,833,'Ibadan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2743,833,'Ogbomosho',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2744,826,'Kano',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2745,833,'Oshogbo',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2746,828,'Ilorin',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2747,831,'Abeokuta',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2748,835,'Port Harcourt',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2749,825,'Zaria',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2750,833,'Ilesha',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2751,817,'Onitsha',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2752,833,'Iwo',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2753,832,'Ado-Ekiti',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2754,823,'Abuja',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2755,825,'Kaduna',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2756,829,'Mushin',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2757,820,'Maiduguri',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2758,817,'Enugu',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2759,833,'Ede',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2760,824,'Aba',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2761,833,'Ife',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2762,833,'Ila',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2763,833,'Oyo',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2764,832,'Ikerre',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2765,822,'Benin City',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2766,833,'Iseyin',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2767,827,'Katsina',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2768,834,'Jos',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2769,836,'Sokoto',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2770,833,'Ilobu',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2771,828,'Offa',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2772,829,'Ikorodu',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2773,832,'Ilawe-Ekiti',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2774,832,'Owo',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2775,833,'Ikirun',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2776,833,'Shaki',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2777,821,'Calabar',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2778,832,'Ondo',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2779,832,'Akure',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2780,836,'Gusau',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2781,831,'Ijebu-Ode',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2782,833,'Effon-Alaiye',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2783,818,'Kumo',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2784,829,'Shomolu',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2785,832,'Oka-Akoko',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2786,832,'Ikare',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2787,822,'Sapele',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2788,818,'Deba Habe',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2789,830,'Minna',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2790,822,'Warri',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2791,830,'Bida',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2792,833,'Ikire',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2793,819,'Makurdi',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2794,834,'Lafia',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2795,833,'Inisa',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2796,831,'Shagamu',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2797,817,'Awka',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2798,818,'Gombe',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2799,833,'Igboho',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2800,833,'Ejigbo',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2801,829,'Agege',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2802,832,'Ise-Ekiti',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2803,821,'Ugep',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2804,829,'Epe',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2805,841,'Alofi',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2806,816,'Kingston',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2807,854,'Oslo',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2808,853,'Bergen',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2809,856,'Trondheim',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2810,855,'Stavanger',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2811,852,'BÃƒÂ¦rum',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2812,201,'Abidjan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2813,202,'BouakÃƒÂ©',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2814,205,'Yamoussoukro',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2815,203,'Daloa',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2816,204,'Korhogo',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2817,867,'al-Sib',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2818,868,'Salala',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2819,867,'Bawshar',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2820,866,'Suhar',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2821,867,'Masqat',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2822,874,'Karachi',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2823,872,'Lahore',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2824,872,'Faisalabad',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2825,872,'Rawalpindi',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2826,872,'Multan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2827,874,'Hyderabad',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2828,872,'Gujranwala',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2829,871,'Peshawar',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2830,869,'Quetta',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2831,870,'Islamabad',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2832,872,'Sargodha',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2833,872,'Sialkot',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2834,872,'Bahawalpur',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2835,874,'Sukkur',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2836,872,'Jhang',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2837,872,'Sheikhupura',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2838,874,'Larkana',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2839,872,'Gujrat',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2840,871,'Mardan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2841,872,'Kasur',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2842,872,'Rahim Yar Khan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2843,872,'Sahiwal',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2844,872,'Okara',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2845,872,'Wah',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2846,872,'Dera Ghazi Khan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2847,873,'Mirpur Khas',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2848,873,'Nawabshah',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2849,871,'Mingora',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2850,872,'Chiniot',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2851,872,'Kamoke',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2852,872,'Mandi Burewala',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2853,872,'Jhelum',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2854,872,'Sadiqabad',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2855,873,'Jacobabad',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2856,873,'Shikarpur',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2857,872,'Khanewal',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2858,872,'Hafizabad',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2859,871,'Kohat',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2860,872,'Muzaffargarh',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2861,872,'Khanpur',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2862,872,'Gojra',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2863,872,'Bahawalnagar',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2864,872,'Muridke',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2865,872,'Pak Pattan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2866,871,'Abottabad',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2867,873,'Tando Adam',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2868,872,'Jaranwala',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2869,873,'Khairpur',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2870,872,'Chishtian Mandi',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2871,872,'Daska',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2872,873,'Dadu',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2873,872,'Mandi Bahauddin',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2874,872,'Ahmadpur East',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2875,872,'Kamalia',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2876,869,'Khuzdar',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2877,872,'Vihari',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2878,871,'Dera Ismail Khan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2879,872,'Wazirabad',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2880,871,'Nowshera',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2881,911,'Koror',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2882,875,'Ciudad de PanamÃƒÂ¡',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2883,876,'San Miguelito',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2884,912,'Port Moresby',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2885,955,'AsunciÃƒÂ³n',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2886,954,'Ciudad del Este',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2887,956,'San Lorenzo',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2888,956,'LambarÃƒÂ©',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2889,956,'Fernando de la Mora',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2890,889,'Lima',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2891,879,'Arequipa',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2892,887,'Trujillo',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2893,888,'Chiclayo',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2894,882,'Callao',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2895,890,'Iquitos',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2896,878,'Chimbote',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2897,886,'Huancayo',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2898,891,'Piura',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2899,883,'Cusco',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2900,894,'Pucallpa',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2901,893,'Tacna',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2902,885,'Ica',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2903,891,'Sullana',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2904,892,'Juliaca',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2905,884,'HuÃƒÂ¡nuco',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2906,880,'Ayacucho',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2907,885,'Chincha Alta',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2908,881,'Cajamarca',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2909,892,'Puno',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2910,882,'Ventanilla',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2911,891,'Castilla',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2912,877,'Adamstown',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2913,781,'Garapan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2914,952,'Lisboa',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2915,953,'Porto',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2916,952,'Amadora',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2917,951,'CoÃƒÂ­mbra',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2918,950,'Braga',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2919,936,'San Juan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2920,930,'BayamÃƒÂ³n',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2921,935,'Ponce',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2922,932,'Carolina',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2923,931,'Caguas',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2924,929,'Arecibo',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2925,933,'Guaynabo',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2926,934,'MayagÃƒÂ¼ez',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2927,937,'Toa Baja',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2928,919,'Warszawa',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2929,915,'LÃƒÂ³dz',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2930,918,'KrakÃƒÂ³w',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2931,913,'Wroclaw',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2932,927,'Poznan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2933,923,'Gdansk',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2934,928,'Szczecin',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2935,914,'Bydgoszcz',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2936,916,'Lublin',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2937,924,'Katowice',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2938,922,'Bialystok',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2939,924,'Czestochowa',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2940,923,'Gdynia',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2941,924,'Sosnowiec',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2942,919,'Radom',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2943,925,'Kielce',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2944,924,'Gliwice',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2945,914,'Torun',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2946,924,'Bytom',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2947,924,'Zabrze',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2948,924,'Bielsko-Biala',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2949,926,'Olsztyn',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2950,921,'RzeszÃƒÂ³w',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2951,924,'Ruda Slaska',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2952,924,'Rybnik',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2953,913,'Walbrzych',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2954,924,'Tychy',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2955,924,'Dabrowa GÃƒÂ³rnicza',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2956,919,'Plock',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2957,926,'Elblag',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2958,920,'Opole',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2959,917,'GorzÃƒÂ³w Wielkopolski',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2960,914,'Wloclawek',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2961,924,'ChorzÃƒÂ³w',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2962,918,'TarnÃƒÂ³w',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2963,917,'Zielona GÃƒÂ³ra',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2964,928,'Koszalin',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2965,913,'Legnica',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2966,927,'Kalisz',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2967,914,'Grudziadz',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2968,923,'Slupsk',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2969,924,'Jastrzebie-ZdrÃƒÂ³j',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2970,924,'Jaworzno',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2971,913,'Jelenia GÃƒÂ³ra',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2972,434,'Malabo',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2973,964,'Doha',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2974,398,'Paris',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2975,406,'Marseille',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2976,407,'Lyon',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2977,402,'Toulouse',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2978,406,'Nice',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2979,404,'Nantes',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2980,388,'Strasbourg',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2981,399,'Montpellier',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2982,389,'Bordeaux',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2983,397,'Rennes',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2984,395,'Le Havre',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2985,403,'Reims',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2986,407,'Lille',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2987,393,'St-Ãƒâ€°tienne',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2988,406,'Toulon',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2989,407,'Grenoble',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2990,404,'Angers',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2991,392,'Dijon',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2992,393,'Brest',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2993,404,'Le Mans',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2994,390,'Clermont-Ferrand',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2995,405,'Amiens',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2996,406,'Aix-en-Provence',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2997,400,'Limoges',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2998,399,'NÃƒÂ®mes',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(2999,394,'Tours',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3000,407,'Villeurbanne',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3001,401,'Metz',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3002,396,'BesanÃƒÂ§on',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3003,391,'Caen',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3004,394,'OrlÃƒÂ©ans',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3005,388,'Mulhouse',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3006,397,'Rouen',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3007,398,'Boulogne-Billancourt',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3008,399,'Perpignan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3009,401,'Nancy',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3010,403,'Roubaix',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3011,398,'Argenteuil',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3012,403,'Tourcoing',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3013,398,'Montreuil',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3014,444,'Cayenne',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3015,963,'Faaa',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3016,963,'Papeete',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3017,965,'Saint-Denis',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3018,973,'Bucuresti',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3019,982,'Iasi',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3020,977,'Constanta',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3021,976,'Cluj-Napoca',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3022,980,'Galati',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3023,991,'Timisoara',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3024,972,'Brasov',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3025,979,'Craiova',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3026,987,'Ploiesti',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3027,971,'Braila',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3028,969,'Oradea',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3029,968,'Bacau',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3030,967,'Pitesti',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3031,966,'Arad',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3032,989,'Sibiu',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3033,985,'TÃƒÂ¢rgu Mures',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3034,983,'Baia Mare',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3035,974,'Buzau',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3036,988,'Satu Mare',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3037,970,'Botosani',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3038,986,'Piatra Neamt',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3039,993,'RÃƒÂ¢mnicu VÃƒÂ¢lcea',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3040,990,'Suceava',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3041,984,'Drobeta-Turnu Severin',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3042,978,'TÃƒÂ¢rgoviste',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3043,994,'Focsani',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3044,981,'TÃƒÂ¢rgu Jiu',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3045,992,'Tulcea',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3046,975,'Resita',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3047,1072,'Kigali',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3048,1125,'Stockholm',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3049,1132,'Gothenburg [GÃƒÂ¶teborg]',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3050,1127,'MalmÃƒÂ¶',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3051,1128,'Uppsala',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3052,1122,'LinkÃƒÂ¶ping',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3053,1131,'VÃƒÂ¤sterÃƒÂ¥s',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3054,1126,'Ãƒâ€“rebro',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3055,1122,'NorrkÃƒÂ¶ping',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3056,1127,'Helsingborg',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3057,1124,'JÃƒÂ¶nkÃƒÂ¶ping',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3058,1129,'UmeÃƒÂ¥',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3059,1127,'Lund',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3060,1132,'BorÃƒÂ¥s',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3061,1130,'Sundsvall',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3062,1123,'GÃƒÂ¤vle',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3063,1102,'Jamestown',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3064,665,'Basseterre',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3065,692,'Castries',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3066,1341,'Kingstown',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3067,1115,'Saint-Pierre',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3068,278,'Berlin',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3069,281,'Hamburg',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3070,277,'Munich [MÃƒÂ¼nchen]',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3071,285,'KÃƒÂ¶ln',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3072,282,'Frankfurt am Main',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3073,285,'Essen',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3074,285,'Dortmund',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3075,276,'Stuttgart',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3076,285,'DÃƒÂ¼sseldorf',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3077,280,'Bremen',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3078,285,'Duisburg',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3079,284,'Hannover',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3080,288,'Leipzig',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3081,277,'NÃƒÂ¼rnberg',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3082,288,'Dresden',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3083,285,'Bochum',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3084,285,'Wuppertal',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3085,285,'Bielefeld',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3086,276,'Mannheim',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3087,285,'Bonn',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3088,285,'Gelsenkirchen',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3089,276,'Karlsruhe',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3090,282,'Wiesbaden',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3091,285,'MÃƒÂ¼nster',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3092,285,'MÃƒÂ¶nchengladbach',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3093,288,'Chemnitz',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3094,277,'Augsburg',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3095,275,'Halle/Saale',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3096,284,'Braunschweig',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3097,285,'Aachen',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3098,285,'Krefeld',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3099,275,'Magdeburg',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3100,289,'Kiel',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3101,285,'Oberhausen',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3102,289,'LÃƒÂ¼beck',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3103,285,'Hagen',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3104,283,'Rostock',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3105,276,'Freiburg im Breisgau',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3106,290,'Erfurt',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3107,282,'Kassel',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3108,287,'SaarbrÃƒÂ¼cken',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3109,286,'Mainz',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3110,285,'Hamm',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3111,285,'Herne',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3112,285,'MÃƒÂ¼lheim an der Ruhr',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3113,285,'Solingen',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3114,284,'OsnabrÃƒÂ¼ck',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3115,286,'Ludwigshafen am Rhein',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3116,285,'Leverkusen',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3117,284,'Oldenburg',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3118,285,'Neuss',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3119,276,'Heidelberg',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3120,282,'Darmstadt',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3121,285,'Paderborn',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3122,279,'Potsdam',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3123,277,'WÃƒÂ¼rzburg',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3124,277,'Regensburg',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3125,285,'Recklinghausen',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3126,284,'GÃƒÂ¶ttingen',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3127,280,'Bremerhaven',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3128,284,'Wolfsburg',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3129,285,'Bottrop',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3130,285,'Remscheid',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3131,276,'Heilbronn',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3132,276,'Pforzheim',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3133,282,'Offenbach am Main',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3134,276,'Ulm',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3135,277,'Ingolstadt',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3136,290,'Gera',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3137,284,'Salzgitter',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3138,279,'Cottbus',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3139,276,'Reutlingen',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3140,277,'FÃƒÂ¼rth',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3141,285,'Siegen',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3142,286,'Koblenz',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3143,285,'Moers',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3144,285,'Bergisch Gladbach',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3145,288,'Zwickau',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3146,284,'Hildesheim',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3147,285,'Witten',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3148,283,'Schwerin',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3149,277,'Erlangen',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3150,286,'Kaiserslautern',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3151,286,'Trier',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3152,290,'Jena',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3153,285,'Iserlohn',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3154,285,'GÃƒÂ¼tersloh',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3155,285,'Marl',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3156,285,'LÃƒÂ¼nen',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3157,285,'DÃƒÂ¼ren',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3158,285,'Ratingen',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3159,285,'Velbert',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3160,276,'Esslingen am Neckar',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3161,1104,'Honiara',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3162,1408,'Lusaka',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3163,1407,'Ndola',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3164,1407,'Kitwe',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3165,1406,'Kabwe',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3166,1407,'Chingola',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3167,1407,'Mufulira',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3168,1407,'Luanshya',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3169,1387,'Apia',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3170,1111,'Serravalle',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3171,1110,'San Marino',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3172,1116,'SÃƒÂ£o TomÃƒÂ©',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3173,1083,'Riyadh',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3174,1079,'Jedda',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3175,1079,'Mekka',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3176,1078,'Medina',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3177,1075,'al-Dammam',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3178,1079,'al-Taif',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3179,1084,'Tabuk',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3180,1074,'Burayda',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3181,1075,'al-Hufuf',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3182,1075,'al-Mubarraz',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3183,1076,'Khamis Mushayt',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3184,1077,'Hail',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3185,1082,'al-Kharj',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3186,1075,'al-Khubar',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3187,1075,'Jubayl',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3188,1075,'Hafar al-Batin',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3189,1075,'al-Tuqba',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3190,1078,'Yanbu',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3191,1076,'Abha',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3192,1073,'AraÃ‚Â´ar',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3193,1075,'al-Qatif',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3194,1079,'al-Hawiya',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3195,1081,'Unayza',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3196,1080,'Najran',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3197,1095,'Pikine',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3198,1095,'Dakar',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3199,1099,'ThiÃƒÂ¨s',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3200,1097,'Kaolack',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3201,1100,'Ziguinchor',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3202,1095,'Rufisque',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3203,1098,'Saint-Louis',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3204,1099,'Mbour',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3205,1096,'Diourbel',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3206,1134,'Victoria',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3207,1105,'Freetown',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3208,1101,'Singapore',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3209,1118,'Bratislava',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3210,1119,'KoÃ…Â¡ice',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3211,1119,'PreÃ…Â¡ov',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3212,1120,'Ljubljana',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3213,1121,'Maribor',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3214,1112,'Mogadishu',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3215,1114,'Hargeysa',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3216,1113,'Kismaayo',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3217,697,'Colombo',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3218,697,'Dehiwala',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3219,697,'Moratuwa',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3220,696,'Jaffna',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3221,695,'Kandy',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3222,697,'Sri Jayawardenepura Kotte',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3223,697,'Negombo',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3224,1093,'Omdurman',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3225,1093,'Khartum',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3226,1093,'Sharq al-Nil',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3227,1086,'Port Sudan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3228,1092,'Kassala',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3229,1094,'Obeid',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3230,1090,'Nyala',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3231,1087,'Wad Madani',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3232,1088,'al-Qadarif',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3233,1085,'Kusti',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3234,1091,'al-Fashir',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3235,1089,'Juba',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3236,381,'Helsinki [Helsingfors]',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3237,381,'Espoo',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3238,383,'Tampere',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3239,381,'Vantaa',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3240,385,'Turku [Ãƒâ€¦bo]',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3241,384,'Oulu',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3242,382,'Lahti',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3243,1117,'Paramaribo',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3244,1133,'Mbabane',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3245,157,'ZÃƒÂ¼rich',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3246,155,'Geneve',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3247,153,'Basel',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3248,154,'Bern',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3249,156,'Lausanne',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3250,1138,'Damascus',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3251,1137,'Aleppo',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3252,1142,'Hims',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3253,1141,'Hama',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3254,1144,'Latakia',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3255,1135,'al-Qamishliya',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3256,1140,'Dayr al-Zawr',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3257,1139,'Jaramana',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3258,1139,'Duma',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3259,1136,'al-Raqqa',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3260,1143,'Idlib',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3261,1159,'Dushanbe',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3262,1160,'Khujand',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3263,1240,'Taipei',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3264,1233,'Kaohsiung',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3265,1238,'Taichung',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3266,1239,'Tainan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3267,1240,'Panchiao',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3268,1240,'Chungho',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3269,1234,'Keelung (Chilung)',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3270,1240,'Sanchung',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3271,1240,'Hsinchuang',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3272,1230,'Hsinchu',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3273,1243,'Chungli',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3274,1233,'Fengshan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3275,1243,'Taoyuan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3276,1229,'Chiayi',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3277,1240,'Hsintien',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3278,1228,'Changhwa',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3279,1240,'Yungho',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3280,1240,'Tucheng',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3281,1237,'Pingtung',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3282,1239,'Yungkang',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3283,1243,'Pingchen',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3284,1238,'Tali',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3285,1242,'Taiping',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3286,1243,'Pate',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3287,1238,'Fengyuan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3288,1240,'Luchou',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3289,1240,'Hsichuh',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3290,1240,'Shulin',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3291,1228,'Yuanlin',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3292,1243,'Yangmei',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3293,1242,'Taliao',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3294,1242,'Kueishan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3295,1240,'Tanshui',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3296,1241,'Taitung',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3297,1231,'Hualien',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3298,1236,'Nantou',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3299,1240,'Lungtan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3300,1244,'Touliu',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3301,1236,'Tsaotun',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3302,1233,'Kangshan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3303,1232,'Ilan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3304,1235,'Miaoli',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3305,1246,'Dar es Salaam',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3306,1247,'Dodoma',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3307,1251,'Mwanza',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3308,1254,'Zanzibar',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3309,1253,'Tanga',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3310,1249,'Mbeya',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3311,1250,'Morogoro',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3312,1245,'Arusha',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3313,1248,'Moshi',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3314,1252,'Tabora',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3315,296,'KÃƒÂ¸benhavn',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3316,293,'Ãƒâ€¦rhus',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3317,295,'Odense',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3318,297,'Aalborg',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3319,294,'Frederiksberg',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3320,1149,'Bangkok',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3321,1155,'Nonthaburi',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3322,1153,'Nakhon Ratchasima',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3323,1150,'Chiang Mai',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3324,1158,'Udon Thani',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3325,1156,'Hat Yai',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3326,1151,'Khon Kaen',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3327,1155,'Pak Kret',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3328,1154,'Nakhon Sawan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3329,1157,'Ubon Ratchathani',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3330,1156,'Songkhla',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3331,1152,'Nakhon Pathom',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3332,1148,'LomÃƒÂ©',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3333,1161,'Fakaofo',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3334,1167,'NukuÃ‚Â´alofa',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3335,1168,'Chaguanas',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3336,1169,'Port-of-Spain',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3337,1146,'NÃ‚Â´DjamÃƒÂ©na',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3338,1147,'Moundou',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3339,268,'Praha',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3340,270,'Brno',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3341,272,'Ostrava',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3342,274,'Plzen',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3343,272,'Olomouc',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3344,271,'Liberec',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3345,269,'CeskÃƒÂ© Budejovice',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3346,273,'Hradec KrÃƒÂ¡lovÃƒÂ©',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3347,271,'ÃƒÅ¡stÃƒÂ­ nad Labem',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3348,273,'Pardubice',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3349,1176,'Tunis',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3350,1174,'Sfax',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3351,1170,'Ariana',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3352,1170,'Ettadhamen',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3353,1175,'Sousse',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3354,1173,'Kairouan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3355,1171,'Biserta',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3356,1172,'GabÃƒÂ¨s',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3357,1199,'Istanbul',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3358,1181,'Ankara',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3359,1200,'Izmir',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3360,1177,'Adana',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3361,1186,'Bursa',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3362,1195,'Gaziantep',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3363,1209,'Konya',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3364,1197,'Mersin (IÃƒÂ§el)',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3365,1182,'Antalya',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3366,1189,'Diyarbakir',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3367,1205,'Kayseri',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3368,1194,'Eskisehir',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3369,1218,'Sanliurfa',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3370,1217,'Samsun',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3371,1211,'Malatya',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3372,1208,'Gebze',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3373,1188,'Denizli',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3374,1220,'Sivas',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3375,1193,'Erzurum',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3376,1177,'Tarsus',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3377,1201,'Kahramanmaras',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3378,1191,'ElÃƒÂ¢zig',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3379,1225,'Van',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3380,1199,'Sultanbeyli',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3381,1208,'Izmit (Kocaeli)',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3382,1212,'Manisa',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3383,1185,'Batman',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3384,1184,'Balikesir',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3385,1216,'Sakarya (Adapazari)',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3386,1196,'Iskenderun',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3387,1215,'Osmaniye',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3388,1187,'Ãƒâ€¡orum',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3389,1210,'KÃƒÂ¼tahya',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3390,1196,'Hatay (Antakya)',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3391,1207,'Kirikkale',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3392,1178,'Adiyaman',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3393,1223,'Trabzon',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3394,1214,'Ordu',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3395,1183,'Aydin',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3396,1224,'Usak',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3397,1190,'Edirne',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3398,1221,'Ãƒâ€¡orlu',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3399,1198,'Isparta',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3400,1202,'KarabÃƒÂ¼k',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3401,1206,'Kilis',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3402,1182,'Alanya',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3403,1213,'Kiziltepe',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3404,1226,'Zonguldak',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3405,1219,'Siirt',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3406,1218,'Viransehir',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3407,1221,'Tekirdag',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3408,1203,'Karaman',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3409,1179,'Afyon',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3410,1180,'Aksaray',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3411,1177,'Ceyhan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3412,1192,'Erzincan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3413,1189,'Bismil',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3414,1183,'Nazilli',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3415,1222,'Tokat',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3416,1204,'Kars',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3417,1186,'InegÃƒÂ¶l',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3418,1184,'Bandirma',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3419,1162,'Ashgabat',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3420,1164,'ChÃƒÂ¤rjew',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3421,1163,'Dashhowuz',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3422,1165,'Mary',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3423,1145,'Cockburn Town',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3424,1227,'Funafuti',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3425,1255,'Kampala',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3426,1262,'Kyiv',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3427,1258,'Harkova [Harkiv]',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3428,1256,'Dnipropetrovsk',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3429,1257,'Donetsk',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3430,1268,'Odesa',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3431,1279,'Zaporizzja',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3432,1266,'Lviv',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3433,1256,'Kryvyi Rig',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3434,1267,'Mykolajiv',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3435,1257,'Mariupol',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3436,1265,'Lugansk',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3437,1277,'Vinnytsja',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3438,1257,'Makijivka',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3439,1259,'Herson',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3440,1264,'Sevastopol',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3441,1264,'Simferopol',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3442,1269,'Pultava [Poltava]',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3443,1275,'TÃ…Â¡ernigiv',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3444,1274,'TÃ…Â¡erkasy',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3445,1257,'Gorlivka',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3446,1280,'Zytomyr',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3447,1271,'Sumy',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3448,1256,'Dniprodzerzynsk',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3449,1263,'Kirovograd',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3450,1260,'Hmelnytskyi',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3451,1276,'TÃ…Â¡ernivtsi',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3452,1270,'Rivne',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3453,1269,'KrementÃ…Â¡uk',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3454,1261,'Ivano-Frankivsk',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3455,1273,'Ternopil',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3456,1278,'Lutsk',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3457,1262,'Bila Tserkva',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3458,1257,'Kramatorsk',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3459,1279,'Melitopol',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3460,1264,'KertÃ…Â¡',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3461,1256,'Nikopol',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3462,1279,'Berdjansk',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3463,1256,'Pavlograd',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3464,1265,'Sjeverodonetsk',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3465,1257,'Slovjansk',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3466,1272,'Uzgorod',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3467,1265,'AltÃ…Â¡evsk',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3468,1265,'LysytÃ…Â¡ansk',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3469,1264,'Jevpatorija',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3470,1260,'Kamjanets-Podilskyi',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3471,1257,'Jenakijeve',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3472,1265,'Krasnyi LutÃ…Â¡',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3473,1265,'Stahanov',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3474,1263,'Oleksandrija',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3475,1271,'Konotop',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3476,1257,'Kostjantynivka',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3477,1280,'BerdytÃ…Â¡iv',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3478,1268,'Izmajil',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3479,1271,'Ã…Â ostka',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3480,1274,'Uman',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3481,1262,'Brovary',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3482,1272,'MukatÃ…Â¡eve',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3483,461,'Budapest',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3484,465,'Debrecen',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3485,460,'Miskolc',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3486,462,'Szeged',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3487,459,'PÃƒÂ©cs',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3488,464,'GyÃƒÂ¶r',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3489,466,'NyiregyhÃƒÂ¡za',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3490,458,'KecskemÃƒÂ©t',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3491,463,'SzÃƒÂ©kesfehÃƒÂ©rvÃƒÂ¡r',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3492,1281,'Montevideo',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3493,812,'NoumÃƒÂ©a',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3494,861,'Auckland',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3495,862,'Christchurch',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3496,861,'Manukau',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3497,861,'North Shore',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3498,861,'Waitakere',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3499,865,'Wellington',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3500,863,'Dunedin',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3501,864,'Hamilton',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3502,865,'Lower Hutt',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3503,1339,'Toskent',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3504,1333,'Namangan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3505,1336,'Samarkand',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3506,1327,'Andijon',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3507,1328,'Buhoro',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3508,1335,'Karsi',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3509,1331,'Nukus',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3510,1330,'KÃƒÂ¼kon',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3511,1330,'Fargona',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3512,1338,'Circik',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3513,1330,'Margilon',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3514,1332,'ÃƒÅ“rgenc',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3515,1338,'Angren',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3516,1329,'Cizah',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3517,1334,'Navoi',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3518,1338,'Olmalik',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3519,1337,'Termiz',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3520,95,'Minsk',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3521,93,'Gomel',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3522,97,'Mogiljov',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3523,98,'Vitebsk',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3524,94,'Grodno',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3525,92,'Brest',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3526,97,'Bobruisk',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3527,92,'BaranovitÃ…Â¡i',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3528,96,'Borisov',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3529,92,'Pinsk',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3530,98,'OrÃ…Â¡a',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3531,93,'Mozyr',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3532,98,'Novopolotsk',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3533,94,'Lida',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3534,96,'Soligorsk',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3535,96,'MolodetÃ…Â¡no',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3536,1386,'Mata-Utu',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3537,1385,'Port-Vila',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3538,1340,'CittÃƒÂ  del Vaticano',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3539,1348,'Caracas',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3540,1361,'MaracaÃƒÂ­bo',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3541,1351,'Barquisimeto',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3542,1347,'Valencia',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3543,1346,'Ciudad Guayana',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3544,1353,'Petare',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3545,1344,'Maracay',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3546,1342,'Barcelona',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3547,1354,'MaturÃƒÂ­n',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3548,1357,'San CristÃƒÂ³bal',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3549,1346,'Ciudad BolÃƒÂ­var',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3550,1356,'CumanÃƒÂ¡',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3551,1352,'MÃƒÂ©rida',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3552,1361,'Cabimas',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3553,1345,'Barinas',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3554,1344,'Turmero',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3555,1353,'Baruta',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3556,1347,'Puerto Cabello',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3557,1349,'Santa Ana de Coro',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3558,1353,'Los Teques',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3559,1349,'Punto Fijo',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3560,1353,'Guarenas',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3561,1355,'Acarigua',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3562,1342,'Puerto La Cruz',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3563,1359,'Ciudad Losada',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3564,1347,'Guacara',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3565,1358,'Valera',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3566,1355,'Guanare',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3567,1356,'CarÃƒÂºpano',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3568,1348,'Catia La Mar',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3569,1342,'El Tigre',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3570,1353,'Guatire',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3571,1350,'Calabozo',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3572,1342,'Pozuelos',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3573,1361,'Ciudad Ojeda',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3574,1353,'Ocumare del Tuy',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3575,1350,'Valle de la Pascua',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3576,1355,'Araure',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3577,1343,'San Fernando de Apure',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3578,1360,'San Felipe',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3579,1344,'El LimÃƒÂ³n',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3580,1030,'Moscow',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3581,1042,'St Petersburg',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3582,1036,'Novosibirsk',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3583,1033,'Nizni Novgorod',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3584,1053,'Jekaterinburg',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3585,1049,'Samara',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3586,1037,'Omsk',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3587,1055,'Kazan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3588,1000,'Ufa',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3589,1058,'TÃ…Â¡eljabinsk',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3590,1046,'Rostov-na-Donu',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3591,1041,'Perm',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3592,1068,'Volgograd',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3593,1070,'Voronez',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3594,1023,'Krasnojarsk',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3595,1050,'Saratov',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3596,1049,'Toljatti',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3597,1066,'Uljanovsk',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3598,1065,'Izevsk',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3599,1022,'Krasnodar',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3600,1010,'Jaroslavl',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3601,1005,'Habarovsk',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3602,1044,'Vladivostok',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3603,1008,'Irkutsk',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3604,996,'Barnaul',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3605,1018,'Novokuznetsk',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3606,1040,'Penza',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3607,1045,'Rjazan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3608,1038,'Orenburg',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3609,1026,'Lipetsk',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3610,1055,'Nabereznyje TÃ…Â¡elny',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3611,1062,'Tula',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3612,1056,'Tjumen',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3613,1018,'Kemerovo',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3614,999,'Astrahan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3615,1057,'Tomsk',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3616,1019,'Kirov',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3617,1009,'Ivanovo',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3618,1061,'TÃ…Â¡eboksary',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3619,1002,'Brjansk',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3620,1063,'Tver',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3621,1025,'Kursk',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3622,1058,'Magnitogorsk',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3623,1012,'Kaliningrad',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3624,1053,'Nizni Tagil',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3625,1032,'Murmansk',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3626,1003,'Ulan-Ude',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3627,1024,'Kurgan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3628,998,'Arkangeli',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3629,1022,'SotÃ…Â¡i',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3630,1051,'Smolensk',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3631,1039,'Orjol',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3632,1052,'Stavropol',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3633,1001,'Belgorod',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3634,1014,'Kaluga',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3635,1067,'Vladimir',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3636,1004,'MahatÃ…Â¡kala',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3637,1069,'TÃ…Â¡erepovets',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3638,1029,'Saransk',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3639,1054,'Tambov',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3640,1034,'Vladikavkaz',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3641,1060,'TÃ…Â¡ita',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3642,1069,'Vologda',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3643,1035,'Veliki Novgorod',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3644,1005,'Komsomolsk-na-Amure',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3645,1021,'Kostroma',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3646,1068,'Volzski',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3647,1046,'Taganrog',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3648,1017,'Petroskoi',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3649,1008,'Bratsk',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3650,1033,'Dzerzinsk',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3651,1007,'Surgut',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3652,1038,'Orsk',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3653,1000,'Sterlitamak',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3654,1008,'Angarsk',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3655,1028,'JoÃ…Â¡kar-Ola',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3656,1010,'Rybinsk',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3657,1018,'Prokopjevsk',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3658,1007,'Niznevartovsk',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3659,1011,'NaltÃ…Â¡ik',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3660,1020,'Syktyvkar',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3661,998,'Severodvinsk',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3662,996,'Bijsk',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3663,1055,'Niznekamsk',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3664,997,'BlagoveÃ…Â¡tÃ…Â¡ensk',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3665,1046,'Ã…Â ahty',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3666,1001,'Staryi Oskol',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3667,1030,'Zelenograd',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3668,1050,'Balakovo',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3669,1022,'Novorossijsk',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3670,1043,'Pihkova',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3671,1058,'Zlatoust',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3672,1047,'Jakutsk',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3673,1031,'Podolsk',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3674,1015,'Petropavlovsk-KamtÃ…Â¡atski',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3675,1053,'Kamensk-Uralski',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3676,1050,'Engels',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3677,1049,'Syzran',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3678,1059,'Grozny',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3679,1046,'NovotÃ…Â¡erkassk',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3680,1041,'Berezniki',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3681,1048,'Juzno-Sahalinsk',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3682,1046,'Volgodonsk',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3683,1006,'Abakan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3684,995,'Maikop',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3685,1058,'Miass',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3686,1022,'Armavir',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3687,1031,'Ljubertsy',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3688,996,'Rubtsovsk',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3689,1067,'Kovrov',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3690,1044,'Nahodka',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3691,1044,'Ussurijsk',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3692,1000,'Salavat',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3693,1031,'MytiÃ…Â¡tÃ…Â¡i',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3694,1031,'Kolomna',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3695,1031,'Elektrostal',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3696,1067,'Murom',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3697,1042,'Kolpino',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3698,1023,'Norilsk',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3699,1055,'Almetjevsk',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3700,1062,'Novomoskovsk',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3701,1066,'Dimitrovgrad',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3702,1053,'Pervouralsk',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3703,1031,'Himki',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3704,1031,'BalaÃ…Â¡iha',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3705,1052,'Nevinnomyssk',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3706,1052,'Pjatigorsk',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3707,1031,'Korolev',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3708,1031,'Serpuhov',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3709,1031,'Odintsovo',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3710,1031,'Orehovo-Zujevo',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3711,1068,'KamyÃ…Â¡in',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3712,1061,'NovotÃ…Â¡eboksarsk',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3713,1016,'TÃ…Â¡erkessk',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3714,1023,'AtÃ…Â¡insk',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3715,1027,'Magadan',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3716,1054,'MitÃ…Â¡urinsk',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3717,1052,'Kislovodsk',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3718,1026,'Jelets',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3719,1057,'Seversk',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3720,1031,'Noginsk',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3721,1043,'Velikije Luki',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3722,1049,'NovokuibyÃ…Â¡evsk',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3723,1000,'Neftekamsk',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3724,1018,'Leninsk-Kuznetski',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3725,1000,'Oktjabrski',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3726,1031,'Sergijev Posad',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3727,1033,'Arzamas',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3728,1018,'Kiseljovsk',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3729,1038,'Novotroitsk',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3730,1014,'Obninsk',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3731,1023,'Kansk',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3732,1065,'Glazov',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3733,1041,'Solikamsk',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3734,1065,'Sarapul',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3735,1008,'Ust-Ilimsk',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3736,1031,'Ã…Â tÃ…Â¡olkovo',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3737,1018,'MezduretÃ…Â¡ensk',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3738,1008,'Usolje-Sibirskoje',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3739,1013,'Elista',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3740,1046,'NovoÃ…Â¡ahtinsk',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3741,1065,'Votkinsk',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3742,1064,'Kyzyl',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3743,1053,'Serov',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3744,1055,'Zelenodolsk',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3745,1031,'Zeleznodoroznyi',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3746,1009,'KineÃ…Â¡ma',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3747,1040,'Kuznetsk',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3748,1020,'Uhta',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3749,1052,'Jessentuki',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3750,1056,'Tobolsk',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3751,1007,'Neftejugansk',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3752,1046,'Bataisk',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3753,1071,'Nojabrsk',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3754,1050,'BalaÃ…Â¡ov',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3755,1025,'Zeleznogorsk',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3756,1031,'Zukovski',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3757,1018,'Anzero-Sudzensk',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3758,1055,'Bugulma',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3759,1023,'Zeleznogorsk',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3760,1053,'Novouralsk',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3761,1042,'PuÃ…Â¡kin',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3762,1020,'Vorkuta',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3763,1004,'Derbent',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3764,1019,'Kirovo-TÃ…Â¡epetsk',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3765,1031,'Krasnogorsk',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3766,1031,'Klin',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3767,1041,'TÃ…Â¡aikovski',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3768,1071,'Novyi Urengoi',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3769,1374,'Ho Chi Minh City',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3770,1373,'Hanoi',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3771,1372,'Haiphong',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3772,1381,'Da Nang',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3773,1371,'BiÃƒÂªn Hoa',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3774,1375,'Nha Trang',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3775,1383,'Hue',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3776,1369,'Can Tho',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3777,1380,'Cam Pha',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3778,1378,'Nam Dinh',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3779,1367,'Quy Nhon',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3780,1365,'Vung Tau',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3781,1376,'Rach Gia',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3782,1364,'Long Xuyen',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3783,1366,'Thai Nguyen',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3784,1382,'Hong Gai',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3785,1368,'Phan ThiÃƒÂªt',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3786,1375,'Cam Ranh',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3787,1379,'Vinh',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3788,1384,'My Tho',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3789,1377,'Da Lat',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3790,1370,'Buon Ma Thuot',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3791,374,'Tallinn',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3792,375,'Tartu',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3793,1312,'New York',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3794,1286,'Los Angeles',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3795,1294,'Chicago',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3796,1322,'Houston',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3797,1317,'Philadelphia',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3798,1284,'Phoenix',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3799,1286,'San Diego',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3800,1322,'Dallas',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3801,1322,'San Antonio',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3802,1302,'Detroit',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3803,1286,'San Jose',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3804,1295,'Indianapolis',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3805,1286,'San Francisco',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3806,1290,'Jacksonville',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3807,1314,'Columbus',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3808,1322,'Austin',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3809,1300,'Baltimore',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3810,1321,'Memphis',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3811,1326,'Milwaukee',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3812,1301,'Boston',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3813,1289,'Washington',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3814,1321,'Nashville-Davidson',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3815,1322,'El Paso',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3816,1325,'Seattle',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3817,1287,'Denver',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3818,1313,'Charlotte',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3819,1322,'Fort Worth',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3820,1316,'Portland',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3821,1315,'Oklahoma City',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3822,1284,'Tucson',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3823,1299,'New Orleans',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3824,1308,'Las Vegas',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3825,1314,'Cleveland',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3826,1286,'Long Beach',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3827,1311,'Albuquerque',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3828,1305,'Kansas City',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3829,1286,'Fresno',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3830,1324,'Virginia Beach',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3831,1291,'Atlanta',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3832,1286,'Sacramento',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3833,1286,'Oakland',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3834,1284,'Mesa',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3835,1315,'Tulsa',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3836,1307,'Omaha',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3837,1303,'Minneapolis',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3838,1292,'Honolulu',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3839,1290,'Miami',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3840,1287,'Colorado Springs',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3841,1305,'Saint Louis',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3842,1297,'Wichita',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3843,1286,'Santa Ana',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3844,1317,'Pittsburgh',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3845,1322,'Arlington',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3846,1314,'Cincinnati',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3847,1286,'Anaheim',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3848,1314,'Toledo',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3849,1290,'Tampa',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3850,1312,'Buffalo',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3851,1303,'Saint Paul',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3852,1322,'Corpus Christi',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3853,1287,'Aurora',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3854,1313,'Raleigh',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3855,1310,'Newark',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3856,1298,'Lexington-Fayette',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3857,1283,'Anchorage',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3858,1298,'Louisville',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3859,1286,'Riverside',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3860,1290,'Saint Petersburg',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3861,1286,'Bakersfield',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3862,1286,'Stockton',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3863,1282,'Birmingham',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3864,1310,'Jersey City',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3865,1324,'Norfolk',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3866,1299,'Baton Rouge',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3867,1290,'Hialeah',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3868,1307,'Lincoln',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3869,1313,'Greensboro',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3870,1322,'Plano',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3871,1312,'Rochester',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3872,1284,'Glendale',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3873,1314,'Akron',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3874,1322,'Garland',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3875,1326,'Madison',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3876,1295,'Fort Wayne',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3877,1286,'Fremont',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3878,1284,'Scottsdale',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3879,1282,'Montgomery',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3880,1299,'Shreveport',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3881,1291,'Augusta-Richmond County',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3882,1322,'Lubbock',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3883,1324,'Chesapeake',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3884,1282,'Mobile',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3885,1296,'Des Moines',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3886,1302,'Grand Rapids',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3887,1324,'Richmond',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3888,1312,'Yonkers',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3889,1325,'Spokane',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3890,1286,'Glendale',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3891,1325,'Tacoma',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3892,1322,'Irving',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3893,1286,'Huntington Beach',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3894,1286,'Modesto',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3895,1313,'Durham',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3896,1291,'Columbus',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3897,1290,'Orlando',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3898,1293,'Boise City',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3899,1313,'Winston-Salem',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3900,1286,'San Bernardino',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3901,1304,'Jackson',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3902,1285,'Little Rock',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3903,1323,'Salt Lake City',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3904,1308,'Reno',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3905,1324,'Newport News',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3906,1284,'Chandler',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3907,1322,'Laredo',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3908,1308,'Henderson',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3909,1324,'Arlington',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3910,1321,'Knoxville',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3911,1322,'Amarillo',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3912,1318,'Providence',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3913,1286,'Chula Vista',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3914,1301,'Worcester',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3915,1286,'Oxnard',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3916,1314,'Dayton',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3917,1286,'Garden Grove',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3918,1286,'Oceanside',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3919,1284,'Tempe',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3920,1282,'Huntsville',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3921,1286,'Ontario',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3922,1321,'Chattanooga',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3923,1290,'Fort Lauderdale',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3924,1301,'Springfield',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3925,1305,'Springfield',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3926,1286,'Santa Clarita',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3927,1286,'Salinas',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3928,1290,'Tallahassee',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3929,1294,'Rockford',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3930,1286,'Pomona',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3931,1299,'Metairie',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3932,1310,'Paterson',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3933,1297,'Overland Park',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3934,1286,'Santa Rosa',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3935,1312,'Syracuse',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3936,1297,'Kansas City',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3937,1324,'Hampton',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3938,1287,'Lakewood',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3939,1325,'Vancouver',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3940,1286,'Irvine',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3941,1294,'Aurora',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3942,1286,'Moreno Valley',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3943,1286,'Pasadena',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3944,1286,'Hayward',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3945,1322,'Brownsville',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3946,1288,'Bridgeport',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3947,1290,'Hollywood',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3948,1302,'Warren',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3949,1286,'Torrance',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3950,1316,'Eugene',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3951,1290,'Pembroke Pines',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3952,1316,'Salem',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3953,1322,'Pasadena',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3954,1286,'Escondido',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3955,1286,'Sunnyvale',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3956,1291,'Savannah',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3957,1286,'Fontana',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3958,1286,'Orange',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3959,1294,'Naperville',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3960,1324,'Alexandria',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3961,1286,'Rancho Cucamonga',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3962,1322,'Grand Prairie',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3963,1286,'East Los Angeles',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3964,1286,'Fullerton',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3965,1286,'Corona',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3966,1302,'Flint',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3967,1308,'Paradise',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3968,1322,'Mesquite',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3969,1302,'Sterling Heights',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3970,1320,'Sioux Falls',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3971,1288,'New Haven',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3972,1297,'Topeka',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3973,1286,'Concord',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3974,1295,'Evansville',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3975,1288,'Hartford',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3976,1313,'Fayetteville',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3977,1296,'Cedar Rapids',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3978,1310,'Elizabeth',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3979,1302,'Lansing',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3980,1286,'Lancaster',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3981,1287,'Fort Collins',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3982,1290,'Coral Springs',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3983,1288,'Stamford',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3984,1286,'Thousand Oaks',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3985,1286,'Vallejo',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3986,1286,'Palmdale',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3987,1319,'Columbia',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3988,1286,'El Monte',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3989,1322,'Abilene',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3990,1308,'North Las Vegas',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3991,1302,'Ann Arbor',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3992,1322,'Beaumont',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3993,1322,'Waco',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3994,1291,'Macon',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3995,1305,'Independence',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3996,1294,'Peoria',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3997,1286,'Inglewood',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3998,1294,'Springfield',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(3999,1286,'Simi Valley',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(4000,1299,'Lafayette',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(4001,1284,'Gilbert',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(4002,1322,'Carrollton',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(4003,1325,'Bellevue',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(4004,1323,'West Valley City',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(4005,1321,'Clarksville',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(4006,1286,'Costa Mesa',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(4007,1284,'Peoria',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(4008,1295,'South Bend',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(4009,1286,'Downey',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(4010,1288,'Waterbury',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(4011,1309,'Manchester',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(4012,1317,'Allentown',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(4013,1322,'McAllen',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(4014,1294,'Joliet',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(4015,1301,'Lowell',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(4016,1323,'Provo',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(4017,1286,'West Covina',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(4018,1322,'Wichita Falls',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(4019,1317,'Erie',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(4020,1286,'Daly City',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(4021,1286,'Citrus Heights',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(4022,1286,'Norwalk',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(4023,1295,'Gary',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(4024,1286,'Berkeley',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(4025,1286,'Santa Clara',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(4026,1326,'Green Bay',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(4027,1290,'Cape Coral',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(4028,1287,'Arvada',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(4029,1287,'Pueblo',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(4030,1323,'Sandy',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(4031,1291,'Athens-Clarke County',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(4032,1301,'Cambridge',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(4033,1287,'Westminster',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(4034,1286,'San Buenaventura',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(4035,1324,'Portsmouth',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(4036,1302,'Livonia',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(4037,1286,'Burbank',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(4038,1290,'Clearwater',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(4039,1322,'Midland',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(4040,1296,'Davenport',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(4041,1286,'Mission Viejo',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(4042,1290,'Miami Beach',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(4043,1308,'Sunrise Manor',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(4044,1301,'New Bedford',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(4045,1286,'El Cajon',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(4046,1315,'Norman',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(4047,1286,'Richmond',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(4048,1312,'Albany',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(4049,1301,'Brockton',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(4050,1324,'Roanoke',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(4051,1306,'Billings',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(4052,1286,'Compton',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(4053,1290,'Gainesville',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(4054,1286,'Fairfield',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(4055,1286,'Arden-Arcade',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(4056,1286,'San Mateo',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(4057,1286,'Visalia',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(4058,1287,'Boulder',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(4059,1313,'Cary',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(4060,1286,'Santa Monica',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(4061,1301,'Fall River',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(4062,1326,'Kenosha',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(4063,1294,'Elgin',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(4064,1322,'Odessa',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(4065,1286,'Carson',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(4066,1319,'Charleston',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(4067,1363,'Charlotte Amalie',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(4068,1410,'Harare',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(4069,1409,'Bulawayo',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(4070,1410,'Chitungwiza',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(4071,1410,'Mount Darwin',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(4072,1411,'Mutare',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(4073,1412,'Gweru',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(4074,957,'Gaza',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(4075,959,'Khan Yunis',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(4076,958,'Hebron',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(4077,961,'Jabaliya',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(4078,960,'Nablus',1,'2012-08-24 12:21:26','2012-08-24 12:21:26'),(4079,962,'Rafah',1,'2012-08-24 12:21:26','2012-08-24 12:21:26');

/*Table structure for table `tbl_countries` */

DROP TABLE IF EXISTS `tbl_countries`;

CREATE TABLE `tbl_countries` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `country_name` varchar(50) NOT NULL,
  `country_code` char(10) NOT NULL,
  `country_code2` char(2) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=240 DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Data for the table `tbl_countries` */

insert  into `tbl_countries`(`id`,`country_name`,`country_code`,`country_code2`,`is_active`,`created`,`modified`) values (1,'Aruba','ABW','AW',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(2,'Afghanistan','AFG','AF',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(3,'Angola','AGO','AO',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(4,'Anguilla','AIA','AI',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(5,'Albania','ALB','AL',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(6,'Andorra','AND','AD',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(7,'Netherlands Antilles','ANT','AN',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(8,'United Arab Emirates','ARE','AE',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(9,'Argentina','ARG','AR',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(10,'Armenia','ARM','AM',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(11,'American Samoa','ASM','AS',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(12,'Antarctica','ATA','AQ',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(13,'French Southern territories','ATF','TF',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(14,'Antigua and Barbuda','ATG','AG',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(15,'Australia','AUS','AU',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(16,'Austria','AUT','AT',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(17,'Azerbaijan','AZE','AZ',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(18,'Burundi','BDI','BI',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(19,'Belgium','BEL','BE',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(20,'Benin','BEN','BJ',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(21,'Burkina Faso','BFA','BF',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(22,'Bangladesh','BGD','BD',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(23,'Bulgaria','BGR','BG',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(24,'Bahrain','BHR','BH',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(25,'Bahamas','BHS','BS',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(26,'Bosnia and Herzegovina','BIH','BA',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(27,'Belarus','BLR','BY',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(28,'Belize','BLZ','BZ',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(29,'Bermuda','BMU','BM',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(30,'Bolivia','BOL','BO',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(31,'Brazil','BRA','BR',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(32,'Barbados','BRB','BB',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(33,'Brunei','BRN','BN',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(34,'Bhutan','BTN','BT',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(35,'Bouvet Island','BVT','BV',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(36,'Botswana','BWA','BW',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(37,'Central African Republic','CAF','CF',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(38,'Canada','CAN','CA',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(39,'Cocos (Keeling) Islands','CCK','CC',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(40,'Switzerland','CHE','CH',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(41,'Chile','CHL','CL',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(42,'China','CHN','CN',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(43,'CÃƒÂ´te dÃ¢â‚¬â„¢Ivoire','CIV','CI',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(44,'Cameroon','CMR','CM',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(45,'Congo, The Democratic Republic of the','COD','CD',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(46,'Congo','COG','CG',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(47,'Cook Islands','COK','CK',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(48,'Colombia','COL','CO',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(49,'Comoros','COM','KM',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(50,'Cape Verde','CPV','CV',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(51,'Costa Rica','CRI','CR',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(52,'Cuba','CUB','CU',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(53,'Christmas Island','CXR','CX',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(54,'Cayman Islands','CYM','KY',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(55,'Cyprus','CYP','CY',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(56,'Czech Republic','CZE','CZ',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(57,'Germany','DEU','DE',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(58,'Djibouti','DJI','DJ',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(59,'Dominica','DMA','DM',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(60,'Denmark','DNK','DK',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(61,'Dominican Republic','DOM','DO',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(62,'Algeria','DZA','DZ',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(63,'Ecuador','ECU','EC',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(64,'Egypt','EGY','EG',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(65,'Eritrea','ERI','ER',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(66,'Western Sahara','ESH','EH',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(67,'Spain','ESP','ES',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(68,'Estonia','EST','EE',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(69,'Ethiopia','ETH','ET',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(70,'Finland','FIN','FI',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(71,'Fiji Islands','FJI','FJ',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(72,'Falkland Islands','FLK','FK',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(73,'France','FRA','FR',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(74,'Faroe Islands','FRO','FO',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(75,'Micronesia, Federated States of','FSM','FM',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(76,'Gabon','GAB','GA',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(77,'United Kingdom','GBR','GB',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(78,'Georgia','GEO','GE',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(79,'Ghana','GHA','GH',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(80,'Gibraltar','GIB','GI',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(81,'Guinea','GIN','GN',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(82,'Guadeloupe','GLP','GP',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(83,'Gambia','GMB','GM',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(84,'Guinea-Bissau','GNB','GW',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(85,'Equatorial Guinea','GNQ','GQ',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(86,'Greece','GRC','GR',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(87,'Grenada','GRD','GD',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(88,'Greenland','GRL','GL',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(89,'Guatemala','GTM','GT',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(90,'French Guiana','GUF','GF',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(91,'Guam','GUM','GU',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(92,'Guyana','GUY','GY',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(93,'Hong Kong','HKG','HK',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(94,'Heard Island and McDonald Islands','HMD','HM',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(95,'Honduras','HND','HN',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(96,'Croatia','HRV','HR',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(97,'Haiti','HTI','HT',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(98,'Hungary','HUN','HU',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(99,'Indonesia','IDN','ID',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(100,'India','IND','IN',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(101,'British Indian Ocean Territory','IOT','IO',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(102,'Ireland','IRL','IE',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(103,'Iran','IRN','IR',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(104,'Iraq','IRQ','IQ',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(105,'Iceland','ISL','IS',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(106,'Israel','ISR','IL',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(107,'Italy','ITA','IT',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(108,'Jamaica','JAM','JM',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(109,'Jordan','JOR','JO',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(110,'Japan','JPN','JP',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(111,'Kazakstan','KAZ','KZ',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(112,'Kenya','KEN','KE',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(113,'Kyrgyzstan','KGZ','KG',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(114,'Cambodia','KHM','KH',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(115,'Kiribati','KIR','KI',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(116,'Saint Kitts and Nevis','KNA','KN',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(117,'South Korea','KOR','KR',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(118,'Kuwait','KWT','KW',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(119,'Laos','LAO','LA',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(120,'Lebanon','LBN','LB',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(121,'Liberia','LBR','LR',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(122,'Libyan Arab Jamahiriya','LBY','LY',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(123,'Saint Lucia','LCA','LC',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(124,'Liechtenstein','LIE','LI',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(125,'Sri Lanka','LKA','LK',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(126,'Lesotho','LSO','LS',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(127,'Lithuania','LTU','LT',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(128,'Luxembourg','LUX','LU',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(129,'Latvia','LVA','LV',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(130,'Macao','MAC','MO',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(131,'Morocco','MAR','MA',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(132,'Monaco','MCO','MC',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(133,'Moldova','MDA','MD',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(134,'Madagascar','MDG','MG',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(135,'Maldives','MDV','MV',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(136,'Mexico','MEX','MX',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(137,'Marshall Islands','MHL','MH',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(138,'Macedonia','MKD','MK',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(139,'Mali','MLI','ML',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(140,'Malta','MLT','MT',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(141,'Myanmar','MMR','MM',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(142,'Mongolia','MNG','MN',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(143,'Northern Mariana Islands','MNP','MP',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(144,'Mozambique','MOZ','MZ',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(145,'Mauritania','MRT','MR',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(146,'Montserrat','MSR','MS',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(147,'Martinique','MTQ','MQ',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(148,'Mauritius','MUS','MU',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(149,'Malawi','MWI','MW',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(150,'Malaysia','MYS','MY',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(151,'Mayotte','MYT','YT',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(152,'Namibia','NAM','NA',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(153,'New Caledonia','NCL','NC',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(154,'Niger','NER','NE',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(155,'Norfolk Island','NFK','NF',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(156,'Nigeria','NGA','NG',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(157,'Nicaragua','NIC','NI',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(158,'Niue','NIU','NU',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(159,'Netherlands','NLD','NL',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(160,'Norway','NOR','NO',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(161,'Nepal','NPL','NP',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(162,'Nauru','NRU','NR',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(163,'New Zealand','NZL','NZ',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(164,'Oman','OMN','OM',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(165,'Pakistan','PAK','PK',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(166,'Panama','PAN','PA',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(167,'Pitcairn','PCN','PN',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(168,'Peru','PER','PE',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(169,'Philippines','PHL','PH',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(170,'Palau','PLW','PW',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(171,'Papua New Guinea','PNG','PG',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(172,'Poland','POL','PL',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(173,'Puerto Rico','PRI','PR',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(174,'North Korea','PRK','KP',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(175,'Portugal','PRT','PT',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(176,'Paraguay','PRY','PY',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(177,'Palestine','PSE','PS',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(178,'French Polynesia','PYF','PF',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(179,'Qatar','QAT','QA',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(180,'RÃƒÂ©union','REU','RE',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(181,'Romania','ROM','RO',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(182,'Russian Federation','RUS','RU',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(183,'Rwanda','RWA','RW',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(184,'Saudi Arabia','SAU','SA',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(185,'Sudan','SDN','SD',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(186,'Senegal','SEN','SN',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(187,'Singapore','SGP','SG',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(188,'South Georgia and the South Sandwich Islands','SGS','GS',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(189,'Saint Helena','SHN','SH',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(190,'Svalbard and Jan Mayen','SJM','SJ',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(191,'Solomon Islands','SLB','SB',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(192,'Sierra Leone','SLE','SL',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(193,'El Salvador','SLV','SV',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(194,'San Marino','SMR','SM',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(195,'Somalia','SOM','SO',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(196,'Saint Pierre and Miquelon','SPM','PM',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(197,'Sao Tome and Principe','STP','ST',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(198,'Suriname','SUR','SR',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(199,'Slovakia','SVK','SK',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(200,'Slovenia','SVN','SI',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(201,'Sweden','SWE','SE',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(202,'Swaziland','SWZ','SZ',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(203,'Seychelles','SYC','SC',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(204,'Syria','SYR','SY',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(205,'Turks and Caicos Islands','TCA','TC',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(206,'Chad','TCD','TD',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(207,'Togo','TGO','TG',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(208,'Thailand','THA','TH',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(209,'Tajikistan','TJK','TJ',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(210,'Tokelau','TKL','TK',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(211,'Turkmenistan','TKM','TM',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(212,'East Timor','TMP','TP',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(213,'Tonga','TON','TO',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(214,'Trinidad and Tobago','TTO','TT',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(215,'Tunisia','TUN','TN',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(216,'Turkey','TUR','TR',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(217,'Tuvalu','TUV','TV',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(218,'Taiwan','TWN','TW',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(219,'Tanzania','TZA','TZ',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(220,'Uganda','UGA','UG',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(221,'Ukraine','UKR','UA',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(222,'United States Minor Outlying Islands','UMI','UM',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(223,'Uruguay','URY','UY',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(224,'United States','USA','US',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(225,'Uzbekistan','UZB','UZ',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(226,'Holy See (Vatican City State)','VAT','VA',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(227,'Saint Vincent and the Grenadines','VCT','VC',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(228,'Venezuela','VEN','VE',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(229,'Virgin Islands, British','VGB','VG',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(230,'Virgin Islands, U.S.','VIR','VI',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(231,'Vietnam','VNM','VN',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(232,'Vanuatu','VUT','VU',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(233,'Wallis and Futuna','WLF','WF',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(234,'Samoa','WSM','WS',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(235,'Yemen','YEM','YE',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(236,'Yugoslavia','YUG','YU',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(237,'South Africa','ZAF','ZA',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(238,'Zambia','ZMB','ZM',1,'2012-08-23 12:38:04','2012-08-23 12:38:04'),(239,'Zimbabwe','ZWE','ZW',1,'2012-08-23 12:38:04','2012-08-23 12:38:04');

/*Table structure for table `tbl_employmentstatus` */

DROP TABLE IF EXISTS `tbl_employmentstatus`;

CREATE TABLE `tbl_employmentstatus` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `employemnt_status` varchar(100) DEFAULT NULL,
  `createdby` int(20) unsigned DEFAULT NULL,
  `modifiedby` int(20) unsigned DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `isactive` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

/*Data for the table `tbl_employmentstatus` */

insert  into `tbl_employmentstatus`(`id`,`employemnt_status`,`createdby`,`modifiedby`,`createddate`,`modifieddate`,`isactive`) values (1,'Full Time',2,2,'2013-09-02 19:43:52','2013-09-02 19:43:52',1),(2,'Part Time',2,2,'2013-09-02 19:44:06','2013-09-02 19:44:06',1),(3,'Permanent',2,2,'2013-09-02 19:44:28','2013-09-02 19:44:28',1),(4,'Temporary',2,2,'2013-09-02 19:44:34','2013-09-02 19:44:34',1),(5,'Probationary',2,2,'2013-09-02 19:44:44','2013-09-02 19:44:44',1),(6,'Contract',2,2,'2013-09-02 19:44:49','2013-09-02 19:44:49',1),(7,'Deputation',2,2,'2013-09-02 19:44:56','2013-09-02 19:44:56',1),(8,'Resigned',2,2,'2013-09-16 11:06:40','2013-09-16 11:06:40',1),(9,'Left',2,2,'2013-09-16 11:06:40','2013-09-16 11:06:40',1),(10,'Suspended',2,2,'2013-09-16 11:06:40','2013-09-16 11:06:40',1);

/*Table structure for table `tbl_months` */

DROP TABLE IF EXISTS `tbl_months`;

CREATE TABLE `tbl_months` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `monthid` bigint(20) unsigned DEFAULT NULL,
  `month_name` varchar(50) DEFAULT NULL,
  `createdby` bigint(20) unsigned DEFAULT NULL,
  `modifiedby` bigint(20) unsigned DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `isactive` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;

/*Data for the table `tbl_months` */

insert  into `tbl_months`(`id`,`monthid`,`month_name`,`createdby`,`modifiedby`,`createddate`,`modifieddate`,`isactive`) values (1,1,'January',10,10,'2013-09-24 14:33:21','2013-09-24 14:33:21',1),(2,2,'February',10,10,'2013-09-24 14:33:29','2013-09-24 14:33:29',1),(3,3,'March',10,10,'2013-09-24 14:33:33','2013-09-24 14:33:33',1),(4,4,'April',10,10,'2013-09-24 14:33:41','2013-09-24 14:33:41',1),(5,5,'May',10,10,'2013-09-24 14:33:44','2013-09-24 14:33:44',1),(6,6,'June',10,10,'2013-09-24 14:33:48','2013-09-24 14:33:48',1),(7,7,'July',10,10,'2013-09-24 14:33:51','2013-09-24 14:33:51',1),(8,8,'August',10,10,'2013-09-24 14:33:55','2013-09-24 14:33:55',1),(9,9,'September',10,10,'2013-09-24 14:34:06','2013-09-24 14:34:06',1),(10,10,'October',10,10,'2013-09-24 14:34:12','2013-09-24 14:34:12',1),(11,11,'November',10,10,'2013-09-24 14:34:18','2013-09-24 14:34:18',1),(12,12,'December',10,10,'2013-09-24 14:34:23','2013-09-24 14:34:23',1);

/*Table structure for table `tbl_password` */

DROP TABLE IF EXISTS `tbl_password`;

CREATE TABLE `tbl_password` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `passwordtype` varchar(255) DEFAULT NULL,
  `description` text,
  `createdby` int(11) unsigned DEFAULT NULL,
  `modifiedby` int(11) unsigned DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `isactive` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

/*Data for the table `tbl_password` */

insert  into `tbl_password`(`id`,`passwordtype`,`description`,`createdby`,`modifiedby`,`createddate`,`modifieddate`,`isactive`) values (1,'Alphanumeric','The password should contain atleast one alphabet and one number.\r\nThe minimum length should be 6 characters and maximum length should be 15 characters.\r\nEg: myname123',4,4,'2013-08-16 17:21:32','2013-08-16 17:21:32',1),(2,'Alphanumeric and Special Characters','The password should contain atleast one alphabet, one number and one special character. \r\nThe minimum length should be 6 characters and maximum length should be 15 characters.\r\nAllowed special characters are .-#$@&_*\r\nExample: myname123@#\r\n',4,4,'2013-08-16 17:22:34','2013-08-16 17:22:34',1),(3,'Only Numbers','The password should contain only numbers.\r\nThe minimum length should be 6 characters and maximum length should be 15 characters.\r\nEg: 123456',4,4,'2013-08-16 17:23:18','2013-08-16 17:23:18',1),(4,'Numbers and Special Characters','The password should contain atleast one number and one special character.\r\nThe minimum length should be 6 characters and maximum length should be 15 characters. \r\nAllowed special characters are .-#$@&_*\r\n\r\nExample: 1234@#$',4,4,'2013-08-16 17:23:56','2013-08-16 17:23:56',1);

/*Table structure for table `tbl_states` */

DROP TABLE IF EXISTS `tbl_states`;

CREATE TABLE `tbl_states` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `country_id` bigint(20) unsigned NOT NULL,
  `state_name` varchar(50) NOT NULL,
  `state_code` varchar(10) DEFAULT NULL,
  `map_point_x` int(3) DEFAULT NULL,
  `map_point_y` int(3) DEFAULT NULL,
  `isactive` tinyint(4) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `FK_tbl_states` (`country_id`),
  CONSTRAINT `FK_tbl_states_countries` FOREIGN KEY (`country_id`) REFERENCES `tbl_countries` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1413 DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Data for the table `tbl_states` */

insert  into `tbl_states`(`id`,`country_id`,`state_name`,`state_code`,`map_point_x`,`map_point_y`,`isactive`,`created`,`modified`) values (1,1,'Aruba',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(2,2,'Balkh',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(3,2,'Herat',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(4,2,'Kabol',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(5,2,'Qandahar',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(6,3,'Benguela',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(7,3,'Huambo',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(8,3,'Luanda',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(9,3,'Namibe',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(10,4,'Anguilla',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(11,5,'Tirana',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(12,6,'Andorra la Vella',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(13,7,'CuraÃƒÂ§ao',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(14,8,'Abu Dhabi',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(15,8,'Ajman',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(16,8,'Dubai',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(17,8,'Sharja',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(18,9,'Buenos Aires',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(19,9,'Catamarca',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(20,9,'Chaco',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(21,9,'Chubut',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(22,9,'CÃƒÂ³rdoba',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(23,9,'Corrientes',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(24,9,'Distrito Federal',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(25,9,'Entre Rios',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(26,9,'Formosa',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(27,9,'Jujuy',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(28,9,'La Rioja',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(29,9,'Mendoza',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(30,9,'Misiones',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(31,9,'NeuquÃƒÂ©n',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(32,9,'Salta',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(33,9,'San Juan',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(34,9,'San Luis',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(35,9,'Santa FÃƒÂ©',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(36,9,'Santiago del Estero',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(37,9,'TucumÃƒÂ¡n',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(38,10,'Lori',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(39,10,'Ã…Â irak',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(40,10,'Yerevan',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(41,11,'Tutuila',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(42,14,'St John',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(43,15,'Capital Region',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(44,15,'New South Wales',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(45,15,'Queensland',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(46,15,'South Australia',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(47,15,'Tasmania',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(48,15,'Victoria',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(49,15,'West Australia',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(50,16,'KÃƒÂ¤rnten',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(51,16,'North Austria',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(52,16,'Salzburg',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(53,16,'Steiermark',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(54,16,'Tiroli',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(55,16,'Wien',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(56,17,'Baki',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(57,17,'GÃƒÂ¤ncÃƒÂ¤',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(58,17,'MingÃƒÂ¤ÃƒÂ§evir',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(59,17,'Sumqayit',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(60,18,'Bujumbura',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(61,19,'Antwerpen',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(62,19,'Bryssel',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(63,19,'East Flanderi',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(64,19,'Hainaut',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(65,19,'LiÃƒÂ¨ge',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(66,19,'Namur',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(67,19,'West Flanderi',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(68,20,'Atacora',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(69,20,'Atlantique',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(70,20,'Borgou',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(71,20,'OuÃƒÂ©mÃƒÂ©',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(72,21,'BoulkiemdÃƒÂ©',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(73,21,'Houet',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(74,21,'Kadiogo',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(75,22,'Barisal',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(76,22,'Chittagong',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(77,22,'Dhaka',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(78,22,'Khulna',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(79,22,'Rajshahi',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(80,22,'Sylhet',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(81,23,'Burgas',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(82,23,'Grad Sofija',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(83,23,'Haskovo',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(84,23,'Lovec',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(85,23,'Plovdiv',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(86,23,'Ruse',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(87,23,'Varna',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(88,24,'al-Manama',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(89,25,'New Providence',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(90,26,'Federaatio',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(91,26,'Republika Srpska',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(92,27,'Brest',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(93,27,'Gomel',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(94,27,'Grodno',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(95,27,'Horad Minsk',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(96,27,'Minsk',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(97,27,'Mogiljov',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(98,27,'Vitebsk',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(99,28,'Belize City',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(100,28,'Cayo',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(101,29,'Hamilton',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(102,29,'Saint GeorgeÃ‚Â´s',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(103,30,'Chuquisaca',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(104,30,'Cochabamba',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(105,30,'La Paz',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(106,30,'Oruro',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(107,30,'PotosÃƒÂ­',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(108,30,'Santa Cruz',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(109,30,'Tarija',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(110,31,'Acre',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(111,31,'Alagoas',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(112,31,'AmapÃƒÂ¡',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(113,31,'Amazonas',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(114,31,'Bahia',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(115,31,'CearÃƒÂ¡',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(116,31,'Distrito Federal',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(117,31,'EspÃƒÂ­rito Santo',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(118,31,'GoiÃƒÂ¡s',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(119,31,'MaranhÃƒÂ£o',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(120,31,'Mato Grosso',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(121,31,'Mato Grosso do Sul',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(122,31,'Minas Gerais',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(123,31,'ParÃƒÂ¡',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(124,31,'ParaÃƒÂ­ba',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(125,31,'ParanÃƒÂ¡',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(126,31,'Pernambuco',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(127,31,'PiauÃƒÂ­',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(128,31,'Rio de Janeiro',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(129,31,'Rio Grande do Norte',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(130,31,'Rio Grande do Sul',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(131,31,'RondÃƒÂ´nia',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(132,31,'Roraima',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(133,31,'Santa Catarina',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(134,31,'SÃƒÂ£o Paulo',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(135,31,'Sergipe',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(136,31,'Tocantins',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(137,32,'St Michael',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(138,33,'Brunei and Muara',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(139,34,'Thimphu',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(140,36,'Francistown',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(141,36,'Gaborone',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(142,37,'Bangui',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(143,38,'Alberta',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(144,38,'British Colombia',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(145,38,'Manitoba',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(146,38,'Newfoundland',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(147,38,'Nova Scotia',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(148,38,'Ontario',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(149,38,'QuÃƒÂ©bec',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(150,38,'Saskatchewan',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(151,39,'Home Island',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(152,39,'West Island',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(153,40,'Basel-Stadt',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(154,40,'Bern',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(155,40,'Geneve',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(156,40,'Vaud',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(157,40,'ZÃƒÂ¼rich',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(158,41,'Antofagasta',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(159,41,'Atacama',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(160,41,'BÃƒÂ­obÃƒÂ­o',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(161,41,'Coquimbo',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(162,41,'La AraucanÃƒÂ­a',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(163,41,'Los Lagos',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(164,41,'Magallanes',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(165,41,'Maule',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(166,41,'OÃ‚Â´Higgins',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(167,41,'Santiago',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(168,41,'TarapacÃƒÂ¡',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(169,41,'ValparaÃƒÂ­so',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(170,42,'Anhui',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(171,42,'Chongqing',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(172,42,'Fujian',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(173,42,'Gansu',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(174,42,'Guangdong',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(175,42,'Guangxi',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(176,42,'Guizhou',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(177,42,'Hainan',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(178,42,'Hebei',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(179,42,'Heilongjiang',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(180,42,'Henan',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(181,42,'Hubei',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(182,42,'Hunan',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(183,42,'Inner Mongolia',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(184,42,'Jiangsu',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(185,42,'Jiangxi',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(186,42,'Jilin',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(187,42,'Liaoning',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(188,42,'Ningxia',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(189,42,'Peking',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(190,42,'Qinghai',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(191,42,'Shaanxi',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(192,42,'Shandong',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(193,42,'Shanghai',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(194,42,'Shanxi',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(195,42,'Sichuan',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(196,42,'Tianjin',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(197,42,'Tibet',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(198,42,'Xinxiang',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(199,42,'Yunnan',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(200,42,'Zhejiang',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(201,43,'Abidjan',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(202,43,'BouakÃƒÂ©',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(203,43,'Daloa',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(204,43,'Korhogo',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(205,43,'Yamoussoukro',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(206,44,'Centre',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(207,44,'ExtrÃƒÂªme-Nord',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(208,44,'Littoral',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(209,44,'Nord',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(210,44,'Nord-Ouest',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(211,44,'Ouest',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(212,45,'Bandundu',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(213,45,'Bas-ZaÃƒÂ¯re',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(214,45,'East Kasai',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(215,45,'Equateur',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(216,45,'Haute-ZaÃƒÂ¯re',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(217,45,'Kinshasa',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(218,45,'North Kivu',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(219,45,'Shaba',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(220,45,'South Kivu',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(221,45,'West Kasai',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(222,46,'Brazzaville',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(223,46,'Kouilou',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(224,47,'Rarotonga',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(225,48,'Antioquia',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(226,48,'AtlÃƒÂ¡ntico',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(227,48,'BolÃƒÂ­var',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(228,48,'BoyacÃƒÂ¡',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(229,48,'Caldas',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(230,48,'CaquetÃƒÂ¡',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(231,48,'Cauca',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(232,48,'Cesar',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(233,48,'CÃƒÂ³rdoba',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(234,48,'Cundinamarca',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(235,48,'Huila',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(236,48,'La Guajira',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(237,48,'Magdalena',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(238,48,'Meta',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(239,48,'NariÃƒÂ±o',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(240,48,'Norte de Santander',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(241,48,'QuindÃƒÂ­o',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(242,48,'Risaralda',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(243,48,'SantafÃƒÂ© de BogotÃƒÂ¡',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(244,48,'Santander',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(245,48,'Sucre',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(246,48,'Tolima',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(247,48,'Valle',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(248,49,'Njazidja',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(249,50,'SÃƒÂ£o Tiago',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(250,51,'San JosÃƒÂ©',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(251,52,'CamagÃƒÂ¼ey',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(252,52,'Ciego de Ãƒï¿½vila',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(253,52,'Cienfuegos',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(254,52,'Granma',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(255,52,'GuantÃƒÂ¡namo',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(256,52,'HolguÃƒÂ­n',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(257,52,'La Habana',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(258,52,'Las Tunas',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(259,52,'Matanzas',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(260,52,'Pinar del RÃƒÂ­o',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(261,52,'Sancti-SpÃƒÂ­ritus',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(262,52,'Santiago de Cuba',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(263,52,'Villa Clara',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(264,53,'Christmas Island',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(265,54,'Grand Cayman',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(266,55,'Limassol',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(267,55,'Nicosia',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(268,56,'HlavnÃƒÂ­ mesto Praha',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(269,56,'JiznÃƒÂ­ Cechy',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(270,56,'JiznÃƒÂ­ Morava',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(271,56,'SevernÃƒÂ­ Cechy',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(272,56,'SevernÃƒÂ­ Morava',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(273,56,'VÃƒÂ½chodnÃƒÂ­ Cechy',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(274,56,'ZapadnÃƒÂ­ Cechy',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(275,57,'Anhalt Sachsen',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(276,57,'Baden-WÃƒÂ¼rttemberg',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(277,57,'Baijeri',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(278,57,'Berliini',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(279,57,'Brandenburg',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(280,57,'Bremen',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(281,57,'Hamburg',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(282,57,'Hessen',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(283,57,'Mecklenburg-Vorpomme',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(284,57,'Niedersachsen',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(285,57,'Nordrhein-Westfalen',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(286,57,'Rheinland-Pfalz',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(287,57,'Saarland',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(288,57,'Saksi',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(289,57,'Schleswig-Holstein',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(290,57,'ThÃƒÂ¼ringen',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(291,58,'Djibouti',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(292,59,'St George',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(293,60,'Ãƒâ€¦rhus',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(294,60,'Frederiksberg',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(295,60,'Fyn',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(296,60,'KÃƒÂ¸benhavn',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(297,60,'Nordjylland',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(298,61,'Distrito Nacional',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(299,61,'Duarte',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(300,61,'La Romana',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(301,61,'Puerto Plata',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(302,61,'San Pedro de MacorÃƒÂ­s',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(303,61,'Santiago',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(304,62,'Alger',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(305,62,'Annaba',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(306,62,'Batna',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(307,62,'BÃƒÂ©char',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(308,62,'BÃƒÂ©jaÃƒÂ¯a',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(309,62,'Biskra',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(310,62,'Blida',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(311,62,'Chlef',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(312,62,'Constantine',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(313,62,'GhardaÃƒÂ¯a',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(314,62,'Mostaganem',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(315,62,'Oran',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(316,62,'SÃƒÂ©tif',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(317,62,'Sidi Bel AbbÃƒÂ¨s',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(318,62,'Skikda',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(319,62,'TÃƒÂ©bessa',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(320,62,'Tiaret',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(321,62,'Tlemcen',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(322,63,'Azuay',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(323,63,'Chimborazo',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(324,63,'El Oro',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(325,63,'Esmeraldas',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(326,63,'Guayas',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(327,63,'Imbabura',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(328,63,'Loja',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(329,63,'Los RÃƒÂ­os',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(330,63,'ManabÃƒÂ­',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(331,63,'Pichincha',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(332,63,'Tungurahua',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(333,64,'al-Buhayra',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(334,64,'al-Daqahliya',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(335,64,'al-Faiyum',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(336,64,'al-Gharbiya',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(337,64,'al-Minufiya',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(338,64,'al-Minya',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(339,64,'al-Qalyubiya',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(340,64,'al-Sharqiya',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(341,64,'Aleksandria',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(342,64,'Assuan',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(343,64,'Asyut',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(344,64,'Bani Suwayf',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(345,64,'Giza',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(346,64,'Ismailia',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(347,64,'Kafr al-Shaykh',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(348,64,'Kairo',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(349,64,'Luxor',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(350,64,'Port Said',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(351,64,'Qina',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(352,64,'Sawhaj',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(353,64,'Shamal Sina',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(354,64,'Suez',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(355,65,'Maekel',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(356,66,'El-AaiÃƒÂºn',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(357,67,'Andalusia',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(358,67,'Aragonia',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(359,67,'Asturia',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(360,67,'Balears',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(361,67,'Baskimaa',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(362,67,'Canary Islands',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(363,67,'Cantabria',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(364,67,'Castilla and LeÃƒÂ³n',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(365,67,'Extremadura',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(366,67,'Galicia',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(367,67,'Kastilia-La Mancha',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(368,67,'Katalonia',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(369,67,'La Rioja',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(370,67,'Madrid',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(371,67,'Murcia',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(372,67,'Navarra',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(373,67,'Valencia',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(374,68,'Harjumaa',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(375,68,'Tartumaa',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(376,69,'Addis Abeba',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(377,69,'Amhara',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(378,69,'Dire Dawa',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(379,69,'Oromia',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(380,69,'Tigray',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(381,70,'Newmaa',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(382,70,'PÃƒÂ¤ijÃƒÂ¤t-HÃƒÂ¤me',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(383,70,'Pirkanmaa',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(384,70,'Pohjois-Pohjanmaa',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(385,70,'Varsinais-Suomi',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(386,71,'Central',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(387,72,'East Falkland',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(388,73,'Alsace',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(389,73,'Aquitaine',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(390,73,'Auvergne',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(391,73,'Basse-Normandie',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(392,73,'Bourgogne',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(393,73,'Bretagne',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(394,73,'Centre',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(395,73,'Champagne-Ardenne',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(396,73,'Franche-ComtÃƒÂ©',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(397,73,'Haute-Normandie',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(398,73,'ÃƒÅ½le-de-France',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(399,73,'Languedoc-Roussillon',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(400,73,'Limousin',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(401,73,'Lorraine',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(402,73,'Midi-PyrÃƒÂ©nÃƒÂ©es',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(403,73,'Nord-Pas-de-Calais',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(404,73,'Pays de la Loire',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(405,73,'Picardie',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(406,73,'Provence-Alpes-CÃƒÂ´te',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(407,73,'RhÃƒÂ´ne-Alpes',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(408,74,'Streymoyar',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(409,75,'Chuuk',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(410,75,'Pohnpei',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(411,76,'Estuaire',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(412,77,'England',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(413,77,'Jersey',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(414,77,'North Ireland',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(415,77,'Scotland',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(416,77,'United Kingdom',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(417,77,'Wales',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(418,78,'Abhasia [Aphazeti]',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(419,78,'Adzaria [AtÃ…Â¡ara]',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(420,78,'Imereti',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(421,78,'Kvemo Kartli',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(422,78,'Tbilisi',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(423,79,'Ashanti',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(424,79,'Greater Accra',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(425,79,'Northern',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(426,79,'Western',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(427,80,'Gibraltar',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(428,81,'Conakry',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(429,82,'Basse-Terre',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(430,82,'Grande-Terre',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(431,83,'Banjul',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(432,83,'Kombo St Mary',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(433,84,'Bissau',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(434,85,'Bioko',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(435,86,'Attika',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(436,86,'Central Macedonia',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(437,86,'Crete',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(438,86,'Thessalia',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(439,86,'West Greece',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(440,87,'St George',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(441,88,'Kitaa',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(442,89,'Guatemala',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(443,89,'Quetzaltenango',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(444,90,'Cayenne',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(445,91,'Guam',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(446,92,'Georgetown',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(447,93,'Hongkong',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(448,93,'Kowloon and New Kowl',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(449,95,'AtlÃƒÂ¡ntida',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(450,95,'CortÃƒÂ©s',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(451,95,'Distrito Central',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(452,96,'Grad Zagreb',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(453,96,'Osijek-Baranja',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(454,96,'Primorje-Gorski Kota',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(455,96,'Split-Dalmatia',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(456,97,'Nord',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(457,97,'Ouest',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(458,98,'BÃƒÂ¡cs-Kiskun',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(459,98,'Baranya',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(460,98,'Borsod-AbaÃƒÂºj-ZemplÃƒÂ©n',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(461,98,'Budapest',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(462,98,'CsongrÃƒÂ¡d',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(463,98,'FejÃƒÂ©r',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(464,98,'GyÃƒÂ¶r-Moson-Sopron',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(465,98,'HajdÃƒÂº-Bihar',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(466,98,'Szabolcs-SzatmÃƒÂ¡r-Ber',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(467,99,'Aceh',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(468,99,'Bali',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(469,99,'Bengkulu',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(470,99,'Central Java',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(471,99,'East Java',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(472,99,'Jakarta Raya',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(473,99,'Jambi',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(474,99,'Kalimantan Barat',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(475,99,'Kalimantan Selatan',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(476,99,'Kalimantan Tengah',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(477,99,'Kalimantan Timur',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(478,99,'Lampung',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(479,99,'Molukit',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(480,99,'Nusa Tenggara Barat',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(481,99,'Nusa Tenggara Timur',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(482,99,'Riau',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(483,99,'Sulawesi Selatan',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(484,99,'Sulawesi Tengah',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(485,99,'Sulawesi Tenggara',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(486,99,'Sulawesi Utara',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(487,99,'Sumatera Barat',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(488,99,'Sumatera Selatan',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(489,99,'Sumatera Utara',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(490,99,'West Irian',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(491,99,'West Java',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(492,99,'Yogyakarta',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(493,100,'Andhra Pradesh',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(494,100,'Assam',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(495,100,'Bihar',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(496,100,'Chandigarh',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(497,100,'Chhatisgarh',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(498,100,'Delhi',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(499,100,'Gujarat',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(500,100,'Haryana',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(501,100,'Jammu and Kashmir',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(502,100,'Jharkhand',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(503,100,'Karnataka',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(504,100,'Kerala',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(505,100,'Madhya Pradesh',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(506,100,'Maharashtra',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(507,100,'Manipur',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(508,100,'Meghalaya',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(509,100,'Mizoram',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(510,100,'Orissa',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(511,100,'Pondicherry',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(512,100,'Punjab',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(513,100,'Rajasthan',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(514,100,'Tamil Nadu',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(515,100,'Tripura',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(516,100,'Uttar Pradesh',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(517,100,'Uttaranchal',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(518,100,'West Bengal',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(519,102,'Leinster',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(520,102,'Munster',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(521,103,'Ardebil',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(522,103,'Bushehr',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(523,103,'Chaharmahal va Bakht',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(524,103,'East Azerbaidzan',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(525,103,'Esfahan',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(526,103,'Fars',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(527,103,'Gilan',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(528,103,'Golestan',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(529,103,'Hamadan',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(530,103,'Hormozgan',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(531,103,'Ilam',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(532,103,'Kerman',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(533,103,'Kermanshah',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(534,103,'Khorasan',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(535,103,'Khuzestan',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(536,103,'Kordestan',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(537,103,'Lorestan',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(538,103,'Markazi',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(539,103,'Mazandaran',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(540,103,'Qazvin',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(541,103,'Qom',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(542,103,'Semnan',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(543,103,'Sistan va Baluchesta',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(544,103,'Teheran',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(545,103,'West Azerbaidzan',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(546,103,'Yazd',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(547,103,'Zanjan',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(548,104,'al-Anbar',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(549,104,'al-Najaf',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(550,104,'al-Qadisiya',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(551,104,'al-Sulaymaniya',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(552,104,'al-Tamim',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(553,104,'Babil',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(554,104,'Baghdad',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(555,104,'Basra',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(556,104,'DhiQar',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(557,104,'Diyala',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(558,104,'Irbil',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(559,104,'Karbala',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(560,104,'Maysan',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(561,104,'Ninawa',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(562,104,'Wasit',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(563,105,'HÃƒÂ¶fuÃƒÂ°borgarsvÃƒÂ¦ÃƒÂ°i',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(564,106,'Ha Darom',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(565,106,'Ha Merkaz',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(566,106,'Haifa',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(567,106,'Jerusalem',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(568,106,'Tel Aviv',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(569,107,'Abruzzit',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(570,107,'Apulia',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(571,107,'Calabria',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(572,107,'Campania',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(573,107,'Emilia-Romagna',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(574,107,'Friuli-Venezia Giuli',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(575,107,'Latium',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(576,107,'Liguria',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(577,107,'Lombardia',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(578,107,'Marche',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(579,107,'Piemonte',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(580,107,'Sardinia',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(581,107,'Sisilia',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(582,107,'Toscana',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(583,107,'Trentino-Alto Adige',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(584,107,'Umbria',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(585,107,'Veneto',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(586,108,'St. Andrew',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(587,108,'St. Catherine',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(588,109,'al-Zarqa',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(589,109,'Amman',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(590,109,'Irbid',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(591,110,'Aichi',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(592,110,'Akita',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(593,110,'Aomori',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(594,110,'Chiba',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(595,110,'Ehime',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(596,110,'Fukui',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(597,110,'Fukuoka',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(598,110,'Fukushima',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(599,110,'Gifu',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(600,110,'Gumma',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(601,110,'Hiroshima',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(602,110,'Hokkaido',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(603,110,'Hyogo',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(604,110,'Ibaragi',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(605,110,'Ishikawa',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(606,110,'Iwate',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(607,110,'Kagawa',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(608,110,'Kagoshima',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(609,110,'Kanagawa',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(610,110,'Kochi',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(611,110,'Kumamoto',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(612,110,'Kyoto',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(613,110,'Mie',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(614,110,'Miyagi',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(615,110,'Miyazaki',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(616,110,'Nagano',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(617,110,'Nagasaki',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(618,110,'Nara',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(619,110,'Niigata',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(620,110,'Oita',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(621,110,'Okayama',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(622,110,'Okinawa',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(623,110,'Osaka',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(624,110,'Saga',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(625,110,'Saitama',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(626,110,'Shiga',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(627,110,'Shimane',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(628,110,'Shizuoka',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(629,110,'Tochigi',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(630,110,'Tokushima',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(631,110,'Tokyo-to',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(632,110,'Tottori',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(633,110,'Toyama',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(634,110,'Wakayama',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(635,110,'Yamagata',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(636,110,'Yamaguchi',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(637,110,'Yamanashi',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(638,111,'Almaty',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(639,111,'Almaty Qalasy',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(640,111,'AqtÃƒÂ¶be',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(641,111,'Astana',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(642,111,'Atyrau',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(643,111,'East Kazakstan',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(644,111,'Mangghystau',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(645,111,'North Kazakstan',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(646,111,'Pavlodar',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(647,111,'Qaraghandy',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(648,111,'Qostanay',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(649,111,'Qyzylorda',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(650,111,'South Kazakstan',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(651,111,'Taraz',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(652,111,'West Kazakstan',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(653,112,'Central',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(654,112,'Coast',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(655,112,'Eastern',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(656,112,'Nairobi',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(657,112,'Nyanza',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(658,112,'Rift Valley',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(659,113,'Bishkek shaary',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(660,113,'Osh',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(661,114,'Battambang',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(662,114,'Phnom Penh',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(663,114,'Siem Reap',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(664,115,'South Tarawa',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(665,116,'St George Basseterre',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(666,117,'Cheju',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(667,117,'Chollabuk',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(668,117,'Chollanam',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(669,117,'Chungchongbuk',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(670,117,'Chungchongnam',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(671,117,'Inchon',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(672,117,'Kang-won',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(673,117,'Kwangju',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(674,117,'Kyonggi',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(675,117,'Kyongsangbuk',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(676,117,'Kyongsangnam',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(677,117,'Pusan',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(678,117,'Seoul',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(679,117,'Taegu',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(680,117,'Taejon',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(681,118,'al-Asima',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(682,118,'Hawalli',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(683,119,'Savannakhet',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(684,119,'Viangchan',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(685,120,'al-Shamal',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(686,120,'Beirut',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(687,121,'Montserrado',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(688,122,'al-Zawiya',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(689,122,'Bengasi',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(690,122,'Misrata',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(691,122,'Tripoli',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(692,123,'Castries',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(693,124,'Schaan',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(694,124,'Vaduz',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(695,125,'Central',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(696,125,'Northern',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(697,125,'Western',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(698,126,'Maseru',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(699,127,'Kaunas',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(700,127,'Klaipeda',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(701,127,'Panevezys',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(702,127,'Ã…Â iauliai',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(703,127,'Vilna',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(704,128,'Luxembourg',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(705,129,'Daugavpils',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(706,129,'Liepaja',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(707,129,'Riika',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(708,130,'Macau',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(709,131,'Casablanca',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(710,131,'Chaouia-Ouardigha',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(711,131,'Doukkala-Abda',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(712,131,'FÃƒÂ¨s-Boulemane',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(713,131,'Gharb-Chrarda-BÃƒÂ©ni H',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(714,131,'Marrakech-Tensift-Al',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(715,131,'MeknÃƒÂ¨s-Tafilalet',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(716,131,'Oriental',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(717,131,'Rabat-SalÃƒÂ©-Zammour-Z',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(718,131,'Souss Massa-DraÃƒÂ¢',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(719,131,'Tadla-Azilal',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(720,131,'Tanger-TÃƒÂ©touan',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(721,131,'Taza-Al Hoceima-Taou',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(722,132,'Monaco',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(723,133,'Balti',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(724,133,'Bender (TÃƒÂ®ghina)',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(725,133,'Chisinau',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(726,133,'Dnjestria',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(727,134,'Antananarivo',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(728,134,'Fianarantsoa',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(729,134,'Mahajanga',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(730,134,'Toamasina',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(731,135,'Maale',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(732,136,'Aguascalientes',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(733,136,'Baja California',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(734,136,'Baja California Sur',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(735,136,'Campeche',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(736,136,'Chiapas',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(737,136,'Chihuahua',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(738,136,'Coahuila de Zaragoza',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(739,136,'Colima',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(740,136,'Distrito Federal',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(741,136,'Durango',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(742,136,'Guanajuato',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(743,136,'Guerrero',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(744,136,'Hidalgo',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(745,136,'Jalisco',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(746,136,'MÃƒÂ©xico',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(747,136,'MichoacÃƒÂ¡n de Ocampo',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(748,136,'Morelos',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(749,136,'Nayarit',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(750,136,'Nuevo LeÃƒÂ³n',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(751,136,'Oaxaca',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(752,136,'Puebla',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(753,136,'QuerÃƒÂ©taro',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(754,136,'QuerÃƒÂ©taro de Arteaga',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(755,136,'Quintana Roo',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(756,136,'San Luis PotosÃƒÂ­',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(757,136,'Sinaloa',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(758,136,'Sonora',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(759,136,'Tabasco',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(760,136,'Tamaulipas',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(761,136,'Veracruz',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(762,136,'Veracruz-Llave',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(763,136,'YucatÃƒÂ¡n',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(764,136,'Zacatecas',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(765,137,'Majuro',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(766,138,'Skopje',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(767,139,'Bamako',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(768,140,'Inner Harbour',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(769,140,'Outer Harbour',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(770,141,'Irrawaddy [Ayeyarwad',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(771,141,'Magwe [Magway]',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(772,141,'Mandalay',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(773,141,'Mon',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(774,141,'Pegu [Bago]',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(775,141,'Rakhine',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(776,141,'Rangoon [Yangon]',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(777,141,'Sagaing',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(778,141,'Shan',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(779,141,'Tenasserim [Tanintha',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(780,142,'Ulaanbaatar',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(781,143,'Saipan',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(782,144,'Gaza',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(783,144,'Inhambane',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(784,144,'Manica',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(785,144,'Maputo',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(786,144,'Nampula',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(787,144,'Sofala',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(788,144,'Tete',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(789,144,'ZambÃƒÂ©zia',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(790,145,'Dakhlet NouÃƒÂ¢dhibou',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(791,145,'Nouakchott',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(792,146,'Plymouth',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(793,147,'Fort-de-France',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(794,148,'Plaines Wilhelms',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(795,148,'Port-Louis',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(796,149,'Blantyre',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(797,149,'Lilongwe',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(798,150,'Johor',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(799,150,'Kedah',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(800,150,'Kelantan',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(801,150,'Negeri Sembilan',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(802,150,'Pahang',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(803,150,'Perak',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(804,150,'Pulau Pinang',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(805,150,'Sabah',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(806,150,'Sarawak',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(807,150,'Selangor',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(808,150,'Terengganu',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(809,150,'Wilayah Persekutuan',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(810,151,'Mamoutzou',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(811,152,'Khomas',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(812,153,'New Caledonia',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(813,154,'Maradi',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(814,154,'Niamey',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(815,154,'Zinder',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(816,155,'Norfolk Island',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(817,156,'Anambra & Enugu & Eb',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(818,156,'Bauchi & Gombe',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(819,156,'Benue',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(820,156,'Borno & Yobe',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(821,156,'Cross River',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(822,156,'Edo & Delta',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(823,156,'Federal Capital Dist',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(824,156,'Imo & Abia',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(825,156,'Kaduna',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(826,156,'Kano & Jigawa',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(827,156,'Katsina',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(828,156,'Kwara & Kogi',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(829,156,'Lagos',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(830,156,'Niger',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(831,156,'Ogun',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(832,156,'Ondo & Ekiti',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(833,156,'Oyo & Osun',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(834,156,'Plateau & Nassarawa',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(835,156,'Rivers & Bayelsa',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(836,156,'Sokoto & Kebbi & Zam',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(837,157,'Chinandega',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(838,157,'LeÃƒÂ³n',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(839,157,'Managua',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(840,157,'Masaya',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(841,158,'Niue',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(842,159,'Drenthe',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(843,159,'Flevoland',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(844,159,'Gelderland',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(845,159,'Groningen',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(846,159,'Limburg',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(847,159,'Noord-Brabant',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(848,159,'Noord-Holland',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(849,159,'Overijssel',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(850,159,'Utrecht',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(851,159,'Zuid-Holland',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(852,160,'Akershus',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(853,160,'Hordaland',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(854,160,'Oslo',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(855,160,'Rogaland',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(856,160,'SÃƒÂ¸r-TrÃƒÂ¸ndelag',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(857,161,'Central',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(858,161,'Eastern',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(859,161,'Western',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(860,162,'Nauru',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(861,163,'Auckland',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(862,163,'Canterbury',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(863,163,'Dunedin',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(864,163,'Hamilton',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(865,163,'Wellington',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(866,164,'al-Batina',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(867,164,'Masqat',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(868,164,'Zufar',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(869,165,'Baluchistan',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(870,165,'Islamabad',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(871,165,'Nothwest Border Prov',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(872,165,'Punjab',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(873,165,'Sind',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(874,165,'Sindh',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(875,166,'PanamÃƒÂ¡',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(876,166,'San Miguelito',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(877,167,'Pitcairn',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(878,168,'Ancash',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(879,168,'Arequipa',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(880,168,'Ayacucho',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(881,168,'Cajamarca',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(882,168,'Callao',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(883,168,'Cusco',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(884,168,'Huanuco',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(885,168,'Ica',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(886,168,'JunÃƒÂ­n',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(887,168,'La Libertad',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(888,168,'Lambayeque',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(889,168,'Lima',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(890,168,'Loreto',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(891,168,'Piura',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(892,168,'Puno',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(893,168,'Tacna',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(894,168,'Ucayali',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(895,169,'ARMM',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(896,169,'Bicol',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(897,169,'Cagayan Valley',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(898,169,'CAR',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(899,169,'Caraga',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(900,169,'Central Luzon',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(901,169,'Central Mindanao',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(902,169,'Central Visayas',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(903,169,'Eastern Visayas',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(904,169,'Ilocos',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(905,169,'National Capital Reg',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(906,169,'Northern Mindanao',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(907,169,'Southern Mindanao',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(908,169,'Southern Tagalog',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(909,169,'Western Mindanao',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(910,169,'Western Visayas',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(911,170,'Koror',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(912,171,'National Capital Dis',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(913,172,'Dolnoslaskie',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(914,172,'Kujawsko-Pomorskie',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(915,172,'Lodzkie',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(916,172,'Lubelskie',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(917,172,'Lubuskie',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(918,172,'Malopolskie',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(919,172,'Mazowieckie',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(920,172,'Opolskie',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(921,172,'Podkarpackie',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(922,172,'Podlaskie',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(923,172,'Pomorskie',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(924,172,'Slaskie',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(925,172,'Swietokrzyskie',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(926,172,'Warminsko-Mazurskie',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(927,172,'Wielkopolskie',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(928,172,'Zachodnio-Pomorskie',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(929,173,'Arecibo',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(930,173,'BayamÃƒÂ³n',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(931,173,'Caguas',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(932,173,'Carolina',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(933,173,'Guaynabo',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(934,173,'MayagÃƒÂ¼ez',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(935,173,'Ponce',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(936,173,'San Juan',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(937,173,'Toa Baja',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(938,174,'Chagang',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(939,174,'Hamgyong N',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(940,174,'Hamgyong P',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(941,174,'Hwanghae N',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(942,174,'Hwanghae P',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(943,174,'Kaesong-si',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(944,174,'Kangwon',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(945,174,'Nampo-si',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(946,174,'Pyongan N',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(947,174,'Pyongan P',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(948,174,'Pyongyang-si',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(949,174,'Yanggang',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(950,175,'Braga',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(951,175,'CoÃƒÂ­mbra',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(952,175,'Lisboa',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(953,175,'Porto',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(954,176,'Alto ParanÃƒÂ¡',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(955,176,'AsunciÃƒÂ³n',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(956,176,'Central',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(957,177,'Gaza',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(958,177,'Hebron',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(959,177,'Khan Yunis',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(960,177,'Nablus',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(961,177,'North Gaza',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(962,177,'Rafah',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(963,178,'Tahiti',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(964,179,'Doha',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(965,180,'Saint-Denis',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(966,181,'Arad',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(967,181,'Arges',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(968,181,'Bacau',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(969,181,'Bihor',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(970,181,'Botosani',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(971,181,'Braila',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(972,181,'Brasov',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(973,181,'Bukarest',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(974,181,'Buzau',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(975,181,'Caras-Severin',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(976,181,'Cluj',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(977,181,'Constanta',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(978,181,'DÃƒÂ¢mbovita',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(979,181,'Dolj',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(980,181,'Galati',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(981,181,'Gorj',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(982,181,'Iasi',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(983,181,'Maramures',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(984,181,'Mehedinti',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(985,181,'Mures',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(986,181,'Neamt',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(987,181,'Prahova',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(988,181,'Satu Mare',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(989,181,'Sibiu',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(990,181,'Suceava',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(991,181,'Timis',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(992,181,'Tulcea',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(993,181,'VÃƒÂ¢lcea',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(994,181,'Vrancea',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(995,182,'Adygea',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(996,182,'Altai',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(997,182,'Amur',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(998,182,'Arkangeli',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(999,182,'Astrahan',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1000,182,'BaÃ…Â¡kortostan',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1001,182,'Belgorod',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1002,182,'Brjansk',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1003,182,'Burjatia',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1004,182,'Dagestan',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1005,182,'Habarovsk',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1006,182,'Hakassia',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1007,182,'Hanti-Mansia',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1008,182,'Irkutsk',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1009,182,'Ivanovo',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1010,182,'Jaroslavl',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1011,182,'Kabardi-Balkaria',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1012,182,'Kaliningrad',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1013,182,'Kalmykia',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1014,182,'Kaluga',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1015,182,'KamtÃ…Â¡atka',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1016,182,'KaratÃ…Â¡ai-TÃ…Â¡erkessia',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1017,182,'Karjala',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1018,182,'Kemerovo',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1019,182,'Kirov',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1020,182,'Komi',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1021,182,'Kostroma',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1022,182,'Krasnodar',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1023,182,'Krasnojarsk',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1024,182,'Kurgan',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1025,182,'Kursk',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1026,182,'Lipetsk',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1027,182,'Magadan',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1028,182,'Marinmaa',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1029,182,'Mordva',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1030,182,'Moscow (City)',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1031,182,'Moskova',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1032,182,'Murmansk',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1033,182,'Nizni Novgorod',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1034,182,'North Ossetia-Alania',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1035,182,'Novgorod',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1036,182,'Novosibirsk',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1037,182,'Omsk',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1038,182,'Orenburg',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1039,182,'Orjol',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1040,182,'Penza',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1041,182,'Perm',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1042,182,'Pietari',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1043,182,'Pihkova',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1044,182,'Primorje',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1045,182,'Rjazan',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1046,182,'Rostov-na-Donu',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1047,182,'Saha (Jakutia)',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1048,182,'Sahalin',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1049,182,'Samara',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1050,182,'Saratov',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1051,182,'Smolensk',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1052,182,'Stavropol',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1053,182,'Sverdlovsk',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1054,182,'Tambov',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1055,182,'Tatarstan',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1056,182,'Tjumen',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1057,182,'Tomsk',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1058,182,'TÃ…Â¡eljabinsk',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1059,182,'TÃ…Â¡etÃ…Â¡enia',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1060,182,'TÃ…Â¡ita',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1061,182,'TÃ…Â¡uvassia',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1062,182,'Tula',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1063,182,'Tver',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1064,182,'Tyva',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1065,182,'Udmurtia',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1066,182,'Uljanovsk',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1067,182,'Vladimir',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1068,182,'Volgograd',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1069,182,'Vologda',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1070,182,'Voronez',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1071,182,'Yamalin Nenetsia',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1072,183,'Kigali',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1073,184,'al-Khudud al-Samaliy',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1074,184,'al-Qasim',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1075,184,'al-Sharqiya',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1076,184,'Asir',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1077,184,'Hail',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1078,184,'Medina',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1079,184,'Mekka',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1080,184,'Najran',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1081,184,'Qasim',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1082,184,'Riad',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1083,184,'Riyadh',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1084,184,'Tabuk',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1085,185,'al-Bahr al-Abyad',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1086,185,'al-Bahr al-Ahmar',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1087,185,'al-Jazira',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1088,185,'al-Qadarif',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1089,185,'Bahr al-Jabal',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1090,185,'Darfur al-Janubiya',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1091,185,'Darfur al-Shamaliya',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1092,185,'Kassala',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1093,185,'Khartum',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1094,185,'Kurdufan al-Shamaliy',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1095,186,'Cap-Vert',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1096,186,'Diourbel',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1097,186,'Kaolack',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1098,186,'Saint-Louis',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1099,186,'ThiÃƒÂ¨s',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1100,186,'Ziguinchor',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1101,187,'Singapore',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1102,189,'Saint Helena',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1103,190,'LÃƒÂ¤nsimaa',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1104,191,'Honiara',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1105,192,'Western',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1106,193,'La Libertad',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1107,193,'San Miguel',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1108,193,'San Salvador',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1109,193,'Santa Ana',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1110,194,'San Marino',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1111,194,'Serravalle/Dogano',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1112,195,'Banaadir',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1113,195,'Jubbada Hoose',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1114,195,'Woqooyi Galbeed',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1115,196,'Saint-Pierre',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1116,197,'Aqua Grande',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1117,198,'Paramaribo',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1118,199,'Bratislava',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1119,199,'VÃƒÂ½chodnÃƒÂ© Slovensko',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1120,200,'Osrednjeslovenska',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1121,200,'Podravska',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1122,201,'East GÃƒÂ¶tanmaan lÃƒÂ¤n',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1123,201,'GÃƒÂ¤vleborgs lÃƒÂ¤n',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1124,201,'JÃƒÂ¶nkÃƒÂ¶pings lÃƒÂ¤n',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1125,201,'Lisboa',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1126,201,'Ãƒâ€“rebros lÃƒÂ¤n',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1127,201,'SkÃƒÂ¥ne lÃƒÂ¤n',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1128,201,'Uppsala lÃƒÂ¤n',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1129,201,'VÃƒÂ¤sterbottens lÃƒÂ¤n',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1130,201,'VÃƒÂ¤sternorrlands lÃƒÂ¤n',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1131,201,'VÃƒÂ¤stmanlands lÃƒÂ¤n',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1132,201,'West GÃƒÂ¶tanmaan lÃƒÂ¤n',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1133,202,'Hhohho',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1134,203,'MahÃƒÂ©',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1135,204,'al-Hasaka',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1136,204,'al-Raqqa',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1137,204,'Aleppo',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1138,204,'Damascus',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1139,204,'Damaskos',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1140,204,'Dayr al-Zawr',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1141,204,'Hama',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1142,204,'Hims',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1143,204,'Idlib',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1144,204,'Latakia',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1145,205,'Grand Turk',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1146,206,'Chari-Baguirmi',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1147,206,'Logone Occidental',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1148,207,'Maritime',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1149,208,'Bangkok',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1150,208,'Chiang Mai',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1151,208,'Khon Kaen',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1152,208,'Nakhon Pathom',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1153,208,'Nakhon Ratchasima',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1154,208,'Nakhon Sawan',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1155,208,'Nonthaburi',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1156,208,'Songkhla',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1157,208,'Ubon Ratchathani',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1158,208,'Udon Thani',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1159,209,'Karotegin',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1160,209,'Khujand',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1161,210,'Fakaofo',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1162,211,'Ahal',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1163,211,'Dashhowuz',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1164,211,'Lebap',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1165,211,'Mary',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1166,212,'Dili',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1167,213,'Tongatapu',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1168,214,'Caroni',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1169,214,'Port-of-Spain',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1170,215,'Ariana',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1171,215,'Biserta',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1172,215,'GabÃƒÂ¨s',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1173,215,'Kairouan',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1174,215,'Sfax',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1175,215,'Sousse',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1176,215,'Tunis',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1177,216,'Adana',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1178,216,'Adiyaman',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1179,216,'Afyon',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1180,216,'Aksaray',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1181,216,'Ankara',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1182,216,'Antalya',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1183,216,'Aydin',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1184,216,'Balikesir',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1185,216,'Batman',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1186,216,'Bursa',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1187,216,'Ãƒâ€¡orum',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1188,216,'Denizli',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1189,216,'Diyarbakir',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1190,216,'Edirne',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1191,216,'ElÃƒÂ¢zig',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1192,216,'Erzincan',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1193,216,'Erzurum',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1194,216,'Eskisehir',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1195,216,'Gaziantep',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1196,216,'Hatay',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1197,216,'IÃƒÂ§el',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1198,216,'Isparta',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1199,216,'Istanbul',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1200,216,'Izmir',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1201,216,'Kahramanmaras',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1202,216,'KarabÃƒÂ¼k',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1203,216,'Karaman',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1204,216,'Kars',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1205,216,'Kayseri',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1206,216,'Kilis',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1207,216,'Kirikkale',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1208,216,'Kocaeli',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1209,216,'Konya',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1210,216,'KÃƒÂ¼tahya',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1211,216,'Malatya',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1212,216,'Manisa',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1213,216,'Mardin',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1214,216,'Ordu',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1215,216,'Osmaniye',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1216,216,'Sakarya',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1217,216,'Samsun',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1218,216,'Sanliurfa',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1219,216,'Siirt',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1220,216,'Sivas',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1221,216,'Tekirdag',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1222,216,'Tokat',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1223,216,'Trabzon',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1224,216,'Usak',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1225,216,'Van',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1226,216,'Zonguldak',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1227,217,'Funafuti',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1228,218,'Changhwa',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1229,218,'Chiayi',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1230,218,'Hsinchu',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1231,218,'Hualien',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1232,218,'Ilan',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1233,218,'Kaohsiung',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1234,218,'Keelung',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1235,218,'Miaoli',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1236,218,'Nantou',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1237,218,'Pingtung',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1238,218,'Taichung',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1239,218,'Tainan',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1240,218,'Taipei',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1241,218,'Taitung',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1242,218,'Taiwan',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1243,218,'Taoyuan',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1244,218,'YÃƒÂ¼nlin',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1245,219,'Arusha',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1246,219,'Dar es Salaam',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1247,219,'Dodoma',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1248,219,'Kilimanjaro',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1249,219,'Mbeya',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1250,219,'Morogoro',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1251,219,'Mwanza',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1252,219,'Tabora',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1253,219,'Tanga',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1254,219,'Zanzibar West',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1255,220,'Central',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1256,221,'Dnipropetrovsk',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1257,221,'Donetsk',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1258,221,'Harkova',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1259,221,'Herson',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1260,221,'Hmelnytskyi',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1261,221,'Ivano-Frankivsk',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1262,221,'Kiova',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1263,221,'Kirovograd',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1264,221,'Krim',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1265,221,'Lugansk',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1266,221,'Lviv',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1267,221,'Mykolajiv',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1268,221,'Odesa',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1269,221,'Pultava',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1270,221,'Rivne',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1271,221,'Sumy',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1272,221,'Taka-Karpatia',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1273,221,'Ternopil',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1274,221,'TÃ…Â¡erkasy',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1275,221,'TÃ…Â¡ernigiv',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1276,221,'TÃ…Â¡ernivtsi',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1277,221,'Vinnytsja',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1278,221,'Volynia',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1279,221,'Zaporizzja',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1280,221,'Zytomyr',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1281,223,'Montevideo',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1282,224,'Alabama','AL',145,86,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1283,224,'Alaska','AK',47,122,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1284,224,'Arizona','AZ',44,78,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1285,224,'Arkansas','AR',120,76,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1286,224,'California','CA',19,67,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1287,224,'Colorado','CO',71,57,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1288,224,'Connecticut','CT',187,36,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1289,224,'District of Columbia','DC',185,54,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1290,224,'Florida','FL',169,111,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1291,224,'Georgia','GA',156,86,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1292,224,'Hawaii','HI',97,147,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1293,224,'Idaho','ID',35,24,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1294,224,'Illinois','IL',128,47,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1295,224,'Indiana','IN',141,50,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1296,224,'Iowa','IA',112,42,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1297,224,'Kansas','KS',98,67,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1298,224,'Kentucky','KY',148,59,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1299,224,'Louisiana','LA',120,97,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1300,224,'Maryland','MD',179,51,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1301,224,'Massachusetts','MA',193,31,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1302,224,'Michigan','MI',146,37,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1303,224,'Minnesota','MN',108,23,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1304,224,'Mississippi','MS',133,84,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1305,224,'Missouri','MO',120,58,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1306,224,'Montana','MT',59,19,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1307,224,'Nebraska','NE',93,49,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1308,224,'Nevada','NV',34,45,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1309,224,'New Hampshire','NH',192,25,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1310,224,'New Jersey','NJ',185,46,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1311,224,'New Mexico','NM',72,79,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1312,224,'New York','NY',180,32,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1313,224,'North Carolina','NC',174,68,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1314,224,'Ohio','OH',154,50,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1315,224,'Oklahoma','OK',102,85,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1316,224,'Oregon','OR',17,28,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1317,224,'Pennsylvania','PA',172,45,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1318,224,'Rhode Island','RI',196,38,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1319,224,'South Carolina','SC',167,79,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1320,224,'South Dakota','SD',90,32,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1321,224,'Tennessee','TN',137,68,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1322,224,'Texas','TX',92,99,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1323,224,'Utah','UT',49,48,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1324,224,'Virginia','VA',174,56,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1325,224,'Washington','WA',21,14,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1326,224,'Wisconsin','WI',122,33,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1327,225,'Andijon',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1328,225,'Buhoro',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1329,225,'Cizah',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1330,225,'Fargona',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1331,225,'Karakalpakistan',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1332,225,'Khorazm',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1333,225,'Namangan',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1334,225,'Navoi',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1335,225,'Qashqadaryo',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1336,225,'Samarkand',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1337,225,'Surkhondaryo',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1338,225,'Toskent',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1339,225,'Toskent Shahri',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1340,226,'Holy See (Vatican Ci',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1341,227,'St George',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1342,228,'AnzoÃƒÂ¡tegui',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1343,228,'Apure',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1344,228,'Aragua',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1345,228,'Barinas',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1346,228,'BolÃƒÂ­var',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1347,228,'Carabobo',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1348,228,'Distrito Federal',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1349,228,'FalcÃƒÂ³n',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1350,228,'GuÃƒÂ¡rico',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1351,228,'Lara',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1352,228,'MÃƒÂ©rida',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1353,228,'Miranda',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1354,228,'Monagas',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1355,228,'Portuguesa',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1356,228,'Sucre',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1357,228,'TÃƒÂ¡chira',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1358,228,'Trujillo',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1359,228,'Venezuela',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1360,228,'Yaracuy',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1361,228,'Zulia',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1362,229,'Tortola',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1363,230,'St Thomas',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1364,231,'An Giang',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1365,231,'Ba Ria-Vung Tau',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1366,231,'Bac Thai',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1367,231,'Binh Dinh',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1368,231,'Binh Thuan',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1369,231,'Can Tho',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1370,231,'Dac Lac',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1371,231,'Dong Nai',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1372,231,'Haiphong',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1373,231,'Hanoi',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1374,231,'Ho Chi Minh City',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1375,231,'Khanh Hoa',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1376,231,'Kien Giang',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1377,231,'Lam Dong',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1378,231,'Nam Ha',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1379,231,'Nghe An',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1380,231,'Quang Binh',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1381,231,'Quang Nam-Da Nang',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1382,231,'Quang Ninh',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1383,231,'Thua Thien-Hue',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1384,231,'Tien Giang',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1385,232,'Shefa',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1386,233,'Wallis',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1387,234,'Upolu',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1388,235,'Aden',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1389,235,'Hadramawt',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1390,235,'Hodeida',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1391,235,'Ibb',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1392,235,'Sanaa',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1393,235,'Taizz',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1394,236,'Central Serbia',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1395,236,'Kosovo and Metohija',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1396,236,'Montenegro',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1397,236,'Vojvodina',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1398,237,'Eastern Cape',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1399,237,'Free State',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1400,237,'Gauteng',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1401,237,'KwaZulu-Natal',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1402,237,'Mpumalanga',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1403,237,'North West',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1404,237,'Northern Cape',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1405,237,'Western Cape',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1406,238,'Central',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1407,238,'Copperbelt',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1408,238,'Lusaka',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1409,239,'Bulawayo',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1410,239,'Harare',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1411,239,'Manicaland',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20'),(1412,239,'Midlands',NULL,NULL,NULL,1,'2012-08-23 20:27:20','2012-08-23 20:27:20');

/*Table structure for table `tbl_timezones` */

DROP TABLE IF EXISTS `tbl_timezones`;

CREATE TABLE `tbl_timezones` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `timezone` varchar(200) CHARACTER SET latin1 NOT NULL,
  `timezone_abbr` varchar(10) DEFAULT NULL,
  `offset_value` varchar(100) NOT NULL,
  `isactive` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=416 DEFAULT CHARSET=utf8;

/*Data for the table `tbl_timezones` */

insert  into `tbl_timezones`(`id`,`timezone`,`timezone_abbr`,`offset_value`,`isactive`) values (1,'Africa/Abidjan','GMT',' 00:00',1),(2,'Africa/Accra','GMT',' 00:00',1),(3,'Africa/Addis_Ababa','EAT','+03:00',1),(4,'Africa/Algiers','CET','+01:00',1),(5,'Africa/Asmara','EAT','+03:00',1),(6,'Africa/Bamako','GMT',' 00:00',1),(7,'Africa/Bangui','WAT','+01:00',1),(8,'Africa/Banjul','GMT',' 00:00',1),(9,'Africa/Bissau','GMT',' 00:00',1),(10,'Africa/Blantyre','CAT','+02:00',1),(11,'Africa/Brazzaville','WAT','+01:00',1),(12,'Africa/Bujumbura','CAT','+02:00',1),(13,'Africa/Cairo','EET','+02:00',1),(14,'Africa/Casablanca','WET',' 00:00',1),(15,'Africa/Ceuta','CET','+01:00',1),(16,'Africa/Conakry','GMT',' 00:00',1),(17,'Africa/Dakar','GMT',' 00:00',1),(18,'Africa/Dar_es_Salaam','EAT','+03:00',1),(19,'Africa/Djibouti','EAT','+03:00',1),(20,'Africa/Douala','WAT','+01:00',1),(21,'Africa/El_Aaiun','WET',' 00:00',1),(22,'Africa/Freetown','GMT',' 00:00',1),(23,'Africa/Gaborone','CAT','+02:00',1),(24,'Africa/Harare','CAT','+02:00',1),(25,'Africa/Johannesburg','SAST','+02:00',1),(26,'Africa/Juba','EAT','+03:00',1),(27,'Africa/Kampala','EAT','+03:00',1),(28,'Africa/Khartoum','EAT','+03:00',1),(29,'Africa/Kigali','CAT','+02:00',1),(30,'Africa/Kinshasa','WAT','+01:00',1),(31,'Africa/Lagos','WAT','+01:00',1),(32,'Africa/Libreville','WAT','+01:00',1),(33,'Africa/Lome','GMT',' 00:00',1),(34,'Africa/Luanda','WAT','+01:00',1),(35,'Africa/Lubumbashi','CAT','+02:00',1),(36,'Africa/Lusaka','CAT','+02:00',1),(37,'Africa/Malabo','WAT','+01:00',1),(38,'Africa/Maputo','CAT','+02:00',1),(39,'Africa/Maseru','SAST','+02:00',1),(40,'Africa/Mbabane','SAST','+02:00',1),(41,'Africa/Mogadishu','EAT','+03:00',1),(42,'Africa/Monrovia','GMT',' 00:00',1),(43,'Africa/Nairobi','EAT','+03:00',1),(44,'Africa/Ndjamena','WAT','+01:00',1),(45,'Africa/Niamey','WAT','+01:00',1),(46,'Africa/Nouakchott','GMT',' 00:00',1),(47,'Africa/Ouagadougou','GMT',' 00:00',1),(48,'Africa/Porto-Novo','WAT','+01:00',1),(49,'Africa/Sao_Tome','GMT',' 00:00',1),(50,'Africa/Tripoli','EET','+02:00',1),(51,'Africa/Tunis','CET','+01:00',1),(52,'Africa/Windhoek','WAST','+02:00',1),(53,'America/Adak','HADT','-09:00',1),(54,'America/Anchorage','AKDT','-08:00',1),(55,'America/Anguilla','AST','-04:00',1),(56,'America/Antigua','AST','-04:00',1),(57,'America/Araguaina','BRT','-03:00',1),(58,'America/Argentina/Buenos_Aires','ART','-03:00',1),(59,'America/Argentina/Catamarca','ART','-03:00',1),(60,'America/Argentina/Cordoba','ART','-03:00',1),(61,'America/Argentina/Jujuy','ART','-03:00',1),(62,'America/Argentina/La_Rioja','ART','-03:00',1),(63,'America/Argentina/Mendoza','ART','-03:00',1),(64,'America/Argentina/Rio_Gallegos','ART','-03:00',1),(65,'America/Argentina/Salta','ART','-03:00',1),(66,'America/Argentina/San_Juan','ART','-03:00',1),(67,'America/Argentina/San_Luis','WARST','-03:00',1),(68,'America/Argentina/Tucuman','ART','-03:00',1),(69,'America/Argentina/Ushuaia','ART','-03:00',1),(70,'America/Aruba','AST','-04:00',1),(71,'America/Asuncion','PYST','-03:00',1),(72,'America/Atikokan','EST','-05:00',1),(73,'America/Bahia','BRT','-03:00',1),(74,'America/Bahia_Banderas','CST','-06:00',1),(75,'America/Barbados','AST','-04:00',1),(76,'America/Belem','BRT','-03:00',1),(77,'America/Belize','CST','-06:00',1),(78,'America/Blanc-Sablon','AST','-04:00',1),(79,'America/Boa_Vista','AMT','-04:00',1),(80,'America/Bogota','COT','-05:00',1),(81,'America/Boise','MDT','-06:00',1),(82,'America/Cambridge_Bay','MDT','-06:00',1),(83,'America/Campo_Grande','AMT','-04:00',1),(84,'America/Cancun','CST','-06:00',1),(85,'America/Caracas','VET','-04:30',1),(86,'America/Cayenne','GFT','-03:00',1),(87,'America/Cayman','EST','-05:00',1),(88,'America/Chicago','CDT','-05:00',1),(89,'America/Chihuahua','MST','-07:00',1),(90,'America/Costa_Rica','CST','-06:00',1),(91,'America/Cuiaba','AMT','-04:00',1),(92,'America/Curacao','AST','-04:00',1),(93,'America/Danmarkshavn','GMT',' 00:00',1),(94,'America/Dawson','PDT','-07:00',1),(95,'America/Dawson_Creek','MST','-07:00',1),(96,'America/Denver','MDT','-06:00',1),(97,'America/Detroit','EDT','-04:00',1),(98,'America/Dominica','AST','-04:00',1),(99,'America/Edmonton','MDT','-06:00',1),(100,'America/Eirunepe','AMT','-04:00',1),(101,'America/El_Salvador','CST','-06:00',1),(102,'America/Fortaleza','BRT','-03:00',1),(103,'America/Glace_Bay','ADT','-03:00',1),(104,'America/Godthab','WGT','-03:00',1),(105,'America/Goose_Bay','ADT','-03:00',1),(106,'America/Grand_Turk','EDT','-04:00',1),(107,'America/Grenada','AST','-04:00',1),(108,'America/Guadeloupe','AST','-04:00',1),(109,'America/Guatemala','CST','-06:00',1),(110,'America/Guayaquil','ECT','-05:00',1),(111,'America/Guyana','GYT','-04:00',1),(112,'America/Halifax','ADT','-03:00',1),(113,'America/Havana','CDT','-04:00',1),(114,'America/Hermosillo','MST','-07:00',1),(115,'America/Indiana/Indianapolis','EDT','-04:00',1),(116,'America/Indiana/Knox','CDT','-05:00',1),(117,'America/Indiana/Marengo','EDT','-04:00',1),(118,'America/Indiana/Petersburg','EDT','-04:00',1),(119,'America/Indiana/Tell_City','CDT','-05:00',1),(120,'America/Indiana/Vevay','EDT','-04:00',1),(121,'America/Indiana/Vincennes','EDT','-04:00',1),(122,'America/Indiana/Winamac','EDT','-04:00',1),(123,'America/Inuvik','MDT','-06:00',1),(124,'America/Iqaluit','EDT','-04:00',1),(125,'America/Jamaica','EST','-05:00',1),(126,'America/Juneau','AKDT','-08:00',1),(127,'America/Kentucky/Louisville','EDT','-04:00',1),(128,'America/Kentucky/Monticello','EDT','-04:00',1),(129,'America/Kralendijk','AST','-04:00',1),(130,'America/La_Paz','BOT','-04:00',1),(131,'America/Lima','PET','-05:00',1),(132,'America/Los_Angeles','PDT','-07:00',1),(133,'America/Lower_Princes','AST','-04:00',1),(134,'America/Maceio','BRT','-03:00',1),(135,'America/Managua','CST','-06:00',1),(136,'America/Manaus','AMT','-04:00',1),(137,'America/Marigot','AST','-04:00',1),(138,'America/Martinique','AST','-04:00',1),(139,'America/Matamoros','CDT','-05:00',1),(140,'America/Mazatlan','MST','-07:00',1),(141,'America/Menominee','CDT','-05:00',1),(142,'America/Merida','CST','-06:00',1),(143,'America/Metlakatla','MeST','-08:00',1),(144,'America/Mexico_City','CST','-06:00',1),(145,'America/Miquelon','PMDT','-02:00',1),(146,'America/Moncton','ADT','-03:00',1),(147,'America/Monterrey','CST','-06:00',1),(148,'America/Montevideo','UYT','-03:00',1),(149,'America/Montreal','EDT','-04:00',1),(150,'America/Montserrat','AST','-04:00',1),(151,'America/Nassau','EDT','-04:00',1),(152,'America/New_York','EDT','-04:00',1),(153,'America/Nipigon','EDT','-04:00',1),(154,'America/Nome','AKDT','-08:00',1),(155,'America/Noronha','FNT','-02:00',1),(156,'America/North_Dakota/Beulah','CDT','-05:00',1),(157,'America/North_Dakota/Center','CDT','-05:00',1),(158,'America/North_Dakota/New_Salem','CDT','-05:00',1),(159,'America/Ojinaga','MDT','-06:00',1),(160,'America/Panama','EST','-05:00',1),(161,'America/Pangnirtung','EDT','-04:00',1),(162,'America/Paramaribo','SRT','-03:00',1),(163,'America/Phoenix','MST','-07:00',1),(164,'America/Port-au-Prince','EST','-05:00',1),(165,'America/Port_of_Spain','AST','-04:00',1),(166,'America/Porto_Velho','AMT','-04:00',1),(167,'America/Puerto_Rico','AST','-04:00',1),(168,'America/Rainy_River','CDT','-05:00',1),(169,'America/Rankin_Inlet','CDT','-05:00',1),(170,'America/Recife','BRT','-03:00',1),(171,'America/Regina','CST','-06:00',1),(172,'America/Resolute','CDT','-05:00',1),(173,'America/Rio_Branco','AMT','-04:00',1),(174,'America/Santa_Isabel','PST','-08:00',1),(175,'America/Santarem','BRT','-03:00',1),(176,'America/Santiago','CLT','-04:00',1),(177,'America/Santo_Domingo','AST','-04:00',1),(178,'America/Sao_Paulo','BRT','-03:00',1),(179,'America/Scoresbysund','EGT','-01:00',1),(180,'America/Shiprock','MDT','-06:00',1),(181,'America/Sitka','AKDT','-08:00',1),(182,'America/St_Barthelemy','AST','-04:00',1),(183,'America/St_Johns','NDT','-02:30',1),(184,'America/St_Kitts','AST','-04:00',1),(185,'America/St_Lucia','AST','-04:00',1),(186,'America/St_Thomas','AST','-04:00',1),(187,'America/St_Vincent','AST','-04:00',1),(188,'America/Swift_Current','CST','-06:00',1),(189,'America/Tegucigalpa','CST','-06:00',1),(190,'America/Thule','ADT','-03:00',1),(191,'America/Thunder_Bay','EDT','-04:00',1),(192,'America/Tijuana','PDT','-07:00',1),(193,'America/Toronto','EDT','-04:00',1),(194,'America/Tortola','AST','-04:00',1),(195,'America/Vancouver','PDT','-07:00',1),(196,'America/Whitehorse','PDT','-07:00',1),(197,'America/Winnipeg','CDT','-05:00',1),(198,'America/Yakutat','AKDT','-08:00',1),(199,'America/Yellowknife','MDT','-06:00',1),(200,'Antarctica/Casey','WST','+08:00',1),(201,'Antarctica/Davis','DAVT','+07:00',1),(202,'Antarctica/DumontDUrville','DDUT','+10:00',1),(203,'Antarctica/Macquarie','MIST','+11:00',1),(204,'Antarctica/Mawson','MAWT','+05:00',1),(205,'Antarctica/McMurdo','NZDT','+13:00',1),(206,'Antarctica/Palmer','CLT','-04:00',1),(207,'Antarctica/Rothera','ROTT','-03:00',1),(208,'Antarctica/South_Pole','NZDT','+13:00',1),(209,'Antarctica/Syowa','SYOT','+03:00',1),(210,'Antarctica/Vostok','VOST','+06:00',1),(211,'Arctic/Longyearbyen','CET','+01:00',1),(212,'Asia/Aden','AST','+03:00',1),(213,'Asia/Almaty','ALMT','+06:00',1),(214,'Asia/Amman','EET','+02:00',1),(215,'Asia/Anadyr','ANAT','+12:00',1),(216,'Asia/Aqtau','AQTT','+05:00',1),(217,'Asia/Aqtobe','AQTT','+05:00',1),(218,'Asia/Ashgabat','TMT','+05:00',1),(219,'Asia/Baghdad','AST','+03:00',1),(220,'Asia/Bahrain','AST','+03:00',1),(221,'Asia/Baku','AZT','+04:00',1),(222,'Asia/Bangkok','ICT','+07:00',1),(223,'Asia/Beirut','EET','+02:00',1),(224,'Asia/Bishkek','KGT','+06:00',1),(225,'Asia/Brunei','BNT','+08:00',1),(226,'Asia/Choibalsan','CHOT','+08:00',1),(227,'Asia/Chongqing','CST','+08:00',1),(228,'Asia/Colombo','IST','+05:30',1),(229,'Asia/Damascus','EET','+02:00',1),(230,'Asia/Dhaka','BDT','+06:00',1),(231,'Asia/Dili','TLT','+09:00',1),(232,'Asia/Dubai','GST','+04:00',1),(233,'Asia/Dushanbe','TJT','+05:00',1),(234,'Asia/Gaza','EET','+02:00',1),(235,'Asia/Harbin','CST','+08:00',1),(236,'Asia/Hebron','EET','+02:00',1),(237,'Asia/Ho_Chi_Minh','ICT','+07:00',1),(238,'Asia/Hong_Kong','HKT','+08:00',1),(239,'Asia/Hovd','HOVT','+07:00',1),(240,'Asia/Irkutsk','IRKT','+09:00',1),(241,'Asia/Jakarta','WIT','+07:00',1),(242,'Asia/Jayapura','EIT','+09:00',1),(243,'Asia/Jerusalem','IST','+02:00',1),(244,'Asia/Kabul','AFT','+04:30',1),(245,'Asia/Kamchatka','PETT','+12:00',1),(246,'Asia/Karachi','PKT','+05:00',1),(247,'Asia/Kashgar','CST','+08:00',1),(248,'Asia/Kathmandu','NPT','+05:45',1),(249,'Asia/Kolkata','IST','+05:30',1),(250,'Asia/Krasnoyarsk','KRAT','+08:00',1),(251,'Asia/Kuala_Lumpur','MYT','+08:00',1),(252,'Asia/Kuching','MYT','+08:00',1),(253,'Asia/Kuwait','AST','+03:00',1),(254,'Asia/Macau','CST','+08:00',1),(255,'Asia/Magadan','MAGT','+12:00',1),(256,'Asia/Makassar','CIT','+08:00',1),(257,'Asia/Manila','PHT','+08:00',1),(258,'Asia/Muscat','GST','+04:00',1),(259,'Asia/Nicosia','EET','+02:00',1),(260,'Asia/Novokuznetsk','NOVT','+07:00',1),(261,'Asia/Novosibirsk','NOVT','+07:00',1),(262,'Asia/Omsk','OMST','+07:00',1),(263,'Asia/Oral','ORAT','+05:00',1),(264,'Asia/Phnom_Penh','ICT','+07:00',1),(265,'Asia/Pontianak','WIT','+07:00',1),(266,'Asia/Pyongyang','KST','+09:00',1),(267,'Asia/Qatar','AST','+03:00',1),(268,'Asia/Qyzylorda','QYZT','+06:00',1),(269,'Asia/Rangoon','MMT','+06:30',1),(270,'Asia/Riyadh','AST','+03:00',1),(271,'Asia/Sakhalin','SAKT','+11:00',1),(272,'Asia/Samarkand','UZT','+05:00',1),(273,'Asia/Seoul','KST','+09:00',1),(274,'Asia/Shanghai','CST','+08:00',1),(275,'Asia/Singapore','SGT','+08:00',1),(276,'Asia/Taipei','CST','+08:00',1),(277,'Asia/Tashkent','UZT','+05:00',1),(278,'Asia/Tbilisi','GET','+04:00',1),(279,'Asia/Tehran','IRST','+03:30',1),(280,'Asia/Thimphu','BTT','+06:00',1),(281,'Asia/Tokyo','JST','+09:00',1),(282,'Asia/Ulaanbaatar','ULAT','+08:00',1),(283,'Asia/Urumqi','CST','+08:00',1),(284,'Asia/Vientiane','ICT','+07:00',1),(285,'Asia/Vladivostok','VLAT','+11:00',1),(286,'Asia/Yakutsk','YAKT','+10:00',1),(287,'Asia/Yekaterinburg','YEKT','+06:00',1),(288,'Asia/Yerevan','AMT','+04:00',1),(289,'Atlantic/Azores','AZOT','-01:00',1),(290,'Atlantic/Bermuda','ADT','-03:00',1),(291,'Atlantic/Canary','WET',' 00:00',1),(292,'Atlantic/Cape_Verde','CVT','-01:00',1),(293,'Atlantic/Faroe','WET',' 00:00',1),(294,'Atlantic/Madeira','WET',' 00:00',1),(295,'Atlantic/Reykjavik','GMT',' 00:00',1),(296,'Atlantic/South_Georgia','GST','-02:00',1),(297,'Atlantic/St_Helena','GMT',' 00:00',1),(298,'Atlantic/Stanley','FKST','-03:00',1),(299,'Australia/Adelaide','CST','+10:30',1),(300,'Australia/Brisbane','EST','+10:00',1),(301,'Australia/Broken_Hill','CST','+10:30',1),(302,'Australia/Currie','EST','+11:00',1),(303,'Australia/Darwin','CST','+09:30',1),(304,'Australia/Eucla','CWST','+08:45',1),(305,'Australia/Hobart','EST','+11:00',1),(306,'Australia/Lindeman','EST','+10:00',1),(307,'Australia/Lord_Howe','LHST','+11:00',1),(308,'Australia/Melbourne','EST','+11:00',1),(309,'Australia/Perth','WST','+08:00',1),(310,'Australia/Sydney','EST','+11:00',1),(311,'Europe/Amsterdam','CET','+01:00',1),(312,'Europe/Andorra','CET','+01:00',1),(313,'Europe/Athens','EET','+02:00',1),(314,'Europe/Belgrade','CET','+01:00',1),(315,'Europe/Berlin','CET','+01:00',1),(316,'Europe/Bratislava','CET','+01:00',1),(317,'Europe/Brussels','CET','+01:00',1),(318,'Europe/Bucharest','EET','+02:00',1),(319,'Europe/Budapest','CET','+01:00',1),(320,'Europe/Chisinau','EET','+02:00',1),(321,'Europe/Copenhagen','CET','+01:00',1),(322,'Europe/Dublin','GMT',' 00:00',1),(323,'Europe/Gibraltar','CET','+01:00',1),(324,'Europe/Guernsey','GMT',' 00:00',1),(325,'Europe/Helsinki','EET','+02:00',1),(326,'Europe/Isle_of_Man','GMT',' 00:00',1),(327,'Europe/Istanbul','EET','+02:00',1),(328,'Europe/Jersey','GMT',' 00:00',1),(329,'Europe/Kaliningrad','FET','+03:00',1),(330,'Europe/Kiev','FET','+03:00',1),(331,'Europe/Lisbon','WET',' 00:00',1),(332,'Europe/Ljubljana','CET','+01:00',1),(333,'Europe/London','GMT',' 00:00',1),(334,'Europe/Luxembourg','CET','+01:00',1),(335,'Europe/Madrid','CET','+01:00',1),(336,'Europe/Malta','CET','+01:00',1),(337,'Europe/Mariehamn','EET','+02:00',1),(338,'Europe/Minsk','FET','+03:00',1),(339,'Europe/Monaco','CET','+01:00',1),(340,'Europe/Moscow','MSK','+04:00',1),(341,'Europe/Oslo','CET','+01:00',1),(342,'Europe/Paris','CET','+01:00',1),(343,'Europe/Podgorica','CET','+01:00',1),(344,'Europe/Prague','CET','+01:00',1),(345,'Europe/Riga','EET','+02:00',1),(346,'Europe/Rome','CET','+01:00',1),(347,'Europe/Samara','SAMT','+04:00',1),(348,'Europe/San_Marino','CET','+01:00',1),(349,'Europe/Sarajevo','CET','+01:00',1),(350,'Europe/Simferopol','FET','+03:00',1),(351,'Europe/Skopje','CET','+01:00',1),(352,'Europe/Sofia','EET','+02:00',1),(353,'Europe/Stockholm','CET','+01:00',1),(354,'Europe/Tallinn','EET','+02:00',1),(355,'Europe/Tirane','CET','+01:00',1),(356,'Europe/Uzhgorod','FET','+03:00',1),(357,'Europe/Vaduz','CET','+01:00',1),(358,'Europe/Vatican','CET','+01:00',1),(359,'Europe/Vienna','CET','+01:00',1),(360,'Europe/Vilnius','EET','+02:00',1),(361,'Europe/Volgograd','VOLT','+04:00',1),(362,'Europe/Warsaw','CET','+01:00',1),(363,'Europe/Zagreb','CET','+01:00',1),(364,'Europe/Zaporozhye','FET','+03:00',1),(365,'Europe/Zurich','CET','+01:00',1),(366,'Indian/Antananarivo','EAT','+03:00',1),(367,'Indian/Chagos','IOT','+06:00',1),(368,'Indian/Christmas','CXT','+07:00',1),(369,'Indian/Cocos','CCT','+06:30',1),(370,'Indian/Comoro','EAT','+03:00',1),(371,'Indian/Kerguelen','TFT','+05:00',1),(372,'Indian/Mahe','SCT','+04:00',1),(373,'Indian/Maldives','MVT','+05:00',1),(374,'Indian/Mauritius','MUT','+04:00',1),(375,'Indian/Mayotte','EAT','+03:00',1),(376,'Indian/Reunion','RET','+04:00',1),(377,'Pacific/Apia','WST','+13:00',1),(378,'Pacific/Auckland','NZDT','+13:00',1),(379,'Pacific/Chatham','CHADT','+13:45',1),(380,'Pacific/Chuuk','CHUT','+10:00',1),(381,'Pacific/Easter','EAST','-06:00',1),(382,'Pacific/Efate','VUT','+11:00',1),(383,'Pacific/Enderbury','PHOT','+13:00',1),(384,'Pacific/Fakaofo','TKT','-10:00',1),(385,'Pacific/Fiji','FJT','+12:00',1),(386,'Pacific/Funafuti','TVT','+12:00',1),(387,'Pacific/Galapagos','GALT','-06:00',1),(388,'Pacific/Gambier','GAMT','-09:00',1),(389,'Pacific/Guadalcanal','SBT','+11:00',1),(390,'Pacific/Guam','ChST','+10:00',1),(391,'Pacific/Honolulu','HST','-10:00',1),(392,'Pacific/Johnston','HST','-10:00',1),(393,'Pacific/Kiritimati','LINT','+14:00',1),(394,'Pacific/Kosrae','KOST','+11:00',1),(395,'Pacific/Kwajalein','MHT','+12:00',1),(396,'Pacific/Majuro','MHT','+12:00',1),(397,'Pacific/Marquesas','MART','-09:30',1),(398,'Pacific/Midway','SST','-11:00',1),(399,'Pacific/Nauru','NRT','+12:00',1),(400,'Pacific/Niue','NUT','-11:00',1),(401,'Pacific/Norfolk','NFT','+11:30',1),(402,'Pacific/Noumea','NCT','+11:00',1),(403,'Pacific/Pago_Pago','SST','-11:00',1),(404,'Pacific/Palau','PWT','+09:00',1),(405,'Pacific/Pitcairn','PST','-08:00',1),(406,'Pacific/Pohnpei','PONT','+11:00',1),(407,'Pacific/Port_Moresby','PGT','+10:00',1),(408,'Pacific/Rarotonga','CKT','-10:00',1),(409,'Pacific/Saipan','ChST','+10:00',1),(410,'Pacific/Tahiti','TAHT','-10:00',1),(411,'Pacific/Tarawa','GILT','+12:00',1),(412,'Pacific/Tongatapu','TOT','+13:00',1),(413,'Pacific/Wake','WAKT','+12:00',1),(414,'Pacific/Wallis','WFT','+12:00',1),(415,'UTC','UTC',' 00:00',1);

/*Table structure for table `tbl_weeks` */

DROP TABLE IF EXISTS `tbl_weeks`;

CREATE TABLE `tbl_weeks` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `week_id` int(11) unsigned DEFAULT NULL,
  `week_name` varchar(255) DEFAULT NULL,
  `createdby` int(11) DEFAULT NULL,
  `modifiedby` int(11) DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `isactive` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

/*Data for the table `tbl_weeks` */

insert  into `tbl_weeks`(`id`,`week_id`,`week_name`,`createdby`,`modifiedby`,`createddate`,`modifieddate`,`isactive`) values (1,0,'Sunday',1,1,'2013-09-02 17:17:44','2013-09-02 17:17:44',1),(2,1,'Monday',1,1,'2013-09-02 17:18:05','2013-09-02 17:18:05',1),(3,2,'Tuesday',1,1,'2013-09-02 17:18:28','2013-09-02 17:18:28',1),(4,3,'Wednesday',1,1,'2013-09-02 17:18:47','2013-09-02 17:18:47',1),(5,4,'Thursday',1,1,'2013-09-02 17:18:56','2013-09-02 17:18:56',1),(6,5,'Friday',1,1,'2013-09-02 17:19:09','2013-09-02 17:19:09',1),(7,6,'Saturday',1,1,'2013-09-02 17:19:20','2013-09-02 17:19:20',1);

/*Table structure for table `tm_clients` */

DROP TABLE IF EXISTS `tm_clients`;

CREATE TABLE `tm_clients` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `client_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone_no` varchar(20) DEFAULT NULL,
  `poc` varchar(100) NOT NULL,
  `address` varchar(200) DEFAULT NULL,
  `country_id` bigint(20) unsigned DEFAULT NULL,
  `state_id` bigint(20) unsigned DEFAULT NULL,
  `fax` varchar(50) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(10) unsigned DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified` timestamp NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `FK_client_country` (`country_id`),
  KEY `FK_client_state` (`state_id`),
  CONSTRAINT `FK_client_country` FOREIGN KEY (`country_id`) REFERENCES `tbl_countries` (`id`),
  CONSTRAINT `FK_client_state` FOREIGN KEY (`state_id`) REFERENCES `tbl_states` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Data for the table `tm_clients` */

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Data for the table `tm_configuration` */

/*Table structure for table `tm_cronjob_status` */

DROP TABLE IF EXISTS `tm_cronjob_status`;

CREATE TABLE `tm_cronjob_status` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `cronjob_status` enum('running','stopped') DEFAULT NULL,
  `start_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `end_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Data for the table `tm_cronjob_status` */

/*Table structure for table `tm_emp_timesheets` */

DROP TABLE IF EXISTS `tm_emp_timesheets`;

CREATE TABLE `tm_emp_timesheets` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `emp_id` int(10) unsigned NOT NULL,
  `project_task_id` bigint(20) unsigned DEFAULT NULL,
  `project_id` bigint(20) unsigned DEFAULT NULL,
  `ts_year` smallint(4) unsigned NOT NULL,
  `ts_month` tinyint(2) unsigned DEFAULT NULL,
  `ts_week` tinyint(1) unsigned DEFAULT NULL,
  `cal_week` tinyint(4) DEFAULT NULL,
  `sun_date` date DEFAULT NULL,
  `sun_duration` varchar(6) DEFAULT NULL,
  `mon_date` date DEFAULT NULL,
  `mon_duration` varchar(6) DEFAULT NULL,
  `tue_date` date DEFAULT NULL,
  `tue_duration` varchar(6) DEFAULT NULL,
  `wed_date` date DEFAULT NULL,
  `wed_duration` varchar(6) DEFAULT NULL,
  `thu_date` date DEFAULT NULL,
  `thu_duration` varchar(6) DEFAULT NULL,
  `fri_date` date DEFAULT NULL,
  `fri_duration` varchar(6) DEFAULT NULL,
  `sat_date` date DEFAULT NULL,
  `sat_duration` varchar(6) DEFAULT NULL,
  `week_duration` varchar(6) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(10) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `IDX_emp_task_time` (`emp_id`,`project_task_id`,`ts_year`,`ts_month`,`ts_week`,`cal_week`),
  KEY `FK_tm_emp_timesheets_proj_task` (`project_task_id`),
  KEY `FK_tm_emp_timesheets_project` (`project_id`),
  CONSTRAINT `FK_tm_emp_timesheets_employee` FOREIGN KEY (`emp_id`) REFERENCES `main_users` (`id`),
  CONSTRAINT `FK_tm_emp_timesheets_proj_task` FOREIGN KEY (`project_task_id`) REFERENCES `tm_project_tasks` (`id`),
  CONSTRAINT `FK_tm_emp_timesheets_project` FOREIGN KEY (`project_id`) REFERENCES `tm_projects` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `tm_emp_timesheets` */

/*Table structure for table `tm_emp_ts_notes` */

DROP TABLE IF EXISTS `tm_emp_ts_notes`;

CREATE TABLE `tm_emp_ts_notes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `emp_id` int(10) unsigned NOT NULL,
  `ts_year` smallint(4) NOT NULL,
  `ts_month` tinyint(2) DEFAULT NULL,
  `ts_week` tinyint(1) DEFAULT NULL,
  `cal_week` tinyint(4) DEFAULT NULL,
  `sun_date` date DEFAULT NULL,
  `sun_note` varchar(200) DEFAULT NULL,
  `sun_reject_note` varchar(200) DEFAULT NULL,
  `mon_date` date DEFAULT NULL,
  `mon_note` varchar(200) DEFAULT NULL,
  `mon_reject_note` varchar(200) DEFAULT NULL,
  `tue_date` date DEFAULT NULL,
  `tue_note` varchar(200) DEFAULT NULL,
  `tue_reject_note` varchar(200) DEFAULT NULL,
  `wed_date` date DEFAULT NULL,
  `wed_note` varchar(200) DEFAULT NULL,
  `wed_reject_note` varchar(200) DEFAULT NULL,
  `thu_date` date DEFAULT NULL,
  `thu_note` varchar(200) DEFAULT NULL,
  `thu_reject_note` varchar(200) DEFAULT NULL,
  `fri_date` date DEFAULT NULL,
  `fri_note` varchar(200) DEFAULT NULL,
  `fri_reject_note` varchar(200) DEFAULT NULL,
  `sat_date` date DEFAULT NULL,
  `sat_note` varchar(200) DEFAULT NULL,
  `sat_reject_note` varchar(200) DEFAULT NULL,
  `week_note` varchar(200) DEFAULT NULL,
  `created_by` int(10) unsigned DEFAULT NULL,
  `modified_by` int(10) unsigned DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `IDX_emp_ts_notes` (`emp_id`,`ts_year`,`ts_month`,`ts_week`,`cal_week`),
  CONSTRAINT `FK_tm_emp_ts_notes_employee` FOREIGN KEY (`emp_id`) REFERENCES `main_users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `tm_emp_ts_notes` */

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `tm_mailing_list` */

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `tm_process_updates` */

/*Table structure for table `tm_project_employees` */

DROP TABLE IF EXISTS `tm_project_employees`;

CREATE TABLE `tm_project_employees` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` bigint(20) unsigned NOT NULL,
  `emp_id` int(10) unsigned NOT NULL,
  `cost_rate` decimal(8,2) unsigned DEFAULT NULL,
  `billable_rate` decimal(7,2) unsigned DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(10) unsigned DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `FK_tm_project_employees_project` (`project_id`),
  KEY `FK_tm_project_employees_employee` (`emp_id`),
  CONSTRAINT `FK_tm_project_employees_employee` FOREIGN KEY (`emp_id`) REFERENCES `main_users` (`id`),
  CONSTRAINT `FK_tm_project_employees_project` FOREIGN KEY (`project_id`) REFERENCES `tm_projects` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `tm_project_employees` */

/*Table structure for table `tm_project_task_employees` */

DROP TABLE IF EXISTS `tm_project_task_employees`;

CREATE TABLE `tm_project_task_employees` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` bigint(20) unsigned NOT NULL,
  `task_id` bigint(20) unsigned NOT NULL,
  `project_task_id` bigint(20) unsigned NOT NULL,
  `emp_id` int(10) unsigned NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(10) unsigned DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `FK_tm_project_task_employees_project` (`project_id`),
  KEY `FK_tm_project_task_employees_task` (`task_id`),
  KEY `FK_tm_project_task_employees_proj_task` (`project_task_id`),
  KEY `FK_tm_project_task_employees_employee` (`emp_id`),
  CONSTRAINT `FK_tm_project_task_employees_employee` FOREIGN KEY (`emp_id`) REFERENCES `main_users` (`id`),
  CONSTRAINT `FK_tm_project_task_employees_proj_task` FOREIGN KEY (`project_task_id`) REFERENCES `tm_project_tasks` (`id`),
  CONSTRAINT `FK_tm_project_task_employees_project` FOREIGN KEY (`project_id`) REFERENCES `tm_projects` (`id`),
  CONSTRAINT `FK_tm_project_task_employees_task` FOREIGN KEY (`task_id`) REFERENCES `tm_tasks` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

/*Data for the table `tm_project_task_employees` */

/*Table structure for table `tm_project_tasks` */

DROP TABLE IF EXISTS `tm_project_tasks`;

CREATE TABLE `tm_project_tasks` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` bigint(20) unsigned NOT NULL,
  `task_id` bigint(20) unsigned NOT NULL,
  `estimated_hrs` mediumint(5) unsigned DEFAULT NULL,
  `is_billable` tinyint(1) DEFAULT '0',
  `billable_rate` decimal(25,2) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(10) unsigned DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified` timestamp NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `FK_tm_project_tasks_project` (`project_id`),
  KEY `FK_tm_project_tasks_task` (`task_id`),
  CONSTRAINT `FK_tm_project_tasks_project` FOREIGN KEY (`project_id`) REFERENCES `tm_projects` (`id`),
  CONSTRAINT `FK_tm_project_tasks_task` FOREIGN KEY (`task_id`) REFERENCES `tm_tasks` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

/*Data for the table `tm_project_tasks` */

/*Table structure for table `tm_projects` */

DROP TABLE IF EXISTS `tm_projects`;

CREATE TABLE `tm_projects` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `project_name` varchar(100) NOT NULL,
  `project_status` enum('initiated','draft','in-progress','hold','completed') NOT NULL,
  `base_project` bigint(20) DEFAULT NULL,
  `description` varchar(500) DEFAULT NULL,
  `client_id` bigint(20) unsigned NOT NULL,
  `currency_id` int(10) unsigned NOT NULL,
  `project_type` enum('billable','non_billable','revenue') NOT NULL,
  `lead_approve_ts` tinyint(4) DEFAULT NULL,
  `estimated_hrs` mediumint(5) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `initiated_date` timestamp NULL DEFAULT NULL,
  `hold_date` timestamp NULL DEFAULT NULL,
  `completed_date` timestamp NULL DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(10) unsigned DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified` timestamp NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `FK_tm_projects_client` (`client_id`),
  KEY `FK_tm_projects_currency` (`currency_id`),
  CONSTRAINT `FK_tm_projects_client` FOREIGN KEY (`client_id`) REFERENCES `tm_clients` (`id`),
  CONSTRAINT `FK_tm_projects_currency` FOREIGN KEY (`currency_id`) REFERENCES `main_currency` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

/*Data for the table `tm_projects` */

/*Table structure for table `tm_tasks` */

DROP TABLE IF EXISTS `tm_tasks`;

CREATE TABLE `tm_tasks` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `task` varchar(200) NOT NULL,
  `is_default` tinyint(4) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(10) unsigned DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified` timestamp NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

/*Data for the table `tm_tasks` */

/*Table structure for table `tm_ts_status` */

DROP TABLE IF EXISTS `tm_ts_status`;

CREATE TABLE `tm_ts_status` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `emp_id` int(10) unsigned NOT NULL,
  `project_id` bigint(20) unsigned DEFAULT NULL,
  `ts_year` smallint(4) unsigned NOT NULL,
  `ts_month` tinyint(2) unsigned DEFAULT NULL,
  `ts_week` tinyint(1) unsigned DEFAULT NULL,
  `cal_week` tinyint(2) DEFAULT NULL,
  `sun_date` date DEFAULT NULL,
  `sun_project_status` enum('saved','submitted','approved','enabled','rejected','blocked','no_entry') DEFAULT NULL,
  `sun_status` enum('saved','submitted','approved','enabled','rejected','blocked','no_entry') DEFAULT NULL,
  `sun_status_date` timestamp NULL DEFAULT NULL,
  `sun_reject_note` varchar(200) DEFAULT NULL,
  `mon_date` date DEFAULT NULL,
  `mon_project_status` enum('saved','submitted','approved','enabled','rejected','blocked','no_entry') DEFAULT NULL,
  `mon_status` enum('saved','submitted','approved','enabled','rejected','blocked','no_entry') DEFAULT NULL,
  `mon_status_date` timestamp NULL DEFAULT NULL,
  `mon_reject_note` varchar(200) DEFAULT NULL,
  `tue_date` date DEFAULT NULL,
  `tue_project_status` enum('saved','submitted','approved','enabled','rejected','blocked','no_entry') DEFAULT NULL,
  `tue_status` enum('saved','submitted','approved','enabled','rejected','blocked','no_entry') DEFAULT NULL,
  `tue_status_date` timestamp NULL DEFAULT NULL,
  `tue_reject_note` varchar(200) DEFAULT NULL,
  `wed_date` date DEFAULT NULL,
  `wed_project_status` enum('saved','submitted','approved','enabled','rejected','blocked','no_entry') DEFAULT NULL,
  `wed_status` enum('saved','submitted','approved','enabled','rejected','blocked','no_entry') DEFAULT NULL,
  `wed_status_date` timestamp NULL DEFAULT NULL,
  `wed_reject_note` varchar(200) DEFAULT NULL,
  `thu_date` date DEFAULT NULL,
  `thu_project_status` enum('saved','submitted','approved','enabled','rejected','blocked','no_entry') DEFAULT NULL,
  `thu_status` enum('saved','submitted','approved','enabled','rejected','blocked','no_entry') DEFAULT NULL,
  `thu_status_date` timestamp NULL DEFAULT NULL,
  `thu_reject_note` varchar(200) DEFAULT NULL,
  `fri_date` date DEFAULT NULL,
  `fri_project_status` enum('saved','submitted','approved','enabled','rejected','blocked','no_entry') DEFAULT NULL,
  `fri_status` enum('saved','submitted','approved','enabled','rejected','blocked','no_entry') DEFAULT NULL,
  `fri_status_date` timestamp NULL DEFAULT NULL,
  `fri_reject_note` varchar(200) DEFAULT NULL,
  `sat_date` date DEFAULT NULL,
  `sat_project_status` enum('saved','submitted','approved','enabled','rejected','blocked','no_entry') DEFAULT NULL,
  `sat_status` enum('saved','submitted','approved','enabled','rejected','blocked','no_entry') DEFAULT NULL,
  `sat_status_date` timestamp NULL DEFAULT NULL,
  `sat_reject_note` varchar(200) DEFAULT NULL,
  `week_status` enum('saved','submitted','approved','enabled','rejected','blocked','no_entry') DEFAULT NULL,
  `created_by` int(10) unsigned DEFAULT NULL,
  `modified_by` int(10) unsigned DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `IDX_emp_ts_project_status` (`emp_id`,`project_id`,`ts_year`,`ts_month`,`ts_week`,`cal_week`),
  KEY `FK_tm_ts_status_project` (`project_id`),
  CONSTRAINT `FK_tm_ts_status_employee` FOREIGN KEY (`emp_id`) REFERENCES `main_users` (`id`),
  CONSTRAINT `FK_tm_ts_status_project` FOREIGN KEY (`project_id`) REFERENCES `tm_projects` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `tm_ts_status` */


/*Table structure for table `main_requisition_history` */

DROP TABLE IF EXISTS `main_requisition_history`;

CREATE TABLE `main_requisition_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `requisition_id` int(20) DEFAULT NULL,
  `candidate_id` int(20) DEFAULT NULL,
  `candidate_name` varchar(150) DEFAULT NULL,
  `interview_id` int(20) DEFAULT NULL,
  `description` varchar(500) DEFAULT NULL,
  `createdby` int(11) DEFAULT NULL,
  `modifiedby` int(11) DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `isactive` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `main_leaverequest_history` */

DROP TABLE IF EXISTS `main_leaverequest_history`;

CREATE TABLE `main_leaverequest_history` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `leaverequest_id` INT(20) DEFAULT NULL,
  `description` VARCHAR(500) DEFAULT NULL,
  `createdby` INT(11) DEFAULT NULL,
  `modifiedby` INT(11) DEFAULT NULL,
  `createddate` DATETIME DEFAULT NULL,
  `modifieddate` DATETIME DEFAULT NULL,
  `isactive` TINYINT(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `main_vendors` */

DROP TABLE IF EXISTS `main_vendors`;

CREATE TABLE `main_vendors` (			
		`id` int(11) unsigned NOT NULL AUTO_INCREMENT,			
		`name` varchar(255) DEFAULT NULL,			
		`contact_person` varchar(255) DEFAULT NULL,			
		`address` varchar(200) DEFAULT NULL,			
		`country` bigint(20) DEFAULT NULL,			
		`state` bigint(20) DEFAULT NULL,			
		`city` bigint(20) DEFAULT NULL,			
		`pincode` varchar(15) DEFAULT NULL,			
		`primary_phone` varchar(15) DEFAULT NULL,			
		`secondary_phone` varchar(15) DEFAULT NULL,			
		`createdby` int(10) unsigned DEFAULT NULL,			
		`modifiedby` int(10) unsigned DEFAULT NULL,			
		`createddate` datetime DEFAULT NULL,			
		`modifieddate` datetime DEFAULT NULL,			
		`isactive` tinyint(1) DEFAULT '1',			
		PRIMARY KEY (`id`)			
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;