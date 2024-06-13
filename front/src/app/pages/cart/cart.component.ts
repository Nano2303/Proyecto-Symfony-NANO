import { HttpClient } from '@angular/common/http';
import { Component, OnDestroy, OnInit } from '@angular/core';
import { Cart, CartItem } from 'src/app/models/cart.model';
import { CartService } from 'src/app/services/cart.service';
import { Subscription, forkJoin } from 'rxjs';
import { HttpHeaders } from '@angular/common/http';
import { ICreateOrderRequest, IPayPalConfig } from 'ngx-paypal';
import { environment } from 'src/environments/environment.prod';

@Component({
  selector: 'app-cart',
  templateUrl: './cart.component.html',
  styleUrls: ['./cart.component.scss']
})
export class CartComponent implements OnInit, OnDestroy {
  cart: Cart = { items: [] };
  displayedColumns: string[] = [
    'product',
    'name',
    'price',
    'quantity',
    'total',
    'action',
  ];
  apiUrl=environment.apiUrl;
  dataSource: CartItem[] = [];
  cartSubscription: Subscription | undefined;
  public payPalConfig?: IPayPalConfig;
  estadoSesion = false;

  constructor(private cartService: CartService, private http: HttpClient) { }

  ngOnInit(): void {
    this.estadoSesion = this.isLoggedIn();
    this.initConfig();
    this.cartSubscription = this.cartService.cart.subscribe((_cart: Cart) => {
      this.cart = _cart;
      this.dataSource = _cart.items;
     
    });
  }

  private initConfig(): void {
    this.payPalConfig = {
      currency: 'EUR',
      clientId: 'EGIEiTDPLtT3tifArVorAkXGidrpwDpsA0-9O4uiSZaQF1YfWmcM4aofe9P66vLy7O1iALKYwBdxhT4g',
      createOrderOnClient: (data) => <ICreateOrderRequest>{
        intent: 'CAPTURE',
        purchase_units: this.getPurchaseUnits()
      },
      onApprove: (data, actions) => {
        actions.order.capture().then((details: any) => {
          console.log('Transaction completed:', details);
          this.onCheckout(); // Llamada a onCheckout después de que la transacción sea aprobada
          this.onClearCart();
        });
      },
      onError: err => {
        console.error('PayPal error:', err);
      }
    };
  }

  private getPurchaseUnits() {
    const items = this.cart.items.map(item => ({
      name: item.name,
      quantity: item.quantity.toString(),
      unit_amount: {
        currency_code: 'USD',
        value: item.price.toFixed(2)
      }
    }));

    const total = this.getTotal(this.cart.items).toFixed(2);

    return [{
      amount: {
        currency_code: 'USD',
        value: total,
        breakdown: {
          item_total: {
            currency_code: 'USD',
            value: total
          }
        }
      },
      items: items
    }];
  }

  getTotal(items: CartItem[]): number {
    return this.cartService.getTotal(items);
  }

  onAddQuantity(item: CartItem): void {
    this.cartService.addToCart(item);
  }

  onRemoveFromCart(item: CartItem): void {
    this.cartService.removeFromCart(item);
  }

  onRemoveQuantity(item: CartItem): void {
    this.cartService.removeQuantity(item);
  }

  onClearCart(): void {
    this.cartService.clearCart();
  }

  onCheckout(): void {
    const addProductObservables = this.cart.items.map(item => {
      return this.http.post(`${this.apiUrl}/carrito/agregar-producto`, {
        productos_id: item.id,
        cantidad: item.quantity
      }, {
        headers: new HttpHeaders({
          'Content-Type': 'application/json'
        }),
        withCredentials: true // Importante para enviar cookies/sesión
      });
    });

    // Usa forkJoin para esperar a que todas las llamadas a agregar producto se completen
    forkJoin(addProductObservables).subscribe(
      responses => {
        console.log('Productos añadidos al carrito:', responses);

        // Llama al endpoint para crear la orden
        this.http.post(`${this.apiUrl}/crear-orden`, null, {
          headers: new HttpHeaders({
            'Content-Type': 'application/json'
          }),
          withCredentials: true, // Asegúrate de enviar credenciales
          responseType: 'text' // Espera una respuesta en texto plano
        })
        .subscribe(
          (response: any) => {
            console.log('Orden creada:', response);

            // Redirige a la página de confirmación de pago o realiza otra acción necesaria
            // Ejemplo de redirección:
          },
          error => {
            console.error('Error al crear la orden:', error);
          }
        );
      },
      error => {
        console.error('Error al añadir productos al carrito:', error);
      }
    );
  }

  isLoggedIn(): boolean {
    return localStorage.getItem('user_role') !== null;
  }

  ngOnDestroy() {
    if (this.cartSubscription) {
      this.cartSubscription.unsubscribe();
    }
  }
}
