import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { map, tap } from 'rxjs/operators';
import { Product } from '../models/product.model';

const STORE_BASE_URL = 'http://localhost:8000/lista_productos';

@Injectable({
  providedIn: 'root',
})
export class StoreService {
  constructor(private httpClient: HttpClient) {}

  getAllProducts(): Observable<Array<Product>> {
    return this.httpClient.get<Array<any>>(STORE_BASE_URL).pipe(
      tap((products) => {
        console.log('Raw products:', products); // Log the raw JSON response
      }),
      map((products) => products.map((product) => this.transformProduct(product)))
    );
  }

  private transformProduct(product: any): Product {
    return {
      id: product.id,
      title: product.nombre,
      price: product.precio,
      category: product.categorias_id.toString(), // Assuming category is an ID, convert to string or map to a category name if needed
      description: product.descripcion,
      image: `./assets/img/${product.src}` // Ensure this path is correct relative to your assets folder
    };
  }
}
