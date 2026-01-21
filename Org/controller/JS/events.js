function loadEvents() {
    var formData = new FormData();
    formData.append('action', 'fetch');
    
    fetch('../controller/ajax_event_handler.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            displayEvents(data.events);
        }
    });
}

function displayEvents(events) {
    var tableBody = document.getElementById('events-body');
    tableBody.innerHTML = '';
    
    if (events.length === 0) {
        tableBody.innerHTML = '<tr><td colspan="10" style="text-align:center;">No events found</td></tr>';
        return;
    }
    
    events.forEach(function(event) {
        var row = `
            <tr id="event-${event.id}">
                <td>${event.id}</td>
                <td><input type="text" id="title-${event.id}" value="${event.title}"></td>
                <td><input type="text" id="desc-${event.id}" value="${event.description}"></td>
                <td><input type="date" id="date-${event.id}" value="${event.event_date}"></td>
                <td><input type="time" id="time-${event.id}" value="${event.event_time}"></td>
                <td><input type="text" id="venue-${event.id}" value="${event.venue}"></td>
                <td><input type="number" id="total-${event.id}" value="${event.total_tickets}"></td>
                <td>${event.available_tickets}</td>
                <td><input type="number" id="price-${event.id}" value="${event.ticket_price}"></td>
                <td>
                    <button class="update-btn" onclick="updateEvent(${event.id})">Update</button>
                    <button class="delete-btn" onclick="deleteEvent(${event.id})">Delete</button>
                </td>
            </tr>
        `;
        tableBody.innerHTML += row;
    });
}

function updateEvent(id) {
    var title = document.getElementById('title-' + id).value;
    var description = document.getElementById('desc-' + id).value;
    var date = document.getElementById('date-' + id).value;
    var time = document.getElementById('time-' + id).value;
    var venue = document.getElementById('venue-' + id).value;
    var total = document.getElementById('total-' + id).value;
    var price = document.getElementById('price-' + id).value;
    
    var formData = new FormData();
    formData.append('action', 'update');
    formData.append('id', id);
    formData.append('title', title);
    formData.append('description', description);
    formData.append('event_date', date);
    formData.append('event_time', time);
    formData.append('venue', venue);
    formData.append('total_tickets', total);
    formData.append('ticket_price', price);
    
    fetch('../controller/ajax_event_handler.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showMessage('Event updated successfully!', 'success');
        } else {
            showMessage('Update failed', 'error');
        }
    });
}

function deleteEvent(id) {
    if (!confirm('Delete this event?')) {
        return;
    }
    
    var formData = new FormData();
    formData.append('action', 'delete');
    formData.append('id', id);
    
    fetch('../controller/ajax_event_handler.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('event-' + id).remove();
            showMessage('Event deleted successfully!', 'success');
        } else {
            showMessage('Delete failed', 'error');
        }
    });
}

function showMessage(message, type) {
    var msgDiv = document.getElementById('message');
    msgDiv.className = type;
    msgDiv.innerText = message;
    msgDiv.style.display = 'block';
    
    setTimeout(function() {
        msgDiv.style.display = 'none';
    }, 3000);
}

window.onload = function() {
    loadEvents();
};