ITEJsBundle
===========

Overview
--------

Creates global JavaScript `SF` object and provide tools for making the bridge between Symfony 2 application and JavaScript.

Features
--------

.. toctree::
    :hidden:

    ajax_content
    ajax_block

- :doc:`AjaxContent <ajax_content>`
- :doc:`AjaxBlock <ajax_block>`

Configuration
-------------

.. code-block:: yaml

    # app/config/config.yml

    ite_js:
        assetic:
            cssrewrite: ~ # override standard `cssrewrite` filter to support @AcmeDemoBundle/Resources/public/... syntax

Usage
-----

This bundle allows its extensions to add stylesheets and javascripts. To enable this feature, add `ite_js_sf_assets()` functions in your `stylesheets` or/and `javascripts` tags of your template. To dump all needed data for SF object in one inline js, add `{{ ite_js_sf_dump() }}` expression after corresponding `javascripts` tag.

.. code-block:: html+jinja

    {# app/Resources/views/base.html.twig #}

    {% stylesheets
        {# ... #}
        '@sf_stylesheets'
    %}
    <link href="{{ asset_url }}" type="text/css" rel="stylesheet" media="screen" />
    {% endstylesheets %}

    {% javascripts
        '@AcmeDemoBundle/Resources/public/js/jquery.js'
        {# ... #}
        '@sf_javascripts'
    %}
    <script type="text/javascript" src="{{ asset_url }}"></script>
    {% endjavascripts %}

    {{ ite_js_sf_dump() }}

Service 'ite_js.sf'
-------------------

This service is responsible for building `SF` object and dumping it to javascript. It dumps some system parameters by default, but you can dump own variables:

.. code-block:: php

    $container->get('ite_js.sf')->parameters->set('foo', 'bar');

SF object in javascript
-----------------------

If you have FOSJsRoutingBundle installed, then `SF.path` and `SF.url` functions will be available as an aliases for Routing.generate.

Extensions
----------

This service has extension support, for example ITEFormBundle works as an extension for this bundle. To register new extension, create new class that implements `ITE\JsBundle\Service\SFExtensionInterface` or extend `ITE\JsBundle\SF\SFExtension` class and register it as a service:

.. code-block:: yaml

    # Acme/DemoBundle/Resources/config/services.yml

    acme_demo.sf.test_extension:
        class: Acme\DemoBundle\SF\TestExtension
        tags:
            - { name: ite_js.sf.extension, alias: test }
