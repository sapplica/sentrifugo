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
class Default_Model_Countries extends Zend_Db_Table_Abstract
{
    protected $_name = 'main_countries';
    protected $_primary = 'id';
	
	public function getCountriesData($sort, $by, $pageNo, $perPage,$searchQuery)
	{
		$where = "isactive = 1";
		
		if($searchQuery)
			$where .= " AND ".$searchQuery;
		$db = Zend_Db_Table::getDefaultAdapter();		
		
		$countriesData = $this->select()
    					   ->setIntegrityCheck(false)	    					
						   ->where($where)
    					   ->order("$by $sort") 
    					   ->limitPage($pageNo, $perPage);
		
		return $countriesData;       		
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
		$objName = 'countries';
		
		$tableFields = array('action'=>'Action','country' => 'Country','countrycode' =>'Country Code');
		
		$tablecontent = $this->getCountriesData($sort, $by, $pageNo, $perPage,$searchQuery);     
		
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
	public function getsingleCountriesData($id,$tbl='')
	{
		
		if($tbl == 'main'){
			$row = $this->fetchRow("country_id_org = '".$id."' AND isactive = 1");
		}else {
			$row = $this->fetchRow("id = '".$id."' AND isactive = 1");
		}
		if (!$row) {
			throw new Exception("Could not find row $id");
		}
		return $row->toArray();
	}
	
	public function getCountriesDataByID($id)
	{
	    $select = $this->select()
						->setIntegrityCheck(false)
						->from(array('c'=>'main_countries'),array('c.*'))
					    ->where('c.isactive = 1 AND c.id='.$id.' ');
		return $this->fetchAll($select)->toArray();
	
	}
	
	public function SaveorUpdateCountryData($data, $where)
	{
	    if($where != ''){
			$this->update($data, $where);
			return 'update';
		} else {
			$this->insert($data);
			$id=$this->getAdapter()->lastInsertId('main_countries');
			return $id;
		}
		
	
	}
	
	public function getDuplicateCountryName($othercountry)
	{
	   $db = Zend_Db_Table::getDefaultAdapter();
		
		$rows = $db->query("select count(*) as count from tbl_countries where lower(country_name) = lower('".$othercountry."')");
		
		return $rows->fetchAll();
	
	}
	
	public function getDuplicateCountryOriginalId($othercountry)
	{
	   $db = Zend_Db_Table::getDefaultAdapter();
		
		$rows = $db->query("select count(*) as count from main_countries where country_id_org = '".$othercountry."'");
		
		return $rows->fetchAll();
	
	}
	
	public function SaveMainCountryData($othercountry,$countrycode)
	{
		$date= gmdate("Y-m-d H:i:s");
	    $db = Zend_Db_Table::getDefaultAdapter();
	 	$rows = $db->query("INSERT INTO `tbl_countries` (country_name,country_code,country_code2,is_active,created,modified) VALUES ('".$othercountry."','".$countrycode."','',1,'".$date."','".$date."') ");		
		
		$id=$this->getAdapter()->lastInsertId('tbl_countries');
		return $id;
	}
	public function getCountriesList()
	{
	     $db = Zend_Db_Table::getDefaultAdapter();
		
		$rows = $db->query("select c.id,c.country_name,c.country_code from tbl_countries c WHERE c.id NOT IN (SELECT mc.country_id_org from main_countries mc where mc.isactive=1);");
		
		return $rows->fetchAll();
	
	}
	
	public function getTotalCountriesList($condition = '')
	{
		if($condition == 'addcountry')
		{
			$select = $this->select()
                            ->setIntegrityCheck(false)
                            ->from(array('c'=>'tbl_countries'),array('c.*'))
							->joinLeft(array('m'=>'main_countries'), "m.country_id_org = c.id and m.isactive = 1",array())
							->order('c.country_name');	
		}
		else
		{
			$select = $this->select()
                            ->setIntegrityCheck(false)
                            ->from(array('c'=>'tbl_countries'),array('c.*'))
							->order('c.country_name');			
		}
		
		return $this->fetchAll($select)->toArray();		
	}
	
	public function getDuplicateCountryData($country)
	{
	   
	    $select = $this->select()
						->setIntegrityCheck(false)
						->from(array('mc'=>'main_countries'),array('count'=>'count(mc.id)'))
					    ->where('mc.country="'.$country.'" AND mc.isactive = 1');
		return $this->fetchAll($select)->toArray();
	
	}
	
	public function getCountryCode($countryid)
	{
	     $select = $this->select()
						->setIntegrityCheck(false)
						->from(array('c'=>'tbl_countries'),array('c.id','c.country_code','c.country_name'))
					    ->where('c.id="'.$countryid.'" AND c.is_active = 1');
		return $this->fetchAll($select)->toArray();
	
	
	}
	
	public function getCountryOrgID($id)
	{
	    $select = $this->select()
						->setIntegrityCheck(false)
						->from(array('c'=>'main_countries'),array('c.country_id_org'))
					    ->where('c.id="'.$id.'" AND c.isactive = 1');
		
		return $this->fetchAll($select)->toArray();
	
	}
	
	public function getActiveCountriesList()
	{
	    $select = $this->select()
						->setIntegrityCheck(false)
						->from(array('c'=>'main_countries'),array('c.id','c.country','c.country_id_org'))
					    ->where('c.isactive = 1')
						->order('c.country');
		
		return $this->fetchAll($select)->toArray();
	
	}
	
	public function UpdateMainCountryData($id)
	{
	 $db = Zend_Db_Table::getDefaultAdapter();
		
		$rows = $db->query("UPDATE tbl_countries SET is_active=0 WHERE id=".$id." ");
		
		
		
	}
	
	public function getActiveCountryName($countryid)
	{
	    $select = $this->select()
						->setIntegrityCheck(false)
						->from(array('c'=>'main_countries'),array('c.id','c.country','c.country_id_org'))
					    ->where('c.isactive = 1 AND c.country_id_org = '.$countryid.' ')
						->order('c.country');
		
		return $this->fetchAll($select)->toArray();
	
	}
	
	
    /**
     * This function is used to build dropdown of countries.
     * 
     * @return Array  Array of country options 
     */    
    public function getCountriesOptions()
    {                
        $db = Zend_Db_Table::getDefaultAdapter();
        $qry = "select id,country,country_id_org from ".$this->_name." where isactive = 1";
        $result = $db->query($qry);
        $options = array();
        while($row = $result->fetch())
        {
            
            $options[$row['country_id_org']] = $row['country'];
        }
        return $options;
    }
   
	public function getCountryNamesByIds($countryIdArray)
	{
		$resultstring = implode(',', $countryIdArray);
		$countryArray = array();
		if($resultstring)
		{
			try
			{
				$qry = "select c.country_id_org, c.country from main_countries c
                                        where c.country_id_org IN (".$resultstring.") ";		
				$db = Zend_Db_Table::getDefaultAdapter();
				$sqlRes = $db->query($qry);
				$countryRes = $sqlRes->fetchAll();

				if(!empty($countryRes))
				{
					foreach($countryRes as $country)
					{
						$countryArray[$country['country_id_org']]= $country['country'];
					}
				}
			}
			catch(Exception $e)
			{
				echo "Error Encountered - ".$e->getMessage();
			}
		}
		return $countryArray;	
	}
   
}