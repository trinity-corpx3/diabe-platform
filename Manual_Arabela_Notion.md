# Manual Único de Operación: Plataforma de Gestión Diabe

## Caso de Estudio: Control Cisterna Arabela 2025 OG

Bienvenidos a la guía operativa definitiva de la plataforma Diabe. Este manual está diseñado para enseñar paso a paso cómo registrar, mantener y auditar los números de las obras, tomando como base práctica el proyecto **Control Cisterna Arabela 2025 OG**.

Este documento cubre:

1. Descripción detallada de cada módulo del sistema
2. Resumen financiero actual de Arabela
3. Guía para continuar alimentando la obra Arabela
4. Guía para registrar una obra completamente nueva
5. Compendio extenso de Preguntas Frecuentes (FAQ)

---

## 1. Módulos de la Plataforma: Guía Completa

### 1.1 Inicio (Dashboard)

- **Para qué sirve:** Es la pantalla principal al entrar al sistema. Funciona como un tablero de mandos ejecutivo que consolida la salud financiera general de la empresa en un solo vistazo.
- **Cómo funciona:** Recopila datos en tiempo real de todos los demás módulos (Facturas, Pagos, Gastos, Proyectos) y los presenta en gráficas interactivas y widgets numéricos. Muestra ingresos totales, egresos, facturas vencidas, facturas pendientes de cobro, y tareas próximas a vencer.
- **Qué mitiga:** Evita la "ceguera de taller", es decir, la situación donde la gerencia no tiene visibilidad instantánea del estado financiero. Sin este módulo, tendrías que abrir Excel, pedir reportes al contador, y juntar información de múltiples fuentes para saber "cómo vamos". Con el Dashboard, abres la app y en 3 segundos tienes el panorama completo.
- **Ejemplo práctico con Arabela:** Al entrar al Dashboard, puedes ver de inmediato que existe una factura pendiente de cobro por $545,751 del proyecto Arabela, lo cual te recuerda darle seguimiento al cliente.

### 1.2 Clientes (Clients)

- **Para qué sirve:** Es el directorio maestro centralizado (CRM base) donde viven los datos fiscales, comerciales y de contacto de todas las empresas o personas que nos contratan.
- **Cómo funciona:** Cada ficha de cliente almacena: RFC, Razón Social, Nombre Comercial, dirección fiscal, correo electrónico, teléfono, condiciones de pago, moneda preferida, y notas privadas. Desde la ficha del cliente puedes ver todas sus facturas, pagos y proyectos asociados.
- **Qué mitiga:**
    - Pérdida de contactos clave cuando un vendedor se va de la empresa
    - Errores de facturación por RFCs mal escritos o desactualizados
    - La desorganización de tener la información del cliente dispersa en Excel, WhatsApp, libretas y correos
    - Duplicidad de clientes (el sistema te avisa si ya existe uno con el mismo RFC)
- **Ejemplo práctico con Arabela:** El cliente "Arabela" (ID: 2) tiene su RFC almacenado correctamente, lo que permite que todas las facturas del proyecto Cisterna se generen con los datos fiscales correctos sin re-capturar nada.

### 1.3 Productos (Products / Services)

- **Para qué sirve:** Es el catálogo estandarizado de todos los conceptos, servicios y materiales que la empresa vende o cobra. Funciona como tu "menú de precios".
- **Cómo funciona:** Cada producto tiene un nombre, descripción, precio unitario, unidad de medida (pieza, metro, hora, servicio) y categoría fiscal. Al crear una factura, simplemente seleccionas productos del catálogo en lugar de escribirlos manualmente.
- **Qué mitiga:**
    - Cobrar precios diferentes a distintos clientes por el mismo concepto
    - Errores de dedo en nombres o descripciones de conceptos en facturas
    - Pérdida de tiempo al re-escribir los mismos conceptos en cada factura nueva
    - Inconsistencias fiscales por usar claves SAT incorrectas
- **Ejemplo práctico:** Si vendes frecuentemente "Suministro e instalación de bomba sumergible", lo capturas una vez en Productos con su precio base, y en cada factura nueva solo lo seleccionas del catálogo.

### 1.4 Facturas (Invoices)

- **Para qué sirve:** Genera, almacena y da seguimiento a las cuentas por cobrar. Es el documento oficial que le dices al cliente: "Me debes esta cantidad por este trabajo".
- **Cómo funciona:** Seleccionas un Cliente, vinculas un Proyecto (paso crucial), agregas los Productos/Servicios del catálogo, defines impuestos y condiciones, y la envías. La factura pasa por estados: Borrador → Enviada → Parcial → Pagada.
- **Qué mitiga:**
    - Olvidar cobrar un anticipo, una estimación o el finiquito de una obra
    - No tener registro histórico de qué se le cobró a quién y cuándo
    - Perder la pista del saldo pendiente de cada cliente
    - Facturar dos veces el mismo concepto por error
