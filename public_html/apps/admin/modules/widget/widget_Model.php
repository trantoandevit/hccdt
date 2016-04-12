<?php

defined('DS') or die('no direct access');

class widget_Model extends Model
{

    public function __construct()
    {
        parent::__construct();
    }

    function qry_all_widget()
    {
        if(DATABASE_TYPE == 'MSSQL')
        {
            $sql = "Select L.C_CODE, L.C_NAME
                    , L.C_XML_DATA.value('(/data/item[@id=\"txt_summary\"]/value)[1]','nvarchar(1000)') as C_SUMMARY
                    From t_cores_list L
                    Inner Join t_cores_listtype LT
                    On L.FK_LISTTYPE = LT.PK_LISTTYPE
                    Where LT.C_CODE = 'DM_WIDGET'
                    Order By L.C_NAME
                    ";
        }
        else if(DATABASE_TYPE == 'MYSQL')
        {
            $sql = "Select L.C_CODE, L.C_NAME
                    ,ExtractValue(L.C_XML_DATA, '/data/item[@id=\"txt_summary\"]/value') as C_SUMMARY
                    From t_cores_list L
                    Inner Join t_cores_listtype LT
                    On L.FK_LISTTYPE = LT.PK_LISTTYPE
                    Where LT.C_CODE = 'DM_WIDGET'
                    Order By L.C_NAME
                    ";
        }
        
        return $this->db->getAll($sql);
    }

    function qry_single_website($website_id)
    {
        return $this->db->getRow("Select C_CODE, C_THEME_CODE From t_ps_website Where PK_WEBSITE = $website_id");
    }

    function qry_current_widget($website, $theme, $position)
    {
        $sql = "Select WTW.C_WIDGET_CODE, WTW.C_PARAM 
            , L.C_NAME, WTW.PK_WEBSITE_THEME_WIDGET
            , WTW.C_POSITION_CODE
            From t_ps_website_theme_widget WTW
            Inner JOin t_cores_list L
            On L.C_CODE = WTW.C_WIDGET_CODE
            Inner Join t_cores_listtype LT
            On LT.PK_LISTTYPE = L.FK_LISTTYPE
            Where WTW.C_WEBSITE_CODE = '$website'
            And WTW.C_THEME_CODE = '$theme'
            And WTW.C_POSITION_CODE = '$position'
            And LT.C_CODE = 'DM_WIDGET'
            Order by WTW.C_ORDER"
        ;
        return $this->db->getAll($sql);
    }

    function qry_all_position()
    {
        $v_website_id       = Session::get('session_website_id');
        $arr_single_website = $this->qry_single_website($v_website_id);
        if (empty($arr_single_website))
        {
            die('You dont have any website');
        }

        $v_website_code = $arr_single_website['C_CODE'];
        $v_theme_code   = $arr_single_website['C_THEME_CODE'];
        
        
        if(DATABASE_TYPE == 'MSSQL')
        {
            $sql = "Select C_XML_DATA.value('(/data/item[@id=\"txtvitriwidget\"]/value)[1]', 'nvarchar(255)')
            From t_cores_list L
            Inner JOin t_cores_listtype LT
            On L.FK_LISTTYPE = LT.PK_LISTTYPE
            Where LT.C_CODE = 'DM_THEME'
            And L.C_CODE = '$v_theme_code'";
        }
        else if(DATABASE_TYPE == 'MYSQL')
        {
            $sql = "Select ExtractValue(C_XML_DATA, '/data/item[@id=\"txtvitriwidget\"]/value')
            From t_cores_list L
            Inner JOin t_cores_listtype LT
            On L.FK_LISTTYPE = LT.PK_LISTTYPE
            Where LT.C_CODE = 'DM_THEME'
            And L.C_CODE = '$v_theme_code'";
        }
        
        return $this->db->getOne($sql);
    }

