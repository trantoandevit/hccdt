<?php

defined('DS') or die('no direct access');

class widget_Controller extends Controller
{

    public function __construct()
    {
        parent::__construct('admin', 'widget');
        Session::init();
        Session::get('user_id') or $this->login_admin(); 
        
        Session::check_permission('QL_DANH_SACH_WIDGET') or $this->access_denied();
        
        $arr_temp = $this->model->qry_all_widget_class();
        if ($arr_temp)
        {
            $arr_temp             = explode(',', str_replace(' ', '', $arr_temp));
            $arr_all_widget_class = array();
            foreach ($arr_temp as $val)
            {
                $arr_all_widget_class[$val]                  = $val;
            }
            Session::set('arr_all_widget_class', $arr_all_widget_class);
        }
        $v_lang_id                                   = Session::get('session_lang_id');
        $this->view->template->arr_all_lang          = $this->model->qry_all_lang();
        $this->view->template->arr_count_article     = $this->model->gp_qry_count_article();
        $this->view->template->arr_all_grant_website = $this->model->gp_qry_all_website_by_user($v_lang_id);
    }

    function main()
    {
        $this->dsp_all_widget();
    }

    public function dsp_all_widget()
    {
        $v_website_id       = Session::get('session_website_id');
        $arr_single_website = $this->model->qry_single_website($v_website_id);
        $v_website_code     = $arr_single_website['C_CODE'];
        $v_theme_code       = $arr_single_website['C_THEME_CODE'];

        $v_all_position   = $this->model->qry_all_position();
        $v_all_position   = str_replace(' ', '', $v_all_position);
        $v_all_position   = explode(',', $v_all_position);
        $arr_all_position = array();
        if (!empty($v_all_position))
        {
            foreach ($v_all_position as $single_pos)
            {
                $arr_current_widget = $this->model->qry_current_widget($v_website_code, $v_theme_code, $single_pos);
                $n                  = count($arr_current_widget);
                for ($i = 0; $i < $n; $i++)
                {
                    $v_code   = $arr_current_widget[$i]['C_WIDGET_CODE'];
                    $v_param  = $arr_current_widget[$i]['C_PARAM'];
                    $v_method = 'dsp_admin_widget_' . $v_code;
                    $arr_current_widget[$i]['C_FORM'] = $this->{'dsp_admin_widget_' . $v_code}($v_param);
                
                }
                $arr_all_position[$single_pos]    = $arr_current_widget;
            }
        }
        $data['arr_all_position']         = $arr_all_position;
        $data['arr_all_widget']           = $this->model->qry_all_widget();
        $n                                = count($data['arr_all_widget']);
        for ($i = 0; $i < $n; $i++)
        {
            $item     = $data['arr_all_widget'][$i];
            $v_method = 'dsp_admin_widget_' . $item['C_CODE'];
            if (method_exists($this, $v_method))
            {
                $data['arr_all_widget'][$i]['C_FORM'] = $this->{$v_method}();
            }
            else
            {
                $data['arr_all_widget'][$i]['C_FORM'] = '';
            }
        }

        $data = array_merge($data, $this->get_cache_status());

        $this->view->render('dsp_all_widget', $data);
    }

    private function get_cache_status()
    {
        $data = array();
        $data['cached_file']      = 0;
        $data['cached_file_size'] = 0;
        $cache_root               = SERVER_ROOT . 'cache' . DS;
        $cache_dirs               = array('widget_support');
        foreach ($cache_dirs as $item)
        {
            if (is_dir($cache_root . $item))
            {
                $data['cached_file_size'] += $this->foldersize($cache_root . $item);
                $data['cached_file'] += $this->count_file($cache_root . $item);
            }
        }
        return $data;
    }

    private function count_file($path)
    {
        $size   = 0;
        $ignore = array('.', '..', 'cgi-bin', '.DS_STORE');
        $files = scandir($path);
        foreach ($files as $t)
        {
            if (in_array($t, $ignore))
                continue;
            if (is_dir(rtrim($path, '/') . '/' . $t))
            {
                $size += $this->count_file(rtrim($path, '/') . '/' . $t);
            }
            else
            {
                $size++;
            }
        }
        return $size;
    }

