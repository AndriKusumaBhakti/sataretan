<?= $this->extend('default/layout-template', get_defined_vars()); ?>
<?= $this->section('content'); ?>

<div class="container-fluid">

    <div class="row justify-content-center">

        <div class="col-lg-9">

            <div class="card video-form-binjas border-0 shadow-sm">

                <div class="card-body p-4">

                    <!-- ================= HEADER ================= -->

                    <div class="mb-4">
                        <h4 class="font-weight-bold mb-1">
                            Pengaturan Tryout Cabang
                        </h4>

                        <small class="text-muted">
                            Pilih cabang tryout yang akan diaktifkan
                        </small>
                    </div>


                    <!-- ================= FORM ================= -->

                    <form
                        id="form-tryout"
                        action="<?= base_url('maintenance/kategori-tryout/save') ?>"
                        method="post">

                        <?= csrf_field() ?>


                        <!-- ================= CABANG ================= -->

                        <div class="mb-4">

                            <h5 class="section-title">
                                <i class="fas fa-code-branch mr-2 text-warning"></i>
                                Pilih Cabang
                            </h5>

                            <select
                                id="cabang-select"
                                name="cabang_id"
                                class="form-control select-paket">

                                <option value="">
                                    -- Pilih Cabang --
                                </option>

                                <?php foreach ($cabang as $c): ?>

                                    <option value="<?= $c['id'] ?>">
                                        <?= esc($c['name']) ?>
                                    </option>

                                <?php endforeach ?>

                            </select>

                        </div>



                        <!-- ================= TRYOUT AKADEMIK ================= -->

                        <div class="mb-4">

                            <h5 class="section-title">
                                <i class="fas fa-book mr-2 text-success"></i>
                                Tryout Akademik
                            </h5>

                            <div class="row" id="akademik-container"></div>

                        </div>



                        <!-- ================= TRYOUT PSIKOLOG ================= -->

                        <div class="mb-4">

                            <h5 class="section-title">
                                <i class="fas fa-brain mr-2 text-primary"></i>
                                Tryout Psikolog
                            </h5>

                            <div class="row" id="psikolog-container"></div>

                        </div>


                        <div class="mb-4">

                            <h5 class="section-title">
                                <i class="fas fa-brain mr-2 text-primary"></i>
                                Tryout SKD
                            </h5>

                            <div class="row" id="skd-container"></div>

                        </div>



                        <!-- ================= BUTTON ================= -->

                        <div class="d-flex justify-content-end">

                            <button
                                id="btn-submit"
                                type="submit"
                                class="btn btn-success rounded-pill px-5">
                                Simpan Pengaturan
                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>



<!-- ================= STYLE ================= -->

<style>
    .video-form-binjas {
        border-radius: 20px;
        box-shadow: 0 10px 28px rgba(0, 0, 0, .08);
    }

    .section-title {
        font-weight: 700;
        margin-bottom: 14px;
    }

    .tryout-box {
        border: 1px solid #e4e6ef;
        border-radius: 14px;
        padding: 14px;
        background: #fff;
    }

    .tryout-title {
        font-weight: 600;
        font-size: 14px;
    }

    .select-paket {
        height: 48px;
    }

    .program-box label {
        margin-right: 10px;
        font-size: 13px;
    }
</style>



<!-- ================= SCRIPT ================= -->

