<?php
$s = array(
    'includeWww' => true,
    'defaultContext' => 'web',
);

$settings = array();

foreach ($s as $key => $value) {
    if (is_string($value) || is_int($value)) { $type = 'textfield'; }
    elseif (is_bool($value)) { $type = 'combo-boolean'; }
    else { $type = 'textfield'; }

    $area = 'default';

    $settings['contextrouter.'.$key] = $modx->newObject('modSystemSetting');
    $settings['contextrouter.'.$key]->set('key', 'contextrouter.'.$key);
    $settings['contextrouter.'.$key]->set('namespace', 'contextrouter');
    $settings['contextrouter.'.$key]->set('value', $value);
    $settings['contextrouter.'.$key]->set('xtype', $type);
    $settings['contextrouter.'.$key]->set('area', $area);
}

return $settings;
