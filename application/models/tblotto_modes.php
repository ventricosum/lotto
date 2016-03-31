<?php

class tbLotto_modes extends DataMapper {

    var $table = 'tbLotto_modes';

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

    function get_lotto_modes($lotto_id, $mode_id) {
        $this->db->select("description, name");
        $this->db->where('lotto_id', $lotto_id);
        $this->db->where('mode_id', $mode_id);
        return $this->db->get($this->table)->result_array();
    }

    function get_all_lotto_modes($lotto_id) {
        $this->db->where('lotto_id', $lotto_id);
        return $this->db->get($this->table)->result_array();
    }

    function get_all_lotto_modes2($lotto_id) {
        $this->db->select("mode_id, name");
        $this->db->where('lotto_id', $lotto_id);
        return $this->db->get($this->table)->result_array();
    }

    static function add($lotto_id, $name, $des) {
        $mode = new tbLotto_modes();
        $mode->lotto_id = $lotto_id;
        $mode->name = $name;
        $mode->description = $des;
        $mode->save();
    }
}