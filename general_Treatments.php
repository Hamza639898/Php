<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Treatments Management</title>
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
            <h3 class="fw-bold mb-3">Treatments Management</h3>

         
            <button class="btn btn-success mb-3 btn-custom" onclick="addTreatment()"><i class="fas fa-plus"></i> Add Treatment</button>

         
            <h4 class="mt-4">Treatments List</h4>
            <div id="treatmentsList">
                <table class="table table-bordered table-custom mt-3">
                    <thead>
                        <tr>
                            <th>Treatment ID</th>
                            <th>Treatment Name</th>
                            <th>Description</th>
                            <th>Cost</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="treatmentsTableBody">
                      
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php include './web_header/footer.php'; ?>
</div>

<script>

function addTreatment() {
    Swal.fire({
        title: 'Add New Treatment',
        html: `
            <div class="mb-3">
                <label for="treatmentName" class="form-label">Treatment Name</label>
                <input type="text" id="treatmentName" class="form-control">
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea id="description" class="form-control"></textarea>
            </div>
            <div class="mb-3">
                <label for="cost" class="form-label">Cost</label>
                <input type="number" id="cost" class="form-control">
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Add',
        preConfirm: () => {
            const treatmentName = document.getElementById('treatmentName').value;
            const description = document.getElementById('description').value;
            const cost = document.getElementById('cost').value;

            if (!treatmentName || !description || !cost) {
                Swal.showValidationMessage('Please fill all fields');
            } else {
                return fetch('Treatments/manage_treatments.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        operation: 'insert',
                        treatmentName,
                        description,
                        cost
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
            Swal.fire('Added!', 'Treatment has been added.', 'success');
            loadTreatments();
        }
    });
}


function loadTreatments() {
    fetch('Treatments/get_treatments.php')
        .then(response => response.text())
        .then(data => {
            document.getElementById('treatmentsTableBody').innerHTML = data;
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
        editButton.onclick = () => editTreatment(id);

        const deleteButton = document.createElement('button');
        deleteButton.classList.add('btn-delete');
        deleteButton.innerHTML = '<i class="fas fa-trash-alt"></i>';
        deleteButton.onclick = () => deleteTreatment(id);

        const actionCell = row.cells[row.cells.length - 1];
        actionCell.innerHTML = '';
        actionCell.append(editButton, deleteButton);
    });
}


function editTreatment(id) {
   
    fetch(`Treatments/get_treatment_entry.php?id=` + id)
        .then(response => response.json())
        .then(data => {
            Swal.fire({
                title: 'Edit Treatment',
                html: `
                    <div class="mb-3">
                        <label for="treatmentName" class="form-label">Treatment Name</label>
                        <input type="text" id="treatmentName" class="form-control" value="${data.treatmentName}">
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea id="description" class="form-control">${data.Description}</textarea>
                    </div>
                    <div class="mb-3">
                        <label for="cost" class="form-label">Cost</label>
                        <input type="number" id="cost" class="form-control" value="${data.cost}">
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'Update',
                preConfirm: () => {
                   
                    const treatmentName = document.getElementById('treatmentName').value;
                    const description = document.getElementById('description').value;
                    const cost = document.getElementById('cost').value;

                   
                    if (!treatmentName || !description || !cost) {
                        Swal.showValidationMessage('Please fill all fields');
                        return false;
                    }

                 
                    return fetch('Treatments/manage_treatments.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({
                            operation: 'update',
                            id: id,
                            treatmentName,
                            description,
                            cost
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
                    Swal.fire('Updated!', 'Treatment has been updated.', 'success');
                    loadTreatments();
                }
            });
        });
}


function deleteTreatment(id) {
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
            fetch('Treatments/manage_treatments.php', {
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
                    Swal.fire('Deleted!', 'Treatment has been deleted.', 'success');
                    loadTreatments(); 
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
    loadTreatments();
};
</script>

</body>
</html>
