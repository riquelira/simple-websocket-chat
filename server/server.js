const http = require("http");
const fs = require("fs");
const path = require("path");
const WebSocket = require("ws");

// Simple HTTP server
const server = http.createServer((req, res) => {
    if (req.url === "/" || req.url === "/index.html") {
        fs.readFile(path.join(__dirname, "..", "client", "index.html"), (err, data) => {
            res.writeHead(200, { "Content-Type": "text/html" });
            res.end(data);
        });
    } else if (req.url === "/index.js") {
        fs.readFile(path.join(__dirname, "..", "client", "index.js"), (err, data) => {
            res.writeHead(200, { "Content-Type": "application/javascript" });
            res.end(data);
        });
    } else {
        res.writeHead(404);
        res.end("Not found");
    }
});

const HTTP_PORT = 8080;
server.listen(HTTP_PORT, () => {
    console.log(`Server running on http://localhost:${HTTP_PORT}`);
});

// Simple WebSocket server
const WS_PORT = 8081;
const wss = new WebSocket.Server({ port: WS_PORT });

wss.on('connection', (ws) => {

    ws.on('message', data => {
        msg = JSON.parse(data);

        if (msg.type === 'join') {
            ws.username = msg.username;

            console.log(`\nNew client connected! ID: ${ws.id} / USERNAME: ${ws.username}`); // log
            console.log(`Connected clients now: ${wss.clients.size}`); // log  

            joinMsg = `"${ws.username}" has joined the chat!`;
            broadcast(ws, joinMsg);
        }

        if (msg.type === 'chat') {
            chatMsg = `> ${ws.username}: ${msg.message}`;
            broadcast(ws, chatMsg);
        }
    });

    ws.on('close', () => {
        console.log(`\nClient disconnected! ID: ${ws.id} / USERNAME: ${ws.username}`); // log
        console.log(`Connected clients now: ${wss.clients.size}\n`); // log

        leftMsg = `"${ws.username}" has left the chat!`;
        broadcast(ws, leftMsg);
    });

    let randomId = Math.random().toString(36).split(".")[1].substring(0, 6);
    ws.id = randomId;

});

function broadcast(ws, data) {
    wss.clients.forEach(client => {
        if (ws !== client && client.readyState === WebSocket.OPEN) {
            client.send(data);
        }
    });
}