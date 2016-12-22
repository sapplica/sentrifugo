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

class Default_DisciplinaryviolationController extends Zend_Controller_Action
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
		$disciplinaryviolationmodel = new Default_Model_Disciplinaryviolation();	
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
			$sort = 'DESC';$by = 'sd.modifieddate';$pageNo = 1;$searchData = '';$searchQuery = '';$searchArray='';
		}
		else 
		{
			$sort = ($this->_getParam('sort') !='')? $this->_getParam('sort'):'DESC';
			$by = ($this->_getParam('by')!='')? $this->_getParam('by'):'sd.modifieddate';
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
				
		$dataTmp = $disciplinaryviolationmodel->getGrid($sort, $by, $perPage, $pageNo, $searchData,$call,$dashboardcall);		 		
					
		array_push($data,$dataTmp);
		$this->view->dataArray = $data;
		$this->view->call = $call ;
		$this->view->messages = $this->_helper->flashMessenger->getMessages();
		//$this->renderScript('commongrid/index.phtml');
		$this->render('commongrid/index', null, true);
		
    }
	
	 public function addAction()
	{
	   $auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
					$loginUserId = $auth->getStorage()->read()->id;
					$loginuserRole = $auth->getStorage()->read()->emprole;
					$loginuserGroup = $auth->getStorage()->read()->group_id;
		}
		$callval = $this->getRequest()->getParam('call');
		if($callval == 'ajaxcall')
			$this->_helper->layout->disableLayout();
		$disciplinaryviolationform = new Default_Form_Disciplinaryviolation();
		$msgarray = array();
		$disciplinaryviolationform->setAttrib('action',BASE_URL.'disciplinaryviolation/add');
		$this->view->form = $disciplinaryviolationform; 
		$this->view->msgarray = $msgarray; 
		$this->view->ermsg = '';
			if($this->getRequest()->getPost()){
				 $result = $this->save($disciplinaryviolationform);	
				 $this->view->msgarray = $result; 
			}  		
		$this->render('form');	
	}

    public function viewAction()
	{	
		$id = $this->getRequest()->getParam('id');
		$callval = $this->getRequest()->getParam('call');
		if($callval == 'ajaxcall')
			$this->_helper->layout->disableLayout();
		$objName = 'disciplinaryviolation';
		$disciplinaryviolationform = new Default_Form_Disciplinaryviolation();
		$disciplinaryviolationform->removeElement("submit");
		$elements = $disciplinaryviolationform->getElements();
		if(count($elements)>0)
		{
			foreach($elements as $key=>$element)
			{
				if(($key!="Cancel")&&($key!="Edit")&&($key!="Delete")&&($key!="Attachments")){
				$element->setAttrib("disabled", "disabled");
					}
        	}
        }
		try
		{
		    if($id)
			{
			    if(is_numeric($id) && $id>0)
				{
					$disciplinaryviolationmodel = new Default_Model_Disciplinaryviolation();	
					$data = $disciplinaryviolationmodel->getDisciplinaryViolationTypeDatabyID($id);
					if(!empty($data))
					{
						$data = $data[0]; 
						$disciplinaryviolationform->populate($data);
					}else
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
			   $this->view->ermsg = 'norecord';
			} 			
		}
		catch(Exception $e)
		{
			   $this->view->ermsg = 'nodata';
		}
		$this->view->controllername = $objName;
		$this->view->id = $id;
		$this->view->data = $data;
		$this->view->flag = 'view';
		$this->view->form = $disciplinaryviolationform;
		$this->render('form');	
		
	}
	
	
	public function editAction()
	{	
	    $auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
					$loginUserId = $auth->getStorage()->read()->id;
					$loginuserRole = $auth->getStorage()->read()->emprole;
					$loginuserGroup = $auth->getStorage()->read()->group_id;
		}
	 	
		$id = $this->getRequest()->getParam('id');
		$callval = $this->getRequest()->getParam('call');
		if($callval == 'ajaxcall')
			$this->_helper->layout->disableLayout();
		
		$disciplinaryviolationform = new Default_Form_Disciplinaryviolation();
		$disciplinaryviolationmodel = new Default_Model_Disciplinaryviolation();
		$disciplinaryviolationform->submit->setLabel('Update');
		try
        {		
			if($id)
			{
			    if(is_numeric($id) && $id>0)
				{
					$data = $disciplinaryviolationmodel->getDisciplinaryViolationTypeDatabyID($id);
					
					if(!empty($data))
					{
						  $data = $data[0];
						
                          $disciplinaryviolationform->populate($data);
						  
                          $disciplinaryviolationform->setAttrib('action',BASE_URL.'disciplinaryviolation/edit/id/'.$id);
                        $this->view->data = $data;
					}else
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
				$this->view->ermsg = 'nodata';
			}
		}	
		catch(Exception $e)
		{
			   $this->view->ermsg = 'nodata';
		}	
		$this->view->form = $disciplinaryviolationform;
		if($this->getRequest()->getPost()){
      		$result = $this->save($disciplinaryviolationform);	
		    $this->view->msgarray = $result; 
		}
		$this->render('form');	
	}
	
	public function save($disciplinaryviolationform)
	{
	  $auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
					$loginUserId = $auth->getStorage()->read()->id;
		} 
	    $disciplinaryviolationmodel = new Default_Model_Disciplinaryviolation();
		$msgarray = array();
	    
		  if($disciplinaryviolationform->isValid($this->_request->getPost())){
            try{
            $id = $this->_request->getParam('id');
            $violationname = $this->_request->getParam('violationname');	
			$description = $this->_request->getParam('description');
			$actionflag = '';
			$tableid  = ''; 
			   $data = array('violationname'=>$violationname, 
							 'description'=>($description!=''?trim($description):NULL),
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
				$Id = $disciplinaryviolationmodel->SaveorUpdateDisciplinaryViolationTypesData($data, $where);
				if($Id == 'update')
				{
				   $tableid = $id;
				   $this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Violation Type updated successfully."));
				}   
				else
				{
				   $tableid = $Id; 	
					$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Violation Type added successfully."));					   
				}   
				$menuID = DISCIPLINARY;
				$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$tableid);
				$this->_redirect('disciplinaryviolation');	
                  }
        catch(Exception $e)
          {
             $msgarray['service_desk_name'] = "Something went wrong, please try again.";
             return $msgarray;
          }
		}else
		{
			$messages = $disciplinaryviolationform->getMessages();
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
            if($auth->hasIdentity())
            {
                $loginUserId = $auth->getStorage()->read()->id;
            }
            $id = $this->_request->getParam('objid');
			$deleteflag=$this->_request->getParam('deleteflag');
            $messages['message'] = '';
            $messages['msgtype'] = '';
            $count = 0;
            $actionflag = 3;
            if($id)
            {
                $disciplinaryviolationmodel = new Default_Model_Disciplinaryviolation();
                $raisedIncidentData = $disciplinaryviolationmodel->getDisciplinaryIncidentCount($id);
                 if(!empty($raisedIncidentData))
                    $count = $raisedIncidentData[0]['count'];
                 if($count < 1)
                {	
                    $disciplinaryviolationtypedata = $disciplinaryviolationmodel->getDisciplinaryViolationTypeDatabyID($id);
                    $data = array('isactive'=>0,'modifieddate'=>gmdate("Y-m-d H:i:s"));
                    $where = array('id=?'=>$id);
                    $reqwhere = array('violation_id=?'=>$id);
                    $Id = $disciplinaryviolationmodel->SaveorUpdateDisciplinaryViolationTypesData($data, $where);
                   
                if($Id == 'update')
				{
                   sapp_Global::send_configuration_mail("Violation Type", $disciplinaryviolationtypedata[0]['violationname']);
				   $menuID = DISCIPLINARY;
				   $result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$id); 
				   $messages['message'] = 'Violation type deleted successfully.';
				    $messages['msgtype'] = 'success';
				}   
				else
                {	$messages['message'] = 'Violation type cannot be deleted.';	
					$messages['msgtype'] = 'error';
				}
             }
                else
                {
                    $messages['message'] = 'Violation type cannot be deleted.';
                    $messages['msgtype'] = 'error';
                } 				   
            }
            else
            { 
                $messages['message'] = 'Violation type cannot be deleted.';
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
	public function addpopupAction()
	{
		Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH."/layouts/scripts/popup/");
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
		}
		$id = $this->getRequest()->getParam('id');

		$msgarray = array();
		$controllername = 'disciplinaryviolation';
		$disciplinaryviolationform = new Default_Form_Disciplinaryviolation();
		$disciplinaryviolationmodel = new Default_Model_Disciplinaryviolation();
		$disciplinaryviolationform->setAction(BASE_URL.'disciplinaryviolation/addpopup');
		if($this->getRequest()->getPost()){
			if($disciplinaryviolationform->isValid($this->_request->getPost())){
			$id = $this->_request->getParam('id');
			$description = $this->_request->getParam('description');
            $violationname = $this->_request->getParam('violationname');	
			$description = $this->_request->getParam('description');
			$actionflag = '';
			$tableid  = ''; 
			   $data = array('violationname'=>$violationname, 
							 'description'=>$description,
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
				$Id = $disciplinaryviolationmodel->SaveorUpdateDisciplinaryViolationTypesData($data, $where);
				$tableid = $Id;
				$menuID = DISCIPLINARY;
				$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$tableid);

				$disciplinaryviolationtypesData = $disciplinaryviolationmodel->getallDisciplinaryViolationTypesData();
				$opt ='';
				foreach($disciplinaryviolationtypesData as $record){
					$opt .= sapp_Global::selectOptionBuilder($record['id'], utf8_encode($record['violationname']));
				}
				$this->view->violationtypesData = $opt;
					
				$this->view->eventact = 'added';
				$close = 'close';
				$this->view->popup=$close;
			}else
			{
				$messages = $disciplinaryviolationform->getMessages();
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
		$this->view->form = $disciplinaryviolationform;
		$this->view->ermsg = '';
		$this->render('form');	
	}
	
	
}

