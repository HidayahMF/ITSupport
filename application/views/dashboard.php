<!-- HEADER -->
<div class="mb-8">
    <h2 class="text-3xl font-bold text-gray-800">Dashboard</h2>
    <p class="text-gray-500">Laporan Aktivitas IT Support</p>
</div>

<!-- SUMMARY CARDS -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">

    <!-- Total Request -->
    <div class="p-5 bg-white rounded-2xl shadow-md hover:shadow-lg transition flex items-center gap-4">
        <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-xl flex justify-center items-center text-2xl">📞</div>
        <div>
            <p class="text-gray-500 text-sm">Total Request</p>
            <h3 class="text-xl font-bold text-gray-800"><?= $totalRequest ?></h3>
        </div>
    </div>

    <!-- Selesai -->
    <div class="p-5 bg-white rounded-2xl shadow-md hover:shadow-lg transition flex items-center gap-4">
        <div class="w-12 h-12 bg-green-100 text-green-600 rounded-xl flex justify-center items-center text-2xl">✔️</div>
        <div>
            <p class="text-gray-500 text-sm">Selesai</p>
            <h3 class="text-xl font-bold text-gray-800"><?= $selesaiReq ?></h3>
        </div>
    </div>

    <!-- Pending -->
    <div class="p-5 bg-white rounded-2xl shadow-md hover:shadow-lg transition flex items-center gap-4">
        <div class="w-12 h-12 bg-yellow-100 text-yellow-500 rounded-xl flex justify-center items-center text-2xl">⏳</div>
        <div>
            <p class="text-gray-500 text-sm">Pending</p>
            <h3 class="text-xl font-bold text-gray-800"><?= $pendingReq ?></h3>
        </div>
    </div>

    <!-- User Aktif -->
    <div class="p-5 bg-white rounded-2xl shadow-md hover:shadow-lg transition flex items-center gap-4">
        <div class="w-12 h-12 bg-purple-100 text-purple-600 rounded-xl flex justify-center items-center text-2xl">👥</div>
        <div>
            <p class="text-gray-500 text-sm">User Aktif</p>
            <h3 class="text-xl font-bold text-gray-800"><?= $jumlahKaryawan ?></h3>
        </div>
    </div>

</div>

<!-- CHART ATAS -->
<div class="mb-10">
    <div class="p-7 bg-white rounded-2xl shadow-lg hover:shadow-xl transition">
        <h3 class="text-lg font-semibold text-gray-700 mb-4">Calling Per Month</h3>
        <canvas id="chartCalling"></canvas>
    </div>
</div>

<!-- TABLE BAWAH -->
<div class="p-7 bg-white rounded-2xl shadow-lg hover:shadow-xl transition">

    <h2 class="text-xl font-semibold text-gray-700 mb-4">Latest Requests</h2>

    <div class="overflow-hidden border border-gray-200 rounded-xl">
        <table id="latestTable" class="min-w-full table-auto">
            <thead class="bg-blue-50">
                <tr class="text-left text-gray-600 text-sm font-semibold">
                    <th class="py-3 px-4 w-16">ID</th>
                    <th class="py-3 px-4 w-48">User</th>
                    <th class="py-3 px-4">Problem</th>
                    <th class="py-3 px-4 w-28">Tgl</th>
                    <th class="py-3 px-4 w-24">Status</th>
                </tr>
            </thead>

            <tbody class="text-gray-700">
                <?php foreach ($lateReq as $Req): ?>
                    <tr>
                        <td class="py-4 px-4 font-medium"><?= $Req['id'] ?></td>
                        <td class="py-4 px-4 font-semibold"><?= $Req['NM_KAR'] ?></td>
                        <td class="py-4 px-4"><?= $Req['report'] ?></td>
                        <td class="py-4 px-4"><?= $Req['tgl'] ?></td>
                        <td class="py-4 px-4">
                            <?php if (!empty($Req['report2'])): ?>
                                <span class="px-3 py-1 rounded-full bg-green-100 text-green-700 text-xs font-semibold">
                                    Done
                                </span>
                            <?php else: ?>
                                <span class="px-3 py-1 rounded-full bg-yellow-100 text-yellow-700 text-xs font-semibold">
                                    Pending
                                </span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</div>