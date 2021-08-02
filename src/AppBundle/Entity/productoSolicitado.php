<?php
/**
 * Created by PhpStorm.
 * User: Thais
 * Date: 7/19/2019
 * Time: 2:04 PM
 */

namespace AppBundle\Entity;


use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="productoSolicitadoRepository")
 * @ORM\Table(name="producto_solicitado")
 */
class productoSolicitado
{
  /**
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   * @ORM\Column(type="integer")
   */private $id;

  /**
   * @ORM\OneToOne(targetEntity="AppBundle\Entity\productoAssets", inversedBy="productoSolicitado", cascade={"persist", "remove"})
   * @ORM\JoinColumn(name="producto_id", referencedColumnName="id", nullable=false,unique=false)
   */
  private $idProducto;

  /**
   * @ORM\Column(type="string")
   */
  private $um;
  /**
   * @ORM\Column(type="string")
   */private $cantidad;
  /**
   * @var productoAssets
   *
   * @ORM\ManyToOne(targetEntity="AppBundle\Entity\solicitud", inversedBy="material")
   * @ORM\JoinColumn(name="solicitud_id", referencedColumnName="id")
   */
  private $solicitud;

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
  /**
   * @return mixed
   */


  /**
   * @return mixed
   */
  public function getUm()
  {
    return $this->um;
  }

  /**
   * @param mixed $um
   */
  public function setUm($um)
  {
    $this->um = $um;
  }

  /**
   * @return mixed
   */
  public function getCantidad()
  {
    return $this->cantidad;
  }

  /**
   * @param mixed $cantidad
   */
  public function setCantidad($cantidad)
  {
    $this->cantidad = $cantidad;
  }

  /**
   * @return mixed
   */
  public function getId()
  {
    return $this->id;
  }

  /**
   * @return productoAssets
   */
  public function getSolicitud()
  {
    return $this->solicitud;
  }

  /**
   * @param productoAssets $solicitud
   */
  public function setSolicitud($solicitud)
  {
    $this->solicitud = $solicitud;
  }

  public function __toString()
  {
    return $this->getUm();
  }




}