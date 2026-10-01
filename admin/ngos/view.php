<?php
require_once __DIR__ . '/../../includes/functions.php';

ob_start();
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <!-- Page Header & Back Link -->
    <div class="mb-6">
        <a href="<?= baseUrl('admin/ngos/index.php') ?>" class="inline-flex items-center text-sm font-medium text-forest hover:text-gold transition-colors mb-4">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Back to NGOs
        </a>
        <div class="flex flex-col md:flex-row md:items-end justify-between">
            <div>
                <h1 class="text-3xl font-serif font-bold text-forest mb-2">NGO Details</h1>
                <p class="text-charcoal-light">Review the organization's information and submitted documents.</p>
            </div>
            
            <div class="mt-4 md:mt-0 flex items-center space-x-3">
                <span id="headerStatusBadge" class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-amber-100 text-amber-800 border border-amber-200 shadow-sm">
                    Pending
                </span>
            </div>
        </div>
    </div>

    <!-- Alert Container for Success Messages -->
    <div id="alertContainer" class="hidden mb-6 p-4 rounded-lg bg-green-50 border border-green-200 flex items-center">
        <svg id="alertIcon" class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
        <span id="alertMessage" class="text-sm font-medium text-green-800"></span>
    </div>

    <!-- Two Column Layout for Info -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        
        <!-- Organization Info -->
        <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm overflow-hidden flex flex-col h-full">
            <div class="px-6 py-4 border-b border-charcoal/10 bg-ivory/30">
                <h2 class="text-lg font-serif font-bold text-forest">Organization Information</h2>
            </div>
            <div class="p-6 flex-grow">
                <dl class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-3">
                        <dt class="text-sm font-medium text-charcoal-light sm:col-span-1">NGO Name</dt>
                        <dd class="text-sm font-medium text-charcoal sm:col-span-2">Seva Roots Initiative</dd>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3">
                        <dt class="text-sm font-medium text-charcoal-light sm:col-span-1">Registration Number</dt>
                        <dd class="text-sm text-charcoal sm:col-span-2">NGO/GJ/RJK/2026/084</dd>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3">
                        <dt class="text-sm font-medium text-charcoal-light sm:col-span-1">Organization Type</dt>
                        <dd class="text-sm text-charcoal sm:col-span-2">Non-Profit Organization</dd>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3">
                        <dt class="text-sm font-medium text-charcoal-light sm:col-span-1">Founded</dt>
                        <dd class="text-sm text-charcoal sm:col-span-2">2021</dd>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3">
                        <dt class="text-sm font-medium text-charcoal-light sm:col-span-1">Location</dt>
                        <dd class="text-sm text-charcoal sm:col-span-2">Rajkot, Gujarat</dd>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3">
                        <dt class="text-sm font-medium text-charcoal-light sm:col-span-1">Email</dt>
                        <dd class="text-sm text-charcoal sm:col-span-2"><a href="mailto:contact@sevaroots.org" class="text-forest hover:text-gold transition-colors">contact@sevaroots.org</a></dd>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3">
                        <dt class="text-sm font-medium text-charcoal-light sm:col-span-1">Phone</dt>
                        <dd class="text-sm text-charcoal sm:col-span-2">+91 98765 43210</dd>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3">
                        <dt class="text-sm font-medium text-charcoal-light sm:col-span-1">Website</dt>
                        <dd class="text-sm text-charcoal sm:col-span-2"><a href="http://www.sevaroots.org" target="_blank" rel="noopener noreferrer" class="text-forest hover:text-gold transition-colors">www.sevaroots.org</a></dd>
                    </div>
                </dl>
            </div>
        </div>

        <div class="space-y-6 flex flex-col h-full">
            <!-- Primary Contact -->
            <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm overflow-hidden flex-1">
                <div class="px-6 py-4 border-b border-charcoal/10 bg-ivory/30">
                    <h2 class="text-lg font-serif font-bold text-forest">Primary Contact</h2>
                </div>
                <div class="p-6">
                    <dl class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-3">
                            <dt class="text-sm font-medium text-charcoal-light sm:col-span-1">Name</dt>
                            <dd class="text-sm font-medium text-charcoal sm:col-span-2">Mehul Shah</dd>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-3">
                            <dt class="text-sm font-medium text-charcoal-light sm:col-span-1">Position</dt>
                            <dd class="text-sm text-charcoal sm:col-span-2">Founder & Director</dd>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-3">
                            <dt class="text-sm font-medium text-charcoal-light sm:col-span-1">Email</dt>
                            <dd class="text-sm text-charcoal sm:col-span-2"><a href="mailto:mehul@sevaroots.org" class="text-forest hover:text-gold transition-colors">mehul@sevaroots.org</a></dd>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-3">
                            <dt class="text-sm font-medium text-charcoal-light sm:col-span-1">Phone</dt>
                            <dd class="text-sm text-charcoal sm:col-span-2">+91 98765 43210</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- About -->
            <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm overflow-hidden flex-1">
                <div class="px-6 py-4 border-b border-charcoal/10 bg-ivory/30">
                    <h2 class="text-lg font-serif font-bold text-forest">About the Organization</h2>
                </div>
                <div class="p-6">
                    <p class="text-sm text-charcoal leading-relaxed mb-4">
                        Seva Roots Initiative works with local communities to support education, food assistance, and community development programs across Rajkot and nearby areas.
                    </p>
                    <div class="flex flex-wrap gap-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-forest/10 text-forest border border-forest/20">Education</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-forest/10 text-forest border border-forest/20">Community Development</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-forest/10 text-forest border border-forest/20">Food & Nutrition</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-forest/10 text-forest border border-forest/20">Volunteer Support</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Submitted Documents -->
    <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm overflow-hidden mb-8">
        <div class="px-6 py-4 border-b border-charcoal/10 flex justify-between items-center bg-ivory/30">
            <h2 class="text-lg font-serif font-bold text-forest">Submitted Documents</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-ivory/10 text-charcoal-light text-sm border-b border-charcoal/10">
                        <th class="px-6 py-3 font-medium">Document</th>
                        <th class="px-6 py-3 font-medium">Document Number</th>
                        <th class="px-6 py-3 font-medium">Status</th>
                        <th class="px-6 py-3 font-medium text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-charcoal/5">
                    <tr class="hover:bg-ivory/30 transition-colors">
                        <td class="px-6 py-4 text-sm font-medium text-charcoal">Registration Certificate</td>
                        <td class="px-6 py-4 text-sm text-charcoal-light font-mono">REG-GJ-2021-084</td>
                        <td class="px-6 py-4 text-sm">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200">Submitted</span>
                        </td>
                        <td class="px-6 py-4 text-sm text-right">
                            <button type="button" class="text-forest font-medium hover:text-gold transition-colors text-sm">View</button>
                        </td>
                    </tr>
                    <tr class="hover:bg-ivory/30 transition-colors">
                        <td class="px-6 py-4 text-sm font-medium text-charcoal">PAN Certificate</td>
                        <td class="px-6 py-4 text-sm text-charcoal-light font-mono">AAAAA1234A</td>
                        <td class="px-6 py-4 text-sm">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200">Submitted</span>
                        </td>
                        <td class="px-6 py-4 text-sm text-right">
                            <button type="button" class="text-forest font-medium hover:text-gold transition-colors text-sm">View</button>
                        </td>
                    </tr>
                    <tr class="hover:bg-ivory/30 transition-colors">
                        <td class="px-6 py-4 text-sm font-medium text-charcoal">Trust Deed</td>
                        <td class="px-6 py-4 text-sm text-charcoal-light font-mono">TD-RJK-2021-084</td>
                        <td class="px-6 py-4 text-sm">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200">Submitted</span>
                        </td>
                        <td class="px-6 py-4 text-sm text-right">
                            <button type="button" class="text-forest font-medium hover:text-gold transition-colors text-sm">View</button>
                        </td>
                    </tr>
                    <tr class="hover:bg-ivory/30 transition-colors">
                        <td class="px-6 py-4 text-sm font-medium text-charcoal">Address Proof</td>
                        <td class="px-6 py-4 text-sm text-charcoal-light font-mono">ADD-RJK-084</td>
                        <td class="px-6 py-4 text-sm">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200">Submitted</span>
                        </td>
                        <td class="px-6 py-4 text-sm text-right">
                            <button type="button" class="text-forest font-medium hover:text-gold transition-colors text-sm">View</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Verification Status & Actions -->
    <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm p-6 mb-8 flex flex-col md:flex-row md:items-center justify-between">
        <div class="mb-6 md:mb-0 max-w-2xl">
            <h2 class="text-lg font-serif font-bold text-forest mb-2">Verification Status</h2>
            <p class="text-sm text-charcoal leading-relaxed">
                This NGO has submitted its registration information and documents and is waiting for administrative verification.
            </p>
        </div>
        
        <div id="actionButtonsContainer" class="flex flex-col sm:flex-row sm:space-x-3 space-y-3 sm:space-y-0">
            <button type="button" id="btnRejectInit" class="px-6 py-2.5 bg-white border border-red-200 text-red-600 hover:bg-red-50 hover:border-red-300 font-medium rounded-md transition-colors shadow-sm text-sm">
                Reject NGO
            </button>
            <button type="button" id="btnApproveInit" class="px-6 py-2.5 bg-forest text-ivory hover:bg-forest-dark font-medium rounded-md transition-colors shadow-sm text-sm">
                Approve NGO
            </button>
        </div>
    </div>

