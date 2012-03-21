++  ContextRouter
++++++++++++++++++++

ContextRouter is a simple plug-and-play plugin allowing you to use different contexts, and, based on the http_host context
settings you need to set anyway, it routes your front-end requests as required.

In essence it's like the Gateway plugin from the docs, except you don't have to manually edit the plugin: it takes
the settings you have already configured in your context and routes based on that. It caches the http_host => context
relation so it doesn't perform excessive database lookups.
