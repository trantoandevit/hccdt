<?php

defined('DS') or die('no direct access');
require_once(SERVER_ROOT . DS . 'libs' . DS . 'recaptchalib.php');

define('PAGE_ARCHIVE', 'C_ARCHIVE_POSITION');
define('PAGE_HOME', 'C_HOME_POSITION');
define('PAGE_SINGLE', 'C_SINGLE_POSITION');

define('STATS_ONLINE', 1);
define('STATS_ALL', 2);

class frontend_Controller extends Controller
{

    function __construct()
    {
        ob_start();
        //ini_set('session.cookie_domain', SITE_ROOT);
        parent::__construct('frontend', 'frontend');
        @Session::init();
        $website_id = get_request_var('website_id', 0);

        if ($website_id == 0)
        {
            $website_id = $this->model->qry_default_website_id();
        }

        $this->website_id = $this->view->website_id = $this->model->website_id = $website_id;
        $arr_single_website = $this->model->qry_single_website($website_id);
        $this->website_code = $this->model->website_code = $arr_single_website['C_CODE'];
        $this->view->website_name = $this->model->website_name = $arr_single_website['C_NAME'];

        //Mobile
//        $detect = new Mobile_Detect();
//        if ($detect->isMobile() && Cookie::get('pc_mode', 0) == 0)
//        {
//            $arr_single_website['C_THEME_CODE'] = 'mobile-2014';
//        }
//        unset($detect);
        //End Mobile
        //Bots OR Crawler
        $v_user_agent_string = strtolower($_SERVER["HTTP_USER_AGENT"]);
        $this->is_bot = FALSE;
        /*
          if (preg_match('/(bot|crawler|coccoc|yahoo|bing|baidu)/', $v_user_agent_string, $matches, PREG_OFFSET_CAPTURE))
          {
          $arr_single_website['C_THEME_CODE'] = 'bots';
          $this->is_bot = TRUE;
          }
         */
//        $arr_single_website['C_THEME_CODE'] = 'mobile-2014';

        $this->theme_code = $this->view->theme_code = $this->model->theme_code = $arr_single_website['C_THEME_CODE'];

        //LienND 2013-03-22: Fake theme URL
        if (file_exists(SERVER_ROOT . '.htaccess'))
        {
            define('CONST_SITE_THEME_ROOT', FULL_SITE_ROOT . 'templates/' . strtolower($this->theme_code) . '/');
        }
        else
        {
            define('CONST_SITE_THEME_ROOT', FULL_SITE_ROOT . 'apps/frontend/themes/' . strtolower($this->theme_code) . '/');
        }

        $this->is_service = false;
        $this->model->update_statistic();
        if ($this->model->check_block_account() == 0)
        {
            $v_username = Session::get('citizen_login_name');
            Session::session_unset('citizen_login_name');
            Session::session_unset('citizen_name');
            Session::session_unset('citizen_login_id');
            Session::session_unset('citizen_email');
            Session::session_unset('account_' . $v_username);
            Session::session_unset('citizen_role');
        }
        $this->_check_login_cookie();
    }

    function __destruct()
    {
        //if ($this->is_service == false)
        //{
        //goi ham statistic
        //$this->do_statistic();
        //$this->model->update_statistic();
        //}
    }

    public function main()
    {
        $this->dsp_home_page();
    }

    private function get_default_data()
    {
        $VIEW_DATA['v_banner'] = $this->model->qry_banner();
        $VIEW_DATA['arr_all_website'] = $this->model->qry_all_website();
        if (!set_viewdata_data($this->website_id, 'menu', $VIEW_DATA['arr_all_menu_position']))
        {
            $VIEW_DATA['arr_all_menu_position'] = $this->model->gp_qry_all_menu_position($this->website_id);
        }
        return $VIEW_DATA;
    }

    public function dsp_home_page()
    {
        $website_id = $this->website_id;
        $VIEW_DATA['v_banner'] = $this->model->qry_banner();
        $VIEW_DATA['arr_all_website'] = $this->model->qry_all_website();
        $this->view->active_menu_top = 'home-page';
        //Lay du lieu menu
        if (!set_viewdata_data($this->website_id, 'menu', $VIEW_DATA['arr_all_menu_position']))
        {
            $VIEW_DATA['arr_all_menu_position'] = $this->model->gp_qry_all_menu_position($website_id);
        }
        //Render
        $this->view->render('dsp_home_page', $VIEW_DATA, $this->theme_code);
    }

    function svc_synthesis()
    {
        header('Content-type: application/json');
        //lay du lieu synthesis
        $month = Date('m');
        $max_dateime_update_record_history_start = $this->model->qry_max_date_history_start();
        $arr_synthesis = $this->model->qry_synthesis_of_district(1);

        require __DIR__ . '/frontend_views/svc_synthesis.php';
    }

    /**
     * dsp tra cuu hs qua ma hs 
     */
    public function dsp_lookup()
    {
        $arr_default_data = $this->get_default_data();
        $VIEW_DATA['v_banner'] = $arr_default_data['v_banner'];
        $VIEW_DATA['arr_all_website'] = $arr_default_data['arr_all_website'];
        $VIEW_DATA['arr_all_menu_position'] = $arr_default_data['arr_all_menu_position'];

        $VIEW_DATA['arr_loockup_link'] = $this->model->get_lookup_link();
        $VIEW_DATA['arr_all_member'] = $this->model->qry_all_member_have_level();
        $this->view->render('dsp_lookup', $VIEW_DATA, $this->theme_code);
    }

    public function slideshow_data()
    {
        $month = date('m');
        $year = date('Y');

        $VIEW_DATA['arr_synthesis'] = $this->model->qry_synthesis(0);
        $VIEW_DATA['arr_all_progress_fields'] = $this->model->qry_all_progress_fields(date('m'), date('Y'));
        $VIEW_DATA['arr_all_village'] = $this->model->qry_synthesis_all_village(date('m'), date('Y'));

        $this->view->render('dsp_slideshow', $VIEW_DATA);
    }

    /**
     * chuyen huong function cho dvc truc tuyen
     * @param type $funtion
     */
    public function redirect_internet_record($funtion)
    {
        $this->$funtion();
    }

    /**
     * hien thi noi dung huong dan nop hs truc tuyen va danh sach member
     */
    public function dsp_guidance_internet_record()
    {
        $arr_default_data = $this->get_default_data();
        $VIEW_DATA['v_banner'] = $arr_default_data['v_banner'];
        $VIEW_DATA['arr_all_website'] = $arr_default_data['arr_all_website'];
        $VIEW_DATA['arr_all_menu_position'] = $arr_default_data['arr_all_menu_position'];
        $this->view->active_menu_top = 'public_service';
        $VIEW_DATA['arr_guidance'] = $this->model->qry_guidance_internet();
        $VIEW_DATA['arr_all_member'] = $this->model->qry_all_member_receive_record();
        $this->view->render('dsp_guidance_internet_record', $VIEW_DATA, $this->theme_code);
    }

    /**
     * danh sach TTHC nop qua mang
     */
    public function dsp_list_internet_record()
    {
        $arr_default_data = $this->get_default_data();
        $VIEW_DATA['v_banner'] = $arr_default_data['v_banner'];
        $VIEW_DATA['arr_all_website'] = $arr_default_data['arr_all_website'];
        $VIEW_DATA['arr_all_menu_position'] = $arr_default_data['arr_all_menu_position'];
        $this->view->active_menu_top = 'public_service';
        $this->view->menu_active = __FUNCTION__;
        $VIEW_DATA['arr_all_spec'] = $this->model->qry_all_spec();
        $VIEW_DATA['arr_all_member'] = $this->model->qry_all_member_receive_record_have_level();

        $v_member_id = get_request_var('member_id', 0);
        $v_spec_code = get_request_var('spec_code', '');
        $VIEW_DATA['arr_all_record_type'] = $this->model->qry_all_record_type_of_member($v_member_id, $v_spec_code);

        $this->view->render('dsp_list_internet_record', $VIEW_DATA, $this->theme_code);
    }

    /**
     * hien thi man hinh dang ky hs truc tuyen
     */
    public function dsp_submit_internet_record()
    {
        if ($this->model->check_block_account() == 1)
        {
            $VIEW_DATA['arr_single_citizen'] = $this->model->qry_single_account_citizen();
        }
        if (get_post_var('txt_record_no', '') != '')
        {
            $response = $this->do_insert_internet_record();
            $VIEW_DATA['response'] = $response;
        }

//        include_once SERVER_ROOT . 'libs' . DS . 'recaptchalib.php';
        include_once SERVER_ROOT . 'libs' . DS . 'Suid.php';

        $arr_default_data = $this->get_default_data();
        $VIEW_DATA['v_banner'] = $arr_default_data['v_banner'];
        $VIEW_DATA['arr_all_website'] = $arr_default_data['arr_all_website'];
        $VIEW_DATA['arr_all_menu_position'] = $arr_default_data['arr_all_menu_position'];
        $VIEW_DATA['arr_all_widget_position'] = $this->prepare_all_widget_position(PAGE_SINGLE);

        $VIEW_DATA['arr_all_member'] = $this->model->qry_all_member_receive_record_have_level();

        $record_type_id = get_request_var('record_type', 0);
        if ($record_type_id == 0)
        {
            $record_type_id = get_request_var('hdn_record_type_id', 0);
        }
        $VIEW_DATA['arr_record_type'] = $this->model->qry_record_type($record_type_id);
        $VIEW_DATA['arr_all_record_type'] = $this->model->qry_all_internet_record_type();
        $this->view->render('dsp_submit_internet_record', $VIEW_DATA, $this->theme_code);
    }

    /**
     * Thực hiện thêm mới hồ sơ
     * @return type
     */
    public function do_insert_internet_record()
    {
        return $this->model->do_insert_internet_record();
    }

    /**
     * man hinh tra cuu tong hop
     */
    public function dsp_synthesis()
    {
        $arr_default_data = $this->get_default_data();
        $VIEW_DATA['v_banner'] = $arr_default_data['v_banner'];
        $VIEW_DATA['arr_all_website'] = $arr_default_data['arr_all_website'];
        $this->view->active_menu_top = 'synthesis';
        $VIEW_DATA['arr_all_menu_position'] = $arr_default_data['arr_all_menu_position'];
        $VIEW_DATA['arr_all_widget_position'] = $this->prepare_all_widget_position(PAGE_SINGLE);
        $v_type = get_request_var('type', '');
        $v_method = get_request_var('method', '');
        $VIEW_DATA['content'] = '';
        if ($v_type == 'member')
        {
            ob_start();
            $this->dsp_synthesis_member($v_method);
            $VIEW_DATA['content'] = ob_get_clean();
        }
        elseif ($v_type == 'spec')
        {
            ob_start();
            $this->dsp_synthesis_spec($v_method);
            $VIEW_DATA['content'] = ob_get_clean();
        }
        elseif ($v_type == 'liveboard')
        {
            ob_start();
            $this->dsp_synthesis_liveboard();
            $VIEW_DATA['content'] = ob_get_clean();
        }
        elseif ($v_type == 'chart')
        {
            ob_start();
            $this->dsp_synthesis_chart($v_method);
            $VIEW_DATA['content'] = ob_get_clean();
        }

        $this->view->render('dsp_synthesis', $VIEW_DATA, $this->theme_code);
    }

    /**
     * man hinh bieu do
     */
    private function dsp_synthesis_chart($v_method)
    {
        $VIEW_DATA['arr_year'] = $this->model->get_year();
        $v_month     = get_post_var('sel_month', (int) Date('m'));
        $v_year      = get_post_var('sel_year', (int) Date('Y'));
        switch ($v_method)
        {
            case 'tien_do':
                $VIEW_DATA['arr_all_member'] = $this->model->qry_all_member();
                $VIEW_DATA['arr_all_spec'] = $this->model->qry_all_spec_exists_record();
                
                //dk loc
                $v_spec_code = get_post_var('sel_spec_code', '');
                $v_member_id = get_post_var('hdn_village_id', 0);
                $VIEW_DATA['arr_synthesis_chart'] = $this->model->qry_synthesis_chart($v_month, $v_year, $v_member_id, $v_spec_code);
                $this->view->render('dsp_progress_chart', $VIEW_DATA);
                break;
            case 'so_sanh':
                
                $v_compare_type = get_post_var('sel_compare_type', 0);
                $VIEW_DATA['arr_synthesis_chart'] = $this->model->qry_synthesis_compare_chart($v_month, $v_year, $v_compare_type);
                $this->view->render('dsp_compare_chart', $VIEW_DATA);
                break;
        }
    }

    /**
     * man hinh danh sach bang theo doi truc tuyen
     */
    private function dsp_synthesis_liveboard()
    {
        $VIEW_DATA['arr_all_liveboard'] = $this->model->qry_all_live_board();
        $this->view->render('dsp_synthesis_liveboard', $VIEW_DATA);
    }

    /**
     * man hinh hien thi theo don vi
     * @param type $v_method
     */
    private function dsp_synthesis_member($v_method)
    {
        //Lay danh sach don vi
        $VIEW_DATA['arr_all_member'] = $this->model->qry_all_member();
        //Danh sanh linh vuc dan tiep nhan ho so
        $VIEW_DATA['arr_all_spec'] = $this->model->qry_all_spec_exists_record();
        //dk loc
        $v_period = get_post_var('rad_period','month');
        $v_spec_code = get_post_var('sel_spec_code', '');
        $v_member_id = get_post_var('sel_village', '');
        if($v_period == 'month')
        {
            $v_month = get_post_var('sel_month', (int) Date('m'));
            $v_year  = get_post_var('sel_year_month', (int) Date('Y'));
            $VIEW_DATA['arr_all_report_data'] = $this->model->qry_synthesis($v_month, $v_year, $v_member_id, $v_spec_code);
        }
        else
        {
//            $v_quarter = get_post_var('sel_quarter', '1');
//            $v_year = get_post_var('sel_year_quarter', (int) Date('Y'));
//            $VIEW_DATA['arr_all_report_data'] = array();
//            
//            $v_end_month = $v_quarter * 3;
//            $v_start_month = $v_end_month - 2;
//            for($i = $v_start_month; $i <= $v_end_month; $i++)
//            {
//                
//                if($i == $v_start_month)
//                {
//                    $VIEW_DATA['arr_all_report_data'] = $this->model->qry_synthesis($i, $v_year, $v_member_id, $v_spec_code);
//                }
//                else
//                {
//                    $VIEW_DATA['arr_all_report_data'] = $this->model->qry_synthesis($i, $v_year, $v_member_id, $v_spec_code);
//                }
//            }
        }
       
        $VIEW_DATA['arr_year'] = $this->model->get_year();
        


        switch ($v_method)
        {
//            case 'thu_ly_va_giai_quyet':
//                $this->view->render('dsp_dv_thu_ly_va_giai_quyet', $VIEW_DATA);
//                break;
//            case 'tu_choi':
//                $this->view->render('dsp_dv_tu_choi', $VIEW_DATA);
//                break;
//            case 'bo_sung';
//                $this->view->render('dsp_dv_bo_sung', $VIEW_DATA);
//                break;
//            case 'tiep_nhan':
            default:
                $this->view->render('dsp_dv_tiep_nhan', $VIEW_DATA);
                break;
        }
    }

