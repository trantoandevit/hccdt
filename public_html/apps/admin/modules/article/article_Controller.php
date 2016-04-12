<?php

defined('DS') or die('no direct access');

class article_Controller extends Controller
{
    public function __construct()
    {
        parent::__construct('admin', 'article');
        Session::init();
        Session::check_permission('QL_DANH_SACH_TIN_BAI') or $this->access_denied();
        
        define('OPT_TAGS', 'all_tags_of_website_' . Session::get('session_website_id'));

        //dang nhap
        (Session::get('user_id')) or $this->login_admin();        
        $this->model->db->debug = false;

        //lay chuyen muc duoc phan quyen
        Session::set('granted_category', $this->model->qry_all_grant_category());
        
        $v_lang_id                                   = session::get('session_lang_id');
        $this->view->template->arr_all_lang          = $this->model->qry_all_lang();
        $this->view->template->arr_count_article     = $this->model->gp_qry_count_article();
        $this->view->template->arr_all_grant_website = $this->model->gp_qry_all_website_by_user($v_lang_id);
        $this->model->goback_url = $this->view->get_controller_url();
    }

    public function __destruct()
    {
        unset($_SESSION['granted_category']);
    }
    
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //
    //Private method section
    //
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    
    
    
    
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //
    //Public method section
    //
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    public function main()
    {
        $this->dsp_all_article();
    }

    public function dsp_all_article()
    {
        header('Content-type: text/html; charset=utf-8');
        
        $granted_category = Session::get('granted_category');
        $granted_category = implode(',', $granted_category);

        $a_other_clause  = '';
        $c_other_clause  = '';
        $ca_other_clause = '';

        $c_other_clause         = ' And PK_CATEGORY In(' . $granted_category . ')';
        $ca_other_clause        = ' And FK_CATEGORY In(' . $granted_category . ')';
        $other_clause_by_status = $this->get_other_clause_by_status();
        if ($other_clause_by_status)
        {
            $a_other_clause .= " And ($other_clause_by_status)";
        }

        $c_other_clause .= ' And FK_WEBSITE = ' . Session::get('session_website_id');
        $data['arr_all_article'] = $this->model->qry_all_article(
                $a_other_clause
                , $c_other_clause
                , $ca_other_clause
        );

        $data['arr_all_user'] = $this->model->qry_all_user();

        //lay cat name cua searchbox
        $category_id           = intval(get_request_var('hdn_category'));
        $data['category_name'] = $this->model->qry_category_name($category_id);

        $data['count_article'] = $this->model->count_all_article();

        $this->view->render('dsp_all_article', $data);
    }

    public function dsp_single_article($id = 0)
    {
        $id                 = intval($id);
        $data['article_id'] = $id;
        $this->view->render('dsp_single_article', $data);
    }

    public function check_unique_title_service()
    {
        $title       = get_request_var('title');
        $id          = (int) get_request_var('id');
        $msg         = new stdClass();
        $msg->errors = '';
        if ($this->model->title_exists($id, $title) == true)
        {
            $msg->errors = __('this title already exists');
        }
        echo json_encode($msg);
    }

    public function dsp_general_info($id = 0)
    {
        $id = intval($id);
       
        $a_other_clause         = '';
        $c_other_clause         = '';
        $other_clause_by_status = '';
        //neu la phong vien, bien tap vien chi hien category cua minh
        $c_other_clause         = ' And PK_CATEGORY In(' . implode(',', Session::get('granted_category')) . ')';
        $c_other_clause .= ' And FK_WEBSITE= ' . Session::get('session_website_id');

        if ($other_clause_by_status = $this->get_other_clause_by_status())
        {
            $a_other_clause .= " And ($other_clause_by_status)";
        }

        $data['arr_single_article'] = $this->model->qry_single_article($id, $a_other_clause, $c_other_clause);
        if (empty($data['arr_single_article']) && $id != 0)
        {
            die(__('this object is nolonger available!'));
        }

        $data['arr_all_category']     = $this->model->qry_all_category($c_other_clause);
        $data['arr_category_article'] = $this->model->qry_category_article($id);
        $data['arr_my_pen_name']      = $this->model->qry_my_pen_name();
        $data['arr_all_attachment']   = $this->model->qry_all_attachment($id);
        
        $this->view->render('dsp_general_info', $data);
    }

