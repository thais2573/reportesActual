<?php
/**
 * Created by PhpStorm.
 * User: thais
 * Date: 17/10/19
 * Time: 11:28
 */

namespace AppBundle\Entity;


use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="deudaRepository")
 * @ORM\Table(name="deuda")
 */
class deuda
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */private $id;
    /**
     * @ORM\Column(type="string")
     */private $tipoComponente;
    /**
     * @var componente
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\equipo", inversedBy="deuda")
     * @ORM\JoinColumn(name="chasis_id", referencedColumnName="id")
     */
    private $cpu;
    /**
     * @var componente
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\inventario", inversedBy="deuda")
     * @ORM\JoinColumn(name="inventario_id", referencedColumnName="id")
     */
    private $estacion;
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

    /**
     * @return componente
     */
    public function getCpu()
    {
        return $this->cpu;
    }

    /**
     * @param componente $cpu
     */
    public function setCpu($cpu)
    {
        $this->cpu = $cpu;
    }

    /**
     * @return componente
     */
    public function getEstacion()
    {
        return $this->estacion;
    }

    /**
     * @param componente $estacion
     */
    public function setEstacion($estacion)
    {
        $this->estacion = $estacion;
    }

}