    /**
     * man hinh hien thi theo linh vuc
     * @param type $v_method
     */
    private function dsp_synthesis_spec($v_method)
    {
        //Lay danh sach don vi
        $arr_all_ou = $this->model->qry_all_member();

        //Danh sanh linh vuc
        $arr_all_spec = $this->model->qry_all_spec();

        //dk loc
        $v_spec_code = get_post_var('sel_spec_code', '');
        $v_member_id = get_post_var('sel_village', '');
        $v_month = get_post_var('sel_month', (int) Date('m'));
        $v_year = get_post_var('sel_year', (int) Date('Y'));

        $arr_all_report_data = $this->model->qry_synthesis_by_spec($v_month, $v_year, $v_member_id, $v_spec_code);

        $VIEW_DATA['arr_year'] = $this->model->get_year();
        $VIEW_DATA['arr_all_member'] = $arr_all_ou;
        $VIEW_DATA['arr_all_spec'] = $arr_all_spec;
        $VIEW_DATA['arr_all_report_data'] = $arr_all_report_data;

        switch ($v_method)
        {
            case 'thu_ly_va_giai_quyet':
                $this->view->render('dsp_lv_thu_ly_va_giai_quyet', $VIEW_DATA);
                break;
            case 'tu_choi':
                $this->view->render('dsp_lv_tu_choi', $VIEW_DATA);
                break;
            case 'bo_sung';
                $this->view->render('dsp_lv_bo_sung', $VIEW_DATA);
                break;
            case 'tiep_nhan':
            default:
                $this->view->render('dsp_lv_tiep_nhan', $VIEW_DATA);
                break;
        }
    }

    public function dsp_single_event()
    {
        $website_id = $this->website_id;
        $event_id = get_request_var('event_id', 0);

        $VIEW_DATA['v_banner'] = $this->model->qry_banner();
        $VIEW_DATA['arr_all_website'] = $this->model->qry_all_website();
        if (!set_viewdata_data($this->website_id, 'menu', $VIEW_DATA['arr_all_menu_position']))
        {
            $VIEW_DATA['arr_all_menu_position'] = $this->model->gp_qry_all_menu_position($website_id);
        }
        $VIEW_DATA['arr_all_widget_position'] = $this->prepare_all_widget_position(PAGE_ARCHIVE);

        $VIEW_DATA['arr_single_event'] = $this->model->qry_single_event($website_id, $event_id);
        $this->view->render('dsp_single_event', $VIEW_DATA, $this->theme_code);
    }

    function dsp_all_rss()
    {
        $data = $this->get_default_data();
        $data['arr_all_widget_position'] = $this->prepare_all_widget_position(PAGE_ARCHIVE);
        $data['arr_all_category'] = $this->model->qry_all_category();
        $data['title'] = 'RSS';
        $this->view->render('dsp_all_rss', $data, $this->theme_code);
    }

    public function dsp_single_cq()
    {
        $website_id = $this->website_id;
        $VIEW_DATA['v_banner'] = $this->model->qry_banner();
        $VIEW_DATA['arr_all_website'] = $this->model->qry_all_website();
        $this->view->widget = 'citizens_question'; // show widget        

        if (!set_viewdata_data($this->website_id, 'menu', $VIEW_DATA['arr_all_menu_position']))
        {
            $VIEW_DATA['arr_all_menu_position'] = $this->model->gp_qry_all_menu_position($website_id);
        }
        //Lay du lieu CHUYEN MUC NOI BAT cua chuyen trang
        if (!set_viewdata_data($this->website_id, 'featured_cat', $VIEW_DATA['arr_all_cat_art']))
        {
            $VIEW_DATA['arr_all_cat_art'] = $this->model->gp_qry_all_featured_category($website_id);
        }
        $VIEW_DATA['arr_all_widget_position'] = $this->prepare_all_widget_position(PAGE_ARCHIVE);

        $VIEW_DATA['arr_all_cq_field'] = $this->model->qry_all_cq_field($website_id);

        $results = $this->model->qry_single_cq();
        $VIEW_DATA['arr_single_cq'] = isset($results['arr_single_cq']) ? $results['arr_single_cq'] : array();
        $VIEW_DATA['arr_cq_connection'] = isset($results['arr_cq_connection']) ? $results['arr_cq_connection'] : array();
        $this->view->render('dsp_single_cq', $VIEW_DATA, $this->theme_code);
    }

    public function dsp_set_question()
    {
        $website_id = $this->website_id;

        $VIEW_DATA['v_banner'] = $this->model->qry_banner();
        $VIEW_DATA['arr_all_website'] = $this->model->qry_all_website();
        $this->view->widget = 'citizens_question'; // show widget

        if (!set_viewdata_data($this->website_id, 'menu', $VIEW_DATA['arr_all_menu_position']))
        {
            $VIEW_DATA['arr_all_menu_position'] = $this->model->gp_qry_all_menu_position($website_id);
        }
        $VIEW_DATA['arr_all_widget_position'] = $this->prepare_all_widget_position(PAGE_ARCHIVE);
        //Lay du lieu CHUYEN MUC NOI BAT cua chuyen trang
        if (!set_viewdata_data($this->website_id, 'featured_cat', $VIEW_DATA['arr_all_cat_art']))
        {
            $VIEW_DATA['arr_all_cat_art'] = $this->model->gp_qry_all_featured_category($website_id);
        }
        $VIEW_DATA['arr_all_cq_field'] = $this->model->qry_all_cq_field($website_id);
        $VIEW_DATA['arr_all_field'] = $this->model->qry_all_cq_field($website_id);

        $this->view->render('dsp_set_question', $VIEW_DATA, $this->theme_code);
    }

    public function dsp_single_category()
    {
        $website_id = $this->website_id;
        $category_id = get_request_var('category_id', 0);

        $date = get_request_var('date');
        $dd = $mm = $yyyy = -1;
        if (strlen($date) == 8)
        {
            $yyyy = substr($date, 0, 4);
            $mm = substr($date, 4, 2);
            $dd = substr($date, 6, 2);
        }
        if (checkdate($mm, $dd, $yyyy))
        {
            $VIEW_DATA['yyyy'] = $yyyy;
            $VIEW_DATA['mm'] = $mm;
            $VIEW_DATA['dd'] = $dd;
        }
        else
        {
            $VIEW_DATA['yyyy'] = -1;
            $VIEW_DATA['mm'] = -1;
            $VIEW_DATA['dd'] = -1;
        }

        $MODEL_DATA = $this->model->qry_single_category($website_id, $category_id);

        $VIEW_DATA['arr_single_category'] = $MODEL_DATA['arr_single_category'];
        $VIEW_DATA['arr_all_sub_category_with_article'] = $MODEL_DATA['arr_all_sub_category_with_article'];

        //Lay du lieu TIN NOI BAT trong ngay
        if (!set_viewdata_data($this->website_id, 'breaking_news', $VIEW_DATA['arr_all_breaking_news']))
        {
            $VIEW_DATA['arr_all_breaking_news'] = $this->model->gp_qry_all_breaking_news($website_id);
        }
        $this->view->menu_active = __FUNCTION__;
        $this->view->widget = 'article'; // show widget

        $VIEW_DATA['v_banner'] = $this->model->qry_banner($category_id);
        $VIEW_DATA['arr_all_website'] = $this->model->qry_all_website();
        if (!set_viewdata_data($this->website_id, 'menu', $VIEW_DATA['arr_all_menu_position']))
        {
            $VIEW_DATA['arr_all_menu_position'] = $this->model->gp_qry_all_menu_position($website_id);
        }
        //Lay du lieu CHUYEN MUC NOI BAT cua chuyen trang
        if (!set_viewdata_data($this->website_id, 'featured_cat', $VIEW_DATA['arr_all_cat_art']))
        {
            $VIEW_DATA['arr_all_cat_art'] = $this->model->gp_qry_all_featured_category($website_id);
        }
        if ($this->is_bot == FALSE)
        {
            $VIEW_DATA['arr_all_widget_position'] = $this->prepare_all_widget_position(PAGE_ARCHIVE);
            $VIEW_DATA['arr_root_category'] = $this->model->qry_root_category($category_id);

            //Tin bai moi
            if (!set_viewdata_data($this->website_id, 'latest_article', $VIEW_DATA['arr_all_latest_article']))
            {
                $VIEW_DATA['arr_all_latest_article'] = $this->model->gp_qry_all_latest_article($website_id);
            }
        }
        else
        {
            $VIEW_DATA['arr_all_widget_position'] = Array();
            $VIEW_DATA['arr_root_category'] = Array();
            //Tin bai moi
            $VIEW_DATA['arr_all_article_new'] = Array();
        }

        $this->view->render('dsp_single_category', $VIEW_DATA, $this->theme_code);
    }

    public function dsp_rss()
    {
        $website_id = $this->website_id;
        $category_id = get_request_var('category_id', 0);

        $VIEW_DATA['arr_single_category'] = $this->model->qry_single_category($website_id, $category_id);
        $this->view->render('dsp_rss', $VIEW_DATA, $this->theme_code);
    }

    public function dsp_tags()
    {
        $website_id = $this->website_id;
        $v_tags = get_request_var('tags');

        $VIEW_DATA['v_banner'] = $this->model->qry_banner();
        $VIEW_DATA['arr_all_website'] = $this->model->qry_all_website();
        if (!set_viewdata_data($this->website_id, 'menu', $VIEW_DATA['arr_all_menu_position']))
        {
            $VIEW_DATA['arr_all_menu_position'] = $this->model->gp_qry_all_menu_position($website_id);
        }
        $VIEW_DATA['arr_all_widget_position'] = $this->prepare_all_widget_position(PAGE_ARCHIVE);

        $VIEW_DATA['arr_all_article'] = $this->model->qry_tags($website_id, $v_tags);
        $this->view->render('dsp_tags', $VIEW_DATA, $this->theme_code);
    }

    public function dsp_single_article()
    {
        $website_id = (int) $this->website_id;
        $category_id = (int) get_request_var('category_id', 0);
        $article_id = (int) get_request_var('article_id', 0);
        $VIEW_DATA['v_banner'] = $this->model->qry_banner($category_id);
        $VIEW_DATA['arr_all_website'] = $this->model->qry_all_website();
        $this->view->widget = 'article'; // show widget

        if (!set_viewdata_data($this->website_id, 'menu', $VIEW_DATA['arr_all_menu_position']))
        {
            $VIEW_DATA['arr_all_menu_position'] = $this->model->gp_qry_all_menu_position($website_id);
        }

        $VIEW_DATA['arr_single_article'] = $this->model->qry_single_article($website_id, $category_id, $article_id);
        $v_tags = get_array_value($VIEW_DATA['arr_single_article'], 'C_TAGS');

        if ($this->is_bot == FALSE)
        {
            //tin cung su kien
            //$VIEW_DATA['arr_related_article'] = $this->model->qry_related_article($article_id, $v_tags);
            //tin moi cap nhat cua chuyen muc
            $VIEW_DATA['arr_new_category_article'] = $this->model->qry_new_category_article($category_id, $article_id);
            $VIEW_DATA['arr_all_widget_position'] = $this->prepare_all_widget_position(PAGE_SINGLE);

            //tin khac
            $VIEW_DATA['arr_other_news'] = $this->model->qry_all_other_article($category_id, $article_id);

            //attachment
            $VIEW_DATA['arr_attachment'] = $this->model->qry_all_attachment($article_id);

            //Tin bai moi cua chuyen trang
            if (!set_viewdata_data($this->website_id, 'latest_article', $VIEW_DATA['arr_all_latest_article']))
            {
                $VIEW_DATA['arr_all_latest_article'] = $this->model->gp_qry_all_latest_article($website_id);
            }

            //Lay du lieu TIN NOI BAT trong ngay
            if (!set_viewdata_data($this->website_id, 'breaking_news', $VIEW_DATA['arr_all_breaking_news']))
            {
                $VIEW_DATA['arr_all_breaking_news'] = $this->model->gp_qry_all_breaking_news($website_id);
            }
            //Lay du lieu CHUYEN MUC NOI BAT cua chuyen trang
            if (!set_viewdata_data($this->website_id, 'featured_cat', $VIEW_DATA['arr_all_cat_art']))
            {
                $VIEW_DATA['arr_all_cat_art'] = $this->model->gp_qry_all_featured_category($website_id);
            }
        }
        else
        {
            //tin moi cap nhat cua chuyen muc
            $VIEW_DATA['arr_new_category_article'] = Array();
            $VIEW_DATA['arr_all_widget_position'] = Array();

            //tin khac
            $VIEW_DATA['arr_other_news'] = Array();

            //attachment
            $VIEW_DATA['arr_attachment'] = Array();

            //Tin bai moi cua chuyen trang
            $VIEW_DATA['arr_all_latest_article'] = Array();
            $VIEW_DATA['arr_all_breaking_news'] = Array();
        }

        $this->model->update_article_views($article_id);
        $this->view->render('dsp_single_article', $VIEW_DATA, $this->theme_code);
    }

    public function dsp_all_comment($article_id)
    {
        $this->model->db->debug = 0;
        $article_id = (int) $article_id;
        $data['arr_all_comment'] = $this->model->qry_all_comment($article_id);

        $this->view->render('dsp_all_comment', $data, $this->theme_code);
    }

    public function dsp_all_cq()
    {
        $website_id = $this->website_id;
        $VIEW_DATA['v_banner'] = $this->model->qry_banner();
        $VIEW_DATA['arr_all_website'] = $this->model->qry_all_website();
        $this->view->widget = 'citizens_question'; // show widget   
        if (!set_viewdata_data($this->website_id, 'menu', $VIEW_DATA['arr_all_menu_position']))
        {
            $VIEW_DATA['arr_all_menu_position'] = $this->model->gp_qry_all_menu_position($website_id);
        }
        //Lay du lieu CHUYEN MUC NOI BAT cua chuyen trang
        if (!set_viewdata_data($this->website_id, 'featured_cat', $VIEW_DATA['arr_all_cat_art']))
        {
            $VIEW_DATA['arr_all_cat_art'] = $this->model->gp_qry_all_featured_category($website_id);
        }
        $VIEW_DATA['arr_all_widget_position'] = $this->prepare_all_widget_position(PAGE_ARCHIVE);

        $VIEW_DATA['arr_all_cq_field'] = $this->model->qry_all_cq_field($website_id);

        $VIEW_DATA['arr_all_cq'] = $this->model->qry_all_cq($website_id);

        $this->view->render('dsp_all_cq', $VIEW_DATA, $this->theme_code);
    }

    public function dsp_all_category()
    {
        $data = $this->get_default_data();
        $this->view->dsp_widget = 'article'; // show widget
        $data['arr_all_category'] = $this->model->qry_all_category();
        $this->view->render('dsp_all_category', $data, $this->theme_code);
    }

    public function dsp_print_article()
    {
        $website_id = $this->website_id;
        $category_id = get_request_var('category_id', 0);
        $article_id = get_request_var('article_id', 0);
        $VIEW_DATA['v_banner'] = $this->model->qry_banner($category_id);
        $VIEW_DATA['arr_single_article'] = $this->model->qry_single_article($website_id, $category_id, $article_id);

        $this->view->render('dsp_print_article', $VIEW_DATA, $this->theme_code);
    }

    public function dsp_recaptcha()
    {
        $this->view->render('dsp_recaptcha');
    }

    public function dsp_search()
    {
        $data = $this->get_default_data();
        $data['title'] = __('search');
        $data['arr_all_widget_position'] = $this->prepare_all_widget_position(PAGE_ARCHIVE);
        $data['arr_all_article'] = $this->model->qry_all_article_by_fulltext_search();
        //Lay du lieu TIN NOI BAT trong ngay
        $data['arr_all_breaking_news'] = $this->model->gp_qry_all_breaking_news($this->website_id);
        $data['arr_all_category'] = $this->model->qry_all_category();
        $this->view->render('dsp_search', $data, $this->theme_code);
    }

