<?php
/**
 * Created by PhpStorm.
 * User: miky
 * Date: 26/11/16
 * Time: 04:06
 */

namespace Miky\Bundle\LocaleBundle\EventListener;



use Miky\Bundle\InstallerBundle\Event\InstallationEvent;
use Miky\Bundle\InstallerBundle\MikyInstallerEvents;
use Miky\Bundle\LocaleBundle\Doctrine\LanguageManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class LocaleInstallerSubscriber implements EventSubscriberInterface
{
    protected $languageManager;

    public function __construct(LanguageManager $languageManager)
    {
        $this->languageManager = $languageManager;
    }


    public static function getSubscribedEvents()
    {
        return array(
            MikyInstallerEvents::INSTALL_INITIALIZE => 'onInstallation',
        );
    }

    public function onInstallation(InstallationEvent $event)
    {
        //$this->languageManager->loadLanguagesFromApi();
    }
}