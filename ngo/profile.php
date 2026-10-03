<?php
require_once __DIR__ . '/../includes/functions.php';

ob_start();
?>
<div class="min-h-screen bg-ivory py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        <!-- HEADER -->
        <div class="mb-10 flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl md:text-4xl font-serif font-bold text-forest mb-2">NGO Profile</h1>
                <div class="flex flex-wrap items-center gap-2 text-sm">
                    <span class="bg-forest/10 text-forest font-bold px-3 py-1 rounded-full flex items-center shadow-sm">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Verified NGO
                    </span>
                    <span class="text-charcoal-light">|</span>
                    <span class="text-charcoal-light">Manage Organization Details</span>
                </div>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="<?= baseUrl('ngo/edit-profile.php') ?>" class="inline-block bg-forest text-white font-bold py-2.5 px-6 rounded-full hover:bg-forest-dark transition-colors shadow-sm text-center no-underline">
                 Edit Profile
                </a>

                <!-- <button href="<?= baseUrl('ngo/edit-profile.php') ?>" type="button" class="inline-block bg-forest text-white font-bold py-2.5 px-6 rounded-full hover:bg-forest-dark transition-colors shadow-sm">
                    Edit Profile
                </button> -->
            </div>
        </div>

        <div class="flex flex-col lg:grid lg:grid-cols-3 gap-8 w-full">
            <!-- MAIN CONTENT (Left 2 Columns) -->
            <div class="lg:col-span-2 space-y-8">
                
                <!-- ORGANIZATION INFORMATION -->
                <div class="bg-white rounded-3xl p-4 md:p-8 shadow-sm border border-gold/20">
                    <h2 class="text-xl md:text-2xl font-serif font-bold text-forest mb-6 border-b border-charcoal/10 pb-4">Organization Information</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-bold text-charcoal-light uppercase tracking-wide mb-1">Organization Name</label>
                            <div class="text-lg font-bold text-charcoal">Seva Roots Initiative</div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-charcoal-light uppercase tracking-wide mb-1">Registration Number</label>
                            <div class="text-lg font-medium text-charcoal">NGO-GJ-2018-00123</div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-charcoal-light uppercase tracking-wide mb-1">Founded Year</label>
                            <div class="text-lg font-medium text-charcoal">2018</div>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-charcoal-light uppercase tracking-wide mb-1">Description</label>
                            <div class="text-base text-charcoal leading-relaxed">
                                Seva Roots Initiative works with local communities to support education, community development, and essential resources for people in need.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CONTACT & ADDRESS -->
                <div class="flex flex-col md:grid md:grid-cols-2 gap-8 w-full">
                    <!-- CONTACT INFORMATION -->
                    <div class="bg-white rounded-3xl p-4 md:p-8 shadow-sm border border-gold/20">
                        <h2 class="text-xl font-serif font-bold text-forest mb-6 border-b border-charcoal/10 pb-4">Contact Info</h2>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-bold text-charcoal-light uppercase tracking-wide mb-1">Email</label>
                                <div class="text-base font-medium text-charcoal">demo@ngo.test</div>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-charcoal-light uppercase tracking-wide mb-1">Phone</label>
                                <div class="text-base font-medium text-charcoal">+91 98765 43210</div>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-charcoal-light uppercase tracking-wide mb-1">Website</label>
                                <a href="#" class="text-base font-medium text-gold hover:text-forest transition-colors">https://sevaroots.example</a>
                            </div>
                        </div>
                    </div>

                    <!-- ADDRESS -->
                    <div class="bg-white rounded-3xl p-4 md:p-8 shadow-sm border border-gold/20">
                        <h2 class="text-xl font-serif font-bold text-forest mb-6 border-b border-charcoal/10 pb-4">Address</h2>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-bold text-charcoal-light uppercase tracking-wide mb-1">Street</label>
                                <div class="text-base font-medium text-charcoal">12 Community Road</div>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-charcoal-light uppercase tracking-wide mb-1">City</label>
                                <div class="text-base font-medium text-charcoal">Rajkot</div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-charcoal-light uppercase tracking-wide mb-1">State</label>
                                    <div class="text-base font-medium text-charcoal">Gujarat</div>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-charcoal-light uppercase tracking-wide mb-1">Pincode</label>
                                    <div class="text-base font-medium text-charcoal">360001</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- IMPACT INFORMATION -->
                <div class="bg-white rounded-3xl p-4 md:p-8 shadow-sm border border-gold/20">
                    <h2 class="text-xl md:text-2xl font-serif font-bold text-forest mb-6 border-b border-charcoal/10 pb-4">Impact Information</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="bg-ivory/30 p-4 rounded-xl border border-charcoal/5 text-center">
                            <div class="text-sm font-bold text-charcoal-light uppercase tracking-wide mb-1">People Helped</div>
                            <div class="text-xl md:text-3xl font-serif font-bold text-forest">15,000+</div>
                        </div>
                        <div class="bg-ivory/30 p-4 rounded-xl border border-charcoal/5 text-center">
                            <div class="text-sm font-bold text-charcoal-light uppercase tracking-wide mb-1">Projects Completed</div>
                            <div class="text-xl md:text-3xl font-serif font-bold text-forest">42</div>
                        </div>
                        <div class="bg-ivory/30 p-4 rounded-xl border border-charcoal/5 text-center">
                            <div class="text-sm font-bold text-charcoal-light uppercase tracking-wide mb-1">Areas Served</div>
                            <div class="text-xl md:text-3xl font-serif font-bold text-forest">5</div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- SIDEBAR CONTENT -->
            <div class="space-y-6 lg:space-y-8">
                
                <!-- PROFILE COMPLETION -->
                <div class="bg-white rounded-3xl p-4 md:p-6 shadow-sm border border-gold/20">
                    <h3 class="text-lg font-serif font-bold text-forest mb-4">Profile Completion</h3>
                    
                    <div class="flex items-end justify-between mb-2">
                        <span class="text-sm font-bold text-charcoal">Overall Status</span>
                        <span class="text-xl font-bold text-forest">85%</span>
                    </div>
                    
                    <div class="w-full bg-ivory rounded-full h-2.5 mb-6 overflow-hidden border border-charcoal/5">
                        <div class="bg-forest h-2.5 rounded-full" style="width: 85%"></div>
                    </div>
                    
                    <div class="space-y-3">
                        <div class="flex items-center text-sm">
                            <svg class="w-5 h-5 text-forest mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <span class="text-charcoal-light">Organization information</span>
                        </div>
                        <div class="flex items-center text-sm">
                            <svg class="w-5 h-5 text-forest mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <span class="text-charcoal-light">Contact information</span>
                        </div>
                        <div class="flex items-center text-sm">
                            <svg class="w-5 h-5 text-forest mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <span class="text-charcoal-light">Address & Documents</span>
                        </div>
                        <div class="flex items-center text-sm opacity-60">
                            <div class="w-5 h-5 rounded-full border-2 border-charcoal/30 mr-2 flex-shrink-0"></div>
                            <span class="text-charcoal-light">Impact information</span>
                        </div>
                    </div>
                </div>

                <!-- VERIFICATION STATUS -->
                <div class="bg-gradient-to-br from-white to-ivory rounded-3xl p-4 md:p-6 shadow-sm border border-gold/20 relative overflow-hidden">
                    <div class="absolute -right-4 -top-4 w-24 h-24 bg-forest/5 rounded-full blur-2xl pointer-events-none"></div>
                    <h3 class="text-xl font-serif font-bold text-forest mb-4 relative z-10">Verification Status</h3>
                    <div class="flex items-start space-x-4 relative z-10">
                        <div class="w-12 h-12 rounded-full bg-forest text-white flex items-center justify-center shadow-md flex-shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <div>
                            <h4 class="text-base font-bold text-charcoal">Verified NGO</h4>
                            <p class="text-sm text-charcoal-light leading-relaxed">Your organization has been verified by KarmaSetu.</p>
                        </div>
                    </div>
                </div>

                <!-- VERIFICATION DOCUMENTS -->
                <div class="bg-white rounded-3xl p-4 md:p-6 shadow-sm border border-gold/20">
                    <h3 class="text-lg font-serif font-bold text-forest mb-4">Verification Documents</h3>
                    
                    <div class="space-y-4">
                        <div class="flex flex-wrap items-center justify-between gap-4 p-3 bg-ivory/30 rounded-xl border border-charcoal/5">
                            <div class="flex items-center">
                                <svg class="w-8 h-8 text-charcoal/40 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                <div>
                                    <p class="text-sm font-bold text-charcoal">Registration Certificate</p>
                                    <p class="text-xs text-forest font-medium mt-0.5 flex items-center">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        Verified
                                    </p>
                                </div>
                            </div>
                            <button class="text-xs font-bold text-gold hover:text-forest transition-colors">View</button>
                        </div>

                        <div class="flex flex-wrap items-center justify-between gap-4 p-3 bg-ivory/30 rounded-xl border border-charcoal/5">
                            <div class="flex items-center">
                                <svg class="w-8 h-8 text-charcoal/40 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path></svg>
                                <div>
                                    <p class="text-sm font-bold text-charcoal">NGO Identity Document</p>
                                    <p class="text-xs text-forest font-medium mt-0.5 flex items-center">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        Verified
                                    </p>
                                </div>
                            </div>
                            <button class="text-xs font-bold text-gold hover:text-forest transition-colors">View</button>
                        </div>

                        <div class="flex flex-wrap items-center justify-between gap-4 p-3 bg-ivory/30 rounded-xl border border-charcoal/5">
                            <div class="flex items-center">
                                <svg class="w-8 h-8 text-charcoal/40 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                                <div>
                                    <p class="text-sm font-bold text-charcoal">Address Proof</p>
                                    <p class="text-xs text-forest font-medium mt-0.5 flex items-center">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        Verified
                                    </p>
                                </div>
                            </div>
                            <button class="text-xs font-bold text-gold hover:text-forest transition-colors">View</button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../includes/layouts/main.php';
?>
