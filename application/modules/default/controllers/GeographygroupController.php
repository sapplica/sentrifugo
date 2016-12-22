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

class Default_GeographygroupController extends Zend_Controller_Action
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
		$geographygroupmodel = new Default_Model_Geographygroup();	
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
			$sort = 'DESC';$by = 'g.modifieddate';$pageNo = 1;$searchData = '';$searchQuery = '';$searchArray='';
		}
		else 
		{
			$sort = ($this->_getParam('sort') !='')? $this->_getParam('sort'):'DESC';
			$by = ($this->_getParam('by')!='')? $this->_getParam('by'):'g.modifieddate';
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
				
		$dataTmp = $geographygroupmodel->getGrid($sort, $by, $perPage, $pageNo, $searchData,$call,$dashboardcall);		 		
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
		$objName = 'geographygroup';
		$geographygroupform = new Default_Form_geographygroup();
		$currencyModel = new Default_Model_Currency();
		$geographygroupform->removeElement("submit");
		$elements = $geographygroupform->getElements();
		if(count($elements)>0)
		{
			foreach($elements as $key=>$element)
			{
				if(($key!="Cancel")&&($key!="Edit")&&($key!="Delete")&&($key!="Attachments")){
				$element->setAttrib("disabled", "disabled");
					}
        	}
        }
		$geographygroupmodel = new Default_Model_Geographygroup();
        try
		{
			if($id)
			{
			    if(is_numeric($id) && $id>0)
				{
					$data = $geographygroupmodel->getGeographyGroupDataByID($id);
					if(!empty($data))
					{ 
					$currencyname = $currencyModel->getsingleCurrencyData($data[0]['currency']);
				    if(!empty($currencyname)){
					 
						$data[0]['currency'] = $currencyname['currencyname'];
				
					}
					
						$geographygroupform->populate($data[0]);
						$this->view->controllername = $objName;
						$this->view->id = $id;
						$this->view->data = $data[0];
						$this->view->form = $geographygroupform;
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
			$this->view->ermsg = 'norecord';
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
            $loginuserRole = $auth->getStorage()->read()->emprole;
			$loginuserGroup = $auth->getStorage()->read()->group_id;
		}
		
	    $popConfigPermission = array();
	 	if(sapp_Global::_checkprivileges(CURRENCY,$loginuserGroup,$loginuserRole,'add') == 'Yes'){
	 		array_push($popConfigPermission,'currency');
	 	}
	 	
	 	$this->view->popConfigPermission = $popConfigPermission;
	 	
		$id = $this->getRequest()->getParam('id');
		$callval = $this->getRequest()->getParam('call');
		if($callval == 'ajaxcall')
			$this->_helper->layout->disableLayout();
		
		$geographygroupform = new Default_Form_geographygroup();
		$geographygroupmodel = new Default_Model_Geographygroup();
        try
		{
			if($id)
			{
			    if(is_numeric($id) && $id>0)
				{
					$data = $geographygroupmodel->getGeographyGroupDataByID($id);
					if(!empty($data))
					{
					  $geographygroupform->populate($data[0]);
					  $geographygroupform->submit->setLabel('Update');
					  $this->view->data = $data;
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
			   $this->view->ermsg = '';
            } 
		}
        catch(Exception $e)
		{
			   $this->view->ermsg = 'nodata';
		}		
		$this->view->form = $geographygroupform;
		if($this->getRequest()->getPost()){
		    if($geographygroupform->isValid($this->_request->getPost())){
			    $id = $this->_request->getParam('id'); 
			    $geographycode = $this->_request->getParam('geographycode');
				$defaultGeographyGroup = $this->_request->getParam('defaultGeographyGroup');
				$geographygroupname = $this->_request->getParam('geographygroupname');
				$geographyregion = $this->_request->getParam('geographyregion');
				$currency = $this->_request->getParam('currency');
				$geographycityname = $this->_request->getParam('geographycityname');
				$date = new Zend_Date();
				$actionflag = '';
				$tableid  = ''; 
			    $data = array( 'currency'=>trim($currency),
							   'geographygroupname'=>trim(($geographygroupname !='')?$geographygroupname:NULL),
							   'geographycode'=>trim($geographycode),
							   'geographyregion'=>trim(($geographyregion !='')?$geographyregion:NULL),
                               'geographycityname'=>trim(($geographycityname !='')?$geographycityname:NULL), 							   
							   'defaultGeographyGroup'=>trim(($defaultGeographyGroup !='')?$defaultGeographyGroup:NULL),
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
					$Id = $geographygroupmodel->SaveorUpdateGeographyGroupData($data, $where);
					if($Id == 'update')
					{
					   $tableid = $id;
					   $this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Geography group updated successfully."));
					}   
					else
					{
                       $tableid = $Id; 	
                        $this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Geography group  added successfully."));					   
					}   
					$menuID = GEOGRAPHYGROUP;
					$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$tableid);
    			    $this->_redirect('geographygroup');		
			}else
			{
     			$messages = $geographygroupform->getMessages();
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
		 $deleteflag= $this->_request->getParam('deleteflag');
		 $messages['message'] = '';
		 $actionflag = 3;
		    if($id)
			{
			 $geographygroupmodel = new Default_Model_Geographygroup();	
			  $geogroupdata = $geographygroupmodel->getGeographyGroupDataByID($id);
			  $data = array('isactive'=>0,'modifieddate'=>gmdate("Y-m-d H:i:s"));
			  $where = array('id=?'=>$id);
			  $Id = $geographygroupmodel->SaveorUpdateGeographyGroupData($data, $where);
			    if($Id == 'update')
				{
				   $menuID = GEOGRAPHYGROUP;
				   $result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$id); 
				   $configmail = sapp_Global::send_configuration_mail('Geography Group',$geogroupdata[0]['geographycode']); 				   
				   $messages['message'] = 'Geography Group deleted successfully.';
				   $messages['msgtype'] = 'success';
				}   
				else
				{
                   $messages['message'] = 'Geography Group cannot be deleted.';
                   $messages['msgtype'] = 'success';  
                }				   
			}
			else
			{ 
			 $messages['message'] = 'Geography Group cannot be deleted.';
			 $messages['msgtype'] = 'success';
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
	
	

}

