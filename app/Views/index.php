<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Sataretan Akademi</title>
  <link rel="icon" type="image/png" href="<?= base_url('file/logo/logo1.png') ?>" />
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-black text-white font-sans">

  <!-- ================= HEADER ================= -->
  <header class="bg-gradient-to-r from-red-900 to-black sticky top-0 z-50 shadow-lg">
    <div class="max-w-7xl mx-auto flex items-center justify-between px-6 py-4">
      <div class="flex items-center gap-3">
        <img src="<?= base_url('file/logo/logo1.png') ?>" class="h-12 w-12" />
        <span class="text-xl font-extrabold tracking-wide">SATARETAN AKADEMI</span>
      </div>
      <nav class="hidden md:flex gap-6 font-semibold">
        <a href="#home" class="hover:text-yellow-400">Home</a>
        <a href="#informasi" class="hover:text-yellow-400">Program</a>
        <a href="#struktur" class="hover:text-yellow-400">Struktur</a>
        <a href="#alumni" class="hover:text-yellow-400">Alumni</a>
        <a href="#kontak" class="hover:text-yellow-400">Kontak</a>
      </nav>
    </div>
  </header>

  <!-- ================= HERO ================= -->
  <section id="home" class="bg-gradient-to-br from-black via-red-900 to-black">
    <div class="max-w-7xl mx-auto px-6 py-24 grid md:grid-cols-2 gap-10 items-center">

      <div>
        <h1 class="text-4xl md:text-5xl font-extrabold leading-tight">
          Bimbel & Pelatihan <br>
          <span class="text-yellow-400">TNI • POLRI • Kedinasan</span>
        </h1>
        <p class="mt-6 text-gray-300 max-w-xl">
          Program terstruktur Psikologi, Akademik, dan Jasmani
          sesuai standar seleksi nasional.
        </p>
        <div class="mt-8 flex gap-4">
          <a href="<?= base_url('login') ?>" class="bg-yellow-400 text-black px-6 py-3 rounded-xl font-bold">
            Masuk & Daftar
          </a>
          <a href="#informasi"
            class="border border-yellow-400 px-6 py-3 rounded-xl font-bold hover:bg-yellow-400 hover:text-black">
            Detail Program
          </a>
        </div>
      </div>

      <div class="relative aspect-[16/9] rounded-2xl overflow-hidden border border-red-800 bg-black">
        <?php
        $slides = [
          'file/poster/sumenep.png',
          'file/banner/sumenep.png',
          'file/banner/banner_alumni.jpg',
          'file/brosur/su_depan.png',
          'file/brosur/treng_depan.png'
        ];
        foreach ($slides as $i => $img):
        ?>
          <img src="<?= base_url($img) ?>"
            class="slide absolute inset-0 w-full h-full object-cover <?= $i === 0 ? 'opacity-100' : 'opacity-0' ?> transition-opacity duration-1000" />
        <?php endforeach; ?>
      </div>

      <script>
        const slides = document.querySelectorAll('.slide');
        let i = 0;
        setInterval(() => {
          slides[i].classList.replace('opacity-100', 'opacity-0');
          i = (i + 1) % slides.length;
          slides[i].classList.replace('opacity-0', 'opacity-100');
        }, 4000);
      </script>

    </div>
  </section>

  <!-- ================= PROGRAM ================= -->
  <section id="informasi" class="max-w-7xl mx-auto px-6 py-20">
    <h2 class="text-3xl font-extrabold text-center mb-12">Program Unggulan</h2>
    <div class="grid md:grid-cols-3 gap-8">
      <div class="bg-zinc-900 p-8 rounded-2xl border border-red-900 text-center">
        <h3 class="text-xl font-bold text-yellow-400">Psikologi</h3>
        <p class="mt-4 text-gray-300">Mental & Kepribadian</p>
      </div>
      <div class="bg-zinc-900 p-8 rounded-2xl border border-red-900 text-center">
        <h3 class="text-xl font-bold text-yellow-400">Akademik</h3>
        <p class="mt-4 text-gray-300">TWK, TIU, Inggris</p>
      </div>
      <div class="bg-zinc-900 p-8 rounded-2xl border border-red-900 text-center">
        <h3 class="text-xl font-bold text-yellow-400">Jasmani</h3>
        <p class="mt-4 text-gray-300">Samapta & Renang</p>
      </div>
    </div>
  </section>

  <!-- ================= STRUKTUR ================= -->
  <section id="struktur" class="bg-gradient-to-r from-zinc-900 to-black px-6 py-20">
    <div class="max-w-7xl mx-auto">
      <h2 class="text-3xl font-extrabold text-center mb-12">Instruktur</h2>

      <?php
      $instruktur = [
        'infra_3.png',
        'infra_6.png',
        'infra_1.jpg',
        'infra_2.jpg',
        'infra_4.png',
        'infra_5.png',
        'infra_7.png',
        'infra_8.jpg',
        'infra_9.jpg',
        'infra_10.jpg',
        'infra_11.jpg',
      ];

      function card($img)
      {
        return '
      <div class="group relative bg-zinc-900 rounded-2xl overflow-hidden border border-red-900 
        shadow-lg hover:shadow-red-900/40 transition 
        w-full max-w-[200px] mx-auto">

        <div class="h-[260px]">
          <img src="' . base_url('file/infra/' . $img) . '"
            class="w-full h-full object-cover group-hover:scale-105 transition duration-500" />
        </div>

        <div class="p-3 text-center">
          <p class="font-bold text-white text-sm">Instruktur</p>
          <p class="text-xs text-gray-400">Kepala Cabang Sumenep</p>
        </div>
      </div>';
      }
      ?>

      <!-- ROW 1 : 3 -->
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-8 justify-items-center mb-10">
        <?= card($instruktur[0]) ?>
        <?= card($instruktur[1]) ?>
        <?= card($instruktur[2]) ?>
      </div>

      <!-- ROW 2 : 4 -->
      <div class="grid grid-cols-1 sm:grid-cols-4 gap-8 justify-items-center mb-10">
        <?= card($instruktur[3]) ?>
        <?= card($instruktur[4]) ?>
        <?= card($instruktur[5]) ?>
        <?= card($instruktur[6]) ?>
      </div>

      <!-- ROW 3 : 4 -->
      <div class="grid grid-cols-1 sm:grid-cols-4 gap-8 justify-items-center">
        <?= card($instruktur[7]) ?>
        <?= card($instruktur[8]) ?>
        <?= card($instruktur[9]) ?>
        <?= card($instruktur[10]) ?>
      </div>


    </div>
  </section>


  <!-- ================= ALUMNI (NAMING FIXED) ================= -->
  <section id="alumni" class="bg-gradient-to-b from-black to-zinc-900 px-6 py-20">
    <div class="max-w-7xl mx-auto">
      <h2 class="text-3xl font-extrabold text-center mb-4">Alumni Berprestasi</h2>
      <p class="text-center text-gray-400 mb-10">TNI • POLRI • Kedinasan</p>

      <div id="alumniGrid"
        class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-6 place-items-center">

        <?php
        $alumni = [
          2026 => 52,
          2025 => 14,
          2024 => 1,
        ];

        foreach ($alumni as $tahun => $jumlah):
          for ($i = 1; $i <= $jumlah; $i++):
            $file = "file/alumni/alumni_{$tahun}_{$i}.jpg";
        ?>
            <div class="alumni-item hidden w-full max-w-[180px]">
              <div class="group relative rounded-xl overflow-hidden border border-red-900 bg-black">
                <div class="aspect-[3/4]">
                  <img src="<?= base_url($file) ?>" loading="lazy"
                    class="w-full h-full object-cover transition group-hover:scale-105" />
                </div>
                <div class="absolute inset-0 bg-black/70 opacity-0 group-hover:opacity-100 transition flex items-end">
                  <div class="p-3">
                    <p class="text-yellow-400 font-bold text-sm">Alumni <?= $tahun ?></p>
                    <p class="text-xs text-gray-300">Sataretan Akademi</p>
                  </div>
                </div>
              </div>
            </div>
        <?php
          endfor;
        endforeach;
        ?>
      </div>

      <div class="flex justify-center gap-4 mt-12">
        <button id="prevBtn"
          class="px-5 py-2 border border-yellow-400 rounded-lg text-yellow-400 hover:bg-yellow-400 hover:text-black disabled:opacity-30">
          Prev
        </button>
        <button id="nextBtn"
          class="px-5 py-2 border border-yellow-400 rounded-lg text-yellow-400 hover:bg-yellow-400 hover:text-black disabled:opacity-30">
          Next
        </button>
      </div>
    </div>
  </section>

  <!-- ================= KONTAK ================= -->
  <!-- ================= KONTAK ================= -->
  <section id="kontak" class="bg-red-900 px-6 py-20">
    <div class="max-w-5xl mx-auto text-center">
      <h2 class="text-3xl font-extrabold mb-10">Hubungi Kami</h2>

      <div class="grid md:grid-cols-2 gap-8 text-left">

        <!-- Lokasi Sumenep -->
        <div class="bg-black/30 p-6 rounded-2xl border border-red-800">
          <h3 class="text-xl font-bold text-yellow-400 mb-3">📍 Sumenep</h3>
          <p class="text-gray-200">
            Dsn. Brambang, Desa Kalimo'ok,<br>
            Kalianget, Kab. Sumenep
          </p>
          <p class="mt-4 font-bold text-yellow-300">
            📞 <a href="https://wa.me/6285706770538"
              target="_blank"
              class="hover:underline hover:text-yellow-400">
              +62 857-0677-0538
            </a>
          </p>
        </div>

        <!-- Lokasi Trenggalek -->
        <div class="bg-black/30 p-6 rounded-2xl border border-red-800">
          <h3 class="text-xl font-bold text-yellow-400 mb-3">📍 Trenggalek</h3>
          <p class="text-gray-200">
            Perumnas Kalutan No. A1,<br>
            Kab. Trenggalek
          </p>
          <p class="mt-4 font-bold text-yellow-300">
            📞 <a href="https://wa.me/6285755088597"
              target="_blank"
              class="hover:underline hover:text-yellow-400">
              +62 857-5508-8597
            </a>
          </p>
        </div>

      </div>

      <!-- Sosial Media -->
      <div class="mt-10 text-center">
        <p class="font-semibold text-gray-200">Instagram</p>
        <p class="text-yellow-300 font-bold">@sataretan.akademi</p>
      </div>
    </div>
  </section>


  <footer class="bg-black text-center py-6 text-gray-400">
    © <?= date('Y') ?> SATARETAN Akademi · All Rights Reserved
  </footer>

  <!-- ================= PAGINATION SCRIPT ================= -->
  <script>
    const alumniItems = document.querySelectorAll('.alumni-item');
    const perPage = 20;
    let page = 1;

    function renderPage(p) {
      alumniItems.forEach((item, i) => {
        item.classList.toggle(
          'hidden',
          !(i >= (p - 1) * perPage && i < p * perPage)
        );
      });
      prevBtn.disabled = p === 1;
      nextBtn.disabled = p * perPage >= alumniItems.length;
    }

    prevBtn.onclick = () => {
      page--;
      renderPage(page);
    };
    nextBtn.onclick = () => {
      page++;
      renderPage(page);
    };

    renderPage(page);
  </script>

</body>

</html>