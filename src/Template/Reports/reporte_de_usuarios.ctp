<div class="row">
  <div class="col-md-12">
    <table class="table table-bordered table-hover table-striped">
      <thead>
        <tr>
          <th>Usuario</th>
          <th>AFP - Fondo</th>
          <th>Fuente</th>
          <th>Fecha Registro</th>
          <th>&Uacute;ltimo Login</th>
          <th>&iquest;Registra cambios&quest;</th>
          <th class="text-center">Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($users as $user) : ?>
        <tr>
          <td><?php echo $user['first_name']; ?> <?php echo $user['last_name']; ?><br/><?php echo $user['email']; ?></td>
          <td><?php echo $user['afp_name']; ?> - <?php echo $user['fondo_name']; ?></td>
          <td><?php echo ucwords($user['social_source']); ?></td>
          <td><?php echo $user['created']->format('Y-m-d H:i:s'); ?></td>
          <td><?php echo $user['login_dt'] ? $user['login_dt']->format('Y-m-d H:i:s') : '-'; ?></td>
          <td><?php echo $user['changes_count'] > 0 ? 'Si' : 'No'; ?></td>
          <td>
            <a href="/users/remove/<?php echo $user['id']; ?>">Remove</a>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