    function recycle_cache()
    {
        $cache_root = SERVER_ROOT . 'cache' . DS;
        $cache_dir  = array('widget_support');
        foreach ($cache_dir as $dir)
        {
            $dir .= DS;
            $this->recursiveDelete($cache_root . $dir);
        }
        $a = file_get_contents(SITE_ROOT);
        echo json_encode($this->get_cache_status());
    }

    /**
     * Delete a file or recursively delete a directory
     *
     * @param string $str Path to file or directory
     */
    private function recursiveDelete($str)
    {
        if (is_file($str))
        {
            return @unlink($str);
        }
        elseif (is_dir($str))
        {
            $scan = glob(rtrim($str, '/') . '/*');
            foreach ($scan as $index => $path)
            {
                $this->recursiveDelete($path);
            }
            return @rmdir($str);
        }
    }

    public function update_widget()
    {
        $v_param  = get_post_var('param', false, false);
        $v_code   = get_post_var('code');
        $v_method = 'update_admin_widget_' . $v_code;
        
        if (($v_param !== false) && $v_code && method_exists($this, $v_method))
        {
            $_POST['param'] = $this->{$v_method}($v_param);
        }
        else
        {
            $_POST['param'] = '';
        }
        echo intval($this->model->update_widget());
    }

    private function foldersize($path)
    {
        $total_size = 0;
        $files      = scandir($path);
        $cleanPath  = rtrim($path, '/') . '/';

        foreach ($files as $t)
        {
            if ($t <> "." && $t <> "..")
            {
                $currentFile = $cleanPath . $t;
                if (is_dir($currentFile))
                {
                    $size = $this->foldersize($currentFile);
                    $total_size += $size;
                }
                else
                {
                    $size = filesize($currentFile);
                    $total_size += $size;
                }
            }
        }

        return $total_size;
    }

    function remove_widget()
    {
       
        $this->model->remove_widget();
    }

    private function admin_widget_header()
    {
        $html = "<form class='frm_widget' name='frm_widget[]' action='#' method='post'>";
        return $html;
    }

    private function admin_widget_footer()
    {
        $v_accept = __('apply');
        $v_close  = __('close');
        $html     = '<div class="widget-button">';
        $html .= "<input type='button' class='ButtonAccept btn-update-widget' 
            value='$v_accept' onClick='update_widget($(this).parents(\".widget-drag:first\"))'/>";
        $html .= "<input type='button' class='ButtonCancel btn-cancel-widget' 
            value='$v_close' onClick='cancel_widget(this)'/>";
        $html .= "</form>";
        $html .= '</div>';
        return $html;
    }

//widget weather -----------------------------------------------------------
   private function dsp_admin_widget_weather($args = '')
    {
        $data['txt_weather_woeid'] = 0;
        $data['txt_weather_title'] = '';
        try
        {
            $args                      = @new SimpleXMLElement($args, LIBXML_NOCDATA);
            $data['txt_weather_woeid'] = (int) $args->txt_weather_woeid;
            $data['txt_weather_title'] = (string) $args->txt_weather_title;
        }
        catch (Exception $ex)
        {
            
        }
        
        $html                = $this->admin_widget_header();
        ob_start();
        $this->view->render('dsp_admin_widget_weather', $data);
        $html .= ob_get_clean();
        $html .= $this->admin_widget_footer();

        return $html;
    }

    private function update_admin_widget_weather($args = '')
    {
        $v_woeid = isset($args['txt_weather_woeid']) ? (int) $args['txt_weather_woeid'] : '';
        $v_title = isset($args['txt_weather_title']) ? (string) $args['txt_weather_title'] : '';

        $xml = "<?xml version='1.0' encoding='utf-8' ?>
            <root>
                <txt_weather_woeid>$v_woeid</txt_weather_woeid>
                <txt_weather_title>$v_title</txt_weather_title>
            </root>
        ";
        return $xml;
    }

//end widget weather -------------------------------------------------------
//widget poll---------------------------------------------------------------

    private function dsp_admin_widget_poll($args = '')
    {
        $data['widget_style'] = '';
        $data['poll_id']      = 0;
        $data['arr_all_poll'] = $this->model->qry_all_poll();
        $data['filter_time']  = 0;
        try
        {
            $args                 = @new SimpleXMLElement($args, LIBXML_NOCDATA);
            $data['poll_id']      = (int) $args->poll_id;
            $data['widget_style'] = (string) $args->widget_style;
        }
        catch (Exception $ex)
        {
            
        }
        $html = $this->admin_widget_header();
        ob_start();
        $this->view->render('dsp_admin_widget_poll', $data);
        $html .= ob_get_clean();
        $html .= $this->admin_widget_footer();
        return $html;
    }