    public function send_mail()
    {
        $this->is_service = true;

        $v_name = get_post_var('txt_name_mail', '');
        $v_email = get_post_var('txt_email_name', '');
        $v_message = get_post_var('txt_message', '');
        $v_url = get_post_var('hdn_url', '');
        $v_email_to = get_post_var('txt_to', '');
        $v_subject = get_post_var('txt_subject', get_system_config_value(CFGKEY_UNIT_NAME));
        $v_cc = get_post_var('txt_cc', '');

        //validate
        if ($v_email_to == '' or $v_name == '' or $v_url == '')
        {
            die(__('please fill all required fields'));
        }

        require_once SERVER_ROOT . 'libs/swift/lib/swift_required.php';

        // Tạo đối tượng transport
        $server_name = get_system_config_value(CFGKEY_MAIL_SERVER);
        $port = get_system_config_value(CFGKEY_MAIL_PORT);
        $ssl = get_system_config_value(CFGKEY_MAIL_SSL) == 'true' ? 'ssl' : null;
        $transport = Swift_SmtpTransport::newInstance($server_name, $port, $ssl);
        $transport->setUsername(get_system_config_value(CFGKEY_MAIL_ACCOUNT));
        $transport->setPassword(get_system_config_value(CFGKEY_MAIL_PASSWORD));

        // Tạo đối tượng mailer sẽ đãm nhận nhiệm vụ gởi mail đi
        $mailer = Swift_Mailer::newInstance($transport);

        //Tạo message để gởi đi
        $message_content = get_system_config_value(CFGKEY_UNIT_NAME) . "\n";
        $message_content .= $v_name;
        $message_content .= $v_email ? "($v_email)" : '';
        $message_content .= ' ' . __('send you an article') . ":\n";
        $message_content .= $v_url . "\n";
        $message_content .= $v_message ? __('with a message') . ":\n" . $v_message : '';

        $message = Swift_Message::newInstance($v_subject, $message_content);
        if ($v_subject == '')
        {
            $message->setSubject(get_system_config_value(CFGKEY_UNIT_NAME));
        }

        //Tạo cc
        ($v_cc == '')? : $message->setCc($v_cc);

        $message->setFrom(get_system_config_value(CFGKEY_MAIL_ADD));
        $message->addTo($v_email_to);

        // Gởi message
        $result = $mailer->send($message);
        if ($result == true)
        {
            $return_msg = __('thank you for using our email function');
        }
        else
        {
            $return_msg = __('error occurs during sending');
        }
        echo $return_msg;
    }

    public function dsp_poll_result($poll_id)
    {
        $poll_id = (int) $poll_id;
        $data['arr_single_poll'] = $this->model->qry_single_poll($poll_id);
        $data['arr_all_opt'] = $this->model->qry_all_poll_detail($poll_id);
        if (!$data['arr_single_poll'])
        {
            die(__('this object is nolong available'));
        }
        $this->view->render('dsp_poll_result', $data, $this->theme_code);
    }

    public function prepare_all_widget_position($page_type)
    {
        $arr_all_position = $this->model->qry_all_widget_position();
        if ($arr_all_position == null)
        {
            return null;
        }
        $v_default_position = str_replace(' ', '', $arr_all_position['C_ALL_POSITION']);
        $v_default_position = explode(',', $v_default_position);

        $v_load_position = isset($arr_all_position[$page_type]) ? $arr_all_position[$page_type] : '';
        $v_load_position = str_replace(' ', '', $v_load_position);
        $v_load_position = explode(',', $v_load_position);

        $v_load_position = array_intersect($v_load_position, $v_default_position);

        $arr_return = array();
        if (!empty($v_load_position))
        {
            foreach ($v_load_position as $single_pos)
            {
                $arr_current_widget = $this->model->qry_all_widget($this->website_code, $this->theme_code, $single_pos);
                $n = count($arr_current_widget);
                for ($i = 0; $i < $n; $i++)
                {
                    $v_code = $arr_current_widget[$i]['C_WIDGET_CODE'];
                    $v_param = $arr_current_widget[$i]['C_PARAM'];
                    $v_method = 'dsp_widget_' . $v_code;
                    if (method_exists($this, $v_method))
                    {
                        $arr_current_widget[$i]['C_CONTENT'] = $this->{'dsp_widget_' . $v_code}($v_param);
                    }
                    else
                    {
                        $arr_current_widget[$i]['C_CONTENT'] = '';
                    }
                }
                $arr_return[$single_pos] = $arr_current_widget;
            }
        }
        return $arr_return;
    }

    public function handle_widget()
    {
        $this->is_service = true;

        $args = isset($_REQUEST) ? $_REQUEST : '';
        $v_code = isset($args['code']) ? $args['code'] : '';
        $v_method = 'handle_widget_' . $v_code;
        $v_param = isset($args['param']) ? $args['param'] : '';

        if (method_exists($this, $v_method))
        {
            $this->{$v_method}($v_param);
        }
    }

    public function send_comment($comment_id = 0)
    {
        //captcha google
//        $this->is_service = true;
//
//        $challenge = get_post_var('recaptcha_challenge_field');
//        $response  = get_post_var('recaptcha_response_field');
//        $resp      = recaptcha_check_answer(
//                RECAPTCHA_PRIVATE_KEY
//                , $_SERVER["REMOTE_ADDR"]
//                , $challenge
//                , $response
//        );
//
//        if (!$resp->is_valid)
//        {
//            $return_msg = __('captcha not valid');
//            die($return_msg);
//        }
//        
        if ($comment_id == 0 OR $comment_id == '' OR $comment_id == NULL)
        {
            //include captcha
            $captcha_url = SERVER_ROOT . 'apps/frontend/themes/' . $this->theme_code . '/captcha/';
            require $captcha_url . 'securimage.php';

            //kiem tra captcha
            $securimage = new Securimage();
            if ($securimage->check($_POST['txt_captcha_code']) == FALSE)
            {
                $return_msg = __('captcha not valid');
                die($return_msg);
            }
        }
        echo $this->model->send_comment();
    }

//widget news photos
    private function dsp_widget_image_news($args = '')
    {
        $VIEWDATA = array();

        $VIEWDATA['quantity'] = '';
        $VIEWDATA['title'] = '';
        $VIEWDATA['widget_style'] = '';
        try
        {
            $args = @new SimpleXMLElement($args, LIBXML_NOCDATA);
            $VIEWDATA['widget_style'] = (string) $args->sel_image_news_color;
            $VIEWDATA['quantity'] = (int) $args->txt_image_news_quantity;
            $VIEWDATA['title'] = (string) $args->txt_image_news_title;
        }
        catch (Exception $ex)
        {
            
        }

        $VIEWDATA['arr_all_image_news'] = $this->model->qry_all_image_news($VIEWDATA['quantity']);
        ob_start();
        $this->view->render('dsp_widget_image_news', $VIEWDATA);
        return ob_get_clean();
    }

    //hWidget rating
    private function dsp_widget_rating($args = '')
    {
        $VIEWDATA = array();

        $VIEWDATA['quantity'] = '';
        $VIEWDATA['title'] = '';
        $VIEWDATA['widget_style'] = '';
        try
        {
            $args = @new SimpleXMLElement($args, LIBXML_NOCDATA);
            $VIEWDATA['widget_style'] = (string) $args->sel_rating_color;
            $VIEWDATA['quantity'] = (int) $args->txt_rating_quantity;
            $VIEWDATA['title'] = (string) $args->txt_rating_title;
        }
        catch (Exception $ex)
        {
            
        }

        $VIEWDATA['arr_all_rating'] = $this->model->qry_all_rating($VIEWDATA['quantity']);
        ob_start();
        $this->view->render('dsp_widget_rating', $VIEWDATA);
        return ob_get_clean();
    }

//widget weblink
    private function dsp_widget_weblink($args = '')
    {
        $widget_style = '';
        try
        {
            $args = @new SimpleXMLElement($args, LIBXML_NOCDATA);
            $widget_style = (string) $args->widget_style;
            $group_weblink_id = (string) $args->group_weblink_id;
            $title_weblink = (string) $args->title_weblink;
        }
        catch (Exception $ex)
        {
            
        }

        $data['widget_style'] = $widget_style;
        $data['group_weblink_id'] = $group_weblink_id;
        $data['title_weblink'] = $title_weblink;
        $data['arr_all_weblink'] = $this->model->qry_all_weblink($group_weblink_id);
        ob_start();
        $this->view->render('dsp_widget_weblink', $data);
        return ob_get_clean();
    }

    //widget video_clip
    private function dsp_widget_video_clip($args = '')
    {
        $data['quantity'] = '';
        $data['title'] = '';
        try
        {
            $args = @new SimpleXMLElement($args, LIBXML_NOCDATA);
            $data['quantity'] = (int) $args->txt_video_clip_quantity;
            $data['title'] = (int) $args->txt_video_clip_title;
        }
        catch (Exception $ex)
        {
            
        }

        $data['arr_all_new_video_clip'] = $this->model->qry_new_video($data['quantity']);
        ob_start();
        $this->view->render('dsp_widget_video_clip', $data);
        return ob_get_clean();
    }

    //widget media_article
    private function dsp_widget_media_article($args = '')
    {
        $data['video_limit'] = 3;
        $data['gallery_limit'] = 3;
        try
        {
            $args = @new SimpleXMLElement($args, LIBXML_NOCDATA);
            $data['video_limit'] = (int) $args->video_limit;
            $data['gallery_limit'] = (int) $args->gallery_limit;
        }
        catch (Exception $ex)
        {
            
        }
        $data['arr_all_new_video'] = $this->model->qry_new_video($data['video_limit']);
        $data['arr_all_pg'] = $this->model->qry_new_photo_gallery($data['gallery_limit']);

        ob_start();
        $this->view->render('dsp_widget_media_article', $data);
        return ob_get_clean();
    }

    //widget subscribe
    private function dsp_widget_subscribe($args = '')
    {
        $data['widget_style'] = '';
        try
        {
            $args = @new SimpleXMLElement($args, LIBXML_NOCDATA);
            $data['widget_style'] = $args->widget_style;
        }
        catch (Exception $ex)
        {
            
        }
        ob_start();
        $this->view->render('dsp_widget_subscribe', $data);
        return ob_get_clean();
    }

    //widget statistic
    private function dsp_widget_statistic($args = '')
    {
        $widget_style = '';
        $data['stats_online'] = $this->model->get_statistic(STATS_ONLINE);
        $data['stats_all'] = $this->model->get_statistic(STATS_ALL);
        try
        {
            $args = @new SimpleXMLElement($args, LIBXML_NOCDATA);
            $widget_style = (string) $args->widget_style;
        }
        catch (Exception $ex)
        {
            
        }

        $data['widget_style'] = $widget_style;
        ob_start();
        $this->view->render('dsp_widget_statistic', $data);
        return ob_get_clean();
    }

//widget adv --------------------------------------------------------------
    private function dsp_widget_adv($args = '')
    {
        $data['selected_position'] = 0;
        try
        {
            $args = @new SimpleXMLElement($args, LIBXML_NOCDATA);
            $data['selected_position'] = (int) $args->selected_position;
        }
        catch (Exception $ex)
        {
            
        }
        $data['arr_all_image'] = $this->model->qry_all_adv_image($data['selected_position']);

        $n = count($data['arr_all_image']);
        $html = '<div class="clear"></div><div class="widget_parent_adv">';
        for ($i = 0; $i < $n; $i++)
        {
            $item = $data['arr_all_image'][$i];
            $v_url = $item['C_URL'];
            $v_image = SITE_ROOT . 'upload/' . $item['C_FILE_NAME'];

            $arr_temp = explode('.', $v_image);
            $v_extension = strtolower(array_pop($arr_temp));
            $v_alt = $item['C_NAME'];

            if ($v_extension != 'swf')
            {
                $html .= '<div class="widget widget-adv" data-code="adv">';
                $html .= '<a href="' . $v_url . '"><img  src="' . $v_image . '" alt="' . $v_alt . '"/></a>';
                $html .= '</div>';
            }
            else
            {
                $v_server_image = str_replace(SITE_ROOT, '', $v_image);
                $v_server_image = SERVER_ROOT . $v_server_image;
                $arr_img_info = getimagesize($v_server_image);
                $v_height = $arr_img_info['1'];

                $html .= '<div class="widget widget-adv" data-code="adv" style="width:100%;display:block;">';
                $html .='<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="" style="width:100%;" height="' . $v_height . 'px">
                            <param name="movie" value="">
                            <param name="quality" value="high">
                            <param name="wmode" value="transparent">
                            <param name="SCALE" value="exactfit">
                            <embed id="banner_img" src="' . $v_image . '" scale="exactfit" wmode="transparent" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" style="width:100%;height:' . $v_height . 'px;">
                        </object>';
                $html .= '</div>';
            }
        }
        $html .= "</div>";
        return $html;
    }

//end widget adv------------------------------------------------------------
//widget category slide
    private function dsp_widget_online_tv($args = '')
    {
        $data['selected_listtype'] = 0;
        $data['title'] = '';
        try
        {
            $args = @new SimpleXMLElement($args, LIBXML_NOCDATA);
            $data['selected_listtype'] = (int) $args->selected_listtype;
            $data['title'] = (string) $args->title;
            $data['chk_radio'] = (string) $args->chk_radio;
        }
        catch (Exception $ex)
        {
            
        }

        $data['arr_all_online_tv'] = $this->model->qry_all_online_tv($data['selected_listtype']);

        ob_start();
        $this->view->render('dsp_widget_online_tv', $data);
        $html = ob_get_clean();
        return $html;
    }

//end
//widget category slide
    private function dsp_widget_gallery($args = '')
    {
        $data['some_gallery_show'] = 0;
        $data['title'] = '';
        try
        {
            $args = @new SimpleXMLElement($args, LIBXML_NOCDATA);
            $data['some_gallery_show'] = (int) $args->some_gallery_show;
        }
        catch (Exception $ex)
        {
            
        }

        $data['arr_all_photo_gallery'] = $this->model->qry_new_photo_gallery($data['some_gallery_show']);

        ob_start();
        $this->view->render('dsp_widget_gallery', $data);
        $html = ob_get_clean();
        return $html;
    }

//end
//widget category slide
    private function dsp_widget_category_slide($args = '')
    {
        $data['sel_widget_category_slide'] = 0;
        try
        {
            $args = @new SimpleXMLElement($args, LIBXML_NOCDATA);
            $data['sel_widget_category_slide'] = (int) $args->sel_widget_category_slide;
            $data['txt_some_news_show'] = (int) $args->txt_some_news_show;
        }
        catch (Exception $ex)
        {
            
        }

        $arr_data = $this->model->qry_all_article_of_widget_category_slide($data['sel_widget_category_slide'], $data['txt_some_news_show']);
        $data['arr_category'] = $arr_data['arr_category'];
        $data['arr_all_article'] = $arr_data['arr_all_article'];

        ob_start();
        $this->view->render('dsp_widget_category_slide', $data);
        $html = ob_get_clean();
        return $html;
    }

//end
//widget poll --------------------------------------------------------------
    private function dsp_widget_poll($args = '')
    {
        $data['v_widget_style'] = '';
        $data['v_index'] = (int) Session::get('widget_poll_count');
        Session::set('widget_poll_count', $data['v_index'] + 1);
        $data['v_poll_id'] = 0;
        $data['v_widget_name'] = __('poll');

        try
        {
            $args = @new SimpleXMLElement($args, LIBXML_NOCDATA);
            $data['v_poll_id'] = (int) $args->poll_id;
            $data['v_widget_style'] = (string) $args->widget_style;
        }
        catch (Exception $ex)
        {
            
        }
        $data['arr_single_poll'] = $this->model->qry_single_poll($data['v_poll_id']);
        $data['arr_all_opt'] = $this->model->qry_all_poll_detail($data['v_poll_id']);
        ob_start();
        $this->view->render('dsp_widget_poll', $data);
        $html = ob_get_clean();
        return $html;
    }

