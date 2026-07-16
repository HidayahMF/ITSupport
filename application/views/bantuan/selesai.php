<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- JQUERY -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- TAILWINDCSS v4 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

    <!-- SELECT2 -->
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- DATATABLES -->
    <link rel="stylesheet"
        href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <!-- SWEETALERT -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- WEBCAM -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.26/webcam.min.js"></script>

    <style>
        .select2-container .select2-selection--single {
            height: 48px !important;
            padding: 8px 12px !important;
            border-radius: 12px !important;
            border: 1px solid #d1d5db !important;
            background-color: #ffffff !important;
        }

        .select2-selection__rendered {
            line-height: 32px !important;
            color: #374151 !important;
        }

        .select2-selection__arrow {
            top: 10px !important;
            right: 10px !important;
        }

        .animate-ping-slow {
            animation: ping 1.4s cubic-bezier(0, 0, 0.2, 1) infinite;
        }
    </style>

    <title>Ticket Penyelesaian</title>
</head>

<body class="bg-gray-100">

    <?php if ($tiket->dt == 0) : ?>
        <!-- MAIN CONTAINER -->
        <div class="min-h-screen flex flex-col md:flex-row">

            <!-- LEFT SIDE (IMAGE) -->
            <div class="hidden md:flex w-1/2 bg-gradient-to-br from-purple-600 via-blue-500 to-indigo-500 p-12 text-white">
                <div class="flex flex-col justify-center items-center w-full space-y-6">

                    <h1 class="text-2xl font-semibold tracking-wide">Foto Masalah (opsional)</h1>

                    <?php if (!empty($tiket->gambar)): ?>
                        <img src="<?= base_url('uploads/it_support/img/' . $tiket->gambar) ?>"
                            class="rounded-xl shadow-lg" />
                    <?php else: ?>
                        <img src="<?= base_url('uploads/it_support/file/' . $tiket->gambar3) ?>"
                            class="rounded-xl shadow-lg opacity-40" />
                    <?php endif; ?>

                    <div class="text-center">
                        <h1 class="bg-white flex gap-3 rounded-xl text-2xl text-slate-700 px-6 py-3">
                            <?= $tiket->time ?>
                        </h1>
                    </div>

                </div>
            </div>

            <!-- RIGHT SIDE (FORM) -->
            <div class="w-full md:w-1/2 bg-white flex items-center justify-center px-8 py-12">
                <div class="w-full max-w-md space-y-8">

                    <!-- HEADER -->
                    <div>
                        <h2 class="text-3xl font-bold text-gray-800">
                            Ticket Number : <?= $tiket->id ?>
                        </h2>
                        <p class="text-gray-500 mt-1 italic">
                            مَنْ جَدَّ وَجَدَ
                        </p>
                    </div>

                    <!-- FORM -->
                    <form class="space-y-6" id="penyelesaian" method="POST">
                        <input type="hidden" name="id" value="<?= $tiket->id ?>">

                        <!-- PIC -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">PIC Pengerjaan</label>
                            <select name="NONIK" class="select2 w-full">
                                <option value="" disabled selected>─ Pilih PIC ─</option>

                                <?php foreach ($pic as $employee): ?>
                                    <option value="<?= $employee['NONIK'] ?>">
                                        <?= $employee['NONIK'] ?> — <?= $employee['NM_KAR'] ?> (<?= $employee['KODEF'] ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Tipe Perbaikan -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tipe Perbaikan</label>
                            <select name="Tipe"
                                class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-300 text-gray-800 shadow-sm focus:ring-2 focus:ring-blue-500 transition">
                                <option value="" disabled selected>Pilih Tipe Perbaikan</option>
                                <option value="Network">🔌 Network</option>
                                <option value="Web App">💻 Web App</option>
                                <option value="TroubleShoot Hardware">🛠 TroubleShoot Hardware</option>
                                <option value="New Installation">📦 New Installation</option>
                                <option value="New Feature App">✨ New Feature App</option>
                                <option value="Email">📩 Email</option>
                                <option value="Active Directory">🗂 Active Directory</option>
                                <option value="Permintaan Hardware">📁 Permintaan Hardware</option>
                                <option value="Other">❓ Other</option>
                            </select>
                        </div>

                        <!-- Detail -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Detail Pekerjaan</label>
                            <textarea name="report" rows="4"
                                placeholder="Perbaikan yang dilakukan..."
                                class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-300 text-gray-800 shadow-sm resize-none focus:ring-2 focus:ring-blue-500 transition"></textarea>
                        </div>

                        <button type="submit"
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-xl font-semibold shadow-lg transition">
                            Selesaikan
                        </button>
                    </form>

                </div>
            </div>
        </div>
    <?php else : ?>
        <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-green-50 to-green-100 p-6">
            <div class="bg-white rounded-3xl shadow-2xl p-10 text-center max-w-lg w-full space-y-6">

                <!-- ICON CHECK ANIMATION -->
                <div class="flex justify-center">
                    <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center">
                        <svg class="w-12 h-12 text-green-600 animate-ping-slow" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                </div>

                <h1 class="text-3xl font-bold text-gray-800">
                    Input Berhasil!
                </h1>

                <p class="text-gray-600 leading-relaxed">
                    Terima kasih, Telah diproses oleh tim IT.
                </p>

                <div class="bg-green-50 text-green-700 p-4 rounded-xl border border-green-200">
                    <p class="font-medium">Ticket ID: <span class="font-bold text-green-900"><?= $tiket->id ?></span></p>
                </div>

            </div>
        </div>
    <?php endif;  ?>


    <!-- SELECT2 -->
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                width: '100%'
            });
        });
    </script>

    <!-- AJAX -->
    <script>
        $(document).on("submit", "#penyelesaian", function(e) {
            e.preventDefault();

            // === VALIDATION ===
            let pic = $("select[name='NONIK']").val();
            if (!pic) {
                Swal.fire({
                    icon: "warning",
                    title: "PIC Belum Dipilih",
                    text: "Silakan pilih PIC terlebih dahulu!"
                });
                return;
            }

            let tipe = $("select[name='Tipe']").val();
            if (!tipe) {
                Swal.fire({
                    icon: "warning",
                    title: "Tipe Perbaikan Belum Dipilih",
                    text: "Silakan pilih jenis perbaikan!"
                });
                return;
            }

            let report = $("textarea[name='report']").val().trim();
            if (report.length === 0) {
                Swal.fire({
                    icon: "warning",
                    title: "Detail Pekerjaan Kosong",
                    text: "Tolong isi detail pekerjaan!"
                });
                return;
            }

            // === SUBMIT AJAX ===
            $.ajax({
                type: "POST",
                url: "<?= base_url('bantuan/proses_selesai') ?>",
                data: $(this).serialize(),
                dataType: "json",

                success: function(res) {
                    Swal.fire({
                        icon: "success",
                        title: "Berhasil!",
                        text: "Penyelesaian berhasil disimpan!",
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = "<?= base_url('terimaKasih') ?>";
                    });
                },

                error: function(xhr) {
                    console.log(xhr.responseText);
                    Swal.fire({
                        icon: "error",
                        title: "Gagal!",
                        text: "Terjadi kesalahan. Cek console!"
                    });
                }
            });
        });
    </script>



</body>

</html>