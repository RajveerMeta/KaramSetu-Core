<?php
$isLoggedIn = !empty($_SESSION['user_id']);
$authUserName = $_SESSION['user_name'] ?? '';
$authUserInitial = !empty($authUserName) ? strtoupper(mb_substr(trim($authUserName), 0, 1, 'UTF-8')) : 'U';
$authUserRole = strtolower(trim($_SESSION['user_role'] ?? 'user'));
?>
<nav class="sticky top-0 z-50 bg-ivory/90 backdrop-blur-md border-b border-gold/20" id="mainNavbar">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between w-full h-20">
            
            <!-- Logo -->
            <div class="flex-shrink-0 flex items-center">
                <a href="<?= baseUrl('') ?>" class="text-2xl font-serif font-bold text-forest flex items-center space-x-2 whitespace-nowrap">
                    <img src="/KaramSetu-Core/uploads/Logo.png" alt="KarmaSetu Logo" class="h-14 w-auto object-contain">
                </a>
            </div>

            <div class="hidden md:flex flex-1 items-center justify-center">
                <div class="flex items-center justify-center space-x-4 lg:space-x-8 px-4 desktop-nav-default">
                    <a href="<?= baseUrl('') ?>" class="text-charcoal hover:text-forest transition-colors text-sm font-medium tracking-wide whitespace-nowrap">Home</a>
                    <a href="<?= baseUrl('?page=causes') ?>" class="text-charcoal hover:text-forest transition-colors text-sm font-medium tracking-wide whitespace-nowrap">Causes</a>
                    <a href="<?= baseUrl('?page=events') ?>" class="text-charcoal hover:text-forest transition-colors text-sm font-medium tracking-wide whitespace-nowrap">Events</a>
                    <a href="<?= baseUrl('?page=ngos') ?>" class="text-charcoal hover:text-forest transition-colors text-sm font-medium tracking-wide whitespace-nowrap">NGOs</a>
                    <a href="<?= baseUrl('?page=about') ?>" class="text-charcoal hover:text-forest transition-colors text-sm font-medium tracking-wide whitespace-nowrap">About</a>
                </div>
            </div>

            <!-- Right Actions -->
            <div class="flex flex-1 md:flex-none items-center justify-end min-w-0 pl-2">
                <div class="auth-guest flex items-center space-x-4"<?= $isLoggedIn ? ' style="display:none;"' : '' ?>>
                    <a href="<?= baseUrl('auth/login.php') ?>" class="text-charcoal hover:text-forest text-sm font-medium transition-colors whitespace-nowrap">Login</a>
                    <a href="<?= baseUrl('auth/register.php') ?>" class="bg-forest text-ivory hover:bg-forest-dark px-5 py-2 rounded-full text-sm font-medium transition-all shadow-sm whitespace-nowrap">Join KarmaSetu</a>
                </div>

                <div class="auth-user relative min-w-0"<?= !$isLoggedIn ? ' style="display:none;"' : '' ?>>
                    <button id="userDropdownBtn" class="flex items-center space-x-2 text-charcoal hover:text-forest text-sm font-medium transition-colors focus:outline-none min-w-0 text-left max-w-[140px] sm:max-w-none">
                        <span class="flex-shrink-0 flex items-center justify-center h-8 w-8 rounded-full bg-forest text-white text-xs font-serif font-bold shadow-sm user-initial"><?= e($authUserInitial) ?></span>
                        <span class="user-name truncate min-w-0"><?= e($authUserName) ?></span>
                        <svg class="flex-shrink-0 w-4 h-4 transition-transform duration-200" id="userDropdownIcon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    
                    <!-- Dropdown menu -->
                    <div id="userDropdownMenu" style="display:none;" class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-lg border border-charcoal/10 overflow-hidden z-50 py-2">
                        <?php if ($authUserRole === 'admin'): ?>
                            <a href="<?= baseUrl('admin/dashboard.php') ?>" class="block px-4 py-2 text-sm text-charcoal hover:bg-ivory-dark hover:text-forest transition-colors">Admin Dashboard</a>
                            <a href="<?= baseUrl('admin/profile.php') ?>" class="block px-4 py-2 text-sm text-charcoal hover:bg-ivory-dark hover:text-forest transition-colors">Profile</a>
                        <?php elseif ($authUserRole === 'ngo'): ?>
                            <a href="<?= baseUrl('ngo/dashboard.php') ?>" class="block px-4 py-2 text-sm text-charcoal hover:bg-ivory-dark hover:text-forest transition-colors">NGO Dashboard</a>
                            <a href="<?= baseUrl('ngo/profile.php') ?>" class="block px-4 py-2 text-sm text-charcoal hover:bg-ivory-dark hover:text-forest transition-colors">Manage Profile</a>
                        <?php else: ?>
                            <a href="<?= baseUrl('user/dashboard.php') ?>" class="block px-4 py-2 text-sm text-charcoal hover:bg-ivory-dark hover:text-forest transition-colors">My Dashboard</a>
                            <a href="<?= baseUrl('user/contributions.php') ?>" class="block px-4 py-2 text-sm text-charcoal hover:bg-ivory-dark hover:text-forest transition-colors">My Contributions</a>
                            <a href="<?= baseUrl('user/events.php') ?>" class="block px-4 py-2 text-sm text-charcoal hover:bg-ivory-dark hover:text-forest transition-colors">My Events-Tickets</a>
                            <a href="<?= baseUrl('user/volunteer-activities.php') ?>" class="block px-4 py-2 text-sm text-charcoal hover:bg-ivory-dark hover:text-forest transition-colors">Volunteer Activities</a>
                            <a href="<?= baseUrl('?page=leaderboard') ?>" class="block px-4 py-2 text-sm text-charcoal hover:bg-ivory-dark hover:text-forest transition-colors">Rewards and Karma Points</a>
                            <a href="<?= baseUrl('user/profile.php') ?>" class="block px-4 py-2 text-sm text-charcoal hover:bg-ivory-dark hover:text-forest transition-colors">Profile & Settings</a>
                        <?php endif; ?>
                        <div class="border-t border-charcoal/5 my-1"></div>
                        <a href="<?= baseUrl('auth/logout.php') ?>" class="logout-btn block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors font-medium">Logout</a>
                    </div>
                </div>


                <!-- Mobile menu button -->
                <div class="md:hidden flex items-center ml-1 sm:ml-2 flex-shrink-0">
                    <button id="mobileMenuBtn" class="text-charcoal hover:text-forest focus:outline-none p-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                </div>
            </div>

        </div>
    </div>

    <!-- Mobile Menu Panel -->
    <div id="mobileMenuPanel" class="md:hidden hidden bg-white border-t border-gold/20 pb-4 absolute left-0 right-0 top-full w-full shadow-lg">
        <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3 mobile-nav-default">
            <a href="<?= baseUrl('') ?>" class="mobile-link block px-3 py-2 rounded-md text-base font-medium text-charcoal hover:text-forest hover:bg-ivory-dark">Home</a>
            <a href="<?= baseUrl('?page=causes') ?>" class="mobile-link block px-3 py-2 rounded-md text-base font-medium text-charcoal hover:text-forest hover:bg-ivory-dark">Causes</a>
            <a href="<?= baseUrl('?page=events') ?>" class="mobile-link block px-3 py-2 rounded-md text-base font-medium text-charcoal hover:text-forest hover:bg-ivory-dark">Events</a>
            <a href="<?= baseUrl('?page=ngos') ?>" class="mobile-link block px-3 py-2 rounded-md text-base font-medium text-charcoal hover:text-forest hover:bg-ivory-dark">NGOs</a>
            <a href="<?= baseUrl('?page=about') ?>" class="mobile-link block px-3 py-2 rounded-md text-base font-medium text-charcoal hover:text-forest hover:bg-ivory-dark">About</a>
        </div>
    </div>
