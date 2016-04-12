<?php

class poll_Model extends Model {

    function __construct() {
        parent::__construct();
    }

    public function qry_all_poll() 
    {
        @session::init();
        $v_website_id=session::get('session_website_id');
        
        
        if(DATABASE_TYPE == 'MSSQL')
        {
            $stmt= "Select PK_POLL,
                        FK_WEBSITE,
                        C_STATUS,C_NAME,
                        CONVERT(varchar(10),C_BEGIN_DATE,103) as C_BEGIN_DATE,
                        CONVERT(varchar(10),C_END_DATE,103) as  C_END_DATE 
                From t_ps_poll where FK_WEBSITE = ?";
        }
        else if(DATABASE_TYPE == 'MYSQL')
        {
            $stmt= "Select PK_POLL,
                        FK_WEBSITE,
                        C_STATUS,C_NAME,
                        DATE_FORMAT(C_BEGIN_DATE,'%d-%m-%Y') as C_BEGIN_DATE,
                        DATE_FORMAT(C_END_DATE,'%d-%m-%Y') as  C_END_DATE 
                From t_ps_poll where FK_WEBSITE = ?";
        }
        
        return $this->db->getAll($stmt,array($v_website_id));
    }
    
    public function qry_single_poll($v_poll_id=0) 
    {
        //var_dump($_POST);exit;
        @session::init();
        $v_website_id  = session::get('session_website_id');
        $v_poll_id     = replace_bad_char($v_poll_id);
        if($v_poll_id>0)
        {
            $stmt="select FK_WEBSITE from t_ps_poll 
                    where PK_POLL = ?";
            $v_website_id_of_poll_id = $this->db->getOne($stmt,$v_poll_id);
            if($v_website_id_of_poll_id == $v_website_id)
            {
                if(DATABASE_TYPE == 'MSSQL')
                {
                    //arr single poll
                    $stmt="Select PK_POLL,
                                        FK_WEBSITE,
                                        C_STATUS,C_NAME,
                                        CONVERT(varchar(10),C_BEGIN_DATE,103)+CHAR(32)+CONVERT(varchar(10),C_BEGIN_DATE,108) as C_BEGIN_DATE,
                                        CONVERT(varchar(10),C_END_DATE,103)+CHAR(32)+CONVERT(varchar(10),C_END_DATE,108) as  C_END_DATE 
                        From t_ps_poll where PK_POLL = ?";
                    $DATA_MODEL['arr_single_poll'] = $this->db->getRow($stmt,array($v_poll_id));
                    
                    //arr poll detail
                    $stmt="select PK_POLL_DETAIL,
                                    FK_POLL,
                                    C_ANSWER,
                                    C_VOTE
                    from t_ps_poll_detail where FK_POLL = ?";
                    $DATA_MODEL['arr_all_answer']  = $this->db->getAll($stmt,array($v_poll_id));
                    
                }
                else if(DATABASE_TYPE == 'MYSQL')
                {
                    //arr single poll
                    $stmt="Select PK_POLL,
                                        FK_WEBSITE,
                                        C_STATUS,C_NAME,
                                        DATE_FORMAT(C_BEGIN_DATE,'%d-%m-%Y %H:%i:%s') as C_BEGIN_DATE,
                                        DATE_FORMAT(C_END_DATE,'%d-%m-%Y %H:%i:%s') as  C_END_DATE 
                        From t_ps_poll where PK_POLL = ?";
                    $DATA_MODEL['arr_single_poll'] = $this->db->getRow($stmt,array($v_poll_id));
                    
                    //arr poll detail
                    $stmt="select PK_POLL_DETAIL,
                                    FK_POLL,
                                    C_ANSWER,
                                    C_VOTE
                    from t_ps_poll_detail where FK_POLL = ?";
                    $DATA_MODEL['arr_all_answer']  = $this->db->getAll($stmt,array($v_poll_id));
                }
                
                return $DATA_MODEL;
            }
        }
        return array();;
    }
    public function delete_poll()
    {
        //var_dump($_POST);
        @session::init();
        $v_website_id   = session::get('session_website_id');
        $arr_poll_id   = explode(',',  get_post_var('hdn_item_id_list'));
        foreach ($arr_poll_id as $v_poll_id)
        {
            $stmt = "select FK_WEBSITE from t_ps_poll where PK_POLL = ?";
            $v_website_id_of_poll_id = $this->db->getOne($stmt, $v_poll_id);
            if ($v_website_id_of_poll_id == $v_website_id) 
            {
                if($this->gp_check_user_permission('XOA_CUOC_THAM_DO_Y_KIEN')>0)
                {
                    $stmt="Delete From t_ps_poll_detail Where FK_POLL = ?";
                    $this->db->Execute($stmt,array($v_poll_id));

                    $stmt="Delete From t_ps_poll Where PK_POLL= ?";
                    $this->db->Execute($stmt,array($v_poll_id));
                }
            }
        }
        $this->exec_done($this->goback_url);
    }
    
