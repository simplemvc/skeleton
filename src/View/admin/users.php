<?php $this->layout('admin::admin-layout', ['title' => 'Admin - Users']) ?>

<h2>Users <form action="/admin/users" method="POST" style="float:right"><button type="submit" class="btn btn-primary"><i class="bi bi-person-plus"></i> Add user</button></form></h2>
<table class="table table-hover">
  <thead>
    <tr>
      <th scope="col">#</th>
      <th scope="col">Username</th>
      <th scope="col">Active</th>
      <th scope="col">Last login</th>
      <th scope="col"></th>
    </tr>
  </thead>
  <tbody>
    <?php 
      $i = $start;
      $zero = strlen($total);
    ?>
    <?php foreach($users as $user): ?>
    <tr>
      <td><?= sprintf("%0{$zero}d", ++$i)?></td>
      <td><?= $this->e($user->username)?></td>
      <td><?= $user->active > 0 ? '<i class="bi bi-check text-success"></i>' : '<i class="bi bi-x text-danger"></i>'?></td>
      <td><?= $this->e($user->last_login)?></td>
      <td>
        <a href="/admin/users/<?= $this->e($user->id) ?>" class="no-underline">
          <i class="bi bi-pencil-square"></i> Edit
        </a> - 
        <a href="#" data-bs-toggle="modal" data-bs-target="#confirmModal" class="no-underline" data-username="<?= $this->e($user->username)?>" data-id="<?= $this->e($user->id)?>">
          <i class="bi bi-trash"></i> Delete
        </a>
      </td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>
<div class="row">
  <p>Total number of users: <strong><?= $this->e($total)?></strong></p>
</div>
<nav aria-label="Page navigation">
  <ul class="pagination justify-content-center">
    <?php $this->insert('admin::pagination', [ 
      'start'    => $start, 
      'size'     => $size, 
      'total'    => $total,
      'url'      => '/admin/users',
      'numItems' => 5
    ]); ?>
  </ul>
</nav>
<!-- Modal -->
<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="confirmModalLabel">Confirm delete</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Are you sure to delete user <span id="username"></span>?<br />
        Please note, this action cannot be undone.
      </div>
      <div class="modal-footer">
        <div id="deleteError"></div>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-danger" id="confirmDelete" data-id="">Confirm</button>
      </div>
    </div>
  </div>
</div>

<?php $this->push('js') ?>
<script type="text/javascript">

  var confirmModal = document.getElementById('confirmModal')
  confirmModal.addEventListener('show.bs.modal', function (e) {
    document.getElementById('deleteError').style.visibility = "hidden"
    document.getElementById('confirmDelete').disabled = false
    document.getElementById('username').innerHTML = e.relatedTarget.attributes['data-username'].value
    document.getElementById('confirmDelete').setAttribute('data-id', e.relatedTarget.attributes['data-id'].value)
  })

  var confirmDelete = document.getElementById('confirmDelete')
  confirmDelete.addEventListener('click', function(e) {
    var xhr = new XMLHttpRequest()
    xhr.open('DELETE', '/admin/users/' + e.target.attributes['data-id'].value)
    xhr.onload = function () {
      if (xhr.readyState == 4 && xhr.status == "200") {
        location.reload()
      } else {
        var response = JSON.parse(xhr.responseText)
        document.getElementById('confirmDelete').disabled = true
        document.getElementById('deleteError').innerHTML = response.error
        document.getElementById('deleteError').style.visibility = "visible"
      }
    }
    xhr.send()
  })

</script>
<?php $this->end() ?>