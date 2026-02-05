# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Build & Development Commands

### Development
```bash
composer dev          # Start dev server (Laravel + queue + logs + Vite in parallel)
composer dev:ssr      # Development with SSR
composer setup        # Full project setup (install, migrate, build)
```

### Testing
```bash
composer test         # Full test suite (clear config + lint check + tests)
./vendor/bin/pest     # Run Pest tests directly
./vendor/bin/pest --filter=TestName  # Run specific test
```

### Linting & Formatting
```bash
composer lint                        # PHP formatting with Pint
./vendor/bin/phpstan analyse -l 8 app  # PHPStan level 8 analysis
npm run lint                         # ESLint + fix
npm run format                       # Prettier formatting
```

### Docker
```bash
docker-compose up     # Full stack (MySQL on port 3307)
```

## Architecture

**Stack:** Laravel 12 + Inertia.js + Vue 3 + TypeScript + Tailwind CSS v4

### Backend Structure

Domain modules with Contract-based design:
- `app/Cart/` - Cart service with Contracts, DTOs, Adapters (uses riesenia/cart)
- `app/Address/` - Address lookup with Clients (Swiftyper API), Contracts, DTOs

Service bindings are in `AppServiceProvider`.

### Frontend Structure

- `resources/js/pages/` - Inertia page components
- `resources/js/components/` - Reusable Vue components
- `resources/js/actions/` - Wayfinder-based routing/actions
- `resources/js/types/` - TypeScript definitions

### Key Patterns

- Strong typing: PHPStan level 8, TypeScript strict
- DTOs for data transfer between layers
- Interfaces (Contracts) for dependency injection
- Laravel Precognition for real-time form validation
- Inertia props for server-to-client data

## Testing

Uses Pest PHP with helpers in `tests/Pest.php`:
- `createProduct()` - Factory helper
- `createOrder()` - Factory helper

Feature tests use database refresh with SQLite in-memory.

## External Services

- **Swiftyper API** - Slovak address autocomplete (configured via `SWIFTYPER_API_TOKEN` in .env)
