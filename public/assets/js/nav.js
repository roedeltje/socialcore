/**
 * Core Navigation JavaScript
 * Universal navigation system for all SocialCore core pages
 * 
 * Usage: Include this file after DOM elements are loaded
 */

class CoreNavigation {
    constructor() {
        this.userMenuBtn = null;
        this.userDropdownMenu = null;
        this.notificationsBtn = null;
        this.isInitialized = false;
        
        // Bind methods to preserve 'this' context
        this.handleUserMenuClick = this.handleUserMenuClick.bind(this);
        this.handleDocumentClick = this.handleDocumentClick.bind(this);
        this.handleNotificationsClick = this.handleNotificationsClick.bind(this);
        this.handleKeyPress = this.handleKeyPress.bind(this);
    }

    /**
     * Initialize the navigation system
     */
    init() {
        if (this.isInitialized) {
            console.warn('CoreNavigation already initialized');
            return;
        }

        this.findElements();
        this.attachEventListeners();
        this.setupAccessibility();
        this.isInitialized = true;
        
        console.log('🎯 Core Navigation initialized successfully');
    }

    /**
     * Find DOM elements
     */
    findElements() {
        this.userMenuBtn = document.getElementById('userMenuBtn');
        this.userDropdownMenu = document.getElementById('userDropdownMenu');
        this.notificationsBtn = document.getElementById('notificationsBtn');
        
        // Debug logging
        if (!this.userMenuBtn) {
            console.warn('User menu button not found');
        }
        if (!this.userDropdownMenu) {
            console.warn('User dropdown menu not found');
        }
        if (!this.notificationsBtn) {
            console.warn('Notifications button not found');
        }
    }

    /**
     * Attach event listeners
     */
    attachEventListeners() {
        // User menu dropdown
        if (this.userMenuBtn && this.userDropdownMenu) {
            this.userMenuBtn.addEventListener('click', this.handleUserMenuClick);
        }
        
        // Notifications button
        if (this.notificationsBtn) {
            this.notificationsBtn.addEventListener('click', this.handleNotificationsClick);
        }
        
        // Global click handler to close dropdowns
        document.addEventListener('click', this.handleDocumentClick);
        
        // Keyboard navigation
        document.addEventListener('keydown', this.handleKeyPress);
        
        // Close dropdown on escape key
        document.addEventListener('keyup', (e) => {
            if (e.key === 'Escape') {
                this.closeDropdown();
            }
        });
    }

    /**
     * Setup accessibility features
     */
    setupAccessibility() {
        if (this.userMenuBtn) {
            this.userMenuBtn.setAttribute('aria-expanded', 'false');
            this.userMenuBtn.setAttribute('aria-haspopup', 'menu');
        }
        
        if (this.userDropdownMenu) {
            this.userDropdownMenu.setAttribute('role', 'menu');
            this.userDropdownMenu.setAttribute('aria-hidden', 'true');
            
            // Add role="menuitem" to dropdown links
            const dropdownItems = this.userDropdownMenu.querySelectorAll('.dropdown-item');
            dropdownItems.forEach(item => {
                item.setAttribute('role', 'menuitem');
                item.setAttribute('tabindex', '-1');
            });
        }
    }

    /**
     * Handle user menu button click
     */
    handleUserMenuClick(e) {
        e.stopPropagation();
        e.preventDefault();
        
        const isVisible = this.userDropdownMenu.style.display !== 'none';
        
        if (isVisible) {
            this.closeDropdown();
        } else {
            this.openDropdown();
        }
    }

    /**
     * Handle notifications button click
     */
    handleNotificationsClick(e) {
        e.preventDefault();
        
        // Get base URL from current page
        const baseUrl = this.getBaseUrl();
        
        // Navigate to notifications page
        window.location.href = `${baseUrl}/?route=notifications`;
    }

    /**
     * Handle document click (for closing dropdowns)
     */
    handleDocumentClick(event) {
        if (!this.userMenuBtn || !this.userDropdownMenu) {
            return;
        }
        
        // Check if click is outside the dropdown area
        const isClickInsideMenu = this.userMenuBtn.contains(event.target) || 
                                 this.userDropdownMenu.contains(event.target);
        
        if (!isClickInsideMenu) {
            this.closeDropdown();
        }
    }

    /**
     * Handle keyboard navigation
     */
    handleKeyPress(e) {
        if (!this.userDropdownMenu || this.userDropdownMenu.style.display === 'none') {
            return;
        }
        
        const dropdownItems = Array.from(this.userDropdownMenu.querySelectorAll('.dropdown-item'));
        const currentFocused = document.activeElement;
        const currentIndex = dropdownItems.indexOf(currentFocused);
        
        switch (e.key) {
            case 'ArrowDown':
                e.preventDefault();
                const nextIndex = currentIndex < dropdownItems.length - 1 ? currentIndex + 1 : 0;
                dropdownItems[nextIndex].focus();
                break;
                
            case 'ArrowUp':
                e.preventDefault();
                const prevIndex = currentIndex > 0 ? currentIndex - 1 : dropdownItems.length - 1;
                dropdownItems[prevIndex].focus();
                break;
                
            case 'Enter':
            case ' ':
                if (currentFocused && dropdownItems.includes(currentFocused)) {
                    e.preventDefault();
                    currentFocused.click();
                }
                break;
        }
    }

