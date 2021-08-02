<?php

namespace AppBundle\Repository;

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\EntityRepository;

/**
 * HistorialRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class HistorialRepository extends EntityRepository
{
  /**
   * Obtener las todas las trazas del sistema
   */
  public function findTrazas()
  {
    $qb = $this->getEntityManager()->createQueryBuilder();
    $qb->select('h')
      ->from('SighBundle:Administracion\Historial', 'h')
      ->orderBy('h.fecha', 'DESC')
      ->setMaxResults(10000);

    return $qb->getQuery()->getResult();
  }
}
