<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Report extends Admin_Controller {
/*
| -----------------------------------------------------
| PRODUCT NAME: 	INILABS SCHOOL MANAGEMENT SYSTEM
| -----------------------------------------------------
| AUTHOR:			INILABS TEAM
| -----------------------------------------------------
| EMAIL:			info@inilabs.net
| -----------------------------------------------------
| COPYRIGHT:		RESERVED BY INILABS IT
| -----------------------------------------------------
| WEBSITE:			http://inilabs.net
| -----------------------------------------------------
*/
	function __construct() {
		parent::__construct();
		$this->load->model("subject_m");
		$this->load->model("subjectattendance_m");
		$this->load->model("student_info_m");
		$this->load->model("parents_info_m");
		$this->load->model('section_m');
		$this->load->model("classes_m");
		$this->load->model("teacher_m");
		$this->load->model("student_m");
		$this->load->model("invoice_m");
		$this->load->model("sattendance_m");
		$this->load->model("payment_m");
		$this->load->model("mark_m");
		$this->load->model("transport_m");
		$this->load->model("hostel_m");
		$this->load->model("hmember_m");
		$this->load->model("tmember_m");
		$this->load->model("grade_m");
		$this->load->model("exam_m");
		$this->load->model("routine_m");
		$language = $this->session->userdata('lang');
		$this->lang->load('report', $language);
	}

	public function student()
	{
		$this->data["subview"] = "report/Student";
		$this->load->view('_layout_main', $this->data);
	}

	public function classreport()
	{
		$this->data['classes'] = $this->classes_m->get_classes();
		$this->data["subview"] = "report/class/ClassReportView";
		$this->load->view('_layout_main', $this->data);
	}

	public function getClassReport()
	{
		$classID 		= $this->input->post('classID');
		$sectionID 		= $this->input->post('sectionID');
		$schoolyearID 	= $this->data['siteinfos']->school_year;

		$classes 	= $this->classes_m->get_classes($classID);

		$subjects = $this->subject_m->get_order_by_subject(array( 'classesID' => $classID));
		$teachers = pluck($this->teacher_m->get_teacher(), 'obj', 'teacherID');

		if($sectionID == 0) {
			$students = pluck($this->student_m->get_order_by_student(array( 'classesID' => $classID, 'schoolyearID' => $schoolyearID)), 'obj', 'studentID');
			$sections = pluck($this->section_m->get_order_by_section(array( 'classesID' => $classID)), 'obj', 'sectionID');

		} else {
			$students = pluck($this->student_m->get_order_by_student(array( 'classesID' => $classID, 'sectionID' => $sectionID, 'schoolyearID' => $schoolyearID)), 'obj', 'studentID');
			$sections 	= pluck($this->section_m->get_order_by_section(array( 'classesID' => $classID, 'sectionID' => $sectionID)), 'obj', 'sectionID');

		}

		$invoices = $this->invoice_m->get_order_by_invoice(array('classesID' => $classID, 'schoolyearID' => $schoolyearID));
		$payments = pluck($this->payment_m->get_order_by_payment(array('schoolyearID' => $schoolyearID)), 'obj', 'invoiceID');
		$studentInvoices = array();
		$feetypes = array();
		$collectionAmount = 0;
		$totalInvoiceAmount = 0;
		foreach ($invoices as $invoice ) {
			if(!isset($students[$invoice->studentID])) continue;

			if(!isset($feetypes[$invoice->feetype])) {
				$feetypes[$invoice->feetype] = 0;
			}

			if(!isset($studentInvoices[$invoice->studentID]['amount'])) {
				$studentInvoices[$invoice->studentID]['amount'] = 0;
			}

			if(isset($payments[$invoice->invoiceID])) {
				if(!isset($studentInvoices[$payments[$invoice->invoiceID]->studentID]['payment'])) {
					$studentInvoices[$payments[$invoice->invoiceID]->studentID]['payment'] = 0;
				}
				$collectionAmount += $payments[$invoice->invoiceID]->paymentamount;
				$feetypes[$invoice->feetype] += $payments[$invoice->invoiceID]->paymentamount;
				$studentInvoices[$payments[$invoice->invoiceID]->studentID]['payment'] += $payments[$invoice->invoiceID]->paymentamount;
			}

			$totalInvoiceAmount += $invoice->amount;
			$studentInvoices[$invoice->studentID]['amount'] += $invoice->amount;
		}

		$dueAmount = $totalInvoiceAmount - $collectionAmount;

		$this->data['class'] = $classes;
		$this->data['subjects'] = $subjects;
		$this->data['teachers'] = $teachers;
		$this->data['students'] = $students;
		$this->data['sections'] = $sections;
		if(isset($sections[$sectionID])) {
			$this->data['sectionName'] = $this->lang->line("report_section")." ".$sections[$sectionID]->section;
		} elseif ($sectionID == 0) {
			$this->data['sectionName'] = $this->lang->line("report_select_all_section");
		}
		$this->data['collectionAmount'] = $collectionAmount;
		$this->data['dueAmount'] = $dueAmount;
		$this->data['studentInvoices'] = $studentInvoices;
		$this->data['feetypes'] = $feetypes;


		echo $this->load->view('report/class/ClassReport', $this->data, true);
	}

	public function attendancereport()
	{
		$this->data['classes'] = $this->classes_m->get_classes();
		$this->data['headerassets'] = array(
			'css' => array(
				'assets/datepicker/datepicker.css',
			),
			'js' => array(
				'assets/datepicker/datepicker.js',
			)
		);
		$attendacewise 	= $this->data['siteinfos']->attendance;
		if($attendacewise == 'subject') {
			$this->data['subjectWise'] = 1;
		} else {
			$this->data['subjectWise'] = 0;
		}

		$this->data["subview"] = "report/attendance/AttendanceReportView";
		$this->load->view('_layout_main', $this->data);
	}

	public function getAttendacneReport()
	{
		$classID 		= $this->input->post('classID');
		$sectionID 		= $this->input->post('sectionID');
		$type 			= $this->input->post('type');
		$date 			= explode('-', $this->input->post('date'));
		$schoolyearID 	= $this->data['siteinfos']->school_year;
		$classes 		= $this->classes_m->get_classes($classID);

		$day = 'a'.(int)$date[0];
		$monthyear = $date[1].'-'.$date[2];

		if($sectionID == 0) {
			$students = pluck($this->student_m->get_order_by_student(array( 'classesID' => $classID, 'schoolyearID' => $schoolyearID)), 'obj', 'studentID');
			$sections = pluck($this->section_m->get_order_by_section(array( 'classesID' => $classID)), 'obj', 'sectionID');
			if($this->input->post('subjectID')) {
				$attendances = $this->subjectattendance_m->get_order_by_sub_attendance(array('classesID' => $classID, 'subjectID' => $this->input->post('subjectID'), 'monthyear' => $monthyear));
			} else {
				$attendances = $this->sattendance_m->get_order_by_attendance(array('classesID' => $classID, 'monthyear' => $monthyear));
			}

		} else {
			$students = pluck($this->student_m->get_order_by_student(array( 'classesID' => $classID, 'sectionID' => $sectionID, 'schoolyearID' => $schoolyearID)), 'obj', 'studentID');
			$sections 	= pluck($this->section_m->get_order_by_section(array( 'classesID' => $classID, 'sectionID' => $sectionID)), 'obj', 'sectionID');
			if($this->input->post('subjectID')) {
				$attendances = $this->subjectattendance_m->get_order_by_sub_attendance(array('classesID' => $classID, 'sectionID' => $sectionID, 'subjectID' => $this->input->post('subjectID'), 'monthyear' => $monthyear));
			} else {
				$attendances = $this->sattendance_m->get_order_by_attendance(array('classesID' => $classID, 'sectionID' => $sectionID, 'monthyear' => $monthyear));
			}

		}

		$attendances = pluck($attendances, 'obj', 'studentID');

		$this->data['attendances'] = $attendances;
		$this->data['students'] = $students;
		$this->data['class'] = $classes;
		$this->data['typeSortForm'] = $type;
		$this->data['day'] = $day;
		if(isset($sections[$sectionID])) {
			$this->data['sectionName'] = $this->lang->line("report_section")." ".$sections[$sectionID]->section;
		} elseif ($sectionID == 0) {
			$this->data['sectionName'] = $this->lang->line("report_select_all_section");
		}

		if($type == 'A') {
			$this->data['type'] = $this->lang->line("report_absent");
		} else {
			$this->data['type'] = $this->lang->line("report_present");
		}

		echo $this->load->view('report/attendance/AttendanceReport', $this->data, true);

	}

	public function studentreport()
	{
		$this->data['classes'] = $this->classes_m->get_classes();
		$this->data['transports'] = $this->transport_m->get_transport();
		$this->data['hostels'] = $this->hostel_m->get_hostel();
		// $this->data['headerassets'] = array(
		// 	'css' => array(
		// 		'assets/datepicker/datepicker.css',
		// 	),
		// 	'js' => array(
		// 		'assets/datepicker/datepicker.js',
		// 	)
		// );
		// $attendacewise 	= $this->data['siteinfos']->attendance;
		// if($attendacewise == 'subject') {
		// 	$this->data['subjectWise'] = 1;
		// } else {
		// 	$this->data['subjectWise'] = 0;
		// }

		$this->data["subview"] = "report/student/StudentReportView";
		$this->load->view('_layout_main', $this->data);
	}

	public function getStudentReport()
	{
		$schoolorclass = $this->input->post('schoolorclass');
		$reportfor = $this->input->post('reportfor');
		$schoolyearID 	= $this->data['siteinfos']->school_year;
		$students = array();
		$where = array();
		$reportTitle = '';

		if($reportfor == 'blood') {
			$reportfor .= ' Group';
			$reportTitle = $this->input->post('value');
			$where = $this->getCondition('bloodgroup');
		} elseif ($reportfor == 'country') {
			$reportTitle = $this->data['allcountry'][$this->input->post('value')];
			$where = $this->getCondition('country');
		} elseif($reportfor == 'gender') {
			$reportTitle = $this->input->post('value');
			$where = $this->getCondition('sex');
		} elseif ($reportfor == 'transport') {
			$route = $this->input->post('value');
			$reportTitle = $this->transport_m->get_single_transport(array('transportID' => $route))->route;

			$transports = $this->tmember_m->get_order_by_tmember(array('transportID' => $route));

			$allStudents = pluck($this->student_m->get_order_by_student($this->getCondition()), 'obj', 'studentID');
			foreach ($transports as $transport) {
				if(isset($allStudents[$transport->studentID])) {
					$students[$transport->studentID] = $allStudents[$transport->studentID];
				}
			}
		} elseif ($reportfor == 'hostel') {
			$hostelID = $this->input->post('value');
			$reportTitle = $this->hostel_m->get_single_hostel(array('hostelID' => $hostelID))->name;

			$hostels = $this->hmember_m->get_order_by_hmember(array('hostelID' => $hostelID));

			$allStudents = pluck($this->student_m->get_order_by_student($this->getCondition()), 'obj', 'studentID');
			foreach ($hostels as $hostel) {
				if(isset($allStudents[$hostel->studentID])) {
					$students[$hostel->studentID] = $allStudents[$hostel->studentID];
				}
			}
		}

		if(count($where)) {
			$students = $this->student_m->get_order_by_student($where);
		}

		$this->data['students'] = $students;
		$this->data['schoolorclass'] = $schoolorclass;
		$this->data['reportfor'] = $reportfor;
		$this->data['reportTitle'] = $reportTitle;
		echo $this->load->view('report/student/StudentReport', $this->data, true);
	}

	public function getCondition($field=NULL)
	{
		$schoolorclass 	= $this->input->post('schoolorclass');
		$schoolyearID 	= $this->data['siteinfos']->school_year;

		if($field != NULL) {
			$data 			= $this->input->post('value');
			$where = array(
				'schoolyearID' => $schoolyearID,
				$field => $data
			);
		} else {
			$where = array(
				'schoolyearID' => $schoolyearID
			);
		}

		if($schoolorclass == 'class') {

			$classID 		= $this->input->post('classID');
			$sectionID 		= $this->input->post('sectionID');

			$this->data['class'] 	= $this->classes_m->get_classes($classID);
			$this->data['classSections'] = pluck($this->section_m->get_order_by_section(array( 'classesID' => $classID)), 'obj', 'sectionID');

			if($sectionID == 0) {
				$where['classesID'] = $classID;
			} else {
				$where['classesID'] = $classID;
				$where['sectionID'] = $sectionID;
			}
		} else {
			$this->data['classes'] 	= pluck($this->classes_m->get_classes(), 'obj' , 'classesID');
			$this->data['sections'] = pluck($this->section_m->get_section(), 'obj', 'sectionID');
		}

		return $where;
	}

	public function getSection()
	{
		$id = $this->input->post('id');
		if((int)$id) {
			$allSection = $this->section_m->get_order_by_section(array('classesID' => $id));

			echo "<option value='0'>", $this->lang->line("report_select_all_section"),"</option>";

			foreach ($allSection as $value) {
				echo "<option value=\"$value->sectionID\">",$value->section,"</option>";
			}

		}
	}

	public function getSubject()
	{
		$classID = $this->input->post('classID');
		if((int)$classID) {
			$allSubject = $this->subject_m->get_order_by_subject(array('classesID' => $classID));

			echo "<option value=''>", $this->lang->line("report_select_subject"),"</option>";

			foreach ($allSubject as $value) {
				echo "<option value=\"$value->subjectID\">",$value->subject,"</option>";
			}

		}
	}

}
