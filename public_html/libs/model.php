<?php if (!defined('SERVER_ROOT')) exit('No direct script access allowed'); ?>
<?php

class Model
{
    /**
     *
     * @var \adoconnection 
     */
    public $db;
    public $goback_url;
    public $goforward_url;

    //public $mysql_db;

    public function __construct()
    {
        switch (DATABASE_TYPE)
        {
            case 'ORACLE':
                //Oracle Setting
                putenv("NLS_LANG=AMERICAN_AMERICA.AL32UTF8");
                $this->db = NewADOConnection(CONST_ORACLE_DSN) or die('Cannot connect to Oracle Database Server!');
                break;

            case 'MYSQL':
                $this->db  = ADONewConnection(CONST_MYSQL_DSN) or die('Cannot connect to MySQL Database Server!');
                //$this->db->Execute('SET NAMES utf8');
                break;

            case 'MSSQL':
            default:
                $this->db        = ADONewConnection('ado_mssql');
                $this->db->Connect(CONST_MSSQL_DSN) or die('Cannot connect to MSSQL Database Server!');
                break;
        }
                
        global $ADODB_CACHE_DIR;
        $ADODB_CACHE_DIR = SERVER_ROOT . 'cache/ADODB_cache/';

        //$this->db->cacheDir = './cache/';
        $this->db->cacheSecs = 3600 * 24;
        $this->db->SetFetchMode(ADODB_FETCH_ASSOC);
        $this->db->debug     = DEBUG_MODE;
        
    }
    
    public function is_mssql()
    {
    	if(DATABASE_TYPE == 'MSSQL')
    	{
    		return TRUE;
    	}
    	
    	return FALSE;
    }
    public function is_mysql()
    {
    	if(DATABASE_TYPE == 'MYSQL')
    	{
    		return TRUE;
    	}
    	
    	return FALSE;
    }
    public function is_oracle()
    {
    	if(DATABASE_TYPE == 'ORACLE')
    	{
    		return TRUE;
    	}
    	
    	return FALSE;
    }
    
    public function get_last_inserted_id($table_name, $id_column_name = '')
    {
        if (DATABASE_TYPE == 'MSSQL')
        {
            return $this->db->getOne("SELECT IDENT_CURRENT('$table_name')");
        }
        elseif (DATABASE_TYPE == 'MYSQL')
        {
            return $this->db->Insert_ID($table_name, $id_column_name);
        }

        return NULL;
    }

    public function qry_all_lang()
    {
        if (DEBUG_MODE < 10)
        {
            $this->db->debug = 0;
        }
        
        $stmt = "Select PK_LIST,C_NAME 
	                From t_cores_list 
	                Where FK_LISTTYPE = (Select PK_LISTTYPE From t_cores_listtype Where C_CODE='DM_NGON_NGU')";
        $ret = $this->db->getAssoc($stmt);
        
        $this->db->debug = DEBUG_MODE;
        return $ret;
    }
    
    //Quay ve man hinh truoc sau khi thuc hien thao tac voi CSDL
    public static function exec_done($url, $filter_array = array())
    {
        $html = '<html><head></head><body>';
        $html .= '<form name="frmMain" action="' . $url . '" method="POST">';
    
        foreach ($filter_array as $key => $val)
        {
            $html .= View::hidden($key, $val);
        }
    
        $html .= '</form>';
        $html .= '<script type="text/javascript">document.frmMain.submit();</script>';
        $html .= '</body></html>';
    
        echo $html;
        exit;
    }
    public static function popup_exec_fail($message = 'Cáº­p nháº­t dá»¯ liá»‡u tháº¥t báº¡i!')
    {
        echo '<script type="text/javascript">';
        echo 'alert("' . replace_bad_char($message) . '");';
        echo 'window.parent.hidePopWin();';
        echo '</script>';
        exit;
    }
    public static function popup_exec_done($retVal = NULL)
    {
        echo '<script type="text/javascript">';
        if ($retVal != NULL && $retVal != FALSE)
        {
            echo "var returnVal = $retVal;";
            echo 'window.parent.hidePopWin(true);';
        }
        else
        {
        echo 'window.parent.hidePopWin();';
            echo 'window.parent.document.frmMain.submit();';
        }
    
        echo '</script>';
        exit;
    }
    
    //Quay ve man hinh truoc sau khi thuc hien thao tac voi CSDL
    public static function exec_fail($url, $message, $filter_array = array())
    {
    $html = '<html><head></head><body>';
    $html .= '<form name="frmMain" action="' . $url . '" method="POST">';
    
    foreach ($filter_array as $key => $val)
    {
    $html .= hidden($key, $val);
    }
    
    $html .= '</form>';
    $html .= '<script type="text/javascript">alert("' . $message . '");document.frmMain.submit();</script>';
            $html .= '</body></html>';
    
        echo $html;
                    exit;
    }
    
    //Common Database Exec
                            public function get_max($table_name, $field_name, $other_clause = '')
    {
        $sql = "SELECT MAX($field_name) as a FROM $table_name WHERE 1>0";
        if ($other_clause !== '')
        {
            $sql .= ' AND ' . $other_clause;
        }
    
        return $this->db->getOne($sql);
        }
    
        //end func GetMaxValue
    
        /**
                * Thuc hien sap xep lai thu tu hen thi
                *
                * @param string $table_name	Ten bang
        * @param string $pk_field		Ten cot dong vai tro PK
        * @param string $order_field	Ten Cot Order
        * @param string $pk_value		Gia tri cua PK
        * @param int $assign_order		Gia tri Order moi
        * @param int $current_order	Gia tri Order hien tai
        * @param string $other_clause	Dieu kien khac
        * @author  Ngo Duc Lien <liennd@gmail.com>
        */
    function ReOrder($table_name, $pk_field, $order_field, $pk_value, $assign_order, $current_order = -1, $other_clause = '')
    {
        if (empty($other_clause))
            $other_clause = '';

        //if (intval($assign_order) == intval($current_order)) return;
        if (intval($current_order) > 0)
        {
            if ($assign_order > $current_order)
                {
                    $str_sql = "update $table_name "
                    . "\n set $order_field = $order_field - 1"
                    . "\n where $order_field > $current_order and $order_field <= $assign_order and $pk_field <> $pk_value";
                }
                else
                {
                    $str_sql = "update $table_name "
                    . "\n set $order_field = $order_field + 1"
                    . "\n where $order_field >= $assign_order and $order_field < $current_order and $pk_field <> $pk_value";
                }
        }
        else
        {
            $str_sql = " update $table_name "
            . "\n set $order_field = $order_field + 1"
            . "\n where $order_field >= $assign_order and $pk_field <> $pk_value";
        }
        
        if (strlen($other_clause) > 0)
        {
            $str_sql.="\n and $other_clause";
        }

        $this->db->Execute($str_sql);
        if ($this->db->ErrorNo() != 0)
        {
            return $this->db->ErrorMsg();
        }

        //Gan dung vi tri hien thi
        $this->db->Execute("update $table_name "
            . "\n set $order_field = $assign_order"
            . "\n where $pk_field = $pk_value");

        /* THU TU HIEN THI KHONG NHAT THIET PHAI LIEN MACH */
        //Abs Order
        $str_query = "select $pk_field from $table_name ";
        if (strlen($other_clause) > 0)
        {
            $str_query.="\n where $other_clause";
        }
        
        $str_query.="\n order by $order_field";

        $this->db->SetFetchMode(ADODB_FETCH_NUM);
        $arr_ID   = $this->db->GetAll($str_query);
        $count_ID = count($arr_ID);
        if ($count_ID > 0)
        {
            for ($i = 0; $i < $count_ID; $i++)
            {
                $ID      = $arr_ID[$i][0];
                $j       = $i + 1;
                $str_sql = "Update $table_name"
                . "\n set $order_field=$j"
                . "\n where $pk_field=$ID";
                $this->db->Execute($str_sql);
                if ($this->db->ErrorNo() != 0)
                {
                    return $this->db->ErrorMsg();
                }
            }
        }
    }//end Func ReOrder()
    
    public function swap_order($p_table_name, $p_pk_column_name, $p_order_columm_name, $p_pk_column_value1, $p_pk_column_value2, $p_other_clause = '')
    {
        if (strlen($p_other_clause) > 0)
        {
            $v_other_clause = ' AND (' . $p_other_clause . ')';
        }
        else
        {
            $v_other_clause = '';
        }

        $this->db->SetFetchMode(ADODB_FETCH_NUM);
        //Lay order hien tai cua doi tuong 1
        $str_sql  = "Select $p_order_columm_name as C_ORDER From $p_table_name Where $p_pk_column_name=$p_pk_column_value1";
        $v_order1 = $this->db->GetOne($str_sql);

        //Lay Order cua doi tuong 2
        $str_sql  = "Select $p_order_columm_name as C_ORDER From $p_table_name Where $p_pk_column_name=$p_pk_column_value2";
        $v_order2 = $this->db->GetOne($str_sql);

        $str_sql = "Update $p_table_name Set $p_order_columm_name=$v_order2 Where $p_pk_column_name=$p_pk_column_value1";
        $this->db->Execute($str_sql);

        $str_sql = "Update $p_table_name Set $p_order_columm_name=$v_order1 Where $p_pk_column_name=$p_pk_column_value2";
        $this->db->Execute($str_sql);

        $ret_array = array(
        $p_pk_column_value1 => $v_order2,
        $p_pk_column_value2 => $v_order1,
        );
        return $ret_array;
    }
    //end func swap_order()
    
    /**
    * Luu du lieu nhi phan vao CSDL trong cot co kieu BLOB
    *
    * @param string $table_name			Ten bang
    * @param string $pk_column_name		Ten cot PK
    * @param string $file_name_column		Ten cot chua ten file
    * @param string $file_content_column	Ten cot chua noi dung file
    * @param string $pk_value				Gia tri PK
    * @param string $full_path_to_file		Duong dan day du toi file
    * @return Gia tri ID vua duoc cap nhat neu thanh cong, nguoc lai false
    * @see UpdateBlobFile
    */
    public function save_file_to_db($table_name, $file_name_column, $file_content_column, $full_path_to_file, $where = '')
    {

        if (!is_file($full_path_to_file))
        return false;

        //Lay ten file
        $arr_path_info = pathinfo($full_path_to_file);
        $str_file_name = $this->db->qstr($arr_path_info['basename']);

        //Luu ten file
        $sql = "Update $table_name set $file_name_column = $str_file_name";
        $sql .= ($where == '') ? '' : " where $where";
        $this->db->Execute($sql);

        //Luu noi dung file
        $this->db->UpdateBlobFile($table_name, $file_content_column, $full_path_to_file, $where);
    }//end func save_file_to_db
    
                                        
    
    public function create_file_from_db($table_name, $file_content_column, $full_path_to_file, $where = '', $over_write = false)
    {
        $str_sql = "Select $file_content_column as FILE_CONTENT from $table_name";
        $str_sql .= (strlen($where) > 0) ? "\n Where $where" : '';

        if (($over_write === true) or (!file_exists($full_path_to_file)))
        {
            $this->db->setFetchMode(ADODB_FETCH_NUM);
            $file_content = $this->db->getOne($str_sql);
            $handle       = @fopen($full_path_to_file, "wb");
            @fwrite($handle, $file_content);
            @fclose($handle);
            $this->db->setFetchMode(ADODB_FETCH_BOTH);
        }
        return false;
    }
    
//end func create_file_from_db
    
