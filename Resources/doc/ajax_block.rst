AjaxBlock Feature
=================

Overview
--------

AjaxBlock is based on the AjaxContent feature, and provides
functionality for rendering twig blocks into any selectors on the page,
based on annotations. For more information about AjaxContent, see
AjaxContent documentation.

Usage
-----

First, you need to enable it in config:

.. code:: yml

    # app/config/config.yml
    ite_js:
        extensions:
            ajax_block: # enabling AjaxBlock
                show_animation:
                    type: fade # default animation for rendering block
                    length: 500 # default animation length

Second, you need to add @AjaxBlock annotation to your controller:

.. code:: php


    use ITE\JsBundle\Annotation\AjaxBlock; //don't forget to use block

    //Acme\DemoBundle\Controller\TestController
    /**
     * @AjaxBlock(
     * blockName="test", //ajax block name in twig template, will be described below - required
     * selector="#test", //selector that will be used to replace block - required
     * showAnimation="fade", showLength="1000", //animation settings
     * template="@AcmeDemo/Test/test.html.twig") // template name, of you want to override it. @Template annotation will be used by default 
     */
    public function testAction()
    {
        return ['foo1' => 'bar1'];
    }

Next, you will need to define ajax\_block in the twig template:

.. code:: twig


    {# @AcmeDemo/Test/test.html.twig #}

    {% block content %}
        {{ foo1 }}
        <div id="test"> {# note, that this is the selector from @AjaxBlock annotation #}
        {% ajax_block test %} {# note, that this is the blockName from @AjaxBlock annotation #}
            {{ rand() }} {# render random number for test #}
        {% end_ajax_block %}
        </div>
    {% endblock %}

Finally, you just need to execute AJAX query:

.. code:: javascript


    $.get(SF.path('test_test'), {}, function(data) {
            // data will be hash: {foo1: "bar1"}, and you will see, that div#test content will be replaced with new random number
        }, 'json');