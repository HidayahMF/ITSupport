<!-- HEADER -->
<div class="mb-8">
    <h2 class="text-3xl font-bold text-gray-800">Detail Inventory Barang</h2>
    <p class="text-gray-500">Informasi lengkap barang inventory</p>
</div>

<!-- TRACKING FLOW -->
<div class="p-6 bg-white rounded-2xl shadow-lg mb-6">
    <h3 class="text-lg font-semibold text-gray-700 mb-4">Status Tracking</h3>
    <?php
        $steps = array('Menunggu Barang', 'Sudah Diterima IT', 'Sudah Diserahkan ke User');
        $icons = array('⏳', '📥', '✔️');
        $activeIdx = array_search($item['status'], $steps);
        if ($activeIdx === false) $activeIdx = 0;
    ?>
    <div class="flex items-center justify-between">
        <?php foreach ($steps as $i => $step): ?>
            <?php $isActive = $i <= $activeIdx; ?>
            <div class="flex flex-col items-center flex-1">
                <div class="w-12 h-12 rounded-full flex items-center justify-center text-xl mb-2 <?= $isActive ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-500' ?>">
                    <?= $icons[$i] ?>
                </div>
                <p class="text-xs font-semibold text-center <?= $isActive ? 'text-green-600' : 'text-gray-400' ?>"><?= $step ?></p>
            </div>
            <?php if ($i < count($steps) - 1): ?>
                <div class="flex-1 h-1 mx-2 rounded-full <?= ($i < $activeIdx) ? 'bg-green-500' : 'bg-gray-200' ?>"></div>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
</div>

<!-- DETAIL DATA -->
<div class="p-6 bg-white rounded-2xl shadow-lg">
    <div class="flex items-center justify-between mb-6">
        <h3 class="text-lg font-semibold text-gray-700">Data Barang</h3>
        <div class="flex gap-2">
            <a href="<?= base_url('inventorybarang/edit/' . $item['id']) ?>"
               class="px-4 py-2 bg-yellow-100 text-yellow-700 rounded-xl font-semibold text-sm hover:bg-yellow-200 transition">
                Edit
            </a>
            <a href="<?= base_url('inventorybarang') ?>"
               class="px-4 py-2 bg-gray-200 text-gray-700 rounded-xl font-semibold text-sm hover:bg-gray-300 transition">
                Kembali
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="p-4 bg-gray-50 rounded-xl">
            <p class="text-xs text-gray-500 font-semibold uppercase">No PR</p>
            <p class="text-sm font-bold text-gray-800 mt-1"><?= htmlspecialchars($item['no_pr']) ?></p>
        </div>
        <!-- <div class="p-4 bg-gray-50 rounded-xl">
            <p class="text-xs text-gray-500 font-semibold uppercase">No MRP</p>
            <p class="text-sm font-bold text-gray-800 mt-1"><?= htmlspecialchars($item['no_mrp']) ?></p>
        </div> -->
        <div class="p-4 bg-gray-50 rounded-xl">
            <p class="text-xs text-gray-500 font-semibold uppercase">Nama User</p>
            <p class="text-sm font-bold text-gray-800 mt-1"><?= htmlspecialchars($item['nama_user']) ?></p>
        </div>
        <div class="p-4 bg-gray-50 rounded-xl">
            <p class="text-xs text-gray-500 font-semibold uppercase">Nama Barang</p>
            <p class="text-sm font-bold text-gray-800 mt-1"><?= htmlspecialchars($item['nama_barang']) ?></p>
        </div>
        <div class="p-4 bg-gray-50 rounded-xl">
            <p class="text-xs text-gray-500 font-semibold uppercase">Qty</p>
            <p class="text-sm font-bold text-gray-800 mt-1"><?= $item['qty'] ?></p>
        </div>
        <div class="p-4 bg-gray-50 rounded-xl">
            <p class="text-xs text-gray-500 font-semibold uppercase">Toko</p>
            <p class="text-sm font-bold text-gray-800 mt-1"><?= htmlspecialchars($item['toko']) ?></p>
        </div>
        <div class="p-4 bg-gray-50 rounded-xl">
            <p class="text-xs text-gray-500 font-semibold uppercase">Tanggal PR</p>
            <p class="text-sm font-bold text-gray-800 mt-1"><?= date('d/m/Y', strtotime($item['tanggal_pr'])) ?></p>
        </div>
        <div class="p-4 bg-gray-50 rounded-xl">
            <p class="text-xs text-gray-500 font-semibold uppercase">Tanggal Terima</p>
            <p class="text-sm font-bold text-gray-800 mt-1"><?= date('d/m/Y', strtotime($item['tanggal_terima'])) ?></p>
        </div>
        <div class="p-4 bg-gray-50 rounded-xl">
            <p class="text-xs text-gray-500 font-semibold uppercase">Tanggal Diserahkan</p>
            <p class="text-sm font-bold text-gray-800 mt-1">
                <?= !empty($item['tanggal_diserahkan']) ? date('d/m/Y', strtotime($item['tanggal_diserahkan'])) : '-' ?>
            </p>
        </div>
        <!-- <div class="p-4 bg-gray-50 rounded-xl">
            <!-- <p class="text-xs text-gray-500 font-semibold uppercase">Lead Time</p> -->
            <?php
                $lead_time = '-';
                if (!empty($item['tanggal_pr']) && !empty($item['tanggal_terima'])) {
                    $tgl_pr = new DateTime($item['tanggal_pr']);
                    $tgl_terima = new DateTime($item['tanggal_terima']);
                    $lead_time = $tgl_pr->diff($tgl_terima)->days . ' Hari';
                }
            ?>
            <p class="text-sm font-bold text-blue-600 mt-1"><?= $lead_time ?></p>
        </div> -->

        <div class="p-4 bg-gray-50 rounded-xl">
            <p class="text-xs text-gray-500 font-semibold uppercase">Status</p>
            <div class="mt-1">
                <?php if ($item['status'] === 'Menunggu Barang'): ?>
                    <span class="px-3 py-1 rounded-full bg-orange-100 text-orange-700 text-xs font-semibold">Menunggu Barang</span>
                <?php elseif ($item['status'] === 'Sudah Diterima IT'): ?>
                    <span class="px-3 py-1 rounded-full bg-yellow-100 text-yellow-700 text-xs font-semibold">Sudah Diterima IT</span>
                <?php else: ?>
                    <span class="px-3 py-1 rounded-full bg-green-100 text-green-700 text-xs font-semibold">Sudah Diserahkan ke User</span>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="mt-4 p-4 bg-gray-50 rounded-xl">
        <p class="text-xs text-gray-500 font-semibold uppercase">Keterangan</p>
        <p class="text-sm text-gray-800 mt-1"><?= !empty($item['keterangan']) ? htmlspecialchars($item['keterangan']) : '-' ?></p>
    </div>
</div>
