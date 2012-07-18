++  ContextRouter
++++++++++++++++++++

ContextRouter is a simple plug-and-play plugin allowing you to use different contexts, and, based on the http_host context
settings you need to set anyway, it routes your front-end requests as required.

In essence it's like the Gateway plugin from the docs, except you don't have to manually edit the plugin: it takes
the settings you have already configured in your context, caches it and routes based on that.

GETTING STARTED
1. Install the plugin (you're working on that, good job!)
2. Make sure your contexts have http_host context settings set.
3. (optional) Assign http_host_aliases context settings where needed to alias other hosts.
4. Clear cache via Site > Clear Cache to rebuild the cache file.
