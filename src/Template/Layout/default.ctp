<!DOCTYPE html>
<html lang="es" xmlns="http://www.w3.org/1999/xhtml"  xmlns:og="http://ogp.me/ns#" xmlns:fb="https://www.facebook.com/2008/fbml" itemscope itemtype="http://schema.org/Organization">
  <head>
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-22286608-4"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', 'UA-22286608-4');
    </script>
    <?php echo $this->Html->charset() ?>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fondos AFP<?php echo !empty($title) ? " - $title" : ''; ?></title>
    <meta name="description" content="Información gratuita y actualizada de los fondos de pensiones de las AFPs de Chile."/>
	<meta name="keywords" content="AFP, Fondos AFP, Gráfico Fondos AFP, AFP Capital, AFP Provida, AFP Habitat, AFP Modelo, AFP Planvital, Habitat AFP"/>
	<meta itemprop="name" content="Fondos AFP" />
	<meta itemprop="description" content="Información gratuita y actualizada de los fondos de pensiones de las AFPs de Chile." />
	<meta itemprop="image" content="http://www.fondosafp.com/img/logo.png" />
	<meta property="og:title" content="Fondos AFP<?php echo !empty($title) ? " - $title" : ''; ?>"/>
    <meta property="og:url" content="http://www.fondosafp.com"/>
	<meta property="og:image" content="http://www.fondosafp.com/img/logo.png" />
	<meta property="og:type" content="website" />
	<meta property="og:site_name" content="Fondos AFP" />
	<meta property="og:description" content="Información gratuita y actualizada de los fondos de pensiones de las AFPs de Chile." />
	<meta property="fb:admins" content="YOUR-FACEBOOK-USER-ID" />
	<meta property="fb:app_id" content="YOUR-FACEBOOK-APP-ID" />
    <?php echo $this->Html->meta('icon') ?>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <?php echo $this->Html->css('app') ?>
    <?php echo $this->Html->css('jquery/jquery-ui.min.css') ?>
    <?php echo $this->Html->css('jquery/jquery-ui.theme.min.css') ?>
    <?php echo $this->Html->css('ie10-viewport-bug-workaround') ?>
    <?php echo $this->Html->css('//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/css/bootstrap-editable.css'); ?>

    <?php echo $this->fetch('meta') ?>
    <?php echo $this->fetch('css') ?>
    <script type="text/javascript">
        window.fbAsyncInit = function() {
          FB.init({
            appId      : 'YOUR-FACEBOOK-APP-ID',
            xfbml      : true,
            version    : 'v2.7'
          });
        };

        (function(d, s, id){
           var js, fjs = d.getElementsByTagName(s)[0];
           if (d.getElementById(id)) {return;}
           js = d.createElement(s); js.id = id;
           js.src = "//connect.facebook.net/es_ES/sdk.js";
           fjs.parentNode.insertBefore(js, fjs);
         }(document, 'script', 'facebook-jssdk'));
    </script>
      <script src="https://www.gstatic.com/firebasejs/7.13.2/firebase-app.js"></script>
      <script src="https://www.gstatic.com/firebasejs/7.13.2/firebase-analytics.js"></script>
      <script>
          var firebaseConfig = {/* todo: this */ };
          firebase.initializeApp(firebaseConfig);
          firebase.analytics();
      </script>
  </head>
  <?php if(isset($_GET['apps']) && $_GET['apps'] == '1') : ?>
      <style>
          .inner p {text-align: justify !important;display:block!important;padding:0px 20px 0px 5px!important;width:100%!important;}
          h1,h2,h3, ul{display:block!important;padding:0 !important;margin:0px 0px 10px 0px!important;width:95%!important;}
          h1{font-size:16pt!important;margin-top:20px!important;}
          h2{font-size:14pt!important;}
          h3{font-size:12pt!important;}
          ul{padding-left:20px!important;width:90%!important;margin-bottom:30px!important;}
          li{text-align: justify !important;}
      </style>
  <?php endif; ?>
  <body>
    <div class="site-wrapper">
      <div class="<?php if (!empty($body_centered)) : ?>site-wrapper-inner<?php else : ?>site-wrapper-top<?php endif; ?>">
        <div class="cover-container">
