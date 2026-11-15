<?php
require_once __DIR__ . '/../includes/functions.php';
requireUser();
require_once __DIR__ . '/../Database/db_config.php';

$userId = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : 0;
if ($userId <= 0) {
    header('Location: ' . baseUrl('auth/login.php'));
    exit;
}

// 1. Fetch current user from users table
$userQuery = mysqli_query($con, "SELECT id, name, email, phone, role, status FROM users WHERE id = $userId AND role = 'user' LIMIT 1");
if (!$userQuery || mysqli_num_rows($userQuery) === 0) {
    header('Location: ' . baseUrl('auth/logout.php'));
    exit;
}
$existingUser = mysqli_fetch_assoc($userQuery);

// 2. Fetch or initialize user_profiles record
$profileQuery = mysqli_query($con, "SELECT * FROM user_profiles WHERE user_id = $userId LIMIT 1");
if ($profileQuery && mysqli_num_rows($profileQuery) > 0) {
    $existingProfile = mysqli_fetch_assoc($profileQuery);
} else {
    // Missing profile record: safely create it with schema defaults
    $initStmt = mysqli_prepare($con, "INSERT INTO user_profiles (user_id) VALUES (?)");
    if ($initStmt) {
        mysqli_stmt_bind_param($initStmt, "i", $userId);
        mysqli_stmt_execute($initStmt);
        mysqli_stmt_close($initStmt);
    }
    $profileQuery = mysqli_query($con, "SELECT * FROM user_profiles WHERE user_id = $userId LIMIT 1");
    $existingProfile = ($profileQuery && mysqli_num_rows($profileQuery) > 0) ? mysqli_fetch_assoc($profileQuery) : [];
}

$successMsg = '';
$errorMsg = '';
$isEditMode = false;

// Check for session flash messages
if (!empty($_SESSION['profile_success'])) {
    $successMsg = $_SESSION['profile_success'];
    unset($_SESSION['profile_success']);
}

// Default form field values from database
$formName = $existingUser['name'] ?? '';
$formPhone = $existingUser['phone'] ?? '';
$formDob = $existingProfile['dob'] ?? '';
$formCity = $existingProfile['city'] ?? '';
$formState = $existingProfile['state'] ?? '';

