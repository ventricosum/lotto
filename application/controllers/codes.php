<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Codes extends CI_Controller {
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
		$this -> data['title'] = LocalizedString("Code");
	}
	/**
	 * index () - table loads all codes
	 * @access public
	 */
	function index ()
	{
		$this -> data['user'] = checklogin();
		$this->data['navigate'] = navigate(2);
		$this->data['contents']['col-sm-6 generate_code'] = $this->generate_codes();
        $this->data['contents']['col-sm-12 export_excel'] = $this->export_btn();
		$this->data['contents']['col-sm-12'] = $this->get_table_codes();

		$this -> load -> view('element/adminLTE', $this->data);
	}
	/**
	 * using tablemanagement
	 * @access protected
	 */
	protected function get_table_codes ()
	{
		$this->load->library('tablemanagement/table_management');
	    $content = $this->table_management->getTable('all-codes-codes');
	    return load("tablemanagement/libre_elements/index", array('content' => $content), TRUE);
	}
	/**
	 * generate_codes() - form to generate codes
	 * @access protected 
	 */
	protected function generate_codes ()
	{
		$formData = array();
		$formData['action'] = "/codes/add_codes";
		$items = array();
		$items[] = array("type" => "number", "title" => "codes qty", "name" => "codes_qty");
		$items[] = array("type" => "number", "title" => "Value", "name" => "value");
		$formData['items'] = $items;
		$formData['submit'] = array('title'=>'Generate', 'style'=>"");
		if ($errors = $this -> session -> flashdata('errors')) {
			$formData['errors'] = $errors;
		}
		if ($alert = $this -> session -> flashdata('alert')) {
			$formData['alert'] = $alert;
		}

		return load("element/form", $formData, TRUE).br();	
	}
	/**
	 * add_codes() - handle generating code
	 */
	function add_codes ()
	{
		$codes_qty = intval($this->input->post('codes_qty'));
		$value = $this->input->post('value');
		try {
            if($codes_qty < 1 || $codes_qty > 100) {
                $this->session->set_flashdata('errors', array('Code quantity should be greater than 0 and less than 100'));
                redirect($_SERVER['HTTP_REFERER']);
            } else {
                if ($value > 0)
                {
                    if( $value <= 1000000) {
                        for ($i=0; $i < $codes_qty; $i++)
                        {
                            $code = new tbCode();
                            $code->value = $value;
                            $code->code = random_string('alnum', 16);
                            $code->save();
                        }
                        $this->session->set_flashdata('alert', $codes_qty . ' new codes are created!');
                    } else {
                        $this->session->set_flashdata('errors', array('The value is too big to proceed'));
                    }
                }
                else
                {
                    $this->session->set_flashdata('errors', array('invalid value'));
                }
            }
			redirect($_SERVER['HTTP_REFERER']);
		} catch (Exception $e) {
			echo $e->getMessage();
		}
	}
	/**
	 * delete_code ()
	 * @method get
	 * @param id: code id
	 * @access public
	 */
	function delete_code ()
	{
		$id = (int) $this->input->get('id');
		try {
			$this->db->delete('tbCode', array('id'=>$id));
			echo 'success';
		} catch (Exception $e) {
			echo $e->getMessage();
		}
	}

    function export_btn() {
        return "<a class='btn' style='margin-bottom:20px;background:#333;color:white;'
        href='".base_url("codes/excel_export")."' target='_blank'>Export to Excel</a>";
    }
    function excel_export() {
        $file="codes.xls";
        $codes = new tbCode();
        $codes = $codes->get_codes();
        $excel = "<table style='border-collapse: collapse;border: 1px solid black;'>";
        $excel .= "<th style='border: 1px solid black;'>Code</th>";
        $excel .= "<th style='border: 1px solid black;'>Value</th>";
        foreach($codes as $code) {
            $excel .= "<tr>";
            $excel .= "<td style='border: 1px solid black;'>{$code['code']}</td>";
            $excel .= "<td style='border: 1px solid black;'>{$code['value']}</td>";
            $excel .= "</tr>";
        }
        $excel .= "<table>";
        header("Content-type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=$file");
        echo $excel;
    }
}
	