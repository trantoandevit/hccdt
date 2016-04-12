<?php if (!defined('SERVER_ROOT')) exit('No direct script access allowed');?>
<?php

class xlist_Model extends Model {

    function __construct() {
        parent::__construct();
    }

    //Listtype
    public function check_existing_listtype_code($code,$listtype_id){
        $code = replace_bad_char($code);
        $listtype_id = replace_bad_char($listtype_id);
        $sql = "Select count(*) count from t_cores_listtype Where (PK_LISTTYPE <> $listtype_id) And (C_CODE='$code')";
        $this->db->debug=0;
        return json_encode($this->db->getRow($sql));
    }

    public function check_existing_listtype_name($name,$listtype_id){
        $name = replace_bad_char($name);
        $listtype_id = replace_bad_char($listtype_id);
        $sql = "Select count(*) count from t_cores_listtype Where (PK_LISTTYPE <> $listtype_id) And (C_NAME='$name')";
        $this->db->debug=0;
        return json_encode($this->db->getRow($sql));
    }

    public function qry_all_listtype() {

        //Luu dieu kien loc
        $v_filter = isset($_POST['txt_filter']) ? replace_bad_char($_POST['txt_filter']) : '';
        $v_page = isset($_POST['sel_goto_page']) ? replace_bad_char($_POST['sel_goto_page']) : 1;
        $v_rows_per_page = isset($_POST['sel_rows_per_page']) ? replace_bad_char($_POST['sel_rows_per_page']) : _CONST_DEFAULT_ROWS_PER_PAGE;

        $v_start = $v_rows_per_page * ($v_page - 1);
        $v_end = $v_start + $v_rows_per_page;

        $condition_query = '';
        if ($v_filter !== '') {
            $condition_query = " And LT.C_NAME Like '%$v_filter%'";
        }

        //Dem tong ban ghi
        $sql_count_record = "Select Count(*) total_record From t_cores_listtype LT Where (1 > 0) $condition_query";

        //Ket qua theo dieu kien loc
        if(DATABASE_TYPE == 'MSSQL')
        {
            $sql = "Select LT.PK_LISTTYPE, LT.C_CODE, LT.C_NAME, LT.C_ORDER,LT.C_STATUS, ($sql_count_record) TOTAL_RECORD, ROW_NUMBER() OVER (ORDER BY LT.C_ORDER ASC) RN FROM t_cores_listtype LT Where (1 > 0) $condition_query";
            $stmt = "Select * From ($sql) a Where a.rn>=$v_start And a.rn<=$v_end";
        }
        elseif(DATABASE_TYPE == 'MYSQL')
        {
            $v_start = $v_start;
            $v_limit = $v_end - $v_start;
            
            $stmt = "Select LT.PK_LISTTYPE, 
                            LT.C_CODE, 
                            LT.C_NAME, 
                            LT.C_ORDER,
                            LT.C_STATUS, 
                           ($sql_count_record) AS TOTAL_RECORD 
                    FROM t_cores_listtype LT Where (1 > 0) $condition_query ORDER BY LT.C_ORDER ASC limit $v_start,$v_limit";
        }

        //return
        return $this->db->getAll($stmt);
    }

    public function qry_single_listtype() {
        //Luu dieu kien loc
        $v_filter = isset($_POST['hdn_filter']) ? replace_bad_char($_POST['hdn_filter']) : '';

        $v_listtype_id = isset($_POST['hdn_item_id']) ? replace_bad_char($_POST['hdn_item_id']) : '0';
        if ($v_listtype_id > 0) {
            $stmt = 'Select PK_LISTTYPE, C_CODE, C_NAME, C_OWNER_CODE_LIST, C_XML_FILE_NAME, C_ORDER, C_STATUS From t_cores_listtype Where PK_LISTTYPE=?';
            $arr_params = array('listtype_id' => $v_listtype_id);
            return $this->db->GetRow($stmt, $arr_params);
        } else {
            return array('C_ORDER' => $this->get_max('t_cores_listtype', 'C_ORDER'));
        }
    }

