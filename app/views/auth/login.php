<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Login | POS App</title>

    <!-- SB Admin 2 CSS -->
    <link href="/pos/public/template/sb-admin-2/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="/pos/public/template/sb-admin-2/css/sb-admin-2.min.css" rel="stylesheet">
</head>

<body class="bg-gradient-primary">

<div class="container">

    <!-- Outer Row -->
    <div class="row justify-content-center">

        <div class="col-xl-4 col-lg-6 col-md-8">

            <div class="card o-hidden border-0 shadow-lg my-5">
                <div class="card-body p-4">

                    <div class="text-center">
                        <h1 class="h4 text-gray-900 mb-4">POS Login</h1>
                    </div>

                    <!-- ERROR MESSAGE -->
                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger">
                            <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                        </div>
                    <?php endif; ?>
<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success">
        <?= $_SESSION['success']; unset($_SESSION['success']); ?>
    </div>
<?php endif; ?>

                    <form class="user"
      method="post"
      action="/pos/public/?controller=auth&action=authenticate"
      autocomplete="off">

    <div class="form-group">
        <input type="text"
               name="username"
               class="form-control form-control-user"
               placeholder="Username"
               autocomplete="off"
               required>
    </div>

    <div class="form-group">
        <input type="password"
               name="password"
               class="form-control form-control-user"
               placeholder="Password"
               autocomplete="new-password"
               required>
    </div>

                        <button type="submit"
                                class="btn btn-primary btn-user btn-block">
                            Login
                        </button>

                    </form>
<div class="text-center">
    <a class="small" href="/pos/public/?controller=auth&action=register">
        Belum punya akun? Daftar
    </a>
</div>
                </div>
            </div>

        </div>

    </div>

</div>

<!-- SB Admin 2 JS -->
<script src="/pos/public/template/sb-admin-2/vendor/jquery/jquery.min.js"></script>
<script src="/pos/public/template/sb-admin-2/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="/pos/public/template/sb-admin-2/js/sb-admin-2.min.js"></script>

</body>
</html>
