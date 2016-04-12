<?php

//write cache
function write_cache_data($arr_data, $file_name)
{
    $v_dir = dirname($file_name);
    if (!is_dir($v_dir))
    {
        $balance = mkdir($v_dir, '0777', true);
        if ($balance == false)
        {
            return false;
        }
    }
    $arr_data = json_encode($arr_data);
    if (file_put_contents($file_name, $arr_data) === FALSE)
    {
        return FALSE;
    }

    return TRUE;
}

function set_viewdata_data($website_id, $prefix, &$data)
{
    $file_name = _CONST_SERVER_CACHE_ROOT . $website_id . DS . $prefix . '.html';
    //$data = get_cache_data($file_name);
    if (file_exists($file_name) && (get_system_config_value(CFGKEY_CACHE) == 'true'))
    {
        $data = json_decode(file_get_contents($file_name), true);
        return TRUE;
    }
    return FALSE;
}

function get_cache_data($file_name)
{
    if (file_exists($file_name))
    {
        return json_decode(file_get_contents($file_name), true);
    }
    return array();
}

//end write cache
//Frag function
function replace_bad_char($str)
{
    $str        = stripslashes($str);
    $arr_search = array('&', '<', '>', '"', "'");
    $arr_replace = array('&amp;', '&lt;', '&gt;', '&#34;', '&#39;');
    $str = str_replace($arr_search, $arr_replace, $str);

    return $str;
}
function restore_bad_char($str)
{
    $str        = stripslashes($str);
    $arr_search = array('&amp;', '&lt;', '&gt;', '&#34;', '&#39;');
    $arr_replace = array('&', '<', '>', '"', "'");
    $str = str_replace($arr_search, $arr_replace, $str);
    return $str;
}

//end func replace_bad_char

function create_single_xml_node($name, $value, $cdata = FALSE)
{
    $node = '<' . $name . '>';
    $node .= ($cdata) ? '<![CDATA[' . $value . ']]>' : $value;
    $node .= '</' . $name . '>';

    return $node;
}

//end func create_single_xml_node

function hidden($name, $value = '')
{
    if (strpos($value, '"') !== FALSE)
    {
        return '<input type="hidden" name="' . $name . '" id="' . $name . '" value=\'' . $value . '\' />';
    }
    else
    {
        return '<input type="hidden" name="' . $name . '" id="' . $name . '" value="' . $value . '" />';
    }
}

function page_calc(&$v_start, &$v_end)
{
    //Luu dieu kien loc
    $v_page          = isset($_POST['sel_goto_page']) ? replace_bad_char($_POST['sel_goto_page']) : 1;
    $v_rows_per_page = isset($_POST['sel_rows_per_page']) ? replace_bad_char($_POST['sel_rows_per_page']) : _CONST_DEFAULT_ROWS_PER_PAGE;

    $v_start = $v_rows_per_page * ($v_page - 1) + 1;
    $v_end   = $v_start + $v_rows_per_page - 1;
}

function is_id_number($id)
{
    return (preg_match('/^\d*$/', trim($id)) == 1);
}

function get_post_var($html_object_name, $default_value = '', $is_replace_bad_char = TRUE)
{
    $var = isset($_POST[$html_object_name]) ? $_POST[$html_object_name] : $default_value;

    if ($is_replace_bad_char)
    {
        return replace_bad_char($var);
    }

    return $var;
}

function get_request_var($html_object_name, $default_value = '', $is_replace_bad_char = TRUE)
{
    $var = isset($_REQUEST[$html_object_name]) ? $_REQUEST[$html_object_name] : $default_value;

    if ($is_replace_bad_char)
    {
        return replace_bad_char($var);
    }

    return $var;
}

function get_filter_condition($arr_html_object_name = array())
{
    $arr_filter = array();
    foreach ($arr_html_object_name as $v_html_object_name)
    {
        $arr_filter[$v_html_object_name] = get_request_var($v_html_object_name);
    }

    return $arr_filter;
}

function debug($var, $show_type = false)
{
    $html = '';
    if (!defined('DEBUG_HIGHLIGHT'))
    {
        $html .= '<link rel="stylesheet" type="text/css" href="'
                . SITE_ROOT . '/' . 'public/highlight/default.min.css' . '"/>';
        $html .= '<script src="' . SITE_ROOT . 'public/highlight/highlight.min.js' . '"></script>';
        $html .= '<script>hljs.initHighlightingOnLoad();</script>';
        define('DEBUG_HIGHTLIGHT', 1);
    }

    $html .= '<div 
        style="border:1px dashed orange;background-color:#333;
        color:white;padding:10px;margin:0 auto;margin-bottom:5px;margin-top:5px;max-width:640px;">';
    $html .= '<h4 style="background-color:orange;color:black;margin-top:0;padding:5px;">Function [debug]</h4>';
    $html .= '<pre><code>';
    ob_start();
    if ($show_type)
        var_dump($var);
    else
        print_r($var);

    $html .= (htmlentities(ob_get_clean()));


    $html .= '</code></pre>';
    $html .= '</div>';

    echo $html;
}

function build_url($param = array(), $opt = array())
{
    $url = '';
    if (count($param) == 0)
        return SITE_ROOT;

    if (file_exists('.htaccess'))
    {
        foreach ($param as $key => $item)
        {
            if ($key != 'url')
            {
                $url .= $item . '/';
            }
        }

        $n = count($opt);
        if ($n > 0)
        {
            $arr_key = array_keys($opt);
            $url .= '?' . $arr_key[0] . '=' . $opt[$arr_key[0]];

            for ($i = 1; $i < $n; $i++)
            {
                $url .= '&' . $arr_key[$i] . '=' . $opt[$arr_key[$i]];
            }
        }
    }
    else
    {
        $url     = 'index.php?';
        $param   = array_merge($param, $opt);
        $arr_key = array_keys($param);

        $url .= $arr_key[0] . '=' . $param[$arr_key[0]];
        $n = count($param);

        for ($i = 1; $i < $n; $i++)
        {
            $key = $arr_key[$i];
            $url .= "&$key={$param[$key]}";
        }
    }

    return SITE_ROOT . $url;
}

