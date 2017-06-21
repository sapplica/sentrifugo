<?php
/********************************************************************************* 
 *  This file is part of Sentrifugo.
 *  Copyright (C) 2015 Sapplica
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

error_reporting(E_ALL | E_STRICT);
ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);


$os_string = php_uname('s');

if (strpos(strtoupper($os_string), 'WIN')!==false)
{
	// Win 
 defined('SERVER_OS') || define('SERVER_OS','windows');
}
else
{
	// Other
 defined('SERVER_OS') || define('SERVER_OS','linux');
}
defined('LIMIT')|| define('LIMIT', 20);
defined('NA')|| define('NA', 0);
defined('YEAR')|| define('YEAR', 1900);
defined('PERPAGE')|| define('PERPAGE', 20);
defined('DASHBOARD_PERPAGE')|| define('DASHBOARD_PERPAGE', 10);
// Define path to application directory
defined('APPLICATION_PATH') || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));
defined('ACCESS_CONTROL_PATH') || define('ACCESS_CONTROL_PATH', realpath(dirname(__FILE__). '/..'));

//Define Application SuperAdmin
defined('SUPERADMIN') || define('SUPERADMIN', 1);
//Defining SuperAdmin Role...
defined('SUPERADMINROLE') || define('SUPERADMINROLE', 1);


//Define Application Groups....
//Super Admin Group..

//Management Group..
defined('MANAGEMENT_GROUP')|| define('MANAGEMENT_GROUP', 1);
//Manager Group..
defined('MANAGER_GROUP')|| define('MANAGER_GROUP', 2);
//HR Group..
defined('HR_GROUP')|| define('HR_GROUP', 3);
//Employee  Group..
defined('EMPLOYEE_GROUP')|| define('EMPLOYEE_GROUP', 4);
//System Admin Group..
defined('SYSTEMADMIN_GROUP')|| define('SYSTEMADMIN_GROUP', 6);
//users Group..
defined('USERS_GROUP')|| define('USERS_GROUP', 5);
//Custom Group..
defined('CUSTOM_GROUP')|| define('CUSTOM_GROUP', 7);

/* Default gmt offset */
define('DEFAULT_GMT_OFFSET','+05:30');
/* END */
defined('YEAR')|| define('YEAR', 1900);