    public function list_get_all_by_listtype_code($listtype_code, $arr_xml_tag = null)
    {
        if ($this->is_oracle() OR $this->is_mysql())
        {
            $sql = 'SELECT t.C_CODE, t.C_NAME ';
            for ($i = 0; $i < sizeof($arr_xml_tag); $i++)
            {
                $id = '"' . $arr_xml_tag[$i] . '"';
                $sql .= ", EXTRACTVALUE(t.C_XML_DATA, '/data/item[@id=$id]/value') $id";
            }
            $sql .= ' FROM t_cores_list t ';
            $sql .= " WHERE (t.FK_LISTTYPE=(Select PK_LISTTYPE FROM t_cores_listtype WHERE C_CODE='$listtype_code')) ";
            $sql .= ' AND (t.C_STATUS > 0)';
        }
        elseif (DATABASE_TYPE == 'MSSSQL')
        {
            $sql = 'SELECT L.C_CODE, L.C_NAME ';
            for ($i = 0; $i < sizeof($arr_xml_tag); $i++)
            {
                $id = $arr_xml_tag[$i];
                $sql .= ",isnull(item.value('(item[@id=''$id'']/value/text())[1]','Nvarchar(Max)'),'') as $id";
            }
            $sql .= " From T_LIST as L CROSS APPLY C_XML_DATA.nodes('/data') t(item)";
            $sql .= " WHERE (L.fk_listtype=(Select PK_LISTTYPE FROM t_listtype WHERE c_code='$listtype_code')) ";
            $sql .= ' AND (L.c_status > 0)';
        }
        else
        {
            return array();
        }
        return $this->db->getAll($sql);
    }
    
//end func list_get_all_by_listtype_code
    
    public function assoc_list_get_all_by_listtype_code($listtype_code)
    {
        $stmt   = "Select L.C_CODE, L.C_NAME From t_cores_list As L Where L.C_STATUS > 0 And L.FK_LISTTYPE=(Select PK_LISTTYPE From t_cores_listtype Where C_CODE=?)";
        $params = array($listtype_code);
        return $this->db->getAssoc($stmt, $params);
    }
    
    
    public function get_new_seq_val($table_seq_name)
    {
        $table_seq_name = $this->replace_bad_char($table_seq_name);
    
        $sql = "Insert Into $table_seq_name(C_DATE_CREATED) Values(getDate())";
        $this->db->Execute($sql);
        return $this->db->getOne("SELECT IDENT_CURRENT('$table_seq_name')");
    }
    
    public function getDate()
    {
    	if ($this->is_mssql())
    	{
        	return $this->db->getOne("Select convert(varchar,getDate(),120) as d");
                                                        }
                                                        elseif ($this->is_mysql())
                                                        {
                                                        return $this->db->getOne("Select DATE_FORMAT(Now(),'%Y-%m-%d %H:%i:%s') as d");
                                                        }
                                                         
                                                        return NULL;
    }
    
    /**
     *
     * @param Int $nDay
     * @return date string in yyymmdd format
     */
    public function date_which_diff_day($nDay)
    {
                                                            $stmt = 'Select Convert(varchar(10),a.C_DATE,103) C_DATE
                                                            From (
                                                            Select C_DATE
                                                            ,ROW_NUMBER() OVER (ORDER BY C_DATE Asc) as RN
                        From t_cores_calendar
                        Where C_OFF=0 And DATEDIFF(day, GETDATE(), C_DATE) >= 0
                            ) a
                            Where a.RN = ?';
                            // $this->db->debug=0;
                            return $this->db->getOne($stmt, array($nDay + 1));
                                        }
    
                                        public function date_which_diff_day_yyyymmdd($nDay)
                                        {
                                        $stmt = "Select Replace(Convert(varchar(10),a.C_DATE,111), '/','-')  C_DATE
                                        From (
                                        Select
                                        C_DATE
                                        , ROW_NUMBER() OVER (Order by C_DATE Asc) as RN
                                        From t_cores_calendar
                                         Where C_OFF=0
                                        And DATEDIFF(day, GETDATE(), C_DATE) >= 0
                                                            ) a
                                        Where a.RN = ?";
                                        return $this->db->getOne($stmt, array($nDay + 1));
                                        }
    
                                        public function next_working_day($count = 1, $from_date_yyyymmddhhmmss = NULL)
                                        {
                                        if ($from_date_yyyymmddhhmmss == NULL)
                                        {
                                                $stmt = "Select Replace(Convert(varchar(10),a.C_DATE,111), '/','-')  C_DATE
                                                From (
                                                        Select
                                                            C_DATE
                                                            , ROW_NUMBER() OVER (Order by C_DATE Asc) as RN
                                                            From t_cores_calendar
                                                            Where C_OFF=0
                                                            And DATEDIFF(day, GETDATE(), C_DATE) >= 0
                                                ) a
                                                Where a.RN = ?";
                                                return $this->db->getOne($stmt, array($count + 1));
                                        }
                                        else
                                        {
                                        $stmt = "Select Replace(Convert(varchar(10),a.C_DATE,111), '/','-')  C_DATE
                                                From (
                                                Select
                                                C_DATE
                                                , ROW_NUMBER() OVER (Order by C_DATE Asc) as RN
                                                From t_cores_calendar
                                                Where C_OFF=0
                                                And DATEDIFF(day, ?, C_DATE) >= 0
                                                ) a
                                                Where a.RN = ?";
                                                return $this->db->getOne($stmt, array($from_date_yyyymmddhhmmss, $count + 1));
                                        }
                                        }
    
    /**
    *  Đếm số ngày làm việc giữa 2 mốc ngày
    * @param $from_date_in_yyyymmdd Ngày bắt đầu, theo định dạng yyyy-mm-dd
    * @param $to_date_in_yyyymmdd Ngày kết thúc, theo định dạng yyyy-mm-dd
    *
    * @return Int Số ngày làm việc giữa hai ngày.
    */
    public function days_between_two_date($from_date_in_yyyymmdd, $to_date_in_yyyymmdd)
    {
            $stmt = 'Select Count(*)
            From t_cores_calendar
            Where C_OFF=0
            And datediff(day, ?, C_DATE)>=0
            And Datediff(day, ?, C_DATE)<0';

            $params = array($from_date_in_yyyymmdd, $to_date_in_yyyymmdd);

                    $this->db->debug = 0;
                    return $this->db->getOne($stmt, $params);
    }
    
    /**
    * Kiem tra hom nay co phai ngay lam viec khong?
    */
    public function check_today_working_day()
    {
        $stmt = 'Select Count(*)
        From t_cores_calendar
        Where C_OFF=0
        And Datediff(day, getDate(), C_DATE)=0';
        return $this->db->getOne($stmt);
    }
    
    public function build_internal_order($table_name, $pk_column_name, $parent_column_name, $order_column_name,  $internal_order_column_name,$pk_value=-1,$other_clause='')
    {
        $this->db->SetFetchMode(ADODB_FETCH_BOTH);

        //Stack
        $arr_stack = array();

        $id = $pk_value;
        //Kiem tra ID co ton tai khong
        $sql = "Select Count(*) From $table_name Where $pk_column_name=$id";
        if ($this->db->getOne($sql) < 1)
        {
            $sql = "Select $pk_column_name From $table_name Where ($parent_column_name Is Null Or $parent_column_name < 1) $other_clause";
            $id = $this->db->getOne($sql);
        }

        /*/Cập nhật Internal Order của node
        $v_order = $this->db->getOne("Select $order_column_name From $table_name Where $pk_column_name=$id");
        $v_order = str_repeat('0', 3 - strlen($v_order)) . $v_order;

        $v_parent_internal_order = $this->db->getOne("Select $internal_order_column_name From $table_name Where $pk_column_name=(Select $parent_column_name From $table_name Where $pk_column_name=$id)");
                $v_new_internal_order = $v_parent_internal_order . $v_order;
$sql = "Update $table_name Set $internal_order_column_name='$v_new_internal_order' Where $pk_column_name=$id";
$this->db->Execute($sql);
*/
        // Cập nhật Internal Order tất cả các node ngang hàng
        $v_parent_id = $this->db->getOne("Select $parent_column_name From $table_name Where $pk_column_name=$id $other_clause");
        if($v_parent_id == '' || $v_parent_id == Null)
        {
            $v_condition_parent = "$parent_column_name IS NULL";
        }
        else
        {
            $v_condition_parent = "$parent_column_name = $v_parent_id";
        }
        $v_parent_internal_order = $this->db->getOne("Select $internal_order_column_name From $table_name Where $pk_column_name=(Select $parent_column_name From $table_name Where $pk_column_name=$id $other_clause)");

        $sql = "Update $table_name
        Set $internal_order_column_name = Concat ('$v_parent_internal_order', Case
                                        When $order_column_name < 10 Then Concat('00', $order_column_name)
                                        When $order_column_name >= 10 And $order_column_name < 100 Then Concat('0', $order_column_name)
                                        Else $order_column_name
                                    End
                )
            WHERE  $v_condition_parent $other_clause";

        $this->db->Execute($sql);

        //Cập nhật Internal Order của tất cả các node con
        $sql = "Select
                $pk_column_name
                ,$internal_order_column_name
                ,$order_column_name
                From $table_name Where $v_condition_parent $other_clause
                Order by $order_column_name";
        $arr_stack = $this->db->getAll($sql);
        $i=1;
        while (sizeof($arr_stack) > 0 && $i < 10000)
        {
            //Pop stack
            $arr_single_row = array_pop($arr_stack);

            $v_ou_id             = $arr_single_row[$pk_column_name];
            $v_internal_order    = $arr_single_row[$internal_order_column_name];
            $v_order             = $arr_single_row[$order_column_name];

            //Update all children internal order
            if (DATABASE_TYPE == 'MSSQL')
            {
            $sql = "Update $table_name
            Set $internal_order_column_name = '$v_internal_order' + Case
                                            When $order_column_name < 10 Then '00' + Convert(varchar(1),$order_column_name)
                                            When $order_column_name >= 10 And $order_column_name < 100 Then '0' + Convert(varchar(2),$order_column_name)
                                            Else Convert(varchar(3),$order_column_name)
                                        End

            WHERE $parent_column_name=$v_ou_id $other_clause";
            }
            elseif (DATABASE_TYPE == 'MYSQL')
            {
                $sql = "Update $table_name
                Set $internal_order_column_name = Concat ('$v_internal_order', Case
                                                When $order_column_name < 10 Then Concat('00', $order_column_name)
                                                When $order_column_name >= 10 And $order_column_name < 100 Then Concat('0', $order_column_name)
                                                Else $order_column_name
                                            End
                        )
                WHERE $parent_column_name=$v_ou_id $other_clause";
            }
            $this->db->Execute($sql);

            //Push stack
            $stmt = "Select
                $pk_column_name
                ,$internal_order_column_name
                ,$order_column_name
                From $table_name Where $parent_column_name=$v_ou_id $other_clause
                Order by $order_column_name";
            $arr_all_sub_ou = $this->db->getAll($stmt);
            foreach ($arr_all_sub_ou as $ou)
            {
                array_push($arr_stack, $ou);
            }
                $i++;
        }//end while
    }
    
    //function order
    public function build_order($table, $pk_col, $order_col, $orther_clause = '')
    {

        $order = "$order_col";

        if(DATABASE_TYPE == 'MSSQL')
        {
            $sql = "  Update $table
                  Set $order_col = B.rn
                  From $table
                  Inner Join (
                                Select $pk_col,ROW_NUMBER() Over(Order By $order) As rn
                                From $table
                                WHERE (1>0) $orther_clause
                              ) B
                  On $table.$pk_col = B.$pk_col
                    ";
            $this->db->Execute($sql);
        }
        else if(DATABASE_TYPE == 'MYSQL')
        {
            $sql = "SELECT $pk_col FROM $table WHERE (1>0) $orther_clause Order By $order";
            $arr_all_record = $this->db->getCol($sql);
            for($i=0;$i<count($arr_all_record);$i++)
            {
                $v_id = $arr_all_record[$i];
                $v_value = $i + 1;
                $sql = "UPDATE $table SET $order_col = $v_value
                        WHERE (1>0) AND $pk_col = $v_id $orther_clause";
                $this->db->Execute($sql);
            }
        }

    }
        
