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

class Timemanagement_CronjobController extends Zend_Controller_Action
{

	private $options;
	public function preDispatch()
	{
		//check Time management module enable
		if(!sapp_Helper::checkTmEnable())
			$this->_redirect('error');

	}

	public function init()
	{

		$this->_options= $this->getInvokeArg('bootstrap')->getOptions();
	}

	public function indexAction()
	{

		$this->_helper->viewRenderer->setNoRender(true);
		$this->_helper->layout()->disableLayout();

		$date = new Zend_Date();
		
		//parameter wise cronjob start
		$a_date = ($this->_getParam('date'))?$this->_getParam('date'):"";
		//parameter wise cronjob end
		if($a_date!="")
		{
			$a_date = date("Y-m-d", strtotime($a_date));
		}
		
		//weekly mail reminder function
		$this->mailreminder($a_date);
		//monthly mail reminder function
		$this->monthlyempblockremainder($a_date);
		
		//monthly block function
		$this->monthlyblockedemp($a_date);

		$tm_cronjob_statusModel = new Timemanagement_Model_Cronjobstatus;
		$checkAnyCronRunning = $tm_cronjob_statusModel->checkCronRunning();
		if(count($checkAnyCronRunning) == 0){
			$cronstatusData = array('cronjob_status' => 'running',
										'start_date' => gmdate("Y-m-d H:i:s"),
										'end_date'=>gmdate("Y-m-d H:i:s")
			);

			$insertedCronjobStatusId = $tm_cronjob_statusModel->saveorUpdateCronjobStatusData($cronstatusData,'');

			$this->tmmailscron();

			$cronstatusData = array('cronjob_status' => 'stopped',
										'end_date'=>gmdate("Y-m-d H:i:s")
			);
			$whereCronstatus = array('id=?'=>$insertedCronjobStatusId);

			$updateCronjobStatus = $tm_cronjob_statusModel->saveorUpdateCronjobStatusData($cronstatusData,$whereCronstatus);

		}else{
			echo 'Some cron running';
		}
	}//end of index action

