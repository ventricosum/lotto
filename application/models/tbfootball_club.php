<?php

class tbFootball_club extends DataMapper {

    var $table = 'tbFootball_club';

    function __construct($init = array(), $code_to_throw = FALSE) {
        parent::__construct();
        if (count($init) > 0) {
            foreach ($init as $key => $value) 
            {
                $this->where($key, $value);
            }
            $this->get();
            if (!$this->exists() && ($code_to_throw !== FALSE)) 
            {
                throw new Exception("", $code_to_throw);
            }
            return $this;
        }
    }
	
	static function get_all_teams ()
	{
		$ci =& get_instance();
		$sql = "SELECT hteam as team FROM `tbMatches`
				UNION 
				SELECT vteam as team FROM `tbMatches`
				group by team";
		return $ci->db->query($sql)->result();
	}
}
// select *
// from (
	// SELECT hteam as team FROM `tbMatches`
				// UNION 
				// SELECT vteam as team FROM `tbMatches`
				// group by team
// ) teams
// left join tbFootball_club club
// on teams.team = club.name
// where id is null
//                                 