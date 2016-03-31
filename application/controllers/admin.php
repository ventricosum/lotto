<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends CI_Controller {

	private $data = array();
    public function __construct()
	{
		parent::__construct();
	}
	/**
	 * login() - admin login
	 * @method void
	 * @param void
	 * @access public
	 */
    public function login()
    {
    	if ($this->session->userdata('admin_token'))
		{
			$user = checklogin();
			if (isset($user->display_name))
			{
				// upline
				redirect("/upline/manager");
			} else {
				// admin
				redirect("/football");
			}
		}
    	$this->data['error'] = $this->session->flashdata('error');
      	$this->load->view('login', $this->data);
    }
	/**
	 * do_login() - handle admin login
	 * @method post
	 * @param username
	 * @param password
	 * @access public
	 */
    public function do_login()
    {
		$username = $this->input->post('username');
		$password = $this->input->post('password');
		$password = md5($password);
		
		try {
			$admin = tbAdmin::login($username, $password);
			$is_upline = 0;
			if ($admin === FALSE)
			{
				// check upline
				$admin = tbUser::login($username, $password);
				if ($admin === FALSE)
				{
					$this->session->set_flashdata('error', 'Username or password is wrong');
					redirect('/admin/login');
				}
				else
				{
					if ($admin->active == -1)
					{
						$this->session->set_flashdata('error', 'Sorry! your account is banned!');
						redirect('/admin/login');
					}
					$is_upline = 1;
				}
			}
			
			$admin->token = random_string('unique');
			$admin->save();
		
			$this->session->set_userdata('admin_token', $admin->token);
			if ($is_upline == 0)
			{
				redirect("/football");	
			}
			else
			{
				redirect("/upline/manager");
			}
		} catch (Exception $e) {
			echo $e->getMessage();
		}
    }
	/**
	 * logout
	 */
	function logout ()
	{
		checklogin();
		$this->session->sess_destroy();
		redirect("/");
	}
	/**
	 * 
	 */
	function get_logo ()
	{
		$this->load->view('get_logo');
	}
	
	function handle_get_logo ()
	{
		$name = $this->input->post('name');
		$link = $this->input->post('link');
		$this->load->library('plusgallery/lbgallery');
		$img_id = $this->lbgallery->upimgbyurl($link);
		
		$t = new tbFootball_club(array("name"=>$name));
		$t->logo = $img_id;
		$t->name = $name;
		$t->save();
		redirect("/admin/get_logo");
	}
}