    private function handle_widget_poll($args = '')
    {
        $v_poll_id = (int) get_request_var('pid');
        $v_choice = (int) get_request_var('aid');
        if (empty($_POST) && !Cookie::get('WIDGET_POLL_' . $v_poll_id))
        {
            $this->view->render('handle_widget_poll');
        }
        else
        {
            //kiem tra da vote
            if (Cookie::get('WIDGET_POLL_' . $v_poll_id))
            {
                $this->dsp_poll_result($v_poll_id);
                return;
            }


            //include captcha
            $captcha_url = SERVER_ROOT . 'apps/frontend/themes/' . $this->theme_code . '/captcha/';
            require $captcha_url . 'securimage.php';
            //kiem tra captcha
            $securimage = new Securimage();
            if ($securimage->check($_POST['txt_captcha_code']) == FALSE)
            {
                $str = __('captcha not valid');
                $url = $this->view->get_controller_url() . 'handle_widget'
                        . "&code=poll&pid=$v_poll_id&aid=$v_choice";
                $this->model->exec_fail($url, $str);
                exit;
            }

            $this->model->handle_widget_poll($v_poll_id, $v_choice);
            $this->dsp_poll_result($v_poll_id);
            return;
        }
    }

//end widget poll-----------------------------------------------------------
//widget free_free_text --------------------------------------------------------
    private function dsp_widget_free_text($args = '')
    {
        $v_name = __('free text');
        $v_title = '';
        $v_content = '';
        $v_style = '';
        $v_content_only = '';

        try
        {
            $args = @new SimpleXMLElement($args, LIBXML_NOCDATA);
            $v_title = (string) $args->title;
            $v_content = (string) $args->content;
            $v_style = (string) $args->widget_style;
            $v_content_only = (string) $args->content_only;
        }
        catch (Exception $ex)
        {
            
        }

        //LienND update 2013-08-22: parse Video
        $v_content = str_replace('<x>', '', $v_content);
        $pattern = "/\[VIDEO\](.*)\[\/VIDEO\]/i";
        $v_content = preg_replace_callback($pattern, 'replace_video', $v_content, -1, $count);

        if ($v_content_only)
        {
            $html = "<div class='widget' data-code='free-text'>$v_content</div>";
        }
        else
        {
            $html = "
                 <div class='widget widget-free-text $v_style' data-code='free_text'>
                    <div class='widget-header'><h6>$v_title</h6></div>
                    <div class='widget-content'>$v_content</div>
                </div>
                ";
        }

        return $html;
    }

//end widget free_text ---------------------------------------------------------
//widget most_visited ------------------------------------------------------
    /**
     * Hien thi widget Tin bai xem nhieu nhat
     * @param string $args
     * @return string
     */
    private function dsp_widget_most_visited($args = '')
    {
        $website_id = $this->website_id;
        $data['v_quantity'] = 5;
        $data['v_style'] = '';
        $data['v_widget_title'] = __('most visited');
        try
        {
            $args = @new SimpleXMLElement($args, LIBXML_NOCDATA);
            $data['v_quantity'] = (int) $args->dsp_quantity;
            $data['v_style'] = (string) $args->widget_style;
        }
        catch (Exception $ex)
        {
            
        }

        if (!set_viewdata_data($this->website_id, 'most_view', $data['arr_all_article']))
        {
            $data['arr_all_article'] = $this->model->gp_qry_all_most_view_article($website_id, $data['v_quantity']);
        }
        //Han che theo dung so luong cau hinh
        array_splice($data['arr_all_article'], $data['v_quantity']);

        ob_start();
        $this->view->render('dsp_widget_most_visited', $data);
        $html = ob_get_clean();
        return $html;
    }

//end widget most visited --------------------------------------------------
//widget support ------------------------------------------------------------
    public function dsp_widget_support($args = '')
    {
        if (!defined('TV_SCHEDULE_SVC_URL'))
        {
            define('TV_SCHEDULE_SVC_URL', 'http://langsontv.vn/broadcast');
        }

        $data['widget_style'] = '';
        try
        {
            $args = @new SimpleXMLElement($args, LIBXML_NOCDATA);
            $data['widget_style'] = (string) $args->widget_style;
        }
        catch (Exception $ex)
        {
            
        }
        ob_start();
        $this->view->render('dsp_widget_support', $data);
        $html = ob_get_clean();
        return $html;
    }

//end widget support ------------------------------------------------------
//widget weather ------------------------------------------------------------
    public function dsp_widget_weather($args = '')
    {
//        if (!defined('TV_SCHEDULE_SVC_URL'))
//        {
//            define('TV_SCHEDULE_SVC_URL', 'http://langsontv.vn/broadcast');
//        }

        $data['txt_weather_woeid'] = 0;
        $data['txt_weather_title'] = '';
        try
        {
            $args = @new SimpleXMLElement($args, LIBXML_NOCDATA);
            $data['txt_weather_woeid'] = (int) $args->txt_weather_woeid;
            $data['txt_weather_title'] = (string) $args->txt_weather_title;
        }
        catch (Exception $ex)
        {
            
        }
        ob_start();
        $this->view->render('dsp_widget_weather', $data);
        $html = ob_get_clean();
        return $html;
    }

//end widget weather ------------------------------------------------------
//widget spotlight -----------------------------------------------------------
    private function dsp_widget_spotlight($args = '')
    {
        $data['widget_style'] = '';
        $data['selected'] = 0;
        $data['display_mode'] = 'basic';

        try
        {
            $args = @new SimpleXMLElement($args, LIBXML_NOCDATA);
            $data['widget_style'] = (string) $args->widget_style;
            $data['selected'] = (int) $args->spotlight_position;
            $data['display_mode'] = (string) $args->display_mode;
        }
        catch (Exception $ex)
        {
            
        }
        $data['arr_single_position'] = $this->model->qry_single_spotlight_pos($data['selected']);
        $file_name = _CONST_SERVER_CACHE_ROOT . $this->website_id . DS . 'spotlight' . DS . $data['selected'] . '.html';
        $data['arr_all_spotlight'] = null;
        if (get_system_config_value(CFGKEY_CACHE) == 'true')
        {
            $data['arr_all_spotlight'] = get_cache_data($file_name);
        }
        if ($data['arr_all_spotlight'] == null)
        {
            $data['arr_all_spotlight'] = $this->model->qry_all_spotlight($data['selected']);
        }
        ob_start();
        $this->view->render('dsp_widget_spotlight', $data);
        $html = ob_get_clean();
        return $html;
    }

    function insert_cq($website_id)
    {
        $this->db->debug = 0;
        $website_id = replace_bad_char($website_id);

        $challenge = get_post_var('recaptcha_challenge_field');
        $response = get_post_var('recaptcha_response_field');
        $resp = recaptcha_check_answer(
                RECAPTCHA_PRIVATE_KEY
                , $_SERVER["REMOTE_ADDR"]
                , $challenge
                , $response
        );

        if (!$resp->is_valid)
        {

            echo(__('captcha not valid'));
            exit();
        }
        echo $this->model->insert_cq($website_id);
    }

//end widget spotlight ---------------------------------------------------------
//widget event
    public function dsp_widget_event($args = '')
    {
        $data['widget_style'] = '';
        $data['selected'] = 0;
        $data['display_mode'] = 'basic';
        $data['display_event_id'] = '';

        try
        {
            $args = @new SimpleXMLElement($args, LIBXML_NOCDATA);
            $data['widget_style'] = (string) $args->widget_style;
            //$data['selected']     = (int) $args->spotlight_position;
            $data['display_mode'] = (string) $args->display_mode;
            $data['display_event_id'] = (int) $args->display_event_id;
        }
        catch (Exception $ex)
        {
            
        }
        $data['arr_event_title'] = $this->model->qry_single_event_title($data['display_event_id']);
        $data['arr_all_event'] = $this->model->qry_single_event($this->website_id, $data['display_event_id']);
        ob_start();
        $this->view->render('dsp_widget_event', $data);
        $html = ob_get_clean();
        return $html;
    }

//end widget event
//widget event
    public function dsp_widget_single_category($args = '')
    {
        $data['limit'] = '3';
        $data['category_id'] = '';
        try
        {
            $args = @new SimpleXMLElement($args, LIBXML_NOCDATA);
            $data['limit'] = (string) $args->limit;
            $data['category_id'] = (string) $args->category_id;
        }
        catch (Exception $ex)
        {
            
        }

        $data['arr_single_category'] = $this->model->qry_article_of_category($this->website_id, $data['category_id'], $data['limit']);
        ob_start();
        $this->view->render('dsp_widget_single_category', $data);
        $html = ob_get_clean();
        return $html;
    }

//end widget event
    public function dsp_single_cq_field()
    {
        $website_id = $this->website_id;
        $field_id = get_request_var('field_id');

        $VIEW_DATA['v_banner'] = $this->model->qry_banner();
        $VIEW_DATA['arr_all_website'] = $this->model->qry_all_website();
        if (!set_viewdata_data($this->website_id, 'menu', $VIEW_DATA['arr_all_menu_position']))
        {
            $VIEW_DATA['arr_all_menu_position'] = $this->model->gp_qry_all_menu_position($website_id);
        }

        $VIEW_DATA['arr_all_widget_position'] = $this->prepare_all_widget_position(PAGE_ARCHIVE);

        $VIEW_DATA['arr_single_cq_field'] = $this->model->qry_single_cq_field($website_id, $field_id);

        $this->view->render('dsp_single_cq_field', $VIEW_DATA, $this->theme_code);
    }

    //thuc hien them stat
    public function update_statistic()
    {
        $this->model->update_statistic();
    }

    //dung js de yeu cau stat de tang toc do xu ly
    //return string js
    public function do_statistic()
    {
        $this->view->render('do_statistic');
    }

    public function rate_article()
    {
        $this->is_service = true;

        $article_id = (int) get_post_var('article_id');
        $rating_value = (int) get_post_var('rating_value');
        $session_key = 'ARTICLE_RATED_' . $article_id;
        $result_value = 0;

//        if (Session::get($session_key) == '')
//        {
        Session::set($session_key, '1');
        $result_value = $this->model->rate_article($article_id, $rating_value);
//        }
        echo $result_value;
    }

    public function create_seo_sitemap()
    {
        if ($_SERVER['REMOTE_ADDR'] == '210.245.83.5' OR $_SERVER['REMOTE_ADDR'] == '127.0.0.1')
        {
            //Lay danh sach chuyen trang
            $sql = 'Select PK_WEBSITE From T_PS_WEBSITE Where C_STATUS=1 Order By C_ORDER';
            $arr_all_website = $this->model->db->getCol($sql);

            set_time_limit(0);
            $v_xml_sitemap = '<?xml version="1.0" encoding="UTF-8"?><urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
            $v_xml_sitemap .= '<url><loc>' . SITE_ROOT . '</loc><lastmod>' . Date('c', time()) . '</lastmod></url>';
            foreach ($arr_all_website as $v_website_id)
            {
                $sql = "Select 
                            FA.PK_ARTICLE
                            ,FA.C_SLUG as C_SLUG_ART
                            ,top10.FK_CATEGORY as PK_CATEGORY
                            ,(Select C_SLUG From T_PS_CATEGORY Where PK_CATEGORY=top10.FK_CATEGORY) as C_SLUG_CAT
                            , $v_website_id as FK_WEBSITE
                            , FA.C_BEGIN_DATE
                        From T_PS_ARTICLE FA 
                            Right Join ( Select 
                                            all_id.PK_ARTICLE
                                            , all_id.RN
                                            , all_id.FK_CATEGORY
                                         From ( Select 
                                                    PK_ARTICLE
                                                    , ROW_NUMBER() Over(Order by C_BEGIN_DATE Desc) as RN         
                                                    ,FK_CATEGORY      
                                                From T_PS_ARTICLE A 
                                                    Right Join ( Select 
                                                                    FK_CATEGORY
                                                                    , CA.FK_ARTICLE                 
                                                                From T_PS_CATEGORY_ARTICLE as CA 
                                                                    Left Join T_PS_CATEGORY as C 
                                                                    On CA.FK_CATEGORY=C.PK_CATEGORY
                                                                Where C.FK_WEBSITE=$v_website_id                                        
                                                               ) mca 
                                                    On mca.FK_ARTICLE=A.PK_ARTICLE
                                                Where A.C_STATUS=3
                                                    And dateDiff(mi, A.C_BEGIN_DATE, getDate()) >= 0
                                                    And dateDiff(mi, getDate(), A.C_END_DATE) >= 0                             
                                                ) all_id
                                        ) top10
                            On FA.PK_ARTICLE=top10.PK_ARTICLE";
                $arr_all_article = $this->model->db->getAll($sql);
                for ($i = 0, $n = sizeof($arr_all_article); $i < $n; $i++)
                {
                    $v_article_id = $arr_all_article[$i]['PK_ARTICLE'];
                    $v_slug_art = $arr_all_article[$i]['C_SLUG_ART'];
                    $v_category_id = $arr_all_article[$i]['PK_CATEGORY'];
                    $v_slug_cat = $arr_all_article[$i]['C_SLUG_CAT'];
                    $v_website_id = $arr_all_article[$i]['FK_WEBSITE'];
                    $v_begin_date = $arr_all_article[$i]['C_BEGIN_DATE'];

                    $v_slug_art = preg_replace('/([^a-zA-Z0-9-])/', '', $v_slug_art);
                    $v_slug_cat = preg_replace('/([^a-zA-Z0-9-])/', '', $v_slug_cat);

                    $v_begin_date = Date('c', $v_begin_date);

                    $v_url = build_url_article($v_slug_cat, $v_slug_art, $v_website_id, $v_category_id, $v_article_id);
                    $v_xml_sitemap .= "<url><loc>$v_url</loc><lastmod>$v_begin_date</lastmod></url>";
                }//end for article
            }//end for website
            $v_xml_sitemap .= '</urlset>';
            file_put_contents(SERVER_ROOT . 'sitemap.xml', $v_xml_sitemap);
        }//end if IP 
    }

    //end func

    /**
     * them moi dat bao
     */
    public function insert_magazine_subscriptions()
    {
        $this->model->insert_magazine_subscriptions();
    }

    /**
     * dsp hien thi thong tin toa soan
     */
    public function dsp_office_info()
    {
        $website_id = $this->website_id;
        $category_id = get_request_var('category_id', 0);
        $article_id = get_request_var('article_id', 0);

        $VIEW_DATA['v_banner'] = $this->model->qry_banner($category_id);

        $VIEW_DATA['arr_all_website'] = $this->model->qry_all_website();
        $this->view->active_menu_top = 'office_info';
        //Lay du lieu menu
        if (!set_viewdata_data($this->website_id, 'menu', $VIEW_DATA['arr_all_menu_position']))
        {
            $VIEW_DATA['arr_all_menu_position'] = $this->model->gp_qry_all_menu_position($website_id);
        }

        //Lay du lieu TIN NOI BAT trong ngay
        $VIEW_DATA['arr_all_breaking_news'] = $this->model->gp_qry_all_breaking_news($website_id);

        //Lay danh sach widgets cua HOMEPGE
        $VIEW_DATA['arr_all_widget_position'] = $this->prepare_all_widget_position(PAGE_HOME);

        //Lay danh sach tin bai doc nhieu nhat
        $VIEW_DATA['arr_all_most_visited'] = $this->model->qry_most_visited_article(_CONST_DEFAULT_LIMIT_MOST_VISITED);

        //lay chi tiet cua tin bai thong tin toa soan
        $VIEW_DATA['arr_single_article'] = $this->model->qry_single_article($website_id, $category_id, $article_id);

        $this->view->render('dsp_office_info', $VIEW_DATA, $this->theme_code);
    }

