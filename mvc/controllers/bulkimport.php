<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Bulkimport extends Admin_Controller {
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
		$language = $this->session->userdata('lang');
        $this->load->model("teacher_m");
        $this->load->model("parents_m");
        $this->load->model("student_m");
        $this->load->model("user_m");
        $this->load->model("book_m");
        $this->load->model("studentrelation_m");
        $this->load->model("section_m");
        $this->load->model("classes_m");
        $this->lang->load('parents', $language);
        $this->lang->load('student', $language);
        $this->lang->load('user', $language);
        $this->lang->load('bulkimport', $language);
        $this->load->library('csvimport');
	}

	public function index() {
    	$this->data["subview"] = "bulkimport/index";
    	$this->load->view('_layout_main', $this->data);
	}
    public function unique_email()
    {
        $this->db->select('email');
        $query = $this->db->get('teacher');
        $teacher_emails = $query->result();
        $this->db->select('email');
        $query1 = $this->db->get('student');
        $std_emails = $query1->result();
        $this->db->select('email');
        $query2 = $this->db->get('parents');
        $parent_emails = $query2->result();
        $this->db->select('email');
        $query3 = $this->db->get('user');
        $user_emails = $query3->result();
        $this->db->select('email');
        $query4 = $this->db->get('systemadmin');
        $systemadmin_emails = $query4->result();
        $emails = array_merge($teacher_emails,$std_emails, $parent_emails,$user_emails, $systemadmin_emails);
        $result = array();
        foreach ($emails as $key => $value) {
            array_push($result, $value->email);
        }
        return $result;
    }
    public function unique_username() {
        $this->db->select('username');
        $query = $this->db->get('teacher');
        $teacher_usernames = $query->result();
        $this->db->select('username');
        $query1 = $this->db->get('student');
        $std_usernames = $query1->result();
        $this->db->select('username');
        $query2 = $this->db->get('parents');
        $parent_usernames = $query2->result();
        $this->db->select('username');
        $query3 = $this->db->get('user');
        $user_usernames = $query3->result();
        $this->db->select('username');
        $query4 = $this->db->get('systemadmin');
        $systemadmin_usernames = $query4->result();
        $usernames = array_merge($teacher_usernames,$std_usernames, $parent_usernames,$user_usernames, $systemadmin_usernames);
        $result = array();
        foreach ($usernames as $key => $value) {
            array_push($result, $value->username);
        }
        return $result;
    }

    public function unique_email_validation() {
        if($this->input->post('email')) {
            $tables = array('student' => 'student', 'parents' => 'parents', 'teacher' => 'teacher', 'user' => 'user', 'systemadmin' => 'systemadmin');
            $array = array();
            $i = 0;
            foreach ($tables as $table) {
                $user = $this->student_m->get_username($table, array("email" => $this->input->post('email')));
                if(count($user)) {
                    $this->form_validation->set_message("unique_email_validation", "%s already exists");
                    $array['permition'][$i] = 'no';
                } else {
                    $array['permition'][$i] = 'yes';
                }
                $i++;
            }

            if(in_array('no', $array['permition'])) {
                return FALSE;
            } else {
                return TRUE;
            }

        }
        return TRUE;
    }

    public function teacher_bulkimport() {
        if(isset($_FILES["csvFile"])) {
            $msg = "";
            $all_useremails = $this->unique_email();
            $all_usernames = $this->unique_username();

            $config['upload_path'] = "./uploads/csv/";
            $config['allowed_types'] = 'text/plain|text/csv|csv';
            $config['max_size'] = '2048';
            $config['file_name'] = $_FILES["csvFile"]['name'];
            $config['overwrite'] = TRUE;
            $this->load->library('upload', $config);
            if(!$this->upload->do_upload("csvFile")) {
                $this->session->set_flashdata('error', $this->lang->line('import_error'));
                redirect(base_url("bulkimport/index"));
            } else {
                $file_data = $this->upload->data();
                $file_path =  './uploads/csv/'.$file_data['file_name'];
                $column_headers = array("Name", "Designation", "Dob", "Gender", "Religion", "Email", "Phone", "Address", "Jod", "Username", "Password");
                if ($this->csvimport->get_array($file_path)) {
                    $i = 0;
                    $csv_array = $this->csvimport->get_array($file_path);
                    $csv_col = array();
                    foreach ($csv_array as $row) {
                        if ($i==0) {
                            $csv_col = array_keys($row);
                        }
                        $match = array_diff($column_headers, $csv_col);
                        if (count($match) <= 0) {
	                        if (in_array($row['Email'], $all_useremails)) {
                                $msg .= $i.". ".$row['Name']." is not added! <br>";
	                            $this->session->set_flashdata('error', "Some row not added because email already exist");
	                        } else {
                                if(filter_var($row['Email'], FILTER_VALIDATE_EMAIL)) {
                                    if (in_array($row['Username'], $all_usernames)) {
                                        $msg .= $i.". ".$row['Name']." is not added!<br>";
                                        $this->session->set_flashdata('error', "Some row not added because username already exist");
                                    } else {
                                        if ($row['Dob'] && $row['Jod'] && $row['Gender']) {
                                            if ($this->validGender($row['Gender'])) {
                                                if ($this->validDate($row['Dob']) && $this->validDate($row['Jod'])) {
                                                    $dob = $this->convertDate($row['Dob']);
                                                    $jod = $this->convertDate($row['Jod']);
                                                    $insert_data = array(
                                                        'name'=>$row['Name'],
                                                        'designation'=>$row['Designation'],
                                                        'dob'=>$dob,
                                                        'sex'=>$row['Gender'],
                                                        'religion'=>$row['Religion'],
                                                        'email'=>$row['Email'],
                                                        'phone'=>$row['Phone'],
                                                        'photo'=>'default.png',
                                                        'address'=>$row['Address'],
                                                        'jod'=>$jod,
                                                        'username'=>$row['Username'],
                                                        'password'=> $this->teacher_m->hash($row['Password']),
                                                        'usertypeID' => 2,
                                                        'photo' => 'default.png',
                                                        "create_date" => date("Y-m-d h:i:s"),
                                                        "modify_date" => date("Y-m-d h:i:s"),
                                                        "create_userID" => $this->session->userdata('loginuserID'),
                                                        "create_username" => $this->session->userdata('username'),
                                                        "create_usertype" => $this->session->userdata('usertype'),
                                                        "active" => 1,
                                                    );
                                                    $this->usercreatemail($row['Email'], $row['Username'], $row['Password']);
                                                    $this->teacher_m->insert_teacher($insert_data);
                                                } else {
                                                    $msg .= $i.". ".$row['Name']." is not added!<br>";
                                                    $this->session->set_flashdata('error', "Invalid Date Format!");
                                                }
                                            } else {
                                                $msg .= $i.". ".$row['Name']." is not added!<br>";
                                                $this->session->set_flashdata('error', "Invalid Gender");
                                            }
                                        }
                                    }
                                }
                                else {
                                    $msg .= $i.". ".$row['Name']." is not added! <br>";
                                    $this->session->set_flashdata('error', "Invalid Email Address!");
                                }
	                        }
                        } else {
                            $this->session->set_flashdata('error', "Wrong csv file!");
                            redirect(base_url("bulkimport/index"));
                        }
                        $i++;
                    }
                    if ($msg!="") {
                        $this->session->set_flashdata('msg', $msg);
                    }
                    $this->session->set_flashdata('success', $this->lang->line('import_success'));
                    redirect(base_url("bulkimport/index"));
                } else {
                    $this->session->set_flashdata('error', $this->lang->line('import_error'));
                    redirect(base_url("bulkimport/index"));
                }
            }
        } else {
            $this->session->set_flashdata('error', $this->lang->line('import_error'));
            redirect(base_url("bulkimport/index"));
        }
    }

    public function validDate($date)
    {
        if (strpos($date,'/') !== false) {
            list($month, $day, $year) = explode('/', $date);
            if ((int)$month && (int)$day && (int)$year) {
                return checkdate($month, $day, $year);
            }
        }else{
            return false;
        }
    }
    public function convertDate($date)
    {
        return date("Y-m-d", strtotime($date));
    }

    public function validGender($gender)
    {
        if ($gender && $gender == "Male" || $gender == "Female") {
            return true;
        }
    }

    protected function rules_parent() {
        $rules = array(
            array(
                'field' => 'name',
                'label' => $this->lang->line("parents_guargian_name"),
                'rules' => 'trim|required|xss_clean|max_length[60]'
            ),
            array(
                'field' => 'father_name',
                'label' => $this->lang->line("parents_father_name"),
                'rules' => 'trim|xss_clean|max_length[60]'
            ),
            array(
                'field' => 'mother_name',
                'label' => $this->lang->line("parents_mother_name"),
                'rules' => 'trim|xss_clean|max_length[60]'
            ),
            array(
                'field' => 'father_profession',
                'label' => $this->lang->line("parents_father_name"),
                'rules' => 'trim|xss_clean|max_length[40]'
            ),
            array(
                'field' => 'mother_profession',
                'label' => $this->lang->line("parents_mother_name"),
                'rules' => 'trim|xss_clean|max_length[40]'
            ),
            array(
                'field' => 'email',
                'label' => $this->lang->line("parents_email"),
                'rules' => 'trim|max_length[40]|valid_email|xss_clean|callback_unique_email_parent'
            ),
            array(
                'field' => 'phone',
                'label' => $this->lang->line("parents_phone"),
                'rules' => 'trim|min_length[5]|max_length[25]|xss_clean'
            ),
            array(
                'field' => 'address',
                'label' => $this->lang->line("parents_address"),
                'rules' => 'trim|max_length[200]|xss_clean'
            ),
            array(
                'field' => 'username',
                'label' => $this->lang->line("parents_username"),
                'rules' => 'trim|required|min_length[4]|max_length[40]|xss_clean|callback_lol_username'
            ),
            array(
                'field' => 'password',
                'label' => $this->lang->line("parents_password"),
                'rules' => 'trim|required|min_length[4]|max_length[40]|xss_clean'
            )
        );
        return $rules;
    }

    public function lol_username() {
        $id = htmlentities(escapeString($this->uri->segment(3)));
        if((int)$id) {
            $parents_info = $this->parents_m->get_single_parents(array('parentsID' => $id));
            $tables = array('student' => 'student', 'parents' => 'parents', 'teacher' => 'teacher', 'user' => 'user', 'systemadmin' => 'systemadmin');
            $array = array();
            $i = 0;
            foreach ($tables as $table) {
                $user = $this->student_m->get_username($table, array("username" => $this->input->post('username'), "username !=" => $parents_info->username ));
                if(count($user)) {
                    $this->form_validation->set_message("lol_username", "%s already exists");
                    $array['permition'][$i] = 'no';
                } else {
                    $array['permition'][$i] = 'yes';
                }
                $i++;
            }
            if(in_array('no', $array['permition'])) {
                return FALSE;
            } else {
                return TRUE;
            }
        } else {
            $tables = array('student' => 'student', 'parents' => 'parents', 'teacher' => 'teacher', 'user' => 'user', 'systemadmin' => 'systemadmin');
            $array = array();
            $i = 0;
            foreach ($tables as $table) {
                $user = $this->student_m->get_username($table, array("username" => $this->input->post('username')));
                if(count($user)) {
                    $this->form_validation->set_message("lol_username", "%s already exists");
                    $array['permition'][$i] = 'no';
                } else {
                    $array['permition'][$i] = 'yes';
                }
                $i++;
            }

            if(in_array('no', $array['permition'])) {
                return FALSE;
            } else {
                return TRUE;
            }
        }
    }

    public function unique_email_parent() {
        if($this->input->post('email')) {
            $id = htmlentities(escapeString($this->uri->segment(3)));
            if((int)$id) {
                $parents = $this->parents_m->get_single_parents(array('parentsID' => $id));
                $tables = array('student' => 'student', 'parents' => 'parents', 'teacher' => 'teacher', 'user' => 'user', 'systemadmin' => 'systemadmin');
                $array = array();
                $i = 0;
                foreach ($tables as $table) {
                    $user = $this->parents_m->get_username($table, array("email" => $this->input->post('email'), 'username !=' => $parents->username ));
                    if(count($user)) {
                        $this->form_validation->set_message("unique_email_parent", "%s already exists");
                        $array['permition'][$i] = 'no';
                    } else {
                        $array['permition'][$i] = 'yes';
                    }
                    $i++;
                }
                if(in_array('no', $array['permition'])) {
                    return FALSE;
                } else {
                    return TRUE;
                }
            } else {
                $tables = array('student' => 'student', 'parents' => 'parents', 'teacher' => 'teacher', 'user' => 'user', 'systemadmin' => 'systemadmin');
                $array = array();
                $i = 0;
                foreach ($tables as $table) {
                    $user = $this->student_m->get_username($table, array("email" => $this->input->post('email')));
                    if(count($user)) {
                        $this->form_validation->set_message("unique_email", "%s already exists");
                        $array['permition'][$i] = 'no';
                    } else {
                        $array['permition'][$i] = 'yes';
                    }
                    $i++;
                }

                if(in_array('no', $array['permition'])) {
                    return FALSE;
                } else {
                    return TRUE;
                }
            }
        }
    }

    public function parent_bulkimport() {
        if(isset($_FILES["csvParent"])) {
            $msg = "";
            $config['upload_path'] = "./uploads/csv/";
            $config['allowed_types'] = 'text/plain|text/csv|csv';
            $config['max_size'] = '2048';
            $config['file_name'] = $_FILES["csvParent"]['name'];
            $config['overwrite'] = TRUE;
            $this->load->library('upload', $config);
            if(!$this->upload->do_upload("csvParent")) {
                $this->session->set_flashdata('error', $this->lang->line('import_error'));
                redirect(base_url("bulkimport/index"));
            } else {
                $file_data = $this->upload->data();
                $file_path =  './uploads/csv/'.$file_data['file_name'];
                $column_headers = array("Name", "Father Name", "Mother Name", "Father Profession", "Email", "Phone", "Address", "Username", "Password");
                if ($this->csvimport->get_array($file_path)) {
                    $i = 0;
                    $csv_array = $this->csvimport->get_array($file_path);
                    $csv_col = array();
                    foreach ($csv_array as $row) {
                        /*Validation rules*/
                        $_POST = $this->arrayToPost($row);
                        $this->load->library('form_validation');
                        $rules = $this->rules_parent();
                        $this->form_validation->set_rules($rules);
                        if($this->form_validation->run() !== false){
                            //validation passed
                            if ($i==0) {
                                $csv_col = array_keys($row);
                            }
                            $match = array_diff($column_headers, $csv_col);
                            if (count($match) <= 0) {
                                $insert_data = array(
                                    'name'=>$row['Name'],
                                    'father_name'=>$row['Father Name'],
                                    'mother_name'=>$row['Mother Name'],
                                    'father_profession'=>$row['Father Profession'],
                                    'mother_profession'=>$row['Mother Profession'],
                                    'email'=>$row['Email'],
                                    'phone'=>$row['Phone'],
                                    'photo'=>'defualt.png',
                                    'address'=>$row['Address'],
                                    'username'=>$row['Username'],
                                    'password'=> $this->parents_m->hash($row['Password']),
                                    'usertypeID' => 2,
                                    'photo' => 'default.png',
                                    "create_date" => date("Y-m-d h:i:s"),
                                    "modify_date" => date("Y-m-d h:i:s"),
                                    "create_userID" => $this->session->userdata('loginuserID'),
                                    "create_username" => $this->session->userdata('username'),
                                    "create_usertype" => $this->session->userdata('usertype'),
                                    "active" => 1,
                                );
                                // For Email
                                $this->usercreatemail($this->input->post('email'), $this->input->post('username'), $this->input->post('password'));
                                $this->parents_m->insert_parents($insert_data);
                            } else {
                                $this->session->set_flashdata('error', "Wrong csv file!");
                                redirect(base_url("bulkimport/index"));
                            }
                        } else {
                            $msg .= $row['Name']." is not added! <br>";
                            $this->session->set_flashdata('error', validation_errors());
                        }
                        /*Validation Rules End*/
                        $i++;
                    }
                    if ($msg!="") {
                            $this->session->set_flashdata('msg', $msg);
                    }
                    $this->session->set_flashdata('success', $this->lang->line('import_success'));
                    redirect(base_url("bulkimport/index"));
                } else {
                    $this->session->set_flashdata('error', $this->lang->line('import_error'));
                    redirect(base_url("bulkimport/index"));
                }

            }
        } else {
            $this->session->set_flashdata('error', $this->lang->line('import_error'));
            redirect(base_url("bulkimport/index"));
        }
    }

    public function arrayToPost($data)
    {
        if (is_array($data)) {
            $post = array();
            foreach ($data as $key => $item) {
                $key = preg_replace('/\s+/', '_', $key);
                // convert the string to all lowercase
                $key = strtolower($key);
                $post[$key] = $item;
            }
            return $post;
        }
        return false;
    }

    protected function rules_student() {
        $rules = array(
            array(
                'field' => 'name',
                'label' => $this->lang->line("student_name"),
                'rules' => 'trim|required|xss_clean|max_length[60]'
            ),
            array(
                'field' => 'dob',
                'label' => $this->lang->line("student_dob"),
                'rules' => 'trim|max_length[10]|callback_date_valid|xss_clean'
            ),
            array(
                'field' => 'gender',
                'label' => $this->lang->line("student_sex"),
                'rules' => 'trim|required|max_length[10]|xss_clean'
            ),
            array(
                'field' => 'religion',
                'label' => $this->lang->line("student_religion"),
                'rules' => 'trim|max_length[25]|xss_clean'
            ),
            array(
                'field' => 'email',
                'label' => $this->lang->line("student_email"),
                'rules' => 'trim|max_length[40]|valid_email|xss_clean|callback_unique_email_validation'
            ),
            array(
                'field' => 'phone',
                'label' => $this->lang->line("student_phone"),
                'rules' => 'trim|max_length[25]|min_length[5]|xss_clean'
            ),
            array(
                'field' => 'address',
                'label' => $this->lang->line("student_address"),
                'rules' => 'trim|max_length[200]|xss_clean'
            ),
            array(
                'field' => 'class',
                'label' => $this->lang->line("student_classes"),
                'rules' => 'trim|required|max_length[11]|xss_clean'
            ),
            array(
                'field' => 'section',
                'label' => $this->lang->line("student_section"),
                'rules' => 'trim|required|max_length[11]|xss_clean'
            ),
            array(
                'field' => 'bloodgroup',
                'label' => $this->lang->line("student_bloodgroup"),
                'rules' => 'trim|max_length[5]|xss_clean'
            ),
            array(
                'field' => 'state',
                'label' => $this->lang->line("student_state"),
                'rules' => 'trim|max_length[128]|xss_clean'
            ),
            array(
                'field' => 'country',
                'label' => $this->lang->line("student_country"),
                'rules' => 'trim|max_length[128]|xss_clean'
            ),
            array(
                'field' => 'registrationno',
                'label' => $this->lang->line("student_registerNO"),
                'rules' => 'trim|required|max_length[40]|callback_unique_registerNO|xss_clean'
            ),
            array(
                'field' => 'username',
                'label' => $this->lang->line("student_username"),
                'rules' => 'trim|required|min_length[4]|max_length[40]|xss_clean|callback_lol_username'
            ),
            array(
                'field' => 'password',
                'label' => $this->lang->line("student_password"),
                'rules' => 'trim|required|min_length[4]|max_length[40]|xss_clean'
            )
        );
        return $rules;
    }

    public function unique_registerNO() {
        $schoolyearID = $this->data['siteinfos']->school_year;

        $student = $this->student_m->get_single_student(array("registerNO" => $this->input->post("registrationno"), "classesID" => $this->input->post('class'), 'schoolyearID' => $schoolyearID));

        if(count($student)) {
            $this->form_validation->set_message("unique_registerNO", "The %s is already exists.");
            return FALSE;
        }
        return TRUE;

    }
    public function date_valid($date) {
        if($date) {
            if(strlen($date) <10) {
                $this->form_validation->set_message("date_valid", "%s is not valid dd-mm-yyyy");
                return FALSE;
            } else {
                $arr = explode("/", $date);
                $mm = $arr[0];
                $dd = $arr[1];
                $yyyy = $arr[2];
                if(checkdate($mm, $dd, $yyyy)) {
                    return TRUE;
                } else {
                    $this->form_validation->set_message("date_valid", "%s is not valid dd-mm-yyyy");
                    return FALSE;
                }
            }
        }
        return TRUE;
    }

    public function student_bulkimport() {

        if(isset($_FILES["csvStudent"])) {
            $msg = "";
            $errors = "";
            $config['upload_path'] = "./uploads/csv/";
            $config['allowed_types'] = 'text/plain|text/csv|csv';
            $config['max_size'] = '2048';
            $config['file_name'] = $_FILES["csvStudent"]['name'];
            $config['overwrite'] = TRUE;
            $this->load->library('upload', $config);
            if(!$this->upload->do_upload("csvStudent")) {
                $this->session->set_flashdata('error', $this->lang->line('import_error'));
                redirect(base_url("bulkimport/index"));
            } else {
                $file_data = $this->upload->data();
                $file_path =  './uploads/csv/'.$file_data['file_name'];
                $column_headers = array("Name", "Dob", "Gender", "Religion", "Email", "Phone", "Address", "Class", "Section", "Username", "Password", "Roll", "BloodGroup", "State", "Country", "RegistrationNO");
                if ($this->csvimport->get_array($file_path)) {
                    $i = 0;
                    $csv_array = $this->csvimport->get_array($file_path);
                    $csv_col = array();
                    foreach ($csv_array as $row) {
                        /*Validation rules*/
                        $_POST = $this->arrayToPost($row);
                        $this->load->library('form_validation');
                        $rules = $this->rules_student();
                        $this->form_validation->set_rules($rules);
                        if($this->form_validation->run() !== false){

                            if ($i==0) {
                                $csv_col = array_keys($row);
                            }
                            $match = array_diff($column_headers, $csv_col);
                            if (count($match) <= 0) {
                                $classID = $this->getClass($row['Class']);
                                $sectionID = $this->getSection($classID, $row['Section']);
                                if ($classID=='error' || $sectionID=='error') {
                                    $this->session->set_flashdata('error', $this->lang->line('import_error'));
                                } else {
                                    $dob = $this->convertDate($row['Dob']);
                                    $insert_data = array(
                                        'name'=>$row['Name'],
                                        'dob'=>$dob,
                                        'sex'=>$row['Gender'],
                                        'religion'=>$row['Religion'],
                                        'email'=>$row['Email'],
                                        'phone'=>$row['Phone'],
                                        'photo'=>'defualt.png',
                                        'address'=> $row['Address'],
                                        "bloodgroup" => $row['BloodGroup'],
                                        "state" => $row['State'],
                                        "country" => $row['Country'],
                                        "registerNO" => $row['RegistrationNO'],
                                        'classesID'=>$classID,
                                        'sectionID'=>$sectionID->sectionID,
                                        'roll' => $row['Roll'],
                                        'username'=>$row['Username'],
                                        'password'=> $this->student_m->hash($row['Password']),
                                        'usertypeID'=> 3,
                                        'parentID'=> 0,
                                        'library' => 0,
                                        'hostel' => 0,
                                        'transport' => 0,
                                        'createschoolyearID' => $this->data['siteinfos']->school_year,
                                        'schoolyearID' => $this->data['siteinfos']->school_year,
                                        "create_date" => date("Y-m-d h:i:s"),
                                        "modify_date" => date("Y-m-d h:i:s"),
                                        "create_userID" => $this->session->userdata('loginuserID'),
                                        "create_username" => $this->session->userdata('username'),
                                        "create_usertype" => $this->session->userdata('usertype'),
                                        "active" => 1,
                                    );
                                    // For Email
                                    $this->usercreatemail($this->input->post('email'), $this->input->post('username'), $this->input->post('password'));
                                    $this->student_m->insert_student($insert_data);
                                    $studentID = $this->db->insert_id();

                                    $section = $this->section_m->get_section($sectionID->sectionID);
                                    $classes = $this->classes_m ->get_classes($classID);

                                    if(count($classes)) {
                                        $setClasses = $classes->classes;
                                    } else {
                                        $setClasses = NULL;
                                    }

                                    if(count($section)) {
                                        $setSection = $section->section;
                                    } else {
                                        $setSection = NULL;
                                    }

                                    $studentReletion = $this->studentrelation_m->get_order_by_studentrelation(array('srstudentID' => $studentID, 'srschoolyearID' => $this->data['siteinfos']->school_year));
                                    if(!count($studentReletion)) {
                                        $arrayStudentRelation = array(
                                            'srstudentID' => $studentID,
                                            'srname' => $row['Name'],
                                            'srclassesID' => $classID,
                                            'srclasses' => $setClasses,
                                            'srroll' => $row['Roll'],
                                            'srregisterNO' => $row['RegistrationNO'],
                                            'srsectionID' => $sectionID->sectionID,
                                            'srsection' => $setSection,
                                            'srschoolyearID' => $this->data['siteinfos']->school_year
                                        );
                                        $this->studentrelation_m->insert_studentrelation($arrayStudentRelation);
                                    } else {
                                        $arrayStudentRelation = array(
                                            'srname' => $row['Name'],
                                            'srclassesID' => $classID,
                                            'srclasses' => $setClasses,
                                            'srroll' => $row['Roll'],
                                            'srregisterNO' => $row['RegistrationNO'],
                                            'srsectionID' => $sectionID->sectionID,
                                            'srsection' => $setSection,
                                        );
                                        $this->studentrelation_m->update_studentrelation_with_multicondition($arrayStudentRelation, array('srstudentID' => $studentID, 'srschoolyearID' => $this->data['siteinfos']->school_year));
                                    }
                                }
                            } else {
                                $this->session->set_flashdata('error', "Wrong csv file!");
                                redirect(base_url("bulkimport/index"));
                            }
                        } else {
                           
                            $errors .= validation_errors()."<br>";
                            $msg .= $row['Name']." is not added! <br>";
                            $this->session->set_flashdata('error', validation_errors());
                        }

                        $i++;
                    }
                    if ($msg!="") {
                        $this->session->set_flashdata('msg', $msg);
                    }
                    if ($errors!="") {
                        $this->session->set_flashdata('errors', $errors);
                    }
                    $this->session->set_flashdata('success', $this->lang->line('import_success'));
                    redirect(base_url("bulkimport/index"));
                } else {
                    $this->session->set_flashdata('error', $this->lang->line('import_error'));
                    redirect(base_url("bulkimport/index"));
                }

            }
        } else {
            $this->session->set_flashdata('error', $this->lang->line('import_error'));
            redirect(base_url("bulkimport/index"));
        }
      
    }

    protected function user_rules() {
        $rules = array(
            array(
                'field' => 'name',
                'label' => $this->lang->line("user_name"),
                'rules' => 'trim|required|xss_clean|max_length[60]'
            ),
            array(
                'field' => 'dob',
                'label' => $this->lang->line("user_dob"),
                'rules' => 'trim|required|max_length[10]|xss_clean'
            ),
            array(
                'field' => 'Gender',
                'label' => $this->lang->line("user_sex"),
                'rules' => 'trim|max_length[10]|xss_clean'
            ),
            array(
                'field' => 'religion',
                'label' => $this->lang->line("user_religion"),
                'rules' => 'trim|max_length[25]|xss_clean'
            ),
            array(
                'field' => 'email',
                'label' => $this->lang->line("user_email"),
                'rules' => 'trim|required|max_length[40]|valid_email|xss_clean|callback_unique_email'
            ),
            array(
                'field' => 'phone',
                'label' => $this->lang->line("user_phone"),
                'rules' => 'trim|min_length[5]|max_length[25]|xss_clean'
            ),
            array(
                'field' => 'address',
                'label' => $this->lang->line("user_address"),
                'rules' => 'trim|max_length[200]|xss_clean'
            ),
            array(
                'field' => 'jod',
                'label' => $this->lang->line("user_jod"),
                'rules' => 'trim|required|max_length[10]|xss_clean'
            ),
            array(
                'field' => 'usertype',
                'label' => $this->lang->line("user_usertype"),
                'rules' => 'trim|required|max_length[11]|xss_clean|callback_unique_usertype'
            ),
            array(
                'field' => 'username',
                'label' => $this->lang->line("user_username"),
                'rules' => 'trim|required|min_length[4]|max_length[40]|xss_clean|callback_lol_username'
            ),
            array(
                'field' => 'password',
                'label' => $this->lang->line("user_password"),
                'rules' => 'trim|required|min_length[4]|max_length[40]|xss_clean'
            ),
        );
        return $rules;
    }

    function unique_usertype() {
        if($this->input->post('usertype') == "") {
            $this->form_validation->set_message("unique_usertype", "The %s field is required ");
            return FALSE;
        } else {
            $type = $this->input->post('usertype');
            $query = $this->db->query("SELECT usertypeID FROM `usertype` WHERE `usertype` = '$type'");
            $usertypeID = $query->row('usertypeID');
            $blockuser = array(1, 2, 3, 4);
            if(in_array($usertypeID, $blockuser)) {
                $this->form_validation->set_message("unique_usertype", "The %s field is required.");
                return FALSE;
            }
        }
    }

    public function user_bulkimport() {
        if(isset($_FILES["csvUser"])) {
            $msg = "";
            $errors = "";

            $config['upload_path'] = "./uploads/csv/";
            $config['allowed_types'] = 'text/plain|text/csv|csv';
            $config['max_size'] = '2048';
            $config['file_name'] = $_FILES["csvUser"]['name'];
            $config['overwrite'] = TRUE;
            $this->load->library('upload', $config);
            if(!$this->upload->do_upload("csvUser")) {
                $this->session->set_flashdata('error', $this->lang->line('import_error'));
                redirect(base_url("bulkimport/index"));
            } else {
                $file_data = $this->upload->data();
                $file_path =  './uploads/csv/'.$file_data['file_name'];
                $column_headers = array("Name", "Dob", "Gender", "Religion", "Email", "Phone", "Address", "Jod", "Username", "Password", "Usertype");
                if ($this->csvimport->get_array($file_path)) {
                    $i = 0;
                    $csv_array = $this->csvimport->get_array($file_path);
                    $csv_col = array();
                    foreach ($csv_array as $row) {
                        /*Validation rules*/
                        $_POST = $this->arrayToPost($row);
                        $this->load->library('form_validation');
                        $rules = $this->user_rules();
                        $this->form_validation->set_rules($rules);
                        if($this->form_validation->run() !== false){
                            $usertype = $this->getUsertype($row['Usertype']);
                            if ($i==0) {
                                $csv_col = array_keys($row);
                            }
                            $match = array_diff($column_headers, $csv_col);
                            if (count($match) <= 0) {
                                $dob = $this->convertDate($row['Dob']);
                                $jod = $this->convertDate($row['Jod']);
                                $insert_data = array(
                                    'name'=>$row['Name'],
                                    'dob'=>$dob,
                                    'sex'=>$row['Gender'],
                                    'religion'=>$row['Religion'],
                                    'email'=>$row['Email'],
                                    'phone'=>$row['Phone'],
                                    'address'=>$row['Address'],
                                    'jod'=>$jod,
                                    'photo' => 'default.png',
                                    'username'=>$row['Username'],
                                    'password'=> $this->user_m->hash($row['Password']),
                                    'usertypeID' => $usertype,
                                    "create_date" => date("Y-m-d h:i:s"),
                                    "modify_date" => date("Y-m-d h:i:s"),
                                    "create_userID" => $this->session->userdata('loginuserID'),
                                    "create_username" => $this->session->userdata('username'),
                                    "create_usertype" => $this->session->userdata('usertype'),
                                    "active" => 1,
                                );
                                $this->user_m->insert_user($insert_data);
                                $this->usercreatemail($this->input->post('email'), $this->input->post('username'), $this->input->post('password'));
                            } else {
                                $this->session->set_flashdata('error', "Wrong csv file!");
                                redirect(base_url("bulkimport/index"));
                            }
                        } else {
                            $errors .= validation_errors()."<br>";
                            $msg .= $row['Name']." is not added! <br>";
                            $this->session->set_flashdata('error', validation_errors());
                        }

                        $i++;
                    }
                    if ($msg!="") {
                            $this->session->set_flashdata('msg', $msg);
                    }
                    $this->session->set_flashdata('success', $this->lang->line('import_success'));
                    redirect(base_url("bulkimport/index"));
                } else {
                    $this->session->set_flashdata('error', $this->lang->line('import_error'));
                    redirect(base_url("bulkimport/index"));
                }

            }
        } else {
            $this->session->set_flashdata('error', $this->lang->line('import_error'));
            redirect(base_url("bulkimport/index"));
        }
    }

    public function getUsertype($type)
    {
        if ($type) {
            $query = $this->db->query("SELECT usertypeID FROM `usertype` WHERE `usertype` = '$type'");
            if (empty($query)) {
                return 'error';
            } else {
                return $query->row('usertypeID');
            }
        } else {
            return 0;
        }
    }

    public function getGuardian($username)
    {
        if ($username) {
            $query = $this->db->query("SELECT parentsID FROM `parents` WHERE `username` = '$username'");
            if (empty($query)) {
                return 'error';
            } else {
                return $query->row('parentsID');
            }
        } else {
            return 0;
        }
    }

    protected function book_rules() {
        $rules = array(
            array(
                'field' => 'book',
                'label' => $this->lang->line("book_name"),
                'rules' => 'trim|required|xss_clean|max_length[60]|callback_unique_book'
            ),
            array(
                'field' => 'author',
                'label' => $this->lang->line("book_author"),
                'rules' => 'trim|required|max_length[100]|xss_clean|callback_unique_book'
            ),
            array(
                'field' => 'subject_code',
                'label' => $this->lang->line("book_subject_code"),
                'rules' => 'trim|required|max_length[20]|xss_clean'
            ),
            array(
                'field' => 'price',
                'label' => $this->lang->line("book_price"),
                'rules' => 'trim|required|numeric|max_length[10]|xss_clean'
            ),
            array(
                'field' => 'quantity',
                'label' => $this->lang->line("book_quantity"),
                'rules' => 'trim|required|numeric|max_length[10]|xss_clean'
            ),
            array(
                'field' => 'rack',
                'label' => $this->lang->line("book_rack_no"),
                'rules' => 'trim|required|max_length[60]|xss_clean'
            )
        );
        return $rules;
    }

    public function book_bulkimport() {
        $msg = "";
        $errors = "";

        if(isset($_FILES["csvBook"])) {
            $config['upload_path'] = "./uploads/csv/";
            $config['allowed_types'] = 'text/plain|text/csv|csv';
            $config['max_size'] = '2048';
            $config['file_name'] = $_FILES["csvBook"]['name'];
            $config['overwrite'] = TRUE;
            $this->load->library('upload', $config);
            if(!$this->upload->do_upload("csvBook")) {
                $this->session->set_flashdata('error', $this->lang->line('import_error'));
                redirect(base_url("bulkimport/index"));
            } else {
                $file_data = $this->upload->data();
                $file_path =  './uploads/csv/'.$file_data['file_name'];
                $column_headers = array("Book", "Subject code", "Author", "Price", "Quantity", "Rack");
                if ($this->csvimport->get_array($file_path)) {
                    $i = 0;
                    $csv_array = $this->csvimport->get_array($file_path);
                    $csv_col = array();
                    foreach ($csv_array as $row) {
                        $_POST = $this->arrayToPost($row);
                        $this->load->library('form_validation');
                        $rules = $this->book_rules();
                        $this->form_validation->set_rules($rules);
                        if($this->form_validation->run() !== false) {
                            if ($i==0) {
                              $csv_col = array_keys($row);
                            }
                            $match = array_diff($column_headers, $csv_col);
                            if (count($match) <= 0) {
                                $insert_data = array(
                                    'book'=>$row['Book'],
                                    'subject_code'=>$row['Subject code'],
                                    'author'=>$row['Author'],
                                    'price'=>$row['Price'],
                                    'quantity'=>$row['Quantity'],
                                    'due_quantity'=>0,
                                    'rack'=>$row['Rack']
                                );
                                $this->book_m->insert_book($insert_data);
                            } else {
                                $this->session->set_flashdata('error', "Wrong csv file!");
                                redirect(base_url("bulkimport/index"));
                            }
                        } else {
                            $errors .= validation_errors()."<br>";
                            $msg .= $row['Book']." is not added! <br>";
                            $this->session->set_flashdata('error', validation_errors());
                        }
                        $i++;
                    }
                    if ($msg!="") {
                        $this->session->set_flashdata('msg', $msg);
                    }
                    $this->session->set_flashdata('success', $this->lang->line('import_success'));
                    redirect(base_url("bulkimport/index"));
                } else {
                    $this->session->set_flashdata('error', $this->lang->line('import_error'));
                    redirect(base_url("bulkimport/index"));
                }
            }
        } else {
            $this->session->set_flashdata('error', $this->lang->line('import_error'));
            redirect(base_url("bulkimport/index"));
        }
    }

    public function unique_book() {

        $student = $this->book_m->get_order_by_book(array("book" => $this->input->post("book"), "author" => $this->input->post('author'), "subject_code" => $this->input->post('subject_code')));

        if(count($student)) {
            $this->form_validation->set_message("unique_book", "%s already exists");
            return FALSE;
        }
        return TRUE;

    }

    function valid_number() {
        if($this->input->post('price') && $this->input->post('price') < 0) {
            $this->form_validation->set_message("valid_number", "%s is invalid number");
            return FALSE;
        }
        return TRUE;
    }

    function valid_number_for_quantity() {
        if($this->input->post('quantity') && $this->input->post('quantity') < 0) {
            $this->form_validation->set_message("valid_number_for_quantity", "%s is invalid number");
            return FALSE;
        }
        return TRUE;
    }

    public function getClass($className)
    {
        $usertype = $this->session->userdata("usertype");
        if ($className) {
            $query = $this->db->query("SELECT classesID FROM `classes` WHERE `classes_numeric` = '$className' OR `classes` = '$className'");
            if (empty($query)) {
                return 'error';
            } else {
                return $query->row('classesID');
            }

        } else {
            return "error";
        }
    }
    public function getSection($className, $section)
    {

        if ($className) {
            $query = $this->db->query("SELECT sectionID, section FROM `section` WHERE `classesID` = '$className' AND `section` = '$section'");
            if (empty($query)) {
                return 'error';
            } else {
                return $query->row();
            }

        } else {
            return "error";
        }
    }


}

/* End of file bulkimport.php */
/* Location: .//var/www/html/schoolv2/mvc/controllers/bulkimport.php */