- **Ejemplo práctico con Arabela:** La factura "0001" por $1,091,502.00 se vinculó al proyecto Arabela. El sistema sabe que de esa factura se han cobrado $545,751 y faltan otros $545,751 por cobrar. Las facturas "1005_Eliminado" y "1006_Eliminado" están marcadas como borradas y NO cuentan para el total facturado.

### 1.5 Pagos (Payments)

- **Para qué sirve:** Registra el dinero que efectivamente ya entró a la cuenta bancaria de la empresa. Es la diferencia entre "me deben" y "ya me pagaron".
- **Cómo funciona:** Se vincula directamente a una Factura existente. Cuando el cliente deposita, capturas: monto recibido, fecha exacta del depósito, método de pago (transferencia, cheque, efectivo), y número de referencia bancaria. El sistema automáticamente actualiza el balance de la factura y el "Total Pagado" del proyecto.
- **Qué mitiga:**
    - La peligrosa confusión entre "facturado" y "cobrado" que lleva a gastar dinero que aún no llega
    - Perder claves de rastreo o comprobantes de depósito
    - No saber qué clientes están al corriente y cuáles deben
    - Discrepancias entre lo que dice el banco y lo que dice la empresa
- **Ejemplo práctico con Arabela:** Se registró un pago de $545,751 contra la factura "0001". Esto hizo que el "Total Pagado" del proyecto Arabela subiera a esa cifra, y la Ganancia Real pasara de $0 a $305,682.59 (pagado menos gastos).

### 1.6 Proyectos (Projects)

- **Para qué sirve:** Es el centro neurálgico financiero de cada obra o contrato. Funciona como una "cubeta" donde se agrupan todas las facturas, todos los pagos, todas las tareas y todos los gastos que pertenecen a un mismo centro de costos.
- **Cómo funciona:** Creas un proyecto con nombre, cliente asociado, y opcionalmente horas presupuestadas. A partir de ese momento, cada factura y cada gasto que se le vincule alimentará automáticamente su tablero financiero (Total Facturado, Total Pagado, Total Gastos, Ganancia, Rentabilidad).
- **Qué mitiga:**
    - La fuga oculta de capital: sin proyectos, la empresa podría creer que una obra es rentable cuando en realidad los gastos se comieron toda la ganancia, pero nadie lo ve porque los gastos están dispersos
    - La imposibilidad de comparar rentabilidad entre obras distintas
    - No saber cuánto costó realmente ejecutar una obra vs. cuánto se cobró
- **Ejemplo práctico con Arabela:** El proyecto "Control Cisterna Arabela 2025 OG" (ID: 2) agrupa la factura "0001", la factura "1015", los 20 gastos por $240,068.41, y gracias a eso sabemos que la ganancia real es $305,682.59.

### 1.7 Tareas (Tasks)

- **Para qué sirve:** Seguimiento de bitácora de actividades, asignación de responsables y registro de horas laboradas dentro de cada proyecto.
- **Cómo funciona:** Se crean tareas dentro de un Proyecto, se asignan a un miembro del equipo, se puede activar un cronómetro (timer) para medir el tiempo invertido, y se marcan como completadas al terminar. Las horas acumuladas se comparan contra las "Horas Presupuestadas" del proyecto.
- **Qué mitiga:**
    - Retrasos operativos por falta de seguimiento del avance de obra
    - Falta de rendición de cuentas (quién hizo qué y cuándo)
    - Pérdida de horas de mano de obra no presupuestadas que erosionan la ganancia sin que nadie lo note
    - No poder medir la productividad del equipo en campo
- **Ejemplo práctico con Arabela:** Actualmente el proyecto Arabela no tiene tareas registradas. Se recomienda crear tareas como "Instalación de bomba", "Excavación", "Pruebas hidráulicas" para llevar bitácora del avance.

### 1.8 Proveedores (Vendors)

- **Para qué sirve:** Es el directorio maestro de las empresas, personas físicas o subcontratistas que nos venden material, nos prestan servicios o ejecutan trabajos para nosotros.
- **Cómo funciona:** Almacena RFC del proveedor, razón social, datos de contacto, cuenta bancaria para pagos, y condiciones comerciales. Al registrar un gasto, seleccionas al proveedor del catálogo.
- **Qué mitiga:**
    - Falta de trazabilidad en las compras (no saber a quién se le compró qué)
    - Fraudes internos por proveedores fantasma
    - Errores en pagos por datos bancarios incorrectos o desactualizados
    - Perder el historial de compras con un proveedor cuando cambia el personal de compras
