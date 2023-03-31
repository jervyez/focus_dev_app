<?php $this->users = new App\Modules\Users\Controllers\Users; ?>
<?php $this->user_model = new \App\Modules\Users\Models\users_m(); ?>


<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>TEST</title>
</head>
<body>

  <?php echo $this->users->test_partial(); ?>

  <p class="">
  Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
  tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
  quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
  consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse
  cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non
  proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
  </p>

  <?php 

      $result = $this->user_model->get_roles();
      $data_result = $result->getResult(); 

echo '<pre>';var_dump($data_result );echo '</pre>'; 
      ?>

</body>
</html>