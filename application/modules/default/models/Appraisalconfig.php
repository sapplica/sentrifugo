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

class Default_Model_Appraisalconfig extends Zend_Db_Table_Abstract
{
    protected $_name = 'main_pa_implementation';
    protected $_primary = 'id';
    protected $_db = '';
	
    public function __construct($config = array()) {
    	$this->_db = Zend_Db_Table::getDefaultAdapter();	
    }
    
    public function getconfig_bu($businessunit_id)
    {
        if($businessunit_id != '')
        {
            $db = Zend_Db_Table::getDefaultAdapter();
            $query = "select * from main_pa_implementation "
                    . "where isactive = 1 and businessunit_id = '".$businessunit_id."' ";
            $result = $db->query($query)->fetchAll();
            if(count($result) > 0)
                return $result[0];            
        }
        return array();
    }
    public function check_act_init($id)
    {
        if($id != '')
        {
            $db = Zend_Db_Table::getDefaultAdapter();
            $query = "select count(id) cnt from main_pa_initialization "
                    . "where status = 1 and pa_configured_id = '".$id."' and isactive = 1";
            $result = $db->query($query)->fetch();
            return $result['cnt'];
        }
    }
    public function getAppraisalconfigData($sort, $by, $pageNo, $perPage,$searchQuery)
    {
        $auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity())
        {
            $loginUserId = $auth->getStorage()->read()->id;
            $businessunit_id = $auth->getStorage()->read()->businessunit_id;
            $department_id = $auth->getStorage()->read()->department_id; 
            $loginuserRole = $auth->getStorage()->read()->emprole;
            $loginuserGroup = $auth->getStorage()->read()->group_id;
        }
        $bu_str = "";
        if($loginuserGroup == HR_GROUP )
            $bu_str = " and c.businessunit_id = ".$businessunit_id." ";
        
        $where = " c.isactive=1 AND b.isactive=1 ";
        if($bu_str != '')
            $where .= $bu_str;
        if($searchQuery)
            $where .= " AND ".$searchQuery." ";
        $db = Zend_Db_Table::getDefaultAdapter();
		/** START
		** query changed on 10-04-2015
		** modified by soujanya
		
		$appraisalConfigData = $this->select()
    					   ->setIntegrityCheck(false)	
                           ->from(array('c'=>'main_pa_implementation'),array('c.*','performance_app_flag'=>'if (c.performance_app_flag=1,"Business Unit wise","Department Wise")','approval_selection'=>'if (c.approval_selection=1,"HR","Manager")','appraisal_ratings'=>'if (c.appraisal_ratings=1,"1-5","1-10")'))
                           ->joinLeft(array('b'=>'main_businessunits'), 'c.businessunit_id = b.id', array('deptname'=>'ifnull(d.deptname,"")'))
                           ->joinLeft(array('d'=>'main_departments'), 'c.department_id = d.id', array('b.unitname'))
                           ->joinLeft(array('e'=>'main_pa_initialization'), 'c.id = e.pa_configured_id', array(
                           new Zend_Db_Expr(" case 	when e.initialize_status = 2 then 'Initialize later' 
     												when e.pa_configured_id is null then 'Not configured yet' 
     												when e.initialize_status = 1 then 
												    case when e.enable_step = 1 then 'Enabled to Managers' 
											             when e.enable_step = 2 then 'Enabled to Employees' end 
											        when (e.pa_configured_id is not null and e.initialize_status is null) then 'In progress' end as initialize_status,
													case when e.status = 1 then 'Open' 
														when e.status = 2 then 'Close' 
														when e.status = 3 then 'Force Close' end as status 
											        
											        "),))                           
                           	->where($where)
    					   ->order("$by $sort") 
    					   ->limitPage($pageNo, $perPage);
		** END 

		
SELECT id, 
`e`.`pa_configured_id`, `e`.`initialize_status`, `e`.`status`, `e`.`enable_step` FROM `main_pa_initialization` AS `e` 
WHERE (e.isactive = 1) and id IN (
select max(id) FROM `main_pa_initialization` GROUP BY `pa_configured_id`
)
		**/
		
		$subSelectQuery = $this->select()
							->setIntegrityCheck(false)	
							->from(array('e'=>'main_pa_initialization'),array('max(e.id)'))
							->where('e.isactive = 1')
							->group('e.pa_configured_id');
		
		$subSelectWhere = 'e.id IN ('.$subSelectQuery.')';
		$tmpQuery = $this->select()
						->setIntegrityCheck(false)	->from(array('e'=>'main_pa_initialization'),array('id','e.pa_configured_id','e.initialize_status','e.status','e.enable_step'))
						->where($subSelectWhere);
		
