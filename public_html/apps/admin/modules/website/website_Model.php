<?php if (!defined('SERVER_ROOT')) exit('No direct script access allowed');

class website_Model extends Model {

    function __construct()
    {
        parent::__construct();
    }

    public function qry_all_website()
    {
        //Phan trang
        page_calc($v_start, $v_end);
        $arr_check=array('t_cores_user_function' =>'FK_WEBSITE',
                         't_cores_group_function' => 'FK_WEBSITE',
                         't_ps_category' => 'FK_WEBSITE',
                         't_ps_advertising_position' => 'FK_WEBSITE',
                         't_ps_banner' => 'FK_WEBSITE',
                         't_ps_event' => 'FK_WEBSITE',
                         't_ps_homepage_category' => 'FK_WEBSITE',
                         't_ps_menu_position' => 'FK_WEBSITE',
                         't_ps_poll' => 'FK_WEBSITE',
                         't_ps_spotlight_position' => 'FK_WEBSITE',
                         't_ps_sticky' => 'FK_WEBSITE',
                        );  
        if (DATABASE_TYPE =='MSSQL')
        {
            $sql_check_depend = $this->gp_build_check_depend_qry($arr_check,'PK_WEBSITE');
        
            $sql = "Select
                        PK_WEBSITE
                        , C_NAME
                        , C_ORDER
                        , C_STATUS
                        , ROW_NUMBER() OVER (ORDER BY C_ORDER) as RN
                     From t_ps_website W";

            $stmt = "Select
                        a.*
                        , (Select Count(*) From t_ps_website) as TOTAL_RECORD
                        ,$sql_check_depend
                     From ($sql) a
                     Where a.rn>=? And a.rn<=? Order By a.rn";
            $params = array($v_start, $v_end);
        }
        elseif (DATABASE_TYPE =='MYSQL')
        {
            $sql_check_depend = $this->gp_build_check_depend_qry($arr_check,'PK_WEBSITE');
            
            $v_start = $v_start - 1;
            $v_limit = $v_end - $v_start;
            
            $stmt = "SELECT PK_WEBSITE
                            , C_NAME
                            , C_ORDER
                            , C_STATUS
                            , (Select Count(*) From t_ps_website) as TOTAL_RECORD
                            , $sql_check_depend 
                    FROM t_ps_website Order by C_ORDER
                     LIMIT ?,?";
            $params = array($v_start, $v_limit);
        }
        
        
        return $this->db->getAll($stmt, $params);
    }

    public function qry_single_website($website_id)
    {
        if ($website_id > 0)
        {
            $stmt = 'Select web.*,us.C_NAME as C_NAME_USER  From t_ps_website web inner join t_cores_user us
                        on web.FK_USER = us.PK_USER
                         Where PK_WEBSITE= ? ';
            $params = array($website_id);

           $DATA_MODEL['arr_single_website'] = $this->db->getRow($stmt, $params);
           
           $stmt = "Select * From t_cores_list Where FK_LISTTYPE = (Select PK_LISTTYPE From t_cores_listtype Where C_CODE='DM_NGON_NGU')";
           $DATA_MODEL['arr_all_lang'] = $this->db->getAll($stmt);
           return $DATA_MODEL;
        }
        $DATA_MODEL['arr_single_website'] = array('C_ORDER' => $this->get_max('t_ps_website', 'C_ORDER'));
        $stmt = "Select * From t_cores_list Where FK_LISTTYPE = (Select PK_LISTTYPE From t_cores_listtype Where C_CODE='DM_NGON_NGU')";
        $DATA_MODEL['arr_all_lang'] = $this->db->getAll($stmt);
        return $DATA_MODEL;
    }