    function update_poll()
    {
        //echo 'File: ' . __FILE__ . '<br>Line: ' . __LINE__;
        //var_dump::display($_POST);
        @session::init();
        $v_website_id            = session::get('session_website_id');
        $v_poll_id               = get_post_var('hdn_item_id','');
        $v_poll_name             = get_post_var('txt_poll_name');
        $v_poll_status           = get_post_var('poll_status');
        
        $v_delete_answer_list    = get_post_var('hdn_id_delete_answer_list','');
        
        $arr_answer              = isset($_POST['txt_poll_answer'])?$_POST['txt_poll_answer']: array();   
        $v_answer_old_list       = get_post_var('hdn_item_id_list_old','');
        $arr_answer_old_list     = ($v_answer_old_list!='')?explode(',', $v_answer_old_list):array();
        
        $v_answer_new_list       = get_post_var('hdn_item_id_list_new','');
        $arr_answer_new_list     = ($v_answer_new_list!='')?explode(',', $v_answer_new_list):array();
        
        $v_begin_date   = jwDate::ddmmyyyy_to_yyyymmdd(get_post_var('txt_begin_date',''))." ".  get_post_var('txt_begin_time','');
        
        $v_end_date     = jwDate::ddmmyyyy_to_yyyymmdd(get_post_var('txt_end_date',''))." ".  get_post_var('txt_end_time','');
        
        //update poll
        if($v_poll_id!='')
        {
            $stmt="select distinct FK_WEBSITE from t_ps_poll Where PK_POLL =?";
            $v_website_id_of_poll = $this->db->getOne($stmt,array($v_poll_id));
            if($v_website_id_of_poll == $v_website_id)
            {
                if($this->gp_check_user_permission('SUA_CUOC_THAM_DO_Y_KIEN')>0)
                {
                    //update poll
                    $stmt="update t_ps_poll set C_STATUS=?,C_NAME=?,C_BEGIN_DATE=?,C_END_DATE=?
                            where PK_POLL=?";
                    $arr_stmt = array($v_poll_status,$v_poll_name,$v_begin_date,$v_end_date,$v_poll_id);
                    $this->db->Execute($stmt,$arr_stmt);
                    //update poll answer
                    for($i=0;$i<count($arr_answer_old_list);$i++)
                    {
                        $v_answer_id    = $arr_answer_old_list[$i];
                        $v_answer_name  = replace_bad_char($arr_answer[$i]);
                        $stmt="Update t_ps_poll_detail set C_ANSWER = '$v_answer_name' where PK_POLL_DETAIL=$v_answer_id";
                        $this->db->Execute($stmt);
                    }
                    //xoá dữ liệu answer
                    if($v_delete_answer_list !='')
                    {
                        $stmt = "Delete From t_ps_poll_detail Where PK_POLL_DETAIL in ($v_delete_answer_list)";
                        $this->db->Execute($stmt);
                    }
                    //thêm answer
                    if($v_answer_new_list!='')
                    {
                        foreach($arr_answer_new_list as $answer)
                        {
                            $v_answer_name = $answer;
                            $stmt="insert into t_ps_poll_detail(FK_POLL,C_ANSWER,C_VOTE)
                                   values (?,?,?)";
                            $this->db->Execute($stmt,array($v_poll_id,$v_answer_name,0));
                        }
                    }
                    $this->exec_done($this->goback_url);
                }
                else
                {
                    echo  "<script>alert('Bạn không có quyền thực hiện thao tác này !!!');</script>";
                    $this->exec_done($this->goback_url);
                }
            }
        }
        //insert poll
        else 
        {
            if($this->gp_check_user_permission('THEM_MOI_CUOC_THAM_DO_Y_KIEN')>0)
            {
               //var_dump($_POST);
               
               $stmt= "insert into t_ps_poll(FK_WEBSITE,C_STATUS,C_NAME,C_BEGIN_DATE,C_END_DATE)
                        values(?,?,?,?,?)";
               $arr_stmt = array($v_website_id,$v_poll_status,$v_poll_name,
                                    $v_begin_date,$v_end_date);
               $this->db->Execute($stmt,$arr_stmt);
               
               $stmt="Select PK_POLL From t_ps_poll ORDER BY PK_POLL desc limit 1";
               $v_new_id = $this->db->getOne($stmt);
                if($v_answer_new_list!='')
                {
                    foreach($arr_answer_new_list as $answer)
                    {
                        $v_answer_name = $answer;
                        $stmt="insert into t_ps_poll_detail(FK_POLL,C_ANSWER,C_VOTE)
                               values (?,?,?)";
                        $this->db->Execute($stmt,array($v_new_id,$v_answer_name,0));
                    }
                }
               $this->exec_done($this->goback_url);
            }
            else 
            {
                echo  "<script>alert('Bạn không có quyền thực hiện thao tác này !!!');</script>";
                $this->exec_done($this->goback_url, $arr_filter);
            }
        }
        
        //Hoàn thành và lưu điều kiện lọc
        //$arr_filter = get_filter_condition(array('sel_goto_page', 'sel_rows_per_page'));
        $this->exec_done($this->goback_url);
    }
}
?>