<footer class="px-8 py-4 border-t border-gray-100 bg-white/50 backdrop-blur-sm">
    <div class="flex flex-col md:flex-row justify-between items-center gap-4">
        <div class="footer-left">
            <p class="text-xs font-medium text-gray-400">
                &copy; <?= date('Y') ?> <span class="text-gray-600">HRWeb Inc.</span> • Internal Management Portal
            </p>
        </div>

        <div class="flex items-center gap-6 text-xs font-semibold text-gray-400">
            <a href="<?= base_url('privacy') ?>" class="hover:text-gray-600 transition-colors">Privacy</a>
            <a href="<?= base_url('terms') ?>" class="hover:text-gray-600 transition-colors">Terms</a>
            <div class="flex items-center gap-2 px-3 py-1 bg-emerald-50 text-emerald-600 rounded-full">
                <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span>
                System Operational
            </div>
        </div>
    </div>
</footer>