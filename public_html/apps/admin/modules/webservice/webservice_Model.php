<?php if (!defined('SERVER_ROOT')) exit('No direct script access allowed');

class webservice_Model extends Model {

    function __construct()
    {
        parent::__construct();
    }
    #PhamHuong[30-7-2015]
    # Chuc nang can bo mot cua sao chep noi dung HDTTHC
    public function qry_all_fields()
    {
        $sql = "SELECT *
                FROM t_cores_list
                WHERE FK_LISTTYPE = (SELECT
                                       PK_LISTTYPE
                                     FROM t_cores_listtype
                                     WHERE C_CODE = '". _CONST_LINH_VUC_TTHC ."'
                                         AND C_STATUS = 1)
                    AND C_STATUS = 1";
        $response =   $this->db->GetAll($sql);
        return $this->db->ErrorNo() == 0 ? $response : FALSE;
    }
    
    
    public function qry_all_record_type($v_string_serch = '',$v_fields_code = '',$v_rows_per_page,$sel_goto_page)
    {
        $v_start = $v_rows_per_page * ($sel_goto_page - 1);
        
        $conditon = '';
        if($v_string_serch != '')
        {
            $conditon .= " And (C_CODE like '%$v_string_serch%' OR C_NAME  like '%$v_string_serch%')";
        }
        if($v_fields_code != '')
        {
            $conditon .= " And C_SPEC_CODE = '$v_fields_code'";
        }
        
        $sql = "SELECT @rownum := @rownum + 1 AS RN,PK_RECORD_TYPE,C_CODE,C_NAME,C_SCOPE
                        FROM t_ps_record_type,(SELECT @rownum := $v_start) r
                        WHERE C_STATUS = 1
                        $conditon Limit $v_start,$v_rows_per_page";
        
//        echo '<hr/><div style="color:red">' . __FILE__;
//        var_dump::display($sql);
//        echo '<hr/></div>' . __LINE__;
//        exit();
        
        $response =   $this->db->GetAll($sql);
        return $this->db->ErrorNo() == 0 ? $response : FALSE;
    }
    public function qry_single_record_type($record_type_id)
    {
        $sql = "SELECT C_XML_DATA
                FROM t_ps_record_type
                WHERE C_STATUS = 1
                    AND PK_RECORD_TYPE = ?";
        $response =   $this->db->GetOne($sql,array($record_type_id));
        return $this->db->ErrorNo() == 0 ? $response : FALSE;
    }
    
    
    #End Chuc nang can bo mot cua sao chep noi dung HDTTHC
    
    
    
    
    
    public function get_data_of_member()
    {
         $sql = "SELECT
                    PK_MEMBER,
                    C_NAME
                  FROM t_ps_member
                  WHERE (FK_MEMBER < 1
                       OR FK_MEMBER IS null) AND C_STATUS = 1";
        $arr_all_district = $this->db->getAll($sql);
        
        $sql = "SELECT
                    PK_MEMBER,
                    C_NAME,
                    FK_MEMBER
                  FROM t_ps_member
                  WHERE FK_MEMBER > 0 AND C_STATUS = 1";
        $arr_all_village = $this->db->getAll($sql);
        
        //tao array return
        $arr_return = array();
        foreach($arr_all_district as $arr_district)
        {
            $v_name = $arr_district['C_NAME'];
            $v_id   = $arr_district['PK_MEMBER'];
            array_push($arr_return, array('C_CODE'=>$v_id,'C_NAME'=>$v_name));
                    
            foreach($arr_all_village as $key => $arr_village)
            {
               $v_village_name = $arr_village['C_NAME'];
               $v_village_id   = $arr_village['PK_MEMBER'];
               $v_parent_id    = $arr_village['FK_MEMBER'];
               if($v_parent_id != $v_id)
               {
                   continue;
               }
               array_push($arr_return, array('C_CODE'=>$v_village_id,'C_NAME'=>'--- '.$v_village_name));
               unset($arr_all_village[$key]);
            }
        }
        return $arr_return;
    }
}