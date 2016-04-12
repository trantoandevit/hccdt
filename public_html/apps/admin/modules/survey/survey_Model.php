<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of survey_Model
 *
 * @author HUONG
 */
class survey_Model extends Model
{
    function __construct() {
        parent::__construct();
        @session::init();
        $this->website_id  = session::get('session_website_id');
    }
    
    public function qry_all_survey($arr_filter = array())
    {
        $v_website_id = $this->website_id;
        $v_condition = '';
        if(sizeof($arr_filter) >0)
        {
            if(isset($arr_filter['txt_filter']) && trim($arr_filter['txt_filter']) != '')
            {
                $v_filter = htmlspecialchars($arr_filter['txt_filter']);
                $v_condition .= " And  C_NAME like '%$v_filter%' ";
              
            }
            if(isset($arr_filter['sel_member']) && trim($arr_filter['sel_member']) >-1)
            {
                $v_member_id  = trim($arr_filter['sel_member']);
                $v_condition .= " And  FK_MEMBER = '$v_member_id' ";
            }
            if(isset($arr_filter['txt_begin_date']) && trim($arr_filter['txt_begin_date']) != '' && jwDate::validateDate($arr_filter['txt_begin_date'],'d-m-Y'))
            {
                $v_begin_date   = trim($arr_filter['txt_begin_date']);
                $v_begin_date   = jwDate::ddmmyyyy_to_yyyymmdd($v_begin_date);
                $v_condition   .= " And C_BEGIN_DATE >= '$v_begin_date'  ";
            }
            if(isset($arr_filter['txt_end_date']) && trim($arr_filter['txt_end_date']) != '' && jwDate::validateDate($arr_filter['txt_end_date'],'d-m-Y'))
            {
                $v_end_date   = trim($arr_filter['txt_end_date']);
                $v_end_date   = jwDate::ddmmyyyy_to_yyyymmdd($v_end_date);
                $v_condition .= " And C_END_DATE <= '$v_end_date'  ";
            }
            if(isset($arr_filter['sel_status']) && trim($arr_filter['sel_status']) > -1)
            {
                $v_status     = trim($arr_filter['sel_status']);
                $v_condition .= " And C_STATUS = '$v_status'  ";
            }
        }
        $stmt = " SELECT ps.*,
                    CASE FK_MEMBER WHEN 0 THEN 'Cổng thông tin'
                                   ELSE
                                   (SELECT C_NAME FROM t_ps_member WHERE PK_MEMBER = ps.FK_MEMBER)
                   END AS C_MEMBER_NAME 
                     FROM t_ps_survey ps 
                     Where 
                     FK_WEBSITE = '$v_website_id'
                    $v_condition 
        ";
        
        $results =  $this->db->GetAll($stmt);
        if($this->db->ErrorNo() != 0)
        {
                $this->exec_fail($this->goback_url, 'Đã xảy ra lõi trong quá trình cập nhật. Xin vui lòng thực hiện lại!'); 
        }
        return $results;
    }
    
    public function qry_all_question($v_survey_id = 0)
    {
        $v_survey_id         = replace_bad_char($v_survey_id);
        $stmt  = "SELECT
                        sq.PK_SURVEY_QUESTION,
                        sq.C_NAME,
                        sq.C_TYPE,
                        (SELECT 

                              CONCAT('<data>',
                                      GROUP_CONCAT('<item',
                                                               CONCAT(' PK_SURVEY_ANSWER=\"',PK_SURVEY_ANSWER,'\"')
                                                              ,CONCAT(' C_NAME=\"',C_NAME,'\"')					
                                      ,'/>' SEPARATOR ' ')
                              ,'</data>')
                              FROM t_ps_survey_answer  sa WHERE sa.FK_SURVEY_QUESTION = sq.PK_SURVEY_QUESTION
                           ) As C_XML_ANSWER
                      FROM t_ps_survey_question sq 
                      WHERE FK_SURVEY = ?
                    ";
         $results =   $this->db->GetAll($stmt,array($v_survey_id));
        if($this->db->ErrorNo() != 0)
        {
                $this->exec_fail($this->goback_url, 'Đã xảy ra lõi trong quá trình cập nhật. Xin vui lòng thực hiện lại!'); 
        }
        return $results;
    }
        
