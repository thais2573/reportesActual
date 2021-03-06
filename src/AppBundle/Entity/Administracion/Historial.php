<?php

namespace AppBundle\Entity\Administracion;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAsset;

/**
 * Historial
 *
 * @ORM\Table(name="historial")
 * @ORM\HasLifecycleCallbacks
 * @ORM\Entity(repositoryClass="AppBundle\Repository\HistorialRepository")
 */
class Historial
{
  /**
   * @var int
   *
   * @ORM\Column(name="id", type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  private $id;

  /**
   * @var string $ipPc
   *
   * @ORM\Column(name="ip_pc", type="string", nullable=false)
   * @Assert\Type(type="string")
   * @Assert\NotBlank(message="Este campo es requerido")
   */
  private $ipPc;

  /**
   * @var \DateTime
   *
   * @ORM\Column(name="fecha", type="datetime", nullable=false)
   * @Assert\DateTime
   */
  private $fecha;

  /**
   * @var integer $tipoAccion
   *
   * @ORM\Column(name="tipo_accion", type="integer", nullable=false)
   * @Assert\Type(type="integer")
   * @Assert\NotBlank( message = "Este campo es requerido" )
   */
  private $tipoAccion;

  /**
   * @var string $datosMod
   *
   * @ORM\Column(name="datos_mod", type="text", nullable=true)
   * @Assert\Type(type="string")
   */
  private $datosMod;

  /**
   * @var string $tablaImplicada
   *
   * @ORM\Column(name="tabla_implicada", type="string", nullable=false)
   * @Assert\Type(type="string")
   * @Assert\NotBlank(message="Este campo es requerido")
   */
  private $tablaImplicada;

  /**
   * @var \AppBundle\Entity\Administracion\Usuario
   *
   * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Administracion\Usuario", inversedBy="historial")
   * @ORM\JoinColumn(name="usuario_id", referencedColumnName="id")
   */
  private $usuario;




  /**
   * @ORM\PrePersist
   */
  public function onPrePersist()
  {
    $this->fecha = new \DateTime();
  }

  /**
   * Get id
   *
   * @return integer
   */
  public function getId()
  {
    return $this->id;
  }

  /**
   * Set ipPc
   *
   * @param string $ipPc
   *
   * @return Historial
   */
  public function setIpPc($ipPc)
  {
    $this->ipPc = $ipPc;

    return $this;
  }

  /**
   * Get ipPc
   *
   * @return string
   */
  public function getIpPc()
  {
    return $this->ipPc;
  }

  /**
   * Set fecha
   *
   * @param \DateTime $fecha
   *
   * @return Historial
   */
  public function setFecha($fecha)
  {
    $this->fecha = $fecha;

    return $this;
  }

  /**
   * Get fecha
   *
   * @return \DateTime
   */
  public function getFecha()
  {
    return $this->fecha;
  }

  /**
   * Set tipoAccion
   *
   * @param integer $tipoAccion
   *
   * @return Historial
   */
  public function setTipoAccion($tipoAccion)
  {
    $this->tipoAccion = $tipoAccion;

    return $this;
  }

  /**
   * Get tipoAccion
   *
   * @return integer
   */
  public function getTipoAccion()
  {
    return $this->tipoAccion;
  }

  /**
   * Set datosMod
   *
   * @param string $datosMod
   *
   * @return Historial
   */
  public function setDatosMod($datosMod)
  {
    $this->datosMod = $datosMod;

    return $this;
  }

  /**
   * Get datosMod
   *
   * @return string
   */
  public function getDatosMod()
  {
    return $this->datosMod;
  }

  /**
   * Set tablaImplicada
   *
   * @param string $tablaImplicada
   *
   * @return Historial
   */
  public function setTablaImplicada($tablaImplicada)
  {
    $this->tablaImplicada = $tablaImplicada;

    return $this;
  }

  /**
   * Get tablaImplicada
   *
   * @return string
   */
  public function getTablaImplicada()
  {
    return $this->tablaImplicada;
  }

  /**
   * Set usuario
   *
   * @param \AppBundle\Entity\Administracion\Usuario $usuario
   *
   * @return Historial
   */
  public function setUsuario(\AppBundle\Entity\Administracion\Usuario $usuario = null)
  {
    $this->usuario = $usuario;

    return $this;
  }

  /**
   * Get usuario
   *
   * @return \AppBundle\Entity\Administracion\Usuario
   */
  public function getUsuario()
  {
    return $this->usuario;
  }

  public function getTipoAccionString() {
    $variable=$this->getTipoAccion();
    switch($variable){
      case 1: return 'Lectura';
      case 2: return 'Agregar';
      case 3: return 'Modificar';
      case 4: return 'Borrar';
      case 5: return 'Autenticaci??n';
    }
  }

  public function getDatosModString() {
    $camp = '';
    if($this->tipoAccion === 3) {
      $un = unserialize($this->getDatosMod());
      if($un) {
        foreach($un as $key => $va) {
            $camp .= $key.':  '.$va[1]."\n";
        }
      }
    }
    else
      $camp = $this->getDatosMod();

    return $camp;
  }

  public function getTablaImplicadaString() {
    $array = explode('-', $this->tablaImplicada);

//    return $this->tablaImplicada;
    return $array[0];
  }
}
