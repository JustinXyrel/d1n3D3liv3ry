<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Site extends CI_Controller {
	public function index(){
		$data = $this->syter->spawn('dashboard');
		$data['code'] = "";
		$this->load->view('page',$data);
	}
	public function login(){
		// $this->load->helper('site/login_helper');
		$this->load->helper('core/on_screen_key_helper');
		$this->load->helper('dine/login_helper');
		$data = $this->syter->spawn(null,false);

		$data['code'] = makeLoginPage();
		$data['add_css'] = array('css/pos.css','css/onscrkeys.css','css/virtual_keyboard.css');
		$data['add_js'] = array('js/on_screen_keys.js','js/jquery.keyboard.extension-navigation.min.js','js/jquery.keyboard.min.js','js/virtual_keyboard.js');
		$data['load_js'] = 'site/login';
		$data['use_js'] = 'loginJs';
		$this->load->view('login',$data);
	}
	public function go_login(){
		$this->load->model('site/site_model');
		$this->load->model('dine/clock_model');
		$username = $this->input->post('username');
		$password = $this->input->post('password');
		$pin = $this->input->post('pin');

		$bra = $this->input->post('branch');
		$user = $this->site_model->get_user_details(null,$username,$password,$pin);
		$error_msg = null;
		$path = null;
		$send_redirect = null;
		if(!isset($user->id)){
			$error_msg = "Error! Wrong login!";
		}
		else{
			$session_details['user'] = array(
				"id"=>$user->id,
				"username"=>$user->username,
				"fname"=>$user->fname,
				"lname"=>$user->lname,
				"mname"=>$user->mname,
				"suffix"=>$user->suffix,
				"full_name"=>$user->fname." ".$user->mname." ".$user->lname." ".$user->suffix,
				"role_id"=>$user->user_role_id,
				"role"=>$user->user_role,
				"access"=>$user->access,
			);
			if ($user->user_role_id == '1' || $user->user_role_id == '2') {
				$session_details['manager_privs'] = array('method'=>'main','id'=>$user->id);
			}

			// $check_in = $this->clock_model->get_user_today_in($user->id);
			// if (empty($check_in)) {
			// 	$send_redirect = base_url()."cashier";
			// } else {
			// 	$send_redirect = base_url()."cashier";
			// 	$conv_time = date("H:i:s",strtotime($check_in[max(array_keys($check_in))]->check_in));
			// 	$session_details['today_in'] = $conv_time;
			// }

			// $check_in = $this->clock_model->get_user_today_in($user->id);
			if ($user->user_role_id == '1') //administrator
				$send_redirect = base_url();
			else if($user->user_role_id == '9')	//store manager	
				$send_redirect = base_url();
			else if($user->user_role_id == '5')	//agents
				$send_redirect = base_url()."cashier";
			else if($user->user_role_id == '8')	//agents
				$send_redirect = base_url()."agents";
			else
				$error_msg = "Error! Wrong login!";
			
			// else {
			// 	$send_redirect = base_url()."cashier";
			// 	$conv_time = date("H:i:s",strtotime($check_in[max(array_keys($check_in))]->check_in));
			// 	$session_details['today_in'] = $conv_time;
			// }
			

			$this->session->set_userdata($session_details);
		}
		echo json_encode(array('error_msg'=>$error_msg,'redirect_address'=>$send_redirect));
	}
	public function go_logout(){
		$this->session->sess_destroy();
		redirect(base_url()."login",'refresh');
	}
	public function site_alerts(){
		$site_alerts = array();
		$alerts = array();
		if($this->session->userdata('site_alerts')){
			$site_alerts = $this->session->userdata('site_alerts');
		}

		foreach ($site_alerts as $alert) {
			$alerts[] = $alert;
		}
		echo json_encode(array("alerts"=>$alerts));
	}
	public function clear_site_alerts(){
		if($this->session->userdata('site_alerts'))
			$this->session->unset_userdata('site_alerts');
	}
}
