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

class Default_MyholidaycalendarController extends Zend_Controller_Action
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
		$call = $this->_getParam('call');
		if($call == 'ajaxcall')
				$this->_helper->layout->disableLayout();
				
		$employeesModel = new Default_Model_Employees();
        $empholidaygroup = $employeesModel->getHolidayGroupForEmployee($loginUserId);  		
		if(isset($empholidaygroup[0]['holiday_group']) && $empholidaygroup[0]['holiday_group'] != '')
		{
				
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
					$sort = 'DESC';$by = 'h.holidaydate';$perPage = 10;$pageNo = 1;$searchData = '';
				}
				else 
				{
					$sort = ($this->_getParam('sort') !='')? $this->_getParam('sort'):'DESC';
					$by = ($this->_getParam('by')!='')? $this->_getParam('by'):'h.holidaydate';
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
				$holidaydatesmodel = new Default_Model_Holidaydates();		
				$objName = 'myholidaycalendar';
				$groupid = $empholidaygroup[0]['holiday_group'];
								    
				$dataTmp = $holidaydatesmodel->getGrid($sort, $by, $perPage, $pageNo, $searchData,$call,$dashboardcall,$objName,$groupid);				
							
				array_push($data,$dataTmp);
				$this->view->dataArray = $data;
				$this->view->call = $call ;
		}
        else
        {
		  $this->view->errormessage = "Not assigned to any group";
        }
        $this->view->controllergrid = 'myholidaycalendar'; 		
		$this->view->messages = $this->_helper->flashMessenger->getMessages();
    }
	
	
    public function viewAction()
	{	
		$id = intval($this->getRequest()->getParam('id'));
		$callval = $this->getRequest()->getParam('call');
		if($callval == 'ajaxcall')
			$this->_helper->layout->disableLayout();
		$objName = 'myholidaycalendar';
		$holidaydatesform = new Default_Form_holidaydates();
		$holidaydatesmodel = new Default_Model_Holidaydates(); 	
		$holidaygroupsmodel = new Default_Model_Holidaygroups();
		$holidaydatesform->removeElement("submit");
		$data = $holidaydatesmodel->getsingleHolidayDatesData($id);
  		   $groupdataArr = $holidaygroupsmodel->getAllGroupData();
		    if(sizeof($groupdataArr) > 0)
            {
				foreach ($groupdataArr as $groupdatares){
					$holidaydatesform->groupid->addMultiOption($groupdatares['id'],utf8_encode($groupdatares['groupname']));
				}
		    }
			$elements = $holidaydatesform->getElements();
			if(count($elements)>0)
			{
				foreach($elements as $key=>$element)
				{
					if(($key!="Cancel")&&($key!="Edit")&&($key!="Delete")&&($key!="Attachments")){
					$element->setAttrib("disabled", "disabled");
						}
				}
			}
	           if(!empty($data['groupid'])) {
						$holidaygroupname = $holidaygroupsmodel->getsingleGroupData($data['groupid']);
						
						if(!empty($holidaygroupname)){
							$data['groupid'] = $holidaygroupname['groupname'];
						}
					}
		if(!empty($data))
		{
			$holidaydatesform->populate($data);			
                        $holidaydate = sapp_Global::change_date($data['holidaydate'], 'view');
			$holidaydatesform->holidaydate->setValue($holidaydate);
		
			$this->view->controllername = $objName;
			$this->view->id = $id;
			$this->view->data = $data;
			$this->view->form = $holidaydatesform;
			$this->view->ermsg = '';
		}else{
			$this->view->ermsg = 'nodata';
		}
	}
	
	
	
	public function save($holidaygroupsform)
	{
	  $auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
					$loginUserId = $auth->getStorage()->read()->id;
		} 
     		if($holidaygroupsform->isValid($this->_request->getPost())){
			    $id = $this->_request->getParam('id'); 
			    $groupname = $this->_request->getParam('groupname');
				$description = $this->_request->getParam('description');
				$date = new Zend_Date();
				$holidaygroupsmodel = new Default_Model_Holidaygroups(); 
				$actionflag = '';
				$tableid  = ''; 
				   $data = array( 'groupname'=>$groupname,
				                  'description'=>trim($description),
								  'modifiedby'=>$loginUserId,
								  'modifieddate'=>$date->get('yyyy-MM-dd HH:mm:ss')
						);
					if($id!=''){
						$where = array('id=?'=>$id);  
						$actionflag = 2;
					}
					else
					{
					    $data['createdby'] = $loginUserId;
						$data['createddate'] = $date->get('yyyy-MM-dd HH:mm:ss');
						$data['isactive'] = 1;
						$where = '';
						$actionflag = 1;
					}
					$Id = $holidaygroupsmodel->SaveorUpdateGroupData($data, $where);
					if($Id == 'update')
					{
					   $tableid = $id;
					   $this->_helper->getHelper("FlashMessenger")->addMessage("Holiday Group updated successfully.");
					}   
					else
					{
                       $tableid = $Id; 	
                        $this->_helper->getHelper("FlashMessenger")->addMessage("Holiday Group added successfully.");					   
					}   
					$menuID = HOLIDAYGROUPS;
					$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$tableid);
    			    $this->_redirect('holidaygroups');		
			}else
			{
     			$messages = $$holidaygroupsform->getMessages();
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
	
	public function deleteAction()
	{
	     $auth = Zend_Auth::getInstance();
     		if($auth->hasIdentity()){
					$loginUserId = $auth->getStorage()->read()->id;
				}
		 $id = $this->_request->getParam('objid');
		 $messages['message'] = '';$messages['msgtype'] = '';
		 $actionflag = 3;
		    if($id)
			{
			$holidaygroupsmodel = new Default_Model_Holidaygroups(); 
			  $data = array('isactive'=>0);
			  $where = array('id=?'=>$id);
			  $Id = $holidaygroupsmodel->SaveorUpdateGroupData($data, $where);
			    if($Id == 'update')
				{
				   $menuID = HOLIDAYGROUPS;
				   $result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$id); 
				   $messages['message'] = 'Holiday group deleted successfully.';
				   $messages['msgtype'] = 'success';
				}   
				else{
                   $messages['message'] = 'Holiday group cannot be deleted.';				
				   $messages['msgtype'] = 'error';
				   }
			}
			else
			{ 
			 $messages['message'] = 'Holiday group cannot be deleted.';
			 $messages['msgtype'] = 'error';
			}
			$this->_helper->json($messages);
		
	}
	
	

}

