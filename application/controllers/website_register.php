<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
if (!defined('BASEPATH'))
	exit('No direct script access allowed');
class website_register extends CI_Controller {

	function __construct() {
		parent::__construct();
		$this -> load -> helper(array('form', 'url'));
	}

	public function success() {
        $data['notification'] = "";
		if ($this->session->flashdata("success")) {
            $data['notification'] = "Thank you! You have successfully register!";
        }
		$this -> load -> view('website_register/success', $data);
	}

	/**
	 * index () - load register form
	 * @access public
	 */
	public function index() {
		$username = $this -> input -> get("username");
		$this -> db -> where(array("username" => $username, "is_upline" => 1, ));
		$user = $this -> db -> get('plusauthentication_user')->row_array();
		if($user) {
			$this -> load -> view('website_register/index', array("error" => $this->session->flashdata("error"),"upline_id" => $user['id']));	
		} else {
			redirect(base_url());
		}
	}

	/**
	 * handle_register () - validate and save new downlines/users
	 */
	public function handle_register() {
		$username = $this -> input -> post("user");
		$password = $this -> input -> post("pass");
		$cpassword = $this -> input -> post("c_pass");
		$email = $this -> input -> post("email");
		$upline_id = (int)$this -> input -> post("upline_id");
		//
		$this->load->helper('email');
		//
		if ((strlen($username) == 0) || (strlen($email) == 0) || (strlen($password) == 0))
		{
			$this->session->set_flashdata("error", "Missing params.");
			redirect("/website/register");
		}
		if (!valid_email($email))
		{
			$this->session->set_flashdata("error", "Invalid email.");
			redirect("/website/register");
		}
		if ($password != $cpassword)
		{
			$this->session->set_flashdata("error", "Password and confirm mismatch.");
			redirect("/website/register");
		}
		
		$user = new tbUser();
		$user->where("username", $username);
		$user->or_where("email", $email);
		$user->get();
		if ($user->exists())
		{
			$this->session->set_flashdata("error", "This username and email currently exist.");
			redirect("/website_register");
		}
		$user->username = $username;
		$user->email = $email;
		$user->password = md5($password);
		$user -> upline = $upline_id;
		$user->active = 1;
		$user->save();

        $user = new tbUser();
        $user->where("username", $username);
        $user->get();
        $this->load->library('plustree/hierarchy');
        $this->hierarchy->update_position_in_tree ($user->id, $upline_id);

		$this->session->set_flashdata("success", 1);
		redirect("/website_register/success");
	}

}
