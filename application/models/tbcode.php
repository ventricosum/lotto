<?php

class tbCode extends DataMapper {

    var $table = 'tbCode';

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

    function get_codes() {
        $this->db->select('code, value');
        $this->db->where('status', 0);
        return $this->db->get($this->table)->result_array();
    }
}