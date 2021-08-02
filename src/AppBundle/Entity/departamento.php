<?php
/**
 * Created by PhpStorm.
 * User: Lisandra
 * Date: 19/02/2019
 * Time: 4:57
 */

namespace AppBundle\Entity;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="departamentoRepository")
 * @ORM\Table(name="departamento")
 */
class departamento
{
  /**
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   * @ORM\Column(type="integer")
   */
  private $id;
  /**
   * @ORM\Column(type="string")
   */
  private $name;
  /**
   * @var departamento
   *
   * @ORM\OneToMany(targetEntity="AppBundle\Entity\inventario",mappedBy="centroCosto")
   */
  private $estacion;
  /**
   * @var departamento
   *
   * @ORM\OneToMany(targetEntity="AppBundle\Entity\solicitud",mappedBy="centroCosto")
   */
  private $solicitud_material;
  /**
   * @ORM\OneToMany(targetEntity="AppBundle\Entity\equipo", mappedBy="departamento")
   */
  private $lista_equipos;
//  /**
//   * @ORM\OneToMany(targetEntity="AppBundle\Entity\equipoAssets", mappedBy="id_area")
//   */
//  private $lista_equiposAssets;
  /**
   * @var area
   *
   * @ORM\ManyToOne(targetEntity="AppBundle\Entity\area",inversedBy="dpto")
   * @ORM\JoinColumn(name="area_id",referencedColumnName="id",unique=false)
   */
  private $area;

  /**
   * @ORM\Column(type="integer")
   */
  private $idCosto;
//  /**
//   * @ORM\Column(type="integer")
//   */
//  private $idCostoReal;
  /**
   * Constructor
   */
  public function __construct()
  {
    $this->lista_equipos = new ArrayCollection();
    $this->solicitud_material = new ArrayCollection();
   // $this->lista_equiposAssets = new ArrayCollection();
  }

  /**
   * Add equipo.
   *
   * @param \AppBundle\Entity\equipo $equipo
   *
   * @return departamento
   */
  public function addEquipo($equipo)
  {
    $this->lista_equipos[] = $equipo;

    return $this;
  }

  /**
   * Remove equipo.
   *
   * @param \AppBundle\Entity\equipo $equipo
   *
   * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
   */
  public function removeEquipo($equipo)
  {
    return $this->lista_equipos->removeElement($equipo);
  }
//  /**
//   * Add equipo.
//   *
//   * @param \AppBundle\Entity\equipoAssets $equipoA
//   *
//   * @return departamento
//   */
//  public function addEquipoAssets($equipoA)
//  {
//    $this->lista_equiposAssets[] = $equipoA;
//
//    return $this;
//  }
//
//  /**
//   * Remove equipo.
//   *
//   * @param \AppBundle\Entity\equipoAssets $equipoA
//   *
//   * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
//   */
//  public function removeEquipoAs($equipoA)
//  {
//    return $this->lista_equiposAssets->removeElement($equipoA);
//  }
  /**
   * Get equipo.
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getEquipos()
  {
    return $this->lista_equipos;
  }
//  /**
//   * Get equipo.
//   *
//   * @return \Doctrine\Common\Collections\Collection
//   */
//  public function getEquiposAssets()
//  {
//    return $this->lista_equiposAssets;
//  }
  /**
   * @param mixed $equipos
   */
  public function setEquipos($equipos)
  {
    $this->lista_equipos = $equipos;
  }
  /**
   * @param mixed $equipos
   */
  public function setEquiposAss($equipos1)
  {
    $this->lista_equiposAssets = $equipos1;
  }
  /**
   * @return mixed
   */
  public function getIdCosto()
  {
    return $this->idCosto;
  }

  /**
   * @param mixed $idCosto
   */
  public function setIdCosto($idCosto)
  {
    $this->idCosto = $idCosto;
  }

  /**
   * @return mixed
   */
  public function getName()
  {
    return $this->name;
  }

  /**
   * @param mixed $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }

  /**
   * @return mixed
   */
  public function getId()
  {
    return $this->id;
  }

  public function __toString()
  {
    return $this->getName();
  }

  /**
   * @return departamento
   */
  public function getEstacion()
  {
    return $this->estacion;
  }

  /**
   * @param departamento $estacion
   */
  public function setEstacion($estacion)
  {
    $this->estacion = $estacion;
  }


  /**
   * @return area
   */
  public function getArea()
  {
    return $this->area;
  }

  /**
   * @param area $area
   */
  public function setArea($area)
  {
    $this->area = $area;
  }


}