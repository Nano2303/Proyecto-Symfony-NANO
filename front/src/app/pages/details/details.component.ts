import { Component, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { CartService } from 'src/app/services/cart.service';
import { StoreService } from 'src/app/services/store.service';
import { Product } from 'src/app/models/product.model';

@Component({
  selector: 'app-details',
  templateUrl: './details.component.html',
  styleUrls: ['./details.component.scss']
})
export class DetailsComponent implements OnInit {
  product: Product | undefined;
  selectedSize: string = 'M';

  constructor(
    private route: ActivatedRoute,
    private router: Router,
    private cartService: CartService,
    private storeService: StoreService // Inyecta el servicio de productos
  ) {}

  ngOnInit(): void {
    const navigation = this.router.getCurrentNavigation();
    console.log('Navigation state:', navigation?.extras?.state); // Verificar estado de navegación
    if (navigation?.extras?.state?.['product']) {
      this.product = navigation.extras.state['product'];
      console.log('Product:', this.product); // Verificar datos del producto
    } else {
      // Handle the case where product data is not available
      const productId = this.route.snapshot.paramMap.get('id');
      console.log('Product ID:', productId); // Verificar ID del producto
      // Fetch product details from your API using productId if needed
      if (productId) {
        this.storeService.getAllProducts().subscribe((products) => {
          this.product = products.find((p) => p.id.toString() === productId);
          console.log('Fetched Product:', this.product); // Verificar datos del producto obtenidos de la API
        });
      }
    }
  }

  onAddToCart(): void {
    if (this.product) {
      const cartItem = {
        id: this.product.id,
        name: this.product.title,
        price: this.product.price,
        quantity: 1,
        product: this.product.image
      };
      this.cartService.addToCart(cartItem);
    }
  }
}
