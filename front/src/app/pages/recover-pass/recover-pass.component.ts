import { Component } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { RecoverPassService } from 'src/app/services/recoverpass/recover-pass.service';

@Component({
  selector: 'app-recover-pass',
  templateUrl: './recover-pass.component.html',
  styleUrls: ['./recover-pass.component.css']
})
export class RecoverPassComponent {
  recoverPassForm: FormGroup;
  newPassForm: FormGroup;
  emailSent: boolean = false;
  successMessage: string | null = null;
  error: string | null = null;

  constructor(private fb: FormBuilder, private recoverPassService: RecoverPassService) {
    this.recoverPassForm = this.fb.group({
      email: ['', [Validators.required, Validators.email]]
    });
    this.newPassForm = this.fb.group({
      codigo_recuperacion: ['', Validators.required],
      password: ['', Validators.required],
      repeat_password: ['', Validators.required]
    });
  }

  onSubmitEmail() {
    if (this.recoverPassForm.valid) {
      this.recoverPassService.recoverPassword(this.recoverPassForm.value).subscribe(
        (response: any) => {
          if (response.Mensaje) {
            this.successMessage = response.Mensaje;
            this.emailSent = true;
            this.error = null;
          } else {
            this.error = 'Ocurrió un error al enviar el enlace de recuperación';
            this.emailSent = false;
          }
        },
        (error: any) => {
          this.error = 'Ocurrió un error al enviar el enlace de recuperación';
          this.emailSent = false;
        }
      );
    }
  }

  onSubmitNewPass() {
    if (this.newPassForm.valid && this.newPassForm.value.password === this.newPassForm.value.repeat_password) {
      const newPassData = {
        codigo: this.newPassForm.value.codigo_recuperacion,
        nueva_contrasena: this.newPassForm.value.password
      };
      console.log('Datos del formulario de nueva contraseña (JSON):', JSON.stringify(newPassData)); // Verificar datos
      this.recoverPassService.setNewPassword(newPassData).subscribe(
        (response: any) => {
          console.log('Respuesta del backend:', response); // Verificar respuesta del backend
          this.successMessage = 'Contraseña cambiada exitosamente';
          this.error = null;
        },
        (error: any) => {
          console.error('Error al cambiar la contraseña', error); // Verificar error
          this.error = 'Ocurrió un error al cambiar la contraseña';
        }
      );
    } else {
      this.error = 'Las contraseñas no coinciden';
    }
  }
}
