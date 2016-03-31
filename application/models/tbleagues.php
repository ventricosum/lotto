<?php

class tbLeagues extends DataMapper {

    var $table = 'tbLeagues';

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
	/**
	 * get_all()
	 * @param void
	 * @access static
	 */
	static function get_all_as_list ()
	{
		$leagues = new tbLeagues();
		$leagues->get();
		
		$dataset = array();
		
		foreach ($leagues as $league) 
		{
			$dataset[] = array(
				"id" => $league->id,
				"name" => $league->name
			); 
		}
		return $dataset;
	}
	
}