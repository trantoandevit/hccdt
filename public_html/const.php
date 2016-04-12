<?php

if (!defined('SERVER_ROOT'))
    exit('No direct script access allowed');
//Tên đơn vị hiển thị tại page erors 404.php
define('_CONST_UNIT_NAME_ERRORS', 'Dịch vụ công Bắc Giang');

//Mặc định số bản ghi hiển thị trên một trang
define('_CONST_DEFAULT_ROWS_PER_PAGE', 10);
define('_CONST_DEFAULT_ROWS_OTHER_NEWS', 10);
define('_CONST_USE_HTML_CACHE', 1);
define('_CONST_LIST_DELIM', ',');
define('_CONST_DEFAULT_ROWS_FIELD', 10);
define('_CONST_DEFAULT_ROWS_CQ', 10);

//so luong tin lien quan hien thi trong trang chu voi cac chuyen muc cua trang tet
define('COUNTS_LIST_CATEGORY_SITCK_TET',3);
//So comment con duoc hien thi
define('_CONTS_NUMBER_SHOW_COMMENT_CHILD',2);
//limit video themes mobile
define('_CONTS_LIMIT_VIDEO',2);
//limit last comment
define('_CONST_LIMIT_LAST_COMMENT',7);
//limit most visited
define('_CONST_DEFAULT_LIMIT_MOST_VISITED', 8);
//limit article new
define('_CONST_DEFAULT_LIMIT_ARTICLE_NEW', 10);
//limit photo gallery home page
define('_CONST_DEFAULT_LIMIT_PHOTO_GALLERY', 1);
//limit breaking news
define('_CONST_DEFAULT_BREAKING_NEWS', 3);
//so luong tin lien quan hien thi trong trang chu voi cac chuyen muc cua trang mobile
define('COUNTS_LIST_CATEGORY_SITCK_MOBILE',2);
//Các nhóm người dùng mặc định trong he thong
define('_CONST_ADMINISTRATOR_USER_GROUP', 'ADMINISTRATORS'); //Quan tri he thong
define('_CONST_REPORTER_USER_GROUP', 'REPORTERS'); //Phong vien
define('_CONST_EDITOR_USER_GROUP', 'EDITORS'); //Bien tap vien
define('_CONST_EDITOR_IN_CHIEF_USER_GROUP', 'EDITOR_IN_CHIEFS'); //Tong bien tap
//config he thong
define('CFGKEY_LIMIT_DISPLAY_STAFF_ON_HOME_PAGE', 'limit_display_staff_on_home_page'); // So can bo danh gia tot duoc hien thi tren trang chu

define('OPT_SYSCFG', 'SYSTEM_CONFIG'); //system config database key trong T_PS_OPTION
define('CFGKEY_CACHE', 'cache_mode');

define('CFGKEY_MAIL_ADD', 'email_address');
define('CFGKEY_MAIL_SERVER', 'email_server');
define('CFGKEY_MAIL_PORT', 'email_server_port');
define('CFGKEY_MAIL_SSL', 'email_server_ssl');
define('CFGKEY_MAIL_ACCOUNT', 'email_account');
define('CFGKEY_MAIL_PASSWORD', 'email_password');


define('CFGKEY_NEW_ARTICLE_ICON', 'new_article_icon');
define('CFGKEY_NEW_ARTICLE_COND', 'new_article_cond');

define('CFGKEY_UNIT_NAME', 'unit_name');
define('CFGKEY_UNIT_ADD', 'unit_address');
define('CFGKEY_UNIT_PHONE', 'unit_phone');
define('CFGKEY_UNIT_FAX', 'unit_fax');
define('CFGKEY_UNIT_COPYRIGHT', 'unit_copyright');
define('CFGKEY_UNIT_EMAIL', 'unit_email');
define('CFGKEY_GUIDACNE_UNIT_NAME', 'guidance_unit_name');
define('CFGKEY_UNIT_WEBSITE', 'unit_website');

//tin khac trong chuyen muc (bao haiduong)
define('_CONST_ARCHIVE_ARTICLE_PER_CATEGORY', 10);
//Che do xem tin
define('CFGKEY_HOMEPAGE_ARTICLE_PER_CATEGORY', 'homepage_article_per_category');
define('CFGKEY_ARCHIVE_ARTICLE_PER_CATEGORY', 'archive_article_per_category');

//dành cho media
$media_categories = array(
    'image'       => 'bmp, gif, jpg, png, psd, pspimage, thm, tif, yuv, swf',
    'text'        => 'doc, docx, log, msg, pages ,rtf, txt, wpd, wps',
    'spreadsheet' => 'xlr, xls, xlsx',
    'data'        => 'csv, dat, efx, gbr, key, pps, ppt, pptx, sdf, tax2010, vcf, xml',
    'audio'       => 'aif, iff, m3u, m4a, mid, mp3, mpa, ra, wav, wma',
    'video'       => '3g2, 3gp, asf, asx, avi, flv, mov, mp4, mpg, rm, swf, vob, wmv, mp3',
    'compressed'  => '7z, deb, gz, pkg, rar, rpm, sit, sitx, tar.gz, zip, zipx'
);

