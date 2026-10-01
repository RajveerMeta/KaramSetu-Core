<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard - KarmaSetu</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">

    <script>
        function jsBaseUrl(path) {
            return '<?= baseUrl('') ?>' + path.replace(/^\/+/, '');
        }
    </script>
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
    <script src="<?= asset('js/jquery.min.js') ?>"></script>
    <script src="<?= asset('js/validation.js') ?>" defer></script>
    <script src="<?= asset('js/auth.js') ?>" defer></script>
    <script src="<?= asset('js/navigation.js') ?>" defer></script>
    <script src="<?= asset('js/causes.js') ?>" defer></script>
    <script src="<?= asset('js/ngo.js') ?>" defer></script>
    <script src="<?= asset('js/events.js') ?>" defer></script>
    <script src="<?= asset('js/user-events.js') ?>" defer></script>
    <script src="<?= asset('js/contributions.js') ?>" defer></script>
    <script src="<?= asset('js/route-protection.js') ?>" defer></script>

    <style>
        /* Bridge visual styles */
        .bridge-line {
            position: absolute;
            z-index: 0;
            border: 1px dashed var(--color-gold);
            opacity: 0.5;
        }
        .bridge-path {
            stroke: var(--color-gold);
            stroke-width: 1.5;
            stroke-dasharray: 4 4;
            fill: none;
            opacity: 0.6;
        }
    </style>
</head>
<body class="bg-ivory text-charcoal font-sans antialiased">
    
    <?php require_once __DIR__ . '/../components/admin/navbar.php'; ?>

    <main>
        <?= $content ?? '' ?>
    </main>

    <?php require_once __DIR__ . '/../components/common/footer.php'; ?>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</body>
</html>
