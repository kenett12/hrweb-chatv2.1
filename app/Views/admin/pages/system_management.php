<?= $this->extend('layouts/main_layout') ?>

<?= $this->section('content') ?>
<div class="max-w-7xl mx-auto">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">System Settings</h1>
            <div class="h-1.5 w-12 rounded-full mt-2" style="background-color: var(--clr-purple);"></div>
            <p class="text-gray-500 mt-3 font-medium">Manage global configuration and required system targets.</p>
        </div>
    </div>

    <!-- Alert Messages (Handled Globally in main_layout, but keeping space open) -->

    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden p-8 max-w-2xl">
        <h2 class="text-xl font-bold text-gray-900 mb-6">TSR Performance KPI</h2>
        
        <form action="<?= base_url('superadmin/system-management/update') ?>" method="POST">
            <?= csrf_field() ?>
            <input type="hidden" name="setting_key" value="min_tsr_leads">
            
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Minimum Required Lead Assignments</label>
                    <p class="text-xs text-gray-500 mb-3">This target is used to calculate the % Utilization for each TSR. The default is 10 lead assignments per TSR.</p>
                    
                    <div class="flex items-center gap-4">
                        <input type="number" name="setting_value" id="min_tsr_leads" value="<?= esc($settings['min_tsr_leads'] ?? 10) ?>" 
                            class="w-32 px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-purple-500/10 focus:border-purple-500 outline-none transition-all text-gray-900 font-bold text-center text-lg" 
                            min="1" required>
                        <span class="text-gray-500 font-medium">Leads</span>
                    </div>
                </div>
            </div>

            <div class="mt-8 pt-6 border-t border-gray-100 flex justify-end">
                <button type="submit" class="btn py-3 px-6 shadow-md" style="background-color: var(--clr-purple); color: white;">
                    <span class="material-symbols-outlined text-[18px]">save</span>
                    Save Settings
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    /* Add temporary custom color mapping for this page */
    :root {
        --clr-purple: #8b5cf6;
    }
</style>
<?= $this->endSection() ?>