    private function update_admin_widget_poll($args = 0)
    {
        $v_widget_style = isset($args['sel_widget_style']) ? (string) $args['sel_widget_style'] : '';
        $v_poll_id      = isset($args['sel_widget_poll']) ? (int) $args['sel_widget_poll'] : 0;
        $xml            = "<?xml version=\"1.0\" encoding=\"utf-8\" ?>
            <root>
                <poll_id>$v_poll_id</poll_id>
                <widget_style>$v_widget_style</widget_style>
            </root>
            ";
        return $xml;
    }

//end widget poll ----------------------------------------------------------
//widget free_text----------------------------------------------------------
    private function dsp_admin_widget_free_text($args = '')
    {
        $html                 = $this->admin_widget_header();
        ob_start();
        $data['title']        = '';
        $data['content']      = '';
        $data['widget_style'] = '';
        $data['content_only'] = '';
        try
        {
            $args                 = @new SimpleXMLElement($args, LIBXML_NOCDATA);
            $data['title']        = (string) $args->title;
            $data['content']      = (string) $args->content;
            $data['widget_style'] = (string) $args->widget_style;
            $data['content_only'] = (string) $args->content_only;
        }
        catch (Exception $ex)
        {
            
        }
        $this->view->render('dsp_admin_widget_free_text', $data);
        $html .= ob_get_clean();
        $html .= $this->admin_widget_footer();
        return $html;
    }

    private function update_admin_widget_free_text($args)
    {
        $v_title        = isset($args['txt_free_text_title']) ? $args['txt_free_text_title'] : '';
        $v_title        = replace_bad_char($v_title);
        $v_content      = isset($args['txt_free_text_content']) ? $args['txt_free_text_content'] : '';
        $v_content      = Model::prepare_tinyMCE($v_content);
        $v_widget_style = isset($args['sel_widget_style']) ? (string) $args['sel_widget_style'] : '';
        $v_content_only = isset($args['chk_content_only']) ? 'checked' : '';

        $xml_data = "<?xml version=\"1.0\" encoding=\"utf-8\" ?>
            <root>
                <title>$v_title</title>
                <content><![CDATA[$v_content]]></content>
                <widget_style>$v_widget_style</widget_style>
                <content_only>$v_content_only</content_only>
            </root>
            ";
        return $xml_data;
    }

//end widget free_text -----------------------------------------------------
//widget spotlight --------------------------------------------------------------
    private function dsp_admin_widget_spotlight($args = '')
    {
        $data['arr_all_position'] = $this->model->qry_all_spotlight();
        //print_r($data['arr_all_position']);
        $data['selected']         = 0;
        $data['widget_style']     = '';
        $data['display_mode']     = '';
        try
        {
            $args                 = @new SimpleXMLElement($args, LIBXML_NOCDATA);
            $data['selected']     = (int) $args->spotlight_position;
            $data['widget_style'] = (string) $args->widget_style;
            $data['display_mode'] = (string) $args->display_mode;
        }
        catch (Exception $ex)
        {
            
        }
        $html = $this->admin_widget_header();
        ob_start();
        $this->view->render('dsp_admin_widget_spotlight', $data);
        $html .= ob_get_clean();
        $html .= $this->admin_widget_footer();

        return $html;
    }

