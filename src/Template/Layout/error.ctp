<!DOCTYPE html>
<html lang="es" xmlns="http://www.w3.org/1999/xhtml"  xmlns:og="http://ogp.me/ns#" xmlns:fb="https://www.facebook.com/2008/fbml" itemscope itemtype="http://schema.org/Organization">
  <head>
    <?php echo $this->Html->charset() ?>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fondos AFP - Error <?php echo !empty($title) ? " - $title" : ''; ?></title>
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
  </head>
  <body>
    <div class="site-wrapper">
      <div class="<?php if (!empty($body_centered)) : ?>site-wrapper-inner<?php else : ?>site-wrapper-top<?php endif; ?>">
        <div class="cover-container">
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
                        <?php if ($this->request->session()->read('Auth.User.id')) : ?>
                        <li<?php if ($this->request->controller == 'Changes' && $this->request->action == 'index') :  ?> class="active"<?php endif; ?>>
                          <a href="/cambios"><i class="glyphicon glyphicon-transfer"></i> Cambios</a>
                        </li>
                        <?php endif; ?>
                        <li<?php if ($this->request->controller == 'Fondos' && $this->request->action == 'index') :  ?> class="active"<?php endif; ?>>
                          <a href="/fondos"><i class="fa fa-line-chart"></i> Fondos</a>
                        </li>
                        <?php if ($this->request->session()->read('Auth.User.id')) : ?>
                        <li><a href="/logout"><i class="glyphicon glyphicon-log-out"></i> Salir</a></li>
                        <?php endif; ?>
                      </ul>
                    </div><!--/.nav-collapse -->
                  </div>
                </nav>
            </div>
          </div>
          <div class="inner cover">
            <?php echo $this->Flash->render() ?>
            <?php echo $this->fetch('content') ?>
          </div>
          <div class="mastfoot">
            <div class="inner">
              <p>FondosAFP 2011-<?php echo date("Y") ?>, por <a href="https://twitter.com/FondosAFP">@FondosAFP</a>.</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.1.1.js" crossorigin="anonymous"></script>
    <script src="/js/jquery/jquery-ui.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    <script src="/js/ie10-viewport-bug-workaround.js"></script>
    <script src="/js/app.js"></script>
    <?php echo $this->fetch('script') ?>
    <script src="https://use.fontawesome.com/cad2af34d5.js"></script>

    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <script>
    (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
    (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
    m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
    })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');
    ga('create', 'TU-ID-DE-ANALYTICS', 'auto');
    ga('send', 'pageview');
    </script>
  </body>
</html>
