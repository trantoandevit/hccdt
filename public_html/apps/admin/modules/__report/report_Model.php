<?php

class report_Model extends Model
{
    function __construct() {
        parent::__construct();
    }
    
    public function qry_all_member($arr_filter = array())
    {
        $district =  '';
        if(sizeof($arr_filter) > 0)
        {
            $district   =  isset($arr_filter['district']) ? $arr_filter['district'] : '';
        }
        $v_condition_= $v_condition = '';
        if(intval($district) >0)
        {
            $v_condition = " And PK_MEMBER ='". trim($district) ."'";
            $v_condition_ = " And ( PK_MEMBER ='". trim($district) ."' OR FK_MEMBER = '". trim($district) ."')";
        }
       
        $sql = "SELECT
                    PK_MEMBER,
                    C_NAME
                  FROM t_ps_member
                  WHERE FK_MEMBER < 1
                        $v_condition
                       OR FK_MEMBER IS null";
        $MODEL_DATA['arr_all_district'] = $this->db->getAll($sql);
        
        $sql = "SELECT
                    PK_MEMBER,
                    C_NAME,
                    FK_MEMBER
                  FROM t_ps_member
                  WHERE FK_MEMBER > 0 
                  $v_condition_
                    ";
        
        $MODEL_DATA['arr_all_village'] = $this->db->getAll($sql);
        
        if($this->db->ErrorNo() != 0)
        {
            exit();
               $this->exec_fail($this->goback_url, 'Đã xảy ra lõi trong quá trình cập nhật. Xin vui lòng thực hiện lại!'); 
        }
        
        return $MODEL_DATA;
    }

