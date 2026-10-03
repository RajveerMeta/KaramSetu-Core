<?php
require_once __DIR__ . '/../includes/functions.php';
ob_start();
?>

<?php
 
    $user = (object)[
        'name' => 'Rajveer',
        'email' => 'rajveer@example.com',
        'phone' => '+91 98765 43210',
        'dob' => '1995-05-15',
        'city' => 'Ahmedabad',
        'state' => 'Gujarat',
        'role' => 'Member',
        'initials' => 'R'
    ];

    $profileStats = (object)[
        'karma_points' => '1,280',
        'rank' => 10,
        'contributions' => 8,
        'volunteer_hours' => '12h',
        'events_participated' => 4
    ];

    $preferences = (object)[
        'email_notifications' => true,
        'donation_updates' => true,
        'event_notifications' => false,
        'ngo_activity_updates' => true,
    ];
?>


<div class="min-h-screen bg-ivory/40 py-10 lg:py-14">

    <!--MAIN CONTAINER -->
    <div class="w-full max-w-[1280px] mx-auto px-4 sm:px-6 lg:px-8">

        <!--PAGE HEADER-->
        <div class="mb-8 lg:mb-10">

            <h1 class="font-serif text-3xl md:text-4xl lg:text-4xl font-bold text-forest">
                My Profile & Settings
            </h1>

            <p class="mt-2 text-sm lg:text-base text-charcoal-light">
                Manage your personal information, account preferences,
                and KarmaSetu activity.
            </p>

        </div>


       
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">


            <!-- LEFT COLUMN-->
            <div class="lg:col-span-2 space-y-6 lg:space-y-8">


                <!--
                     PROFILE OVERVIEW
                     -->
                <section
                    class="relative w-full overflow-hidden rounded-2xl
                           border border-charcoal/10 bg-white
                           shadow-sm "
                >

                    <!-- Top Background -->
                    <div class="h-24 bg-forest/5"></div>


                    <!-- Profile Content -->
                    <div class="relative px-5 pb-6 sm:px-6 lg:px-8">

                        <!-- Avatar -->
                        <div class="-mt-12 flex flex-col md:flex-row justify-center">

                            <div
                                class="flex flex-col md:flex-row h-24 w-24 items-center justify-center p-5 md:p-8
                                       rounded-full border-4 border-white
                                       bg-forest text-3xl md:text-4xl font-serif font-bold
                                       text-white shadow-md"
                            >
                                <?= e($user->initials) ?>
                            </div>

                        </div>


                        <!-- User Name -->
                        <div class="mt-4 text-center">

                            <h2 class="font-serif text-xl lg:text-2xl font-bold text-charcoal">
                                <?= e($user->name) ?>
                            </h2>

                            <p class="mt-1 text-sm font-medium text-forest">
                                <?= e($user->role) ?>
                            </p>

                        </div>


                        <!-- Points / Rank -->
                        <div
                            class="mt-6 flex flex-col md:flex-row w-full items-center rounded-xl
                                   border border-charcoal/10
                                   bg-ivory/40"
                        >

                            <!-- Points -->
                            <div
                                class="flex w-full md:w-3/4 flex-col items-center
                                       justify-center border-r
                                       border-charcoal/10 py-4"
                            >

                                <span class="text-xl font-bold text-forest">
                                    <?= e($profileStats->karma_points) ?>
                                </span>

                                <span
                                    class="mt-1 text-[10px] font-bold
                                           uppercase tracking-wider
                                           text-charcoal-light"
                                >
                                    Points
                                </span>

                            </div>


                            <!-- Rank -->
                            <div
                                class="flex w-full md:w-1/2 flex-col items-center
                                       justify-center py-4"
                            >

                                <span class="font-serif text-xl font-bold text-gold">
                                    #<?= e($profileStats->rank) ?>
                                </span>

                                <span
                                    class="mt-1 text-[10px] font-bold
                                           uppercase tracking-wider
                                           text-charcoal-light"
                                >
                                    Rank
                                </span>

                            </div>

                        </div>


                        <!-- Edit Profile -->
                        <button
                            type="button"
                            id="editProfileBtn"
                            class="mt-5 w-full rounded-full border
                                   border-forest px-5 py-2.5
                                   text-sm font-bold text-forest
                                   transition-colors
                                   hover:bg-forest hover:text-white"
                                   
                        >
                            Edit Profile
                        </button>

                    </div>

                </section>



                <!--
                     ACTIVITY SUMMARY
                     -->
                <section
                    class="w-full rounded-2xl border border-charcoal/10
                           bg-white p-5 md:p-8 shadow-sm
                           sm:p-6 lg:p-7"
                >

                    <h3
                        class="border-b border-charcoal/10 pb-4
                               font-serif text-sm font-bold
                               uppercase tracking-wider text-charcoal"
                    >
                        Activity Summary
                    </h3>


                    <div class="mt-1">

                        <!-- Karma Points -->
                        <div
                            class="flex flex-col md:flex-row items-center justify-between
                                   border-b border-charcoal/5 py-4"
                        >

                            <span class="text-sm text-charcoal-light">
                                Karma Points
                            </span>

                            <span class="text-sm font-bold text-forest">
                                <?= e($profileStats->karma_points) ?>
                            </span>

                        </div>


                        <!-- Contributions -->
                        <div
                            class="flex flex-col md:flex-row items-center justify-between
                                   border-b border-charcoal/5 py-4"
                        >

                            <span class="text-sm text-charcoal-light">
                                Contributions
                            </span>

                            <span class="text-sm font-bold text-charcoal">
                                <?= e($profileStats->contributions) ?>
                            </span>

                        </div>


                        <!-- Volunteer Hours -->
                        <div
                            class="flex flex-col md:flex-row items-center justify-between
                                   border-b border-charcoal/5 py-4"
                        >

                            <span class="text-sm text-charcoal-light">
                                Volunteer Hours
                            </span>

                            <span class="text-sm font-bold text-charcoal">
                                <?= e($profileStats->volunteer_hours) ?>
                            </span>

                        </div>


                        <!-- Events -->
                        <div
                            class="flex flex-col md:flex-row items-center justify-between
                                   py-4"
                        >

                            <span class="text-sm text-charcoal-light">
                                Events Participated
                            </span>

                            <span class="text-sm font-bold text-charcoal">
                                <?= e($profileStats->events_participated) ?>
                            </span>

                        </div>

                    </div>

                </section>



                <!--
                     SECURITY
                     -->
                <section
                    class="w-full rounded-2xl border border-charcoal/10
                           bg-white p-5 md:p-8 shadow-sm
                           sm:p-6 lg:p-7"
                >

                    <h3
                        class="border-b border-charcoal/10 pb-4
                               font-serif text-sm font-bold
                               uppercase tracking-wider text-charcoal"
                    >
                        Security
                    </h3>


                    <div>

                        <!-- Change Password -->
                        <div
                            class="flex flex-col md:flex-row items-center justify-between
                                   gap-4 border-b border-charcoal/10
                                   py-5"
                        >

                            <div class="min-w-0">

                                <h4 class="text-sm font-bold text-charcoal">
                                    Change Password
                                </h4>

                                <p class="mt-1 text-xs text-charcoal-light">
                                    Update your account password
                                </p>

                            </div>


                            <button
                                type="button"
                                class="shrink-0 rounded-md
                                       border border-charcoal/20
                                       px-4 py-1.5 text-xs font-bold
                                       text-charcoal
                                       transition-colors
                                       hover:bg-ivory"
                            >
                                Change
                            </button>

                        </div>


                        <!-- Two Factor -->
                        <div
                            class="flex flex-col md:flex-row items-center justify-between
                                   gap-4 border-b border-charcoal/10
                                   py-5"
                        >

                            <div class="min-w-0">

                                <h4 class="text-sm font-bold text-charcoal">
                                    Two-Factor Auth
                                </h4>

                                <p class="mt-1 text-xs text-charcoal-light">
                                    Add extra account security
                                </p>

                            </div>


                            <button
                                type="button"
                                class="shrink-0 rounded-md
                                       border border-charcoal/20
                                       px-4 py-1.5 text-xs font-bold
                                       text-charcoal
                                       transition-colors
                                       hover:bg-ivory"
                            >
                                Enable
                            </button>

                        </div>


                        <!-- Active Sessions -->
                        <div
                            class="flex flex-col md:flex-row items-center justify-between
                                   gap-4 py-5"
                        >

                            <div class="min-w-0">

                                <h4 class="text-sm font-bold text-charcoal">
                                    Active Sessions
                                </h4>

                                <p class="mt-1 text-xs text-charcoal-light">
                                    Manage logged-in devices
                                </p>

                            </div>


                            <button
                                type="button"
                                class="shrink-0 rounded-md
                                       border border-charcoal/20
                                       px-4 py-1.5 text-xs font-bold
                                       text-charcoal
                                       transition-colors
                                       hover:bg-ivory"
                            >
                                View
                            </button>

                        </div>

                    </div>

                </section>

            </div>



            <!-- RIGHT COLUMN-->
            <div class="lg:col-span-1 space-y-6 lg:space-y-8">


                <!--
                     PERSONAL INFORMATION
                     -->
                <section
                    class="w-full rounded-2xl border border-charcoal/10
                           bg-white p-5 md:p-8 shadow-sm
                           sm:p-6 lg:p-7"
                >

                    <h3
                        class="font-serif text-xl font-bold
                               uppercase tracking-wide text-forest"
                    >
                        Personal Information
                    </h3>


                    <div id="profileDisplayMode" class="mt-4">
                        <div class="flex flex-col border-b border-charcoal/5 py-3">
                            <span class="text-xs font-bold uppercase tracking-wider text-charcoal">Full Name</span>
                            <span class="mt-1 text-sm text-charcoal-light"><?= e($user->name) ?></span>
                        </div>
                        <div class="flex flex-col border-b border-charcoal/5 py-3">
                            <span class="text-xs font-bold uppercase tracking-wider text-charcoal">Email Address</span>
                            <span class="mt-1 text-sm text-charcoal-light"><?= e($user->email) ?></span>
                        </div>
                        <div class="flex flex-col border-b border-charcoal/5 py-3">
                            <span class="text-xs font-bold uppercase tracking-wider text-charcoal">Phone Number</span>
                            <span class="mt-1 text-sm text-charcoal-light"><?= e($user->phone) ?></span>
                        </div>
                        <div class="flex flex-col border-b border-charcoal/5 py-3">
                            <span class="text-xs font-bold uppercase tracking-wider text-charcoal">Date of Birth</span>
                            <span class="mt-1 text-sm text-charcoal-light"><?= e($user->dob) ?></span>
                        </div>
                        <div class="flex flex-col border-b border-charcoal/5 py-3">
                            <span class="text-xs font-bold uppercase tracking-wider text-charcoal">City</span>
                            <span class="mt-1 text-sm text-charcoal-light"><?= e($user->city) ?></span>
                        </div>
                        <div class="flex flex-col py-3">
                            <span class="text-xs font-bold uppercase tracking-wider text-charcoal">State</span>
                            <span class="mt-1 text-sm text-charcoal-light"><?= e($user->state) ?></span>
                        </div>
                    </div>


                    <form
                        id="profileEditMode"
                        action="#"
                        method="POST"
                        class="mt-6 hidden validation-form"
                    >

                        <div class="space-y-5">


                            <!-- Full Name -->
                            <div>

                                <label
                                    for="full_name"
                                    class="mb-2 block text-xs font-bold
                                           uppercase tracking-wider
                                           text-charcoal"
                                >
                                    Full Name
                                </label>

                                <input
                                    id="full_name"
                                    name="name"
                                    type="text"
                                    value="<?= e($user- data-validate="alpha">name) ?>"
                                    readonly
                                    class="w-full rounded-lg border
                                           border-charcoal/20
                                           bg-gray-50 px-4 py-3
                                           text-sm text-charcoal
                                           outline-none"
                                >

                            </div>


                            <!-- Email -->
                            <div>

                                <label
                                    for="email"
                                    class="mb-2 block text-xs font-bold
                                           uppercase tracking-wider
                                           text-charcoal"
                                >
                                    Email Address
                                </label>

                                <input
                                    id="email"
                                    name="email"
                                    type="email"
                                    value="<?= e($user- data-validate="email">email) ?>"
                                    readonly
                                    class="w-full rounded-lg border
                                           border-charcoal/20
                                           bg-gray-50 px-4 py-3
                                           text-sm text-charcoal
                                           outline-none"
                                >

                            </div>


                            <!-- Phone -->
                            <div>

                                <label
                                    for="phone"
                                    class="mb-2 block text-xs font-bold
                                           uppercase tracking-wider
                                           text-charcoal"
                                >
                                    Phone Number
                                </label>

                                <input
                                    id="phone"
                                    name="phone"
                                    type="text"
                                    value="<?= e($user->phone) ?>"
                                    class="w-full rounded-lg border
                                           border-charcoal/20
                                           bg-white px-4 py-3
                                           text-sm text-charcoal
                                           outline-none
                                           transition
                                           focus:border-forest
                                           focus:ring-1
                                           focus:ring-forest"
                                >

                            </div>


                            <!-- Date of Birth -->
                            <div>

                                <label
                                    for="dob"
                                    class="mb-2 block text-xs font-bold
                                           uppercase tracking-wider
                                           text-charcoal"
                                >
                                    Date of Birth
                                </label>

                                <input
                                    id="dob"
                                    name="dob"
                                    type="date"
                                    value="<?= e($user->dob) ?>"
                                    class="w-full rounded-lg border
                                           border-charcoal/20
                                           bg-white px-4 py-3
                                           text-sm text-charcoal
                                           outline-none
                                           transition
                                           focus:border-forest
                                           focus:ring-1
                                           focus:ring-forest"
                                >

                            </div>


                            <!-- City -->
                            <div>

                                <label
                                    for="city"
                                    class="mb-2 block text-xs font-bold
                                           uppercase tracking-wider
                                           text-charcoal"
                                >
                                    City
                                </label>

                                <input
                                    id="city"
                                    name="city"
                                    type="text"
                                    value="<?= e($user- data-validate="alpha">city) ?>"
                                    class="w-full rounded-lg border
                                           border-charcoal/20
                                           bg-white px-4 py-3
                                           text-sm text-charcoal
                                           outline-none
                                           transition
                                           focus:border-forest
                                           focus:ring-1
                                           focus:ring-forest"
                                >

                            </div>


                            <!-- State -->
                            <div>

                                <label
                                    for="state"
                                    class="mb-2 block text-xs font-bold
                                           uppercase tracking-wider
                                           text-charcoal"
                                >
                                    State
                                </label>

                                <input
                                    id="state"
                                    name="state"
                                    type="text"
                                    value="<?= e($user- data-validate="alpha">state) ?>"
                                    class="w-full rounded-lg border
                                           border-charcoal/20
                                           bg-white px-4 py-3
                                           text-sm text-charcoal
                                           outline-none
                                           transition
                                           focus:border-forest
                                           focus:ring-1
                                           focus:ring-forest"
                                >

                            </div>

                        </div>


                        <!-- Save / Cancel -->
                        <div
                            class="mt-6 flex flex-col md:flex-row gap-3 border-t border-charcoal/10
                                   pt-5"
                        >

                            <button
                                type="button"
                                id="cancelEditBtn"
                                class="w-full md:w-1/2 rounded-full border border-charcoal/20
                                       bg-white px-6 py-3 text-sm font-bold
                                       text-charcoal shadow-sm transition-colors
                                       hover:bg-gray-50"
                            >
                                Cancel
                            </button>

                            <button
                                type="button"
                                id="saveProfileBtn"
                                class="w-full md:w-1/2 rounded-full
                                       bg-forest px-6 py-3
                                       text-sm font-bold text-white
                                       shadow-sm transition-colors
                                       hover:bg-forest-dark"
                            >
                                Save Changes
                            </button>

                        </div>

                    </form>

                </section>



                <!--
                     ACCOUNT PREFERENCES
                     -->
                <section
                    class="w-full rounded-2xl border border-charcoal/10
                           bg-white p-5 md:p-8 shadow-sm
                           sm:p-6 lg:p-7"
                >

                    <h3
                        class="font-serif text-xl font-bold
                               uppercase tracking-wide text-forest"
                    >
                        Account Preferences
                    </h3>


                    <div class="mt-6">


                        <!-- Email Notifications -->
                        <div
                            class="flex flex-col md:flex-row items-start justify-between
                                   gap-4 border-b border-charcoal/10
                                   py-5 first:pt-0"
                        >

                            <div class="min-w-0 flex-1">

                                <h4 class="text-sm font-bold text-charcoal">
                                    Email Notifications
                                </h4>

                                <p class="mt-1 text-xs leading-5 text-charcoal-light">
                                    Receive general platform updates
                                    and newsletters.
                                </p>

                            </div>


                            <label
                                class="relative mt-1 inline-flex
                                       shrink-0 cursor-pointer
                                       items-center"
                            >

                                <input
                                    type="checkbox"
                                    class="peer sr-only"
                                    <?= e($preferences->email_notifications ? 'checked' : '') ?>
                                >

                                <div
                                    class="relative h-6 w-11 rounded-full
                                           bg-charcoal/20
                                           after:absolute after:left-[2px]
                                           after:top-[2px]
                                           after:h-5 after:w-5
                                           after:rounded-full
                                           after:border
                                           after:border-gray-300
                                           after:bg-white
                                           after:transition-all
                                           peer-checked:bg-forest
                                           peer-checked:after:translate-x-full
                                           peer-checked:after:border-white"
                                ></div>

                            </label>

                        </div>



                        <!-- Donation Updates -->
                        <div
                            class="flex flex-col md:flex-row items-start justify-between
                                   gap-4 border-b border-charcoal/10
                                   py-5"
                        >

                            <div class="min-w-0 flex-1">

                                <h4 class="text-sm font-bold text-charcoal">
                                    Donation Updates
                                </h4>

                                <p class="mt-1 text-xs leading-5 text-charcoal-light">
                                    Get receipts and impact reports
                                    for your contributions.
                                </p>

                            </div>


                            <label
                                class="relative mt-1 inline-flex
                                       shrink-0 cursor-pointer
                                       items-center"
                            >

                                <input
                                    type="checkbox"
                                    class="peer sr-only"
                                    <?= e($preferences->donation_updates ? 'checked' : '') ?>
                                >

                                <div
                                    class="relative h-6 w-11 rounded-full
                                           bg-charcoal/20
                                           after:absolute after:left-[2px]
                                           after:top-[2px]
                                           after:h-5 after:w-5
                                           after:rounded-full
                                           after:border
                                           after:border-gray-300
                                           after:bg-white
                                           after:transition-all
                                           peer-checked:bg-forest
                                           peer-checked:after:translate-x-full
                                           peer-checked:after:border-white"
                                ></div>

                            </label>

                        </div>



                        <!-- Event Notifications -->
                        <div
                            class="flex flex-col md:flex-row items-start justify-between
                                   gap-4 border-b border-charcoal/10
                                   py-5"
                        >

                            <div class="min-w-0 flex-1">

                                <h4 class="text-sm font-bold text-charcoal">
                                    Event Notifications
                                </h4>

                                <p class="mt-1 text-xs leading-5 text-charcoal-light">
                                    Be notified about upcoming fundraising
                                    events near you.
                                </p>

                            </div>


                            <label
                                class="relative mt-1 inline-flex
                                       shrink-0 cursor-pointer
                                       items-center"
                            >

                                <input
                                    type="checkbox"
                                    class="peer sr-only"
                                    <?= e($preferences->event_notifications ? 'checked' : '') ?>
                                >

                                <div
                                    class="relative h-6 w-11 rounded-full
                                           bg-charcoal/20
                                           after:absolute after:left-[2px]
                                           after:top-[2px]
                                           after:h-5 after:w-5
                                           after:rounded-full
                                           after:border
                                           after:border-gray-300
                                           after:bg-white
                                           after:transition-all
                                           peer-checked:bg-forest
                                           peer-checked:after:translate-x-full
                                           peer-checked:after:border-white"
                                ></div>

                            </label>

                        </div>



                        <div
                            class="flex flex-col md:flex-row items-start justify-between
                                   gap-4 py-5"
                        >

                            <div class="min-w-0 flex-1">

                                <h4 class="text-sm font-bold text-charcoal">
                                    NGO Activity Updates
                                </h4>

                                <p class="mt-1 text-xs leading-5 text-charcoal-light">
                                    Follow updates from NGOs you've supported.
                                </p>

                            </div>


                            <label
                                class="relative mt-1 inline-flex
                                       shrink-0 cursor-pointer
                                       items-center"
                            >

                                <input
                                    type="checkbox"
                                    class="peer sr-only"
                                    <?= e($preferences->ngo_activity_updates ? 'checked' : '') ?>
                                >

                                <div
                                    class="relative h-6 w-11 rounded-full
                                           bg-charcoal/20
                                           after:absolute after:left-[2px]
                                           after:top-[2px]
                                           after:h-5 after:w-5
                                           after:rounded-full
                                           after:border
                                           after:border-gray-300
                                           after:bg-white
                                           after:transition-all
                                           peer-checked:bg-forest
                                           peer-checked:after:translate-x-full
                                           peer-checked:after:border-white"
                                ></div>

                            </label>

                        </div>

                    </div>

                </section>

            </div>

        </div>



        <!--=======
             DANGER ZONE
            ======= -->
        <section
            class="mt-8 w-full rounded-2xl border
                   border-red-200 bg-white p-6 md:p-8 shadow-sm
                   sm:p-7 lg:p-8"
        >

            <h3
                class="font-serif text-xl font-bold
                       uppercase tracking-wide text-red-700"
            >
                Danger Zone
            </h3>


            <p
                class="mt-2 max-w-2xl text-sm leading-6
                       text-red-600/80"
            >
                Permanently delete your KarmaSetu account and associated
                personal data. This action cannot be undone.
            </p>


            <button
                type="button"
                class="mt-5 rounded-lg border-2
                       border-red-200 px-6 py-2.5
                       text-sm font-bold text-red-600
                       transition-colors
                       hover:border-red-300
                       hover:bg-red-50"
            >
                Delete Account
            </button>

        </section>

    </div>

