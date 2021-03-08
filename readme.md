The main purpose of this application is to communicate with RabbitMq queue messaging system via mqtt protocol.
You can gain access to the application via ldap credentials.

login: tesla
password: password

controller methods:
- publisher - action which send message to rabbitmq on given topic
- subscriber - action which subscribe you to rabbitmq topic where you can listen to messages
- jsonEntry - action where you can paste your json file and receive xml version of it
- parseJson - action which parsing the pasted json file to xml
- charts - generate 3 d chart based on the points from file - public/data.csv