	/*****Timemanagememt crons ********/
	//weekly reminder mail cron
	public function mailreminder($aDate)
	{
		$this->_helper->viewRenderer->setNoRender(true);
		// the above is to stop search for view file
		$this->_helper->layout()->disableLayout();

		$tm_cronjob_statusModel = new Timemanagement_Model_Cronjobstatus;
		$checkAnyCronRunning = $tm_cronjob_statusModel->checkCronRunning();
		if(count($checkAnyCronRunning) == 0){
			$cronstatusData = array('cronjob_status' => 'running',
										'start_date' => gmdate("Y-m-d H:i:s"),
										'end_date'=>gmdate("Y-m-d H:i:s")
			);

			$insertedCronjobStatusId = $tm_cronjob_statusModel->saveorUpdateCronjobStatusData($cronstatusData,'');

			$tmconf_model = new Timemanagement_Model_Configuration();
			$getTmConfig = $tmconf_model->getActiveRecord();

			if(!empty($getTmConfig)){
				try{

					$weekCon_arr = array('sun'=>'Sunday','mon'=>'Monday','tue'=>'Tuesday','wed'=>'Wednesday','thu'=>'Thursday','fri'=>'Friday','sat'=>'Saturday');
					$startday_err=array('Sunday'=>7,'Monday'=>8,'Tuesday'=>9,'Wednesday'=>10,'Thursday'=>11,'Friday'=>12,'Saturday'=>13);
					$endday_err=array('Sunday'=>1,'Monday'=>2,'Tuesday'=>3,'Wednesday'=>4,'Thursday'=>5,'Friday'=>6,'Saturday'=>7);

					//$a_date = $this->_getParam('date');
					//$a_date = '2015-06-04';
					$a_date=($aDate!="")?$aDate:date('Y-m-d');
					$date = new DateTime($a_date);
					//echo $date->format('l').'----'. $weekCon_arr[$getTmConfig[0]['ts_weekly_reminder_day']];exit;
					if($date->format('l') == $weekCon_arr[$getTmConfig[0]['ts_weekly_reminder_day']]){

						$cron_day=$date->format('l');

						$start_interval='P'.$startday_err[$cron_day].'D';
						$end_interval='P'.$endday_err[$cron_day].'D';
						$date->sub(new DateInterval($start_interval));
						$ls_week=$date->format('Y-m-d');
						$date = new DateTime($a_date);
						$date->sub(new DateInterval($end_interval));
						$ls_day=$date->format('Y-m-d');
						//echo $ls_week.'-----'.$ls_day;//exit;

						$tmUsers_model=new Timemanagement_Model_Users();

						$mgr_list=$tmUsers_model->getManagers(); //echo '<pre>';print_r($mgr_list);exit;

						//reporting to managers
						foreach($mgr_list as $mgr)
						{
							$emp_data = $tmUsers_model->getEmployeesReportingTo($mgr['user_id'],$cron_day);

							$emp_arr=array();
							if(count($emp_data) > 0){
								foreach($emp_data as $emp)
								{
									$emp_joindate = new DateTime($emp['date_of_joining']);
									$emp_joindate = $emp_joindate->format('Y-m-d');

									$not_fill_arr = $tmUsers_model->checkweekdaysdatacron($ls_week, $ls_day, $emp['user_id'],$emp['department_id'],$emp['holiday_group'],$emp_joindate);
									//echo "<pre>";print_r($not_fill_arr);exit;

									if(count($not_fill_arr)>0)
									{
										$pre_fin_dates = $not_fill_arr;
										//echo "<pre>";print_r($pre_fin_dates);

										//echo "<pre>";print_r($fin_dates);
										$fin_dates = $pre_fin_dates; //print_r($fin_dates);
										if(count($fin_dates)>0)
										{
											$this->send_mail_emp($emp['user_id'],$ls_week,$ls_day,$emp['userfullname'],$emp['emailaddress'],$fin_dates,'weekly');
											$emp_arr[$emp['user_id']]=$emp['userfullname'];
										}
									}
								}
								$this->send_mail_manager($emp_arr,$ls_week,$ls_day,$mgr['user_id'],$mgr['userfullname'],$mgr['emailaddress'],'weeklyreminder');
							}

						}
					}else{
						echo "Date doesn't match";
					}

					$cronstatusData = array('cronjob_status' => 'stopped',
										'end_date'=>gmdate("Y-m-d H:i:s")
					);
					$whereCronstatus = array('id=?'=>$insertedCronjobStatusId);

					$updateCronjobStatus = $tm_cronjob_statusModel->saveorUpdateCronjobStatusData($cronstatusData,$whereCronstatus);
				}
				catch(Exception $e)
				{
					print_r($e); die;
				}
			}else{
				echo 'Timemanagement configuration not yet set.';
				
				$cronstatusData = array('cronjob_status' => 'stopped',
										'end_date'=>gmdate("Y-m-d H:i:s")
					);
				$whereCronstatus = array('id=?'=>$insertedCronjobStatusId);

				$updateCronjobStatus = $tm_cronjob_statusModel->saveorUpdateCronjobStatusData($cronstatusData,$whereCronstatus);
			}
		}else{
			echo 'Cron running';
		}
	}

	public function send_mail_emp($empid,$ls_week,$ls_day,$emp_name,$emp_email,$days,$whichremainder,$blockreminderday = '')
	{
		$base_url = 'http://'.$this->getRequest()->getHttpHost() . $this->getRequest()->getBaseUrl();
		$period = $ls_week.' to '.$ls_day;
		$view = $this->getHelper('ViewRenderer')->view;

		$this->view->base_url=$base_url;
		$this->view->emp_name=$emp_name;
		$this->view->days=$days;
		$this->view->period=$period;

		$text = '';
		$mail_type = '';
		if($whichremainder == 'weekly'){
			$text = $view->render('tmmailtemplates/ts_weekly_employee.phtml');
			$mail_type = 'submit_pending';
		}else if($whichremainder == 'monthly'){
			$this->view->blockreminderday = $blockreminderday;
			$text = $view->render('tmmailtemplates/empmonthlyremainder.phtml');
			$mail_type = 'reminder';
		}else if($whichremainder == 'empblock'){
			$text = $view->render('tmmailtemplates/monthlytimesheetstatus.phtml');
			$mail_type = 'block';
		}

		/*Insert data to send mail*/
		$totDays = implode(",", $days);
		$mailslist_data = array('emp_id'=>$empid,
								'mail_type'=>$mail_type,
								'ts_dates'=>$totDays,
		                        'emp_full_name'=>$emp_name,
								'email'=>$emp_email,
								'ts_start_date'=>$ls_week,
								'ts_end_date'=>$ls_day,
								'mail_content'=>$text,
								'is_mail_sent'=>0,
								'created' => gmdate("Y-m-d H:i:s"),
		);

		$mailslist_where = '';

		$tmMail_model = new Timemanagement_Model_Mailslist();
		$id = $tmMail_model->addOrUpdateMailsList($mailslist_data,$mailslist_where);
		/*end*/
	}

