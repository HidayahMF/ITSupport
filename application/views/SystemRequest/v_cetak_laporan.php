<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Cetak PDF - IT System Request</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #333;
            line-height: 1.4;
            margin: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 25px;
            border-bottom: 3px double #000;
            padding-bottom: 10px;
        }

        .header h2 {
            margin: 0;
            text-transform: uppercase;
            font-size: 18px;
        }

        .header p {
            margin: 5px 0 0 0;
            font-size: 12px;
            color: #555;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .info-table td {
            padding: 4px 6px;
            vertical-align: top;
        }

        .info-table td.label {
            width: 22%;
            font-weight: bold;
        }

        .info-table td.colon {
            width: 2%;
        }

        /* Styling Tabel Detail Masalah & Solusi */
        .detail-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .detail-table th,
        .detail-table td {
            border: 1px solid #666;
            padding: 8px;
            text-align: left;
            vertical-align: top;
        }

        .detail-table th {
            background-color: #f2f2f2;
            width: 25%;
        }

        /* Area Tanda Tangan */
        .ttd-container {
            width: 100%;
            margin-top: 40px;
        }

        .ttd-box {
            float: right;
            width: 200px;
            text-align: center;
        }

        .ttd-box-left {
            float: left;
            width: 200px;
            text-align: center;
        }

        .ttd-space {
            height: 70px;
        }

        .ttd-nama {
            font-weight: bold;
            text-decoration: underline;
        }

        @media print {
            body {
                margin: 0;
            }

            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body onload="window.print();">

    <div class="header">
        <h2>IT Department - System Request Report</h2>
        <p>Formulir Permintaan Sistem</p>
    </div>

    <table class="info-table">
        <tr>
            <td class="label">Tanggal Permintaan</td>
            <td class="colon">:</td>
            <td><?= isset($request->tanggal_permintaan) ? date('d F Y H:i', strtotime($request->tanggal_permintaan)) : '-'; ?> WIB</td>
        </tr>
        <tr>
            <td class="label">Nama Peminta</td>
            <td class="colon">:</td>
            <td><strong><?= isset($request->nama_peminta) ? $request->nama_peminta : '-'; ?></strong></td>
        </tr>
        <tr>
            <td class="label">Departemen / Kontak</td>
            <td class="colon">:</td>
            <td><?= isset($request->departemen_peminta) ? $request->departemen_peminta : '-'; ?> / <?= isset($request->kontak_peminta) ? $request->kontak_peminta : '-'; ?></td>
        </tr>
        <tr>
            <td class="label">Status Tiket</td>
            <td class="colon">:</td>
            <td><strong><?= isset($request->status) ? strtoupper($request->status) : 'PENDING'; ?> </strong></td>
        </tr>
    </table>

    <table class="detail-table">
        <tr>
            <th>Masalah / Request</th>
            <td><?= isset($request->masalah) ? nl2br(htmlentities($request->masalah)) : '-'; ?></td>
        </tr>
        <tr>
            <th>Rencana Solusi</th>
            <td><?= isset($request->solusi) ? nl2br(htmlentities($request->solusi)) : '-'; ?></td>
        </tr>
        <tr>
            <th>Kondisi (Sebelum)</th>
            <td><?= isset($request->kondisi_before) ? nl2br(htmlentities($request->kondisi_before)) : '-'; ?></td>
        </tr>
        <tr>
            <th>Kondisi (Sesudah)</th>
            <td><?= isset($request->kondisi_after) ? nl2br(htmlentities($request->kondisi_after)) : '-'; ?></td>
        </tr>
        <tr>
            <th>Preferensi UI / Tampilan</th>
            <td><?= isset($request->preferensi_ui) ? nl2br(htmlentities($request->preferensi_ui)) : '-'; ?></td>
        </tr>
        <tr>
            <th>Kebutuhan Output Data</th>
            <td><?= isset($request->kebutuhan_data_output) ? nl2br(htmlentities($request->kebutuhan_data_output)) : '-'; ?></td>
        </tr>
    </table>

    <div class="ttd-container">
        <div class="ttd-box-left">
            <p>&nbsp;</p>
            <p>Pemohon / Peminta,</p>
            <div class="ttd-space"></div>
            <p class="ttd-nama"><?= isset($request->nama_peminta) ? $request->nama_peminta : 'User'; ?></p>
            <p><?= isset($request->departemen_peminta) ? $request->departemen_peminta : 'User Dept'; ?></p>
        </div>

        <div class="ttd-box">
            <p>Jakarta, <?= date('d F Y'); ?></p>
            <p>Mengetahui / IT Support,</p>
            <div class="ttd-space"></div>
            <p class="ttd-nama">
                <?= isset($request->Name) && $request->Name != '' ? html_escape($request->Name) : 'User Dept'; ?>
            </p>
            <p>IT Department</p>
        </div>
        <div style="clear: both;"></div>
    </div>

</body>

</html>