    public function qry_single_evaluation($arr_filter = array())
    {
        
        $v_end_date = $v_begin_date = '';
        if(sizeof($arr_filter) > 0)
        {
            $v_begin_date     = isset($arr_filter['txt_begin_date']) ? $arr_filter['txt_begin_date'] : date('d/m/Y');
            $v_end_date    = isset($arr_filter['txt_end_date']) ? $arr_filter['txt_end_date'] : date('d/m/Y');
        }
        $arr_member = $this->qry_all_member($arr_filter);
        $arr_all_district = $arr_member['arr_all_district'];
        $arr_all_village  = $arr_member['arr_all_village'];
        
        $arr_all_member = array();
        if(sizeof($arr_all_district) >0)
        {
            //TH Lay danh sach don vi theo cap huyen so hoac tat ca
            for($i =0; $i<count($arr_all_district);$i ++)
            {

                $v_member_id      =    $arr_all_district[$i]['PK_MEMBER'];
                $arr_all_member[]  =    $arr_all_district[$i];

                for($j=0;$j<count($arr_all_village);$j ++)
                { 
                    $v_memeber_village_id  = $arr_all_village[$j]['FK_MEMBER'];
                    if($v_member_id == $v_memeber_village_id)
                    {
                        $arr_all_village[$j]['C_NAME'] = ' -- '.$arr_all_village[$j]['C_NAME'];
                        $arr_all_member[] = $arr_all_village[$j];
                    }
                }
            }
        }
        else
        {
            //TH: Chi lay thong tin cap huyen theo dieu kien loc
            $arr_all_member = $arr_all_village;
        }
        $stmt = '';
        $v_condition  = '';
//        14/10/2014
        $v_begin_date = jwDate::ddmmyyyy_to_yyyymmdd($v_begin_date);
        $v_end_date   = jwDate::ddmmyyyy_to_yyyymmdd($v_end_date);
        if($v_begin_date != '')
        {
            $v_condition .= " and date(CONCAT(a.C_YEAR,'-',a.C_MONTH,'-',a.C_DAY)) >= '$v_begin_date' ";
        }
        if($v_end_date != '')
        {
            $v_condition .= " and date(CONCAT(a.C_YEAR,'-',a.C_MONTH,'-',a.C_DAY)) <= '$v_end_date' ";
        }
        $arr_tieu_chi = $this->db->GetAll("SELECT
                                PK_LIST,
                                C_CODE,
                                ExtractValue(C_XML_DATA,'//item[@id=\"txt_code_config\"]/value') AS C_CODE_CONFIG
                              FROM t_cores_list
                              WHERE FK_LISTTYPE = (SELECT
                                                     PK_LISTTYPE
                                                   FROM t_cores_listtype
                                                   WHERE C_CODE = '"._CONST_DM_TIEU_CHI_DANH_GIA."'
                                                       AND C_STATUS = 1)
                                  AND C_STATUS = 1");
        
        $condition_tieuchi =  $condition_diem = '' ;
        $v_qry_tong_diem   = '';
        for($i =0;$i < count($arr_tieu_chi);$i ++)
        {
            $v_list_id      = isset($arr_tieu_chi[$i]['PK_LIST']) ? $arr_tieu_chi[$i]['PK_LIST'] : '';
            $v_code         = isset($arr_tieu_chi[$i]['C_CODE']) ? $arr_tieu_chi[$i]['C_CODE'] : ''; //diem danh gia
            $v_code_config  = isset($arr_tieu_chi[$i]['C_CODE_CONFIG']) ? $arr_tieu_chi[$i]['C_CODE_CONFIG'] : '';
            $v_code         = (intval($v_code) >0) ? $v_code : 0;
            $condition_tieuchi .= " ,(SELECT
                                            IF(SUM(a.C_VOTE)>0,SUM(a.C_VOTE),0)
                                           FROM t_ps_assessment a
                                           WHERE FK_USER = l.PK_LIST
                                           AND a.FK_CRITERIAL = $v_list_id
                                                 $v_condition
                                         ) as $v_code_config"; 
           
            $condition_diem .= ", $v_code AS 'POSTION_$v_code_config' ";
            //tao qery tinh tong diem
            if($i ==0) $v_qry_tong_diem .= " (a.$v_code_config * a.POSTION_$v_code_config) ";
            else $v_qry_tong_diem .= " + (a.$v_code_config * a.POSTION_$v_code_config )";
            
        }
        
        if(trim($v_qry_tong_diem) != '')
        {
            $v_qry_tong_diem = " ,ROUND( (( $v_qry_tong_diem )  / IF(C_VOTE_TONG>0,C_VOTE_TONG,0)),2) AS  TONG_DANH_GIA ";
        }
       
        for($i =0;$i<count($arr_all_member);$i ++)
        {
            $v_member_id = $arr_all_member[$i]['PK_MEMBER'];
            $v_member_name = $arr_all_member[$i]['C_NAME'];
            
            $sql = "SELECT
                            PK_MEMBER ,
                            '$v_member_name' AS C_NAME_MEMBER,
                            PK_LIST,
                            l.C_NAME  AS C_STAFF_NAME
                            $condition_tieuchi
                            $condition_diem
                            ,(SELECT
                                IF(SUM(a.C_VOTE)>0,SUM(a.C_VOTE),0)
                                FROM t_ps_assessment a
                                WHERE FK_USER = l.PK_LIST
                                  AND a.FK_CRITERIAL IN (SELECT
                                                    PK_LIST
                                                  FROM t_cores_list
                                                  WHERE FK_LISTTYPE = (SELECT
                                                                         PK_LISTTYPE
                                                                       FROM t_cores_listtype
                                                                       WHERE C_CODE = '"._CONST_DM_TIEU_CHI_DANH_GIA."'
                                                                           AND C_STATUS = 1))
                                                        $v_condition
                                                                           ) as C_VOTE_TONG
                            FROM t_cores_list l LEFT JOIN 
                            t_ps_member m
                            ON m.PK_MEMBER =$v_member_id
                            WHERE FK_LISTTYPE = (SELECT
                                                 PK_LISTTYPE
                                               FROM t_cores_listtype
                                               WHERE C_CODE = '"._CONST_CAN_BO_DANH_GIA."'
                                                   AND C_STATUS = 1)
                                      AND ExtractValue(l.C_XML_DATA,'//item[@id=\"ddl_member\"]/value') = '$v_member_id'
                                      AND l.C_STATUS = 1";
            
            $sql  =  "SELECT a.* $v_qry_tong_diem
                            from ($sql) a";
            
            if($stmt =='')
            {
                $stmt .= $sql;
            }
            else
            {
                $stmt .= ' union '.$sql;
            }
        }
         $arr_staff = $this->db->GetAll($stmt);
        return $arr_staff;
        
        
        
        
    }
    public function qry_all_evaluation($arr_filter = array())
    {
        $v_year = $v_month = $v_quarter = '';
        if(sizeof($arr_filter) > 0)
        {
            $v_year     = isset($arr_filter['year']) ? $arr_filter['year'] : date('Y');
            $v_month    = isset($arr_filter['month']) ? $arr_filter['month'] : 0;
            $v_quarter  = isset($arr_filter['quarter']) ? $arr_filter['quarter'] : 0;
        }
        
        $arr_member = $this->qry_all_member($arr_filter);
        $arr_all_district = $arr_member['arr_all_district'];
        $arr_all_village  = $arr_member['arr_all_village'];
        
        $arr_all_member = array();
        if(sizeof($arr_all_district) >0)
        {
            //TH Lay danh sach don vi theo cap huyen so hoac tat ca
            for($i =0; $i<count($arr_all_district);$i ++)
            {

                $v_member_id      =    $arr_all_district[$i]['PK_MEMBER'];
                $arr_all_member[]  =    $arr_all_district[$i];

                for($j=0;$j<count($arr_all_village);$j ++)
                { 
                    $v_memeber_village_id  = $arr_all_village[$j]['FK_MEMBER'];
                    if($v_member_id == $v_memeber_village_id)
                    {
                        $arr_all_village[$j]['C_NAME'] = ' -- '.$arr_all_village[$j]['C_NAME'];
                        $arr_all_member[] = $arr_all_village[$j];
                    }
                }
            }
        }
        else
        {
            //TH: Chi lay thong tin cap huyen theo dieu kien loc
            $arr_all_member = $arr_all_village;
        }
        for($i =0;$i<count($arr_all_member);$i ++)
        {
            $v_member_id = $arr_all_member[$i]['PK_MEMBER'];
            
            $v_condition = '';
            $v_condition .= " And a.C_YEAR = '$v_year' ";
            if(intval($v_month) >0)
            {
                $v_condition .= " And  C_MONTH = '$v_month'";
            }
            if(intval($v_quarter) >0)
            {
                if($v_quarter == 1)     $v_condition .= " And  C_MONTH in (1,2,3)";
                elseif($v_quarter == 2) $v_condition .= " And  C_MONTH in(4,5,6) ";
                elseif($v_quarter == 3) $v_condition .= " And  C_MONTH in(7,8,9)";
                elseif($v_quarter == 4) $v_condition .= " And  C_MONTH in(10,11,12)";
            }
            $sql = "SELECT
                    C_CODE_CONFIG,
                    SUM(C_VOTE) as C_VOTE,
                    C_CODE
                 FROM t_ps_assessment a
                   LEFT JOIN (SELECT
                                PK_LIST,
                                C_CODE,
                                ExtractValue(C_XML_DATA,'//item[@id=\"txt_code_config\"]/value') AS C_CODE_CONFIG
                              FROM t_cores_list
                              WHERE FK_LISTTYPE = (SELECT
                                                     PK_LISTTYPE
                                                   FROM t_cores_listtype
                                                   WHERE C_CODE = '"._CONST_DM_TIEU_CHI_DANH_GIA."'
                                                       AND C_STATUS = 1)
                                  AND C_STATUS = 1) l
                     ON l.PK_LIST = a.FK_CRITERIAL
                 WHERE FK_USER IN(SELECT
                                    PK_LIST
                                  FROM t_cores_list
                                  WHERE FK_LISTTYPE = (SELECT
                                                         PK_LISTTYPE
                                                       FROM t_cores_listtype
                                                       WHERE C_CODE = '"._CONST_CAN_BO_DANH_GIA."')
                                      AND ExtractValue(C_XML_DATA,'//item[@id=\"ddl_member\"]/value') = $v_member_id)
                                          $v_condition
                 GROUP BY FK_CRITERIAL";
            $arr_vote = $this->db->GetAll($sql);
            if($this->db->ErrorNo() == 0)
            {
                $v_total_vote       = 0;
                $v_total_position   = 0;
                
                foreach ($arr_vote as $arr_single_vote)
                {
                    $v_code_vote    = $arr_single_vote['C_CODE_CONFIG']; // Ma Phu de phan biet tieu chi danh gia
                    $v_vote         = $arr_single_vote['C_VOTE'];
                    $v_code         = $arr_single_vote['C_CODE']; // diem
                    
                    $arr_all_member[$i][$v_code_vote] = $v_vote;
                    $v_total_vote += $v_vote;
                    if(intval($v_code) >0)
                    {
                        $v_total_position +=  ($v_code * $v_vote);
                    }
                }
                if($v_total_vote >0)
                {
                    $arr_all_member[$i]['TONG_DANH_GIA'] = $v_total_position /$v_total_vote;
                }
                else 
                {
                    $arr_all_member[$i]['TONG_DANH_GIA'] = 0;
                }
                
            }
            else
            {
                exit('Co loi xay ra vui long thuc hien lai');
            }
            
        }        
        return $arr_all_member;
    }
    
