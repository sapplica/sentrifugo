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

class Default_DateformatController extends Zend_Controller_Action
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
		$dateformat = new Default_Model_Dateformat();	
        $call = $this->_getParam('call');
		if($call == 'ajaxcall')
				$this->_helper->layout->disableLayout();
		
		$view = Zend_Layout::getMvcInstance()->getView();		
		$objname = $this->_getParam('objname');
		$refresh = $this->_getParam('refresh');
		$data = array();
		$searchQuery = '';
		$searchArray = array();
		$tablecontent='';
		
		if($refresh == 'refresh')
		{
			$sort = 'DESC';$by = 'modifieddate';$perPage = 10;$pageNo = 1;$searchData = '';$searchQuery = '';$searchArray='';
		}
		else 
		{
			$sort = ($this->_getParam('sort') !='')? $this->_getParam('sort'):'DESC';
			$by = ($this->_getParam('by')!='')? $this->_getParam('by'):'modifieddate';
			$perPage = $this->_getParam('per_page',10);
			$pageNo = $this->_getParam('page', 1);
			/** search from grid - START **/
			$searchData = $this->_getParam('searchData');	
			if($searchData != '' && $searchData!='undefined')
			{
				$searchValues = json_decode($searchData);
				foreach($searchValues as $key => $val)
				{
					$searchQuery .= " ".$key." like '%".$val."%' AND ";
					$searchArray[$key] = $val;
				}
				$searchQuery = rtrim($searchQuery," AND");					
			}
			/** search from grid - END **/
		}
				
		$objName = 'dateformat';
		
		$tableFields = array('action'=>'Action','dateformat' => 'Date Format','description' => 'Description','example'=>'Example');
		
			
		$tablecontent = $dateformat->getDateFormatData($sort, $by, $pageNo, $perPage,$searchQuery);     
		
		$dataTmp = array(
			'sort' => $sort,
			'by' => $by,
			'pageNo' => $pageNo,
			'perPage' => $perPage,				
			'tablecontent' => $tablecontent,
			'objectname' => $objName,
			'extra' => array(),
			'tableheader' => $tableFields,
			'jsGridFnName' => 'getAjaxgridData',
			'jsFillFnName' => '',
			'searchArray' => $searchArray,'call'=>$call
		);			
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
		$objName = 'dateformat';
		$form = new Default_Form_dateformat();
		$form->removeElement("submit");
		$elements = $form->getElements();
		if(count($elements)>0)
		{
			foreach($elements as $key=>$element)
			{
				if(($key!="Cancel")&&($key!="Edit")&&($key!="Delete")&&($key!="Attachments")){
				$element->setAttrib("disabled", "disabled");
					}
        	}
        }
		$dateformat = new Default_Model_Dateformat();
        try
		{
			if($id)
			{  		
			  $data = $dateformat->getDateFormatDataByID($id);
			  if(!empty($data))
			   {
				$form->populate($data[0]);
				$this->view->controllername = $objName;
				$this->view->id = $id;
				$this->view->form = $form;
				$this->view->ermsg = '';
			   }
               else
				{
				$this->view->ermsg = 'no record';
				}  			   
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
		
		$dateformatform = new Default_Form_dateformat();
		$dateformatmodel = new Default_Model_Dateformat();
		
		   try
		   {
				if($id)
				{ 
				$data = $dateformatmodel->getDateFormatDataByID($id);
					if(!empty($data))
					{
					  $dateformatform->setAttrib('action',BASE_URL.'dateformat/edit/id/'.$id);
					  $dateformatform->populate($data[0]);
					  $this->view->data = $data;
					  $this->view->id = $id;
					  $this->view->ermsg = '';
					}else
					{
					$this->view->ermsg = 'no record';
					} 		
				}
            }			
			catch(Exception $e){
			   $this->view->ermsg = 'nodata';
		    }
	
		$this->view->form = $dateformatform;
		
		if($this->getRequest()->getPost()){
		    if($dateformatform->isValid($this->_request->getPost())){
			    $id = $this->_request->getParam('id');
				$dateformat = $this->_request->getParam('dateformat');
				$description = $this->_request->getParam('description');
                                $example = $this->_request->getParam('example');
				$date = new Zend_Date();
				   $data = array('dateformat'=>trim($dateformat),
						  'description'=>trim($description),
						  'modifiedby'=>$loginUserId,
                                                  'example' => $example,
						  'modifieddate'=>$date->get('yyyy-MM-dd HH:mm:ss')
						);
					if($id!='')
					{
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
					$Id = $dateformatmodel->SaveorUpdateDateFormatData($data, $where);
					if($Id == 'update')
					{
					   $tableid = $id;
					   $this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Date Format updated successfully."));
					}   
					else
					{
                       $tableid = $Id; 
					   $this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Date Format added successfully."));
					}   
					$menuID =DATEFORMAT;
					$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$tableid);
    			    $this->_redirect('dateformat');
			
			}else
			{
			    $messages = $dateformatform->getMessages();
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
		$dateformat = $this->_request->getParam('dateformat');
		$description = $this->_request->getParam('description');
                $example = $this->_request->getParam('example');
		$dateformatform = new Default_Form_dateformat();
		$dateformatmodel = new Default_Model_Dateformat();
		$messages = $dateformatform->getMessages();
		$actionflag = '';
		$tableid  = '';
		
		if($this->getRequest()->getPost()){
		    if($dateformatform->isValid($this->_request->getPost())){
				   $data = array('dateformat'=>trim($dateformat),
						  'description'=>trim($description),
						  'modifiedby'=>$loginUserId,
                                                  'example' => $example,
						  'modifieddate'=>Zend_Registry::get('currentdate')
						);
                                   print_r($data);exit;
					if($id!=''){
						$where = array('id=?'=>$id);  
						$messages['message']='Date Format updated successfully';
						$actionflag = 2;
						
					}
					else
					{
					    $data['createdby'] = $loginUserId;
						$data['createddate'] = Zend_Registry::get('currentdate');
						$data['isactive'] = 1;
						$where = '';
						$messages['message']='Date Format  added successfully';
						$actionflag = 1;
					}
					$Id = $dateformatmodel->SaveorUpdateDateFormatData($data, $where);
					if($Id == 'update')
					   $tableid = $id;
					else
                       $tableid = $Id; 					
				    $menuID = DATEFORMAT;
					$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$tableid);
    			    $messages['result']='saved';
					$this->_helper->json($messages);
			
			}else
			{
			    $messages = $dateformatform->getMessages();
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
		 $messages['msgtype'] = '';$messages['flagtype'] = '';
		 $actionflag = 3;
		    if($id)
			{
			 $dateformatmodel = new Default_Model_Dateformat();
			  $data = array('isactive'=>0);
			  $where = array('id=?'=>$id);
			  $Id = $dateformatmodel->SaveorUpdateDateFormatData($data, $where);
			    if($Id == 'update')
				{
				   $menuID =DATEFORMAT;
				   $result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$id); 
				   $messages['message'] = 'Date format deleted successfully';
				   $messages['msgtype'] = 'success';	
				}   
				else
				{
                   $messages['message'] = 'Date format cannot be deleted';
				   $messages['msgtype'] = 'error';
				}   
				   
			}
			else
			{ 
			 $messages['message'] = 'Date format cannot be deleted';
			 $messages['msgtype'] = 'error';
			}
			$this->_helper->json($messages);
		
	}
	
	

}

