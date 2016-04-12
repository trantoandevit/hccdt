<?php

class event_Model extends Model {

    function __construct() {
        parent::__construct();
    }

    public function qry_all_event() 
    {
        @session::init();
        $v_website_id  = session::get('session_website_id');
        $v_txt_search  = get_post_var('txt_search','');
        $v_text_type   = get_post_var('type_event','');
        $sql_search    = '';
        if ($v_txt_search != '')
        {
            $sql_search .=  " and C_NAME like '%$v_txt_search%'";
        }
        elseif($v_text_type !='')
        {
            $sql_search .=  " and C_IS_REPORT = $v_text_type";
        }
        
        page_calc($v_start, $v_end);
        if(DATABASE_TYPE == 'MSSQL')
        {
            $sql= "Select *,ROW_NUMBER() OVER (ORDER BY PK_EVENT) as rn,COUNT(*) OVER (PARTITION BY 1 ) as TOTAL_RECORD,
                (select COUNT(*) from t_ps_event_article where FK_EVENT = PK_EVENT) as C_TOTAL_ARTICLE  From t_ps_event 
                Where FK_WEBSITE = ? $sql_search";
            $stmt = "Select a.* 
                    From ($sql) a Where a.rn>=$v_start And a.rn<=$v_end ORDER BY C_ORDER";
            $DATA_MODEL['arr_all_event'] = $this->db->getAll($stmt,array($v_website_id));
        }
        else if(DATABASE_TYPE == 'MYSQL')
        {
            $v_start = $v_start - 1;
            $v_limit = $v_end - $v_start;
            $stmt = " SELECT 
                            *,
                            (SELECT COUNT(PK_EVENT) FROM t_ps_event WHERE FK_WEBSITE = ? $sql_search) AS TOTAL_RECORD, 
                            (SELECT COUNT(*) FROM t_ps_event_article WHERE FK_EVENT = PK_EVENT) AS C_TOTAL_ARTICLE 
                    FROM t_ps_event 
                    WHERE FK_WEBSITE = ? $sql_search ORDER BY C_ORDER limit ?,?";
            $DATA_MODEL['arr_all_event'] = $this->db->getAll($stmt,array($v_website_id,$v_website_id,$v_start,$v_limit));
        }
        
        
        $DATA_MODEL['arr_search']    = array('txt_search' => $v_txt_search,
                                             'type_event' => $v_text_type);
        return $DATA_MODEL;
    }
    