    private function update_admin_widget_spotlight($args = '')
    {
        $v_position     = isset($args['sel_widget_spotlight']) ? (int) $args['sel_widget_spotlight'] : 0;
        $v_widget_style = isset($args['sel_widget_style']) ? (string) $args['sel_widget_style'] : '';
        $display_mode   = isset($args['sel_display']) ? (string) $args['sel_display'] : '';
        $xml_data       = "<?xml version='1.0' encoding='utf-8' ?>
            <root>
                <spotlight_position>$v_position</spotlight_position>
                <widget_style>$v_widget_style</widget_style>
                <display_mode>$display_mode</display_mode>
            </root>
            ";
        return $xml_data;
    }

//end widget spotlight -----------------------------------------------------
//widget event ------------------------------------------------------------
    private function dsp_admin_widget_event($args = '')
    {
        $data['arr_all_event'] = $this->model->qry_all_event();
        $data['widget_style']     = '';
        $data['display_mode']     = '';
        $data['display_event_id'] = '';
        $data['selected'] = 0;
        try
        {
            $args                 = @new SimpleXMLElement($args, LIBXML_NOCDATA);
            $data['widget_style'] = (string) $args->widget_style;
            $data['display_mode'] = (string) $args->display_mode;
            $data['display_event_id'] = (string) $args->display_event_id;
            $data['selected']     = (int) $args->display_event_id;
        }
        catch (Exception $ex)
        {
            
        }
        $html = $this->admin_widget_header();
        ob_start();
        $this->view->render('dsp_admin_widget_event', $data);
        $html .= ob_get_clean();
        $html .= $this->admin_widget_footer();

        return $html;
    }
    
    private function update_admin_widget_event($args = '')
    {
        $data['arr_all_event'] = $this->model->qry_all_event();
        $v_widget_style = isset($args['sel_widget_style']) ? (string) $args['sel_widget_style'] : '';
        $display_mode   = isset($args['sel_display']) ? (string) $args['sel_display'] : '';
        $display_event_id   = isset($args['sel_widget_event']) ? (int) $args['sel_widget_event'] : '';
        $xml_data       = "<?xml version='1.0' encoding='utf-8' ?>
            <root>
                <widget_style>$v_widget_style</widget_style>
                <display_mode>$display_mode</display_mode>
                <display_event_id>$display_event_id</display_event_id>
            </root>
            ";
        return $xml_data;
    }
//end widget event --------------------------------------------------------
//widget online tv
    private function dsp_admin_widget_online_tv($args = '')
    {
        $data['selected_listtype'] = 0;
        $data['title']             = '';
        try
        {
            $args                      = @new SimpleXMLElement($args, LIBXML_NOCDATA);
            $data['selected_listtype'] = (int) $args->selected_listtype;
            $data['title'] = (string) $args->title;
            $data['chk_radio'] = (string) $args->chk_radio;
        }
        catch (Exception $ex)
        {
            
        }
        $data['arr_all_listtype'] = $this->model->qry_all_listtype();
        
        $html                = $this->admin_widget_header();
        ob_start();
        $this->view->render('dsp_admin_widget_online_tv', $data);
        $html .= ob_get_clean();
        $html .= $this->admin_widget_footer();

        return $html;
    }

    private function update_admin_widget_online_tv($args = '')
    {
        $v_selected_listtype = isset($args['sel_widget_online_tv']) ? (int) $args['sel_widget_online_tv'] : 0;
        $v_title = isset($args['txt_title']) ? (string) $args['txt_title'] : '';
        $v_chk = isset($args['chk_radio']) ? (string) $args['chk_radio'] : '';

        $xml = "<?xml version='1.0' encoding='utf-8' ?>
            <root>
                <selected_listtype>$v_selected_listtype</selected_listtype>
                <title>$v_title</title>
                <chk_radio>$v_chk</chk_radio>
            </root>
        ";
        return $xml;
    }
//end
//widget gallery
    private function dsp_admin_widget_gallery($args = '')
    {
        $data['some_gallery_show'] = 0;
        $data['title']             = '';
        try
        {
            $args                      = @new SimpleXMLElement($args, LIBXML_NOCDATA);
            $data['some_gallery_show'] = (int) $args->some_gallery_show;
        }
        catch (Exception $ex)
        {
            
        }
        
        $html                = $this->admin_widget_header();
        ob_start();
        $this->view->render('dsp_admin_widget_gallery',$data);
        $html .= ob_get_clean();
        $html .= $this->admin_widget_footer();

        return $html;
    }

