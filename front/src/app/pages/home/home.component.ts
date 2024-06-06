import { Component, OnDestroy, OnInit } from '@angular/core';
import { Subscription } from 'rxjs';
import { Product } from 'src/app/models/product.model';
import { CartService } from 'src/app/services/cart.service';
import { StoreService } from 'src/app/services/store.service';

const ROWS_HEIGHT: { [id: number]: number } = { 1: 400, 3: 335, 4: 350 };

@Component({
  selector: 'app-home',
  templateUrl: './home.component.html',
  styleUrls: ['./home.component.scss']
})
export class HomeComponent implements OnInit, OnDestroy {
  cols = 3;
  rowHeight: number = ROWS_HEIGHT[this.cols];
  products: Array<Product> | undefined;
  categories: Array<string> = [];
  count = '12';
  sort = 'desc';
  category: string | undefined;
  productsSubscription: Subscription | undefined;

  constructor(
    private cartService: CartService,
    private storeService: StoreService
  ) {}

  ngOnInit(): void {
    this.getProducts();
  }

  onColumnsCountChange(colsNum: number): void {
    this.cols = colsNum;
    this.rowHeight = ROWS_HEIGHT[colsNum];
  }

  onItemsCountChange(count: number): void {
    this.count = count.toString();
    this.getProducts();
  }

  onSortChange(newSort: string): void {
    this.sort = newSort;
    this.getProducts();
  }

  onShowCategory(newCategory: string): void {
    this.category = newCategory;
    this.getProducts();
  }

  getProducts(): void {
    this.productsSubscription = this.storeService
      .getAllProducts()
      .subscribe((_products) => {
        this.products = this.filterAndSortProducts(_products);
        this.categories = this.getCategories(_products);
      });
  }

  filterAndSortProducts(products: Array<Product>): Array<Product> {
    let filteredProducts = products;
    if (this.category) {
      filteredProducts = filteredProducts.filter(product => product.category === this.category);
    }
    if (this.sort === 'desc') {
      filteredProducts = filteredProducts.sort((a, b) => b.price - a.price);
    } else {
      filteredProducts = filteredProducts.sort((a, b) => a.price - b.price);
    }
    return filteredProducts.slice(0, parseInt(this.count, 10));
  }

  getCategories(products: Array<Product>): Array<string> {
    const categories = products.map(product => product.category);
    return Array.from(new Set(categories));
  }

  onAddToCart(product: Product): void {
    this.cartService.addToCart({
      product: product.image,
      name: product.title,
      price: product.price,
      quantity: 1,
      id: product.id,
    });
  }

  ngOnDestroy(): void {
    if (this.productsSubscription) {
      this.productsSubscription.unsubscribe();
    }
  }
}
