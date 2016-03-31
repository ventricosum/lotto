<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Rounds extends CI_Controller {
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
        $this -> data['title'] = LocalizedString("Rounds");
    }

    function index ()
    {
        $lotto_id = $this->uri->segment(3);
        $this -> data['user'] = checklogin();
        $this -> data['navigate'] = navigate(7, -1);
        $this -> data['contents']['col-sm-6'] = $this->config_adjustment_form() . br();
        $this -> data['contents']['col-sm-12'] =
            $this->get_total_money_from_bids($lotto_id).
            $this->table_round_tab($lotto_id).
            load("element/table", $this->table_rounds($lotto_id), TRUE);
        $this -> load -> view('element/adminLTE', $this->data);
    }

    function table_round_tab($lotto_id) {
        $lotto = new tbLotto();
        $lotto_types = $lotto->get_lotto();
        $html = "<div id='table-tab'>";
        foreach($lotto_types as $lotto_type) {
            if($lotto_id == $lotto_type['lotto_id']) {
                $active = "class='active'";
            } else {
                $active = "";
            }
            $html .= "<a $active href='".base_url("rounds/index/{$lotto_type['lotto_id']}")."'>{$lotto_type['lotto_name']}</a>";
        }
        $html .= "</div>";
        return $html;
    }
    function table_rounds($lotto_id)
    {
        $header = array(
            LocalizedString("Round Number"),
            LocalizedString("Money Keep to Next Round"),
            LocalizedString("Money to Next Round"),
            LocalizedString("Profit"),
            LocalizedString("Charity"),
        );
        $tbround = new tbRound();
        $dataset = $tbround->get_table_rounds($lotto_id);
        $rows = array();
        foreach ($dataset as $data)
        {
            $row = array();
            $row[] = $data['round_number'];
            $row[] = money_format("$%i", $data['total_money'] * tbSetting::get_setting('money_keep_to_next_round'));
            $row[] = money_format("$%i", $data['total_money'] * tbSetting::get_setting('money_to_next_round'));
            $row[] = money_format("$%i", $data['total_money'] * tbSetting::get_setting('profit'));
            $row[] = money_format("$%i", $data['total_money'] * tbSetting::get_setting('charity'));
            $rows[] = $row;
        }
        return array("headers" => $header, "rows" => $rows, "tableId" => "round_statistic");
    }

    protected function get_total_money_from_bids($lotto_id) {
        $bid = new tbLotto_bid();
        $total_money = $bid->get_total_money_from_bids($lotto_id);
        $total_html = "<p><b>".LocalizedString("Total Money from Bet").": ".money_format("$%i",$total_money['total_money'])."</b></p>";
        $total_html .= "<p><b>".LocalizedString("Money Keep to Next Round").": ".money_format("$%i",$total_money['total_money'] * tbSetting::get_setting('money_keep_to_next_round'))."</b>, ";
        $total_html .= "<b>".LocalizedString("Money to Next Round").": ".money_format("$%i",$total_money['total_money'] * tbSetting::get_setting('money_to_next_round'))."</b>, ";
        $total_html .= "<b>".LocalizedString("Profit").": ".money_format("$%i",$total_money['total_money'] * tbSetting::get_setting('profit'))."</b>, ";
        $total_html .= "<b>".LocalizedString("Charity").": ".money_format("$%i",$total_money['total_money'] * tbSetting::get_setting('charity'))."</b>";
        $total_html .= "</p>";
        return $total_html;
    }

    protected function config_adjustment_form ()
    {
        $form = array(
            "action" => "/rounds/handle_save_config",
            "submit" => "Save",
            "items" => $this -> get_config(),
            "error" => ($error = $this -> session -> flashdata("error")) ? $error : null,
        );
        return libre_form($form, "tablemanagement/libre_elements/form");
    }

    function handle_save_config ()
    {
        $post_data = $this->input->post();
        //echo"<pre>";var_dump($post_data);die;
        try {
            foreach($post_data as $name => $value) {
                if($value < 0) {
                    $this->session->set_flashdata("error", "Rate can not be smaller than 0");
                    redirect($_SERVER['HTTP_REFERER']);
                }
                $setting = new tbSetting(array('keyname' => $name));
                if (!$setting->exists())
                {
                    $setting->keyname = $name;
                }
                $setting->content = $value;
                $setting->save();
            }
            redirect($_SERVER['HTTP_REFERER']);
        } catch (Exception $e) {
            $this->session->set_flashdata("error", $e->getMessage());
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    protected function get_config ()
    {
        $items = array();
        $items[] = item("number", LocalizedString("Money Keep to Next Round"), "money_keep_to_next_round", null, tbSetting::get_setting('money_keep_to_next_round'));
        $items[] = item("number", LocalizedString("Money to Next Round"), "money_to_next_round", null, tbSetting::get_setting('money_to_next_round'));
        $items[] = item("number", LocalizedString("Profit"), "profit", null, tbSetting::get_setting('profit'));
        $items[] = item("number", LocalizedString("Charity"), "charity", null, tbSetting::get_setting('charity'));
        return $items;
    }
}