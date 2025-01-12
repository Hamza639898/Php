<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dentists Management</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>

<?php include './web_header/header.php'; ?>
<?php include './web_header/sidebar.php'; ?>

<div class="main-panel">
    <div class="content">
        <div class="container-fluid">
            <h3 class="fw-bold mb-3">Dentists Management</h3>

            <button class="btn btn-success mb-3 btn-custom" onclick="addDentist()"><i class="fas fa-plus"></i> Add Dentist</button>

     
            <h4 class="mt-4">Dentists List</h4>
            <div id="dentistsList">
                <table class="table table-bordered table-custom mt-3">
                    <thead>
                        <tr>
                            <th>Dentist ID</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Specialty</th>
                            <th>Contact Number</th>
                            <th>Email</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="dentistsTableBody">
                        
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php include './web_header/footer.php'; ?>
</div>

<script>

function addDentist() {
    Swal.fire({
        title: 'Add New Dentist',
        html: `
            <div class="mb-3">
                <label for="firstName" class="form-label">First Name</label>
                <input type="text" id="firstName" class="form-control">
            </div>
            <div class="mb-3">
                <label for="lastName" class="form-label">Last Name</label>
                <input type="text" id="lastName" class="form-control">
            </div>
            <div class="mb-3">
                <label for="specialty" class="form-label">Specialty</label>
                <input type="text" id="specialty" class="form-control">
            </div>
            <div class="mb-3">
                <label for="contactNumber" class="form-label">Contact Number</label>
                <input type="text" id="contactNumber" class="form-control">
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" id="email" class="form-control">
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Add',
        preConfirm: () => {
            const firstName = document.getElementById('firstName').value;
            const lastName = document.getElementById('lastName').value;
            const specialty = document.getElementById('specialty').value;
            const contactNumber = document.getElementById('contactNumber').value;
            const email = document.getElementById('email').value;

            if (!firstName || !lastName || !specialty || !contactNumber || !email) {
                Swal.showValidationMessage('Please fill all fields');
            } else {
                return fetch('dentists/manage.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        operation: 'insert',
                        firstName,
                        lastName,
                        specialty,
                        contactNumber,
                        email
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
            Swal.fire('Added!', 'Dentist has been added.', 'success');
            loadDentists(); 
        }
    });
}


function loadDentists() {
    fetch('dentists/get_dentists.php')
        .then(response => response.text())
        .then(data => {
            document.getElementById('dentistsTableBody').innerHTML = data;
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
        editButton.onclick = () => editDentist(id);

        const deleteButton = document.createElement('button');
        deleteButton.classList.add('btn-delete');
        deleteButton.innerHTML = '<i class="fas fa-trash-alt"></i>';
        deleteButton.onclick = () => deleteDentist(id);

        const actionCell = row.cells[row.cells.length - 1];
        actionCell.innerHTML = '';
        actionCell.append(editButton, deleteButton);
    });
}

// تعديل الطبيب
function editDentist(id) {
    fetch(`dentists/get_dentist_entery.php?id=` + id)
        .then(response => response.json())
        .then(data => {
          
            if (data.status && data.status === 'error') {
                Swal.fire('Error', data.message, 'error');
                return;
            }

          
            Swal.fire({
                title: 'Edit Dentist',
                html: `
                    <div class="mb-3">
                        <label for="firstName" class="form-label">First Name</label>
                        <input type="text" id="firstName" class="form-control" value="${data.FirstName}">
                    </div>
                    <div class="mb-3">
                        <label for="lastName" class="form-label">Last Name</label>
                        <input type="text" id="lastName" class="form-control" value="${data.LastName}">
                    </div>
                    <div class="mb-3">
                        <label for="specialty" class="form-label">Specialty</label>
                        <input type="text" id="specialty" class="form-control" value="${data.Specialty}">
                    </div>
                    <div class="mb-3">
                        <label for="contactNumber" class="form-label">Contact Number</label>
                        <input type="text" id="contactNumber" class="form-control" value="${data.ContactNumber}">
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" id="email" class="form-control" value="${data.Email}">
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'Update',
                preConfirm: () => {
                    const firstName = document.getElementById('firstName').value;
                    const lastName = document.getElementById('lastName').value;
                    const specialty = document.getElementById('specialty').value;
                    const contactNumber = document.getElementById('contactNumber').value;
                    const email = document.getElementById('email').value;

                    if (!firstName || !lastName || !specialty || !contactNumber || !email) {
                        Swal.showValidationMessage('Please fill all fields');
                        return false;
                    }

                    return fetch('dentists/manage.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({
                            operation: 'update',
                            id: id,
                            firstName,
                            lastName,
                            specialty,
                            contactNumber,
                            email
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
                    Swal.fire('Updated!', 'Dentist has been updated.', 'success');
                    loadDentists(); 
                }
            });
        });
}


function deleteDentist(id) {
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
            fetch('dentists/manage.php', {
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
                    Swal.fire('Deleted!', 'Dentist has been deleted.', 'success');
                    loadDentists();
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
    loadDentists();
};
</script>

</body>
</html>
