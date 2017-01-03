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

class Default_EmpconfigurationController extends Zend_Controller_Action
{

	private $options;
	private $empConfigureArray;
	public function preDispatch()
	{
			

	}

	public function init()
	{
		$this->_options= $this->getInvokeArg('bootstrap')->getOptions();
		$this->empConfigureArray = array(		
								   'employeedocs' => 'Employee Documents',
									'emp_leaves' => 'Employee Leaves',
									'emp_leaves' => 'Employee Leaves',
								   'emp_holidays' => 'Employee Holidays',
								   'emp_salary' => 'Salary Details',
								   'emppersonaldetails'=>'Personal Details',
								   'empcommunicationdetails'=>'Contact Details',
								   'emp_skills' => 'Employee Skills',
								   'emp_jobhistory' => 'Employee Job History',
								   'experience_details' => 'Experience Details',
								   'education_details' => 'Education  Details',
								   'trainingandcertification_details' => 'Training & Certification  Details',
								   'medical_claims' => 'Medical Claims',
								   'disabilitydetails' => 'Disability Details',
								   'dependency_details' => 'Dependency Details',
								   'visadetails' => 'Visa and Immigration Details',
								   'creditcarddetails' => 'Corporate Card Details',
								   'workeligibilitydetails' => 'Work Eligibility Details',
								   'emp_additional' => 'Additional Details',
								   //'emp_performanceappraisal' => 'Performance Appraisal',
								   'emp_payslips' => 'Pay Slips',
								   'emp_benifits' => 'Benefits',
								   'emp_renumeration' => 'Remuneration Details',
								   'emp_security' => 'Security Credentials',
								   'assetdetails' => 'Asset Details'
								   );

	}


	public function indexAction()
	{
		$dataArray = array();
		if(defined('EMPTABCONFIGS'))
		{
			$empOrganizationTabs = explode(",",EMPTABCONFIGS);

			if(!empty($empOrganizationTabs) && !empty($empOrganizationTabs[0])){
				foreach($this->empConfigureArray as $key => $val){
					if(in_array($key,$empOrganizationTabs)){
						$dataArray[$val] = 'YES';
					}else{
						$dataArray[$val] = 'NO';
					}
				}
			}
		}
		$this->view->dataArray = $dataArray;
		$this->view->messages = $this->_helper->flashMessenger->getMessages();
	}

	public function editAction()
	{
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
			$loginuserGroup = $auth->getStorage()->read()->group_id;
		}

		$empconfigurationform = new Default_Form_empconfiguration();

		$empconfigurationform->setAttrib('action',BASE_URL.'empconfiguration/edit');

		if($this->_request->getPost()){
			if($empconfigurationform->isValid($this->_request->getPost()))
			{
				$checktype = $this->_request->getParam('checktype');
				$emptab = sapp_Global::generateEmpTabConstants($checktype);
				$this->_helper->getHelper("FlashMessenger")->addMessage($emptab);
				$this->_redirect('empconfiguration');

			}else
			{
				$messages = $empconfigurationform->getMessages();
				foreach ($messages as $key => $val)
				{
					foreach($val as $key2 => $val2)
					{
						$msgarray[$key] = $val2;
						break;
					}
				}

				$this->view->msgarray = $msgarray;
			}
		}
		if(defined('EMPTABCONFIGS'))
		{
			$empOrganizationTabs = explode(",",EMPTABCONFIGS);

			if(!$this->_request->getPost()){
				if(!empty($empOrganizationTabs)){
					$empconfigurationform->setDefaults(array('checktype'=>$empOrganizationTabs));

					$keysempConfigArray = array_keys($this->empConfigureArray);
					if(count($keysempConfigArray) == count($empOrganizationTabs)){
						$empconfigurationform->setDefault('checkall',1);
					}
				}
			}
		}
		$this->view->form = $empconfigurationform;
		$this->view->succesmsg = $this->_helper->flashMessenger->getMessages();
	}

	public function addAction()
	{
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
			$loginuserGroup = $auth->getStorage()->read()->group_id;
		}
		$empconfigurationform = new Default_Form_empconfiguration();
		$empconfigurationform->setAttrib('action',BASE_URL.'empconfiguration/add');
		if($loginuserGroup == ''){
			$checkTypeArray = array_keys($this->empConfigureArray);
		}
		if(!empty($checkTypeArray)){
			$empconfigurationform->setDefaults(array('checktype'=>$checkTypeArray));
			$empconfigurationform->setDefault('checkall',1);
		}
		$this->view->form = $empconfigurationform;
		$this->view->succesmsg = $this->_helper->flashMessenger->getMessages();

		if($this->_request->getPost()){
			if($empconfigurationform->isValid($this->_request->getPost()))
			{
				$checktype = $this->_request->getParam('checktype');
				$emptab = sapp_Global::generateEmpTabConstants($checktype);
				$this->_helper->getHelper("FlashMessenger")->addMessage($emptab);
				$this->_redirect('empconfiguration');
			}else
			{
				$messages = $empconfigurationform->getMessages();
				foreach ($messages as $key => $val)
				{
					foreach($val as $key2 => $val2)
					{
						$msgarray[$key] = $val2;
						break;
					}
				}

				$this->view->msgarray = $msgarray;
			}
		}
	}
}
