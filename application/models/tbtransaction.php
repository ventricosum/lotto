<?php

class tbTransaction extends DataMapper {

    var $table = 'tbTransaction';

    function __construct($init = null, $code_to_throw = FALSE) {
        parent::__construct();
        if (count($init) > 0) {
            foreach ($init as $key => $value) {
                $this->where($key, $value);
            }
            $this->get();
            if (!$this->exists() && ($code_to_throw !== FALSE)) {
                throw new Exception("", $code_to_throw);
            }
            return $this;
        }
    }
	
	static function add ($user_id, $type, $amount, $status, $transaction_type = null, $transaction_info = "")
	{
		$transaction = new tbTransaction();
		$transaction->user_id = $user_id;
		$transaction->amount = $amount;
		$transaction->type = $type;
		$transaction->status = $status;
		
		if (isset($transaction_type))
		{
			if ($transaction_type == 0)
			{
				// bank
				$transaction->bank_info = json_encode($transaction_info);
			}
			else
			{
				// thirdparty
				$transaction->thirdparty_info = json_encode($transaction_info);
			}
		}
		$transaction->save();
	}
	/**
	 * 
	 */
	function get_withdrawal ()
	{
		$this->db->select("*");
		$this->db->from("( select user_id,id as tran_id,amount,createtime,bank_info,thirdparty_info,status from tbTransaction where type = 1 ) tran ");
		$this->db->join("( select id,username from plusauthentication_user ) user ", "tran.user_id = user.id", "inner");
		
		return $this->db->get()->result();
	}
	/**
	 * get_history_transactions
	 */
	static function get_history_transactions ($user_id, $offset, $limit)
	{
		$transaction = new tbTransaction();
		$transaction->where('status', 1);
		$transaction->where('user_id', $user_id);
		$transaction->order_by('createtime', 'desc');
		$transaction->limit($limit, $offset);
		return $transaction->get();
	}
}