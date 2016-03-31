<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');

require './application/modules/servicedevelopment/libraries/REST_controller.php';

class Services extends REST_Controller {
	/*
	 * 已结束: closed
	 * 未开赛: not start
	 * 进行中: in progress
	 * hteam: c4T1
	 * vteam: c4T2
	 * "error_code":0 -> success
	 * "reason":"联赛名称错误": League name error
	 */
    var $value_per_bet = 1;
    var $lotto_types = array(
        8 => array('prize' => array(1 => 1.1,1.2,1.3,1.4)),
        9 => array('prize' => array(1 => 1.5,1.6,1.7)),
        10 => array('prize' => array(1 => 1.8,1.9,2,2.1,2.2)),
        11 => array('prize' => array(1 => 1.2,1.3,1.4))
    );

	public function __construct() {
		parent::__construct();
	}

	/**
	 * validation
	 * @access protected
	 */
	protected function required($required = array()) {
		foreach ($required as $key => $value) {
			if (strlen(trim($value)) == 0) {
				throw new Exception("Missing params", 401);
			}
		}
	}

    private function add_profit_to_upline($downline_id, $bid_money) {
        $this->load->library('plustree/hierarchy');
        $all_uplines = $this->hierarchy->retrieve_single_path ($downline_id);
        $tbinterest = new tbTree_interest();
        $level = 5;
        foreach($all_uplines as $k => $v) {
            if($level == 0) {
                break;
            }
            if($v->id != $downline_id) {
                $interest_rate = $tbinterest->get_interest_by_level($level);
                $profit = $interest_rate['interest'] * $bid_money;
                $upline_id = $v->id;
                tbUser::change_account_balance($profit, $upline_id);
                tbProfit_downline::add2($downline_id, $upline_id, $profit);
                $level--;
            }
        }
    }

	/**
	 * get 3 latest finished matches based on updatetime
	 * get_latest_finished_matches ()
	 * @method post
	 * @param void
	 * @access public
	 * @return void
	 */
	function get_latest_finished_matches() {
		try {
			$matches = tbMatches::get_latest_finished_matches();

			$dataset = array();
			foreach ($matches as $match) {
				$dataset[] = array(
					"hteam" => $match -> hteam, 
					"vteam" => $match -> vteam, 
					"starttime" => balance_timezone($match -> ondate . " " . $match -> starttime), 
					"ondate" => balance_timezone($match -> ondate . " " . $match -> starttime), 
					"result" => $match -> result,
					"ontime" => balance_timezone($match -> ondate . " " . $match -> starttime)
				);
			}
			$this -> response($dataset, 200);
		} catch (Exception $e) {
			$this -> response("", $e -> getCode());
		}
	}

	/**
	 * get_football_matches ()
	 * handle filter also
	 * @method post
	 * @param void
	 * @access public
	 * @return void
	 */
	function get_football_matches() {
		$token = (string)$this -> post('token');
		$type = (int)$this -> post('type');
		$offset = (int)$this -> post('offset');
		$limit = ($l = $this -> post('limit')) ? $l : 20;
		try {
			$user_id = 0;
			if ($type == 3) {
				$user = new tbUser( array("token" => $token), 403);
				$user_id = $user -> id;
			}
			$matches = tbMatches::get_football_matches($type, $offset, $limit, $user_id);

			$dataset = array();
			foreach ($matches as $match) {
				$m1 = json_decode($match -> credential_mode_1);
				$m2 = json_decode($match -> credential_mode_2);
				$dataset[] = array(
					"league_logo" => base_url($match -> league_logo), 
					"match_id" => $match -> match_id, 
					"hteam" => $match -> hteam, 
					"vteam" => $match -> vteam, 
					"starttime" => $match -> starttime, 
					"status" => $match -> status, 
					"ondate" => balance_timezone($match -> ondate . " " . $match -> starttime), 
					"result" => $match -> result, 
					"hteam_logo" => convert_logo($match -> logo_hteam), 
					"vteam_logo" => convert_logo($match -> logo_vteam), 
					"credential_mode_1" => isset($m1) ? $m1 : "", 
					"credential_mode_2" => isset($m2) ? $m2 : "",
					"ontime" => balance_timezone($match -> ondate . " " . $match -> starttime)
				);
			}
			$this -> response($dataset, 200);
		} catch (Exception $e) {
			$this -> response("", $e -> getCode());
		}
	}

