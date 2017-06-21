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

require_once 'Zend/Db/Table/Abstract.php';
class Default_Model_Cities extends Zend_Db_Table_Abstract
{
    protected $_name = 'main_cities';
    protected $_primary = 'id';
	
	public function getCitiesData($sort, $by, $pageNo, $perPage,$searchQuery)
	{
		$where = "c.isactive = 1 AND ct.is_active=1 AND s.isactive=1";
		
		if($searchQuery)
			$where .= " AND ".$searchQuery;
		$db = Zend_Db_Table::getDefaultAdapter();		
		
		$CitiesData = $this->select()
    					   ->setIntegrityCheck(false)	
                           ->from(array('c'=>'main_cities'),array('c.*'))
						   ->joinLeft(array('ct'=>'tbl_countries'), 'ct.id=c.countryid',array('country_name'=>'ct.country_name'))						   						   
                           ->joinLeft(array('s'=>'tbl_states'), 's.id=c.state',array('state_name'=>'s.state_name'))						   						   
						   ->where($where)
    					   ->order("$by $sort") 
    					   ->limitPage($pageNo, $perPage);
		
		return $CitiesData;       		
	}
	
	public function getGrid($sort,$by,$perPage,$pageNo,$searchData,$call,$dashboardcall,$a='',$b='',$c='',$d='')
	{		
        $searchQuery = '';
        $searchArray = array();
        $data = array();
		
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
		$objName = 'cities';
		
		$tableFields = array('action'=>'Action','country_name' =>'Country','state_name' =>'State','city' => 'City');
		
		$tablecontent = $this->getCitiesData($sort, $by, $pageNo, $perPage,$searchQuery);     
		
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
			'searchArray' => $searchArray,
			'call'=>$call,
			'dashboardcall'=>$dashboardcall
		);	
		return $dataTmp;
	}
	
	
	public function getsingleCitiesData($id,$tbl='')
	{
		if($tbl == 'main'){
			$row = $this->fetchRow("city_org_id = '".$id."' AND isactive = 1");
		}else{
			$row = $this->fetchRow("id = '".$id."' AND isactive = 1");
		}
		if (!$row) {
			throw new Exception("Could not find row $id");
		}
		return $row->toArray();
	}
	
	public function getDuplicateCityName($othercityname,$stateid)
	{
	   $db = Zend_Db_Table::getDefaultAdapter();
		
		$rows = $db->query("select count(*) as count from tbl_cities where lower(city_name) = lower('".$othercityname."') and state_id = ".$stateid." ");
		
		return $rows->fetchAll();
	
	}
	
	public function getCitiesDataByID($id,$tbl='')
	{
		 $select = $this->select()
						->setIntegrityCheck(false)
						->from(array('c'=>'main_cities'),array('c.*'))
					    ->where('c.isactive = 1 AND c.id='.$id.' ');
		return $this->fetchAll($select)->toArray();
	}
	
	public function SaveorUpdateCitiesData($countryid,$stateid, $cityname,$cityid,$loginUserId)
	{
		$date= gmdate("Y-m-d H:i:s");
		$db = Zend_Db_Table::getDefaultAdapter();

	 	$rows = $db->query('INSERT INTO `main_cities` (countryid,state,city,city_org_id,createdby,modifiedby,createddate,modifieddate,isactive) VALUES ('.$countryid.','.$stateid.',(SELECT `city_name` FROM tbl_cities WHERE id = '.$cityid.'),'.$cityid.','.$loginUserId.','.$loginUserId.',"'.$date.'","'.$date.'",1) ON DUPLICATE KEY UPDATE city=(SELECT `city_name` FROM tbl_cities WHERE id = '.$cityid.'),modifiedby='.$loginUserId.',modifieddate="'.$date.'",isactive=1 ');
		
		$id=$this->getAdapter()->lastInsertId('main_states');
		return $id;
		
	
	}
	
	public function SaveMainCityData($stateid,$othercityname)
	{
	    $date= gmdate("Y-m-d H:i:s");
	    $db = Zend_Db_Table::getDefaultAdapter();
	 	$rows = $db->query("INSERT INTO `tbl_cities` (state_id,city_name,is_active,created,modified) VALUES (".$stateid.",'".$othercityname."',1,'".$date."','".$date."') ");		
		
		$id=$this->getAdapter()->lastInsertId('tbl_cities');
		return $id;
	}
	
	public function deleteCityData($data,$where)
	{
	    if($where != ''){
			$this->update($data, $where);
			return 'update';
		} else {
			$this->insert($data);
			$id=$this->getAdapter()->lastInsertId('main_states');
			return $id;
		}
	
	}
	
	
		
	public function getCitiesList($stateid,$condition = '')
	{
		if($condition == 'addcity')
		{
			$select = $this->select()
						->setIntegrityCheck(false)
						->from(array('c'=>'tbl_cities'),array('c.id','c.city_name'))
						->joinLeft(array('m'=>'main_cities'), "m.city_org_id = c.id and m.isactive = 1",array())
						->where('c.state_id='.$stateid.' AND c.is_active=1 AND m.city_org_id is null')
						->order('c.city_name');
		}
	    else
		{
			$select = $this->select()
						->setIntegrityCheck(false)
						->from(array('c'=>'tbl_cities'),array('c.id','c.city_name'))
						->where('c.state_id='.$stateid.' AND c.is_active=1 ')
						->order('c.city_name');
		}
		
		return $this->fetchAll($select)->toArray();
    } 
	
	public function getUniqueCitiesList($state_id,$cityids)
	{
	    $where = '';
	    if($cityids !='')
		{
		  $where = "and c.id NOT IN(".$cityids.") ";
		}
	    $db = Zend_Db_Table::getDefaultAdapter();
        
        $query = "select c.id,c.city_name from tbl_cities c where c.is_active = 1 and c.state_id=".$state_id." $where ORDER BY c.city_name ";
        $result = $db->query($query)->fetchAll();
	    return $result;
		
	}

    public function getCityName($cityid)
	{
	     $select = $this->select()
						->setIntegrityCheck(false)
						->from(array('c'=>'tbl_cities'),array('cityname'=>'c.city_name','c.id'))
						->where('c.id='.$cityid.' AND c.is_active=1 ');
						
		
		return $this->fetchAll($select)->toArray();
	
	}	
	
	public function getTotalCitiesList()
	{
	    $select = $this->select()
						->setIntegrityCheck(false)
						->from(array('c'=>'tbl_cities'),array('c.id','c.city_name'));
					
		return $this->fetchAll($select)->toArray();
	
	}
	
	public function getBasicCitiesList($stateid)
	{
		$select = $this->select()
						->setIntegrityCheck(false)
						->from(array('c'=>'main_cities'),array('c.*'))
						->where('c.state='.$stateid.' AND c.isactive=1 ')
						->order('c.city');
		
		return $this->fetchAll($select)->toArray();
	}
	
	public function getCitiesNameData($city_org_id)
	{
	    $select = $this->select()
						->setIntegrityCheck(false)
						->from(array('c'=>'main_cities'),array('c.id','c.city_org_id','c.city'))
						->where('c.city_org_id='.$city_org_id.' AND c.isactive=1 ');
		
		return $this->fetchAll($select)->toArray();
    } 
	
	public function getCityNamesByIds($cityIdArray)
	{
		$resultstring = implode(',', $cityIdArray);
		$cityArray = array();
		if($resultstring)
		{
			try
			{
				$qry = "select c.city_org_id, c.city from main_cities c
                                        where c.city_org_id IN (".$resultstring.")";
				$db = Zend_Db_Table::getDefaultAdapter();
				$sqlRes = $db->query($qry);
				$cityRes = $sqlRes->fetchAll();

				if(!empty($cityRes))
				{
					foreach($cityRes as $city)
					{
						$cityArray[$city['city_org_id']]= $city['city'];
					}
				}
			}
			catch(Exception $e)
			{
				echo "Error Encountered - ".$e->getMessage();
			}
		}
		return $cityArray;
	}
}