<?php if(!isset($_GET['apps']) || $_GET['apps'] != '1') : ?>
          <div class="masthead clearfix">
            <div class="inner">
                <!-- Fixed navbar -->
                <nav class="navbar navbar-fixed-top">
                  <div class="container">
                    <div class="navbar-header">
                      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                      </button>
                      <a class="navbar-brand" href="#">Fondos AFP</a>
                    </div>
                    <div id="navbar" class="navbar-collapse collapse">
                      <ul class="nav navbar-nav pull-right">
                        <?php if ($this->request->session()->read('Auth.User.id')) : ?>
                        <li<?php if ($this->request->controller == 'Dashboard' && $this->request->action == 'index') :  ?> class="active"<?php endif; ?>>
                          <a href="/dashboard"><i class="glyphicon glyphicon-dashboard"></i> Dashboard</a>
                        </li>
                        <?php else : ?>
                          <li<?php if ($this->request->controller == 'Pages' && $this->request->action == 'display' && $this->request->pass['0'] == 'home') :  ?> class="active"<?php endif; ?>>
                            <a href="/"><i class="glyphicon glyphicon-home"></i> Inicio</a>
                          </li>
                        <?php endif; ?>
                        <li<?php if ($this->request->controller == 'Blog' && $this->request->action == 'index') :  ?> class="active"<?php endif; ?>>
                            <a href="/blog"><i class="glyphicon glyphicon-bell"></i> Noticias</a>
                        </li>
                        <?php if ($this->request->session()->read('Auth.User.id')) : ?>
                        <li<?php if ($this->request->controller == 'Changes' && $this->request->action == 'index') :  ?> class="active"<?php endif; ?>>
                          <a href="/cambios"><i class="glyphicon glyphicon-transfer"></i> Cambios</a>
                        </li>
                        <?php endif; ?>
                        <li<?php if ($this->request->controller == 'Fondos' && $this->request->action == 'index') :  ?> class="active"<?php endif; ?>>
                          <a href="/fondos"><i class="fa fa-line-chart"></i> Fondos</a>
                        </li>
                        <?php if ($this->request->session()->read('Auth.User.role') == 'admin') : ?>
                        <li<?php if ($this->request->controller == 'Reports' && $this->request->action == 'index') :  ?> class="active"<?php endif; ?>>
                          <a href="/reports"><i class="glyphicon glyphicon-tasks"></i> Reportes</a>
                        </li>
                        <?php endif; ?>
                         <?php if ($this->request->session()->read('Auth.User.role') == 'admin') : ?>
                        <li<?php if ($this->request->controller == 'Ranking' && $this->request->action == 'index') :  ?> class="active"<?php endif; ?>>
                          <a href="/ranking"><i class="fa fa-users"></i> Ranking</a>
                        </li>
                        <?php endif; ?>
                        <?php if ($this->request->session()->read('ghost_id')) : ?>
                        <li><a href="/users/end-ghost-login"><i class="glyphicon glyphicon-log-out"></i> End Ghost Login</a></li>
                        <?php endif ?>
                        <?php if ($this->request->session()->read('Auth.User.id')) : ?>
                        <li><a href="/logout"><i class="glyphicon glyphicon-log-out"></i> Salir</a></li>
                        <?php endif; ?>
                      </ul>
                    </div><!--/.nav-collapse -->
                  </div>
                </nav>
            </div>
          </div>
<?php endif; ?>
          <div class="inner cover">
            <?php echo $this->Flash->render() ?>
            <?php echo $this->fetch('content') ?>
          </div>
<?php if(!isset($_GET['apps']) || $_GET['apps'] != '1') : ?>
          <div class="mastfoot">
            <div class="inner">
              <p>
                FondosAFP 2011-<?php echo date("Y") ?>, por <a href="https://twitter.com/FondosAFP">@FondosAFP</a> -
                contacto@fondosafp.com
              </p>
            </div>
          </div>
<?php endif; ?>
        </div>
      </div>
    </div>
    <?php if (!empty($this->request->session()->read('Auth.User')) && empty($this->request->session()->read('Auth.User.preference'))) : ?>
    <div id="modal-afpfondo" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title">Bienvenido a Fondos AFP</h4>
            </div>
            <div class="modal-body">
              <form>
                <div class="form-group">
                  <label for="afp">&iquest;En qu&eacute; AFP te encuentras actualmente?</label>
                  <select name="afp" id="afp" class="form-control">
                    <?php foreach ($afps as $afp) : ?>
                    <option value="<?php echo $afp->id ?>"><?php echo $afp->name; ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="form-group">
                  <label for="fondo">&iquest;En qu&eacute; fondo tienes tus cotizaciones obligatorias?</label>
                  <select name="fondo" id="fondo" class="form-control">
                    <?php foreach ($fondos as $f) : ?>
                    <option value="<?php echo $f->id ?>"><?php echo $f->name; ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
              </form>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-primary" onclick="return saveUserPreferences();">Comenzar</button>
            </div>
          </div>
        </div>
    </div>
    <?php endif; ?>
    <?php if (!empty($this->request->session()->read('Auth.User')) && strpos($this->request->session()->read('Auth.User.email'), '@facebook.com') > 0) : ?>
    <div id="modal-email" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title">Necesitamos tu correo electr&oacute;nico</h4>
            </div>
            <div class="modal-body">
              <form class="form-horizontal">
                <div class="form-group">
                  <label for="email"  class="col-sm-12 text-center">FondosAFP necesita enviarte algunas notificaciones</label>
                  <div class="col-sm-10 margin-top-10">
                    <input type="email" class="form-control" name="user_email" id="user_email" placeholder="Ingresa un correo electr&oacute;nico" value="" />
                  </div>
                </div>
              </form>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-primary" onclick="return saveEmail();">Guardar</button>
            </div>
          </div>
        </div>
    </div>
    <?php endif; ?>

    <script src="https://code.jquery.com/jquery-3.1.1.js" crossorigin="anonymous"></script>
    <script src="/js/jquery/jquery-ui.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    <script src="/js/ie10-viewport-bug-workaround.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/js/bootstrap-editable.min.js"></script>
    <script src="/js/app.js"></script>
    <?php echo $this->fetch('script') ?>
    <script src="https://use.fontawesome.com/cad2af34d5.js"></script>

    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </body>
</html>