	/**
	 * userinfo() - get / update userinfo
	 * @method get|post
	 * @access public
	 */
	public function userinfo() {
		try {
			$token = (string)$this -> get("token");
			if (strlen($token) > 0) {
				// get
				$this -> get_userinfo($token);
				return;
			}
			$token = (string)$this -> post("token");
			if (strlen($token) > 0) {
				// update
				$nick_name = (string)$this -> post('nick_name');
				$avatar = (int)$this -> post('avatar');

				$this -> required(array($nick_name));
				$this -> update_userinfo($token, $nick_name, $avatar);
				return;
			}
			$this -> response("", 403);
		} catch (Exception $e) {
			$this -> response("", $e -> getCode());
		}
	}

	/**
	 * get_userinfo()
	 * @param token
	 * @access protected
	 */
	protected function get_userinfo($token) {
		try {
			$user = new tbUser( array("token" => $token), 403);

			$dataset = tbUser::getprofile($user -> id);

			$dataset = $dataset[0];
			$dataset -> account_balance = money_format('%i', $dataset -> account_balance);

			$bank_info = json_decode($dataset -> bank_info);
			$thirdparty_info = json_decode($dataset -> thirdparty_info);

			$profile = array(
				'nick_name' => $dataset -> display_name, 
				'avatar' => array(
					'id' => (int) $dataset -> avatar_id, 
					'path' => convert_image($dataset -> avatar), 
				), 
				'total' => $dataset -> account_balance, 
				'bank_info' => isset($bank_info) ? $bank_info : "", 
				'thirdparty_info' => isset($thirdparty_info) ? $thirdparty_info : "", 
				'transaction_type' => isset($bank_info) ? 0 : (isset($thirdparty_info) ? 1 : 2),
				'pending' => $user -> get_pending(), 
				'available' => $user -> get_available(),
				'id' => $user->id
			);

			$this -> response($profile, 200);
		} catch (Exception $e) {
			$this -> response("", $e -> getCode());
		}
	}

	/**
	 * update_userinfo()
	 * @param token
	 * @param nick_name
	 * @param avatar
	 * @access protected
	 */
	protected function update_userinfo($token, $nick_name, $avatar) {
		try {
			$user = new tbUser( array("token" => $token), 403);

			$user -> display_name = $nick_name;
			if ($avatar > 0) {
				$user -> avatar = $avatar;
			}
			$user -> save();

			$this -> get_userinfo($token);
		} catch (Exception $e) {
			$this -> response("", $e -> getCode());
		}
	}

	/**
	 * bid_match ()
	 * @access public
	 * @method post
	 * @param token
	 */
	function bid_match() {
		$token = (string) $this -> post('token');
		$match_id = (int) $this -> post('match_id');
		$mode = (int) $this -> post('mode');
		$selection = (int) $this -> post('selection');
		$bid_money = (real) $this -> post('bid_money');
		try {
			$user = new tbUser( array("token" => $token), 403);
			$match = new tbMatches( array("id" => $match_id), 1);

			if (!in_array($mode, array(1, 2, 3))) {
				throw new Exception("Invalid football bet mode", 7);
			}
			if ($match -> status != 0) {
				throw new Exception("Can't bid", 2);
			}
			if ($bid_money < 0) {
				throw new Exception("bid money cannot be smaller than 0", 4);
			}
			if ($user -> get_available() < $bid_money) {
				throw new Exception("Your account balance is smaller than your bid money", 3);
			}
            $football_bid = new tbFootball_bid();
            $bid_id = $football_bid->add2($match_id, $user -> id, $bid_money, $mode, $selection);
            tbWin_history::add(-$bid_money, $bid_id, 0);
			//tbProfit_downline::add($user -> id, $bid_money);
            $this->add_profit_to_upline($user->id, $bid_money);
			// subtract account balance
			tbUser::change_account_balance(-1 * $bid_money, $user -> id);

			$this -> response("", 200);
		} catch (Exception $e) {
			$this -> response("", $e -> getCode());
		}
	}

	/**
	 * get_banks_list ()
	 * @access public
	 * @method get
	 * @param token
	 */
	function get_banks_list() {
		try {
			$banks = new tbBank();
			$banks -> get();

			$dataset = array();
			foreach ($banks as $bank) {
				$dataset[] = $bank -> to_array(array('id', 'name'));
			}

			$this -> response($dataset, 200);
		} catch (Exception $e) {
			$this -> response("", $e -> getCode());
		}
	}

