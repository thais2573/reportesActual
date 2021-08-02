<?php
/**
 * Created by PhpStorm.
 * User: Thais
 * Date: 4/12/2019
 * Time: 11:02 AM
 */

namespace AppBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;

/**
 * @ORM\Entity(repositoryClass="equipoAssetsRepository")
 * @ORM\Table(name="tb_equipoassets")
 *
 */
class equipoAssets
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
  private $descripcion;
  /**
   * @ORM\Column(type="string",unique=true)
   */
  private $numInventario;
  /**
//   * @ORM\ManyToOne(targetEntity="AppBundle\Entity\area",inversedBy="equipoAsset")
//   * @ORM\JoinColumn(name="id_costo",referencedColumnName="id",unique=false)
   * @ORM\Column(type="string")
   */
  private $id_costo;
  /**
   *
//   *@ORM\ManyToOne(targetEntity="AppBundle\Entity\departamento",inversedBy="lista_equiposAssets")
//   * @ORM\JoinColumn(name="id_area",referencedColumnName="id",unique=false)
   * @ORM\Column(type="string")
   */
  private $id_area;
  /**
   * @ORM\Column(type="string")
   */
  private $activo;
  /**
   * @ORM\Column(type="string",nullable=true)
   */
  private $serie;

  /**
   * @return mixed
   */
  public function getDescripcion()
  {
    return $this->descripcion;
  }

  /**
   * @param mixed $descripcion
   */
  public function setDescripcion($descripcion)
  {
    $this->descripcion = $descripcion;
  }

  /**
   * @return mixed
   */
  public function getNumInventario()
  {
    return $this->numInventario;
  }

  /**
   * @param mixed $numInventario
   */
  public function setNumInventario($numInventario)
  {
    $this->numInventario = $numInventario;
  }

  /**
   * @return mixed
   */
  public function getIdCosto()
  {
    return $this->id_costo;
  }

  /**
   * @param mixed $id_costo
   */
  public function setIdCosto($id_costo)
  {
    $this->id_costo = $id_costo;
  }

  /**
   * @return mixed
   */
  public function getIdArea()
  {
    return $this->id_area;
  }

  /**
   * @param departamento $id_area
   */
  public function setIdArea($id_area)
  {
    $this->id_area = $id_area;
  }

  /**
   * @return mixed
   */
  public function getActivo()
  {
    return $this->activo;
  }

  /**
   * @param mixed $activo
   */
  public function setActivo($activo)
  {
    $this->activo = $activo;
  }

  /**
   * @return mixed
   */
  public function getSerie()
  {
    return $this->serie;
  }

  /**
   * @param mixed $serie
   */
  public function setSerie($serie)
  {
    $this->serie = $serie;
  }

  /**
   * @return mixed
   */
  public function getId()
  {
    return $this->id;
  }



}