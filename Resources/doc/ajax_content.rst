AjaxContent Feature
===================

Overview
--------

AjaxContent provides functional for injecting any data to ajax responses
without modifying original response. In any words, ajax response will be
wrapped, then wrapped data will be processed and original data will be
returned to original AJAX callback.

Usage
-----

First, you need to regiser an extension:

.. code-block:: yaml

    # Acme/DemoBundle/Resources/config/services.yml

    acme_demo.sf.test_extension:
        class: Acme\DemoBundle\SF\TestExtension
        tags:
            - { name: ite_js.sf.extension, alias: test } //note, that alias will be used for wraping your data

Then, just return needed values in extension:

.. code-block:: php


    //Acme\DemoBundle\SF\TestExtension

    public function getAjaxContent(AjaxRequestEvent $event)
    {
        return ['foo' => 'bar'];
    }

    public function getJavascripts()
    {
        return array('@AcmeDemoBundle/Resources/public/js/foo_bar.js'); //this file will be described below
    }

Then you need to create a wrap data processor:

.. code-block:: javascript


    (function($) {
      $(document).on('ite-ajax-loaded.content', function (e, contentData) {
        var data = 'undefined' !== typeof contentData._sf_test ? contentData._sf_test : [];
        //data will be hash: {foo: 'bar'}
      });
    })(jQuery);

Finally, you can create test controller:

.. code-block:: php


    //Acme\DemoBundle\Controller\TestController

    public function testAction()
    {
        return new JsonResponse(['foo1' => 'bar1']);
    }

and call it via AJAX from JS side:

.. code-block:: javascript

    //AcmeDemoBundle/Resources/public/js/some_script.js
    (function($) {
        $.get(SF.path('test_test'), {}, function(data) {
            // data will be hash: {foo1: "bar1"}, but if you see on the browser console, then actual response will be different.
            // so, original (controller) data will be returned to this, original callback, and content data can be processed
            // on "ite-ajax-loaded.content" event.
        }, 'json');
    })(jQuery);