<div class="row">
    <div class="col-md-3 col-sm-4 col-xs-12 text-center margin-bottom-20">
      <?php $profile = $this->request->session()->read('Auth.User'); ?>
      <img src="<?php echo $profile['picture']; ?>" class="user_image" alt="<?php echo $profile['first_name']. ' ' . $profile['last_name']; ?>" title="<?php echo $profile['first_name']. ' ' . $profile['last_name']; ?>" />
    </div>
    <div class="col-md-9 col-sm-8 col-xs-12">
      <form class="form-horizontal">
        <div class="form-group">
          <label class="col-md-2 col-sm-2 col-xs-4 control-label">Nombre</label>
          <div class="col-md-10 col-sm-10 col-xs-8">
            <p class="form-control-static"><?php echo $profile['first_name']. ' ' . $profile['last_name']; ?></p>
          </div>
        </div>
        <div class="form-group">
          <label class="col-md-2 col-sm-2 col-xs-4 control-label">Email</label>
          <div class="col-md-10 col-sm-10 col-xs-8">
            <p class="form-control-static"><?php echo $profile['email']; ?></p>
          </div>
        </div>
        <div class="form-group">
          <script type="text/javascript">
              <?php
              $afpIds = [];
              foreach ($afps as $afp) {
                  $afpIds[] = [
                      'value' => $afp->id,
                      'text' => ucwords(strtolower($afp->name))
                  ];
              }
              ?>
              var afps = <?php echo json_encode($afpIds); ?>;
              var afpId = '<?php echo $profile['preference']['afp_id']; ?>';
          </script>
          <label class="col-md-2 col-sm-2 col-xs-4 control-label">AFP</label>
          <div class="col-md-10 col-sm-10 col-xs-8">
            <p class="form-control-static">
                <?php if ($profile['preference'] != null) : ?>
                    <?php foreach ($afps as $afp) :  ?>
                        <?php if ($afp->id == $profile['preference']['afp_id']) : ?>
                        <a id="afp_link" data-type="select"><?php echo ucwords(strtolower($afp->name)); ?></a>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php else : ?>
                    -
                <?php endif ?>
            </p>
          </div>
        </div>
        <div class="form-group">
          <?php $lastChangeFondos = []; ?>
          <?php if (!empty($metrics['ganancia'])) : ?>
          <?php foreach ($metrics['ganancia'] as $ganancia) : ?>
          <?php $lastChangeFondos[] = $ganancia['lastChange']['to_fondo']['name']; ?>
          <?php endforeach; ?>
          <?php endif; ?>
          <label class="col-md-2 col-sm-2 col-xs-4 control-label">Fondo<?php if (count($lastChangeFondos) > 1) : ?>s<?php endif; ?></label>
          <div class="col-md-10 col-sm-10 col-xs-8">
            <?php if ($profile['preference'] != null) : ?>
                <?php foreach ($fondos as $f) :  ?>
                    <?php if ($f->id == $profile['preference']['fondo_id']) : ?>
                        <?php 
                        $fondoName = $f->name;
                        foreach ($lastChangeFondos as $k => $v){
                            if ($v == $fondoName) {
                                unset($lastChangeFondos[$k]);   
                            }
                        }
                        sort($lastChangeFondos);
                        ?>
                    <?php endif; ?>
                <?php endforeach; ?>
                <p class="form-control-static fondo fondo_<?php echo $fondoName; ?>">
                   <?php echo $fondoName; ?>
                </p>
                <?php if (count($lastChangeFondos) > 0) : ?>
                , <p class="form-control-static fondo fondo_<?php echo $lastChangeFondos[0]; ?>">
                   <?php echo $lastChangeFondos[0]; ?>
                </p>
                <?php endif; ?>
            <?php else : ?>
                <p class="form-control-static fondo">
                   - 
                </p>
            <?php endif ?>
          </div>
        </div>
      </form>
    </div>
</div>
