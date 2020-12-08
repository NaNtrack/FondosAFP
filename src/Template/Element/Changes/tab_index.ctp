<div class="row margin-bottom-30">
  <div class="col-md-12">
    <canvas id="cambios_chart" width="400" height="120"></canvas>
  </div>
</div>
<script type="text/javascript">
    window.cambios_chart = <?php echo json_encode($cambiosData); ?>;
    window.cambios_labels = <?php echo json_encode($cambiosLabels); ?>;
    window.cambios_values = <?php echo json_encode($cambiosValues); ?>;
</script>
