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

        <?php if( $Client->getCurrentProfile() ) : ?>

            <div class="alert alert-info">
                <p>
                    <i class="fa fa-info-circle"></i>
                    The profile exists and we have access to it using the token. <br/>
                    Depending if the user granted us access, to their data, we may see the full profile
                    or just a garbled email address.
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
                <form name="signInForm" id="signInForm" method="post" target="_blank" action="<?php echo $url;?>">
                    <?php $Client->tokenizeForm(); ?>
                    <button class="btn btn-lg btn-success" type="submit">
                        <em><i class="fa fa-graduation-cap"></i> Verify your student account </em>
                    </button>
                </form>

        <?php endif; ?>

    </div>
</div>


<h2>Advanced</h2>