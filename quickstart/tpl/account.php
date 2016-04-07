<?php
/**
 * StudentConnect API Client - Authorize Demo Template
 * @author adrian7 (adrian@studentmoneysaver.co.uk)
 * @version 1.0
 */

global $Client; ?>

<div class="panel panel-default">
    <div class="panel-heading" id="profile"><h3><i class="fa fa-cog"></i> Profile</h3></div>
    <div class="panel-body">

        <?php if( $Client->getProfile() ) : ?>

            <div class="alert alert-info">
                <p>
                    <i class="fa fa-info-circle"></i> The profile exists and we hav access to it using the token.
                </p>
            </div>

            <h4>Request</h4>
            <?php echo $Client->getFormattedRequest('<pre><code>', '</code></pre>'); ?>

            <h4>Response</h4>
            <?php echo $Client->getFormattedResponse('<pre><code>', '</code></pre>'); ?>

            <h4>Code</h4>
            <?php echo code_snippet('account-data'); ?>

        <?php else: $url = $Client->generateSignInURI(); ?>

                <div class="alert alert-info">
                    <p>
                        <i class="fa fa-info-circle"></i> We don't have yet access to the the profile.
                        We generate the sign in URL and ask them to verify their account.
                    </p>
                </div>

                <h4>Request</h4>
                <?php echo $Client->getFormattedRequest('<pre><code>', '</code></pre>'); ?>

                <h4>Response</h4>
                <?php echo $Client->getFormattedResponse('<pre><code>', '</code></pre>'); ?>

                <h4>Code</h4>
                <?php echo code_snippet('signin-uri'); ?>

                <h4 id="profile-signin-url">Result</h4>
                <a class="btn btn-lg btn-info" href="<?php echo $url;?>" target="_self">
                    Verify your student account <i class="fa fa-arrow-right"></i>
                </a>

        <?php endif; ?>

    </div>
</div>


<h3>Advanced</h3>