<?php

class citizens_question_Model extends Model {

    function __construct() {
        parent::__construct();
    }

    public function qry_all_question()
    {
        $v_website_id  = session::get('session_website_id');
        //cac bien loc
        $sql_search   = '';
        $v_key_word   = get_post_var('txt_advanced_search','');
        $sql_search  .= ($v_key_word=='')?'':" And C_TITLE like '%$v_key_word%'";
        
        $v_field_id   = get_post_var('select_field','0');
        $sql_search  .= ($v_field_id=='0')?'':" And FK_FIELD= '$v_field_id' ";
        
        $v_begin_date = jwDate::ddmmyyyy_to_yyyymmdd(get_post_var('txt_begin_time',''));
        $sql_search  .= ($v_begin_date=='')?'':" And  q.C_DATE >= '$v_begin_date' ";
        
        $v_end_date   = jwDate::ddmmyyyy_to_yyyymmdd(get_post_var('txt_end_time',''));
        $sql_search  .= ($v_end_date=='')?'':" And  q.C_DATE <= '$v_end_date' ";
        
        page_calc($v_start, $v_end);
        if($v_start >0) 
        {
            $v_start = $v_start -1;
        }
        else
        {
            $v_start = 0;
        }
        $v_limit  = $v_end - $v_start;
        $v_limit = ($v_limit) >0 ? $v_limit : 5;
        
        $sql="select q.PK_CQ,
                @curRow := @curRow + 1 AS rn,
                q.C_TITLE,
                q.C_NAME,
                q.C_STATUS,
                q.C_ORDER
            from t_ps_cq q inner join t_ps_cq_field f on f.PK_FIELD = q.FK_FIELD
            where 1=1 AND f.FK_WEBSITE = $v_website_id $sql_search";
        $stmt="select *,
                     (Select Count(*) From t_ps_cq) as TOTAL_RECORD  
            from ($sql) a JOIN    (SELECT @curRow := 0) r order by C_ORDER asc  limit $v_start,$v_limit";
        
        $v_boolen_search = ($sql_search=='')?'0':'1';
        $DATA_MODEL['arr_search'] = array('txt_advanced_search' =>$v_key_word,
                            'select_field' => $v_field_id,
                            'txt_begin_time' => get_post_var('txt_begin_time',''),
                            'txt_end_time' => get_post_var('txt_end_time',''),
                            'boolen_question_search' => $v_boolen_search
            );
        $DATA_MODEL['arr_all_question'] = $this->db->getAll($stmt);
        
        return $DATA_MODEL;
    }
    public function qry_all_field()
    {
        $sql_check_depend =$this->gp_build_check_depend_qry(array('t_ps_cq'=>'FK_FIELD'), 'PK_FIELD');
        
        $v_website_id  = session::get('session_website_id');
        //cac bien loc
        $sql_search   = '';
        $v_key_word   = get_post_var('txt_field_advanced_search','');
        $sql_search  .= ($v_key_word=='')?'':" And C_NAME like '%$v_key_word%' ";
        
        $v_field_id   = get_post_var('select_status','-1');
        $sql_search  .= ($v_field_id=='-1')?'':" And C_STATUS='$v_field_id' ";
        
        $v_begin_date = jwDate::ddmmyyyy_to_yyyymmdd(get_post_var('txt_field_begin_time',''));
        $sql_search  .= ($v_begin_date=='')?'':" And '$v_begin_date' >= C_DATE ";
        
        $v_end_date   = jwDate::ddmmyyyy_to_yyyymmdd(get_post_var('txt_field_end_time',''));
        $sql_search  .= ($v_end_date=='')?'':" And C_DATE <= '$v_end_date' ";
        
        $stmt="select C_STATUS,C_ORDER,C_NAME,PK_FIELD,$sql_check_depend from t_ps_cq_field where FK_WEBSITE = $v_website_id $sql_search order by C_ORDER";
        $DATA_MODEL['arr_all_field'] = $this->db->getAll($stmt);
        
        $v_boolen_search = ($sql_search=='')?'0':'1';
        $DATA_MODEL['arr_search'] = array('txt_field_advanced_search' =>$v_key_word,
                            'select_status' => $v_field_id,
                            'txt_field_begin_time' => get_post_var('txt_field_begin_time',''),
                            'txt_field_end_time' => get_post_var('txt_field_end_time',''),
                            'boolen_field_search' => $v_boolen_search);
        return $DATA_MODEL;
    }
    public function delete_question()
    {
        $v_website_id = session::get('session_website_id');
        $v_id_list = get_post_var('hdn_item_id_list');
        $arr_id    = explode(',', $v_id_list);
        foreach($arr_id as $v_id)
        {
            $stmt = "delete FROM t_ps_cq where PK_CQ = $v_id and FK_FIELD IN (SELECT PK_FIELD FROM t_ps_cq_field WHERE FK_WEBSITE = '$v_website_id')";
            $this->db->Execute($stmt);
          
        }
        $this->exec_done($this->goback_url,array('hdn_tab_select'=>'question'));
    }
    
