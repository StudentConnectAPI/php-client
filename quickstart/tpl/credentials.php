<?php
/**
 * StudentConnect API Client - Template to ask for credentials
 * @author adrian7 (adrian@studentmoneysaver.co.uk)
 * @version 1.0
 */

$env = new \Dotenv\Dotenv(__DIR__ . '/../../');
$env->load();

$external = ( __DIR__ . '/../../.endpoints' );

$endpoints = [
     ( $endpoint = getOption('api_endpoint') ? $endpoint : 'https://v1.teststudentconnectapi.com/api'),
    'https://v1.studentconnectapi.com/api'
];

if( file_exists( $external ) )
    $endpoints = array_merge($endpoints, @explode("\n", @file_get_contents($external)))

?>

<div class="panel panel-default">
    <div class="panel-heading"><h3><i class="fa fa-cogs"></i> Enter your API credentials</h3></div>
    <div class="panel-body">
        <form class="form form-horizontal" method="post" action="<?php echo basename( $_SERVER['SCRIPT_FILENAME'] ); ?>" target="_self">
            <div class="form-group">
                <label for="api_endpoint" class="col-sm-2 control-label">Endpoint:</label>
                <div class="col-sm-10">
                    <div class="input-group">
                        <select class="form-control input-lg" id="api_endpoint" name="api_endpoint">

                            <?php if( $default = getenv('API_ENDPOINT') ): ?>
                                <option selected><?php echo $default; ?></option>
                            <?php endif; ?>

                            <?php foreach ($endpoints as $endpoint): ?>
                                    <option><?php echo $endpoint; ?></option>
                            <?php endforeach; ?>

                        </select>
                        <span class="input-group-addon"><a href="#endpoint" onclick="window.open(document.getElementById('api_endpoint').value);" target="_blank"><i class="fa fa-external-link"></i></a></span>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="app_key" class="col-sm-2 control-label">Application Key:</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control input-lg" id="app_key" name="app_key" placeholder="..." value="<?php  echo ( $appKey = getOption('app_key') ? $appKey : getenv('API_KEY') ); ?>"/>
                </div>
            </div>
            <div class="form-group">
                <label for="app_secret" class="col-sm-2 control-label">Application Secret:</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control input-lg" id="app_secret" name="app_secret" placeholder="..." value="<?php  echo ( $appSecret = getOption('app_secret') ? $appSecret : getenv('API_SECRET') ); ?>"/>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-4 col-sm-8">
                    <button type="submit" class="btn btn-lg btn-info">Submit</button>
                </div>
            </div>
        </form>
    </div>
</div>