- **Ejemplo práctico con Arabela:** Los proveedores registrados incluyen: Constructora Irexa SA de CV, Orlando Lopez Hernandez, Lourdes Gutierrez Ortega, Aceros el Arbol, Dixa Equipo de Seguridad, Comercializadora Rayon CJ, Comisiones Bancarias, Cesar Alejandro Quintos Gonzalez, y Nómina Operativa. Cada gasto de Arabela apunta a uno de estos proveedores.

### 1.9 Gastos (Expenses)

- **Para qué sirve:** El registro absoluto y exhaustivo de toda salida de dinero de la empresa: material de construcción, nóminas, comisiones bancarias, gasolina, viáticos, subcontratos, comida de obra, herramienta, renta de maquinaria, etc.
- **Cómo funciona:** Cada gasto contiene: proveedor, categoría fiscal, monto (con IVA), proyecto asignado, fecha de pago, y archivo adjunto (foto del ticket o PDF de factura). Solo los gastos con **Fecha de Pago** asignada restan de la Ganancia; los que no la tienen se consideran "pendientes de pago".
- **Qué mitiga:**
    - El clásico "dónde quedó la bolita": gastos hormiga anónimos que nadie registra y que se comen la utilidad
    - Obras que parecen súper rentables porque nadie cargó los gastos reales al proyecto correcto
    - Falta de comprobantes fiscales (el sistema exige adjuntar archivo)
    - Imposibilidad de deducir gastos ante el SAT por no tener registro organizado
- **Ejemplo práctico con Arabela:** Se registraron 20 gastos que suman $240,068.41, todos vinculados correctamente al proyecto y todos con fecha de pago asignada. Por eso la plataforma los resta del cobro y muestra la ganancia real de $305,682.59.

### 1.10 Informes (Reports)

- **Para qué sirve:** Generador dinámico de reportes en PDF y tablas estadísticas para la toma de decisiones gerenciales, contables y fiscales.
- **Cómo funciona:** Seleccionas filtros (rango de fechas, cliente, proyecto, categoría) y el sistema genera automáticamente reportes como: Estado de Pérdidas y Ganancias, Reporte de Gastos por Categoría, Reporte de Impuestos (IVA cobrado vs acreditado), Resumen de Proyecto, Antigüedad de Cuentas por Cobrar, entre otros. Se pueden exportar a PDF.
- **Qué mitiga:**
    - Depender del contador para saber cómo le fue a la empresa en un trimestre
    - Tomar decisiones a ciegas por no tener datos consolidados
    - Perder horas armando reportes manuales en Excel
    - Errores humanos al consolidar cifras de diferentes fuentes
- **Ejemplo práctico con Arabela:** El "Reporte de Proyecto" de Arabela genera un PDF con el Sumario completo: valor del proyecto, facturado, pagado, balance, gastos pagados, gastos pendientes, y ganancia. Este reporte ahora usa correctamente el cálculo basado en pagos reales.

### 1.11 Bancos (Banks)

- **Para qué sirve:** Módulo especializado que presenta la realidad financiera cruda y consolidada de liquidez, agrupada por proyecto. Es el "momento de la verdad" donde se ve si una obra realmente deja dinero o no.
- **Cómo funciona:** Toma la lógica más estricta de flujo de efectivo: `Dinero Cobrado − Dinero Gastado = Ganancia Real`. Muestra por cada proyecto: Total Facturado, Total Pagado, Total Gastos, Ganancia, IVA Cobrado, IVA Acreditado, IVA por Pagar, y Rentabilidad porcentual. También muestra totales globales de toda la empresa.
- **Qué mitiga:**
    - **La ilusión de la riqueza:** Previene que la dirección gaste dinero creyendo que hubo un millón de ganancia por la facturación, cuando el margen neto real es mucho menor
    - Desconocer la posición fiscal de IVA (cuánto debemos al SAT vs cuánto nos acreditan)
    - No poder comparar la rentabilidad de múltiples obras simultáneamente en una sola pantalla
    - Tomar decisiones de inversión basadas en facturación en lugar de flujo de efectivo real
- **Ejemplo práctico con Arabela:** En Bancos se ve claramente que aunque se facturaron $1,091,502, la ganancia real es solo $305,682.59 porque solo se ha cobrado la mitad. Esto previene que la gerencia crea que hay $851,433 de utilidad disponible.

---

## 2. Resumen Financiero Actual: Control Cisterna Arabela 2025 OG

| Concepto                   | Monto           |
| -------------------------- | --------------- |
| **Total Facturado**        | $1,091,502.00   |
| **Total Pagado (Cobrado)** | $545,751.00     |
| **Pendiente de Cobro**     | $545,751.00     |
| **Total Gastos (Pagados)** | $240,068.41     |
| **Ganancia Real**          | **$305,682.59** |
| **Rentabilidad**           | **56.0%**       |

