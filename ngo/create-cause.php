<?php
require_once __DIR__ . '/../includes/functions.php';
ob_start();
?>
<div class="min-h-screen bg-ivory py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-3xl mx-auto">
        <div class="mb-8">
            <a href="<?= baseUrl('ngo/causes.php') ?>" class="text-sm font-bold text-gold hover:text-forest transition-colors flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Back to Causes
            </a>
        </div>
        
        <div class="bg-white p-6 md:p-10 rounded-3xl shadow-sm border border-gold/20">
            <h1 class="text-xl md:text-3xl font-serif font-bold text-forest mb-2">Create New Cause</h1>
            <p class="text-charcoal-light mb-8">Start a new fundraising campaign for your organization.</p>
            
            <form id="causeForm" class="space-y-6">
                <!-- Success Message -->
                <div id="successMsg" class="hidden p-4 rounded-xl bg-forest/10 border border-forest text-forest font-bold text-sm mb-6">
                    Cause created successfully!
                </div>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-bold text-charcoal mb-1">Cause Title <span class="text-red-500">*</span></label>
                        <input type="text" id="title" required class="w-full px-4 py-3 border border-charcoal/20 rounded-xl text-charcoal placeholder-charcoal/40 focus:outline-none focus:ring-2 focus:ring-forest focus:border-transparent transition-all bg-ivory/30 focus:bg-white" placeholder="e.g. Clean Water Initiative" data-validate="required">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-bold text-charcoal mb-1">Category <span class="text-red-500">*</span></label>
                        <select id="category" required class="w-full px-4 py-3 border border-charcoal/20 rounded-xl text-charcoal focus:outline-none focus:ring-2 focus:ring-forest focus:border-transparent transition-all bg-ivory/30 focus:bg-white appearance-none" data-validate="required">
                            <option value="">Select a category</option>
                            <option value="education">Education</option>
                            <option value="health">Health & Nutrition</option>
                            <option value="environment">Environment</option>
                            <option value="community">Community Development</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-bold text-charcoal mb-1">Target Amount (₹) <span class="text-red-500">*</span></label>
                        <input type="number" id="target" required min="1" class="w-full px-4 py-3 border border-charcoal/20 rounded-xl text-charcoal placeholder-charcoal/40 focus:outline-none focus:ring-2 focus:ring-forest focus:border-transparent transition-all bg-ivory/30 focus:bg-white" placeholder="500000" data-validate="required numeric">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-bold text-charcoal mb-1">Short Description <span class="text-red-500">*</span></label>
                        <textarea id="short_desc" required rows="2" class="w-full px-4 py-3 border border-charcoal/20 rounded-xl text-charcoal placeholder-charcoal/40 focus:outline-none focus:ring-2 focus:ring-forest focus:border-transparent transition-all bg-ivory/30 focus:bg-white" placeholder="A brief summary of this cause..." data-validate="required"></textarea>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-bold text-charcoal mb-1">Detailed Description</label>
                        <textarea id="long_desc" rows="5" class="w-full px-4 py-3 border border-charcoal/20 rounded-xl text-charcoal placeholder-charcoal/40 focus:outline-none focus:ring-2 focus:ring-forest focus:border-transparent transition-all bg-ivory/30 focus:bg-white" placeholder="Provide full details about how the funds will be used..."></textarea>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-charcoal mb-1">Start Date</label>
                            <input type="date" id="start_date" class="w-full px-4 py-3 border border-charcoal/20 rounded-xl text-charcoal focus:outline-none focus:ring-2 focus:ring-forest focus:border-transparent transition-all bg-ivory/30 focus:bg-white">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-charcoal mb-1">End Date</label>
                            <input type="date" id="end_date" class="w-full px-4 py-3 border border-charcoal/20 rounded-xl text-charcoal focus:outline-none focus:ring-2 focus:ring-forest focus:border-transparent transition-all bg-ivory/30 focus:bg-white">
                        </div>
                    </div>
                </div>
                
                <div class="flex flex-col md:flex-row flex-wrap gap-4 pt-4 border-t border-charcoal/10 mt-8">
                    <a href="<?= baseUrl('ngo/causes.php') ?>" class="flex-1 text-center bg-ivory text-charcoal font-bold py-3 rounded-xl hover:bg-charcoal/5 transition-colors border border-charcoal/20">Cancel</a>
                    <button type="submit" class="flex-1 text-center bg-forest text-white font-bold py-3 rounded-xl hover:bg-forest-dark transition-colors shadow-md">Create Cause</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('causeForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            document.getElementById('successMsg').classList.remove('hidden');
            window.scrollTo({top: 0, behavior: 'smooth'});
            form.reset();
            setTimeout(() => {
                document.getElementById('successMsg').classList.add('hidden');
            }, 5000);
        });
    }
});
</script>
<?php
$content = ob_get_clean();
require_once __DIR__ . '/../includes/layouts/main.php';
?>
