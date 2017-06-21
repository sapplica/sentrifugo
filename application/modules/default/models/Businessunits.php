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

class Default_Model_Businessunits extends Zend_Db_Table_Abstract
{
    protected $_name = 'main_businessunits';
    protected $_primary = 'id';
	
	public function getBusinessUnitsData($sort, $by, $pageNo, $perPage,$searchQuery)
	{
		$where = " b.isactive = 1 AND b.id <> 0 ";
		if($searchQuery)
			$where .= " AND ".$searchQuery;
		$db = Zend_Db_Table::getDefaultAdapter();		
		
		$businessunitsdata = $this->select()
    					   ->setIntegrityCheck(false)	 
						   ->from(array('b' => 'main_businessunits'),array('id'=>'distinct(b.id)','isactive'=>'b.isactive','unitcode'=>'b.unitcode','address1'=>'b.address1','unitname'=>'b.unitname','unithead'=>'b.unithead','startdate'=>'DATE_FORMAT(b.startdate,"'.DATEFORMAT_MYSQL.'")'))
						   ->joinLeft(array('c'=>'main_cities'),'b.city=c.city_org_id',array('city'=>'c.city'))
						   ->joinLeft(array('s'=>'main_states'),' s.state_id_org = b.state',array('state'=>'s.state'))
						   ->joinLeft(array('cn'=>'main_countries'),' cn.country_id_org = b.country',array('country'=>'cn.country'))
						   ->joinLeft(array('tz'=>'main_timezone'), 'b.timezone=tz.id and tz.isactive=1', array('timezone' => 'concat(tz.timezone," [",tz.timezone_abbr,"]")'))
						   ->where($where)
    					   ->order("$by $sort") 
    					   ->limitPage($pageNo, $perPage);
		
		return $businessunitsdata;       		
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
				$searchAlias = ' ';
				if($key == 'city')
					$searchAlias = " c.";
				if($key == 'state')
					$searchAlias = " s.";
				if($key == 'country')
					$searchAlias = " cn.";
				if($key == 'timezone')
					$searchAlias = " tz.";
				if($key == 'startdate')
				{
					$searchQuery .= " ".$key." like '%".  sapp_Global::change_date(urldecode($val),'database')."%' AND ";
				}
				else
				$searchQuery .= $searchAlias.$key." like '%".urldecode($val)."%' AND ";
				$searchArray[$key] = urldecode($val);
			}
			$searchQuery = rtrim($searchQuery," AND");					
		}
		$objName = 'businessunits';
		$tableFields = array('action'=>'Action','unitname' =>'Name','unitcode' => 'Code','startdate'=>'Started On','address1'=>'Street Address','city'=>'City','state'=>'State','country'=>'Country','timezone'=>'Time zone');
		$tablecontent = $this->getBusinessUnitsData($sort, $by, $pageNo, $perPage,$searchQuery);     
		
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
			'dashboardcall'=>$dashboardcall,
			'search_filters' => array(
					'startdate' =>array('type'=>'datepicker')					
				)
				);			
		return $dataTmp;
	}
	
	public function getSingleUnitData($id)
	{
		
		$db = Zend_Db_Table::getDefaultAdapter();
		$query = "select * from main_businessunits where id = ".$id." AND isactive = 1";
		$result = $db->query($query)->fetch();
		return $result;
	}
	
	public function SaveorUpdateBusinessUnits($data, $where)
	{
		if($where != ''){
			$this->update($data, $where);
			return 'update';
		} else {
			$this->insert($data);
			$id=$this->getAdapter()->lastInsertId('main_businessunits');
			return $id;
		}
	}
	
        /**
         * This function is used to get business unit list.
         * @param integer $page_no   = page number.
         * @param integer $per_page  = no.of record per page.
         * @return array Array of business units
         */
        public function getunitlist_service($page_no,$per_page)
        {
            $db = Zend_Db_Table::getDefaultAdapter();
            $query_cnt = "select count(*) cnt from main_businessunits where isactive = 1 and id > 0";
            $result_cnt = $db->query($query_cnt);
            $row_cnt = $result_cnt->fetch();
            $total_cnt = $row_cnt['cnt'];
            
            $offset = ($per_page*$page_no) - $per_page;
            $limit_str = " limit ".$per_page." offset ".$offset;
            $page_cnt = ceil($total_cnt/$per_page);
            
            $query = "select b.id,b.unitname,b.unitcode,b.address1,c.country,s.state,ci.city 
                        from main_businessunits b 
                        left join main_countries c on c.country_id_org = b.country 
                        left join main_states s on s.state_id_org = b.state 
                        left join main_cities ci on b.city = ci.city_org_id 
                        where b.isactive = 1 and b.id > 0 order by b.unitname asc ".$limit_str;
            $result = $db->query($query);
            $data = $result->fetchAll();
            
            return array('rows' => $data,'page_cnt' => $page_cnt);            
        }
	public function getDeparmentList()
	{
	
	  $select = $this->select()
						->setIntegrityCheck(false)
						->from(array('b'=>'main_businessunits'),array('b.id','b.unitname'))
					    ->where('b.isactive = 1')
						->order('b.unitname');
		return $this->fetchAll($select)->toArray();
	
	}
        /**
         * This function gives array of business units and its id's for drop down list.
         * @return Array  
         */
	public function getBusinessUnitsList()
        {
            $data = $this->fetchAll("isactive = 1","unitname")->toArray();
            $options_arr = array();
            foreach($data as $option)
            {
                $options_arr[$option['id']] = $option['unitname'];
            }
			
            return $options_arr;
        }
	public function checkUnitCodeDuplicates($code,$id)
	{
		if($id){
			$row = $this->fetchRow("unitcode = '".$code."' AND id <> ".$id.' AND  isactive=1'); 
		}else{
			$row = $this->fetchRow("unitcode = '".$code."' AND isactive=1");
		}	
		
		if(!$row){
			return false;
		}else{
			return true;
		}
	}
	
	public function getParicularBusinessUnit($id)
	{
	  $select = $this->select()
						->setIntegrityCheck(false)
						->from(array('b'=>'main_businessunits'),array('b.id','b.unitname'))
					    ->where('b.id = '.$id.' AND b.isactive = 1');
	
		return $this->fetchAll($select)->toArray();	
	}
	
	public function checkdeptstobusinessunits($businessunitid)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$query = "select count(*) as count from main_departments where unitid = ".$businessunitid." AND isactive = 1";
		$result = $db->query($query)->fetch();
	    return $result['count'];
	}
	
	public function getBusinessunitNamesByIds($businessunitArray)
	{
		$resultstring = implode(',', $businessunitArray);
		$BUArray = array();
		if($resultstring)
		{
			try
			{
				$qry = "select b.id, if(b.id = 0,'',b.unitname) as unitname from main_businessunits b
                                        where b.id IN (".$resultstring.")";
				$db = Zend_Db_Table::getDefaultAdapter();
				$sqlRes = $db->query($qry);
				$buRes = $sqlRes->fetchAll();

				if(!empty($buRes))
				{
					foreach($buRes as $bu)
					{
						$BUArray[$bu['id']]= $bu['unitname'];
					}
				}
			}
			catch(Exception $e)
			{
				echo "Error Encountered - ".$e->getMessage();
			}
		}
		return $BUArray;
	}
	
	public function getDeptForBusinessUnit($bid,$pageNo,$perPage)
	{
		if($pageNo != 0)
		$limitpage = (($pageNo-1)*$perPage);
		else
		$limitpage = 0;
		
		$db = Zend_Db_Table::getDefaultAdapter();
		$query = "select d.*,c.country as ccountry,s.state as sstate,ct.city as ccity,if(d.isactive = 1,'Active','Inactive') as status,DATE_FORMAT(d.startdate,'".DATEFORMAT_MYSQL."') as startdate from main_departments d 
					left join main_countries c on c.country_id_org = d.country
					left join main_states s on s.state_id_org = d.state
					left join main_cities ct on ct.city_org_id = d.city
					where unitid = ".$bid." GROUP BY d.id LIMIT ".$limitpage.", ".$perPage;
		$result = $db->query($query)->fetchAll();
	    return $result;
	}
	
        public function getBU_report()
        {
            $db = Zend_Db_Table::getDefaultAdapter();
            $query = "select id,concat(unitname,' (',unitcode,')') bu_name from main_businessunits where isactive = 1";
            $result = $db->query($query)->fetchAll();
            return $result;
        }
	public function getDeptCountForBusinessUnit($bid)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$query = "select count(*) as rowcount from main_departments d 
					left join main_countries c on c.country_id_org = d.country
					left join main_states s on s.state_id_org = d.state
					left join main_cities ct on ct.city_org_id = d.city
					where unitid = ".$bid." GROUP BY d.id ";
		$result = $db->query($query)->fetchAll();
	    return count($result);
	}
	
	public function getBusinessUnits($buids)
	{
				$qry = "select b.id, b.unitname from main_businessunits b
                                        where b.id IN (".$buids.")";
				$db = Zend_Db_Table::getDefaultAdapter();
				$sqlRes = $db->query($qry);
				$buRes = $sqlRes->fetchAll();

				
		return $buRes;
	}
	public function checkDuplicateUnitName($unitName)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$qry = "select count(*) as count from main_businessunits b where b.unitname='".$unitName."' AND b.isactive=1 ";
		$res = $db->query($qry)->fetchAll();
		return $res;
	}
}