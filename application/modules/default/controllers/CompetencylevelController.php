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

class Default_CompetencylevelController extends Zend_Controller_Action
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
		$competencylevelmodel = new Default_Model_Competencylevel();	
        $call = $this->_getParam('call');
		if($call == 'ajaxcall')
				$this->_helper->layout->disableLayout();
		
		$view = Zend_Layout::getMvcInstance()->getView();		
		$objname = $this->_getParam('objname');
		$refresh = $this->_getParam('refresh');
		$dashboardcall = $this->_getParam('dashboardcall',null);
		$data = array();$searchQuery = '';	$searchArray = array();	$tablecontent='';
		
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
			$searchData = $this->_getParam('searchData');	
			$searchData = rtrim($searchData,',');
		}
		
		$dataTmp = $competencylevelmodel->getGrid($sort,$by,$perPage,$pageNo,$searchData,$call,$dashboardcall);
		
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
		$objName = 'competencylevel';
		$competencylevelform = new Default_Form_competencylevel();
		$competencylevelform->removeElement("submit");
		$elements = $competencylevelform->getElements();
		if(count($elements)>0)
		{
			foreach($elements as $key=>$element)
			{
				if(($key!="Cancel")&&($key!="Edit")&&($key!="Delete")&&($key!="Attachments")){
				$element->setAttrib("disabled", "disabled");
					}
        	}
        }
		$competencylevelmodel = new Default_Model_Competencylevel();	
		try
		{
			if($id)
			{
				$data = $competencylevelmodel->getsingleCompetencyLevelData($id);
				if(!empty($data) && $data != 'norows')
				{
					$competencylevelform->populate($data[0]);
					$this->view->form = $competencylevelform;
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
		$objName = 'competencylevel';
		$competencylevelform = new Default_Form_competencylevel();
		$competencylevelmodel = new Default_Model_Competencylevel();
		
		try
		{
			if($id)
			{
				$data = $competencylevelmodel->getsingleCompetencyLevelData($id);
				if(!empty($data) && $data != 'norows')
				{
					$competencylevelform->populate($data[0]);
					$competencylevelform->submit->setLabel('Update');
					$this->view->form = $competencylevelform;
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
				$this->view->form = $competencylevelform;
			}
		}
		catch(Exception $e)
		{
			 $this->view->ermsg = 'nodata';
		}
		
		if($this->getRequest()->getPost()){
		    if($competencylevelform->isValid($this->_request->getPost())){
			    $id = $this->_request->getParam('id'); 
			    $competencylevel = $this->_request->getParam('competencylevel');
				$description = $this->_request->getParam('description');
				$date = new Zend_Date();
				$actionflag = '';
				$tableid  = ''; 
				   $data = array( 'competencylevel'=>trim($competencylevel),
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
					$Id = $competencylevelmodel->SaveorUpdateCompetencyLevelData($data, $where);
					if($Id == 'update')
					{
					   $tableid = $id;
					  $this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Competency level updated successfully."));
					}   
					else
					{
                       $tableid = $Id; 	
					   $this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Competency level added successfully."));
					}   
					$menuID = COMPETENCYLEVEL;
					$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$tableid);
    			    $this->_redirect('competencylevel');		
			}else
			{
     			$messages = $competencylevelform->getMessages();
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
    /**
     * This action is used to add compentency level in popups.
     */
    public function addpopupAction()
    {	
        Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH."/layouts/scripts/popup/");
        $auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity())
        {
            $loginUserId = $auth->getStorage()->read()->id;
        }
        $id = $this->getRequest()->getParam('id');
        $callval = $this->getRequest()->getParam('call');
        $previname = $this->_getParam('previname',null);
        if($callval == 'ajaxcall')
            $this->_helper->layout->disableLayout();
        $objName = 'competencylevel';
        $competencylevelform = new Default_Form_competencylevel();
        $competencylevelform->setAction(BASE_URL.'competencylevel/addpopup');
        $competencylevelmodel = new Default_Model_Competencylevel();
		
        try
        {			
            $this->view->form = $competencylevelform;
            $this->view->controllername = $objName;
        }
        catch(Exception $e)
        {
            $this->view->ermsg = 'nodata';
        }
		
        if($this->getRequest()->getPost())
        {
            if($competencylevelform->isValid($this->_request->getPost()))
            {
                $id = $this->_request->getParam('id'); 
                $competencylevel = $this->_request->getParam('competencylevel');
                $description = $this->_request->getParam('description');
                $date = new Zend_Date();
                $actionflag = '';
                $tableid  = ''; 
                $data = array( 
                            'competencylevel'=>trim($competencylevel),
                            'description'=>trim($description),
                            'modifiedby'=>$loginUserId,			
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
                    $data['createddate'] = gmdate("Y-m-d H:i:s");
                    $data['isactive'] = 1;
                    $where = '';
                    $actionflag = 1;
                }
                $Id = $competencylevelmodel->SaveorUpdateCompetencyLevelData($data, $where);
				$menuID = COMPETENCYLEVEL;
                $result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$tableid);
                $this->view->eventact = 'added';
                $close = 'close';
                $this->view->popup=$close;
                $competencylevelArr = $competencylevelmodel->getCompetencylevelList();
		$opt = "";

		if(!empty($competencylevelArr))
		{			
                    foreach ($competencylevelArr as $competencylevelres)
                    {                        
                        $opt .= sapp_Global::selectOptionBuilder($competencylevelres['id'], $competencylevelres['competencylevel']);                                
                    }
		}
                $this->view->compdata = $opt;
                if(isset($_POST['previname']) && $_POST['previname'] != '')
                    $previname = $_POST['previname'];
            }
            else
            {
                $messages = $competencylevelform->getMessages();
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
        $this->view->previname = $previname;
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
			$competencylevelmodel = new Default_Model_Competencylevel();
			  $data = array('isactive'=>0,'modifieddate'=>gmdate("Y-m-d H:i:s"));
			  $where = array('id=?'=>$id);
                          $compentency_data = $competencylevelmodel->getsingleCompetencyLevelData($id);
			  $Id = $competencylevelmodel->SaveorUpdateCompetencyLevelData($data, $where);
			    if($Id == 'update')
				{
                                sapp_Global::send_configuration_mail("Competency Level", $compentency_data[0]['competencylevel']);
				   $menuID = COMPETENCYLEVEL;
				   $result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$id); 
				   $messages['message'] = 'Competency level deleted successfully.';
				    $messages['msgtype'] = 'success';
				}   
				else
                {	$messages['message'] = 'Competency level cannot be deleted.';	
					$messages['msgtype'] = 'error';
				}
			}
			else
			{ 
			 $messages['message'] = 'Competency level cannot be deleted.';
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
			}
			$this->_helper->json($messages);
	}

}