    private function update_admin_widget_gallery($args = '')
    {
        $v_selected_listtype = isset($args['some_gallery_show']) ? (int) $args['some_gallery_show'] : 0;

        $xml = "<?xml version='1.0' encoding='utf-8' ?>
            <root>
                <some_gallery_show>$v_selected_listtype</some_gallery_show>
            </root>
        ";
        return $xml;
    }
//end
//widget adv ---------------------------------------------------------------
    private function dsp_admin_widget_adv($args = '')
    {
        $data['selected_position'] = 0;
        try
        {
            $args                      = @new SimpleXMLElement($args, LIBXML_NOCDATA);
            $data['selected_position'] = (int) $args->selected_position;
        }
        catch (Exception $ex)
        {
            
        }
        $data['arr_all_adv'] = $this->model->qry_all_adv();
        $html                = $this->admin_widget_header();
        ob_start();
        $this->view->render('dsp_admin_widget_adv', $data);
        $html .= ob_get_clean();
        $html .= $this->admin_widget_footer();

        return $html;
    }

    private function update_admin_widget_adv($args = '')
    {
        $v_selected_position = isset($args['sel_widget_adv']) ? (int) $args['sel_widget_adv'] : 0;
        $v_widget_style      = isset($args['sel_widget_style']) ? (string) $args['sel_widget_style'] : '';

        $xml = "<?xml version='1.0' encoding='utf-8' ?>
            <root>
                <selected_position>$v_selected_position</selected_position>
                <widget_style>$v_widget_style</widget_style>
            </root>
        ";
        return $xml;
    }

//end widget adv -----------------------------------------------------------
//widget category silde
    private function dsp_admin_widget_category_slide($args = '')
    {
        $data['selected_position'] = 0;
        try
        {
            $args                      = @new SimpleXMLElement($args, LIBXML_NOCDATA);
            $data['sel_widget_category_slide'] = (int) $args->sel_widget_category_slide;
            $data['txt_some_news_show'] = (int) $args->txt_some_news_show;
        }
        catch (Exception $ex)
        {
            
        }
        $data['arr_all_category'] = $this->model->qry_all_category();
        $html                = $this->admin_widget_header();
        ob_start();
        $this->view->render('dsp_admin_widget_category_slide', $data);
        $html .= ob_get_clean();
        $html .= $this->admin_widget_footer();

        return $html;
    }

    private function update_admin_widget_category_slide($args = '')
    {
        $v_selected_category = isset($args['sel_widget_category_slide']) ? (int) $args['sel_widget_category_slide'] : 0;
        $v_widget_style      = isset($args['txt_some_news_show']) ? (string) $args['txt_some_news_show'] : '';

        $xml = "<?xml version='1.0' encoding='utf-8' ?>
            <root>
                <sel_widget_category_slide>$v_selected_category</sel_widget_category_slide>
                <txt_some_news_show>$v_widget_style</txt_some_news_show>
            </root>
        ";
        return $xml;
    }
//end
//widget most_visited ---------------------------------------------------
    private function dsp_admin_widget_most_visited($args = '')
    {
        $data['quantity']     = 5;
        $data['widget_style'] = '';
        try
        {
            $args                 = @new SimpleXMLElement($args, LIBXML_NOCDATA);
            $data['quantity']     = (int) $args->dsp_quantity;
            $data['widget_style'] = (string) $args->widget_style;
        }
        catch (Exception $ex)
        {
            
        }
        $html = $this->admin_widget_header();
        ob_start();
        $this->view->render('dsp_admin_widget_most_visited', $data);
        $html .= ob_get_clean();
        $html .= $this->admin_widget_footer();

        return $html;
    }

    private function update_admin_widget_most_visited($args = '')
    {
        $v_dsp_quantity = isset($args['txt_widget_most_visited_quantity']) ? (int) $args['txt_widget_most_visited_quantity'] : 5;
        $v_widget_style = isset($args['sel_widget_style']) ? (string) $args['sel_widget_style'] : '';

        $xml = "<?xml version='1.0' encoding='utf-8' ?>
            <root>
                <dsp_quantity>$v_dsp_quantity</dsp_quantity>
                <widget_style>$v_widget_style</widget_style>
            </root>
        ";
        return $xml;
    }

//end widget most_visited -----------------------------------------------
//widget support -----------------------------------------------------------
    private function dsp_admin_widget_support($args = '')
    {
        $data['widget_style'] = '';

        try
        {
            $args                 = @new SimpleXMLElement($args, LIBXML_NOCDATA);
            $data['widget_style'] = (string) $args->widget_style;
        }
        catch (Exception $ex)
        {
            
        }
        $html = $this->admin_widget_header();
        ob_start();
        $this->view->render('dsp_admin_widget_support', $data);
        $html .= ob_get_clean();
        $html .= $this->admin_widget_footer();

        return $html;
    }

