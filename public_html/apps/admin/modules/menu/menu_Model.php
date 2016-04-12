<?php

class menu_Model extends Model
{

    function __construct()
    {
        parent::__construct();
    }

    public function qry_all_menu()
    {
        @session::init();
        $v_website_id                   = session::get('session_website_id');
        $stmt                           = "Select * From t_ps_menu_position Where FK_WEBSITE = ?";
        $DATA_MODEL['arr_all_position'] = $this->db->getAll($stmt, array($v_website_id));

        $stmt = "Select 
                    * 
                From t_cores_list 
                Where FK_LISTTYPE=(select PK_LISTTYPE from t_cores_listtype where C_CODE='DM_THEME') 
                    And C_CODE = (select C_THEME_CODE from t_ps_website where PK_WEBSITE = ?)
                ";
        $DATA_MODEL['arr_theme_position'] = $this->db->getRow($stmt, $v_website_id);

        $stmt                       = "Select C_OPTION_VALUE From t_ps_option Where C_OPTION_KEY = 'WEBSITE_MENU'";
        $DATA_MODEL['website_menu'] = $this->db->getOne($stmt);
        return $DATA_MODEL;
    }

    public function qry_single_position($v_position_id)
    {
        @session::init();
        $v_website_id  = session::get('session_website_id');
        $v_position_id = replace_bad_char($v_position_id);
        if ($v_position_id == 0)
        {
            $stmt          = "Select PK_MENU_POSITION From t_ps_menu_position Where FK_WEBSITE = ?";
            $v_position_id = $this->db->getOne($stmt, array($v_website_id));

            $stmt = "select  PK_MENU,
                                C_NAME,
                                C_ORDER,
                                C_INTERNAL_ORDER,
                                FK_PARENT,
                                C_VALUE,
                                FK_MENU_POSITION 
                    from t_ps_menu where FK_MENU_POSITION = ? ORDER BY C_INTERNAL_ORDER";
            return $this->db->getAll($stmt, array($v_position_id));
        }
        else
        {
            $stmt  = "select COUNT(*) From t_ps_menu_position Where PK_MENU_POSITION=? and FK_WEBSITE = ?";
            $count = $this->db->getOne($stmt, array($v_position_id, $v_website_id));
            if ($count > 0)
            {
                $stmt = "select  PK_MENU,
                                C_NAME,
                                C_ORDER,
                                C_INTERNAL_ORDER,
                                FK_PARENT,
                                C_VALUE,
                                FK_MENU_POSITION 
                    from t_ps_menu where FK_MENU_POSITION = ? ORDER BY C_INTERNAL_ORDER";
                return $this->db->getAll($stmt, array($v_position_id));
            }
        }
        return array();
    }

    public function qry_single_menu($v_menu_id = 0)
    {
        @session::init();
        $v_website_id = session::get('session_website_id');
        $v_menu_id    = replace_bad_char($v_menu_id);
        $v_postion_id = get_post_var('hdn_position_id', '');
        if ($v_menu_id > 0)
        {
           

            $stmt                    = "select FK_WEBSITE from t_ps_menu_position 
                    where PK_MENU_POSITION = (Select FK_MENU_POSITION From t_ps_menu where PK_MENU = ?)";
            $v_website_id_of_menu_id = $this->db->getOne($stmt, $v_menu_id);
            if ($v_website_id_of_menu_id == $v_website_id)
            {
                $stmt                          = "Select m.PK_MENU,
                                m.FK_MENU_POSITION,
                                m.C_NAME,
                                m.C_VALUE,
                                m.C_ORDER,
                                m.FK_PARENT
                     From t_ps_menu m 
                     where m.PK_MENU = ?";
                $DATA_MODEL['arr_single_menu'] = $this->db->getRow($stmt, array($v_menu_id));
                $stmt                          = "select * from (Select * from t_ps_menu where FK_PARENT <>$v_menu_id or FK_PARENT is null) a
                            where a.PK_MENU<>$v_menu_id and a.FK_MENU_POSITION = $v_postion_id Order by C_INTERNAL_ORDER";
                $DATA_MODEL['arr_all_menu']    = $this->db->getAll($stmt);
                return $DATA_MODEL;
            }
        }
        $stmt                          = "Select m.C_ORDER
                      From t_ps_menu m where FK_MENU_POSITION = $v_postion_id order by C_ORDER desc";
        $DATA_MODEL['arr_single_menu'] = $this->db->getRow($stmt);
        $stmt                          = "Select * from t_ps_menu where FK_MENU_POSITION = $v_postion_id Order by C_INTERNAL_ORDER";
        $DATA_MODEL['arr_all_menu']    = $this->db->getAll($stmt);
        return $DATA_MODEL;
    }

