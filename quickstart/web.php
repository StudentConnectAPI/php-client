<?php
/**
 * StudentConnect API Client - QuickStart Web
 * @author adrian7 (adrian@studentmoneysaver.co.uk)
 * @version 1.0
 */

require_once ( __DIR__ . '/include/boostrap.php' ); ?>
<!DOCTYPE html>
<html>
<head lang="en">

    <meta charset="UTF-8"/>
    <meta name="credits" content="https://studentconnectapi.com/"/>

    <base target="_top"/>

    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>

    <title>Quick Start with StudentConnect API</title>

</head>
<body>

    <div class="wrapper">

        <div class="header">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-6 col-md-4 col-sm-12 col-xs-12">
                        <div class="brand">
                            <a class="logo" href="<?php echo basename(__FILE__) ?>" target="_self">
                                <img src="assets/studentconnect-logo.png"/>
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-8 col-sm-12 col-xs-12">
                        <div class="pull-right menu">
                            <nav class="navbar">
                                <ul class="nav navbar-nav">
                                    <li class="nav-item">
                                        <a class="nav-link" href="https://docs.studentconnectapi.com" target="_blank">
                                            Read the docs  <i class="fa fa-external-link"></i>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="http://studentconnectapi.com#contact" target="_blank">
                                            Request access  <i class="fa fa-external-link"></i>
                                        </a>
                                    </li>

                                    <?php if( getOption('app_key') ): ?>
                                        <li class="nav-item">
                                            <a class="nav-link" href="<?php echo basename(__FILE__) ?>?logout=1" title="leave current session" onclick="alert('You\'re leaving the current session. \nYour api key and secret will be forgotten.')">
                                                Exit session <i class="fa fa-power-off"></i>
                                            </a>
                                        </li>
                                    <?php endif; ?>

                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="main-content">
            <div class="container-fluid">

                <div class="row">
                    <div class="col-lg-12">
                        <h1>Quick-Start Guide</h1>
                        <p class="lead">
                            This is a demo of the StudentConnect API for the tech savy ones.
                            Find out more at <a href="http://studentconnectapi.com" target="_blank">studentconnectapi.com</a>.
                        </p>
                        <hr style="border-color: #19C2FA;"/>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12">

                        <?php

                        if( init_client() ){

                            //request token
                            display('authorize');

                            //request account details
                            display('account');

                            //request a list of institutions
                            display('client');

                        }
                        else
                            display('credentials', TRUE);

                        //stop output if errors were detected
                        if( has_error() )
                            exit();

                        ?>

                    </div>
                </div>

            </div>
        </div>

    </div>

    <div class="footer">

        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-4">
                    <div class="column">
                        <em>
                            <a href="https://www.studentconnectapi.com/" target="_blank">visit StudentConnectAPI.com</a>
                        </em>
                    </div>
                </div>

                <div class="col-lg-8">
                    <ul class="menu list-unstyled">
                        <li><a href="https://docs.studentconnectapi.com" target="_blank">API Docs</a></li>
                        <li><a href="https://studentconnectapi.com/#contact" target="_blank">Contact us</a></li>
                        <li><a href="https://studentmoneysaver.co.uk/advertise" target="_blank">Advertise</a></li>
                        <li><img src="assets/studentconnect-icon.png"/></li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="clearfix"></div>

    </div>

    <?php include_once (__DIR__ . '/include/ui-bootstrap.php' ); ?>

</body>
</html>
