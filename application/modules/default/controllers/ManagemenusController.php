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
        //echo "<pre>"; print_r($isactiveArr); exit();
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
			
            if($this->_request->getPost())
            { 
                //$defined_menus = array(TIMEMANAGEMENT,RESOURCEREQUISITION,BGCHECKS,STAFFING,COMPLIANCES,REPORTS,BENEFITS,SERVICEDESK,PERFORMANCEAPPRAISAL);
                $defined_menus = unserialize(MANAGE_MODULE_ARRAY);
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
                if(!empty($chk_menu))
                {                                        
                    foreach($chk_menu as $menu)
                    {       
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
	            $jsonlogarr = json_encode($logarr);
	            $menuID = MANAGEMODULE;
	            $actionflag = 2;
	            
                 if(!empty($logmenus))//only activated records are logged in log manager.
                  $menumodel->addOrUpdateMenuLogManager($menuID,$actionflag,$jsonlogarr,$loginUserId,$menuNames);
               
                    $this->_helper->getHelper("FlashMessenger")->addMessage("Modules updated successfully.");	
                    $trDb->commit();		
                    sapp_Global::generateAccessControl();
                    $this->_redirect('managemenus');
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
            /*if(defined('PERFORMANCEAPPRAISAL_M') && $menu == PERFORMANCEAPPRAISAL_M)
            {
                $querystring_menu = "UPDATE main_menu SET isactive = ".$is_active." where id in (".MYPERFORMANCEAPPRAISAL.",".MYTEAMPERFORMANCEAPPRAISAL.")  ";
                $menumodel->UpdateMenus($querystring_menu);

                $querystring_menu = "UPDATE main_privileges SET isactive = ".$is_active." where object in (".MYPERFORMANCEAPPRAISAL.",".MYTEAMPERFORMANCEAPPRAISAL.")  ";
                $menumodel->UpdateMenus($querystring_menu);
            }*/

           
           
        }
    }
}