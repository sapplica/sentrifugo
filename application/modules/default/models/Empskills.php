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

class Default_Model_Empskills extends Zend_Db_Table_Abstract
{
	protected $_name = 'main_empskills';
	protected $_primary = 'id';

	public function getEmpSkillsData($sort, $by, $pageNo, $perPage,$searchQuery,$id)
	{
		$where = " e.user_id = ".$id." AND e.isactive = 1 ";

		if($searchQuery)
		$where .= " AND ".$searchQuery;
		$db = Zend_Db_Table::getDefaultAdapter();

		$empskillsData = $this->select()
		->setIntegrityCheck(false)
		->from(array('e' => 'main_empskills'),array('id'=>'e.id','skillname'=>'e.skillname','yearsofexp'=>'e.yearsofexp','year_skill_last_used'=>'DATE_FORMAT(e.year_skill_last_used,"'.DATEFORMAT_MYSQL.'")'))
		->joinLeft(array('c'=>'main_competencylevel'),'e.competencylevelid=c.id AND c.isactive = 1',array('competencylevelid'=>'c.competencylevel'))
		->where($where)
		->order("$by $sort")
		->limitPage($pageNo, $perPage);
		
		return $empskillsData;
	}
	/*	Purpose:	TO get drop down for search filters.Getting all competency levels from main_competencylevel table.
		Modified Date	:	21/10/2013.
		Modified By:	Yamini.
		*/
	public function empcompetencylevels($userId)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$Data = $db->query("select c.id,c.competencylevel
								from main_competencylevel as c
								where c.isactive=1 ");	
		$data = $Data->fetchAll();
		
		return $data;
	}
	public function getsingleEmpSkillsData($id)
	{
		$select = $this->select()
		->setIntegrityCheck(false)
		->from(array('es'=>'main_empskills'),array('es.*'))
		->where('es.id='.$id.' AND es.isactive = 1');
			
		return $this->fetchAll($select)->toArray();
	}

	public function SaveorUpdateEmpSkills($data, $where)
	{
		if($where != ''){
			$this->update($data, $where);
			return 'update';
		} else {
			$this->insert($data);
			$id=$this->getAdapter()->lastInsertId('main_empskills');
			return $id;
		}

	}
	public function getGrid($sort,$by,$perPage,$pageNo,$searchData,$call,$dashboardcall,$exParam1='',$exParam2='',$exParam3='',$exParam4='')
	{
		$searchQuery = '';$tablecontent = '';
		$searchArray = array();$data = array(); $dataTmp = array();
		/** search from grid - START **/
		if($searchData != '' && $searchData!='undefined')
		{
			$searchValues = json_decode($searchData);
			foreach($searchValues as $key => $val)
			{
				if($key == 'year_skill_last_used')
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
		/*	Purpose:TO get drop down for search filters.Getting all competency levels from main_competencylevel table.
			Modified Date	:	21/10/2013.
			Modified By:	Yamini.
			*/
		$empcompetencyLevelsArr = $this->empcompetencylevels($exParam1);
		$levelsArr = array();
		if(!empty($empcompetencyLevelsArr)){
			for($i=0;$i<sizeof($empcompetencyLevelsArr);$i++)
			{
				$levelsArr[$empcompetencyLevelsArr[$i]['id']] = $empcompetencyLevelsArr[$i]['competencylevel'];
			}
		}
		$objName = 'empskills';

		$tableFields = array('action'=>'Action','skillname'=>'Skill','yearsofexp'=>'Years of Experience','competencylevelid'=>'Competency Level','year_skill_last_used'=>'Skill Last Used Year');
			
		$tablecontent = $this->getEmpSkillsData($sort,$by,$pageNo,$perPage,$searchQuery,$exParam1);
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
						'menuName'=>'Skills',
						'formgrid'=>'true',
						'unitId'=>$exParam1,
						'dashboardcall'=>$dashboardcall,
						'call'=>$call,
						'context'=>$exParam2,
						'search_filters' => array(
						'competencylevelid' => array('type'=>'select',
													'filter_data'=>array(''=>'All')+$levelsArr),
						'year_skill_last_used'=>array('type'=>'datepicker')
		)
		);
		return $dataTmp;
	}


}
?>