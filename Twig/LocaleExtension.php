<?php
/**
 * Created by PhpStorm.
 * User: miky
 * Date: 28/09/16
 * Time: 17:47
 */

namespace Miky\Bundle\LocaleBundle\Twig;


class LocaleExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('displayLanguage', array($this, 'displayLanguage')),
        );
    }

    public function displayLanguage($locale)
    {
        return \Locale::getDisplayLanguage($locale, $locale);
    }

    public function getName()
    {
        return 'locale_extension';
    }

}