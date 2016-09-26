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

class Default_CancelleavesController extends Zend_Controller_Action
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
	
		$leaverequestmodel = new Default_Model_Leaverequest();	
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
				
		$objName = 'cancelleaves';
		$queryflag = 'cancel';
		$dataTmp = $leaverequestmodel->getGrid($sort, $by, $perPage, $pageNo, $searchData,$call,$dashboardcall,$objName,$queryflag); 

		$leavesCountArray = sapp_Helper::getLeavesCountByCategory($loginUserId);
		
		array_push($data,$dataTmp);
		$this->view->dataArray = $data;
		$this->view->call = $call ;
		$this->view->leavesCountArray = $leavesCountArray ;
		$this->view->messages = $this->_helper->flashMessenger->getMessages();
    }
	
    public function viewAction()
	{	
	    $auth = Zend_Auth::getInstance();
     		if($auth->hasIdentity()){
					$loginUserId = $auth->getStorage()->read()->id;
					
			}
		$leaverequestmodel = new Default_Model_Leaverequest();	
		$id = $this->getRequest()->getParam('id');
		try
		{
		    $useridArr = $leaverequestmodel->getUserID($id); 
		    if(!empty($useridArr))
			{
			  $user_id = $useridArr[0]['user_id'];
				if($user_id == $loginUserId)
				{
					$callval = $this->getRequest()->getParam('call');
					if($callval == 'ajaxcall')
						$this->_helper->layout->disableLayout();
					$objName = 'pendingleaves';
					$leaverequestform = new Default_Form_leaverequest();
					$leaverequestform->removeElement("submit");
					$elements = $leaverequestform->getElements();
					if(count($elements)>0)
					{
						foreach($elements as $key=>$element)
						{
							if(($key!="Cancel")&&($key!="Edit")&&($key!="Delete")&&($key!="Attachments")){
							$element->setAttrib("disabled", "disabled");
								}
						}
					}
						$leaverequestmodel = new Default_Model_Leaverequest();
						$data = $leaverequestmodel->getsinglePendingLeavesData($id);
						$data = $data[0];
						if(!empty($data) && $data['leavestatus'] == 'Cancel')
							{
								$employeeleavetypemodel = new Default_Model_Employeeleavetypes();
								$usersmodel = new Default_Model_Users();
								$employeeleavetypeArr = $employeeleavetypemodel->getsingleEmployeeLeavetypeData($data['leavetypeid']);
							if($employeeleavetypeArr != 'norows')
								{
									$leaverequestform->leavetypeid->addMultiOption($employeeleavetypeArr[0]['id'],utf8_encode($employeeleavetypeArr[0]['leavetype']));
									$data['leavetypeid']=$employeeleavetypeArr[0]['leavetype'];	   
								}
								if($data['leaveday'] == 1)
								{
								  $leaverequestform->leaveday->addMultiOption($data['leaveday'],'Full Day');
									$data['leaveday']=	'Full Day';   
								}
								else 
								{
								  $leaverequestform->leaveday->addMultiOption($data['leaveday'],'Half Day');
								  $data['leaveday']='Half Day'; 
								}					
							   
								$repmngrnameArr = $usersmodel->getUserDetailsByID($data['rep_mang_id'],'all');	
								$leaverequestform->populate($data);								
								$from_date = sapp_Global::change_date($data["from_date"], 'view');
								$to_date = sapp_Global::change_date($data["to_date"], 'view');
								$appliedon = sapp_Global::change_date($data["createddate"], 'view');
								$leaverequestform->from_date->setValue($from_date);
								$leaverequestform->to_date->setValue($to_date);
								$leaverequestform->createddate->setValue($appliedon);
								$leaverequestform->appliedleavesdaycount->setValue($data['appliedleavescount']);
							if(!empty($repmngrnameArr)){
								 $leaverequestform->rep_mang_id->setValue($repmngrnameArr[0]['userfullname']);
								  $data['rep_mang_id']=$repmngrnameArr[0]['userfullname'];
								}
								else {
								  $leaverequestform->rep_mang_id->setValue('');
								  $data['rep_mang_id']=$repmngrnameArr[0]['userfullname'];
								}
								$leaverequestform->setDefault('leavetypeid',$data['leavetypeid']);
								$leaverequestform->setDefault('leaveday',$data['leaveday']);
								$this->view->controllername = $objName;
								$this->view->id = $id;
								$this->view->form = $leaverequestform;
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
			$leaverequestmodel = new Default_Model_Leaverequest();
			  $data = array('leavestatus'=>4);
			  $where = array('id=?'=>$id);
			  $Id = $leaverequestmodel->SaveorUpdateLeaveRequest($data, $where);
			    if($Id == 'update')
				{
				   $menuID = PENDINGLEAVES;
				   $result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$id); 
				   $messages['message'] = 'Leave request cancelled.';
				}   
				else
                   $messages['message'] = 'Leave request cannot be cancelled.';				
			}
			else
			{ 
			 $messages['message'] = 'Leave request cannot be cancelled.';
			}
			$this->_helper->json($messages);
		
	}
}

