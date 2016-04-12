<?php if (!defined('SERVER_ROOT')) exit('No direct script access allowed');

class user_Controller extends Controller {

    function __construct()
    {
        parent::__construct('admin', 'user');
        $this->check_login();
        $this->view->template->show_left_side_bar =FALSE;
        $this->view->template->show_div_website = FALSE;
        $this->view->template->arr_count_article = $this->model->gp_qry_count_article();
        //$this->view->template->show_div_website = FALSE;
        //Lang::load_lang('lang_vi');
        @session::init();
        $v_lang_id = session::get('session_lang_id');
        $this->view->template->arr_all_lang = $this->model->qry_all_lang();
        $this->view->template->arr_all_grant_website = $this->model->gp_qry_all_website_by_user($v_lang_id);
        
        if(session::check_permission('XEM_DANH_SACH_SU_KIEN')==FALSE)
        {
            die('Bạn không có quyền thực hiện chức năng này !!!');
        }
    }
    protected function access_denied()
    {
        return NULL;
    }

    function main()
    {
        //Kiem tra quyen
        //(Session::get('is_admin') == 1) Or die($this->access_denied());
         //phần dành cho dashboard
        $this->dsp_all_sub_ou();
    }

    function dsp_all_sub_ou($ou_id=1)
    {
        //Kiem tra quyen
        //@(Session::get('is_admin') == 1) Or die($this->access_denied());

        $ou_id = replace_bad_char($ou_id);

        $VIEW_DATA['ou_id'] = $ou_id;

        $VIEW_DATA['arr_all_sub_ou']        = $this->model->qry_all_sub_ou($ou_id);
        $VIEW_DATA['arr_all_user_by_ou']    = $this->model->qry_all_user_by_ou($ou_id);
        $VIEW_DATA['arr_ou_path']           = $this->model->qry_ou_path($ou_id);

        $VIEW_DATA['arr_all_group_by_ou']   = $this->model->qry_all_group_by_ou($ou_id);

        $this->view->render('dsp_all_sub_ou', $VIEW_DATA);
    }

    public function dsp_single_user($user_id)
    {
        //Kiem tra quyen
        #(Session::get('is_admin') == 1) Or die($this->access_denied());
        //echo 'asdasdasd';
        $user_id = replace_bad_char($user_id);
        if (!( preg_match( '/^\d*$/', trim($user_id)) == 1 ))
        {
            $user_id = 0;
        }
        $v_parent_ou_id = replace_bad_char($_REQUEST['parent_ou_id']);

        $VIEW_DATA['arr_parent_ou_path']    = $this->model->qry_ou_path($v_parent_ou_id);

        //Danh sach chuyen trang
        $VIEW_DATA['arr_all_website_option'] = $this->model->qry_all_website_option();

        //Cac Group ma NSD nay dang tham gia
        $VIEW_DATA['arr_all_group_by_user']= $this->model->qry_all_group_by_user($user_id);
        
        $VIEW_DATA['arr_all_job_title'] = $this->model->qry_all_job_title();

        $VIEW_DATA['arr_single_user'] = $this->model->qry_single_user($user_id);
        $this->view->render('dsp_single_user', $VIEW_DATA);
    }

    public function dsp_single_group($group_id)
    {
        //Kiem tra quyen
        #(Session::get('is_admin') == 1) Or die($this->access_denied());

        $group_id = replace_bad_char($group_id);
        if (!( preg_match( '/^\d*$/', trim($group_id)) == 1 ))
        {
            $group_id = 0;
        }

        $v_parent_ou_id = replace_bad_char($_REQUEST['parent_ou_id']);
        $VIEW_DATA['arr_parent_ou_path']    = $this->model->qry_ou_path($v_parent_ou_id);

        $VIEW_DATA['arr_single_group'] = $this->model->qry_single_group($group_id);
        $VIEW_DATA['arr_all_user_by_group'] = $this->model->qry_all_user_by_group($group_id);

        $VIEW_DATA['arr_all_website_option']    = $this->model->qry_all_website_option();

        $this->view->render('dsp_single_group', $VIEW_DATA);
    }
    public function dsp_all_ou_to_add()
    {
        $VIEW_DATA['arr_all_ou'] = $this->model->qry_all_ou();
        $this->view->render('dsp_all_ou_to_add',$VIEW_DATA);
    }
    public function dsp_single_ou($ou_id)
    {
        //Kiem tra quyen
        #(Session::get('is_admin') == 1) Or die($this->access_denied());

        $ou_id = replace_bad_char($ou_id);
        if (!( preg_match( '/^\d*$/', trim($ou_id)) == 1 ))
        {
            $ou_id = 0;
        }

        $v_parent_ou_id = replace_bad_char($_REQUEST['parent_ou_id']);

        $VIEW_DATA['arr_parent_ou_path']    = $this->model->qry_ou_path($v_parent_ou_id);
        $VIEW_DATA['arr_single_ou']         = $this->model->qry_single_ou($ou_id);

        $this->view->render('dsp_single_ou', $VIEW_DATA);
    }

