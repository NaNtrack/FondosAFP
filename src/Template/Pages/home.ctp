<?php $this->Html->css('signin', ['block' => true]); ?>
<div class="homepage">
    <h1 class="cover-heading"><img src="/img/logo.png" width="258" height="63" title="Fondos AFP" alt="Fondos AFP"/></h1>
    <p class="lead">Información gratuita y actualizada de las AFPs de Chile</p>

</div>
<div class="row">
    <div class="col-md-12 text-center">
        <a class="btn btn-default google" href="/google_login"><i class="fa fa-google-plus modal-icons"></i> Ingresar con Google</a>
        <a class="btn btn-default facebook" href="/facebook_login"><i class="fa fa-facebook modal-icons fa-2x"></i> Ingresar con Facebook</a>
    </div>
</div>
<br/>
<?php echo $this->element('social-buttons'); ?>
