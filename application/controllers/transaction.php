<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Transaction extends CI_Controller {
	/*
	 * 已结束: closed
	 * 未开赛: not start
	 * 进行中: in progress
	 * hteam: c4T1
	 * vteam: c4T2
	 * "error_code":0 -> success
	 * "reason":"联赛名称错误": League name error
	 */
	private $data = array();
    public function __construct()
	{
		parent::__construct();
		$this -> data['title'] = LocalizedString("Transaction");
	}
	/**
	 * index () - table loads all pending withdrawals
	 * @access public
	 */
	function index ()
	{
		$this -> data['user'] = checklogin();
		$this -> data['navigate'] = navigate(3, -1);
		$this -> data['contents']['col-sm-12'] = load("element/table", $this->table_withdrawal(), TRUE);

		$this -> load -> view('element/adminLTE', $this->data);
	}
	/**
	 * get table withdrawal
	 */
	function table_withdrawal()
  	{
    	$header = array(
	      	LocalizedString("Id"),
	      	LocalizedString("Createtime"),
	      	LocalizedString("Username"),
	      	LocalizedString("Status"),
	      	LocalizedString("Amount"),
	      	LocalizedString("Transaction account"),
	      	LocalizedString("Action")
      	);
		$dataset = tbTransaction::get_withdrawal();
		$rows = array();
	    foreach ($dataset as $data)
	    {
	    	$action = "";
	    	if ($data->status == 0)
			{
				$action.= html("a", LocalizedString("Approve"), array(
					"onclick" => "return confirm('Are u sure?')",
					"href" => "/transaction/process_transaction?id={$data->tran_id}&status=1"
				));	
				$action.= br();
				$action.= html("a", LocalizedString("Decline"), array(
					"onclick" => "return confirm('Are u sure?')",
					"href" => "/transaction/process_transaction?id={$data->tran_id}&status=-1"
				));	
			}
			else
			{
				
			}
	
		    $row = array();
			$row[] = $data->tran_id;
		    $row[] = $data->createtime;
		    $row[] = $data->username;
		    $row[] = $this->convert_status_as_text($data->status);
			$row[] = $data->amount;
		    $row[] = $this->convert_transaction_account_as_text(json_decode(json_decode($data->bank_info)), json_decode(json_decode($data->thirdparty_info)));
			$row[] = $action;
		    $rows[] = $row;
	    }
	    return array("headers" => $header, "rows" => $rows, "tableId" => "transaction_withdraw");
  	}
	/**
	 * convert_status_as_text($status)
	 * @access protected
	 */
	protected function convert_status_as_text ($status)
	{
		switch ($status) {
			case 1:
				return "approved";
			case -1:			
				return "declined";
			default:
				return "pending";
		}
	}
	/**
	 * 
	 */
	protected function convert_transaction_account_as_text ($bank_info = NULL, $transaction_info = NULL)
	{
		if (isset($bank_info))
		{
			$banks = $this->get_banks_as_array();
			return html("p", "Bank: " . @$banks[$bank_info->choice]) .
					html("p", "First name: " . @$bank_info->first_name) .
					html("p", "Last name: " . @$bank_info->last_name) .
					html("p", "Card no.: " . @$bank_info->card_no);
		} 
		elseif (isset($transaction_info))
		{
			$payment = $this->get_thirdparty_payments_as_array();
			return html("p", "3rd Party Payment: " . @$payment[$transaction_info->choice]) .
					html("p", "Userame: " . @$transaction_info->username) .
					html("p", "First name: " . @$transaction_info->first_name) .
					html("p", "Last name: " . @$transaction_info->last_name) .
					html("p", "Email: " . @$transaction_info->email);
		}
		else
		{
			return "";
		}
	}
	/**
	 * get_banks_as_dict ()
	 * @access public
	 */
	function get_banks_as_array ()
	{
        $banks = new tbBank();
		$banks->get();
		
		$dataset = array();
		foreach ($banks as $bank) 
		{
			$dataset+= array($bank->id => $bank->name); 
		}
		return $dataset;
	}
	/**
	 * get_thirdparty_payments_as_dict ()
	 * @access public
	 */
	function get_thirdparty_payments_as_array ()
	{
        $tps = new tbThirdparty_payment();
		$tps->get();
		
		$dataset = array();
		foreach ($tps as $tp) 
		{
			$dataset+= array($tp->id => $tp->name); 
		}
		return $dataset;
	}
	/**
	 * @access public
	 * @method get
	 * @param status
	 * @param id
	 */
	function process_transaction ()
	{
		$id = $this->input->get('id');
		$status = $this->input->get('status');
		
		if (!in_array($status, array(1, -1)))
		{
			// prevent
			$status = -1;
		}
		
		try {
			$tran = new tbTransaction(array('id'=>$id));
			if ($tran->exists() && ($tran->type == 1))
			{
				$tran->status = $status;
				$tran->save();
				
				if ($status == 1)
				{
					// subtract account balance
					tbUser::change_account_balance(-1*$tran->amount, $tran->user_id);
                    $this->load->library('pushplus/lbpush_plus');
                    $this->lbpush_plus->pushByOwners($tran->user_id, "You withdrawal has been approved successfully");
				}
			}
			redirect($_SERVER["HTTP_REFERER"]);
		} catch (Exception $e) {
			echo $e->getMessage();
		}
	}
}