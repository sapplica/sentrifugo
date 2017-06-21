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

class Default_Model_Medicalclaims extends Zend_Db_Table_Abstract
{	
    protected $_name = 'main_empmedicalclaims';
    protected $_primary = 'id';
	
       
    public function getempmedicalclaimdetails($sort, $by, $pageNo, $perPage,$searchQuery,$userid)
	{
       	$where = "m.isactive = 1 AND m.user_id = ".$userid;
		if($searchQuery)
			$where .= " AND ".$searchQuery;	
			
		$medicalclaimsdata = $this->select()
						->from(array('m'=>'main_empmedicalclaims'),array('id'=>'id','amount_approved'=>'amount_approved','amount_claimed_for'=>'amount_claimed_for','expected_date_join'=>'DATE_FORMAT(expected_date_join,"'.DATEFORMAT_MYSQL.'")','leaveappliedbyemployee_days'=>'leaveappliedbyemployee_days','leavebyemployeer_days'=>'leavebyemployeer_days','injury_type'=>'if(m.injury_type = 1, 	"Paternity",if(m.injury_type = 2 , "Maternity",if (m.injury_type = 	3,"Disability","Injury")))'))
						 ->where($where)
						  ->order("$by $sort") 
						  ->limitPage($pageNo, $perPage);
		
		return $medicalclaimsdata;       		
	}
	
	public function getmedicalclaimsdetails($id=0)
	{  
		$medicalclaimsDetailsArr="";
		$where = "";
		$db = Zend_Db_Table::getDefaultAdapter();		
		if($id != 0)
		{
			$where = "id =".$id;
			$medicalclaimsdata = $this->select()
									->from(array('m'=>'main_empmedicalclaims'))
									->where($where);
		
			
			$medicalclaimsDetailsArr = $this->fetchAll($medicalclaimsdata)->toArray(); 
        }
		return $medicalclaimsDetailsArr;       		
	}
	public function getempmedicalclaimtypes($userId)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$Data = $db->query('select if(injury_type = 1, "paternity",if(injury_type = 2 , "maternity",if (injury_type = 3,"disability","injury"))) as injuryType from main_empmedicalclaims where isactive =1 and user_id ='.$userId);	
		$data = $Data->fetchAll();
		
		return $data;
	}
    
    public function SaveorUpdateEmpmedicalclaimsDetails($data, $where)
    {
	    if($where != '')
        {
            $this->update($data, $where);
			return 'update';
        }
        else
        {
			$this->insert($data);
			$id=$this->getAdapter()->lastInsertId('main_empmedicalclaims');
			return $id;
		}
		
	
	}
	public function getGrid($sort,$by,$perPage,$pageNo,$searchData,$call,$dashboardcall,$exParam1='',$exParam2='',$exParam3='',$exParam4='')
	{		
        $searchQuery = '';$tablecontent = '';
        $searchArray = array();$data = array();$id='';
        $dataTmp = array();
		if($searchData != '' && $searchData!='undefined')
		{
			$searchValues = json_decode($searchData);
			foreach($searchValues as $key => $val)
			{	
				 if($key == 'expected_date_join')
				{
					$searchQuery .= " ".$key." like '%".  sapp_Global::change_date($val,'database')."%' AND ";
				}
				else
				{
					$searchQuery .= " ".$key." like '%".$val."%' AND ";										
				}
				$searchArray[$key] = $val;
			}
			$searchQuery = rtrim($searchQuery," AND");					
		}
		/** search from grid - END **/
		$objName = 'medicalclaims';
		$tableFields = array('action'=>'Action','injury_type'=>'Medical Claim Type','leaveappliedbyemployee_days'=>'Approved Leaves','leavebyemployeer_days'=>'Employee Applied Leaves','expected_date_join'=> 'Date of Joining');
						
		$tablecontent = $this->getempmedicalclaimdetails($sort,$by,$pageNo,$perPage,$searchQuery,$exParam1);
				
			
		$dataTmp = array('userid'=>$exParam1,
						'sort' => $sort,
						'by' => $by,
						'pageNo' => $pageNo,
						'perPage' => $perPage,				
						'tablecontent' => $tablecontent,
						'objectname' => $objName,
						'extra' => array(),
						'tableheader' => $tableFields,
						'jsGridFnName' => 'getEmployeeAjaxgridData',
						'jsFillFnName' => '',
						'searchArray' => $searchArray,
						'add'=>'add',
						'menuName'=>'Medical Claims',
						'formgrid' => 'true',
						'unitId'=>$exParam1,
						'call'=>$call,
						'context'=>$exParam2,
						'dashboardcall'=>$dashboardcall,
						'search_filters' => array(
						'injury_type' => array('type'=>'select',
											'filter_data'=>array(''=>'All',1 => 'Paternity',2 => 'Maternity',
												3=>'Disability',4=>'Injury')),
						'expected_date_join'=>array('type'=>'datepicker')
											)
			);	
			
		return $dataTmp;
	}

}