<?php
/**
 * Created by PhpStorm.
 * User: Thais
 * Date: 4/23/2019
 * Time: 9:58 AM
 */

namespace  AppBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;

/**
 * @ORM\Table(name="tb_componente_de_inventario")
 *@ORM\Entity()
 */
class componente_de_inventarios
{

  /**
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   * @ORM\Column(type="integer")
   */
  private $id;
  /**
   * @ORM\Column(type="string",nullable=true)
   */
  private $modelo;
  /**
   * @ORM\Column(type="string")
   */
  private $serie;
  /**
   * @ORM\Column(type="string",nullable=true)
   */
  private $marca;
  /**
   *
   * @ORM\Column(type="string",nullable=true)
   */
  private $estado;
    /**
     * @var inventario
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\inventario", inversedBy="componentes_en_inventarios")
     * @ORM\JoinColumn(name="estacion_id", referencedColumnName="id" , onDelete="SET NULL")
     */
    private $inventarioC;
  /**
   * @ORM\Column(type="string",nullable=true)
   */
  private $watts;
  /**
   * @ORM\Column(type="string",nullable=true)
   */
  private $sata;
  /**
   * @ORM\Column(type="string",nullable=true)
   */
  private $capacidad;
  /**
   * @ORM\Column(type="string",nullable=true)
   */
  private $tipo;
  /**
   * @ORM\Column(type="string",nullable=true)
   */
  private  $velocidad;
  /**
   * @ORM\Column(type="string",nullable=true)
   */
  private $lga;
  /**
   * @ORM\Column(type="string",nullable=true)
   */
  private $ram;
  /**
   * @ORM\Column(type="string",nullable=true)
   */
  private $ranuraVideo;
  /**
   * @ORM\Column(type="string",nullable=true)
   */
  private $optico;
  /**
   * @ORM\Column(type="string",nullable=true)
   */
  private $conector;
  /**
   * @Assert\NotBlank()
   * @ORM\Column(type="string",nullable=false)
   */
  private $tipoComponente;

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
  public function getEstado()
  {
    return $this->estado;
  }

  /**
   * @param mixed $estado
   */
  public function setEstado($estado)
  {
    $this->estado = $estado;
  }

  /**
   * @return componente
   */
  public function getInventarioC()
  {
    return $this->inventarioC;
  }

  /**
   * @param componente $inventarioC
   */
  public function setInventarioC($inventarioC)
  {
    $this->inventarioC = $inventarioC;
  }

  /**
   * @return mixed
   */
  public function getWatts()
  {
    return $this->watts;
  }

  /**
   * @param mixed $watts
   */
  public function setWatts($watts)
  {
    $this->watts = $watts;
  }

  /**
   * @return mixed
   */
  public function getSata()
  {
    return $this->sata;
  }

  /**
   * @param mixed $sata
   */
  public function setSata($sata)
  {
    $this->sata = $sata;
  }

  /**
   * @return mixed
   */
  public function getCapacidad()
  {
    return $this->capacidad;
  }

  /**
   * @param mixed $capacidad
   */
  public function setCapacidad($capacidad)
  {
    $this->capacidad = $capacidad;
  }

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
  public function getVelocidad()
  {
    return $this->velocidad;
  }

  /**
   * @param mixed $velocidad
   */
  public function setVelocidad($velocidad)
  {
    $this->velocidad = $velocidad;
  }

  /**
   * @return mixed
   */
  public function getLga()
  {
    return $this->lga;
  }

  /**
   * @param mixed $lga
   */
  public function setLga($lga)
  {
    $this->lga = $lga;
  }

  /**
   * @return mixed
   */
  public function getRam()
  {
    return $this->ram;
  }

  /**
   * @param mixed $ram
   */
  public function setRam($ram)
  {
    $this->ram = $ram;
  }

  /**
   * @return mixed
   */
  public function getRanuraVideo()
  {
    return $this->ranuraVideo;
  }

  /**
   * @param mixed $ranuraVideo
   */
  public function setRanuraVideo($ranuraVideo)
  {
    $this->ranuraVideo = $ranuraVideo;
  }

  /**
   * @return mixed
   */
  public function getOptico()
  {
    return $this->optico;
  }

  /**
   * @param mixed $optico
   */
  public function setOptico($optico)
  {
    $this->optico = $optico;
  }

  /**
   * @return mixed
   */
  public function getConector()
  {
    return $this->conector;
  }

  /**
   * @param mixed $conector
   */
  public function setConector($conector)
  {
    $this->conector = $conector;
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
  public function getTipoComponente()
  {
    return $this->tipoComponente;
  }

  /**
   * @param mixed $tipoComponente
   */
  public function setTipoComponente($tipoComponente)
  {
    $this->tipoComponente = $tipoComponente;
  }


}