        function prepare_tinyMCE($val)
        {
            $val = strval($val);
            //$val = str_replace("&", '&amp;', $val);
            // remove all non-printable characters. CR(0a) and LF(0b) and TAB(9) are allowed
            // this prevents some character re-spacing such as <java\0script>
            // note that you have to handle splits with \n, \r, and \t later since they *are* allowed in some inputs
            $val = preg_replace('/([\x00-\x08][\x0b-\x0c][\x0e-\x20])/', '', $val);
    
            //$val = stripslashes($val);
            //$val = strip_tags($val); # Remove tags HTML e PHP.
            //$val = addslashes($val); # Adiciona barras invertidas à uma string.
            //$val = preg_replace('/([\x00-\x08,\x0b-\x0c,\x0e-\x19])/', '', $val);
            // straight replacements, the user should never need these since they're normal characters
            // this prevents like <IMG SRC=&#X40&#X61&#X76&#X61&#X73&#X63&#X72&#X69&#X70&#X74&#X3A&#X61
            // &#X6C&#X65&#X72&#X74&#X28&#X27&#X58&#X53&#X53&#X27&#X29>
            $search = 'abcdefghijklmnopqrstuvwxyz';
            $search.= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $search.= '1234567890!@#$%^&*()';
            $search.= '~`";:?+/={}[]-_|\'\\';
    
            for ($i = 0; $i < strlen($search); $i++)
            {
                // ;? matches the ;, which is optional
                // 0{0,7} matches any padded zeros, which are optional and go up to 8 chars
                // &#x0040 @ search for the hex values
                $val = preg_replace('/(&#[x|X]0{0,8}' . dechex(ord($search[$i])) . ';?)/i', $search[$i], $val); // with a ;
                // &#00064 @ 0{0,7} matches '0' zero to seven times
                $val = preg_replace('/(&#0{0,8}' . ord($search[$i]) . ';?)/', $search[$i], $val); // with a ;
            }

            // now the only remaining whitespace attacks are \t, \n, and \r
            $ra1 = array(
                'javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink'
                , 'link', 'script', 'embed', 'object', 'iframe', 'frame', 'frameset'
                , 'ilayer', 'layer', 'bgsound', 'title'
            );
            $ra2 = array(
                'onabort', 'onactivate', 'onafterprint', 'onafterupdate'
                , 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate'
                , 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload'
                , 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange'
                , 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut'
                , 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick'
                , 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave'
                , 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate'
                , 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout'
                , 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete'
                , 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave'
                , 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel'
                , 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange'
                , 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart'
                , 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll'
                , 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop'
                , 'onsubmit', 'onunload'
            );
            $ra = array_merge($ra1, $ra2);

            $found = true; // keep replacing as long as the previous round replaced something
            while ($found == true)
            {
                $val_before = $val;
                for ($i = 0; $i < sizeof($ra); $i++)
                {
                    $pattern = '/';
                    for ($j = 0; $j < strlen($ra[$i]); $j++)
                    {
                        if ($j > 0)
                        {
                            $pattern .= '(';
                            $pattern .= '(&#[x|X]0{0,8}([9][a][b]);?)?';
                            $pattern .= '|(&#0{0,8}([9][10][13]);?)?';
                            $pattern .= ')?';
                        }
                        $pattern .= $ra[$i][$j];
                    }
                    $pattern .= '/i';
                    $replacement = substr($ra[$i], 0, 2) . '<x>' . substr($ra[$i], 2); // add in <> to nerf the tag
                    $val         = preg_replace($pattern, $replacement, $val); // filter out the hex tags
                    if ($val_before == $val)
                    {
                        // no replacements were made, so exit the loop
                        $found = false;
                    }
                }
            }
        $val = str_replace('\'', '&#39;', $val);
        return $val;
    }
    
    public function build_interal_order($table_name='t_cores_ou', $pk_column_name='PK_OU', $parent_column_name='FK_OU', $pk_value=-1, $order_column_name='C_ORDER', $internal_order_column_name='C_INTERNAL_ORDER')
    {
    	$this->db->SetFetchMode(ADODB_FETCH_BOTH);
    
    	//Stack
    	$arr_stack = array();
    
    	$id = $pk_value;
    	//Kiem tra ID co ton tai khong
    	$sql = "Select Count(*) From $table_name Where $pk_column_name=$id";
    	if ($this->db->getOne($sql) < 1)
    	{
    		$sql = "Select $pk_column_name From $table_name Where ($parent_column_name Is Null Or $parent_column_name < 1)";
    		$id = $this->db->getOne($sql);
    	}
    
    	//Cáº­p nháº­t Internal Order cá»§a node
    	$v_order = $this->db->getOne("Select $order_column_name From $table_name Where $pk_column_name=$id");
    	$v_order = str_repeat('0', 3 - strlen($v_order)) . $v_order;
    
    	$v_parent_internal_order = $this->db->getOne("Select $internal_order_column_name From $table_name Where $pk_column_name=(Select $parent_column_name From $table_name Where $pk_column_name=$id)");
    	$v_new_internal_order = $v_parent_internal_order . $v_order;
    	$sql = "Update $table_name Set $internal_order_column_name='$v_new_internal_order' Where $pk_column_name=$id";
    	$this->db->Execute($sql);
    
    	//Cáº­p nháº­t Internal Order cá»§a táº¥t cáº£ cÃ¡c node con
    	$sql = "Select
			    	$pk_column_name
			    	,$internal_order_column_name
			    	,$order_column_name
		    	From $table_name 
		    	Where $parent_column_name=(Select $parent_column_name From $table_name Where $pk_column_name=$id)
		    	Order by $order_column_name";
    	$arr_stack = $this->db->getAll($sql);
    	$i=1;
    	while (sizeof($arr_stack) > 0 && $i < 10000)
    	{
    		//Pop stack
    		$arr_single_row = array_pop($arr_stack);
    
    		$v_ou_id             = $arr_single_row[$pk_column_name];
    		$v_internal_order    = $arr_single_row[$internal_order_column_name];
    		$v_order             = $arr_single_row[$order_column_name];
    
    		//Update all children internal order
    		if (DATABASE_TYPE == 'MSSQL')
    		{
    			$sql = "Update $table_name
    			Set $internal_order_column_name = '$v_internal_order' + Case
															    			When $order_column_name < 10 Then '00' + Convert(varchar(1),$order_column_name)
															    			When $order_column_name >= 10 And $order_column_name < 100 Then '0' + Convert(varchar(2),$order_column_name)
															    			Else Convert(varchar(3),$order_column_name)
														    			End
    			WHERE $parent_column_name=$v_ou_id";
    		}
    		elseif (DATABASE_TYPE == 'MYSQL')
    		{
    			$sql = "Update $table_name
    			Set $internal_order_column_name = Concat ('$v_internal_order', Case
																	    			When $order_column_name < 10 Then Concat('00', $order_column_name)
																	    			When $order_column_name >= 10 And $order_column_name < 100 Then Concat('0', $order_column_name)
																	    			Else $order_column_name
																    			End
    													)
    			WHERE $parent_column_name=$v_ou_id";
    		}
    		$this->db->Execute($sql);
    
    		//Push stack
    		$stmt = "Select
			    		$pk_column_name
			    		,$internal_order_column_name
			    		,$order_column_name
		    		From $table_name Where $parent_column_name=$v_ou_id
		    		Order by $order_column_name";
    		$arr_all_sub_ou = $this->db->getAll($stmt);
    		foreach ($arr_all_sub_ou as $ou)
    		{
    			array_push($arr_stack, $ou);
    		}
    		$i++;
    	}//end while
    }//end func

    
///////////////////////////////////////////////////////////////////////////////////////////////////////
//
//phần dành cho báo điện tử Go-PAPER
//
///////////////////////////////////////////////////////////////////////////////////////////////////////
    
    public function gp_qry_all_website_by_user($v_lang_id)
    {
        if (DEBUG_MODE < 10)
        {
            $this->db->debug = 0;
        }
    
        @session::init();
        $v_user_id       = session::get('user_id');
        $v_is_admin      = session::get('is_admin');
        $sql             = " Where FK_GROUP IN (SELECT FK_GROUP FROM t_cores_user_group  WHERE FK_USER=$v_user_id) ";
        $stmt            = "Select 
                                w.PK_WEBSITE
                                ,w.C_NAME
                                ,w.FK_LANG
                            From t_cores_user_function uf
                                right join t_ps_website w
                                ON uf.FK_WEBSITE=w.PK_WEBSITE
                            Where uf.FK_USER = $v_user_id
                            union
                            Select 
                                w.PK_WEBSITE,
                                w.C_NAME,
                                w.FK_LANG
                            From t_cores_group_function gf
                                right join t_ps_website w
                                ON gf.FK_WEBSITE=w.PK_WEBSITE
                            ";
        if ($v_is_admin < 1)
        {
            $stmt            = $stmt . $sql;
        }
    
        $stmt            = "Select a.PK_WEBSITE,a.C_NAME from ($stmt) a WHERE a.FK_LANG = $v_lang_id";
        $arr_website     = $this->db->getAssoc($stmt);
        
        $this->db->debug = DEBUG_MODE;
        return $arr_website;
    }
    
    /**
     * Dem so tin bai theo tung trang thai
     * @return array
     */
    public function gp_qry_count_article()
    {
        @session::init();
        $v_website_id     = session::get('session_website_id');
        $detect = new Mobile_Detect();
        if ($detect->isMobile() && Cookie::get('pc_mode', 0) == 0)
        {
            unset($detect);
            return array();
        }
        else
        {
            $sql = "Select
                  count_approval
                  , count_editor
                From (Select
                        count(PK_ARTICLE)        as 'count_approval'
                      From t_ps_article A
                      Where C_DEFAULT_WEBSITE = $v_website_id
                          And C_STATUS = 2) a
                  , (Select
                       count(PK_ARTICLE)        as 'count_editor'
                     From t_ps_article
                     Where C_DEFAULT_WEBSITE = $v_website_id
                         And C_STATUS = 1) e";
        }
        return $this->db->getrow($sql);
    }

