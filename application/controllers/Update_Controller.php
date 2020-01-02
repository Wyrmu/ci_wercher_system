<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Update_Controller extends CI_Controller {


	public function __construct() {
		parent::__construct();

		$this->load->model('Model_Selects');
		$this->load->model('Model_Inserts');
		$this->load->model('Model_Deletes');
		$this->load->model('Model_Updates');
	}
	public function EmployApplicant()
	{
		if (isset($_POST['ApplicantID'])) {
			$ApplicantID = $this->input->post('ApplicantID',FALSE); // TODO: (Dec 12, 2019) Changed from TRUE to FALSE > No XSS filtering.
			$ClientID = $this->input->post('ClientID',TRUE);
			$H_Days = $this->input->post('H_Days',TRUE);
			$H_Months = $this->input->post('H_Months',TRUE);
			$H_Years = $this->input->post('H_Years',TRUE);
			if($H_Days == NULL) {
				$H_Days = 0;
			}
			if($H_Months == NULL) {
				$H_Months = 0;
			}
			if($H_Years == NULL) {
				$H_Years = 0;
			}
			$Temp_ApplicantID = $ApplicantID;
			$Temp_ApplicantID++;

			if ($ApplicantID == NULL || $ClientID == NULL) {
				$this->session->set_flashdata('prompts','<div class="text-center" style="width: 100%;padding: 21px; color: #F52F2F;"><h5><i class="fas fa-times"></i> Something\'s wrong, Please try again! (Error: Missing Field/s) A:' . $ApplicantID . ' C:' . $ClientID .' D:' . $H_Days . ' H:' . $H_Months . ' Y:' . $H_Years . ' </h5></div>');
				redirect($_SERVER['HTTP_REFERER']);
			}
			else
			{
				$CheckApplicant = $this->Model_Selects->CheckApplicant($ApplicantID);
				if ($CheckApplicant->num_rows() > 0) {
					$row = $CheckApplicant->row_array();

					date_default_timezone_set('Asia/Manila');

					$DateStarted = date('Y-m-d h:i:s A');

					if ($H_Months == NULL) {
						$DateEnds = date('Y-m-d h:i:s A', strtotime('+0 months', strtotime($DateStarted)));
					} else {
						$DateEnds = date('Y-m-d h:i:s A', strtotime('+'.$H_Months.' months', strtotime($DateStarted)));
					}
					if ($H_Days == NULL) {
						$DateEnds = date('Y-m-d h:i:s A', strtotime('+0 days', strtotime($DateEnds)));
					} else {
						$DateEnds = date('Y-m-d h:i:s A', strtotime('+'.$H_Days.' days', strtotime($DateEnds)));
					}
					if ($H_Years == NULL) {
						$DateEnds = date('Y-m-d h:i:s A', strtotime('+0 days', strtotime($DateEnds)));
					} else {
						$DateEnds = date('Y-m-d h:i:s A', strtotime('+'.$H_Years.' years', strtotime($DateEnds)));
					}

					$data = array(
						'ClientEmployed' => $ClientID,
						'DateStarted' => $DateStarted,
						'DateEnds' => $DateEnds,
					);
					$EmployNewApplicant = $this->Model_Updates->EmployNewApplicant($Temp_ApplicantID,$ApplicantID,$data);
					$data = array(
						'ClientID' => $ClientID,
						'FirstName' => $row['FirstName'],
						'MiddleInitial' => $row['MiddleInitial'],
						'LastName' => $row['LastName'],
						'SalaryExpected' => $row['SalaryExpected'],
					);
					$EmployNewApplicant = $this->Model_Inserts->InsertToClient($ClientID,$Temp_ApplicantID,$data);
					if ($EmployNewApplicant == TRUE) {
						$this->session->set_flashdata('prompts','<div class="text-center" style="width: 100%;padding: 21px; color: #45C830;"><h5><i class="fas fa-check"></i> Applicant employed!</h5></div>');
						// LOGBOOK
						date_default_timezone_set('Asia/Manila');
						$LogbookCurrentTime = date('Y-m-d h:i:s A');
						$LogbookType = 'Employment';
						$LogbookEvent = 'Applicant ID ' . $Temp_ApplicantID .' has been employed to Client ID ' . $ClientID . ' for ';
						if($H_Years != 0) {
							$LogbookEvent = $LogbookEvent . $H_Years;
							if($H_Years == 1) {
								$LogbookEvent = $LogbookEvent . ' year, ';
							} else {
								$LogbookEvent = $LogbookEvent . ' years, ';
							}
						}
						if($H_Months != 0) {
							$LogbookEvent = $LogbookEvent . $H_Months;
							if($H_Months == 1) {
								$LogbookEvent = $LogbookEvent . ' month, ';
							} else {
								$LogbookEvent = $LogbookEvent . ' months, ';
							}
						}
						if($H_Days != 0) {
							$LogbookEvent = $LogbookEvent . $H_Days;
							if($H_Days == 1) {
								$LogbookEvent = $LogbookEvent . ' day, ';
							} else {
								$LogbookEvent = $LogbookEvent . ' days, ';
							}
						}
						$LogbookEvent = substr($LogbookEvent, 0, -2) . '!';
						$LogbookLink = base_url() . 'ViewEmployee?id=' . $Temp_ApplicantID;
						$data = array(
							'Time' => $LogbookCurrentTime,
							'Type' => $LogbookType,
							'Event' => $LogbookEvent,
							'Link' => $LogbookLink,
						);
						$LogbookInsert = $this->Model_Inserts->InsertLogbook($data);
						redirect($_SERVER['HTTP_REFERER']);
					}
					else
					{
						$this->session->set_flashdata('prompts','<div class="text-center" style="width: 100%;padding: 21px; color: #F52F2F;"><h5><i class="fas fa-times"></i> Something\'s wrong, Please try agains!</h5></div>');
						redirect($_SERVER['HTTP_REFERER']);
					}
				}
				else
				{
					$this->session->set_flashdata('prompts','<div class="text-center" style="width: 100%;padding: 21px; color: #F52F2F;"><h5><i class="fas fa-times"></i> Something\'s wrong, Please try againss!</h5></div>');
					redirect($_SERVER['HTTP_REFERER']);
				}
			}
		}
		else
		{
			$this->session->set_flashdata('prompts','<div class="text-center" style="width: 100%;padding: 21px; color: #F52F2F;"><h5><i class="fas fa-times"></i> Something\'s wrong, Please try againsss!</h5></div>');
			redirect($_SERVER['HTTP_REFERER']);
		}
	}
	public function ExtendContract()
	{
		if (isset($_POST['ApplicantID'])) {
			$ApplicantID = $this->input->post('ApplicantID',FALSE); // TODO: (Dec 12, 2019) Changed from TRUE to FALSE > No XSS filtering.
			$E_CurrentDate = $this->input->post('E_CurrentDate',TRUE);
			$E_Days = $this->input->post('E_Days',TRUE);
			$E_Months = $this->input->post('E_Months',TRUE);
			$E_Years = $this->input->post('E_Years',TRUE);

			if ($ApplicantID == NULL) {
				$this->session->set_flashdata('prompts','<div class="text-center" style="width: 100%;padding: 21px; color: #F52F2F;"><h5><i class="fas fa-times"></i> Something\'s wrong, Please try again! (Error: Extend Contract) A:' . $ApplicantID . ' D:' . $E_Days . ' H:' . $E_Months . ' Y:' . $E_Years . ' </h5></div>');
				redirect($_SERVER['HTTP_REFERER']);
			}
			else
			{
				$CheckApplicant = $this->Model_Selects->CheckApplicant($ApplicantID);
				if ($CheckApplicant->num_rows() > 0) {

					date_default_timezone_set('Asia/Manila');

					if ($E_Months == NULL) {
						$DateEnds = date('Y-m-d h:i:s A', strtotime('+0 months', strtotime($E_CurrentDate)));
					} else {
						$DateEnds = date('Y-m-d h:i:s A', strtotime('+'.$E_Months.' months', strtotime($E_CurrentDate)));
					}
					if ($E_Days == NULL) {
						$DateEnds = date('Y-m-d h:i:s A', strtotime('+0 days', strtotime($DateEnds)));
					} else {
						$DateEnds = date('Y-m-d h:i:s A', strtotime('+'.$E_Days.' days', strtotime($DateEnds)));
					}
					if ($E_Years == NULL) {
						$DateEnds = date('Y-m-d h:i:s A', strtotime('+0 days', strtotime($DateEnds)));
					} else {
						$DateEnds = date('Y-m-d h:i:s A', strtotime('+'.$E_Years.' years', strtotime($DateEnds)));
					}

					$data = array(
						'DateEnds' => $DateEnds,
					);
					$EmployNewApplicant = $this->Model_Updates->ExtendContract($ApplicantID,$data);
					if ($EmployNewApplicant == TRUE) {
						$this->session->set_flashdata('prompts','<div class="text-center" style="width: 100%;padding: 21px; color: #45C830;"><h5><i class="fas fa-check"></i> Contract Extended to ' . $DateEnds . '!</h5></div>');
						// LOGBOOK
						date_default_timezone_set('Asia/Manila');
						$LogbookCurrentTime = date('Y-m-d h:i:s A');
						$LogbookType = 'Update';
						$LogbookEvent = 'Applicant ID ' . $ApplicantID .' has their contract extended by ';
						if($E_Years != 0) {
							$LogbookEvent = $LogbookEvent . $E_Years;
							if($E_Years == 1) {
								$LogbookEvent = $LogbookEvent . ' year, ';
							} else {
								$LogbookEvent = $LogbookEvent . ' years, ';
							}
						}
						if($E_Months != 0) {
							$LogbookEvent = $LogbookEvent . $E_Months;
							if($E_Months == 1) {
								$LogbookEvent = $LogbookEvent . ' month, ';
							} else {
								$LogbookEvent = $LogbookEvent . ' months, ';
							}
						}
						if($E_Days != 0) {
							$LogbookEvent = $LogbookEvent . $E_Days;
							if($E_Days == 1) {
								$LogbookEvent = $LogbookEvent . ' day, ';
							} else {
								$LogbookEvent = $LogbookEvent . ' days, ';
							}
						}
						$LogbookEvent = substr($LogbookEvent, 0, -2) . '!';
						$LogbookLink = base_url() . 'ViewEmployee?id=' . $ApplicantID;
						$data = array(
							'Time' => $LogbookCurrentTime,
							'Type' => $LogbookType,
							'Event' => $LogbookEvent,
							'Link' => $LogbookLink,
						);
						$LogbookInsert = $this->Model_Inserts->InsertLogbook($data);
						redirect($_SERVER['HTTP_REFERER']);
					}
					else
					{
						$this->session->set_flashdata('prompts','<div class="text-center" style="width: 100%;padding: 21px; color: #F52F2F;"><h5><i class="fas fa-times"></i> Something\'s wrong, Please try agains!</h5></div>');
						redirect($_SERVER['HTTP_REFERER']);
					}
				}
				else
				{
					$this->session->set_flashdata('prompts','<div class="text-center" style="width: 100%;padding: 21px; color: #F52F2F;"><h5><i class="fas fa-times"></i> Something\'s wrong, Please try againss!</h5></div>');
					redirect($_SERVER['HTTP_REFERER']);
				}
			}
		}
		else
		{
			$this->session->set_flashdata('prompts','<div class="text-center" style="width: 100%;padding: 21px; color: #F52F2F;"><h5><i class="fas fa-times"></i> Something\'s wrong, Please try againsss!</h5></div>');
			redirect($_SERVER['HTTP_REFERER']);
		}
	}
	public function UpdateEmployee()
	{
		$ApplicantID = $this->input->post('M_ApplicantID');
		$pImage = $this->input->post('M_ApplicantImage');
		# PERSONAL INFORMATION
		$PositionDesired = $this->input->post('PositionDesired');
		$PositionGroup = $this->input->post('PositionGroup');
		$SalaryExpected = $this->input->post('SalaryExpected');
		$LastName = $this->input->post('LastName');
		$FirstName = $this->input->post('FirstName');
		$MI = $this->input->post('MI');
		$Gender = $this->input->post('Gender');
		$Age = $this->input->post('Age');
		$Height = $this->input->post('Height');
		$Weight = $this->input->post('Weight');
		$Religion = $this->input->post('Religion');
		
		$bDate = $this->input->post('bDate');
		$bPlace = $this->input->post('bPlace');
		$Citizenship = $this->input->post('Citizenship');
		$CivilStatus = $this->input->post('CivilStatus');
		$No_Children = $this->input->post('No_Children');
		$PhoneNumber = $this->input->post('PhoneNumber');
		# DOCUMENTS
		$SSS = $this->input->post('SSS');
		$SSS_Effective = $this->input->post('SSS_Effective');
		$RCN = $this->input->post('RCN');
		$RCN_at = $this->input->post('RCN_at');
		$RCN_On = $this->input->post('RCN_On');
		$TIN = $this->input->post('TIN');
		$TIN_At = $this->input->post('TIN_At');
		$TIN_On = $this->input->post('TIN_On');

		$HDMF = $this->input->post('HDMF');
		$HDMF_At = $this->input->post('HDMF_At');
		$HDMF_On = $this->input->post('HDMF_On');

		$PhilHealth = $this->input->post('PhilHealth');
		$PhilHealth_At = $this->input->post('PhilHealth_At');
		$PhilHealth_On = $this->input->post('PhilHealth_On');
		$ATM_No = $this->input->post('ATM_No');

		# ADDRESSES
		$Address_Present = $this->input->post('Address_Present');
		$Address_Provincial = $this->input->post('Address_Provincial');
		$Address_Manila = $this->input->post('Address_Manila');

		if ($PositionDesired == NULL || $SalaryExpected == NULL || $LastName == NULL || $FirstName == NULL || $MI == NULL || $Gender == NULL || $Age == NULL || $Height == NULL || $Weight == NULL || $Religion == NULL || $bDate == NULL || $bPlace == NULL || $Citizenship == NULL || $CivilStatus == NULL || $No_Children == NULL || $PhoneNumber == NULL || $SSS == NULL || $SSS_Effective == NULL || $RCN == NULL || $RCN_at == NULL || $RCN_On == NULL || $TIN == NULL || $TIN_At == NULL || $TIN_On == NULL || $HDMF == NULL || $HDMF_At == NULL || $HDMF_On == NULL || $Address_Present == NULL) {
			$this->session->set_flashdata('prompts','<div class="text-center" style="width: 100%;padding: 21px; color: #F52F2F;"><h5><i class="fas fa-times"></i> All fields are required!</h5></div>');
			$data = array(
				'PositionDesired' => $PositionDesired,
				'PositionGroup' => $PositionGroup,
				'SalaryExpected' => $SalaryExpected,
				'LastName' => $LastName,
				'FirstName' => $FirstName,
				'MI' => $MI,
				'Gender' => $Gender,
				'Age' => $Age,
				'Height' => $Height,
				'Weight' => $Weight,
				'Religion' => $Religion,
				'bDate' => $bDate,
				'bPlace' => $bPlace,
				'Citizenship' => $Citizenship,
				'CivilStatus' => $CivilStatus,
				'No_Children' => $No_Children,
				'PhoneNumber' => $PhoneNumber,
				'SSS' => $SSS,
				'SSS_Effective' => $SSS_Effective,
				'RCN' => $RCN,
				'RCN_at' => $RCN_at,
				'RCN_On' => $RCN_On,
				'TIN' => $TIN,
				'TIN_At' => $TIN_At,
				'TIN_On' => $TIN_On,

				'HDMF' => $HDMF,
				'HDMF_At' => $HDMF_At,
				'HDMF_On' => $HDMF_On,
				'ATM_No' => $ATM_No,

				'PhilHealth' => $PhilHealth,
				'PhilHealth_At' => $PhilHealth_At,
				'PhilHealth_On' => $PhilHealth_On,
				


				'Address_Present' => $Address_Present,
				'Address_Provincial' => $Address_Provincial,
				'Address_Manila' => $Address_Manila,
			);
			$this->session->set_flashdata($data);
			redirect($_SERVER['HTTP_REFERER']);
		}
		else
		{
				$config['upload_path']          = './uploads/'.$ApplicantID;
				$config['allowed_types']        = 'gif|jpg|png';
				$config['max_size']             = 2000;
				$config['max_width']            = 2000;
				$config['max_height']           = 2000;

				$this->load->library('upload', $config);
				if (!is_dir('uploads'))
				{
					mkdir('./uploads', 0777, true);
				}
				if (!is_dir('uploads/' . $ApplicantID))
				{
					mkdir('./uploads/' . $ApplicantID, 0777, true);
					$dir_exist = false;
				}

				if (!$_FILES['pImage']['name'] == '') {
					if (! $this->upload->do_upload('pImage'))
					{
						$this->session->set_flashdata('prompts', '<div class="text-center" style="width: 100%;padding: 21px; color: #F52F2F;"><h5><i class="fas fa-times"></i> '.$this->upload->display_errors().'</h5></div>');
						redirect($_SERVER['HTTP_REFERER']);
					}
					else
					{
						$pImage = base_url().'uploads/'.$ApplicantID.'/'.$this->upload->data('file_name');
					}
				}
				// INSERT EMPLOYEE
				$data = array(
					'ApplicantImage' => $pImage,
					'ApplicantID' => $ApplicantID,
					'PositionDesired' => $PositionDesired,
					'PositionGroup' => $PositionGroup,
					'SalaryExpected' => $SalaryExpected,
					'LastName' => ucfirst($LastName),
					'FirstName' => ucfirst($FirstName),
					'MiddleInitial' => ucfirst($MI),
					'Gender' => $Gender,
					'Age' => $Age,
					'Height' => $Height,
					'Weight' => $Weight,
					'Religion' => $Religion,
					'BirthDate' => $bDate,
					'BirthPlace' => $bPlace,
					'Citizenship' => $Citizenship,
					'CivilStatus' => $CivilStatus,
					'No_OfChildren' => $No_Children,
					
					'Address_Present' => $Address_Present,
					'Address_Provincial' => $Address_Provincial,
					'Address_Manila' => $Address_Manila,

					'Phone_No' => $PhoneNumber,

					'SSS_No' => $SSS,
					'EffectiveDateCoverage' => $SSS_Effective,
					'ResidenceCertificateNo' => $RCN,
					'Rcn_At' => $RCN_at,
					'Rcn_On' => $RCN_On,
					'TIN' => $TIN,
					'TIN_At' => $TIN_At,
					'TIN_On' => $TIN_On,
					
					'HDMF' => $HDMF,
					'HDMF_At' => $HDMF_At,
					'HDMF_On' => $HDMF_On,
					'ATM_No' => $ATM_No,

					'PhilHealth' => $PhilHealth,
					'PhilHealth_At' => $PhilHealth_At,
					'PhilHealth_On' => $PhilHealth_On,
				);
				$addedEmployee = $this->Model_Updates->UpdateEmployee($ApplicantID, $data);
				if ($addedEmployee == TRUE) {

					$AcadHCheckbox = $this->input->post('AcadHCheckbox');
					$listCheck = "'" . implode("','", $AcadHCheckbox) . "'";
					$this->Model_Deletes->RemoveAcadHistory($listCheck);

					$EmpRecordCheckbox = $this->input->post('EmpRecordCheckbox');
					$listCheck = "'" . implode("','", $EmpRecordCheckbox) . "'";
					$this->Model_Deletes->RemoveEmpRecord($listCheck);

					$MachOpCheckbox = $this->input->post('MachOpCheckbox');
					$listCheck = "'" . implode("','", $MachOpCheckbox) . "'";
					$this->Model_Deletes->RemoveMachineOperated($listCheck);

					if (isset($_SESSION["acadcart"])) {
						foreach ($_SESSION["acadcart"] as $s_da) {
							$data = array(
								'ApplicantID' => $ApplicantID,
								'Level' => $s_da['acadcart']['SchoolLevel'],
								'SchoolName' => $s_da['acadcart']['SchoolName'],
								'SchoolAddress' => $s_da['acadcart']['SchoolAddress'],
								'DateStarted' => $s_da['acadcart']['FromYearSchool'],
								'DateEnds' => $s_da['acadcart']['ToYearSchool'],
								'HighDegree' => $s_da['acadcart']['H_Attained'],

							);
							$this->Model_Inserts->InsertAcadH($data);
						}
					}
					if (isset($_SESSION["emp_cart"])) {
						foreach ($_SESSION["emp_cart"] as $s_da) {
							$data = array(
								'ApplicantID' => $ApplicantID,
								'Name' => $s_da['emp_cart']['EmployeerName'],
								'Address' => $s_da['emp_cart']['emAddress'],
								'PeriodCovered' => $s_da['emp_cart']['PeriodCovered'],
								'Position' => $s_da['emp_cart']['Position'],
								'Salary' => $s_da['emp_cart']['Salary'],
								'CauseOfSeparation' => $s_da['emp_cart']['cos'],

							);
							$this->Model_Inserts->InsertEmploymentRecord($data);
						}
					}
					if (isset($_SESSION["mach_cart"])) {
						foreach ($_SESSION["mach_cart"] as $s_da) {
							$data = array(
								'ApplicantID' => $ApplicantID,
								'MachineName' => $s_da['mach_cart']['MachineName'],
							);
							$this->Model_Inserts->InsertMachineOperated($data);
						}
					}
					// if (isset($_SESSION["rela_cart"])) {
					// 	foreach ($_SESSION["rela_cart"] as $s_da) {
					// 		$data = array(
					// 			'ApplicantID' => $customid,
					// 			'Relation' => $s_da['rela_cart']['Relation'],
					// 			'Name' => $s_da['rela_cart']['rName'],
					// 			'Occupation' => $s_da['rela_cart']['rOccupation'],
					// 		);
					// 		$this->Model_Inserts->InsertRelativesdata($data);
					// 	}
					// }
					// if (isset($_SESSION["beneCart"])) {
					// 	foreach ($_SESSION["beneCart"] as $s_da) {
					// 		$data = array(
					// 			'ApplicantID' => $customid,
					// 			'Name' => $s_da['beneCart']['BeneName'],
					// 			'Relationship' => $s_da['beneCart']['BeneRelationship'],
					// 		);
					// 		$this->Model_Inserts->InserBeneficia($data);
					// 	}
					// }
					unset($_SESSION["acadcart"]);
					unset($_SESSION["emp_cart"]);
					unset($_SESSION["mach_cart"]);
					// unset($_SESSION["rela_cart"]); 
					// unset($_SESSION["beneCart"]);
					
					$this->session->set_flashdata('prompts','<div class="text-center" style="width: 100%;padding: 21px; color: #45C830;"><h5><i class="fas fa-check"></i> Details updated!</h5></div>');
					// LOGBOOK
					date_default_timezone_set('Asia/Manila');
					$LogbookCurrentTime = date('Y-m-d h:i:s A');
					$LogbookType = 'Update';
					$LogbookEvent = 'Updated details on Person ID ' . $ApplicantID . '.';
					$LogbookLink = base_url() . 'ViewEmployee?id=' . $ApplicantID;
					$data = array(
						'Time' => $LogbookCurrentTime,
						'Type' => $LogbookType,
						'Event' => $LogbookEvent,
						'Link' => $LogbookLink,
					);
					$LogbookInsert = $this->Model_Inserts->InsertLogbook($data);
					redirect($_SERVER['HTTP_REFERER']);
				}
				else
				{
					$this->session->set_flashdata('prompts','<div class="text-center" style="width: 100%;padding: 21px; color: #F52F2F;"><h5><i class="fas fa-times"></i> Something\'s wrong!</h5></div>');
					redirect($_SERVER['HTTP_REFERER']);
				}
			}
		}
	public function AddNote()
	{
		if (isset($_POST['Note'])) {
			$Note = $this->input->post('Note',TRUE);
			// LOGBOOK
			date_default_timezone_set('Asia/Manila');
			$LogbookCurrentTime = date('Y-m-d h:i:s A');
			$LogbookType = 'Note';
			$data = array(
				'Time' => $LogbookCurrentTime,
				'Type' => $LogbookType,
				'Event' => $Note,
			);
			$LogbookInsert = $this->Model_Inserts->InsertLogbook($data);
			redirect(base_url() . 'Dashboard#Logbook');

		}
	}
	public function SetReminder()
	{
		if (isset($_POST['ApplicantID'])) {
			$ApplicantID = $this->input->post('ApplicantID',FALSE); // TODO: (Dec 12, 2019) Changed from TRUE to FALSE > No XSS filtering.
			$R_Type = $this->input->post('R_Type',TRUE);
			$R_Days = $this->input->post('R_Days',TRUE);
			$R_Months = $this->input->post('R_Months',TRUE);
			$R_Years = $this->input->post('R_Years',TRUE);
			$ReminderDate = 0;

			if ($ApplicantID == NULL) {
				$this->session->set_flashdata('prompts','<div class="text-center" style="width: 100%;padding: 21px; color: #F52F2F;"><h5><i class="fas fa-times"></i> Something\'s wrong, Please try again! (Error: Extend Contract) A:' . $ApplicantID . ' D:' . $E_Days . ' H:' . $E_Months . ' Y:' . $E_Years . ' </h5></div>');
				redirect($_SERVER['HTTP_REFERER']);
			}
			else
			{
				$CheckApplicant = $this->Model_Selects->CheckApplicant($ApplicantID);
				if ($CheckApplicant->num_rows() > 0) {

					date_default_timezone_set('Asia/Manila');

					if ($R_Months == NULL) {
						$ReminderDate = $ReminderDate + 0;
					} else {
						$ReminderDate = $ReminderDate + ($R_Months * 2629743);
					}
					if ($R_Days == NULL) {
						$ReminderDate = $ReminderDate + 0;
					} else {
						$ReminderDate = $ReminderDate + ($R_Days * 86400);
					}
					if ($R_Years == NULL) {
						$ReminderDate = $ReminderDate + 0;
					} else {
						$ReminderDate = $ReminderDate + ($R_Years * 31556926);
					}

					$data = array(
						'ReminderType' => $R_Type,
						'ReminderDate' => $ReminderDate,
						'ReminderLocked' => 'No',
					);
					$SetReminder = $this->Model_Inserts->InsertReminder($ApplicantID,$data);
					if ($SetReminder == TRUE) {
						$this->session->set_flashdata('prompts','<div class="text-center" style="width: 100%;padding: 21px; color: #45C830;"><h5><i class="fas fa-check"></i> Reminder set!</h5></div>');
						// LOGBOOK
						date_default_timezone_set('Asia/Manila');
						$LogbookCurrentTime = date('Y-m-d h:i:s A');
						$LogbookType = 'New';
						$LogbookEvent = 'A reminder has been set for ID ' . $ApplicantID .', alerting after ';
						if($R_Years != 0) {
							$LogbookEvent = $LogbookEvent . $R_Years;
							if($R_Years == 1) {
								$LogbookEvent = $LogbookEvent . ' year, ';
							} else {
								$LogbookEvent = $LogbookEvent . ' years, ';
							}
						}
						if($R_Months != 0) {
							$LogbookEvent = $LogbookEvent . $R_Months;
							if($R_Months == 1) {
								$LogbookEvent = $LogbookEvent . ' month, ';
							} else {
								$LogbookEvent = $LogbookEvent . ' months, ';
							}
						}
						if($R_Days != 0) {
							$LogbookEvent = $LogbookEvent . $R_Days;
							if($R_Days == 1) {
								$LogbookEvent = $LogbookEvent . ' day, ';
							} else {
								$LogbookEvent = $LogbookEvent . ' days, ';
							}
						}
						$LogbookEvent = substr($LogbookEvent, 0, -2) . '!';
						$LogbookLink = base_url() . 'ViewEmployee?id=' . $ApplicantID;
						$data = array(
							'Time' => $LogbookCurrentTime,
							'Type' => $LogbookType,
							'Event' => $LogbookEvent,
							'Link' => $LogbookLink,
						);
						$LogbookInsert = $this->Model_Inserts->InsertLogbook($data);
						redirect($_SERVER['HTTP_REFERER']);
					}
					else
					{
						$this->session->set_flashdata('prompts','<div class="text-center" style="width: 100%;padding: 21px; color: #F52F2F;"><h5><i class="fas fa-times"></i> Something\'s wrong, Please try agains!</h5></div>');
						redirect($_SERVER['HTTP_REFERER']);
					}
				}
				else
				{
					$this->session->set_flashdata('prompts','<div class="text-center" style="width: 100%;padding: 21px; color: #F52F2F;"><h5><i class="fas fa-times"></i> Something\'s wrong, Please try againss!</h5></div>');
					redirect($_SERVER['HTTP_REFERER']);
				}
			}
		}
		else
		{
			$this->session->set_flashdata('prompts','<div class="text-center" style="width: 100%;padding: 21px; color: #F52F2F;"><h5><i class="fas fa-times"></i> Something\'s wrong, Please try againsss!</h5></div>');
			redirect($_SERVER['HTTP_REFERER']);
		}
	}
	public function BlacklistEmployee()
	{
		$ApplicantID = $this->input->get('id');
		if (!isset($_GET['id'])) {
			redirect('Employee');
		}
		else
		{
			$Removethis = $this->Model_Updates->BlacklistEmployee($ApplicantID);
			if ($Removethis == TRUE) {
				$this->session->set_flashdata('prompts','<div class="text-center" style="width: 100%;padding: 21px; color: #45C830;"><h5><i class="fas fa-check"></i> Employee ID ' . $ApplicantID . ' has been blacklisted.</h5></div>');
				// LOGBOOK
				date_default_timezone_set('Asia/Manila');
				$LogbookCurrentTime = date('Y-m-d h:i:s A');
				$LogbookType = 'Archival';
				$LogbookEvent = 'Employee ID ' . $ApplicantID .' has been blacklisted.';
				$LogbookLink = base_url() . 'ViewEmployee?id=' . $ApplicantID;
				$data = array(
					'Time' => $LogbookCurrentTime,
					'Type' => $LogbookType,
					'Event' => $LogbookEvent,
					'Link' => $LogbookLink,
				);
				$LogbookInsert = $this->Model_Inserts->InsertLogbook($data);
				if (isset($_SERVER['HTTP_REFERER'])) {
					redirect($_SERVER['HTTP_REFERER']);
				}
				else
				{
					redirect('Employee');
				}
			}
			else
			{
				redirect('Employee');
			}
		}
	}
	public function SetWeeklyHours()
	{
		if (isset($_POST['ApplicantID'])) {
			$ApplicantID = $this->input->post('ApplicantID',FALSE); // TODO: (Dec 12, 2019) Changed from TRUE to FALSE > No XSS filtering.
			$ClientID = $this->input->post('ClientID',TRUE);
			$GetWeeklyDates = $this->Model_Selects->GetWeeklyDates();
			$ArrayInt = 0;
			$ArrayLength = $GetWeeklyDates->num_rows();
			foreach ($GetWeeklyDates->result_array() as $nrow):
				$ArrayInt++;
				$Type = $this->input->post('Type_' . $nrow['Time'],TRUE);
				$Hours = $this->input->post('Hours_' . $nrow['Time'],TRUE);
				$Overtime = $this->input->post('OTHours_' . $nrow['Time'],TRUE);
				$Date = $this->input->post($nrow['Time'],TRUE);
				if($Hours == NULL) {
					$Hours = 0;
				}
				if($Overtime == NULL) {
					$Overtime = 0;
				}

				if ($ApplicantID == NULL) {
					$this->session->set_flashdata('prompts','<div class="text-center" style="width: 100%;padding: 21px; color: #F52F2F;"><h5><i class="fas fa-times"></i> Something\'s wrong, Please try again! (Error: Missing Field/s)</h5></div>');
					redirect($_SERVER['HTTP_REFERER']);
				}
				else
				{
					date_default_timezone_set('Asia/Manila');

					$data = array(
						'ClientID' => $ClientID,
						'Date' => $Date,
						'Hours' => $Hours,
						'Overtime' => $Overtime,
						'Type' => $Type,
					);
					$UpdateWeeklyHours = $this->Model_Updates->UpdateWeeklyHours($ApplicantID,$data);
					if ($UpdateWeeklyHours == TRUE) {
						if ($ArrayInt >= $ArrayLength) {
							$this->session->set_flashdata('prompts','<div class="text-center" style="width: 100%;padding: 21px; color: #45C830;"><h5><i class="fas fa-check"></i> Updated!</h5></div>');
							// LOGBOOK
							date_default_timezone_set('Asia/Manila');
							$LogbookCurrentTime = date('Y-m-d h:i:s A');
							$LogbookType = 'Update';
							$LogbookEvent = 'Updated weekly hours for ' . $ApplicantID . '.';
							$LogbookLink = base_url() . 'ViewClient?id=' . $Temp_ApplicantID;
							$LogbookLink = base_url() . 'Clients';
							$data = array(
								'Time' => $LogbookCurrentTime,
								'Type' => $LogbookType,
								'Event' => $LogbookEvent,
								'Link' => $LogbookLink,
							);
							$LogbookInsert = $this->Model_Inserts->InsertLogbook($data);
							redirect($_SERVER['HTTP_REFERER']);
						}
					}
					else
					{
						$this->session->set_flashdata('prompts','<div class="text-center" style="width: 100%;padding: 21px; color: #F52F2F;"><h5><i class="fas fa-times"></i> Something\'s wrong, Please try agains!</h5></div>');
						redirect($_SERVER['HTTP_REFERER']);
					}
				}
			endforeach;

		}
		else
		{
			$this->session->set_flashdata('prompts','<div class="text-center" style="width: 100%;padding: 21px; color: #F52F2F;"><h5><i class="fas fa-times"></i> Something\'s wrong, Please try againsss!</h5></div>');
			redirect($_SERVER['HTTP_REFERER']);
		}
	}
	public function ViewClientEmployees()
	{
		$ClientID = $this->input->post('ViewClientID',FALSE); // TODO: (Dec 12, 2019) Changed from TRUE to FALSE > No XSS filtering.
		$Mode = $this->input->post('Mode',TRUE);
		$FromDate = $this->input->post('FromDate',TRUE);
		$ToDate = $this->input->post('ToDate',TRUE);
		// $Year = substr($FromDate, 0, 4);
		// $Month = substr($FromDate, 5);
		// $Month = substr($Month, 0, 2);
		// $Day = substr($FromDate, -2);

		if ($Mode == NULL || $FromDate == NULL || $ToDate == NULL) {
			$this->session->set_flashdata('prompts','<div class="text-center" style="width: 100%;padding: 21px; color: #F52F2F;"><h5><i class="fas fa-times"></i> Error: Date range must be valid</h5></div>');
			redirect($_SERVER['HTTP_REFERER']);
		}

		if ($ClientID == NULL) {
			$this->session->set_flashdata('prompts','<div class="text-center" style="width: 100%;padding: 21px; color: #F52F2F;"><h5><i class="fas fa-times"></i> Something\'s wrong, Please try again! (Error: Missing Client ID)</h5></div>');
			redirect($_SERVER['HTTP_REFERER']);
		}
		else
		{
			date_default_timezone_set('Asia/Manila');
			$date1 = new DateTime($FromDate);
			$date2 = new DateTime($ToDate);

			$diff = $date2->diff($date1)->format("%a");
			if ($diff > 730) {
				$this->session->set_flashdata('prompts','<div class="text-center" style="width: 100%;padding: 21px; color: #F52F2F;"><h5><i class="fas fa-times"></i> Error: Date Range for weekly must be lower than 2 years</h5></div>');
				redirect($_SERVER['HTTP_REFERER']);
			} elseif ($diff > 180 && $diff < 730) {
				$this->session->set_flashdata('prompts','<div class="text-center" style="width: 100%;padding: 21px; color: #FFA500;"><h5><i class="fas fa-exclamation-triangle"></i> Note: You are viewing at a huge date range, performance may get slower than usual</h5></div>');
			}
			// TODO: Clean & optimize this. May cause lag on huge database.
			$this->Model_Updates->UpdateWeeklyHoursCurrent();
			$this->Model_Deletes->CleanWeeklyDates();
			$HoursHolidays = array('01-01', '04-09', '04-10', '05-01', '06-12', '08-31', '11-30', '12-25', '12-30'); // MONTH - DAY
			$SpecialHolidays = array('01-25', '02-25', '04-11', '08-21', '11-01', '11-02', '12-08', '12-24', '12-31'); // MONTH - DAY

			for ($i = 0; $i <= $diff; $i++) {
				$Date = date('Y-m-d', strtotime('+' . $i . ' day', strtotime($FromDate)));
				$DateChecker = new DateTime($Date);
				if (in_array($DateChecker->format('m-d'), $HoursHolidays)) {
					$Type = 'Holiday';
				} elseif (in_array($DateChecker->format('m-d'), $SpecialHolidays)) {
					$Type = 'Special';
				} else {
					$Type = 'Normal';
				}
				$data = array(
					'Time' => $Date,
					'Current' => 'Current',
				);
				$ClientViewTime = $this->Model_Inserts->InsertDummyHours($data);
				if ($ClientViewTime && $i == $diff) {
					redirect('ViewClient?id=' . $ClientID);
				}
			}
		}
	}
	public function ImportExcel()
	{
		$ClientID = $this->input->post('ExcelClientID',FALSE); // TODO: (Dec 12, 2019) Changed from TRUE to FALSE > No XSS filtering.
		$File = $_FILES['file'];
		var_dump($File);
		date_default_timezone_set('Asia/Manila');
		$this->load->library('SimpleXLSX');	
			if ( $xlsx = SimpleXLSX::parse( $File['tmp_name'] ) ) {

				$dim = $xlsx->dimension();
				$cols = $dim[0];
				$RowCount = 0;
				$ColCount = 0;

				foreach ( $xlsx->rows() as $k => $r ):
					if ($k == 0) continue; // skip first row
					// echo '<tr class="clickable-row" data-toggle="modal" data-target="#HoursWeeklyModal">';
					for ( $i = 0; $i < $cols; $i ++ ) {
						if ($RowCount == 0){
							if ($ColCount == 3) {
								$StartingDate = ( isset( $r[ $i ] ) ? $r[ $i ] : '&nbsp;' );

								$this->Model_Updates->UpdateWeeklyHoursCurrent();
								$this->Model_Deletes->CleanWeeklyDates();
								$HoursHolidays = array('01-01', '04-09', '04-10', '05-01', '06-12', '08-31', '11-30', '12-25', '12-30'); // MONTH - DAY
								$SpecialHolidays = array('01-25', '02-25', '04-11', '08-21', '11-01', '11-02', '12-08', '12-24', '12-31'); // MONTH - DAY

								for ($i = 0; $i <= $cols - 5; $i++) {
									$Date = date('Y-m-d', strtotime('+' . $i . ' day', strtotime($StartingDate)));
									$DateChecker = new DateTime($Date);
									if (in_array($DateChecker->format('m-d'), $HoursHolidays)) {
										$Type = 'Holiday';
									} elseif (in_array($DateChecker->format('m-d'), $SpecialHolidays)) {
										$Type = 'Special';
									} else {
										$Type = 'Normal';
									}
									$data = array(
										'Time' => $Date,
										'Regular' => '1',
										'Current' => 'Current',
									);
									if (isset($Holiday)) {
										$data['Holiday'] = '1';
									} else {
										$data['Holiday'] = NULL;
									}
									if (isset($Special)) {
										$data['Special'] = '1';
									} else {
										$data['Special'] = NULL;
									}
									$ClientViewTime = $this->Model_Inserts->InsertDummyHours($data);
								}

							}

						}
						if ($RowCount >= 1) {
							if ($ColCount == 0) {
								$ApplicantID = ( isset( $r[ $i ] ) ? $r[ $i ] : '&nbsp;' );
							}
							if ($ColCount == 1) {
								$Name = ( isset( $r[ $i ] ) ? $r[ $i ] : '&nbsp;' );
							}
							if ($ColCount == 2) {
								$Salary = ( isset( $r[ $i ] ) ? $r[ $i ] : '&nbsp;' );
							}
							if ($ColCount >= 3 && $ColCount < $cols - 1) {
								$GetWeeklyDates = $this->Model_Selects->GetWeeklyDates();
								// foreach ($GetWeeklyDates->result_array($ColCount - 3) as $nrow):
								// 	echo $nrow['Time'];
								// endforeach;

								$Split = explode('/', ( isset( $r[ $i ] ) ? $r[ $i ] : '&nbsp;' ));
								print_r($Split);
								if ($Split[0] == 'N') {
									$Type = 'Normal';
								} elseif ($Split[0] == 'R') {
									$Type = 'Rest Day';
								} elseif ($Split[0] == 'H') {
									$Type = 'Holiday';
								} elseif ($Split[0] == 'S') {
									$Type = 'Special';
								} else {
									$Type = 'Unknown';
								}


								$data = array(
									'ClientID' => $ClientID,
									'Date' => $GetWeeklyDates->result_array()[$ColCount - 3]['Time'],
									'Type' => $Type,
									'Hours' => $Split[1],
									'Overtime' => $Split[2],
									'NightShift' => $Split[3],
								);
								$UpdateWeeklyHours = $this->Model_Updates->UpdateWeeklyHours($ApplicantID,$data);
								// echo '------------- <br>';
								// echo 'Applicant ID: ' . $ApplicantID . '<br>';
								// echo 'Name: ' . $Name . '<br>';
								// echo 'Salary: ' . $Salary . '<br>';
								// echo 'Date: ' . $GetWeeklyDates->result_array()[$ColCount - 3]['Time'] . '<br>';
								// echo 'Hours: ' . ( isset( $r[ $i ] ) ? $r[ $i ] : '&nbsp;' ) . '<br>';
								// echo '------------- <br>';
							}
						}
						// echo '<td><i class="Hours_' . ( isset( $r[ $i ] ) ? $r[ $i ] : '&nbsp;' ) . '"></i>' . ( isset( $r[ $i ] ) ? $r[ $i ] : '&nbsp;' ) . '</td>';
						$ColCount++;
					}
					// echo '</tr>';
					$RowCount++;
					$ColCount = 0;
				endforeach;
				if ($RowCount <= $xlsx->rows()) {
					echo '<hr><h1>HELLO</h1><hr>';
					redirect('ViewClient?id=' . $ClientID);
				}
				$this->load->view('_template/users/u_redirecting');
				echo '<br>' . $cols;
				// echo '</table>';
			} else {
				$Error = SimpleXLSX::parseError();
				$this->session->set_flashdata('prompts','<div class="text-center" style="width: 100%;padding: 21px; color: #F52F2F;"><h5><i class="fas fa-times"></i> Error: ' . $Error . '</h5></div>');
				redirect($_SERVER['HTTP_REFERER']);
			}

			// $date1 = new DateTime($FromDate);
			// $date2 = new DateTime($ToDate);

			// $diff = $date2->diff($date1)->format("%a");
			// if ($diff > 730) {
			// 	$this->session->set_flashdata('prompts','<div class="text-center" style="width: 100%;padding: 21px; color: #F52F2F;"><h5><i class="fas fa-times"></i> Error: Date Range for weekly must be lower than 2 years</h5></div>');
			// 	redirect($_SERVER['HTTP_REFERER']);
			// } elseif ($diff > 180 && $diff < 730) {
			// 	$this->session->set_flashdata('prompts','<div class="text-center" style="width: 100%;padding: 21px; color: #FFA500;"><h5><i class="fas fa-exclamation-triangle"></i> Note: You are viewing at a huge date range, performance may get slower than usual</h5></div>');
			// }
			// // TODO: Clean & optimize this. May cause lag on huge database.
			// $this->Model_Updates->UpdateWeeklyHoursCurrent();
			// $this->Model_Deletes->CleanWeeklyDates();
			// for ($i = 0; $i <= $diff; $i++) {
			// 	if ($Day < 10 && $i != 0) {
			// 		$Date = $Year . '-' . $Month . '-' . '0' . $Day;
			// 	} else {
			// 		$Date = $Year . '-' . $Month . '-' . $Day;
			// 	}
			// 	$data = array(
			// 		'Time' => $Date,
			// 		'Current' => 'Current',
			// 	);
			// 	$ClientViewTime = $this->Model_Inserts->InsertClientViewTime($data);
			// 	$Day++;
			// 	if ($ClientViewTime && $i == $diff) {
			// 		redirect('ViewClient?id=' . $ClientID);
			// 	}
			// }
	}
}