    private function update_admin_widget_support($args = '')
    {
        $v_widget_style = isset($args['sel_widget_style']) ? (string) $args['sel_widget_style'] : '';
        $xml            = "<?xml version='1.0' encoding='utf-8' ?>
            <root>
                <widget_style>$v_widget_style</widget_style>
            </root>
        ";
        return $xml;
    }

//end widget support 
    //widget weblink
    private function dsp_admin_widget_weblink($args = '')
    {
        $data['widget_style'] = '';
        $data['arr_group_weblink']  = $this->model->qry_all_group_weblink();
        try
        {
            $args                       = @new SimpleXMLElement($args, LIBXML_NOCDATA);
            $data['widget_style']       = (string) $args->widget_style;
            $data['group_weblink_id']   = (string) $args->group_weblink_id;
            $data['title_weblink']      = (string) $args->title_weblink;
        }
        catch (Exception $ex)
        {
            
        }
        $html = $this->admin_widget_header();
        ob_start();
        $this->view->render('dsp_admin_widget_weblink', $data);
        $html .= ob_get_clean();
        $html .= $this->admin_widget_footer();

        return $html;
    }

    private function update_admin_widget_weblink($args = '')
    {
        $v_widget_style = isset($args['sel_widget_style']) ? (string) $args['sel_widget_style'] : '';
        $v_group_weblink = isset($args['sel_group_web_link']) ? (string) $args['sel_group_web_link'] : '';
        $v_title_weblink = isset($args['chk_show_title']) ? 1 : 0;
        $xml            = "<?xml version='1.0' encoding='utf-8' ?>
            <root>
                <widget_style>$v_widget_style</widget_style>
                <group_weblink_id>$v_group_weblink</group_weblink_id>
                <title_weblink>$v_title_weblink</title_weblink>
            </root>
        ";
        return $xml;
    }

    //endwidget weblink
    //widget statistic
    private function dsp_admin_widget_statistic($args = '')
    {
        $data['widget_style'] = '';

        try
        {
            $args                 = @new SimpleXMLElement($args, LIBXML_NOCDATA);
            $data['widget_style'] = (string) $args->widget_style;
        }
        catch (Exception $ex)
        {
            
        }
        $html = $this->admin_widget_header();
        ob_start();
        $this->view->render('dsp_admin_widget_statistic', $data);
        $html .= ob_get_clean();
        $html .= $this->admin_widget_footer();

        return $html;
    }
    
     //single_category
    private function dsp_admin_widget_single_category($args = '')
    {
        $data['arr_all_category'] = $this->model->qry_all_category();
        $data['limit']     = '3';
        $data['category_id'] = '';
        try
        {
            $args                 = @new SimpleXMLElement($args, LIBXML_NOCDATA);
            $data['limit'] = (string) $args->limit;
            $data['category_id'] = (string) $args->category_id;
        }
        catch (Exception $ex)
        {
            
        }
        
        $html = $this->admin_widget_header();
        ob_start();
        $this->view->render('dsp_admin_widget_single_category', $data);
        $html .= ob_get_clean();
        $html .= $this->admin_widget_footer();

        return $html;
    }
    private function update_admin_widget_single_category($args = '')
    {
        $v_cat_id = isset($args['sel_category']) ? (string) $args['sel_category'] : '';
        $v_limit  = isset($args['txt_limit']) ? (string) $args['txt_limit'] : '';
        $xml            = "<?xml version='1.0' encoding='utf-8' ?>
            <root>
                <category_id>$v_cat_id</category_id>
                <limit>$v_limit</limit>
            </root>
        ";
        return $xml;
    }
    
    private function update_admin_widget_statistic($args = '')
    {
        $v_widget_style = isset($args['sel_widget_style']) ? (string) $args['sel_widget_style'] : '';
        $xml            = "<?xml version='1.0' encoding='utf-8' ?>
            <root>
                <widget_style>$v_widget_style</widget_style>
            </root>
        ";
        return $xml;
    }

