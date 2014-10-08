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
 * Helper class to define lot of useful functions.
 * @author ramakrishna
 */
class sapp_Helper 
{
    /**
     * This function is used in header to display left side menu of service desk.
     * @param integer $login_id = id of the login user.
     * @param string $call       = from where function is calling
     * @return string HTML content
     */
    public static function service_header($data,$call)
    {
        $login_id = $data->id;
        $pending_url = BASE_URL."/servicerequests/index/t/".sapp_Global::_encrypt("1")."/v/".sapp_Global::_encrypt("17");
        $closed_url = BASE_URL."/servicerequests/index/t/".sapp_Global::_encrypt("1")."/v/".sapp_Global::_encrypt("2");
        $cancel_url = BASE_URL."/servicerequests/index/t/".sapp_Global::_encrypt("1")."/v/".sapp_Global::_encrypt("3");
        $reject_url = BASE_URL."/servicerequests/index/t/".sapp_Global::_encrypt("1")."/v/".sapp_Global::_encrypt("16");
        $all_url = BASE_URL."/servicerequests/index/t/".sapp_Global::_encrypt("1");
        $sd_req_model = new Default_Model_Servicerequests();
        $counts = $sd_req_model->getRequestsCnt($login_id,'request');        
                
        $pending_cnt = $closed_cnt = $cancel_cnt = $rejected_cnt = 0;
        if(count($counts) > 0)
        {
            foreach($counts as $cnt)
            {
                if($cnt['status'] != 'Closed' && $cnt['status'] != 'Cancelled' && $cnt['status'] != 'Rejected') $pending_cnt += $cnt['cnt'];
                if($cnt['status'] == 'Closed') $closed_cnt += $cnt['cnt'];
                if($cnt['status'] == 'Cancelled') $cancel_cnt += $cnt['cnt'];
                if($cnt['status'] == 'Rejected') $rejected_cnt += $cnt['cnt'];
            }
        }
        $html = '';
        if($call == 'helper')
        {
            $html .= '<div style="" class="side-menu div_mchilds_'.SERVICEDESK.' selected_menu_class">';
            $html .= '    <ul>';
        }
        $html .= '        <li class="acc_li"><span><b>My request summary</b></span>';
        $html .= '            <ul>';
        $html .= '                <li menu-url="'.$all_url.'" parent-div="div_mchilds_'.SERVICEDESK.'" super-parent="main_parent_'.SERVICEDESK.'"  class="clickable_menu"><a href="'.(($call == 'menusettings')?"javascript:void(0);":$all_url).'" ><i class="span_sermenu">All</i> <b class="super_cnt">'.($pending_cnt+$cancel_cnt+$closed_cnt+$rejected_cnt).'</b></a></li>';
        $html .= '                <li menu-url="'.$pending_url.'" parent-div="div_mchilds_'.SERVICEDESK.'" super-parent="main_parent_'.SERVICEDESK.'"  class="clickable_menu"><a href="'.(($call == 'menusettings')?"javascript:void(0);":$pending_url).'" ><i class="span_sermenu">Open</i> <b class="super_cnt">'.$pending_cnt.'</b></a></li>';
        $html .= '                <li menu-url="'.$closed_url.'" parent-div="div_mchilds_'.SERVICEDESK.'" super-parent="main_parent_'.SERVICEDESK.'"  class="clickable_menu"><a href="'.(($call == 'menusettings')?"javascript:void(0);":$closed_url).'" ><i class="span_sermenu">Closed</i> <b class="super_cnt">'.$closed_cnt.'</b></a></li>';
        $html .= '                <li menu-url="'.$reject_url.'" parent-div="div_mchilds_'.SERVICEDESK.'" super-parent="main_parent_'.SERVICEDESK.'"  class="clickable_menu"><a href="'.(($call == 'menusettings')?"javascript:void(0);":$reject_url).'" ><i class="span_sermenu">Rejected</i> <b class="super_cnt">'.$rejected_cnt.'</b></a></li>';
        $html .= '                <li menu-url="'.$cancel_url.'" parent-div="div_mchilds_'.SERVICEDESK.'" super-parent="main_parent_'.SERVICEDESK.'"  class="clickable_menu"><a href="'.(($call == 'menusettings')?"javascript:void(0);":$cancel_url).'" ><i class="span_sermenu">Cancelled</i> <b class="super_cnt">'.$cancel_cnt.'</b></a></li>';
        $html .= '            </ul>';
        $html .= '        </li>';
        if($call == 'helper')
        {
            $html .= '    </ul>';
            $html .= '</div>';
        }
        if($data->is_orghead == 1)
            $html = '';
        $check_receiver = $sd_req_model->check_receiver($login_id, $data->businessunit_id);
        $check_reporting = $sd_req_model->check_reporting($login_id);
        $check_approver = $sd_req_model->check_approver($login_id);
        
        if($check_receiver == 'yes' && $check_reporting == 'yes')
        {            
            $html .= self::sd_req_summary($login_id,'rec_rept',$call);            
        }
        else if($check_approver == 'yes' && $check_reporting == 'yes')
        {            
            $html .= self::sd_req_summary($login_id,'rept_app',$call);
        }
        else if($check_receiver == 'yes')
        {            
            $html .= self::sd_req_summary($login_id,'receiver',$call);            
        }
        else if($check_reporting == 'yes')
        {            
            $html .= self::sd_req_summary($login_id,'reporting',$call);
        }
        else if($check_approver == 'yes')
        {            
            $html .= self::sd_req_summary($login_id,'approver',$call);
        }
        if($data->is_orghead == 1)
        {
            $html .= self::sd_all_summary($login_id,'org_head',$call);
        }
                
        return $html;
    }//end of service_header function
    
