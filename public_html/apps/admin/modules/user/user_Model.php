<?php if (!defined('SERVER_ROOT')) exit('No direct script access allowed');

class user_Model extends Model {

    function __construct()
    {
        parent::__construct();
    }
   
    public function qry_ou_tree()
    {
        $sql = 'Select PK_OU, FK_OU, C_NAME, C_ORDER, C_INTERNAL_ORDER From t_cores_ou Order By C_INTERNAL_ORDER';
        $this->db->debug = 0;
        return $this->db->getAll($sql);
    }

    public function qry_all_sub_ou($ou_id)
    {
        //$ou_id must be a integer
        if (!( preg_match( '/^\d*$/', trim($ou_id)) == 1 ))
        {
            $ou_id = $this->db->getOne('Select PK_OU From t_cores_ou Where FK_OU=-1 limit 1');
        }

        $stmt = 'Select PK_OU, FK_OU, C_NAME, C_ORDER From t_cores_ou Where FK_OU=? Order By C_INTERNAL_ORDER';
        $params = array($ou_id);

        return $this->db->getAll($stmt, $params);
    }

    public function qry_all_user_by_ou($ou_id)
    {
        //$ou_id must be a integer
        if (!( preg_match( '/^\d*$/', trim($ou_id)) == 1 ))
        {
            $ou_id = $this->db->getOne('Select PK_OU From t_cores_ou Where FK_OU=(Select PK_OU From t_cores_ou Where FK_OU=PK_OU limit 1) limit 1');
        }

        $stmt = 'Select * From t_cores_user Where FK_OU=? Order By C_ORDER';
        $params = array($ou_id);
        return $this->db->getAll($stmt, $params);
    }

    public function qry_all_group_by_ou($ou_id)
    {
        $stmt = 'Select * From t_cores_group Where FK_OU=? Order By C_NAME';
        $params = array($ou_id);
        return $this->db->getAll($stmt, $params);
    }

    public function qry_ou_path($ou_id)
    {
        if (!( preg_match( '/^\d*$/', trim($ou_id)) == 1 ))
        {
            $ou_id = $this->get_root_ou();
        }

        if (DATABASE_TYPE == 'MSSQL')
        {
            return $this->db->getAssoc("Select PK_OU, C_NAME From  dbo.f_qry_ou_path($ou_id) Order By C_INTERNAL_ORDER");
        }
        elseif (DATABASE_TYPE == 'MYSQL')
        {
            $ret_array = array();

            $stmt = 'Select PK_OU, C_NAME, C_INTERNAL_ORDER, FK_OU From t_cores_ou Where PK_OU=?';
            $params = array($ou_id);
            $arr_ou_info = $this->db->getRow($stmt, $params);

            $v_parent_ou_id   = $arr_ou_info['FK_OU'];
            $v_internal_order = $arr_ou_info['C_INTERNAL_ORDER'];
            $v_ou_id          = $arr_ou_info['PK_OU'];
            $v_ou_name        = $arr_ou_info['C_NAME'];

            $ret_array[$v_ou_name] =  $v_ou_id;
            while (strlen($v_internal_order) > 3)
            {
                $stmt = 'Select PK_OU, C_NAME, C_INTERNAL_ORDER, FK_OU From t_cores_ou Where PK_OU=?';
                $params = array($v_parent_ou_id);
                $arr_ou_info = $this->db->getRow($stmt, $params);

                $v_parent_ou_id   = $arr_ou_info['FK_OU'];
                $v_internal_order = $arr_ou_info['C_INTERNAL_ORDER'];
                $v_ou_id          = $arr_ou_info['PK_OU'];
                $v_ou_name        = $arr_ou_info['C_NAME'];

                $ret_array[$v_ou_name] =  $v_ou_id;
            }
            return array_flip(array_reverse($ret_array));
        }
    }

    public function qry_single_ou($ou_id)
    {
        if ($ou_id > 0)
        {
            $stmt = 'Select * From t_cores_ou Where PK_OU=?';
            return $this->db->getRow($stmt, array($ou_id));
        }
        else
        {
            return array(
                'C_ORDER' => $this->get_max('t_cores_ou', 'C_ORDER', ' FK_OU <> -1') + 1,
            );
        }
    }

