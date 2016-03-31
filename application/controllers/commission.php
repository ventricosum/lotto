<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Commission extends CI_Controller {

    private $data = array();

    public function __construct()
    {
        parent::__construct();
        $this -> data['title'] = LocalizedString("Commission");
    }

    function index ()
    {
        $this->load->library('plustree/hierarchy');
        $content = '';
        $content .= "<h3>Downline Level: </h3>";
        $tbtree = new tbTree_interest();
        $tree_interest = $tbtree->get_tree_interest();
        for($i=1;$i<=5;$i++) {
            if(isset($tree_interest[$i-1]['interest'])) {
                $interest = $tree_interest[$i-1]['interest'];
            } else {
                $interest = tbSetting::get_setting("downline_rate");
            }
            $content .= "<div style='margin: 10px 0 10px 0'>Level $i - <input form='rate_form' type='text' name='interest[]' value='".$interest."'></div>";
        }
        $content .= '<form id="rate_form" action="'.base_url("commission/process_changing_rate").'" method="post"><input type="submit" value="Save Changes" class="btn btn-primary"></form>';

        $this -> data['user'] = checklogin();
        $this -> data['navigate'] = navigate(5, -1);
        $this -> data['contents']['col-sm-12'] = $content;
        $this -> load -> view('element/adminLTE', $this->data);
    }

    function process_changing_rate() {
        $input_data = $this->input->post();
        foreach($input_data['interest'] as $k => $v) {
            if(is_numeric($v)) {
                $level = $k + 1;
                $tbtree = new tbTree_interest(array('id' => $level));
                if(!$tbtree->id) {
                    $tbtree = new tbTree_interest();
                }
                $tbtree->interest = $v;
                $tbtree->save();
            }
        }
        redirect(base_url("commission"));
    }
}
