<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of survey_Controller
 *
 * @author HUONG
 */
class survey_Controller extends Controller
{
    function __construct() {
        parent::__construct('admin','survey');
        $this->get_contronller_url =$this->view->get_controller_url();
        $this->check_login();
        $this->model->goback_url = $this->view->get_controller_url();
        $this->view->template->show_left_side_bar = FALSE;
        $this->view->template->arr_count_article = $this->model->gp_qry_count_article();
        //$this->view->template->show_div_website = FALSE;
        
        session::init();
        $v_lang_id = session::get('session_lang_id');
        $this->view->template->arr_all_lang = $this->model->qry_all_lang();
        $this->view->template->arr_all_grant_website = $this->model->gp_qry_all_website_by_user($v_lang_id);
        if(session::check_permission('QL_CAU_HOI_KHAO_SAT')==FALSE)
        {
            die('Bạn không có quyền thực hiện chức năng này !!!');
        }
    }
    function main()
    {
      $this->dsp_all_survey();
    }
    public function dsp_all_survey()
    {
        $v_filter           = isset($_REQUEST['txt_filter']) ? $_REQUEST['txt_filter'] : '';
        $v_member_id        = isset($_REQUEST['sel_ft_member']) ? $_REQUEST['sel_ft_member'] : -1 ;
        $v_begin_date       = isset($_REQUEST['txt_ft_begin_date']) ? $_REQUEST['txt_ft_begin_date'] : '';
        $v_end_date         = isset($_REQUEST['txt_ft_end_date']) ? $_REQUEST['txt_ft_end_date'] : '';
        $v_status           = isset($_REQUEST['sel_ft_status']) ? $_REQUEST['sel_ft_status'] : -1;
        $arr_filter = array(
                                'txt_filter'    =>$v_filter
                               ,'sel_member'    =>$v_member_id
                               ,'txt_begin_date'=>$v_begin_date
                               ,'txt_end_date'  =>$v_end_date
                               ,'sel_status'    =>$v_status
                            );
        
       $VIEW_DATA['arr_all_survey'] = $this->model->qry_all_survey($arr_filter);
       $VIEW_DATA['arr_all_member']         = $this->model->qry_all_member();
       $this->view->render('dsp_all_survey',$VIEW_DATA);
    }
    function dsp_single_question()
    {
        $VIEW_DATA   = array();       
        
        $this->view->render('dsp_single_question',$VIEW_DATA);
       
    }
    
