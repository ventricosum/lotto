<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Test extends CI_Controller {
    /*
     * 已结束: closed
     * 未开赛: not start
     * 进行中: in progress
     * hteam: c4T1
     * vteam: c4T2
     * "error_code":0 -> success
     * "reason":"联赛名称错误": League name error
     */
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
    private $data = array();
    public function __construct()
    {
        parent::__construct();
        $this -> data['title'] = "Test Lotto";
    }

    function add_district() {
        if($this->input->post()) {
            $data = $this->input->post();
            $data['city_id'] = $this->uri->segment(3);
            $this->db->insert("tbDistricts", $data);
        }
        $html = "";
        $html .= '<form method="post" action="">';
        $html .= 'District: <br><input style="width:200px" type="text" name="district"><br><br>';
        $html .= '<input type="submit" value="Submit">';
        $html .= '</form>';
        $this -> data['user'] = checklogin();
        $this -> data['navigate'] = navigate(6, -1);
        $this -> data['contents']['col-sm-12'] = load("element/table", $this->district_table($this->uri->segment(3)), TRUE) . $html;
        $this -> load -> view('element/adminLTE', $this->data);
    }

    function city_table() {
        $header = array(
            "ID",
            "City",
            "Action"
        );
        $dataset = $this->db->get("tbCities")->result_array();
        $rows = array();
        foreach ($dataset as $data)
        {
            $row = array();
            $row[] = $data['id'];
            $row[] = $data['city'];
            $row[] = '<a href="'.base_url("test/add_district/{$data['id']}").'">View</a>';
            $rows[] = $row;
        }
        return array("headers" => $header, "rows" => $rows, "tableId" => "city_statistic");
    }

    function district_table($city_id)
    {
        $header = array(
            "ID",
            "District"
        );
        $this->db->where('city_id', $city_id);
        $dataset = $this->db->get("tbDistricts")->result_array();
        $rows = array();
        foreach ($dataset as $data) {
            $row = array();
            $row[] = $data['id'];
            $row[] = $data['district'];
            $rows[] = $row;
        }
        return array("headers" => $header, "rows" => $rows, "tableId" => "city_statistic");
    }

    function add_city() {
        if($this->input->post()) {
            $data = $this->input->post();
            $this->db->insert("tbCities", $data);
        }
        $html = "";
        $html .= '<form method="post" action="">';
        $html .= 'City: <br><input style="width:200px" type="text" name="city"><br><br>';
        $html .= '<input type="submit" value="Submit">';
        $html .= '</form>';
        $this -> data['user'] = checklogin();
        $this -> data['navigate'] = navigate(6, -1);
        $this -> data['contents']['col-sm-12'] = load("element/table", $this->city_table(), TRUE) . $html;
        $this -> load -> view('element/adminLTE', $this->data);
    }
    function index ()
    {
        $html =  "<form method='post' action='".base_url("test/check_lotto_results")."'>";

        $html .= "Lotto: <br><select name='function'>";
        for($i=1;$i<12;$i++) {
            if($i == 4) {
                continue;
            }
            $html .= "<option value='check_lotto".$i."_winners-$i'>$i</option>";
        }
        $html .= "</select><br>";

        $html .= "Mode: <br><select name='lotto_mode'>";
        for($i=1;$i<14;$i++) {
            $html .= "<option value='$i'>$i</option>";
        }
        $html .= "</select><br>";

        $html .= "Result: <br><input style='width:200px' type='text' name='result' value='01,02,03,04,05'><br>";
        $html .= "Selection: <br><input style='width:200px' type='text' name='selection'><br>";
        $html .= "Bet: <br><input type='number' name='bet' value='1'><br><br>";
        $html .= "<input type='submit' value='Submit'>";
        $html .= "</form>";
        $this -> data['user'] = checklogin();
        $this -> data['navigate'] = navigate(6, -1);
        $this -> data['contents']['col-sm-12'] = $html;
        $this -> load -> view('element/adminLTE', $this->data);
    }

    function check_lotto_results() {
        $data = $this->input->post();
        //echo"<pre>";var_dump($data);die;
        $separate = explode('-', $data['function']);
        $function = $separate[0];
        $type = $separate[1];
        foreach ($this->lotto_types as $lotto_id => $info) {
            if($lotto_id == $type) {
                $this -> $function($data['result'],$data['selection'], $data['bet'], $data['lotto_mode'],$info['prize'], $info['modes'], $info['total_objects']);
            }
        }
        //echo"<pre>";var_dump($data);die;
    }

    private function check_lotto1_winners($result, $selection, $bet, $my_mode, $prize_list, $modes, $total_objects) {
        $result_array = explode(',', $result);
                $prize = $bet * $this -> value_per_bet * $prize_list['mode' . $my_mode];
                $selection_array = explode(',', $selection);
                if (in_array($my_mode, $modes['guess_the_first']) && $selection == $result_array[0]) {//guess the first ball
                    echo "WIN - PRIZE: $prize";
                } elseif (in_array($my_mode, array_keys($modes['guess_any']))) {//guess any
                    foreach ($modes['guess_any'] as $mode => $number) {
                        if ($my_mode == $mode) {
                            if (count(array_intersect($selection_array, $result_array)) == $number) {
                                echo "WIN - PRIZE: $prize";
                            }
                            break;
                        }
                    }
                } elseif (in_array($my_mode, $modes['guess_in_row'])) {//in row
                    if (strpos($result, $selection) !== FALSE) {
                        echo "WIN - PRIZE: $prize";
                    }
                } elseif (in_array($my_mode, array_keys($modes['guess_in_position']))) {//guess certain balls in right position
                    foreach ($modes['guess_in_position'] as $mode => $number) {
                        if ($my_mode == $mode) {
                            if ($this -> check_win_in_position($selection_array, $result_array, $number, $total_objects)) {
                                echo "WIN - PRIZE: $prize";
                            }
                            break;
                        }
                    }
                }
    }

    private function check_lotto2_winners($result,$selection, $bet, $my_mode, $prize_list, $modes, $total_objects) {
        $result_array = explode(',', $result);

                $prize = $bet * $this -> value_per_bet * $prize_list['mode' . $my_mode];
                $selection_array = explode(',', $selection);
                if (in_array($my_mode, $modes['guess_the_first']) && $selection == $result_array[0]) {//guess the first ball
                    echo "WIN - PRIZE: $prize";
                } elseif (in_array($my_mode, array_keys($modes['guess_any']))) {//guess any
                    foreach ($modes['guess_any'] as $mode => $number) {
                        if ($my_mode == $mode) {
                            if (count(array_intersect($selection_array, $result_array)) == $number) {
                                echo "WIN - PRIZE: $prize";
                            }
                            break;
                        }
                    }
                } elseif (in_array($my_mode, $modes['guess_in_row'])) {//in row
                    if (strpos($result, $selection) !== FALSE) {
                        echo "WIN - PRIZE: $prize";
                    }
                } elseif (in_array($my_mode, array_keys($modes['guess_in_position']))) {//guess certain balls in right position
                    foreach ($modes['guess_in_position'] as $mode => $number) {
                        if ($my_mode == $mode) {
                            if ($this -> check_win_in_position($selection_array, $result_array, $number, $total_objects)) {
                                echo "WIN - PRIZE: $prize";
                            }
                            break;
                        }
                    }
                }

    }

    private function check_lotto3_winners($result, $selection, $bet, $my_mode, $prize_list, $modes, $total_objects) {
        $result_array = explode(',', $result);
                $prize = $bet * $this -> value_per_bet * $prize_list['mode' . $my_mode];
                $selection_array = explode(',', $selection);
                if (in_array($my_mode, $modes['guess_the_last']) && $selection == end($result_array)) {//guess the last one
                    echo "WIN - PRIZE: $prize";
                } elseif (in_array($my_mode, $modes['guess_last_in_row'])) {// guess the last balls in a row
                    $result = implode(',', array_reverse($result_array));
                    $selection = implode(',', array_reverse($selection_array));
                    if (strpos($result, $selection) === 0) {
                        echo "WIN - PRIZE: $prize";
                    }
                } elseif (in_array($my_mode, array_keys($modes['guess_any_last']))) {// guess the last balls in any order
                    foreach ($modes['guess_any_last'] as $mode => $total) {
                        if ($my_mode == $mode) {
                            $this -> guess_any_last($result_array, $selection_array, $total, $prize, 1, 1);
                            break;
                        }
                    }
                } elseif (in_array($my_mode, array_keys($modes['guess_some_in_last']))) {//guess some certain balls in defined last values in any order
                    foreach ($modes['guess_some_in_last'] as $mode => $number) {
                        if ($my_mode == $mode) {
                            $this -> get_some_in_last($result_array, $selection_array, $number['total'], $number['guess'], $prize, 1, 1);
                            break;
                        }
                    }
                }
    }

    private function check_lotto5_winners($result, $selection, $bet, $my_mode, $prize_list, $sub_modes, $total_objects) {
        $result_array = explode(',', $result);
                $prize = $bet * $this -> value_per_bet;
                $selection_array = explode(',', $selection);
                if ($my_mode == 1) {
                    if($selection == $result_array[0]) {
                        $prize *= $prize_list[1][0];
                        echo "WIN - PRIZE: $prize";
                    } else {
                        return;
                    }
                } elseif (in_array($my_mode, $sub_modes['guess_first_in_row'])) {
                    foreach ($sub_modes['guess_first_in_row'] as $mode) {
                        if ($my_mode == $mode) {
                            if(strpos($result, $selection) === 0) {
                                $prize *= $prize_list[$mode];
                                echo "WIN - PRIZE: $prize";
                            }
                            break;
                        }
                    }
                } else {
                    $current_prize = $this->check_car_lotto_winners($result_array, $selection_array, 0);
                    $prize *= $prize_list[$my_mode][$current_prize];
                    if($prize) {
                        echo "WIN - PRIZE: $prize";
                    }
                }
    }

    private function check_lotto6_winners($result, $selection, $bet, $my_mode, $prize_list, $sub_modes, $total_objects) {
        $result_array = explode(',', $result);
                $prize = $bet * $this -> value_per_bet;
                $selection_array = explode(',', $selection);

                if ($my_mode == 1) {
                    if($selection == $result_array[0]) {
                        $prize *= $prize_list[1][0];
                        echo "WIN - PRIZE: $prize";
                    } else {
                        return;
                    }
                } else {
                    $current_prize = $this->check_car_lotto_winners($result_array, $selection_array, 0);
                    $prize *= $prize_list[$my_mode][$current_prize];
                    if($prize) {
                        echo "WIN - PRIZE: $prize";
                    }
                }
    }

    private function check_lotto7_winners($result, $selection, $bet, $my_mode, $prize_list, $modes, $total_objects) {
        $result_array = explode(',', $result);
                $prize = $bet * $this -> value_per_bet * $prize_list['mode' . $my_mode];
                $selection_array = explode(',', $selection);
                if (in_array($my_mode, $modes['guess_the_first']) && $selection == $result_array[0]) {//guess the first ball
                    echo "WIN - PRIZE: $prize";
                } elseif (in_array($my_mode, array_keys($modes['guess_any']))) {//guess any
                    foreach ($modes['guess_any'] as $mode => $number) {
                        if ($my_mode == $mode) {
                            if (count(array_intersect($selection_array, $result_array)) == $number) {
                                echo "WIN - PRIZE: $prize";
                            }
                            break;
                        }
                    }
                } elseif (in_array($my_mode, $modes['guess_in_row'])) {//in row
                    if (strpos($result, $selection) !== FALSE) {
                        echo "WIN - PRIZE: $prize";
                    }
                } elseif (in_array($my_mode, $modes['guess_any_first'])) {// guess the first balls in any order
                    foreach ($modes['guess_any_first'] as $mode => $number) {
                        if ($my_mode == $mode) {
                            $this -> guess_any_first($result_array, $selection_array, $number, $prize, 1, 1);
                            break;
                        }
                    }
                }
    }

    /**
     * Baccarat
     * 1.small single, 2.small double, 3.big single, 4.big double
     */
    private function check_lotto8_winners($result, $selection, $chip, $my_mode, $prize_list, $modes, $total_objects) {
        /*$choices = json_decode('aa');
        foreach($choices as $choice) {
            $prize = $choice['chip'] * $prize_list[$choice['selection']];
        }*/
        $result = explode(',', $result);
        $result = $result[0];
        $selected_balls = "";
        if($selection == 1) {
            $selected_balls = "01,03,05,07,09";
        } elseif($selection == 2) {
            $selected_balls = "02,04,06,08,10";
        } elseif($selection == 3) {
            $selected_balls = "11,13,15,17,19";
        } elseif($selection == 4) {
            $selected_balls = "12,14,16,18,20";
        }
        $selection_array = explode(',', $selected_balls);
        if(in_array($result, $selection_array)) {
            echo "WIN - PRIZE: ".$chip*$prize_list[$selection];
        } else {
            echo "LOSE";
        }
    }

    /**
     * Dragon - Tiger
     * 1.dragon(>), 2.tiger(<), 3.draw(=)
     */
    private function check_lotto9_winners($result, $selection, $chip, $my_mode, $prize_list, $modes, $total_objects) {
        $result_array = explode(',', $result);
        $first_ball = $result_array[0];
        $last_ball = end($result_array);
        $prize = $chip * $prize_list[$selection];
        if($selection == "1") {
            if($first_ball > $last_ball) {
                echo "WIN - PRIZE: $prize";
            } else {
                echo "LOSE";
            }
        } elseif($selection == "2") {
            if($first_ball < $last_ball) {
                echo "WIN - PRIZE: $prize";
            } else {
                echo "LOSE";
            }
        } elseif($selection == "3") {
            if($first_ball == $last_ball) {
                echo "WIN - PRIZE: $prize";
            } else {
                echo "LOSE";
            }
        }
    }

    /**
     * Special6
     */
    private function check_lotto10_winners($result, $selection, $chip, $my_mode, $prize_list, $modes, $total_objects) {
        $result_array = explode(',', $result);
        $result_array = array_slice($result_array, 0, 3);
        $prize = $chip * $prize_list[$selection];
        if($selection == 1) {
            sort($result_array);
            if(count(array_unique($result_array)) == 1) {
                echo "WIN - PRIZE: $prize";
            } else {
                echo "LOSE";
            }
        } elseif($selection == 2) {
            $is_win = $this->check_value_in_order($result_array, 3);
            if($is_win) {
                echo "WIN - PRIZE: $prize";
            } else {
                echo "LOSE";
            }
        } elseif($selection == 3) {
            sort($result_array);
            if(count(array_unique($result_array)) <= 2) {
                echo "WIN - PRIZE: $prize";
            } else {
                echo "LOSE";
            }
        } elseif($selection == 4) {
            $is_win = $this->check_value_in_order($result_array, 2);
            if($is_win) {
                echo "WIN - PRIZE: $prize";
            } else {
                echo "LOSE";
            }
        } else {
            echo "WIN - PRIZE: $prize";
        }
    }

    /**
     * Banker and Player
     */
    private function check_lotto11_winners($result, $selection, $chip, $my_mode, $prize_list, $modes, $total_objects) {
        $result_array = explode(',',$result);
        $prize = $chip * $prize_list[$selection];
        $first_number = $result_array[0] + $result_array[1];
        $last_number = $result_array[3] + $result_array[4];
        if($first_number > $last_number) {
            if($selection == 1) {
                echo "WIN - PRIZE: $prize";
            } else {
                echo "LOSE";
            }
        } elseif($first_number < $last_number) {
            if($selection == 2) {
                echo "WIN - PRIZE: $prize";
            } else {
                echo "LOSE";
            }
        } else {
            if($selection == 3) {
                echo "WIN - PRIZE: $prize";
            } else {
                echo "LOSE";
            }
        }
    }

    function check_value_in_order($result_array, $condition) {
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
            echo "WIN - PRIZE: $prize";
        }
    }

    private function guess_any_last($result_array, $selection_array, $total, $prize, $bid_id, $user_id) {
        $result_array = array_slice($result_array, -$total);
        sort($result_array);
        sort($selection_array);
        if ($result_array == $selection_array) {
            echo "WIN - PRIZE: $prize";
        }
    }

    private function guess_any_first($result_array, $selection_array, $number, $prize, $bid_id, $user_id) {
        $result_array = array_slice($result_array, 0, $number);
        if (count(array_intersect($selection_array, $result_array)) == $number) {
            echo "WIN - PRIZE: $prize";
        }
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

}