    public function update_website()
    {
        $v_website_id = get_post_var('hdn_item_id',0);

        is_id_number($v_website_id) OR $v_website_id = 0;

        $v_code                 = strtoupper(get_post_var('txt_code',''));
        $v_name                 = get_post_var('txt_name','');
        $v_order                = get_post_var('txt_order','1');
        $v_monitor_user_id      = get_post_var('hdn_monitor_user_id','0');
        $v_theme_code           = get_post_var('hdn_theme_code','');
        $v_status               = get_post_var('chk_status','0');
        $v_lang                 = get_post_var('website_lang');    
        
        $stmt = 'Select Count(*) From t_ps_website Where PK_WEBSITE <> ? And (C_CODE=? Or C_NAME=?)';
        $v_count = $this->db->getOne($stmt, array($v_website_id,$v_code,$v_name));
        if ($v_count < 1)
        {
            if ($v_website_id < 1)
            {
                if($this->gp_check_user_permission('THEM_MOI_CHUYEN_TRANG',FALSE)>0)
                {
                    $v_current_order = 0;
                    $stmt = 'Insert Into t_ps_website(C_CODE, C_NAME, C_ORDER, C_STATUS, FK_USER, C_THEME_CODE,FK_LANG) Values(?,?,?,?,?,?,?)';
                    $params = array($v_code, $v_name, $v_order,$v_status, $v_monitor_user_id, $v_theme_code,$v_lang);
                    $this->db->Execute($stmt, $params);

                    $v_website_id = $this->db->getOne("Select IDENT_CURRENT('t_ps_website')");
                }
                else 
                {
                    echo "<script>alert('Bạn không có quyền thực hiện thao tác này !!!');</script>";
                    $arr_filter = get_filter_condition(array('sel_goto_page', 'sel_rows_per_page'));
                    $this->exec_done($this->goback_url, $arr_filter);
                }
            }
            else
            {
                if($this->gp_check_user_permission('SUA_CHUYEN_TRANG',FALSE)>0)
                {
                    $stmt ='select C_ORDER from t_ps_website where PK_WEBSITE = ?';
                    $v_current_order = $this->db->getOne($stmt,array($v_website_id));
                    $stmt = 'Update t_ps_website Set
                                    C_CODE=?
                                    , C_NAME=?
                                    , C_ORDER=?
                                    , C_STATUS=?
                                    , FK_USER=?
                                    ,C_THEME_CODE=?
                                    ,FK_LANG=? 
                            Where PK_WEBSITE=?';
                    $params = array($v_code, $v_name, $v_order,$v_status, $v_monitor_user_id, $v_theme_code,$v_lang,$v_website_id );
                    $this->db->Execute($stmt, $params);
                }
                else
                {
                   echo  "<script>alert('Bạn không có quyền thực hiện thao tác này !!!');</script>";
                   $arr_filter = get_filter_condition(array('sel_goto_page', 'sel_rows_per_page'));
                   $this->exec_done($this->goback_url, $arr_filter);
                }    
            }

            //Reorder
            $this->ReOrder('t_ps_website', 'PK_WEBSITE', 'C_ORDER', $v_website_id, $v_order,$v_current_order);
        }

        //Luu dieu kien loc
        $arr_filter = get_filter_condition(array('sel_goto_page', 'sel_rows_per_page'));
        $this->exec_done($this->goback_url, $arr_filter);
    }

    public function delete_website()
    {
        $v_website_id_list = get_post_var('hdn_item_id_list','');
        $arr_check=array('t_cores_user_function' =>'FK_WEBSITE',
                         't_cores_group_function' => 'FK_WEBSITE',
                         't_ps_category' => 'FK_WEBSITE',
                         't_ps_advertising_position' => 'FK_WEBSITE',
                         't_ps_banner' => 'FK_WEBSITE',
                         't_ps_event' => 'FK_WEBSITE',
                         't_ps_homepage_category' => 'FK_WEBSITE',
                         't_ps_menu_position' => 'FK_WEBSITE',
                         't_ps_photo_gallery' => 'FK_WEBSITE',
                         't_ps_poll' => 'FK_WEBSITE',
                         't_ps_spotlight_position' => 'FK_WEBSITE',
                         't_ps_sticky' => 'FK_WEBSITE',
                        );         
        $sql_check_depend = $this->gp_build_check_depend_qry($arr_check,'PK_WEBSITE');
        $stmt="Select PK_WEBSITE,$sql_check_depend from t_ps_website where PK_WEBSITE in ($v_website_id_list)";
        $arr_check = $this->db->getAssoc($stmt);
        
        if ($v_website_id_list != '')
        {
            //echo $this->gp_check_user_permission('XOA_CHUYEN_TRANG');exit;
          if($this->gp_check_user_permission('XOA_CHUYEN_TRANG',FALSE)>0)
          {
            $arr_to_delete_website_id = explode(',',$v_website_id_list);
            foreach ($arr_to_delete_website_id as $v_website_id)
            {
                $v_count_category = $arr_check[$v_website_id];
                if ($v_count_category < 1)
                {
                    $stmt = 'Delete From t_ps_website Where PK_WEBSITE=?';
                    $this->db->Execute($stmt, array($v_website_id));
                }
            }
          }
          else 
          {
             echo '<script>alert("Bạn không có quyền thực hiện thao tác này !!!");</script>';;
          }
        }
        //exit;
        //Luu dieu kien loc
        $arr_filter = get_filter_condition(array('sel_goto_page', 'sel_rows_per_page'));
        $this->exec_done($this->goback_url, $arr_filter);
    }
    public function qry_all_theme()
    {
        $stmt = "select * from t_cores_list 
            where FK_LISTTYPE = (select PK_LISTTYPE from t_cores_listtype where C_CODE = 'DM_THEME')";
        return $this->db->getAll($stmt);
    }
    public function check_code()
    {
        $v_website_id = get_post_var('website_id',0);
        $v_website_code = get_post_var('website_code','');
        $this->db->debug = 0;
        $stmt = 'Select Count(*) From t_ps_website Where C_CODE=? And PK_WEBSITE <> ?';
        $v_count = $this->db->getOne($stmt, array($v_website_code, $v_website_id));
        
        if($v_count > 0)
        {
            echo 1;
        }
        else 
        {
            echo 0;
        }
    }
    public function check_name()
    {
        $v_website_id = get_post_var('website_id',0);
        $v_website_name = get_post_var('website_name','');
        $this->db->debug = 0;
        $stmt = 'Select Count(*) From t_ps_website Where C_NAME=? And PK_WEBSITE <> ?';
        $v_count = $this->db->getOne($stmt, array($v_website_name, $v_website_id));
        if($v_count > 0)
        {
            echo 1;
        }
        else 
        {
            echo 0;
        }
    }

}