//Defining objects for menus
defined('ACCOUNTCLASSTYPE')|| define('ACCOUNTCLASSTYPE', 92);
defined('HUMANRESOURCE')|| define('HUMANRESOURCE', 3);
defined('SITECONFIGURATION')|| define('SITECONFIGURATION', 70);
defined('MANAGEMODULE')|| define('MANAGEMODULE', 142);
defined('EMPLOYEECONFIGURATION')|| define('EMPLOYEECONFIGURATION', 113);
defined('EMPLOYEESELFSERVICE')|| define('EMPLOYEESELFSERVICE', 4);
defined('BGCHECKS')|| define('BGCHECKS', 5);
defined('ORGANIZATION')|| define('ORGANIZATION', 1);
defined('BENEFITS')|| define('BENEFITS', 15);
defined('LEAVES')|| define('LEAVES', 31);
defined('STAFFING')|| define('STAFFING', 6);
defined('COMPLIANCES')|| define('COMPLIANCES', 7);
defined('REPORTS')|| define('REPORTS', 8);
defined('RESOURCEREQUISITION')|| define('RESOURCEREQUISITION', 19);
defined('TIMEMANAGEMENT')|| define('TIMEMANAGEMENT', 130);
defined('EXPENSES')|| define('EXPENSES', 185);
defined('EXPENSE_CATEGORY')|| define('EXPENSE_CATEGORY', 191);
defined('EXPENSE_PAYMENTMODE')|| define('EXPENSE_PAYMENTMODE', 192);
defined('RECEIPTS')|| define('RECEIPTS', 193);
defined('TRIPS')|| define('TRIPS', 194);
defined('ADVANCES')|| define('ADVANCES', 195);
defined('MYADVANCES')|| define('MYADVANCES', 196);
defined('EMPLOYEEADVANCES')|| define('EMPLOYEEADVANCES', 197);
defined('SUB_EXPENSES')|| define('SUB_EXPENSES', 198);
defined('MYEMPLOYEEEXPENSES')|| define('MYEMPLOYEEEXPENSES', 199);
defined('ASSETCATEGORIES')|| define('ASSETCATEGORIES', 190);
defined('SUBASSET')|| define('SUBASSET', 200);
defined('AGENCYLIST')|| define('AGENCYLIST', 69);
defined('APPROVEDLEAVES')|| define('APPROVEDLEAVES', 63);
defined('APPROVEDREQUISITIONS')|| define('APPROVEDREQUISITIONS', 134);
defined('ASSIGNMENTENTRYREASONCODE')|| define('ASSIGNMENTENTRYREASONCODE', 122);
defined('ATTENDANCESTATUSCODE')|| define('ATTENDANCESTATUSCODE', 126);
defined('BANKACCOUNTTYPE')|| define('BANKACCOUNTTYPE',  123);
defined('BUSINESSUNITS')|| define('BUSINESSUNITS',  10);
defined('CANCELLEAVES')|| define('CANCELLEAVES',   64);
defined('CITIES')|| define('CITIES', 102);
defined('COMPETENCYLEVEL')|| define('COMPETENCYLEVEL',  124);
defined('PERFORMANCEAPPRSETUP')|| define('PERFORMANCEAPPRSETUP',  50);
defined('COUNTRIES')|| define('COUNTRIES', 100);
defined('CURRENCY')|| define('CURRENCY',  110);
defined('CURRENCYCONVERTER')|| define('CURRENCYCONVERTER',111);
defined('CANDIDATEDETAILS')|| define('CANDIDATEDETAILS',  55);
defined('DATEFORMAT')|| define('DATEFORMAT', 78);
defined('WEEKDAYS')|| define('WEEKDAYS',81);
defined('DEPARTMENTS')|| define('DEPARTMENTS',11);
defined('EDUCATIONLEVELCODE')|| define('EDUCATIONLEVELCODE', 125);
defined('EEOCCATEGORY')|| define('EEOCCATEGORY',  115);
defined('EMAILCONTACTS')|| define('EMAILCONTACTS', 136);
defined('EMPBENEFITSUSENROLLMENT')|| define('EMPBENEFITSUSENROLLMENT',39);
defined('EMPLEAVESUMMARY')|| define('EMPLEAVESUMMARY', 45);
defined('EMPSCREENING')|| define('EMPSCREENING',23);
defined('EMPLOYEE')|| define('EMPLOYEE',14);
defined('EMPLOYMENTSTATUS')|| define('EMPLOYMENTSTATUS', 114);
defined('ETHNICCODE')|| define('ETHNICCODE', 85);
defined('GENDER')|| define('GENDER',  86);
defined('GEOGRAPHYGROUP')|| define('GEOGRAPHYGROUP',103);
defined('IDENTITYCODES')|| define('IDENTITYCODES', 133);
defined('IDENTITYDOCUMENTS')|| define('IDENTITYDOCUMENTS', 139);
defined('EMPLOYEETABS')|| define('EMPLOYEETABS', 140);
defined('SCREENING')|| define('SCREENING', 141);
defined('APPRAISALINITIALIZATION')|| define('APPRAISALINITIALIZATION',51);
defined('JOBTITLES')|| define('JOBTITLES', 116);
defined('PERFORMANCEKIPS')|| define('PERFORMANCEKIPS', 47);
defined('PERFORMANCEKRAS')|| define('PERFORMANCEKRAS', 48);
defined('LANGUAGE')|| define('LANGUAGE',  121);
defined('LEAVEMANAGEMENT')|| define('LEAVEMANAGEMENT', 44);
defined('LEAVEREQUEST')|| define('LEAVEREQUEST',   61);
defined('EMPLOYEELEAVETYPES')|| define('EMPLOYEELEAVETYPES',   128);
defined('LICENSETYPE')|| define('LICENSETYPE',93);
defined('HOLIDAYDATES')|| define('HOLIDAYDATES',   42);
defined('HOLIDAYGROUPS')|| define('HOLIDAYGROUPS', 41);
defined('USERMANAGEMENT')|| define('USERMANAGEMENT', 2);
defined('MANAGEREMPLOYEEVACATIONS')|| define('MANAGEREMPLOYEEVACATIONS',   65);
defined('MARITALSTATUS')|| define('MARITALSTATUS', 87);
defined('MILITARYSERVICE')|| define('MILITARYSERVICE', 108);
defined('MONTHSLIST')|| define('MONTHSLIST', 82);
defined('MYDETAILS')|| define('MYDETAILS',  32);
defined('MYHOLIDAYCALENDAR')|| define('MYHOLIDAYCALENDAR', 43);
defined('MYEMPLOYEES')|| define('MYEMPLOYEES',34);
defined('NATIONALITY')|| define('NATIONALITY',91);
defined('NATIONALITYCONTEXTCODE')|| define('NATIONALITYCONTEXTCODE',90);
defined('NUMBERFORMATS')|| define('NUMBERFORMATS', 132);
defined('REQUISITION')|| define('REQUISITION',54);
defined('HEIRARCHY')|| define('HEIRARCHY',  13);
defined('ORGANISATIONINFO')|| define('ORGANISATIONINFO', 9);
defined('STRUCTURE')|| define('STRUCTURE',  12);
defined('PAYFREQUENCY')|| define('PAYFREQUENCY', 117);
defined('PENDINGLEAVES')|| define('PENDINGLEAVES', 62);
defined('POSITIONS')|| define('POSITIONS', 120);
defined('PREFIX')|| define('PREFIX', 88);
defined('RACECODE')|| define('RACECODE',89);
defined('REJECTEDLEAVES')|| define('REJECTEDLEAVES',135);
defined('REJECTEDREQUISITIONS')|| define('REJECTEDREQUISITIONS', 138);
defined('REMUNERATIONBASIS')|| define('REMUNERATIONBASIS',118);
defined('ROLES')|| define('ROLES', 20);
defined('MANAGEEXTERNALUSERS')|| define('MANAGEEXTERNALUSERS', 21);
defined('BENEFITSSAVINGPLANENROLLMENT')|| define('BENEFITSSAVINGPLANENROLLMENT', 38);
defined('SCHEDULEINTERVIEWS')|| define('SCHEDULEINTERVIEWS',57);
defined('BGSCREENINGTYPE')|| define('BGSCREENINGTYPE', 68);
defined('SHORTLISTEDCANDIDATES')|| define('SHORTLISTEDCANDIDATES', 56);
defined('SITEPREFERENCE')|| define('SITEPREFERENCE',131);
defined('STATES')|| define('STATES', 101);
defined('SERVICEDESK')|| define('SERVICEDESK', 143);
defined('SERVICEDESKDEPARTMENT')|| define('SERVICEDESKDEPARTMENT', 144);
defined('SERVICEDESKREQUEST')|| define('SERVICEDESKREQUEST', 145);
defined('SERVICEDESKCONFIGURATION')|| define('SERVICEDESKCONFIGURATION', 146);
defined('TIMEFORMAT')|| define('TIMEFORMAT', 79);
defined('TIMEZONE')|| define('TIMEZONE',80);
defined('VENDORSCREENING')|| define('VENDORSCREENING', 24);
defined('VENDORS')|| define('VENDORS', 187);
defined('VETERANSTATUS')|| define('VETERANSTATUS', 107);
defined('WORKELIGIBILITYDOCTYPES')|| define('WORKELIGIBILITYDOCTYPES', 127);
defined('SD_TRANS')|| define('SD_TRANS', 148);
defined('PERFORMANCEAPPRAISAL')|| define('PERFORMANCEAPPRAISAL', 149);
defined('APPRAISALCATEGORIES')|| define('APPRAISALCATEGORIES', 150);
defined('APPRAISALSKILLS')|| define('APPRAISALSKILLS', 151);
defined('APPRAISALQUESTIONS')|| define('APPRAISALQUESTIONS', 152);
defined('INITIALIZE_APPRAISAL')|| define('INITIALIZE_APPRAISAL', 154);
defined('APPRAISALRATINGS')|| define('APPRAISALRATINGS', 160);
defined('APPRAISALSELF')|| define('APPRAISALSELF', 161);
defined('APPRAISAL_SETTINGS')|| define('APPRAISAL_SETTINGS', 155);
defined('FEED_FORWARD')|| define('FEED_FORWARD', 163);
defined('ANNOUNCEMENTS')|| define('ANNOUNCEMENTS', 168);
defined('APPRAISAL_MANAGER')|| define('APPRAISAL_MANAGER', 169);
defined('FEEDFORWARDQUESTIONS')|| define('FEEDFORWARDQUESTIONS', 166);
defined('INITIALIZE_FEEDFORWARD')|| define('INITIALIZE_FEEDFORWARD', 167);
defined('APPRAISE_YOUR_MANAGER')|| define('APPRAISE_YOUR_MANAGER', 170);
defined('MANAGERSTATUS')|| define('MANAGERSTATUS', 158);
defined('EMPLOYEESTATUS')|| define('EMPLOYEESTATUS', 159);
defined('DASHBOARD_MENU')|| define('DASHBOARD_MENU', 173);
defined('MANAGER_FEEDFORWARD')|| define('MANAGER_FEEDFORWARD', 171);
defined('MY_TEAM_APPRAISAL')|| define('MY_TEAM_APPRAISAL', 174);
defined('APPRAISAL_HISTORY')|| define('APPRAISAL_HISTORY', 175);
defined('POLICY_DOCUMENTS')|| define('POLICY_DOCUMENTS', 176);
defined('EXIT_PROC')|| define('EXIT_PROC', 177);
defined('EXIT_PROC_SETTINGS')|| define('EXIT_PROC_SETTINGS', 178);
defined('EXIT_PROC_TYPES')|| define('EXIT_PROC_TYPES', 179);
defined('INITIATE_EXIT_PROC')|| define('INITIATE_EXIT_PROC', 180);
defined('ALL_EXIT_PROCS')|| define('ALL_EXIT_PROCS', 181);
defined('POLICY_DOCS_CATEGORIES')|| define('POLICY_DOCS_CATEGORIES', 182);
defined('MANAGE_POLICY_DOCS')|| define('MANAGE_POLICY_DOCS', 183);
defined('ADD_EMPLOYEE_LEAVES')|| define('ADD_EMPLOYEE_LEAVES', 184);
defined('ASSETS')|| define('ASSETS', 186);
defined('DISCIPLINARY')|| define('DISCIPLINARY', 201);
defined('VIOLATION_TYPE')|| define('VIOLATION_TYPE', 202);
defined('RAISE_INCIDENT')|| define('RAISE_INCIDENT', 203);
defined('MY_INCIDENT')|| define('MY_INCIDENT', 204);
defined('TEAM_INCIDENT')|| define('TEAM_INCIDENT', 205);
defined('CLIENTS')|| define('CLIENTS', 208);
defined('EXIT_QUESTIONS')|| define('EXIT_QUESTIONS', 210);
// Controllers which are not there in main_menu
defined('VIEWSETTINGS')|| define('VIEWSETTINGS', 'VIEWSETTINGS');
defined('DASHBOARD')|| define('DASHBOARD', 'DASHBOARD');
defined('LOGMANAGER')|| define('LOGMANAGER', 'LOGMANAGER');
defined('USERLOGINLOG')|| define('USERLOGINLOG', 'USERLOGINLOG');

