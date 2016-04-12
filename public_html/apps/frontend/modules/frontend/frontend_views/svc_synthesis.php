<?php

$json = array();

foreach ($arr_synthesis as $arr_value)
{
    $v_member_name = $arr_value['C_NAME'];

    $v_tong_tiep_nhan_thang = $arr_value['C_COUNT_TONG_TIEP_NHAN_TRONG_THANG'];

    $v_dang_thu_ly = $arr_value['C_COUNT_DANG_THU_LY'];
    $v_dang_cho_tra_ket_qua = $arr_value['C_COUNT_DANG_CHO_TRA_KET_QUA'];
    $v_dang_thu_ly_dung_tien_do = $arr_value['C_COUNT_DANG_THU_LY_DUNG_TIEN_DO'];
    $v_dang_thu_ly_cham_tien_do = $arr_value['C_COUNT_DANG_THU_LY_CHAM_TIEN_DO'];
    $v_thu_ly_qua_han = $arr_value['C_COUNT_THU_LY_QUA_HAN'];
    $v_thue = $arr_value['C_COUNT_THUE'];

    $v_da_tra_ket_qua_truoc_han = $arr_value['C_COUNT_DA_TRA_KET_QUA_TRUOC_HAN'];
    $v_da_tra_ket_qua_dung_han = $arr_value['C_COUNT_DA_TRA_KET_QUA_DUNG_HAN'];
    $v_da_tra_ket_qua_qua_han = $arr_value['C_COUNT_DA_TRA_KET_QUA_QUA_HAN'];
    $v_da_tra_ket_qua = $arr_value['C_COUNT_DA_TRA_KET_QUA'];

    $v_cong_dan_rut = $arr_value['C_COUNT_CONG_DAN_RUT'];
    $v_tu_choi = $arr_value['C_COUNT_TU_CHOI'];
    $v_bo_sung = $arr_value['C_COUNT_BO_SUNG'];

    $v_tong_da_tra = $v_da_tra_ket_qua_truoc_han + $v_da_tra_ket_qua_dung_han + $v_da_tra_ket_qua_qua_han;
    $v_tong_dang_giai_quyet = $v_dang_thu_ly + $v_bo_sung;
    $v_ky_truoc = ($v_tong_dang_giai_quyet + $v_tong_da_tra + $v_tu_choi + $v_cong_dan_rut + $v_dang_cho_tra_ket_qua) - $v_tong_tiep_nhan_thang;
    //tinh toan ty le
    $v_ky_truoc = ($v_ky_truoc > 0 ) ? $v_ky_truoc : 0;
    $v_ty_le = 0;
    if ($v_tong_da_tra > 0)
    {
        $v_ty_le = (($v_da_tra_ket_qua_truoc_han + $v_da_tra_ket_qua_dung_han) / $v_tong_da_tra) * 100;
    }

    $v_ty_le = $v_ty_le ? number_format($v_ty_le, 2, '.', ',') . '%' : '-';


    $json_node = array(
        'id'              => $arr_value['C_UNIT_CODE'],
        'name'            => $arr_value['C_NAME'],
        'tiep_nhan'       => array(
            'tong_so'   => $v_tong_tiep_nhan_thang + $v_ky_truoc,
            'ky_truoc'  => $v_ky_truoc,
            'tiep_nhan' => $v_tong_tiep_nhan_thang
        ),
        'dang_giai_quyet' => array(
            'tong_so'      => $v_dang_thu_ly + $v_bo_sung,
            'chua_den_han' => $v_dang_thu_ly - $v_thu_ly_qua_han,
            'qua_han'      => $v_thu_ly_qua_han,
            'bo_sung'      => $v_bo_sung
        ),
        'tra_ket_qua'     => array(
            'tong_so'               => $v_tong_da_tra,
            'som_han'               => $v_da_tra_ket_qua_truoc_han,
            'dung_han'              => $v_da_tra_ket_qua_dung_han,
            'qua_han'               => $v_da_tra_ket_qua_qua_han,
            'cho_tra'               => $v_dang_cho_tra_ket_qua,
            'tu_choi'               => $v_dang_thu_ly + $v_bo_sung,
            'cong_dan_rut'          => $v_cong_dan_rut,
            'ti_le_som_va_dung_han' => $v_ty_le
        ),
        'children'        => array()
    );

    if (!isset($arr_value['C_CODE']))
    {
        $json[] = $json_node;
    }
    else
    {
        $json_node['id'] = $arr_value['C_CODE'];
        foreach ($json as &$node)
        {
            if ($node['id'] == $arr_value['C_UNIT_CODE'])
            {
                $node['children'][] = $json_node;
            }
        }
    }
}
//nếu value=0 thay = dấu -
array_walk_recursive($json, function(&$val)
{
    if (!$val)
    {
        $val = '-';
    }
});

//jsonp
//echo $_GET['callback'] . '(' . json_encode($json) . ')';
echo json_encode(array(
    'date'   => date_create($max_dateime_update_record_history_start)->format('d/m/Y'),
    'report' => $json
));


