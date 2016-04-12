<?php
defined('DS') or die('no direct access');

class GP_Cache
{
    public $model;
    function __construct()
    {
        $this->model = new GP_Cache_Model();
    }
    
    /**
     * Cache moi thu lien quan den tin bai
     * @param Int $website_id ID website
     */
    public function create_all_article_type_cache ($website_id)
    {
        $v_success = TRUE;
        //0. Tin noi bat cua chuyen trang
        $v_success = $v_success && $this->create_home_page_article_cache($website_id);
        
        //1. Cache chuyen muc + tin bai trang chu
        $v_success = $v_success && $this->create_featured_category_cache($website_id);
        
        //2. Cache tin bai moi nhat
        $v_success = $v_success && $this->create_latest_article_cache($website_id);
        
        //3. Cache Tin doc nhieu nhat
        $v_success = $v_success && $this->create_most_view_article_cache($website_id) ;
        
        //4. Cache Tin dang chu y trong ngay (Breaking news)
        $v_success = $v_success && $this->create_breaking_news_cache($website_id) ;
        
        return $v_success;
    }
    
    /**
     * Tao cache chuyen muc noi bat tren trang chu (feature_category)
     * @param int $website_id ID chuyen trang
     * @return Boolean
     *      o   TRUE neu tao cache thanh cong
     *      o   FALSE neu tao khong thanh cong
     */
    public function create_featured_category_cache($website_id)
    {
        $arr_data   = $this->model->gp_qry_all_featured_category($website_id);
        $file_name  = _CONST_SERVER_CACHE_ROOT . $website_id . DS . 'featured_cat.html';
        return write_cache_data($arr_data, $file_name);
    }
    
    /**
     * Tao cache tin moi nhat (latest article)
     * @param int $website_id ID chuyen trang
     * @return Boolean
     *      o   TRUE neu tao cache thanh cong
     *      o   FALSE neu tao khong thanh cong
     */
    public function create_latest_article_cache($website_id)
    {
        $arr_data   = $this->model->gp_qry_all_latest_article($website_id);
        $file_name  = _CONST_SERVER_CACHE_ROOT . $website_id . DS . 'latest_article.html';
        return write_cache_data($arr_data, $file_name);
    }
    
    /**
     * Tao cache menu ()
     * @param int $website_id ID chuyen trang
     * @return Boolean
     *      o   TRUE neu tao cache thanh cong
     *      o   FALSE neu tao khong thanh cong
     */
    public function create_menu_cache($website_id)
    {
        $arr_data   = $this->model->gp_qry_all_menu_position($website_id);
        $file_name  = _CONST_SERVER_CACHE_ROOT . $website_id . DS . 'menu.html';
        return write_cache_data($arr_data, $file_name);
    }
    
    /**
     * Tao cache tin bai noi bat trang chu sticky
     * @param int $website_id ID chuyen trang
     * @return Boolean
     *      o   TRUE neu tao cache thanh cong
     *      o   FALSE neu tao khong thanh cong
     */
    public function create_home_page_article_cache($website_id)
    {
        $arr_data   = $this->model->gp_qry_all_sticky($website_id);
        $file_name  = _CONST_SERVER_CACHE_ROOT . $website_id . DS . 'sticky.html';
        
        return write_cache_data($arr_data, $file_name);
    }
    
    /**
     * Tao cache tin tieu diem
     * @param int $website_id ID chuyen trang
     * @param int $position_id ID bo tin  (vi tri)
     * 
     * @return Boolean
     *      o   TRUE neu tao cache thanh cong
     *      o   FALSE neu tao khong thanh cong
     */
    public function create_spotlight_cache($website_id,$position_id)
    {
        $arr_data      = $this->model->gp_qry_all_spotlight($website_id, $position_id);
        $file_name = _CONST_SERVER_CACHE_ROOT . $website_id . DS . 'spotlight' . DS . $position_id . '.html';
        
        return write_cache_data($arr_data, $file_name);
    }
    
