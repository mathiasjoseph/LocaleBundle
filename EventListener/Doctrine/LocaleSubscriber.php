<?php
/**
 * Created by PhpStorm.
 * User: miky
 * Date: 27/10/16
 * Time: 09:08
 */

namespace Miky\Bundle\LocaleBundle\EventListener\Doctrine;
use Miky\Bundle\AdBundle\Entity\Ad;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\HttpFoundation\RequestStack;

class LocaleSubscriber implements EventSubscriber
{

    private $requestStack;

    /**
     * LocaleSubscriber constructor.
     */
    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function getSubscribedEvents()
    {
        return array(
            'prePersist',
        );
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof Ad) {
            $entityManager = $args->getEntityManager();
           $entity->setLocale($this->requestStack->getCurrentRequest()->getLocale());
            $entityManager->flush();
        }
    }

}