    public function update_listtype() {
        if (!isset($_POST))
        {
            $this->exec_done($this->goback_url);
        }

        $v_listtype_id      = replace_bad_char($_POST['hdn_item_id']);
        $v_listtype_code    = trim(replace_bad_char($_POST['txt_code']));
        $v_listtype_name    = replace_bad_char($_POST['txt_name']);
        $v_xml_file_name    = replace_bad_char($_POST['txt_xml_file_name']);
        $v_order            = replace_bad_char($_POST['txt_order']);

        $v_status = isset($_POST['chk_status']) ? 1 : 0;
        $v_save_and_addnew = isset($_POST['chk_save_and_addnew']) ? 1 : 0;
        //check trung ma
        $sql = "SELECT
                    COUNT(PK_LISTTYPE)
                  FROM t_cores_listtype
                  WHERE C_CODE = '$v_listtype_code'";
        if($v_listtype_id > 0)
        {
            $sql .= " AND PK_LISTTYPE <> $v_listtype_id";
        }
        $check = $this->db->getOne($sql);
        if($check > 0)
        {
            $this->exec_fail($this->goback_url,__('duplicate code list'));
            return;
        }
        
        if ($v_listtype_id > 0) {
            //Update
            $sql = "Update t_cores_listtype Set
                        C_CODE='$v_listtype_code'
                        ,C_NAME='$v_listtype_name'
                        ,C_OWNER_CODE_LIST=''
                        ,C_XML_FILE_NAME='$v_xml_file_name'
                        ,C_ORDER=$v_order
                        ,C_STATUS=$v_status
                    Where PK_LISTTYPE=$v_listtype_id";
        } else {
            //Insert
            $sql = "Insert Into t_cores_listtype (
                                C_CODE
                                ,C_NAME
                                ,C_OWNER_CODE_LIST
                                ,C_XML_FILE_NAME
                                ,C_ORDER
                                ,C_STATUS
                    ) Values (
                                '$v_listtype_code'
                                ,'$v_listtype_name'
                                ,''
                                ,'$v_xml_file_name'
                                ,$v_order
                                ,$v_status
                             )";
            $v_listtype_id = $this->db->getOne("SELECT IDENT_CURRENT('t_cores_listtype')");
        }
        $this->db->Execute($sql);
        $this->ReOrder('t_cores_listtype','PK_LISTTYPE','C_ORDER', $v_listtype_id, $v_order);

        //Luu dieu kien loc
        $v_filter = isset($_POST['txt_filter']) ? replace_bad_char($_POST['txt_filter']) : '';
        $v_page = isset($_POST['sel_goto_page']) ? replace_bad_char($_POST['sel_goto_page']) : 1;
        $v_rows_per_page = isset($_POST['sel_rows_per_page']) ? replace_bad_char($_POST['sel_rows_per_page']) : _CONST_DEFAULT_ROWS_PER_PAGE;
        $arr_filter = array(
                            'txt_filter'   => $v_filter,
                            'sel_goto_page'   => $v_page,
                            'sel_rows_per_page'   => $v_rows_per_page
                        );

