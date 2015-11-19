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

class Default_HeirarchyController extends Zend_Controller_Action
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
					$loginuserRole = $auth->getStorage()->read()->emprole;
					$loginuserGroup = $auth->getStorage()->read()->group_id;
		}
		$addpermission = 'No';
		$editpermission = 'No';
		$structureModel = new Default_Model_Structure();
		$orgData = $structureModel->getOrgData();
		$heirarchyModel = new Default_Model_Heirarchy();				
		$levelsdata = $heirarchyModel->getlevelsusernames();
		//$baseUrl = $this->getBaseurl();	
		//echo 		$baseUrl;exit;
		$emps = $heirarchyModel->getAllEmployees();
		$vEmps = $heirarchyModel->getVAllEmployees();
		$output = '';
		$empData = array();
		if(count($emps) > 0)
		{
			foreach($emps as $empRecord)
			{						
					$empData[] = array('id'=>$empRecord['id'],'name'=>ucwords($empRecord['name']),'profileimg'=>$empRecord['profileimg']);
			}				
		}				
		$editpermission = sapp_Global::_checkprivileges(HEIRARCHY,$loginuserGroup,$loginuserRole,'edit');
		$addpermission = sapp_Global::_checkprivileges(HEIRARCHY,$loginuserGroup,$loginuserRole,'add');		
		$dataArr = array();				
		$tmplevelsdata = $levelsdata;
		$parentArr = array();		
		for($i=0;$i<sizeof($levelsdata);$i++)
		{
			$parentArr[] = $levelsdata[$i]['parent'];
		}
		$parentArr = array_unique($parentArr);
		$parr = array();
		foreach($parentArr as $parent)
		{
			foreach($tmplevelsdata as $data)
			{
				if($data['parent'] == $parent)
				{
					$parr[$parent][] = array('userid' => $data['userid'],'userfullname' => $data['userfullname'],'profileimg'=>$data['profileimg'],'level_number'=>$data['level_number'],'parent'=>$data['parent'],'jobtitlename'=>$data['jobtitlename']);
					
				}
			}
		}
               
		if(!empty($parr)) {
			$output = "<ul  id='org' style='display:none;'>";			
			$output .=  "<li>
						<i></i>	
						<p class='tags-ctrl'>						 
						  <img class='main-img' border='0' src='".DOMAIN."public/uploads/profile/".$parr[key($parr)][0]['profileimg']."' onerror='this.src=\"".DOMAIN."public/media/images/hierarchy-deafult-pic.jpg\"' />
						  <span class='main-name' title='".ucwords($parr[key($parr)][0]['userfullname'])."' id='".$parr[key($parr)][0]['userid']."'>".$parr[key($parr)][0]['userfullname']."</span>
						  <span class='main-name' title='".ucwords($parr[key($parr)][0]['jobtitlename'])."'>".$parr[key($parr)][0]['jobtitlename']."</span>
						  </p>";
			$output .= $this->hasChildNoEdit($parr[key($parr)][0]['userid'],$parr);
			$output .= " </li></ul>";			
		}
		$this->view->output = $output;	
		$this->view->allEmpdata = $vEmps;
		$this->view->empData = $empData;
		$this->view->orgData = $orgData;
		$this->view->editpermission = $editpermission;
		$this->view->addpermission = $addpermission;
		
	}
	
	public function hasChildNoEdit($parent,$parr)
	{
		//$baseUrl = $this->getBaseurl();
		$output = '';
		if(isset($parr[$parent]))
		{
			$output = "<ul>";
			foreach($parr[$parent] as $pdata)
			{
		      $truncatedName = substr(($pdata['userfullname']),0,9);
			  $output .=  "<li>
			  <i></i>
			  <p class='tags-ctrl'>			  
			  <img class='main-img' border='0' src='".DOMAIN."public/uploads/profile/".$pdata['profileimg']."' onerror='this.src=\"".DOMAIN."public/media/images/hierarchy-deafult-pic.jpg\"' />
			  <span class='main-name' title='".ucwords($pdata['userfullname'])."' id='".$pdata['userid']."'>".$pdata['userfullname']."</span>
			  <span class='main-desig' title='".ucwords($pdata['jobtitlename'])."'>".$pdata['jobtitlename']."</span>
			  </p>";
			  $output .= $this->hasChildNoEdit($pdata['userid'],$parr);
			} 
			$output .= "</ul>";
		}
		else 
		{
			$output .= "</li>";
		}
		return $output;
	}
	
	public function editAction()
	{
		$heirarchyModel = new Default_Model_Heirarchy();				
		$levelsdata = $heirarchyModel->getlevelsusernames();
		//$baseUrl = $this->getBaseurl();		
		$emps = $heirarchyModel->getAllEmployees();
		$structureModel = new Default_Model_Structure();
		$orgData = $structureModel->getOrgData();
		$vEmps = $heirarchyModel->getVAllEmployees();
		$output = '';
		$empData = array();
		if(count($emps) > 0)
		{
                    foreach($emps as $empRecord)
                    {						
                        $empData[] = array('id'=>$empRecord['id'],'name'=>ucwords($empRecord['name']),'profileimg'=>$empRecord['profileimg']);
                    }				
		}		
		$dataArr = array();			
		
		$tmplevelsdata = $levelsdata;
		$parentArr = array();		
		for($i=0;$i<sizeof($levelsdata);$i++)
		{
			$parentArr[] = $levelsdata[$i]['parent'];
		}
		$parentArr = array_unique($parentArr);
		$parr = array();
		foreach($parentArr as $parent)
		{
                    foreach($tmplevelsdata as $data)
                    {
                        if($data['parent'] == $parent)
                        {
                            $parr[$parent][] = array('userid' => $data['userid'],'userfullname' => $data['userfullname'],'profileimg'=>$data['profileimg'],'level_number'=>$data['level_number'],'parent'=>$data['parent'],'jobtitlename'=>$data['jobtitlename']);
                        }
                    }
		}		
		if(!empty($parr)) {
			$output = "<ul  id='org' style='display:none;'>";
			$output .=  "<li>
                                        <i>
                                        <div class='fltright'><b title='Add' onclick='modifylist(\"add\",\"".$parr[0][0]['userid']."\",\"".$parr[0][0]['level_number']."\",\"".$parr[0][0]['parent']."\");' class='sprite addrecord-3'></b>
                                         <b title='Change' onclick='modifylist(\"edit\",\"".$parr[0][0]['userid']."\",\"".$parr[0][0]['level_number']."\",\"".$parr[0][0]['parent']."\");' class='sprite edit-1'></b>
                                         <b title='Delete' onclick='modifylist(\"remove\",\"".$parr[0][0]['userid']."\",\"".$parr[0][0]['level_number']."\",\"".$parr[0][0]['parent']."\");' class='sprite delete-1'></b>
                                </div> </i>
                         <p class='tags-ctrl'>
                                 <img border='0' src='".DOMAIN."public/uploads/profile/".$parr[0][0]['profileimg']."' onerror='this.src=\"".DOMAIN."public/media/images/hierarchy-deafult-pic.jpg\"' />
                                 <span class='main-name' title='".$parr[0][0]['userfullname']."' id='".$parr[0][0]['userid']."'>".$parr[0][0]['userfullname']."</span>
                                 <span class='main-desig'>".$parr[0][0]['jobtitlename']."</span>
                        </p>";
			$output .= $this->hasChild($parr[0][0]['userid'],$parr);
			$output .= " </li></ul>";			
		}	
		$this->view->allEmpdata = $vEmps;
		$this->view->output = $output;		
		$this->view->empData = $empData;
		$this->view->orgData = $orgData;
		$this->view->messages = $this->_helper->flashMessenger->getMessages();		
	}
	
	public function hasChild($parent,$parr)
	{	
		//$baseUrl = $this->getBaseurl();
		$output = '';
		if(isset($parr[$parent]))
		{
			$output = "<ul>";
			foreach($parr[$parent] as $pdata)
			{
		      $truncatedName = substr(($pdata['userfullname']),0,9);
			  $output .=  "<li>
  				  <i>
				  	<div class='fltright'>
					  <b title='Add' onclick='modifylist(\"add\",\"".$pdata['userid']."\",\"".$pdata['level_number']."\",\"".$pdata['parent']."\");' class='sprite addrecord-3'></b>
					  <b title='Change' onclick='modifylist(\"edit\",\"".$pdata['userid']."\",\"".$pdata['level_number']."\",\"".$pdata['parent']."\");' class='sprite edit-1'></b>
					  <b title='Delete' onclick='modifylist(\"remove\",\"".$pdata['userid']."\",\"".$pdata['level_number']."\",\"".$pdata['parent']."\");' class='sprite delete-1'></b>
					  </div>
				  </i>

			  <p class='tags-ctrl'>
	  			  <img border='0' src='".DOMAIN."public/uploads/profile/".$pdata['profileimg']."' onerror='this.src=\"".DOMAIN."public/media/images/hierarchy-deafult-pic.jpg\"' />
					<span class='main-name' title='".$pdata['userfullname']."' id='".$pdata['userid']."'>".$pdata['userfullname']."</span>
					<span class='main-desig'>".$pdata['jobtitlename']."</span>
  				</p>  
			  ";
			  $output .= $this->hasChild($pdata['userid'],$parr);
			} 
			$output .= "</ul>";
		}
		else 
		{
			$output .= "</li>";
		}
		return $output;
	}
	
	/*public function getBaseurl()
	{
		$baseUrl = DOMAIN;
		$baseUrl = rtrim($baseUrl,'/');	

		return $baseUrl;
	}*/
	
	
	public function addlistAction()
	{
		$this->_helper->layout->disableLayout();
		$userid = $this->_getParam('userid'); // child should be added to this userid		
		$level = $this->_getParam('level');	// the level of the child is +1 of this level
		$parent = $this->_getParam('parent'); //parent of the userid to whom child is added - no need
		$actionName = $this->_getParam('actionName');
		$heirarchyModel = new Default_Model_Heirarchy();	
		$emps = $heirarchyModel->getAllEmployees();
		$empData = array();
		if(count($emps) > 0)
		{
			foreach($emps as $empRecord)
			{						
					$empData[] = array('id'=>$empRecord['id'],'name'=>ucwords($empRecord['name']),'profileimg'=>$empRecord['profileimg']);
			}				
		}
		$newlevel = $level;
		$this->view->actionName = $actionName;
		$this->view->childeleparent = $userid;
		$this->view->parentid = $parent;
		$this->view->newlevel = $newlevel;
		$this->view->empData = $empData;		
	}
	
	public function editlistAction()
	{
		$this->_helper->layout->disableLayout();
		$userid = $this->_getParam('userid'); // child should be added to this userid
		$olduserid = $this->_getParam('olduserid'); // in case of edit
		$level = $this->_getParam('level');	// the level of the child is +1 of this level
		$parent = $this->_getParam('parent'); //parent of the userid to whom child is added - no need
		$actionName = $this->_getParam('actionName');
		$heirarchyModel = new Default_Model_Heirarchy();	
		$emps = $heirarchyModel->getAllEmployees();
		$empData = array();
		if(count($emps) > 0)
		{
			foreach($emps as $empRecord)
			{						
					$empData[] = array('id'=>$empRecord['id'],'name'=>ucwords($empRecord['name']),'profileimg'=>$empRecord['profileimg']);
			}				
		}
		$newlevel  = $level;
		$this->view->actionName = $actionName;		
		$this->view->parentid = $parent;
		$this->view->olduserid = $olduserid;
		$this->view->empData = $empData;
		$this->view->newlevel = $newlevel;
	}
	
	public function saveadddataAction()
	{
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity()){
					$loginUserId = $auth->getStorage()->read()->id;
					$loginuserRole = $auth->getStorage()->read()->emprole;
					$loginuserGroup = $auth->getStorage()->read()->group_id;
		}
		$updateresult = '';
		$newuserid = $this->_getParam('newuserid'); // userid newly inserting	when action = add and userid who is replaced by when action = edit	
		$level = $this->_getParam('level');	// level of the user
		$parent = $this->_getParam('parent'); //parent of the user
		$actionName = $this->_getParam('actionName');	//Whether add or edit
		
		$heirarchyModel = new Default_Model_Heirarchy();
		$insertorupdateModel = new Default_Model_Insertorupdate();
		$newuserArr = explode(',',$newuserid);
		if(isset($newuserid) && $newuserid != '' && $newuserid != 0)
		{
			for($i=0;$i<sizeof($newuserArr);$i++)
			{
				$insertdata = array(
						'level_number'		=>		$level,
						'parent'			=>		$parent,
						'userid'			=>		$newuserArr[$i],
						'createdby'			=>		$loginUserId,
						'modifiedby'		=>		$loginUserId,
						'createddate'		=>		gmdate("Y-m-d H:i:s"),
						'modifieddate'		=>		gmdate("Y-m-d H:i:s"),
						'isactive'			=>		1
				);
				$updatedata = array(
						'level_number'		=>		$level,
						'parent'			=>		$parent,
						'modifiedby'		=>		$loginUserId,
						'modifieddate'		=>		gmdate("Y-m-d H:i:s"),
						'isactive'			=>		1
					);
				$insertorupdateModel->insertOrUpdate($insertdata,$updatedata);
			}	
			$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Hierarchy data is added successfully."));
			$updateresult['result'] = 'saved';
		}else{
			$updateresult['result'] = 'error';
		}
		$this->_helper->json($updateresult);
	}
	
	public function saveeditdataAction()
	{
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity()){
					$loginUserId = $auth->getStorage()->read()->id;
					$loginuserRole = $auth->getStorage()->read()->emprole;
					$loginuserGroup = $auth->getStorage()->read()->group_id;
		}
		
		$updateresult = '';
		$newuserid = $this->_getParam('newuserid'); // userid newly inserted
		$level = $this->_getParam('level');	// level of the user		
		$actionName = $this->_getParam('actionName');	//Whether add or edit
		$olduserid = $this->_getParam('olduserid'); //who is being replaced in edit action
		$olduserparent = $this->_getParam('olduserparent'); // parent of the old user
		
		$heirarchyModel = new Default_Model_Heirarchy();
		$insertorupdateModel = new Default_Model_Insertorupdate();
		
		if(isset($newuserid) && $newuserid != '' && $newuserid != 0)
		{
			if($actionName == 'edit')
			{	
				/* starting transaction */
				$trDb = Zend_Db_Table::getDefaultAdapter();					
				$trDb->beginTransaction();
				try
				{
					/*Removing old user */
					$data = array(				
						'modifiedby'		=>		$loginUserId,				
						'modifieddate'		=>		gmdate("Y-m-d H:i:s"),
						'isactive'			=>		0
					);
					$userwhere = array(										
							'userid=?'			=>		$olduserid
							);				
					$heirarchyModel->SaveorUpdateleveldata($data,$userwhere);
					
					/* Adding new user */
					$insertdata = array(
							'level_number'		=>		$level,
							'parent'			=>		$olduserparent,
							'userid'			=>		$newuserid,
							'createdby'			=>		$loginUserId,
							'modifiedby'		=>		$loginUserId,
							'createddate'		=>		gmdate("Y-m-d H:i:s"),
							'modifieddate'		=>		gmdate("Y-m-d H:i:s"),
							'isactive'			=>		1
					);
					$updatedata = array(
							'level_number'		=>		$level,
							'parent'			=>		$olduserparent,
							'modifiedby'		=>		$loginUserId,
							'modifieddate'		=>		gmdate("Y-m-d H:i:s"),
							'isactive'			=>		1
					);
					$where = '';				
					$insertorupdateModel->insertOrUpdate($insertdata,$updatedata);
					
					
					
					
					/* Pointing old user's children to new user*/
					$data = array(
						'parent'			=>		$newuserid,
						'modifiedby'		=>		$loginUserId,				
						'modifieddate'		=>		gmdate("Y-m-d H:i:s")				
						);
					$where = array(
							'parent=?'			=>		$olduserid,
							'isactive=?'		=>		1
						);				
					$heirarchyModel->SaveorUpdateleveldata($data,$where);	
						
					$trDb->commit();	
					$updateresult['result'] = 'updated';
					$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Hierarchy data is updated successfully."));
				}
				catch(Exception $e)
				{
					$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Hierarchy data cannot be updated."));
					$updateresult['result'] = 'failed';
					$trDb->rollBack();								
				}
				/* End Transaction */
			}					
		}else{
			$updateresult['result'] = 'error';
		}
		
		$this->_helper->json($updateresult);
	}
	
	public function deletelistAction()
	{
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity()){
					$loginUserId = $auth->getStorage()->read()->id;
					$loginuserRole = $auth->getStorage()->read()->emprole;
					$loginuserGroup = $auth->getStorage()->read()->group_id;
		}
		$updateresult = '';
		$userid = $this->_getParam('userid'); // id of the user who is being removed
		$level = $this->_getParam('level');	// level of the user
		$parent = $this->_getParam('parent'); //parent of the user
		
		$trDb = Zend_Db_Table::getDefaultAdapter();					
		$trDb->beginTransaction();
		try
		{
			$heirarchyModel = new Default_Model_Heirarchy();
			$data = array(				
					'modifiedby'		=>		$loginUserId,				
					'modifieddate'		=>		gmdate("Y-m-d H:i:s"),
					'isactive'			=>		0
				);
			$userwhere = array(					
					'parent=?'			=>		$parent,
					'userid=?'			=>		$userid
					);
			$heirarchyModel->SaveorUpdateleveldata($data,$userwhere);
			
			$this->deletechildren($userid,$loginUserId);			
			$trDb->commit();
			$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Hierarchy data is removed successfully."));			
			$updateresult['result'] = 'updated';
		}
		catch(Exception $e)
		{
			$updateresult['result'] = 'failed';
			$trDb->rollBack();								
		}
		$this->_helper->json($updateresult);
	}
	
	public function deletechildren($userid,$loginUserId)
	{
		$heirarchyModel = new Default_Model_Heirarchy();
		$childArr = $heirarchyModel->getchildren($userid);				
		
		if(!empty($childArr))
		{
			foreach($childArr as $child)
			{	
				$deleteuserid = $child['userid'];
				$data = array(				
					'modifiedby'		=>		$loginUserId,				
					'modifieddate'		=>		gmdate("Y-m-d H:i:s"),
					'isactive'			=>		'0'
				);
				$userwhere = array(										
						'userid=?'			=>		$userid
						);
				$heirarchyModel->SaveorUpdateleveldata($data,$userwhere);
				$this->deletechildren($deleteuserid,$loginUserId);	
			}
		}else{
			$data = array(				
					'modifiedby'		=>		$loginUserId,				
					'modifieddate'		=>		gmdate("Y-m-d H:i:s"),
					'isactive'			=>		'0'
				);
				$userwhere = array(										
						'userid=?'			=>		$userid
						);
				$heirarchyModel->SaveorUpdateleveldata($data,$userwhere);
		}
		
	}
	
	
}
?>