# Prepracticas-Numero-1

Trabajo numero uno de preprácticas: aplicación Laravel con Breeze y vistas Blade,
organizada en capas de frontend y backend.

La aplicación usa una interfaz básica en español. Incluye página de inicio, registro,
inicio y cierre de sesión, dashboard protegido con los datos reales de la cuenta
y perfil para editar nombre, correo y contraseña o eliminar la cuenta.
Usa formularios simples y enlaces de navegación. No requiere roles de usuario.

## Dónde trabajar

```text
Prepracticas-Numero-1/
├── frontend/
│   ├── resources/views/    Pantallas y componentes Blade
│   ├── resources/css/      Estilos con Tailwind CSS
│   ├── resources/js/       JavaScript y Alpine.js
│   ├── package.json        Dependencias del frontend
│   └── vite.config.js      Compilación del frontend
├── backend/
│   ├── app/Http/Controllers/  Lógica de las solicitudes
│   ├── app/Models/            Modelos y acceso a datos
│   ├── routes/                Direcciones de la aplicación
│   ├── database/              Migraciones y base SQLite
│   ├── config/                Configuración de Laravel
│   ├── tests/                 Pruebas automáticas
│   ├── public/                Entrada web y recursos compilados
│   └── artisan                Comandos de Laravel
└── scripts/                   Preparación e inicio en Windows
```

Para cambiar lo que se ve, editar `frontend/resources`. Para cambiar cómo se
procesa o guarda la información, editar `backend`. Laravel carga las vistas desde
el frontend mediante `backend/config/view.php`; Vite genera los recursos en
`backend/public/build`. Ambas capas forman una sola aplicación con autenticación
mediante las sesiones de Laravel.

## Abrir en esta computadora

En PowerShell, dentro de `C:\Prepracticas-Numero-1`:

```powershell
powershell -ExecutionPolicy Bypass -File .\scripts\dev.ps1
```

Abrir <http://127.0.0.1:8000/register> y crear una cuenta. No hay usuarios ni
contraseñas predeterminados. El servidor se detiene con `Ctrl+C`.

Las rutas principales son `/login`, `/dashboard` y `/profile`. El dashboard y el
perfil requieren iniciar sesión. La base de datos local es SQLite y se guarda en
`backend/database/database.sqlite`.

## Preparar otra copia en Windows

Requisitos: Windows x64, Node.js 22.12 o superior y Microsoft Visual C++
Redistributable x64. Git es necesario para sincronizar el repositorio.

```powershell
powershell -ExecutionPolicy Bypass -File .\scripts\setup-windows.ps1
```

El script descarga PHP 8.4 y Composer desde sus fuentes oficiales y verifica sus
hashes antes de instalarlos en `.tools`. Después instala las dependencias fijadas
en los archivos lock, prepara `.env`, crea la clave de aplicación, aplica las
migraciones y compila los estilos. Conserva una clave y base de datos existentes.
La activación de las herramientas solo afecta la terminal actual.

Para ejecutar comandos manualmente en PowerShell:

```powershell
. .\scripts\Activate-Project.ps1
cd backend
php artisan test
composer validate --strict
cd ../frontend
npm run build
```

Para desarrollar estilos con recarga automática, mantener `npm run dev` abierto
en una segunda terminal dentro de `frontend`. El servidor PHP se inicia desde la
raíz con `scripts/dev.ps1`. `composer run dev` dentro de `backend` también inicia
el servidor PHP usando los recursos ya compilados.

## Preparar en Linux o en la nube

Se necesitan PHP 8.3 o superior, Composer, Node.js 22.12 o superior y las
extensiones PHP de Laravel, incluyendo `pdo_sqlite`, `mbstring`, `openssl`, `curl`,
`fileinfo`, `dom` y `xml`.

```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate
npm --prefix ../frontend ci
npm --prefix ../frontend run build
php artisan serve --host=0.0.0.0 --port=8000
```

Crear `.env` y generar la clave solo la primera vez. En un entorno en la nube,
abrir el puerto 8000 con la herramienta de vista previa del proveedor y ajustar
`APP_URL` a esa dirección.

## Trabajar localmente y en la nube

Las dos carpetas son copias independientes. GitHub permite compartir el código.
Antes de cambiar de entorno, guardar y subir los cambios de la copia actual:

```bash
git status
git add .
git commit -m "Configurar Laravel con Breeze y Blade"
git push origin main
```

En la otra copia, con el árbol de trabajo limpio:

```bash
git pull --ff-only origin main
cd backend
composer install
npm --prefix ../frontend ci
php artisan migrate
npm --prefix ../frontend run build
```

En Windows, activar primero las herramientas con `scripts/Activate-Project.ps1`.
Resolver cualquier cambio local pendiente antes de traer cambios; no descartar
archivos para forzar la sincronización.

`backend/.env`, las herramientas, dependencias y bases SQLite están excluidos de Git.
Cada entorno tiene su propia clave y datos; las cuentas creadas localmente no se
copian a la nube. El correo usa el canal `log`: los enlaces de recuperación de
contraseña se escriben en `backend/storage/logs/laravel.log` durante el desarrollo.

## Publicar en Vercel

El proyecto incluye `vercel.json` y `api/index.php` para ejecutar Laravel con
`vercel-php@0.9.0` (PHP 8.5). La raíz del proyecto de Vercel debe ser la raíz del
repositorio, no `frontend` ni `backend`.

El `composer.json` de la raíz solo coordina el despliegue; las dependencias de la
aplicación siguen en `backend/composer.json`. Vercel compila los recursos del
frontend, instala las dependencias del backend y aplica las migraciones.

Antes de desplegar, configurar en Vercel las variables de producción:

- `APP_KEY`: una clave de Laravel propia de producción, generada con
  `php artisan key:generate --show`, guardada como secreto.
- `APP_URL`: la dirección HTTPS definitiva de la aplicación.
- `DATABASE_URL`: conexión a PostgreSQL, guardada como secreto. La integración de
  Supabase también puede proporcionar `POSTGRES_URL` automáticamente.

Con Supabase, usar la conexión Session Pooler (puerto 5432) en `DB_URL`. Crear una
vez el esquema privado `laravel` con `CREATE SCHEMA IF NOT EXISTS laravel;` antes
del primer despliegue. Así las tablas de Laravel quedan fuera de la API pública
de Supabase.

Las cuentas, sesiones y caché se guardan en PostgreSQL. SQLite sigue siendo la
base local y no se sube. Vercel usa almacenamiento temporal para las vistas
compiladas; ese almacenamiento no conserva usuarios. Las cuentas locales no se
copian a producción.

Desde la raíz, con Vercel CLI autenticado y el proyecto vinculado:

```bash
vercel deploy --prod
```

El correo conserva el canal de desarrollo hasta configurar un proveedor SMTP
con las variables `MAIL_*`; la recuperación por correo requiere ese proveedor.

## Tecnologías

- Laravel 13, PHP 8.4 y Composer 2.
- Laravel Breeze 2.4, Blade, Alpine.js y Tailwind CSS 4.
- Vite para compilar CSS y JavaScript.
- SQLite para el entorno de desarrollo.

Documentación oficial: [Laravel](https://laravel.com/docs/13.x),
[Breeze](https://github.com/laravel/breeze),
[PHP para Windows](https://www.php.net/downloads.php?os=windows&version=8.4) y
[Composer](https://getcomposer.org/download/).
