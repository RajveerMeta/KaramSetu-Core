<?php
$current_script = $_SERVER['SCRIPT_NAME'];

$nav_items = [
    'dashboard' => ['url' => 'admin/dashboard.php', 'label' => 'Dashboard', 'match' => 'admin/dashboard'],
    'ngos' => ['url' => 'admin/ngos/index.php', 'label' => 'NGOs', 'match' => 'admin/ngos'],
    'users' => ['url' => 'admin/users/index.php', 'label' => 'Users', 'match' => 'admin/users'],
    'causes' => ['url' => 'admin/causes/index.php', 'label' => 'Causes', 'match' => 'admin/causes'],
    'events' => ['url' => 'admin/events/index.php', 'label' => 'Events', 'match' => 'admin/events'],
    'contributions' => ['url' => 'admin/contributions/index.php', 'label' => 'Contributions', 'match' => 'admin/contributions'],
    'reports' => ['url' => 'admin/reports/index.php', 'label' => 'Reports', 'match' => 'admin/reports'],
];
?>
<nav class="sticky top-0 z-50 bg-ivory/90 backdrop-blur-md border-b border-gold/20" id="adminNavbar">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between w-full h-20">
            
            <!-- Logo -->
            <div class="flex-shrink-0 flex items-center">
                <a href="<?= baseUrl('admin/dashboard.php') ?>" class="text-2xl font-serif font-bold text-forest flex items-center gap-2 whitespace-nowrap">
                    <img src="/KaramSetu-Core/uploads/Logo.png" alt="KarmaSetu Logo" class="h-14 w-auto object-contain">
                </a>
            </div>

            <div class="hidden md:flex flex-wrap items-center justify-center gap-4 lg:gap-6 flex-1 px-4 md:px-8 desktop-nav-admin">
                <?php foreach ($nav_items as $item): ?>
                    <?php 
                        $isActive = strpos($current_script, $item['match']) !== false;
                        $desktopClasses = $isActive 
                            ? 'text-forest border-b-2 border-forest pb-1 transition-colors text-sm font-semibold tracking-wide whitespace-nowrap' 
                            : 'text-charcoal hover:text-forest transition-colors text-sm font-medium tracking-wide whitespace-nowrap';
                    ?>
                    <a href="<?= baseUrl($item['url']) ?>" class="<?= $desktopClasses ?>"><?= $item['label'] ?></a>
                <?php endforeach; ?>
            </div>

            <div class="flex flex-1 md:flex-none items-center justify-end min-w-0 pl-2">
                <div class="relative">
                    <button id="userDropdownBtn" class="flex items-center gap-2 text-charcoal hover:text-forest text-sm font-medium transition-colors focus:outline-none min-w-0 text-left max-w-[140px] sm:max-w-none">
                        <span class="flex items-center justify-center h-8 w-8 rounded-full bg-forest text-white text-xs font-serif font-bold shadow-sm flex-shrink-0">A</span>
                        <span class="truncate min-w-0 hidden sm:block">Admin</span>
                        <svg class="w-4 h-4 transition-transform duration-200" id="userDropdownIcon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    
                    <div id="userDropdownMenu" style="display:none;" class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-charcoal/10 overflow-hidden z-50 py-2">
                        <a href="<?= baseUrl('admin/profile.php') ?>" class="block px-4 py-2 text-sm text-charcoal hover:bg-ivory-dark hover:text-forest transition-colors">Profile</a>
                        <div class="border-t border-charcoal/5 my-1"></div>
                        <a href="#" class="logout-btn block px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors font-medium">Logout</a>
                    </div>
                </div>

                <!-- Mobile menu button -->
                <div class="md:hidden flex items-center ml-1 sm:ml-2 flex-shrink-0">
                    <button id="adminMobileMenuBtn" class="text-charcoal hover:text-forest focus:outline-none p-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Menu Panel -->
    <div id="adminMobileMenuPanel" class="md:hidden hidden bg-white border-t border-gold/20 pb-4 absolute left-0 right-0 top-full w-full shadow-lg">
        <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
            <?php foreach ($nav_items as $item): ?>
                <?php 
                    $isActive = strpos($current_script, $item['match']) !== false;
                    $mobileClasses = $isActive 
                        ? "block px-3 py-2 rounded-md text-base font-bold text-forest bg-forest/5" 
                        : "block px-3 py-2 rounded-md text-base font-medium text-charcoal hover:text-forest hover:bg-ivory-dark";
                ?>
                <a href="<?= baseUrl($item['url']) ?>" class="<?= $mobileClasses ?>"><?= $item['label'] ?></a>
            <?php endforeach; ?>
        </div>
    </div>
</nav>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const mobileBtn = document.getElementById("adminMobileMenuBtn");
        const mobilePanel = document.getElementById("adminMobileMenuPanel");
        const mobileMenuIconPath = mobileBtn ? mobileBtn.querySelector("path") : null;
        
        const iconMenu = "M4 6h16M4 12h16M4 18h16";
        const iconClose = "M6 18L18 6M6 6l12 12";

        if (mobileBtn && mobilePanel && mobileMenuIconPath) {
            mobileBtn.addEventListener("click", () => {
                const isHidden = mobilePanel.classList.toggle("hidden");
                mobileMenuIconPath.setAttribute("d", isHidden ? iconMenu : iconClose);
            });
        }
        
        // Handle desktop dropdown
        const userBtn = document.getElementById("userDropdownBtn");
        const userMenu = document.getElementById("userDropdownMenu");
        if(userBtn && userMenu) {
            userBtn.addEventListener("click", function(e) {
                e.stopPropagation();
                userMenu.style.display = userMenu.style.display === "none" ? "block" : "none";
            });
            document.addEventListener("click", function(e) {
                if(!userBtn.contains(e.target) && !userMenu.contains(e.target)) {
                    userMenu.style.display = "none";
                }
            });
        }
    });
</script>

