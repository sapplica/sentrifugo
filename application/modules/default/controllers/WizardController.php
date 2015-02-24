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


class Default_WizardController extends Zend_Controller_Action
{

	private $options;
	public function preDispatch()
	{
		$ajaxContext = $this->_helper->getHelper('AjaxContext');
		$ajaxContext->addActionContext('updatewizardcompletion', 'json')->initContext();
	}

	public function init()
	{
		$this->_options= $this->getInvokeArg('bootstrap')->getOptions();
	}
	
	public function indexAction() {
		$wizard_model = new Default_Model_Wizard();
		$wizardData = $wizard_model->getWizardData();
		if($wizardData['manage_modules'] == 1)
			$this->_redirect('wizard/managemenu');
		else if($wizardData['site_config'] == 1)
			$this->_redirect('wizard/configuresite');
		else if($wizardData['org_details'] == 1)
			$this->_redirect('wizard/configureorganisation');
		else
			$this->_redirect('wizard/managemenu');		
		
	}
	
	public function managemenuAction() {
		$wizard_model = new Default_Model_Wizard();
		$wizardData = $wizard_model->getWizardData();
		$menu_model = new Default_Model_Menu();
        $isactiveArr = $menu_model->getisactivemenus();
        $this->view->isactArr = $isactiveArr;
        $this->view->wizarddata = $wizardData;
        $this->view->messages = $this->_helper->flashMessenger->getMessages();
	}
	
	public function savemenuAction()
    {
    
        $auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity())
        {
            $loginUserId = $auth->getStorage()->read()->id;
        }
        $date = new Zend_Date(); 
        $wizard_model = new Default_Model_Wizard();
		$wizardData = $wizard_model->getWizardData();
		
