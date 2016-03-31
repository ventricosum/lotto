<?php

class tbProfit_downline extends DataMapper {

    var $table = 'tbProfit_downline';

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
	
	static function add ($user_id, $profit)
	{
		$user = new tbUser(array("id"=>$user_id));
		$upline = new tbUser(array("id"=>$user->upline));
		
		if ($upline->result_count() == 1)
		{
			$rate = tbSetting::get_setting('downline_rate');
			$transaction = new tbProfit_downline();
			$transaction->user_id = $user_id;
			$transaction->profit = $profit*floatval($rate);
			$transaction->upline_id = $upline->id;
			$transaction->save();
		}
	}

    static function add2 ($downline_id, $upline_id, $profit)
    {
        $transaction = new tbProfit_downline();
        $transaction->user_id = $downline_id;
        $transaction->profit = $profit;
        $transaction->upline_id = $upline_id;
        $transaction->save();
    }
	/**
	 * 
	 */
	function get_downlines_profit ($upline)
	{
		$this->db->select("user.username, coalesce(profit.profit, 0) as profit", FALSE);
		$this->db->from("(select * from plusauthentication_user where upline = {$upline}) user");
		$this->db->join("(SELECT sum(profit) as profit, user_id as downline_id FROM `tbProfit_downline` where upline_id = {$upline} group by user_id) profit", "user.id = profit.downline_id", "left");
		
		return $this->db->get()->result();
	}
	/**
	 * 
	 */
	function get_total_downlines_profit ($upline)
	{
		$sql = "SELECT sum(profit) as profit FROM `tbProfit_downline` where upline_id = {$upline}";
		
		return $this->db->query($sql)->result();
	}

    function retrieve_full_tree ($root_id = null)
    {
        $sql = "SELECT t2.id, t2.username
				FROM {$this->_table} AS node
				JOIN {$this->_table} parent ON node.lft BETWEEN parent.lft AND parent.rgt
                JOIN plusauthentication_user t2 ON node.id = t2.id
				";
        if (!empty($root_id))
        {
            $sql.= " WHERE parent.id = {$root_id}";
        }
        $sql.= " ORDER BY node.lft;";
        return $this->get_result($sql);
    }

    function get_downline_profit($downline_id, $upline_id) {
        $this->db->select("t1.username, COALESCE(SUM(t2.profit), 0) as profit", FALSE);
        $this->db->from("plusauthentication_user t1");
        $this->db->join("$this->table t2", "t1.id = t2.user_id AND user_id = $downline_id AND upline_id = $upline_id", "left");
        $this->db->where('t1.id', $downline_id);
        return $this->db->get()->row_array();
    }
}