/*Exit procedure menus*/
defined('EXITINITIALSTATUS')|| define('EXITINITIALSTATUS', 180);
defined('EXITPROCEDURE')|| define('EXITPROCEDURE', 177);


//Manage Moduels Array
defined('MANAGE_MODULE_ARRAY')|| define('MANAGE_MODULE_ARRAY', serialize (array(TIMEMANAGEMENT,RESOURCEREQUISITION,BGCHECKS,REPORTS,SERVICEDESK,PERFORMANCEAPPRAISAL)));
// Employee documents 
defined('EMP_MAX_DOCS')|| define('EMP_MAX_DOCS', 20);

//Defining messaged for for deleting menu records
defined('ACCOUNTCLASSTYPE_DELETE')|| define('ACCOUNTCLASSTYPE_DELETE', 'account class type');
defined('HUMANRESOURCE_DELETE')|| define('HUMANRESOURCE_DELETE', 'human resource');
defined('BGCHECKS_DELETE')|| define('BGCHECKS_DELETE', 'background check');
defined('STAFFING_DELETE')|| define('STAFFING_DELETE', 'staffing');
defined('COMPLIANCES_DELETE')|| define('COMPLIANCES_DELETE', 'compliance');
defined('REPORTS_DELETE')|| define('REPORTS_DELETE', 'report');
defined('RESOURCEREQUISITION_DELETE')|| define('RESOURCEREQUISITION_DELETE', 'resource requisition');
defined('TIMEMANAGEMENT_DELETE')|| define('TIMEMANAGEMENT_DELETE', 'time management');
defined('AGENCYLIST_DELETE')|| define('AGENCYLIST_DELETE', 'agency');
defined('APPROVEDLEAVES_DELETE')|| define('APPROVEDLEAVES_DELETE', 'leave');
defined('APPROVEDREQUISITIONS_DELETE')|| define('APPROVEDREQUISITIONS_DELETE', 'approved requisition');
defined('ASSIGNMENTENTRYREASONCODE_DELETE')|| define('ASSIGNMENTENTRYREASONCODE_DELETE', 'assignment entry reason code');
defined('ATTENDANCESTATUSCODE_DELETE')|| define('ATTENDANCESTATUSCODE_DELETE', 'attendance status code');
defined('BANKACCOUNTTYPE_DELETE')|| define('BANKACCOUNTTYPE_DELETE',  'bank account type');
defined('BUSINESSUNITS_DELETE')|| define('BUSINESSUNITS_DELETE',  'business unit');
defined('CANCELLEAVES_DELETE')|| define('CANCELLEAVES_DELETE',   'cancelled leave');
defined('CITIES_DELETE')|| define('CITIES_DELETE', 'city');
defined('COMPETENCYLEVEL_DELETE')|| define('COMPETENCYLEVEL_DELETE',  'competency level');
defined('PERFORMANCEAPPRSETUP_DELETE')|| define('PERFORMANCEAPPRSETUP_DELETE',  'performance appraisal setup');
defined('COUNTRIES_DELETE')|| define('COUNTRIES_DELETE', 'country');
defined('CURRENCY_DELETE')|| define('CURRENCY_DELETE',  'currency');
defined('CURRENCYCONVERTER_DELETE')|| define('CURRENCYCONVERTER_DELETE','currency converter');
defined('CANDIDATEDETAILS_DELETE')|| define('CANDIDATEDETAILS_DELETE', 'candidate detail');
defined('DATEFORMAT_DELETE')|| define('DATEFORMAT_DELETE', 'date format');
defined('INTERVIEWROUNDS_DELETE')|| define('INTERVIEWROUNDS_DELETE', 'interview round');
defined('SERVICEREQUESTS_DELETE')|| define('SERVICEREQUESTS_DELETE', 'service reqiest');
/*	Employee Tabs 	*/
defined('EMPSKILLS_DELETE')|| define('EMPSKILLS_DELETE', 'skill');
defined('EMPJOBHISTORY_DELETE')|| define('EMPJOBHISTORY_DELETE', 'job history');
defined('EXPERIENCEDETAILS_DELETE')|| define('EXPERIENCEDETAILS_DELETE', 'experience details');
defined('EDUCATIONDETAILS_DELETE')|| define('EDUCATIONDETAILS_DELETE', 'education details');
defined('EMPLEAVES_DELETE')|| define('EMPLEAVES_DELETE', 'leave details');
defined('EMPHOLIDAYS_DELETE')|| define('EMPHOLIDAYS_DELETE', 'holiday details');
defined('TRAININGANDCERTIFICATIONDETAILS_DELETE')|| define('TRAININGANDCERTIFICATIONDETAILS_DELETE', 'certification details');
defined('MEDICALCLAIMS_DELETE')|| define('MEDICALCLAIMS_DELETE', 'medical claim details');
defined('EMPADDITIONALDETAILS_DELETE')|| define('EMPADDITIONALDETAILS_DELETE', 'additional details');
defined('DEPENDENCYDETAILS_DELETE')|| define('DEPENDENCYDETAILS_DELETE', 'dependency details');
defined('APPRREQCANDIDATES_DELETE')|| define('APPRREQCANDIDATES_DELETE','candidate');
defined('PROCESSES_DELETE')|| define('PROCESSES_DELETE','process');
defined('IDENTITYDOCUMENTS_DELETE')|| define('IDENTITYDOCUMENTS_DELETE','identity document');
/*	Employee Tabs End	*/

