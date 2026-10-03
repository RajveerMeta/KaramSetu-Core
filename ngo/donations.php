<?php
require_once __DIR__ . '/../includes/functions.php';
ob_start();
?>
<div class="min-h-screen bg-ivory py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        
        <!-- HEADER -->
        <div class="mb-10">
            <h1 class="text-2xl md:text-4xl font-serif font-bold text-forest mb-2">NGO Contributions</h1>
            <p class="text-charcoal-light">View and manage contributions received by your organization.</p>
        </div>

        <!-- SUMMARY CARDS -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:p-6 mb-10">
            <div class="bg-white rounded-3xl p-4 md:p-6 shadow-sm border border-gold/20 flex flex-col justify-center items-center text-center">
                <div class="w-12 h-12 bg-forest/10 rounded-full flex items-center justify-center mb-3">
                    <svg class="w-6 h-6 text-forest" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                </div>
                <h3 class="text-sm font-bold text-charcoal-light uppercase tracking-wider mb-1">Total Contributions</h3>
                <p class="text-xl md:text-3xl font-serif font-bold text-forest">128</p>
            </div>
            
            <div class="bg-white rounded-3xl p-4 md:p-6 shadow-sm border border-gold/20 flex flex-col justify-center items-center text-center">
                <div class="w-12 h-12 bg-forest/10 rounded-full flex items-center justify-center mb-3">
                    <svg class="w-6 h-6 text-forest" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <h3 class="text-sm font-bold text-charcoal-light uppercase tracking-wider mb-1">Money Received</h3>
                <p class="text-xl md:text-3xl font-serif font-bold text-forest">₹3,75,000</p>
            </div>
            
            <div class="bg-white rounded-3xl p-4 md:p-6 shadow-sm border border-gold/20 flex flex-col justify-center items-center text-center">
                <div class="w-12 h-12 bg-forest/10 rounded-full flex items-center justify-center mb-3">
                    <svg class="w-6 h-6 text-forest" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
                <h3 class="text-sm font-bold text-charcoal-light uppercase tracking-wider mb-1">Food Contributions</h3>
                <p class="text-xl md:text-3xl font-serif font-bold text-forest">25 kg</p>
            </div>
            
            <div class="bg-white rounded-3xl p-4 md:p-6 shadow-sm border border-gold/20 flex flex-col justify-center items-center text-center">
                <div class="w-12 h-12 bg-forest/10 rounded-full flex items-center justify-center mb-3">
                    <svg class="w-6 h-6 text-forest" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                </div>
                <h3 class="text-sm font-bold text-charcoal-light uppercase tracking-wider mb-1">Item Contributions</h3>
                <p class="text-xl md:text-3xl font-serif font-bold text-forest">25 items</p>
            </div>
        </div>

        <!-- MAIN CONTENT AREA -->
        <div class="bg-white rounded-3xl shadow-sm border border-gold/20 overflow-hidden">
            
            <!-- FILTERS -->
            <div class="p-4 md:p-8 border-b border-charcoal/10 bg-ivory/30">
                <div class="flex flex-col md:flex-row gap-4 items-end">
                    <div class="w-full md:w-1/3">
                        <label class="block text-sm font-bold text-charcoal mb-2">Contribution Type</label>
                        <select id="typeFilter" class="w-full px-4 py-2 border border-charcoal/20 rounded-xl text-charcoal focus:outline-none focus:ring-2 focus:ring-forest transition-all bg-white appearance-none">
                            <option value="all">All</option>
                            <option value="money">Money</option>
                            <option value="food">Food</option>
                            <option value="clothes">Clothes</option>
                            <option value="useful_items">Useful Items</option>
                            <option value="volunteer_time">Volunteer Time</option>
                        </select>
                    </div>
                    <div class="w-full md:w-1/3">
                        <label class="block text-sm font-bold text-charcoal mb-2">Status</label>
                        <select id="statusFilter" class="w-full px-4 py-2 border border-charcoal/20 rounded-xl text-charcoal focus:outline-none focus:ring-2 focus:ring-forest transition-all bg-white appearance-none">
                            <option value="all">All</option>
                            <option value="received">Received</option>
                            <option value="pending">Pending</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- CONTRIBUTIONS LIST -->
            <div class="p-4 md:p-8">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse min-w-[800px]">
                        <thead>
                            <tr class="border-b-2 border-charcoal/10 text-charcoal font-bold text-sm uppercase tracking-wider">
                                <th class="pb-4 pl-2">Contributor</th>
                                <th class="pb-4">Type</th>
                                <th class="pb-4">Date</th>
                                <th class="pb-4">Amount / Quantity</th>
                                <th class="pb-4">Status</th>
                            </tr>
                        </thead>
                        <tbody id="contributionsList">
                            <tr class="border-b border-charcoal/5 hover:bg-ivory/50 transition-colors" data-type="money" data-status="received">
                                <td class="py-4 pl-2">
                                    <div class="font-bold text-charcoal">Rajveer Meta</div>
                                </td>
                                <td class="py-4">
                                    <span class="inline-block px-3 py-1 bg-blue-100 text-blue-700 text-xs font-bold rounded-full">Money</span>
                                </td>
                                <td class="py-4 text-sm text-charcoal">16 Sep 2026</td>
                                <td class="py-4 font-bold text-forest">₹2,000</td>
                                <td class="py-4">
                                    <span class="inline-block px-3 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-full">Received</span>
                                </td>
                            </tr>
                            
                            <tr class="border-b border-charcoal/5 hover:bg-ivory/50 transition-colors" data-type="food" data-status="received">
                                <td class="py-4 pl-2">
                                    <div class="font-bold text-charcoal">Anonymous</div>
                                </td>
                                <td class="py-4">
                                    <span class="inline-block px-3 py-1 bg-orange-100 text-orange-700 text-xs font-bold rounded-full">Food</span>
                                </td>
                                <td class="py-4 text-sm text-charcoal">15 Sep 2026</td>
                                <td class="py-4 font-bold text-forest">25 kg</td>
                                <td class="py-4">
                                    <span class="inline-block px-3 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-full">Received</span>
                                </td>
                            </tr>
                            
                            <tr class="border-b border-charcoal/5 hover:bg-ivory/50 transition-colors" data-type="clothes" data-status="received">
                                <td class="py-4 pl-2">
                                    <div class="font-bold text-charcoal">Amit Patel</div>
                                </td>
                                <td class="py-4">
                                    <span class="inline-block px-3 py-1 bg-purple-100 text-purple-700 text-xs font-bold rounded-full">Clothes</span>
                                </td>
                                <td class="py-4 text-sm text-charcoal">16 Sep 2026</td>
                                <td class="py-4 font-bold text-forest">15 items</td>
                                <td class="py-4">
                                    <span class="inline-block px-3 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-full">Received</span>
                                </td>
                            </tr>
                            
                            <tr class="border-b border-charcoal/5 hover:bg-ivory/50 transition-colors" data-type="useful_items" data-status="received">
                                <td class="py-4 pl-2">
                                    <div class="font-bold text-charcoal">Priya Singh</div>
                                </td>
                                <td class="py-4">
                                    <span class="inline-block px-3 py-1 bg-teal-100 text-teal-700 text-xs font-bold rounded-full">Useful Items</span>
                                </td>
                                <td class="py-4 text-sm text-charcoal">14 Sep 2026</td>
                                <td class="py-4 font-bold text-forest">10 items</td>
                                <td class="py-4">
                                    <span class="inline-block px-3 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-full">Received</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <div id="noResults" class="hidden text-center py-10 text-charcoal-light">
                    No contributions match the selected filters.
                </div>
            </div>
        </div>

    </div>
</div>



<?php
$content = ob_get_clean();
require_once __DIR__ . '/../includes/layouts/main.php';
?>


