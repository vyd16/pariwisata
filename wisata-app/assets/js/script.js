/**
 * Tourism Website - JavaScript
 * Dynamic functionality for forms and UI
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize all components
    initNavbar();
    initMobileMenu();
    initPassengerForms();
    initImagePreview();
    initAlertDismiss();
    initConfirmDelete();
    initScrollAnimations();
});

/**
 * Navbar scroll effect
 */
function initNavbar() {
    const navbar = document.querySelector('.navbar');
    if (!navbar) return;
    
    window.addEventListener('scroll', () => {
        if (window.scrollY > 50) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    });
}

/**
 * Mobile menu toggle
 */
function initMobileMenu() {
    const menuToggle = document.querySelector('.menu-toggle');
    const navMenu = document.querySelector('.nav-menu');
    const sidebar = document.querySelector('.sidebar');
    const sidebarToggle = document.querySelector('.sidebar-toggle');
    
    if (menuToggle && navMenu) {
        menuToggle.addEventListener('click', () => {
            navMenu.classList.toggle('active');
            menuToggle.classList.toggle('active');
        });
        
        // Close menu when clicking outside
        document.addEventListener('click', (e) => {
            if (!menuToggle.contains(e.target) && !navMenu.contains(e.target)) {
                navMenu.classList.remove('active');
                menuToggle.classList.remove('active');
            }
        });
    }
    
    // Admin sidebar toggle
    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.toggle('active');
        });
    }
}

/**
 * Dynamic Passenger Form Management (Master-Detail)
 */
function initPassengerForms() {
    const addBtn = document.getElementById('addPassenger');
    const container = document.getElementById('passengerContainer');
    
    if (!addBtn || !container) return;
    
    let passengerCount = container.querySelectorAll('.passenger-form').length || 1;
    
    // Add passenger
    addBtn.addEventListener('click', () => {
        passengerCount++;
        const template = createPassengerForm(passengerCount);
        container.insertAdjacentHTML('beforeend', template);
        updateRemoveButtons();
    });
    
    // Remove passenger (delegated event)
    container.addEventListener('click', (e) => {
        if (e.target.closest('.remove-passenger')) {
            const form = e.target.closest('.passenger-form');
            form.remove();
            updatePassengerNumbers();
            updateRemoveButtons();
        }
    });
    
    // Initial state
    updateRemoveButtons();
}

/**
 * Create passenger form HTML
 */
function createPassengerForm(number) {
    return `
        <div class="passenger-form animate-fade-in-up">
            <button type="button" class="remove-passenger" title="Hapus">×</button>
            <div class="passenger-header">
                <span class="passenger-number">${number}</span>
                <strong>Penumpang ${number}</strong>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Nama Lengkap *</label>
                    <input type="text" name="penumpang[${number}][nama]" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">No. Identitas (KTP/Paspor)</label>
                    <input type="text" name="penumpang[${number}][no_identitas]" class="form-control">
                </div>
                <div class="form-group">
                    <label class="form-label">No. Telepon</label>
                    <input type="tel" name="penumpang[${number}][telepon]" class="form-control">
                </div>
            </div>
        </div>
    `;
}

/**
 * Update passenger numbers after removal
 */
function updatePassengerNumbers() {
    const forms = document.querySelectorAll('.passenger-form');
    forms.forEach((form, index) => {
        const number = index + 1;
        form.querySelector('.passenger-number').textContent = number;
        form.querySelector('.passenger-header strong').textContent = `Penumpang ${number}`;
        
        // Update input names
        form.querySelectorAll('input').forEach(input => {
            const name = input.getAttribute('name');
            if (name) {
                const newName = name.replace(/penumpang\[\d+\]/, `penumpang[${number}]`);
                input.setAttribute('name', newName);
            }
        });
    });
}

/**
 * Show/hide remove buttons based on count
 */
function updateRemoveButtons() {
    const forms = document.querySelectorAll('.passenger-form');
    const removeButtons = document.querySelectorAll('.remove-passenger');
    
    removeButtons.forEach(btn => {
        btn.style.display = forms.length > 1 ? 'flex' : 'none';
    });
}

/**
 * Image preview before upload
 */
function initImagePreview() {
    const imageInputs = document.querySelectorAll('input[type="file"][accept*="image"]');
    
    imageInputs.forEach(input => {
        input.addEventListener('change', function() {
            const file = this.files[0];
            if (!file) return;
            
            // Find or create preview element
            let preview = this.parentElement.querySelector('.img-preview');
            if (!preview) {
                preview = document.createElement('img');
                preview.className = 'img-preview mt-1';
                this.parentElement.appendChild(preview);
            }
            
            // Validate file type
            if (!file.type.startsWith('image/')) {
                alert('Pilih file gambar yang valid');
                this.value = '';
                return;
            }
            
            // Validate file size (max 2MB)
            if (file.size > 2097152) {
                alert('Ukuran file maksimal 2MB');
                this.value = '';
                return;
            }
            
            // Show preview
            const reader = new FileReader();
            reader.onload = (e) => {
                preview.src = e.target.result;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        });
    });
}

/**
 * Auto dismiss alerts
 */
function initAlertDismiss() {
    const alerts = document.querySelectorAll('.alert');
    
    alerts.forEach(alert => {
        // Auto dismiss after 5 seconds
        setTimeout(() => {
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-10px)';
            setTimeout(() => alert.remove(), 300);
        }, 5000);
        
        // Click to dismiss
        alert.addEventListener('click', () => {
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 300);
        });
    });
}

/**
 * Confirm delete actions
 */
function initConfirmDelete() {
    document.addEventListener('click', (e) => {
        const deleteBtn = e.target.closest('[data-confirm]');
        if (deleteBtn) {
            const message = deleteBtn.dataset.confirm || 'Apakah Anda yakin ingin menghapus?';
            if (!confirm(message)) {
                e.preventDefault();
            }
        }
    });
}

/**
 * Scroll reveal animations
 */
function initScrollAnimations() {
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-fade-in-up');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);
    
    // Observe elements with scroll-reveal class
    document.querySelectorAll('.scroll-reveal').forEach(el => {
        observer.observe(el);
    });
}

/**
 * Format number as Rupiah
 */
function formatRupiah(number) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(number);
}

/**
 * Calculate total price based on passengers
 */
function calculateTotal() {
    const pricePerPerson = parseInt(document.getElementById('pricePerPerson')?.value || 0);
    const passengerCount = document.querySelectorAll('.passenger-form').length;
    const total = pricePerPerson * passengerCount;
    
    const totalDisplay = document.getElementById('totalPrice');
    if (totalDisplay) {
        totalDisplay.textContent = formatRupiah(total);
    }
    
    const totalInput = document.getElementById('totalPriceInput');
    if (totalInput) {
        totalInput.value = total;
    }
}

// Expose to global scope if needed
window.calculateTotal = calculateTotal;
window.formatRupiah = formatRupiah;