    /*
     * Lấy danh sách chuyên mục nổi bật (hiển thi trên trang chủ)
     * Và danh sách tin bài thuộc mỗi chuyên mục đó
     */
    function gp_qry_all_featured_category($website_id = 0)
    {   
        $website_id           = replace_bad_char($website_id);
        $article_per_category = get_system_config_value('homepage_article_per_category');
        //Lay danh sach chuyen muc noi bat (featured_category)
        $sql = "Select
                  c.PK_CATEGORY
                  , c.C_NAME
                  , c.C_SLUG
                  , c.FK_WEBSITE
                  , c.C_IS_VIDEO
                 
                FROM t_ps_homepage_category hc
                  Inner Join t_ps_category c
                    On hc.FK_CATEGORY = c.PK_CATEGORY
                Where c.C_STATUS = 1
                    And hc.FK_WEBSITE = $website_id
                Order by hc.C_ORDER";

        $arr_categories = $this->db->getAll($sql);
        $n              = count($arr_categories);
        
        for ($i = 0; $i < $n; $i++)
        {
            $category                 = &$arr_categories[$i];
            $category_id              = $category['PK_CATEGORY'];
            //Lay sticky cua chuyen muc
            $category['arr_articles'] = $this->gp_qry_all_sticky_article($website_id, $category_id, false, $article_per_category);

            //Neu sticky chua du thi lay them tin bai Binh thuong
            $remaining = $article_per_category - count($category['arr_articles']);
            if ($remaining)
            {
                if(DATABASE_TYPE == 'MSSQL')
                {
                    $arr_sticky_id = implode(',', array_keys($category['arr_articles']));
                    $sql_inner     = "
                        Select Top $remaining
                            A.PK_ARTICLE, CA.FK_CATEGORY
                        From t_ps_article A
                        Left Join t_ps_category_article CA
                            On CA.FK_ARTICLE = A.PK_ARTICLE
                        Left Join t_ps_category C
                            On C.PK_CATEGORY = CA.FK_CATEGORY
                        Where C.PK_CATEGORY = $category_id
                            And A.C_STATUS = 3
                            And C.C_STATUS = 1
                            And A.C_BEGIN_DATE <= GETDATE()
                            And A.C_END_DATE >= GetDate()
                    ";
                    if (!empty($arr_sticky_id))
                    {
                        $sql_inner .= "And A.PK_ARTICLE Not In($arr_sticky_id)";
                    }
                    $sql_inner .= " Order by A.C_BEGIN_DATE Desc";
                    $sql       = "
                        Select 
                            A.PK_ARTICLE as ID, A.PK_ARTICLE, A.C_TITLE, A.C_SLUG
                            , A.C_SUMMARY, A.C_BEGIN_DATE, A.C_HAS_VIDEO
                            , A.C_HAS_PHOTO, $website_id As FK_WEBSITE
                            , A.C_FILE_NAME, FK_CATEGORY
                            ,(Select C_SLUG From t_ps_category Where PK_CATEGORY = I.FK_CATEGORY) As C_SLUG_CAT
                            , A.C_SUB_TITLE
                        From t_ps_article A
                        Inner Join ($sql_inner) I
                            On I.PK_ARTICLE = A.PK_ARTICLE
                    ";
                    $arr_other = $this->db->GetAssoc($sql);
                }
                else if(DATABASE_TYPE == 'MYSQL')
                {
                    //su dung force index cho nhung chuyen muc co ban ghi nhieu
                    $sql = "Select count(*) From t_ps_category_article Where FK_CATEGORY = $category_id";
                    $v_check = $this->db->getOne($sql);
                    $v_force_index = '';
                    if($v_check >= 500)
                    {
                        $v_force_index = 'FORCE INDEX(C_BEGIN_DATE)';
                    }
                    
                    $arr_sticky_id = implode(',', array_keys($category['arr_articles']));
                    $sql_inner = "SELECT a.PK_ARTICLE,
                                        cca.FK_CATEGORY
                                 FROM t_ps_article a
                                 $v_force_index
                                 RIGHT JOIN
                                   (SELECT DISTINCT FK_CATEGORY,
                                                    FK_ARTICLE
                                    FROM t_ps_category_article ca
                                    LEFT JOIN t_ps_category c ON ca.FK_CATEGORY = c.PK_CATEGORY
                                    WHERE c.PK_CATEGORY = $category_id
                                      AND c.C_STATUS = 1) cca ON a.PK_ARTICLE = cca.FK_ARTICLE
                                 WHERE a.C_STATUS = 3
                                   AND a.C_BEGIN_DATE <= NOW()
                                   AND a.C_END_DATE >= NOW()
                                 ";
                    
                    if (!empty($arr_sticky_id))
                    {
                        $sql_inner .= "And a.PK_ARTICLE Not In($arr_sticky_id)";
                    }
                    
                    $sql_inner .= " Order by a.C_BEGIN_DATE Desc LIMIT $remaining";
                    $sql       = "
                        Select
                              A.PK_ARTICLE     as ID
                              , A.PK_ARTICLE
                              , A.C_TITLE
                              , A.C_SLUG
                              , A.C_SUMMARY
                              , A.C_BEGIN_DATE
                              , A.C_HAS_VIDEO
                              , A.C_HAS_PHOTO
                              , $website_id    As FK_WEBSITE
                              , A.C_FILE_NAME
                              , FK_CATEGORY
                              ,(Select C_SLUG From t_ps_category Where PK_CATEGORY = I.FK_CATEGORY) As C_SLUG_CAT
                              , A.C_SUB_TITLE
                        From t_ps_article A
                        Inner Join ($sql_inner) I
                            On I.PK_ARTICLE = A.PK_ARTICLE
                    ";
                    $arr_other = $this->db->GetAssoc($sql);
                }
                
                
                if (!empty($arr_other))
                {
                    $category['arr_articles'] = array_merge($category['arr_articles'], $arr_other);
                }
            }
            $category['arr_articles'] = array_values($category['arr_articles']);
        } //end for $arr_categories

        return $arr_categories;
    }

    /**
     * Lay danh sach tin bai noi bat tren trang chu cua 1 chuyen trang, chuyen muc
     * @param Int $website_id ID chuyen trang
     * @param Int $category_id ID chuyen muc
     * @param Int $is_default Co hien thi tren trang chu hay khong?
     * @param Int $limit So luong lay
     */
    function gp_qry_all_sticky_article($website_id, $category_id, $is_default, $limit)
    {
        $is_default  = $is_default ? 1 : 0;
        $category_id = (int) $category_id;
        $limit       = (int) $limit;
        
        if(DATABASE_TYPE == 'MSSQL')
        {
            $sql_inner = "
                Select Top $limit 
                    PK_ARTICLE, C.C_SLUG as C_SLUG_CAT, CA.FK_CATEGORY
                From t_ps_sticky S
                Left Join t_ps_article A
                    On s.FK_ARTICLE = A.PK_ARTICLE
                Left Join t_ps_category_article CA
                    On CA.FK_ARTICLE = A.PK_ARTICLE
                Left Join t_ps_category C
                    On C.PK_CATEGORY = CA.FK_CATEGORY
                Where S.FK_CATEGORY = $category_id
                    And S.C_DEFAULT = $is_default
                    And A.C_STATUS = 3
                    And C.C_STATUS = 1
                    And DATEDIFF(mi,A.C_BEGIN_DATE,GETDATE())>=0
                    And DATEDIFF(mi,GETDATE(),A.C_END_DATE) >= 0 
                Order By S.C_ORDER
                ";
            $sql = "Select 
                        A.PK_ARTICLE as ID
                        , A.PK_ARTICLE
                        , A.C_TITLE
                        , A.C_SLUG
                        , A.C_SUMMARY
                        , A.C_BEGIN_DATE
                        , A.C_HAS_VIDEO
                        , A.C_HAS_PHOTO
                        , $website_id As FK_WEBSITE
                        , A.C_FILE_NAME
                        , FK_CATEGORY
                        , C_SLUG_CAT
                        , A.C_SUB_TITLE
                    From t_ps_article A
                        Inner Join ($sql_inner) I
                        On A.PK_ARTICLE = I.PK_ARTICLE";
            return $this->db->GetAssoc($sql);
        }
        else if(DATABASE_TYPE == 'MYSQL')
        {
            //Lay tin noi bat cua chuyen trang
            if ( ($website_id > 0) && ($category_id < 1 OR $category_id == NULL))
            {
                
            }
            elseif ( ($website_id < 1 OR $website_id == NULL) && ($category_id > 0))
            {
                //Lay tin noi bat cua chuyen muc                
                
            }
            $sql_inner = "Select 
                                PK_ARTICLE
                                , C.C_SLUG as C_SLUG_CAT
                                , CA.FK_CATEGORY
                            From t_ps_sticky S
                                Left Join t_ps_article a
                                On S.FK_ARTICLE = a.PK_ARTICLE
                                    Left Join t_ps_category_article CA
                                    On CA.FK_ARTICLE = a.PK_ARTICLE
                                        Left Join t_ps_category C
                                        On C.PK_CATEGORY = CA.FK_CATEGORY
                            Where S.FK_CATEGORY = $category_id
                                And S.C_DEFAULT = $is_default
                                And a.C_STATUS = 3
                                And C.C_STATUS = 1
                                And a.C_BEGIN_DATE <= Now()
                                And a.C_END_DATE >= Now() 
                            Order By S.C_ORDER LIMIT $limit
                            ";
            
            $sql = "Select
                          A.PK_ARTICLE     as ID
                          , A.PK_ARTICLE
                          , A.C_TITLE
                          , A.C_SLUG
                          , A.C_SUMMARY
                          , A.C_BEGIN_DATE
                          , A.C_HAS_VIDEO
                          , A.C_HAS_PHOTO
                          , $website_id    As FK_WEBSITE
                          , A.C_FILE_NAME
                          , FK_CATEGORY
                          , C_SLUG_CAT
                          , A.C_SUB_TITLE
                    From t_ps_article A
                        Right Join ($sql_inner) I
                        On A.PK_ARTICLE = I.PK_ARTICLE";
            return $this->db->GetAssoc($sql);
        }        
    }

    
    /*
     * Lay ID chuyen trang theo ma (code)
     */
    function gp_get_website_id($ws_code)
    {
        return $this->db->getOne('Select PK_WEBSITE From t_ps_website Where C_CODE = ?', array($ws_code));
    }

    /*
     * Kiem tra quyen cua NSD
     */
    public function gp_check_user_permission($code_function, $user_permission_on_website = TRUE)
    {
        @session::init();
        $v_website_id      = session::get('session_website_id');
        $v_user_id         = session::get('user_id');
        $v_user_permission = ($user_permission_on_website == TRUE) ? $v_website_id . '::' . $code_function : $code_function;
        
        //check quyen admin
        $stmt   = "select 
                        COUNT(*) 
                    From t_cores_user_group ug 
                        inner join t_cores_group g
                        on ug.FK_GROUP = g.PK_GROUP
                    where FK_USER = $v_user_id 
                        and g.C_CODE='ADMINISTRATORS'";
        $v_is_admin        = $this->db->getOne($stmt);
        if ($v_is_admin > 0)
        {
            return 1;
        }
        
        
        //check quyen cua user theo chuyen tran
        if ($user_permission_on_website == TRUE)
        {
            // $v_user_permission=$v_website_id.'::'.$code_function;
            $sql = "SELECT 
                        (Upper(w.PK_WEBSITE) + '::' + UF.C_FUNCTION_CODE) AS C_FUNCTION_CODE
                    FROM t_cores_user_function UF
                       LEFT JOIN t_ps_website w 
                       ON UF.FK_WEBSITE=w.PK_WEBSITE
                   WHERE UF.FK_USER=$v_user_id
                   UNION 
                   SELECT 
                       (Upper(w.PK_WEBSITE) + '::' + GF.C_FUNCTION_CODE) AS C_FUNCTION_CODE
                   FROM t_cores_group_function GF
                       LEFT JOIN t_ps_website w ON GF.FK_WEBSITE=w.PK_WEBSITE
                   WHERE FK_GROUP IN (SELECT FK_GROUP
                                        FROM t_cores_user_group
                                        WHERE FK_USER=$v_user_id
                                      ) ";
        }
        else
        {
            //$v_user_permission = $code_function;
            $sql = "select distinct 
                        C_FUNCTION_CODE 
                    from t_cores_user_function 
                    where FK_USER = $v_user_id
                    union
                    select distinct 
                        C_FUNCTION_CODE 
                    from t_cores_group_function 
                    where FK_GROUP in (select 
                                            FK_GROUP 
                                        from t_cores_user_group 
                                        where FK_USER = $v_user_id
                                        )";
        }

        $arr_all_function = $this->db->getCol($sql);
        return in_array($v_user_permission, $arr_all_function);
    }
    
    
    /**
     * tao sql check depend mssql
     * @param type $arr_depended_table
     * @param type $v_column_name
     * @return string
     */
    public function gp_build_check_depend_qry($arr_depended_table, $v_column_name)
    {
        
        
        $sql_check_depend = '';
        
        if(DATABASE_TYPE == 'MSSQL')
        {
            $sql_check_depend = '(Select Sum(build_check_depend_qry.C_COUNT) COUNT_DEPEND From (';
            $i                = 0;
            foreach ($arr_depended_table as $table => $foreign_key)
            {
                $sql_check_depend .= ($i == 0) ? '' : ' UNION ALL';
                $sql_check_depend .= " Select Count(*) C_COUNT From $table Where $foreign_key=$v_column_name";
                $i++;
            }
            $sql_check_depend .= ') build_check_depend_qry)as COUNT_DEPEND';
        }
        else if(DATABASE_TYPE == 'MYSQL')
        {
             
            foreach ($arr_depended_table as $table => $foreign_key)
            {
                if($sql_check_depend == '')
                {
                    $sql_check_depend .= "( (Select Count(*) C_COUNT From $table Where $foreign_key=$v_column_name)";
                }
                else
                {
                    $sql_check_depend .= " + (Select Count(*) C_COUNT From $table Where $foreign_key=$v_column_name)";
                }
            }    
            $sql_check_depend .= ") as COUNT_DEPEND ";
        }
       
        return $sql_check_depend;
    }
    