    public function update_general_info()
    {
        $a_other_clause = '';
        $c_other_clause = '';

        //neu la phong vien, bien tap vien chi hien category cua minh
        $c_other_clause = ' And PK_CATEGORY In(' . implode(',', Session::get('granted_category')) . ')';
        $c_other_clause .= ' And FK_WEBSITE= ' . Session::get('session_website_id');

        if ($other_clause_by_status = $this->get_other_clause_by_status())
        {
            $a_other_clause .= " And ($other_clause_by_status)";
        }
        $v_id               = intval(get_request_var('hdn_item_id', 0));
        $arr_single_article = $this->model->qry_single_article(
                intval(get_request_var('hdn_item_id', 0))
                , $a_other_clause
                , $c_other_clause
        );
        if (count($arr_single_article) == 0 && $v_id > 0)
        {
            echo json_encode(array(
                'msg'    => __('invalid request data'),
                'status' => 'error'
            ));
            return;
        }

        $return_msg = $this->model->update_general_info();
        echo json_encode($return_msg);
    }

    public function dsp_preview($id = 0)
    {
        $a_other_clause         = '';
        $c_other_clause         = '';
        $c_other_clause .= ' And PK_CATEGORY In(' . implode(',', Session::get('granted_category')) . ')';
        if ($other_clause_by_status = $this->get_other_clause_by_status())
        {
            $a_other_clause .= " And ($other_clause_by_status)";
        }
        $c_other_clause .= " And FK_WEBSITE = " . Session::get('session_website_id');
        $data['arr_single_article'] = $this->model->qry_single_article($id, $a_other_clause, $c_other_clause);
        if (empty($data['arr_single_article']))
        {
            die(__('this object is nolonger available!'));
        }
        $this->view->render('dsp_preview', $data);
    }

    public function dsp_edit_article($id = 0)
    {
        $a_other_clause = '';
        $c_other_clause = '';

        //neu la phong vien, bien tap vien chi hien category cua minh
        $c_other_clause = ' And PK_CATEGORY In(' . implode(',', Session::get('granted_category')) . ')';
        $c_other_clause .= ' And FK_WEBSITE= ' . Session::get('session_website_id');

        if ($other_clause_by_status = $this->get_other_clause_by_status())
        {
            $a_other_clause .= " And ($other_clause_by_status)";
        }
        $data['arr_single_article'] = $this->model->qry_single_article(intval($id), $a_other_clause, $c_other_clause);
        if (empty($data['arr_single_article']))
        {
            die(__('this object is nolonger available!'));
        }

        $data['arr_category_article'] = $this->model->qry_category_article($id);
        $data['arr_all_category']     = $this->model->qry_all_category($c_other_clause);
        $data['arr_sticky_category']  = $this->model->qry_sticky_category($id);

        $data['v_id'] = $id;
        $this->view->render('dsp_edit_article', $data);
    }

    public function update_edited_article($no_website = '0')
    {
        $id             = intval(get_post_var('hdn_item_id', 0));
        $a_other_clause = '';
        $c_other_clause = '';

        //neu la phong vien, bien tap vien chi hien category cua minh
        $c_other_clause = ' And PK_CATEGORY In(' . implode(',', Session::get('granted_category')) . ')';
        if($no_website == '0')
        {
            $c_other_clause .= ' And FK_WEBSITE= ' . Session::get('session_website_id');
        }

        if ($other_clause_by_status = $this->get_other_clause_by_status())
        {
            $a_other_clause .= " And ($other_clause_by_status)";
        }
        $data['arr_single_article'] = $this->model->qry_single_article($id, $a_other_clause, $c_other_clause);
        if (empty($data['arr_single_article']))
        {
            die(json_encode(
                            array(
                                'msg'    => __('update fail'),
                                'status' => 'error',
                                'id'     => intval(get_post_var('hdn_item_id', 0))
                            )
                    )
            );
        }

        $a = $this->model->update_edited_article();
        echo json_encode($a);
    }