</div>

<style>
    /* Fix for Edit Profile button hover */
    #editProfileBtn {
        background-color: transparent !important;
        color: #3F6B4F !important;
        border-color: #3F6B4F !important;
        transition: all 0.3s ease;
    }
    #editProfileBtn:hover {
        background-color: #3F6B4F !important;
        color: #FFFFFF !important;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const editBtn = document.getElementById('editProfileBtn');
    const displayMode = document.getElementById('profileDisplayMode');
    const editMode = document.getElementById('profileEditMode');
    const cancelBtn = document.getElementById('cancelEditBtn');
    const saveBtn = document.getElementById('saveProfileBtn');

    if (editBtn && displayMode && editMode && cancelBtn && saveBtn) {
        editBtn.addEventListener('click', function() {
            displayMode.classList.add('hidden');
            editMode.classList.remove('hidden');
            
            const card = editMode.closest('section');
            if (card) {
                card.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
        });

        cancelBtn.addEventListener('click', function() {
            editMode.classList.add('hidden');
            displayMode.classList.remove('hidden');
        });

        saveBtn.addEventListener('click', function() {
            alert('Profile saved successfully!');
            
            editMode.classList.add('hidden');
            displayMode.classList.remove('hidden');
        });
    }
});
</script>


<?php
$content = ob_get_clean();
require_once __DIR__ . '/../includes/layouts/main.php';
?>
