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

/**
 * Shortcuticons View Helper
 *
 * A View Helper that creates the menu
 *
 *
 */

class Zend_View_Helper_Shortcuticons extends Zend_View_Helper_Abstract {

	public  function shortcuticons($userId)
	{
		$role_id ="";$privilegedmenuIdsArr=array();$privilegedmenuIdsCsv="";
		
		$settingsModel = new Default_Model_Settings();
		$session=new Zend_Auth_Storage_Session();
		$data=$session->read();
		//echo "Session Data : <pre>";print_r($data);die;
		if(!empty($data))	$role_id = $data['emprole'];
		
		$iconidcount = $settingsModel->getActiveiconCount($userId);
				
		$shortcutsStr = '<div><ul>';
		if($iconidcount[0]['count'] > 0)
		{
			$menuIdsArr = $settingsModel->getMenuIds($userId,2);
			
			if(!empty($menuIdsArr) === true)
			{
				$menuIdsStr = $menuIdsArr[0]['menuid'];
				/*
					Modified By:	Yamini
					Purpose:	Checking the privileges for shortcut icons for logged in role...
					Modified Date:	26/09/2013.
					
				*/
				$idCsv=1;	//Flag 
				
				$privilege_model = new Default_Model_Privileges();
				$privilegesofObj = $privilege_model->getObjPrivileges($menuIdsStr,"",$role_id,$idCsv);
				$menuwithaddprivilegeArr = array(SITEPREFERENCE,LEAVEREQUEST,IDENTITYCODES,IDENTITYDOCUMENTS);
				/* This condition is to check whether the menu is active. If active then only the shortcut will be displayed.*/
				if(!empty($privilegesofObj) && isset($privilegesofObj))
				{
					for($i=0;$i<sizeof($privilegesofObj);$i++)
					{
						//if($privilegesofObj[$i]['viewpermission'] == "Yes" || (in_array($privilegesofObj[$i]['object'],$menuwithaddprivilegeArr) && $privilegesofObj[$i]['addpermission'] == "Yes" ))
						if($privilegesofObj[$i]['isactive'] == 1)
						 array_push($privilegedmenuIdsArr,$privilegesofObj[$i]['object']);
					}
					$privilegedmenuIdsCsv= implode(",",$privilegedmenuIdsArr);
				}
				if($privilegedmenuIdsCsv != "")	$menuIdsStr = $privilegedmenuIdsCsv;
				
				$menuDetailsArr = $settingsModel->getMenuName($menuIdsStr,1);
				$betaFlag = 0;
				if(!empty($menuDetailsArr))
				{
					for($s = 0; $s < sizeof($menuDetailsArr); $s++)
					{
						$menuName = $menuDetailsArr[$s]['menuName'];
						$tmpMenuUrl = $menuDetailsArr[$s]['url'];

						//echo $s." >> ".(strpos($tmpMenuUrl,'http://') === false)."<br/>";

						if($tmpMenuUrl && (strpos($tmpMenuUrl,'http://') === false || strpos($tmpMenuUrl,'http://') === false  || strpos($tmpMenuUrl,'http://') === false))
						{
							$menuUrl = BASE_URL.substr($tmpMenuUrl,1,strlen($tmpMenuUrl));
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
						$shortcutsStr.='<li><a href="'.$menuUrl.'" title="'.$menuName.'" ><img src="'.MEDIA_PATH.''.$menuIcon.'"  onerror=this.src="'.MEDIA_PATH.'images/sampleimg.png"; height="33" width="33" border="0" /></a></li>';

						if($menuDetailsArr[$s]['id'] > 148 && $menuDetailsArr[$s]['id'] < 175)
							$betaFlag = 1;
					}										

					echo $shortcutsStr.='</ul></div>';
					if($betaFlag == 1)
						echo '<div class="beta_info" title="beta version">Beta</div>';
				}
				else
				{
					echo "No active shortcut icons. ".'<a href="'.BASE_URL.'viewsettings/2">'."Click here".'</a> '."to configure.";
				}
			}
			else
			{
				echo "You have not configured your shortcut icons. ".'<a href="'.BASE_URL.'viewsettings/2">'."Click here".'</a> '."to configure.";
			}
		}
		else
		{
			echo "You have not configured your shortcut icons. ".' <a href="'.BASE_URL.'viewsettings/2">'."Click here".'</a> '."to configure";
		}
		
	}
}
?>