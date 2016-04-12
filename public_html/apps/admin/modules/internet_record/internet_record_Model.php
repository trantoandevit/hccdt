<?php

if (!defined('SERVER_ROOT'))
    exit('No direct script access allowed');

class internet_record_Model extends Model
{

    function __construct()
    {
        parent::__construct();
    }
    /**
     * lay tat ca ho so
     * @return type
     */
    public function qry_all_record($record_type_id = '',$member_id = '')
    {
        $condition = '';
        if($record_type_id != '')
        {
            $condition = " And FK_RECORD_TYPE = $record_type_id";
        }
        
        if($member_id != '')
        {
            $condition = " And FK_VILLAGE_ID = $member_id";
        }
        
        $sql = " SELECT
                    R.PK_RECORD,
                    R.C_RECORD_NO,
                    R.C_RETURN_EMAIL,
                    R.C_CITIZEN_NAME,  
                    (SELECT
                       C_NAME
                     FROM t_ps_member
                     WHERE PK_MEMBER = R.FK_VILLAGE_ID) AS C_MEMBER_NAME,
                        CASE 
                                WHEN R.C_DELETED = 1 THEN 
                                'Đã bị xóa'
                                WHEN ISNULL(C_RECORD_NO) THEN
                                'Chưa xác nhận'
                                WHEN ISNULL(C_PROCESSING_RECORD) THEN
                                'Một cửa chưa xác nhận'
                        ELSE R.C_PROCESSING_RECORD 
                        END AS C_PROCESSING_RECORD
                  FROM t_ps_record R
                  WHERE (1 > 0) $condition
                  ORDER BY C_STATUS,PK_RECORD";
        return $this->db->getAll($sql);
    }
    
    /**
     * lay thong bao
     * @return type
     */
    public function qry_all_notice($member_id = '')
    {
        $condition = '';
        if($member_id != '')
        {
            $condition = " And FK_VILLAGE_ID = $member_id";
        }
        $sql = "SELECT
                    FK_RECORD_TYPE,
                    COUNT(FK_RECORD_TYPE) AS C_COUNT,
                    (SELECT C_CODE FROM t_ps_record_type WHERE PK_RECORD_TYPE = R.FK_RECORD_TYPE) AS C_RECORD_TYPE_CODE,
                    (SELECT CONCAT(C_CODE,' - ' ,C_NAME) FROM t_ps_record_type WHERE PK_RECORD_TYPE = R.FK_RECORD_TYPE) AS C_RECORD_TYPE_NAME
                  FROM t_ps_record R  Where (1>0) And C_STATUS = 0 $condition
                  GROUP BY FK_RECORD_TYPE";
        return $this->db->getAll($sql);
    }
    
    /**
     * lay tat ca don vi truc thuc
     * @return type
     */
    public function qry_all_member()
    {
        $sql = "SELECT
                    PK_MEMBER,
                    C_NAME
                  FROM t_ps_member
                  WHERE (FK_MEMBER < 1
                       OR FK_MEMBER IS null) AND C_STATUS = 1";
        $MODEL_DATA['arr_all_district'] = $this->db->getAll($sql);
        
        $sql = "SELECT
                    PK_MEMBER,
                    C_NAME,
                    FK_MEMBER
                  FROM t_ps_member
                  WHERE FK_MEMBER > 0 AND C_STATUS = 1";
        
        $MODEL_DATA['arr_all_village'] = $this->db->getAll($sql);
        
        return $MODEL_DATA;
    }
    /**
     * lấy chi tiết thông tin hồ sơ được nộp thông qua cổng
     * @param type $v_record_id
     * @return type
     */
    public function qry_single_record($v_record_id)
    {
        if(!is_numeric($v_record_id) or $v_record_id < 1)
        {
            return array();
        }
        $stmt = "SELECT
                    R.PK_RECORD,
                    R.C_CITIZEN_ADDRESS,
                    RT.C_CODE AS C_RECORD_TYPE_CODE,
                    RT.C_NAME AS C_RECORD_TYPE_NAME,
                    (SELECT C_NAME FROM t_cores_list WHERE C_CODE = RT.C_SPEC_CODE) AS C_SPEC_NAME,
                    (SELECT C_NAME FROM t_ps_member WHERE PK_MEMBER = R.FK_VILLAGE_ID) AS C_MEMBER_NAME,
                    R.C_RECORD_NO,
                    R.C_CITIZEN_NAME,
                    R.C_RETURN_PHONE_NUMBER,
                    R.C_RETURN_EMAIL,
                    R.C_NOTE,
                    (SELECT
                            CONCAT('<data>',GROUP_CONCAT(
                                          CONCAT('<item c_file_name=\"',C_FILE_NAME,'\" ><![CDATA[',C_NAME,']]>','</item>')
                                           SEPARATOR ' ')
                                  ,'</data>')
                          FROM t_ps_record_file
                          WHERE FK_RECORD = R.PK_RECORD) AS C_XML_FILE
                  FROM t_ps_record R
                    LEFT JOIN t_ps_record_type RT
                    ON R.FK_RECORD_TYPE = RT.PK_RECORD_TYPE
                  WHERE PK_RECORD = ?";
        return $this->db->getRow($stmt, array($v_record_id));
    }
    
