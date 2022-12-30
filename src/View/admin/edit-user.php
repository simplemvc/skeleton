<?php $this->layout('admin::admin-layout', ['title' => 'Admin - Edit User']) ?>

<h2>Edit User</h2>
<form action="/admin/users/<?= $this->e($user->id)?>" method="POST">
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
    <input type="text" class="form-control" id="username" name="username" aria-describedby="usernameHelp" value="<?= $this->e($user->username) ?>" disabled>
    <div id="usernamelHelp" class="form-text">You cannot change the username</div>
  </div>
  <div class="mb-3">
    <div class="form-check form-switch">
      <input class="form-check-input" type="checkbox" id="activeChecked" name="active" <?= $user->active ? 'checked' : ''?>>
      <label class="form-check-label" for="activeChecked">Active</label>
    </div>
  </div>
  <div class="mb-3">
    <div class="accordion" id="accordionExample">
      <div class="accordion-item">
        <h2 class="accordion-header" id="headingOne">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-controls="collapseOne" aria-expanded="<?= empty($formErrors) ? 'false' : 'true'?>">
            Change Password
          </button>
        </h2>
        <div id="collapseOne" class="accordion-collapse <?= empty($formErrors) ? 'collapse' : ''?>" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
          <div class="accordion-body">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control <?= isset($formErrors['password']) ? 'is-invalid' : ''?>" id="password" name="password" aria-describedby="passwordHelp">
            <div id="passwordlHelp" class="form-text">Your password must be at least 10 characters long</div>
            <div id="validationServerPassword" class="invalid-feedback">
              <?= $this->e($formErrors['password'] ?? '')?>
            </div>
            <label for="confirmPassword" class="form-label">Confirm Password</label>
            <input type="password" class="form-control <?= isset($formErrors['password']) ? 'is-invalid' : ''?>" id="confirmPassword" name="confirmPassword" aria-describedby="confirmHelp">
            <div id="confirmHelp" class="form-text">Re-enter the password to confirm</div>
          </div>
        </div>
      </div>
    </div>
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