    /**
     * dsp hien thi lien ket trang
     */
    public function dsp_weblink()
    {
        $website_id = $this->website_id;
        $category_id = get_request_var('category_id', 0);
        $article_id = get_request_var('article_id', 0);

        $VIEW_DATA['v_banner'] = $this->model->qry_banner($category_id);

        $VIEW_DATA['arr_all_website'] = $this->model->qry_all_website();
        $this->view->active_menu_top = 'weblink';
        //Lay du lieu menu
        if (!set_viewdata_data($this->website_id, 'menu', $VIEW_DATA['arr_all_menu_position']))
        {
            $VIEW_DATA['arr_all_menu_position'] = $this->model->gp_qry_all_menu_position($website_id);
        }

        //Lay du lieu TIN NOI BAT trong ngay
        $VIEW_DATA['arr_all_breaking_news'] = $this->model->gp_qry_all_breaking_news($website_id);

        //Lay danh sach widgets cua HOMEPGE
        $VIEW_DATA['arr_all_widget_position'] = $this->prepare_all_widget_position(PAGE_HOME);

        //Lay danh sach tin bai doc nhieu nhat
//        $VIEW_DATA['arr_all_most_visited'] = $this->model->qry_most_visited_article(_CONST_DEFAULT_LIMIT_MOST_VISITED);
        //lay chi tiet cua tin bai thong tin toa soan
        $VIEW_DATA['arr_all_weblink'] = $this->model->qry_all_weblink();

        $this->view->render('dsp_weblink', $VIEW_DATA, $this->theme_code);
    }

    /**
     * hien thi so do site
     */
    public function dsp_all_sitemap()
    {
        $website_id = $this->website_id;
        $VIEW_DATA['v_banner'] = $this->model->qry_banner();

        $VIEW_DATA['arr_all_website'] = $this->model->qry_all_website();
        $this->view->active_menu_top = 'sitemap';
        //su dung menu cache
        if (!set_viewdata_data($this->website_id, 'menu', $VIEW_DATA['arr_all_menu_position']))
        {
            $VIEW_DATA['arr_all_menu_position'] = $this->model->gp_qry_all_menu_position($website_id);
        }
        //Lay du lieu TIN NOI BAT trong ngay
        $VIEW_DATA['arr_all_breaking_news'] = $this->model->gp_qry_all_breaking_news($website_id);

        //Lay danh sach widgets cua HOMEPGE
        $VIEW_DATA['arr_all_widget_position'] = $this->prepare_all_widget_position(PAGE_ARCHIVE);

        $this->view->render('dsp_all_sitemap', $VIEW_DATA, $this->theme_code);
    }

    /**
     * dsp gop y phan hoi
     */
    public function dsp_feedback()
    {
        $website_id = $this->website_id;
        $VIEW_DATA['v_banner'] = $this->model->qry_banner();
        $VIEW_DATA['arr_all_website'] = $this->model->qry_all_website();
        if (!set_viewdata_data($this->website_id, 'menu', $VIEW_DATA['arr_all_menu_position']))
        {
            $VIEW_DATA['arr_all_menu_position'] = $this->model->gp_qry_all_menu_position($website_id);
        }
        //Lay du lieu CHUYEN MUC NOI BAT cua chuyen trang
        if (!set_viewdata_data($this->website_id, 'featured_cat', $VIEW_DATA['arr_all_cat_art']))
        {
            $VIEW_DATA['arr_all_cat_art'] = $this->model->gp_qry_all_featured_category($website_id);
        }
        $VIEW_DATA['arr_all_widget_position'] = $this->prepare_all_widget_position(PAGE_ARCHIVE);
        $this->view->menu_active = __FUNCTION__;
        $this->view->active_menu_top = 'feedback';

        //Lay du lieu TIN NOI BAT trong ngay
        $VIEW_DATA['arr_all_breaking_news'] = $this->model->gp_qry_all_breaking_news($website_id);

        $this->view->render('dsp_feedback', $VIEW_DATA, $this->theme_code);
    }

    /**
     * dsp gop y phan hoi
     */
    public function dsp_all_feedback()
    {
        $website_id = $this->website_id;
        $VIEW_DATA['v_banner'] = $this->model->qry_banner();
        $VIEW_DATA['arr_all_website'] = $this->model->qry_all_website();
        if (!set_viewdata_data($this->website_id, 'menu', $VIEW_DATA['arr_all_menu_position']))
        {
            $VIEW_DATA['arr_all_menu_position'] = $this->model->gp_qry_all_menu_position($website_id);
        }
        //Lay du lieu CHUYEN MUC NOI BAT cua chuyen trang
        if (!set_viewdata_data($this->website_id, 'featured_cat', $VIEW_DATA['arr_all_cat_art']))
        {
            $VIEW_DATA['arr_all_cat_art'] = $this->model->gp_qry_all_featured_category($website_id);
        }
        $VIEW_DATA['arr_all_widget_position'] = $this->prepare_all_widget_position(PAGE_ARCHIVE);
        $this->view->menu_active = __FUNCTION__;
        $this->view->active_menu_top = 'feedback';
        //Lay du lieu TIN NOI BAT trong ngay
        $VIEW_DATA['arr_all_breaking_news'] = $this->model->gp_qry_all_breaking_news($website_id);


        //lay danh sach feedback public
        $VIEW_DATA['arr_all_public_feedback'] = $this->model->qry_all_public_feedback($website_id, 'all');

        $this->view->render('dsp_all_feedback', $VIEW_DATA, $this->theme_code);
    }

    /**
     * insert gop y phan hoi
     */
    public function insert_feedback()
    {
        $this->model->insert_feedback();
    }

    public function dsp_guidance()
    {
        $website_id = $this->website_id;
        $VIEW_DATA['v_banner'] = $this->model->qry_banner();
        $VIEW_DATA['arr_all_member'] = $this->model->qry_all_member();
        $VIEW_DATA['arr_all_website'] = $this->model->qry_all_website();

        if (!set_viewdata_data($this->website_id, 'menu', $VIEW_DATA['arr_all_menu_position']))
        {
            $VIEW_DATA['arr_all_menu_position'] = $this->model->gp_qry_all_menu_position($website_id);
        }
        $this->view->active_menu_top = 'guidance';
        //----
        $this->view->title = 'Hướng dẫn thủ tục hành chính';

        //Lay danh sach linh vuc
        $VIEW_DATA['arr_all_list'] = $this->model->qry_all_record_listtype();
        //Lay danh sách thủ tục theo mã lĩnh vực. dùng cho filter
        $v_record_list = get_request_var('sel_record_list', 0);
        $VIEW_DATA['arr_all_record_type'] = $this->model->qry_all_record_type($v_record_list);

        //Lay danh sach tất cả các thu tục 
        $VIEW_DATA['arr_all_guidance'] = $this->model->qry_all_record_type_guidance();
        //lay danh sach các widget
        $VIEW_DATA['arr_all_widget_position'] = $this->prepare_all_widget_position(PAGE_ARCHIVE);

        $this->view->render('dsp_guidance', $VIEW_DATA, $this->theme_code);
    }

    /**
     * Lấy chi tiết thông tin hướng dẫn của thủ tục
     * @param int $v_id ma thủ tục
     */
    public function dsp_single_guidance()
    {
        $website_id = $this->website_id;
        $VIEW_DATA['v_banner'] = $this->model->qry_banner();
        $this->view->active_menu_top = 'guidance';
        $VIEW_DATA['arr_all_website'] = $this->model->qry_all_website();

        if (!set_viewdata_data($this->website_id, 'menu', $VIEW_DATA['arr_all_menu_position']))
        {
            $VIEW_DATA['arr_all_menu_position'] = $this->model->gp_qry_all_menu_position($website_id);
        }
        $this->view->menu_active = __FUNCTION__;
        $this->view->title = 'Hướng dẫn thủ tục hành chính';
        //----


        $v_id = get_request_var('record_type_id', 0);
        $VIEW_DATA['arr_single_guidance'] = $this->model->qry_single_record_type($v_id);
        $this->view->render('dsp_single_guidance', $VIEW_DATA, $this->theme_code);
    }

    //tải file về danh cho hướng dẫn thủ tục
    public function download()
    {
        $v_file_name = get_request_var('file_name', '', TRUE);
        $v_record_type_code = get_request_var('record_code', '', TRUE);
        $v_name = get_request_var('name', '', TRUE);
        if (trim($v_record_type_code) != '')
        {
            $dir_path = CONST_TYPE_FILE_UPLOAD . 'template_files_types';
            if (!is_dir($dir_path))
                die('Đường dẫn không chính xác vui lòng kiểm tra lại! Hoặc liện hệ với nhà cung cấp để biết thêm chi tiết!');
            if (trim($v_file_name) != '')
            {
                //dowload tung file
                foreach (scandir($dir_path) as $item)
                {
                    if ($v_file_name == md5($item) && $item != '.' && $item != '..')
                    {
                        $dir_path_file = CONST_TYPE_FILE_UPLOAD . 'template_files_types' . DS . $item;
                        if (is_file($dir_path_file))
                        {
                            if (file_exists($dir_path_file))
                            {
                                header('Content-Description: File Transfer');
                                header('Content-Type: application/octet-stream');
                                header('Content-Disposition: attachment; filename=' . basename($v_name));
                                header('Expires: 0');
                                header('Cache-Control: no-cache');
                                header('Pragma: public');
                                header('Content-Length: ' . filesize($dir_path_file));
                                ob_clean();
                                flush();
                                readfile($dir_path_file);
                                exit();
                            }
                        }
                        else
                        {
                            die('Tập tin bạn tải về không tồn tại hoặc đã bị xóa xin vui lòng liên hệ nhà cung cấp dịch vụ!');
                        }
                    }
                }
            }
        }
    }

    public function dsp_survey()
    {
        $website_id = $this->website_id;
        $VIEW_DATA['v_banner'] = $this->model->qry_banner();
        $VIEW_DATA['arr_all_member'] = $this->model->qry_all_member();
        $VIEW_DATA['arr_all_website'] = $this->model->qry_all_website();

        if (!set_viewdata_data($this->website_id, 'menu', $VIEW_DATA['arr_all_menu_position']))
        {
            $VIEW_DATA['arr_all_menu_position'] = $this->model->gp_qry_all_menu_position($website_id);
        }
        //Lay du lieu CHUYEN MUC NOI BAT cua chuyen trang
        if (!set_viewdata_data($this->website_id, 'featured_cat', $VIEW_DATA['arr_all_cat_art']))
        {
            $VIEW_DATA['arr_all_cat_art'] = $this->model->gp_qry_all_featured_category($website_id);
        }
        $VIEW_DATA['arr_all_widget_position'] = $this->prepare_all_widget_position(PAGE_ARCHIVE);
        $this->view->menu_active = __FUNCTION__;
        //----

        $this->view->title = 'Câu hỏi khảo sát';
        $v_survey = get_request_var('sel_survey', 0);

        $VIEW_DATA['arr_single_survey'] = $this->model->qry_single_survey($v_survey);

        $VIEW_DATA['arr_all_member_survey'] = $this->model->qry_all_member_survey();
        if (sizeof($VIEW_DATA['arr_single_survey']) <= 0 && $v_survey <= 0) // lay danh sach cau hoi  dau tien trong danh sach $VIEW_DATA['arr_all_member_survey']
        {
            for ($o = 0; $o < sizeof($VIEW_DATA['arr_all_member_survey']); $o++)
            {
                $v_xml_survey = $VIEW_DATA['arr_all_member_survey'][$o]['C_XML_SURVEY'];
                @$dom = simplexml_load_string($v_xml_survey);
                if ($dom)
                {
                    $xpath = '//item';
                    $arr_survey = $dom->xpath($xpath);
                    if (sizeof($arr_survey) > 0)
                    {
                        $status = 0;
                        foreach ($arr_survey as $single_survey)
                        {
                            $v_survey_id = (string) $single_survey->attributes()->PK_SURVEY;
                            $VIEW_DATA['arr_single_survey'] = $this->model->qry_single_survey($v_survey_id);
                            if (sizeof($VIEW_DATA['arr_single_survey']) > 0)
                            {
                                $status = 1;
                                break;
                            }
                        }
                        if ($status == 1)
                        {
                            break;
                        }
                    }
                }
            }
        }

        $this->view->render('dsp_survey', $VIEW_DATA, $this->theme_code);
    }

    /**
     * Cap nhat dap an tra loi tu cong dan
     * @return string Neu != 1 la xay ra loi
     */
    /*
      public function do_update_answer()
      {
      $v_list_question_id = get_post_var('hdn_list_question_id', '');
      $v_survey_id        = get_post_var('hdn_survey_id', 0);
      //Kiem tra da tra loi roi thi ko cho tra loi tiep
      $v_challenge        = get_post_var('recaptcha_challenge_field');
      $v_response         = get_post_var('recaptcha_response_field');
      //        $resp               = recaptcha_check_answer(_CONST_RECAPCHA_PRIVATE_KEY, $_SERVER["REMOTE_ADDR"], $v_challenge, $v_response);
      //        if (!$resp->is_valid)
      //        {
      //            echo 'capcha_error';
      //            exit();
      //        }

      //check survey
      $v_count_survey_id = $this->model->qry_survey_get_id($v_survey_id);
      if (trim($v_list_question_id) == '' OR $v_count_survey_id <= 0)
      {
      echo __('happen failed to your system. That you please executable again few minutes.');
      exit();
      }
      $arr_question_id = explode(',', $v_list_question_id);

      //check exists question id
      if ((int) $this->model->qry_count_question_id($v_list_question_id, $v_survey_id) != count($arr_question_id))
      {
      echo __('happen failed to your system. That you please executable again few minutes.');
      exit();
      }

      for ($i = 0; $i < count($arr_question_id); $i++)
      {
      $v_question_id = isset($arr_question_id[$i]) ? trim(replace_bad_char($arr_question_id[$i])) : 0;
      $v_question_type = get_request_var('type_' . $arr_question_id[$i], '');
      if (trim($v_question_type) == '' OR (int) $v_question_id <= 0)
      {
      echo __('happen failed to your system. That you please executable again few minutes.');
      exit();
      }
      // Chua check truong hop neu type = 0 or 1 thi check bat buoc ton tai answer Neu ko exit error
      //Check answer text and textaria
      if ((int) $v_question_type > 1)
      {
      $v_answer_results = get_post_var('txt_answer_' . $v_question_id, '');
      if (trim($v_answer_results) == '')
      {
      echo __('you need to answer all the questions before hitting the reply button');
      exit();
      }
      $v_current_date = date('Y-m-d');
      $v_answer_name = '';

      //check exists answert
      $arr_single_answer = $this->model->get_single_answer(0, $v_question_id);
      if (count($arr_single_answer) > 0)
      {
      //update
      $v_xml_answer = isset($arr_single_answer['C_RESULT']) ? $arr_single_answer['C_RESULT'] : '';
      $v_answer_id = isset($arr_single_answer['PK_SURVEY_ANSWER']) ? $arr_single_answer['PK_SURVEY_ANSWER'] : 0;

      @$dom = simplexml_load_string($v_xml_answer);

      if (trim($v_xml_answer) == '' OR !$dom)
      {
      echo __('happen failed to your system. That you please executable again few minutes.');
      exit();
      }
      $xml = new SimpleXMLExtended($v_xml_answer);
      $xml_current = $xml->addChild('item');
      $xml_current->addAttribute('date', "$v_current_date");
      $xml_current->addCData($v_answer_results);
      $v_answer_results = $xml->asXML();

      $params = array($v_survey_id, $v_question_id, $v_answer_name, $v_answer_results, $v_answer_id);

      $result = $this->model->do_update_answer($params);
      if ($result != 0)
      {

      echo __('happen failed to your system. That you please executable again few minutes.');
      exit();
      }
      }
      else
      {
      $v_answer_results = "<?xml version=\"1.0\"?>
      <root>
      <item date=\"$v_current_date\"><![CDATA['$v_answer_results']]></item>
      </root>";
      $parrans = array($v_survey_id, $v_question_id, $v_answer_name, $v_answer_results);
      //insert
      $result = $this->model->do_insert_answer($parrans);
      if ($result != 0)
      {

      echo __('happen failed to your system. That you please executable again few minutes.');
      exit();
      }
      }
      }
      elseif ((int) $v_question_type == 0) // answer laf kieu checkbox
      {
      $arr_answer_id = get_post_var('chk_answer_' . $v_question_id, array(), FALSE);
      if (sizeof($arr_answer_id) == 0)
      {
      echo __('you need to answer all the questions before hitting the reply button');
      exit();
      }
      for ($o = 0; $o < sizeof($arr_answer_id); $o++)
      {
      $v_answer_id = end(explode('chk_answer_', $arr_answer_id[$o]));
      if ($v_answer_id <= 0)
      {

      echo __('happen failed to your system. That you please executable again few minutes.');
      exit();
      }

      $arr_single_answer = $this->model->get_single_answer($v_answer_id);
      if (count($arr_single_answer) <= 0)
      {
      echo __('happen failed to your system. That you please executable again few minutes.');
      exit();
      }
      $v_vote = isset($arr_single_answer['C_RESULT']) ? (int) $arr_single_answer['C_RESULT'] : 0;
      $v_answer_results = ($v_vote > 0) ? ($v_vote + 1) : 1;

      $params = array($v_answer_results, $v_answer_id);
      $result = $this->model->do_update_answer_vote($params);
      if ($result != 0)
      {
      echo __('happen failed to your system. That you please executable again few minutes.');
      exit();
      }
      }
      }
      elseif ((int) $v_question_type == 1) // answer la kieu radio
      {

      $v_answer_id = get_post_var('rad_answer_' . $v_question_id, 0);

      if ((int) ($v_answer_id) <= 0)
      {
      echo __('happen failed to your system. That you please executable again few minutes.');
      exit();
      }

      $arr_single_answer = $this->model->get_single_answer($v_answer_id);
      if (count($arr_single_answer) <= 0)
      {
      echo __('happen failed to your system. That you please executable again few minutes.');
      exit();
      }
      $v_vote = isset($arr_single_answer['C_RESULT']) ? (int) $arr_single_answer['C_RESULT'] : 0;
      $v_answer_results = ($v_vote > 0) ? ($v_vote + 1) : 1;

      $params = array($v_answer_results, $v_answer_id);
      $result = $this->model->do_update_answer_vote($params);
      if ($result != 0)
      {
      echo __('happen failed to your system. That you please executable again few minutes.');
      exit();
      }
      }
      else //error
      {
      echo __('happen failed to your system. That you please executable again few minutes.');
      exit();
      }
      }
      echo 1;
      exit();
      }

     */

