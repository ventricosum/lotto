<?php

class tbTree_interest extends DataMapper {

    var $table = 'tbTree_interest';

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

    function get_tree_interest() {
        return $this->db->get($this->table)->result_array();
    }

    function get_interest_by_level($level) {
        $this->db->where('id', $level);
        return $this->db->get($this->table)->row_array();
    }

}