    /**
     * This function is helper function to service_header to handle all request summary.
     * @param integer $login_id  = id of login user
     * @param string $context    = tells which type of call
     * @param string $call       = from where function is calling
     * @return string HTML content
     */
    public static function sd_all_summary($login_id,$context,$call)
    {
        $sd_req_model = new Default_Model_Servicerequests();
        $url_arr = array();
        $html = "";
        if($context == 'rec_rept' || $context == 'receiver')
        {
            $grid_type = 7;
            
            $all_counts = $sd_req_model->getRequestsCnt($login_id,'all_rec_rept');
            
            $to_app_cnt = isset($all_counts['To management approve'])?$all_counts['To management approve']:0;
            $to_mapp_cnt = isset($all_counts['To manager approve'])?$all_counts['To manager approve']:0;
            $tot_toapp_cnt = $to_app_cnt + $to_mapp_cnt;
            $url_arr['All'] = array('url'=>  self::sd_url_builder($grid_type, ''),'count' => (isset($all_counts['all'])?$all_counts['all']:0));
            $url_arr['Pending'] = array('url'=>  self::sd_url_builder($grid_type, '1'),'count' => (isset($all_counts['Pending'])?$all_counts['Pending']:0));
            $url_arr['Closed'] = array('url'=>  self::sd_url_builder($grid_type, '2'),'count' => (isset($all_counts['Closed'])?$all_counts['Closed']:0));
            $url_arr['Cancelled'] = array('url'=>  self::sd_url_builder($grid_type, '3'),'count' => (isset($all_counts['Cancelled'])?$all_counts['Cancelled']:0));
            $url_arr['Overdue'] = array('url'=>  self::sd_url_builder($grid_type, '4'),'count' => (isset($all_counts['overdue'])?$all_counts['overdue']:0));
            $url_arr['Due today'] = array('url'=>  self::sd_url_builder($grid_type, '5'),'count' => (isset($all_counts['duetoday'])?$all_counts['duetoday']:0));
            $url_arr['To approve'] = array('url'=>  self::sd_url_builder($grid_type, '6'),'count' => $tot_toapp_cnt);
            $url_arr['Approved'] = array('url'=>  self::sd_url_builder($grid_type, '7'),'count' => (isset($all_counts['Approved'])?$all_counts['Approved']:0));
        }        
        if($context == 'org_head')
        {
            $grid_type = 9;
            
            $all_counts = $sd_req_model->getRequestsCnt($login_id,'org_head');
            
            $to_app_cnt = isset($all_counts['To management approve'])?$all_counts['To management approve']:0;
            $to_mapp_cnt = isset($all_counts['To manager approve'])?$all_counts['To manager approve']:0;
            $tot_toapp_cnt = $to_app_cnt + $to_mapp_cnt;
            
            $app1_cnt = isset($all_counts['Management approved'])?$all_counts['Management approved']:0;
            $app2_cnt = isset($all_counts['Manager approved'])?$all_counts['Manager approved']:0;
            $tot_app_cnt = $app1_cnt + $app2_cnt;
            
            $rej1_cnt = isset($all_counts['Management rejected'])?$all_counts['Management rejected']:0;
            $rej2_cnt = isset($all_counts['Manager rejected'])?$all_counts['Manager rejected']:0;
            $tot_rej_cnt = $rej1_cnt + $rej2_cnt;
            
            $close_cnt = isset($all_counts['Closed'])?$all_counts['Closed']:0;
            $rej_cnt = isset($all_counts['Rejected'])?$all_counts['Rejected']:0;
            $cl_rj_cnt = $close_cnt + $rej_cnt;
            
            $url_arr['All'] = array('url'=>  self::sd_url_builder($grid_type, ''),'count' => (isset($all_counts['all'])?$all_counts['all']:0));
            $url_arr['Open'] = array('url'=>  self::sd_url_builder($grid_type, '1'),'count' => (isset($all_counts['Open'])?$all_counts['Open']:0));
            $url_arr['Closed/Rejected'] = array('url'=>  self::sd_url_builder($grid_type, '22'),'count' => $cl_rj_cnt);
            $url_arr['Cancelled'] = array('url'=>  self::sd_url_builder($grid_type, '3'),'count' => (isset($all_counts['Cancelled'])?$all_counts['Cancelled']:0));
            $url_arr['Overdue'] = array('url'=>  self::sd_url_builder($grid_type, '4'),'count' => (isset($all_counts['overdue'])?$all_counts['overdue']:0));
            $url_arr['Due today'] = array('url'=>  self::sd_url_builder($grid_type, '5'),'count' => (isset($all_counts['duetoday'])?$all_counts['duetoday']:0));
            $url_arr['To approve'] = array('url'=>  self::sd_url_builder($grid_type, '6'),'count' => $tot_toapp_cnt);            
            $url_arr['Approved'] = array('url'=>  self::sd_url_builder($grid_type, '20'),'count' => $tot_app_cnt);
            $url_arr['Rejected'] = array('url'=>  self::sd_url_builder($grid_type, '21'),'count' => $tot_rej_cnt);
        }
        if(count($url_arr) > 0)
        {                
            if($call == 'helper')
            {
                $html .= '<div style="" class="side-menu div_mchilds_'.SERVICEDESK.' selected_menu_class"><ul>'; 
            }

            $html .='<li class="acc_li"><span><b>All request summary</b></span>';
            $html .='  <ul>';
        
            foreach($url_arr as $menu_name => $menu_arr)
            {
                $html .='    <li menu-url="'.$menu_arr['url'].'" parent-div="div_mchilds_'.SERVICEDESK.'" super-parent="main_parent_'.SERVICEDESK.'"  class="clickable_menu"><a href="'.(($call == 'menusettings')?"javascript:void(0);":$menu_arr['url']).'" ><i class="span_sermenu">'.$menu_name.'</i> <b class="super_cnt">'.$menu_arr['count'].'</b></a></li>';
            }
        
        
            $html .='  </ul>';
            $html .='</li>';

            if($call == 'helper')
            {
                $html .='</ul></div>';
            }        
        }
        return $html;
    }
    /**
     * This function helps to build URL to service desk.
     * @param int $grid_type    = type of grid 
     * @param int $status       = status of service desk.
     * @return string Formatted URL.
     */
    public static function sd_url_builder($grid_type,$status)
    {
        if($status == '')
            return BASE_URL."/servicerequests/index/t/".sapp_Global::_encrypt($grid_type);
        else 
            return BASE_URL."/servicerequests/index/t/".sapp_Global::_encrypt($grid_type)."/v/".sapp_Global::_encrypt($status);
    }
    /**
     * This function is helper function to service_header to handle my action summary.
     * @param integer $login_id  = id of login user
     * @param string $context    = tells which type of call
     * @param string $call       = from where function is calling
     * @return string HTML content
     */
    public static function sd_req_summary($login_id,$context,$call)
    {
        $sd_req_model = new Default_Model_Servicerequests();
        $action_counts = array();
        $url_arr = array();
        $html = "";
        if($context == 'receiver')
        {
            $action_counts = $sd_req_model->getRequestsCnt($login_id,'receiver');
            $grid_type = 2;
            
            $mapp_cnt = isset($action_counts['Manager approved'])?$action_counts['Manager approved']:0;
            $app_cnt = isset($action_counts['Management approved'])?$action_counts['Management approved']:0;
            $rmapp_cnt = isset($action_counts['Manager rejected'])?$action_counts['Manager rejected']:0;
            $rapp_cnt = isset($action_counts['Management rejected'])?$action_counts['Management rejected']:0;
            $wmapp_cnt = isset($action_counts['To manager approve'])?$action_counts['To manager approve']:0;
            $wapp_cnt = isset($action_counts['To management approve'])?$action_counts['To management approve']:0;
            
            $pending_cnt = $mapp_cnt + $app_cnt + $rmapp_cnt + $rapp_cnt;
            $waiting_cnt = $wapp_cnt + $wmapp_cnt;
            
            $url_arr['All'] = array('url' => self::sd_url_builder($grid_type, ''),'count' => (isset($action_counts['all'])?$action_counts['all']:0),);
            $url_arr['Open'] = array('url' => self::sd_url_builder($grid_type, '1'),'count' => isset($action_counts['Open'])?$action_counts['Open']:0,);
            $url_arr['Pending'] = array('url' => self::sd_url_builder($grid_type, '8'),'count' => $pending_cnt,);
            $url_arr['Closed'] = array('url' => self::sd_url_builder($grid_type, '2'),'count' => (isset($action_counts['Closed'])?$action_counts['Closed']:0),);
            $url_arr['Rejected'] = array('url' => self::sd_url_builder($grid_type, '16'),'count' => (isset($action_counts['Rejected'])?$action_counts['Rejected']:0),);
            $url_arr['Cancelled'] = array('url' => self::sd_url_builder($grid_type, '3'),'count' => (isset($action_counts['Cancelled'])?$action_counts['Cancelled']:0),);
            $url_arr['Due today'] = array('url' => self::sd_url_builder($grid_type, '5'),'count' => (isset($action_counts['duetoday'])?$action_counts['duetoday']:0),);
            $url_arr['Overdue'] = array('url' => self::sd_url_builder($grid_type, '4'),'count' => (isset($action_counts['overdue'])?$action_counts['overdue']:0),);
            $url_arr['Sent for approval'] = array('url' => self::sd_url_builder($grid_type, '9'),'count' => $waiting_cnt,);
        }
        else if($context == 'reporting')
        {
            $grid_type = 4;
            $action_counts = $sd_req_model->getRequestsCnt($login_id,'reporting');
            
            $app_count = isset($action_counts['Manager approved'])?$action_counts['Manager approved']:0;
            $reject_cnt = isset($action_counts['Manager rejected'])?$action_counts['Manager rejected']:0;
            $rp_rj_cnt =  isset($action_counts['Rejected'])?$action_counts['Rejected']:0;
            $rp_cl_cnt =  isset($action_counts['Closed'])?$action_counts['Closed']:0;
                        
            $cl_rj_cnt = $rp_cl_cnt + $rp_rj_cnt;
            
            $url_arr['All'] = array('url' => self::sd_url_builder($grid_type, ''),'count' => (isset($action_counts['all'])?$action_counts['all']:0),);
            $url_arr['To approve'] = array('url' => self::sd_url_builder($grid_type, '13'),'count' => (isset($action_counts['To manager approve'])?$action_counts['To manager approve']:0),);
            $url_arr['Approved'] = array('url' => self::sd_url_builder($grid_type, '18'),'count' =>$app_count,);
            $url_arr['Rejected'] = array('url' => self::sd_url_builder($grid_type, '19'),'count' => $reject_cnt,);
            $url_arr['Closed/Rejected'] = array('url' => self::sd_url_builder($grid_type, '22'),'count' => $cl_rj_cnt,);
        }
        else if($context == 'rept_app')
        {
            $grid_type = 8;
            $action_counts = $sd_req_model->getRequestsCnt($login_id,'rept_app');
            
            $mapp_cnt = isset($action_counts['Manager approved'])?$action_counts['Manager approved']:0;
            $app_cnt = isset($action_counts['Management approved'])?$action_counts['Management approved']:0;
            $mrej_cnt = isset($action_counts['Manager rejected'])?$action_counts['Manager rejected']:0;
            $rej_cnt = isset($action_counts['Management rejected'])?$action_counts['Management rejected']:0;
            $wmapp_cnt = isset($action_counts['To manager approve'])?$action_counts['To manager approve']:0;
            $wapp_cnt = isset($action_counts['To management approve'])?$action_counts['To management approve']:0;
            $close_cnt = isset($action_counts['Closed'])?$action_counts['Closed']:0;
            $reject_cnt = isset($action_counts['Rejected'])?$action_counts['Rejected']:0;
            
            $approved_cnt = $mapp_cnt + $app_cnt; 
            $tot_reject_cnt =  $mrej_cnt + $rej_cnt;
            $waiting_cnt = $wapp_cnt + $wmapp_cnt;
            $tot_close_cnt = $reject_cnt + $close_cnt;
                        
            $url_arr['All'] = array('url' => self::sd_url_builder($grid_type, ''),'count' => (isset($action_counts['all'])?$action_counts['all']:0),);                        
            $url_arr['To approve'] = array('url' => self::sd_url_builder($grid_type, '6'),'count' => $waiting_cnt,);
            $url_arr['Approved'] = array('url' => self::sd_url_builder($grid_type, '20'),'count' => $approved_cnt,);
            $url_arr['Rejected'] = array('url' => self::sd_url_builder($grid_type, '21'),'count' => $tot_reject_cnt,);
            $url_arr['Closed/Rejected'] = array('url' => self::sd_url_builder($grid_type, '22'),'count' => $tot_close_cnt,);
        }
        else if($context == 'approver')
        {
            $grid_type = 5;
            $action_counts = $sd_req_model->getRequestsCnt($login_id,'approver');
                        
            $app_cnt = isset($action_counts['Management approved'])?$action_counts['Management approved']:0;            
            $rej_cnt = isset($action_counts['Management rejected'])?$action_counts['Management rejected']:0;            
            $wapp_cnt = isset($action_counts['To management approve'])?$action_counts['To management approve']:0;
            $close_cnt = isset($action_counts['Closed'])?$action_counts['Closed']:0;
            $reject_cnt = isset($action_counts['Rejected'])?$action_counts['Rejected']:0;
                                                
            $tot_close_cnt = $reject_cnt + $close_cnt;
                        
            $url_arr['All'] = array('url' => self::sd_url_builder($grid_type, ''),'count' => (isset($action_counts['all'])?$action_counts['all']:0),);                        
            $url_arr['To approve'] = array('url' => self::sd_url_builder($grid_type, '23'),'count' => $wapp_cnt,);
            $url_arr['Approved'] = array('url' => self::sd_url_builder($grid_type, '24'),'count' => $app_cnt,);
            $url_arr['Rejected'] = array('url' => self::sd_url_builder($grid_type, '25'),'count' => $rej_cnt,);
            $url_arr['Closed/Rejected'] = array('url' => self::sd_url_builder($grid_type, '22'),'count' => $tot_close_cnt,);
        }        
        else if($context == 'rec_rept')
        {
            $grid_type = 6;
            $action_counts = $sd_req_model->getRequestsCnt($login_id,'rec_rept');
            
            $mapp_cnt = isset($action_counts['Manager approved'])?$action_counts['Manager approved']:0;
            $app_cnt = isset($action_counts['Management approved'])?$action_counts['Management approved']:0;
            $rmapp_cnt = isset($action_counts['Manager rejected'])?$action_counts['Manager rejected']:0;
            $rapp_cnt = isset($action_counts['Management rejected'])?$action_counts['Management rejected']:0;
            $wmapp_cnt = isset($action_counts['To manager approve'])?$action_counts['To manager approve']:0;
            $wapp_cnt = isset($action_counts['To management approve'])?$action_counts['To management approve']:0;
            $mrejected_cnt = isset($action_counts['mrejected'])?$action_counts['mrejected']:0;
            $mclosed_cnt = isset($action_counts['mclosed'])?$action_counts['mclosed']:0;
            
            $pending_cnt = $mapp_cnt + $app_cnt + $rmapp_cnt + $rapp_cnt;
            $waiting_cnt = $wapp_cnt + $wmapp_cnt;
            $cl_rj_cnt = $mrejected_cnt + $mclosed_cnt;
            
            $url_arr['All'] = array('url' => self::sd_url_builder($grid_type, ''),'count' => (isset($action_counts['all'])?$action_counts['all']:0),);
            $url_arr['Open'] = array('url' => self::sd_url_builder($grid_type, '1'),'count' => isset($action_counts['Open'])?$action_counts['Open']:0,);
            $url_arr['Pending'] = array('url' => self::sd_url_builder($grid_type, '8'),'count' => $pending_cnt,);
            $url_arr['Closed'] = array('url' => self::sd_url_builder($grid_type, '2'),'count' => (isset($action_counts['Closed'])?$action_counts['Closed']:0),);
            $url_arr['Rejected'] = array('url' => self::sd_url_builder($grid_type, '16'),'count' => (isset($action_counts['Rejected'])?$action_counts['Rejected']:0),);
            $url_arr['Cancelled'] = array('url' => self::sd_url_builder($grid_type, '3'),'count' => (isset($action_counts['Cancelled'])?$action_counts['Cancelled']:0),);
            $url_arr['Due today'] = array('url' => self::sd_url_builder($grid_type, '5'),'count' => (isset($action_counts['duetoday'])?$action_counts['duetoday']:0),);
            $url_arr['Overdue'] = array('url' => self::sd_url_builder($grid_type, '4'),'count' => (isset($action_counts['overdue'])?$action_counts['overdue']:0),);
            $url_arr['Sent for approval'] = array('url' => self::sd_url_builder($grid_type, '9'),'count' => $waiting_cnt,);
            $url_arr['As a reporting manager'] = array('url' => '','count' => 0,);
            $url_arr['To approve'] = array('url' => self::sd_url_builder($grid_type, '10'),'count' => (isset($action_counts['to_approve'])?$action_counts['to_approve']:0),);
            $url_arr['Approved '] = array('url' => self::sd_url_builder($grid_type, '18'),'count' => (isset($action_counts['manager_approved'])?$action_counts['manager_approved']:0),);
            $url_arr['Rejected '] = array('url' => self::sd_url_builder($grid_type, '19'),'count' => (isset($action_counts['manager_rejected'])?$action_counts['manager_rejected']:0),);
            $url_arr['Closed/Rejected'] = array('url' => self::sd_url_builder($grid_type, '22'),'count' => $cl_rj_cnt,);
        }
        if(count($url_arr) > 0)
        {        
            if($call == 'helper')
            {
                $html .='<div style="" class="side-menu div_mchilds_'.SERVICEDESK.' selected_menu_class"><ul>';
            }

            $html .='<li class="acc_li"><span><b>My action summary</b></span>';
            $html .='  <ul>';

            foreach($url_arr as $menu_name => $menu_arr)
            {
                if($menu_arr['url'] != '')
                    $html .='    <li menu-url="'.$menu_arr['url'].'" parent-div="div_mchilds_'.SERVICEDESK.'" super-parent="main_parent_'.SERVICEDESK.'"  class="clickable_menu"><a href="'.(($call == 'menusettings')?"javascript:void(0);":$menu_arr['url']).'" ><i class="span_sermenu">'.$menu_name.'</i> <b class="super_cnt">'.$menu_arr['count'].'</b></a></li>';
                else 
                    $html .= '<span><b>'.$menu_name.'</b></span>';
            }

            $html .='  </ul>';
            $html .='</li>';

            if($call == 'helper')
            {
                $html .='</ul></div>';
            }       
        }
        return $html;
    }
    /**
     * This function is used in views of service desk related,this will help to reuse html tags in view files
     * @param array $msg_array     = array of error messages
     * @param object $form         = form object
     * @param string $element      = name of the element
     * @param string $imgtitle      = title of the image
     * @param string $extra_class  = extra classes that will apply to master div tag.
     * @param string $required     = required class name.
     * @param array $popup_arr     = array of parameters that can form link for popup.
     * @return string HTML content
     */
    public static function sd_form_helper($msg_array,$form,$element,$imgtitle,$extra_class,$required,$popup_arr)
    {
    	if($imgtitle !='')
    	   $labelimg = '<img class="tooltip" title="'.$imgtitle.'" src="'.DOMAIN.'/public/media/images/help.png">';
    	else
    	   $labelimg = '';       		
?>
        <div class="new-form-ui <?php echo $extra_class;?>">
            <label class="<?php echo $required;?>"><?php echo $form->$element->getLabel();?> <?php echo $labelimg;?></label>
            <div class="division"><?php echo $form->$element; ?>
                <?php if(isset($msg_array[$element])){?>
                    <span class="errors" id="errors-<?php echo $form->$element->getId(); ?>"><?php echo $msg_array[$element];?></span>
                <?php }?>
<?php 
                    if(count($popup_arr) > 0)
                    {                        
?>	
                        <span class="add-coloum" onclick="displaydeptform('<?php echo DOMAIN.$popup_arr['popup_url'] ?>','<?php echo $popup_arr['popup_disp_name'];?>');"> <?php echo $popup_arr['popup_link_name'];?> </span>			
<?php       
                    }
?>
            </div>
        </div>
<?php
    }//end of sd_form_helper function.
            
