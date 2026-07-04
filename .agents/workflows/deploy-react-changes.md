---
description: Cómo compilar y desplegar cambios del frontend React (diabe-ui) al servidor de producción via Dockploy
---

# 🚀 Deploy de Cambios React (diabe-ui → diabe-platform)

## Pre-requisitos

- Node.js **18+** instalado localmente
- Acceso a ambos repos: `diabe-ui` (frontend) y `diabe-platform` (backend)
- Acceso al panel de Dockploy

---

## Paso 1: Compilar el Frontend

```bash
cd /ruta/a/diabe-ui
npm install        # solo la primera vez o si hay dependencias nuevas
npm run build
```

Esto genera los archivos compilados en `dist/react/`.  
Al terminar, anota los **hashes** del archivo principal en la última línea del output:

```
dist/react/index-XXXXXXXX.js    ← JS principal
```

También hay un CSS:

```bash
ls dist/react/index-*.css       ← CSS principal
```

---

## Paso 2: Copiar al Repo del Backend

```bash
cp -r dist/react/* /ruta/a/diabe-platform/public/react/
```

---

## Paso 3: Actualizar `head.blade.php`

Edita `resources/views/react/head.blade.php` (líneas 1-2) con los **nuevos hashes**:

```html
<link rel="stylesheet" href="/react/index-NUEVO_HASH.css" />
<script type="module" crossorigin src="/react/index-NUEVO_HASH.js"></script>
```

> ⚠️ **IMPORTANTE:** Si no actualizas estos hashes, el navegador seguirá cargando la versión anterior del React.

---

## Paso 4: Commit, Push y Tag de Release

```bash
cd /ruta/a/diabe-platform
git add -A
git commit -m "feat: Descripción del cambio"
git push origin main
```

El deploy se dispara **por tag**, no por push. Cuando quieras desplegar:

```bash
git tag -a vX.Y.Z -m "Descripción del release"
git push origin vX.Y.Z
```

---

## Paso 5: Esperar el Build en GitHub Actions y el Deploy en Dockploy

Al hacer push del tag:

1. GitHub Actions (`docker-build.yml`) construye la imagen y la publica en `ghcr.io/trinity-corpx3/diabe-platform` con los tags `vX.Y.Z`, `latest` y el SHA del commit
2. Dockploy (configurado "on tag") hace pull de la imagen y despliega el nuevo contenedor

⏱️ Tiempo estimado: **5-10 minutos** (build en Actions + pull/deploy)

---

## Paso 6: Inyectar Traducciones (Post-Deploy)

Después del rebuild, entra al contenedor via Dockploy:

1. Abre **Docker Terminal** → selecciona `app-1` → clic en **`/bin/sh`** (NO Bash)
2. Ejecuta:

```sh
cd /var/www/app && php -r '
$files = glob("public/es*.json");
echo count($files) . " files\n";
foreach ($files as $f) {
  $j = json_decode(file_get_contents($f), true);
  if (!is_array($j)) continue;
  $j["total_paid"] = "Total Pagado";
  $j["total_expenses"] = "Total Gastos";
  file_put_contents($f, json_encode($j, JSON_UNESCAPED_UNICODE));
  echo "OK " . basename($f) . "\n";
}
echo "Done!\n";
'
```

> ℹ️ Las traducciones se pierden en cada rebuild porque los JSON se regeneran. Este paso debe ejecutarse después de cada deploy.

---

## Paso 7: Verificar

1. Abre la app en el navegador
2. Haz **Cmd + Shift + R** (hard refresh) para limpiar caché
3. Verifica que los cambios se reflejen correctamente

---

## Estructura de Archivos Clave

| Archivo                                               | Propósito                                         |
| ----------------------------------------------------- | ------------------------------------------------- |
| `diabe-ui/src/pages/`                                 | Código fuente de las páginas React                |
| `diabe-ui/src/common/routes.tsx`                      | Registro de rutas del frontend                    |
| `diabe-ui/src/components/layouts/Default.tsx`         | Sidebar / navegación                              |
| `diabe-platform/public/react/`                        | Archivos compilados del frontend                  |
| `diabe-platform/resources/views/react/head.blade.php` | Referencias a los JS/CSS compilados               |
| `diabe-platform/Dockerfile`                           | Build config (copia `public/` → `custom_public/`) |

---

## Notas Importantes

- **No usar Bash** en el contenedor — es Alpine Linux, solo tiene `/bin/sh`
- Los archivos React se sirven desde `/var/www/app/custom_public/react/` en el contenedor (no desde `public/react/`)
- La migración de `payroll_entries` es idempotente (`Schema::hasTable` check) para evitar crash loops
- Si el contenedor entra en crash loop, revisar los logs en Dockploy para identificar el error de migración