    public function dsp_all_version($id = 0)
    {
        $id = intval($id);

        $a_other_clause = '';
        $c_other_clause = '';

        //neu la phong vien, bien tap vien chi hien category cua minh
        $c_other_clause = ' And PK_CATEGORY In(' . implode(',', Session::get('granted_category')) . ')';
        $c_other_clause .= ' And FK_WEBSITE= ' . Session::get('session_website_id');

        if ($other_clause_by_status = $this->get_other_clause_by_status())
        {
            $a_other_clause .= " And ($other_clause_by_status)";
        }

        $arr_single_article = $this->model->qry_single_article($id, $a_other_clause, $c_other_clause);
        if (!empty($arr_single_article))
        {
            
            $data['arr_all_version'] = array();
            $data['v_id'] = $id;
            
            $xml          = $arr_single_article['C_XML_VERSION'] ? $arr_single_article['C_XML_VERSION'] : '<root/>';
            $xml          = new SimpleXMLElement($xml);
            $xml          = $xml->xpath('//version');
            $i            = 0;
            
            foreach ($xml as $item)
            {
                $i++;
                $data['arr_all_version'][] = array(
                    'id'          => $i,
                    'date'        => strval($item->date),
                    'action'      => strval($item->action),
                    'status'      => strval($item->status),
                    'has_content' => strlen(strval($item->content)),
                    'user_name'   => $item->user_name != NULL ? $item->user_name : '?'
                );
            }

            $this->view->render('dsp_all_version', $data);
        }
        else
        {
            die(__('this object is nolonger available!'));
        }
    }

    public function dsp_single_version()
    {
        $article_id         = $data['article_id'] = intval(get_request_var('article'));
        $version_id         = $data['version_id'] = intval(get_request_var('version')) - 1;

        $a_other_clause = '';
        $c_other_clause = '';

        //neu la phong vien, bien tap vien chi hien category cua minh
        $c_other_clause = ' And PK_CATEGORY In(' . implode(',', Session::get('granted_category')) . ')';
        $c_other_clause .= ' And FK_WEBSITE= ' . Session::get('session_website_id');

        if ($other_clause_by_status = $this->get_other_clause_by_status())
        {
            $a_other_clause .= " And ($other_clause_by_status)";
        }

        $arr_single_article = $this->model->qry_single_article($article_id, $a_other_clause, $c_other_clause);
        if (!empty($arr_single_article))
        {
            $xml = $arr_single_article['C_XML_VERSION'];
            $xml = new SimpleXMLElement($xml, LIBXML_NOCDATA);
            $xml = $xml->xpath("//version");

            if (empty($xml[$version_id]))
            {
                die(__('this object is nolonger available!'));
            }
            $xml                        = $xml[$version_id];
            $xml->summary               = $this->model->prepare_tinyMCE(html_entity_decode($xml->summary));
            $xml->content               = $this->model->prepare_tinyMCE(html_entity_decode($xml->content));
            $xml->title                 = $arr_single_article['C_TITLE'];
            $data['dom_single_version'] = $xml;
        }
        else
        {
            die(__('this object is nolonger available!'));
        }

        $this->view->render('dsp_single_version', $data);
    }

    public function restore_version()
    {
        $article_id = intval(get_post_var('article_id'));
        $version_id = intval(get_post_var('version_id'));
        
        
        $a_other_clause = '';
        $c_other_clause = '';

        //neu la phong vien, bien tap vien chi hien category cua minh
        $c_other_clause = ' And PK_CATEGORY In(' . implode(',', Session::get('granted_category')) . ')';
        $c_other_clause .= ' And FK_WEBSITE= ' . Session::get('session_website_id');

        if ($other_clause_by_status = $this->get_other_clause_by_status())
        {
            $a_other_clause .= " And ($other_clause_by_status)";
        }

        $arr_single_article = $this->model->qry_single_article($article_id, $a_other_clause, $c_other_clause);
        
        if (count($arr_single_article)>0)
        {
            $this->model->restore_version($article_id, $version_id);
        }
        else
        {
            die(__('invalid request data'));
        }
    }

    function dsp_all_article_svc()
    {
        $v_website      = intval(get_request_var('sel_website', Session::get('session_website_id')));
        $a_other_clause = ' And C_STATUS >= 3';
        if (intval(get_request_var('hdn_category')) == 0 && get_request_var('txt_title') == '')
        {
            $a_other_clause .= ' And 1=2';
        }

        $c_other_clause           = ' And FK_WEBSITE = ' . $v_website;
        $c_other_clause .= ' And PK_CATEGORY In(' . implode(',', Session::get('granted_category')) . ')';
        $ca_other_clause          = ' And FK_CATEGORY In(' . implode(',', Session::get('granted_category')) . ')';
        $data['v_website']        = $v_website;
        $data['arr_all_website']  = $this->model->qry_all_website();
        $data['arr_all_category'] = $this->model->qry_all_category($c_other_clause);
        $data['arr_all_article']  = $this->model->qry_all_article($a_other_clause, $c_other_clause, $ca_other_clause);
        $this->view->render('dsp_all_article_svc', $data);
    }

