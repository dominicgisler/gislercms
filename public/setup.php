<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="Gisler Software">

    <title>Setup - GislerCMS</title>

    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="css/login.css" rel="stylesheet">
</head>
<body>
    <form id="setup-form" class="form-signin">
        <div class="text-center mb-4">
            <img class="mb-4" src="img/logo-black.png" alt="" width="72" height="72">
            <h1 class="h3 mb-3 font-weight-normal">GislerCMS - Setup</h1>
        </div>
        <div class="collapse show multi-collapse" id="step1">
            <div class="text-center mb-4">
                <h2 class="h4 mb-3 font-weight-normal">Step 1</h2>
                <p>
                    Please provide your database connection data below.
                </p>
            </div>
            <div class="form-label-group">
                <input type="text" id="dbHost" name="db_host" class="form-control" placeholder="Host" value="localhost" required autofocus>
                <label for="dbHost">Host</label>
            </div>
            <div class="form-label-group">
                <input type="text" id="dbDatabase" name="db_database" class="form-control" placeholder="Database" required>
                <label for="dbDatabase">Database</label>
            </div>
            <div class="form-label-group">
                <input type="text" id="dbUser" name="db_user" class="form-control" placeholder="Username" required>
                <label for="dbUser">Username</label>
            </div>
            <div class="form-label-group">
                <input type="password" id="dbPassword" name="db_password" class="form-control" placeholder="Password" required>
                <label for="dbPassword">Password</label>
            </div>
            <button class="btn btn-lg btn-primary btn-block" type="button" data-toggle="collapse" data-target=".multi-collapse" aria-expanded="false" aria-controls="step1 step2">Next</button>
        </div>
        <div class="collapse multi-collapse" id="step2">
            <div class="text-center mb-4">
                <h2 class="h4 mb-3 font-weight-normal">Step 2</h2>
                <p>
                    How would you like to log in?
                </p>
            </div>
            <div class="form-label-group">
                <input type="text" id="userUsername" name="user_username" class="form-control" placeholder="Username" required>
                <label for="userUsername">Username</label>
            </div>
            <div class="d-flex">
                <div class="form-label-group w-50 mr-1">
                    <input type="text" id="userFirstname" name="user_firstname" class="form-control" placeholder="Username" required>
                    <label for="userLastname">Firstname</label>
                </div>
                <div class="form-label-group w-50 ml-1">
                    <input type="text" id="userLastname" name="user_lastname" class="form-control" placeholder="Username" required>
                    <label for="userLastname">Lastname</label>
                </div>
            </div>
            <div class="form-label-group">
                <input type="email" id="userEmail" name="user_email" class="form-control" placeholder="Email" required>
                <label for="userEmail">Email</label>
            </div>
            <div class="form-label-group">
                <input type="text" id="userPassword" name="user_password" class="form-control" placeholder="Password" required>
                <label for="userPassword">Password</label>
            </div>

            <button class="btn btn-lg btn-primary btn-block" type="button" data-toggle="collapse" data-target=".multi-collapse" aria-expanded="false" aria-controls="step1 step2">Back</button>
            <button class="btn btn-lg btn-primary btn-block" type="submit">Setup</button>
        </div>
        <p class="mt-5 mb-3 text-muted text-center">&copy; <?php echo date('Y'); ?> Gisler Software</p>
    </form>
    <script src="js/jquery-3.3.1.slim.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>
