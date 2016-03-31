<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Upline extends CI_Controller {
	
	private $data = array();
    public function __construct()
	{
		parent::__construct();
		$this -> data['title'] = LocalizedString("Upline");
	}
	/**
	 * index () - table loads all uplines
	 * @access public
	 */
	function index ()
	{
		$this -> data['user'] = checklogin();
		$this->data['navigate'] = navigate(4, -1);
		$this->data['contents']['col-sm-12'] = 
			$this->form_save_downline_rate() . br() .
			$this->get_table_upline();

		$this -> load -> view('element/adminLTE', $this->data);
	}
	/**
	 * using tablemanagement
	 * @access protected
	 */
	protected function get_table_upline ()
	{
		$this->load->library('tablemanagement/table_management');
	    $content = $this->table_management->getTable('all-uplines');
	    return load("tablemanagement/libre_elements/index", array('content' => $content), TRUE);
	}
	/**
	 * add_upline() - create new upline
	 */
	function add_upline ()
	{
		checklogin();
		$this -> data["content"] = $this -> create_form_add_upline();
		load("tablemanagement/libre_elements/modal", $this -> data);
	}
	/**
	 * @param void
	 * @return 
	 * form to add to the content of libre_elements/modal
	 * @access public
	 */	
	public function create_form_add_upline()
	{
		$form = array(
			"action" => "/upline/handle_add_upline", 
			"submit" => "Add", 
			"items" => $this -> form_add_upline(), 
			"error" => ($error = $this -> session -> flashdata("error")) ? $error : null,
			"ajax" => true
		);
		return libre_form($form, "tablemanagement/libre_elements/form");
	}
	/**
	 * @param void
	 * @return 
	 * form to add to the items of form element
	 * @access protected
	 */
	protected function form_add_upline()
	{
		$items = array();
		$items[] = item("text", "Username", "username");
		$items[] = item("text", "Email", "email");
		$items[] = item("password", "Password", "password");
		return $items;
	}
	/**
	 * handle_add_upline
	 * @access public
	 */
	function handle_add_upline ()
	{
		$username = $this->input->post("username");
		$email = $this->input->post("email");
		$password = $this->input->post("password");
		try {
			$this->load->helper('email');
			//
			if ((strlen($username) == 0) || (strlen($email) == 0) || (strlen($password) == 0))
			{
				echo "Missing params.";
				return;
			}
			if (!valid_email($email))
			{
				echo "Invalid email.";
				return;
			}

			$user = new tbUser();
			$user->where("username", $username);
			$user->or_where("email", $email);
			$user->get();
			if ($user->exists())
			{
				echo "This username and email currently exist.";
				return;
			}
			$user->username = $username;
			$user->email = $email;
			$user->password = md5($password);
			$user->is_upline = 1;
			$user->active = 1;
			$user->save();

            $user = new tbUser();
            $user->where("username", $username);
            $user->get();
            $this->load->library('plustree/hierarchy');
            $this->hierarchy->update_position_in_tree ($user->id);

			echo "success";
		} catch (Exception $e) {
			echo $e->getMessage();
		}
	}
	/**
	 * delete a upline
	 */
	function delete_upline ()
	{
		checklogin();
		$id = (int) $this->input->get("id");
	    if ($this->input->post('_table_management'))
	    {
	      	try {
	        	$this->db->delete('plusauthentication_user', array('id'=>$id));
	        	echo 'success';
	      	} catch (Exception $e) {
	        	//throw $e->getMessage();
	      	}
	    }
	}
	/**
	 * index () - upline manager page
	 * @access public
	 */
	function manager ()
	{
		$upline = checklogin(-1);
		$this -> data['user'] = $upline;
		$this->data['navigate'] = navigate(-1, -1);
		
		$this->data['contents']['col-sm-12'] =
			heading("Referer Code:", 2) .
			html("p", base_url("/website_register/index?username={$upline->username}")) .
			html("p", html("b", "Total: ") . $this->get_total_profit($upline->id) ) .
 			load("element/table", $this->table_downline_benefit($upline->id), TRUE);

		$this -> load -> view('element/adminLTE', $this->data);
	}
	/**
	 * 
	 */
	protected function get_total_profit ($upline_id)
	{
		$profit = tbProfit_downline::get_total_downlines_profit($upline_id);
		return floatval($profit[0]->profit);
	} 
	/**
	 * 
	 */
	private function table_downline_benefit ($upline_id)
	{
		$header = array(
	      	LocalizedString("Username"),
	      	LocalizedString("Profit"),
      	);

        $this->load->library('plustree/hierarchy');
        $all_downlines = $this->hierarchy->retrieve_full_tree ($upline_id);
        $tbprofit_downline = new tbProfit_downline();
        $rows = array();
        for($i=1;$i<count($all_downlines);$i++) {
            $data = $tbprofit_downline->get_downline_profit($all_downlines[$i]->id, $upline_id);
            $rows[] = array($data['username'], $data['profit']);
        }

	    return array("headers" => $header, "rows" => $rows, "tableId" => "downline_profit");
	}
	/**
	 * 
	 */
	protected function form_save_downline_rate ()
	{
		$form = array(
			"action" => "/upline/handle_save_downline_rate", 
			"submit" => "Save", 
			"items" => $this -> downline_rate(), 
			"error" => ($error = $this -> session -> flashdata("error")) ? $error : null, 
		);
		return libre_form($form, "tablemanagement/libre_elements/form");		
	}
	
	protected function downline_rate ()
	{
		$items = array();
		$items[] = item("text", "Downline Rate", "rate", null, tbSetting::get_setting('downline_rate'));
		return $items;		
	}
	/**
	 * save rate
	 */
	function handle_save_downline_rate ()
	{
		$rate = floatval($this->input->post("rate"));
		try {
			if ($rate < 0)
			{
				$this->session->set_flashdata("error", "Rate can not be smaller than 0");
				redirect($_SERVER['HTTP_REFERER']);
			}
			
			$setting = new tbSetting(array('keyname' => 'downline_rate'));
			if (!$setting->exists())
			{
				$setting->keyname = "downline_rate";
			}
			$setting->content = $rate;
			$setting->save();
			redirect($_SERVER['HTTP_REFERER']);
		} catch (Exception $e) {
			$this->session->set_flashdata("error", $e->getMessage());
			redirect($_SERVER['HTTP_REFERER']);
		}
	}
	
}