    public function qry_single_event($v_event_id=0) 
    {
        //var_dump($_POST);exit;
        @session::init();
        $v_website_id   = session::get('session_website_id');
        $v_event_id     = replace_bad_char($v_event_id);
        if($v_event_id>0)
        {
           
            $stmt="select FK_WEBSITE from t_ps_event 
                    where PK_EVENT = ?";
            $v_website_id_of_event_id = $this->db->getOne($stmt,$v_event_id);
            if($v_website_id_of_event_id == $v_website_id)
            {
                if(DATABASE_TYPE == 'MSSQL')
                {   
                    //array single event
                    $stmt="SELECT e.PK_EVENT,
                                        e.C_NAME,
                                        CONVERT(varchar(19),e.C_BEGIN_DATE,103) +char(32)+CONVERT(varchar(10),e.C_BEGIN_DATE,108) as C_BEGIN_DATE,
                                        CONVERT(varchar(19),e.C_END_DATE,103) +char(32)+CONVERT(varchar(10),e.C_END_DATE,108) as C_END_DATE,
                                        e.C_STATUS,
                                        e.C_DEFAULT,
                                        e.FK_WEBSITE,
                                        e.C_FILE_NAME,
                                        e.C_SLUG,
                                        e.C_ORDER,
                                        e.C_IS_REPORT
                        FROM t_ps_event e
                        WHERE PK_EVENT = ?";

                    $DATA_MODEL['arr_single_event'] = $this->db->getRow($stmt,array($v_event_id));
                    
                    //arr all article of event
                    $stmt="select ea.*,a.C_TITLE
                        , DateDiff(mi, A.C_BEGIN_DATE, GetDate()) as CK_BEGIN_DATE
                        , DateDiff(mi, GetDate(), A.C_END_DATE) as CK_END_DATE
                        , A.C_STATUS, C.C_STATUS as C_CAT_STATUS
                        From t_ps_event_article ea 
                        inner join t_ps_article a
                        on ea.FK_ARTICLE = a.PK_ARTICLE
                        inner join t_ps_category C
                        on C.PK_CATEGORY = EA.FK_CATEGORY
                        where FK_EVENT = ?
                        Order by A.C_BEGIN_DATE Desc";
                    $DATA_MODEL['arr_all_article']  = $this->db->getAll($stmt,array($v_event_id));
                }
                else if(DATABASE_TYPE == 'MYSQL')
                {
                    //array single event
                    $stmt="SELECT e.PK_EVENT,
                                        e.C_NAME,
                                        DATE_FORMAT(C_BEGIN_DATE,'%d-%m-%Y %H:%i:%s') as C_BEGIN_DATE,
                                        DATE_FORMAT(C_END_DATE,'%d-%m-%Y %H:%i:%s') as C_END_DATE,
                                        e.C_STATUS,
                                        e.C_DEFAULT,
                                        e.FK_WEBSITE,
                                        e.C_FILE_NAME,
                                        e.C_SLUG,
                                        e.C_ORDER,
                                        e.C_IS_REPORT
                        FROM t_ps_event e
                        WHERE PK_EVENT = ?";

                    $DATA_MODEL['arr_single_event'] = $this->db->getRow($stmt,array($v_event_id));
                    
                    //arr all article of event
                    $stmt="select ea.*,A.C_TITLE
                        , DATEDIFF(NOW(),A.C_BEGIN_DATE) As CK_BEGIN_DATE
                        , DATEDIFF(A.C_END_DATE, NOW()) As CK_END_DATE
                        , A.C_STATUS, C.C_STATUS as C_CAT_STATUS
                        From t_ps_event_article ea 
                        inner join t_ps_article A
                        on ea.FK_ARTICLE = A.PK_ARTICLE
                        inner join t_ps_category C
                        on C.PK_CATEGORY = ea.FK_CATEGORY
                        where FK_EVENT = ?
                        Order by A.C_BEGIN_DATE Desc";
                    $DATA_MODEL['arr_all_article']  = $this->db->getAll($stmt,array($v_event_id));
                }
                

                return $DATA_MODEL;
            }
        }
        return array();;
    }
    public function swap_order($id,$id_swap)
    {
        //var_dump($_POST);
        $v_id           = replace_bad_char($id);
        $v_id_swap      = replace_bad_char($id_swap);
        
        @session::init();
        $v_website_id = session::get('session_website_id');
        $stmt   ="select COUNT(*) from t_ps_event where FK_WEBSITE = ? and (PK_EVENT = ? or PK_EVENT = ?)";
        $count  = $this->db->getOne($stmt,array($v_website_id,$v_id,$v_id_swap));
        if($count>1)
        {
            $stmt = "Select C_ORDER From t_ps_event Where PK_EVENT = ?";
            $temp = $this->db->getOne($stmt,array($v_id));

            $stmt = "Update t_ps_event set C_ORDER=(SELECT TEMP.C_ORDER FROM (SELECT C_ORDER FROM t_ps_event WHERE PK_EVENT=?) TEMP) Where PK_EVENT=?";
            $this->db->Execute($stmt,array($v_id_swap,$v_id));

            $stmt = "Update t_ps_event set C_ORDER = ? where PK_EVENT= ?";
            $this->db->Execute($stmt,array($temp,$v_id_swap));
        }
        $this->exec_done($this->goback_url);
    }
    public function delete_event()
    {
        @session::init();
        $v_website_id   = session::get('session_website_id');
        $arr_event_id   = explode(',',  get_post_var('hdn_item_id_list'));
        foreach ($arr_event_id as $v_event_id)
        {
            $stmt = "select FK_WEBSITE from t_ps_event where PK_EVENT = ?";
            $v_website_id_of_event_id = $this->db->getOne($stmt, $v_event_id);
            if ($v_website_id_of_event_id == $v_website_id) 
            {
                if($this->gp_check_user_permission('XOA_SU_KIEN')>0)
                {
                    $stmt="Delete From t_ps_event_article Where FK_EVENT = ?";
                    $this->db->Execute($stmt,array($v_event_id));

                    $stmt="Delete From t_ps_event Where PK_EVENT= ?";
                    $this->db->Execute($stmt,array($v_event_id));
                }
            }
        }
       $this->exec_done($this->goback_url);
    }
    
