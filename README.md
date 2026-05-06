# Kunno Tech Test

Bienvenido/a a la prueba técnica de Kunno. Este ejercicio evalúa tu capacidad para implementar lógica de negocio en Laravel, trabajar con jobs asíncronos y escribir código limpio y testeable.

## Contexto

Kunno es una plataforma que gestiona operaciones financieras entre agencias. Cada operación genera una comisión que debe calcularse y procesarse de forma asíncrona.

## Objetivo

Implementar un sistema de comisiones para operaciones entre agencias, compuesto por **3 módulos** descritos en [`MODULES.md`](MODULES.md).

## Requisitos

- PHP 8.1+
- Laravel 10+
- MySQL 8+ o SQLite (para tests)
- Composer

## Instalación

```bash
git clone <tu-fork>
cd kunno-tech-test
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
```

## Estructura

```
routes/api.php                              ← 3 rutas definidas, sin implementar
database/migrations/create_agencies_table  ← Ya creada, no tocar
database/seeders/DatabaseSeeder.php        ← 2 agencias + operación de prueba
app/Models/Operation.php                   ← Esqueleto vacío
app/Services/CommissionService.php         ← TODO con guía de pasos
app/Jobs/ProcessCommissionPayment.php      ← Esqueleto vacío
app/Http/Controllers/OperationController  ← 3 métodos vacíos
tests/Feature/CommissionTest.php           ← 9 tests listos
```

## Cómo entregar

1. Haz un fork de este repositorio
2. Implementa los módulos descritos en `MODULES.md`
3. Asegúrate de que los 9 tests pasen: `php artisan test`
4. Abre un Pull Request con una descripción de tus decisiones técnicas

## Evaluación

| Criterio | Peso |
|---|---|
| Tests en verde | 40% |
| Lógica de comisiones correcta | 25% |
| Calidad y limpieza del código | 20% |
| Manejo de errores y edge cases | 15% |

## Tiempo estimado

2–3 horas. No buscamos perfección, buscamos razonamiento claro.

---

¿Dudas? Escribe a [tech@kunno.com](mailto:tech@kunno.com)
