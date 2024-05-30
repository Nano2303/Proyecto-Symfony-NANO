<?php

namespace App\Entity;

use App\Repository\OrdenesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrdenesRepository::class)]
class Ordenes
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    private ?string $fecha = null;

    #[ORM\Column]
    private ?float $total = null;

    #[ORM\ManyToOne(inversedBy: 'ordenes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Usuarios $usuarios = null;

    /**
     * @var Collection<int, DetallesOrden>
     */
    #[ORM\OneToMany(targetEntity: DetallesOrden::class, mappedBy: 'ordenes', orphanRemoval: true)]
    private Collection $detalles_orden;

    #[ORM\Column(length: 255)]
    private ?string $direccion_envio = null;

    #[ORM\OneToOne(inversedBy: 'ordenes', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Pagos $pagos = null;

    public function __construct()
    {
        $this->detalles_orden = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFecha(): ?string
    {
        return $this->fecha;
    }

    public function setFecha(string $fecha): static
    {
        $this->fecha = $fecha;

        return $this;
    }

    public function getTotal(): ?float
    {
        return $this->total;
    }

    public function setTotal(float $total): static
    {
        $this->total = $total;

        return $this;
    }

    public function getUsuarios(): ?Usuarios
    {
        return $this->usuarios;
    }

    public function setUsuarios(?Usuarios $usuarios): static
    {
        $this->usuarios = $usuarios;

        return $this;
    }

    /**
     * @return Collection<int, DetallesOrden>
     */
    public function getDetallesOrden(): Collection
    {
        return $this->detalles_orden;
    }

    public function addDetallesOrden(DetallesOrden $detallesOrden): static
    {
        if (!$this->detalles_orden->contains($detallesOrden)) {
            $this->detalles_orden->add($detallesOrden);
            $detallesOrden->setOrdenes($this);
        }

        return $this;
    }

    public function removeDetallesOrden(DetallesOrden $detallesOrden): static
    {
        if ($this->detalles_orden->removeElement($detallesOrden)) {
            // set the owning side to null (unless already changed)
            if ($detallesOrden->getOrdenes() === $this) {
                $detallesOrden->setOrdenes(null);
            }
        }

        return $this;
    }

    public function getDireccionEnvio(): ?string
    {
        return $this->direccion_envio;
    }

    public function setDireccionEnvio(string $direccion_envio): static
    {
        $this->direccion_envio = $direccion_envio;

        return $this;
    }

    public function getPagos(): ?Pagos
    {
        return $this->pagos;
    }

    public function setPagos(Pagos $pagos): static
    {
        $this->pagos = $pagos;

        return $this;
    }
}
