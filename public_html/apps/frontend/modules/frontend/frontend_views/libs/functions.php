<?php

/**
 * ham nay lay thong tin tu trang http://langsontv.vn/broadcast
 * neu trang do doi theme co the chay se loi
 * @param string $url duong dan den service
 * @return array $arr mang thong tin da xu ly
 */
function get_tv_schedule_v1($url)
{
    $cache_dir = SERVER_ROOT . 'cache' . DS;
    if (!is_dir($cache_dir))
    {
        mkdir($cache_dir);
    }
    $cache_dir .= 'widget_support' . DS;
    if (!is_dir($cache_dir))
    {
        mkdir($cache_dir);
    }
    $v_tv_schedule_file = $cache_dir . 'tv_schedule' . Date('Y_m_d_H') . '.php';

    if (file_exists($v_tv_schedule_file))
    {
        return unserialize(file_get_contents($v_tv_schedule_file));
    }

    $arr = array(); //bien nay de return
    //fake user agent
    $user_agent = 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.17 (KHTML, like Gecko) Chrome/24.0.1312.2 Safari/537.17';
    $old_agent  = ini_get('user_agent');
    ini_set('user_agent', $user_agent);
    $raw_html   = str_replace(array('<br />', '<br/>'), '', file_get_contents($url));
    $raw_html = preg_replace('#\s(style)="[^"]+"#', '', $raw_html);


    //tra ve user agent mac dinh
    ini_set('user_agent', $old_agent);

    preg_match("/<div class = \"bmprogam_page_title\">(.*)<\/div>/i", $raw_html, $matches);
//    preg_match('/<div class="views-row views-row-1 views-row-odd views-row-first">">(.*)<\/div>/i", $raw_html, $matches);
    $title = isset($matches[1]) ? $matches[1] : '';

    $count = preg_match_all("/<p class=\"rtejustify\">(.*)<\/p>/i", $raw_html, $matches);
    if ($count)
    {
        foreach ($matches[1] as $item)
        {
            $item  = explode(':', trim(preg_replace('/( +)/', ' ', strip_tags($item)), ' '));
            $count = preg_match("/([0-9]h.*)/i", $item[0]);
            if (isset($item[1]) && $count > 0)
            {
                $item[1] = str_replace('&nbsp;', '', trim($item[1]));
                $item[1] = preg_replace('/( ?)\/( ?)/', '/', $item[1]);
                $item[2] = $title;
                if (isset($arr[$item[0]]) == false)
                {
                    $arr[$item[0]] = $item;
                }
            }
        }
    }
    if (count($arr) == 0)
    {
        $count = preg_match_all("/<p class=\"MsoNormal\">(.*)<\/p>/i", $raw_html, $matches);
        if ($count)
        {
            foreach ($matches[1] as $item)
            {
                $item  = explode(':', trim(preg_replace('/( +)/', ' ', strip_tags($item)), ' '));
                $count = preg_match("/([0-9]h.*)/i", $item[0]);
                if (isset($item[1]) && $count > 0)
                {
                    $item[1] = str_replace('&nbsp;', '', trim($item[1]));
                    $item[1] = preg_replace('/( ?)\/( ?)/', '/', $item[1]);
                    $item[2] = $title;
                    if (isset($arr[$item[0]]) == false)
                    {
                        $arr[$item[0]] = $item;
                    }
                }
            }
        }
    }

    file_put_contents($v_tv_schedule_file, serialize($arr));
    return $arr;
}

?>
