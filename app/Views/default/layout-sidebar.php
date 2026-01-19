<?php
$uri = service('uri');
$segment    = $uri->getTotalSegments() >= 1 ? $uri->getSegment(1) : '';
$subsegment = $uri->getTotalSegments() >= 2 ? $uri->getSegment(2) : '';
?>

<ul class="navbar-nav sidebar accordion modern-sidebar"
    id="accordionSidebar">

    <!-- BRAND -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center"
        href="<?= base_url('/') ?>">
        <div class="logo-circle-sidebar">
            <img src="<?= base_url('file/logo/logo1.png') ?>"
                alt="Sataretan Akademi"
                class="sidebar-logo">
        </div>
        <span class="sidebar-brand-text ml-2">SATARETAN</span>
    </a>

    <hr class="sidebar-divider my-3">

    <?php foreach ($menuItems as $item): ?>
        <?php
        $hasSubmenu = !empty($item['submenu']);
        $isParentActive = $segment === ($item['segment'] ?? '');

        if ($hasSubmenu) {
            foreach ($item['submenu'] as $sub) {
                if (
                    $segment === ($item['segment'] ?? '') &&
                    $subsegment === ($sub['subsegment'] ?? '')
                ) {
                    $isParentActive = true;
                }
            }
        }

        $collapseId = 'collapse' . md5($item['title']);
        ?>

        <li class="nav-item <?= $isParentActive ? 'active' : '' ?>">

            <?php if ($hasSubmenu): ?>
                <a class="nav-link d-flex justify-content-between align-items-center
                   <?= !$isParentActive ? 'collapsed' : '' ?>"
                    href="#"
                    data-toggle="collapse"
                    data-target="#<?= $collapseId ?>"
                    aria-expanded="<?= $isParentActive ? 'true' : 'false' ?>">

                    <div class="nav-left d-flex align-items-center">
                        <i class="fas <?= $item['icon'] ?> fa-fw"></i>
                        <span><?= $item['title'] ?></span>
                    </div>
                </a>

                <div id="<?= $collapseId ?>"
                    class="collapse <?= $isParentActive ? 'show' : '' ?>"
                    data-parent="#accordionSidebar">
                    <div class="collapse-inner">
                        <?php foreach ($item['submenu'] as $sub): ?>
                            <?php
                            $isSubActive =
                                $segment === ($item['segment'] ?? '') &&
                                $subsegment === ($sub['subsegment'] ?? '');
                            ?>
                            <a class="collapse-item <?= $isSubActive ? 'active' : '' ?>"
                                href="<?= $sub['url'] ?>">
                                <?= $sub['title'] ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>

            <?php else: ?>
                <a class="nav-link <?= $isParentActive ? 'active' : '' ?>"
                    href="<?= $item['url'] ?>">
                    <div class="nav-left d-flex align-items-center">
                        <i class="fas <?= $item['icon'] ?> fa-fw"></i>
                        <span><?= $item['title'] ?></span>
                    </div>
                </a>
            <?php endif; ?>

        </li>
    <?php endforeach; ?>

</ul>

<div class="sidebar-overlay"></div>

<!-- ===== STYLE SIDEBAR ===== -->
<style>
    /* CONTAINER */
    .modern-sidebar {
        background: #0f0f0f;
        border-right: 1px solid #7f1d1d;
        box-shadow: 8px 0 40px rgba(0, 0, 0, .7);
        width: 240px;
        position: fixed;
        top: 0;
        left: 0;
        height: 100vh;
        z-index: 1040;
        overflow-y: auto;
        transition: transform .3s ease;
    }

    /* BRAND */
    .sidebar-brand {
        height: 90px;
        text-decoration: none;
    }

    .logo-circle-sidebar {
        width: 52px;
        height: 52px;
        border-radius: 50%;
        background: radial-gradient(circle, #facc15, #b45309);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .sidebar-logo {
        width: 34px;
    }

    .sidebar-brand-text {
        font-weight: 800;
        letter-spacing: 1px;
        color: #facc15;
        font-size: 15px;
    }

    /* DIVIDER */
    .sidebar-divider {
        border-top: 1px solid #262626;
    }

    /* NAV ITEM */
    .sidebar .nav-item {
        margin: 4px 10px;
    }

    /* LINK */
    .sidebar .nav-link {
        color: #cfcfcf;
        border-radius: 12px;
        padding: 12px 14px;
        transition: all .25s ease;
    }

    .sidebar .nav-link i {
        color: #facc15;
        margin-right: 10px;
    }

    /* HOVER */
    .sidebar .nav-link:hover {
        background: #1a1a1a;
        color: #fff;
    }

    /* ACTIVE */
    .sidebar .nav-item.active>.nav-link,
    .sidebar .nav-link.active {
        background: linear-gradient(135deg, #facc15, #ca8a04);
        color: #000;
        font-weight: 700;
    }

    .sidebar .nav-item.active>.nav-link i,
    .sidebar .nav-link.active i {
        color: #000;
    }

    /* SUBMENU */
    .collapse-inner {
        background: #111;
        border-radius: 12px;
        margin: 6px 0;
        padding: 6px;
    }

    .collapse-item {
        color: #ccc;
        border-radius: 10px;
        padding: 10px 12px;
        margin-bottom: 4px;
        transition: all .2s ease;
    }

    .collapse-item:hover {
        background: #1f1f1f;
        color: #fff;
    }

    .collapse-item.active {
        background: #facc15;
        color: #000;
        font-weight: 600;
    }

    /* OVERLAY */
    .sidebar-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, .45);
        z-index: 1035;
        display: none;
    }

    /* MOBILE */
    @media (max-width: 768px) {
        .modern-sidebar {
            transform: translateX(-100%);
        }

        body.sidebar-open .modern-sidebar {
            transform: translateX(0);
        }

        body.sidebar-open .sidebar-overlay {
            display: block;
        }

        body.sidebar-open {
            overflow: hidden;
        }
    }
</style>

<!-- ===== SCRIPT ===== -->
<script>
    $(function() {
        $('#sidebarToggleTop').on('click', function() {
            $('body').toggleClass('sidebar-open');
        });

        $('.sidebar-overlay').on('click', function() {
            $('body').removeClass('sidebar-open');
        });
    });
</script>