        if ($v_save_and_addnew > 0) {
            $this->exec_done($this->goforward_url, $arr_filter);
        } else {
            $this->exec_done($this->goback_url, $arr_filter);
        }
    }

    public function qry_all_listtype_option() {
        $str_sql = "Select LT.PK_LISTTYPE, LT.C_NAME From t_cores_listtype LT Where (LT.C_STATUS > 0) Order by LT.C_ORDER";
        return $this->db->getAssoc($str_sql);
    }

    public function delete_listtype() {
        if (!isset($_POST))
        {
            $this->exec_done($this->goback_url);
            return;
        }

        $v_item_id_list = isset($_POST['hdn_item_id_list']) ? replace_bad_char($_POST['hdn_item_id_list']) : '';

        //Kiem tra Loai danh muc nay co chua Doi tuong danh muc khong?
        if ($v_item_id_list != '') {
            if (strpos($v_item_id_list, ',') != false) {
                //Nhieu doi tuong duoc chon
                $sql = "Select count(*) from t_cores_list Where FK_LISTTYPE in ($v_item_id_list)";
                if ($this->db->getOne($sql) == 0) {
                    $sql = "Delete From t_cores_listtype Where PK_LISTTYPE in ($v_item_id_list)";
                    $this->db->Execute($sql);
                }
            } else {
                //Chi co 1 doi tuong duoc chon
                $sql = "Select count(*) from t_cores_list Where FK_LISTTYPE=$v_item_id_list";
                if ($this->db->getOne($sql) == 0) {
                    $sql = "Delete From t_cores_listtype Where PK_LISTTYPE=$v_item_id_list";
                    $this->db->Execute($sql);
                }
            }
        }

        //Luu dieu kien loc
        $v_filter = isset($_POST['txt_filter']) ? replace_bad_char($_POST['txt_filter']) : '';
        $v_page = isset($_POST['sel_goto_page']) ? replace_bad_char($_POST['sel_goto_page']) : 1;
        $v_rows_per_page = isset($_POST['sel_rows_per_page']) ? replace_bad_char($_POST['sel_rows_per_page']) : _CONST_DEFAULT_ROWS_PER_PAGE;
        $arr_filter = array(
                            'txt_filter'   => $v_filter,
                            'sel_goto_page'   => $v_page,
                            'sel_rows_per_page'   => $v_rows_per_page
                        );

        $this->exec_done($this->goback_url, $arr_filter);
    }

    //List
    public function check_existing_list_code($code, $listtype_id, $list_id){
        $code           = replace_bad_char($code);
        $listtype_id    = replace_bad_char($listtype_id);
        $list_id        = replace_bad_char($list_id);

        $sql = "Select count(*) count from t_cores_list Where (PK_LIST <> $list_id) AND (C_CODE='$code') AND (FK_LISTTYPE=$listtype_id)";
        $this->db->debug=0;
        return json_encode($this->db->getRow($sql));
    }
    public function check_existing_list_name($name, $listtype_id, $list_id){
        $name = replace_bad_char($name);
        $listtype_id    = replace_bad_char($listtype_id);
        $list_id        = replace_bad_char($list_id);

        $sql = "Select count(*) count from t_cores_list Where (PK_LIST <> $list_id) AND (C_NAME='$name') And (FK_LISTTYPE=$listtype_id)";
        $this->db->debug=0;
        return json_encode($this->db->getRow($sql));
    }

    public function qry_all_list() {
        //Luu dieu kien loc
        $v_filter = isset($_POST['txt_filter']) ? replace_bad_char($_POST['txt_filter']) : '';

        $v_listtype_id = 0;
        if (isset($_POST['sel_listtype_filter'])) {
            $v_listtype_id = replace_bad_char($_POST['sel_listtype_filter']);
        } else {
            $v_listtype_id = @$this->db->GetOne('Select PK_LISTTYPE From t_cores_listtype Order by C_ORDER');
        }

         //Luu dieu kien loc
        $v_page = isset($_POST['sel_goto_page']) ? replace_bad_char($_POST['sel_goto_page']) : 1;
        $v_rows_per_page = isset($_POST['sel_rows_per_page']) ? replace_bad_char($_POST['sel_rows_per_page']) : _CONST_DEFAULT_ROWS_PER_PAGE;

        $v_start = $v_rows_per_page * ($v_page - 1);
        $v_end = $v_start + $v_rows_per_page;

        $condition_query = '';
        if ($v_filter !== '') {
            $condition_query .= " And C_NAME Like '%$v_filter%'";
        }
        if ($v_listtype_id > 0) {
            $condition_query .= " And FK_LISTTYPE=$v_listtype_id";
        }

        //Dem tong so trang
        $sql_count_record = "Select Count(*) total_record From t_cores_list L Where (1 > 0) $condition_query";

        //Ket qua theo dieu kien loc
        
        if(DATABASE_TYPE == 'MSSQL')
        {
            $sql = "Select PK_LIST, C_CODE, C_NAME, C_ORDER, C_STATUS, ($sql_count_record) TOTAL_RECORD, ROW_NUMBER() OVER (ORDER BY C_ORDER ASC) RN FROM t_cores_list L Where (1 > 0) $condition_query";
            $stmt = "Select * From ($sql) a Where a.rn>=$v_start And a.rn<=$v_end";
        }
        elseif(DATABASE_TYPE == 'MYSQL')
        {
            $v_start = $v_start;
            $v_limit = $v_end - $v_start;
            
            $stmt = "Select PK_LIST, 
                            C_CODE, 
                            C_NAME, 
                            C_ORDER, 
                            C_STATUS, 
                            ($sql_count_record) TOTAL_RECORD 
                    FROM t_cores_list L Where (1 > 0) $condition_query ORDER BY C_ORDER ASC limit $v_start,$v_limit";
        }
        
        //return
        return $this->db->getAll($stmt);

    } //end func qry_all_list

    public function qry_single_list() {
        $v_list_id = isset($_POST['hdn_item_id']) ? replace_bad_char($_POST['hdn_item_id']) : '0';
        $v_listtype_id = isset($_POST['sel_listtype_filter']) ? replace_bad_char($_POST['sel_listtype_filter']) : '0';

        if ($v_list_id > 0) {
            $stmt = 'Select L.PK_LIST
                            , L.FK_LISTTYPE
                            , L.C_CODE
                            , L.C_NAME
                            , L.C_XML_DATA
                            , L.C_ORDER
                            , L.C_STATUS
                            , LT.C_XML_FILE_NAME
                    From t_cores_list L left join t_cores_listtype LT on L.FK_LISTTYPE=LT.PK_LISTTYPE
                    Where L.PK_LIST=?';
            $params = array('id' => $v_list_id);
            return $this->db->getRow($stmt, $params);
        } else {
            $a['C_XML_FILE_NAME']   = $this->db->getOne("SELECT c_xml_file_name FROM t_cores_listtype WHERE PK_LISTTYPE=$v_listtype_id");
            $a['C_ORDER']           = $this->get_max('t_cores_list', 'C_ORDER', 'FK_LISTTYPE=' . $v_listtype_id);
            $a['FK_LISTTYPE']       = $v_listtype_id;
            return $a;
        }
    } //end func qry_single_list

    public function update_list() {

        if (!isset($_POST))
        {
            $this->exec_done($this->goback_url);
            return;
        }

        $v_list_id      = replace_bad_char($_POST['hdn_item_id']);
        $v_listtype_id  = replace_bad_char($_POST['sel_listtype_filter']);
        $v_list_code    = trim(replace_bad_char($_POST['txt_code']));
        $v_list_name    = replace_bad_char($_POST['txt_name']);
        $v_order        = replace_bad_char($_POST['txt_order']);
        $v_status       = isset($_POST['chk_status']) ? 1 : 0;
        $v_xml_data     = $_POST['XmlData'];
        

        $v_save_and_addnew = isset($_POST['chk_save_and_addnew']) ? 1 : 0;
        
        //kiem tra ma danh muc bi trung
        $sql = "SELECT
                COUNT(PK_LIST)
              FROM t_cores_list
              WHERE C_CODE = '$v_list_code'
                  AND FK_LISTTYPE = $v_listtype_id";
        if($v_list_id > 0)
        {
            $sql .= " AND PK_LIST <> $v_list_id";
        }
        $check = $this->db->getOne($sql);
        if($check > 0)
        {
            $this->exec_fail($this->goback_url,__('duplicate code list'),array('sel_listtype_filter'=>$v_listtype_id));
            return;
        }
        
        //thuc hien cap nhat
        if ($v_list_id > 0) //Update
        {
            
            $stmt = "Update t_cores_list Set
                        FK_LISTTYPE=$v_listtype_id
                        ,C_CODE='$v_list_code'
                        ,C_NAME='$v_list_name'
                        ,C_OWNER_CODE_LIST=''
                        ,C_XML_DATA='$v_xml_data'
                        ,C_ORDER=$v_order
                        ,C_STATUS=$v_status
                    Where PK_LIST=$v_list_id";
        } else {
            //Insert

            $stmt = "Insert Into t_cores_list (
                        FK_LISTTYPE,
                        C_CODE,
                        C_NAME,
                        C_OWNER_CODE_LIST,
                        C_XML_DATA,
                        C_ORDER,
                        C_STATUS

                    ) values (
                        $v_listtype_id,
                        '$v_list_code',
                        '$v_list_name',
                        '',
                        '$v_xml_data',
                        $v_order,
                        $v_status
                    )";
            $v_list_id = $this->db->getOne("SELECT IDENT_CURRENT('t_cores_list')");
        }
        //echo $stmt; exit;
        $this->db->Execute($stmt);
        $this->ReOrder('t_cores_list','PK_LIST','C_ORDER', $v_list_id, $v_order, "FK_LISTTYPE=$v_listtype_id");

        //Luu dieu kien loc
        $v_filter = isset($_POST['txt_filter']) ? $_POST['txt_filter'] : '';
        $v_page = isset($_POST['sel_goto_page']) ? replace_bad_char($_POST['sel_goto_page']) : 1;
        $v_rows_per_page = isset($_POST['sel_rows_per_page']) ? replace_bad_char($_POST['sel_rows_per_page']) : _CONST_DEFAULT_ROWS_PER_PAGE;

        $arr_filter = array(
            'sel_listtype_filter'   => $v_listtype_id,
            'txt_filter'            => $v_filter,
            'sel_goto_page'         => $v_page,
            'sel_rows_per_page'   => $v_rows_per_page
        );


        if ($v_save_and_addnew > 0) {
            $this->exec_done($this->goforward_url, $arr_filter);
        } else {
            $this->exec_done($this->goback_url, $arr_filter);
        }



        if ($v_save_and_addnew > 0) {
            $this->exec_done($this->goforward_url, $arr_filter);
        } else {
            $this->exec_done($this->goback_url, $arr_filter);
        }
    } //end func update_list

    public function delete_list() {
        if (!isset($_POST))
        {
            $this->exec_done($this->goback_url);
            return;
        }

        $v_item_id_list = isset($_POST['hdn_item_id_list']) ? replace_bad_char($_POST['hdn_item_id_list']) : '';

        //Kiem tra Loai danh muc nay co chua Doi tuong danh muc khong?
        if ($v_item_id_list != '')
        {
            if (strpos($v_item_id_list, ',') !== FALSE)
            {
                //Nhieu doi tuong duoc chon
                $sql = "Delete From t_cores_list Where PK_LIST in ($v_item_id_list)";
            }
            else
            {
                //Chi co 1 doi tuong duoc chon
                $sql = "Delete From t_cores_list Where PK_LIST=$v_item_id_list";

            }
			$this->db->Execute($sql);
        }



        //Luu dieu kien loc
        $v_filter = isset($_POST['txt_filter']) ? $_POST['txt_filter'] : '';
        $v_listtype_id = isset($_POST['sel_listtype_filter']) ? $_POST['sel_listtype_filter'] : '0';
        $v_page = isset($_POST['sel_goto_page']) ? replace_bad_char($_POST['sel_goto_page']) : 1;
        $v_rows_per_page = isset($_POST['sel_rows_per_page']) ? replace_bad_char($_POST['sel_rows_per_page']) : _CONST_DEFAULT_ROWS_PER_PAGE;
        $arr_filter = array(
            'sel_listtype_filter'   => $v_listtype_id,
            'txt_filter'            => $v_filter,
            'sel_goto_page'   => $v_page,
            'sel_rows_per_page'   => $v_rows_per_page
        );

        $this->exec_done($this->goback_url, $arr_filter);
    } //end func delete_list()

}