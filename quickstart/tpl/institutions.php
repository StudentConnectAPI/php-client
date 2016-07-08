<?php
/**
 * StudentConnect API Client - Authorize Demo Template
 * @author adrian7 (adrian@studentmoneysaver.co.uk)
 * @version 1.0
 */

global $Client;

$token = ( $t = getOption('api_token') ) ? $t : NULL; ?>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3><i class="fa fa-cog"></i> Get a list of institutions</h3>
    </div>
    <div class="panel-body">

        <?php if( $Client->get('/institutions', ['country'=>'GB', 'limit'=>25]) ): ?>

                <h4>Request</h4>
                <?php echo $Client->getFormattedRequest('<pre><code class="json">', '</code></pre>'); ?>

                <h4>Response</h4>
                <?php echo $Client->getFormattedResponse('<pre><code class="json">', '</code></pre>'); ?>

                <h4>Code</h4>
                <?php echo code_snippet('institutions'); ?>

        <?php endif; ?>

    </div>
</div>
