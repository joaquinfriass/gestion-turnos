# Gestión de Turnos

Aplicación PHP MVC simple para administrar turnos médicos con tres roles:
administrador, recepcionista y médico.

## Requisitos

- PHP 8.1 o superior con PDO MySQL habilitado.
- MySQL o MariaDB.
- XAMPP en Windows, o un entorno equivalente.
- Navegador con acceso a internet para cargar Bootstrap, Bootstrap Icons y jQuery desde CDN.

## Instalación

1. Copiar el proyecto en `C:\xampp\htdocs\gestionTurnos`.
2. Iniciar Apache y MySQL desde XAMPP.
3. Importar [database.sql](database.sql) desde phpMyAdmin o consola MySQL.
4. Revisar la conexión en [config/conexion.php](config/conexion.php):

```php
$host = 'localhost';
$port = '3308';
$db = 'gestion_turnos';
$user = 'root';
$pass = 'root';
```

5. Abrir la aplicación:

```text
http://localhost/gestionTurnos/index.php
```

También se puede probar con el servidor embebido de PHP:

```powershell
php -S 127.0.0.1:8099 -t .
```

Y luego abrir:

```text
http://127.0.0.1:8099/index.php
```

## Credenciales de prueba

Administrador:

```text
admin@gestionturnos.com
admin123
```

Recepción:

```text
recepcion@gestionturnos.com
recepcion123
```

Médico:

```text
jperez@gestionturnos.com
medico123
```

## Flujo de uso

Administrador:

- Accede al dashboard general.
- Gestiona turnos, pacientes, médicos y usuarios.
- Crea, edita y elimina registros.

Recepción:

- Accede a su dashboard operativo.
- Crea turnos.
- Consulta médicos.
- Consulta y crea pacientes.

Médico:

- Ve su agenda del día.
- Consulta historial de turnos.
- Consulta pacientes asociados.
- Marca turnos como atendidos.

## Smoke test manual

Después de importar la base:

1. Iniciar sesión como administrador.
2. Entrar a `Turnos`, `Pacientes`, `Medicos` y `Usuarios`; todas las pantallas deben cargar.
3. Crear un paciente nuevo y verificar que aparece en el listado.
4. Crear un turno futuro para un médico y un paciente.
5. Intentar crear otro turno para el mismo médico en la misma fecha y hora; debe mostrarse aviso de horario ocupado.
6. Cerrar sesión.
7. Iniciar sesión como recepción y crear un paciente desde `Pacientes`.
8. Iniciar sesión como médico y marcar un turno como `atendido`.
9. Probar que una ruta protegida sin sesión redirige a login, por ejemplo:

```text
http://localhost/gestionTurnos/index.php?action=turnos
```

## Seguridad y mantenimiento

- Los formularios POST usan token CSRF.
- Las contraseñas se guardan con `password_hash`.
- Los archivos de sesión locales en `storage/sessions/sess_*` están ignorados por Git.
- No versionar dumps con datos reales ni archivos de sesión.

## Estructura principal

```text
config/          Conexión PDO
controllers/     Controladores por módulo
models/          Acceso a datos
views/           Vistas PHP
public/css       Estilos
public/js        JavaScript
storage/sessions Sesiones locales de PHP
database.sql     Esquema y datos iniciales
```