    function update_widget()
    {
        $v_code             = get_post_var('code');
        $v_id               = intval(get_post_var('widget_id'));
        $v_new_order        = intval(get_post_var('new_order'));
        $v_position         = get_post_var('position');
        $v_website_id       = Session::get('session_website_id');
        $v_param            = (string) get_post_var('param', '', false);
        $arr_single_website = $this->qry_single_website($v_website_id);

        if (!$v_code or !$v_position)
        {
            return 0;
        }
        if (empty($arr_single_website))
        {
            return 0;
        }

        $v_website_code = $arr_single_website['C_CODE'];
        $v_theme_code   = $arr_single_website['C_THEME_CODE'];
        if ($v_id == 0)
        {
            $sql   = "Insert Into t_ps_website_theme_widget
                (C_WEBSITE_CODE, C_THEME_CODE, C_WIDGET_CODE, C_POSITION_CODE, C_PARAM )
                Values(?, ?, ?, ?, ?)";
            $param = array($v_website_code, $v_theme_code, $v_code, $v_position, $v_param);
        }
        else
        {
            if(DATABASE_TYPE == 'MSSQL')
            {
                $sql   = " Update t_ps_website_theme_widget
                        Set C_POSITION_CODE = ?
                        ,C_PARAM = ?
                        From t_ps_website_theme_widget
                        Where PK_WEBSITE_THEME_WIDGET = ?
                        And C_WEBSITE_CODE = '$v_website_code'";
                $param = array($v_position, $v_param, $v_id);
            }
            else if(DATABASE_TYPE == 'MYSQL')
            {
                $sql   = " Update t_ps_website_theme_widget
                        Set C_POSITION_CODE = ?
                        ,C_PARAM = ?
                        Where PK_WEBSITE_THEME_WIDGET = ?
                        And C_WEBSITE_CODE = '$v_website_code'";
                $param = array($v_position, $v_param, $v_id);
            }
            
            
        }

        $this->db->Execute($sql, $param);
        if ($this->db->errorNo() == 0)
        {
            if ($v_id == 0)
            {
                if(DATABASE_TYPE == 'MSSQL')
                {
                    $v_id            = $this->db->getOne("Select IDENT_CURRENT('t_ps_website_theme_widget')");
                }
                else if(DATABASE_TYPE == 'MYSQL')
                {
                    $v_id            = $this->db->getOne("SELECT MAX(PK_WEBSITE_THEME_WIDGET) FROM t_ps_website_theme_widget");
                }
                
            }
            $sql             = "Select C_ORDER From t_ps_website_theme_widget 
                                Where PK_WEBSITE_THEME_WIDGET = $v_id";
            $v_new_order     = $v_new_order + 1;
            $v_current_order = intval($this->db->getOne($sql));

            $other_clause = "  C_WEBSITE_CODE = '$v_website_code'
            And C_THEME_CODE  = '$v_theme_code'
            And C_POSITION_CODE = '$v_position'";
            $this->ReOrder(
                    't_ps_website_theme_widget'
                    , 'PK_WEBSITE_THEME_WIDGET'
                    , 'C_ORDER'
                    , $v_id
                    , $v_new_order
                    , $v_current_order
                    , $other_clause
            );
            return $v_id;
        }
    }

    function remove_widget()
    {
        $v_id               = intval(get_post_var('widget_id'));
        $arr_single_website = $this->qry_single_website(Session::get('session_website_id'));
        $v_website_code     = $arr_single_website['C_CODE'];
        if ($v_id)
        {
            $sql = "Delete From t_ps_website_theme_widget 
                Where PK_WEBSITE_THEME_WIDGET In (
                    select TEMP.PK_WEBSITE_THEME_WIDGET from (Select PK_WEBSITE_THEME_WIDGET
                    From t_ps_website_theme_widget
                    Where PK_WEBSITE_THEME_WIDGET = $v_id
                    ANd C_WEBSITE_CODE = '$v_website_code') TEMP
                )";
        }
        $this->db->Execute($sql);
    }

    function qry_all_poll()
    {
        $v_website_id = Session::get('session_website_id');
        $sql          = "Select PK_POLL, C_NAME
            From t_ps_poll
            Where C_STATUS = 1
            And FK_WEBSITE = $v_website_id";

        return $this->db->getAssoc($sql);
    }

