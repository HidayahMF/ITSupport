# API Reference

ITSupport has no formal REST framework — these are CodeIgniter controller methods
returning JSON (or HTML pages). All endpoints are public (no authentication layer;
see [AUDIT.md](AUDIT.md)).

## Helpdesk / Dashboard

| Method | Endpoint | Purpose |
| --- | --- | --- |
| GET | `/` , `/dashboard` | Dashboard page (KPIs, chart, latest requests) |
| GET | `/bantuan` | Helpdesk report form |
| POST | `/bantuan/simpan` | Create ticket + WhatsApp notifications (JSON) |
| GET | `/bantuan/selesai?id=NNN` | Ticket completion page (public link) |
| POST | `/bantuan/proses_selesai` | Mark ticket done with PIC / repair type / notes |
| GET | `/terimaKasih` | Thank-you page after completion |

## System Request

| Method | Endpoint | Purpose |
| --- | --- | --- |
| GET | `/systemrequest` | Request form |
| POST | `/systemrequest/submit` | Insert `IT_SYSTEM_REQUEST` |
| GET | `/systemrequest/get_employees?searchTerm=` | Select2 employee search (SQL Server HRIS) |
| POST | `/systemrequest/ask_ai` | Gemini suggestion for a problem text |
| GET | `/systemrequest/cek_model` | Debug: list Gemini models (requires configured key) |
| GET | `/systemrequest/report` | Report dashboard |
| GET | `/systemrequest/update_status/{id}/{status}` | Change status; assign IT via `?it_id=` |
| GET | `/systemrequest/cetak_laporan/{id}` | Printable per-ticket report |
| GET | `/systemrequest/get_employeesItSupport` | List assignable IT staff |
| GET | `/systemrequest/get_ProgrammerKerja?ditangani_oleh=` | Busy-check for a programmer |

## Inventory Barang

| Method | Endpoint | Purpose |
| --- | --- | --- |
| GET | `/inventorybarang` | Main inventory page (3 tabs) |
| GET | `/inventorybarang/penerimaan_list` | Receiving history (JSON) |
| GET | `/inventorybarang/inventory_dropdown` | PR/item dropdown (JSON) |
| GET | `/inventorybarang/inventory_dropdown_serah_terima` | Stock-IT items with remaining qty (JSON) |
| GET | `/inventorybarang/employee_dropdown?search=` | Active employees for handover (JSON) |
| GET | `/inventorybarang/penerimaan_detail/{id}` | Single receiving row (JSON) |
| POST | `/inventorybarang/simpan_penerimaan` | Create receiving record |
| POST | `/inventorybarang/update_penerimaan/{id}` | Update receiving record |
| POST | `/inventorybarang/hapus_penerimaan/{id}` | Delete receiving record |
| GET | `/inventorybarang/serah_terima_list` | Handover history (JSON) |
| GET | `/inventorybarang/serah_terima_detail/{id}` | Single handover row (JSON) |
| POST | `/inventorybarang/simpan_serah_terima` | Create handover record |
| POST | `/inventorybarang/update_serah_terima/{id}` | Update handover record |
| POST | `/inventorybarang/hapus_serah_terima/{id}` | Delete handover record |
| GET | `/inventorybarang/export_excel` | Excel `.xlsx` export (PHPExcel, fallback to `.xls`) |
| GET | `/inventorybarang/export_excel_simple` | Dependency-free HTML `.xls` export |

## Charts / IoT

| Method | Endpoint | Purpose |
| --- | --- | --- |
| GET | `/api/callapi` | Monthly ticket counts (JSON) for the dashboard chart |
| POST | `/api/insert` | IoT telemetry ingestion (JSON) into `IOT_SENSOR_LOG` |

### `POST /api/insert` payload

```json
{
  "device_id": "sensor-01",
  "temperature": 32.5,
  "voltage": 220.1,
  "alarm_status": 0
}
```

Responses: `200 {"status":"ok"}` · `400` invalid JSON · `422` missing field ·
`500` database insert failure.