    function delete_article()
    {
        $a_other_clause         = '';
        $c_other_clause         = '';
        $v_website              = Session::get('session_website_id');
        $granted_category       = implode(',', Session::get('granted_category'));
        if ($other_clause_by_status = $this->get_other_clause_by_status())
        {
            $a_other_clause = " And ($other_clause_by_status)";
        }
        $c_other_clause .= " And FK_WEBSITE = $v_website";
        $c_other_clause .= " And PK_CATEGORY In($granted_category)";

        $this->model->delete_article($a_other_clause, $c_other_clause);
    }

    private function get_other_clause_by_status()
    {
        $arr_granted_group      = Session::get('arr_all_grant_group_code');
        $other_clause_by_status = array();
        $user_id = Session::get('user_id');
        

        if (in_array('TONG_BIEN_TAP', $arr_granted_group))
        {
            $other_clause_by_status[] = "C_STATUS >= 2";
        }
        if (in_array('ADMINISTRATORS', $arr_granted_group))
        {
            $other_clause_by_status[] = "1 = 1";
        }
        if (in_array('BIEN_TAP_VIEN', $arr_granted_group))
        {
            $other_clause_by_status[] = "C_STATUS = 1";
        }
        $other_clause_by_status[] = "(C_STATUS = 0 And FK_INIT_USER = $user_id)";
        if (count($other_clause_by_status))
        {
            $other_clause_by_status = implode(' Or ', $other_clause_by_status);
            return $other_clause_by_status;
        }
        return '';
    }

   

    public function fix_db_content()
    {
        $this->model->fix_db_content();
    }

    //cache 
    public function create_cache()
    {
        //kiem tra cachemode
        if (get_system_config_value(CFGKEY_CACHE) == 'false')
        {
            $this->model->exec_done($this->model->goback_url);
        }
        $website_id = session::get('session_website_id');
        
        $cache = New GP_Cache();
        $cache->create_all_article_type_cache($website_id);
        
        $this->model->exec_done($this->model->goback_url, $_POST);
    }

    public function dsp_all_tags()
    {
        $data['arr_all_tags'] = unserialize($this->model->qry_all_tags());
        $this->view->render('dsp_all_tags', $data);
    }
    
   
    
    //dsp kiem duyet tin bai
    public function dsp_approve_article()
    {
        $this->view->render('dsp_approve_article');
    }
    
    //ajax lay du lieu tin bai de kiem duyet (mobile)
    public function arp_approve_article($cond_status=2)
    {
        $cond_status = replace_bad_char($cond_status);
        //quyen 
        $granted_category = Session::get('granted_category');
        $granted_category = implode(',', $granted_category);

        $a_other_clause  = '';
        $c_other_clause  = '';
        $ca_other_clause = '';

        $c_other_clause         = ' And PK_CATEGORY In(' . $granted_category . ')';
        $ca_other_clause        = ' And FK_CATEGORY In(' . $granted_category . ')';
        $other_clause_by_status = $this->get_other_clause_by_status();
        
        if ($other_clause_by_status)
        {
            $a_other_clause .= " And ($other_clause_by_status) And C_STATUS = '$cond_status'";
        }

        $data['arr_all_article'] = $this->model->qry_all_approve_article(
                $a_other_clause
                , $c_other_clause
                , $ca_other_clause
        );

        $this->view->render('dsp_arp_approve_article',$data);
    }
    
    //hien thi chi tiet duyet tin bai
    public function dsp_single_approve_article()
    {
        $v_article_id = get_post_var('hdn_item_id','0');
        
        $a_other_clause         = '';
        $c_other_clause         = '';
        $c_other_clause .= ' And PK_CATEGORY In(' . implode(',', Session::get('granted_category')) . ')';
        if ($other_clause_by_status = $this->get_other_clause_by_status())
        {
            $a_other_clause .= " And ($other_clause_by_status)";
        }
        
        //lay du lieu single
        if($v_article_id != '0')
        {
            $data['arr_single_article'] = $this->model->qry_single_approve_article($v_article_id, $a_other_clause, $c_other_clause);
        }
        else
        {
            $data['arr_single_article'] = array();
        }
        
        if (empty($data['arr_single_article']))
        {
            die(__('this object is nolonger available!'));
        }
        
        //bien tap
        $data['arr_category_article'] = $this->model->qry_category_article($v_article_id,'1');
        $data['arr_all_category']     = $this->model->qry_all_category($c_other_clause);
        $data['arr_sticky_category']  = $this->model->qry_sticky_category($v_article_id);

        $data['v_id'] = $v_article_id;
        $this->view->render('dsp_single_approve_article',$data);
    }
    
    
}

?>
