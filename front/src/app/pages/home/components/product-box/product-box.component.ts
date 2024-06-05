import { Component, EventEmitter, Input, Output } from '@angular/core';
import { Router } from '@angular/router';
import { Product } from 'src/app/models/product.model';

@Component({
  selector: '[app-product-box]',
  templateUrl: './product-box.component.html',
  styleUrls: ['./product-box-component.scss']
})
export class ProductBoxComponent {
  @Input() fullWidthMode = false;
  @Input() product: Product | undefined;
  @Output() addToCart = new EventEmitter<Product>();

  constructor(private router: Router) {}

  onAddToCart(): void {
    this.addToCart.emit(this.product);
  }

  onViewDetails(): void {
    if (this.product) {
      this.router.navigate(['/details', this.product.id], { state: { product: this.product } });
    }
  }
}
