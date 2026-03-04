<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In | HRWeb Inc.</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="<?= base_url('assets/css/global/main.css') ?>?v=<?= time() ?>">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .material-symbols-outlined { font-family: 'Material Symbols Outlined' !important; font-variation-settings: 'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24; display: inline-block; white-space: nowrap; -webkit-font-smoothing: antialiased; }
        .login-bg { background-image: url('<?= base_url('assets/img/bg.jpg') ?>'); background-size: cover; background-position: center; position: relative; }
        .login-bg::before { content:''; position:absolute; inset:0; background:rgba(10,20,35,0.72); z-index:0; }
        .login-bg > * { position: relative; z-index: 1; }
    </style>
</head>
<body class="h-screen overflow-hidden" style="background:#f7f7f7;">

<!-- Preloader -->
<div id="globalPreloader" style="position:fixed;inset:0;background:#f7f7f7;z-index:9999;display:flex;align-items:center;justify-content:center;">
    <div class="preloader-spinner"></div>
</div>

<div class="flex h-screen">

    <!-- Left: Brand Panel with bg.jpg -->
    <div class="hidden lg:flex lg:w-1/2 xl:w-3/5 flex-col justify-between p-12 login-bg">

        <!-- Top logo -->
        <div class="flex items-center gap-3">
            <img src="<?= base_url('assets/img/logo.png') ?>" alt="HRWeb Inc." class="h-10 object-contain" style="filter:brightness(0) invert(1);" onerror="this.style.display='none'">
        </div>

        <!-- Center content -->
        <div>
            <h2 class="text-4xl font-light text-white leading-tight mb-4">Streamline your<br><span class="font-semibold" style="color:#0070f2;">HR operations.</span></h2>
            <p class="text-base font-light" style="color:rgba(255,255,255,0.55); max-width:380px; line-height:1.7;">Manage your TSR teams, client accounts, and support tickets from one unified enterprise workspace.</p>

            <!-- Feature pills -->
            <div class="flex flex-wrap gap-2 mt-8">
                <?php foreach(['TSR Management','Client Registry','Ticket Oversight','Live Chat','Bot Knowledge Base'] as $f): ?>
                <span class="px-3 py-1.5 rounded text-xs font-medium" style="background:rgba(0,112,242,0.15); color:rgba(255,255,255,0.7); border:1px solid rgba(0,112,242,0.3);"><?= $f ?></span>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Copyright -->
        <p class="text-xs" style="color:rgba(255,255,255,0.3);">&copy; <?= date('Y') ?> HRWeb Inc. All rights reserved.</p>
    </div>

    <!-- Right: Login Form Panel -->
    <div class="flex-1 flex flex-col justify-center items-center bg-white p-8 lg:p-16 relative">

        <!-- Top bar for mobile -->
        <div class="absolute top-0 left-0 right-0 h-12 lg:hidden flex items-center px-6" style="background:#0f1e2e;">
            <img src="<?= base_url('assets/img/logo.png') ?>" alt="HRWeb Inc." class="h-6 object-contain" style="filter:brightness(0) invert(1);" onerror="this.style.display='none'">
        </div>

        <div class="w-full max-w-sm mt-8 lg:mt-0">

            <!-- Title -->
            <div class="mb-8">
                <h1 class="text-2xl font-semibold" style="color:#32363a;">Sign In</h1>
                <p class="text-sm mt-1" style="color:#6a6d70;">Enter your credentials to access the HRWeb portal.</p>
            </div>

            <!-- Error flash -->
            <?php if (session()->getFlashdata('msg')): ?>
            <div class="mb-5 p-3 rounded flex items-center gap-3 text-sm" style="background:#fff0f0; border:1px solid #f5c0c0; color:#bb0000; border-radius:4px;">
                <span class="material-symbols-outlined text-[18px]">error</span>
                <span><?= session()->getFlashdata('msg') ?></span>
            </div>
            <?php endif; ?>

            <?= form_open('auth/authenticate', ['class' => 'space-y-5']) ?>

                <!-- Email -->
                <div>
                    <label class="block text-xs font-semibold mb-1.5 uppercase tracking-wider" style="color:#6a6d70;" for="email">Email Address</label>
                    <?= form_input([
                        'name' => 'email',
                        'id' => 'email',
                        'type' => 'email',
                        'class' => 'w-full px-3 py-2 text-sm border outline-none transition-all',
                        'style' => 'border-color:#d9d9d9; border-radius:4px; color:#32363a; font-family:inherit;',
                        'placeholder' => 'name@company.com',
                        'required' => 'required',
                        'onfocus' => "this.style.borderColor='#0070f2'; this.style.boxShadow='0 0 0 2px rgba(0,112,242,0.2)';",
                        'onblur' => "this.style.borderColor='#d9d9d9'; this.style.boxShadow='none';"
                    ]) ?>
                </div>

                <!-- Password -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="text-xs font-semibold uppercase tracking-wider" style="color:#6a6d70;" for="password">Password</label>
                        <a href="<?= base_url('forgot-password') ?>" class="text-xs font-medium" style="color:#0070f2;">Forgot password?</a>
                    </div>
                    <div class="relative">
                        <?= form_password([
                            'name' => 'password',
                            'id' => 'password',
                            'class' => 'w-full px-3 py-2 text-sm border outline-none transition-all pr-10',
                            'style' => 'border-color:#d9d9d9; border-radius:4px; color:#32363a; font-family:inherit;',
                            'required' => 'required',
                            'onfocus' => "this.style.borderColor='#0070f2'; this.style.boxShadow='0 0 0 2px rgba(0,112,242,0.2)';",
                            'onblur' => "this.style.borderColor='#d9d9d9'; this.style.boxShadow='none';"
                        ]) ?>
                        <button type="button" onclick="togglePassword('password','eyeIcon')" class="absolute right-3 top-1/2 -translate-y-1/2 focus:outline-none transition-colors" style="color:#89919a;" onmouseover="this.style.color='#32363a'" onmouseout="this.style.color='#89919a'">
                            <span class="material-symbols-outlined text-[18px]" id="eyeIcon">visibility</span>
                        </button>
                    </div>
                </div>

                <!-- Remember -->
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="remember" id="remember" class="w-4 h-4 rounded cursor-pointer" style="accent-color:#0070f2;">
                    <label for="remember" class="text-sm cursor-pointer" style="color:#6a6d70;">Keep me signed in</label>
                </div>

                <!-- Submit -->
                <button type="submit" class="w-full py-2.5 text-white text-sm font-semibold transition-colors focus:outline-none" style="background:#0070f2; border-radius:4px; border:1px solid #0070f2;" onmouseover="this.style.background='#055ec3'" onmouseout="this.style.background='#0070f2'">
                    Sign In
                </button>

            <?= form_close() ?>

            <!-- Footer links -->
            <div class="mt-8 pt-6 border-t flex gap-4 text-xs" style="border-color:#e8e8e8;">
                <a href="<?= base_url('privacy-terms') ?>" style="color:#0070f2;" class="hover:underline">Privacy &amp; Terms</a>
                <a href="<?= base_url('support') ?>" style="color:#6a6d70;" class="hover:underline">Contact Support</a>
            </div>
        </div>
    </div>
</div>

<script>
function togglePassword(inputId, iconId) {
    const i = document.getElementById(inputId), e = document.getElementById(iconId);
    i.type = i.type === 'password' ? 'text' : 'password';
    e.textContent = i.type === 'password' ? 'visibility' : 'visibility_off';
}
// Hide preloader
window.addEventListener('load', () => { const p=document.getElementById('globalPreloader'); if(p){p.style.opacity='0'; setTimeout(()=>p.style.display='none',300);} });
</script>
<script src="<?= base_url('assets/js/global/utils.js') ?>?v=<?= time() ?>"></script>
</body>
</html>