    /**
     * Cap nhat dap an tra loi tu cong dan
     * @return string Neu != 1 la xay ra loi
     */
    public function do_update_answer()
    {
        $v_list_question_id = get_post_var('hdn_list_question_id', '');
        $v_survey_id = get_post_var('hdn_survey_id', 0);
        //Kiem tra da tra loi roi thi ko cho tra loi tiep
        $v_challenge = get_post_var('recaptcha_challenge_field');
        $v_response = get_post_var('recaptcha_response_field');
        $resp = recaptcha_check_answer(_CONST_RECAPCHA_PRIVATE_KEY, $_SERVER["REMOTE_ADDR"], $v_challenge, $v_response);
        if (!$resp->is_valid)
        {
            echo 'capcha_error';
            exit();
        }

        //check survey
        $v_count_survey_id = $this->model->qry_survey_get_id($v_survey_id);
        if (trim($v_list_question_id) == '' OR $v_count_survey_id <= 0)
        {
            echo __('happen failed to your system. That you please executable again few minutes.');
            exit();
        }
        $arr_question_id = explode(',', $v_list_question_id);

        //check exists question id
        if ((int) $this->model->qry_count_question_id($v_list_question_id, $v_survey_id) != count($arr_question_id))
        {
            echo __('happen failed to your system. That you please executable again few minutes.');
            exit();
        }

        for ($i = 0; $i < count($arr_question_id); $i++)
        {
            $v_question_id = isset($arr_question_id[$i]) ? trim(replace_bad_char($arr_question_id[$i])) : 0;
            $v_question_type = get_request_var('type_' . $arr_question_id[$i], '');
            if (trim($v_question_type) == '' OR (int) $v_question_id <= 0)
            {
                echo __('happen failed to your system. That you please executable again few minutes.');
                exit();
            }
            // Chua check truong hop neu type = 0 or 1 thi check bat buoc ton tai answer Neu ko exit error
            //Check answer text and textaria
            if ((int) $v_question_type > 1)
            {
                $v_answer_results = get_post_var('txt_answer_' . $v_question_id, '');
                if (trim($v_answer_results) == '')
                {
                    echo __('you need to answer all the questions before hitting the reply button');
                    exit();
                }
                $v_current_date = date('Y-m-d');
                $v_answer_name = '';

                //check exists answert
                $arr_single_answer = $this->model->get_single_answer(0, $v_question_id);
                if (count($arr_single_answer) > 0)
                {
                    //update
                    $v_xml_answer = isset($arr_single_answer['C_RESULT']) ? $arr_single_answer['C_RESULT'] : '';
                    $v_answer_id = isset($arr_single_answer['PK_SURVEY_ANSWER']) ? $arr_single_answer['PK_SURVEY_ANSWER'] : 0;

                    @$dom = simplexml_load_string($v_xml_answer);

                    if (trim($v_xml_answer) == '' OR ! $dom)
                    {
                        echo __('happen failed to your system. That you please executable again few minutes.');
                        exit();
                    }
                    $xml = new SimpleXMLExtended($v_xml_answer);
                    $xml_current = $xml->addChild('item');
                    $xml_current->addAttribute('date', "$v_current_date");
                    $xml_current->addCData($v_answer_results);
                    $v_answer_results = $xml->asXML();

                    $params = array($v_survey_id, $v_question_id, $v_answer_name, $v_answer_results, $v_answer_id);

                    $result = $this->model->do_update_answer($params);
                    if ($result != 0)
                    {
                        echo __('happen failed to your system. That you please executable again few minutes.');
                        exit();
                    }
                }
                else
                {
                    $v_answer_results = "<?xml version=\"1.0\"?>
                                                <root>
                                                            <item date=\"$v_current_date\"><![CDATA[$v_answer_results]]></item>
                                                </root>";
                    $parrans = array($v_survey_id, $v_question_id, $v_answer_name, $v_answer_results);
                    //insert
                    $result = $this->model->do_insert_answer($parrans);
                    if ($result != 0)
                    {
                        echo __('happen failed to your system. That you please executable again few minutes.');
                        exit();
                    }
                }
            }
            elseif ((int) $v_question_type == 0) // answer laf kieu checkbox
            {
                $arr_answer_id = get_post_var('chk_answer_' . $v_question_id, array(), FALSE);
                if (sizeof($arr_answer_id) == 0)
                {
                    echo __('you need to answer all the questions before hitting the reply button');
                    exit();
                }
                for ($o = 0; $o < sizeof($arr_answer_id); $o++)
                {
                    $v_answer_id = end(explode('chk_answer_', $arr_answer_id[$o]));
                    if ($v_answer_id <= 0)
                    {
                        echo __('happen failed to your system. That you please executable again few minutes.');
                        exit();
                    }
                    $arr_single_answer = $this->model->get_single_answer($v_answer_id);
                    if (count($arr_single_answer) <= 0)
                    {
                        echo __('happen failed to your system. That you please executable again few minutes.');
                        exit();
                    }
                    $v_vote = isset($arr_single_answer['C_RESULT']) ? (int) $arr_single_answer['C_RESULT'] : 0;
                    $v_answer_results = ($v_vote > 0) ? ($v_vote + 1) : 1;

                    $params = array($v_answer_results, $v_answer_id);
                    $result = $this->model->do_update_answer_vote($params);
                    if ($result != 0)
                    {
                        echo __('happen failed to your system. That you please executable again few minutes.');
                        exit();
                    }
                }
            }
            elseif ((int) $v_question_type == 1) // answer la kieu radio
            {
                $v_answer_id = get_post_var('rad_answer_' . $v_question_id, 0);

                if ((int) ($v_answer_id) <= 0)
                {
                    echo __('happen failed to your system. That you please executable again few minutes.');
                    exit();
                }

                $arr_single_answer = $this->model->get_single_answer($v_answer_id);
                if (count($arr_single_answer) <= 0)
                {
                    echo __('happen failed to your system. That you please executable again few minutes.');
                    exit();
                }
                $v_vote = isset($arr_single_answer['C_RESULT']) ? (int) $arr_single_answer['C_RESULT'] : 0;
                $v_answer_results = ($v_vote > 0) ? ($v_vote + 1) : 1;

                $params = array($v_answer_results, $v_answer_id);
                $result = $this->model->do_update_answer_vote($params);
                if ($result != 0)
                {
                    echo __('happen failed to your system. That you please executable again few minutes.');
                    exit();
                }
            }
            else //error 
            {
                echo __('happen failed to your system. That you please executable again few minutes.');
                exit();
            }
        }
        echo 1;
        exit();
    }

    public function dsp_all_scope()
    {
        $website_id = $this->website_id;
        $VIEW_DATA['v_banner'] = $this->model->qry_banner();
        $VIEW_DATA['arr_all_member'] = $this->model->qry_all_member();
        $VIEW_DATA['arr_all_website'] = $this->model->qry_all_website();

        if (!set_viewdata_data($this->website_id, 'menu', $VIEW_DATA['arr_all_menu_position']))
        {
            $VIEW_DATA['arr_all_menu_position'] = $this->model->gp_qry_all_menu_position($website_id);
        }
        $VIEW_DATA['arr_all_widget_position'] = $this->prepare_all_widget_position(PAGE_ARCHIVE);
        $this->view->active_menu_top = 'evaluation';
        $this->view->menu_active = __FUNCTION__;
        //----
        $VIEW_DATA['arr_all_member_evaluation'] = $this->model->dsp_all_member();

        //Lay ket qua danh gia can bo

        $v_district_id = get_request_var('sel_district', 0);
        $v_village_id = get_request_var('sel_village', 0);
        $v_txt_filter = get_request_var('txt_member_name', '');
        $v_limit = defined('_CONST_LIMT_STAFFT_SINGLE_PAGE') ? _CONST_LIMT_STAFFT_SINGLE_PAGE : 10;
        $VIEW_DATA['arr_evaluation_results'] = $this->model->qry_all_evaluation(0, $v_limit, $v_district_id, $v_village_id, $v_txt_filter);

        $this->view->title = 'Đánh giá cán bộ';
        $this->view->render('dsp_all_scope', $VIEW_DATA, $this->theme_code);
    }

    // Chi tiet thong tin can bo 
    public function dsp_single_staff()
    {
        $website_id = $this->website_id;
        $VIEW_DATA['v_banner'] = $this->model->qry_banner();
        $VIEW_DATA['arr_all_website'] = $this->model->qry_all_website();
        $this->view->menu_active = __FUNCTION__;
        if (!set_viewdata_data($this->website_id, 'menu', $VIEW_DATA['arr_all_menu_position']))
        {
            $VIEW_DATA['arr_all_menu_position'] = $this->model->gp_qry_all_menu_position($website_id);
        }
//        $VIEW_DATA['arr_all_widget_position'] = $this->prepare_all_widget_position(PAGE_ARCHIVE);

        $v_staff_id = get_request_var('staff_id', 0);

        $VIEW_DATA['criterail'] = $this->model->qry_all_criterial($v_staff_id);
        $arr_single_staff = $this->model->qry_staff_get_by_id($v_staff_id);
        $VIEW_DATA['arr_single_staff'] = $arr_single_staff['arr_single_user'];
        $VIEW_DATA['point'] = isset($arr_single_staff['C_POINT']) ? $arr_single_staff['C_POINT'] : 0;

        $VIEW_DATA['arr_ressult_vote'] = $this->model->qry_single_result($v_staff_id);

        if (intval($v_staff_id) > 0)
        {
            $this->view->render('dsp_single_staff', $VIEW_DATA, $this->theme_code);
        }
    }

    public function dsp_update_vote()
    {
        $user_id = get_post_var('user_id', '');
        $today = get_post_var('today', '');
        $v_fk_creterial = get_post_var('fk_criterial', '');
        $v_village_short_code = get_post_var('village_short_code', '');
        $v_record_code = get_post_var('record_code', '');
        //kiem tra ho so da duoc danh gia chua
        if ($this->model->check_record_evaluated($v_record_code) == true)
        {
            echo '2';
            return false;
        }
        //kiem tra ma ho so co dung k 
        if ($this->_check_record_code($v_village_short_code, $v_record_code))
        {
            $result = $this->model->do_update_vote($user_id, $today, $v_fk_creterial);
            if ($result)
            {
                $this->model->do_insert_record_evaluated($v_record_code);
                echo '1';
            }
            else
            {
                echo '-1';
            }
        }
        else
        {
            echo '0';
        }
    }

    /**
     * thuc hien update so diem danh gia ko can qua kiem tra (su dung cho mobile)
     */
    public function do_update_vote()
    {
        $user_id = get_post_var('user_id', '');
        $today = get_post_var('today', '');
        $v_fk_creterial = get_post_var('fk_criterial', '');
        $result = $this->model->do_update_vote($user_id, $today, $v_fk_creterial);
        if ($result)
        {
            $arr_ressult_vote = $this->model->qry_single_result($user_id);
            echo json_encode($arr_ressult_vote);
        }
        else
        {
            echo '0';
        }
    }

    /**
     * Huong dan danh gia can bo
     */
    public function dsp_assessment_guidelines()
    {
        $website_id = $this->website_id;
        $VIEW_DATA['v_banner'] = $this->model->qry_banner();
        $VIEW_DATA['arr_all_member'] = $this->model->qry_all_member();
        $VIEW_DATA['arr_all_website'] = $this->model->qry_all_website();

        if (!set_viewdata_data($this->website_id, 'menu', $VIEW_DATA['arr_all_menu_position']))
        {
            $VIEW_DATA['arr_all_menu_position'] = $this->model->gp_qry_all_menu_position($website_id);
        }
        $this->view->menu_active = __FUNCTION__;
        $this->view->active_menu_top = 'evaluation';
//        $VIEW_DATA['arr_all_widget_position'] = $this->prepare_all_widget_position(PAGE_ARCHIVE);

        $VIEW_DATA['arr_assessment_guidancelines'] = $this->model->qey_assessment_guidelines();
        $this->view->render('dsp_assessment_guidelines', $VIEW_DATA, $this->theme_code);
    }

    // Ket qua dnah gia can bo
    public function dsp_evaluation_results()
    {
        $website_id = $this->website_id;
        $VIEW_DATA['v_banner'] = $this->model->qry_banner();
        $VIEW_DATA['arr_all_member'] = $this->model->qry_all_member();
        $VIEW_DATA['arr_all_website'] = $this->model->qry_all_website();
        $this->view->active_menu_top = 'evaluation';
        if (!set_viewdata_data($this->website_id, 'menu', $VIEW_DATA['arr_all_menu_position']))
        {
            $VIEW_DATA['arr_all_menu_position'] = $this->model->gp_qry_all_menu_position($website_id);
        }
        $this->view->menu_active = __FUNCTION__;
        //        $VIEW_DATA['arr_all_widget_position'] = $this->prepare_all_widget_position(PAGE_ARCHIVE);

        $v_staff_id = get_request_var('staff_id', 0);
        if ($v_staff_id <= 0)
        {
            $arr_results = $this->model->qry_evaluation_results();
            $VIEW_DATA['arr_evaluation_results'] = isset($arr_results['arr_all_scope_0_1']) ? $arr_results['arr_all_scope_0_1'] : array();
            $VIEW_DATA['arr_evaluation_results_child'] = isset($arr_results['arr_all_scope_2']) ? $arr_results['arr_all_scope_2'] : array();

            $VIEW_DATA['arr_all_criteria'] = $this->model->qry_all_criterial();
            $this->view->render('dsp_evaluation_results', $VIEW_DATA, $this->theme_code);
        }
        else
        {
            $arr_single_staff = $this->model->qry_staff_get_by_id($v_staff_id);
            $VIEW_DATA['arr_single_staff'] = $arr_single_staff['arr_single_user'];
            $VIEW_DATA['arr_result'] = $this->model->qry_single_result($v_staff_id);
            $VIEW_DATA['point'] = isset($arr_single_staff['C_POINT']) ? $arr_single_staff['C_POINT'] : 0;

            $this->view->render('dsp_single_evaluatosion_resuslt', $VIEW_DATA, $this->theme_code);
        }
    }

