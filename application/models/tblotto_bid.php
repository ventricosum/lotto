<?php

class tbLotto_bid extends DataMapper {

    var $table = 'tbLotto_bid';

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
	
	static function add($round_id, $user_id, $bet, $mode, $selection) {
		$bid = new tbLotto_bid();
		$bid->round_id = $round_id;
		$bid->user_id = $user_id;
		$bid->bet = $bet;
		$bid->mode = $mode;
		$bid->selection = $selection;
		$bid->save();
		return $bid->id;
	}

    function add2($round_id, $user_id, $bet, $mode, $selection) {
        $data['round_id'] = $round_id;
        $data['user_id'] = $user_id;
        $data['bet'] = $bet;
        $data['mode'] = $mode;
        $data['selection'] = $selection;
        $this->db->insert($this->table,$data);
        return $this->db->insert_id();
    }
	
	function get_bids($round_id) {
		$this->db->where('round_id', $round_id);
		return $this->db->get($this->table)->result_array();
	}
	
	function get_history($user_id, $status, $offset, $limit)
	{
		$this->db->select("t1.round_id, t1.bet, t1.createtime");
		$this->db->select("CASE WHEN t2.prize IS NULL THEN -t1.bet*2 ELSE t2.prize END as prize", FALSE);
        $this->db->select("t3.lotto_id");
        $this->db->select("t4.name");
		$this->db->from("$this->table t1");
        if($status == 1) {
            $this->db->join("tbRound t3", "t1.round_id = t3.id AND t3.status = 1");
        } elseif($status == 2) {
            $this->db->join("tbRound t3", "t1.round_id = t3.id AND t3.status = 0");
        } else {
            $this->db->join("tbRound t3", "t1.round_id = t3.id");
        }
        $this->db->join("tbLotto t4", "t3.lotto_id = t4.id");
		$this->db->join("tbWin_history t2", "t1.id = t2.bid_id and t2.bid_type = 1", "left");
		$this->db->where('user_id', $user_id);
		$this->db->order_by('createtime', 'desc');
		$this->db->limit($limit, $offset);
		return $this->db->get()->result_array();
	}

    function get_summary($user_id, $status) {
        $this->db->select("CASE WHEN t2.prize IS NULL THEN -t1.bet*2 ELSE t2.prize END as prize", FALSE);
        $this->db->from("$this->table t1");
        $this->db->join("tbWin_history t2", "t1.id = t2.bid_id", "left");
        if($status == 1) {
            $this->db->join("tbRound t3", "t1.round_id = t3.id AND t3.status = 1");
        } elseif($status == 2) {
            $this->db->join("tbRound t3", "t1.round_id = t3.id AND t3.status = 0");
        } else {
            $this->db->join("tbRound t3", "t1.round_id = t3.id");
        }
        $this->db->where("t1.user_id", $user_id);
        return $this->db->get()->result_array();
    }

    function get_total_money_from_bids($lotto_id) {
        $this->db->select("SUM(bet * 2) as total_money");
        $this->db->join("tbRound t2", "$this->table.round_id = t2.id");
        $this->db->where('lotto_id', $lotto_id);
        return $this->db->get($this->table)->row_array();
    }

    static function delete_bid($round_id) {
        $lotto_bid = new tbLotto_bid();
        $lotto_bid->where('round_id', $round_id)->get();
        $lotto_bid->delete_all();
    }
}