function cur_url()
{

    $pageURL = 'http';
    if (!empty($_SERVER['HTTPS']))
    {
        if ($_SERVER['HTTPS'] == 'on')
        {
            $pageURL .= "s";
        }
    }
    $pageURL .= "://";
    if ($_SERVER["SERVER_PORT"] != "80")
    {
        $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
    }
    else
    {
        $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
    }
    return $pageURL;
}

//build url frontend
function check_file_htaccess()
{
    $path = SERVER_ROOT . ".htaccess";
    return file_exists($path);
}

function build_url_article($category_slug, $article_slug, $website_id, $category_id, $article_id)
{
    $category_slug = auto_slug($category_slug);
    $article_slug  = auto_slug($article_slug);
    if (check_file_htaccess())
    {
        $url = SITE_ROOT . "tin-bai/" . $category_slug . "/" . $article_slug . '/' . $website_id . "-" . $category_id . "-" . $article_id;
    }
    else
    {
        $url = SITE_ROOT . "index.php?url=frontend/frontend/dsp_single_article/&website_id=$website_id&category_id=$category_id&article_id=$article_id&as=$article_slug&cs=$category_slug";
    }
    return $url;
}

function build_url_category($category_slug, $website_id, $category_id)
{
    $category_slug = auto_slug($category_slug);
    if (check_file_htaccess())
    {
        $url = SITE_ROOT . "chuyen-muc" . '/' . $category_slug . '/' . $website_id . "-" . $category_id;
    }
    else
    {
        $url = SITE_ROOT . "index.php?url=frontend/frontend/dsp_single_category/&website_id=$website_id&category_id=$category_id&cs=$category_slug";
    }
    return $url;
}

function build_url_event($event_slug, $website_id, $event_id)
{
    if (check_file_htaccess())
    {
        $url = SITE_ROOT . 'su-kien' . '/' . $event_slug . '/' . $website_id . '-' . $event_id;
    }
    else
    {
        $url = SITE_ROOT . "index.php?url=frontend/frontend/dsp_single_event/&website_id=$website_id&event_id=$event_id&es=$event_slug";
    }
    return $url;
}
/**
 * tao link cho chuc nang lien ket web
 * @param type $website_id
 * @return string
 */
function build_url_weblink($website_id)
{
    if (check_file_htaccess())
    {
        $url = SITE_ROOT . 'weblink' . '/' . $website_id;
    }
    else
    {
        $url = SITE_ROOT . "index.php?url=frontend/frontend/dsp_weblink/&website_id=$website_id";
    }
    return $url;
}
function build_url_sitemap($website_id)
{
    if (check_file_htaccess())
    {
        $url = SITE_ROOT . 'sitemap' . '/' . $website_id;
    }
    else
    {
        $url = SITE_ROOT . "index.php?url=frontend/frontend/dsp_all_sitemap/&website_id=$website_id";
    }
    return $url;
}
function build_url_home($website_id)
{
    if (check_file_htaccess())
    {
        $url = SITE_ROOT . $website_id;
    }
    else
    {
        $url = SITE_ROOT . "index.php?url=frontend/frontend/&website_id=$website_id";
    }
    return $url;
}
/**
 * tao duong link chuc nang thong tin toa soan
 * @param type $website_id
 * @param type $category_id
 * @param type $article_id
 * @return string
 */
function build_url_office_info($website_id,$category_id,$article_id)
{
    if (check_file_htaccess())
    {
        $url = SITE_ROOT .'thong-tin-toa-soan/'. $website_id . '-'.$category_id.'-'.$article_id;
    }
    else
    {
        $url = SITE_ROOT . "index.php?url=frontend/frontend/dsp_office_info/&website_id=$website_id&category_id=$category_id&article_id=$article_id";
    }
    return $url;
}

function build_url_contact_advertising($website_id, $list_id)
{
    if (check_file_htaccess())
    {
        $url = SITE_ROOT.'lien-he-quang-cao/'.$website_id.'-'.$list_id;
    }
    else
    {
        $url = SITE_ROOT.'index.php?url=frontend/frontend/dsp_contact_advertising/&website_id='.$website_id.'&list_id='.$list_id;
    }
    return $url;
}

function build_url_print($category_slug, $article_slug, $website_id, $category_id, $article_id)
{
    $category_slug = auto_slug($category_slug);
    $article_slug  = auto_slug($article_slug);
    if (check_file_htaccess())
    {
        $url = SITE_ROOT . "in-trang" . '/' . $category_slug . '/' . $article_slug . '/' . $website_id . "-" . $category_id . "-" . $article_id;
    }
    else
    {
        $url = SITE_ROOT . "index.php?url=frontend/frontend/dsp_print_article/&website_id=$website_id&category_id=$category_id&article_id=$article_id";
    }
    return $url;
}

function build_url_photo_gallery($website_id, $slug, $gallery_id)
{
    if (check_file_htaccess())
    {
        $url = SITE_ROOT . "phong-su-anh/$slug/$website_id-$gallery_id";
    }
    else
    {
        $url = SITE_ROOT . "index.php?url=frontend/frontend/dsp_single_gallery/&website_id=$website_id&gallery_id=$gallery_id";
    }
    return $url;
}
/**
 * tao duong link dat bao 
 * @param type $website_id
 * @return string
 */
function build_url_magazine_subscriptions($website_id)
{
    if (check_file_htaccess())
    {
        $url = SITE_ROOT . "dat-bao/$website_id";
    }
    else
    {
        $url = SITE_ROOT . "index.php?url=frontend/frontend/dsp_magazine_subscriptions/&website_id=$website_id";
    }
    return $url;
}

/**
 * tao url danh sach tat ca phong su anh
 * @param type $website_id
 * @return string
 */
function build_url_all_photo_gallery($website_id)
{
    if (check_file_htaccess())
    {
        $url = SITE_ROOT . "danh-sach-phong-su-anh/$website_id";
    }
    else
    {
        $url = SITE_ROOT . "index.php?url=frontend/frontend/dsp_all_gallery/&website_id=$website_id";
    }
    return $url;
}

