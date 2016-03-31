<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Football extends CI_Controller {
	/*
	 * 已结束: closed
	 * 未开赛: not start
	 * 进行中: in progress
	 * hteam: c4T1
	 * vteam: c4T2
	 * "error_code":0 -> success
	 * "reason":"联赛名称错误": League name error
	 */
	private $data = array();
    public function __construct()
	{
		parent::__construct();
		$this -> data['title'] = LocalizedString("Football");
	}
	/**
	 * cronjob
	 * get_matches () - get matches from api and save new ones to database
	 * @access public
	 * @method void
	 * @param void
	 */
	function get_matches ()
	{
		$leagues = tbLeagues::get_all_as_list();
		foreach ($leagues as $league) 
		{
			$league_id = $league["id"];
			$dataset = json_decode($this->connect_to_football_api($league["name"]));
			if ($dataset->error_code == 0)// success
			{
				$saicheng1 = $dataset->result->views->saicheng1;
				$saicheng2 = $dataset->result->views->saicheng2;
				
				$matches = array_merge($saicheng1, $saicheng2);
				// loop through dataset 
				foreach ($matches as $match) 
				{
					// check if we can get result for this match
					$check = json_decode($this->connect_to_combat_api($match->c4T1, $match->c4T2));	
					if ($check->error_code == 0)
					{
						$m = new tbMatches();
						$date = $this->guess_date(substr($match->c2, 0, 5), $match->c1);
						$m->does_match_exist($match->c4T1, $match->c4T2, $match->c3, $date, $league_id, @$match->c4R, $match->c1);
					}
				} 
			}
			else 
			{
				if ($dataset->reason == "联赛名称错误")
				{
					echo "league name error: " . $league["name"];
				}		
			}
		}
		echo "get_matches";
	}
	/**
	 * get_logo ()
	 */
	function get_logo ()
	{
		$teams = tbFootball_club::get_all_teams();
		
		foreach ($teams as $team) 
		{
			$team_info = json_decode($this->connect_to_team_api($team->team));
			if ($team_info->error_code == 0)
			{
				$sample = $team_info->result->list[0];
				$logo_link = "";
				if ($sample->c4T1 == $team->team)
				{
					$logo_link = $sample->c4T1URL;
				}
				else
				{
					$logo_link = $sample->c4T2URL;
				}
				$this->save_logo($logo_link, $team->team);
			}
		}
		
		$matches = new tbMatches();
		$matches->get();
		
		foreach ($matches as $match) 
		{
			$dataset = json_decode($this->connect_to_combat_api($match->hteam, $match->vteam));	
			if ($dataset->error_code == 0)
			{
				$sample = $dataset->result->list[0];
				$this->save_logo($sample->team1url, $sample->team1);
				$this->save_logo($sample->team2url, $sample->team2);
			}
		}
	}
	protected function save_logo ($logo_link, $team_name)
	{
		$this->load->library('plusgallery/lbgallery');
		$prefix = "http://www.sinaimg.cn/lf/sports/logo85/";
		$regex = '/id=(?P<digit>\d+)/';
		preg_match($regex, $logo_link, $matches);
		if (isset($matches['digit']))
		{
			$url = $prefix . $matches['digit'] . ".png"; 
			$img_id = $this->lbgallery->upimgbyurl($url);
			
			$t = new tbFootball_club(array("name"=>$team_name));
			$t->logo = $img_id;
			$t->name = $team_name;
			$t->save();
		}
	}
	/**
	 * connect to combat api
	 * @access protected
	 * @method void
	 */
	function connect_to_combat_api ($hteam, $vteam)
	{
		// connect to server to get all dependences
		$url = "http://op.juhe.cn/onebox/football/combat?key=" . FOOTBALL_API_KEY . "&hteam={$hteam}&vteam={$vteam}";
		//open connection
		$ch = curl_init();
		
		//set the url, number of POST vars, POST data
		curl_setopt($ch,CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		//execute post
		$result = curl_exec($ch);
		//close connection
		curl_close($ch); 
		return $result;
	}
	/**
	 * connect to api
	 * @access protected
	 * @method void
	 */
	function connect_to_football_api ($league_name)
	{
		// connect to server to get all dependences
		$url = "http://op.juhe.cn/onebox/football/league?key=" . FOOTBALL_API_KEY . "&league={$league_name}";
		//open connection
		$ch = curl_init();
		
		//set the url, number of POST vars, POST data
		curl_setopt($ch,CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		//execute post
		$result = curl_exec($ch);
		//close connection
		curl_close($ch); 
		return $result;
	}
	/**
	 * Connect to team api
	 * @access protected
	 */
	protected function connect_to_team_api ($team_name)
	{
		// connect to server to get all dependences
		$url = "http://op.juhe.cn/onebox/football/team?key=" . FOOTBALL_API_KEY . "&team={$team_name}"; 
		
		//open connection
		$ch = curl_init();
		
		//set the url, number of POST vars, POST data
		curl_setopt($ch,CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		//execute post
		$result = curl_exec($ch);
		//close connection
		curl_close($ch); 
		return $result;
	}
	/**
	 * @access protected
	 */
	protected function guess_date ($month_day, $match_status)
	{
		$current = strtotime(date("Y-m-d"));
		$guess_date = date('Y') . "-" . $month_day;
		$year = date('Y');
		if (($match_status == "未开赛") && (strtotime($current) > strtotime($guess_date)))
		{
			$year = (intval(date('Y')) + 1) ;
		}
		if (($match_status == "已结束") && (strtotime($current) > strtotime($guess_date)))
		{
			$year = (intval(date('Y')) - 1) ;
		}
		return $year . '-' . $month_day;
	}
	/**
	 * index () - table loads all matches
	 * @access public
	 */
	function index ()
	{
		$this -> data['user'] = checklogin();
		$this->data['navigate'] = navigate(1, -1);
		$this->data['contents']['col-sm-12'] = $this->get_table_football_matches();

		$this -> load -> view('element/adminLTE', $this->data);
	}
	/**
	 * using tablemanagement
	 * @access protected
	 */
	protected function get_table_football_matches ()
	{
		$this->load->library('tablemanagement/table_management');
	    $content = $this->table_management->getTable('all-football-matches');
	    return load("tablemanagement/libre_elements/index", array('content' => $content), TRUE);
	}
	/**
	 * config_payrate ()
	 * @method get
	 * @param id: match id
	 * @access public
	 */
	function config_payrate ()
	{
		$id = (int) $this->input->get('id');
		try {
			$match = new tbMatches(array("id"=>$id));
			$dataset = array(
				"match_id" => $id,
				"hteam" => $match->hteam,
				"vteam" => $match->vteam,
				"credential_mode_1" => json_decode($match->credential_mode_1),
				"credential_mode_2" => json_decode($match->credential_mode_2)
			);
			$this -> data["content"] = load("config_payrate", $dataset, TRUE);
			load("element/modal", $this -> data);
		} catch (Exception $e) {
			echo $e->getMessage();	
		}
	}
	/**
	 * handle_save_payrate () 
	 * @method post
	 * @param 
	 * @access public
	 */
	function handle_save_payrate ()
	{
		$match_id = (int) $this->input->post('match_id');
		try {
			$match = new tbMatches(array("id"=>$match_id));
			if ($match->exists())
			{
				$credential_mode_1 = array(
					"draw" => floatval($this->input->post('draw')),
					"hteam_win" => floatval($this->input->post('hteam_win')),
					"vteam_win" => floatval($this->input->post('vteam_win')),
				);
				$match->credential_mode_1 = json_encode($credential_mode_1);
				// mode 2
				$credential_mode_2 = array(
					"small" => floatval($this->input->post('small')),
					"big" => floatval($this->input->post('big')),
					"balance" => floatval($this->input->post('balance')),
				);
				$match->credential_mode_2 = json_encode($credential_mode_2);
				$match->save();
				echo 'success';
			}
		} catch (Exception $e) {
			echo $e->getMessage();	
		}
	}
	/**
	 * cronjob - find result for unfinished matches
	 * @method void
	 * @access public
	 */
	function get_result_for_unfinished_matches ()
	{
		$matches = tbMatches::get_unfinished_matches();
		
		foreach ($matches as $match) 
		{
			$dataset = json_decode($this->connect_to_combat_api($match->hteam, $match->vteam));	
			if ($dataset->error_code == 0)
			{
				// list history match
				$data = $dataset->result->list;
				foreach ($data as $d) 
				{
					// check date and time
					if (	
						(substr($d->date, 0, 5) == date("m-d", strtotime($match->ontime))) &&
						($d->time == date("H:i", strtotime($match->ontime)))
						)
					{
						if (strpos($d->score, "-"))
						{
							$m = new tbMatches(array("id"=>$match->id));
							$m->status = 1;
							$m->result = $d->score;
							$m->save();
							tbFootball_bid::check_winner($match->id);
						}
					}
				}
			} else {
                $tbbid = new tbFootball_bid();
                $users = $tbbid->get_bid_users($match->id);
                foreach($users as $user) {
                    tbUser::change_account_balance($user['bid_money'], $user['user_id']);
                    $win_history = new tbWin_history(array("bid_id"=>$user['id'], "bid_type" => 0));
                    $win_history->delete();
                    $tbfootball_bid = new tbFootball_bid(array('match_id' => $match->id));
                    $tbfootball_bid->delete();
                }
                $m = new tbMatches(array("id"=>$match->id));
                $m->delete();
            }
		}
		echo 'done';
	}
}