	public function send_mail_manager($emp_arr,$ls_week,$ls_day,$mgr_id,$mgr_name,$mgr_mail,$whichremainder)
	{
		$base_url = 'http://'.$this->getRequest()->getHttpHost() . $this->getRequest()->getBaseUrl();

		$emp_names='';
		foreach($emp_arr as $key=>$value)
		{
			$emp_names.=$value."<br/>";
		}
		if(count($emp_arr) > 0)
		{
			$view = $this->getHelper('ViewRenderer')->view;
			$period = $ls_week.' to '.$ls_day;
			$this->view->mgr_name=$mgr_name;
			$this->view->base_url=$base_url;
			$this->view->ls_week=$ls_week;
			$this->view->ls_day=$ls_day;
			$this->view->emp_names=$emp_names;
			$this->view->emp_arr=$emp_arr;
			$this->view->period=$period;



			if($whichremainder == 'weeklyreminder'){
				$mail_type = 'submit_pending';
				$text = $view->render('tmmailtemplates/ts_weekly_manager.phtml');
					
			}else if($whichremainder == 'monthlyblock'){
				$mail_type = 'block';
				$text = $view->render('tmmailtemplates/monthlyemptimesheet.phtml');
			}

			$trDb = Zend_Db_Table::getDefaultAdapter();
			// starting transaction
			$trDb->beginTransaction();
			try
			{
				$mailslist_data = array('emp_id'=>$mgr_id,
								'mail_type'=>$mail_type,
								'ts_dates'=>'',
								'emp_full_name'=>$mgr_name,
								'email'=>$mgr_mail,
								'ts_start_date'=>$ls_week,
								'ts_end_date'=>$ls_day,
								'mail_content'=>$text,
								'is_mail_sent'=>0,
								'created' => gmdate("Y-m-d H:i:s"),
				);

				$mailslist_where = '';

				$tmMail_model = new Timemanagement_Model_Mailslist();
				$id = $tmMail_model->addOrUpdateMailsList($mailslist_data,$mailslist_where);
				//End
				$trDb->commit();
			}catch (Exception $e)
			{
				$trDb->rollBack();
			}
		}
	}

