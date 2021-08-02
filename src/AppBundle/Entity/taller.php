<?php
/**
 * Created by PhpStorm.
 * User: Thais
 * Date: 3/22/2019
 * Time: 5:50 PM
 */

namespace AppBundle\Entity;



use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="taller")
 */
class taller
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
  private $tipo_periferico;
  /**
   * @ORM\Column(type="string")
   */
  private $id_periferico;
  /**
   * @ORM\Column(type="string")
   */
  private $modelo;
  /**
   * @ORM\Column(type="string")
   */
  private $dpto;

  /**
   * @ORM\Column(type="string")
   */
  private $numInventario;

  /**
   * @ORM\Column(type="date",nullable=true)
   *@Assert\Date
   */
  private $fechaSalida;
  /**
   * @return mixed
   */
  public function getTipoPeriferico()
  {
    return $this->tipo_periferico;
  }

  /**
   * @param mixed $tipo_periferico
   */
  public function setTipoPeriferico($tipo_periferico)
  {
    $this->tipo_periferico = $tipo_periferico;
  }

  /**
   * @return mixed
   */
  public function getIdPeriferico()
  {
    return $this->id_periferico;
  }

  /**
   * @param mixed $id_periferico
   */
  public function setIdPeriferico($id_periferico)
  {
    $this->id_periferico = $id_periferico;
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
  public function getFechaSalida()
  {
    return $this->fechaSalida;
  }

  /**
   * @param mixed $fechaSalida
   */
  public function setFechaSalida($fechaSalida)
  {
    $this->fechaSalida = $fechaSalida;
  }

  /**
   * @return mixed
   */
  public function getModelo()
  {
    return $this->modelo;
  }

  /**
   * @param mixed $modelo
   */
  public function setModelo($modelo)
  {
    $this->modelo = $modelo;
  }


}