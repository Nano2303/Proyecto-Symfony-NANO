<?php

namespace App\Entity;

use App\Repository\CarritoComprasRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CarritoComprasRepository::class)]
class CarritoCompras
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?float $total = null;

    #[ORM\ManyToOne(inversedBy: 'carrito_compras')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Usuarios $usuarios = null;

    /**
     * @var Collection<int, ProductosCarrito>
     */
    #[ORM\OneToMany(targetEntity: ProductosCarrito::class, mappedBy: 'carritoCompras', orphanRemoval: true)]
    private Collection $productos_carrito;

    public function __construct()
    {
        $this->productos_carrito = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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
     * @return Collection<int, ProductosCarrito>
     */
    public function getProductosCarrito(): Collection
    {
        return $this->productos_carrito;
    }

    public function addProductosCarrito(ProductosCarrito $productosCarrito): static
    {
        if (!$this->productos_carrito->contains($productosCarrito)) {
            $this->productos_carrito->add($productosCarrito);
            $productosCarrito->setCarritoCompras($this);
        }

        return $this;
    }

    public function removeProductosCarrito(ProductosCarrito $productosCarrito): static
    {
        if ($this->productos_carrito->removeElement($productosCarrito)) {
            // set the owning side to null (unless already changed)
            if ($productosCarrito->getCarritoCompras() === $this) {
                $productosCarrito->setCarritoCompras(null);
            }
        }

        return $this;
    }
}
