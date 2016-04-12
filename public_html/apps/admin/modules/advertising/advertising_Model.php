<?php

class advertising_Model extends Model {

    function __construct() {
        parent::__construct();
    }

    public function qry_all_advertising() 
    {
        @session::init();
        $v_website_id=session::get('session_website_id');
        $stmt= "Select * From t_ps_advertising_position Where FK_WEBSITE = ?";
        return $this->db->getAll($stmt,array($v_website_id));
    }
    public function qry_single_position($v_position_id)
    {
        @session::init();
        $v_website_id=session::get('session_website_id');
        $v_position_id = replace_bad_char($v_position_id);
        if($v_position_id == 0)
        {
            $stmt = "Select top 1 PK_ADV_POSITION From t_ps_advertising_position Where FK_WEBSITE = ?";
            $v_position_id = $this->db->getOne($stmt,array($v_website_id));
            if(DATABASE_TYPE == 'MSSQL')
            {
                $stmt = "select  PK_ADVERTISING,
                                C_NAME,
                                C_ORDER,
                                C_URL,
                                CONVERT(varchar(10),C_BEGIN_DATE,103) as C_BEGIN_DATE,
                                CONVERT(varchar(10),C_END_DATE ,103) as C_END_DATE 
                    from t_ps_advertising where FK_ADV_POSITION = ? ORDER BY C_ORDER";
            }
            else if(DATABASE_TYPE == 'MYSQL')
            {
                $stmt = "select  PK_ADVERTISING,
                                C_NAME,
                                C_ORDER,
                                C_URL,
                                DATE_FORMAT(C_BEGIN_DATE,'%d-%m-%Y') as C_BEGIN_DATE,
                                DATE_FORMAT(C_END_DATE,'%d-%m-%Y')   as C_END_DATE  
                    from t_ps_advertising where FK_ADV_POSITION = ? ORDER BY C_ORDER";
            }
            
            return $this->db->getAll($stmt,array($v_position_id));
        }
        else 
        {
            $stmt="select COUNT(*) From t_ps_advertising_position Where PK_ADV_POSITION=? and FK_WEBSITE = ?";
            $count = $this->db->getOne($stmt,array($v_position_id,$v_website_id));
            if($count > 0)
            {
                
                if(DATABASE_TYPE == 'MSSQL')
                {
                    $stmt = "select  PK_ADVERTISING,
                                    C_NAME,
                                    C_ORDER,
                                    C_URL,
                                    CONVERT(varchar(10),C_BEGIN_DATE,103) as C_BEGIN_DATE,
                                    CONVERT(varchar(10),C_END_DATE ,103) as C_END_DATE 
                        from t_ps_advertising where FK_ADV_POSITION = ? ORDER BY C_ORDER";
                }
                else if(DATABASE_TYPE == 'MYSQL')
                {
                    $stmt = "select  PK_ADVERTISING,
                                    C_NAME,
                                    C_ORDER,
                                    C_URL,
                                    DATE_FORMAT(C_BEGIN_DATE,'%d-%m-%Y') as C_BEGIN_DATE,
                                    DATE_FORMAT(C_END_DATE,'%d-%m-%Y')   as C_END_DATE  
                        from t_ps_advertising where FK_ADV_POSITION = ? ORDER BY C_ORDER";
                }
                return $this->db->getAll($stmt,array($v_position_id));
            }
        } 
        return array();
    }
    public function qry_single_advertising($v_adv_id=0) 
    {
        
        
        @session::init();
        $v_website_id = session::get('session_website_id');
        $v_adv_id = replace_bad_char($v_adv_id);
        if($v_adv_id>0)
        {
        
            $stmt="select FK_WEBSITE from t_ps_advertising_position 
                    where PK_ADV_POSITION = (Select FK_ADV_POSITION From t_ps_advertising where PK_ADVERTISING = ?)";
            $v_website_id_of_adv_id = $this->db->getOne($stmt,$v_adv_id);
            if($v_website_id_of_adv_id == $v_website_id)
            {
                if(DATABASE_TYPE == 'MSSQL')
                {
                    $stmt="Select adv.PK_ADVERTISING,
                                adv.FK_ADV_POSITION,
                                adv.C_NAME,
                                adv.C_FILE_NAME,
                                adv.C_URL,
                                CONVERT(varchar(19),C_BEGIN_DATE,103) +char(32)+CONVERT(varchar(10),C_BEGIN_DATE,108) as C_BEGIN_DATE,
                                CONVERT(varchar(19),C_END_DATE,103) +char(32)+CONVERT(varchar(10),C_END_DATE,108) as C_END_DATE
                     From T_PS_ADVERTISING adv
                     where adv.PK_ADVERTISING = ?";
                }
                else if(DATABASE_TYPE == 'MYSQL')
                {
                    $stmt="Select adv.PK_ADVERTISING,
                                adv.FK_ADV_POSITION,
                                adv.C_NAME,
                                adv.C_FILE_NAME,
                                adv.C_URL,
                                DATE_FORMAT(C_BEGIN_DATE,'%d-%m-%Y %H:%i:%s') as C_BEGIN_DATE,
                                DATE_FORMAT(C_END_DATE,'%d-%m-%Y %H:%i:%s') as C_END_DATE
                     From t_ps_advertising adv
                     where adv.PK_ADVERTISING = ?";
                }
                return $this->db->getRow($stmt,array($v_adv_id));
            }
        }
        return array();;
    }
    public function swap_order($id,$id_swap)
    {
        $v_id           = replace_bad_char($id);
        $v_id_swap      = replace_bad_char($id_swap);
        $v_position_id  = get_post_var('hdn_position_id');
        @session::init();
        $v_website_id = session::get('session_website_id');
        $stmt="  select COUNT(FK_ADV_POSITION) from t_ps_advertising_position inner join t_ps_advertising
                    on PK_ADV_POSITION = FK_ADV_POSITION
                    where PK_ADVERTISING in (?,?) and FK_WEBSITE=?";
        $count = $this->db->getOne($stmt,array($v_id,$v_id_swap,$v_website_id));
        if($count > 1)
        {
            $stmt = "Select C_ORDER From t_ps_advertising Where PK_ADVERTISING = ?";
            $temp = $this->db->getOne($stmt,array($v_id));
            
            $stmt = "Update t_ps_advertising set C_ORDER=(SELECT TEMP.C_ORDER FROM (SELECT C_ORDER FROM t_ps_advertising WHERE PK_ADVERTISING=?) TEMP) Where PK_ADVERTISING=?";
            $this->db->Execute($stmt,array($v_id_swap,$v_id));
            
            $stmt = "Update t_ps_advertising set C_ORDER = ? where PK_ADVERTISING= ?";
            $this->db->Execute($stmt,array($temp,$v_id_swap));
        }
        $this->exec_done($this->goback_url,array('hdn_position_id' => $v_position_id));
    }
    public function update_position()
    {
        //var_dump($_POST);
        @session::init();
        $v_website_id=session::get('session_website_id');
        $v_txt_name = get_post_var('txt_position_name','');
        $v_txt_new_name = get_post_var('txt_new_position_name','');
        $v_position_id  = get_post_var('hdn_position_id');
        if($v_txt_new_name=='')
        {
            $stmt="Select FK_WEBSITE From t_ps_advertising_position Where FK_WEBSITE= ? and PK_ADV_POSITION=?";
            $v_website_id_of_position = $this->db->getOne($stmt,array($v_website_id,$v_position_id));
            if($v_website_id_of_position == $v_website_id)
            {
                if($this->gp_check_user_permission('SUA_VI_TRI_QUANG_CAO')>0)
                {
                    $stmt="Update t_ps_advertising_position set C_NAME = ? Where FK_WEBSITE= ? and PK_ADV_POSITION=?";
                    $this->db->Execute($stmt,array($v_txt_name,$v_website_id,$v_position_id));
                }
            }
            
        }
        else
        {
                if($this->gp_check_user_permission('THEM_MOI_VI_TRI_QUANG_CAO')>0)
                {
                    $stmt="Insert into t_ps_advertising_position(C_NAME,FK_WEBSITE) values (?,?)";
                    $this->db->Execute($stmt,array($v_txt_new_name,$v_website_id));
                }
        }
        if($v_position_id=='')
        {
            $stmt="select top 1 PK_ADV_POSITION from t_ps_advertising_position order by PK_ADV_POSITION desc";
            $v_position_id = $this->db->getOne($stmt);
        }
       $this->exec_done($this->goback_url, array('hdn_position_id'=>$v_position_id));
    }
    public function delete_position()
    {
        //var_dump($_POST);
        @session::init();
        $v_website_id   =session::get('session_website_id');
        $v_position_id  = get_post_var('hdn_position_id');
        
        $stmt="Select FK_WEBSITE From t_ps_advertising_position Where FK_WEBSITE= ? and PK_ADV_POSITION=?";
        $v_website_id_of_position = $this->db->getOne($stmt,array($v_website_id,$v_position_id));
        if($v_website_id_of_position == $v_website_id)
        {
            if($this->gp_check_user_permission('XOA_VI_TRI_QUANG_CAO')>0)
            {
                $arr_check=array('t_ps_advertising' =>'FK_ADV_POSITION');  
                $sql_check_depend = $this->gp_build_check_depend_qry($arr_check,'PK_ADV_POSITION');

                $stmt="Select sum(COUNT_DEPEND) From (
                                        SELECT *,$sql_check_depend
                                        FROM t_ps_advertising_position) ap
                        WHERE ap.PK_ADV_POSITION = ?  AND ap.FK_WEBSITE = ?";
                $count=$this->db->getOne($stmt,array($v_position_id,$v_website_id));
                if($count<1)
                {
                    $stmt="Delete From t_ps_advertising_position where PK_ADV_POSITION = $v_position_id and FK_WEBSITE = $v_website_id";
                    $this->db->Execute($stmt);
                }
            }
        }
        $this->exec_done($this->goback_url);
    }
    public function delete_advertising()
    {
        //var_dump($_POST);
        @session::init();
        $v_website_id   = session::get('session_website_id');
        $v_position_id  = get_post_var('hdn_position_id'); 
        $arr_adv_id     = explode(',',  get_post_var('hdn_item_id_list'));
        $stmt="Select count(*) FRom t_ps_advertising_position Where FK_WEBSITE = $v_website_id and PK_ADV_POSITION = $v_position_id";
        $count = $this->db->getOne($stmt);
        if($count > 0)
        {
            foreach ($arr_adv_id as $v_adv_id)
            {
                $stmt="Delete From t_ps_advertising Where PK_ADVERTISING = $v_adv_id and FK_ADV_POSITION = $v_position_id";
                $this->db->Execute($stmt);
            }
        }
        $this->exec_done($this->goback_url,array('hdn_position_id'=>$v_position_id));
    }
    
    function update_advertising()
    {
        //var_dump($_POST);exit;
        @session::init();
        
        $v_webste_id    = session::get('session_website_id');
        $v_adv_id       = get_post_var('hdn_item_id','');
        $v_position_id  = get_post_var('hdn_position_id','');
        $v_adv_name     = get_post_var('txt_adv_name');
        $v_adv_url      = get_post_var('txt_url','');
        $v_file_name    = get_post_var('hdn_file_name'); 
        
        $v_begin_date   = jwDate::ddmmyyyy_to_yyyymmdd(get_post_var('txt_begin_date',''))." ".  get_post_var('txt_begin_time','');
        
        $v_end_date     = jwDate::ddmmyyyy_to_yyyymmdd(get_post_var('txt_end_date',''))." ".  get_post_var('txt_end_time','');
        
        //update advertising
        if($v_adv_id!='')
        {
            $stmt="Select ap.FK_WEBSITE From t_ps_advertising a inner join t_ps_advertising_position ap
                        on ap.PK_ADV_POSITION = a.FK_ADV_POSITION Where a.PK_ADVERTISING = ? ";
            $v_website_id_of_adv = $this->db->getOne($stmt,array($v_adv_id));
            if($v_website_id_of_adv == $v_webste_id)
            {
                if($this->gp_check_user_permission('SUA_QUANG_CAO')>0)
                {
                    $stmt= "Update t_ps_advertising set C_NAME=?,C_BEGIN_DATE=?,C_END_DATE=?,C_URL=?,C_FILE_NAME=?  
                                Where PK_ADVERTISING= ? and FK_ADV_POSITION=? ";
                    $this->db->Execute($stmt,array($v_adv_name,$v_begin_date,$v_end_date,$v_adv_url,$v_file_name,$v_adv_id,$v_position_id));
                    
                    //build order
                    $orther_clause = $other_clause = " AND FK_ADV_POSITION = $v_position_id";
                    $this->build_order('t_ps_advertising', 'PK_ADVERTISING', 'C_ORDER', $orther_clause);
                    
                    //$this->exec_done($this->goback_url,array('hdn_position_id'=>$v_position_id));
                }
                else
                {
                    echo  "<script>alert('Bạn không có quyền thực hiện thao tác này !!!');</script>";
                    $arr_filter = get_filter_condition(array('sel_goto_page', 'sel_rows_per_page'));
                    $this->exec_done($this->goback_url, $arr_filter);
                }
            }
                
        }
        //insert advertising
        else 
        {
            if($this->gp_check_user_permission('THEM_MOI_QUANG_CAO')>0)
            {
//               $stmt = "update t_ps_advertising set C_ORDER = C_ORDER + 1 where FK_ADV_POSITION = ?";
//               $this->db->Execute($stmt,array($v_position_id));
               
               $stmt= "insert into t_ps_advertising(
                                                    FK_ADV_POSITION,
                                                    C_NAME,
                                                    C_ORDER,
                                                    C_FILE_NAME,
                                                    C_URL,
                                                    C_BEGIN_DATE,
                                                    C_END_DATE) 
                                values (?,?,?,?,?,?,?) ";
               $this->db->Execute($stmt,array($v_position_id,$v_adv_name,1,$v_file_name,$v_adv_url,$v_begin_date,$v_end_date));
               
               //reorder
               $v_adv_id = $this->db->getOne("SELECT MAX(PK_ADVERTISING) FROM t_ps_advertising");
               $other_clause = " FK_ADV_POSITION = $v_position_id";
               $this->ReOrder('t_ps_advertising', 'PK_ADVERTISING', 'C_ORDER', $v_adv_id, 1, -1, $other_clause);
               
               $this->exec_done($this->goback_url,array('hdn_position_id'=>$v_position_id));
            }
            else 
            {
                echo  "<script>alert('Bạn không có quyền thực hiện thao tác này !!!');</script>";
                $arr_filter = get_filter_condition(array('sel_goto_page', 'sel_rows_per_page'));
                $this->exec_done($this->goback_url, $arr_filter);
            }
        }
        
        //Hoàn thành và lưu điều kiện lọc
        $arr_filter = get_filter_condition(array('sel_goto_page', 'sel_rows_per_page'));
        $this->exec_done($this->goback_url, $arr_filter);
    }
}
?>