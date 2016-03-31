<?php

class tbMatches extends DataMapper {

    var $table = 'tbMatches';

    function __construct($init = array(), $code_to_throw = FALSE) {
        parent::__construct();
        if (is_array($init) && (count($init) > 0)) {
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
	 * check if a match exist
	 */
	function does_match_exist ($hteam, $vteam, $starttime, $ondate, $league, $result = null, $status)
	{
		$this->where("hteam", $hteam);
		$this->where("vteam", $vteam);
		$this->where("starttime", $starttime);
		$this->where("ondate", $ondate);
		$this->where("league", $league);
		$this->where("status <>", 1);
		
		$this->get();
		if ($this->exists())
		{
			// match existed
			// check whether match ended
			if (isset($result) && ($status == "已结束"))
			{
				//已结束: closed
				// match ended
				$this->status = 1;
				$this->result = $result;
				
				$this->save();
				// check winner here
				// 1st get all bids for this match
				// 
				tbFootball_bid::check_winner($this->id);				
			}
			
			if ($status == "进行中")
			{
				//已结束: in progress
				$this->status = 2;
				$this->result = $result;
				
				$this->save();
			}
			
		}
		else
		{
			if ($status != "已结束")
			{
				
				$this->hteam = $hteam;
				$this->vteam = $vteam;
				$this->starttime = $starttime;
				$this->ondate = $ondate;
				$this->league = $league;
				$this->status = 0;
				$this->credential_mode_1 = json_encode(
					array(
						"draw" => 1.5,
						"hteam_win" => 1.5,
						"vteam_win" => 1.5,
					)
				);
				$this->credential_mode_2 = json_encode(
					array(
						"small" => 1,
						"big" => 1.5,
						"balance" => 0,
					)
				);
				
				$this->save();
			}
			// else
			// {
				// $this->hteam = $hteam;
				// $this->vteam = $vteam;
				// $this->starttime = $starttime;
				// $this->ondate = $ondate;
				// $this->league = $league;
				// $this->status = 1;
				// $this->result = $result;
// 				
				// $this->save();
			// }
		}
	}
	/**
	 * 
	 */
	static function get_latest_finished_matches ()
	{
		$ci =& get_instance();
		$sql = 
		   "select matches.*, leagues.name as league_name, leagues.description as league_description
			from
			(
				SELECT *, concat(ondate, ' ', starttime) as ontime FROM `tbMatches` where status = 1 order by ontime desc limit 3
			) matches
			left join 
			tbLeagues leagues
			on matches.league = leagues.id";
		return $ci->db->query($sql)->result();
	}
	/**
	 * 
	 */
	static function get_football_matches ($type = 0, $offset, $limit, $user_id = 0)
	{
		$filter = "";
		$sort = "desc";
		switch ($type) {
			case 1:
				$filter = " where status = 0 ";
				$sort = "asc";
				break;
			case 2:
				$filter = " where status = 1 ";
				$sort = "desc";
				break;
			case 3:
				$filter = " where id in (SELECT matches.id
						FROM `tbFootball_bid` bid 
						left join tbMatches matches on bid.match_id = matches.id 
						where bid.user_id = {$user_id}
						group by id)";
				$sort = "desc";	
				break;
			default:
				
				break;
		}
		$ci =& get_instance();
		$sql = "select matches.*, matches.id as match_id, leagues.name as league_name, leagues.description as league_description, 
				image_hteam.big_image as logo_hteam, leagues.logo as league_logo,
				image_vteam.big_image as logo_vteam, matches.credential_mode_1, matches.credential_mode_2
				from
				(
			        SELECT *, concat(ondate, ' ', starttime) as ontime FROM `tbMatches` {$filter} order by ontime {$sort} limit {$offset}, {$limit}
				) matches
				left join 
				tbLeagues leagues
				on matches.league = leagues.id
				left join tbFootball_club club_hteam
				on club_hteam.name = matches.hteam
				left join tbImage image_hteam
				on image_hteam.id = club_hteam.logo
				left join tbFootball_club club_vteam
				on club_vteam.name = matches.vteam
				left join tbImage image_vteam
				on image_vteam.id = club_vteam.logo";
		return $ci->db->query($sql)->result();
	}
	/**
	 * 
	 */
	static function get_unfinished_matches ()
	{
		$ci =& get_instance();
		$sql = "select *
				from
				(
					SELECT *, concat(ondate, ' ', starttime) as ontime FROM `tbMatches` 
				) matches
				where matches.ontime < now() - INTERVAL 90 MINUTE and status != 1
				ORDER BY `matches`.`ontime`  DESC";
		return $ci->db->query($sql)->result();		
	}
}
