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

class Default_RemunerationbasisController extends Zend_Controller_Action
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
		$remunerationbasismodel = new Default_Model_Remunerationbasis();	
        $call = $this->_getParam('call');
		if($call == 'ajaxcall')
				$this->_helper->layout->disableLayout();
		
		$view = Zend_Layout::getMvcInstance()->getView();		
		$objname = $this->_getParam('objname');
		$refresh = $this->_getParam('refresh');
		$dashboardcall = $this->_getParam('dashboardcall',null);
		$data = array();	$searchQuery = '';		$searchArray = array();		$tablecontent='';
		
		if($refresh == 'refresh')
		{
			if($dashboardcall == 'Yes')
				$perPage = DASHBOARD_PERPAGE;
			else	
				$perPage = PERPAGE;
										
			$sort = 'DESC';$by = 'modifieddate';$pageNo = 1;$searchData = '';$searchQuery = '';	
			$searchArray = array();
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
		}
		$dataTmp = $remunerationbasismodel->getGrid($sort,$by,$perPage,$pageNo,$searchData,$call,$dashboardcall);
		
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
		$objName = 'remunerationbasis';
		$remunerationbasisform = new Default_Form_remunerationbasis();
		$remunerationbasisform->removeElement("submit");
		$elements = $remunerationbasisform->getElements();
		if(count($elements)>0)
		{
			foreach($elements as $key=>$element)
			{
				if(($key!="Cancel")&&($key!="Edit")&&($key!="Delete")&&($key!="Attachments")){
				$element->setAttrib("disabled", "disabled");
					}
        	}
        }
		$remunerationbasismodel = new Default_Model_Remunerationbasis();	
		try
		{
			if(is_numeric($id) && $id>0)
			{
				$data = $remunerationbasismodel->getsingleRemunerationBasisData($id);
				if(!empty($data) && $data != 'norows')
				{
					$remunerationbasisform->populate($data[0]);
					$this->view->form = $remunerationbasisform;
					$this->view->controllername = $objName;
					$this->view->id = $id;
					$this->view->data = $data[0];
				    $this->view->ermsg = '';
				}
				else
				{
					 $this->view->ermsg = 'norecord';
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
		$objName = 'remunerationbasis';
		$id = $this->getRequest()->getParam('id');
		$callval = $this->getRequest()->getParam('call');
		if($callval == 'ajaxcall')
			$this->_helper->layout->disableLayout();
		
		$remunerationbasisform = new Default_Form_remunerationbasis();
		$remunerationbasismodel = new Default_Model_Remunerationbasis();
		try
		{
			if($id)
			{
				if(is_numeric($id) && $id>0)
				{
					$data = $remunerationbasismodel->getsingleRemunerationBasisData($id);
					if(!empty($data) && $data != 'norows')
					{
						$remunerationbasisform->populate($data[0]);
						$remunerationbasisform->submit->setLabel('Update'); 
						$this->view->form = $remunerationbasisform;
						$this->view->controllername = $objName;
						$this->view->id = $id;
						$this->view->ermsg = '';
					}
					else
					{
						 $this->view->ermsg = 'norecord';
					}
				}
				else
				{
					$this->view->ermsg = 'norecord';
				}
			}
			else
			{
				$this->view->form = $remunerationbasisform;
				$this->view->ermsg = '';
			}
		}
		catch(Exception $e)
		{
			 $this->view->ermsg = 'nodata';
		}
		if($this->getRequest()->getPost()){
		    if($remunerationbasisform->isValid($this->_request->getPost())){
			    $id = $this->_request->getParam('id'); 
			    $remtype = $this->_request->getParam('remtype');
				$remdesc = $this->_request->getParam('remdesc');
				$date = new Zend_Date();
				$actionflag = '';
				$tableid  = ''; 
				   $data = array('remtype'=>trim($remtype),
				           'remdesc'=>trim($remdesc),
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
					$Id = $remunerationbasismodel->SaveorUpdateRemunerationBasisData($data, $where);
					if($Id == 'update')
					{
					   $tableid = $id;
						$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Remuneration basis updated successfully."));
					}   
					else
					{
                       $tableid = $Id; 	
						$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Remuneration basis added successfully."));
					}   
					$menuID = REMUNERATIONBASIS;
					$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$tableid);
    			    $this->_redirect('remunerationbasis');		
			}else
			{
     			$messages = $remunerationbasisform->getMessages();
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
	
	public function deleteAction()
	{
	     $auth = Zend_Auth::getInstance();
     		if($auth->hasIdentity()){
					$loginUserId = $auth->getStorage()->read()->id;
				}
		 $id = $this->_request->getParam('objid');
		 $deleteflag=$this->_request->getParam('deleteflag');
		 $messages['message'] = ''; $messages['msgtype'] = '';$messages['flagtype'] = '';
		 $actionflag = 3;
		    if($id)
			{
			 $remunerationbasismodel = new Default_Model_Remunerationbasis();
			  $data = array('isactive'=>0, 'modifieddate'=>gmdate("Y-m-d H:i:s"));
			  $where = array('id=?'=>$id);
                          $re_data = $remunerationbasismodel->getsingleRemunerationBasisData($id);
			  $Id = $remunerationbasismodel->SaveorUpdateRemunerationBasisData($data, $where);
			    if($Id == 'update')
                            {
                                sapp_Global::send_configuration_mail("Remuneration Basis", $re_data[0]['remtype']);
				   $menuID = REMUNERATIONBASIS;
				   $result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$id); 
				   $messages['message'] = 'Remuneration basis deleted successfully.';
				    $messages['msgtype'] = 'success';
				}   
				else
				{
                   $messages['message'] = 'Remuneration basis cannot be deleted.';	
					$messages['msgtype'] = 'error';
				}
			}
			else
			{ 
			 $messages['message'] = 'Remuneration basis cannot be deleted.';
			  $messages['msgtype'] = 'error';
			}
				// delete success message after delete in view
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
			//$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Remuneration basis deleted successfully.",'deleteflag'=>$deleteflag)); 
		}
			$this->_helper->json($messages);
		
	}
	
	

}

