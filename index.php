<?php

require 'src/header.php';

if (!isLoggedIn()) {
    include 'src/views/partial/login-form.php';
} else {
    include 'src/views/partial/dashboard.php';
}

include 'src/footer.php';
