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

class Default_Model_Approvedrequisitions extends Zend_Db_Table_Abstract
{
    protected $_name = 'main_requisition';
    protected $_primary = 'id';
        	
    public function getGrid($sort,$by,$perPage,$pageNo,$searchData,$call,$dashboardcall,$statusid,$a1,$a2,$a3)
    {
        $searchQuery = '';
        $searchArray = array();
        $data = array();
	$requi_model = new Default_Model_Requisition();	
        $auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity())
        {
            $loginUserId = $auth->getStorage()->read()->id;			
            $loginuserGroup = $auth->getStorage()->read()->group_id;
        } 
        if($searchData != '' && $searchData!='undefined')
        {
            $searchValues = json_decode($searchData);
            if(count($searchValues) >0)
            {
                foreach($searchValues as $key => $val)
                {
                    if($key == 'onboard_date' || $key == 'r.createdon')
                    {
                        $searchQuery .= " date(".$key.") = '".  sapp_Global::change_date($val,'database')."' AND ";
                    }
                    else
                        $searchQuery .= " ".$key." like '%".$val."%' AND ";
                    $searchArray[$key] = $val;
                }
                $searchQuery = rtrim($searchQuery," AND");					
            }
        }
        $sarray = array('Approved' => 2,'Closed' => 4,'On hold' => 5,'Complete' => 6,'In process' => 7);
        
        if($statusid !='' && is_numeric($statusid))
        {
            if($statusid == 1)
                $queryflag = 'Approved';
            if($statusid == 2)
                $queryflag = 'Approved';
            else if($statusid == 4)
                $queryflag = 'Closed'; 
            else if($statusid == 5)
                $queryflag = 'On hold'; 
            else if($statusid == 6)
                $queryflag = 'Complete'; 
            else if($statusid == 7)
                $queryflag = 'In process'; 
            $statusidstring = sapp_Global::_encrypt($statusid);
        }
        else 
        {
            $statusid = 2;
            $queryflag = 'Approved';
            $statusidstring = sapp_Global::_encrypt('2');
        }
        $objName = 'approvedrequisitions';

        $tableFields = array('action'=>'Action',
                             'requisition_code' => 'Requisition Code',                            							 
                            'jobtitle_name' => 'Job Title',  
                             'createdby_name'	=> 'Raised By',
                           // 'reporting_manager_name' => 'Reporting Manager',
                             'req_no_positions' => 'No. of Positions',
                             'filled_positions' => 'Filled Positions',			 
                           // 'r.createdon'=> 'Raised On',
                          //  'onboard_date' => 'Due Date',
                            );
        $search_filters = array(
                            'r.createdon' =>array('type'=>'datepicker'),
                            'onboard_date'=>array('type'=>'datepicker')
                        );
        if($dashboardcall == 'Yes')
        {
            $tableFields['req_status'] = "Status";
            $search_filters['req_status'] = array(
                'type' => 'select',
                'filter_data' => array('Approved' => 'Approved','Closed'=>'Closed','On hold'=>'On hold','Complete'=>'Complete','In process'=>'In process',),
            );
            $statusid = 2;
            $queryflag = 'Approved';
            $statusidstring = sapp_Global::_encrypt('2');
            if(isset($searchArray['req_status']))
            {
                $queryflag = $searchArray['req_status'];
                $statusidstring = sapp_Global::_encrypt($sarray[$queryflag]);
                $statusid = $sarray[$queryflag];
            }
        }
        
        $tablecontent = $requi_model->getRequisitionData($sort, $by, $pageNo, $perPage,$searchQuery,$loginUserId,$loginuserGroup,2,$queryflag);     

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
				'add' =>'add',
                'searchArray' => $searchArray,
				'menuName'=>'Approved Requisitions',
				'formgrid' => 'true',
				'unitId'=>$statusidstring,
				'call'=>$call,
				'dashboardcall'=>$dashboardcall,
				'search_filters' => $search_filters,
        );
        if(($statusid == 4 || $statusid == 6) && ($loginuserGroup == HR_GROUP || $loginuserGroup == ''))
        {
            $dataTmp = $dataTmp + array('defined_actions'=>array('view'));            
        }
        return $dataTmp;
    }            
}//end of class