<?php
$v_woeid = $txt_weather_woeid;
$v_title = $txt_weather_title;
$v_url_weather = "http://weather.yahooapis.com/forecastrss?w=$v_woeid&u=c";
?>
<div class="widget-weather widget widget_blue">
    <div class="widget-header"><h6><?php echo $v_title;?></h6></div>
    <?php 
        $xml_weather = file_get_contents($v_url_weather);

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
        if(count($xml_weather_today)<1)
        {
            echo __('service unavailable');

        }
        else
        {
            $v_weather_date         = date_create_from_format('d M Y', (string) $xml_weather_today[0]->attributes()->date)->format('d/m/Y');
            $v_weather_current_cond = $xml_weather->xpath('//yweather:condition');
            $v_weather_current_cond = $v_weather_current_cond[0]->attributes()->temp;
            $xml_weather_forecast   = $xml_weather->xpath('//yweather:forecast');
            $v_weather_low          = (string) $xml_weather_forecast[0]->attributes()->low;
            $v_weather_high         = (string) $xml_weather_forecast[0]->attributes()->high;
        ?>
        <div class="widget-weather-content">
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
        </div>
    <?php }//endif?>
</div>