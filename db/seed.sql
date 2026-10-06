-- Saatavat-demo: testiaineisto. Kaikki yritykset, henkilöt, Y-tunnukset ja yhteystiedot ovat keksittyjä.

INSERT INTO companies (id, name, business_id, api_key, interest_rate) VALUES
    (1, 'Pohjolan Pakkaus Oy',   '2345678-0', 'demo-key-pohjola',     11.50),
    (2, 'Tammerkosken Tili Ky',  '3456781-4', 'demo-key-tammerkoski', 11.50),
    (3, 'Kymen Kuljetus Oy',     '1122334-7', 'demo-key-kymi',         0.00);

INSERT INTO invoices (id, company_id, invoice_number, customer_name, customer_email, customer_phone, debtor_type, amount, issue_date, due_date, status, reference) VALUES
    -- Pohjolan Pakkaus Oy
    (1001, 1, 'INV-2026-001', 'Kuusamon Kahvila Oy',   'laskut@kuusamonkahvila.example',  '+358 40 1234501', 'business',  1250.00, '2026-06-01', '2026-06-15', 'open', '1000012'),
    (1002, 1, 'INV-2026-002', 'Matti Mallikas',        'matti.mallikas@example.com',      '+358 40 1234502', 'consumer',    89.90, '2026-06-03', '2026-06-17', 'open', '1000025'),
    (1003, 1, 'INV-2026-003', 'Maija Meikäläinen',     'maija.meikalainen@example.com',   '+358 40 1234503', 'consumer',   100.00, '2026-06-05', '2026-06-19', 'open', '1000038'),
    (1004, 1, 'INV-2026-004', 'O''Brien & Pojat Oy',   'talous@obrien-pojat.example',     '+358 40 1234504', 'business',  4300.00, '2026-06-10', '2026-07-10', 'open', '1000041'),
    (1005, 1, 'INV-2026-005', 'Kuusamon Kahvila Oy',   'laskut@kuusamonkahvila.example',  '+358 40 1234501', 'business',   540.00, '2026-06-12', '2026-06-26', 'paid', '1000054'),
    (1006, 1, 'INV-2026-005', 'Lahden Leipä Oy',       'info@lahdenleipa.example',        '+358 40 1234506', 'business',   720.00, '2026-06-15', '2026-06-29', 'open', '1000067'),
    (1007, 1, 'INV-2026-007', 'Sari Sivistynyt',       'sari.sivistynyt@example.com',     '+358 40 1234507', 'consumer',    65.00, '2026-06-20', '2026-06-10', 'open', '1000070'),
    (1042, 1, 'INV-2026-042', 'Rovaniemen Rengas Oy',  'rengas@rovaniemenrengas.example', '+358 40 1234542', 'business',   698.46, '2026-07-01', '2026-07-15', 'open', '1000083'),
    (1009, 1, 'INV-2026-009', 'Turun Tukku Oy',        'ostot@turuntukku.example',        '+358 40 1234509', 'business',  2000.00, '2026-07-02', '2026-07-16', 'paid', '1000096'),
    (1010, 1, 'INV-2026-010', 'Pekka Puhelin',         'pekka.puhelin@example.com',       '+358 40 1234510', 'consumer',   -45.00, '2026-08-02', '2026-08-16', 'open', '1000106'),
    (1011, 1, 'INV-2026-011', 'Helsingin Hieronta Ky', 'hieronta@example.com',            '+358 40 1234511', 'business',   310.00, '2026-08-05', '2026-08-19', 'paid', '1000119'),
    (1012, 1, 'INV-2026-012', 'Oulun Optiikka Oy',     'oulun.optiikka@example',          '0401234512',      'business',   150.00, '2026-08-10', '2026-08-24', 'paid', '1000122'),
    -- Tammerkosken Tili Ky
    (2001, 2, 'LASKU-77',     'Pirkanmaan Putki Oy',   'putki@pirkanmaanputki.example',   '+358 50 2000001', 'business',   980.00, '2026-07-01', '2026-07-31', 'open', '2000011'),
    (2002, 2, 'LASKU-78',     'Ville Vuokralainen',    'ville.vuokralainen@example.com',  '+358 50 2000002', 'consumer',  1000.00, '2026-07-03', '2026-08-02', 'open', '2000024'),
    (2003, 2, 'LASKU-79',     'Nokian Nostot Oy',      'laskutus@nokiannostot.example',   '+358 50 2000003', 'business', 15400.00, '2026-07-10', '2026-08-09', 'collection', '2000037'),
    (2004, 2, 'LASKU-80',     'Anna Asiakas',          'anna.asiakas@example.com',        '+358 50 2000004', 'consumer',   250.00, '2026-07-15', '2026-07-29', 'open', '2000040'),
    -- Kymen Kuljetus Oy
    (3001, 3, 'K-1',          'Kotkan Kone Oy',        'kone@kotkankone.example',         '+358 44 3000001', 'business',  3200.00, '2026-05-01', '2026-05-31', 'open', '3000010'),
    (3002, 3, 'K-2',          'Kaisa Kuluttaja',       'kaisa.kuluttaja@example.com',     '+358 44 3000002', 'consumer',    48.00, '2026-05-02', '2026-05-16', 'open', '3000023'),
    (3003, 3, 'K-3',          'Haminan Huolto Oy',     'huolto@haminanhuolto.example',    '+358 44 3000003', 'business',  1100.00, '2026-05-10', '2026-06-09', 'open', '3000036');

INSERT INTO payments (invoice_id, amount, paid_at, bank_archive_id) VALUES
    (1005,  540.00, '2026-06-24', 'A1005'),
    (1042,  484.90, '2026-07-05', 'A1042-1'),
    (1042,  139.53, '2026-07-08', 'A1042-2'),
    (1042,   74.03, '2026-07-14', 'A1042-3'),
    (1009, 2500.00, '2026-08-01', 'A1009'),
    (1011,  310.00, '2026-07-30', 'A1011'),
    (1012,   75.00, '2026-08-20', 'A1012-1'),
    (1012,   75.00, '2026-08-20', 'A1012-1');

INSERT INTO reminders (invoice_id, kind, fee, sent_at) VALUES
    (1002, 'reminder',  5.00, '2026-06-25'),
    (1002, 'demand',   14.00, '2026-07-10'),
    (1003, 'reminder',  5.00, '2026-06-27'),
    (2003, 'reminder',  5.00, '2026-08-17'),
    (2003, 'demand',   40.00, '2026-09-01'),
    (2004, 'reminder',  5.00, '2026-08-05'),
    (2004, 'reminder',  5.00, '2026-08-12'),
    (2004, 'demand',   24.00, '2026-08-26'),
    (2004, 'demand',   24.00, '2026-09-09'),
    (2004, 'demand',   24.00, '2026-09-23');

SELECT setval('companies_id_seq', (SELECT max(id) FROM companies));
SELECT setval('invoices_id_seq',  (SELECT max(id) FROM invoices));
