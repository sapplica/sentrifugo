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

class Default_CurrencyController extends Zend_Controller_Action
{

	private $options;
	public function preDispatch()
	{
			

	}

	public function init()
	{
		$this->_options= $this->getInvokeArg('bootstrap')->getOptions();

	}

	public function indexAction()
	{
		$currencymodel = new Default_Model_Currency();
		$call = $this->_getParam('call');
		if($call == 'ajaxcall')
		$this->_helper->layout->disableLayout();
		$view = Zend_Layout::getMvcInstance()->getView();
		$objname = $this->_getParam('objname');
		$refresh = $this->_getParam('refresh');
		$dashboardcall = $this->_getParam('dashboardcall');

		$data = array();
		$searchQuery = '';
		$searchArray = array();
		$tablecontent='';

		if($refresh == 'refresh')
		{
			if($dashboardcall == 'Yes')
			$perPage = DASHBOARD_PERPAGE;
			else
			$perPage = PERPAGE;
			$sort = 'DESC';$by = 'modifieddate';$pageNo = 1;$searchData = '';$searchQuery = '';$searchArray='';
		}
		else
		{
			$sort = ($this->_getParam('sort') !='')? $this->_getParam('sort'):'DESC';
			$by = ($this->_getParam('by')!='')? $this->_getParam('by'):'modifieddate';
			if($dashboardcall == 'Yes')
			$perPage = $this->_getParam('per_page',DASHBOARD_PERPAGE);
			else
			$perPage = $this->_getParam('per_page',PERPAGE);
			$pageNo = $this->_getParam('page', 1);
			/** search from grid - START **/
			$searchData = $this->_getParam('searchData');
			$searchData = rtrim($searchData,',');
			/** search from grid - END **/
		}

		$dataTmp = $currencymodel->getGrid($sort, $by, $perPage, $pageNo, $searchData,$call,$dashboardcall);
			
		array_push($data,$dataTmp);
		$this->view->dataArray = $data;
		$this->view->call = $call ;
		$this->view->messages = $this->_helper->flashMessenger->getMessages();
	}

	public function viewAction()
	{
		$id = $this->getRequest()->getParam('id');
		$callval = $this->getRequest()->getParam('call');
		if($callval == 'ajaxcall')
		$this->_helper->layout->disableLayout();
		$objName = 'currency';
		$currencyform = new Default_Form_currency();
		$currencyform->removeElement("submit");
		$elements = $currencyform->getElements();
		if(count($elements)>0)
		{
			foreach($elements as $key=>$element)
			{
				if(($key!="Cancel")&&($key!="Edit")&&($key!="Delete")&&($key!="Attachments")){
					$element->setAttrib("disabled", "disabled");
				}
			}
		}
		$currencymodel = new Default_Model_Currency();
		try
		{

			if(is_numeric($id) && $id>0)
			{
				$data = $currencymodel->getCurrencyDataByID($id);
				
				if(!empty($data))
				{
					$currencyform->populate($data[0]);
					$this->view->controllername = $objName;
					$this->view->id = $id;
					$this->view->data = $data[0];
					$this->view->form = $currencyform;
					$this->view->ermsg = '';
				}
				else
				{
					$this->view->ermsg = 'nodata';
				}
			}
			else
			{
				$this->view->ermsg = 'nodata';
			}

		}
		catch(Exception $e)
		{
			$this->view->ermsg = 'nodata';
		}

	}


	public function editAction()
	{
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
		}
		$id = $this->getRequest()->getParam('id');
		$callval = $this->getRequest()->getParam('call');
		if($callval == 'ajaxcall')
		$this->_helper->layout->disableLayout();