    public function delete_field()
    {
        $sql_check_depend =$this->gp_build_check_depend_qry(array('t_ps_cq'=>'FK_FIELD'), 'PK_FIELD');
        
        $v_website_id = session::get('session_website_id');
        $v_id_list = get_post_var('hdn_item_id_list');
        $arr_id    = explode(',', $v_id_list);
        foreach($arr_id as $v_id)
        {
            $stmt = "delete FROM t_ps_cq_field where PK_FIELD = $v_id and FK_WEBSITE = $v_website_id";
            $this->db->Execute($stmt);
        }
        
        $this->exec_done($this->goback_url,array('hdn_tab_select'=>'field'));
    }
    
    public function swap_order_cq($type)
    {
        $type = replace_bad_char($type);
        $v_website_id = session::get('session_website_id');
        $v_id = get_post_var('hdn_item_id', '');
        $v_id_swap = get_post_var('hdn_item_id_swap', '');
        $stmt="select COUNT(a.FK_WEBSITE) from (select f.FK_WEBSITE from t_ps_cq q inner join t_ps_cq_field f 
                on f.PK_FIELD = q.FK_FIELD where q.PK_CQ = $v_id and f.FK_WEBSITE = $v_website_id
                union
                select f.FK_WEBSITE from t_ps_cq q inner join t_ps_cq_field f 
                on f.PK_FIELD = q.FK_FIELD where q.PK_CQ = $v_id_swap and f.FK_WEBSITE = $v_website_id) a";
        $count = $this->db->getOne($stmt);
        if($count=='1')
        {
             if ($type == 'question') {
                if ($v_id_swap != '' && $v_id != '') {
                    $this->swap_order('t_ps_cq', 'PK_CQ', 'C_ORDER', $v_id_swap, $v_id);    
                    
                    $this->exec_done($this->goback_url,array('hdn_tab_select'=>$type));
                }
            } else if ($type == 'field') {
                if ($v_id_swap != '' && $v_id != '') {
                    $this->swap_order('t_ps_cq_field', 'PK_FIELD', 'C_ORDER', $v_id, $v_id_swap);
                    $this->exec_done($this->goback_url,array('hdn_tab_select'=>$type));
                }
            }
        }
        
