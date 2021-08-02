<?php
/**
 * Created by PhpStorm.
 * User: Lisandra
 * Date: 04/03/2019
 * Time: 15:41
 */

namespace AppBundle\Repository;


class tipoRepository
{
    public function findByFiltersAndPaginate(array $criteria, array $orderBy = null, array $pagination = null)
    {
        $queryBuilder = $this->createQueryBuilder('tipo');

        if ($criteria !== null)
            foreach ($criteria as $key => $value) {
                $field = "tipo." . $key;
                $queryBuilder->andWhere($field . " like '%" . $value . "%'");
            }

        if ($orderBy !== null)
            foreach ($orderBy as $key => $value) {
                $queryBuilder->orderBy("tipo." . $key, $value);
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
        $queryBuilder = $this->createQueryBuilder('tipo');

        if ($criteria !== null)

            foreach ($criteria as $key => $value) {
                $field = "tipo." . $key;
                $queryBuilder->andWhere($field . " like '%" . $value . "%'");
            }
        if ($orderBy !== null)

            if ($orderBy !== null)
                foreach ($orderBy as $key => $value) {
                    $queryBuilder->orderBy("tipo." . $key, $value);
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