<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of member_Model
 *
 * @author Tam Viet
 */
class member_Model extends Model
{

    public function __construct()
    {
        parent:: __construct();
    }

    /**
     * Update neu Id= !0 or Inser neu id = 1
     * 
     * @param int $v_member_id Ma cua member
     * @return bool  True if success else False 
     */
    public function update_member($arr_post = array())
    {
        if (sizeof($arr_post) < 0)
        {
            $this->model->exec_fail($this->view->get_controller_url(), 'Dữ liệu chưa hợp lệ xin kiểm tra lại!');
        }
        $v_member_id        = $arr_post['member_id'];
        $v_member_name      = $arr_post['txt_member_name'];
        $v_member_code      = mb_strtoupper(trim($arr_post['txt_member_code']));
        $v_member_address   = $arr_post['txt_member_address'];
        $v_member_email     = $arr_post['txt_member_email'];
        $rad_level          = $arr_post['rad_level'];
        $v_member_parent_id = $arr_post['sel_member_parent'];
        $v_status           = $arr_post['chk_status'];
        $v_xml_data         = html_entity_decode($arr_post['XmlData']);
        $v_hdn_single_method= $arr_post['hdn_dsp_single_method'];
        $v_short_code       = $arr_post['txt_short_code'];
        $v_rad_village      = $arr_post['rad_village'];
        $v_order            = isset($_REQUEST['txt_order']) ? $_REQUEST['txt_order'] : 1;
        //kiem tra ma don vi
        if ($this->_check_exists_member_code($v_member_code, $v_member_id) > 0)
        {
            $v_back_url = $this->goback_url . $v_hdn_single_method . "/$v_member_id";
            $message = 'Mã đơn vị đã tồn tại! Xin kiểm tra lại!';
            $this->exec_fail($v_back_url, $message, $arr_post);
            exit();
        }
        //
        if ($rad_level != 2 && $this->_check_exist_exchange_email($v_member_email, $v_member_id) > 0)
        {
            $v_back_url = $this->goback_url . $v_hdn_single_method . "/$v_member_id";
            $message = 'Email trao đổi thông tin đã tồn tại !';
            $this->exec_fail($v_back_url, $message, $arr_post);
            exit();
        }
        //neu la cap xa => lấy thông tin từ huyện
        if ($rad_level == 2)
        {
            $arr_info = $this->qry_single_member($v_member_parent_id);

            $v_member_email = $arr_info['C_EXCHANGE_EMAIL'];
            $v_xml_data     = $arr_info['C_XML_DATA'];
            $v_short_code   = $arr_info['C_SHORT_CODE'];
        }
        if ($v_member_id == 0)//insert
        {
            //  Insert
            $stmt = "INSERT INTO t_ps_member 
                            ( 
                            C_NAME, 
                            C_CODE, 
                            C_SHORT_CODE,
                            C_ADDRESS, 
                            C_EXCHANGE_EMAIL, 
                            C_SCOPE, 
                            C_XML_DATA,
                            C_STATUS,
                            FK_MEMBER,
                            FK_VILLAGE_ID,
							C_ORDER
                            )
                            VALUES
                            ( 
                            ?, 
                            ?, 
                            ?, 
                            ?, 
                            ?, 
                            ?, 
                            ?,
                            ?,
                            ?,
                            ?,
							?
                            )";
            $params = array($v_member_name, $v_member_code,
                $v_short_code, $v_member_address,
                $v_member_email, $rad_level,
                $v_xml_data, $v_status,
                $v_member_parent_id, ($v_rad_village == '') ? NULL : $v_rad_village,$v_order);
            $this->db->Execute($stmt, $params);
        }
        else //update
        {
            //check child
            if ((int) $this->_check_exists_member_child($v_member_id) > 0 && $rad_level == 2)
            {
                $this->exec_fail($this->goback_url . $v_hdn_single_method, 'Cần phải loại bỏ hết đơn vị cấp xã trực thuộc mới có thể thay đổi cấp đơn vị', array('hdn_item_id' => $v_member_id));
                exit();
            }
            $stmt = " UPDATE    t_ps_member  SET 
                                C_NAME              = ?, 
                                C_CODE              = ?, 
                                C_SHORT_CODE        = ?, 
                                C_ADDRESS           = ?, 
                                C_EXCHANGE_EMAIL    = ?, 
                                C_SCOPE             = ?, 
                                C_XML_DATA          = ?,
                                C_STATUS            = ?,
                                FK_MEMBER           = ?,
                                FK_VILLAGE_ID       = ?,
                                C_ORDER             = ?
                        WHERE               
                                PK_MEMBER           = ?
                    ";
            $params = array($v_member_name
                            , $v_member_code
                            , $v_short_code
                            , $v_member_address
                            , $v_member_email
                            , $rad_level
                            , $v_xml_data
                            , $v_status
                            , $v_member_parent_id
                            ,($v_rad_village == '') ? NULL : $v_rad_village
                            ,$v_order
                            , $v_member_id
                            );
            $this->db->Execute($stmt, $params);
            if((int)($v_member_parent_id) == 0)
            {
                //Update xml cho cap xa neu member chi sua la cap huyen
                $this->db->Execute("Update t_ps_member set C_XML_DATA= ? where FK_MEMBER = ?",array($v_xml_data,$v_member_id ));
            }
        }
        if ($this->db->ErrorNo() == 0)
        {
            $this->exec_done($this->goback_url);
        }
        $this->exec_fail($this->goback_url . $v_hdn_single_method, 'Xảy ra lỗi trong quá trình cập nhật. Xin vui lòng thử lại!', array('hdn_item_id' => $v_member_id));
    }

