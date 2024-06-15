import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Component, OnDestroy, OnInit } from '@angular/core';
import { Cart, CartItem } from 'src/app/models/cart.model';
import { CartService } from 'src/app/services/cart.service';
import { Subscription, forkJoin } from 'rxjs';
import { ICreateOrderRequest, IPayPalConfig } from 'ngx-paypal';
import { environment } from 'src/environments/environment.prod';
import { Router } from '@angular/router';
import Swal from 'sweetalert2';
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

  constructor(private cartService: CartService, private http: HttpClient,private router: Router) { }

  ngOnInit(): void {
    this.estadoSesion = this.isLoggedIn();
    this.initConfig();
    this.loadCart(); // Cargar carrito desde localStorage
    this.cartSubscription = this.cartService.cart.subscribe((_cart: Cart) => {
      this.cart = _cart;
      this.dataSource = _cart.items;
    });
  }

  private loadCart(): void {
    const savedCart = localStorage.getItem('cart');
    if (savedCart) {
      this.cart = JSON.parse(savedCart);
      this.dataSource = this.cart.items;
    } else {
      this.cartSubscription = this.cartService.cart.subscribe((_cart: Cart) => {
        this.cart = _cart;
        this.dataSource = _cart.items;
      });
    }
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
          
           Swal.fire({
            title: 'Pedido realizado',
            text: 'Tu pedido ha sido realizado con éxito',
            icon: 'success',
            confirmButtonText: 'Aceptar'
          }).then(() => {
            
            this.router.navigate(['/orders']);
          });

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

    forkJoin(addProductObservables).subscribe(
      responses => {
        console.log('Productos añadidos al carrito:', responses);

        this.http.post(`${this.apiUrl}/crear-orden`, null, {
          headers: new HttpHeaders({
            'Content-Type': 'application/json'
          }),
          withCredentials: true,
          responseType: 'text'
        })
        .subscribe(
          (response: any) => {
            console.log('Orden creada:', response);
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
