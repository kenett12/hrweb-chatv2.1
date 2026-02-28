<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Login | HRWeb Inc.</title>
    
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />

    <style>
        /* Set base font */
        body { font-family: 'Roboto', sans-serif; }

        /* Material Symbols Rendering Fix */
        .material-symbols-outlined {
            font-family: 'Material Symbols Outlined' !important;
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            display: inline-block;
            white-space: nowrap;
            -webkit-font-smoothing: antialiased;
        }

        /* Vibrant Palette Implementation */
        :root {
            --clr-charcoal: #696C6F;
            --clr-green: #20ae5c;
            --clr-cyan: #3297ca;
            --clr-blue: #1e72af;
            --clr-yellow: #ffc338;
            --clr-red: #eb6063;
        }

        /* Interactive Colorful Focus */
        .input-vibrant:focus {
            border-color: var(--clr-cyan) !important;
            box-shadow: 0 0 0 4px rgba(50, 151, 202, 0.1);
        }

        /* Tailwind Configuration for custom colors */
        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        colors: {
                            'clr-blue': '#1e72af',
                            'clr-cyan': '#3297ca',
                            'clr-green': '#20ae5c',
                        }
                    }
                }
            }
        </script>
    </style>
</head>
<body class="bg-white">

<!-- Global Preloader Overlay -->
<div id="globalPreloader" style="background-color: #ffffff;">
    <div class="preloader-spinner"></div>
</div>

<div class="flex h-screen overflow-hidden">
    <div class="hidden lg:block lg:w-[65%] relative bg-cover bg-center" 
         style="background-image: url('<?= base_url('assets/img/bg.jpg') ?>');">
        <div class="absolute inset-0 bg-gradient-to-tr from-[#1e72af]/20 to-transparent"></div>
    </div>

    <div class="flex-1 lg:max-w-[35%] flex flex-col justify-center items-center p-10 relative">
        <div class="w-full max-w-[360px]">
            
            <div class="mb-10">
                <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight leading-tight">
                    Login to 
                    <span class="inline-flex">
                        <span class="text-gray-900">HR</span>
                        <span style="color: var(--clr-green);">W</span>
                        <span style="color: var(--clr-cyan);">e</span>
                        <span style="color: var(--clr-blue);">b</span>
                        <span class="mr-1.5"></span>
                        <span style="color: var(--clr-yellow);">I</span>
                        <span style="color: var(--clr-red);">nc.</span>
                    </span>
                </h1>
                <div class="h-1 w-12 mt-2 rounded-full" style="background-color: var(--clr-yellow);"></div>
            </div>

            <?php if (session()->getFlashdata('msg')): ?>
                <div class="mb-6 p-4 rounded-xl flex items-center gap-3 border" 
                     style="background-color: rgba(235, 96, 99, 0.05); border-color: var(--clr-red); color: var(--clr-red);">
                    <span class="material-symbols-outlined notranslate">error</span>
                    <span class="text-sm font-semibold"><?= session()->getFlashdata('msg') ?></span>
                </div>
            <?php endif; ?>

            <?= form_open('auth/authenticate', ['class' => 'space-y-6']) ?>
                
                <div class="space-y-2">
                    <?= form_label('Email Address', 'email', ['class' => 'block text-xs font-bold text-gray-400 uppercase tracking-widest']) ?>
                    <?= form_input([
                        'name' => 'email',
                        'id' => 'email',
                        'type' => 'email',
                        'class' => 'input-vibrant w-full px-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl outline-none transition-all',
                        'placeholder' => 'name@company.com',
                        'required' => 'required'
                    ]) ?>
                </div>

                <div class="space-y-2">
                    <div class="flex justify-between items-center">
                        <?= form_label('Password', 'password', ['class' => 'block text-xs font-bold text-gray-400 uppercase tracking-widest']) ?>
                        <a href="<?= base_url('forgot-password') ?>" style="color: var(--clr-blue);" class="text-[10px] font-bold uppercase tracking-widest hover:underline">Forgot?</a>
                    </div>
                    <div class="relative">
                        <?= form_password([
                            'name' => 'password',
                            'id' => 'password',
                            'class' => 'input-vibrant w-full px-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl outline-none transition-all pr-12',
                            'required' => 'required'
                        ]) ?>
                        <button type="button" onclick="togglePassword('password', 'eyeIcon')" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors flex items-center focus:outline-none">
                            <span class="material-symbols-outlined notranslate text-[22px]" id="eyeIcon">visibility</span>
                        </button>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" name="remember" id="remember" 
                           class="w-4 h-4 rounded border-gray-300 cursor-pointer" 
                           style="accent-color: var(--clr-green);">
                    <?= form_label('Keep me logged in', 'remember', ['class' => 'text-sm text-gray-500 font-medium cursor-pointer']) ?>
                </div>

                <button type="submit" 
                        class="w-full py-4 text-white font-bold rounded-xl transition-all shadow-lg hover:brightness-110 active:scale-[0.98]"
                        style="background-color: var(--clr-green); box-shadow: 0 10px 15px -3px rgba(32, 174, 92, 0.2);">
                    Sign In to Portal
                </button>

            <?= form_close() ?>

            <div class="mt-12 pt-8 border-t border-gray-100 flex flex-wrap gap-x-6 gap-y-2 text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                <a href="<?= base_url('privacy-terms') ?>" style="color: var(--clr-blue);" class="hover:underline">Privacy & Terms</a>
                <a href="<?= base_url('support') ?>" class="hover:text-gray-600">Contact Support</a>
            </div>
        </div>
    </div>
</div>

<script>
    /**
     * Toggles password visibility between text and dots
     */
    function togglePassword(inputId, iconId) {
        const passwordInput = document.getElementById(inputId);
        const eyeIcon = document.getElementById(iconId);
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            eyeIcon.textContent = 'visibility_off';
        } else {
            passwordInput.type = 'password';
            eyeIcon.textContent = 'visibility';
        }
    }
</script>
</script>

<script src="<?= base_url('assets/js/global/utils.js') ?>?v=<?= time() ?>"></script>
</body>
</html>