    /**
     * This function is used for popups in views of service desk related,this will help to reuse popup container view files
     * @param string $controllername = name of the controller
     * @return string HTML content
     */
    public static function popup_helper($controllername)
    {
?>
        <div id="<?php echo $controllername?>Container"  style="display: none; overflow: auto;">
            <iframe id="<?php echo $controllername?>Cont" class="business_units_iframe" frameborder="0"></iframe>
        </div>
<?php     	
    }// end of popup_helper 
    
    /**
     * This function gives names of menu names
     * @return array Array of grid menu names
     */
    public static function sd_menu_names()
    {
        return array(1=> 'My Request ',2=>'My Action ',3=>'My Action ', 
                     4 => 'My Action ',5=> 'My Action ',6 => 'My Action ',
                     7=> 'My Action ',8 => 'My Action ',9 => 'All Request');
    }
    
    public static function sd_action_names()
    {
        return array(1 => 'Open',2 => 'Closed',3 => 'Cancelled',4 => 'Overdue',5 => 'Due today',
                    6 => 'To approve',7 => 'Approved',8 => 'Pending',9 => 'Sent for approval',
                    10 => 'To approve',11 => 'To approve',12 => 'Approved/Rejected',13 => 'To approve',
                    14 => 'Approved/Rejected',15 => 'Pending',16 => 'Rejected',17 => 'Open',
                    18 => 'Approved',19 => 'Rejected',22 => 'Closed/Rejected',20 => 'Approved',
                    21 => 'Rejected',23 => 'To approve',24 => 'Approved',25 => 'Rejected');
    }
    
