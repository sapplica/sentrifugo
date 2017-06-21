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

class Default_Model_Appraisalratings extends Zend_Db_Table_Abstract
{
    protected $_name = 'main_pa_ratings';
    protected $_primary = 'id';
	
	public function getAppraisalRatingsData($sort, $by, $pageNo, $perPage,$searchQuery,$implementationflag)
	{
		$auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity())
		{
			$loginuserRole = $auth->getStorage()->read()->emprole;
			$loginuserGroup = $auth->getStorage()->read()->group_id;
			$businessUnit = $auth->getStorage()->read()->businessunit_id;
			$department = $auth->getStorage()->read()->department_id;
     	}
		$where = " i.isactive=1 AND b.isactive=1 AND r.isactive=1 ";
		$db = Zend_Db_Table::getDefaultAdapter();		
		/* if($loginuserGroup == HR_GROUP)
		{
			$where.= " AND i.businessunit_id = $businessUnit AND (i.department_id=$department or i.department_id is null) ";
		} */
        if($searchQuery)
		{
            $where .= " AND ".$searchQuery;	
		}
		$appraisalRatingsData = $this->select()
    					   ->setIntegrityCheck(false)	
                           ->from(array('r'=>'main_pa_ratings'),array('r.*','rating' =>'concat(r.rating_value,"-",r.rating_text)','rating_type'=>'if (r.rating_type=1,"1-5","1-10")'))
							->joinInner(array('i'=>'main_pa_initialization'), 'i.id = r.pa_initialization_id', array('appr_period'=>'concat(case when i.appraisal_mode = "Yearly" then "Y" when i.appraisal_mode = "Half-yearly" then "H" when i.appraisal_mode = "Quarterly" then "Q" end,"",i.appraisal_period," Appraisal, ",i.to_year)','i.enable_step','i.employee_response','i.initialize_status','appraisal_ratings'=>'if (i.appraisal_ratings=1,"1-5","1-10")', new Zend_Db_Expr("CASE WHEN i.status=1 THEN 'Open' WHEN i.status=2 THEN 'Closed' ELSE 'Force Closed' END as status "),
							new Zend_Db_Expr("case when initialize_status = 1 then case when i.enable_step = 1 then 'Enabled to Managers' when i.enable_step = 2 then 'Enabled to Employees' end when initialize_status = 2 then 'Initialize later' when initialize_status is null then 'In progress' end as appraisal_process_status")))
                           ->joinLeft(array('b'=>'main_businessunits'), 'i.businessunit_id = b.id', array('deptname'=>'ifnull(d.deptname,"--")'))
                           ->joinLeft(array('d'=>'main_departments'), 'i.department_id = d.id', array('b.unitname'))
                           ->where($where)
    					   ->order("$by $sort")
    					   ->group('i.id') 
    					   ->limitPage($pageNo,$perPage);
		return $appraisalRatingsData;
	}
	public function getGrid($sort,$by,$perPage,$pageNo,$searchData,$call,$dashboardcall,$implementationflag,$b='',$c='',$d='')
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
				else if($key == 'rating_type')
					$searchQuery .= " r.".$key." like '%".$val."%' AND ";
				else if($key == 'status')
					$searchQuery .= " i.".$key." like '%".$val."%' AND ";	
				// else	
					// $searchQuery .= " c.".$key." like '%".$val."%' AND ";
					   $searchArray[$key] = $val;
			}
			$searchQuery = rtrim($searchQuery," AND");					
		}
		$objName = 'appraisalratings';
		$tableFields = array('action'=>'Action','appr_period'=>'Appraisal Period','unitname'=>'Business Unit','deptname' => 'Department','rating_type'=>'Rating Type','status'=>'Appraisal Status','appraisal_process_status'=>'Process Status');
		$tablecontent = $this->getAppraisalRatingsData($sort, $by, $pageNo, $perPage,$searchQuery,$implementationflag);
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
			'actionparam'=>'yes',
			'dashboardcall'=>$dashboardcall,
							'search_filters' => array(                            
								'rating_type' => array(
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
	
	
/**
 * 
 * Enter description here ...
 * @param unknown_type $data
 * @param unknown_type $where
 */	
	public function SaveorUpdateAppraisalRatingsData($data, $where)
	{ 
		  if($where != ''){
			$this->update($data, $where);
			return 'update';
		} else {
			$this->insert($data);
			$id=$this->getAdapter()->lastInsertId('main_pa_ratings'); 
			return $id;
		}
		
	
	}
	
	public function checkAccessAddratings($businessUnit,$department)
	{	
		$db = Zend_Db_Table::getDefaultAdapter();
		$auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity())
		{
			$loginuserRole = $auth->getStorage()->read()->emprole;
			$loginuserGroup = $auth->getStorage()->read()->group_id;
			$businessUnit = $auth->getStorage()->read()->businessunit_id;
			$department = $auth->getStorage()->read()->department_id;
     	}
		if($loginuserRole != 1)
		{
			if($businessUnit != '')
			{
				$query = "select id,performance_app_flag,appraisal_ratings from main_pa_implementation where businessunit_id = ".$businessUnit." and isactive = 1;";
				$res = $db->query($query)->fetchAll();
				if($res != '')
				{ 
					$implementation = '';
					foreach($res as $result)
					{
						$implementation = $result['performance_app_flag'];
					}
					if($implementation != '' && $implementation == 0)
					{
					 $query = "select id,performance_app_flag,appraisal_ratings from main_pa_implementation where businessunit_id = ".$businessUnit." and department_id = ".$department." and isactive = 1";
						$res1 = $db->query($query)->fetchAll();
						return $res1;
						
					}	
					else
					{
						return $res;
							
					}			
				}
			}
			else
			{
				return null;
			}
		}
		else
		{
			$query = "select * from main_pa_implementation where isactive = 1;";
			$res = $db->query($query)->fetchAll();
			return $res;
		}
	}
	
public function getAppraisalRatingsbyID($id)
	{
	    $select = $this->select()
						->setIntegrityCheck(false)
						->from(array('im'=>'main_pa_ratings'),array('im.*'))
					    ->where('im.isactive = 1 AND im.pa_configured_id='.$id.' ');
					    
			//echo "select:".$select;		    
		return $this->fetchAll($select)->toArray();
	
	}
	
	public function getAppraisalRatingsbyInitId($initid,$con = "")
	{
		if(!empty($con) && $con == '')
		{
			$select = $this->select()
						->setIntegrityCheck(false)
						->from(array('r'=>'main_pa_ratings'),array('r.*'))
						->joinInner(array('a'=>'main_pa_initialization'),'r.pa_initialization_id = a.id',array(" case when a.status = 1 then 'Open' 
							when a.status = 2 then 'Closed' 
							when a.status = 3 then 'Force Closed' end as status, 
						case 	when a.initialize_status = 2 then 'Initialize later' 
						when a.pa_configured_id is null then 'Not configured yet' 
						when a.initialize_status = 1 then 
						case when a.enable_step = 1 then 'Enabled to Managers' 
							 when a.enable_step = 2 then 'Enabled to Employees' end 
						when (a.pa_configured_id is not null and a.initialize_status is null) then 'In progress' end as initialize_status													
						"))
					    ->where('r.isactive = 1 AND r.id='.$initid.'');
		}
		else
		{
			$select = $this->select()
						->setIntegrityCheck(false)
						->from(array('im'=>'main_pa_ratings'),array('im.*'))
					    ->where('im.isactive = 1 AND im.pa_initialization_id='.$initid.'')
					    ->order('im.id');
		}
		return $this->fetchAll($select)->toArray();
	
	}	
	
	public function getManagerProfileImg($useridstring='')
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$emparr = array();
		if($useridstring !='' && $useridstring !='null')
		{
			$qry = "select e.userfullname,e.user_id,e.emprole,e.profileimg from main_employees_summary e where e.user_id IN(".$useridstring.")";
			$res = $db->query($qry)->fetchAll();
		}
		if(!empty($res))
		{
			foreach($res as $resArr)
			{
				$emparr['line_manager_img_'.$resArr['user_id']]= $resArr['profileimg'];
			}
		}
		return $emparr;
		
	}	
	
	public function getappdata($init_id)
    {
        $data = array();
        if($init_id != '')
        {
            $db = Zend_Db_Table::getDefaultAdapter();
            $query = "select pa.businessunit_id,if(pa.department_id is null,'',pa.department_id) deptid,b.unitname,if(d.deptname is null,'',d.deptname) deptname,pa.to_year,pa.initialize_status
                      from main_pa_initialization pa inner join main_businessunits b on b.id = pa.businessunit_id 
                      left join main_departments d on d.id = pa.department_id 
                       where pa.id = $init_id and pa.isactive = 1 ;";
            $data = $db->query($query)->fetch();
        }
        return $data;
    }
	
	
	public function getApprisalDatawithConfigId($configId)
    {
        $data = array();
        if($configId != '')
        {
            $db = Zend_Db_Table::getDefaultAdapter();
            $query = "select * from main_pa_implementation where id = $configId and isactive=1;";
            $data = $db->query($query)->fetch();
        }
        return $data;
    }
	
}