    /**
     * Tao cache Su kien
     * @param int $website_id ID chuyen trang
     * @return boolean:
     *      o TRUE neu tao file cache thành công
     *      o FALSE nếu không thành công
     */
    public function create_event_cache($website_id)
    {
        $arr_data  = $this->model->gp_qry_all_event($website_id);
        $file_name = _CONST_SERVER_CACHE_ROOT . $website_id . DS . 'event.html';
        
        return write_cache_data($arr_data, $file_name);
    }
    
    /**
     * Tao cache phong su anh
     * @param int $website_id ID chuyen trang
     * @return boolean:
     *      o TRUE neu tao file cache thành công
     *      o FALSE nếu không thành công
     */
    public function create_report_cache($website_id)
    {
        $arr_data  = $this->model->gp_qry_all_report($website_id);
        $file_name = _CONST_SERVER_CACHE_ROOT . $website_id . DS . 'report.html';
        
        return write_cache_data($arr_data, $file_name);
    }
    
    
    /**
     * Tao cache banner
     * @param int $website_id ID chuyen trang
     * @return boolean:
     *      o TRUE neu tao file cache thành công
     *      o FALSE nếu không thành công
     */
    public function create_banner_cache($website_id)
    {
        $arr_data  = $this->model->gp_qry_all_banner($website_id);
        $v_cache_file_name = _CONST_SERVER_CACHE_ROOT . $website_id . DS . 'banner.html';
        write_cache_data($arr_data, $v_cache_file_name);
        
        $v_default_done = FALSE;
        for ($i=0, $n=sizeof($arr_data); $i<$n; $i++)
        {
            $v_default              = $arr_data[$i]['C_DEFAULT'];
            $v_image_file_name      = $arr_data[$i]['C_FILE_NAME'];
            $v_category_id          = $arr_data[$i]['FK_CATEGORY'];
            
            if ($v_default == 1)
            {
                if ($v_default_done == TRUE)
                {
                    continue;
                }
                
                $v_cache_file_name = _CONST_SERVER_CACHE_ROOT . $website_id . DS . 'banner' . DS . 'default.html';
                write_cache_data($v_image_file_name, $v_cache_file_name);
                $v_default_done = TRUE;
            }
            else
            {
                $v_cache_file_name = _CONST_SERVER_CACHE_ROOT . $website_id . DS . 'banner' . DS . $v_category_id . '.html';
                write_cache_data($v_image_file_name, $v_cache_file_name);
            }
        }
        return TRUE;
    } //end create_banner_cache
    
    /**
     * Tao cache tin bai xem nhieu nhat
     * @param Int $website_id ID chuyen trang
     */
    public function create_most_view_article_cache($website_id)
    {
        $arr_data  = $this->model->gp_qry_all_most_view_article($website_id);
        $v_cache_file_name = _CONST_SERVER_CACHE_ROOT . $website_id . DS . 'most_view.html';
        write_cache_data($arr_data, $v_cache_file_name);
    }
    
    /**
     * Tao cache Tin dang chu y trong ngay (Breaking news)
     * @param unknown $website_id
     */
    public function create_breaking_news_cache($website_id)
    {
        $arr_data  = $this->model->gp_qry_all_breaking_news($website_id);
        $v_cache_file_name = _CONST_SERVER_CACHE_ROOT . $website_id . DS . 'breaking_news.html';
        write_cache_data($arr_data, $v_cache_file_name);
    }
    
    /**
     * Cache danh sach phong su anh
     * @param Int $website_id ID chuyen trang
     */
    public function create_new_photo_gallery_cache($website_id)
    {
        $arr_data = $this->model->gp_qry_all_new_photo_gallery($website_id);
        $v_cache_file_name = _CONST_SERVER_CACHE_ROOT . $website_id . DS . 'new_photo_gallery.html';
        write_cache_data($arr_data, $v_cache_file_name);
    }
}//end class cache_Controller


class GP_Cache_Model extends Model
{

    function __construct()
    {
        parent::__construct();
    }
    
}//end class cache_Controller