# Simple WebSocket Chat

## 📌 Description

This is a simple real-time chat project using **WebSockets**.

The system is divided into two parts:

- **Server**: Contains implementations in both **Node.js** and **PHP** to manage connections. You can choose whichever you prefer to test. 
- **Client**: Built with **HTML + JavaScript**, connects to the server and exchanges messages in real time.  

## 📂 Folder Structure

```
simple-websocket-chat/
├── client/
│   ├── index.html      # Main file for the web client
│   ├── index.js        # Script that handles WebSocket communication for the client
│
├── server/
│   ├── nodejs/
│   │   ├── server.js   # WebSocket server written in Node.js
│   ├── php/
│       ├── server.php  # WebSocket server written in PHP
│
├── package.json        # Project definitions and dependencies for Node.js
├── package-lock.json   # Dependency version control for Node.js
├── composer.json       # Project definitions and dependencies for PHP
├── composer.lock       # Dependency version control for PHP
├── .gitignore
├── README.md
```

## 🚀 Prerequisites

- [Node.js](https://nodejs.org/) installed (Node.js server)  
- `npm` (Node Package Manager)
- [PHP](https://www.php.net/) installed (PHP server)  
- [Composer](https://getcomposer.org/) (PHP dependency manager)
- [Pie](https://example.com/pie) installed (PHP additional features and extensions)

## 🔧 Installation

***NOTE***: Installation proccess in Debian/Ubuntu.

### Node.js Server

```bash
### Install Node.js and npm with nvm (https://nodejs.org/en/download)
$ curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.40.3/install.sh | bash
$ \. "$HOME/.nvm/nvm.sh"
$ nvm install 22
$ node --version
$ npm --version

### Clone this repository
$ git clone https://github.com/riquelira/simple-websocket-chat.git

### Access the project folder
$ cd simple-websocket-chat

### Install dependencies
$ npm install

### Check if dependency "ws" is installed
$ npm list
```

### PHP Server

```bash
### Install PHP, Composer and Pie 
$ sudo apt install php8.4 php8.4-dev
$ curl -o- https://getcomposer.org/installer | php && sudo mv composer.phar /usr/local/bin/composer
$ curl -L --output "pie.phar" https://github.com/php/pie/releases/download/1.2.1/pie.phar && chmod +x pie.phar && sudo mv pie.phar /usr/local/bin/pie
$ composer --version
$ pie --version

### Clone this repository
$ git clone https://github.com/riquelira/simple-websocket-chat.git

### Access the project folder
$ cd simple-websocket-chat

### Install Swoole dependency
$ composer install --ignore-platform-reqs
$ sudo pie install # type "1" for "swoole/swoole"

### Check if "swoole" is installed
$ sudo pie show
```

## ▶️ Execution

### Node.js Server

```bash
### Start the server
$ npm start
```
The server will start at:
👉 http://localhost:8080

### PHP Server

```bash
### Start the server
$ composer start
```
The server will start at:
👉 http://localhost:8081

## 💬 Usage

1. Access http://localhost:8080 or http://localhost:8081 in two or more tabs in your browser.
2. You will be able to connect to the WebSocket server and send messages.
3. The server will display connected clients and their messages in the terminal.

Example of terminal outputs:

### Node.js

```bash
Server running on http://localhost:8080

New client connected! ID: mtnltl / USERNAME: henrique
Connected clients now: 1

New client connected! ID: e8indd / USERNAME: fulano
Connected clients now: 2
```

### PHP

```bash
Server started at http://localhost:8081 and ws://localhost:8081

New client connected! ID: 3 / USERNAME: henrique
Connected clients now: 1

New client connected! ID: 4 / USERNAME: hiago
Connected clients now: 2
```