        $this->exec_fail($this->view->get_controller_url(), __('error function during the implementation'),array('hdn_tab_select'=>$type));
    }
    
    public function qry_single_question($id)
    {
        $v_question_id = replace_bad_char($id);
        $v_website_id = session::get('session_website_id');
        if(is_numeric($id))
        {
            $stmt="select COUNT(a.FK_WEBSITE) from (
                        select FK_WEBSITE from t_ps_cq q inner join t_ps_cq_field f 
                        on q.FK_FIELD = f.PK_FIELD
                        where q.PK_CQ = $id and f.FK_WEBSITE = $v_website_id
                ) a ";
            $count = $this->db->getOne($stmt);
            if($count != 0)
            {
                    $stmt="select PK_CQ,
                                FK_FIELD,
                                C_NAME,
                                C_ADDRESS,
                                C_PHONE,
                                C_EMAIL,
                                C_SLUG,
                                C_TITLE,
                                C_CONTENT,
                                C_ANSWER,
                                DATE_FORMAT(C_DATE,'%d-%m-%Y') as C_DATE,
                                C_STATUS,
                                C_ORDER 
                        from t_ps_cq where PK_CQ = $v_question_id";
                    return $this->db->getRow($stmt);
            }
        }
        return array();
    }
    
    public function qry_single_field($id)
    {
        $id=  replace_bad_char($id);
        $v_website_id = session::get('session_website_id');
        if($id!='0')
        {
            $stmt="select PK_FIELD,C_STATUS,
                      C_ORDER,C_NAME,FK_WEBSITE, DATE_FORMAT(C_DATE,'%d-%m-%Y') AS C_DATE
            from t_ps_cq_field where FK_WEBSITE = $v_website_id and PK_FIELD = $id";
        }
        else
        {
            $stmt= "select  C_ORDER as C_ORDER_MAX from t_ps_cq_field where FK_WEBSITE = $v_website_id order by C_ORDER desc limit 1";
        }
        return $this->db->getRow($stmt);
    }
    
    public function update_question()
    {
        $v_website_id = session::get('session_website_id');
        
       
        $v_question_id   = get_post_var('hdn_item_id',0);        
        $v_field_id      = get_post_var('select_field',0);
        $v_sender        = get_post_var('txt_sender','');
        $v_address       = get_post_var('txt_address','');
        $v_phone         = get_post_var('txt_phone','');
        $v_email         = get_post_var('txt_email','');
        $v_order         = get_post_var('txt_order','');
        $v_title         = htmlspecialchars(get_post_var('txt_title',''));
        $v_slug          = get_post_var('txt_slug','');
        $v_content       = htmlspecialchars($this->prepare_tinyMCE(get_post_var('txt_content','',FALSE)));
        $v_answer        = htmlspecialchars($this->prepare_tinyMCE(get_post_var('txt_answer','',FALSE)));
        $v_status        = (get_post_var('chk_status','0')=='0')?'0':'1';
        $v_current_order = ($v_question_id != '') ? get_post_var('hdn_current_order') : '-1';
        $other_clause    = "FK_FIELD in (select PK_FIELD FROM t_ps_cq_field where FK_WEBSITE = $v_website_id)";
        
        $stmt="update t_ps_cq set FK_FIELD = ?,
                                   C_NAME = ?,
                                   C_ADDRESS = ?,
                                   C_PHONE = ?,
                                   C_EMAIL = ?,
                                   C_TITLE = ?,
                                   C_SLUG = ?,
                                   C_STATUS = ?,
                                   C_CONTENT = ?,
                                   C_ANSWER = ?
            where PK_CQ = ? and ? = (select FK_WEBSITE from t_ps_cq_field f where f.PK_FIELD = ?)";
        $this->db->Execute($stmt,array($v_field_id,$v_sender,
                                       $v_address,$v_phone,
                                       $v_email,$v_title,$v_slug,$v_status,
                                       $v_content,$v_answer,
                                       $v_question_id,$v_website_id,$v_field_id,$v_question_id
                                       
            ));
        $this->ReOrder('t_ps_cq', 'PK_CQ', 'C_ORDER', $v_question_id, $v_order, $v_current_order,$other_clause);
        $this->exec_done($this->goback_url);
    }
    public function update_field()
    {
        $v_website_id = session::get('session_website_id');
       
        $v_field_id      = get_post_var('hdn_item_id',0);
        $v_field_name    = get_post_var('txt_field_name','');
        $v_order         = get_post_var('txt_order','');
        $v_status        = (get_post_var('chk_status','0')!='0')?'1':'0';
        $v_current_order = ($v_field_id != '') ? get_post_var('hdn_current_order') : '-1';
        
        $other_clause    = "FK_WEBSITE = $v_website_id";
        if($v_field_id !='')
        {
            $stmt = "update t_ps_cq_field set 
                                   C_NAME = ?,
                                   C_STATUS = ? 
            where PK_FIELD = ? and FK_WEBSITE = $v_website_id";

            $this->db->Execute($stmt, array($v_field_name, $v_status, $v_field_id, $v_website_id));
        }
        else
        {
            $stmt = "insert into t_ps_cq_field(C_STATUS,C_ORDER,C_NAME,FK_WEBSITE,C_DATE) values
                     ($v_status,1,'$v_field_name',$v_website_id,Now())";
            
             $this->db->Execute($stmt);
             $stmt = "select PK_FIELD from t_ps_cq_field  where FK_WEBSITE= $v_website_id order by PK_FIELD desc limit 1";
             $v_field_id=$this->db->getOne($stmt);
        }
        $this->ReOrder('t_ps_cq_field', 'PK_FIELD', 'C_ORDER', $v_field_id, $v_order, $v_current_order,$other_clause);
        $this->exec_done($this->goback_url,array('hdn_tab_select'=>'field'));
    }
}
?>