    public function qry_all_category()
    {
        @session::init();
        $v_website_id = session::get('session_website_id');
        $stmt         = "select * from t_ps_category where FK_WEBSITE = ? Order by C_INTERNAL_ORDER";
        return $this->db->getAll($stmt, array($v_website_id));
    }

    public function swap_order($id, $id_swap)
    {
        $v_id          = replace_bad_char($id);
        $v_id_swap     = replace_bad_char($id_swap);
        $v_position_id = get_post_var('hdn_position_id');
        @session::init();
        $v_website_id  = session::get('session_website_id');
        $stmt          = "  select COUNT(FK_MENU_POSITION) from t_ps_menu_position inner join t_ps_menu
                    on PK_MENU_POSITION = FK_MENU_POSITION
                    where PK_MENU in (?,?) and FK_WEBSITE=?";
        $count         = $this->db->getOne($stmt, array($v_id, $v_id_swap, $v_website_id));
        if ($count > 1)
        {
            $stmt = "Select C_ORDER From t_ps_menu Where PK_MENU = ?";
            $temp = $this->db->getOne($stmt, array($v_id));
            //echo $temp;
            $stmt = "Update t_ps_menu set C_ORDER=(Select TEMP.C_ORDER From (Select C_ORDER From t_ps_menu Where PK_MENU=?) TEMP) Where PK_MENU=?";
            $this->db->Execute($stmt, array($v_id_swap, $v_id));

            $stmt = "Update t_ps_menu set C_ORDER = ? where PK_MENU= ?";
            $this->db->Execute($stmt, array($temp, $v_id_swap));
            
            $v_other_clause = " AND FK_MENU_POSITION = $v_position_id";
            
            $this->build_internal_order(
                    't_ps_menu', 'PK_MENU', 'FK_PARENT'
                    , 'C_ORDER', 'C_INTERNAL_ORDER', '-1', $v_other_clause);
        }
        $this->exec_done($this->goback_url, array('hdn_position_id' => $v_position_id));
    }

    public function update_position()
    {
        @session::init();
        $v_website_id   = session::get('session_website_id');
        $v_txt_name     = get_post_var('txt_position_name', '');
        $v_txt_new_name = get_post_var('txt_new_position_name', '');
        $v_position_id  = get_post_var('hdn_position_id');
        $v_sitemap      = isset($_POST['chk_sitemap'])? 1 : 0;
        $v_has_sitemap  = 0;
        // Kiem tra da co menu lua chon lam sitemap hay chua. Khi menu cap nhat chon lam sitemap 
        if($v_sitemap == 1)
        {
            $sql = "select COUNT(PK_MENU_POSITION) 
                            from t_ps_menu_position 
                            where C_TYPE =1 
                                    and FK_WEBSITE =?";
          $v_has_sitemap = $this->db->getOne($sql,array($v_website_id));
        }
        if ($v_txt_new_name == '')
        {
            $stmt                     = "Select FK_WEBSITE From t_ps_menu_position Where FK_WEBSITE= ? and PK_MENU_POSITION=?";
            $v_website_id_of_position = $this->db->getOne($stmt, array($v_website_id, $v_position_id));
            if ($v_website_id_of_position == $v_website_id)
            {
                if ($this->gp_check_user_permission('SUA_VI_TRI_MENU') > 0)
                {
                     //Loai bo lua chon nhung sitemap truoc
                    if($v_has_sitemap > 0)
                    {
                        $stmt = "Update t_ps_menu_position set C_TYPE = ? Where FK_WEBSITE= ? ";
                        $this->db->Execute($stmt,array('0',$v_website_id));
                    }
                    $stmt = "Update t_ps_menu_position set C_NAME = ?, C_TYPE = ? Where FK_WEBSITE= ? and PK_MENU_POSITION=?";
                    $this->db->Execute($stmt, array($v_txt_name, $v_sitemap, $v_website_id, $v_position_id));
                }
            }
        }
        else
        {
            if ($this->gp_check_user_permission('THEM_MOI_VI_TRI_MENU') > 0)
            {
                //Loai bo lua chon nhung sitemap truoc
                  if($v_has_sitemap == 1)
                  {
                        $stmt = "Update t_ps_menu_position set C_TYPE = 0 Where FK_WEBSITE = $v_website_id ";
                        $this->db->Execute($stmt);
                  }
                $stmt = "Insert into t_ps_menu_position(C_NAME,FK_WEBSITE,C_TYPE) values (?,?,?)";
                $this->db->Execute($stmt, array($v_txt_new_name, $v_website_id,$v_sitemap));
            }
        }
        if ($v_position_id == '')
        {
            $stmt          = "select PK_MENU_POSITION from t_ps_menu_position order by PK_MENU_POSITION desc";
            $v_position_id = $this->db->getOne($stmt);
        }
        $this->exec_done($this->goback_url, array('hdn_position_id' => $v_position_id));
    }

