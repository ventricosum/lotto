<?php

class tbLotto extends DataMapper {

    var $table = 'tbLotto';

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
	
	function get_lotto() {
        $this->db->select("id as lotto_id, name as lotto_name, logo");
        return $this->db->get($this->table)->result_array();
    }

    function get_table_lotto() {
        return $this->db->get($this->table)->result_array();
    }
}