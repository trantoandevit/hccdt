<?php
if (!defined('SERVER_ROOT')) exit('No direct script access allowed');

class xlist_Controller extends Controller {

    function __construct() {
        parent::__construct('admin', 'xlist');
        $this->check_login();
        $this->model->goback_url = $this->view->get_controller_url();
        $this->view->template->show_left_side_bar =FALSE;
        $this->view->template->show_div_website = FALSE;
        $this->view->template->arr_count_article = $this->model->gp_qry_count_article();
        
        @session::init();
        $v_lang_id = session::get('session_lang_id');
        
       
        $this->view->template->arr_all_lang = $this->model->qry_all_lang();
        $this->view->template->arr_all_grant_website = $this->model->gp_qry_all_website_by_user($v_lang_id);
    }

    //main function
    public function main() {
        //Kiem tra quyen
        #(Session::get('is_admin') == 1) Or die($this->access_denied());
        if(session::check_permission('QL_DANH_SACH_LISTTYPE',FALSE)==FALSE)
        {
            die('Bạn không có quyền thực hiện chức năng này !!!');
        }
        $this->dsp_all_listtype();
    }

    //listtype
    public function dsp_all_listtype() {
        //Kiem tra quyen
        #(Session::get('is_admin') == 1) Or die($this->access_denied());
        if(session::check_permission('QL_DANH_SACH_LISTTYPE',FALSE)==FALSE)
        {
            die('Bạn không có quyền thực hiện chức năng này !!!');
        }
        $d['arr_all_listtype'] = $this->model->qry_all_listtype();
        $this->view->render('dsp_all_listtype',$d);
    }

    public function dsp_single_listtype() {
        //Kiem tra quyen
        #(Session::get('is_admin') == 1) Or die($this->access_denied());
        if(session::check_permission('QL_DANH_SACH_LISTTYPE',FALSE)==FALSE)
        {
            die('Bạn không có quyền thực hiện chức năng này !!!');
        }
        $d['arr_single_listtype'] = $this->model->qry_single_listtype();
        $this->view->render('dsp_single_listtype',$d);
    }

    public function dsp_all_file_xml(){
        //Kiem tra quyen
        #(Session::get('is_admin') == 1) Or die($this->access_denied());

        $this->view->render('dsp_all_file_xml');
    }

    public function update_listtype(){
        //Kiem tra quyen
        #(Session::get('is_admin') == 1) Or die($this->access_denied());
        if(session::check_permission('QL_DANH_SACH_LISTTYPE',FALSE)==FALSE)
        {
            die('Bạn không có quyền thực hiện chức năng này !!!');
        }
        $this->model->goback_url = $this->view->get_controller_url() . 'dsp_all_listtype';
        $this->model->goforward_url = $this->view->get_controller_url() . 'dsp_single_listtype' ;
        $this->model->update_listtype();
    }

    public function delete_listtype(){
        //Kiem tra quyen
        #(Session::get('is_admin') == 1) Or die($this->access_denied());
        if(session::check_permission('QL_DANH_SACH_LISTTYPE',FALSE)==FALSE)
        {
            die('Bạn không có quyền thực hiện chức năng này !!!');
        }
        $this->model->goback_url = $this->view->get_controller_url() . 'dsp_all_listtype';
        $this->model->delete_listtype();

    }

    public function check_existing_listtype_code($code_id){
        //Kiem tra quyen
        #(Session::get('is_admin') == 1) Or die($this->access_denied());
        if(session::check_permission('QL_DANH_SACH_LISTTYPE',FALSE)==FALSE)
        {
            die('Bạn không có quyền thực hiện chức năng này !!!');
        }
        $arrArg = explode(_CONST_LIST_DELIM, $code_id);
        $code = trim($arrArg[0]);
        $listtype_id = trim($arrArg[1]);
        echo $this->model->check_existing_listtype_code($code, $listtype_id);
    }
    public function check_existing_listtype_name($name_id){
        //Kiem tra quyen
       # (Session::get('is_admin') == 1) Or die($this->access_denied());
        if(session::check_permission('QL_DANH_SACH_LISTTYPE',FALSE)==FALSE)
        {
            die('Bạn không có quyền thực hiện chức năng này !!!');
        }
        $arrArg = explode(_CONST_LIST_DELIM, $name_id);
        $name = trim($arrArg[0]);
        $listtype_id = trim($arrArg[1]);
        echo $this->model->check_existing_listtype_name($name, $listtype_id);
    }

    //List
    public function check_existing_list_code($code_id){
        //Kiem tra quyen
        #(Session::get('is_admin') == 1) Or die($this->access_denied());
        if(session::check_permission('QL_DANH_SACH_LIST',FALSE)==FALSE)
        {
            die('Bạn không có quyền thực hiện chức năng này !!!');
        }
        $arrArg = explode(_CONST_LIST_DELIM, $code_id);
        $code = trim($arrArg[0]);
        $listtype_id = trim($arrArg[1]);
        $list_id = trim($arrArg[2]);
        echo $this->model->check_existing_list_code($code, $listtype_id, $list_id);
    }

    public function check_existing_list_name($name_id)
    {
        //Kiem tra quyen
        #(Session::get('is_admin') == 1) Or die($this->access_denied());
        if(session::check_permission('QL_DANH_SACH_LIST',FALSE)==FALSE)
        {
            die('Bạn không có quyền thực hiện chức năng này !!!');
        }

        $arrArg = explode(_CONST_LIST_DELIM, $name_id);
        $name = trim($arrArg[0]);
        $listtype_id = trim($arrArg[1]);
        $list_id = trim($arrArg[2]);
        echo $this->model->check_existing_list_name($name, $listtype_id, $list_id);
    }

    public function dsp_all_list() {
        //Kiem tra quyen
        #(Session::get('is_admin') == 1) Or die($this->access_denied());
        if(session::check_permission('QL_DANH_SACH_LIST',FALSE)==FALSE)
        {
            die('Bạn không có quyền thực hiện chức năng này !!!');
        }
        $d['arr_all_listtype_option'] = $this->model->qry_all_listtype_option();
        $d['arr_all_list'] = $this->model->qry_all_list();
        $this->view->render('dsp_all_list', $d);
    }

    public function dsp_single_list() {
        //Kiem tra quyen
       # (Session::get('is_admin') == 1) Or die($this->access_denied());
        if(session::check_permission('QL_DANH_SACH_LIST',FALSE)==FALSE)
        {
            die('Bạn không có quyền thực hiện chức năng này !!!');
        }

        $d['arr_all_listtype_option'] = $this->model->qry_all_listtype_option();
        $d['arr_single_list'] = $this->model->qry_single_list();
        $this->view->render('dsp_single_list',$d);
    }

    public function update_list() {
        //Kiem tra quyen
        #(Session::get('is_admin') == 1) Or die($this->access_denied());
        if(session::check_permission('QL_DANH_SACH_LIST',FALSE)==FALSE)
        {
            die('Bạn không có quyền thực hiện chức năng này !!!');
        }
        $this->model->goback_url = $this->view->get_controller_url() . 'dsp_all_list';
        $this->model->goforward_url = $this->view->get_controller_url() . 'dsp_single_list' ;
        $this->model->update_list();
    }

    public function delete_list() {
        //Kiem tra quyen
       # (Session::get('is_admin') == 1) Or die($this->access_denied());
        if(session::check_permission('QL_DANH_SACH_LIST',FALSE)==FALSE)
        {
            die('Bạn không có quyền thực hiện chức năng này !!!');
        }
        $this->model->goback_url = $this->view->get_controller_url() . 'dsp_all_list';
        $this->model->delete_list();
    }
}