		$currencyform = new Default_Form_currency();
		$currencymodel = new Default_Model_Currency();
		try
		{
			if($id)
			{
				if(is_numeric($id) && $id>0)
				{
					$data = $currencymodel->getCurrencyDataByID($id);
					
					if(!empty($data))
					{
						$currencyform->populate($data[0]);
						$currencyform->submit->setLabel('Update');
						$this->view->ermsg = '';
					}
					else
					{
						$this->view->ermsg = 'nodata';
					}
				}
				else
				{
					$this->view->ermsg = 'nodata';
				}
			}else
			{
				$this->view->ermsg = '';
			}
		}
		catch(Exception $e)
		{
			$this->view->ermsg = 'nodata';
		}
		$this->view->form = $currencyform;
		if($this->getRequest()->getPost()){
			if($currencyform->isValid($this->_request->getPost())){
				$id = $this->_request->getParam('id');
				$currencyname = $this->_request->getParam('currencyname');
				$currencycode = $this->_request->getParam('currencycode');
				$description = $this->_request->getParam('description');
				$date = new Zend_Date();
				$actionflag = '';
				$tableid  = '';
				$data = array('currencyname'=>trim($currencyname),
				                 'currencycode'=>trim($currencycode),
				         	  'description'=>trim($description),
						  'modifiedby'=>$loginUserId,
						  'modifieddate'=>gmdate("Y-m-d H:i:s")
				);
				if($id!=''){
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
				$Id = $currencymodel->SaveorUpdateCurrencyData($data, $where);
				if($Id == 'update')
				{
					$tableid = $id;
					$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Currency updated successfully."));
                                    //start of updating currency converter
                                    $db = Zend_Db_Table::getDefaultAdapter();
                                    $up_query1 = "update main_currencyconverter set basecurrtext = '".trim($currencyname)."' where basecurrency = '".$id."';";
                                    $db->query($up_query1);
                                    $up_query2 = "update main_currencyconverter set targetcurrtext = '".trim($currencyname)."' where targetcurrency = '".$id."';";
                                    $db->query($up_query2);
                                    //end of updating currency converter
				}
				else
				{
					$tableid = $Id;
					$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Currency  added successfully."));
				}
				$menuID =  CURRENCY; 
				$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$tableid);
				$this->_redirect('currency');
			}else
			{
				$messages = $currencyform->getMessages();
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

	public function addpopupAction()
	{
		Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH."/layouts/scripts/popup/");
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
		}
		$id = $this->getRequest()->getParam('id');

		$msgarray = array();
		$controllername = 'currency';
		$currencyform = new Default_Form_currency();
		$currencymodel = new Default_Model_Currency();
		$currencyform->setAction(BASE_URL.'currency/addpopup');
		if($this->getRequest()->getPost()){
			if($currencyform->isValid($this->_request->getPost())){
				$id = $this->_request->getParam('id');
				$currencyname = $this->_request->getParam('currencyname');
				$currencycode = $this->_request->getParam('currencycode');
				$description = $this->_request->getParam('description');
				$date = new Zend_Date();
				$actionflag = '';
				$tableid  = '';
				$data = array('currencyname'=>trim($currencyname),
				                 'currencycode'=>trim($currencycode),
				         	  'description'=>trim($description),
						  'modifiedby'=>$loginUserId,
						  'modifieddate'=>gmdate("Y-m-d H:i:s")
				);
				if($id!=''){
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
				$Id = $currencymodel->SaveorUpdateCurrencyData($data, $where);
				$tableid = $Id;
				$menuID =  CURRENCY; 
				$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$tableid);
				$currencyData = $currencymodel->fetchAll('isactive = 1','currencyname')->toArray();
				$opt ='';
				foreach($currencyData as $record){
					$opt .= sapp_Global::selectOptionBuilder($record['id'], $record['currencyname']);
				}
				$this->view->currencyData = $opt;
				$this->view->eventact = 'added';
				$close = 'close';
				$this->view->popup=$close;
			}else
			{
				$messages = $currencyform->getMessages();
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
		$this->view->controllername = $controllername;
		$this->view->form = $currencyform;
		$this->view->ermsg = '';

	}


	public function deleteAction()
	{
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
		}
		$id = $this->_request->getParam('objid');
		$deleteflag= $this->_request->getParam('deleteflag');
		$messages['message'] = '';
		$messages['msgtype'] = '';
		$actionflag = 3;
		if($id)
		{
			$currencymodel = new Default_Model_Currency();
			$currencydata = $currencymodel->getCurrencyDataByID($id);
			$data = array('isactive'=>0,'modifieddate'=>gmdate("Y-m-d H:i:s"));
			$where = array('id=?'=>$id);
			$Id = $currencymodel->SaveorUpdateCurrencyData($data, $where);
			if($Id == 'update')
			{
				$menuID =  CURRENCY; 
				$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$id);
				$configmail = sapp_Global::send_configuration_mail('Currency',$currencydata[0]['currencyname']);
				$messages['message'] = 'Currency deleted successfully.';
				$messages['msgtype'] = 'success';
			}
			else
			{
				$messages['message'] = 'Currency cannot be deleted.';
				$messages['msgtype'] = 'error';
			}
		}
		else
		{
			$messages['message'] = 'Currency cannot be deleted.';
			$messages['msgtype'] = 'error';
		}
		if($deleteflag==1)
		{
			if(	$messages['msgtype'] == 'error')
			{
				$this->_helper->getHelper("FlashMessenger")->addMessage(array("error"=>$messages['message'],"msgtype"=>$messages['msgtype'] ,'deleteflag'=>$deleteflag));
			}
			if(	$messages['msgtype'] == 'success')
			{
				$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>$messages['message'],"msgtype"=>$messages['msgtype'],'deleteflag'=>$deleteflag));
			}
				
		}
		$this->_helper->json($messages);

	}

	public function gettargetcurrencyAction()
	{
		$ajaxContext = $this->_helper->getHelper('AjaxContext');
		$ajaxContext->addActionContext('gettargetcurrency', 'html')->initContext();
		$basecurr_id = $this->_request->getParam('basecurr_id');
		$currencyconverterform = new Default_Form_currencyconverter();
		$currencymodel = new Default_Model_Currency();
		$targetcurrencydata = $currencymodel->getTargetCurrencyList($basecurr_id);
		$this->view->currencyconverterform=$currencyconverterform;
		$this->view->targetcurrencydata=$targetcurrencydata;
	}

}