    /**
     * tao sql check depend mysql
     * @param type $arr_depended_table
     * @param type $v_column_name
     * @return string
     */
    public function gp_build_check_depend_qry_mysql($arr_depended_table, $v_column_name)
    {
        $sql_check_depend = '';
        foreach ($arr_depended_table as $table => $foreign_key)
        {
            if($sql_check_depend == '')
            {
                $sql_check_depend .= "( (Select Count(*) C_COUNT From $table Where $foreign_key=$v_column_name)";
            }
            else
            {
                $sql_check_depend .= " + (Select Count(*) C_COUNT From $table Where $foreign_key=$v_column_name)";
            }
        }    
        $sql_check_depend .= ") as COUNT_DEPEND ";
        return $sql_check_depend;
    }
    
    
    public function gp_qry_lang_of_website($v_website_id)
    {
    	if (DEBUG_MODE < 10)
    	{
    		$this->db->debug = 0;
    	}
    	
        $v_website_id = replace_bad_char($v_website_id);
        if ($v_website_id != 0)
        {
            $stmt = "select C_CODE from t_cores_list Where PK_LIST = (select FK_LANG from t_ps_website where PK_WEBSITE=$v_website_id)";
            $ret = $this->db->getOne($stmt);
        }
        else
        {
            if(DATABASE_TYPE == 'MYSQL')
            {
                $stmt = "select C_CODE from t_cores_list 
                        where FK_LISTTYPE = (Select PK_LISTTYPE from t_cores_listtype 
                        where C_CODE = 'DM_NGON_NGU')";
            }
            else if(DATABASE_TYPE == 'MSSQL')
            {
                $stmt = "select top 1 C_CODE from t_cores_list 
                        where FK_LISTTYPE = (Select PK_LISTTYPE from t_cores_listtype 
                        where C_CODE = 'DM_NGON_NGU')";
            }
            
            $ret = $this->db->getOne($stmt);
        }
        
        $this->db->debug = DEBUG_MODE;
        return $ret;
    }

    /*
     * Lay danh sach tin noi bat cua trang chu sticky
     * 
     * @return array
     */
    public function gp_qry_all_sticky($website_id = 0)
    {
        $website_id = replace_bad_char($website_id);
        if ($website_id == 0)
        {
            $stmt       = "select PK_WEBSITE from t_ps_website  where C_ORDER = 1";
            $website_id = $this->db->getOne($stmt);
        }
        
        if(DATABASE_TYPE == 'MSSQL')
        {
            $stmt       = "SELECT sa.FK_CATEGORY,
                                sa.C_SLUG_CATEGORY,
                                sa.FK_ARTICLE,
                                sa.FK_WEBSITE,
                                sa.C_TITLE,
                                sa.C_SUMMARY,
                                sa.C_SLUG_ARTICLE,
                                sa.C_ORDER,
                                C_FILE_NAME,
                                sa.C_BEGIN_DATE,
                                sa.C_HAS_VIDEO,
                                sa.C_HAS_PHOTO
                        From
                       (SELECT FK_CATEGORY,

                          (SELECT C_SLUG
                           FROM t_ps_category
                           WHERE PK_CATEGORY = FK_CATEGORY) AS C_SLUG_CATEGORY,
                               FK_ARTICLE,

                          (SELECT FK_WEBSITE
                           FROM t_ps_category
                           WHERE PK_CATEGORY = FK_CATEGORY) AS FK_WEBSITE,
                               C_FILE_NAME,
                               C_TITLE,
                               C_SUMMARY,
                               C_SLUG AS C_SLUG_ARTICLE,
                               C_ORDER,
                               Convert(varchar,C_BEGIN_DATE, 120) as C_BEGIN_DATE,
                               C_HAS_VIDEO,
                               C_HAS_PHOTO
                        FROM t_ps_sticky s
                        INNER JOIN t_ps_article a ON FK_ARTICLE = PK_ARTICLE
                        WHERE C_DEFAULT = 1
                          AND FK_WEBSITE= $website_id 
                          AND a.C_STATUS = 3
                          AND (select C_STATUS from t_ps_category where PK_CATEGORY = s.FK_CATEGORY) = 1 
                          AND DATEDIFF(mi,C_BEGIN_DATE,GETDATE())>=0
                          AND DATEDIFF(mi,GETDATE(),C_END_DATE) >= 0) sa
                     ORDER BY C_ORDER";
        }
        else if(DATABASE_TYPE == 'MYSQL')
        {
            $stmt = "Select
                        sa.FK_CATEGORY
                        , (Select C_SLUG From t_ps_category Where PK_CATEGORY = sa.FK_CATEGORY) as C_SLUG_CATEGORY
                        , A.PK_ARTICLE   as FK_ARTICLE
                        , $website_id    as FK_WEBSITE
                        , A.C_TITLE
                        , A.C_SUMMARY
                        , A.C_SLUG       as C_SLUG_ARTICLE
                        , sa.C_ORDER
                        , A. C_FILE_NAME
                        , DATE_FORMAT(A.C_BEGIN_DATE,'%Y-%m-%d %H:%i:%s') as C_BEGIN_DATE
                        , A.C_HAS_VIDEO
                        , A.C_HAS_PHOTO
                    From t_ps_article A
                      Right Join (Select
                                    FK_ARTICLE
                                    , FK_CATEGORY
                                    , C_ORDER
                                  From t_ps_sticky S
                                  Where C_DEFAULT = 1
                                      And (Select C_STATUS From t_ps_category Where PK_CATEGORY = FK_CATEGORY) = 1
                                      And FK_WEBSITE = $website_id) sa
                        On A.PK_ARTICLE = sa.FK_ARTICLE
                    Where A.C_STATUS = 3
                         AND DATEDIFF(NOW(),C_BEGIN_DATE) >=0
                         AND DATEDIFF(C_END_DATE, NOW()) >= 0
                    Order by sa.C_ORDER";
        }

        return $this->db->getAll($stmt);
    }

    /*
    function qry_all_category_to_cache($website_id = 0)
    {
        $website_id = replace_bad_char($website_id);
        if (get_system_config_value(CFGKEY_NEW_ARTICLE_ICON) == 'true')
        {
            $new_article_mode = 0;
        }
        else
        {
            $new_article_mode = 1;
        }
        $new_article_cond = get_system_config_value(CFGKEY_NEW_ARTICLE_COND);
        if ($website_id == 0)
        {
            $stmt       = "select top 1 PK_WEBSITE from t_ps_website Where C_STATUS>0 Order By C_ORDER";
            $website_id = $this->db->getOne($stmt);
        }

        $v_limit_article_per_category = 4;


        //LienND Update 20130328, Tin lay bao gom ca tin truc tiep, tin cua tat ca cac node con
        //1. Lay danh sach chuyen muc noi bat
        $stmt                      = "Select 
					C.PK_CATEGORY      
					,STUFF(
						(SELECT ','+ convert(varchar,PK_CATEGORY) 
							FROM t_ps_category xx
							WHERE xx.C_INTERNAL_ORDER Like C.C_INTERNAL_ORDER + '%'
							FOR XML PATH('')) , 1 , 1 , '' 
					 ) as C_SUB_NODE_LIST
															 
				From t_ps_category C 
					Right Join T_PS_HOMEPAGE_CATEGORY HC 
					On C.PK_CATEGORY=HC.FK_CATEGORY
				Where C.FK_WEBSITE=?
					And C.C_STATUS=1
				Order By HC.C_ORDER";
        $arr_all_homepage_category = $this->db->getAssoc($stmt, array($website_id));

        $sql2 = '';
        foreach ($arr_all_homepage_category as $v_category_id => $v_sub_node_list)
        {
            if ($sql2 != '')
            {
                $sql2 .= "\n UNION ALL \n";
            }
            $sql2 .= "Select 
						 FC.FK_WEBSITE
						,FC.PK_CATEGORY
						,FC.C_SLUG
						,FC.C_NAME 
						,(Select 
								FA.PK_ARTICLE
								,FA.C_TITLE
								,FA.C_SLUG
								,FA.C_SUMMARY
								,FA.C_BEGIN_DATE
								,FA.C_HAS_VIDEO
								,FA.C_HAS_PHOTO
								,$website_id AS FK_WEBSITE
								, C_FILE_NAME 
								,(dateDiff(dd, FA.C_BEGIN_DATE, getDate()) - $new_article_cond) as CK_NEW_ARTICLE
								,FK_CATEGORY
								,C_SLUG_CAT
							
						   From t_ps_article FA 
								Right Join (Select 
												*
											From (
													Select 
														A.PK_ARTICLE                     
														,ROW_NUMBER() Over(Order by A.C_BEGIN_DATE Desc) as RN 
														, fca.FK_CATEGORY
														, (Select C_SLUG From t_ps_category Where PK_CATEGORY=fca.FK_CATEGORY) as C_SLUG_CAT
													From t_ps_article A 
														Right Join (Select 
																		MAX(CA.FK_CATEGORY) as FK_CATEGORY
																		, CA.FK_ARTICLE                                 
																	From t_ps_category_article as CA Left Join t_ps_category as C on CA.FK_CATEGORY=C.PK_CATEGORY
																	Where C.FK_WEBSITE=30
																		And C.C_STATUS=1";
            if ($v_sub_node_list != $v_category_id)
            {
                $sql2 .= " And CA.FK_CATEGORY in ($v_sub_node_list)";
            }
            else
            {
                $sql2 .= " And CA.FK_CATEGORY=$v_category_id";
            }
            $sql2 .= " Group By FK_ARTICLE
																	) fca 
														On A.PK_ARTICLE=fca.FK_ARTICLE           
													Where A.C_STATUS=3
														And DATEDIFF(mi,A.C_BEGIN_DATE,GETDATE())>=0
														And DATEDIFF(mi,GETDATE(),A.C_END_DATE) >= 0                    
												) as sub_a
											Where sub_a.RN>=1 And sub_a.RN <= 4
											) as top4a
								On FA.PK_ARTICLE=top4a.PK_ARTICLE
							For XML Raw, root('data')
							) AS C_XML_ARTICLE 
					From t_ps_category FC
					Where FC.PK_CATEGORY = $v_category_id ";
        }//end foreach
        //echo 'Line:'.__LINE__.'<br>File:'.__FILE__;
        //echo '<textarea>'. $sql2 .'</textarea>';exit;

        /*
          $stmt       = "
          Select
          c.PK_CATEGORY
          , hc.C_ORDER
          , c.C_NAME
          , c.C_SLUG
          ,  (select FK_WEBSITE from t_ps_category where PK_CATEGORY = c.PK_CATEGORY ) as FK_WEBSITE
          , ( Select top 4 x.* From (select a.PK_ARTICLE,C_TITLE,a.C_SLUG , a.C_SUMMARY, a.C_BEGIN_DATE
          ,(select C_FILE_NAME from T_PS_MEDIA where PK_MEDIA = A.FK_MEDIA) as C_FILE_NAME
          ,0 As gia,ca.C_ORDER
          , (dateDiff(dd, A.C_BEGIN_DATE, getDate()) - $new_article_cond) as CK_NEW_ARTICLE
          , A.C_HAS_VIDEO, A.C_HAS_PHOTO
          From t_ps_article a inner join (select FK_CATEGORY,FK_ARTICLE,C_ORDER From t_ps_sticky
          where FK_CATEGORY = hc.FK_CATEGORY and C_DEFAULT = 0) ca on a.PK_ARTICLE = ca.FK_ARTICLE
          where DATEDIFF(mi,C_BEGIN_DATE,GETDATE())>=0 and DATEDIFF(mi,GETDATE(),C_END_DATE) >= 0 and a.C_STATUS = 3

          Union all
          Select
          top 10 A.PK_ARTICLE
          , A.C_TITLE
          , A.C_SLUG
          , A.C_SUMMARY
          , A.C_BEGIN_DATE
          ,(select C_FILE_NAME from T_PS_MEDIA where PK_MEDIA = A.FK_MEDIA) as C_FILE_NAME
          , 1 as gia
          , 0 as C_ORDER
          , (dateDiff(dd, A.C_BEGIN_DATE, getDate()) - $new_article_cond) as CK_NEW_ARTICLE
          , A.C_HAS_VIDEO, A.C_HAS_PHOTO
          From t_ps_article A

          Left join t_ps_category_article CA
          On A.PK_ARTICLE=CA.FK_ARTICLE

          Where CA.FK_CATEGORY=hc.FK_CATEGORY and DATEDIFF(mi,A.C_BEGIN_DATE,GETDATE())>=0 and DATEDIFF(mi,GETDATE(),A.C_END_DATE) >= 0 and A.C_STATUS = 3
          and PK_ARTICLE not in (select FK_ARTICLE from t_ps_sticky where FK_CATEGORY = hc.FK_CATEGORY)
          ) x  Order by gia , C_ORDER , C_BEGIN_DATE desc  For XML Raw, root('data')) as C_XML_ARTICLE
          From T_PS_HOMEPAGE_CATEGORY hc

          Join t_ps_category c
          On hc.FK_CATEGORY = c.PK_CATEGORY

          Where hc.FK_WEBSITE = $website_id and c.C_STATUS = 1
          Order by C_ORDER"
          ;
          return $this->db->getAll($stmt);
         */