    /**
     * Open the dropdown menu
     */
    openDropdown() {
        if (!this.userDropdownMenu || !this.userMenuBtn) {
            return;
        }
        
        this.userDropdownMenu.style.display = 'block';
        this.userMenuBtn.classList.add('active');
        this.userMenuBtn.setAttribute('aria-expanded', 'true');
        this.userDropdownMenu.setAttribute('aria-hidden', 'false');
        
        // Focus first menu item for keyboard navigation
        const firstItem = this.userDropdownMenu.querySelector('.dropdown-item');
        if (firstItem) {
            setTimeout(() => firstItem.focus(), 50);
        }
        
        // Add animation class
        this.userDropdownMenu.classList.add('fade-in');
    }

    /**
     * Close the dropdown menu
     */
    closeDropdown() {
        if (!this.userDropdownMenu || !this.userMenuBtn) {
            return;
        }
        
        this.userDropdownMenu.style.display = 'none';
        this.userMenuBtn.classList.remove('active');
        this.userMenuBtn.setAttribute('aria-expanded', 'false');
        this.userDropdownMenu.setAttribute('aria-hidden', 'true');
        
        // Remove animation class
        this.userDropdownMenu.classList.remove('fade-in');
    }

    /**
     * Toggle dropdown menu
     */
    toggleDropdown() {
        const isVisible = this.userDropdownMenu && this.userDropdownMenu.style.display !== 'none';
        
        if (isVisible) {
            this.closeDropdown();
        } else {
            this.openDropdown();
        }
    }

    /**
     * Get base URL for navigation
     */
    getBaseUrl() {
        // Try to get from meta tag first
        const baseUrlMeta = document.querySelector('meta[name="base-url"]');
        if (baseUrlMeta) {
            return baseUrlMeta.content;
        }
        
        // Fallback to current origin
        return window.location.origin;
    }

    /**
     * Update notification badge
     */
    updateNotificationBadge(count) {
        if (!this.notificationsBtn) {
            return;
        }
        
        let badge = this.notificationsBtn.querySelector('.notification-badge');
        
        if (count > 0) {
            if (!badge) {
                badge = document.createElement('span');
                badge.className = 'notification-badge';
                this.notificationsBtn.appendChild(badge);
            }
            
            badge.textContent = count > 9 ? '9+' : count.toString();
            badge.style.display = 'flex';
        } else {
            if (badge) {
                badge.style.display = 'none';
            }
        }
    }

    /**
     * Refresh user avatar
     */
    updateUserAvatar(avatarUrl) {
        const avatars = document.querySelectorAll('.user-avatar, .current-user-avatar');
        
        avatars.forEach(avatar => {
            if (avatar.tagName === 'IMG') {
                avatar.src = avatarUrl;
            }
        });
    }

    /**
     * Add loading state to navigation
     */
    setLoadingState(loading = true) {
        const navButtons = document.querySelectorAll('.nav-btn');
        
        navButtons.forEach(btn => {
            if (loading) {
                btn.style.opacity = '0.6';
                btn.style.pointerEvents = 'none';
            } else {
                btn.style.opacity = '1';
                btn.style.pointerEvents = 'auto';
            }
        });
    }

    /**
     * Show a temporary notification in the navigation
     */
    showNotification(message, type = 'info', duration = 3000) {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `nav-notification nav-notification-${type}`;
        notification.textContent = message;
        
        // Style the notification
        Object.assign(notification.style, {
            position: 'fixed',
            top: '20px',
            right: '20px',
            background: type === 'error' ? '#ff4757' : '#4A90E2',
            color: 'white',
            padding: '12px 16px',
            borderRadius: '8px',
            boxShadow: '0 4px 12px rgba(0,0,0,0.3)',
            zIndex: '9999',
            fontSize: '14px',
            fontWeight: '500',
            maxWidth: '300px',
            opacity: '0',
            transform: 'translateX(100%)',
            transition: 'all 0.3s ease'
        });
        
        // Add to page
        document.body.appendChild(notification);
        
        // Animate in
        setTimeout(() => {
            notification.style.opacity = '1';
            notification.style.transform = 'translateX(0)';
        }, 50);
        
        // Remove after duration
        setTimeout(() => {
            notification.style.opacity = '0';
            notification.style.transform = 'translateX(100%)';
            
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 300);
        }, duration);
    }

    /**
     * Destroy the navigation (cleanup)
     */
    destroy() {
        if (!this.isInitialized) {
            return;
        }
        
        // Remove event listeners
        if (this.userMenuBtn) {
            this.userMenuBtn.removeEventListener('click', this.handleUserMenuClick);
        }
        
        if (this.notificationsBtn) {
            this.notificationsBtn.removeEventListener('click', this.handleNotificationsClick);
        }
        
        document.removeEventListener('click', this.handleDocumentClick);
        document.removeEventListener('keydown', this.handleKeyPress);
        
        // Reset state
        this.userMenuBtn = null;
        this.userDropdownMenu = null;
        this.notificationsBtn = null;
        this.isInitialized = false;
        
        console.log('🎯 Core Navigation destroyed');
    }
}

// Create global instance
window.CoreNavigation = CoreNavigation;

// Auto-initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function() {
        if (!window.coreNav) {
            window.coreNav = new CoreNavigation();
            window.coreNav.init();
        }
    });
} else {
    // DOM already loaded
    if (!window.coreNav) {
        window.coreNav = new CoreNavigation();
        window.coreNav.init();
    }
}

// Export for module systems
if (typeof module !== 'undefined' && module.exports) {
    module.exports = CoreNavigation;
}

// Utility functions for easy access
window.updateNotificationCount = function(count) {
    if (window.coreNav) {
        window.coreNav.updateNotificationBadge(count);
    }
};

window.updateUserAvatar = function(avatarUrl) {
    if (window.coreNav) {
        window.coreNav.updateUserAvatar(avatarUrl);
    }
};

window.showNavNotification = function(message, type, duration) {
    if (window.coreNav) {
        window.coreNav.showNotification(message, type, duration);
    }
};