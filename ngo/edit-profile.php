<?php
require_once __DIR__ . '/../includes/functions.php';

// Mock data
$ngos = require __DIR__ . '/../data/ngos.php';
$ngo = $ngos[0]; // Seva Roots Initiative

// Pre-fill values
$ngo_name = $_POST['ngo_name'] ?? $ngo['name'] ?? '';
$registration_number = $_POST['registration_number'] ?? $ngo['registration_number'] ?? '';
$established_date = $_POST['established_date'] ?? '2012-01-01'; // established_year is 2012
$ngo_type = $_POST['ngo_type'] ?? 'Non-Profit Organization';
$primary_cause = $_POST['primary_cause'] ?? $ngo['category'] ?? '';
$description = $_POST['description'] ?? $ngo['description'] ?? '';

$email = $_POST['email'] ?? $ngo['email'] ?? '';
$phone = $_POST['phone'] ?? $ngo['phone'] ?? '';
$website = $_POST['website'] ?? $ngo['website'] ?? '';
$contact_person = $_POST['contact_person'] ?? 'Rahul Desai';
$designation = $_POST['designation'] ?? 'Director';

$address = $_POST['address'] ?? '45 Seva Bhavan, Main Road';
$city = $_POST['city'] ?? $ngo['location'] ?? '';
$state = $_POST['state'] ?? 'Gujarat';
$pincode = $_POST['pincode'] ?? '390001';

$mission = $_POST['mission'] ?? $ngo['mission'] ?? '';
$impact = $_POST['impact'] ?? 'Supported 1000+ individuals across 5 programs.';
$selected_service_areas = $_POST['service_areas'] ?? $ngo['service_areas'] ?? [];

$isSuccess = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $isSuccess = true;
}

ob_start();
?>