	//Employee Mail remainder for blocking his monthly timesheet.
	public function monthlyempblockremainder($aDate)
	{
		$this->_helper->viewRenderer->setNoRender(true);
		// the above is to stop search for view file
		$this->_helper->layout()->disableLayout();

		$tm_cronjob_statusModel = new Timemanagement_Model_Cronjobstatus;
		$checkAnyCronRunning = $tm_cronjob_statusModel->checkCronRunning();
		if(count($checkAnyCronRunning) == 0){
			$cronstatusData = array('cronjob_status' => 'running',
										'start_date' => gmdate("Y-m-d H:i:s"),
										'end_date'=>gmdate("Y-m-d H:i:s")
			);

			$insertedCronjobStatusId = $tm_cronjob_statusModel->saveorUpdateCronjobStatusData($cronstatusData,'');

			$tmconf_model = new Timemanagement_Model_Configuration();
			$getTmConfig = $tmconf_model->getActiveRecord();
			$blockReminderConfigDay ='';

			if(!empty($getTmConfig)){

				if(trim($getTmConfig[0]['ts_block_dates_range']) == '1-31'){
					$blockReminderConfigDay = 2;
				}else if(trim($getTmConfig[0]['ts_block_dates_range']) == '26-25'){
					$blockReminderConfigDay = 27;
				}

				if(strlen($blockReminderConfigDay) == 1){
					$blockReminderConfigDay = '0'.$blockReminderConfigDay;
				}

				//$a_date = '2015-07-27';
				 $a_date=($aDate!="")?$aDate:date('Y-m-d');
				//$a_date = $this->_getParam('date');
				$date = new DateTime($a_date);
				//echo $date->format('d').'-----'.$blockReminderConfigDay.'------';exit;
				if($date->format('d') == $blockReminderConfigDay){
					$tmUsers_model=new Timemanagement_Model_Users();
					$mgr_list=$tmUsers_model->getManagers();

					$today = new DateTime($a_date);
					$cron_run_date =  $today->format('Y').'-'.$today->format('m').'-'.$blockReminderConfigDay;
					$cron_run_date = strtotime($cron_run_date);
					$cron_run_date = date('Y-m-d',$cron_run_date);

					/*$tdate = new DateTime(date('Y-m-d'));
					 $ctdate = new DateTime(date('Y-m-d'));*/

					$tdate = new DateTime($a_date);
					$ctdate = new DateTime($a_date);

					if($blockReminderConfigDay == 2){
						//First day of the previous month:
						$tdate->sub(new DateInterval('P1M'));
						$firstday_prev_month =$tdate->format('Y-m-01');
						$hidstartweek_date = $firstday_prev_month;

						//Last day of the previous month:
						$lastday_prev_month =  $tdate->format('Y').'-'.$tdate->format('m').'-31';
						$lastday_prev_month = strtotime($lastday_prev_month);
						$lastday_prev_month = date('Y-m-d',$lastday_prev_month);
						$hidendweek_date = $lastday_prev_month;

						$lastday_prev_month = new DateTime(date($lastday_prev_month));
						if($lastday_prev_month->format('m') != 31){
							$hidendweek_date = date("Y-m-t", strtotime($tdate->format('Y').'-'.$tdate->format('m').'-01'));
						}

						$month_name = date("F, Y", strtotime($hidendweek_date));
					}else if($blockReminderConfigDay == 27){
						//First day of the previous month:
						$tdate->sub(new DateInterval('P1M'));
						$firstday_prev_month =$tdate->format('Y-m-26');
						$hidstartweek_date = $firstday_prev_month;

						//Last day of the previous month:
						$lastday_prev_month = $ctdate->format('Y-m-25');
						$hidendweek_date = $lastday_prev_month;
						$month_name = date("F, Y", strtotime($hidendweek_date));
					}

					//echo 'TM_BLOCK_START_DAY---'.TM_BLOCK_START_DAY.'---startdate----'.$hidstartweek_date;echo '<br>';
					//echo 'TM_BLOCK_END_DAY---'.TM_BLOCK_END_DAY.'---enddate----'.$hidendweek_date;echo '<br>';

					$month_name = date("F, Y", strtotime($hidendweek_date));

					// managers loop
					if(count($mgr_list) > 0){
						foreach($mgr_list as $mgr)
						{
							$emp_data = $tmUsers_model->getEmployeesReportingTo($mgr['user_id'],$cron_run_date);

							$emp_arr=array();
							if(count($emp_data)>0){
								foreach($emp_data as $emp)
								{
									$emp_joindate = new DateTime($emp['date_of_joining']);
									$emp_joindate = $emp_joindate->format('Y-m-d');

									$not_fill_arr = $tmUsers_model->checkweekdaysdatacron($hidstartweek_date, $hidendweek_date, $emp['user_id'],$emp['department_id'],$emp['holiday_group'],$emp_joindate);
									//echo "<pre>";print_r($not_fill_arr);exit;

									if(count($not_fill_arr)>0)
									{
										$this->send_mail_emp($emp['user_id'],$hidstartweek_date,$hidendweek_date,$emp['userfullname'],$emp['emailaddress'],$not_fill_arr,'monthly',$blockReminderConfigDay);
										$emp_arr[$emp['user_id']]=$emp['userfullname'];
									}
								}
							}
						}
					}

					$cronstatusData = array('cronjob_status' => 'stopped',
										'end_date'=>gmdate("Y-m-d H:i:s")
					);
					$whereCronstatus = array('id=?'=>$insertedCronjobStatusId);

					$updateCronjobStatus = $tm_cronjob_statusModel->saveorUpdateCronjobStatusData($cronstatusData,$whereCronstatus);
				}else{
					echo "Date doesn't match";
					
					$cronstatusData = array('cronjob_status' => 'stopped',
										'end_date'=>gmdate("Y-m-d H:i:s")
					);
					$whereCronstatus = array('id=?'=>$insertedCronjobStatusId);

					$updateCronjobStatus = $tm_cronjob_statusModel->saveorUpdateCronjobStatusData($cronstatusData,$whereCronstatus);
				}
			}else{
				echo 'Timemanagement configuration not yet set.';
				
				$cronstatusData = array('cronjob_status' => 'stopped',
										'end_date'=>gmdate("Y-m-d H:i:s")
					);
				$whereCronstatus = array('id=?'=>$insertedCronjobStatusId);

				$updateCronjobStatus = $tm_cronjob_statusModel->saveorUpdateCronjobStatusData($cronstatusData,$whereCronstatus);
			}
		}else{
			echo 'Some cron running';
		}
	}
	//End


