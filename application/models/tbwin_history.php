<?php

class tbWin_history extends DataMapper {

    var $table = 'tbWin_history';

    function __construct($init = array(), $code_to_throw = FALSE) {
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
	
	//bid_type: 0 => football, 1 => lotto
	static function add ($prize, $bid_id, $bid_type = 0)
	{
		$win = new tbWin_history();
		$win->prize = $prize;
		$win->bid_id = $bid_id;
		$win->bid_type = $bid_type;
		$win->save();
	}
	
	function get_latest_winners($limit) {
		$this->db->select("t1.prize, t1.bid_type");
		$this->db->select("CASE WHEN t3.display_name is NULL THEN t5.display_name ELSE t3.display_name END as display_name", FALSE);
		$this->db->from("$this->table t1");
		$this->db->join("tbLotto_bid t2", "t1.bid_id = t2.id AND t1.bid_type = 1","left");
		$this->db->join("plusauthentication_user t3", "t2.user_id = t3.id", "left");
		$this->db->join("tbFootball_bid t4", "t1.bid_id = t4.id AND t1.bid_type = 0","left");
		$this->db->join("plusauthentication_user t5", "t4.user_id = t5.id", "left");
        $this->db->where("t1.prize > 0");
		$this->db->order_by('t1.id', 'DESC');
		$this->db->limit($limit);
		return $this->db->get()->result_array();
	}

}