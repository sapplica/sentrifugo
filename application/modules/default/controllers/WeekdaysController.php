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

class Default_WeekdaysController extends Zend_Controller_Action
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
		$weekdaysmodel = new Default_Model_Weekdays();	
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
			$sort = 'DESC';$by = 'w.modifieddate';$pageNo = 1;$searchData = '';$searchQuery = '';$searchArray='';
		}
		else 
		{
			$sort = ($this->_getParam('sort') !='')? $this->_getParam('sort'):'DESC';
			$by = ($this->_getParam('by')!='')? $this->_getParam('by'):'w.modifieddate';
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
				
		$dataTmp = $weekdaysmodel->getGrid($sort, $by, $perPage, $pageNo, $searchData,$call,$dashboardcall);     			
					
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
		$objName = 'weekdays';
		$weekdaysform = new Default_Form_weekdays();
		$weekdaysform->removeElement("submit");
		$elements = $weekdaysform->getElements();
		if(count($elements)>0)
		{
			foreach($elements as $key=>$element)
			{
				if(($key!="Cancel")&&($key!="Edit")&&($key!="Delete")&&($key!="Attachments")){
				$element->setAttrib("disabled", "disabled");
					}
        	}
        }
		$weekdaysmodel = new Default_Model_Weekdays();
        try
        { 	
            if($id)	
            {
                if(is_numeric($id) && $id>0)
				{			
					$data = $weekdaysmodel->getWeekdayDataByID($id);
					if(!empty($data) && $data != "norows")
					{
					  $particularweeknameArr = $weekdaysmodel->getParticularWeekDayName($data[0]['day_name']);
					   if(!empty($particularweeknameArr))
						 $weekdaysform->day_name->addMultiOption($particularweeknameArr[0]['week_id'],utf8_encode($particularweeknameArr[0]['week_name']));
						 
						$weekdaysform->populate($data[0]);
						$weekdaysform->setDefault('day_name',$data[0]['day_name']);
						$this->view->controllername = $objName;
						$this->view->id = $id;
						$this->view->form = $weekdaysform;
						$this->view->ermsg = '';
					}
					else
					{
					$this->view->ermsg = 'nodata';
					}
                }else
				{
				$this->view->ermsg = 'nodata';
				}					
			}else
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
		
		$weekdaysform = new Default_Form_weekdays();
		$weekdaysmodel = new Default_Model_Weekdays();
        try
        {		
			if($id)
			{
			    if(is_numeric($id) && $id>0)
				{
					$data = $weekdaysmodel->getWeekdayDataByID($id);
					if(!empty($data) && $data != "norows")
					{
					  $particularweeknameArr = $weekdaysmodel->getParticularWeekDayName($data[0]['day_name']);
					   if(!empty($particularweeknameArr))
						 $weekdaysform->day_name->addMultiOption($particularweeknameArr[0]['week_id'],utf8_encode($particularweeknameArr[0]['week_name']));
						 
						$weekdaysform->populate($data[0]);
						$weekdaysform->submit->setLabel('Update');
						$weekdaysform->setDefault('day_name',$data[0]['day_name']);
						$this->view->ermsg = '';
						$this->view->data = $data;
						 $this->view->id = $id;
					}
					else
					{
					$this->view->ermsg = 'nodata';
					}
                }else
				{
				$this->view->ermsg = 'nodata';
				}					
			}
			else
			{
			   $activedaysArr =  $weekdaysmodel->getWeeklist();
				$newarr = array();
				$weekidstr = '';
				if(!empty($activedaysArr))
				{
					for($i=0;$i<sizeof($activedaysArr);$i++)
					{
						$newarr1[] = $activedaysArr[$i]['day_name'];
					}
					$weekidstr = implode(",",$newarr1);
					 
				}	
				 if($weekidstr !='')				
				   $weekArr = $weekdaysmodel->getWeekdayslist($weekidstr);
				 else
				   $weekArr = $weekdaysmodel->getCompleteWeekdayslist(); 		 
					if(!empty($weekArr))
					   {
						for($i=0;$i<sizeof($weekArr);$i++)
							{
									$weekdaysform->day_name->addMultiOption($weekArr[$i]['week_id'],utf8_encode($weekArr[$i]['week_name']));
							}
					   }
					$this->view->weekArr = $weekArr;
                    $this->view->ermsg = '';					
			}
		}
		catch(Exception $e)
		{
			   $this->view->ermsg = 'nodata';
		}
		$this->view->form = $weekdaysform;
		if($this->getRequest()->getPost()){
		    if($weekdaysform->isValid($this->_request->getPost())){
			    $id = $this->_request->getParam('id');
				$dayname = $this->_request->getParam('day_name');
				$shortname = $this->_request->getParam('dayshortcode');
				$longname = $this->_request->getParam('daylongcode');
				$description = $this->_request->getParam('description');
				$date = new Zend_Date();
				$actionflag = '';
				$tableid  = '';
				   $data = array('day_name'=>trim($dayname),
				          'dayshortcode'=>trim($shortname),
                          'daylongcode'=>trim($longname),						  
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
					$Id = $weekdaysmodel->SaveorUpdateWeekdaysdataData($data, $where);
					if($Id == 'update')
					{
					   $tableid = $id;
					   $this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Day updated successfully."));
					}   
					else
					{
                       $tableid = $Id; 					
					   $this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Day added successfully."));
					}   
					$menuID = WEEKDAYS;
					$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$tableid);
    			     $this->_redirect('weekdays');
			
			}else
			{
			    $messages = $weekdaysform->getMessages();
				foreach ($messages as $key => $val)
					{
						foreach($val as $key2 => $val2)
						 {
							$msgarray[$key] = $val2;
						 }
					}
				$this->view->msgarray = $msgarray;
			
			}
		}
	}
	
	public function saveupdateAction()
	{
	     $auth = Zend_Auth::getInstance();
     		if($auth->hasIdentity()){
					$loginUserId = $auth->getStorage()->read()->id;
				}
	    $id = $this->_request->getParam('id');
		$dayname = $this->_request->getParam('day_name');
		$shortname = $this->_request->getParam('dayshortcode');
		$longname = $this->_request->getParam('daylongcode');
		$description = $this->_request->getParam('description');
		$weekdaysform = new Default_Form_weekdays();
		$weekdaysmodel = new Default_Model_Weekdays();
		$messages = $weekdaysform->getMessages();
		$actionflag = '';
		$tableid  = '';
		
		if($this->getRequest()->getPost()){
		    if($weekdaysform->isValid($this->_request->getPost())){
				   $data = array('day_name'=>trim($dayname),
				          'dayshortcode'=>trim($shortname),
                          'daylongcode'=>trim($longname),						  
						  'description'=>trim($description),
						  'modifiedby'=>$loginUserId,
						  'modifieddate'=>Zend_Registry::get('currentdate')
						);
					if($id!=''){
						$where = array('id=?'=>$id);  
						$messages['message']='Day updated successfully.';
						$actionflag = 2;
						
					}
					else
					{
					    $data['createdby'] = $loginUserId;
						$data['createddate'] = Zend_Registry::get('currentdate');
						$data['isactive'] = 1;
						$where = '';
						$messages['message']='Day  added successfully.';
						$actionflag = 1;
					}
					$Id = $weekdaysmodel->SaveorUpdateWeekdaysdataData($data, $where);
					if($Id == 'update')
					   $tableid = $id;
					else
                       $tableid = $Id; 					
					$menuID = WEEKDAYS;
					$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$tableid);
    			    $messages['result']='saved';
					$this->_helper->json($messages);
			
			}else
			{
			    $messages = $weekdaysform->getMessages();
				$messages['result']='error';
    			$this->_helper->json($messages);
			
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
		 $messages['message'] = '';
		 $messages['msgtype'] = '';
		 $messages['flagtype'] = '';
		 $weekname= '' ;
		 $actionflag = 3;
		    if($id)
			{
			 $weekdaysmodel = new Default_Model_Weekdays();
			 $weekname = $weekdaysmodel->getcombinedweekname($id);
			 
			  $data = array('isactive'=>0,'modifieddate'=>gmdate("Y-m-d H:i:s"));
			  $where = array('id=?'=>$id);
			  $Id = $weekdaysmodel->SaveorUpdateWeekdaysdataData($data, $where);
			    if($Id == 'update')
				{
				  $menuID = WEEKDAYS;	
				   $result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$id);
				   if(!empty($weekname))
				   $configmail = sapp_Global::send_configuration_mail('Days List',$weekname[0]['week_name']);				   				   
				   $messages['message'] = 'Day deleted successfully.';
				   $messages['msgtype'] = 'success';
				}   
				else
				{
                   $messages['message'] = 'Day cannot be deleted.';
                   $messages['msgtype'] = 'error'; 		   
				}
			}
			else
			{ 
			 $messages['message'] = 'Day cannot be deleted.';
			 $messages['msgtype'] = 'error';
			}
			$this->_helper->json($messages);
		
	}
	
	

}

