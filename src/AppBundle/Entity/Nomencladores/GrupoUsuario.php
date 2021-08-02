<?php

namespace AppBundle\Entity\Nomencladores;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAsset;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * GrupoUsuario
 *
 * @ORM\Table(name="grupo_usuario")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\GrupoUsuarioRepository")
 */
class GrupoUsuario
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
   * @var string
   *
   * @ORM\Column(type="string",length=2147483647, name="nombre")
   * @Assert\NotBlank( message = "El campo no puede ser vacio")
   * @Assert\Type(type="string")
   */
  private $nombre;

  /**
   * @var string
   *
   * @ORM\Column(type="string",length=2147483647, name="descripcion", nullable=true)
   * @Assert\Type(type="string")
   */
  private $descripcion;

  /**
   * @ORM\ManyToMany(targetEntity="Roles", inversedBy="grupos")
   * @ORM\JoinTable(name="grupo_has_roles",
   *     joinColumns={@ORM\JoinColumn(name="grupo_id", referencedColumnName="id")},
   *     inverseJoinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="id")}
   * )
   *
   * @var ArrayCollection $roles
   */
  private $roles;

  /**
   * @var \AppBundle\Entity\Administracion\Usuario
   *
   * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Administracion\Usuario", mappedBy="grupos")
   */
  private $usuario;


  /**
   * Constructor
   */
  public function __construct()
  {
    $this->roles = new ArrayCollection();
    $this->usuario = new ArrayCollection();
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
   * Set nombre
   *
   * @param string $nombre
   *
   * @return GrupoUsuario
   */
  public function setNombre($nombre)
  {
    $this->nombre = $nombre;

    return $this;
  }

  /**
   * Get nombre
   *
   * @return string
   */
  public function getNombre()
  {
    return $this->nombre;
  }

  /**
   * Set descripcion
   *
   * @param string $descripcion
   *
   * @return GrupoUsuario
   */
  public function setDescripcion($descripcion)
  {
    $this->descripcion = $descripcion;

    return $this;
  }

  /**
   * Get descripcion
   *
   * @return string
   */
  public function getDescripcion()
  {
    return $this->descripcion;
  }

  /**
   * Add role
   *
   * @param \AppBundle\Entity\Nomencladores\Roles $role
   *
   * @return GrupoUsuario
   */
  public function addRole(\AppBundle\Entity\Nomencladores\Roles $role)
  {
    $this->roles[] = $role;

    return $this;
  }

  /**
   * Remove role
   *
   * @param \AppBundle\Entity\Nomencladores\Roles $role
   */
  public function removeRole(\AppBundle\Entity\Nomencladores\Roles $role)
  {
    $this->roles->removeElement($role);
  }

  /**
   * Get roles
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getRoles()
  {
    return $this->roles;
  }

  /**
   * Add usuario
   *
   * @param \AppBundle\Entity\Administracion\Usuario $usuario
   *
   * @return GrupoUsuario
   */
  public function addUsuario(\AppBundle\Entity\Administracion\Usuario $usuario)
  {
    $this->usuario[] = $usuario;

    return $this;
  }

  /**
   * Remove usuario
   *
   * @param \AppBundle\Entity\Administracion\Usuario $usuario
   */
  public function removeUsuario(\AppBundle\Entity\Administracion\Usuario $usuario)
  {
    $this->usuario->removeElement($usuario);
  }

  /**
   * Get usuario
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getUsuario()
  {
    return $this->usuario;
  }

    public function getRolesString()
    {
        $diag = '';
        for($i = 0; $i < count($this->roles); $i++) {
            if ($i < count($this->roles) - 1) {
                $diag .= $this->roles[$i]->getRol()."\n";
            }
            else
                $diag .= $this->roles[$i]->getRol();
        }

        return $diag;
    }
}
