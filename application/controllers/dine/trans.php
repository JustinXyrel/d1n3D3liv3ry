<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Trans extends CI_Controller {
	public function __construct()
	{
		parent::__construct();

		
		$this->load->model('dine/trans_model');
		$this->load->model('site/site_model');
		$this->load->helper('dine/trans_helper');
	}
	public function index()
	{
		sess_clear('ord_cart');
		sess_clear('ord_mod');
		sess_clear('trans_header');

		$data = $this->syter->spawn('trans');

        $data['page_title'] = "New Transaction";

		$data['add_js'] = array('js/plugins/wizard-steps/jquery.cookie-1.3.1.js','js/plugins/wizard-steps/jquery.steps.min.js','js/plugins/typeaheadmap/typeaheadmap.js');
        $data['add_css'] = array('css/wizard-steps/jquery.steps.css','js/plugins/typeaheadmap/typeaheadmap.css');

        $data['load_js'] = "dine/trans.php";
        $this->load->model('dine/menu_model');
        $menus = $this->menu_model->get_menus();
        $data['code'] = trans_display(null, $menus);
		$data['use_js'] = "transLoadJS";
        $this->load->view('page',$data);
	}
	public function customer_search()
	{
		$search = $this->input->post('search');
        $this->load->model('dine/trans_model');
        $found = $this->trans_model->get_customer_search($search);
        $customers = array();
        if(count($found) > 0 ){
            foreach ($found as $res) {
                $customers[] = array('key'=>$res->phone,'value'=>$res->cust_id);
            }
        }
        echo json_encode($customers);
	}
	public function get_customer_details_db()
	{
		$cust_id = $this->input->post('cust_id');
		$this->load->model('dine/customers_model');
        $customer = $this->customers_model->get_customer($cust_id);
        $items = $customer[0];
        echo json_encode($items);
	}
	public function get_customer_address()
	{	
		$cust_id = $this->input->post('cust_id');
		$this->load->helper('dine/trans_helper');
		$this->load->model('dine/customers_model');
		$item = $this->customers_model->get_customer_address($cust_id, null);
		$data['code'] = customer_address_tbl($cust_id, $item);	 
		echo json_encode($data);
	}
	public function pop_add_modifier()
	{
		$this->load->model('dine/menu_model');
		$this->load->model('dine/trans_model');
		$mod_grp = $this->menu_model->get_menu_modifiers($_GET['id'],null, null);
	    $mod_items = array();
	    if($mod_grp != null)
	    {
	    	foreach($mod_grp as $var)
	    	{
				$mod_items[$var->mod_group_name] = $this->trans_model->get_menu_modifier_items($var->mod_group_id);
	    	}
	    }
		$data['code'] = pop_add_modifier_form($mod_items, $_GET['row']);
		$data['load_js'] = "dine/trans.php";
		$data['use_js'] = "ModifierJS";
		$this->load->view('load',$data);
	}
	public function pop_edit_menu_item($id)
	{
		$item = sess('ord_cart');
		$data['code'] = pop_edit_menu_item_form($item[$id], $id, $item);
		$this->load->view('load',$data);
	}
	public function pop_edit_menu_item_db($id)
	{
		$item = sess('ord_cart');
		$qty = $this->input->post('qty');
		echo json_encode(array('qty'=>$qty, 'item'=>$item[$id]));
	}
	public function pop_new_address()
	{
		$data['code'] = pop_new_address_form();
		$this->load->view('load',$data);
	}
	public function pop_new_address_db()
	{
		$items = array(
			'street_no' => $this->input->post('street_no'),
			'street_address' => $this->input->post('street_address'),
			'zip' => $this->input->post('zip'),
			'region' => $this->input->post('region'),
			'city' => $this->input->post('city'),
			'base_location' => $this->input->post('base_location'),
		);

		$this->load->model('dine/customers_model');
		$id = $this->customers_model->add_customer_address($items);
		$msg = "Added Address: ". ucwords($items['street_address']).", ".ucwords($items['city']) .", ".ucwords($items['region']);

		$this->make->sRow(array('class'=>'t-rows', 'ref'=>$id,'id'=>'row-'.$id));
            $this->make->td(ucwords($this->input->post('base_location')));
            $this->make->td(ucwords($this->input->post('street_no')));
            $this->make->td(ucwords($this->input->post('street_address')));
	        $this->make->td(ucwords($this->input->post('city')));
            $this->make->td(ucwords($this->input->post('region')));
            $this->make->td(ucwords($this->input->post('zip')));

        $this->make->eRow();
        $row = $this->make->code();
        echo json_encode(array('row'=>$row, 'msg'=>$msg, 'items'=>$items));		
	}
	public function pop_add_modifier_db(){
		echo json_encode(array('msg'=>'modifiers submitter'));	
	}
	public function check_menu_schedule($id){
		date_default_timezone_set('Asia/Manila');

		$this->load->model('dine/menu_model');
		$schs = $this->menu_model->get_menu_schedule_details($id);
		$currTime = strtotime(date('H:i:s'));
		$currDay = strtolower(date('D'));
	    $msg = 'Sorry, product is unavailable at this time.';
		foreach($schs as $key => $val)
		{	
			$strTime = strtotime($val->time_on);
			$endTime = strtotime($val->time_off);

			if($currDay == $val->day)
			{
				if ((($currTime < $strTime) && ($currTime > $endTime)) || (($currTime > $strTime) && ($currTime < $endTime)))
				{
					 $msg='';
				}
			}
		}
		echo json_encode(array('msg'=>$msg));
	}
	public function check_menu_availability($menu_id=null, $mod_id=null){
		$this->load->model('dine/menu_model');
		$msg = $error = '';
		if($menu_id != null)
		{
			$menu = $this->menu_model->get_branch_menus($menu_id);
			if($menu[0]->inactive == 1)
			{
				$msg = 'Sorry. Item '. $menu[0]->menu_name.' is not available from the selected branch.';
				$error = 'error';					
			}
		}
		if($mod_id !=null)
		{

		}
		echo json_encode(array('msg'=>$msg, 'error'=>$error));

	}
	public function menu_item_db()
	{
		$id = $this->input->post('menu-drop');
		$this->load->helper('dine/trans_helper');
		$this->load->model('dine/menu_model');
	    $menus = $this->menu_model->get_menus($id,null,false);
      	$mod_grp = $this->menu_model->get_menu_modifiers($id,null, null);

      	$code = add_menu_item_tbl($menus, $mod_grp);
      	$msg = 'Item Added';
		echo json_encode(array('code'=> $code, 'msg'=>$msg));
	}
	public function check_rows_from_wagon()
	{
		$name = $this->input->post('name');
		$line_id = $this->input->post('line_id');
		$mod_id = $this->input->post('mod_id');
		$wagon_ord_mod = sess($name);
		$wagon_ord_item = sess('ord_cart');

		foreach($wagon_ord_item as $cart_line_id => $row)
		    foreach ($wagon_ord_mod as $mod)
		    {
		    	if(isset($row['line_id']) && $row['line_id'] == $line_id)
		    	{
			        if (isset($mod['line_id']) && $mod['mod_id'] == $mod_id && $mod['line_id'] == $line_id)
			        { 
			            echo json_encode(array('error'=>'Modifier item already exist.'));
					    return false;
					}
				}
			}

		echo json_encode(array('error'=>null));
	}
	public function check_menu_mod_items($line_id){
		$wagon_ord_mod = sess('ord_mod');
		$to_delete = array();
		    foreach ($wagon_ord_mod as $key => $mod)
		    {
		    	if(isset($line_id) && $mod['line_id'] == $line_id)
		    	{
			       $to_delete[]=$key;
				}
			}

		echo json_encode($to_delete);
	}

	public function branches_db()
	{
		$id = $this->input->post('addr_id');
		$addr_arr = $this->input->post('addr_arr');	
		$addr_arr = explode(" ", $addr_arr);
		
		$this->load->model('dine/trans_model');
		$this->load->helper('dine/trans_helper');
		$branches = $this->trans_model->get_branches_list(null);
		$branch_list = array();
		$under_vicinity = false;
		foreach($branches as $key=>$br)
		{
			$br_arr = explode(" ", $br->addr);
			$result = array_intersect($addr_arr , $br_arr ); //matched elements
			$num = count($result);
			if($num > 0)
			{
				$under_vicinity = true;
			}
			$branch_list[$key] = $num;	
		}

		array_multisort($branch_list, SORT_DESC, $branches);
		$code = branches_list_tbl($branches, $under_vicinity);
		echo json_encode(array('code'=>$code, 'under_vicinity'=>$under_vicinity));
	}
	public function branch_search()
	{
		$search = $this->input->post('search');
        $this->load->model('dine/trans_model');
        $found = $this->trans_model->get_branch_search($search);
        $branch = array();
        if(count($found) > 0 ){
            foreach ($found as $res) {
                $branch[] = array('key'=>$res->branch_code.": ".$res->branch_name,'value'=>$res->branch_id);
            }
        }
        echo json_encode($branch);
	}
	public function get_found_branch()
	{
		$id = $this->input->post('id');
		$this->load->helper('dine/trans_helper');
		$branches = $this->trans_model->get_branches_list($id);
		$code = branches_list_tbl($branches);
		echo json_encode(array('code'=>$code));
	}
	public function get_order_details()
	{
		$this->load->model('dine/customers_model');
		$this->load->model('dine/trans_model');

		$cust_id = $this->input->post('cust_id');
		$addr_id = $this->input->post('addr_id');
		$branch_id = $this->input->post('branch_id');

		$cust_details = $this->customers_model->get_customer($cust_id);
		$cust_address = $this->customers_model->get_customer_address($cust_id, $addr_id);

		$branch = $this->trans_model->get_branches_list($branch_id);
		

		$total_amount = $this->total_amount();
		$this->load->helper('dine/trans_helper');

		
		$code = makeOrderSummary($total_amount, $cust_address[0], $cust_details[0], $branch[0]);
		echo json_encode(array('code'=>$code));
	}
    public function total_amount()
    {
    	$total_amount = 0;
    	$cart = sess('ord_cart');
    	foreach($cart as $key=>$item){
    		$total_amount+=$item['cost'];
    	}
    	return $total_amount;
    }
    public function submit_trans_db($asJson=true,$submit=null)
    {
    	$this->load->model('dine/trans_model');
        $this->load->model('dine/cashier_model');
        $this->load->model('core/ref_model');
		$this->load->model('site/site_model');
       
		$trans_cart = sess('ord_cart');
        $trans_mod_cart = sess('ord_mod');
        $total_amount = $this->total_amount();
        $error = null;
        $act = null;
        $dateNow = $this->site_model->get_db_now('sql');
 		$counter = sess('trans_header');

        if(count($counter) > 0){

            $trans_sales = array(
                "user_id"       => 1,
                "shift_id"      => 1,
                "terminal_id"   => 1,
                "type"          =>'delivery',
                "datetime"      => $dateNow,
                "total_amount"  => $total_amount,
                "memo"          => null,
                "customer_id"	=> $counter[0]['cust_id'],
                "address_id"	=>	$counter[0]['addr_id'],
                "branch_id"		=>	$counter[0]['branch_id']
            );
            
         
			 $sales_id = $this->cashier_model->add_trans_sales($trans_sales);
	            
	            $act="add";          
	            $trans_sales_menu = array();
	            foreach ($trans_cart as $trans_id => $v) {
	                $trans_sales_menu[] = array(
	                    "sales_id" => $sales_id,
	                    "line_id" => $v['line_id'],
	                    "menu_id" => $v['menu_id'],
	                    "price" => $v['cost'],
	                    "qty" => $v['qty'],
	                    "discount"=> 0
	                );
	            }

	            $this->cashier_model->add_trans_sales_menus($trans_sales_menu);
	            $prep_time = $this->trans_model->get_preparation_time($sales_id);
	            $trans_sales_menu_modifiers = array();
		                
		        if(count($trans_mod_cart) > 0){
		                foreach ($trans_mod_cart as $trans_mod_id => $m) {
	                       $trans_sales_menu_modifiers[] = array(
	                            "sales_id" => $sales_id,
	                            "line_id" => $m['line_id'],
	                            "menu_id" => $m['menu_id'],
	                            "mod_id" => $m['mod_id'],
	                            "price" => $m['cost'],
	                            "qty" => 1,
	                            "discount"=> 0
	                        );   
		                }

		                if(count($trans_sales_menu_modifiers) > 0)
		                    $this->cashier_model->add_trans_sales_menu_modifiers($trans_sales_menu_modifiers);
		        }

		        $unique_code = $this->generate_delivery_code( $sales_id );
		        $trans_type = CALL_CENTER_TRANS;
				$ref = $this->ref_model->get_next_ref($trans_type);
			    $this->ref_model->db->trans_start();
				    $this->ref_model->save_ref($trans_type,$ref);
				    $this->cashier_model->update_trans_sales(array('prep_time'=>$prep_time[0]->prep_time, 'trans_ref'=>$ref,'paid'=>1, 'delivery_code' => $unique_code),$sales_id);   
		        $this->ref_model->db->trans_complete();

		       	echo json_encode(array('error'=>$error,'act'=>$act,'id'=>$sales_id, 'uniq_code'=>$unique_code, 'counter'=>$counter));
    	}else{
    		$error = 'Unable to submit transaction';
		   	echo json_encode(array('error'=>$error, 'counter'=>$counter));
    	}

    }
    public function generate_delivery_code($sales_id){
    	$pass = substr(md5($sales_id . (uniqid(mt_rand(), true))) , 0, 6);
		return $pass;
    }
    public function pop_display_code($uniq){
    	$this->load->helper('dine/trans_helper');
    	echo makeDisplayCode($uniq);
    }
    public function trans_message($message=null){
    	$this->load->helper('dine/trans_helper');
    	echo makeDisplayAlertMsg();
    }
}