<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cashier extends CI_Controller {
	#CONTROL PANEL
    public function __construct(){
        parent::__construct();
        $this->load->model('dine/cashier_model');
        $this->load->model('dine/customers_model');
        $this->load->helper('dine/cashier_helper');
        date_default_timezone_set('Asia/Manila');

    }
    public function index(){
        $this->load->helper('core/on_screen_key_helper');
        $data = $this->syter->spawn(null);
        sess_clear('trans_mod_cart');
        sess_clear('trans_cart');
        sess_clear('counter');
        sess_clear('trans_disc_cart');
        sess_clear('trans_vip_disc_cart');
        sess_clear('trans_charge_cart');
        sess_clear('trans_instruction');
        sess_clear('trans_order_date_time');

        $reasons = $this->cashier_model->get_reasons_void(false);
        $reasons_complaint = $this->cashier_model->get_reasons_complaint(false);

        $data['code'] = indexPage($reasons, $reasons_complaint);
        $data['add_css'] = array('css/cashier.css','css/onscrkeys.css', 'css/daterangepicker/daterangepicker-bs3.css', 'css/agents.css', 'css/datepicker.css');
        $data['add_js'] = array('js/on_screen_keys.js', 'js/plugins/daterangepicker/daterangepicker.js', 'js/bootstrap-datepicker.js');
        $data['load_js'] = 'dine/cashier.php';
        $data['use_js'] = 'controlPanelJs';
        $this->load->view('cashier',$data);
    }

    public function set_notification()
    {
        $this->load->model('dine/transorder_model');
        
        $agent = sess('user');
        $args = array('type_id'=>40, 
                    'confirmed'=>array(0,3), 
                    'void_ref'=>NULL,  
                    'user_id'=>$agent['id'], 
                    'trans_sales.inactive'=>0, 
                    "trans_sales.trans_ref  IS NOT NULL" => array('use'=>'where',
                                                                  'val'=>null,
                                                                  'third'=>false));
        //get all today's data

        $args["DATE_FORMAT(trans_sales.datetime,'%Y-%m-%d')"] = date('Y-m-d');
        
        $pending = $this->transorder_model->get_transactions(null, $args, null);
       
        $pending_ids = array_map(function($e) {
          return is_object($e) ? $e->sales_id : $e['sales_id'];
        }, $pending);

       
        if(isset($_SESSION['pending'])){
             $this->session->set_userdata('pending', $pending_ids);
        }else{
            $pending_array = array('pending'=>$pending_ids);
            $this->session->set_userdata($pending_array);
        }

        $p_ids = $this->session->userdata('pending');
        $arr_cancelled = $arr_unprocessed = array();

        foreach($pending as $key=>$val)
        {
            if($val->confirmed == 3)
                $arr_cancelled[$val->sales_id] = $pending[$key];
            else
                $arr_unprocessed[$val->sales_id] = $pending[$key];
        }

        echo json_encode(array('count_cancelled'=>count($arr_cancelled), 'count_unprocessed'=>count($arr_unprocessed)));
    }

    // public function _remap($method,$params=array())
    // {
    //     if (!$this->session->userdata('today_in') && !$this->session->userdata('manager_privs')) {
    //         header("Location:".base_url()."clock");
    //     } else {
    //         if (method_exists($this, $method))
    //             call_user_func_array(array($this,$method), $params);
    //         else
    //             show_404($method);
    //     }
    // }
    public function set_minimum_purchase()
    {
        $this->load->model('dine/cashier_model');
        $branch_id = $this->input->post('branch_id');  
        $result = $this->cashier_model->get_min_purchase($branch_id);
       
        if(!empty($result))
        {
            $result = $result[0];
            $min_purchase = $result->min_purchase;

        }else{
            $min_purchase = 0;
        }

        echo json_encode(array('min_purchase'=>$min_purchase));
    }
   
    public function cancelled_order($id){
        $this->load->model('dine/cashier_model');
        $orders = $this->cashier_model->get_trans_sales($id);
    }
    
    public function order_date_form()
    {
        $this->load->helper('dine/cashier_helper');
        $data['code'] = pop_order_date_form();
        $data['load_js'] = 'dine/cashier.php';
        $data['use_js'] = 'order_date_js';
        $this->load->view('load',$data);
    }
    
    public function pop_order_date_form_db(){
        $items = array(
            'time'=>$this->input->post('delivery_time'),
            'date'=>$this->input->post('delivery_date'),
        );
        echo json_encode($items);
    }
    public function orders($status='open', $now='all_trans',$search_val='',$show='box', $search_by='', $daterange=''){
        $daterange = urldecode($daterange);
        $advance = false;
        $search_val = urldecode($search_val);
        $this->load->model('dine/cashier_model');
        $this->load->model('site/site_model');
        $where = '';
        $user = $this->session->userdata('user');
        $args = array(
            'trans_sales.type_id'=>CALL_CENTER_TRANS,
            'trans_sales.inactive'=>0,
            'trans_sales.user_id'=> $user['id'],
            'trans_sales.trans_ref IS NOT NULL'=>array('use'=>'where','val'=>null,'third'=>false)
        );

        if($search_val == 'all'){
            if($status == 'all'){
                unset($args['DATE_FORMAT(trans_sales.datetime,"%Y-%m-%d")']);
                unset($args['trans_sales.user_id']);
                unset($args['trans_sales.inactive']);
                if($daterange != '')
                {
                    $dates = explode(" to ",$daterange);
                    $date_from = (empty($dates[0]) ? date('Y-m-d') : date('Y-m-d',strtotime(str_replace('-', '/', $dates[0]))));
                    $date_to = (empty($dates[1]) ? date('Y-m-d') :  date('Y-m-d', strtotime(str_replace('-', '/', $dates[1]))));

                    $where = "DATE(trans_sales.datetime) BETWEEN REPLACE('". $date_from ."',' ','') AND REPLACE('". $date_to ."',' ','')"; 
                }
              
            }
        }else if($search_val == ''){

            if($now != 'all_trans'){
                $args["DATE_FORMAT(trans_sales.datetime,'%Y-%m-%d')"] = date('Y-m-d');
            }
            if($status == 'rejected'){
                $args['trans_sales.confirmed'] = 2;            
            }
            if($status == 'open'){
                $advance = true;
                $args['trans_sales.confirmed'] = 1;            
                $args['trans_sales.start_time IS NULL'] = array('use'=>'where','val'=>null,'third'=>false);
                $args['trans_sales.completed'] = NULL;
                $args['time_diff_minutes <'] = array('use'=> 'HAVING' , 'val'=>'overall_time', 'third'=>false);  
                $args['trans_sales.inactive'] = 0;
            }
            if($status == 'processed'){
                $args['trans_sales.dr_no  IS NOT NULL'] = array('use'=>'where','val'=>null,'third'=>false);
                $args['trans_sales.delivered'] = 0;
            }
            if($status == 'advance'){
                $args['trans_sales.confirmed'] = 1;            
                $args['time_diff_minutes >'] = array('use'=> 'HAVING' , 'val'=>'overall_time', 'third'=>false);  
                $advance = true;
            }
            if($status == 'delivered'){
                $args['trans_sales.delivered'] = 1;
                $args['trans_sales.delivered_time IS NOT NULL'] = array('use'=>'where','val'=>null,'third'=>false);
            }
            if($status == 'hold'){
                $args['trans_sales.void_ref'] = null;   
                $args['trans_sales.on_hold'] = 1;            
            }
            if($status == 'cancelled'){
                $args['trans_sales.void_ref'] = null;
                $args['trans_sales.confirmed'] = 3;            
            }
            if($status == 'pending'){
                $args['trans_sales.void_ref'] = null;
                $args['trans_sales.confirmed'] = 0;   
            } 

        }else{ 
            unset($args['DATE_FORMAT(trans_sales.datetime,"%Y-%m-%d")']);
            unset($args['trans_sales.user_id']);
            if($daterange != '')
            {
                $dates = explode(" to ",$daterange);
                $date_from = (empty($dates[0]) ? date('Y-m-d') : date('Y-m-d',strtotime(str_replace('-', '/', $dates[0]))));
                $date_to = (empty($dates[1]) ? date('Y-m-d') :  date('Y-m-d', strtotime(str_replace('-', '/', $dates[1]))));

                $where = "DATE(trans_sales.datetime) BETWEEN REPLACE('". $date_from ."',' ','') AND REPLACE('". $date_to ."',' ','')";
                
            }
        } 
        
        $orders = $this->cashier_model->get_trans_sales(null,$args, 'desc' ,$search_val, $search_by,  $where, $advance);
        $query = $this->db->last_query();
        // die();
        if(count($orders) == 0)
        {
            $this->make->sDivCol(4);
                $this->make->H(4,"No transaction.");
            $this->make->eDivCol();
        }

            $code = "";
            $ids = array();
            $time = date('m/d/Y H:i:s');
            $this->make->sDivRow();
            $ord=array();
            $combine_cart = sess('trans_combine_cart');
            $status = '';
            foreach ($orders as $res)
            {
                // if($status == 'advance')
                //     ;
                // else if()
                //     $status = 'rejected';
                if($res->void_ref == NULL && $res->confirmed == 0)
                    $status = 'pending';  
                if($res->confirmed==1 && ($res->start_time == NULL || $res->complete_time != NULL))
                    $status = 'open'; 
                if($res->dr_no != NULL && $res->delivered == 0)
                     $status = 'processed'; 
                if($res->delivered == 1 && $res->delivered_time != NULL)
                    $status = 'delivered'; 
                if($res->void_ref == NULL && $res->confirmed == 3)
                    $status = 'cancelled'; 
                if($res->void_ref == NULL && $res->on_hold == 1)
                    $status = 'hold'; 
                if($res->inactive == 1 && $res->reason != NULL)
                    $status = 'void';

                $ord[$res->sales_id] = array(
                    "type"=>$res->type,
                    "status"=>$status,
                    "user_id"=>$res->user_id,
                    "name"=>$res->username,
                    "terminal_id"=>$res->terminal_id,
                    "terminal_name"=>$res->terminal_name,
                    "shift_id"=>$res->shift_id,
                    "datetime"=>$res->datetime,
                    "amount"=>$res->total_amount
                );
                if($show == "box")
                {
                    if($search_val == '')
                        $this->make->sDivCol(4,'left',0);
                    else if($search_val == 'all')
                        $this->make->sDivCol(4,'left',0);
                    else
                        $this->make->sDivCol(6,'left',0);
                        
                        $this->make->sDiv(array('class'=>$status.'-stat order-btn-'.$res->sales_id,'id'=>'order-btn-'.$res->sales_id,'ref'=>$res->sales_id));
                            if($res->trans_ref == null){
                                $this->make->sBox('default',array('class'=>'box-solid'));
                            }else{
                                $this->make->sBox('default',array('class'=>'box-solid bg-gray'));
                            }
                                $this->make->sBoxBody();
                                    $this->make->sDivRow();
                                        $this->make->sDivCol();
                                            $this->make->H(5,'Time Ordered: '.strtoupper(substr($res->datetime, 10, strlen($res->datetime))) . "<span class='label label-red' style='float: right;'>".strtoupper(ago($res->datetime,$time))."</span>",array('style'=>'margin-top:15px;font-weight: bold; opacity: 0.8;'));
                                        $this->make->eDivCol();
                                        $this->make->sDivCol();
                                            $this->make->H(4,$res->trans_ref . ' ' . "<span style='float: right;'>₱ ".num($res->total_amount) . "</span>",array("style"=>'font-weight: 800; font-size: medium;'));
                                        $this->make->eDivCol();
                                        $this->make->sDivCol();
                                            $this->make->H(5,'ETD: '.strtoupper(sql2Date($res->order_delivery_date)) . ' ' .$res->order_delivery_time ,array('style'=>'margin-top:15px;font-weight: bold; opacity: 0.8;'));
                                        $this->make->eDivCol();
                                        
                                    $this->make->eDivRow();

                                $this->make->eBoxBody();
                            $this->make->eBox();
                        $this->make->eDiv();
                        $this->make->eDivCol();
                }
               
                $ids[] = $res->sales_id;
            }
        
        $this->make->eDivRow();
        $code = $this->make->code();

        
        echo json_encode(array('code'=>$code,'ids'=>$ord, 'order'=> $orders, 'query'=> $query));
    }
    public function order_view($sales_id=null){

        $order = $this->get_order(false,$sales_id);

        $ord = $order['order'];
        $det = $order['details'];
        $args = array('customer_nos.default_no' => 1);
        $info = $this->customers_model->get_cust_info(null, $ord['customer_id'], $args);


        $info_div = '';
        if(!empty($info))
        {
            $info = $info[0];
            $info_div = "Customer Information: <br/><h3>".strtoupper($info->fname . " " .$info->lname)." <br/><small>".$info->phone_no."</small></h3>";
        }else{
            // $args = array('customer_nos.default_no' => 1);
            $info = $this->customers_model->get_cust_info(null, $ord['customer_id'], array());
            if(!empty($info))
            {                
                $info = $info[0];
                $info_div = "Customer Information: <br/><h3>".strtoupper($info->fname . " " .$info->lname)." <br/><small>".$info->phone_no."</small></h3>";
            }
        }   


        $discs = $order['discounts'];
        $charges = $order['charges'];
        $vip = $order['vip_discount'];
        $total = 0;

        $menu_cancelled_items = $mod_cancelled_items = array();

        $totals = $this->total_trans(false,$det,$discs,$charges, $vip);
        $this->make->H(3,strtoupper($ord['type'])." #".$ord['sales_id'],array('class'=>'receipt text-center'));
        $this->make->H(5,strong('Date Ordered: ').sql2DateTime($ord['datetime']),array('class'=>'receipt text-center'));
        $this->make->H(5,strong('Delivery Date and Time: ').sql2Date($ord['order_delivery_date']) . ' ' . $ord['order_delivery_time'],array('class'=>'receipt text-center'));
        $this->make->append('<hr>');
        $this->make->sDiv(array('class'=>'body'));
            $this->make->sUl();


                foreach ($det as $menu_id => $opt) {

                    $qty = $this->make->span($opt['qty'],array('class'=>'qty','return'=>true));
                    $name = $this->make->span($opt['name'],array('class'=>'name','return'=>true));
                    $cost = $this->make->span("₱ ".num($opt['price']),array('class'=>'cost','return'=>true));
                    $price = $opt['price'];
                    $class = ($opt['cancelled'] == 1) ? 'li-active' : '';
                    if($class != '')
                        $menu_cancelled_items[] = $opt['menu_id'];

                    $this->make->li($qty." ".$name." ".$cost, array('class'=>$class) );
                    if(count($opt['modifiers']) > 0){
                        foreach ($opt['modifiers'] as $mod_id => $mod) {
                            $name = $this->make->span($mod['name'],array('class'=>'name','style'=>'margin-left:36px;','return'=>true));
                            $cost = "";
                            if($mod['price'] > 0 )
                                $cost = $this->make->span('₱ '.num($mod['price']),array('class'=>'cost','return'=>true));
                            $class = ($mod['cancelled'] == 1) ? 'li-active':'';
                            if($class != '')
                                $mod_cancelled_items[] = $mod['sales_mod_id'];
        
                            $this->make->li($name." ".$cost, array('class'=>$class) );
                            $price += $mod['price'];
                        }
                    }
                    $total += $opt['qty'] * $price  ;
                }

                $cancelled_items = array('menu'=>$menu_cancelled_items, 'mod'=>$mod_cancelled_items);

                $this->session->unset_userdata('cancelled_items');     
                $this->session->set_userData('cancelled_items', $cancelled_items);

                if(count($charges) > 0){
                    foreach ($charges as $charge_id => $ch) {
                        $qty = $this->make->span(fa('fa fa-tag'),array('class'=>'qty','return'=>true));
                        $name = $this->make->span($ch['name'],array('class'=>'name','return'=>true));
                        $tx = $ch['amount'];
                        if($ch['absolute'] == 0)
                            $tx =  $ch['amount']."%";
                        else
                            $tx = "₱ ".num($ch['amount']);

                        $cost = $this->make->span($tx,array('class'=>'cost','return'=>true));
                        $this->make->li($qty." ".$name." ".$cost);
                    }
                }
                #VIP DISCOUNT
                if(!empty($vip)){

                    extract($vip['vip']);
                    $qty = $this->make->span(fa('fa fa-tag'),array('return'=>true));
                    $name = $this->make->span(ucwords($disc_type) . " Discount",array('class'=>'name','return'=>true));
                    $tx = $disc_rate;
                    if($is_absolute == 0)
                        $tx =  $tx."%";
                    else
                        $tx = "₱ ".num($tx);

                    $cost = $this->make->span(" (".$tx.") ",array('style'=>'float: right;','class'=>'cost','return'=>true));
                    $this->make->li($qty." ".$name." ".$cost);
                }

                 #DISCOUNT
                if(!empty($discs)){
                  
                    foreach($discs as $dc)
                    {             

                        extract($dc);       
                        $qty = $this->make->span(fa('fa fa-tag'),array('return'=>true));
                        $name = $this->make->span(ucwords($disc_code) . " Discount",array('class'=>'name','return'=>true));
                       
                        $tx = "₱ ".num($amount*$guest);

                        $cost = $this->make->span(" (".$tx.") ",array('style'=>'float: right;','class'=>'cost','return'=>true));
                        $this->make->li($qty." ".$name." ".$cost);
                    }
                }

            $this->make->eUl();
            $this->make->eDiv();
        
        if($ord['instruction'])
        { 
            $this->make->append('<hr>');
            $this->make->sDiv(array('class'=>'footer', 'style'=>'position: relative;height: 40px; overflow: hidden; width: 100%;'));
                $this->make->H(5,'&nbsp;&nbsp;<strong>Instruction:</strong> '. $ord['instruction'],array('class'=>'receipt text-left')); 
            $this->make->eDiv();
        }

        $this->make->append('<hr>');
        $total = $totals['total'] + $totals['discount'];
        $net = $totals['total'] - $totals['discount'];

        $this->make->H(4,'TOTAL: ₱'.num($total),array('class'=>'receipt text-center'));
        $this->make->H(5,'DISCOUNT: ₱'.num($totals['discount']),array('class'=>'receipt text-center'));
        $this->make->H(5,'NET AMOUNT DUE: ₱'.num($total-$totals['discount']),array('class'=>'receipt text-center'));

        $code = $this->make->code();

        echo json_encode(array('code'=>$code, 'on_hold'=>$ord['on_hold'], 'delivered'=>$ord['delivered'],'info'=>$info_div));
    }
    public function hold_status_db($sales_id=null, $hold_status=null){
        $items = array( 'on_hold' => $hold_status );

        $this->cashier_model->update_trans_sales($items, $sales_id);
        if($hold_status == 1)
            echo json_encode(array('msg'=>'Item is now on hold.'));
        else
            echo json_encode(array('msg'=>'Production has been resumed.'));
    }
    public function get_complain_order($id=null){
        $this->load->model('dine/cashier_model'); 

        $list = $this->cashier_model->get_trans_sales_complain($id);
        if(!empty($list))
            echo json_encode(array('list'=>$list));
        else
            echo json_encode(array('list'=>null));
    }
    public function complain_order($sales_id=null){
        $this->load->model('dine/cashier_model');
       
        $reason = $this->input->post('reason');
        $reason  = rtrim( $reason , "|");
        $reasons = explode('|', $reason);
        if(!empty($reasons))
        {
            $this->cashier_model->delete_trans_sales_complain($sales_id);
            foreach($reasons as $r)
            {
                $items = array( 'reason'=> $r,
                                'remarks'=>$this->input->post('remarks'),
                                'sales_id'=>$sales_id,
                        );

                $this->cashier_model->add_trans_sales_complain($items);
                echo json_encode(array('error'=>''));
            }    
        }
    }
    public function void_order($sales_id=null){
        $this->load->model('dine/cashier_model');
        $this->load->model('dine/items_model');
        $this->load->model('core/trans_model');
        $order = $this->get_order_header(false,$sales_id);
        $reason = "";
        $error = '';
        if($this->input->post('reason'))
            $reason = $this->input->post('reason');
        if($order['paid'] == 0){
            $this->cashier_model->update_trans_sales(array('reason'=>$reason,'inactive'=>1),$sales_id);
        }
        else{
            $order = $this->get_order(false,$sales_id);
            $trans = $this->load_trans(false,$order,true);
            $void = $this->submit_trans(false,null,true,$sales_id);
            $this->finish_trans($void['id'],true,true);
            $this->cashier_model->update_trans_sales(array('reason'=>$reason,'inactive'=>1),$sales_id);
        }
        echo json_encode(array('error'=>$error));
    }
    public function get_branch_details($asJson=true){
       $this->load->model('dine/setup_model');
       $details = $this->setup_model->get_branch_details();
       $det = array();
       foreach ($details as $res) {
           $det = array(
                    "id"=>$res->branch_id,
                    "code"=>$res->branch_code,
                    "name"=>$res->branch_name,
                    "desc"=>$res->branch_desc,
                    "contact_no"=>$res->contact_no,
                    "delivery_no"=>$res->delivery_no,
                    "address"=>$res->address,
                    "base_location"=>$res->base_location,
                    "currency"=>$res->currency,
                    "tin"=>$res->tin,
                    "machine_no"=>$res->machine_no,
                    "bir"=>$res->bir,
                    "permit_no"=>$res->permit_no,
                    "serial"=>$res->serial,
                    "email"=>$res->email,
                    "website"=>$res->website,
                    "layout"=>base_url().'uploads/'.$res->image
                  );
       }
       if($asJson)
            echo json_encode($det);
        else
            return $det;
    }
    #TABLES
    public function tables(){
        $this->load->model('site/site_model');
        $this->load->model('dine/cashier_model');
        $this->load->helper('dine/cashier_helper');
        $this->load->helper('core/on_screen_key_helper');
        $data = $this->syter->spawn(null);
        sess_clear('trans_type_cart');
        $data['code'] = tablesPage();

        $data['add_css'] = array('css/cashier.css','css/onscrkeys.css','css/rtag.css');
        $data['add_js'] = array('js/on_screen_keys.js');
        $data['load_js'] = 'dine/cashier.php';
        $data['use_js'] = 'tablesJs';
        $data['noNavbar'] = true;
        $this->load->view('cashier',$data);
    }
    public function get_tables($asJson=true,$tbl_id=null){
        $this->load->model('dine/cashier_model');
        $tbl = array();
        $occ = array();
        $occ_tbls = $this->cashier_model->get_occupied_tables();
        foreach ($occ_tbls as $det) {
          $occ[] = $det->table_id;
        }
        $tables = $this->cashier_model->get_tables();
        foreach ($tables as $res) {
            $status = 'green';
            if(in_array($res->tbl_id, $occ)){
              $status = 'red';
            }
            $tbl[$res->tbl_id] = array(
                "name"=> $res->name,
                "top"=> $res->top,
                "left"=> $res->left,
                "stat"=> $status
            );
        }
        if($asJson)
            echo json_encode($tbl);
        else
            return $tbl;
    }
    function get_table_orders($asJson=true,$tbl_id=null){
        $this->load->model('dine/cashier_model');
        $this->load->model('site/site_model');
        $args = array();
        $args["trans_sales.trans_ref  IS NULL"] = array('use'=>'where','val'=>null,'third'=>false);
        $args["trans_sales.table_id"] = $tbl_id;
        $orders = $this->cashier_model->get_trans_sales(null,$args);
        $time = date('m/d/Y H:i:s');
        $this->make->sDivRow();
        $ord=array();
        foreach ($orders as $res) {
            $status = "open";
            if($res->trans_ref != "")
                $status = "settled";
            $ord[$res->sales_id] = array(
                "type"=>$res->type,
                "status"=>$status,
                "user_id"=>$res->user_id,
                "name"=>$res->username,
                "terminal_id"=>$res->terminal_id,
                "terminal_name"=>$res->terminal_name,
                "shift_id"=>$res->shift_id,
                "datetime"=>$res->datetime,
                "amount"=>$res->total_amount
            );
            $this->make->sDivCol(4,'left',0);
                    $this->make->sDiv(array('class'=>'order-btn','id'=>'order-btn-'.$res->sales_id,'ref'=>$res->sales_id));
                        if($res->trans_ref == null){
                            $this->make->sBox('default',array('class'=>'box-solid'));
                        }else{
                            $this->make->sBox('default',array('class'=>'box-solid bg-green'));
                        }
                            $this->make->sBoxBody();
                                $this->make->sDivRow();
                                    $this->make->sDivCol(6);
                                        $this->make->sDiv(array('style'=>'margin-left:20px;'));
                                            $this->make->H(5,strtoupper($res->type)." #".$res->sales_id,array("style"=>'font-weight:700;'));
                                            if($res->trans_ref == null){
                                                $this->make->H(5,strtoupper($res->username),array("style"=>'color:#888'));
                                                $this->make->H(5,strtoupper($res->terminal_name),array("style"=>'color:#888'));
                                            }else{
                                                $this->make->H(5,strtoupper($res->username),array("style"=>'color:#fff'));
                                                $this->make->H(5,strtoupper($res->terminal_name),array("style"=>'color:#fff'));
                                            }
                                            $this->make->H(5,"<span class='label label-default'>".strtoupper(ago($res->datetime,$time))."</span>");
                                        $this->make->eDiv();
                                    $this->make->eDivCol();
                                    $this->make->sDivCol(6);
                                        $this->make->H(4,'Order Total',array('class'=>'text-center'));
                                        $this->make->H(3,'₱ '.num($res->total_amount),array('class'=>'text-center'));
                                    $this->make->eDivCol();
                                $this->make->eDivRow();
                            $this->make->eBoxBody();
                        $this->make->eBox();
                    $this->make->eDiv();
            $this->make->eDivCol();
        }
        $this->make->eDivRow();
        $code = $this->make->code();
        echo json_encode(array('code'=>$code,'ids'=>$ord));
    }
    #CHARGES
    function get_charges($asJson=true){
        $this->load->model('dine/cashier_model');
        $this->load->model('dine/settings_model');
        $charges = $this->settings_model->get_charges();
        $discs = array();
        $del_charge = null;

        if(DELIVERY_CHARGE_ID)
        {
            $delivery_charge = $this->settings_model->get_charges(DELIVERY_CHARGE_ID);
            if(!empty($delivery_charge))
            {
                $delivery_charge = $delivery_charge[0];
                $del_charge[DELIVERY_CHARGE_ID] =  array(
                    "charge_code"=>$delivery_charge->charge_code,
                    "charge_name"=>$delivery_charge->charge_name,
                    "charge_amount"=>$delivery_charge->charge_amount,
                    "no_tax"=>$delivery_charge->no_tax,
                    "absolute"=>$delivery_charge->absolute
                );
            }
        }
            
        $this->make->sDivRow();
            foreach ($charges as $res) {
                $text = num($res->charge_amount);
                if($res->absolute == 0){
                    $text .= " %";
                }
                $this->make->sDivCol(12);
                    $this->make->button("[".strtoupper($res->charge_code)."] ".strtoupper($res->charge_name)." <br> ".$text,
                                        array('id'=>'charges-btn-'.$res->charge_id,'class'=>'disc-btn-row btn-block counter-btn-orange double'));
                $this->make->eDivCol();
                $ids[$res->charge_id] = array(
                    "charge_code"=>$res->charge_code,
                    "charge_name"=>$res->charge_name,
                    "charge_amount"=>$res->charge_amount,
                    "no_tax"=>$res->no_tax,
                    "absolute"=>$res->absolute
                );
            }
        $this->make->eDivRow();
        $code = $this->make->code();
        echo json_encode(array('code'=>$code,'ids'=>$ids, 'apply'=>$del_charge));
    }
//FROM NEW 
     #DISCOUNTS
    function get_discounts($asJson=true, $sr=false){
        $this->load->model('dine/cashier_model');
        $this->load->model('dine/settings_model');
        $trans_disc_cart = sess('trans_disc_cart');
        if($sr)
        {
            $discounts = $this->settings_model->get_receipt_discounts(SENIOR_CITIZEN_ID);
        }else{
            $discounts = $this->settings_model->get_receipt_discounts();
        }
        $discs = array();
        $this->make->sDivRow();
            foreach ($discounts as $res) {
                $this->make->sDivCol(12);
                    $this->make->button("[".strtoupper($res->disc_code)."] ".strtoupper($res->disc_name),array('id'=>'item-disc-btn-'.$res->disc_code,'class'=>'disc-btn-row btn-block counter-btn-green'));
                $this->make->eDivCol();
                $ids[$res->disc_code] = array(
                    "disc_code"=>$res->disc_code,
                    "disc_id"=>$res->disc_id,
                    "disc_name"=>$res->disc_name,
                    "disc_rate"=>$res->disc_rate,
                    "no_tax"=>$res->no_tax,
                    "is_absolute"=>$res->is_absolute,
                );
                if(isset($trans_disc_cart[$res->disc_code])){
                    $row = $trans_disc_cart[$res->disc_code];
                    $ids[$res->disc_code]['guest'] = $row['guest'];
                    $ids[$res->disc_code]['disc_type'] = $row['disc_type'];
                    foreach ($row['persons'] as $code => $per) {
                        $ids[$res->disc_code]['persons'][$code] = array(
                            'name' => $per['name'],
                            'code' => $per['code'],
                            'bday' => $per['bday']
                        );
                    }
                }
            }
            $this->make->eDivRow();
            $code = $this->make->code();
            echo json_encode(array('code'=>$code,'ids'=>$ids));
        }
    public function remove_person_disc($disc=null,$code=null){
        $trans_disc_cart = sess('trans_disc_cart');
        $persons = array();
        if(isset($trans_disc_cart[$disc]['persons'])){
         $persons = $trans_disc_cart[$disc]['persons'];
        }
        unset($persons[$code]);
        $trans_disc_cart[$disc]['persons'] = $persons;
        sess_initialize('trans_disc_cart',$trans_disc_cart);
        echo json_encode($trans_disc_cart[$disc]);
    }
    public function load_disc_persons($disc=null){
        $trans_disc_cart = sess('trans_disc_cart');
            $persons = array();
        if(isset($trans_disc_cart[$disc]['persons'])){
            $persons = $trans_disc_cart[$disc]['persons'];
        }
        $this->make->sUl(array('class'=>'ul-hover-blue'));
        $items = array();
        foreach ($persons as $res) {
            $this->make->sLi(array('id'=>'disc-person-'.$res['code'],'class'=>'disc-person','style'=>'padding:5px;padding-bottom:10px;padding-top:10px;border-bottom:1px solid #ddd;'));
                $this->make->H(4,$res['code']." ".$res['name']." ".$res['bday'],array('style'=>'margin:0;padding:0;margin-left:10px;'));
            $this->make->eLi();
            $items[$res['code']] = array(
                "name"=> $res['code'],
                "bday"=> $res['bday'],
                "disc"=> $disc
            );
        }
        $this->make->eUl();
        $code = $this->make->code();
        echo json_encode(array('code'=>$code,'items'=>$items));
    }
    public function add_person_disc(){
       $trans_disc_cart = sess('trans_disc_cart');
       $persons = array();
       if(isset($trans_disc_cart[$this->input->post('disc-disc-code')]['persons'])){
         $persons = $trans_disc_cart[$this->input->post('disc-disc-code')]['persons'];
       }
       $error = "";
       $items = array();
       $bday = null;
       if($this->input->post('disc-cust-bday'))
           $bday = $this->input->post('disc-cust-bday');

       if(!isset($persons[$this->input->post('disc-cust-code')])){
               $persons[$this->input->post('disc-cust-code')] = array(
                    "name"  => $this->input->post('disc-cust-name'),
                    "code"  => $this->input->post('disc-cust-code'),
                    "bday"  => $bday
               );
       }
       else{
        $error = "Person is already added.";
       }

       $trans_disc_cart[$this->input->post('disc-disc-code')]['persons'] = $persons;
       sess_initialize('trans_disc_cart',$trans_disc_cart);

        $this->make->sUl(array('class'=>'ul-hover-blue'));
        $items = array();
        foreach ($persons as $res) {
            $this->make->sLi(array('id'=>'disc-person-'.$res['code'],'class'=>'disc-person','style'=>'padding:5px;padding-bottom:10px;padding-top:10px;border-bottom:1px solid #ddd;'));
                $this->make->H(4,$res['code']." ".$res['name']." ".$res['bday'],array('style'=>'margin:0;padding:0;margin-left:10px;'));
            $this->make->eLi();
            $items[$res['code']] = array(
                "name"=> $res['code'],
                "bday"=> $res['bday'],
                "disc"=> $this->input->post('disc-disc-code')
            );
        }
        $this->make->eUl();
        $code = $this->make->code();

        $trans_disc_cart = sess('trans_disc_cart');
        // echo count($trans_disc_cart[SENIOR_CITIZEN_CODE]['persons']);

        echo json_encode(array('code'=>$code,'items'=>$items,'error'=>$error, 'count'=>count($trans_disc_cart[SENIOR_CITIZEN_CODE]['persons'])));
    }
    
    public function add_trans_disc(){
       $trans_disc_cart = sess('trans_disc_cart');
       
       $disc_cart = array();
       $discount = 0;
       $error = "";
       if(isset($trans_disc_cart[$this->input->post('disc-disc-code')])){
        $disc_cart = $trans_disc_cart[$this->input->post('disc-disc-code')];
       }

       if($this->input->post('guests') > 0){

            if(isset($disc_cart['persons']) && count($disc_cart['persons']) <= $this->input->post('guests')){
                $disc_cart['guest'] =  $this->input->post('guests');
                $disc_cart['disc_rate'] =  $this->input->post('disc-disc-rate');
                $disc_cart['disc_code'] =  $this->input->post('disc-disc-code');
                $disc_cart['disc_id'] =  $this->input->post('disc-disc-id');
                $disc_cart['disc_type'] =  $this->input->post('type');
                $disc_cart['no_tax'] =  $this->input->post('disc-no-tax');
               
                
                $trans_cart = array();
                if($this->session->userData('trans_cart')){
                    $trans_cart = $this->session->userData('trans_cart');
                }

                $maxes = $this->get_max_price_and_qty_for_disc($this->input->post('guests'),$trans_cart);

                $highest_disc = 0;
                foreach($maxes as $key => $val)
                    $highest_disc +=($val['cost']*$val['qty']);
                
                    switch ($disc_cart['disc_type']){
                        case "highest":
                            $discount = ($disc_cart['disc_rate'] / 100) * $highest_disc;
                            $disc_cart['amount'] = $discount;
                            break;
                    }

                $trans_disc_cart[$this->input->post('disc-disc-code')] = $disc_cart;
                sess_initialize('trans_disc_cart',$trans_disc_cart);

            }
            else{
                $error = "Invalid No. of Persons";
            }
       }
       else{
            $error = "Invalid total No. Of Guests";
       }

       echo json_encode(array('error'=>$error, 'items'=>$disc_cart, 'id'=>$disc_cart['disc_code']));
    }

    public function del_trans_disc($disc_code=null){
       $trans_disc_cart = sess('trans_disc_cart');
       unset($trans_disc_cart[$disc_code]);
       sess_initialize('trans_disc_cart',$trans_disc_cart);
    }

 //FROM NEW DINE   
    #VIP DISCOUNT
    
    public function add_trans_vip_disc(){
        $disc_cart['disc_rate'] = $this->input->post('disc_rate');
        $disc_cart['is_absolute'] = $this->input->post('is_absolute');
        $disc_cart['disc_type'] = 'vip';
        $trans_sales_vip['vip']=$disc_cart;
        $disc_cart['amount'] = $this->input->post('disc_rate');
        $disc_cart['disc_code'] = 'VIP';
        
        
        sess_initialize('trans_vip_disc_cart',$trans_sales_vip);
        echo json_encode(array('items'=>$disc_cart, 'id'=>'vip'));
    }

    #COUNTER
    public function counter($type=null,$branch_id=null,$sales_id=null){
        $this->load->model('site/site_model');
        $this->load->model('dine/cashier_model');
        $this->load->model('dine/customers_model');
        $this->load->model('dine/settings_model');
        $this->load->helper('dine/cashier_helper');
        $data = $this->syter->spawn(null);
        $ins = null;
        $cust_id = null;
        $found = null;
        $charges = null;

        $trans_cart = sess('trans_type_cart');
        if(!empty($trans_cart))
        {
            $trans_cart = $trans_cart[0];
            $cust_id = $trans_cart['customer_id'];
        }

        if($sales_id != null){
            $order = $this->get_order(false,$sales_id);
            $trans = $this->load_trans(false,$order);
            $time = $trans['datetime'];
            $type = $type." #".$order['order']['sales_id'];
            $ins =  $order['order']['instruction'];
   
            $trans_type_cart = array('customer_id'=>$order['order']['customer_id'],
                                     'confirmed'=>$order['order']['confirmed'], 
                                     'type'=>'delivery', 
                                     'branch_id'=>$order['order']['branch_id'], 
                                     'address_id'=>$order['order']['address_id'],
                                     'travel_time'=>$order['order']['delivery_time']);
            $this->session->unset_userdata('branch_id');
            $this->session->set_userdata($trans_type_cart);
            $branch_id =  $order['order']['branch_id'];
            $cust_id = $order['order']['customer_id'];
        }
        else{
            $trans = $this->new_trans(false,$type);
            $time = $trans['datetime'];
        }

        if($cust_id != null)
        {
            $found = $this->customers_model->get_customer($cust_id);
            if(!empty($found))
               $found = $found[0];
        }
            
        $data['code'] = counterPage($type,$time, $ins, $branch_id, $found);
        
        $data['add_css'] = array('css/virtual_keyboard.css', 'css/cashier.css');
        $data['add_js'] = array('js/jquery.keyboard.extension-navigation.min.js','js/jquery.keyboard.min.js');
        $data['load_js'] = 'dine/cashier.php';
        $data['use_js'] = 'counterJs';
        $data['noNavbar'] = true;
        $this->load->view('cashier',$data);
    }
    public function branch_zoning($customer_id=null){

        $this->load->model('site/site_model'); 
        $this->load->model('dine/customers_model');
        $this->load->model('dine/trans_model');
        $this->load->helper('dine/cashier_helper');

        $data = $this->syter->spawn(null);
        $data['add_css'] = array('css/cashier.css');
        $data['load_js'] = 'dine/cashier.php';
        $data['use_js'] = 'branchZoningJs';
        $data['noNavbar'] = true;

        $type = sess('trans_type_cart');

        // print_r($type);
        // die('--');
        if(!empty($type))
        {
            $key = end(array_keys($type));
            $type = $type[$key];            
        
            $cust = $this->customers_model->get_customer_address($type['customer_id'], $type['address_id']);

            $cust = $cust[0];
            $addr = $cust->region ." ". $cust->street_no ." ".$cust->region ." ". $cust->street_address." ". $cust->city ." ". $cust->landmark; 
            $addr = explode(" ", $addr);

        }


        $streets = $this->trans_model->get_street_list(null);
        $streets_new = $street_list = array();
        $branch_list = array();
        foreach($streets as $key=>$st)
        {
            $br_arr = explode(" ", $st->street_name);
            $result = array_intersect($addr , $br_arr ); //matched elements
            $num = count($result);

            if($num > 0)
            {
                if(!in_array($st->branch_id, $branch_list)) {
                    $branch_list[] = $st->branch_id;
                    $street_list[$key] = $num;  
                    $streets_new[] = $st;
                }
                
               
            }
        }

        array_multisort($street_list, SORT_DESC, $streets_new);

        usort($streets_new, function($a, $b)
        {
            return strcmp($a->travel_time, $b->travel_time);
        });

        $branch_info = null;

        $data['code'] = branches_list_tbl($customer_id,$streets_new);
        $this->load->view('cashier',$data);
    }

    public function get_branch_db($branch_id){
        $this->load->model('dine/trans_model');
        $branch_info = $this->trans_model->get_branches_list($branch_id);
        $branch_info = $branch_info[0];

        echo json_encode(array('branch_info' => $branch_info));
    }
    public function combine($type=null,$sales_id=null){
        $this->load->model('site/site_model');
        $this->load->model('dine/cashier_model');
        $this->load->helper('dine/cashier_helper');
        sess_clear('trans_combine_cart');
        $data = $this->syter->spawn(null);
        $order = $this->get_order(false,$sales_id);
        $trans = $this->load_trans(false,$order);
        $time = $trans['datetime'];
        $type = $type." #".$order['order']['sales_id'];
        sess_add('trans_combine_cart',array('sales_id'=>$order['order']['sales_id'],'balance'=>$order['order']['balance']));
        $data['code'] = combinePage($type,$time);
        $data['add_css'] = 'css/cashier.css';
        $data['load_js'] = 'dine/cashier.php';
        $data['use_js'] = 'combineJs';
        $data['noNavbar'] = true;
        $this->load->view('cashier',$data);
    }
    public function save_combine(){
        $trans_combine_cart = sess('trans_combine_cart');
        $main_sales_id = null;
        $trans_cart = array();
        $trans_mod_cart = array();
        $ctr = 1;
        $liner = 0;
        foreach ($trans_combine_cart as $key => $co) {
            $sales_id = $co['sales_id'];
            $order = $this->get_order(false,$sales_id);
            $header = $order['order'];
            $details = $order['details'];
            $com = "";
            if($ctr == 1){
                $main_sales_id = $sales_id;
                foreach ($details as $line_id => $menu){
                    $trans_cart[$line_id] = array(
                        "menu_id"=> $menu['menu_id'],
                        "name"=> $menu['name'],
                        "cost"=> $menu['price'],
                        "qty"=> $menu['qty'],
                    );
                    if(count($menu['modifiers']) > 0){
                        foreach ($menu['modifiers'] as $mod) {
                            if($mod['line_id'] == $line_id){
                                $trans_mod_cart[] = array(
                                    "trans_id"=>$mod['line_id'],
                                    "mod_id"=>$mod['id'],
                                    "menu_id"=>$menu['menu_id'],
                                    "menu_name"=>$menu['name'],
                                    "name"=>$mod['name'],
                                    "cost"=>$mod['price'],
                                    "qty"=>$mod['qty']
                                );
                            }
                        }#END FOR EACH
                    }#END IF
                    $liner = $line_id;
                }#END MAIN FOR EACH
            }
            else{
                foreach ($details as $line_id => $menu){
                    $liner++;
                    $trans_cart[$liner] = array(
                        "menu_id"=> $menu['menu_id'],
                        "name"=> $menu['name'],
                        "cost"=> $menu['price'],
                        "qty"=> $menu['qty'],
                    );
                    if(count($menu['modifiers']) > 0){
                        foreach ($menu['modifiers'] as $mod) {
                            if($mod['line_id'] == $line_id){
                                $trans_mod_cart[] = array(
                                    "trans_id"=>$liner,
                                    "mod_id"=>$mod['id'],
                                    "menu_id"=>$menu['menu_id'],
                                    "menu_name"=>$menu['name'],
                                    "name"=>$mod['name'],
                                    "cost"=>$mod['price'],
                                    "qty"=>$mod['qty']
                                );
                            }
                        }#END FOR EACH
                    }#END IF
                }#END MAIN FOR EACH
                $this->cashier_model->update_trans_sales(array('inactive'=>1,'reason'=>'combined to receipt# '.$main_sales_id),$sales_id);
                $com .= $sales_id.",";
            }#END IF
            $ctr++;
        }
        $sale = $this->submit_trans(false,null,false,null,$trans_cart,$trans_mod_cart);
        $com = substr($com, 0,-1);
        site_alert('Success! Reciept #'.$com.' combined to reciept#'.$sale['id'],'success');
    }
    public function split($type=null,$sales_id=null){
        $this->load->model('site/site_model');
        $this->load->model('dine/cashier_model');
        $this->load->helper('dine/cashier_helper');
        sess_clear('trans_split_cart');
        $data = $this->syter->spawn(null);
        $order = $this->get_order(false,$sales_id);
        $trans = $this->load_trans(false,$order);
        $time = $trans['datetime'];
        $type = $type." #".$order['order']['sales_id'];
        $data['code'] = splitPage($type,$time);
        $data['add_css'] = 'css/cashier.css';
        $data['load_js'] = 'dine/cashier.php';
        $data['use_js'] = 'splitJs';
        $data['noNavbar'] = true;
        $this->load->view('cashier',$data);
    }
    public function save_split(){
        $trans_split_cart = sess('trans_split_cart');
        $trans_cart = sess('trans_cart');
        $trans_mod_cart = sess('trans_mod_cart');
        $ctr = 1;
        $error = "";
        foreach ($trans_cart as $trans_id => $tr) {
            if($tr['qty'] > 0){
                $error = "Please Assign All Items";
                break;
            }
        }

        if($error == ""){
            $split_into = "";
            foreach ($trans_split_cart as $num => $row) {
                if($ctr > 1){
                    $counter = sess('counter');
                    unset($counter['sales_id']);
                    $this->session->set_userData('counter',$counter);
                }
                $sale = $this->submit_trans(false,null,false,null,$row);
                $ctr++;
                $split_into .= " #".$sale['id'].", ";
            }
            site_alert('Success! Transaction split into '.substr($split_into,0,-1),'success');
        }
        echo json_encode(array('error'=>$error));
    }
    public function even_split($num=null){
        sess_clear('trans_split_cart');
        $trans_cart = sess('trans_cart');
        $trans_mod_cart = sess('trans_mod_cart');
        $ctr = 1;
        $error = "";
        $split_into = "";
        foreach ($trans_cart as $trans_id => $opt) {
            $opt['cost'] = $opt['cost']/$num;
            $trans_cart[$trans_id] = $opt;
        }
        foreach ($trans_mod_cart as $trans_mod_id => $mod) {
            $mod['cost'] = $mod['cost']/$num;
            $trans_mod_cart[$trans_mod_id] = $mod;
        }
        for ($i=1; $i <= $num; $i++) {
            if($ctr > 1){
                $counter = sess('counter');
                unset($counter['sales_id']);
                $this->session->set_userData('counter',$counter);
            }
            $sale = $this->submit_trans(false,null,false,null,$trans_cart,$trans_mod_cart);
            $ctr++;
            $split_into .= " #".$sale['id'].", ";
        }
        site_alert('Success! Transaction split into '.substr($split_into,0,-1),'success');
        echo json_encode(array('error'=>$error));
    }
    public function new_split_block($num=0){
        $code = "";
        $trans_split_cart = sess('trans_split_cart');
        // if(count($trans_split_cart) > 0){
        //     $num = max(array_keys($trans_split_cart)) + 1;
        // }
        // else{
        //     if($num > 0){
        //         $num += 1;
        //     }
        // }
        $this->make->sDivCol(4);
            $this->make->sDiv(array('class'=>'sel-div','id'=>'sel-div-'.$num));
                $this->make->sDiv(array('class'=>'sel-trans-list'));
                    $this->make->sUl(array("style"=>'padding-top:10px;'));
                        // $this->make->li('<span class="qty">100</span><span class="name">100</span><span class="cost">100</span>');
                    $this->make->eUl();
                $this->make->eDiv();
                $this->make->sDivRow();
                    $this->make->sDivCol(4);
                        $this->make->button(fa('fa-plus fa-lg fa-fw'),array('class'=>'add-btn btn-block counter-btn-green'));
                    $this->make->eDivCol();
                    $this->make->sDivCol(4);
                        $this->make->button(fa('fa-minus fa-lg fa-fw'),array('class'=>'del-btn btn-block counter-btn-red'));
                    $this->make->eDivCol();
                    $this->make->sDivCol(4);
                        $this->make->button(fa('fa-trash-o fa-lg fa-fw'),array('class'=>'remove-btn btn-block counter-btn-orange'));
                    $this->make->eDivCol();
                $this->make->eDivRow();
            $this->make->eDiv();
        $this->make->eDivCol();

        $code = $this->make->code();
        echo json_encode(array('code'=>$code,'num'=>$num));
    }
    public function clear_split(){
        $code = "";
        $trans_split_cart = sess('trans_split_cart');
        $trans_cart = sess('trans_cart');
        $trans_mod_cart = sess('trans_mod_cart');
        $upds = array();
        if(count($trans_split_cart) > 0){
            foreach ($trans_split_cart as $num => $trans) {
                foreach ($trans as $line_id => $row) {
                    $tr_cart = $trans_cart[$line_id];
                    $tr_cart['qty'] += $row['qty'];

                    $trans_cart[$line_id] = $tr_cart;
                    sess_update('trans_cart',$line_id,$trans_cart[$line_id]);
                    $upds[$line_id] = $tr_cart['qty'];
                }
            }
        }
        sess_clear('trans_split_cart');
        echo json_encode(array('content'=>$upds));
    }
    public function add_split_block($num=1,$line_id=null){
        $code = "";
        $trans_split_cart = sess('trans_split_cart');
        $trans_cart = sess('trans_cart');
        $trans_mod_cart = sess('trans_mod_cart');
        $from_qty = 0;
        if(isset($trans_cart[$line_id])){
           if(!isset($trans_split_cart[$num][$line_id])){
               $trans_split_cart[$num][$line_id] = $trans_cart[$line_id];
               $trans_split_cart[$num][$line_id]['qty'] = 0;
           }
           $tr_cart = $trans_cart[$line_id];
           $tr_cart['qty'] -= 1;
           $from_qty = $tr_cart['qty'];
           $trans_cart[$line_id] = $tr_cart;

           $tr_spl_cart = $trans_split_cart[$num][$line_id];
           $tr_spl_cart['qty'] += 1;
           $split_qty = $tr_spl_cart['qty'];
           $trans_split_cart[$num][$line_id] = $tr_spl_cart;

           sess_update('trans_split_cart',$num,$trans_split_cart[$num]);
           sess_update('trans_cart',$line_id,$trans_cart[$line_id]);
           // echo var_dump(sess('trans_split_cart'));
        }
        // $code = $this->make->code();
        echo json_encode(array('from_qty'=>$from_qty,'split_qty'=>$split_qty));
    }
    public function minus_split_block($num=1,$line_id=null){
        $code = "";
        $trans_split_cart = sess('trans_split_cart');
        $trans_cart = sess('trans_cart');
        $trans_mod_cart = sess('trans_mod_cart');
        $from_qty = 0;
        if(isset($trans_cart[$line_id])){
           if(!isset($trans_split_cart[$num][$line_id])){
               $trans_split_cart[$num][$line_id] = $trans_cart[$line_id];
           }
           $tr_cart = $trans_cart[$line_id];
           $tr_cart['qty'] += 1;
           $from_qty = $tr_cart['qty'];
           $trans_cart[$line_id] = $tr_cart;

           $tr_spl_cart = $trans_split_cart[$num][$line_id];
           $tr_spl_cart['qty'] -= 1;
           $split_qty = $tr_spl_cart['qty'];
           $trans_split_cart[$num][$line_id] = $tr_spl_cart;
           sess_update('trans_split_cart',$num,$trans_split_cart[$num]);
           sess_update('trans_cart',$line_id,$trans_cart[$line_id]);
           // echo var_dump(sess('trans_split_cart'));
        }
        // $code = $this->make->code();
        echo json_encode(array('from_qty'=>$from_qty,'split_qty'=>$split_qty));
    }
    public function remove_split_block($num=null){
        $code = "";
        $trans_split_cart = sess('trans_split_cart');
        $trans_cart = sess('trans_cart');
        $trans_mod_cart = sess('trans_mod_cart');
        $upds = array();
        if(isset($trans_split_cart[$num]) ){
            foreach ($trans_split_cart[$num] as $line_id => $row) {
                $tr_cart = $trans_cart[$line_id];
                $tr_cart['qty'] += $row['qty'];

                $trans_cart[$line_id] = $tr_cart;
                sess_update('trans_cart',$line_id,$trans_cart[$line_id]);
                $upds[$line_id] = $tr_cart['qty'];
            }
        }

        if(isset($trans_split_cart[$num]))
            sess_delete('trans_split_cart',$num);
        echo json_encode(array('content'=>$upds));
    }
    #Delivery
    public function delivery($cust_id = null, $retrieve=true){
        $this->load->model('site/site_model');
        $this->load->model('dine/customers_model');
        $this->load->model('dine/cashier_model');
        $this->load->helper('dine/cashier_helper');

        sess_clear('cust_address');

        $selected = '';
        
        if($retrieve == true)
        {
            sess_clear('trans_type_cart');
        }else{
            $type = sess('trans_type_cart');
            if(!empty($type))
            {
                $key = end(array_keys($type));
                $type = $type[$key];
                $selected = (isset($type['address_id']))?$type['address_id']: '';
            }
        }

        $data = $this->syter->spawn(null);

        $found = null; $contacts = $list = array();

        if(!empty($cust_id))
        {
            $found = $this->customers_model->get_customer($cust_id);
            $found = $found[0];
            $contacts = $this->customers_model->get_phone_number(null, $cust_id);
            $list = $this->customers_model->get_customer_address($cust_id, null);
        }  

        $data['code'] = deliveryPage($found,'delivery', $list, $selected, $contacts);
        $data['add_css'] = array('css/cashier.css','css/virtual_keyboard.css', 'js/plugins/typeaheadmap/typeaheadmap.css');
        $data['add_js'] = array('js/jquery.keyboard.extension-navigation.min.js','js/jquery.keyboard.min.js', 'js/plugins/typeaheadmap/typeaheadmap.js');
        $data['load_js'] = 'dine/cashier.php';
        $data['use_js'] = 'deliveryJs';
        $data['noNavbar'] = true;
        $this->load->view('cashier',$data);
    }

    public function pickup(){
        $this->load->model('site/site_model');
        $this->load->model('dine/cashier_model');
        $this->load->helper('dine/cashier_helper');
        $data = $this->syter->spawn(null);
        $data['code'] = deliveryPage(array(),'pickup');
        $data['add_css'] = array('css/cashier.css','css/virtual_keyboard.css');
        $data['add_js'] = array('js/jquery.keyboard.extension-navigation.min.js','js/jquery.keyboard.min.js');
        $data['load_js'] = 'dine/cashier.php';
        $data['use_js'] = 'deliveryJs';
        $data['noNavbar'] = true;
        $this->load->view('cashier',$data);
    }
    public function search_customers($search=null){
        $this->load->model('dine/customers_model');
        $found = array();
        if($search != ""){
            $found = $this->customers_model->search_customers($search);
        }

        // echo $this->db->last_query();

        $results = array();
        if(count($found) > 0 ){
            foreach ($found as $res) {
                $results[$res->cust_id] = array('name'=>ucwords(strtolower($res->fname." ".$res->mname." ".$res->lname." ".$res->suffix)),'phone'=>$res->phone_no, 'is_vip'=>$res->is_vip);
            }
        }
        echo json_encode($results);
    }
    public function search_gift_card($search = null){
        $this->load->model('dine/gift_cards_model');

        if (is_null($search)) {
            echo json_encode(array('error'=>'Please enter gift card code'));
            return false;
        }
        $search = str_replace("-", "", $search);
        $return = $this->gift_cards_model->get_gift_card_info($search,false);

        if (empty($return)) {
            echo json_encode(array('error'=>'Gift card does not exist'));
        } else {
            if ($return[0]->inactive == 1)
                echo json_encode(array('error'=>'Gift card has already been used'));
            else
                echo json_encode(array('gc_id'=>$return[0]->gc_id,'card_no'=>$return[0]->card_no,'amount'=>number_format($return[0]->amount,2)));
        }
        return false;
    }
    public function get_customers($id=null){
        $this->load->model('dine/customers_model');
        $found = $this->customers_model->get_customer($id);
        $results = array();
        if(count($found) > 0 ){
            foreach ($found as $res) {
                $results[$res->cust_id] = array(
                    'cust_id'=>ucwords(strtolower($res->cust_id)),
                    'fname'=>ucwords(strtolower($res->fname)),
                    'lname'=>ucwords(strtolower($res->lname)),
                    'mname'=>ucwords(strtolower($res->mname)),
                    'suffix'=>ucwords(strtolower($res->suffix)),
                    'email'=>ucwords(strtolower($res->email)),
                    'phone'=>ucwords(strtolower($res->phone)),
                    'street_no'=>ucwords(strtolower($res->street_no)),
                    'street_address'=>ucwords(strtolower($res->street_address)),
                    'city'=>ucwords(strtolower($res->city)),
                    'region'=>ucwords(strtolower($res->region)),
                    'landmark'=>ucwords(strtolower($res->landmark)),
                    'zip'=>$res->zip
                );
            }
        }
        echo json_encode($results);
    }
    public function get_customer_details($id=null){
        $this->load->model('dine/customers_model');
        $this->load->helper('dine/cashier_helper');

        $addr = $this->customers_model->get_customer_address($id, null);
        $contacts = $this->customers_model->get_phone_number(null, $id);
        $found = $this->customers_model->get_customer($id);   
        if(count($found) > 0 ){
            foreach ($found as $res) {
                $results[$res->cust_id] = array(
                    'cust_id'=>ucwords(strtolower($res->cust_id)),
                    'fname'=>ucwords(strtolower($res->fname)),
                    'lname'=>ucwords(strtolower($res->lname)),
                    'mname'=>ucwords(strtolower($res->mname)),
                    'suffix'=>ucwords(strtolower($res->suffix)),
                    'email'=>ucwords(strtolower($res->email)),
                    'phone'=>ucwords(strtolower($res->phone)),
                    'is_vip'=>$res->is_vip
                );
            }
        }
        $con_code = customer_no_list($id, $contacts);
        $code = customer_address_tbl($id, $addr);   

        echo json_encode(array('cust'=>$results[$res->cust_id], 'contacts'=>$con_code, 'code'=>$code));
    }
    public function new_trans($asJson=true,$type=null){
        $this->load->model('site/site_model');
        $this->load->model('dine/clock_model');
        sess_clear('trans_mod_cart');
        sess_clear('trans_cart');
        sess_clear('counter');
        sess_clear('trans_disc_cart');
        sess_clear('trans_charge_cart');
        $time = date('m/d/Y H:i:s');
        
        $user = $this->session->userdata('user');
        $get_shift = $this->clock_model->get_shift_id(date2Sql($time),$user['id']);
        $shift_id = 0;
        if(count($get_shift) > 0){
            $shift_id = $get_shift[0]->shift_id;
        }
        $counter = array(
            "datetime"=> $time,
            "sales_id"=> null,
            "shift_id"=> $shift_id,
            "terminal_id"=> TERMINAL_ID,
            "user_id"=> $user['id'],
            "type"=> $type
        );

        $this->session->set_userData('counter',$counter);
        if($asJson)
            echo json_encode($counter);
        else
            return $counter;
    }

    public function load_trans($asJson=true,$trans=null,$noSalesId=false){
        $this->load->model('site/site_model');
        $this->load->model('dine/clock_model');
        sess_clear('trans_mod_cart');
        sess_clear('trans_cart');
        sess_clear('counter');
        sess_clear('trans_disc_cart');
        sess_clear('trans_vip_disc_cart');
        sess_clear('trans_charge_cart');

        $time = date('m/d/Y H:i:s');
        $user = $this->session->userdata('user');
        $get_shift = $this->clock_model->get_shift_id(date2Sql($time),$user['id']);
        $shift_id = 0;
        
        if(count($get_shift) > 0){
            $shift_id = $get_shift[0]->shift_id;
        }

        $order=$trans['order'];
        $details=$trans['details'];
        $discounts = $trans['discounts'];
        $vip_discount = $trans['vip_discount'];
        $charges = $trans['charges'];
        $sales_id = $order['sales_id'];
        if($noSalesId)
            $sales_id = "";
        $counter = array(
            "datetime"=> sql2DateTime($order['datetime']),
            "shift_id"=> $shift_id,
            "sales_id"=> $sales_id,
            "terminal_id"=> TERMINAL_ID,
            "user_id"=> $user['id'],
            "type"=> $order['type']
        );
        $trans_type_cart = array();
        if($order['type'] == 'dinein'){
            $trans_type_cart[0]['type']='dinein';
            $trans_type_cart[0]['table']=$order['table_id'];
            $trans_type_cart[0]['guest']=$order['guest'];
        }

        $trans_cart = array();
        $trans_mod_cart = array();
        $trans_disc_cart = array();
        $trans_charge_cart = array();
        $trans_vip_disc_cart = array();

        foreach ($details as $line_id => $menu) {
            $trans_cart[$line_id] = array(
                "menu_id"=> $menu['menu_id'],
                "name"=> $menu['name'],
                "cost"=> $menu['price'],
                "qty"=> $menu['qty'],
                "no_tax"=> $menu['no_tax'],
                "cancelled"=>$menu['cancelled']
            );

            if(count($menu['modifiers']) > 0){
                foreach ($menu['modifiers'] as $mod) {
                    if($mod['line_id'] == $line_id){
                        $trans_mod_cart[] = array(
                            "sales_mod_id"=>$mod['sales_mod_id'],
                            "trans_id"=>$mod['line_id'],
                            "mod_id"=>$mod['id'],
                            "menu_id"=>$menu['menu_id'],
                            "menu_name"=>$menu['name'],
                            "name"=>$mod['name'],
                            "cost"=>$mod['price'],
                            "qty"=>$mod['qty'],
                            "cancelled"=>$mod['cancelled']
                        );
                    }
                }#END FOR EACH
            }#END IF
        }
        if(count($discounts) > 0){
            foreach ($discounts as $disc_id => $dc) {
                $trans_disc_cart[$disc_id] = array(
                    "name"  => $dc['name'],
                    "code"  => $dc['code'],
                    "bday"  => $dc['bday'],
                    "guest" => $dc['guest'],
                    "disc_rate" => $dc['disc_rate'],
                    "disc_code" => $dc['disc_code'],
                    "disc_type" => $dc['disc_type'],
                    "items" => $dc['items']
                );
            }
        }


        if(!empty($vip_discount)){
            foreach($vip_discount as $type=>$vdc){
             
                $trans_vip_disc_cart['vip'] = array(
                    "disc_rate" => $vdc['disc_rate'],
                    "disc_type" => $vdc['disc_type'],
                    "is_absolute" => $vdc['is_absolute']
                );
            }
        }


        if(count($charges) > 0){
            foreach ($charges as $charge_id => $dc) {
                $trans_charge_cart[$charge_id] = array(
                    "name"  => $dc['name'],
                    "code"  => $dc['code'],
                    "amount"  => $dc['amount'],
                    "absolute" => $dc['absolute']
                );
            }
        }
        $this->session->set_userData('trans_cart',$trans_cart);
        $this->session->set_userData('trans_mod_cart',$trans_mod_cart);
        if(count($trans_type_cart) > 0){
            $this->session->set_userData('trans_type_cart',$trans_type_cart);
        }
        if(count($trans_disc_cart) > 0){
            $this->session->set_userData('trans_disc_cart',$trans_disc_cart);
        }
        if(count($trans_vip_disc_cart) > 0){
            $this->session->set_userData('trans_vip_disc_cart',$trans_vip_disc_cart);
        }
        if(count($trans_charge_cart) > 0){
            $this->session->set_userData('trans_charge_cart',$trans_charge_cart);
        }
        $this->session->set_userData('counter',$counter);

        if($asJson)
            echo json_encode($counter);
        else
            return $counter;
    }
    public function get_trans_cart($asJson=true){
        $trans_cart = sess('trans_cart');
        $trans_mod_cart = sess('trans_mod_cart');
        $trans_cancelled_cart = sess('cancelled_items');

        $order = null;

        foreach ($trans_cart as $trans_id => $menu) {
            $order[$trans_id] =  array(
                "cancelled"=> $menu['cancelled'],
                "menu_id"=> $menu['menu_id'],
                "name"=> $menu['name'],
                "cost"=> $menu['cost'],
                "qty"=> $menu['qty'],
            );
            
            // if(!empty( $trans_cancelled_cart['menu']))
            // {
            //     if (in_array($menu['menu_id'], $trans_cancelled_cart['menu'])) {
            //         $order[$trans_id]['cancelled'] = 1;
            //     }else
            //         $order[$trans_id]['cancelled'] = 0;
            // }

            $mods = array();
            if(count($trans_mod_cart) > 0){
                foreach ($trans_mod_cart as $id => $mod) {

                    if($mod['trans_id'] == $trans_id){
                        $mods[$id] = array(
                            "sales_mod_id"=>$mod['sales_mod_id'],
                            "trans_id"=>$mod['trans_id'],
                            "mod_id"=>$mod['mod_id'],
                            "menu_id"=>$mod['menu_id'],
                            "menu_name"=>$mod['menu_name'],
                            "name"=>$mod['name'],
                            "cost"=>$mod['cost'],
                            "qty"=>$mod['qty'],
                            "cancelled"=>$mod['cancelled']
                        );

                        // if(!empty( $trans_cancelled_cart['mod']))
                        // {
                        //     if (in_array($mod['sales_mod_id'], $trans_cancelled_cart['mod'])) {
                        //         $mods[$id]['cancelled'] = 1;
                        //     }else
                        //         $mods[$id]['cancelled'] = 0;
                        // }
                    }#IF
                }#FOREACH
            }#IF
            $order[$trans_id]['modifiers'] = $mods;

        }

        if($asJson)
            echo json_encode($order);
        else
            return $order;
    }

    public function get_trans_charges($asJson=true){
        $trans_charge_cart = sess('trans_charge_cart');
        $charge = null;
        foreach ($trans_charge_cart as $charge_id => $dc) {
            $charge[$charge_id] = array(
                "name"  => $dc['name'],
                "code"  => $dc['code'],
                "amount"  => $dc['amount'],
                "absolute" => $dc['absolute']
            );
        }
        if($asJson)
            echo json_encode($charge);
        else
            return $charge;
    }
    public function get_order($asJson=true,$sales_id=null){
        /*
         * -------------------------------------------
         *   Load receipt data
         * -------------------------------------------
        */
        $this->load->model('dine/cashier_model');
        $orders = $this->cashier_model->get_trans_sales($sales_id);
        $order = array();
        $details = array();
        foreach ($orders as $res) {
            $order = array(
                "sales_id"=>$res->sales_id,
                'ref'=>$res->trans_ref,
                "type"=>$res->type,
                "table_id"=>$res->table_id,
                "table_name"=>$res->table_name,
                "guest"=>$res->guest,
                "user_id"=>$res->user_id,
                "branch_id"=>$res->branch_id,
                "address_id"=>$res->address_id,
                "name"=>$res->username,
                "terminal_id"=>$res->terminal_id,
                "terminal_name"=>$res->terminal_name,
                "shift_id"=>$res->shift_id,
                "datetime"=>$res->datetime,
                "amount"=>$res->total_amount,
                "balance"=>$res->total_amount - $res->total_paid,
                "paid"=>$res->paid,
                "printed"=>$res->printed,
                "instruction"=>$res->instruction,
                "on_hold"=>$res->on_hold,
                "confirmed"=>$res->confirmed,
                "customer_id"=>$res->customer_id,
                "delivered"=>$res->delivered,
                "delivery_time"=>$res->delivery_time,
                "order_delivery_date"=>$res->order_delivery_date,
                "order_delivery_time"=>$res->order_delivery_time,
                // "pay_type"=>$res->pay_type,
                // "pay_amount"=>$res->pay_amount,
                // "pay_ref"=>$res->pay_ref,
                // "pay_card"=>$res->pay_card,
            );
        }
        $order_menus = $this->cashier_model->get_trans_sales_menus(null,array("trans_sales_menus.sales_id"=>$sales_id));
        $order_mods = $this->cashier_model->get_trans_sales_menu_modifiers(null,array("trans_sales_menu_modifiers.sales_id"=>$sales_id));
        // $sales_discs = $this->cashier_model->get_trans_sales_discounts(null,array("trans_sales_discounts.sales_id"=>$sales_id));
        $sales_discs = $this->cashier_model->get_trans_sales_discounts(null,array("trans_sales_discounts.sales_id"=>$sales_id, "trans_sales_discounts.disc_id  IS NOT NULL" => array('use'=>'where','val'=>null,'third'=>false), ));
        $sales_vip_discs = $this->cashier_model->get_trans_sales_discounts(null,array("trans_sales_discounts.sales_id"=>$sales_id,"trans_sales_discounts.type"=>'vip'));

        $sales_tax = $this->cashier_model->get_trans_sales_tax(null,array("trans_sales_tax.sales_id"=>$sales_id));
        $sales_payments = $this->cashier_model->get_trans_sales_payments(null,array("trans_sales_payments.sales_id"=>$sales_id));
        $sales_no_tax = $this->cashier_model->get_trans_sales_no_tax(null,array("trans_sales_no_tax.sales_id"=>$sales_id));
        $sales_charges = $this->cashier_model->get_trans_sales_charges(null,array("trans_sales_charges.sales_id"=>$sales_id));
        $pays = array();

        foreach ($sales_payments as $py) {
            $pays[$py->payment_id] = array(
                    "sales_id"      => $py->sales_id,
                    "payment_type"  => $py->payment_type,
                    "amount"        => $py->amount,
                    "to_pay"        => $py->to_pay,
                    "reference"     => $py->reference,
                    "card_type"     => $py->card_type,
                    "user_id"       => $py->user_id,
                    "datetime"      => $py->datetime,
                );
        }
        foreach ($order_menus as $men) {
            $details[$men->line_id] = array(
                "id"=>$men->sales_menu_id,
                "menu_id"=>$men->menu_id,
                "name"=>$men->menu_name,
                "code"=>$men->menu_code,
                "price"=>$men->price,
                "cost"=>$men->price,
                "qty"=>$men->qty,
                "no_tax"=>$men->no_tax,
                "discount"=>$men->discount,
                "cancelled"=>$men->cancelled,
            );
            $mods = array();


            foreach ($order_mods as $mod) {
                if($mod->line_id == $men->line_id){
                    $mods[$mod->sales_mod_id] = array(
                        "sales_mod_id"=>$mod->sales_mod_id,
                        "id"=>$mod->mod_id,
                        "line_id"=>$mod->line_id,
                        "name"=>$mod->mod_name,
                        "price"=>$mod->price,
                        "cost"=>$mod->price,
                        "qty"=>$mod->qty,
                        "discount"=>$mod->discount,
                        "cancelled"=>$mod->cancelled,
                    );
                }
            }
            $details[$men->line_id]['modifiers'] = $mods;
        }
        $discounts = array();
        foreach ($sales_discs as $dc) {
            $items = array();
            if($dc->items != ""){
                $items = explode(',', $dc->items);
            }
            $discounts[$dc->disc_id] = array(
                    "name"  => $dc->name,
                    "code"  => $dc->code,
                    "bday"  => sql2Date($dc->bday),
                    "guest" => $dc->guest,
                    "disc_rate" => $dc->disc_rate,
                    "disc_code" => $dc->disc_code,
                    "disc_type" => $dc->type,
                    "items" => $items,
                    "amount" => $dc->amount,
                );
        }

        $vip_discounts = array();
        foreach($sales_vip_discs as $vdc){
            $vip_discounts['vip']=array(
                "disc_rate" => $vdc->disc_rate,
                "disc_type" => $vdc->type,
                "is_absolute" =>$vdc->is_absolute,
            );
        }

        $tax = array();
        foreach ($sales_tax as $tx) {
            $tax[$tx->sales_tax_id] = array(
                    "sales_id"  => $tx->sales_id,
                    "name"  => $tx->name,
                    "rate" => $tx->rate,
                    "amount" => $tx->amount
                );
        }
        $no_tax = array();
        foreach ($sales_no_tax as $nt) {
            $no_tax[$nt->sales_no_tax_id] = array(
                "sales_id" => $nt->sales_id,
                "amount" => $nt->amount,
            );
        }
        $charges = array();
        foreach ($sales_charges as $ch) {
            $charges[$ch->charge_id] = array(
                    "name"  => $ch->charge_name,
                    "code"  => $ch->charge_code,
                    "amount"  => $ch->rate,
                    "absolute" => $ch->absolute
                );
        }
        if($asJson)
            echo json_encode(array('order'=>$order,"details"=>$details,"vip_discount"=>$vip_discounts,"discounts"=>$discounts,"taxes"=>$tax,"no_tax"=>$no_tax,"payments"=>$pays,"charges"=>$charges));
        else
            return array('order'=>$order,"details"=>$details,"vip_discount"=>$vip_discounts, "discounts"=>$discounts,"taxes"=>$tax,"no_tax"=>$no_tax,"payments"=>$pays,"charges"=>$charges);
    }
    public function get_order_header($asJson=true,$sales_id=null,$args=array()){
        $this->load->model('dine/cashier_model');
        $orders = $this->cashier_model->get_trans_sales($sales_id,$args);
        foreach ($orders as $res) {
            $order = array(
                "sales_id"=>$res->sales_id,
                "type"=>$res->type,
                "user_id"=>$res->user_id,
                "name"=>$res->username,
                "terminal_id"=>$res->terminal_id,
                "terminal_name"=>$res->terminal_name,
                "shift_id"=>$res->shift_id,
                "datetime"=>$res->datetime,
                "amount"=>$res->total_amount,
                "balance"=>$res->total_amount - $res->total_paid,
                "paid"=>$res->paid,
                "delivery_code"=>$res->delivery_code
            );
        }
        if($asJson)
            echo json_encode($order);
        else
            return $order;
    }
    public function get_menu_categories($asJson=true){
        $this->load->model('dine/menu_model');
        $categories = $this->menu_model->get_menu_categories(null,true);
        $json = array();
        foreach ($categories as $cat) {
            $json[$cat->menu_cat_id] = array(
                'name'=>$cat->menu_cat_name
            );
        }
        echo json_encode($json);
    }
    public function get_menus($cat_id=null,$item_id=null,$asJson=true){
        $this->load->model('dine/menu_model');
        $this->load->model('dine/cashier_model');
        $this->load->model('site/site_model');

        $trans_type_cart = sess('trans_type_cart');

        if(count($trans_type_cart) > 0)
            $trans_type_cart = $trans_type_cart[count($trans_type_cart) - 1];

        if(isset($trans_type_cart['branch_id'])){
            $branch_id = $trans_type_cart['branch_id'];
        }
        if(!isset($branch_id))
            $branch_id =  $this->session->userdata('branch_id');

        $menus = $this->menu_model->get_menus($item_id,$cat_id,true, $branch_id);

        $json = array();

        if(count($menus) > 0){
            $ids = array();
            foreach ($menus as $res) {
                $json[$res->menu_id] = array(
                    "name"=>$res->menu_name,
                    "category"=>$res->menu_cat_id,
                    "cost"=>$res->cost,
                    "no_tax"=>$res->no_tax,
                    "sched_id"=>$res->menu_sched_id,
                    "branch_menu_id"=>$res->branch_menu_id,
                );
                $ids[] = $res->menu_id;
            }
            $promos = $this->cashier_model->get_menu_promos($ids);
            $prs = array();
            $prm = array();
            foreach ($promos as $pr) {
                $prs[] = $pr->promo_id;
                $prm[$pr->item_id][] = array('id'=>$pr->promo_id,'val'=>$pr->value,'abs'=>$pr->absolute);
            }

            $time = date('m/d/Y H:i:s');
            $day = strtolower(date('D',strtotime($time)));
            $sched = $this->cashier_model->get_menu_promo_schedule($prs,$day,date2SqlDateTime($time));
            // echo $this->db->last_query();
            // die();
            $schs = array();
            foreach ($sched as $sc) {
                $schs[] = $sc->promo_id;
            }

            foreach ($json as $menu_id => $opt) {
                if(isset($prm[$menu_id])){
                    foreach ($prm[$menu_id] as $p) {
                        if(in_array($p['id'], $schs)){
                            if($p['abs'] == 0){
                                $opt['cost'] -= $pr->value;

                            }
                            else{
                                $opt['cost'] -=  ($pr->value / 100) * $opt['cost'];
                            }
                            $json[$menu_id] = $opt;
                            break;
                        }
                    }####
                }
            }

        }

        echo json_encode($json);
    }
    public function get_menu_modifiers($menu_id=null){
        $this->load->model('dine/menu_model');
        $this->load->model('dine/mods_model');
        $menu_mods = $this->menu_model->get_menu_modifiers($menu_id);
        $group = array();
        $grp = array();
        if(count($menu_mods) > 0){
            foreach ($menu_mods as $res) {
                $group[$res->mod_group_id] = array(
                    "name"=>$res->mod_group_name,
                    "mandatory"=>$res->mandatory,
                    "multiple"=>$res->multiple
                );

                $grp[] = $res->mod_group_id;
            }
            $details = $this->mods_model->get_modifier_group_details(null,$grp);
            $dets = array();
            foreach ($details as $det) {
                $dets=array(
                    "name"=>$det->mod_name,
                    "cost"=>$det->mod_cost
                );
                $group[$det->mod_group_id]['details'][$det->mod_id] = $dets;
            }
        }
        echo json_encode($group);
    }
    public function add_trans_modifier(){
        $wagon = array();
        $error = null;
        $name  = 'trans_mod_cart';
        $id = null;
        $row = null;
        if($this->session->userData($name)){
            $wagon = $this->session->userData($name);
        }
        $row = $this->input->post();
        if(count($wagon) > 0){
            foreach($wagon as $key => $det) {
                // echo $det['mod_id'].' == '.$row['mod_id'].' && '.$det['trans_id'].' == '.$row['trans_id'];
                if($det['mod_id'] == $row['mod_id'] && $det['trans_id'] == $row['trans_id']){
                    $error = 'It is already added';
                    break;
                }
                else{
                   $error = null;
                }
            }
            if($error == null)
                    $wagon[] = $row;
        }
        else{
            $wagon[] = $row;
        }
        $id = max(array_keys($wagon));
        $this->session->set_userData($name,$wagon);
        echo json_encode(array("items"=>$row,"id"=>$id,"error"=>$error));
    }
    public function delete_trans_menu_modifier($trans_id=null){
        $wagon = array();
        $error = null;
        $name  = 'trans_mod_cart';
        $id = null;
        $row = null;
        $wagon = $this->session->userData($name);
        foreach ($wagon as $key => $det) {
            if($det['trans_id'] == $trans_id){
                unset($wagon[$key]);
            }
        }
        $this->session->set_userData($name,$wagon);
        echo json_encode(array("items"=>$row,"id"=>$id));
    }
    public function update_trans_qty($trans_id=null){
        $wagon = array();
        $error = null;
        $name  = 'trans_cart';
        $wagon = $this->session->userData($name);
        $row = $wagon[$trans_id];
        $char = $this->input->post('operator');
        $val = $this->input->post('value');
        switch($char){
            case "times":
                $row['qty'] *= $val;
                break;
            case "equal":
                $row['qty'] = $val;
                break;
            case "plus":
                $row['qty'] += $val;
                break;
            case "minus":
                $row['qty'] -= $val;
                if($row['qty'] <= 0)
                    $row['qty'] = 1;
                break;
            case "none":
                $row['qty'] = $this->input->post('qty');
                break;
        }
        $wagon[$trans_id] = $row;
        $this->session->set_userData($name,$wagon);
        echo json_encode(array("error"=>null,"qty"=>$row['qty']));
    }
    public function get_max_price_and_qty_for_disc($guest=0,$trans_cart=array(),$max=0,$qty=0){
        $cost = $discs = array();
        $max_qty = 0;
        $miss_qty = 0;
        
        foreach ($trans_cart as $line_id => $row)
            $cost[$line_id] = $row['cost'];
        
        array_multisort($cost, SORT_DESC, $trans_cart);
        
        foreach($trans_cart as $line_id=>$val)
        {
            $max_qty += $val['qty'];
            
            if($max_qty < $guest)  
            {
                $discs[$line_id] = $val;
            }else if($max_qty == $guest){
                $discs[$line_id] = $val;

                return $discs;
            }else if($max_qty > $guest)
            {
                $max_qty -= $val['qty'];  //original
                $missing = $guest-$max_qty;
                
                if($missing <= 0)
                {
                    unset($discs[$line_id]);
                    break;
                    return $discs;
                }else{
                    $val['qty'] = $missing;
                    $max_qty += $missing;
                    $discs[$line_id] = $val;
                    if($max_qty == $guest){
                        break;
                        return $discs;
                    }
                }    
            }    
            else      
                $max_qty-=$val['qty'];
        }
        
        return $discs;
       
    }
    public function total_trans($asJson=true,$cart=null,$disc_cart=null,$charge_cart=null, $vip_disc_cart=null){
        $trans_cart = array();
        if($this->session->userData('trans_cart')){
            $trans_cart = $this->session->userData('trans_cart');
        }
        $trans_mod_cart = array();
        if($this->session->userData('trans_mod_cart')){
            $trans_mod_cart = $this->session->userData('trans_mod_cart');
        }
        if(is_array($cart)){
            $trans_cart = $cart;
        }

        $total = 0;
        $discount = 0;
        if(count($trans_cart) > 0){
            foreach ($trans_cart as $trans_id => $trans){
                if(isset($trans['cost']))
                    $cost = $trans['cost'];
                if(isset($trans['price']))
                    $cost = $trans['price'];

                if(isset($trans['modifiers'])){
                    foreach ($trans['modifiers'] as $trans_mod_id => $mod) {
                        if($trans_id == $mod['line_id'])
                            $cost += $mod['price'];
                    }
                }
                else{
                    if(count($trans_mod_cart) > 0){
                        foreach ($trans_mod_cart as $trans_mod_id => $mod) {
                            if($trans_id == $mod['trans_id'])
                                $cost += $mod['cost'];
                        }
                    }
                }
                $total += $trans['qty'] * $cost;
            }
        }
        $trans_disc_cart = sess('trans_disc_cart');
        if(is_array($disc_cart)){
            $trans_disc_cart = $disc_cart;
        }
        $trans_vip_disc_cart = sess('trans_vip_disc_cart');
        if(is_array($vip_disc_cart)){
            $trans_vip_disc_cart = $vip_disc_cart;
        }
      
        $discs = array();
        if(count($trans_disc_cart) > 0 ){
         
            foreach ($trans_disc_cart as $disc_id => $row) {

                if($disc_id == 'SNDISC' || $row['disc_code'] == 'SNDISC')
                {                    
                    $disc_guest = 1;
                    $guest = (isset($row['guest'])) ? $row['guest'] : count($row['persons']);
                    $maxes = $this->get_max_price_and_qty_for_disc($guest,$trans_cart);
                    
                    $highest_disc = 0;
                    foreach($maxes as $key => $val)
                    {
                        $highest_disc +=($val['cost']*$val['qty']);
                    }

                    if(isset($row['disc_type']))
                    {
                        $rate = $row['disc_rate'];

                        switch ($row['disc_type']){
                               case "highest":
                                    $discs[] = array('type'=>$row['disc_code'],'amount'=>($rate / 100) * $highest_disc);
                                    $discount += ($rate / 100) * $highest_disc;
                                    $total -= $discount;
                                    break;
                                case "item":
                                        $item_cost = 0;
                                        foreach ($row['items'] as $line) {
                                            if(isset($trans_cart[$line])){
                                                if(isset($trans_cart[$line]['cost']))
                                                    $cost =  $trans_cart[$line]['cost'];
                                                if(isset( $trans_cart[$line]['price']))
                                                    $cost =  $trans_cart[$line]['price'];
                                                $item_cost += $cost;
                                                ###
                                                if(isset($trans_cart[$line]['modifiers'])){
                                                    foreach ($trans_cart[$line]['modifiers'] as $trans_mod_id => $mod) {
                                                        if($line == $mod['line_id'])
                                                            $item_cost += $mod['price'];
                                                    }
                                                }
                                                else{
                                                    if(count($trans_mod_cart) > 0){
                                                        foreach ($trans_mod_cart as $trans_mod_id => $mod) {
                                                            if($line == $mod['trans_id']){
                                                                $item_cost += $mod['cost'];
                                                            }
                                                        }
                                                    }
                                                }
                                                ####
                                            }
                                        }
                                        $discs[] = array('type'=>$row['disc_code'],'amount'=>($rate / 100) * $item_cost);
                                        $discount += ($rate / 100) * $item_cost;
                                        $total -= $discount;
                                        break;
                                case "equal":
                                        $divi = $total/$row['guest'];
                                        $discs[] = array('type'=>$row['disc_code'],'amount'=>($rate / 100) * $divi);
                                        $discount += ($rate / 100) * $divi;
                                        $total -= $discount;
                                        break;
                                default:
                                    $discs[] = array('type'=>$row['disc_code'],'amount'=>($rate / 100) * $total);
                                    $discount += ($rate / 100) * $total;
                                    $total -= $discount;
                                    break;

                        }
                    }

                }
            }
        }   
     
        $vip_discount = 0;
        if(!empty($trans_vip_disc_cart))
        {

            $trans_vip_disc_cart = $trans_vip_disc_cart['vip'];

            if($trans_vip_disc_cart['is_absolute'] == 0)
            {   
                $vip_discount = ($trans_vip_disc_cart['disc_rate']/100) * $total;
                $total-=$vip_discount;
            }else{
                $vip_discount = $trans_vip_disc_cart['disc_rate'];
                $total-=$vip_discount;
            }
        }

        $discount+=$vip_discount;


        $trans_charge_cart = sess('trans_charge_cart');
        if(is_array($charge_cart)){
            $trans_charge_cart = $charge_cart;
        }
        #CHARGES
        $charges = array();
        $total_charges = 0;
        if(count($trans_charge_cart) > 0 ){
            $tax = $this->get_tax_rates(false);
            $am = 0;
            if(count($tax) > 0){
                $taxable_amount = 0;
                $not_taxable_amount = 0;
                foreach ($trans_cart as $trans_id => $v) {
                    if(isset($v['cost']))
                        $cost = $v['cost'];
                    if(isset($v['price']))
                        $cost = $v['price'];
                    ####################
                    if(isset($v['modifiers'])){
                        foreach ($v['modifiers'] as $trans_mod_id => $m) {
                            if($trans_id == $m['line_id']){
                                $cost += $m['price'];
                            }
                        }
                    }
                    else{
                        if(count($trans_mod_cart) > 0){
                            foreach ($trans_mod_cart as $trans_mod_id => $m) {
                                if($trans_id == $m['trans_id']){
                                    $cost += $m['cost'];
                                }
                            }
                        }
                    }
                    ####################
                    foreach ($trans_disc_cart as $disc_id => $row) {
                        $rate = $row['disc_rate'];
                        switch ($row['disc_type']) {
                            case "item":
                                    if( in_array($trans_id, $row['items'])){
                                        $discount = ($rate / 100) * $cost;
                                        $cost -= $discount;
                                    }
                                    break;
                            case "equal":
                                    $divi = $cost/$row['guest'];
                                    $discount = ($rate / 100) * $divi;
                                    $cost -= $discount;
                                    break;
                            default:
                                $discount = ($rate / 100) * $cost;
                                $cost -= $discount;
                        }
                    }

                    if($v['no_tax'] == 0){
                        $taxable_amount += $cost * $v['qty'];
                    }
                    else{
                        $not_taxable_amount += $cost * $v['cost'];
                    }
                }

                $am = $taxable_amount;
                $trans_sales_tax = array();
                foreach ($tax as $tax_id => $tx) {
                    $rate = ($tx['rate'] / 100);
                    $tax_value = ($am / ($rate + 1) ) * $rate;
                    $am -= $tax_value;
                }
            }
            else{
                $am = $total;
            }
            foreach ($trans_charge_cart as $charge_id => $opt) {
                $charge_amount = $opt['amount'];
                if($opt['absolute'] == 0){
                    $charge_amount = ($opt['amount'] / 100) * $am;
                }
                $charges[$charge_id] = array('code'=>$opt['code'],
                                   'name'=>$opt['name'],
                                   'amount'=>$charge_amount,
                                   );
                $total_charges += $charge_amount;
            }
            $total += $total_charges;
        }

        if($asJson)
            echo json_encode(array('total'=>$total,'discount'=>$discount,'discs'=>$discs,'charge'=>$total_charges,'charges'=>$charges));
        else
            return array('total'=>$total,'discount'=>$discount,'discs'=>$discs,'charge'=>$total_charges,'charges'=>$charges);
    }
    public function get_tax_rates($asJson=true,$tax_id=null){
        $this->load->model('dine/settings_model');
        $taxes = $this->settings_model->get_tax_rates($tax_id);
        $tax = array();
        foreach ($taxes as $res) {
            $tax[$res->tax_id] = array(
                "name"=>$res->name,
                "rate"=>$res->rate
            );
        }
        if($asJson)
            echo json_encode($tax);
        else
            return $tax;
    }
    public function finish_trans_del($sales_id){
        $this->load->model('dine/cashier_model');
        $this->load->model('core/ref_model');
        $this->load->model('site/site_model');

        $trans_type = CALL_CENTER_TRANS;
        $time = date('m/d/Y H:i:s');

        $ref = $this->ref_model->get_next_ref($trans_type);
        $this->ref_model->db->trans_start();
        $this->ref_model->save_ref($trans_type,$ref);

        $this->cashier_model->update_trans_sales(array('trans_ref'=>$ref,'paid'=>1, 'datetime'=>date2SqlDateTime($time)),$sales_id);
        $this->ref_model->db->trans_complete(); 

        echo json_encode(array('msg'=>'Sales updated', 'sales_id'=>$sales_id));
    }
    public function submit_trans($asJson=true,$submit=null,$void=false,$void_ref=null,$cart=null,$mod_cart=null,$print=false){  
        
        $this->load->model('dine/cashier_model');
        $this->load->model('core/ref_model');
        $this->load->model('dine/trans_model');

        $confirmed = $this->input->post('confirmed');

        $counter = sess('counter');
        $trans_cart = sess('trans_cart');
        $trans_mod_cart = sess('trans_mod_cart');
        $trans_type_cart = sess('trans_type_cart');
        $trans_disc_cart = sess('trans_disc_cart');
        $trans_charge_cart = sess('trans_charge_cart');
        $trans_instruction = sess('trans_instruction');
        $trans_vip_disc_cart = sess('trans_vip_disc_cart');
        $trans_order_date_time  =   sess('trans_date_time');

        $totals  = $this->total_trans(false,$cart);
        $total_amount = $totals['total'];
        $error = null;
        $act = null;
        $sales_id = null;
        $type = null;
        $type_id = CALL_CENTER_TRANS;
        $print_echo = array();
        if($void === true){
            $type_id = SALES_VOID_TRANS;
        }

        if($void_ref == null || $void_ref == 0)
            $void_ref = null;

        if(count($trans_cart) <= 0){
            $error = "Error! There are no items.";
        }
        else if(count($counter) <= 0){
            $error = "Error! Shift or User is invalid.";
        }
        else{
            if(is_array($cart)){
                $trans_cart = $cart;
            }
            if(is_array($mod_cart)){
                $trans_mod_cart = $mod_cart;
            }
            $type = $counter['type'];
            #save sa trans_sales
            $delivery_time = $instruction = $table = $customer = $branch_id = $address_id = null;
            $guest = 0;

            if(count($trans_type_cart) > 0)
                $trans_type_cart = $trans_type_cart[count($trans_type_cart) - 1];

            if(count($trans_instruction) > 0)
                $trans_instruction = $trans_instruction[count($trans_instruction) - 1];
            else{   
                if(!empty($trans_instruction))    
                    $trans_instruction = $trans_instruction[0];
                else
                    $trans_instruction = array();
            }

            if(isset($trans_type_cart['table']))
                $table = $trans_type_cart['table'];
            
            if(isset($trans_type_cart['guest']))
                $guest = $trans_type_cart['guest'];
            
            if(isset($trans_type_cart['customer_id']))
                $customer = $trans_type_cart['customer_id'];
             
            if($customer == '')
                $customer = $this->session->userdata('customer_id');
            
            if(isset($trans_type_cart['branch_id']))
                $branch_id = $trans_type_cart['branch_id'];

            if(isset($trans_type_cart['travel_time']))
                $delivery_time = $trans_type_cart['travel_time'];
            
            if($branch_id == '')
                $branch_id = $this->session->userdata('branch_id');
            
            if(isset($trans_type_cart['address_id']))
                $address_id = $trans_type_cart['address_id'];
            
            if($address_id == '')
                $address_id = $this->session->userdata('address_id');
            
            if(isset($trans_instruction['instruction']))
                $instruction = $trans_instruction['instruction'];

            if($trans_order_date_time)
            {
                end($trans_order_date_time);
                $key = key($trans_order_date_time);
                $trans_order_date_time = $trans_order_date_time[$key];
            } 

            extract($trans_order_date_time);
          
            $order_delivery_date = (empty($order_delivery_date) ? date('Y-m-d') : date('Y-m-d',strtotime(str_replace('-', '/', $order_delivery_date))));
            $order_delivery_time = (isset($order_delivery_time) ?  date("H:i", strtotime($order_delivery_time)) : date("H:i"));
          
            $trans_sales = array(
                "user_id"       => $counter['user_id'],
                "branch_id"     => $branch_id,
                "address_id"     => $address_id,
                "type_id"       => $type_id,
                "shift_id"      => $counter['shift_id'],
                "terminal_id"   => $counter['terminal_id'],
                "type"          => $counter['type'],
                "datetime"      => date2SqlDateTime($counter['datetime']),
                "total_amount"  => $total_amount,
                "void_ref"      => $void_ref,
                "memo"          => null,
                "table_id"      => $table,
                "guest"         => $guest,
                "customer_id"   => $customer,
                "instruction"   => $instruction,
                "delivery_time" => $delivery_time,
                "order_delivery_time" => $order_delivery_time,
                "order_delivery_date" => (isset($order_delivery_date) ? $order_delivery_date : ''),
            );

            if(isset($counter['sales_id']) && $counter['sales_id'] != null){
                $sales_id = $counter['sales_id'];

                $void_trans_sales = array('inactive'=>1);
                $this->cashier_model->update_trans_sales($void_trans_sales,$sales_id);

                // $this->cashier_model->delete_trans_sales_menus($sales_id);
                // $this->cashier_model->delete_trans_sales_menu_modifiers($sales_id);
                // $this->cashier_model->delete_trans_sales_discounts($sales_id);
                // $this->cashier_model->delete_trans_sales_charges($sales_id);
                // $this->cashier_model->delete_trans_sales_tax($sales_id);
                // $this->cashier_model->delete_trans_sales_no_tax($sales_id);
                $act="update";
                $sales_id = $this->cashier_model->add_trans_sales($trans_sales);


                $trans_type = DELIVERY_CODE;
        
                $ref = $this->ref_model->get_next_ref($trans_type);
                $this->ref_model->db->trans_start();
                    $this->ref_model->save_ref($trans_type,$ref);
                    $this->cashier_model->update_trans_sales(array('delivery_code'=>$ref),$sales_id);
                $this->ref_model->db->trans_complete();

                $act="add";

                // if($confirmed == 3)
                // {
                //     $this->cashier_model->update_trans_sales(array('confirmed'=>0),$sales_id);
                // }

                if($submit === null || $submit == 0 || $submit == null)
                    site_alert('Transaction Updated.','success');
            
            }else{

                $sales_id = $this->cashier_model->add_trans_sales($trans_sales);
                // $trans_type = CALL_CENTER_TRANS;
        
                // $ref = $this->ref_model->get_next_ref($trans_type);
                // $this->ref_model->db->trans_start();
                //     $this->ref_model->save_ref($trans_type,$ref);
                    
                  
                //     $this->cashier_model->update_trans_sales(array('trans_ref'=>$ref,'paid'=>1),$sales_id);
                // $this->ref_model->db->trans_complete();


                $trans_type = DELIVERY_CODE;
        
                $ref = $this->ref_model->get_next_ref($trans_type);
                $this->ref_model->db->trans_start();
                    $this->ref_model->save_ref($trans_type,$ref);
                    $this->cashier_model->update_trans_sales(array('delivery_code'=>$ref),$sales_id);
                $this->ref_model->db->trans_complete();

                $act="add";
            }

            #save sa trans_sales_menus
            $trans_sales_menu = array();
            foreach ($trans_cart as $trans_id => $v) {
                $trans_sales_menu[] = array(
                    "sales_id" => $sales_id,
                    "line_id" => $trans_id,
                    "menu_id" => $v['menu_id'],
                    "price" => $v['cost'],
                    "qty" => $v['qty'],
                    "discount"=> 0
                );
            }
            $this->cashier_model->add_trans_sales_menus($trans_sales_menu);
            #save sa trans_sales_menu_modifiers
            if(count($trans_mod_cart) > 0){
                $trans_sales_menu_modifiers = array();
                foreach ($trans_mod_cart as $trans_mod_id => $m) {
                    if(isset($trans_cart[$m['trans_id']])){
                        $trans_sales_menu_modifiers[] = array(
                            "sales_id" => $sales_id,
                            "line_id" => $m['trans_id'],
                            "menu_id" => $m['menu_id'],
                            "mod_id" => $m['mod_id'],
                            "price" => $m['cost'],
                            "qty" => $m['qty'],
                            "discount"=> 0
                        );
                    }
                }
                if(count($trans_sales_menu_modifiers) > 0)
                    $this->cashier_model->add_trans_sales_menu_modifiers($trans_sales_menu_modifiers);
           
            }

            #save sa trans_sales_discounts
            // print_r($trans_disc_cart);
            // die();
            // if(count($trans_disc_cart) > 0){
            //     $trans_sales_disc_cart = array();
            //     foreach ($trans_disc_cart as $disc_id => $dc) {
            //         $dit = "";
            //         foreach ($dc['items'] as $lines) {
            //             $dit .= $lines.",";
            //         }
            //         if($dit != "")
            //             $dit = substr($dit,0,-1);
            //         $trans_sales_disc_cart[] = array(
            //             "sales_id"=>$sales_id,
            //             "disc_id"=>$disc_id,
            //             "disc_code"=>$dc['disc_code'],
            //             "disc_rate"=>$dc['disc_rate'],
            //             "type"=>$dc['disc_type'],
            //             "name"=>$dc['name'],
            //             "bday"=>date2Sql($dc['bday']),
            //             "code"=>$dc['code'],
            //             // "items"=>$dit,
            //             "guest"=>$dc['guest']
            //         );
            //     }
            //     if(count($trans_sales_disc_cart) > 0)
            //         $this->cashier_model->add_trans_sales_discounts($trans_sales_disc_cart);
            // }
                //------------------------------------------------STARTS HERE
            if(count($trans_disc_cart) > 0)
            {
                $trans_sales_disc_cart = array();
                $total = 0;
                foreach ($trans_cart as $trans_id => $trans){
                    if(isset($trans['cost']))
                        $cost = $trans['cost'];
                    if(isset($trans['price']))
                        $cost = $trans['price'];

                    if(isset($trans['modifiers'])){
                        foreach ($trans['modifiers'] as $trans_mod_id => $mod) {
                            if($trans_id == $mod['line_id'])
                                $cost += $mod['price'];
                        }
                    }

                    else{
                        if(count($trans_mod_cart) > 0){
                            foreach ($trans_mod_cart as $trans_mod_id => $mod) {
                                if($trans_id == $mod['trans_id'])
                                    $cost += $mod['cost'];
                            }
                        }
                    }
                    if(isset($counter['zero_rated']) && $counter['zero_rated'] == 1){
                        $rate = 1.12;
                        $cost = ($cost / $rate);
                        $zero_rated += $v['qty'] * $cost;
                    }
                    $total += $trans['qty'] * $cost;
                }

                foreach ($trans_disc_cart as $disc_id => $dc) {
                    $dit = "";
                    if(isset($dc['items'])){
                        foreach ($dc['items'] as $lines) {
                            $dit .= $lines.",";
                        }
                        if($dit != "")
                            $dit = substr($dit,0,-1);
                    }



                    $maxes = $this->get_max_price_and_qty_for_disc($dc['guest'],$trans_cart);

                    $highest_disc = 0;
                    foreach($maxes as $key => $val)
                        $highest_disc +=($val['cost']*$val['qty']);
                
                    $discount = 0;
                    $rate = $dc['disc_rate'];

                    switch ($dc['disc_type']) {
                        case "equal":
                            $divi = $total/$dc['guest'];
                            if($dc['no_tax'] == 1)
                                $divi = ($divi / 1.12);
                            $no_persons = count($dc['persons']);
                            $discs[] = array('type'=>$dc['disc_code'],'amount'=>($rate / 100) * $divi);
                            $discount = ($rate / 100) * $divi;
                            break;
                        case "highest":
                            $discount = ($rate / 100) * $highest_disc;
                            $discs[] = array('type'=>$dc['disc_code'],'amount'=>$discount);
                            break;
                        default:
                            $no_citizens = count($dc['persons']);
                            if($dc['no_tax'] == 1)
                                $total = ($total / 1.12);
                            $discs[] = array('type'=>$dc['disc_code'],'amount'=>($rate / 100) * $total);
                            $discount = ($rate / 100) * $total;
                            // }
                    }

                    foreach ($dc['persons'] as $pcode => $oper) {
                        $dcBday = null;
                        if(isset($oper['bday']) && $oper['bday'] != "")
                            $dcBday = date2Sql($oper['bday']);
                        $trans_sales_disc_cart[] = array(
                            "sales_id"=>$sales_id,
                            "disc_id"=>$dc['disc_id'],
                            "disc_code"=>$dc['disc_code'],
                            "disc_rate"=>$dc['disc_rate'],
                            "no_tax"=>$dc['no_tax'],
                            "type"=>$dc['disc_type'],
                            "name"=>$oper['name'],
                            "bday"=>$dcBday,
                            "code"=>$oper['code'],
                            "items"=>$dit,
                            "guest"=>$dc['guest'],
                            "amount"=>$discount/count($dc['persons']),
                        );
                    }
                }

                if(count($trans_sales_disc_cart) > 0)
                    $this->cashier_model->add_trans_sales_discounts($trans_sales_disc_cart);
            }

            //--------------------------------------------------------------------------------------END HERE
        
            if(!empty($trans_vip_disc_cart)){
                $vip = $trans_vip_disc_cart['vip'];
                // print_r($vip);

                    $trans_sales_vip_disc_cart[] = array(
                        "sales_id"=>$sales_id,
                        "disc_rate"=>$vip['disc_rate'],
                        "type"=>$vip['disc_type'],
                        "is_absolute"=>$vip['is_absolute'],
                    );
                // print_r($trans_sales_vip_disc_cart);
                if(count($trans_sales_vip_disc_cart) > 0)
                    $this->cashier_model->add_trans_sales_discounts($trans_sales_vip_disc_cart);
            }



            #save sa trans_sales_charges
            if(count($trans_charge_cart) > 0){
                $trans_sales_charge_cart = array();
                foreach ($trans_charge_cart as $charge_id => $ch) {
                    $trans_sales_charge_cart[] = array(
                        "sales_id"=>$sales_id,
                        "charge_id"=>$charge_id,
                        "charge_code"=>$ch['code'],
                        "charge_name"=>$ch['name'],
                        "rate"=>$ch['amount'],
                        "absolute"=>$ch['absolute']
                    );
                }
                if(count($trans_sales_charge_cart) > 0)
                    $this->cashier_model->add_trans_sales_charges($trans_sales_charge_cart);
            }
            #SAVE SA TRANS_SALES_TAX
            // $total_amount
            $tax = $this->get_tax_rates(false);
            if(count($tax) > 0){
                $taxable_amount = 0;
                $not_taxable_amount = 0;
                foreach ($trans_cart as $trans_id => $v) {
                    $cost = $v['cost'];
                    if(count($trans_mod_cart) > 0){
                        foreach ($trans_mod_cart as $trans_mod_id => $m) {
                            if($trans_id == $m['trans_id']){
                                $cost += $m['cost'];
                            }
                        }
                    }

                    foreach ($trans_disc_cart as $disc_id => $row) {
                        $rate = $row['disc_rate'];
                        switch ($row['disc_type']) {
                            case "item":
                                    if( in_array($trans_id, $row['items'])){
                                        $discount = ($rate / 100) * $cost;
                                        $cost -= $discount;
                                    }
                                    break;
                            case "equal":
                                    $divi = $cost/$row['guest'];
                                    $discount = ($rate / 100) * $divi;
                                    $cost -= $discount;
                                    break;
                            default:
                                $discount = ($rate / 100) * $cost;
                                $cost -= $discount;
                        }
                    }

                    if($v['no_tax'] == 0){
                        $taxable_amount += $cost * $v['qty'];
                    }
                    else{
                        $not_taxable_amount += $cost * $v['cost'];
                    }
                }

                $trans_sales_no_tax[] = array(
                    "sales_id"=>$sales_id,
                    "amount"=>$not_taxable_amount
                );

                if(count($trans_sales_no_tax) > 0)
                    $this->cashier_model->add_trans_sales_no_tax($trans_sales_no_tax);
                $am = $taxable_amount;
                $trans_sales_tax = array();

                foreach ($tax as $tax_id => $tx) {
                    $rate = ($tx['rate'] / 100);
                    $tax_value = ($am / ($rate + 1) ) * $rate;
                    // ($am / 1.12) * .12
                    $trans_sales_tax[] = array(
                        "sales_id"=>$sales_id,
                        "name"=>$tx['name'],
                        "rate"=>$tx['rate'],
                        "amount"=>$tax_value,
                    );
                    $am -= $tax_value;
                }
                if(count($trans_sales_tax) > 0)
                    $this->cashier_model->add_trans_sales_tax($trans_sales_tax);
            }
            #print
            if ($print == "true" || $print === true)
                $print_echo = $this->print_sales_receipt($sales_id,false);
        
            $trans_prep_time_sales = $this->cashier_model->get_preparation_time($sales_id); 


            $this->cashier_model->update_trans_sales(array('prep_time'=>$trans_prep_time_sales->trans_prep_time_sq),$sales_id);

        }
        if($asJson)
            echo json_encode(array('error'=>$error,'act'=>$act,'id'=>$sales_id,'type'=>$type));
        else
            return array('error'=>$error,'act'=>$act,'id'=>$sales_id,'type'=>$type);
    }

    //  public function finish_trans($sales_id=null,$move=false,$void=false){
    //     $this->load->model('dine/cashier_model');
    //     $this->load->model('dine/items_model');
    //     $this->load->model('core/ref_model');
     
    //     $trans_type = CALL_CENTER_TRANS;
        
    //     $ref = $this->ref_model->get_next_ref($trans_type);
    //     $this->ref_model->db->trans_start();
    //         $this->ref_model->save_ref($trans_type,$ref);
    //         $this->cashier_model->update_trans_sales(array('trans_ref'=>$ref,'paid'=>1),$sales_id);
    //     $this->ref_model->db->trans_complete();
    // }

     #PAYMENTMETHOD
    public function payment_method($sales_id=null){
        $this->load->model('site/site_model');
        $this->load->model('dine/cashier_model');
        $this->load->model('dine/settings_model');
        $this->load->helper('dine/cashier_helper');
        $this->load->helper('core/on_screen_key_helper');

        $data = $this->syter->spawn(null);
        $order = $this->get_order(false,$sales_id);

        $totals = $this->total_trans(false,$order['details'],$order['discounts'],$order['charges']);

        $data['code'] = paymentMethodPage($order['order'],$order['details'],$order['discounts'],$totals,$order['charges']);
        $data['add_css'] = array('css/cashier.css','css/onscrkeys.css');
        $data['add_js'] = array('js/on_screen_keys.js');

        $data['load_js'] = 'dine/cashier.php';
        $data['use_js'] = 'paymentJs';
        // $data['noNavbar'] = true;
        $this->load->view('cashier',$data);
    }
    #SETTLEMENT
    public function settle($sales_id=null){
        $this->load->model('site/site_model');
        $this->load->model('dine/cashier_model');
        $this->load->model('dine/settings_model');
        $this->load->helper('dine/cashier_helper');
        $this->load->helper('core/on_screen_key_helper');
        $data = $this->syter->spawn(null);
        $order = $this->get_order(false,$sales_id);
        // $discounts = $this->settings_model->get_receipt_discounts();
        $totals = $this->total_trans(false,$order['details'],$order['discounts'],$order['charges']);

        $data['code'] = settlePage($order['order'],$order['details'],$order['discounts'],$totals,$order['charges']);
        $data['add_css'] = array('css/cashier.css','css/onscrkeys.css');
        $data['add_js'] = array('js/on_screen_keys.js');

        $data['load_js'] = 'dine/cashier.php';
        $data['use_js'] = 'settleJs';
        // $data['noNavbar'] = true;
        $this->load->view('cashier',$data);
    }
    public function get_order_payments($asJson=true,$sales_id=null,$payment_id=null){
        $this->load->model('dine/cashier_model');
        $payments = $this->cashier_model->get_trans_sales_payments($payment_id,array('trans_sales_payments.sales_id'=>$sales_id));
        $pays = array();
        foreach ($payments as $res) {
            $pays[$res->payment_id] = array(
                "sales_id"=>$res->sales_id,
                "type"=>$res->payment_type,
                "amount"=>$res->amount,
                "reference"=>$res->reference,
                "datetime"=>$res->datetime,
                "user_id"=>$res->user_id,
                "username"=>$res->username,
                "to_pay"=>$res->to_pay,
                "card_type"=>$res->card_type
            );
        }
        if($asJson)
            echo json_encode($pays);
        else
            return $pays;
    }
    public function set_payment($sales_id=null,$amount=null,$type=null){
        $this->load->model('dine/cashier_model');
        $order = $this->get_order_header(false,$sales_id);
        $error = "";
        $payments = $this->get_order_payments(false,$sales_id);
        $total_to_pay = $order['amount'];
        $paid = $order['paid'];
        $total_paid = 0;
        $balance = $order['balance'];
        // $delivery_code = $order['delivery_code'];

        if(count($payments) > 0){
            foreach ($payments as $pay_id => $pay) {
                $total_paid += $pay['amount'];
            }
        }
        if($total_to_pay >= $total_paid)
            $total_to_pay -= $total_paid;
        else
            $total_to_pay = 0;
        $change = 0;
        if($total_to_pay > 0){
            $payment = array(
                'sales_id'      =>  $sales_id,
                'payment_type'  =>  $type,
                'amount'        =>  $amount,
                'to_pay'        =>  $total_to_pay,
                "user_id"       =>  1,
                // 'reference'     =>  null,
                // 'card_type'     =>  null
            );


            if ($type=="credit") {
                $payment['card_type'] = $this->input->post('card_type');
                $payment['card_number'] = $this->input->post('card_number');
                $payment['approval_code'] = $this->input->post('approval_code');
            } elseif ($type=="debit") {
                $payment['card_number'] = $this->input->post('card_number');
                $payment['approval_code'] = $this->input->post('approval_code');
            } elseif ($type=="gc") {
                $this->load->model('dine/gift_cards_model');
                $gc_id = $this->input->post('gc_id');
                $gc_code = $this->input->post('gc_code');

                $result = $this->gift_cards_model->get_gift_cards($gc_id,false);

                if (empty($result)) {
                    echo json_encode(array('error'=>'Gift card is invalid'));
                    return false;
                }

                $this->gift_cards_model->update_gift_cards(array('inactive'=>1),$gc_id);
                $payment['reference'] = $gc_code;
                $payment['amount'] = $result[0]->amount;
                $amount = $result[0]->amount;
            }

            if ($type == 'cash') {
                if($amount > $total_to_pay){
                    $payment['change'] = $change = $amount - $total_to_pay;

                }
            }

            $this->cashier_model->delete_trans_payment_method($sales_id);

            $payment_id = $this->cashier_model->add_trans_payment_method($payment);
            $new_total_paid = 0;
            if($amount > $total_to_pay){
                $new_total_paid = $order['amount'];
                $balance = 0;
            }else{
                $new_total_paid = $total_paid+$amount;
                // $balance = $total_to_pay - $amount;
                $balance = $balance - $amount;
            }

            // $this->cashier_model->update_trans_sales(array('total_paid'=>$new_total_paid),$sales_id);
            // if ($balance == 0) {
            //     $this->finish_trans($sales_id,true);
            // }
           
           
        }
        else{
            $error = 'Amount Received.';
        }
        echo json_encode(array('error'=>$error,'type'=>$type, 'change'=>$change,'tendered'=>$amount,'balance'=>$balance, 'delivery_code'=>$order['delivery_code']));
    }


    public function pop_new_address(){
        $this->load->helper('dine/cashier_helper');
        $data['code'] = pop_new_address_form();
        // $data['add_js'] = array('js/plugins/typeaheadmap/typeaheadmap.js');
        // $data['add_css'] = array('js/plugins/typeaheadmap/typeaheadmap.css');
        $data['load_js'] = 'dine/cashier.php';
        $data['use_js'] = 'newAddressJs';
        $this->load->view('load',$data);
    }

    public function city_search()
    {
        $search = $this->input->post('search');
        $this->load->model('dine/cashier_model');
        $found = $this->cashier_model->get_city_search($search);
        $searched = array();
        if(count($found) > 0 ){
            foreach ($found as $res) {
                $searched[] = array('key'=>$res->municipality,'value'=>$res->province, 'id'=>$res->id);
            }
        }
        echo json_encode($searched);
    }

    public function pop_new_address_db(){

        $items = array(
            'street_no' => $this->input->post('street_no'),
            'street_address' => $this->input->post('street_address'),
            'zip' => $this->input->post('zip'),
            'region' => $this->input->post('region'),
            'city' => $this->input->post('city'),
            'landmark' => $this->input->post('landmark'),
        );

        $this->load->model('dine/customers_model');
        $id = $this->customers_model->add_customer_address($items);
        $msg = "Added Address: ". ucwords($items['street_address']).", ".ucwords($items['city']) .", ".ucwords($items['region']);

        echo json_encode(array('msg'=>$msg, 'items'=>$items));
    }

    public function new_cust_address_row(){
       
        $items = array(
            'street_no' => $this->input->post('street_no'),
            'street_address' => $this->input->post('street_address'),
            'zip' => $this->input->post('zip'),
            'region' => $this->input->post('region'),
            'city' => $this->input->post('city'),
            'landmark' => $this->input->post('landmark'),
        );

        $this->make->sRow(array('class'=>'t-rows'));
            $this->make->td(ucwords($this->input->post('street_no')));
            $this->make->td(ucwords($this->input->post('street_address')));
            $this->make->td(ucwords($this->input->post('city')));
            $this->make->td(ucwords($this->input->post('region')));
            $this->make->td(ucwords($this->input->post('zip')));
            $this->make->td(ucwords($this->input->post('landmark')));
        $this->make->eRow();
    
        $code = $this->make->code();
        
        echo json_encode(array('code'=>$code, 'items'=>$items));
    }

    public function get_cust_addr_db($cust_id)
    {

        $this->load->model('dine/customers_model');
        $arr_td = array('street_no', 'street_address','city' , 'region', 'zip' , 'landmark');
        $address = $this->input->post('address');
        
        $count = 0;
        $args = array();

        if(!empty($address))        
            foreach($address as $addr)
            {
                $args[$arr_td[$count]] = $addr;
                $count++;
            }
       
        $result = $this->customers_model->get_customer_address_search($cust_id, $args, null);
        echo json_encode(array('id'=>$result->id));
            
    }

    public function add_payment($sales_id=null,$amount=null,$type=null){
        $this->load->model('dine/cashier_model');
        $order = $this->get_order_header(false,$sales_id);
        $error = "";
        $payments = $this->get_order_payments(false,$sales_id);
        $total_to_pay = $order['amount'];
        $paid = $order['paid'];
        $total_paid = 0;
        $balance = $order['balance'];
        if(count($payments) > 0){
            foreach ($payments as $pay_id => $pay) {
                $total_paid += $pay['amount'];
            }
        }
        if($total_to_pay >= $total_paid)
            $total_to_pay -= $total_paid;
        else
            $total_to_pay = 0;
        $change = 0;
        if($total_to_pay > 0){
            $payment = array(
                'sales_id'      =>  $sales_id,
                'payment_type'  =>  $type,
                'amount'        =>  $amount,
                'to_pay'        =>  $total_to_pay,
                "user_id"       =>  1,
                // 'reference'     =>  null,
                // 'card_type'     =>  null
            );


            if ($type=="credit") {
                $payment['card_type'] = $this->input->post('card_type');
                $payment['card_number'] = $this->input->post('card_number');
                $payment['approval_code'] = $this->input->post('approval_code');
            } elseif ($type=="debit") {
                $payment['card_number'] = $this->input->post('card_number');
                $payment['approval_code'] = $this->input->post('approval_code');
            } elseif ($type=="gc") {
                $this->load->model('dine/gift_cards_model');
                $gc_id = $this->input->post('gc_id');
                $gc_code = $this->input->post('gc_code');

                $result = $this->gift_cards_model->get_gift_cards($gc_id,false);

                if (empty($result)) {
                    echo json_encode(array('error'=>'Gift card is invalid'));
                    return false;
                }

                $this->gift_cards_model->update_gift_cards(array('inactive'=>1),$gc_id);
                $payment['reference'] = $gc_code;
                $payment['amount'] = $result[0]->amount;
                $amount = $result[0]->amount;
            }


            $payment_id = $this->cashier_model->add_trans_sales_payments($payment);
            $new_total_paid = 0;
            if($amount > $total_to_pay){
                $new_total_paid = $order['amount'];
                $balance = 0;
            }
            else{
                $new_total_paid = $total_paid+$amount;
                // $balance = $total_to_pay - $amount;
                $balance = $balance - $amount;
            }

            $this->cashier_model->update_trans_sales(array('total_paid'=>$new_total_paid),$sales_id);
            if ($balance == 0) {
            //     // if ($paid == 0) {
                    $this->finish_trans($sales_id,true);
            //     // }
            }
            // if($paid == 0){
            //     $move = true;
            // }
            // else
            //     $move = false;
            // if(in_array($type, array([0]=>'cash'))){
            if ($type == 'cash') {
                if($amount > $total_to_pay){
                    $change = $amount - $total_to_pay;
                }
            }
        }
        else{
            $error = 'Amount Received.';
        }
        echo json_encode(array('error'=>$error,'change'=>$change,'tendered'=>$amount,'balance'=>$balance));
    }
    public function delete_payment($payment_id=null,$sales_id=null){
        $this->load->model('dine/cashier_model');
        $this->cashier_model->delete_trans_sales_payments($payment_id);
        $payment = $this->get_order_payments(false,$sales_id);
        $order = $this->get_order_header(false,$sales_id);
        $error = "";
        $balance = 0;
        $total_paid = 0;
        foreach ($payment as $payment_id => $pay) {
            $total_paid += $pay['amount'];
        }
        $this->cashier_model->update_trans_sales(array('total_paid'=>$total_paid),$sales_id);
        echo json_encode(array('error'=>$error,'balance'=>$order['amount'] - $total_paid));
    }
    public function finish_trans($sales_id=null,$move=false,$void=false){
        $this->load->model('dine/cashier_model');
        $this->load->model('dine/items_model');
        $this->load->model('core/ref_model');
        $loc_id = 2;
        $trans_type = CALL_CENTER_TRANS;
        if($void)
            $trans_type = SALES_VOID_TRANS;
        $ref = $this->ref_model->get_next_ref($trans_type);
            $this->ref_model->db->trans_start();
            $this->ref_model->save_ref($trans_type,$ref);
            $this->cashier_model->update_trans_sales(array('trans_ref'=>$ref,'paid'=>1),$sales_id);
            if($move || $move == "true"){
                $opts = array(
                    "type_id" => $trans_type,
                    "trans_id" => $sales_id,
                    "trans_ref" => $ref,
                );
                if($void)
                    $rrr = true;
                else
                    $rrr = false;
                $items = $this->order_items_used($sales_id,$rrr);
                $this->items_model->move_items($loc_id,$items,$opts);
            }
        $this->ref_model->db->trans_complete();
    }
    public function settle_transactions($sales_id=null){
        $payments = $this->get_order_payments(false,$sales_id);
        $this->make->sDiv(array('class'=>'pay-row-list','style'=>'padding:10px;'));
        $icons = array(
            "cash"=>'money',
            "credit"=>'credit-card',
            "debit"=>'credit-card',
            "gift"=>'gift',
            "check"=>'check-square-o',
        );
        $ids = array();
        foreach ($payments as $payment_id => $pay) {
            $this->make->sDiv(array('class'=>'pay-row-div bg-blue','id'=>'pay-row-div-'.$payment_id));
                $this->make->sDivRow();
                    $this->make->sDivCol(2,'left',0,array('style'=>'margin-right:20px;'));
                        $this->make->H(3,fa('fa-'.$icons[$pay['type']].' fa-3x fa-fw'),array('class'=>'headline'));
                    $this->make->eDivCol();
                    $this->make->sDivCol(2);
                        $this->make->H(5,strtoupper($pay['type']));
                        $this->make->H(5,strtoupper(sql2DateTime($pay['datetime'])));
                    $this->make->eDivCol();
                    $this->make->sDivCol(5);
                        $this->make->H(5,'Tendered: PHP '.strtoupper(num($pay['amount'])));
                        $change = 0;
                        if($pay['amount'] > $pay['to_pay'])
                            $change = $pay['amount'] - $pay['to_pay'];
                        $this->make->H(5,'Change:   PHP '.strtoupper(num($change)));
                        $this->make->H(5,strtoupper($pay['username']));
                    $this->make->eDivCol();
                    $this->make->sDivCol(2,'right',0,array('style'=>'margin-top:10px;'));
                        $this->make->button(fa('fa-ban fa-lg fa-fw').' VOID',array('id'=>'void-payment-btn-'.$payment_id,'ref'=>$payment_id,'class'=>'btn-block settle-btn-red double'));
                    $this->make->eDivCol();
                $this->make->eDivRow();
            $this->make->eDiv();
            $ids[] = $payment_id;
        }
        $this->make->eDiv();
        $code = $this->make->code();
        echo json_encode(array('code'=>$code,'ids'=>$ids));
    }
	public function manager_settle_transactions($sales_id=null){
        // echo "Sales ID : ".$sales_id."<br>";
		$payments = $this->get_order_payments(false,$sales_id);
		// echo $this->db->last_query();
        $this->make->sDiv(array('class'=>'pay-row-list','style'=>'padding:10px; background-color:#0073b7;'));
        $icons = array(
            "cash"=>'money',
            "credit"=>'credit-card',
            "debit"=>'credit-card',
            "gift"=>'gift',
            "check"=>'check-square-o',
        );
        $ids = array();
        foreach ($payments as $payment_id => $pay) {
            $this->make->sDiv(array('class'=>'pay-row-div bg-blue','id'=>'pay-row-div-'.$payment_id));
                $this->make->sDivRow(array('class'=>'bg-blue'));
                    $this->make->sDivCol(2,'left',0,array('style'=>'margin-right:20px;', 'class'=>'bg-blue'));
                        $this->make->H(3,fa('fa-'.$icons[$pay['type']].' fa-3x fa-fw'),array('class'=>'headline'));
                    $this->make->eDivCol();
                    $this->make->sDivCol(2,'',0,array('class'=>'bg-blue'));
                        // $this->make->H(5,'Sales ID #'.$sales_id); // !!!
                        $this->make->H(5,strtoupper($pay['type']));
                        $this->make->H(5,strtoupper(sql2DateTime($pay['datetime'])));
                    $this->make->eDivCol();
                    $this->make->sDivCol(5,'',0,array('class'=>'bg-blue'));
                        $this->make->H(5,'Tendered: PHP '.strtoupper(num($pay['amount'])));
                        $change = 0;
                        if($pay['amount'] > $pay['to_pay'])
                            $change = $pay['amount'] - $pay['to_pay'];
                        $this->make->H(5,'Change:   PHP '.strtoupper(num($change)));
                        $this->make->H(5,strtoupper($pay['username']));
                    $this->make->eDivCol();
                    // $this->make->sDivCol(2,'right',0,array('style'=>'margin-top:10px;'));
                        // $this->make->button(fa('fa-ban fa-lg fa-fw').' VOID',array('id'=>'void-payment-btn-'.$payment_id,'ref'=>$payment_id,'class'=>'btn-block settle-btn-red double'));
                    // $this->make->eDivCol();
					$this->make->sDivCol(2,'right',0,array('style'=>'margin-top:10px;', 'class'=>'bg-blue'));
                       $this->make->H(5,"Sales ID #".$sales_id);
                    $this->make->eDivCol();
                $this->make->eDivRow();
            $this->make->eDiv();
            $ids[] = $payment_id;
        }
        $this->make->eDiv();
        $code = $this->make->code();
        echo json_encode(array('code'=>$code,'ids'=>$ids));
    }
    public function order_items_used($sales_id=null,$add=false){
        $this->load->model('dine/cashier_model');
        $this->load->model('dine/menu_model');
        $this->load->model('dine/mods_model');
        $this->load->model('dine/items_model');
        $order = $this->get_order(false,$sales_id);
        $details = $order['details'];
        $menus = array();
        $mods = array();
        foreach ($details as $det) {
            $menus[] = $det['menu_id'];
            if(count($det['modifiers']) > 0){
                foreach ($det['modifiers'] as $mod_id => $mod) {
                    $mods[] = $mod['id'];
                }
            }
        }
        $menu_recipe = $this->menu_model->get_recipe_items($menus);
        $me = array();
        foreach ($menu_recipe as $mn) {
            $me[$mn->menu_id][$mn->item_id] = array('item_uom'=>$mn->uom,'item_qty'=>$mn->qty);
        }
        $mods_recipe = $this->mods_model->get_modifier_recipe(null,$mods);
        $mo = array();
        foreach ($mods_recipe as $mn) {
            $mo[$mn->mod_id][$mn->item_id] = array('item_uom'=>$mn->uom,'item_qty'=>$mn->qty);
        }
        $items = array();
        foreach ($details as $line_id => $det) {
            $mul = $det['qty'];
            if(isset($me[$det['menu_id']])){
               foreach ($me[$det['menu_id']] as $item_id => $opt) {
                   if(isset($items[$item_id])){
                        if($add)
                            $items[$item_id]['qty'] += ($mul * $opt['item_qty']);
                        else
                            $items[$item_id]['qty'] += (($mul * $opt['item_qty']) * -1);

                        $items[$item_id]['uom'] = $opt['item_uom'];
                   }
                   else{
                        if($add)
                            $items[$item_id]['qty'] = ($mul * $opt['item_qty']);
                        else
                            $items[$item_id]['qty'] = (($mul * $opt['item_qty']) * -1);
                        $items[$item_id]['uom'] = $opt['item_uom'];
                   }

               }
            }
            #
            if(count($det['modifiers']) > 0){
                foreach ($det['modifiers'] as $mod_id => $mod) {
                    if(isset($mo[$mod['id']])){
                        foreach ($mo[$mod['id']] as $mod_item_id => $mopt) {
                           if(isset($items[$mod_item_id])){
                                if($add)
                                    $items[$mod_item_id]['qty'] += ($mul * $mopt['item_qty']);
                                else
                                    $items[$mod_item_id]['qty'] += (($mul * $mopt['item_qty']) * -1);
                                $items[$mod_item_id]['uom'] = $mopt['item_uom'];
                           }
                           else{
                                if($add)
                                    $items[$mod_item_id]['qty'] = ($mul * $mopt['item_qty']);
                                else
                                    $items[$mod_item_id]['qty'] = (($mul * $mopt['item_qty']) * -1);
                                $items[$mod_item_id]['uom'] = $mopt['item_uom'];
                           }
                       }
                    }
                    #
                }
            }
            #
        }
        #
        return $items;
    }
    public function print_sales_receipt($sales_id=null,$asJson=true,$return_print_str=false,$add_reprinted=true){
        // // Load PHPRtfLite Class
        // require_once APPPATH."/third_party/PHPRtfLite.php";

        /*
         * -----------------------------------------------------------
         *      Start of Receipt Printing
         * -----------------------------------------------------------
        */
        $branch = $this->get_branch_details(false);
        $return = $this->get_order(false,$sales_id);
        $order = $return['order'];
        $details = $return['details'];
        $payments = $return['payments'];
        $discounts = $return['discounts'];
        $charges = $return['charges'];
        $tax = $return['taxes'];
        $no_tax = $return['no_tax'];

        $print_str = "\r\n"

            .$this->align_center($branch['name'],46," ")."\r\n"
            // .$this->align_center($branch['desc'],46," ")."\r\n"
            .$this->align_center($branch['address'],46," ")."\r\n"
            .$this->align_center('TIN # '.$branch['tin'],46," ")."\r\n"
            .$this->align_center('BIR # '.$branch['bir'],46," ")."\r\n"
            .$this->align_center('MACHINE # '.$branch['machine_no'],46," ")."\r\n"
            .$this->align_center('SN #'.$branch['serial'],46," ")."\r\n"
            .$this->align_center('PERMIT #'.$branch['permit_no'],46," ")."\r\n"
            ."============================================="."\r\n";


        if (!empty($payments)){
            $print_str .= $this->align_center(ucwords($order['type'])." OR# ".$order['ref'],46," ")."\r\n";
            // $print_str .= $this->align_center(date('Y-m-d D H:i:s',strtotime($order['datetime']))." ".$order['terminal_name']." ".$order['name'],46," ")."\r\n";
        }
        else{
            $print_str .= $this->align_center(ucwords($order['type'])." # ".$order['sales_id'],46," ")."\r\n";
        }

        $orddetails = "";
        if($order['table_id'] != "" || $order['table_id'] != 0)
            $orddetails .= $order['table_name']." ";

        if($order['guest'] != 0)
            $orddetails .= "Guest #".$order['guest'];

        if($orddetails != "")
            $print_str .= $this->align_center($orddetails,46," ")."\r\n";
        $print_str .= $this->align_center(date('Y-m-d D H:i:s',strtotime($order['datetime']))." ".$order['terminal_name']." ".$order['name'],46," ")."\r\n";
        
        if (!empty($payments)) {
            if($add_reprinted){
                if($order['printed'] == 1){        
                    $print_str .= $this->align_center('[REPRINTED]',46," ")."\r\n";
                }
                else{
                    $this->cashier_model->update_trans_sales(array('printed'=>1),$order['sales_id']);
                }            
            }
            else{
                $this->cashier_model->update_trans_sales(array('printed'=>1),$order['sales_id']);
            }            



        }

        $print_str .= "============================================="."\r\n\r\n";


        foreach ($details as $val) {
            $print_str .= $this->append_chars(number_format($val['qty']),"right",6," ");

            if ($val['qty'] == 1) {
                $print_str .= $this->append_chars($val['name'],"right",30," ").
                    $this->append_chars(number_format($val['price'],2)."V","left",9," ")."\r\n";
            } else {
                $print_str .= $this->append_chars($val['name']." @ ".$val['price'],"right",30," ");
                    $this->append_chars(number_format($val['price'] * $val['qty'],2)."V","left",9," ")."\r\n";
            }

            if (empty($val['modifiers']))
                continue;

            $modifs = $val['modifiers'];
            foreach ($modifs as $vv) {
                $print_str .= "      ".$this->append_chars(number_format($val['qty']),"right",5," ");

                if ($vv['qty'] == 1) {
                    $print_str .= $this->append_chars($vv['name'],"right",25," ").
                        $this->append_chars(number_format($vv['price'],2)."V","left",9," ")."\r\n";
                } else {
                    $print_str .=  $this->append_chars($vv['name']." @ ".$vv['price'],"right",25," ").
                        $this->append_chars(number_format($vv['price'] * $vv['qty'],2)."V","left",9," ")."\r\n";
                }
            }
        }
        // $vat = round($order['amount'] / (1 + BASE_TAX) * BASE_TAX,1);
        $vat = 0;
        if($tax > 0){
            foreach ($tax as $tx) {
               $vat += $tx['amount'];
            }
        }
        $no_tax_amt = 0;
        foreach ($no_tax as $k=>$v) {
            $no_tax_amt += $v['amount'];
        }

        $totalsss = $this->total_trans(false,$details,$discounts);

        // $print_str .= "\r\n".$this->append_chars(ucwords($order['type']),"right",35," ").$this->append_chars("P".number_format(($totalsss['total']+$totalsss['discount']),2),"left",10," ")."\r\n";
        $print_str .= "\r\n".$this->append_chars(ucwords("Total"),"right",35," ").$this->append_chars("P ".number_format(($order['amount']),2),"left",10," ")."\r";
        $discs = $totalsss['discs'];
        if($discs > 0){
            foreach ($discs as $ds) {
                $print_str .= "\r\n".$this->append_chars(strtoupper($ds['type']),"right",35," ").$this->append_chars("(P ".number_format($ds['amount'],2).")","left",10," ")."\r\n";
            }
        }

        $print_str .= "\r\n".$this->append_chars(ucwords("VAT SALES"),"right",35," ").$this->append_chars(number_format($order['amount'] - $vat,2),"left",10," ")."\r\n";
        $print_str .= $this->append_chars(ucwords("VAT EXEMPT SALES"),"right",35," ").$this->append_chars(number_format($no_tax_amt,2),"left",10," ")."\r\n";
        if($tax > 0){
            foreach ($tax as $tx) {
               $print_str .= $this->append_chars($tx['name']."(".$tx['rate']."%)","right",35," ").$this->append_chars(number_format($tx['amount'],2),"left",10," ")."\r\n";
            }
        }

        if(Count($charges) > 0){
            foreach ($charges as $charge_id => $opt) {
                $charge_amount = $opt['amount'];
                if($opt['absolute'] == 0){
                    $charge_amount = ($opt['amount'] / 100) * ($order['amount'] - $vat);
                }
                $print_str .= $this->append_chars($opt['name'],"right",35," ").$this->append_chars(number_format($charge_amount,2),"left",10," ")."\r\n";
            }

        }

        if (!empty($payments)) {

            $print_str .= "\r\n".$this->append_chars("Amount due","right",35," ").$this->append_chars("P ".number_format($order['amount'],2),"left",10," ")."\r\n";

            $pay_total = 0;
            foreach ($payments as $payment_id => $opt) {

                $print_str .= $this->append_chars(ucwords($opt['payment_type']),"right",35," ").$this->append_chars("P ".number_format($opt['amount'],2),"left",10," ")."\r\n";
                if (!empty($opt['reference'])) {
                    $print_str .= $this->append_chars("     Reference ".$opt['reference'],"right",45," ")."\r\n";
                }
                $pay_total += $opt['amount'];
            }

            $print_str .= $this->append_chars("Change","right",35," ").$this->append_chars("P ".number_format($pay_total - $order['amount'],2),"left",10," ")."\r\n";
            $print_str .= "\r\n"
            .$this->align_center("This serves as your official receipt.",46," ")."\r\n"
            .$this->align_center("Thank you and please come again.",46," ")."\r\n"
            .$this->align_center("For feedback, please call us at",46," ")."\r\n"

            .$this->align_center($branch['contact_no'],46," ")."\r\n"
            .$this->align_center("Email : ".$branch['email'],46," ")."\r\n"
            .$this->align_center(" Please visit us at ".$branch['website'],46," ")."\r\n\r\n";

        } else {
            $print_str .= "\r\n".$this->align_center("=============================================",45," ");
            $print_str .= "\r\n".$this->append_chars("Amount due","right",35," ").$this->append_chars("P ".number_format($order['amount'],2),"left",10," ")."\r\n";
            $print_str .= $this->align_center("=============================================",45," ");
        }

        if ($return_print_str) {
            return $print_str;
        }


        $filename = "sales.txt";
        $fp = fopen($filename, "w+");
        fwrite($fp,$print_str);
        fclose($fp);

        $batfile = "print.bat";
        $fh1 = fopen($batfile,'w+');
        $root = dirname(BASEPATH);

        fwrite($fh1, "NOTEPAD /P \"".realpath($root."/".$filename)."\"");
        fclose($fh1);
        session_write_close();
        // exec($filename);
        exec($batfile);
        session_start();
        unlink($filename);
        unlink($batfile);

        if ($asJson)
            echo json_encode(array('msg'=>'Receipt # '.(!empty($order['ref']) ? $order['ref'] : $sales_id).' has been printed'));
        else
            return array('msg'=>'Receipt # '.(!empty($order['ref']) ? $order['ref'] : $sales_id).' has been printed');
    }
    private function append_chars($string,$position = "right",$count = 0, $char = ""){
        $rep_count = $count - strlen($string);
        $append_string = "";
        for ($i=0; $i < $rep_count ; $i++) {
            $append_string .= $char;
        }
        if ($position == 'right')
            return $string.$append_string;
        else
            return $append_string.$string;
    }
    private function align_center($string,$count,$char = " "){
        $rep_count = $count - strlen($string);
        for ($i=0; $i < $rep_count; $i++) {
            if ($i % 2 == 0) {
                $string = $char.$string;
            } else {
                $string = $string.$char;
            }
        }
        return $string;
    }
	public function manager_view_orders($terminal='my',$status='open',$types='all',$now=null,$show='box'){
        $this->load->model('dine/cashier_model');
        $this->load->model('site/site_model');
        $args = array(
            "trans_sales.trans_ref"=>null,
            "trans_sales.terminal_id"=>1,
            "trans_sales.type_id"=>SALES_TRANS,
            "trans_sales.inactive"=>0,
        );
        if($terminal != 'my')
            unset($args["trans_sales.terminal_id"]);
        if($status != 'open'){
            unset($args["trans_sales.trans_ref"]);
            $args["trans_sales.trans_ref  IS NOT NULL"] = array('use'=>'where','val'=>null,'third'=>false);
        }
        if($types != 'all'){
            $args["trans_sales.type"] = $types;
        }
        $orders = $this->cashier_model->get_trans_sales(null,$args);
		// echo $this->cashier_model->db->last_query();
        $code = "";
        $ids = array();
        $time = date('m/d/Y H:i:s');
        $this->make->sDivRow();
        $ord=array();
        $combine_cart = sess('trans_combine_cart');
        foreach ($orders as $res) {
            $status = "open";
            if($res->trans_ref != "")
                $status = "settled";
            $ord[$res->sales_id] = array(
                "type"=>$res->type,
                "status"=>$status,
                "user_id"=>$res->user_id,
                "name"=>$res->username,
                "terminal_id"=>$res->terminal_id,
                "terminal_name"=>$res->terminal_name,
                "shift_id"=>$res->shift_id,
                "datetime"=>$res->datetime,
                "amount"=>$res->total_amount
            );
            if($show == "box"){
                $this->make->sDivCol(4,'left',0);
                    $this->make->sDiv(array('class'=>'order-btn','id'=>'order-btn-'.$res->sales_id,'ref'=>$res->sales_id));
                        if($res->trans_ref == null){
                            $this->make->sBox('default',array('class'=>'box-solid'));
                        }else{
                            $this->make->sBox('default',array('class'=>'box-solid bg-green'));
                        }
                            $this->make->sBoxBody();
                                $this->make->sDivRow();
                                    $this->make->sDivCol(6);
                                        $this->make->H(5,strtoupper($res->type)." #".$res->sales_id,array("style"=>'font-weight:700;'));
                                        if($res->trans_ref == null){
                                            $this->make->H(5,strtoupper($res->username),array("style"=>'color:#888'));
                                            $this->make->H(5,strtoupper($res->terminal_name),array("style"=>'color:#888'));
                                        }else{
                                            $this->make->H(5,strtoupper($res->username),array("style"=>'color:#fff'));
                                            $this->make->H(5,strtoupper($res->terminal_name),array("style"=>'color:#fff'));
                                        }
                                        $this->make->H(5,tagWord(strtoupper(ago($res->datetime,$time) ) ) );
                                    $this->make->eDivCol();
                                    $this->make->sDivCol(6);
                                        $this->make->H(4,'Order Total',array('class'=>'text-center'));
                                        $this->make->H(3,num($res->total_amount),array('class'=>'text-center'));
                                    $this->make->eDivCol();
                                $this->make->eDivRow();

                            $this->make->eBoxBody();
                        $this->make->eBox();
                    $this->make->eDiv();
                $this->make->eDivCol();
            }
            else if($show=='combineList'){
                $got = false;
                if(count($combine_cart) > 0){
                    foreach ($combine_cart as $key => $co) {
                        if($co['sales_id'] == $res->sales_id){
                            $got = true;
                            break;
                        }
                    }
                }
                if(!$got){
                    $this->make->sDivRow(array('class'=>'orders-list-div-btnish sel-row','id'=>'order-btnish-'.$res->sales_id, "ref"=>$res->sales_id, "type"=>$res->type));
                        $this->make->sDivCol(6);
                            $this->make->sDiv(array('style'=>'margin-left:10px;'));
                                $this->make->H(5,strtoupper($res->type)." #".$res->sales_id,array("style"=>'font-weight:700;'));
                                $this->make->H(5,strtoupper($res->username),array("style"=>'color:#888'));
                                $this->make->H(5,strtoupper($res->terminal_name),array("style"=>'color:#888'));
                            $this->make->eDiv();
                        $this->make->eDivCol();
                        $this->make->sDivCol(6);
                            $this->make->sDiv(array('style'=>'margin-left:10px;'));
								if($status != 'open')
									$this->make->H(4,'ORDER TOTAL',array('class'=>'text-center'));
								else
									$this->make->H(4,'BALANCE DUE',array('class'=>'text-center'));
                                // $this->make->H(3,num($res->total_amount),array('class'=>'text-center','style'=>'margin-top:10px;'));
                                $this->make->H(3,num($res->total_amount),array('class'=>'text-center'));
                            $this->make->eDiv();
                        $this->make->eDivCol();
                        // $this->make->sDivCol(4);
                            // $this->make->sDiv(array('class'=>'order-btn-right-container','style'=>'margin-left:10px;margin-right:10px;margin-top:15px;'));
                                // $this->make->button(fa('fa-angle-double-right fa-lg fa-fw'),array('id'=>'add-to-btn-'.$res->sales_id,'ref'=>$res->sales_id,'class'=>'add-btn-row btn-block counter-btn-green'));
                            // $this->make->eDiv();
                        // $this->make->eDivCol();
                    $this->make->eDivRow();
                }
            }
            $ids[] = $res->sales_id;
        }
        //}
        $this->make->eDivRow();
        $code = $this->make->code();
        echo json_encode(array('code'=>$code,'ids'=>$ord));
    }
    public function save_cust_address(){

        $this->make->sDivRow();
            $this->make->sDivCol(12);
                $this->make->p('Address will be saved for future reference.');
            $this->make->eDivCol();
        $this->make->eDivRow();

        $code = $this->make->code();
        echo $code;
    }
    public function vip_confirmation(){

        $this->make->sDivRow();
            $this->make->sDivCol(12);
                $this->make->p('Would you like to add VIP Discount?');
            $this->make->eDivCol();
        $this->make->eDivRow();

        $code = $this->make->code();
        echo $code;
    }
    public function check_vip_cart(){
        $vip_discount = sess('trans_vip_disc_cart');
        echo json_encode(array('count'=>count($vip_discount)));
    }
    public function print_xread(){
        $this->load->model('dine/clock_model');
        $this->load->model('site/site_model');

        $print_str = $this->show_xread(false);
        $filename = "xread.txt";
        $fp = fopen($filename, "w+");
        fwrite($fp,$print_str);
        fclose($fp);

        $batfile = "print_xread.bat";
        $fh1 = fopen($batfile,"w+");
        $root = dirname(BASEPATH);

        fwrite($fh1, "NOTEPAD /P \"".realpath($root."/".$filename)."\"");
        fclose($fh1);

        $user = $this->session->userdata('user');
        $user_id = $user['id'];
        $date = date('m/d/Y H:i:s');
        $get_shift = $this->clock_model->get_shift_id(date2Sql($date),$user_id);

        if (empty($get_shift)) {
            echo json_encode(array('error'=>'Current shift is invalid'));
            return false;
        }

        $userdata = $this->session->userdata('user');
        $user_id = $userdata['id'];
        $id = $this->cashier_model->add_read_details(
            array(
                'read_type' => X_READ,
                'read_date' => date('Y-m-d'),
                'user_id'   => $user_id,
            )
        );

        $shift_id = $get_shift[0]->shift_id;
        $this->clock_model->update_clockout(array('xread_id'=>$id),$shift_id);

        session_write_close();
        exec($batfile);
        session_start();
        unlink($filename);
        unlink($batfile);

        echo json_encode(array('msg'=>"X-Read successfully printed at ".date('Y-m-d H:i:s')));
    }
    public function show_xread($asJson=true){
        $this->load->model('dine/cashier_model');
        $result = $this->cashier_model->get_latest_read_date(X_READ);
        // $latest_date = date('Y-m-d',strtotime($result->maxi.' +1 day'));
        // $print_date = (date('Y-m-d') > $latest_date ? $latest_date : date('Y-m-d'));
        $print_date = date('Y-m-d');
        $userdata = $this->session->userdata('user');

        $shifts = $this->cashier_model->get_user_shifts(
            array(
                'DATE(shifts.check_in)'=>$print_date,
                'shifts.terminal_id'=>TERMINAL_ID,
                'users.id' => $userdata['id']
            )
        );

        $main_str = "";
        $userdata = $this->session->userdata('user');
        foreach ($shifts as $shft_v) {
            $orders = $this->cashier_model->get_trans_sales(
                null,
                array(
                    'DATE(datetime)'=>$print_date,
                    'shift_id'=>$shft_v->shift_id,
                    'trans_sales.terminal_id'=>TERMINAL_ID,
                    'trans_sales.user_id'=>$userdata['id'],
                    'trans_sales.type_id'=>SALES_TRANS,
                    'trans_sales.inactive'=>0),
                'asc');

            if (empty($orders))
                continue;

            $print_str = "\r\nX-READ FOR ".$print_date." at Terminal ".TERMINAL_ID."\r\n"
                .$this->append_chars("","right",46,"-")."\r\n\r\n";
            $total = 0;
            foreach ($orders as $val) {
                $print_str .= (!empty($val->trans_ref) ? "RECEIPT NO. ".$val->trans_ref."\r\n" : "")
                    .$this->print_sales_receipt($val->sales_id,false,true,false)."\r\n\r\n"
                    .$this->append_chars("","right",46,"-")."\r\n\r\n";
                $total += $val->total_amount;
            }

            $print_str = $print_str."\r\n\r\n\r\n"
                .$this->append_chars("","right",46,"-")."\r\n\r\n"
                ."X-READ DATA at Terminal ".TERMINAL_ID."\r\n"
                ."Cashier    : ".$shft_v->username."\r\n"
                ."Check in   : ".$shft_v->check_in."\r\n"
                ."Check out  : ".$shft_v->check_out."\r\n\r\n"
                ."Total Sales : P ".number_format($total,2)."\r\n"
                ."Cash Float  : P ".number_format($shft_v->cash_float,2)."\r\n\r\n"
                .$this->append_chars("","right",46,"-");

            $main_str .= $print_str;
        }

        if (empty($main_str)) {
            $main_str = $this->append_chars("","right",46,"-")."\r\n\r\n"
                ."X-READ (".$print_date."):\r\nNo transactions found for this date\r\n\r\n"
                .$this->append_chars("","right",46,"-");
        }

        if ($asJson)
            echo json_encode(array('txt'=>$main_str));
        else
            return $main_str;
    }
    public function print_zread(){
        $result = $this->cashier_model->get_latest_read_date(Z_READ);
        if (date('Y-m-d') == $result->maxi) {
            echo json_encode(array('error_msg'=>'Z-Read data for today exists. Duplicate read dates prohibited.'));
            return false;
        }


        $read = $this->show_zread(false);
        $print_str = $read['txt'];
        $total = $read['total'];
        $date = $read['date'];
        $filename = "xread.txt";
        $fp = fopen($filename, "w+");
        fwrite($fp,$print_str);
        fclose($fp);

        $batfile = "print_xread.bat";
        $fh1 = fopen($batfile,"w+");
        $root = dirname(BASEPATH);

        fwrite($fh1, "NOTEPAD /P \"".realpath($root."/".$filename)."\"");
        fclose($fh1);

        $userdata = $this->session->userdata('user');
        $user_id = $userdata['id'];
        $this->cashier_model->add_read_details(
            array(
                'read_date'   => $date,
                'read_type'   => Z_READ,
                'user_id'     => $user_id,
                'grand_total' => $total
            )
        );

        session_write_close();
        exec($batfile);
        session_start();
        unlink($filename);
        unlink($batfile);

        echo json_encode(array('msg'=>"Z-Read successfully printed at ".date('Y-m-d H:i:s')));
    }
    public function show_zread($asJson=true){
        $this->load->model('dine/cashier_model');
        $print_date = date('Y-m-d');
        $prev_day = date('Y-m-d',strtotime('-1 day'));

        $result = $this->cashier_model->get_latest_read_date(Z_READ);
        $prev_day_trans = $this->cashier_model->get_trans_sales(null,array('DATE(datetime)'=>$prev_day,'trans_sales.type_id'=>SALES_TRANS,'trans_sales.inactive'=>0));
        $latest_date = date('Y-m-d');
        if (!empty($result->maxi)) {
            $latest_date = date('Y-m-d',strtotime($result->maxi.' +1 day'));
        } elseif (empty($result->maxi) && !empty($prev_day_trans)) {
            $latest_date = $prev_day;
        }
        $print_date = (date('Y-m-d') > $latest_date ? $latest_date : date('Y-m-d'));

        $orders = $this->cashier_model->get_trans_sales(null,
            array(
                'DATE(datetime)'=>$print_date,'trans_sales.type_id'=>SALES_TRANS,'trans_sales.inactive'=>0),'asc');
        $print_str = "";
        $total = 0;
        foreach ($orders as $val) {
            $print_str .= $this->print_sales_receipt($val->sales_id,false,true)."\r\n\r\n"
                .$this->append_chars("","right",46,"-")."\r\n\r\n";
            $total += $val->total_amount;
        }

        $prev_sales = 0;
        if (!empty($result->maxi)) {
            $resultx = $this->cashier_model->get_read_details(Z_READ,$result->maxi);
            if (!empty($resultx[0]))
                $prev_sales = $resultx[0]->grand_total;
        }

        $print_str = $print_str.($print_str == "" ? "" : "\r\n")
            .$this->append_chars("","right",46,"-")."\r\n\r\n"
            ."Z-READ DATA (".$print_date.")\r\n"
            ."Prev Day Sales : P ".number_format($prev_sales,2)."\r\n"
            ."Total Sales    : P ".number_format($total,2)."\r\n"
            ."\r\n".$this->append_chars("","right",46,"-");

        if ($asJson)
            echo json_encode(array('txt'=>$print_str,'total'=>$total,'date'=>$print_date));
        else
            return array('txt'=>$print_str,'total'=>$total,'date'=>$print_date);
    }
	public function manager_print_all_receipts($terminal='my',$status='open',$types='all',$now=null,$show='box', $asJson=true){
        $this->load->model('dine/cashier_model');
        $this->load->model('site/site_model');
        $args = array(
            "trans_sales.trans_ref"=>null,
            "trans_sales.terminal_id"=>1,
            "trans_sales.type_id"=>SALES_TRANS,
            "trans_sales.inactive"=>0,
        );
        if($terminal != 'my')
            unset($args["trans_sales.terminal_id"]);
        if($status != 'open'){
            unset($args["trans_sales.trans_ref"]);
            $args["trans_sales.trans_ref  IS NOT NULL"] = array('use'=>'where','val'=>null,'third'=>false);
        }
        if($types != 'all'){
            $args["trans_sales.type"] = $types;
        }
        $orders = $this->cashier_model->get_trans_sales(null,$args);
        $code = "";
        $ids = array();
        $time = date('m/d/Y H:i:s');
        $ord=array();

		$print_str = "";

        foreach ($orders as $res) {
            $status = "open";
            if($res->trans_ref != "")
                $status = "settled";
            $ord[$res->sales_id] = array(
                "type"=>$res->type,
                "status"=>$status,
                "user_id"=>$res->user_id,
                "name"=>$res->username,
                "terminal_id"=>$res->terminal_id,
                "terminal_name"=>$res->terminal_name,
                "shift_id"=>$res->shift_id,
                "datetime"=>$res->datetime,
                "amount"=>$res->total_amount
            );

			$print_str .= "SALES ID ".$res->sales_id."\r\n"
                .$this->print_sales_receipt($res->sales_id,false,true)."\r\n"
                .$this->append_chars("","right",46,"-");

            $ids[] = $res->sales_id;
        }

		$filename = "all_sales.txt";
        $fp = fopen($filename, "w+");
        fwrite($fp,$print_str);
        fclose($fp);

        $batfile = "print.bat";
        $fh1 = fopen($batfile,'w+');
        $root = dirname(BASEPATH);

        fwrite($fh1, "NOTEPAD /P \"".realpath($root."/".$filename)."\"");
        fclose($fh1);
        session_write_close();
        exec($batfile);
        session_start();
        unlink($filename);
        unlink($batfile);

		 if ($asJson)
				echo json_encode(array('txt'=>'<pre>'.$print_str.'</pre>', 'msg'=>'Successfully printed all receipts'));
			else
				return $print_str;
    }
    public function add_reference_address($cust_id){
        $this->load->model('dine/customers_model');
        $found = $this->customers_model->get_customer($cust_id);

        $found = $found[0];

        $items = array(
            'street_no' =>$found->street_no,
            'cust_id' => $cust_id,
            'street_address' => $found->street_address,
            'zip' => $found->zip,
            'region' => $found->region,
            'city' => $found->city,
            'landmark' =>$found->landmark,
        );

        $this->load->model('dine/customers_model');
        $id = $this->cashier_model->add_reference_address($items);
        echo json_encode(array('id'=>$id));
    }

}   