    /**
     * 
     * @param array $params_answer mang chua tham so gia tri thuc hien update or inser
     * @param int $v_question_id ma cua cau hoi
     * @param array $arr_all_method  Mang chua cac duong dan xu ly
     * @return goback url
     */
    public function do_update_answer($params_answer=array(),$v_question_answer_id = 0)
    {
        if($v_question_answer_id == 0 && sizeof($params_answer)>0)
        {
            //Insert  dap an
            $qry_update_answer =  "INSERT INTO t_ps_survey_answer 
                                            ( 
                                                FK_SURVEY, 
                                                FK_SURVEY_QUESTION, 
                                                C_NAME, 
                                                C_RESULT
                                            )
                                            VALUES
                                            ( ?,?,?,? )";
            $this->db->Execute($qry_update_answer,$params_answer);
            if($this->db->ErrorNo() != 0)
            {
                   $this->exec_fail($this->goback_url, 'Đã xảy ra lõi trong quá trình cập nhật. Xin vui lòng thực hiện lại!'); 
            }
        }
        else if($v_question_answer_id > 0 && sizeof($params_answer)>0)
        {
          
         //Update dap an
             $stmt = "UPDATE t_ps_survey_answer 
                        SET	
                        FK_SURVEY = ? , 
                        FK_SURVEY_QUESTION = ? , 
                        C_NAME   = ? , 
                        C_RESULT = ?

                        WHERE
                        PK_SURVEY_ANSWER = '$v_question_answer_id'";
            $this->db->Execute($stmt,$params_answer);
            if($this->db->ErrorNo() != 0)
            {
                   $this->exec_fail($this->goback_url, 'Đã xảy ra lõi trong quá trình cập nhật. Xin vui lòng thực hiện lại!'); 
            }
        }
        
    }
    
    public function do_delete_answer($v_list_id ='',$v_where = '')
    {
            if(trim($v_where) != '')
            {
                $stmt = "DELETE
                                FROM t_ps_survey_answer
                                 where (1 =1) $v_where ";
               
               $this->db->Execute($stmt);
               if($this->db->ErrorNo() != 0)
                {
                       $this->exec_fail($this->goback_url, 'Đã xảy ra lõi trong quá trình cập nhật. Xin vui lòng thực hiện lại!'); 
                }   
            }
            if(trim($v_list_id) !='')
            {
                $stmt = "DELETE
                                 FROM t_ps_survey_answer
                                 WHERE PK_SURVEY_ANSWER IN($v_list_id)";
                $this->db->Execute($stmt);
                if($this->db->ErrorNo() != 0)
                 {
                        $this->exec_fail($this->goback_url, 'Đã xảy ra lõi trong quá trình cập nhật. Xin vui lòng thực hiện lại!'); 
                 }
            }
    }
    
    public function do_delete_question($v_list_id ='')
    {
           $stmt = "DELETE
                            FROM t_ps_survey_question
                            WHERE PK_SURVEY_QUESTION IN($v_list_id)";
           $this->db->Execute($stmt);
           if($this->db->ErrorNo() != 0)
            {
                   $this->exec_fail($this->goback_url, 'Đã xảy ra lõi trong quá trình cập nhật. Xin vui lòng thực hiện lại!'); 
            }
    }
    /**
     * 
     * @param array $params Mang chua tham so insert or update
     * @param int $status 
     */
    public function do_update_question($params = array(),$v_question_id = 0)
    {
        
        if(sizeof($params)>0 && $v_question_id == 0)
        {
            //Insert Question
            $qry_update_question = "INSERT INTO t_ps_survey_question 
                                (               
                                FK_SURVEY, 
                                C_NAME, 
                                C_TYPE
                                )
                                VALUES
                                ( ?, ?,?)";
            $this->db->Execute($qry_update_question,$params);
            
            if($this->db->ErrorNo() == 0)
            {
                return $this->db->GetOne('SELECT LAST_INSERT_ID()');
            }
        }
        elseif(sizeof($params)>0 && $v_question_id > 0)
        {
            //Update question
            if($this->db->ErrorNo() == 0)
            {
                 $stmt =" UPDATE t_ps_survey_question 
                                    SET	
                                    FK_SURVEY = ? , 
                                    C_NAME = ?, 
                                    C_TYPE = ?

                                    WHERE
                                    PK_SURVEY_QUESTION = '$v_question_id'";
                 $this->db->Execute($stmt,$params);
                if($this->db->ErrorNo() == 0)
                {
                    return $v_question_id;
                }
            }
        }
        if($this->db->ErrorNo() != 0)
        {
               $this->exec_fail($this->goback_url, 'Đã xảy ra lõi trong quá trình cập nhật. Xin vui lòng thực hiện lại!'); 
        }
        return 0;
    }
    
//    
    function qry_single_survey($v_survey_id =0)
    {
        $v_website_id = $this->website_id;
        if($v_survey_id > 0)
        {
            $stmt ="select * from t_ps_survey where (1=1) And PK_SURVEY=? and FK_WEBSITE = ? ";
            $results =  $this->db->GetRow($stmt,array($v_survey_id,$v_website_id));
        }
        else
        {
            return array();
        }
        if($this->db->ErrorNo() != 0 OR sizeof($results) <=0)
        {
               $this->exec_fail($this->goback_url, 'Đã xảy ra lỗi câu hỏi này không tồn tại xin kiểm tra lại'); 
        }
        return $results; 
    }
    
