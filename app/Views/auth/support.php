<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Contact Support | HRWeb Inc.</title>
    
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,600&display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />

    <style>
        /* Set base font */
        body { font-family: 'DM Sans', sans-serif; }

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
<body class="bg-gray-50 flex flex-col min-h-screen">

<div class="flex flex-col items-center p-10 relative flex-1 mt-10">
    <div class="w-full max-w-2xl bg-white p-10 rounded-2xl shadow-sm border border-gray-100">
        
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight leading-tight">
                    Contact Support
                </h1>
                <div class="h-1 w-12 mt-2 rounded-full" style="background-color: var(--clr-yellow);"></div>
            </div>
            <a href="<?= base_url('login') ?>" class="text-sm font-semibold flex items-center gap-1 hover:underline transition-all" style="color: var(--clr-blue);">
                <span class="material-symbols-outlined notranslate text-[18px]">arrow_back</span>
                Back to Login
            </a>
        </div>

        <div class="prose prose-sm max-w-none text-gray-600 space-y-8 mt-6">
            
            <div class="flex items-start gap-4">
                <div class="p-3 rounded-xl" style="background-color: rgba(32, 174, 92, 0.1); color: var(--clr-green);">
                    <span class="material-symbols-outlined notranslate">location_on</span>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-gray-900 mb-1">Office Location</h2>
                    <p class="leading-relaxed">
                        Cavite: SertTech Bldg. B2 L1 Birmingham Plains,<br>
                        Brgy. Bacao 1, Gen. Trias Cavite 4107 Philippines
                    </p>
                </div>
            </div>

            <div class="flex items-start gap-4">
                <div class="p-3 rounded-xl" style="background-color: rgba(50, 151, 202, 0.1); color: var(--clr-cyan);">
                    <span class="material-symbols-outlined notranslate">mail</span>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-gray-900 mb-1">Email Support</h2>
                    <p class="leading-relaxed">
                        [Placeholder Email Address]
                    </p>
                </div>
            </div>
            
            <div class="flex items-start gap-4">
                <div class="p-3 rounded-xl" style="background-color: rgba(235, 96, 99, 0.1); color: var(--clr-red);">
                    <span class="material-symbols-outlined notranslate">phone_in_talk</span>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-gray-900 mb-1">Phone Number</h2>
                    <p class="leading-relaxed">
                        [Placeholder Phone Number]
                    </p>
                </div>
            </div>

        </div>

        <div class="mt-12 pt-8 border-t border-gray-100 flex flex-wrap gap-x-6 gap-y-2 text-[10px] font-bold text-gray-400 uppercase tracking-widest">
            <span>&copy; <?= date('Y') ?> HRWeb Inc. All rights reserved.</span>
        </div>
    </div>
</div>

</body>
</html>
