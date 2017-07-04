# hwm-server
Server handling data from hwm client

## Software requirements
* PHP > 7.0
* Running webserver with vhost/location pointing to web folder

## Configuration
Copy file .env.dist to .env

Fill .env variables

| Variable          | Description                                                  |
| ----------------- | ------------------------------------------------------------ |
| AUTH_TOKEN        | Authentication token identical with auth token of HWM server |
| INFLUXDB_HOST     | Host of influxDB                                             |
| INFLUXDB_PORT     | Port of influxDB                                             |
| INFLUXDB_USERNAME | Username of influxDB                                         |
| INFLUXDB_PASSWORD | Password of influxDB                                         |
| INFLUXDB_DATABASE | Schema used in influxDB                                      |