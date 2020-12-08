<?php
$this->Html->script('//cdn.jsdelivr.net/momentjs/latest/moment.min.js', ['block' => true]);
$this->Html->script('//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js', ['block' => true]);
$this->Html->css('//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css', ['block' => true]);
?>
<div class="row">
  <div class="col-md-12">
      <form class="form-inline" method="post">
        <input type="hidden" name="form" value="add-history" />
        <div class="form-group">
          <div class="input-group">
            <input type="text" class="form-control" id="fecha" name="fecha" placeholder="Fecha" />
            <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
          </div>
          
        </div>
        <div class="form-group">
          <select name="desde" id="desde">
            <option value="">Desde</option>
            <option value="A">A</option>
            <option value="B">B</option>
            <option value="C">C</option>
            <option value="D">D</option>
            <option value="E">E</option>
          </select>
        </div>
        <div class="form-group">
          <select name="hasta" id="hasta">
            <option value="">Hasta</option>
            <option value="A">A</option>
            <option value="B">B</option>
            <option value="C">C</option>
            <option value="D">D</option>
            <option value="E">E</option>
          </select>
        </div>
        <!--
        <div class="form-group">
          <div class="input-group">
            <input type="text" maxlength="3" placeholder="100" value="100" name="porcentaje" id="porcentaje" class="onlyDigits w35" />
            <span class="input-group-addon" id="basic-addon2" style="padding:3px;">%</span>
          </div>
        </div>
        -->
        <button type="submit" class="btn btn-success" onclick="return agregarCambio()">Agregar Cambio</button>
      </form>
  </div>
