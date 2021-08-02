<?php

namespace AppBundle\Entity\Administracion;


use AppBundle\Entity\Nomencladores\GrupoUsuario;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAsset;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;

/**
 * Usuario
 *
 * @ORM\Table(name="usuario")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UsuarioRepository")
 * @DoctrineAsset\UniqueEntity(fields={"username"}, message="Ya existe un usuario con ese nombre de usuario.")
 */
class Usuario implements UserInterface
{
  /**
   * @var int
   *
   * @ORM\Column(name="id", type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   * @ORM\Column(type="integer")
   */
  private $id;
  /**
   * @var string $first_name
   *
   * @ORM\Column(name="first_name", type="string", nullable=false)
   * @Assert\Type(type="string")
   * @ORM\Column(type="string")
   */
  private $first_name;
  /**
   * @var string $last_name
   *
   * @ORM\Column(name="last_name", type="string", nullable=false)
   * @Assert\Type(type="string")
   * @ORM\Column(type="string")
   */
  private $last_name;

  /**
   * @var string $username
   *
   * @ORM\Column(name="username", type="string", nullable=false)
   * @Assert\Type(type="string")
   * @ORM\Column(type="string")
   */
  private $username;
  /**
   * @var string $email
   *
   * @ORM\Column(name="email", type="string", nullable=false)
   * @Assert\Type(type="string")
   * @ORM\Column(type="string")
   */
  private $email;
  /**
   * @var integer
   *
   * @ORM\Column(name="id_account", type="integer", nullable=false)
   * @Assert\Type(type="integer")
   * @ORM\Column(type="string")
   */
  private $idAccount;

  /**
   * @var integer
   *
   * @ORM\Column(name="estado", type="integer", nullable=false)
   * @ORM\Column(type="string")
   */
  private $estado;

  /**
   * @var \DateTime
   *
   * @ORM\Column(name="ultima_conexion", type="datetime", nullable=true)
   * @Assert\Type("\DateTime")
   * @ORM\Column(type="string")
   */
  private $ultimaConexion;

  /**
   * @var string $password
   *
   * @ORM\Column(name="password", type="string", nullable=true)
   * @Assert\Type(type="string")
   * @ORM\Column(type="string")
   */
  private $password;

