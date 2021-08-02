<?php
/**
 * Created by PhpStorm.
 * User: Thais
 * Date: 7/19/2019
 * Time: 10:01 AM
 */

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="productoAssetsRepository")
 * @ORM\Table(name="productoAssets")
 */
class productoAssets
{
  /**
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   * @ORM\Column(type="integer")
   */private $id;

  /**
   * @ORM\Column(type="string",unique=true)
   */
  private $idProducto;

  /**
   * @ORM\Column(type="string")
   */
  private $descripcion;
  /**
   * @ORM\Column(type="string")
   */
  private $fechaEntrada;
  /**
   * @ORM\Column(type="string")
   */
  private $umAlmacen;
  /**
   * @ORM\OneToOne(targetEntity="AppBundle\Entity\productoSolicitado", mappedBy="idProducto", cascade={"persist", "remove"})
   */
  private $productoSolicitado;
  /**
   * @ORM\Column(type="string")
   */
  private $existencia;

//  public function __construct()
//  {
//    $this->productoSolicitado = new ArrayCollection();
//  }
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
  public function getFechaEntrada()
  {
    return $this->fechaEntrada;
  }

  /**
   * @param mixed $fechaEntrada
   */
  public function setFechaEntrada($fechaEntrada)
  {
    $this->fechaEntrada = $fechaEntrada;
  }

  /**
   * @return mixed
   */
  public function getUmAlmacen()
  {
    return $this->umAlmacen;
  }

  /**
   * @param mixed $umAlmacen
   */
  public function setUmAlmacen($umAlmacen)
  {
    $this->umAlmacen = $umAlmacen;
  }
  /**
   * @return mixed
   */
  public function getIdProducto()
  {
    return $this->idProducto;
  }

  /**
   * @param mixed $idProducto
   */
  public function setIdProducto($idProducto)
  {
    $this->idProducto = $idProducto;
  }
  public function __toString()
  {
    return $this->getDescripcion();
  }

  /**
   * @return mixed
   */
  public function getProductoSolicitado()
  {
    return $this->productoSolicitado;
  }

  /**
   * @param mixed $productoSolicitado
   */
  public function setProductoSolicitado($productoSolicitado)
  {
    $this->productoSolicitado = $productoSolicitado;
  }

    /**
     * @return mixed
     */
    public function getExistencia()
    {
        return $this->existencia;
    }

    /**
     * @param mixed $existencia
     */
    public function setExistencia($existencia)
    {
        $this->existencia = $existencia;
    }

}