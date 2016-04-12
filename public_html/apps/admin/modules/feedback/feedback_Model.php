<?php

defined('DS') or die('no direct access');

class feedback_Model extends Model
{

    function __construct()
    {
        parent::__construct();
    }
   /**
    * lay danh sach tat ca gop y phan hoi
    * @return type
    */
    public function qry_all_feedback()
    {
        $v_website_id = session::get('session_website_id');
        
        $sql_total_record = " (Select COUNT(*) From t_ps_feedback Where FK_WEBSITE = $v_website_id) As TOTAL_RECORD ,";
        $sql = "Select
                    PK_FEEDBACK,
                    $sql_total_record
                    C_NAME,
                    C_ADDRESS,
                    C_EMAIL,
                    DATE_FORMAT(C_INIT_DATE,'%d/%m/%Y') As C_INIT_DATE,
                    C_TITLE,
                    C_CONTENT,
                    C_REPLY,
                    FK_WEBSITE,
                    FK_USER,
                    C_FILE_NAME,
                    C_PUBLIC,
                    C_PRIVATE_ANSWER
                  From t_ps_feedback
                  Where FK_WEBSITE = $v_website_id";
        return $this->db->getAll($sql);
    }
    /**
     * delete list gop y phan hoi
     */
    public function delete_feedback()
    {
        $v_list_id = get_post_var('hdn_item_id_list','');
        
        $sql = "Delete From t_ps_feedback Where PK_FEEDBACK in ($v_list_id)";
        $this->db->Execute($sql);
        $this->exec_done($this->goback_url);
    }
    /**
     * lay thong tin chi tiet cua gop y phan hoi
     */
    public function qry_single_feedback($v_id)
    {
        if($v_id == 0)
        {
            return array();
        }
        $v_website_id = session::get('session_website_id');
        $sql_user_name = " (Select C_NAME From t_cores_user Where PK_USER = t_ps_feedback.FK_USER) As C_USER_NAME ,";
        $sql = "Select
                PK_FEEDBACK,
                C_NAME,
                $sql_user_name
                C_ADDRESS,
                C_EMAIL,
                DATE_FORMAT(C_INIT_DATE,'%d/%m/%Y') As C_INIT_DATE,
                C_TITLE,
                C_CONTENT,
                C_REPLY,
                FK_WEBSITE,
                FK_USER,
                C_FILE_NAME,
                C_PUBLIC,
                C_PRIVATE_ANSWER
              From t_ps_feedback
              Where PK_FEEDBACK = $v_id
                  And FK_WEBSITE = $v_website_id";
        return $this->db->getRow($sql);
    }
    
    
    public function update_feedback()
    {
        $v_website_id     = session::get('session_website_id');
        $v_id             = get_post_var('hdn_item_id',0);
        $v_reply          =  $this->prepare_tinyMCE(get_request_var('txt_reply', '', 0));//xu ly rieng
        $v_public         = isset($_POST['chk_public'])?1:0;
        $v_private_answer = isset($_POST['chk_private_answer'])?1:0;
        $v_init_user      = NULL;
        
        //valid date
        if($v_id == 0)
        {
            $this->exec_fail($this->goback_url, 'Đối tượng ko tồn tại');
        }
        
        //nguoi tra loi
        if($v_reply != '' OR $v_reply != NULL)
        {
            $v_init_user      = session::get('user_id');
        }
        
        //kiem tra xem da gui cau tra loi qua email chưa
        $sql = "Select
                    C_PRIVATE_ANSWER
                  From t_ps_feedback
                  Where PK_FEEDBACK = $v_id";
        $v_private_answered = $this->db->getOne($sql);
        $v_private_answer = ($v_private_answered == 0)?$v_private_answer:1;
        
        //gui cau tra loi qua email neu la lan dau tien
        if($v_private_answered == 0 && $v_private_answer == 1)
        {
            //lay mail cua nguoi gop y
            $sql = "Select
                        C_EMAIL,
                        C_TITLE,
                        C_CONTENT,
                        (Select
                           C_NAME
                         From t_cores_user
                         Where PK_USER = $v_init_user) As C_USER_NAME
                      From t_ps_feedback
                      Where PK_FEEDBACK = $v_id";
            $arr_info    = $this->db->getRow($sql);
            
            $v_email_to  = $arr_info['C_EMAIL'];
            $v_title     = $arr_info['C_TITLE'];
            $v_content   = $arr_info['C_CONTENT'];
            $v_user_name = $arr_info['C_USER_NAME'];
            
            require_once SERVER_ROOT . 'libs/swift/lib/swift_required.php';

            // Tạo đối tượng transport
            $server_name = get_system_config_value(CFGKEY_MAIL_SERVER);
            $port        = get_system_config_value(CFGKEY_MAIL_PORT);
            $ssl         = get_system_config_value(CFGKEY_MAIL_SSL) == 'true' ? 'ssl' : null;
            $transport   = Swift_SmtpTransport::newInstance($server_name, $port, $ssl);
            $transport->setUsername(get_system_config_value(CFGKEY_MAIL_ACCOUNT));
            $transport->setPassword(get_system_config_value(CFGKEY_MAIL_PASSWORD));
            //tao subject
            $v_subject   = __('feedback').' - '.get_system_config_value(CFGKEY_UNIT_NAME);
            // Tạo đối tượng mailer sẽ đãm nhận nhiệm vụ gởi mail đi
            $mailer = Swift_Mailer::newInstance($transport);

            //Tạo message để gởi đi
            $message_content = '<b>'.$v_subject .'</b>'. "<br>";
            $message_content .= '<b>'.__('question').'</b>'. "<br>";
            $message_content .= '<div style="margin-left:20px"><b>'.$v_title.'</b>'."<br>".$v_content."</div><br>";
            $message_content .= '<b>'.__('answer'). ' - ' . $v_user_name.'</b>'."<br>";
            $message_content .= $v_reply ."<br>";
            $message_content .= '<div style="float:right">'.__('thank you for use feedback').'</div>';

            $message = Swift_Message::newInstance();
            
            //su dung the html
            $message->setContentType("text/html");
            //set subject mail
            $message->setSubject($v_subject);
            //set body
            $message->setBody($message_content);
            
            $message->setFrom(get_system_config_value(CFGKEY_MAIL_ADD));
            $message->addTo($v_email_to);

            // Gởi message
            $result = $mailer->send($message);
            
        }
        
        
        //cap nhat thong tin 
        $stmt = "Update t_ps_feedback
                Set C_REPLY = ?,
                  C_PUBLIC = ?,
                  C_PRIVATE_ANSWER = ?,
                  FK_USER = ?
                Where PK_FEEDBACK = ?
                    And FK_WEBSITE = ?";
        $arr_param = array($v_reply,$v_public,$v_private_answer,$v_init_user,$v_id,$v_website_id);
        $this->db->Execute($stmt,$arr_param);
        
        $this->exec_done($this->goback_url);
    }
}

?>
