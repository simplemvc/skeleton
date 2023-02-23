<?php $this->layout('layout', ['title' => 'Login']) ?>

<main class="form-signin">
  <form method="POST" action="<?= $this->e($login_url) ?>">
    <img class="mb-4" src="/img/logo.png" alt="" width="100%">
    <p>User: <i>admin</i>, Password: <i>supersecret</i></p>
    <div class="form-floating">
      <input type="text" class="form-control" id="username" name="username" placeholder="Username">
      <label for="username">Username</label>
    </div>
    <div class="form-floating">
      <input type="password" class="form-control" id="password" name="password" placeholder="Password">
      <label for="password">Password</label>
    </div>

    <?php if (isset($error)): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <?= $this->e($error)?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>

    <button class="w-100 btn btn-lg btn-primary" type="submit">Login</button>
    <p class="mt-5 mb-3 text-muted">&copy; <?= date('Y')?> <a href="https://github.com/simplemvc">SimpleMVC</a></p>
  </form>
</main>

<?php $this->push('css') ?>
<link href="/css/login.css" rel="stylesheet">
<?php $this->end() ?>