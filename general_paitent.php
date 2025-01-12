<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Patients Management</title>
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
            <h3 class="fw-bold mb-3">Patients Management</h3>

           
            <button class="btn btn-success mb-3 btn-custom" onclick="addPatient()"><i class="fas fa-plus"></i> Add Patient</button>

          
            <h4 class="mt-4">Patients List</h4>
            <div id="patientsList">
                <table class="table table-bordered table-custom mt-3">
                    <thead>
                        <tr>
                            <th>Patient ID</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Date of Birth</th>
                            <th>Gender</th>
                            <th>Contact Number</th>
                            <th>Email</th>
                            <th>Address</th>
                            <th>Medical History</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="patientsTableBody">
                       
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php include './web_header/footer.php'; ?>
</div>

<script>

function addPatient() {
    Swal.fire({
        title: 'Add New Patient',
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
                <label for="dateOfBirth" class="form-label">Date of Birth</label>
                <input type="date" id="dateOfBirth" class="form-control">
            </div>
            <div class="mb-3">
                <label for="gender" class="form-label">Gender</label>
                <select id="gender" class="form-control">
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="contactNumber" class="form-label">Contact Number</label>
                <input type="text" id="contactNumber" class="form-control">
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" id="email" class="form-control">
            </div>
            <div class="mb-3">
                <label for="address" class="form-label">Address</label>
                <textarea id="address" class="form-control"></textarea>
            </div>
            <div class="mb-3">
                <label for="medicalHistory" class="form-label">Medical History</label>
                <textarea id="medicalHistory" class="form-control"></textarea>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Add',
        preConfirm: () => {
            const firstName = document.getElementById('firstName').value;
            const lastName = document.getElementById('lastName').value;
            const dateOfBirth = document.getElementById('dateOfBirth').value;
            const gender = document.getElementById('gender').value;
            const contactNumber = document.getElementById('contactNumber').value;
            const email = document.getElementById('email').value;
            const address = document.getElementById('address').value;
            const medicalHistory = document.getElementById('medicalHistory').value;

            if (!firstName || !lastName || !dateOfBirth || !contactNumber || !email || !address || !medicalHistory) {
                Swal.showValidationMessage('Please fill all fields');
            } else {
                return fetch('Patint/manage.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        operation: 'insert',
                        firstName,
                        lastName,
                        dateOfBirth,
                        gender,
                        contactNumber,
                        email,
                        address,
                        medicalHistory
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
            Swal.fire('Added!', 'Patient has been added.', 'success');
            loadPatients(); 
        }
    });
}


function loadPatients() {
    fetch('Patint/get_patint.php')
        .then(response => response.text())
        .then(data => {
            document.getElementById('patientsTableBody').innerHTML = data;
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
        editButton.onclick = () => editPatient(id);

        const deleteButton = document.createElement('button');
        deleteButton.classList.add('btn-delete');
        deleteButton.innerHTML = '<i class="fas fa-trash-alt"></i>';
        deleteButton.onclick = () => deletePatient(id);

        const actionCell = row.cells[row.cells.length - 1];
        actionCell.innerHTML = '';
        actionCell.append(editButton, deleteButton);
    });
}


function editPatient(id) {
    fetch(`Patint/get_enters.php?id=` + id)
        .then(response => response.json())
        .then(data => {
         
            if (data.status && data.status === 'error') {
                Swal.fire('Error', data.message, 'error');
                return;
            }

            
            Swal.fire({
                title: 'Edit Patient',
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
                        <label for="dateOfBirth" class="form-label">Date of Birth</label>
                        <input type="date" id="dateOfBirth" class="form-control" value="${data.DateOfBirth}">
                    </div>
                    <div class="mb-3">
                        <label for="gender" class="form-label">Gender</label>
                        <select id="gender" class="form-control">
                            <option value="Male" ${data.Gender === 'Male' ? 'selected' : ''}>Male</option>
                            <option value="Female" ${data.Gender === 'Female' ? 'selected' : ''}>Female</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="contactNumber" class="form-label">Contact Number</label>
                        <input type="text" id="contactNumber" class="form-control" value="${data.ContactNumber}">
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" id="email" class="form-control" value="${data.Email}">
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <textarea id="address" class="form-control">${data.Address}</textarea>
                    </div>
                    <div class="mb-3">
                        <label for="medicalHistory" class="form-label">Medical History</label>
                        <textarea id="medicalHistory" class="form-control">${data.MedicalHistory}</textarea>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'Update',
                preConfirm: () => {
                    const firstName = document.getElementById('firstName').value;
                    const lastName = document.getElementById('lastName').value;
                    const dateOfBirth = document.getElementById('dateOfBirth').value;
                    const gender = document.getElementById('gender').value;
                    const contactNumber = document.getElementById('contactNumber').value;
                    const email = document.getElementById('email').value;
                    const address = document.getElementById('address').value;
                    const medicalHistory = document.getElementById('medicalHistory').value;

                    if (!firstName || !lastName || !dateOfBirth || !contactNumber || !email || !address || !medicalHistory) {
                        Swal.showValidationMessage('Please fill all fields');
                        return false;
                    }

                    return fetch('Patint/manage.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({
                            operation: 'update',
                            id: id,
                            firstName,
                            lastName,
                            dateOfBirth,
                            gender,
                            contactNumber,
                            email,
                            address,
                            medicalHistory
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
                    Swal.fire('Updated!', 'Patient has been updated.', 'success');
                    loadPatients(); 
                }
            });
        });
}



function deletePatient(id) {
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
            fetch('Patint/manage.php', {
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
                    Swal.fire('Deleted!', 'Patient has been deleted.', 'success');
                    loadPatients(); 
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
    loadPatients();
};
</script>

</body>
</html>
