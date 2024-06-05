import { Component, Input, OnInit } from '@angular/core';
import { Cart, CartItem } from 'src/app/models/cart.model';
import { CartService } from 'src/app/services/cart.service';
import { LoginService } from 'src/app/services/auth/login.service';
import { Router } from '@angular/router';
import { HttpClient } from '@angular/common/http';

@Component({
  selector: 'app-header',
  templateUrl: './header.component.html',
  styleUrls: ['./header.component.scss']
})
export class HeaderComponent implements OnInit {
  private _cart: Cart = { items: [] };
  itemsQuantity = 0;
  isLoggedIn = false;

  @Input()
  get cart(): Cart {
    return this._cart;
  }

  set cart(cart: Cart) {
    this._cart = cart;

    this.itemsQuantity = cart.items
      .map((item) => item.quantity)
      .reduce((prev, curent) => prev + curent, 0);
  }

  constructor(
    private cartService: CartService,
    private loginService: LoginService,
    private router: Router,
    private http: HttpClient
  ) {}

  ngOnInit(): void {
    console.log('ngOnInit called');
    this.checkSession();
  }

  getTotal(items: CartItem[]): number {
    return this.cartService.getTotal(items);
  }

  onClearCart(): void {
    this.cartService.clearCart();
  }

  onLogout(): void {
    this.loginService.logout().subscribe({
      next: () => {
        this.router.navigate(['/login']);
        this.isLoggedIn = false;
      },
      error: (error) => {
        console.error('Error al cerrar sesión:', error);
      }
    });
  }

  checkSession(): void {
    console.log('checkSession called');
    this.http.get<{ rol: string }>('http://localhost:8000/comprobar-session').subscribe({
      next: (response) => {
        this.isLoggedIn = true;
        console.log('Hay una sesion iniciada: ' + this.isLoggedIn);
      },
      error: (error) => {
        if (error.status === 404) {
          this.isLoggedIn = false;
          console.log('No hay ninguna sesion iniciada: ' + this.isLoggedIn);
        } else {
          console.error('Error comprobando la sesión:', error);
        }
      }
    });
  }
}
