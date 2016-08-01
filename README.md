## StudentConnect API Client for PHP ![build status](https://circleci.com/gh/StudentConnectAPI/php-client.png?circle-token=6686607caa1cef0caa8510ffc5280cef85de6f10)

This is the official PHP client for [StudentConnect API](https://studentconnectapi.com)
 
### Installation 

    composer require studentconnect/api-client

### Example code

    <?php 
    
    use StudentConnect\API\Client\Client;
    use StudentConnect\API\Client\Exceptions\ClientException;
    
    try{
   
        $Client = new Client('https://api.endpoint', '{app_key}', '{app_secret}');
        $Client->authorize();
        
        $uri = $Client->tokenizeURI( $Client->generateSignInURI() );
        
        echo ( '<a href="'. $uri .'">Sign In with StudentConnect &rarr;</a>' );
   
    }
    catch(ClientException $e){
        throw new App\ApplicationException( $e->getMessage(), $e->getStatus(), $e );
    }
   
     
### Quickstart

If you have a web server at hand, just set the host's root to /quickstart/web.php and check the guide.   
Documentation can be found here: [docs.studentconnectapi.com](https://docs.studentconnectapi.com)