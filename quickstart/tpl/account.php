<?php
/**
 * StudentConnect API Client - Authorize Demo Template
 * @author adrian7 (adrian@studentmoneysaver.co.uk)
 * @version 1.0
 */

global $Client; ?>

<div class="panel panel-default">
    <div class="panel-heading" id="profile"><h3><i class="fa fa-cog"></i> Profile data</h3></div>
    <div class="panel-body">

        <p>
            To obtain access to a user profile, they'll have to sign up or log in using your token. <br/>
            The easiest way to achieve this, is to build a form with a hidden token field and submit it to the
            signin URI. To get a signin uri, you need to make a POST request to the <code>/signin</code> API resource
            and disable forwarding using <code>forward = 0</code>. <br/>

            The php client provides a handy method for this:
            <code>Client::generateSignInURI()</code>.

            Also adding a hidden token field to your form is as easy as <code>$Client->tokenizeForm()</code> .
        </p>

        <p>
            To capture profile data, you make a GET request to the <code>/profile</code> resource.<br/>
            If the token has been associated with a profile, you'll get a 200 success response code and the data your client
            has been given access to. <br/>
        </p>

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
                        <i class="fa fa-info-circle"></i> We don't have yet access to the the user profile.
                        We generate the sign in URL and ask them to verify their account. <br/>
                        <a href="#">Check this video</a> to find out how you can verify as a student using the demo platform.
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
                    <button class="btn btn-lg btn-success" type="submit" style="padding: 0px; border: none;">
                        <img src="https://cdn.studentconnectapi.com/branding/buttons/studentconnect-button-blue-sm.png"/>
                    </button>
                </form>

        <?php endif; ?>

    </div>
</div>


<h2>Advanced</h2>