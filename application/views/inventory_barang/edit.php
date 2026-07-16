<!-- HEADER -->
<div class="mb-8">
    <h2 class="text-3xl font-bold text-gray-800">Edit Inventory Barang</h2>
    <p class="text-gray-500">Ubah data barang inventory</p>
</div>

<!-- FORM -->
<div class="p-6 bg-white rounded-2xl shadow-lg">
    <form id="formEdit" onsubmit="return false;">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <!-- No PR -->
            <div>
                <label class="block text-sm font-semibold text-gray-600 mb-1">No PR <span class="text-red-500">*</span></label>
                <input type="text" name="no_pr" id="no_pr" required
                       value="<?= htmlspecialchars($item['no_pr']) ?>"
                       class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition text-sm">
            </div>

            <!-- No MRP -->
            <div>
                <label class="block text-sm font-semibold text-gray-600 mb-1">No MRP <span class="text-red-500">*</span></label>
                <input type="text" name="no_mrp" id="no_mrp" required
                       value="<?= htmlspecialchars($item['no_mrp']) ?>"
                       class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition text-sm">
            </div>

            <!-- Nama User -->
            <div>
                <label class="block text-sm font-semibold text-gray-600 mb-1">Nama User <span class="text-red-500">*</span></label>
                <input type="text" name="nama_user" id="nama_user" required
                       value="<?= htmlspecialchars($item['nama_user']) ?>"
                       class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition text-sm">
            </div>

            <!-- Nama Barang -->
            <div>
                <label class="block text-sm font-semibold text-gray-600 mb-1">Nama Barang <span class="text-red-500">*</span></label>
                <input type="text" name="nama_barang" id="nama_barang" required
                       value="<?= htmlspecialchars($item['nama_barang']) ?>"
                       class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition text-sm">
            </div>

            <!-- Qty -->
            <div>
                <label class="block text-sm font-semibold text-gray-600 mb-1">Qty <span class="text-red-500">*</span></label>
                <input type="number" name="qty" id="qty" required min="1"
                       value="<?= $item['qty'] ?>"
                       class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition text-sm">
            </div>

            <!-- Toko -->
            <div>
                <label class="block text-sm font-semibold text-gray-600 mb-1">Toko <span class="text-red-500">*</span></label>
                <input type="text" name="toko" id="toko" required
                       value="<?= htmlspecialchars($item['toko']) ?>"
                       class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition text-sm">
            </div>

            <!-- Tanggal PR -->
            <div>
                <label class="block text-sm font-semibold text-gray-600 mb-1">Tanggal PR <span class="text-red-500">*</span></label>
                <input type="date" name="tanggal_pr" id="tanggal_pr" required
                       value="<?= $item['tanggal_pr'] ?>"
                       class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition text-sm">
            </div>

            <!-- Tanggal Terima -->
            <div>
                <label class="block text-sm font-semibold text-gray-600 mb-1">Tanggal Terima <span class="text-red-500">*</span></label>
                <input type="date" name="tanggal_terima" id="tanggal_terima" required
                       value="<?= $item['tanggal_terima'] ?>"
                       class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition text-sm">
            </div>

            <!-- Status -->
            <div>
                <label class="block text-sm font-semibold text-gray-600 mb-1">Status <span class="text-red-500">*</span></label>
                <select name="status" id="status" required onchange="toggleTanggalDiserahkan()"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition text-sm">
                    <option value="Menunggu Barang" <?= ($item['status'] === 'Menunggu Barang') ? 'selected' : '' ?>>Menunggu Barang</option>
                    <option value="Sudah Diterima IT" <?= ($item['status'] === 'Sudah Diterima IT') ? 'selected' : '' ?>>Sudah Diterima IT</option>
                    <option value="Sudah Diserahkan ke User" <?= ($item['status'] === 'Sudah Diserahkan ke User') ? 'selected' : '' ?>>Sudah Diserahkan ke User</option>
                </select>
            </div>

            <!-- Tanggal Diserahkan -->
            <div id="wrapTglDiserahkan" class="<?= ($item['status'] !== 'Sudah Diserahkan ke User') ? 'hidden' : '' ?>">
                <label class="block text-sm font-semibold text-gray-600 mb-1">Tanggal Diserahkan <span class="text-red-500">*</span></label>
                <input type="date" name="tanggal_diserahkan" id="tanggal_diserahkan"
                       value="<?= $item['tanggal_diserahkan'] ?>"
                       class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition text-sm">
            </div>

            <!-- Keterangan -->
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-gray-600 mb-1">Keterangan</label>
                <textarea name="keterangan" id="keterangan" rows="3"
                          class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition text-sm resize-none"><?= htmlspecialchars($item['keterangan']) ?></textarea>
            </div>

        </div>

        <!-- TOMBOL -->
        <div class="flex justify-end gap-3 mt-8">
            <a href="<?= base_url('inventorybarang') ?>"
               class="px-6 py-2.5 bg-gray-300 hover:bg-gray-400 text-gray-700 rounded-xl font-semibold transition text-sm">
                Batal
            </a>
            <button type="submit" id="btnUpdate"
                    class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-semibold transition text-sm">
                Update
            </button>
        </div>

    </form>
</div>


<script>
function toggleTanggalDiserahkan() {
    var status = $('#status').val();
    var $wrap = $('#wrapTglDiserahkan');
    var $input = $('#tanggal_diserahkan');
    if ($wrap.length === 0 || $input.length === 0) return;
    if (status === 'Sudah Diserahkan ke User') {
        $wrap.removeClass('hidden');
        $input.prop('required', true);
    } else {
        $wrap.addClass('hidden');
        $input.prop('required', false).val('');
    }
}

$(document).ready(function() {
    toggleTanggalDiserahkan();

    // Submit AJAX
    $('#formEdit').on('submit', function(e) {
        e.preventDefault();

        var qty = parseInt($('#qty').val());
        if (isNaN(qty) || qty < 1) {
            Swal.fire('Validasi', 'Qty harus lebih dari 0.', 'warning');
            return;
        }

        var status = $('#status').val();
        if (status === 'Sudah Diserahkan ke User' && !$('#tanggal_diserahkan').val()) {
            Swal.fire('Validasi', 'Tanggal diserahkan wajib diisi.', 'warning');
            return;
        }

        var btn = $('#btnUpdate');
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...');

        $.ajax({
            url: '<?= base_url('inventorybarang/update/' . $item['id']) ?>',
            type: 'POST',
            data: $('#formEdit').serialize(),
            dataType: 'json',
            success: function(res) {
                if (res.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: res.message,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = '<?= base_url('inventorybarang') ?>';
                    });
                } else {
                    Swal.fire('Gagal', res.message, 'error');
                }
            },
            error: function() {
                Swal.fire('Error', 'Terjadi kesalahan server.', 'error');
            },
            complete: function() {
                btn.prop('disabled', false).html('Update');
            }
        });
    });
});
</script>
