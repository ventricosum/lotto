<?php

class tbAdmin extends DataMapper {

    var $table = 'tbAdmin';

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
	 * login() - check login credential and save last action
	 * @method void
	 * @param username
	 * @param password
	 * @access static
	 */
	static function login ($username, $password)
    {
        $admin = new tbAdmin();
        $admin->where('username', $username);
        $admin->where('password', $password);
        $admin->get();
        if ($admin->exists()) {
			$admin->lastaction = date('Y-m-d H:i:s');
			$admin->save();
			
            return $admin;
        } else {
            return FALSE;
        }
    }
	
	
}