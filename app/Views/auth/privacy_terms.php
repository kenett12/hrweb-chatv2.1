<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Privacy & Terms | HRWeb Inc.</title>
    
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
<body class="bg-gray-50 flex flex-col min-h-screen">

<div class="flex flex-col items-center p-10 relative flex-1">
    <div class="w-full max-w-4xl bg-white p-10 rounded-2xl shadow-sm border border-gray-100">
        
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight leading-tight">
                    Privacy & Terms
                </h1>
                <div class="h-1 w-12 mt-2 rounded-full" style="background-color: var(--clr-yellow);"></div>
            </div>
            <a href="<?= base_url('login') ?>" class="text-sm font-semibold flex items-center gap-1 hover:underline transition-all" style="color: var(--clr-blue);">
                <span class="material-symbols-outlined notranslate text-[18px]">arrow_back</span>
                Back to Login
            </a>
        </div>

        <div class="prose prose-sm max-w-none text-gray-600 space-y-6">
            <section>
                <h2 class="text-lg font-bold text-gray-900 mb-2">1. Terms of Service</h2>
                <p>Welcome to HRWeb Inc. By accessing our portal, you agree to be bound by these Terms of Service, all applicable laws and regulations, and agree that you are responsible for compliance with any applicable local laws. If you do not agree with any of these terms, you are prohibited from using or accessing this site.</p>
            </section>
            
            <section>
                <h2 class="text-lg font-bold text-gray-900 mb-2">2. Use License</h2>
                <p>Permission is granted to temporarily download one copy of the materials (information or software) on HRWeb Inc.'s web site for personal, non-commercial transitory viewing only. This is the grant of a license, not a transfer of title, and under this license you may not:</p>
                <ul class="list-disc pl-5 mt-2 space-y-1">
                    <li>modify or copy the materials;</li>
                    <li>use the materials for any commercial purpose, or for any public display (commercial or non-commercial);</li>
                    <li>attempt to decompile or reverse engineer any software contained on HRWeb Inc.'s web site;</li>
                    <li>remove any copyright or other proprietary notations from the materials; or</li>
                    <li>transfer the materials to another person or "mirror" the materials on any other server.</li>
                </ul>
            </section>

            <section>
                <h2 class="text-lg font-bold text-gray-900 mb-2">3. Privacy Policy</h2>
                <p>Your privacy is important to us. It is HRWeb Inc.'s policy to respect your privacy regarding any information we may collect from you across our website, and other sites we own and operate.</p>
                <p class="mt-2">We only ask for personal information when we truly need it to provide a service to you. We collect it by fair and lawful means, with your knowledge and consent. We also let you know why we’re collecting it and how it will be used.</p>
            </section>

            <section>
                <h2 class="text-lg font-bold text-gray-900 mb-2">4. Data Storage and Security</h2>
                <p>We only retain collected information for as long as necessary to provide you with your requested service. What data we store, we’ll protect within commercially acceptable means to prevent loss and theft, as well as unauthorized access, disclosure, copying, use or modification.</p>
            </section>

            <section>
                <h2 class="text-lg font-bold text-gray-900 mb-2">5. User Consent</h2>
                <p>By using our website, you hereby consent to our Privacy Policy and agree to its Terms and Conditions.</p>
            </section>
        </div>

        <div class="mt-12 pt-8 border-t border-gray-100 flex flex-wrap gap-x-6 gap-y-2 text-[10px] font-bold text-gray-400 uppercase tracking-widest">
            <span>&copy; <?= date('Y') ?> HRWeb Inc. All rights reserved.</span>
        </div>
    </div>
</div>

</body>
</html>
