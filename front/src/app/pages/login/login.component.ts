// src/app/pages/login/login.component.ts
import { Component, OnInit } from '@angular/core';
import { FormBuilder, Validators } from '@angular/forms';
import { Router } from '@angular/router';
import { LoginService } from '../../services/auth/login.service';
import { LoginRequest } from 'src/app/services/auth/login.model';

@Component({
  selector: 'app-login',
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.scss']
})
export class LoginComponent implements OnInit {
  loginError: string = '';
  loginForm = this.formBuilder.group({
    email: ['', [Validators.required, Validators.email]],
    password: ['', Validators.required]
  });

  constructor(
    private formBuilder: FormBuilder, 
    private router: Router, 
    private loginService: LoginService
  ) { }

  ngOnInit(): void {}

  login(): void {
    if (this.loginForm.valid) {
      this.loginService.login(this.loginForm.value as LoginRequest).subscribe({
        next: (user) => {
          console.log(user);
          this.router.navigateByUrl('/home');
        },
        error: (error) => {
          console.error(error);
          this.loginError = error.message || 'Credenciales inválidas';
        },
        complete: () => {
          console.info("Login completo");
          this.loginForm.reset();
        }
      });
    } else {
      this.loginForm.markAllAsTouched();
      console.log("Error al ingresar datos");
    }
  }

  get email() {
    return this.loginForm.controls.email;
  }

  get password() {
    return this.loginForm.controls.password;
  }
}
