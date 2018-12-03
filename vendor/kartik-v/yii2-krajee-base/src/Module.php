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

use ReflectionException;
use yii\base\InvalidConfigException;
use yii\base\Module as YiiModule;

/**
 * Base module class for Krajee extensions
 *
 * @author Kartik Visweswaran <kartikv2@gmail.com>
<<<<<<< HEAD
=======
 * @since 2.0.2
>>>>>>> df1a21e84a73f0fb0e15ac53c3cc5acf88287564
 */
class Module extends YiiModule implements BootstrapInterface
{
    use TranslationTrait;
    use BootstrapTrait;

    /**
     * @inheritdoc
     * @throws InvalidConfigException
     * @throws ReflectionException
     */
    public function init()
    {
        $this->initBsVersion();
        parent::init();
        $this->initI18N();
    }
}