<div class="min-h-screen bg-ivory pt-24 pb-16">
    <div class="container mx-auto px-4 max-w-4xl">
        
        <!-- Header -->
        <div class="mb-8">
            <a href="<?= baseUrl('ngo/dashboard.php') ?>" class="inline-flex items-center text-charcoal-light hover:text-forest font-medium mb-4 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Back to Dashboard
            </a>
            <h1 class="text-4xl md:text-5xl font-serif font-bold text-forest mb-4">Edit NGO Profile</h1>
            <p class="text-lg text-charcoal-light">Update your organization's information and profile details.</p>
        </div>

        <?php if ($isSuccess): ?>
        <div class="mb-8 bg-green-50 p-6 rounded-xl shadow-sm border border-green-200 text-center relative overflow-hidden">
            <h2 class="text-2xl font-serif font-bold text-green-800 mb-2">NGO profile updated successfully!</h2>
        </div>
        <?php endif; ?>

        <div class="bg-white rounded-3xl shadow-sm border border-gold/20 overflow-hidden">
            <form id="ngoEditProfileForm" action="#" method="POST" class="p-6 md:p-10 space-y-12 validation-form" novalidate enctype="multipart/form-data">                
                
                <!-- Section 01: Organization Information -->
                <section>
                    <div class="border-b border-charcoal/10 pb-4 mb-6">
                        <h2 class="text-2xl font-serif font-bold text-forest flex items-center">
                            <span class="bg-forest text-white text-sm w-8 h-8 rounded-full flex items-center justify-center mr-3 font-sans">01</span>
                            Organization Information
                        </h2>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label for="ngo_name" class="block text-sm font-bold text-charcoal mb-2">NGO Name *</label>
                            <input type="text" id="ngo_name" name="ngo_name" value="<?= htmlspecialchars($ngo_name) ?>" required class="w-full px-4 py-3 border border-charcoal/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-gold focus:border-gold transition-all bg-ivory/30 focus:bg-white text-charcoal" data-validate="required alpha">
                        </div>
                        
                        <div>
                            <label for="registration_number" class="block text-sm font-bold text-charcoal mb-2">Registration Number *</label>
                            <input type="text" id="registration_number" name="registration_number" value="<?= htmlspecialchars($registration_number) ?>" required class="w-full px-4 py-3 border border-charcoal/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-gold focus:border-gold transition-all bg-ivory/30 focus:bg-white text-charcoal" data-validate="required">
                        </div>
                        
                        <div>
                            <label for="established_date" class="block text-sm font-bold text-charcoal mb-2">Established Date *</label>
                            <input type="date" id="established_date" name="established_date" value="<?= htmlspecialchars($established_date) ?>" required class="w-full px-4 py-3 border border-charcoal/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-gold focus:border-gold transition-all bg-ivory/30 focus:bg-white text-charcoal" data-validate="required">
                        </div>
                        
                        <div>
                            <label for="ngo_type" class="block text-sm font-bold text-charcoal mb-2">NGO Type *</label>
                            <div class="relative">
                                <select id="ngo_type" name="ngo_type" required class="w-full px-4 py-3 border border-charcoal/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-gold focus:border-gold transition-all bg-ivory/30 focus:bg-white text-charcoal appearance-none" data-validate="required">
                                    <option value="" disabled>Select NGO Type</option>
                                    <option value="Trust" <?= $ngo_type === 'Trust' ? 'selected' : '' ?>>Trust</option>
                                    <option value="Society" <?= $ngo_type === 'Society' ? 'selected' : '' ?>>Society</option>
                                    <option value="Section 8 Company" <?= $ngo_type === 'Section 8 Company' ? 'selected' : '' ?>>Section 8 Company</option>
                                    <option value="Non-Profit Organization" <?= $ngo_type === 'Non-Profit Organization' ? 'selected' : '' ?>>Non-Profit Organization</option>
                                    <option value="Other" <?= $ngo_type === 'Other' ? 'selected' : '' ?>>Other</option>
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
                                    <option value="" disabled>Select Primary Cause</option>
                                    <option value="Education" <?= $primary_cause === 'Education' ? 'selected' : '' ?>>Education</option>
                                    <option value="Healthcare" <?= $primary_cause === 'Healthcare' ? 'selected' : '' ?>>Healthcare</option>
                                    <option value="Women Empowerment" <?= $primary_cause === 'Women Empowerment' ? 'selected' : '' ?>>Women Empowerment</option>
                                    <option value="Child Welfare" <?= $primary_cause === 'Child Welfare' ? 'selected' : '' ?>>Child Welfare</option>
                                    <option value="Animal Welfare" <?= $primary_cause === 'Animal Welfare' ? 'selected' : '' ?>>Animal Welfare</option>
                                    <option value="Environment" <?= $primary_cause === 'Environment' ? 'selected' : '' ?>>Environment</option>
                                    <option value="Community Development" <?= $primary_cause === 'Community Development' ? 'selected' : '' ?>>Community Development</option>
                                    <option value="Other" <?= $primary_cause === 'Other' ? 'selected' : '' ?>>Other</option>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-charcoal">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </div>
                            </div>
                        </div>

                        <div class="md:col-span-2">
                            <label for="description" class="block text-sm font-bold text-charcoal mb-2">Description *</label>
                            <textarea id="description" name="description" rows="4" required class="w-full px-4 py-3 border border-charcoal/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-gold focus:border-gold transition-all bg-ivory/30 focus:bg-white text-charcoal resize-none" data-validate="required"><?= htmlspecialchars($description) ?></textarea>
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
                            <label for="email" class="block text-sm font-bold text-charcoal mb-2">Official Email *</label>
                            <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>" required class="w-full px-4 py-3 border border-charcoal/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-gold focus:border-gold transition-all bg-ivory/30 focus:bg-white text-charcoal" data-validate="required email">
                        </div>
                        
                        <div>
                            <label for="phone" class="block text-sm font-bold text-charcoal mb-2">Phone *</label>
                            <input type="tel" id="phone" name="phone" value="<?= htmlspecialchars($phone) ?>" required class="w-full px-4 py-3 border border-charcoal/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-gold focus:border-gold transition-all bg-ivory/30 focus:bg-white text-charcoal" data-validate="required numeric">
                        </div>

                        <div>
                            <label for="website" class="block text-sm font-bold text-charcoal mb-2">Website</label>
                            <input type="text" id="website" name="website" value="<?= htmlspecialchars($website) ?>" class="w-full px-4 py-3 border border-charcoal/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-gold focus:border-gold transition-all bg-ivory/30 focus:bg-white text-charcoal">
                        </div>

                        <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="contact_person" class="block text-sm font-bold text-charcoal mb-2">Contact Person *</label>
                                <input type="text" id="contact_person" name="contact_person" value="<?= htmlspecialchars($contact_person) ?>" required class="w-full px-4 py-3 border border-charcoal/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-gold focus:border-gold transition-all bg-ivory/30 focus:bg-white text-charcoal" data-validate="required alpha">
                            </div>
                            <div>
                                <label for="designation" class="block text-sm font-bold text-charcoal mb-2">Designation *</label>
                                <input type="text" id="designation" name="designation" value="<?= htmlspecialchars($designation) ?>" required class="w-full px-4 py-3 border border-charcoal/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-gold focus:border-gold transition-all bg-ivory/30 focus:bg-white text-charcoal" data-validate="required alpha">
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Section 03: Address Information -->
                <section>
                    <div class="border-b border-charcoal/10 pb-4 mb-6">
                        <h2 class="text-2xl font-serif font-bold text-forest flex items-center">
                            <span class="bg-forest text-white text-sm w-8 h-8 rounded-full flex items-center justify-center mr-3 font-sans">03</span>
                            Address Information
                        </h2>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label for="address" class="block text-sm font-bold text-charcoal mb-2">Address *</label>
                            <input type="text" id="address" name="address" value="<?= htmlspecialchars($address) ?>" required class="w-full px-4 py-3 border border-charcoal/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-gold focus:border-gold transition-all bg-ivory/30 focus:bg-white text-charcoal" data-validate="required">
                        </div>
                        
                        <div>
                            <label for="city" class="block text-sm font-bold text-charcoal mb-2">City *</label>
                            <input type="text" id="city" name="city" value="<?= htmlspecialchars($city) ?>" required class="w-full px-4 py-3 border border-charcoal/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-gold focus:border-gold transition-all bg-ivory/30 focus:bg-white text-charcoal" data-validate="required alpha">
                        </div>
                        
                        <div>
                            <label for="state" class="block text-sm font-bold text-charcoal mb-2">State *</label>
                            <input type="text" id="state" name="state" value="<?= htmlspecialchars($state) ?>" required class="w-full px-4 py-3 border border-charcoal/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-gold focus:border-gold transition-all bg-ivory/30 focus:bg-white text-charcoal" data-validate="required alpha">
                        </div>
                        
                        <div>
                            <label for="pincode" class="block text-sm font-bold text-charcoal mb-2">Pincode *</label>
                            <input type="text" id="pincode" name="pincode" value="<?= htmlspecialchars($pincode) ?>" required class="w-full px-4 py-3 border border-charcoal/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-gold focus:border-gold transition-all bg-ivory/30 focus:bg-white text-charcoal" data-validate="required numeric">
                        </div>
                    </div>
                </section>

                <!-- Section 04: Documents -->
                <section>
                    <div class="border-b border-charcoal/10 pb-4 mb-6">
                        <h2 class="text-2xl font-serif font-bold text-forest flex items-center">
                            <span class="bg-forest text-white text-sm w-8 h-8 rounded-full flex items-center justify-center mr-3 font-sans">04</span>
                            Documents
                        </h2>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Registration Certificate -->
                        <div class="bg-ivory/30 p-5 rounded-xl border border-charcoal/10">
                            <label class="block text-sm font-bold text-charcoal mb-2">Registration Certificate</label>
                            <div class="mb-3 text-sm text-forest font-medium flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Current: reg_cert_2012.pdf
                            </div>
                            <input type="file" id="doc_registration" name="doc_registration" class="block w-full text-sm text-charcoal-light file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-bold file:bg-gold/10 file:text-forest hover:file:bg-gold/20 transition-colors" accept=".pdf,.jpg,.jpeg,.png" data-validate="file" data-filetypes="pdf,jpg,jpeg,png" data-filesize="5120">
                            <p class="text-xs text-charcoal-light mt-2">Optional. Upload a new file (PDF, JPG, PNG up to 5MB) to replace.</p>
                        </div>
                        
                        <!-- Tax Document -->
                        <div class="bg-ivory/30 p-5 rounded-xl border border-charcoal/10">
                            <label class="block text-sm font-bold text-charcoal mb-2">Tax Document (e.g. 12A/80G)</label>
                            <div class="mb-3 text-sm text-forest font-medium flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Current: tax_12a_exempt.pdf
                            </div>
                            <input type="file" id="doc_tax" name="doc_tax" class="block w-full text-sm text-charcoal-light file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-bold file:bg-gold/10 file:text-forest hover:file:bg-gold/20 transition-colors" accept=".pdf,.jpg,.jpeg,.png" data-validate="file" data-filetypes="pdf,jpg,jpeg,png" data-filesize="5120">
                            <p class="text-xs text-charcoal-light mt-2">Optional. Upload a new file (PDF, JPG, PNG up to 5MB) to replace.</p>
                        </div>

                        <!-- Address Proof -->
                        <div class="bg-ivory/30 p-5 rounded-xl border border-charcoal/10">
                            <label class="block text-sm font-bold text-charcoal mb-2">Address Proof</label>
                            <div class="mb-3 text-sm text-forest font-medium flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Current: address_proof.pdf
                            </div>
                            <input type="file" id="doc_address" name="doc_address" class="block w-full text-sm text-charcoal-light file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-bold file:bg-gold/10 file:text-forest hover:file:bg-gold/20 transition-colors" accept=".pdf,.jpg,.jpeg,.png" data-validate="file" data-filetypes="pdf,jpg,jpeg,png" data-filesize="5120">
                            <p class="text-xs text-charcoal-light mt-2">Optional. Upload a new file (PDF, JPG, PNG up to 5MB) to replace.</p>
                        </div>

                        <!-- Supporting Document -->
                        <div class="bg-ivory/30 p-5 rounded-xl border border-charcoal/10">
                            <label class="block text-sm font-bold text-charcoal mb-2">Supporting Document</label>
                            <div class="mb-3 text-sm text-charcoal-light font-medium flex items-center">
                                No document currently uploaded
                            </div>
                            <input type="file" id="doc_support" name="doc_support" class="block w-full text-sm text-charcoal-light file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-bold file:bg-gold/10 file:text-forest hover:file:bg-gold/20 transition-colors" accept=".pdf,.jpg,.jpeg,.png" data-validate="file" data-filetypes="pdf,jpg,jpeg,png" data-filesize="5120">
                            <p class="text-xs text-charcoal-light mt-2">Optional. Upload a new file (PDF, JPG, PNG up to 5MB).</p>
                        </div>
                    </div>
                </section>

                <!-- Section 05: Organization Details -->
                <section>
                    <div class="border-b border-charcoal/10 pb-4 mb-6">
                        <h2 class="text-2xl font-serif font-bold text-forest flex items-center">
                            <span class="bg-forest text-white text-sm w-8 h-8 rounded-full flex items-center justify-center mr-3 font-sans">05</span>
                            Organization Details
                        </h2>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label for="mission" class="block text-sm font-bold text-charcoal mb-2">Mission *</label>
                            <textarea id="mission" name="mission" rows="3" required class="w-full px-4 py-3 border border-charcoal/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-gold focus:border-gold transition-all bg-ivory/30 focus:bg-white text-charcoal resize-none" data-validate="required"><?= htmlspecialchars($mission) ?></textarea>
                        </div>
                        
                        <div class="md:col-span-2">
                            <label for="impact" class="block text-sm font-bold text-charcoal mb-2">Impact *</label>
                            <textarea id="impact" name="impact" rows="3" required class="w-full px-4 py-3 border border-charcoal/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-gold focus:border-gold transition-all bg-ivory/30 focus:bg-white text-charcoal resize-none" data-validate="required"><?= htmlspecialchars($impact) ?></textarea>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-bold text-charcoal mb-3">Service Areas * (Select at least one)</label>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                <?php
                                $areas = ['Vadodara', 'Ahmedabad', 'Surat', 'Rajkot', 'Gandhinagar', 'Bhavnagar', 'Jamnagar', 'Junagadh', 'Anand', 'Navsari', 'Surendranagar', 'Rural Villages'];
                                foreach ($areas as $idx => $area): 
                                    $isChecked = in_array($area, $selected_service_areas) ? 'checked' : '';
                                ?>
                                <label class="flex items-center space-x-3 p-3 border border-charcoal/10 rounded-xl hover:bg-gold/5 cursor-pointer transition-colors group">
                                    <div class="relative flex items-center justify-center">
                                        <input type="checkbox" name="service_areas[]" value="<?= $area ?>" class="peer appearance-none w-5 h-5 border-2 border-charcoal/30 rounded focus:ring-2 focus:ring-forest focus:outline-none checked:bg-forest checked:border-forest transition-colors" data-validate="min-items" data-min-items="1" <?= $isChecked ?>>
                                        <svg class="absolute w-3.5 h-3.5 text-white opacity-0 peer-checked:opacity-100 pointer-events-none transition-opacity" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </div>
                                    <span class="text-sm font-medium text-charcoal group-hover:text-forest transition-colors"><?= $area ?></span>
                                </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Actions -->
                <div class="flex flex-col-reverse md:flex-row items-center justify-end gap-4 pt-6 border-t border-charcoal/10">
                    <a href="<?= baseUrl('ngo/dashboard.php') ?>" class="w-full md:w-auto px-8 py-3.5 border-2 border-charcoal/20 text-charcoal font-bold rounded-xl hover:bg-charcoal/5 transition-colors text-center">
                        Cancel
                    </a>
                    <button type="submit" class="w-full md:w-auto px-8 py-3.5 bg-forest text-white font-bold rounded-xl hover:bg-forest-dark shadow-md hover:shadow-lg transition-all text-center">
                        Save Changes
                    </button>
                </div>
                
            </form>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../includes/layouts/main.php';
?>