    function update_event()
    {
        
        @session::init();
        $v_txt_search  = get_post_var('txt_search','');
        $v_text_type   = get_post_var('type_event','');
        $arr_filter  = array('txt_search'=>$v_txt_search,'type_event'=>$v_text_type);
        
        $v_website_id     = session::get('session_website_id');
        $v_event_id       = get_post_var('hdn_item_id','');
        $v_event_name     = get_post_var('txt_event_name');
        $v_event_slug     = auto_slug(get_post_var('txt_event_slug'));
        $v_event_status   = get_post_var('event_status',0);
        $v_event_defautl  = get_post_var('event_default',0);
        $v_is_report      = get_post_var('is_report',0);
        
        $v_article_list  = get_post_var('hdn_item_id_list');
        $arr_category_article = explode(',', $v_article_list);
        
        
        //kiem tra dieu kien
        if(count($arr_category_article)<1)
        {
            $this->exec_done($this->goback_url,$arr_filter);
        }
        
        $v_file_name       = get_post_var('hdn_file_name'); 
        
        $v_begin_date   = jwDate::ddmmyyyy_to_yyyymmdd(get_post_var('txt_begin_date',''))." ".  get_post_var('txt_begin_time','');
        
        $v_end_date     = jwDate::ddmmyyyy_to_yyyymmdd(get_post_var('txt_end_date',''))." ".  get_post_var('txt_end_time','');
        
        //update su kien
        if($v_event_id != '')
        {
            $stmt="select distinct FK_WEBSITE from t_ps_event Where PK_EVENT =?";
            $v_website_id_of_event = $this->db->getOne($stmt,array($v_event_id));
            if($v_website_id_of_event == $v_website_id)
            {
                if($this->gp_check_user_permission('SUA_SU_KIEN')>0)
                {
                    $stmt= "update t_ps_event set C_NAME = ?, 
						C_BEGIN_DATE=?,
						C_END_DATE=?,
						C_STATUS=?,
						C_DEFAULT=?,
						C_FILE_NAME=?,
						C_SLUG=?,
                                                C_IS_REPORT=? 
                            Where PK_EVENT= ?";
                    $arr_stmt = array($v_event_name,$v_begin_date,
                                      $v_end_date,$v_event_status,
                                      $v_event_defautl,$v_file_name,
                                      $v_event_slug,$v_is_report,$v_event_id);
                    $this->db->Execute($stmt,$arr_stmt);
                    
                    //xoa dữ liệu article
                    $stmt = "Delete from t_ps_event_article Where FK_EVENT=$v_event_id";
                    $this->db->Execute($stmt);
                    foreach($arr_category_article as $row_article)
                    {
                        $arr_cat_at   = explode(' ', $row_article);
                        $v_cat_id     = $arr_cat_at[0];
                        $v_article_id = $arr_cat_at[1];
                        $stmt="insert into t_ps_event_article(FK_EVENT,FK_CATEGORY,FK_ARTICLE)
                               values (?,?,?)";
                        $this->db->Execute($stmt,array($v_event_id,$v_cat_id,$v_article_id));
                    }
                    //build order
                    $v_other_clause = " AND FK_WEBSITE = $v_website_id";
                    $this->build_order('t_ps_event', 'PK_EVENT', 'C_ORDER', $v_other_clause);
                    
                    $this->exec_done($this->goback_url,$arr_filter);
                }
                else
                {
                    echo  "<script>alert('Bạn không có quyền thực hiện thao tác này !!!');</script>";
                    $this->exec_done($this->goback_url,$arr_filter);
                }
            }
        }
        //them moi su kien
        else 
        {
            
            if($this->gp_check_user_permission('THEM_MOI_SU_KIEN')>0)
            {
               //$stmt = "update t_ps_event set C_ORDER = C_ORDER + 1 where FK_WEBSITE = ?";
               //$this->db->Execute($stmt,array($v_website_id));
               
               $stmt= "insert into t_ps_event(C_NAME,
						C_BEGIN_DATE,
						C_END_DATE,
						C_STATUS,
						C_DEFAULT,
						FK_WEBSITE,
						C_FILE_NAME,
						C_SLUG,
						C_ORDER,
                                                C_IS_REPORT)
                        values (?,?,?,?,?,?,?,?,?,?)";
                
               $arr_stmt = array($v_event_name,$v_begin_date,$v_end_date,
                                    $v_event_status,$v_event_defautl,$v_website_id,
                                    $v_file_name,$v_event_slug,1,  $v_is_report);
               $this->db->Execute($stmt,$arr_stmt);
               
               $stmt="Select PK_EVENT From t_ps_event Where FK_WEBSITE = ? Order By PK_EVENT Desc";
               $v_new_id = $this->db->getOne($stmt,array($v_website_id));
               
               foreach($arr_category_article as $row_article)
               {
                    $arr_cat_at   = explode(' ', $row_article);
                    $v_cat_id     = $arr_cat_at[0];
                    $v_article_id = $arr_cat_at[1];
                    $stmt="insert into t_ps_event_article(FK_EVENT,FK_CATEGORY,FK_ARTICLE)
                           values (?,?,?)";
                    $this->db->Execute($stmt,array($v_new_id,$v_cat_id,$v_article_id));
               }
                
                //reorder
                $v_other_clause = " AND FK_WEBSITE = $v_website_id";
                $pk_value = $this->db->getOne("SELECT MAX(PK_EVENT) FROM t_ps_event");
                $this->ReOrder('t_ps_event', 'PK_EVENT', 'C_ORDER', $pk_value, '1', $current_order = -1, $v_other_clause);
                    
               $this->exec_done($this->goback_url,$arr_filter);
            }
            else 
            {
                echo  "<script>alert('Bạn không có quyền thực hiện thao tác này !!!');</script>";
                $this->exec_done($this->goback_url, $arr_filter);
            }
        }
        
        //Hoàn thành và lưu điều kiện lọc
        //$arr_filter = get_filter_condition(array('sel_goto_page', 'sel_rows_per_page'));
        $this->exec_done($this->goback_url,$arr_filter);
    }
}
?>