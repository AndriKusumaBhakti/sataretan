<?= $this->extend('default/layout-template') ?>
<?= $this->section('content') ?>

<div class="container-fluid">
    <div class="text-center mb-4">
        <h1 class="h5 font-weight-bold text-white">
            Hitung Nilai Jasmani T.A. 2026
        </h1>
    </div>

    <div class="row justify-content-center">
        <div class="col-xl-5 col-lg-6 col-md-8">

            <form id="form-jasmani" method="post" action="<?= site_url('tryout/jasmani/store') ?>" class="jasmani-card">
                <?= csrf_field() ?>

                <?php if ($isGuruOrAdmin): ?>
                    <div class="form-group">
                        <label>Pilih User</label>
                        <select name="user_id" class="form-control select-users" required>
                            <option value="">-- Pilih User --</option>
                            <?php foreach ($users as $u): ?>
                                <option value="<?= $u['id'] ?>">
                                    <?= esc($u['name']) ?> (<?= esc($u['email']) ?>)
                                </option>
                            <?php endforeach ?>
                        </select>
                    </div>
                <?php endif ?>

                <div class="form-group">
                    <label>Program</label>
                    <select id="program" name="program" class="form-control select-users" required>
                        <option value="">-- Pilih Program --</option>
                        <option value="tni">TNI</option>
                        <option value="polri">POLRI</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Jenis Kelamin</label>
                    <select id="jenis_kelamin" name="jenis_kelamin" class="form-control select-users" required>
                        <option value="">-- Pilih --</option>
                        <option value="pria">Pria</option>
                        <option value="wanita">Wanita</option>
                    </select>
                </div>

                <!-- ====== FORM DINAMIS ====== -->
                <div id="field-tni-pria" class="jasmani-field d-none">
                    <?= view('tryout/jasmani/partials/tni_pria') ?>
                </div>

                <div id="field-tni-wanita" class="jasmani-field d-none">
                    <?= view('tryout/jasmani/partials/tni_wanita') ?>
                </div>

                <div id="field-polri-pria" class="jasmani-field d-none">
                    <?= view('tryout/jasmani/partials/polri_pria') ?>
                </div>

                <div id="field-polri-wanita" class="jasmani-field d-none">
                    <?= view('tryout/jasmani/partials/polri_wanita') ?>
                </div>

                <button id="btn-submit" class="btn btn-success btn-block mt-3 font-weight-bold">
                    Hitung & Simpan
                </button>
            </form>

        </div>
    </div>
</div>

<style>
    .jasmani-card {
        background: rgba(255, 255, 255, .95);
        border-radius: 18px;
        padding: 22px;
        box-shadow: 0 18px 45px rgba(0, 0, 0, .25);
    }

    label {
        font-size: 12.5px;
        font-weight: 600;
        color: #6c757d;
    }

    .form-control {
        border-radius: 10px;
        font-size: 14px;
    }

    .bg-light {
        background: #edf0f2 !important;
    }

    .divider {
        margin: 16px 0 12px;
        padding: 6px 12px;
        font-size: 13px;
        font-weight: 700;
        background: #f1f3f5;
        color: #6c757d;
        border-radius: 8px;
    }

    .btn-warning {
        background: #27ae60;
        border: none;
        color: #fff;
    }

    .select-users {
        height: 48px;
        padding: 6px 42px 6px 14px;
        appearance: none;
        cursor: pointer;
        background-repeat: no-repeat;
        background-position: right 14px center;
        background-size: 14px;
    }
</style>

