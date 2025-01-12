<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Management</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>

<?php include 'web_header/header.php'; ?>
<?php include 'web_header/sidebar.php'; ?>

<div class="main-panel">
    <div class="content">
        <div class="container-fluid">
            <h3 class="fw-bold mb-3">User Management</h3>

           
            <button class="btn btn-success mb-3 btn-custom" onclick="addUser()"><i class="fas fa-plus"></i> Add User</button>

          
            <h4 class="mt-4">Users List</h4>
            <div id="usersList">
                <table class="table table-bordered table-custom mt-3">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Security Question</th>
                            <th>User Type</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="usersTableBody">
                 
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php include 'web_header/footer.php'; ?>
</div>

<script>

function addUser() {
    Swal.fire({
        title: 'Add New User',
        html: `
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" id="username" class="form-control">
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" id="password" class="form-control">
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" id="email" class="form-control">
            </div>
            <div class="mb-3">
                <label for="security_question" class="form-label">Security Question</label>
                <input type="text" id="security_question" class="form-control">
            </div>
            <div class="mb-3">
                <label for="security_answer" class="form-label">Security Answer</label>
                <input type="text" id="security_answer" class="form-control">
            </div>
            <div class="mb-3">
                <label for="user_type" class="form-label">User Type</label>
                <select id="user_type" class="form-control">
                    <option value="admin">Admin</option>
                    <option value="user">User</option>
                    <option value="finance">Finance</option>
                </select>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Add',
        preConfirm: () => {
            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;
            const email = document.getElementById('email').value;
            const security_question = document.getElementById('security_question').value;
            const security_answer = document.getElementById('security_answer').value;
            const user_type = document.getElementById('user_type').value;

            if (!username || !password || !email || !security_question || !security_answer || !user_type) {
                Swal.showValidationMessage('Please fill all fields');
            } else {
                return fetch('users/manage_user.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        operation: 'insert',
                        username,
                        password,
                        email,
                        security_question,
                        security_answer,
                        user_type
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status !== 'success') {
                        throw new Error(data.message);
                    }
                    return data;
                })
                .catch(error => {
                    Swal.showValidationMessage(`Request failed: ${error}`);
                });
            }
        }
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire('Added!', 'User has been added.', 'success');
            loadUsers(); 
        }
    });
}


function loadUsers() {
    fetch('users/get_users.php')
        .then(response => response.text())
        .then(data => {
            document.getElementById('usersTableBody').innerHTML = data;
            applyIcons();
        });
}


function applyIcons() {
    const tableRows = document.querySelectorAll('tbody tr');
    tableRows.forEach(row => {
        const id = row.cells[0].innerText;

        const editButton = document.createElement('button');
        editButton.classList.add('btn-edit');
        editButton.innerHTML = '<i class="fas fa-edit"></i>';
        editButton.onclick = () => editUser(id);

        const deleteButton = document.createElement('button');
        deleteButton.classList.add('btn-delete');
        deleteButton.innerHTML = '<i class="fas fa-trash-alt"></i>';
        deleteButton.onclick = () => deleteUser(id);

        const actionCell = row.cells[row.cells.length - 1];
        actionCell.innerHTML = '';
        actionCell.append(editButton, deleteButton);
    });
}


function editUser(id) {
    
    fetch(`users/get_user_entry.php?id=` + id)
        .then(response => response.json())
        .then(data => {
            Swal.fire({
                title: 'Edit User',
                html: `
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" id="username" class="form-control" value="${data.username}">
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" id="password" class="form-control" value="${data.password}">
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" id="email" class="form-control" value="${data.email}">
                    </div>
                    <div class="mb-3">
                        <label for="security_question" class="form-label">Security Question</label>
                        <input type="text" id="security_question" class="form-control" value="${data.security_question}">
                    </div>
                    <div class="mb-3">
                        <label for="security_answer" class="form-label">Security Answer</label>
                        <input type="text" id="security_answer" class="form-control" value="${data.security_answer}">
                    </div>
                    <div class="mb-3">
                        <label for="user_type" class="form-label">User Type</label>
                        <select id="user_type" class="form-control">
                            <option value="admin" ${data.user_type === 'admin' ? 'selected' : ''}>Admin</option>
                            <option value="user" ${data.user_type === 'user' ? 'selected' : ''}>User</option>
                            <option value="finance" ${data.user_type === 'finance' ? 'selected' : ''}>Finance</option>
                        </select>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'Update',
                preConfirm: () => {
                    const username = document.getElementById('username').value;
                    const password = document.getElementById('password').value;
                    const email = document.getElementById('email').value;
                    const security_question = document.getElementById('security_question').value;
                    const security_answer = document.getElementById('security_answer').value;
                    const user_type = document.getElementById('user_type').value;

                    if (!username || !password || !email || !security_question || !security_answer || !user_type) {
                        Swal.showValidationMessage('Please fill all fields');
                        return false;
                    }

                   
                    return fetch('users/manage_user.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({
                            operation: 'update',
                            id: id,
                            username,
                            password,
                            email,
                            security_question,
                            security_answer,
                            user_type
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status !== 'success') {
                            throw new Error(data.message);
                        }
                        return data;
                    })
                    .catch(error => {
                        Swal.showValidationMessage(`Request failed: ${error}`);
                    });
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire('Updated!', 'User has been updated.', 'success');
                    loadUsers();
                }
            });
        });
}


function deleteUser(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('users/manage_user.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    operation: 'delete',
                    id: id
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire('Deleted!', 'User has been deleted.', 'success');
                    loadUsers(); 
                } else {
                    Swal.fire('Error!', data.message, 'error');
                }
            })
            .catch(error => {
                Swal.fire('Error!', 'Request failed: ' + error, 'error');
            });
        }
    });
}


window.onload = function() {
    loadUsers();
};
</script>

</body>
</html>
