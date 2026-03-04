<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Support | HRWeb Inc.</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; background: #f7f7f7; color: #32363a; min-height: 100vh; display: flex; flex-direction: column; align-items: center; padding: 40px 16px; }
        .material-symbols-outlined { font-family: 'Material Symbols Outlined' !important; font-variation-settings: 'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24; display: inline-block; white-space: nowrap; -webkit-font-smoothing: antialiased; }

        /* Shell top bar */
        .shell-bar { width: 100%; max-width: 600px; display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; }
        .shell-logo { display: flex; align-items: center; gap: 8px; text-decoration: none; }
        .shell-logo img { height: 28px; object-fit: contain; }
        .shell-logo span { font-size: 14px; font-weight: 600; color: #32363a; }
        .back-link { display: inline-flex; align-items: center; gap: 4px; font-size: 13px; font-weight: 500; color: #0070f2; text-decoration: none; }
        .back-link:hover { text-decoration: underline; }

        /* Card */
        .card { width: 100%; max-width: 600px; background: #fff; border: 1px solid #e0e0e0; border-radius: 4px; overflow: hidden; }
        .card-header { padding: 24px 32px 18px; border-bottom: 1px solid #e0e0e0; background: #fafafa; }
        .card-header h1 { font-size: 18px; font-weight: 600; color: #32363a; }
        .card-header p { font-size: 13px; color: #6a6d70; margin-top: 4px; }
        .card-body { padding: 8px 0; }

        /* Contact rows */
        .contact-row { display: flex; align-items: flex-start; gap: 16px; padding: 22px 32px; border-bottom: 1px solid #f0f0f0; }
        .contact-row:last-child { border-bottom: none; }
        .contact-icon { width: 40px; height: 40px; border-radius: 4px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .contact-icon.blue  { background: #e8f1fc; color: #0070f2; }
        .contact-icon.green { background: #f0fdf4; color: #107e3e; }
        .contact-icon.amber { background: #fff8e5; color: #e9730c; }
        .contact-info h2 { font-size: 13px; font-weight: 600; color: #32363a; margin-bottom: 4px; }
        .contact-info p { font-size: 13px; color: #6a6d70; line-height: 1.6; }
        .contact-info a { color: #0070f2; text-decoration: none; }
        .contact-info a:hover { text-decoration: underline; }

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
            <h1>Contact Support</h1>
            <p>Reach out to our team for technical or account assistance.</p>
        </div>
        <div class="card-body">

            <div class="contact-row">
                <div class="contact-icon green">
                    <span class="material-symbols-outlined" style="font-size:20px;">location_on</span>
                </div>
                <div class="contact-info">
                    <h2>Office Location</h2>
                    <p>
                        SertTech Bldg. B2 L1 Birmingham Plains,<br>
                        Brgy. Bacao 1, Gen. Trias, Cavite 4107<br>
                        Philippines
                    </p>
                </div>
            </div>

            <div class="contact-row">
                <div class="contact-icon blue">
                    <span class="material-symbols-outlined" style="font-size:20px;">mail</span>
                </div>
                <div class="contact-info">
                    <h2>Email Support</h2>
                    <p><a href="mailto:support@hrweb.ph">support@hrweb.ph</a></p>
                    <p>We respond to all inquiries within 1 business day.</p>
                </div>
            </div>

            <div class="contact-row">
                <div class="contact-icon amber">
                    <span class="material-symbols-outlined" style="font-size:20px;">phone_in_talk</span>
                </div>
                <div class="contact-info">
                    <h2>Phone</h2>
                    <p>[Placeholder Phone Number]</p>
                    <p>Available Monday – Friday, 8:00 AM – 5:00 PM PHT</p>
                </div>
            </div>

        </div>
        <div class="card-footer">
            &copy; <?= date('Y') ?> HRWeb Inc. All rights reserved.
        </div>
    </div>

</body>
</html>
