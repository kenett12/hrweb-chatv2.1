<!DOCTYPE html>
<html lang="en" class="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'System Registry' ?> | Pulse HR</title>

    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <link rel="stylesheet" href="<?= base_url('assets/css/global/main.css') ?>?v=<?= time() ?>">

    <script>
        // Prevent FOUC (Flash of Unstyled Content) for the sidebar collapse state
        if (localStorage.getItem('sidebarCollapsed') === 'true') {
            document.documentElement.classList.add('sidebar-is-collapsed');
        }
    </script>

    <?= $this->renderSection('styles') ?>
</head>

<body class="bg-[#f9fafb] text-gray-900 h-screen flex overflow-hidden">

    <!-- Global Preloader Overlay -->
    <div id="globalPreloader">
        <div class="preloader-spinner"></div>
    </div>

    <?php if (!url_is('login*') && !url_is('auth*')): ?>
        <?= $this->include('shared/sidebar') ?>
    <?php endif; ?>

    <div class="flex-1 flex flex-col min-w-0 overflow-hidden relative">

        <?php if (!url_is('login*') && !url_is('auth*')): ?>
            <?= $this->include('shared/header') ?>
        <?php endif; ?>

        <main class="flex-1 overflow-y-auto <?= (url_is('login*') || url_is('auth*')) ? 'p-0' : 'p-6 md:p-8' ?>">

            <!-- Flash data moved to SweetAlert Toasts below -->

            <div class="fade-in">
                <?= $this->renderSection('content') ?>
            </div>

        </main>

        <?php if (!url_is('login*') && !url_is('auth*')): ?>
            <?= $this->include('shared/footer') ?>
        <?php endif; ?>
    </div>

    <script src="<?= base_url('assets/js/global/utils.js') ?>?v=<?= time() ?>"></script>
    <script>
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        <?php if (session()->getFlashdata('success')): ?>
        Toast.fire({
            icon: 'success',
            title: '<?= esc(session()->getFlashdata('success')) ?>'
        });
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
        Toast.fire({
            icon: 'error',
            title: '<?= esc(session()->getFlashdata('error')) ?>'
        });
        <?php endif; ?>
    </script>
    <?= $this->renderSection('scripts') ?>
</body>

</html>