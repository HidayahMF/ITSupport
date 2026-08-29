# Architecture

## Overview

ITSupport is a server-rendered **CodeIgniter 3.1.13** PHP application. It is a single
deployment that groups four internal IT workflows together, backed by **two databases**
at once and two external HTTP integrations.

```
Browser (server-rendered views + Tailwind CDN + jQuery + Chart.js)
        │
        ▼
CodeIgniter 3.1.13 controllers  ──►  models  ──►  MySQL  (helpdesk / KARYAWAN / IT.rpt_it)
        │                                  └──►  SQL Server (PDO sqlsrv):
        │                                        PURC_* procurement, hris_Employee,
        │                                        IT_SYSTEM_REQUEST, inventory_*, IOT_SENSOR_LOG
        │
        ├──► cURL ──► WhatsApp gateway (Bantuan notifications)
        └──► cURL ──► Google Gemini API (SystemRequest "Tanya AI")
```

## Modules

| Module | Controller(s) | Purpose | Data source |
| --- | --- | --- | --- |
| Helpdesk | `Bantuan`, `Dashboard`, `Api` | Report IT problems with photo/attachment; WhatsApp notification; ticket completion | MySQL (`IT.rpt_it`, `KARYAWAN`) |
| System Request | `SystemRequest` | Request a new IT system; AI-assisted solution suggestion; report dashboard; assignment + print | SQL Server (`IT_SYSTEM_REQUEST`, `hris_Employee`) |
| Inventory | `InventoryBarang` | PR-grouped inventory; receiving; handover; Excel export | SQL Server (`PURC_*`, `inventory_*`) |
| IoT telemetry | `Api::insert` | Ingest device JSON into `IOT_SENSOR_LOG` | SQL Server |

## Data flow highlights

### Inventory (most complex workflow)

1. `InventoryBarang_model::get_all()` joins `PURC_PURCHREQUEST_TEMP` (dept `0800`, PR year suffix) with
   `PURC_MATCATALOG`, `PURC_BIDPR`, and the local tracking table `inventory_barang`.
2. Receiving lines are logged in `inventory_penerimaan`; the `inventory_barang` row is
   auto-created on first receipt.
3. Handovers are logged in `inventory_serah_terima`.
4. **Status is derived from quantities**, not trusted flags:
   `Inventory_status::effective_status(db_status, diterima, diserahkan, qty)`.
5. Receiving and handover quantity overruns are rejected by
   `Inventory_status::receive_error()` / `handover_error()` before DB writes.
6. `export_excel()` produces a multi-sheet `.xlsx` via PHPExcel; if the composer
   dependencies are missing it falls back to `export_excel_simple()` (HTML `.xls`).

### Helpdesk

1. Employee submits `bantuan/simpan` → row inserted into `IT.rpt_it`.
2. If a WhatsApp gateway is configured, each recipient gets a message containing a
   completion link (`bantuan/selesai?id=NNN`).
3. An IT PIC opens the link, records repair type + notes → `bantuan/proses_selesai`.
4. The dashboard aggregates counts and the "Calling Per Month" bar chart via `Api::callApi`.

### System Request

1. Requester fills the form; Select2 searches `hris_Employee` via `get_employees`.
2. Optionally "Tanya AI" posts the problem text to `ask_ai` → Gemini returns a
   solution/data-output suggestion (prompt forced to plain text) that can be copied
   into the form.
3. `submit` writes `IT_SYSTEM_REQUEST`. The dark report dashboard shows KPIs, status
   distribution, per-department totals, 6-month trend, and per-programmer load; tickets
   are assigned to an IT staff member (with a busy-check) and can be printed.

### IoT

`POST Api/insert` expects JSON `{device_id, temperature, voltage, alarm_status}`,
validates required fields (422 / 400 on bad input), and writes a row to
`IOT_SENSOR_LOG`. No device-side code ships in this repository.

## Configuration flow

Secrets and environment values are resolved in this order (all getenv names are
documented in `.env.example`):

| Concern | Resolution |
| --- | --- |
| Base URL | `APP_BASE_URL` env → CodeIgniter auto-detect (`application/config/config.php`) |
| Gemini key / model | `GEMINI_API_KEY`, `GEMINI_MODEL` env → `application/config/itsupport.php` |
| WhatsApp gateway / recipients | `WHATSAPP_GATEWAY_URL`, `WHATSAPP_RECIPIENTS` env → `application/config/itsupport.php` |
| Database credentials | `application/config/database.php` (gitignored; template in `database.example.php`) |

## Database

Two connections (see `application/config/database.example.php`):

| Connection | Driver | Tables used |
| --- | --- | --- |
| `default` | `mysqli` (MySQL) | `KARYAWAN`, `hc.KARYAWAN`, `IT.rpt_it` |
| `sqlServer` | `pdo` / `sqlsrv` | `PURC_PURCHREQUEST_TEMP`, `PURC_MATCATALOG`, `PURC_BIDPR`, `hris_Employee`, `MASCOSTCENTER`, `IT_SYSTEM_REQUEST`, `IOT_SENSOR_LOG`, `inventory_barang`, `inventory_penerimaan`, `inventory_serah_terima` |

The `inventory_*` and `IT_SYSTEM_REQUEST`/`IOT_SENSOR_LOG` schemas are applied outside
this repository (no migrations/seeders are shipped). Field names are exactly those
referenced by the queries in `application/models/` and the controllers.

See [API.md](API.md) for the full endpoint reference and [AUDIT.md](AUDIT.md) for the
detailed code-audit findings.