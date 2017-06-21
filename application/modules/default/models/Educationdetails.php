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

class Default_Model_Educationdetails extends Zend_Db_Table_Abstract
{	
    protected $_name = 'main_empeducationdetails';
    protected $_primary = 'id';
	
       
    public function geteducationdetails($sort, $by, $pageNo, $perPage,$searchQuery,$userid)
	{
		// FOr particular Employee if record exists get the data.....
        $where = "e.isactive = 1 AND e.user_id = ".$userid;
		if($searchQuery)
			$where .= " AND ".$searchQuery;		
		$empeducationDetails = $this->select()
		                ->setIntegrityCheck(false)
						->from(array('e'=>'main_empeducationdetails'),array('id'=>'id','institution_name'=>'institution_name','course'=>'course','from_date'=>'DATE_FORMAT(from_date,"'.DATEFORMAT_MYSQL.'")','to_date'=>'DATE_FORMAT(to_date,"'.DATEFORMAT_MYSQL.'")','percentage'=>'percentage'))
						->joinLeft(array('ed'=>'main_educationlevelcode'), 'ed.id=e.educationlevel AND ed.isactive = 1',array('educationlevel'=>'ed.educationlevelcode'))							   
						 ->where($where)
						  ->order("$by $sort") 
						  ->limitPage($pageNo, $perPage);
		
		return $empeducationDetails;       		
	}
	
	public function geteducationdetailsRecord($id=0)
	{  
		$empeducationDetailsArr="";$where = "";
		$db = Zend_Db_Table::getDefaultAdapter();		
		if($id != 0)
		{
			$where = "id =".$id;
			$empeducationDetails = $this->select()
									->from(array('e'=>'main_empeducationdetails'))
									->where($where);
		
			
			$empeducationDetailsArr = $this->fetchAll($empeducationDetails)->toArray(); 
        }
		return $empeducationDetailsArr;       		
	}
    
    public function SaveorUpdateEducationDetails($data, $where)
    {	
	    if($where != '')
        {
           $this->update($data,$where);
			return 'update';
        }
        else
        {
			$this->insert($data);
			$id=$this->getAdapter()->lastInsertId('main_empeducationdetails');
			return $id;
		}
	}
	public function getGrid($sort,$by,$perPage,$pageNo,$searchData,$call,$dashboardcall,$exParam1='',$exParam2='',$exParam3='',$exParam4='')
	{		
        $searchQuery = '';$tablecontent = '';$level_opt = array();
        $searchArray = array();$data = array();$id='';
        $dataTmp = array();
		if($searchData != '' && $searchData!='undefined')
		{
			$searchValues = json_decode($searchData);
			foreach($searchValues as $key => $val)
			{
				if($key == 'from_date' || $key == 'to_date')
				{
					$searchQuery .= " ".$key." like '%".  sapp_Global::change_date($val,'database')."%' AND ";
				}
				else 
				$searchQuery .= " ".$key." like '%".$val."%' AND ";
				$searchArray[$key] = $val;
			}
			$searchQuery = rtrim($searchQuery," AND");					
		}
		/** search from grid - END **/
		$objName = 'educationdetails';
				
		$tableFields = array('action'=>'Action','educationlevel'=>'Education Level',
							 'institution_name'=>'Institution Name','course'=>'Course',
							 'from_date'=>'From',"to_date"=>"To","percentage"=>"Percentage");	
		
		$tablecontent = $this->geteducationdetails($sort, $by,$pageNo,$perPage,$searchQuery,$exParam1);     
		
		$educationlevelcodemodel = new Default_Model_Educationlevelcode();
		$educationlevelArr = $educationlevelcodemodel->getEducationlevelData();
		if(!empty($educationlevelArr))
		{
			foreach ($educationlevelArr as $educationlevelres)
			{                                                        
				$level_opt[$educationlevelres['id']] = $educationlevelres['educationlevelcode'];
			}
		}
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
						'dashboardcall'=>$dashboardcall,
						'add'=>'add',
						'menuName'=>'Education',
						'formgrid' => 'true',
						'unitId'=>$exParam1,
						'call'=>$call,
						'context'=>$exParam2,
						'search_filters' => array('from_date' =>array('type'=>'datepicker'),
													'to_date' =>array('type'=>'datepicker'),
                                                            'educationlevel' => array(
                                                                'type' => 'select',
                                                                'filter_data' => array('' => 'All')+$level_opt,
                                                            ),
                                                )									
						);	
		return $dataTmp;
	}
}