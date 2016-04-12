<?php
/**
 * Description of custommer_Model
 *
 * @author Tam Viet
 */
class citizen_account_Model extends Model
{
    function __construct() 
    {
        parent::__construct();
    } 
    
    /**
     * Lay thong tin tat ca cac khach hang
     * @return array Mang chua thong tin danh sach cac khach hàng
     */
    public function dsp_all_account()
    {
        //paging
        page_calc($v_start, $v_end);
        $v_start = $v_start -1;
        $v_limit = $v_end - $v_start;
        
        #Diều kiện lọc
        $v_username  = get_request_var('txt_username','');
        $v_email     = get_request_var('txt_email','');
        $v_name      = get_request_var('txt_name','');
        $v_address   = get_request_var('txt_address','');
        $v_status    = get_request_var('sel_status','');
        $v_organize  = get_request_var('sel_organize','');
        
        $v_condition = '';
        if(trim($v_username) != '')//tim kiem theo ten dang nhap
        {
            $v_condition .= " And C_USERNAME like '%$v_username%'";
        }
        if(trim($v_email) != '')//tim kiem theo email
        {
            $v_condition .= " And C_EMAIL = '$v_email'";
        }
        if($v_status != '')//tim kiem theo trang thai
        {
            $v_condition .= " And C_STATUS = '$v_status'";
        }
        if($v_organize != '')//tim kiem theo to chuc/ca nhan
        {
            $v_condition .= " And C_ORGAN = '$v_organize'";
        }
        if(trim($v_name) != '')//tim kiem theo ten
        {
            $v_condition .= " And INSTR(ExtractValue(C_XML_DATA,'//item/name'),'$v_name')";
        }
        if(trim($v_address) != '')//tim kiem theo dia chi
        {
            $v_condition .= " And INSTR(ExtractValue(C_XML_DATA,'//item/address'),'$v_address')";
        }
        
        $sql = "SELECT
                    c.*,
                    (SELECT
                       COUNT(PK_CITIZEN)
                     FROM t_ps_citizen) AS TOTAL_RECORD
                  FROM t_ps_citizen c
                  WHERE (1>0) $v_condition
                  LIMIT $v_start,$v_limit";
        
        $resluts = $this->db->GetAll($sql);
        if($this->db->ErrorNo() == 0)
        {
            return $resluts;
        }
        return array();
    }
    /**
     * xoa account
     * @param type $list_delete
     */
    public function delete_account($list_delete)
    {
        $v_list_record_id = $this->db->GetOne("SELECT  GROUP_CONCAT(PK_RECORD) FROM t_ps_record WHERE FK_CITIZEN In ($list_delete)"); 
        //1.de thuc hien xoa tai khoan, phai xoa toan bo record cua tai khoan do
        if(trim($v_list_record_id) != '')
        {
            $arr_file_name = $this->get_file_attach($v_list_record_id);
       
            foreach($arr_file_name as $v_file_name)
            {
                $v_path_file = _CONST_RECORD_FILE . $v_file_name;
                if(is_file($v_path_file))
                {
                    @unlink($v_path_file);
                }
            }
            //Ho so cua cong dan nop ko dang ky
            $stmt = "DELETE
                            FROM t_ps_record_file
                            WHERE FK_RECORD IN($v_list_record_id)";
            $this->db->Execute($stmt);

            $stmt = "DELETE FROM t_ps_record WHERE FK_CITIZEN In ($list_delete)";
            $this->db->Execute($stmt);
        }
        
        //2.xoa tai khoan
        $sql = "DELETE FROM t_ps_citizen WHERE PK_CITIZEN IN ($list_delete)";
        $this->db->Execute($sql);
        //2.xoa tai khoan
        $sql = "DELETE FROM t_ps_citizen_tmp WHERE FK_CITIZEN IN ($list_delete)";
        $this->db->Execute($sql);
        $this->exec_done($this->goback_url);
    }
    
