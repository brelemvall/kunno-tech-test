# Kunno Tech Test

Bienvenido/a a la prueba técnica de Kunno. Este ejercicio evalúa tu capacidad para implementar lógica de negocio en Laravel, trabajar con jobs asíncronos y escribir código limpio y testeable.

## Contexto

Kunno es una plataforma que gestiona operaciones financieras entre agencias. Cada operación genera una comisión que debe calcularse y procesarse de forma asíncrona.

## Objetivo

Implementar un sistema de comisiones para operaciones entre agencias inmobiliarias / comercializadoras, compuesto por **3 módulos** descritos en [`MODULES.md`](MODULES.md).

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
routes/api.php                              
database/migrations/create_agencies_table 
database/seeders/DatabaseSeeder.php        
app/Models/Operation.php                   
app/Services/CommissionService.php        
app/Jobs/ProcessCommissionPayment.php      
app/Http/Controllers/OperationController  
tests/Feature/CommissionTest.php          
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
¿Dudas? Escribe a [blemus@kunno.cloud / cmartinez@kunno.cloud]

