JsBundle
========

Add ite_js.sf service in symfony 2 and global SF object in javascript.

ite_js.sf service
-----------------

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
    
    
