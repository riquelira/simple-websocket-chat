const username = requestUsername();

setUsername(username);

const ws = connectWebSocket(username);

loadChat(ws);

function connectWebSocket(username) {
    const ws = new WebSocket(`ws://localhost:8081`);

    ws.addEventListener('open', () => {
        ws.send(JSON.stringify({ type: 'join', username: username }));
        console.log('Connected to WebSocket server'); // log
    });

    ws.addEventListener('close', () => {
        alert('Disconnected from WebSocket server. Click OK to try to reconnect.');
        window.location.reload();
    });

    ws.addEventListener('message', data => {
        output.append(data.data, document.createElement('br'));
    });

    return ws;
}

function requestUsername() {
    do typedUsername = prompt('Enter your username:');
    while (typedUsername === undefined || typedUsername === null || typedUsername.trim() === '')
    return typedUsername;
}

function setUsername(username) {
    const usernameDisplay = document.getElementById('username');
    usernameDisplay.append(username);
}

function loadChat(ws) {
    const input = document.getElementById('input');
    const output = document.getElementById('output');

    input.addEventListener('keypress', e => {
        if (e.key === 'Enter') {
            if (input.value.trim() === '') return;

            if (ws.readyState !== WebSocket.OPEN) {
                alert('WebSocket connection is not open. Refresh the page to reconnect.');
                return;
            }

            const userChatMessage = input.value;

            output.append(`(me) ${username}: ${userChatMessage}`, document.createElement('br'));

            ws.send(JSON.stringify({ type: 'chat', message: userChatMessage }));

            input.value = '';
        }
    });
}