    /**
     * lay danh sach file dinh kem cua hs 
     * @param type $record_id
     * @return type
     */
    public function get_file_attach($v_list_record_id)
    {
        if(trim($v_list_record_id) == '')
        {
            return array();
        }
        
        $stmt = "SELECT
                    C_FILE_NAME
                  FROM t_ps_record_file
                  WHERE FK_RECORD in($v_list_record_id)";
        return $this->db->GetCol($stmt);
    }
    
    
    /**
     * lay thong tin 1 account
     * @param type $account_id
     * @return type
     */
    public function qry_single_account($account_id)
    {
        $sql = "SELECT * FROM t_ps_citizen WHERE PK_CITIZEN = $account_id";
        return $this->db->getRow($sql);
    }
    /**
     * cap nhat thong tin account
     * @param type $account_id
     * @param type $account_status
     * @param type $account_reason
     */
    public function update_account($account_id,$account_status,$account_reason)
    {
        if($account_status == '1')//neu status la hoat dong
        {
            $sql = "UPDATE t_ps_citizen SET C_STATUS = $account_status WHERE PK_CITIZEN = $account_id";
            $this->db->Execute($sql);
        }
        elseif($account_status == '0')//neu status la khoa
        {
            $sql = "SELECT C_XML_DATA FROM t_ps_citizen WHERE PK_CITIZEN = $account_id";
            $xml_data = $this->db->GetOne($sql);
            $dom = simplexml_load_string($xml_data, 'SimpleXMLElement',LIBXML_NOCDATA);
            
            if(isset($dom->reason))//neu ton tai tag reason
            {
                $sql = "UPDATE t_ps_citizen
                                SET C_STATUS = '$account_status',
                                  C_XML_DATA = UpdateXML(C_XML_DATA, '//reason', '<reason><![CDATA[$account_reason]]></reason>')
                                WHERE PK_CITIZEN = $account_id";
            }
            else //ko ton tai tag reason
            {
                $sxe = new SimpleXMLElement($xml_data);
                $node_reason = $sxe->addChild('reason');
                $node= dom_import_simplexml($node_reason); 
                $no = $node->ownerDocument; 
                $node->appendChild($no->createCDATASection($account_reason)); 
                $xml_data = $sxe->asXML();
                
                $sql = "UPDATE t_ps_citizen
                                SET C_STATUS = '$account_status',
                                  C_XML_DATA = '$xml_data'
                                WHERE PK_CITIZEN = $account_id";
            }
            $this->db->Execute($sql);
        }
         $this->exec_done($this->goback_url);
    }
    
    //Xoa tai khoan dan ky qua han kich hoat    
    public function do_delete_overdue_confirm()
    {
        $v_limit_account_date_trigger = defined('_CONS_LIMIT_ACCOUNT_DATE_TRIGGER') ? _CONS_LIMIT_ACCOUNT_DATE_TRIGGER : 7;
        $sql = "DELETE
                    FROM t_ps_citizen
                    WHERE PK_CITIZEN IN(SELECT
                                          FK_CITIZEN
                                        FROM t_ps_citizen_tmp
                                        WHERE C_STATUS = 1
                                            AND DATE_ADD(C_CREATE_DATE,INTERVAL  $v_limit_account_date_trigger  DAY) < NOW())";
        $this->db->Execute($sql);
        $sql = "delete from t_ps_citizen_tmp
                    where C_STATUS = 1 
                            And date_add(C_CREATE_DATE,interval  $v_limit_account_date_trigger  day) < now()  ";
        $this->db->Execute($sql);
        if($this->db->ErrorNo() == 0) return TRUE;
        return FALSE;
    }
    
    //Dem tong so tai khoan moi dang ky qua han kich hoat
    public function qry_count_ac_new_over_confirm()
    {
        $v_limit_account_date_trigger = defined('_CONS_LIMIT_ACCOUNT_DATE_TRIGGER') ? _CONS_LIMIT_ACCOUNT_DATE_TRIGGER : 7;
        $sql =" SELECT
                        COUNT(PK_CITIZEN)
                      FROM t_ps_citizen
                      WHERE PK_CITIZEN IN(SELECT
                                            FK_CITIZEN
                                          FROM t_ps_citizen_tmp
                                          WHERE C_STATUS = 1
                                              AND DATE_ADD(C_CREATE_DATE,INTERVAL $v_limit_account_date_trigger DAY) < NOW())";
        return $this->db->GetOne($sql);
        
    }
    //Huy bo thong tin luu tru yeu cau xác nhan doi voi cac tai khoan yeu cau thay doi email qua han.
    public function do_del_ac_tmp_overdue_confirm()
    {
        $v_limit_account_date_trigger = defined('_CONS_LIMIT_ACCOUNT_DATE_TRIGGER') ? _CONS_LIMIT_ACCOUNT_DATE_TRIGGER : 7;
        
        $sql = "delete from t_ps_citizen_tmp
                    where (C_STATUS = 2 OR C_STATUS = 3)
                            And date_add(C_CREATE_DATE,interval  $v_limit_account_date_trigger  day) < now()  ";
        @$this->db->Execute($sql);
    }
}

