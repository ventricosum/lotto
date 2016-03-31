<?php

class tbSetting extends DataMapper {

    var $table = 'tbSetting';

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
	
	static function get_setting ($keyname = "")
	{
		$setting = new tbSetting();
		$setting->where('keyname', $keyname);
		$setting->get();
		if ($setting->exists())
		{
			return $setting->content;
		}
		return "";
	}
}