	//Block timesheet monthly and send a mails.
	public function monthlyblockedemp($aDate)
	{

		$this->_helper->viewRenderer->setNoRender(true);
		// the above is to stop search for view file
		$this->_helper->layout()->disableLayout();

		$tmconf_model = new Timemanagement_Model_Configuration();
		$getTmConfig = $tmconf_model->getActiveRecord();
		$blockConfigDay = '';

		$tm_cronjob_statusModel = new Timemanagement_Model_Cronjobstatus;
		$checkAnyCronRunning = $tm_cronjob_statusModel->checkCronRunning();
		if(count($checkAnyCronRunning) == 0){
			$cronstatusData = array('cronjob_status' => 'running',
									'start_date' => gmdate("Y-m-d H:i:s"),
									'end_date'=>gmdate("Y-m-d H:i:s")
			);

			$insertedCronjobStatusId = $tm_cronjob_statusModel->saveorUpdateCronjobStatusData($cronstatusData,'');

			if(!empty($getTmConfig)){

				if(trim($getTmConfig[0]['ts_block_dates_range']) == '1-31'){
					$blockConfigDay = 3;
				}else if(trim($getTmConfig[0]['ts_block_dates_range']) == '26-25'){
					$blockConfigDay = 28;
				}

				if(strlen($blockConfigDay) == 1){
					$blockConfigDay = '0'.$blockConfigDay;
				}

				//$a_date = '2015-07-28';
				  $a_date=($aDate!="")?$aDate:date('Y-m-d');
				//$a_date = $this->_getParam('date');
				$date = new DateTime($a_date);
				//echo $date->format('d').'-----'.$blockConfigDay.'------';
				if($date->format('d') == $blockConfigDay){
					$tmUsers_model=new Timemanagement_Model_Users();
					$mgr_list=$tmUsers_model->getManagers();
					//$lead_list=$tmUsers_model->getEmployees('lead');//echo '<pre>'; print_r($lead_list);exit;

					$today = new DateTime($a_date);
					$cron_run_date =  $today->format('Y').'-'.$today->format('m').'-'.$blockConfigDay;
					$cron_run_date = strtotime($cron_run_date);
					$cron_run_date = date('Y-m-d',$cron_run_date);

					/*$tdate = new DateTime(date('Y-m-d'));
					 $ctdate = new DateTime(date('Y-m-d'));*/

					$tdate = new DateTime($a_date);
					$ctdate = new DateTime($a_date);

					if($blockConfigDay == 3){
						//First day of the previous month:
						$tdate->sub(new DateInterval('P1M'));
						$firstday_prev_month =$tdate->format('Y-m-01');
						$hidstartweek_date = $firstday_prev_month;

						//Last day of the previous month:
						$lastday_prev_month =  $tdate->format('Y').'-'.$tdate->format('m').'-31';
						$lastday_prev_month = strtotime($lastday_prev_month);
						$lastday_prev_month = date('Y-m-d',$lastday_prev_month);
						$hidendweek_date = $lastday_prev_month;

						$lastday_prev_month = new DateTime(date($lastday_prev_month));
						if($lastday_prev_month->format('m') != 31){
							$hidendweek_date = date("Y-m-t", strtotime($tdate->format('Y').'-'.$tdate->format('m').'-01'));
						}

						$month_name = date("F, Y", strtotime($hidendweek_date));
					}else if($blockConfigDay == 28){
						//First day of the previous month:
						$tdate->sub(new DateInterval('P1M'));
						$firstday_prev_month =$tdate->format('Y-m-26');
						$hidstartweek_date = $firstday_prev_month;

						//Last day of the previous month:
						$lastday_prev_month = $ctdate->format('Y-m-25');
						$hidendweek_date = $lastday_prev_month;
						$month_name = date("F, Y", strtotime($hidendweek_date));
					}

					//echo 'TM_BLOCK_START_DAY---'.TM_BLOCK_START_DAY.'---startdate----'.$hidstartweek_date;echo '<br>';
					//echo 'TM_BLOCK_END_DAY---'.TM_BLOCK_END_DAY.'---enddate----'.$hidendweek_date;echo '<br>';exit;

					$month_name = date("F, Y", strtotime($hidendweek_date));

					// managers loop
					$atleastOneEmpBlocked = false;
				    if(count($mgr_list) > 0){
						foreach($mgr_list as $mgr)
						{
							$emp_data = $tmUsers_model->getEmployeesReportingTo($mgr['user_id'],$cron_run_date);

							$emp_arr=array();
							if(count($emp_data) > 0){
								foreach($emp_data as $emp)
								{
									$emp_joindate = new DateTime($emp['date_of_joining']);
									$emp_joindate = $emp_joindate->format('Y-m-d');

									$not_fill_arr = $tmUsers_model->checkweekdaysdatacron($hidstartweek_date, $hidendweek_date, $emp['user_id'],$emp['department_id'],$emp['holiday_group'],$emp_joindate);
									//echo "<pre>";print_r($not_fill_arr);exit;

									if(count($not_fill_arr)>0)
									{
                                        $atleastOneEmpBlocked = true;
										$this->notfilledDays($emp['user_id'],$not_fill_arr,$blockConfigDay);
										$this->send_mail_emp($emp['user_id'],$hidstartweek_date,$hidendweek_date,$emp['userfullname'],$emp['emailaddress'],$not_fill_arr,'empblock');
										$emp_arr[$emp['user_id']]=$emp['userfullname'];
									}
								}
								$this->send_mail_manager($emp_arr,$hidstartweek_date,$hidendweek_date,$mgr['user_id'],$mgr['userfullname'],$mgr['emailaddress'],'monthlyblock');
							}
						}
					}

					/*insert record in mail_list table for front end use*/
					if(!$atleastOneEmpBlocked){

						$mailslistdummy_data = array('emp_id'=> NULL,
								'mail_type'=>'block',
								'ts_dates'=> NULL,
								'emp_full_name'=> NULL,
								'email'=> NULL,
								'ts_start_date'=>$hidstartweek_date,
								'ts_end_date'=>$hidendweek_date,
								'mail_content'=> NULL,
								'is_mail_sent'=>1,
								'created' => gmdate("Y-m-d H:i:s"),
						);

						$mailslist_where = '';
						
						$tmMail_model = new Timemanagement_Model_Mailslist();
						$id = $tmMail_model->addOrUpdateMailsList($mailslistdummy_data,$mailslist_where);
					}
						/*end insert record in mail_list table for front end use*/

					// cronstatus update
					$cronstatusData = array('cronjob_status' => 'stopped',
										'end_date'=>gmdate("Y-m-d H:i:s")
					);
					$whereCronstatus = array('id=?'=>$insertedCronjobStatusId);

					$updateCronjobStatus = $tm_cronjob_statusModel->saveorUpdateCronjobStatusData($cronstatusData,$whereCronstatus);
					//end cronstatus update
				}else{
					echo "Date doesn't match";					
					
					// cronstatus update
					$cronstatusData = array('cronjob_status' => 'stopped',
										'end_date'=>gmdate("Y-m-d H:i:s")
					);
					$whereCronstatus = array('id=?'=>$insertedCronjobStatusId);

					$updateCronjobStatus = $tm_cronjob_statusModel->saveorUpdateCronjobStatusData($cronstatusData,$whereCronstatus);
					//end cronstatus update
				}
			}else{
				echo 'Timemanagement configuration not yet set.';
				
				
				// cronstatus update
				$cronstatusData = array('cronjob_status' => 'stopped',
									'end_date'=>gmdate("Y-m-d H:i:s")
				);
				$whereCronstatus = array('id=?'=>$insertedCronjobStatusId);

				$updateCronjobStatus = $tm_cronjob_statusModel->saveorUpdateCronjobStatusData($cronstatusData,$whereCronstatus);
				//end cronstatus update
			}
		}else{
			echo 'Some cron running';
		}
	}
	//End

