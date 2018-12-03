<?php

/**
 * @package   yii2-krajee-base
 * @author    Kartik Visweswaran <kartikv2@gmail.com>
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014 - 2018
<<<<<<< HEAD
 * @version   2.0.3
=======
 * @version   2.0.2
>>>>>>> df1a21e84a73f0fb0e15ac53c3cc5acf88287564
 */

namespace kartik\base;

/**
 * Asset bundle for loading animations.
 *
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 */
class AnimateAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->setSourcePath(__DIR__ . '/assets');
        $this->setupAssets('css', ['css/animate']);
        parent::init();
    }
}
