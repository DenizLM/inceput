<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <!-- Primary Meta Tags -->
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Fontawesome -->
    <link type="text/css" href="vendor/@fortawesome/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Pixel CSS -->
    <link type="text/css" href="css/pixel.css" rel="stylesheet">

</head>

<body>


<header class="header-global">

</header>
<main style="max-height: 95vh;min-height: 95vh; background-color: rgba(62,84,172,0.09); overflow-y: scroll">
    @yield('content')
</main>

<!-- Core -->
<script src="./vendor/@popperjs/core/dist/umd/popper.min.js"></script>
<script src="./vendor/bootstrap/dist/js/bootstrap.min.js"></script>
<script src="./vendor/headroom.js/dist/headroom.min.js"></script>

<!-- Vendor JS -->
<script src="./vendor/onscreen/dist/on-screen.umd.min.js"></script>
<script src="./vendor/jarallax/dist/jarallax.min.js"></script>
<script src="./vendor/smooth-scroll/dist/smooth-scroll.polyfills.min.js"></script>
<script src="./vendor/vivus/dist/vivus.min.js"></script>
<script src="./vendor/vanillajs-datepicker/dist/js/datepicker.min.js"></script>

<script async defer src="https://buttons.github.io/buttons.js"></script>

<!-- Pixel JS -->
<script src="./assets/js/pixel.js"></script>
@yield('scripts')
</body>
<footer style="background-color: rgba(62,84,172,0.09); height: 5vh">
    <div class="text-center pt-2">
        <p>Powered by <a href="">Open data</a> & coffee</p>
    </div>
</footer>
</html>
