<?php
/**
 * Created by PhpStorm.
 * User: Thais
 * Date: 3/11/2019
 * Time: 8:49 AM
 */

namespace AppBundle\Entity;



use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="areaRepository")
 * @ORM\Table(name="area")
 */
class area
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
  private $id_area;
  /**
   * @ORM\Column(type="string")
   */
  private $nombre;
  /**
   * @ORM\OneToMany(targetEntity="AppBundle\Entity\departamento", mappedBy="area", cascade={"persist","remove"})
   */
  private $dpto;
//  /**
//   * @ORM\OneToMany(targetEntity="AppBundle\Entity\equipoAssets", mappedBy="id_costo", cascade={"persist","remove"})
//   */
//  private $equipoAsset;
  /**
   * @return mixed
   */
  public function getNombre()
  {
    return $this->nombre;
  }

  /**
   * @param mixed $nombre
   */
  public function setNombre($nombre)
  {
    $this->nombre = $nombre;
  }

  /**
   * @return mixed
   */
  public function getId()
  {
    return $this->id;
  }

  /**
   * @return mixed
   */
  public function getIdArea()
  {
    return $this->id_area;
  }

  /**
   * @param mixed $id_area
   */
  public function setIdArea($id_area)
  {
    $this->id_area = $id_area;
  }

  /**
   * @return mixed
   */
  public function getDpto()
  {
    return $this->dpto;
  }

  /**
   * @param mixed $dpto
   */
  public function setDpto($dpto)
  {
    $this->dpto = $dpto;
  }

}