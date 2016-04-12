<?php

if (!defined('SERVER_ROOT'))
    exit('No direct script access allowed');

class category_Controller extends Controller
{

    public function __construct()
    {
        Session::init();
        //kiem tra dang nhap
        Session::get('user_id') or $this->login_admin();
        
        Session::check_permission('QL_DANH_SACH_CHUYEN_MUC') or $this->access_denied();
        
        parent::__construct('admin', 'category');
        $v_lang_id                                   = session::get('session_lang_id');
        $this->view->template->arr_all_lang          = $this->model->qry_all_lang();
        $this->view->template->arr_count_article     = $this->model->gp_qry_count_article();
        $this->view->template->arr_all_grant_website = $this->model->gp_qry_all_website_by_user($v_lang_id);
    }

    public function main()
    {
        $this->view->render('main');
    }

    public function dsp_all_category()
    {
        $other_clause = ' And C.FK_WEBSITE = ' . Session::get('session_website_id');

        $VIEW_DATA['arr_all_category'] = $this->model->qry_all_category($other_clause);

        $this->view->render('dsp_all_category', $VIEW_DATA);
    }

    public function swap_category_order()
    {
       

        //validate
        $item1 = get_request_var('item1', '');
        $item2 = get_request_var('item2', '');

        if (intval($item2) == $item2 && intval($item1) == $item1)
        {
            $this->model->swap_category_order($item1, $item2);
        }
        else
        {
            echo 'Du lieu khong hop le';
        }
    }

    public function dsp_single_category($id = 0)
    {
        $id                               = intval($id);
        $VIEW_DATA['arr_single_category'] = $this->model->qry_single_category($id);
        if (empty($VIEW_DATA['arr_single_category']) && $id != 0)
        {
            die(__('this object is nolonger available!'));
        }
        $other_clause = " And FK_WEBSITE = " . Session::get('session_website_id');
        if ($id)
        {
            $internal_order = $VIEW_DATA['arr_single_category']['C_INTERNAL_ORDER'];
            $other_clause .= " And C_INTERNAL_ORDER Not Like '$internal_order%' ";
        }

        $VIEW_DATA['arr_all_category'] = $this->model->qry_all_category($other_clause);
        $this->view->render('dsp_single_category', $VIEW_DATA);
    }

    public function update_category($no_use_var = 0)
    {
        $this->model->update_category();
    }

    public function delete_category()
    {
       
        $this->model->delete_category();
    }

    public function dsp_featured_category()
    {
      

        $view_data['arr_all_featured'] = $this->model->qry_all_featured();
        $view_data['website_id']       = Session::get('session_website_id', 0);
        $this->view->render('dsp_featured_category', $view_data);
    }

    //cac $_GET option:
    public function dsp_all_category_svc($website_id)
    {
       
        $website_id               = intval($website_id);
        $qry_cat_other_clause     = ' And FK_WEBSITE = ' . $website_id;
        $data['arr_all_category'] = $this->model->qry_all_category($qry_cat_other_clause);
        $data['arr_all_website']  = $this->model->qry_all_website();
        $data['website_id']       = $website_id;
        $this->view->render('dsp_all_category_svc', $data);
    }

    public function insert_featured_category()
    {
       
        $this->model->insert_featured_category();
    }

    public function delete_featured_category()
    {
    
        $this->model->delete_featured_category();
    }

    public function swap_featured_order()
    {
    
        $this->model->swap_featured_order();
    }

    public function write_cache()
    {
      
        get_system_config_value(CFGKEY_CACHE) == 'true' or $this->model->exec_fail($this->view->get_controller_url(), __('cache write error'));
        $website_id    = Session::get('session_website_id');

        //cache sticky category
        $cache = new GP_Cache();
        $balance = $cache->create_featured_category_cache($website_id);
        if ($balance == FALSE)
        {
            $this->model->exec_fail($this->view->get_controller_url(), __('cache write error'));
        }
        $v_active_tab = get_post_var('hdn_active_tab', 0); 
        $this->model->exec_done($this->view->get_controller_url(), array('hdn_active_tab' => $v_active_tab));
    }
    
    public function move_vinhphuctv_category()
    {
    	$this->model->move_vinhphuctv_category();
    }

}

?>