		 $appraisalConfigData = $this->select()
    					   ->setIntegrityCheck(false)	
                           ->from(array('c'=>'main_pa_implementation'),array('c.*','performance_app_flag'=>'if (c.performance_app_flag=1,"Business Unit wise","Department Wise")','approval_selection'=>'if (c.approval_selection=1,"HR","Manager")','appraisal_ratings'=>'if (c.appraisal_ratings=1,"1-5","1-10")'))
                           ->joinLeft(array('b'=>'main_businessunits'), 'c.businessunit_id = b.id', array('deptname'=>'ifnull(d.deptname,"")'))
                           ->joinLeft(array('d'=>'main_departments'), 'c.department_id = d.id', array('b.unitname'))
                           ->joinLeft(array('e'=> $tmpQuery), 'c.id = e.pa_configured_id', array(
                           new Zend_Db_Expr(" case when e.status = 1 then 'Open' 
														when e.status = 2 then 'Closed' 
														when e.status = 3 then 'Force Closed' end as status, 
											        case 	when e.initialize_status = 2 then 'Initialize later' 
     												when e.pa_configured_id is null then 'Not configured yet' 
     												when e.initialize_status = 1 then 
												    case when e.enable_step = 1 then 'Enabled to Managers' 
											             when e.enable_step = 2 then 'Enabled to Employees' end 
											        when (e.pa_configured_id is not null and e.initialize_status is null) then 'In progress' end as initialize_status
													
											        "),))                           
                           	->where($where)
    					   ->order("$by $sort") 
    					   ->limitPage($pageNo, $perPage);
		
		return $appraisalConfigData;
   		
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
					 if($key == 'unitname')
					 	$searchQuery .= " b.".$key." like '%".$val."%' AND ";
					 else if($key == 'deptname')
					 	$searchQuery .= " d.".$key." like '%".$val."%' AND ";	
					 else if($key == 'status')	
						$searchQuery .= " e.".$key." like '%".$val."%' AND ";
						else 
						$searchQuery .= " c.".$key." like '%".$val."%' AND ";
                           $searchArray[$key] = $val;
				}
				$searchQuery = rtrim($searchQuery," AND");					
			}
			
		$objName = 'appraisalconfig';
		
		$tableFields = array('action'=>'Action','unitname'=>'Business Unit','deptname' => 'Department',
                    'performance_app_flag'=>'Applicability','appraisal_mode'=>'Appraisal Mode','appraisal_ratings'=>'Ratings','status'=>'Appraisal Status','initialize_status'=>'Process Status');
		
		$tablecontent = $this->getAppraisalconfigData($sort, $by, $pageNo, $perPage,$searchQuery);     
		
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
			'add' =>'add',
			'call'=>$call,
			'dashboardcall'=>$dashboardcall,
                        'search_filters' => array(
                            'performance_app_flag' => array(
                                'type' => 'select',
                                'filter_data' => array('' => 'All',1 => 'Business Unit wise', 0 => 'Department wise'),
                            ),
                            'appraisal_mode' => array(
                                'type' => 'select',
                                'filter_data' => array('' => 'All','Quarterly'=>'Quarterly', 'Half-yearly'=>'Half-yearly','Yearly'=>'Yearly'),
                            ),
                            'approval_selection' => array(
                                'type' => 'select',
                                'filter_data' => array('' => 'All',1 => 'HR', 2 => 'Manager')
                            ),
                            'appraisal_ratings' => array(
                                'type' => 'select',
                                'filter_data' => array('' => 'All',1 => '1-5', 2 => '1-10')
                            ),
                            'status' => array(
                                'type' => 'select',
                                'filter_data' => array('' => 'All',1 => 'Open', 2 => 'Close',3=>'Force Close')
                            ),
                            
                        ),
                        
		);
		return $dataTmp;
	}
	
	public function getAppraisalConfigbyID($id)
	{
	    $select = $this->select()
						->setIntegrityCheck(false)
						->from(array('im'=>'main_pa_implementation'),array('im.*'))
					    ->where('im.isactive = 1 AND im.id='.$id.' ');
		return $this->fetchAll($select)->toArray();
	
	}