    /**
     * Lay danh dach cac linh vuc
     */
    public function qry_all_record_list_type()
    {
        $sql = "SELECT C_NAME,C_CODE,PK_LIST
                        FROM t_cores_list
                        WHERE FK_LISTTYPE = (SELECT
                                               PK_LISTTYPE
                                             FROM t_cores_listtype
                                             WHERE C_CODE = '"._CONST_LINH_VUC_TTHC."'
                                                 AND C_STATUS = 1)
                            AND C_STATUS = 1 ORDER BY C_ORDER";
        return $this->db->GetAll($sql);
    }
    /** Lay tat ca danh sach thu tuc
     */
    public function qry_all_record_type()
    {
        $sql ="SELECT
                    rt.PK_RECORD_TYPE,
                    rt.C_CODE,
                    rt.C_NAME,
                    CONCAT(l.C_CODE,'-',l.PK_LIST) AS C_RECORD_LIST_CODE_ID
                  FROM t_ps_record_type rt
                    LEFT JOIN t_cores_list l
                      ON rt.C_SPEC_CODE = l.C_CODE
                  WHERE rt.C_STATUS = 1
                      AND l.C_STATUS = 1
                  ORDER BY rt.C_ORDER";
        return $this->db->GetAll($sql);
    }
    
    /*
     * Bao cao tong hop tinh hinh giai quyet ho so cho bao cao chi tiet.
     */
    public function qry_single_synthesis_record($arr_filter = array())
    {
        $v_year = $v_month = $v_quarter = $condition_where = $v_where_spec_code ='';
        if(sizeof($arr_filter) > 0)
        {
            $v_year         = isset($arr_filter['year']) ? $arr_filter['year'] : date('Y');
            $v_month        = isset($arr_filter['month']) ? $arr_filter['month'] : 0;
            $v_quarter      = isset($arr_filter['quarter']) ? $arr_filter['quarter'] : 0;
            $v_district     = isset($arr_filter['district']) ? $arr_filter['district'] : 0;
            $v_record_type  = isset($arr_filter['sel_record_list_type']) ? $arr_filter['sel_record_list_type'] : '';
        }
        
        $v_condition = $v_condition_district = '';
        if($v_district >0 )
        {
            $v_condition_district = " AND ( table_hr_tmp.FK_MEMBER = '$v_district' OR table_hr_tmp.PK_MEMBER = '$v_district' )";
//            $v_column   = ;
        }
        $v_condition     .= " AND YEAR(C_HISTORY_DATE) = '$v_year' ";
        if(intval($v_month) >0)
        {
            $v_condition .= " And  MONTH(C_HISTORY_DATE) = '$v_month'";
        }
        if(intval($v_quarter) >0)
        {
            if($v_quarter == 1)   
            {
                $v_condition .= " And  1<= MONTH(C_HISTORY_DATE) and MONTH(C_HISTORY_DATE) <=3 ";
            }
            elseif($v_quarter == 2) 
            {
                $v_condition .= " And  4<= MONTH(C_HISTORY_DATE) and MONTH(C_HISTORY_DATE) <=6 ";
            }
            elseif($v_quarter == 3)
            {
                $v_condition .= " And  7<= MONTH(C_HISTORY_DATE) and MONTH(C_HISTORY_DATE) <=9 " ;
            }
            elseif($v_quarter == 4) 
            {
                $v_condition .= " And  7<= MONTH(C_HISTORY_DATE) and  MONTH(C_HISTORY_DATE) <=9 ";
            }
        }
        
        $condition_where = " ,hs.C_SPEC_CODE ";
        if(isset($v_record_type) && trim($v_record_type) >0)
        {
            $v_where_spec_code = " and l.PK_LIST = '$v_record_type'";
        }
         $stmt = "  SELECT table_hr_tmp.*,L.C_SPEC_NAME,L.C_SPEC_CODE FROM 	
                    (SELECT
                      l.C_NAME AS C_SPEC_NAME,
                      l.C_CODE AS C_SPEC_CODE
                    FROM t_cores_list l
                    WHERE l.FK_LISTTYPE = (SELECT
                                             PK_LISTTYPE
                                           FROM t_cores_listtype
                                           WHERE C_CODE = 'DANH_MUC_LINH_VUC')
                        AND l.C_STATUS = 1
                        $v_where_spec_code ) L 
                        LEFT JOIN 
                    (SELECT
                      IF(HS.FK_VILLAGE_ID>0, CONCAT('--',MD.C_NAME),MD.C_NAME) AS C_NAME,
                      HS.C_UNIT_CODE,
                      HS.C_SPEC_CODE,
                      MD.C_CODE,
                      MD.PK_MEMBER,
                      MD.FK_MEMBER,
                      HS.FK_VILLAGE_ID,
                      IF(
                              COALESCE(HS.C_COUNT_DA_TRA_KET_QUA_TRUOC_HAN,0) 
                            + COALESCE(HS.C_COUNT_DA_TRA_KET_QUA_DUNG_HAN,0) 
                            + COALESCE(HS.C_COUNT_DA_TRA_KET_QUA_QUA_HAN,0)
                            + COALESCE(HS.C_COUNT_DANG_CHO_TRA_KET_QUA,0)

                            + COALESCE(HS.C_COUNT_DANG_THU_LY_DUNG_TIEN_DO,0)
                            + COALESCE(HS.C_COUNT_DANG_THU_LY_CHAM_TIEN_DO,0)
                            + COALESCE(HS.C_COUNT_THU_LY_QUA_HAN,0)

                            + COALESCE(HS.C_COUNT_CONG_DAN_RUT,0)
                            + COALESCE(HS.C_COUNT_TU_CHOI,0)
                            + COALESCE(HS.C_COUNT_BO_SUNG,0)
                            + COALESCE(HS.C_COUNT_THUE,0)			
                            - COALESCE(HS.C_COUNT_TONG_TIEP_NHAN_TRONG_THANG,0)>0,  
                                    COALESCE(HS.C_COUNT_DA_TRA_KET_QUA_TRUOC_HAN,0) 
                                    + COALESCE(HS.C_COUNT_DA_TRA_KET_QUA_DUNG_HAN,0) 
                                    + COALESCE(HS.C_COUNT_DA_TRA_KET_QUA_QUA_HAN,0)
                                    + COALESCE(HS.C_COUNT_DANG_CHO_TRA_KET_QUA,0)

                                    + COALESCE(HS.C_COUNT_DANG_THU_LY_DUNG_TIEN_DO,0)
                                    + COALESCE(HS.C_COUNT_DANG_THU_LY_CHAM_TIEN_DO,0)
                                    + COALESCE(HS.C_COUNT_THU_LY_QUA_HAN,0)

                                    + COALESCE(HS.C_COUNT_CONG_DAN_RUT,0)
                                    + COALESCE(HS.C_COUNT_TU_CHOI,0)
                                    + COALESCE(HS.C_COUNT_BO_SUNG,0)
                                    + COALESCE(HS.C_COUNT_THUE,0)			
                                    - COALESCE(HS.C_COUNT_TONG_TIEP_NHAN_TRONG_THANG,0)
                                    ,0) 
                      AS C_COUNT_KY_TRUOC,

                      COALESCE(HS.C_COUNT_TONG_TIEP_NHAN,0) AS C_COUNT_TONG_TIEP_NHAN,
                      COALESCE(HS.C_COUNT_TONG_TIEP_NHAN_TRONG_THANG,0) AS C_COUNT_TONG_TIEP_NHAN_TRONG_THANG,
                      COALESCE(HS.C_COUNT_DANG_THU_LY,0) AS C_COUNT_DANG_THU_LY,

                      COALESCE(HS.C_COUNT_DANG_THU_LY_DUNG_TIEN_DO,0) AS C_COUNT_DANG_THU_LY_DUNG_TIEN_DO,
                      COALESCE(HS.C_COUNT_DANG_THU_LY_CHAM_TIEN_DO,0) AS C_COUNT_DANG_THU_LY_CHAM_TIEN_DO,
                      COALESCE(HS.C_COUNT_THU_LY_QUA_HAN,0) AS C_COUNT_THU_LY_QUA_HAN,

                      COALESCE(HS.C_COUNT_DANG_CHO_TRA_KET_QUA,0) AS C_COUNT_DANG_CHO_TRA_KET_QUA,
                      COALESCE(HS.C_COUNT_DA_TRA_KET_QUA_TRUOC_HAN,0) AS C_COUNT_DA_TRA_KET_QUA_TRUOC_HAN,
                      COALESCE(HS.C_COUNT_DA_TRA_KET_QUA_DUNG_HAN,0) AS C_COUNT_DA_TRA_KET_QUA_DUNG_HAN,
                      COALESCE(HS.C_COUNT_DA_TRA_KET_QUA_QUA_HAN,0) AS C_COUNT_DA_TRA_KET_QUA_QUA_HAN,
                      (COALESCE(    HS.C_COUNT_DA_TRA_KET_QUA_DUNG_HAN,0) 
                                +   COALESCE(HS.C_COUNT_DA_TRA_KET_QUA_TRUOC_HAN,0) 
                                +   COALESCE(HS.C_COUNT_DA_TRA_KET_QUA_QUA_HAN,0)
                                ) AS C_COUNT_DA_TRA,

                      COALESCE(HS.C_COUNT_CONG_DAN_RUT,0) AS C_COUNT_CONG_DAN_RUT,
                      COALESCE(HS.C_COUNT_TU_CHOI,0) AS C_COUNT_TU_CHOI,
                      COALESCE(HS.C_COUNT_BO_SUNG,0) AS C_COUNT_BO_SUNG,  
                      COALESCE(HS.C_COUNT_THUE,0) AS C_COUNT_THUE,
                      COALESCE(ROUND(
                                    (
                                            (		COALESCE(HS.C_COUNT_DA_TRA_KET_QUA_DUNG_HAN,0) 
                                                    + 	COALESCE(HS.C_COUNT_DA_TRA_KET_QUA_TRUOC_HAN,0)
                                            ) 
                                            / 
                                            (
                                                            COALESCE(HS.C_COUNT_DA_TRA_KET_QUA_DUNG_HAN,0) 
                                                    + 	COALESCE(HS.C_COUNT_DA_TRA_KET_QUA_TRUOC_HAN,0) 
                                                    +	COALESCE(HS.C_COUNT_DA_TRA_KET_QUA_QUA_HAN,0))
                                            )
                                             * 100
                                    ),0)
                                    AS C_COUNT_TY_LE

                    FROM t_ps_record_history_stat HS
                      RIGHT JOIN (
                                    SELECT
                                m.PK_MEMBER,
                                m.C_NAME,
                                m.C_CODE,
                                m.C_SCOPE,
                                COALESCE(m.FK_VILLAGE_ID,0) AS FK_VILLAGE_ID,
                                m.FK_MEMBER,
                                tmp_member.C_MAX_DATE
                              FROM t_ps_member m
                                LEFT JOIN (SELECT
                                             M.*,
                                             MAX(RH.C_HISTORY_DATE) AS C_MAX_DATE
                                           FROM (SELECT
                                                   MD.PK_MEMBER,
                                                   MD.C_CODE,
                                                   MD.FK_MEMBER,
                                                   COALESCE(MD.FK_VILLAGE_ID,0) AS FK_VILLAGE_ID
                                                 FROM t_ps_member MD
                                                 WHERE MD.C_STATUS = 1
                                                 ORDER BY MD.C_SHORT_CODE, MD.PK_MEMBER, MD.FK_VILLAGE_ID) M
                                             LEFT JOIN t_ps_record_history_stat RH
                                               ON (M.C_CODE = RH.C_UNIT_CODE
                                                   AND M.FK_VILLAGE_ID = RH.FK_VILLAGE_ID
                                                   AND M.FK_VILLAGE_ID = 0)
                                                  OR (M.FK_VILLAGE_ID = RH.FK_VILLAGE_ID
                                                      AND M.FK_VILLAGE_ID != 0
                                                      AND RH.C_UNIT_CODE = (SELECT
                                                                              C_CODE
                                                                            FROM t_ps_member
                                                                            WHERE PK_MEMBER = M.FK_MEMBER))
                                           WHERE (1 = 1) $v_condition                                               
                                           GROUP BY M.C_CODE) tmp_member
                                        ON tmp_member.PK_MEMBER = m.PK_MEMBER
                                    AND m.C_STATUS = 1	
                                  ) MD
                                  ON (MD.C_CODE = HS.C_UNIT_CODE
                                          AND MD.FK_VILLAGE_ID = HS.FK_VILLAGE_ID
                                          AND MD.FK_VILLAGE_ID = 0)
                                         OR (MD.FK_VILLAGE_ID = HS.FK_VILLAGE_ID
                                             AND MD.FK_VILLAGE_ID != 0 
                                            AND HS.C_UNIT_CODE = (SELECT C_CODE FROM t_ps_member WHERE PK_MEMBER = MD.FK_MEMBER)	
                                             )
                                       AND HS.C_HISTORY_DATE = MD.C_MAX_DATE
                             ORDER BY HS.C_UNIT_CODE,HS.FK_VILLAGE_ID
                    ) table_hr_tmp
                    ON table_hr_tmp.C_SPEC_CODE = L.C_SPEC_CODE
                    where 1=1 $v_condition_district
                    ORDER BY PK_MEMBER,C_UNIT_CODE,FK_VILLAGE_ID
                  ";
        return $this->db->GetAll($stmt);
    }
    
