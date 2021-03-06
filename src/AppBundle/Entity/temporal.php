<?php
/**
 * Created by PhpStorm.
 * User: Thais
 * Date: 2/28/2019
 * Time: 9:33 AM
 */

namespace AppBundle\Entity;


use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="temporal")
 */
class temporal
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
  private $idperiferico;
  /**
   * @ORM\Column(type="string")
   */
  private $tipo;
  /**
   * @ORM\Column(type="string")
   */
  private $cantidad;
  /**
   * @ORM\Column(type="string")
   */
  private $marca;
  /**
   * @ORM\Column(type="string")
   */
  private $inventario;


  /**
   * @ORM\Column(type="string",nullable=true)
   */
  private $estacionActual;

  /**
   * @return mixed
   */
  public function getTipo()
  {
    return $this->tipo;
  }

  /**
   * @param mixed $tipo
   */
  public function setTipo($tipo)
  {
    $this->tipo = $tipo;
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
  public function getMarca()
  {
    return $this->marca;
  }

  /**
   * @param mixed $marca
   */
  public function setMarca($marca)
  {
    $this->marca = $marca;
  }


  /**
   * @return mixed
   */
  public function getInventario()
  {
    return $this->inventario;
  }

  /**
   * @param mixed $inventario
   */
  public function setInventario($inventario)
  {
    $this->inventario = $inventario;
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
  public function getIdperiferico()
  {
    return $this->idperiferico;
  }

  /**
   * @param mixed $idperiferico
   */
  public function setIdperiferico($idperiferico)
  {
    $this->idperiferico = $idperiferico;
  }

  /**
   * @return mixed
   */
  public function getEstacionActual()
  {
    return $this->estacionActual;
  }

  /**
   * @param mixed $estacionActual
   */
  public function setEstacionActual($estacionActual)
  {
    $this->estacionActual = $estacionActual;
  }



}