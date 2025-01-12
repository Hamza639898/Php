<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Patient Treatments Management</title>
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
            <h3 class="fw-bold mb-3">Patient Treatments Management</h3>

          
            <button class="btn btn-success mb-3 btn-custom" onclick="addPatientTreatment()"><i class="fas fa-plus"></i> Add Patient Treatment</button>

           
            <h4 class="mt-4">Patient Treatments List</h4>
            <div id="patientTreatmentsList">
                <table class="table table-bordered table-custom mt-3">
                    <thead>
                        <tr>
                            <th>Patient Treatment ID</th>
                            <th>Patient Name</th>
                            <th>Treatment Name</th>
                            <th>Appointment ID</th>
                            <th>Treatment Date</th>
                            <th>Treatment Notes</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="patientTreatmentsTableBody">
                     
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php include './web_header/footer.php'; ?>
</div>

<script>

function addPatientTreatment() {
    fetch('Patient Treatments/get_patients_treatments_appointments.php') 
        .then(response => response.json())
        .then(data => {
            
            const patientsOptions = data.patients.map(patient => 
                `<option value="${patient.PatientID}">${patient.PatientID} - ${patient.FullName}</option>`
            ).join('');
            
           
            const treatmentsOptions = data.treatments.map(treatment => 
                `<option value="${treatment.TreatmentID}">${treatment.TreatmentID} - ${treatment.treatmentName}</option>`
            ).join('');
            
            
            const appointmentsOptions = data.appointments.map(appointment => 
                `<option value="${appointment.AppointmentID}">${appointment.AppointmentID}</option>`
            ).join('');

            Swal.fire({
                title: 'Add New Patient Treatment',
                html: `
                    <div class="mb-3">
                        <label for="patientID" class="form-label">Patient</label>
                        <select id="patientID" class="form-control">
                            <option value="">Select Patient</option>
                            ${patientsOptions}
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="treatmentID" class="form-label">Treatment</label>
                        <select id="treatmentID" class="form-control">
                            <option value="">Select Treatment</option>
                            ${treatmentsOptions}
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="appointmentID" class="form-label">Appointment</label>
                        <select id="appointmentID" class="form-control">
                            <option value="">Select Appointment</option>
                            ${appointmentsOptions}
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="treatmentDate" class="form-label">Treatment Date</label>
                        <input type="date" id="treatmentDate" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="treatmentNotes" class="form-label">Treatment Notes</label>
                        <textarea id="treatmentNotes" class="form-control"></textarea>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'Add',
                preConfirm: () => {
                    const patientID = document.getElementById('patientID').value;
                    const treatmentID = document.getElementById('treatmentID').value;
                    const appointmentID = document.getElementById('appointmentID').value;
                    const treatmentDate = document.getElementById('treatmentDate').value;
                    const treatmentNotes = document.getElementById('treatmentNotes').value;

                    if (!patientID || !treatmentID || !appointmentID || !treatmentDate) {
                        Swal.showValidationMessage('Please fill all fields');
                    } else {
                        return fetch('Patient Treatments/manage_patient_treatments.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({
                                operation: 'insert',
                                patientID,
                                treatmentID,
                                appointmentID,
                                treatmentDate,
                                treatmentNotes
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
                    Swal.fire('Added!', 'Patient treatment has been added.', 'success');
                    loadPatientTreatments(); 
                }
            });
        });
}


function loadPatientTreatments() {
    fetch('Patient Treatments/get_patient_treatments.php')
        .then(response => response.text())
        .then(data => {
            document.getElementById('patientTreatmentsTableBody').innerHTML = data;
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
        editButton.onclick = () => editPatientTreatment(id);

        const deleteButton = document.createElement('button');
        deleteButton.classList.add('btn-delete');
        deleteButton.innerHTML = '<i class="fas fa-trash-alt"></i>';
        deleteButton.onclick = () => deletePatientTreatment(id);

        const actionCell = row.cells[row.cells.length - 1];
        actionCell.innerHTML = '';
        actionCell.append(editButton, deleteButton);
    });
}


function editPatientTreatment(id) {

    fetch(`Patient Treatments/get_patient_treatment_entry.php?id=` + id)
        .then(response => response.json())
        .then(data => {
            fetch('Patient Treatments/get_patients_treatments_appointments.php') 
                .then(response => response.json())
                .then(optionsData => {
                    const patientsOptions = optionsData.patients.map(patient => {
                        const selected = patient.PatientID === data.PatientID ? 'selected' : '';
                        return `<option value="${patient.PatientID}" ${selected}>${patient.PatientID} - ${patient.FullName}</option>`;
                    }).join('');

                    const treatmentsOptions = optionsData.treatments.map(treatment => {
                        const selected = treatment.TreatmentID === data.TreatmentID ? 'selected' : '';
                        return `<option value="${treatment.TreatmentID}" ${selected}>${treatment.TreatmentID} - ${treatment.treatmentName}</option>`;
                    }).join('');

                    const appointmentsOptions = optionsData.appointments.map(appointment => {
                        const selected = appointment.AppointmentID === data.AppointmentID ? 'selected' : '';
                        return `<option value="${appointment.AppointmentID}" ${selected}>${appointment.AppointmentID}</option>`;
                    }).join('');

                    Swal.fire({
                        title: 'Edit Patient Treatment',
                        html: `
                            <div class="mb-3">
                                <label for="patientID" class="form-label">Patient</label>
                                <select id="patientID" class="form-control">
                                    <option value="">Select Patient</option>
                                    ${patientsOptions}
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="treatmentID" class="form-label">Treatment</label>
                                <select id="treatmentID" class="form-control">
                                    <option value="">Select Treatment</option>
                                    ${treatmentsOptions}
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="appointmentID" class="form-label">Appointment</label>
                                <select id="appointmentID" class="form-control">
                                    <option value="">Select Appointment</option>
                                    ${appointmentsOptions}
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="treatmentDate" class="form-label">Treatment Date</label>
                                <input type="date" id="treatmentDate" class="form-control" value="${data.TreatmentDate}">
                            </div>
                            <div class="mb-3">
                                <label for="treatmentNotes" class="form-label">Treatment Notes</label>
                                <textarea id="treatmentNotes" class="form-control">${data.TreatmentNotes}</textarea>
                            </div>
                        `,
                        showCancelButton: true,
                        confirmButtonText: 'Update',
                        preConfirm: () => {
                            // استخراج القيم من الحقول
                            const patientID = document.getElementById('patientID').value;
                            const treatmentID = document.getElementById('treatmentID').value;
                            const appointmentID = document.getElementById('appointmentID').value;
                            const treatmentDate = document.getElementById('treatmentDate').value;
                            const treatmentNotes = document.getElementById('treatmentNotes').value;

                            
                            if (!patientID || !treatmentID || !appointmentID || !treatmentDate) {
                                Swal.showValidationMessage('Please fill all fields');
                                return false;
                            }

                           
                            return fetch('Patient Treatments/manage_patient_treatments.php', {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json' },
                                body: JSON.stringify({
                                    operation: 'update',
                                    id: id,
                                    patientID,
                                    treatmentID,
                                    appointmentID,
                                    treatmentDate,
                                    treatmentNotes
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
                            Swal.fire('Updated!', 'Patient treatment has been updated.', 'success');
                            loadPatientTreatments(); 
                        }
                    });
                });
        });
}


function deletePatientTreatment(id) {
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
            fetch('Patient Treatments/manage_patient_treatments.php', {
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
                    Swal.fire('Deleted!', 'Patient treatment has been deleted.', 'success');
                    loadPatientTreatments(); 
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
    loadPatientTreatments();
};
</script>

</body>
</html>