defined('WEEKDAYS_DELETE')|| define('WEEKDAYS_DELETE','week day');
defined('DEPARTMENTS_DELETE')|| define('DEPARTMENTS_DELETE','department');
defined('EDUCATIONLEVELCODE_DELETE')|| define('EDUCATIONLEVELCODE_DELETE', 'education level code');
defined('EEOCCATEGORY_DELETE')|| define('EEOCCATEGORY_DELETE',  'EEOC category');
defined('EMAILCONTACTS_DELETE')|| define('EMAILCONTACTS_DELETE', 'email contact');
defined('EMPBENEFITSUSENROLLMENT_DELETE')|| define('EMPBENEFITSUSENROLLMENT_DELETE','employee benefits us enrollment');
defined('EMPLEAVESUMMARY_DELETE')|| define('EMPLEAVESUMMARY_DELETE', 'leave');
defined('EMPSCREENING_DELETE')|| define('EMPSCREENING_DELETE',   'Background check process');
defined('EMPLOYEE_DELETE')|| define('EMPLOYEE_DELETE','employee');
defined('EMPLOYMENTSTATUS_DELETE')|| define('EMPLOYMENTSTATUS_DELETE', 'employment status');
defined('ETHNICCODE_DELETE')|| define('ETHNICCODE_DELETE', 'ethnic code');
defined('GENDER_DELETE')|| define('GENDER_DELETE',  'gender');
defined('GEOGRAPHYGROUP_DELETE')|| define('GEOGRAPHYGROUP_DELETE','geography group');
defined('IDENTITYCODES_DELETE')|| define('IDENTITYCODES_DELETE', 'identity code');
defined('APPRAISALINITIALIZATION_DELETE')|| define('APPRAISALINITIALIZATION_DELETE','appraisal initialization');
defined('JOBTITLES_DELETE')|| define('JOBTITLES_DELETE', 'job title');
defined('PERFORMANCEKIPS_DELETE')|| define('PERFORMANCEKIPS_DELETE', 'performace KIP');
defined('PERFORMANCEKRAS_DELETE')|| define('PERFORMANCEKRAS_DELETE', 'performace KRA');
defined('LANGUAGE_DELETE')|| define('LANGUAGE_DELETE',  'language');
defined('LEAVEMANAGEMENT_DELETE')|| define('LEAVEMANAGEMENT_DELETE', 'leave management');
defined('LEAVEREQUEST_DELETE')|| define('LEAVEREQUEST_DELETE',   'leave request');
defined('EMPLOYEELEAVETYPES_DELETE')|| define('EMPLOYEELEAVETYPES_DELETE',   'leave type');
defined('LICENSETYPE_DELETE')|| define('LICENSETYPE_DELETE','license type');
defined('HOLIDAYDATES_DELETE')|| define('HOLIDAYDATES_DELETE',   'holiday');
defined('HOLIDAYGROUPS_DELETE')|| define('HOLIDAYGROUPS_DELETE', 'holiday group');
defined('USERMANAGEMENT_DELETE')|| define('USERMANAGEMENT_DELETE', 'user');
defined('MANAGEREMPLOYEEVACATIONS_DELETE')|| define('MANAGEREMPLOYEEVACATIONS_DELETE','vacation');
defined('MARITALSTATUS_DELETE')|| define('MARITALSTATUS_DELETE', 'marital status');
defined('MILITARYSERVICE_DELETE')|| define('MILITARYSERVICE_DELETE', 'military status');
defined('MONTHSLIST_DELETE')|| define('MONTHSLIST_DELETE', 'month');
defined('MYDETAILS_DELETE')|| define('MYDETAILS_DELETE',  'detail');
defined('MYHOLIDAYCALENDAR_DELETE')|| define('MYHOLIDAYCALENDAR_DELETE','calendar');
defined('MYEMPLOYEES_DELETE')|| define('MYEMPLOYEES_DELETE','employee');
defined('NATIONALITY_DELETE')|| define('NATIONALITY_DELETE','nationality');
defined('NATIONALITYCONTEXTCODE_DELETE')|| define('NATIONALITYCONTEXTCODE_DELETE','nationality context code');
defined('NUMBERFORMATS_DELETE')|| define('NUMBERFORMATS_DELETE','number formats');
defined('REQUISITION_DELETE')|| define('REQUISITION_DELETE','requisition');
defined('HEIRARCHY_DELETE')|| define('HEIRARCHY_DELETE','heirarchy');
defined('ORGANISATIONINFO_DELETE')|| define('ORGANISATIONINFO_DELETE', 'organisation information');
defined('STRUCTURE_DELETE')|| define('STRUCTURE_DELETE',  'structure');
defined('PAYFREQUENCY_DELETE')|| define('PAYFREQUENCY_DELETE', 'pay frequency');
defined('PENDINGLEAVES_DELETE')|| define('PENDINGLEAVES_DELETE', 'pending leave');
defined('POSITIONS_DELETE')|| define('POSITIONS_DELETE','position');
defined('PREFIX_DELETE')|| define('PREFIX_DELETE', 'prefix');
defined('RACECODE_DELETE')|| define('RACECODE_DELETE','race code');
defined('REJECTEDLEAVES_DELETE')|| define('REJECTEDLEAVES_DELETE','rejected leave');
defined('REJECTEDREQUISITIONS_DELETE')|| define('REJECTEDREQUISITIONS_DELETE','rejected requisition');
defined('REMUNERATIONBASIS_DELETE')|| define('REMUNERATIONBASIS_DELETE','remuneration basis');
defined('ROLES_DELETE')|| define('ROLES_DELETE', 'role');
defined('BENEFITSSAVINGPLANENROLLMENT_DELETE')|| define('BENEFITSSAVINGPLANENROLLMENT_DELETE','benefit or saving plan enrollment');
defined('SCHEDULEINTERVIEWS_DELETE')|| define('SCHEDULEINTERVIEWS_DELETE','scheduled interviews');
defined('SERVICEDESKDEPARTMENT_DELETE')|| define('SERVICEDESKDEPARTMENT_DELETE','category');
defined('SERVICEDESKREQUEST_DELETE')|| define('SERVICEDESKREQUEST_DELETE','request type');
defined('SERVICEDESKCONF_DELETE')|| define('SERVICEDESKCONF_DELETE','setting');
defined('BGSCREENINGTYPE_DELETE')|| define('BGSCREENINGTYPE_DELETE', 'screening type');
defined('SHORTLISTEDCANDIDATES_DELETE')|| define('SHORTLISTEDCANDIDATES_DELETE','candidate');
defined('SITEPREFERENCE_DELETE')|| define('SITEPREFERENCE_DELETE','site preference');
defined('STATES_DELETE')|| define('STATES_DELETE','state');
defined('TIMEFORMAT_DELETE')|| define('TIMEFORMAT_DELETE','time format');
defined('TIMEZONE_DELETE')|| define('TIMEZONE_DELETE','time zone');
defined('VENDORSCREENING_DELETE')|| define('VENDORSCREENING_DELETE','vendor screening');
defined('VENDORS_DELETE')|| define('VENDORS_DELETE','vendors');
defined('VETERANSTATUS_DELETE')|| define('VETERANSTATUS_DELETE', 'veteran status');
defined('WORKELIGIBILITYDOCTYPES_DELETE')|| define('WORKELIGIBILITYDOCTYPES_DELETE', 'work eligibility document type');
defined('APPRAISALCATEGORY_DELETE')|| define('APPRAISALCATEGORY_DELETE','appraisal parameter');
defined('APPRAISALSKILLS_DELETE')|| define('APPRAISALSKILLS_DELETE','appraisal skill');
defined('APPRAISALQUESTIONS_DELETE')|| define('APPRAISALQUESTIONS_DELETE','appraisal question');
defined('APPRAISALGROUPS_DELETE')|| define('APPRAISALGROUPS_DELETE','appraisal group');
defined('APPRAISALINIT_DELETE')|| define('APPRAISALINIT_DELETE','appraisal initialization');
defined('APPRAISALSTATUSEMPLOYEE_DELETE')|| define('APPRAISALSTATUSEMPLOYEE_DELETE','employee appraisal status');
defined('APPRAISALSTATUSMANAGER_DELETE')|| define('APPRAISALSTATUSMANAGER_DELETE','manager appraisal status');
defined('APPRAISALCONFIG_DELETE')|| define('APPRAISALCONFIG_DELETE','configuration');
defined('APPRAISALSELF_DELETE')|| define('APPRAISALSELF_DELETE','self appraisal');
defined('APPRAISALRATINGS_DELETE')|| define('APPRAISALRATINGS_DELETE','appraisal ratings');
defined('ANNOUNCEMENTS_DELETE')|| define('ANNOUNCEMENTS_DELETE','announcement');
defined('FEEDFORWARDINIT_DELETE')|| define('FEEDFORWARDINIT_DELETE','feedforward initialization');
defined('APPRAISALMANAGER_DELETE')|| define('APPRAISALMANAGER_DELETE','feedforward initialization');
defined('FEEDFORWARDQUESTIONS_DELETE')|| define('FEEDFORWARDQUESTIONS_DELETE','feedforward question');

