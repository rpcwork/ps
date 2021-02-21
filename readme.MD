

# Autocomplete using Elasticsearch, Node/Vue3, Kafka, Debezium, MySQL, NginX/PHP

This demo implements a web app to look up the calling code for any country using a Vue3 autocomplete component that hits an Nginx API supported by a PHP 7.2 FastCGI Process Mananger and a Data Change-Extract-Index pipeline to get the data over to ElasticSearch. Elasticsearch will provide us the fast indexed data source for our autocomplete via the NginX API. See here how to Build and run the [Web app](#How-to-Build-and-Run-the-Web-app) or  [ Test the API](#How-to-test-the-API ). 




### System Requirements
 - Atleast 4GB Ram
 - Docker
 - Docker-compose




# How it works
![Autocomplete Fast Indexed Architecture](https://user-images.githubusercontent.com/3967769/108647824-98544a80-7487-11eb-9061-bdb196a614c9.png)
The application uses docker-compose to provision a system of eight containers.

### Node/Vue3 Web app
 - Container 1: Web app powered by Node/Vue3 available at http://<API_HOST>
 

### Data Indexing Pipeline
 - Container 2: Mysql available at <API_HOST> Port 3306. This stores the data about countries, iso codes and their calling codes. 
 - Container 3: Apache Kafka available at <API_HOST> Port 9092. Debezium connects the Mysql on 3306 to 9092 on Kafka. Kafka receives the Data Change events via Debezium on container 5 and makes them available as "topics" to which clients can subscribe on port 9092. 
 - Container 4: ElasticSearch available at <API_HOST> Port 9200. Debezium Confluent JDBC Connector running on container 5 enables ElasticSearch Container to subscribe to Kafka topics over Port 9092
 - Container 5: Debezium Connector on <API_HOST> Port 8083 and 5005. Debezium is a distributed platform that turns existing database events into event streams, so applications can quickly react to each row-level change in the databases. 
 - Container 6: Zookeeper live configuration manager available on Ports 2181, 2888 and 3888. ZooKeeper is a centralized service for maintaining configuration information, naming, providing distributed synchronization, and providing group services. 


### API
 - Container 7: Nginx API Gateway
 - Container 8: PHP 7.2 Fast CGI process manager gateway running LUMEN. LUMEN is a "small footprint" version of Laravel designed for building blazing fast microservices.



Here is the docker containers list of a healthy demo running on a Ubuntu 18.04 LTS Cloud Server at 208.117.84.30.

```bash
$ docker ps
CONTAINER ID   IMAGE                                                 COMMAND                  CREATED        STATUS        PORTS                                                                                        NAMES
c507b7ae4f4b   autocompleteapp_autocompleteapp                       "/docker-entrypoint.…"   10 hours ago   Up 10 hours   0.0.0.0:80->80/tcp                                                                           prod
8227755e7bd0   autocompleteapi_autocompleteapi_nginx                 "/docker-entrypoint.…"   10 hours ago   Up 10 hours   80/tcp, 0.0.0.0:8081->81/tcp                                                                 autocompleteapi_autocompleteapi_nginx_1
93c883c6e2c2   autocompleteapi_autocompleteapi_php                   "docker-php-entrypoi…"   10 hours ago   Up 10 hours   0.0.0.0:9000->9000/tcp                                                                       autocompleteapi_autocompleteapi_php_1
64295ee8805e   debezium/connect-jdbc-es:1.4                          "/docker-entrypoint.…"   10 hours ago   Up 10 hours   0.0.0.0:5005->5005/tcp, 8778/tcp, 9092/tcp, 0.0.0.0:8083->8083/tcp, 9779/tcp                 autocompletees_connect_1
498f7e32e36e   debezium/kafka:1.4                                    "/docker-entrypoint.…"   10 hours ago   Up 10 hours   8778/tcp, 9779/tcp, 0.0.0.0:9092->9092/tcp                                                   autocompletees_kafka_1
4e29dddad068   debezium/example-mysql:1.4                            "docker-entrypoint.s…"   10 hours ago   Up 10 hours   0.0.0.0:3306->3306/tcp, 33060/tcp                                                            autocompletees_mysql_1
ee0fbf74a3f5   docker.elastic.co/elasticsearch/elasticsearch:7.3.0   "/usr/local/bin/dock…"   10 hours ago   Up 10 hours   0.0.0.0:9200->9200/tcp, 9300/tcp                                                             autocompletees_elastic_1
7316e95ab275   debezium/zookeeper:1.4                                "/docker-entrypoint.…"   10 hours ago   Up 10 hours   0.0.0.0:2181->2181/tcp, 0.0.0.0:2888->2888/tcp, 8778/tcp, 0.0.0.0:3888->3888/tcp, 9779/tcp   autocompletees_zookeeper_1
```





# How to Build and Run the Web app?

The docker-compose will orchestrate a system of 8 containers. This can get quite power hungry even on a reasonably well-appointed Mackbook. So, please connect to power if you will be running this demo for long. If you have less than 4GB of RAM on the target machine, containers will take longer to come up - especially elasticsearch container.

1) Ensure that you have docker installed and it is running.

2) Ensure that you have the docker-compose command installed. If you have docker desktop on Mac/Windows, you have docker-compose installed already. Otherwise, see here for instructions: https://docs.docker.com/compose/install/

```bash
# Instructions for Ubuntu 18.04 LTS
sudo curl -L https://github.com/docker/compose/releases/download/1.21.2/docker-compose-`uname -s`-`uname -m` -o /usr/local/bin/docker-compose
sudo chmod +x /usr/local/bin/docker-compose
```

3) Clone this repo

```bash
$ git clone https://github.com/rpcwork/ps.git
```

4) Change to the repo directory

```bash
$ cd ps
```

5) Create the environment files. We would use consul agents to manage and propogate these settings outside of this demo

```bash

$ touch .env.production; cat >.env.production <<- EOM
MYSQL_ROOT_PASSWORD=debezium
MYSQL_USER=mysqluser
MYSQL_PASSWORD=mysqlpw
EOM

touch .env.development; cat >.env.development <<- EOM
MYSQL_ROOT_PASSWORD=debezium
MYSQL_USER=mysqluser
MYSQL_PASSWORD=mysqlpw
EOM

```


6) Launching on local machine? Skip to step 5. Otherwise, open ./autocompleteapp/.env.production and change the value of VUE_APP_API_HOST to the main FQDN/IP of the EC2 instance/Remote Server. If you are going to provision this to AWS Fargate, set this host value to the IP of the Service Loadbalancer.


7) Build and launch the containers

```bash
# we default to production build
$ ./make.sh up
```

8) Load the App in the browser. 

```php
Visit: http://localhost/ 

or 

http://<IP of the remote server where you built this>

# I have this repo provisioned and running on ubuntu cloud server running at 208.117.84.30
# So you could visit the following in a browser
# http://208.117.84.30
```

9) Once you are done with demo, deprovision the containers

```bash
$ ./make.sh down
```


 
# How to test the API? 

Before you can test the API, you must first build the app on a local machine or a remote machine by following the directions listed under [Web app](#How-to-Build-and-Run-the-Web-app). Then, come back here to test the API.

```bash

# API HOST will be "localhost" or the IP Address/FQDN of the server where the demo has been provisioned 
curl -X GET "API_HOST:8081/search?q=ind"

# I have this repo provisioned and running on ubuntu cloud server running at 208.117.84.30
# curl -X GET "208.117.84.30:8081/search?q=ind" 
```