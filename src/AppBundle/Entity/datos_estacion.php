<?php
/**
 * Created by PhpStorm.
 * User: Thais
 * Date: 6/20/2019
 * Time: 1:59 PM
 */

namespace AppBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;

/**
 * @ORM\Table(name="datos_estacion")
 * @ORM\Entity(repositoryClass="datos_estacionRepository")
 */
class datos_estacion
{
  /**
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   * @ORM\Column(type="integer")
   */
  private $id;

  /**
   * @ORM\Column(type="string")
   * @Assert\NotBlank(message="Debe seleccionar un responsable")
   */
  private $responsable;
  /**
   * @ORM\Column(type="string")
   * @Assert\NotBlank(message="Debe establecer una contraseña para el setup")
   */
  private $passSetup;
  /**
   * @ORM\Column(type="string")
   * @Assert\NotBlank( )
   * @Assert\Ip(message="Formato incorrecto de IP")
   */
  private $ip;
  /**
   * @ORM\Column(type="string")
   * @Assert\NotBlank(message="Debe establecer un nombre de red")
   *
   */
  private $nombreRed;
  /**
   * @ORM\Column(type="string",nullable=true)
   */
  private $antivirus;
  /**
   * @ORM\Column(type="string",nullable=true)
   *
   */
  private $tecnico;
  /**
   * @ORM\Column(type="string",nullable=true)
   *
   */
  private $jefeInformatica;

  /**
   * @return mixed
   */
  public function getResponsable()
  {
    return $this->responsable;
  }

  /**
   * @param mixed $responsable
   */
  public function setResponsable($responsable)
  {
    $this->responsable = $responsable;
  }

  /**
   * @return mixed
   */
  public function getPassSetup()
  {
    return $this->passSetup;
  }

  /**
   * @param mixed $passSetup
   */
  public function setPassSetup($passSetup)
  {
    $this->passSetup = $passSetup;
  }

  /**
   * @return mixed
   */
  public function getIp()
  {
    return $this->ip;
  }

  /**
   * @param mixed $ip
   */
  public function setIp($ip)
  {
    $this->ip = $ip;
  }

  /**
   * @return mixed
   */
  public function getNombreRed()
  {
    return $this->nombreRed;
  }

  /**
   * @param mixed $nombreRed
   */
  public function setNombreRed($nombreRed)
  {
    $this->nombreRed = $nombreRed;
  }

  /**
   * @return mixed
   */
  public function getAntivirus()
  {
    return $this->antivirus;
  }

  /**
   * @param mixed $antivirus
   */
  public function setAntivirus($antivirus)
  {
    $this->antivirus = $antivirus;
  }

  /**
   * @return mixed
   */
  public function getTecnico()
  {
    return $this->tecnico;
  }

  /**
   * @param mixed $tecnico
   */
  public function setTecnico($tecnico)
  {
    $this->tecnico = $tecnico;
  }

  /**
   * @return mixed
   */
  public function getJefeInformatica()
  {
    return $this->jefeInformatica;
  }

  /**
   * @param mixed $jefeInformatica
   */
  public function setJefeInformatica($jefeInformatica)
  {
    $this->jefeInformatica = $jefeInformatica;
  }

  /**
   * @return mixed
   */
  public function getId()
  {
    return $this->id;
  }

}