<?php
defined('DS') or die('no direct access');

class spotlight_Model extends Model
{

    function __construct()
    {
        parent::__construct();
    }

    function qry_all_position()
    {
        $sql = "Select PK_SPOTLIGHT_POSITION, C_NAME From t_ps_spotlight_position
                Where FK_WEBSITE = " . Session::get('session_website_id');
        return $this->db->getAll($sql);
    }

    function update_position()
    {
        //get data
        $v_pos_id = intval(get_post_var('hdn_item_id'));
        $v_name   = get_post_var('txt_name');
        if (!$v_name)
        {
            die('invalid request data');
        }

        if ($v_pos_id == 0)
        {
            $sql   = "Insert Into t_ps_spotlight_position (C_NAME, FK_WEBSITE) Values(?,?)";
            $param = array($v_name, Session::get('session_website_id'));
        }
        else
        {
            $sql     = "Update t_ps_spotlight_position Set C_NAME = ?
                    Where FK_WEBSITE = ?
                     And PK_SPOTLIGHT_POSITION = ?";
            $param[] = $v_name;
            $param[] = Session::get('session_website_id');
            $param[] = $v_pos_id;
        }

        $this->db->Execute($sql, $param);
    }

    function qry_single_position($id)
    {
        $sql = "Select PK_SPOTLIGHT_POSITION, C_NAME FROM t_ps_spotlight_position 
            Where PK_SPOTLIGHT_POSITION = ?";
        $sql .= ' And FK_WEBSITE =' . Session::get('session_website_id');
        return $this->db->getRow($sql, array($id));
    }

    function qry_all_spotlight($pos_id)
    {
        if(DATABASE_TYPE == 'MSSQL')
        {
            $sql     = "Select S.PK_SPOTLIGHT, S.FK_CATEGORY, S.FK_ARTICLE
                            , A.C_TITLE, A.C_STATUS
                            , dateDiff(mi, A.C_BEGIN_DATE, getDate()) as CK_BEGIN_DATE
                            , dateDiff(mi, getDate(), A.C_END_DATE) as CK_END_DATE
                            , C.C_STATUS As C_CAT_STATUS
                        From t_ps_spotlight S
                        Inner Join t_ps_spotlight_position SP
                        On S.FK_SPOTLIGHT_POSITION = SP.PK_SPOTLIGHT_POSITION
                        Inner Join t_ps_article A
                        On A.PK_ARTICLE = S.FK_ARTICLE
                        Inner Join t_ps_category C
                        ON S.FK_CATEGORY = C.PK_CATEGORY
                        Where FK_SPOTLIGHT_POSITION = ? 
                        And SP.FK_WEBSITE = ?
                        Order by S.C_ORDER";
        }
        elseif(DATABASE_TYPE == 'MYSQL')
        {
            $sql     = "Select S.PK_SPOTLIGHT, S.FK_CATEGORY, S.FK_ARTICLE
                            , A.C_TITLE, A.C_STATUS
                            , DATEDIFF(NOW(),A.C_BEGIN_DATE) As CK_BEGIN_DATE
                            , DATEDIFF(A.C_END_DATE, NOW()) As CK_END_DATE
                            , C.C_STATUS As C_CAT_STATUS
                        From t_ps_spotlight S
                        Inner Join t_ps_spotlight_position SP
                        On S.FK_SPOTLIGHT_POSITION = SP.PK_SPOTLIGHT_POSITION
                        Inner Join t_ps_article A
                        On A.PK_ARTICLE = S.FK_ARTICLE
                        Inner Join t_ps_category C
                        ON S.FK_CATEGORY = C.PK_CATEGORY
                        Where FK_SPOTLIGHT_POSITION = ? 
                        And SP.FK_WEBSITE = ?
                        Order by S.C_ORDER";
        }
        
        $param[] = $pos_id;
        $param[] = Session::get('session_website_id');

        return $this->db->getAll($sql, $param);
    }

    function delete_position()
    {
        $pos_id = intval(get_post_var('position_id'));
        if (!$pos_id)
        {
            die('invalid request data');
        }
        $sql     = 'Delete From t_ps_spotlight_position 
            Where PK_SPOTLIGHT_POSITION = ?
            And FK_WEBSITE = ?';
        $param[] = $pos_id;
        $param[] = Session::get('session_website_id');
        $this->db->Execute($sql, $param);
    }

    function insert_spotlight()
    {
        $arr_article = get_post_var('article', array(), false);
        $v_position_id = intval(get_post_var('position'));
        $sql           = "Select PK_SPOTLIGHT_POSITION From t_ps_spotlight_position 
                Where PK_SPOTLIGHT_POSITION = $v_position_id
                And FK_WEBSITE = " . Session::get('session_website_id');
        $v_position_id = intval($this->db->getOne($sql));
        if (!$arr_article or !$v_position_id)
        {
            die(__('invalid request data'));
        }

        $arr_insert = array();
        foreach ($arr_article as $item)
        {
            $stmt = "SELECT
                        COUNT(PK_SPOTLIGHT)
                      FROM t_ps_spotlight WHERE FK_ARTICLE = ? AND FK_CATEGORY = ? AND FK_SPOTLIGHT_POSITION = ?";
            $arr_param = array(intval($item['article_id']),intval($item['article_category_id']),$v_position_id);
            //kiem tra trung lap tin bai
            if($this->db->getOne($stmt,$arr_param)<1)
            {
                $v_article_id  = intval($item['article_id']);
                $v_category_id = intval($item['article_category_id']);
                $arr_insert[]  = "($v_position_id, $v_category_id, $v_article_id,0)";
            }
        }
        
        $arr_insert    = implode(', ', $arr_insert);
        $sql           = "Insert Into t_ps_spotlight 
                            (FK_SPOTLIGHT_POSITION, FK_CATEGORY, FK_ARTICLE, C_ORDER) 
                          Values $arr_insert";
        $this->db->Execute($sql);
        $other_clause  = " AND FK_SPOTLIGHT_POSITION = $v_position_id";
        $this->build_order('t_ps_spotlight', 'PK_SPOTLIGHT', 'C_ORDER', $other_clause);
    }