    public function dsp_ou_tree()
    {
        //Kiem tra quyen
        (Session::get('is_admin') == 1) Or die($this->access_denied());

        $VIEW_DATA['arr_ou_tree'] = $this->model->qry_ou_tree();
        $this->view->render('dsp_ou_tree', $VIEW_DATA);
    }

    public function update_ou()
    {
        //Kiem tra quyen
        (Session::get('is_admin') == 1) Or die($this->access_denied());

        $v_parent_ou_id       = replace_bad_char($_POST['hdn_parent_ou_id']);

        $this->model->goback_url = $this->view->get_controller_url() . 'dsp_all_sub_ou/' . $v_parent_ou_id;
        $this->model->update_ou();
    }

    public function delete_ou()
    {
        //Kiem tra quyen
        (Session::get('is_admin') == 1) Or die($this->access_denied());

        $v_parent_ou_id       = $this->get_post_var('hdn_current_ou_id');
        $this->model->goback_url = $this->view->get_controller_url() . 'dsp_all_sub_ou/' . $v_parent_ou_id;
        $this->model->delete_ou();
    }

    public function update_user()
    {
        //var_dump($_POST);exit;
        //Kiem tra quyen
        //echo Session::get('is_admin');
        (Session::get('is_admin')==1) Or die($this->access_denied());

        $v_parent_ou_id       = $this->get_post_var('hdn_current_ou_id');

        //$this->model->goback_url = $this->view->get_controller_url() . 'dsp_all_sub_ou/' . $v_parent_ou_id;
        $this->model->update_user();
    }

    public function delete_user()
    {
        //Kiem tra quyen
        (Session::get('is_admin') == 1) Or die($this->access_denied());

        $v_parent_ou_id       = $this->get_post_var('hdn_current_ou_id');
        $this->model->goback_url = $this->view->get_controller_url() . 'dsp_all_sub_ou/' . $v_parent_ou_id;

        $this->model->delete_user();
    }

    public function update_group()
    {
        //Kiem tra quyen
        (Session::get('is_admin') == 1) Or die($this->access_denied());
       //var_dump($_POST);exit;
        $v_parent_ou_id       = $this->get_post_var('hdn_current_ou_id');
        $this->model->goback_url    = $this->view->get_controller_url() . 'dsp_all_sub_ou/' . $v_parent_ou_id;

        $this->model->update_group();
    }
    public function delete_group()
    {
        //Kiem tra quyen
        (Session::get('is_admin') == 1) Or die($this->access_denied());

        $v_parent_ou_id       = $this->get_post_var('hdn_current_ou_id');
        $this->model->goback_url = $this->view->get_controller_url() . 'dsp_all_sub_ou/' . $v_parent_ou_id;

        $this->model->delete_group();
    }

    public function dsp_all_user_to_add()
    {
        //Kiem tra quyen
        //(Session::get('is_admin') == 1) Or die($this->access_denied());

        $VIEW_DATA['arr_all_user_to_add'] = $this->model->qry_all_user_to_add();
        $this->view->render('dsp_all_user_to_add', $VIEW_DATA);
    }

    //Dich vu hien thi danh sach NSD theo phong ban (vật lý)
    public function dsp_all_user_by_ou_to_add()
    {
        $VIEW_DATA['arr_all_user_to_add'] = $this->model->qry_all_user_by_ou_to_add();

        $this->view->render('dsp_all_user_by_ou_to_add', $VIEW_DATA);
    }

    public function dsp_all_group_to_add()
    {
        $VIEW_DATA['arr_all_group_to_add'] = $this->model->qry_all_group_to_add();
        $this->view->render('dsp_all_group_to_add', $VIEW_DATA);
    }

    public function dsp_all_user_and_group_to_add()
    {
        $v_my_dept_only = isset($_GET['my_dept_only']) ? replace_bad_char($_GET['my_dept_only']) : 0;
        $VIEW_DATA['arr_all_user_to_add'] = $this->model->qry_all_user_to_add($v_my_dept_only);
        $VIEW_DATA['arr_all_group_to_add'] = $this->model->qry_all_group_to_add($v_my_dept_only);
        $this->view->render('dsp_all_user_and_group_to_add', $VIEW_DATA);
    }

    //HIen thi danh sach tat ca user, group de phan quyen
    public function dsp_all_user_and_group_to_grand()
    {
        $v_filter = '';
        if (isset($_POST['txt_filter']))
        {
            $v_filter = replace_bad_char($_POST['txt_filter']);
        }

        $VIEW_DATA['arr_all_user_to_grand']     = $this->model->qry_all_user_to_grand($v_filter);
        $VIEW_DATA['arr_all_group_to_grand']    = $this->model->qry_all_group_to_grand($v_filter);
        $VIEW_DATA['filter']                    = $v_filter;

        $this->view->render('dsp_all_user_and_group_to_grand', $VIEW_DATA);
    }

