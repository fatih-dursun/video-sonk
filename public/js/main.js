// Sidebar Toggle (Sadece Mobilde)
const menuToggle = document.getElementById('menuToggle');
const sidebar = document.getElementById('sidebar');

if (menuToggle && sidebar) {
    const overlay = document.createElement('div');
    overlay.className = 'sidebar-overlay';
    overlay.id = 'sidebarOverlay';
    document.body.appendChild(overlay);

    menuToggle.addEventListener('click', () => {
        if (window.innerWidth <= 768) {
            sidebar.classList.toggle('active');
            overlay.classList.toggle('active');
        }
    });

    overlay.addEventListener('click', () => {
        sidebar.classList.remove('active');
        overlay.classList.remove('active');
    });

    const navItems = sidebar.querySelectorAll('.nav-item');
    navItems.forEach(item => {
        item.addEventListener('click', () => {
            if (window.innerWidth <= 768) {
                sidebar.classList.remove('active');
                overlay.classList.remove('active');
            }
        });
    });
}

// Search System
const searchInput = document.getElementById('searchInput');
const searchResults = document.getElementById('searchResults');
const searchClear = document.getElementById('searchClear');
const searchBtn = document.getElementById('searchBtn');
let searchTimeout;

if (searchInput && searchResults && searchClear && searchBtn) {
    
    // Input değişiminde
    searchInput.addEventListener('input', (e) => {
        const query = e.target.value.trim();
        
        // Clear button toggle
        if (query.length > 0) {
            searchClear.classList.add('active');
        } else {
            searchClear.classList.remove('active');
        }
        
        clearTimeout(searchTimeout);
        
        if (query.length < 2) {
            searchResults.classList.remove('active');
            searchResults.innerHTML = '';
            return;
        }
        
        // Debounce: 300ms
        searchTimeout = setTimeout(() => {
            performSearch(query);
        }, 300);
    });

    // Clear button
    searchClear.addEventListener('click', () => {
        searchInput.value = '';
        searchClear.classList.remove('active');
        searchResults.classList.remove('active');
        searchResults.innerHTML = '';
        searchInput.focus();
    });

    // Search button
    searchBtn.addEventListener('click', () => {
        performFullSearch();
    });

    // Enter tuşu
    searchInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            performFullSearch();
        }
    });

    // Dışarı tıklayınca kapat
    document.addEventListener('click', (e) => {
        if (!searchInput.contains(e.target) && 
            !searchResults.contains(e.target) && 
            !searchBtn.contains(e.target) && 
            !searchClear.contains(e.target)) {
            searchResults.classList.remove('active');
        }
    });
}

function performFullSearch() {
    const query = searchInput.value.trim();
    if (query.length >= 2) {
        const basePath = typeof BASE_PATH !== 'undefined' ? BASE_PATH : '/video-portal/public';
        window.location.href = `${basePath}/arama?q=${encodeURIComponent(query)}`;
    }
}

async function performSearch(query) {
    try {
        const basePath = typeof BASE_PATH !== 'undefined' ? BASE_PATH : '/video-portal/public';
        const response = await fetch(`${basePath}/api/search?q=${encodeURIComponent(query)}`);
        const data = await response.json();
        
        displaySearchResults(data.results);
    } catch (error) {
        console.error('Arama hatası:', error);
        searchResults.innerHTML = '<div style="padding: 20px; text-align: center; color: #dc3545;">Arama yapılırken hata oluştu</div>';
        searchResults.classList.add('active');
    }
}

function displaySearchResults(results) {
    if (results.length === 0) {
        searchResults.innerHTML = '<div style="padding: 20px; text-align: center; color: #888;">Sonuç bulunamadı</div>';
        searchResults.classList.add('active');
        return;
    }

    const basePath = typeof BASE_PATH !== 'undefined' ? BASE_PATH : '/video-portal/public';

    const html = results.map(video => {
        // Logo HTML'i (varsa)
        const logoHtml = video.category_logo 
            ? `<img src="${video.category_logo}" class="search-result-logo" alt="">` 
            : '';
        
        return `
        <a href="${basePath}/video/${video.slug}" class="search-result-item">
            <div class="search-result-thumbnail-wrapper">
                <img src="${basePath}${video.featured_image_path}" alt="${escapeHtml(video.title)}" class="search-result-thumbnail">
                ${logoHtml}
            </div>
            <div class="search-result-info">
                <div class="search-result-title">${escapeHtml(video.title)}</div>
                <div class="search-result-meta">
                    <span class="category-badge" style="background-color: ${video.background_color}; color: ${video.text_color}; margin-right: 8px;">
                        ${escapeHtml(video.category_name)}
                    </span>
                    ${formatNumber(video.view_count)} görüntülenme
                </div>
            </div>
        </a>
    `}).join('');

    searchResults.innerHTML = html;
    searchResults.classList.add('active');
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function formatNumber(num) {
    return new Intl.NumberFormat('tr-TR').format(num);
}

// Admin: Color Preset Selection
const colorPresets = document.querySelectorAll('.color-preset');
const bgColorInput = document.getElementById('background_color');
const textColorInput = document.getElementById('text_color');

if (colorPresets.length > 0 && bgColorInput && textColorInput) {
    colorPresets.forEach(preset => {
        preset.addEventListener('click', () => {
            colorPresets.forEach(p => p.classList.remove('active'));
            preset.classList.add('active');
            
            bgColorInput.value = preset.dataset.bg;
            textColorInput.value = preset.dataset.text;
        });
    });
}
