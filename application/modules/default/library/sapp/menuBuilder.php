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

class sapp_menuBuilder 
{
	public $privelegesMenuIdsStr = '';
	public $empRole = '';
	public $class_name = '';
	public $actionData = '';
	public $strMenuName = '';
	public $src = '';
	public $menu_name = '';
	public $tmpMenuDataArr = array();
     /**
     * @var Zend_Loader_PluginLoader
     */
    public $pluginLoader;
 
    /**
     * Constructor: initialize plugin loader
     * 
     * @return void
     */
    public function __construct()
    {
        $this->pluginLoader = new Zend_Loader_PluginLoader();
    }
	/**
	**	1. gets menudata from database
	**	2. prepares the first and second level menuids array
	**	3. builds the menu tree html
	**	4. con = settings for settings menu
	**/
 	public static function buildMenu($con="",$userRole = 2)
	{
		$uploadPath = MEDIA_PATH.'/public/media/images/menuIcons/';	
		//$uploadPath = BASE_URL.'/public/media/images/menuIcons/';	
		/*self::getMenuIdsByRole($userRole);
		if(empty($this->privelegesMenuIdsStr))
		{
			echo "No menus for this role";
			die;
		}*/
		/** 
		**	get menu and obj-menu data
		**/
		$sql = "select mid.id,mid.menuName,mid.iconPath, mid.url,ob.parent
				from main_objmenu ob join main_menu mid on ob.menuId = mid.id 
				where ob.isactive = 1 ";
		//$sql .= " and ob.menuId IN (".$this->privelegesMenuIdsStr.")";		
		$sql .= "order by ob.parent , mid.id ";
		
		$db = Zend_Db_Table::getDefaultAdapter();
		$sql = $db->query($sql);
		$res = $sql->fetchAll();

		$tmpArr = array();
		$htmlStr = '';

		if(!empty($res))
		{
			/**
			**	prepare
			**	1. $tmpMenuArr - array with menuid as index and parent as value
			**	2. $this->tmpMenuDataArr - array with menuid as index and the menudata as its value
			**/
			$tmpMenuArr = array();
			
			for($i = 0; $i < sizeof($res); $i++)
			{
				$tmpMenuArr[$res[$i]['id']] = $res[$i]['parent'];
				$tmp = array();
				$tmp["parent"] =  $res[$i]['parent'];
				$tmp["menuName"] =  $res[$i]['menuName'];
				$tmp["iconPath"] =  $res[$i]['iconPath'];
				$tmp["url"] =  $res[$i]['url'];

				$this->tmpMenuDataArr[$res[$i]['id']] = $tmp;
			}
			
			/**
			** prepare an array with parent and second level menus - $tmpArr
			**/
			$emptyArr = array();
			foreach($tmpMenuArr as $key => $value)
			{
				if($value == 0 && !array_key_exists($value,$tmpArr))
					$tmpArr[$key] = $emptyArr;
				else if(array_key_exists($value,$tmpArr) && !array_key_exists($key,$tmpArr[$value]))
					$tmpArr[$value][$key] = array();				
			}
			/**
			** prepare the html for the menu tree
			**/
			try
			{
				
				$w=0;$htmlStr = '';
				foreach($tmpArr as $key => $arr)
				{
					$tmpArrKeys = array_keys($arr);
					self::prepareMenuData(sizeof($tmpArrKeys),$key,1);
					if($con == 'settings')
					{
						$htmlStr.= "<li  class='has-sub '><a class='".$this->class_name."' id='".$key."' href='javascript:void(0);' title='".ucfirst($this->menu_name)."'>";	
						$htmlStr.= "<img src= '".$this->src."' onerror = 'this.src=\" ".$baseUrlStr."/public/media/images/home_icon.png\"' />";
						$htmlStr.= "<span>".$this->strMenuName."</span></a>";
					}
					else
					{
						$htmlStr.= "<li  class='has-sub '>
								<a class='".$this->class_name."' ".$this->actionData." title='".ucfirst($this->menu_name)."'>";
						$htmlStr.= "<img src= '".$this->src."' onerror = 'this.src=\" ".$baseUrlStr."/public/media/images/home_icon.png\"' />";
						$htmlStr.= "<span>".$this->strMenuName."</span></a>";
					}
					
					
					if(!empty($tmpArrKeys))
					{
						$htmlStr.= "<ul>";
						for($k = 0; $k < sizeof($tmpArrKeys); $k++)
						{
							$subMenuArrKeys = array_keys($tmpMenuArr,$tmpArrKeys[$k]);
							self::prepareMenuData(sizeof($subMenuArrKeys),$tmpArrKeys[$k],2);

							if($con == 'settings')
							{
								$htmlStr.= "<li  class='has-sub '><a class='".$this->class_name."' id='".$tmpArrKeys[$k]."' href='javascript:void(0);' title='".ucfirst($this->menu_name)."'><span>".$this->strMenuName."</span></a>";			
							}
							else
							{	
								$htmlStr.= "<li class='has-sub '>
									<a class='".$this->class_name."' ".$this->actionData." title='".ucfirst($this->menu_name)."'>
										<span>".$this->strMenuName."</span></a>";
							}
								if(sizeof($subMenuArrKeys) > 0)
								{
									$htmlStr.= "<ul>";	
										for($j = 0; $j < sizeof($subMenuArrKeys); $j++)
										{
											$thirdMenuArrKeys = 0;
											self::prepareMenuData($thirdMenuArrKeys,$subMenuArrKeys[$j],3);
											if($con == 'settings')
											{
												$htmlStr.= "<li  class='has-sub '><a class='".$this->class_name."' id='".$subMenuArrKeys[$j]."' href='javascript:void(0);' title='".ucfirst($this->menu_name)."'><span>".$this->strMenuName."</span></a></li>";			
											}
											else
											{
												$htmlStr.= "<li class='has-sub'>
													<a class='".$this->class_name."' ".$this->actionData." title='".ucfirst($this->menu_name)."'>
														<span>".$this->strMenuName."</span></a></li>";
											}
										}
									$htmlStr.= "</ul>";
								}
							$htmlStr.= "</li>";
						}
						$htmlStr.= "</ul>";
					}
					$htmlStr.= "</li>";
					$w++;
					//if($w == (sizeof($tmpArr))) 
					//	echo $htmlStr;
				}
			}
			catch(Exception $e)
			{
				echo $e->getMessage();
			}
		}
		return;
	}
	/**
	** prepares the menu data for the first, second and third level menu items
	** based on the child items array count, the arrow class and menu name length are determined
	**/
	public static function prepareMenuData($keysArrSize,$menuId,$level)
	{
		if(empty($baseUrlStr))
			$baseUrlStr = $this->serverUrl().$this->baseUrl();	
		
		$uploadPath = $baseUrlStr.'/public/media/images/menuIcons/';

		$menu_id = $this->tmpMenuDataArr[$menuId]['id'];
		$menu_name = $this->tmpMenuDataArr[$menuId]['menuName'];
		$ParentId = $this->tmpMenuDataArr[$menuId]['parent'];
		$menu_link = $baseUrlStr.$this->tmpMenuDataArr[$menuId]['url'];
		$menu_icon = $this->tmpMenuDataArr[$menuId]['iconPath'];
		
		if($menu_icon != '')
			$src = $uploadPath.$menu_icon;
		else
			$src = $baseUrlStr.'/public/media/images/home_icon.png';

		$class_name = '';
		if($keysArrSize > 0)
			$class_name = 'menutag parentmenu';
		else
			$class_name = 'childelement menutag';				
	
		/* To remove href and put onclick javascript void for parent menus */
		$actionData = '';
		if($menu_link == $baseUrlStr.'/#') 
			$actionData = "onclick = 'javascript:void(0);'";
		else
			$actionData = "href='".$menu_link."'";	

		if($level == 1)
		{
			if($keysArrSize == 0) //Lines 137 to 142 added - Display of menu name with 30 characters, if the menu doesn't have children
			{
				if(strlen($menu_name) > 30)
				$strMenuName =  substr($menu_name,0,30)."...";
				else $strMenuName =  $menu_name;
			}
			else if(strlen($menu_name) > 20)
				$strMenuName =  substr($menu_name,0,20)."...";
			else
				$strMenuName =  $menu_name;
			
		}
		else if($keysArrSize == 0)
		{
			if(strlen($menu_name) > 30)
				$strMenuName =  substr($menu_name,0,30)."...";
			else
				$strMenuName =  $menu_name;
			
		}
		else
		{
			if(strlen($menu_name) > 23)
				$strMenuName =  substr($menu_name,0,23)."...";				
			else
				$strMenuName =  $menu_name;
			
		}				
		$this->class_name = $class_name;
		$this->actionData = $actionData;
		$this->strMenuName = $strMenuName;
		$this->src = $src;
		$this->menu_name = $menu_name;
	}
	public static function getMenuIdsByRole($userRole)
	{
		$privelegesMenuIds = array();
		$selectQuery = "select p.object from main_privileges p where p.role = ".$userRole;
		$db = Zend_Db_Table::getDefaultAdapter();
		$sql = $db->query($selectQuery);
		$result = $sql->fetchAll();
		$tmpArr = array();
		foreach ($result as $sub) {
		  $tmpArr[] = implode(',', $sub);
		}
		$resultstring = implode(',', $tmpArr);
		if($resultstring)
		{
		    try
					{
						$qry = "select ob.menuId, ob.nav_ids from main_objmenu ob 
								where ob.menuId IN (".$resultstring.") or ob.nav_ids IN (".$resultstring.") ";		
						$db = Zend_Db_Table::getDefaultAdapter();
						$sqlRes = $db->query($qry);
						$navRes = $sqlRes->fetchAll();
						if(!empty($navRes))
						{
						    for($n = 0; $n < sizeof($navRes); $n++)
							{
								$navIdsArr[] = trim($navRes[$n]['nav_ids'],',');
							}
						}
					}
					catch(Exception $e)
					{
						echo "Error Encountered - ".$e->getMessage();
					}
					if(!empty($navIdsArr))
						{
							$tmpStr = implode(",",$navIdsArr);
							$tmpArr = array_unique (explode(",",$tmpStr));
							$this->privelegesMenuIdsStr = implode(",",$tmpArr);
						}
		}
		/*if(!empty($result))
		{
			foreach($result as $res)
			{
				if(!empty($res['object']))
				{
					try
					{
						$qry = "select ob.menuId, ob.nav_ids from main_objmenu ob 
								where ob.menuId = ".$res['object']." or ob.nav_ids like '%,".$res['object'].",%' and ob.isactive = 1";
						$db = Zend_Db_Table::getDefaultAdapter();
						$sqlRes = $db->query($qry);
						$navRes = $sqlRes->fetchAll();
						//echo "<pre>";print_r($navRes);echo "</pre>";die;
						if(!empty($navRes))
						{
							$navIdsArr = explode(",",trim($navRes[0]['nav_ids'],","));
							//echo sizeof($navIdsArr);
							//echo "<pre>";print_r($navIdsArr);echo "</pre>";die;
							for($n = 0; $n < sizeof($navIdsArr); $n++)
							{
								if(!in_array($privelegesMenuIds, $navIdsArr[$n]) === true)
									array_push($privelegesMenuIds, $navIdsArr[$n]);
							}
						}
						
					}
					catch(Exception $e)
					{
						echo "Error Encountered - ".$e->getMessage();
					}
							
				}
			}
			echo "<pre>";print_r($privelegesMenuIds);echo "</pre>";die;
			if(!empty($privelegesMenuIds))
			{
				
				echo $this->privelegesMenuIdsStr = implode(",",$privelegesMenuIds);
			}
		}*/
		else
		{
			$this->privelegesMenuIdsStr ="";
		}
	}
	public static function getShortcutIcons($userId)
	{
		$settingsModel = new Default_Model_Settings();
		
		$iconidcount = $settingsModel->getActiveiconCount($userId);
		//echo"<pre>";print_r($iconidcount);exit;
		//echo "<pre>";print_r($menuIdsArr);exit;
		$shortcutsStr = '<ul>';
		if($iconidcount[0]['count'] > 0)
		{
			$menuIdsArr = $settingsModel->getMenuIds($userId,2);
			//print_r($menuIdsArr);		
			if(!empty($menuIdsArr) === true)
			{
				$menuIdsStr = $menuIdsArr[0]['menuid'];
				//print_r($menuIdsStr);
				$menuDetailsArr = $settingsModel->getMenuName($menuIdsStr);

				//print_r($menuDetailsArr);

				for($s = 0; $s < sizeof($menuDetailsArr); $s++)
				{
					$menuName = $menuDetailsArr[$s]['menuName'];
					$tmpMenuUrl = $menuDetailsArr[$s]['url'];

					//echo $s." >> ".(strpos($tmpMenuUrl,'http://') === false)."<br/>";

					if($tmpMenuUrl && (strpos($tmpMenuUrl,'http://') === false || strpos($tmpMenuUrl,'http://') === false  || strpos($tmpMenuUrl,'http://') === false))
					{
						$menuUrl = DOMAIN.substr($tmpMenuUrl,1,strlen($tmpMenuUrl));
					}
					else if(strpos($tmpMenuUrl,'http://') === true || strpos($tmpMenuUrl,'http://') === true  || strpos($tmpMenuUrl,'http://') === true)
						$menuUrl = $tmpMenuUrl;
					else
						$menuUrl = 'javascript:void(0);';
						
                    if($menuDetailsArr[$s]['iconPath'] !='') 
					  $menuIcon = 'images/menuIcons/shortcuts/'.$menuDetailsArr[$s]['iconPath'];
                    else
                      $menuIcon = 'images/sampleimg.png';	

                    //$shortcutsStr.='<li><a href="'.$menuUrl.'"><img src="'.MEDIA_PATH.'images/'.$menuIcon.'" onerror=this.src="'.MEDIA_PATH.'images/sampleimg.png"; height="33" width="33" border="0" /></a></li>';					  
					$shortcutsStr.='<li><a href="'.$menuUrl.'"><img src="'.MEDIA_PATH.''.$menuIcon.'"  height="33" width="33" border="0" /></a></li>';
				}										

				echo $shortcutsStr.='</ul>';

			}
			else
			{
				echo 'You have not configured your shortcuts. <a href="'.DOMAIN.'viewsettings/2">Click here</a> to configure.';
			}
		}
		else
		{
			echo 'You have not configured your shortcuts. <a href="'.DOMAIN.'viewsettings/2">Click here</a> to configure.';
		}
		
	}
	public static function getBreadCrumbsData($baseUrlString = '')
	{		
		$pageUrl = explode("/",$_SERVER['REQUEST_URI']); 
		$pageName = $pageUrl[2];			
		
		if($pageName == '')
		{
			$breadCrumbsData .= '';
		}
		else if($pageName == 'dashboard')
		{
			$breadCrumbsData = '<div class="breadcrumbs">';	
			
			if($pageUrl[3] == 'viewsettings')
			$breadCrumbsData .= '<a href="'.$baseUrlString.'">Home</a> <span class="arrows">&rsaquo;</span> Settings';
			else if($pageUrl[3] == 'viewprofile')
			$breadCrumbsData .= '<a href="'.$baseUrlString.'">Home</a> <span class="arrows">&rsaquo;</span> Profile';
			else if($pageUrl[3] == 'changepassword')
			$breadCrumbsData .= '<a href="'.$baseUrlString.'">Home</a> <span class="arrows">&rsaquo;</span> Change Password';			
			
			$breadCrumbsData .='</div>';
		}
		else
		{	
			$breadCrumbsData = '<div class="breadcrumbs">';		
			$url = "/".$pageName;
			$breadCrumIds = self::getBreadCrumDetails($url);
			if(!empty($breadCrumIds))
			{
				$breadcrumstring = trim($breadCrumIds[0]['nav_ids'], ',');
				$breadcrumArr = explode(",",$breadcrumstring);
				for($i=0;$i<sizeof($breadcrumArr);$i++)
				{
				   $breadCrumNames[] = self::getBreadCrumNames($breadcrumArr[$i]);		  
				}
				$breadCrumbsData .= '<span class="firstbreadcrumb" onclick="window.location=\''.$baseUrlString.'\'">Home</span> <span class="arrows">&rsaquo;</span> ';			
						
				for($b = 0; $b < sizeof($breadCrumNames);$b++)
				{	
					if($b == 0)
					{
						$breadCrumbsData .= '<span>'.$breadCrumNames[$b][0]['menuName'].'</span> <span class="arrows">&rsaquo;</span> ';
					}
					else if($b == (sizeof($breadCrumNames) - 1))
					{
						if($pageUrl[3] == '')					
						$breadCrumbsData .= '<span>'.$breadCrumNames[$b][0]['menuName'].'</span>';
						else
						$breadCrumbsData .= '<a href="'.$baseUrlString.$breadCrumNames[$b][0]['url'].'" >'.$breadCrumNames[$b][0]['menuName'].'</a>';
					}
					else
					{
						$breadCrumbsData .= '<span>'.$breadCrumNames[$b][0]['menuName'].'</span> <span class="arrows">&rsaquo;</span> ';
					}			
				}	
				if($pageUrl[3] == 'add')	
				$breadCrumbsData .= '<span class="arrows">&rsaquo;</span> <span>Add</span>';
				else if($pageUrl[3] == 'edit')
				$breadCrumbsData .= '<span class="arrows">&rsaquo;</span> <span>Edit</span>';
				else if($pageUrl[3] == 'view')
				$breadCrumbsData .= '<span class="arrows">&rsaquo;</span> <span>View</span>';
				
				//$breadCrumbsData = rtrim($breadCrumbsData, " >> "); 
				$breadCrumbsData .='</div>';
			}
			else
			{
				$breadCrumbsData = '';
			}
		}
		
		echo $breadCrumbsData;
	}
	
