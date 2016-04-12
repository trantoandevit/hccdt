<?php
defined('DS') or die('no direct access');
error_reporting(E_ALL);
$v_index                   = (int) Session::get('count_widget_support');
$v_index++;
Session::set('count_widget_support', $v_index);
$data['xml_exchange_rate'] = '';
//kiem tra cache dir
$cache_dir                 = SERVER_ROOT . 'cache' . DS;
if (!is_dir($cache_dir))
{
    mkdir($cache_dir);
}
$cache_dir .= 'widget_support' . DS;
if (!is_dir($cache_dir))
{
    mkdir($cache_dir);
}
try
{
    $v_today_exchange_file = $cache_dir . 'rate_exchange_' . Date('Y_m_d_H') . '.xml';
   
    if (!file_exists($v_today_exchange_file))
    {
        $xml_exchange = file_get_contents(EXCHANGE_RATE_SVC_URL);
        file_put_contents($v_today_exchange_file, $xml_exchange);
    }

    $xml_exchange            = file_get_contents($v_today_exchange_file);
    $xml_exchange            = new SimpleXMLElement($xml_exchange, LIBXML_NOCDATA);
    $data['xml_exchange']    = $xml_exchange;
    $v_exchange_file_date    = $xml_exchange->xpath('//DateTime');
    $v_exchange_file_date    = (string) $v_exchange_file_date[0];
    $v_exchange_rate_svc_src = $xml_exchange->xpath('//Source');
    $v_exchange_rate_svc_src = (string) $v_exchange_rate_svc_src[0];
    $arr_all_currency        = $xml_exchange->xpath('//Exrate');
}
catch (Exception $ex)
{
    
}

try
{
    $v_today_exchange_file = $cache_dir . 'gold_' . Date('Y_m_d_H') . '.xml';

    if (!file_exists($v_today_exchange_file))
    {
        $xml_exchange = file_get_contents(GOLD_PRICE_SVC_URL);
        file_put_contents($v_today_exchange_file, $xml_exchange);
    }

    $xml_gold           = file_get_contents($v_today_exchange_file);
    $xml_gold           = new SimpleXMLElement($xml_gold, LIBXML_NOCDATA);
    $v_gold_svc_src     = $xml_gold->xpath('//url');
    $v_gold_svc_src     = (string) $v_gold_svc_src[0];
    $xml_gold_meta_data = $xml_gold->xpath('//ratelist');
    $v_gold_svc_date    = $xml_gold_meta_data[0]->attributes()->updated;
    $v_gold_svc_unit    = $xml_gold_meta_data[0]->attributes()->unit;
    $arr_gold_city      = $xml_gold->xpath('//city[@name="Hà Nội"]');
}
catch (Exception $ex)
{
    
}

try
{
    $v_today_exchange_file = $cache_dir . 'weather_' . Date('Ymd') . '.xml';

    if (!file_exists($v_today_exchange_file))
    {
        $xml_exchange = file_get_contents(WEATHER_SVC_URL);
        file_put_contents($v_today_exchange_file, $xml_exchange);
    }

    $xml_weather       = file_get_contents($v_today_exchange_file);
    $xml_weather       = new SimpleXMLElement($xml_weather, LIBXML_NOCDATA);
    $xml_weather       = $xml_weather->channel;
    $v_weather_src     = (string) $xml_weather->title;
    $v_weather_desc    = (string) $xml_weather->item->description;
    $find_img          = '<img(.*)/>';
    preg_match($find_img, $v_weather_desc, $matches);
    $v_weather_img_tag = '';
    if (count($matches))
    {
        $v_weather_img_tag      = '<' . $matches[0];
    }
    //lay thong tin
    $xml_weather_today      = $xml_weather->xpath('//item/yweather:forecast');
    $v_weather_date         = date_create_from_format('d M Y', (string) $xml_weather_today[0]->attributes()->date)->format('d/m/Y');
    $v_weather_current_cond = $xml_weather->xpath('//yweather:condition');
    $v_weather_current_cond = $v_weather_current_cond[0]->attributes()->temp;
    $xml_weather_forecast   = $xml_weather->xpath('//yweather:forecast');
    $v_weather_low          = (string) $xml_weather_forecast[0]->attributes()->low;
    $v_weather_high         = (string) $xml_weather_forecast[0]->attributes()->high;
}
catch (Exception $ex)
{
    
}

//lay lich chieu film
//require_once(SERVER_ROOT . 'apps/frontend/modules/frontend/frontend_views/libs/functions.php');
//$arr_tv_schedule = get_tv_schedule_v1(TV_SCHEDULE_SVC_URL);
?>

<div class="widget widget-support <?php echo $widget_style ?>" data-code="support">
    <div class="widget-header">
        <h6><?php echo __('support info') ?></h6>
    </div>
    <div class="widget-content" style="font-size:10px;">
        <div class="theme-tabs widget-support-tabs" id="widget-support-tabs-<?php echo $v_index ?>">
            <ul class="">
                <li class="">
                    <a href="#support-weather-<?php echo $v_index ?>">
                        <?php echo __('weather') ?>
                    </a>
                </li>
                <li class="">
                    <a href="#support-gold-price-<?php echo $v_index ?>">
                        <?php echo __('gold price') ?>
                    </a>
                </li>
                <li class="">
                    <a href="#support-exchange-rate-<?php echo $v_index ?>">
                        <?php echo __('exchange rate') ?>
                    </a>
                </li>