defined('ADDEMPLOYEELEAVES_DELETE')|| define('ADDEMPLOYEELEAVES_DELETE','Employee Leaves');
defined('CATEGORIES_DELETE')|| define('CATEGORIES_DELETE','category');
defined('POLICYDOCUMENTS_DELETE')|| define('POLICYDOCUMENTS_DELETE','document');
defined('VISAANDIMMIGRATIONDETAILS_DELETE')|| define('VISAANDIMMIGRATIONDETAILS_DELETE','visa and immigration details');

defined('EXITTYPES_DELETE')|| define('EXITTYPES_DELETE','exit type');
defined('CONFIGUREEXITQS_DELETE')|| define('CONFIGUREEXITQS_DELETE','exit type questions');
defined('EXITPROCSETTINGS_DELETE')|| define('EXITPROCSETTINGS_DELETE','exit procedure setting');
defined('ALLEXITPROC_DELETE')|| define('ALLEXITPROC_DELETE','exit procedure');

defined('APPRAISALHISTORYSELF_DELETE')|| define('APPRAISALHISTORYSELF_DELETE','self history');
defined('APPRAISALHISTORYTEAM_DELETE')|| define('APPRAISALHISTORYTEAM_DELETE','team history');

defined('DISCIPLINARYMYINCIDENTS_DELETE')|| define('DISCIPLINARYMYINCIDENTS_DELETE','disciplinary incident');
defined('DISCIPLINARYTEAMINCIDENTS_DELETE')|| define('DISCIPLINARYTEAMINCIDENTS_DELETE','disciplinary incident');
defined('DISCIPLINARYINCIDENT_DELETE')|| define('DISCIPLINARYINCIDENT_DELETE','disciplinary incident');
defined('DISCIPLINARYVIOLATION_DELETE')|| define('DISCIPLINARYVIOLATION_DELETE','Violation Type');
defined('DISCIPLINARYALLINCIDENTS_DELETE')|| define('DISCIPLINARYALLINCIDENTS_DELETE','Violation Type');
defined('EXITTYPES_DELETE')|| define('EXITTYPES_DELETE','exittypes');
defined('EXITPROC_DELETE')|| define('EXITPROC_DELETE','exitproc');

