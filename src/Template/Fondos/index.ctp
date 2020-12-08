<?php 
$this->Html->script('//code.highcharts.com/stock/highstock.js', ['block' => true]);
$this->Html->script('//code.highcharts.com/stock/modules/exporting.js', ['block' => true]);
if ($this->request->session()->read('Auth.User.id')) {
    $this->Html->script('fondos_', ['block' => true]);
} else {
    $this->Html->script('fondos', ['block' => true]);
}
$this->Html->script('//cdn.jsdelivr.net/momentjs/latest/moment.min.js', ['block' => true]);
$this->Html->script('//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js', ['block' => true]);
$this->Html->css('//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css', ['block' => true]);
$profile = $this->request->session()->read('Auth.User'); 
$afp_selected = null;
$fondo_selected = null;
?>
<div class="row">
  <div class="col-md-12 text-center margin-bottom-5"> 
    <?php if (empty($this->request->session()->read('Auth.User')) || $this->request->session()->read('Auth.User.preference.mostrar_otras_afps') == '1') : ?>
    <div class="btn-group" role="group" aria-label="...">
      <?php foreach ($afps as $afp) : ?>
      <button 
        data-id="<?php echo $afp->id; ?>"
        data-type="afp"
        type="button" 
        class="btn btn-default<?php if ((!empty($profile) && !empty($profile['preference']['afp_id']) && $profile['preference']['afp_id'] == $afp->id) || (!empty($afp_api) && $afp_api == $afp->api_name)) : $afp_selected = $afp->id; ?> active<?php endif; ?>"
        onclick="return selectAFP(<?php echo $afp->id; ?>, '<?php echo $afp->api_name; ?>');">
            <?php echo ucwords(strtolower($afp->name)); ?>
      </button>
      <?php endforeach; ?>
    </div>
    <?php else : ?>
        <?php 
        foreach ($afps as $afp) {
            if ((!empty($profile) && !empty($profile['preference']['afp_id']) && $profile['preference']['afp_id'] == $afp->id) || (!empty($afp_api) && $afp_api == $afp->api_name))  { 
                $afp_selected = $afp->id; 
            }
        }
        ?>
    <?php endif; ?>
  </div>
  <div class="col-md-12 text-center margin-bottom-5">
    <div class="btn-group" role="group" aria-label="...">
      <?php foreach ($fondos as $f) : ?>
      <button 
        data-id="<?php echo $f->id; ?>"
        data-type="fondo"
        type="button" 
        class="btn btn-default<?php if ((!empty($profile) && !empty($profile['preference']['fondo_id']) && $profile['preference']['fondo_id'] == $f->id) || (!empty($fondo_api) && $fondo_api == $f->api_name)) : $fondo_selected = $f->id; ?> active<?php endif; ?>"
        onclick="return selectFondo(<?php echo $f->id; ?>, '<?php echo $f->api_name; ?>')">
            <?php echo $f->name; ?>
      </button>
      <?php endforeach; ?>
    </div>
    &nbsp;
    <div class="btn-group" role="group" aria-label="...">
      <button type="button" class="btn btn-default active" data-type="type" data-name="valor" title="Valor" onclick="selectType('valor');"><i class="glyphicon glyphicon-usd"></i></button>
      <button type="button" class="btn btn-default" data-type="type" data-name="porcentaje" title="Porcentaje" onclick="selectType('porcentaje');">%</button>
      <button type="button" class="btn btn-default" data-type="type" data-name="patrimonio" title="Patrimonio" onclick="selectType('patrimonio');"><i class="glyphicon glyphicon-piggy-bank"></i></button>
    </div>
    <?php if ($this->request->session()->read('Auth.User.id')) : ?>
    &nbsp;
    <div class="btn-group" role="group" aria-label="...">
      <button type="button" id="fechas" class="btn btn-default" title="Fechas"><i class="glyphicon glyphicon-calendar"></i></button>
    </div>
    <?php endif; ?>
  </div>
  <?php if (empty($profile)) : ?>
    <?php foreach ($afps as $afp) : ?>
    <input type="hidden" name="afp" id="afp_selected" value="<?php echo $afp->id; ?>" />
    <?php break; endforeach; ?>
    <?php foreach ($fondos as $f) : ?>
    <input type="hidden" name="fondo" id="fondo_selected" value="<?php echo $f->id; ?>" />
    <?php break; endforeach; ?>
  <?php else : ?>
    <input type="hidden" name="afp" id="afp_selected" value="<?php echo $afp_selected ?>" />
    <input type="hidden" name="fondo" id="fondo_selected" value="<?php echo $fondo_selected ?>" />
  <?php endif; ?>
    <input type="hidden" name="type" id="type" value="valor" />
    <?php if ($this->request->session()->read('Auth.User.id')) : ?>
    <input type="hidden" name="desde" id="desde" value="<?php echo $this->request->query('desde'); ?>" />
    <input type="hidden" name="hasta" id="hasta" value="<?php echo $this->request->query('hasta'); ?>" />
    <?php endif; ?>
    <input type="hidden" name="afp_api" id="afp_api" value="<?php echo $afp_api; ?>" />
    <input type="hidden" name="fondo_api" id="fondo_api" value="<?php echo $fondo_api; ?>" />
    <input type="hidden" name="type_api" id="type_api" value="<?php echo $type_api; ?>" />
</div>
<div class="row">
  <div class="col-md-12">
    <div id="chart_container"> </div>
  </div>
</div>
<?php echo $this->element('social-buttons', ['css' => 'margin-top-20']); ?>
<?php echo $this->element('comments', ['page' => '/fondos']); ?>
<script type="text/javascript">
    <?php if (empty($profile)) : ?>
        window.onload = function(e){ 
            <?php 
            if(!empty($afp_api))  {
              foreach ($afps as $afp) {
                if ($afp->api_name == $afp_api) {
                    ?>selectAFP(<?php echo $afp->id; ?>, '<?php echo $afp->api_name; ?>', false);<?php
                    break;
                }
              }
            } else {
              foreach ($afps as $afp) {
                ?>selectAFP(<?php echo $afp->id; ?>, '<?php echo $afp->api_name; ?>', false);<?php
                break;
              }
            }
            if(!empty($fondo_api))  {
              foreach ($fondos as $f) {
                if ($f->api_name == $fondo_api) {
                    ?>selectFondo(<?php echo $f->id; ?>, '<?php echo $f->api_name; ?>');<?php
                    break;
                }
              }
            } else {
              foreach ($fondos as $f) {
                  ?>selectFondo(<?php echo $f->id; ?>, '<?php echo $f->api_name; ?>');<?php
                  break;
              }
            }
            ?>
        };
    <?php endif; ?>
</script>