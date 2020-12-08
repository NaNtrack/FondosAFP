<?php $this->Html->script('dashboard', ['block' => true]); ?>
<div>
  <!-- Nav tabs -->
  <ul class="nav nav-tabs" role="tablist" id="dashboard_tabs">
    <li role="presentation" class="active">
      <a href="#profile" aria-controls="profile" role="tab" data-toggle="tab">
        <i class="glyphicon glyphicon-user"></i>
        Perfil
      </a>
    </li>
    <li role="presentation">
      <a href="#metrics" aria-controls="metrics" role="tab" data-toggle="tab">
        <i class="glyphicon glyphicon-stats"></i>
        M&eacute;tricas
      </a>
    </li>
    <li role="presentation">
      <a href="#settings" aria-controls="settings" role="tab" data-toggle="tab">
        <i class="glyphicon glyphicon-cog"></i>
        Configuraci&oacute;n
      </a>
    </li>
  </ul>

  <!-- Tab panes -->
  <div class="tab-content">
    <div role="tabpanel" class="tab-pane fade in active" id="profile">
      <?php echo $this->element('Dashboard/tab_profile'); ?>
    </div>
    <div role="tabpanel" class="tab-pane fade" id="metrics">
      <?php echo $this->element('Dashboard/tab_metrics'); ?>  
    </div>
    <div role="tabpanel" class="tab-pane fade" id="settings">
      <?php echo $this->element('Dashboard/tab_settings'); ?>    
    </div>
  </div>
</div>