</nav>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const mobileBtn = document.getElementById('mobileMenuBtn');
        const mobilePanel = document.getElementById('mobileMenuPanel');
        const mobileMenuIconPath = mobileBtn ? mobileBtn.querySelector('path') : null;
        
        // Hamburger SVG path
        const iconMenu = "M4 6h16M4 12h16M4 18h16";
        // Close (X) SVG path
        const iconClose = "M6 18L18 6M6 6l12 12";

        if (mobileBtn && mobilePanel && mobileMenuIconPath) {
            mobileBtn.addEventListener('click', () => {
                const isHidden = mobilePanel.classList.toggle('hidden');
                mobileMenuIconPath.setAttribute('d', isHidden ? iconMenu : iconClose);
            });
            
            // Close menu on link click
            const mobileLinks = mobilePanel.querySelectorAll('.mobile-link');
            mobileLinks.forEach(link => {
                link.addEventListener('click', () => {
                    mobilePanel.classList.add('hidden');
                    mobileMenuIconPath.setAttribute('d', iconMenu);
                });
            });
        }

        // User dropdown functionality
        const userDropdownBtn = document.getElementById('userDropdownBtn');
        const userDropdownMenu = document.getElementById('userDropdownMenu');
        const userDropdownIcon = document.getElementById('userDropdownIcon');

        function openUserDropdown() {
            if (userDropdownMenu) {
                userDropdownMenu.style.display = 'block';
            }
            if (userDropdownIcon) {
                userDropdownIcon.classList.add('rotate-180');
            }
        }

        function closeUserDropdown() {
            if (userDropdownMenu) {
                userDropdownMenu.style.display = 'none';
            }
            if (userDropdownIcon) {
                userDropdownIcon.classList.remove('rotate-180');
            }
        }

        function isUserDropdownOpen() {
            return userDropdownMenu && userDropdownMenu.style.display !== 'none';
        }

        if (userDropdownBtn && userDropdownMenu) {
            userDropdownBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                if (isUserDropdownOpen()) {
                    closeUserDropdown();
                } else {
                    openUserDropdown();
                }
            });

            // Close when clicking outside
            document.addEventListener('click', function(e) {
                if (!userDropdownBtn.contains(e.target) && !userDropdownMenu.contains(e.target)) {
                    closeUserDropdown();
                }
            });

            // Close on Escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' || e.key === 'Esc' || e.keyCode === 27) {
                    closeUserDropdown();
                }
            });

            // Close on selecting any menu link or button
            const dropdownLinks = userDropdownMenu.querySelectorAll('a, button');
            dropdownLinks.forEach(function(item) {
                item.addEventListener('click', function() {
                    closeUserDropdown();
                });
            });
        }
    });
</script>