        //LienND Tunning (Tam thoi khong lay tin noi bat cua chuyen muc        
/*
        $sql2 = "Select 
                    FC.*
                    ,(
                        Select 
                            FA.PK_ARTICLE
                            ,FA.C_TITLE
                            ,FA.C_SLUG
                            ,FA.C_SUMMARY
                            ,FA.C_BEGIN_DATE
                            ,FA.C_HAS_VIDEO
                            ,FA.C_HAS_PHOTO
                            ,$website_id AS FK_WEBSITE
                            , C_FILE_NAME 
                            ,(dateDiff(dd, FA.C_BEGIN_DATE, getDate()) - $new_article_cond) as CK_NEW_ARTICLE
                            ,FK_CATEGORY
                        From t_ps_article FA 
                            Right Join ( 
                                            Select 
                                                PK_ARTICLE 
                                                ,FK_CATEGORY
                                            From 
                                            (
                                                Select 
                                                    A.PK_ARTICLE        
                                                    ,ROW_NUMBER() Over(Order by A.C_BEGIN_DATE Desc) as RN 
                                                    ,fca.FK_CATEGORY
                                                From t_ps_article A 
                                                    Right Join (
                                                                    Select 
                                                                        MAX(CA.FK_CATEGORY) as FK_CATEGORY
                                                                        , CA.FK_ARTICLE 
                                                                    From t_ps_category_article as CA Left Join t_ps_category as C on CA.FK_CATEGORY=C.PK_CATEGORY
                                                                    Where C.FK_WEBSITE=$website_id
                                                                        And C.C_STATUS=1
                                                                        And CA.FK_CATEGORY=FC.PK_CATEGORY
                                                                    Group By FK_ARTICLE
                                                                ) fca on A.PK_ARTICLE=fca.FK_ARTICLE      
                                                Where A.C_STATUS=3
                                                    And DATEDIFF(mi,A.C_BEGIN_DATE,GETDATE())>=0
                                                    AND DATEDIFF(mi,GETDATE(),A.C_END_DATE) >= 0                           
                                            ) as mrs 
                                            Where mrs.RN>=1 And mrs.RN<=$v_limit_article_per_category
                                        ) as ma On FA.PK_ARTICLE=ma.PK_ARTICLE                        
                        For XML Raw, root('data')
                    ) AS C_XML_ARTICLE
                From
                (
                    Select 
                        c.FK_WEBSITE
                        ,c.PK_CATEGORY
                        ,c.C_SLUG
                        ,c.C_NAME    
                        ,hc.C_ORDER
                    From t_ps_category c Right Join T_PS_HOMEPAGE_CATEGORY hc On c.PK_CATEGORY=hc.FK_CATEGORY
                    Where hc.FK_WEBSITE=$website_id    
                ) as FC
                Order By FC.C_ORDER";

        return $this->db->getAll($sql2);
    }
    */

    function gp_qry_all_event($website_id)
    {
        $website_id = replace_bad_char($website_id);
        if ($website_id == 0)
        {
            $stmt              = "select PK_WEBSITE from t_ps_website  where C_ORDER = 1";
            $website_id        = $this->db->getOne($stmt);
        }
        
        if(DATABASE_TYPE == 'MSSQL')
        {
            //query dem so tin bai
            $sql_count_article = '
                Select Count(*) 
                From t_ps_event_article ea 
                Left Join t_ps_article a
                     On ea.FK_ARTICLE = a.PK_ARTICLE
                Left Join t_ps_category c
                     On c.PK_CATEGORY = ea.FK_CATEGORY
                Where 
                    FK_EVENT = e.PK_EVENT
                    And DateDiff(mi,a.C_BEGIN_DATE, GetDate()) >= 0
                    And DateDiff(mi,GetDate(), a.C_END_DATE) >= 0
                    And c.C_STATUS = 1
                    And a.C_STATUS = 3
                            ';
            //query lay su kien
            $stmt = "select 
                            e.*
                            , ($sql_count_article) as C_COUNT_ARTICLE
                        from t_ps_event e 
                        where C_STATUS = 1 
                        and C_DEFAULT=1 and 
                        DATEDIFF(mi,C_BEGIN_DATE,GETDATE())>=0 and  
                        DATEDIFF(mi,GETDATE(),C_END_DATE) >= 0  and FK_WEBSITE = $website_id 
                        and e.C_IS_REPORT <> 1 
                        order by C_ORDER";
        }
        else if(DATABASE_TYPE == 'MYSQL')
        {
            //query dem so tin bai
            $sql_count_article = '
                Select Count(*) 
                From t_ps_event_article ea 
                Left Join t_ps_article a
                     On ea.FK_ARTICLE = a.PK_ARTICLE
                Left Join t_ps_category c
                     On c.PK_CATEGORY = ea.FK_CATEGORY
                Where 
                    FK_EVENT = e.PK_EVENT
                    And DATEDIFF(NOW(), a.C_BEGIN_DATE) >= 0
                    And DATEDIFF(a.C_END_DATE, NOW()) >= 0
                    And c.C_STATUS = 1
                    And a.C_STATUS = 3
                            ';
            //query lay su kien
            $stmt = "select 
                            e.*
                            , ($sql_count_article) as C_COUNT_ARTICLE
                        from t_ps_event e 
                        where C_STATUS = 1 
                        and C_DEFAULT=1 and 
                        DATEDIFF(NOW(), C_BEGIN_DATE) >=0 and  
                        DATEDIFF(C_END_DATE, NOW()) >= 0  and FK_WEBSITE = $website_id 
                        and e.C_IS_REPORT <> 1 
                        order by C_ORDER";
            if (!$arr_all_event = $this->db->getAll($stmt))
            {
                return $arr_all_event;
            }

            foreach ($arr_all_event as &$event)
            {
                if ($event['C_COUNT_ARTICLE'] == 1)
                {
                    if(DATABASE_TYPE == 'MSSQL')
                    {
                        $sql_article                = "
                            Select Top 1 
                                a.PK_ARTICLE, a.C_SLUG
                                , c.PK_CATEGORY, c.C_SLUG As C_CAT_SLUG
                            From t_ps_event_article ea
                            Left Join t_ps_article a
                                On ea.FK_ARTICLE = a.PK_ARTICLE
                            Left Join t_ps_category c
                                On c.PK_CATEGORY = ea.FK_CATEGORY
                            Where 
                                ea.FK_EVENT = ?
                                And DateDiff(mi,a.C_BEGIN_DATE, GetDate()) >= 0
                                And DateDiff(mi,GetDate(), a.C_END_DATE) >= 0
                                And c.C_STATUS = 1
                                And a.C_STATUS = 3
                            Order By a.C_BEGIN_DATE Desc
                                 ";
                    }
                    else if(DATABASE_TYPE == 'MYSQL')
                    {
                        $sql_article                = "
                            Select 
                                a.PK_ARTICLE, a.C_SLUG
                                , c.PK_CATEGORY, c.C_SLUG As C_CAT_SLUG
                            From t_ps_event_article ea
                            Left Join t_ps_article a
                                On ea.FK_ARTICLE = a.PK_ARTICLE
                            Left Join t_ps_category c
                                On c.PK_CATEGORY = ea.FK_CATEGORY
                            Where 
                                ea.FK_EVENT = ?
                                And DATEDIFF(NOW(), a.C_BEGIN_DATE) >=0 
                                And DATEDIFF(a.C_END_DATE, NOW()) >= 0 
                                And c.C_STATUS = 1
                                And a.C_STATUS = 3
                            Order By a.C_BEGIN_DATE Desc
                                 ";
                    }
                    $event['arr_first_article'] = $this->db->getRow($sql_article, array($event['PK_EVENT']));

                } //end if
            } //end foreach
            return $arr_all_event;
        }
        
        return $this->db->getAll($stmt);
    }

