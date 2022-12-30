<?php $this->layout('admin::admin-layout', ['title' => 'Admin - New User']) ?>

<h2>New User</h2>
<form action="/admin/users" method="POST">
  <?php if(isset($error)): ?>
    <div class="mb-3">
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= $this->e($error)?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    </div>
  <?php endif; ?>
  <?php if(isset($result)): ?>
    <div class="mb-3">
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= $this->e($result)?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    </div>
  <?php endif; ?>
  <div class="mb-3">
    <label for="username" class="form-label">Username</label>
    <input type="text" class="form-control <?= isset($formErrors['username']) ? 'is-invalid' : ''?>" id="username" name="username" aria-describedby="usernameHelp" value="<?= $this->e($username ?? '') ?>" required>
    <div id="usernamelHelp" class="form-text">A username can be also an email address.</div>
    <div id="validationServerUsername" class="invalid-feedback">
      <?= $this->e($formErrors['username'] ?? '')?>
    </div> 
  </div>
  <div class="mb-3">
    <label for="password" class="form-label">Password</label>
    <input type="password" class="form-control <?= isset($formErrors['password']) ? 'is-invalid' : ''?>" id="password" name="password" aria-describedby="passwordHelp" required>
    <div id="passwordlHelp" class="form-text">Your password must be at least 10 characters long</div>
    <div id="validationServerPassword" class="invalid-feedback">
      <?= $this->e($formErrors['password'] ?? '')?>
    </div>
  </div>
  <div class="mb-3">
    <label for="confirmPassword" class="form-label">Confirm Password</label>
    <input type="password" class="form-control <?= isset($formErrors['password']) ? 'is-invalid' : ''?>" id="confirmPassword" name="confirmPassword" aria-describedby="confirmHelp" required>
    <div id="confirmHelp" class="form-text">Re-enter the password to confirm</div>
  </div>
  <button type="button" class="btn btn-secondary" id="cancel">Cancel</button>
  <button type="submit" class="btn btn-primary">Save</button>
</form>

<?php $this->push('js') ?>
<script type="text/javascript">
  var cancelButton = document.getElementById('cancel')
  cancelButton.addEventListener('click', function(e) {
    window.location.href = '/admin/users'
  })
</script>
<?php $this->end() ?>