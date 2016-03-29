<?php
/**
 * @link http://www.digitaldeals.cz/
 * @copyright Copyright (c) 2016 Digital Deals s.r.o.
 * @license http://www.digitaldeals.cz/license/
 */

namespace dlds\intercooler\widgets;

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use dlds\intercooler\Intercooler;

/**
 * This is the main class of the LazilyLoad widget
 *
 * @author Jiri Svoboda <jiri.svobodao@dlds.cz>
 * @package lazyload
 */
class LazilyLoad extends \yii\base\Widget {

    /**
     * @var string inital html before lazily load is done
     */
    public $loadingHtml;

    /**
     * @var string fallback html if lazily load failed
     */
    public $fallbackHtml;

    /**
     * @var string wrapper tag
     */
    public $wrapper = 'div';

    /**
     * @var array additional wrapper options
     */
    public $options = [];

    /**
     * @var array intercooler config
     */
    public $intercooler = [];

    /**
     * @var Intercooler instance
     */
    protected $_handler;

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->_handler = new Intercooler($this->intercooler);

        echo Html::beginTag($this->wrapper, $this->initOptions());

        if ($this->loadingHtml)
        {
            echo Html::tag('div', $this->loadingHtml, [
                'class' => 'ic-loading ic-indicator',
            ]);
        }

        if ($this->fallbackHtml)
        {
            echo Html::tag('div', $this->fallbackHtml, [
                'class' => 'ic-fallback',
                'style' => 'display:none',
            ]);
        }
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        echo Html::endTag($this->wrapper);
    }

    /**
     * Initalizes and retrieves all required options for intercooler and user defined options
     */
    protected function initOptions()
    {
        $options = ArrayHelper::merge($this->_handler->getOptions($this->id), $this->options);

        if ($this->fallbackHtml)
        {
            $options[Intercooler::getAttrName(Intercooler::ATTR_EVT_ON_ERROR)] = new \yii\web\JsExpression("var e = document.querySelector('#$this->id .ic-fallback'); if (typeof(e) != 'undefined' && e != null) {e.style.display = null;}");
            $options[Intercooler::getAttrName(Intercooler::ATTR_EVT_ON_BEFORE_SEND)] = new \yii\web\JsExpression("var icf = document.querySelector('#$this->id .ic-fallback'); if (typeof(icf) != 'undefined' && icf != null) {icf.style.display = 'none';} var icl = document.querySelector('#$this->id .ic-loading'); if (typeof(icl) != 'undefined' && icl != null) {icl.style.display = null;}");
            $options[Intercooler::getAttrName(Intercooler::ATTR_EVT_ON_COMPLETE)] = new \yii\web\JsExpression("var e = document.querySelector('#$this->id .ic-loading'); if (typeof(e) != 'undefined' && e != null) {e.style.display = 'none';}");
        }

        return $options;
    }
}