    function qry_all_spotlight()
    {
        $v_website_id = Session::get('session_website_id');
        $sql          = "Select PK_SPOTLIGHT_POSITION, C_NAME 
            From t_ps_spotlight_position
            Where FK_WEBSITE = $v_website_id
            ";
        return $this->db->getAssoc($sql);
    }
    
    function qry_all_event()
    {
        $v_website_id = Session::get('session_website_id');
        $sql = "SELECT PK_EVENT
                       , C_NAME
                FROM t_ps_event
                WHERE NOW() >= C_BEGIN_DATE
                  AND C_END_DATE >= NOW()
                  AND C_STATUS = 1
                  AND FK_WEBSITE = $v_website_id
                ORDER BY C_ORDER ASC
                ";
        return $this->db->GetAssoc($sql);
    }
    
    public function qry_all_category()
    {
        @session::init();
        $v_website_id = session::get('session_website_id');
        $stmt         = "SELECT
                            PK_CATEGORY,
                            concat_name(C_NAME,(LENGTH(C_INTERNAL_ORDER)/3 - 1))
                          FROM t_ps_category
                          WHERE FK_WEBSITE = ?
                              AND C_STATUS = 1
                          ORDER BY C_INTERNAL_ORDER";
        return $this->db->GetAssoc($stmt, array($v_website_id));
    }
    
    function qry_all_adv()
    {
        $v_website_id = Session::get('session_website_id');
        $sql          = "Select PK_ADV_POSITION, C_NAME
            From t_ps_advertising_position
            Where FK_WEBSITE = $v_website_id";
        return $this->db->getAssoc($sql);
    }

    function qry_all_widget_class()
    {
        $arr_single_website = $this->qry_single_website(Session::get('session_website_id'));
        $v_theme_code = $arr_single_website['C_THEME_CODE'];
        
        if(DATABASE_TYPE == 'MSSQL')
        {
            $sql                = " Select C_XML_DATA.value('(//item[@id=\"txt_widget_class\"])[1]', 'Varchar(255)')
                                From t_cores_list L
                                Inner Join t_cores_listtype LT
                                On L.FK_LISTTYPE = LT.PK_LISTTYPE
                                Where LT.C_CODE = 'DM_THEME'
                                And L.C_CODE = '$v_theme_code'";
        }
        else if(DATABASE_TYPE == 'MYSQL')
        {
            $sql                = " Select ExtractValue(C_XML_DATA, '//item[@id=\"txt_widget_class\"]/value')
                                From t_cores_list L
                                Inner Join t_cores_listtype LT
                                On L.FK_LISTTYPE = LT.PK_LISTTYPE
                                Where LT.C_CODE = 'DM_THEME'
                                And L.C_CODE = '$v_theme_code'";
        }
        
        return $this->db->getOne($sql);
    }
    //lay du lieu tat ca danh muc truyen hinh
    public function qry_all_listtype()
    {
        $arr_listype = explode(',', _CONST_LISTTYPE_ONLINE_TV);
        $stmt = "select all_lt.* from (";
        foreach ($arr_listype as $value)
        {
            if($stmt == "select all_lt.* from (")
            {
                $stmt .= "select PK_LISTTYPE,C_NAME from t_cores_listtype where C_CODE = '$value'";
            }
            else
            {
                $stmt .= " union select PK_LISTTYPE,C_NAME from t_cores_listtype where C_CODE = '$value'";
            }
        }
        $stmt .= ") all_lt";
        return $this->db->getAll($stmt);
    }
    
    public function qry_all_group_weblink()
    {
        $stmt = "SELECT
                    PK_LIST,C_NAME
                    FROM t_cores_list
                    WHERE C_STATUS = 1
                      AND FK_LISTTYPE = (SELECT
                                           PK_LISTTYPE
                                         FROM t_cores_listtype
                                         WHERE C_STATUS = 1
                                             AND C_CODE = '"._CONST_WEBLINK_GROUP."')";
        return $this->db->GetAll($stmt);
    }
}

?>