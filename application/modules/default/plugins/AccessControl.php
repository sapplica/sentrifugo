<?php
class Default_Plugin_AccessControl extends Zend_Controller_Plugin_Abstract
{
  private $_acl,$id_param;
          
  public function preDispatch(Zend_Controller_Request_Abstract $request)
  {
  	$storage = new Zend_Auth_Storage_Session();
        $data = $storage->read();
        $role = $data['emprole'];
        
        if($role == 1)
            $role = 'admin';
        else if($role == 2)
	 $role = 'management';
	else if($role == 3)
	 $role = 'Manager';
	else if($role == 4)
	 $role = 'HRM';
	else if($role == 5)
	 $role = 'Employee';
	else if($role == 6)
	 $role = 'External';
	else if($role == 7)
	 $role = 'Sysadmin';
	else if($role == 8)
	 $role = 'Employees';
	
		  
  	$request->getModuleName();
        $request->getControllerName();
        $request->getActionName();
    	
        
        $module = $request->getModuleName();
	$resource = $request->getControllerName();
	$privilege = $request->getActionName();
	$this->id_param = $request->getParam('id');
	$allowed = false;
        $acl = $this->_getAcl();
	$moduleResource = "$module:$resource";
	
	if($resource == 'profile')
            $role = 'viewer';
		
	if($resource == 'services')
            $role = 'services';
		
	if($role != '') 
        {
            if ($acl->has($moduleResource)) 
            {						
                $allowed = $acl->isAllowed($role, $moduleResource, $privilege);	
			    	 
            }	 
            if (!$allowed)//  && $role !='admin') 
            {				
                $request->setControllerName('error');
	        $request->setActionName('error');
            }
	}
  }
  
protected function _getAcl()
{
    if ($this->_acl == null ) 
    {
           $acl = new Zend_Acl();

           $acl->addRole('admin');            
           $acl->addRole('viewer');            
           
	 $acl->addRole('management');
	 $acl->addRole('Manager');
	 $acl->addRole('HRM');
	 $acl->addRole('Employee');
	 $acl->addRole('External');
	 $acl->addRole('Sysadmin');
	 $acl->addRole('Employees');
           $storage = new Zend_Auth_Storage_Session();
           $data = $storage->read();
           $role = $data['emprole'];

           $acl->addResource(new Zend_Acl_Resource('login:index'));	
           $acl->allow('viewer', 'login:index', array('index','confirmlink','forgotpassword','forgotsuccess','login','pass','browserfailure','forcelogout','useractivation'));

           if($role == 1 ) 
           {				 		    	
                   
		 $acl->addResource(new Zend_Acl_Resource('default:accountclasstype'));
                    $acl->allow('admin', 'default:accountclasstype', array('index','view','edit','addpopup','saveupdate','delete'));

		 $acl->addResource(new Zend_Acl_Resource('default:agencylist'));
                    $acl->allow('admin', 'default:agencylist', array('index','add','view','edit','delete','deletepoc'));

		 $acl->addResource(new Zend_Acl_Resource('default:approvedleaves'));
                    $acl->allow('admin', 'default:approvedleaves', array('index','view','delete'));

		 $acl->addResource(new Zend_Acl_Resource('default:approvedrequisitions'));
                    $acl->allow('admin', 'default:approvedrequisitions', array('index','view','edit'));

		 $acl->addResource(new Zend_Acl_Resource('default:attendancestatuscode'));
                    $acl->allow('admin', 'default:attendancestatuscode', array('index','view','edit','delete'));

		 $acl->addResource(new Zend_Acl_Resource('default:bankaccounttype'));
                    $acl->allow('admin', 'default:bankaccounttype', array('index','view','edit','addpopup','delete'));

		 $acl->addResource(new Zend_Acl_Resource('default:bgscreeningtype'));
                    $acl->allow('admin', 'default:bgscreeningtype', array('index','view','edit','add','delete','addpopup'));

		 $acl->addResource(new Zend_Acl_Resource('default:businessunits'));
                    $acl->allow('admin', 'default:businessunits', array('index','edit','view','delete','getdeptnames'));

		 $acl->addResource(new Zend_Acl_Resource('default:cancelleaves'));
                    $acl->allow('admin', 'default:cancelleaves', array('index','view','delete'));

		 $acl->addResource(new Zend_Acl_Resource('default:candidatedetails'));
                    $acl->allow('admin', 'default:candidatedetails', array('index','view','edit','add','delete','chkcandidate','uploadfile','deleteresume','download','multipleresume'));

		 $acl->addResource(new Zend_Acl_Resource('default:cities'));
                    $acl->allow('admin', 'default:cities', array('index','view','edit','delete','getcitiescand','addpopup','addnewcity'));

		 $acl->addResource(new Zend_Acl_Resource('default:competencylevel'));
                    $acl->allow('admin', 'default:competencylevel', array('index','view','edit','addpopup','delete'));

		 $acl->addResource(new Zend_Acl_Resource('default:countries'));
                    $acl->allow('admin', 'default:countries', array('index','view','edit','saveupdate','delete','getcountrycode','addpopup','addnewcountry'));

		 $acl->addResource(new Zend_Acl_Resource('default:currency'));
                    $acl->allow('admin', 'default:currency', array('index','view','edit','addpopup','delete','gettargetcurrency'));

		 $acl->addResource(new Zend_Acl_Resource('default:currencyconverter'));
                    $acl->allow('admin', 'default:currencyconverter', array('index','add','view','edit','delete'));

		 $acl->addResource(new Zend_Acl_Resource('default:dashboard'));
                    $acl->allow('admin', 'default:dashboard', array('index','saveuserdashboard','getwidgtes','emailsettings','changepassword','editpassword','update','uploadpreview','viewprofile','viewsettings','savemenuwidgets','getmenuname','fetchmenuname','getnavids','getopeningpositondate','menuwork'));

		 $acl->addResource(new Zend_Acl_Resource('default:departments'));
                    $acl->allow('admin', 'default:departments', array('index','view','viewpopup','edit','editpopup','getdepartments','delete','getempnames'));

		 $acl->addResource(new Zend_Acl_Resource('default:educationlevelcode'));
                    $acl->allow('admin', 'default:educationlevelcode', array('index','view','edit','delete'));

		 $acl->addResource(new Zend_Acl_Resource('default:eeoccategory'));
                    $acl->allow('admin', 'default:eeoccategory', array('index','view','edit','delete'));

		 $acl->addResource(new Zend_Acl_Resource('default:emailcontacts'));
                    $acl->allow('admin', 'default:emailcontacts', array('index','add','edit','getgroupoptions','view','delete','getmailcnt'));

		 $acl->addResource(new Zend_Acl_Resource('default:empconfiguration'));
                    $acl->allow('admin', 'default:empconfiguration', array('index','edit','add'));

		 $acl->addResource(new Zend_Acl_Resource('default:empleavesummary'));
                    $acl->allow('admin', 'default:empleavesummary', array('index','statusid','view','delete'));

		 $acl->addResource(new Zend_Acl_Resource('default:employee'));
                    $acl->allow('admin', 'default:employee', array('getemprequi','index','add','edit','view','getdepartments','getpositions','delete','getempreportingmanagers','makeactiveinactive','changereportingmanager','addemppopup'));

		 $acl->addResource(new Zend_Acl_Resource('default:employeeleavetypes'));
                    $acl->allow('admin', 'default:employeeleavetypes', array('index','view','edit','delete'));

		 $acl->addResource(new Zend_Acl_Resource('default:employmentstatus'));
                    $acl->allow('admin', 'default:employmentstatus', array('index','view','edit','delete','addpopup'));

		 $acl->addResource(new Zend_Acl_Resource('default:empscreening'));
                    $acl->allow('admin', 'default:empscreening', array('index','add','edit','view','getemployeedata','getagencylist','getpocdata','forcedfullupdate','delete','checkscreeningstatus','uploadfeedback','download','deletefeedback'));

		 $acl->addResource(new Zend_Acl_Resource('default:ethniccode'));
                    $acl->allow('admin', 'default:ethniccode', array('index','view','edit','saveupdate','delete','addpopup'));

		 $acl->addResource(new Zend_Acl_Resource('default:gender'));
                    $acl->allow('admin', 'default:gender', array('index','view','edit','saveupdate','delete','addpopup'));

		 $acl->addResource(new Zend_Acl_Resource('default:geographygroup'));
                    $acl->allow('admin', 'default:geographygroup', array('index','view','edit','delete'));

		 $acl->addResource(new Zend_Acl_Resource('default:heirarchy'));
                    $acl->allow('admin', 'default:heirarchy', array('index','edit','addlist','editlist','saveadddata','saveeditdata','deletelist','editActi','deletelist_1'));

		 $acl->addResource(new Zend_Acl_Resource('default:holidaydates'));
                    $acl->allow('admin', 'default:holidaydates', array('index','add','addpopup','view','viewpopup','edit','editpopup','delete'));

		 $acl->addResource(new Zend_Acl_Resource('default:holidaygroups'));
                    $acl->allow('admin', 'default:holidaygroups', array('index','add','view','edit','delete','getempnames','getholidaynames','addpopup'));

		 $acl->addResource(new Zend_Acl_Resource('default:identitycodes'));
                    $acl->allow('admin', 'default:identitycodes', array('index','add','addpopup','edit'));

		 $acl->addResource(new Zend_Acl_Resource('default:identitydocuments'));
                    $acl->allow('admin', 'default:identitydocuments', array('index','add','view','edit'));

		 $acl->addResource(new Zend_Acl_Resource('default:index'));
                    $acl->allow('admin', 'default:index', array('index','loginpopupsave','welcome','logout','clearsessionarray','forcelogout','browserfailure','sendpassword','updatecontactnumber','getstates','getstatesnormal','getcities','getcitiesnormal','getdepartments','getpositions','gettargetcurrency','calculatedays','calculatebusinessdays','calculatecalendardays','fromdatetodate','fromdatetodateorg','medicalclaimdates','gettimeformat','chkcurrenttime','popup','createorremoveshortcut','sessiontour','getissuingauthority','setsessionval','checkisactivestatus'));

		 $acl->addResource(new Zend_Acl_Resource('default:jobtitles'));
                    $acl->allow('admin', 'default:jobtitles', array('index','view','edit','addpopup','delete'));

		 $acl->addResource(new Zend_Acl_Resource('default:language'));
                    $acl->allow('admin', 'default:language', array('index','view','edit','delete','addpopup'));

		 $acl->addResource(new Zend_Acl_Resource('default:leavemanagement'));
                    $acl->allow('admin', 'default:leavemanagement', array('index','add','view','edit','delete'));

		 $acl->addResource(new Zend_Acl_Resource('default:leaverequest'));
                    $acl->allow('admin', 'default:leaverequest', array('index','saveleaverequestdetails','gethalfdaydetails'));

		 $acl->addResource(new Zend_Acl_Resource('default:licensetype'));
                    $acl->allow('admin', 'default:licensetype', array('index','view','edit','delete'));

		 $acl->addResource(new Zend_Acl_Resource('default:managemenus'));
                    $acl->allow('admin', 'default:managemenus', array('index','save'));

		 $acl->addResource(new Zend_Acl_Resource('default:manageremployeevacations'));
                    $acl->allow('admin', 'default:manageremployeevacations', array('index','view','edit','delete'));

		 $acl->addResource(new Zend_Acl_Resource('default:maritalstatus'));
                    $acl->allow('admin', 'default:maritalstatus', array('index','view','edit','saveupdate','delete','addpopup'));

		 $acl->addResource(new Zend_Acl_Resource('default:militaryservice'));
                    $acl->allow('admin', 'default:militaryservice', array('index','view','edit','delete'));

		 $acl->addResource(new Zend_Acl_Resource('default:mydetails'));
                    $acl->allow('admin', 'default:mydetails', array('index','personaldetailsview','personal','communicationdetailsview','communication','skills','education','experience','leaves','holidays','salarydetailsview','salarydetails','certification','creditcarddetailsview','creditcard','visadetailsview','visa','medicalclaims','disabilitydetailsview','disability','dependency','workeligibilitydetailsview','workeligibility','additionaldetailsedit','jobhistory','delete'));

		 $acl->addResource(new Zend_Acl_Resource('default:myemployees'));
                    $acl->allow('admin', 'default:myemployees', array('index','view','perview','comview','skillsview','expview','eduview','trainingview','additionaldetailsview','jobhistoryview'));

		 $acl->addResource(new Zend_Acl_Resource('default:myholidaycalendar'));
                    $acl->allow('admin', 'default:myholidaycalendar', array('index','view','delete'));

		 $acl->addResource(new Zend_Acl_Resource('default:nationality'));
                    $acl->allow('admin', 'default:nationality', array('index','view','edit','delete','addpopup'));

		 $acl->addResource(new Zend_Acl_Resource('default:nationalitycontextcode'));
                    $acl->allow('admin', 'default:nationalitycontextcode', array('index','view','edit','delete'));

		 $acl->addResource(new Zend_Acl_Resource('default:numberformats'));
                    $acl->allow('admin', 'default:numberformats', array('index','add','view','edit','delete'));

		 $acl->addResource(new Zend_Acl_Resource('default:organisationinfo'));
                    $acl->allow('admin', 'default:organisationinfo', array('index','edit','saveupdate','uploadpreview','validateorgstartdate','getcompleteorgdata','addorghead'));

		 $acl->addResource(new Zend_Acl_Resource('default:payfrequency'));
                    $acl->allow('admin', 'default:payfrequency', array('index','addpopup','view','edit','delete'));

		 $acl->addResource(new Zend_Acl_Resource('default:pendingleaves'));
                    $acl->allow('admin', 'default:pendingleaves', array('index','view','delete'));

		 $acl->addResource(new Zend_Acl_Resource('default:positions'));
                    $acl->allow('admin', 'default:positions', array('index','add','view','edit','addpopup','delete'));

		 $acl->addResource(new Zend_Acl_Resource('default:prefix'));
                    $acl->allow('admin', 'default:prefix', array('index','view','edit','saveupdate','delete','addpopup'));

		 $acl->addResource(new Zend_Acl_Resource('default:racecode'));
                    $acl->allow('admin', 'default:racecode', array('index','view','edit','saveupdate','delete','addpopup'));

		 $acl->addResource(new Zend_Acl_Resource('default:rejectedleaves'));
                    $acl->allow('admin', 'default:rejectedleaves', array('index','view','delete'));

		 $acl->addResource(new Zend_Acl_Resource('default:rejectedrequisitions'));
                    $acl->allow('admin', 'default:rejectedrequisitions', array('index','view'));

		 $acl->addResource(new Zend_Acl_Resource('default:remunerationbasis'));
                    $acl->allow('admin', 'default:remunerationbasis', array('index','view','edit','delete'));

		 $acl->addResource(new Zend_Acl_Resource('default:reports'));
                    $acl->allow('admin', 'default:reports', array('getrolepopup','emprolesgrouppopup','getinterviewroundsdata','interviewrounds','rolesgroup','exportemprolesgroup','exportrolesgroupreport','exportinterviewrpt','exportactiveuserrpt','exportemployeereport','rolesgrouprptpdf','activeuserrptpdf','emprptpdf','interviewrptpdf','rolesgroupdata','emprolesgroup','emprolesgroupdata','activeuser','getactiveuserdata','getempreportdata','empauto','employeereport','index','holidaygroupreports','getpdfreportholiday','getexcelreportholiday','leavesreport','getpdfreportleaves','getexcelreportleaves','leavesreporttabheader','leavemanagementreport','getpdfreportleavemanagement','getexcelreportleavemanagement','bunitauto','bunitcodeauto','getexcelreportbusinessunit','getbusinessunitspdf','businessunits','userlogreport','departments','exportdepartmentpdf','getexcelreportdepartment','candidaterptexcel','candidaterptpdf','getcandidatesreportdata','candidatesreport','requisitionauto','requisitionrptexcel','requisitionrptpdf','getrequisitionsstatusreportdata','requisitionstatusreport','activitylogreport','downloadreport','agencylistreport','agencynameauto','agencysebsiteauto','empscreening','getspecimennames','getagencynames','getexcelreportempscreening','getempscreeningpdf'));

		 $acl->addResource(new Zend_Acl_Resource('default:requisition'));
                    $acl->allow('admin', 'default:requisition', array('index','add','edit','viewhr','approverequisition','addcandidate','interview','getdepartments','getpositions','viewpopup','view','delete','getapprreqdata','chkreqforclose','getempreportingmanagers','getemailcount','getapprovers'));

		 $acl->addResource(new Zend_Acl_Resource('default:roles'));
                    $acl->allow('admin', 'default:roles', array('index','view','edit','saveupdate','delete','getgroupmenu'));

		 $acl->addResource(new Zend_Acl_Resource('default:scheduleinterviews'));
                    $acl->allow('admin', 'default:scheduleinterviews', array('candidatepopup','index','view','add','downloadresume','edit','getcandidates','delete'));

		 $acl->addResource(new Zend_Acl_Resource('default:shortlistedcandidates'));
                    $acl->allow('admin', 'default:shortlistedcandidates', array('index','edit','view','add','delete'));

		 $acl->addResource(new Zend_Acl_Resource('default:sitepreference'));
                    $acl->allow('admin', 'default:sitepreference', array('index','add','view','edit','delete'));

		 $acl->addResource(new Zend_Acl_Resource('default:states'));
                    $acl->allow('admin', 'default:states', array('index','view','edit','delete','getstates','getstatescand','addpopup','addnewstate'));

		 $acl->addResource(new Zend_Acl_Resource('default:structure'));
                    $acl->allow('admin', 'default:structure', array('index'));

		 $acl->addResource(new Zend_Acl_Resource('default:timezone'));
                    $acl->allow('admin', 'default:timezone', array('index','view','edit','saveupdate','delete','addpopup'));

		 $acl->addResource(new Zend_Acl_Resource('default:usermanagement'));
                    $acl->allow('admin', 'default:usermanagement', array('index','view','edit','saveupdate','delete','getemailofuser'));

		 $acl->addResource(new Zend_Acl_Resource('default:veteranstatus'));
                    $acl->allow('admin', 'default:veteranstatus', array('index','view','edit','delete'));

		 $acl->addResource(new Zend_Acl_Resource('default:workeligibilitydoctypes'));
                    $acl->allow('admin', 'default:workeligibilitydoctypes', array('index','view','edit','addpopup','delete'));

		 $acl->addResource(new Zend_Acl_Resource('default:processes'));
                    $acl->allow('admin', 'default:processes', array('index','addpopup','editpopup','viewpopup','delete','savecomments','displaycomments','savefeedback'));

		 $acl->addResource(new Zend_Acl_Resource('default:interviewrounds'));
                    $acl->allow('admin', 'default:interviewrounds', array('index','addpopup','editpopup','viewpopup','delete'));

		 $acl->addResource(new Zend_Acl_Resource('default:empperformanceappraisal'));
                    $acl->allow('admin', 'default:empperformanceappraisal', array('index','edit','view'));

		 $acl->addResource(new Zend_Acl_Resource('default:emppayslips'));
                    $acl->allow('admin', 'default:emppayslips', array('index','edit','view'));

		 $acl->addResource(new Zend_Acl_Resource('default:empbenefits'));
                    $acl->allow('admin', 'default:empbenefits', array('index','edit','view'));

		 $acl->addResource(new Zend_Acl_Resource('default:emprequisitiondetails'));
                    $acl->allow('admin', 'default:emprequisitiondetails', array('index','edit','view'));

		 $acl->addResource(new Zend_Acl_Resource('default:empremunerationdetails'));
                    $acl->allow('admin', 'default:empremunerationdetails', array('index','edit','view'));

		 $acl->addResource(new Zend_Acl_Resource('default:empsecuritycredentials'));
                    $acl->allow('admin', 'default:empsecuritycredentials', array('index','edit','view'));

		 $acl->addResource(new Zend_Acl_Resource('default:apprreqcandidates'));
                    $acl->allow('admin', 'default:apprreqcandidates', array('index','viewpopup'));

		 $acl->addResource(new Zend_Acl_Resource('default:emppersonaldetails'));
                    $acl->allow('admin', 'default:emppersonaldetails', array('index','view','edit'));

		 $acl->addResource(new Zend_Acl_Resource('default:empcommunicationdetails'));
                    $acl->allow('admin', 'default:empcommunicationdetails', array('index','view','viewAction_27','edit'));

		 $acl->addResource(new Zend_Acl_Resource('default:trainingandcertificationdetails'));
                    $acl->allow('admin', 'default:trainingandcertificationdetails', array('index','edit','view','addpopup','editpopup','viewpopup','delete'));

		 $acl->addResource(new Zend_Acl_Resource('default:experiencedetails'));
                    $acl->allow('admin', 'default:experiencedetails', array('index','edit','view','addpopup','editpopup','viewpopup','delete'));

		 $acl->addResource(new Zend_Acl_Resource('default:educationdetails'));
                    $acl->allow('admin', 'default:educationdetails', array('index','edit','view','addpopup','viewpopup','editpopup','delete'));

		 $acl->addResource(new Zend_Acl_Resource('default:medicalclaims'));
                    $acl->allow('admin', 'default:medicalclaims', array('index','edit','addpopup','viewpopup','editpopup','delete','view'));

		 $acl->addResource(new Zend_Acl_Resource('default:empleaves'));
                    $acl->allow('admin', 'default:empleaves', array('index','edit','view','viewpopup'));

		 $acl->addResource(new Zend_Acl_Resource('default:empskills'));
                    $acl->allow('admin', 'default:empskills', array('index','edit','view','addpopup','viewpopup','editpopup','delete'));

		 $acl->addResource(new Zend_Acl_Resource('default:disabilitydetails'));
                    $acl->allow('admin', 'default:disabilitydetails', array('index','add','edit','view'));

		 $acl->addResource(new Zend_Acl_Resource('default:workeligibilitydetails'));
                    $acl->allow('admin', 'default:workeligibilitydetails', array('index','add','edit','view'));

		 $acl->addResource(new Zend_Acl_Resource('default:empadditionaldetails'));
                    $acl->allow('admin', 'default:empadditionaldetails', array('index','edit','view','addpopup','viewpopup','editpopup','delete'));

		 $acl->addResource(new Zend_Acl_Resource('default:visaandimmigrationdetails'));
                    $acl->allow('admin', 'default:visaandimmigrationdetails', array('index','add','edit','view'));

		 $acl->addResource(new Zend_Acl_Resource('default:creditcarddetails'));
                    $acl->allow('admin', 'default:creditcarddetails', array('index','add','edit','view'));

		 $acl->addResource(new Zend_Acl_Resource('default:dependencydetails'));
                    $acl->allow('admin', 'default:dependencydetails', array('index','edit','view','addpopup','editpopup','viewpopup','delete'));

		 $acl->addResource(new Zend_Acl_Resource('default:empholidays'));
                    $acl->allow('admin', 'default:empholidays', array('index','edit','view','viewpopup'));

		 $acl->addResource(new Zend_Acl_Resource('default:empjobhistory'));
                    $acl->allow('admin', 'default:empjobhistory', array('index','edit','view','addpopup','viewpopup','editpopup','delete'));

		 $acl->addResource(new Zend_Acl_Resource('default:empsalarydetails'));
                    $acl->allow('admin', 'default:empsalarydetails', array('index','edit','view'));

		 $acl->addResource(new Zend_Acl_Resource('default:logmanager'));
                    $acl->allow('admin', 'default:logmanager', array('index','view','empnamewithidauto'));

		 $acl->addResource(new Zend_Acl_Resource('default:userloginlog'));
                    $acl->allow('admin', 'default:userloginlog', array('index','empnameauto','empidauto','empipaddressauto','empemailauto'));
			   		  	   				   
           }  
           if($role == 2 )
           {
		 $acl->addResource(new Zend_Acl_Resource('default:accountclasstype'));
                            $acl->allow('management', 'default:accountclasstype', array('index','addpopup','saveupdate','add','edit','delete','view','Account Class Types '));

		 $acl->addResource(new Zend_Acl_Resource('default:agencylist'));
                            $acl->allow('management', 'default:agencylist', array('index','deletepoc','add','edit','delete','view','Agencies'));

		 $acl->addResource(new Zend_Acl_Resource('default:approvedleaves'));
                            $acl->allow('management', 'default:approvedleaves', array('index','view','Approved Leaves'));

		 $acl->addResource(new Zend_Acl_Resource('default:approvedrequisitions'));
                            $acl->allow('management', 'default:approvedrequisitions', array('index','edit','view','Approved Requisitions'));

		 $acl->addResource(new Zend_Acl_Resource('default:bgscreeningtype'));
                            $acl->allow('management', 'default:bgscreeningtype', array('index','addpopup','add','edit','delete','view','Screening Types'));

		 $acl->addResource(new Zend_Acl_Resource('default:businessunits'));
                            $acl->allow('management', 'default:businessunits', array('index','getdeptnames','add','edit','delete','view','Business Units'));

		 $acl->addResource(new Zend_Acl_Resource('default:cancelleaves'));
                            $acl->allow('management', 'default:cancelleaves', array('index','view','Cancelled Leaves'));

		 $acl->addResource(new Zend_Acl_Resource('default:candidatedetails'));
                            $acl->allow('management', 'default:candidatedetails', array('index','chkcandidate','uploadfile','deleteresume','download','multipleresume','add','edit','delete','view','CV Management'));

		 $acl->addResource(new Zend_Acl_Resource('default:cities'));
                            $cities_add = 'yes';
                                if($this->id_param == '' && $cities_add == 'yes')
                                    $acl->allow('management', 'default:cities', array('index','getcitiescand','addpopup','addnewcity','add','delete','view','Cities','edit'));

                                else
                                    $acl->allow('management', 'default:cities', array('index','getcitiescand','addpopup','addnewcity','add','delete','view','Cities'));

                                
		 $acl->addResource(new Zend_Acl_Resource('default:countries'));
                            $acl->allow('management', 'default:countries', array('index','saveupdate','getcountrycode','addpopup','addnewcountry','add','edit','delete','view','Countries'));

		 $acl->addResource(new Zend_Acl_Resource('default:currency'));
                            $acl->allow('management', 'default:currency', array('index','addpopup','gettargetcurrency','add','edit','delete','view','Currencies'));

		 $acl->addResource(new Zend_Acl_Resource('default:currencyconverter'));
                            $acl->allow('management', 'default:currencyconverter', array('index','add','edit','delete','view','Currency Conversions'));

		 $acl->addResource(new Zend_Acl_Resource('default:dashboard'));
                        $acl->allow('management', 'default:dashboard', array('index','saveuserdashboard','getwidgtes','emailsettings','changepassword','editpassword','update','uploadpreview','viewprofile','viewsettings','savemenuwidgets','getmenuname','fetchmenuname','getnavids','getopeningpositondate','menuwork'));

		 $acl->addResource(new Zend_Acl_Resource('default:departments'));
                            $acl->allow('management', 'default:departments', array('index','viewpopup','editpopup','getdepartments','getempnames','add','edit','delete','view','Departments'));

		 $acl->addResource(new Zend_Acl_Resource('default:emailcontacts'));
                            $acl->allow('management', 'default:emailcontacts', array('index','getgroupoptions','getmailcnt','add','edit','delete','view','Email Contacts'));

		 $acl->addResource(new Zend_Acl_Resource('default:empleavesummary'));
                            $acl->allow('management', 'default:empleavesummary', array('index','statusid','view','Employee Leaves Summary'));

		 $acl->addResource(new Zend_Acl_Resource('default:employee'));
                            $acl->allow('management', 'default:employee', array('getemprequi','index','getdepartments','getpositions','getempreportingmanagers','makeactiveinactive','changereportingmanager','addemppopup','add','edit','view','Employees'));

		 $acl->addResource(new Zend_Acl_Resource('default:empscreening'));
                            $acl->allow('management', 'default:empscreening', array('index','getemployeedata','getagencylist','getpocdata','forcedfullupdate','checkscreeningstatus','uploadfeedback','download','deletefeedback','add','edit','delete','view','Employee/Candidate Screening'));

		 $acl->addResource(new Zend_Acl_Resource('default:ethniccode'));
                            $acl->allow('management', 'default:ethniccode', array('index','saveupdate','addpopup','add','edit','delete','view','Ethnic Codes'));

		 $acl->addResource(new Zend_Acl_Resource('default:gender'));
                            $acl->allow('management', 'default:gender', array('index','saveupdate','addpopup','add','edit','delete','view','Gender'));

		 $acl->addResource(new Zend_Acl_Resource('default:geographygroup'));
                            $acl->allow('management', 'default:geographygroup', array('index','add','edit','delete','view','Geo Groups'));

		 $acl->addResource(new Zend_Acl_Resource('default:heirarchy'));
                            $acl->allow('management', 'default:heirarchy', array('index','addlist','editlist','saveadddata','saveeditdata','deletelist','editActi','deletelist_1','Organization Hierarchy'));

		 $acl->addResource(new Zend_Acl_Resource('default:holidaydates'));
                            $acl->allow('management', 'default:holidaydates', array('index','addpopup','viewpopup','editpopup','add','edit','delete','view','Manage Holidays'));

		 $acl->addResource(new Zend_Acl_Resource('default:holidaygroups'));
                            $acl->allow('management', 'default:holidaygroups', array('index','getempnames','getholidaynames','addpopup','add','edit','delete','view','Manage Holiday Group'));

		 $acl->addResource(new Zend_Acl_Resource('default:identitycodes'));
                            $acl->allow('management', 'default:identitycodes', array('index','addpopup','edit','Identity Codes'));

		 $acl->addResource(new Zend_Acl_Resource('default:index'));
                        $acl->allow('management', 'default:index', array('index','loginpopupsave','welcome','logout','clearsessionarray','forcelogout','browserfailure','sendpassword','updatecontactnumber','getstates','getstatesnormal','getcities','getcitiesnormal','getdepartments','getpositions','gettargetcurrency','calculatedays','calculatebusinessdays','calculatecalendardays','fromdatetodate','fromdatetodateorg','medicalclaimdates','gettimeformat','chkcurrenttime','popup','createorremoveshortcut','sessiontour','getissuingauthority','setsessionval','checkisactivestatus'));

		 $acl->addResource(new Zend_Acl_Resource('default:leavemanagement'));
                            $acl->allow('management', 'default:leavemanagement', array('index','add','edit','delete','view','Leave Management Options'));

		 $acl->addResource(new Zend_Acl_Resource('default:leaverequest'));
                            $leaverequest_add = 'yes';
                                if($this->id_param == '' && $leaverequest_add == 'yes')
                                    $acl->allow('management', 'default:leaverequest', array('index','saveleaverequestdetails','gethalfdaydetails','add','Leave Request','edit'));

                                else
                                    $acl->allow('management', 'default:leaverequest', array('index','saveleaverequestdetails','gethalfdaydetails','add','Leave Request'));

                                
		 $acl->addResource(new Zend_Acl_Resource('default:licensetype'));
                            $acl->allow('management', 'default:licensetype', array('index','add','edit','delete','view','License Types'));

		 $acl->addResource(new Zend_Acl_Resource('default:manageremployeevacations'));
                            $acl->allow('management', 'default:manageremployeevacations', array('index','edit','view','Manage Employee Leaves'));

		 $acl->addResource(new Zend_Acl_Resource('default:maritalstatus'));
                            $acl->allow('management', 'default:maritalstatus', array('index','saveupdate','addpopup','add','edit','delete','view','Marital Status'));

		 $acl->addResource(new Zend_Acl_Resource('default:mydetails'));
                            $acl->allow('management', 'default:mydetails', array('index','personaldetailsview','personal','communicationdetailsview','communication','skills','education','experience','leaves','holidays','salarydetailsview','salarydetails','certification','creditcarddetailsview','creditcard','visadetailsview','visa','medicalclaims','disabilitydetailsview','disability','dependency','workeligibilitydetailsview','workeligibility','additionaldetailsedit','jobhistory','add','edit','delete','view','My Details'));

		 $acl->addResource(new Zend_Acl_Resource('default:myemployees'));
                            $acl->allow('management', 'default:myemployees', array('index','perview','comview','skillsview','expview','eduview','trainingview','additionaldetailsview','jobhistoryview','view','My Team'));

		 $acl->addResource(new Zend_Acl_Resource('default:myholidaycalendar'));
                            $acl->allow('management', 'default:myholidaycalendar', array('index','view','My Holiday Calendar'));

		 $acl->addResource(new Zend_Acl_Resource('default:nationality'));
                            $acl->allow('management', 'default:nationality', array('index','addpopup','add','edit','delete','view','Nationalities'));

		 $acl->addResource(new Zend_Acl_Resource('default:nationalitycontextcode'));
                            $acl->allow('management', 'default:nationalitycontextcode', array('index','add','edit','delete','view','Nationality Context Codes'));

		 $acl->addResource(new Zend_Acl_Resource('default:numberformats'));
                            $acl->allow('management', 'default:numberformats', array('index','add','edit','delete','view','Number Formats'));

		 $acl->addResource(new Zend_Acl_Resource('default:organisationinfo'));
                            $acl->allow('management', 'default:organisationinfo', array('index','saveupdate','uploadpreview','validateorgstartdate','getcompleteorgdata','addorghead','edit','view','Organization Info'));

		 $acl->addResource(new Zend_Acl_Resource('default:pendingleaves'));
                            $acl->allow('management', 'default:pendingleaves', array('index','delete','view','Pending Leaves'));

		 $acl->addResource(new Zend_Acl_Resource('default:prefix'));
                            $acl->allow('management', 'default:prefix', array('index','saveupdate','addpopup','add','edit','delete','view','Prefixes'));

		 $acl->addResource(new Zend_Acl_Resource('default:racecode'));
                            $acl->allow('management', 'default:racecode', array('index','saveupdate','addpopup','add','edit','delete','view','Race Codes'));

		 $acl->addResource(new Zend_Acl_Resource('default:rejectedleaves'));
                            $acl->allow('management', 'default:rejectedleaves', array('index','view','Rejected Leaves'));

		 $acl->addResource(new Zend_Acl_Resource('default:rejectedrequisitions'));
                            $acl->allow('management', 'default:rejectedrequisitions', array('index','view','Rejected Requisitions'));

		 $acl->addResource(new Zend_Acl_Resource('default:reports'));
                            $acl->allow('management', 'default:reports', array('getrolepopup','emprolesgrouppopup','getinterviewroundsdata','interviewrounds','rolesgroup','exportemprolesgroup','exportrolesgroupreport','exportinterviewrpt','exportactiveuserrpt','exportemployeereport','rolesgrouprptpdf','activeuserrptpdf','emprptpdf','interviewrptpdf','rolesgroupdata','emprolesgroup','emprolesgroupdata','activeuser','getactiveuserdata','getempreportdata','empauto','employeereport','index','holidaygroupreports','getpdfreportholiday','getexcelreportholiday','leavesreport','getpdfreportleaves','getexcelreportleaves','leavesreporttabheader','leavemanagementreport','getpdfreportleavemanagement','getexcelreportleavemanagement','bunitauto','bunitcodeauto','getexcelreportbusinessunit','getbusinessunitspdf','businessunits','userlogreport','departments','exportdepartmentpdf','getexcelreportdepartment','candidaterptexcel','candidaterptpdf','getcandidatesreportdata','candidatesreport','requisitionauto','requisitionrptexcel','requisitionrptpdf','getrequisitionsstatusreportdata','requisitionstatusreport','activitylogreport','downloadreport','agencylistreport','agencynameauto','agencysebsiteauto','empscreening','getspecimennames','getagencynames','getexcelreportempscreening','getempscreeningpdf','Reports'));

		 $acl->addResource(new Zend_Acl_Resource('default:requisition'));
                            $acl->allow('management', 'default:requisition', array('index','viewhr','approverequisition','addcandidate','interview','getdepartments','getpositions','viewpopup','getapprreqdata','chkreqforclose','getempreportingmanagers','getemailcount','getapprovers','add','edit','delete','view','Openings/Positions'));

		 $acl->addResource(new Zend_Acl_Resource('default:roles'));
                            $acl->allow('management', 'default:roles', array('index','saveupdate','getgroupmenu','add','edit','delete','view','Roles & Privileges'));

		 $acl->addResource(new Zend_Acl_Resource('default:scheduleinterviews'));
                            $acl->allow('management', 'default:scheduleinterviews', array('candidatepopup','index','downloadresume','getcandidates','add','edit','delete','view','Scheduled Interviews'));

		 $acl->addResource(new Zend_Acl_Resource('default:shortlistedcandidates'));
                            $acl->allow('management', 'default:shortlistedcandidates', array('index','edit','view','Shortlisted & Selected Candidates'));

		 $acl->addResource(new Zend_Acl_Resource('default:sitepreference'));
                            $acl->allow('management', 'default:sitepreference', array('index','view','add','edit','Site Preferences'));

		 $acl->addResource(new Zend_Acl_Resource('default:states'));
                            $states_add = 'yes';
                                if($this->id_param == '' && $states_add == 'yes')
                                    $acl->allow('management', 'default:states', array('index','getstates','getstatescand','addpopup','addnewstate','add','delete','view','States','edit'));

                                else
                                    $acl->allow('management', 'default:states', array('index','getstates','getstatescand','addpopup','addnewstate','add','delete','view','States'));

                                
		 $acl->addResource(new Zend_Acl_Resource('default:structure'));
                            $acl->allow('management', 'default:structure', array('index','Organization Structure'));

		 $acl->addResource(new Zend_Acl_Resource('default:timezone'));
                            $acl->allow('management', 'default:timezone', array('index','saveupdate','addpopup','add','edit','delete','view','Time Zones'));

		 $acl->addResource(new Zend_Acl_Resource('default:usermanagement'));
                            $acl->allow('management', 'default:usermanagement', array('index','saveupdate','getemailofuser','add','edit','view','Manage External Users'));

		 $acl->addResource(new Zend_Acl_Resource('default:processes'));
                            $acl->allow('management', 'default:processes', array('index','addpopup','editpopup','viewpopup','savecomments','displaycomments','savefeedback','index','addpopup','editpopup','viewpopup','delete','savecomments','displaycomments','savefeedback'));

		 $acl->addResource(new Zend_Acl_Resource('default:interviewrounds'));
                            $acl->allow('management', 'default:interviewrounds', array('index','addpopup','editpopup','viewpopup','index','addpopup','editpopup','viewpopup','delete'));

		 $acl->addResource(new Zend_Acl_Resource('default:empperformanceappraisal'));
                            $acl->allow('management', 'default:empperformanceappraisal', array('index','view','index','edit','view'));

		 $acl->addResource(new Zend_Acl_Resource('default:emppayslips'));
                            $acl->allow('management', 'default:emppayslips', array('index','view','index','edit','view'));

		 $acl->addResource(new Zend_Acl_Resource('default:empbenefits'));
                            $acl->allow('management', 'default:empbenefits', array('index','view','index','edit','view'));

		 $acl->addResource(new Zend_Acl_Resource('default:emprequisitiondetails'));
                            $acl->allow('management', 'default:emprequisitiondetails', array('index','view','index','edit','view'));

		 $acl->addResource(new Zend_Acl_Resource('default:empremunerationdetails'));
                            $acl->allow('management', 'default:empremunerationdetails', array('index','view','index','edit','view'));

		 $acl->addResource(new Zend_Acl_Resource('default:empsecuritycredentials'));
                            $acl->allow('management', 'default:empsecuritycredentials', array('index','view','index','edit','view'));

		 $acl->addResource(new Zend_Acl_Resource('default:apprreqcandidates'));
                            $acl->allow('management', 'default:apprreqcandidates', array('index','viewpopup','index','viewpopup'));

		 $acl->addResource(new Zend_Acl_Resource('default:emppersonaldetails'));
                            $acl->allow('management', 'default:emppersonaldetails', array('index','view','index','view','edit'));

		 $acl->addResource(new Zend_Acl_Resource('default:empcommunicationdetails'));
                            $acl->allow('management', 'default:empcommunicationdetails', array('index','view','viewAction_27','index','view','viewAction_27','edit'));

		 $acl->addResource(new Zend_Acl_Resource('default:trainingandcertificationdetails'));
                            $acl->allow('management', 'default:trainingandcertificationdetails', array('index','view','addpopup','editpopup','viewpopup','index','edit','view','addpopup','editpopup','viewpopup','delete'));

		 $acl->addResource(new Zend_Acl_Resource('default:experiencedetails'));
                            $acl->allow('management', 'default:experiencedetails', array('index','view','addpopup','editpopup','viewpopup','index','edit','view','addpopup','editpopup','viewpopup','delete'));

		 $acl->addResource(new Zend_Acl_Resource('default:educationdetails'));
                            $acl->allow('management', 'default:educationdetails', array('index','view','addpopup','viewpopup','editpopup','index','edit','view','addpopup','viewpopup','editpopup','delete'));

		 $acl->addResource(new Zend_Acl_Resource('default:medicalclaims'));
                            $acl->allow('management', 'default:medicalclaims', array('index','addpopup','viewpopup','editpopup','view','index','edit','addpopup','viewpopup','editpopup','delete','view'));

		 $acl->addResource(new Zend_Acl_Resource('default:empleaves'));
                            $acl->allow('management', 'default:empleaves', array('index','view','viewpopup','index','edit','view','viewpopup'));

		 $acl->addResource(new Zend_Acl_Resource('default:empskills'));
                            $acl->allow('management', 'default:empskills', array('index','view','addpopup','viewpopup','editpopup','index','edit','view','addpopup','viewpopup','editpopup','delete'));

		 $acl->addResource(new Zend_Acl_Resource('default:disabilitydetails'));
                            $acl->allow('management', 'default:disabilitydetails', array('index','view','index','add','edit','view'));

		 $acl->addResource(new Zend_Acl_Resource('default:workeligibilitydetails'));
                            $acl->allow('management', 'default:workeligibilitydetails', array('index','view','index','add','edit','view'));

		 $acl->addResource(new Zend_Acl_Resource('default:empadditionaldetails'));
                            $acl->allow('management', 'default:empadditionaldetails', array('index','view','addpopup','viewpopup','editpopup','index','edit','view','addpopup','viewpopup','editpopup','delete'));

		 $acl->addResource(new Zend_Acl_Resource('default:visaandimmigrationdetails'));
                            $acl->allow('management', 'default:visaandimmigrationdetails', array('index','view','index','add','edit','view'));

		 $acl->addResource(new Zend_Acl_Resource('default:creditcarddetails'));
                            $acl->allow('management', 'default:creditcarddetails', array('index','view','index','add','edit','view'));

		 $acl->addResource(new Zend_Acl_Resource('default:dependencydetails'));
                            $acl->allow('management', 'default:dependencydetails', array('index','view','addpopup','editpopup','viewpopup','index','edit','view','addpopup','editpopup','viewpopup','delete'));

		 $acl->addResource(new Zend_Acl_Resource('default:empholidays'));
                            $acl->allow('management', 'default:empholidays', array('index','view','viewpopup','index','edit','view','viewpopup'));

		 $acl->addResource(new Zend_Acl_Resource('default:empjobhistory'));
                            $acl->allow('management', 'default:empjobhistory', array('index','view','addpopup','viewpopup','editpopup','index','edit','view','addpopup','viewpopup','editpopup','delete'));

		 $acl->addResource(new Zend_Acl_Resource('default:empsalarydetails'));
                            $acl->allow('management', 'default:empsalarydetails', array('index','view','index','edit','view'));

		 $acl->addResource(new Zend_Acl_Resource('default:logmanager'));
                            $acl->allow('management', 'default:logmanager', array('index','view','empnamewithidauto','index','view','empnamewithidauto'));

		 $acl->addResource(new Zend_Acl_Resource('default:userloginlog'));
                            $acl->allow('management', 'default:userloginlog', array('index','empnameauto','empidauto','empipaddressauto','empemailauto','index','empnameauto','empidauto','empipaddressauto','empemailauto'));
}if($role == 3 )
           {
		 $acl->addResource(new Zend_Acl_Resource('default:approvedleaves'));
                            $acl->allow('Manager', 'default:approvedleaves', array('index','view','Approved Leaves'));

		 $acl->addResource(new Zend_Acl_Resource('default:approvedrequisitions'));
                            $acl->allow('Manager', 'default:approvedrequisitions', array('index','view','Approved Requisitions'));

		 $acl->addResource(new Zend_Acl_Resource('default:businessunits'));
                            $acl->allow('Manager', 'default:businessunits', array('index','getdeptnames','view','Business Units'));

		 $acl->addResource(new Zend_Acl_Resource('default:cancelleaves'));
                            $acl->allow('Manager', 'default:cancelleaves', array('index','view','Cancelled Leaves'));

		 $acl->addResource(new Zend_Acl_Resource('default:candidatedetails'));
                            $acl->allow('Manager', 'default:candidatedetails', array('index','chkcandidate','uploadfile','deleteresume','download','multipleresume','view','CV Management'));

		 $acl->addResource(new Zend_Acl_Resource('default:dashboard'));
                        $acl->allow('Manager', 'default:dashboard', array('index','saveuserdashboard','getwidgtes','emailsettings','changepassword','editpassword','update','uploadpreview','viewprofile','viewsettings','savemenuwidgets','getmenuname','fetchmenuname','getnavids','getopeningpositondate','menuwork'));

		 $acl->addResource(new Zend_Acl_Resource('default:departments'));
                            $acl->allow('Manager', 'default:departments', array('index','viewpopup','editpopup','getdepartments','getempnames','view','Departments'));

		 $acl->addResource(new Zend_Acl_Resource('default:employee'));
                            $acl->allow('Manager', 'default:employee', array('getemprequi','index','getdepartments','getpositions','getempreportingmanagers','makeactiveinactive','changereportingmanager','addemppopup','view','Employees'));

		 $acl->addResource(new Zend_Acl_Resource('default:heirarchy'));
                            $acl->allow('Manager', 'default:heirarchy', array('index','addlist','editlist','saveadddata','saveeditdata','deletelist','editActi','deletelist_1','Organization Hierarchy'));

		 $acl->addResource(new Zend_Acl_Resource('default:index'));
                        $acl->allow('Manager', 'default:index', array('index','loginpopupsave','welcome','logout','clearsessionarray','forcelogout','browserfailure','sendpassword','updatecontactnumber','getstates','getstatesnormal','getcities','getcitiesnormal','getdepartments','getpositions','gettargetcurrency','calculatedays','calculatebusinessdays','calculatecalendardays','fromdatetodate','fromdatetodateorg','medicalclaimdates','gettimeformat','chkcurrenttime','popup','createorremoveshortcut','sessiontour','getissuingauthority','setsessionval','checkisactivestatus'));

		 $acl->addResource(new Zend_Acl_Resource('default:leaverequest'));
                            $leaverequest_add = 'yes';
                                if($this->id_param == '' && $leaverequest_add == 'yes')
                                    $acl->allow('Manager', 'default:leaverequest', array('index','saveleaverequestdetails','gethalfdaydetails','add','Leave Request','edit'));

                                else
                                    $acl->allow('Manager', 'default:leaverequest', array('index','saveleaverequestdetails','gethalfdaydetails','add','Leave Request'));

                                
		 $acl->addResource(new Zend_Acl_Resource('default:manageremployeevacations'));
                            $acl->allow('Manager', 'default:manageremployeevacations', array('index','edit','view','Manage Employee Leaves'));

		 $acl->addResource(new Zend_Acl_Resource('default:mydetails'));
                            $acl->allow('Manager', 'default:mydetails', array('index','personaldetailsview','personal','communicationdetailsview','communication','skills','education','experience','leaves','holidays','salarydetailsview','salarydetails','certification','creditcarddetailsview','creditcard','visadetailsview','visa','medicalclaims','disabilitydetailsview','disability','dependency','workeligibilitydetailsview','workeligibility','additionaldetailsedit','jobhistory','add','edit','delete','view','My Details'));

		 $acl->addResource(new Zend_Acl_Resource('default:myemployees'));
                            $acl->allow('Manager', 'default:myemployees', array('index','perview','comview','skillsview','expview','eduview','trainingview','additionaldetailsview','jobhistoryview','view','My Team'));

		 $acl->addResource(new Zend_Acl_Resource('default:myholidaycalendar'));
                            $acl->allow('Manager', 'default:myholidaycalendar', array('index','view','My Holiday Calendar'));

		 $acl->addResource(new Zend_Acl_Resource('default:organisationinfo'));
                            $acl->allow('Manager', 'default:organisationinfo', array('index','saveupdate','uploadpreview','validateorgstartdate','getcompleteorgdata','addorghead','Organization Info'));

		 $acl->addResource(new Zend_Acl_Resource('default:pendingleaves'));
                            $acl->allow('Manager', 'default:pendingleaves', array('index','delete','view','Pending Leaves'));

		 $acl->addResource(new Zend_Acl_Resource('default:rejectedleaves'));
                            $acl->allow('Manager', 'default:rejectedleaves', array('index','view','Rejected Leaves'));

		 $acl->addResource(new Zend_Acl_Resource('default:rejectedrequisitions'));
                            $acl->allow('Manager', 'default:rejectedrequisitions', array('index','view','Rejected Requisitions'));

		 $acl->addResource(new Zend_Acl_Resource('default:requisition'));
                            $acl->allow('Manager', 'default:requisition', array('index','viewhr','approverequisition','addcandidate','interview','getdepartments','getpositions','viewpopup','getapprreqdata','chkreqforclose','getempreportingmanagers','getemailcount','getapprovers','add','edit','delete','view','Openings/Positions'));

		 $acl->addResource(new Zend_Acl_Resource('default:scheduleinterviews'));
                            $acl->allow('Manager', 'default:scheduleinterviews', array('candidatepopup','index','downloadresume','getcandidates','edit','view','Scheduled Interviews'));

		 $acl->addResource(new Zend_Acl_Resource('default:shortlistedcandidates'));
                            $acl->allow('Manager', 'default:shortlistedcandidates', array('index','view','Shortlisted & Selected Candidates'));

		 $acl->addResource(new Zend_Acl_Resource('default:structure'));
                            $acl->allow('Manager', 'default:structure', array('index','Organization Structure'));

		 $acl->addResource(new Zend_Acl_Resource('default:emppersonaldetails'));
                            $acl->allow('Manager', 'default:emppersonaldetails', array('index','view'));

		 $acl->addResource(new Zend_Acl_Resource('default:empcommunicationdetails'));
                            $acl->allow('Manager', 'default:empcommunicationdetails', array('index','view','viewAction_27'));

		 $acl->addResource(new Zend_Acl_Resource('default:trainingandcertificationdetails'));
                            $acl->allow('Manager', 'default:trainingandcertificationdetails', array('addpopup','editpopup','index','view','viewpopup'));

		 $acl->addResource(new Zend_Acl_Resource('default:experiencedetails'));
                            $acl->allow('Manager', 'default:experiencedetails', array('addpopup','editpopup','index','view','viewpopup'));

		 $acl->addResource(new Zend_Acl_Resource('default:educationdetails'));
                            $acl->allow('Manager', 'default:educationdetails', array('addpopup','editpopup','index','view','viewpopup'));

		 $acl->addResource(new Zend_Acl_Resource('default:medicalclaims'));
                            $acl->allow('Manager', 'default:medicalclaims', array('addpopup','editpopup','index','viewpopup','view'));

		 $acl->addResource(new Zend_Acl_Resource('default:empleaves'));
                            $acl->allow('Manager', 'default:empleaves', array('index','view','viewpopup'));

		 $acl->addResource(new Zend_Acl_Resource('default:empskills'));
                            $acl->allow('Manager', 'default:empskills', array('addpopup','editpopup','index','view','viewpopup'));

		 $acl->addResource(new Zend_Acl_Resource('default:disabilitydetails'));
                            $acl->allow('Manager', 'default:disabilitydetails', array('index','view'));

		 $acl->addResource(new Zend_Acl_Resource('default:workeligibilitydetails'));
                            $acl->allow('Manager', 'default:workeligibilitydetails', array('index','view'));

		 $acl->addResource(new Zend_Acl_Resource('default:visaandimmigrationdetails'));
                            $acl->allow('Manager', 'default:visaandimmigrationdetails', array('index','view'));

		 $acl->addResource(new Zend_Acl_Resource('default:creditcarddetails'));
                            $acl->allow('Manager', 'default:creditcarddetails', array('index','view'));

		 $acl->addResource(new Zend_Acl_Resource('default:dependencydetails'));
                            $acl->allow('Manager', 'default:dependencydetails', array('addpopup','editpopup','index','view','viewpopup'));

		 $acl->addResource(new Zend_Acl_Resource('default:empholidays'));
                            $acl->allow('Manager', 'default:empholidays', array('index','view','viewpopup'));

		 $acl->addResource(new Zend_Acl_Resource('default:empjobhistory'));
                            $acl->allow('Manager', 'default:empjobhistory', array('addpopup','editpopup','index','view','viewpopup'));

		 $acl->addResource(new Zend_Acl_Resource('default:empadditionaldetails'));
                            $acl->allow('Manager', 'default:empadditionaldetails', array('addpopup','editpopup','index','view','viewpopup'));

		 $acl->addResource(new Zend_Acl_Resource('default:interviewrounds'));
                            $acl->allow('Manager', 'default:interviewrounds', array('index','addpopup','editpopup','viewpopup','index','addpopup','editpopup','viewpopup','delete'));

		 $acl->addResource(new Zend_Acl_Resource('default:apprreqcandidates'));
                            $acl->allow('Manager', 'default:apprreqcandidates', array('index','viewpopup'));
}if($role == 4 )
           {
		 $acl->addResource(new Zend_Acl_Resource('default:agencylist'));
                            $acl->allow('HRM', 'default:agencylist', array('index','deletepoc','add','edit','delete','view','Agencies'));

		 $acl->addResource(new Zend_Acl_Resource('default:approvedleaves'));
                            $acl->allow('HRM', 'default:approvedleaves', array('index','view','Approved Leaves'));

		 $acl->addResource(new Zend_Acl_Resource('default:approvedrequisitions'));
                            $acl->allow('HRM', 'default:approvedrequisitions', array('index','edit','view','Approved Requisitions'));

		 $acl->addResource(new Zend_Acl_Resource('default:attendancestatuscode'));
                            $acl->allow('HRM', 'default:attendancestatuscode', array('index','add','edit','view','Attendance Status'));

		 $acl->addResource(new Zend_Acl_Resource('default:bankaccounttype'));
                            $acl->allow('HRM', 'default:bankaccounttype', array('index','addpopup','add','edit','view','Bank Account Types'));

		 $acl->addResource(new Zend_Acl_Resource('default:bgscreeningtype'));
                            $acl->allow('HRM', 'default:bgscreeningtype', array('index','addpopup','add','edit','delete','view','Screening Types'));

		 $acl->addResource(new Zend_Acl_Resource('default:businessunits'));
                            $acl->allow('HRM', 'default:businessunits', array('index','getdeptnames','add','edit','delete','view','Business Units'));

		 $acl->addResource(new Zend_Acl_Resource('default:cancelleaves'));
                            $acl->allow('HRM', 'default:cancelleaves', array('index','view','Cancelled Leaves'));

		 $acl->addResource(new Zend_Acl_Resource('default:candidatedetails'));
                            $acl->allow('HRM', 'default:candidatedetails', array('index','chkcandidate','uploadfile','deleteresume','download','multipleresume','add','edit','view','CV Management'));

		 $acl->addResource(new Zend_Acl_Resource('default:competencylevel'));
                            $acl->allow('HRM', 'default:competencylevel', array('index','addpopup','add','edit','view','Competency Levels'));

		 $acl->addResource(new Zend_Acl_Resource('default:dashboard'));
                        $acl->allow('HRM', 'default:dashboard', array('index','saveuserdashboard','getwidgtes','emailsettings','changepassword','editpassword','update','uploadpreview','viewprofile','viewsettings','savemenuwidgets','getmenuname','fetchmenuname','getnavids','getopeningpositondate','menuwork'));

		 $acl->addResource(new Zend_Acl_Resource('default:departments'));
                            $acl->allow('HRM', 'default:departments', array('index','viewpopup','editpopup','getdepartments','getempnames','add','edit','delete','view','Departments'));

		 $acl->addResource(new Zend_Acl_Resource('default:educationlevelcode'));
                            $acl->allow('HRM', 'default:educationlevelcode', array('index','add','edit','view','Education Levels'));

		 $acl->addResource(new Zend_Acl_Resource('default:eeoccategory'));
                            $acl->allow('HRM', 'default:eeoccategory', array('index','add','edit','view','EEOC Categories'));

		 $acl->addResource(new Zend_Acl_Resource('default:empconfiguration'));
                            $acl->allow('HRM', 'default:empconfiguration', array('index','edit','Employee Tabs'));

		 $acl->addResource(new Zend_Acl_Resource('default:empleavesummary'));
                            $acl->allow('HRM', 'default:empleavesummary', array('index','statusid','view','Employee Leaves Summary'));

		 $acl->addResource(new Zend_Acl_Resource('default:employee'));
                            $acl->allow('HRM', 'default:employee', array('getemprequi','index','getdepartments','getpositions','getempreportingmanagers','makeactiveinactive','changereportingmanager','addemppopup','add','edit','view','Employees'));

		 $acl->addResource(new Zend_Acl_Resource('default:employeeleavetypes'));
                            $acl->allow('HRM', 'default:employeeleavetypes', array('index','add','edit','view','Leave Types'));

		 $acl->addResource(new Zend_Acl_Resource('default:employmentstatus'));
                            $acl->allow('HRM', 'default:employmentstatus', array('index','addpopup','add','edit','view','Employment Status'));

		 $acl->addResource(new Zend_Acl_Resource('default:empscreening'));
                            $acl->allow('HRM', 'default:empscreening', array('index','getemployeedata','getagencylist','getpocdata','forcedfullupdate','checkscreeningstatus','uploadfeedback','download','deletefeedback','add','edit','view','Employee/Candidate Screening'));

		 $acl->addResource(new Zend_Acl_Resource('default:heirarchy'));
                            $acl->allow('HRM', 'default:heirarchy', array('index','addlist','editlist','saveadddata','saveeditdata','deletelist','editActi','deletelist_1','Organization Hierarchy'));

		 $acl->addResource(new Zend_Acl_Resource('default:holidaydates'));
                            $acl->allow('HRM', 'default:holidaydates', array('index','addpopup','viewpopup','editpopup','add','edit','view','Manage Holidays'));

		 $acl->addResource(new Zend_Acl_Resource('default:holidaygroups'));
                            $acl->allow('HRM', 'default:holidaygroups', array('index','getempnames','getholidaynames','addpopup','add','edit','view','Manage Holiday Group'));

		 $acl->addResource(new Zend_Acl_Resource('default:identitydocuments'));
                            $acl->allow('HRM', 'default:identitydocuments', array('index','view','add','edit','Identity Documents'));

		 $acl->addResource(new Zend_Acl_Resource('default:index'));
                        $acl->allow('HRM', 'default:index', array('index','loginpopupsave','welcome','logout','clearsessionarray','forcelogout','browserfailure','sendpassword','updatecontactnumber','getstates','getstatesnormal','getcities','getcitiesnormal','getdepartments','getpositions','gettargetcurrency','calculatedays','calculatebusinessdays','calculatecalendardays','fromdatetodate','fromdatetodateorg','medicalclaimdates','gettimeformat','chkcurrenttime','popup','createorremoveshortcut','sessiontour','getissuingauthority','setsessionval','checkisactivestatus'));

		 $acl->addResource(new Zend_Acl_Resource('default:jobtitles'));
                            $acl->allow('HRM', 'default:jobtitles', array('index','addpopup','add','edit','view','Job Titles'));

		 $acl->addResource(new Zend_Acl_Resource('default:leavemanagement'));
                            $acl->allow('HRM', 'default:leavemanagement', array('index','add','edit','view','Leave Management Options'));

		 $acl->addResource(new Zend_Acl_Resource('default:leaverequest'));
                            $leaverequest_add = 'yes';
                                if($this->id_param == '' && $leaverequest_add == 'yes')
                                    $acl->allow('HRM', 'default:leaverequest', array('index','saveleaverequestdetails','gethalfdaydetails','add','Leave Request','edit'));

                                else
                                    $acl->allow('HRM', 'default:leaverequest', array('index','saveleaverequestdetails','gethalfdaydetails','add','Leave Request'));

                                
		 $acl->addResource(new Zend_Acl_Resource('default:manageremployeevacations'));
                            $acl->allow('HRM', 'default:manageremployeevacations', array('index','edit','view','Manage Employee Leaves'));

		 $acl->addResource(new Zend_Acl_Resource('default:mydetails'));
                            $acl->allow('HRM', 'default:mydetails', array('index','personaldetailsview','personal','communicationdetailsview','communication','skills','education','experience','leaves','holidays','salarydetailsview','salarydetails','certification','creditcarddetailsview','creditcard','visadetailsview','visa','medicalclaims','disabilitydetailsview','disability','dependency','workeligibilitydetailsview','workeligibility','additionaldetailsedit','jobhistory','add','edit','delete','view','My Details'));

		 $acl->addResource(new Zend_Acl_Resource('default:myemployees'));
                            $acl->allow('HRM', 'default:myemployees', array('index','perview','comview','skillsview','expview','eduview','trainingview','additionaldetailsview','jobhistoryview','view','My Team'));

		 $acl->addResource(new Zend_Acl_Resource('default:myholidaycalendar'));
                            $acl->allow('HRM', 'default:myholidaycalendar', array('index','view','My Holiday Calendar'));

		 $acl->addResource(new Zend_Acl_Resource('default:organisationinfo'));
                            $acl->allow('HRM', 'default:organisationinfo', array('index','saveupdate','uploadpreview','validateorgstartdate','getcompleteorgdata','addorghead','edit','view','Organization Info'));

		 $acl->addResource(new Zend_Acl_Resource('default:payfrequency'));
                            $acl->allow('HRM', 'default:payfrequency', array('index','addpopup','add','edit','view','Pay Frequency'));

		 $acl->addResource(new Zend_Acl_Resource('default:pendingleaves'));
                            $acl->allow('HRM', 'default:pendingleaves', array('index','delete','view','Pending Leaves'));

		 $acl->addResource(new Zend_Acl_Resource('default:positions'));
                            $acl->allow('HRM', 'default:positions', array('index','addpopup','add','edit','view','Positions'));

		 $acl->addResource(new Zend_Acl_Resource('default:rejectedleaves'));
                            $acl->allow('HRM', 'default:rejectedleaves', array('index','view','Rejected Leaves'));

		 $acl->addResource(new Zend_Acl_Resource('default:rejectedrequisitions'));
                            $acl->allow('HRM', 'default:rejectedrequisitions', array('index','view','Rejected Requisitions'));

		 $acl->addResource(new Zend_Acl_Resource('default:remunerationbasis'));
                            $acl->allow('HRM', 'default:remunerationbasis', array('index','add','edit','view','Remuneration Basis'));

		 $acl->addResource(new Zend_Acl_Resource('default:requisition'));
                            $acl->allow('HRM', 'default:requisition', array('index','viewhr','approverequisition','addcandidate','interview','getdepartments','getpositions','viewpopup','getapprreqdata','chkreqforclose','getempreportingmanagers','getemailcount','getapprovers','add','edit','delete','view','Openings/Positions'));

		 $acl->addResource(new Zend_Acl_Resource('default:scheduleinterviews'));
                            $acl->allow('HRM', 'default:scheduleinterviews', array('candidatepopup','index','downloadresume','getcandidates','add','edit','view','Scheduled Interviews'));

		 $acl->addResource(new Zend_Acl_Resource('default:shortlistedcandidates'));
                            $acl->allow('HRM', 'default:shortlistedcandidates', array('index','view','Shortlisted & Selected Candidates'));

		 $acl->addResource(new Zend_Acl_Resource('default:structure'));
                            $acl->allow('HRM', 'default:structure', array('index','Organization Structure'));

		 $acl->addResource(new Zend_Acl_Resource('default:usermanagement'));
                            $acl->allow('HRM', 'default:usermanagement', array('index','saveupdate','getemailofuser','add','edit','view','Manage External Users'));

		 $acl->addResource(new Zend_Acl_Resource('default:workeligibilitydoctypes'));
                            $acl->allow('HRM', 'default:workeligibilitydoctypes', array('index','addpopup','add','edit','view','Work Eligibility Document Types'));

		 $acl->addResource(new Zend_Acl_Resource('default:processes'));
                            $acl->allow('HRM', 'default:processes', array('index','addpopup','editpopup','viewpopup','savecomments','displaycomments','savefeedback','index','addpopup','editpopup','viewpopup','delete','savecomments','displaycomments','savefeedback'));

		 $acl->addResource(new Zend_Acl_Resource('default:interviewrounds'));
                            $acl->allow('HRM', 'default:interviewrounds', array('index','addpopup','editpopup','viewpopup','index','addpopup','editpopup','viewpopup','delete'));

		 $acl->addResource(new Zend_Acl_Resource('default:empperformanceappraisal'));
                            $acl->allow('HRM', 'default:empperformanceappraisal', array('index','view','index','edit','view'));

		 $acl->addResource(new Zend_Acl_Resource('default:emppayslips'));
                            $acl->allow('HRM', 'default:emppayslips', array('index','view','index','edit','view'));

		 $acl->addResource(new Zend_Acl_Resource('default:empbenefits'));
                            $acl->allow('HRM', 'default:empbenefits', array('index','view','index','edit','view'));

		 $acl->addResource(new Zend_Acl_Resource('default:emprequisitiondetails'));
                            $acl->allow('HRM', 'default:emprequisitiondetails', array('index','view','index','edit','view'));

		 $acl->addResource(new Zend_Acl_Resource('default:empremunerationdetails'));
                            $acl->allow('HRM', 'default:empremunerationdetails', array('index','view','index','edit','view'));

		 $acl->addResource(new Zend_Acl_Resource('default:empsecuritycredentials'));
                            $acl->allow('HRM', 'default:empsecuritycredentials', array('index','view','index','edit','view'));

		 $acl->addResource(new Zend_Acl_Resource('default:apprreqcandidates'));
                            $acl->allow('HRM', 'default:apprreqcandidates', array('index','viewpopup','index','viewpopup'));

		 $acl->addResource(new Zend_Acl_Resource('default:emppersonaldetails'));
                            $acl->allow('HRM', 'default:emppersonaldetails', array('index','view','index','view','edit'));

		 $acl->addResource(new Zend_Acl_Resource('default:empcommunicationdetails'));
                            $acl->allow('HRM', 'default:empcommunicationdetails', array('index','view','viewAction_27','index','view','viewAction_27','edit'));

		 $acl->addResource(new Zend_Acl_Resource('default:trainingandcertificationdetails'));
                            $acl->allow('HRM', 'default:trainingandcertificationdetails', array('index','view','addpopup','editpopup','viewpopup','index','edit','view','addpopup','editpopup','viewpopup','delete'));

		 $acl->addResource(new Zend_Acl_Resource('default:experiencedetails'));
                            $acl->allow('HRM', 'default:experiencedetails', array('index','view','addpopup','editpopup','viewpopup','index','edit','view','addpopup','editpopup','viewpopup','delete'));

		 $acl->addResource(new Zend_Acl_Resource('default:educationdetails'));
                            $acl->allow('HRM', 'default:educationdetails', array('index','view','addpopup','viewpopup','editpopup','index','edit','view','addpopup','viewpopup','editpopup','delete'));

		 $acl->addResource(new Zend_Acl_Resource('default:medicalclaims'));
                            $acl->allow('HRM', 'default:medicalclaims', array('index','addpopup','viewpopup','editpopup','view','index','edit','addpopup','viewpopup','editpopup','delete','view'));

		 $acl->addResource(new Zend_Acl_Resource('default:empleaves'));
                            $acl->allow('HRM', 'default:empleaves', array('index','view','viewpopup','index','edit','view','viewpopup'));

		 $acl->addResource(new Zend_Acl_Resource('default:empskills'));
                            $acl->allow('HRM', 'default:empskills', array('index','view','addpopup','viewpopup','editpopup','index','edit','view','addpopup','viewpopup','editpopup','delete'));

		 $acl->addResource(new Zend_Acl_Resource('default:disabilitydetails'));
                            $acl->allow('HRM', 'default:disabilitydetails', array('index','view','index','add','edit','view'));

		 $acl->addResource(new Zend_Acl_Resource('default:workeligibilitydetails'));
                            $acl->allow('HRM', 'default:workeligibilitydetails', array('index','view','index','add','edit','view'));

		 $acl->addResource(new Zend_Acl_Resource('default:empadditionaldetails'));
                            $acl->allow('HRM', 'default:empadditionaldetails', array('index','view','addpopup','viewpopup','editpopup','index','edit','view','addpopup','viewpopup','editpopup','delete'));

		 $acl->addResource(new Zend_Acl_Resource('default:visaandimmigrationdetails'));
                            $acl->allow('HRM', 'default:visaandimmigrationdetails', array('index','view','index','add','edit','view'));

		 $acl->addResource(new Zend_Acl_Resource('default:creditcarddetails'));
                            $acl->allow('HRM', 'default:creditcarddetails', array('index','view','index','add','edit','view'));

		 $acl->addResource(new Zend_Acl_Resource('default:dependencydetails'));
                            $acl->allow('HRM', 'default:dependencydetails', array('index','view','addpopup','editpopup','viewpopup','index','edit','view','addpopup','editpopup','viewpopup','delete'));

		 $acl->addResource(new Zend_Acl_Resource('default:empholidays'));
                            $acl->allow('HRM', 'default:empholidays', array('index','view','viewpopup','index','edit','view','viewpopup'));

		 $acl->addResource(new Zend_Acl_Resource('default:empjobhistory'));
                            $acl->allow('HRM', 'default:empjobhistory', array('index','view','addpopup','viewpopup','editpopup','index','edit','view','addpopup','viewpopup','editpopup','delete'));

		 $acl->addResource(new Zend_Acl_Resource('default:empsalarydetails'));
                            $acl->allow('HRM', 'default:empsalarydetails', array('index','view','index','edit','view'));
}if($role == 5 )
           {
		 $acl->addResource(new Zend_Acl_Resource('default:approvedleaves'));
                            $acl->allow('Employee', 'default:approvedleaves', array('index','view','Approved Leaves'));

		 $acl->addResource(new Zend_Acl_Resource('default:businessunits'));
                            $acl->allow('Employee', 'default:businessunits', array('index','getdeptnames','view','Business Units'));

		 $acl->addResource(new Zend_Acl_Resource('default:cancelleaves'));
                            $acl->allow('Employee', 'default:cancelleaves', array('index','view','Cancelled Leaves'));

		 $acl->addResource(new Zend_Acl_Resource('default:dashboard'));
                        $acl->allow('Employee', 'default:dashboard', array('index','saveuserdashboard','getwidgtes','emailsettings','changepassword','editpassword','update','uploadpreview','viewprofile','viewsettings','savemenuwidgets','getmenuname','fetchmenuname','getnavids','getopeningpositondate','menuwork'));

		 $acl->addResource(new Zend_Acl_Resource('default:departments'));
                            $acl->allow('Employee', 'default:departments', array('index','viewpopup','editpopup','getdepartments','getempnames','view','Departments'));

		 $acl->addResource(new Zend_Acl_Resource('default:employee'));
                            $acl->allow('Employee', 'default:employee', array('getemprequi','index','getdepartments','getpositions','getempreportingmanagers','makeactiveinactive','changereportingmanager','addemppopup','view','Employees'));

		 $acl->addResource(new Zend_Acl_Resource('default:heirarchy'));
                            $acl->allow('Employee', 'default:heirarchy', array('index','addlist','editlist','saveadddata','saveeditdata','deletelist','editActi','deletelist_1','Organization Hierarchy'));

		 $acl->addResource(new Zend_Acl_Resource('default:index'));
                        $acl->allow('Employee', 'default:index', array('index','loginpopupsave','welcome','logout','clearsessionarray','forcelogout','browserfailure','sendpassword','updatecontactnumber','getstates','getstatesnormal','getcities','getcitiesnormal','getdepartments','getpositions','gettargetcurrency','calculatedays','calculatebusinessdays','calculatecalendardays','fromdatetodate','fromdatetodateorg','medicalclaimdates','gettimeformat','chkcurrenttime','popup','createorremoveshortcut','sessiontour','getissuingauthority','setsessionval','checkisactivestatus'));

		 $acl->addResource(new Zend_Acl_Resource('default:leaverequest'));
                            $leaverequest_add = 'yes';
                                if($this->id_param == '' && $leaverequest_add == 'yes')
                                    $acl->allow('Employee', 'default:leaverequest', array('index','saveleaverequestdetails','gethalfdaydetails','add','Leave Request','edit'));

                                else
                                    $acl->allow('Employee', 'default:leaverequest', array('index','saveleaverequestdetails','gethalfdaydetails','add','Leave Request'));

                                
		 $acl->addResource(new Zend_Acl_Resource('default:manageremployeevacations'));
                            $acl->allow('Employee', 'default:manageremployeevacations', array('index','edit','view','Manage Employee Leaves'));

		 $acl->addResource(new Zend_Acl_Resource('default:mydetails'));
                            $acl->allow('Employee', 'default:mydetails', array('index','personaldetailsview','personal','communicationdetailsview','communication','skills','education','experience','leaves','holidays','salarydetailsview','salarydetails','certification','creditcarddetailsview','creditcard','visadetailsview','visa','medicalclaims','disabilitydetailsview','disability','dependency','workeligibilitydetailsview','workeligibility','additionaldetailsedit','jobhistory','add','edit','delete','view','My Details'));

		 $acl->addResource(new Zend_Acl_Resource('default:myemployees'));
                            $acl->allow('Employee', 'default:myemployees', array('index','perview','comview','skillsview','expview','eduview','trainingview','additionaldetailsview','jobhistoryview','view','My Team'));

		 $acl->addResource(new Zend_Acl_Resource('default:myholidaycalendar'));
                            $acl->allow('Employee', 'default:myholidaycalendar', array('index','view','My Holiday Calendar'));

		 $acl->addResource(new Zend_Acl_Resource('default:organisationinfo'));
                            $acl->allow('Employee', 'default:organisationinfo', array('index','saveupdate','uploadpreview','validateorgstartdate','getcompleteorgdata','addorghead','Organization Info'));

		 $acl->addResource(new Zend_Acl_Resource('default:pendingleaves'));
                            $acl->allow('Employee', 'default:pendingleaves', array('index','delete','view','Pending Leaves'));

		 $acl->addResource(new Zend_Acl_Resource('default:rejectedleaves'));
                            $acl->allow('Employee', 'default:rejectedleaves', array('index','view','Rejected Leaves'));

		 $acl->addResource(new Zend_Acl_Resource('default:scheduleinterviews'));
                            $acl->allow('Employee', 'default:scheduleinterviews', array('candidatepopup','index','downloadresume','getcandidates','edit','view','Scheduled Interviews'));

		 $acl->addResource(new Zend_Acl_Resource('default:structure'));
                            $acl->allow('Employee', 'default:structure', array('index','Organization Structure'));

		 $acl->addResource(new Zend_Acl_Resource('default:emppersonaldetails'));
                            $acl->allow('Employee', 'default:emppersonaldetails', array('index','view','index','view','edit'));

		 $acl->addResource(new Zend_Acl_Resource('default:empcommunicationdetails'));
                            $acl->allow('Employee', 'default:empcommunicationdetails', array('index','view','viewAction_27','index','view','viewAction_27','edit'));

		 $acl->addResource(new Zend_Acl_Resource('default:trainingandcertificationdetails'));
                            $acl->allow('Employee', 'default:trainingandcertificationdetails', array('index','view','addpopup','editpopup','viewpopup','index','edit','view','addpopup','editpopup','viewpopup','delete'));

		 $acl->addResource(new Zend_Acl_Resource('default:experiencedetails'));
                            $acl->allow('Employee', 'default:experiencedetails', array('index','view','addpopup','editpopup','viewpopup','index','edit','view','addpopup','editpopup','viewpopup','delete'));

		 $acl->addResource(new Zend_Acl_Resource('default:educationdetails'));
                            $acl->allow('Employee', 'default:educationdetails', array('index','view','addpopup','viewpopup','editpopup','index','edit','view','addpopup','viewpopup','editpopup','delete'));

		 $acl->addResource(new Zend_Acl_Resource('default:medicalclaims'));
                            $acl->allow('Employee', 'default:medicalclaims', array('index','addpopup','viewpopup','editpopup','view','index','edit','addpopup','viewpopup','editpopup','delete','view'));

		 $acl->addResource(new Zend_Acl_Resource('default:empleaves'));
                            $acl->allow('Employee', 'default:empleaves', array('index','view','viewpopup','index','edit','view','viewpopup'));

		 $acl->addResource(new Zend_Acl_Resource('default:empskills'));
                            $acl->allow('Employee', 'default:empskills', array('index','view','addpopup','viewpopup','editpopup','index','edit','view','addpopup','viewpopup','editpopup','delete'));

		 $acl->addResource(new Zend_Acl_Resource('default:disabilitydetails'));
                            $acl->allow('Employee', 'default:disabilitydetails', array('index','view','index','add','edit','view'));

		 $acl->addResource(new Zend_Acl_Resource('default:workeligibilitydetails'));
                            $acl->allow('Employee', 'default:workeligibilitydetails', array('index','view','index','add','edit','view'));

		 $acl->addResource(new Zend_Acl_Resource('default:empadditionaldetails'));
                            $acl->allow('Employee', 'default:empadditionaldetails', array('index','view','addpopup','viewpopup','editpopup','index','edit','view','addpopup','viewpopup','editpopup','delete'));

		 $acl->addResource(new Zend_Acl_Resource('default:visaandimmigrationdetails'));
                            $acl->allow('Employee', 'default:visaandimmigrationdetails', array('index','view','index','add','edit','view'));

		 $acl->addResource(new Zend_Acl_Resource('default:creditcarddetails'));
                            $acl->allow('Employee', 'default:creditcarddetails', array('index','view','index','add','edit','view'));

		 $acl->addResource(new Zend_Acl_Resource('default:dependencydetails'));
                            $acl->allow('Employee', 'default:dependencydetails', array('index','view','addpopup','editpopup','viewpopup','index','edit','view','addpopup','editpopup','viewpopup','delete'));

		 $acl->addResource(new Zend_Acl_Resource('default:empholidays'));
                            $acl->allow('Employee', 'default:empholidays', array('index','view','viewpopup','index','edit','view','viewpopup'));

		 $acl->addResource(new Zend_Acl_Resource('default:empjobhistory'));
                            $acl->allow('Employee', 'default:empjobhistory', array('index','view','addpopup','viewpopup','editpopup','index','edit','view','addpopup','viewpopup','editpopup','delete'));

		 $acl->addResource(new Zend_Acl_Resource('default:interviewrounds'));
                            $acl->allow('Employee', 'default:interviewrounds', array('index','addpopup','editpopup','viewpopup','index','addpopup','editpopup','viewpopup','delete'));

		 $acl->addResource(new Zend_Acl_Resource('default:apprreqcandidates'));
                            $acl->allow('Employee', 'default:apprreqcandidates', array('index','viewpopup'));
}if($role == 6 )
           {
		 $acl->addResource(new Zend_Acl_Resource('default:businessunits'));
                            $acl->allow('External', 'default:businessunits', array('index','getdeptnames','view','Business Units'));

		 $acl->addResource(new Zend_Acl_Resource('default:dashboard'));
                        $acl->allow('External', 'default:dashboard', array('index','saveuserdashboard','getwidgtes','emailsettings','changepassword','editpassword','update','uploadpreview','viewprofile','viewsettings','savemenuwidgets','getmenuname','fetchmenuname','getnavids','getopeningpositondate','menuwork'));

		 $acl->addResource(new Zend_Acl_Resource('default:departments'));
                            $acl->allow('External', 'default:departments', array('index','viewpopup','editpopup','getdepartments','getempnames','view','Departments'));

		 $acl->addResource(new Zend_Acl_Resource('default:empscreening'));
                            $acl->allow('External', 'default:empscreening', array('index','getemployeedata','getagencylist','getpocdata','forcedfullupdate','checkscreeningstatus','uploadfeedback','download','deletefeedback','edit','view','Employee/Candidate Screening'));

		 $acl->addResource(new Zend_Acl_Resource('default:heirarchy'));
                            $acl->allow('External', 'default:heirarchy', array('index','addlist','editlist','saveadddata','saveeditdata','deletelist','editActi','deletelist_1','Organization Hierarchy'));

		 $acl->addResource(new Zend_Acl_Resource('default:index'));
                        $acl->allow('External', 'default:index', array('index','loginpopupsave','welcome','logout','clearsessionarray','forcelogout','browserfailure','sendpassword','updatecontactnumber','getstates','getstatesnormal','getcities','getcitiesnormal','getdepartments','getpositions','gettargetcurrency','calculatedays','calculatebusinessdays','calculatecalendardays','fromdatetodate','fromdatetodateorg','medicalclaimdates','gettimeformat','chkcurrenttime','popup','createorremoveshortcut','sessiontour','getissuingauthority','setsessionval','checkisactivestatus'));

		 $acl->addResource(new Zend_Acl_Resource('default:organisationinfo'));
                            $acl->allow('External', 'default:organisationinfo', array('index','saveupdate','uploadpreview','validateorgstartdate','getcompleteorgdata','addorghead','Organization Info'));

		 $acl->addResource(new Zend_Acl_Resource('default:structure'));
                            $acl->allow('External', 'default:structure', array('index','Organization Structure'));

		 $acl->addResource(new Zend_Acl_Resource('default:processes'));
                            $acl->allow('External', 'default:processes', array('index','editpopup','viewpopup','savecomments','displaycomments','savefeedback'));
}if($role == 7 )
           {
		 $acl->addResource(new Zend_Acl_Resource('default:accountclasstype'));
                            $acl->allow('Sysadmin', 'default:accountclasstype', array('index','addpopup','saveupdate','add','edit','delete','view','Account Class Types '));

		 $acl->addResource(new Zend_Acl_Resource('default:approvedleaves'));
                            $acl->allow('Sysadmin', 'default:approvedleaves', array('index','view','Approved Leaves'));

		 $acl->addResource(new Zend_Acl_Resource('default:businessunits'));
                            $acl->allow('Sysadmin', 'default:businessunits', array('index','getdeptnames','view','Business Units'));

		 $acl->addResource(new Zend_Acl_Resource('default:cancelleaves'));
                            $acl->allow('Sysadmin', 'default:cancelleaves', array('index','view','Cancelled Leaves'));

		 $acl->addResource(new Zend_Acl_Resource('default:cities'));
                            $cities_add = 'yes';
                                if($this->id_param == '' && $cities_add == 'yes')
                                    $acl->allow('Sysadmin', 'default:cities', array('index','getcitiescand','addpopup','addnewcity','add','delete','view','Cities','edit'));

                                else
                                    $acl->allow('Sysadmin', 'default:cities', array('index','getcitiescand','addpopup','addnewcity','add','delete','view','Cities'));

                                
		 $acl->addResource(new Zend_Acl_Resource('default:countries'));
                            $acl->allow('Sysadmin', 'default:countries', array('index','saveupdate','getcountrycode','addpopup','addnewcountry','add','edit','delete','view','Countries'));

		 $acl->addResource(new Zend_Acl_Resource('default:currency'));
                            $acl->allow('Sysadmin', 'default:currency', array('index','addpopup','gettargetcurrency','add','edit','delete','view','Currencies'));

		 $acl->addResource(new Zend_Acl_Resource('default:currencyconverter'));
                            $acl->allow('Sysadmin', 'default:currencyconverter', array('index','add','edit','delete','view','Currency Conversions'));

		 $acl->addResource(new Zend_Acl_Resource('default:dashboard'));
                        $acl->allow('Sysadmin', 'default:dashboard', array('index','saveuserdashboard','getwidgtes','emailsettings','changepassword','editpassword','update','uploadpreview','viewprofile','viewsettings','savemenuwidgets','getmenuname','fetchmenuname','getnavids','getopeningpositondate','menuwork'));

		 $acl->addResource(new Zend_Acl_Resource('default:departments'));
                            $acl->allow('Sysadmin', 'default:departments', array('index','viewpopup','editpopup','getdepartments','getempnames','view','Departments'));

		 $acl->addResource(new Zend_Acl_Resource('default:emailcontacts'));
                            $acl->allow('Sysadmin', 'default:emailcontacts', array('index','getgroupoptions','getmailcnt','add','edit','delete','view','Email Contacts'));

		 $acl->addResource(new Zend_Acl_Resource('default:employee'));
                            $acl->allow('Sysadmin', 'default:employee', array('getemprequi','index','getdepartments','getpositions','getempreportingmanagers','makeactiveinactive','changereportingmanager','addemppopup','view','Employees'));

		 $acl->addResource(new Zend_Acl_Resource('default:ethniccode'));
                            $acl->allow('Sysadmin', 'default:ethniccode', array('index','saveupdate','addpopup','add','edit','delete','view','Ethnic Codes'));

		 $acl->addResource(new Zend_Acl_Resource('default:gender'));
                            $acl->allow('Sysadmin', 'default:gender', array('index','saveupdate','addpopup','add','edit','delete','view','Gender'));

		 $acl->addResource(new Zend_Acl_Resource('default:geographygroup'));
                            $acl->allow('Sysadmin', 'default:geographygroup', array('index','add','edit','delete','view','Geo Groups'));

		 $acl->addResource(new Zend_Acl_Resource('default:heirarchy'));
                            $acl->allow('Sysadmin', 'default:heirarchy', array('index','addlist','editlist','saveadddata','saveeditdata','deletelist','editActi','deletelist_1','Organization Hierarchy'));

		 $acl->addResource(new Zend_Acl_Resource('default:identitycodes'));
                            $acl->allow('Sysadmin', 'default:identitycodes', array('index','addpopup','add','edit','Identity Codes'));

		 $acl->addResource(new Zend_Acl_Resource('default:index'));
                        $acl->allow('Sysadmin', 'default:index', array('index','loginpopupsave','welcome','logout','clearsessionarray','forcelogout','browserfailure','sendpassword','updatecontactnumber','getstates','getstatesnormal','getcities','getcitiesnormal','getdepartments','getpositions','gettargetcurrency','calculatedays','calculatebusinessdays','calculatecalendardays','fromdatetodate','fromdatetodateorg','medicalclaimdates','gettimeformat','chkcurrenttime','popup','createorremoveshortcut','sessiontour','getissuingauthority','setsessionval','checkisactivestatus'));

		 $acl->addResource(new Zend_Acl_Resource('default:leaverequest'));
                            $leaverequest_add = 'yes';
                                if($this->id_param == '' && $leaverequest_add == 'yes')
                                    $acl->allow('Sysadmin', 'default:leaverequest', array('index','saveleaverequestdetails','gethalfdaydetails','add','Leave Request','edit'));

                                else
                                    $acl->allow('Sysadmin', 'default:leaverequest', array('index','saveleaverequestdetails','gethalfdaydetails','add','Leave Request'));

                                
		 $acl->addResource(new Zend_Acl_Resource('default:licensetype'));
                            $acl->allow('Sysadmin', 'default:licensetype', array('index','add','edit','delete','view','License Types'));

		 $acl->addResource(new Zend_Acl_Resource('default:manageremployeevacations'));
                            $acl->allow('Sysadmin', 'default:manageremployeevacations', array('index','edit','view','Manage Employee Leaves'));

		 $acl->addResource(new Zend_Acl_Resource('default:maritalstatus'));
                            $acl->allow('Sysadmin', 'default:maritalstatus', array('index','saveupdate','addpopup','add','edit','delete','view','Marital Status'));

		 $acl->addResource(new Zend_Acl_Resource('default:mydetails'));
                            $acl->allow('Sysadmin', 'default:mydetails', array('index','personaldetailsview','personal','communicationdetailsview','communication','skills','education','experience','leaves','holidays','salarydetailsview','salarydetails','certification','creditcarddetailsview','creditcard','visadetailsview','visa','medicalclaims','disabilitydetailsview','disability','dependency','workeligibilitydetailsview','workeligibility','additionaldetailsedit','jobhistory','add','edit','delete','view','My Details'));

		 $acl->addResource(new Zend_Acl_Resource('default:myemployees'));
                            $acl->allow('Sysadmin', 'default:myemployees', array('index','perview','comview','skillsview','expview','eduview','trainingview','additionaldetailsview','jobhistoryview','view','My Team'));

		 $acl->addResource(new Zend_Acl_Resource('default:myholidaycalendar'));
                            $acl->allow('Sysadmin', 'default:myholidaycalendar', array('index','view','My Holiday Calendar'));

		 $acl->addResource(new Zend_Acl_Resource('default:nationality'));
                            $acl->allow('Sysadmin', 'default:nationality', array('index','addpopup','add','edit','delete','view','Nationalities'));

		 $acl->addResource(new Zend_Acl_Resource('default:nationalitycontextcode'));
                            $acl->allow('Sysadmin', 'default:nationalitycontextcode', array('index','add','edit','delete','view','Nationality Context Codes'));

		 $acl->addResource(new Zend_Acl_Resource('default:numberformats'));
                            $acl->allow('Sysadmin', 'default:numberformats', array('index','add','edit','delete','view','Number Formats'));

		 $acl->addResource(new Zend_Acl_Resource('default:organisationinfo'));
                            $acl->allow('Sysadmin', 'default:organisationinfo', array('index','saveupdate','uploadpreview','validateorgstartdate','getcompleteorgdata','addorghead','Organization Info'));

		 $acl->addResource(new Zend_Acl_Resource('default:pendingleaves'));
                            $acl->allow('Sysadmin', 'default:pendingleaves', array('index','delete','view','Pending Leaves'));

		 $acl->addResource(new Zend_Acl_Resource('default:prefix'));
                            $acl->allow('Sysadmin', 'default:prefix', array('index','saveupdate','addpopup','add','edit','delete','view','Prefixes'));

		 $acl->addResource(new Zend_Acl_Resource('default:racecode'));
                            $acl->allow('Sysadmin', 'default:racecode', array('index','saveupdate','addpopup','add','edit','delete','view','Race Codes'));

		 $acl->addResource(new Zend_Acl_Resource('default:rejectedleaves'));
                            $acl->allow('Sysadmin', 'default:rejectedleaves', array('index','view','Rejected Leaves'));

		 $acl->addResource(new Zend_Acl_Resource('default:roles'));
                            $acl->allow('Sysadmin', 'default:roles', array('index','saveupdate','getgroupmenu','add','edit','delete','view','Roles & Privileges'));

		 $acl->addResource(new Zend_Acl_Resource('default:scheduleinterviews'));
                            $acl->allow('Sysadmin', 'default:scheduleinterviews', array('candidatepopup','index','downloadresume','getcandidates','edit','view','Scheduled Interviews'));

		 $acl->addResource(new Zend_Acl_Resource('default:sitepreference'));
                            $acl->allow('Sysadmin', 'default:sitepreference', array('index','view','add','edit','Site Preferences'));

		 $acl->addResource(new Zend_Acl_Resource('default:states'));
                            $states_add = 'yes';
                                if($this->id_param == '' && $states_add == 'yes')
                                    $acl->allow('Sysadmin', 'default:states', array('index','getstates','getstatescand','addpopup','addnewstate','add','delete','view','States','edit'));

                                else
                                    $acl->allow('Sysadmin', 'default:states', array('index','getstates','getstatescand','addpopup','addnewstate','add','delete','view','States'));

                                
		 $acl->addResource(new Zend_Acl_Resource('default:structure'));
                            $acl->allow('Sysadmin', 'default:structure', array('index','Organization Structure'));

		 $acl->addResource(new Zend_Acl_Resource('default:timezone'));
                            $acl->allow('Sysadmin', 'default:timezone', array('index','saveupdate','addpopup','add','edit','delete','view','Time Zones'));

		 $acl->addResource(new Zend_Acl_Resource('default:usermanagement'));
                            $acl->allow('Sysadmin', 'default:usermanagement', array('index','saveupdate','getemailofuser','add','edit','view','Manage External Users'));

		 $acl->addResource(new Zend_Acl_Resource('default:managemenus'));
                            $acl->allow('Sysadmin', 'default:managemenus', array('index','save','index','save'));

		 $acl->addResource(new Zend_Acl_Resource('default:emppersonaldetails'));
                            $acl->allow('Sysadmin', 'default:emppersonaldetails', array('index','view'));

		 $acl->addResource(new Zend_Acl_Resource('default:empcommunicationdetails'));
                            $acl->allow('Sysadmin', 'default:empcommunicationdetails', array('index','view','viewAction_27'));

		 $acl->addResource(new Zend_Acl_Resource('default:trainingandcertificationdetails'));
                            $acl->allow('Sysadmin', 'default:trainingandcertificationdetails', array('addpopup','editpopup','index','view','viewpopup'));

		 $acl->addResource(new Zend_Acl_Resource('default:experiencedetails'));
                            $acl->allow('Sysadmin', 'default:experiencedetails', array('addpopup','editpopup','index','view','viewpopup'));

		 $acl->addResource(new Zend_Acl_Resource('default:educationdetails'));
                            $acl->allow('Sysadmin', 'default:educationdetails', array('addpopup','editpopup','index','view','viewpopup'));

		 $acl->addResource(new Zend_Acl_Resource('default:medicalclaims'));
                            $acl->allow('Sysadmin', 'default:medicalclaims', array('addpopup','editpopup','index','viewpopup','view'));

		 $acl->addResource(new Zend_Acl_Resource('default:empleaves'));
                            $acl->allow('Sysadmin', 'default:empleaves', array('index','view','viewpopup'));

		 $acl->addResource(new Zend_Acl_Resource('default:empskills'));
                            $acl->allow('Sysadmin', 'default:empskills', array('addpopup','editpopup','index','view','viewpopup'));

		 $acl->addResource(new Zend_Acl_Resource('default:disabilitydetails'));
                            $acl->allow('Sysadmin', 'default:disabilitydetails', array('index','view'));

		 $acl->addResource(new Zend_Acl_Resource('default:workeligibilitydetails'));
                            $acl->allow('Sysadmin', 'default:workeligibilitydetails', array('index','view'));

		 $acl->addResource(new Zend_Acl_Resource('default:visaandimmigrationdetails'));
                            $acl->allow('Sysadmin', 'default:visaandimmigrationdetails', array('index','view'));

		 $acl->addResource(new Zend_Acl_Resource('default:creditcarddetails'));
                            $acl->allow('Sysadmin', 'default:creditcarddetails', array('index','view'));

		 $acl->addResource(new Zend_Acl_Resource('default:dependencydetails'));
                            $acl->allow('Sysadmin', 'default:dependencydetails', array('addpopup','editpopup','index','view','viewpopup'));

		 $acl->addResource(new Zend_Acl_Resource('default:empholidays'));
                            $acl->allow('Sysadmin', 'default:empholidays', array('index','view','viewpopup'));

		 $acl->addResource(new Zend_Acl_Resource('default:empjobhistory'));
                            $acl->allow('Sysadmin', 'default:empjobhistory', array('addpopup','editpopup','index','view','viewpopup'));

		 $acl->addResource(new Zend_Acl_Resource('default:empadditionaldetails'));
                            $acl->allow('Sysadmin', 'default:empadditionaldetails', array('addpopup','editpopup','index','view','viewpopup'));

		 $acl->addResource(new Zend_Acl_Resource('default:interviewrounds'));
                            $acl->allow('Sysadmin', 'default:interviewrounds', array('index','addpopup','editpopup','viewpopup','index','addpopup','editpopup','viewpopup','delete'));

		 $acl->addResource(new Zend_Acl_Resource('default:apprreqcandidates'));
                            $acl->allow('Sysadmin', 'default:apprreqcandidates', array('index','viewpopup'));
}if($role == 8 )
           {
		 $acl->addResource(new Zend_Acl_Resource('default:approvedleaves'));
                            $acl->allow('Employees', 'default:approvedleaves', array('index','view','Approved Leaves'));

		 $acl->addResource(new Zend_Acl_Resource('default:businessunits'));
                            $acl->allow('Employees', 'default:businessunits', array('index','getdeptnames','view','Business Units'));

		 $acl->addResource(new Zend_Acl_Resource('default:cancelleaves'));
                            $acl->allow('Employees', 'default:cancelleaves', array('index','view','Cancelled Leaves'));

		 $acl->addResource(new Zend_Acl_Resource('default:dashboard'));
                        $acl->allow('Employees', 'default:dashboard', array('index','saveuserdashboard','getwidgtes','emailsettings','changepassword','editpassword','update','uploadpreview','viewprofile','viewsettings','savemenuwidgets','getmenuname','fetchmenuname','getnavids','getopeningpositondate','menuwork'));

		 $acl->addResource(new Zend_Acl_Resource('default:departments'));
                            $acl->allow('Employees', 'default:departments', array('index','viewpopup','editpopup','getdepartments','getempnames','view','Departments'));

		 $acl->addResource(new Zend_Acl_Resource('default:employee'));
                            $acl->allow('Employees', 'default:employee', array('getemprequi','index','getdepartments','getpositions','getempreportingmanagers','makeactiveinactive','changereportingmanager','addemppopup','view','Employees'));

		 $acl->addResource(new Zend_Acl_Resource('default:heirarchy'));
                            $acl->allow('Employees', 'default:heirarchy', array('index','addlist','editlist','saveadddata','saveeditdata','deletelist','editActi','deletelist_1','Organization Hierarchy'));

		 $acl->addResource(new Zend_Acl_Resource('default:index'));
                        $acl->allow('Employees', 'default:index', array('index','loginpopupsave','welcome','logout','clearsessionarray','forcelogout','browserfailure','sendpassword','updatecontactnumber','getstates','getstatesnormal','getcities','getcitiesnormal','getdepartments','getpositions','gettargetcurrency','calculatedays','calculatebusinessdays','calculatecalendardays','fromdatetodate','fromdatetodateorg','medicalclaimdates','gettimeformat','chkcurrenttime','popup','createorremoveshortcut','sessiontour','getissuingauthority','setsessionval','checkisactivestatus'));

		 $acl->addResource(new Zend_Acl_Resource('default:leaverequest'));
                            $leaverequest_add = 'yes';
                                if($this->id_param == '' && $leaverequest_add == 'yes')
                                    $acl->allow('Employees', 'default:leaverequest', array('index','saveleaverequestdetails','gethalfdaydetails','add','Leave Request','edit'));

                                else
                                    $acl->allow('Employees', 'default:leaverequest', array('index','saveleaverequestdetails','gethalfdaydetails','add','Leave Request'));

                                
		 $acl->addResource(new Zend_Acl_Resource('default:manageremployeevacations'));
                            $acl->allow('Employees', 'default:manageremployeevacations', array('index','edit','view','Manage Employee Leaves'));

		 $acl->addResource(new Zend_Acl_Resource('default:mydetails'));
                            $acl->allow('Employees', 'default:mydetails', array('index','personaldetailsview','personal','communicationdetailsview','communication','skills','education','experience','leaves','holidays','salarydetailsview','salarydetails','certification','creditcarddetailsview','creditcard','visadetailsview','visa','medicalclaims','disabilitydetailsview','disability','dependency','workeligibilitydetailsview','workeligibility','additionaldetailsedit','jobhistory','add','edit','delete','view','My Details'));

		 $acl->addResource(new Zend_Acl_Resource('default:myemployees'));
                            $acl->allow('Employees', 'default:myemployees', array('index','perview','comview','skillsview','expview','eduview','trainingview','additionaldetailsview','jobhistoryview','view','My Team'));

		 $acl->addResource(new Zend_Acl_Resource('default:myholidaycalendar'));
                            $acl->allow('Employees', 'default:myholidaycalendar', array('index','view','My Holiday Calendar'));

		 $acl->addResource(new Zend_Acl_Resource('default:organisationinfo'));
                            $acl->allow('Employees', 'default:organisationinfo', array('index','saveupdate','uploadpreview','validateorgstartdate','getcompleteorgdata','addorghead','Organization Info'));

		 $acl->addResource(new Zend_Acl_Resource('default:pendingleaves'));
                            $acl->allow('Employees', 'default:pendingleaves', array('index','delete','view','Pending Leaves'));

		 $acl->addResource(new Zend_Acl_Resource('default:rejectedleaves'));
                            $acl->allow('Employees', 'default:rejectedleaves', array('index','view','Rejected Leaves'));

		 $acl->addResource(new Zend_Acl_Resource('default:scheduleinterviews'));
                            $acl->allow('Employees', 'default:scheduleinterviews', array('candidatepopup','index','downloadresume','getcandidates','edit','view','Scheduled Interviews'));

		 $acl->addResource(new Zend_Acl_Resource('default:structure'));
                            $acl->allow('Employees', 'default:structure', array('index','Organization Structure'));

		 $acl->addResource(new Zend_Acl_Resource('default:emppersonaldetails'));
                            $acl->allow('Employees', 'default:emppersonaldetails', array('index','view','index','view','edit'));

		 $acl->addResource(new Zend_Acl_Resource('default:empcommunicationdetails'));
                            $acl->allow('Employees', 'default:empcommunicationdetails', array('index','view','viewAction_27','index','view','viewAction_27','edit'));

		 $acl->addResource(new Zend_Acl_Resource('default:trainingandcertificationdetails'));
                            $acl->allow('Employees', 'default:trainingandcertificationdetails', array('index','view','addpopup','editpopup','viewpopup','index','edit','view','addpopup','editpopup','viewpopup','delete'));

		 $acl->addResource(new Zend_Acl_Resource('default:experiencedetails'));
                            $acl->allow('Employees', 'default:experiencedetails', array('index','view','addpopup','editpopup','viewpopup','index','edit','view','addpopup','editpopup','viewpopup','delete'));

		 $acl->addResource(new Zend_Acl_Resource('default:educationdetails'));
                            $acl->allow('Employees', 'default:educationdetails', array('index','view','addpopup','viewpopup','editpopup','index','edit','view','addpopup','viewpopup','editpopup','delete'));

		 $acl->addResource(new Zend_Acl_Resource('default:medicalclaims'));
                            $acl->allow('Employees', 'default:medicalclaims', array('index','addpopup','viewpopup','editpopup','view','index','edit','addpopup','viewpopup','editpopup','delete','view'));

		 $acl->addResource(new Zend_Acl_Resource('default:empleaves'));
                            $acl->allow('Employees', 'default:empleaves', array('index','view','viewpopup','index','edit','view','viewpopup'));

		 $acl->addResource(new Zend_Acl_Resource('default:empskills'));
                            $acl->allow('Employees', 'default:empskills', array('index','view','addpopup','viewpopup','editpopup','index','edit','view','addpopup','viewpopup','editpopup','delete'));

		 $acl->addResource(new Zend_Acl_Resource('default:disabilitydetails'));
                            $acl->allow('Employees', 'default:disabilitydetails', array('index','view','index','add','edit','view'));

		 $acl->addResource(new Zend_Acl_Resource('default:workeligibilitydetails'));
                            $acl->allow('Employees', 'default:workeligibilitydetails', array('index','view','index','add','edit','view'));

		 $acl->addResource(new Zend_Acl_Resource('default:empadditionaldetails'));
                            $acl->allow('Employees', 'default:empadditionaldetails', array('index','view','addpopup','viewpopup','editpopup','index','edit','view','addpopup','viewpopup','editpopup','delete'));

		 $acl->addResource(new Zend_Acl_Resource('default:visaandimmigrationdetails'));
                            $acl->allow('Employees', 'default:visaandimmigrationdetails', array('index','view','index','add','edit','view'));

		 $acl->addResource(new Zend_Acl_Resource('default:creditcarddetails'));
                            $acl->allow('Employees', 'default:creditcarddetails', array('index','view','index','add','edit','view'));

		 $acl->addResource(new Zend_Acl_Resource('default:dependencydetails'));
                            $acl->allow('Employees', 'default:dependencydetails', array('index','view','addpopup','editpopup','viewpopup','index','edit','view','addpopup','editpopup','viewpopup','delete'));

		 $acl->addResource(new Zend_Acl_Resource('default:empholidays'));
                            $acl->allow('Employees', 'default:empholidays', array('index','view','viewpopup','index','edit','view','viewpopup'));

		 $acl->addResource(new Zend_Acl_Resource('default:empjobhistory'));
                            $acl->allow('Employees', 'default:empjobhistory', array('index','view','addpopup','viewpopup','editpopup','index','edit','view','addpopup','viewpopup','editpopup','delete'));

		 $acl->addResource(new Zend_Acl_Resource('default:interviewrounds'));
                            $acl->allow('Employees', 'default:interviewrounds', array('index','addpopup','editpopup','viewpopup','index','addpopup','editpopup','viewpopup','delete'));

		 $acl->addResource(new Zend_Acl_Resource('default:apprreqcandidates'));
                            $acl->allow('Employees', 'default:apprreqcandidates', array('index','viewpopup'));
}

     // setup acl in the registry for more
           Zend_Registry::set('acl', $acl);
           $this->_acl = $acl;
    }
   return $this->_acl;
}
  }
  
  ?>