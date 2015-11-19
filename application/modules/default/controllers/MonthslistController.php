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

class Default_MonthslistController extends Zend_Controller_Action
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
		$monthslistmodel = new Default_Model_Monthslist();	
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
			$sort = 'DESC';$by = 'm.modifieddate';$perPage = 10;$pageNo = 1;$searchData = '';$searchQuery = '';$searchArray='';
		}
		else 
		{
			$sort = ($this->_getParam('sort') !='')? $this->_getParam('sort'):'DESC';
			$by = ($this->_getParam('by')!='')? $this->_getParam('by'):'m.modifieddate';
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
		$dataTmp = $monthslistmodel->getGrid($sort, $by, $perPage, $pageNo, $searchData,$call,$dashboardcall);     				
						
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
		$objName = 'monthslist';
		$monthslistform = new Default_Form_monthslist();
		$monthslistform->removeElement("submit");
		$elements = $monthslistform->getElements();
		if(count($elements)>0)
		{
			foreach($elements as $key=>$element)
			{
				if(($key!="Cancel")&&($key!="Edit")&&($key!="Delete")&&($key!="Attachments")){
				$element->setAttrib("disabled", "disabled");
					}
        	}
        }
		$monthslistmodel = new Default_Model_Monthslist();	
		try
		{
		   if($id)
		   {
				if(is_numeric($id) && $id>0)
				{
					$data = $monthslistmodel->getMonthNameDataByID($id);
					if(!empty($data))
					{
						$particularmonthnameArr = $monthslistmodel->getParticularMonthName($data[0]['month_id']);
						   if(!empty($particularmonthnameArr))
							 $monthslistform->month_id->addMultiOption($particularmonthnameArr[0]['monthid'],utf8_encode($particularmonthnameArr[0]['month_name'])); 
							 
						$monthslistform->populate($data[0]);
						$monthslistform->setDefault('month_id',$data[0]['month_id']);
						$this->view->controllername = $objName;
						$this->view->id = $id;
						$this->view->form = $monthslistform;
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
		
		$monthslistform = new Default_Form_monthslist();
		$monthslistmodel = new Default_Model_Monthslist();
		try
		{
			if($id)
			{
			    if(is_numeric($id) && $id>0)
				{
					$data = $monthslistmodel->getMonthNameDataByID($id);
					if(!empty($data))
					{
					 $particularmonthnameArr = $monthslistmodel->getParticularMonthName($data[0]['month_id']);
					   if(!empty($particularmonthnameArr))
						 $monthslistform->month_id->addMultiOption($particularmonthnameArr[0]['monthid'],utf8_encode($particularmonthnameArr[0]['month_name'])); 
						 
					 $monthslistform->populate($data[0]);
					 $monthslistform->submit->setLabel('Update');
					 $monthslistform->setDefault('month_id',$data[0]['month_id']);
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
			    $activeMonthsArr =  $monthslistmodel->getMonthsList();
				$newarr = array();
				$monthidstr = '';
				if(!empty($activeMonthsArr))
						{
							for($i=0;$i<sizeof($activeMonthsArr);$i++)
							{
								$newarr1[] = $activeMonthsArr[$i]['month_id'];
							}
							$monthidstr = implode(",",$newarr1);
							 
						}
                if($monthidstr !='')				
				   $monthArr = $monthslistmodel->getMonthNamelist($monthidstr);
				 else
				   $monthArr = $monthslistmodel->getCompleteMonthNamelist();

                if(!empty($monthArr))
					   {
						for($i=0;$i<sizeof($monthArr);$i++)
							{
									$monthslistform->month_id->addMultiOption($monthArr[$i]['monthid'],utf8_encode($monthArr[$i]['month_name']));
							}
					   }
					$this->view->monthArr = $monthArr;	
                    $this->view->ermsg = '';					
			}
		}
        catch(Exception $e)
		{
			   $this->view->ermsg = 'nodata';
		} 		
		$this->view->form = $monthslistform;
		if($this->getRequest()->getPost()){
		    if($monthslistform->isValid($this->_request->getPost())){
			$id = $this->_request->getParam('id');
			$month_id = $this->_request->getParam('month_id');
			$monthcode = $this->_request->getParam('monthcode');
			$description = $this->_request->getParam('description');
			$date = new Zend_Date();
			$actionflag = '';
			$tableid  = '';
				   $data = array('month_id'=>$month_id,
				          'monthcode'=>trim($monthcode),
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
					$Id = $monthslistmodel->SaveorUpdateMonthListData($data, $where);
					if($Id == 'update')
					{
					   $tableid = $id;
					   $this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Month updated successfully."));
					}   
					else
					{
                       $tableid = $Id; 
						$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Month added successfully."));
					}   
					$menuID = MONTHSLIST;
					$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$tableid);
    			    $this->_redirect('monthslist');
			
			}else
			{
			    $messages = $monthslistform->getMessages();
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
		$monthname = $this->_request->getParam('month_name');
		$monthcode = $this->_request->getParam('monthcode');
		$description = $this->_request->getParam('description');
		$monthslistform = new Default_Form_monthslist();
		$monthslistmodel = new Default_Model_Monthslist();
		$messages = $monthslistform->getMessages();
		$actionflag = '';
		$tableid  = '';
		
		if($this->getRequest()->getPost()){
		    if($monthslistform->isValid($this->_request->getPost())){
				   $data = array('month_name'=>trim($monthname),
				          'monthcode'=>trim($monthcode),
						  'description'=>trim($description), 
                    	  'modifiedby'=>$loginUserId,
						  'modifieddate'=>Zend_Registry::get('currentdate')
						);
					if($id!=''){
						$where = array('id=?'=>$id);  
						$messages['message']='Month updated successfully';
						$actionflag = 2;
						
					}
					else
					{
					    $data['createdby'] = $loginUserId;
						$data['createddate'] = Zend_Registry::get('currentdate');
						$data['isactive'] = 1;
						$where = '';
						$messages['message']='Month  added successfully';
						$actionflag = 1;
					}
					$Id = $monthslistmodel->SaveorUpdateMonthListData($data, $where);
					if($Id == 'update')
					   $tableid = $id;
					else
                       $tableid = $Id; 					
			        $menuID = MONTHSLIST;
					$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$tableid);
    			    $messages['result']='saved';
					$this->_helper->json($messages);
			
			}else
			{
			    $messages = $monthslistform->getMessages();
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
		 $messages['message'] = '';$messages['flagtype'] = '';
		 $messages['msgtype'] = '';
		 $monthname = '';
		 $actionflag = 3;
		    if($id)
			{
			 $monthslistmodel = new Default_Model_Monthslist();
			  $monthname = $monthslistmodel->getcombinedmonthname($id);
			  
			  $data = array('isactive'=>0,'modifieddate'=>gmdate("Y-m-d H:i:s"));
			  $where = array('id=?'=>$id);
			  $Id = $monthslistmodel->SaveorUpdateMonthListData($data, $where);
			    if($Id == 'update')
				{
				   $menuID = MONTHSLIST;
				   $result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$id);
				 
				   if($monthname !='')
				    $configmail = sapp_Global::send_configuration_mail('Months List',$monthname);				   				   
				   $messages['message'] = 'Month deleted successfully.';
				   $messages['msgtype'] = 'success';
				}   
				else
				{
                   $messages['message'] = 'Month cannot be deleted.';
                   $messages['msgtype'] = 'error';				   
				}
			}
			else
			{ 
			 $messages['message'] = 'Month cannot be deleted.';
			 $messages['msgtype'] = 'error';
			}
			$this->_helper->json($messages);
		
	}
	
	

}