**Detalle de Facturas:**

| # Factura      | Monto         | Balance Pendiente | Estado              |
| -------------- | ------------- | ----------------- | ------------------- |
| 0001           | $1,091,502.00 | $545,751.00       | Parcialmente Pagada |
| 1015           | $545,751.00   | $545,751.00       | Enviada (Pendiente) |
| 1005_Eliminado | $545,751.00   | $0.00             | ❌ Eliminada        |
| 1006_Eliminado | $545,751.00   | $0.00             | ❌ Eliminada        |

**Proveedores con gastos registrados en esta obra:**
Constructora Irexa SA de CV · Orlando Lopez Hernandez · Lourdes Gutierrez Ortega · Aceros el Arbol · Dixa Equipo de Seguridad y Herramientas · Comercializadora Rayon CJ · Comisiones Bancarias · Cesar Alejandro Quintos Gonzalez · Nómina Operativa

> **Regla Constitucional:** La ganancia y la rentabilidad se calculan **única y exclusivamente** sobre depósitos confirmados en banco (Total Pagado), restando salidas confirmadas (Gastos pagados). Una factura de dos millones de pesos no genera ganancia hasta que se cobra.

---

## 3. Guía de Campo: Continuar alimentando la obra "Arabela 2025 OG"

Para que los números de esta obra ($305,682.59 de ganancia) sigan siendo la verdad absoluta, el personal administrativo y operativo debe seguir este protocolo al pie de la letra.

### A. Registrar un nuevo Pago del Cliente (Entrada de Dinero)

**Escenario:** El cliente "Arabela" deposita un avance de $200,000 MXN vía SPEI.

**Módulo involucrado:** Facturas → Pagos

1. Navega en el menú lateral y haz clic en **Facturas**.
2. Usa la barra de búsqueda y escribe `"Arabela"` o `"1015"`.
3. Localiza la Factura `"1015"` (balance pendiente: $545,751.00).
4. Selecciona la factura y haz clic en **Introducir Pago** (Enter Payment).
5. Llena los datos con precisión quirúrgica:
    - **Monto:** Borra el monto por defecto ($545,751) y escribe `200000`. Solo captura lo que realmente depositó.
    - **Fecha de pago:** Selecciona el día exacto que el banco reportó la entrada del SPEI. NO la fecha en que te enteraste.
    - **Tipo de Pago:** Selecciona `Transferencia Bancaria`.
    - **Número de Transacción:** Captura la clave de rastreo SPEI para futuras auditorías.
6. Haz clic en **Guardar Pago**.

**¿Qué pasa después?**
- La factura "1015" cambiará de estado "Enviada" a "Parcial" (porque aún falta saldo).
- En **Bancos**, el "Total Pagado" de Arabela brincará de $545,751 a $745,751.
- La **Ganancia Real** subirá automáticamente de $305,682.59 a $505,682.59.
- La **Rentabilidad** se recalculará instantáneamente.

**Errores comunes a evitar:**
- ❌ NO registres el pago contra la factura "0001" si ya está parcialmente pagada y existe la "1015" para el saldo restante. Verifica cuál factura corresponde al depósito.
- ❌ NO inventes la fecha de pago. Usa la fecha exacta del estado de cuenta bancario.
- ❌ NO olvides la clave de rastreo. Sin ella, en una auditoría no podrás comprobar el ingreso.

### B. Registrar un nuevo Gasto de la Obra (Salida de Dinero)

**Escenario:** El equipo de campo compró varilla y cemento en "Aceros el Arbol" por $35,000 MXN con factura.

**Módulo involucrado:** Gastos

1. Navega al módulo de **Gastos** (Expenses) en el menú lateral.
2. Haz clic en **Nuevo Gasto**.
3. Captura cada campo con rigor absoluto:
    - **Proveedor:** Selecciona "Aceros el Arbol" del catálogo. Si no existe, créalo primero en **Proveedores**.
    - **Categoría:** Elige "Materiales de Construcción" o la categoría fiscal que aplique.
    - **Monto:** Ingresa `35000` (incluye IVA). Si la factura desglosa IVA, captúralo en el campo correspondiente.
    - **Moneda:** Verifica que esté en MXN.
    - ⚠️ **PROYECTO (EL CAMPO MÁS IMPORTANTE):**
      Haz clic en el desplegable "Proyecto" y busca **"Control Cisterna Arabela 2025 OG"**.
      > _Si dejas esto en blanco, la plataforma asumirá que fue un gasto administrativo general. El proyecto Arabela NO registrará este costo, inflando artificialmente su ganancia. Esto es el error #1 más común y más destructivo._
    - ⚠️ **FECHA DE PAGO (EL SEGUNDO CAMPO MÁS IMPORTANTE):**
      Despliega el calendario y selecciona el día exacto en que salió el dinero de la cuenta bancaria.
      > _Un gasto SIN fecha de pago = gasto "por pagar" (deuda pendiente). Los gastos pendientes NO restan de la Ganancia. Solo impactan cuando se marca como pagados con fecha. Esto es el error #2 más común._
    - **Archivo adjunto:** Sube la foto del ticket o el PDF de la factura CFDI. Nunca captures un gasto sin respaldo documental.
    - **Notas privadas:** Escribe contexto útil, ej: "Varilla 3/8 para armado de losa cisterna, pedido #4521".
