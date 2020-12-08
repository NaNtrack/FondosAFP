<div class="row">
    <div class="col-md-9 col-sm-8 col-xs-12">
        <form class="form-horizontal" method="POST">
            <input type="hidden" name="form" value="settings" />
            <div class="form-group row">
                <label class="col-md-12 col-sm-12 col-xs-12 control-label text-left">General</label>
            </div>
            <div class="form-group row">
                <label for="home" class="col-md-2 col-md-push-1 col-sm-3 col-sm-push-1 col-xs-11 col-xs-push-1 control-label text-normal">Pagina Inicial</label>
                <div class="col-md-4 col-md-push-1 col-sm-5 col-sm-push-1 col-xs-11 col-xs-push-1">
                  <select name="homepage" class="form-control" id="homepage">
                    <option value="dashboard"<?php if ($this->request->session()->read('Auth.User.preference.homepage') == 'dashboard') : ?> selected="selected"<?php endif; ?>>Dashboard</option>
                    <option value="dashboard#profile"<?php if ($this->request->session()->read('Auth.User.preference.homepage') == 'dashboard#profile') : ?> selected="selected"<?php endif; ?>>&nbsp;&nbsp;&nbsp;&nbsp;Dashboard - Perfil</option>
                    <option value="dashboard#metrics"<?php if ($this->request->session()->read('Auth.User.preference.homepage') == 'dashboard#metrics') : ?> selected="selected"<?php endif; ?>>&nbsp;&nbsp;&nbsp;&nbsp;Dashboard - M&eacute;tricas</option>
                    <option value="dashboard#settings<?php if ($this->request->session()->read('Auth.User.preference.homepage') == 'dashboard#settings') : ?> selected="selected"<?php endif; ?>">&nbsp;&nbsp;&nbsp;&nbsp;Dashboard - Configuraci&oacute;n</option>
                    <option value="cambios"<?php if ($this->request->session()->read('Auth.User.preference.homepage') == 'cambios') : ?> selected="selected"<?php endif; ?>>Cambios</option>
                    <option value="cambios"<?php if ($this->request->session()->read('Auth.User.preference.homepage') == 'cambios#index') : ?> selected="selected"<?php endif; ?>>&nbsp;&nbsp;&nbsp;&nbsp;Cambios - Cambios</option>
                    <option value="cambios#cargar"<?php if ($this->request->session()->read('Auth.User.preference.homepage') == 'cambios#cargar') : ?> selected="selected"<?php endif; ?>>&nbsp;&nbsp;&nbsp;&nbsp;Cambios - Cargar Certificado</option>
                    <option value="cambios#history<?php if ($this->request->session()->read('Auth.User.preference.homepage') == 'cambios#history') : ?> selected="selected"<?php endif; ?>">&nbsp;&nbsp;&nbsp;&nbsp;Cambios - Historial</option>
                    <option value="fondos"<?php if ($this->request->session()->read('Auth.User.preference.homepage') == 'fondos') : ?> selected="selected"<?php endif; ?>>Fondos</option>
                  </select>
                </div>
            </div>
            
            <div class="form-group row">
                <label class="col-md-12 col-sm-12 col-xs-12 control-label text-left">Fondos</label>
            </div>
            <div class="form-group row">
              <div class="col-md-4 col-md-push-1 col-sm-4 col-sm-push-1 col-xs-11 col-xs-push-1">
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="mostrar_otras_afps"<?php if ($this->request->session()->read('Auth.User.preference.mostrar_otras_afps') == '1') : ?> checked="checked"<?php endif; ?>>
                        Mostrar otras AFPs
                    </label>
                </div>
              </div>
            </div>
            <div class="form-group row">
                <label class="col-md-12 col-sm-12 col-xs-12 control-label text-left">Enviar correo</label>
            </div>
            <div class="form-group row">
              <div class="col-md-4 col-md-push-1 col-sm-4 col-sm-push-1 col-xs-11 col-xs-push-1">
                <div class="checkbox">
                    <label><input type="checkbox" name="event_fondos_up"<?php if ($this->request->session()->read('Auth.User.preference.event_fondos_up') == '1') : ?> checked="checked"<?php endif; ?>> Cuando suben mis fondos</label>
                </div>
              </div>
            </div>
            <div class="form-group row">
              <div class="col-md-4 col-md-push-1 col-sm-4 col-sm-push-1 col-xs-11 col-xs-push-1">
                <div class="checkbox">
                    <label><input type="checkbox" name="event_fondos_down"<?php if ($this->request->session()->read('Auth.User.preference.event_fondos_down') == '1') : ?> checked="checked"<?php endif; ?>> Cuando bajen mis fondos</label>
                </div>
              </div>
            </div>
            <div class="form-group row">
              <div class="col-md-4 col-md-push-1 col-sm-4 col-sm-push-1 col-xs-11 col-xs-push-1">
                <div class="checkbox">
                    <label><input type="checkbox" name="resumen_mensual"<?php if ($this->request->session()->read('Auth.User.preference.resumen_mensual') == '1') : ?> checked="checked"<?php endif; ?>> Resumen mensual</label>
                </div>
              </div>
            </div>
            <!--
            <div class="form-group row">
              <div class="col-md-4 col-md-push-1 col-sm-4 col-sm-push-1 col-xs-11 col-xs-push-1">
                <div class="checkbox">
                    <label><input type="checkbox" name="event_ranking_up"<?php if ($this->request->session()->read('Auth.User.preference.event_ranking_up') == '1') : ?> checked="checked"<?php endif; ?>> Cuando subo en el ranking</label>
                </div>
              </div>
            </div>
            <div class="form-group row">
              <div class="col-md-4 col-md-push-1 col-sm-4 col-sm-push-1 col-xs-11 col-xs-push-1">
                <div class="checkbox">
                    <label><input type="checkbox" name="event_ranking_down"<?php if ($this->request->session()->read('Auth.User.preference.event_ranking_down') == '1') : ?> checked="checked"<?php endif; ?>> Cuando bajo en el ranking</label>
                </div>
              </div>
            </div>
            <div class="form-group row">
              <div class="col-md-4 col-md-push-1 col-sm-4 col-sm-push-1 col-xs-11 col-xs-push-1">
                <div class="checkbox">
                    <label><input type="checkbox" name="event_new_follow"<?php if ($this->request->session()->read('Auth.User.preference.event_new_follow') == '1') : ?> checked="checked"<?php endif; ?>> Cuando alguien se subscribe a mi canal</label>
                </div>
              </div>
            </div>
            -->
            <div class="form-group">
                <div class="col-sm-10">
                    <button class="btn btn-success" type="submit">Actualizar</button>
                </div>
            </div>
        </form>
    </div>
</div>
