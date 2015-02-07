<!DOCTYPE html>
<html lang="en">
    <head>
        <meta content="IE=9" http-equiv="X-UA-Compatible">
        <meta content="IE=edge" http-equiv="X-UA-Compatible">
        <meta charset="utf-8">
        <title>{{ Data.title }}</title>
        <link rel="icon" href="{{ Core.Domains.theme }}images/faviconu.ico" type="image/x-icon">
        <!-- Bootstrap -->		
        <link href="{{ Core.Domains.theme }}css/bootstrap.min.css" rel="stylesheet">
        <link href="{{ Core.Domains.theme }}css/jquery-ui.css" rel="stylesheet">
        <link href="{{ Core.Domains.theme }}css/font-awesome.css" rel="stylesheet">
        <link href="{{ Core.Domains.theme }}js/wdScrollTab/css/TabPanel.css" rel="stylesheet" type="text/css"/>
        <link href="{{ Core.Domains.theme }}php/general.css" rel="stylesheet">        
        <link rel="stylesheet" href="{{ Core.Domains.theme }}css/jquery.fileupload.css">
        <link href="{{ Core.Domains.theme }}css/bootstrap-switch.css" rel="stylesheet">
        <link href="{{ Core.Domains.theme }}js/bootstrap-wysiwyg/editor.css" rel="stylesheet">
        <link href="{{ Core.Domains.theme }}js/bootstrap-tagsinput/bootstrap-tagsinput.css" rel="stylesheet">
        <link href="{{ Core.Domains.theme }}js/bootstrap-tagsinput/app.css" rel="stylesheet">
        <link href="{{ Core.Domains.theme }}js/select2/select2.css" rel="stylesheet"/>
        <link href="{{ Core.Domains.theme }}js/circliful/css/jquery.circliful.css" rel="stylesheet" type="text/css" />
        {{ View.CSS|raw }}
        {{ Modules.top.css|raw }}
        <link href="{{Core.Domains.theme}}css/{{Browser.os}}/{{Browser.name}}.css" rel="stylesheet" type="text/css" />
        <script src="{{ Core.Domains.theme }}js/jquery-1.10.2.min.js"></script>
        {{ Modules.top.js|raw }}
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->
        {{ Widgets.ALL|raw }}        
    </head>
    <body role="document">
        {% include('header.twig') %}
        {% block content %}{% endblock %}
        {% include('footer.twig') %}
        {{ View.JS|raw }}
        {{ Modules.bottom.css|raw }}
        {{ Modules.bottom.js|raw }}
                
        <script type="text/javascript" src="{{ Core.Domains.theme }}js/wdScrollTab/src/Plugins/Fader.js"></script>
        <script type="text/javascript" src="{{ Core.Domains.theme }}js/wdScrollTab/src/Plugins/TabPanel.js"></script>
        <script type="text/javascript" src="{{ Core.Domains.theme }}js/wdScrollTab/src/Plugins/Math.uuid.js"></script>
        <script src="{{ Core.Domains.theme }}js/jquery.ui.widget.js"></script>
        <script src="{{ Core.Domains.theme }}js/jquery.iframe-transport.js"></script>
        <script src="{{ Core.Domains.theme }}js/jquery.fileupload.js"></script>
        <script src="{{ Core.Domains.theme }}js/jquery.fileupload-process.js"></script>
        <script src="{{ Core.Domains.theme }}js/jquery.fileupload-validate.js"></script>		
        <script src="{{ Core.Domains.theme }}js/jquery.jcryption.3.0.1.js"></script>
        <script src="{{ Core.Domains.core }}js/jquery.validate.js"></script>
        <script src="{{ Core.Domains.core }}js/jquery-ui-1.10.3.js"></script>
        <script src="{{ Core.Domains.theme }}js/bootstrap.min.js"></script>		
        <script src="{{ Core.Domains.theme }}js/jquery.parallax-1.1.3.js"></script>
        <script src="{{ Core.Domains.theme }}js/jquery.localscroll-1.2.7-min.js"></script>		
        <script src="{{ Core.Domains.theme }}js/retina-1.1.0.min.js"></script>
        <script src="{{ Core.Domains.theme }}js/scrolld.min.js"></script>
        <script src="{{ Core.Domains.theme }}js/scripts.js"></script>
        <script src="{{ Core.Domains.theme }}js/bootstrap-switch.js"></script>
        <script src="{{ Core.Domains.theme }}js/blur.min.js"></script>
        <script src="{{ Core.Domains.theme }}js/blur.min.js"></script>
        <script src="{{ Core.Domains.theme }}php/general.js"></script>      
        <script src="{{ Core.Domains.theme }}js/bootstrap-wysiwyg/editor.js"></script>
        <script src="{{ Core.Domains.theme }}js/bootstrap-tagsinput/bootstrap-tagsinput.min.js"></script>
        <script src="{{ Core.Domains.theme }}js/typeahead.bundle.js"></script>
        <script src="{{ Core.Domains.theme }}js/select2/select2.min.js"></script>
        <script src="{{ Core.Domains.core }}js/highcharts.js"></script>
        <script src="{{ Core.Domains.theme }}js/circliful/js/jquery.circliful.min.js"></script>
        <script src="{{ Core.Domains.theme }}js/cropbox.js"></script>
        <script src="{{ Core.Domains.theme }}js/jquery.form.js"></script>
        <script src="{{ Core.Domains.theme }}js/additional-methods.min.js"></script>
        <script src="{{ Core.Domains.core }}js/main.js"></script>
        <script src="https://maps.googleapis.com/maps/api/js?v=3.exp"></script>
    </body>
</html>

<!--
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?php siteTitle();?></title>
        <link rel="icon" href="<?php bpt();?>images/favicon.ico" type="image/x-icon">

        <link href="<?php bp();?>css/bootstrap.min.css" rel="stylesheet">
        <link href="<?php bpt();?>css/flexslider.css" rel="stylesheet">
        <link href="<?php bpt();?>css/font-awesome.css" rel="stylesheet">
        <link href="<?php bpt();?>css/style.css" rel="stylesheet">
        <link href="<?php bpt();?>css/custom.css" rel="stylesheet">


        <script src="<?php bp();?>js/jquery-1.8.3.min.js"></script>
    </head>
    <body role="document">
        <?php twig('header.twig'); ?>
        <section id="titleArea">
            <div class="container">

                <div class="row">
                    <div class="col-lg-12">

                        <h2>EAV Render Page</h2>

                        <h3>Lorem Ipsum is simply dummy</h3>
                        <?php
                            eavData(4, 2, 3);
                            eavContent();
                        ?>
                    </div>
                </div>
            </div>
        </section>
        <hr>
        <?php twig('footer.twig', array("extraVar"=>"This text has been passed though an extraVar in the footer.")); ?>
        <script src="<?php bp();?>js/bootstrap.min.js"></script>
        <script src="<?php bpt();?>js/jquery.parallax-1.1.3.js"></script>
        <script src="<?php bpt();?>js/jquery.localscroll-1.2.7-min.js"></script>        
        <script src="<?php bpt();?>js/jquery.flexslider-min.js"></script>
        <script src="<?php bpt();?>js/retina-1.1.0.min.js"></script>
        <script src="<?php bpt();?>js/scrolld.min.js"></script>
        <script src="<?php bpt();?>js/scripts.js"></script>
    </body>
</html>
-->