    function update_survey($params =array(),$v_survey_id =0)
    {
        $v_website_id = $this->website_id;
        //upload survey
        if((int)$v_survey_id == 0)
        {
            //insert survey 
            $stmt = " INSERT INTO t_ps_survey 
                                (
                                C_NAME, 
                                C_BEGIN_DATE, 
                                C_END_DATE, 
                                C_STATUS, 
                                FK_MEMBER,
                                FK_WEBSITE
                                )
                                VALUES
                                (
                                ?,
                                ?,
                                ?,
                                ?,
                                ?,
                                '$v_website_id'
                                )";
             $this->db->Execute($stmt,$params);
            return $this->db->GetOne('SELECT LAST_INSERT_ID()');
        }
        else
        {
            $stmt = "UPDATE t_ps_survey 
                    SET
                        C_NAME          = ? , 
                        C_BEGIN_DATE    = ? , 
                        C_END_DATE      = ? , 
                        C_STATUS        = ? , 
                        FK_MEMBER           = ?
                        
                    WHERE
                        PK_SURVEY = '$v_survey_id'
                    And FK_WEBSITE = '$v_website_id'
                    ";
            
            $this->db->Execute($stmt,$params);
            return $v_survey_id;
        }
       
        if($this->db->ErrorNo() != 0)
        {
               $this->exec_fail($this->goback_url, 'Đã xảy ra lõi trong quá trình cập nhật. Xin vui lòng thực hiện lại!'); 
        }
        return 0;
        
    }
     /**
     * Lay thong tin chi tiet mot member theo ID
     * 
     * @param int $v_member_id  Ma cua member can lay ID
     * @return array            Mang  chua thong tin chi tiet member theo id 
     */
    public function qry_single_member()
    {
        $stmt = "Select * From t_ps_member ";
        $results = $this->db->GetRow($stmt);
        
        if($this->db->ErrorNo() ==0 )
        {
            return $results;
        }
        $this->exec_fail($this->goback_url,'Đã xảy ra lỗi. Xin vui lòng thử lại!');
    }
    
    public function qry_all_member()
    {
        $sql = "SELECT
                    PK_MEMBER,
                    C_NAME
                  FROM t_ps_member
                  WHERE FK_MEMBER < 1
                       OR FK_MEMBER IS null";
        $MODEL_DATA['arr_all_district'] = $this->db->getAll($sql);
        
        $sql = "SELECT
                    PK_MEMBER,
                    C_NAME,
                    FK_MEMBER
                  FROM t_ps_member
                  WHERE FK_MEMBER > 0";
        
        $MODEL_DATA['arr_all_village'] = $this->db->getAll($sql);
        
        if($this->db->ErrorNo() != 0)
        {
               $this->exec_fail($this->goback_url, 'Đã xảy ra lõi trong quá trình cập nhật. Xin vui lòng thực hiện lại!'); 
        }
        return $MODEL_DATA;
    }
    
    public function qry_delete_survey($v_list_survey_id = '')
    {
        if(trim($v_list_survey_id) != '')
        {
              $stmt = "DELETE FROM t_ps_survey 
                    WHERE
                    PK_SURVEY in($v_list_survey_id)";
              $this->db->Execute($stmt);
        }
        if($this->db->ErrorNo() != 0)
        {
               $this->exec_fail($this->goback_url, 'Đã xảy ra lõi trong quá trình cập nhật. Xin vui lòng thực hiện lại!'); 
        }
        $this->exec_done($this->goback_url);
    }
    
    public  function get_all_question_answer($v_survey_id = 0)
    {
        $stmt = "SELECT
                        sq.*,
                        (SELECT
                           CONCAT('<data>', 
                                    GROUP_CONCAT('<row', 
                                                CONCAT(' PK_SURVEY_ANSWER=\"', PK_SURVEY_ANSWER,'\"') ,
                                                CONCAT(' C_TOTAL_VOTE=\"',(SELECT SUM(C_RESULT) FROM t_ps_survey_answer WHERE FK_SURVEY_QUESTION = sa.FK_SURVEY_QUESTION),'\"'),
                                                CONCAT(' C_NAME=\"', C_NAME,'\" ') ,'>', C_RESULT
                                        , '</row>' SEPARATOR ' ') ,
                                '</data>')
                         FROM t_ps_survey_answer sa
                         WHERE sa.FK_SURVEY_QUESTION = sq.PK_SURVEY_QUESTION) AS C_XML_ANSWER
                      FROM t_ps_survey_question sq
                       WHERE sq.FK_SURVEY = ?";
       return  $this->db->GetAll($stmt,array($v_survey_id));
    }
    
    /** 
     * Lay thong tin question theo id
     * @param  int question_id ma cua cau hoi
     */
    public function  qry_single_question($question_id = 0)
    {
       if($question_id >0)
       {
            $sql = " select * from t_ps_survey_question where PK_SURVEY_QUESTION = ? ";
            return $this->db->GetRow($sql,array($question_id));
       }
       return array();
        
    }
}

?>
