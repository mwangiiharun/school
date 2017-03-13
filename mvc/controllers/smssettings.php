
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Smssettings extends Admin_Controller {
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
	function __construct () {
		parent::__construct();
		$this->load->model("smssettings_m");
		$language = $this->session->userdata('lang');
		$this->lang->load('smssettings', $language);
		if(config_item('demo')) {
            $this->session->set_flashdata('error', 'In demo SMS setting module is disable!');
            redirect(base_url('dashboard/index'));
        }
	}

	protected function rules_clickatell() {
		$rules = array(
			array(
				'field' => 'clickatell_username', 
				'label' => $this->lang->line("smssettings_username"), 
				'rules' => 'trim|required|xss_clean|max_length[255]'
			), 
			array(
				'field' => 'clickatell_password', 
				'label' => $this->lang->line("smssettings_password"),
				'rules' => 'trim|required|xss_clean|max_length[255]'
			),
			array(
				'field' => 'clickatell_api_key', 
				'label' => $this->lang->line("smssettings_api_key"), 
				'rules' => 'trim|required|xss_clean|max_length[255]'
			),
		);
		return $rules;
	}

	protected function rules_twilio() {
		$rules = array(
			array(
				'field' => 'twilio_accountSID', 
				'label' => $this->lang->line("smssettings_accountSID"), 
				'rules' => 'trim|required|xss_clean|max_length[255]'
			), 
			array(
				'field' => 'twilio_authtoken', 
				'label' => $this->lang->line("smssettings_authtoken"),
				'rules' => 'trim|required|xss_clean|max_length[255]'
			),
			array(
				'field' => 'twilio_fromnumber', 
				'label' => $this->lang->line("smssettings_fromnumber"), 
				'rules' => 'trim|required|xss_clean|max_length[255]'
			),
		);
		return $rules;
	}
	protected  function a_stalking(){}

	protected function rules_astalking() {
		$rules = array(
			array(
				'field' => 'astalking_username',
				'label' => $this->lang->line("smssettings_username"),
				'rules' => 'trim|required|xss_clean|max_length[255]'
			),
			array(
				'field' => 'astalking_password',
				'label' => $this->lang->line("smssettings_api_key"),
				'rules' => 'trim|required|xss_clean|max_length[255]'
			)
		);
		return $rules;
	}

	protected function rules_msg91() {
		$rules = array(
			array(
				'field' => 'msg91_authKey',
				'label' => $this->lang->line("smssettings_authkey"),
				'rules' => 'trim|required|xss_clean|max_length[255]'
			),
			array(
				'field' => 'msg91_senderID',
				'label' => $this->lang->line("smssettings_senderID"),
				'rules' => 'trim|required|xss_clean|max_length[255]'
			)
		);
		return $rules;
	}

	public function index() {

		$clickatell_bind = array();
		$get_clickatells = $this->smssettings_m->get_order_by_clickatell();
		foreach ($get_clickatells as $key => $get_clickatell) {
			$clickatell_bind[$get_clickatell->field_names] = $get_clickatell->field_values;
		}
		$this->data['set_clickatell'] = $clickatell_bind;

		$twilio_bind = array();
		$get_twilios = $this->smssettings_m->get_order_by_twilio();
		foreach ($get_twilios as $key => $get_twilio) {
			$twilio_bind[$get_twilio->field_names] = $get_twilio->field_values;
		}
		$this->data['set_twilio'] = $twilio_bind;

		$astalking_bind = array();
		$get_astalkings = $this->smssettings_m->get_order_by_astalking();
		foreach ($get_astalkings as $key => $get_astalking) {
			$astalking_bind[$get_astalking->field_names] = $get_astalking->field_values;
		}
		$this->data['set_astalking'] = $astalking_bind;

        $msg91_bind = array();
		$get_msg91s = $this->smssettings_m->get_order_by_msg91();
		foreach ($get_msg91s as $key => $get_msg91) {
			$msg91_bind[$get_msg91->field_names] = $get_msg91->field_values;
		}
		$this->data['set_msg91'] = $msg91_bind;

		if($_POST) {
			$type = $this->input->post('type');
			if($type == 'clickatell') {
				$this->data['clickatell'] = 1;
				$this->data['twilio'] = 0;
				$this->data['astalking'] = 0;
				$this->data['msg91'] = 0;

				$rules = $this->rules_clickatell();
				$this->form_validation->set_rules($rules);
				if ($this->form_validation->run() == FALSE) {
					$this->data["subview"] = "smssettings/index";
					$this->load->view('_layout_main', $this->data);
				} else {

					$username = $this->input->post('clickatell_username');
					$password = $this->input->post('clickatell_password');
					$api_key = $this->input->post('clickatell_api_key');

					$array = array(
					   array(
					      'field_names' => 'clickatell_username',
					      'field_values' => $username
					   ),
					   array(
					      'field_names' => 'clickatell_password',
					      'field_values' => $password
					   ),
					   array(
					      'field_names' => 'clickatell_api_key',
					      'field_values' => $api_key
					   )
					);

					$this->smssettings_m->update_clickatell($array);
					$this->data["subview"] = "smssettings/index";
					$this->load->view('_layout_main', $this->data);
				}
			} elseif($type == 'twilio') {
				$this->data['clickatell'] = 0;
				$this->data['twilio'] = 1;
				$this->data['astalking'] = 0;
                $this->data['msg91'] = 0;

				$rules = $this->rules_twilio();
				$this->form_validation->set_rules($rules);
				if ($this->form_validation->run() == FALSE) {
					$this->data["subview"] = "smssettings/index";
					$this->load->view('_layout_main', $this->data);
				} else {
					$accountSID = $this->input->post('twilio_accountSID');
					$authtoken = $this->input->post('twilio_authtoken');
					$fromnumber = $this->input->post('twilio_fromnumber');

					$array = array(
					   array(
					      'field_names' => 'twilio_accountSID',
					      'field_values' => $accountSID
					   ),
					   array(
					      'field_names' => 'twilio_authtoken',
					      'field_values' => $authtoken
					   ),
					   array(
					      'field_names' => 'twilio_fromnumber',
					      'field_values' => $fromnumber
					   )
					);

					$this->smssettings_m->update_twilio($array);
					$this->data["subview"] = "smssettings/index";
					$this->load->view('_layout_main', $this->data);
				}
			} elseif($type == 'astalking') {
				$this->data['clickatell'] = 0;
				$this->data['twilio'] = 0;
				$this->data['astalking'] = 1;
                $this->data['msg91'] = 0;

				$rules = $this->rules_astalking();
				$this->form_validation->set_rules($rules);
				if ($this->form_validation->run() == FALSE) {
					$this->data["subview"] = "smssettings/index";
					$this->load->view('_layout_main', $this->data);
				} else {
					$username = $this->input->post('astalking_username');
					$password = $this->input->post('astalking_password');

					$array = array(
					   array(
					      'field_names' => 'astalking_username',
					      'field_values' => $username
					   ),
					   array(
					      'field_names' => 'astalking_password',
					      'field_values' => $password
					   )
					);

					$this->smssettings_m->update_astalking($array);
					$this->data["subview"] = "smssettings/index";
					$this->load->view('_layout_main', $this->data);
				}
			} elseif($type == 'msg91') {
                $this->data['clickatell'] = 0;
                $this->data['twilio'] = 0;
                $this->data['astalking'] = 0;
                $this->data['msg91'] = 1;

                $rules = $this->rules_msg91();
                $this->form_validation->set_rules($rules);
                if ($this->form_validation->run() == FALSE) {
                    $this->data["subview"] = "smssettings/index";
                    $this->load->view('_layout_main', $this->data);
                } else {
                    $authKey = $this->input->post('msg91_authKey');
                    $senderID = $this->input->post('msg91_senderID');

                    $array = array(
                        array(
                            'field_names' => 'msg91_authKey',
                            'field_values' => $authKey
                        ),
                        array(
                            'field_names' => 'msg91_senderID',
                            'field_values' => $senderID
                        )
                    );

                    $this->smssettings_m->update_msg91($array);
                    $this->data["subview"] = "smssettings/index";
                    $this->load->view('_layout_main', $this->data);
                }
            }

		} else {
			$this->data['clickatell'] = 1;
			$this->data['twilio'] = 0;
			$this->data['astalking'] = 0;
			$this->data['msg91'] = 0;

			$this->data["subview"] = "smssettings/index";
			$this->load->view('_layout_main', $this->data);
		}
	}
}

/* End of file student.php */
/* Location: .//D/xampp/htdocs/school/mvc/controllers/student.php */