<!--                <li class="">
                    <a href="#support-tv-schedule-<?php echo $v_index ?>">
                        <?php echo __('TV schedule') ?>
                    </a>
                </li>-->
            </ul>
            <div id="support-weather-<?php echo $v_index ?>">
                <?php if (count($xml_weather) == 0): ?>
                    <?php echo __('service unavailable') ?>
                <?php else: ?>
                    <table>
                        <colgroup>
                            <col width="20%">
                            <col width="20%">
                            <col width="60%">
                        </colgroup>
                        <tr>
                            <td rowspan="2"></td>
                            <td rowspan="2"><?php echo $v_weather_img_tag ?></td>
                            <th> <h2 class=""><?php echo $v_weather_current_cond ?>°C</h2></th>
                        </tr>
                        <tr>
                            <td><h3>(<?php echo $v_weather_low ?>°C-<?php echo $v_weather_high ?>°C)</h3></td>
                        </tr>
                    </table>
                    <p class="widget-comment">
                        (<?php echo __('source') . ': ' . WEATHER_SVC_SRC ?>)
                    </p>
                <?php endif; ?>
            </div>
            <div id="support-gold-price-<?php echo $v_index ?>">
                <?php $n = count($arr_gold_city); ?>
                <?php if ($n == 0): ?>
                    <?php echo __('service unavailable') ?>
                <?php else: ?>
                    <table width="100%">
                        <colgroup>
                            <col width="43.3%">
                            <col width="33.3%">
                            <col width="23.3%">
                        </colgroup>
                        <?php for ($i = 0; $i < $n; $i++): ?>
                            <?php
                            $xml_city      = $arr_gold_city[$i];
                            $arr_gold_item = $xml_city->xpath('//item');
                            $m             = count($arr_gold_item);
                            ?>
                            <tr>
                                <th colspan="3"><?php echo $xml_city->attributes()->name ?></th>
                            </tr>
                            <tr>
                                <th><?php echo __('type') ?></th>
                                <th><?php echo __('buy') ?></th>
                                <th><?php echo __('sell') ?></th>
                            </tr>
                            <?php for ($j = 0; $j < $m; $j++): ?>
                                <?php $item = $arr_gold_item[$j]; ?>
                                <tr>
                                    <td class="td-type"><?php echo $item->attributes()->type; ?></td>
                                    <td><?php echo $item->attributes()->buy; ?></td>
                                    <td><?php echo $item->attributes()->sell; ?></td>
                                </tr>
                            <?php endfor; ?>
                        <?php endfor; ?>
                    </table>
                    <p class="widget-comment">
                        (<?php echo __('unit') . ': ' . 'vnd' ?>, <?php echo __('source') . ': ' . GOLD_PRICE_SVC_SRC ?>)
                    </p>
                <?php endif; ?>
            </div>
            <div id="support-exchange-rate-<?php echo $v_index ?>">
                <?php $n    = count($arr_all_currency); ?>
                <?php if ($n == 0): ?>
                    <?php echo __('service unavailable') ?>
                <?php else: ?>
                    <table  style="width: 100%;">
                        <colgroup>
                            <col width="25%">
                            <col width="25%">
                            <col width="25%">
                            <col width="25%">
                        </colgroup>
                        <tr>
                            <th><?php echo __('code') ?></th>
                            <th><?php echo __('buy') ?></th>
                            <th><?php echo __('transfer') ?></th>
                            <th><?php echo __('sell') ?></th>
                        </tr>

                        <?php for ($i = 0; $i < $n; $i++): ?>
                            <?php $item  = $arr_all_currency[$i]; ?>
                            <tr>
                                <td class="td-type"><?php echo $item->attributes()->CurrencyCode; ?></td>
                                <td><?php echo $item->attributes()->Buy; ?></td>
                                <td><?php echo $item->attributes()->Transfer; ?></td>
                                <td><?php echo $item->attributes()->Sell; ?></td>
                            </tr>
                        <?php endfor; ?>
                    </table>
                    <p class="widget-comment">
                        (<?php echo __('unit') . ': ' . 'vnd' ?>, <?php echo __('source') . ': ' . EXCHANGE_RATE_SVC_SRC ?>)
                    </p>
                <?php endif; ?>
            </div>
            <?php /*
            <div class="support-tv-schedule" id="support-tv-schedule-<?php echo $v_index ?>">
                <div>
                    <table  style="width: 100%;">
                        <colgroup>
                            <col width="20%">
                            <col width="80%">
                        </colgroup>
                        <tr>
                            <?php 
                            reset($arr_tv_schedule);
                            $first_item = current($arr_tv_schedule);
                            $title = $first_item[2];
                            ?>
                            <th colspan="2" class="support-title"><?php echo($title) ?></th>
                        </tr>
                        <tr>
                            <th><?php echo __('time') ?></th>
                            <th><?php echo __('show') ?></th>
                        </tr>
                        <?php foreach ($arr_tv_schedule as $item): ?>
                            <tr>
                                <td class="tv-schedule-time">
                                    <?php echo $item[0] ?>
                                </td>
                                <td>
                                    <?php echo $item[1] ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                    <p class="widget-comment">
                        (<?php echo __('source') . ': ' . TV_SCHEDULE_SVC_URL ?>)
                    </p>
                </div>
            </div>
            */?>
        </div>
    </div>
</div>
<script>
    $('#widget-support-tabs-<?php echo $v_index ?>').tabs();
</script>