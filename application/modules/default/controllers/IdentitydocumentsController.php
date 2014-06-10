<?php
/********************************************************************************* 
 *  This file is part of Sentrifugo.
 *  Copyright (C) 2014 Sapplica
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

class Default_IdentitydocumentsController extends Zend_Controller_Action
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
		$identitydocumentsModel = new Default_Model_Identitydocuments();	
       
		$identityDocumentArr = $identitydocumentsModel->getIdentitydocumnetsrecord();
		//echo "In Controller <pre>";print_r($identityDocumentArr);die;
		if(!empty($identityDocumentArr))
		 $this->view->dataArray = $identityDocumentArr;
		$this->view->messages = $this->_helper->flashMessenger->getMessages();
    }
	
	 public function addAction()
	{
		$msgarray = array();
	    $auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
		}
		$identitydocumentsform = new Default_Form_identitydocuments();
		//echo "<pre>";print_r($IdentityCodesform);die;
		
		$identitydocumentsform->setAttrib('action',DOMAIN.'identitydocuments/add');
		$this->view->form = $identitydocumentsform; 	
		$this->view->msgarray = $msgarray;
        
		if($this->getRequest()->getPost())
		{
		     $result = $this->save($identitydocumentsform);	
          //  echo "<pre>";print_r($result);//exit;	
			$this->view->form = $identitydocumentsform;			 
		     $this->view->msgarray = $result; 
        }  		
	}

    public function viewAction()
	{	
		
	}
	
	
	public function editAction()
	{	
	    $auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
					$loginUserId = $auth->getStorage()->read()->id;
		}
		$id = $this->getRequest()->getParam('id');
		
		$identitydocumentsform = new Default_Form_identitydocuments();
		
		$identitydocumentsModel = new Default_Model_Identitydocuments();		
		try
		{
			if($id && $id>0 && is_numeric($id))
			{$id = abs($id);	
				$data = $identitydocumentsModel->getIdentitydocumnetsrecordwithID($id);
				if(!empty($data))
				{
					$identitydocumentsform->populate($data[0]);
					$selected_document_ids = array();

					if($data[0]['passport'] == 1)
					  $selected_document_ids[] = 1;
					if($data[0]['ssn'] == 1)
					  $selected_document_ids[] = 2;
                    if($data[0]['aadhaar'] == 1)
					  $selected_document_ids[] = 3;
					if($data[0]['pancard'] == 1)
					  $selected_document_ids[] = 4;
                    if($data[0]['drivinglicense'] == 1)
					  $selected_document_ids[] = 5;
					if($data[0]['others'] !='')
					{
					  $identitydocumentsform->othercheck->setValue(1);
					  $identitydocumentsform->otherdocument->setValue($data[0]['others']);
                    }					  
                    					  
 					$identitydocumentsform->setDefaults(array('identitydoc'=>$selected_document_ids));
					$identitydocumentsform->submit->setLabel('Update');
					$this->view->data = $data;
					$this->view->id = $id;
					$this->view->nodata = '';
				}
				else
				{
					$this->view->nodata = 'norecord';
				}
				$identitydocumentsform->setAttrib('action',DOMAIN.'identitydocuments/edit/id/'.$id);
			}
            else
			{
				$this->view->nodata = 'norecord';
			}
		}
		catch(Exception $e)
		{
			 $this->view->nodata = 'nodata';
		}
		$this->view->form = $identitydocumentsform;
		
		if($this->getRequest()->getPost())
		{
      		$result = $this->save($identitydocumentsform);	
			//echo"<pre>";print_r($result);exit;
			$this->view->msgarray = $result; 
		}
	}
	
	public function save($identitydocumentsform)
	{
	  $auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
					$loginUserId = $auth->getStorage()->read()->id;
		} 
		$date = new Zend_Date();
		$errorflag = "true";
		$msgarray = array();
		$passport = '';
		$ssn = '';
		$aadhaar = '';
		$pancard = '';
		$drivinglicense = '';
		//echo"<pre>";print_r($this->_request->getPost());
				$identitydoc = $this->_request->getParam('identitydoc');
					for($i=0;$i<sizeof($identitydoc);$i++)
					{
					  if($identitydoc[$i]== 1)
						$passport = 1;
					  else if($identitydoc[$i]== 2)
						$ssn = 1;
					  else if($identitydoc[$i]== 3)
						$aadhaar = 1;
					  else if($identitydoc[$i]== 4)
						$pancard = 1;
					  else if($identitydoc[$i]== 5)
						$drivinglicense = 1;					
					}
				$othercheck = $this->_request->getParam('othercheck');
				$otherdocument = $this->_request->getParam('otherdocument');
		        if($othercheck == 1)
                   { 
				     if($otherdocument == '')
					 {
					    $errorflag = 'false';
					    $msgarray['otherdocument'] = "Please enter document name.";
					 }
                   } 				   
				
		if($identitydocumentsform->isValid($this->_request->getPost()) && $errorflag == "true")
		{
		   $identitydocumentsModel = new Default_Model_Identitydocuments();	
			$id = $this->_request->getParam('id'); 

				$data = array(  'passport'=>($passport!=''?$passport:NUll),
				                 'ssn'=>($ssn!=''?$ssn:NULL),
								 'aadhaar'=>($aadhaar!=''?$aadhaar:NULL),
                                 'pancard'=>($pancard!=''?$pancard:NULL),
								 'drivinglicense'=>($drivinglicense!=''?$drivinglicense:NULL),
								 'others'=>($otherdocument!=''?$otherdocument:NULL),
								 'modifiedby'=>$loginUserId,
							//	  'modifieddate'=>$date->get('yyyy-MM-dd HH:mm:ss') 				
								'modifieddate'=>gmdate("Y-m-d H:i:s")
				      		);
					if($id!='')
					{
						$where = array('id=?'=>$id);  
						$actionflag = 2;
					}
					else
					{
					    $data['createdby'] = $loginUserId;
						//$data['createddate'] = $date->get('yyyy-MM-dd HH:mm:ss');
						$data['createddate'] = gmdate("Y-m-d H:i:s");
						$data['isactive'] = 1;
						$where = '';
						$actionflag = 1;
					}
					//echo "<pre>";print_r($data);exit;
					$Id = $identitydocumentsModel->SaveorUpdateIdentitydocumentsData($data, $where);
					if($Id == 'update')
					{
					   $tableid = $id;
					   $this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Identity documents updated successfully."));
					}   
					else
					{
                       $tableid = $Id; 	
                        $this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Identity documents added successfully."));					   
					}   
					$menumodel = new Default_Model_Menu();
					$menuidArr = $menumodel->getMenuObjID('/identitydocuments');
					$menuID = $menuidArr[0]['id'];
					//echo "<pre>";print_r($menuidArr);exit;
					$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$tableid);
					//echo $result;exit;
    			    $this->_redirect('identitydocuments');		
			}else
			{
     			$messages = $identitydocumentsform->getMessages();
				//echo "<br/>Messages >> <pre>";print_r($messages);die;
				foreach ($messages as $key => $val)
					{
						foreach($val as $key2 => $val2)
						 {
							$msgarray[$key] = $val2;
							break;
						 }
					}
				//echo "<br/>msgArr <pre>";print_r($msgarray);die;
				return $msgarray;	
			}
	
	}
	
}

