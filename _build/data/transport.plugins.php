<?php
$plugins = array();

/* create the plugin object */
$plugins[0] = $modx->newObject('modPlugin');
$plugins[0]->set('id',1);
$plugins[0]->set('name','ContextRouter');
$plugins[0]->set('description','ContextRouter is a simple plug-and-play plugin allowing you to use different contexts, and, based on the http_host context settings you need to set anyway, it routes your front-end requests as required.');
$plugins[0]->set('plugincode', getSnippetContent($sources['root'] . 'ContextRouter.plugin.php'));
$plugins[0]->set('category', 0);

$events = array();
$events['OnHandleRequest']= $modx->newObject('modPluginEvent');
$events['OnHandleRequest']->fromArray(array(
    'event' => 'OnHandleRequest',
    'priority' => 0,
    'propertyset' => 0,
),'',true,true);
$events['OnContextSave']= $modx->newObject('modPluginEvent');
$events['OnContextSave']->fromArray(array(
    'event' => 'OnContextSave',
    'priority' => 0,
    'propertyset' => 0,
),'',true,true);
$events['OnContextRemove']= $modx->newObject('modPluginEvent');
$events['OnContextRemove']->fromArray(array(
    'event' => 'OnContextRemove',
    'priority' => 0,
    'propertyset' => 0,
),'',true,true);

if (is_array($events) && !empty($events)) {
    $plugins[0]->addMany($events);
    $modx->log(xPDO::LOG_LEVEL_INFO,'Packaged in '.count($events).' Plugin Events for ContextRouter.'); flush();
} else {
    $modx->log(xPDO::LOG_LEVEL_ERROR,'Could not find plugin events for ContextRouter!');
}
unset($events);

return $plugins;

?>
