<?php
require_once __DIR__ . '/../includes/functions.php';
ob_start();
?>


<div class="min-h-screen bg-ivory py-16 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="text-center mb-12">
            <span class="text-gold font-bold tracking-wider text-sm uppercase mb-3 block">NGO Partnership</span>
            <h1 class="text-4xl md:text-5xl font-serif font-bold text-forest mb-4">Join KarmaSetu as a Verified NGO</h1>
            <p class="text-charcoal-light text-lg max-w-2xl mx-auto mb-4">Tell us about your organization, your mission, and the work you do. Our team will review your application before your NGO becomes visible to the KarmaSetu community.</p>
            <div class="inline-flex items-center space-x-2 bg-white px-4 py-2 rounded-full shadow-sm border border-gold/20">
                <svg class="w-5 h-5 text-gold" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                <span class="text-sm font-medium text-forest">Your application will be reviewed by the KarmaSetu administration team.</span>
            </div>
        </div>

        <!-- Success State (Hidden initially) -->
        <div id="ngoApplicationSuccess" class="hidden bg-white p-10 md:p-16 rounded-3xl shadow-sm border border-gold/20 text-center relative overflow-hidden">
            <div class="absolute top-0 right-0 -mt-8 -mr-8 w-32 h-32 bg-gold/10 rounded-full blur-2xl z-0 pointer-events-none"></div>
            <div class="absolute bottom-0 left-0 -mb-8 -ml-8 w-32 h-32 bg-forest/5 rounded-full blur-2xl z-0 pointer-events-none"></div>
            
            <div class="relative z-10">
                <div class="w-20 h-20 bg-forest/10 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-forest" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                </div>
                <h2 class="text-3xl font-serif font-bold text-forest mb-4">Application Submitted</h2>
                <p class="text-charcoal-light text-lg max-w-xl mx-auto mb-8">Thank you for applying to join KarmaSetu. Your application has been submitted successfully and is now pending administrative review.</p>
                
                <div class="inline-flex items-center space-x-2 bg-yellow-50 px-5 py-2.5 rounded-full border border-yellow-200 mb-8">
                    <div class="w-2 h-2 rounded-full bg-yellow-400 animate-pulse"></div>
                    <span class="text-sm font-bold text-yellow-700">Status: Pending Review</span>
                </div>
                
                <div>
                    <a href="/" class="inline-block bg-forest text-white hover:bg-forest-dark px-8 py-3 rounded-full font-bold transition-all shadow-md hover:shadow-lg">View Application Status</a>
                </div>
            </div>
        </div>

        <!-- Application Form -->
        <div id="ngoApplicationFormContainer" class="bg-white rounded-3xl shadow-sm border border-gold/20 overflow-hidden">
            <form id="ngoApplicationForm" action="#" method="POST" class="p-6 md:p-10 space-y-12">
                
                
                <!-- Section 01: Organization Details -->
                <section>
                    <div class="border-b border-charcoal/10 pb-4 mb-6">
                        <h2 class="text-2xl font-serif font-bold text-forest flex items-center">
                            <span class="bg-forest text-white text-sm w-8 h-8 rounded-full flex items-center justify-center mr-3 font-sans">01</span>
                            Organization Details
                        </h2>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label for="ngo_name" class="block text-sm font-bold text-charcoal mb-2">NGO / Organization Name *</label>
                            <input type="text" id="ngo_name" name="ngo_name" required data-no-name-val="true" class="w-full px-4 py-3 border border-charcoal/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-gold focus:border-gold transition-all bg-ivory/30 focus:bg-white text-charcoal" placeholder="Enter the registered name of your organization" data-validate="required">
                        </div>
                        
                        <div>
                            <label for="registration_number" class="block text-sm font-bold text-charcoal mb-2">Registration Number *</label>
                            <input type="text" id="registration_number" name="registration_number" required data-no-name-val="true" class="w-full px-4 py-3 border border-charcoal/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-gold focus:border-gold transition-all bg-ivory/30 focus:bg-white text-charcoal" placeholder="e.g., MAH/1234/Pune/2010" data-validate="required">
                        </div>
                        
                        <div>
                            <label for="established_date" class="block text-sm font-bold text-charcoal mb-2">Established Date *</label>
                            <input type="date" id="established_date" name="established_date" required class="w-full px-4 py-3 border border-charcoal/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-gold focus:border-gold transition-all bg-ivory/30 focus:bg-white text-charcoal" data-validate="required">
                        </div>
                        
                        <div>
                            <label for="ngo_type" class="block text-sm font-bold text-charcoal mb-2">NGO Type *</label>
                            <div class="relative">
                                <select id="ngo_type" name="ngo_type" required class="w-full px-4 py-3 border border-charcoal/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-gold focus:border-gold transition-all bg-ivory/30 focus:bg-white text-charcoal appearance-none" data-validate="required">
                                    <option value="" disabled selected>Select NGO Type</option>
                                    <option value="Trust">Trust</option>
                                    <option value="Society">Society</option>
                                    <option value="Section 8 Company">Section 8 Company</option>
                                    <option value="Non-Profit Organization">Non-Profit Organization</option>
                                    <option value="Other">Other</option>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-charcoal">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <label for="primary_cause" class="block text-sm font-bold text-charcoal mb-2">Primary Cause *</label>
                            <div class="relative">
                                <select id="primary_cause" name="primary_cause" required class="w-full px-4 py-3 border border-charcoal/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-gold focus:border-gold transition-all bg-ivory/30 focus:bg-white text-charcoal appearance-none" data-validate="required">
                                    <option value="" disabled selected>Select Primary Cause</option>
                                    <option value="Education">Education</option>
                                    <option value="Healthcare">Healthcare</option>
                                    <option value="Women Empowerment">Women Empowerment</option>
                                    <option value="Child Welfare">Child Welfare</option>
                                    <option value="Animal Welfare">Animal Welfare</option>
                                    <option value="Environment">Environment</option>
                                    <option value="Hunger & Food Support">Hunger & Food Support</option>
                                    <option value="Elderly Care">Elderly Care</option>
                                    <option value="Disability Support">Disability Support</option>
                                    <option value="Community Development">Community Development</option>
                                    <option value="Other">Other</option>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-charcoal">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </div>
                            </div>
                        </div>
                        
                        <div class="md:col-span-2 relative">
                            <label for="description" class="block text-sm font-bold text-charcoal mb-2">Organization Description *</label>
                            <textarea id="description" name="description" rows="4" required class="w-full px-4 py-3 border border-charcoal/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-gold focus:border-gold transition-all bg-ivory/30 focus:bg-white text-charcoal resize-none ngo-char-count" placeholder="Briefly describe your organization's history, vision, and core activities..." maxlength="500" data-validate="required"></textarea>
                            <div class="absolute bottom-3 right-4 text-xs text-charcoal/40 char-counter">0 / 500</div>
                        </div>
                    </div>
                </section>

                <!-- Section 02: Contact Information -->
                <section>
                    <div class="border-b border-charcoal/10 pb-4 mb-6">
                        <h2 class="text-2xl font-serif font-bold text-forest flex items-center">
                            <span class="bg-forest text-white text-sm w-8 h-8 rounded-full flex items-center justify-center mr-3 font-sans">02</span>
                            Contact Information
                        </h2>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="official_email" class="block text-sm font-bold text-charcoal mb-2">Official Email Address *</label>
                            <input type="email" id="official_email" name="official_email" required class="w-full px-4 py-3 border border-charcoal/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-gold focus:border-gold transition-all bg-ivory/30 focus:bg-white text-charcoal" placeholder="contact@ngo.org" data-validate="required email">
                        </div>
                        
                        <div>
                            <label for="phone" class="block text-sm font-bold text-charcoal mb-2">Official Phone Number *</label>
                            <input type="tel" id="phone" name="phone" required class="w-full px-4 py-3 border border-charcoal/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-gold focus:border-gold transition-all bg-ivory/30 focus:bg-white text-charcoal" placeholder="10-digit number" data-validate="required numeric">
                        </div>
                        
                        <div class="md:col-span-2">
                            <label for="website" class="block text-sm font-bold text-charcoal mb-2">Website</label>
                            <input type="url" id="website" name="website" class="w-full px-4 py-3 border border-charcoal/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-gold focus:border-gold transition-all bg-ivory/30 focus:bg-white text-charcoal" placeholder="https://www.yourngo.org">
                        </div>
                        
                        <div>
                            <label for="contact_person" class="block text-sm font-bold text-charcoal mb-2">Contact Person Name *</label>
                            <input type="text" id="contact_person" name="contact_person" required class="w-full px-4 py-3 border border-charcoal/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-gold focus:border-gold transition-all bg-ivory/30 focus:bg-white text-charcoal" placeholder="Full Name" data-validate="required alpha">
                        </div>
                        
                        <div>
                            <label for="designation" class="block text-sm font-bold text-charcoal mb-2">Contact Person Designation *</label>
                            <input type="text" id="designation" name="designation" required data-no-name-val="true" class="w-full px-4 py-3 border border-charcoal/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-gold focus:border-gold transition-all bg-ivory/30 focus:bg-white text-charcoal" placeholder="e.g., Director, Secretary" data-validate="required">
                        </div>
                    </div>
                </section>

                <!-- Section 03: Organization Address -->
                <section>
                    <div class="border-b border-charcoal/10 pb-4 mb-6">
                        <h2 class="text-2xl font-serif font-bold text-forest flex items-center">
                            <span class="bg-forest text-white text-sm w-8 h-8 rounded-full flex items-center justify-center mr-3 font-sans">03</span>
                            Organization Address
                        </h2>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label for="address" class="block text-sm font-bold text-charcoal mb-2">Address *</label>
                            <textarea id="address" name="address" rows="2" required class="w-full px-4 py-3 border border-charcoal/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-gold focus:border-gold transition-all bg-ivory/30 focus:bg-white text-charcoal resize-none" placeholder="Street Address, Building, Area" data-validate="required"></textarea>
                        </div>
                        
                        <div>
                            <label for="city" class="block text-sm font-bold text-charcoal mb-2">City *</label>
                            <input type="text" id="city" name="city" required class="w-full px-4 py-3 border border-charcoal/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-gold focus:border-gold transition-all bg-ivory/30 focus:bg-white text-charcoal" placeholder="City" data-validate="required alpha">
                        </div>
                        
                        <div>
                            <label for="state" class="block text-sm font-bold text-charcoal mb-2">State *</label>
                            <input type="text" id="state" name="state" required class="w-full px-4 py-3 border border-charcoal/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-gold focus:border-gold transition-all bg-ivory/30 focus:bg-white text-charcoal" placeholder="State" data-validate="required alpha">
                        </div>
                        
                        <div>
                            <label for="pincode" class="block text-sm font-bold text-charcoal mb-2">Pincode *</label>
                            <input type="text" id="pincode" name="pincode" required data-no-name-val="true" class="w-full px-4 py-3 border border-charcoal/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-gold focus:border-gold transition-all bg-ivory/30 focus:bg-white text-charcoal" placeholder="6-digit pincode" data-validate="required">
                        </div>
                    </div>
                </section>

                <!-- Section 04: Verification Documents -->
                <section>
                    <div class="border-b border-charcoal/10 pb-4 mb-6">
                        <h2 class="text-2xl font-serif font-bold text-forest flex items-center">
                            <span class="bg-forest text-white text-sm w-8 h-8 rounded-full flex items-center justify-center mr-3 font-sans">04</span>
                            Verification Documents
                        </h2>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Doc 1 -->
                        <div class="border border-charcoal/20 border-dashed rounded-xl p-6 bg-ivory/30 hover:bg-ivory/60 transition-colors relative group">
                            <input type="file" id="registration_certificate" name="registration_certificate" required accept=".pdf,.jpg,.jpeg,.png" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10 file-input" data-validate="required file">
                            <div class="text-center pointer-events-none">
                                <svg class="w-8 h-8 text-gold mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                                <p class="text-sm font-bold text-forest mb-1">NGO Registration Certificate *</p>
                                <p class="text-xs text-charcoal/60 mb-2">PDF, JPG, PNG (Max 5MB)</p>
                                <button type="button" class="text-xs font-bold bg-white text-charcoal border border-charcoal/20 rounded-md px-3 py-1.5 shadow-sm group-hover:border-gold transition-colors file-btn pointer-events-auto">Browse File</button>
                                <p class="text-xs text-forest font-bold mt-2 hidden file-name"></p>
                            </div>
                        </div>
                        
                        <!-- Doc 2 -->
                        <div class="border border-charcoal/20 border-dashed rounded-xl p-6 bg-ivory/30 hover:bg-ivory/60 transition-colors relative group">
                            <input type="file" id="tax_document" name="tax_document" required accept=".pdf,.jpg,.jpeg,.png" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10 file-input" data-validate="required file">
                            <div class="text-center pointer-events-none">
                                <svg class="w-8 h-8 text-gold mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                <p class="text-sm font-bold text-forest mb-1">PAN / Tax Document *</p>
                                <p class="text-xs text-charcoal/60 mb-2">PDF, JPG, PNG (Max 5MB)</p>
                                <button type="button" class="text-xs font-bold bg-white text-charcoal border border-charcoal/20 rounded-md px-3 py-1.5 shadow-sm group-hover:border-gold transition-colors file-btn pointer-events-auto">Browse File</button>
                                <p class="text-xs text-forest font-bold mt-2 hidden file-name"></p>
                            </div>
                        </div>
                        
                        <!-- Doc 3 -->
                        <div class="border border-charcoal/20 border-dashed rounded-xl p-6 bg-ivory/30 hover:bg-ivory/60 transition-colors relative group">
                            <input type="file" id="address_proof" name="address_proof" required accept=".pdf,.jpg,.jpeg,.png" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10 file-input" data-validate="required file">
                            <div class="text-center pointer-events-none">
                                <svg class="w-8 h-8 text-gold mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                <p class="text-sm font-bold text-forest mb-1">Address Proof *</p>
                                <p class="text-xs text-charcoal/60 mb-2">PDF, JPG, PNG (Max 5MB)</p>
                                <button type="button" class="text-xs font-bold bg-white text-charcoal border border-charcoal/20 rounded-md px-3 py-1.5 shadow-sm group-hover:border-gold transition-colors file-btn pointer-events-auto">Browse File</button>
                                <p class="text-xs text-forest font-bold mt-2 hidden file-name"></p>
                            </div>
                        </div>
                        
                        <!-- Doc 4 -->
                        <div class="border border-charcoal/20 border-dashed rounded-xl p-6 bg-ivory/30 hover:bg-ivory/60 transition-colors relative group">
                            <input type="file" id="supporting_document" name="supporting_document" accept=".pdf,.jpg,.jpeg,.png" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10 file-input" data-validate="file">
                            <div class="text-center pointer-events-none">
                                <svg class="w-8 h-8 text-gold mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path></svg>
                                <p class="text-sm font-bold text-forest mb-1">Supporting Document (Optional)</p>
                                <p class="text-xs text-charcoal/60 mb-2">PDF, JPG, PNG (Max 5MB)</p>
                                <button type="button" class="text-xs font-bold bg-white text-charcoal border border-charcoal/20 rounded-md px-3 py-1.5 shadow-sm group-hover:border-gold transition-colors file-btn pointer-events-auto">Browse File</button>
                                <p class="text-xs text-forest font-bold mt-2 hidden file-name"></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-6 bg-blue-50/50 p-4 rounded-xl border border-blue-100 flex items-start space-x-3">
                        <svg class="w-5 h-5 text-blue-500 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <p class="text-sm text-charcoal/80 leading-relaxed">Additional financial verification may be requested after your NGO application is reviewed. Bank details are not required at this stage.</p>
                    </div>
                </section>

                <!-- Section 05: About Your Impact -->
                <section>
                    <div class="border-b border-charcoal/10 pb-4 mb-6">
                        <h2 class="text-2xl font-serif font-bold text-forest flex items-center">
                            <span class="bg-forest text-white text-sm w-8 h-8 rounded-full flex items-center justify-center mr-3 font-sans">05</span>
                            About Your Impact
                        </h2>
                    </div>
                    
                    <div class="space-y-6">
                        <div>
                            <label for="mission" class="block text-sm font-bold text-charcoal mb-2">Mission Statement *</label>
                            <textarea id="mission" name="mission" rows="3" required class="w-full px-4 py-3 border border-charcoal/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-gold focus:border-gold transition-all bg-ivory/30 focus:bg-white text-charcoal resize-none" placeholder="What is the core mission of your organization?" data-validate="required"></textarea>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-bold text-charcoal mb-3">Areas You Serve *</label>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                                <label class="flex items-center space-x-3 p-3 border border-charcoal/10 rounded-xl cursor-pointer hover:bg-ivory/50 transition-colors">
                                    <input type="checkbox" name="service_areas[]" value="Local Community" class="h-4 w-4 text-forest focus:ring-gold border-charcoal/30 rounded accent-forest">
                                    <span class="text-sm text-charcoal font-medium">Local Community</span>
                                </label>
                                <label class="flex items-center space-x-3 p-3 border border-charcoal/10 rounded-xl cursor-pointer hover:bg-ivory/50 transition-colors">
                                    <input type="checkbox" name="service_areas[]" value="District" class="h-4 w-4 text-forest focus:ring-gold border-charcoal/30 rounded accent-forest">
                                    <span class="text-sm text-charcoal font-medium">District</span>
                                </label>
                                <label class="flex items-center space-x-3 p-3 border border-charcoal/10 rounded-xl cursor-pointer hover:bg-ivory/50 transition-colors">
                                    <input type="checkbox" name="service_areas[]" value="State" class="h-4 w-4 text-forest focus:ring-gold border-charcoal/30 rounded accent-forest">
                                    <span class="text-sm text-charcoal font-medium">State</span>
                                </label>
                                <label class="flex items-center space-x-3 p-3 border border-charcoal/10 rounded-xl cursor-pointer hover:bg-ivory/50 transition-colors">
                                    <input type="checkbox" name="service_areas[]" value="National" class="h-4 w-4 text-forest focus:ring-gold border-charcoal/30 rounded accent-forest">
                                    <span class="text-sm text-charcoal font-medium">National</span>
                                </label>
                                <label class="flex items-center space-x-3 p-3 border border-charcoal/10 rounded-xl cursor-pointer hover:bg-ivory/50 transition-colors">
                                    <input type="checkbox" name="service_areas[]" value="Rural Communities" class="h-4 w-4 text-forest focus:ring-gold border-charcoal/30 rounded accent-forest">
                                    <span class="text-sm text-charcoal font-medium">Rural Communities</span>
                                </label>
                                <label class="flex items-center space-x-3 p-3 border border-charcoal/10 rounded-xl cursor-pointer hover:bg-ivory/50 transition-colors">
                                    <input type="checkbox" name="service_areas[]" value="Urban Communities" class="h-4 w-4 text-forest focus:ring-gold border-charcoal/30 rounded accent-forest">
                                    <span class="text-sm text-charcoal font-medium">Urban Communities</span>
                                </label>
                            </div>
                        </div>
                        
                        <div>
                            <label for="impact" class="block text-sm font-bold text-charcoal mb-2">Brief Impact / Achievements *</label>
                            <textarea id="impact" name="impact" rows="3" required class="w-full px-4 py-3 border border-charcoal/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-gold focus:border-gold transition-all bg-ivory/30 focus:bg-white text-charcoal resize-none" placeholder="Highlight key metrics or achievements (e.g., 'Provided meals to 5,000 families in 2023')" data-validate="required"></textarea>
                        </div>
                    </div>
                </section>

                <!-- Section 06: Declaration -->
                <section>
                    <div class="border-b border-charcoal/10 pb-4 mb-6">
                        <h2 class="text-2xl font-serif font-bold text-forest flex items-center">
                            <span class="bg-forest text-white text-sm w-8 h-8 rounded-full flex items-center justify-center mr-3 font-sans">06</span>
                            Declaration
                        </h2>
                    </div>
                    
                    <div class="space-y-4">
                        <label class="flex items-start space-x-3 cursor-pointer">
                            <input type="checkbox" id="confirm_info" name="confirm_info" required class="h-5 w-5 text-forest focus:ring-gold border-charcoal/30 rounded accent-forest mt-0.5" data-validate="required">
                            <span class="text-sm text-charcoal font-medium leading-relaxed">I confirm that the information provided in this application is accurate and complete to the best of my knowledge. *</span>
                        </label>
                        
                        <label class="flex items-start space-x-3 cursor-pointer">
                            <input type="checkbox" id="agree_guidelines" name="agree_guidelines" required class="h-5 w-5 text-forest focus:ring-gold border-charcoal/30 rounded accent-forest mt-0.5" data-validate="required terms">
                            <span class="text-sm text-charcoal font-medium leading-relaxed">I agree to KarmaSetu's NGO verification and platform guidelines. *</span>
                        </label>
                        
                        <p class="text-xs text-charcoal/60 mt-4 italic">Submitting this application does not guarantee approval. KarmaSetu may request additional information or documentation during verification.</p>
                    </div>
                </section>

                <!-- Submit Area -->
                <div class="border-t border-gold/20 pt-8 mt-12 flex flex-col sm:flex-row items-center justify-end space-y-4 sm:space-y-0 sm:space-x-4">
                    <button type="button" class="w-full sm:w-auto px-6 py-3 text-sm font-bold text-forest bg-ivory border border-gold/30 hover:bg-gold/10 hover:border-gold rounded-full transition-colors">
                        Save as Draft
                    </button>
                    <button type="submit" class="w-full sm:w-auto px-10 py-3 text-sm font-bold text-white bg-forest hover:bg-forest-dark shadow-md hover:shadow-lg rounded-full transition-all">
                        Submit Application
                    </button>
                </div>
            
            </form>
            
            <!-- Success State (Hidden by default) -->
            <div id="successState" style="display: none;" class="text-center py-12 px-6">
                <div class="w-20 h-20 bg-forest/10 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-forest" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h2 class="text-3xl font-serif font-bold text-forest mb-4">Application Submitted Successfully</h2>
                <p class="text-charcoal-light text-lg mb-8 max-w-xl mx-auto">Thank you for registering your NGO with KarmaSetu. Your application has been submitted for review.</p>
                
                <div class="bg-ivory/50 border border-charcoal/10 rounded-2xl p-6 max-w-md mx-auto mb-8 text-left space-y-4">
                    <div class="flex justify-between items-center border-b border-charcoal/10 pb-4">
                        <span class="text-charcoal-light font-medium text-sm">Application ID</span>
                        <span class="font-bold text-forest text-lg">NGO-001</span>
                    </div>
                    <div class="flex justify-between items-center pb-2">
                        <span class="text-charcoal-light font-medium text-sm">Status</span>
                        <span class="inline-flex items-center px-3 py-1 rounded-full bg-gold/20 text-forest text-xs font-bold uppercase tracking-wider">Pending Review</span>
                    </div>
                </div>
                
                <p class="text-sm text-charcoal/80 mb-10 max-w-lg mx-auto leading-relaxed">Our administration team will review your organization details and documents. We will contact you via email once the initial verification is complete.</p>
                
                <a href="<?= baseUrl('ngo/application-status.php') ?>" class="bg-forest text-ivory hover:bg-forest-dark px-8 py-3 rounded-full font-bold transition-all shadow-md inline-block">
                    View Application Status
                </a>
            </div>

        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    $('#ngoApplicationForm').on('submit', function(e) {
        e.preventDefault();
        
        let isValid = true;
        $(this).find('[required]').each(function() {
            if (!$(this).val() && !$(this).is(':checkbox')) {
                isValid = false;
                $(this).addClass('border-red-500');
            } else if ($(this).is(':checkbox') && !$(this).is(':checked')) {
                isValid = false;
                $(this).next().addClass('text-red-500');
            } else {
                $(this).removeClass('border-red-500');
                if ($(this).is(':checkbox')) $(this).next().removeClass('text-red-500');
            }
        });
        
        if ($('input[name="service_areas[]"]:checked').length === 0) {
            isValid = false;
            $('input[name="service_areas[]"]').parent().addClass('border-red-500');
        } else {
            $('input[name="service_areas[]"]').parent().removeClass('border-red-500');
        }
        
        if (isValid) {
            $('#ngoApplicationForm').hide();
            $('#successState').fadeIn();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        } else {
            alert("Please fill all required fields correctly.");
        }
    });
    
    $('#ngoApplicationForm [required]').on('input change', function() {
        $(this).removeClass('border-red-500');
        if ($(this).is(':checkbox')) $(this).next().removeClass('text-red-500');
    });
    
    $('input[name="service_areas[]"]').on('change', function() {
        if ($('input[name="service_areas[]"]:checked').length > 0) {
            $('input[name="service_areas[]"]').parent().removeClass('border-red-500');
        }
    });
});
</script>
<?php
$content = ob_get_clean();
require_once __DIR__ . '/../includes/layouts/main.php';
?>

