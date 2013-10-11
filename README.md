JsBundle
========

Add ite_js.sf service in symfony 2 and global SF object in javascript.

Configuration
-------------

```yml
# app/config/config.yml
ite_js:
    flashes_selector:     '#flashes'
```    

Service 'ite_js.sf'
-------------------

This service is responsible for building SF object and dumping it to javascript. It dumps some system parameters by default, but you can dump own variables:
``` php
$container->get('ite_js.sf')->getParameterBag()->set('foo', 'bar');
```
Also it can act on kernel.view and kernal.response internal symfony 2 events. So, for example, it use these events to collect and pass to javascript session flashes during AJAX requests.

SF object in javascript
-----------------------

All parameters are stored in SF.parameters object. Check sources for available methods.
If you have FOSJsRoutingBundle installed, then SF.path function will be available as an alias for Routing.generate.
SF.renderFlashes(flashes, selector) - render session flashes, you can override it using SF.fn.renderFlashes.

Extensions
----------

This service has extension support, for example ITEFormBundle works as an extension for this bundle. To register new extension, create new class that implements ITE\JsBundle\Service\SFExtensionInterface

``` php
// src/Acme/DemoBundle/SF/TestExtension.php
class TestExtension implements SFExtensionInterface
{
    public function dump()
    {
        // ...
    }
        
    public function onKernelView(GetResponseForControllerResultEvent $event)
    {
        // ...
    }
        
    public function onKernelResponse(FilterResponseEvent $event)
    {
        // ...
    }
}
```
and register it as a service:
```yml
    acme_demo.sf.extension.test:
        class:                                              Acme\DemoBundle\SF\TestExtension
        tags:
            - { name: ite_js.sf.extension, alias: test }
```   
After that you can get neede extension in this way:
``` php
$container->get('ite_js.sf')->getExtension('form');
```

    
    
