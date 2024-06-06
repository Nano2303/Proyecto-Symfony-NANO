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
  estadoSesion = false;
  emailUsuario = '';
  

  @Input()
  get cart(): Cart {
    this.estadoSesion = this.isLoggedIn();
    this.emailUsuario = localStorage.getItem('user_email') ?? '';
    this.emailUsuario = this.emailUsuario.split('@')[0];
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
  //  console.log('ngOnInit called');
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
      },
      error: (error) => {
        console.error('Error al cerrar sesión:', error);
      }
    });
  }

  isLoggedIn(): boolean {
    if (localStorage.getItem('user_role') == null){
      return false
    }else{
      return true;
    }
  }

  
}
