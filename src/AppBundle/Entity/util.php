<?php
/**
 * Created by PhpStorm.
 * User: thais
 * Date: 12/09/19
 * Time: 14:56
 */

namespace AppBundle\Entity;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="util")
 */
class util
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */private $id;
    /**
     * @ORM\Column(type="string")
     */
    private $idUH;
    /**
     * @ORM\Column(type="string")
     */private $descripcion;
    /**
     * @ORM\Column(type="string")
     */private $fechaAlta;
    /**
     * @ORM\Column(type="string")
     */private $activo;
    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\utilDetalles", mappedBy="util", cascade={"persist","remove"})
     */
    private $detalles;

    public function __construct() {
        $this->detalles = new ArrayCollection();
    }
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getIdUH()
    {
        return $this->idUH;
    }

    /**
     * @param mixed $idUH
     */
    public function setIdUH($idUH)
    {
        $this->idUH = $idUH;
    }

    /**
     * @return mixed
     */
    public function getDetalles()
    {
        return $this->detalles;
    }

    /**
     * @param mixed $detalles
     */
    public function setDetalles($detalles)
    {
        $this->detalles = $detalles;
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
    public function getFechaAlta()
    {
        return $this->fechaAlta;
    }

    /**
     * @param mixed $fechaAlta
     */
    public function setFechaAlta($fechaAlta)
    {
        $this->fechaAlta = $fechaAlta;
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

    public function __toString()
    {
       return $this->descripcion;
    }


}