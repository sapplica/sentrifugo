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

class Default_Model_Settings extends Zend_Db_Table_Abstract{
   protected $_name = 'main_settings';

   public function getMenuName($menuid,$isactive=''){
		
      $menuIdsArr = explode(',',$menuid);
	  $sign = '/#';
	  $query = '';
	  if($isactive !='')
	  $query = " AND m.isactive=1";
	  
      $select = $this->select()
						->setIntegrityCheck(false)
						->from(array('m'=>'main_menu'),array('m.menuName','m.url','m.iconPath','m.id'))
						
	                    ->where('m.url != "'.$sign.'" '.$query.' AND m.id IN(?)',$menuIdsArr);

		return $this->fetchAll($select)->toArray();
			

   }
   
    public function fetchMenuName($menuid){
		
      
	  $sign = '/#';
      $select = $this->select()
						->setIntegrityCheck(false)
						->from(array('m'=>'main_menu'),array('m.menuName','m.url','m.iconPath'))
						->where('m.id="'.$menuid.'" AND m.url != "'.$sign.'"');
	                    
        
		return $this->fetchAll($select)->toArray();
			

   }

   public function saveSettingsMenu($data)
	{
		
		$this->insert($data);
		$id=$this->getAdapter()->lastInsertId('main_settings');
		return $id;
	}

	public function getMenuIdCount($userid){
      $select = $this->select()
						->setIntegrityCheck(false)
						->from(array('s'=>'main_settings'), array('count'=>'COUNT(s.id)'))
						->where('s.userid="'.$userid.'" AND s.flag=1 AND s.isactive=1');
	  
		return $this->fetchAll($select)->toArray();

	}

	public function getMenuIds($userid,$flag = 1){

		$select = $this->select()
						->setIntegrityCheck(false)
						->from(array('s'=>'main_settings'),array('s.menuid'))
						->where('s.userid='.$userid.' AND s.flag="'.$flag.'" AND s.isactive=1');
		return $this->fetchAll($select)->toArray();

	}
	
	public function getIconIds($userid){

		$select = $this->select()
						->setIntegrityCheck(false)
						->from(array('s'=>'main_settings'),array('s.menuid','s.menuicon'))
						->where('s.userid="'.$userid.'" AND s.flag=2 AND s.isactive=1');
		return $this->fetchAll($select)->toArray();

	}

	public function getActivemenuCount($userid){
       $select = $this->select()
						->setIntegrityCheck(false)
						->from(array('s'=>'main_settings'), array('count'=>'COUNT(s.id)'))
						->where('s.userid="'.$userid.'" AND s.flag=1 AND s.isactive=1 AND s.menuid <> ""');
	  
		return $this->fetchAll($select)->toArray();


	}
	
	public function getActiveiconCount($userid){
      $select = $this->select()
						->setIntegrityCheck(false)
						->from(array('s'=>'main_settings'), array('count'=>'COUNT(s.id)'))
						->where('s.userid='.$userid.' AND s.flag=2 AND s.isactive=1 AND s.menuid <> ""');
	  
		return $this->fetchAll($select)->toArray();


	}
	
	public function getMenucountwithflag($userid){
	
	   $flagArr = array(1,2);
       $select = $this->select()
						->setIntegrityCheck(false)
						->from(array('s'=>'main_settings'), array('flag','count'=>'COUNT(s.id)'))
						->where('s.userid="'.$userid.'" AND s.isactive=1 AND s.menuid <> ""  AND s.flag IN(?)',$flagArr)
						->group('s.flag');
	    
		return $this->fetchAll($select)->toArray();


	}
	
	public function getActiveCountSettings($userid,$flag){
       $select = $this->select()
						->setIntegrityCheck(false)
						->from(array('s'=>'main_settings'), array('count'=>'COUNT(s.id)'))
						->where('s.userid="'.$userid.'" AND s.flag="'.$flag.'" AND s.isactive=1');
	  
		return $this->fetchAll($select)->toArray();


	}

	public function addOrUpdateMenus($data,$where){

        if($where != ''){
			$this->update($data, $where);
			return 'update';
		} else {
			$this->insert($data);
			$id=$this->getAdapter()->lastInsertId('main_settings');
			return $id;
		}

	}
	public function getNavigationIds()
	{
		$select = $this->select()
						->setIntegrityCheck(false)
						->from(array('a' => 'main_objmenu'),array('menuId', 'parent'))
						->join(array('b'=>'main_menu'),'b.id = a.menuId');
		try{
			return $this->fetchAll($select)->toArray();		
		}
		catch(Zend_Exception $e)
		{
			echo $e->getMessage();
		}
	}

	public function insertnavid($menuId,$navIds)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$sql=$db->query("UPDATE main_objmenu SET nav_ids = '".$navIds."' WHERE menuId = ".$menuId);
		
	}
	
	public function getOpeningPositinDate()
	{
		$select = $this->select()
						->setIntegrityCheck(false)
						->from(array('rp' => 'recruitmentpositions'),array('recrpostitle'))
						->where('date(rp.recrposenddate) <=now() AND rp.sentrifugo_status = 1');
		
		try{
			return $this->fetchAll($select)->toArray();		
		}
		catch(Zend_Exception $e)
		{
			echo $e->getMessage();
		}
	}
	public function getallmenuids($userid)
	{	
		$select = $this->select()
						->setIntegrityCheck(false)
						->from(array('s'=>'main_settings'),array('s.menuid','s.flag'))
						->where('s.userid='.$userid.' AND s.isactive=1');
		$data = $this->fetchAll($select)->toArray();		
		return $data;
		
	}
	
	public function getallmenunames($allmenuids,$isactive='') 
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		if($isactive !='')
		$menu = $db->query("select distinct id, menuName,iconPath from main_menu where isactive = 1 and FIND_IN_SET(id,'".$allmenuids."');");
		else
		$menu = $db->query("select distinct id, menuName,iconPath from main_menu where FIND_IN_SET(id,'".$allmenuids."');");
		$menunames = $menu->fetchAll();	
		return $menunames;
	}
	
}
?>