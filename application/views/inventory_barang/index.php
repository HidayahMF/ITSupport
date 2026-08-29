<!-- ============================================================= -->
<!-- ==== NEW: DataTables CSS (untuk pagination & search tabel) ==== -->
<!-- ============================================================= -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.13.8/css/jquery.dataTables.min.css">
<style>
    /* ==== NEW: sticky header untuk tabel Inventory PR ==== */
    #inventoryTable thead th {
        position: sticky;
        top: 0;
        z-index: 10;
    }

    /* ==== NEW: styling tab navigation ==== */
    .tab-btn {
        padding: 0.65rem 1.25rem;
        font-weight: 600;
        font-size: 0.875rem;
        border-radius: 0.75rem 0.75rem 0 0;
        color: #6b7280;
        border-bottom: 3px solid transparent;
        transition: all 0.15s ease;
        cursor: pointer;
        white-space: nowrap;
    }
    .tab-btn:hover {
        color: #2563eb;
        background: #eff6ff;
    }
    .tab-btn.active {
        color: #2563eb;
        border-bottom-color: #2563eb;
        background: #eff6ff;
    }
    .tab-content { display: none; }
    .tab-content.active { display: block; }

    /* ==== NEW: DataTables sedikit disesuaikan ke gaya Tailwind ==== */
    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_filter,
    .dataTables_wrapper .dataTables_info,
    .dataTables_wrapper .dataTables_paginate {
        font-size: 0.8rem;
        color: #4b5563;
    }
    .dataTables_wrapper .dataTables_filter input,
    .dataTables_wrapper .dataTables_length select {
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        padding: 0.3rem 0.6rem;
        outline: none;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button {
        border-radius: 0.5rem !important;
        padding: 0.3rem 0.65rem !important;
        margin-left: 2px;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: #2563eb !important;
        color: #fff !important;
        border: none !important;
    }
</style>

<!-- HEADER -->
<div class="mb-8">
    <h2 class="text-3xl font-bold text-gray-800">Inventory Barang</h2>
    <p class="text-gray-500">Pantau pengadaan barang dari penerimaan hingga penyerahan ke user</p>
</div>

<!-- SUMMARY CARDS -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">

    <!-- Total PR Aktif -->
    <div class="p-5 bg-white rounded-2xl shadow-md hover:shadow-lg transition flex items-center gap-4">
        <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-xl flex justify-center items-center text-2xl">📦</div>
        <div>
            <p class="text-gray-500 text-sm">Total PR Aktif</p>
            <h3 class="text-xl font-bold text-gray-800"><?= $summary['total'] ?></h3>
        </div>
    </div>

    <!-- Menunggu Barang -->
    <div class="p-5 bg-white rounded-2xl shadow-md hover:shadow-lg transition flex items-center gap-4">
        <div class="w-12 h-12 bg-yellow-100 text-yellow-500 rounded-xl flex justify-center items-center text-2xl">⏳</div>
        <div>
            <p class="text-gray-500 text-sm">Menunggu Barang</p>
            <h3 class="text-xl font-bold text-gray-800"><?= $summary['menunggu'] ?></h3>
        </div>
    </div>

    <!-- Stock IT -->
    <div class="p-5 bg-white rounded-2xl shadow-md hover:shadow-lg transition flex items-center gap-4">
        <div class="w-12 h-12 bg-blue-100 text-blue-500 rounded-xl flex justify-center items-center text-2xl">📥</div>
        <div>
            <p class="text-gray-500 text-sm">Stock IT</p>
            <h3 class="text-xl font-bold text-gray-800"><?= $summary['diterima'] ?></h3>
        </div>
    </div>

    <!-- Sudah Diserahkan ke User -->
    <div class="p-5 bg-white rounded-2xl shadow-md hover:shadow-lg transition flex items-center gap-4">
        <div class="w-12 h-12 bg-green-100 text-green-600 rounded-xl flex justify-center items-center text-2xl">✔️</div>
        <div>
            <p class="text-gray-500 text-sm">Sudah Diserahkan ke User</p>
            <h3 class="text-xl font-bold text-gray-800"><?= $summary['diserahkan'] ?></h3>
        </div>
    </div>

</div>

<!-- FILTER & TOMBOL TAMBAH -->
<div class="p-6 bg-white rounded-2xl shadow-md mb-6">
    <div class="flex flex-col md:flex-row md:items-end gap-4 mb-4">
        <div class="flex-1">
            <label class="block text-sm font-semibold text-gray-600 mb-1">Status</label>
            <select id="filterStatus" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition text-sm">
                <option value="">Semua Status</option>
                <option value="Menunggu Barang" <?= ($filter['status'] == 'Menunggu Barang') ? 'selected' : '' ?>>Menunggu Barang</option>
                <option value="Stock IT" <?= ($filter['status'] == 'Stock IT') ? 'selected' : '' ?>>Stock IT</option>
                <option value="Sudah Diserahkan ke User" <?= ($filter['status'] == 'Sudah Diserahkan ke User') ? 'selected' : '' ?>>Sudah Diserahkan ke User</option>
            </select>
        </div>
        <div class="flex-1">
            <label class="block text-sm font-semibold text-gray-600 mb-1">User Penerima</label>
            <select id="filterUser" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition text-sm">
                <option value="">Semua User</option>
                <?php foreach ($serah_terima_user_list as $su): ?>
                    <option value="<?= htmlspecialchars($su['nama_user']) ?>" <?= ($filter['nama_user'] == $su['nama_user']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($su['nama_user']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="flex-1">
            <label class="block text-sm font-semibold text-gray-600 mb-1">No PR</label>
            <select id="filterPr" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition text-sm">
                <option value="">Semua No PR</option>
                <?php foreach ($pr_list as $p): ?>
                    <option value="<?= htmlspecialchars($p['no_pr']) ?>" <?= ($filter['no_pr'] == $p['no_pr']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($p['no_pr']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="flex-1">
            <label class="block text-sm font-semibold text-gray-600 mb-1">Nama Barang</label>
            <input type="text" id="filterBarang" value="<?= htmlspecialchars($filter['nama_barang']) ?>"
                   placeholder="Cari nama barang..."
                   class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition text-sm">
        </div>
    </div>
    <div class="flex flex-col md:flex-row md:items-end gap-4">
        <div class="flex-1">
            <label class="block text-sm font-semibold text-gray-600 mb-1">Tanggal Terima (Dari)</label>
            <input type="date" id="filterTglAwal" value="<?= htmlspecialchars($filter['tanggal_awal']) ?>"
                   class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition text-sm">
        </div>
        <div class="flex-1">
            <label class="block text-sm font-semibold text-gray-600 mb-1">Tanggal Terima (Sampai)</label>
            <input type="date" id="filterTglAkhir" value="<?= htmlspecialchars($filter['tanggal_akhir']) ?>"
                   class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition text-sm">
        </div>
        <div class="flex gap-2">
            <button onclick="applyFilter()" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-semibold transition text-sm">
                Filter
            </button>
            <button onclick="resetFilter()" class="px-5 py-2.5 bg-gray-300 hover:bg-gray-400 text-gray-700 rounded-xl font-semibold transition text-sm">
                Reset
            </button>
            <a href="<?= base_url('inventorybarang/export_excel') ?><?= !empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '' ?>"
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-xl font-semibold transition text-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Export Excel
            </a>
        </div>
    </div>
</div>

<!-- ============================================================= -->
<!-- ==== NEW: TAB NAVIGATION (mengganti 3 section vertikal) ==== -->
<!-- ============================================================= -->
<div class="bg-white rounded-2xl shadow-md mb-6 overflow-hidden">
    <div class="flex border-b border-gray-200 overflow-x-auto">
        <div class="tab-btn active" data-tab="tabInventory" onclick="switchTab('tabInventory', this)">
            📦 Inventory PR
        </div>
        <div class="tab-btn" data-tab="tabPenerimaan" onclick="switchTab('tabPenerimaan', this)">
            📥 Penerimaan Barang
        </div>
        <div class="tab-btn" data-tab="tabSerahTerima" onclick="switchTab('tabSerahTerima', this)">
            🤝 Serah Terima ke User
        </div>
    </div>
</div>

<!-- ===================================================================== -->
<!-- TAB 1: INVENTORY PR (TABLE) -->
<!-- ===================================================================== -->
<div id="tabInventory" class="tab-content active">

    <div class="p-6 bg-white rounded-2xl shadow-lg hover:shadow-xl transition">

        <!-- ==== NEW: Expand All / Collapse All ==== -->
        <div class="flex justify-end gap-2 mb-3">
            <button onclick="expandAllPr()" class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition">
                Expand All
            </button>
            <button onclick="collapseAllPr()" class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-gray-100 text-gray-600 hover:bg-gray-200 transition">
                Collapse All
            </button>
        </div>

        <div class="overflow-x-auto max-h-[70vh] overflow-y-auto">
            <table id="inventoryTable" class="min-w-full table-auto">
                <thead class="bg-blue-50">
                    <tr class="text-left text-gray-600 text-sm font-semibold">
                        <th class="py-3 px-4 w-8"></th>
                        <th class="py-3 px-4">No</th>
                        <th class="py-3 px-4">No PR</th>
                        <th class="py-3 px-4">Tgl PR</th>
                        <th class="py-3 px-4 text-center"># Item</th>
                        <th class="py-3 px-4 text-center">Total Qty</th>
                        <th class="py-3 px-4 text-center">Progress</th>
                        <th class="py-3 px-4">Status</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700">
                    <?php if (!empty($grouped)): ?>
                        <?php $no = 0; ?>
                        <?php foreach ($grouped as $pr_no => $pr): ?>
                            <?php $no++; ?>
                            <!-- PR Row -->
                            <tr class="hover:bg-gray-50 border-b border-gray-100 cursor-pointer pr-row" data-pr="<?= htmlspecialchars($pr_no) ?>">
                                <td class="py-4 px-4">
                                    <svg class="w-4 h-4 text-gray-500 transition-transform pr-arrow" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </td>
                                <td class="py-4 px-4 font-medium"><?= $no ?></td>
                                <td class="py-4 px-4 font-semibold text-blue-700"><?= htmlspecialchars($pr_no) ?></td>
                                <td class="py-4 px-4"><?= date('d/m/Y', strtotime($pr['tanggal_pr'])) ?></td>
                                <td class="py-4 px-4 text-center font-semibold"><?= count($pr['items']) ?></td>
                                <td class="py-4 px-4 text-center font-semibold"><?= $pr['total_qty'] ?></td>
                                <td class="py-4 px-4">
                                    <div class="flex items-center gap-2">
                                        <div class="flex-1 bg-gray-200 rounded-full h-2.5">
                                            <div class="h-2.5 rounded-full <?= $pr['progress_pct'] >= 100 ? 'bg-green-500' : ($pr['progress_pct'] > 0 ? 'bg-blue-500' : 'bg-gray-300') ?>" style="width: <?= $pr['progress_pct'] ?>%"></div>
                                        </div>
                                        <span class="text-xs font-semibold text-gray-600 min-w-[36px]"><?= $pr['progress_pct'] ?>%</span>
                                    </div>
                                </td>
                                <td class="py-4 px-4">
                                    <?php if ($pr['status'] === 'Menunggu Barang'): ?>
                                        <span class="px-3 py-1 rounded-full bg-yellow-100 text-yellow-700 text-xs font-semibold">Menunggu Barang</span>
                                    <?php elseif ($pr['status'] === 'Stock IT'): ?>
                                        <span class="px-3 py-1 rounded-full bg-blue-100 text-blue-700 text-xs font-semibold">Stock IT</span>
                                    <?php elseif ($pr['status'] === 'Sudah Diserahkan ke User'): ?>
                                        <span class="px-3 py-1 rounded-full bg-green-100 text-green-700 text-xs font-semibold">Sudah Diserahkan ke User</span>
                                    <?php else: ?>
                                        <span class="px-3 py-1 rounded-full bg-purple-100 text-purple-700 text-xs font-semibold"><?= htmlspecialchars($pr['status']) ?></span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <!-- Detail Rows (hidden by default) -->
                            <?php foreach ($pr['items'] as $item): ?>
                                <tr class="detail-row detail-<?= htmlspecialchars(str_replace('/', '-', $pr_no)) ?> hidden bg-gray-50/50 border-b border-gray-100">
                                    <td class="py-3 px-4"></td>
                                    <td class="py-3 px-4"></td>
                                    <td class="py-3 px-4 pl-8" colspan="2">
                                        <span class="text-sm font-medium text-gray-700"><?= htmlspecialchars($item['nama_barang']) ?></span>
                                    </td>
                                    <td class="py-3 px-4 text-center text-sm"><?= round($item['Qty']) ?></td>
                                    <td class="py-3 px-4 text-center">
                                        <span class="text-sm font-semibold"><?= $item['total_diterima'] ?> / <?= round($item['Qty']) ?></span>
                                    </td>
                                    <td class="py-3 px-4">
                                        <div class="flex items-center gap-2">
                                            <div class="flex-1 bg-gray-200 rounded-full h-2">
                                                <div class="h-2 rounded-full <?= $item['progress'] >= 100 ? 'bg-green-500' : ($item['progress'] > 0 ? 'bg-blue-500' : 'bg-gray-300') ?>" style="width: <?= $item['progress'] ?>%"></div>
                                            </div>
                                            <span class="text-xs font-semibold text-gray-600 min-w-[36px]"><?= $item['progress'] ?>%</span>
                                        </div>
                                    </td>
                                    <td class="py-3 px-4">
                                        <?php if ($item['effective_status'] === 'Menunggu Barang'): ?>
                                            <span class="px-2 py-0.5 rounded-full bg-yellow-100 text-yellow-700 text-xs font-semibold">Menunggu Barang</span>
                                        <?php elseif ($item['effective_status'] === 'Stock IT'): ?>
                                            <span class="px-2 py-0.5 rounded-full bg-blue-100 text-blue-700 text-xs font-semibold">Stock IT</span>
                                        <?php else: ?>
                                            <span class="px-2 py-0.5 rounded-full bg-green-100 text-green-700 text-xs font-semibold">Sudah Diserahkan ke User</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php if (empty($grouped)): ?>
    <div class="p-16 text-center text-gray-400">
        <div class="flex flex-col items-center">
            <span class="text-5xl mb-3">📭</span>
            <p class="text-lg font-semibold">Belum ada data</p>
            <p class="text-sm">Silakan tambah inventory barang baru</p>
        </div>
    </div>
    <?php endif; ?>

</div>
<!-- END TAB 1 -->

<!-- ===================================================================== -->
<!-- TAB 2: PENERIMAAN BARANG -->
<!-- ===================================================================== -->
<div id="tabPenerimaan" class="tab-content">

    <div class="flex items-center justify-between mb-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Penerimaan Barang</h2>
            <p class="text-gray-500 text-sm">Histori penerimaan barang per PR secara bertahap</p>
        </div>
        <button onclick="openModalTambah()" class="px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-xl font-semibold transition text-sm">
            + Tambah Penerimaan
        </button>
    </div>

    <div class="p-6 bg-white rounded-2xl shadow-lg">
        <div class="overflow-x-auto">
            <table id="penerimaanTable" class="min-w-full table-auto w-full">
                <thead class="bg-blue-50">
                    <tr class="text-left text-gray-600 text-sm font-semibold">
                        <th class="py-3 px-4">No</th>
                        <th class="py-3 px-4">No PR</th>
                        <th class="py-3 px-4">Nama Barang</th>
                        <th class="py-3 px-4">Tgl Diterima</th>
                        <th class="py-3 px-4 text-center">Qty Diterima</th>
                        <th class="py-3 px-4 text-center">Progress</th>
                        <th class="py-3 px-4">Keterangan</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700" id="penerimaanBody">
                    <tr>
                        <td colspan="8" class="py-8 text-center text-gray-400">Memuat data...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<!-- END TAB 2 -->

<!-- ===================================================================== -->
<!-- TAB 3: SERAH TERIMA KE USER -->
<!-- ===================================================================== -->
<div id="tabSerahTerima" class="tab-content">

    <div class="flex items-center justify-between mb-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Serah Terima ke User</h2>
            <p class="text-gray-500 text-sm">Histori penyerahan barang ke user pemohon</p>
        </div>
        <button onclick="openModalTambahSerahTerima()" class="px-5 py-2.5 bg-purple-600 hover:bg-purple-700 text-white rounded-xl font-semibold transition text-sm">
            + Tambah Serah Terima
        </button>
    </div>

    <div class="p-6 bg-white rounded-2xl shadow-lg">
        <div class="overflow-x-auto">
            <table id="serahTerimaTable" class="min-w-full table-auto w-full">
                <thead class="bg-purple-50">
                    <tr class="text-left text-gray-600 text-sm font-semibold">
                        <th class="py-3 px-4">No</th>
                        <th class="py-3 px-4">No PR</th>
                        <th class="py-3 px-4">Nama Barang</th>
                        <th class="py-3 px-4">Diserahkan ke</th>
                        <th class="py-3 px-4 text-center">Qty Diserahkan</th>
                        <th class="py-3 px-4">Tgl Serah</th>
                        <th class="py-3 px-4">Keterangan</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700" id="serahTerimaBody">
                    <tr>
                        <td colspan="8" class="py-8 text-center text-gray-400">Memuat data...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<!-- END TAB 3 -->

<!-- MODAL TAMBAH / EDIT PENERIMAAN -->
<div id="modalPenerimaan" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg">
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 id="modalPenerimaanTitle" class="text-xl font-bold text-gray-800">Tambah Penerimaan</h3>
                <button onclick="closeModalPenerimaan()" class="p-2 rounded-lg bg-gray-100 hover:bg-gray-200 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form id="formPenerimaan" onsubmit="return false;">
                <input type="hidden" id="penerimaanId" value="">

                <!-- Pilih PR/Barang -->
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-600 mb-1">PR / Barang <span class="text-red-500">*</span></label>
                    <select id="penerimaanInventoryId" required
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition text-sm">
                        <option value="">-- Pilih PR / Barang --</option>
                        <?php foreach ($inventory_list as $inv): ?>
                            <option value="<?= htmlspecialchars($inv['no_pr']) ?>|<?= htmlspecialchars($inv['material_id']) ?>"
                                    data-qty="<?= (int)$inv['qty'] ?>"
                                    data-material-id="<?= htmlspecialchars($inv['material_id']) ?>"
                                    data-nama-barang="<?= htmlspecialchars($inv['nama_barang']) ?>"
                                    data-inventory-id="<?= $inv['inventory_id'] ? (int)$inv['inventory_id'] : '' ?>">
                                <?= htmlspecialchars($inv['no_pr']) ?> — <?= htmlspecialchars($inv['nama_barang']) ?> (Qty: <?= (int)$inv['qty'] ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <p id="penerimaanInfo" class="text-xs text-gray-500 mt-1 hidden"></p>
                </div>

                <!-- Tanggal Diterima -->
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-600 mb-1">Tanggal Diterima <span class="text-red-500">*</span></label>
                    <input type="date" id="penerimaanTanggal" required
                           class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition text-sm">
                </div>

                <!-- Qty Diterima -->
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-600 mb-1">Qty Diterima <span class="text-red-500">*</span></label>
                    <input type="number" id="penerimaanQty" required min="1"
                           placeholder="0"
                           class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition text-sm">
                    <p id="penerimaanSisaInfo" class="text-xs text-gray-500 mt-1 hidden"></p>
                </div>

                <!-- Keterangan -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-600 mb-1">Keterangan</label>
                    <textarea id="penerimaanKeterangan" rows="2"
                              placeholder="Opsional..."
                              class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition text-sm resize-none"></textarea>
                </div>

                <!-- Tombol -->
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closeModalPenerimaan()"
                            class="px-6 py-2.5 bg-gray-300 hover:bg-gray-400 text-gray-700 rounded-xl font-semibold transition text-sm">
                        Batal
                    </button>
                    <button type="submit" id="btnSimpanPenerimaan"
                            class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-semibold transition text-sm">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL TAMBAH / EDIT SERAH TERIMA -->
<div id="modalSerahTerima" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg">
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 id="modalSerahTerimaTitle" class="text-xl font-bold text-gray-800">Tambah Serah Terima</h3>
                <button onclick="closeModalSerahTerima()" class="p-2 rounded-lg bg-gray-100 hover:bg-gray-200 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form id="formSerahTerima" onsubmit="return false;">
                <input type="hidden" id="serahTerimaId" value="">
                <input type="hidden" id="serahTerimaInventoryId" value="">

                <!-- Pilih PR/Barang -->
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-600 mb-1">PR / Barang <span class="text-red-500">*</span></label>
                    <select id="serahTerimaInventorySelect" required
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition text-sm">
                        <option value="">-- Memuat data... --</option>
                    </select>
                    <p id="serahTerimaInfo" class="text-xs text-gray-500 mt-1 hidden"></p>
                </div>

                <!-- Nama User Penerima -->
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-600 mb-1">Nama User Penerima <span class="text-red-500">*</span></label>
                    <select id="serahTerimaNamaUser" required
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition text-sm">
                        <option value="">-- Pilih Karyawan --</option>
                    </select>
                </div>

                <!-- Tanggal Serah -->
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-600 mb-1">Tanggal Serah <span class="text-red-500">*</span></label>
                    <input type="date" id="serahTerimaTanggal" required
                           class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition text-sm">
                </div>

                <!-- Qty Diserahkan -->
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-600 mb-1">Qty Diserahkan <span class="text-red-500">*</span></label>
                    <input type="number" id="serahTerimaQty" required min="1"
                           placeholder="0"
                           class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition text-sm">
                    <p id="serahTerimaSisaInfo" class="text-xs text-gray-500 mt-1 hidden"></p>
                </div>

                <!-- Keterangan -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-600 mb-1">Keterangan</label>
                    <textarea id="serahTerimaKeterangan" rows="2"
                              placeholder="Opsional..."
                              class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition text-sm resize-none"></textarea>
                </div>

                <!-- Tombol -->
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closeModalSerahTerima()"
                            class="px-6 py-2.5 bg-gray-300 hover:bg-gray-400 text-gray-700 rounded-xl font-semibold transition text-sm">
                        Batal
                    </button>
                    <button type="submit" id="btnSimpanSerahTerima"
                            class="px-6 py-2.5 bg-purple-600 hover:bg-purple-700 text-white rounded-xl font-semibold transition text-sm">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ============================================================= -->
<!-- ==== NEW: DataTables JS (dimuat sebelum script utama) ==== -->
<!-- ============================================================= -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.13.8/js/jquery.dataTables.min.js"></script>

<script>
// =====================================================================
// ==== NEW: TAB SWITCHING LOGIC ====
// =====================================================================
function switchTab(tabId, el) {
    $('.tab-content').removeClass('active');
    $('#' + tabId).addClass('active');

    $('.tab-btn').removeClass('active');
    $(el).addClass('active');

    // Scroll ke atas tab area supaya user tidak "nyasar" di tengah scroll lama
    $('html, body').animate({ scrollTop: $('.tab-btn').first().offset().top - 100 }, 200);
}

// ==== NEW: Expand All / Collapse All untuk tabel Inventory PR ====
function expandAllPr() {
    $('.detail-row').removeClass('hidden');
    $('.pr-arrow').addClass('rotate-90');
}
function collapseAllPr() {
    $('.detail-row').addClass('hidden');
    $('.pr-arrow').removeClass('rotate-90');
}

$(document).ready(function() {
    // Expand/collapse PR rows
    $('.pr-row').on('click', function() {
        var pr = $(this).data('pr');
        var safePr = String(pr).replace(/\//g, '-');
        var $details = $('.detail-' + safePr);
        var $arrow = $(this).find('.pr-arrow');

        if ($details.hasClass('hidden')) {
            $details.removeClass('hidden');
            $arrow.addClass('rotate-90');
        } else {
            $details.addClass('hidden');
            $arrow.removeClass('rotate-90');
        }
    });
});

// ==================== FILTER ====================
function applyFilter() {
    var params = new URLSearchParams();

    var status = $('#filterStatus').val();
    var user = $('#filterUser').val();
    var pr = $('#filterPr').val();
    var barang = $('#filterBarang').val();
    var tglAwal = $('#filterTglAwal').val();
    var tglAkhir = $('#filterTglAkhir').val();

    if (status) params.set('status', status);
    if (user) params.set('nama_user', user);
    if (pr) params.set('no_pr', pr);
    if (barang) params.set('nama_barang', barang);
    if (tglAwal) params.set('tanggal_awal', tglAwal);
    if (tglAkhir) params.set('tanggal_akhir', tglAkhir);

    window.location.href = '<?= base_url('inventorybarang') ?>?' + params.toString();
}

function resetFilter() {
    window.location.href = '<?= base_url('inventorybarang') ?>';
}

// =====================================================================
// PENERIMAAN BARANG
// =====================================================================

// ==== NEW: simpan instance DataTables supaya bisa di-destroy sebelum re-render ====
var penerimaanDT = null;
var serahTerimaDT = null;

function loadPenerimaanTable() {
    $.ajax({
        url: '<?= base_url('inventorybarang/penerimaan_list') ?>',
        type: 'GET',
        dataType: 'json',
        success: function(res) {
            if (res.status === 'success') {
                renderPenerimaanTable(res.data);
            }
        }
    });
}

function renderPenerimaanTable(data) {
    // ==== NEW: destroy DataTable dulu sebelum tbody diisi ulang ====
    if (penerimaanDT) {
        penerimaanDT.destroy();
        penerimaanDT = null;
    }

    var $tbody = $('#penerimaanBody');
    $tbody.empty();

    if (!data || data.length === 0) {
        $tbody.append('<tr><td colspan="8" class="py-8 text-center text-gray-400">Belum ada data penerimaan</td></tr>');
        return;
    }

    $.each(data, function(i, row) {
        var progressPct = row.progress_pct;
        var badgeClass = '';
        if (progressPct >= 100) {
            badgeClass = 'bg-green-100 text-green-700';
        } else {
            badgeClass = 'bg-yellow-100 text-yellow-700';
        }

        var tgl = '-';
        if (row.tanggal_diterima) {
            var parts = row.tanggal_diterima.split('-');
            tgl = parts[2] + '/' + parts[1] + '/' + parts[0];
        }

        var html = '<tr class="hover:bg-gray-50 border-b border-gray-100">';
        html += '<td class="py-4 px-4 font-medium">' + (i + 1) + '</td>';
        html += '<td class="py-4 px-4 font-semibold">' + escapeHtml(row.no_pr || '-') + '</td>';
        html += '<td class="py-4 px-4">' + escapeHtml(row.nama_barang || '-') + '</td>';
        html += '<td class="py-4 px-4">' + tgl + '</td>';
        html += '<td class="py-4 px-4 text-center font-semibold">' + row.qty_diterima + '</td>';
        html += '<td class="py-4 px-4 text-center"><span class="px-2 py-1 rounded-full text-xs font-semibold ' + badgeClass + '">' + escapeHtml(row.progress) + '</span></td>';
        html += '<td class="py-4 px-4 text-sm">' + escapeHtml(row.keterangan || '-') + '</td>';
        html += '<td class="py-4 px-4 text-center">';
        html += '<div class="flex items-center justify-center gap-1">';
        html += '<button onclick="openModalEdit(' + row.id + ')" class="p-2 rounded-lg bg-yellow-100 text-yellow-600 hover:bg-yellow-200 transition" title="Edit">';
        html += '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>';
        html += '</button>';
        html += '<button onclick="hapusPenerimaan(' + row.id + ')" class="p-2 rounded-lg bg-red-100 text-red-600 hover:bg-red-200 transition" title="Hapus">';
        html += '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>';
        html += '</button>';
        html += '</div>';
        html += '</td>';
        html += '</tr>';

        $tbody.append(html);
    });

    // ==== NEW: init DataTables (pagination + search) untuk tabel Penerimaan ====
    penerimaanDT = $('#penerimaanTable').DataTable({
        pageLength: 10,
        lengthMenu: [5, 10, 25, 50, 100],
        order: [],
        columnDefs: [
            { orderable: false, targets: [7] } // kolom Aksi tidak bisa di-sort
        ],
        language: {
            search: "Cari:",
            lengthMenu: "Tampilkan _MENU_ data",
            info: "Menampilkan _START_-_END_ dari _TOTAL_ data",
            infoEmpty: "Tidak ada data",
            zeroRecords: "Data tidak ditemukan",
            paginate: { previous: "‹", next: "›" }
        }
    });
}

function escapeHtml(text) {
    if (!text) return '';
    var div = document.createElement('div');
    div.appendChild(document.createTextNode(text));
    return div.innerHTML;
}

// ==================== MODAL TAMBAH ====================
function openModalTambah() {
    $('#modalPenerimaanTitle').text('Tambah Penerimaan');
    $('#penerimaanId').val('');
    $('#penerimaanInventoryId').val('').prop('disabled', false);
    $('#penerimaanTanggal').val('');
    $('#penerimaanQty').val('');
    $('#penerimaanKeterangan').val('');
    $('#penerimaanInfo').addClass('hidden');
    $('#penerimaanSisaInfo').addClass('hidden');
    $('#btnSimpanPenerimaan').text('Simpan');

    initSelect2Penerimaan(false);

    $('#modalPenerimaan').removeClass('hidden');
}

// ==================== MODAL EDIT ====================
function openModalEdit(id) {
    $.ajax({
        url: '<?= base_url('inventorybarang/penerimaan_detail/') ?>' + id,
        type: 'GET',
        dataType: 'json',
        success: function(res) {
            if (res.status === 'success') {
                var d = res.data;
                $('#modalPenerimaanTitle').text('Edit Penerimaan');
                $('#penerimaanId').val(d.id);
                $('#penerimaanTanggal').val(d.tanggal_diterima);
                $('#penerimaanQty').val(d.qty_diterima);
                $('#penerimaanKeterangan').val(d.keterangan || '');

                initSelect2Penerimaan(true);

                var $opt = $('#penerimaanInventoryId option[data-inventory-id="' + d.inventory_id + '"]');
                if ($opt.length) {
                    $('#penerimaanInventoryId').val($opt.val()).trigger('change');
                    updateSisaInfo($opt.val(), d.qty_diterima);
                }

                $('#modalPenerimaan').removeClass('hidden');
            } else {
                Swal.fire('Gagal', res.message, 'error');
            }
        }
    });
}

function updateSisaInfo(selectedVal, excludeQty) {
    excludeQty = excludeQty || 0;
    if (!selectedVal || selectedVal.indexOf('|') === -1) {
        $('#penerimaanInfo').addClass('hidden');
        return;
    }
    var $opt = $('#penerimaanInventoryId option[value="' + CSS.escape(selectedVal) + '"]');
    if ($opt.length) {
        var qty = parseInt($opt.data('qty')) || 0;
        $('#penerimaanInfo').text('Qty total: ' + qty).removeClass('hidden');
    } else {
        $('#penerimaanInfo').addClass('hidden');
    }
}

function initSelect2Penerimaan(isDisabled) {
    $('#penerimaanInventoryId').select2({
        dropdownParent: $('#modalPenerimaan'),
        placeholder: 'Ketik No PR atau Nama Barang...',
        allowClear: true,
        width: '100%'
    });
    if (isDisabled) {
        $('#penerimaanInventoryId').prop('disabled', true);
    }
    $('#penerimaanInventoryId').on('select2:select', function(e) {
        updateSisaInfo(e.params.data.id, 0);
    });
}

function closeModalPenerimaan() {
    $('#penerimaanInventoryId').select2('destroy');
    $('#modalPenerimaan').addClass('hidden');
}

$('#modalPenerimaan').on('click', function(e) {
    if ($(e.target).is('#modalPenerimaan')) {
        closeModalPenerimaan();
    }
});

// ==================== SIMPAN PENERIMAAN ====================
$('#formPenerimaan').on('submit', function(e) {
    e.preventDefault();

    var id = $('#penerimaanId').val();
    var rawVal = $('#penerimaanInventoryId').val();
    var $selected = $('#penerimaanInventoryId option:selected');

    if (!rawVal || rawVal.indexOf('|') === -1) {
        Swal.fire('Validasi', 'Pilih PR/Barang terlebih dahulu.', 'warning');
        return;
    }

    var parts = rawVal.split('|');
    var noPr = parts[0];
    var materialId = parts[1];
    var qtyTotal = parseInt($selected.data('qty')) || 0;
    var namaBarang = $selected.data('nama-barang') || '';

    var tanggal = $('#penerimaanTanggal').val();
    var qty = parseInt($('#penerimaanQty').val());
    var keterangan = $('#penerimaanKeterangan').val();

    if (!noPr || !materialId) {
        Swal.fire('Validasi', 'Pilih PR/Barang terlebih dahulu.', 'warning');
        return;
    }
    if (!tanggal) {
        Swal.fire('Validasi', 'Tanggal diterima wajib diisi.', 'warning');
        return;
    }
    if (isNaN(qty) || qty < 1) {
        Swal.fire('Validasi', 'Qty diterima harus lebih dari 0.', 'warning');
        return;
    }

    var btn = $('#btnSimpanPenerimaan');
    btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...');

    var url = id
        ? '<?= base_url('inventorybarang/update_penerimaan/') ?>' + id
        : '<?= base_url('inventorybarang/simpan_penerimaan') ?>';

    var postData = {
        no_pr: noPr,
        material_id: materialId,
        nama_barang: namaBarang,
        qty_total: qtyTotal,
        tanggal_diterima: tanggal,
        qty_diterima: qty,
        keterangan: keterangan
    };

    $.ajax({
        url: url,
        type: 'POST',
        data: postData,
        dataType: 'json',
        success: function(res) {
            if (res.status === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: res.message,
                    timer: 1500,
                    showConfirmButton: false
                }).then(function() {
                    closeModalPenerimaan();
                    loadPenerimaanTable();
                    location.reload();
                });
            } else {
                Swal.fire('Gagal', res.message, 'error');
            }
        },
        error: function() {
            Swal.fire('Error', 'Terjadi kesalahan server.', 'error');
        },
        complete: function() {
            btn.prop('disabled', false).html(id ? 'Update' : 'Simpan');
        }
    });
});

// ==================== HAPUS PENERIMAAN ====================
function hapusPenerimaan(id) {
    Swal.fire({
        title: 'Hapus Penerimaan?',
        html: 'Data penerimaan ini akan dihapus permanen.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '<?= base_url('inventorybarang/hapus_penerimaan/') ?>' + id,
                type: 'POST',
                dataType: 'json',
                success: function(res) {
                    if (res.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Terhapus!',
                            text: res.message,
                            timer: 1500,
                            showConfirmButton: false
                        }).then(function() {
                            loadPenerimaanTable();
                            location.reload();
                        });
                    } else {
                        Swal.fire('Gagal', res.message, 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Terjadi kesalahan server.', 'error');
                }
            });
        }
    });
}

// ==================== INIT PENERIMAAN TABLE ====================
$(document).ready(function() {
    loadPenerimaanTable();
    loadSerahTerimaTable();
});

// =====================================================================
// SERAH TERIMA KE USER
// =====================================================================

function loadSerahTerimaTable() {
    $.ajax({
        url: '<?= base_url('inventorybarang/serah_terima_list') ?>',
        type: 'GET',
        dataType: 'json',
        success: function(res) {
            if (res.status === 'success') {
                renderSerahTerimaTable(res.data);
            }
        }
    });
}

function renderSerahTerimaTable(data) {
    // ==== NEW: destroy DataTable dulu sebelum tbody diisi ulang ====
    if (serahTerimaDT) {
        serahTerimaDT.destroy();
        serahTerimaDT = null;
    }

    var $tbody = $('#serahTerimaBody');
    $tbody.empty();

    if (!data || data.length === 0) {
        $tbody.append('<tr><td colspan="8" class="py-8 text-center text-gray-400">Belum ada data serah terima</td></tr>');
        return;
    }

    $.each(data, function(i, row) {
        var tgl = '-';
        if (row.tanggal_serah) {
            var parts = row.tanggal_serah.split('-');
            tgl = parts[2] + '/' + parts[1] + '/' + parts[0];
        }

        var html = '<tr class="hover:bg-gray-50 border-b border-gray-100">';
        html += '<td class="py-4 px-4 font-medium">' + (i + 1) + '</td>';
        html += '<td class="py-4 px-4 font-semibold">' + escapeHtml(row.no_pr || '-') + '</td>';
        html += '<td class="py-4 px-4">' + escapeHtml(row.nama_barang || '-') + '</td>';
        html += '<td class="py-4 px-4 font-semibold">' + escapeHtml(row.nama_user || '-') + '</td>';
        html += '<td class="py-4 px-4 text-center font-semibold">' + row.qty_diserahkan + '</td>';
        html += '<td class="py-4 px-4">' + tgl + '</td>';
        html += '<td class="py-4 px-4 text-sm">' + escapeHtml(row.keterangan || '-') + '</td>';
        html += '<td class="py-4 px-4 text-center">';
        html += '<div class="flex items-center justify-center gap-1">';
        html += '<button onclick="openModalEditSerahTerima(' + row.id + ')" class="p-2 rounded-lg bg-yellow-100 text-yellow-600 hover:bg-yellow-200 transition" title="Edit">';
        html += '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>';
        html += '</button>';
        html += '<button onclick="hapusSerahTerima(' + row.id + ')" class="p-2 rounded-lg bg-red-100 text-red-600 hover:bg-red-200 transition" title="Hapus">';
        html += '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>';
        html += '</button>';
        html += '</div>';
        html += '</td>';
        html += '</tr>';

        $tbody.append(html);
    });

    // ==== NEW: init DataTables (pagination + search) untuk tabel Serah Terima ====
    serahTerimaDT = $('#serahTerimaTable').DataTable({
        pageLength: 10,
        lengthMenu: [5, 10, 25, 50, 100],
        order: [],
        columnDefs: [
            { orderable: false, targets: [7] } // kolom Aksi tidak bisa di-sort
        ],
        language: {
            search: "Cari:",
            lengthMenu: "Tampilkan _MENU_ data",
            info: "Menampilkan _START_-_END_ dari _TOTAL_ data",
            infoEmpty: "Tidak ada data",
            zeroRecords: "Data tidak ditemukan",
            paginate: { previous: "‹", next: "›" }
        }
    });
}

// ==================== MODAL TAMBAH SERAH TERIMA ====================
function loadEmployeeDropdown(callback) {
    var $sel = $('#serahTerimaNamaUser');
    $sel.html('<option value="">Memuat data...</option>');
    $.ajax({
        url: '<?= base_url('inventorybarang/employee_dropdown') ?>',
        type: 'GET',
        dataType: 'json',
        success: function(res) {
            if (res.status === 'success' && res.data.length > 0) {
                var html = '<option value="">-- Pilih Karyawan --</option>';
                $.each(res.data, function(i, emp) {
                    html += '<option value="' + escapeHtml(emp.Name) + '">'
                        + escapeHtml(emp.Name)
                        + ' (' + escapeHtml(emp.NIP) + ')'
                        + '</option>';
                });
                $sel.html(html);
            } else {
                $sel.html('<option value="">-- Tidak ada data karyawan --</option>');
            }
            if (callback) callback();
        },
        error: function() {
            $sel.html('<option value="">-- Gagal memuat data --</option>');
            if (callback) callback();
        }
    });
}

function initSelect2NamaUser() {
    $('#serahTerimaNamaUser').select2({
        dropdownParent: $('#modalSerahTerima'),
        placeholder: 'Ketik nama atau NIP...',
        allowClear: true,
        width: '100%'
    });
}

function loadSerahTerimaDropdown(callback) {
    var $sel = $('#serahTerimaInventorySelect');
    $sel.html('<option value="">Memuat data...</option>');
    $.ajax({
        url: '<?= base_url('inventorybarang/inventory_dropdown_serah_terima') ?>',
        type: 'GET',
        dataType: 'json',
        success: function(res) {
            if (res.status === 'success' && res.data.length > 0) {
                var html = '<option value="">-- Pilih PR / Barang --</option>';
                $.each(res.data, function(i, inv) {
                    html += '<option value="' + inv.inventory_id + '"'
                        + ' data-no-pr="' + escapeHtml(inv.no_pr) + '"'
                        + ' data-nama-barang="' + escapeHtml(inv.nama_barang) + '"'
                        + ' data-qty="' + inv.qty + '"'
                        + ' data-sisa="' + (inv.total_diterima - inv.total_diserahkan) + '"'
                        + '>'
                        + escapeHtml(inv.no_pr) + ' — ' + escapeHtml(inv.nama_barang)
                        + ' (Sisa: ' + (inv.total_diterima - inv.total_diserahkan) + ')'
                        + '</option>';
                });
                $sel.html(html);
            } else {
                $sel.html('<option value="">-- Tidak ada barang Stock IT --</option>');
            }
            if (callback) callback();
        },
        error: function() {
            $sel.html('<option value="">-- Gagal memuat data --</option>');
            if (callback) callback();
        }
    });
}

function openModalTambahSerahTerima() {
    $('#modalSerahTerimaTitle').text('Tambah Serah Terima');
    $('#serahTerimaId').val('');
    $('#serahTerimaInventoryId').val('');
    $('#serahTerimaInventorySelect').val('').prop('disabled', true);
    $('#serahTerimaTanggal').val('');
    $('#serahTerimaQty').val('');
    $('#serahTerimaKeterangan').val('');
    $('#serahTerimaInfo').addClass('hidden');
    $('#serahTerimaSisaInfo').addClass('hidden');
    $('#btnSimpanSerahTerima').text('Simpan');

    loadSerahTerimaDropdown(function() {
        initSelect2SerahTerima(false);
    });
    loadEmployeeDropdown(function() {
        initSelect2NamaUser();
    });

    $('#modalSerahTerima').removeClass('hidden');
}

// ==================== MODAL EDIT SERAH TERIMA ====================
function openModalEditSerahTerima(id) {
    $.ajax({
        url: '<?= base_url('inventorybarang/serah_terima_detail/') ?>' + id,
        type: 'GET',
        dataType: 'json',
        success: function(res) {
            if (res.status === 'success') {
                var d = res.data;
                $('#modalSerahTerimaTitle').text('Edit Serah Terima');
                $('#serahTerimaId').val(d.id);
                $('#serahTerimaInventoryId').val(d.inventory_id);
                $('#serahTerimaNamaUser').val(d.nama_user || '');
                $('#serahTerimaTanggal').val(d.tanggal_serah);
                $('#serahTerimaQty').val(d.qty_diserahkan);
                $('#serahTerimaKeterangan').val(d.keterangan || '');

                loadSerahTerimaDropdown(function() {
                    var $sel = $('#serahTerimaInventorySelect');
                    var $opt = $sel.find('option[value="' + d.inventory_id + '"]');
                    if (!$opt.length) {
                        $sel.append('<option value="' + d.inventory_id + '">'
                            + escapeHtml(d.no_pr) + ' — ' + escapeHtml(d.nama_barang)
                            + ' (Qty: ' + (d.qty_total || 0) + ')</option>');
                    }
                    $sel.val(d.inventory_id).trigger('change');
                    initSelect2SerahTerima(true);
                    updateSisaInfoSerahTerima(d.inventory_id, d.qty_diserahkan);
                });

                loadEmployeeDropdown(function() {
                    initSelect2NamaUser();
                    if (d.nama_user) {
                        var $userSel = $('#serahTerimaNamaUser');
                        if ($userSel.find('option[value="' + escapeHtml(d.nama_user) + '"]').length === 0) {
                            $userSel.append('<option value="' + escapeHtml(d.nama_user) + '">' + escapeHtml(d.nama_user) + '</option>');
                        }
                        $userSel.val(d.nama_user).trigger('change');
                    }
                });

                $('#modalSerahTerima').removeClass('hidden');
            } else {
                Swal.fire('Gagal', res.message, 'error');
            }
        }
    });
}

function updateSisaInfoSerahTerima(inventoryId, excludeQty) {
    excludeQty = excludeQty || 0;
    if (!inventoryId) {
        $('#serahTerimaInfo').addClass('hidden');
        return;
    }
    var $opt = $('#serahTerimaInventorySelect option[value="' + inventoryId + '"]');
    if ($opt.length) {
        var qty = parseInt($opt.data('qty')) || 0;
        var noPr = $opt.data('no-pr') || '';
        var namaBrg = $opt.data('nama-barang') || '';
        $('#serahTerimaInfo').text('PR: ' + noPr + ' — ' + namaBrg + ' | Qty total: ' + qty).removeClass('hidden');
    } else {
        $('#serahTerimaInfo').addClass('hidden');
    }
}

function initSelect2SerahTerima(isDisabled) {
    $('#serahTerimaInventorySelect').select2({
        dropdownParent: $('#modalSerahTerima'),
        placeholder: 'Ketik No PR atau Nama Barang...',
        allowClear: true,
        width: '100%'
    });
    $('#serahTerimaInventorySelect').prop('disabled', isDisabled);
    $('#serahTerimaInventorySelect').off('select2:select').on('select2:select', function(e) {
        var inventoryId = e.params.data.id;
        $('#serahTerimaInventoryId').val(inventoryId);
        updateSisaInfoSerahTerima(inventoryId, 0);
    });
}

function closeModalSerahTerima() {
    if ($('#serahTerimaInventorySelect').data('select2')) {
        $('#serahTerimaInventorySelect').select2('destroy');
    }
    if ($('#serahTerimaNamaUser').data('select2')) {
        $('#serahTerimaNamaUser').select2('destroy');
    }
    $('#modalSerahTerima').addClass('hidden');
}

$('#modalSerahTerima').on('click', function(e) {
    if ($(e.target).is('#modalSerahTerima')) {
        closeModalSerahTerima();
    }
});

// ==================== SIMPAN SERAH TERIMA ====================
$('#formSerahTerima').on('submit', function(e) {
    e.preventDefault();

    var id = $('#serahTerimaId').val();
    var inventoryId = $('#serahTerimaInventorySelect').val();
    var namaUser = $('#serahTerimaNamaUser').val();
    var tanggal = $('#serahTerimaTanggal').val();
    var qty = parseInt($('#serahTerimaQty').val());
    var keterangan = $('#serahTerimaKeterangan').val();

    if (!inventoryId) {
        Swal.fire('Validasi', 'Pilih PR/Barang terlebih dahulu.', 'warning');
        return;
    }
    if (!namaUser || namaUser.trim() === '') {
        Swal.fire('Validasi', 'Nama user penerima wajib diisi.', 'warning');
        return;
    }
    if (!tanggal) {
        Swal.fire('Validasi', 'Tanggal serah wajib diisi.', 'warning');
        return;
    }
    if (isNaN(qty) || qty < 1) {
        Swal.fire('Validasi', 'Qty diserahkan harus lebih dari 0.', 'warning');
        return;
    }

    var btn = $('#btnSimpanSerahTerima');
    btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...');

    var url = id
        ? '<?= base_url('inventorybarang/update_serah_terima/') ?>' + id
        : '<?= base_url('inventorybarang/simpan_serah_terima') ?>';

    var postData = {
        inventory_id: inventoryId,
        nama_user: namaUser,
        tanggal_serah: tanggal,
        qty_diserahkan: qty,
        keterangan: keterangan
    };

    $.ajax({
        url: url,
        type: 'POST',
        data: postData,
        dataType: 'json',
        success: function(res) {
            if (res.status === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: res.message,
                    timer: 1500,
                    showConfirmButton: false
                }).then(function() {
                    closeModalSerahTerima();
                    loadSerahTerimaTable();
                    location.reload();
                });
            } else {
                Swal.fire('Gagal', res.message, 'error');
            }
        },
        error: function() {
            Swal.fire('Error', 'Terjadi kesalahan server.', 'error');
        },
        complete: function() {
            btn.prop('disabled', false).html(id ? 'Update' : 'Simpan');
        }
    });
});

// ==================== HAPUS SERAH TERIMA ====================
function hapusSerahTerima(id) {
    Swal.fire({
        title: 'Hapus Serah Terima?',
        html: 'Data serah terima ini akan dihapus permanen.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '<?= base_url('inventorybarang/hapus_serah_terima/') ?>' + id,
                type: 'POST',
                dataType: 'json',
                success: function(res) {
                    if (res.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Terhapus!',
                            text: res.message,
                            timer: 1500,
                            showConfirmButton: false
                        }).then(function() {
                            loadSerahTerimaTable();
                            location.reload();
                        });
                    } else {
                        Swal.fire('Gagal', res.message, 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Terjadi kesalahan server.', 'error');
                }
            });
        }
    });
}
</script>