    /*
     * Bao cao tong hop tinh hinh giai quyet ho so cho bao cao tong hop.
     */
    public function qry_all_synthesis_record($arr_filter = array())
    {
        $v_year = $v_month = $v_quarter = $condition_where = $v_where_spec_code ='';
        if(sizeof($arr_filter) > 0)
        {
            $v_year         = isset($arr_filter['year']) ? $arr_filter['year'] : date('Y');
            $v_month        = isset($arr_filter['month']) ? $arr_filter['month'] : 0;
            $v_quarter      = isset($arr_filter['quarter']) ? $arr_filter['quarter'] : 0;
            $v_district     = isset($arr_filter['district']) ? $arr_filter['district'] : 0;
        }
       
        $v_condition = $v_condition_district = '';
        if($v_district >0 )
        {
            $v_condition_district = " And (RH.FK_MEMBER = '$v_district' OR RH.PK_MEMBER = '$v_district') ";
        }
        $v_condition     .= " AND YEAR(C_HISTORY_DATE) = '$v_year' ";
        if(intval($v_month) >0)
        {
            $v_condition        .= " And  MONTH(C_HISTORY_DATE) = '$v_month'";
        }
        if(intval($v_quarter) >0)
        {
            if($v_quarter == 1)   
            {
                $v_condition .= " And  1<= MONTH(C_HISTORY_DATE) and MONTH(C_HISTORY_DATE) <=3 ";
            }
            elseif($v_quarter == 2) 
            {
                $v_condition .= " And  4<= MONTH(C_HISTORY_DATE) and MONTH(C_HISTORY_DATE) <=6 ";
            }
            elseif($v_quarter == 3)
            {
                $v_condition .= " And  7<= MONTH(C_HISTORY_DATE) and MONTH(C_HISTORY_DATE) <=9 " ;
            }
            elseif($v_quarter == 4) 
            {
                $v_condition .= " And  10<= MONTH(C_HISTORY_DATE) and  MONTH(C_HISTORY_DATE) <=12 ";
            }
        }
        $stmt = "SELECT 
                        RH.C_NAME,
                        RH.C_UNIT_CODE,
                        RH.FK_VILLAGE_ID,
                        RH.C_CODE,
                        RH.PK_MEMBER,
--                        SUM(RH.C_COUNT_KY_TRUOC) AS C_COUNT_KY_TRUOC,
                         IF(
                                SUM(RH.C_COUNT_DA_TRA_KET_QUA_TRUOC_HAN) 
                              + SUM(RH.C_COUNT_DA_TRA_KET_QUA_DUNG_HAN) 
                              + SUM(RH.C_COUNT_DA_TRA_KET_QUA_QUA_HAN)
                              + SUM(RH.C_COUNT_DANG_CHO_TRA_KET_QUA)

                              + SUM(RH.C_COUNT_DANG_THU_LY_DUNG_TIEN_DO)
                              + SUM(RH.C_COUNT_DANG_THU_LY_CHAM_TIEN_DO)
                              + SUM(RH.C_COUNT_THU_LY_QUA_HAN)

                              + SUM(RH.C_COUNT_CONG_DAN_RUT)
                              + SUM(RH.C_COUNT_TU_CHOI)
                              + SUM(RH.C_COUNT_BO_SUNG)
                              + SUM(RH.C_COUNT_THUE)			
                              - SUM(RH.C_COUNT_TONG_TIEP_NHAN_TRONG_THANG)>0,
                                      SUM(RH.C_COUNT_DA_TRA_KET_QUA_TRUOC_HAN) 
                                      + SUM(RH.C_COUNT_DA_TRA_KET_QUA_DUNG_HAN) 
                                      + SUM(RH.C_COUNT_DA_TRA_KET_QUA_QUA_HAN)
                                      + SUM(RH.C_COUNT_DANG_CHO_TRA_KET_QUA)

                                      + SUM(RH.C_COUNT_DANG_THU_LY_DUNG_TIEN_DO)
                                      + SUM(RH.C_COUNT_DANG_THU_LY_CHAM_TIEN_DO)
                                      + SUM(RH.C_COUNT_THU_LY_QUA_HAN)

                                      + SUM(RH.C_COUNT_CONG_DAN_RUT)
                                      + SUM(RH.C_COUNT_TU_CHOI)
                                      + SUM(RH.C_COUNT_BO_SUNG)
                                      + SUM(RH.C_COUNT_THUE)		
                                      - SUM(RH.C_COUNT_TONG_TIEP_NHAN_TRONG_THANG)
                                      ,0)
                        AS C_COUNT_KY_TRUOC,
                        SUM(RH.C_COUNT_TONG_TIEP_NHAN) AS C_COUNT_TONG_TIEP_NHAN,
                        SUM(RH.C_COUNT_TONG_TIEP_NHAN_TRONG_THANG) AS C_COUNT_TONG_TIEP_NHAN_TRONG_THANG,
                        SUM(RH.C_COUNT_DANG_THU_LY) AS C_COUNT_DANG_THU_LY,
                        SUM(RH.C_COUNT_DANG_THU_LY_DUNG_TIEN_DO) AS C_COUNT_DANG_THU_LY_DUNG_TIEN_DO,
                        SUM(RH.C_COUNT_DANG_THU_LY_CHAM_TIEN_DO) AS C_COUNT_DANG_THU_LY_CHAM_TIEN_DO,
                        SUM(RH.C_COUNT_DANG_CHO_TRA_KET_QUA) AS C_COUNT_DANG_CHO_TRA_KET_QUA,
                        SUM(RH.C_COUNT_THU_LY_QUA_HAN) as C_COUNT_THU_LY_QUA_HAN,
                        
                        SUM(RH.C_COUNT_DA_TRA_KET_QUA_TRUOC_HAN) AS C_COUNT_DA_TRA_KET_QUA_TRUOC_HAN,
                        SUM(RH.C_COUNT_DA_TRA_KET_QUA_DUNG_HAN) AS C_COUNT_DA_TRA_KET_QUA_DUNG_HAN,
                        SUM(RH.C_COUNT_DA_TRA_KET_QUA_QUA_HAN) AS C_COUNT_DA_TRA_KET_QUA_QUA_HAN,
                        SUM(RH.C_COUNT_DA_TRA) AS C_COUNT_DA_TRA,
                        SUM(RH.C_COUNT_CONG_DAN_RUT) AS C_COUNT_CONG_DAN_RUT,
                        SUM(RH.C_COUNT_TU_CHOI) AS C_COUNT_TU_CHOI,
                        SUM(RH.C_COUNT_THUE) AS C_COUNT_THUE,
                        COALESCE(ROUND(
                                (
                                        (		SUM(COALESCE(RH.C_COUNT_DA_TRA_KET_QUA_DUNG_HAN,0))
                                                + 	SUM(COALESCE(RH.C_COUNT_DA_TRA_KET_QUA_TRUOC_HAN,0))
                                        ) 
                                        / 
                                        (
                                                        SUM(COALESCE(RH.C_COUNT_DA_TRA_KET_QUA_DUNG_HAN,0) )
                                                + 	SUM(COALESCE(RH.C_COUNT_DA_TRA_KET_QUA_TRUOC_HAN,0) )
                                                +	SUM(COALESCE(RH.C_COUNT_DA_TRA_KET_QUA_QUA_HAN,0)))
                                        )
                                         * 100
                                ),0)
                                AS C_COUNT_TY_LE 
                 FROM (SELECT
                  IF(HS.FK_VILLAGE_ID>0, CONCAT('--',MD.C_NAME),MD.C_NAME) AS C_NAME,
                  HS.C_UNIT_CODE,
                  HS.C_SPEC_CODE,
                  MD.C_CODE,
                  MD.PK_MEMBER,
                  MD.FK_MEMBER,
                  HS.FK_VILLAGE_ID,              

                  COALESCE(HS.C_COUNT_TONG_TIEP_NHAN,0) AS C_COUNT_TONG_TIEP_NHAN,
                  COALESCE(HS.C_COUNT_TONG_TIEP_NHAN_TRONG_THANG,0) AS C_COUNT_TONG_TIEP_NHAN_TRONG_THANG,
                  COALESCE(HS.C_COUNT_DANG_THU_LY,0) AS C_COUNT_DANG_THU_LY,

                  COALESCE(HS.C_COUNT_DANG_THU_LY_DUNG_TIEN_DO,0) AS C_COUNT_DANG_THU_LY_DUNG_TIEN_DO,
                  COALESCE(HS.C_COUNT_DANG_THU_LY_CHAM_TIEN_DO,0) AS C_COUNT_DANG_THU_LY_CHAM_TIEN_DO,
                  COALESCE(HS.C_COUNT_THU_LY_QUA_HAN,0) AS C_COUNT_THU_LY_QUA_HAN,

                  COALESCE(HS.C_COUNT_DANG_CHO_TRA_KET_QUA,0) AS C_COUNT_DANG_CHO_TRA_KET_QUA,
                  COALESCE(HS.C_COUNT_DA_TRA_KET_QUA_TRUOC_HAN,0) AS C_COUNT_DA_TRA_KET_QUA_TRUOC_HAN,
                  COALESCE(HS.C_COUNT_DA_TRA_KET_QUA_DUNG_HAN,0) AS C_COUNT_DA_TRA_KET_QUA_DUNG_HAN,
                  COALESCE(HS.C_COUNT_DA_TRA_KET_QUA_QUA_HAN,0) AS C_COUNT_DA_TRA_KET_QUA_QUA_HAN,
                  (COALESCE(HS.C_COUNT_DA_TRA_KET_QUA_DUNG_HAN,0) + COALESCE(HS.C_COUNT_DA_TRA_KET_QUA_TRUOC_HAN,0) +COALESCE(HS.C_COUNT_DA_TRA_KET_QUA_QUA_HAN,0)) AS C_COUNT_DA_TRA,

                  COALESCE(HS.C_COUNT_CONG_DAN_RUT,0) AS C_COUNT_CONG_DAN_RUT,
                  COALESCE(HS.C_COUNT_TU_CHOI,0) AS C_COUNT_TU_CHOI,
                  COALESCE(HS.C_COUNT_BO_SUNG,0) AS C_COUNT_BO_SUNG,  
                  COALESCE(HS.C_COUNT_THUE,0) AS C_COUNT_THUE                     
                FROM t_ps_record_history_stat HS
                  RIGHT JOIN (
                                SELECT
                            m.PK_MEMBER,
                            m.C_NAME,
                            m.C_CODE,
                            m.C_SCOPE,
                            COALESCE(m.FK_VILLAGE_ID,0) AS FK_VILLAGE_ID,
                            m.FK_MEMBER,
                            tmp_member.C_MAX_DATE
                          FROM t_ps_member m
                            LEFT JOIN (SELECT
                                         M.*,
                                         MAX(RH.C_HISTORY_DATE) AS C_MAX_DATE
                                       FROM (SELECT
                                               m.PK_MEMBER,
                                               m.C_CODE,
                                               m.FK_MEMBER,
                                               COALESCE(m.FK_VILLAGE_ID,0) AS FK_VILLAGE_ID
                                             FROM t_ps_member m
                                             WHERE m.C_STATUS = 1
                                             ORDER BY m.C_SHORT_CODE, m.PK_MEMBER, m.FK_VILLAGE_ID) M
                                         LEFT JOIN t_ps_record_history_stat RH
                                           ON (M.C_CODE = RH.C_UNIT_CODE
                                               AND M.FK_VILLAGE_ID = RH.FK_VILLAGE_ID
                                               AND M.FK_VILLAGE_ID = 0)
                                              OR (M.FK_VILLAGE_ID = RH.FK_VILLAGE_ID
                                                  AND M.FK_VILLAGE_ID != 0
                                                  AND RH.C_UNIT_CODE = (SELECT
                                                                          C_CODE
                                                                        FROM t_ps_member
                                                                        WHERE PK_MEMBER = M.FK_MEMBER))
                                       WHERE (1 = 1)  $v_condition
                                       GROUP BY M.C_CODE) tmp_member
                              ON tmp_member.PK_MEMBER = m.PK_MEMBER
                                AND m.C_STATUS = 1
                              ) MD
                              ON (MD.C_CODE = HS.C_UNIT_CODE
                                      AND MD.FK_VILLAGE_ID = HS.FK_VILLAGE_ID
                                      AND MD.FK_VILLAGE_ID = 0)
                                     OR (MD.FK_VILLAGE_ID = HS.FK_VILLAGE_ID
                                         AND MD.FK_VILLAGE_ID != 0 
                                        AND HS.C_UNIT_CODE = (SELECT C_CODE FROM t_ps_member WHERE PK_MEMBER = MD.FK_MEMBER)	
                                         )
                                   AND HS.C_HISTORY_DATE = MD.C_MAX_DATE
                         ORDER BY PK_MEMBER,HS.C_UNIT_CODE,HS.FK_VILLAGE_ID,MD.C_NAME
                ) RH
                where (1 = 1) $v_condition_district
                GROUP BY RH.C_CODE 
                ORDER BY RH.C_UNIT_CODE,RH.FK_VILLAGE_ID";
        
        return $this->db->GetAll($stmt);
    }
    
    public function qry_all_year()
    {
        $stmt = 'SELECT
                    YEAR(C_HISTORY_DATE) as C_YEAR
                  FROM t_ps_record_history_stat GROUP BY YEAR(C_HISTORY_DATE)';
        return $this->db->GetAll($stmt);
    }
    
    public function qry_all_year_evaluation()
    {
        $stmt = " SELECT C_YEAR FROM t_ps_assessment GROUP BY C_YEAR ";
        return $this->db->GetAll($stmt);
    }
}
?>

