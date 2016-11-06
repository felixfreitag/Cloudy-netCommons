# Cloudy-netCommons
The development branch of Cloudy for the netCommons project

This repository contains developments made on the Cloudy platform within the netCommons research project.

### Testing

The script "cloudynitzar.sh" should be run to convert a Debian host into a Commnunity Networking cloud in a box.

Cloudynitzar is a shell script that turns your plain Debian system into a community networking cloud in a box (i.e. a full-featured Cloudy device). It might work on Debian derivatives too, like Ubuntu. 

### Requirements
A system with an up-to-date Debian 8 *Jessie* installation with `curl`, `lsb-release` and an Internet connection.

### Procedure
From your Debian system run, as root:

````sh
apt-get update; apt-get install -y curl lsb-release
curl -k https://raw.githubusercontent.com/Clommunity/cloudynitzar/master/cloudynitzar.sh | bash -
````

### First steps after intallation

To access the web management interface you have to open your browser and type the address of the Cloudy machine in the port 7000.

http://ADDRESS:7000

Credentials:

    User: <your root user>
    Password: <your password>