/**
 * 
 * @param type $website_id
 * @return string
 */
function build_url_all_img_news($website_id)
{
    if (check_file_htaccess())
    {
        $url = SITE_ROOT . "danh-sach-tin-anh/$website_id";
    }
    else
    {
        $url = SITE_ROOT . "index.php?url=frontend/frontend/dsp_all_img_news/&website_id=$website_id";
    }
    return $url;
}

function build_url_search($website_id, $keywords = '', $category = '', $start_date = '', $end_date = '')
{
    if (check_file_htaccess())
    {
        $url       = SITE_ROOT . "tim-kiem/$website_id/";
        $start_get = '?';
    }
    else
    {
        $url       = SITE_ROOT . "index.php?url=frontend/frontend/dsp_search/&website_id=$website_id";
        $start_get = '&';
    }
    $url .= $start_get . "keywords=$keywords&category=$category&begin_date=$start_date&end_date=$end_date";
    return $url;
}

function build_url_rss($category_slug, $website_id, $category_id)
{
    $category_slug = auto_slug($category_slug);
    if (check_file_htaccess())
    {
        $url = SITE_ROOT . "rss/$category_slug/$website_id-$category_id";
    }
    else
    {
        $url = SITE_ROOT . "index.php?url=frontend/frontend/dsp_rss/&website_id=$website_id&category_id=$category_id";
    }
    return $url;
}
function build_url_submit_internet_record($record_type='',$member_id='')
{
    
    if (check_file_htaccess())
    {
        $url = SITE_ROOT . "dich_vu_cong/nop_ho_so/";
        if($record_type != '')
        {
            $url .= $record_type;
        }
        if($member_id != '')
        {
            $url .= '-'. $member_id;
        }
    }
    else
    {
        $url = SITE_ROOT . "index.php?url=frontend/frontend/redirect_internet_record/dsp_submit_internet_record";
        if($record_type != '')
        {
            $url .= "&record_type=$record_type";
        }
        if($member_id != '')
        {
            $url .= "&member_id=$member_id";
        }
    }
    return $url;
}
/**
 * tao url dang ky tthc qua mang
 * @param type $member_id
 * @param type $spec_code
 * @param type $record_type
 * @return type
 */
function build_url_send_internet_record($member_id = '',$spec_code = '',$record_type='')
{
    if (check_file_htaccess())
    {
        $url = SITE_ROOT . "dich_vu_cong";
        
        if($member_id == '' && $spec_code == ''&& $record_type == '')
        {
            $url .= '/danh_sach';
        }
        
        if($member_id != '' && $spec_code == '' && $record_type == '')
        {
            $url .= '/danh_sach/'. $member_id;
        }
        
        if($member_id != '' && $spec_code != '')
        {
            $url .= '/danh_sach/'. $member_id . '-' . $spec_code;
        }
        
        if($record_type != '' && $member_id == '')
        {
            $url .= '/nop_ho_so/'. $record_type;
        }
        elseif($member_id != '' && $record_type != '')
        {
            $url .=  '/nop_ho_so/'. $record_type . '-' . $member_id;
        }
    }
    else
    {
        $url = SITE_ROOT . "index.php?url=frontend/frontend/redirect_internet_record/";
        if($member_id == '' && $spec_code == '')
        {
            $url .= 'dsp_guidance_internet_record';
        }
        else
        {
            $url .= "dsp_list_internet_record&member_id=$member_id&spec_code=$spec_code";
        }
    }
    return $url;
}

function build_url_all_category($website_id)
{
    if (check_file_htaccess())
    {
        $url = SITE_ROOT . "ds-chuyen-muc/$website_id";
    }
    else
    {
        $url = SITE_ROOT . "index.php?url=frontend/frontend/dsp_all_category/&website_id=$website_id";
    }
    return $url;
}

function build_url_tags($website_id, $tags)
{
    if (check_file_htaccess())
    {
        $url = SITE_ROOT . "tags/$tags/$website_id";
    }
    else
    {
        $url = SITE_ROOT . "index.php?url=frontend/frontend/dsp_tags/&website_id=$website_id&tags=$tags";
    }
    return $url;
}
/**
 * tao link cho gop y phan hoi
 * @param type $website_id
 * @return string
 */
function build_url_feedback($website_id,$page = 0)
{
    $url = SITE_ROOT . "feedback/";
    if(intval($page) >0) // Xem danh sach feedback
    {
        if (check_file_htaccess())
        {
           $url .= $website_id .'-'.$page; 
        }
        else
        {
            $url .= '?$website='.$website_id.'&page='.$page;
        }
        return $url;
    }
    //Send feedback
    if (check_file_htaccess())
    {
        $url .= 'send/'.$website_id;
    }
    else
    {
        $url .= 'send?website_id'.$website_id;
    }
    return $url;
}

function build_url_cq($website_id)
{
    if (check_file_htaccess())
    {
        $url = SITE_ROOT . "hoi-dap/$website_id";
    }
    else
    {
        $url = SITE_ROOT . "index.php?url=frontend/frontend/dsp_all_cq/&website_id=$website_id";
    }
    return $url;
}

function build_url_cq_detail($website_id, $v_slug, $cq_id)
{
    if (check_file_htaccess())
    {
        $url = SITE_ROOT . "tra-loi/$v_slug/$website_id-$cq_id";
    }
    else
    {
        $url = SITE_ROOT . "index.php?url=frontend/frontend/dsp_single_cq/&slug_cq=$v_slug&website_id=$website_id&cq_id=$cq_id";
    }
    return $url;
}

function build_url_set_cq($website_id)
{
    if (check_file_htaccess())
    {
        $url = SITE_ROOT . "dat-cau-hoi/$website_id";
    }
    else
    {
        $url = SITE_ROOT . "index.php?url=frontend/frontend/dsp_set_question/&website_id=$website_id";
    }
    return $url;
}

