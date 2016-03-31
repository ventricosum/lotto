<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Customer extends CI_Controller {
	private $data = array();
    public function __construct()
	{
		parent::__construct();
		$this -> data['title'] = LocalizedString("Customer");
	}
	function index ()
	{
		$this -> data['user'] = checklogin();
		$this->data['navigate'] = navigate(6, -1);
		$this->data['contents']['col-sm-12'] = 
			html("a", "Promote all users to upline", array(
					"onclick"=>"return deleteElement('/customer/promote_all/1')",
					"href" => "#",
					"class" => "btn btn-primary"
				)) . nbs(2) .
			html("a", "Downgrade all users", array(
					"onclick"=>"return deleteElement('/customer/promote_all/0')",
					"href" => "#",
					"class" => "btn btn-primary"
				)) . br(2) .
				
			$this->get_table_crm_customer();

		$this -> load -> view('element/adminLTE', $this->data);
	}
	/**
	 * using tablemanagement
	 * @access protected
	 */
	protected function get_table_crm_customer ()
	{
		$this->load->library('tablemanagement/table_management');
	    $content = $this->table_management->getTable('crm-users');
	    return load("tablemanagement/libre_elements/index", array('content' => $content), TRUE);
	}
	/**
	 * edit customer info
	 */
	function edit ()
	{
		$id = (int) $this -> input -> get("id");
		$this -> data["content"] = $this -> create_form_crm_customer($id);
		load("tablemanagement/libre_elements/modal", $this -> data);
	}
	/**
	 * index() get the form to edit customer
	 *
	 * @param 
	 * $id: id of attribute in tbAttribute
	 * @return 
	 * form to add to the content of libre_elements/modal
	 * @access public
	 */	
	public function create_form_crm_customer($id=-1)
	{
		$form = array(
			"action" => "/customer/handle_edit_customer", 
			"submit" => "Save changes", 
			"items" => $this -> form_crm_customer($id), 
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
	protected function form_crm_customer($id='')
	{
		$user = new tbUser(array("id"=>$id));
		
		$items = array();
		if ($user->exists())
		{
			$items[] = item("hidden", "", "id", null, $user -> id);
			$items[] = item("text", "Email", "email", null, $user -> email);
			$items[] = item("text", "Username", "username", null, $user -> username);
			$items[] = item("checkbox", "Is Upline?", "is_upline", array(array("title" => "is_upline", "value" => 1, "checked" => ($user -> is_upline == 1) ? true : false)));
		}
		return $items;
	}
	/**
	 * 
	 */
	function handle_edit_customer ()
	{
		$id = $this->input->post("id");
		$username = $this->input->post("username");
		$email = $this->input->post("email");
		$is_upline = (int) $this->input->post("is_upline");
		try {
			$this->load->helper('email');
			//
			if ((strlen($username) == 0) || (strlen($email) == 0))
			{
				echo "Missing params.";
			}
			if (!valid_email($email))
			{
				echo "Invalid email.";
			}
			
			$user = new tbUser();
			$user->where("id <>", $id);
			$user->where("(username = '{$username}' or email = '{$email}')", null, FALSE);
			$user->get();
			if ($user->exists())
			{
				echo "This username or email currently exist.";
				return;
			}
			$user = new tbUser;
			$user->get_by_id($id);
			if ($user->exists())
			{
				$user->username = $username;
				$user->email = $email;
				$user -> is_upline = $is_upline;
				$user->save();
			}
			echo "success";
		} catch (Exception $e) {
			echo $e->getMessage();
		}
	}
	/**
	 * promote_all() - change all user to upline
	 */
	public function promote_all($status = 0)
	{
		checklogin();
		if ($status != 0)
		{
			$status = 1;
		}
		$sql = "UPDATE `plusauthentication_user` SET `is_upline`= {$status}";
		$this->db->query($sql);
		redirect($_SERVER['HTTP_REFERER']);
	}
}