4. Haz clic en **Guardar**.

**¿Qué pasa después?**
- En **Bancos**, el "Total Gastos" de Arabela subirá de $240,068.41 a $275,068.41.
- La **Ganancia Real** bajará automáticamente de $305,682.59 a $270,682.59.
- En **Informes > Reporte de Proyecto**, la sección de gastos mostrará el nuevo registro.

**Errores comunes a evitar:**
- ❌ NO dejes el campo "Proyecto" vacío. Siempre vincúlalo a la obra correcta.
- ❌ NO omitas la "Fecha de Pago" si ya se realizó el desembolso.
- ❌ NO dupliques gastos. Si el ticket ya fue capturado por otro miembro del equipo, verificalo antes.
- ❌ NO captures gastos de una obra diferente en Arabela. Si te equivocas, edítalo de inmediato.

### C. Registrar una nueva Factura al Cliente (Estimación adicional)

**Escenario:** Se negoció un trabajo extra en la cisterna (ampliación) por $150,000 MXN adicionales.

**Módulo involucrado:** Facturas

1. Ve a **Facturas** > **Nueva Factura**.
2. Selecciona al cliente "Arabela".
3. En el campo **Proyecto**, selecciona "Control Cisterna Arabela 2025 OG".
4. Agrega los conceptos del trabajo extra con precios e impuestos.
5. Haz clic en **Guardar** y marca la factura como **Enviada**.

**¿Qué pasa después?**
- El "Total Facturado" de Arabela subirá de $1,091,502 a $1,241,502.
- El "Pendiente de Cobro" aumentará correspondientemente.
- La Ganancia NO cambia (porque aún no se cobra).

### D. Crear Tareas para documentar el avance de obra

**Escenario:** Quieres registrar bitácora del avance de la cisterna.

**Módulo involucrado:** Tareas

1. Ve al módulo de **Tareas** o entra al detalle del proyecto Arabela.
2. Haz clic en **Nueva Tarea**.
3. **Descripción:** Escribe una actividad específica, ej: "Excavación para base de cisterna".
4. **Proyecto:** Asegura que esté vinculado a "Control Cisterna Arabela 2025 OG".
5. **Asignado a:** Selecciona al responsable de campo.
6. **Estado:** Marca como "En progreso" o "Completada" según corresponda.
7. **Timer:** Opcionalmente inicia el cronómetro para medir horas invertidas.
8. Guarda la tarea.

**¿Qué pasa después?**
- Las horas acumuladas se comparan contra las "Horas Presupuestadas" del proyecto.
- Tendrás un historial de bitácora de todo lo que se hizo en la obra.

---

## 4. El Ciclo de Vida Completo: Cómo abrir una Obra Nueva desde Cero

Ganamos la licitación de "Mantenimiento Corporativo Sur 2026". A continuación, el flujo completo que deben seguir todos los departamentos involucrados.

### Etapa 1: Dar de alta al Cliente (si es nuevo)

**Responsable:** Ventas / Administración
**Módulo:** Clientes

Si el cliente que nos contrata no existe en el sistema:
1. Ve a **Clientes** > **Nuevo Cliente**.
2. Captura los datos fiscales completos:
    - **Nombre:** Razón social exacta como aparece en la constancia de situación fiscal.
    - **RFC:** Verifica que tenga 12 o 13 caracteres correctos.
    - **Dirección fiscal:** Calle, número, colonia, CP, ciudad, estado.
    - **Correo electrónico:** El correo al que llegarán las facturas electrónicas.
    - **Teléfono:** Para contacto de cobranza.
    - **Condiciones de pago:** Selecciona los días de crédito (ej. 30 días, 60 días).
3. Haz clic en **Guardar**.

**Tip:** En este momento ya puedes verificar que no existe un duplicado. Si el sistema te alerta que ya hay un cliente con ese RFC, usa el existente.

### Etapa 2: Registrar Proveedores nuevos (si aplica)

**Responsable:** Compras / Administración
**Módulo:** Proveedores

