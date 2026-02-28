<?= $this->extend('layouts/main_layout') ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" />
<style>
    .dashboard-swiper {
        width: 100%;
        height: 380px;
        border-radius: 2rem;
        overflow: hidden;
        box-shadow: 0 10px 30px -10px rgba(0,0,0,0.1);
    }
    .swiper-slide {
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
        padding: 3rem;
        background-size: cover;
        background-position: center;
        position: relative;
    }
    .swiper-slide::before {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(to top, rgba(15, 23, 42, 0.95) 0%, rgba(15, 23, 42, 0.4) 50%, rgba(15, 23, 42, 0.1) 100%);
        z-index: 1;
    }
    .slide-content {
        position: relative;
        z-index: 2;
        color: white;
        max-width: 800px;
    }
    .swiper-pagination-bullet {
        background: white;
        opacity: 0.5;
    }
    .swiper-pagination-bullet-active {
        opacity: 1;
        background: var(--clr-blue);
        width: 24px;
        border-radius: 4px;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="max-w-7xl mx-auto">
    <div class="mb-10 flex flex-col md:flex-row md:items-center md:justify-between gap-6">
        <div>
            <div class="inline-flex items-center gap-3 px-5 py-2.5 bg-white border border-gray-200 shadow-sm rounded-2xl mb-3">
                <div class="w-8 h-8 rounded-xl bg-blue-50 flex items-center justify-center text-[#1e72af]">
                    <i class="fas fa-building text-sm"></i>
                </div>
                <h1 class="text-xl font-black text-gray-900 tracking-tight uppercase"><?= $company_name ?></h1>
            </div>
            <p class="text-gray-500 font-medium">Manage your corporate support inquiries and chat history.</p>
        </div>

        <div class="bg-white border border-gray-100 px-6 py-4 rounded-3xl flex items-center space-x-4 shadow-sm">
            <div class="h-12 w-12 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-blue-100"
                style="background-color: var(--clr-blue);">
                <i class="fas fa-id-card-alt text-xl"></i>
            </div>
            <div class="pr-4">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Primary HR Contact</p>
                <p class="text-base font-black text-gray-900"><?= $hr_contact ?></p>
            </div>
        </div>
    </div>

    <!-- News Carousel Section -->
    <div class="swiper dashboard-swiper mb-12">
        <div class="swiper-wrapper">
            <!-- Slide 1 -->
            <div class="swiper-slide" style="background-image: url('https://images.unsplash.com/photo-1522071820081-009f0129c71c?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80');">
                <div class="slide-content">
                    <span class="inline-block px-3 py-1 bg-blue-500 text-white text-[10px] font-bold uppercase tracking-widest rounded-full mb-4">Company Announcement</span>
                    <h2 class="text-3xl md:text-4xl font-black mb-2 tracking-tight">Annual Corporate Retreat 2026</h2>
                    <p class="text-slate-200 font-medium max-w-2xl leading-relaxed">Join us this coming November as we head to the mountains for our yearly strategy and team-building summit. Registration details will be sent to your HR representatives soon.</p>
                </div>
            </div>
            
            <!-- Slide 2 -->
            <div class="swiper-slide" style="background-image: url('https://images.unsplash.com/photo-1576091160550-2173dba999ef?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80');">
                <div class="slide-content">
                    <span class="inline-block px-3 py-1 bg-emerald-500 text-white text-[10px] font-bold uppercase tracking-widest rounded-full mb-4">HR & Benefits</span>
                    <h2 class="text-3xl md:text-4xl font-black mb-2 tracking-tight">Upgraded Healthcare Packages</h2>
                    <p class="text-slate-200 font-medium max-w-2xl leading-relaxed">We have partnered with leading medical providers to expand your vision and dental coverage. Review the new policy documents in the knowledge base.</p>
                </div>
            </div>

            <!-- Slide 3 -->
            <div class="swiper-slide" style="background-image: url('https://images.unsplash.com/photo-1551288049-bebda4e38f71?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80');">
                <div class="slide-content">
                    <span class="inline-block px-3 py-1 bg-amber-500 text-white text-[10px] font-bold uppercase tracking-widest rounded-full mb-4">System Notice</span>
                    <h2 class="text-3xl md:text-4xl font-black mb-2 tracking-tight">Scheduled Portal Maintenance</h2>
                    <p class="text-slate-200 font-medium max-w-2xl leading-relaxed">The HRWeb portal and live ticketing systems will undergo scheduled maintenance this Sunday from 2 AM to 4 AM EST. Please plan accordingly.</p>
                </div>
            </div>
        </div>
        <!-- Add Pagination -->
        <div class="swiper-pagination"></div>
        <!-- Add Navigation -->
        <div class="swiper-button-prev !text-white opacity-50 hover:opacity-100 transition-opacity"></div>
        <div class="swiper-button-next !text-white opacity-50 hover:opacity-100 transition-opacity"></div>
    </div>

    <!-- Live Analytics Engine (Light Theme) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-12">
        
        <!-- Visitors Card -->
        <div class="bg-white rounded-[2rem] p-6 border border-gray-100 shadow-sm hover:border-blue-100 transition-colors">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-8 h-8 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600">
                    <i class="fas fa-users-viewfinder text-sm"></i>
                </div>
                <h4 class="text-gray-900 font-bold text-[15px]">Visitors</h4>
            </div>
            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mb-1">Today</p>
            <div class="flex items-baseline gap-3">
                <span class="text-4xl font-black text-gray-900"><?= $visitors_today ?? 0 ?></span>
                <span class="flex items-center gap-1 text-[13px] font-bold <?= ($visitors_trend ?? 0) >= 0 ? 'text-emerald-500' : 'text-rose-500' ?>">
                    <i class="fas fa-arrow-<?= ($visitors_trend ?? 0) >= 0 ? 'up' : 'down' ?> text-[10px]"></i>
                    <?= abs(round($visitors_trend ?? 0, 1)) ?>%
                </span>
            </div>
            <div class="mt-6 pt-4 border-t border-gray-50 flex items-center justify-between">
                <span class="text-[11px] text-gray-400 font-bold uppercase tracking-widest">Last 7 days</span>
                <div class="flex items-center gap-2">
                    <span class="text-emerald-500 text-xs font-bold"><i class="fas fa-arrow-up text-[10px]"></i> <?= round($visitors_last_7 ?? 0, 1) ?></span>
                </div>
            </div>
        </div>

        <!-- Chats Card -->
        <div class="bg-white rounded-[2rem] p-6 border border-gray-100 shadow-sm hover:border-blue-100 transition-colors">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-8 h-8 rounded-xl bg-pink-50 flex items-center justify-center text-pink-500">
                    <i class="fas fa-comment-dots text-sm"></i>
                </div>
                <h4 class="text-gray-900 font-bold text-[15px]">Chats</h4>
            </div>
            <div class="flex justify-between items-start mb-2">
                <div>
                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mb-1">Answered</p>
                    <span class="text-4xl font-black text-gray-900"><?= $chats_today ?? 0 ?></span>
                </div>
                <div class="text-right">
                    <p class="text-[10px] text-gray-300 font-bold uppercase tracking-widest mb-1">Missed</p>
                    <span class="text-2xl font-black text-gray-200">0</span>
                </div>
            </div>
            <div class="mt-6 pt-4 border-t border-gray-50 flex items-center justify-between">
                <span class="text-[11px] text-gray-400 font-bold uppercase tracking-widest">Last 7 days</span>
                <div class="flex items-center gap-2 text-gray-400 text-xs font-bold">
                    <span class="text-emerald-500"><i class="fas fa-arrow-up text-[10px]"></i> <?= round($chats_last_7 ?? 0, 1) ?></span>
                    <span class="text-gray-300"><i class="fas fa-arrow-down text-[10px]"></i> 0</span>
                </div>
            </div>
        </div>

        <!-- Page Views Card -->
        <div class="bg-white rounded-[2rem] p-6 border border-gray-100 shadow-sm hover:border-blue-100 transition-colors">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-8 h-8 rounded-xl bg-orange-50 flex items-center justify-center text-orange-500">
                    <i class="fas fa-eye text-sm"></i>
                </div>
                <h4 class="text-gray-900 font-bold text-[15px]">Page Views</h4>
            </div>
            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mb-1">Today</p>
            <div class="flex items-baseline gap-3">
                <span class="text-4xl font-black text-gray-900"><?= $views_today ?? 0 ?></span>
                <span class="flex items-center gap-1 text-[13px] font-bold <?= ($views_trend ?? 0) >= 0 ? 'text-emerald-500' : 'text-rose-500' ?>">
                    <i class="fas fa-arrow-<?= ($views_trend ?? 0) >= 0 ? 'up' : 'down' ?> text-[10px]"></i>
                    <?= abs(round($views_trend ?? 0, 1)) ?>%
                </span>
            </div>
            <div class="mt-6 pt-4 border-t border-gray-50 flex items-center justify-between">
                <span class="text-[11px] text-gray-400 font-bold uppercase tracking-widest">Last 7 days</span>
                <div class="flex items-center gap-2">
                    <span class="text-emerald-500 text-xs font-bold"><i class="fas fa-arrow-up text-[10px]"></i> <?= round($views_last_7 ?? 0, 1) ?></span>
                </div>
            </div>
        </div>

        <!-- Reporting Card -->
        <div class="bg-white rounded-[2rem] p-6 border border-gray-100 shadow-sm hover:border-blue-100 transition-colors relative overflow-hidden">
            <div class="flex items-center justify-between mb-8">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-sky-50 flex items-center justify-center text-sky-500">
                        <i class="fas fa-chart-simple text-sm"></i>
                    </div>
                    <h4 class="text-gray-900 font-bold text-[15px]">Reporting</h4>
                </div>
                <a href="#" class="text-[10px] font-bold text-emerald-600 uppercase tracking-widest hover:underline">More</a>
            </div>
            
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-xs text-gray-500 font-medium">Positive Sentiment</span>
                    <span class="text-xs font-bold <?= ($sentiment ?? 0) > 50 ? 'text-emerald-600' : 'text-rose-600' ?>"><?= $sentiment ?? 0 ?>%</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-xs text-gray-400 font-medium opacity-60">Engagement</span>
                    <span class="text-xs font-bold text-rose-400 opacity-40">0.0%</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-xs text-gray-400 font-medium opacity-60">Availability</span>
                    <span class="text-xs font-bold text-emerald-500">100%</span>
                </div>
            </div>
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

        <?php if (!empty($recent_tickets)): ?>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <tbody class="divide-y divide-gray-50">
                        <?php foreach ($recent_tickets as $ticket): ?>
                            <tr class="hover:bg-gray-50/30 transition-colors group cursor-pointer" onclick="window.location.href='<?= base_url('client/tickets/view/' . $ticket['id']) ?>'">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 rounded-xl flex items-center justify-center transition-colors shadow-sm"
                                            style="background-color: <?= $ticket['status'] === 'Resolved' ? 'rgba(32, 174, 92, 0.1)' : ($ticket['status'] === 'In Progress' ? 'rgba(255, 195, 56, 0.1)' : 'rgba(30, 114, 175, 0.1)') ?>; color: <?= $ticket['status'] === 'Resolved' ? 'var(--clr-green)' : ($ticket['status'] === 'In Progress' ? 'var(--clr-yellow)' : 'var(--clr-blue)') ?>;">
                                            <i class="fas <?= $ticket['status'] === 'Resolved' ? 'fa-check-circle' : ($ticket['status'] === 'In Progress' ? 'fa-spinner fa-spin' : 'fa-ticket-alt') ?> text-lg"></i>
                                        </div>
                                        <div>
                                            <p class="font-bold text-gray-900 text-base group-hover:text-[#3297ca] transition-colors"><?= esc(strlen($ticket['subject']) > 50 ? substr($ticket['subject'], 0, 50) . '...' : $ticket['subject']) ?></p>
                                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mt-1"><?= esc($ticket['ticket_number']) ?> • <?= date('M d, Y', strtotime($ticket['updated_at'])) ?></p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-[10px] font-bold uppercase tracking-widest border"
                                        style="background-color: <?= $ticket['status'] === 'Resolved' ? '#f0fdf4' : ($ticket['status'] === 'In Progress' ? '#fefce8' : '#eff6ff') ?>; border-color: <?= $ticket['status'] === 'Resolved' ? '#bbf7d0' : ($ticket['status'] === 'In Progress' ? '#fef08a' : '#bfdbfe') ?>; color: <?= $ticket['status'] === 'Resolved' ? '#166534' : ($ticket['status'] === 'In Progress' ? '#854d0e' : '#1e40af') ?>;">
                                        <?= esc($ticket['status']) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="px-8 py-4 border-t border-gray-50 flex justify-center bg-gray-50/30">
                <a href="<?= base_url('client/tickets') ?>" class="text-[11px] font-bold text-gray-500 hover:text-[#3297ca] uppercase tracking-widest flex items-center gap-2 transition-colors">
                    View All History <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        <?php else: ?>
            <div class="p-24 text-center">
                <div class="w-20 h-20 mx-auto rounded-3xl flex items-center justify-center mb-6"
                    style="background-color: rgba(50, 151, 202, 0.05); color: var(--clr-cyan);">
                    <i class="fas fa-history text-3xl"></i>
                </div>
                <p class="text-gray-400 text-sm italic font-medium">No recent chat sessions or support tickets found for
                    your organization.</p>
            </div>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.socket.io/4.7.2/socket.io.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
<script>
    // Initialize Swiper
    const swiper = new Swiper('.dashboard-swiper', {
        loop: true,
        autoplay: {
            delay: 5000,
            disableOnInteraction: false,
        },
        pagination: {
            el: '.swiper-pagination',
            clickable: true,
        },
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
        effect: 'fade',
        fadeEffect: {
            crossFade: true
        }
    });

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