// 3. Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $isEditMode = true;

    $submittedName = trim($_POST['name'] ?? '');
    $submittedPhone = trim($_POST['phone'] ?? '');
    $submittedDob = trim($_POST['dob'] ?? '');
    $submittedCity = trim($_POST['city'] ?? '');
    $submittedState = trim($_POST['state'] ?? '');

    // Keep submitted values in form inputs on validation failure
    $formName = ($submittedName !== '') ? $submittedName : $existingUser['name'];
    $formPhone = $submittedPhone;
    $formDob = $submittedDob;
    $formCity = $submittedCity;
    $formState = $submittedState;

    // Check for empty form submission
    if ($submittedName === '' && $submittedPhone === '' && $submittedDob === '' && $submittedCity === '' && $submittedState === '') {
        $errorMsg = 'Please enter valid information to update your profile.';
    }

    // Server-side validation
    $nameToSave = $existingUser['name'];
    if ($errorMsg === '' && $submittedName !== '') {
        if (strlen($submittedName) < 2 || strlen($submittedName) > 150) {
            $errorMsg = 'Full Name must be between 2 and 150 characters.';
        } elseif (!preg_match("/^[a-zA-Z\s]+$/", $submittedName)) {
            $errorMsg = 'Full Name can only contain letters and spaces.';
        } else {
            $nameToSave = $submittedName;
        }
    }

    $phoneToSave = ($submittedPhone !== '') ? $submittedPhone : ($existingUser['phone'] ?? null);
    if ($errorMsg === '' && $submittedPhone !== '') {
        $digitsOnly = preg_replace('/[^0-9]/', '', $submittedPhone);
        if (strlen($submittedPhone) > 20) {
            $errorMsg = 'Phone number cannot exceed 20 characters.';
        } elseif (strlen($digitsOnly) < 10 || strlen($digitsOnly) > 15) {
            $errorMsg = 'Phone number must contain between 10 and 15 digits.';
        } elseif (!preg_match("/^[\+]?[0-9\s\-]+$/", $submittedPhone)) {
            $errorMsg = 'Phone number can only contain digits, spaces, hyphens, and optional leading plus sign.';
        } else {
            $phoneToSave = $submittedPhone;
        }
    }

    $dobToSave = ($submittedDob !== '') ? $submittedDob : ($existingProfile['dob'] ?? null);
    if ($errorMsg === '' && $submittedDob !== '') {
        $d = DateTime::createFromFormat('Y-m-d', $submittedDob);
        if (!$d || $d->format('Y-m-d') !== $submittedDob) {
            $errorMsg = 'Please enter a valid date of birth (YYYY-MM-DD).';
        } elseif ($d > new DateTime('today')) {
            $errorMsg = 'Date of birth cannot be in the future.';
        } elseif ($d < new DateTime('1900-01-01')) {
            $errorMsg = 'Date of birth must be after 1900.';
        } else {
            $dobToSave = $submittedDob;
        }
    }

    $cityToSave = ($submittedCity !== '') ? $submittedCity : ($existingProfile['city'] ?? null);
    if ($errorMsg === '' && $submittedCity !== '') {
        if (strlen($submittedCity) < 2 || strlen($submittedCity) > 100) {
            $errorMsg = 'City must be between 2 and 100 characters.';
        } elseif (!preg_match("/^[a-zA-Z\s]+$/", $submittedCity)) {
            $errorMsg = 'City can only contain letters and spaces.';
        } else {
            $cityToSave = $submittedCity;
        }
    }

    $stateToSave = ($submittedState !== '') ? $submittedState : ($existingProfile['state'] ?? null);
    if ($errorMsg === '' && $submittedState !== '') {
        if (strlen($submittedState) < 2 || strlen($submittedState) > 100) {
            $errorMsg = 'State must be between 2 and 100 characters.';
        } elseif (!preg_match("/^[a-zA-Z\s]+$/", $submittedState)) {
            $errorMsg = 'State can only contain letters and spaces.';
        } else {
            $stateToSave = $submittedState;
        }
    }

    // Execute updates using procedural MySQLi prepared statements
    if ($errorMsg === '') {
        $userStmt = mysqli_prepare($con, "UPDATE users SET name = ?, phone = ?, updated_at = NOW() WHERE id = ?");
        $userUpdated = false;
        if ($userStmt) {
            mysqli_stmt_bind_param($userStmt, "ssi", $nameToSave, $phoneToSave, $userId);
            $userUpdated = mysqli_stmt_execute($userStmt);
            mysqli_stmt_close($userStmt);
        }

        $profileStmt = mysqli_prepare($con, "UPDATE user_profiles SET dob = ?, city = ?, state = ?, updated_at = NOW() WHERE user_id = ?");
        $profileUpdated = false;
        if ($profileStmt) {
            mysqli_stmt_bind_param($profileStmt, "sssi", $dobToSave, $cityToSave, $stateToSave, $userId);
            $profileUpdated = mysqli_stmt_execute($profileStmt);
            mysqli_stmt_close($profileStmt);
        }

        if ($userUpdated && $profileUpdated) {
            $_SESSION['user_name'] = $nameToSave;
            $_SESSION['profile_success'] = 'Profile updated successfully!';
            header('Location: ' . baseUrl('user/profile.php'));
            exit;
        } else {
            $errorMsg = 'Failed to save profile changes. Please try again.';
        }
    }
}

// 4. Calculate dynamic statistics
$karmaPoints = (int) ($existingProfile['karma_points'] ?? 0);

$rankQuery = mysqli_query($con, "SELECT COUNT(*) AS higher_count FROM user_profiles WHERE karma_points > $karmaPoints");
$rankRow = $rankQuery ? mysqli_fetch_assoc($rankQuery) : null;
$userRank = ($rankRow ? (int) $rankRow['higher_count'] : 0) + 1;

$contribQuery = mysqli_query($con, "SELECT COUNT(*) AS total FROM contributions WHERE user_id = $userId AND payment_status IN ('completed', 'not_applicable') AND fulfillment_status != 'cancelled'");
$contribRow = $contribQuery ? mysqli_fetch_assoc($contribQuery) : null;
$totalContributions = $contribRow ? (int) $contribRow['total'] : 0;

$volQuery = mysqli_query($con, "SELECT COALESCE(SUM(hours_completed), 0) AS total_hours FROM volunteer_activities WHERE user_id = $userId AND status = 'completed'");
$volRow = $volQuery ? mysqli_fetch_assoc($volQuery) : null;
$volunteerHours = ($volRow ? (float) $volRow['total_hours'] : 0) . 'h';

