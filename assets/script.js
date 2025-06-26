// Execute after the page is loaded
document.addEventListener('DOMContentLoaded', function() {
    initializeFormInteractions();
    initializePasswordToggle();
    initializeFormValidation();
    checkUrlParams();
});

// Initialize form interactions
function initializeFormInteractions() {
    const inputs = document.querySelectorAll('input');
    
    inputs.forEach(input => {
        // Focus effect
        input.addEventListener('focus', function() {
            this.parentElement.classList.add('focused');
            addInputAnimation(this);
        });
        
        // Blur effect
        input.addEventListener('blur', function() {
            if (this.value === '') {
                this.parentElement.classList.remove('focused');
            }
            removeInputAnimation(this);
        });
        
        // Input effect
        input.addEventListener('input', function() {
            validateField(this);
        });
    });
}

// Add input field animation effect
function addInputAnimation(input) {
    input.style.transform = 'scale(1.02)';
    input.style.transition = 'all 0.3s ease';
}

// Remove input field animation effect
function removeInputAnimation(input) {
    input.style.transform = 'scale(1)';
}

// Form validation
function initializeFormValidation() {
    const forms = document.querySelectorAll('form');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!validateForm(this)) {
                e.preventDefault();
                showValidationError('Please check if the form is filled correctly');
            } else {
                showLoadingState(this);
            }
        });
    });
}

// Validate the entire form
function validateForm(form) {
    const inputs = form.querySelectorAll('input[required]');
    let isValid = true;
    
    inputs.forEach(input => {
        if (!validateField(input)) {
            isValid = false;
        }
    });
    
    return isValid;
}

// Validate a single field
function validateField(input) {
    const value = input.value.trim();
    const type = input.type;
    const name = input.name;
    
    // Clear previous error states
    clearFieldError(input);
    
    // Required field check
    if (input.hasAttribute('required') && value === '') {
        showFieldError(input, 'This field is required');
        return false;
    }
    
    // Email format check
    if (type === 'email' && value !== '') {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(value)) {
            showFieldError(input, 'Please enter a valid email address');
            return false;
        }
    }
    
    // Password strength check
    if (name === 'password' && value !== '') {
        if (value.length < 6) {
            showFieldError(input, 'Password must be at least 6 characters long');
            return false;
        }
    }
    
    // Username check
    if (name === 'username' && value !== '') {
        const usernameRegex = /^[a-zA-Z0-9_]{3,20}$/;
        if (!usernameRegex.test(value)) {
            showFieldError(input, 'Username can only contain letters, numbers, and underscores, and must be 3-20 characters long');
            return false;
        }
    }
    
    return true;
}

// Show field error
function showFieldError(input, message) {
    input.style.borderColor = '#f56565';
    input.style.backgroundColor = '#fed7d7';
    
    // Remove existing error information
    const existingError = input.parentElement.querySelector('.field-error');
    if (existingError) {
        existingError.remove();
    }
    
    // Add error information
    const errorElement = document.createElement('div');
    errorElement.className = 'field-error';
    errorElement.textContent = message;
    errorElement.style.cssText = `
        color: #f56565;
        font-size: 0.8rem;
        margin-top: 5px;
        animation: slideDown 0.3s ease;
    `;
    
    input.parentElement.appendChild(errorElement);
}

// Clear field error
function clearFieldError(input) {
    input.style.borderColor = '#e1e5e9';
    input.style.backgroundColor = '#f8f9fa';
    
    const errorElement = input.parentElement.querySelector('.field-error');
    if (errorElement) {
        errorElement.remove();
    }
}

// Show validation error information
function showValidationError(message) {
    const container = document.querySelector('.container');
    const existingError = container.querySelector('.validation-error');
    
    if (existingError) {
        existingError.remove();
    }
    
    const errorDiv = document.createElement('div');
    errorDiv.className = 'validation-error error-message';
    errorDiv.textContent = message;
    
    container.insertBefore(errorDiv, container.firstChild);
    
    // Automatically disappear after 3 seconds
    setTimeout(() => {
        if (errorDiv.parentElement) {
            errorDiv.remove();
        }
    }, 3000);
}

// Show form submission loading state
function showLoadingState(form) {
    const submitBtn = form.querySelector('.btn');
    const originalText = submitBtn.textContent;
    
    submitBtn.disabled = true;
    submitBtn.style.opacity = '0.7';
    submitBtn.innerHTML = '<span>Processing...</span>';
    
    // Add loading animation
    const loadingSpinner = document.createElement('div');
    loadingSpinner.innerHTML = '⏳';
    loadingSpinner.style.cssText = `
        display: inline-block;
        margin-left: 8px;
        animation: spin 1s linear infinite;
    `;
    
    submitBtn.appendChild(loadingSpinner);
    
    // Add rotation animation
    const style = document.createElement('style');
    style.textContent = `
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    `;
    document.head.appendChild(style);
}

