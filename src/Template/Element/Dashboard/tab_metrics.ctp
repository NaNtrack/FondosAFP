<div class="row">
  <?php if (!empty($metrics['ganancia'])) : ?>
  <?php foreach ($metrics['ganancia'] as $ganancia) : ?>
  <div class="col-md-4 col-md-push-0 col-sm-5 col-sm-push-1 col-xs-10 col-xs-push-1">
    <div class="jumbotron text-center<?php if($ganancia['valor']>=0) : ?> jumbotron-green<?php else : ?> jumbotron-red<?php endif ?>">
      <h1 data-toggle="tooltip" data-placement="top" title="Monto ganado/perdido desde el último cambio (se actualiza diariamente)">Ultimo Cambio</h1>
      <?php if (!empty($ganancia)) : ?>
        <p class="<?php if($ganancia['valor'] > 0) : ?>text-success<?php else : ?>text-warning<?php endif; ?>"><b>$<?php echo round($ganancia['valor'], 2); ?> / cuota</b></p>
        <p>
          <span class="fondo fondo_<?php echo $ganancia['lastChange']['from_fondo']['name']; ?>">
              <?php echo $ganancia['lastChange']['from_fondo']['name']; ?>
          </span>
          <i class="glyphicon glyphicon-arrow-right"></i>
          <span class="fondo fondo_<?php echo $ganancia['lastChange']['to_fondo']['name']; ?>">
              <?php echo $ganancia['lastChange']['to_fondo']['name']; ?>
          </span>
        </p>
        <p>desde <?php echo date_format($ganancia['lastChange']['cuota_dt'], 'Y-m-d'); ?></p>
        <p><a class="btn btn-success btn-lg" href="/fondos?desde=<?php echo date_format($ganancia['lastChange']['cuota_dt'], 'Y-m-d') ?> &hasta=<?php echo date("Y-m-d"); ?>" role="button">Ver gr&aacute;fico</a></p>
      <?php else : ?>
        <p class="text-xxl">0</p>
        <p>Sin información</p>
      <?php endif; ?>
    </div>
  </div>
  <?php endforeach; ?>
  <?php endif; ?>
  <div class="col-md-4 col-md-push-0 col-sm-5 col-sm-push-1 col-xs-10 col-xs-push-1">
    <div class="jumbotron text-center<?php if($metrics['rentabilidad']>=0) : ?> jumbotron-green<?php else : ?> jumbotron-red<?php endif ?>">
      <h1 data-toggle="tooltip" data-placement="top" title="Rentabilidad nominal, suma de todas las rentabilidades obtenidas en el tiempo, incluyendo la del último cambio">Rentabilidad</h1>
      <p class="text-xxl"><?php echo number_format(round($metrics['rentabilidad'],2), 2); ?>%</p>
      <p>Rentabilidad nominal</p>
    </div>
  </div>
  <div class="col-md-4 col-md-push-0 col-sm-5 col-sm-push-1 col-xs-10 col-xs-push-1">
    <div class="jumbotron text-center<?php if($metrics['rendimiento']>=0) : ?> jumbotron-green<?php else : ?> jumbotron-red<?php endif ?>">
      <h1 data-toggle="tooltip" data-placement="top" title="Rentabilidad promedio obtenida, no considera los cambios entre el mismo fondo">Rentabilidad</h1>
      <p class="text-xxl"><?php echo number_format($metrics['rendimiento'], 2); ?>%</p>
      <p>Rentabilidad promedio</p>
    </div>
  </div>
  <div class="col-md-4 col-md-push-0 col-sm-5 col-sm-push-1 col-xs-10 col-xs-push-1">
    <div class="jumbotron text-center<?php if($metrics['consistencia']>=0) : ?> jumbotron-green<?php else : ?> jumbotron-red<?php endif ?>">
      <h1 data-toggle="tooltip" data-placement="top" title="Cambios con ganancia / Total">Consistencia</h1>
      <p class="text-xxl"><?php echo number_format($metrics['consistencia'], 2); ?>%</p>
      <p>Cambios con ganancia / Total</p>
    </div>
  </div>
</div>