    public function delete_position()
    {
        @session::init();
        $v_website_id  = session::get('session_website_id');
        $v_position_id = get_post_var('hdn_position_id');

        $stmt                     = "Select FK_WEBSITE From t_ps_menu_position Where FK_WEBSITE= ? and PK_MENU_POSITION=?";
        $v_website_id_of_position = $this->db->getOne($stmt, array($v_website_id, $v_position_id));
        if ($v_website_id_of_position == $v_website_id)
        {
            if ($this->gp_check_user_permission('XOA_VI_TRI_MENU') > 0)
            {
                $arr_check = array('t_ps_menu'      => 'FK_MENU_POSITION');
                $sql_check_depend = $this->gp_build_check_depend_qry($arr_check, 'PK_MENU_POSITION');

                $stmt  = "Select sum(COUNT_DEPEND) From (
                                        SELECT *,$sql_check_depend
                                        FROM t_ps_menu_position) mp
                        WHERE mp.PK_MENU_POSITION = ?  AND mp.FK_WEBSITE = ?";
                $count = $this->db->getOne($stmt, array($v_position_id, $v_website_id));
                echo $count;
                if ($count < 1)
                {
                    $stmt = "Delete From t_ps_menu_position where PK_MENU_POSITION = $v_position_id and FK_WEBSITE = $v_website_id";
                    $this->db->Execute($stmt);
                }
            }
        }
        $this->exec_done($this->goback_url);
    }

    public function delete_menu()
    {
        @session::init();
        $v_website_id  = session::get('session_website_id');
        $v_position_id = get_post_var('hdn_position_id');
        $arr_menu_id   = explode(',', get_post_var('hdn_item_id_list'));
        $stmt          = "Select count(*) FRom t_ps_menu_position Where FK_WEBSITE = $v_website_id and PK_MENU_POSITION = $v_position_id";
        $count         = $this->db->getOne($stmt);
        if ($count > 0)
        {
            if ($this->gp_check_user_permission('XOA_MENU') > 0)
            {
                foreach ($arr_menu_id as $v_menu_id)
                {
                    $stmt = "Delete FROM t_ps_menu Where PK_MENU = $v_menu_id and FK_MENU_POSITION = $v_position_id";
                    $this->db->Execute($stmt);
                }
            }
        }
        $this->exec_done($this->goback_url, array('hdn_position_id' => $v_position_id));
    }

    function update_menu()
    {
        @session::init();
        $v_webste_id     = session::get('session_website_id');
        //lay thong tin truyen len
        $v_menu_id       = get_post_var('hdn_item_id', '');
        $v_position_id   = get_post_var('hdn_position_id', '');
        $v_menu_name     = get_post_var('txt_menu_name');
        $v_menu_parent   = get_post_var('menu_select', '0');
        $v_menu_type     = get_post_var('hdn_menu_type', '');
        $v_type_detail   = get_post_var('txt_menu_type_detail');
        $v_menu_order    = get_post_var('txt_menu_order','0');
        //danh cho reorder
        $other_clause    = " FK_MENU_POSITION = $v_position_id";
        $other_clause .= ($v_menu_parent != '') ? " And FK_PARENT = $v_menu_parent" : '0';
        $v_current_order = ($v_menu_id != '') ? get_post_var('hdn_current_order') : '-1';
        
        //de lieu default
        $v_data_module    = 0;
        $v_data_url       = 0;
        $v_data_category  = 0;
        $v_data_article   = 0;
        $v_module_value   = '';
        $v_article_id     = '';
        $v_article_slug   = '';
        $v_category_id    = '';
        $v_category_slug  = '';
        $v_internal_order = '001';
        
        //url
        if ($v_menu_type == 'url')
        {
            $v_data_url = 1;
        }
        //chuyen muc
        elseif ($v_menu_type == 'category')
        {
            $v_data_category = 1;
            $v_category_id   = get_post_var('hdn_category_id', '');
            $v_category_slug = get_post_var('hdn_category_slug', '');
        }
        //module
        elseif ($v_menu_type == 'module')
        {
            $v_data_module  = 1;
            $v_module_value = get_post_var('hdn_module_value', '');
        }
        //article
        else
        {
            $v_data_article  = 1;
            $v_article_id    = get_post_var('hdn_article_id', '');
            $v_article_slug  = get_post_var('hdn_article_slug', '');
            $v_category_id   = get_post_var('hdn_category_id', '');
            $v_category_slug = get_post_var('hdn_category_slug', '');
            
            if ($v_category_slug == '')
            {
            	$v_category_slug = $this->db->getOne('Select C_SLUG From t_ps_category Where PK_CATEGORY=?', array($v_category_id));
            }
        }
        //tao xml cho menu
        $v_xml           = '';
        $v_xml .="<MenuType>";
        $v_xml .='<item data="' . $v_data_category . '" type="category"><id>' . $v_category_id . '</id><name>' . $v_type_detail . '</name><slug>' . $v_category_slug . '</slug></item>';
        $v_xml .='<item data="' . $v_data_url . '" type="url">' . $v_type_detail . '</item>';
        $v_xml .='<item data="' . $v_data_article . '" type="article">
                                <article_id>' . $v_article_id . '</article_id>
                                <article_slug>' . $v_article_slug . '</article_slug>
                                <category_id>' . $v_category_id . '</category_id>
                                <category_slug>' . $v_category_slug . '</category_slug>
                                <title>' . $v_type_detail . '</title>
                          </item>';
        $v_xml .='<item data="' . $v_data_module . '" type="module" value="' . $v_module_value . '">' . $v_type_detail . '</item>';
        $v_xml .="</MenuType>";
        
        //update menu
        if ($v_menu_id != '')
        {
            //kiem tra website id
            $stmt                = "Select ap.FK_WEBSITE From t_ps_menu a inner join t_ps_menu_position ap
                        on ap.PK_MENU_POSITION = a.FK_MENU_POSITION Where a.PK_MENU = ? ";
            $v_website_id_of_adv = $this->db->getOne($stmt, array($v_menu_id));
            if ($v_website_id_of_adv == $v_webste_id)
            {
                //kiem tra quyen sua menu
                if ($this->gp_check_user_permission('SUA_MENU') > 0)
                {
                    //update khi la menu parent
                    if ($v_menu_parent == '0')
                    {
                        $stmt = "Update t_ps_menu set FK_PARENT=?,C_NAME=?,C_VALUE=?
                                Where PK_MENU= ? and FK_MENU_POSITION=? ";
                        $this->db->Execute($stmt, array('0', $v_menu_name, $v_xml, $v_menu_id, $v_position_id));
                    }
                    //update khi la menu con
                    else
                    {
                        $stmt = "Update t_ps_menu set FK_PARENT=?,C_NAME=?,C_VALUE=?,C_ORDER=?
                                Where PK_MENU= ? and FK_MENU_POSITION=? ";
                        $this->db->Execute($stmt, array($v_menu_parent, $v_menu_name, $v_xml, $v_menu_order, $v_menu_id, $v_position_id));
                    }
                }
                //ko co quyen
                else
                {
                    echo "<script>alert('Bạn không có quyền thực hiện thao tác này !!!');</script>";
                    $arr_filter = get_filter_condition(array('sel_goto_page', 'sel_rows_per_page'));
                    $this->exec_done($this->goback_url, $arr_filter);
                }
            }
        }
        //insert menu
        else
        {
            //kiem tra quyen them moi
            if ($this->gp_check_user_permission('THEM_MOI_MENU') > 0)
            {
                //kiem tra website id
                $stmt                = "Select FK_WEBSITE From t_ps_menu_position Where PK_MENU_POSITION = ?";
                $v_website_id_of_adv = $this->db->getOne($stmt, array($v_position_id));

                if ($v_website_id_of_adv == $v_webste_id)
                {
                    //menu parent
                    if ($v_menu_parent == '0')
                    {
                        $stmt = "insert into t_ps_menu(FK_MENU_POSITION,FK_PARENT,C_NAME,C_ORDER,C_VALUE,C_INTERNAL_ORDER)
                               values(?,?,?,?,?,?)";
                        $this->db->Execute($stmt, array($v_position_id, 0, $v_menu_name, $v_menu_order, $v_xml, $v_internal_order));
                        $stmt      = "select PK_MENU from t_ps_menu order by PK_MENU desc";
                        $v_menu_id = $this->db->getOne($stmt);
                    }
                    //menu con
                    else
                    {
                        $stmt = "insert into t_ps_menu(FK_MENU_POSITION,FK_PARENT,C_NAME,C_ORDER,C_VALUE,C_INTERNAL_ORDER)
                               values(?,?,?,?,?,?)";
                        $this->db->Execute($stmt, array($v_position_id, $v_menu_parent, $v_menu_name, $v_menu_order, $v_xml, $v_internal_order));

                        $stmt      = "select PK_MENU from t_ps_menu order by PK_MENU desc";
                        $v_menu_id = $this->db->getOne($stmt);
                    }
                }
            }
            //ko co quyen
            else
            {
                echo "<script>alert('Bạn không có quyền thực hiện thao tác này !!!');</script>";
                $arr_filter = get_filter_condition(array('sel_goto_page', 'sel_rows_per_page'));
                $this->exec_done($this->goback_url, $arr_filter);
            }
        }
        //re order all
        $this->ReOrder('t_ps_menu', 'PK_MENU', 'C_ORDER', $v_menu_id, $v_menu_order, $v_current_order, $other_clause);
        $v_other_clause = " AND FK_MENU_POSITION = $v_position_id";
        $this->build_internal_order(
                't_ps_menu', 'PK_MENU', 'FK_PARENT'
                , 'C_ORDER', 'C_INTERNAL_ORDER', '-1',$v_other_clause);
       $this->exec_done($this->goback_url, array('hdn_position_id' => $v_position_id));
    }

    public function update_theme_position()
    {
        $v_website_id       = session::get('session_website_id');
        $v_hdn_item_id_list = get_post_var('hdn_item_id_list');
        $arr_list           = explode(',', $v_hdn_item_id_list);

        $xml = '<theme_position id_website="' . $v_website_id . '">';
        foreach ($arr_list as $row)
        {
            $arr_row            = explode(':', $row);
            $v_position_code    = $arr_row[0];
            $v_position_menu_id = $arr_row[1];
            $xml.='<item position_menu_id="' . $v_position_menu_id . '" position_code="' . $v_position_code . '" />';
        }
        $xml.='</theme_position>';
        if ($this->gp_check_user_permission('SUA_THEME_POSITION') > 0)
        {
            $stmt  = "Select C_OPTION_VALUE From t_ps_option Where C_OPTION_KEY = 'WEBSITE_MENU'";
            $v_xml = $this->db->getOne($stmt);
            $dom   = simplexml_load_string($v_xml);
            if (is_object($dom))
            {
                $x_path = '//theme_position[@id_website=' . $v_website_id . ']';
                $r      = $dom->xpath($x_path);
            }
            else
            {
                $stmt = "
                    Insert Into t_ps_option(C_OPTION_KEY, C_OPTION_VALUE)
                    Values('WEBSITE_MENU', '<data></data>')
                ";
                $this->db->Execute($stmt);
            }
            
            
            if (isset($r[0]))
            {
                
                if(DATABASE_TYPE == 'MSSQL')
                {
                    $stmt = "UPDATE t_ps_option	
                            SET C_OPTION_VALUE.modify('delete $x_path')
                            WHERE C_OPTION_KEY = 'WEBSITE_MENU'";
                    $this->db->Execute($stmt);
                    $stmt = "UPDATE t_ps_option
                            SET C_OPTION_VALUE.modify('insert $xml as last into (/data)[1]')
                            WHERE C_OPTION_KEY = 'WEBSITE_MENU'";
                    $this->db->Execute($stmt);
                }
                else if(DATABASE_TYPE == 'MYSQL')
                {
                    $stmt = "UPDATE t_ps_option
                            SET C_OPTION_VALUE = UpdateXML(C_OPTION_VALUE,'$x_path','$xml')
                            WHERE C_OPTION_KEY = 'WEBSITE_MENU'";
                    $this->db->Execute($stmt);
                }
            }
            else
            {
                if(DATABASE_TYPE == 'MSSQL')
                {
                    $stmt = "UPDATE t_ps_option
                                SET C_OPTION_VALUE.modify('insert $xml as last into (/data)[1]')
                                WHERE C_OPTION_KEY = 'WEBSITE_MENU'";
                    $this->db->Execute($stmt);
                }
                else if(DATABASE_TYPE == 'MYSQL')
                {
                    //lay xml 
                    $stmt  = "Select C_OPTION_VALUE From t_ps_option Where C_OPTION_KEY = 'WEBSITE_MENU'";
                    $v_xml = $this->db->getOne($stmt);
                    
                    //them node moi vao xml
                    $v_xml = str_replace('<data>', "<data>$xml", $v_xml);
                    
                    //cap nhat xml
                    $sql = "UPDATE t_ps_option SET C_OPTION_VALUE = '$v_xml' WHERE C_OPTION_KEY = 'WEBSITE_MENU'";
                    $this->db->Execute($sql);
                }
                
            }
        }
        $this->exec_done($this->goback_url);
    }

}

?>