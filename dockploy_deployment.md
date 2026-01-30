# Guía de Despliegue en Dockploy - Constructora MMI

Esta guía cubre el proceso completo para llevar tu configuración personalizada de Invoice Ninja v5 a Dockploy.

## 1. Preparación del Repositorio (GitHub)

Actualmente tu repositorio apunta al oficial de `invoice-ninja`. Debemos cambiar esto para que apunte a **TU** repositorio privado donde guardarás esta configuración personalizada.

### A. Crear tu Repo
1. Ve a GitHub y crea un **Nuevo Repositorio** (ej. `constructora-erp`).
2. No lo inicialices con README/gitignore, déjalo vacío.

### B. Vincular Localmente
Ejecuta estos comandos en tu terminal local para subir tus cambios:

```bash
# 1. Agrega todos los archivos modificados
git add .

# 2. Confirma los cambios locales
git commit -m "Configuración Constructora: Traducciones, Campos Personalizados y Comando de Setup"

# 3. Renombra el remoto 'origin' actual o añade uno nuevo (Recomendado: nuevo remoto para no perder referencia)
# Reemplaza URL_DE_TU_NUEVO_REPO con la url real (ej. https://github.com/tu-usuario/constructora-erp.git)
git remote add mmi URL_DE_TU_NUEVO_REPO

# 4. Sube los cambios a tu nuevo repositorio
git push -u mmi HEAD
```

## 2. Configuración en Dockploy

### A. Crear Proyecto/Servicio
1. En Dockploy, selecciona **"Application"** (o Service).
2. Conecta tu cuenta de GitHub y selecciona el repositorio `constructora-erp`.
3. Rama (Branch): Probablemente `v5-stable` o la rama en la que estés trabajando (verifica con `git branch`).

### B. Environment Variables (Variables de Entorno)
Copia estas variables en la sección de "Environment" de Dockploy. Son críticas para Snappdf y la base de datos.

| Variable | Valor Recomendado | Notas |
| :--- | :--- | :--- |
| `APP_ENV` | `production` | |
| `APP_DEBUG` | `false` | |
| `APP_URL` | `https://tu-dominio-en-dockploy.com` | El dominio final |
| `PDF_GENERATOR` | `snappdf` | Motor de PDF local rápido |
| `SNAPPDF_CHROMIUM_PATH` | `/usr/bin/chromium-browser` | **Crucial en Docker Linux** |
| `PHANTOMJS_PDF_GENERATION` | `false` | Desactivar legacy |
| `DB_CONNECTION` | `mysql` | |
| `DB_HOST` | `mysql` (o el nombre de tu servicio db) | |
| `DB_USERNAME` | `ninja` | O lo que definas en tu DB |
| `DB_PASSWORD` | `password_secreto` | O lo que definas en tu DB |

### C. Build & Deploy Settings
Dockploy usualmente detecta `Dockerfile` o `composer.json`. Para Invoice Ninja se recomienda usar el Dockerfile oficial si es posible, o una imagen base de PHP.

Si usas **Nixpacks/Buildpacks** (común en Dockploy/Coolify), asegúrate de que instale:
*   PHP 8.2+
*   Extensiones: bcmath, gd, gmp, pdo_mysql, zip
*   **Chromium** (Necesario para Snappdf) -> *Esto puede requerir un `nixpacks.toml` o `apt-get install chromium-browser` en el build.*

## 3. Post-Deployment (Configuración Automática)

Una vez que la aplicación esté "Running" (verde), necesitas ejecutar los comandos para aplicar la "personalización de la constructora".

En la **Consola/Terminal** de la aplicación en Dockploy (o como "Post-Deploy Command"), ejecuta:

```bash
# 1. Migraciones de DB (Base)
php artisan migrate --force

# 2. Nuestro Comando Mágico (Aplica Campos Personalizados y Dashboard MMI)
php artisan ninja:configure-construction-company

# 3. Optimización Final
php artisan optimization:clear
php artisan config:cache
php artisan view:cache
```

---
**Resultado Final**:
Al entrar a tu URL, verás el sistema ya traducido a "Obra", "Insumo", y con los campos personalizados ("Fecha Inicio Obra", etc.) listos para usar sin configuración manual.
