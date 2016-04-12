<?php

class weblink_Controller extends Controller
{

    public function __construct()
    {
        parent::__construct('admin', 'weblink');
        Session::init();
        //dang nhap
        (Session::get('user_id')) or $this->login_admin();

        $v_lang_id = session::get('session_lang_id');
        $this->view->template->arr_all_lang = $this->model->qry_all_lang();
        $this->view->template->arr_count_article = $this->model->gp_qry_count_article();
        $this->view->template->arr_all_grant_website = $this->model->gp_qry_all_website_by_user($v_lang_id);

        $this->model->goback_url = $this->view->get_controller_url();
        Session::check_permission('QL_DANH_SACH_WEBLINK') or $this->access_denied();
    }

    public function main()
    {
        $data['arr_all_weblink'] = $this->model->qry_all_weblink();
        $this->view->render('dsp_main', $data);
    }

    public function dsp_single_weblink($id)
    {
        $id = (int) $id;
        $data['v_id'] = $id;
        $data['arr_single_weblink'] = array();
        if ($id > 0)
        {
            $data['arr_single_weblink'] = $this->model->qry_single_weblink($id);
        }
        
        $data['arr_all_group_type'] = $this->model->qry_all_group_type();

        $this->view->render('dsp_single_weblink', $data);
    }

    public function update_weblink()
    {
        $this->model->update_weblink();
    }
    
    public function swap_order()
    {
        $item1 = get_post_var('item1');//id
        $item2 = get_post_var('item2');//id swap

        $table = 't_ps_weblink';        
        $pk_col = 'PK_WEBLINK';
        $order_col = 'C_ORDER';
        $this->model->swap_order($table, $pk_col, $order_col, $item1, $item2);
    }

    public function delete_weblink()
    {
        $this->model->delete_weblink();
    }
    
}

?>
