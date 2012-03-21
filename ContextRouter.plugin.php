<?php
/* @var modX $modx
 **/

$event = $modx->event->name;

switch ($event) {
    default:
    case 'OnHandleRequest':
        if ($modx->context->key == 'mgr') return;

        $host = $_SERVER['HTTP_HOST'];
        if (empty($host)) return;

        $contexts = $modx->cacheManager->get('contextrouter');

        if (!is_array($contexts)) {
            $contexts = array();
            $ctxs = $modx->getCollection('modContext',array(
                'key:!=' => 'mgr'
            ));

            /* @var modContext $ctx */
            foreach ($ctxs as $ctx) {
                $key = $ctx->get('key');

                /* @var modContextSetting $ctxSetting */
                $ctxSetting = $modx->getObject('modContextSetting',array('context_key' => $key, 'key' => 'http_host'));
                if ($ctxSetting instanceof modContextSetting) {
                    $contexts[$ctxSetting->get('value')] = $ctx->get('key');
                }
            }
            $modx->cacheManager->set('contextrouter',$contexts);
        }

        if (array_key_exists($host, $contexts)) {
            $modx->switchContext($contexts[$host]);
        }
        break;

    case 'OnContextSave':
    case 'OnContextRemove':
        $modx->cacheManager->delete('contextrouter');
        break;
}
