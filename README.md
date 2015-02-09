# LISSA Kickstart Distribution

LISSA is an open source technology stack for real time messaging in second
screen applications. It allows media providers to publish live and on-demand
video streams and push related notifications to clients.

The LISSA Kickstart profile is a reusable Drupal 8 distribution for managing
and publishing events and notifications. It comes pre-configured with the
following functionality:

- Real-time push message API through the LISSA backend stack for publishing
  live notifications.
- REST API for publishing events.
- REST API for publishing on-demand notifications.
- A proof-of-concept for soccer matches with contextual soccer statistics.
- Twitter integration for pushing notifications to a Twitter account.

LISSA is separated into four projects:

- [Kickstart](https://github.com/oneagency/lissa_kickstart): The Drupal installation profile for the backend interface.
- [Infrastructure](https://github.com/oneagency/lissa_infrastructure): Vagrant and Chef scripts for provisioning a box with the services needed for running LISSA.
- [Deploy](https://github.com/oneagency/lissa_deploy): Capistrano script for deploying LISSA Kickstart to a Vagrant box or remote servers.
- [Worker](https://github.com/oneagency/lissa_worker): PHP script that parses and forwards notifications from a message queue to a push stream server. 

## Installation

### Using Vagrant

The recommended way to install the LISSA distribution is by using the LISSA
[infrastructure](https://github.com/oneagency/lissa_infrastructure) and
[deploy](https://github.com/oneagency/lissa_deploy) repositories on GitHub.
The infrastructure repo will provision a Vagrant box containing all the
required services and the deploy repo will install and configure a Drupal 8
instance with the LISSA Kickstart distribution.

#### Requirements

- Bundler
- OSX: Xcode command line tools: xcode-select --install
- Virtualbox 4.3.10+
- Vagrant 1.6.3
- vagrant-omnibus plugin
- vagrant-hostsupdater plugin
- Git

#### Vagrant setup

```bash
git clone https://github.com/oneagency/lissa_infrastructure
cd lissa_infrastructure
vagrant up --provision`
```

#### Drupal installation

```bash
git clone https://github.com/oneagency/lissa_deploy
cd lissa_deploy
bundle install
bundle exec cap local deploy
```

When Drupal has been installed you can go to <http://admin.lissa.dev> and log in with admin:admin.

### Using Phing

You can set up the Drupal distribution with Phing following these steps:

- cd to the root of this repository
- Execute the following command: phing -Ddocroot=/path/to/docroot
- Replace /path/to/docroot with the path of your virtual host.

The following steps will be executed:

- Create a docroot directory under /path/to/docroot
- Execute drush make on the build.make file
  - Drush make will set up drupal 8 core
  - Drush make will add the lissa_kickstart profile to the profiles directory
  - Drush make will execute the lissa_kickstart.make file
- Execute drush site-install with the parameters provided in
  build.defaults.properties

### Manual Installation

If you decide to install LISSA Kickstart manually please keep the following
things in mind:

- Use the Drupal core version specified in the build.make file. Other versions
  of Drupal 8 may not be compatible.
- Install the contrib modules specified in the lissa_kickstart.make file.
- Install the services (see the [infrastructure repo](https://github.com/oneagency/lissa_infrastructure)
  for versions and configuration):
  - A RabbitMQ server
  - An Nginx push stream server
  - A service running the [worker PHP script](https://github.com/oneagency/lissa_worker)


### Server Installation

You can provision your own server for a LISSA Kickstart installation by using
the [infrastructure](https://github.com/oneagency/lissa_infrastructure) repo
on GitHub. This contains a set of Chef cookbooks with all the necessary services
for running the full LISSA technology stack.

After provisioning a server you can clone the
[deploy](https://github.com/oneagency/lissa_deploy) repository for deploying the
distribution using capistrano.

#### Distributed Servers

The [infrastructure](https://github.com/oneagency/lissa_infrastructure) repo
has support for provisioning the backend across multiple servers. See the
documentation included with the repository for more information.

## Configuration

### AMQP Server

By default LISSA Kickstart will push its notifications to an AMQP server running on localhost:5672. You can change the address and credentials on <http://admin.lissa.dev/admin/config/services/notification-push/settings>.

### Twitter

Twitter messages are by default pushed by a locked demo account. You can use another Twitter account by changing the settings at <http://admin.lissa.dev/admin/config/services/notification-twitter/settings>.

## Demo

You can test the application using the demo web client located at <http://admin.lissa.dev/profiles/lissa_kickstart/test/client/demo.html>.

The test web application will:

- Load all events from the Drupal 8 REST event API.
- Load all existing notifications from the Drupal 8 REST notification API.
- Set up websocket connections to all events.

Notifications added to events will show up automatically in the test web application.

## Architecture

![LISSA Component Diagram](doc/component-diagram.png)

The LISSA stack is divided in the following components:
 
### Drupal 8

The administration backend where operators manage events and notifications.
Using the views and rest modules it also provides a REST API for fetching all
published events and past notifications.

Runs on port 80 in the default infrastructure single server setup, login with admin/admin in the local setup:

<http://admin.lissa.dev>

### RabbitMQ MessageQueue

A message queue for storing and forwarding the real time notifications to
clients. External services like Facebook, Twitter or other Drupal sites can
plugin to the queue to send additional data in real time to clients.

Runs on port 15672 in the default infrastructure single server setup, login with guest/guest in the local setup:

<http://admin.lissa.dev:15672>

### PHP Worker

Parses the notifications from the message queue and forwards them to the nginx
push stream server. This can be used for additional processing prior to sending
the data to clients. It also increases scalability by providing multiple
workers.

There's an implementation that is set up either using the infrastructure repo or
by running the worker.php script from the [worker repo](https://github.com/oneagency/lissa_worker)
using a process manager like supervisord.

### Nginx push stream server

Allows websocket connections for pushing the notification to clients.

Runs on port 8080 with the following endpoints:

- /publish/uuid: publish notifications to all clients
- /ws/uuid: websocket endpoint for clients.

uuid can be replaced by the UUID of the event node a clients wants to receive
notifications from.

## APIs

### Events API

The events API is a JSON feed listing all public event nodes and their meta data. It can be accessed using the following method:

- HTTP GET request to http://admin.lissa.dev/api/events
- Extra request header: Accept: application/ext+json

Without the header only a limited set of data will be returned. The extended format will also load data for referenced entities (images, players, teams, etc.).

### Notification Replay API

The notification replay API is a JSON feed that returns existing notifications for a given event. It can be used to replay an event or allow users to watch an already ongoing event.

The API can be accessed using the following method:

- HTTP GET request to http://admin.lissa.dev/api/notifications/[event-uuid] 
- Replace [event-uuid] by the uuid of the event you're requesting notifications for.
- Extra request header: Accept: application/ext+json

### Message Queue

The message queue is an AMQP service that accepts notifications related to the events. The Drupal 8 backend will post the notification entity actions to this queue so the worker can forward them to the websocket connections.

| Type        | Address | Credentials |
| ----------- |--------------|------|
| RabbitMQ server	 | admin.lissa.dev:5672 | guest / guest |
| RabbitMQ queue | content_notification | |

### Notification Stream

The notifications are available via an [Nginx push stream](http://wiki.nginx.org/HttpPushStreamModule). Each event has its own channel (using the event UUID) where notifications will be sent to in JSON format.

There's an example client (JS) available on <http://admin.lissa.dev/profiles/lissa_kickstart/test/client/demo.html>

| Type | Value | URL |
|------|-------|-----|
| Websocket endpoint |	/ws |	admin.lissa.dev:8080/ws |
| Stream subscription endpoint |	/subscribe |	admin.lissa.dev:8080/subscribe |
| Channels (websocket) |	uuid of event |	admin.lissa.dev:8080/ws/event-uuid |

#### Testing the stream
- Go to <http://admin.lissa.dev/profiles/lissa_kickstart/test/client/demo.html>
- Enter admin.lissa.dev for the admin server and admin.lissa.dev:8080 for the worker server
- Submit the form
- Go to <http://admin.lissa.dev>
- Login with admin / admin
- Go to <http://admin.lissa.dev/node/4/timeline>
- Add notifications
 
When submitting the web client form the following will happen:

- Events are loaded via AJAX using the event API
- For each event the notification replay API is loaded to fetch existing notifications
- For each event a websocket channel is openend using the event UUID
 
#### Using the websocket

- Connect to the websocket on admin.lissa.dev:8080/ws
- Subscribe to the channel using the event uuid you want to get messages for.
 
#### Monitoring

You can monitor in- and outgoing notifications in the RabbitMQ control panel.

- Go to <http://admin.lissa.dev:15672>
- Login with guest / guest
- Go to <http://admin.lissa.dev:15672/#/queues/%2F/content_notification>
- When you add a notification it will be queued and automatically consumed
 
#### Background info

When adding a notification in the Drupal backend it will follow this path:

1. Notification entity is added to the Drupal database
2. Drupal will push a JSON version of the notification data to the RabbitMQ queue at admin.lissa.dev:15672
3. A supervisor process on admin.lissa.dev continuously monitors the RabbitMQ queue
4. The process will consume items posted to the queue and forwards them to admin.lissa.dev:8080/publish using the event uuid as channel name.
5. The nginx push stream server listens on /publish and will forward the payload to all clients connected on /ws/event-uuid or /subscribe/event-uuid 

#### Data format

The notifications are returned in JSON format. Each message sent over the websocket is an object with the following properties:

- text: the notification entity data in JSON
- tag: the action (one of create, update or delete)
- id: an internal id to identify individual messages
- channel: the host event uuid
