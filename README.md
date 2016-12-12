# A quick and dirty "Getting Started with Docker, RabbitMQ, Ruby, and PHP/Lumen"

The best part about this project is that, out of the box and assuming you have Docker properly installed on your system, running `docker-compose build` and `docker-compose-up` should re-create this entire project.

## Commands

List containers:  
`docker ps` - for all running containers  
`docker ps` - for all containers

Execute shit:  
`docker exec -ti [CONTAINER_NAME/ID] [COMMAND]`  
`docker exec -ti [CONTAINER_NAME/ID] /bin/bash`  

Attach to container's main process:  
`docker attach [CONTAINER_NAME/ID]`

Docker compose commands to get shit going:  
`docker-compose build` - build from docker-compose.yml  
`docker-compose up` - run from docker-compose.yml

*Missing: `docker build` and `docker run`,  likely important in some cases for testing.*

# Parts

## docker-compose.yml

### Create networks

    networks:
      web:
        driver: bridge
      receiver_nw:
        driver: bridge

### Create services

    rabbitmq:
      image: rabbitmq:3
      networks:
        web:
          aliases:
            - rabbitmq
        receiver_nw:
          aliases:
            - rabbitmq
    receiver-service:
      build:
        context: ./receive-rb
      networks:
        - receiver_nw
    web:
      build:
        context: ./sender-lumen
      ports:
        - "8000:80"
      networks:
        - web

#### Build images

Images are built either from remote/locally existing images, or from custom Dockerfiles.

##### Build from Local or Docker Hub image

    rabbitmq:
      image: rabbitmq:3

Just put the image name and tag. (`NAME:TAG`)

##### Build from Dockerfile

    receiver-service:
      build:
        context: ./receive-rb

The context provides the path which the `docker build` operation will be run from. This means it will look for the Dockerfile in that path. An additional setting for build (Dockerfile) allows you to set the name of the Dockerfile to be used.

#### Set network aliases

    rabbitmq:
      networks:
        web:
          aliases:
            - rabbitmq
        receiver_nw:
          aliases:
            - rabbitmq

Network aliases are used within shared networks to specify a *hostname* from which other containers can access it through. For example, RabbitMQ clients just needed to change their connection settings to use the hostname *rabbitmq*, as described in above example.

## Rabbitmq

The standard `rabbitmq:3` image was used. Network aliases needed to be set to allow access to the MQ from other containers in shared networks, as described in the *Set network aliases* section.

## PHP+Apache

Use Dockerfile command to grab an appropriate image:  
`FROM php:5.6-apache`

### Some Apache modules are disabled

Use Dockerfile command to enable required modules:  
`RUN a2enmod rewrite`

### Override 000-default.conf to change DocumentRoot (if required)

Use Dockerfile command to override virtual host DocumentRoot:  
`COPY ./000-default.conf /etc/apache2/sites-available`

### [A lot of PHP extensions are missing.](https://github.com/docker-library/php/issues/75)  
Use Dockerfile command to install PHP extensions:  
`RUN docker-php-ext-install bcmath`

## Lumen/Laravel

*(.dockerignore and updating via Composer must be implemented). In the meanwhile, `composer install` must be manually run in the Lumen container directory.*

Make sure the DocumentRoot for the container is set to the public folder, or else you will run into [problems with the request not being properly handled.](http://stackoverflow.com/questions/29728973/notfoundhttpexception-with-lumen)

## Ruby (Vanilla)

Using the `-onbuild` tagged Ruby images from Docker Hub automatically run utilities such as `bundle install`. Go check them out if clarification is required.

Permissions must be granted for Ruby scripts to be executed. Below is an example of the permissions modification required for execution:

    RUN chmod 755 ./receive.rb
    CMD ["./receive.rb"]

By default, STDOUT does not output until the end of execution. To set it for output at runtime, use the following line:  
`STDOUT.sync = true`

When all of the services start, RabbitMQ will not be ready to create connections. For the first few seconds of running, any attempts to connect to it are refused. This causes connection issues, so the following snippet was used to connect:

    conn = Bunny.new(:host => "rabbitmq", :automatically_recover => false)
    begin
      conn.start
      puts "we in this bitch"
    rescue Bunny::TCPConnectionFailed => e
      puts "stupid dog"
      sleep 1.1
      retry
    end
