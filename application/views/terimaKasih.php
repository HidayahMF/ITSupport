<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>terimaKasih</title>

    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

    <style>
        /* Text Marquee */
        .marquee-wrapper {
            overflow: hidden;
            white-space: nowrap;
        }

        .marquee-track {
            display: inline-block;
            padding-left: 100%;
            animation: marquee 12s linear infinite;
        }

        @keyframes marquee {
            0% { transform: translateX(0); }
            100% { transform: translateX(-100%); }
        }

        /* Floating Images */
        .floating-img {
            position: absolute;
            width: 90px;
            height: 90px;
            border-radius: 50%;
            object-fit: cover;
            animation: float 6s ease-in-out infinite;
            opacity: 0.8;
            filter: drop-shadow(0 0 10px rgba(0,0,0,0.3));
        }

        @keyframes float {
            0% { transform: translateY(0) rotate(0); }
            50% { transform: translateY(-25px) rotate(20deg); }
            100% { transform: translateY(0) rotate(0); }
        }
    </style>
</head>

<body class="min-h-screen bg-black flex items-center justify-center relative overflow-hidden">

    <!-- MARQUEE TEXT -->
    <div class="absolute top-10 w-full marquee-wrapper">
        <div class="marquee-track text-white font-bold text-5xl tracking-[0.3em]">
            TERIMAKASIH TERIMAKASIH TERIMAKASIH TERIMAKASIH TERIMAKASIH TERIMAKASIH TERIMAKASIH
        </div>
    </div>

    <!-- Random Faces Container -->
    <div id="faces-container" class="w-full h-full"></div>

    <script>
        // URL GAMBAR TEMEN LU
        // const imgSrc = "<?= base_url('assets/img/temen.jpeg') ?>";

        function spawnRandomFace() {
            const img = document.createElement("img");
            img.src = imgSrc;
            img.classList.add("floating-img");

            // Random posisi
            img.style.top = Math.random() * window.innerHeight + "px";
            img.style.left = Math.random() * window.innerWidth + "px";

            // Random ukuran sedikit
            const size = 60 + Math.random() * 80;
            img.style.width = size + "px";
            img.style.height = size + "px";

            document.getElementById("faces-container").appendChild(img);

            // Hapus setelah 10 detik biar ga kebanyakan
            setTimeout(() => { img.remove(); }, 10000);
        }

        // Muncul tiap 300ms
        setInterval(spawnRandomFace, 300);
    </script>

</body>

</html>
