<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Visitorinfo extends Admin_Controller {

	function __construct() {
		parent::__construct();
		$this->load->model("visitorinfo_m");
		$this->load->model('usertype_m');
		$this->load->model('systemadmin_m');
		$this->load->model('student_m');
		$this->load->model('parents_m');
		$this->load->model('teacher_m');
		$this->load->model('user_m');
		$language = $this->session->userdata('lang');
		$this->lang->load('visitorinfo', $language);
	}

	protected function rules() {
		$rules = array(
			array(
				'field' => 'name',
				'label' => $this->lang->line("name"),
				'rules' => 'trim|required|xss_clean|max_length[60]'
			),
			array(
				'field' => 'email_id',
				'label' => $this->lang->line("email_id"),
				'rules' => 'trim|required|max_length[40]|valid_email|xss_clean'
			),
			array(
				'field' => 'phone',
				'label' => $this->lang->line("phone"),
				'rules' => 'trim|required|max_length[25]|min_length[5]|xss_clean'
			),
			array(
				'field' => 'company_name',
				'label' => $this->lang->line("company_name"),
				'rules' => 'trim|max_length[200]|xss_clean'
			),
            array(
				'field' => 'to_meet_usertypeID',
				'label' => $this->lang->line("usertypeID"),
				'rules' => 'trim|required|max_length[200]|xss_clean'
			),
			array(
				'field' => 'coming_from',
				'label' => $this->lang->line("coming_from"),
				'rules' => 'trim|required|xss_clean'
			),
			array(
				'field' => 'to_meet_personID',
				'label' => $this->lang->line("to_meet"),
				'rules' => 'trim|required|xss_clean'
			),
			array(
				'field' => 'representing',
				'label' => $this->lang->line("representing"),
				'rules' => 'trim|required|max_length[40]|xss_clean'
			)
		);
		return $rules;
	}

	public function index() {
		$this->data['usertypes'] = $this->usertype_m->get_usertype();
		$usertypeID = $this->input->post("usertypeID");
		if($usertypeID != 0) {
			if($usertypeID == 1) {
				$this->data['users'] = $this->systemadmin_m->get_systemadmin();
			} elseif($usertypeID == 2) {
				$this->data['users'] = $this->teacher_m->get_teacher();
			} elseif($usertypeID == 3) {
				$this->data['users'] = $this->student_m->get_order_by_student(array('schoolyearID' => $this->data['siteinfos']->school_year));
			} elseif($usertypeID == 4) {
				$this->data['users'] = $this->parents_m->get_parents();
			} else {
				$this->data['users'] = $this->user_m->get_order_by_user(array('usertypeID' => $usertypeID));
			}
		} else {
			$this->data['users'] = "empty";
		}
		$this->data['to_meet'] = 0;
        $this->data['passes'] = $this->visitorinfo_m->get_order_by_visitorinfo(array('schoolyearID' => $this->session->userdata('defaultschoolyearID')));
        $mapArray = array();
        $mapUsertype = pluck($this->usertype_m->get_usertype(), 'usertype', 'usertypeID');

        $systemadmins = $this->systemadmin_m->get_systemadmin();
        if(count($systemadmins)) {
            foreach ($systemadmins as $systemadmin) {
                $mapArray[$systemadmin->usertypeID][$systemadmin->systemadminID] = array($systemadmin->name, $mapUsertype[$systemadmin->usertypeID]);
            }
        }
        $teachers = $this->teacher_m->get_teacher();
        if(count($teachers)) {
            foreach ($teachers as $teacher) {
                $mapArray[$teacher->usertypeID][$teacher->teacherID] = array($teacher->name, $mapUsertype[$teacher->usertypeID]);
            }
        }
        $students = $this->student_m->get_order_by_student(array('schoolyearID' => $this->session->userdata('defaultschoolyearID')));
        if(count($students)) {
            foreach ($students as $student) {
                $mapArray[$student->usertypeID][$student->studentID] = array($student->name, $mapUsertype[$student->usertypeID]);
            }
        }
        $parents = $this->parents_m->get_parents();
        if(count($parents)) {
            foreach ($parents as $parent) {
                $mapArray[$parent->usertypeID][$parent->parentsID] = array($parent->name, $mapUsertype[$parent->usertypeID]);
            }
        }
        $users = $this->user_m->get_order_by_user();
        if(count($users)) {
            foreach ($users as $user) {
                $mapArray[$user->usertypeID][$user->userID] = array($user->name, $mapUsertype[$user->usertypeID]);
            }
        }

        $this->data['allUsers'] = $mapArray;

		if ($_POST) {
			$rules = $this->rules();
			$array = array();
            $this->form_validation->set_rules($rules);
            if ($this->form_validation->run() == FALSE) {
                $response = array("validate" => false,"name" => form_error('name'));
                $response['email_id'] = form_error('email_id');
                $response['phone'] = form_error('phone');
                $response['company_name'] = form_error('company_name');
                $response['to_meet_usertypeID'] = form_error('to_meet_usertypeID');
                $response['coming_from'] = form_error('coming_from');
                $response['to_meet_personID'] = form_error('to_meet_personID');
                $response['representing'] = form_error('representing');
                echo json_encode($response, 500);
                exit;
            } else {
                for($i=0; $i<count($rules); $i++) {
                    $array[$rules[$i]['field']] = $this->input->post($rules[$i]['field']);
                }
                $array["check_in"] = date("Y-m-d h:i:s");
                $array["status"] = 0;
                $array["schoolyearID"] = $this->data['siteinfos']->school_year;
                $encoded_data = $_POST['image'];
                $binary_data = base64_decode( $encoded_data );
                // // save to server (beware of permissions)
                $file_name_rename = rand(1, 100000000000);
                $new_file = "visitor".$file_name_rename.'.jpeg';
                $result = file_put_contents( 'uploads/visitor/'.$new_file, $binary_data );
                $array["photo"] = $new_file;
                if ($result) {
                    if($id = $this->visitorinfo_m->insert_visitorinfo($array)) {
                        $this->session->set_flashdata('success', $this->lang->line("upload_success"));

                        $arr = array(
                              'id'=>$id,
                              'to_meet'=> $mapArray[$array["to_meet_usertypeID"]][$array["to_meet_personID"]][0],
                              'to_meet_type'=>$mapArray[$array["to_meet_usertypeID"]][$array["to_meet_personID"]][1],
                            );
                        echo json_encode($arr);
                    } else {
                        $this->session->set_flashdata('error', $this->lang->line("upload_error_data"));
                        $this->data["subview"] = "visitorinfo/index";
                        $this->load->view('_layout_main', $this->data);
                    }
                } else {
                    $this->session->set_flashdata('error', $this->lang->line("upload_error"));
                    $this->data["subview"] = "visitorinfo/index";
                    $this->load->view('_layout_main', $this->data);
                }
            }
	    } else {
			$this->data["subview"] = "visitorinfo/index";
			$this->load->view('_layout_main', $this->data);
		}

	}

	function usercall() {
		$schoolyearID = $this->session->userdata('defaultschoolyearID');
		$usertypeID = $this->input->post('id');
		if((int)$usertypeID) {
			$this->data['users'] = array();
			if($usertypeID == 1) {
				$this->data['users'] = $this->systemadmin_m->get_systemadmin();
				echo "<option value='0'>", $this->lang->line("visitor_select_user"),"</option>";
				if(count($this->data['users'])) {
					foreach ($this->data['users'] as $value) {
						echo "<option value=\"$value->systemadminID\">",$value->name,"</option>";
					}
				}
			} elseif($usertypeID == 2) {
				$this->data['users'] = $this->teacher_m->get_teacher();
				echo "<option value='0'>", $this->lang->line("visitor_select_user"),"</option>";
				if(count($this->data['users'])) {
					foreach ($this->data['users'] as $value) {
						echo "<option value=\"$value->teacherID\">",$value->name,"</option>";
					}
				}
			} elseif($usertypeID == 3) {
				$this->data['users'] = $this->student_m->get_order_by_student(array('schoolyearID' => $schoolyearID));
				echo "<option value='0'>", $this->lang->line("visitor_select_user"),"</option>";
				if(count($this->data['users'])) {
					foreach ($this->data['users'] as $value) {
						echo "<option value=\"$value->studentID\">",$value->name,"</option>";
					}
				}
			} elseif($usertypeID == 4) {
				$this->data['users'] = $this->parents_m->get_parents();
				echo "<option value='0'>", $this->lang->line("visitor_select_user"),"</option>";
				if(count($this->data['users'])) {
					foreach ($this->data['users'] as $value) {
						echo "<option value=\"$value->parentsID\">",$value->name,"</option>";
					}
				}
			} else {
				$this->data['users'] = $this->user_m->get_order_by_user(array('usertypeID' => $usertypeID));
				echo "<option value='0'>", $this->lang->line("visitor_select_user"),"</option>";
				if(count($this->data['users'])) {
					foreach ($this->data['users'] as $value) {
						echo "<option value=\"$value->userID\">",$value->name,"</option>";
					}
				}
			}
		}
	}


	public function logout() {

		$id = $this->input->post('visitorID');
		if ((int)$id) {
			$array = [];
			$array['check_out'] = date("Y-m-d h:i:s");
			$array['status'] = 1;
			if($this->visitorinfo_m->update_visitorinfo($array, $id)) {
	    		$this->session->set_flashdata('success', $this->lang->line("checkout_success"));
	    		echo base_url("visitorinfo/index");
	    	} else {
	    		$this->session->set_flashdata('error', $this->lang->line("checkout_error"));
				echo base_url("visitorinfo/index");
	    	}
		} else {
			$this->session->set_flashdata('error', $this->lang->line("invalid_id"));
			echo base_url("visitorinfo/index");
		}

	}

	public function delete() {
		$id = htmlentities(escapeString($this->uri->segment(3)));
		if((int)$id) {
			$this->visitorinfo_m->delete_visitorinfo($id);
			$this->session->set_flashdata('success', $this->lang->line('menu_success'));
			redirect(base_url("visitorinfo/index"));
		} else {
			redirect(base_url("visitorinfo/index"));
		}
	}

	public function view() {
        //get all users
        $mapArray = array();
        $mapUsertype = pluck($this->usertype_m->get_usertype(), 'usertype', 'usertypeID');

        $systemadmins = $this->systemadmin_m->get_systemadmin();
        if(count($systemadmins)) {
            foreach ($systemadmins as $systemadmin) {
                $mapArray[$systemadmin->usertypeID][$systemadmin->systemadminID] = array($systemadmin->name, $mapUsertype[$systemadmin->usertypeID]);
            }
        }
        $teachers = $this->teacher_m->get_teacher();
        if(count($teachers)) {
            foreach ($teachers as $teacher) {
                $mapArray[$teacher->usertypeID][$teacher->teacherID] = array($teacher->name, $mapUsertype[$teacher->usertypeID]);
            }
        }
        $students = $this->student_m->get_order_by_student(array('schoolyearID' => $this->session->userdata('defaultschoolyearID')));
        if(count($students)) {
            foreach ($students as $student) {
                $mapArray[$student->usertypeID][$student->studentID] = array($student->name, $mapUsertype[$student->usertypeID]);
            }
        }
        $parents = $this->parents_m->get_parents();
        if(count($parents)) {
            foreach ($parents as $parent) {
                $mapArray[$parent->usertypeID][$parent->parentsID] = array($parent->name, $mapUsertype[$parent->usertypeID]);
            }
        }
        $users = $this->user_m->get_order_by_user();
        if(count($users)) {
            foreach ($users as $user) {
                $mapArray[$user->usertypeID][$user->userID] = array($user->name, $mapUsertype[$user->usertypeID]);
            }
        }

		$id = $this->input->post('visitorinfoID');
		if((int)$id) {
			$data = $this->visitorinfo_m->get_visitorinfo($id);
			$arr = array(
					  'id'=>$data->visitorID,
					  'photo'=>$data->photo,
					  'phone'=>$data->phone,
					  'email_id'=>$data->email_id,
					  'name'=>$data->name,
                      'to_meet'=> $mapArray[$data->to_meet_usertypeID][$data->to_meet_personID][0],
                      'to_meet_type'=>$mapArray[$data->to_meet_usertypeID][$data->to_meet_personID][1],
					  'company_name'=>$data->company_name,
					  'coming_from'=>$data->coming_from,
					  'representing'=>$data->representing,
					);
			echo json_encode($arr);
		} else {
			redirect(base_url("visitorinfo/index"));
		}
	}



}

/* End of file visitorinfo.php */
/* Location: .//var/www/html/schoolv2/mvc/controllers/visitorinfo.php */
