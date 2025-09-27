# Simple WebSocket Chat

## 📌 Description

This is a simple real-time chat project using **WebSockets**.

The system is divided into two parts:

- **Server (Node.js)** that manages connections.  
- **Client (HTML + JavaScript)** that connects to the server and exchanges messages in real time.  

## 📂 Folder Structure

```
simple-websocket-chat/
├── client/
│   ├── index.html      # Main file for the web client
│   ├── index.js        # Script that handles WebSocket communication for the client
│
├── server/
│   ├── server.js       # WebSocket server written in Node.js
│
├── package.json        # Project definitions and dependencies
├── package-lock.json   # Dependency version control
├── .gitignore
├── README.md
```

## 🚀 Prerequisites

- [Node.js](https://nodejs.org/) installed  
- `npm` (Node Package Manager)  

## 🔧 Installation

```bash
### Clone this repository
$ git clone https://github.com/riquelira/simple-websocket-chat.git

### Access the project folder
$ cd simple-websocket-chat

### Install dependencies
$ npm install
```

## ▶️ Execution

```bash
### Start the server
$ npm start
```
The server will start at:
👉 http://localhost:8080

## 💬 Usage

1. Access http://localhost:8080 in two tabs in your browser.
2. You will be able to connect to the WebSocket server and send messages.
3. The server will display connected clients and their messages in the terminal.

Example of terminal output:

```bash
> simples-websocket-chat@1.0.0 start
> node ./server/server.js

Server running on http://localhost:8080

New client connected! ID: mtnltl / USERNAME: henrique
Connected clients now: 1

New client connected! ID: e8indd / USERNAME: fulano
Connected clients now: 2
```