    /**
     * Dem so don vi cap con thuoc don vi dang kiem tra theo id
     * 
     * @return int So don vi truc thuoc la don vi cap con
     */
    private function _check_exists_member_child($v_memeber_id)
    {
        $stmt = "SELECT COUNT(PK_MEMBER) FROM t_ps_member WHERE FK_MEMBER = ?";
        return $this->db->GetOne($stmt, array($v_memeber_id));
    }

    private function _check_exist_exchange_email($v_exchange_email, $v_member_id = 0)
    {
        $v_condition = '';
        if ((int) $v_member_id > 0)
        {
            $v_condition = " And PK_MEMBER <> '$v_member_id' ";
        }
        return $this->db->getOne("SELECT COUNT(PK_MEMBER) FROM t_ps_member WHERE (1=1) And C_EXCHANGE_EMAIL = ? AND C_SCOPE IN (0,1) $v_condition ", array($v_exchange_email));
    }

    /**
     * Kiem tra su ton tai ma cua don vi truc thuoc
     * 
     * @param int $v_code_member Ma cua member them moi hoac sua doi
     * @return int Khong ton tai return >0, Neu khong ton tai return = 0
     */
    private function _check_exists_member_code($v_code_member, $v_member_id = 0)
    {
        $v_condition = '';
        if ((int) $v_member_id > 0)
        {
            $v_condition = " And PK_MEMBER <> '$v_member_id' ";
        }
        return $this->db->getOne("SELECT COUNT(PK_MEMBER) FROM t_ps_member WHERE (1=1) $v_condition And C_CODE = ?", array($v_code_member));
    }

    /**
     * Lay thong tin chi tiet mot member theo ID
     * 
     * @param int $v_member_id  Ma cua member can lay ID
     * @return array            Mang  chua thong tin chi tiet member theo id 
     */
    public function qry_single_member($v_member_id = 0)
    {

        if ((int) $v_member_id == 0)
        {
            return array();
        }

        $stmt = "Select * From t_ps_member where PK_MEMBER = ?";
        $results = $this->db->GetRow($stmt, array($v_member_id));

        if ($this->db->ErrorNo() == 0)
        {
            return $results;
        }
        $this->exec_fail($this->goback_url, 'Đã xảy ra lỗi. Xin vui lòng thử lại!');
    }