    /**
     * Kiem tra su ton tai cua ma ho so duoc nhap
     * @param string $village_short_code C_SHORT_CODE cua table membner
     * @param string $recored_code        ma ho so 
     * @return 1 ton tai,Khong ron tai return 0
     */
    private function _check_record_code($v_village_short_code, $v_record_code)
    {
        $arr_loockup_link = $this->model->get_lookup_link();

        if (trim($v_village_short_code) != '' && trim($v_record_code) != '')
        {
            //lay url danh sach hs 
            $v_url = $arr_loockup_link[$v_village_short_code]['C_LOOKUP_LINK'];

            //arr du lieu
            $html_resp = file_get_contents($v_url . $v_record_code);
            if (strlen($html_resp) > 0)
            {
                return true;
            }
        }
        return false;
    }

    //Fc dang ky tai khoan danh cho khach hang
    public function register()
    {
        if ($this->check_citizen_login())
        {
            $page = $_SERVER['PHP_SELF'];
            header("Refresh: 0; url=$page");
            return;
        }
        $website_id = $this->website_id;
        $VIEW_DATA['v_banner'] = $this->model->qry_banner();
        $VIEW_DATA['arr_all_member'] = $this->model->qry_all_member();
        $VIEW_DATA['arr_all_website'] = $this->model->qry_all_website();

        if (!set_viewdata_data($this->website_id, 'menu', $VIEW_DATA['arr_all_menu_position']))
        {
            $VIEW_DATA['arr_all_menu_position'] = $this->model->gp_qry_all_menu_position($website_id);
        }
        $this->view->menu_active = __FUNCTION__;
        $VIEW_DATA['arr_all_widget_position'] = $this->prepare_all_widget_position(PAGE_ARCHIVE);


        $this->view->render('dsp_register', $VIEW_DATA, $this->theme_code);
    }

    public function do_register()
    {
        if ($this->check_citizen_login())
        {
            echo 1;
            return;
        }

        $arr_result = $this->model->do_register();
        if (sizeof($arr_result['error']) > 0)
        {
            echo $arr_result['error'];
            return;
        }

        $VIEW_DATA['username'] = $v_username = $arr_result['username'];
        $VIEW_DATA['email'] = $v_email = $arr_result['email'];
        $VIEW_DATA['code'] = $v_code = $arr_result['code'];
        $VIEW_DATA['create_date'] = $arr_result['create_date'];

        ob_start();
        $this->view->render('dsp_content_send_mail', $VIEW_DATA, $this->theme_code);
        $html = ob_get_clean();
        //send mail
        require SERVER_ROOT . 'libs' . DS . 'mail_sender.php';
        $mail_sender = new MailSender();
        $reslut_sen_mail = $mail_sender->SendMail($v_email, $html, 'text/html');

        echo '1';
        return;
    }

    /*
     * Gui ma kich hoat
     */

    public function send_code_trigger()
    {
        $v_username = get_post_var('username', '');
        // Session::get('account_'.$v_username) neu chua kich hoat value  =1
        if (trim($v_username) == '' OR ( Session::get('account_' . $v_username) != '1'))
        {
            echo '0';
            return;
        }
        $arr_result = $this->model->send_code_trigger($v_username);

        if (sizeof($arr_result['error']) > 0)
        {
            echo '0';
            return;
        }
        $VIEW_DATA['username'] = $v_username = $arr_result['username'];
        $VIEW_DATA['email'] = $v_email = $arr_result['email'];
        $VIEW_DATA['code'] = $v_code = $arr_result['code'];
        $VIEW_DATA['create_date'] = $arr_result['create_date'];

        ob_start();
        $this->view->render('dsp_content_send_mail', $VIEW_DATA, $this->theme_code);
        $html = ob_get_clean();
        //send mail
        require SERVER_ROOT . 'libs' . DS . 'mail_sender.php';
        $mail_sender = new MailSender();
        $reslut_sen_mail = $mail_sender->SendMail($v_email, $html, 'text/html');
        if ($reslut_sen_mail)
        {
            echo '1';
            return;
        }

        echo '0';
        return;
    }

//    Hien thi giao dien kich hoat
    public function dsp_do_account_trigger()
    {
        $VIEW_DATA = array();
        $website_id = $this->website_id;
        $VIEW_DATA['v_banner'] = $this->model->qry_banner();
        $VIEW_DATA['arr_all_member'] = $this->model->qry_all_member();
        $VIEW_DATA['arr_all_website'] = $this->model->qry_all_website();

        if (!set_viewdata_data($this->website_id, 'menu', $VIEW_DATA['arr_all_menu_position']))
        {
            $VIEW_DATA['arr_all_menu_position'] = $this->model->gp_qry_all_menu_position($website_id);
        }
        $this->view->menu_active = __FUNCTION__;

        $v_username = get_request_var('username', '');
        if (trim($v_username) == '')
        {
            echo '<h1 style="width:100%;text-align:center; color:Red;">Tài khoản này không tồn tại hoặc đã được kích hoạt.<br/> Nhấn vào đây để quay lại <a href="' . SITE_ROOT . '">trang chủ</a></h1>';
            exit();
        }
        //Kiem tra tai khoan da duoc kich hoat?
        $arr_citizen = $this->model->qry_citizen_tmp_get_by_username($v_username);
        if (sizeof($arr_citizen) != 1)
        {
            echo '<h1 style="width:100%;text-align:center; color:Red;">Tài khoản này không tồn tại hoặc đã được kích hoạt.<br/> Nhấn vào đây để quay lại <a href="' . SITE_ROOT . '">trang chủ</a></h1>';
            exit();
        }

        $this->view->render('dsp_do_account_trigger', $VIEW_DATA, $this->theme_code);
    }

    //cap nhat thong tin kich hoat
    function update_account_trigger()
    {
        if ($_POST)
        {
            $v_username = get_post_var('username', '');
            $arr_result = $this->model->update_account_trigger();
            if (sizeof($arr_result['error']) > 0)
            {
                echo $arr_result['error'];
                return;
            }
            echo '1'; // Update thanh cong
            session_start();
            unset($_SESSION['account_' . $v_username]); //Huy trang thai xac dinh chua kich hoat
            return;
        }
        echo '0';
        return;
    }

    public function do_login()
    {
        $v_username = get_post_var('txt_username', NULL);
        $v_password = get_post_var('txt_password', NULL);
        $v_password = encrypt_password($v_password);

        if ($this->check_citizen_login())
        {
            echo '1';
            return;
        }
        if ($_POST)
        {
            $v_username = get_post_var('txt_username');
            $result = $this->model->do_login($v_username, $v_password);
            if (sizeof($result['error']) > 0)
            {
                Session::session_unset('citizen_login_name');
                Session::session_unset('citizen_name');
                Session::session_unset('citizen_login_id');
                Session::session_unset('citizen_email');
                Session::session_unset('account_' . $v_username);
                Session::session_unset('citizen_role');
                echo $result['error'];
                return;
            }
            echo 1;
            return;
        }
        session_destroy();
        echo 'Đã xảy ra lỗi trong quá trình đăng nhập. Vui lòng thực hiện lại';
        return;
    }

    public function do_logout()
    {
        session_destroy();
        setcookie("_uuu_", "", time() - 3600);
        setcookie("_ppp_", "", time() - 3600);
        return;
    }

    public function dsp_single_account()
    {
        if (!$this->check_citizen_login())
        {
            $page = $_SERVER['PHP_SELF'];
            header("Refresh: 0; url=$page");
            exit();
        }

        $VIEW_DATA = array();
        $website_id = $this->website_id;
        $VIEW_DATA['v_banner'] = $this->model->qry_banner();
        $VIEW_DATA['arr_all_member'] = $this->model->qry_all_member();
        $VIEW_DATA['arr_all_website'] = $this->model->qry_all_website();

        if (!set_viewdata_data($this->website_id, 'menu', $VIEW_DATA['arr_all_menu_position']))
        {
            $VIEW_DATA['arr_all_menu_position'] = $this->model->gp_qry_all_menu_position($website_id);
        }
        $this->view->menu_active = __FUNCTION__;

        $VIEW_DATA['arr_single_citizen_account'] = $this->model->qry_single_account_citizen();

        $this->view->render('dsp_single_citizen_account', $VIEW_DATA, $this->theme_code);
    }

    /**
     * 
     * @return Load home page
     */
    private function check_citizen_login()
    {
        $v_login_id = Session::get('citizen_login_id');
        if ($v_login_id > 0)
        {
            return TRUE;
        }
        else
        {
            return FALSE;
        }
//        $page = $_SERVER['PHP_SELF'];            
//        header("Refresh: 0; url=$page");
    }

    public function dsp_history_filing()
    {
        if (!$this->check_citizen_login())
        {
            $page = $_SERVER['PHP_SELF'];
            header("Refresh: 0; url=$page");
            exit();
        }

        $arr_filter = $this->_arr_filter_history_filing();

        $VIEW_DATA = array();
        $website_id = $this->website_id;
        $VIEW_DATA['v_banner'] = $this->model->qry_banner();
        $VIEW_DATA['arr_all_member'] = $this->model->qry_all_member();
        $VIEW_DATA['arr_all_website'] = $this->model->qry_all_website();

        if (!set_viewdata_data($this->website_id, 'menu', $VIEW_DATA['arr_all_menu_position']))
        {
            $VIEW_DATA['arr_all_menu_position'] = $this->model->gp_qry_all_menu_position($website_id);
        }
        $this->view->menu_active = __FUNCTION__;
        $arr_loockup_link = $this->model->get_lookup_link();

        $v_detail_record_no = get_post_var('hdn_record_no', '');
        if (trim($v_detail_record_no) != '')
        {
            $v_html_detail_record = $v_html_detail_form = '';

            $arr_single_record = $this->model->qry_all_record(array('txt_record_no' => $v_detail_record_no));
            if (sizeof($arr_single_record) == 1)
            {
                $v_status = $arr_single_record[0]['C_STATUS'];
                $v_xml_data = $arr_single_record[0]['C_XML_DATA'];
                $v_deleted = $arr_single_record[0]['C_DELETED'];

                if ($v_status == -1)
                {
                    //TH1: Ho so chua duoc duyet hoac da bi xoa tren cong
                    $v_html_detail_record = "<h3 style='margin:8px;'>" . __('Hồ sơ có mã "' . $v_detail_record_no . '" bị từ chối. Do nội dung không đúng hoặc không hợp lệ. Mọi thắc mắc xin vui lòng liên hệ với nhà cung cấp dịch vụ.') . " </h3><hr/>";
                    $v_html_detail_form = 'FALSE';
                }
                else if ($v_status == 1)
                {//TH2: Ho So da duoc duyet  nhung chua duoc tiep nhan
                    //lay short code
                    $v_short_code = preg_replace('/([A-Z-0-9]+)-([A-Z-0-9]+)-([A-Z-0-9]+)/', '$2', $v_detail_record_no);
                    $v_short_code = preg_replace('/([A-Z-0-9]+).(.+)/', '$1', $v_detail_record_no);
                    
                    //Lay ma thu tuc
                    if(count(explode(".", $v_detail_record_no)) > 2)
                    {
                        $v_record_type_code = preg_replace('/([A-Z-0-9]+).([A-Z-0-9]+).(.+)/', '$2', $v_detail_record_no);
                    }
                    else
                    {
                        $v_record_type_code = preg_replace('/([A-Z-0-9]+)-([A-Z-0-9]+)-(.+)/', '$1', $v_detail_record_no);                    
                    }
                    
                    //lay url
                    $v_url                  = $arr_loockup_link[$v_short_code]['C_LOOKUP_LINK'];
                    $v_url_record_form_link = $arr_loockup_link[$v_short_code]['C_LOOKUP_DETAIL_FORM_LINK'];
                    //lay html
                    
                    $v_html_detail_record = file_get_contents($v_url.$v_detail_record_no);
                   
                    $v_html_detail_form     = file_get_contents($v_url_record_form_link.$v_detail_record_no);
                    
                    if(1==1)
                    {
                        $v_html_detail_form = $v_html_detail_form;
                    }
                    else
                    {
                        if($v_deleted == 1)
                        {
                            //TH1: Ho so chua duoc duyet hoac da bi xoa tren cong
                            $v_html_detail_record = "<h3 style='margin:8px;'>". __('Hồ sơ có mã "'.$v_detail_record_no .'" bị từ chối. Do nội dung không đúng hoặc không hợp lệ.') . " </h3><hr/>";
                        }
                        else
                        {
                            //TH1: Ho so chua duoc duyet hoac da bi xoa tren cong
                            $v_html_detail_record = "<h3 style='margin:8px;'>". __('Hồ sơ có mã "'.$v_detail_record_no .'" đang được xử lý.') . " </h3><hr/>";
                        }
                        $v_html_detail_form   ='<h3 stype"margin:10px">Không thể xem đơn do hồ sơ đã bị từ chối hoặc chưa được xử lý</h3><hr />';
                    }
                }
                elseif ($v_status == 0)
                {
                    $v_html_detail_record = "<h3 style='margin:8px;'>" . __('Hồ sơ chưa được xác nhận') . " $v_detail_record_no</h3><hr/>";
                    $v_html_detail_form = '<h3>Hồ sơ chưa được xác nhận.</h3>';
                }
            }
            //neu html='' thong bao loi
            if ($v_html_detail_record == '')
            {
                $v_html_detail_record = "<h3 style='margin:8px;'>" . __('sorry, system not found record') . " $v_detail_record_no</h3>";
            }

            $VIEW_DATA['v_html_detail_record'] = $v_html_detail_record;
            $VIEW_DATA['v_xml_data'] = $v_xml_data;
            $VIEW_DATA['v_html_detail_form'] = $v_html_detail_form;
        }
        else
        {
            $VIEW_DATA['arr_all_record'] = $this->model->qry_all_record($arr_filter);
        }


        $this->view->render('dsp_history_filing', $VIEW_DATA, $this->theme_code);
    }

    private function _arr_filter_history_filing()
    {
        $v_record_no = get_request_var('txt_record_no', NULL);
        $v_begin_send_date = get_request_var('txt_begin_send_date', NULL);
        $v_end_send_date = get_request_var('txt_end_send_date', NULL);
        $v_fact_begin_date = get_request_var('txt_fact_begin_date', NULL);
        $v_fact_end_date = get_request_var('txt_fact_end_date', NULL);

        return $arr_filter = array(
            'txt_record_no'       => $v_record_no,
            'txt_begin_send_date' => $v_begin_send_date,
            'txt_end_send_date'   => $v_end_send_date,
            'txt_fact_begin_date' => $v_fact_begin_date,
            'txt_fact_end_date'   => $v_fact_end_date
        );
    }

