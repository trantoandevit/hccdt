<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of member_Controller
 *
 * @author Tam Viet
 */
class member_Controller extends Controller
{

    public function __construct()
    {
        parent:: __construct('admin', 'member');
        $this->check_login();
        $this->model->goback_url = $this->view->get_controller_url();
        $this->view->template->show_left_side_bar = FALSE;
        $this->view->template->arr_count_article = $this->model->gp_qry_count_article();
        //$this->view->template->show_div_website = FALSE;

        session::init();
        $v_lang_id = session::get('session_lang_id');
        $this->view->template->arr_all_lang = $this->model->qry_all_lang();
        $this->view->template->arr_all_grant_website = $this->model->gp_qry_all_website_by_user($v_lang_id);

        if (session::check_permission('QL_DON_VI_TRUC_THUOC', FALSE) == FALSE)
        {
            die('Bạn không có quyền thực hiện chức năng này !!!');
        }
    }

    /**
     * Ham chay mac hinh hien thi thong tin tat ca member
     */
    public function main()
    {
        $this->dsp_all_member();
    }

    /**
     * Hien thi thong tin chi tiet member
     */
    public function dsp_single_member()
    {
        $v_member_id = get_post_var('hdn_item_id');
        $v_member_id = (is_id_number($v_member_id) > 0 ) ? $v_member_id : 0;

        //Lay thong tin member
        $VIEW_DATA['arr_single_member'] = $this->model->qry_single_member($v_member_id);

        //Lay danh sach don vi Quan/Huyen
        $VIEW_DATA['arr_single_member_level_1'] = $this->model->qry_all_member_level_1($v_member_id);

        $this->view->render('dsp_single_member', $VIEW_DATA);
    }

    /**
     * Cap nhat thong tin member (Insert or Update)
     */
    public function update_member()
    {
        $v_member_id = get_post_var('hdn_item_id', 0);
        $v_member_name = get_post_var('txt_member_name', '');
        $v_member_code = get_post_var('txt_member_code', '');
        $v_member_address = get_post_var('txt_member_address', '');
        $v_member_email = get_post_var('txt_member_email', '');
        $v_member_parent_id = get_post_var('sel_member_parent', 0);
        $v_status = isset($_POST['chk_status']) ? 1 : 0;
        $rad_level = get_post_var('rad_level', '');
        $rad_village = get_post_var('rad_village', '');

        $v_hdn_single_method = get_post_var('hdn_dsp_single_method', '');
        $v_xml_data = get_post_var('XmlData', '<data></data>');
        $v_short_code = get_post_var('txt_short_code', '');

        if ((trim($v_member_email) == '' OR ! filter_var($v_member_email, FILTER_VALIDATE_EMAIL)) && $rad_level != 2)
        {
            $this->model->exec_fail($this->view->get_controller_url() . 'dsp_single_member' . DS . $v_member_id, 'Địa chỉ email không hợp lệ, Vui lòng kiểm tra lại!');
            exit();
        }
        $arr_post = array(
            'member_id'             => $v_member_id,
            'txt_member_name'       => $v_member_name,
            'txt_member_code'       => $v_member_code,
            'txt_member_address'    => $v_member_address,
            'txt_member_email'      => $v_member_email,
            'rad_level'             => $rad_level,
            'sel_member_parent'     => $v_member_parent_id,
            'chk_status'            => $v_status,
            'XmlData'               => $v_xml_data,
            'txt_short_code'        => $v_short_code,
            'hdn_dsp_single_method' => $v_hdn_single_method,
            'rad_village'           => $rad_village
        );

        if (sizeof($arr_post) > 0 && (int) $v_member_id >= 0 && trim($v_member_name) != '' && trim($v_member_code) != '' && trim($v_member_address) != '' && trim($rad_level) != '')
        {
            $this->model->update_member($arr_post);
        }
        else
        {
//            $this->model->exec_fail($this->view->get_controller_url().'dsp_single_member'.DS.$v_member_id,'Dữ liệu chưa hợp lệ xin kiểm tra lại!');
            exit();
        }
    }

    /**
     * Hien  thi danh sach tat ca member
     */
    public function dsp_all_member()
    {
        $v_filter = replace_bad_char(get_request_var('txt_filter', ''));
        $arr_filter = array('txt_filter' => $v_filter);

        $VIEW_DATA['arr_all_member'] = $this->model->qry_all_member($arr_filter);
        $this->view->render('dsp_all_member', $VIEW_DATA);
    }

    /**
     * xoa thanh vien
     */
    public function dsp_delete_member()
    {
        $v_list_id = get_post_var('hdn_item_id_list', '');
        $this->model->delete_member($v_list_id);
    }

    /**
     * lấy xã trực thuộc
     */
    public function arp_get_all_village()
    {
        if (DEBUG_MODE > 10)
        {
            $this->model->db->debug = 1;
        }

        $v_district_id = get_post_var('district_id', 41);
        $arr_single_memeber = $this->model->qry_single_member($v_district_id);
        
        $xml_data  = isset($arr_single_memeber['C_XML_DATA']) ? $arr_single_memeber['C_XML_DATA'] : '<data></data>';            
        @$dom      = simplexml_load_string($xml_data);
        $arr_all_village = array();
        if($dom)
        {
            $location        = xpath($dom, '//item[@id="location"]/value', XPATH_STRING);
            $uri             = xpath($dom, '//item[@id="uri"]/value', XPATH_STRING);
            try
            {
                $client = new SoapClient($location . '?wsdl', array('location' => $location,
                                                'uri'      => $uri));
                $xml_all_village    = $client->__soapCall('get_all_village', array());
                @$dom_all_village   = simplexml_load_string($xml_all_village);
                if($dom_all_village)
                {
                    $arr_village  = $dom_all_village->xpath('//item');
                    for($i =0;$i<count($arr_village);$i ++)
                    {
                        $v_ou_id = (string)$arr_village[$i]->PK_OU;
                        $v_name  = (string)$arr_village[$i]->C_NAME;
                        $arr_all_village[$v_ou_id] = $v_name;
                    }
                }
            }
            catch (Exception $ex)
            {
                $arr_all_village['errors'] = 'Không thể lấy thông tin danh sách cấp xã. Xin vui lòng thực hiện lại.';
            }
        }
        echo json_encode($arr_all_village);
    }

    public function do_synchronize()
    {
        ini_set('memory_limit', '250M');
        set_time_limit(0);
        $this->model->do_synchronize();
        if (!defined('CLI'))
        {
            $this->model->exec_done($this->model->goback_url);
        }
    }

    function do_sync_user()
    {
        ini_set("default_socket_timeout", 6000);
        ini_set('memory_limit', '250M');
        set_time_limit(0);
        $this->model->do_sync_user();
        if (!defined('CLI'))
        {
            $this->model->exec_done($this->model->goback_url);
        }
    }

}