    /**
     * 
     * @param type $v_list_delete
     */
    public function do_delete_record($v_list_delete)
    {
        $arr_id = explode(',', $v_list_delete);
        
        foreach($arr_id as $id)
        {
            if(!is_numeric($id) && $id > 0)
            {
                continue;
            }
            //xoa file
            $stmt       = "SELECT FK_CITIZEN FROM t_ps_record WHERE PK_RECORD = ?";
            $citizen_id = $this->db->GetOne($stmt,array($id));
            if($citizen_id <=0 )
            {
                //      Cong dan khong dang ky nop ho so- Xoa toan bo thong tin
                //xoa file
                $stmt = "SELECT
                            C_FILE_NAME
                          FROM t_ps_record_file rf
                          WHERE FK_RECORD = ? ";
                $arr_file_name = $this->db->getCol($stmt,array($id));

                foreach($arr_file_name as $v_file_name)
                {
                    $v_path_file = _CONST_RECORD_FILE . $v_file_name;

                    if(is_file($v_path_file))
                    {
                        unlink($v_path_file);
                    }
                }

                //xoa record file
                $stmt = "DELETE FROM t_ps_record_file WHERE FK_RECORD = ?";
                $this->db->Execute($stmt,array($id));
                //xoa record
                $stmt = "DELETE FROM t_ps_record WHERE PK_RECORD = ?";
                $this->db->Execute($stmt,array($id));
            }
            else
            {
                 //Cong dan nop ho so co dang ky - doi trang thai de cong dan xem lich su nop
                $stmt = "UPDATE t_ps_record
                                SET C_DELETED = '1'
                                ,C_PROCESSING_RECORD = 'Đã bị xóa'
                                WHERE PK_RECORD = ?";
                $this->db->Execute($stmt,array($id));
            }
        }
        $this->exec_done($this->goback_url);
    }
    
    /**
     * 1.   Gửi thông tin hồ sơ qua email.
     * 2.   Kích hoạt webservice phần mềm một cửa đọc Email nhận hồ sơ
     * @param int $v_record_id mã hồ sơ
     */
    public function do_confirm_record($v_record_id)
    {
        $MODE_DATA['error']   = null;
        $MODE_DATA['success'] = 'fail';
        //tao instance mailsender
        $mail_sender = new MailSender();
        
        //tao subject send email
        $subject = 'Ho so dich vu cong cap do 3 '. Date('Ymd h:i:s');
        $mail_sender->subject = $subject;
        
        //khoi tao lai thong tin mail
        $mail_sender->init();
        
        #Lấy danh sách file dinh kèm của hồ sơ $record_id
        $arr_file = $this->get_file_attach($v_record_id);
        //dinh kem file
        foreach($arr_file as $file_name)
        {
            $path = _CONST_RECORD_FILE . $file_name;
            if(file_exists($path))
            {
                $mail_sender->Attach_file($path);
            }
            else
            {
                $MODE_DATA['error']  = "Thất bại gửi hồ sơ có ID:$v_record_id [Không tìm lấy tập tin đính kèm '$file_name']" ;
                $MODE_DATA['success']= 'fail';
                return $MODE_DATA;
            }
        }
        //Lấy thông tin hồ sơ và thông tin service của đơn vị tiếp nhận
        $arr_data = $this->get_xml_record_data($v_record_id);
        
        #Email nhận nhận thông tin hồ sơ
        $exchange_email = $arr_data['email_exchange'];
        //Thông tin hồ sơ
        $xml_string     = $arr_data['xml_string'];
        
        #Thông tin service
        $ws_location    = $arr_data['ws_location'];
        $ws_uri         = $arr_data['ws_uri'];
                
        //Đính kèm thông tin hồ sơ dưới dạng On The Fly
        $mail_sender->OnTheFly_Attach($xml_string,'record_info.xml');
        
        //gui mail
        $sendmail_result = $mail_sender->SendMail($exchange_email,'internet record');
        
        //kiem tra ket qua gui mail
        if($sendmail_result)
        {
            $function = 'read_mail';
            $arr_param = array($subject,'DVC',$v_record_id,'INTERNET_RECORD');
            $recursive = true;
            try
            {
                //goi services lay mail
                $client = new SoapClient($ws_location.'?wsdl', array('location' => $ws_location,
                                                'uri' => $ws_uri));
                $response = call_soap_service($client,$function,$arr_param,$recursive,5);
                if(!$response)
                {
                    throw new Exception('Xảy ra lỗi trong quá trình gửi gửi.'); 
                }
            }
            catch (Exception $ex)
            {
                $MODE_DATA['error'] = $ex->getMessage();
            }
            $MODE_DATA['error']  = "Chuyển hồ sơ [ID:$v_record_id] thành công." ;
            $MODE_DATA['success']= 'done';
            return $MODE_DATA;
        }
        $MODE_DATA['success']= 'fail';
        return $MODE_DATA;
         
    }
    
