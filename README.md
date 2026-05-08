# Kunno Tech Test — Backend Developer Senior

## Requisitos del sistema

| Requisito | Version minima | Recomendado |
|-----------|---------------|-------------|
| PHP | 8.1+ | 8.2 |
| Composer | 2.x | 2.x |
| Laravel | 10.x | 10.x |
| Base de datos | SQLite (incluido) | SQLite |

> **IMPORTANTE:** Los tests usan SQLite en memoria. No necesitas instalar MySQL.

---

## Setup rapido — 3 pasos

### Windows
```bat
REM 1. Clonar el repo
git clone <repo-url>
cd kunno-tech-test

REM 2. Correr el script de setup
setup.bat

REM 3. Empezar la prueba cuando veas los tests en INCOMPLETE
```

### Mac / Linux
```bash
git clone <repo-url>
cd kunno-tech-test
chmod +x setup.sh
./setup.sh
```

### Manual (si los scripts fallan)
```bash
composer install
cp .env.example .env
php artisan key:generate
touch database/kunno_test.sqlite
php artisan test --filter CommissionTest
```

---

## Si tienes problemas de setup

### "php no se reconoce como comando" en Windows
Instala **Laragon** (recomendado para Windows):
- Descarga: https://laragon.org/download/
- Incluye PHP 8.2, Composer y MySQL preconfigurados
- Abre la terminal de Laragon y ejecuta `setup.bat` desde ahi

### "Could not find a composer.json file"
Estas en la carpeta equivocada. Verifica que estes dentro de `kunno-tech-test/`:
```bat
cd kunno-tech-test
dir composer.json
```
Debe mostrar el archivo. Si no, ve un nivel arriba y vuelve a entrar.

### "composer install" tarda mucho
Normal la primera vez — descarga ~40MB de dependencias. Espera 2-5 min.

---

## Lo que ya existe — NO modificar

| Archivo | Descripcion |
|---------|-------------|
| `database/migrations/..._create_agencies_table.php` | Tabla base — ya creada |
| `database/seeders/DatabaseSeeder.php` | Datos de prueba |
| `routes/api.php` | Rutas definidas — implementar controladores |
| `tests/Feature/CommissionTest.php` | 9 tests que deben pasar |
| `phpunit.xml` | Configuracion de tests con SQLite en memoria |

---

## Lo que debes construir

### Modulo 1 — Modelo de datos (15 min)
Migraciones y modelos para:
- `operations` — operaciones inmobiliarias
- `commission_participants` — participantes y porcentajes
- `commission_logs` — historial auditable de calculos

### Modulo 2 — CommissionService (20 min)
Implementar `app/Services/CommissionService.php`

### Modulo 3 — Resiliencia de pagos (10 min)
Job `ProcessCommissionPayment` con reintentos

Ver detalle completo en **MODULES.md**

---

## Verificacion de que esta todo listo

```bash
php artisan test --filter CommissionTest
```

Resultado esperado al iniciar (ANTES de implementar):
```
FAILED  Tests\Feature\CommissionTest
  - it_has_the_required_tables          -> tablas no existen aun
  - it_calculates_commissions_correctly -> no implementado
  ...
```

Resultado esperado al TERMINAR:
```
PASS  Tests\Feature\CommissionTest
  v it_has_the_required_tables
  v it_calculates_commissions_correctly
  ...
```

---

## Reglas

- Tiempo: 60 minutos
- IA permitida: Claude, Copilot, Gemini, ChatGPT
- Pantalla compartida: obligatorio durante toda la sesion
- No modificar: archivos existentes marcados como "NO modificar"