    //Cap nhat thong tin tai khoan
    public function do_upate_citizen_account()
    {
        if (!$this->check_citizen_login())
        {
            $page = $_SERVER['PHP_SELF'];
            header("Refresh: 0; url=$page");
            exit();
        }
        $arr_result = $this->model->do_update_citizen_account();
        $v_email = get_request_var('txt_email', '');

        if (sizeof($arr_result['error']) > 0)
        {
            $this->model->exec_fail($v_url = SITE_ROOT . 'tai-khoan/chi-tiet', $arr_result['error']);
        }
        if ($v_email != Session::get('citizen_email'))
        {
            $VIEW_DATA['username'] = $v_username = $arr_result['username'];
            $VIEW_DATA['email'] = $v_email_new = $arr_result['email'];
            $VIEW_DATA['code'] = $v_code = $arr_result['code'];
            $VIEW_DATA['create_date'] = $arr_result['create_date'];
            $VIEW_DATA['citizen_id'] = $arr_result['citizen_id'];
            $VIEW_DATA['type'] = 'trigger_email';
            ob_start();
            $this->view->render('dsp_content_send_mail', $VIEW_DATA, $this->theme_code);
            $html = ob_get_clean();
            //send mail
            require SERVER_ROOT . 'libs' . DS . 'mail_sender.php';
            $mail_sender = new MailSender();
            @$reslut_sen_mail = $mail_sender->SendMail($v_email_new, $html, 'text/html');
        }

        $v_url = SITE_ROOT . 'tai-khoan/chi-tiet';
        $this->model->exec_done($v_url);
    }

    //Huy thay doi email
    public function dsp_destroyed_change_email()
    {
        $v_username = get_request_var('username', '');
        $v_citizen_id = get_request_var('id', 0);
        if (intval($v_citizen_id) > 0 && trim($v_username) != '')
        {
            $result = $this->model->dsp_destroyed_change_email($v_username, $v_citizen_id);
            if ($result == 1)
            {
                $v_to_send_email = get_request_var('send_to_email');
                if ($v_to_send_email == 1)
                {
                    $url_page = $_SERVER['PHP_SELF'];
                    $this->model->exec_fail($url_page, __('you have successfully changed email!'));
                    echo 1;
                    exit();
                }
                echo 1;
                exit();
            }
        }
        echo 0;
        exit();
    }

    //Fc xac nhan doi email
    public function dsp_active_change_email()
    {
        $website_id = $this->website_id;
        $VIEW_DATA['v_banner'] = $this->model->qry_banner();

        $VIEW_DATA['arr_all_website'] = $this->model->qry_all_website();

        //su dung menu cache
        if (!set_viewdata_data($this->website_id, 'menu', $VIEW_DATA['arr_all_menu_position']))
        {
            $VIEW_DATA['arr_all_menu_position'] = $this->model->gp_qry_all_menu_position($website_id);
        }
        //Lay du lieu TIN NOI BAT trong ngay
        $VIEW_DATA['arr_all_breaking_news'] = $this->model->gp_qry_all_breaking_news($website_id);

        $v_username = get_request_var('username', '');
        $v_citizen_id = get_request_var('id', '');
        $v_code = get_request_var('txt_code', '');
        if (trim($v_username) != '' && intval($v_citizen_id) > 0)
        {
            // Kiem tinh hop le tua tai hoan yeu cau doi mail
            if ($this->model->check_account_change_email($v_username, $v_citizen_id) != 1)
            {
                $VIEW_DATA['error'] = "Đã xảy ra lỗi. Yêu cầu thay đổi email của bạn đã được kích hoạt hoặc thời gian yêu cầu xác nhận thay đổi email đã quá hạn cho phép.";
            }
            else
            {
                $VIEW_DATA['arr_single_citizen'] = array('username' => $v_username, 'id' => $v_citizen_id);
            }
            if ($_POST)
            {
                $v_citizen_id = get_post_var('hdn_citizen_id', '');

                $result = $this->model->dsp_active_change_email($v_citizen_id, $v_code);
                if (isset($result['success']) && $result['success'] == 1)
                {
                    $VIEW_DATA['success'] = 'TRUE';
                }
                else
                {
                    $VIEW_DATA['success'] = $result['error'];
                }
            }
        }

        $this->view->render('dsp_active_change_email', $VIEW_DATA, $this->theme_code);
    }

    /*     * *
     * Check username exist
     */

    public function check_username_exist()
    {
        if (get_request_var('username_reset'))
        {
            $username_check = $this->model->check_username_exist(get_request_var('username_reset'));
            if ($username_check)
            {
                echo 'true';
            }
            else
            {
                echo 'false';
            }
        }
    }

    //reset passsword
    public function do_reset_password()
    {
        $v_challenge = get_post_var('recaptcha_challenge_field');
        $v_response = get_post_var('recaptcha_response_field');
        $resp = recaptcha_check_answer(_CONST_RECAPCHA_PRIVATE_KEY, $_SERVER["REMOTE_ADDR"], $v_challenge, $v_response);
        if ($resp->is_valid)//$resp->is_validss
        {
            $email = get_request_var('txt_reset_email');
            $username = get_request_var('txt_reset_username');
            $id_user = $this->model->check_username_and_email($username, $email);
            if ($id_user > 0)
            {
                $citizen_tmp_code = $this->model->data_table_citizen_tmp($id_user, $email, 3);
                if ($citizen_tmp_code)
                {
                    $VIEW_DATA['username'] = $username;
                    $VIEW_DATA['email'] = $email;
                    $VIEW_DATA['code'] = $citizen_tmp_code;
                    $VIEW_DATA['type'] = 'trigger_reset_password';

                    ob_start();
                    $this->view->render('dsp_content_send_mail', $VIEW_DATA, $this->theme_code);
                    $html = ob_get_clean();
                    //send mail
                    require SERVER_ROOT . 'libs' . DS . 'mail_sender.php';
                    $mail_sender = new MailSender();
                    $reslut_sen_mail = $mail_sender->SendMail($email, $html, 'text/html');
                    if ($reslut_sen_mail > 0)
                    {
                        $VIEW_DATA = array();
                        $VIEW_DATA['email'] = $email;
                        $this->view->render('dsp_send_request_success', $VIEW_DATA, $this->theme_code);
                    }
                    else
                    {
                        $url_page = $this->view->get_controller_url('frontend', 'frontend') . 'dsp_form_request_password';
                        $this->model->exec_fail($url_page, __('Gửi email thất bại.'));
                    }
                }
                else
                {
                    $url_page = $this->view->get_controller_url('frontend', 'frontend') . 'dsp_form_request_password';
                    $this->model->exec_fail($url_page, __('Lỗi hệ thống!'));
                }
            }
            else
            {
                $url_page = $this->view->get_controller_url('frontend', 'frontend') . 'dsp_form_request_password';
                $this->model->exec_fail($url_page, __('Email hoặc Tên đăng nhập không đúng!'));
            }
        }
        else
        {
            $url_page = $this->view->get_controller_url('frontend', 'frontend') . 'dsp_form_request_password';
            $this->model->exec_fail($url_page, __('Mã xác nhập sai!'));
        }
    }

    //display form sent request passs
    public function dsp_form_request_password()
    {
        $this->view->render('dsp_form_request_password', array(), $this->theme_code);
    }

    //change pass
    public function dsp_change_password()
    {
        $website_id = $this->website_id;
        $VIEW_DATA['v_banner'] = $this->model->qry_banner();
        $VIEW_DATA['arr_all_website'] = $this->model->qry_all_website();
        $this->view->widget = 'citizens_question'; // show widget        

        if (!set_viewdata_data($this->website_id, 'menu', $VIEW_DATA['arr_all_menu_position']))
        {
            $VIEW_DATA['arr_all_menu_position'] = $this->model->gp_qry_all_menu_position($website_id);
        }
        if (get_request_var('email') && get_request_var('v_code'))
        {
            $email = get_request_var('email');
            $code = get_request_var('v_code');
            $check_reset = $this->model->check_reset_password($email, $code);
            if ($check_reset)
            {
                $VIEW_DATA['email'] = $email;
                $this->view->render('dsp_change_password', $VIEW_DATA, $this->theme_code);
            }
            else
            {
                $url_page = SITE_ROOT;
                $this->model->exec_fail($url_page, __('your page does not exist!'));
            }
        }
        else
        {
            $url_page = $url_page = SITE_ROOT;
            $this->model->exec_fail($url_page, __('your page does not exist!'));
        }
    }

    public function do_change_password()
    {
        if (get_request_var('new_pass', '') && get_request_var('re_pass', '') && get_request_var('email', ''))
        {
            if (strlen(get_request_var('new_pass', '')) < 6)
            {
                $url = $_SERVER['HTTP_REFERER'];
                $this->model->exec_fail($url, 'Mật khẩu thay đổi cần có độ dài ít nhất 6 ký tự');
            }
            if (strcmp(get_request_var('new_pass', ''), get_request_var('re_pass', '')) === 0)
            {
                $this->model->change_password(get_request_var('email', ''), get_request_var('re_pass', ''));
                $url_page = SITE_ROOT;
                $this->model->exec_fail($url_page, __('Đổi mật khẩu thành công!'));
            }
        }
    }

    private function _check_login_cookie()
    {
        if (isset($_COOKIE['_uuu_']) && isset($_COOKIE['_ppp_']))
        {
            $v_username = $_COOKIE['_uuu_'];
            $v_password = $_COOKIE['_ppp_'];

            $result = $this->model->do_login($v_username, $v_password);
            if (sizeof($result['error']) > 0)
            {
                Session::session_unset('citizen_login_name');
                Session::session_unset('citizen_name');
                Session::session_unset('citizen_login_id');
                Session::session_unset('citizen_email');
                Session::session_unset('account_' . $v_username);
                Session::session_unset('citizen_role');
                setcookie("_uuu_", "", time() - 3600);
                setcookie("_ppp_", "", time() - 3600);
                return;
            }
            return;
        }
    }

    public function touch_screen($method = 'district')
    {
        $VIEW_DATA['arr_all_member'] = array();
        $VIEW_DATA['arr_all_staff'] = array();

        if ($method == 'district')
        {
            $VIEW_DATA['active'] = 'district';
            $v_scope = 1;
        }
        elseif ($method == 'department')
        {
            $VIEW_DATA['active'] = 'department';
            $v_scope = 0;
        }
        elseif ($method == 'village')
        {
            $VIEW_DATA['active'] = 'village';
            $v_scope = 2;
        }

        $member_id = get_request_var('member_id', 0);
        $VIEW_DATA['active'] = 'district';
        if ($member_id != 0 && is_numeric($member_id))
        {
            $v_limit = defined('_CONST_LIMT_STAFFT_SINGLE_PAGE') ? _CONST_LIMT_STAFFT_SINGLE_PAGE : 10;
            $VIEW_DATA['arr_all_staff'] = $this->model->qry_all_evaluation(0, $v_limit, $member_id);
        }
        else
        {
            $VIEW_DATA['arr_all_member'] = $this->model->dsp_all_member($v_scope);
        }
        $this->view->render('dsp_select_district', $VIEW_DATA);
    }

    public function danh_gia($staff_id)
    {
        $v_staff_id = replace_bad_char($staff_id);

        $VIEW_DATA['criterail'] = $this->model->qry_all_criterial($v_staff_id);
        $arr_single_staff = $this->model->qry_staff_get_by_id($v_staff_id);
        $VIEW_DATA['arr_single_staff'] = $arr_single_staff['arr_single_user'];
        $VIEW_DATA['point'] = isset($arr_single_staff['C_POINT']) ? $arr_single_staff['C_POINT'] : 0;
        $VIEW_DATA['staff_id'] = $v_staff_id;
        $VIEW_DATA['arr_result_vote'] = $this->model->qry_single_result($v_staff_id);
        if (intval($v_staff_id) > 0)
        {
            $this->view->render('dsp_danh_gia', $VIEW_DATA);
        }
    }
	
	
	
	#3. Thêm vào frontend/frontend_Controller
    public function dsp_tong_hop_tinh_hinh_giai_quyet_tthc()
    {
        $max_dateime_update_record_history_start = $this->model->qry_max_date_history_start();
        
        $arr_synthesis = $this->model->qry_synthesis();
        $tong_count_tra_som_han =  $tong_count_tra_dung_han = $tong_count_tra_qua_han = 0;
        foreach ($arr_synthesis as $arr_value)
        {
            $tong_count_tra_som_han   += isset($arr_value['C_COUNT_TRA_SOM_HAN']) ? (int)$arr_value['C_COUNT_TRA_SOM_HAN'] : 0;
            $tong_count_tra_dung_han  += isset($arr_value['C_COUNT_TRA_DUNG_HAN']) ? (int)$arr_value['C_COUNT_TRA_DUNG_HAN'] : 0;
            $tong_count_tra_qua_han   += isset($arr_value['C_COUNT_TRA_QUA_HAN']) ? (int)$arr_value['C_COUNT_TRA_QUA_HAN'] : 0;
        }
        $tong_tra = ($tong_count_tra_som_han + $tong_count_tra_dung_han  + $tong_count_tra_qua_han);
        
        $ty_le = '100%';
        if($tong_tra >0 )
        {
            $ty_le = (($tong_count_tra_som_han + $tong_count_tra_dung_han ) /$tong_tra) * 100;
            $ty_le = round($ty_le,2);
            $ty_le .= "%";
        }
        ob_start();
        ?>
		  <html>
                <head>
                    <meta charset="utf-8" http-equiv="Cache-Control" content="no-cache">
                </head>
                    <body>
            <div style="background: white;overflow: hidden">

                <div style="font-family: Arial, Verdana, Geneva, Lucida, 'lucida grande', helvetica, sans-serif;
                    box-shadow: none;margin: 0 0; width:260px;">

                  <div style="border-top: 0;background-color: #FFF;">
                    <div style="text-align: center">
                            <span style="font-size: 14px;font-weight: bold;color:#1F497D;margin-bottom: 5px;">
                                <span style="color:black;"> Đến tháng <span id="dnn_ctr2095_CityWebSupport_lblMonth"><?php   $arr_maxDate = explode('-', $max_dateime_update_record_history_start);echo (int)$arr_maxDate[1] . '/'.$arr_maxDate[2] ?></span> 
                                <?php  echo $v_email = get_system_config_value(CFGKEY_UNIT_NAME_SERVICE);?>
                                </span>
                            </span>
                      <div style="padding-bottom: 7px;"></div>

                      <a href="<?php echo FULL_SITE_ROOT?>" target="_blank" style="text-decoration: none;">
                        <span style="color:#C00000;font-size: 40px; font-weight: bold;"> <span id="dnn_ctr2095_CityWebSupport_lblPercent"><?php echo$ty_le; ?></span></span>
                        <br>
                        <span style="color:#1F497D;font-weight:bold;font-size: 90%"> hồ sơ đúng hạn
                        </span>
                        <br>
                      </a>
                      
                        <div style="padding-bottom: 7px;"></div>
                        <span style="font-size: 11px;font-style: italic;font-weight:bold;color:#1F497D;font-family: Verdana, Arial, Helvetica, sans-serif">
                          (tự động cập nhật vào lúc <br>
                              <span id="dnn_ctr2095_CityWebSupport_lblTime"><?php echo str_replace('-','/', $max_dateime_update_record_history_start);?></span>)
                        </span>
                    </div>
                        
                  </div>
                </div>
            </div>
      </body>
                </html>
        <?php
         $html = ob_get_clean();
         echo $html;
    }

}

//add Cdata to xml
class SimpleXMLExtended extends SimpleXMLElement // http://coffeerings.posterous.com/php-simplexml-and-cdata
{

    public function addCData($cdata_text)
    {
        $node = dom_import_simplexml($this);
        $no = $node->ownerDocument;
        $node->appendChild($no->createCDATASection($cdata_text));
    }

}
