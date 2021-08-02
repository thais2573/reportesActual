<?php

namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * equipoRepository
 *
 * This class was generated by the PhpStorm "Php Annotations" Plugin. Add your own custom
 * repository methods below.
 */
class equipoRepository extends EntityRepository
{
  public function findTiposEquipo(inventario $inventario)
  {
    $qb = $this->getEntityManager()->createQueryBuilder();
    $qb->select('p')
      ->from('AppBundle:equipo', 'e')
      ->select('e.tipoEquipo')
      ->where('e.estacion=:estac')
      ->setParameter('estac',$inventario)
      ->groupBy('e.tipoEquipo');

    return $qb->getQuery()->getResult();
  }

  public function CantidadEquipos($idEstacion)
  {
    $queryBuilder = $this->createQueryBuilder('equipo');
    $queryBuilder
      ->select('count(equipo.id)','equipo.tipoEquipo')
      ->where('equipo.estacion = :tipo')
      ->setParameter('tipo', $idEstacion)
      ->groupBy('equipo.tipoEquipo');

    return $queryBuilder->getQuery()->getResult();
  }

}