function build_url_all_rss($website_id)
{
    if (check_file_htaccess())
    {
        $url = SITE_ROOT . "rss/$website_id";
    }
    else
    {
        $url = SITE_ROOT . "index.php?url=frontend/frontend/dsp_all_rss/&website_id=$website_id";
    }
    return $url;
}
/**
 * tao link dang ky tin thu frontend
 * @param type $website_id
 * @return string
 */
function build_url_subscribe($website_id)
{
    if (check_file_htaccess())
    {
        $url = SITE_ROOT . "tin-thu/$website_id";
    }
    else
    {
        $url = SITE_ROOT . "index.php?url=frontend/frontend/dsp_subscribe/&website_id=$website_id";
    }
    return $url;
}

function build_url_cq_field($website_id, $field_id)
{
    if (check_file_htaccess())
    {
        $url = SITE_ROOT . "hoi-dap/linh-vuc/$website_id-$field_id";
    }
    else
    {
        $url = SITE_ROOT . "index.php?url=frontend/frontend/dsp_single_cq/&website_id=$website_id&field_id=$field_id";
    }
    return $url;
}

/**
 * Xoa bo cac the HTML dinh dang trong doan text
 *
 * @param string $str Xau html
 * @return string Xau da XOA het cac the HTML
 */
function remove_html_tag($str)
{
    $search = array("'<[\/\!]*?[^<>]*?>'si");          // Strip out HTML tags

    $replace = array("");
    return preg_replace($search, $replace, $str);
}

//asdasd
function auto_slug($str)
{
    //LienND update 2013-04-03
    //1.Xoa dau cach thua
    $v_one_space = chr(32);
    $str         = trim(preg_replace('/\s\s+/', $v_one_space, $str));

    //Chuyen unicode có dấu -> khong dấu
    $str = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/", 'a', $str);
    $str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/", 'e', $str);
    $str = preg_replace("/(ì|í|ị|ỉ|ĩ)/", 'i', $str);
    $str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/", 'o', $str);
    $str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/", 'u', $str);
    $str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/", 'y', $str);
    $str = preg_replace("/(đ)/", 'd', $str);
    $str = preg_replace("/(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)/", 'A', $str);
    $str = preg_replace("/(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)/", 'E', $str);
    $str = preg_replace("/(Ì|Í|Ị|Ỉ|Ĩ)/", 'I', $str);
    $str = preg_replace("/(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)/", 'O', $str);
    $str = preg_replace("/(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)/", 'U', $str);
    $str = preg_replace("/(Ỳ|Ý|Ỵ|Ỷ|Ỹ)/", 'Y', $str);
    $str = preg_replace("/(Đ)/", 'D', $str);

    //Chuyen dau cach thanh gach ngang
    $str = str_replace($v_one_space, '-', $str);
    //$str = preg_replace("/( )/", '-', $str);
    //Xoa tat ca cac ky tu khac
    $str = replace_slug_bar_char($str);
    
    //Chuyen nhieu dau gach thanh 1 dau gach
    $str = preg_replace('/[-]+/u', '-', $str);
    return $str;
}

function replace_slug_bar_char($str)
{
    return preg_replace('/([^a-zA-Z0-9-])/', '', $str);
}

function get_leftmost_words($text, $word_count)
{
    $s            = chr(32);
    $text         = preg_replace('/\s+/u', $s, $text);
    $arr_all_word = explode($s, $text);
    $ret          = '';
    for ($i = 0; $i < $word_count - 1; $i++)
    {
        if (isset($arr_all_word[$i]))
        {
            $ret .= $arr_all_word[$i] . $s;
        }
        else
        {
            return $ret;
        }
    }
    
    return $ret .'...';
}

function replace_video($m)
{
    
    $count = (int) Session::get('COUNT_PAGE_VIDEO');
    Session::set('COUNT_PAGE_VIDEO', $count + 1);
    
    $v_width  = (defined('_CONST_VIDEO_WIDTH') == TRUE)? _CONST_VIDEO_WIDTH:'100%';
    $v_height = (defined('_CONST_VIDEO_HEIGHT') == TRUE)? _CONST_VIDEO_HEIGHT:'100%';
    
    $v_file_extension = substr(strrchr($m[1],'.'),1);
    $v_style = '';
    $v_provider = 'video';
    if($v_file_extension == 'mp3')
    {
        $v_style = ' style="margin: 0 auto;height:24px"';
        $v_provider = 'sound';
    }
    $html  = '
        <div style="width:100%;float:left">
            <center>
                    <div class="video_container" '.$v_style.'>
                        <embed 
                            id="ply'.$count.'" 
                            src="'. SITE_ROOT .'public/jwplayer/player.swf" 
                            width="'.$v_width.'" height="'.$v_height.'" type="application/x-shockwave-flash" 
                            data="'. SITE_ROOT .'public/jwplayer/player.swf" 
                            allowscriptaccess="always" allowfullscreen="true" wmode="transparent" 
                            flashvars="height='.$v_height.';width='.$v_width.';plugins=ova&amp;file='.$m[1].'&amp;image='.Session::get('VIDEO_THUMBNAIL').' &amp;provider='.$v_provider .'&amp;controlbar=bottom&amp;volume=100&amp;stretching=exactfit"
                            />
                    </div>  
            </center>
        </div>
   ';
    return $html;
}
function replace_mobile_video($m)
{
    $count = (int) Session::get('COUNT_PAGE_VIDEO');
    Session::set('COUNT_PAGE_VIDEO', $count + 1);
    
    $v_file_name_without_extention = $m[1];
    
    $v_player_string .= '<video id="hotplayer' . $count .'" autobuffer height="450px" width="98%" style="width: 98%; height: 450px;" controls="controls" poster="' . Session::get('VIDEO_THUMBNAIL') . '" loop>';
    $v_player_string .= '	<source src="' . $m[1] . '" type="video/mp4">';
    $v_player_string .= '	<source src="' . $m[1] . '.ogg" type="video/ogg">';
    $v_player_string .= '	<source src="' . $m[1] . '.webm" type="video/webm">';      
    $v_player_string .= '</video>';	
    
    return $v_player_string;
}

