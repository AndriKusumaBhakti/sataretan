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
    /* BRAND */
    .sidebar-brand {
        height: 90px;
        text-decoration: none;
    }

    .logo-circle-sidebar {
        width: 52px;
        height: 52px;
        border-radius: 50%;
        background: radial-gradient(circle, #dc2626, #7f1d1d);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .sidebar-logo {
        width: 50px;
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

    /* HOVER */
    .sidebar .nav-link:hover {
        background: #1a1a1a;
        color: #fff;
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

    /* OVERLAY */
    .sidebar-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, .45);
        z-index: 1035;
        display: none;
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