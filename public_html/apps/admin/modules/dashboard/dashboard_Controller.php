<?php
if (!defined('SERVER_ROOT')) exit('No direct script access allowed');

class dashboard_Controller extends Controller {

    function __construct() {
        parent::__construct('admin','dashboard');
        //Kiem tra dang nhap
        $this->check_login();
        
        //kiem tra neu la mobile thi redirec sang approvice article
        $detect = new Mobile_Detect();
        if ($detect->isMobile() && Cookie::get('pc_mode', 0) == 0)
        {
            unset($detect);
            $this->model->exec_done($this->view->get_controller_url('article','admin').'dsp_approve_article');
        }
        
        $this->model->goback_url = $this->view->get_controller_url();
        $this->view->template->show_left_side_bar = FALSE;
        $this->view->template->dsp_side_bar = FALSE;
        
        $arr_all_lang          = $this->model->qry_all_lang();
        
        foreach ($arr_all_lang as $key => $value)
        {
            $v_lang_id = $key;
            break;
        }
        
        if(!isset($_SESSION['session_lang_id']))
        {
            $arr_all_grant_website = $this->model->gp_qry_all_website_by_user($v_lang_id);
            session::set('session_lang_id',$v_lang_id);
        }
        
        else 
        {
            $arr_all_grant_website = $this->model->gp_qry_all_website_by_user($_SESSION['session_lang_id']);
        }
        
        //phần dành cho dashboard
        foreach ($arr_all_grant_website as $key => $value)
        {
            $v_website_id = $key;
            break;
        }
        if(!isset($_SESSION['session_website_id']))
        {
            //$v_website_id = isset($v_website_id)?$v_website_id:0;
            session::set('session_website_id',$v_website_id);
        }
        $this->view->template->arr_all_lang = $arr_all_lang;
        $this->view->template->arr_all_grant_website = $arr_all_grant_website;
        
        $this->view->template->arr_count_article = $this->model->gp_qry_count_article();
    }
    
    function main() {
        $this->dsp_all_dashboard();
    }
    
    function dsp_all_dashboard()
    {
        $this->view->render('dsp_all_dashboard');
    }
    
    function do_change_session_menu_select($value)
    {
        $this->model->go_back_url = $this->view->get_controller_url('dashboard','admin');
        $this->model->do_change_session_menu_select($value);
    }
    
    public function do_change_session_website_id()
    {
        $v_website_id = get_request_var('website_id');
        $v_lang_id    = get_request_var('lang_id');
        $this->model->goback_url = $this->view->get_controller_url('dashboard','admin');
        $this->model->set_session($v_website_id,$v_lang_id);
    }
    
    public function dsp_change_password()
    {
        $this->view->render('dsp_change_password');
    }
    
    public function do_change_password()
    {
       $this->model->do_change_password();
    }
    
    //tao my sql
    public function create_mysql($table_name)
    {
        $this->model->create_mysql($table_name);
    }
    
    //chuyen doi du lieu mssql sang mysql
    public function mssql_to_mysql()
    {
//        $arr_table = array('T_CORES_APPLICATION','T_CORES_CALENDAR','T_CORES_GROUP','T_CORES_GROUP_FUNCTION','T_CORES_LIST','T_CORES_LISTTYPE',
//                            'T_CORES_OU','T_CORES_USER','T_CORES_USER_FUNCTION','T_CORES_USER_GROUP',
//                            'T_PS_ADVERTISING','T_PS_ADVERTISING_POSITION','T_PS_ARTICLE','T_PS_ARTICLE_ATTACHMENT','T_PS_ARTICLE_COMMENT',
//                            'T_PS_ARTICLE_RATING','T_PS_BANNER',
//                            'T_PS_BANNER_CATEGORY','T_PS_CATEGORY','T_PS_CATEGORY_ARTICLE','T_PS_CQ','T_PS_CQ_FIELD',
//                            'T_PS_EVENT','T_PS_EVENT_ARTICLE','T_PS_GROUP_CATEGORY','T_PS_HOMEPAGE_CATEGORY','T_PS_MEDIA',
//                            'T_PS_MENU','T_PS_MENU_POSITION','T_PS_OPTION','T_PS_PHOTO_GALLERY','T_PS_PHOTO_GALLERY_DETAIL','T_PS_POLL',
//                            'T_PS_POLL_DETAIL','T_PS_SPOTLIGHT','T_PS_SPOTLIGHT_POSITION','T_PS_STATS_VISITORS','T_PS_STICKY','T_PS_SUBSCRIBER','T_PS_USER_CATEGORY',
//                            'T_PS_WEBLINK','T_PS_WEBSITE','T_PS_WEBSITE_THEME_WIDGET');
        
        $arr_table = array('T_CORES_APPLICATION','T_CORES_CALENDAR','T_CORES_GROUP','T_CORES_GROUP_FUNCTION','T_CORES_LIST','T_CORES_LISTTYPE',
                            'T_CORES_OU','T_CORES_USER','T_CORES_USER_FUNCTION','T_CORES_USER_GROUP',
                            );
//        $this->model->mssql_to_mysql($arr_table);
    }
    
    //update du lieu luu thua C_DEFAULT_CATEGORY and C_DEFAULT_WEBSITE
//    public function  update_extra_data()
//    {
//        $this->model->update_extra_data();
//    }
//    
    public function article_entity_endcode()
    {
        $this->model->article_entity_endcode();
    }
//    
//    public function abc()
//    {
//        $this->model->abc();
//    }
}
?>