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

class Default_ManagemenusController extends Zend_Controller_Action
{

	private $options;
	
	/**
	 * Init
	 * 
	 * @see Zend_Controller_Action::init()
	 */
    public function init()
    {
        $this->_options= $this->getInvokeArg('bootstrap')->getOptions();
    }

    /**
     * @name indexAction
     *      
     *  
     *  @author Mainak
     *  @version 1.0
     */
    
    public function indexAction()
    {			
        $menu_model = new Default_Model_Menu();
        $isactiveArr = $menu_model->getisactivemenus();
        $this->view->isactArr = $isactiveArr;
        $this->view->messages = $this->_helper->flashMessenger->getMessages();
    }
	
    public function saveAction()
    {
    
        $auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity())
        {
            $loginUserId = $auth->getStorage()->read()->id;
        }
        $date = new Zend_Date(); 
		
        $trDb = Zend_Db_Table::getDefaultAdapter();		
        // starting transaction
        $trDb->beginTransaction();	
        try 
        { 
			
            //if($this->_request->getPost() && $chk_menu != '' && $chk_menu != ',')
            if($this->_request->getPost())
            { 
                $defined_menus = array(TIMEMANAGEMENT,PERFORMANCEAPPRAISAL_M,RESOURCEREQUISITION,BGCHECKS,STAFFING,COMPLIANCES,REPORTS,BENEFITS);
                $chk_menu = $this->_request->getParam('chk_menu');// menus to be activate
				$chk_menu = trim($chk_menu,',');
				$logmenus = $chk_menu;
				if($chk_menu != '' && $chk_menu != ',' && !is_array($chk_menu))
				{
					$chk_menu = explode(',',$chk_menu);					
				}
                else 
                    $chk_menu = array();
                $disable_menus = array_diff($defined_menus, $chk_menu); //menus to be deactivated                              
                //echo "<pre>"; print_r($defined_menus); echo "</pre>";exit;
				//echo "<pre>"; print_r($defined_menus);print_r($chk_menu);print_r($disable_menus);echo "</pre>"; die;
                if(!empty($chk_menu))
                {                                        
                    foreach($chk_menu as $menu)
                    {       
						//echo $menu.'</br>';
                        $this->save_helper(1,$menu);                                                          
                    }
                                        
                }
                if(!empty($disable_menus))
                {
                    foreach($disable_menus as $menu)
                    {                                                
                        $this->save_helper(0,$menu);                                                          
                    }
                } 

	            // Code to Update Logmanager table with comma separated menuids 
	            		
					 $menumodel = new Default_Model_Menu();
		             $menuNames = $menumodel->getMenusNamesByIds($logmenus); 
                    
		             
	            $logarr = array('userid' => $loginUserId,
	                            'recordid' =>$logmenus,
	                            'childrecordid' => $menuNames,
	                            'date' => $date->get('yyyy-MM-dd HH:mm:ss')
	                            );
	            //echo "<pre>";print_r($logarr);echo "</pre>";
	            $jsonlogarr = json_encode($logarr);
	            $menuID = MANAGEMODULE;
	            $actionflag = 2;
	            
                 if(!empty($logmenus))//only activated records are logged in log manager.
                  $menumodel->addOrUpdateMenuLogManager($menuID,$actionflag,$jsonlogarr,$loginUserId,$menuNames);
               
                    $this->_helper->getHelper("FlashMessenger")->addMessage("Modules updated successfully.");	
                    $trDb->commit();		
                    sapp_Global::generateAccessControl();
                    $this->_redirect('managemenus');
                 
                /*else
                {
                  $this->_helper->getHelper("FlashMessenger")->addMessage("No Menus were added.");
                  $this->_redirect('managemenus');				  
                }*/ 				
            }
			else
            {
                $this->_helper->getHelper("FlashMessenger")->addMessage("No Menus were added.");
                $this->_redirect('managemenus');				  
            } 	
        }
        catch (Exception $e) 
        {		
            $trDb->rollBack();			
           // $msg = Zend_Registry::get('exception_msg');	
            $msg = $e->getMessage();//echo $msg; die;
            $this->_helper->getHelper("FlashMessenger")->addMessage($msg);
            $this->_redirect('managemenus');	  			

        }
    }//end of save function.
    /**
     * This function helps in saving manage menus.
     * @param integer $is_active  = 1 is to activate,0 is to deactivate
     * @param integer $menu       = id of the parent menu to be saved
     */
    public function save_helper($is_active,$menu)
    {
   
        $auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity())
        {
            $loginUserId = $auth->getStorage()->read()->id;
        } 
        $date = new Zend_Date();
        $menumodel = new Default_Model_Menu();
        $menu_childs = $menumodel->getMenusWithChilds($menu);
        $resArrString = implode(",",$menu_childs);	 
        if($resArrString != '')
        {
            $where = " id in (".$resArrString.")";
            $where_privi = " object in (".$resArrString.")";		
            $querystring_menu = "UPDATE main_menu SET isactive = ".$is_active." where $where  ";
            $menumodel->UpdateMenus($querystring_menu);        
            $querystring_menu = "UPDATE main_privileges SET isactive = ".$is_active." where $where_privi  ";
            $menumodel->UpdateMenus($querystring_menu);
            if($menu == PERFORMANCEAPPRAISAL_M)
            {
                $querystring_menu = "UPDATE main_menu SET isactive = ".$is_active." where id in (".MYPERFORMANCEAPPRAISAL.",".MYTEAMPERFORMANCEAPPRAISAL.")  ";
                $menumodel->UpdateMenus($querystring_menu);

                $querystring_menu = "UPDATE main_privileges SET isactive = ".$is_active." where object in (".MYPERFORMANCEAPPRAISAL.",".MYTEAMPERFORMANCEAPPRAISAL.")  ";
                $menumodel->UpdateMenus($querystring_menu);
            }

           
           
        }
    }
        /*
         public function saveAction()
    {
        $auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity())
        {
            $loginUserId = $auth->getStorage()->read()->id;
        } 
        $trDb = Zend_Db_Table::getDefaultAdapter();		
        // starting transaction
        $trDb->beginTransaction();	
        try 
        {  
            if($this->_request->getPost())
            {
                $defined_menus = array(130,18,19,5,6,7,8);
                                $chk_menu = $this->_request->getParam('chk_menu');
                                
                                $totalmenusize = $this->_request->getParam('totalmenusize');
                                //echo sizeof($chk_menu['child']);exit;
                                //echo "<pre>";print_r(array_diff($defined_menus, $chk_menu));echo "</pre>";exit;

                                if(!empty($chk_menu))
                                {
                                        $date = new Zend_Date();
                                                      $menumodel = new Default_Model_Menu();
                                                      $resArrData =  $menumodel->getExcludedMenuids();
                                                              foreach($resArrData as $resdata)
                                                              {
                                                                $resArr[]= $resdata['id'];
                                                              }
                                                      $resArrString = implode(",",$resArr);	
                                                      $actionflag = 2;
                                                      $totalisactivezero = $menumodel->UpdateMenuTable($resArrString);
                                                      $where = '';
                                                      foreach ($chk_menu['child'] as $menuids)
                                                      {
                                                        $where.= " id= $menuids OR ";
                                                        $menulogids[] = $menuids;
                                                      }
                                                      //echo "<pre>";print_r($menulogids);exit;
                                                      $menuidstr = implode(",",$menulogids);
                                                      $where = trim($where," OR");
                                                      $querystring = "UPDATE main_menu SET isactive = 1 where $where  ";
                                                      $menusave = $menumodel->UpdateMenus($querystring);
                                                      // Code to Update Logmanager table with comma separated menuids 
                                                              $logarr = array('userid' => $loginUserId,
                                                                      'recordid' =>$menuidstr,
                                                                      'childrecordid' => '',
                                                                      'date' => $date->get('yyyy-MM-dd HH:mm:ss')
                                                                      );
                                                          $jsonlogarr = json_encode($logarr);
                                                              $menuID = 0;
                                                              $actionflag = 2;
                                                              $id = $menumodel->addOrUpdateMenuLogManager($menuID,$actionflag,$jsonlogarr,$loginUserId);

                                                      $this->_helper->getHelper("FlashMessenger")->addMessage("Menus added successfully.");	
                          $trDb->commit();							

                                                              
                                                              $this->_redirect('managemenus');
                                              } 
                                              else
                                              {
                                                $this->_helper->getHelper("FlashMessenger")->addMessage("No Menus were added.");
                                                $this->_redirect('managemenus');				  
                                              } 				
                                      }
              }
              catch (Exception $e) 
              {
                      $trDb->rollBack();			
                      $msg = Zend_Registry::get('exception_msg');	
          $this->_helper->getHelper("FlashMessenger")->addMessage($msg);
                      $this->_redirect('managemenus');	  			
                      //$this->_helper->json(array('saved'=>'exception','exception'=>$msg));	
              }

	}
         */
	
	/*public function managemenuAction(){
		$this->view->msg='this is manage menu';
	}
	public function sitepreferencesAction()
	{
	}
	
	public function newAction()
	{
		$menu_model = new Default_Model_Menu();
		$isactiveArr = $menu_model->getisactivemenus();
		$this->view->isactArr = $isactiveArr;
		$this->view->messages = $this->_helper->flashMessenger->getMessages();
	}*/
}