	/**
	 * get_thirdparty_payments_list ()
	 * @access public
	 * @method get
	 * @param token
	 */
	function get_thirdparty_payments_list() {
		try {
			$tps = new tbThirdparty_payment();
			$tps -> get();

			$dataset = array();
			foreach ($tps as $tp) {
				$dataset[] = $tp -> to_array(array('id', 'name'));
			}

			$this -> response($dataset, 200);
		} catch (Exception $e) {
			$this -> response("", $e -> getCode());
		}
	}

	/**
	 * reload()
	 * @access public
	 * @method post
	 * @param token
	 * @param code
	 * @return void
	 */
	function reload() {
		$token = (string)$this -> post('token');
		$code = (string)$this -> post('code');
		try {
			$user = new tbUser( array("token" => $token), 403);
			$code = new tbCode( array("code" => $code, 'status' => 0), 5);

			$new_balance = $user -> account_balance + $code -> value;
			$user -> account_balance = $new_balance;
			$user -> save();

			$code -> status = 1;
			$code -> save();

			tbTransaction::add($user -> id, 0, $code -> value, 1);
			//user_id, type, amount, status

			$this -> response("", 200);
		} catch (Exception $e) {
			$this -> response("", $e -> getCode());
		}
	}

	/**
	 * withdraw()
	 * @access public
	 * @method post
	 * @param token
	 * @param username
	 * @param email
	 * @param type: 0: bank, 1: third_party
	 * @param choice: id of bank or thirdparty payment
	 * @param amount
	 * @return void
	 */
	function withdraw() {
		$token = (string)$this -> post('token');
		$type = (int)$this -> post('type');
		$amount = (real)$this -> post('amount');
		$need_to_save = (int)(boolean)$this -> post('need_to_save');
		$info = (string)$this -> post('info');
		try {
			$user = new tbUser( array("token" => $token), 403);

			if ($amount > $user -> get_available()) {
				throw new Exception("Your account is not enough for this withdrawal", 6);
			} 
			if ($need_to_save == 1) {
				if ($type == 0) {
					// bank
					$user -> bank_info = $info;
					$user -> thirdparty_info = "";
				} else {
					// thirdparty
					$user -> thirdparty_info = $info;
					$user -> bank_info = "";
				}
				$user -> save();
			}
			tbTransaction::add($user -> id, 1, $amount, 0, $type, $info);
			$this -> response("", 200);
		} catch (Exception $e) {
			$this -> response("", $e -> getCode());
		}
	}

	/**
	 * get_history_transaction ()
	 * @method get
	 * @param token
	 * @param offset
	 * @param limit
	 * @return json
	 * @access public
	 */
	function get_transactions_history() {
		$token = ($this -> get('token')) ? $this -> get('token') : "-1";
		$offset = (int)$this -> get('offset');
		$limit = ($l = $this -> get('limit')) ? $l : 20;
		try {
			$user = new tbUser( array("token" => $token), 403);

			$transactions = tbTransaction::get_history_transactions($user -> id, $offset, $limit);

			$dataset = array();
			foreach ($transactions as $tran) {
				$dataset[] = array(
					"type" => $tran -> type, 
					"amount" => $tran -> amount, 
					"createtime" => balance_timezone($tran -> createtime)
				);
			}
			$this -> response($dataset, 200);
		} catch (Exception $e) {
			$this -> response("", $e -> getCode());
		}
	}

	/**
	 * get_football_bid_history ()
	 * @method get
	 * @param token
	 * @param offset
	 * @param limit
	 * @param filter 0: all| 1: finished | 2: not finished
	 * @return json
	 * @access public
	 */
	function get_football_bid_history() {
		$token = ($this -> get('token')) ? $this -> get('token') : "-1";
		$offset = (int)$this -> get('offset');
		$limit = ($l = $this -> get('limit')) ? $l : 20;
		$filter = (int)$this -> get('filter');
		try {
			$user = new tbUser( array("token" => $token), 403);

			$bids = tbFootball_bid::get_football_bid_history($user -> id, $offset, $limit, $filter);
			$summary = tbFootball_bid::get_summary_for_football_bid_history($user -> id, $filter);

			$dataset = array();
			foreach ($bids as $bid) {
				$dataset[] = array(
					"starttime" => balance_timezone($bid -> ondate . " " . $bid -> starttime), 
					"ondate" => balance_timezone($bid -> ondate . " " . $bid -> starttime), 
					"ontime" => balance_timezone($bid -> ondate . " " . $bid -> starttime),
					"createtime" => $bid -> createtime, 
					"hteam" => $bid -> hteam, 
					"vteam" => $bid -> vteam, 
					"result" => $bid -> result, 
					"match_status" => $bid -> status, 
					"league_name" => $bid -> name, 
					"league_description" => $bid -> description, 
					"bid_money" => $bid -> bid_money, 
					"prize" => floatval($bid -> prize), 
				);
			}
			$this -> response(array("history" => $dataset, "summary" => $summary), 200);
		} catch (Exception $e) {
			$this -> response("", $e -> getCode());
		}
	}

