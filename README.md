## StudentConnect API Client for PHP

This is the PHP Client for StudentConnect API. 
 
### Installation 

    composer require studentconnect/api-client dev-master

### Example code

    <?php 
    
    use StudentConnect\API\Client\Client;
    use StudentConnect\API\Client\Exceptions\ClientException;
    
    try{
   
        $Client = new Client('https://api.endpoint', '{application_key}', '{application_secret}');
        $Client->authorize();
        
        $uri = $Client->generateSignInURI();
        
        echo ( '<a href="'. $uri .'" target="_self">Verify your student account &rarr;</a>' );
   
    }
    catch(ClientException $e){
        throw new App\ApplicationException( $e->getMessage(), $e->getStatus(), $e );
    }
   
    
        
 
### Quickstart

If you have a web server at hand, just set the host's root to /quickstart/web.php and check the guide.   