$eventsQuery = mysqli_query($con, "SELECT COUNT(DISTINCT event_id) AS total_events FROM event_bookings WHERE user_id = $userId AND booking_status IN ('confirmed', 'attended')");
$eventsRow = $eventsQuery ? mysqli_fetch_assoc($eventsQuery) : null;
$eventsParticipated = $eventsRow ? (int) $eventsRow['total_events'] : 0;

// 5. Construct view objects matching existing UI structure
$userName = $existingUser['name'] ?? '';
$userInitials = strtoupper(substr(trim($userName ?: 'U'), 0, 1));

$user = (object)[
    'name' => $userName,
    'email' => $existingUser['email'] ?? '',
    'phone' => !empty($existingUser['phone']) ? $existingUser['phone'] : '',
    'dob' => !empty($existingProfile['dob']) ? $existingProfile['dob'] : '',
    'city' => !empty($existingProfile['city']) ? $existingProfile['city'] : '',
    'state' => !empty($existingProfile['state']) ? $existingProfile['state'] : '',
    'role' => 'Member',
    'initials' => $userInitials
];

$profileStats = (object)[
    'karma_points' => number_format($karmaPoints),
    'rank' => $userRank,
    'contributions' => $totalContributions,
    'volunteer_hours' => $volunteerHours,
    'events_participated' => $eventsParticipated
];

$preferences = (object)[
    'email_notifications' => !empty($existingProfile['email_notifications']),
    'donation_updates' => !empty($existingProfile['donation_updates']),
    'event_notifications' => !empty($existingProfile['event_notifications']),
    'ngo_activity_updates' => !empty($existingProfile['ngo_activity_updates'])
];

ob_start();
?>


<div class="min-h-screen bg-ivory/40 py-10 lg:py-14">

    <!--MAIN CONTAINER -->
    <div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

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

        <?php if (!empty($successMsg)): ?>
            <div class="mb-6 rounded-xl border border-forest/20 bg-forest/10 p-4 text-sm font-medium text-forest flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <svg class="h-5 w-5 text-forest shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span><?= e($successMsg) ?></span>
                </div>
            </div>
        <?php endif; ?>

        <?php if (!empty($errorMsg)): ?>
            <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 text-sm font-medium text-red-700 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <svg class="h-5 w-5 text-red-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span><?= e($errorMsg) ?></span>
                </div>
            </div>
        <?php endif; ?>
       
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
                                    class="mt-1 text-xs font-bold
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
                                    class="mt-1 text-xs font-bold
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


                    <div id="profileDisplayMode" class="mt-4 <?= $isEditMode ? 'hidden' : '' ?>">
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
                            <span class="mt-1 text-sm text-charcoal-light"><?= e($user->phone ?: 'Not provided') ?></span>
                        </div>
                        <div class="flex flex-col border-b border-charcoal/5 py-3">
                            <span class="text-xs font-bold uppercase tracking-wider text-charcoal">Date of Birth</span>
                            <span class="mt-1 text-sm text-charcoal-light"><?= e($user->dob ?: 'Not provided') ?></span>
                        </div>
                        <div class="flex flex-col border-b border-charcoal/5 py-3">
                            <span class="text-xs font-bold uppercase tracking-wider text-charcoal">City</span>
                            <span class="mt-1 text-sm text-charcoal-light"><?= e($user->city ?: 'Not provided') ?></span>
                        </div>
                        <div class="flex flex-col py-3">
                            <span class="text-xs font-bold uppercase tracking-wider text-charcoal">State</span>
                            <span class="mt-1 text-sm text-charcoal-light"><?= e($user->state ?: 'Not provided') ?></span>
                        </div>
                    </div>


                    <form
                        id="profileEditMode"
                        action="<?= baseUrl('user/profile.php') ?>"
                        method="POST"
                        class="mt-6 <?= $isEditMode ? '' : 'hidden' ?> validation-form"
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
                                    value="<?= e($formName) ?>"
                                    data-validate="alpha"
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
                                    value="<?= e($user->email) ?>"
                                    data-validate="email"
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
                                    value="<?= e($formPhone) ?>"
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
                                    value="<?= e($formDob) ?>"
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
                                    value="<?= e($formCity) ?>"
                                    data-validate="alpha"
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
                                    value="<?= e($formState) ?>"
                                    data-validate="alpha"
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
                                type="submit"
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

    if (editBtn && displayMode && editMode && cancelBtn) {
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
    }
});
</script>


<?php
$content = ob_get_clean();
require_once __DIR__ . '/../includes/layouts/main.php';
?>