	//child function to block
	function notfilledDays($user_id,$fin_dates,$blockConfigDay){
		$monthCalWeek = array(); //cal_weeks in a month for notfilleddates
		$monthCalWeekDates = array();

		foreach($fin_dates as $notFilledDate){
			//echo $notFilledDate.'<br>';
			$cal_week = strftime('%U',strtotime($notFilledDate));
			if(!in_array($cal_week,$monthCalWeek)){
				$monthCalWeek[] = $cal_week;
			}
			/* $monthCalWeekDates(create individual cal_week dates array), This array not for specific month week */
			$monthCalWeekDates[$cal_week][] = $notFilledDate;
		}

		if(count($monthCalWeek) > 0){
			foreach($monthCalWeek as $key => $calWeekVal){
				if(count($monthCalWeekDates[$calWeekVal])){
					$calWeekDate = $monthCalWeekDates[$calWeekVal][0];
					$ts_model = new Timemanagement_Model_Timesheetstatus();

					//get start date of the week
					$getDayString = strftime('%A',strtotime($calWeekDate));
					if($getDayString == 'Sunday'){
						$startDateOfWeek = date('F d, Y', strtotime($calWeekDate));
					}else{
						$startDateOfWeek = date('F d, Y', strtotime('last sunday', strtotime($calWeekDate)));
					}

					$YearFromStartDate = strftime('%Y',strtotime($startDateOfWeek));

					$weekDates = array(date('Y-m-d', strtotime($startDateOfWeek)),
					date('Y-m-d', strtotime($startDateOfWeek . " +1 days")),
					date('Y-m-d', strtotime($startDateOfWeek . " +2 days")),
					date('Y-m-d', strtotime($startDateOfWeek . " +3 days")),
					date('Y-m-d', strtotime($startDateOfWeek . " +4 days")),
					date('Y-m-d', strtotime($startDateOfWeek . " +5 days")),
					date('Y-m-d', strtotime($startDateOfWeek . " +6 days")),
					);

					$loopDate = $weekDates[0];

					$yearCalWeekArray = array();
					$monthCalWeekArray = $monthWeekCalDatesArray = array();

					while (strtotime($loopDate) <= strtotime($weekDates[6])) {

						$dateYearVal = strftime('%Y',strtotime($loopDate));
						$dateMonthVal = (int)strftime('%m',strtotime($loopDate));

						$yearCalWeekArray[] = $dateYearVal;
						$monthCalWeekArray[] = (int)$dateMonthVal;
						$monthWeekCalDatesArray[$dateMonthVal][] = $loopDate;

						if(!in_array($loopDate,$monthWeekCalDatesArray[$dateMonthVal])){
							$monthCalWeekArray[$dateMonthVal][] = $loopDate;
						}
						$loopDate = date ("Y-m-d", strtotime("+1 day", strtotime($loopDate)));
					}

					$yearCalWeekArray = array_unique($yearCalWeekArray);
					$monthCalWeekArray = array_unique($monthCalWeekArray);
					//echo '<pre>';
					//print_r($monthWeekCalDatesArray); exit;
					foreach($monthCalWeekArray as $monthnum){
						$monthCalFirstWeek = date("Y-m-d", strtotime(strftime('%Y',strtotime($monthWeekCalDatesArray[$monthnum][0])).'-'.strftime('%m',strtotime($monthWeekCalDatesArray[$monthnum][0])).'-01'));
						$monthCalFirstWeek = strftime('%U',strtotime($monthCalFirstWeek));

						$year = strftime('%Y',strtotime($monthWeekCalDatesArray[$monthnum][0]));
						$week = ($calWeekVal-$monthCalFirstWeek) + 1;
						$monthnum = (int)$monthnum;
						$empTsDataByDate = $ts_model->getTsRecordExists($user_id,$year,$monthnum,$week,$calWeekVal);//print_r($empTsDataByDate);exit;

						$weekTableDateKeys = array('sun_date','mon_date','tue_date','wed_date','thu_date','fri_date','sat_date');
						$weekTableStatusKeys = array('sun_status','mon_status','tue_status','wed_status','thu_status','fri_status','sat_status');
						$projWeekTableStatusKeys = array('sun_project_status','mon_project_status','tue_project_status','wed_project_status','thu_project_status','fri_project_status','sat_project_status');

						if(count($empTsDataByDate) == 0){
							$insertData = array('emp_id'=>$user_id,
											 'ts_year'=>$year,
											 'ts_month' => $monthnum,
											 'ts_week' => $week,
											 'cal_week'=>$calWeekVal,
											 'is_active' => 1,
						                     'created_by' => 1,
											 'created' => gmdate("Y-m-d H:i:s"),
											 'modified' => gmdate("Y-m-d H:i:s"),
							);

							$tableStatusArray = array();
							$blockedFlag = false;
							$projStatusArray = array();
							foreach($weekTableDateKeys as $key => $tableDate){
								$projStatusArray[$projWeekTableStatusKeys[$key]] = 'no_entry';
								$insertData[$tableDate] = $weekDates[$key];

								if(in_array($weekDates[$key],$monthCalWeekDates[$calWeekVal])){
									if(in_array($weekDates[$key],$monthWeekCalDatesArray[$monthnum])){ 
										$blockedFlag = true;
										$tableStatusArray[$weekTableStatusKeys[$key]] = 'blocked';
									}else{
										$tableStatusArray[$weekTableStatusKeys[$key]] = 'no_entry';
									}
								}else{
									$tableStatusArray[$weekTableStatusKeys[$key]] = 'no_entry';
								}
							}

							// row not exists in tbl_ts_status, insert in 3 tables(tbl_ts_status,tm_emp_timesheets,tm_emp_ts_notes)
							$insertedEmpTsid = $ts_model->SaveEmpTsData($insertData);
							$insertedEmpTsNoesid = $ts_model->SaveEmpTsNotesData($insertData);

							// add day status and week status to inserted array
							if($blockedFlag){
								$week_status = array('week_status' => 'blocked');
							}else{
								$week_status = array('week_status' => 'no_entry');
							}

							$tsinsertData = $insertData + $tableStatusArray+ $week_status+$projStatusArray;
							$insertedTsid = $ts_model->SaveTsData($tsinsertData);

						}else{ // update tbl_ts_status table for not not filled dates
							$updateData = array();
							$blockedFlag = false;
							foreach($weekTableDateKeys as $key => $tableDate){
								if(in_array($weekDates[$key],$monthCalWeekDates[$calWeekVal])){
									if(in_array($weekDates[$key],$monthWeekCalDatesArray[$monthnum])){ 
									    $blockedFlag = true;
										$updateData[$weekTableStatusKeys[$key]] = 'blocked';
									}else{
										$updateData[$weekTableStatusKeys[$key]]  = 'no_entry';
									}
								}
							}
							
							// add day status and week status to inserted array
							if($blockedFlag){
								$week_status_text = 'blocked';
							}else{
								$week_status_text = 'no_entry';
							}
							
							$updateData = $updateData + array('week_status' => $week_status_text,'modified' => gmdate("Y-m-d H:i:s"));
							$where['emp_id = ?'] = $user_id;
							$where['ts_year = ?'] = $year;
							$where['ts_month = ?'] = $monthnum;
							$where['ts_week = ?'] = $week;
							$where['cal_week = ?'] = $calWeekVal;

							$updateTs = $ts_model->updateTsData($updateData,$where);
						}
					}
				}//end if(dates count in a week)
			}//end foreach $monthCalWeek Array
		}//end if $monthCalWeek count > 0
	}


