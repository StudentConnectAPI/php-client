<?php
/**
 * StudentConnect API Client - Authorize Demo Template
 * @author adrian7 (adrian@studentmoneysaver.co.uk)
 * @version 1.0
 */

global $Client;

/**
 * @var \StudentConnect\API\Client\Token|null
 */
$token = NULL;

$token = ( $t = getOption('api_token') ) ? $t : NULL; ?>

<h2>Basics</h2>
<p>How you authenticate, request sign in and profile details</p><hr/>

<div class="panel panel-default">
    <div class="panel-heading"><h3><i class="fa fa-cog"></i> Client authorization</h3></div>
    <div class="panel-body">

        <?php if( $token and $token->isValid() ) : ?>

            <div class="alert alert-info">
                <p>
                    <i class="fa fa-info-circle"></i> Our client is already authorized.
                    Authorization token is shown below.
                </p>
            </div>

            <pre><code class="php">"<?php echo $token->getValue(); ?>"</code></pre>

            <h4>Code</h4>
            <?php
                echo code_snippet('client-auth-token', [
                        'endpoint'  => API_ENDPOINT,
                        'key'       => APP_KEY,
                        'secret'    => APP_SECRET
                ]);
            ?>

        <?php else: $Client->authorize(); setOption('api_token', $Client->getToken()); ?>

            <div class="alert alert-info">
                <p>
                    <i class="fa fa-info-circle"></i>
                    In order to interact with the API we need to authorize our client first. We send a signed request to the
                    <em>/authorize</em> resource, and receive a unique token.
                </p>
            </div>

                <h4>Request</h4>
                <?php echo $Client->getFormattedRequest('<pre><code>', '</code></pre>'); ?>

                <h4>Response</h4>
                <?php echo $Client->getFormattedResponse('<pre><code>', '</code></pre>'); ?>

            <h4>Code</h4>
            <?php
                echo code_snippet('client-auth', [
                        'endpoint'  => API_ENDPOINT,
                        'key'       => APP_KEY,
                        'secret'    => APP_SECRET
                ]);
            ?>

        <?php endif; ?>

    </div>
</div>