    function update_survey()
    {
        //upload survey
        $v_survey_id            = get_request_var('hdn_item_id',0);
        $v_list_item_new_id     = get_request_var('hdn_list_new_item_id','');
        $v_survey_name          = htmlspecialchars(get_request_var('txt_survey_name',''));
        $v_begin_date           = get_request_var('txt_begin_date','');
        $v_begin_date           = jwDate::ddmmyyyy_to_yyyymmdd($v_begin_date);
        $v_end_date             = get_request_var('txt_end_date','');
        $v_end_date             = jwDate::ddmmyyyy_to_yyyymmdd($v_end_date);
        $v_member              = get_request_var('sel_member','');
        $v_status               = isset($_REQUEST['chk_status']) ? 1:0;
        
        $params = array(
                        $v_survey_name,
                        $v_begin_date,
                        $v_end_date,
                        $v_status,
                        $v_member
                        );
        
        $v_survey_id = $this->model->update_survey($params,$v_survey_id);
        
        if($v_survey_id >0)
        {
            //Add question new
            $v_question_lis_id_new  = get_request_var('hdn_list_new_item_id','');
            if(trim($v_question_lis_id_new) !='')
            {
                $arr_list_id_new = explode(',', $v_question_lis_id_new);
                for($i= 0; $i<count($arr_list_id_new);$i++)
                {
                    $v_question_type = get_request_var('txt_question_type_new_'.$arr_list_id_new[$i],'');
                    $v_question_name = htmlspecialchars(get_request_var('txt_question_name_new_'.$arr_list_id_new[$i],''));
                    if((int)$arr_list_id_new[$i] >0 && $v_question_name != '')
                    {
                        $params = array($v_survey_id,$v_question_name,$v_question_type);
                        $v_question_update_id =  $this->model->do_update_question($params,0);
                        if($v_question_update_id >0)
                        {
                            //add new answer
                            $arr_answer  = isset($_REQUEST['txt_answer_name_new_'.$arr_list_id_new[$i]]) ? $_REQUEST['txt_answer_name_new_'.$arr_list_id_new[$i]] : array();
                            $arr_answer  = is_array($arr_answer) ? $arr_answer : (array)$arr_answer;
                            for($o= 0;$o < count($arr_answer) ;$o ++)
                            {
                                $v_answer_name = htmlspecialchars($arr_answer[$o]);
                                $params   = array($v_survey_id,$v_question_update_id,$v_answer_name,'');
                                $this->model->do_update_answer($params,0);
                            }
                        }
                    }
                }
            }
            //Update question old
            $v_question_list_id_old  = get_request_var('hdn_list_old_item_id','');
            if(trim($v_question_list_id_old) !='')
            {
                $arr_list_id_old = explode(',', $v_question_list_id_old);
                for($i= 0; $i<count($arr_list_id_old);$i++)
                {
                    $v_question_id   = $arr_list_id_old[$i];
                    if((int)$v_question_id >0)
                    {
                        $v_question_type = get_request_var('txt_question_type_old_'.trim($arr_list_id_old[$i]),'');
                        $v_question_name = htmlspecialchars(get_request_var('txt_question_name_old_'.trim($arr_list_id_old[$i]),''));
                       
                        $params = array($v_survey_id,$v_question_name,$v_question_type);
                        /**
                        * Kiem tra loai của cau hoi hien tai co bi thay doi 
                        * Neu la cau hoi thuoc loai nhieu lua tron tra loi thanh danh mot lua chọn dang (text or area) thì tien hanh xoa tat ca cac cau tra loi truoc
                        */
                        $v_question_type_old =  -1;
                        $arr_old_question = $this->model->qry_single_question($v_question_id);
                        if(sizeof($arr_old_question) >0 )
                        {
                            $v_question_type_old = $arr_old_question['C_TYPE'];
                        }
                        $v_question_update_id = $this->model->do_update_question($params,$v_question_id);
                        if($v_question_update_id >0)
                        {
                            
                             //add new answer
                            $arr_answer  = isset($_REQUEST['txt_answer_name_old_'.trim($arr_list_id_old[$i])]) ? $_REQUEST['txt_answer_name_old_'.trim($arr_list_id_old[$i])] : array();
                            $arr_answer  = is_array($arr_answer) ? $arr_answer : (array)$arr_answer;
                            //id ansswer
                            $arr_answer_id  = isset($_REQUEST['text_question_answer_id_old_'.trim($arr_list_id_old[$i])]) ? $_REQUEST['text_question_answer_id_old_'.trim($arr_list_id_old[$i])] : array();
                            $arr_answer_id  = isset($arr_answer_id) ? $arr_answer_id : (array)$arr_answer_id;
                           
                            if($v_question_type_old == 0 OR $v_question_type_old == 1)
                            {
                                if($v_question_type ==2 OR $v_question_type == 3 )
                                {
                                    $this->model->do_delete_answer(''," And  FK_SURVEY_QUESTION = '$v_question_update_id' ");
                                }
                            }
                            for($o= 0;$o < count($arr_answer) ;$o ++)
                            {
                                $v_answer_name = htmlspecialchars($arr_answer[$o]);
                                $v_answer_id   = isset($arr_answer_id[$o]) ? $arr_answer_id[$o] :0 ;
                                $params        = array($v_survey_id,$v_question_update_id,$v_answer_name,'');
                                
                                $this->model->do_update_answer($params,$v_answer_id);
                            }
                        }
                    }
                }
            }
            //Xoa bo cac cau hoi cu da duoc chọn xóa
            $v_question_list_delete_id  = get_request_var('hdn_list_question_delete_id','');
            if(trim($v_question_list_delete_id) !='')
            {
                $this->model->do_delete_question($v_question_list_delete_id);
                $this->model->do_delete_answer(''," And FK_SURVEY_QUESTION in($v_question_list_delete_id)");
            }
            
            $v_list_anwer_delete_id = get_request_var('hdn_list_answer_delete_id','');
            if(trim($v_list_anwer_delete_id) !='')
            {
                $this->model->do_delete_answer($v_list_anwer_delete_id);
            }
        }
        $this->model->exec_done($this->get_contronller_url.'dsp_single_survey/'.$v_survey_id.'/', array('hdn_item_id'=>$v_survey_id));
    }
    
    /**
     * Hien thi chi tiet cau hoi khao sat theo ma cau hoi 
     * @param int $v_survey_id ma cua cau hoi khao sat
     * @return html Hien thi chi tiet cau hoi khao sat duoi dang html
     */
    function dsp_single_survey($v_survey_id =0)
    {
        $v_survey_id = replace_bad_char($v_survey_id);
        if((int)$v_survey_id == 0)
        {
            $v_survey_id = get_request_var('hdn_item_id');
        }
      
        $VIEW_DATA['arr_single_survey']   = $this->model->qry_single_survey($v_survey_id);
        $VIEW_DATA['arr_all_member']         = $this->model->qry_all_member();
        $VIEW_DATA['arr_all_question']    = $this->model->qry_all_question($v_survey_id);  
        
        $this->view->render('dsp_single_survey',$VIEW_DATA);
        
       
    }
    
    /**
     * Xoa cau hoi khao sat
     */
    public function dsp_delete_survey()
    {
        $v_list_survey_id = get_request_var('hdn_item_id_list','');
        $this->model->qry_delete_survey($v_list_survey_id);
    }
    
    /**
     * Bao cao ket qua trung cau y kien
     * @return html chua ket qua y kien danh gia
     */
    public function dsp_report_answer($v_survey_id = 0)
    {
        $v_survey = replace_bad_char($v_survey_id);
        $VIEW_DATA = array();
        $VIEW_DATA['arr_single_answer']   = $this->model->qry_single_survey($v_survey_id);
        $VIEW_DATA['arr_question_answer'] = $this->model->get_all_question_answer($v_survey);
        $VIEW_DATA['v_current_survey_id']    = $v_survey;
        $this->view->render('dsp_reporting_answer',$VIEW_DATA);
    }
    
    public function dsp_print_report_answer($v_survey_id = 0){
        $v_survey = replace_bad_char($v_survey_id);
        $VIEW_DATA = array();
        $VIEW_DATA['arr_single_answer']   = $this->model->qry_single_survey($v_survey_id);
        $VIEW_DATA['arr_question_answer'] = $this->model->get_all_question_answer($v_survey);
    
        $this->view->render('dsp_print_report_answer',$VIEW_DATA);
    }
}

?>