	//cron to send timemanagement mails from tm_mailing_list
	function tmmailscron()
	{
		try{
			$tmMail_model = new Timemanagement_Model_Mailslist();
			$mails_data =  $tmMail_model->getPendingMailsData();
			//echo "<pre>";print_r($mails_data);exit;
			if(count($mails_data) > 0)
			{
				foreach($mails_data as $mail)
				{
					if($mail['mail_type'] == 'submit_pending'){
						$options['subject'] = APPLICATION_NAME.': Pending Submission';
						$options['header'] = 'Pending Submission';
					}else if($mail['mail_type'] == 'reminder'){
						$options['subject'] = $options['subject'] = APPLICATION_NAME.': Timesheet Pending Reminder';
						$options['header'] = 'Timesheet Pending Reminder';
					}else if($mail['mail_type'] == 'block'){
						if(trim($mail['ts_dates']) == ''){
							$options['subject'] = APPLICATION_NAME.': Employee Timesheet Blocked';
							$options['header'] = 'Notification';
						}else if(trim($mail['ts_dates']) != ''){
							$options['subject'] = APPLICATION_NAME.': Timesheet Blocked';
							$options['header'] = 'Notification';
						}
					}

					$options['toEmail'] = $mail['email'];
					$options['toName'] = $mail['emp_full_name'];
					$options['message'] = $mail['mail_content'];

					$mail_status = sapp_Mail::_email($options);

					$mail_where = array('id=?' => $mail['id']);
					$new_maildata['is_mail_sent'] = 1;
					if($mail_status === true)
					{
						//to udpate email table that mail is sent.
						$id = $tmMail_model->addOrUpdateMailsList($new_maildata,$mail_where);
					}
				}
			}
			else
			{
				echo "No any pending mails";
			}
		}
		catch(Exception $e){
			echo $e->getMessage();
		}
	}
	/*****END Timemanagememt crons ********/

}