    public function update_ou()
    {
        $v_parent_cores_ou_id = replace_bad_char($_POST['hdn_parent_ou_id']);
        $v_ou_id              = replace_bad_char($_POST['hdn_item_id']);
        $v_name               = replace_bad_char($_POST['txt_name']);
        $v_order              = replace_bad_char($_POST['txt_order']);

        $v_xml_data           = replace_bad_char($_POST['XmlData']);

        //Kiem tra trung ten
        //$stmt = 'Select Count(*) From t_cores_ou Where C_NAME=? And PK_OU <> ? And FK_OU=?';
        //$params = array($v_name, $v_ou_id, $v_parent_cores_ou_id);
        $stmt = 'Select Count(*) From t_cores_ou Where C_NAME=? And PK_OU <> ?';
        $params = array($v_name, $v_ou_id);
        $v_duplicate_name = $this->db->getOne($stmt, $params);

        if ($v_duplicate_name)
        {
            $this->popup_exec_fail('Tên đơn vị đã tồn tại!');
            return;
        }

        if ($v_ou_id < 1)
        {
            $stmt = 'Insert Into t_cores_ou(FK_OU, C_NAME,C_ORDER) Values(?, ?, ?)';
            $params = array( $v_parent_cores_ou_id
                            ,$v_name
                            ,$v_order
            );
            $this->db->Execute($stmt, $params);
            $v_ou_id = $this->db->getOne("Select IDENT_CURRENT('t_cores_ou')");

            $v_current_order = -1;

        }
        else
        {
            $v_current_order = $this->db->getOne('Select C_ORDER From t_cores_ou Where PK_OU=?', array($v_ou_id));
            $stmt = 'Update t_cores_ou Set
                        C_NAME=?
                        ,C_ORDER=?
                    Where PK_OU=?';
            $params = array(
                        $v_name
                        ,$v_order
                        ,$v_ou_id
            );

            $this->db->Execute($stmt, $params);
        }
        //reorder
        $this->ReOrder('t_cores_ou','PK_OU','C_ORDER', $v_ou_id, $v_order, $v_current_order, " FK_OU=$v_parent_cores_ou_id AND PK_OU <> $v_parent_cores_ou_id");

        //Rebuild internal order
        $this->build_interal_order('t_cores_ou', 'PK_OU', 'FK_OU', -1);

        $this->popup_exec_done();

    }

    public function delete_ou()
    {
        $v_ou_id = isset($_POST['hdn_item_id']) ? replace_bad_char($_POST['hdn_item_id']) : 0;
        
        //dem so NSD con trong ou
        $sql = "SELECT
                    COUNT(*)
                  FROM t_cores_user
                  WHERE FK_OU IN(SELECT
                                   PK_OU
                                 FROM t_cores_ou
                                 WHERE C_INTERNAL_ORDER LIKE CONCAT('%',(SELECT
                                                                           C_INTERNAL_ORDER
                                                                         FROM t_cores_ou
                                                                         WHERE PK_OU = ?),'%'))";
        $count_user = $this->db->getOne($sql,array($v_ou_id));
        if((int) $count_user > 0)
        {
            $this->exec_fail($this->goback_url, 'Bạn phải xóa hết NSD !!!');
        }
        
        //dem so ou con
        $sql = "SELECT
                    COUNT(*)
                  FROM t_cores_ou
                  WHERE FK_OU = ?";
        $count_ou = $this->db->getOne($sql,array($v_ou_id));
        if((int) $count_ou > 0)
        {
            $this->exec_fail($this->goback_url, 'Bạn phải xóa hết đơn vị !!!');
        }
        
        //xoa
        $stmt = "DELETE
                FROM t_cores_ou
                WHERE PK_OU = ?";
                    
        $params = array($v_ou_id);
        $this->db->Execute($stmt, $params);

        $this->exec_done($this->goback_url);
    }
    /**
     * lay thong tin chi tiet cua 
     * @param type $user_id
     * @return type
     */
    public function qry_single_user($user_id)
    {
        if ($user_id > 0)
        {
            $stmt = "Select 
                        PK_USER,
                        FK_OU,
                        C_NAME,
                        C_LOGIN_NAME,
                        C_PASSWORD,
                        C_IS_ADMIN,
                        C_ORDER,
                        C_STATUS,
                        C_LAST_LOGIN_DATE,
                        C_XML_DATA,
                        C_JOB_TITLE,
                        C_QUIT_JOB,
                        C_ALIAS,
                        DATE_FORMAT(C_BIRTHDAY,'%d-%m-%Y') As C_BIRTHDAY,
                        C_GENDER,
                        C_ID_CARD,
                        C_ADDRESS,
                        C_MOBILE,
                        C_PHONE,
                        C_FAX,
                        C_EMAIL,
                        C_PORTRAIT_FILE_NAME
                    From t_cores_user Where PK_USER=?";
            return $this->db->getRow($stmt, $user_id);
        }
        else
        {
            return array(
                'C_ORDER' => $this->get_max('t_cores_user', 'C_ORDER') + 1
            );
        }
    }


