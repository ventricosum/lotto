<?php

class tbAdvertisement extends DataMapper {

    var $table = 'tbAdvertisement';

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

    function get_advertisement ()
    {
        $this->db->select('image, link');
        return $this->db->get($this->table)->row_array();
    }
}