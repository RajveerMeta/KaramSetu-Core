<?php
require_once __DIR__ . '/../includes/functions.php';


$status = $_GET['status'] ?? 'pending';

ob_start();
?>


<div class="min-h-screen bg-ivory py-16 px-4 sm:px-6 lg:px-8">
    <div class="max-w-5xl mx-auto space-y-12">
        
        <!-- Header -->
        <div class="text-center">
            <h1 class="text-3xl md:text-4xl md:text-5xl font-serif font-bold text-forest mb-4">NGO Application Status</h1>
            <p class="text-charcoal-light text-lg max-w-2xl mx-auto">Track the status of your NGO registration and verification application.</p>
        </div>

        <!-- Status Section -->
        <div class="bg-white rounded-3xl shadow-sm border border-gold/20 overflow-hidden relative">
            <?php if($status === 'pending'): ?>
                <div class="absolute top-0 left-0 w-full h-2 bg-yellow-400"></div>
            <?php elseif($status === 'approved'): ?>
                <div class="absolute top-0 left-0 w-full h-2 bg-forest"></div>
            <?php elseif($status === 'rejected'): ?>
                <div class="absolute top-0 left-0 w-full h-2 bg-red-500"></div>
            <?php endif; ?>

            <div class="p-4 md:p-6 md:p-10 border-b border-charcoal/10">
                <div class="flex flex-col md:flex-row items-center justify-between gap-4 md:p-6 text-center md:text-left">
                    <div>
                        <?php if($status === 'pending'): ?>
                            <h2 class="text-xl md:text-3xl font-serif font-bold text-forest mb-2">Pending Review</h2>
                            <p class="text-charcoal-light text-sm max-w-xl">Your application has been submitted successfully and is currently being reviewed by the KarmaSetu administration team.</p>
                        <?php elseif($status === 'approved'): ?>
                            <h2 class="text-xl md:text-3xl font-serif font-bold text-forest mb-2">Application Approved</h2>
                            <p class="text-charcoal-light text-sm max-w-xl">Congratulations! Your NGO has been verified. You can now access your NGO dashboard.</p>
                        <?php elseif($status === 'rejected'): ?>
                            <h2 class="text-xl md:text-3xl font-serif font-bold text-red-600 mb-2">Application Requires Changes</h2>
                            <p class="text-charcoal-light text-sm max-w-xl">Please review the feedback below and update your application.</p>
                        <?php endif; ?>
                    </div>
                    
                    <div>
                        <?php if($status === 'pending'): ?>
                            <div class="inline-flex flex-wrap items-center gap-2 bg-yellow-50 px-5 py-2.5 rounded-full border border-yellow-200">
                                <div class="w-2 h-2 rounded-full bg-yellow-400 animate-pulse"></div>
                                <span class="text-sm font-bold text-yellow-700 uppercase tracking-wider">Pending Review</span>
                            </div>
                        <?php elseif($status === 'approved'): ?>
                            <a href="#" class="inline-block bg-forest text-white px-8 py-3 rounded-full font-bold shadow-md hover:shadow-lg transition-all hover:bg-forest-dark">Go to Dashboard</a>
                        <?php elseif($status === 'rejected'): ?>
                            <button type="button" class="inline-block bg-red-600 text-white px-8 py-3 rounded-full font-bold shadow-md hover:shadow-lg transition-all hover:bg-red-700">Update Application</button>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if($status === 'rejected' && $application['rejection_reason']): ?>
                    <div class="mt-8 bg-red-50 p-4 md:p-6 rounded-xl border border-red-100">
                        <h3 class="text-red-800 font-bold mb-2">Reviewer Feedback:</h3>
                        <p class="text-red-700 text-sm">The submitted address proof document is illegible. Please provide a clearer scanned copy of the document.</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Application Summary -->
            <div class="p-4 md:p-6 md:p-10 bg-ivory/30">
                <h3 class="text-sm font-bold text-charcoal uppercase tracking-wider mb-6">Application Summary</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-y-6 gap-x-8">
                    <div>
                        <p class="text-xs text-charcoal/60 mb-1">Organization</p>
                        <p class="text-sm font-bold text-forest">Seva Roots Initiative</p>
                    </div>
                    <div>
                        <p class="text-xs text-charcoal/60 mb-1">Application ID</p>
                        <p class="text-sm font-bold text-charcoal">NGO-001</p>
                    </div>
                    <div>
                        <p class="text-xs text-charcoal/60 mb-1">Application Date</p>
                        <p class="text-sm font-bold text-charcoal">19 September 2026</p>
                    </div>
                    <div>
                        <p class="text-xs text-charcoal/60 mb-1">NGO Type</p>
                        <p class="text-sm font-medium text-charcoal">Trust</p>
                    </div>
                    <div>
                        <p class="text-xs text-charcoal/60 mb-1">Primary Cause</p>
                        <p class="text-sm font-medium text-charcoal">Education</p>
                    </div>
                    <div>
                        <p class="text-xs text-charcoal/60 mb-1">Contact</p>
                        <p class="text-sm font-medium text-charcoal">Rajveer Meta <br> <span class="text-xs">contact@sevaroots.org</span></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Progress Tracker -->
        <div class="bg-white rounded-3xl shadow-sm border border-gold/20 p-4 md:p-6 md:p-10">
            <h3 class="text-sm font-bold text-charcoal uppercase tracking-wider mb-8 text-center md:text-left">Application Progress</h3>
            
            <div class="relative">
                <!-- Desktop Horizontal Line -->
                <div class="hidden md:block absolute top-4 md:p-6 left-12 right-12 h-1 bg-charcoal/10 -z-10"></div>
                <!-- Mobile Vertical Line -->
                <div class="md:hidden absolute top-4 md:p-6 bottom-6 left-6 w-1 bg-charcoal/10 -z-10"></div>

                <div class="flex flex-col md:flex-row justify-between space-y-8 md:space-y-0">
                    
                    <!-- Step 1 -->
                    <div class="flex md:flex-col items-center md:text-center gap-4 md:gap-3 relative z-10 w-full md:w-1/4">
                        <div class="w-12 h-12 rounded-full bg-forest text-white flex items-center justify-center shadow-md flex-shrink-0">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-forest">Application Submitted</p>
                        </div>
                    </div>

                    <!-- Step 2 -->
                    <div class="flex md:flex-col items-center md:text-center gap-4 md:gap-3 relative z-10 w-full md:w-1/4">
                        <div class="w-12 h-12 rounded-full bg-forest text-white flex items-center justify-center shadow-md flex-shrink-0">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-forest">Documents Submitted</p>
                        </div>
                    </div>

                    <!-- Step 3 -->
                    <div class="flex md:flex-col items-center md:text-center gap-4 md:gap-3 relative z-10 w-full md:w-1/4">
                        <?php if($status === 'pending'): ?>
                            <div class="w-12 h-12 rounded-full bg-yellow-400 text-white flex items-center justify-center shadow-md flex-shrink-0 border-4 border-white">
                                <div class="w-3 h-3 bg-white rounded-full"></div>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-yellow-600">Under Review</p>
                            </div>
                        <?php else: ?>
                            <div class="w-12 h-12 rounded-full <?= $status === 'rejected' ? 'bg-red-500' : 'bg-forest' ?> text-white flex items-center justify-center shadow-md flex-shrink-0">
                                <?php if($status === 'rejected'): ?>
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                <?php else: ?>
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                <?php endif; ?>
                            </div>
                            <div>
                                <p class="text-sm font-bold <?= $status === 'rejected' ? 'text-red-600' : 'text-forest' ?>">Review Complete</p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Step 4 -->
                    <div class="flex md:flex-col items-center md:text-center gap-4 md:gap-3 relative z-10 w-full md:w-1/4">
                        <?php if($status === 'approved'): ?>
                            <div class="w-12 h-12 rounded-full bg-forest text-white flex items-center justify-center shadow-md flex-shrink-0 border-4 border-white">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-forest">Approved</p>
                            </div>
                        <?php else: ?>
                            <div class="w-12 h-12 rounded-full bg-white border-2 border-charcoal/20 text-charcoal/40 flex items-center justify-center flex-shrink-0">
                            </div>
                            <div>
                                <p class="text-sm font-bold text-charcoal/40">Approved</p>
                            </div>
                        <?php endif; ?>
                    </div>

                </div>
            </div>
        </div>

        <div class="flex flex-col lg:grid lg:grid-cols-3 gap-8 w-full">
            <div class="lg:col-span-2 space-y-8">
                
                <!-- Submitted Information -->
                <div class="bg-white rounded-3xl shadow-sm border border-gold/20 p-4 md:p-6 md:p-10">
                    <h3 class="text-xl font-serif font-bold text-forest mb-6">Application Information</h3>
                    
                    <div class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:p-6 pb-6 border-b border-charcoal/10">
                            <div>
                                <p class="text-xs text-charcoal/60 mb-1">Registration Number</p>
                                <p class="text-sm font-medium text-charcoal">SRI/2026/89402</p>
                            </div>
                            <div>
                                <p class="text-xs text-charcoal/60 mb-1">Established Date</p>
                                <p class="text-sm font-medium text-charcoal">Not provided in dummy</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:p-6 pb-6 border-b border-charcoal/10">
                            <div>
                                <p class="text-xs text-charcoal/60 mb-1">Phone</p>
                                <p class="text-sm font-medium text-charcoal">+91 98765 43210</p>
                            </div>
                            <div>
                                <p class="text-xs text-charcoal/60 mb-1">Website</p>
                                <p class="text-sm font-medium text-charcoal text-forest">www.sevaroots.org</p>
                            </div>
                            <div class="md:col-span-2">
                                <p class="text-xs text-charcoal/60 mb-1">Address</p>
                                <p class="text-sm font-medium text-charcoal">123 Seva Marg, Ahmedabad, Gujarat - 380001</p>
                            </div>
                        </div>

                        <div>
                            <p class="text-xs text-charcoal/60 mb-1">Mission</p>
                            <p class="text-sm font-medium text-charcoal leading-relaxed">To empower underprivileged communities through sustainable education and healthcare.</p>
                        </div>
                        
                        <div>
                            <p class="text-xs text-charcoal/60 mb-1">Impact Description</p>
                            <p class="text-sm font-medium text-charcoal leading-relaxed">Supported over 5000 families in 2025 across 12 villages.</p>
                        </div>

                        <div>
                            <p class="text-xs text-charcoal/60 mb-2">Service Areas</p>
                            <div class="flex flex-wrap gap-2">
                                
                                    <span class="inline-block bg-ivory border border-gold/30 text-charcoal text-xs px-3 py-1.5 rounded-full">Gujarat</span>
                                
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Document Verification -->
                <div class="bg-white rounded-3xl shadow-sm border border-gold/20 p-4 md:p-6 md:p-10">
                    <h3 class="text-xl font-serif font-bold text-forest mb-6">Verification Documents</h3>

                    <div class="space-y-4">
                        <?php
                        $dummy_documents = [
                            ['name' => 'NGO Registration Certificate', 'type' => 'PDF Document', 'status' => 'Submitted'],
                            ['name' => 'PAN / Tax Document', 'type' => 'PDF Document', 'status' => 'Submitted'],
                            ['name' => 'Address Proof', 'type' => 'PDF Document', 'status' => 'Submitted']
                        ];
                        foreach($dummy_documents as $document):
                        ?>
                        <div class="flex flex-wrap items-center justify-between gap-4 p-4 bg-ivory/50 border border-charcoal/10 rounded-xl">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center text-forest shadow-sm border border-charcoal/5">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-charcoal"><?= htmlspecialchars($document['name']) ?></p>
                                    <p class="text-xs text-charcoal/60"><?= htmlspecialchars($document['type']) ?></p>
                                </div>
                            </div>
                            <span class="text-xs font-bold text-forest bg-forest/10 px-3 py-1 rounded-full uppercase tracking-wider"><?= htmlspecialchars($document['status']) ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

            </div>

            <!-- Sidebar -->
            <div class="space-y-8">
                <!-- Under Review Card -->
                <div class="bg-blue-50/50 rounded-3xl border border-blue-100 p-4 md:p-8">
                    <div class="flex items-start space-x-4">
                        <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div>
                            <h3 class="text-blue-900 font-bold mb-2">Under Review</h3>
                            <p class="text-sm text-blue-800/80 leading-relaxed">Our team is reviewing the information and documents submitted with your application. You will be notified when the review is complete.</p>
                        </div>
                    </div>
                </div>

                <!-- What Happens Next -->
                <div class="bg-white rounded-3xl shadow-sm border border-gold/20 p-4 md:p-8">
                    <h3 class="text-lg font-serif font-bold text-forest mb-6">What Happens Next?</h3>
                    
                    <div class="space-y-6">
                        <div class="flex flex-wrap gap-4">
                            <div class="w-8 h-8 rounded-full bg-ivory border border-gold/30 flex items-center justify-center text-sm font-bold text-forest flex-shrink-0">1</div>
                            <div>
                                <p class="text-sm font-bold text-charcoal mb-1">Application Review</p>
                                <p class="text-xs text-charcoal-light leading-relaxed">Our team verifies your organization details and documents.</p>
                            </div>
                        </div>
                        <div class="flex flex-wrap gap-4">
                            <div class="w-8 h-8 rounded-full bg-ivory border border-gold/30 flex items-center justify-center text-sm font-bold text-forest flex-shrink-0">2</div>
                            <div>
                                <p class="text-sm font-bold text-charcoal mb-1">Approval Decision</p>
                                <p class="text-xs text-charcoal-light leading-relaxed">The administration team approves or rejects the application after review.</p>
                            </div>
                        </div>
                        <div class="flex flex-wrap gap-4">
                            <div class="w-8 h-8 rounded-full bg-ivory border border-gold/30 flex items-center justify-center text-sm font-bold text-forest flex-shrink-0">3</div>
                            <div>
                                <p class="text-sm font-bold text-charcoal mb-1">NGO Profile Activation</p>
                                <p class="text-xs text-charcoal-light leading-relaxed">Once approved, your NGO profile and fundraising features can become available on KarmaSetu.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Help -->
                <div class="bg-forest text-white rounded-3xl shadow-sm p-4 md:p-8 text-center relative overflow-hidden">
                    <div class="absolute top-0 right-0 -mt-6 -mr-6 w-24 h-24 bg-white/10 rounded-full blur-xl z-0 pointer-events-none"></div>
                    <div class="relative z-10">
                        <h3 class="text-lg font-serif font-bold mb-3">Need Help?</h3>
                        <p class="text-sm text-ivory/80 mb-6 leading-relaxed">If you have questions about your application, you can contact the KarmaSetu support team.</p>
                        <a href="#" class="inline-block bg-white text-forest hover:bg-ivory px-6 py-3 rounded-full text-sm font-bold transition-all shadow-sm w-full">Contact Support</a>
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

