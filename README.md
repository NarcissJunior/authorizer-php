# Authorizer


Authorizer is an application responsible to validate and make credit card transactios.
The application will read a text file with use cases that determine if a transaction can occur normally or not and return a response considering the input.

We have some business rules that can be checked here:

- Unique account
- Active credit card for this account
- Sufficient balance to complete the transaction
- Duplicated transactions from same merchant
- Multiple transactions in a short period of time


## Running

To build and run the application you need PHP version 7.4 installed or docker.
Below you can see both examples.


### Docker

To build our image, just run the follow the command:

    docker build -t authorizer .

This command build the application and set a Tag with name "Authorizer" so we can reference our image.
To run the application, use this command and choose a file to see the output:

`docker run -i authorizer < documents/{file-name}.txt`


### Local

You can start the application using the following command:

    php index.php < documents/{file-name}.txt

This command will run the application passing the specified file. It will receive in the standard Input and proccess the file based on the rules above.
After the verifications, you can see the results in the standard output in Json.

## Tests

To run the application tests use this command:

    "./vendor/bin/phpunit" tests/unit