foreach ($media_categories as $categoryname => $categorydata)
{
    $categoryname = str_replace(' ', '', strtoupper($categoryname));
    define("EXT_$categoryname", $categorydata);
}
define('EXT_ALL', implode(',', $media_categories));
define('_CONST_IMAGE_FILE_EXT', preg_replace('/\s+/', '', 'jpg, png, gif, bmp,swf'));
define('_CONST_WELLKNOWN_FILE_EXT', preg_replace('/\s+/', '', ' accdb,  avi, csv, doc, docx, mp3, mp4, mpeg, pdf, pps, rar, zip, swf, txt, flv'));
define('_CONST_UPLOAD_FILE_EXT', preg_replace('/\s+/', '', 'jpg, png, gif, bmp,swf, accdb,  avi, csv, doc, docx, mp3, mp4, mpeg, pdf, pps, rar, zip, swf, txt, xls'));


//Const ma list type
define('_CONST_WEBLINK_GROUP','DM_NHOM_LIEN_KET_WEB');

define('_CONST_CAN_BO_DANH_GIA','DM_CAN_BO_DANH_GIA');
define('_CONST_HUONG_DAN_NOP_HS','DM_ND_HD_NOP_HS');
define('_CONST_DM_TIEU_CHI_DANH_GIA','DM_TIEU_CHI_DANH_GIA');
define('_CONST_LINH_VUC_TTHC','DANH_MUC_LINH_VUC');
//Danh muc huong dan danh gia can bo
define('_CONST_DM_HUONG_DAN_DANH_GIA_CAN_BO', 'DM_HUONG_DAN_DANH_GIA_CAN_BO');

define('_CONST_MIN_ROW_TO_MYSQL_USE_INDEX', 500);

define('_CONST_TYPE_FILE_ACCEPT', 'doc,pdf');


$file_upload_template_file_type = SERVER_ROOT . 'upload' . DS . 'hdtthc' . DS;
define('CONST_TYPE_FILE_UPLOAD',$file_upload_template_file_type);



//ReCapcha
define('_CONST_RECAPCHA_PUBLIC_KEY', '6LdpjNoSAAAAAMvTFbLh2LPN4z32Dyb6YD2v8vUI');
define('_CONST_RECAPCHA_PRIVATE_KEY', '6LdpjNoSAAAAAB6kCDmrY8RmuysVHTWsr8qxSuQb');

//record file
$path = SERVER_ROOT . 'record_file/';
define('_CONST_RECORD_FILE', $path);

$path = SITE_ROOT . 'record_file/';
define('_CONST_SITE_RECORD_FILE', $path);

//mang cau hinh thiet lap hinh anh icon cho cac file dowload guidance theo dinh dang doi file
$arr_icon_file_guidance = array(
                                    'docx'   => 'icon-doc.gif',
                                    'doc'   => 'icon-doc.gif'
                                    ,'xlsx' => 'icon-xlsx.gif'
                                );
define('CONTS_ICON_FILE_GUIDANCE',  json_encode($arr_icon_file_guidance));


//duong link luu nhat ky gui mail
define('CONTS_MAIL_LOG_DIR',  SERVER_ROOT.DS.'mail_log'.DS);
//subject mac dinh
define('CONTS_MAIL_SUBJECT',  "Cổng thông tin điện tử dịch vụ công tỉnh Bắc Giang ngày");
//link server anh can bo duoc danh gia
define('CONST_DIRECT_VOTE_IMAGES', SERVER_ROOT.'upload'.DS.'avatar_staff'.DS);
//link site anh can bo duoc danh gia
define('CONST_URL_VOTE_IMAGES', SITE_ROOT.'upload/avatar_staff/');

//limit file size
define('_CONST_LIMIT_FILE_SIZE',10);
//limit feeback
define('_CONST_DEFAULT_ROWS_FEEBACK_PAGE',10); 

// do thu tuc hien thi trong mot trang doi voi huong dan thu tuc
define('_CONTS_LIMIT_GUIDANCE_LIST',10);
//So can bo co trong so cao duoc hien thi trang danh gia can bo
define('_CONST_LIMT_STAFFT_SINGLE_PAGE',10); 

//Goi han thoi gian kich hoat tai khoan cua con dan khi dang ky hoăc thay doi email
define('_CONS_LIMIT_ACCOUNT_DATE_TRIGGER',7);

//apps dir
define('CONST_APPS_DIR', SERVER_ROOT . 'apps' . DS);

#2. const.php
define('CFGKEY_UNIT_NAME_SERVICE', 'unit_name_services');