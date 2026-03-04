<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Privacy &amp; Terms | HRWeb Inc.</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; background: #f7f7f7; color: #32363a; min-height: 100vh; display: flex; flex-direction: column; align-items: center; padding: 40px 16px; }
        .material-symbols-outlined { font-family: 'Material Symbols Outlined' !important; font-variation-settings: 'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24; display: inline-block; white-space: nowrap; -webkit-font-smoothing: antialiased; }

        /* Shell top bar */
        .shell-bar { width: 100%; max-width: 860px; display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; }
        .shell-logo { display: flex; align-items: center; gap: 8px; text-decoration: none; }
        .shell-logo img { height: 28px; object-fit: contain; }
        .shell-logo span { font-size: 14px; font-weight: 600; color: #32363a; }
        .back-link { display: inline-flex; align-items: center; gap: 4px; font-size: 13px; font-weight: 500; color: #0070f2; text-decoration: none; }
        .back-link:hover { text-decoration: underline; }

        /* Card */
        .card { width: 100%; max-width: 860px; background: #fff; border: 1px solid #e0e0e0; border-radius: 4px; overflow: hidden; }
        .card-header { padding: 24px 32px 18px; border-bottom: 1px solid #e0e0e0; background: #fafafa; }
        .card-header h1 { font-size: 18px; font-weight: 600; color: #32363a; }
        .card-header p { font-size: 13px; color: #6a6d70; margin-top: 4px; }
        .card-body { padding: 28px 32px; }

        /* Sections */
        section { margin-bottom: 28px; }
        section:last-child { margin-bottom: 0; }
        section h2 { font-size: 14px; font-weight: 600; color: #32363a; margin-bottom: 8px; display: flex; align-items: center; gap: 8px; }
        section h2 .section-num { display: inline-flex; align-items: center; justify-content: center; width: 22px; height: 22px; border-radius: 2px; background: #0070f2; color: #fff; font-size: 11px; font-weight: 700; flex-shrink: 0; }
        section p { font-size: 13px; color: #6a6d70; line-height: 1.7; }
        section p + p { margin-top: 8px; }
        ul.policy-list { margin-top: 10px; padding-left: 0; list-style: none; display: flex; flex-direction: column; gap: 6px; }
        ul.policy-list li { font-size: 13px; color: #6a6d70; padding-left: 20px; position: relative; line-height: 1.6; }
        ul.policy-list li::before { content: '–'; position: absolute; left: 4px; color: #0070f2; }

        /* Divider */
        hr.section-divider { border: none; border-top: 1px solid #f0f0f0; margin: 28px 0; }

        /* Footer */
        .card-footer { padding: 14px 32px; border-top: 1px solid #e0e0e0; background: #fafafa; font-size: 11px; color: #89919a; }
    </style>
</head>
<body>

    <!-- Top bar -->
    <div class="shell-bar">
        <a href="<?= base_url() ?>" class="shell-logo">
            <img src="<?= base_url('assets/img/logo.png') ?>" alt="HRWeb Inc." onerror="this.style.display='none'">
            <span>HRWeb Inc.</span>
        </a>
        <a href="<?= base_url('login') ?>" class="back-link">
            <span class="material-symbols-outlined" style="font-size:16px;">arrow_back</span>
            Back to Sign In
        </a>
    </div>

    <!-- Card -->
    <div class="card">
        <div class="card-header">
            <h1>Privacy &amp; Terms</h1>
            <p>Last updated &bull; <?= date('F Y') ?> &bull; HRWeb Inc.</p>
        </div>
        <div class="card-body">

            <section>
                <h2><span class="section-num">1</span> Terms of Service</h2>
                <p>Welcome to HRWeb Inc. By accessing our portal, you agree to be bound by these Terms of Service, all applicable laws and regulations, and agree that you are responsible for compliance with any applicable local laws. If you do not agree with any of these terms, you are prohibited from using or accessing this site.</p>
            </section>

            <hr class="section-divider">

            <section>
                <h2><span class="section-num">2</span> Use License</h2>
                <p>Permission is granted to temporarily access the materials on HRWeb Inc.'s portal for personal, non-commercial use only. Under this license you may not:</p>
                <ul class="policy-list">
                    <li>Modify or copy the materials</li>
                    <li>Use the materials for any commercial purpose or public display</li>
                    <li>Attempt to decompile or reverse engineer any software on the portal</li>
                    <li>Remove any copyright or proprietary notations from the materials</li>
                    <li>Transfer the materials to another person or mirror on any other server</li>
                </ul>
            </section>

            <hr class="section-divider">

            <section>
                <h2><span class="section-num">3</span> Privacy Policy</h2>
                <p>Your privacy is important to us. It is HRWeb Inc.'s policy to respect your privacy regarding any information we may collect from you across our portal and other services we own and operate.</p>
                <p>We only ask for personal information when we truly need it to provide a service to you. We collect it by fair and lawful means, with your knowledge and consent, and we let you know why we're collecting it and how it will be used.</p>
            </section>

            <hr class="section-divider">

            <section>
                <h2><span class="section-num">4</span> Data Storage &amp; Security</h2>
                <p>We only retain collected information for as long as necessary to provide you with your requested service. What data we store, we protect within commercially acceptable means to prevent loss, theft, and unauthorized access, disclosure, copying, use, or modification.</p>
            </section>

            <hr class="section-divider">

            <section>
                <h2><span class="section-num">5</span> User Consent</h2>
                <p>By using our portal, you hereby consent to our Privacy Policy and agree to its Terms and Conditions. Continued use of the platform after changes to this policy constitutes acceptance of those changes.</p>
            </section>

        </div>
        <div class="card-footer">
            &copy; <?= date('Y') ?> HRWeb Inc. All rights reserved.
        </div>
    </div>

</body>
</html>
