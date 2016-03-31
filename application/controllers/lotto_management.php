<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Lotto_management extends CI_Controller {
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
        $this -> data['title'] = LocalizedString("Lotto Management");
    }

    function index ()
    {
        $this -> data['user'] = checklogin();
        $this -> data['navigate'] = navigate(8, -1);
        $this -> data['contents']['col-sm-12'] = $this->get_table_lotto();
        $this -> load -> view('element/adminLTE', $this->data);
    }

    function get_table_lotto() {
        $this->load->library('tablemanagement/table_management');
        $content = $this->table_management->getTable('all-lotto');
        return load("tablemanagement/libre_elements/index", array('content' => $content), TRUE);
    }

    function edit ()
    {
        $id = (int) $this -> input -> get("id");
        $this -> data["content"] = $this -> edit_lotto_form($id);
        load("tablemanagement/libre_elements/modal", $this -> data);
    }

    public function edit_lotto_form($id)
    {
        $form = array(
            "action" => "/lotto_management/handle_edit_lotto",
            "submit" => "Save changes",
            "items" => $this -> lotto_form($id),
            "error" => ($error = $this -> session -> flashdata("error")) ? $error : null,
            "ajax" => true
        );
        return libre_form($form, "tablemanagement/libre_elements/form");
    }

    protected function lotto_form($id)
    {
        $lotto = new tbLotto(array("id"=>$id));

        $items = array();
        if ($lotto->exists())
        {
            $items[] = item("hidden", "", "id", null, $lotto -> id);
            $items[] = item("text", "Name", "name", null, $lotto -> name);
            $items[] = item("text", "Description", "desc", null, $lotto -> desc);
            $items[] = item("file", "Logo", "logo", null, $lotto -> logo);
        }
        return $items;
    }

    function handle_edit_lotto ()
    {
        $id = $this->input->post("id");
        $name = $this->input->post("name");
        $desc = $this->input->post("desc");
        $ci = & get_instance();
        $ci->load->library('plusgallery/lbgallery');
        $image_id = $ci->lbgallery->upimage('logo');
        $link_image = base_url(refine_image_url($ci->lbgallery->logimg($image_id)));
        try {
            $lotto = new tbLotto(array('id' => $id), 403);
            $lotto->name = $name;
            $lotto->desc = $desc;
            $lotto->logo = $image_id;
            $lotto->logo_html = "<img style='max-width:100px !important;max-height:100px' src='$link_image'>";
            $lotto->save();
            echo "success";
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    function detail() {
        $lotto_id = $this->input->get('id');
        $this -> data['user'] = checklogin();
        $this -> data['navigate'] = navigate(8, -1);
        $this -> data['contents']['col-sm-12'] = load("element/table", $this->get_table_lotto_mode($lotto_id), TRUE);
        $this -> load -> view('element/adminLTE', $this->data);
    }

    private function get_table_lotto_mode($lotto_id) {
        $header = array(
            LocalizedString("Mode ID"),
            LocalizedString("Name"),
            LocalizedString("Description"),
            LocalizedString("Action")
        );
        $modes = new tbLotto_modes();
        $dataset = $modes->get_all_lotto_modes($lotto_id);
        $rows = array();
        foreach ($dataset as $data)
        {
            $action = "";
            $action.= html("a", LocalizedString("Edit"), array(
                "href" => "/lotto_management/edit_mode?id={$data['id']}&lotto_id=$lotto_id",
            ));

            $row = array();
            $row[] = $data['mode_id'];
            $row[] = $data['name'];
            $row[] = $data['description'];
            $row[] = $action;
            $rows[] = $row;
        }
        return array("headers" => $header, "rows" => $rows, "tableId" => "lotto_mode_management");
    }

    function edit_mode() {
        $id = $this->input->get('id');
        $lotto_id = $this->input->get('lotto_id');
        $this -> data['user'] = checklogin();
        $this -> data['navigate'] = navigate(8, -1);
        $this -> data['contents']['col-sm-12'] =
            $this->edit_mode_form($id, $lotto_id) .
            "<script src='//cdn.ckeditor.com/4.4.7/standard/ckeditor.js'></script>" .
            "<script>CKEDITOR.replace( 'description' );</script>";
        $this -> load -> view('element/adminLTE', $this->data);
    }

    protected function edit_mode_form ($id, $lotto_id)
    {
        $mode = new tbLotto_modes(array('id' => $id), 403);
        if($mode->exists()) {
            $items = array();
            $items[] = item("hidden", "", "id", null, $mode->id);
            $items[] = item("hidden", "", "lotto_id", null, $lotto_id);
            $items[] = item("text", "Name", "name", null, $mode->name);
            $items[] = item("textarea", "Description", "description", null, $mode->description);

            $form = array(
                "action" => "/lotto_management/handle_edit_mode",
                "submit" => "Save",
                "items" => $items,
                "error" => ($error = $this -> session -> flashdata("error")) ? $error : null,
            );
            return libre_form($form, "tablemanagement/libre_elements/form");
        }
    }

    function handle_edit_mode() {
        $id = $this->input->post('id');
        $lotto_id = $this->input->post('lotto_id');
        $name = $this->input->post('name');
        $description = $this->input->post('description');
        $mode = new tbLotto_modes(array('id' => $id), 403);
        $mode->name = $name;
        $mode->description = $description;
        $mode->save();
        redirect(base_url("lotto_management/detail?id=$lotto_id"));
    }
}