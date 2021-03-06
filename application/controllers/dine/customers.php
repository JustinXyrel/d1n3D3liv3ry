<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Customers extends CI_Controller {
	public function __construct()
	{
		parent::__construct();
		$this->load->model('dine/customers_model');
		$this->load->model('site/site_model');
		$this->load->helper('dine/customers_helper');
	}
	public function index()
	{
		$data = $this->syter->spawn('customers');

		$customers = $this->customers_model->get_customer();
		$data['code'] = customers_display($customers);

		$this->load->view('page',$data);
	}
	public function cust_setup($cust_id = null)
	{
		$data = $this->syter->spawn();

		if (is_null($cust_id)){
			$data['page_title'] = fa('fa-user fa-fw')." Add New Customer";
		}else {
			$customer = $this->customers_model->get_customer($cust_id);
			$customer = $customer[0];
			if (!empty($customer->cust_id)) {
				// $data['page_title'] = fa('fa-user fa-fw')." ".iSetObj($customer,'lname'.', '.'fname');
				$data['page_title'] = fa('fa-user fa-fw')." ".iSetObj($customer,'lname').", ".iSetObj($customer,'fname')." ".iSetObj($customer,'suffix');
				// if (!empty($customer->update_date))
					// $data['page_subtitle'] = "Last updated ".$customer->update_date;

			} else {
				header('Location:'.base_url().'customers/cust_setup');
			}
		}

		$data['code'] = customers_form_container($cust_id);
		$data['load_js'] = "dine/customers.php";
		$data['use_js'] = "customerFormContainerJs";

		$this->load->view('page',$data);
	}
	public function customer_load($cust_id = null)
	{
		$details = array();
		if (!is_null($cust_id))
			$item = $this->customers_model->get_customer($cust_id);
		if (!empty($item))
			$details = $item[0];

		$data['code'] = customers_details_form($details,$cust_id);
		$data['load_js'] = "dine/customers.php";
		$data['use_js'] = "customerDetailsJs";
		$this->load->view('load',$data);
	}

	public function customer_details_db($fromDel=false)
	{
		$this->load->model('dine/customers_model');
		// if (!$this->input->post())
			// header("Location:".base_url()."items");

		$items = array(
			'fname' => $this->input->post('fname'),
			'lname' => $this->input->post('lname'),
			'mname' => $this->input->post('mname'),
			'suffix' => $this->input->post('suffix'),
			// 'phone' => $this->input->post('phone'),
			'email' => $this->input->post('email'),
			// 'tax_exempt' => (int)$this->input->post('tax_exempt'),
			// 'street_no' => $this->input->post('street_no'),
			// 'street_address' => $this->input->post('street_address'),
			// 'city' => $this->input->post('city'),
			// 'region' => $this->input->post('region'),
			// 'zip' => $this->input->post('zip'),
			// 'landmark' => $this->input->post('landmark'),
			// 'inactive' => (int)$this->input->post('inactive'),
		);

		if($fromDel){
			unset($items['tax_exempt']);
			unset($items['inactive']);
		}
		
		$cust_id = $up_count = '';

		if ($this->input->post('cust_id')) 
		{
			$cust_id = $this->input->post('cust_id');
			$this->customers_model->update_customer($items,$cust_id);
			$msg = "Updated Customer: ".ucwords($items['fname'])." ".ucwords($items['lname']);
		}else{
			$cust_id = $this->customers_model->add_customer($items);
			$msg = "Added New Customer: ".ucwords($items['fname'])." ".ucwords($items['lname']);
		}

		$new_address = sess('cust_address');
		if(!empty($new_address))
		{
			foreach($new_address as $key)
			{
				$items = array(
					'cust_id' => $cust_id,
					'street_no' => $key['street_no'],
					'street_address' => $key['street_address'],
					'zip' => $key['zip'],
					'region' => $key['region'],
					'city' => $key['city'],
					'landmark' => $key['landmark'],
				);
				
				$id = $this->customers_model->add_customer_address($items);
			}
		}

		$new_contacts = sess('phone_nos');

		if(!empty($new_contacts))
		{
			foreach($new_contacts as $key)
			{
				$items = array(
					'cust_id' => $cust_id,
					'phone_no' => $key['phone_no'],
				);
				$id = $this->customers_model->add_phone_number($items);
			}
		}

		$default_no = sess('default_no');
		if(!empty($default_no))
		{

			$key = end(array_keys($default_no));
	        $default_no = $default_no[$key];
	        
	        //remove previous default Number
	        $items = array('default_no'=>0);
	        $wh = array('default_no' => 1);
	        $id = $this->customers_model->update_phone_number($items, $cust_id, $wh);
		
			//set NEW default number
	        $items = array('default_no'=>1);	
	        $wh = array('default_no' => 0, 'phone_no'=>$default_no['phone_no']);
	        $id = $this->customers_model->update_phone_number($items, $cust_id,  $wh);
    		
        }

		sess_clear('phone_nos');
		sess_clear('cust_address');
		sess_clear('default_no');

		echo json_encode(array('id'=>$cust_id,'msg'=>$msg));
	}
	#customers menu
    public function cashier_customers(){
        $this->load->model('site/site_model');
		$this->load->helper('core/on_screen_key_helper');
        $this->load->model('dine/customers_model');
        $this->load->helper('dine/customers_helper');
        $data = $this->syter->spawn(null);
        $data['code'] = customersPage();
        // $data['add_css'] = 'css/cashier.css';
		$data['add_css'] = array('css/pos.css','css/onscrkeys.css','css/virtual_keyboard.css', 'css/cashier.css');	
		$data['add_js'] = array('js/on_screen_keys.js','js/jquery.keyboard.extension-navigation.min.js','js/jquery.keyboard.min.js');
        $data['load_js'] = 'dine/customers.php';
        $data['use_js'] = 'customersJs';
        $data['noNavbar'] = true; /*Hides the navbar. Comment-out this line to display the navbar.*/
        $this->load->view('cashier',$data);
    }
	public function load_customer_details()
	{
		$details = array();
		// $telno = $this->input->post('telno');
		$telno = str_replace('-', '', $this->input->post('telno'));
		
		if (!is_null($telno))
			$item = $this->customers_model->get_customer_info($telno);
		if (!empty($item))
			$details = $item[0];

		$data['code'] = customers_details_form($details,$details->cust_id);
		$data['load_js'] = "dine/customers.php";
		$data['use_js'] = "customerDetailsJs";
		$this->load->view('load',$data);
	}
	public function customers_list()
	{
		$customers = $this->customers_model->get_customer();

		$data['code'] = customersList($customers);
		$data['load_js'] = "dine/customers.php";
		$data['use_js'] = "customerDetailsJs";
		$this->load->view('load',$data);
	}
	public function validate_phone_number(){
		$telno = $this->input->post('telno');
		$cust_count = 0;
		
		$cust_count = $this->customers_model->get_all_customer_count($telno);
		$cust_det = $cust_count[0];
		
		if(empty($telno)){
			echo "empty";
		}else if($cust_det->total_count == 0){
			echo "none";
		}else if($cust_det->total_count > 0){
			echo "success||".$telno;
		}

	}
}