    /**
     * lay danh sach file dinh kem cua hs 
     * @param type $record_id
     * @return type
     */
    public function get_file_attach($record_id)
    {
        if(!is_numeric($record_id) or  $record_id < 1)
        {
            return array();
        }
        
        $stmt = "SELECT
                    C_FILE_NAME
                  FROM t_ps_record_file
                  WHERE FK_RECORD = ?";
        return $this->db->getCol($stmt,array($record_id));
    }
    /**
     * lay du lieu xml data de gui mail
     * @param type $record_id
     * @return type
     */
    public function get_xml_record_data($record_id)
    {
        if(!is_numeric($record_id) or  $record_id < 1)
        {
            return array();
        }
        //lay email nhan thong tin
        $stmt = "SELECT
                    C_EXCHANGE_EMAIL,
                    ExtractValue(C_XML_DATA,'//item[@id=\"location\"]/value') as C_LOCATION,
                    ExtractValue(C_XML_DATA,'//item[@id=\"uri\"]/value') AS C_URI
                  FROM t_ps_member
                  WHERE PK_MEMBER = (SELECT
                                       FK_VILLAGE_ID
                                     FROM t_ps_record
                                     WHERE PK_RECORD = ?)";
        
        $arr_exchange_info = $this->db->getRow($stmt,array($record_id));
        $MODEL_DATA['email_exchange'] = $arr_exchange_info['C_EXCHANGE_EMAIL'];
        $MODEL_DATA['ws_location'] = $arr_exchange_info['C_LOCATION'];
        $MODEL_DATA['ws_uri'] = $arr_exchange_info['C_URI'];
        
        //lay xml data
        $stmt = "SELECT
                    (SELECT
                       C_CODE_MAPPING
                     FROM t_ps_record_type_member
                     WHERE FK_MEMBER = R.FK_VILLAGE_ID
                         AND FK_RECORD_TYPE = R.FK_RECORD_TYPE
                     LIMIT 1) AS C_RECORD_TYPE_CODE,
                    R.C_RECORD_NO,
                    R.C_CITIZEN_ADDRESS,
                    R.C_RECEIVE_DATE,
                    R.C_RETURN_PHONE_NUMBER,
                    R.C_RETURN_EMAIL,
                    R.C_NOTE,
                    R.C_CITIZEN_NAME,
                    RT.C_SPEC_CODE
                  FROM t_ps_record R
                    LEFT JOIN t_ps_record_type RT
                      ON R.FK_RECORD_TYPE = RT.PK_RECORD_TYPE
                  WHERE R.PK_RECORD = ?";
        $arr_data = $this->db->getRow($stmt,array($record_id));
        
        $arr_file = $this->get_file_attach($record_id);
        $xml_attach = '';
        
        $xml_data = '<data>';
        $xml_data .= '<C_RECORD_TYPE_CODE>' . $arr_data['C_RECORD_TYPE_CODE'].'</C_RECORD_TYPE_CODE>';
//        $xml_data .= '<C_RECORD_NO>' . $arr_data['C_RECORD_NO'].'</C_RECORD_NO>';
        $xml_data .= '<C_RECORD_NO></C_RECORD_NO>';
        $xml_data .= '<C_RECEIVE_DATE>' . $arr_data['C_RECEIVE_DATE'].'</C_RECEIVE_DATE>';
        $xml_data .= '<C_RETURN_PHONE_NUMBER>' . $arr_data['C_RETURN_PHONE_NUMBER'].'</C_RETURN_PHONE_NUMBER>';
        $xml_data .= '<C_RETURN_EMAIL>' . $arr_data['C_RETURN_EMAIL'].'</C_RETURN_EMAIL>';
        $xml_data .= '<C_NOTE>' . $arr_data['C_NOTE'].'</C_NOTE>';
        $xml_data .= '<C_CITIZEN_NAME>' . $arr_data['C_CITIZEN_NAME'].'</C_CITIZEN_NAME>';
        $xml_data .= '<C_SPEC_CODE>' . $arr_data['C_SPEC_CODE'].'</C_SPEC_CODE>';
        $xml_data .= '<C_CITIZEN_ADDRESS><![CDATA[' . $arr_data['C_CITIZEN_ADDRESS'].']]></C_CITIZEN_ADDRESS>';
        foreach($arr_file as $v_file_name)
        {
            $xml_attach .= '<file>' .$v_file_name.'</file>';
        }
        $xml_data .= '<ATTACHS>'.$xml_attach.'</ATTACHS>';
        $xml_data .= '</data>';
        
        $xml_data = xml_add_declaration($xml_data);
        $MODEL_DATA['xml_string'] = $xml_data;
        return $MODEL_DATA;
    }
    