defined('ASSETS_DELETE')|| define('ASSETS_DELETE','asset');
defined('ASSETCATEGORIES_DELETE')|| define('ASSETCATEGORIES_DELETE','asset category');

// Installation URLS
defined('PHPURL')|| define('PHPURL', 'http://www.sentrifugo.com/home/installation-guide#prerequisites');
defined('PDOURL')|| define('PDOURL', 'http://www.sentrifugo.com/home/installation-guide#prerequisites');
defined('MODURL')|| define('MODURL', 'http://www.sentrifugo.com/home/installation-guide#prerequisites');
defined('GDURL')|| define('GDURL', 'http://www.sentrifugo.com/home/installation-guide#prerequisites');
defined('OPENSSLURL')|| define('OPENSSLURL', 'http://www.sentrifugo.com/home/installation-guide#prerequisites');
       
$domain = "";
if(isset($_SERVER['HTTP_HOST']))
{
	
    $domainurl = isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on' ? 'https' : 'http';
  
    $domainurl .= '://'. $_SERVER['HTTP_HOST'];
   
    $domain = $domainurl;
    $domainurl .= rtrim(str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']),'/');
   
    // Base URI 
    $base_uri = parse_url($domainurl, PHP_URL_PATH);
  
    if(substr($base_uri, 0, 1) != '/') $base_uri = '/'.$base_uri;
    if(substr($base_uri, -1, 1) != '/') $base_uri .= '/';
}
else
{
    $domainurl = 'http://localhost';
    $base_uri = '/';
}
		 
// Define Project Name

defined('DOMAIN') || define('DOMAIN', $domainurl.'/');
defined('PARENTDOMAIN') || define('PARENTDOMAIN', 'http://www.sentrifugo.com');
defined('WEBSERVICEURL') || define('WEBSERVICEURL', 'http://www.sentrifugo.com/services');

defined('SERVICEDOMAIN') || define('SERVICEDOMAIN', $domain);

defined('IMAGE_UPLOAD_PATH') || define('IMAGE_UPLOAD_PATH',realpath(dirname(__FILE__)).'/uploads/organisation');

defined('DONOTREPLYEMAIL') || define('DONOTREPLYEMAIL', 'donot-reply@example.com');

defined('DONOTREPLYNAME') || define('DONOTREPLYNAME', 'Do-not-reply');


defined('DATA_PATH') || define('DATA_PATH', realpath(dirname(__FILE__) . '/../data'));

   // for extras
    defined('EXTERNALS')
    || define('EXTERNALS', realpath(dirname(__FILE__) . '/../externals'));

defined('ADMIN_PATH')
    || define('ADMIN_PATH', $domainurl. '/admin/');

    // Define path to admin directory
defined('ADMIN_PATH_REAL')
    || define('ADMIN_PATH_REAL', realpath(dirname(__FILE__) . '/../application/modules/admin'));


// define path upto /public
defined('BASE_PATH') || define('BASE_PATH', realpath(dirname(__FILE__).'/'));

// Define application environment
defined('APPLICATION_ENV') || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

// define path upto /public
defined('MEDIA_PATH') || define('MEDIA_PATH', $domainurl.'/public/media/');
    
// define path upto /servicedesk uploads
defined('SD_FILES_PATH') || define('SD_FILES_PATH', $domainurl.'/public/uploads/servicedesk/');

// define path upto /employeedocs uploads
defined('EMP_DOC_FILES_PATH') || define('EMP_DOC_FILES_PATH', $domainurl.'/public/uploads/employeedocs/');

// define path upto /announcements uploads
defined('CA_FILES_PATH') || define('CA_FILES_PATH', $domainurl.'/public/uploads/announcements/');
defined('CA_FILES_TEMP_PATH') || define('CA_FILES_TEMP_PATH', $domainurl.'/public/uploads/ca_temp/');

// define path upto /scripts
defined('SCRIPTS_PATH') || define('SCRIPTS_PATH', $domainurl.'/public/scripts/');    

// define main xml layout file name
defined('MAIN_XML_LAYOUT') || define('MAIN_XML_LAYOUT', 'main.xml');

// define default theme
defined('THEME_COMPANY') || define('THEME_COMPANY', 'default');
defined('THEME_NAME') || define('THEME_NAME', 'default');

defined('BASE_URL') || define('BASE_URL', $domainurl."/index.php/");
defined('DS') || define('DS', DIRECTORY_SEPARATOR);

// define path upto /public
defined('JQUERY_PATH') || define('JQUERY_PATH', $domainurl.'/public/scripts/jqgrid');

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    get_include_path(),
)));

