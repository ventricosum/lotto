<?php

class tbLotto_result_tracking extends DataMapper {

    var $table = 'tbLotto_result_tracking';

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
		$this->db->where('DATE(dateline) < CURRENT_DATE');
		$this->db->where('lotto_id', $lotto_id);
		$this->order_by('id', 'DESC');
		$this->limit(1);
		return $this->db->get($this->table)->row_array();
	}
}