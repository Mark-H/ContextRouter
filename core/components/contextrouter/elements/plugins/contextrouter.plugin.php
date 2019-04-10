<?php
/* @var modX $modx
 * @var array $scriptProperties
 **/

$event = $modx->event->name;

switch ($event) {
    case 'OnContextSave':
    case 'OnContextRemove':
    case 'OnSiteRefresh':
        /* @var ContextRouter $contextRouter */
        $core_path = $modx->getOption('contextrouter.core_path', null, $modx->getOption('core_path').'components/contextrouter/') . 'model/';
        $contextRouter =& $modx->getService('contextrouter','ContextRouter', $core_path, $scriptProperties);
        if (!$contextRouter) {
            $modx->log(modX::LOG_LEVEL_ERROR,'Error instantiating ContextRouter class from ' . $core_path);
            return;
        }

        $contextRouter->buildRoutesCache();
        break;

    case 'OnHandleRequest':
    case 'OnMODXInit':
    default:
        if ($modx->context->key == 'mgr') return;

        $routes = $modx->cacheManager->get('contextrouter', array());

        if (!is_array($routes)) {
            /* @var ContextRouter $contextRouter */
            $core_path = $modx->getOption('contextrouter.core_path', null, $modx->getOption('core_path').'components/contextrouter/') . 'model/';
            $contextRouter =& $modx->getService('contextrouter','ContextRouter', $core_path, $scriptProperties);
            if (!$contextRouter) {
                $modx->log(modX::LOG_LEVEL_ERROR,'Error instantiating ContextRouter class from ' . $core_path);
                return;
            }

            $contextRouter->buildRoutesCache();
            $routes = $contextRouter->getRoutes();
        }

        /* Do the actual routing. */
        $host = $_SERVER['HTTP_HOST'];
        if (empty($host)) return;

        if (array_key_exists($host, $routes)) {
            $modx->switchContext($routes[$host]);
            
    		if((boolean) $modx->getOption('contextrouter.redirectAliasToHttpHost',null,false)){
			    $http_host = $modx->getOption('http_host',null,$host);
				$responseCode = $modx->getOption('contextrouter.redirectResponseHeader',null,'HTTP/1.1 301 Moved Permanently');
				if($host !== $http_host){
				    $options = array(
					    'responseCode' => $responseCode
					);
				    $modx->sendRedirect('http://' . $http_host . $_SERVER['REQUEST_URI'],$options);
				}
			}
            
        }
        break;

}