// Initialize password display toggle functionality
function initializePasswordToggle() {
    const passwordInputs = document.querySelectorAll('input[type="password"]');
    
    passwordInputs.forEach(input => {
        const toggleBtn = document.createElement('button');
        toggleBtn.type = 'button';
        toggleBtn.innerHTML = '👁️';
        toggleBtn.className = 'password-toggle';
        toggleBtn.style.cssText = `
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            font-size: 1.2rem;
            opacity: 0.6;
            transition: opacity 0.3s ease;
        `;
        
        toggleBtn.addEventListener('click', function() {
            togglePasswordVisibility(input, this);
        });
        
        toggleBtn.addEventListener('mouseenter', function() {
            this.style.opacity = '1';
        });
        
        toggleBtn.addEventListener('mouseleave', function() {
            this.style.opacity = '0.6';
        });
        
        input.parentElement.style.position = 'relative';
        input.parentElement.appendChild(toggleBtn);
    });
}

// Toggle password visibility
function togglePasswordVisibility(input, toggleBtn) {
    if (input.type === 'password') {
        input.type = 'text';
        toggleBtn.innerHTML = '🙈';
    } else {
        input.type = 'password';
        toggleBtn.innerHTML = '👁️';
    }
}

// Check URL parameters
function checkUrlParams() {
    const urlParams = new URLSearchParams(window.location.search);
    const message = urlParams.get('message');
    
    if (message === 'session_expired') {
        showValidationError('Session expired, please login again');
    }
}

// Add page transition animation effect
function addPageTransition() {
    const container = document.querySelector('.container');
    
    container.style.opacity = '0';
    container.style.transform = 'translateY(20px)';
    
    setTimeout(() => {
        container.style.transition = 'all 0.5s ease';
        container.style.opacity = '1';
        container.style.transform = 'translateY(0)';
    }, 100);
}

