<?php
/**
 * Created by PhpStorm.
 * User: LisMar
 * Date: 15/09/2018
 * Time: 0:29
 */

namespace AppBundle\Doctrine;



use AppBundle\Entity\Administracion\Usuario;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class HashPasswordListener implements EventSubscriber
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;

    }

    /**
     * Returns an array of events this subscriber wants to listen to.
     *
     * @return array
     */
    public function getSubscribedEvents()
    {
        return ['prePersist', 'preUpdate'];
    }

    public function prePersist(LifecycleEventArgs $args)
    {

        $entity = $args->getEntity();
        if (!$entity instanceof Usuario) {
            return;
        }
        $this->encodePassword($entity);
    }

    public function preUpdate(LifecycleEventArgs $args)
    {

        $entity = $args->getEntity();
        if (!$entity instanceof Usuario) {
            return;
        }
        $this->encodePassword($entity);

        $em = $args->getEntityManager();
        $meta = $em->getClassMetadata(get_class($entity));
        $em->getUnitOfWork()->recomputeSingleEntityChangeSet($meta, $entity);
    }

    private function encodePassword(Usuario $entity)
    {
        if (!$entity->getPlainPassword()) {

            return;
        }
        $encodePassword = $this->passwordEncoder->encodePassword($entity, $entity->getPlainPassword());
        $entity->setPassword($encodePassword);


    }
}