Si la obra requiere proveedores que aún no existen en el catálogo:
1. Ve a **Proveedores** > **Nuevo Proveedor**.
2. Captura:
    - **Nombre/Razón Social:** Tal cual aparece en su constancia fiscal.
    - **RFC:** Para poder deducir sus facturas.
    - **Datos de contacto:** Correo, teléfono.
    - **Cuenta bancaria:** CLABE para transferencias (si aplica).
3. Haz clic en **Guardar**.

**Tip:** Registra a TODOS los proveedores que anticipes necesitar antes de iniciar la obra, así el personal de campo no pierde tiempo creándolos después.

### Etapa 3: Crear el Proyecto (Centro de Costos)

**Responsable:** Ventas / Director de Obra
**Módulo:** Proyectos

1. Ve a **Proyectos** > **Nuevo Proyecto**.
2. Captura:
    - **Nombre:** Usa la nomenclatura oficial: `"Mantenimiento Corporativo Sur 2026"`.
    - **Cliente:** Selecciona al cliente de la Etapa 1.
    - **Horas Presupuestadas:** Si la obra está acotada, ingresa las horas estimadas.
    - **Tasa por hora:** Si cobras por hora, define la tarifa del proyecto.
    - **Fecha de vencimiento:** La fecha contractual de entrega.
    - **Notas públicas:** Dirección de la obra, contacto en sitio.
    - **Notas privadas:** Datos internos (margen esperado, riesgos identificados).
3. Haz clic en **Guardar**.

**Resultado:** Se crea el "Centro de Costos" vacío. En Bancos aparecerá con $0 en todas las columnas. A partir de aquí, todo lo que le vincules (facturas, gastos, pagos) alimentará sus números.

### Etapa 4: Emitir la Factura (Presupuesto / Contrato)

**Responsable:** Finanzas / Facturación
**Módulo:** Facturas

1. Ve a **Facturas** > **Nueva Factura**.
2. Selecciona al Cliente de la Etapa 1.
3. ⚠️ **Vincula el Proyecto:** En el campo "Proyecto", selecciona "Mantenimiento Corporativo Sur 2026".
4. Agrega los conceptos del catálogo de **Productos** o escríbelos manualmente:
    - Concepto 1: "Suministro e instalación de sistema hidráulico" — $500,000
    - Concepto 2: "Mano de obra especializada" — $200,000
    - Concepto 3: "Materiales diversos" — $100,000
5. Define impuestos (IVA 16%) y descuentos si aplican.
6. Haz clic en **Guardar**.
7. Cambia el estado a **Enviada** para que el sistema empiece a contabilizar.

**Resultado:** El "Total Facturado" del proyecto brinca de $0 a $800,000 (o el monto que hayas facturado). La "Ganancia" sigue en $0 porque aún no se cobra nada.

### Etapa 5: Operación Diaria — Registrar Gastos

**Responsable:** Residentes de Obra / Compras
**Módulo:** Gastos

Durante semanas y meses, el equipo de campo incurrirá en gastos constantes:
1. **Regla de oro para el personal de campo:** "TODO gasto debe ir al sistema antes del viernes de cada semana".
2. Por cada compra (ferretería, renta de maquinaria, nómina, gasolina, comida, subcontratos):
    - Abre **Gastos** > **Nuevo Gasto**.
    - Selecciona el **Proveedor** correcto.
    - Selecciona la **Categoría** fiscal.
    - Ingresa el **Monto** exacto.
    - Selecciona el **Proyecto** "Mantenimiento Corporativo Sur 2026". **NUNCA lo dejes en blanco.**
    - Asigna la **Fecha de Pago** si ya se pagó (transferencia, efectivo, cheque).
    - Si es factura a crédito (aún no pagas), NO pongas fecha de pago todavía. Cuando la pagues, edita el gasto y agrega la fecha.
    - Sube el **comprobante** (foto de ticket o PDF).
    - Guarda.

**Resultado progresivo:** Conforme se capturan gastos, el "Total Gastos" del proyecto sube. Si ya hay cobros registrados, la "Ganancia" baja correspondientemente, reflejando la realidad.

### Etapa 6: Cobrar Estimaciones y Avances

**Responsable:** Tesorería / Administración
**Módulo:** Facturas → Pagos

El cliente deposita $250,000 como primer anticipo:
1. Ve a la **Factura** de "Mantenimiento Corporativo Sur".
2. Haz clic en **Introducir Pago**.
3. Captura el monto exacto: $250,000.
4. Fecha de pago: la del estado de cuenta bancario.
5. Método: Transferencia Bancaria. Referencia: la clave SPEI.
6. Guarda.

**Resultado:** En Bancos, el proyecto pasa de tener $0 de "Total Pagado" a $250,000. Si ya tienes $180,000 de gastos registrados, la Ganancia será $70,000 y la Rentabilidad será 28%.