</div>
<div class="row margin-top-10">
  <div class="col-md-12">
      <table class="table table-striped table-hover table-bordered">
      <tr>
        <th class="text-center col-md-2" rowspan="2" style="vertical-align:middle;">Fecha</th>
        <th class="text-center col-md-2 hidden-xs" rowspan="2" style="vertical-align:middle;">AFP</th>
        <th class="text-center col-md-3" colspan="2">Fondo</th>
        <th class="text-center col-md-2" rowspan="2" style="vertical-align:middle;">Resultado / Cuota</th>
        <th class="text-center col-md-2" rowspan="2" style="vertical-align:middle;">Rentabilidad</th>
        <th class="text-center col-md-2" rowspan="2" style="vertical-align:middle;">Acciones</th>
      </tr>
      <tr style="background-color:#f9f9f9">
        <th class="text-center">Desde</th>
        <th class="text-center">Hasta</th>
      </tr>
      <tr style="display: none;"></tr>
      <?php if ((empty($this->request->query['page']) || $this->request->query['page'] == 1) && !empty($cuotas)) : ?>
      <?php foreach ($cuotas as $i => $cuota) :  ?>
        <tr class="info">
          <td class="text-center align-middle"><?php echo $cuota->fecha->i18nFormat("yyyy-MM-dd"); ?></td>
          <td class="text-center align-middle hidden-xs">
              <?php foreach ($afps as $afp) :  ?>
                  <?php if ($afp->id == $this->request->session()->read('Auth.User.preference.afp_id')) : ?>
                      <?php echo ucwords(strtolower($afp->name)); ?>
                  <?php endif; ?>
              <?php endforeach; ?>
          </td>
          <td class="text-center align-middle">
              <span class="fondo fondo_<?php echo $lastChanges[$i]['to_fondo']['name']; ?>">
                  <?php echo $lastChanges[$i]['to_fondo']['name']; ?>
              </span>
              <br/>
              <i class="glyphicon glyphicon-usd"></i><?php echo $cuota->valor; ?>
          </td>
          <td class="text-center align-middle">-</td>
          <td class="text-right align-middle <?php echo $ganancias[$i] < 0 ? 'text-danger' : 'text-success' ?>"><i class="glyphicon glyphicon-usd"></i><?php echo $ganancias[$i] ?></td>
          <?php 
          $rendimiento = 100*(($ganancias[$i])/$lastChanges[$i]['to_value']);
          ?>
          <td class="text-right align-middle<?php if ($rendimiento >= 0) : ?> text-success<?php else : ?> text-danger<?php endif ?>"><?php echo number_format($rendimiento, 4); ?>%</td>
          <td class="text-center"></td>
        </tr>
      <?php endforeach; ?>
      <?php endif; ?>

      <?php if (!empty($changes)) : ?>
        <?php foreach ($changes as $fecha => $afp) : ?>
            <tr id="cambio-<?php echo $fecha; ?>">
              <td class="text-center align-middle" id="fecha-<?php echo $fecha; ?>" rowspan="<?php echo count($afp); ?>"><?php echo $fecha; ?></td>
            <?php foreach ($afp as $afpName => $fromName) : ?>
                <?php $i = 0; $total = 0; ?>
                <?php foreach ($fromName as $fromFondoName => $toFondo) : $i++; ?>
                    <?php if ($i == 1) : ?>
                    <td class="text-center align-middle hidden-xs" id="afp-<?php echo $fecha; ?>" rowspan="<?php echo count($fromName); ?>"><?php echo $afpName; ?></td>
                    <script type="text/javascript">
                        document.getElementById("fecha-<?php echo $fecha; ?>").rowSpan = <?php echo count($fromName); ?>;
                    </script>
                    <?php endif; ?>
                    <?php $tf = 0; ?>
                    <?php foreach ($toFondo as $toFondosName => $data) : $tf++; $total++ ?>
                    <?php if ($tf == 1) : ?>
                    <td class="text-center align-middle" rowspan="<?php echo count($toFondo); ?>">
                      <p class="fondo fondo_<?php echo $fromFondoName; ?>">
                          <?php echo $fromFondoName ?>
                      </p>
                      <br/>
                      <i class="glyphicon glyphicon-usd"></i><?php echo $data['from_value']; ?>
                    </td>
                    <?php endif; ?>
                    <td class="text-center align-middle">
                        <p class="fondo fondo_<?php echo $toFondosName; ?>">
                          <?php echo $toFondosName ?>
                      </p>
                      <br/>
                      <i class="glyphicon glyphicon-usd"></i><?php echo $data['to_value']; ?>
                    </td>
                    <td class="text-right align-middle <?php echo $data['profits_loss'] < 0 ? 'text-danger' : 'text-success' ?>">
                      <i class="glyphicon glyphicon-usd"></i><?php echo $data['profits_loss']; ?>
                    </td>
                    <td class="text-right align-middle <?php echo $data['performance'] < 0 ? 'text-danger' : 'text-success' ?>">
                        <?php echo $data['performance'] ?>%
                    </td>
                    <td class="text-center">
                        <?php if ($data['source'] !== 'certificado') : ?>
                          <a class="btn btn-sm btn-danger" onclick="return removeCambio(<?php echo $data['id'] ?>);"><i class="glyphicon glyphicon-trash"></i></a>
                        <?php endif; ?>
                    </td>
                    <?php if ($tf < count($toFondo)) :?>
                    </tr>
                    <tr id="cambio-<?php echo $fecha; ?>">
                    <?php else : ?>
                    <script type="text/javascript">
                        console.log('la ctm');
                        document.getElementById("fecha-<?php echo $fecha; ?>").rowSpan = <?php echo $total; ?>;
                        document.getElementById("afp-<?php echo $fecha; ?>").rowSpan = <?php echo $total; ?>;
                    </script>
                    <?php endif; ?>
                    <?php endforeach; ?>
                    <?php if ($i < count($fromName)) :?>
                    </tr>
                    <tr id="cambio-<?php echo $fecha; ?>">
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endforeach; ?>
          <?php endforeach; ?>
      <?php else : ?>
      <tr>
        <td colspan="7">Ud no registra cambios</td>
      </tr>
      <?php endif; ?>
    </table>
  </div>
</div>  
<div class="paginator">
    <ul class="pagination">
        <?= $this->Paginator->prev('< ' . __('previous'), ['url'=> ['#' => 'history']]) ?>
        <?= $this->Paginator->numbers(['url'=> ['#' => 'history']]) ?>
        <?= $this->Paginator->next(__('next') . ' >', ['url'=> ['#' => 'history']]) ?>
    </ul>
</div>