    function swap_spotlight_order()
    {
        $item1 = intval(get_post_var('item1'));
        $item2 = intval(get_post_var('item2'));

        if ($item1 && $item2)
        {
            $this->swap_order('t_ps_spotlight', 'PK_SPOTLIGHT', 'C_ORDER', $item1, $item2);
        }
    }

    function delete_spotlight()
    {
        $arr_item = get_post_var('chk_item', array(), false);
        if (!$arr_item)
        {
            die(__('invalid request data'));
        }
        $arr_item  = replace_bad_char(implode(',', $arr_item));
        $v_website = Session::get('session_website_id');
        $sql       = "Delete From t_ps_spotlight 
                Where PK_SPOTLIGHT In(
                SELECT TEMP.PK_SPOTLIGHT FROM (Select S.PK_SPOTLIGHT From t_ps_spotlight S
                Inner Join t_ps_spotlight_position SP
                On S.FK_SPOTLIGHT_POSITION = SP.PK_SPOTLIGHT_POSITION
                Where SP.FK_WEBSITE = $v_website
                And S.PK_SPOTLIGHT In($arr_item)) TEMP
                )";
        $this->db->Execute($sql);
    }

    function qry_frontend_spotlight($pos_id)
    {
        $v_website_id = Session::get('session_website_id');
        $old_mode     = $this->db->fetchMode;
        $this->db->SetFetchMode(ADODB_FETCH_NUM);
        if(DATABASE_TYPE == 'MSSQL')
        {
            $sql          = "Select S.FK_CATEGORY, S.FK_ARTICLE, A.C_SLUG, A.C_FILE_NAME, A.C_SUMMARY, A.C_PEN_NAME
                                , C.C_SLUG as C_CAT_SLUG, C.FK_WEBSITE, A.C_TITLE
                                From t_ps_spotlight S
                                Inner Join t_ps_category_article CA
                                On S.FK_CATEGORY = CA.FK_CATEGORY
                                And S.FK_ARTICLE = CA.FK_ARTICLE
                                Inner Join t_ps_category C
                                On C.PK_CATEGORY = S.FK_CATEGORY
                                Inner Join t_ps_article A
                                On A.PK_ARTICLE = S.FK_ARTICLE
                                Inner Join t_ps_spotlight_position SP
                                On SP.PK_SPOTLIGHT_POSITION = S.FK_SPOTLIGHT_POSITION
                                Where S.FK_SPOTLIGHT_POSITION = $pos_id
                                And SP.FK_WEBSITE = $v_website_id
                                And DateDiff(mi, A.C_BEGIN_DATE, getDate()) >= 0
                                And DateDiff(mi, getDate(), A.C_END_DATE) >= 0
                                And A.C_STATUS = 3
                                And C.C_STATUS = 1
                                Order By S.C_ORDER
                                ";
                            
        }
        else if(DATABASE_TYPE == 'MYSQL')
        {
            $sql          = "Select S.FK_CATEGORY, S.FK_ARTICLE, A.C_SLUG, A.C_FILE_NAME, A.C_SUMMARY, A.C_PEN_NAME
                                , C.C_SLUG as C_CAT_SLUG, C.FK_WEBSITE, A.C_TITLE
                                From t_ps_spotlight S
                                Inner Join t_ps_category_article CA
                                On S.FK_CATEGORY = CA.FK_CATEGORY
                                And S.FK_ARTICLE = CA.FK_ARTICLE
                                Inner Join t_ps_category C
                                On C.PK_CATEGORY = S.FK_CATEGORY
                                Inner Join t_ps_article A
                                On A.PK_ARTICLE = S.FK_ARTICLE
                                Inner Join t_ps_spotlight_position SP
                                On SP.PK_SPOTLIGHT_POSITION = S.FK_SPOTLIGHT_POSITION
                                Where S.FK_SPOTLIGHT_POSITION = $pos_id
                                And SP.FK_WEBSITE = $v_website_id
                                And DATEDIFF(NOW(),A.C_BEGIN_DATE) >= 0
                                And DATEDIFF(A.C_END_DATE, NOW()) >= 0
                                And A.C_STATUS = 3
                                And C.C_STATUS = 1
                                Order By S.C_ORDER
                                ";
        }
        
        $a            = $this->db->getAll($sql);
        $this->db->SetFetchMode($old_mode);
        return $a;
    }

}

?>