/**
 * 
 * Enter description here ...
 * @param unknown_type $data
 * @param unknown_type $where
 */	
	public function SaveorUpdateAppraisalConfigData($data, $where)
	{ //echo "coming here";
	  //echo "<pre>"; print_r($data); echo "</pre>"; die;
	    if($where != ''){
			$this->update($data, $where);
			return 'update';
		} else {
			$this->insert($data);
			$id=$this->getAdapter()->lastInsertId('main_pa_implementation'); 
			return $id;
		}
		
	
	}
	
	public function getActiveServiceDepartments($bunitid,$deptid='')
	{
		$where = 'sc.isactive=1';
		if($bunitid != '' && $bunitid !='null')
			$where .= ' AND sc.businessunit_id = '.$bunitid.'';
		if($deptid !='' && $deptid !='null')
			$where .= ' AND sc.department_id = '.$deptid.'';
		
		
		$db = Zend_Db_Table::getDefaultAdapter();
		$qry = "select sc.service_desk_id from main_pa_implementation sc where ".$where." ";
		$res = $db->query($qry)->fetchAll();
		$serviceId = '';
		if(!empty($res))
		{
			foreach ($res as $ids)
			{
				$serviceId.=$ids['service_desk_id'].',';
			}
			$serviceId = rtrim($serviceId,',');
		}
		$resWhere = 'sd.isactive = 1';
		if($serviceId !='')
		$resWhere.= ' AND sd.id NOT IN ('.$serviceId.')';
		$resultqry = "select sd.id,sd.service_desk_name from main_sd_depts sd where ".$resWhere." ";
		$result = $db->query($resultqry)->fetchAll();
		return $result;
	}
	
	public function checkuniqueAppraisalConfigData($bunitid,$paflag,$deptid='')
	{
		$where = 'im.isactive=1 AND im.businessunit_id ='.$bunitid.' AND im.performance_app_flag ='.$paflag.'';
		if($deptid !='' && $deptid !='null')
			$where .= ' AND im.department_id = '.$deptid.'';
			
	   	$db = Zend_Db_Table::getDefaultAdapter();
		$qry = "select count(*) as count from main_pa_implementation im where ".$where." ";
		$res = $db->query($qry)->fetchAll();	
		return $res;	
		
	}
	
	/**
	 * 
	 * Here we are checking if any pending request is there for a business unit
	 * @param integer $bunitid = Id of business unit.
	 */
	public function getPendingAppraisalConfigData($bunitid)
	{
		$where = 'im.isactive=1 AND im.businessunit_id ='.$bunitid.' and pi.isactive=1 and pi.status =1';
			
	   	$db = Zend_Db_Table::getDefaultAdapter();
		 $qry = "select count(*) as count from main_pa_implementation im
				left join main_pa_initialization pi on pi.pa_configured_id = im.id 
		        where ".$where." ";
		$res = $db->query($qry)->fetchAll();	
		return $res;	
		
	}
	
		
	public function getDepartments($id,$dept_list)
	{
		$str = '';
		if($id != '')
            {
           if($dept_list!='')
           $str = "AND d.id not in($dept_list)";
            	
	  $select = $this->select()
						->setIntegrityCheck(false)
						->from(array('d'=>'main_departments'),array('d.id','d.deptname'))
					    ->where('d.unitid = '.$id.' AND d.isactive = 1 '.$str)
						->order('d.deptname');
	//echo $select;exit;					
		return $this->fetchAll($select)->toArray();
            }
            else 
                return array();
	}
	
	public function getExistDepartments($id)
	{
		if($id != '')
           {
	  $select = $this->select()
						->setIntegrityCheck(false)
						->from(array('d'=>'main_pa_implementation'),array('d.businessunit_id','d.department_id'))
					    ->where('d.businessunit_id = '.$id.' AND d.isactive = 1');
						
	//echo $select;exit;					
		return $this->fetchAll($select)->toArray();
            }
            else 
                return array();
	}
	
    public function check_delete($config_id)
    {
        if($config_id != '')
        {
            $db = Zend_Db_Table::getDefaultAdapter();
            $query = "select count(id) cnt from main_pa_initialization "
                    . "where pa_configured_id = '".$config_id."' and isactive = 1";
            $result = $db->query($query)->fetch();
            return $result['cnt'];
        }
        return 5;
    }

    /**
     * 
     * Check weather duplicate combination of Appraisal configuration exists or not
     * @param Array $data - Configuration posted by user
     * @return Boolean - 'True' when there are duplicate records else 'False'
     */
    public function combinationExists($data = array()) {
    	
    	if (empty($data['department_id'])) {
	    	// Get count of duplicates by 'Business unit'
	    	$sql = "SELECT COUNT(id) count_rec FROM main_pa_implementation WHERE businessunit_id = '".$data['business_unit_id']."' AND  department_id IS NULL";
    	} else {
    		// Get count of duplicates by 'Business Unit' and 'Department ID'
    		$sql = "SELECT COUNT(id) count_rec FROM main_pa_implementation WHERE businessunit_id = '".$data['business_unit_id']."' AND department_id = '".$data['department_id']."'";    	
    	}
    	
    	if (!empty($data['id'])) {
    		$sql .= " AND id !='".$data['id']."'";
    	}
    	
    	$result = $this->_db->query($sql)->fetch();
    	
    	if ($result["count_rec"] > 0) {
    		return true;
    	}
    	return false;
    }
    // for appraisal initialization status of Business unit 
    public function initializedCheck($bunit='',$paflag='')
    {
    	$db = Zend_Db_Table::getDefaultAdapter();
    	if($paflag == 1)
    	$con = " is null ";
    	$con = " is not null ";
    	$query = "select count(id) count from main_pa_initialization where businessunit_id = $bunit and isactive=1 and status = 2 and department_id $con ;";
    	$result = $this->_db->query($query)->fetch();
    	return $result['count'];
    }
    
    public function filterBU()
    {
    	$db = Zend_Db_Table::getDefaultAdapter();
    	/**
		** commented on 20-04-2015
		**
		$qry_main = "  select a.unitid 
							from main_departments a 
							where 
							unitid not in (select b.businessunit_id 
							from main_pa_implementation b
							where b.isactive = 1 and b.department_id is null and b.businessunit_id is not null) 
							and id not in (select b.department_id 
							from main_pa_implementation b
							where b.isactive = 1 and b.department_id is not null and b.businessunit_id is not null) 
							and a.isactive = 1
							group by a.unitid;"; 
		**
		** commented on 20-04-2015
		**/
		 $qry_main = "  select a.unitid from main_businessunits b left join main_departments a on b.id = a.unitid 
							where 
							a.unitid not in (select b.businessunit_id 
							from main_pa_implementation b
							where b.isactive = 1 and b.department_id is null and b.businessunit_id is not null) 
							and a.id not in (select b.department_id 
							from main_pa_implementation b
							where b.isactive = 1 and b.department_id is not null and b.businessunit_id is not null) 
							and a.isactive = 1
							group by a.unitid;";

    	$result_main = $this->_db->query($qry_main)->fetchAll();
    	$bunitImpArr = array();
    	$result  = '';
    	if(!empty($result_main))
    	{ 
    		foreach($result_main as $main_res)
    		{
    			$bunit_main = $main_res['unitid'];
	    		array_push($bunitImpArr,$bunit_main);
    		}
    	        $bunitList = implode(",",$bunitImpArr);
    		
    	    $unit_Qry = "select id,unitname from main_businessunits where id in ($bunitList) and isactive=1;";  
    	    $result = $this->_db->query($unit_Qry)->fetchAll();  
    	        
    	}
    	return $result;
    	
    	
    
    }
    
    public function getUserDetailsByID($unit_id,$department_id)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
    	$str_dept = '';
    	if($department_id != '')
    	$str_dept = " department_id = $department_id and ";
    	$qry = "select es.id,es.user_id,es.userfullname,es.emailaddress from main_employees_summary es where es.user_id in (
				select s.user_id from main_employees_summary s 
				inner join main_roles r on s.emprole = r.id where (s.businessunit_id=$unit_id 
				and $str_dept r.group_id = ".HR_GROUP.") or r.group_id = ".MANAGEMENT_GROUP."  and r.isactive=1 ) and es.isactive =1 ;"; 
    	$result = $this->_db->query($qry)->fetchAll();
    	 return $result;
    }

	public function checkInitializationData($impId)
	{
		if(!empty($impId))
		{
			$db = Zend_Db_Table::getDefaultAdapter();
			$query = 'select count(id) as cnt from main_pa_initialization where pa_configured_id = '.$impId;
			$res = $this->_db->query($query)->fetchAll();
			
			if(!empty($res) && isset($res[0]))
			{
				if($res[0]['cnt'] > 0)
					return 1;
				else
					return 0;
			}
			else
				return 0;

		}
	}

	public function getBunit($businessunit_id)
    {
        $data = array();
        if($businessunit_id != '')
        {
            $db = Zend_Db_Table::getDefaultAdapter();
            $query = "select unitname from main_businessunits where id = $businessunit_id and isactive=1;";
            $data = $db->query($query)->fetch();
        }
        return $data;
    }
    
	public function getBunitDept($businessunit_id,$deptId)
    {
        $data = array();
        if($businessunit_id != '')
        {
            $db = Zend_Db_Table::getDefaultAdapter();
            $query = "select b.unitname,d.deptname from main_departments d 
						inner join main_businessunits b on d.unitid = b.id where d.id = $deptId and d.unitid = $businessunit_id and d.isactive=1 and b.isactive=1;";
            $data = $db->query($query)->fetch();
        }
        return $data;
    }
   
}