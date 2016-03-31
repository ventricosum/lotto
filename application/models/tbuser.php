<?php

class tbUser extends DataMapper {

    var $table = 'plusauthentication_user';

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

    /*
     * getprofile() - including information and setting
     */
    function getprofile($userid = -1) 
    {
        $sql = "select 
                    user.*,
                    avatar.big_image as avatar, avatar.id as avatar_id
                from (
                	select * from plusauthentication_user where id = {$userid}
                ) 
                 user
                left join ( select id, big_image from tbImage) avatar on avatar.id = user.avatar";
        return $this->db->query($sql)->result();
    }
	/**
	 * get_pending
	 */
	function get_pending ()
	{
		$userid = $this->id;
		$sql = "select coalesce(sum(amount), 0) as amount from tbTransaction where status = 0 and type = 1 and user_id = {$userid}";
		$rsl = $this->db->query($sql)->result();
		return $rsl[0]->amount;
	}
	/**
	 * get_available
	 */
	function get_available ()
	{
		$userid = $this->id;
		$pending = $this->get_pending($userid);
		return $this->account_balance - $pending;
	}
	/**
	 * change account balance
	 */
	static function change_account_balance ($amount = 0, $user_id = -1)
	{
		$user = new tbUser(array('id'=>$user_id));
		if ($user->exists())
		{
			$new_balance = $user->account_balance + $amount;
			$user->account_balance = $new_balance;
			$user->save();
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
        $upline = new tbUser();
        $upline->where('username', $username);
        $upline->where('password', $password);
		$upline->where('is_upline', 1);
        $upline->get();
        if ($upline->exists()) {
            return $upline;
        } else {
            return FALSE;
        }
    }
}