    function gp_qry_all_report($website_id)
    {
        ///t_ps_event???
        $website_id = replace_bad_char($website_id);
        if ($website_id == 0)
        {
            if(DATABASE_TYPE == 'MYSQL')
            {
                $stmt       = "select PK_WEBSITE from t_ps_website  where C_ORDER = 1";
            }
            else if(DATABASE_TYPE == 'MSSQL')
            {
                $stmt       = "select top 1 PK_WEBSITE from t_ps_website  where C_ORDER = 1";
            }
            $website_id = $this->db->getOne($stmt);
        }
        
        if(DATABASE_TYPE == 'MSSQL')
        {
            $stmt = "Select 
                        * 
                    From t_ps_event E
                    Where C_STATUS = 1 
                        And C_DEFAULT=1 
                        And DATEDIFF(mi,C_BEGIN_DATE,GETDATE())>=0 
                        And DATEDIFF(mi,GETDATE(),C_END_DATE) >= 0  
                        And FK_WEBSITE = $website_id 
                        And E.C_IS_REPORT = 1 
                    Order by C_ORDER";
        }
        else if(DATABASE_TYPE == 'MYSQL')
        {
            $stmt = "Select 
                        * 
                    From t_ps_event E
                    Where C_STATUS = 1 
                        And C_DEFAULT = 1 
                        And C_BEGIN_DATE <= Now()  
                        And C_END_DATE >= Now()
                        And FK_WEBSITE = $website_id 
                        And E.C_IS_REPORT = 1 
                    Order by C_ORDER";
        }
        
        return $this->db->getAll($stmt);
    }

//    public function gp_qry_all_menu_position($website_id = 0)
//    {
//        $website_id = replace_bad_char($website_id);
//        $stmt       = "select C_OPTION_VALUE from t_ps_option where C_OPTION_KEY = 'WEBSITE_MENU' ";
//        $xml        = $this->db->getOne($stmt);
//        if ($website_id == 0)
//        {
//            if(DATABASE_TYPE == 'MYSQL')
//            {
//                $stmt = "select PK_WEBSITE from t_ps_website  where C_ORDER = 1";
//            }
//            elseif(DATABASE_TYPE == 'MSSQL')
//            {
//                $stmt = "select top 1 PK_WEBSITE from t_ps_website  where C_ORDER = 1";
//            }
//            $website_id = $this->db->getOne($stmt);
//        }
//        $dom        = simplexml_load_string($xml);
//        if (is_object($dom))
//        {
//            $x_path = "//theme_position[@id_website='$website_id']/item";
//            $r      = (array) $dom->xpath($x_path);
//            //tao array site map
//            $arr_sitemap = array( '@attributes' => array(
//                                                    'position_menu_id' =>  -1,
//                                                    'position_code' =>  'sitemap'
//                                                  ));
//            $r[] = $arr_sitemap;
//        }
//        else
//        {
//            $r = array();
//        }
//        
//        $DATA_MODEL            = array();
//        
//        for($count=0;$count<count($r);$count++)
//        {
//            $row = (array) $r[$count];
//             
//            $v_menu_position_id           = (int) $row['@attributes']['position_menu_id'];
//            $v_position_code              = (string) $row['@attributes']['position_code'];
//            
//            //kiem tra co phai site map k
//            if($v_menu_position_id == -1)
//            {
//                $stmt = "Select *
//                        From t_ps_menu
//                        Where FK_MENU_POSITION = (Select
//                                                    PK_MENU_POSITION
//                                                  From t_ps_menu_position
//                                                  Where C_TYPE = 1
//                                                      And FK_WEBSITE = $website_id)
//                        ORDER By C_INTERNAL_ORDER"; 
//            }
//            //ko phai site map
//            else
//            {
//                $stmt = "select *
//                        from t_ps_menu
//                        where FK_MENU_POSITION = $v_menu_position_id
//                        order by C_INTERNAL_ORDER";
//            }
//            $DATA_MODEL[$v_position_code] = $this->db->getAll($stmt);
//            
//            for ($i = 0; $i < count($DATA_MODEL[$v_position_code]); $i++)
//            {       
//                $data_xml    = simplexml_load_string(xml_add_declaration($DATA_MODEL[$v_position_code][$i]['C_VALUE']));
//                $x_path      = "//item[@data='1']";
//                $arr_xml     = $data_xml->xpath($x_path);
//                $v_menu_type = $arr_xml[0]->attributes()->type;
//                if ($v_menu_type == 'url')
//                {
//                    $DATA_MODEL[$v_position_code][$i]['C_URL'] = strval($arr_xml[0]);
//                }
//                elseif ($v_menu_type == 'category')
//                {
//                    $x_path        = "//item[@data='1']/id";
//                    $arr_xml       = $data_xml->xpath($x_path);
//                    $v_category_id = strval($arr_xml[0]);
//
//                    $x_path          = "//item[@data='1']/slug";
//                    $arr_xml         = $data_xml->xpath($x_path);
//                    $v_category_slug = strval($arr_xml[0]);
//
//                    $DATA_MODEL[$v_position_code][$i]['C_URL'] = build_url_category($v_category_slug, $website_id, $v_category_id);
//                }
//                elseif ($v_menu_type == 'article')
//                {
//                    $x_path        = "//item[@data='1']/category_id";
//                    $arr_xml       = $data_xml->xpath($x_path);
//                    $v_category_id = strval($arr_xml[0]);
//
//                    $x_path          = "//item[@data='1']/category_slug";
//                    $arr_xml         = $data_xml->xpath($x_path);
//                    $v_category_slug = strval($arr_xml[0]);
//
//                    $x_path       = "//item[@data='1']/article_id";
//                    $arr_xml      = $data_xml->xpath($x_path);
//                    $v_article_id = strval($arr_xml[0]);
//
//                    $x_path                                    = "//item[@data='1']/article_slug";
//                    $arr_xml                                   = $data_xml->xpath($x_path);
//                    $v_article_slug                            = strval($arr_xml[0]);
//                    $DATA_MODEL[$v_position_code][$i]['C_URL'] = build_url_article($v_category_slug, $v_article_slug, $website_id, $v_category_id, $v_article_id);
//                }
//                elseif ($v_menu_type == 'module')
//                {
//                    $x_path         = "//item[@data='1']";
//                    $arr_xml        = $data_xml->xpath($x_path);
//                    $v_value_module = $arr_xml[0]->attributes()->value;
//                    
//                    //cau hoi cong dan
//                    if ($v_value_module == 'citizens question')
//                    {
//                        $DATA_MODEL[$v_position_code][$i]['C_URL'] = build_url_cq($website_id);
//                    }
////                    //lien ket web
////                    else if($v_value_module == 'weblink')
////                    {
////                        $DATA_MODEL[$v_position_code][$i]['C_URL'] = build_url_weblink($website_id);
////                    }
//                    //so do site
//                    else if($v_value_module == 'sitemap')
//                    {
//                        $DATA_MODEL[$v_position_code][$i]['C_URL'] = build_url_sitemap($website_id);
//                    }
//                    //gop y phan hoi
//                    else if($v_value_module == 'feedback')
//                    {
//                        $DATA_MODEL[$v_position_code][$i]['C_URL'] = build_url_feedback($website_id);
//                    }
//                    //dịch vụ công trực tuyến
//                    else if($v_value_module == 'public_service')
//                    {
//                        $DATA_MODEL[$v_position_code][$i]['C_URL'] = build_url_send_internet_record();
//                    }
//                    //tra cuu tong hop
//                    else if($v_value_module == 'synthesis')
//                    {
//                        $DATA_MODEL[$v_position_code][$i]['C_URL'] = build_url_synthesis($this->website_id);
//                    }
//                    //Danh gia can bo
//                    else if($v_value_module == 'evaluation')
//                    {
//                        $DATA_MODEL[$v_position_code][$i]['C_URL'] = build_url_evaluation();
//                    }
//                    //Huong dan thu tục hành chính
//                    else if($v_value_module == 'guidance')
//                    {
//                        $DATA_MODEL[$v_position_code][$i]['C_URL'] = build_url_guidance(true);
//                    }
//                }
//            }
//        }
//        
//        return $DATA_MODEL;
//    }
    public function gp_qry_all_menu_position($website_id = 0)
    {
        $website_id = replace_bad_char($website_id);
        $stmt       = "select C_OPTION_VALUE from t_ps_option where C_OPTION_KEY = 'WEBSITE_MENU' ";
        $xml        = $this->db->getOne($stmt);
        if ($website_id == 0)
        {
            $stmt = "select PK_WEBSITE from t_ps_website  where C_ORDER = 1";
            $website_id = $this->db->getOne($stmt);
        }
        $dom        = simplexml_load_string($xml);
        if (is_object($dom))
        {
            $x_path = "//theme_position[@id_website='$website_id']/item";
            $r      = (array) $dom->xpath($x_path);
            //tao array site map
            $arr_sitemap = array( '@attributes' => array(
                                                    'position_menu_id' =>  -1,
                                                    'position_code' =>  'sitemap'
                                                  ));
            $r[] = $arr_sitemap;
        }
        else
        {
            $r = array();
        }
        
        $DATA_MODEL            = array();
        
        for($count=0;$count<count($r);$count++)
        {
            $row = (array) $r[$count];
             
            $v_menu_position_id           = (int) $row['@attributes']['position_menu_id'];
            $v_position_code              = (string) $row['@attributes']['position_code'];
            
            //kiem tra co phai site map k
            if($v_menu_position_id == -1)
            {
                $stmt = "Select *
                        From t_ps_menu
                        Where FK_MENU_POSITION = (Select
                                                    PK_MENU_POSITION
                                                  From t_ps_menu_position
                                                  Where C_TYPE = 1
                                                      And FK_WEBSITE = $website_id)
                        ORDER By C_INTERNAL_ORDER"; 
            }
            //ko phai site map
            else
            {
                $stmt = "select *
                        from t_ps_menu
                        where FK_MENU_POSITION = $v_menu_position_id
                        order by C_INTERNAL_ORDER";
            }
            
            $DATA_MODEL[$v_position_code] = $this->db->getAll($stmt);
            for ($i = 0; $i < count($DATA_MODEL[$v_position_code]); $i++)
            {       
                $data_xml    = simplexml_load_string(xml_add_declaration($DATA_MODEL[$v_position_code][$i]['C_VALUE']));
                $x_path      = "//item[@data='1']";
                $arr_xml     = $data_xml->xpath($x_path);
                $v_menu_type = $arr_xml[0]->attributes()->type;
                
                if ($v_menu_type == 'url')
                {
                    $DATA_MODEL[$v_position_code][$i]['C_URL'] = strval($arr_xml[0]);
                    $DATA_MODEL[$v_position_code][$i]['C_MENU_TYPE'] = 'link';
                }
                elseif ($v_menu_type == 'category')
                {
                    $x_path        = "//item[@data='1']/id";
                    $arr_xml       = $data_xml->xpath($x_path);
                    $v_category_id = strval($arr_xml[0]);

                    $x_path          = "//item[@data='1']/slug";
                    $arr_xml         = $data_xml->xpath($x_path);
                    $v_category_slug = strval($arr_xml[0]);

                    $DATA_MODEL[$v_position_code][$i]['C_URL'] = build_url_category($v_category_slug, $website_id, $v_category_id);
                    $DATA_MODEL[$v_position_code][$i]['C_MENU_TYPE'] = 'category';
                }
                elseif ($v_menu_type == 'article')
                {
                    $x_path        = "//item[@data='1']/category_id";
                    $arr_xml       = $data_xml->xpath($x_path);
                    $v_category_id = strval($arr_xml[0]);

                    $x_path          = "//item[@data='1']/category_slug";
                    $arr_xml         = $data_xml->xpath($x_path);
                    $v_category_slug = strval($arr_xml[0]);

                    $x_path       = "//item[@data='1']/article_id";
                    $arr_xml      = $data_xml->xpath($x_path);
                    $v_article_id = strval($arr_xml[0]);

                    $x_path                                    = "//item[@data='1']/article_slug";
                    $arr_xml                                   = $data_xml->xpath($x_path);
                    $v_article_slug                            = strval($arr_xml[0]);
                    $DATA_MODEL[$v_position_code][$i]['C_URL'] = build_url_article($v_category_slug, $v_article_slug, $website_id, $v_category_id, $v_article_id);
                    $DATA_MODEL[$v_position_code][$i]['C_MENU_TYPE'] = 'article';
                }
                elseif ($v_menu_type == 'module')
                {
                    $x_path         = "//item[@data='1']";
                    $arr_xml        = $data_xml->xpath($x_path);
                    $v_value_module = (string)$arr_xml[0]->attributes()->value;
                    
                    //cau hoi cong dan
                    if ($v_value_module == 'citizens question')
                    {
                        $DATA_MODEL[$v_position_code][$i]['C_URL'] = build_url_cq($website_id);
                    }
                    //lien ket web
                    else if($v_value_module == 'weblink')
                    {
                        $DATA_MODEL[$v_position_code][$i]['C_URL'] = build_url_weblink($website_id);
                    }
                    //so do site
                    else if($v_value_module == 'sitemap')
                    {
                        $DATA_MODEL[$v_position_code][$i]['C_URL'] = build_url_sitemap($website_id);
                    }
                    //gop y phan hoi
                    else if($v_value_module == 'feedback')
                    {
                        $DATA_MODEL[$v_position_code][$i]['C_URL'] = build_url_feedback($website_id);
                    }
                    //dịch vụ công trực tuyến
                    else if($v_value_module == 'public_service')
                    {
                        $DATA_MODEL[$v_position_code][$i]['C_URL'] = build_url_send_internet_record();
                    }
                    //tra cuu tong hop
                    else if($v_value_module == 'synthesis')
                    {
                        $DATA_MODEL[$v_position_code][$i]['C_URL'] = build_url_synthesis($website_id);
                    }
                    //Huong dan thu tục hành chính
                    else if($v_value_module == 'guidance')
                    {
                        $DATA_MODEL[$v_position_code][$i]['C_URL'] = build_url_guidance($website_id,true);
                    }                    
                    //đăng ký hồ sơ online
                    else if($v_value_module == 'record_submit_internet')
                    {
                        $DATA_MODEL[$v_position_code][$i]['C_URL'] = build_url_submit_internet_record($website_id);
                    }
                    //lịch sử nộp hồ sơ
                    else if($v_value_module == 'history_filing')
                    {
                        $DATA_MODEL[$v_position_code][$i]['C_URL'] = build_url_single_account_citizen($website_id, true);
                    }
                    //Danh gia can bo
                    else if($v_value_module == 'evaluation')
                    {
                        $DATA_MODEL[$v_position_code][$i]['C_URL'] = build_url_evaluation();
                    }
                    //thong tin toa soan
                    else if($v_value_module == 'office_info')
                    {
                        //lay du lieu
                        $sql = "Select
                                    FK_WEBSITE,
                                    FK_ARTICLE,
                                    (select
                                       C_DEFAULT_CATEGORY
                                  from t_ps_article
                                  Where PK_ARTICLE = OI.FK_ARTICLE) as C_CATEGORY_ID 
                                  From t_ps_office_info OI 
                                  Where C_TYPE = 1
                                        And OI.FK_WEBSITE = $this->website_id
                                        And OI.C_STATUS = 1";
                        $arr_data = $this->db->getRow($sql);
                        $website_modile_id  = isset($arr_data['FK_WEBSITE'])?$arr_data['FK_WEBSITE']:0;
                        $article_id         = isset($arr_data['FK_ARTICLE'])?$arr_data['FK_ARTICLE']:0;
                        $category_id        = isset($arr_data['C_CATEGORY_ID'])?$arr_data['C_CATEGORY_ID']:0;
                        //tao duong link
                        $DATA_MODEL[$v_position_code][$i]['C_URL'] = build_url_office_info($website_modile_id,$category_id,$article_id);
                    }
                    $DATA_MODEL[$v_position_code][$i]['C_MENU_TYPE'] = 'module';
                    $DATA_MODEL[$v_position_code][$i]['C_MODULE_CURRENT'] = $v_value_module;
                }
            }
        }
        
        return $DATA_MODEL;
    }
    
