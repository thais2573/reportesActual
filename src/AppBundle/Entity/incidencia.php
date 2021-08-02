<?php
/**
 * Created by PhpStorm.
 * User: Lisandra
 * Date: 05/02/2019
 * Time: 15:20
 */

namespace AppBundle\Entity;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\incidenciaRepository")
 * @ORM\Table(name="incidencia")
 */
class incidencia
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;
    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     */
    private $tipo;
    /**
     * @ORM\Column(type="string")
     */
    private $user;
    /**
     * @ORM\Column(type="string",nullable=true)
     */
    private $dpto;
    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     */
    private $asunto;
    /**
     * @ORM\Column(type="string",nullable=true)
     */
    private $resumen;
    /**
     * @ORM\Column(type="datetime")
     */
    private $fecha;

    /**
     * @ORM\Column(type="string")
     */
    private $estado;
    /**
     * @ORM\Column(type="string",nullable=true)
     */
    private $estadoMovimiento;

    /**
     * @ORM\Column(type="string",nullable=true)
     */
    private $respuesta;

    /**
     * @ORM\Column(type="string",nullable=true)
     *
     */
    private $tipo_mov;

    /**
     * @ORM\Column(type="string")
     */
    private $tec_asignado;

    /**
     * @ORM\Column(type="string",nullable=true)
     */
    private $correo;
    /**
     * @ORM\Column(type="datetime",nullable=true)
     * @Assert\DateTime()
     */
    private $fechaA;

    /**
     * @ORM\Column(type="string",nullable=true)
     *
     */
    private $asesorio;

  /**
   * @ORM\Column(type="string",nullable=true)
   *
   */
  private $num_inventario;
  /**
   * @ORM\Column(type="string",nullable=true)
   *
   */
  private $idE;
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\inventario",cascade={"persist"})
     */
    private $inventario;

    /**
     * @return mixed
     */
    public function getCorreo()
    {
        return $this->correo;
    }

    /**
     * @param mixed $correo
     */
    public function setCorreo($correo)
    {
        $this->correo = $correo;
    }

    /**
     * @return mixed
     */
    public function getFechaA()
    {
        return $this->fechaA;
    }

    /**
     * @param mixed $fechaA
     */
    public function setFechaA($fechaA)
    {
        $this->fechaA = $fechaA;
    }


    /**
     * @return mixed
     */
    public function getTecAsignado()
    {
        return $this->tec_asignado;
    }

    /**
     * @param mixed $tec_asignado
     */
    public function setTecAsignado($tec_asignado)
    {
        $this->tec_asignado = $tec_asignado;
    }


    /**
     * @return mixed
     */
    public function getAsesorio()
    {
        return $this->asesorio;
    }

    /**
     * @param mixed $asesorio
     */
    public function setAsesorio($asesorio)
    {
        $this->asesorio = $asesorio;
    }


    /**
     * @return mixed
     */
    public function getTipoMov()
    {
        return $this->tipo_mov;
    }

    /**
     * @param mixed $tipo_mov
     */
    public function setTipoMov($tipo_mov)
    {
        $this->tipo_mov = $tipo_mov;
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
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user)
    {
        $this->user = $user;
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
    public function getAsunto()
    {
        return $this->asunto;
    }

    /**
     * @param mixed $asunto
     */
    public function setAsunto($asunto)
    {
        $this->asunto = $asunto;
    }

    /**
     * @return mixed
     */
    public function getResumen()
    {
        return $this->resumen;
    }

    /**
     * @param mixed $resumen
     */
    public function setResumen($resumen)
    {
        $this->resumen = $resumen;
    }

    /**
     * @return mixed
     */
    public function getFecha()
    {
        return $this->fecha;
    }

    /**
     * @param mixed $fecha
     */
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;
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
     * @return mixed
     */
    public function getRespuesta()
    {
        return $this->respuesta;
    }

    /**
     * @param mixed $respuesta
     */
    public function setRespuesta($respuesta)
    {
        $this->respuesta = $respuesta;
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
  public function getIdE()
  {
    return $this->idE;
  }

  /**
   * @param mixed $idE
   */
  public function setIdE($idE)
  {
    $this->idE = $idE;
  }

  public function __toString()
  {
    // TODO: Implement __toString() method.
    return $this->getEstado();
  }

  /**
   * @return mixed
   */
  public function getNumInventario()
  {
    return $this->num_inventario;
  }

  /**
   * @param mixed $num_inventario
   */
  public function setNumInventario($num_inventario)
  {
    $this->num_inventario = $num_inventario;
  }

    /**
     * @return mixed
     */
    public function getEstadoMovimiento()
    {
        return $this->estadoMovimiento;
    }

    /**
     * @param mixed $estadoMovimiento
     */
    public function setEstadoMovimiento($estadoMovimiento)
    {
        $this->estadoMovimiento = $estadoMovimiento;
    }


}