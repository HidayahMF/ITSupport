<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Permintaan Sistem IT</title>

    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Select2 CSS & jQuery -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <!-- Boxicons -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <style>
        :root {
            --primary-white: #ffffff;
            --app-bg: #f4f7f9;
            --secondary-blue-light: #e0f2fe;
            --secondary-blue: #38bdf8;
            --third-orange: #f97316;
            --third-orange-hover: #ea580c;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --border-color: #cbd5e1;
            --focus-ring: rgba(56, 189, 248, 0.4);
        }

        * {
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

        body {
            background-color: var(--app-bg);
            margin: 0;
            padding: 40px 20px;
            color: var(--text-main);
            display: flex;
            justify-content: center;
        }

        .container {
            background: var(--primary-white);
            width: 100%;
            max-width: 760px;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.04);
            padding: 40px 50px;
            animation: fadeIn 0.5s ease-out;
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
        }

        .header h2 {
            margin: 0;
            font-size: 24px;
            font-weight: 700;
            color: var(--text-main);
        }

        .header p {
            margin: 8px 0 0;
            color: var(--text-muted);
            font-size: 14px;
        }

        .section-title {
            font-size: 16px;
            font-weight: 600;
            color: var(--secondary-blue);
            margin: 30px 0 20px;
            display: flex;
            align-items: center;
        }

        .section-title::before {
            content: '';
            display: inline-block;
            width: 8px;
            height: 24px;
            background: var(--third-orange);
            border-radius: 4px;
            margin-right: 12px;
        }

        .form-group {
            margin-bottom: 24px;
        }

        label {
            display: block;
            font-weight: 500;
            margin-bottom: 8px;
            font-size: 14px;
            color: var(--text-main);
        }

        input,
        textarea {
            width: 100%;
            padding: 14px 16px;
            background: #f8fafc;
            border: 1px solid var(--border-color);
            border-radius: 10px;
            font-size: 14px;
            color: var(--text-main);
            transition: all 0.2s ease;
            resize: vertical;
        }

        input:focus,
        textarea:focus {
            background: var(--primary-white);
            border-color: var(--secondary-blue);
            outline: none;
            box-shadow: 0 0 0 4px var(--secondary-blue-light);
        }

        input[readonly] {
            background-color: #e2e8f0;
            color: var(--text-muted);
            cursor: not-allowed;
        }

        /* Custom Select2 SaaS Style */
        .select2-container--default .select2-selection--single {
            background-color: #f8fafc;
            border: 1px solid var(--border-color);
            border-radius: 10px;
            height: 48px;
            padding: 10px 16px;
            display: flex;
            align-items: center;
            transition: all 0.2s ease;
        }

        .select2-container--default.select2-container--open .select2-selection--single,
        .select2-container--default .select2-selection--single:focus {
            background: var(--primary-white);
            border-color: var(--secondary-blue);
            box-shadow: 0 0 0 4px var(--secondary-blue-light);
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            padding: 0;
            color: var(--text-main);
            font-size: 14px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 46px;
            right: 10px;
        }

        .select2-dropdown {
            border-color: var(--secondary-blue-light);
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        /* Button */
        button {
            width: 100%;
            padding: 16px;
            background: var(--third-orange);
            color: var(--primary-white);
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            margin-top: 20px;
            box-shadow: 0 4px 14px rgba(249, 115, 22, 0.3);
        }

        button:hover {
            background: var(--third-orange-hover);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(249, 115, 22, 0.4);
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 600px) {
            .container {
                padding: 30px 20px;
            }

            body {
                padding: 20px 10px;
            }
        }


        /* Custom Select2 SaaS Style Modernization */
        .select2-container--default .select2-selection--single {
            background-color: #f8fafc;
            border: 1px solid var(--border-color);
            border-radius: 10px;
            height: 52px;
            /* Lebih proporsional */
            padding: 12px 16px;
            display: flex;
            align-items: center;
            transition: all 0.3s ease;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }

        /* Focus & Open State */
        .select2-container--default.select2-container--open .select2-selection--single,
        .select2-container--default .select2-selection--single:focus,
        .select2-container--default.select2-container--focus .select2-selection--single {
            background: var(--primary-white);
            border-color: var(--secondary-blue);
            box-shadow: 0 0 0 4px var(--secondary-blue-light);
            outline: none;
        }

        /* Typography Text Terpilih */
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            padding: 0;
            color: var(--text-main);
            font-size: 15px;
            font-weight: 500;
        }

        /* Placeholder */
        .select2-container--default .select2-selection--single .select2-selection__placeholder {
            color: #94a3b8;
            font-weight: 400;
        }

        /* Arrow Customization & Animation */
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 50px;
            right: 14px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow b {
            border-color: #94a3b8 transparent transparent transparent;
            border-width: 6px 5px 0 5px;
            transition: transform 0.3s ease;
        }

        .select2-container--default.select2-container--open .select2-selection--single .select2-selection__arrow b {
            transform: rotate(180deg);
            border-color: var(--secondary-blue) transparent transparent transparent;
        }

        /* Dropdown Menu Box */
        .select2-dropdown {
            border: 1px solid var(--secondary-blue-light);
            border-radius: 12px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin-top: 8px;
            /* Jarak dari input box */
            animation: slideDown 0.2s ease-out;
        }

        /* Search Input inside Dropdown */
        .select2-search--dropdown {
            padding: 12px;
        }

        .select2-search--dropdown .select2-search__field {
            border-radius: 8px;
            border: 1px solid var(--border-color);
            padding: 10px 14px;
            background-color: #f8fafc;
            transition: all 0.2s;
        }

        .select2-search--dropdown .select2-search__field:focus {
            border-color: var(--secondary-blue);
            box-shadow: 0 0 0 3px var(--secondary-blue-light);
            outline: none;
        }

        /* Options Item Default & Hover */
        .select2-results__option {
            padding: 12px 16px;
            font-size: 14px;
            color: var(--text-main);
            transition: background-color 0.1s ease;
        }

        .select2-container--default .select2-results__option--highlighted.select2-results__option--selectable {
            background-color: var(--secondary-blue-light);
            color: var(--secondary-blue);
            font-weight: 600;
        }

        .select2-container--default .select2-results__option--selected {
            background-color: #f1f5f9;
        }

        /* Animation Dropdown Muncul */
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* AI Button & Suggestion Box */
        .masalah-wrapper {
            position: relative;
        }

        .btn-ai {
            position: absolute;
            right: 10px;
            bottom: 15px;
            background: var(--secondary-blue-light);
            color: var(--secondary-blue);
            border: 1px solid var(--secondary-blue);
            border-radius: 8px;
            padding: 6px 12px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            width: auto;
            margin: 0;
            box-shadow: none;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .btn-ai:hover {
            background: var(--secondary-blue);
            color: var(--primary-white);
            transform: none;
            box-shadow: none;
        }

        .btn-ai:disabled {
            opacity: 0.7;
            cursor: wait;
        }

        .ai-suggestion-box {
            display: none;
            background: #fffbeb;
            border: 1px solid #fde68a;
            border-radius: 10px;
            padding: 15px;
            margin-top: 10px;
            font-size: 13px;
            color: #92400e;
            line-height: 1.5;
            animation: fadeIn 0.3s ease-out;
        }

        .ai-suggestion-box strong {
            display: block;
            margin-bottom: 5px;
            color: #b45309;
        }

        .btn-apply-ai {
            background: #f59e0b;
            color: white;
            border: none;
            border-radius: 6px;
            padding: 6px 10px;
            font-size: 12px;
            cursor: pointer;
            margin-top: 10px;
            width: auto;
            box-shadow: none;
        }

        .btn-apply-ai:hover {
            background: #d97706;
            transform: none;
            box-shadow: none;
        }

        /* --- NAVBAR SAAS STYLE --- */
        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: var(--primary-white);
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.05);
            padding: 15px 50px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 1000;
        }

        .nav-brand {
            font-size: 18px;
            font-weight: 700;
            color: var(--secondary-blue);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .nav-brand span {
            color: var(--text-main);
        }

        .nav-links {
            display: flex;
            gap: 20px;
        }

        .nav-links a {
            text-decoration: none;
            color: var(--text-muted);
            font-weight: 500;
            font-size: 14px;
            padding: 8px 12px;
            border-radius: 8px;
            transition: all 0.2s ease;
        }

        .nav-links a:hover {
            color: var(--third-orange);
            background: #fff7ed;
        }

        .nav-links a.active {
            color: var(--third-orange);
            background: #fff7ed;
            font-weight: 600;
        }

        .nav-brand i {
            font-size: 24px;
            color: var(--secondary-blue);
        }

        .nav-links a {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .nav-links i {
            font-size: 18px;
        }
    </style>
</head>

<body>

    <div class="container">
        <nav class="navbar">
            <a href="<?= site_url('systemrequest') ?>" class="nav-brand">
                <i class='bx bxs-zap'></i> <span>IT Request</span>
            </a>
            <div class="nav-links">
                <a href="<?= site_url('systemrequest') ?>" class="active">
                    <i class='bx bx-edit-alt'></i> Form Request
                </a>
                <a href="<?= site_url('systemrequest/report') ?>">
                    <i class='bx bx-bar-chart-square'></i> Report Dashboard
                </a>
            </div>
        </nav>

        <form action="<?= site_url('systemrequest/submit') ?>" method="POST">

            <div class="section-title">Informasi Peminta</div>

            <div class="form-group">
                <label>Cari Karyawan (NIP / Nama)</label>
                <select id="cari_karyawan" style="width: 100%;" required></select>
                <input type="hidden" name="nama_peminta" id="nama_peminta">
                <!-- nip -->
                <input type="hidden" name="nip" id="nip">
            </div>

            <div class="form-group">
                <label>Departemen</label>
                <input type="text" name="departemen_peminta" id="departemen_peminta" readonly placeholder="Terisi otomatis berdasarkan karyawan...">
            </div>

            <div class="form-group">
                <label>Kontak (Email / WhatsApp)</label>
                <input type="text" name="kontak_peminta" placeholder="Masukkan kontak yang bisa dihubungi..." required>
            </div>

            <div class="section-title">Spesifikasi Kebutuhan</div>

            <div class="form-group">
                <label>1. Ada masalah apa?</label>
                <div class="masalah-wrapper">
                    <textarea name="masalah" id="masalah" rows="4" placeholder="Jelaskan kendala saat ini..." required></textarea>
                    <button type="button" id="btn-tanya-ai" class="btn-ai">✨ Tanya AI</button>
                </div>

                <div id="ai-suggestion-box" class="ai-suggestion-box">
                    <strong>💡 AI Suggestion (Solusi & Ide):</strong>
                    <div id="ai-response-text"></div>
                    <button type="button" id="btn-apply-ai" class="btn-apply-ai">Salin ke Solusi</button>
                </div>
            </div>
            <div class="form-group"><label>2. Bagaimana solusinya?</label><textarea name="solusi" rows="3" placeholder="Sistem seperti apa yang bisa menyelesaikan masalah tersebut..." required></textarea></div>
            <div class="form-group"><label>3a. Bagaimana keadaan before?</label><textarea name="kondisi_before" rows="3" placeholder="Alur kerja sebelum ada sistem (manual)..." required></textarea></div>
            <div class="form-group"><label>3b. Bagaimana keadaan yang diinginkan?</label><textarea name="kondisi_after" rows="3" placeholder="Ekspektasi proses setelah sistem diimplementasikan..." required></textarea></div>
            <div class="form-group"><label>4. Preferensi UI Form?</label><textarea name="preferensi_ui" rows="2" placeholder="Contoh: Clean, minimalis, mirip dashboard Google..."></textarea></div>
            <div class="form-group"><label>5. Output / Data yang ingin dilihat?</label><textarea name="kebutuhan_data_output" rows="3" placeholder="Contoh: Laporan excel bulanan, grafik pie chart..." required></textarea></div>

            <button type="submit">Submit Request</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            $('#cari_karyawan').select2({
                placeholder: 'Ketik NIP atau Nama Karyawan...',
                minimumInputLength: 2,
                ajax: {
                    url: '<?= site_url("systemrequest/get_employees") ?>',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            searchTerm: params.term
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data
                        };
                    },
                    cache: true
                }
            });


            <?php if ($this->session->flashdata('status_swal')): ?>
                Swal.fire({
                    icon: '<?= $this->session->flashdata('status_swal'); ?>',
                    title: '<?= $this->session->flashdata('status_swal') == 'success' ? 'Berhasil!' : 'Opss..'; ?>',
                    text: '<?= $this->session->flashdata('pesan_swal'); ?>',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'OK'
                });
            <?php endif; ?>


            $('#cari_karyawan').on('select2:select', function(e) {
                var data = e.params.data;
                $('#nama_peminta').val(data.text);
                $('#departemen_peminta').val(data.dept ? data.dept : 'Tidak ada departemen');
                $('#nip').val(data.id ? data.id : 'Tidak ada NIP');
            });

            // Fitur Tanya AI
            $('#btn-tanya-ai').click(function() {
                var masalahText = $('#masalah').val();
                var btn = $(this);

                if (masalahText.trim() === '') {
                    alert('Harap ketik masalahnya dulu sebelum bertanya ke AI.');
                    $('#masalah').focus();
                    return;
                }

                // Efek loading pada tombol
                btn.prop('disabled', true).html('⏳ Memproses...');

                $.ajax({
                    url: '<?= site_url("systemrequest/ask_ai") ?>',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        masalah: masalahText
                    },
                    success: function(res) {
                        if (res.status === 'success') {
                            // Tampilkan hasil AI dengan memecah baris baru menjadi <br>
                            var formattedText = res.data.replace(/\n/g, '<br>');
                            $('#ai-response-text').html(formattedText);
                            $('#ai-suggestion-box').slideDown();
                        } else {
                            alert(res.message);
                        }
                    },
                    error: function() {
                        alert('Terjadi kesalahan saat menghubungi AI.');
                    },
                    complete: function() {
                        // Kembalikan state tombol
                        btn.prop('disabled', false).html('✨ Tanya AI');
                    }
                });
            });

            // Fitur untuk otomatis menyalin saran AI ke textarea "Solusi"
            $('#btn-apply-ai').click(function() {
                var aiText = $('#ai-response-text').text(); // Ambil teks murni
                $('textarea[name="solusi"]').val(aiText).focus();

                // Opsional: Beri efek highlight agar user sadar kolom solusi sudah terisi
                $('textarea[name="solusi"]').css('border-color', 'var(--third-orange)');
                setTimeout(function() {
                    $('textarea[name="solusi"]').css('border-color', 'var(--border-color)');
                }, 1000);
            });
        });
    </script>
</body>

</html>