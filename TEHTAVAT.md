# Tehtävät

Jokainen tehtävä on kirjoitettu tiketin muotoon, kuten se tulisi tiimille oikeasti. Tehtävän
tulos on pull request, jonka kuvauksessa on: mitä muutettiin, miten se varmennettiin (testituloste
tai muu todiste) ja missä AI auttoi. Kouluttaja kertoo, mitkä tehtävät tehdään.

Aloita jokainen tehtävä lukemalla `README.md`:n laskentasäännöt. Ne ovat tämän sovelluksen
spesifikaatio.

## T1 – Viivästyskorko lasketaan yhdeltä päivältä liikaa

Asiakas Pohjolan Pakkaus Oy reklamoi: laskulle INV-2026-001 (eräpäivä 15.6.2026) laskettiin
25.6.2026 maksetusta suorituksesta korkoa 4,33 €, mutta asiakkaan oman laskelman mukaan korko
on 3,94 €. Selvitä kumpi on oikeassa ja korjaa tarvittaessa. Testien on oltava vihreät.

## T2 – Haku kaatuu, kun asiakkaan nimessä on heittomerkki

Haku `GET /api/invoices?q=O'Brien` palauttaa virheen 500. Asiakkaalla O'Brien & Pojat Oy on
avoin lasku, joka pitää löytyä haulla. Korjaa haku niin, että se toimii kaikilla nimillä.

## T3 – Lasku INV-2026-042 ei vaihdu maksetuksi

Rovaniemen Rengas Oy on maksanut laskun INV-2026-042 kolmessa erässä, ja suoritusten summa
täsmää pääomaan 698,46 €. Laskun tila on silti `open`, ja asiakas saa aiheettomia muistutuksia.
Selvitä syy ja korjaa.

## T4 – Pankin maksuaineiston tuonti

Toteuta skripti `bin/import_payments.php`, joka lukee tiedoston `fixtures/payments_import.csv`
ja kirjaa suoritukset oikeille laskuille viitenumeron perusteella. Skriptin pitää tulostaa
yhteenveto: montako suoritusta kirjattiin ja mitkä rivit jäivät käsittelemättä ja miksi. Tuonti
pitää voida ajaa uudelleen ilman, että suorituksia kirjautuu kahteen kertaan.

## T5 – Maksuvaatimuksen kulu väärin tasan 100 euron laskulla

Kuluttaja-asiakas Maija Meikäläinen (lasku INV-2026-003, pääoma 100,00 €) sai maksuvaatimuksen,
jonka kulu oli 24,00 €. Asiakkaan mukaan kulu saa olla enintään 14,00 €. Selvitä ja korjaa.

## T6 – Tietoturvakatselmointi ennen kumppani-integraatiota

API avataan ulkopuoliselle kumppanille kahden viikon päästä. Tee tietoturvakatselmointi ja
kirjaa löydökset vakavuusjärjestyksessä. Korjaa vakavimmat; muista tee tiketit (lyhyt kuvaus
riittää). Käytä apuna OWASP Top 10 -listaa.

## T7 – Datan laatutarkistus ennen migraatiota

Testiaineisto migroidaan uuteen järjestelmään. Kirjoita skripti `bin/data_quality.php`, joka
raportoi laskuista, suorituksista ja asiakasyrityksistä kaikki poikkeamat, jotka estäisivät
puhtaan migraation. Perustele jokainen tarkistus yhdellä lauseella.