function get_system_config_value($key)
{
    global $CONFIG;
    if (method_exists($CONFIG, 'xpath') == false)
    {
        return null;
    }
    $xpath = "//item[@id='$key'][last()]/value/text()";
    $arr   = $CONFIG->xpath($xpath);
    return isset($arr[0]) ? (string) $arr[0] : null;
}

function xml_remove_declaration($xml_string)
{
    return trim(preg_replace('/\<\?xml(.*)\?\>/', '', $xml_string));
}

function xml_add_declaration($xml_string, $utf8_encoding = TRUE)
{
    $xml_string = xml_remove_declaration($xml_string);
    
    if ($xml_string == '')
    {
        $xml_string = '<root/>';
    }

    if ($utf8_encoding)
    {
        return '<?xml version="1.0" encoding="UTF-8"?>' . $xml_string;
    }

    return '<?xml version="1.0" standalone="yes"?>' . $xml_string;
}

function get_xml_value($dom, $xpath)
{
    $r = $dom->xpath($xpath);
    if (isset($r[0]))
    {
        return strval($r[0]);
    }

    return NULL;
}

// Fixes the encoding to uf8 
function fixEncoding($in_str)
{
    $cur_encoding = mb_detect_encoding($in_str);
    if ($cur_encoding == "UTF-8" && mb_check_encoding($in_str, "UTF-8"))
    {
        return $in_str;
    }
    else
    {
        return utf8_encode($in_str);
    }
}

// fixEncoding 

function get_array_value($array, $key, $default_val = '')
{
    return isset($array[$key]) ? $array[$key] : $default_val;
}

function encrypt_password($password)
{
    $length_pass = strlen($password);
    if ($length_pass < 5)
    {
        return '';
    }

    $no_start  = ceil($length_pass / 2);
    $no_end    = ($length_pass - $no_start);
    $start_str = substr($password, 0, $no_start);
    $end_str   = substr($password, $no_start, $no_end);

    return md5(md5($start_str) . base64_encode($end_str));
}

function is_ajax()
{
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
    {
        return true;
    }
    return false;
}

function debug_timetrack_start(&$v_time_start, $message = '')
{
    $traces = debug_backtrace();
    echo '<pre>Function: ' . $traces[1]['function'] . '</pre>';

    $v_time_start = microtime(true);
}

function debug_timetrack_finish($v_time_start)
{
    $v_time_end = microtime(true);
    $time_spent = $v_time_end - $v_time_start;
    echo '<pre>----->Tong thoi gian thuc hien: ' . $time_spent . ' giay</pre>';
}

//function restore_XML_bad_char($v_xml_string)
//{
//	$v_xml_string = str_replace('&amp;','&',$v_xml_string);
//	$v_xml_string = str_replace('&quot;','"',$v_xml_string);
//	$v_xml_string = str_replace('&#39;',"'",$v_xml_string);
//	$v_xml_string = str_replace('&lt;','<',$v_xml_string);
//	$v_xml_string = str_replace('&gt;','>',$v_xml_string);
//	$v_xml_string = str_replace('&#34;','"',$v_xml_string);
//    
//	return $v_xml_string;
//}
//function replace_XML_bad_char($v_xml_string)
//{
//	$v_xml_string = stripslashes($v_xml_string);
//	$v_xml_string = str_replace('&','&amp;',$v_xml_string);
//	$v_xml_string = str_replace('"','&quot;',$v_xml_string);
//	$v_xml_string = str_replace('<','&lt;',$v_xml_string);
//	$v_xml_string = str_replace('>','&gt;',$v_xml_string);
//	$v_xml_string = str_replace("'",'&#39;', $v_xml_string);
//    
//	return $v_xml_string;
//}

function fix_xml_cdata($v_xml_data)
{
    $v_xml_data = str_replace('<value>', '<value><![CDATA[', $v_xml_data);
    $v_xml_data = str_replace('</value>', ']]></value>', $v_xml_data);
    
    //<value>true</value>
    //<value>false</value>
    return $v_xml_data;
}

/**
 * Xoa het cac dinh dang cua MS Word trong noi dung tn bai
 * @param string $v_html
 * @return string
 */
function clean_msword_html($v_html) {
	// 1. remove line breaks / Mso classes
	$stringStripper = '/(\n|\r| class=(")?Mso[a-zA-Z]+(")?)/si';
	$output = preg_replace($stringStripper, ' ', $v_html);

	// 2. strip Word generated HTML comments
	$commentSripper = '/<!--(.*?)-->/si';
	$output = preg_replace($commentSripper, '', $output);

	// 3. remove tags leave content if any
	$tagStripper = '/<(\/)*(meta|link|span|\\?xml:|st1:|o:|font)(.*?)>/si';
	$output = preg_replace($tagStripper, '', $output);

	// 4. Remove everything in between and including tags '<style(.)style(.)>'
	$badTags = array('style', 'script','applet','embed','noframes','noscript');

	for ($i=0; $i< count($badTags); $i++)
	{
		$tagStripper = '/<' . $badTags[$i] . '(.*?)' . $badTags[$i] . '(.*?)>/si';
		$output = preg_replace($tagStripper, '', $output);
	}
	 
	// 5. remove attributes ' style="..."'
	$badAttributes = array('style', 'start');
	for ($i=0; $i< count($badAttributes);$i++)
	{
		$attributeStripper = '/ ' .  $badAttributes[$i] . '="(.*?)"/si';
		$output = preg_replace($attributeStripper, '', $output);
	}

	//6. remove space before close tag >
	$output = preg_replace('/\s+>/si','>',$output);

	//7. Mutil spaces to 1 space
    $output         = preg_replace('/\s+/u', chr(32), $output);

	//No space between close tag and next open tag
	$output         = preg_replace('/>\s+</u', '><', $output);

	return $output;
}
/**
 * Lay gia tri cho boi dau hieu mau
 *
 * @param string $html_content Xau can lay
 * @param string dau hieu bat dau $bp
 * @param string dau hieu ket thuc $ep
 * @return string xau thu duoc
 */