    public function update_user()
    {
        $v_user_id          = replace_bad_char($_POST['hdn_item_id']);
        $v_ou_id            = replace_bad_char($_POST['hdn_parent_ou_id']);
        $v_name             = replace_bad_char($_POST['txt_name']);
        
        
        $v_new_password     = replace_bad_char($_POST['txt_new_password']);
        
        $v_new_password = encrypt_password($v_new_password);
        
        $v_order            = replace_bad_char($_POST['txt_order']);
        $v_status           = isset($_POST['chk_status']) ? 1 : 0;
        //$v_xml_data         = $_POST['XmlData'];
        
        $v_job_title        = replace_bad_char($_POST['hdn_job_title']);

        $v_login_name       = isset($_POST['txt_login_name']) ? replace_bad_char($_POST['txt_login_name']) : '';
        $v_login_name       = str_replace(',', '', $v_login_name);
        if(substr_count($v_login_name, ' ') >0)
        {
            Model::popup_exec_fail('Tên tài khoản không được chứa khoảng trống!');
        }
        
        $v_group_id_list    = isset($_POST['hdn_group_id_list']) ? replace_bad_char($_POST['hdn_group_id_list']) : '';
        //thong tin ca nhan
        $v_alias                 = get_post_var('txt_alias','');
        $v_date_of_birth         = jwDate::ddmmyyyy_to_yyyymmdd(get_post_var('txt_date_of_birth',''));
        $v_sex                   = get_post_var('rad_sex','');
        $v_identification_number = get_post_var('txt_identification_number','');
        $v_address               = get_post_var('txt_address','');
        $v_mobile                = get_post_var('txt_mobile','');
        $v_phone                 = get_post_var('txt_phone','');
        $v_fax                   = get_post_var('txt_fax','');
        $v_email                 = get_post_var('txt_email','');

        $v_quit_job              = isset($_POST['chk_quit_job'])?1:0;

        $v_thumbnail             = get_post_var('hdn_thumbnail','');
        //tieu su NSD
         //array education
        $arr_school  = isset($_POST['txt_education_school']) ? $_POST['txt_education_school'] : array(); 
        $arr_address = isset($_POST['txt_education_address']) ? $_POST['txt_education_address'] : array(); 
        $arr_from    = isset($_POST['txt_education_begin_year']) ? $_POST['txt_education_begin_year'] : array(); 
        $arr_to      = isset($_POST['txt_education_end_year']) ? $_POST['txt_education_end_year'] : array(); 
        $arr_degree  = isset($_POST['sel_education_degree']) ? $_POST['sel_education_degree'] : array(); 

        //array work
        $arr_work_name       = isset($_POST['txt_work_name']) ? $_POST['txt_work_name'] : array(); 
        $arr_work_address    = isset($_POST['txt_work_address']) ? $_POST['txt_work_address'] : array(); 
        $arr_work_position   = isset($_POST['txt_work_position']) ? $_POST['txt_work_position'] : array(); 
        $arr_work_start      = isset($_POST['txt_work_start']) ? $_POST['txt_work_start'] : array(); 
        $arr_work_finish     = isset($_POST['txt_work_finish']) ? $_POST['txt_work_finish'] : array(); 

        //Kiem tra trung ten dang nhap
        $stmt = 'Select Count(*) From t_cores_user Where C_LOGIN_NAME=? And PK_USER <> ?';
        $params = array($v_login_name, $v_user_id);
        $v_duplicate_login_name = $this->db->getOne($stmt, $params);

        if ($v_user_id > 0) {
            //Update
            $stmt = "Update t_cores_user Set
                        C_NAME=?
                        ,C_ORDER=?
                        ,C_STATUS=?
                        ,FK_OU=?
                        ,C_JOB_TITLE=?
                        ,C_ALIAS = ?
                        ,C_BIRTHDAY = ?
                        ,C_GENDER = ?
                        ,C_ID_CARD = ?
                        ,C_ADDRESS = ?
                        ,C_MOBILE = ?
                        ,C_PHONE = ?
                        ,C_FAX = ?
                        ,C_EMAIL = ?
                        ,C_PORTRAIT_FILE_NAME = ?
                        ,C_QUIT_JOB = ?
                    Where PK_USER=?";
            $params = array(
                    $v_name
                    ,$v_order
                    ,$v_status
                    ,$v_ou_id
                    ,$v_job_title
                    ,$v_alias,$v_date_of_birth,$v_sex,$v_identification_number
                    ,$v_address,$v_mobile,$v_phone,$v_fax,$v_email,$v_thumbnail,$v_quit_job
                    ,$v_user_id
            );
            $this->db->Execute($stmt, $params);

            //Co thay doi mat khau khong
            if ($v_new_password != '')
            {
                $this->db->Execute("Update t_cores_user Set C_PASSWORD='$v_new_password' Where PK_USER=$v_user_id");
            }
        }
        else {
            if ($v_duplicate_login_name)
            {
                Model::popup_exec_fail('Tên đăng nhập đã tồn tại!');
                return;
            }
            //Insert
            $stmt = "Insert Into t_cores_user
                    (   C_LOGIN_NAME 
                        , C_NAME 
                        , C_PASSWORD
                        , C_ORDER
                        , C_STATUS
                        , FK_OU
                        , C_JOB_TITLE
                        , C_ALIAS
                        , C_BIRTHDAY
                        , C_GENDER
                        , C_ID_CARD
                        , C_ADDRESS
                        , C_MOBILE
                        , C_PHONE
                        , C_FAX
                        , C_EMAIL
                        , C_PORTRAIT_FILE_NAME
                        , C_QUIT_JOB
                    )
                    values (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
            $params = array(
                    $v_login_name
                    ,$v_name
                    ,$v_new_password
                    ,$v_order
                    ,$v_status
                    ,$v_ou_id
                    ,$v_job_title
                    ,$v_alias,$v_date_of_birth,$v_sex,$v_identification_number
                    ,$v_address,$v_mobile,$v_phone,$v_fax,$v_email,$v_thumbnail,$v_quit_job
            );
            $this->db->Execute($stmt, $params);
            //kiem tra insert duoc chua
            $v_check = $this->db->Affected_Rows();
            if($v_check > 0)
            {
                if(DATABASE_TYPE == 'MSSQL')
                {
                    $v_user_id = $this->db->getOne("SELECT IDENT_CURRENT('t_cores_user')");
                }
                else if(DATABASE_TYPE == 'MYSQL')
                {
                    $v_user_id = $this->db->getOne("Select
                                                        PK_USER
                                                      From t_cores_user
                                                      Order by PK_USER DESC");
                }
                
            }
            else
            {
                $this->popup_exec_done();
            }
            
        }
        //Reorder
        $this->ReOrder('t_cores_user','PK_USER','C_ORDER', $v_user_id, $v_order, -1, "FK_OU=$v_ou_id");


        //Cap nhat thong tin nhom
        //Xoa het du lieu cu
        $stmt = 'Delete From t_cores_user_group Where FK_USER=?';
        $this->db->execute($stmt, array($v_user_id));
        //Cap nhat du lieu moi
        
        $arr_group_id_list = explode(',', $v_group_id_list);
        foreach ($arr_group_id_list as $v_group_id)
        {
            if ( $v_group_id != NULL && $v_group_id != '')
            {
                $stmt = 'Insert Into t_cores_user_group(FK_GROUP, FK_USER) Values(?, ?)';
                $params = array($v_group_id, $v_user_id );
                $this->db->Execute($stmt, $params);
            }
        }

        //Cap nhat thong tin quyen tren chuyen trang
        $v_website_id   = isset($_POST['hdn_website_id']) ? replace_bad_char($_POST['hdn_website_id']) : '';
        //var_dump($v_website_id);
        $v_grant_function               = isset($_POST['hdn_grant_function']) ? replace_bad_char($_POST['hdn_grant_function']) : '';
        
        $v_grant_function_without_web   = isset($_POST['hdn_grant_function_without_website']) ? replace_bad_char($_POST['hdn_grant_function_without_website']) : '';
        
        //Xoa het thong tin cu
        $this->db->Execute('Delete From t_cores_user_function Where FK_USER=? And FK_WEBSITE is null', array($v_user_id));
        $this->db->Execute('Delete From t_cores_user_function Where FK_USER=? And FK_WEBSITE=?', array($v_user_id, $v_website_id));
        $arr_grant_function = explode(',', $v_grant_function);
        //cập nhật lại thông tin
        $arr_grant_function_without_web = explode(',', $v_grant_function_without_web); 
        foreach($arr_grant_function_without_web as $v_function)
        {
            if ( $v_function != NULL && $v_function != '')
            {
                $stmt = 'Insert Into t_cores_user_function(FK_USER, FK_WEBSITE, C_FUNCTION_CODE) Values (?, ? , ?)';
                $params = array($v_user_id, NULL, trim($v_function));
                $this->db->Execute($stmt, $params);
            }
        }
        
        foreach ($arr_grant_function as $v_function)
        {
            if ( $v_function != NULL && $v_function != '')
            {
                $stmt = 'Insert Into t_cores_user_function(FK_USER, FK_WEBSITE, C_FUNCTION_CODE) Values (?, ? , ?)';
                $params = array($v_user_id, $v_website_id, trim($v_function));
                $this->db->Execute($stmt, $params);
            }
        }
        
         //Cap nhat thong tin quyen tren chuyen muc
        $v_grant_category   = isset($_POST['hdn_grant_category']) ? replace_bad_char($_POST['hdn_grant_category']) : '';
        //Xoa het thong tin cu
        $this->db->Execute('Delete From t_ps_user_category Where FK_USER=?', array($v_user_id));
        $arr_grant_category = explode(',', $v_grant_category);
        foreach ($arr_grant_category as $v_category)
        {
            if ( $v_category != NULL && $v_category != '')
            {
                $stmt = 'Insert Into t_ps_user_category(FK_USER, FK_CATEGORY) Values (?, ?)';
                $params = array($v_user_id, $v_category);
                $this->db->Execute($stmt, $params);
            }
        }
       
        $this->popup_exec_done(NULL);
    }

    public function delete_user()
    {
        $v_user_id = isset($_POST['hdn_item_id']) ? replace_bad_char($_POST['hdn_item_id']) : 0;

        //Xoa NSD khoi nhom
        $stmt = 'Delete From t_cores_user_group Where FK_USER=?';
        $params = array($v_user_id);
        $this->db->Execute($stmt, $params);

        //Xoa quyen
        $stmt = 'Delete From t_cores_user_function Where FK_USER=?';
        $params = array($v_user_id);
        $this->db->Execute($stmt, $params);

        $stmt = 'Delete From t_cores_user Where PK_USER=?';
        $params = array($v_user_id);
        $this->db->Execute($stmt, $params);

        $this->exec_done($this->goback_url);
    }

    public function qry_all_application_option()
    {
        $sql = 'Select PK_APPLICATION, C_NAME
                From T_CORES_APPLICATION
                Where C_STATUS > 0
                Order By C_ORDER';
        $this->db->debug = 0;
        return $this->db->getAssoc($sql);
    }

    public function qry_single_group($group_id)
    {
        if ($group_id > 0)
        {
            $stmt = 'Select * From t_cores_group Where PK_GROUP=?';
            $params = array($group_id);

            return $this->db->getRow($stmt, $params);
        }

        return array();
    }

    public function qry_all_user_by_group($group_id)
    {
        $stmt = 'Select u.PK_USER
                        ,u.C_NAME
                        ,u.C_STATUS
                From t_cores_user u left join  t_cores_user_group g on u.PK_USER=g.FK_USER
                Where g.FK_GROUP=?';
        $params = array($group_id);

        return $this->db->getAll($stmt, $params);
    }

    public function update_group()
    {
        $v_group_id         = replace_bad_char($_POST['hdn_item_id']);
        $v_ou_id            = replace_bad_char($_POST['hdn_parent_ou_id']);
        $v_code             = trim(replace_bad_char($_POST['txt_code']));
        $v_name             = trim(replace_bad_char($_POST['txt_name']));

        $v_user_id_list     = replace_bad_char($_POST['hdn_user_id_list']);

        //Kiem tra trung ma, trung ten
        $stmt = 'Select Count(*) From t_cores_group Where C_CODE=? And PK_GROUP <> ?';
        $params = array($v_code, $v_group_id);
        $v_duplicate_code = $this->db->getOne($stmt, $params);
        if ($v_duplicate_code)
        {
            model::popup_exec_fail('Mã nhóm đã tồn tại!');
            return;
        }

        $stmt = 'Select Count(*) From t_cores_group Where C_NAME=? And PK_GROUP <> ?';
        $params = array($v_name, $v_group_id);
        $v_duplicate_code = $this->db->getOne($stmt, $params);
        if ($v_duplicate_code)
        {
            model::popup_exec_fail('Tên nhóm đã tồn tại!');
            return;
        }

        if ($v_group_id < 1)
        {
            $stmt = 'Insert Into t_cores_group(FK_OU, C_CODE, C_NAME) Values(?, ?, N?)';
            $params = array($v_ou_id, $v_code, $v_name);

            $this->db->Execute($stmt, $params);

            $v_group_id = $this->db->getOne("Select IDENT_CURRENT('t_cores_group')");
        }
        else
        {
            $stmt = 'Update t_cores_group Set
                        FK_OU=?
                        ,C_CODE=?
                        ,C_NAME=N?
                    Where PK_GROUP=?';

            $params = array($v_ou_id, $v_code, $v_name, $v_group_id);

            $this->db->Execute($stmt, $params);
        }

        //Cap nhat NSD trong nhom
        //Xoa het du lieu cu
        $stmt = 'Delete From t_cores_user_group Where FK_GROUP=?';
        $this->db->execute($stmt, array($v_group_id));
        //Cap nhat du lieu moi
        $arr_user_id_list = explode(',', $v_user_id_list);
        foreach ($arr_user_id_list as $v_user_id)
        {
            $stmt = 'Insert Into t_cores_user_group(FK_GROUP, FK_USER) Values(?, ?)';
            $params = array($v_group_id, $v_user_id );
            $this->db->Execute($stmt, $params);
        }

        //Cap nhat quyen cua nhom
        $v_website_id    = isset($_POST['hdn_website_id']) ? replace_bad_char($_POST['hdn_website_id']) : 0;

        $v_grant_function   = isset($_POST['hdn_grant_function']) ? replace_bad_char($_POST['hdn_grant_function']) : '';
        
       
        $v_grant_function_without_web   = isset($_POST['hdn_grant_function_without_website']) ? replace_bad_char($_POST['hdn_grant_function_without_website']) : '';
        
        //Xoa het thong tin cac quyen cu
        $this->db->Execute('Delete From t_cores_group_FUNCTION Where FK_GROUP=? And FK_WEBSITE is null', array($v_group_id));
        
        $this->db->Execute('Delete From t_cores_group_FUNCTION Where FK_GROUP=? And FK_WEBSITE=?', array($v_group_id, $v_website_id));
        
        //cập nhật lại thông tin 
        $arr_grant_function_without_web = explode(',', $v_grant_function_without_web);
        foreach($arr_grant_function_without_web as $v_function)
        {
             if ( $v_function != NULL && $v_function != '')
            {
                $stmt = 'Insert Into t_cores_group_FUNCTION(FK_GROUP, FK_WEBSITE, C_FUNCTION_CODE) Values (?, ? , ?)';
                $params = array($v_group_id, NULL, trim($v_function));
                $this->db->Execute($stmt, $params);
            }
        }
        
        $arr_grant_function = explode(',', $v_grant_function);
        foreach ($arr_grant_function as $v_function)
        {
            if ( $v_function != NULL && $v_function != '')
            {
                $stmt = 'Insert Into t_cores_group_FUNCTION(FK_GROUP, FK_WEBSITE, C_FUNCTION_CODE) Values (?, ? , ?)';
                $params = array($v_group_id, $v_website_id, trim($v_function));
                $this->db->Execute($stmt, $params);
            }
        }
        
        //cap nhat quyen tren chuyen muc cho nhom
        $v_grant_category   = isset($_POST['hdn_grant_category']) ? replace_bad_char($_POST['hdn_grant_category']) : '';

        //Xoa het thong tin cac quyen tren chuyen muc cu
        $this->db->Execute('Delete From T_PS_GROUP_CATEGORY Where FK_GROUP=?', array($v_group_id));

        $v_grant_category = explode(',', $v_grant_category);
        //var_dump($v_grant_category);
        foreach ($v_grant_category as $v_category)
        {
            if ( $v_category != NULL && $v_category != '')
            {
                $stmt = 'Insert Into T_PS_GROUP_CATEGORY(FK_GROUP,FK_CATEGORY) Values (?, ?)';
                $params = array($v_group_id, trim($v_category));
                $this->db->Execute($stmt, $params);
            }
        }
        //exit;
        $this->popup_exec_done();
    }

    public function delete_group()
    {
        $v_group_id = isset($_POST['hdn_item_id']) ? replace_bad_char($_POST['hdn_item_id']) : 0;

        $v_is_build_in = $this->db->getOne('Select C_BUILT_IN From t_cores_group Where PK_GROUP=?', array($v_group_id));

        if ($v_is_build_in == 0)
        {
            //Xoa NSD trong nhom
            $stmt = 'Delete From t_cores_user_group Where FK_GROUP=?';
            $params = array($v_group_id);
            $this->db->Execute($stmt, $params);

            //Xoa quyen cua nhom
            $stmt = 'Delete From t_cores_group_FUNCTION Where FK_GROUP=?';
            $params = array($v_group_id);
            $this->db->Execute($stmt, $params);

            //Xoa
            $stmt = 'Delete From t_cores_group Where PK_GROUP=? And (Select Count(*) From t_cores_user_group Where FK_GROUP=?) = 0';
            $params = array($v_group_id, $v_group_id);
            $this->db->Execute($stmt, $params);
        }

        $this->exec_done($this->goback_url);
    }

    public function qry_all_user_to_add($my_dept_only=0)
    {

        $stmt = 'Select PK_USER, C_LOGIN_NAME as C_CODE, C_NAME, C_STATUS,C_JOB_TITLE From t_cores_user Where C_STATUS > 0';
        if ($my_dept_only == 1)
        {
            $v_user_code = Session::get('user_code');
            $stmt .= "  And FK_OU=(Select FK_OU From t_cores_user Where C_LOGIN_NAME='$v_user_code')";
        }

        $v_group_code = isset($_REQUEST['group']) ? replace_bad_char($_REQUEST['group']) : '';
        if ($v_group_code != '')
        {
            $stmt .= "  And PK_USER In (Select FK_USER
                                        From t_cores_user_group UG Right Join t_cores_group G On UG.FK_GROUP=G.PK_GROUP
                                        Where G.C_CODE='$v_group_code')";
        }

        $stmt .= ' Order By C_NAME';

        return $this->db->getAll($stmt);
    }

    /**
     * Lấy danh sách NSD theo phòng ban
     */
    public function qry_all_user_by_ou_to_add()
    {
        if (DATABASE_TYPE == 'MSSQL')
        {
            $stmt = 'Select
                        OU.PK_OU
                        , OU.C_NAME
                        ,(Select PK_USER, C_LOGIN_NAME, C_NAME, C_JOB_TITLE From t_cores_user Where FK_OU=OU.PK_OU And C_STATUS > 0 For XML Raw) C_XML_USER
                    From t_cores_ou OU Order By C_INTERNAL_ORDER';

            return $this->db->getAll($stmt);
        }
        elseif (DATABASE_TYPE == 'MYSQL')
        {
            $stmt = "Select
                        OU.PK_OU
                        , OU.C_NAME
                        ,(Select GROUP_CONCAT('<row'
                                                , Concat(' PK_USER=\"', PK_USER, '\"')
                                                , Concat(' C_LOGIN_NAME=\"', C_LOGIN_NAME, '\"')
                                                , Concat(' C_NAME=\"', C_NAME, '\"')
                                                , Concat(' C_JOB_TITLE=\"', C_JOB_TITLE, '\"')
                                                , ' /> '
                                                SEPARATOR ''
                                             )
                         from t_cores_user Where FK_OU=OU.PK_OU ) AS C_XML_USER
                    From t_cores_ou OU
                    Order By C_INTERNAL_ORDER";
            $arr_all_ou = $this->db->getAll($stmt);
            return $arr_all_ou;
            /*
            for ($i=0; $i<sizeof($arr_all_ou); $i++)
            {
                $stmt = 'Select
                            PK_USER
                            , C_LOGIN_NAME
                            , C_NAME
                            , C_JOB_TITLE
                        From t_cores_user
                        Where FK_OU=?
                            And C_STATUS > 0
                        Order By C_ORDER';
                $params = array($arr_all_ou[$i]['PK_OU']);
                $arr_all_user_by_ou = $this->db->getAll($stmt, $params);
                $v_xml_user = '';
                for($j=0;$j<sizeof($arr_all_user_by_ou);$j++)
                {
                    $v_xml_user .= '<row';
                    $v_xml_user .= ' PK_USER="' . $arr_all_user_by_ou[$j]['PK_USER'] . '"';
                    $v_xml_user .= ' C_LOGIN_NAME="' . $arr_all_user_by_ou[$j]['C_LOGIN_NAME'] . '"';
                    $v_xml_user .= ' C_NAME="' . $arr_all_user_by_ou[$j]['C_NAME'] . '"';
                    $v_xml_user .= ' C_JOB_TITLE="' . $arr_all_user_by_ou[$j]['C_JOB_TITLE'] . '"';
                    $v_xml_user .= '/>';
                }

                $arr_all_ou[$i]['C_XML_USER'] = $v_xml_user;
            } //end for $i
            return $arr_all_ou;
            */
        } //end if DATABASE_TYPE

         return array();
    }

    public function qry_all_group_by_user($user_id)
    {
        $stmt = 'Select g.PK_GROUP, g.C_NAME
                From t_cores_group g left join t_cores_user_group ug on g.PK_GROUP=ug.FK_GROUP
                WHere ug.FK_USER=?';
        $params = array($user_id);

        return $this->db->getAssoc($stmt, $params);
    }

    public function qry_all_group_to_add($my_dept_only=0)
    {
        $stmt = 'Select PK_GROUP, C_CODE, C_NAME From t_cores_group';
        if ($my_dept_only == 1)
        {
            $v_user_code = Session::get('user_code');
            $stmt .= " Where FK_OU=(Select FK_OU From t_cores_user Where C_LOGIN_NAME='$v_user_code')";
        }
        $stmt .= ' Order By C_NAME';

        return $this->db->getAll($stmt);
    }

    public function qry_all_user_to_grand($v_filter)
    {
        $stmt = 'Select PK_USER, C_NAME, C_STATUS From t_cores_user ';
        if ($v_filter != '')
        {
            $stmt .= " Where C_NAME like '%$v_filter%' ";
        }
        $stmt .= ' Order By C_NAME';
        return $this->db->getAll($stmt);
    }

    public function qry_all_group_to_grand($v_filter)
    {
        $stmt = 'Select PK_GROUP, C_NAME From t_cores_group ';
        if ($v_filter != '')
        {
            $stmt .= " Where C_NAME like '%$v_filter%' ";
        }
        $stmt .= ' Order By C_NAME';
        return $this->db->getAll($stmt);
    }
    public function qry_single_user_permit_on_category($user_id)
    {
        $stmt = 'Select FK_CATEGORY
                From t_ps_user_category
                Where FK_USER=?';
        $params = array($user_id);

        $this->db->debug=0;
        return $this->db->getCol($stmt, $params);
    }
    
    public function qry_single_user_permit_on_website($user_id, $website_id)
    {
        $stmt = 'Select C_FUNCTION_CODE
                From t_cores_user_function
                Where FK_USER=? And FK_WEBSITE=?';
        $params = array($user_id, $website_id);

        $this->db->debug=0;
        return $this->db->getCol($stmt, $params);
    }

    public function qry_single_group_permit_on_website($group_id, $website_id)
    {
        $stmt = 'Select C_FUNCTION_CODE
                From t_cores_group_FUNCTION
                Where FK_GROUP=? And FK_WEBSITE=?';
        $params = array($group_id, $website_id);

        $this->db->debug=0;
        return $this->db->getCol($stmt, $params);
    }
    
    public function qry_single_group_permit_on_category($group_id)
    {
        $stmt = 'Select FK_CATEGORY
                From T_PS_GROUP_CATEGORY
                Where FK_GROUP=?';
        $params = array($group_id);

        $this->db->debug=0;
        return $this->db->getCol($stmt, $params);
    }
    public  function qry_single_user_permit_without_website($user_id)
    {
        $v_user_id = replace_bad_char($user_id);
        $stmt = "Select distinct C_FUNCTION_CODE from t_cores_user_function where FK_USER = $v_user_id and FK_WEBSITE is null";
        $this->db->debug=0;
        return $this->db->getCol($stmt);
    }
    public function qry_single_group_permit_without_website($group_id)
    {
        $v_group_id = replace_bad_char($group_id);
        $stmt = "Select distinct C_FUNCTION_CODE from t_cores_group_FUNCTION where FK_GROUP = $v_group_id and FK_WEBSITE is null";
        $this->db->debug=0;
        return $this->db->getCol($stmt);
    }
    public function update_user_permit()
    {
        $v_user_id          = isset($_POST['hdn_item_id']) ? replace_bad_char($_POST['hdn_item_id']) : 0;
        $v_application_id   = isset($_POST['sel_application']) ? replace_bad_char($_POST['sel_application']) : 0;

        $v_grant_function   = isset($_POST['hdn_grant_function']) ? replace_bad_char($_POST['hdn_grant_function']) : '';

        //Xoa het thong tin cu
        $this->db->Execute('Delete From t_cores_user_function Where FK_USER=? And FK_APPLICATION=?', array($v_user_id, $v_application_id));

        $arr_grant_function = explode(',', $v_grant_function);
        foreach ($arr_grant_function as $v_function)
        {
            if ( $v_function != NULL && $v_function != '')
            {
                $stmt = 'Insert Into t_cores_user_function(FK_USER, FK_APPLICATION, C_FUNCTION_CODE) Values (?, ? , ?)';
                $params = array($v_user_id, $v_application_id, trim($v_function));
                $this->db->Execute($stmt, $params);
            }
        }

        $this->popup_exec_done(FALSE);
    }

    public function update_group_permit()
    {
        $v_group_id          = isset($_POST['hdn_item_id']) ? replace_bad_char($_POST['hdn_item_id']) : 0;
        $v_application_id    = isset($_POST['sel_application']) ? replace_bad_char($_POST['sel_application']) : 0;

        $v_grant_function   = isset($_POST['hdn_grant_function']) ? replace_bad_char($_POST['hdn_grant_function']) : '';

        //Xoa het thong tin cac quyen cu
        $this->db->Execute('Delete From t_cores_group_FUNCTION Where FK_GROUP=? And FK_APPLICATION=?', array($v_group_id, $v_application_id));

        $arr_grant_function = explode(',', $v_grant_function);
        foreach ($arr_grant_function as $v_function)
        {
            if ( $v_function != NULL && $v_function != '')
            {
                $stmt = 'Insert Into t_cores_group_FUNCTION(FK_GROUP, FK_APPLICATION, C_FUNCTION_CODE) Values (?, ? , ?)';
                $params = array($v_group_id, $v_application_id, trim($v_function));
                $this->db->Execute($stmt, $params);
            }
        }

        $this->popup_exec_done(FALSE);
    }

    public function qry_all_website_option()
    {
        //temp
        //return array('1' => "Chuyên trang 1", '2' => "Chuyên trang 2",'3' => "Chuyên trang 3");

        $stmt = 'Select PK_WEBSITE, C_NAME From t_ps_website Where C_STATUS > 0 Order By C_NAME';
        return $this->db->getAssoc($stmt);
    }
    public function qry_all_category($website_id)
    {
        $this->db->debug = 0;
        return $this->db->getAll(
                        'Select 
                        PK_CATEGORY, C_ORDER, C_INTERNAL_ORDER, C_NAME, C_STATUS, FK_PARENT
                        From T_PS_CATEGORY
                        Where FK_WEBSITE = ? 
                        Order by C_INTERNAL_ORDER'
                        , array($website_id,1)
        );
    }
    public function qry_all_job_title()
    {
        return $this->assoc_list_get_all_by_listtype_code('DM_CHUC_DANH');
    }
    public function qry_all_ou()
    {
        $stmt="SELECT PK_OU,
                      FK_OU,
                      C_NAME,
                      C_ORDER,
                      C_STATUS,
                      C_INTERNAL_ORDER,
                      C_XML_DATA
                 FROM t_cores_ou
                 ORDER BY C_INTERNAL_ORDER";
        return $this->db->getAll($stmt);
    }
    
     public function get_root_ou()
    {
        return $this->db->getOne('Select PK_OU From t_cores_ou Where (FK_OU < 0 Or FK_OU Is Null)');
    }
}