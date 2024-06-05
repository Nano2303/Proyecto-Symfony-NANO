import { Component } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { RegisterService } from 'src/app/services/register/register-service.service';

@Component({
  selector: 'app-register',
  templateUrl: './register.component.html',
  styleUrls: ['./register.component.css']
})
export class RegisterComponent {
  registerForm: FormGroup;
  errors: string | null = null;
  successMessage: string | null = null;

  constructor(private fb: FormBuilder, private registerService: RegisterService) {
    this.registerForm = this.fb.group({
      nombre: ['', Validators.required],
      email: ['', [Validators.required, Validators.email]],
      password: ['', Validators.required],
      repeat_password: ['', Validators.required],
      telefono: ['', Validators.required],
      direccion: ['', Validators.required],
      ciudad: ['', Validators.required],
      provincia: ['', Validators.required],
      codigo_postal: ['', Validators.required],
      pais: ['', Validators.required]
    });
  }

  onSubmit() {
    if (this.registerForm.valid) {
      const formValue = this.registerForm.value;
      const userData = {
        usuario: {
          email: formValue.email,
          password: formValue.password,
          nombre: formValue.nombre,
          telefono: formValue.telefono
        },
        direccion: {
          calle: formValue.direccion,
          ciudad: formValue.ciudad,
          provincia: formValue.provincia,
          codigo_postal: formValue.codigo_postal,
          pais: formValue.pais
        }
      };

      this.registerService.register(userData).subscribe(
        (response: any) => {
          this.errors = null;
          this.successMessage = 'Usuario registrado exitosamente';
          console.log(response);
        },
        (error: any) => {
          this.errors = error.error.errors || 'Ocurrió un error';
          this.successMessage = null;
          console.error(error);
        }
      );
    }
  }
}
