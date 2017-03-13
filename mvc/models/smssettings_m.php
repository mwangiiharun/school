<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class smssettings_m extends MY_Model {

	function get_order_by_clickatell() {
		$query = $this->db->get_where('smssettings', array('types' => 'clickatell'));
		return $query->result();
	}

	function update_clickatell($array) {
		$this->db->update_batch('smssettings', $array, 'field_names'); 
	}

	function get_order_by_twilio() {
		$query = $this->db->get_where('smssettings', array('types' => 'twilio'));
		return $query->result();
	}

	function update_twilio($array) {
		$this->db->update_batch('smssettings', $array, 'field_names'); 
	}

	function get_order_by_astalking() {
		$query = $this->db->get_where('smssettings', array('types' => 'astalking'));
		return $query->result();
	}

	function update_astalking($array) {
		$this->db->update_batch('smssettings', $array, 'field_names'); 
	}

    function get_order_by_msg91() {
        $query = $this->db->get_where('smssettings', array('types' => 'msg91'));
        return $query->result();
    }

    function update_msg91($array) {
		$this->db->update_batch('smssettings', $array, 'field_names');
	}


}