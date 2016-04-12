<?php

if (!defined('SERVER_ROOT'))
    exit('No direct script access allowed');

class record_type_Model extends Model
{

    function __construct()
    {
        parent::__construct();
    }

    public function qry_all_record_type($arr_filter)
    {
        //Phan trang
        page_calc($v_start, $v_end);

        $condition_query = '';
        $v_filter               = $arr_filter['txt_filter'];
        $v_status               = $arr_filter['sel_status'];
        $v_member               = $arr_filter['sel_member'];
        $v_internet             = $arr_filter['chk_internet'];
       
        //loc theo ten hoac ma
        if ($v_filter != '')
        {
            $condition_query .= " And (RT.C_CODE like '%$v_filter%' Or RT.C_NAME like '%$v_filter%')";
        }
        //loc theo trang thai
        if ($v_status != -1)
        {
            $condition_query .= " And C_STATUS = $v_status";
        }
        //loc theo don vi tiep nhan hs
        if($v_member != 0)
        {
            $condition_query .= " And PK_RECORD_TYPE IN (SELECT
                                                            FK_RECORD_TYPE
                                                          FROM t_ps_record_type_member
                                                          WHERE FK_MEMBER = $v_member)";
        }
        
        //loc theo tthc nop qua internet
        if($v_internet == 1)
        {
            $condition_query .= " And PK_RECORD_TYPE IN (SELECT
                                                                DISTINCT FK_RECORD_TYPE
                                                              FROM t_ps_record_type_member)";
        }
        
        //Dem tong ban ghi
        $sql_count_record = "Select Count(*) From t_ps_record_type RT Where (1 > 0) $condition_query";

        $v_start = $v_start - 1;
        $v_limit = $v_end - $v_start;

        $sql = "Select RT.*
                    , ($sql_count_record) TOTAL_RECORD
                FROM t_ps_record_type RT
                Where (1 > 0) $condition_query
                Order By C_CODE
                Limit $v_start, $v_limit";

        return $this->db->getAll($sql);

    }

    public function delete_record_type($arr_filter = array())
    {
        $v_record_type_id_list = get_post_var('hdn_item_id_list',0);
        $arr_record_type_id = explode(',', $v_record_type_id_list);
        $message = '';
        $count_error = 0;
        
        foreach($arr_record_type_id as $record_type_id)
        {
            //kiem tra so hs thuoc tthc
            $sql = "Select COUNT(*) From t_ps_record Where FK_RECORD_TYPE = $record_type_id";
            $v_check = $this->db->GetOne($sql);
            if($v_check > 0) //thuc hien tao message error
            {
                $count_error++;
                $sql = "SELECT C_CODE FROM t_ps_record_type WHERE PK_RECORD_TYPE = $record_type_id";
                $record_type_code = $this->db->getOne($sql);
                if($count_error <= 1)
                {
                    $message .= 'TTHC mã: ' . $record_type_code;
                }
                else
                {
                    $message .= ', ' . $record_type_code;
                }
                
                continue;
            }
            else //thuc hien xoa
            {
                //xoa file
                $sql = "SELECT
                            C_XML_DATA
                          FROM t_ps_record_type
                          WHERE PK_RECORD_TYPE = $record_type_id";
                $xml_data = $this->db->getOne($sql);
                $dom = simplexml_load_string($xml_data);
                $r = $dom->xpath('//media/file');
                $v_list_file_key = implode($r, ',');
                $this->delete_file_tempate_type($record_type_id, $v_list_file_key);
                
                //xoa record type
                $sql = "Delete From t_ps_record_type
                        Where PK_RECORD_TYPE = $record_type_id";
                $this->db->Execute($sql);
                //xoa t_ps_record_type_memeber
                $sql = "Delete From t_ps_record_type_member
                        Where FK_RECORD_TYPE = $record_type_id";
                $this->db->Execute($sql);
            }
        }
        
        //mot so TTHC van con hs tiep nhan
        if(strlen($message) > 0)
        {
            $message .= " vẫn còn hồ sơ tiếp nhận";
            $this->exec_fail($this->goback_url, $message, $arr_filter);
        }
        else
        {
            //hoan thanh xoa
            $this->exec_done($this->goback_url, $arr_filter);
        }
    }
    /**
     * lay thong tin cua tthc
     * @param type $p_record_type_id
     * @return type
     */
    public function qry_single_record_type($p_record_type_id)
    {
        if ($p_record_type_id < 1)
        {
            return array('C_ORDER' => $this->get_max('t_ps_record_type', 'C_ORDER') + 1);
        }

        $stmt   = 'Select
                        PK_RECORD_TYPE
                      ,C_CODE
                      ,C_NAME
                      ,C_XML_DATA
                      ,C_ORDER
                      ,C_STATUS
                      ,C_SPEC_CODE
                      ,C_SCOPE
                 From t_ps_record_type RT Where PK_RECORD_TYPE=?';
        $params = array($p_record_type_id);
        return $this->db->GetRow($stmt, $params);
    }
    public function qry_record_type_member($v_record_type_id)
    {
        if($v_record_type_id <= 0)
        {
            return array();
        }
        
        $stmt = "SELECT
                    FK_MEMBER
                  FROM t_ps_record_type_member
                  WHERE FK_RECORD_TYPE = ?";
        return $this->db->getCol($stmt,array($v_record_type_id));
    }
    public function get_all_template_file($v_record_type_id)
    {
        $arr_single_record_type = $this->qry_single_record_type($v_record_type_id);
        
        $xml_data     = isset($arr_single_record_type['C_XML_DATA']) ? $arr_single_record_type['C_XML_DATA'] : '';
        if(trim($xml_data) == '' OR $xml_data == NULL)
        {
            return;
        }
        $dom          = simplexml_load_string($xml_data);
        
        $v_xpath      = '//data/media/file/text()';
        $r            = $dom->xpath($v_xpath);
        $arr_all_file = array();
        foreach ($r as $item)
        {
            $item = (string)$item ;

            if(trim($item) != '' && $item != NULL)
            {   
                $v_path_file = CONST_TYPE_FILE_UPLOAD . 'template_files_types' .DS . $item;
                                
                if(is_file($v_path_file))
                {
                    $arr_string = explode('_', $item,2);
                    $key_file   = isset($arr_string[0]) ? $arr_string[0] : '';
                    $arr_all_file[$item]['file_name'] =  isset($arr_string[1]) ? $arr_string[1] : '';
                    $arr_all_file[$item]['path']      =  $v_path_file;
                    $arr_all_file[$item]['type']      = filetype($v_path_file);
                }
            }            
        }
        return $arr_all_file;
    }
    
    public function qry_all_member($v_record_type_id = 0)
    {
        $group_by =  $condition = '';
        if($v_record_type_id >0)
        {
            $condition = " AND FK_RECORD_TYPE = '$v_record_type_id' ";
        }
        else
        {
            $group_by  = "GROUP BY PK_MEMBER";
        }
        $sql = "SELECT
                        PK_MEMBER,
                        m.C_NAME,
                        C_CODE_MAPPING
                      FROM t_ps_member m
                        LEFT JOIN (SELECT * FROM t_ps_record_type_member WHERE 1=1 $condition) tm
                          ON PK_MEMBER = tm.FK_MEMBER
                      WHERE (m.FK_MEMBER < 1
                              OR m.FK_MEMBER IS NULL)
                          AND m.C_STATUS = 1 $group_by";
        $MODEL_DATA['arr_all_district'] = $this->db->getAll($sql);
        $sql = "SELECT
                    PK_MEMBER,
                    C_NAME,
                    m.FK_MEMBER,
                    C_CODE_MAPPING
                  FROM t_ps_member m
                   LEFT JOIN (SELECT * FROM t_ps_record_type_member WHERE 1=1 $condition) tm
                          ON PK_MEMBER = tm.FK_MEMBER
                  WHERE m.FK_MEMBER > 0 AND C_STATUS = 1 $group_by";
        $MODEL_DATA['arr_all_village'] = $this->db->getAll($sql);
        
        return $MODEL_DATA;
    }
    
    public function update_record_type($arr_filter)
    {
        $v_record_type_id   = get_post_var('hdn_item_id', 0);
        $v_code             = trim(get_post_var('txt_code', ''));
        $v_name             = get_post_var('txt_name', '');       
        $v_xml_data         = get_post_var('XmlData', '<data></data>', FALSE);
        $v_order            = get_post_var('txt_order', 1);
        $v_spec_code        = get_post_var('sel_spec_code', 'XX');
        $v_scope        = get_post_var('rd_scope', 3);
        
        $arr_file           = isset($_FILES['uploader']['name']) ? $_FILES['uploader']['name'] : array();
        
        $v_list_file_key    = get_post_var('hdn_delete_file_list_id',''); 
        $v_status             = isset($_POST['chk_status']) ? 1 : 0;
        $v_save_and_addnew    = isset($_POST['chk_save_and_addnew']) ? 1 : 0;
        
        $arr_member             = isset($_POST['chk_member']) ? $_POST['chk_member'] : array();
        $arr_code_mapping             = get_post_var('code_mapping',array(),FALSE);
        
        $arr_filetype_name_new  = isset($arr_filter['txt_file_type_new']) ?$arr_filter['txt_file_type_new'] : array();
        $v_filetype_list_old_id = isset($arr_filter['hdn_file_type_list_old_id']) ?$arr_filter['hdn_file_type_list_old_id'] : '';
        $arr_filetype_list_old_id = explode(',', $v_filetype_list_old_id) ;
        //Kiem tra trung ma
        $sql = "Select Count(*)
                From t_ps_record_type
                Where C_CODE='$v_code' And PK_RECORD_TYPE <> $v_record_type_id";
        if ($this->db->getOne($sql) > 0)
        {
            $this->exec_fail($this->goback_url, 'Mã loại hồ sơ đã tồn tại', $arr_filter);
            return;
        }

        //Kiem tra trung ten
        $sql = "Select Count(*)
                From t_ps_record_type
                Where C_NAME='$v_name' And PK_RECORD_TYPE <> $v_record_type_id";
        
        if ($this->db->getOne($sql) > 0)
        {
            $this->exec_fail($this->goback_url, 'Tên loại hồ sơ đã tồn tại', $arr_filter);
            return;
        }
        
        if ($v_record_type_id < 1) //Insert
        {
            // add dom media vao xml_data khi them moi
            $doc = new SimpleXMLExtended($v_xml_data);
            $media = $doc->addChild('media');
            $list_filetype_name = $doc->addChild('list_file');
            
            for($i =0;$i <count($arr_filetype_name_new);$i ++)
            {
                $v_filetye_name  = $arr_filetype_name_new[$i];
                $v_not_null      = isset($_POST['chk_file_type_old'][$i]) ? 1 : 0;
                $v_item = $list_filetype_name->addChild('item');
                $v_item->addAttribute('id',$i + 1);
                $v_item->addAttribute('not_null',$v_not_null);
                $v_item->addChild('value')->addCdata($v_filetye_name);
            }
            $v_xml_data = $doc->asXML();
            
            $stmt   = 'Insert Into t_ps_record_type (C_CODE, C_NAME, C_XML_DATA,C_STATUS,C_ORDER, C_SPEC_CODE) Values (?,?,?,?,?,?)';
            $params = array($v_code, $v_name, $v_xml_data, $v_status, $v_order, $v_spec_code);
            $this->db->Execute($stmt, $params);

            $v_record_type_id = $this->get_last_inserted_id('t_ps_record_type', 'PK_RECORD_TYPE');
            
        }
        else //Update
        {
            //Lay xml hien tai
            $xml_data_current = $this->db->GetOne("select C_XML_DATA from t_ps_record_type where PK_RECORD_TYPE = ? ",array($v_record_type_id));
            $xml_data_current = trim($xml_data_current) != '' ?  $xml_data_current : '<data />';
            $dom              = simplexml_load_string($xml_data_current);
            
            //Lay danh sach file name dang luu tru
            $v_xpath_file          = '//data/media/file';
            $arr_results           = $dom->xpath($v_xpath_file);
            $doc = new SimpleXMLElement($v_xml_data);
            $media = $doc->addChild('media');
            foreach ($arr_results as $item)
            {
                $media->addChild('file',$item);
            }
            $v_xml_data = $doc->asXML();
            
            // lay xml luu tru ma linh vuc
            $v_xpath_item          = '//item/value';
            $obj_listype_code_spcode   = $dom->xpath($v_xpath_item);
            $v_listype_code_spcode = isset($obj_listype_code_spcode[0]) ? (string)$obj_listype_code_spcode[0] : '';
            
            if(trim($v_listype_code_spcode) != '' && $v_listype_code_spcode != null)
            {
                $xml = new SimpleXMLExtended($v_xml_data);
                $xml_crr = $xml->addChild('item');
                $xml_crr->addAttribute('id','sel_spec_code');            
                $xml_crr->addChild('value')->addCData($v_listype_code_spcode);            
                $v_xml_data = $xml->asXML();
            }
            
            //Lay lai danh sach cac filetype_name old
            $v_xpath_filetype_name = "//list_file//item";
            $obj_filetype_name     = $dom->xpath($v_xpath_filetype_name);
            
            unset($xml);
            $xml           = new SimpleXMLExtended($v_xml_data);
            $xml_file_list =  $xml->addChild('list_file');
            //update filetype old
            $filetype_id = 0; // ma cua filetype_name. Chua co thi tao moi
            foreach ($obj_filetype_name as $v_single_filetype)
            {
                $v_filetye_id   = (string)$v_single_filetype->attributes()->id;
                
               
                foreach ($arr_filetype_list_old_id as $key=>$val)
                {
                    if(trim($v_filetye_id) == trim($val))
                    {
                        $v_filetype_name = get_post_var('txt_file_type_old_'.trim($v_filetye_id),'');
                        $v_not_null      = isset($_POST['chk_file_type_old_'.trim($v_filetye_id)]) ? 1 : 0;
                        $v_item = $xml_file_list->addChild('item');
                        $v_item->addAttribute('id',$v_filetye_id);
                        $v_item->addAttribute('not_null',$v_not_null);
                        $v_item->addChild('value')->addCData($v_filetype_name);
                        $filetype_id = ((int)$val > $filetype_id) ? $val : $filetype_id;
                    }
                }
            }
            //addnew 
            for($i =0;$i <count($arr_filetype_name_new);$i ++)
            {
                $filetype_id ++;
                $v_filetye_name  = $arr_filetype_name_new[$i];
                $v_item = $xml_file_list->addChild('item');
                $v_item->addAttribute('id',$filetype_id);
                $v_item->addChild('value')->addCdata($v_filetye_name);
            }
            $v_xml_data = $xml->asXML();
            
            $stmt   = 'Update t_ps_record_type Set
                        C_CODE=?
                        ,C_NAME=?
                        ,C_XML_DATA=?
                        ,C_STATUS=?
                        ,C_ORDER=?
                        ,C_SPEC_CODE=?
                        ,C_SCOPE=?
                    Where PK_RECORD_TYPE=?';
            $params = array(
                $v_code,
                $v_name,
                $v_xml_data,
                $v_status,
                $v_order,
                $v_spec_code,
                $v_scope,
                $v_record_type_id
            );

            $this->db->Execute($stmt, $params);
        }

        $this->ReOrder('t_ps_record_type', 'PK_RECORD_TYPE', 'C_ORDER', $v_record_type_id, $v_order);
        //them du lieu vao bang t_ps_record_type_member
        //1. xoa tat ca tiep nhan hs truc tuyen
        $stmt = "DELETE
                FROM t_ps_record_type_member
                WHERE FK_RECORD_TYPE = ?";
        $this->db->Execute($stmt,array($v_record_type_id));
        //2. them lai don vi tiep nhan hs truc tuyen
        $sql = '';
        
        for($o = 0;$o < count($arr_member);$o++)
        {
            if($sql == '')
            {
                $sql .= "($arr_member[$o],$v_record_type_id,'$arr_code_mapping[$o]')";
            }
            else
            {
                $sql .= ",($arr_member[$o],$v_record_type_id,'$arr_code_mapping[$o]')";
            }
        }
        
        $sql = "INSERT INTO t_ps_record_type_member
                (FK_MEMBER,
                 FK_RECORD_TYPE,C_CODE_MAPPING)
                VALUES " . $sql;
        $this->db->Execute($sql);
        
        //Them file dinh kem
        if(count($arr_file) > 0)
        {
            $this->update_template_file_type($v_record_type_id,$arr_file);
        }
        //xoa file dinh kem da chon xoa
        if(trim($v_list_file_key) != '')
        {
            $this->delete_file_tempate_type($v_record_type_id,$v_list_file_key);
        }
        
        //Luu dieu kien loc
        $arr_filter = get_filter_condition(array('txt_filter', 'sel_goto_page', 'sel_rows_per_page'));
        
        //Done
        if ($v_save_and_addnew > 0)
        {
            $this->exec_done($this->goforward_url, $arr_filter);
        }
        else
        {
           $this->exec_done($this->goback_url, $arr_filter);
        }
    }
    
    function update_template_file_type($v_record_type_id,$arr_file)
    {
        $this->db->SetFetchMode(ADODB_FETCH_DEFAULT); 
        //Lay danh sach cac file da tai len
       $stmt = "SELECT ExtractValue(C_XML_DATA,'count(/data/media)') as C_COUNT_MEDIA, C_XML_DATA
                    FROM t_ps_record_type 
                    WHERE PK_RECORD_TYPE = ?";
        $resluts = $this->db->GetRow($stmt,array($v_record_type_id));
        
        $v_xml_data       = $resluts['C_XML_DATA'];        
        $dom              = simplexml_load_string($v_xml_data);
        
        $arr_new_file     = array();
        if((int)$resluts['C_COUNT_MEDIA'] >0 )
        {
            $v_xpath        = '//data/media/file/text()';
            $arr_results    = $dom->xpath($v_xpath);
            
            foreach ($arr_results as $item)
            {
                $arr_new_file[] = '<file>'.(string)$item.'</file>';
            }
        }
       
        // add file moi
        $v_count_file = count($arr_file);
        if($v_count_file > 0)
        {
            for($i = 0;$i < $v_count_file; $i ++)
            {
                 if ($_FILES['uploader']['error'][$i] == 0)
                 {
                    $v_file_name     = $this->vn_str_filter($_FILES['uploader']['name'][$i]);
                    $v_tmp_name      = $_FILES['uploader']['tmp_name'][$i];
                    
                    $v_file_ext      = array_pop(explode('.', $v_file_name));
                    $v_user_id       = session::get('user_id');
                    $v_cur_file_name = uniqid().'_' . $v_file_name;
                    $v_upload_date = date('Y-m-d');
                    
                    if (in_array($v_file_ext, explode(',', _CONST_TYPE_FILE_ACCEPT)))
                    {                     
                        //check folder root
                        $v_dir_file = CONST_TYPE_FILE_UPLOAD . 'template_files_types' ;
                        if(file_exists($v_dir_file) == FALSE)
                        {
                            mkdir($v_dir_file, 0777, true);
                        }
                        if (!move_uploaded_file($v_tmp_name, CONST_TYPE_FILE_UPLOAD . 'template_files_types' . DS . $v_cur_file_name))
                        {
                            $this->popup_exec_fail('Xảy ra sự cố khi upload file!');
                        }
                        else
                        {
                            $arr_new_file[] = '<file>'.$v_cur_file_name.'</file>';
                        }
                    }
                }
            }
            
            $tring_xml_file  = (string)implode('', $arr_new_file);
            $sql = "
                UPDATE t_ps_record_type 
                        SET	
                         C_XML_DATA = UpdateXML(C_XML_DATA,'//data/media','<media>$tring_xml_file</media>') 	
                        WHERE
                        PK_RECORD_TYPE = ? ";
            $this->db->Execute($sql,array($v_record_type_id));
        }
    }
    
    
    
    
    public function delete_file_tempate_type($v_record_type_id,$v_list_file_key)
    {
        $arr_file_id  = explode('|',$v_list_file_key);
        $v_xml_data = '';
        
        if(count($arr_file_id) >0 )
        {
            for($i = 0; $i <count($arr_file_id); $i++)
            {
                $v_file_id = trim($arr_file_id[$i]);
                 $sql = "
                        UPDATE t_ps_record_type 
                                SET	
                                 C_XML_DATA = UpdateXML(C_XML_DATA,'//data/media/file[node()=\"$v_file_id\"]',' ') 	
                                WHERE
                                PK_RECORD_TYPE = ? ";
                $this->db->Execute($sql,array($v_record_type_id));                
                //Xoa thong tun file
                $v_path_file = CONST_TYPE_FILE_UPLOAD . 'template_files_types' .DS . $arr_file_id[$i];
                if(is_file($v_path_file))
                {
                    unlink($v_path_file);
                }

            }
        }
    }
 //Loai bo dau va khi thu xau khi upload file
function vn_str_filter ($str){

    $str = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/", 'a', $str);
    $str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/", 'e', $str);
    $str = preg_replace("/(ì|í|ị|ỉ|ĩ)/", 'i', $str);
    $str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/", 'o', $str);
    $str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/", 'u', $str);
    $str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/", 'y', $str);
    $str = preg_replace("/(đ)/", 'd', $str);
    $str = preg_replace("/(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)/", 'A', $str);
    $str = preg_replace("/(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)/", 'E', $str);
    $str = preg_replace("/(Ì|Í|Ị|Ỉ|Ĩ)/", 'I', $str);
    $str = preg_replace("/(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)/", 'O', $str);
    $str = preg_replace("/(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)/", 'U', $str);
    $str = preg_replace("/(Ỳ|Ý|Ỵ|Ỷ|Ỹ)/", 'Y', $str);
    $str = preg_replace("/(Đ)/", 'D', $str);
    $str = preg_replace("/( )/", '-', $str);
    $str = preg_replace("/,/", '-', $str);
    $str = preg_replace('/(?|\'|"|&|#)/', '', $str);
    //$str = str_replace(" ", "-", str_replace("&*#39;","",$str));
    return $str;
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