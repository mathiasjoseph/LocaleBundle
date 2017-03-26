<?php
/**
 * Created by PhpStorm.
 * User: miky
 * Date: 02/10/16
 * Time: 15:32
 */

namespace Miky\Bundle\LocaleBundle\Installer;


use Miky\Bundle\InstallerBundle\Model\InstallerInterface;
use Miky\Bundle\LocaleBundle\Manager\LanguageManager;

class LocaleInstaller implements InstallerInterface
{
    protected $languageManager;

    /**
     * LocationInstaller constructor.
     */
    public function __construct(LanguageManager $languageManager){
        $this->languageManager = $languageManager;
    }

    public function run(){
        $this->languageManager->loadLanguagesFromApi();
    }
}