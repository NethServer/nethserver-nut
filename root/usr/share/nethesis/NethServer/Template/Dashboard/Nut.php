<?php

$status = $view['status'];
$load = intval($status['ups.load']);
$involtage = intval($status['input.voltage']);
$outvoltage = intval($status['output.voltage']);
$outvoltage = intval($status['output.voltage']);
$involtage = intval($status['input.voltage']);
$battcharg = intval($status['battery.charge']);
$battvolt = intval($status['battery.voltage']);
$runtime = intval($status['battery.runtime']/60);
$battvolt_min = 30;
$battvolt_max = 60;

$upsstatus = $status['ups.status'];
$color = "green";
if (strpos($upsstatus,"OL") !== false) {
    $upsstatus_label = "on_line_label";
}
if (strpos($upsstatus,"CHRG") !== false) {
    $upsstatus_lbael = "on_line_chrg_label";
}
if (strpos($upsstatus,"OB") !== false) {
    $upsstatus_label = "on_battery_label";
    $color = 'orange';
}
if (strpos($upsstatus,"LB") !== false) {
    $upsstatus_label = "low_battery_label";
    $color = 'red';
}


echo "<div class='nut-item'>";
echo "<dl>";
echo $view->header()->setAttribute('template',$T('nut_title'));
echo "<dt>".$T('model_label')."</dt><dd>"; echo $status['ups.model']; echo "</dd>";
echo "<dt>".$T('status_label')."</dt><dd><span style='color:$color; font-weight: bold'>"; echo $T($upsstatus_label); echo "</span></dd>";
echo "<dt>".$T('load_label')."</dt><dd>"; echo $load; echo "/100<div id='dashboard_nut_load'></div>"; echo "</dd>";
echo "<dt>".$T('battery_chrg_label')."</dt><dd>"; echo $battcharg; echo "/100<div id='dashboard_battery_chrg'></div>"; echo "</dd>";
echo "<dt>".$T('battery_runtime_label')."</dt><dd>"; echo $runtime." ";  echo $T('minutes_label'); echo "</dd>";
echo "<dt style='margin-top: 5px'>".$T('battery_volt_label')."</dt><dd style='margin-top: 5px'>"; echo $battvolt; echo "</dd>";
echo "<dt>".$T('temperature_label')."</dt><dd>"; echo intval($status['ups.temperature']); echo "</dd>";
echo "<dt>".$T('input_volt_label')."</dt><dd>"; echo $involtage; echo "</dd>";
echo "<dt>".$T('output_volt_label')."</dt><dd>"; echo $outvoltage; echo "</dd>";
echo "</dl>";
echo "</div>";
echo "<div class='nut-item'>";
echo "<a href='#' id='dashboard_show_full_status'>".$T('show_full_status_label')."</a>";
echo "<pre id='dashboard_nut_full_status'>";
foreach($status as $key=>$prop) {
    echo "$key : $prop\n";
}
echo "</pre>";
echo "</div>";

$view->includeJavascript("
(function ( $ ) {
    
    $('#dashboard_nut_full_status').hide();
    
    $('#dashboard_show_full_status').click(function(e) {
        e.preventDefault();
        $('#dashboard_nut_full_status').toggle();
    });

    $('#dashboard_battery_chrg').progressbar({
        value: $battcharg
    });

    color = 'orange';
    if ($battcharg < 50) {
        color = 'red';
    } else if ($battcharg > 80) {
        color = 'green';
    }
    $('#dashboard_battery_chrg').find('.ui-progressbar-value').css({ 'background': color });

    $('#dashboard_nut_load').progressbar({
        value: $load
    });
    color = 'orange';
    if ($load <= 50) {
        color = 'green';
    } else if ($load > 80) {
        color = 'red';
    }
    $('#dashboard_nut_load').find('.ui-progressbar-value').css({ 'background': color });

} ( jQuery ));
");   

$view->includeCSS("
  div.nut-item {
    margin: 5px;
    padding: 5px;
    border: 1px solid #ccc;
  }
  .nut-item dt {
    float: left;
    clear: left;
    text-align: right;
    font-weight: bold;
    margin-right: 0.5em;
    padding: 0.1em;
  }
  .nut-item dt:after {
    content: \":\";
  }
  .nut-item dd {
    padding: 0.1em;
  }
  .nut-item h2 {
    font-weight: bold;
    font-size: 120%;
    text-align: center;
    padding: 0.2em;
  }
  .nut-item .ui-progressbar {
      width: 200px;
      height: 20px;
  }
  .nut-item pre {
      margin-top: 2px;
      padding: 2px;
  }
");

