<?= $this->extend('layouts/main_layout') ?>

<?= $this->section('content') ?>
<!-- SAP Fiori Page Header -->
<div class="fiori-page-header">
    <div>
        <h1 class="fiori-page-title">System Settings</h1>
        <p class="fiori-page-subtitle">Configure global system parameters and KPI performance targets</p>
    </div>
</div>

<div style="max-width:640px;">
    <!-- KPI Settings Card -->
    <div class="fiori-card mb-4">
        <div class="fiori-card__header">
            <div>
                <h2 class="fiori-card__title">TSR Performance KPI</h2>
                <p class="fiori-card__subtitle">Minimum lead assignment threshold used to calculate TSR utilization</p>
            </div>
            <span class="material-symbols-outlined text-[20px]" style="color:var(--fiori-text-muted);">tune</span>
        </div>
        <form action="<?= base_url('superadmin/system-management/update') ?>" method="POST" class="fiori-card__content">
            <?= csrf_field() ?>
            <input type="hidden" name="setting_key" value="min_tsr_leads">

            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider mb-2" style="color:var(--fiori-text-secondary);">Minimum Required Lead Assignments</label>
                    <div class="flex items-center gap-3">
                        <input type="number" name="setting_value" id="min_tsr_leads"
                            value="<?= esc($settings['min_tsr_leads'] ?? 10) ?>"
                            class="fiori-input text-center font-semibold text-base"
                            style="width:90px;"
                            min="1" required>
                        <span class="text-sm" style="color:var(--fiori-text-secondary);">lead assignments per TSR</span>
                    </div>
                </div>

                <!-- Info box -->
                <div class="flex items-start gap-3 p-3 rounded" style="background:var(--fiori-blue-light); border:1px solid #b3d4fb; border-radius:4px;">
                    <span class="material-symbols-outlined text-[18px] flex-none mt-0.5" style="color:var(--fiori-blue);">info</span>
                    <p class="text-xs" style="color:var(--fiori-blue);">
                        Utilization % = (Lead Assignments ÷ This Value) × 100. The default value is 10.
                    </p>
                </div>

                <div class="flex justify-end pt-2">
                    <button type="submit" class="btn btn-accent">
                        <span class="material-symbols-outlined text-[16px]">save</span>
                        Save Settings
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>