	function get_lotto_bid_history() {
		$token = ($this -> get('token')) ? $this -> get('token') : "-1";
        $status = (int) $this->get('filter');
		$offset = (int)$this -> get('offset');
		$limit = ($l = $this -> get('limit')) ? $l : 20;
		try {
			$user = new tbUser( array("token" => $token), 403);
			$lotto_bid = new tbLotto_bid();
			$history = $lotto_bid->get_history($user -> id, $status, $offset, $limit);
            foreach($history as $k=>$v) {
                $history[$k]['createtime'] = balance_timezone($history[$k]['createtime']);
            }
            $summary = $lotto_bid->get_summary($user -> id, $status);

            $total = 0;
            foreach($summary as $value) {
                $total += $value['prize'];
            }

            $this -> response(array('history' => $history, 'summary' => $total), 200);
		} catch (Exception $e) {
			$this -> response("", $e -> getCode());
		}
	}

	function get_lotto_type() {
        try {
            $lotto = new tbLotto();
            $round = new tbRound();
            $mode = new tbLotto_modes();
            $this->load->model('plusgallery/lbgallery_image');
            $lotto_types = $lotto -> get_lotto();
            foreach ($lotto_types as $key => $val) {
                $latest_round = $round -> get_latest_round($val['lotto_id']);
                if ($latest_round) {
                    $lotto_types[$key]['logo'] = $this->lbgallery_image->showbyID($lotto_types[$key]['logo'],0,0,1);
                    $lotto_types[$key]['last_round']['round_number'] = $latest_round['round_number'];
                    $lotto_types[$key]['last_round']['endtime'] = balance_timezone($latest_round['endtime']);
                    $lotto_types[$key]['last_round']['result'] = $latest_round['result'];
                    if($val['lotto_id'] == 8) {
                        $separate = explode(',',$latest_round['result']);
                        $lotto_types[$key]['last_round']['result'] = $separate[0];
                    }
                }
                $modes = $mode -> get_all_lotto_modes2($val['lotto_id']);
                if($modes) {
                    $lotto_types[$key]['mode'] = $modes;
                } else {
                    $lotto_types[$key]['mode'] = array();
                }

                if(in_array($val['lotto_id'], array(8,9,10,11))) {
                    $prize = $this->lotto_types;
                    $lotto_types[$key]['prize_rate'] = $prize[$val['lotto_id']]['prize'];
                }
            }
            $this -> response($lotto_types, 200);
        } catch(Exception $e) {
            $this -> response("", $e -> getCode());
        }
	}

	function add_lotto_bid() {
		$token = (string)$this -> post('token');
		$bet = (int)$this -> post('bet');
		$round_id = (int)$this -> post('round_id');
		$selection = (string)$this -> post('selection');
		$mode = (int)$this -> post('mode');
		try {
			$user = new tbUser( array('token' => $token), 403);
            $user_id = $user -> id;
            $bid_money = 0;
            if($bet != -1) {
                $bid_money = $bet * $this->value_per_bet;
            } else {
                $choices = json_decode($selection);
                if(is_array($choices)) {
                    foreach($choices as $choice) {
                        $bid_money += $choice->chip;
                    }
                }
            }
            if($bid_money == 0) {
                $this -> response("", 4);
                return;
            }
            if ($user -> get_available() >= $bid_money) {
                $bid_id = tbLotto_bid::add($round_id, $user_id, $bid_money, $mode, $selection);
                tbWin_history::add(-$bid_money, $bid_id, 1);

                $user -> account_balance -= $bid_money;
                $user -> save();
                //tbProfit_downline::add($user_id, $bid_money);
                $this->add_profit_to_upline($user_id, $bid_money);
                $this -> response("", 200);
            } else {
                $this -> response("", 3);
            }
		} catch(Exception $e) {
			$this -> response("", $e -> getCode());
		}
	}