function get_value_by_pattern($html_content,$bp,$ep){
	//$bp = _remove_html_tag($bp);
	//$ep = _remove_html_tag($ep);

	preg_match("/$bp(.+)$ep/eUim",$html_content,$arr_matches);
	if (count($arr_matches) >= 1){
		//return _restore_html_tag($arr_matches[1]);
		return ($arr_matches[1]);
	}
	else{
		return '';
	}
}



function check_user_token()
{
    if (get_request_var('user_token', 'USER_NO_TOKEN') == session::get('user_token',''))
    {
        return TRUE;
    }
    
    return FALSE;
}

function unicode_to_nosign($str)
{
    $ret_str = Array();

    $unicode	= preg_split( "/\,/", 'á,à,ả,ã,ạ,ă,ắ,ằ,ẳ,ẵ,ặ,â,ấ,ầ,ẩ,ẫ,ậ,é,è,ẻ,ẽ,ẹ,ê,ế,ề,ể,ễ,ệ,í,ì,ỉ,ĩ,ị,ó,ò,ỏ,õ,ọ,ô,ố,ồ,ổ,ỗ,ộ,ơ,ớ,ờ,ở,ỡ,ợ,ú,ù,ủ,ũ,ụ,ư,ứ,ừ,ử,ữ,ự,ý,ỳ,ỷ,ỹ,ỵ,đ,Á,À,Ả,Ã,Ạ,Ă,Ắ,Ằ,Ẳ,Ẵ,Ặ,Â,Ấ,Ầ,Ẩ,Ẫ,Ậ,É,È,Ẻ,Ẽ,Ẹ,Ê,Ế,Ề,Ể,Ễ,Ệ,Í,Ì,Ỉ,Ĩ,Ị,Ó,Ò,Ỏ,Õ,Ọ,Ô,Ố,Ồ,Ổ,Ỗ,Ộ,Ơ,Ớ,Ờ,Ở,Ỡ,Ợ,Ú,Ù,Ủ,Ũ,Ụ,Ư,Ứ,Ừ,Ử,Ữ,Ự,Ý,Ỳ,Ỷ,Ỹ,Ỵ,Đ');

    $nosign	= preg_split( "/\,/", 'a,a,a,a,a,a,a,a,a,a,a,a,a,a,a,a,a,e,e,e,e,e,e,e,e,e,e,e,i,i,i,i,i,o,o,o,o,o,o,o,o,o,o,o,o,o,o,o,o,o,u,u,u,u,u,u,u,u,u,u,u,y,y,y,y,y,d,A,A,A,A,A,A,A,A,A,A,A,A,A,A,A,A,A,E,E,E,E,E,E,E,E,E,E,E,I,I,I,I,I,O,O,O,O,O,O,O,O,O,O,O,O,O,O,O,O,O,U,U,U,U,U,U,U,U,U,U,U,Y,Y,Y,Y,Y,D');

    foreach( $unicode as $key => $val) $ret_str[ $val]= $nosign[ $key];

    return strtr( $str, $ret_str);
}

function utf8_to_nosign($str)
{
    $ret_str = Array();

    $utf8	= preg_split( "/\,/", 'Ã¡,Ã ,áº£,Ã£,áº¡,Äƒ,áº¯,áº±,áº³,áºµ,áº·,Ã¢,áº¥,áº§,áº©,áº«,áº­,Ã©,Ã¨,áº»,áº½,áº¹,Ãª,áº¿,á»,á»ƒ,á»…,á»‡,Ã­,Ã¬,á»‰,Ä©,á»‹,Ã³,Ã²,á»,Ãµ,á»,Ã´,á»‘,á»“,á»•,á»—,á»™,Æ¡,á»›,á»,á»Ÿ,á»¡,á»£,Ãº,Ã¹,á»§,Å©,á»¥,Æ°,á»©,á»«,á»­,á»¯,á»±,Ã½,á»³,á»·,á»¹,á»µ,Ä‘,Ã,Ã€,áº¢,Ãƒ,áº ,Ä‚,áº®,áº°,áº²,áº´,áº¶,Ã‚,áº¤,áº¦,áº¨,áºª,áº¬,Ã‰,Ãˆ,áºº,áº¼,áº¸,ÃŠ,áº¾,á»€,á»‚,á»„,á»†,Ã,ÃŒ,á»ˆ,Ä¨,á»Š,Ã“,Ã’,á»Ž,Ã•,á»Œ,Ã”,á»,á»’,á»”,á»–,á»˜,Æ ,á»š,á»œ,á»ž,á» ,á»¢,Ãš,Ã™,á»¦,Å¨,á»¤,Æ¯,á»¨,á»ª,á»¬,á»®,á»°,Ã,á»²,á»¶,á»¸,á»´,Ä');

    $nosign	= preg_split( "/\,/", 'a,a,a,a,a,a,a,a,a,a,a,a,a,a,a,a,a,e,e,e,e,e,e,e,e,e,e,e,i,i,i,i,i,o,o,o,o,o,o,o,o,o,o,o,o,o,o,o,o,o,u,u,u,u,u,u,u,u,u,u,u,y,y,y,y,y,d,A,A,A,A,A,A,A,A,A,A,A,A,A,A,A,A,A,E,E,E,E,E,E,E,E,E,E,E,I,I,I,I,I,O,O,O,O,O,O,O,O,O,O,O,O,O,O,O,O,O,U,U,U,U,U,U,U,U,U,U,U,Y,Y,Y,Y,Y,D');

    foreach( $utf8 as $key => $val) $ret_str[ $val]= $nosign[ $key];

    return strtr( $str, $ret_str);
}

