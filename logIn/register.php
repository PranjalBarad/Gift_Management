
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Register page</title>
  <link rel="stylesheet" href="../../project/logIn/register2.css" />
</head>
<body>
  <section class="container">
    <form class="register-form" action="#" method="post">
      <h2>Register now </h2>
       <input type="text" placeholder="Name" name="name" required >
        <input type="email" placeholder="Email" name="email" required >
        <input type="password" placeholder="Password" name="password" required />
        <input type="password" placeholder="Confirm password" name="cpassword" required />
        <button type="submit" class="btn" value="Register now"  name="submit-btn">Register</button>
      <p class="login-link">Already have an account?  <a href="log.php"> LogIn  now</a></p>
    </form>
</section>
</body>
</html>