### Etapa 7: Seguimiento y Cierre de Obra

**Responsable:** Director de Obra / Gerencia
**Módulos:** Bancos, Informes, Proyectos

Cuando la obra termina:
1. Revisa en **Bancos** que el Total Pagado coincida con el Total Facturado (todo cobrado).
2. Revisa que no queden gastos pendientes de captura.
3. Genera un **Informe de Proyecto** en PDF como cierre documental.
4. Si el proyecto ya no tendrá más movimientos, puedes archivarlo en **Proyectos** > Archivar.

---

## 5. Compendio Extenso de Preguntas Frecuentes (FAQ)

### Sobre Ganancia y Rentabilidad

**Q1. La obra dice ganancia del 90% pero es imposible, gastamos muchísimo material. ¿La plataforma está mal?**
> **R:** No. Revisa tus gastos en el módulo "Gastos". Lo más probable es que el equipo capturó los tickets pero **olvidó asignarles el Proyecto correcto** en el desplegable. Si los gastos quedan "huérfanos" (sin proyecto), la obra cree que es pura utilidad. Filtra gastos, edítalos, asígnales el proyecto y verás cómo la ganancia baja al porcentaje real.

**Q2. Facturé $5 millones por un hotel. Todo está en verde. ¿Por qué Bancos dice Ganancia $0 y Rentabilidad 0.0%?**
> **R:** Porque aún no hay depósitos del cliente. La Rentabilidad opera sobre "Total Pagado" (cobro real), no sobre "Total Facturado" (deseo futuro). En cuanto registres un **Pago** contra tu Factura, verás nacer la rentabilidad.

**Q3. ¿De dónde sale el porcentaje de "Rentabilidad"?**
> **R:** Fórmula: `(Ganancia / Total Pagado) × 100`. Si te marca 56%, de cada $100 que entró al banco, $56 son utilidad neta y $44 se invirtieron en gastos de obra.

**Q4. ¿Por qué la Rentabilidad de Arabela es 56% y no 28%?**
> **R:** Porque ahora se calcula sobre lo cobrado ($545,751), no sobre lo facturado ($1,091,502). La ganancia ($305,682.59) dividida entre $545,751 = 56%. Antes se dividía entre $1,091,502 = 28%. El 56% refleja mejor la eficiencia real de tu operación con el dinero que ya tienes.

### Sobre Gastos

**Q5. Capturé 20 boletos de avión y nóminas, todos asignados al proyecto correcto, pero el Sumario dice gastos en cero. ¿Se borró la info?**
> **R:** Nada se borró. Pero no pusiste la **Fecha de Pago**. Sin ella, el sistema asume que es una deuda pendiente (aún no salió dinero del banco). Para que impacte la Ganancia, edita cada gasto y asigna la fecha en que realmente se pagó.

**Q6. ¿Puedo registrar gastos a crédito (que aún no pago)?**
> **R:** Sí. Crea el gasto normalmente pero NO asignes Fecha de Pago. El gasto aparecerá en "Gastos Pendientes" del proyecto. Cuando lo pagues, edita el gasto y agrega la fecha. En ese momento restará de la Ganancia.

**Q7. Me equivoqué de proyecto al capturar un gasto. ¿Cómo lo corrijo?**
> **R:** Ve a **Gastos**, busca el registro erróneo, haz clic en **Editar**, cambia el Proyecto al correcto y guarda. Los números de ambos proyectos se ajustan instantáneamente.

**Q8. ¿Puedo subir varios gastos de golpe o tengo que ir uno por uno?**
> **R:** El método estándar es uno por uno para asegurar que cada gasto tenga su proveedor, proyecto y fecha correctos. Sin embargo, si tu administrador tiene un CSV de gastos, puede usar la función de importación masiva si está habilitada.

### Sobre Facturas y Pagos

**Q9. ¿Qué pasa si elimino una factura del proyecto?**
> **R:** La factura eliminada deja de contar para el "Total Facturado" y el saldo deudor desaparece. Las facturas eliminadas de Arabela ("1005_Eliminado" y "1006_Eliminado") son un ejemplo: ya no impactan los números del proyecto.

**Q10. ¿Puedo tener varias facturas para un mismo proyecto?**
> **R:** Sí, es lo normal. Puedes tener una factura por el anticipo, otra por cada estimación de avance, y una final por el finiquito. Todas se suman al "Total Facturado" del proyecto siempre y cuando estén vinculadas al mismo Proyecto.

**Q11. El cliente pagó una factura pero en dos depósitos distintos (uno de $300k y otro de $200k). ¿Cómo lo registro?**
> **R:** Registra dos pagos separados contra la misma factura. Primer Pago: $300,000 con fecha del primer depósito. Segundo Pago: $200,000 con fecha del segundo depósito. La factura pasará a estado "Parcial" con el primer pago y "Pagada" con el segundo (si cubre el total).

