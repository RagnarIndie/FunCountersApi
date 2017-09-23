# FunCountersApi
Simple event counters update and receive api system

## Install
1. ```git clone git@github.com:RagnarIndie/FunCountersApi.git && cd FunCountersApi && ./install.sh```
2. Get some coffee (for a 3 mins)
3. Check out http://api.counters.loc/

## Use
API has 3 main endpoints:
1. ```GET /``` - app status
2. ```GET /summary[.json|.csv]``` - event counters summary. You can choose between output formats by appending ```.json``` or ```.csv``` extensions to the end of this endpoint. Default output format is JSON. 
3. ```POST /counters``` - main event counters update handler 

```POST /counters``` endpoint expects from you 2 params in the request body (in json format):
```javascript
{
  "event": "download",
  "country": "US"
}
```

To get some data for tests just run ```php generate_events.php``` from outside of the Docker env (host machine)


## DB
You can get access to the DB using this MySQL connection settings:
```
Host: 127.0.0.1
Port: 3306
User: root
Pass: root
DB: counters_db
```

## Structure
* ```install.sh``` - initial install script
* ```run.sh``` - runs Docker env
* ```stop.sh``` - stops Docker env
* ```remove-images.sh``` - removes all of the built Docker images before cold run
* ```/FunCountersApi/volumes/mysqldump/counters_db.sql``` - initial DB dump
* ```/FunCountersApi/app/backend``` - backend application sources
* ```/FunCountersApi/generate_events.php``` - simple event generator