    //endwidget statistic
    //widget_media_article
    private function dsp_admin_widget_media_article($args = '')
    {
        $data['video_limit']   = 3;
        $data['gallery_limit'] = 3;
        try
        {
            $args                  = @new SimpleXMLElement($args, LIBXML_NOCDATA);
            $data['video_limit']   = (int) $args->video_limit;
            $data['gallery_limit'] = (int) $args->gallery_limit;
        }
        catch (Exception $ex)
        {
            
        }
        $html = $this->admin_widget_header();
        ob_start();
        $this->view->render('dsp_admin_widget_media_article', $data);
        $html .= ob_get_clean();
        $html .= $this->admin_widget_footer();

        return $html;
    }

    private function update_admin_widget_media_article($args = '')
    {
        $video_limit   = (int) get_array_value($args, 'txt_video_limit', 3);
        $gallery_limit = (int) get_array_value($args, 'txt_gallery_limit', 3);
        $xml           = "<?xml version='1.0' encoding='utf-8' ?>
            <root>
                <video_limit>$video_limit</video_limit>
                <gallery_limit>$gallery_limit</gallery_limit>
            </root>
        ";
        return $xml;
    }

    //end widget_media_article
    
    //widget subscribe
     private function dsp_admin_widget_subscribe($args = '')
    {
        $data['widget_style']   = '';
        try
        {
            $args                  = @new SimpleXMLElement($args, LIBXML_NOCDATA);
            $data['widget_style']   = $args->widget_style;
        }
        catch (Exception $ex)
        {
            
        }
        $html = $this->admin_widget_header();
        ob_start();
        $this->view->render('dsp_admin_widget_subscribe', $data);
        $html .= ob_get_clean();
        $html .= $this->admin_widget_footer();

        return $html;
    }

    private function update_admin_widget_subscribe($args = '')
    {
        $widget_style   =  get_array_value($args, 'widget_style');
        $xml           = "<?xml version='1.0' encoding='utf-8' ?>
            <root>
                <widget_style>$widget_style</widget_style>
            </root>
        ";
        
        return $xml;
    }

    //end widget subscribe
    //widget video_clip -----------------------------------------------------------
   private function dsp_admin_widget_video_clip($args = '')
    {
       $data['txt_video_clip_quantity'] = 0;
       $data['txt_video_clip_title'] = '';
        try
        {
            $args                      = @new SimpleXMLElement($args, LIBXML_NOCDATA);
            $data['txt_video_clip_quantity'] = (int) $args->txt_video_clip_quantity;
            $data['txt_video_clip_title'] = (string) $args->txt_video_clip_title;
        }
        catch (Exception $ex)
        {
            
        }        
        $html                = $this->admin_widget_header();
        ob_start();
        $this->view->render('dsp_admin_widget_video_clip', $data);
        $html .= ob_get_clean();
        $html .= $this->admin_widget_footer();

        return $html;
    }

    private function update_admin_widget_video_clip($args = '')
    {
        $v_quantity = isset($args['txt_video_clip_quantity']) ? (int) $args['txt_video_clip_quantity'] : '';
        $v_title = isset($args['txt_video_clip_title']) ? (string) $args['txt_video_clip_title'] : '';

       $xml = "<?xml version='1.0' encoding='utf-8' ?>
            <root>
                <txt_video_clip_quantity>$v_quantity</txt_video_clip_quantity>
                <txt_video_clip_title>$v_title</txt_video_clip_title>
            </root>
        ";
        return $xml;
    }

//end widget video_clip -------------------------------------------------------
    
    
//    Tin anh
    private  function dsp_admin_widget_image_news($args = '')
    { 
       $data['txt_image_news_title']     = '';
       $data['txt_image_news_quantity']  = 0;
        $data['sel_image_news_color'] = '';
        try
        {
            $args                      = @new SimpleXMLElement($args, LIBXML_NOCDATA);
            $data['sel_image_news_color']         = (string) $args->sel_image_news_color;
            $data['txt_image_news_quantity']      = (int) $args->txt_image_news_quantity;
            $data['txt_image_news_title']         = (string) $args->txt_image_news_title;
        }
        catch (Exception $ex)
        {
            
        }        
        $html                = $this->admin_widget_header();
        ob_start();
        $this->view->render('dsp_admin_widget_image_news', $data);
        $html .= ob_get_clean();
        $html .= $this->admin_widget_footer();

        return $html;
    }
    private function update_admin_widget_image_news($args = '')
    {
        $v_quantity = isset($args['txt_image_news_quantity']) ? (int) $args['txt_image_news_quantity'] : 0;
        $v_title    = isset($args['txt_image_news_title']) ? (string) $args['txt_image_news_title'] : '';
        $v_position = isset($args['sel_image_news_color']) ? (string) $args['sel_image_news_color'] : '';
        
       $xml = "<?xml version='1.0' encoding='utf-8' ?>
            <root>
               <sel_image_news_color>$v_position</sel_image_news_color>
                <txt_image_news_quantity>$v_quantity</txt_image_news_quantity>
                <txt_image_news_title>$v_title</txt_image_news_title>
            </root>
        ";
        return $xml;
    }
    