    /**
     * thuc hien chuyen ho so về 1 cửa
     * @param type $record_id
     * @param type $client
     * @return boolean
     */
    public function do_confirm($record_id,$client)
    {
        if(!is_numeric($record_id) or  $record_id < 1)
        {
            return false;
        }
        
        $function = 'receive_internet_record';
        $arr_param = array('DVC',$record_id);
        $receive_record_result = $client->__soapCall($function, $arr_param);
        
        if($receive_record_result != '0')
        {
            //xoa file
            $stmt       = "SELECT FK_CITIZEN FROM t_ps_record WHERE PK_RECORD = ?";
            $citizen_id = $this->db->GetOne($stmt,array($record_id));
            if($citizen_id <=0 )
            {

                $arr_file_name = $this->get_file_attach($record_id);
                foreach($arr_file_name as $v_file_name)
                {
                    $v_path_file = _CONST_RECORD_FILE . $v_file_name;

                    if(is_file($v_path_file))
                    {
                        unlink($v_path_file);
                    }
                }
                //Ho so cua cong dan nop ko dang ky
                $stmt = "DELETE FROM t_ps_record_file WHERE FK_RECORD = ?";
                $this->db->Execute($stmt,array($record_id));

                $stmt = "DELETE FROM t_ps_record WHERE PK_RECORD = ?";
                $this->db->Execute($stmt,array($record_id));
            }
            else
            {
                 //Xu ly ho so cua cong dan nop co dang ky
                $stmt = "UPDATE t_ps_record
                                SET C_STATUS = '1'
                                WHERE PK_RECORD = ?";
                $this->db->Execute($stmt,array($record_id));
                
                $sql = "UPDATE t_ps_record SET C_RECORD_NO = '$receive_record_result' WHERE PK_RECORD = '$record_id'";
                $this->db->Execute($sql);
            
                /// Xoa file : Vi chua lam chuc năng xem file nen ko can luu lai
                // $arr_file_name = $this->get_file_attach($record_id);
                // foreach($arr_file_name as $v_file_name)
                // {
                    // $v_path_file = _CONST_RECORD_FILE . $v_file_name;

                    // if(is_file($v_path_file))
                    // {
                        // unlink($v_path_file);
                    // }
                // }
                // //Ho so cua cong dan nop ko dang ky
                // $stmt = "DELETE FROM t_ps_record_file WHERE FK_RECORD = ?";
                // $this->db->Execute($stmt,array($record_id));
            }
            
            
            return TRUE;
        }
        return FALSE;
    }
    
    /**
     * Lay danh sach ho so dang xy ly[chua xoa, chua tu choi, da xác nhận về một cửa]
     */
    public function qry_all_record_dang_xu_ly()
    {
        $sql = "SELECT
                    C_RECORD_NO,
                    (SELECT
                       C_XML_DATA
                     FROM t_ps_member
                     WHERE PK_MEMBER = r.FK_VILLAGE_ID) AS C_XML_DATA
                  FROM t_ps_record r
                  WHERE C_DELETED <> 1
                      AND C_RECORD_NO IS NOT NULL";
        $response = $this->db->GetAll($sql);
        return ($this->db->ErrorNo() == 0) ? $response : array();
    }
    
    /*
     * Cap nhat trang thai xu ly ho so
     */
    public function update_process_record($record_no,$deleted,$processing,$v_receive_date,$v_return_date)
    {
        if($v_receive_date != '')
        {
            $sql_sub = ",C_RECEIVE_DATE = '$v_receive_date'";
        }
        if($v_return_date != '')
        {
            $sql_sub .= ",C_RETURN_DATE = '$v_return_date'";
        }
        $sql = "UPDATE t_ps_record
                      SET C_DELETED = ?,
                      C_PROCESSING_RECORD = ?
                      $sql_sub
                WHERE C_RECORD_NO = ?;";
        $this->db->Execute($sql,array($deleted,$processing,$record_no));
        return ($this->db->ErrorNo() == 0) ? TRUE  : FALSE;
    }
    
}
//add Cdata to xml
class SimpleXMLExtended extends SimpleXMLElement // http://coffeerings.posterous.com/php-simplexml-and-cdata
{
  public function addCData($cdata_text)
  {
    $node= dom_import_simplexml($this); 
    $no = $node->ownerDocument; 
    $node->appendChild($no->createCDATASection($cdata_text)); 
  } 
}