    function dsp_single_user_to_grand()
    {
        $VIEW_DATA['user_id'] = replace_bad_char($_REQUEST['hdn_item_id']);
        $VIEW_DATA['user_name'] = replace_bad_char($_REQUEST['hdn_item_name']);
        $VIEW_DATA['user_type'] = replace_bad_char($_REQUEST['type']);

        if (!( preg_match( '/^\d*$/', trim($VIEW_DATA['user_id'])) == 1 ))
        {
           exit;
        }

        $VIEW_DATA['arr_all_application_option']    = $this->model->qry_all_application_option();

        $this->view->render('dsp_single_user_to_grand', $VIEW_DATA);
    }
    function dsp_single_group_to_grand()
    {
        $VIEW_DATA['group_id'] = replace_bad_char($_REQUEST['hdn_item_id']);
        $VIEW_DATA['group_name'] = replace_bad_char($_REQUEST['hdn_item_name']);

        if (!( preg_match( '/^\d*$/', trim($VIEW_DATA['user_id'])) == 1 ))
        {
           exit;
        }

        $VIEW_DATA['arr_all_application_option']    = $this->model->qry_all_application_option();

        $this->view->render('dsp_single_group_to_grand', $VIEW_DATA);
    }
    public function arp_user_permit_on_category()
    {
        $VIEW_DATA['user_id']           = isset($_REQUEST['user_id']) ? replace_bad_char($_REQUEST['user_id']) : 0;

        if (!( preg_match( '/^\d*$/', trim($VIEW_DATA['user_id'])) == 1 ))
        {
           exit;
        }
        echo json_encode($this->model->qry_single_user_permit_on_category($VIEW_DATA['user_id']));
    }    

    public function arp_user_permit_on_website()
    {
        $VIEW_DATA['user_id']           = isset($_REQUEST['user_id']) ? replace_bad_char($_REQUEST['user_id']) : 0;
        $VIEW_DATA['website_id']        = isset($_REQUEST['website_id']) ? replace_bad_char($_REQUEST['website_id']) : 0;

        if (!( preg_match( '/^\d*$/', trim($VIEW_DATA['user_id'])) == 1 ))
        {
           exit;
        }

        if (!( preg_match( '/^\d*$/', trim($VIEW_DATA['website_id'])) == 1 ))
        {
           exit;
        }
       
        echo json_encode($this->model->qry_single_user_permit_on_website($VIEW_DATA['user_id'] , $VIEW_DATA['website_id']));
        
    }
    public function arp_user_permit_without_website()
    {
        $VIEW_DATA['user_id']          = isset($_REQUEST['user_id']) ? replace_bad_char($_REQUEST['user_id']) : 0;
        
        if (!( preg_match( '/^\d*$/', trim($VIEW_DATA['user_id'])) == 1 ))
        {
           exit;
        }   
        
       echo json_encode($this->model->qry_single_user_permit_without_website($VIEW_DATA['user_id']));   
    }
    
    public function arp_group_permit_without_website()
    {
        $VIEW_DATA['group_id']          = isset($_REQUEST['group_id']) ? replace_bad_char($_REQUEST['group_id']) : 0;
        
        if (!( preg_match( '/^\d*$/', trim($VIEW_DATA['group_id'])) == 1 ))
        {
           exit;
        }   
        
       echo json_encode($this->model->qry_single_group_permit_without_website($VIEW_DATA['group_id']));
    }
    
    public function arp_group_permit_on_category()
    {
        $VIEW_DATA['group_id']          = isset($_REQUEST['group_id']) ? replace_bad_char($_REQUEST['group_id']) : 0;
        
        if (!( preg_match( '/^\d*$/', trim($VIEW_DATA['group_id'])) == 1 ))
        {
           exit;
        }

        if (!( preg_match( '/^\d*$/', trim($VIEW_DATA['website_id'])) == 1 ))
        {
           exit;
        }
        echo json_encode($this->model->qry_single_group_permit_on_category($VIEW_DATA['group_id']));
    }

    public function arp_group_permit_on_website()
    {
        $VIEW_DATA['group_id']          = isset($_REQUEST['group_id']) ? replace_bad_char($_REQUEST['group_id']) : 0;
        $VIEW_DATA['website_id']        = isset($_REQUEST['website_id']) ? replace_bad_char($_REQUEST['website_id']) : 0;

        if (!( preg_match( '/^\d*$/', trim($VIEW_DATA['group_id'])) == 1 ))
        {
           exit;
        }

        if (!( preg_match( '/^\d*$/', trim($VIEW_DATA['website_id'])) == 1 ))
        {
           exit;
        }
       
        echo json_encode($this->model->qry_single_group_permit_on_website($VIEW_DATA['group_id'] , $VIEW_DATA['website_id']));
    }

    public function update_user_permit()
    {
        $this->model->update_user_permit();
    }
    public function update_group_permit()
    {
        $this->model->update_group_permit();
    }

    public function dsp_website_permit($website_id = 0)
    {
        //Kiem tra quyen
        #(Session::get('is_admin') == 1) Or die($this->access_denied());

        #$VIEW_DATA['arr_all_permit'] = $this->model->qry_all_permit();

        //Danh sach chuyen muc
        //echo $website_id;exit;
        $VIEW_DATA['arr_all_category'] = $this->model->qry_all_category($website_id);

        $this->view->render('dsp_website_permit', $VIEW_DATA);
    }
}