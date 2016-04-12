<?php

class frontend_Model extends Model
{

    public $v_default_website_id;

    function __construct()
    {
        parent::__construct();
    }

    private function _count_article_by_category($v_category_id)
    {
        $stmt = 'Select Count(*) From t_ps_category_article CA Where FK_CATEGORY = ?';
        $params = array($v_category_id);

        return $this->db->getOne($stmt, $params);
    }

    /*
     * Lay danh sach comment tin bai moi nhat
     */

    public function qry_all_last_comment($v_limit)
    {
        $v_website_id = $this->website_id;
        if (DATABASE_TYPE == 'MYSQL')
        {
            $stmt = "SELECT PK_ARTICLE ,
                            C_SUB_TITLE,
                            C_SLUG,
                            C_TITLE,
                            C_DEFAULT_WEBSITE,
                            C_DEFAULT_CATEGORY,
                            C_FILE_NAME,

                       (SELECT C_SLUG
                        FROM t_ps_category C
                        WHERE C.PK_CATEGORY = A.C_DEFAULT_CATEGORY) AS C_CAT_SLUG,
                            LAS_COMMENT.C_COMMENT
                     FROM t_ps_article A
                     INNER JOIN
                       (SELECT FK_ARTICLE,
                               C_COMMENT
                        FROM t_ps_article_comment
                        ORDER BY  C_INIT_DATE  DESC ) AS LAS_COMMENT ON A.PK_ARTICLE = LAS_COMMENT.FK_ARTICLE
                     AND C_DEFAULT_WEBSITE = ?
                     And DATEDIFF(NOW(),A.C_BEGIN_DATE) >=0
                     And DATEDIFF(NOW(),A.C_END_DATE) <= 0 
                     AND
                       (SELECT COUNT(C.PK_CATEGORY)
                        FROM t_ps_category C
                        WHERE A.C_DEFAULT_CATEGORY = C.PK_CATEGORY
                          AND C.C_STATUS =1)>0
                          LIMIT $v_limit
                        ";
        }
        return $this->db->getAll($stmt, $v_website_id);
    }

    /**
     *  Lay danh sach tin bai co rating cao
     * @param int   $v_website_id ma website
     * @return array() Mang chua nhung tin bai co rating cao nhat
     */
    public function qry_all_rating($limit)
    {
        $v_website_id = $this->website_id;
        if (DATABASE_TYPE == 'MYSQL')
        {
            $stmt = "SELECT A.PK_ARTICLE,
                            A.C_STATUS,
                            A.C_TITLE,
                            C_SLUG,
                            A.C_FILE_NAME,
                            A.C_SUMMARY,
                            C_SUB_TITLE,
                            C_DEFAULT_CATEGORY,
                            (select C_SLUG from t_ps_category C where C.PK_CATEGORY = A.C_DEFAULT_CATEGORY) as C_CAT_SLUG,
                            RT.C_RATING
                     FROM t_ps_article A
                     INNER JOIN
                       (SELECT AR.FK_ARTICLE,
                               ROUND(SUM(AR.C_RATING)/COUNT(AR.PK_ARTICLE_RATING))AS C_RATING
                        FROM t_ps_article_rating AR
                        GROUP BY AR.FK_ARTICLE) AS RT
                     WHERE A.PK_ARTICLE = RT.FK_ARTICLE
                       AND A.C_STATUS = 3
                       And DATEDIFF(NOW(),A.C_BEGIN_DATE) >=0
                       And DATEDIFF(NOW(),A.C_END_DATE) <= 0 
                       AND A.C_DEFAULT_WEBSITE = ?
                       AND A.C_DEFAULT_CATEGORY in(SELECT PK_CATEGORY
                                                    FROM t_ps_category
                                                    WHERE C_STATUS = 1
                                                      AND FK_WEBSITE =?)
                         
                     ORDER BY RT.C_RATING DESC
                     LIMIT $limit";
        }
        return $this->db->getAll($stmt, array($v_website_id, $v_website_id));
    }

    //end function

    /**
     * Lấy hướng dẫn nộp hồ sơ trực tuyến
     * @return type
     */
    public function qry_guidance_internet()
    {
        $sql = "SELECT *
                    FROM t_cores_list
                    WHERE FK_LISTTYPE = (SELECT
                                           PK_LISTTYPE
                                         FROM t_cores_listtype
                                         WHERE C_CODE = '" . _CONST_HUONG_DAN_NOP_HS . "')
                        AND C_STATUS = 1
                    ORDER BY C_ORDER
                    LIMIT 1";

        return $this->db->getRow($sql);
    }

    /**
     * lay danh sach tat ca don vi
     * @return type
     */
    public function qry_all_member_receive_record()
    {
        $sql = "SELECT
                M.PK_MEMBER,
                M.C_NAME
              FROM t_ps_member M
                inner JOIN (SELECT DISTINCT
                              FK_MEMBER
                            FROM t_ps_record_type_member) RTM
                  ON M.PK_MEMBER = RTM.FK_MEMBER";
        return $this->db->GetAll($sql);
    }

    /**
     * lay tat ca don vi tiep nhan hs truc tuyen co phan cap theo level
     * @return type
     */
    public function qry_all_member_receive_record_have_level()
    {
        $sql = "SELECT DISTINCT M.PK_MEMBER,
                                M.C_NAME,
                                M.C_SHORT_CODE 
                        FROM t_ps_member M
                          INNER JOIN t_ps_record_type_member RTM
                            ON M.PK_MEMBER = RTM.FK_MEMBER
                        WHERE (M.FK_MEMBER < 1
                                OR M.FK_MEMBER IS NULL)
                            AND M.C_STATUS = 1";
        $MODEL_DATA['arr_all_district'] = $this->db->getAll($sql);

        $sql = "SELECT DISTINCT M.PK_MEMBER,
                                M.C_NAME,
                                M.FK_MEMBER,
                                M.C_SHORT_CODE 
                        FROM t_ps_member M
                          INNER JOIN t_ps_record_type_member RTM
                            ON M.PK_MEMBER = RTM.FK_MEMBER
                        WHERE M.FK_MEMBER > 0 AND M.C_STATUS = 1";

        $MODEL_DATA['arr_all_village'] = $this->db->getAll($sql);

        return $MODEL_DATA;
    }

    /**
     * lay danh sach tat ca duong link bang theo doi truc tuyen cua cac don vi
     * @return type
     */
    public function qry_all_live_board()
    {
        $sql = "SELECT
                C_NAME,
                C_CODE,
                ExtractValue(C_XML_DATA,'//item[@id=\"txt_liveboard\"]/value') AS C_LIVEBOARD_LINK 
              FROM t_ps_member
              WHERE C_STATUS = 1 AND C_SCOPE IN (0,1) 
              ORDER BY PK_MEMBER,FK_MEMBER";
        return $this->db->getAll($sql);
    }

    /**
     * lay danh sach tthc
     * @param type $v_member_id
     * @param type $v_spec_code
     * @return type
     */
    public function qry_all_record_type_of_member($v_member_id, $v_spec_code = '')
    {
        $condition_member = '';
        $condition_spec   = '';
        $v_member_id = intval($v_member_id);
        if ($v_member_id > 0)
        {
            $condition_member .= " And FK_MEMBER = '$v_member_id' ";
        }
        if ($v_spec_code != '')
        {
            $condition_spec .= " AND RT.C_SPEC_CODE = '$v_spec_code' ";
        }

        $stmt = "SELECT
                    RT.PK_RECORD_TYPE,
                    RT.C_CODE,
                    RT.C_NAME,
                    RT.C_SPEC_CODE,
                    (Select C_NAME From t_cores_list Where C_CODE = RT.C_SPEC_CODE) As C_SPEC_NAME 
                  FROM (SELECT DISTINCT FK_RECORD_TYPE FROM t_ps_record_type_member WHERE (1 > 0) $condition_member) RTM
                    LEFT JOIN t_ps_record_type RT
                      ON RTM.FK_RECORD_TYPE = RT.PK_RECORD_type
                  WHERE (1 > 0) $condition_spec
                  ORDER BY C_SPEC_CODE";
        return $this->db->getAll($stmt);
    }

    /**
     * lấy thông tin TTHC
     * @param type $record_type_id
     * @return type
     */
    public function qry_record_type($record_type_id)
    {
        if (!is_numeric($record_type_id) or $record_type_id < 1)
        {
            return array();
        }
        $stmt = "SELECT
                    (SELECT
                       C_NAME
                     FROM t_cores_list
                     WHERE C_CODE = RT.C_SPEC_CODE) AS C_SPEC_NAME,
                    RT.*
                  FROM t_ps_record_type RT
                  WHERE PK_RECORD_TYPE = ?";
        return $this->db->getRow($stmt, array($record_type_id));
    }

    /**
     * lay danh muc linh vuc
     * @return type
     */
    public function qry_all_spec()
    {
        $sql = "SELECT C_CODE,C_NAME
                FROM t_cores_list
                WHERE FK_LISTTYPE = (SELECT
                                       PK_LISTTYPE
                                     FROM t_cores_listtype
                                     WHERE C_CODE = '" . _CONST_LINH_VUC_TTHC . "')";
        return $this->db->GetAssoc($sql);
    }

    /**
     * thuc hien them hs internet
     * @return string
     */
    public function do_insert_internet_record()
    {
        //thong tin 
        $v_member_id = get_post_var('hdn_member_id', 0);
        $v_record_type_id = get_post_var('hdn_record_type_id', 0);
        $v_record_type_code = get_post_var('hdn_record_type_code', '');
//        $v_record_no = get_post_var('txt_record_no', '');
        $v_record_no = '';
                
        $v_name = get_post_var('txt_name', '');
        $v_phone = get_post_var('txt_phone', '');
        $v_email = get_post_var('txt_email', '');
        $v_address = get_post_var('txt_address', '');
        $v_note = get_post_var('txt_note', '');
        $v_citizen_id = 0;
        if ($this->check_block_account() == 1)
        {
            $v_citizen_id = Session::get('citizen_login_id');
            $arr_single_citizen = $this->qry_single_account_citizen();
            $v_email = $arr_single_citizen['C_EMAIL'];
            $v_account_xml = $arr_single_citizen['C_XML_DATA'];
            $v_organ = $arr_single_citizen['C_ORGAN'];
            @$dom = simplexml_load_string($v_account_xml, 'SimpleXMLElement', LIBXML_NOCDATA);
            if (@$dom)
            {
                if ($v_organ == 0)
                {
                    $obj_value = $dom->xpath('//item');
                    $v_phone = isset($obj_value[0]->tel) ? (string) $obj_value[0]->tel : '';
                    $v_name = isset($obj_value[0]->name) ? (string) $obj_value[0]->name : '';
                    $v_address = isset($obj_value[0]->address) ? (string) $obj_value[0]->address : '';
                }
                else
                {
                    $v_phone = isset($obj_value[0]->tel) ? (string) $obj_value[0]->tel : '';
                    $v_name = isset($obj_value[0]->name) ? (string) $obj_value[0]->name : '';
                    $v_address = isset($obj_value[0]->address) ? (string) $obj_value[0]->address : '';
                }
            }
        }
        //validate
        $response = array();
        $response['success'] = false;
        $response['message'] = array();
        $v_challenge = get_post_var('recaptcha_challenge_field');
        $v_response = get_post_var('recaptcha_response_field');
        $resp = recaptcha_check_answer(_CONST_RECAPCHA_PRIVATE_KEY, $_SERVER["REMOTE_ADDR"], $v_challenge, $v_response);
        //save field
        $response['field'] = array('hdn_member_id'        => $v_member_id, 'hdn_record_type_id'   => $v_record_type_id,
            'hdn_record_type_code' => $v_record_type_code, 
            'txt_record_no'        => NULL, //$v_record_no,
            'txt_name'             => $v_name, 'txt_phone'            => $v_phone,
            'txt_email'            => $v_email, 'txt_note'             => $v_note,
            'txt_address'          => $v_address
        );
        $v_count_file = count($_FILES['uploader']['name']);
        //capcha
        if (!$resp->is_valid)
        {
            $response['message']['recapcha'] = 'Bạn chưa nhập mã xác nhận hoặc mã xác nhận chưa đúng!';
        }

        //file
        if (!$v_count_file or empty($_FILES['uploader']['name'][0]))
        {
            $response['message']['uploader'] = 'Hồ sơ này yêu cầu File đính kèm!';
        }
        //member id
        if ($v_member_id == 0)
        {
            $response['message']['hdn_member_id'] = 'Bạn phải chọn đơn vị tiếp nhận!';
        }
        //record type
        if ($v_record_type_id == 0)
        {
            $response['message']['hdn_record_type_id'] = 'Mã TTHC không hợp lệ!';
        }
        //record type code
        if ($v_record_type_code == '')
        {
            $response['message']['hdn_record_type_code'] = 'Mã TTHC không hợp lệ!';
        }
        //reocrd no
//        if ($v_record_no == '')
//        {
//            $response['message']['txt_record_no'] = 'Mã hồ sơ không hợp lệ!';
//        }
        //name
        if ($v_name == '')
        {
            $response['message']['txt_name'] = 'Bạn phải nhập họ tên!';
        }
        //phone
        if ($v_phone == '')
        {
            $response['message']['txt_phone'] = 'Bạn phải nhập số điện thoại!';
        }
        //email
        if ($v_email == '')
        {
            $response['message']['txt_email'] = 'Bạn phải nhập email!';
        }
        //file size
        for ($i = 0; $i < $v_count_file; $i++)
        {
            //kiem tra upload loi
            if ($_FILES['uploader']['error'][$i] == 0)
            {
                //lay thong tin file
                $v_file_name = $_FILES['uploader']['name'][$i];
                $v_tmp_name = $_FILES['uploader']['tmp_name'][$i];
                $v_size = $_FILES['uploader']['size'][$i] / 1048576;

                $v_file_ext = strtolower(array_pop(explode('.', $v_file_name)));

                if (in_array($v_file_ext, explode(',', trim(_CONST_TYPE_FILE_ACCEPT))))
                {
                    if ($v_size > _CONST_LIMIT_FILE_SIZE)
                    {
                        $response['message']['uploader'] = 'Dung lượng file vượt quá giới hạn!';
                    }
                }
                else
                {
                    $response['message']['uploader'] = 'Định dạng file không hợp quy định!';
                }
            }
        }

        //return message neu du lieu ko phu hop
        if (count($response['message']) > 0)
        {
            return $response;
        }

        //thuc hien insert
        $stmt = "INSERT INTO t_ps_record(FK_RECORD_TYPE,
                                    C_RECORD_NO,C_SUBMITTED_DATE,
                                    C_RETURN_PHONE_NUMBER,C_RETURN_EMAIL,
                                    C_NOTE,C_CITIZEN_NAME,FK_VILLAGE_ID,C_CITIZEN_ADDRESS,FK_CITIZEN,C_STATUS)
                            VALUES
                            (?,?,NOW(),?,?,?,?,?,?,?,0)
                            ";
        $this->db->Execute($stmt, array($v_record_type_id, $v_record_no, $v_phone, $v_email, $v_note, $v_name, $v_member_id, $v_address, $v_citizen_id));