<script>
    const form = document.getElementById('form-jasmani');
    const program = document.getElementById('program');
    const gender = document.getElementById('jenis_kelamin');

    /* ================= DEBOUNCE ================= */
    let hitungTimer = null;
    const HITUNG_DELAY = 200; // ms

    /* ================= TOGGLE FORM ================= */
    function hideAll() {
        document.querySelectorAll('.jasmani-field').forEach(el => {
            el.classList.add('d-none');
            el.querySelectorAll('input').forEach(i => i.disabled = true);
        });
    }

    function show(id) {
        const el = document.getElementById(id);
        if (!el) return;
        el.classList.remove('d-none');
        el.querySelectorAll('input').forEach(i => i.disabled = false);
    }

    function toggleForm() {
        hideAll();
        if (program.value && gender.value) {
            show(`field-${program.value}-${gender.value}`);
        }
    }

    program.addEventListener('change', toggleForm);
    gender.addEventListener('change', toggleForm);

    function getActive() {
        return document.querySelector('.jasmani-field:not(.d-none)');
    }

    /* ================= CSRF ================= */
    let csrfName = '<?= csrf_token() ?>';
    let csrfHash = '<?= csrf_hash() ?>';

    /* ================= AMBIL SEMUA NILAI (HASIL) ================= */
    function collectAllNilai(active) {
        const data = {};
        active.querySelectorAll('[data-nilai]').forEach(el => {
            if (el.value !== '') {
                data[el.dataset.nilai] = Number(el.value);
            }
        });
        return data;
    }

    /* ================= INPUT HANDLER ================= */
    document.addEventListener('input', e => {
        const active = getActive();
        if (!active) return;

        /* ================= BMI (REALTIME) ================= */
        if (
            e.target.classList.contains('tinggi') ||
            e.target.classList.contains('berat')
        ) {
            const tinggi = active.querySelector('.tinggi')?.value;
            const berat = active.querySelector('.berat')?.value;
            if (!tinggi || !berat) return;

            fetch("<?= site_url('kalkulator/bmi') ?>", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded",
                        "X-Requested-With": "XMLHttpRequest"
                    },
                    body: new URLSearchParams({
                        tinggi,
                        berat,
                        gender: gender.value,
                        [csrfName]: csrfHash
                    })
                })
                .then(r => r.json())
                .then(r => {
                    active.querySelector('.nilai_bmi').value = r.bmi ?? '';
                    active.querySelector('.kategori_bmi').value = r.kategori ?? '';
                    csrfHash = r.csrfHash ?? csrfHash;
                });
            return;
        }

        /* ================= GARJAS (DELAYED) ================= */
        if (!e.target.dataset.type) return;

        clearTimeout(hitungTimer);

        hitungTimer = setTimeout(() => {
            const current = getActive();
            if (!current) return;

            fetch("<?= site_url('kalkulator/hitung') ?>", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded",
                        "X-Requested-With": "XMLHttpRequest"
                    },
                    body: new URLSearchParams({
                        type: e.target.dataset.type,
                        nilai: Number(e.target.value),
                        kategori: program.value,
                        gender: gender.value,
                        [csrfName]: csrfHash
                    })
                })
                .then(r => r.json())
                .then(r => {
                    const n = current.querySelector(
                        `[data-nilai="${e.target.dataset.type}"]`
                    );
                    if (n) n.value = r.nilai ?? '';

                    csrfHash = r.csrfHash ?? csrfHash;
                });

        }, HITUNG_DELAY);
    });

    /* ================= VALIDASI ================= */
    form.addEventListener('submit', e => {
        let valid = true;

        // reset error
        document.querySelectorAll('.is-invalid').forEach(el => {
            el.classList.remove('is-invalid');
        });

        if (!program.value) {
            program.classList.add('is-invalid');
            valid = false;
        }

        if (!gender.value) {
            gender.classList.add('is-invalid');
            valid = false;
        }

        const active = getActive();
        if (!active) {
            alert('Silakan pilih Program dan Jenis Kelamin');
            e.preventDefault();
            return;
        }

        active.querySelectorAll('input:not([type="hidden"])').forEach(input => {
            if (input.disabled) return;

            if (input.value === '') {
                input.classList.add('is-invalid');
                valid = false;
            }

            if (input.type === 'number') {
                const val = Number(input.value);
                if (isNaN(val) || val < 0) {
                    input.classList.add('is-invalid');
                    valid = false;
                }
            }
        });

        if (!valid) {
            e.preventDefault();
            alert('⚠️ Lengkapi semua field dengan nilai yang valid');
            document.querySelector('.is-invalid')?.scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });
        } else {
            const btn = document.getElementById('btn-submit');
            btn.disabled = true;
            btn.innerHTML = `
            <span class="spinner-border spinner-border-sm mr-2"></span>
            Menyimpan...
        `;
        }
    });
</script>

<?= $this->endSection() ?>