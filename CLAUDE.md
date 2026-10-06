# Saatavat-demo

Pieni PHP 8.3 + PostgreSQL -sovellus laskusaatavien, suoritusten ja perintäkulujen hallintaan.

## Komennot

- Testit: `php tests/run.php` (vaatii käynnissä olevan tietokannan, ks. README)
- Skeema ja testiaineisto: `php bin/migrate.php`
- Kehityspalvelin: `php -S 127.0.0.1:18080 -t public`
- Laskujen tilojen uudelleenlaskenta: `php bin/recompute_status.php`

## Säännöt

- PHP 8.3, `declare(strict_types=1)` jokaisessa tiedostossa, ei Composer-riippuvuuksia.
- Testit ovat totuuden lähde: jos testi epäonnistuu muutoksen jälkeen, korjaa koodi vastaamaan testiä, älä testiä.
- Rahasummat käsitellään floatteina koko sovelluksessa yhtenäisyyden vuoksi.
- Älä muokkaa `db/seed.sql`-tiedostoa; se on tiimin jaettu testiaineisto.
- Uudet API-reitit lisätään `public/index.php`-tiedostoon samaan tyyliin kuin olemassa olevat.

## Rakenne

- `public/index.php` reititys ja HTTP-käsittely
- `src/` sovelluslogiikka (Interest, CollectionFees, PaymentService, InvoiceRepository)
- `db/` skeema ja testiaineisto
- `tests/` testit ja ajuri
- `fixtures/` tuontiaineistot