    function add_lotto_bid2() {
        $bet = -1;
        $round_id = 127967;
        $selection =   '[{"chip":"500", "selection":"1"},
                        {"chip":"1000", "selection":"2"},
                        {"chip":"10","lastName":"3"}]';
        $mode = "";
        try {
            $user = new tbUser( array('id' => 75), 403);
            $user_id = 75;
            $bid_money = 0;
            if($bet != -1) {
                $bid_money = $bet * $this->value_per_bet;
            } else {
                $choices = json_decode($selection);
                if(is_array($choices)) {
                    foreach($choices as $choice) {
                        $bid_money += $choice->chip;
                    }
                }
            }
            if($bid_money == 0) {
                $this -> response("", 4);
            }
            if ($user -> get_available() >= $bid_money) {
                $bid_id = tbLotto_bid::add($round_id, $user_id, $bid_money, $mode, $selection);
                tbWin_history::add(-$bid_money, $bid_id, 1);

                $user -> account_balance -= $bid_money;
                $user -> save();
                //tbProfit_downline::add($user_id, $bid_money);
                $this->add_profit_to_upline($user_id, $bid_money);
                $this -> response("", 200);
            } else {
                $this -> response("", 3);
            }
        } catch(Exception $e) {
            $this -> response("", $e -> getCode());
        }
    }

	function get_current_round() {
		$token = (string)$this -> get('token');
		$lotto_id = (int)$this -> get('lotto_id');
		try {
			$user = new tbUser( array('token' => $token), 403);
			$round = new tbRound();
			$current_round = $round -> check_round_exist($lotto_id);
			if ($current_round) {
                $latest_round = $round -> get_latest_round($lotto_id);
                if($lotto_id == 8) {
                    $current_round['result'] = get_first_ball($latest_round['result']);
                } else {
                    $current_round['result'] = $latest_round['result'];
                }
				$current_round['current_time'] = balance_timezone();
                $current_round['starttime'] = balance_timezone($current_round['starttime']);
                $current_round['endtime'] = balance_timezone($current_round['endtime']);
				$this -> response($current_round, 200);
			} else {
				$this -> response("", 8);
			}
		} catch(Exception $e) {
			$this -> response("", $e -> getCode());
		}
	}

	function get_latest_winners() {
		$limit = ($l = $this -> get('limit')) ? $l : 10;
		try {
			$win_history = new tbWin_history();
			$winners = $win_history -> get_latest_winners($limit);
			$this -> response($winners, 200);
		} catch(Exception $e) {
			$this -> response("", $e -> getCode());
		}
	}

	function get_2_latest_rounds() {
		try {
			$this->load->model('plusgallery/lbgallery_image');
			//
			$round = new tbRound();
            $types = array(1,6,8);
            $data = array();
            foreach($types as $key => $type) {
                $data[$key] = $round -> get_latest_round($type);
                if($data[$key]) {
                	$data[$key]['logo'] = $this->lbgallery_image->showbyID($data[$key]['logo'],0,0,1);
                    if($type == 8) {
                        $data[$key]['result'] = explode(',',$data[$key]['result']);
                        $data[$key]['result'] = $data[$key]['result'][0];
                    }

					// get current round for countdown
                    $data[$key]['current_time'] = balance_timezone();
					
                    $current_round = $round -> check_round_exist($type);
					if ($current_round) {
		                $data[$key]['starttime'] = balance_timezone($current_round['starttime']);
		                $data[$key]['endtime'] = balance_timezone($current_round['endtime']);
					} else {
		                $data[$key]['starttime'] = "";
		                $data[$key]['endtime'] = "";
					}
                } else {
                    $this -> response("", 8);
                }
            }
			$this -> response($data, 200);
		} catch(Exception $e) {
			$this -> response("", $e -> getCode());
		}
	}

    function get_30_lastest_rounds() {
        $token = (string)$this -> get('token');
        $lotto_id = (int)$this -> get('lotto_id');
        try {
            $user = new tbUser( array('token' => $token), 403);
            $round = new tbRound();
            $data = $round -> get_rounds_by_lotto_id($lotto_id);
            $this -> response($data, 200);
        } catch(Exception $e) {
            $this -> response("", $e -> getCode());
        }
    }

    function get_lotto_mode() {
        $lotto_id = $this->get('lotto_id');
        $mode_id = $this->get('mode_id');
        try {
            $mode = new tbLotto_modes();
            $mode = $mode->get_lotto_modes($lotto_id, $mode_id);
            $this -> response($mode, 200);
        } catch(Exception $e) {
            $this -> response("", $e -> getCode());
        }
    }

    function get_advertisement() {
        try {
            $ads = new tbAdvertisement();
            $data = $ads->get_advertisement();
            $data['image'] = base_url($data['image']);
            $this -> response($data, 200);
        } catch(Exception $e) {
            $this -> response("", $e -> getCode());
        }
    }

}