    /**
     * Lay tat ca cac don vi truc thuoc theo dieu kien loc 
     * 
     * @param array() $arr_filter Mang chua noi dung loc
     * @return array() mang chua danh sach cac don vi truoc thuoc
     */
    public function qry_all_member($arr_filter = array())
    {
        $condition = '';
        if (sizeof($arr_filter) > 0 && trim(replace_bad_char($arr_filter['txt_filter'])) != '')
        {
            $v_filter = trim(replace_bad_char($arr_filter['txt_filter']));
            $condition = " And (C_NAME like '%$v_filter%' OR C_CODE like '%$v_filter%') ";
            $stmt = "SELECT
                    pm.*,
                    '' as C_XML_MEMBER_CHILD 
                  FROM t_ps_member pm
                  WHERE (1=1)
                      $condition
                  ORDER BY C_NAME ASC";
        }
        else
        {
            $stmt = "SELECT
                        (SELECT
                           CONCAT('<data>'
                                      , GROUP_CONCAT('<row '
                                      , CONCAT(' PK_MEMBER =\"',PK_MEMBER,'\" ')
                                      , CONCAT(' C_NAME =\"',C_NAME,'\" ')
                                      , CONCAT(' C_CODE=\"',C_CODE,'\" ')
                                      , CONCAT(' C_SCOPE=\"',C_SCOPE,'\" ')
                                      , CONCAT(' FK_MEMBER=\"',FK_MEMBER,'\" ')	
                                      , CONCAT(' C_STATUS=\"',C_STATUS,'\" ')
									  , CONCAT(' C_ORDER=\"',if(C_ORDER is null,1,C_ORDER),'\" ')
                                      ,'/>' 
                                      SEPARATOR '')
                                 ,'</data>')
                         FROM t_ps_member
                         WHERE FK_MEMBER = pm.PK_MEMBER 
                         ORDER BY C_NAME ASC) AS C_XML_MEMBER_CHILD,
                        pm.*
                      FROM t_ps_member pm
                      WHERE FK_MEMBER = 0
                      ORDER BY C_ORDER ASC";
        }
        return $this->db->getAll($stmt);
    }

    /**
     * Lay danh sach tat ca ca quan huyen
     * 
     * @return array Mang chua danh sách Quan/Huyen 
     */
    public function qry_all_member_level_1($v_member_id = 0)
    {
        $v_condition = '';
        if ((int) $v_member_id > 0)
        {
            $v_condition = " And PK_MEMBER <> '$v_member_id' ";
        }
        $stmt = "Select
                        PK_MEMBER,
                        C_NAME,
                        C_CODE
                      From t_ps_member
                      Where C_SCOPE = 1
                            $v_condition
                            And C_STATUS =1";
        return $this->db->GetAll($stmt);
    }

    /**
     * xoa member
     * @param type $v_list_id
     */
    function delete_member($v_list_id = '')
    {
        if (trim($v_list_id) != '')
        {
            //xoa member
            $this->db->Execute("DELETE FROM t_ps_member WHERE PK_MEMBER IN ($v_list_id)");
            //xoa thu tuc internet member tiep nhan
            $this->db->Execute("DELETE FROM t_ps_record_type_member WHERE FK_MEMBER IN ($v_list_id)");
                    }
        $this->exec_done($this->goback_url);
    }
 