        if ($this->db->Affected_Rows() > 0)
        {
            $record_type_name = $this->db->GetOne("select C_NAME from t_ps_record_type where PK_RECORD_TYPE='$v_record_type_id'");
            $response = array('success'    =>true,
                                'record_type_name'  => $record_type_name, 
                                'txt_email' => $v_email
                                );
            $v_record_id = $this->db->GetOne('SELECT MAX(PK_RECORD ) FROM t_ps_record');
            //File dinh kem
            for ($i = 0; $i < $v_count_file; $i++)
            {
                if ($_FILES['uploader']['error'][$i] == 0)
                {
                    $v_file_name = $_FILES['uploader']['name'][$i];
                    $v_tmp_name = $_FILES['uploader']['tmp_name'][$i];

                    $v_file_ext = strtolower(array_pop(explode('.', $v_file_name)));

                    if (in_array($v_file_ext, explode(',', _CONST_TYPE_FILE_ACCEPT)))
                    {
                        $v_new_file_name = uniqid() . '.' . $v_file_ext;
                        if (move_uploaded_file($v_tmp_name, _CONST_RECORD_FILE . DS . $v_new_file_name))
                        {
                            $stmt = 'Insert Into t_ps_record_file(FK_RECORD,C_NAME, C_FILE_NAME) Values(?,?,?)';
                            $params = array($v_record_id, $v_file_name, $v_new_file_name);
                            $this->db->Execute($stmt, $params);
                        }
                    }
                }
            }
        }
        else
        {
            $response['system_error'] = 'Hệ thống xảy ra sự cố, bạn vui lòng nhập lại thông tin!';
        }
        return $response;
    }

    public function qry_all_image_news($limit)
    {
        $v_website = $this->website_id;

        $sql = "select
                    A.PK_ARTICLE,
                    A.C_TITLE,
                    A.C_SUB_TITLE,
                    A.C_SUMMARY,
                    A.C_CONTENT,
                    A.C_SLUG,
                    A.C_FILE_NAME,
                    (Select
                       C_SLUG
                     From t_ps_category
                     Where PK_CATEGORY = A.C_DEFAULT_CATEGORY) as C_SLUG_CATEGORY,
                    A.C_DEFAULT_CATEGORY AS PK_CATEGORY
                  from t_ps_article A
                  where C_IS_IMG_NEWS = 1
                      and A.C_STATUS = 3
                      and A.C_DEFAULT_WEBSITE = $v_website
                      And DATEDIFF(NOW(),A.C_BEGIN_DATE) >= 0
                      And DATEDIFF(NOW(),A.C_END_DATE) <= 0
                      and (Select
                             C_STATUS
                           From t_ps_category
                           Where PK_CATEGORY = A.C_DEFAULT_CATEGORY) = 1
                  order by A.C_BEGIN_DATE
                  LIMIT $limit";

        return $this->db->getAll($sql);
    }

    /**
     * Lay danh sach tat ca cac chuyen trang dang hoat dong (active)
     */
    public function qry_all_active_website()
    {
        $sql = 'Select 
                    W.PK_WEBSITE
                    ,W.C_CODE
                    ,W.C_THEME_CODE
                    ,W.FK_USER
                    ,W.C_NAME
                    ,L.C_CODE as C_LANG_CODE
                From t_ps_website W
                    Left Join t_cores_list L
                    On W.FK_LANG=L.PK_LIST
                Where W.C_STATUS=1
                Order By W.C_ORDER';
        return $this->db->getAll($sql);
    }

    public function qry_default_website_id()
    {
        $sql = "Select PK_WEBSITE From t_ps_website Order By C_ORDER";
        return $this->db->GetOne($sql);
    }

    function qry_all_events_by_article($article_id)
    {
        $article_id = (int) $article_id;
        $website_id = $this->website_id;

        if (DATABASE_TYPE == 'MSSQL')
        {
            $sql = "
                Select E.PK_EVENT, E.C_NAME, E.C_BEGIN_DATE, E.C_END_DATE, E.C_SLUG
                From t_ps_event E
                Inner Join t_ps_event_article EA
                On E.PK_EVENT = EA.FK_EVENT
                Where E.FK_WEBSITE = $website_id
                And E.C_STATUS = 1
                And dateDiff(dd, E.C_BEGIN_DATE, getDate()) >= 0
                And dateDiff(dd, getDate(), E.C_END_DATE) >=0
                And EA.FK_ARTICLE = $article_id
            ";
        }
        else if (DATABASE_TYPE == 'MYSQL')
        {
            $sql = "
                Select E.PK_EVENT, E.C_NAME, E.C_BEGIN_DATE, E.C_END_DATE, E.C_SLUG
                From t_ps_event E
                Inner Join t_ps_event_article EA
                On E.PK_EVENT = EA.FK_EVENT
                Where E.FK_WEBSITE = $website_id
                And E.C_STATUS = 1
                And NOW() >= E.C_BEGIN_DATE
                And E.C_END_DATE >= NOW()
                And EA.FK_ARTICLE = $article_id
            ";
        }

        return $this->db->getAll($sql);
    }

    //lay danh sach tin moi nhat cung chuyen muc voi tin bai dang xem chi tiet
    function qry_new_category_article($category_id, $article_id)
    {
        $limit = _CONST_DEFAULT_LIMIT_ARTICLE_NEW;
        $website_id = $this->website_id;

        if (DATABASE_TYPE == 'MSSQL')
        {
            $sql = "
                Select Top $limit A.PK_ARTICLE, A.C_SLUG, A.C_TITLE
                    , COnvert(varchar, A.C_BEGIN_DATE, 103) As C_BEGIN_DATE
                    , C.PK_CATEGORY, C.C_SLUG as C_CAT_SLUG
                From t_ps_article A
                Inner Join t_ps_category_article CA
                On CA.FK_ARTICLE = A.PK_ARTICLE
                Inner Join t_ps_category C
                On C.PK_CATEGORY = CA.FK_CATEGORY
                Where C.C_STATUS = 1
                And C.FK_WEBSITE = $website_id
                And A.C_STATUS = 3
                And dateDiff(dd, A.C_BEGIN_DATE, getDate()) >= 0
                And dateDiff(dd, A.C_END_DATE, getdate()) <= 0
                And C.PK_CATEGORY = $category_id
                And A.PK_ARTICLE <> $article_id
                Order by A.C_BEGIN_DATE Desc
            ";
        }
        else if (DATABASE_TYPE == 'MYSQL')
        {
            $v_use_index = '';
            $v_count = $this->_count_article_by_category($category_id);
            if ($v_count > _CONST_MIN_ROW_TO_MYSQL_USE_INDEX)
            {
                $v_use_index = ' Use Index (C_BEGIN_DATE) ';
            }

            $sql = "Select
                        A.PK_ARTICLE
                        , A.C_SLUG
                        , A.C_TITLE
                        , DATE_FORMAT(A.C_BEGIN_DATE,'%d-%m-%Y') As C_BEGIN_DATE
                        , C.PK_CATEGORY
                        , C.C_SLUG      as C_CAT_SLUG
                    From t_ps_category C
                        right join t_ps_category_article CA
                          On C.PK_CATEGORY = CA.FK_CATEGORY
                        left join t_ps_article A $v_use_index
                          On CA.FK_ARTICLE = A.PK_ARTICLE 
                    Where C.C_STATUS = 1
                          And C.FK_WEBSITE = $website_id
                          And A.C_STATUS = 3
                          And A.C_BEGIN_DATE < Now()
                          And A.C_END_DATE > Now()
                          And C.PK_CATEGORY = $category_id      
                          And A.PK_ARTICLE <> $article_id 
                    Order by A.C_BEGIN_DATE Desc
                    Limit $limit";
        }
        return $this->db->getAll($sql);
    }

    /**
     * qry tat ca tin bai lien quan
     * @param type $article_id 
     * @return array article same events and tags
     */
    function qry_related_article($article_id, $v_tags)
    {
        $article_id = (int) $article_id;
        $limit = _CONST_DEFAULT_ROWS_OTHER_NEWS;
        $website = $this->website_id;

        //xu ly tag
        $fulltext_seach_cond = '';
        if ($v_tags)
        {
            $v_tags = preg_replace('/,( +)/', ',', $v_tags);
            $v_tags = explode(',', $v_tags);
            for ($i = 0; $i < count($v_tags); $i++)
            {
                $v_tags[$i] = '"' . preg_replace('/( +)/', ' ', trim($v_tags[$i], ' ')) . '"';
            }

            if (DATABASE_TYPE == 'MSSQL')
            {
                $fulltext_seach_cond = "And Contains(A.C_TAGS , '" . implode(' Or ', $v_tags) . "')";
            }
            else if (DATABASE_TYPE == 'MYSQL')
            {
                foreach ($v_tags as $tag)
                {
                    if (!isset($fulltext_seach_cond) || $fulltext_seach_cond == '' || $fulltext_seach_cond == NULL)
                    {
                        $fulltext_seach_cond = " And MATCH (C_CONTENT) AGAINST ('$tag')";
                    }
                    else
                    {
                        $fulltext_seach_cond .= " OR MATCH (C_CONTENT) AGAINST ('$tag')";
                    }
                }
            }
        }

        //lay danh sach event
        $arr_events = $this->qry_all_events_by_article($article_id);
        $eventid_list = '0';
        $count_events = count($arr_events);
        for ($i = 0; $i < $count_events; $i++)
        {
            $eventid_list .= ',' . $arr_events[$i]['PK_EVENT'];
        }


        if (DATABASE_TYPE == 'MSSQL')
        {
            //LienND tuning 2013-04-25
            $sql = "Select
                            RA.PK_ARTICLE
                            ,RA.C_SLUG 
                            , Convert(varchar, RA.C_BEGIN_DATE, 103) as C_BEGIN_DATE
                            ,RA.C_TITLE
                            ,C.PK_CATEGORY
                            ,C.C_SLUG as C_CAT_SLUG
                        From (";
            //Tin bai cung tag
            if ($fulltext_seach_cond != '')
            {
                $sql .= "Select 
                            A.PK_ARTICLE
                            ,A.C_SLUG 
                            ,A.C_BEGIN_DATE
                            ,A.C_TITLE
                            ,A.C_DEFAULT_CATEGORY
                        From t_ps_article A
                        Where A.C_STATUS = 3    
                            And DateDiff(dd, A.C_BEGIN_DATE, getDate()) >=0
                            And DateDiff(dd, A.C_END_DATE, getDate()) <= 0 
                            $fulltext_seach_cond                            
                        UNION ";
            }

            //Tin bai cung su kien
            $sql .= "Select 
                             A.PK_ARTICLE
                            ,A.C_SLUG 
                            ,A.C_BEGIN_DATE
                            ,A.C_TITLE
                            ,A.C_DEFAULT_CATEGORY
                        From t_ps_event_article EA 
                            Left Join t_ps_article A
                            On EA.FK_ARTICLE=A.PK_ARTICLE
                        Where EA.FK_EVENT in ($eventid_list)
                            And A.C_STATUS = 3    
                            And DateDiff(dd, A.C_BEGIN_DATE, getDate()) >=0
                            And DateDiff(dd, A.C_END_DATE, getDate()) <= 0";
            $sql .= ") ra 
                    Left Join t_ps_category C
                    On ra.C_DEFAULT_CATEGORY=C.PK_CATEGORY
                Order By ra.C_BEGIN_DATE";
        }
        else if (DATABASE_TYPE == 'MYSQL')
        {
            //LienND tuning 2013-04-25
            $sql = "Select
                            RA.PK_ARTICLE
                            ,RA.C_SLUG 
                            , DATE_FORMAT(RA.C_BEGIN_DATE,'%d-%m-%Y') as C_BEGIN_DATE
                            ,RA.C_TITLE
                            ,C.PK_CATEGORY
                            ,C.C_SLUG as C_CAT_SLUG
                        From (";
            //Tin bai cung tag
            if ($fulltext_seach_cond != '')
            {
                $sql .= "Select 
                            A.PK_ARTICLE
                            ,A.C_SLUG 
                            ,A.C_BEGIN_DATE
                            ,A.C_TITLE
                            ,A.C_DEFAULT_CATEGORY
                        From t_ps_article A
                        Where A.C_STATUS = 3    
                            And DATEDIFF(NOW(),A.C_BEGIN_DATE) >=0
                            And DATEDIFF(NOW(),A.C_END_DATE) <= 0 
                            $fulltext_seach_cond                            
                        UNION ";
            }

            //Tin bai cung su kien
            $sql .= "Select 
                             A.PK_ARTICLE
                            ,A.C_SLUG 
                            ,A.C_BEGIN_DATE
                            ,A.C_TITLE
                            ,A.C_DEFAULT_CATEGORY
                        From t_ps_event_article EA 
                            Left Join t_ps_article A
                            On EA.FK_ARTICLE=A.PK_ARTICLE
                        Where EA.FK_EVENT in ($eventid_list)
                            And A.C_STATUS = 3    
                            And DATEDIFF(NOW(),A.C_BEGIN_DATE) >=0
                            And DATEDIFF(NOW(),A.C_END_DATE) <= 0";
            $sql .= ") RA 
                    Left Join t_ps_category C
                    On RA.C_DEFAULT_CATEGORY=C.PK_CATEGORY
                Order By RA.C_BEGIN_DATE";
        }


        return $this->db->getAll($sql);

        /*
          $sql = "
          Select Top $limit A.PK_ARTICLE, A.C_TITLE, A.C_SLUG
          , Convert(varchar, A.C_BEGIN_DATE, 103) as C_BEGIN_DATE
          , C.PK_CATEGORY, C.C_SLUG as C_CAT_SLUG
          From t_ps_article A
          Inner Join (
          Select Max(FK_CATEGORY) as FK_CATEGORY, FK_ARTICLE
          From t_ps_category_article Group By FK_ARTICLE
          ) CA
          On A.PK_ARTICLE = CA.FK_ARTICLE
          Inner Join t_ps_category C
          On CA.FK_CATEGORY = C.PK_CATEGORY
          Left Join t_ps_event_article EA
          On EA.FK_ARTICLE = A.PK_ARTICLE
          And EA.FK_CATEGORY = C.PK_CATEGORY
          Left Join t_ps_event E
          ON EA.FK_EVENT = E.PK_EVENT
          Where C.FK_WEBSITE = $website
          And A.C_STATUS = 3
          And C.C_STATUS = 1
          And DateDiff(dd, A.C_BEGIN_DATE, getDate()) >=0
          And DateDiff(dd, A.C_END_DATE, getDate()) <= 0
          And(
          E.PK_EVENT In($eventid_list)
          $fulltext_seach_cond
          )
          Order By C_BEGIN_DATE Desc
          ";
          return $this->db->getAll($sql);
         */
    }

    /**
     * 
     * @param type $article_id
     * @param type $rate_value
     * @return Kết quả rating tổng kết của tin bài đó
     */
    function rate_article($article_id, $rate_value)
    {
        $this->db->debug = 0;
        $client_ip = ip2long($_SERVER['REMOTE_ADDR']);
        $website_id = $this->website_id;

        if (DATABASE_TYPE == 'MSSQL')
        {
            $sql = "
                Insert Into t_ps_article_rating(FK_ARTICLE, C_IP, C_RATING)
                Select Top 1 CA.FK_ARTICLE, $client_ip, $rate_value
                From t_ps_category_article CA
                Inner Join t_ps_category C
                On C.PK_CATEGORY = CA.FK_CATEGORY
                Where C.FK_WEBSITE = $website_id
                And CA.FK_ARTICLE = $article_id
            ";
        }
        else if (DATABASE_TYPE == 'MYSQL')
        {
            $sql_website = "Select C_DEFAULT_WEBSITE From t_ps_article Where PK_ARTICLE = $article_id";
            $v_default_ps_site = $this->db->getOne($sql_website);
//            echo __FILE__;
//            var_dump::display($v_default_ps_site);
//            echo 'on line: ' . __LINE__;

            if ($v_default_ps_site == $website_id)
            {
                $sql = "
                                Insert Into t_ps_article_rating 
                                (FK_ARTICLE, C_IP, C_RATING)
                                Values($article_id,$client_ip,$rate_value)";
            }
        }

        $this->db->execute($sql);
        $affected_rows = $this->db->Affected_Rows();

        //tinh toan lai cache ket qua rate
        if ($affected_rows)
        {
            //lay ket qua

            if (DATABASE_TYPE == 'MSSQL')
            {
                $sql = "
                    Select Cast(Sum(C_RATING) As Numeric(10,1)) / Cast(Count(*) As Numeric(10,1)) As C_RESULT From t_ps_article_rating
                    Where FK_ARTICLE = $article_id
                ";
            }
            else if (DATABASE_TYPE == 'MYSQL')
            {
                $sql = "
                                Select ROUND(( SUM(C_RATING)  / COUNT(*))) As C_RESULT 
                                From t_ps_article_rating
                               Where FK_ARTICLE = $article_id
                ";
            }

            $result_value = (double) $this->db->getOne($sql);
            $sql = "
                Update t_ps_article 
                Set C_CACHED_RATING = $result_value
                    ,C_CACHED_RATING_COUNT = (Select Count(*) From t_ps_article_rating Where FK_ARTICLE = $article_id)
                Where PK_ARTICLE = $article_id
            ";
            $this->db->Execute($sql);
        }
        return $result_value;
    }

    /**
     * 
     * @param type $id
     * @return type 0: đã xác thực, không tồn tại hoặc quá 3 ngày
     * @return 1: chưa xác thực, trong vòng 3 ngày
     */
    public function qry_single_subscriber($id)
    {
        $id = (int) $id;
        $website = $this->website_id;
        if (DATABASE_TYPE == 'MSSQL')
        {
            $sql = "
                    Select C_AUTH
                    From t_ps_subscriber 
                    Where PK_SUBSCRIBER = $id
                    And DateDiff(dd, C_DATE, getDate()) <= 3
                    And FK_WEBSITE = $website
                ";
        }
        else if (DATABASE_TYPE == 'MYSQL')
        {
            $sql = "
                    Select C_AUTH
                    From t_ps_subscriber 
                    Where PK_SUBSCRIBER = $id
                    And DATEDIFF(NOW(),C_DATE) <= 3
                    And FK_WEBSITE = $website
                ";
        }

        return $this->db->getRow($sql);
    }

    function qry_all_weblink($group_weblink_id = 0)
    {
        $v_condition = "";
        if (intval($group_weblink_id) > 0)
        {
            $v_condition = " And W.FK_TYPE = '$group_weblink_id' ";
        }
        $website = $this->website_id;
        if (DATABASE_TYPE == 'MSSQL')
        {
            $sql = "
                Select W.C_URL, W.C_NAME, W.C_FILE_NAME, W.C_NEW_WINDOWN
                From t_ps_weblink W
                Where dateDiff(dd, C_BEGIN_DATE, getDate()) >= 0
                And dateDiff(dd, getDate(), C_END_DATE) >= 0
                And W.C_STATUS = 1
                And W.FK_WEBSITE = $website
                $v_condition
                Order By W.C_ORDER
            ";
        }
        else if (DATABASE_TYPE == 'MYSQL')
        {
            $sql = "Select
                            W.C_URL,
                            W.C_NAME,
                            W.C_FILE_NAME,
                            W.C_NEW_WINDOWN,
                            W.FK_TYPE,
                            (Select
                               C_NAME
                             From t_cores_list
                             Where PK_LIST = W.FK_TYPE) as C_TYPE_NAME
                          From t_ps_weblink W
                          WHERE C_BEGIN_DATE <= NOW()
                              AND C_END_DATE >= NOW()
                              And W.C_STATUS = 1
                              And W.FK_WEBSITE = $website
                              $v_condition
                        Order By W.FK_TYPE,W.C_ORDER";
        }

        return $this->db->getAll($sql);
    }

    /**
     * tao moi email dang ky tin thu
     * @return boolean
     */
    public function insert_subscribe()
    {
        //lay du lieu
        $website = $this->website_id;
        $v_name = get_post_var('txt_name');
        $v_email = get_post_var('txt_email');
        $v_date = date('Y-m-d H:i:s');

        $lang_confirm = __('Please Confirm Subscription');
        $lang_subscribe = __('Yes, subscribe me to this list');
        $lang_key = __('register key');
        $key = md5(uniqid());


        //validate
        $v_email = filter_var($v_email, FILTER_SANITIZE_EMAIL);

        if (filter_var($v_email, FILTER_VALIDATE_EMAIL) == false)
        {
            return false;
        }

        //verify
        if (DATABASE_TYPE == 'MSSQL')
        {
            $sql = "
                    Select COUNT(*) 
                    From t_ps_subscriber 
                    Where C_EMAIL = '$v_email' 
                    And (
                                C_AUTH > 0 
                                Or dateDiff(dd, C_DATE, getDate()) <=3
                        )
                    And FK_WEBSITE = $website
                ";
        }
        else if (DATABASE_TYPE == 'MYSQL')
        {
            $sql = "
                    Select COUNT(*) 
                    From t_ps_subscriber 
                    Where C_EMAIL = '$v_email' 
                    And (
                                C_AUTH > 0 
                                Or DATEDIFF(NOW(),C_DATE) <= 3
                        )
                    And FK_WEBSITE = $website
                ";
        }

        //neu da dang ky roi
        $result = $this->db->getOne($sql);
        if ($result > 0)
        {
            return false;
        }

        //execute
        $sql = "
            Insert Into t_ps_subscriber(C_NAME, C_EMAIL, C_DATE, C_AUTH, FK_WEBSITE, C_ACTIVATE)
            Values(?,?,?,?,?,?)
        ";
        $param = array(
            $v_name,
            $v_email,
            $v_date,
            0,
            $website,
            $key
        );
        $this->db->Execute($sql, $param);


        //tra ve tinh trang xu ly
        if ($this->db->errorNo() == 0)
        {

            if (DATABASE_TYPE == 'MSSQL')
            {
                $sql = "Select IDENT_CURRENT('t_ps_subscriber')";
            }
            else if (DATABASE_TYPE == 'MYSQL')
            {
                $sql = "Select PK_SUBSCRIBER From t_ps_subscriber Order By PK_SUBSCRIBER Desc ";
            }

            $id = $this->db->getOne($sql);

            //gui thu xac nhan
            require_once SERVER_ROOT . 'libs/swift/lib/swift_required.php';
            $server_name = get_system_config_value(CFGKEY_MAIL_SERVER);
            $port = get_system_config_value(CFGKEY_MAIL_PORT);
            $ssl = get_system_config_value(CFGKEY_MAIL_SSL) == 'true' ? 'ssl' : null;
            $transport = Swift_SmtpTransport::newInstance($server_name, $port, $ssl);
            $transport->setUsername(get_system_config_value(CFGKEY_MAIL_ACCOUNT));
            $transport->setPassword(get_system_config_value(CFGKEY_MAIL_PASSWORD));
            $v_subject = get_system_config_value(CFGKEY_UNIT_NAME) . ' - ' . 'auth email';

            // Tạo đối tượng mailer sẽ đãm nhận nhiệm vụ gởi mail đi
            $mailer = Swift_Mailer::newInstance($transport);
            $url = SITE_ROOT . "index.php?url=frontend/frontend/dsp_auth_subscribe/$id&key=$key";

            $mail_content = "
                <h3>$lang_confirm</h3>
                <div><a href='$url'>$lang_subscribe</a></div>
                <div>Email: $v_email</div>
                <div>$lang_key: $key</div>
            ";

            //Tạo message để gởi đi
            $message = Swift_Message::newInstance($v_subject);

            $message->addPart($mail_content, 'text/html');
            $message->setFrom(get_system_config_value(CFGKEY_MAIL_ADD));
            $message->addTo($v_email);

            // Gởi message
            $mailer->send($message);

            return $id;
        }
        else
        {
            return -1; //loi xay ra
        }
    }

    /**
     * tao moi email dang ky tin thu
     * @return boolean
     */
    public function delete_subscriber()
    {
        $website = $this->website_id;

        $v_email = get_post_var('txt_email');
        $v_subscribe_id = get_post_var('hdn_item_id', '0');
        $v_key_confirm = get_post_var('txt_code', '');

        $lang_confirm = __('Please Confirm Subscription');
        $lang_key = __('register key');


        //neu co id se xoa
        if ($v_subscribe_id != '0' && $v_key_confirm != '')
        {
            $stmt = "Select count(PK_SUBSCRIBER) From  t_ps_subscriber Where PK_SUBSCRIBER = ? and C_ACTIVATE = ?";
            $v_check = $this->db->getOne($stmt, array($v_subscribe_id, $v_key_confirm));
            if ($v_check == 1)
            {
                $stmt = "Delete From t_ps_subscriber
                    Where PK_SUBSCRIBER = ? And C_ACTIVATE = ?";
                $this->db->Execute($stmt, array($v_subscribe_id, $v_key_confirm));

                //hoan thanh huy dang ky
                return -2;
            }
            else
            {
                //ma xac nhan khong dung
                return $v_subscribe_id;
            }
        }

        //gui mail va ma xac nhan
        //validate
        $v_email = filter_var($v_email, FILTER_SANITIZE_EMAIL);

        if (filter_var($v_email, FILTER_VALIDATE_EMAIL) == false)
        {
            return false;
        }
        //lay key cua eamil
        $sql = "Select PK_SUBSCRIBER,C_ACTIVATE From t_ps_subscriber Where C_EMAIL = '$v_email'";
        $result = $this->db->getRow($sql);

        $v_key = isset($result['C_ACTIVATE']) ? $result['C_ACTIVATE'] : '';
        $v_id = isset($result['PK_SUBSCRIBER']) ? $result['PK_SUBSCRIBER'] : -1;

        //kiem tra sang ky chua
        if ($v_key == '' or $v_key == NULL)
        {
            return false;
        }

        //gui thu xac nhan
        require_once SERVER_ROOT . 'libs/swift/lib/swift_required.php';
        $server_name = get_system_config_value(CFGKEY_MAIL_SERVER);
        $port = get_system_config_value(CFGKEY_MAIL_PORT);
        $ssl = get_system_config_value(CFGKEY_MAIL_SSL) == 'true' ? 'ssl' : null;
        $transport = Swift_SmtpTransport::newInstance($server_name, $port, $ssl);
        $transport->setUsername(get_system_config_value(CFGKEY_MAIL_ACCOUNT));
        $transport->setPassword(get_system_config_value(CFGKEY_MAIL_PASSWORD));
        $v_subject = get_system_config_value(CFGKEY_UNIT_NAME) . ' - ' . 'auth email';

        // Tạo đối tượng mailer sẽ đãm nhận nhiệm vụ gởi mail đi
        $mailer = Swift_Mailer::newInstance($transport);
        $url = SITE_ROOT . "index.php?url=frontend/frontend/dsp_auth_subscribe/$v_id&key=$v_key&type=stop";

        $mail_content = "
            <h3>$lang_confirm</h3>
            <div><a href='$url'>$url</a></div>
            <div>Email: $v_email</div>
            <div>$lang_key: $v_key</div>
        ";

        //Tạo message để gởi đi
        $message = Swift_Message::newInstance($v_subject);

        $message->addPart($mail_content, 'text/html');
        $message->setFrom(get_system_config_value(CFGKEY_MAIL_ADD));
        $message->addTo($v_email);

        // Gởi message
        $mailer->send($message);

        return $v_id;
    }

    function activate_subscriber()
    {
        //dung id hoac email deu duoc
        $v_id = get_request_var('hdn_item_id');
        $v_email = get_request_var('txt_email');
        $v_activate = get_request_var('txt_code');
        $website = $this->website_id;

        $subscribe_id = 0;

        //valide date and update
        if (($v_id or $v_email) && $v_activate)
        {
            if (DATABASE_TYPE == 'MSSQL')
            {
                $sql = "
                    Update t_ps_subscriber Set C_AUTH = 1
                    Where FK_WEBSITE = $website
                    And C_ACTIVATE = '$v_activate'
                    And C_AUTH = 0
                    And DateDiff(dd, C_DATE, getDate()) <= 3
                    And (PK_SUBSCRIBER = $v_id or C_EMAIL = '$v_email')
                    ";
            }
            else if (DATABASE_TYPE == 'MYSQL')
            {
                $sql = "
                    Update t_ps_subscriber Set C_AUTH = 1
                    Where FK_WEBSITE = $website
                    And C_ACTIVATE = '$v_activate'
                    And C_AUTH = 0
                    And DATEDIFF(NOW(),C_DATE) <= 3
                    And (PK_SUBSCRIBER = $v_id or C_EMAIL = '$v_email')
                    ";
            }

            $this->db->Execute($sql);
        }

        //cap nhat thanh cong
        if ($this->db->errorNo() == 0)
        {
            $subscribe_id = $v_id;
        }

        //tao url exec done
        $url = $this->url_auth_subscribe . $subscribe_id;
        $this->exec_done($url);
    }

    public function qry_banner($v_category_id = 0)
    {
        $v_website_id = $this->website_id;
        $v_category_id = replace_bad_char($v_category_id);
        if ($v_category_id == 0)
        {
            $stmt = "select b.C_FILE_NAME from t_ps_banner b
                    where b.C_DEFAULT = 1 and b.C_STATUS =1 and FK_WEBSITE = $v_website_id";
            return $this->db->getOne($stmt);
        }
        else
        {
            $stmt = "select C_FILE_NAME from t_ps_banner b
                    where PK_BANNER = (select FK_BANNER from t_ps_banner_category where FK_CATEGORY = $v_category_id)";

            $v_banner = $this->db->getOne($stmt);

            if ($v_banner != NULL)
            {
                return $v_banner;
            }
            else
            {
                $stmt = "select C_FILE_NAME from t_ps_banner b
                    where b.C_DEFAULT = 1 and b.C_STATUS =1 and b.FK_WEBSITE = $v_website_id";
                return $this->db->getOne($stmt);
            }
        }
    }

    public function qry_all_website()
    {
        $stmt = "SELECT w.*,l.C_NAME AS C_LIST_NAME,l.C_CODE AS C_LIST_CODE
                        FROM t_ps_website w
                          LEFT JOIN t_cores_list l
                            ON w.FK_LANG = l.PK_LIST
                        WHERE w.C_STATUS = 1
                            AND l.C_STATUS = 1
                        ORDER BY w.C_ORDER";
        return $this->db->getAll($stmt);
    }

    function qry_single_event_title($id)
    {
        $sql = "Select C_NAME From t_ps_event Where PK_EVENT = $id";
        return $this->db->getRow($sql);
    }

    public function qry_single_event($website_id, $event_id)
    {
        $v_page = get_request_var('page', 1);
        $v_start = _CONST_DEFAULT_ROWS_PER_PAGE * ($v_page - 1) + 1;
        $v_end = $v_start + _CONST_DEFAULT_ROWS_PER_PAGE - 1;
        $new_article_mode = get_system_config_value(CFGKEY_NEW_ARTICLE_ICON) == 'true' ? 0 : 1;
        $new_article_cond = (int) get_system_config_value(CFGKEY_NEW_ARTICLE_COND);

        $website_id = replace_bad_char($website_id);
        $event_id = replace_bad_char($event_id);


        if (DATABASE_TYPE == 'MSSQL')
        {
            $sql = "SELECT x.*
                                FROM
                                  (SELECT sa.*,
                                          ROW_NUMBER()OVER( ORDER BY C_BEGIN_DATE_SQL DESC) AS rn
                                   FROM
                                     (SELECT 
                                             CONVERT(varchar(10),A.C_BEGIN_DATE,103)AS C_BEGIN_DATE,
                                             A.PK_ARTICLE,
                                             A.C_TITLE,
                                             A.C_SUMMARY,
                                             A.C_FILE_NAME,
                                             EA.FK_CATEGORY,
                                             (dateDiff(dd, A.C_BEGIN_DATE, getDate()) - $new_article_cond) as CK_NEW_ARTICLE,
                                            (select C_SLUG from t_ps_category where PK_CATEGORY=ea.FK_CATEGORY) as C_SLUG_CAT,
                                             A.C_SLUG,
                                             C_HAS_VIDEO, C_HAS_PHOTO
                                             ,A.C_BEGIN_DATE as C_BEGIN_DATE_SQL
                                      FROM t_ps_event_article ea 
                                      INNER JOIN t_ps_article a ON FK_ARTICLE = PK_ARTICLE
                                      Inner Join t_ps_event e
                                      On ea.FK_EVENT = e.PK_EVENT
                                        Where (
                                                select C_STATUS from t_ps_category 
                                                where PK_CATEGORY = FK_CATEGORY
                                            ) = 1 
                                        And E.FK_WEBSITE = $website_id 
                                        AND DATEDIFF(mi,A.C_BEGIN_DATE,GETDATE())>=0
                                        AND DATEDIFF(mi,GETDATE(), A.C_END_DATE) >= 0
                                        AND A.C_STATUS = 3
                                        AND E.PK_EVENT = $event_id
                                     )sa
                                ) x
                                WHERE x.rn>=$v_start 
                                  AND x.rn<=$v_end 
                                  FOR XML Raw,
                                          root('data')";
            $stmt = "SELECT PK_EVENT,
                                    C_SLUG,
                                    C_NAME,($sql)as C_XML_ARTICLE,
                           (select COUNT(*) from t_ps_event_article inner join t_ps_event on PK_EVENT = FK_EVENT 
                                where FK_EVENT = e.PK_EVENT and C_STATUS = 1 and C_DEFAULT =1
                             ) AS TOTAL_RECORD 
                           FROM t_ps_event e
                           WHERE e.PK_EVENT = ?
                              AND E.FK_WEBSITE = ?
                              AND E.C_STATUS=1
                              AND E.C_DEFAULT =1";
            $arr_param = array($event_id, $website_id);
        }
        else if (DATABASE_TYPE == 'MYSQL')
        {
            $v_limit = $v_end - $v_start;
            $v_start = $v_start - 1;
            //set group concat max len (phong truong hop server set default thap)
            $sql = "SET SESSION group_concat_max_len = 1000000";
            $this->db->Execute($sql);

            $sql = "SELECT
                                CONCAT('<data>',GROUP_CONCAT('<row '
                                        ,CONCAT(' C_BEGIN_DATE=\"', DATE_FORMAT(A.C_BEGIN_DATE,'%d-%m-%Y'), '\"')
                                        ,CONCAT(' PK_ARTICLE=\"', A.PK_ARTICLE, '\"')
                                        ,CONCAT(' C_TITLE=\"', A.C_TITLE, '\"')
                                        ,CONCAT(' C_SUMMARY=\"', f_replace_xml_bad_char(A.C_SUMMARY), '\"')
                                        ,CONCAT(' C_FILE_NAME=\"', A.C_FILE_NAME, '\"')
                                        ,CONCAT(' FK_CATEGORY=\"', EA.FK_CATEGORY, '\"')
                                        ,CONCAT(' CK_NEW_ARTICLE=\"',(DATEDIFF(NOW(),A.C_BEGIN_DATE) - $new_article_cond), '\"')
                                        ,CONCAT(' C_SLUG_CAT=\"',(SELECT C_SLUG
                                                                    FROM t_ps_category
                                                                    WHERE PK_CATEGORY=EA.FK_CATEGORY), '\"')
                                        ,CONCAT(' C_SLUG=\"', A.C_SLUG, '\"')
                                        ,CONCAT(' C_HAS_VIDEO=\"',if(C_HAS_VIDEO is null,0,C_HAS_VIDEO) , '\"')
                                        ,CONCAT(' C_HAS_PHOTO=\"', if(C_HAS_VIDEO is null,0,C_HAS_PHOTO), '\"')
                                        ,CONCAT(' C_BEGIN_DATE_SQL=\"', A.C_BEGIN_DATE, '\"')
                                        , ' />'
                                        SEPARATOR ''),'</data>') 
                                 FROM t_ps_event_article EA
                                 INNER JOIN t_ps_article A ON FK_ARTICLE = PK_ARTICLE
                                 INNER JOIN t_ps_event E ON EA.FK_EVENT = E.PK_EVENT
                                 WHERE
                                     (SELECT C_STATUS
                                      FROM t_ps_category
                                      WHERE PK_CATEGORY = FK_CATEGORY) = 1
                                   AND E.FK_WEBSITE = $website_id
                                   AND NOW() >= A.C_BEGIN_DATE
                                   AND A.C_END_DATE >= NOW()
                                   AND A.C_STATUS = 3
                                   AND E.PK_EVENT = $event_id 
                             ";
            $stmt = "SELECT PK_EVENT,
                                    C_SLUG,
                                    C_NAME,($sql)as C_XML_ARTICLE,
                                (SELECT COUNT(*)
                                    FROM t_ps_event_article
                                    INNER JOIN t_ps_event ON PK_EVENT = FK_EVENT
                                    WHERE FK_EVENT = e.PK_EVENT
                                      AND C_STATUS = 1
                                      AND C_DEFAULT =1) AS TOTAL_RECORD
                             FROM t_ps_event e
                             WHERE e.PK_EVENT = ?
                               AND e.FK_WEBSITE = ?
                               AND e.C_STATUS=1
                               AND e.C_DEFAULT =1";

            $arr_param = array($event_id, $website_id);
        }
        return $this->db->GetRow($stmt, $arr_param);
    }

    /*
     * hlay danh sach tin anh
     */

    public function qry_all_img_news($website_id)
    {
        $v_page = get_request_var('page', 1);
        $v_start = _CONST_DEFAULT_ROWS_PER_PAGE * ($v_page - 1) + 1;
        $v_end = $v_start + _CONST_DEFAULT_ROWS_PER_PAGE - 1;

        $new_article_mode = get_system_config_value(CFGKEY_NEW_ARTICLE_ICON) == 'true' ? 0 : 1;
        $new_article_cond = (int) get_system_config_value(CFGKEY_NEW_ARTICLE_COND);

        $website_id = replace_bad_char($website_id);


        if (DATABASE_TYPE == 'MSSQL')
        {
            
        }
        else if (DATABASE_TYPE == 'MYSQL')
        {
            $v_limit = $v_end - $v_start;
            $v_start = $v_start - 1;

            $sql = "Select
                    A.PK_ARTICLE,
                    A.C_TITLE,
                    A.C_SUB_TITLE,
                    A.C_SUMMARY,
                    A.C_CONTENT,
                    A.C_SLUG,
                    A.C_FILE_NAME,
                    (Select
                       C_SLUG
                     From t_ps_category
                     Where PK_CATEGORY = A.C_DEFAULT_CATEGORY) as C_SLUG_CATEGORY,
                    A.C_DEFAULT_CATEGORY AS PK_CATEGORY,
                    (select COUNT( A.PK_ARTICLE) from t_ps_article A
                            where C_IS_IMG_NEWS = 1
                                and A.C_STATUS = 3
                                and A.C_DEFAULT_WEBSITE = 17
                                And DATEDIFF(NOW(),A.C_BEGIN_DATE) >= 0
                                And DATEDIFF(NOW(),A.C_END_DATE) <= 0
                                and (Select
                                       C_STATUS
                                     From t_ps_category
                                     Where PK_CATEGORY = A.C_DEFAULT_CATEGORY) = 1
                            order by A.C_BEGIN_DATE ) as TOTAL_RECORD
                  from t_ps_article A
                  where C_IS_IMG_NEWS = 1
                      and A.C_STATUS = 3
                      and A.C_DEFAULT_WEBSITE = $website_id
                      And DATEDIFF(NOW(),A.C_BEGIN_DATE) >= 0
                      And DATEDIFF(NOW(),A.C_END_DATE) <= 0
                      and (Select
                             C_STATUS
                           From t_ps_category
                           Where PK_CATEGORY = A.C_DEFAULT_CATEGORY) = 1
                  order by A.C_BEGIN_DATE
                  LIMIT  $v_start,$v_limit ";
        }
        return $this->db->getAll($sql);
    }

    public function qry_all_photo_gallery($website_id)
    {
        $v_page = get_request_var('page', 1);
        $v_start = _CONST_DEFAULT_ROWS_PER_PAGE * ($v_page - 1) + 1;
        $v_end = $v_start + _CONST_DEFAULT_ROWS_PER_PAGE - 1;

        $new_article_mode = get_system_config_value(CFGKEY_NEW_ARTICLE_ICON) == 'true' ? 0 : 1;
        $new_article_cond = (int) get_system_config_value(CFGKEY_NEW_ARTICLE_COND);

        $website_id = replace_bad_char($website_id);


        if (DATABASE_TYPE == 'MSSQL')
        {
            
        }
        else if (DATABASE_TYPE == 'MYSQL')
        {
            $v_limit = $v_end - $v_start;
            $v_start = $v_start - 1;

            $sql = "Select
                    PK_PHOTO_GALLERY,
                    C_SLUG,
                    C_TITLE,
                    C_SUMMARY,
                    C_FILE_NAME,
                    C_BEGIN_DATE,
                    C_END_DATE,
                    (select
                        COUNT(*)
                      from t_ps_photo_gallery
                      where C_STATUS = 3
                          And FK_WEBSITE = 17
                          And DATEDIFF(NOW(),C_BEGIN_DATE) >= 0
                          And DATEDIFF(C_END_DATE,NOW()) >= 0) as TOTAL_RECORD
                  From t_ps_photo_gallery
                  Where C_STATUS = 3
                      And FK_WEBSITE = $website_id
                      And DATEDIFF(NOW(),C_BEGIN_DATE) >= 0
                      And DATEDIFF(C_END_DATE,NOW()) >= 0
                  Order by C_BEGIN_DATE LIMIT $v_start,$v_limit";
        }
        return $this->db->getAll($sql);
    }

    /**
     *  Lay danh sach tin bai cua mot chuyen muc
     */
    public function qry_all_article_by_category($website_id, $category_id)
    {
        $date = get_request_var('date');
        $dd = $mm = $yyyy = -1;
        if (strlen($date) == 8)
        {
            $yyyy = substr($date, 0, 4);
            $mm = substr($date, 4, 2);
            $dd = substr($date, 6, 2);
        }
        if (checkdate($mm, $dd, $yyyy))
        {
            if (DATABASE_TYPE == 'MSSQL')
            {
                $date_cond = " And datediff(dd, C_BEGIN_DATE, '$yyyy-$mm-$dd') = 0";
            }
            else if (DATABASE_TYPE == 'MYSQL')
            {
                $date_cond = " And DATEDIFF('$yyyy-$mm-$dd',C_BEGIN_DATE, NOW()) = 0";
            }
        }
        else
        {
            $date_cond = '';
        }

        //Lay thong tin cau hinh: So tin bai hien thi/trang cua chuyen muc
        $v_rows_per_page = (int) get_system_config_value(CFGKEY_ARCHIVE_ARTICLE_PER_CATEGORY);

        $v_page = (int) get_request_var('page', 1);
        if ($v_page == 0)
        {
            $v_page = 1;
        }

        $v_start = $v_rows_per_page * ($v_page - 1) + 1;
        $v_end = $v_start + $v_rows_per_page - 1;
        $new_article_mode = get_system_config_value(CFGKEY_NEW_ARTICLE_ICON) == 'true' ? 0 : 1;
        $new_article_cond = (int) get_system_config_value(CFGKEY_NEW_ARTICLE_COND);

        $website_id = replace_bad_char($website_id);
        $category_id = replace_bad_char($category_id);
        if (DATABASE_TYPE == 'MSSQL')
        {
            $sql = "select 
                    PK_CATEGORY
                    ,C_SLUG
                    ,C_NAME
                    ,C_IS_VIDEO
                    ,(Select 
                            MA.TOTAL_RECORD
                            , Convert(varchar(10),FA.C_BEGIN_DATE,103)as C_BEGIN_DATE
                            , FA.C_BEGIN_DATE as C_BEGIN_DATE_YYYYMMDD
                            , FA.PK_ARTICLE
                            , FA.C_TITLE
                            , FA.C_SUMMARY
                            , FA.C_FILE_NAME
                            , FA.C_SLUG
                            , 0 As CK_NEW_ARTICLE
                            , FA.C_HAS_VIDEO
                            , FA.C_HAS_PHOTO
                        From t_ps_article FA 
                            Right Join( Select 
                                            mrs.PK_ARTICLE
                                            ,mrs.TOTAL_RECORD
                                        From ( Select 
                                                    A.PK_ARTICLE    
                                                    ,ROW_NUMBER() Over (Order By C_BEGIN_DATE Desc) as RN
                                                    ,Count(*) over(Partition By 1) as TOTAL_RECORD
                                                From t_ps_article A
                                                    Right Join ( Select 
                                                                        MAX(CA.FK_CATEGORY) as FK_CATEGORY
                                                                        , CA.FK_ARTICLE 
                                                                    From t_ps_category_article as CA 
                                                                        Left Join t_ps_category as C 
                                                                        On CA.FK_CATEGORY=C.PK_CATEGORY
                                                                    Where C.FK_WEBSITE=$website_id
                                                                        And C.PK_CATEGORY=$category_id
                                                                        And C.C_STATUS=1
                                                                    Group By FK_ARTICLE
                                                                ) fca 
                                                    On A.PK_ARTICLE=fca.FK_ARTICLE
                                                Where A.C_STATUS=3
                                                    And C_BEGIN_DATE <= GetDate()
                                                    And C_END_DATE >= GETDATE()$date_cond
                                                ) as mrs
                                        Where mrs.RN>=$v_start And mrs.RN<=$v_end
                                        ) as MA 
                            On FA.PK_ARTICLE=MA.PK_ARTICLE
                        For XML Raw, root('data')
                    ) as C_XML_ARTICLE    
                From t_ps_category
                Where PK_CATEGORY=$category_id and FK_WEBSITE=$website_id and C_STATUS=1";
        }
        else if (DATABASE_TYPE == 'MYSQL')
        {

            //total record
            $sql = "SELECT
                    COUNT(FA.PK_ARTICLE)
                    FROM t_ps_article FA
                      RIGHT JOIN (SELECT
                                    mrs.PK_ARTICLE
                                  FROM (SELECT
                                          A.PK_ARTICLE
                                        FROM t_ps_article A
                                          RIGHT JOIN (SELECT
                                                        MAX(CA.FK_CATEGORY) AS FK_CATEGORY,
                                                        CA.FK_ARTICLE
                                                      FROM t_ps_category_article AS CA
                                                        LEFT JOIN t_ps_category AS C
                                                          ON CA.FK_CATEGORY = C.PK_CATEGORY
                                                      WHERE C.FK_WEBSITE = $website_id
                                                          AND C.PK_CATEGORY = $category_id
                                                          AND C.C_STATUS = 1
                                                      GROUP BY FK_ARTICLE) fca
                                            ON A.PK_ARTICLE = fca.FK_ARTICLE
                                        WHERE A.C_STATUS = 3
                                            AND C_BEGIN_DATE <= NOW()
                                            AND C_END_DATE >= NOW() $date_cond
                                        ORDER BY C_BEGIN_DATE DESC
                                        ) AS mrs) AS MA
                        ON FA.PK_ARTICLE = MA.PK_ARTICLE";
            $v_total_record = $this->db->getOne($sql);

            //tinh toan phan trang
            $v_start = $v_start - 1;
            $v_limit = $v_end - $v_start;
            //set group concat max len (phong truong hop server set default thap)
//            $sql = "SET SESSION group_concat_max_len = 1000000";
//            $this->db->Execute($sql);
            //lay du lieu category article
            $sql = "select 
                    PK_CATEGORY
                    ,C_SLUG
                    ,C_NAME
                    ,C_IS_VIDEO
                    ,$v_total_record as TOTAL_RECORD
                    ,(SELECT
                            CONCAT('<data>',GROUP_CONCAT('<row '
                                            ,CONCAT(' TOTAL_RECORD=\"', $v_total_record, '\"')
                                            ,CONCAT(' C_BEGIN_DATE=\"', DATE_FORMAT(FA.C_BEGIN_DATE,'%d-%m-%Y'), '\"')
                                            ,CONCAT(' C_BEGIN_DATE_YYYYMMDD=\"', FA.C_BEGIN_DATE, '\"')
                                            ,CONCAT(' PK_ARTICLE=\"', FA.PK_ARTICLE, '\"')
                                            ,CONCAT(' C_TITLE=\"', FA.C_TITLE, '\"')
                                            ,CONCAT(' C_SUMMARY=\"', f_replace_xml_bad_char(FA.C_SUMMARY), '\"')
                                            ,CONCAT(' C_FILE_NAME=\"', IF(FA.C_FILE_NAME IS NULL, '', FA.C_FILE_NAME), '\"')
                                            ,CONCAT(' C_SLUG=\"', IF(FA.C_SLUG IS NULL, '', FA.C_SLUG), '\"')
                                            ,CONCAT(' CK_NEW_ARTICLE=\"', 0 , '\"')
                                            ,CONCAT(' C_HAS_VIDEO=\"', IF(FA.C_HAS_VIDEO IS NULL, 0, FA.C_HAS_VIDEO), '\"') 
                                            ,CONCAT(' C_HAS_PHOTO=\"', IF(FA.C_HAS_PHOTO IS NULL, 0, FA.C_HAS_PHOTO), '\"') 
                                            , ' />'
                                            SEPARATOR ''),'</data>')
                            FROM t_ps_article FA
                              RIGHT JOIN (SELECT
                                            mrs.PK_ARTICLE
                                          FROM (SELECT
                                                  A.PK_ARTICLE
                                                FROM t_ps_article A
                                                  RIGHT JOIN (SELECT
                                                                MAX(CA.FK_CATEGORY) AS FK_CATEGORY,
                                                                CA.FK_ARTICLE
                                                              FROM t_ps_category_article AS CA
                                                                LEFT JOIN t_ps_category AS C
                                                                  ON CA.FK_CATEGORY = C.PK_CATEGORY
                                                              WHERE C.FK_WEBSITE = $website_id
                                                                  AND C.PK_CATEGORY = $category_id
                                                                  AND C.C_STATUS = 1
                                                              GROUP BY FK_ARTICLE) fca
                                                    ON A.PK_ARTICLE = fca.FK_ARTICLE
                                                WHERE A.C_STATUS = 3
                                                    AND C_BEGIN_DATE <= NOW()
                                                    AND C_END_DATE >= NOW() $date_cond
                                                ORDER BY C_BEGIN_DATE DESC
                                                LIMIT $v_start,$v_limit) AS mrs) AS MA
                                ON FA.PK_ARTICLE = MA.PK_ARTICLE) as C_XML_ARTICLE    
                From t_ps_category
                Where PK_CATEGORY=$category_id and FK_WEBSITE=$website_id and C_STATUS=1";
            return $this->db->getRow($sql);
        }
    }

    public function qry_single_category($website_id, $category_id)
    {
        //1. Lay danh sach tin bao thuoc truc tiep chuyen muc
        $arr_all_article = $this->qry_all_article_by_category($website_id, $category_id);

        //LienND update 2013-07-29: Lay danh sach chuyen muc con + Tin bai tuong ung cua chuyen muc con
        //2. Danh sach chuyen muc con
        $sql = "Select
        			PK_CATEGORY
        		From t_ps_category
				Where FK_PARENT=$category_id
					And FK_WEBSITE=$website_id
					And C_STATUS=1
				Order By C_INTERNAL_ORDER";
        $arr_all_sub_category = $this->db->getCol($sql);

        //3. Danh sach tin bai cua moi chuyen muc con
        $arr_all_sub_category_with_article = Array();
        foreach ($arr_all_sub_category as $v_sub_category_id)
        {
            $arr_all_sub_category_with_article["'$v_sub_category_id'"] = $this->qry_all_article_by_category($website_id, $v_sub_category_id);
        }

        $MODEL_DATA['arr_single_category'] = $arr_all_article;
        $MODEL_DATA['arr_all_sub_category_with_article'] = $arr_all_sub_category_with_article;
        return $MODEL_DATA;
    }

    function qry_root_category($category_id)
    {
        $stmt = 'Select C_INTERNAL_ORDER, FK_WEBSITE
                    From t_ps_category 
                    Where pk_category= ?';
        $params = array($category_id);
        $cat_info = $this->db->getRow($stmt, $params);

        $v_the_leaf_internal_order = $cat_info['C_INTERNAL_ORDER'];
        $v_website_id = $cat_info['FK_WEBSITE'];

        $sql = '';
        $v_parent_internal_order = '';
        while ($v_parent_internal_order != $v_the_leaf_internal_order)
        {
            $v_parent_internal_order = substr($v_the_leaf_internal_order, 0, strlen($v_parent_internal_order) + 3);

            if ($sql != '')
            {
                $sql .= ' UNION ALL ';
            }
            $sql .= " Select PK_CATEGORY, C_NAME From t_ps_category Where C_INTERNAL_ORDER = '$v_parent_internal_order' And FK_WEBSITE=$v_website_id";
        }

        return $this->db->getAssoc($sql);
    }

    public function qry_single_article($website_id, $category_id, $article_id)
    {
        $website_id = replace_bad_char($website_id);
        $category_id = replace_bad_char($category_id);
        $article_id = replace_bad_char($article_id);
        $v_default = _CONST_DEFAULT_ROWS_OTHER_NEWS;


        if (DATABASE_TYPE == 'MSSQL')
        {
            $stmt = "SELECT 
                            (
                                CONVERT(varchar(10),C_BEGIN_DATE,103)
                                + ' ' + CONVERT(varchar(5),C_BEGIN_DATE,108)
                             )AS C_BEGIN_DATE,
                            C_TITLE,
                            C_SLUG as C_SLUG_ARTICLE,
                            C_SUB_TITLE,
                            C_PEN_NAME,
                            C_KEYWORDS,
                            C_TAGS,
                            C_CACHED_RATING,
                            C_CACHED_RATING_COUNT,
                            C_SUMMARY,
                            C_TAGS,
                            C_FILE_NAME,
                            C_CONTENT,
                            (SELECT C_SLUG FROM t_ps_category WHERE PK_CATEGORY = ?) as C_SLUG_CAT,
                            (SELECT C_NAME FROM t_ps_category WHERE PK_CATEGORY = ?) as C_CATEGORY_NAME
                     FROM t_ps_article a
                     INNER JOIN t_ps_category_article ca ON a.PK_ARTICLE = ca.FK_ARTICLE
                     WHERE PK_ARTICLE = ? 
                       AND FK_CATEGORY = ? 
                       AND FK_CATEGORY IN
                         (SELECT PK_CATEGORY
                          FROM t_ps_category
                          WHERE FK_WEBSITE = ?) 
                       AND C_STATUS = 3 
                       AND (select C_STATUS from t_ps_category where PK_CATEGORY = ?) = 1 
                       AND C_BEGIN_DATE <= GetDate() 
                       AND C_END_DATE >= GetDate()";
            $article = $this->db->getRow($stmt, array($category_id, $category_id, $article_id, $category_id, $website_id, $category_id));
        }
        else if (DATABASE_TYPE == 'MYSQL')
        {
            $stmt = "SELECT
                            DATE_FORMAT(C_BEGIN_DATE,'%d-%m-%Y %H:%i:%s') AS C_BEGIN_DATE,
                            C_TITLE,
                            C_SLUG                AS C_SLUG_ARTICLE,
                            C_SUB_TITLE,
                            C_PEN_NAME,
                            C_KEYWORDS,
                            C_TAGS,
                            C_CACHED_RATING,
                            C_CACHED_RATING_COUNT,
                            C_SUMMARY,
                            C_TAGS,
                            C_FILE_NAME,
                            C_CONTENT,
                            (SELECT
                               C_SLUG
                             FROM t_ps_category
                             WHERE PK_CATEGORY = ?) AS C_SLUG_CAT,
                            (SELECT
                               C_NAME
                             FROM t_ps_category
                             WHERE PK_CATEGORY = ?) AS C_CATEGORY_NAME
                          FROM t_ps_article a
                            INNER JOIN t_ps_category_article ca
                              ON a.PK_ARTICLE = ca.FK_ARTICLE
                          WHERE PK_ARTICLE = ?
                              AND FK_CATEGORY = ?
                              AND FK_CATEGORY IN(SELECT
                                                   PK_CATEGORY
                                                 FROM t_ps_category
                                                 WHERE FK_WEBSITE = ?)
                              AND C_STATUS = 3
                              AND (SELECT
                                     C_STATUS
                                   FROM t_ps_category
                                   WHERE PK_CATEGORY = ?) = 1
                              AND C_BEGIN_DATE <= NOW()
                              AND C_END_DATE >= NOW()";
            $article = $this->db->getRow($stmt, array($category_id, $category_id, $article_id, $category_id, $website_id, $category_id));
        }

        $article['ROOT_CATEGORY'] = $this->qry_root_category($category_id);

        return $article;
    }

    public function qry_single_gallery($website_id, $gallery_id)
    {
        $website_id = replace_bad_char($website_id);
        $gallery_id = replace_bad_char($gallery_id);


        if (DATABASE_TYPE == 'MSSQL')
        {
            $sql_img = "select x.* from (
			select ga.*
                        from t_ps_photo_gallery_detail ga 
			where 
			FK_PHOTO_GALLERY = g.PK_PHOTO_GALLERY
                    ) 
                    x FOR XML RAW, ROOT('data')";

            $sql_other = "select x.* from (
			select PK_PHOTO_GALLERY,C_TITLE,C_SLUG,CONVERT(varchar(10),
                        C_BEGIN_DATE,103)as C_BEGIN_DATE 
                        From t_ps_photo_gallery 
                        where 
			C_STATUS = 3
			and DATEDIFF(mi,C_BEGIN_DATE,g.C_BEGIN_DATE) >=0
			and PK_PHOTO_GALLERY <> g.PK_PHOTO_GALLERY
			and FK_WEBSITE = $website_id
                    ) 
                    x FOR XML RAW, ROOT('data')";

            $stmt = "select g.PK_PHOTO_GALLERY,
                                    g.C_SLUG,C_TITLE,
                                    g.C_SUMMARY,
                                    CONVERT(varchar(10),g.C_BEGIN_DATE,103) as C_BEGIN_DATE,
                                    ($sql_img) as C_XML_IMG,
                                    ($sql_other) as C_XML_OTHER
                    from t_ps_photo_gallery g
                    where PK_PHOTO_GALLERY = ? 
                    and C_STATUS = 3
                    and  DATEDIFF(mi,g.C_BEGIN_DATE,GETDATE())>=0 
                    AND DATEDIFF(mi,GETDATE(),g.C_END_DATE) >= 0  
                    and FK_WEBSITE = ?";
        }
        else if (DATABASE_TYPE == 'MYSQL')
        {
            //set group concat max len (phong truong hop server set default thap)
            $sql = "SET SESSION group_concat_max_len = 1000000";
            $this->db->Execute($sql);

            $sql_img = "
                            select
                                CONCAT('<data>',GROUP_CONCAT('<row '
                                    ,CONCAT(' PK_PHOTO_GALLERY_DETAIL=\"', PK_PHOTO_GALLERY_DETAIL, '\"')
                                    ,CONCAT(' FK_PHOTO_GALLERY=\"', FK_PHOTO_GALLERY, '\"')
                                    ,CONCAT(' C_ORDER=\"', C_ORDER, '\"')
                                    ,CONCAT(' C_NOTE=\"', C_NOTE, '\"')
                                    ,CONCAT(' C_FILE_NAME=\"', C_FILE_NAME, '\"')
                                    , ' />'
                                SEPARATOR ''),'</data>')
                            from t_ps_photo_gallery_detail ga 
                            where 
                            FK_PHOTO_GALLERY = g.PK_PHOTO_GALLERY
                        ";

            $sql_other = "
			select 
                            CONCAT('<data>',GROUP_CONCAT('<row '
                                    ,CONCAT(' PK_PHOTO_GALLERY=\"', PK_PHOTO_GALLERY, '\"')
                                    ,CONCAT(' C_TITLE=\"', C_TITLE, '\"')
                                    ,CONCAT(' C_SLUG=\"', C_SLUG, '\"')
                                    ,CONCAT(' C_BEGIN_DATE=\"', DATE_FORMAT(C_BEGIN_DATE,'%d-%m-%Y'), '\"')
                                    , ' />'
                            SEPARATOR ''),'</data>')
                        From t_ps_photo_gallery 
                        where 
			C_STATUS = 3
			and  DATEDIFF(g.C_BEGIN_DATE, C_BEGIN_DATE) >=0
			and PK_PHOTO_GALLERY <> g.PK_PHOTO_GALLERY
			and FK_WEBSITE = $website_id
                    ";
            $stmt = "select g.PK_PHOTO_GALLERY,
                                    g.C_SLUG,C_TITLE,
                                    g.C_SUMMARY,
                                    DATE_FORMAT(g.C_BEGIN_DATE,'%d-%m-%Y') as C_BEGIN_DATE,
                                    ($sql_img) as C_XML_IMG,
                                    ($sql_other) as C_XML_OTHER
                    from t_ps_photo_gallery g
                    where PK_PHOTO_GALLERY = ? 
                    and C_STATUS = 3
                    and DATEDIFF(NOW(),g.C_BEGIN_DATE) >=0 
                    AND DATEDIFF(g.C_END_DATE, NOW()) >= 0  
                    and FK_WEBSITE = ?";
        }

        return $this->db->getRow($stmt, array($gallery_id, $website_id));
    }

    public function qry_all_event($website_id = 0)
    {
        $website_id = replace_bad_char($website_id);
        if ($website_id == 0)
        {
            $stmt = "select PK_WEBSITE from t_ps_website  where C_ORDER = 1";
            $website_id = $this->db->getOne($stmt);
        }

        if (DATABASE_TYPE == 'MSSQL')
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
        else if (DATABASE_TYPE == 'MYSQL')
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
                        and C_DEFAULT=1 
                        and NOW() >= C_BEGIN_DATE
                        and C_END_DATE >= NOW())
                        and FK_WEBSITE = $website_id 
                        and e.C_IS_REPORT <> 1 
                        order by C_ORDER";
        }


        if (!$arr_all_event = $this->db->getAll($stmt))
        {
            return $arr_all_event;
        }

        foreach ($arr_all_event as &$event)
        {
            if ($event['C_COUNT_ARTICLE'] == 1)
            {
                if (DATABASE_TYPE == 'MSSQL')
                {
                    $sql_article = "
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
                else if (DATABASE_TYPE == 'MYSQL')
                {
                    $sql_article = "
                        Select 
                            a.PK_ARTICLE
                            ,a.C_SLUG
                            ,a.C_FILE_NAME
                            ,c.PK_CATEGORY
                            , c.C_SLUG As C_CAT_SLUG
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

    public function qry_tags($website_id, $tags)
    {
        $v_page = get_request_var('page', 1);
        $v_start = _CONST_DEFAULT_ROWS_PER_PAGE * ($v_page - 1) + 1;
        $v_end = $v_start + _CONST_DEFAULT_ROWS_PER_PAGE - 1;

        $website_id = replace_bad_char($website_id);
        $tags = replace_bad_char($tags);
        $sql = "select C_TITLE,
                        PK_ARTICLE,
                        '$tags' as C_TAGS, 
                        C_SUMMARY,
                        CONVERT(varchar(10),C_BEGIN_DATE,103) as C_BEGIN_DATE,
                        C_FILE_NAME,
                        ROW_NUMBER() OVER (order by C_BEGIN_DATE desc)as rn,
                        COUNT(*) OVER (PARTITION BY 1 ) as TOTAL_RECORD,
                        C_SLUG as C_SLUG_ARTICLE,
                        (select top 1 c.C_SLUG FROM t_ps_category c inner join t_ps_category_article ca on PK_CATEGORY = FK_CATEGORY where FK_ARTICLE = a.PK_ARTICLE) as C_SLUG_CATEGORY,
                        (select top 1 c.PK_CATEGORY FROM t_ps_category c inner join t_ps_category_article ca on PK_CATEGORY = FK_CATEGORY where FK_ARTICLE = a.PK_ARTICLE) as C_CATEGORY_id 
             from t_ps_article a
             where 
             C_TAGS Like '%$tags%' 
             and C_STATUS = 3 
             AND DATEDIFF(mi,C_BEGIN_DATE,GETDATE())>=0 
             AND DATEDIFF(mi,GETDATE(),C_END_DATE) >= 0  
             AND PK_ARTICLE in (select FK_ARTICLE from t_ps_category inner join t_ps_category_article on PK_CATEGORY = FK_CATEGORY
                                                     where FK_WEBSITE = $website_id)";
        $stmt = "select * from ($sql) as al where al.rn>=$v_start and al.rn<=$v_end";

        return $this->db->getAll($stmt);
    }

    public function qry_all_report($website_id = 0)
    {
        $website_id = replace_bad_char($website_id);
        if ($website_id == 0)
        {
            $stmt = "select PK_WEBSITE from t_ps_website  where C_ORDER = 1";
            $website_id = $this->db->getOne($stmt);
        }

        if (DATABASE_TYPE == 'MSSQL')
        {
            $stmt = "select * from t_ps_event e 
                    where C_STATUS = 1 and C_DEFAULT=1 and 
                    DATEDIFF(mi,C_BEGIN_DATE,GETDATE())>=0 and  
                    DATEDIFF(mi,GETDATE(),C_END_DATE) >= 0  and FK_WEBSITE = $website_id 
                    and e.C_IS_REPORT = 1 
                    order by C_ORDER";
        }
        else if (DATABASE_TYPE == 'MYSQL')
        {
            $stmt = "select * from t_ps_event e 
                    where C_STATUS = 1 and C_DEFAULT=1 and 
                    NOW() >= C_BEGIN_DATE
                    and C_END_DATE >= NOW()
                    and FK_WEBSITE = $website_id 
                    and e.C_IS_REPORT = 1 
                    order by C_ORDER";
        }

        return $this->db->getAll($stmt);
    }

    function qry_all_widget_position()
    {
        $v_website_code = $this->website_code;
        $v_theme_code = $this->theme_code;

        if (DATABASE_TYPE == 'MSSQL')
        {
            $sql = "Select C_XML_DATA.value('(/data/item[@id=\"txtvitriwidget\"]/value)[1]', 'nvarchar(255)') as C_ALL_POSITION
                        ,C_XML_DATA.value('(/data/item[@id=\"txt_widget_position_home\"]/value)[1]', 'nvarchar(255)')  as C_HOME_POSITION
                        ,C_XML_DATA.value('(/data/item[@id=\"txt_widget_position_archive\"]/value)[1]', 'nvarchar(255)')  as C_ARCHIVE_POSITION
                        ,C_XML_DATA.value('(/data/item[@id=\"txt_widget_position_single\"]/value)[1]', 'nvarchar(255)')  as C_SINGLE_POSITION
                        From t_cores_list L
                        Inner JOin t_cores_listtype LT
                        On L.FK_LISTTYPE = LT.PK_LISTTYPE
                        Where LT.C_CODE = 'DM_THEME'
                        And L.C_CODE = '$v_theme_code'";
        }
        else if (DATABASE_TYPE == 'MYSQL')
        {
            $sql = "Select ExtractValue(C_XML_DATA, '/data/item[@id=\"txtvitriwidget\"]/value') as C_ALL_POSITION
                        ,ExtractValue(C_XML_DATA, '/data/item[@id=\"txt_widget_position_home\"]/value') as C_HOME_POSITION
                        ,ExtractValue(C_XML_DATA, '/data/item[@id=\"txt_widget_position_archive\"]/value') as C_ARCHIVE_POSITION
                        ,ExtractValue(C_XML_DATA, '/data/item[@id=\"txt_widget_position_single\"]/value')  as C_SINGLE_POSITION
                        From t_cores_list L
                        Inner JOin t_cores_listtype LT
                        On L.FK_LISTTYPE = LT.PK_LISTTYPE
                        Where LT.C_CODE = 'DM_THEME'
                        And L.C_CODE = '$v_theme_code'";
        }

        return $this->db->getRow($sql);
    }

    function qry_single_website($website_id)
    {
        return $this->db->getRow("Select C_CODE, C_THEME_CODE,C_NAME From t_ps_website Where PK_WEBSITE = $website_id");
    }

    function qry_all_widget($website, $theme, $position)
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

    function qry_all_adv_image($pos_id)
    {
        if (DATABASE_TYPE == 'MSSQL')
        {
            $sql = " Select ADV.C_NAME, ADV.C_FILE_NAME, ADV.C_URL
                From t_ps_advertising ADV
                Where FK_ADV_POSITION = $pos_id
                And DateDiff(mi, ADV.C_BEGIN_DATE, getDate()) >= 0
                And DateDiff(mi, getDate(), ADV.C_END_DATE) >= 0";
            return $this->db->getAll($sql);
        }
        else if (DATABASE_TYPE == 'MYSQL')
        {
            $sql = "Select
                        C_NAME,
                        C_FILE_NAME,
                        C_URL
                      From t_ps_advertising 
                      Where FK_ADV_POSITION = $pos_id
                          And NOW() >= C_BEGIN_DATE
                          And C_END_DATE >= NOW()";
            return $this->db->getAll($sql);
        }
    }

    function qry_single_poll($poll_id)
    {
        if (DATABASE_TYPE == 'MSSQL')
        {
            $sql = "Select PK_POLL, C_NAME
                ,dateDiff(mi, C_BEGIN_DATE, getDate()) as CK_BEGIN_DATE
                ,dateDiff(mi, getDate(), C_END_DATE) as CK_END_DATE
                From t_ps_poll
                Where C_STATUS > 0 
                And PK_POLL = $poll_id";
        }
        else if (DATABASE_TYPE == 'MYSQL')
        {
            $sql = "Select PK_POLL, C_NAME
                ,DATEDIFF(NOW(),C_BEGIN_DATE) as CK_BEGIN_DATE
                ,DATEDIFF(C_END_DATE, NOW()) as CK_END_DATE
                From t_ps_poll
                Where C_STATUS > 0 
                And PK_POLL = $poll_id";
        }

        return $this->db->getRow($sql);
    }

    function qry_all_poll_detail($poll_id)
    {
        $sql = " Select PK_POLL_DETAIL, C_ANSWER, C_VOTE From t_ps_poll_detail
            Where FK_POLL = $poll_id";
        return $this->db->getAll($sql);
    }

    function qry_most_visited_article($limit)
    {
        $v_website_id = $this->website_id;
        $limit = replace_bad_char($limit);

        //LienND Tunning Query
        if (DATABASE_TYPE == 'MSSQL')
        {
            $sql = "Select 
                    FA.PK_ARTICLE
                    ,FA.C_TITLE
                    ,FA.C_SLUG 
                    ,MA.FK_CATEGORY  as PK_CATEGORY 
                    ,FA.C_HAS_PHOTO
                    ,FA.C_HAS_VIDEO
                    , Convert(varchar, FA.C_BEGIN_DATE, 120) as C_BEGIN_DATE
                    ,(Select C_SLUG From t_ps_category Where PK_CATEGORY=MA.FK_CATEGORY) as C_CAT_SLUG
                    , FA.C_FILE_NAME
                From t_ps_article FA Right Join 
                    (Select 
                        mrs.PK_ARTICLE
                        ,mrs.FK_CATEGORY
                    From
                    (
                        Select 
                            a.* 
                            ,ROW_NUMBER() Over(Order by C_VIEWS Desc) as RN
                        From
                        (
                            Select 
                                A.PK_ARTICLE
                                ,cca.FK_CATEGORY
                                ,A.C_VIEWS
                            From t_ps_article A left Join (Select 
                                                                MAX(CA.FK_CATEGORY) as FK_CATEGORY
                                                                , CA.FK_ARTICLE 
                                                            From t_ps_category_article as CA Left Join t_ps_category as C on CA.FK_CATEGORY=C.PK_CATEGORY
                                                            Where C.FK_WEBSITE=$v_website_id And C.C_STATUS=1
                                                            Group By FK_ARTICLE) cca ON A.PK_ARTICLE = cca.FK_ARTICLE            
                            Where A.C_STATUS=3
                                And dateDiff(mi, A.C_BEGIN_DATE, getDate()) >= 0
                                And dateDiff(mi, getDate(), A.C_END_DATE) >= 0 
                                And dateDiff(day, A.C_BEGIN_DATE, getDate()) <= 30
                        ) a
                    ) mrs
                    Where mrs.RN>=1 And mrs.RN<=$limit
                    ) MA On FA.PK_ARTICLE=MA.PK_ARTICLE";
        }
        else if (DATABASE_TYPE == 'MYSQL')
        {
            $sql = "SELECT FA.PK_ARTICLE ,
                            FA.C_TITLE ,
                            FA.C_SLUG ,
                            MA.FK_CATEGORY AS PK_CATEGORY ,
                            FA.C_HAS_PHOTO ,
                            FA.C_HAS_VIDEO ,
                            DATE_FORMAT(FA.C_BEGIN_DATE,'%Y-%m-%d %H:%i:%s') AS C_BEGIN_DATE ,

                       (SELECT C_SLUG
                        FROM t_ps_category
                        WHERE PK_CATEGORY=MA.FK_CATEGORY) AS C_CAT_SLUG ,
                            FA.C_FILE_NAME
                     FROM t_ps_article FA
                     RIGHT JOIN
                       (SELECT A.PK_ARTICLE ,
                                     A.C_DEFAULT_CATEGORY as FK_CATEGORY 
                              FROM t_ps_article A 
                              left join t_ps_category C on C.PK_CATEGORY = A.C_DEFAULT_CATEGORY
                              WHERE A.C_STATUS=3
                                and A.C_DEFAULT_WEBSITE = $v_website_id
                                and C.C_STATUS = 1
                                AND DATEDIFF(NOW(),A.C_BEGIN_DATE) >= 0
                                AND DATEDIFF(A.C_END_DATE, NOW()) >= 0
                              ORDER BY A.C_VIEWS DESC LIMIT $limit) MA 
                              ON FA.PK_ARTICLE=MA.PK_ARTICLE";
        }
        return $this->db->getAll($sql);
    }

    function handle_widget_poll($poll_id, $choice)
    {
        if (DATABASE_TYPE == 'MSSQL')
        {
            $sql = "
                    Update t_ps_poll_detail
                    Set C_VOTE = C_VOTE + 1
                    From t_ps_poll_detail PD
                    Inner Join t_ps_poll P
                    On PD.FK_POLL = P.PK_POLL
                    Where P.PK_POLL = $poll_id
                    And PD.PK_POLL_DETAIL = $choice
                     ";
            $this->db->Execute($sql);
        }
        else if (DATABASE_TYPE == 'MYSQL')
        {
            $sql = "
                    Update t_ps_poll_detail
                    Set C_VOTE = C_VOTE + 1
                    Where FK_POLL = $poll_id
                    And PK_POLL_DETAIL = $choice
                     ";
            $this->db->Execute($sql);
        }

        if ($this->db->errorNo() == 0)
        {
            Cookie::set('WIDGET_POLL_' . $poll_id, 1, time() + 3600 * 24 * 30 * 12 * 10);
        }
    }

    function qry_all_spotlight($pos_id)
    {
        $v_website_id = $this->website_id;
        $old_mode = $this->db->fetchMode;
        $this->db->SetFetchMode(ADODB_FETCH_NUM);
        if (DATABASE_TYPE == 'MSSQL')
        {
            $sql = "Select S.FK_CATEGORY, S.FK_ARTICLE, A.C_SLUG, A.C_FILE_NAME, A.C_SUMMARY, A.C_PEN_NAME
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
        else if (DATABASE_TYPE == 'MYSQL')
        {
            $sql = "Select S.FK_CATEGORY, S.FK_ARTICLE, A.C_SLUG, A.C_FILE_NAME, A.C_SUMMARY, A.C_PEN_NAME
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
                    And DATEDIFF(NOW(), A.C_BEGIN_DATE ) >= 0
                    And DATEDIFF(A.C_END_DATE, NOW()) >= 0
                    And A.C_STATUS = 3
                    And C.C_STATUS = 1
                    Order By S.C_ORDER
                    ";
        }

        $a = $this->db->getAll($sql);
        $this->db->SetFetchMode($old_mode);
        return $a;
    }

    function qry_new_video($limit)
    {
        //return Array();//Tunning for VinhPhucTV


        $fetch_mode = $this->db->fetchMode;
        $this->db->SetFetchMode(ADODB_FETCH_NUM);
        $v_website_id = $this->website_id;
        if (DATABASE_TYPE == 'MSSQL')
        {
            $sql = "
                Select TOP $limit A.PK_ARTICLE, A.C_SLUG,A.C_TITLE, C.PK_CATEGORY
                    , C.C_SLUG as C_CAT_SLUG, A.C_FILE_NAME, A.C_CONTENT
                From t_ps_category C
                Inner Join (
                            Select Max(FK_CATEGORY) as FK_CATEGORY, FK_ARTICLE 
                            From t_ps_category_article Group By FK_ARTICLE
                            ) CA
                On CA.FK_CATEGORY = C.PK_CATEGORY
                Inner Join t_ps_article A
                On CA.FK_ARTICLE = A.PK_ARTICLE
                 Where C.C_IS_VIDEO = 1
                 And C.FK_WEBSITE = $v_website_id
                 And C.C_STATUS = 1
                 And A.C_STATUS = 3
                 And DateDiff(mi, A.C_BEGIN_DATE, getDate()) >= 0
                 And DateDiff(mi, getDate(), A.C_END_DATE) >= 0
                 Order by A.C_BEGIN_DATE Desc
            ";
        }
        else if (DATABASE_TYPE == 'MYSQL')
        {
            $sql = "
                Select
                    A.PK_ARTICLE,
                    A.C_SLUG,
                    A.C_TITLE,
                    C.PK_CATEGORY,
                    C.C_SLUG      as C_CAT_SLUG,
                    A.C_FILE_NAME,
                    A.C_CONTENT,
                    C.C_NAME AS C_CAT_TITLE,
                    A.C_SUMMARY,
                    A.C_BEGIN_DATE
                  From t_ps_category C
                    right Join t_ps_article A use index (C_DEFAULT_CATEGORY)
                      On C.PK_CATEGORY = A.C_DEFAULT_CATEGORY
                  Where C.C_IS_VIDEO = 1
                      And C.FK_WEBSITE = $v_website_id
                      And C.C_STATUS = 1
                      And A.C_STATUS = 3
                      And DATEDIFF(NOW(),A.C_BEGIN_DATE) >= 0
                      And DATEDIFF(A.C_END_DATE, NOW()) >= 0
                  Order by A.C_BEGIN_DATE Desc
                  limit $limit
            ";
        }
        $a = $this->db->getAll($sql);
        $this->db->SetFetchMode($fetch_mode);
        return $a;
    }

    function qry_new_photo_gallery($limit)
    {
        $fetch_mode = $this->db->fetchMode;
        $this->db->SetFetchMode(ADODB_FETCH_NUM);
        $website_id = $this->website_id;
        if (DATABASE_TYPE == 'MSSQL')
        {
            $sql = "
                Select Top $limit PG.PK_PHOTO_GALLERY, PG.C_TITLE, PG.C_SLUG, PG.C_FILE_NAME, PG.C_SUMMARY
                From t_ps_photo_gallery PG
                Where PG.FK_WEBSITE = $website_id
                And PG.C_STATUS = 3
                Order by PG.C_BEGIN_DATE Desc
            ";
        }
        else if (DATABASE_TYPE == 'MYSQL')
        {
            $sql = "
                Select PG.PK_PHOTO_GALLERY, PG.C_TITLE, PG.C_SLUG, PG.C_FILE_NAME, PG.C_SUMMARY
                From t_ps_photo_gallery PG
                Where PG.FK_WEBSITE = $website_id
                And PG.C_STATUS = 3
                Order by PG.C_BEGIN_DATE Desc Limit $limit
            ";
        }

        $a = $this->db->getAll($sql);
        $this->db->SetFetchMode($fetch_mode);
        return $a;
    }

    function send_comment()
    {
        $comment_id = get_post_var('comment_id', 0);
        if ($comment_id == 0)
        {
            $v_email = get_post_var('txt_email');
            $v_name = get_post_var('txt_name');
            $v_title = get_post_var('txt_title');
            $v_comment = get_post_var('txt_comment');
            $v_article_id = (int) get_post_var('hdn_article');
            $v_parent_id = (int) get_post_var('hdn_parent');
            $v_like = 0;
            $v_init_date = date('Y-m-d H:i');

            if (!$v_name or ! $v_comment or ! $v_article_id)
            {
                return __('please fill all required fields');
            }
            $sql = "
                Insert Into t_ps_article_comment (FK_ARTICLE,C_GUEST_NAME, C_GUEST_EMAIL, C_TITLE, C_COMMENT, C_INIT_DATE, C_STATUS,C_LIKE,FK_PARENT)
                Values(?, ?, ?, ?, ?, ?, ?, ?, ?)
            ";
            $param = array($v_article_id, $v_name, $v_email, $v_title, $v_comment, $v_init_date, 0, $v_like, $v_parent_id);
            $this->db->Execute($sql, $param);
            if ($this->db->errorNo() == 0)
            {
                return __('your comment has been sent');
            }
            else
            {
                return __('error occurs during sending');
            }
        }
        else
        {

            $sql = "select
                        COUNT(PK_ARTICLE_COMMENT),C_LIKE
                      from t_ps_article_comment
                      where PK_ARTICLE_COMMENT = $comment_id";
            $result = $this->db->getRow($sql);
            if (count($result) > 0)
            {
                $v_status_like = Cookie::get('LIKE_' . $comment_id);
                // su sung chuc nang like lan 1
                if ($v_status_like == '' OR $v_status_like == NULL)
                {
                    $v_like = (int) $result['C_LIKE'] + 1;
                    $v_status_like = 1;
                }
                else
                {
                    // su sung chuc nang like lan 2
                    if ($v_status_like == 1)
                    {
                        $v_like = ((int) $result['C_LIKE'] > 0 ) ? (int) $result['C_LIKE'] - 1 : 0;
                        $v_status_like = 2;
                    }
                    else
                    {
                        // da su dung 2 lan chuc nang like trong vong 24h
                        return 'FALSE';
                    }
                }
                $sql = "update  t_ps_article_comment
                          set C_LIKE = $v_like
                          where PK_ARTICLE_COMMENT = $comment_id";
                $this->db->Execute($sql);
            }
            if ($this->db->errorNo() > 0)
            {
//                    return 'there was an error in the selection process';
            }
            else
            {
                //Khoi tao Cookie luu thoi gian  va trang thai su dung chuc năng like. Thiet lap 5 phút
                Cookie::set('LIKE_' . $comment_id, $v_status_like, time() + 300);
                $sql = "select
                                   COUNT(PK_ARTICLE_COMMENT),C_LIKE
                              from t_ps_article_comment
                              where PK_ARTICLE_COMMENT = $comment_id";
                $result = $this->db->getRow($sql);
                return $result['C_LIKE'];
            }
        }
    }

    function qry_all_comment($article_id)
    {
        $_POST['sel_rows_per_page'] = _CONST_DEFAULT_ROWS_PER_PAGE;
        $_POST['sel_goto_page'] = (int) get_request_var('page', 1);
        page_calc($v_start, $v_end);

        if (DATABASE_TYPE == 'MSSQL')
        {
            $stmt = "
                Select C_TITLE,C_COMMENT, C_GUEST_NAME, C_GUEST_EMAIL
                , Convert(varchar(50), C_INIT_DATE, 120) as C_INIT_DATE
                , (ROW_NUMBER() OVER (ORDER BY C_INIT_DATE desc) ) as RN
                , COUNT(*) OVER(PARTITION BY 1) as TOTAL_RECORD
                From t_ps_article_comment Where FK_ARTICLE = $article_id And C_STATUS = 1
                ";
            $stmt = "Select Temp.* From ($sql) Temp Where Temp.RN Between $v_start and $v_end";
        }
        else if (DATABASE_TYPE == 'MYSQL')
        {
            $v_start = $v_start - 1;
            $v_limit = $v_end - $v_start;
            $sql = "select COUNT(PK_ARTICLE_COMMENT) 
                    from t_ps_article_comment 
                    where C_STATUS =1 
                          and FK_PARENT = ac.PK_ARTICLE_COMMENT";
            $stmt = "  SELECT ac.*
                        ,(select CONCAT('<data>',
                                        GROUP_CONCAT('<item '
                                                        ,CONCAT('PK_ARTICLE_COMMENT =\"', PK_ARTICLE_COMMENT ,'\" ')
                                                        ,CONCAT('C_STATUS =\"', C_STATUS ,'\" ')
                                                        ,CONCAT('C_TITLE =\"', C_TITLE ,'\" ')
                                                        ,CONCAT('C_GUEST_EMAIL =\"', C_GUEST_EMAIL ,'\" ')
                                                        ,CONCAT('C_GUEST_NAME =\"', C_GUEST_NAME ,'\" ')
                                                        ,CONCAT('C_INIT_DATE =\"', C_INIT_DATE ,'\" ')
                                                        ,CONCAT('FK_ARTICLE =\"', FK_ARTICLE ,'\" ')
                                                        ,CONCAT('C_COMMENT =\"', C_COMMENT ,'\" ')
                                                        ,CONCAT('C_LIKE =\"', C_LIKE ,'\" ')
                                                        ,CONCAT('FK_PARENT =\"', FK_PARENT ,'\" ')
                                                        ,CONCAT('TOTAL_RECORD =\"',( $sql),'\" ')
                                                        ,'/>'
                                        SEPARATOR ''),'</data>') 
                          from t_ps_article_comment 
                          WHERE  FK_PARENT = ac.PK_ARTICLE_COMMENT
                                AND C_STATUS = 1
                                ORDER BY C_LIKE DESC,
                                     C_INIT_DATE DESC
                          )as C_COMMENT_CHILD 
                        ,(SELECT COUNT(PK_ARTICLE_COMMENT)
                          FROM t_ps_article_comment ac
                          WHERE FK_ARTICLE = $article_id
                                AND C_STATUS = 1) AS TOTAL_ALL_RECOR
                        ,(SELECT COUNT(PK_ARTICLE_COMMENT)
                          FROM t_ps_article_comment ac
                          WHERE FK_ARTICLE = $article_id
                                AND C_STATUS = 1
                                AND FK_PARENT =0) AS TOTAL_RECOR
                FROM t_ps_article_comment ac
                WHERE FK_ARTICLE = $article_id
                AND FK_PARENT = 0
                AND C_STATUS = 1
                ORDER BY C_LIKE DESC,
                     C_INIT_DATE DESC
                Limit $v_start,$v_end";
        }
        return $this->db->getAll($stmt);
    }

    function update_article_views($article_id)
    {
        $sql = "Update t_ps_article Set C_VIEWS = C_VIEWS + 1 Where PK_ARTICLE = $article_id";
        $this->db->Execute($sql);
    }

    function qry_all_article()
    {
        $website_id = $this->website_id;
        $v_keywords = trim(get_request_var('keywords'));
        $v_keywords = preg_replace("/[,\.,\;,\,()'\"&#]/", ' ', $v_keywords);
        $v_keywords = preg_replace('/( )+/', '%', trim($v_keywords));
        //$v_keywords     = '"' . $v_keywords . '"';
        $v_begin_date = get_request_var('begin_date');
        $v_end_date = get_request_var('end_date');
        $category_id = (int) get_request_var('category', '');
        $_POST['sel_rows_per_page'] = _CONST_DEFAULT_ROWS_PER_PAGE;
        $_POST['sel_goto_page'] = (int) get_request_var('page', 1);
        page_calc($v_start, $v_end);

        $new_article_mode = get_system_config_value(CFGKEY_NEW_ARTICLE_ICON) ? 0 : 1;
        $new_article_cond = (int) get_system_config_value(CFGKEY_NEW_ARTICLE_COND);
        //datetime
        $v_begin_date = date_create_from_format('d/m/Y', $v_begin_date);
        if ($v_begin_date)
        {
            $v_begin_date = $v_begin_date->format('Y-m-d');
        }
        else
        {
            $v_begin_date = '';
        }
        $v_end_date = date_create_from_format('d/m/Y', $v_end_date);
        if ($v_end_date)
        {
            $v_end_date = $v_end_date->format('Y-m-d');
        }
        else
        {
            $v_end_date = '';
        }
        $sql_id_article = "
            Select A.PK_ARTICLE, A.C_BEGIN_DATE, C.PK_CATEGORY
                , ROW_NUMBER() Over(Order By A.C_BEGIN_DATE Desc) as RN
                , Count(*) Over (Partition by 1) as TOTAL_RECORD
            From t_ps_article A
            Inner Join (
                Select Max(FK_CATEGORY) as FK_CATEGORY, FK_ARTICLE 
                From t_ps_category_article Group By FK_ARTICLE
            ) CA
            On A.PK_ARTICLE = CA.FK_ARTICLE
            Left Join t_ps_category C
            On CA.FK_CATEGORY = C.PK_CATEGORY
            Where A.C_STATUS = 3
            And DateDiff(dd, A.C_BEGIN_DATE, getDate()) >= 0
            And DateDiff(dd, getDate(), A.C_END_DATE) >= 0
            And ( DateDiff(dd, '$v_begin_date', A.C_BEGIN_DATE) >=0 Or '$v_begin_date' = '' )
            And ( DateDiff(dd, A.C_BEGIN_DATE, '$v_end_date') >= 0 Or '$v_begin_date' = '' )
            And C.FK_WEBSITE = $website_id
            And ( C.PK_CATEGORY = $category_id Or $category_id = 0)
            And C.C_STATUS = 1
        ";
        if ($v_keywords)
        {
            $sql_id_article .= "And Contains((C_TITLE, C_TAGS, C_SUMMARY, C_CONTENT, C_KEYWORDS), '$v_keywords', language 1066)";
        }
        $sql_id_article = "Select Temp.* From ($sql_id_article) TEMP Where TEmp.RN Between $v_start And $v_end";
        $sql = "
            Select 
                A.PK_ARTICLE, A.C_TITLE, A.C_SLUG, A.C_SUMMARY, A.C_BEGIN_DATE, A.C_FILE_NAME
                , C.PK_CATEGORY, C.C_SLUG as C_CAT_SLUG
                , (dateDiff(dd, A.C_BEGIN_DATE, getDate()) - $new_article_cond) As CK_NEW_ARTICLE
                , A.C_HAS_VIDEO, A.C_HAS_PHOTO
                ,SUB_A.*
            From ($sql_id_article) SUB_A
            Inner Join t_ps_article A
            On SUB_A.PK_ARTICLE = A.PK_ARTICLE
            Left Join t_ps_category C
            On SUB_A.PK_CATEGORY = C.PK_CATEGORY
        ";
        return $this->db->getAll($sql);
    }

    //Lien:
    public function qry_all_article_by_fulltext_search()
    {
        $website_id = $this->website_id;

        $v_keywords = trim(get_request_var('keywords'));
        $v_keywords = preg_replace("/[,\.,\;,\,()'\"&#]/", ' ', $v_keywords);
//        $v_keywords                 = preg_replace('/( )+/', '%', trim($v_keywords));

        $category_id = (int) get_request_var('category', 0);

        $_POST['sel_rows_per_page'] = _CONST_DEFAULT_ROWS_PER_PAGE;
        $_POST['sel_goto_page'] = (int) get_request_var('page', 1);
        page_calc($v_start, $v_end);

        //Loc theo ngay
        $v_date_condition = "";
        $v_begin_date = get_request_var('begin_date', '');
        $v_end_date = get_request_var('end_date', '');

        if ($v_begin_date != '')
        {
            $v_begin_date = jwDate::ddmmyyyy_to_yyyymmdd($v_begin_date);
            $v_date_condition .= " AND A.C_BEGIN_DATE >= $v_begin_date";
        }
        if ($v_end_date != '')
        {
            $v_end_date = jwDate::ddmmyyyy_to_yyyymmdd($v_end_date);
            $v_date_condition .= " AND $v_end_date >= A.C_BEGIN_DATE";
        }
        if (DATABASE_TYPE == 'MYSQL')
        {
            $v_start = $v_start - 1;
            $v_limit = $v_end - $v_start;

            $fulltext_condition = '';
            if ($v_keywords != '' && $v_keywords != NULL)
            {
//                $fulltext_condition = " AND ( MATCH(C_TITLE,C_SUMMARY,C_CONTENT,C_SUB_TITLE) AGAINST('$v_keywords'IN BOOLEAN MODE)
//                                       )";
                $fulltext_condition = " And ( C_TITLE like('%$v_keywords%') OR C_SUMMARY like ('%$v_keywords%') OR C_SUB_TITLE like ('%$v_keywords%'))";
            }

            //condition neu co category
            $sql_join = '';
            $category_condition = '';
            if ($category_id == 0)
            {
                $sql_join = "left join t_ps_category C
                               On A.C_DEFAULT_CATEGORY = C.PK_CATEGORY";
            }
            else
            {
                $sql_join = "left join t_ps_category_article CA
                               ON A.PK_ARTICLE = CA.FK_ARTICLE
                           left join t_ps_category C
                               ON CA.FK_CATEGORY = C.PK_CATEGORY";
                $category_condition = " AND CA.FK_CATEGORY = $category_id";
            }
            //lay tong so tin bai (TOTAL_RECORD)
            $sql = "SELECT
                    COUNT(A.PK_ARTICLE) 
                  FROM t_ps_article A use index(IDX_FULLTEXT_SEARCH)
                  $sql_join
                  WHERE (1 > 0) $fulltext_condition
                      AND A.C_STATUS = 3
                      AND NOW()>= A.C_BEGIN_DATE
                      AND A.C_END_DATE >= NOW()
                      AND C_DEFAULT_WEBSITE = $website_id 
                      And C.C_STATUS = 1
                       $v_date_condition $category_condition";

            $sql = "SELECT 
                        ($sql) AS TOTAL_RECORD,
                        A.PK_ARTICLE,
                        A.C_TITLE,
                        A.C_SUMMARY,
                        A.C_SLUG,
                        A.C_BEGIN_DATE,
                        A.C_DEFAULT_CATEGORY AS PK_CATEGORY,
                        A.C_HAS_VIDEO,
                        A.C_HAS_PHOTO,
                        (SELECT
                           C_SLUG
                         FROM t_ps_category
                         WHERE PK_CATEGORY = A.C_DEFAULT_CATEGORY) AS C_CAT_SLUG,
                        C_FILE_NAME,
                        0                 CK_NEW_ARTICLE
                      FROM t_ps_article A use index(IDX_FULLTEXT_SEARCH)
                      $sql_join
                      WHERE (1 > 0)
                                $fulltext_condition
                                AND A.C_STATUS = 3
                                AND NOW()>= A.C_BEGIN_DATE
                                AND A.C_END_DATE >= NOW()
                                And A.C_DEFAULT_WEBSITE = $website_id
                                And C.C_STATUS = 1
                                $v_date_condition $category_condition
                                Order By A.C_BEGIN_DATE
                            LIMIT $v_start,$v_limit";
        }

        return $this->db->getAll($sql);

        /*
          $sql_matched_record = 'Select
          m.PK_ARTICLE
          ,TOTAL_RECORD
          From
          (
          Select
          FT_TBL.PK_ARTICLE
          , ROW_NUMBER() Over(Order by KEY_TBL.RANK Desc) as RN
          , Count(*) Over (Partition by 1) as TOTAL_RECORD
          From t_ps_article AS FT_TBL Inner Join CONTAINSTABLE (t_ps_article,*,'$v_keywords') AS KEY_TBL On FT_TBL.PK_ARTICLE = KEY_TBL.[KEY]
          Where FT_TBL.C_STATUS = 3
          And DateDiff(dd, FT_TBL.C_BEGIN_DATE, getDate()) >= 0
          And DateDiff(dd, getDate(), FT_TBL.C_END_DATE) >= 0';
          //Dieu kien loc ve ngay
          if ($v_begin_date != '')
          {
          " And (DateDiff(dd, '$v_begin_date', FT_TBL.C_BEGIN_DATE) >=0) ";
          }
          if ($v_end_date != '')
          {
          " And ( DateDiff(dd, FT_TBL.C_BEGIN_DATE, '$v_end_date') >= 0 ";
          }



          if (is_integer($category_id))
          {
          $sql_matched_record .= ' And ';
          }

          $params = array($v_keywords);
         */
    }

    function qry_all_category()
    {
        $website_id = $this->website_id;
        $sql = "
            Select PK_CATEGORY, C_NAME, C_INTERNAL_ORDER, C_SLUG
            From t_ps_category
            Where FK_WEBSITE = $website_id
            Order By C_INTERNAL_ORDER
        ";
        return $this->db->getAll($sql);
    }

    function qry_single_spotlight_pos($id)
    {
        $sql = "Select C_NAME From t_ps_spotlight_position Where PK_SPOTLIGHT_POSITION = $id";
        return $this->db->getRow($sql);
    }

    function qry_all_cq($website_id)
    {
        $v_status = get_request_var('status', '');
        if (intval($v_status) == 1)
        {
            unset($_REQUEST['txt_question_name']);
            unset($_REQUEST['field_id']);

//            unset($_REQUEST['sel_listype']); Dung loc theo loai cau hoi khong dung
            unset($_REQUEST['submit']);
            unset($_REQUEST['txt_user_send_name']);
        }
        $v_question_name = get_request_var('txt_question_name', '');
        $v_question_name = htmlspecialchars($v_question_name);
        $v_field_id = get_request_var('field_id', '');

//        Dung loc theo loai cau hoi khong dung
//        $v_listype             = get_request_var('sel_listype','') ;
//        if($v_listype == 0)
//        {
//            $v_listype = $v_field_id;
//        }

        $v_user_send_name = get_request_var('txt_user_send_name', '');

        $v_conditon = '';
        if (trim($v_question_name) != '')
        {
            $v_conditon .= " And c.C_TITLE like ('%$v_question_name%') ";
        }
//        Dung loc theo loai cau hoi khong dung
//        if(intval($v_listype) > 0 ) 
//        {
//            $v_conditon .= " And c.FK_FIELD = '$v_listype' ";
//        }
        if (trim($v_user_send_name) != '')
        {
            $v_conditon .= " And c.C_NAME like ('%$v_user_send_name%') ";
        }

        if (intval($v_field_id) > 0)
        {
            $v_conditon .= " And c.FK_FIELD = '$v_field_id' ";
        }

        $v_page = get_request_var('page', 1);
        $v_page = ($v_page <= 0) ? 1 : $v_page;
        $v_limit = defined('_CONST_DEFAULT_ROWS_FIELD') ? _CONST_DEFAULT_ROWS_FIELD : 10;

        $v_start = ($v_limit * ($v_page - 1));

        $website_id = replace_bad_char($website_id);
        $stmt = "select a.* from (SELECT
                                        c.PK_CQ,
                                        c.FK_FIELD,
                                        c.C_NAME,
                                        c.C_ADDRESS,
                                        c.C_PHONE,
                                        c.C_EMAIL,
                                        c.C_TITLE,
                                        c.C_CONTENT,
                                        c.C_ANSWER,
                                        c.C_STATUS,
                                        c.C_ORDER,
                                        c.C_DATE,
                                        c.C_SLUG,
                                        cf.FK_WEBSITE,
                                        (SELECT C_NAME FROM t_ps_website WHERE cf.FK_WEBSITE = PK_WEBSITE AND C_STATUS =1) AS C_WEBSITE_NAME,
                                        cf.C_NAME as C_FIELD_NAME,
                                        (select count(PK_CQ) FROM t_ps_cq c
                                        LEFT JOIN t_ps_cq_field cf
                                          ON c.FK_FIELD = cf.PK_FIELD
                                      WHERE c.C_STATUS = 1
                                          AND cf.FK_WEBSITE ='$website_id'
                                          AND cf.C_STATUS = 1 ) as C_TOTAL
                                    FROM t_ps_cq c
                                        LEFT JOIN t_ps_cq_field cf
                                          ON c.FK_FIELD = cf.PK_FIELD
                                      WHERE c.C_STATUS = 1
                                          AND cf.FK_WEBSITE ='$website_id'
                                          AND cf.C_STATUS = 1
                                          $v_conditon
                                      ORDER BY c.PK_CQ DESC) a
                      Limit  $v_start,$v_limit ";

        return $this->db->getAll($stmt);
    }

    public function qry_single_cq()
    {
        $cq_id = get_request_var('cq_id');
        $v_limit = _CONST_DEFAULT_ROWS_CQ;
        $stmt = "SELECT q.PK_CQ,
                    q.FK_FIELD,
                    q.C_NAME,
                    q.C_ADDRESS,
                    q.C_PHONE,
                    q.C_EMAIL,
                    q.C_TITLE,
                    q.C_CONTENT,
                    q.C_ANSWER,
                    q.C_STATUS,
                    q.C_ORDER,                    
                    DATE_FORMAT(q.C_DATE,'%d-%m-%Y') AS C_DATE,
                    q.C_SLUG,
                    f.C_NAME AS C_NAME_FIELD
             FROM t_ps_cq q
             INNER JOIN t_ps_cq_field f ON q.FK_FIELD = f.PK_FIELD
             WHERE q.PK_CQ = ?
               AND q.C_STATUS = 1
               
            ";
        $MODEL_DATA['arr_single_cq'] = $this->db->getRow($stmt, array($cq_id));
        $v_field_id = isset($MODEL_DATA['arr_single_cq']['FK_FIELD']) ? $MODEL_DATA['arr_single_cq']['FK_FIELD'] : 0;

        $sql = "SELECT q.PK_CQ,
                    q.FK_FIELD,
                    q.C_NAME,
                    q.C_ADDRESS,
                    q.C_PHONE,
                    q.C_EMAIL,
                    q.C_TITLE,
                    q.C_CONTENT,
                    q.C_ANSWER,
                    q.C_STATUS,
                    q.C_ORDER,                    
                    DATE_FORMAT(q.C_DATE,'%d-%m-%Y') AS C_DATE,
                    q.C_SLUG,
                    f.C_NAME AS C_NAME_FIELD
             FROM t_ps_cq q
             INNER JOIN t_ps_cq_field f ON q.FK_FIELD = f.PK_FIELD
             WHERE q.PK_CQ < ?
               AND q.C_STATUS = 1
               And q.FK_FIELD = ?
               Order By q.FK_FIELD DESC
               limit 10
            ";
        $MODEL_DATA['arr_cq_connection'] = $this->db->GetAll($sql, array($cq_id, $v_field_id));
        return $MODEL_DATA;
    }

    public function qry_all_cq_field($website_id)
    {
        $website_id = replace_bad_char($website_id);

        $stmt = "SELECT PK_FIELD,
                        C_STATUS,
                        C_ORDER,
                        C_NAME,
                        FK_WEBSITE,
                        DATE_FORMAT(C_DATE,'%d-%m-%Y') AS C_DATE,
                        (SELECT COUNT(PK_CQ) FROM t_ps_cq WHERE FK_FIELD = cf.PK_FIELD AND C_STATUS =1) AS Count_QUESTION
                 FROM t_ps_cq_field cf
                 WHERE C_STATUS = 1
                   AND FK_WEBSITE = $website_id order by C_ORDER";

        return $this->db->getAll($stmt);
    }

    public function insert_cq($website_id)
    {
        $this->db->debug = 0;
        $website_id = replace_bad_char($website_id);

        $v_field_id = get_post_var('select_field');
        $v_name = get_post_var('txt_name');
        $v_address = get_post_var('txt_address');
        $v_phone = get_post_var('txt_phone');
        $v_email = get_post_var('txt_email');
        $v_title = get_post_var('txt_title');
        $v_content = get_post_var('txt_content');
        $v_order = '-1';
        $v_slug = auto_slug($v_title);
        $other_clause = "FK_FIELD in (select PK_FIELD FROM t_ps_cq_field where FK_WEBSITE = $website_id)";
        $stmt = "select FK_WEBSITE from t_ps_cq_field where PK_FIELD = $v_field_id";
        $check = $this->db->getOne($stmt);

        if ($check == $website_id)
        {
            if (DATABASE_TYPE == 'MSSQL')
            {
                $stmt = "INSERT INTO t_ps_cq (FK_FIELD,
                    C_NAME, 
                    C_ADDRESS, 
                    C_PHONE, 
                    C_EMAIL, 
                    C_TITLE, 
                    C_CONTENT,
                    C_STATUS,
                    C_ORDER,
                    C_DATE,
                    C_SLUG)
                    VALUES (?,?,?,?,?,?,?,?,?,GETDATE(),?)";
                $param = array($v_field_id,$v_name,$v_address, $v_phone, $v_email, $v_title, $v_content, '0', $v_order, $v_slug);
            }
            else if (DATABASE_TYPE == 'MYSQL')
            {
                $stmt = "INSERT INTO t_ps_cq (FK_FIELD,
                    C_NAME, 
                    C_ADDRESS, 
                    C_PHONE, 
                    C_EMAIL, 
                    C_TITLE, 
                    C_CONTENT,
                    C_STATUS,
                    C_ORDER,
                    C_DATE,
                    C_SLUG)
                    VALUES (?,?,?,?,?,?,?,?,?,NOW(),?)";
                $param = array($v_field_id,  $v_name, $v_address, $v_phone, $v_email, $v_title, $v_content, '0', $v_order, $v_slug);
            }

            $this->db->Execute($stmt, $param);

            $stmt = "select PK_CQ From t_ps_cq order by PK_CQ DESC limit 1";
            $v_question_id = $this->db->getOne($stmt);
            $this->ReOrder('t_ps_cq', 'PK_CQ', 'C_ORDER', $v_question_id, '1', '-1', $other_clause);

            if ($this->db->errorNo() == 0)
            {
                return '1';
            }
        }
        return '0';
    }

    public function qry_single_cq_field($website_id, $field_id)
    {
        $v_page = get_request_var('page', 1);
        $v_start = _CONST_DEFAULT_ROWS_CQ * ($v_page - 1) + 1;
        $v_end = $v_start + _CONST_DEFAULT_ROWS_CQ - 1;

        $website_id = replace_bad_char($website_id);
        $field_id = replace_bad_char($field_id);

        $sql = " SELECT CONCAT('<data>'
			,GROUP_CONCAT('<Raw '
						,CONCAT(' rn=\"', @curRow := @curRow + 1,'\" ')
						,CONCAT(' PK_CQ=\"',PK_CQ,'\" ') 						
						,CONCAT(' FK_FIELD=\"',FK_FIELD,'\" ')
						,CONCAT(' C_NAME=\"',C_NAME,'\" ')
						,CONCAT(' C_ADDRESS=\"',C_ADDRESS,'\" ')
						,CONCAT(' C_PHONE=\"',C_PHONE,'\" ')
						,CONCAT(' C_EMAIL=\"',C_EMAIL,'\" ')
						,CONCAT(' C_TITLE=\"',C_TITLE,'\" ') 
						,CONCAT(' C_CONTENT=\"',C_CONTENT,'\" ')
						,CONCAT(' C_ANSWER=\"', IFNULL(C_ANSWER, ''),'\" ')
						,CONCAT(' C_STATUS=\"',C_STATUS,'\" ')
						,CONCAT(' C_ORDER=\"',C_ORDER,'\" ')
						,CONCAT(' C_DATE=\"',DATE_FORMAT(C_DATE,'%d-%m-%Y') ,'\" ')
						,CONCAT(' C_SLUG=\"',C_SLUG,'\" ')
                                    ,' />' SEPARATOR ' ')
                        ,'</data>')  	
                FROM t_ps_cq q
                JOIN
                  (SELECT @curRow := 0) r 
                          WHERE FK_FIELD = f.PK_FIELD  
                          LIMIT $v_start,$v_end ";
        $stmt = "select f.PK_FIELD,f.C_STATUS,f.C_ORDER,f.C_NAME,f.FK_WEBSITE,date_format(f.C_DATE,'%d-%m-%Y') as C_DATE ,
                                ($sql)as C_XML_DATA,
                       (select COUNT(*) from t_ps_cq where FK_FIELD = f.PK_FIELD and C_STATUS = 1) as TOTAL_RECORD 
                       from t_ps_cq_field f where PK_FIELD = ? and FK_WEBSITE=? and f.C_STATUS = 1
                       ";
        return $this->db->getRow($stmt, array($field_id, $website_id));
    }

    /**
     * Lay danh sach tin bai moi nhat cua chuyen trang
     * @param int $website_id ID chuyen trang
     */
    public function qry_all_latest_article_by_website($website_id)
    {
        //return array();
        $website_id = replace_bad_char($website_id);
        $v_limit = _CONST_DEFAULT_LIMIT_ARTICLE_NEW;

        //mca max_category_article       
        /*
          $stmt = 'Select
          FA.PK_ARTICLE
          ,FA.C_SLUG as C_SLUG_ART
          ,FA.C_TITLE
          ,top10.FK_CATEGORY as PK_CATEGORY
          ,(Select C_SLUG From t_ps_category Where PK_CATEGORY=top10.FK_CATEGORY) as C_SLUG_CAT
          , ? as FK_WEBSITE
          From t_ps_article FA
          Right Join ( Select
          all_id.PK_ARTICLE
          , all_id.RN
          , all_id.FK_CATEGORY
          From ( Select
          PK_ARTICLE
          , ROW_NUMBER() Over(Order by C_BEGIN_DATE Desc) as RN
          ,FK_CATEGORY
          From t_ps_article A
          Right Join ( Select
          Max(FK_CATEGORY) as FK_CATEGORY
          , CA.FK_ARTICLE
          From t_ps_category_article as CA
          Left Join t_ps_category as C
          On CA.FK_CATEGORY=C.PK_CATEGORY
          Where C.FK_WEBSITE=?
          Group By     FK_ARTICLE
          ) mca
          On mca.FK_ARTICLE=A.PK_ARTICLE
          Where A.C_STATUS=3
          And dateDiff(mi, A.C_BEGIN_DATE, getDate()) >= 0
          And dateDiff(mi, getDate(), A.C_END_DATE) >= 0
          And dateDiff(day, A.C_BEGIN_DATE, getDate()) <= 3
          ) all_id
          Where all_id.RN>=1 and all_id.RN<=?
          ) top10
          On FA.PK_ARTICLE=top10.PK_ARTICLE';
         */


        if (DATABASE_TYPE == 'MSSQL')
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
        else if (DATABASE_TYPE == 'MYSQL')
        {
//            $stmt = "SELECT
//                            FA.PK_ARTICLE,
//                            FA.C_SLUG         AS C_SLUG_ART,
//                            FA.C_TITLE,
//                            top10.FK_CATEGORY AS PK_CATEGORY,
//                            (SELECT
//                               C_SLUG
//                             FROM t_ps_category
//                             WHERE PK_CATEGORY = top10.FK_CATEGORY) AS C_SLUG_CAT,
//                            ?              AS FK_WEBSITE
//                          FROM t_ps_article FA
//                            RIGHT JOIN (SELECT
//                                            PK_ARTICLE,
//                                            C_DEFAULT_CATEGORY AS FK_CATEGORY
//                                          FROM t_ps_article
//                                          WHERE C_DEFAULT_WEBSITE = ?
//                                              AND DATEDIFF(NOW(),C_BEGIN_DATE) >= 0
//                                              AND DATEDIFF(C_END_DATE, NOW()) >= 0
//                                              AND DATEDIFF(NOW(),C_BEGIN_DATE) <= 3
//                                          LIMIT 0,?) top10
//                              ON FA.PK_ARTICLE = top10.PK_ARTICLE";

            $stmt = "SELECT FA.PK_ARTICLE,
                            FA.C_SLUG As C_SLUG_ART,
                            FA.C_TITLE,
                            FA.C_DEFAULT_CATEGORY As PK_CATEGORY,
                            (SELECT
                               C_SLUG
                             FROM t_ps_category
                             WHERE PK_CATEGORY = FA.C_DEFAULT_CATEGORY) AS C_SLUG_CAT,
                             ? AS FK_WEBSITE
                     FROM t_ps_article FA
                     RIGHT JOIN
                       (SELECT A.PK_ARTICLE,
                               A.C_BEGIN_DATE
                        FROM
                          (SELECT PK_ARTICLE,
                                  C_BEGIN_DATE
                           FROM t_ps_article
                           FORCE INDEX(C_BEGIN_DATE)
                           WHERE C_DEFAULT_WEBSITE = ?
                             AND DATEDIFF(NOW(),C_BEGIN_DATE) >= 0
                             AND DATEDIFF(C_END_DATE, NOW()) >= 0
                             AND C_STATUS = 3
                           ORDER BY C_BEGIN_DATE DESC LIMIT ?) A
                        WHERE DATEDIFF(NOW(),A.C_BEGIN_DATE) <= 3) top10 ON FA.PK_ARTICLE = top10.PK_ARTICLE";

            $arr_param = array($website_id, $website_id, $v_limit);
        }


        $arr_all_latest_article_by_website = $this->db->getAll($stmt, $arr_param);

        return $arr_all_latest_article_by_website;
    }

//end func qry_all_latest_article_by_website

    public function qry_all_attachment($articleid)
    {
        $sql = "Select C_FILE_NAME 
            From t_ps_article_attachment
            WHERE FK_ARTICLE = ?";
        return $this->db->getAll($sql, array($articleid));
    }

    /**
     * Lay danh sach tin bai cu hon TIN BAI DANG XEM CHI TIET (Cac tin khac)
     * @param unknown $categoryid
     * @param unknown $articleid
     */
    public function qry_all_other_article($categoryid, $articleid)
    {
        $v_default = _CONST_DEFAULT_ROWS_OTHER_NEWS;
        if (DATABASE_TYPE == 'MSSQL')
        {
            $sql = "
                    Select temp.PK_ARTICLE, C_SLUG, C_TITLE 
                        ,CONVERT(varchar(10),a.C_BEGIN_DATE,103) AS C_BEGIN_DATE 
                    FROM
                    (
                        SELECT top $v_default PK_ARTICLE
                        FROM (Select * From t_ps_category_article Where FK_CATEGORY = ?) ca 
                        INNER JOIN t_ps_article a
                        ON a.PK_ARTICLE = ca.FK_ARTICLE
                        WHERE DATEDIFF(mi,C_BEGIN_DATE, (SELECT C_BEGIN_DATE FROM t_ps_article WHERE PK_ARTICLE = ?))>=0 
                        AND PK_ARTICLE <> ? 
                        AND C_STATUS=3 
                        AND (Select C_STATUS From t_ps_category Where PK_CATEGORY = ?) = 1 
                        Order BY a.C_BEGIN_DATE DESC
                    ) temp
                    INNER JOIN t_ps_article a
                    ON temp.PK_ARTICLE = a.PK_ARTICLE
                    ";
            return $this->db->getAll($sql, array($categoryid, $articleid, $articleid, $categoryid));
        }
        else if (DATABASE_TYPE == 'MYSQL')
        {
            $v_use_index = '';
            $v_count = $this->_count_article_by_category($categoryid);
            if ($v_count > _CONST_MIN_ROW_TO_MYSQL_USE_INDEX)
            {
                $v_use_index = ' Use Index (C_BEGIN_DATE) ';
            }

            $sql = "Select
                        A.PK_ARTICLE
                        , A.C_SLUG
                        , A.C_TITLE
                        , DATE_FORMAT(A.C_BEGIN_DATE,'%d-%m-%Y') As C_BEGIN_DATE
                        , C.PK_CATEGORY
                        , C.C_SLUG      as C_CAT_SLUG
                        , A.C_FILE_NAME
                        , A.C_SUMMARY
                    From t_ps_category_article CA
                        Left Join t_ps_article A $v_use_index
                        On CA.FK_ARTICLE = A.PK_ARTICLE      
                            Left Join t_ps_category C
                            On CA.FK_CATEGORY = C.PK_CATEGORY
                    Where C.PK_CATEGORY = $categoryid
                        And A.C_BEGIN_DATE < (Select C_BEGIN_DATE From t_ps_article Where PK_ARTICLE=$articleid)
                        And A.C_STATUS = 3
                        And A.C_BEGIN_DATE < Now()
                        And A.C_END_DATE > Now()  
                        And C.C_STATUS=1   
                    Order By A.C_BEGIN_DATE Desc
                    Limit $v_default";
            return $this->db->getAll($sql);
        }
    }

    public function qry_all_article_new($website_id)
    {
        $website_id = replace_bad_char($website_id);
        $v_limit = _CONST_DEFAULT_LIMIT_ARTICLE_NEW;

        $stmt = 'Select 
                    FA.PK_ARTICLE
                    ,FA.C_SLUG as C_SLUG_ART
                    ,FA.C_TITLE
                    ,MA.FK_CATEGORY as PK_CATEGORY
                    ,(Select C_SLUG From t_ps_category Where PK_CATEGORY=MA.FK_CATEGORY) as C_SLUG_CAT
                    , ? as FK_WEBSITE
                From t_ps_article FA Right Join
                (
                    Select mrs.*
                    From
                    (
                        Select 
                            PK_ARTICLE
                            , ROW_NUMBER() Over(Order by C_BEGIN_DATE Desc) as RN         
                            ,FK_CATEGORY      
                        From t_ps_article A left join (
                            Select 
                                Max(FK_CATEGORY) as FK_CATEGORY
                                , CA.FK_ARTICLE                 
                            From t_ps_category_article as CA Left Join t_ps_category as C on CA.FK_CATEGORY=C.PK_CATEGORY
                            Where C.FK_WEBSITE=?   
                            Group By     FK_ARTICLE         
                        ) cca on cca.FK_ARTICLE=A.PK_ARTICLE
                        Where A.C_STATUS=3
                            And dateDiff(mi, A.C_BEGIN_DATE, getDate()) >= 0
                            And dateDiff(mi, getDate(), A.C_END_DATE) >= 0 
                    ) mrs
                    Where mrs.RN>=1 and mrs.RN<=?
                ) MA on FA.PK_ARTICLE=MA.PK_ARTICLE';
        return $this->db->getAll($stmt, array($website_id, $website_id, $v_limit));
    }

    function update_statistic()
    {
        Session::init();
        $ip = ip2long($_SERVER['REMOTE_ADDR']);
        $time = time();
        $count = Cookie::get('STATS_COUNTED') == 1 ? 1 : 0;
        Cookie::set('STATS_COUNTED', 1);

        if (DATABASE_TYPE == 'MSSQL')
        {
            $sql = "
                    IF EXISTS (SELECT PK_STATS_VISITORS FROM t_ps_stats_visitors WHERE C_IP = $ip)
                        UPDATE t_ps_stats_visitors SET C_TIME = $time, C_VISIT = C_VISIT + $count WHERE C_IP = $ip
                    ELSE
                        INSERT INTO t_ps_stats_visitors(C_IP, C_TIME, C_VISIT) VALUES ($ip, $time, 1)
                ";
            $this->db->Execute($sql);
        }
        else if (DATABASE_TYPE == 'MYSQL')
        {
            $v_check_exists = $this->db->getOne("SELECT count(PK_STATS_VISITORS) FROM t_ps_stats_visitors WHERE C_IP = $ip");

            if ($v_check_exists > 0)
            {
                $sql = "UPDATE t_ps_stats_visitors SET C_TIME = $time, C_VISIT = C_VISIT + $count WHERE C_IP = $ip";
                $this->db->Execute($sql);
            }
            else
            {
                $sql = "INSERT INTO t_ps_stats_visitors(C_IP, C_TIME, C_VISIT) VALUES ($ip, $time, 1)";
                $this->db->Execute($sql);
            }
        }
    }

    function get_statistic($mode)
    {
        $cur_time = time();

        if ($mode == STATS_ONLINE) //dem nhung nguoi truy cap trong vong 60s
        {
            $sql = "Select COUNT(*) From t_ps_stats_visitors";
            $sql .= " Where $cur_time - C_TIME <= 60";
        }
        elseif ($mode == STATS_ALL)
        {
            $sql = "Select Sum(C_VISIT) From t_ps_stats_visitors";
        }
        return $this->db->getOne($sql);
    }

    //lay tat ca tin bai cua widget category silde
    public function qry_all_article_of_widget_category_slide($v_category_id, $v_some_news_show)
    {
        $v_category_id = replace_bad_char($v_category_id);
        $v_some_news_show = replace_bad_char($v_some_news_show);
        $v_website_id = $this->website_id;
        $stmt = "select C_NAME,C_SLUG,PK_CATEGORY from t_ps_category where PK_CATEGORY = $v_category_id and FK_WEBSITE = $v_website_id";
        $DATA_MODEl['arr_category'] = $this->db->getRow($stmt);

        if (count($DATA_MODEl['arr_category']) > 0)
        {
            if (DATABASE_TYPE == 'MSSQL')
            {
                $stmt = "Select top $v_some_news_show Convert(varchar(10),a.C_BEGIN_DATE,103)as C_BEGIN_DATE
                                , a.C_BEGIN_DATE as C_BEGIN_DATE_YYYYMMDD
                                , a.PK_ARTICLE
                                , a.C_TITLE
                                , a.C_SUMMARY
                                , a.C_FILE_NAME
                                , a.C_SLUG 
                                , a.C_REDIRECT_URL
                             From 
                    (select 0 as order_a,FK_ARTICLE from t_ps_sticky where FK_CATEGORY = $v_category_id
                    union
                    select 1 as order_a,FK_ARTICLE from t_ps_category_article where FK_CATEGORY = $v_category_id and FK_ARTICLE not in (select FK_ARTICLE from t_ps_sticky where FK_CATEGORY = $v_category_id)) all_a 
                    inner join t_ps_article a
                    on all_a.FK_ARTICLE = a.PK_ARTICLE
                    where a.C_STATUS=3
                    And a.C_BEGIN_DATE <= GetDate()
                    And a.C_END_DATE >= GETDATE()
                    order by all_a.order_a,all_a.FK_ARTICLE desc";
            }
            else if (DATABASE_TYPE == 'MYSQL')
            {
                $stmt = "Select  DATE_FORMAT(a.C_BEGIN_DATE,'%d-%m-%Y') as C_BEGIN_DATE
                                , a.C_BEGIN_DATE as C_BEGIN_DATE_YYYYMMDD
                                , a.PK_ARTICLE
                                , a.C_TITLE
                                , a.C_SUMMARY
                                , a.C_FILE_NAME
                                , a.C_SLUG 
                                , a.C_REDIRECT_URL
                             From 
                    (select 0 as order_a,FK_ARTICLE from t_ps_sticky where FK_CATEGORY = $v_category_id
                    union
                    select 1 as order_a,FK_ARTICLE from t_ps_category_article where FK_CATEGORY = $v_category_id and FK_ARTICLE not in (select FK_ARTICLE from t_ps_sticky where FK_CATEGORY = $v_category_id)) all_a 
                    inner join t_ps_article a
                    on all_a.FK_ARTICLE = a.PK_ARTICLE
                    where a.C_STATUS=3
                    And a.C_BEGIN_DATE <= NOW()
                    And a.C_END_DATE >= NOW()
                    order by all_a.order_a,all_a.FK_ARTICLE desc Limit $v_some_news_show";
            }
            $DATA_MODEl['arr_all_article'] = $this->db->getAll($stmt);
        }

        return $DATA_MODEl;
    }

    //end
    //lay tat ca list trong list type online tv
    public function qry_all_online_tv($v_listtype_id)
    {
        $v_listtype_id = replace_bad_char($v_listtype_id);
        $stmt = "select PK_LIST,FK_LISTTYPE,C_CODE,C_NAME,C_XML_DATA from t_cores_list where FK_LISTTYPE = $v_listtype_id and C_STATUS = 1 order by C_ORDER ";

        return $this->db->getAll($stmt);
    }

    //end

    /**
     * lấy tất cả danh sách bao (sử dụng danh mục động)
     * @return type
     */
    public function qry_all_magazine()
    {
        $sql = "Select
                    PK_LIST,
                    C_NAME
                  From t_cores_list
                  Where FK_LISTTYPE = (Select
                                         PK_LISTTYPE
                                       From t_cores_listtype
                                       Where C_CODE = '" . _CONST_MAGAZINE . "')
                      And C_STATUS = 1";

        return $this->db->getAll($sql);
    }

    /**
     * insert dat bao vao co so du lieu
     */
    public function insert_magazine_subscriptions()
    {
        //tao duong link quay lai
        $url_exec_done = build_url_magazine_subscriptions($this->website_id);

        //xu ly du lieu
        $v_name = get_post_var('txt_name', '');
        $v_unit = get_post_var('txt_unit', '');
        $v_address = get_post_var('txt_address', '');
        $v_address_to_receive = get_post_var('txt_address_receive', '');
        $v_phone = get_post_var('txt_phone', '');
        $v_email = get_post_var('txt_email', '');
        $v_begin_date = get_post_var('txt_begin_date', '');
        $v_end_date = get_post_var('txt_end_date', '');

        $arr_magazine = isset($_POST['chk_magazine']) ? $_POST['chk_magazine'] : array();
        $v_status = isset($_POST['chk_status']) ? 1 : 0;

        //valid date
        if ($v_name == '' OR $v_address == '' OR $v_address_to_receive == '' OR $v_phone == '' OR $v_email == '' OR $v_begin_date == '' OR $v_end_date == '')
        {
            $this->exec_fail($url_exec_done, 'Dữ liệu truyền vào không hợp lệ !!!');
        }

        //include captcha
        $captcha_url = SERVER_ROOT . 'apps/frontend/themes/' . $this->theme_code . '/captcha/';
        require $captcha_url . 'securimage.php';

        //kiem tra captcha
        $securimage = new Securimage();
        if ($securimage->check($_POST['txt_captcha_code']) == FALSE)
        {
            $message = "Mã captcha ko đúng !!!";

            //arr_filter
            $arr_filter = array('message'             => $message, 'txt_name'            => $v_name,
                'txt_unit'            => $v_unit, 'txt_address'         => $v_address,
                'txt_address_receive' => $v_address_to_receive, 'txt_phone'           => $v_phone,
                'txt_email'           => $v_email, 'txt_begin_date'      => $v_begin_date, 'txt_end_date'        => $v_end_date);
            $this->exec_done($url_exec_done, $arr_filter);
        }

        //tao xml magazine
        $xml = '<data>';
        for ($i = 0; $i < count($arr_magazine); $i++)
        {
            $v_id_magazine = $arr_magazine[$i];
            $number_post_name = 'txt_number_' . $v_id_magazine;
            $v_number = get_post_var($number_post_name, 0);

            $xml .= '<item>';
            $xml .= "<id>$v_id_magazine</id>";
            $xml .= "<number>$v_number</number>";
            $xml .= '</item>';
        }
        $xml .= '</data>';

        //insert du lieu
        $v_end_date = jwDate::ddmmyyyy_to_yyyymmdd($v_end_date);
        $v_begin_date = jwDate::ddmmyyyy_to_yyyymmdd($v_begin_date);

        $sql = "Insert Into t_ps_magazine_subscripttions
                            (C_NAME,
                             C_UNIT,
                             C_ADRESS,
                             C_ADDRESS_TO_RECEIVE,
                             C_PHONE,
                             C_EMAIL,
                             C_BEGIN_DATE,
                             C_END_DATE,
                             C_XML_MAGAZINE,
                             C_STATUS)
                VALUES (?,?,?,?,?,?,?,?,?,?)";
        $arr_param = array($v_name, $v_unit, $v_address, $v_address_to_receive, $v_phone, $v_email, $v_begin_date, $v_end_date, $xml, $v_status);

        $this->db->Execute($sql, $arr_param);
        //kiem tra da insert duoc chua
        $affected_rows = $this->db->Affected_Rows();
        $message = "";
        if ($affected_rows > 0)
        {
            $message = "Đặt báo thành công, Cảm ơn bạn đã sử dụng chức năng Đặt báo !!!";
        }
        else
        {
            $message = "Đặt báo không thành công, bạn hãy kiểm tra lại thông tin !!!";
        }

        $this->exec_done($url_exec_done, array('message' => $message));
    }

    /**
     * insert gop y phan hoi vao co so du lieu
     */
    public function insert_feedback()
    {
        //tao duong link quay lai
        $url_exec_done = build_url_feedback($this->website_id);

        //du lieu truyen vao
        $v_name = get_post_var('txt_name', '');
        $v_address = get_post_var('txt_address', '');
        $v_email = get_post_var('txt_email', '');
        $v_title = get_post_var('txt_title', '');
        $v_content = get_post_var('txt_content', '');
        $v_captcha_code = get_post_var('txt_captcha_code', '');
        $v_file_name = '';

        //arr_filter
        $arr_filter = array('message'     => '', 'txt_name'    => $v_name,
            'txt_address' => $v_address, 'txt_email'   => $v_email,
            'txt_title'   => $v_title, 'txt_content' => $v_content);

        //valid date
        if ($v_name == '' OR $v_email == '' OR $v_title == '' OR $v_captcha_code == '')
        {
            $message = "Dữ liệu truyền vào không hợp lệ !!!";
            $arr_filter['message'] = $message;
            $this->exec_fail($url_exec_done, $arr_filter);
        }

        //include captcha
        $captcha_url = SERVER_ROOT . 'apps/frontend/themes/' . $this->theme_code . '/captcha/';
        require $captcha_url . 'securimage.php';

        //kiem tra captcha
        $securimage = new Securimage();
        if ($securimage->check($_POST['txt_captcha_code']) == FALSE)
        {
            $message = "Mã captcha ko đúng !!!";
            $arr_filter['message'] = $message;
            $this->exec_done($url_exec_done, $arr_filter);
        }


        //xu ly file day len sever
        if (empty($_FILES['file_upload']['tmp_name']) == FALSE)
        {
            $v_file_name = $_FILES['file_upload']['name'];
            $v_file_extention = end(explode('.', $v_file_name));
            $v_file_temp = $_FILES['file_upload']['tmp_name'];
            $v_file_size = $_FILES['file_upload']['size'];

            //file size ko duoc qua 10mb
            if ($v_file_size > 10485760)
            {
                $message = "dung lượng file quá lớn !!!";
                $arr_filter['message'] = $message;
                unlink($v_file_temp);
                $this->exec_done($url_exec_done, $arr_filter);
            }
            //kiem tra duoi file
            if (in_array(strtolower($v_file_extention), explode(',', trim(_CONST_IMAGE_FILE_EXT))) == FALSE && in_array(strtolower($v_file_extention), explode(',', trim(_CONST_WELLKNOWN_FILE_EXT))) == FALSE)
            {
                $message = "File không hợp lệ !!!";
                $arr_filter['message'] = $message;
                unlink($v_file_temp);
                $this->exec_done($url_exec_done, $arr_filter);
            }
            //lay nam va thang de tao thu muc
            $year_now = Date('Y');
            $month_now = Date('m');


            $v_dir = SERVER_ROOT . 'upload' . DS . 'gop_y_phan_hoi' . DS . $year_now . DS . $month_now . DS;
            if (is_dir($v_dir) == FALSE)
            {
                mkdir($v_dir, 0777, TRUE);
            }
            //chuyen file temp vao thu muc
            move_uploaded_file($v_file_temp, $v_dir . $v_file_name);

            //file name insert vao database
            $v_file_name = 'gop_y_phan_hoi' . DS . $year_now . DS . $month_now . DS . $v_file_name;
        }

        //insert gop y phan hoi
        $sql = "Insert Into t_ps_feedback
                            (C_NAME,
                            C_ADDRESS,
                            C_EMAIL,
                            C_INIT_DATE,
                            C_TITLE,
                            C_CONTENT,
                            FK_WEBSITE,
                            C_FILE_NAME)
                VALUES (?,?,?,NOW(),?,?,?,?)";
        $arr_param = array($v_name, $v_address, $v_email, $v_title, $v_content, $this->website_id, $v_file_name);

        $this->db->Execute($sql, $arr_param);
        //kiem tra da insert duoc chua
        $affected_rows = $this->db->Affected_Rows();
        $message = "";
        if ($affected_rows > 0)
        {
            $message = "Gửi câu hỏi thành công, Cảm ơn bạn đã góp ý !!!";
        }
        else
        {
            $message = "Gửi câu hỏi không thành công, bạn hãy kiểm tra lại thông tin !!!";
        }

        $this->exec_done($url_exec_done, array('message' => $message));
    }

    // Lay tat ca danh muc quang cao
    public function qry_all_list_advertising()
    {
        $sql = "Select L.PK_LIST
                        , L.C_NAME
                        , R.C_FILE_NAME
                        , R.PK_ADV_RATE
                From t_cores_list As L
                Inner Join t_cores_listtype As T
                On L.FK_LISTTYPE = T.PK_LISTTYPE
                Inner Join t_ps_adv_rates As R
                On R.FK_LIST = L.PK_LIST
                Where T.C_CODE = 'DM_BIEU_GIA_QUANG_CAO'
                And T.C_STATUS = 1
                And L.C_STATUS = 1
                And Datediff(NOW(),R.C_BEGIN_DATE) >= 0
                And Datediff(R.C_END_DATE,NOW()) >= 0
                And R.C_STATUS = 1
                Order By L.C_ORDER ASC
                ";
        return $this->db->GetAll($sql);
    }

    // Lay noi dung bieu gia theo id
    public function qry_single_advertising()
    {
        $v_adv_rate_id = replace_bad_char(get_request_var('list_id'));
        $condition = $this->check_exists_adv_rate($v_adv_rate_id) === TRUE ? ' And PK_ADV_RATE = ' . $v_adv_rate_id : ' And C_DEFAULT = 1';
        $sql = "Select R.PK_ADV_RATE
                        , L.C_NAME
                        , R.C_CONTENT
                        , R.C_TYPE
                From t_ps_adv_rates As R
                Inner Join t_cores_list As L
                On R.FK_LIST = L.PK_LIST
                Where 1 = 1
                $condition
                And R.C_STATUS = 1
                ";
        return $this->db->GetRow($sql);
    }

    // Kiem tra ton tai noi dung bieu gia
    public function check_exists_adv_rate($id)
    {
        $sql = "Select PK_ADV_RATE
                From t_ps_adv_rates
                Where PK_ADV_RATE = ?
                ";
        $res = $this->db->GetRow($sql, array($id));
        if (count($res) > 0)
        {
            return true;
        }
        return false;
    }

    // Lay tat ca vi tri quang cao
    function qry_all_advertising_position()
    {
        $sql = "Select L.PK_LIST
                        , L.C_NAME
                From t_cores_list As L
                Inner Join t_cores_listtype As T
                On L.FK_LISTTYPE = T.PK_LISTTYPE
                Where T.C_CODE = 'DM_VI_TRI_QUANG_CAO'
                Order By L.C_ORDER ASC
                ";
        return $this->db->GetAll($sql);
    }

    public function insert_contact_advertising()
    {
        //xu ly du lieu
        $v_name = get_post_var('txt_name', '');
        $v_unit = get_post_var('txt_unit', '');
        $v_address = get_post_var('txt_address', '');
        $v_phone = get_post_var('txt_phone', '');
        $v_email = get_post_var('txt_email', '');
        $v_begin_date = get_post_var('txt_begin_date', '');
        $v_end_date = get_post_var('txt_end_date', '');
        $v_tariff_if = get_post_var('hdn_tariff_id');
        //tao duong link quay lai
        $url_exec_done = build_url_contact_advertising($this->website_id, $v_tariff_if);

        $arr_adv_pos = isset($_POST['chk_adv_pos']) ? $_POST['chk_adv_pos'] : array();
        $v_adv_pos = implode('-', $arr_adv_pos);
        //valid date
        if ($v_name == '' OR $v_address == '' OR $v_phone == '' OR $v_email == '' OR $v_begin_date == '' OR $v_end_date == '')
        {
            $this->exec_fail($url_exec_done, 'Dữ liệu truyền vào không hợp lệ !!!');
        }

        //include captcha
        $captcha_url = SERVER_ROOT . 'apps/frontend/themes/' . $this->theme_code . '/captcha/';
        require $captcha_url . 'securimage.php';

        //kiem tra captcha
        $securimage = new Securimage();
        if ($securimage->check($_POST['txt_captcha_code']) == FALSE)
        {
            $message = "Mã captcha ko đúng !!!";

            //arr_filter
            $arr_filter = array('message'        => $message, 'txt_name'       => $v_name,
                'txt_unit'       => $v_unit, 'txt_address'    => $v_address,
                'txt_phone'      => $v_phone, 'chk_adv_pos'    => $v_adv_pos,
                'txt_email'      => $v_email, 'txt_begin_date' => $v_begin_date, 'txt_end_date'   => $v_end_date);
            $this->exec_done($url_exec_done, $arr_filter);
        }

        //tao xml position
        $xml = '<?xml version="1.0" encoding="utf-8" ?><data>';
        for ($i = 0; $i < count($arr_adv_pos); $i++)
        {
            $v_id_adv_pos = $arr_adv_pos[$i];
            $xml .= "<id>$v_id_adv_pos</id>";
        }
        $xml .= '</data>';

        //insert du lieu
        $v_end_date = jwDate::ddmmyyyy_to_yyyymmdd($v_end_date);
        $v_begin_date = jwDate::ddmmyyyy_to_yyyymmdd($v_begin_date);

        $sql = "Insert Into t_ps_adv_order
                            (C_NAME,
                             C_UNIT,
                             C_ADDRESS,
                             C_PHONE,
                             C_EMAIL,
                             C_BEGIN_DATE,
                             C_END_DATE,
                             C_XML_ADV_ORDER,
                             C_STATUS)
                VALUES (?,?,?,?,?,?,?,?,?)";
        $arr_param = array($v_name, $v_unit, $v_address, $v_phone, $v_email, $v_begin_date, $v_end_date, $xml, 0);

        $this->db->Execute($sql, $arr_param);
        //kiem tra da insert duoc chua
        $affected_rows = $this->db->Affected_Rows();
        $message = "";
        if ($affected_rows > 0)
        {
            $message = "Gửi yêu cầu thành công!";
        }
        else
        {
            $message = "Gửi yêu cầu không thành công, bạn hãy kiểm tra lại thông tin !!!";
        }

        $this->exec_done($url_exec_done, array('message' => $message));
    }

    /**
     * lay danh sach feedback public
     * @param type $website_id
     * @return type
     */
    public function qry_all_public_feedback($website_id)
    {
        $v_limi = defined('_CONST_DEFAULT_ROWS_FEEBACK_PAGE') ? _CONST_DEFAULT_ROWS_FEEBACK_PAGE : 5;
        $v_page = get_request_var('page', 1);
        $v_start = ($v_limi * ($v_page - 1));
        $sql = "Select
                    PK_FEEDBACK,
                    C_NAME,
                    C_ADDRESS,
                    DATE_FORMAT(C_INIT_DATE,'%d-%m-%Y') As C_INIT_DATE,
                    C_TITLE,
                    (SELECT C_NAME FROM t_ps_website WHERE $website_id = PK_WEBSITE AND C_STATUS =1) AS C_WEBSITE_NAME,
                    (Select
                        COUNT(*)
                      From t_ps_feedback
                      Where FK_WEBSITE = 17
                          And C_PUBLIC = 1) As TOTAL_RECORD,
                    C_CONTENT,
                    C_REPLY,
                    (Select
                       C_NAME
                     From t_cores_user
                     Where PK_USER = FB.FK_USER) as C_USER_NAME
                  From t_ps_feedback FB
                  Where FK_WEBSITE = $website_id
                      And C_PUBLIC = 1
                  Order by C_INIT_DATE DESC 
                limit $v_start,$v_limi";

        return $this->db->getAll($sql);
    }

    public function qry_article_of_category($website_id, $category_id, $limit)
    {
        $stmt = "SELECT
                COUNT(*)
              FROM t_ps_sticky
              WHERE FK_WEBSITE = ?
                  AND C_DEFAULT <> 1
                  AND FK_CATEGORY = ?";
        $count = $this->db->getOne($stmt, array($website_id, $category_id));
        //neu sticky du
        if ($count >= $limit)
        {
            $v_sub_query = "SELECT
                                FK_ARTICLE
                              FROM t_ps_sticky
                              WHERE FK_WEBSITE = $website_id
                                  AND C_DEFAULT <> 1
                                  AND FK_CATEGORY = $category_id
                              ORDER BY C_ORDER
                              LIMIT $limit";
        }
        //lay them tin bai thong thuong
        else
        {
            $article_limit = (int) $limit - (int) $count;
            $v_sub_query = "(SELECT
                                FK_ARTICLE
                              FROM t_ps_sticky
                              WHERE FK_WEBSITE = $website_id
                                  AND C_DEFAULT <> 1
                                  AND FK_CATEGORY = $category_id
                              ORDER BY C_ORDER
                              LIMIT $limit) 
                              UNION ALL 
                              (
                                SELECT
                                  FK_ARTICLE
                                FROM t_ps_category_article ca
                                  LEFT JOIN t_ps_article a
                                  ON ca.FK_ARTICLE = a.PK_ARTICLE
                                WHERE ca.FK_CATEGORY = 1270
                                    AND ca.FK_ARTICLE NOT IN(SELECT
                                                               FK_ARTICLE
                                                             FROM t_ps_sticky
                                                             WHERE FK_WEBSITE = $website_id
                                                                 AND FK_CATEGORY = $category_id
                                                                 AND C_DEFAULT <> 1)
                                ORDER BY C_BEGIN_DATE DESC
                                LIMIT $article_limit
                              )";
        }

        $stmt = " SELECT
                    A.*,
                   $limit as  `C_LIMIT` 
                    ,
                    DATE_FORMAT(A.C_BEGIN_DATE,'%d-%m-%Y') AS C_BEGIN_DATE_DDMMYY,
                    (SELECT
                        C_NAME
                      FROM t_ps_category
                      WHERE PK_CATEGORY = $category_id) As C_CATEGORY_NAME,
                    (SELECT
                      PK_CATEGORY
                    FROM t_ps_category
                    WHERE PK_CATEGORY = $category_id) As PK_CATEGORY,
                    (SELECT
                        C_SLUG
                      FROM t_ps_category
                      WHERE PK_CATEGORY = $category_id) As C_SLUG_CAT
                  FROM t_ps_article A
                    RIGHT JOIN ($v_sub_query) S
                      ON A.PK_ARTICLE = S.FK_ARTICLE
                   WHERE A.C_DEFAULT_WEBSITE = $website_id AND A.C_STATUS = 3";
        return $this->db->getAll($stmt);
    }

    /**
     * 
     * @return type
     */
    public function get_year()
    {
        $sql = "SELECT
                    MAX(YEAR(C_HISTORY_DATE)) AS C_MAX_YEAR,
                    MIN(YEAR(C_HISTORY_DATE)) AS C_MIN_YEAR
                  FROM t_ps_record_history_stat";
        return $this->db->getRow($sql);
    }

    /**
     * lay duong lin tra cuu chi tiet hs cua tung don vi
     * @return type
     */
    public function get_lookup_link()
    {
        $sql = "SELECT
                C_SHORT_CODE,
                ExtractValue(C_XML_DATA,'//item[@id=\"txt_lookup\"]//value') AS C_LOOKUP_LINK,
                ExtractValue(C_XML_DATA,'//item[@id=\"txt_lookup_list\"]//value') AS C_LOOKUP_LIST_LINK,
                ExtractValue(C_XML_DATA,'//item[@id=\"txt_lookup_detail_form\"]//value') AS C_LOOKUP_DETAIL_FORM_LINK,
                ExtractValue(C_XML_DATA,'//item[@id=\"txt_processing_record\"]//value') AS C_PROCESSING_RECORD_LINK
              FROM t_ps_member";
        return $this->db->GetAssoc($sql);
    }

    /**
     * lay danh sach tat ca don vi truc thuoc
     * @return type
     */
    public function qry_all_member_have_level()
    {
        $sql = "SELECT
                    PK_MEMBER,
                    C_SHORT_CODE,
                    C_NAME
                  FROM t_ps_member
                  WHERE (FK_MEMBER < 1
                       OR FK_MEMBER IS null) AND C_STATUS = 1";
        $MODEL_DATA['arr_all_district'] = $this->db->getAll($sql);

        $sql = "SELECT
                    PK_MEMBER,
                    C_SHORT_CODE,
                    C_NAME,
                    FK_MEMBER
                  FROM t_ps_member
                  WHERE FK_MEMBER > 0 AND C_STATUS = 1";

        $MODEL_DATA['arr_all_village'] = $this->db->getAll($sql);

        return $MODEL_DATA;
    }

    /**
     * Lay so lieu tong hop cua toan bo cac don vi
     * @param type $month
     * @param type $year
     * @param type $unit_code
     * @param type $spec_code
     */
    public function qry_synthesis($type_group = 0)
    {
        #variables request
        $v_village_id  = get_request_var('hdn_village_id',0);
        $v_fields_code = get_request_var('sel_spec_code','');
        $sel_year_month   = get_request_var('sel_year_month', Date('Y'));
        $sel_year_quarter = get_request_var('sel_year_quarter', Date('Y'));
        $v_month       = get_request_var('sel_month',date('m'));
        $v_quater      = get_request_var('sel_quarter',1);
        $rad_period    = get_request_var('rad_period','month');
        
        #End variables request        
        $condition = '';
        if($v_village_id >0)
        {
            $arr_single_member = $this->db->GetRow('SELECT
                                                    if(FK_MEMBER>0,(select C_CODE from t_ps_member  where PK_MEMBER = m.FK_MEMBER),C_CODE) as C_CODE,
                                                    C_NAME,
                                                    FK_MEMBER,
                                                    FK_VILLAGE_ID
                                                  FROM t_ps_member m
												  
                                                  WHERE  and m.C_STATUS = 1 And PK_MEMBER = ?',array($v_village_id));
            $v_fk_member     = isset($arr_single_member['FK_MEMBER']) ? $arr_single_member['FK_MEMBER'] : 0;
            $v_fk_village_id = isset($arr_single_member['FK_VILLAGE_ID']) ? $arr_single_member['FK_VILLAGE_ID'] : 0;
            $v_member_code   = isset($arr_single_member['C_CODE']) ? $arr_single_member['C_CODE'] : '';
                
            if( $v_fk_member > 0)
            {
                $condition = " And hs.FK_VILLAGE_ID = '$v_fk_village_id' And hs.C_UNIT_CODE = '$v_member_code' ";
            }
            else
            {
                $condition = " And hs.C_UNIT_CODE = '$v_member_code' ";
            }
        }
        if(strval($v_fields_code) != '')
        {
            $condition .= " And hs.C_SPEC_CODE = '$v_fields_code'";
        }
        
        if($rad_period == 'month')
        {
            $condition .= " And Month(C_HISTORY_DATE) = '$v_month'";
            $condition .= " And YEAR(C_HISTORY_DATE) = '$sel_year_month'";
        }
        else
        {
            if($v_quater == 1)
            {
                $v_quater = "'1','2','3'";
            }
            elseif($v_quater == 2)
            {
                $v_quater = "'4','5','6'";
            }
            elseif($v_quater == 3)
            {
                $v_quater = "'7','8','9'";
            }
            else
            {
                $v_quater = "'10','11','12'";
            }
            $condition .= " And Month(C_HISTORY_DATE) in ($v_quater)";
            $condition .= " And YEAR(C_HISTORY_DATE) = '$sel_year_quarter'";
        }
        
        if($type_group == 0)
        {
            $condition_type_group = "m.C_CODE = hs.C_UNIT_CODE";
            $group_by = "hs.C_UNIT_CODE";
        }
        else
        {
            $condition_type_group = "m.C_CODE = hs.C_UNIT_CODE AND  COALESCE( m.FK_VILLAGE_ID,0) = hs.FK_VILLAGE_ID )

                      OR ( m.FK_VILLAGE_ID = hs.FK_VILLAGE_ID 
                           AND FK_MEMBER = (SELECT PK_MEMBER FROM t_ps_member WHERE C_CODE = hs.C_UNIT_CODE)";
            $group_by = "hs.FK_VILLAGE_ID,C_UNIT_CODE";
        }
        $sql ="SELECT
                HS.C_UNIT_CODE,
                hs.C_SPEC_CODE,
                m.C_STATUS     AS C_MEMBER_STATUS,
                if(m.FK_VILLAGE_ID >0,concat(' -- ',m.C_NAME),m.C_NAME)    AS C_NAME,
                SUM(C_COUNT_KY_TRUOC) AS C_COUNT_KY_TRUOC,
                SUM(C_COUNT_TIEP_NHAN) AS C_COUNT_TIEP_NHAN,
                SUM(C_COUNT_THU_LY_CHUA_DEN_HAN) AS C_COUNT_THU_LY_CHUA_DEN_HAN,
                SUM(C_COUNT_THU_LY_QUA_HAN) AS C_COUNT_THU_LY_QUA_HAN,
                SUM(C_COUNT_TRA_SOM_HAN) AS C_COUNT_TRA_SOM_HAN,
                SUM(C_COUNT_TRA_DUNG_HAN) AS C_COUNT_TRA_DUNG_HAN,
                SUM(C_COUNT_TRA_QUA_HAN) AS C_COUNT_TRA_QUA_HAN,
                SUM(C_COUNT_BO_SUNG) AS C_COUNT_BO_SUNG,
                SUM(C_COUNT_NVTC) AS C_COUNT_NVTC,
                SUM(C_COUNT_TU_CHOI) AS C_COUNT_TU_CHOI,
                SUM(C_COUNT_CONG_DAN_RUT) AS C_COUNT_CONG_DAN_RUT,
                SUM(C_COUNT_CHO_TRA_KY_TRUOC) AS C_COUNT_CHO_TRA_KY_TRUOC,
                SUM(C_COUNT_CHO_TRA_TRONG_KY) AS C_COUNT_CHO_TRA_TRONG_KY,
                SUM(C_COUNT_THUE) AS C_COUNT_THUE
              FROM t_ps_record_history_stat hs
                Left JOIN t_ps_member m
                  ON ($condition_type_group )
              WHERE 1=1 $condition
			  and m.C_STATUS = 1
              GROUP BY $group_by
               ORDER BY m.C_ORDER,m.FK_VILLAGE_ID";
			   //echo $sql;
        return $this->db->GetAll($sql);
    }

    /**
     * lay du lieu tong hop cho bieu do
     * @param int $month
     * @param type $year
     * @param type $unit_code
     * @param type $spec_code
     * @param type $group_unit
     * @return type
     */
    public function qry_synthesis_chart($month, $year, $v_village_id, $spec_code)
    {
        $condition = " And Month(C_HISTORY_DATE)= '$month' ";
        $condition .= " And Year(C_HISTORY_DATE)= '$year' ";
        if($v_village_id >0)
        {
            $arr_single_member = $this->db->GetRow('SELECT
                                                    if(FK_MEMBER>0,(select C_CODE from t_ps_member  where PK_MEMBER = m.FK_MEMBER),C_CODE) as C_CODE,
                                                    C_NAME,
                                                    FK_MEMBER,
                                                    FK_VILLAGE_ID
                                                  FROM t_ps_member m
                                                  WHERE PK_MEMBER = ?',array($v_village_id));
            $v_fk_member     = isset($arr_single_member['FK_MEMBER']) ? $arr_single_member['FK_MEMBER'] : 0;
            $v_fk_village_id = isset($arr_single_member['FK_VILLAGE_ID']) ? $arr_single_member['FK_VILLAGE_ID'] : 0;
            $v_member_code   = isset($arr_single_member['C_CODE']) ? $arr_single_member['C_CODE'] : '';
                
            if( $v_fk_member > 0)
            {
                $condition .= " And hs.FK_VILLAGE_ID = '$v_fk_village_id' And hs.C_UNIT_CODE = '$v_member_code' ";
            }
            else
            {
                $condition .= " And hs.C_UNIT_CODE = '$v_member_code' ";
            }
        }
        if($spec_code != '')
        {
            $condition .= " And C_SPEC_CODE= '$spec_code' ";
        }
        
        $sql = "SELECT
                    SUM(C_COUNT_KY_TRUOC) AS C_COUNT_KY_TRUOC,
                    SUM(C_COUNT_TIEP_NHAN) AS C_COUNT_TIEP_NHAN,
                    
                    SUM(C_COUNT_THU_LY_CHUA_DEN_HAN) AS C_COUNT_THU_LY_CHUA_DEN_HAN,
                    SUM(C_COUNT_THU_LY_QUA_HAN) AS C_COUNT_THU_LY_QUA_HAN,
                    
                    SUM(C_COUNT_TRA_SOM_HAN) AS C_COUNT_TRA_SOM_HAN,
                    SUM(C_COUNT_TRA_DUNG_HAN) AS C_COUNT_TRA_DUNG_HAN,
                    SUM(C_COUNT_TRA_QUA_HAN) AS C_COUNT_TRA_QUA_HAN,
                    
                    SUM(C_COUNT_BO_SUNG) AS C_COUNT_BO_SUNG,
                    SUM(C_COUNT_NVTC) AS C_COUNT_NVTC,
                    SUM(C_COUNT_TU_CHOI) AS C_COUNT_TU_CHOI,
                    SUM(C_COUNT_CONG_DAN_RUT) AS C_COUNT_CONG_DAN_RUT,
                    SUM(C_COUNT_CHO_TRA_KY_TRUOC) AS C_COUNT_CHO_TRA_KY_TRUOC,
                    SUM(C_COUNT_CHO_TRA_TRONG_KY) AS C_COUNT_CHO_TRA_TRONG_KY,
                    SUM(C_COUNT_THUE) AS C_COUNT_THUE
                  FROM t_ps_record_history_stat hs				  
                  Where 1=1 $condition
                    group by C_UNIT_CODE
                ";
        return $this->db->getRow($sql);
    }
    
    public function qry_synthesis_compare_chart($month, $year, $v_compare_type)
    {
        $condition = " And Month(hs.C_HISTORY_DATE)= '$month' ";
        $condition .= " And Year(hs.C_HISTORY_DATE)= '$year' ";
        if($v_compare_type == 0)
        {
            $condition .= " And hs.FK_VILLAGE_ID = '0' ";
        }
        else
        {
            $condition .= " And hs.FK_VILLAGE_ID > 0";
        }
        
        $sql = "SELECT
                    m.C_NAME,
                    SUM(C_COUNT_KY_TRUOC) AS C_COUNT_KY_TRUOC,
                    SUM(C_COUNT_TIEP_NHAN) AS C_COUNT_TIEP_NHAN,
                    SUM(C_COUNT_THU_LY_CHUA_DEN_HAN) AS C_COUNT_THU_LY_CHUA_DEN_HAN,
                    SUM(C_COUNT_THU_LY_QUA_HAN) AS C_COUNT_THU_LY_QUA_HAN,
                    SUM(C_COUNT_TRA_SOM_HAN) AS C_COUNT_TRA_SOM_HAN,
                    SUM(C_COUNT_TRA_DUNG_HAN) AS C_COUNT_TRA_DUNG_HAN,
                    SUM(C_COUNT_TRA_QUA_HAN) AS C_COUNT_TRA_QUA_HAN,
                    SUM(C_COUNT_BO_SUNG) AS C_COUNT_BO_SUNG,
                    SUM(C_COUNT_NVTC) AS C_COUNT_NVTC,
                    SUM(C_COUNT_TU_CHOI) AS C_COUNT_TU_CHOI,
                    SUM(C_COUNT_CONG_DAN_RUT) AS C_COUNT_CONG_DAN_RUT,
                    SUM(C_COUNT_CHO_TRA_KY_TRUOC) AS C_COUNT_CHO_TRA_KY_TRUOC,
                    SUM(C_COUNT_CHO_TRA_TRONG_KY) AS C_COUNT_CHO_TRA_TRONG_KY,
                    SUM(C_COUNT_THUE) AS C_COUNT_THUE
                  FROM t_ps_record_history_stat hs
                   Left JOIN t_ps_member m
                  ON (m.C_CODE = hs.C_UNIT_CODE AND  COALESCE( m.FK_VILLAGE_ID,0) = hs.FK_VILLAGE_ID )

                      OR ( m.FK_VILLAGE_ID = hs.FK_VILLAGE_ID 
                           AND FK_MEMBER = (SELECT PK_MEMBER FROM t_ps_member WHERE C_CODE = hs.C_UNIT_CODE)
                          )
                  Where 1=1 
                    $condition
                    group by C_UNIT_CODE,hs.FK_VILLAGE_ID
                    order by m.C_ORDER
                ";
        return $this->db->GetAll($sql);
    }
    
    
    public function qry_all_progress_fields($month,$year)
    {
       
        $condition = " And Month(C_HISTORY_DATE)= '$month' ";
        $condition .= " And Year(C_HISTORY_DATE)= '$year' ";
        $sql = "SELECT
                    m.C_NAME,
                    hs.C_SPEC_CODE,
                    hs.C_UNIT_CODE,
                    l.C_NAME AS C_SPEC_NAME,
                    (
                        SELECT COUNT(*) FROM t_ps_member pm WHERE C_CODE = hs.C_UNIT_CODE AND COALESCE(FK_VILLAGE_ID,0) = hs.FK_VILLAGE_ID 
                        AND (SELECT COUNT(*) FROM t_ps_member WHERE FK_MEMBER = pm.PK_MEMBER)
                    ) as C_COUNT_VILLAGE, 
                    SUM(C_COUNT_KY_TRUOC) AS C_COUNT_KY_TRUOC,
                    SUM(C_COUNT_TIEP_NHAN) AS C_COUNT_TIEP_NHAN,
                    SUM(C_COUNT_THU_LY_CHUA_DEN_HAN) AS C_COUNT_THU_LY_CHUA_DEN_HAN,
                    SUM(C_COUNT_THU_LY_QUA_HAN) AS C_COUNT_THU_LY_QUA_HAN,
                    SUM(C_COUNT_TRA_SOM_HAN) AS C_COUNT_TRA_SOM_HAN,
                    SUM(C_COUNT_TRA_DUNG_HAN) AS C_COUNT_TRA_DUNG_HAN,
                    SUM(C_COUNT_TRA_QUA_HAN) AS C_COUNT_TRA_QUA_HAN,
                    SUM(C_COUNT_BO_SUNG) AS C_COUNT_BO_SUNG,
                    SUM(C_COUNT_NVTC) AS C_COUNT_NVTC,
                    SUM(C_COUNT_TU_CHOI) AS C_COUNT_TU_CHOI,
                    SUM(C_COUNT_CONG_DAN_RUT) AS C_COUNT_CONG_DAN_RUT,
                    SUM(C_COUNT_CHO_TRA_KY_TRUOC) AS C_COUNT_CHO_TRA_KY_TRUOC,
                    SUM(C_COUNT_CHO_TRA_TRONG_KY) AS C_COUNT_CHO_TRA_TRONG_KY,
                    SUM(C_COUNT_THUE) AS C_COUNT_THUE
                  FROM t_ps_record_history_stat hs
                   LEFT JOIN t_ps_member m
                  ON (m.C_CODE = hs.C_UNIT_CODE AND  COALESCE( m.FK_VILLAGE_ID,0) = hs.FK_VILLAGE_ID )

                      OR ( m.FK_VILLAGE_ID = hs.FK_VILLAGE_ID 
                           AND FK_MEMBER = (SELECT PK_MEMBER FROM t_ps_member WHERE C_CODE = hs.C_UNIT_CODE)
                          )
                          LEFT JOIN t_cores_list l ON l.C_CODE = hs.C_SPEC_CODE
                  WHERE 1=1 
                    $condition
                    GROUP BY C_UNIT_CODE,hs.C_SPEC_CODE
                    ORDER BY m.C_ORDER";
        return $this->db->GetAll($sql);
    }
    
    
    public function qry_synthesis_all_village($month,$year)
    {
       
        $condition = " And Month(C_HISTORY_DATE)= '$month' ";
        $condition .= " And Year(C_HISTORY_DATE)= '$year' ";
        $sql = "SELECT
                    m.C_NAME,
                    hs.C_UNIT_CODE,
                    SUM(C_COUNT_KY_TRUOC) AS C_COUNT_KY_TRUOC,
                    SUM(C_COUNT_TIEP_NHAN) AS C_COUNT_TIEP_NHAN,
                    SUM(C_COUNT_THU_LY_CHUA_DEN_HAN) AS C_COUNT_THU_LY_CHUA_DEN_HAN,
                    SUM(C_COUNT_THU_LY_QUA_HAN) AS C_COUNT_THU_LY_QUA_HAN,
                    SUM(C_COUNT_TRA_SOM_HAN) AS C_COUNT_TRA_SOM_HAN,
                    SUM(C_COUNT_TRA_DUNG_HAN) AS C_COUNT_TRA_DUNG_HAN,
                    SUM(C_COUNT_TRA_QUA_HAN) AS C_COUNT_TRA_QUA_HAN,
                    SUM(C_COUNT_BO_SUNG) AS C_COUNT_BO_SUNG,
                    SUM(C_COUNT_NVTC) AS C_COUNT_NVTC,
                    SUM(C_COUNT_TU_CHOI) AS C_COUNT_TU_CHOI,
                    SUM(C_COUNT_CONG_DAN_RUT) AS C_COUNT_CONG_DAN_RUT,
                    SUM(C_COUNT_CHO_TRA_KY_TRUOC) AS C_COUNT_CHO_TRA_KY_TRUOC,
                    SUM(C_COUNT_CHO_TRA_TRONG_KY) AS C_COUNT_CHO_TRA_TRONG_KY,
                    SUM(C_COUNT_THUE) AS C_COUNT_THUE
                  FROM t_ps_record_history_stat hs
                   LEFT JOIN t_ps_member m
                  ON (m.C_CODE = hs.C_UNIT_CODE AND  COALESCE( m.FK_VILLAGE_ID,0) = hs.FK_VILLAGE_ID )

                      OR ( m.FK_VILLAGE_ID = hs.FK_VILLAGE_ID 
                           AND FK_MEMBER = (SELECT PK_MEMBER FROM t_ps_member WHERE C_CODE = hs.C_UNIT_CODE)
                          )
                          LEFT JOIN t_cores_list l ON l.C_CODE = hs.C_SPEC_CODE
                  WHERE 1=1 
                    AND hs.FK_VILLAGE_ID > 0
                    $condition
                    GROUP BY C_UNIT_CODE,hs.FK_VILLAGE_ID
                    ORDER BY C_UNIT_CODE";
        return $this->db->GetAll($sql);
    }
    
    
    /**
     *  Lấy danh sách tất cả lĩnh vực
     * @return array
     */
    public function qry_all_record_listtype()
    {
        $stmt = "SELECT  PK_LIST,     
                         C_NAME
                        
                FROM t_cores_list
                WHERE FK_LISTTYPE = (SELECT
                                       PK_LISTTYPE
                                     FROM t_cores_listtype
                                     WHERE C_CODE = '" . _CONST_LINH_VUC_TTHC . "'
                                        AND C_STATUS =1
                                     )
                     AND C_STATUS =1
                     ORDER BY C_ORDER ASC ";
        return $this->db->GetAssoc($stmt);
    }

    /**
     * Lay tat ca tthc tiep nhan internet
     * @return type
     */
    public function qry_all_internet_record_type()
    {
        $sql = "SELECT
                    RT.*,
                    L.C_NAME AS C_SPEC_NAME,
                    RTM.C_LIST_MEMBER
                  FROM t_ps_record_type RT
                    RIGHT JOIN (SELECT
                                  FK_RECORD_TYPE,
                                  GROUP_CONCAT(FK_MEMBER) AS C_LIST_MEMBER
                                FROM t_ps_record_type_member
                                GROUP BY FK_RECORD_TYPE) RTM
                      ON RT.PK_RECORD_TYPE = RTM.FK_RECORD_TYPE
                    LEFT JOIN t_cores_list L
                      ON RT.C_SPEC_CODE = L.C_CODE
                  WHERE L.C_STATUS = 1
                      AND FK_LISTTYPE = (SELECT
                                           PK_LISTTYPE
                                         FROM t_cores_listtype
                                         WHERE C_CODE = '" . _CONST_LINH_VUC_TTHC . "')
                  ORDER BY RT.C_CODE";
        return $this->db->getAll($sql);
    }

    /**
     * Lay danh sach thu tuc(dung cho page search)
     */
    public function qry_all_record_type($v_list_id = 0)
    {
        $v_list_id = isset($v_list_id) ? replace_bad_char($v_list_id) : 0;
        $v_condition = '';
        if ($v_list_id > 0)
        {
            $v_condition .=" AND C_SPEC_CODE = (SELECT
                                                   C_CODE
                                                 FROM t_cores_list
                                                 WHERE FK_LISTTYPE = (SELECT
                                                                        PK_LISTTYPE
                                                                      FROM t_cores_listtype
                                                                      WHERE C_CODE = '" . _CONST_LINH_VUC_TTHC . "'
                                                                          AND C_STATUS = 1)
                                                    AND PK_LIST = '$v_list_id'
                                                    AND C_STATUS = 1
                                                 )";
        }

        $sql = " SELECT RT.C_CODE,
                            CONCAT(RT.C_CODE,' - ',RT.C_NAME) as C_NAME
                            FROM t_ps_record_type RT
                            WHERE (1=1)
                                    $v_condition
                                    AND RT.C_STATUS = 1
                                    ORDER BY RT.C_ORDER ASC
                                    ";

        $results = $this->db->GetAssoc($sql);
        if ($this->db->ErrorNo() == 0)
        {
            return $results;
        }
        return array();
    }

    /**
     * Lay danh sach thu tuc "Dung cho huong dan thu tuc"
     */
    public function qry_all_record_type_guidance()
    {

        //Id linh vuc
        $v_list_id = get_request_var('sel_record_list', 0);
        //code thu tuc
        $v_record_type_code = get_request_var('txt_record_type_code', '');
        //Cap do cua thu tuc
        $v_record_level = get_request_var('sel_record_level', 0);
        $v_member_id = get_request_var('sel_member', 0);
        $sel_cap_do = get_request_var('sel_cap_do', '');

        $v_page = get_request_var('page', 1);
        $v_page = ($v_page <= 0) ? 1 : $v_page;
        $limit = defined('_CONTS_LIMIT_GUIDANCE_LIST') ? _CONTS_LIMIT_GUIDANCE_LIST : 10;

        $v_start = ($limit * ($v_page - 1));

        $conditions = '';
        $v_list_name = '';
        if ($v_list_id > 0)
        {
            $conditions .= " And rt.C_SPEC_CODE = (SELECT
                                                C_CODE
                                              FROM t_cores_list
                                              WHERE PK_LIST = '$v_list_id'
                                                  AND C_STATUS = 1) ";
        }
        $v_condition_member = '';
        if (trim($v_member_id) > 0)
        {
            $v_condition_member = " And rtm.FK_MEMBER = '$v_member_id'  ";
        }
        if (trim($v_record_type_code) != '')
        {
            $conditions .= " And rt.C_CODE = '$v_record_type_code' ";
        }

        //   check la ho so nop truc tuyen chưa làm  
        if ((int) $v_record_level == 2)
        {
            $conditions .= " And ((SELECT
                                COUNT(PK_RECORD_TYPE_MEMBER)
                              FROM t_ps_record_type_member rtm
                              WHERE rt.PK_RECORD_TYPE = FK_RECORD_TYPE $v_condition_member) > 0) ";
        }
        else if ((int) $v_record_level == 1)
        {
            $conditions .= " And ((SELECT
                                COUNT(PK_RECORD_TYPE_MEMBER)
                              FROM t_ps_record_type_member rtm
                              WHERE rt.PK_RECORD_TYPE = FK_RECORD_TYPE $v_condition_member ) = 0) ";
        }
        
        if($sel_cap_do != '')
        {
             $conditions .= " And (rt.C_SCOPE in ($sel_cap_do)) ";
        }
        $stmt = "Select
                        (SELECT COUNT(PK_RECORD_TYPE) FROM t_ps_record_type WHERE C_STATUS =1 $conditions ) AS C_TOTAL,
                        rt.*,
                        (Select
                              COUNT(PK_RECORD_TYPE_MEMBER)
                            From t_ps_record_type_member rtm
                            Where rt.PK_RECORD_TYPE = FK_RECORD_TYPE
                                 $v_condition_member) AS C_SEND_OVER_INTERNET
                         From t_ps_record_type rt
                         Where rt.C_STATUS = 1
                               $conditions
                      Order By C_ORDER ASC 
                      limit $v_start,$limit";


        $results['arr_all_record_type'] = $this->db->GetAll($stmt);
        $results['count_all_record'] = $this->db->GetOne(" SELECT COUNT(PK_RECORD_TYPE) as  C_TOTAL FROM t_ps_record_type rt WHERE C_STATUS =1 $conditions ");
        if ($this->db->ErrorNo() == 0)
        {
            for ($i = 0; $i < count($results['arr_all_record_type']); $i++)
            {
                $xml_data = isset($results['arr_all_record_type'][$i]['C_XML_DATA']) ? $results['arr_all_record_type'][$i]['C_XML_DATA'] : '';
                if (trim($xml_data) == '' OR $xml_data == NULL)
                {
                    return;
                }
                $dom = simplexml_load_string($xml_data);

                $v_xpath = '//data/media/file/text()';
                $r = $dom->xpath($v_xpath);
                $arr_all_file = array();
                foreach ($r as $item)
                {
                    $item = (string) $item;

                    if (trim($item) != '' && $item != NULL)
                    {
                        $v_path_file = CONST_TYPE_FILE_UPLOAD . 'template_files_types' . DS . $item;

                        if (is_file($v_path_file))
                        {
                            $arr_string = explode('_', $item, 2);
                            $arr_all_file[$item]['name'] = isset($arr_string[1]) ? $arr_string[1] : '';
                            $key_file = isset($arr_string[0]) ? $arr_string[0] : '';
                            $arr_all_file[$item]['file_name'] = $item;
                            $arr_all_file[$item]['path'] = $v_path_file;
                            $arr_all_file[$item]['type'] = filetype($v_path_file);
                        }
                    }
                }
                $results['arr_all_record_type'][$i]['arr_all_file'] = $arr_all_file;
            }
            return $results;
        }
        return array();
    }

    /**
     * Lấy Thông tin  chi tiết thủ tục theo mã thủ tục
     */
    public function qry_single_record_type($v_id)
    {
        $sql = "select
                   C_SPEC_CODE
                 from t_ps_record_type rt
                 where PK_RECORD_TYPE = $v_id
               ";
        $v_code = $this->db->GetOne($sql);

        $stmt = "select
                   (select
                             PK_LIST
                       from t_cores_list
                       where C_CODE =?)as PK_LIST,
                   (select
                          C_NAME
                      from t_cores_list
                      where C_CODE =?) AS C_NAME_THU_TUC,
                   C_NAME,
                   C_XML_DATA,
                   C_CODE,
                   PK_RECORD_TYPE                   
               from t_ps_record_type rt
               where
                   PK_RECORD_TYPE = ?
                   and C_STATUS = 1
                   ";
        return $this->db->GetRow($stmt, array($v_code, $v_code, $v_id));
    }

    /**
     * lay tat ca don vi 
     * @return type
     */
    public function qry_all_member()
    {
        $sql = "SELECT
                    PK_MEMBER,
                    C_NAME,
                    C_CODE,
                    FK_MEMBER
                  FROM t_ps_member
                  WHERE (FK_MEMBER < 1
                       OR FK_MEMBER IS null) AND C_STATUS = 1";
        $MODEL_DATA['arr_all_district'] = $this->db->getAll($sql);
        
        $sql = "SELECT
                    PK_MEMBER,
                    C_NAME,
                    FK_VILLAGE_ID,
                    (select C_CODE from t_ps_member where PK_MEMBER = m.FK_MEMBER) as C_CODE,
                    FK_MEMBER
                  FROM t_ps_member m
                  WHERE FK_MEMBER > 0 AND C_STATUS = 1";

        $MODEL_DATA['arr_all_village'] = $this->db->getAll($sql);
        return $MODEL_DATA;
    }

    /**
     * Query chi tiet cau hoi theo ma  doi vi hoi
     * @param int $v_survey ma cau hoi
     * @return array 
     */
    public function qry_single_survey($v_survey_id = 0)
    {
        $v_website_id = $this->website_id;
        $v_current_date = date('Y-m-d H:i:s');
        $arr_single_member = $this->db->GetRow(" SELECT
                                                C_NAME,PK_MEMBER
                                              FROM t_ps_member
                                              WHERE PK_MEMBER = (SELECT
                                                                   FK_MEMBER
                                                                 FROM t_ps_survey
                                                                 WHERE PK_SURVEY = ?
                                                                 limit 1) ", array($v_survey_id));

        $v_conditon = " And PK_SURVEY = '$v_survey_id' ";
        if (trim($arr_single_member['C_NAME']) != '')
        {
            $member_id = isset($arr_single_member['PK_MEMBER']) ? $arr_single_member['PK_MEMBER'] : 0;
            $v_conditon .= " And FK_MEMBER = '$member_id' 
                                Order By PK_SURVEY DESC
                                LIMIT 1";
            $member_name = __('public service');
        }
        //lay danh sach cac id của question
        $sql = " (
                select group_concat(PK_SURVEY_QUESTION)
                FROM t_ps_survey_question sq 
                    WHERE FK_SURVEY = (SELECT 
                                         ps.PK_SURVEY
                                       FROM t_ps_survey ps
                                       WHERE  
                                            C_STATUS = 1 
                                            AND C_END_DATE >= '$v_current_date'
                                            $v_conditon)) as C_LIST_PK_SURVEY_QUESTION, ";
        $stmt = "SELECT $sql sq.*,
                            '$member_name' As C_MEMBER_NAME,
                            (SELECT 
                                  CONCAT('<data>',
                                          GROUP_CONCAT('<item',
                                                                   CONCAT(' PK_SURVEY_ANSWER=\"',PK_SURVEY_ANSWER,'\"')
                                                                  ,CONCAT(' C_NAME=\"',C_NAME,'\"')					
                                          ,'/>' SEPARATOR ' ')
                                  ,'</data>')
                                  FROM t_ps_survey_answer  sa WHERE sa.FK_SURVEY_QUESTION = sq.PK_SURVEY_QUESTION
                               ) AS C_XML_ANSWER,
                               (SELECT 
                                         ps.C_NAME
                                       FROM t_ps_survey ps
                                       WHERE  
                                            C_STATUS = 1 
                                            AND C_END_DATE >= '$v_current_date'
                                            AND ps.FK_WEBSITE = '$v_website_id' $v_conditon ) as C_SURVEY_NAME
                                                
                    FROM t_ps_survey_question sq 
                    WHERE FK_SURVEY = (SELECT 
                                         ps.PK_SURVEY
                                       FROM t_ps_survey ps
                                       WHERE  
                                            C_STATUS = 1 
                                            AND C_END_DATE >= '$v_current_date'
                                            And ps.FK_WEBSITE = '$v_website_id'
                                            $v_conditon)";

        $results = $this->db->GetAll($stmt);
        if ($this->db->ErrorNo() == 0)
            return $results;

        return array();
    }

    /**
     * lay thong tin cau khao sat và don vi khao sat
     * @return array 
     */
    public function qry_all_member_survey()
    {
        $v_website_id = $this->website_id;
        $stmt = "  SELECT
                            0 AS PK_MEMBER,
                            '" . __('public service') . "' AS C_NAME,
                            (SELECT
                               CONCAT('<data>',     
                                        GROUP_CONCAT('<item', 
                                          CONCAT(' PK_SURVEY=\"',ps.PK_SURVEY,'\"'),
                                          CONCAT(' C_NAME=\"', ps.C_NAME,'\"') ,
                                '/>' SEPARATOR ' '),
                                '</data>')
                             FROM t_ps_survey ps
                             WHERE ps.FK_MEMBER  =0  AND ps.C_STATUS =1 and ps.FK_WEBSITE = '$v_website_id' order by ps.PK_SURVEY DESC  ) AS C_XML_SURVEY
                             UNION
                             (
                  SELECT
                      pm.PK_MEMBER,
                      pm.C_NAME,
                      (SELECT
                         CONCAT('<data>',     
                                  GROUP_CONCAT('<item', 
                                    CONCAT(' PK_SURVEY=\"',ps.PK_SURVEY,'\"'),
                                    CONCAT(' C_NAME=\"', ps.C_NAME,'\"') ,
                          '/>' SEPARATOR ' '),
                          '</data>')
                       FROM t_ps_survey ps
                       WHERE pm.PK_MEMBER = ps.FK_MEMBER AND ps.C_STATUS =1 and ps.FK_WEBSITE = '$v_website_id' ) AS C_XML_SURVEY
                    FROM t_ps_member pm
                    WHERE pm.C_STATUS = 1)";
        $results = $this->db->GetAll($stmt);
        if ($this->db->ErrorNo() == 0)
            return $results;
        return array();
    }

    /**
     * Kiem tra su ton tai cua cau hoi
     * @param int $v_question_id ma cua question
     * @return  int Ton tai return =1 ,true,Khong ton tai tra ve gia tri rong
     */
    public function qry_count_question_id($v_question_id = 0, $v_survey_id = 0)
    {
        $sql = "select count(PK_SURVEY_QUESTION) from t_ps_survey_question where PK_SURVEY_QUESTION In($v_question_id)  And FK_SURVEY = ?";
        $results = $this->db->GetOne($sql, array($v_survey_id));
        if ($this->db->ErrorNo() == 0)
            return $results;
        return 0;
    }

    /**
     * Lay id  cau tra loi cua cau hoi dang text va textaria theo id cau hoi
     * @return int Ton lai >0, khong ton tai =0
     */
    public function get_single_answer($v_answer_id = 0, $v_question_id = 0)
    {
        $v_condition = "";
        if ($v_answer_id > 0)
        {
            $v_condition = " And PK_SURVEY_ANSWER = '$v_answer_id' ";
        }
        if ($v_question_id > 0)
        {
            $v_condition = " And  FK_SURVEY_QUESTION = '$v_question_id' ";
        }
        $sql = " select PK_SURVEY_ANSWER,C_RESULT from t_ps_survey_answer where (1=1) $v_condition";
        $arr_single_answer = $this->db->GetRow($sql);
        if ($this->db->ErrorNo() == 0 && sizeof($arr_single_answer) > 0)
            return $arr_single_answer;
        return array();
    }

    public function qry_survey_get_id($v_survey_id = 0)
    {
        $sql = " select count(PK_SURVEY) from t_ps_survey where PK_SURVEY  = ?";
        $v_survey_id = $this->db->GetOne($sql, array($v_survey_id));
        if ($this->db->ErrorNo() == 0)
            return $v_survey_id;
        return 0;
    }

    public function do_insert_answer($parrans = array())
    {
        $stmt = "
             INSERT INTO t_ps_survey_answer 
                     (
                     FK_SURVEY, 
                     FK_SURVEY_QUESTION, 
                     C_NAME, 
                     C_RESULT
                     )
                     VALUES
                     (
                     ?, 
                     ?, 
                     ?, 
                     ?
                     )";
        $this->db->Execute($stmt, $parrans);
        return $this->db->ErrorNo();
    }

    //Cap nhat cau ket qua cau tra loi answer dang text va textAria
    public function do_update_answer($params = array())
    {
        $sql = "UPDATE t_ps_survey_answer 
                         SET
                         FK_SURVEY = ? , 
                         FK_SURVEY_QUESTION = ?, 
                         C_NAME = ? , 
                         C_RESULT = ?
                         WHERE
                         PK_SURVEY_ANSWER = ? ;
                 ";
        $this->db->Execute($sql, $params);
        return $this->db->ErrorNo();
    }

    //Cap nhat cau ket qua cau tra loi answer doi voi cac anser dang checkbox va radio
    public function do_update_answer_vote($params = array())
    {
        $sql = "UPDATE t_ps_survey_answer 
                         SET
                         C_RESULT = ?
                         WHERE
                         PK_SURVEY_ANSWER = ? ;
                 ";
        $this->db->Execute($sql, $params);
        return $this->db->ErrorNo();
    }

    /**
     * lay thong tin danh sach cac don vi 
     * @param int $v_scope_id Ma cap do don vi
     * @return array Mang chua danh sach don vi
     */
    public function dsp_all_member($v_scope_id = -1)
    {
        if (intval($v_scope_id) >= 0)
        {
            $sql = "SELECT
                        '$v_scope_id' as C_SCOPE_ID, 
                         PK_MEMBER,
                         C_NAME
                       FROM t_ps_member
                       WHERE C_STATUS = 1
                           AND C_SCOPE = ?";
            $resuts = $this->db->GetAll($sql, array($v_scope_id));
        }
        else
        {
            $sql = "SELECT
                        C_SCOPE as C_SCOPE_ID, 
                         PK_MEMBER,
                         C_NAME,
                         FK_MEMBER
                       FROM t_ps_member
                       WHERE C_STATUS = 1
                           ";
            $resuts = $this->db->GetAll($sql);
        }

        if ($this->db->ErrorNo() == 0)
            return $resuts;
        return array();
    }

    /**
     * Lay ten cua nhan vien theo ma id
     * @param int $v_staff_id ID nhan vien
     * @return string Ten cua nhan vien
     */
    public function qry_staff_get_by_id($v_staff_id)
    {
        $sql = "SELECT
                        l.*,m.C_NAME AS C_MEMBER_NAME,m.C_SHORT_CODE
                      FROM t_cores_list l LEFT JOIN t_ps_member m  ON (m.PK_MEMBER = (ExtractValue(l.C_XML_DATA,'//item[@id=\"ddl_member\"]/value')))

                            WHERE FK_LISTTYPE = (SELECT
                                                   PK_LISTTYPE
                                                 FROM t_cores_listtype
                                                 WHERE C_CODE = '" . _CONST_CAN_BO_DANH_GIA . "'
                                                     AND C_STATUS = 1)
                                AND l.C_STATUS = 1
                            AND l.PK_LIST = ?";
        $MODE_DATA['arr_single_user'] = $this->db->GetRow($sql, array($v_staff_id));
        $arr_evaluation = $this->qry_all_evaluation($v_staff_id);
        $MODE_DATA['C_POINT'] = $arr_evaluation[0]['C_TOTAL_POINT'];
        return $MODE_DATA;
    }

    public function do_update_vote($user_id, $today, $v_fk_creterial)
    {
        $v_key_cookie_check = 'vote_user_' . intval($user_id);
        if (!isset($_COOKIE[$v_key_cookie_check]) OR $_COOKIE[$v_key_cookie_check] === NULL)
        {
            $arr_today_date = explode('-', $today);
            // Kiem tra ton tai danh gia trong ngay
            $stmt = "SELECT PK_ASSESSMENT
                    , C_VOTE
                    FROM t_ps_assessment
                    WHERE FK_CRITERIAL = ?
                      AND C_DAY = DAY(NOW())
                      AND C_MONTH = MONTH(NOW())
                      AND C_YEAR = YEAR(NOW())
                      AND FK_USER = ?
                    ";
            $res = $this->db->GetRow($stmt, array($v_fk_creterial, $user_id));

            if (count($res) > 0)
            {
                $new_vote = $res['C_VOTE'] + 1;
                $stmt = "UPDATE t_ps_assessment
                        SET C_VOTE = ?
                        WHERE PK_ASSESSMENT = ?
                        ";
                $res = $this->db->Execute($stmt, array($new_vote, $res['PK_ASSESSMENT']));
            }
            else
            {
                $stmt = "INSERT INTO t_ps_assessment(FK_USER, FK_CRITERIAL, C_VOTE, C_DAY, C_MONTH, C_YEAR)
                            VALUES(?,?,?,DAY(NOW()),MONTH(NOW()),YEAR(NOW()))
                        ";
                $res = $this->db->Execute($stmt, array($user_id, $v_fk_creterial, 1));
            }
            if ($this->db->ErrorNo() == 0)
            {
                setcookie($v_key_cookie_check, 'check_vote', time() + 30);
                return true;
            }
        }
        else
        {
            unset($_COOKIE[$v_key_cookie_check]);
            return false;
        }
    }

    /**
     * thuc hien them mã hồ sơ đã được đánh giá
     * @param type $record_no
     */
    public function do_insert_record_evaluated($record_no)
    {
        $sql = "INSERT INTO t_ps_record_evaluated(C_RECORD_NO) VALUES('$record_no')";
        $this->db->Execute($sql);
    }

    // Lay danh gia tung can bo
    public function qry_single_result($user_id)
    {
        $day = get_post_var('day', '');
        $month = get_post_var('month', '');
        $year = get_post_var('year', '');
        $cond = '';
        if (!empty($day))
        {
            $cond .= " AND C_DAY = $day";
        }
        if (!empty($month))
        {
            $cond .= " AND C_MONTH = $month";
        }
        if (!empty($year))
        {
            $cond .= " AND C_YEAR = $year";
        }
        $sql = "SELECT
                    C.*,
                    COALESCE(A.C_VOTE,0) AS C_VOTE,
                    (SELECT
                       COALESCE(SUM(C_VOTE),0)
                     FROM t_ps_assessment
                     WHERE FK_USER = '$user_id') AS C_TOTAL_VOTE
                  FROM (SELECT
                          PK_LIST,
                          C_NAME,
                          CAST(C_CODE AS SIGNED) AS C_CODE
                        FROM t_cores_list
                        WHERE FK_LISTTYPE = (SELECT
                                               PK_LISTTYPE
                                             FROM t_cores_listtype
                                             WHERE C_CODE = '" . _CONST_DM_TIEU_CHI_DANH_GIA . "')
                            AND C_STATUS = 1 ) C
                    LEFT JOIN (SELECT
                                 FK_CRITERIAL,
                                 SUM(C_VOTE)  AS C_VOTE,
                                 FK_USER
                               FROM t_ps_assessment
                               WHERE FK_USER = '$user_id' $cond
                               GROUP BY FK_CRITERIAL) A
                      ON C.PK_LIST = A.FK_CRITERIAL
                  ORDER BY C.C_CODE desc";
        $arr_result = $this->db->GetAll($sql);

        if (count($arr_result > 0))
        {
            return $arr_result;
        }
        return false;
    }

    // Lay tat ca tieu chi
    public function qry_all_criterial($v_staff_id = 0)
    {
        if ($v_staff_id != 0)
        {
            $v_staff_id = replace_bad_char($v_staff_id);
            $sql = "SELECT
	                                l.PK_LIST,
	                                l.C_CODE,
	                                l.C_NAME,       
	                                ExtractValue(C_XML_DATA, '/data/item[@id=\"txt_file_name\"]/value') AS IMAGE_LINK,
	                                (
	                                      SELECT  SUM(a.C_VOTE) 
	                                              FROM t_ps_assessment a
	                                              WHERE FK_CRITERIAL  = l.PK_LIST 
	                                                      AND a.FK_USER = ? ) AS C_VOTE     
	                              FROM t_cores_list l
	                              WHERE C_STATUS = 1
	                                  AND FK_LISTTYPE = (SELECT
	                                                       PK_LISTTYPE
	                                                     FROM t_cores_listtype
	                                                     WHERE C_CODE = '" . _CONST_DM_TIEU_CHI_DANH_GIA . "')
	                              ORDER BY l.C_ORDER ASC";

            $arr_criterial = $this->db->GetAll($sql, array($v_staff_id));
        }
        else
        {
            $sql = "select * from t_cores_list l WHERE C_STATUS = 1
	                                  AND FK_LISTTYPE = (SELECT
	                                                       PK_LISTTYPE
	                                                     FROM t_cores_listtype
	                                                     WHERE C_CODE = \"" . _CONST_DM_TIEU_CHI_DANH_GIA . "\")
	                              ORDER BY l.C_ORDER ASC";
            $arr_criterial = $this->db->GetAll($sql);
        }

        if (count($arr_criterial) > 0)
        {
            return $arr_criterial;
        }
        return false;
    }

    /**
     * Lay ket qua danh gia can bo theo don vi
     * @return array Mang chua ket qua danh gia
     */
    public function qry_evaluation_results()
    {
        $arr_all_criterial = $this->qry_all_criterial();
        $qry = '';
        $arr_list_id = array();
        for ($i = 0; $i < count($arr_all_criterial); $i++)
        {
            $v_list_id = $arr_all_criterial[$i]['PK_LIST'];
            $arr_list_id[] = $v_list_id;
            $qry .= " ,(SELECT
                            Sum(C_VOTE)
                          FROM t_ps_assessment
                          WHERE FK_CRITERIAL = '$v_list_id'
                              AND FK_USER IN(SELECT
                                               PK_LIST
                                             FROM t_cores_list l
                                             WHERE (ExtractValue(l.C_XML_DATA,'//item[@id=\"ddl_member\"]/value')) = pm.PK_MEMBER)
                              AND C_STATUS = 1) AS C_VOTE_$v_list_id ";

            $qry .= " ,(SELECT
                            Sum(C_VOTE)
                          FROM t_ps_assessment
                          WHERE FK_CRITERIAL = '$v_list_id'
                              AND FK_USER IN(SELECT
                                               PK_LIST
                                             FROM t_cores_list l
                                             WHERE (ExtractValue(l.C_XML_DATA,'//item[@id=\"ddl_member\"]/value')) IN(SELECT
                                                                                                                       PK_MEMBER
                                                                                                                     FROM t_ps_member
                                                                                                                     WHERE C_STATUS = 1)
                                                 AND l.C_STATUS = 1)) AS C_VOTE_TOTAL_$v_list_id ";
        }
        $v_list_list_id = implode(',', $arr_list_id);
        $qry .= " ,(SELECT
                            SUM(C_VOTE)
                          FROM t_ps_assessment
                          WHERE FK_CRITERIAL IN($v_list_list_id)
                              AND FK_USER IN(SELECT
                                               PK_LIST
                                             FROM t_cores_list l
                                             WHERE (ExtractValue(l.C_XML_DATA,'//item[@id=\"ddl_member\"]/value'))IN(SELECT
                                                                                                                       PK_MEMBER
                                                                                                                     FROM t_ps_member
                                                                                                                     WHERE C_STATUS = 1)
                                                 AND l.C_STATUS = 1)) AS C_VOTE_ALL ";
        //Query danh sach ket qua don vi cap so va huyen
        $qty_all_scope_0_1 = "SELECT
                                        C_NAME,
                                        PK_MEMBER,
                                        (SELECT
                                           Sum(C_VOTE)
                                         FROM t_ps_assessment
                                         WHERE FK_CRITERIAL in ($v_list_list_id)
                                             AND FK_USER IN(SELECT
                                                              PK_LIST
                                                            FROM t_cores_list l
                                                            WHERE (ExtractValue(l.C_XML_DATA,'//item[@id=\"ddl_member\"]/value')) = pm.PK_MEMBER)
                                             AND C_STATUS = 1) AS C_TOTAL_VOTE
                                             $qry
                                      FROM t_ps_member pm
                                      WHERE C_STATUS = 1
                                      And (C_SCOPE = 1 Or C_SCOPE = 0)
                                    ";
        $results['arr_all_scope_0_1'] = $this->db->GetAll($qty_all_scope_0_1);
        //Query danh sach ket qua don vi cap xa
        $qty_all_scope_2 = "SELECT
                                        C_NAME,
                                        PK_MEMBER,
                                        FK_MEMBER,
                                        (SELECT
                                           Sum(C_VOTE)
                                         FROM t_ps_assessment
                                         WHERE FK_CRITERIAL in ($v_list_list_id)
                                             AND FK_USER IN(SELECT
                                                              PK_LIST
                                                            FROM t_cores_list l
                                                            WHERE (ExtractValue(l.C_XML_DATA,'//item[@id=\"ddl_member\"]/value')) = pm.PK_MEMBER)
                                             AND C_STATUS = 1) AS C_TOTAL_VOTE
                                             $qry
                                      FROM t_ps_member pm
                                      WHERE C_STATUS = 1
                                      And (C_SCOPE = 2)
                                    ";
        $results['arr_all_scope_2'] = $this->db->GetAll($qty_all_scope_2);
        if ($this->db->ErrorNo() == 0)
            return $results;
        return array();
    }

    /**
     * * lay danh sach ket qua danh gia can bo
     * @param type $v_staff_id
     * @param type $v_page_singel_limit
     * @param type $v_district_id
     * @param type $v_village_id
     * @return type
     */
    public function qry_all_evaluation($v_staff_id = 0, $v_page_singel_limit = 0, $v_district_id = 0, $v_village_id = 0, $v_txt_filter = '')
    {
        $limit = get_system_config_value(CFGKEY_LIMIT_DISPLAY_STAFF_ON_HOME_PAGE);
        $limit = ((int) $limit > 0) ? ' LIMIT ' . (int) $limit : 10;

        $v_conditon = '';
        if ($v_staff_id > 0)
        {
            $v_conditon .= " AND asm.FK_USER = '$v_staff_id'";
        }
        if ($v_page_singel_limit > 0)
        {
            $limit = 'LIMIT ' . $v_page_singel_limit;
            $v_curent_page = get_request_var('page', 1);
            if ($v_curent_page > 1)
            {
                $v_start = ($v_page_singel_limit * ($v_curent_page - 1)) - 1;
                $v_start = ($v_start > 0) ? $v_start : 0;
                $v_end = $v_page_singel_limit + $v_start - 1;
                $limit = " LIMIT $v_start,$v_end ";
            }
        }

        $v_conditon_membner = '';
        $v_conditon_count = '';
        if ($v_village_id > 0)
        {
            $v_conditon_membner = " And M.PK_MEMBER = '$v_village_id' ";
            $v_conditon_count = " And m.PK_MEMBER = '$v_village_id' ";
        }
        elseif ($v_district_id > 0)
        {
            $v_conditon_membner = " And M.PK_MEMBER = '$v_district_id' ";
            $v_conditon_count = " And m.PK_MEMBER = '$v_district_id' ";
        }

        if (trim($v_txt_filter) != '')
        {
            $v_conditon_membner .= " And (M.C_NAME like '%$v_txt_filter%' Or U.C_NAME like '%$v_txt_filter%')";
            $v_conditon_count .= " And (m.C_NAME like '%$v_txt_filter%' Or u.C_NAME like '%$v_txt_filter%')";
        }

        $sql = "SELECT
                        COALESCE(EU.C_TOTAL_POINT,0) AS C_TOTAL_POINT,
                        U.*,
                        M.C_NAME AS C_UNIT_NAME,
                        (SELECT
                            COUNT(*)
                          FROM (SELECT
                                  *,
                                  ExtractValue(C_XML_DATA,'//item[@id=\"ddl_member\"]/value') AS FK_MEMBER
                                FROM t_cores_list
                                WHERE FK_LISTTYPE = (SELECT
                                                       PK_LISTTYPE
                                                     FROM t_cores_listtype
                                                     WHERE C_CODE = '" . _CONST_CAN_BO_DANH_GIA . "')) u
                            LEFT JOIN t_ps_member m
                              ON u.FK_MEMBER = m.PK_MEMBER
                          WHERE (1 > 0) $v_conditon_count) AS C_TOTAL_STAFF 
                      FROM (SELECT
                              *,
                              ExtractValue(C_XML_DATA,'//item[@id=\"ddl_member\"]/value') AS FK_MEMBER
                            FROM t_cores_list
                            WHERE FK_LISTTYPE = (SELECT
                                                   PK_LISTTYPE
                                                 FROM t_cores_listtype
                                                 WHERE C_CODE = '" . _CONST_CAN_BO_DANH_GIA . "')) U
                        LEFT JOIN (SELECT
                                     ROUND((SUM(E.C_VOTE * E.C_CODE)/SUM(E.C_VOTE)),0) AS C_TOTAL_POINT,
                                     E.FK_USER
                                   FROM (SELECT
                                           SUM(asm.C_VOTE)  AS C_VOTE,
                                           asm.FK_USER,
                                           asm.FK_CRITERIAL,
                                           l.C_CODE
                                         FROM t_ps_assessment asm
                                           LEFT JOIN t_cores_list l
                                             ON asm.FK_CRITERIAL = l.PK_LIST
                                         Where (1>0) $v_conditon
                                         GROUP BY asm.FK_CRITERIAL, asm.FK_USER
                                         ORDER BY asm.FK_USER) E
                                   GROUP BY FK_USER) EU
                          ON EU.FK_USER = U.PK_LIST
                        LEFT JOIN t_ps_member M
                          ON U.FK_MEMBER = M.PK_MEMBER
                      WHERE M.C_STATUS = 1 $v_conditon_membner
                      ORDER BY C_TOTAL_POINT DESC
                      $limit";
        return $this->db->getAll($sql);
    }

    /**
     * Lay thong tin huong dan danh gia can bo
     * @return array mang chua thong tin huong dan danh gia can bo
     */
    public function qey_assessment_guidelines()
    {
        $sql = "SELECT C_XML_DATA
                    FROM t_cores_list
                    WHERE FK_LISTTYPE = (SELECT
                                           PK_LISTTYPE
                                         FROM t_cores_listtype
                                         WHERE C_CODE = '" . _CONST_DM_HUONG_DAN_DANH_GIA_CAN_BO . "')
                        AND C_STATUS = 1
                    ORDER BY C_ORDER 
                    LIMIT 1";

        return $this->db->GetOne($sql);
    }

    public function qry_max_date_history_start()
    {
        return $this->db->GetOne('SELECT DATE_FORMAT(C_HISTORY_DATE,\'%d-%m-%Y\') FROM t_ps_record_history_stat ORDER BY PK_HISTORY_STAT DESC LIMIT 1');
    }

    public function do_register()
    {
        $MODE_DATA = array();
        if ($_POST)
        {
            $v_username = get_post_var('txt_username', '');
            $v_password = get_post_var('txt_password', '');
            $v_confirm_password = get_post_var('txt_confirm_password', '');
            $v_email = get_post_var('txt_email', '');
            $v_confirm_email = get_post_var('txt_confirm_email', '');

            if (trim($v_username) == '' OR ! preg_match('/^[a-zA-Z0-9_]+$/', $v_username))
            {
                $MODE_DATA['error'] = 'Tên tài khoản không hợp lệ vui lòng chọn tên tài khoản khác.';
                return $MODE_DATA;
            }
            if (trim($v_password) == '' OR trim($v_confirm_password) == '' OR ( trim($v_password) != trim($v_confirm_password)))
            {
                $MODE_DATA['error'] = 'Mật khẩu không hợp lệ hoặc mật khẩu nhập lại không đúng.';
                return $MODE_DATA;
            }
            if (trim($v_email) == '' OR ( trim($v_email) != trim($v_confirm_email)) OR ! filter_var($v_email, FILTER_VALIDATE_EMAIL))
            {
                $MODE_DATA['error'] = 'Email không hợp lệ hoặc email nhập lại không đúng.';
                return $MODE_DATA;
            }
            // check exists username
            if ($this->db->GetOne("Select COUNT(PK_CITIZEN) From t_ps_citizen Where C_USERNAME = ?", $v_username) > 0)
            {
                $MODE_DATA['error'] = 'Tên tài khoản này đã tồn tại. Vui lòng chọn tên tài khoản khác.';
                return $MODE_DATA;
            }

            // check exists email
            $sql_exists_email = "SELECT
                                          (COUNT(PK_CITIZEN) + (SELECT COUNT(*) FROM t_ps_citizen_tmp WHERE C_EMAIL_CONFIRM = ?)) AS C_COUNT_EMAIL
                                        FROM t_ps_citizen
                                        WHERE C_EMAIL = ?
                                            AND C_USERNAME != ?";
            if ($this->db->GetOne($sql_exists_email, array($v_email, $v_email, $v_username)) > 0)
            {
                $MODE_DATA['error'] = 'Email này đã tồn tại. Vui lòng chọn Email khác.';
                return $MODE_DATA;
            }
            $v_challenge = get_post_var('recaptcha_challenge_field');
            $v_response = get_post_var('recaptcha_response_field');
            $resp = recaptcha_check_answer(_CONST_RECAPCHA_PRIVATE_KEY, $_SERVER["REMOTE_ADDR"], $v_challenge, $v_response);
            //save field
            //capcha
            if (!$resp->is_valid)
            {
                $MODE_DATA['error'] = 'capcha_error';
                return $MODE_DATA;
            }
            $v_xml_data = xml_add_declaration("<root></root>");
            //insert 
            $sql = 'INSERT INTO t_ps_citizen
                                  (
                                   C_USERNAME,
                                   C_PASSWORD,
                                   C_XML_DATA,
                                   C_EMAIL,
                                   C_STATUS
                                   )
                          VALUES (
                                  ?,
                                  ?,
                                  ?,
                                  ?,
                                  ?
                                  )';
            $params = array($v_username, encrypt_password($v_password), $v_xml_data, $v_email, '-1');
            $this->db->Execute($sql, $params);

            if ($this->db->ErrorNo() == 0)
            {
                $v_citizen_id = $this->db->GetOne("select PK_CITIZEN From t_ps_citizen where C_USERNAME = ?", $v_username);
                if ($v_citizen_id <= 0)
                {
                    $MODE_DATA['error'] = 'Xảy ra lỗi trong quá trình cập nhật. Xin vui lòng thực hiện lại.';
                    return $MODE_DATA;
                }
                // Insert t_ps_citizen_tmp and send mail
                $sql = 'INSERT INTO t_ps_citizen_tmp 
                                                    (FK_CITIZEN, 
                                                    C_CODE, 
                                                    C_CREATE_DATE, 
                                                    C_EMAIL_CONFIRM,
                                                    C_STATUS
                                                    )
                                                    VALUES
                                                    (?, 
                                                    ?, 
                                                    ?, 
                                                    ?,
                                                    1
                                                    )
                                    ';
                $v_code = uniqid();
                $v_create_date = date("Y-m-d H:i:s");
                $params = array($v_citizen_id, $v_code, $v_create_date, $v_email);
                $this->db->Execute($sql, $params);
                if ($this->db->ErrorNo() != 0)
                {
                    //xoa du lieu vua cap nhat  neu trong qua trinh cap nhat xay ra loi
                    $this->db->Execute("delete from t_ps_citizen where PK_CITIZEN = ?", $v_citizen_id);
                    $MODE_DATA['error'] = 'Xảy ra lỗi trong quá trình cập nhật. Xin vui lòng thực hiện lại.';
                    return $MODE_DATA;
                }
                Session::set('account_' . $v_username, '1');
                $MODE_DATA['username'] = $v_username;
                $MODE_DATA['email'] = $v_email;
                $MODE_DATA['code'] = $v_code;
                $MODE_DATA['create_date'] = $v_create_date;
                return $MODE_DATA;
            }
        }
        $MODE_DATA['error'] = 'Xảy ra lỗi trong quá trình thực hiện. Vui lòng thực hiện lại.';
        return $MODE_DATA;
    }

    //Gui ma kich hoat
    public function send_code_trigger($v_username = '')
    {
        $MODE_DATA = array();
        if (trim($v_username) != '')
        {
            $v_citizen_id = $this->db->GetOne("SELECT
                                                    PK_CITIZEN
                                                  FROM t_ps_citizen c
                                                  WHERE C_STATUS =  - 1
                                                      AND (SELECT
                                                             COUNT(FK_CITIZEN)
                                                           FROM t_ps_citizen_tmp
                                                           WHERE FK_CITIZEN = c.PK_CITIZEN) = 1
                                                      AND C_USERNAME = ?", $v_username);
            if ($v_citizen_id <= 0)
            {
                $MODE_DATA['error'] = '0';
                return $MODE_DATA;
            }
            $arr_citizen_tmp = $this->db->GetRow("SELECT C_EMAIL_CONFIRM,C_CREATE_DATE FROM t_ps_citizen_tmp Where FK_CITIZEN= ?", $v_citizen_id);

            // Insert t_ps_citizen_tmp and send mail
            $sql = 'Update t_ps_citizen_tmp 
                                            Set
                                                C_CODE = ?
                                                Where
                                                FK_CITIZEN = ? ;
                                                ';
            $v_code = uniqid();
            $params = array($v_code, $v_citizen_id);
            $this->db->Execute($sql, $params);

            if ($this->db->ErrorNo() == 0 && sizeof($arr_citizen_tmp) > 0)
            {
                $MODE_DATA['username'] = $v_username;
                $MODE_DATA['email'] = $arr_citizen_tmp['C_EMAIL_CONFIRM'];
                $MODE_DATA['code'] = $v_code;
                $MODE_DATA['create_date'] = $arr_citizen_tmp['C_CREATE_DATE'];
                ;
                return $MODE_DATA;
            }
        }
        return $MODE_DATA['error'] = 0;
    }

//         get array t_ps_citizen_tmp by username
    public function qry_citizen_tmp_get_by_username($v_username = '')
    {
        if (trim($v_username) != '')
        {
            $qry = "SELECT *
                            FROM t_ps_citizen_tmp
                            WHERE FK_CITIZEN IN(SELECT
                                                   PK_CITIZEN
                                                 FROM t_ps_citizen
                                                 WHERE C_USERNAME = ? AND C_STATUS = -1)
                                                 AND C_STATUS  =1 ";
            return $this->db->GetAll($qry, array($v_username));
        }
        return array();
    }

    public function update_account_trigger()
    {
        $MODE_DATA = array();
        $v_username = get_request_var('hdn_username', '');
        $v_name = get_request_var('txt_name', '');
        $v_address = get_request_var('txt_address', '');
        $v_code = get_request_var('txt_code', '');
        $v_tel = get_request_var('txt_tel', '');
        $v_organ = get_request_var('rd_organ', '');

        if (trim($v_tel) == '' OR preg_match('/^[0-9]+$/', $v_tel, $match) != 1 OR strlen($v_tel) < 9 OR strlen($v_tel) > 12)
        {
            $MODE_DATA['error'] = 'Bạn chưa nhập số điện thoại hoặc số điện thoại không hợp lệ.';
            return $MODE_DATA;
        }
        if (trim($v_name) == '')
        {
            $MODE_DATA['error'] = 'Bạn chưa nhập họ tên.';
            return $MODE_DATA;
        }
        if (trim($v_address) == '')
        {
            $MODE_DATA['error'] = 'Bạn chưa nhập địa chỉ.';
            return $MODE_DATA;
        }
        if (trim($v_code) == '')
        {
            $MODE_DATA['error'] = 'Bạn chưa nhập mã xác nhận hoặc mã xác nhận không đúng.';
            return $MODE_DATA;
        }
        //kiem tra su ton tai tai khoan chua kich hoat
        $arr_citizen_id = $this->qry_count_citizen($v_username, $v_code);

        if (sizeof($arr_citizen_id) != 1 OR ! ($v_organ == 1 OR $v_organ == 0))
        {
            $MODE_DATA['error'] = 'Mã xác nhận không đúng hoăc tài khoản hiện tại của bạn đã được kích hoạt hoặc đã bị xóa do quá thời gian yêu cầu kích hoạt!. Vui lòng kiểm tra lại';
            return $MODE_DATA;
        }
        $v_citizen_id = $arr_citizen_id[0]['FK_CITIZEN'];
        if ($v_organ == 0)
        {
            //Doi tương la ca nhan
            $v_gender = get_request_var('sel_gender', '');
            $v_birthday = get_request_var('txt_birthday', '');
            $v_birthday = jwDate::ddmmyyyy_to_yyyymmdd(trim($v_birthday));
            $v_identity_card = get_request_var('txt_identity_card', '');

            if (!in_array($v_gender, array(0, 1)))
            {
                $MODE_DATA['error'] = 'Bạn chưa chọn giới tính.';
                return $MODE_DATA;
            }
            if (trim($v_birthday) == '')
            {
                $MODE_DATA['error'] = 'Bạn chưa nhập ngày sinh hoặc ngày sinh không hợp lệ.';
                return $MODE_DATA;
            }
            if (trim($v_identity_card) == '' OR preg_match('/^[0-9]+$/', $v_identity_card, $match) != 1 OR ! in_array(strlen($v_identity_card), array(9, 12)))
            {
                $MODE_DATA['error'] = 'Bạn chưa nhập CMND hoặc CMND không hợp lệ.';
                return $MODE_DATA;
            }
            $v_xml = "<root>
                            <item>
                                <tel><![CDATA[$v_tel]]></tel>
                                <name><![CDATA[$v_name]]></name>
                                <address><![CDATA[$v_address]]></address>
                                <birthday><![CDATA[$v_birthday]]></birthday>
                                <identity_card><![CDATA[$v_identity_card]]></identity_card> 
                                <gender><![CDATA[$v_gender]]></gender> 
                            </item>
                        </root>";
            $v_xml = xml_add_declaration($v_xml);
            //update
            $sql = 'UPDATE t_ps_citizen 
                                SET
                                C_USERNAME = ?, 
                                C_XML_DATA = ?, 
                                C_ORGAN = 0, 
                                C_STATUS = 1
                                WHERE
                                PK_CITIZEN = ? ';
            $params = array($v_username, $v_xml, $v_citizen_id);
        }
        else
        {
            $v_company_perfix = get_request_var('txt_company_perfix', '');
            $v_tax_code = get_request_var('txt_tax_code', '');
            $v_name_en = get_request_var('txt_name_en', '');
            $v_business_registers = get_request_var('txt_business_registers', '');
            $v_date = get_request_var('txt_date', '');
            $v_granting_agencies = get_request_var('txt_granting_agencies', '');
            $v_boss = get_request_var('txt_boss', '');
            $v_position = get_request_var('txt_position', '');
            if (trim($v_company_perfix) == '')
            {
                $MODE_DATA['error'] = 'Vui lòng nhập mã công ty hoặc tổ chức.';
                return $MODE_DATA;
            }
            if (trim($v_tax_code) == '')
            {
                $MODE_DATA['error'] = 'Vui lòng nhập Mã số thuế của công ty hoặc tổ chức.';
                return $MODE_DATA;
            }
            $v_xml = "<root>
                            <item>
                                <tel><![CDATA[$v_tel]]></tel>
                                <name><![CDATA[$v_name]]></name>
                                <name_en><![CDATA[$v_name_en]]></name_en>
                                <business_registers><![CDATA[$v_business_registers]]></business_registers>
                                <business_date><![CDATA[$v_date]]></business_date>
                                <granting_agencies><![CDATA[$v_granting_agencies]]></granting_agencies>
                                <address><![CDATA[$v_address]]></address>
                                <tax_code><![CDATA[$v_tax_code]]></tax_code>
                                <company_prefix><![CDATA[$v_company_perfix]]></company_prefix>
                                <boss><![CDATA[$v_boss]]></boss>
                                <boss_position><![CDATA[$v_position]]></boss_position>
                            </item>
                        </root>";
            $v_xml = xml_add_declaration($v_xml);
            //update
            $sql = 'UPDATE t_ps_citizen 
                                SET
                                C_USERNAME = ?, 
                                C_XML_DATA = ?, 
                                C_ORGAN = 1, 
                                C_STATUS = 1
                                WHERE
                                PK_CITIZEN = ? ';
            $params = array($v_username, $v_xml, $v_citizen_id);
        }

        $this->db->Execute($sql, $params);
        if ($this->db->ErrorNo() == 0)
        {
            $this->db->Execute("DELETE FROM t_ps_citizen_tmp WHERE	FK_CITIZEN = ?", array($v_citizen_id));
            if ($this->db->ErrorNo() == 0)
            {
                return '1';
            }
        }
        $MODE_DATA['error'] = 'Xảy ra lỗi trong quá trình cập nhận. Vui lòng thực hiện lại.';
        return $MODE_DATA;
    }

    //kiem tra tai khoan da kich hoat hay chua?
    private function qry_count_citizen($v_usernam = '', $v_code = '')
    {
        if (trim($v_usernam) == '' OR trim($v_code) == '')
        {
            return 0;
        }
        $v_limit_account_date_trigger = defined('_CONS_LIMIT_ACCOUNT_DATE_TRIGGER') ? _CONS_LIMIT_ACCOUNT_DATE_TRIGGER : 7;
        $sql = 'SELECT
                            FK_CITIZEN
                          FROM t_ps_citizen_tmp ct
                          WHERE FK_CITIZEN = (SELECT
                                                PK_CITIZEN
                                              FROM t_ps_citizen
                                              WHERE C_STATUS =  -1
                                                  AND C_USERNAME = ?)
                              AND ct.C_CODE = ?
                              AND ct.C_STATUS = 1
                              AND date_add(ct.C_CREATE_DATE,interval ' . $v_limit_account_date_trigger . ' day) >= now()    
                            ';
        $params = array($v_usernam, $v_code);
        return $this->db->getAll($sql, $params);
    }

    public function do_login($v_username = '', $v_password = '')
    {
        $MODE_DATA = array();
        if (trim($v_username) == '' OR $v_password == '')
        {
            $MODE_DATA['error'] = 'Vui lòng nhập đầy đủ tên tài khoản và mật khẩu.';
            return $MODE_DATA;
        }
        //Check tai khoan bi khóa
        $sql = "SELECT
                            PK_CITIZEN,
                            ExtractValue(C_XML_DATA, '//reason') AS C_REASON
                          FROM t_ps_citizen
                          WHERE C_USERNAME = ?
                                And C_PASSWORD = ? 
                              AND C_STATUS =  0";
        $arr_reason = $this->db->GetRow($sql, array($v_username, $v_password));
        if (sizeof($arr_reason) > 0)
        {
            $v_reason = isset($arr_reason['C_REASON']) ? $arr_reason['C_REASON'] : '';
            $MODE_DATA['error'] = "Tài khoản của bạn đã bị khóa.
                                        \n Lý do khóa: $v_reason 
                                        \n Mọi thắc mắc vui lòng liên hệ với nhà cung cấp dịch vụ.";
            return $MODE_DATA;
        }

        $sql = "SELECT count(PK_CITIZEN)
                 FROM t_ps_citizen
                 WHERE C_USERNAME = ?";
        if ($this->db->GetOne($sql, array($v_username)) != 1)
        {
            $MODE_DATA['error'] = 'Tài khoản hoặc mật khẩu không chính xác. Vui lòng kiểm tra lại';
            return $MODE_DATA;
        }

        $sql = "SELECT PK_CITIZEN,
                           ExtractValue(C_XML_DATA,'//item/name') as C_NAME,
                           C_EMAIL,
                           C_STATUS
                 FROM t_ps_citizen
                 WHERE C_USERNAME = ?
                        AND C_PASSWORD = ?
                        AND C_STATUS <> 0";
        $arr_single_citizen = $this->db->GetAll($sql, array($v_username, $v_password));
        if (sizeof($arr_single_citizen) <= 0)
        {
            $MODE_DATA['error'] = 'Tài khoản hoặc mật khẩu không chính xác. Vui lòng kiểm tra lại1';
            return $MODE_DATA;
        }

        $v_citizen_id = $arr_single_citizen[0]['PK_CITIZEN'];
        $v_citizen_name = $arr_single_citizen[0]['C_NAME'];
        $v_stauts = $arr_single_citizen[0]['C_STATUS'];
        $v_email = $arr_single_citizen[0]['C_EMAIL'];
        if ($v_status == -1)
        {
            Session::set('citizen_login_name', $v_username);
            Session::set('citizen_login_id', $v_citizen_id);
            Session::set('citizen_name', $v_citizen_name);
            Session::set('citizen_email', $v_email);
            Session::set('account_' . $v_username, '1'); // Trang thai chua kich hoat
        }
        else
        {
            @$dom = simplexml_load_string($v_xml_data);
            if ($dom)
            {
                $v_email = (string) $dom->xpath('//item/email');
            }
            Session::set('citizen_login_name', $v_username);
            Session::set('citizen_login_id', $v_citizen_id);
            Session::set('citizen_name', $v_citizen_name);
            Session::set('citizen_role', $v_stauts);
            Session::set('citizen_email', $v_email);
        }

        if (get_post_var('chk_save_password'))
        {
            setcookie('_uuu_', $v_username, time() + 60 * 60 * 24 * 3);
            setcookie('_ppp_', $v_password, time() + 60 * 60 * 24 * 3);
        }
        $MODE_DATA['success'] = '1';
        return $MODE_DATA;
    }

    public function qry_single_account_citizen()
    {
        $this->check_citizen_login();

        $v_citizen_login_id = Session::get('citizen_login_id');
        $v_citizen_login_name = Session::get('citizen_login_name');

        $sql = "select 	PK_CITIZEN, 
                                C_USERNAME, 
                                C_EMAIL, 
                                C_XML_DATA, 
                                C_ORGAN, 
                                (select C_EMAIL_CONFIRM from t_ps_citizen_tmp where FK_CITIZEN = c.PK_CITIZEN And C_STATUS = 2)as C_EMAIL_CONFIRM ,
                                 C_STATUS
                                 from t_ps_citizen  c
                                where PK_CITIZEN = ? 
                                And C_USERNAME = ?";
        return $this->db->GetRow($sql, array($v_citizen_login_id, $v_citizen_login_name));
    }

    public function check_block_account()
    {
        $v_citizen_id = Session::get('citizen_login_id');
        $v_citizen_id = (int) $v_citizen_id > 0 ? $v_citizen_id : 0;
        return $this->db->GetOne("select C_STATUS from t_ps_citizen where PK_CITIZEN = ?", array($v_citizen_id));
    }

    public function do_update_citizen_account()
    {
        $v_citizen_id = get_post_var('hdn_citizen_id', '');
        $v_name = get_post_var('txt_name', '');
        $v_new_pass = get_post_var('txt_new_password', '');
        $v_confirm_new_pass = get_post_var('txt_confirm_new_password', '');
        $v_current_pass = get_post_var('txt_current_password', '');
        $v_current_pass = encrypt_password($v_current_pass);
        $v_email = get_post_var('txt_email', '');
        $v_tel = get_post_var('txt_tel', '');
        $v_address = get_post_var('txt_address', '');
        $v_username = Session::get('citizen_login_name');

        //check tel: Chi chap nhan số độ gài 9-12 ký tự
        if (trim($v_tel) == '' OR preg_match('/^[0-9]+$/', $v_tel, $match) != 1 OR strlen($v_tel) < 9 OR strlen($v_tel) > 12)
        {
            $MODE_DATA['error'] = 'Bạn chưa nhập số điện thoại hoặc số điện thoại không hợp lệ.';
            return $MODE_DATA;
        }
        // check exists username
        if ($this->db->GetOne("Select COUNT(PK_CITIZEN) From t_ps_citizen Where C_STATUS = 1 And C_USERNAME = ?", $v_username) != 1)
        {
            $MODE_DATA['error'] = 'Tài khoản đã bị khóa hoặc xảy ra lỗi. Vui lòng thực hiện lại.';
            return $MODE_DATA;
        }

        //check password
        if ($this->db->GetOne("select Count(PK_CITIZEN) from t_ps_citizen where PK_CITIZEN = ? And C_USERNAME= ? And C_STATUS = 1 And C_PASSWORD = ?", array($v_citizen_id, $v_username, $v_current_pass)) != 1)
        {
            $MODE_DATA['error'] = 'Mật khẩu hiện tại không đúng. Vui lòng kiểm tra lại.';
            return $MODE_DATA;
        }

        if (trim($v_new_pass) != trim($v_confirm_new_pass) && trim($v_new_pass) != '')
        {
            $MODE_DATA['error'] = 'Xác nhận lại mật khẩu mới không đúng.';
            return $MODE_DATA;
        }
        if (trim($v_new_pass) != '')
        {
            $v_current_pass = encrypt_password($v_new_pass);
        }

        if (trim($v_current_pass) == '')
        {
            $MODE_DATA['error'] = 'Mật khẩu không hợp lệ hoặc mật khẩu nhập lại không đúng.';
            return $MODE_DATA;
        }
        if (trim($v_email) == '' OR ! filter_var($v_email, FILTER_VALIDATE_EMAIL))
        {
            $MODE_DATA['error'] = 'Email không hợp lệ hoặc email nhập lại không đúng.';
            return $MODE_DATA;
        }
        // check exists email
        $sql_exists_email = "SELECT
                                      (COUNT(PK_CITIZEN) + (SELECT COUNT(*) FROM t_ps_citizen_tmp WHERE C_EMAIL_CONFIRM = ? and FK_CITIZEN <> ?)) AS C_COUNT_EMAIL
                                    FROM t_ps_citizen c
                                    WHERE C_EMAIL = ?
                                        AND C_USERNAME != ? And PK_CITIZEN != ? And C_EMAIL != ?";
        if ($this->db->GetOne($sql_exists_email, array($v_email, $v_citizen_id, $v_email, $v_username, $v_citizen_id, $v_email)) > 0)
        {
            $MODE_DATA['error'] = 'Email này đã tồn tại. Vui lòng chọn Email khác.';
            return $MODE_DATA;
        }


        $v_current_email = Session::get('citizen_email');

        $arr_single_citizen = $this->db->GetRow("select C_ORGAN,C_XML_DATA from t_ps_citizen where PK_CITIZEN = ? and C_USERNAME = ? And C_STATUS = 1", array($v_citizen_id, $v_username));
        $v_organ = $arr_single_citizen['C_ORGAN'];
        $v_organ = $arr_single_citizen['C_ORGAN'];
        $v_xml_data = $arr_single_citizen['C_XML_DATA'];
        @$dom_xml = simplexml_load_string($v_xml_data);
        $v_reason = '';
        if ($dom_xml)
        {
            $v_xptah_reason = "//reason";
            $obj_reason = $dom_xml->xpath($v_xptah_reason);
            $v_reason = isset($obj_reason[0]) ? (string) $obj_reason[0] : '';
        }
        if ($v_organ == 0)
        {
            //Cas nhan
            $v_birth_day = get_post_var('txt_birth_day', '');
            $v_gender = get_post_var('sel_gender', '');
            $v_identity_card = get_post_var('txt_identity_card', '');
            if (!in_array($v_gender, array(0, 1)))
            {
                $MODE_DATA['error'] = 'Bạn chưa chọn giới tính.';
                return $MODE_DATA;
            }
            if (trim($v_birth_day) == '')
            {
                $MODE_DATA['error'] = 'Bạn chưa nhập ngày sinh hoặc ngày sinh không hợp lệ.';
                return $MODE_DATA;
            }
            if (trim($v_identity_card) == '' OR preg_match('/^[0-9]+$/', $v_identity_card, $match) != 1 OR ! in_array(strlen($v_identity_card), array(9, 12)))
            {
                $MODE_DATA['error'] = 'Bạn chưa nhập CMND hoặc CMND không hợp lệ.';
                return $MODE_DATA;
            }
            $v_xml = "<root>
                            <item>
                                <tel><![CDATA[$v_tel]]></tel>
                                <name><![CDATA[$v_name]]></name>
                                <address><![CDATA[$v_address]]></address>
                                <birthday><![CDATA[$v_birth_day]]></birthday>
                                <identity_card><![CDATA[$v_identity_card]]></identity_card> 
                                <gender><![CDATA[$v_gender]]></gender> 
                            </item>
                        </root>";
            $v_xml = xml_add_declaration($v_xml);

            $params = array($v_current_pass, $v_current_email, $v_xml, $v_citizen_id, $v_username);
        }
        elseif ($v_organ == 1)
        {
            $v_company_perfix = get_request_var('txt_company_perfix', '');
            $v_tax_code = get_request_var('txt_tax_code', '');
            $v_name_en = get_request_var('txt_name_en', '');
            $v_business_registers = get_request_var('txt_business_registers', '');
            $v_date = get_request_var('txt_date', '');
            $v_granting_agencies = get_request_var('txt_granting_agencies', '');
            $v_boss = get_request_var('txt_boss', '');
            $v_position = get_request_var('txt_position', '');

            if (trim($v_company_perfix) == '')
            {
                $MODE_DATA['error'] = 'Vui lòng nhập mã công ty/tổ chức.';
                return $MODE_DATA;
            }
            if (trim($v_tax_code) == '')
            {
                $MODE_DATA['error'] = 'Vui lòng nhập Mã số thuế của công ty hoặc tổ chức.';
                return $MODE_DATA;
            }

            $v_xml = "<root>
                            <item>
                                <tel><![CDATA[$v_tel]]></tel>
                                <name><![CDATA[$v_name]]></name>
                                <name_en><![CDATA[$v_name_en]]></name_en>
                                <business_registers><![CDATA[$v_business_registers]]></business_registers>
                                <business_date><![CDATA[$v_date]]></business_date>
                                <granting_agencies><![CDATA[$v_granting_agencies]]></granting_agencies>
                                <address><![CDATA[$v_address]]></address>
                                <tax_code><![CDATA[$v_tax_code]]></tax_code>
                                <company_prefix><![CDATA[$v_company_perfix]]></company_prefix>
                                <boss><![CDATA[$v_boss]]></boss>
                                <boss_position><![CDATA[$v_position]]></boss_position>
                            </item>
                            <reason><![CDATA[$v_reason]]></reason>
                        </root>";
            $v_xml = xml_add_declaration($v_xml);

            $params = array($v_current_pass, $v_current_email, $v_xml, $v_citizen_id, $v_username);
        }
        else
        {
            $MODE_DATA['error'] = 'Hệ thống cập nhật xảy ra lỗi. Vui lòng thử lại sau.';
            return $MODE_DATA;
        }
        $sql = "UPDATE t_ps_citizen 
                                SET
                                C_PASSWORD = ? , 
                                C_EMAIL = ?, 
                                C_XML_DATA = ? 
                                WHERE
                                PK_CITIZEN = ?
                                And C_USERNAME = ?
                                ";
        $this->db->Execute($sql, $params);
        if ($this->db->ErrorNo() == 0)
        {
            if ($v_email != Session::get('citizen_email'))
            {
                $v_code = uniqid();
                $v_create_date = date("Y-m-d H:i:s");

                if ($this->db->GetOne("select count(FK_CITIZEN) from t_ps_citizen_tmp where FK_CITIZEN = ?", $v_citizen_id) > 0)
                {
                    $sql = "UPDATE t_ps_citizen_tmp 
                                    SET
                                    C_CODE =?, 
                                    C_EMAIL_CONFIRM = ?, 
                                    C_CREATE_DATE = ? 
                                    WHERE
                                    FK_CITIZEN = ?";

                    $params_tmp = array($v_code, $v_email, $v_create_date, $v_citizen_id);
                }
                else
                {
                    $sql = "INSERT INTO t_ps_citizen_tmp
                                    (FK_CITIZEN,
                                     C_CODE,
                                     C_EMAIL_CONFIRM,
                                     C_CREATE_DATE,
                                     C_STATUS)
                        VALUES (?,
                                ?,
                                ?,
                                ?,
                                2)";
                    $params_tmp = array($v_citizen_id, $v_code, $v_email, $v_create_date);
                }

                $this->db->Execute($sql, $params_tmp);
                $MODE_DATA['username'] = $v_username;
                $MODE_DATA['email'] = $v_email;
                $MODE_DATA['code'] = $v_code;
                $MODE_DATA['create_date'] = $v_create_date;
                $MODE_DATA['citizen_id'] = $v_citizen_id;
                return $MODE_DATA;
            }
            return 1;
        }
        $MODE_DATA['error'] = 'Tài khoản đã bị khóa hoặc xảy ra lỗi. Vui lòng thực hiện lại.';
        return $MODE_DATA;
    }

    public function dsp_destroyed_change_email($v_username = '', $v_citizen_id = 0)
    {
        $sql = "DELETE
                                    FROM t_ps_citizen_tmp
                                    WHERE FK_CITIZEN = (SELECT
                                                          PK_CITIZEN
                                                        FROM t_ps_citizen
                                                        WHERE PK_CITIZEN = ?
                                                            AND C_USERNAME = ?
                                                            AND C_STATUS = 1)
                                        AND C_STATUS = 2 ";
        $this->db->Execute($sql, array($v_citizen_id, $v_username));
        if ($this->db->ErrorNo() == 0)
        {
            return 1;
        }
        return 0;
    }

    //check username
    public function check_username_exist($username)
    {
        // check exists email
        $sql_exists_username = "SELECT COUNT(*) FROM t_ps_citizen WHERE C_USERNAME = ?";
        if ($this->db->GetOne($sql_exists_username, array($username)) > 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    //check email and username exist
    public function check_username_and_email($user, $email)
    {
        $sql_exists = "SELECT PK_CITIZEN FROM t_ps_citizen WHERE C_USERNAME = ? AND C_Email=?";
        $id = $this->db->GetOne($sql_exists, array($user, $email));
        return $id;
    }

    //insert or update table citizen tmp  by binhpt
    public function data_table_citizen_tmp($v_citizen_id, $email, $event = 3)
    {
        $v_code = uniqid();
        $v_create_date = date("Y-m-d H:i:s");
        $sql = 'SELECT COUNT(*) FROM t_ps_citizen_tmp WHERE C_EMAIL_CONFIRM = ? AND FK_CITIZEN = ?';
        if ($this->db->GetOne($sql, array($email, $v_citizen_id)) > 0)
        {
            $sql_update = "UPDATE t_ps_citizen_tmp SET C_CODE=?, C_CREATE_DATE=?,C_STATUS = ? WHERE FK_CITIZEN=? AND C_EMAIL_CONFIRM=?";
            $params = array($v_code, $v_create_date, $event, $v_citizen_id, $email);
            if ($this->db->Execute($sql_update, $params))
                return $v_code;
            else
                return FALSE;
        }
        else
        {
            $sql_insert = 'INSERT INTO t_ps_citizen_tmp(FK_CITIZEN,C_CODE,C_CREATE_DATE,C_EMAIL_CONFIRM,C_STATUS)VALUES(?,?,?,?,?) ';
            $params = array($v_citizen_id, $v_code, $v_create_date, $email, $event);
            if ($this->db->Execute($sql_insert, $params))
                return $v_code;
            else
                return FALSE;
        }
    }

    /**
     * Kiem tra trang thai dang nhap. Neu chua dang nhap load lai page 
     */
    private function check_citizen_login()
    {
        //Check trang thai dang nhap cua citizen
        $v_username_old = Session::get('citizen_login_name');
        if (trim($v_username_old) == '' OR $v_username_old == NULL)
        {
            $page = $_SERVER['PHP_SELF'];
            header("Refresh: 0; url=$page");
            return;
        }
    }

    //check reset password by binhpt
    public function check_reset_password($email, $code)
    {
        $sql = 'SELECT COUNT(*) FROM t_ps_citizen_tmp WHERE C_EMAIL_CONFIRM = ? AND C_CODE = ? AND C_STATUS=?';
        $params = array($email, $code, 3);
        if ($this->db->GetOne($sql, $params) > 0)
        {
            return TRUE;
        }
        return FALSE;
    }

    //Reset password
    public function change_password($email, $pass)
    {
        $sql_update = "UPDATE t_ps_citizen SET C_PASSWORD=? WHERE C_EMAIL=?";
        $params = array(encrypt_password($pass), $email);
        $sql_update_status = "DELETE FROM t_ps_citizen_tmp WHERE C_EMAIL_CONFIRM=?";
        $this->db->Execute($sql_update_status, array($email));
        return $this->db->Execute($sql_update, $params);
    }

    public function send_email_activation_code()
    {
        $v_citizen_id = Session::get('citizen_login_id');
        //check thong tin tai khoan co phai da yeu cau thay doi email
        $qry = "SELECT
                    tmp.*,
                    c.C_USERNAME
                  FROM t_ps_citizen_tmp tmp
                    JOIN t_ps_citizen c
                      ON tmp.FK_CITIZEN = c.PK_CITIZEN

                  WHERE c.PK_CITIZEN = ? AND tmp.C_STATUS = 2 And c.C_STATUS =1";
        $arr_citizen_tmp = $this->db->GetAll($qry, $v_citizen_id);
        if (sizeof($arr_citizen_tmp) != 1)
        {
            return array();
        }
        $sql = "UPDATE t_ps_citizen_tmp 
                                SET
                                C_CODE =?
                                WHERE
                                    FK_CITIZEN = ?
                                And C_STATUS = 2
                                ";
        $v_code = uniqid();
        $params_tmp = array($v_code, $v_citizen_id);
        $this->db->Execute($sql, $params_tmp);

        $MODE_DATA['username'] = $v_username = $arr_citizen_tmp[0]['C_USERNAME'];
        $MODE_DATA['email'] = $v_email = $arr_citizen_tmp[0]['C_EMAIL_CONFIRM'];
        $MODE_DATA['code'] = $v_code;
        $MODE_DATA['create_date'] = $v_create_date = $arr_citizen_tmp[0]['C_CREATE_DATE'];
        $MODE_DATA['citizen_id'] = $v_citizen_id;
        if ($this->db->ErrorNo() == 0)
        {
            return $MODE_DATA;
        }
        return array();
    }

    public function check_account_change_email($v_username = '', $v_citizen_id = 0)
    {
        $v_limit_account_date_trigger = defined('_CONS_LIMIT_ACCOUNT_DATE_TRIGGER') ? _CONS_LIMIT_ACCOUNT_DATE_TRIGGER : 7;
        $sql = "SELECT
                            COUNT(*)
                          FROM t_ps_citizen_tmp ct
                          WHERE FK_CITIZEN = ?
                              And  (SELECT
                                               COUNT(*)
                                             FROM t_ps_citizen
                                             WHERE PK_CITIZEN = ?
                                                AND C_USERNAME = ? 
                                                 AND C_STATUS = 1) = 1
                              AND C_STATUS = 2 
                              AND date_add(ct.C_CREATE_DATE,interval '$v_limit_account_date_trigger' day) >= now()  ";
        $params = array($v_citizen_id, $v_citizen_id, $v_username);
        return $this->db->GetOne($sql, $params);
    }

    public function dsp_active_change_email($v_citizen_id = 0, $v_code = '')
    {
        $MODE_DATA = array();
        $v_limit_account_date_trigger = defined('_CONS_LIMIT_ACCOUNT_DATE_TRIGGER') ? _CONS_LIMIT_ACCOUNT_DATE_TRIGGER : 7;
        if (intval($v_citizen_id) > 0 && trim($v_code) != '')
        {
            $sql = "SELECT
                            COUNT(*)
                          FROM t_ps_citizen_tmp ct
                          WHERE FK_CITIZEN = ?
                              And  (SELECT
                                               COUNT(*)
                                             FROM t_ps_citizen
                                             WHERE PK_CITIZEN = ct.FK_CITIZEN
                                                 AND C_STATUS = 1) = 1
                              AND ct.C_STATUS = 2 
                              AND ct.C_CODE = ? 
                              AND date_add(ct.C_CREATE_DATE,interval '$v_limit_account_date_trigger' day) >= now()  ";
            $params = array($v_citizen_id, $v_code);
            if ($this->db->GetOne($sql, $params) == 1)
            {
                $sql = "UPDATE t_ps_citizen c
                        SET C_EMAIL = (SELECT
                                         C_EMAIL_CONFIRM FROM t_ps_citizen_tmp
                        WHERE FK_CITIZEN = c.PK_CITIZEN AND C_CODE  = ?) WHERE PK_CITIZEN = ?";
                $params = array($v_code, $v_citizen_id);
                $this->db->Execute($sql, $params);
                if ($this->db->ErrorNo() == 0)
                {
                    $this->db->Execute("DELETE FROM t_ps_citizen_tmp WHERE FK_CITIZEN = ?", $v_citizen_id);
                    $MODE_DATA['success'] = 1;
                }
            }
            else
            {
                $MODE_DATA['error'] = 'Đã xảy ra lỗi. Yêu cầu thay đổi email của bạn đã được kích hoạt hoặc thời gian yêu cầu xác nhận thay đổi email đã quá hạn cho phép.';
            }
        }
        else
        {
            $MODE_DATA['error'] = 'Vui lòng nhập mã kích hoạt';
        }
        return $MODE_DATA;
    }

    public function qry_all_record($arr_filter = array())
    {
        $this->check_citizen_login();
        
        $v_fk_citizen = Session::get('citizen_login_id');
        
        $v_record_no = isset($arr_filter['txt_record_no']) ? $arr_filter['txt_record_no'] : '';
        $v_begin_send_date = isset($arr_filter['txt_begin_send_date']) ? $arr_filter['txt_begin_send_date'] : '';
        $v_begin_send_date = jwDate::ddmmyyyy_to_yyyymmdd($v_begin_send_date);
        
        $v_end_send_date = isset($arr_filter['txt_end_send_date']) ? $arr_filter['txt_end_send_date'] : '';
        $v_end_send_date = jwDate::ddmmyyyy_to_yyyymmdd($v_end_send_date);

        $v_conditon = '';
        if (trim($v_record_no) != '')
        {
            $v_conditon .= " And C_RECORD_NO = '$v_record_no'";
        }
        if (trim($v_begin_send_date) != '')
        {
            $v_conditon .= " And C_SUBMITTED_DATE >= '$v_begin_send_date'";
        }
        if (trim($v_end_send_date) != '')
        {
            $v_conditon .= " And C_SUBMITTED_DATE <= '$v_end_send_date'";
        }
        $v_conditon .= " And FK_CITIZEN =  '$v_fk_citizen'";
        
        $v_citizen_id = Session::get('citizen_login_id');
        $v_limit = defined('_CONST_DEFAULT_ROWS_PER_PAGE') ? _CONST_DEFAULT_ROWS_PER_PAGE : 10;
        $v_page = get_request_var('page', 1);
        $v_start = $v_limit * ($v_page - 1);
        return $this->db->GetAll("
                        SELECT 	
                            @rownum:=@rownum + 1 AS RN ,
                            (SELECT COUNT(*) FROM t_ps_record where (1>0) $v_conditon ) AS TOTAL_RECORD ,
                            PK_RECORD, 
                            (SELECT C_NAME FROM t_ps_record_type WHERE PK_RECORD_TYPE  = R.FK_RECORD_TYPE) AS C_RECORD_TYPE_NAME, 
                            C_RECORD_NO, 
                            C_RECEIVE_DATE, 
                             CAST(R.C_RECEIVE_DATE AS CHAR(19)) AS C_RECEIVE_DATE , CAST(R.C_RETURN_DATE AS CHAR(19)) AS C_RETURN_DATE , 
                            C_XML_PROCESSING, 
                            C_XML_DATA, 
                            C_REJECTED, 
                            C_CITIZEN_NAME, 
                            C_REJECT_DATE, 
                            C_SUBMITTED_DATE,
                            C_STATUS,
                            C_DELETED,
                            C_SUBMITTED_DATE as C_FACT_RECEIVE_DATA,
                            CASE 
                                WHEN R.C_DELETED = 1 THEN 
                                'Đã bị xóa'
                                WHEN ISNULL(C_RECORD_NO) THEN
                                'Chưa xác nhận'
                                WHEN ISNULL(C_PROCESSING_RECORD) THEN
                                'Một cửa chưa xác nhận'
                        ELSE R.C_PROCESSING_RECORD 
                        END AS C_PROCESSING_RECORD
                            FROM 	
                            t_ps_record R 
                            , (SELECT @rownum:=0) r 
                             where (1>0) $v_conditon
                                 And FK_CITIZEN = '$v_citizen_id'
                                 order by PK_RECORD DESC
                                 Limit $v_start,$v_limit
                            ");
    }
    /**
     * kiem tra ma ho so da duoc danh gia chua
     * @param type $v_record_no
     * @return boolean
     */

    /**
     * kiem tra ma ho so da duoc danh gia chua
     * @param type $v_record_no
     * @return boolean
     */
    public function check_record_evaluated($v_record_no)
    {

        $sql = "SELECT COUNT(*) FROM t_ps_record_evaluated WHERE C_RECORD_NO = '$v_record_no'";
        if ($this->db->getOne($sql) > 0)
        {
            return true;
        }
        return false;
    }
    
    public function qry_all_spec_exists_record()
    {
        $sql = "SELECT
                    C_CODE,
                    C_NAME,
                    C_UNIT_CODE,
                    if(FK_VILLAGE_ID >0,CONCAT(C_UNIT_CODE,'_',FK_VILLAGE_ID),'' ) as C_UNIT_CODE_FK_FILLAGE_ID
                  FROM t_cores_list
                    LEFT JOIN (SELECT
                                 C_SPEC_CODE,
                                 C_UNIT_CODE,
                                 FK_VILLAGE_ID
                               FROM t_ps_record_history_stat
                               GROUP BY C_SPEC_CODE,C_UNIT_CODE,FK_VILLAGE_ID
                               ORDER BY C_UNIT_CODE,FK_VILLAGE_ID) AS t
                      ON C_CODE = C_SPEC_CODE
                  WHERE FK_LISTTYPE = (SELECT
                                         PK_LISTTYPE
                                       FROM t_cores_listtype
                                       WHERE C_CODE =  '" . _CONST_LINH_VUC_TTHC . "')
                      AND C_STATUS = 1
                      AND C_UNIT_CODE IS NOT NULL  ";
        return $this->db->GetAll($sql);
    }
}
