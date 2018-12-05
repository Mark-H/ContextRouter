<?php

class ContextRouter {
    public $routes = array();
    public $cacheKey = 'contextrouter';
    public $cacheOptions = array();
    public $config = array();

    /**
     * @param \modX $modx
     * @param array $config
     */
    function __construct(modX &$modx,array $config = array()) {
        $this->modx =& $modx;
        $this->includeWww = $modx->getOption('contextrouter.includeWww', null, true);
        $this->config = $config;
    }

    public function getRoutes () {
        $this->routes = $this->modx->cacheManager->get($this->cacheKey, $this->cacheOptions);
        if (!is_array($this->routes) || empty($this->routes)) {
            $this->routes = array();
            $this->buildRoutesCache();
        }
        return $this->routes;
    }

    public function buildRoutesCache() {
        $defaultContext = $this->modx->getOption('contextrouter.defaultContext', null, 'web');
        if (!empty($defaultContext)) {
            $this->addToRoutesArray('default', $defaultContext, true);
        }

        $global = $this->modx->getOption('http_host');
        if (!empty($global)) {
            $this->addToRoutesArray($global, 'web');
        }

        $c = $this->modx->newQuery('modContext');
        $c->select($this->modx->getSelectColumns('modContext', 'modContext', '', ['key']));
        $c->where(array(
            'key:!=' => 'mgr'
        ));
        $c->prepare();

        $contexts = array();
        $stmt = $this->modx->query($c->toSQL());
        if ($stmt) {
            $contexts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        foreach ($contexts as $ctx) {
            $key = $ctx['key'];

            /* @var modContextSetting $ctxSetting */
            $ctxSetting = $this->modx->getObject('modContextSetting',array('context_key' => $key, 'key' => 'http_host'));
            if ($ctxSetting instanceof modContextSetting && ($ctxSetting->get('value') != '')) {
                $host = $ctxSetting->get('value');
                $this->addToRoutesArray($host, $key);
            }

            /* get http_host aliases */
            $ctxSetting = $this->modx->getObject('modContextSetting',array('context_key' => $key, 'key' => 'http_host_aliases'));
            if ($ctxSetting instanceof modContextSetting && ($ctxSetting->get('value') != '')) {
                $hosts = explode(',', $ctxSetting->get('value'));
                foreach ($hosts as $host) {
                    $this->addToRoutesArray($host, $key);
                }
            }
        }

        $this->modx->cacheManager->set($this->cacheKey, $this->routes, 0, $this->cacheOptions);
    }

    private function addToRoutesArray($host, $ctx, $skipWww = false) {
        if (!empty($host)) {
            if ($this->includeWww && !$skipWww) {
                if (substr($host, 0, 4) == 'www.') $host = substr($host, 4);
                if (isset($this->routes['www.' . $host]) && ($this->routes['www.' . $host] != $ctx)) {
                    $this->modx->log(modX::LOG_LEVEL_ERROR,'[ContextRouter] You may have conflicting http_host definitions for the www subdomain. Overwriting context '. $this->routes['www.' . $host] . ' with ' . $ctx . ' for host www.' . $host);
                }
                $this->routes['www.' . $host] = $ctx;
            }
            if (isset($this->routes[$host])  && ($this->routes['www.' . $host] != $ctx)) {
                $this->modx->log(modX::LOG_LEVEL_ERROR,'[ContextRouter] You may have conflicting http_host definitions. Overwriting context '. $this->routes[$host] . ' with ' . $ctx . ' for host ' . $host);
            }
            $this->routes[$host] = $ctx;
        }
    }
}