// Add mouse follow effect (optional enhanced feature)
function addMouseFollowEffect() {
    let mouseX = 0;
    let mouseY = 0;
    
    document.addEventListener('mousemove', function(e) {
        mouseX = e.clientX;
        mouseY = e.clientY;
        
        const container = document.querySelector('.container');
        const rect = container.getBoundingClientRect();
        const centerX = rect.left + rect.width / 2;
        const centerY = rect.top + rect.height / 2;
        
        const deltaX = (mouseX - centerX) / centerX;
        const deltaY = (mouseY - centerY) / centerY;
        
        // Slight 3D tilt effect
        const rotateX = deltaY * 5;
        const rotateY = deltaX * 5;
        
        container.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg)`;
    });
    
    // Reset when mouse leaves
    document.addEventListener('mouseleave', function() {
        const container = document.querySelector('.container');
        container.style.transform = 'perspective(1000px) rotateX(0deg) rotateY(0deg)';
    });
}

// Add keyboard shortcut support
function addKeyboardShortcuts() {
    document.addEventListener('keydown', function(e) {
        // Enter key to quickly submit the form
        if (e.key === 'Enter' && e.ctrlKey) {
            const activeForm = document.querySelector('form');
            if (activeForm) {
                const submitBtn = activeForm.querySelector('.btn');
                if (submitBtn && !submitBtn.disabled) {
                    submitBtn.click();
                }
            }
        }
        
        // Escape key to clear the form
        if (e.key === 'Escape') {
            clearAllForms();
        }
    });
}

// Clear all forms
function clearAllForms() {
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.reset();
        // Clear all error states
        const inputs = form.querySelectorAll('input');
        inputs.forEach(input => {
            clearFieldError(input);
        });
    });
}

// Add form auto-save functionality (using memory storage)
function addAutoSave() {
    const formData = {};
    const inputs = document.querySelectorAll('input');
    
    inputs.forEach(input => {
        // Restore data from memory (lost on page refresh)
        if (formData[input.name]) {
            input.value = formData[input.name];
        }
        
        // Listen for input changes and save to memory
        input.addEventListener('input', function() {
            if (this.type !== 'password') { // Do not save password
                formData[this.name] = this.value;
            }
        });
    });
}

// Add form submission confirmation
function addSubmitConfirmation() {
    const forms = document.querySelectorAll('form');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const isLogin = form.querySelector('input[name="username"]') && 
                           form.querySelector('input[name="password"]') && 
                           !form.querySelector('input[name="email"]');
            
            if (!isLogin) { // Only show confirmation for registration forms
                const confirmed = confirm('Are you sure you want to submit the registration information?');
                if (!confirmed) {
                    e.preventDefault();
                }
            }
        });
    });
}

// Add network status detection
function addNetworkStatusDetection() {
    function updateNetworkStatus() {
        const isOnline = navigator.onLine;
        const existingNotice = document.querySelector('.network-notice');
        
        if (existingNotice) {
            existingNotice.remove();
        }
        
        if (!isOnline) {
            const notice = document.createElement('div');
            notice.className = 'network-notice';
            notice.innerHTML = '⚠️ Network connection is lost, please check your network settings';
            notice.style.cssText = `
                position: fixed;
                top: 20px;
                left: 50%;
                transform: translateX(-50%);
                background: #f56565;
                color: white;
                padding: 10px 20px;
                border-radius: 8px;
                z-index: 1000;
                font-size: 0.9rem;
                animation: slideDown 0.3s ease;
            `;
            
            document.body.appendChild(notice);
        }
    }
    
    window.addEventListener('online', updateNetworkStatus);
    window.addEventListener('offline', updateNetworkStatus);
    
    // Initial check
    updateNetworkStatus();
}

// Add dark mode toggle (optional feature)
function addDarkModeToggle() {
    // Create toggle button
    const darkModeBtn = document.createElement('button');
    darkModeBtn.innerHTML = '🌙';
    darkModeBtn.className = 'dark-mode-toggle';
    darkModeBtn.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: rgba(255, 255, 255, 0.2);
        border: none;
        border-radius: 50%;
        width: 50px;
        height: 50px;
        font-size: 1.5rem;
        cursor: pointer;
        transition: all 0.3s ease;
        z-index: 1000;
        backdrop-filter: blur(10px);
    `;
    
    darkModeBtn.addEventListener('click', toggleDarkMode);
    document.body.appendChild(darkModeBtn);
    
    // Check user preferences
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    if (prefersDark) {
        enableDarkMode();
    }
}

// Toggle dark mode
function toggleDarkMode() {
    const body = document.body;
    const isDark = body.classList.contains('dark-mode');
    
    if (isDark) {
        disableDarkMode();
    } else {
        enableDarkMode();
    }
}

// Enable dark mode
function enableDarkMode() {
    const darkStyles = `
        body.dark-mode {
            background: linear-gradient(135deg, #2d3748 0%, #4a5568 100%) !important;
        }
        
        .dark-mode .container {
            background: rgba(45, 55, 72, 0.95) !important;
            color: white;
        }
        
        .dark-mode .logo h1,
        .dark-mode .welcome-title {
            color: white !important;
        }
        
        .dark-mode .logo p,
        .dark-mode .welcome-subtitle,
        .dark-mode .info-text {
            color: #cbd5e0 !important;
        }
        
        .dark-mode .form-group input {
            background: #4a5568 !important;
            border-color: #718096 !important;
            color: white;
        }
        
        .dark-mode .form-group input:focus {
            background: #2d3748 !important;
        }
        
        .dark-mode .form-group label {
            color: #a0aec0 !important;
        }
    `;
    
    let styleElement = document.getElementById('dark-mode-styles');
    if (!styleElement) {
        styleElement = document.createElement('style');
        styleElement.id = 'dark-mode-styles';
        document.head.appendChild(styleElement);
    }
    
    styleElement.textContent = darkStyles;
    document.body.classList.add('dark-mode');
    
    const toggleBtn = document.querySelector('.dark-mode-toggle');
    if (toggleBtn) {
        toggleBtn.innerHTML = '☀️';
    }
}

// Disable dark mode
function disableDarkMode() {
    document.body.classList.remove('dark-mode');
    const styleElement = document.getElementById('dark-mode-styles');
    if (styleElement) {
        styleElement.remove();
    }
    
    const toggleBtn = document.querySelector('.dark-mode-toggle');
    if (toggleBtn) {
        toggleBtn.innerHTML = '🌙';
    }
}

// Add performance monitoring
function addPerformanceMonitoring() {
    // Monitor page load time
    window.addEventListener('load', function() {
        const loadTime = performance.now();
        console.log(`页面加载完成，耗时: ${loadTime.toFixed(2)}ms`);
        
        // If the load time is too long, show a prompt
        if (loadTime > 3000) {
            showValidationError('Page loading is slow, please check your network connection');
        }
    });
}

// Initialize all enhancements
function initializeEnhancements() {
    addPageTransition();
    addKeyboardShortcuts();
    addAutoSave();
    addSubmitConfirmation();
    addNetworkStatusDetection();
    addDarkModeToggle();
    addPerformanceMonitoring();
    
    // Optional: Enable mouse follow effect (may affect performance)
    // addMouseFollowEffect();
}

// Initialize enhancements after page load
document.addEventListener('DOMContentLoaded', function() {
    initializeEnhancements();
});

// Export functions for external use
window.FormUtils = {
    validateForm,
    validateField,
    showValidationError,
    clearAllForms,
    toggleDarkMode
};