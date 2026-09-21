import './bootstrap';

document.addEventListener('DOMContentLoaded', () => {
    // ==========================================
    // 1. Mobile Navbar Toggle
    // ==========================================
    const mobileMenuBtn = document.getElementById('mobile-menu-btn');
    const mobileMenu = document.getElementById('mobile-menu');
    const hamburgerIcon = document.getElementById('hamburger-icon');
    const closeIcon = document.getElementById('close-icon');

    if (mobileMenuBtn && mobileMenu) {
        mobileMenuBtn.addEventListener('click', () => {
            const isExpanded = mobileMenuBtn.getAttribute('aria-expanded') === 'true';
            mobileMenuBtn.setAttribute('aria-expanded', !isExpanded);
            mobileMenu.classList.toggle('hidden');
            hamburgerIcon?.classList.toggle('hidden');
            closeIcon?.classList.toggle('hidden');
        });

        // Close mobile menu when clicking any nav link
        document.querySelectorAll('.mobile-nav-link').forEach((link) => {
            link.addEventListener('click', () => {
                mobileMenu.classList.add('hidden');
                mobileMenuBtn.setAttribute('aria-expanded', 'false');
                hamburgerIcon?.classList.remove('hidden');
                closeIcon?.classList.add('hidden');
            });
        });
    }

    // ==========================================
    // 2. Smooth Scrolling with Sticky Navbar Offset
    // ==========================================
    document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
        anchor.addEventListener('click', function (e) {
            const targetId = this.getAttribute('href');
            if (!targetId || targetId === '#') return;

            const targetEl = document.querySelector(targetId);
            if (targetEl) {
                e.preventDefault();
                const navHeight = 72; // Header height offset
                const targetPosition = targetEl.getBoundingClientRect().top + window.pageYOffset - navHeight;
                window.scrollTo({
                    top: targetPosition,
                    behavior: 'smooth'
                });
            }
        });
    });

    // ==========================================
    // 3. Search Filter for Kecamatan Cards
    // ==========================================
    const searchInput = document.getElementById('cari-kecamatan') || document.querySelector('.cari-nama-kecamatan');
    const kecamatanCards = document.querySelectorAll('.kecamatan-card');
    const noResultsEl = document.getElementById('no-results');

    if (searchInput && kecamatanCards.length > 0) {
        searchInput.addEventListener('input', (e) => {
            const query = e.target.value.trim().toLowerCase();
            let visibleCount = 0;

            kecamatanCards.forEach((card) => {
                const name = card.getAttribute('data-name') || card.textContent.toLowerCase();
                if (name.includes(query)) {
                    card.classList.remove('hidden');
                    visibleCount++;
                } else {
                    card.classList.add('hidden');
                }
            });

            if (noResultsEl) {
                if (visibleCount === 0 && query !== '') {
                    noResultsEl.classList.remove('hidden');
                } else {
                    noResultsEl.classList.add('hidden');
                }
            }
        });
    }

    // ==========================================
    // 4. Ranking Tab Switcher (Officers vs Reporters)
    // ==========================================
    const tabOfficersBtn = document.getElementById('tab-officers-btn');
    const tabReportersBtn = document.getElementById('tab-reporters-btn');
    const tabOfficersContent = document.getElementById('tab-officers-content');
    const tabReportersContent = document.getElementById('tab-reporters-content');

    const activeClasses = ['bg-[#5b7e3c]', 'text-white', 'shadow-xs'];
    const inactiveClasses = ['text-stone-600', 'hover:text-stone-900', 'hover:bg-stone-50'];

    if (tabOfficersBtn && tabReportersBtn && tabOfficersContent && tabReportersContent) {
        tabOfficersBtn.addEventListener('click', () => {
            tabOfficersBtn.classList.add(...activeClasses);
            tabOfficersBtn.classList.remove(...inactiveClasses);

            tabReportersBtn.classList.remove(...activeClasses);
            tabReportersBtn.classList.add(...inactiveClasses);

            tabOfficersContent.classList.remove('hidden');
            tabReportersContent.classList.add('hidden');
        });

        tabReportersBtn.addEventListener('click', () => {
            tabReportersBtn.classList.add(...activeClasses);
            tabReportersBtn.classList.remove(...inactiveClasses);

            tabOfficersBtn.classList.remove(...activeClasses);
            tabOfficersBtn.classList.add(...inactiveClasses);

            tabReportersContent.classList.remove('hidden');
            tabOfficersContent.classList.add('hidden');
        });
    }

    // ==========================================
    // 5. Interactive Heatmap District Selector
    // ==========================================
    const heatmapButtons = document.querySelectorAll('.heatmap-btn');
    const detailName = document.getElementById('detail-name');
    const detailStatus = document.getElementById('detail-status');
    const detailBadge = document.getElementById('detail-badge');
    const detailTotal = document.getElementById('detail-total');
    const detailSelesai = document.getElementById('detail-selesai');
    const detailAktif = document.getElementById('detail-aktif');

    const updateHeatmapDetail = (btn) => {
        const name = btn.getAttribute('data-name');
        const status = btn.getAttribute('data-status');
        const total = btn.getAttribute('data-total');
        const selesai = btn.getAttribute('data-selesai');
        const aktif = btn.getAttribute('data-aktif');

        if (detailName) detailName.textContent = name;
        if (detailStatus) detailStatus.textContent = `Status: ${status}`;
        if (detailTotal) detailTotal.textContent = total;
        if (detailSelesai) detailSelesai.textContent = selesai;
        if (detailAktif) detailAktif.textContent = aktif;

        if (detailBadge) {
            // Reset badge colors
            detailBadge.className = 'inline-flex items-center gap-1.5 mt-1 px-2.5 py-0.5 rounded-full text-xs font-bold';
            const dot = detailBadge.querySelector('span');

            if (status === 'Aktif') {
                detailBadge.classList.add('bg-rose-100', 'text-rose-800');
                if (dot) dot.className = 'w-1.5 h-1.5 rounded-full bg-rose-500';
            } else {
                detailBadge.classList.add('bg-emerald-100', 'text-emerald-800');
                if (dot) dot.className = 'w-1.5 h-1.5 rounded-full bg-emerald-500';
            }
        }
    };

    heatmapButtons.forEach((btn) => {
        btn.addEventListener('click', () => updateHeatmapDetail(btn));
        btn.addEventListener('mouseenter', () => updateHeatmapDetail(btn));
    });
});
