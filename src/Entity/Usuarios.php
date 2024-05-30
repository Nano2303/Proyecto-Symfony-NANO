<?php

namespace App\Entity;

use App\Repository\UsuariosRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UsuariosRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
class Usuarios implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 15)]
    private ?string $telefono = null;

    /**
     * @var Collection<int, Ordenes>
     */
    #[ORM\OneToMany(targetEntity: Ordenes::class, mappedBy: 'usuarios', orphanRemoval: true)]
    private Collection $ordenes;

    /**
     * @var Collection<int, CarritoCompras>
     */
    #[ORM\OneToMany(targetEntity: CarritoCompras::class, mappedBy: 'usuarios', orphanRemoval: true)]
    private Collection $carrito_compras;

    /**
     * @var Collection<int, Direcciones>
     */
    #[ORM\OneToMany(targetEntity: Direcciones::class, mappedBy: 'usuarios', orphanRemoval: true , cascade:["persist"])]
    private Collection $direcciones;

    #[ORM\Column(length: 50)]
    private ?string $nombre = null;

    public function __construct()
    {
        $this->ordenes = new ArrayCollection();
        $this->carrito_compras = new ArrayCollection();
        $this->direcciones = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getTelefono(): ?string
    {
        return $this->telefono;
    }

    public function setTelefono(string $telefono): static
    {
        $this->telefono = $telefono;

        return $this;
    }

    /**
     * @return Collection<int, Ordenes>
     */
    public function getOrdenes(): Collection
    {
        return $this->ordenes;
    }

    public function addOrdene(Ordenes $ordene): static
    {
        if (!$this->ordenes->contains($ordene)) {
            $this->ordenes->add($ordene);
            $ordene->setUsuarios($this);
        }

        return $this;
    }

    public function removeOrdene(Ordenes $ordene): static
    {
        if ($this->ordenes->removeElement($ordene)) {
            // set the owning side to null (unless already changed)
            if ($ordene->getUsuarios() === $this) {
                $ordene->setUsuarios(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, CarritoCompras>
     */
    public function getCarritoCompras(): Collection
    {
        return $this->carrito_compras;
    }

    public function addCarritoCompra(CarritoCompras $carritoCompra): static
    {
        if (!$this->carrito_compras->contains($carritoCompra)) {
            $this->carrito_compras->add($carritoCompra);
            $carritoCompra->setUsuarios($this);
        }

        return $this;
    }

    public function removeCarritoCompra(CarritoCompras $carritoCompra): static
    {
        if ($this->carrito_compras->removeElement($carritoCompra)) {
            // set the owning side to null (unless already changed)
            if ($carritoCompra->getUsuarios() === $this) {
                $carritoCompra->setUsuarios(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Direcciones>
     */
    public function getDirecciones(): Collection
    {
        return $this->direcciones;
    }

    public function addDireccione(Direcciones $direccione): static
    {
        if (!$this->direcciones->contains($direccione)) {
            $this->direcciones->add($direccione);
            $direccione->setUsuarios($this);
        }

        return $this;
    }

    public function removeDireccione(Direcciones $direccione): static
    {
        if ($this->direcciones->removeElement($direccione)) {
            // set the owning side to null (unless already changed)
            if ($direccione->getUsuarios() === $this) {
                $direccione->setUsuarios(null);
            }
        }

        return $this;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): static
    {
        $this->nombre = $nombre;

        return $this;
    }
}
