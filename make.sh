#!/bin/bash

####################################################################################################
# Make facility to bring up containers 
# Usage: ./make.sh <direction> <mode>
# 
# @params
#  1. <direction> String, Required, Possible values: up or down
#  2. <mode> String, Optional, Possible values: production or development, Default production
# 
# example: 
# ./make.sh up production
####################################################################################################


# default var MODE to production
MODE="production"

ARGMODE=$2

if [$# -lt 3]]
then
    echo "Please provide second argument as up or down"

fi


# see if second argument has been passed. If it is "development", set mode to it
if [[ "${#ARGMODE}" -ne 0 ]]
then 
    if [[ "${ARGMODE}" != "development" && "${ARGMODE}" != "production" ]]
    then
        echo "Build mode provided is neither development nor production"
        exit 0;
    fi
    # if it is neither development or production, warn & exit
    if [[ "${MODE}" != "development" ]]
    then
        MODE=${ARGMODE}
    fi
fi



####################################################################################################
# Function envup: Sources the .env files for the correct build environment requested
####################################################################################################
envup() {

  local file=$([ -z "${MODE}" ] && echo ".env" || echo ".env.${MODE}")

  if [ -f $file ]; then
    set -a
    source $file
    set +a
  else
    echo "No $file file found" 1>&2
    return 1
  fi
}


####################################################################################################
# Function up: Starts the containers
####################################################################################################
up() {

    printf "Running some checks before we begin.. \n\n" 

    # See if docker exists otherwise notify and wait
    {
        docker ps -q
    } || {
        printf "Docker doesn't seem to running. Please start docker on your computer.\n"
        printf "When docker has finished starting up press [ENTER} to continue starting or Ctrl+C to exit."
        read
    }

    # Does a folder called autocompletees exist
    [ ! -d "./autocompletees" ] && echo "Have you git cloned the repo? If you have, are you in the directory where the repo has been cloned?" && return 0

    printf "All checks passed.. ready to go \n\n"

    printf "Starting Data Indexing Pipeline Containers: Elasticsearch, ZooKeeper, Kafka, Debezium, MySQL..\n\n"

    # Start the data change extraction pipeline. 
    cd ./autocompletees/  && export DEBEZIUM_VERSION=1.4;docker-compose -f docker-compose-es-prod.yaml up -d > /dev/null 2>&1

    printf "Waiting max 35 seconds for ElasticSearch to come up..\n\n"

    # sleep 35 seconds to let elasticsearch come up
    sleep 35

    printf "Populating data..\n\n"

    #populate data
    sh populate.sh > /dev/null 2>&1

    printf "Starting API Containers: Nginx, PHP..\n\n"

    # Start the API
    cd ../autocompleteapi/  &&  docker-compose -f docker-compose.prod.yml up -d --build > /dev/null 2>&1

    printf "Starting Web App Containers: Node/Vue3..\n\n"

    # Start the Web App
    cd ../autocompleteapp/  &&  docker-compose -f docker-compose.prod.yml up -d --build > /dev/null 2>&1

    printf "Finished starting successfully. Please visit : http://localhost/  or http://<IP of the remote server where you built this>. \n\n"

    return 1
}


####################################################################################################
# Function down: Stops the containers
####################################################################################################
down(){

    # Does a folder called autocompletees exist
    [ ! -d "./autocompletees" ] && echo "Are you in the directory where the repo has been cloned?" && return 0

    printf "Stopping Web App Containers..\n\n"

    # are we in the autocompleteapp directory, if not cd into it
    cd ./autocompleteapp/

    # Stop the Web App
    docker-compose -f docker-compose.prod.yml down > /dev/null 2>&1

    printf "Stopping API Containers..\n\n"

    # Stop the API
    cd ../autocompleteapi/  &&  docker-compose -f docker-compose.prod.yml down > /dev/null 2>&1

    printf "Stopping Data Indexing Pipeline Containers: Elasticsearch, ZooKeeper, Kafka, Debezium, MySQL..\n\n"

    # Stop the data change extraction pipeline. 
    cd ../autocompletees/  &&  export DEBEZIUM_VERSION=1.4;docker-compose -f docker-compose-es-prod.yaml down > /dev/null 2>&1

    printf "Finished stopping successfully.\n\n"

    return 1

}


# depending on MODE, source the correcct ./env.${MODE}
envup


# launch the requested command
"$@"