defined('APP_LIBRARY_PATH') || define('APP_LIBRARY_PATH', APPLICATION_PATH . '/../library');

// for menu manager
defined('MENU_MANAGER') || define('MENU_MANAGER', ADMIN_PATH . 'menumanager/');

// real path for public -> media -> js
// Define path to application directory
defined('MEDIA_R_PATH') || define('MEDIA_R_PATH', realpath(dirname(__FILE__) . '/../public/media/'));


// Define path to Glitch directory
defined('GLITCH_LIB_PATH') || define('GLITCH_LIB_PATH', APP_LIBRARY_PATH );

// Define path to Glitch directory
defined('GLITCH_APP_ENV') || define('GLITCH_APP_ENV', 'development');

// Define path to Glitch directory
defined('GLITCH_CONFIGS_PATH') || define('GLITCH_CONFIGS_PATH', APP_LIBRARY_PATH . '/Glitch/Config/ini.php');
	
	
//Define Upload image path
defined('USER_UPLOAD_PATH') || define('USER_UPLOAD_PATH',realpath(dirname(__FILE__)).'/uploads/profile');

defined('USER_PREVIEW_UPLOAD_PATH') || define('USER_PREVIEW_UPLOAD_PATH', realpath(dirname(__FILE__)).'/uploads/preview');
defined('EMP_EXCEL_UPLOAD_PATH') || define('EMP_EXCEL_UPLOAD_PATH', realpath(dirname(__FILE__)).'/uploads/emp_excel');

// Define paths to upload files	
defined('UPLOAD_PATH_RESUMES') || define('UPLOAD_PATH_RESUMES', realpath(dirname(__FILE__)).'/uploads/resumes');	
defined('UPLOAD_PATH_FEEDBACK') || define('UPLOAD_PATH_FEEDBACK', realpath(dirname(__FILE__)).'/uploads/feedback');	

// Define Service Desk files
defined('SD_TEMP_UPLOAD_PATH') || define('SD_TEMP_UPLOAD_PATH', realpath(dirname(__FILE__)).'/uploads/sd_temp/');
defined('SD_UPLOAD_PATH') || define('SD_UPLOAD_PATH', realpath(dirname(__FILE__)).'/uploads/servicedesk/');

// Define Employee Documents
defined('EMP_DOC_TEMP_UPLOAD_PATH') || define('EMP_DOC_TEMP_UPLOAD_PATH', realpath(dirname(__FILE__)).'/uploads/emp_doc_temp/');
defined('EMP_DOC_UPLOAD_PATH') || define('EMP_DOC_UPLOAD_PATH', realpath(dirname(__FILE__)).'/uploads/employeedocs/');

// Define Company Announcements files
// Path of attachment files when Announcement saved as draft
defined('CA_TEMP_UPLOAD_PATH') || define('CA_TEMP_UPLOAD_PATH', realpath(dirname(__FILE__)).'/uploads/ca_temp/');
// Path of attachment files when Announcement posted 
defined('CA_UPLOAD_PATH') || define('CA_UPLOAD_PATH', realpath(dirname(__FILE__)).'/uploads/announcements/');

