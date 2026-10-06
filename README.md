# Saatavat-demo

> **VAROITUS: Tämä on koulutuskäyttöön tehty demosovellus, jossa on tarkoituksella puutteita.
> Älä ota sitä tuotantokäyttöön äläkä käytä siinä oikeaa dataa.** Kaikki yritykset, henkilöt,
> Y-tunnukset, yhteystiedot ja avaimet ovat keksittyjä.

Pieni PHP 8.3 + PostgreSQL -palvelu laskusaatavien hallintaan: laskut, suoritukset,
viivästyskorko ja perintäkulut. Sovellus on tehty harjoitusalustaksi agenttiseen
ohjelmistokehitykseen: tehtävät löytyvät tiedostosta `TEHTAVAT.md`.

## Pika-aloitus

Vaihtoehto A – tietokanta Dockerissa, PHP omalla koneella (WSL2/Linux/macOS):

```bash
docker compose up -d db                     # PostgreSQL 16 + skeema + testiaineisto
sudo apt install php-cli php-pgsql          # Debian/Ubuntu/WSL2; macOS: brew install php
php tests/run.php                           # kaikkien testien pitäisi mennä läpi
php -S 127.0.0.1:8080 -t public             # kehityspalvelin
```

Vaihtoehto B – kaikki Dockerissa:

```bash
docker compose up --build                   # db + app, API osoitteessa http://localhost:8080
docker compose exec app php tests/run.php
```

Jos tietokanta on jo olemassa ilman aineistoa: `php bin/migrate.php` luo skeeman ja lataa
testiaineiston uudelleen (tuhoaa nykyiset taulut). Yhteysasetukset luetaan `.env`-tiedostosta.

## Laskentasäännöt

Nämä säännöt ovat demon oma spesifikaatio. Ne ovat yksinkertaistettuja eivätkä juridinen ohje.

**Viivästyskorko.** Korkoa kertyy eräpäivää seuraavasta päivästä maksupäivään asti, molemmat
päivät mukaan lukien. Vuodessa on 365 päivää. Korko = pääoma × vuosikorko × päivät / 365,
pyöristys sentteihin vasta lopuksi. Vuosikorko luetaan velkojan asetuksista (`companies.interest_rate`).

**Perintäkulut.** Maksumuistutuksen kulu on 5,00 €, ja muistutuksia lähetetään enintään kaksi
laskua kohden. Maksuvaatimuksen kulu kuluttajasaatavalla määräytyy pääoman mukaan: enintään
100,00 € pääomasta 14,00 €, enintään 1 000,00 € pääomasta 24,00 € ja sitä suuremmasta 50,00 €.
Maksuvaatimuksia lähetetään enintään kaksi laskua kohden. Kuluttajasaatavan kulujen
kokonaiskatto on 60 € / 120 € / 210 € samoilla pääomarajoilla. Yrityssaatavalla maksuvaatimuksen
kulu on kiinteä 40,00 € eikä kokonaiskattoa ole.

**Suoritukset.** Suorituksen summan on oltava suurempi kuin nolla. Sama pankin arkistointitunnus
kirjataan vain kerran. Lasku on maksettu, kun suoritusten summa on sentin tarkkuudella yhtä suuri
kuin pääoma. Ylisuoritus ei saa merkitä laskua hiljaisesti maksetuksi: se merkitään tilaan
`overpaid` tai hylätään, ja tapaus raportoidaan.

**Pääsynhallinta.** Jokainen asiakasyritys (velkoja) näkee ja muokkaa vain omia laskujaan.
Tunnistus tehdään `X-Api-Key`-otsakkeella.

## API

Kaikki pyynnöt `/api/health`-reittiä lukuun ottamatta vaativat otsakkeen `X-Api-Key`.
Testiaineiston avaimet: `demo-key-pohjola`, `demo-key-tammerkoski`, `demo-key-kymi`.

| Metodi | Reitti | Kuvaus |
| --- | --- | --- |
| GET | `/api/health` | Elossaolotarkistus |
| GET | `/api/invoices` | Velkojan laskut; `?q=nimi` hakee asiakkaan nimellä |
| GET | `/api/invoices/{id}` | Lasku suorituksineen ja muistutuksineen |
| PATCH | `/api/invoices/{id}` | Päivitä laskun kenttiä (JSON-runko) |
| POST | `/api/invoices/{id}/payments` | Kirjaa suoritus: `amount`, `paid_at`, `bank_archive_id` |
| GET | `/api/invoices/{id}/interest?as_of=YYYY-MM-DD` | Viivästyskorko annettuun päivään |
| POST | `/api/invoices/{id}/reminders` | Lähetä muistutus tai maksuvaatimus: `kind` = `reminder` / `demand` |

Esimerkkejä:

```bash
curl -H "X-Api-Key: demo-key-pohjola" localhost:8080/api/invoices
curl -H "X-Api-Key: demo-key-pohjola" localhost:8080/api/invoices/1001
curl -H "X-Api-Key: demo-key-pohjola" "localhost:8080/api/invoices/1001/interest?as_of=2026-06-25"
curl -X POST -H "X-Api-Key: demo-key-pohjola" -H "Content-Type: application/json" \
  -d '{"amount": 100.00, "paid_at": "2026-10-01", "bank_archive_id": "B2026-0100"}' \
  localhost:8080/api/invoices/1001/payments
```

## Testit

```bash
php tests/run.php
```

Testiajuri on riippuvukseton: `tests/*Test.php` määrittelee `test_`-alkuisia funktioita, ajuri
suorittaa ne ja palauttaa poistumiskoodin 1, jos jokin epäonnistuu. Integraatiotestit tarvitsevat
käynnissä olevan tietokannan.

## Rakenne

```
public/index.php          reititys
src/                      sovelluslogiikka
db/schema.sql, seed.sql   skeema ja testiaineisto
bin/                      ylläpitoskriptit
tests/                    testit
fixtures/                 tuontiaineistot
```
