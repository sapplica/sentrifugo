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
 * Login_Model_Users
 *
 * @author Enrico Zimuel (enrico@zimuel.it)
 */
class Services_Model_Holiday extends Zend_Db_Table_Abstract
{
	protected $_name = 'main_holidaygroups';
	protected $_primary = 'id';


	/**
	 * Check if a username is a Ldap user
	 *
	 * @param string $username
	 * @return boolean
	 */
	public function myholidaycalender($postarray)
    {
	  
	  if(isset($postarray['userid']) && $postarray['userid'] != '')
	  {
	    
	     $result = array();
	     $userid = $postarray['userid'];
		 $holidaygroupidArr = $this->getholidaygroup($userid);
			if(!empty($holidaygroupidArr) )
			{
			    $holidaygroupid = $holidaygroupidArr[0]['holiday_group'];
				$result =  $this->getHolidaydatesList($holidaygroupid);
			    if(!empty($result))
				{
							$data = array('status'=>'1',
							  'message'=>'Success',
							  'result' => $result);
				}else
                {
				            $data = array('status'=>'0','message'=>'Holiday dates has not been assigned to this group','result' => ''); 
				}		
			}
			else
			{
						$data = array('status'=>'0','message'=>'Holiday Group has not been assigned yet','result' => ''); 
			}
	     
	  }else if($postarray['userid'] == '')
	  {
	     $data = array('status'=>'0','message'=>'User Id cannot be empty.','result' => $result);
	  }
	  //echo "<pre>";print_r($data);exit;
	  return $data;
    }

   
    public function getholidaygroup($userid)
	{
	    $result= array();
		if ($userid !='')
		{
			$query = $this->select()
			->setIntegrityCheck(false)
			->from(array('e'=>'main_employees'),array('e.id','e.holiday_group'))
			->where("e.user_id = '".$userid."' and e.isactive = 1");
				
			$result = $this->fetchAll($query)->toArray();
			
		}
		return $result;

	}	
	
	public function getHolidaydatesList($groupid)
	{
	    $result= array();
		if ($groupid !='')
		{
			 $select = $this->select()
						->setIntegrityCheck(false)
						->from(array('h'=>'main_holidaydates'),array('h.holidayname','h.holidaydate','h.holidayyear'))
					    ->where('h.groupid = '.$groupid.' AND h.isactive = 1');
	
			 $result = $this->fetchAll($select)->toArray();
		}
		
		return $result;
	}	
	

}