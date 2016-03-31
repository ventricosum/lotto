<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Lotto extends CI_Controller {
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
	// hei long happy 10
	var $lotto1 = "hljklsf";
	// guan dong happy
	var $lotto2 = "gdklsf";
	// chong qing shi shi
	var $lotto3 = "cqssc";
	// hei long shi shi
	var $lotto4 = "hljssc";
	// beijing racer 10
	var $lotto5 = "bjpks";
	// beijing happy 8
	var $lotto6 = "bjklb";
	// shan dong qun ying
	var $lotto7 = "sdqyh";

	var $token = "79A4A62B73CB9D0A9282E08BA70C5122";
	var $uid = "78803";

	var $value_per_bet = 1;
	var $lotto_types = array(
		1 => array('function' => 'check_lotto1_winners', 'starttime' => '08:45:00', 'duration' => '10 minutes', 'total_rounds' => 84, 'total_objects' => 8, 'name' => "hljklsf", 'modes' => array('guess_the_first' => array(1, 2), 'guess_any' => array(3 => 2, 6 => 3, 9 => 4, 10 => 5), 'guess_in_row' => array(4, 7), 'guess_in_position' => array(5 => 2, 8 => 3)), 'prize' => array("mode1" => 24, "mode2" => 8, "mode3" => 8, "mode4" => 31, "mode5" => 62, "mode6" => 24, "mode7" => 1300, "mode8" => 8000, "mode9" => 80, "mode10" => 320)), 
		2 => array('function' => 'check_lotto2_winners', 'starttime' => '08:45:00', 'duration' => '10 minutes', 'total_rounds' => 84, 'total_objects' => 8, 'name' => "gdklsf", 'modes' => array('guess_the_first' => array(1), 'guess_any' => array(2 => 1, 3 => 2, 6 => 3, 9 => 4, 10 => 5), 'guess_in_row' => array(4, 7), 'guess_in_position' => array(5 => 2, 8 => 3)), 'prize' => array("mode1" => 25, "mode2" => 5, "mode3" => 8, "mode4" => 31, "mode5" => 62, "mode6" => 24, "mode7" => 1300, "mode8" => 8000, "mode9" => 80, "mode10" => 320)), 
		3 => array('function' => 'check_lotto3_winners', 'starttime' => '00:00:00', 'duration' => '10 minutes', 'total_rounds' => 120, 'total_objects' => 5, 'name' => "cqssc", 'modes' => array('guess_the_last' => array(1), 'guess_any_last' => array(2 => 2, 4 => 3), 'guess_last_in_row' => array(3, 6, 7, 8), 'guess_some_in_last' => array(5 => array("guess" => 2, "total" => 3))), 'prize' => array("mode1" => 10, "mode2" => 50, "mode3" => 100, "mode4" => 160, "mode5" => 320, "mode6" => 1000, "mode7" => 10000, "mode8" => 100000)),
		5 => array('function' => 'check_lotto5_winners', 'starttime' => '09:05:00', 'duration' => '5 minutes', 'total_rounds' => 179, 'total_objects' => 10, 'name' => "bjpks", 'modes' => array('guess_first_in_row' => array(11,12,13)), 'prize' => array(1 => array(10), 2 => array(0,2,55), 3 => array(0,2,10,160), 4 => array(0,2,5,20,350), 5 => array(0,2,3,8,30,500), 6 => array(0,0,2,10,100,2000,10000), 7 => array(0,0,2,10,25,400,4500,20000), 8 => array(0,0,2,10,20,100,500,2000,40000), 9 => array(0,0,2,5,10,50,250,5000,10000,80000), 10 => array(2,0,0,0,0,0,0,0,0,0,888888), 11 => 90, 12 => 700, 13 => 5000)),
		6 => array('function' => 'check_lotto6_winners', 'starttime' => '09:05:00', 'duration' => '5 minutes', 'total_rounds' => 179, 'total_objects' => 80, 'name' => "bjklb", 'modes' => array(), 'prize' => array(1 => array(4), 2 => array(0,0,16), 3 => array(0,0,4,30), 4 => array(0,0,2,10,40), 5 => array(0,0,0,4,40,250), 6 => array(0,0,0,4,8,50,600), 7 => array(2,0,0,0,4,25,160,8000), 8 => array(2,0,0,0,4,10,40,700,20000), 9 => array(2,0,0,0,0,6,40,400,5000,50000), 10 => array(10,0,0,0,0,0,0,0,0,0,5000000))),
		7 => array('function' => 'check_lotto7_winners', 'starttime' => '09:10:00', 'duration' => '15 minutes', 'total_rounds' => 78, 'total_objects' => 5, 'name' => "sdqyh", 'modes' => array('guess_the_first' => array(1), 'guess_any_first' => array(4 => 2, 5 => 3, 6 => 4), 'guess_any' => array(7 => 1, 8 => 2, 9 => 3, 10 => 4, 11 => 5), 'guess_in_row' => array(2, 3)), 'prize' => array("mode1" => 26, "mode2" => 590, "mode3" => 12300, "mode4" => 300, "mode5" => 2000, "mode6" => 10000, "mode7" => 5, "mode8" => 30, "mode9" => 100, "mode10" => 1000, "mode11" => 10000)),
        8 => array('function' => 'check_lotto8_winners', 'starttime' => '08:45:00', 'duration' => '10 minutes', 'total_rounds' => 84, 'total_objects' => 8, 'name' => "gdklsf", 'modes' => array(), 'prize' => array(1 => 1.1,1.2,1.3,1.4)),
        9 => array('function' => 'check_lotto9_winners', 'starttime' => '00:00:00', 'duration' => '10 minutes', 'total_rounds' => 120, 'total_objects' => 5, 'name' => "cqssc", 'modes' => array(), 'prize' => array(1 => 1.5,1.6,1.7)),
        10 => array('function' => 'check_lotto10_winners', 'starttime' => '00:00:00', 'duration' => '10 minutes', 'total_rounds' => 120, 'total_objects' => 5, 'name' => "cqssc", 'modes' => array(), 'prize' => array(1 => 1.8,1.9,2,2.1,2.2)),
        11 => array('function' => 'check_lotto11_winners', 'starttime' => '00:00:00', 'duration' => '10 minutes', 'total_rounds' => 120, 'total_objects' => 5, 'name' => "cqssc", 'modes' => array(), 'prize' => array(1 => 1.2,1.3,1.4))
    );

	public function __construct() {
		parent::__construct();
		$this -> data['title'] = LocalizedString("Lotto");
	}

	/**
	 * cronjob
	 * generate rounds for the next following day
	 */

	function generate_rounds() {
        $round = new tbRound();
        $empty_rounds = $round -> get_last_day_empty_rounds();
        if($empty_rounds) {
            foreach($empty_rounds as $empty_round) {
                if($empty_round['lotto_id'] != 7) {
                    $round_id = $empty_round['round_id'];
                    $bid_users = $round -> get_bid_from_empty_round($round_id);
                    foreach($bid_users as $bid_user) {
                        $user = new tbUser(array("id" => $bid_user['user_id']), 403);
                        $user->account_balance += $bid_user['bet']*2;
                        $user->save();
                    }
                    tbLotto_bid::delete_bid($round_id);
                }
            }
        }
		foreach ($this->lotto_types as $lotto_id => $info) {
			$last_round = $round -> get_last_result_id($lotto_id);
			if ($info['name'] == "gdklsf") {
				$result_id = date('Ymd01', time());
			} elseif ($info['name'] == "cqssc") {
				$result_id = date('Ymd120', strtotime('-1 day'));
			} elseif ($info['name'] == "sdqyh") {
				$result_id = date('Ymd001', time());
			} else {
				$result_id = $last_round['result_id'] + 1;
			}
			$total = $result_id + $info['total_rounds'];
			if($info['name'] == "cqssc") {
				$total = date('Ymd001', time()) + $info['total_rounds'] - 1;
			}
			$starttime = date("Y-m-d {$info['starttime']}");
			for ($i = $result_id; $i < $total; $i++) {
				$endtime = date('Y-m-d H:i:s', strtotime("+{$info['duration']}", strtotime($starttime)));
				$round = new tbRound();
				$round -> lotto_id = $lotto_id;
				$round -> result_id = $i;
				$round -> starttime = $starttime;
				$round -> endtime = $endtime;
				$round -> save();
				$starttime = $endtime;
				if ($info['name'] == "cqssc") {
					if ($endtime == date("Y-m-d 04:00:00")) {
						$starttime = date("Y-m-d 7:55:00");
					}
					if ($i == $result_id) {
						$i = date('Ymd001', time());
					}
				}
			}
		}
		echo 'generate_rounds';
	}


	/**
	 * cronjob - 1 time/10 seconds
	 * check all lotto APIs
	 * match result with rounds
	 */

    function check_lotto_results() {
        foreach ($this->lotto_types as $lotto_id => $info) {
            $response = file_get_contents("http://api.caipiaokong.com/lottery/?name={$info['name']}&format=json&uid={$this->uid}&token={$this->token}");
            $result = json_decode($response, true);
            if ($result) {
                $current_time = date("Y-m-d H:i:s");
                $round = new tbRound;
                foreach ($result as $key => $res) {
                    $empty_round = $round -> get_round_by_result_id($lotto_id, $key);
                    if($empty_round) {
                        if($current_time >= $empty_round['endtime']) {
                            if($lotto_id == 6) {
                                $res['number'] = substr($res['number'], 0, 29);
                            }
                            tbRound::update_result($empty_round['id'], $res['number']);
                            $this -> $info['function']($res['number'], $empty_round['id'], $info['prize'], $info['modes'], $info['total_objects']);
                            break;
                        }
                    }
                }
            }
        }
		echo 'check_lotto_result';
    }
	
	private function check_lotto1_winners($result, $round_id, $prize_list, $modes, $total_objects) {
		$result_array = explode(',', $result);
		$bids = new tbLotto_bid();
		$bids = $bids -> get_bids($round_id);
		if ($bids) {
			foreach ($bids as $bid) {
				$prize = $bid['bet'] * $this -> value_per_bet * $prize_list['mode' . $bid['mode']];
				$selection_array = explode(',', $bid['selection']);
				if (in_array($bid['mode'], $modes['guess_the_first']) && $bid['selection'] == $result_array[0]) {//guess the first ball
                    $this->message_to_winners($prize, $bid['id'], $bid['user_id']);
				} elseif (in_array($bid['mode'], array_keys($modes['guess_any']))) {//guess any
					foreach ($modes['guess_any'] as $mode => $number) {
						if ($bid['mode'] == $mode) {
							if (count(array_intersect($selection_array, $result_array)) == $number) {
                                $this->message_to_winners($prize, $bid['id'], $bid['user_id']);
							}
							break;
						}
					}
				} elseif (in_array($bid['mode'], $modes['guess_in_row'])) {//in row
					if (strpos($result, $bid['selection']) !== FALSE) {
                        $this->message_to_winners($prize, $bid['id'], $bid['user_id']);
					}
				} elseif (in_array($bid['mode'], array_keys($modes['guess_in_position']))) {//guess certain balls in right position
					foreach ($modes['guess_in_position'] as $mode => $number) {
						if ($bid['mode'] == $mode) {
							if ($this -> check_win_in_position($selection_array, $result_array, $number, $total_objects)) {
                                $this->message_to_winners($prize, $bid['id'], $bid['user_id']);
							}
							break;
						}
					}
				}
			}
		}
	}

	private function check_lotto2_winners($result, $round_id, $prize_list, $modes, $total_objects) {
		$result_array = explode(',', $result);
		$bids = new tbLotto_bid();
		$bids = $bids -> get_bids($round_id);
		if ($bids) {
			foreach ($bids as $bid) {
				$prize = $bid['bet'] * $this -> value_per_bet * $prize_list['mode' . $bid['mode']];
				$selection_array = explode(',', $bid['selection']);
				if (in_array($bid['mode'], $modes['guess_the_first']) && $bid['selection'] == $result_array[0]) {//guess the first ball
                    $this->message_to_winners($prize, $bid['id'], $bid['user_id']);
				} elseif (in_array($bid['mode'], array_keys($modes['guess_any']))) {//guess any
					foreach ($modes['guess_any'] as $mode => $number) {
						if ($bid['mode'] == $mode) {
							if (count(array_intersect($selection_array, $result_array)) == $number) {
                                $this->message_to_winners($prize, $bid['id'], $bid['user_id']);
							}
							break;
						}
					}
				} elseif (in_array($bid['mode'], $modes['guess_in_row'])) {//in row
					if (strpos($result, $bid['selection']) !== FALSE) {
                        $this->message_to_winners($prize, $bid['id'], $bid['user_id']);
					}
				} elseif (in_array($bid['mode'], array_keys($modes['guess_in_position']))) {//guess certain balls in right position
					foreach ($modes['guess_in_position'] as $mode => $number) {
						if ($bid['mode'] == $mode) {
							if ($this -> check_win_in_position($selection_array, $result_array, $number, $total_objects)) {
                                $this->message_to_winners($prize, $bid['id'], $bid['user_id']);
							}
							break;
						}
					}
				}
			}
		}
	}

	private function check_lotto3_winners($result, $round_id, $prize_list, $modes, $total_objects) {
		$result_array = explode(',', $result);
		$bids = new tbLotto_bid();
		$bids = $bids -> get_bids($round_id);
		if ($bids) {
			foreach ($bids as $bid) {
				$prize = $bid['bet'] * $this -> value_per_bet * $prize_list['mode' . $bid['mode']];
				$selection_array = explode(',', $bid['selection']);
				if (in_array($bid['mode'], $modes['guess_the_last']) && $bid['selection'] == end($result_array)) {//guess the last one
                    $this->message_to_winners($prize, $bid['id'], $bid['user_id']);
				} elseif (in_array($bid['mode'], $modes['guess_last_in_row'])) {// guess the last balls in a row
					$result = implode(',', array_reverse($result_array));
					$selection = implode(',', array_reverse($selection_array));
					if (strpos($result, $selection) === 0) {
                        $this->message_to_winners($prize, $bid['id'], $bid['user_id']);
					}
				} elseif (in_array($bid['mode'], array_keys($modes['guess_any_last']))) {// guess the last balls in any order
					foreach ($modes['guess_any_last'] as $mode => $total) {
						if ($bid['mode'] == $mode) {
							$this -> guess_any_last($result_array, $selection_array, $total, $prize, $bid['id'], $bid['user_id']);
							break;
						}
					}
				} elseif (in_array($bid['mode'], array_keys($modes['guess_some_in_last']))) {//guess some certain balls in defined last values in any order
					foreach ($modes['guess_some_in_last'] as $mode => $number) {
						if ($bid['mode'] == $mode) {
							$this -> get_some_in_last($result_array, $selection_array, $number['total'], $number['guess'], $prize, $bid['id'], $bid['user_id']);
							break;
						}
					}
				}
			}
		}
	}
	
	private function check_lotto5_winners($result, $round_id, $prize_list, $sub_modes, $total_objects) {
		$result_array = explode(',', $result);
		$bids = new tbLotto_bid();
		$bids = $bids -> get_bids($round_id);
		if ($bids) {
			foreach ($bids as $bid) {
				$prize = $bid['bet'] * $this -> value_per_bet;
				$selection_array = explode(',', $bid['selection']);

				if ($bid['mode'] == 1) {
                    if($bid['selection'] == $result_array[0]) {
                        $prize *= $prize_list[1][0];
                        $this->message_to_winners($prize, $bid['id'], $bid['user_id']);
                    } else {
                        return;
                    }
				} elseif (in_array($bid['mode'], $sub_modes['guess_first_in_row'])) {
					foreach ($sub_modes['guess_first_in_row'] as $mode) {
						if ($bid['mode'] == $mode) {
							if(strpos($result, $bid['selection']) === 0) {
                                $prize *= $prize_list[$mode];
                                $this->message_to_winners($prize, $bid['id'], $bid['user_id']);
                            }
							break;
						}
					}
				} else {
					$current_prize = $this->check_car_lotto_winners($result_array, $selection_array, 0);
                    $prize *= $prize_list[$bid['mode']][$current_prize];
                    if($prize) {
                        $this->message_to_winners($prize, $bid['id'], $bid['user_id']);
                    }
				}
			}
		}
	}
	
	private function check_lotto6_winners($result, $round_id, $prize_list, $sub_modes, $total_objects) {
		$result_array = explode(',', $result);
		$bids = new tbLotto_bid();
		$bids = $bids -> get_bids($round_id);
		if ($bids) {
			foreach ($bids as $bid) {
				$prize = $bid['bet'] * $this -> value_per_bet;
				$selection_array = explode(',', $bid['selection']);

				if ($bid['mode'] == 1) {
                    if($bid['selection'] == $result_array[0]) {
                        $prize *= $prize_list[1][0];
                        $this->message_to_winners($prize, $bid['id'], $bid['user_id']);
                    } else {
                        return;
                    }
				} else {
                    $current_prize = $this->check_car_lotto_winners($result_array, $selection_array, 0);
                    $prize *= $prize_list[$bid['mode']][$current_prize];
                    if($prize) {
                        $this->message_to_winners($prize, $bid['id'], $bid['user_id']);
                    }
				}
			}
		}
	}
	
	private function check_lotto7_winners($result, $round_id, $prize_list, $modes, $total_objects) {
		$result_array = explode(',', $result);
		$bids = new tbLotto_bid();
		$bids = $bids -> get_bids($round_id);
		if ($bids) {
			foreach ($bids as $bid) {
				$prize = $bid['bet'] * $this -> value_per_bet * $prize_list['mode' . $bid['mode']];
				$selection_array = explode(',', $bid['selection']);
				if (in_array($bid['mode'], $modes['guess_the_first']) && $bid['selection'] == $result_array[0]) {//guess the first ball
                    $this->message_to_winners($prize, $bid['id'], $bid['user_id']);
				} elseif (in_array($bid['mode'], array_keys($modes['guess_any']))) {//guess any
					foreach ($modes['guess_any'] as $mode => $number) {
						if ($bid['mode'] == $mode) {
							if (count(array_intersect($selection_array, $result_array)) == $number) {
                                $this->message_to_winners($prize, $bid['id'], $bid['user_id']);
							}
							break;
						}
					}
				} elseif (in_array($bid['mode'], $modes['guess_in_row'])) {//in row
					if (strpos($result, $bid['selection']) !== FALSE) {
                        $this->message_to_winners($prize, $bid['id'], $bid['user_id']);
					}
				} elseif (in_array($bid['mode'], $modes['guess_any_first'])) {// guess the first balls in any order
					foreach ($modes['guess_any_first'] as $mode => $number) {
						if ($bid['mode'] == $mode) {
							$this -> guess_any_first($result_array, $selection_array, $number, $prize, $bid['id'], $bid['user_id']);
							break;
						}
					}
				}
			}
		}
	}

    /**
     * Baccarat
     * Selection 1 => small single, 2 => small double, 3 => big single, 4 => big double
     */
    private function check_lotto8_winners($result, $round_id, $prize_list, $modes, $total_objects) {
        $result = explode(',', $result);
        $result = $result[0];
        $bids = new tbLotto_bid();
        $bid = $bids -> get_bids($round_id);
        if($bid) {
            foreach($bid as $b) {
                $choices = json_decode($b['selection']);
                foreach($choices as $choice) {
                    $selected_balls = "";
                    if($choice->selection == 1) {
                        $selected_balls = "01,03,05,07,09";
                    } elseif($choice->selection == 2) {
                        $selected_balls = "02,04,06,08,10";
                    } elseif($choice->selection == 3) {
                        $selected_balls = "11,13,15,17,19";
                    } elseif($choice->selection == 4) {
                        $selected_balls = "12,14,16,18,20";
                    }
                    $selection_array = explode(',', $selected_balls);
                    if(in_array($result, $selection_array)) {
                        $prize = $choice->chip * $prize_list[$choice->selection];
                        $this->message_to_winners($prize, $b['id'], $b['user_id']);
                    }
                }
            }
        }
    }

    /**
     * Dragon - Tiger
     * Selection 1 => dragon(>), 2 => tiger(<), 3 => draw(=)
     */
    private function check_lotto9_winners($result, $round_id, $prize_list, $modes, $total_objects) {
        $result_array = explode(',', $result);
        $first_ball = $result_array[0];
        $last_ball = end($result_array);

        $bids = new tbLotto_bid();
        $bid = $bids -> get_bids($round_id);
        if($bid) {
            foreach($bid as $b) {
                $choices = json_decode($b['selection']);
                foreach($choices as $choice) {
                    $prize = $choice->chip * $prize_list[$choice->selection];
                    if($choice->selection == "1") {
                        if($first_ball > $last_ball) {
                            $this->message_to_winners($prize, $b['id'], $b['user_id']);
                        }
                    } elseif($choice->selection == "2") {
                        if($first_ball < $last_ball) {
                            $this->message_to_winners($prize, $b['id'], $b['user_id']);
                        }
                    } elseif($choice->selection == "3") {
                        if($first_ball == $last_ball) {
                            $this->message_to_winners($prize, $b['id'], $b['user_id']);
                        }
                    }
                }
            }
        }
    }

    /**
     * Special6
     */
    private function check_lotto10_winners($result, $round_id, $prize_list, $modes, $total_objects) {
        $result_array = explode(',', $result);
        $result_array = array_slice($result_array, 0, 3);

        $bids = new tbLotto_bid();
        $bid = $bids -> get_bids($round_id);
        if($bid) {
            foreach($bid as $b) {
                $choices = json_decode($b['selection']);
                foreach($choices as $choice) {
                    $prize = $choice->chip * $prize_list[$choice->selection];
                    if($choice->selection == 1) {
                        sort($result_array);
                        if(count(array_unique($result_array)) == 1) {
                            $this->message_to_winners($prize, $b['id'], $b['user_id']);
                        }
                    } elseif($choice->selection == 2) {
                        $is_win = $this->check_value_in_order($result_array, 3);
                        if($is_win) {
                            $this->message_to_winners($prize, $b['id'], $b['user_id']);
                        }
                    } elseif($choice->selection == 3) {
                        sort($result_array);
                        if(count(array_unique($result_array)) <= 2) {
                            $this->message_to_winners($prize, $b['id'], $b['user_id']);
                        }
                    } elseif($choice->selection == 4) {
                        $is_win = $this->check_value_in_order($result_array, 2);
                        if($is_win) {
                            $this->message_to_winners($prize, $b['id'], $b['user_id']);
                        }
                    } else {
                        $this->message_to_winners($prize, $b['id'], $b['user_id']);
                    }
                }
            }
        }
    }

    /**
     * Banker and Player
     * selection: 1 => Banker, 2 => Player, 3 => Draw
     */
    private function check_lotto11_winners($result, $round_id, $prize_list, $modes, $total_objects) {
        $result_array = explode(',', $result);
        $first_number = $result_array[0] + $result_array[1];
        $last_number = $result_array[3] + $result_array[4];
        $bids = new tbLotto_bid();
        $bid = $bids -> get_bids($round_id);
        if($bid) {
            foreach($bid as $b) {
                $choices = json_decode($b['selection']);
                foreach($choices as $choice) {
                    $prize = $choice->chip * $prize_list[$choice->selection];
                    if($first_number > $last_number) {
                        if($choice->selection == 1) {
                            $this->message_to_winners($prize, $b['id'], $b['user_id']);
                        }
                    } elseif($first_number < $last_number) {
                        if($choice->selection == 2) {
                            $this->message_to_winners($prize, $b['id'], $b['user_id']);
                        }
                    } else {
                        if($choice->selection == 3) {
                            $this->message_to_winners($prize, $b['id'], $b['user_id']);
                        }
                    }
                }
            }
        }
    }

    private function check_value_in_order($result_array, $condition) {
        $count = count($result_array);
        $equal_values = 0;
        foreach($result_array as $k => $v) {
            if($k < $count - 1) {
                $next_result = $result_array[$k] + 1;
                if($result_array[$k] == 9) {
                    $next_result = 0;
                }
                if($result_array[$k + 1] == $next_result) {
                    $equal_values += 1;
                }
            }
        }
        if($equal_values == $condition - 1) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    private function get_some_in_last($result_array, $selection_array, $total, $guess, $prize, $bid_id, $user_id) {
        $result_array = array_slice($result_array, -$total);
        $count = 0;
        foreach($result_array as $result_value) {
            foreach($selection_array as $key => $select_value) {
                if($result_value == $select_value) {
                    $count += 1;
                    unset($selection_array[$key]);
                    break;
                }
            }
        }
        if($count == $guess) {
            $this->message_to_winners($prize, $bid_id, $user_id);
        }
    }

    private function guess_any_last($result_array, $selection_array, $total, $prize, $bid_id, $user_id) {
        $result_array = array_slice($result_array, -$total);
        sort($result_array);
        sort($selection_array);
        if ($result_array == $selection_array) {
            $this->message_to_winners($prize, $bid_id, $user_id);
        }
    }

	private function guess_any_first($result_array, $selection_array, $number, $prize, $bid_id, $user_id) {
		$result_array = array_slice($result_array, 0, $number);
		if (count(array_intersect($selection_array, $result_array)) == $number) {
            $this->message_to_winners($prize, $bid_id, $user_id);
		}
	}

	/*private function check_win_in_position($selection, $result_array, $number, $total_objects) {
		for ($i = 0; $i < $total_objects - ($number - 1); $i++) {
			$check_win = TRUE;
			for ($j = 0; $j < $number; $j++) {
				if ($j == 0) {
					if ($selection[$i] != $result_array[$i]) {
						$check_win = FALSE;
					}
				} else {
					if ($selection[$i + $j] != $result_array[$i + $j]) {
						$check_win = FALSE;
					}
				}
			}
			if ($check_win) {
				return $check_win;
			}
		}
		return FALSE;
	}*/
    private function check_win_in_position($selection_array, $result_array, $number, $total_objects) {
        $is_win = FALSE;
        foreach($selection_array as $k => $v) {
            if($k < $total_objects-$number+1) {
                $condition = TRUE;
                for($i=1;$i<$number;$i++) {
                    if($selection_array[$k + $i] != $result_array[$k + $i]) {
                        $condition = FALSE;
                        break;
                    }
                }
                if($v == $result_array[$k] && $condition) {
                    $is_win = TRUE;
                    break;
                }
            }
        }
        return $is_win;
    }

    private function check_car_lotto_winners($result_array, $selection_array, $current_prize) {
        $count = count($selection_array);
        $check = $selection_array[0] == $result_array[0];
        array_shift($selection_array);
        array_shift($result_array);
        if($count == 1) {
            if($check) {
                return $current_prize + 1;
            } else {
                return $current_prize;
            }
        } elseif($check) {
            $current_prize += 1;
        }
        return $this->check_car_lotto_winners($result_array, $selection_array, $current_prize);
    }

    private function message_to_winners($prize,$bid_id, $user_id) {
		$win_history = new tbWin_history(array('bid_id' => $bid_id, 'bid_type' => 1));
        $win_history->prize = $prize;
		$win_history->bid_type = 1;
		$win_history->bid_id = $bid_id;
        $win_history->save();
		
        tbUser::change_account_balance($prize,$user_id);
        $this->load->library('pushplus/lbpush_plus');
        $dollar = money_format("$%i", $prize);
        $this->lbpush_plus->pushByOwners($user_id, "Congratulations! You've won a prize of $dollar");
    }
	
	/**
	 * cronjob 1 time/10 seconds
	 */
	function result_tracking() {
		$lottos = new tbLotto();
		$lottos -> get();

		foreach ($lottos as $lotto) {
			$response = file_get_contents("http://api.caipiaokong.com/lottery/?name={$lotto->code}&format=json&uid={$this->uid}&token={$this->token}");
			$result = json_decode($response, true);
			if (isset($result)) {
				foreach ($result as $key => $res) {
					$l = new tbLotto_result_tracking( array('result_id' => $key, 'lotto_id' => $lotto -> id));
					if (!$l -> exists()) {
						$l -> result_id = $key;
						$l -> lotto_id = $lotto -> id;
						$l -> result = $res['number'];
						$l -> dateline = $res['dateline'];
						$l -> save();
					}
				}
			}
		}
		echo "success";
	}

}