	public static function getBreadCrumDetails($url)
	{	
		$selectQuery = "select mo.nav_ids FROM main_objmenu mo join main_menu mm on mo.menuId = mm.id
						where mo.menuId = mm.id and mm.url = '".$url."'";		
		try
		{
			$db = Zend_Db_Table::getDefaultAdapter();
			$sql = $db->query($selectQuery);
			$result = $sql->fetchAll();
		}
		catch(Exception $e){
			echo $e->getMessage();
		}		
	}
	
	public static function getBreadCrumNames($menuid)
	{	  
		$selectQuery = "select mm.menuName,mm.url FROM main_menu mm WHERE mm.id = ".$menuid;		
		try{
			$db = Zend_Db_Table::getDefaultAdapter();
			$sql=$db->query($selectQuery);
			return $result = $sql->fetchAll();
		}
		catch(Exception $e){
			echo $e->getMessage();
		}	
	}
	
	public static function getRecentlyViewedItems()
	{
		//The Logic used behind this functionality is we are using the object of zend session to store the action 
		$recentlyViewed = new Zend_Session_Namespace('recentlyViewed'); // Creating a new session with namespace
		if(!empty($recentlyViewed->recentlyViewedObject))
		{	
			echo '<div class="recentviewd"><label id="recentviewtext">Recently viewed</label><ul>';
			$rvSize = 0;
			if(sizeof($recentlyViewed->recentlyViewedObject) > 3)
				$rvSize = 3;
			else
				$rvSize = sizeof($recentlyViewed->recentlyViewedObject);
			//echo "<pre>";print_r($recentlyViewed->recentlyViewedObject);exit;	
			$menuName = '';												
			for($i=0;$i<$rvSize;$i++)
			{
				$pagesplit = explode("!@#",$recentlyViewed->recentlyViewedObject[$i]);
				$pagesplitName = $pagesplit[0]; 
				$pagesplitLink = $pagesplit[1];

				/* Instead of url - display menu name for each list item*/
				if($pagesplitName != 'dashboard' && $pagesplitName != 'welcome' && $pagesplitName != 'viewsettings')
				{
					$selectQuery1 = "select m.menuName from main_menu m where m.url = '/".$pagesplitName."'";
					$db = Zend_Db_Table::getDefaultAdapter();
					$sql=$db->query($selectQuery1);
					$resultarray = $sql->fetchAll();
					
					if(!empty($resultarray))
						$menuName = ucfirst($resultarray[0]['menuName']);
					else $menuName = ucfirst($pagesplitName);
				}
				else $menuName = ucfirst($pagesplitName);
				/* Display of add, edit or view in each list item */
				$urldata = explode("/",$pagesplitLink);
				if(isset($urldata[3]))
				{
					if($urldata[3] == 'add')
						$menuName .= '-Add';
					else if($urldata[3] == 'edit')
						$menuName .= '-Edit';
					else if($urldata[3] == 'view')
						$menuName .= '-View';
					else if($urldata[3] == 'viewsettings')
						$menuName = 'Settings';
					else if($urldata[3] == 'viewprofile')
						$menuName = 'Profile';
					else if($urldata[3] == 'changepassword')
						$menuName = 'Change password';
				}							 
				echo '<li><span id="redirectlink" onclick ="redirecttolink(\''.$pagesplitLink.'\');">'.$menuName.'</span><a href="javascript:void(0);" onClick="closetab(this,\''.$pagesplitName.'\',\''.$pagesplitLink.'\')"></a></li>';											
			}
		} 
		$tmpPageLink = explode("/",$_SERVER['REQUEST_URI']); 
		$pageName = $tmpPageLink[2];
		$pageLink = $_SERVER['REQUEST_URI'];
		$a= 'index';
		if(isset($recentlyViewed->recentlyViewedObject))
		{
			//if(sizeof($recentlyViewed->recentlyViewedObject) > 3 && $pageLink != '/hrms/' && !in_array($pageName."!@#".$pageLink, $recentlyViewed->recentlyViewedObject))
			if(sizeof($recentlyViewed->recentlyViewedObject) > 3 && $pageLink != DOMAIN && !in_array($pageName."!@#".$pageLink, $recentlyViewed->recentlyViewedObject))
			{
				array_shift($recentlyViewed->recentlyViewedObject);											
			}
			if($pageName != 'public')
			{
				if(!in_array($pageName."!@#".$pageLink, $recentlyViewed->recentlyViewedObject))
				{
					//if($pageLink != '/hrms/')
					if($pageLink != DOMAIN)
					{
						array_push($recentlyViewed->recentlyViewedObject,$pageName."!@#".$pageLink);
					}
				}  											
			}
		}
		else
		{
			$recentlyViewed->recentlyViewedObject = array();
			//if(!in_array($pageName."!@#".$pageLink, $recentlyViewed->recentlyViewedObject))
			//{
			//if($pageLink != '/hrms/' && !in_array($pageName."!@#".$pageLink, $recentlyViewed->recentlyViewedObject)) 
			if($pageLink != DOMAIN && !in_array($pageName."!@#".$pageLink, $recentlyViewed->recentlyViewedObject)) 
				array_push($recentlyViewed->recentlyViewedObject,$pageName."!@#".$pageLink);
			//}  
		}
		//echo '<pre>'; print_r($recentlyViewed->recentlyViewedObject); echo '</pre>';die;

		echo '</ul></div>';
	}
	
	
}
?>