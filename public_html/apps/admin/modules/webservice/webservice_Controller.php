<?php if (!defined('SERVER_ROOT')) exit('No direct script access allowed');

class webservice_Controller extends Controller {
    public $_arr_grant;
    function __construct() {
        parent::__construct('admin', 'webservice');
        $this->_arr_grant = array('MEMBER');
    }
    #PhamHuong[30-7-2015]
    #Chuc nang can bo mot cua sao chep noi dung HDTTHC
    public function get_data_all_guidance()
    {
       
    }
    /**
     * Lay danh sach linh vục
     * @return array fields
     */
    public function get_all_feilds()
    {
        $response = $this->model->qry_all_fields();
        if($response === FALSE)
        {
            ob_clean();
            header('content-type:application/xml');
            $xml  = '<data><message_errors>Đã xảy ra lỗi không lấy được danh sách lĩnh vực</message_errors></data>';
            echo xml_add_declaration($xml);
            exit();
        }
        
        $xml  = '<data>';
        for($i = 0;$i <count($response);$i ++)
        {
            $v_list_id      = $response[$i]['PK_LIST'];
            $v_listype_id   = $response[$i]['FK_LISTTYPE'];
            $v_code         = $response[$i]['C_CODE'];
            $v_name         = $response[$i]['C_NAME'];
            $xml .=  "<item>";
            $xml .=  "<PK_LIST><![CDATA[$v_list_id]]></PK_LIST>";
            $xml .=  "<FK_LISTTYPE><![CDATA[$v_listype_id]]></FK_LISTTYPE>";
            $xml .=  "<C_CODE><![CDATA[$v_code]]></C_CODE>";
            $xml .=  "<C_NAME><![CDATA[$v_name]]></C_NAME>";
            $xml .=  "</item>";
        }
        $xml  .= '</data>';
        ob_clean();
        header('content-type:application/xml');
        echo xml_add_declaration($xml);
    }
    
    /**
     * Lay danh sach linh vục
     * @return array fields
     */
    public function get_all_record_type()
    {
        $v_string_serch         = get_request_var('txt_filter','');
        $v_fields_code          = get_request_var('sel_feilds','');
        $sel_rows_per_page      = get_request_var('sel_rows_per_page',10);
        $sel_goto_page          = get_request_var('sel_goto_page',1);
        $sel_goto_page          = $sel_goto_page > 0  ? $sel_goto_page : 1;
        $response = $this->model->qry_all_record_type($v_string_serch,$v_fields_code,$sel_rows_per_page,$sel_goto_page);
        if($response === FALSE)
        {
            ob_clean();
            header('content-type:application/xml');
            $xml  = '<data><message_errors>Đã xảy ra lỗi không lấy được danh sách thủ tục</message_errors></data>';
            echo xml_add_declaration($xml);
            exit();
        }
        
        $xml  = '<data>';
        for($i = 0;$i <count($response);$i ++)
        {
            $v_record_type_id      = $response[$i]['PK_RECORD_TYPE'];
            $v_cope                = $response[$i]['C_SCOPE'];
            $v_code                = $response[$i]['C_CODE'];
            $v_name                = $response[$i]['C_NAME'];
            $v_rn                  = $response[$i]['RN'];
            
            $xml .=  "<item>";
            $xml .=  "<RN><![CDATA[$v_rn]]></RN>";
            $xml .=  "<PK_RECORD_TYPE><![CDATA[$v_record_type_id]]></PK_RECORD_TYPE>";
            $xml .=  "<C_SCOPE><![CDATA[$v_cope]]></C_SCOPE>";
            $xml .=  "<C_CODE><![CDATA[$v_code]]></C_CODE>";
            $xml .=  "<C_NAME><![CDATA[$v_name]]></C_NAME>";
            $xml .=  "</item>";
        }
        $xml  .= '</data>';
        ob_clean();
        header('content-type:application/xml');
        echo xml_add_declaration($xml);
    }
    
    public function get_single_record_type()
    {
        $v_record_type_id = get_request_var('record_type_id',0);
        $response = $this->model->qry_single_record_type($v_record_type_id);
        if($response === FALSE)
        {
            ob_clean();
            header('content-type:application/xml');
            $xml  = '<data><message_errors>Đã xảy ra lỗi không lấy thông tin thủ tục</message_errors></data>';
            echo xml_add_declaration($xml);
            exit();
        }
        $response = xml_remove_declaration($response);
        ob_clean();
        header('content-type:application/xml');
        echo xml_add_declaration($response);
        exit();
    }
    #End Chuc nang can bo mot cua sao chep noi dung HDTTHC

    public function main()
    {
        return NULL;
    }

    public function arp_data_for_xlist_ddli($listtype_code)
    {
        $v_format = get_request_var('format','json');
        $listtype_code = strtoupper(replace_bad_char($listtype_code));
        $arr_list = array();
        if(in_array($listtype_code, $this->_arr_grant))
        {
            $funtion = 'get_data_of_' . $listtype_code;
            $arr_list = $this->model->$funtion();
        }
        else
        {
            $arr_list = $this->model->list_get_all_by_listtype_code($listtype_code);
        }
        
        if ($v_format == 'json')
        {
            @ob_clean();
            header('content-type:application/json');
            echo json_encode($arr_list);
        }
        elseif ($v_format == 'xml')
        {
            $xml = '<data>';
            for ($i=0, $n=count($arr_list); $i<$n; $i++)
            {
                $xml .= '<item value="' . $arr_list[$i]['C_CODE'] . '" name="' . $arr_list[$i]['C_NAME'] . '" />';
            }
            $xml .= '</data>';

            @ob_clean();
            header('content-type:text/xml');
            echo xml_add_declaration($xml);
        }
    }
}