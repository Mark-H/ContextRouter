<?php
/* @var modX $modx
 * @var array $scriptProperties
 **/

$event = $modx->event->name;

switch ($event) {
    default:
    case 'OnHandleRequest':
        if ($modx->context->key == 'mgr') return;

        $routes = $modx->cacheManager->get('contextrouter', array());

        if (!is_array($routes)) {
            /* @var ContextRouter $contextRouter */
            $core_path = $modx->getOption('contextrouter.core_path', null, $modx->getOption('core_path').'components/contextrouter/') . 'model/';
            $contextRouter = $modx->getService('contextrouter','ContextRouter', $core_path, $scriptProperties);
            if (!$contextRouter) { $modx->log(modX::LOG_LEVEL_ERROR,'Error instantiating ContextRouter class from ' . $core_path); return; }

            $contextRouter->buildRoutesCache();
            $routes = $contextRouter->getRoutes();
        }

        /* Do the actual routing. */
        $host = $_SERVER['HTTP_HOST'];
        if (empty($host)) return;

        if (array_key_exists($host, $routes)) {
            $modx->switchContext($routes[$host]);
        }
        break;

    case 'OnContextSave':
    case 'OnContextRemove':
    case 'OnSiteRefresh':
        /* @var ContextRouter $contextRouter */
        $core_path = $modx->getOption('contextrouter.core_path', null, $modx->getOption('core_path').'components/contextrouter/') . 'model/';
        $contextRouter = $modx->getService('contextrouter','ContextRouter', $core_path, $scriptProperties);
        if (!$contextRouter) { $modx->log(modX::LOG_LEVEL_ERROR,'Error instantiating ContextRouter class from ' . $core_path); return; }

        $contextRouter->buildRoutesCache();
        break;
}
