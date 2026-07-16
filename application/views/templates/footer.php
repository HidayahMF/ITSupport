<script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.26/webcam.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    //Select 2
    $(document).ready(function() {
        $('#selectKaryawan').select2({
            width: '100%',
            placeholder: "Cari nama karyawan...",
            allowClear: true
        });
    });
</script>

<!-- Hamburger -->
<script>
    const sidebar = document.getElementById("sidebar");
    const overlay = document.getElementById("overlay");
    const hamburger = document.getElementById("hamburgerBtn");

    // buka sidebar
    hamburger.addEventListener("click", () => {
        sidebar.classList.remove("-translate-x-full");
        overlay.classList.remove("hidden");
    });

    // tutup sidebar
    overlay.addEventListener("click", () => {
        sidebar.classList.add("-translate-x-full");
        overlay.classList.add("hidden");
    });
</script>


<script>
    let useFrontCamera = false; // default kamera belakang

    $(document).ready(function() {
        // TABLE
        $('#latestTable').DataTable({
            pageLength: 5,
            lengthChange: false,
            ordering: false,
            paging: false,
            info: true,
            language: {
                search: "Search:"
            }
        });

        // CAMERA INIT
        if ($("#camera").length) {
            initCamera();
        }
    });

    function initCamera() {
        Webcam.reset();

        Webcam.set({
            width: 350,
            height: 260,
            image_format: 'jpeg',
            jpeg_quality: 90,
            constraints: {
                facingMode: useFrontCamera ? "user" : "environment"
            }
        });

        Webcam.attach('#camera');

        // Update icon
        $("#switchCameraIcon").text(useFrontCamera ? "🔄" : "🔄");
    }

    function switchCamera() {
        useFrontCamera = !useFrontCamera;

        // animasi kecil
        $("#camera").addClass("opacity-0 scale-95 transition duration-200");

        setTimeout(() => {
            initCamera();
            $("#camera").removeClass("opacity-0 scale-95");
        }, 200);
    }

    function take() {
        Webcam.snap(function(data_uri) {
            $("#previewBox").removeClass("hidden");
            $("#result").html(`
                <img src="${data_uri}" class="rounded-xl w-full shadow-md" />
            `);

            $("#fotoBase64").val(data_uri);
        });
    }
</script>



<script>
    fetch("https://app.bmc.co.id/itSupport/Api/callAPi")
        .then(response => response.json())
        .then(data => {

            let bulan = data.map(x => x.bulan);
            let total = data.map(x => x.total);

            const canvas = document.getElementById('chartCalling');

            if (canvas) {
                const ctx = canvas.getContext('2d');

                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: bulan,
                        datasets: [{
                            label: "Calling Per Month",
                            data: total,
                            backgroundColor: '#4993FA'
                        }]
                    },
                    options: {
                        responsive: true
                    }
                });
            }


        });
</script>

<!-- Ajax form -->
<script>
    $("#forIT").on("submit", function(e) {
        e.preventDefault();

        let formData = new FormData(this);

        // ---- CEK FOTO DARI CAMERA (BASE64) ----
        let fotoBase64 = $("#fotoBase64").val();
        if (!fotoBase64 || fotoBase64.trim().length === 0) {
            // kalau kosong → hapus biar backend tidak error
            formData.delete("foto");
        }

        // ---- CEK FILE LAMPIRAN OPSIONAL ----
        let lampiran = formData.get("lampiran");
        if (!lampiran || lampiran.size === 0) {
            formData.delete("lampiran");
        }

        $.ajax({
            url: "<?= base_url('bantuan/simpan') ?>",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,

            success: function(res) {
                Swal.fire({
                    icon: "success",
                    title: "Berhasil",
                    text: "Laporan sudah terkirim!",
                }).then(() => location.reload());
            },

            error: function(xhr) {
                console.log(xhr.responseText);
            }
        });
    });
</script>


<script>
    document.addEventListener("DOMContentLoaded", function() {
        let now = new Date();

        // Format YYYY-MM-DD HH:MM:SS
        let formatted =
            now.getFullYear() + "-" +
            String(now.getMonth() + 1).padStart(2, "0") + "-" +
            String(now.getDate()).padStart(2, "0") + " " +
            String(now.getHours()).padStart(2, "0") + ":" +
            String(now.getMinutes()).padStart(2, "0") + ":" +
            String(now.getSeconds()).padStart(2, "0");

        var el = document.getElementById("tanggal");
        if (el) {
            el.value = formatted;
        }
    });
</script>






</body>

</html>