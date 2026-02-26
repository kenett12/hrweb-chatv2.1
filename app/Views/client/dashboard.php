<?= $this->extend('layouts/main_layout') ?>

<?= $this->section('content') ?>
<div class="max-w-7xl mx-auto">
    <div class="mb-10 flex flex-col md:flex-row md:items-center md:justify-between gap-6">
        <div>
            <h1 class="text-4xl font-black text-gray-900 tracking-tight italic"><?= $company_name ?></h1>
            <div class="h-1.5 w-16 rounded-full mt-2" style="background-color: var(--clr-blue);"></div>
            <p class="text-gray-500 mt-4 font-medium">Manage your corporate support inquiries and chat history.</p>
        </div>

        <div class="bg-white border border-gray-100 px-6 py-4 rounded-3xl flex items-center space-x-4 shadow-sm">
            <div class="h-12 w-12 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-blue-100"
                style="background-color: var(--clr-blue);">
                <i class="fas fa-id-card-alt text-xl"></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Primary HR Contact</p>
                <p class="text-base font-black text-gray-900"><?= $hr_contact ?></p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">

        <div
            class="bg-white rounded-[2rem] border-2 border-dashed border-gray-100 p-8 flex flex-col justify-center items-center text-center opacity-80">
            <div class="p-5 rounded-full mb-6 transition-transform hover:rotate-45 duration-700"
                style="background-color: rgba(255, 195, 56, 0.1); color: var(--clr-yellow);">
                <i class="fas fa-cog text-4xl"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-400">Account Settings</h3>
            <span class="mt-2 px-4 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest"
                style="background-color: rgba(255, 195, 56, 0.1); color: #b48a1d;">
                Coming Soon
            </span>
        </div>

    </div>

    <div class="mt-12 bg-white rounded-[2rem] border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-8 py-6 border-b border-gray-50 flex justify-between items-center"
            style="background-color: rgba(50, 151, 202, 0.02);">
            <div class="flex items-center gap-3">
                <div class="w-2 h-2 rounded-full" style="background-color: var(--clr-cyan);"></div>
                <h4 class="font-bold text-gray-900 uppercase text-xs tracking-widest">Recent Support History</h4>
            </div>
        </div>

        <div class="p-24 text-center">
            <div class="w-20 h-20 mx-auto rounded-3xl flex items-center justify-center mb-6"
                style="background-color: rgba(50, 151, 202, 0.05); color: var(--clr-cyan);">
                <i class="fas fa-history text-3xl"></i>
            </div>
            <p class="text-gray-400 text-sm italic font-medium">No recent chat sessions or support tickets found for
                your organization.</p>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.socket.io/4.7.2/socket.io.min.js"></script>
<script>
    const socket = io('http://localhost:3001');
    socket.on('global_ticket_change', (data) => {
        fetch(window.location.href)
            .then(res => res.text())
            .then(html => {
                const doc = new DOMParser().parseFromString(html, 'text/html');
                const newContent = doc.querySelector('.fade-in');
                if (newContent) {
                    document.querySelector('.fade-in').innerHTML = newContent.innerHTML;
                }
            });
    });
    console.log("Colorful Client Dashboard Real-Time Sync Active (Seamless).");
</script>
<?= $this->endSection() ?>