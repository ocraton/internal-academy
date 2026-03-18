# Internal Academy

Piattaforma interna per la gestione e iscrizione ai workshop aziendali, costruita con Laravel 13, Inertia.js v2 e Vue 3.

## Requisiti

- [Docker Desktop](https://www.docker.com/products/docker-desktop/) — tutto il resto (PHP, Composer, Node, MySQL) gira nei container tramite Laravel Sail.

## Installazione

**1. Clona il progetto e copia il file di configurazione:**

```bash
git clone <repo>
cd internal-academy
cp .env.example .env
```

**2. Installa le dipendenze PHP senza avere PHP o Composer in locale:**

Se non hai PHP/Composer installati sulla macchina, usa il container ufficiale di Sail per eseguire `composer install`:

```bash
docker run --rm -it -v $(pwd):/app -w /app laravelsail/php84-composer bash
composer install
exit
```

**3. Avvia i container e configura l'applicazione:**

```bash
vendor/bin/sail up -d
vendor/bin/sail artisan key:generate
vendor/bin/sail artisan migrate
vendor/bin/sail artisan db:seed
vendor/bin/sail npm install
vendor/bin/sail npm run build
```

## Eseguire i test

```bash
# Tutti i test
vendor/bin/sail artisan test --compact

# Solo test specifici
vendor/bin/sail artisan test --compact --filter=NomeTest

# Con coverage (richiede Xdebug)
vendor/bin/sail artisan test --compact --coverage
```

## Generare i dati di test

```bash
vendor/bin/sail artisan migrate:fresh --seed
```

## Credenziali di default (dopo il seeder)

| Ruolo    | Email                       | Password |
|----------|-----------------------------|----------|
| Admin    | `admin@academy.test`        | password |
| Employee | `employee1@academy.test`    | password |
| Employee | `employee2@academy.test`    | password |
| Employee | `employee3@academy.test`    | password |
| Employee | `employee4@academy.test`    | password |
| Employee | `employee5@academy.test`    | password |

## Comando reminder

Invia le email di promemoria agli iscritti ai workshop del giorno successivo. Le email vengono spedite tramite coda, quindi è necessario avere il worker attivo in un terminale separato.

**1. Avvia il queue worker (terminale separato):**

```bash
vendor/bin/sail artisan queue:work
```

**2. Esegui il comando reminder:**

```bash
vendor/bin/sail artisan academy:remind
```

