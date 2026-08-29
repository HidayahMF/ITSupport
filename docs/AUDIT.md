# Audit & Known Issues

Status of the repository as of this audit pass. Everything here was verified against
the source code. Status: 🟢 verified working · 🟡 partial / needs attention ·
🔴 broken / missing.

This file intentionally references **no** credentials, phone numbers, or private
infrastructure addresses.

## Security

- 🟢 **Hardcoded Gemini key removed from source.** `SystemRequest.php` no longer contains
  an API key; the key is read from `GEMINI_API_KEY` (env) or
  `application/config/itsupport.php` (gitignored). Verification: full-tree secret scan
  finds no `AIza…`/`AQ.…`-style keys in tracked files.
- 🔴 **The previously committed key is still in Git history.** Removing the key from the
  working tree does **not** revoke it. The original credential must be **revoked/rotated**
  in Google AI Studio / Google Cloud. Do not reuse it.
- 🟢 **WhatsApp gateway URL + recipients moved to config.** `Bantuan.php` reads them from
  `wa_gateway_url` / `wa_recipients` (env or `itsupport.php`). When unset, WhatsApp
  notifications are skipped but the ticket is still saved.
- 🟢 **cURL SSL verification re-enabled** for the Gemini calls (previously
  `CURLOPT_SSL_VERIFYPEER => false`).
- 🟡 **No authentication / authorization / CSRF.** Every endpoint is public.
  `csrf_protection = FALSE`, `global_xss_filtering = FALSE`, empty `encryption_key`,
  `cookie_secure = FALSE`. A login + role layer is the largest remaining security gap.
- 🟡 **Upload validation is minimal.** `Bantuan::simpan` accepts any file, stores a
  base64 capture as `.jpg` without MIME verification, and sizes/dir permissions were
  only partially hardened (directories now 0755). Add type/size allow-lists.
- 🟡 **Verbose logging.** `log_threshold = 4` and some model code logs full payloads at
  error level — noisy in shared logs.
- 🟡 **Debug endpoint** `systemrequest/cek_model` is public.

## Functional fixes applied

| Issue | Status | Fix |
| --- | --- | --- |
| Dead `inventorybarang/{tambah,simpan,edit,update,hapus,detail,lihat}` routes → 404 | 🟢 fixed | Removed from `routes.php`; orphaned views `tambah.php`/`edit.php`/`detail.php` deleted |
| `update_penerimaan()` passed `nama_barang` to `get_qty_from_pr()` (expected `material_id`) → PR qty resolved to 0, edits rejected | 🟢 fixed | Now passes `material_id` |
| `terimaKasih.php`: `imgSrc` used before definition → `ReferenceError`, floating images never render | 🟢 fixed | `const imgSrc` restored |
| Dead `lihatDetail()` / `renderTracking()` / detail modal / unused `inventoryListCache` in inventory `index.php` | 🟢 fixed | Removed |
| `export_excel()` fatal when `vendor/autoload.php` (PHPExcel) absent | 🟢 fixed | Falls back to `export_excel_simple()` |

## Verified infrastructure facts

- **Two databases.** `default` = MySQL (`mysqli`), `sqlServer` = SQL Server (PDO
  `sqlsrv`). In a fresh checkout both need to exist and match the table/column names
  used in the models.
- **No migrations or seeders.** The schema for `inventory_*`, `IT_SYSTEM_REQUEST`,
  `IOT_SENSOR_LOG`, and the helpdesk tables is not versioned in this repository.
- **No automated deployment config** (no Docker / CI / server provisioning in-repo);
  deployment is manual Apache + PHP.
- **Excel export** depends on the abandoned PHPExcel package via composer `vendor/`.
  It works only after `composer install` and is a compatibility risk on modern PHP;
  the fallback covers the missing-dependency case.

## Remaining limitations

- 🟡 **Inventory scope is hardcoded** to department `0800` and PR year suffix; the list
  of assignable IT staff NIPs is hardcoded in `SystemRequest`. Consider externalizing
  to config.
- 🟡 **`inventory_barang.tanggal_pr` is set to the creation timestamp**, not the real PR
  date, so lead-time can be inaccurate for auto-created rows.
- 🟡 **No audit trail / activity logging** for who created/edited inventory lines or
  changed ticket statuses.
- 🟡 **Front-end libraries are loaded from public CDNs** — a CDN outage degrades the UI;
  pinning local copies is a future improvement.
- 🟡 **`report_request_view` prints date-of-completion as "today"** on the print report
  (it uses `date('d F Y')`), not the ticket's real completion date.
- 🟢 Automated tests exist for the inventory business rules (`php tests/run.php`,
  28 assertions). Controllers/models requiring live databases are not covered.

## Recommended next steps (security first)

1. Rotate/revoke the previously exposed Gemini key.
2. Add authentication + role checks + CSRF for write endpoints.
3. Add upload validation (MIME + size) and access control on `uploads/`.
4. Ship DB migrations/DDL for the owned tables.
5. Add a small deployment pipeline or a Docker-based dev environment.