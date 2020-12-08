<?php $this->Html->script('Chart.min.js', ['block' => true]); ?>
<?php $this->Html->script('changes.js', ['block' => true]); ?>
<div>
  <!-- Nav tabs -->
  <ul class="nav nav-tabs" role="tablist" id="changes_tabs">
    <li role="presentation"<?php if (empty($this->request->query['t']) || $this->request->query['t'] == 'index') : ?> class="active"<?php endif; ?>>
      <a href="#index" aria-controls="index" role="tab" data-toggle="tab">
        <i class="glyphicon glyphicon-transfer"></i>
        Cambios
      </a>
    </li>
    <li role="presentation"<?php if (!empty($this->request->query['t']) && $this->request->query['t'] == 'history') : ?> class="active"<?php endif; ?>>
      <a href="#history" aria-controls="history" role="tab" data-toggle="tab">
        <i class="glyphicon glyphicon-time"></i>
        Historial
      </a>
    </li>
    <?php if (true) : ?>
    <li role="presentation"<?php if (!empty($this->request->query['t']) && $this->request->query['t'] == 'cargar') : ?> class="active"<?php endif; ?>>
      <a href="#cargar" aria-controls="cargar" role="tab" data-toggle="tab">
        <i class="glyphicon glyphicon-open-file"></i>
        Cargar Certificado
      </a>
    </li>
    <?php endif; ?>
  </ul>

  <!-- Tab panes -->
  <div class="tab-content">
    <div role="tabpanel" class="tab-pane fade<?php if (empty($this->request->query['t']) || $this->request->query['t'] == 'index') : ?>in active<?php endif; ?>" id="index">
      <?php if (!empty($cambiosData)) : ?>
        <?php echo $this->element('Changes/tab_index'); ?>
      <?php else : ?>
        <p class="form-control-static">Aun no has registrado cambios de fondos</p>
      <?php endif; ?>
    </div>
    <div role="tabpanel" class="tab-pane fade<?php if (!empty($this->request->query['t']) && $this->request->query['t'] == 'history') : ?>in active<?php endif; ?>" id="history">
      <?php echo $this->element('Changes/tab_history'); ?>  
    </div>
    <?php if (true) : ?>
    <div role="tabpanel" class="tab-pane fade<?php if (!empty($this->request->query['t']) && $this->request->query['t'] == 'cargar') : ?>in active<?php endif; ?>" id="cargar">
      <?php echo $this->element('Changes/tab_cargar'); ?>
    </div>
    <?php endif; ?>
  </div>
</div>
