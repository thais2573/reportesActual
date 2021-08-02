<?php
/**
 * Created by PhpStorm.
 * User: thais
 * Date: 12/09/19
 * Time: 15:23
 */

namespace AppBundle\Entity;


use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="utilDetallesRepository")
 * @ORM\Table(name="util_detalles")
 */
class utilDetalles
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */private $id;
/**
     * @ORM\Column(type="string")
     */private $codigoCosto;
    /**
     * @ORM\Column(type="string")
     */private $codigoArea;
    /**
     * @ORM\Column(type="string")
     */private $nombreCosto;
    /**
     * @ORM\Column(type="string")
     */private $nombreArea;
    /**
     * @ORM\Column(type="string")
     */private $cantidad;



    /**
     * @var util
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\util",inversedBy="detalles")
     * @ORM\JoinColumn(name="util_id",referencedColumnName="id",unique=false)
     */
    private $util;
    /**
    /**
     * @return mixed
     */
    public function getIdUtil()
    {
        return $this->idUtil;
    }

    /**
     * @param mixed $idUtil
     */
    public function setIdUtil($idUtil)
    {
        $this->idUtil = $idUtil;
    }

    /**
     * @return mixed
     */
    public function getCodigoCosto()
    {
        return $this->codigoCosto;
    }

    /**
     * @param mixed $codigoCosto
     */
    public function setCodigoCosto($codigoCosto)
    {
        $this->codigoCosto = $codigoCosto;
    }

    /**
     * @return mixed
     */
    public function getCodigoArea()
    {
        return $this->codigoArea;
    }

    /**
     * @param mixed $codigoArea
     */
    public function setCodigoArea($codigoArea)
    {
        $this->codigoArea = $codigoArea;
    }

    /**
     * @return mixed
     */
    public function getNombreCosto()
    {
        return $this->nombreCosto;
    }

    /**
     * @param mixed $nombreCosto
     */
    public function setNombreCosto($nombreCosto)
    {
        $this->nombreCosto = $nombreCosto;
    }

    /**
     * @return mixed
     */
    public function getNombreArea()
    {
        return $this->nombreArea;
    }

    /**
     * @param mixed $nombreArea
     */
    public function setNombreArea($nombreArea)
    {
        $this->nombreArea = $nombreArea;
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
     * @return util
     */
    public function getUtil()
    {
        return $this->util;
    }

    /**
     * @param util $util
     */
    public function setUtil($util)
    {
        $this->util = $util;
    }

}