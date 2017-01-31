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

class Default_CanceloncallsController extends Zend_Controller_Action
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
	    $auth = Zend_Auth::getInstance();
     		if($auth->hasIdentity()){
					$loginUserId = $auth->getStorage()->read()->id;
				}  
	
		$oncallrequestmodel = new Default_Model_Oncallrequest();	
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
			// search from grid - START 
			$searchData = $this->_getParam('searchData');	
			$searchData = rtrim($searchData,',');
			// search from grid - END 
		}
				
		$objName = 'canceloncalls';
		$queryflag = 'cancel';
		$dataTmp = $oncallrequestmodel->getGrid($sort, $by, $perPage, $pageNo, $searchData,$call,$dashboardcall,$objName,$queryflag); 

		$oncallsCountArray = sapp_Helper::getOncallsCountByCategory($loginUserId);
		
		array_push($data,$dataTmp);
		$this->view->dataArray = $data;
		$this->view->call = $call ;
		$this->view->oncallsCountArray = $oncallsCountArray ;
		$this->view->messages = $this->_helper->flashMessenger->getMessages();
    }
	
    public function viewAction()
	{	
	    $auth = Zend_Auth::getInstance();
     		if($auth->hasIdentity()){
					$loginUserId = $auth->getStorage()->read()->id;
					
			}
		$oncallrequestmodel = new Default_Model_Oncallrequest();	
		$id = $this->getRequest()->getParam('id');
		try
		{
		    $useridArr = $oncallrequestmodel->getUserID($id); 
		    if(!empty($useridArr))
			{
			  $user_id = $useridArr[0]['user_id'];
				if($user_id == $loginUserId)
				{
					$callval = $this->getRequest()->getParam('call');
					if($callval == 'ajaxcall')
						$this->_helper->layout->disableLayout();
					$objName = 'pendingoncalls';
					$oncallrequestform = new Default_Form_oncallrequest();
					$oncallrequestform->removeElement("submit");
					$elements = $oncallrequestform->getElements();
					if(count($elements)>0)
					{
						foreach($elements as $key=>$element)
						{
							if(($key!="Cancel")&&($key!="Edit")&&($key!="Delete")&&($key!="Attachments")){
							$element->setAttrib("disabled", "disabled");
								}
						}
					}
						$oncallrequestmodel = new Default_Model_Oncallrequest();
						$data = $oncallrequestmodel->getsinglePendingOncallsData($id);
						$data = $data[0];
						if(!empty($data) && $data['oncallstatus'] == 'Cancel')
							{
								$employeeoncalltypemodel = new Default_Model_Employeeoncalltypes();
								$usersmodel = new Default_Model_Users();
								$employeeoncalltypeArr = $employeeoncalltypemodel->getsingleEmployeeOncalltypeData($data['oncalltypeid']);
							if($employeeoncalltypeArr != 'norows')
								{
									$oncallrequestform->oncalltypeid->addMultiOption($employeeoncalltypeArr[0]['id'],$employeeoncalltypeArr[0]['oncalltype']);
									$data['oncalltypeid']=$employeeoncalltypeArr[0]['oncalltype'];	   
								}
								if($data['oncallday'] == 1)
								{
								  $oncallrequestform->oncallday->addMultiOption($data['oncallday'],'Full Day');
									$data['oncallday']=	'Full Day';   
								}
								else 
								{
								  $oncallrequestform->oncallday->addMultiOption($data['oncallday'],'Half Day');
								  $data['oncallday']='Half Day'; 
								}					
							   
								$repmngrnameArr = $usersmodel->getUserDetailsByID($data['rep_mang_id'],'all');	
								$oncallrequestform->populate($data);								
								$from_date = sapp_Global::change_date($data["from_date"], 'view');
								$to_date = sapp_Global::change_date($data["to_date"], 'view');
								$appliedon = sapp_Global::change_date($data["createddate"], 'view');
								$oncallrequestform->from_date->setValue($from_date);
								$oncallrequestform->to_date->setValue($to_date);
								$oncallrequestform->createddate->setValue($appliedon);
								$oncallrequestform->appliedoncallsdaycount->setValue($data['appliedoncallscount']);
							if(!empty($repmngrnameArr)){
								 $oncallrequestform->rep_mang_id->setValue($repmngrnameArr[0]['userfullname']);
								  $data['rep_mang_id']=$repmngrnameArr[0]['userfullname'];
								}
								else {
								  $oncallrequestform->rep_mang_id->setValue('');
								  $data['rep_mang_id']=$repmngrnameArr[0]['userfullname'];
								}
								$oncallrequestform->setDefault('oncalltypeid',$data['oncalltypeid']);
								$oncallrequestform->setDefault('oncallday',$data['oncallday']);
								$this->view->controllername = $objName;
								$this->view->id = $id;
								$this->view->form = $oncallrequestform;
								$this->view->data = $data;
								$this->view->reportingmanagerStatus = (!empty($repmngrnameArr))?$repmngrnameArr[0]['isactive']:'';
							}	
						
						else
						{
						   $this->view->rowexist = "rows";
						}
					}else
					{
					   $this->view->rowexist = "rows";
					}
            }else
            {
			   $this->view->rowexist = "norows";
            }
        }
        catch(Exception $e){
			    $this->view->rowexist = "norows";
		    } 		
                    					
	}
	
	public function deleteAction()
	{
	     $auth = Zend_Auth::getInstance();
     		if($auth->hasIdentity()){
					$loginUserId = $auth->getStorage()->read()->id;
				}
		 $id = $this->_request->getParam('objid');
		 $messages['message'] = '';
		 $actionflag = 3;
		    if($id)
			{
			$oncallrequestmodel = new Default_Model_Oncallrequest();
			  $data = array('oncallstatus'=>4);
			  $where = array('id=?'=>$id);
			  $Id = $oncallrequestmodel->SaveorUpdateOncallRequest($data, $where);
			    if($Id == 'update')
				{
				   $menuID = PENDINGONCALLS;
				   $result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$id); 
				   $messages['message'] = 'On call request cancelled.';
				}   
				else
                   $messages['message'] = 'On call request cannot be cancelled.';				
			}
			else
			{ 
			 $messages['message'] = 'On call request cannot be cancelled.';
			}
			$this->_helper->json($messages);
		
	}
}

