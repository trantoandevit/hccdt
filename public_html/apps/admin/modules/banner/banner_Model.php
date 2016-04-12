<?php

class banner_Model extends Model {
	/**
	 * 
	 * @var \ADOConnection
	 */
	public $db;
	
    function __construct() {
        parent::__construct();
    }

    public function qry_all_banner() 
    {
       @session::init();
       $v_website_id=session::get('session_website_id');
       page_calc($v_start, $v_end);
       if(DATABASE_TYPE=='MYSQL')
       {     
	       	/*Tinh toan phan trang	
       		$v_start = $v_start - 1;
       		$v_limit = $v_end - $v_start;
       		*/
       		$stmt = "SELECT PK_BANNER,
		       				FK_WEBSITE,
		       				FK_MEDIA,
		       				C_STATUS,
		       				C_DEFAULT,
		       				C_FILE_NAME
	       			 FROM t_ps_banner 
	       			 WHERE  FK_WEBSITE=? 
	       			 ORDER BY PK_BANNER DESC "; 
       		return $this->db->getAll($stmt,array($v_website_id));       		
       }
       else
       {  	       
	       $sql="Select *,ROW_NUMBER() OVER (ORDER BY PK_BANNER) as RN From t_ps_banner";
	       $stmt = "SELECT a.PK_BANNER,
	                        a.FK_WEBSITE,
	                        a.C_FILE_NAME,
	                        a.C_STATUS,
	                        a.C_DEFAULT
	                 FROM
	                   ($sql) a
	                 WHERE a.FK_WEBSITE = ?
	                   AND a.rn>=?
	                   AND a.rn<=?
	                 ORDER BY a.rn ";
	               
	       return $this->db->getAll($stmt,array($v_website_id,$v_start,$v_end));
    	}
    	
    }
    public function delete_banner() 
    {
       $v_website_id = session::get('session_website_id');
       echo "web id=:".$v_website_id;
       $v_banner_list = get_post_var('hdn_item_id_list');
       echo 'pp:'.$v_banner_list;
       
       if($this->gp_check_user_permission('XOA_BANNER')>0)   
       {
           $stmt="Delete From t_ps_banner_category Where FK_BANNER IN ($v_banner_list) And FK_CATEGORY IN (Select PK_CATEGORY From t_ps_category Where FK_WEBSITE=?)";
           $this->db->Execute($stmt,array($v_website_id));       
       
           $stmt="Delete From t_ps_banner Where PK_BANNER in ($v_banner_list) And FK_WEBSITE=?";
           $this->db->Execute($stmt,array($v_website_id));   
       }
       $this->exec_done($this->goback_url);
    }

    public function qry_single_banner($v_id_banner=0) 
    {
        @session::init();
        $v_website_id = session::get('session_website_id');
        if($v_id_banner>0)
        {
            $stmt="Select FK_WEBSITE From t_ps_banner where FK_WEBSITE = ? and PK_BANNER = ?";
            $v_website_id_of_banner = $this->db->getOne($stmt,array($v_website_id,$v_id_banner));
            if($v_website_id_of_banner == $v_website_id)
            {
                $stmt = "select b.* from t_ps_banner b
                    where PK_BANNER = ?";
                $DATA_MODEL['arr_single_banner'] = $this->db->getRow($stmt, array($v_id_banner));

                $stmt = "select FK_CATEGORY from t_ps_banner_category where FK_BANNER = ?";
                $DATA_MODEL['arr_all_cat_to_check'] = $this->db->getCol($stmt, array($v_id_banner));

                $DATA_MODEL['arr_all_category_on_web'] = $this->qry_all_category_on_web($v_id_banner);
                return $DATA_MODEL;
            }
        }
        $DATA_MODEL['arr_all_category_on_web'] = $this->qry_all_category_on_web($v_id_banner);
        return $DATA_MODEL;
    
    }
    public function qry_all_category_on_web($v_id_banner)
    {
        @session::init();
        $v_website_id = session::get('session_website_id');
        $stmt="Select FK_WEBSITE From t_ps_banner where FK_WEBSITE = ? and PK_BANNER = ?";
        $v_website_id_of_banner = $this->db->getOne($stmt,array($v_website_id,$v_id_banner));
        if($v_website_id_of_banner == $v_website_id)
        {        	
             $stmt = "select *,(select COUNT(*)from t_ps_banner_category where FK_CATEGORY=PK_CATEGORY and FK_BANNER not in (?)) as C_DEPEND 
                	from t_ps_category where FK_WEBSITE = ? order by C_INTERNAL_ORDER";
             return $this->db->getAll($stmt,array($v_id_banner,$v_website_id));
        }
        else
        {
        		/* Hiện danh sách các chuyên mục của chuyên trang hiện tại khi thêm mới.*/
        		 $stmt = "select *,(select COUNT(*)from t_ps_banner_category where FK_CATEGORY=PK_CATEGORY and FK_BANNER not in (?)) as C_DEPEND 
              	from t_ps_category  where FK_WEBSITE = ? order by C_INTERNAL_ORDER";        	
        		return $this->db->getAll($stmt,array($v_id_banner,$v_website_id));
        		
        }
       return array();
    }
    
