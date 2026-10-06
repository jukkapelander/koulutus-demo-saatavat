-- Saatavat-demo: skeema. HUOM: tämä on koulutuskäyttöön tehty demo, ei tuotantomalli.

DROP TABLE IF EXISTS reminders;
DROP TABLE IF EXISTS payments;
DROP TABLE IF EXISTS invoices;
DROP TABLE IF EXISTS companies;

-- Velkojat eli API:n asiakasyritykset
CREATE TABLE companies (
    id              SERIAL PRIMARY KEY,
    name            TEXT NOT NULL,
    business_id     TEXT NOT NULL,                         -- Y-tunnus
    api_key         TEXT NOT NULL UNIQUE,
    interest_rate   NUMERIC(5,2) NOT NULL DEFAULT 11.50    -- viivästyskorko, % vuodessa
);

-- Velkojan laskut (saatavat)
CREATE TABLE invoices (
    id              SERIAL PRIMARY KEY,
    company_id      INTEGER NOT NULL REFERENCES companies(id),
    invoice_number  TEXT NOT NULL,
    customer_name   TEXT NOT NULL,                         -- velallinen
    customer_email  TEXT,
    customer_phone  TEXT,
    debtor_type     TEXT NOT NULL CHECK (debtor_type IN ('consumer', 'business')),
    amount          NUMERIC(12,2) NOT NULL,                -- pääoma
    issue_date      DATE NOT NULL,
    due_date        DATE NOT NULL,
    status          TEXT NOT NULL DEFAULT 'open',          -- open | paid | collection
    reference       TEXT                                   -- viitenumero
);

-- Suoritukset
CREATE TABLE payments (
    id              SERIAL PRIMARY KEY,
    invoice_id      INTEGER NOT NULL REFERENCES invoices(id),
    amount          NUMERIC(12,2) NOT NULL,
    paid_at         DATE NOT NULL,
    bank_archive_id TEXT,                                  -- pankin arkistointitunnus
    created_at      TIMESTAMP NOT NULL DEFAULT now()
);

-- Maksumuistutukset ja maksuvaatimukset
CREATE TABLE reminders (
    id              SERIAL PRIMARY KEY,
    invoice_id      INTEGER NOT NULL REFERENCES invoices(id),
    kind            TEXT NOT NULL CHECK (kind IN ('reminder', 'demand')),
    fee             NUMERIC(8,2) NOT NULL,
    sent_at         DATE NOT NULL
);

CREATE INDEX idx_invoices_company ON invoices(company_id);
CREATE INDEX idx_payments_invoice ON payments(invoice_id);
CREATE INDEX idx_reminders_invoice ON reminders(invoice_id);
