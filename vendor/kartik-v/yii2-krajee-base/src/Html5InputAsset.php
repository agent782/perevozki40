<?php

/**
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014 - 2018
 * @package yii2-krajee-base
<<<<<<< HEAD
 * @version 2.0.3
=======
 * @version 2.0.2
>>>>>>> df1a21e84a73f0fb0e15ac53c3cc5acf88287564
 */

namespace kartik\base;

/**
 * Asset bundle for the [[Html5Input]] widget.
 *
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 */
class Html5InputAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->setSourcePath(__DIR__ . '/assets');
        $this->setupAssets('css', ['css/html5input']);
        parent::init();
    }
}