    function update_banner()
    {
        @session::init();
        $v_website_id   =   session::get('session_website_id');
        $v_banner_id    = get_post_var('hdn_item_id',0);
          
        is_id_number($v_banner_id) OR $v_banner_id = 0;
        //var_dump($_POST);
        $arr_category   = (get_post_var('hdn_item_id_list')=="")?array():  explode(',', get_post_var('hdn_item_id_list'));
        $v_file_name     = get_post_var('hdn_file_name',"");
        isset($_POST['chk_banner_status'])?  $v_status = 1 : $v_status = 0; 
        isset($_POST['chk_banner_default'])?  $v_default = 1 : $v_default = 0;        
        //$v_status       = get_post_var('banner_status',0);
        //$v_default      = get_post_var('banner_default',0);
        
        if($v_file_name !="")
        {
            if($v_banner_id > 0)
            {
                $stmt="Select FK_WEBSITE From t_ps_banner where FK_WEBSITE = ? and PK_BANNER = ?";
                $v_website_id_of_banner = $this->db->getOne($stmt,array($v_website_id,$v_banner_id));
                if($v_website_id_of_banner == $v_website_id)
                {
                    if($this->gp_check_user_permission('SUA_BANNER')>0)
                    {
                        $stmt="Update t_ps_banner set  FK_WEBSITE=?,C_FILE_NAME=?,C_STATUS=?,C_DEFAULT=? Where PK_BANNER = ?";
                        $this->db->Execute($stmt,array($v_website_id,$v_file_name,$v_status,$v_default,$v_banner_id));

                        $stmt="Delete From t_ps_banner_category Where FK_BANNER =?";
                        $this->db->Execute($stmt,array($v_banner_id));
                        if($v_default==1)
                        {
                            $stmt="Update t_ps_banner set C_DEFAULT=0 Where PK_BANNER not in(?) AND FK_WEBSITE = ?";
                            $this->db->Execute($stmt,array($v_banner_id,$v_website_id));
                        }
                        foreach($arr_category as $v_category_id)
                        {
                            $stmt="Insert into t_ps_banner_category(FK_BANNER,FK_CATEGORY) values(?,?)";
                            $this->db->Execute($stmt,array($v_banner_id,$v_category_id));
                        }
                    }
                    else
                    {
                        echo  "<script>alert('Báº¡n khÃ´ng cÃ³ quyá»�n thá»±c hiá»‡n thao tÃ¡c nÃ y !!!');</script>";
                        $arr_filter = get_filter_condition(array('sel_goto_page', 'sel_rows_per_page'));
                        $this->exec_done($this->goback_url, $arr_filter);
                    }
                }
            }
            else 
            {
                if($this->gp_check_user_permission('THEM_MOI_BANNER')>0)
                {
                    $stmt="Insert into t_ps_banner(FK_WEBSITE,C_FILE_NAME,C_STATUS,C_DEFAULT) values(?,?,?,?)";
                    $this->db->Execute($stmt,array($v_website_id,$v_file_name,$v_status,$v_default));

                    $stmt="SELECT LAST_INSERT_ID(PK_BANNER) FROM t_ps_banner ORDER BY PK_BANNER DESC";
                    $v_new_banner_id = $this->db->getOne($stmt);                    
                    if($v_default==1)
                    {
                        $stmt="Update t_ps_banner set C_DEFAULT=0 Where PK_BANNER not in(?) AND FK_WEBSITE = ?";
                        $this->db->Execute($stmt,array($v_new_banner_id,$v_website_id));
                    }

                    for($i=0;$i<count($arr_category);$i++)
                    {
                        $v_category_id = $arr_category[$i];
                        $stmt="Insert into t_ps_banner_category(FK_BANNER,FK_CATEGORY) values (?,?)";
                        $this->db->Execute($stmt,array($v_new_banner_id,$v_category_id));
                    }
                }
                else 
                {
                    echo  "<script>alert('Báº¡n khÃ´ng cÃ³ quyá»�n thá»±c hiá»‡n thao tÃ¡c nÃ y !!!');</script>";
                    $arr_filter = get_filter_condition(array('sel_goto_page', 'sel_rows_per_page'));
                    $this->exec_done($this->goback_url, $arr_filter);
                }
            }
        }
        //HoÃ n thÃ nh vÃ  lÆ°u Ä‘iá»�u kiá»‡n lá»�c
        $arr_filter = get_filter_condition(array('sel_goto_page', 'sel_rows_per_page'));
        $this->exec_done($this->goback_url, $arr_filter);
    }
}
?>