**Q12. ¿Puedo registrar un pago sin factura?**
> **R:** No directamente. Todo pago debe estar vinculado a una factura. Si el cliente depositó sin tener factura, primero crea la factura correspondiente y luego registra el pago.

### Sobre Proyectos

**Q13. ¿Puedo tener un proyecto sin cliente?**
> **R:** Técnicamente sí, pero no es recomendable. Sin cliente, no puedes vincular facturas ni pagos, y el proyecto quedaría como un centro de costos "solo gastos" sin ingresos.

**Q14. ¿Qué hago si la misma obra tiene dos clientes diferentes?**
> **R:** Crea un proyecto por cada cliente. Si el "Edificio Reforma" tiene un cliente para la fase 1 (estructura) y otro para la fase 2 (acabados), crea dos proyectos separados para poder facturar y cobrar correctamente a cada uno.

**Q15. ¿Puedo archivar un proyecto terminado?**
> **R:** Sí. Ve al proyecto, selecciona la opción de **Archivar**. El proyecto dejará de aparecer en las listas activas pero conservará todo su historial. Puedes desarchivarlo en cualquier momento si necesitas consultarlo o si surge un trabajo extra.

### Sobre Proveedores

**Q16. ¿Qué es un "RFC" en la ficha del proveedor?**
> **R:** Es el Registro Federal de Contribuyentes. Es el identificador fiscal mexicano equivalente al NIF/CIF en España. Es obligatorio para poder deducir las facturas de compra ante el SAT.

**Q17. Un mismo proveedor nos surte material para varias obras. ¿Lo registro una sola vez?**
> **R:** Sí. El proveedor se registra una sola vez. Cuando captures gastos, seleccionas al mismo proveedor pero lo vinculas a proyectos diferentes según corresponda.

### Sobre el Módulo de Bancos

**Q18. ¿Bancos se conecta a mi cuenta bancaria real?**
> **R:** No. El módulo "Bancos" es un consolidador de la información que ya capturaste en Facturas, Pagos y Gastos. No lee tu estado de cuenta automáticamente. Su valor es que te muestra la foto real de rentabilidad por proyecto.

**Q19. ¿Por qué veo IVA Cobrado, IVA Acreditado e IVA por Pagar en Bancos?**
> **R:** Son indicadores fiscales. El IVA Cobrado es el impuesto que cobraste al cliente. El IVA Acreditado es el impuesto que tus proveedores te cobraron. El IVA por Pagar es la diferencia que debes enterarle al SAT. Estos campos te ayudan a anticipar tu obligación fiscal mensual.

### Sobre Informes y Reportes

**Q20. ¿Puedo generar un reporte PDF del estado completo de una obra?**
> **R:** Sí. Ve a **Informes** o al detalle del proyecto y genera el "Reporte de Proyecto". Incluirá el Sumario con Total Facturado, Pagado, Gastos, Ganancia, lista de facturas y lista de gastos desglosados.

**Q21. ¿Puedo comparar la rentabilidad de todas mis obras en una sola vista?**
> **R:** Sí, exactamente para eso sirve **Bancos**. Muestra una tabla con todos los proyectos activos, sus totales y rentabilidad lado a lado. Puedes identificar de inmediato cuál obra va bien y cuál se está comiendo el capital.

### Errores Críticos y Cómo Evitarlos

**Q22. ¿Cuáles son los 3 errores más destructivos que puede cometer un usuario nuevo?**
> **R:**
> 1. **Capturar gastos sin asignar Proyecto.** La obra aparenta ser más rentable de lo que es.
> 2. **Capturar gastos sin Fecha de Pago.** El gasto existe en el sistema pero no impacta la Ganancia.
> 3. **Confundir "Facturado" con "Cobrado".** Tomar decisiones de gasto basándose en lo facturado cuando el cliente aún no paga.

**Q23. ¿Existe un checklist semanal para asegurar que los datos estén al día?**
> **R:** Recomendamos esta rutina cada viernes:
> 1. ✅ Verificar que todos los tickets y facturas de la semana estén capturados en **Gastos** con Proyecto y Fecha de Pago.
> 2. ✅ Revisar en el estado de cuenta bancario si hubo depósitos de clientes y registrarlos como **Pagos**.
> 3. ✅ Entrar a **Bancos** y validar que los números hacen sentido vs. la realidad operativa.
> 4. ✅ Si se facturó algo nuevo durante la semana, verificar que la factura esté vinculada al proyecto correcto.

---

> **Última actualización:** 26 de febrero de 2026
> **Versión:** 2.0 — Manual Extendido con Caso Arabela