  /**
   * @var string $nombre
   *
   * @ORM\Column(name="ci", type="string", nullable=false)
   * @Assert\Type(type="string")
   * @ORM\Column(type="string")
   */
  private $ci;

//  /**
//   * @Assert\NotBlank()
//   * @ORM\Column(name="rol", type="string")
//   * @ORM\Column(type="string")
//   */
//  private $rol;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Nomencladores\GrupoUsuario", inversedBy="usuario")
     * @ORM\JoinTable(name="usuario_has_grupo",
     *     joinColumns={@ORM\JoinColumn(name="usuario_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="grupo_id", referencedColumnName="id")}
     * )
     *
     * @var ArrayCollection $grupos
     */
    private $grupos;
  /**
   * @var \AppBundle\Entity\Administracion\Historial
   *
   * @ORM\OneToMany(targetEntity="AppBundle\Entity\Nomencladores\Historial", mappedBy="usuario")
   * @ORM\Column(type="string")
   */
  private $historial;

  /**
   * @ORM\Column(type="string")
   */
  private $plainPassword;
  /**
   * @ORM\Column(type="string")
   */
  private $consecutivo_solicitud;

  /**
   * Constructor
   */
  public function __construct()
  {
    $this->estado = 1;
      $this->grupos = new ArrayCollection();
    $this->historial = new ArrayCollection();
  }

  public function getRoles()
  {
      $roles = [];

      /** @var GrupoUsuario $grupo */
      foreach ($this->grupos as $grupo) {
          $rolG = $grupo->getRoles()->toArray();
          foreach ($rolG as $rol) {
              if(!in_array($rol, $roles))
                  $roles[] = $rol;
          }
      }

      return $roles;
  }

  public function eraseCredentials()
  {
    $this->plainPassword = null;
  }

  /**
   * @return string
   * Funcion necesaria para serializar la info del user en la session para en cada request comprobar q la este hacianedo el mismo usuario
   */
  public function serialize()
  {
    return serialize(array(
      $this->id,
      $this->username,
      $this->password
    ));
  }

  public function unserialize($serialized)
  {
    list (
      $this->id,
      $this->username,
      $this->password
      ) = unserialize($serialized);
  }

  public function getSalt()
  {
    return null;
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
   * @return string
   */
  public function getEmail()
  {
    return $this->email;
  }

  /**
   * @param string $email
   */
  public function setEmail($email)
  {
    $this->email = $email;
  }

  /**
   * Set username
   *
   * @param string $username
   *
   * @return Usuario
   */
  public function setUsername($username)
  {
    $this->username = $username;

    return $this;
  }

  /**
   * Get username
   *
   * @return string
   */
  public function getUsername()
  {
    return $this->username;
  }

  /**
   * Set idAccount
   *
   * @param integer $idAccount
   *
   * @return Usuario
   */
  public function setIdAccount($idAccount)
  {
    $this->idAccount = $idAccount;

    return $this;
  }

  /**
   * Get idAccount
   *
   * @return integer
   */
  public function getIdAccount()
  {
    return $this->idAccount;
  }

  /**
   * Set estado
   *
   * @param integer $estado
   *
   * @return Usuario
   */
  public function setEstado($estado)
  {
    $this->estado = $estado;

    return $this;
  }

  /**
   * Get estado
   *
   * @return integer
   */
  public function getEstado()
  {
    return $this->estado;
  }

  /**
   * Set ultimaConexion
   *
   * @param \DateTime $ultimaConexion
   *
   * @return Usuario
   */
  public function setUltimaConexion($ultimaConexion)
  {
    $this->ultimaConexion = $ultimaConexion;

    return $this;
  }

  /**
   * Get ultimaConexion
   *
   * @return \DateTime
   */
  public function getUltimaConexion()
  {
    return $this->ultimaConexion;
  }

  /**
   * @return Bool Whether the user is active or not
   */
  public function isActiveNow()
  {
    // Delay during wich the user will be considered as still active
    $delay = new \DateTime('2 minutes ago');

    return ($this->getUltimaConexion() > $delay);
  }

  /**
   * Set password
   *
   * @param string $password
   *
   * @return Usuario
   */
  public function setPassword($password)
  {
    $this->password = $password;

    return $this;
  }

  /**
   * Get password
   *
   * @return string
   */
  public function getPassword()
  {
    return $this->password;
  }


  /**
   * Add historial
   *
   * @param \AppBundle\Entity\Administracion\Historial $historial
   *
   * @return Usuario
   */
  public function addHistorial(\AppBundle\Entity\Administracion\Historial $historial)
  {
    $this->historial[] = $historial;

    return $this;
  }

//  /**
//   * @return mixed
//   */
//  public function getRol()
//  {
//    return $this->rol;
//  }
//
//  /**
//   * @param mixed $rol
//   */
//  public function setRol($rol)
//  {
//    $this->rol = $rol;
//  }

  /**
   * @return mixed
   */
  public function getPlainPassword()
  {
    return $this->plainPassword;
  }

  /**
   * @param mixed $plainPassword
   */
  public function setPlainPassword($plainPassword)
  {
    $this->plainPassword = $plainPassword;
    $this->password = null;
  }

  /**
   * Remove historial
   *
   * @param \AppBundle\Entity\Administracion\Historial $historial
   */
  public function removeHistorial(\AppBundle\Entity\Administracion\Historial $historial)
  {
    $this->historial->removeElement($historial);
  }

  /**
   * Get historial
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getHistorial()
  {
    return $this->historial;
  }

  /**
   * Set ci
   *
   * @param string $ci
   *
   * @return Usuario
   */
  public function setCi($ci)
  {
    $this->ci = $ci;

    return $this;
  }

  /**
   * Get ci
   *
   * @return string
   */
  public function getCi()
  {
    return $this->ci;
  }



  public function getEstString()
  {
    switch ($this->estado) {
      case 1:
        return 'Activo';
        break;
      case 2:
        return 'Inactivo';
        break;
    }
  }

  public function getEstadoString($est)
  {
    switch ($est) {
      case 1:
        return 'Activo';
        break;
      case 2:
        return 'Inactivo';
        break;
    }
  }

  /**
   * @return string
   */
  public function getFirstName()
  {
    return $this->first_name;
  }

  /**
   * @param string $first_name
   */
  public function setFirstName($first_name)
  {
    $this->first_name = $first_name;
  }

  /**
   * @return string
   */
  public function getLastName()
  {
    return $this->last_name;
  }

  /**
   * @param string $last_name
   */
  public function setLastName($last_name)
  {
    $this->last_name = $last_name;
  }
    public function __toString()
    {
        return $this->getFirstName().' '.$this->getLastName();
    }

  /**
   * @return mixed
   */
  public function getConsecutivoSolicitud()
  {
    return $this->consecutivo_solicitud;
  }

  /**
   * @param mixed $consecutivo_solicitud
   */
  public function setConsecutivoSolicitud($consecutivo_solicitud)
  {
    $this->consecutivo_solicitud = $consecutivo_solicitud;
  }

    /**
     * @return ArrayCollection
     */
    public function getGrupos()
    {
        return $this->grupos;
    }

    /**
     * @param ArrayCollection $grupos
     */
    public function setGrupos($grupos)
    {
        $this->grupos = $grupos;
    }
  //  public function __toString()
 //   {
//        return $this->username;
 //   }

}