// Define policy Documents upload path
defined('POLICY_DOC_TEMP_UPLOAD_PATH') || define('POLICY_DOC_TEMP_UPLOAD_PATH', realpath(dirname(__FILE__)).'/uploads/policy_doc_temp/');
defined('POLICY_DOC_UPLOAD_PATH') || define('POLICY_DOC_UPLOAD_PATH', realpath(dirname(__FILE__)).'/uploads/policydocs/');
defined('POLICY_DOC_PATH') || define('POLICY_DOC_PATH', $domainurl.'/public/uploads/policydocs/');


//expenses receipts path
defined('EXPENSES_RECEIPTS_PATH') || define('EXPENSES_RECEIPTS_PATH', realpath(dirname(__FILE__)).'/uploads/expenses_receipts/');
defined('EXPENSES_RECEIPTS_TEMP_UPLOAD_PATH') || define('EXPENSES_RECEIPTS_TEMP_UPLOAD_PATH', realpath(dirname(__FILE__)).'/uploads/expense_receipt_temp/');

//assets images path
defined('ASSETS_IMAGES_PATH') || define('ASSETS_IMAGES_PATH', realpath(dirname(__FILE__)).'/uploads/assets_images_temp/');
defined('ASSETS_IMAGES_TEMP_PATH') || define('ASSETS_IMAGES_TEMP_PATH', realpath(dirname(__FILE__)).'/uploads/assets_images_temp/');
defined('ASSETS_IMAGES') || define('ASSETS_IMAGES', realpath(dirname(__FILE__)).'/uploads/assets_images/');
//assets qrcode image path
defined('ASSETS_QRCODE_PATH') || define('ASSETS_QRCODE_PATH', realpath(dirname(__FILE__)).'/uploads/assets/QR_Image/');
// Used in creating zip files while user download employee documents.
defined('DATE_CONSTANT') ? '' : define('DATE_CONSTANT', date('Y-m-d H:i:s'));

$paths = array(
	APP_LIBRARY_PATH,
	get_include_path()
);
$os = PHP_OS;
switch($os)
{
    case "Linux": define("SEPARATOR", "/"); break;
    case "Windows": define("SEPARATOR", "\\"); break;
    case "WINNT": define("SEPARATOR", "\\"); break;
    default: define("SEPARATOR", "/"); break;
}

//for time management

defined('MANAGE_MODULE_ARRAY')|| define('MANAGE_MODULE_ARRAY', serialize (array(TIMEMANAGEMENT,RESOURCEREQUISITION,BGCHECKS,REPORTS,SERVICEDESK,PERFORMANCEAPPRAISAL)));

defined('TIMEMANAGEMENT_DELETE')|| define('TIMEMANAGEMENT_DELETE', 'time management');
/**
 * TIME MANAGEMENT CONSTANTS GOES FROM HERE 
 * =========================================
 */

// define path upto /public
defined('TM_MEDIA_PATH') || define('TM_MEDIA_PATH', $domainurl.'/public/media/timemanagement/');
defined('EXPENSES_MEDIA_PATH') || define('EXPENSES_MEDIA_PATH', $domainurl.'/public/media/expenses/');
defined('ASSETS_MEDIA_PATH') || define('ASSETS_MEDIA_PATH', $domainurl.'/public/media/assets/');
defined('DEFAULTTASKS_DELETE')|| define('DEFAULTTASKS_DELETE','default task'); 
defined('CLIENTS_DELETE')|| define('CLIENTS_DELETE','client'); 
defined('PROJECTS_DELETE')|| define('PROJECTS_DELETE','project');
defined('EXPENSECATEGORY_DELETE')|| define('EXPENSECATEGORY_DELETE','expense category');
defined('ASSETS_DELETE')|| define('ASSETS_DELETE','assets');
defined('EXPENSES_DELETE')|| define('EXPENSES_DELETE','expenses');
defined('PAYMENTMODE_DELETE')|| define('PAYMENTMODE_DELETE','paymentmode');
defined('EXPENSECATEGORIES_DELETE')|| define('EXPENSECATEGORIES_DELETE','categories');
defined('EMPLOYEEADVANCES_DELETE')|| define('EMPLOYEEADVANCES_DELETE','employeeadvances');
defined('PROJECTTASKS_DELETE')|| define('PROJECTTASKS_DELETE','project tasks');
defined('PROJECTRESOURCES_DELETE')|| define('PROJECTRESOURCES_DELETE','project resource'); 
defined('EMPLOYEEPROJECTS_DELETE')|| define('EMPLOYEEPROJECTS_DELETE','employee projects delete'); 
defined('LEADPROJECTS_DELETE')|| define('LEADPROJECTS_DELETE','lead projects delete'); 
defined('EXIT_MEDIA_PATH') || define('EXIT_MEDIA_PATH', $domainurl.'/public/media/exit/');
//==========================END FOR TIME MANAGEMENT CONSTANTS=============================
/**
 * EXPENSES CONSTANTS GOES FROM HERE 
 * =========================================
 */
defined('TRIPS_DELETE')|| define('TRIPS_DELETE','trip'); 
//==========================END FOR EXPENSES CONSTANTS=============================
/**
 * EXPENSES CONSTANTS GOES FROM HERE 
 * =========================================
 */
defined('ASSETS_DELETE')|| define('ASSETS_DELETE','asset category'); 
//==========================END FOR EXPENSES CONSTANTS=============================


defined('MYEMPLOYEEEXPENSES_DELETE')|| define('MYEMPLOYEEEXPENSES_DELETE','employee expenses'); 
 

