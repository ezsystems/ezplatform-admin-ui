<?php
/**
 * Created by PhpStorm.
 * User: mikolaj
 * Date: 11/17/17
 * Time: 12:36 PM
 */

namespace EzSystems\EzPlatformAdminUiBundle\Templating\Twig;


class ClassTwigExtension extends \Twig_Extension
{
    public function getFunctions()
    {
        return array(
            'class' => new \Twig_SimpleFunction('class', array($this, 'getClass'))
        );
    }

    public function getName()
    {
        return 'class_twig_extension';
    }

    public function getClass($object)
    {
        return (new \ReflectionClass($object))->getShortName();
    }
}