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

    <link rel="stylesheet" media="all" href="https://cdn.studentmoneysaver.co.uk/assets/css/bootstrap.min.css?v=v3.0.4.6"/>
    <link rel="stylesheet" media="all" href="https://cdn.studentmoneysaver.co.uk/assets/css/font-awesome.min.css?v=v3.0.4.6"/>
    <link rel="stylesheet" media="all" href="https://cdn.studentmoneysaver.co.uk/assets/css/jquery-ui.min.css?v=v3.0.4.6"/>

    <link href="https://fonts.googleapis.com/css?family=Lato:400,700" rel="stylesheet" type="text/css"/>
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:400,700" rel="stylesheet" type="text/css"/>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/9.3.0/styles/default.min.css" rel="stylesheet" type="text/css"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/9.3.0/styles/idea.min.css" rel="stylesheet" type="text/css"/>

    <style type="text/css">
        html, body{
            margin: 0px auto;
            padding: 0px;
            font-family: "Lato", "Helvetica Neue", Helvetica, Arial sans-serif;
            font-size: 16px;
            background: #f5f5f5;
        }

        p{
            font-size: 18px;
        }

        h4{
            font-size: 20px;
        }

        body{
            margin-top: 60px;
        }

        h1, .h1, h2, .h2, h3, .h3{
            margin-top: 10px;
            font-weight: bold;
        }

        h3{
            font-size: 1.1em;
        }

        .alert{
            border-radius: 4px;
            border-width: 4px;
            border-style: solid;
            font-size: 100%;
        }

        .error{
            background: #FF6F5C;
            border-color: #FFA79C;
            color: #fafafa;
        }

        .panel{
            margin-bottom: 40px;
        }

        pre{
            font-family: "Source Sans Pro", monospace sans-serif;
            background: #fff;
            color: #222222;
            font-size: 100%;
            border: 2px #dfdfdf solid;
            box-shadow:inset 2px 2px 2px rgba(0, 0, 0, .1);
            padding: 0px;
        }

        pre:hover{
            border-color: #05a6db;
        }

        .header{
            top: 0px;
            width: 100%;
            z-index: 99;
            position: fixed;
            background: #19C2FA;
            border-bottom: 1px solid #05a6db;
            box-shadow: 0px 0px 9px rgba(0, 0, 0, 0.4);
            color: #fff;
        }

        .header a{
            color: #fff;
            font-weight: bold;
        }

        .brand{
            padding: 7px 0px;
            display: block;
            font-size: 1em;
        }

        .logo img{
            max-height: 32px;
        }

        .navbar{
            border: none;
            margin-bottom: 0px;
        }

        .navbar-nav>li>a.badge{
            border-radius: 0px;
            font-size: 15px;
            background: none;
        }

        .navbar-nav>li>a.badge.verified{
            background: none;
        }

        .navbar-nav>li>a.badge:hover{
            background: none;
        }

        .navbar-nav>li>a,
        .navbar-nav>li>a.badge{
            padding-top: 15px;
            padding-bottom: 15px;
            font-size: 100%;
        }

        .navbar-nav>li>a:hover,
        .navbar-nav>li>a:focus,
        .navbar-nav>li>a:active{
            background: #05a6db;
        }

        .panel{
            border-color: #19C2FA;
        }

        .panel-default>.panel-heading{
            background: #ebebeb;
        }

        .panel h4{
            margin-bottom: 10px;
            width: 100%;
            border-bottom: 1px dashed #dddddd;
            padding: 10px 0px;
            padding-top: 20px;
        }

        .footer{
            margin-top: 20px;
            min-height: 100px;
            background: #32353b;
            padding: 20px 0px;
            color: #ffffff;
        }

        .footer .column{
            padding: 12px 0px;
            text-transform: uppercase;
            font-weight: bold;
        }

        .footer a{
            color: #ffffff;
        }

        .footer .menu {
            list-style-type: none;
            display: inline-block;
            float: right;
            margin-bottom: 0px;
        }

        .footer .menu li {
            display: inline-block;
            float: left;
            text-transform: uppercase;
            font-weight: bold;
            margin-right: 40px;
        }

        .footer .menu li a {
            color: #ffffff;
            text-decoration: none;
            padding: 12px 0px;
            display: block;
        }
    </style>
</head>
<body>

    <div class="header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-6">
                    <div class="brand">
                        <a class="logo" href="<?php echo basename(__FILE__) ?>" target="_self">
                            <img src="assets/studentconnect-logo.png"/>
                        </a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="pull-right menu">
                        <nav class="navbar">
                            <ul class="nav navbar-nav">
                                <li class="nav-item">
                                    <?php if( is_verified() ): ?>
                                          <a class="badge verified"  href="#profile">
                                              <strong>verified <i class="fa fa-check"></i> </strong>
                                          </a>
                                    <?php else: ?>
                                        <a class="badge" href="#profile-signin-url">
                                            <strong>anonymous <i class="fa fa-circle"></i> </strong>
                                        </a>
                                    <?php endif; ?>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="https://docs.studentconnectapi.com" target="_blank">
                                        API Docs  <i class="fa fa-external-link"></i>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="http://studentconnectapi.com#contact" target="_blank">
                                        Request an App Key  <i class="fa fa-external-link"></i>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="<?php echo basename(__FILE__) ?>?logout=1" title="leave current session" onclick="alert('You\'re leaving the current session. \nYour api key and secret will be forgotten.')">
                                        <i class="fa fa-sign-out"></i>
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">

        <div class="row">
            <div class="col-lg-12">
                <h1>Quickstart Guide</h1>
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

    <div class="footer">

        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-4">
                    <div class="column">
                        <em>
                            <a href="https://www.studentmoneysaver.co.uk/">visit StudentMoneySaver.co.uk</a>
                        </em>
                    </div>
                </div>

                <div class="col-lg-8">
                    <ul class="menu list-unstyled">
                        <li><a href="https://docs.studentconnectapi.com">API Docs</a></li>
                        <li><a href="https://studentconnectapi.com/#contact">Contact us</a></li>
                        <li><a href="https://studentmoneysaver.co.uk/advertise">Advertise</a></li>
                        <li>
                            <img src="assets/studentconnect-icon.png"/>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

    </div>

    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/9.3.0/highlight.min.js"></script>
    <script type="text/javascript">hljs.initHighlightingOnLoad();</script>
</body>
</html>