<script>
    let csrfName = '<?= csrf_token() ?>'
    let csrfHash = '<?= csrf_hash() ?>'


    /* ================= LOAD CABANG ================= */

    document
        .getElementById('cabang-select')
        .addEventListener('change', function() {

            const cabangId = this.value

            if (!cabangId) {

                document.getElementById('akademik-container').innerHTML = ''
                document.getElementById('psikolog-container').innerHTML = ''
                document.getElementById('skd-container').innerHTML = ''
                return

            }

            fetch("<?= site_url('/maintenance/kategori-tryout/get-by-cabang') ?>", {

                    method: "POST",

                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded",
                        "X-Requested-With": "XMLHttpRequest"
                    },

                    body: new URLSearchParams({
                        cabang_id: cabangId,
                        [csrfName]: csrfHash
                    })

                })
                .then(res => res.json())
                .then(res => {

                    if (res.csrfHash) {
                        csrfHash = res.csrfHash
                    }

                    renderTryout(res)

                })

        })


    /* ================= ENABLE INPUT ================= */

    function toggleInput(checkbox) {

        const box = checkbox.closest('.tryout-box')

        const persen = box.querySelector('.persen-input')
        const mode = box.querySelector('.mode-select')

        persen.disabled = !checkbox.checked
        mode.disabled = !checkbox.checked

    }



    /* ================= SHOW PENILAIAN ================= */

    function togglePenilaian(select) {

        const box = select.closest('.tryout-box')
        const penilaian = box.querySelector('.penilaian-box')

        if (select.value === 'offline') {
            penilaian.style.display = 'block'
        } else {
            penilaian.style.display = 'none'
        }

    }



    /* ================= TEMPLATE TRYOUT ================= */

    function templateTryout(category, item, selected) {

        const checked = selected?.checked ?? false
        const persen = selected?.persen ?? ''
        const mode = selected?.mode ?? ''
        const penilaian = selected?.penilaian_type ?? ''
        const programs = selected?.program ?? []

        const checkProgram = (p) => programs.includes(p) ? 'checked' : ''

        return `

    <div class="col-md-4 mb-3">

        <div class="tryout-box">

            <div class="form-check mb-2">

                <input
                    type="checkbox"
                    class="form-check-input"
                    name="${category}[]"
                    value="${item.key}"
                    ${checked?'checked':''}
                    onchange="toggleInput(this)"
                >

                <label class="form-check-label tryout-title">
                    ${item.value}
                </label>

            </div>

            <div class="row">

                <div class="col-6">

                    <label class="small text-muted">
                        Persentase
                    </label>

                    <input
                        type="number"
                        class="form-control persen-input"
                        name="persen_${category}[${item.key}]"
                        placeholder="0-100"
                        value="${persen}"
                        ${checked?'':'disabled'}
                    >

                </div>


                <div class="col-6">

                    <label class="small text-muted">
                        Mode
                    </label>

                    <select
                        class="form-control select-paket mode-select"
                        name="mode_${category}[${item.key}]"
                        onchange="togglePenilaian(this)"
                        ${checked?'':'disabled'}
                    >

                        <option value="">Pilih</option>

                        <option value="online" ${mode==='online'?'selected':''}>
                            Online
                        </option>

                        <option value="offline" ${mode==='offline'?'selected':''}>
                            Offline
                        </option>

                    </select>

                </div>



                <div
                    class="col-12 mt-2 penilaian-box"
                    style="display:${mode==='offline'?'block':'none'}"
                >

                    <label class="small text-muted">
                        Jenis Penilaian
                    </label>

                    <select
                        class="form-control select-paket"
                        name="penilaian_${category}[${item.key}]"
                    >

                        <option value="">Pilih</option>

                        <option value="pernyataan" ${penilaian==='pernyataan'?'selected':''}>
                            Pernyataan
                        </option>

                        <option value="angka" ${penilaian==='angka'?'selected':''}>
                            Angka
                        </option>

                        <option value="keduanya" ${penilaian==='keduanya'?'selected':''}>
                            Pernyataan + Angka
                        </option>

                    </select>

                </div>



                <div class="col-12 mt-3 program-box">

                    <label class="small text-muted">
                        Program
                    </label>

                    <div>

                        <label>
                            <input
                                type="checkbox"
                                name="program_${category}[${item.key}][]"
                                value="polri"
                                ${checkProgram('polri')}
                            > POLRI
                        </label>

                        <label>
                            <input
                                type="checkbox"
                                name="program_${category}[${item.key}][]"
                                value="kedinasan"
                                ${checkProgram('kedinasan')}
                            > Kedinasan
                        </label>

                        <label>
                            <input
                                type="checkbox"
                                name="program_${category}[${item.key}][]"
                                value="tni"
                                ${checkProgram('tni')}
                            > TNI
                        </label>

                    </div>

                </div>

            </div>

        </div>

    </div>

    `
    }



    /* ================= RENDER TRYOUT ================= */

    function renderTryout(data) {

        const akademikBox = document.getElementById('akademik-container')
        const psikologBox = document.getElementById('psikolog-container')
        const skdBox = document.getElementById('skd-container')

        akademikBox.innerHTML = ''
        psikologBox.innerHTML = ''
        skdBox.innerHTML = ''


        data.pilihan_akademik.forEach(item => {

            const selected = data.akademik.find(x => x.key === item.key) || null

            akademikBox.innerHTML += templateTryout(
                'akademik',
                item,
                selected
            )

        })


        data.pilihan_psikolog.forEach(item => {

            const selected = data.psikolog.find(x => x.key === item.key) || null

            psikologBox.innerHTML += templateTryout(
                'psikolog',
                item,
                selected
            )

        })


        data.pilihan_skd.forEach(item => {

            const selected = data.skd.find(x => x.key === item.key) || null

            skdBox.innerHTML += templateTryout(
                'skd',
                item,
                selected
            )

        })

    }



    /* ================= SUBMIT ================= */

    document
        .getElementById('form-tryout')
        .addEventListener('submit', function() {

            const btn = document.getElementById('btn-submit')

            btn.disabled = true
            btn.innerHTML = 'Menyimpan...'

        })


    <?php if (!empty($selected_cabang)) : ?>

        document.addEventListener("DOMContentLoaded", function() {

            const select = document.getElementById("cabang-select")

            select.value = "<?= $selected_cabang ?>"

            select.dispatchEvent(new Event('change'))

        })

    <?php endif; ?>
</script>


<?= $this->endSection(); ?>