    public static function process_emp_excel($file_name)
    {        
        require_once 'Classes/PHPExcel.php';
        require_once 'Classes/PHPExcel/IOFactory.php';
        
        $auth = Zend_Auth::getInstance();
        if($auth->hasIdentity())
        {
            $loginUserId = $auth->getStorage()->read()->id;
        }
                
        $emp_model = new Default_Model_Employee();
        $usersModel = new Default_Model_Usermanagement();
        $identity_code_model = new Default_Model_Identitycodes();
                
        $objReader = PHPExcel_IOFactory::createReaderForFile($file_name);
        $objPHPExcel = $objReader->load($file_name);
        //Read first sheet
        $sheet 	= $objPHPExcel->getSheet(0);

        // Get worksheet dimensions
        $sizeOfWorksheet = $sheet->getHighestDataRow();
        $highestColumn 	 = $sheet->getHighestDataColumn();
        if($sizeOfWorksheet > 1)
        {    		
            $arrReqHeaders = array(
                'Prefix','Full name','Role Type','Email','Business Unit','Department','Reporting manager','Job Title' ,
                'Position','Employment Status','Date of joining','Date of leaving','Experience','Extension',
                'Work telephone number','Fax',
           );
				
            //Get first/header from excel
            $firstRow = $sheet->rangeToArray('A' . 1 . ':' . $highestColumn . 1, NULL, TRUE, TRUE);
            $arrGivenHeaders = $firstRow[0];
            
            $diffArray = array_diff_assoc($arrReqHeaders,$arrGivenHeaders);	
            $prefix_arr = $emp_model->getPrefix_emp_excel();
            $roles_arr = $emp_model->getRoles_emp_excel();
            $bu_arr = $emp_model->getBU_emp_excel();
            $dep_arr = $emp_model->getDep_emp_excel();
            $job_arr = $emp_model->getJobs_emp_excel();
            $positions_arr = $emp_model->getPositions_emp_excel();
            $users_arr = $emp_model->getUsers_emp_excel();
            $emp_stat_arr = $emp_model->getEstat_emp_excel();
            $dol_emp_stat_arr = $emp_model->getDOLEstat_emp_excel();
            $mng_roles_arr = $emp_model->getMngRoles_emp_excel();
            $emps_arr = $emp_model->getEmps_emp_excel();
            $emails_arr = $emps_arr['email'];
            $emp_ids_arr = $emps_arr['ids'];
            $emp_depts_arr = $emp_model->getEmpsDeptWise();
            $dept_bu_arr = $emp_model->getDeptBUWise();
            $pos_jt_arr = $emp_model->getPosJTWise();
                                      
            $identity_codes = $identity_code_model->getIdentitycodesRecord();
            $emp_identity_code = isset($identity_codes[0])?$identity_codes[0]['employee_code']:"";
            $trDb = Zend_Db_Table::getDefaultAdapter();
            // starting transaction
            $trDb->beginTransaction();
            try
            {
                //start of validations
                $ex_prefix_arr = array();$ex_fullname_arr = array();$ex_role_arr = array();$ex_email_arr = array();
                $ex_bu_arr = array();$ex_dep_arr = array();$ex_rm_arr = array();$ex_jt_arr = array();$ex_pos_arr = array();
                $ex_es_arr = array();$ex_doj_arr = array();$ex_dol_arr = array();$ex_exp_arr = array();$ex_ext_arr = array();
                $ex_wn_arr = array();$ex_fax_arr = array();$tot_rec_cnt = 0;
                
                $err_msg = "";
                for($i=2; $i <= $sizeOfWorksheet; $i++ )
                {
                    $rowData_org = $sheet->rangeToArray('A' . $i . ':' . $highestColumn . $i, NULL, TRUE, TRUE);
                    $rowData = $rowData_org[0];    
                    $rowData_cpy = $rowData;
                    foreach($rowData_cpy as $rkey => $rvalue)
                    {
                        $rowData[$rkey] = trim($rvalue);
                    }
                    //start of mandatory checking
                    if(empty($rowData[0]))
                    {
                        $err_msg = "Prefix cannot be empty at row ".$i.".";
                        break;
                    }
                    if(empty($rowData[1]))
                    {
                        $err_msg = "Full Name cannot be empty at row ".$i.".";
                        break;
                    }
                    if(empty($rowData[2]))
                    {
                        $err_msg = "Role Type cannot be empty at row ".$i.".";
                        break;
                    }
                    if(empty($rowData[3]))
                    {
                        $err_msg = "Email cannot be empty at row ".$i.".";
                        break;
                    }
                    if(empty($rowData[6]))
                    {
                        $err_msg = "Reporting manager cannot be empty at row ".$i.".";
                        break;
                    }
                    if(empty($rowData[7]))
                    {
                        $err_msg = "Job title cannot be empty at row ".$i.".";
                        break;
                    }
                    if(empty($rowData[8]))
                    {
                        $err_msg = "Position cannot be empty at row ".$i.".";
                        break;
                    }
                    if(empty($rowData[9]))
                    {
                        $err_msg = "Employment status cannot be empty at row ".$i.".";
                        break;
                    }
                    if(empty($rowData[10]))
                    {
                        $err_msg = "Date of joining cannot be empty at row ".$i.".";
                        break;
                    }
                    if(!in_array($rowData[2], $mng_roles_arr) && empty($rowData[5]))
                    {
                        $err_msg = "Department cannot be empty at row ".$i.".";
                        break;
                    }
                    if(in_array($rowData[9], $dol_emp_stat_arr) && empty($rowData[11]))
                    {
                        $err_msg = "Date of leaving cannot be empty at row ".$i.".";
                        break;
                    }
                    if(!in_array($rowData[9], $dol_emp_stat_arr) && !empty($rowData[11]) && in_array($rowData[9],$emp_stat_arr))
                    {
                        $err_msg = "Date of leaving must be empty for '".$rowData[9]."' at row ".$i.".";
                        break;
                    }
                    // end of mandatory checking
                    // start of pattern checking
                    if (!preg_match("/^(?=.*[a-zA-Z])([^ ][a-zA-Z0-9 ]*)$/", trim($rowData[0])) && !empty($rowData[0]) )
                    {
                        $err_msg = "Prefix is not a valid format at row ".$i.".";
                        
                        break;
                    }
                    if (!preg_match("/^([a-zA-Z.]+ ?)+$/", $rowData[1])  && !empty($rowData[1]))
                    {
                        $err_msg = "Full Name is not a valid format at row ".$i.".";
                        break;
                    }
                    if (!preg_match("/^[a-zA-Z]+?$/", $rowData[2])  && !empty($rowData[2]))
                    {
                        $err_msg = "Role Type is not a valid format at row ".$i.".";
                        break;
                    }
                    if (!preg_match("/^(?!.*\.{2})[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/", $rowData[3])  && !empty($rowData[3]))
                    {
                        $err_msg = "Email is not a valid format at row ".$i.".";                        
                        break;
                    }
                    if(!preg_match("/^[a-zA-Z0-9\&\'\.\s]+$/", $rowData[4])  && !empty($rowData[4]))
                    {
                        $err_msg = "Business unit is not a valid format at row ".$i.".";
                        break;
                    }
                    if(!preg_match("/^[a-zA-Z0-9\&\'\.\s]+$/", $rowData[5])  && !empty($rowData[5]))
                    {
                        $err_msg = "Department is not a valid format at row ".$i.".";
                        break;
                    }
                    if(!preg_match("/^([a-zA-Z]+\-[0-9]+)$/", $rowData[6])  && !empty($rowData[6]))
                    {
                        $err_msg = "Reporting manager is not a valid format at row ".$i.".";
                        break;
                    }
                    
                    if(!preg_match("/^[a-zA-Z][a-zA-Z0-9\s]*$/", $rowData[7])  && !empty($rowData[7]))
                    {
                        $err_msg = "Job title is not a valid format at row ".$i.".";
                        break;
                    }
                    if(!preg_match("/^[a-zA-Z][a-zA-Z0-9\-\s]*$/i", $rowData[8])  && !empty($rowData[8]))
                    {
                        $err_msg = "Position is not a valid format at row ".$i.".";
                        break;
                    }
                    if(!preg_match("/^(?=.*[a-zA-Z])([^ ][a-zA-Z0-9 ]*)$/", $rowData[9])  && !empty($rowData[9]))
                    {
                        $err_msg = "Employment status is not a valid format at row ".$i.".";
                        break;
                    }
                    if(!empty($rowData[10]))
                    {
                        try
                        {
                            $test_doj = new DateTime($rowData[10]);
                        } catch (Exception $ex) {
                            return array('status' => 'error' , 'msg' => "Date of joining is not a valid format at row ".$i.".");
                        }                    
                    }
                    if(!empty($rowData[11]))
                    {
                        try
                        {
                            $test_dol = new DateTime($rowData[11]);
                        } catch (Exception $ex) {
                            return array('status' => 'error' , 'msg' => "Date of leaving is not a valid format at row ".$i.".");
                        }                    
                    }
                    if(!empty($rowData[11]) && $rowData[11] < $rowData[10])
                    {
                        $err_msg = "Date of leaving must be greater than date of joining at row ".$i.".";
                        break;
                    }
                    if(!preg_match("/^[0-9]\d{0,1}(\.\d*)?$/", $rowData[12])  && !empty($rowData[12]))
                    {
                        $err_msg = "Experience is not a valid format at row ".$i.".";
                        break;
                    }
                    if(!preg_match("/^[0-9]+$/", $rowData[13])  && !empty($rowData[13]))
                    {
                        $err_msg = "Extension is not a valid format at row ".$i.".";
                        break;
                    }
                    if(!preg_match("/^(?!0{10})[0-9\+\-\)\(]+$/", $rowData[14])  && !empty($rowData[14]))
                    {
                        $err_msg = "Work telephone number is not a valid format at row ".$i.".";
                        break;
                    }
                    if(!preg_match("/^[0-9\+\-\)\(]+$/", $rowData[15])  && !empty($rowData[15]))
                    {
                        $err_msg = "Fax is not a valid format at row ".$i.".";
                        break;
                    }                    
                    // end of pattern checking
                    // start of checking existence in the system.
                    if(!array_key_exists(strtolower($rowData[0]), $prefix_arr) && !empty($rowData[0]))
                    {
                        $err_msg = "Unknown prefix at row ".$i.".";
                        break;
                    }
                    if(!array_key_exists(strtolower($rowData[2]), $roles_arr)  && !empty($rowData[2]))
                    {
                        $err_msg = "Unknown role type at row ".$i.".";
                        break;
                    }
                    if(!array_key_exists(strtolower($rowData[4]), $bu_arr)  && !empty($rowData[4]))
                    {
                        $err_msg = "Unknown business unit at row ".$i.".";
                        break;
                    }
                    if(!array_key_exists(strtolower($rowData[5]), $dep_arr)  && !empty($rowData[5]))
                    {
                        $err_msg = "Unknown department at row ".$i.".";
                        break;
                    }
                    if(in_array(strtolower($rowData[3]),$emails_arr) && !empty($rowData[3]))
                    {
                        $err_msg = "Email already exists at row ".$i.".";
                        break;
                    }
                    if(!in_array(strtolower($rowData[6]),$emp_ids_arr) && !empty($rowData[6]))
                    {
                        $err_msg = "Unknown reporting manager at row ".$i.".";
                        break;
                    }
                    if(!array_key_exists(strtolower($rowData[7]), $job_arr)  && !empty($rowData[7]))
                    {
                        $err_msg = "Unknown job title at row ".$i.".";
                        break;
                    }
                    if(!array_key_exists(strtolower($rowData[8]), $positions_arr)  && !empty($rowData[8]))
                    {
                        $err_msg = "Unknown position at row ".$i.".";
                        break;
                    }
                    if(!array_key_exists(strtolower($rowData[9]), $emp_stat_arr)  && !empty($rowData[9]))
                    {
                        $err_msg = "Unknown employment status at row ".$i.".";
                        break;
                    }
                    // end of checking existence in the system.                    
                    
                    if(!empty($rowData[5]))
                    {
                        if(isset($emp_depts_arr[$dep_arr[strtolower($rowData[5])]]) && !in_array(strtolower($rowData[6]),$emp_depts_arr[$dep_arr[strtolower($rowData[5])]]) )
                        {
                            if(!in_array(strtolower($rowData[6]),$emp_depts_arr[0]))
                            {
                                $err_msg = "Reporting manager is not belongs to '".$rowData[5]."' department at row ".$i.".";
                                break;
                            }
                        }
                    }
                    else
                    {
                        if(!in_array(strtolower($rowData[6]),$emp_depts_arr[0]))
                        {
                            $err_msg = "Reporting manager is not belongs to management group at row ".$i.".";
                            break;
                        }
                    }
                    
                    if(!empty($rowData[5]))
                    {
                        if(in_array(strtolower($rowData[5]),$dept_bu_arr[0]) && !empty($rowData[4]))
                        {
                            $err_msg = "Business unit not needed for this department '".$rowData[5]."' at row ".$i.".";
                            break;
                        }
                        if(!in_array(strtolower($rowData[5]),$dept_bu_arr[0]) && empty($rowData[4]))
                        {
                            $err_msg = "Business unit cannot be empty at row ".$i.".";
                            break;
                        }
                        if(!empty($rowData[4]))
                        {
                            if(isset($dept_bu_arr[$bu_arr[strtolower($rowData[4])]]) && !in_array(strtolower($rowData[5]),$dept_bu_arr[$bu_arr[strtolower($rowData[4])]])  && !empty($rowData[4]))
                            {
                                $err_msg = "Department is not belongs to '".$rowData[4]."' business unit at row ".$i.".";
                                break;
                            }
                        }
                        
                    }
                    if(!empty($rowData[7]) && !empty($rowData[8]))
                    {
                        if(isset($pos_jt_arr[$job_arr[strtolower($rowData[7])]]) && !in_array(strtolower($rowData[8]),$pos_jt_arr[$job_arr[strtolower($rowData[7])]])  && !empty($rowData[7]))
                        {
                            $err_msg = "Position is not belongs to '".$rowData[7]."' job title at row ".$i.".";
                            break;
                        }
                    }
                    
                }//end of for loop
                
                if(!empty($err_msg))
                    return array('status' => 'error' , 'msg' => $err_msg);
                $err_msg = "";
                for($i=2; $i <= $sizeOfWorksheet; $i++ )
                {
                    $rowData_org = $sheet->rangeToArray('A' . $i . ':' . $highestColumn . $i, NULL, TRUE, TRUE);
                    $rowData = $rowData_org[0];
                    $rowData_cpy = $rowData;
                    foreach($rowData_cpy as $rkey => $rvalue)
                    {
                        $rowData[$rkey] = trim($rvalue);
                    }
                    
                    $ex_prefix_arr[] = $rowData[0]; $ex_fullname_arr[] = $rowData[1]; $ex_role_arr[] = $rowData[2];
                    $ex_email_arr[$i] = $rowData[3]; $ex_bu_arr[] = $rowData[4]; $ex_dep_arr[] = $rowData[5];
                    $ex_rm_arr[] = $rowData[6]; $ex_jt_arr[] = $rowData[7]; $ex_pos_arr[] = $rowData[8];
                    $ex_es_arr[] = $rowData[9]; $ex_doj_arr[] = $rowData[10]; $ex_dol_arr[] = $rowData[11];
                    $ex_exp_arr[] = $rowData[12];    $ex_ext_arr[] = $rowData[13];    $ex_wn_arr[] = $rowData[14];
                    $ex_fax_arr[] = $rowData[15];
                    
                    $tot_rec_cnt++;
                }
                
                foreach($ex_email_arr as $key1 => $value1)
                {
                    $d = 0;
                    foreach($ex_email_arr as $key2 => $value2)
                    {
                        if($key1 != $key2 && $value1 == $value2)
                        {
                            $err_msg = "Duplicate email entry at row ".$key2.".";
                            $d++;
                            break;
                        }
                    }
                    if($d>0)
                        break;
                }
                if(!empty($err_msg))
                    return array('status' => 'error' , 'msg' => $err_msg);
                                                                               
                //end of validations
                //start of saving
                if($tot_rec_cnt > 0)
                {
                    for($i=2; $i <= $sizeOfWorksheet; $i++ )
                    {
                        $emp_id = $emp_identity_code."-".str_pad($usersModel->getMaxEmpId($emp_identity_code), 4, '0', STR_PAD_LEFT);
                        $rowData_org = $sheet->rangeToArray('A' . $i . ':' . $highestColumn . $i, NULL, TRUE, TRUE);
                        $rowData = $rowData_org[0];
                        $rowData_cpy = $rowData;
                        foreach($rowData_cpy as $rkey => $rvalue)
                        {
                            $rowData[$rkey] = trim($rvalue);
                        }
                        
                        $emppassword = sapp_Global::generatePassword();
                        $date = new DateTime($rowData[10]);
                        $date_of_joining = $date->format('Y-m-d');
                        $date_of_leaving = "";
                        if($rowData[11] != '')
                        {
                            $ldate = new DateTime($rowData[11]);
                            $date_of_leaving = $ldate->format('Y-m-d');
                        }

                        $user_data = array(
                            'emprole' =>$roles_arr[strtolower($rowData[2])],
                            'userfullname' => $rowData[1],
                            'emailaddress' => $rowData[3],
                            'jobtitle_id'=> $job_arr[strtolower($rowData[7])],
                            'modifiedby'=> $loginUserId,
                            'modifieddate'=> gmdate("Y-m-d H:i:s"),                                                                      
                            'emppassword' => md5($emppassword),
                            'employeeId' => $emp_id,
                            'modeofentry' => "Direct",                                                              
                            'selecteddate' => $date_of_joining,                    
                            'userstatus' => 'old',                    
                        );
                        $user_data['createdby'] = $loginUserId;
                        $user_data['createddate'] = gmdate("Y-m-d H:i:s");
                        $user_data['isactive'] = 1;

                        $user_id = $usersModel->SaveorUpdateUserData($user_data, '');

                        $data = array(  
                            'user_id'=>$user_id,
                            'reporting_manager'=>$users_arr[strtolower($rowData[6])],
                            'emp_status_id'=>$emp_stat_arr[strtolower($rowData[9])],
                            'businessunit_id'=>(!empty($rowData[4]))?$bu_arr[strtolower($rowData[4])]:0,
                            'department_id'=>(!empty($rowData[5]))?$dep_arr[strtolower($rowData[5])]:null,
                            'jobtitle_id'=>$job_arr[strtolower($rowData[7])], 
                            'position_id'=>$positions_arr[strtolower($rowData[8])], 
                            'prefix_id'=>$prefix_arr[strtolower($rowData[0])],
                            'extension_number'=>($rowData[13]!=''?$rowData[13]:NULL),
                            'office_number'=>($rowData[14]!=''?$rowData[14]:NULL),
                            'office_faxnumber'=>($rowData[15]!=''?$rowData[15]:NULL),  									
                            'date_of_joining'=>$date_of_joining,
                            'date_of_leaving'=>($date_of_leaving!=''?$date_of_leaving:NULL),
                            'years_exp'=>($rowData[12]=='')?null:$rowData[12],
                            'modifiedby'=>$loginUserId,				
                            'modifieddate'=>gmdate("Y-m-d H:i:s")
                        );
                        $data['createdby'] = $loginUserId;
                        $data['createddate'] = gmdate("Y-m-d H:i:s");;
                        $data['isactive'] = 1;
                        $emp_model->SaveorUpdateEmployeeData($data, '');
                        
                        
                        
                        $text = "<div style='padding: 0; text-align: left; font-size:14px; font-family:Arial, Helvetica, sans-serif;'>				
	<span style='color:#3b3b3b;'>Hello ".ucfirst($rowData[1]).",</span><br />
	
	<div style='padding:20px 0 0 0;color:#3b3b3b;'>You have been added to ". APPLICATION_NAME.". The login credentials for your Sentrifugo account are:</div>
	
	<div style='padding:20px 0 0 0;color:#3b3b3b;'>Username: <strong>".$emp_id."</strong></div>
	<div style='padding:5px 0 0 0;color:#3b3b3b;'>Password: <strong>".$emppassword."</strong></div>
	
	<div style='padding:20px 0 10px 0;'>Please <a href='".DOMAIN."index/popup' target='_blank' style='color:#b3512f;'>click here</a> to login  to your Sentrifugo account.</div>

</div>";
                        $options['subject'] = APPLICATION_NAME.': Login Credentials';
                        $options['header'] = 'Greetings from Sentrifugo';
                        $options['toEmail'] = $rowData[3];
                        $options['toName'] = $rowData[1];
                        $options['message'] = $text;
                        $options['cron'] = 'yes';
                        $result = sapp_Global::_sendEmail($options);
                    }//end of for loop

                    $trDb->commit();
                    return array('status' =>"success",'msg' => 'Employees saved successfully.');
                }
                else
                {
                    return array('status' => 'error' , 'msg' => "No records to save.");
                }
                //end of saving
            }
            catch(Exception $e)
            {
                $trDb->rollBack();
                return array('status' => 'error' , 'msg' => "Something went wrong,please try again.");
            }            
        }
        else 
        {
            return array('status' => 'error' , 'msg' => "No records to save.");
        }
        
    }//end of process_emp_excel function
}//end of class