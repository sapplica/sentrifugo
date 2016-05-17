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

class Timemanagement_ConfigurationController extends Zend_Controller_Action
{

	private $options;
	public function preDispatch()
	{
		/*$userModel = new Timemanagement_Model_Users();
		$checkTmEnable = $userModel->checkTmEnable();

		if(!$checkTmEnable){
			$this->_redirect('error');
		}*/
		
		//check Time management module enable
		if(!sapp_Helper::checkTmEnable())
			$this->_redirect('error');

	}

	public function init()
	{
		$this->_options= $this->getInvokeArg('bootstrap')->getOptions();

	}

	public function indexAction()
	{
		$configurationModel = new Timemanagement_Model_Configuration();
		$activerecordArr = $configurationModel->getActiveRecord();

		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity())
		{
			$sess_values = $auth->getStorage()->read();
		}
		if(!empty($activerecordArr))
		$this->view->dataArray = $activerecordArr;

		$this->view->messages = $this->_helper->flashMessenger->getMessages();
		$this->view->admin = 'Yes';
		$this->view->sess_values = $sess_values;
	}

	public function addAction()
	{
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
			$loginuserRole = $auth->getStorage()->read()->emprole;
			$loginuserGroup = $auth->getStorage()->read()->group_id;
		}

		$configurationForm = new Timemanagement_Form_Configuration();
		$configurationModel = new Timemanagement_Model_Configuration();
		$activerecordArr = $configurationModel->getActiveRecord();

		$msgarray = array();
			
		$configurationForm->setAttrib('action',BASE_URL.'timemanagement/configuration/add');
		$this->view->form = $configurationForm;
		$this->view->msgarray = $msgarray;
		if(count($activerecordArr)>0)
		{
			$this->view->nodata = "";
			$activerecordArr = $configurationModel->getActiveRecord();
			if(count($activerecordArr[0])>0)
			{
				foreach($activerecordArr[0] as $key=>$val)
				{
					$activerecordArr[$key] = htmlentities(addslashes($val), ENT_QUOTES, "UTF-8");
				}
			}

			$configurationForm->submit->setLabel('UPDATE');
			$configurationForm->populate($activerecordArr);
		}
		if($this->getRequest()->getPost()){
			if($configurationForm->isValid($this->_request->getPost())){
				$result = $this->save($configurationForm);
				$this->view->msgarray = $msgarray;
			}else
			{
				$messages = $configurationForm->getMessages();
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

	public function save($configurationForm)
	{
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity())
		{
			$loginUserId = $auth->getStorage()->read()->id;
		}
		if($configurationForm->isValid($this->_request->getPost()))
		{
			$trDb = Zend_Db_Table::getDefaultAdapter();
			// starting transaction
			$trDb->beginTransaction();
			try
			{
				$configurationModel = new Timemanagement_Model_Configuration();
				$id = (int)$this->_request->getParam('id');
				$id = abs($id);
				//$ts_block_reminder_day = $this->_request->getParam('ts_block_reminder_day');
				//$ts_blocking_day = $this->_request->getParam('ts_blocking_day');
				$ts_weekly_reminder_day = $this->_request->getParam('ts_weekly_reminder_day');
				$ts_block_dates_range = $this->_request->getParam('ts_block_dates_range');

				$date = new Zend_Date();
				$data = array(
				//'ts_block_reminder_day'=>$ts_block_reminder_day,
				// 'ts_blocking_day'=>$ts_blocking_day,
                                'ts_weekly_reminder_day'=>$ts_weekly_reminder_day,
				                'ts_block_dates_range'=>$ts_block_dates_range,
                                'created_by' => $loginUserId,
                                'modified_by'=>$loginUserId,
                                'modified'=>$date->get('yyyy-MM-dd HH:mm:ss'),
                                'created'=>$date->get('yyyy-MM-dd HH:mm:ss'),
                                'is_active'=>1
				);

				$update_arr = array(
                                    'is_active' => 0,
                                    'modified'=>$date->get('yyyy-MM-dd HH:mm:ss'),
                                    'modified_by'=>$loginUserId,                                                
				);

				$configurationModel->SaveorUpdateConfigurationData($update_arr, array('is_active'=>1));

				$Id = $configurationModel->SaveorUpdateConfigurationData($data, $where='');

				if($id!='')
				{
					$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Configuration updated successfully."));
				}
				else
				{
					$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Configuration added successfully."));
				}

				$trDb->commit();
			}
			catch (Exception $e)
			{
				$trDb->rollBack();
				$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Something went wrong,please try again later."));
			}
			$this->_redirect('timemanagement/configuration');
		}
		else
		{
			$messages = $configurationForm->getMessages();
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

}

