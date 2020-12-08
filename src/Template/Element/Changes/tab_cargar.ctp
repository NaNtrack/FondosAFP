<div class="row margin-bottom-30">
  <div class="col-md-12">
    <p class="form-control-static">Aqui puedes cargar la <b>Cartola de Movimientos</b> o el <b>Certificado de Movimientos</b> entregado por tu AFP.</p>
  </div>
</div>
<div class="row">
  <div class="col-md-12">
    <?php echo $this->Form->create('Movimiento', ['enctype' => 'multipart/form-data']); ?>
    <div class="row">
      <div class="col-md-4">
        <?php echo $this->Form->input('upload', ['type' => 'file', 'label' => false]); ?>
      </div>
    </div>
    <div class="row margin-top-20">
      <div class="col-md-4">
        <?php echo $this->Form->button('Cargar PDF', ['class' => 'btn btn-success']); ?>
      </div>
    </div>
    <?php echo $this->Form->end(); ?>  
  </div>
</div>
