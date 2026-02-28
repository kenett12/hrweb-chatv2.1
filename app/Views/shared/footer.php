<footer class="px-8 py-4 border-t border-gray-100 bg-white/50 backdrop-blur-sm">
    <div class="flex flex-col md:flex-row justify-between items-center gap-4">
        <div class="footer-left">
            <p class="text-xs font-medium text-gray-400">
                &copy; <?= date('Y') ?> <span class="text-gray-600">HRWeb Inc.</span> • Chat Support System
            </p>
        </div>

        <div class="flex items-center gap-6 text-xs font-semibold text-gray-400">
            <a href="<?= base_url('privacy') ?>" class="hover:text-gray-600 transition-colors">Privacy</a>
            <a href="<?= base_url('terms') ?>" class="hover:text-gray-600 transition-colors">Terms</a>
        </div>
    </div>
</footer>