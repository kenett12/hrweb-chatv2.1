<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Forgot Password | HRWeb Inc.</title>
    
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

<div class="flex h-screen overflow-hidden">
    <div class="hidden lg:block lg:w-[65%] relative bg-cover bg-center" 
         style="background-image: url('<?= base_url('assets/img/bg.jpg') ?>');">
        <div class="absolute inset-0 bg-gradient-to-tr from-[#1e72af]/20 to-transparent"></div>
    </div>

    <div class="flex-1 lg:max-w-[35%] flex flex-col justify-center items-center p-10 relative">
        <div class="w-full max-w-[360px]">
            
            <a href="<?= base_url('login') ?>" class="inline-flex items-center gap-1 text-sm font-semibold mb-8 hover:underline transition-all" style="color: var(--clr-blue);">
                <span class="material-symbols-outlined notranslate text-[18px]">arrow_back</span>
                Back to Login
            </a>

            <div class="mb-10">
                <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight leading-tight">
                    Reset Password
                </h1>
                <p class="text-sm text-gray-500 mt-3 font-medium">
                    Enter your email address and we'll send you a link to reset your password.
                </p>
            </div>

            <form action="#" method="POST" class="space-y-6" onsubmit="event.preventDefault(); alert('Password reset functionality has not been implemented yet. Please contact support.');">
                
                <div class="space-y-2">
                    <label for="email" class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Email Address</label>
                    <input type="email" name="email" id="email" 
                           class="input-vibrant w-full px-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl outline-none transition-all" 
                           placeholder="name@company.com" required>
                </div>

                <button type="submit" 
                        class="w-full py-4 text-white font-bold rounded-xl transition-all shadow-lg hover:brightness-110 active:scale-[0.98]"
                        style="background-color: var(--clr-blue); box-shadow: 0 10px 15px -3px rgba(30, 114, 175, 0.2);">
                    Send Reset Link
                </button>

            </form>

            <div class="mt-12 pt-8 border-t border-gray-100 flex flex-wrap gap-x-6 gap-y-2 text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                <a href="<?= base_url('privacy-terms') ?>" style="color: var(--clr-blue);" class="hover:underline">Privacy & Terms</a>
                <a href="<?= base_url('support') ?>" class="hover:text-gray-600">Contact Support</a>
            </div>
        </div>
    </div>
</div>

</body>
</html>
