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

class estacionRepository extends EntityRepository
{
    public function findByFiltersAndPaginate(array $criteria, array $orderBy = null, array $pagination = null)
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