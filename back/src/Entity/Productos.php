<?php

namespace App\Entity;

use App\Repository\ProductosRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductosRepository::class)]
class Productos
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nombre = null;

    #[ORM\Column(length: 255)]
    private ?string $descripcion = null;

    #[ORM\Column]
    private ?float $precio = null;

    #[ORM\Column(length: 5)]
    private ?string $talla = null;

    #[ORM\Column(length: 20)]
    private ?string $color = null;

    #[ORM\Column]
    private ?int $cantidad_inventario = null;

    /**
     * @var Collection<int, DetallesOrden>
     */
    #[ORM\OneToMany(targetEntity: DetallesOrden::class, mappedBy: 'productos', orphanRemoval: true)]
    private Collection $detalles_orden;

    /**
     * @var Collection<int, ProductosCarrito>
     */
    #[ORM\OneToMany(targetEntity: ProductosCarrito::class, mappedBy: 'productos', orphanRemoval: true)]
    private Collection $productos_carrito;

    #[ORM\ManyToOne(inversedBy: 'productos')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Categorias $categorias = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $src = null;

    public function __construct()
    {
        $this->detalles_orden = new ArrayCollection();
        $this->productos_carrito = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    public function setDescripcion(string $descripcion): static
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    public function getPrecio(): ?float
    {
        return $this->precio;
    }

    public function setPrecio(float $precio): static
    {
        $this->precio = $precio;

        return $this;
    }

    public function getTalla(): ?string
    {
        return $this->talla;
    }

    public function setTalla(string $talla): static
    {
        $this->talla = $talla;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(string $color): static
    {
        $this->color = $color;

        return $this;
    }

    public function getCantidadInventario(): ?int
    {
        return $this->cantidad_inventario;
    }

    public function setCantidadInventario(int $cantidad_inventario): static
    {
        $this->cantidad_inventario = $cantidad_inventario;

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
            $detallesOrden->setProductos($this);
        }

        return $this;
    }

    public function removeDetallesOrden(DetallesOrden $detallesOrden): static
    {
        if ($this->detalles_orden->removeElement($detallesOrden)) {
            // set the owning side to null (unless already changed)
            if ($detallesOrden->getProductos() === $this) {
                $detallesOrden->setProductos(null);
            }
        }

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
            $productosCarrito->setProductos($this);
        }

        return $this;
    }

    public function removeProductosCarrito(ProductosCarrito $productosCarrito): static
    {
        if ($this->productos_carrito->removeElement($productosCarrito)) {
            // set the owning side to null (unless already changed)
            if ($productosCarrito->getProductos() === $this) {
                $productosCarrito->setProductos(null);
            }
        }

        return $this;
    }

    public function getCategorias(): ?Categorias
    {
        return $this->categorias;
    }

    public function setCategorias(?Categorias $categorias): static
    {
        $this->categorias = $categorias;

        return $this;
    }

    public function getSrc(): ?string
    {
        return $this->src;
    }

    public function setSrc(string $src): static
    {
        $this->src = $src;

        return $this;
    }
}
