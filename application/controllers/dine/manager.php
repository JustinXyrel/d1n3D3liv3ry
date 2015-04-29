<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Manager extends CI_Controller {
    #manager
    public function __construct()
    {
        parent::__construct();
        $this->load->model('site/site_model');
        $this->load->model('dine/manager_model');
        $this->load->model('dine/cashier_model');
        $this->load->helper('dine/manager_helper');
        $this->load->helper('core/on_screen_key_helper');
        $this->load->helper('core/string_helper');
    }
    public function _remap($method,$params=array())
    {
        if (!$this->session->userdata('manager_privs'))
            switch ($method) {
                case 'go_login':
                    $this->go_login();
                    break;

                default:
                    $this->manager_login();
                    break;
            }
        else
            return call_user_func_array(array($this,$method),$params);
    }
    function manager_login()
    {
        $data = $this->syter->spawn(null,false);
        $data['code'] = managerLoginPage();
        $data['add_css'] = array('css/pos.css','css/onscrkeys.css');
        $data['add_js'] = array('js/on_screen_keys.js');
        $data['load_js'] = 'dine/manager';
        $data['use_js'] = 'managerLoginJs';

        $this->load->view('login',$data);
    }
    function go_login() {
        $pin = $this->input->post('pin');
        $manager = $this->manager_model->get_manager_by_pin($pin);

        if (!isset($manager->id)) {
            echo json_encode(array('error_msg'=>'Invalid manager pin'));
        } else {
            $this->session->set_userdata('manager_privs',array('method'=>'page','id'=>$manager->id));
            echo json_encode(array('success_msg'=>'Go'));
        }

        // return false;
    }
    function go_logout(){
        $userdata = $this->session->userdata('manager_privs');
        if ($userdata['method'] == 'page')
            $this->session->unset_userdata('manager_privs');

        header('Location:'.base_url()."cashier");
        // redirect(base_url()."cashier",'refresh');
    }
    public function index(){
        $data = $this->syter->spawn(null);
        $data['code'] = managerPage();
        $data['add_css'] = 'css/cashier.css';
        $data['load_js'] = 'dine/manager.php';
        $data['use_js'] = 'managerJs';
        $data['noNavbar'] = true; /*Hides the navbar. Comment-out this line to display the navbar.*/
        $this->load->view('cashier',$data);
    }
	public function manager_settings(){
        $data = $this->syter->spawn(null);
        $data['code'] = managerSettingsPage();
        $data['add_css'] = array('css/pos.css','css/onscrkeys.css','css/virtual_keyboard.css', 'css/cashier.css');
		$data['add_js'] = array('js/on_screen_keys.js','js/jquery.keyboard.extension-navigation.min.js','js/jquery.keyboard.min.js');
        $data['load_js'] = 'dine/manager.php';
        $data['use_js'] = 'systemJS';
        $this->load->view('load',$data);
    }
	public function manager_end_of_day(){
        $data = $this->syter->spawn(null);
        $data['code'] = managerEndOfDayPage();
        // $data['add_css'] = array('css/pos.css','css/cashier.css','css/cashier.css');
        $data['load_js'] = 'dine/manager.php';
        $data['use_js'] = 'endofdayJS';
        $this->load->view('load',$data);
    }
    public function manager_orders(){
        $data = $this->syter->spawn(null);
        $data['code'] = managerOrdersPage();
        $data['add_css'] = array('css/pos.css','css/onscrkeys.css','css/virtual_keyboard.css', 'css/cashier.css');
		$data['add_js'] = array('js/on_screen_keys.js','js/jquery.keyboard.extension-navigation.min.js','js/jquery.keyboard.min.js');
        $data['load_js'] = 'dine/manager.php';
        // $data['use_js'] = 'managerJs';
        $data['use_js'] = 'managerOrdersJS';
        $this->load->view('load',$data);
    }

    public function print_endofday_receipt($asJson=true)
    {
        $this->load->model('dine/manager_model');
        // // Load PHPRtfLite Class
        // require_once APPPATH."/third_party/PHPRtfLite.php";

        /*
         * -----------------------------------------------------------
         *      Start of Receipt Printing
         * -----------------------------------------------------------
        */
        // $return = $this->get_order(false,$sales_id);
        // $order = $return['order'];
        // $details = $return['details'];

        $print_str = "\r\n"
            .$this->align_center("END OF DAY REPORT",46," ")."\r\n"
            .$this->align_center("",46," ")."\r\n"
            .$this->align_center("GENERATED ".date('m/d/Y h:i:s A'),46," ")."\r\n"
            ."---------------------------------------------"."\r\n\r\n"
            .$this->align_center("TRANSACTION SUMMARY",46," ")."\r\n";

        $date = date('Y-m-d');
        $gtotal = $summary_total = 0;

        $cash_total = 0;
        $credit_total = 0;
        $check_total = 0;
        $debit_total = 0;
        $gc_total = 0;
        ///////////////FOR CASH
        $get_cash = $this->manager_model->get_payment_type($date,'cash');
        if(count($get_cash) > 0){

            $get_cash_count = $this->manager_model->get_payment_count($date,'cash');

            foreach($get_cash as $cval){
                if($cval->to_pay > $cval->amount){
                    $cash_total += $cval->amount;
                }else{
                    $cash_total += $cval->to_pay;
                }
                // echo $cash_total."---";
            }

            $print_str .=  $this->append_chars('CASH('.count($get_cash_count).')',"right",23," ").
                        $this->append_chars('P'.number_format($cash_total,2),"left",23," ")."\r\n";
        }
        ///////////////FOR CREDIT
        $get_credit = $this->manager_model->get_payment_type($date,'credit');
        if(count($get_credit) > 0){

            $get_credit_count = $this->manager_model->get_payment_count($date,'credit');

            foreach($get_credit as $cval){
                if($cval->to_pay > $cval->amount){
                    $credit_total += $cval->amount;
                }else{
                    $credit_total += $cval->to_pay;
                }
                // echo $cash_total."---";
            }

            $print_str .=  $this->append_chars('CREDIT('.count($get_credit_count).')',"right",23," ").
                        $this->append_chars('P'.number_format($credit_total,2),"left",23," ")."\r\n";
        }
         ///////////////FOR DEBIT
        $get_debit = $this->manager_model->get_payment_type($date,'debit');
        if(count($get_debit) > 0){

            $get_debit_count = $this->manager_model->get_payment_count($date,'debit');

            foreach($get_debit as $cval){
                if($cval->to_pay > $cval->amount){
                    $debit_total += $cval->amount;
                }else{
                    $debit_total += $cval->to_pay;
                }
                // echo $cash_total."---";
            }

            $print_str .=  $this->append_chars('DEBIT('.count($get_debit_count).')',"right",23," ").
                        $this->append_chars('P'.number_format($debit_total,2),"left",23," ")."\r\n";
        }
         ///////////////FOR GC
        $get_gc = $this->manager_model->get_payment_type($date,'gc');
        if(count($get_gc) > 0){

            $get_gc_count = $this->manager_model->get_payment_count($date,'gc');

            foreach($get_gc as $cval){
                if($cval->to_pay > $cval->amount){
                    $gc_total += $cval->amount;
                }else{
                    $gc_total += $cval->to_pay;
                }
                // echo $cash_total."---";
            }

            $print_str .=  $this->append_chars('GIFTCARD('.count($get_gc_count).')',"right",23," ").
                        $this->append_chars('P'.number_format($gc_total,2),"left",23," ")."\r\n";
        }

        $print_str .= $this->append_chars('============',"left",46," ")."\r\n";
        $gtotal = $cash_total + $credit_total + $debit_total + $gc_total;
        $print_str .= $this->append_chars('P'.number_format($gtotal,2),"left",46," ")."\r\n\r\n";

        //////////////////////////////////summary type

        $print_str .= $this->align_center("SALES BY ORDER TYPE SUMMARY",46," ")."\r\n";

        $counter_total = $dinein_total = $drivethru_total = $deliver_total = $pickup_total = $takeout_total = 0;

        ///////////////FOR COUNTER
        $get_counter = $this->manager_model->get_summary_type($date,'counter');
        if(count($get_counter) > 0){
            $get_counter_count = $this->manager_model->get_summary_count($date,'counter');
            foreach($get_counter as $cval){

                    $counter_total += $cval->total_paid;

            }

            $print_str .=  $this->append_chars('COUNTER('.count($get_counter_count).')',"right",23," ").
                        $this->append_chars('P'.number_format($counter_total,2),"left",23," ")."\r\n";
        }
        ///////////////FOR DINEIN
        $get_dinein = $this->manager_model->get_summary_type($date,'dinein');
        if(count($get_dinein) > 0){
            $get_dinein_count = $this->manager_model->get_summary_count($date,'dinein');
            foreach($get_dinein as $cval){

                    $dinein_total += $cval->total_paid;

            }

            $print_str .=  $this->append_chars('DINE-IN('.count($get_dinein_count).')',"right",23," ").
                        $this->append_chars('P'.number_format($dinein_total,2),"left",23," ")."\r\n";
        }
        ///////////////FOR DRIVETHRU
        $get_drive = $this->manager_model->get_summary_type($date,'drivethru');
        if(count($get_drive) > 0){
            $get_drivethru_count = $this->manager_model->get_summary_count($date,'drivethru');
            foreach($get_drive as $cval){

                    $drivethru_total += $cval->total_paid;

            }

            $print_str .=  $this->append_chars('DRIVE-THRU('.count($get_drivethru_count).')',"right",23," ").
                        $this->append_chars('P'.number_format($drivethru_total,2),"left",23," ")."\r\n";
        }
        ///////////////FOR DELIVERY
        $get_deliver = $this->manager_model->get_summary_type($date,'delivery');
        if(count($get_deliver) > 0){
            $get_deliver_count = $this->manager_model->get_summary_count($date,'delivery');
            foreach($get_deliver as $cval){

                    $deliver_total += $cval->total_paid;

            }

            $print_str .=  $this->append_chars('DELIVERY('.count($get_deliver_count).')',"right",23," ").
                        $this->append_chars('P'.number_format($deliver_total,2),"left",23," ")."\r\n";
        }
        ///////////////FOR PICKUP
        $get_pickup = $this->manager_model->get_summary_type($date,'pickup');
        if(count($get_pickup) > 0){
            $get_pickup_count = $this->manager_model->get_summary_count($date,'pickup');
            foreach($get_pickup as $cval){

                    $pickup_total += $cval->total_paid;

            }

            $print_str .=  $this->append_chars('PICKUP('.count($get_pickup_count).')',"right",23," ").
                        $this->append_chars('P'.number_format($pickup_total,2),"left",23," ")."\r\n";
        }
        ///////////////FOR TAKEOUT
        $get_takeout = $this->manager_model->get_summary_type($date,'takeout');
        if(count($get_takeout) > 0){
            $get_takeout_count = $this->manager_model->get_summary_count($date,'takeout');
            foreach($get_takeout as $cval){

                    $takeout_total += $cval->total_paid;

            }

            $print_str .=  $this->append_chars('TAKEOUT('.count($get_takeout_count).')',"right",23," ").
                        $this->append_chars('P'.number_format($takeout_total,2),"left",23," ")."\r\n";
        }

        $print_str .= $this->append_chars('============',"left",46," ")."\r\n";
        $summary_total = $drivethru_total + $counter_total + $dinein_total;
        $print_str .= $this->append_chars('P'.number_format($summary_total,2),"left",46," ")."\r\n\r\n";

        //////////////////////////////////station summary

        $print_str .= $this->align_center("SALES BY STATION SUMMARY",46," ")."\r\n";

        $get_terminals = $this->manager_model->get_terminals();

        if(count($get_terminals) > 0){
            $total_all_terminals = 0;
            foreach ($get_terminals as $val) {
                $t_terminal = 0;
                $get_terminal_total = $this->manager_model->get_terminal_total($date,$val->terminal_id);
                if(count($get_terminal_total) > 0){
                    // $CI->make->sDivRow();
                    // $CI->make->sDivCol('6','left');
                    //     $CI->make->span('POS STATION '.$val->terminal_id.'('.count($get_terminal_total).')',array('class'=>'', 'style'=>'font-size:14px;'));
                    // $CI->make->eDivCol();
                    foreach($get_terminal_total as $cval){

                            $t_terminal += $cval->total_paid;

                        // echo $cash_total."---";
                    }

                    $print_str .=  $this->append_chars('POS STATION '.$val->terminal_id.'('.count($get_terminal_total).')',"right",23," ").
                        $this->append_chars('P'.number_format($t_terminal,2),"left",23," ")."\r\n";
                    // $CI->make->sDivCol('6','right');
                    //     $CI->make->span('P'.number_format($t_terminal,2),array('class'=>'', 'style'=>'font-size:14px;'));
                    // $CI->make->eDivCol();
                    // $CI->make->eDivRow();
                }
                $total_all_terminals += $t_terminal;
            }

            $print_str .= $this->append_chars('============',"left",46," ")."\r\n";
            $print_str .= $this->append_chars('P'.number_format($total_all_terminals,2),"left",46," ")."\r\n\r\n";
        }

        //////////////////////////////////void summary

        $print_str .= $this->align_center("VOID SUMMARY",46," ")."\r\n";

        $openvoid_total = $settledvoid_total = 0;
        ///////////////FOR VOID OPEN
        $openvoid = $this->manager_model->get_void_open($date);
        if(count($openvoid) > 0){
            foreach($openvoid as $cval){

                    $openvoid_total += $cval->total_amount;

                // echo $cash_total."---";
            }
            $print_str .=  $this->append_chars('OPEN-VOID('.count($openvoid).')',"right",23," ").
                        $this->append_chars('P'.number_format($openvoid_total,2),"left",23," ")."\r\n";

        }
        ///////////////FOR SETTLE VOID
        $settledvoid = $this->manager_model->get_void_settled($date);
        if(count($settledvoid) > 0){
            foreach($settledvoid as $cval){

                    $settledvoid_total += $cval->total_amount;

                // echo $cash_total."---";
            }
            $print_str .=  $this->append_chars('SETTLED-VOID('.count($settledvoid).')',"right",23," ").
                        $this->append_chars('P'.number_format($settledvoid_total,2),"left",23," ")."\r\n";

        }

        $print_str .= $this->append_chars('============',"left",46," ")."\r\n";
        $total_all_void = $openvoid_total + $settledvoid_total;
        $print_str .= $this->append_chars('P'.number_format($total_all_void,2),"left",46," ")."\r\n\r\n";

        ////////////////////////////end////////////////////////

        $filename = "endofday.txt";
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
        //unlink($filename);
        unlink($batfile);

        if ($asJson)
            echo json_encode(array('msg'=>'End of Day Report has been printed'));
        else
            return array('msg'=>'End of Day Report has been printed');
    }
    private function append_chars($string,$position = "right",$count = 0, $char = "")
    {
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
    private function align_center($string,$count,$char = " ")
    {
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
    public function manager_end_of_day_report()
    {
        $data = $this->syter->spawn(null);
        $data['code'] = managerEndofDayReport();
        $data['add_css'] = array('css/pos.css','css/onscrkeys.css','css/virtual_keyboard.css', 'css/cashier.css');
        $data['add_js'] = array('js/bootbox2.min.js');
        $data['load_js'] = 'dine/manager.php';
        $data['use_js'] = 'endofdayReportJS';
        $this->load->view('load',$data);
    }
    public function manager_xread(){
        $data = $this->syter->spawn(null);

        $data['code'] = managerXreadPage();
        $data['load_js'] = 'dine/manager.php';
        $data['use_js'] = 'xreadJS';
        $this->load->view('load',$data);
    }
    public function manager_zread(){
        $data = $this->syter->spawn(null);

        $date = date('Y-m-d');
        $allsales = $this->manager_model->get_all_sales_today($date);

        $data['code'] = managerZreadPage($allsales);
        $data['load_js'] = 'dine/manager.php';
        $data['use_js'] = 'zreadJS';
        $this->load->view('load',$data);
    }
    public function print_reading($asJson=true)
    {
        $this->load->model('site/site_model');
        $this->load->model('dine/manager_model');
        $this->load->model('dine/cashier_model');

        $date = date('Y-m-d');
        if($this->input->post('read') == 'xread'){
            $allsales = $this->manager_model->get_all_sales_today($date,TERMINAL_ID);
        }else{
            $allsales = $this->manager_model->get_all_sales_today($date);
        }

        //echo count($allsales);
        $print_str = "";
        foreach($allsales as $val){


        $return = $this->get_order(false,$val->sales_id);
        $order = $return['order'];
        $details = $return['details'];
        $discounts = $return['discounts'];
        $tax = $return['tax'];

        $print_str .= "\r\n"
            .$this->align_center("CHOWKING ROBINSONS GALLERIA (0913)",46," ")."\r\n"
            .$this->align_center("owned by FRESH 'N FAMOUS",46," ")."\r\n"
            .$this->align_center("Level 1 Robinson's Galleria Ortigas QC",46," ")."\r\n"
            .$this->align_center("TIN# 000-333-173-015-VAT",46," ")."\r\n"
            .$this->align_center("POS01: SNTPA00061",46," ")."\r\n"
            .$this->align_center("BIR Permit No. 0511-116-9186-015",46," ")."\r\n";

        $void = "";
        if($order['inactive'] == 1){
            $void = "VOIDED";
            $reason = $order['reason'];
                $print_str .= $this->align_center('Receipt No. '.$order['ref']." (VOIDED)",46," ")."\r\n"
                           .$this->align_center($order['reason'],46," ")."\r\n";

        }else{
            $print_str .= $this->align_center('Receipt No. '.$order['ref'],46," ")."\r\n";
        }

        $print_str .= "============================================="."\r\n";

        if (!empty($order['pay_types']))
            $print_str .= $this->align_center(date('Y-m-d D H:i:s',strtotime($order['datetime']))." ".$order['terminal_name']." OR# ".$order['ref'],46," ")."\r\n";
        else
            $print_str .= $this->align_center(date('Y-m-d D H:i:s',strtotime($order['datetime'])),46," ")."\r\n";

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
        foreach ($tax as $vtax) {
            $vat += $vtax['amount'];
        }

        $print_str .= "\r\n".$this->append_chars(ucwords($order['type']),"right",35," ").$this->append_chars(number_format($order['amount'] - $vat,2),"left",10," ")."\r\n";
        foreach($tax as $vtax){
        $print_str .= $this->append_chars($vtax['name'],"right",35," ").$this->append_chars(number_format($vtax['amount'],2),"left",10," ")."\r\n";
        }
        if (!empty($order['pay_type'])) {
            $print_str .= "\r\n".$this->append_chars("Amount due","right",35," ").$this->append_chars("P".number_format($order['amount'],2),"left",10," ")."\r\n";
            $print_str .= $this->append_chars(ucwords($order['pay_type']),"right",35," ").$this->append_chars("P".number_format($order['pay_amount'],2),"left",10," ")."\r\n";
            if (!empty($order['pay_ref'])) {
                $print_str .= $this->append_chars("     Reference ".$order['pay_ref'],"right",45," ")."\r\n";
            }
            $print_str .= $this->append_chars("Change","right",35," ").$this->append_chars("P".number_format($order['pay_amount'] - $order['amount'],2),"left",10," ")."\r\n";


        } else {
            $print_str .= "\r\n".$this->align_center("=============================================",45," ");
            $print_str .= "\r\n".$this->append_chars("Amount due","right",35," ").$this->append_chars("P".number_format($order['amount'],2),"left",10," ")."\r\n";
            $print_str .= $this->align_center("=============================================",45," ");
        }
            $print_str .= "\r\n"
                .$this->align_center("This serves as your official receipt.",46," ")."\r\n"
                .$this->align_center("Thank you and please come again.",46," ")."\r\n"
                .$this->align_center("For feedback, please call us at",46," ")."\r\n"
                .$this->align_center("(02)XXX-XXXX or (XXX)XXX-XXXX",46," ")."\r\n"
                .$this->align_center("Email : feedback@xxxx.com.ph",46," ")."\r\n"
                .$this->align_center(" Please visit us at www.xxxxxx.com.ph",46," ")."\r\n\r\n"
                .$this->align_center("****************************************",46," ")."\r\n\r\n";


        }///////////////end ng foreach

        if($this->input->post('read') == 'xread'){
            $filename = "xreading.txt";
            $msg = "X-Reading has been print.";
        }else{
             $filename = "zreading.txt";
             $msg = "Z-Reading has been print.";
        }
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
        // unlink($filename);
        unlink($batfile);

        if ($asJson)
            echo json_encode(array('msg'=>$msg));
        else
            return array('msg'=>$msg);
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
                "guest"=>$res->guest,
                "user_id"=>$res->user_id,
                "name"=>$res->username,
                "terminal_id"=>$res->terminal_id,
                "terminal_name"=>$res->terminal_name,
                "shift_id"=>$res->shift_id,
                "datetime"=>$res->datetime,
                "amount"=>$res->total_amount,
                "balance"=>$res->total_amount - $res->total_paid,
                "paid"=>$res->paid,
                // "pay_type"=>$res->pay_type,
                // "pay_amount"=>$res->pay_amount,
                // "pay_ref"=>$res->pay_ref,
                // "pay_card"=>$res->pay_card,
                "inactive"=>$res->inactive,
                "reason"=>$res->reason,
            );
        }

        $order_menus = $this->cashier_model->get_trans_sales_menus(null,array("trans_sales_menus.sales_id"=>$sales_id));
        $order_mods = $this->cashier_model->get_trans_sales_menu_modifiers(null,array("trans_sales_menu_modifiers.sales_id"=>$sales_id));
        $sales_discs = $this->cashier_model->get_trans_sales_discounts(null,array("trans_sales_discounts.sales_id"=>$sales_id));
        $sales_tax = $this->cashier_model->get_trans_sales_tax(null,array("trans_sales_tax.sales_id"=>$sales_id));
        foreach ($order_menus as $men) {
            $details[$men->line_id] = array(
                "id"=>$men->sales_menu_id,
                "menu_id"=>$men->menu_id,
                "name"=>$men->menu_name,
                "code"=>$men->menu_code,
                "price"=>$men->price,
                "qty"=>$men->qty,
                "discount"=>$men->discount
            );
            $mods = array();
            foreach ($order_mods as $mod) {
                if($mod->line_id == $men->line_id){
                    $mods[$mod->sales_mod_id] = array(
                        "id"=>$mod->mod_id,
                        "line_id"=>$mod->line_id,
                        "name"=>$mod->mod_name,
                        "price"=>$mod->price,
                        "qty"=>$mod->qty,
                        "discount"=>$mod->discount
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
                    "items" => $items
                );
        }
        $tax = array();
        foreach ($sales_tax as $st) {
            $tax[$st->sales_tax_id] = array(
                    "sales_id"  => $st->sales_id,
                    "name"  => $st->name,
                    "rate" => $st->rate,
                    "amount" => $st->amount
                );
        }
        if($asJson)
            echo json_encode(array('order'=>$order,"details"=>$details,"discounts"=>$discounts,"tax"=>$tax));
        else
            return array('order'=>$order,"details"=>$details,"discounts"=>$discounts,"tax"=>$tax);
    }
    public function system_settings(){
        $this->load->model('site/site_model');
        $this->load->model('dine/manager_model');
        $this->load->model('dine/setup_model');
        $this->load->helper('core/on_screen_key_helper');
        $this->load->helper('dine/manager_helper');
        $data = $this->syter->spawn(null);

        $details = $this->setup_model->get_details(1);
        $det = $details[0];


        $data['code'] = systemSettingsPage($det);
        $data['add_css'] = array('css/pos.css','css/onscrkeys.css','css/virtual_keyboard.css', 'css/cashier.css');
        $data['add_js'] = array('js/on_screen_keys.js','js/jquery.keyboard.extension-navigation.min.js','js/jquery.keyboard.min.js');
        $data['load_js'] = 'dine/manager.php';
        $data['use_js'] = 'systemSettingsJS';
        $this->load->view('load',$data);
    }

    public function system_settings_db(){
        $this->load->model('dine/setup_model');
        $items = array(
            "branch_code"=>$this->input->post('branch_code'),
            "branch_name"=>$this->input->post('branch_name'),
            "branch_desc"=>$this->input->post('branch_desc'),
            "contact_no"=>$this->input->post('contact_no'),
            "delivery_no"=>$this->input->post('delivery_no'),
            "address"=>$this->input->post('address'),
            "tin"=>$this->input->post('tin'),
            "machine_no"=>$this->input->post('machine_no'),
            "bir"=>$this->input->post('bir'),
            "permit_no"=>$this->input->post('permit_no'),
            "serial"=>$this->input->post('serial'),
            "email"=>$this->input->post('email'),
            "website"=>$this->input->post('website')
            // "currency"=>$this->input->post('currency')
        );

            $this->setup_model->update_details($items, 1);
            // $id = $this->input->post('cat_id');
            $act = 'update';
            $msg = 'Updated Branch Details';

        echo json_encode(array('msg'=>$msg));
    }

    public function check_zread_okay()
    {
        $unsettled_trans = $this->check_unsettled_sales(false);
        $unclosed_xread = $this->check_unclosed_xread(false);

        if (!empty($unclosed_xread) || !empty($unsettled_trans))
            echo json_encode(!empty($unsettled_trans) ? $unsettled_trans : $unclosed_xread);
    }

    public function check_unclosed_xread($asJson=true)
    {
        $this->load->model('dine/clock_model');
        $shift = $this->clock_model->get_shifts('check_out IS NULL OR check_out = \'\' OR cashout_id IS NULL OR cashout_id =\'\'');

        $return_array = array();

        if (!empty($shift))
            $return_array = array('error'=>'<h5>Some shifts have missing X-Read data.<br/>Unable to process Z-Read.</h5>');

        if ($asJson) {
            echo json_encode($return_array);
            return false;
        } else
            return $return_array;
    }

    public function check_unsettled_sales($asJson=true,$date=null)
    {
        $this->load->model('dine/cashier_model');
        $unsettled_sales = $this->cashier_model->get_trans_sales(null,
            array(
                'trans_sales.inactive' => 0,
                'trans_sales.total_amount <' => 'trans_sales.total_paid',
                'trans_sales.type_id' => SALES_TRANS,
                'date(datetime)' => (is_null($date) ? date('Y-m-d') : $date)
            )
        );

        $return_array = array();

        if (!empty($return_array))
            $return_array = array('error'=>'<h5>There are unsettled transactions for '.$date.'. Unable to proceed.</h5>');

        if ($asJson) {
            echo json_encode($return_array);
            return false;
        } else
            return $return_array;
    }

    public function manager_reports()
    {
        $data = $this->syter->spawn(null);
        $data['code'] = managerReportPage();
        // $data['add_css'] = array('css/pos.css','css/onscrkeys.css','css/virtual_keyboard.css', 'css/cashier.css');
        $data['load_js'] = 'dine/manager.php';
        $data['use_js'] = 'managerReportsJs';
        $this->load->view('load',$data);
    }

    public function manager_report_form($report_title)
    {
        if ($report_title == "daily-sales") {
            $code = build_report("display_daily_sales",array(
                    'date' => array("Select date","date",date('Y-m-d'),null,array('class'=>'rOkay','ro-msg'=>'Please select a valid date')),
                ));
        } elseif ($report_title == "terminal-sales") {
            $code = build_report("display_daily_sales",array(
                    'date' => array("Select date","date",date('Y-m-d'),null,array('class'=>'rOkay','ro-msg'=>'Please select a valid date')),
                ));
        } elseif ($report_title == "cashier-sales") {
            $code = build_report("display_daily_sales",array(
                    'date' => array("Select date","date",date('Y-m-d'),null,array('class'=>'rOkay','ro-msg'=>'Please select a valid date')),
                ));
        } elseif ($report_title == "customer-statistics") {
            $code = build_report("display_daily_sales",array(
                    'date' => array("Select date","date",date('Y-m-d'),null,array('class'=>'rOkay','ro-msg'=>'Please select a valid date')),
                ));
        } elseif ($report_title == "senior-citizen-dd") {
            $code = build_report("display_daily_sales",array(
                    'date' => array("Select date","date",date('Y-m-d'),null,array('class'=>'rOkay','ro-msg'=>'Please select a valid date')),
                ));
        } elseif ($report_title == "voided-transactions") {
            $code = build_report("display_daily_sales",array(
                    'date' => array("Select date","date",date('Y-m-d'),null,array('class'=>'rOkay','ro-msg'=>'Please select a valid date')),
                ));
        } elseif ($report_title == "daily-tax-dues") {
            $code = build_report("display_daily_sales",array(
                    'date' => array("Select date","date",date('Y-m-d'),null,array('class'=>'rOkay','ro-msg'=>'Please select a valid date')),
                ));
        } elseif ($report_title == "daily-menu-orders") {
            $code = build_report("display_daily_sales",array(
                    'date' => array("Select date","date",date('Y-m-d'),null,array('class'=>'rOkay','ro-msg'=>'Please select a valid date')),
                ));
        } elseif ($report_title == "daily-top-menu") {
            $code = build_report("display_daily_sales",array(
                    'date' => array("Select date","date",date('Y-m-d'),null,array('class'=>'rOkay','ro-msg'=>'Please select a valid date')),
                ));
        }
        echo json_encode(array('code'=>$code));
    }

    public function report_stuff(){

    }
}