    //    Lay tin bai rating cao
    private  function dsp_admin_widget_rating($args = '')
    { 
       $data['txt_rating_title']     = '';
       $data['txt_rating_quantity']  = 0;
        $data['sel_rating_color'] = '';
        try
        {
            $args                      = @new SimpleXMLElement($args, LIBXML_NOCDATA);
            $data['sel_rating_color']         = (string) $args->sel_rating_color;
            $data['txt_rating_quantity']      = (int) $args->txt_rating_quantity;
            $data['txt_rating_title']         = (string) $args->txt_rating_title;
        }
        catch (Exception $ex)
        {
            
        }        
        $html                = $this->admin_widget_header();
        ob_start();
        $this->view->render('dsp_admin_widget_rating', $data);
        $html .= ob_get_clean();
        $html .= $this->admin_widget_footer();

        return $html;
    }
    private function update_admin_widget_rating($args = '')
    {
        $v_quantity = isset($args['txt_rating_quantity']) ? (int) $args['txt_rating_quantity'] : 0;
        $v_title    = isset($args['txt_rating_title']) ? (string) $args['txt_rating_title'] : '';
        $v_position = isset($args['sel_rating_color']) ? (string) $args['sel_rating_color'] : '';
        
       $xml = "<?xml version='1.0' encoding='utf-8' ?>
            <root>
               <sel_rating_color>$v_position</sel_rating_color>
                <txt_rating_quantity>$v_quantity</txt_rating_quantity>
                <txt_rating_title>$v_title</txt_rating_title>
            </root>
        ";
        return $xml;
    }
    
    //cache
    public function create_cache()
    {
        $v_website_id = $website_id = Session::get('session_website_id');
        
        //1 Lay danh sach position cua theme
        $stmt = "Select
                    ExtractValue(C_XML_DATA, '/data/item[@id=\"txtvitriwidget\"]/value')
                From t_cores_list L
                    Inner JOin t_cores_listtype LT
                      On L.FK_LISTTYPE = LT.PK_LISTTYPE
                Where LT.C_CODE = 'DM_THEME'
                      And L.C_CODE = (Select C_THEME_CODE From t_ps_website Where PK_WEBSITE=?)";
        $params = array($v_website_id);
        
        $v_all_position_list = $this->model->db->getOne($stmt, $params);
        $v_all_position_list = str_replace(' ', '', $v_all_position_list);
        $arr_all_position    = explode(',', $v_all_position_list);
        
        if (sizeof($arr_all_position) > 0)
        {
            foreach ($arr_all_position as $v_position)
            {
                $arr_current_widget = $this->model->qry_current_widget($v_website_code, $v_theme_code, $v_position);
                
                echo '<hr/>File: '. __FILE__ . '<br>Line: ' . __LINE__; var_dump::display($arr_current_widget);
                /*
                $n                  = count($arr_current_widget);
                for ($i = 0; $i < $n; $i++)
                {
                    $v_code   = $arr_current_widget[$i]['C_WIDGET_CODE'];
                    $v_param  = $arr_current_widget[$i]['C_PARAM'];
                    $v_method = 'dsp_admin_widget_' . $v_code;
                    if (method_exists($this, $v_method) && Session::check_permission('SUA_WIDGET'))
                    {
                        $arr_current_widget[$i]['C_FORM'] = $this->{'dsp_admin_widget_' . $v_code}($v_param);
                    }
                    else
                    {
                        $arr_current_widget[$i]['C_FORM'] = '';
                    }
                }
                $arr_all_position[$single_pos]    = $arr_current_widget;
                */
            }
        }
        echo '<hr/>File: '. __FILE__ . '<br>Line: ' . __LINE__; var_dump::display($arr_all_position);
    }
}