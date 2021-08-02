<?php
/**
 * Created by PhpStorm.
 * User: thais
 * Date: 19/09/19
 * Time: 10:21
 */

namespace AppBundle\Entity;


use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="tipo_componente")
 */
class tipoComponente
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
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

}