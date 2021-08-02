<?php
/**
 * Created by PhpStorm.
 * User: Lisandra
 * Date: 06/02/2019
 * Time: 10:54
 */

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Bundle\FrameworkBundle\Tests\Fixtures\Validation\Category;

class inventarioRepository extends EntityRepository
{

  public function EstacionT($dpto)
  {

    $queryBuilder = $this->createQueryBuilder('inventario');



    $queryBuilder
      ->andWhere('inventario.dpto = :tipo')
      ->setParameter('tipo', $dpto)
      ->select('inventario.id');

    return $queryBuilder->getQuery()->execute();

  }
  public function Todo($id)
  {

    $queryBuilder = $this->createQueryBuilder('inventario');



    $queryBuilder
      ->andWhere('inventario.id = :tipo')
      ->setParameter('tipo', $id)
      ->select('inventario');

    return $queryBuilder->getQuery()->execute();

  }


  public function TodoCentroCosto($componente,$numInv)
  {

    $repository = $this->getDoctrine()
      ->getRepository('AppBundle:'.$componente);


    $queryBuilder = $this->createQueryBuilder('tabla');



    $queryBuilder
      ->andWhere('tabla.numInventario = :numI')
      ->setParameter('numI', $numInv)
      ->select('tabla');

    return $queryBuilder->getQuery()->execute();

  }


    public function findByFiltersAndPaginate(array $criteria, array $orderBy = null, array $pagination = null,array $lista)
    {
        $queryBuilder = $this->createQueryBuilder('estacion');

        if ($criteria !== null)
            foreach ($criteria as $key => $value) {
                $field = "estacion." . $key;
                $queryBuilder->andWhere($field . " like '%" . $value . "%'");
            }

        if ($orderBy !== null)
            foreach ($orderBy as $key => $value) {
                $queryBuilder->orderBy("estacion." . $key, $value);
            }

        foreach ($pagination as $key => $value) {
            switch ($key) {
                case 'limit':
                    $queryBuilder->setMaxResults($value);
                    break;
                case 'offset':
                    $queryBuilder->setFirstResult($value);
                    break;
            }
        }


        return $queryBuilder->getQuery()->execute();
    }

    public function findByFiltersStatus(array $criteria, array $orderBy = null, array $pagination = null)
    {
        $queryBuilder = $this->createQueryBuilder('estacion');

        if ($criteria !== null)

            foreach ($criteria as $key => $value) {
                $field = "estacion." . $key;
                $queryBuilder->andWhere($field . " like '%" . $value . "%'");
            }
        if ($orderBy !== null)

            if ($orderBy !== null)
                foreach ($orderBy as $key => $value) {
                    $queryBuilder->orderBy("estacion." . $key, $value);
                }


        foreach ($pagination as $key => $value) {
            switch ($key) {
                case 'limit':
                    $queryBuilder->setMaxResults($value);
                    break;
                case 'offset':
                    $queryBuilder->setFirstResult($value);
                    break;
            }
        }


        return $queryBuilder->getQuery()->execute();

    }

}