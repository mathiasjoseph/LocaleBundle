<?php
/**
 * Created by PhpStorm.
 * User: miky
 * Date: 04/10/16
 * Time: 22:37
 */

namespace Miky\Bundle\LocaleBundle\Manager;


use Miky\Bundle\LocaleBundle\Entity\Language;
use Miky\Bundle\LocationBundle\Webservices\GeoList\GeoListProvider;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

class LanguageManager
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var string
     */
    protected $class;

    /**
     * @var \Doctrine\Common\Persistence\ObjectRepository
     */
    protected $repository;

    protected $geoListProvider;

    protected $container;

    protected $locales;

    protected $requestStack;

    /**
     * Constructor.
     * @param ObjectManager $om
     * @param string $class
     */
    public function __construct(ObjectManager $om, $class, GeoListProvider $geoListProvider, RequestStack $requestStack, $locales)
    {
        $this->objectManager = $om;
        $this->repository = $om->getRepository($class);
        $metadata = $om->getClassMetadata($class);
        $this->class = $metadata->getName();
        $this->geoListProvider = $geoListProvider;
        $this->requestStack = $requestStack;
        $this->locales = $locales;
    }

    /**
     * {@inheritDoc}
     */
    public function deleteLanguage(Language $language)
    {
        $this->objectManager->remove($language);
        $this->objectManager->flush();
    }
    public function getArrayByName(){
        $array = array();

        foreach ($this->locales as $l){
            $array[$l] = ucfirst(\Locale::getDisplayLanguage($l, $this->requestStack->getCurrentRequest()->getLocale()));
        }
        return $array;
    }

    public function getClass()
    {
        return $this->class;
    }

    public function findLanguageBy(array $criteria)
    {
        return $this->repository->findOneBy($criteria);
    }

    public function findLanguagesBy(array $criteria)
    {
        return $this->repository->findBy($criteria);
    }

    public function findLanguages()
    {
        return $this->repository->findAll();
    }

    public function reloadLanguage(Language $language)
    {
        $this->objectManager->refresh($language);
    }

    public function loadLanguagesFromApi(){
        $currents = $this->findLanguages();
        foreach ($currents as $b){
            $this->objectManager->remove($b);
        }
        $this->objectManager->flush();
        $list = $this->geoListProvider->getLanguageList();
        foreach ($list as $c){
            $language = $this->createLanguage();
            $language->setName($c['name']);
            $language->setShortName($c['code']);
            $this->objectManager->persist($language);
        }
        $this->objectManager->flush();
    }

    /**
     * Updates a Language.
     *
     * @param Language $language
     * @param Boolean $andFlush Whether to flush the changes (default true)
     */
    public function updateLanguage(Language $language, $andFlush = true)
    {
        $this->objectManager->persist($language);
        if ($andFlush) {
            $this->objectManager->flush();
        }
    }

    /**
     * Returns an empty Language instance
     *
     * @return Language
     */
    public function createLanguage()
    {
        $class = $this->getClass();
        $language = new $class;
        return $language;
    }



    /**
     * Refreshed a Language by Language Instance
     * @param Language $language
     * @return Ad
     */
    public function refreshLanguage(Language $language)
    {

        $refreshedLanguage = $this->findLanguageBy(array('id' => $language->getId()));
        if (null === $refreshedLanguage) {
            throw new UsernameNotFoundException(sprintf('User with ID "%d" could not be reloaded.', $language->getId()));
        }
        return $refreshedLanguage;
    }


    public function supportsClass($class)
    {
        return $class === $this->getClass();
    }
}