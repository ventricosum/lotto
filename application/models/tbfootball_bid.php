<?php

class tbFootball_bid extends DataMapper {

    var $table = 'tbFootball_bid';

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
	 * check winner
	 */
	static function check_winner($match_id=-1)
	{
		$ci =& get_instance();
		$ci->load->library('pushplus/lbpush_plus');
		$match = new tbMatches(array('id'=>$match_id));
		if ($match->exists())
		{
			$credential_mode_1 = json_decode($match->credential_mode_1);
			$credential_mode_2 = json_decode($match->credential_mode_2);
			$result = explode("-", $match->result);
			
			// get all bids for this match
			$bids = new tbFootball_bid(array('match_id'=>$match->id));
			foreach ($bids as $bid) 
			{
				if ($bid->status != 0) continue;
				// mark this bid as won
				$bid->status = 1;
				$bid->save();
				// switch mode
				$prize = 0;
				switch ($bid->mode) {
					case 1:
						if (($bid->selection == 0) && ($result[0] > $result[1]))
						{
							// win
							$prize = $credential_mode_1->hteam_win * $bid->bid_money;
						} 
						elseif (($bid->selection == 1) && ($result[0] == $result[1]))
						{
							// win
							$prize = $credential_mode_1->draw * $bid->bid_money;
						}
						elseif (($bid->selection == 2) && ($result[0] < $result[1]))
						{
							// win
							$prize = $credential_mode_1->vteam_win * $bid->bid_money;
						}
						break;
					case 2:
						$total_goals = $result[0] + $result[1] - $credential_mode_2->balance;
						$switch_index = (int) ($bid->selection == 0);// small
						if ($total_goals > $credential_mode_2->big)
						{
							// -+100%
							$prize = 2 * (1 - $switch_index) * $bid->bid_money;
						}
						else if ($total_goals == $credential_mode_2->big)
						{
							// -+50%
							$prize = (1.5 - $switch_index) * $bid->bid_money;
							if ($switch_index == 1)
							{
								// user still lose 50% bet amount
								$force_no_add = true;
							}
						}
						else if ($total_goals == $credential_mode_2->small)
						{
							// +-50%
							$prize = (0.5 + $switch_index) * $bid->bid_money;
							if ($switch_index == 0)
							{
								// user still lose 50% bet amount
								$force_no_add = true;
							}
						}
						else if ($total_goals < $credential_mode_2->small)
						{
							// +-100%
							$prize = 2 * $switch_index * $bid->bid_money;
						}
						break;
					case 3:
						$total_goals = $result[0] + $result[1];
						if (
							( ($total_goals & 1) && ($bid->selection == 0) ) ||//odd
							(!($total_goals & 1) && ($bid->selection == 1))    //even
						)
						{
						  	$prize = 2*$bid->bid_money;
						} 
						break;
					default:
						break;
				}
				if ($prize > 0)
				{
					tbUser::change_account_balance($prize, $bid->user_id);
					//
					$win_history = new tbWin_history(array('bid_id' => $bid->id, 'bid_type' => 0));
					$current_prize = (real) @$win_history->prize;
			        $win_history->prize = $current_prize + $prize;
					$win_history->bid_type = 0;
					$win_history->bid_id = $bid->id;
			        $win_history->save();
					// push
			        $dollar = money_format("$%i", $current_prize + $prize);
			        $ci->lbpush_plus->pushByOwners($bid->user_id, "Congratulations! You've won a prize of $dollar");
					continue;
				}
				// mark this bid as won
				$bid->status = -1;
				$bid->save();
			}
		}
	}

    function add2($match_id, $user_id, $bet, $mode, $selection) {
        $data['match_id'] = $match_id;
        $data['user_id'] = $user_id;
        $data['bid_money'] = $bet;
        $data['mode'] = $mode;
        $data['selection'] = $selection;
        $this->db->insert($this->table,$data);
        return $this->db->insert_id();
    }

	static function get_football_bid_history ($user_id, $offset, $limit, $filter)
	{
		$ci =& get_instance();
		$ci->db->select("matches.starttime, matches.ondate, bid.createtime, matches.hteam, matches.vteam, matches.status, matches.result, league.name, league.description, bid.bid_money, win.prize, bid.user_id");
		$ci->db->from("tbFootball_bid bid");
		$ci->db->join("tbMatches matches", "bid.match_id = matches.id", "inner");
		$ci->db->join("tbWin_history win", "win.bid_id = bid.id and win.bid_type = 0", "left");
		$ci->db->join("tbLeagues league", "league.id = matches.league", "left");
		$ci->db->where("user_id", $user_id);
		if ($filter == 1)
		{
			$ci->db->where("matches.status", 1);// end	
		}
		elseif ($filter == 2) 
		{
			$ci->db->where("matches.status <>", 1);// end
		}
		$ci->db->order_by("createtime", "desc");
		$ci->db->limit($limit, $offset);
		return $ci->db->get()->result();
	}
	/**
	 * 
	 */
	static function get_summary_for_football_bid_history ($user_id, $filter)
	{
		$ci =& get_instance();
		$ci->db->select("sum(win.prize) as summary", FALSE);
		$ci->db->from("tbFootball_bid bid");
		$ci->db->join("tbMatches matches", "bid.match_id = matches.id", "inner");
		$ci->db->join("tbWin_history win", "win.bid_id = bid.id and win.bid_type = 0", "left");
		$ci->db->join("tbLeagues league", "league.id = matches.league", "left");
		$ci->db->where("user_id", $user_id);
		if ($filter == 1)
		{
			$ci->db->where("matches.status", 1);// end
		}
		elseif ($filter == 2)
		{
			$ci->db->where("matches.status <>", 1);// end
		}
		$rsl = $ci->db->get()->result();
		return floatval($rsl[0]->summary);
	}

    function get_bid_users($match_id) {
        $this->db->select("id, user_id, bid_money");
        $this->db->where('match_id', $match_id);
        return $this->db->get($this->table)->result_array();
    }

}