        $trDb = Zend_Db_Table::getDefaultAdapter();		
        // starting transaction
        $trDb->beginTransaction();	
        try 
        { 
			
            if($this->_request->getPost())
            { 
                $defined_menus = array(TIMEMANAGEMENT,RESOURCEREQUISITION,BGCHECKS,STAFFING,COMPLIANCES,REPORTS,BENEFITS,SERVICEDESK);
                $chk_menu = $this->_request->getParam('chk_menu');// menus to be activate
				$chk_menu = trim($chk_menu,',');
				$logmenus = $chk_menu;
				if($chk_menu != '' && $chk_menu != ',' && !is_array($chk_menu))
				{
					$chk_menu = explode(',',$chk_menu);					
				}
                else 
                    $chk_menu = array();
                $disable_menus = array_diff($defined_menus, $chk_menu); //menus to be deactivated                              
                if(!empty($chk_menu))
                {                                        
                    foreach($chk_menu as $menu)
                    {       
                        $this->save_helper(1,$menu);                                                          
                    }
                                        
                }
                if(!empty($disable_menus))
                {
                    foreach($disable_menus as $menu)
                    {                                                
                        $this->save_helper(0,$menu);                                                          
                    }
                } 

	            // Code to Update Logmanager table with comma separated menuids 
	            		
					 $menumodel = new Default_Model_Menu();
		             $menuNames = $menumodel->getMenusNamesByIds($logmenus); 
                    
		             
	            $logarr = array('userid' => $loginUserId,
	                            'recordid' =>$logmenus,
	                            'childrecordid' => $menuNames,
	                            'date' => $date->get('yyyy-MM-dd HH:mm:ss')
	                            );
	            $jsonlogarr = json_encode($logarr);
	            $menuID = MANAGEMODULE;
	            $actionflag = 2;
	            
                 if(!empty($logmenus))//only activated records are logged in log manager.
                  $menumodel->addOrUpdateMenuLogManager($menuID,$actionflag,$jsonlogarr,$loginUserId,$menuNames);
                  
                 
                  	$wizardarray = array('manage_modules' => 2,
                  						 'modifiedby'=>$loginUserId,
                  						'modifieddate'=>gmdate("Y-m-d H:i:s")
                  					);
					 if($wizardData['site_config'] == 2 && $wizardData['org_details'] == 2)
					 {
					 	$wizardarray['iscomplete'] = 2;
					 }                  					
               		$wizard_model->SaveorUpdateWizardData($wizardarray,'');
                    $trDb->commit();		
                    sapp_Global::generateAccessControl();
               		$this->_helper->getHelper("FlashMessenger")->addMessage("Modules updated successfully.");
               		$this->_redirect('wizard/managemenu');
               		
            }
			else
            {
                $this->_helper->getHelper("FlashMessenger")->addMessage("No Menus were added.");
                $this->_redirect('wizard/managemenu');				  
            } 	
        }
        catch (Exception $e) 
        {		
            $trDb->rollBack();			
            $msg = $e->getMessage();
            $this->_helper->getHelper("FlashMessenger")->addMessage($msg);
            $this->_redirect('managemenu');	  			

        }
    }
    
 	public function save_helper($is_active,$menu)
    {
   
        $auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity())
        {
            $loginUserId = $auth->getStorage()->read()->id;
        } 
        $date = new Zend_Date();
        $menumodel = new Default_Model_Menu();
        $menu_childs = $menumodel->getMenusWithChilds($menu);
        $resArrString = implode(",",$menu_childs);	 
        if($resArrString != '')
        {
            $where = " id in (".$resArrString.")";
            $where_privi = " object in (".$resArrString.")";		
            $querystring_menu = "UPDATE main_menu SET isactive = ".$is_active." where $where  ";
            $menumodel->UpdateMenus($querystring_menu);        
            $querystring_menu = "UPDATE main_privileges SET isactive = ".$is_active." where $where_privi  ";
            $menumodel->UpdateMenus($querystring_menu);
            if(defined('PERFORMANCEAPPRAISAL_M') && $menu == PERFORMANCEAPPRAISAL_M)
            {
                $querystring_menu = "UPDATE main_menu SET isactive = ".$is_active." where id in (".MYPERFORMANCEAPPRAISAL.",".MYTEAMPERFORMANCEAPPRAISAL.")  ";
                $menumodel->UpdateMenus($querystring_menu);

                $querystring_menu = "UPDATE main_privileges SET isactive = ".$is_active." where object in (".MYPERFORMANCEAPPRAISAL.",".MYTEAMPERFORMANCEAPPRAISAL.")  ";
                $menumodel->UpdateMenus($querystring_menu);
            }

           
           
        }
    }
    
    
    public function configuresiteAction()
    {
    	$auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity())
        {
            $loginUserId = $auth->getStorage()->read()->id;
            $loginuserRole = $auth->getStorage()->read()->emprole;
            $loginuserGroup = $auth->getStorage()->read()->group_id;
        }
        $popConfigPermission = array();
        $msgarray = array();
		
        $wizardpreferenceform = new Default_Form_wizardpreference();	
        $dateformatidmodel = new Default_Model_Dateformat();
        $timeformatidmodel = new Default_Model_Timeformat();	
        $currencyidmodel = new Default_Model_Currency();
        $systempreferencemodel = new Default_Model_Sitepreference();
        $orginfomodel = new Default_Model_Organisationinfo();
        $identitycodesmodel = new Default_Model_Identitycodes(); 
        $employmentstatusmodel = new Default_Model_Employmentstatus();
        $statesmodel = new Default_Model_States();
        $citiesmodel = new Default_Model_Cities();
        $date_formats_arr = array();
        $time_formats_arr = array();
        $passworddataArr = array();
        $currencynameArr = array();
        $countryId = '';
        $stateId = '';
        $cityId = '';  
        $new_stateId = '';
        $new_cityId = '';  
        $empstatusids = '';
        $timezonemodel = new Default_Model_Timezone();
        $wizard_model = new Default_Model_Wizard();
		$wizardData = $wizard_model->getWizardData();
		
		/* START
		 * Queries to check whether the configuration are already set.
		 * If set then prepopulate the fields
		 */
		
		$sitepreferencedata = $systempreferencemodel->SitePreferanceData();
		$orginfodata = $orginfomodel->getOrganisationInfo();
		$empstatusdata = $employmentstatusmodel->getEmploymentStatuslist();
		$identitycodedata = $identitycodesmodel->getIdentitycodesRecord();
		
		/*
		 * END - Checking configuration is set or not.
		 */
				
        $allTimezoneData = $timezonemodel->fetchAll('isactive=1','timezone')->toArray();
        if(sapp_Global::_checkprivileges(CURRENCY,$loginuserGroup,$loginuserRole,'add') == 'Yes'){
			array_push($popConfigPermission,'currency');
		}  
		if(sapp_Global::_checkprivileges(TIMEZONE,$loginuserGroup,$loginuserRole,'add') == 'Yes'){
			array_push($popConfigPermission,'timezone');
		} 
            try
            {
            	if(!empty($orginfodata))
                {
                	$countryId = isset($orginfodata[0]['country'])?$orginfodata[0]['country']:"";
                    $stateId = isset($orginfodata[0]['state'])?$orginfodata[0]['state']:"";
                    $cityId = isset($orginfodata[0]['city'])?$orginfodata[0]['city']:"";
                    $wizardpreferenceform->organisationid->setValue($orginfodata[0]['id']);
                	
                }else
                {
                	if(isset($wizardData['country']) && $wizardData['country'] !='null')
                	 $countryId = $wizardData['country'];
                    if(isset($wizardData['state']) && $wizardData['state'] !='null')
                	 $stateId = $wizardData['state'];
                	if(isset($wizardData['city']) && $wizardData['city'] !='null')
                	 $cityId = $wizardData['city']; 	 
                }
            	if(count($_POST) > 0)
                {
                    $countryId = isset($_POST['perm_country'])?$_POST['perm_country']:"";
                    $stateId = isset($_POST['perm_state'])?$_POST['perm_state']:"";
                    $cityId = isset($_POST['perm_city'])?$_POST['perm_city']:"";                                    
                }
                $date_formats_arr = $dateformatidmodel->getAllDateFormats();
                $time_formats_arr = $timeformatidmodel->fetchAll()->toArray();           		
                $defaultempstatusdata = $employmentstatusmodel->getCompleteStatuslist();
                $wizardpreferenceform->passwordid->addMultiOption('','Select Password Preference');	 
                $passworddataArr = $systempreferencemodel->getPasswordData();	
                foreach($passworddataArr as $passwordres)
                {
                    $wizardpreferenceform->passwordid->addMultiOption($passwordres['id'],utf8_encode($passwordres['passwordtype']));
                }
                if(sizeof($allTimezoneData) > 0)
                {                
                    foreach ($allTimezoneData as $timezoneidres)
                    {
                        $wizardpreferenceform->timezoneid->addMultiOption($timezoneidres['id'],utf8_encode($timezoneidres['timezone'].' ['.$timezoneidres['timezone_abbr'].']'));
                    } 
                }
                else
                {		
                    $msgarray['timezoneid'] = 'Time Zone is not configured yet.';
                }
                
            	if(sizeof($defaultempstatusdata) > 0)
                {            
                    foreach ($defaultempstatusdata as $empstatusres)
                    {
                        $wizardpreferenceform->workcodename->addMultiOption($empstatusres['id'],utf8_encode($empstatusres['employemnt_status']));
                    }
                }
                
                /* Start  
                 * To prepopulate the form if already configured
                 */
                
                if(!empty($sitepreferencedata))
                {
                	if(isset($sitepreferencedata[0]['dateformatid']))
                		$wizardpreferenceform->setDefault('dateformatid',$sitepreferencedata[0]['dateformatid']);
                	if(isset($sitepreferencedata[0]['timeformatid']))	
                		$wizardpreferenceform->setDefault('timeformatid',$sitepreferencedata[0]['timeformatid']);
                	if(isset($sitepreferencedata[0]['timezoneid']))	
                		$wizardpreferenceform->setDefault('timezoneid',$sitepreferencedata[0]['timezoneid']);
                	if(isset($sitepreferencedata[0]['passwordid']))	
                		$wizardpreferenceform->setDefault('passwordid',$sitepreferencedata[0]['passwordid']);	
                	if(isset($sitepreferencedata[0]['currencyid']))
                		{
                			$wizardpreferenceform->currencyid->setValue($sitepreferencedata[0]['currencyid']);
                			$currencynameArr = $currencyidmodel->getCurrencyDataByID($sitepreferencedata[0]['currencyid']);
                		}	
                	if(!empty($currencynameArr))
                	{	
                		$wizardpreferenceform->currencyname->setValue($currencynameArr[0]['currencyname']);
                		$wizardpreferenceform->currencycode->setValue($currencynameArr[0]['currencycode']);
                	}	
                }
               
                if($countryId !='')
                {
                		$wizardpreferenceform->setDefault('perm_country',$countryId);
                		$statesData = $statesmodel->getStatesList((int)$countryId);
                		if(!empty($statesData))
                		{
		                    foreach($statesData as $res)
		                    {
		                    	if($stateId == $res['id'])
                            		$new_stateId = $res['id'].'!@#'.utf8_encode($res['state_name']);
		                        $wizardpreferenceform->perm_state->addMultiOption($res['id'].'!@#'.utf8_encode($res['state_name']),utf8_encode($res['state_name']));
		                    }
		                     if(count($_POST) == 0)
                        		$stateId = $new_stateId;
                		}
                }
                if($stateId !='')
                {
                	$wizardpreferenceform->setDefault('perm_state',$stateId);
                 	$citiesData = $citiesmodel->getCitiesList((int)$stateId);
                    foreach($citiesData as $res)
                    {
                    	if($cityId == $res['id'])
                            $new_cityId = $res['id'].'!@#'.utf8_encode($res['city_name']);
                        $wizardpreferenceform->perm_city->addMultiOption($res['id'].'!@#'.utf8_encode($res['city_name']),utf8_encode($res['city_name']));
                    }
                    
                    if(count($_POST) == 0)
                        $cityId = $new_cityId;
                }

                if($cityId !='')
                	$wizardpreferenceform->setDefault('perm_city',$cityId);
                
                
                if(!empty($identitycodedata))
                {
                	$wizardpreferenceform->empcodeid->setValue($identitycodedata[0]['id']);
                	$wizardpreferenceform->employee_code->setValue($identitycodedata[0]['employee_code']);
                }
                
            	if(sizeof($empstatusdata) > 0)
                {            
                    foreach ($empstatusdata as $empstats)
                    {
                        $empstatusids.= $empstats['workcodename'].',';
                    }
                    $empstatusids = rtrim($empstatusids,',');
                }
                
                /*
                 * End - Prepopulating data
                 */
                
                $wizardpreferenceform->setAttrib('action',DOMAIN.'wizard/configuresite');
                $this->view->msgarray = $msgarray;
            }
            catch(Exception $e)
            {
                $this->view->nodata = "nodata";
            }
        $this->view->form = $wizardpreferenceform;
        $this->view->date_formats_arr = $date_formats_arr;
        $this->view->time_formats_arr = $time_formats_arr;
        $this->view->passworddata = $passworddataArr;
        $this->view->empstatusids = $empstatusids;
        if($this->getRequest()->getPost())
        {
            $result = $this->savesitepreference($wizardpreferenceform,$wizardData);	
            $this->view->msgarray = $result; 
        }
		$this->view->popConfigPermission = $popConfigPermission;
		$this->view->wizarddata = $wizardData;
		$this->view->messages = $this->_helper->flashMessenger->getMessages();
    	
    }
    
    public function savesitepreference($wizardpreferenceform,$wizardData)
    {
    $auth = Zend_Auth::getInstance();
        if($auth->hasIdentity())
        {
            $loginUserId = $auth->getStorage()->read()->id;
        } 
        if($wizardpreferenceform->isValid($this->_request->getPost()))
        {
            $trDb = Zend_Db_Table::getDefaultAdapter();		
            // starting transaction
            $trDb->beginTransaction();
            try
            {
                $systempreferencemodel = new Default_Model_Sitepreference();
                $currencymodel = new Default_Model_Currency(); 
                $orgInfoModel = new Default_Model_Organisationinfo();
                $wizard_model = new Default_Model_Wizard();
                $IdentityCodesModel = new Default_Model_Identitycodes();
                $employmentstatusmodel = new Default_Model_Employmentstatus();
                $countriesmodel = new Default_Model_Countries();
                $statesmodel = new Default_Model_States();
                $citiesmodel = new Default_Model_Cities();
                
                $id = (int)$this->_request->getParam('id'); 
                $currencyid = (int)$this->_request->getParam('currencyid');
                $organisationid = (int)$this->_request->getParam('organisationid');
                $empcodeid = (int)$this->_request->getParam('empcodeid');
                $dateformatid = $this->_request->getParam('dateformatid');
                $timeformatid = $this->_request->getParam('timeformatid');
                $timezoneid = $this->_request->getParam('timezoneid');
                $currencyname = $this->_request->getParam('currencyname');
                $currencycode = $this->_request->getParam('currencycode');
                $passwordid = $this->_request->getParam('passwordid');
                $perm_country = $this->_request->getParam('perm_country');
				$perm_stateparam = $this->_request->getParam('perm_state');
				$perm_stateArr = explode("!@#",$this->_request->getParam('perm_state'));
				$perm_state = $perm_stateArr[0];
				$perm_cityparam = $this->_request->getParam('perm_city');
				$perm_cityArr = explode("!@#",$this->_request->getParam('perm_city'));
				$perm_city = $perm_cityArr[0];
                $employee_code = $this->_request->getParam('employee_code');
                $workcodename = $this->_request->getParam('workcodename');
                $date = new Zend_Date();
                $menumodel = new Default_Model_Menu();
                
                /*
                 * Save or Update - Currency name in currency table based on currency ID
                 */
	                $currency_data = array('currencyname'=>trim($currencyname),
					                       'currencycode'=>trim($currencycode),
							  	           'modifiedby'=>$loginUserId,
								  	       'modifieddate'=>gmdate("Y-m-d H:i:s")
					);
					if($currencyid !='')
					{
						$currencywhere = array('id=?'=>$currencyid);
					}
					else
					{
						$currency_data['createdby'] = $loginUserId;
						$currency_data['createddate'] = gmdate("Y-m-d H:i:s");
						$currency_data['isactive'] = 1;
						$currencywhere = '';
					}
					
					$CurrencyId = $currencymodel->SaveorUpdateCurrencyData($currency_data, $currencywhere);
				/*
				 * End 
				 */
                
				/*
				 * Start -  Updating and Inserting Site Preference Data after fetching currency id
				 */
                
                $siteprference_data = array( 
                                'dateformatid'=>$dateformatid,
                                'timeformatid'=>$timeformatid,
                                'timezoneid'=>$timezoneid,
                                'currencyid'=>$currencyid!=''?$currencyid:$CurrencyId,
                                'passwordid'=>$passwordid, 								 
                				'createdby'=>$loginUserId,
                                'createddate'=>$date->get('yyyy-MM-dd HH:mm:ss'),
                                'modifiedby'=>$loginUserId,
                                'modifieddate'=>$date->get('yyyy-MM-dd HH:mm:ss'),
                				'isactive'=>1,
                            );
                
                $site_update_arr = array(
                                    'isactive' => 0,
                                    'modifieddate'=>$date->get('yyyy-MM-dd HH:mm:ss'),
                                    'modifiedby'=>$loginUserId,                                                
                                );
                 
                $systempreferencemodel->SaveorUpdateSystemPreferanceData($site_update_arr, 'isactive = 1'); 
                $Id = $systempreferencemodel->SaveorUpdateSystemPreferanceData($siteprference_data, '');
                
                /*
                 *  End
                 */
                
                /*
                 * Updating Country,state and city based on organisation id
                 */
                	// Inserting into main_countries if not added
                	$countryExistsArr = $countriesmodel->getActiveCountryName($perm_country);
                	if(empty($countryExistsArr))
                	{
	                	$countrynamearr = $countriesmodel->getCountryCode($perm_country);	
	            		if(!empty($countrynamearr))
						{
							 $country_data = array('country'=>trim($countrynamearr[0]['country_name']),
										           'countrycode'=>trim($countrynamearr[0]['country_code']),
												  'citizenship'=>NULL,
							 					  'createdby'=>$loginUserId,
	                                			  'createddate'=>$date->get('yyyy-MM-dd HH:mm:ss'),	
												  'modifiedby'=>$loginUserId,
												  'modifieddate'=>gmdate("Y-m-d H:i:s"),
												  'country_id_org'=>$perm_country,
							 					  'isactive'=>1	
							);
							$Country_Id = $countriesmodel->SaveorUpdateCountryData($country_data, '');
						}
                	}
					
					// Inserting into main_state if not added
						$State_Id = $statesmodel->SaveorUpdateStatesData($perm_country,$perm_stateArr[1],$perm_state,$loginUserId);
					
					// Inserting into main_cities if not added
						$City_Id = $citiesmodel->SaveorUpdateCitiesData($perm_country,$perm_state,$perm_cityArr[1],$perm_city,$loginUserId);
                
                 	$location_data = array('country'=>$perm_country,
					                       'state'=>$perm_state,
							  	           'city'=>$perm_city,
                 							'createdby'=>$loginUserId,
                                			 'createddate'=>$date->get('yyyy-MM-dd HH:mm:ss'),
                 							'modifiedby'=>$loginUserId,
								  	       'modifieddate'=>gmdate("Y-m-d H:i:s"),
                 							'isactive'=>1	
					);
					if($organisationid !='')
					{
						$locwhere = array('id=?'=>$organisationid);
						$LocationId = $orgInfoModel->SaveorUpdateData($location_data, $orgwhere);
					}
						
						$LocationId = $wizard_model->SaveorUpdateWizardData($location_data, '');
                
                /*
                 * End
                 */
					
				/*
				 * Start - Updating Employee Code
				 */	
					$empcode_data = array('employee_code'=>trim($employee_code),
							  	           'modifiedBy'=>$loginUserId,
								  	       'modifieddate'=>gmdate("Y-m-d H:i:s")
					);
					if($empcodeid !='')
					{
						$empcodewhere = array('id=?'=>$empcodeid);
					}
					else
					{
						$empcode_data['createdby'] = $loginUserId;
						$empcode_data['createddate'] = gmdate("Y-m-d H:i:s");
						$empcodewhere = '';
					}
					
					$EmpCodeId = $IdentityCodesModel->SaveorUpdateIdentitycodesData($empcode_data, $empcodewhere);
				/*
				 * End
				 */
					
				/*
				 * Start - Update employment status data
				 */	
					if(!empty($workcodename))
					{
						$empstat_update_arr = array(
				                                    'isactive' => 0,
				                                    'modifieddate'=>$date->get('yyyy-MM-dd HH:mm:ss'),
				                                    'modifiedby'=>$loginUserId,                                                
                                					);
						$Empstat_update_Id = $employmentstatusmodel->SaveorUpdateEmploymentStatusData($empstat_update_arr, 'isactive=1');
						                                					
						for($j=0;$j<sizeof($workcodename);$j++)
					   	{
					   		switch ($workcodename[$j])
					   			 {
								    case 1:
								        $workcode = 'FT';
								        break;
								    case 2:
								        $workcode = 'PT';
								        break;    
								    case 3:
								        $workcode = 'PERM';
								        break;
								    case 4:
								        $workcode = 'TEMP';
								        break;
							        case 5:
								        $workcode = 'PROB';
								        break;
							        case 6:
								        $workcode = 'CONT';
								        break;
							        case 7:
								        $workcode = 'DEP';
								        break;
							        case 8:
								        $workcode = 'RES';
								        break;
								    case 9:
								        $workcode = 'LEFT';
								        break;
								    case 10:
								        $workcode = 'SUSP';
								        break;            
								    default:
								        $workcode = 'FT';
								}	
								
								$empstatus_data = array('workcode'=>trim($workcode),
									          'workcodename'=>trim($workcodename[$j]),
											  'createdby'=>$loginUserId,
											  'createddate'=>gmdate("Y-m-d H:i:s"),	
											  'modifiedby'=>$loginUserId,
											  'modifieddate'=>gmdate("Y-m-d H:i:s"),
											  'isactive'=>1	
											);
							  $Empstat_Id = $employmentstatusmodel->SaveorUpdateEmploymentStatusData($empstatus_data, '');				
					   }
					}
				/*
				 * End
				 */	
					
				/*
				 * Update Wizard Table
				 */	
					
					$wizardarray = array('site_config' => 2,
                  						 'modifiedby'=>$loginUserId,
                  						'modifieddate'=>gmdate("Y-m-d H:i:s")
                  					);
            		if($wizardData['org_details'] == 2)
					 {
					 	$wizardarray['iscomplete'] = 2;
					 }                  					
               		$wizard_model->SaveorUpdateWizardData($wizardarray,'');
               		$trDb->commit();
                	$this->_helper->getHelper("FlashMessenger")->addMessage("Site Preference updated successfully.");
                	$this->_redirect('wizard/configuresite');	
            }
            catch (Exception $e) 
            {
                $trDb->rollBack();
                $this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Something went wrong,please try again later."));
                 $this->_redirect('wizard/configuresite');					   
            }
           		
        }
        else
        {
            $messages = $wizardpreferenceform->getMessages();
            foreach ($messages as $key => $val)
            {
                foreach($val as $key2 => $val2)
                {
                    $msgarray[$key] = $val2;
                    break;
                }
            }
            return $msgarray;	
        }
    	
    }
    
    public function configureorganisationAction()
    {
    	$auth = Zend_Auth::getInstance();
        if($auth->hasIdentity())
        {
            $loginUserId = $auth->getStorage()->read()->id;
            $loginuserRole = $auth->getStorage()->read()->emprole;
            $loginuserGroup = $auth->getStorage()->read()->group_id;
        }
        $popConfigPermission = array();
        $new_stateId ='';
		if(sapp_Global::_checkprivileges(COUNTRIES,$loginuserGroup,$loginuserRole,'add') == 'Yes'){
                array_push($popConfigPermission,'country');
        }
		if(sapp_Global::_checkprivileges(STATES,$loginuserGroup,$loginuserRole,'add') == 'Yes'){
                array_push($popConfigPermission,'state');
        }
		if(sapp_Global::_checkprivileges(CITIES,$loginuserGroup,$loginuserRole,'add') == 'Yes'){
                array_push($popConfigPermission,'city');
        }
        $msgarray = array();
        $new_stateId = '';
        $new_cityId = '';
        $id = $this->getRequest()->getParam('id');
        $form = new Default_Form_Organisationinfo();
        $orgInfoModel = new Default_Model_Organisationinfo();
        $countriesModel = new Default_Model_Countries();
        $statesmodel = new Default_Model_States();
        $citiesmodel = new Default_Model_Cities();
        $wizard_model = new Default_Model_Wizard();
		$wizardData = $wizard_model->getWizardData();
        $orginfodata = $orgInfoModel->getOrganisationInfo();
        $allCountriesData = $countriesModel->fetchAll('isactive=1','country')->toArray();
        $allStatesData = $statesmodel->fetchAll('isactive=1','state')->toArray();
        $allCitiesData = $citiesmodel->fetchAll('isactive=1','city')->toArray();
        $form->setAttrib('action',DOMAIN.'wizard/configureorganisation');
        $flag = 'true';
        if(empty($allCountriesData))
        {
                $msgarray['country'] = 'Countries are not configured yet.';
                $flag = 'false';
        }
        if(empty($allStatesData))
        {
                $msgarray['state'] = 'States are not configured yet.';
                $flag = 'false';
        }
        if(empty($allCitiesData))
        {
                $msgarray['city'] = 'Cities are not configured yet.';
                $flag = 'false';
        }
        if(!empty($orginfodata))
        {
            try 
            {
                $data = $orginfodata[0];
                $data['org_startdate'] = sapp_Global::change_date($data['org_startdate'],'view');
                $form->populate($data);
                $countryId = $data['country'];
                $stateId = $data['state'];
                $cityId = $data['city'];
                $actionpage = 'edit';                
                if(count($_POST) > 0)
                {
                    $countryId = isset($_POST['country'])?$_POST['country']:"";
                    $stateId = isset($_POST['state'])?$_POST['state']:"";
                    $cityId = isset($_POST['city'])?$_POST['city']:"";                                    
                }
                if($countryId != '')
                {
                    $statesData = $statesmodel->getBasicStatesList((int)$countryId);
                    foreach($statesData as $res)
                    {
                        if($stateId == $res['state_id_org'])
                            $new_stateId = $res['state_id_org'].'!@#'.utf8_encode($res['state']);
                        $form->state->addMultiOption($res['state_id_org'].'!@#'.utf8_encode($res['state']),utf8_encode($res['state']));
                    }
                    if(count($_POST) == 0)
                        $stateId = $new_stateId;
                }
                if($stateId != '')
                {
                    $citiesData = $citiesmodel->getBasicCitiesList((int)$stateId);

                    foreach($citiesData as $res)
                    {
                        if($cityId == $res['city_org_id'])
                            $new_cityId = $res['city_org_id'].'!@#'.utf8_encode($res['city']);
                        $form->city->addMultiOption($res['city_org_id'].'!@#'.utf8_encode($res['city']),utf8_encode($res['city']));
                    }
                    if(count($_POST) == 0)
                        $cityId = $new_cityId;
                }
                $form->setDefault('country',$countryId);
                $form->setDefault('state',$stateId);
                $form->setDefault('city',$cityId);
                $this->view->domainValue = $data['domain'];
                $this->view->org_image = $data['org_image'];
                $this->view->ermsg = '';
                $this->view->datarr = $data;
            }
            catch(Exception $e)
            {
                $this->view->ermsg = 'nodata';
            }
        }else
        {
        			sapp_Global::buildlocations($form,$wizardData);
        }
        $this->view->form = $form;
        if(!empty($allCountriesData) && !empty($allStatesData) && !empty($allCitiesData))
        {
            $this->view->configuremsg = '';
        }else{
            $this->view->configuremsg = 'notconfigurable';
        }
        
        $this->view->wizarddata = $wizardData;
        $this->view->msgarray = $msgarray;
        $this->view->popConfigPermission = $popConfigPermission;
        $this->view->messages = $this->_helper->flashMessenger->getMessages();
        if($this->getRequest()->getPost())
        {
        	$result = $this->saveorginfo($form,$loginUserId,$wizardData);
			$this->view->msgarray = $result;
			if(isset($this->msgarray['domain'])) 
            $this->view->msMsg = 'multiselecterror';
        }
        
    }
    
    
	public function saveorginfo($form,$loginUserId,$wizardData)
    {
    		$orgInfoModel = new Default_Model_Organisationinfo();
    		$wizard_model = new Default_Model_Wizard();
    		$id = $this->getRequest()->getParam('id');
            $imagerror = $this->_request->getParam('imgerr');
            $imagepath = $this->_request->getParam('org_image_value');
            $imgerrmsg = $this->_request->getParam('imgerrmsg');
            $pphnumber = $this->_request->getParam('phonenumber');
            $sphnumber = $this->_request->getParam('secondaryphone');
            $org_startdate = sapp_Global::change_date($this->_request->getParam('org_startdate'),'database');

            $flag = 'true';
            if(isset($imagepath) && $imagepath != '')
            {                
                $imageArr = explode('.',$imagepath);
                if(sizeof($imageArr) > 1)
                {
                    $imagename = $imageArr[0]; $imageext = $imageArr[1];
                    $extArr = array('gif', 'jpg', 'jpeg', 'png');
                    if(!in_array($imageext, $extArr))
                    {
                        $msgarray['org_image_value'] = 'Please upload an appropriate image file.';
                        $flag = 'false';
                    }
                }
                else
                {
                    $msgarray['org_image_value'] = 'Please upload an appropriate image file.';
                    $flag = 'false';
                }
            }
            if($imagerror == 'error')
            {
                if($imgerrmsg != '' && $imgerrmsg != 'undefined')
                    $msgarray['org_image_value'] = $imgerrmsg;
                else
                    $msgarray['org_image_value'] = 'Please upload an appropriate image file.';
                $flag = 'false';
            }
            if($pphnumber == $sphnumber && $sphnumber != '' && $pphnumber != '')
            {
                $msgarray['secondaryphone'] = 'Please enter different phone number.';
                $flag = 'false';
            }
            if($form->isValid($this->_request->getPost()) && $flag != 'false')                    
            { 
				$domain = $this->_request->getParam('domain'); 
				$domain = implode(',',$domain);
				$date = new Zend_Date();
				$data = array(
							'organisationname'=> trim($this->_request->getParam('organisationname')),
							'domain' =>trim($domain),
							'website' => trim($this->_request->getParam('website')),
							'org_image'=> $imagepath,
							'orgdescription'=>trim($this->_request->getParam('orgdescription')),
							'totalemployees'=>trim($this->_request->getParam('totalemployees')),
							'org_startdate' => ($org_startdate!=''?$org_startdate:NULL), 
							'phonenumber'=>trim($this->_request->getParam('phonenumber')),
							'secondaryphone' =>trim($this->_request->getParam('secondaryphone')),
							'faxnumber' => trim($this->_request->getParam('faxnumber')),
							'country' => trim((int)$this->_request->getParam('country')),
							'state' => trim(intval($this->_request->getParam('state'))),
							'city' => trim(intval($this->_request->getParam('city'))),
							'address1' => trim($this->_request->getParam('address1')),
							'address2' => trim($this->_request->getParam('address2')),
							'address3' => trim($this->_request->getParam('address3')),
							'description' => trim($this->_request->getParam('description')),
							//'orghead' => trim($this->_request->getParam('orghead')),
							'designation' => trim($this->_request->getParam('jobtitle_id',null)),
							'modifiedby' =>	$loginUserId,
							'modifieddate' => gmdate("Y-m-d H:i:s")
						);
				
				$db = Zend_Db_Table::getDefaultAdapter();	
				$db->beginTransaction();
				try
				{
		  
					$path = IMAGE_UPLOAD_PATH;
					$imagepath = $this->_request->getParam('org_image_value');
					$filecopy = 'success';
					if($imagepath !='')
					{
						$filecopy = 'error';
						if(file_exists(USER_PREVIEW_UPLOAD_PATH.'//'.$imagepath))
						{
							try
							{
								if(copy(USER_PREVIEW_UPLOAD_PATH.'//'.$imagepath, $path.'//'.$imagepath))
									$filecopy = 'success';
								unlink(USER_PREVIEW_UPLOAD_PATH.'//'.$imagepath);

							}
							catch(Exception $e)
							{
								echo $msgarray['org_image_value'] = $e->getMessage();exit;
							}
						}
					}
					$where = array('id=?'=>$id);
					if($imagepath == '')
						unset($data['org_image']);
					else if($filecopy == 'error')
						unset($data['org_image']);
					if($id!='')
					{
						$where = array('id=?'=>$id);
						$actionflag = 2;
					}
					else
					{
						$data['createdby'] = $loginUserId;					
						$data['createddate'] = gmdate("Y-m-d H:i:s");
						$data['isactive'] = 1;
						$where = '';
						$actionflag = 1;
					}
					$Id = $orgInfoModel->SaveorUpdateData($data, $where);
									
					$menuID = ORGANISATIONINFO;
					try 
					{
						if($Id != '' && $Id != 'update')
						$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$Id);
						else 
						$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$id);
					}
					catch(Exception $e) 
					{ 
						echo $e->getMessage();
					}
					
					
					
					$wizardarray = array('org_details' => 2,
                  						 'modifiedby'=>$loginUserId,
                  						'modifieddate'=>gmdate("Y-m-d H:i:s")
                  					);
					if($wizardData['site_config'] == 2)
					 {
					 	$wizardarray['iscomplete'] = 2;
					 }                  					
               		$wizard_model->SaveorUpdateWizardData($wizardarray,'');
               		
               		$location_data = array('country' => trim((int)$this->_request->getParam('country')),
											'state' => trim(intval($this->_request->getParam('state'))),
											'city' => trim(intval($this->_request->getParam('city'))),
                 							'modifiedby'=>$loginUserId,
								  	       'modifieddate'=>gmdate("Y-m-d H:i:s"),
					);
					
					$LocationId = $wizard_model->SaveorUpdateWizardData($location_data, '');
					
               		$db->commit();
               			if($filecopy == 'success')
							$this->_helper->getHelper("FlashMessenger")->addMessage("Organization information saved successfully.");
						else
							$this->_helper->getHelper("FlashMessenger")->addMessage("Organization information saved successfully but failed to upload the logo.");
               			$this->_redirect('wizard/configureorganisation');
                }
				catch(Exception $e)
				{	
					$db->rollBack();
					$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Something went wrong,please try again later."));
					$this->_redirect('wizard/configureorganisation');
				}
            }
            else
            {
                $messages = $form->getMessages();
                foreach ($messages as $key => $val)
                {
                    foreach($val as $key2 => $val2)
                    {
                        $msgarray[$key] = $val2;
                        break;
                    }
                    
                }
                //echo '<pre>';print_r($messages);exit;
                return $msgarray;
                
            }			
    }
    
    public function updatewizardcompletionAction()
    {
    	$this->_helper->layout->disableLayout();
    	$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
		}
		$result['result'] = '';
    	$wizard_model = new Default_Model_Wizard();
    	$db = Zend_Db_Table::getDefaultAdapter();	
		$db->beginTransaction();
		try
		{
	    	$Completion_data = array( 'iscomplete'=>0,
	                 				'modifiedby'=>$loginUserId,
									'modifieddate'=>gmdate("Y-m-d H:i:s"),
						);
			$CompleteId = $wizard_model->SaveorUpdateWizardData($Completion_data, '');
			$db->commit();
			$result['result'] = 'success';
		}
		catch(Exception $e)
		{	
			$db->rollBack();
			$result['result'] = 'fail';
		}
		$this->_helper->json($result);			
    }
	
	
}

