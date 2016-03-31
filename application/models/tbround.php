<?php

class tbRound extends DataMapper {

    var $table = 'tbRound';

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

    function get_last_result_id($lotto_id) {
        $this->db->where('lotto_id', $lotto_id);
        $this->order_by('id', 'DESC');
        $this->limit(1);
        return $this->db->get($this->table)->row_array();
    }
	
	function get_latest_last_day_round($lotto_id) {
		$this->db->where('DATE(starttime) = CURRENT_DATE');
		$this->db->where('lotto_id', $lotto_id);
		$this->db->where('status', 0);
		$this->limit(1);
		return $this->db->get($this->table)->row_array();
	}

    function get_round_by_result_id($lotto_id, $result_id) {
        $this->db->select("id, result_id, endtime");
        $this->db->where('lotto_id', $lotto_id);
        $this->db->where('result_id', $result_id);
        $this->db->where('status', 0);
        $this->limit(1);
        return $this->db->get($this->table)->row_array();
    }
	
	static function update_result($id, $result) {
		$round = new tbRound(array('id' => $id));
		if($round->exists()) {
			$round->result = $result;
			$round->status = 1;
			$round->save();
		}
	}
	
	static function delete_empty_round() {
		$round = new tbRound();
		$round->where('status', 0)->get();
		$round->delete_all();
	}

    function delete_lotto56_empty_round() {
        $this->db->where('status', 0);
        $this->db->where_in('lotto_id', array('5,6'));
        $this->db->delete($this->table);
    }

    function get_last_day_empty_rounds() {
        $this->db->select("id as round_id, lotto_id");
        $this->db->where('DATE(createtime) < CURRENT_DATE');
        $this->db->where('status', 0);
        return $this->db->get($this->table)->result_array();
    }

    function get_bid_from_empty_round($round_id) {
        $this->db->select("t2.user_id, t2.bet");
        $this->db->from("$this->table t1");
        $this->db->join("tbLotto_bid t2", "t1.id = t2.round_id");
        $this->db->where("t2.round_id", $round_id);
        return $this->db->get()->result_array();
    }
	
	function get_latest_round($lotto_id) {
        $this->db->select("t2.name, t2.logo");
		$this->db->select("t1.id as round_id, t1.result_id as round_number, t1.starttime,t1.endtime, t1.result");
        $this->db->from("$this->table t1");
        $this->db->join("tbLotto t2", "t1.lotto_id = t2.id");
		$this->db->where('lotto_id', $lotto_id);
		$this->db->where('status', 1);
		$this->db->order_by('t1.id', "DESC");
		$this->db->limit(1);
		return $this->db->get()->row_array();
	}
	
	function check_round_exist($lotto_id) {
		$this->db->select("id as round_id, lotto_id, result_id as round_number, endtime, starttime");
		$this->db->where('lotto_id', $lotto_id);
		$this->db->where('NOW() BETWEEN starttime AND endtime');
		return $this->db->get($this->table)->row_array();
	}

    function get_table_rounds($lotto_id) {
        $this->db->select("t1.result_id as round_number");
        $this->db->select("CASE WHEN t2.bet IS NULL THEN 0 ELSE SUM(t2.bet * 2) END as total_money", FALSE);
        $this->db->from("$this->table t1");
        $this->db->join("tbLotto_bid t2", "t1.id = t2.round_id", "left");
        $this->db->where("t1.status",1);
        if($lotto_id) {
            $this->db->where("t1.lotto_id",$lotto_id);
        }
        $this->db->group_by("t1.result_id");
        $this->db->limit(100);
        $this->db->order_by("t1.id", "DESC");
        return $this->db->get()->result_array();
    }

    function get_rounds_by_lotto_id($lotto_id, $offset = 0, $limit = 30) {
        $this->db->select("result_id as round_number, result");
        $this->db->where("status", 1);
        $this->db->where("lotto_id", $lotto_id);
		$this->db->order_by("endtime", "desc");
        $this->limit($limit, $offset);
        return $this->db->get($this->table)->result_array();
    }
}