    function gp_load_options($key)
    {
    	if (DEBUG_MODE < 10)
    	{
    		$this->db->debug = 0;
    	}
        
        $sql    = 'Select C_OPTION_VALUE From t_ps_option Where C_OPTION_KEY = ?';
        $params = array($key);
        $ret = $this->db->getOne($sql, $params);
        
        if (empty($ret))
        {
            $ret = '';
        }
        $this->db->debug = DEBUG_MODE;
        return $ret;
    }
    
    /**
     * Lay danh sach tin bai moi nhat cua chuyen trang
     * @param Int $website_id ID chuyen trang
     * 
     * @return Array Mang danh sach tin bai moi nhat cua chuyen trang
     */
    public function gp_qry_all_latest_article($website_id)
    {
        $website_id = replace_bad_char($website_id);
        $v_limit    = _CONST_DEFAULT_LIMIT_ARTICLE_NEW;
        if($this->is_mssql())
        {
            //LienND update 2013-04-17
            $stmt = 'Select
                            FA.PK_ARTICLE
                          , FA.C_SLUG as C_SLUG_ART
                          , FA.C_TITLE
                          , top10.FK_CATEGORY as PK_CATEGORY
                          , (Select C_SLUG From t_ps_category Where PK_CATEGORY=top10.FK_CATEGORY) as C_SLUG_CAT
                          , ? as FK_WEBSITE
                  From t_ps_article FA
                          Right join (Select
                                    all_id.PK_ARTICLE
                                  , all_id.RN
                                  , all_id.FK_CATEGORY
                                  From (Select
                                                            A.PK_ARTICLE
                                                          , ROW_NUMBER() Over(Order by C_BEGIN_DATE Desc) as RN
                                                          , mca.FK_CATEGORY
                                                  From VIEW_LAST_3DAYS_ACTIVE_ARTICLE A
                                                          Left join (Select
                                                                                            Max(FK_CATEGORY) As FK_CATEGORY
                                                                                          , CA.FK_ARTICLE
                                                                                          From t_ps_category_article as CA
                                                                                          Left join t_ps_category as C
                                                                                           On CA.FK_CATEGORY=C.PK_CATEGORY
                                                                                          Where C.FK_WEBSITE=?
                                                                                          Group by FK_ARTICLE
                                                                                  ) mca
                                                          On mca.FK_ARTICLE=A.PK_ARTICLE
                                          ) all_id
                                  Where all_id.RN>=1
                                  And all_id.RN<=?
                          ) top10
                          On FA.PK_ARTICLE=top10.PK_ARTICLE';
            $arr_param = array($website_id, $website_id, $v_limit);
        }
        else if($this->is_mysql())
        {
            $stmt = "Select
                        A.PK_ARTICLE
                        , A.C_SLUG         as C_SLUG_ART
                        , A.C_TITLE
                        , top_latest.FK_CATEGORY as PK_CATEGORY
                        , (Select C_SLUG From t_ps_category Where PK_CATEGORY = top_latest.FK_CATEGORY) as C_SLUG_CAT
                        , ?                as FK_WEBSITE
                    From t_ps_article A
                        Right Join (SELECT
                                        PK_ARTICLE
                                        , C_DEFAULT_CATEGORY AS FK_CATEGORY
                                      FROM t_ps_article
                                      WHERE C_DEFAULT_WEBSITE = ?
                                          AND (C_BEGIN_DATE <= Now())
                                          AND (C_END_DATE >= Now())
                                          Order By C_BEGIN_DATE DESC
                                       LIMIT 0,?
                                   ) top_latest
                        On A.PK_ARTICLE = top_latest.PK_ARTICLE";
            $arr_param = array($website_id,$website_id, $v_limit);
        }
        
        $arr_all_latest_article_by_website = $this->db->getAll($stmt, $arr_param);
        
        return $arr_all_latest_article_by_website;
    } //end func gp_qry_all_latest_article
    
    public function gp_qry_all_spotlight($v_website_id, $pos_id)
    {
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
            $sql          = "Select S.FK_CATEGORY,
                                   S.FK_ARTICLE,
                                   A.C_SLUG,
                                   A.C_FILE_NAME,
                                   A.C_SUMMARY,
                                   A.C_PEN_NAME ,
                                   C.C_SLUG As C_CAT_SLUG,
                                   C.FK_WEBSITE,
                                   A.C_TITLE
                            From t_ps_spotlight S
                            Inner join t_ps_category_article CA On S.FK_CATEGORY = CA.FK_CATEGORY
                            And S.FK_ARTICLE = CA.FK_ARTICLE
                            Inner join t_ps_category C On C.PK_CATEGORY = S.FK_CATEGORY
                            Inner join t_ps_article A On A.PK_ARTICLE = S.FK_ARTICLE
                            Inner join t_ps_spotlight_position SP On SP.PK_SPOTLIGHT_POSITION = S.FK_SPOTLIGHT_POSITION
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
    
    /**
     * Lay danh sach banner
     * @param Int $website_id ID chuyen trang
     * @param Int $v_category_id ID chuyen muc
     * @return String Ten file anh banner
     */
    public function gp_qry_all_banner($website_id)
    {
        $sql = "Select 
                    B.PK_BANNER
                    ,B.C_DEFAULT
                    ,B.C_FILE_NAME
                    ,BC.FK_CATEGORY
                From t_ps_banner B
                    Right Join t_ps_banner_category BC 
                    On BC.FK_BANNER=B.PK_BANNER
                Where B.FK_WEBSITE=$website_id
                    And B.C_STATUS=1
                Order By C_DEFAULT Desc, BC.FK_CATEGORY Asc";
        
        return $this->db->GetAll($sql);
    }
    
    /**
     * Lay danh sach tin bai doc nhieu nhat cua mot chuyen trang
     * @param unknown $website_id
     */
    public function gp_qry_all_most_view_article($website_id, $qty=10)
    {
        $website_id = replace_bad_char($website_id);
        $stmt = "SELECT
                    FA.PK_ARTICLE
                    , FA.C_TITLE
                    , FA.C_SLUG
                    , MA.FK_CATEGORY AS PK_CATEGORY
                    , FA.C_HAS_PHOTO
                    , FA.C_HAS_VIDEO
                    , DATE_FORMAT(FA.C_BEGIN_DATE,'%Y-%m-%d %H:%i:%s') AS C_BEGIN_DATE
                    , (SELECT
                           C_SLUG
                       FROM t_ps_category
                       WHERE PK_CATEGORY = MA.FK_CATEGORY) AS C_CAT_SLUG
                    , FA.C_FILE_NAME
                FROM t_ps_article FA
                    RIGHT JOIN (SELECT
                                    A.PK_ARTICLE
                                    , A.C_DEFAULT_CATEGORY as FK_CATEGORY
                                FROM t_ps_article A force index(C_VIEWS)
                                    left join t_ps_category C
                                      on C.PK_CATEGORY = A.C_DEFAULT_CATEGORY
                                WHERE A.C_STATUS = 3
                                      and A.C_DEFAULT_WEBSITE = ?
                                      and C.C_STATUS = 1
                                      AND A.C_BEGIN_DATE < Now()
                                      AND A.C_END_DATE > Now()
                                ORDER BY A.C_VIEWS DESC
                                LIMIT ?) MA
                      ON FA.PK_ARTICLE = MA.PK_ARTICLE";
        $params = array($website_id, $qty);
        
        return $this->db->getAll($stmt, $params);
    }
    
    /**
     * Lay danh sach tin dang chu y trong ngay
     * @param Int $website_id ID chuyen trang
     */
    public function gp_qry_all_breaking_news($website_id)
    {
        $website_id = replace_bad_char($website_id);
        $stmt = "Select
                    C.C_SLUG        As CAT_SLUG
                    , A.C_SLUG      As ART_SLUG
                    , A.C_TITLE
                    , S.FK_CATEGORY
                    , S.FK_ARTICLE
                    , S.FK_WEBSITE
                From t_ps_category C
                    Inner Join t_ps_sticky S
                      On C.PK_CATEGORY = S.FK_CATEGORY
                    Inner Join t_ps_article A
                      On A.PK_ARTICLE = S.FK_ARTICLE
                Where S.C_TYPE = 2
                      And S.FK_WEBSITE = $website_id";
        return $this->db->GetAll($stmt);
    }//end func gp_qry_all_breaking_news
    
    
    /**
     * Lay danh sach phong su anh
     * @param Int $website_id ID chuyen trang
     */
    public function gp_qry_all_new_photo_gallery($website_id, $limit=_CONST_DEFAULT_LIMIT_PHOTO_GALLERY)
    {
        $stmt = 'Select
                    PG.PK_PHOTO_GALLERY
                    , PG.C_TITLE
                    , PG.C_SLUG
                    , PG.C_FILE_NAME
                    , PG.C_SUMMARY
                From t_ps_photo_gallery PG
                Where PG.FK_WEBSITE = ?
                      And PG.C_STATUS = 3
                Order by PG.C_BEGIN_DATE Desc
                Limit ?';
        $params = array($website_id, $limit);
        
        return $this->db->getAll($stmt, $params);
    }
    /**
     * Phuc vu cho phan widget
     */
    //public function gp_qry_all_free_text_widget_data($website_id, $them)
} //end class