function build_url_synthesis($v_website_id=0,$type='',$method='')
{
    if (check_file_htaccess())
    {
        $url       = FULL_SITE_ROOT . "tra_cuu_tong_hop";
        if($type != '')
        {
            $type_code = '';
            switch ($type)
            {
                case 'member':
                    $type_code = 'tong_hop_theo_don_vi';
                    break;
                case 'spec':
                    $type_code = 'tong_hop_theo_linh_vuc';
                    break;
                case 'liveboard':
                    $type_code = 'bang_theo_doi_truc_tuyen';
                    break;
                case 'chart':
                    $type_code = 'bieu_do_thong_ke';
                    break;
            }
            
            $url  .= "/$type_code";
            if($method != '')
            {
                $url  .= "/$method";
            }
            $url .="/$v_website_id";
        }
        else
        {
            $url .= "/$v_website_id";
        }
    }
    else
    {
        $url       = SITE_ROOT . "index.php?url=frontend/frontend/dsp_synthesis&type=$type&method=$method&website_id=$v_website_id";
    }
    return $url;
}
/**
 * tao url cho huong dan TTHC
 * @param type $v_url_defaule
 * @param type $v_record_type_id
 * @param type $sel_record_list
 * @param type $v_record_type_code
 * @param type $sel_record_type
 * @param type $sel_record_level
 * @param type $page
 * @return string
 */
function build_url_guidance($v_url_defaule=false,$v_record_type_id =0, $sel_record_list = 0, $v_record_type_code = '', $sel_record_type = '', $sel_record_level= 0,$page = 1)
{
    $url       = FULL_SITE_ROOT . "huong-dan-thu-tuc";
    if($v_url_defaule) // Hien thi url danh sach thu tuc
    {
       if(!check_file_htaccess())
        {
            $url       = SITE_ROOT . "index.php?url=frontend/frontend/dsp_guidance";
        }
        return $url;
    }
    if((int)$v_record_type_id > 0) //Hien thi url xem chi tiet thu tuc
    {
         if(!check_file_htaccess())
         {
                $url       = SITE_ROOT . "index.php?url=frontend/frontend/dsp_single_guidance?record_type_id=".$v_record_type_id;
         }
        return $url. '/chi-tiet/'.$v_record_type_id;
    }
    if (!check_file_htaccess()) // Hien thi url trang tim kiem huong dan thu tuc
    {
            $url       = SITE_ROOT . "index.php?url=frontend/frontend/dsp_guidance";
    }
    $url .="?sel_record_list=$sel_record_list&txt_record_type_code=$v_record_type_code&sel_record_type$sel_record_type=&sel_record_level=$sel_record_level&page=$page";
    return $url;
}


function build_url_lookup($page=1,$unit='')
{
    $url_page = '';
    if (check_file_htaccess())
    {
         $url_page = ($page > 0)?"?page=$page":"";
         $url_unit = ($unit != '')?"&unit=$unit":"";
         $url = SITE_ROOT . "tra_cuu_ho_so" . $url_page . $url_unit;
    }
    else
    {
        $url_page = ($page > 0)?"&page=$page":"";
        $url_unit = ($unit != '')?"&unit=$unit":"";
        $url = SITE_ROOT . "index.php?url=frontend/frontend/dsp_loockup" . $url_page . $url_unit;
    }
    return $url;
}

function build_url_single_staff($v_staff_id = 0)
{
    if (check_file_htaccess())
        {
            $url       = FULL_SITE_ROOT . "danh-gia-can-bo/can-bo/$v_staff_id";
        }
        else
        {
            $url       = SITE_ROOT . "index.php?url=frontend/frontend/dsp_single_staff?staff_id=".$v_staff_id;
        }
        return $url;
}

function build_url_evaluation_update()
{
    if(check_file_htaccess())
    {
        $url = FULL_SITE_ROOT.'danh-gia-can-bo/can-bo/update_vote';
    }
    else
    {
        $url = FULL_SITE_ROOT.'index.php?url=frontend/frontend/dsp_update_vote/';
    }
    return $url;
}   

/**
 * Tao URL Danh' gia can bo
 * @param int $v_village
 * @param bool $v_help 
 * @param bool $v_show_result Neu >0 Xem ket qua chi tiet cua tung can bo truyen id can bo,
 *                                   Neu true hien thi danh sach ket qua tat ca cac can bo
 * @return string url
 */
function build_url_evaluation($v_help = FALSE,$v_show_result = FALSE,$v_page=0)
{ 
    $url = SITE_ROOT . 'danh-gia-can-bo/';
    if( $v_page>0)
    {
        //search
        $v_village_id = isset($_REQUEST['sel_village']) ? $_REQUEST['sel_village'] :'';
        $v_district_id = isset($_REQUEST['sel_district']) ? $_REQUEST['sel_district'] :'';
        $v_txt_member_name = isset($_REQUEST['txt_member_name']) ? $_REQUEST['txt_member_name'] :'';
        $v_page = ($v_page > 0) ? $v_page : 1;
        
        $url .= "?sel_district=$v_district_id&sel_village=$v_village_id&txt_member_name=$v_txt_member_name&page=$v_page";
        return $url;
    }
    if($v_show_result) 
    {
        $v_staff_id = '';
        if(intval($v_show_result) >0 && $v_show_result != 'true')
        {
           $v_staff_id  = intval($v_show_result);
        }
        //Tao url xem ket qua danh gia can bo
        if (check_file_htaccess())
        {
            $url .= "ket-qua-danh-gia/$v_staff_id";
        }
        else
        {
            $url = SITE_ROOT . "index.php?url=frontend/frontend/dsp_evaluation_results/$v_staff_id";
        }
        return $url;
    }
    
    if($v_help) 
    {
        //Tao URL xem huong dan danh gia can bo
        if (check_file_htaccess())
        {
            $url .= "huong-dan-danh-gia";
        }
        else
        {
            $url = SITE_ROOT . "index.php?url=frontend/frontend/dsp_assessment_guidelines/";
        }
        return $url;
    }
    //Tao huong dan url hien thi danh sach cac khu vuc
    if (!check_file_htaccess())
    {
        $url = SITE_ROOT . "index.php?url=frontend/frontend/dsp_all_scope";
    }
    
    return $url;
}


