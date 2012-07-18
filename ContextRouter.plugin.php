<?php
/* @var modX $modx
 * @var array $scriptProperties
 **/

$event = $modx->event->name;

switch ($event) {
    default:
    case 'OnHandleRequest':
        if ($modx->context->key == 'mgr') return;

        $contexts = $modx->cacheManager->get('contextrouter');

        if (!is_array($contexts)) {
            $contexts = array();

            $www = $modx->getOption('includeWww', $scriptProperties, true);

            /* Set the default and web context hosts */
            $defaultContext = $modx->getOption('defaultContext', $scriptProperties, 'web');
            if (!empty($defaultContext)) {
                $contexts['default'] = $defaultContext;
            }
            $host = $modx->getOption('HTTP_HOST');
            if (!empty($host)) {
                if ($www) {
                    if (substr($host, 0, 4) == 'www.') $host = substr($host, 4);
                    $contexts['www.' . $host] = 'web';
                }
                $contexts[$host] = 'web';
            }

            /* Get all contexts and their http_host settings */
            $ctxs = $modx->getCollection('modContext',array(
                'key:!=' => 'mgr'
            ));
            /* @var modContext $ctx */
            foreach ($ctxs as $ctx) {
                $key = $ctx->get('key');

                /* @var modContextSetting $ctxSetting */
                $ctxSetting = $modx->getObject('modContextSetting',array('context_key' => $key, 'key' => 'http_host'));
                if ($ctxSetting instanceof modContextSetting && ($ctxSetting->get('value') != '')) {
                    $host = $ctxSetting->get('value');
                    $key = $ctx->get('key');
                    if ($www) {
                        if (substr($host, 0, 4) == 'www.') $host = substr($host, 4);
                        $contexts['www.' . $host] = $key;
                    }
                    $contexts[$host] = $key;
                }

                /* get http_host aliases */
                $ctxSetting = $modx->getObject('modContextSetting',array('context_key' => $key, 'key' => 'http_host_aliases'));
                if ($ctxSetting instanceof modContextSetting && ($ctxSetting->get('value') != '')) {
                    $hosts = explode(',', $ctxSetting->get('value'));
                    $key = $ctx->get('key');
                    foreach ($hosts as $host) {
                        if ($www) {
                            if (substr($host, 0, 4) == 'www.') $host = substr($host, 4);
                            $contexts['www.' . $host] = $key;
                        }
                        $contexts[$host] = $key;
                    }
                }
            }
            $modx->cacheManager->set('contextrouter',$contexts);
        }


        /* Hardcore routing action. These 4 lines are why you use this plugin. */
        $host = $_SERVER['HTTP_HOST'];
        if (empty($host)) break;

        if (array_key_exists($host, $contexts)) {
            $modx->switchContext($contexts[$host]);
        }
        break;

    case 'OnContextSave':
    case 'OnContextRemove':
    case 'OnSiteRefresh':
        $modx->cacheManager->delete('contextrouter');
        break;
}
