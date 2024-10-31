function get_users() {
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/users.php", true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onload = function() {
        document.getElementById('users-data').innerHTML = this.responseText;
        document.querySelectorAll('tr[data-user-id]').forEach(function(row) {
            row.addEventListener('click', function() {
                let userId = this.getAttribute('data-user-id');
                fetchUserDetails(userId);
            });
        });
    }

    xhr.send('get_users');
}

function fetchUserDetails(userId) {
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/users.php", true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onload = function() {
        if (this.responseText) {
            document.getElementById('modal-user-details').innerHTML = this.responseText;
            let userDetailsModal = new bootstrap.Modal(document.getElementById('userDetailsModal'));
            userDetailsModal.show();
        } else {
            alert('error', 'Failed to fetch user details!');
        }
    }

    xhr.send('fetch_user_details=' + userId);
}


function remove_user(user_id) {
    if (confirm("Are you sure you want to remove this user?")) {
        let data = new FormData();
        data.append('user_id', user_id);
        data.append('remove_user', '');

        let xhr = new XMLHttpRequest();
        xhr.open("POST", "ajax/users.php", true);

        xhr.onload = function() {
            if (this.responseText == 1) {
                alert('success', 'User removed!');
                get_users();
                // Close the modal
                let userDetailsModal = document.getElementById('userDetailsModal');
                let modalInstance = bootstrap.Modal.getInstance(userDetailsModal);
                if (modalInstance) {
                    modalInstance.hide();
                }
            } else {
                alert('error', 'User removal failed!');
            }
        }
        xhr.send(data);
    }
}


function toggle_status(id, val) {
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/users.php", true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onload = function() {
        if (this.responseText == 1) {
            alert('success', 'Status toggled!');
            get_users();
        } else {
            alert('error', 'Server down!');
        }
    }

    xhr.send('toggle_status=' + id + '&value=' + val);
}

function search_user(username) {
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/users.php", true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onload = function() {
        document.getElementById('users-data').innerHTML = this.responseText;
        document.querySelectorAll('tr[data-user-id]').forEach(function(row) {
            row.addEventListener('click', function() {
                let userId = this.getAttribute('data-user-id');
                fetchUserDetails(userId);
            });
        });
    }

    xhr.send('search_user&name=' + username);
}

window.onload = function() {
    get_users();
}
