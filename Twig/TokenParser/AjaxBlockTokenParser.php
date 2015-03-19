<?php
/**
 * This file is created by sam0delkin (t.samodelkin@gmail.com).
 * IT-Excellence (http://itedev.com)
 * Date: 12.03.2015
 * Time: 16:14
 */

namespace ITE\JsBundle\Twig\TokenParser;

use ITE\JsBundle\Twig\Node\AjaxBlockReferenceNode;
use Twig_Error_Syntax;
use Twig_Node;
use Twig_Node_Block;
use Twig_Node_Print;
use Twig_Token;

/**
 * Class AjaxBlockTokenParser
 *
 * @package ITE\JsBundle\Twig\TokenParser
 */
class AjaxBlockTokenParser extends \Twig_TokenParser_Block
{
    /**
     * @param Twig_Token $token
     * @return AjaxBlockReferenceNode
     * @throws Twig_Error_Syntax
     */
    public function parse(Twig_Token $token)
    {
        $lineno = $token->getLine();
        $stream = $this->parser->getStream();
        $realName = $stream->expect(Twig_Token::NAME_TYPE)->getValue();
        $name = sprintf('ajax_block_%s', md5($realName));

        if ($this->parser->hasBlock($name)) {
            throw new Twig_Error_Syntax(sprintf("The ajax_block '$name' has already been defined line %d", $this->parser->getBlock($name)->getLine()), $stream->getCurrent()->getLine(), $stream->getFilename());
        }
        $this->parser->setBlock($name, $block = new Twig_Node_Block($name, new Twig_Node(array()), $lineno));
        $this->parser->pushLocalScope();
        $this->parser->pushBlockStack($name);

        if ($stream->nextIf(Twig_Token::BLOCK_END_TYPE)) {
            $body = $this->parser->subparse(array($this, 'decideBlockEnd'), true);
            if ($token = $stream->nextIf(Twig_Token::NAME_TYPE)) {
                $value = $token->getValue();

                if ($value != $name) {
                    throw new Twig_Error_Syntax(sprintf("Expected end_ajax_block for ajax_block '$name' (but %s given)", $value), $stream->getCurrent()->getLine(), $stream->getFilename());
                }
            }
        } else {
            $body = new Twig_Node(array(
              new Twig_Node_Print($this->parser->getExpressionParser()->parseExpression(), $lineno),
            ));
        }
        $stream->expect(Twig_Token::BLOCK_END_TYPE);

        $block->setNode('body', $body);
        $this->parser->popBlockStack();
        $this->parser->popLocalScope();

        return new AjaxBlockReferenceNode($realName, $name, $lineno, $this->getTag());
    }

    /**
     * @param Twig_Token $token
     * @return bool
     */
    public function decideBlockEnd(Twig_Token $token)
    {
        return $token->test('end_ajax_block');
    }

    /**
     * Gets the tag name associated with this token parser.
     *
     * @return string The tag name
     */
    public function getTag()
    {
        return 'ajax_block';
    }

}