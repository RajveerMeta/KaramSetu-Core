<?php
require_once __DIR__ . '/../includes/functions.php';
ob_start();
?>

<div class="min-h-[calc(100vh-200px)] flex items-center justify-center py-16 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full bg-white p-8 sm:p-10 rounded-2xl shadow-sm border border-gold/20 relative overflow-hidden">
        
        <!-- Subtle decorative element -->
        <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-gold/10 rounded-full blur-xl z-0 pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 -mb-4 -ml-4 w-24 h-24 bg-forest/5 rounded-full blur-xl z-0 pointer-events-none"></div>

        <div class="relative z-10">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-serif font-bold text-forest mb-2">Join KarmaSetu</h1>
                <p class="text-charcoal-light">Create an account to start your journey.</p>
            </div>
            
            <form id="registerForm" class="space-y-6 validation-form" action="#" method="POST" data-success-redirect="<?= baseUrl('auth/login.php') ?>" novalidate>
                
                <div class="space-y-5">
                    <div class="relative pb-1">
                        <label for="name" class="block text-sm font-medium text-charcoal mb-1">Full Name</label>
                        <input id="name" name="name" type="text" autocomplete="name" required data-validate="required alpha" 
                            class="appearance-none block w-full px-4 py-3 border border-charcoal/20 rounded-lg text-charcoal placeholder-charcoal/40 focus:outline-none focus:ring-1 focus:ring-gold focus:border-gold transition-colors sm:text-sm bg-ivory/30 focus:bg-white" 
                            placeholder="Enter your full name">
                        <p class="absolute -bottom-4 left-1 text-[11px] text-red-500 hidden" id="nameError">Full name is required.</p>
                    </div>

                    <div class="relative pb-1">
                        <label for="email" class="block text-sm font-medium text-charcoal mb-1">Email Address</label>
                        <input id="email" name="email" type="email" autocomplete="email" required data-validate="required email" 
                            class="appearance-none block w-full px-4 py-3 border border-charcoal/20 rounded-lg text-charcoal placeholder-charcoal/40 focus:outline-none focus:ring-1 focus:ring-gold focus:border-gold transition-colors sm:text-sm bg-ivory/30 focus:bg-white" 
                            placeholder="Enter your email address">
                        <p class="absolute -bottom-4 left-1 text-[11px] text-red-500 hidden" id="emailError">Please enter a valid email address.</p>
                    </div>

                    <div class="relative pb-1">
                        <label for="phone" class="block text-sm font-medium text-charcoal mb-1">Phone Number</label>
                        <input id="phone" name="phone" type="tel" autocomplete="tel" required data-validate="required numeric" data-min="10" data-max="15" 
                            class="appearance-none block w-full px-4 py-3 border border-charcoal/20 rounded-lg text-charcoal placeholder-charcoal/40 focus:outline-none focus:ring-1 focus:ring-gold focus:border-gold transition-colors sm:text-sm bg-ivory/30 focus:bg-white" 
                            placeholder="Enter your phone number">
                        <p class="absolute -bottom-4 left-1 text-[11px] text-red-500 hidden" id="phoneError">Please enter a valid phone number.</p>
                    </div>
                    
                    <div class="relative pb-1">
                        <label for="password" class="block text-sm font-medium text-charcoal mb-1">Password</label>
                        <input id="password" name="password" type="password" autocomplete="new-password" required minlength="8" data-validate="required strongPassword"
                            class="appearance-none block w-full px-4 py-3 border border-charcoal/20 rounded-lg text-charcoal placeholder-charcoal/40 focus:outline-none focus:ring-1 focus:ring-gold focus:border-gold transition-colors sm:text-sm bg-ivory/30 focus:bg-white" 
                            placeholder="Create a password">
                        <p class="absolute -bottom-4 left-1 text-[11px] text-red-500 hidden" id="passwordError">Password must be at least 8 characters.</p>
                    </div>

                    <div class="relative pb-1">
                        <label for="password_confirmation" class="block text-sm font-medium text-charcoal mb-1">Confirm Password</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required minlength="8" data-validate="required confirmPassword" data-password-id="password"
                            class="appearance-none block w-full px-4 py-3 border border-charcoal/20 rounded-lg text-charcoal placeholder-charcoal/40 focus:outline-none focus:ring-1 focus:ring-gold focus:border-gold transition-colors sm:text-sm bg-ivory/30 focus:bg-white" 
                            placeholder="Confirm your password">
                        <p class="absolute -bottom-4 left-1 text-[11px] text-red-500 hidden" id="password_confirmationError">Passwords do not match.</p>
                    </div>
                </div>

                <div class="flex items-start mt-4">
                    <div class="flex items-center h-5">
                        <input id="terms" name="terms" type="checkbox" required data-validate="required terms"
                            class="h-4 w-4 text-forest focus:ring-gold border-charcoal/30 rounded cursor-pointer transition-colors accent-forest">
                    </div>
                    <div class="ml-3 text-sm">
                        <label for="terms" class="text-charcoal cursor-pointer select-none">
                            I agree to the <a href="<?= baseUrl('?page=about') ?>" class="text-forest hover:text-gold transition-colors">Terms of Service</a> and <a href="<?= baseUrl('?page=about') ?>" class="text-forest hover:text-gold transition-colors">Privacy Policy</a>
                        </label>
                        <p class="text-[11px] text-red-500 hidden mt-1" id="termsError">You must accept the terms and conditions.</p>
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" 
                        class="w-full flex justify-center py-3 px-4 border border-transparent text-sm font-bold rounded-full text-white bg-forest hover:bg-forest-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-forest transition-all shadow-md hover:shadow-lg">
                        Create Account
                    </button>
                </div>
            </form>
            
            <div class="text-center mt-8 pt-6 border-t border-charcoal/10">
                <p class="text-sm text-charcoal">
                    Already have an account? 
                    <a href="<?= baseUrl('auth/login.php') ?>" class="font-bold text-forest hover:text-gold transition-colors ml-1">
                        Login
                    </a>
                </p>
            </div>
        </div>
    </div>
</div>



<?php
$content = ob_get_clean();
require_once __DIR__ . '/../includes/layouts/main.php';
?>