function build_url_survey($v_website_id = 0,$v_survey_id = 0)
{
    if($v_survey_id >0)
    {
        // Gui cau tra loi
         if (check_file_htaccess())
        {
            $url = SITE_ROOT . "cau-hoi-khao-sat/gui-dap-an/$v_survey_id";
        }
        else
        {
            $url = SITE_ROOT . "index.php?url=frontend/frontend/do_update_answer?survey_id=$v_survey_id";
        }
        return $url;
    }
    //Tao url mac dinh xem danh sach cau hoi
     if (check_file_htaccess())
     {
        $url = SITE_ROOT . "cau-hoi-khao-sat/".$v_website_id;
     }
     else
     {
        $url = SITE_ROOT . "index.php?url=frontend/frontend/dsp_survey/";
     }
     return $url;
    
}

//Build link account citizen
function build_url_single_account_citizen($page = 1,$view_history_recrd = false)
{
  
    $url = SITE_ROOT;
    $url_page = '';
    if($page>0) $url_page = "?page=$page";
      
    if($view_history_recrd)
    {
        
        // Hien thi lich su no ho so
       if(check_file_htaccess())
       {
           $url .=  "tai-khoan/lich-su-giao-dich$url_page";
       }
       else
       {
           $url = SITE_ROOT . "index.php?url=frontend/frontend/dsp_history_filing$url_page";
       }
       return $url;
    }
    if(check_file_htaccess())
    {
        $url .=  "tai-khoan/chi-tiet";
    }
    else
    {
        $url = SITE_ROOT . "index.php?url=frontend/frontend/dsp_single_account";
    }
    return $url;
}

function build_url_trigger_change_email($v_username =  '',$v_citizen_id = 0,$v_sen_code_trigger = false,$v_trigger =true)
{
    $v_url = FULL_SITE_ROOT;
    if($v_sen_code_trigger)
    {
        //Gui lại ma xac nhan doi email
        if(check_file_htaccess())
        {
            $v_url .= "tai-khoan/gui-ma-xac-nhan-doi-email";
        }
        else
        {
            $v_url = SITE_ROOT . "index.php?url=frontend/frontend/send_email_activation_code";
        }
        return $v_url;
    }
    
    if(trim($v_username) != '' && (int)($v_citizen_id) > 0)
    {
        if(check_file_htaccess())
        {
            if($v_trigger)
            {
                $v_url .= 'tai-khoan/xac-nhan-doi-email/';
            }   
            else
            {
                $v_url .= 'tai-khoan/huy-xac-nhan-doi-email/';
            }
            $v_url .=  "$v_username-$v_citizen_id";
        }
        else
        {
            if($v_trigger)
            {
                 $url = SITE_ROOT . "index.php?url=frontend/frontend/dsp_active_change_email?username=$v_username&id=$v_citizen_id";
            }   
            else
            {
                 $url = SITE_ROOT . "index.php?url=frontend/frontend/dsp_destroyed_change_email?username=$v_username&id=$v_citizen_id";
            }
        }
        
    }
    return $v_url;
}

//doi password
function build_url_change_password($v_email =  '',$v_code ='')
{
    $url = FULL_SITE_ROOT;
    if(check_file_htaccess())
    {
        $url .=  "tai-khoan/doi-mat-khau/$v_email/$v_code";
    }
    else
    {
        $url.= "index.php?url=frontend/frontend/dsp_change_password/?&email=$v_email&v_code=$v_code";
    }
    return $url;
}
function account_trigger($v_username = '')
{
    $url = FULL_SITE_ROOT;
    if(check_file_htaccess())
    {
        $url .= "tai-khoan/kich-hoat/$v_username";
    }
    else
    {
        $url = FULL_SITE_ROOT . "index.php?url=frontend/frontend/dsp_do_account_trigger?username=$v_username";
    }
    return $url;
}



function parse_boolean($str)
{
    if ($str == '')
    {
        return FALSE;
    }
    switch (strtolower($str))
    {
        case 'true':
        case '1':
        case 'yes':
        case 'y':
            return TRUE;
    }

    return FALSE;
}

define('XPATH_STRING', 10);
define('XPATH_DOM', 20);
define('XPATH_ARRAy', 30);

/**
 * Ánh xạ của SimpleXMLElement::xpath nhưng kiểm tra điều kiện để không gây FALTAL ERROR
 * @param \SimpleXMLElement $dom
 * @param string $xpath
 * @param mixed $return Kiểu dữ liệu trả về
 * @return \SimpleXMLElement
 */
function xpath($dom, $xpath, $return = XPATH_ARRAy)
{
    $dom OR trigger_error('xpath: $dom is not instance of SimpleXMLElement', E_USER_WARNING);
    $r = $dom ? $dom->xpath($xpath) : array();
    switch ($return)
    {
        case XPATH_STRING:
            return isset($r[0]) ? strval($r[0]) : '';
            break;
        case XPATH_DOM:
            return isset($r[0]) ? $r[0] : new SimpleXMLElement('<root/>');
            break;
        case XPATH_ARRAy:
        default:
            return $r ? $r : array();
    }
}

function call_soap_service($client,$function,$arr_param = array(),$recursive = false,$index=0)
{
    $result = $client->__soapCall($function, $arr_param);

    if($result == true)
    {
        return $result;
    }
    else if($index <= 5 && $recursive == true && $result !== true)
    {
        $index++;
        return call_soap_service($client,$function,$arr_param,$recursive,$index);
    }
    else
    {
        return false;
    }

}
/**
 * chuyen so thuong thanh kieu so la ma
 * @param type $integer
 * @return type
 */
function romanic_number($integer) 
{ 
    $table = array('M'=>1000, 'CM'=>900, 'D'=>500, 'CD'=>400, 'C'=>100, 'XC'=>90, 'L'=>50, 'XL'=>40, 'X'=>10, 'IX'=>9, 'V'=>5, 'IV'=>4, 'I'=>1); 
    $return = ''; 
    while($integer > 0) 
    { 
        foreach($table as $rom=>$arb) 
        { 
            if($integer >= $arb) 
            { 
                $integer -= $arb; 
                $return .= $rom; 
                break; 
            } 
        } 
    } 

    return $return; 
} 