</div>

<!-- Approve Modal -->
<div id="approveModal" class="fixed inset-0 z-[100] hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-charcoal/60 backdrop-blur-sm transition-opacity" aria-hidden="true" id="approveOverlay"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        
        <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-charcoal/10">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg leading-6 font-medium text-forest font-serif" id="modal-title">Approve Seva Roots Initiative?</h3>
                        <div class="mt-2">
                            <p class="text-sm text-charcoal-light">This will mark the NGO as approved. They will be notified and granted full access to the platform.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-ivory/50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-charcoal/10">
                <button type="button" id="btnConfirmApprove" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-forest text-base font-medium text-ivory hover:bg-forest-dark focus:outline-none sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                    Approve
                </button>
                <button type="button" id="btnCancelApprove" class="mt-3 w-full inline-flex justify-center rounded-md border border-charcoal/20 shadow-sm px-4 py-2 bg-white text-base font-medium text-charcoal hover:bg-ivory focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="fixed inset-0 z-[100] hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-charcoal/60 backdrop-blur-sm transition-opacity" aria-hidden="true" id="rejectOverlay"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        
        <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-charcoal/10">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-medium text-red-800 font-serif" id="modal-title">Reject Seva Roots Initiative?</h3>
                        <div class="mt-2">
                            <p class="text-sm text-charcoal-light mb-4">Please provide a reason for rejection. This will be sent to the NGO.</p>
                            
                            <div class="w-full">
                                <label for="rejectionReason" class="block text-sm font-medium text-charcoal mb-1">Reason for rejection <span class="text-red-500">*</span></label>
                                <textarea id="rejectionReason" rows="3" class="w-full px-3 py-2 border border-charcoal/20 rounded-md focus:outline-none focus:ring-1 focus:ring-red-500 text-sm" placeholder="Explain why the application is being rejected..."></textarea>
                                <p id="rejectionError" class="mt-1 text-sm text-red-600 hidden">Reason cannot be empty.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-ivory/50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-charcoal/10">
                <button type="button" id="btnConfirmReject" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                    Reject NGO
                </button>
                <button type="button" id="btnCancelReject" class="mt-3 w-full inline-flex justify-center rounded-md border border-charcoal/20 shadow-sm px-4 py-2 bg-white text-base font-medium text-charcoal hover:bg-ivory focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        const approveModal = document.getElementById('approveModal');
        const rejectModal = document.getElementById('rejectModal');
        
        const btnApproveInit = document.getElementById('btnApproveInit');
        const btnRejectInit = document.getElementById('btnRejectInit');
        const actionButtonsContainer = document.getElementById('actionButtonsContainer');
        
        const headerStatusBadge = document.getElementById('headerStatusBadge');
        const alertContainer = document.getElementById('alertContainer');
        const alertMessage = document.getElementById('alertMessage');
        const alertIcon = document.getElementById('alertIcon');
        
        if (btnApproveInit) {
            btnApproveInit.addEventListener('click', () => {
                approveModal.classList.remove('hidden');
            });
        }
        
        document.getElementById('btnCancelApprove').addEventListener('click', () => {
            approveModal.classList.add('hidden');
        });
        
        document.getElementById('approveOverlay').addEventListener('click', () => {
            approveModal.classList.add('hidden');
        });
        
        document.getElementById('btnConfirmApprove').addEventListener('click', () => {
            approveModal.classList.add('hidden');
            
            headerStatusBadge.textContent = 'Approved';
            headerStatusBadge.className = 'inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800 border border-green-200 shadow-sm';
            
            actionButtonsContainer.innerHTML = '<span class="text-sm font-medium text-charcoal-light flex items-center"><svg class="w-4 h-4 text-green-500 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg> Action Completed</span>';
            
            alertMessage.textContent = 'NGO approved successfully.';
            alertContainer.className = 'mb-6 p-4 rounded-lg bg-green-50 border border-green-200 flex items-center';
            alertIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>';
            alertIcon.className = 'w-5 h-5 text-green-500 mr-3';
            alertContainer.classList.remove('hidden');
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
        
        if (btnRejectInit) {
            btnRejectInit.addEventListener('click', () => {
                rejectModal.classList.remove('hidden');
                document.getElementById('rejectionReason').value = '';
                document.getElementById('rejectionError').classList.add('hidden');
                document.getElementById('rejectionReason').classList.remove('border-red-500');
            });
        }
        
        document.getElementById('btnCancelReject').addEventListener('click', () => {
            rejectModal.classList.add('hidden');
        });
        
        document.getElementById('rejectOverlay').addEventListener('click', () => {
            rejectModal.classList.add('hidden');
        });
        
        document.getElementById('btnConfirmReject').addEventListener('click', () => {
            const reason = document.getElementById('rejectionReason').value.trim();
            const errorMsg = document.getElementById('rejectionError');
            const reasonInput = document.getElementById('rejectionReason');
            
            if (!reason) {
                errorMsg.classList.remove('hidden');
                reasonInput.classList.add('border-red-500');
                return;
            }
            
            rejectModal.classList.add('hidden');
            
            headerStatusBadge.textContent = 'Rejected';
            headerStatusBadge.className = 'inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800 border border-red-200 shadow-sm';
            
            actionButtonsContainer.innerHTML = '<span class="text-sm font-medium text-charcoal-light flex items-center"><svg class="w-4 h-4 text-red-500 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg> Action Completed</span>';
            
            alertMessage.textContent = 'NGO rejected successfully.';
            alertContainer.className = 'mb-6 p-4 rounded-lg bg-red-50 border border-red-200 flex items-center';
            alertIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>';
            alertIcon.className = 'w-5 h-5 text-red-500 mr-3';
            alertContainer.classList.remove('hidden');
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
        
        document.getElementById('rejectionReason').addEventListener('input', function() {
            document.getElementById('rejectionError').classList.add('hidden');
            this.classList.remove('border-red-500');
        });
    });
</script>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../includes/layouts/admin.php';
?>
