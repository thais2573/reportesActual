<?php
/**
 * Created by PhpStorm.
 * User: Lisandra
 * Date: 19/02/2019
 * Time: 8:47
 */

namespace AppBundle\Repository;
use Doctrine\ORM\EntityRepository;

class mouseRepository extends EntityRepository
{
    public function Todo($id)
    {

        $queryBuilder = $this->createQueryBuilder('mouse');



        $queryBuilder
            ->andWhere('mouse.inventario = :tipo')
            ->setParameter('tipo', $id)
            ->select('mouse');

        return $queryBuilder->getQuery()->execute();

    }

}