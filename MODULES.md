# Módulos Técnicos

## Módulo 1 — Modelo y Migración de Operaciones

Crea la migración y el modelo `Operation` con los siguientes campos:

| Campo | Tipo | Descripción |
|---|---|---|
| `id` | bigIncrements | PK |
| `agency_id` | foreignId | Agencia que origina la operación |
| `amount` | decimal(12,2) | Monto de la operación |
| `currency` | string(3) | Código ISO 4217 (ej. `EUR`, `USD`) |
| `commission_rate` | decimal(5,4) | Tasa aplicada (ej. `0.0250` = 2.5%) |
| `commission_amount` | decimal(12,2) | nullable — se llena al procesar |
| `status` | enum | `pending`, `processed`, `failed` |
| `processed_at` | timestamp | nullable |
| `timestamps` | — | created_at / updated_at |

**Requisitos del modelo:**
- Cast de `amount`, `commission_rate` y `commission_amount` a `decimal:2`
- Scope `pending()` que filtre por `status = pending`
- Relación `belongsTo` con `Agency`

---

## Módulo 2 — Servicio de Comisiones (`CommissionService`)

Implementa `app/Services/CommissionService.php` con dos métodos públicos:

### `calculate(Operation $operation): float`

Calcula el monto de comisión aplicando la tasa de la operación:

```
commission_amount = amount × commission_rate
```

Reglas:
- Redondear a 2 decimales (usar `round()` o `bcmul`)
- Lanzar `\InvalidArgumentException` si `amount <= 0`
- Lanzar `\InvalidArgumentException` si `commission_rate` está fuera del rango `[0.0001, 0.9999]`

### `process(Operation $operation): void`

Orquesta el procesamiento completo:

1. Llama a `calculate()` y guarda el resultado en `commission_amount`
2. Actualiza `status` a `processed`
3. Registra `processed_at` con la fecha/hora actual
4. Persiste los cambios con `save()`
5. En caso de excepción, actualiza `status` a `failed` y relanza la excepción

---

## Módulo 3 — Job Asíncrono (`ProcessCommissionPayment`)

Implementa `app/Jobs/ProcessCommissionPayment.php`:

- Debe usar la interfaz `ShouldQueue`
- Recibe una instancia de `Operation` en el constructor
- En el método `handle()`, inyecta `CommissionService` y llama a `process()`
- Configura **3 intentos máximos** (`$tries = 3`)
- Configura **backoff de 60 segundos** entre reintentos (`$backoff = 60`)
- Si falla tras los 3 intentos, loguear el error con `Log::error()`

### Endpoint que despacha el job

`POST /api/operations/{operation}/process` debe:
1. Buscar la operación o retornar 404
2. Validar que `status === pending` (retornar 422 si no lo es)
3. Despachar `ProcessCommissionPayment::dispatch($operation)`
4. Retornar `202 Accepted` con `{ "message": "Processing queued" }`

---

## Endpoints requeridos

| Método | Ruta | Descripción |
|---|---|---|
| `GET` | `/api/operations` | Lista todas las operaciones |
| `POST` | `/api/operations` | Crea una nueva operación |
| `POST` | `/api/operations/{operation}/process` | Despacha el job de procesamiento |

### Payload para crear operación

```json
{
  "agency_id": 1,
  "amount": 1500.00,
  "currency": "EUR",
  "commission_rate": 0.025
}
```

### Validaciones requeridas

- `agency_id`: requerido, debe existir en tabla `agencies`
- `amount`: requerido, numérico, mayor que 0
- `currency`: requerido, string de exactamente 3 caracteres
- `commission_rate`: requerido, numérico, entre 0.0001 y 0.9999
