<table class="table table-striped table-hover table-bordered">
  <thead>
    <tr>
      <th class="text-center">Posici&oacute;n</th>
      <th class="text-center">Nombre</th>
      <th class="text-center">Rentabilidad Nominal</th>
      <th class="text-center">Rentabilidad Promedio</th>
      <th class="text-center">Consistencia</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach($ranking as $rank) : ?>
    <tr>
      <td class="text-center"><?php echo $rank['puesto']; ?>&deg;</td>
      <td class="text-center">
        <a href="/profile/<?php echo $rank['user']['uuid']; ?>">
        <?php if (!empty($rank['user']['first_name']) || !empty($rank['user']['last_name'])) : ?>
          <?php echo $rank['user']['first_name']; ?> <?php echo $rank['user']['last_name']; ?>
        <?php else : ?>
          <i>Usuario an&oacute;nimo</i>
        <?php endif; ?>
        </a>
      </td>
      <td class="text-center<?php if ($rank['rentabilidad'] >= 0) : ?> text-success<?php else : ?> text-danger<?php endif; ?>">
        <b><?php echo number_format(round($rank['rentabilidad'],2), 2); ?>%</b>
      </td>
      <td class="text-center<?php if ($rank['performance'] >= 0) : ?> text-success<?php else : ?> text-danger<?php endif; ?>">
        <b><?php echo number_format(round($rank['performance'],2), 2); ?>%</b>
      </td>
      <td class="text-center<?php if ($rank['consistencia'] >= 0) : ?> text-success<?php else : ?> text-danger<?php endif; ?>">
        <b><?php echo number_format(round($rank['consistencia'],2), 2); ?>%</b>
      </td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>