    public function do_synchronize()
    {
        $sql = "SELECT
                    IF(FK_VILLAGE_ID = 0 OR ISNULL(FK_VILLAGE_ID),C_CODE,(SELECT C_CODE FROM t_ps_member WHERE PK_MEMBER = m.FK_MEMBER)) AS C_CODE,
                    FK_VILLAGE_ID,
                    FK_MEMBER,
                    C_NAME,
                    C_XML_DATA
                  FROM t_ps_member m where m.C_STATUS = 1
                  ORDER BY C_CODE";
        $arr_all_unit = $this->db->GetAll($sql);
        
        foreach ($arr_all_unit as $arr_single_unit)
        {
            $xml_data  = $arr_single_unit['C_XML_DATA'];            
            @$dom      = simplexml_load_string($xml_data);
            if(!$dom)
            {
                continue;
            }
            $location        = xpath($dom, '//item[@id="location"]/value', XPATH_STRING);
            $uri             = xpath($dom, '//item[@id="uri"]/value', XPATH_STRING);
            $v_fk_village_id = ($arr_single_unit['FK_VILLAGE_ID'] > 0) ? $arr_single_unit['FK_VILLAGE_ID'] : 0;
            $unit_code       = $arr_single_unit['C_CODE'];
            $v_village_name  = $arr_single_unit['C_NAME'];
	
            $max_date_update = $this->db->GetOne("SELECT
                                    MAX(C_HISTORY_DATE)
                                  FROM t_ps_record_history_stat
                                  WHERE C_UNIT_CODE = ?
                                      AND FK_VILLAGE_ID = ?",array($unit_code,$v_fk_village_id));
            if(trim($max_date_update) == '')
            {
                $client = new SoapClient($location . '?wsdl', array('location' => $location,
                                                'uri'      => $uri));
                $max_date_update    = $client->__soapCall('get_min_date_report', array());
                if($max_date_update == '')
                {
                    continue;
                }
            }
            if(date('Y-m-d H:i:s', strtotime($max_date_update)) != $max_date_update)
            {
                //$max_date_update: không phải là ngày tháng: lỗi chưa xử lý
                continue;
            }
            $brack = false;
            while ($brack == false)
            {
                //Cập nhật dữ liệu tháng hiện tại
                $month = date('m', strtotime($max_date_update));
                $year = date('Y', strtotime($max_date_update));
                try
                {
                    $client = new SoapClient($location . '?wsdl', array('location' => $location,
                                            'uri'      => $uri));
                    $xml_progress_report = $client->__soapCall('progress_report', array($v_fk_village_id,$month,$year));
                }
                catch (Exception $e)
                {
                    $this->exec_fail($_REQUEST['controller'], "Xảy ra lỗi trong quá trình cập nhật dữ liệu đơn vị '$v_village_name'. Vui lòng thử lại hoặc kiểm tra thông tin cấu hình");
                    exit();
                }
                $history_date  = date('Y-m-t',strtotime($max_date_update));
                if(($month >= date('m') && $year == date('Y')) OR $year > date('Y'))
                {
                    $history_date  = date('Y-m-d');
                    $brack = true;
                }                
                $max_date_update = date('Y-m-d',strtotime('+1 month',strtotime($max_date_update)));
                //delete record_history_old
                $this->delete_record_history_start($v_fk_village_id,$history_date,$unit_code);
                //insert record_history
                $response = $this->insert_member_info($xml_progress_report,$v_fk_village_id,$v_village_name,$history_date,$unit_code);
                if(!$response)
                {
                    $this->exec_fail($_REQUEST['controller'], "Xảy ra lỗi trong quá trình cập nhật dữ liệu đơn vị '$v_village_name'");
                    exit();
                }
            }
        }
    }

    function do_sync_user()
    {
        $v_listtype_id = $this->db->GetOne("SELECT pk_listtype FROM t_cores_listtype WHERE c_code='DM_CAN_BO_DANH_GIA'");

        $sql = "SELECT PK_MEMBER,C_CODE, C_XML_DATA
                    FROM t_ps_member
                    WHERE C_SCOPE <> 2
                    ORDER BY pk_member";
        $arr_all_unit = $this->db->GetAll($sql);
        //duyệt từng đơn vị lấy DS user


        $v_member_order = 0;
        foreach ($arr_all_unit as $arr_single_unit)
        {
            $xml_data = $arr_single_unit['C_XML_DATA'];
            $v_member_code = $arr_single_unit['C_CODE'];
            $v_member_id = $arr_single_unit['PK_MEMBER'];
            $v_member_order += 100;

            $dom = simplexml_load_string($xml_data);
            $location = xpath($dom, '//item[@id="location"]/value', XPATH_STRING);
            $uri = xpath($dom, '//item[@id="uri"]/value', XPATH_STRING);
            $uri = 'urn://tyler/res';

            try
            {
                $client = new SoapClient($location . '?wsdl', array('location' => $location,
                                            'uri'      => $uri));
                $result = $client->__soapCall('r3_staff', array());
                $dom_all_user = simplexml_load_string($result);
                if (!$dom_all_user)
                {
                    throw new Exception("Không load được xml");
                }

                $v_u_order = $v_member_order;
                $arr_received_user = array();

                foreach (xpath($dom_all_user, '//user') as $dom_user)
                {
                    $v_u_name = (string) $dom_user->user_name;
                    $v_u_code = (string) $dom_user->user_code . '@' . strtolower($v_member_code);
                    $v_u_village = (string) $dom_user->village_id;
                    $v_u_edu = (string) $dom_user->education;
                    $v_u_job = (string) $dom_user->job_title;
                    $v_u_email = (string) $dom_user->email;
                    $v_u_birth = date_create((string) $dom_user->birth_day);
                    $v_u_order++;

                    if ($v_u_village)
                    {
                        $v_u_member = $this->db->GetOne("SELECT pk_member FROM t_ps_member WHERE fk_village_id=?", array($v_u_village));
                    }
                    else
                    {
                        $v_u_member = $v_member_id;
                    }

                    if ($v_u_birth)
                    {
                        $v_u_birth = $v_u_birth->format('d-m-Y');
                    }
                    else
                    {
                        $v_u_birth = '';
                    }

                    $arr_received_user[] = $v_u_code;

                    $v_inserted_id = $this->db->GetOne("SELECT pk_list FROM t_cores_list WHERE c_code=? AND fk_listtype=?", array($v_u_code, $v_listtype_id));

                    $v_u_xml = "<?xml version='1.0' encoding='utf-8' ?>
                        <data>
                            <item id='ddl_member'>
                                <value><![CDATA[$v_u_member]]></value>
                            </item>
                            <item id='txt_birthday'>
                                <value><![CDATA[$v_u_birth]]></value>
                            </item>
                            <item id='txt_education'>
                                 <value><![CDATA[$v_u_edu]]></value>
                            </item>
                            <item id='txt_job_title'>
                                <value><![CDATA[$v_u_job]]></value>
                            </item>
                            <item id='txt_email'>
                                <value><![CDATA[$v_u_email]]></value>
                            </item>
                        </data>
                    ";

                    if ($v_inserted_id)
                    {
                        //update
                        $sql = "
                            UPDATE t_cores_list SET 
                                c_code=?,
                                c_name=?,
                                c_xml_data=?,
                                c_order=?,
                                c_status=1
                            WHERE pk_list=?
                        ";
                        $params = array($v_u_code, $v_u_name, $v_u_xml, $v_u_order, $v_inserted_id);
                        $this->db->Execute($sql, $params);
                    }
                    else
                    {
                        //insert
                        $sql = "
                            INSERT INTO t_cores_list(fk_listtype, c_code, c_name, c_xml_data, c_order,c_status)
                            VALUES (?, ?, ?, ?, ?, 1)
                        ";
                        $params = array($v_listtype_id, $v_u_code, $v_u_name, $v_u_xml, $v_u_order);
                        $this->db->Execute($sql, $params);
                    }
                }
                //xóa user không còn
                if (count($arr_received_user))
                {
                    $arr_received_user = "'" . implode("','", $arr_received_user) . "'";
                    $this->db->Execute("UPDATE t_cores_list SET c_status=0, c_order=9999 
                        WHERE fk_listtype=$v_listtype_id AND c_code NOT IN($arr_received_user) AND c_code LIKE ?"
                            , array("%@" . strtolower($v_member_code)));
                }
            }
            catch (Exception $ex)
            {
                if (function_exists('log_cli'))
                {
                    log_cli($ex->getTraceAsString());
                }
            }
        }
    }
    private function delete_record_history_start($v_fk_village_id,$max_date_update,$unit_code)
    {
        $date_delete = date('Y-m',  strtotime($max_date_update));
        $sql = "DELETE FROM t_ps_record_history_stat 
                    WHERE
                    FK_VILLAGE_ID = ?
                    and DATE_FORMAT(C_HISTORY_DATE,'%Y-%m') = ? 
                    and C_UNIT_CODE = ?
                ";
        $params  = array($v_fk_village_id,$date_delete,$unit_code);
        $this->db->Execute($sql,$params);
    }
    public function insert_member_info($xml_progress_report,$v_fk_village_id,$v_village_name,$history_date,$unit_code)
    {
        //insert cho don vi cap huyen
        @$dom_progress_report = simplexml_load_string($xml_progress_report);
        if(!$dom_progress_report)
        {
            return FALSE;
        }
        $arr_progress_report = $dom_progress_report->xpath('//row');
        if(count($arr_progress_report) == 0)
        {
            return TRUE;
        }
        $sql_tmp = "INSERT INTO t_ps_record_history_stat 
                    (
                        C_UNIT_CODE, 
                        C_SPEC_CODE, 
                        C_CREATE_DATE, 
                        C_HISTORY_DATE, 
                        C_COUNT_KY_TRUOC, 
                        C_COUNT_TIEP_NHAN, 
                        C_COUNT_THU_LY_CHUA_DEN_HAN, 
                        C_COUNT_THU_LY_QUA_HAN, 
                        C_COUNT_TRA_SOM_HAN, 
                        C_COUNT_TRA_DUNG_HAN, 
                        C_COUNT_TRA_QUA_HAN, 
                        C_COUNT_BO_SUNG, 
                        C_COUNT_NVTC, 
                        C_COUNT_TU_CHOI, 
                        C_COUNT_CONG_DAN_RUT, 
                        C_COUNT_CHO_TRA_KY_TRUOC, 
                        C_COUNT_CHO_TRA_TRONG_KY, 
                        C_COUNT_THUE, 
                        C_VILLAGE_NAME, 
                        FK_VILLAGE_ID
                    )
                    VALUES ";
        $v_create_date = date('Y-m-d H:i:s');
        for($i =0;$i<count($arr_progress_report);$i ++)
        {
            $row    =   $arr_progress_report[$i];
            $C_SPEC_CODE                = isset($row->spec_code) ? strval($row->spec_code) : '';
            $C_HISTORY_DATE             = $history_date;
            $C_COUNT_KY_TRUOC           = isset($row->C_COUNT_KY_TRUOC) ? intval($row->C_COUNT_KY_TRUOC) : 0; 
            $C_COUNT_TIEP_NHAN          = isset($row->C_COUNT_TIEP_NHAN) ? intval( $row->C_COUNT_TIEP_NHAN) : 0;
            $C_COUNT_THU_LY_CHUA_DEN_HAN= isset($row->C_COUNT_THU_LY_CHUA_DEN_HAN) ? intval($row->C_COUNT_THU_LY_CHUA_DEN_HAN) : 0;
            $C_COUNT_THU_LY_QUA_HAN     = isset($row->C_COUNT_THU_LY_QUA_HAN) ? intval( $row->C_COUNT_THU_LY_QUA_HAN) : 0;
            $C_COUNT_TRA_SOM_HAN        = isset($row->C_COUNT_TRA_SOM_HAN) ? intval($row->C_COUNT_TRA_SOM_HAN) :0;
            $C_COUNT_TRA_DUNG_HAN       = isset($row->C_COUNT_TRA_DUNG_HAN) ? intval($row->C_COUNT_TRA_DUNG_HAN) : 0; 
            $C_COUNT_TRA_QUA_HAN        = isset($row->C_COUNT_TRA_QUA_HAN) ? intval($row->C_COUNT_TRA_QUA_HAN) :0;
            $C_COUNT_BO_SUNG            = isset($row->C_COUNT_BO_SUNG) ? intval($row->C_COUNT_BO_SUNG) : 0;
            $C_COUNT_NVTC               = isset($row->C_COUNT_NVTC) ? intval($row->C_COUNT_NVTC) : 0;
            $C_COUNT_TU_CHOI            = isset($row->C_COUNT_TU_CHOI) ? intval( $row->C_COUNT_TU_CHOI) : 0; 
            $C_COUNT_CONG_DAN_RUT       = isset($row->C_COUNT_CONG_DAN_RUT) ? intval($row->C_COUNT_CONG_DAN_RUT) : 0; 
            $C_COUNT_CHO_TRA_KY_TRUOC   = isset($row->C_COUNT_CHO_TRA_KY_TRUOC) ? intval($row->C_COUNT_CHO_TRA_KY_TRUOC) :0; 
            $C_COUNT_CHO_TRA_TRONG_KY   = isset($row->C_COUNT_CHO_TRA_TRONG_KY) ? intval($row->C_COUNT_CHO_TRA_TRONG_KY) : 0;
            $C_COUNT_THUE               = isset($row->C_COUNT_THUE) ? intval($row->C_COUNT_THUE ): 0;

            if($i > 0)
            {
                $sql_tmp .= ',(';
            }
            else
            {                    
                $sql_tmp .= '(';
            }
            $sql_tmp .= "'$unit_code'"; //C_UNIT_CODE
            $sql_tmp .= ", '$C_SPEC_CODE'"; //C_SPEC_CODE
            $sql_tmp .= ", '$v_create_date'"; //C_CREATE_DATE
            $sql_tmp .= ", '$C_HISTORY_DATE'"; //C_HISTORY_DATE
            $sql_tmp .= ", '$C_COUNT_KY_TRUOC'"; //C_COUNT_KY_TRUOC
            $sql_tmp .= ", '$C_COUNT_TIEP_NHAN'"; //C_COUNT_TIEP_NHAN
            $sql_tmp .= ", '$C_COUNT_THU_LY_CHUA_DEN_HAN'"; //C_COUNT_THU_LY_CHUA_DEN_HAN
            $sql_tmp .= ", '$C_COUNT_THU_LY_QUA_HAN'"; //C_COUNT_THU_LY_QUA_HAN
            $sql_tmp .= ", '$C_COUNT_TRA_SOM_HAN'"; //C_COUNT_TRA_SOM_HAN
            $sql_tmp .= ", '$C_COUNT_TRA_DUNG_HAN'"; //C_COUNT_TRA_DUNG_HAN
            $sql_tmp .= ", '$C_COUNT_TRA_QUA_HAN'"; //C_COUNT_TRA_QUA_HAN
            $sql_tmp .= ", '$C_COUNT_BO_SUNG'"; //C_COUNT_BO_SUNG
            $sql_tmp .= ", '$C_COUNT_NVTC'"; //C_COUNT_NVTC
            $sql_tmp .= ", '$C_COUNT_TU_CHOI'"; //C_COUNT_TU_CHOI
            $sql_tmp .= ", '$C_COUNT_CONG_DAN_RUT'"; //C_COUNT_CONG_DAN_RUT
            $sql_tmp .= ", '$C_COUNT_CHO_TRA_KY_TRUOC'"; //C_COUNT_CHO_TRA_KY_TRUOC
            $sql_tmp .= ", '$C_COUNT_CHO_TRA_TRONG_KY'"; //C_COUNT_CHO_TRA_TRONG_KY
            $sql_tmp .= ", '$C_COUNT_THUE'"; //C_COUNT_THUE
            $sql_tmp .= ", '$v_village_name'"; //C_VILLAGE_NAME
            $sql_tmp .= ", '$v_fk_village_id'"; //FK_VILLAGE_ID
            $sql_tmp .= ')';
        }
        $this->db->Execute($sql_tmp);
        return ($this->db->ErrorNo() == 0) ? TRUE : FALSE ;
    }

    public function insert_internet_record_info($xml_internet_record)
    {
        $dom = simplexml_load_string($xml_internet_record);

        $processing = xpath($dom, '//data/processing', XPATH_STRING);
        $arr_processing = json_decode(htmlspecialchars_decode($processing));

        $today_return = xpath($dom, '//data/today_return', XPATH_STRING);
        $arr_today_return = json_decode(htmlspecialchars_decode($today_return));

        //Danh sach ho so dang xu ly
        foreach ($arr_processing as $processing)
        {
            $sql = "UPDATE t_ps_record ";
            $sql .= " SET ";
            $sql .= " FK_RECORD_TYPE = '" . $processing["FK_RECORD_TYPE"] . "'";
            $sql .= ",C_RECORD_NO = '" . $processing["C_RECORD_NO"] . "'";
            $sql .= ",C_RECEIVE_DATE = '" . $processing["C_RECEIVE_DATE"] . "'";
            $sql .= ",C_RETURN_DATE = '" . $processing["C_RETURN_DATE"] . "'";
            $sql .= ",C_RETURN_PHONE_NUMBER = '" . $processing["C_RETURN_PHONE_NUMBER"] . "'";
            $sql .= ",C_XML_DATA = '" . $processing["C_XML_DATA"] . "'";
            $sql .= ",C_XML_PROCESSING = '" . $processing["C_XML_PROCESSING"] . "'";
            $sql .= ",C_DELETED = '" . $processing["C_DELETED"] . "'";
            $sql .= ",C_CLEAR_DATE = '" . $processing["C_CLEAR_DATE"] . "'";
            $sql .= ",C_XML_WORKFLOW = '" . $processing["C_XML_WORKFLOW"] . "'";
            $sql .= ",C_RETURN_EMAIL = '" . $processing["C_RETURN_EMAIL"] . "'";
            $sql .= ",C_REJECTED = '" . $processing["C_REJECTED"] . "'";
            $sql .= ",C_CITIZEN_NAME = '" . $processing["C_CITIZEN_NAME"] . "'";
            $sql .= ",C_REJECT_DATE = '" . $processing["C_REJECT_DATE"] . "'";
            $sql .= ",FK_VILLAGE_ID = '" . $processing["FK_VILLAGE_ID"] . "'";
            $sql .= ",C_BIZ_DAYS_EXCEED = '" . $processing["C_BIZ_DAYS_EXCEED"] . "'";
            $sql .= ",C_REJECT_REASON = '" . $processing["C_REJECT_REASON"] . "'";
            $sql .= ",C_BIZ_DONE_DATE = '" . $processing["C_BIZ_DONE_DATE"] . "'";
            $sql .= " WHERE C_RECORD_NO = '" . $processing["C_RECORD_NO"] . "'";

            $this->db->Execute($sql);
        }
        //Danh sach ho moi tra ket qua trong ngay
        foreach ($arr_today_return as $today_return)
        {
            $sql = "UPDATE t_ps_record ";
            $sql .= " SET ";
            $sql .= " FK_RECORD_TYPE = '" . $today_return["FK_RECORD_TYPE"] . "'";
            $sql .= ",C_RECORD_NO = '" . $today_return["C_RECORD_NO"] . "'";
            $sql .= ",C_RECEIVE_DATE = '" . $today_return["C_RECEIVE_DATE"] . "'";
            $sql .= ",C_RETURN_DATE = '" . $today_return["C_RETURN_DATE"] . "'";
            $sql .= ",C_RETURN_PHONE_NUMBER = '" . $today_return["C_RETURN_PHONE_NUMBER"] . "'";
            $sql .= ",C_XML_DATA = '" . $today_return["C_XML_DATA"] . "'";
            $sql .= ",C_XML_PROCESSING = '" . $today_return["C_XML_PROCESSING"] . "'";
            $sql .= ",C_DELETED = '" . $today_return["C_DELETED"] . "'";
            $sql .= ",C_CLEAR_DATE = '" . $today_return["C_CLEAR_DATE"] . "'";
            $sql .= ",C_XML_WORKFLOW = '" . $today_return["C_XML_WORKFLOW"] . "'";
            $sql .= ",C_RETURN_EMAIL = '" . $today_return["C_RETURN_EMAIL"] . "'";
            $sql .= ",C_REJECTED = '" . $today_return["C_REJECTED"] . "'";
            $sql .= ",C_CITIZEN_NAME = '" . $today_return["C_CITIZEN_NAME"] . "'";
            $sql .= ",C_REJECT_DATE = '" . $today_return["C_REJECT_DATE"] . "'";
            $sql .= ",FK_VILLAGE_ID = '" . $today_return["FK_VILLAGE_ID"] . "'";
            $sql .= ",C_BIZ_DAYS_EXCEED = '" . $today_return["C_BIZ_DAYS_EXCEED"] . "'";
            $sql .= ",C_REJECT_REASON = '" . $today_return["C_REJECT_REASON"] . "'";
            $sql .= ",C_BIZ_DONE_DATE = '" . $today_return["C_BIZ_DONE_DATE"] . "'";
            $sql .= " WHERE C_RECORD_NO = '" . $today_return["C_RECORD_NO"] . "'";
            $this->db->Execute($sql);
        }
    }

}

?>
