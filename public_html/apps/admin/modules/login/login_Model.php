<?php if (!defined('SERVER_ROOT')) exit('No direct script access allowed');

class login_Model extends Model {

    function __construct()
    {
        parent::__construct();
    }

    public function do_login()
    {
        if (!isset($_POST['txt_login_name']))
        {
            $this->exec_done($this->goback_url);
            return;
        }

        $v_login_name   = get_post_var('txt_login_name', NULL);
        $v_password     = get_post_var('txt_password', NULL);
        
        $redirect_url   = get_post_var('hdn_redirect_url', '');
        $check_permit   = get_post_var('hdn_check_permit', '');
        
        $v_password     = encrypt_password($v_password);
        
        if($v_password == NULL)
        {
            echo '<script>alert("Phai nhap [Mat khau]!"); document.location.replace("index.php");</script>\n';
            $this->exec_done($this->goback_url);
            exit();
        }

            $stmt = 'Select u.PK_USER
                            ,u.FK_OU
                            ,u.C_NAME as C_USER_NAME
                            ,u.C_LOGIN_NAME
                            ,u.C_XML_DATA
                            ,u.C_IS_ADMIN
                            ,u.C_JOB_TITLE
                            ,ou.C_NAME as C_OU_NAME
                    From t_cores_user u Left Join t_cores_ou as ou On u.FK_OU=ou.PK_OU
                    Where u.C_LOGIN_NAME=? And u.C_PASSWORD=? And u.C_STATUS = 1';
            $params = array($v_login_name, $v_password);
            $arr_single_user = $this->db->getRow($stmt, $params);
       // exit;
        if (sizeof($arr_single_user) > 0) {
            @session::init();
            $v_user_id = $arr_single_user['PK_USER'];

            session::set('login_name', $v_login_name);
            session::set('user_login_name', $v_login_name);
            session::set('user_name', $arr_single_user['C_USER_NAME']);
            session::set('user_code', $v_login_name);
            session::set('user_id', $arr_single_user['PK_USER']);
            session::set('ou_id', $arr_single_user['FK_OU']);
            session::set('ou_name', $arr_single_user['C_OU_NAME']);
            session::set('user_granted_xml', $arr_single_user['C_XML_DATA']);
            //session::set('auth_by', $v_auth_by);
            session::set('user_job_title', $arr_single_user['C_JOB_TITLE']);
            session::set('time_to_join',date("d/m/Y H:i:s"));
            session::set('menu_select','div_menu_content');

            //Danh sách nhóm mà NSD là thành viên
            $stmt = 'Select G.C_CODE
                    From t_cores_group G Left Join t_cores_user_group UG On G.PK_GROUP=UG.FK_GROUP
                    Where UG.FK_USER=?';
            $params = array($arr_single_user['PK_USER']);
            $arr_group_code = $this->db->getCol($stmt, $params);
            session::set('arr_group_code', $arr_group_code);
            $v_is_admin = in_array('ADMINISTRATORS', $arr_group_code)?1:0;
           
            session::set('is_admin', $v_is_admin);
            
            //User Token 
            session::set('user_token', md5(uniqid()));
            
            //var_dump(session::get('is_admin'));exit;
            //La thanh vien ban lanh dao?
           /* if (in_array(_CONST_BOD_GROUP_CODE, $arr_group_code))
            {
                session::set('is_bod_member',1);
            }*/

            //Cap nhat thong tin lan dang nhap cua cua NSD
            $stmt  = 'Update t_cores_user Set C_LAST_LOGIN_DATE=Now() Where PK_USER=?';
            $this->db->Execute($stmt, array($arr_single_user['PK_USER']));

            //Danh sach quyen
            //Cau truc MA_UNG_DUNG::MA_CHUC_NANG
           
            $stmt = "Select Concat(Upper(w.PK_WEBSITE) , '::' , UF.C_FUNCTION_CODE) as C_FUNCTION_CODE
                    From t_cores_user_function UF 
                    Left Join t_ps_website w ON UF.FK_WEBSITE=w.PK_WEBSITE
                    Where UF.FK_USER=?
                    UNION
                    Select Concat(Upper(w.PK_WEBSITE) , '::' , GF.C_FUNCTION_CODE) as C_FUNCTION_CODE
                    From t_cores_group_function GF 
                    Left Join t_ps_website w ON GF.FK_WEBSITE=w.PK_WEBSITE
                    Where  FK_GROUP IN
                        (SELECT FK_GROUP
                         FROM t_cores_user_group
                         WHERE FK_USER=?)";
            session::set('arr_all_grant_function_code', $this->db->getCol($stmt, array($v_user_id, $v_user_id)) ) ; 
           
            $stmt = "Select g.C_CODE From t_cores_group g 
                        inner join 
                        t_cores_user_group ug
                        on  g.PK_GROUP = ug.FK_GROUP
                        Where FK_USER = ? ";
            session::set('arr_all_grant_group_code',$this->db->getCol($stmt, array($v_user_id)));
            
            $stmt="select distinct C_FUNCTION_CODE from t_cores_user_function where FK_USER = ?
                    union
                    select distinct C_FUNCTION_CODE from t_cores_group_function where FK_GROUP in
                    (select FK_GROUP from t_cores_user_group where FK_USER = ?)";
            $arr_all_grant_function_without_web = $this->db->getCol($stmt, array($v_user_id,$v_user_id));
            
            $v_user_id    = Session::get('user_id');
            $qry_group    = "SELECT DISTINCT(FK_GROUP) FROM t_cores_user_group WHERE FK_USER = ?";
            $arr_group_id = $this->db->GetCol($qry_group,array($v_user_id));
            
            //  array category id permission
            $v_condition_group_cat = "";
            if(count($arr_group_id) > 0)
            {
                $v_condition_group_cat = " And  FK_GROUP in(" . implode(',', $arr_group_id) . ")";
            }
            $arr_granted_category = array();
            if ($_SESSION['is_admin'] != 1)
            {
                $stmt = "SELECT
                                FK_CATEGORY
                              FROM t_ps_group_category
                              WHERE 1=1  ". $v_condition_group_cat ." UNION SELECT
                                                          FK_CATEGORY
                                                        FROM t_ps_user_category
                                                        WHERE FK_USER = ?";
                $arr_granted_category =  $this->db->GetCol($stmt,array($v_user_id));
              
            }
           
            Session::set('granted_category', $arr_granted_category);
           
            //redirect
            if(!isset($arr_all_grant_function_without_web))
            {
                $arr_all_grant_function_without_web = array();
            }
            session::set('arr_all_grant_function_without_web', $arr_all_grant_function_without_web) ; 
            
            //ton tai file htaccess
            $detect = new Mobile_Detect();
            
            if (file_exists(SERVER_ROOT . '.htaccess'))
            {
                $this->header_location($redirect_url,$check_permit);
                //Mobile
                if ($detect->isMobile() && Cookie::get('pc_mode', 0) == 0)
                {
                    unset($detect);
                    $this->exec_done(SITE_ROOT . 'admin/article/dsp_approve_article');
                }
                else
                {
                    $this->exec_done(SITE_ROOT . 'admin/dashboard');
                }
            }
            //khong co file htaccess
            else
            {
                $this->header_location($redirect_url,$check_permit);
                //Mobile
                if ($detect->isMobile() && Cookie::get('pc_mode', 0) == 0)
                {
                    unset($detect);
                    $this->exec_done('index.php?url=admin/article/dsp_approve_article');
                }
                else
                {
                    $this->exec_done('index.php?url=admin/dashboard');
                }
            }
            exit;
           
        } else {
            session::destroy();
            echo '<script>alert("Sai [Ten dang nhap] hoac [Mat khau]!");</script>';
            $this->exec_done($this->goback_url);
            exit();
        }
    }
    
    public function header_location($redirect_url,$check_permit)
    {
        if($redirect_url != '')
        {
            if($check_permit != '' && session::check_permission($check_permit,FALSE) == FALSE)
            {
                die('Bạn không có quyền thực hiện chức năng này !!!');
                exit();
            }
            $location = urldecode($redirect_url);
            header('location:'.$location);
            exit();
        }
    }
}