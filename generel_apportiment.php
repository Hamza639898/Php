<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Appointments Management</title>
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
            <h3 class="fw-bold mb-3">Appointments Management</h3>

            <!-- زر إضافة موعد جديد -->
            <button class="btn btn-success mb-3 btn-custom" onclick="addAppointment()"><i class="fas fa-calendar-plus"></i> Add Appointment</button>

            <!-- جدول المواعيد -->
            <h4 class="mt-4">Appointments List</h4>
            <div id="appointmentsList">
                <table class="table table-bordered table-custom mt-3">
                    <thead>
                        <tr>
                            <th>Appointment ID</th>
                            <th>Patient Name</th>
                            <th>Dentist Name</th>
                            <th>Appointment Date</th>
                            <th>Appointment Time</th>
                            <th>Reason</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="appointmentsTableBody">
                        <!-- سيتم إدراج البيانات هنا باستخدام AJAX -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php include './web_header/footer.php'; ?>
</div>

<script>
// إضافة موعد جديد
function addAppointment() {
    fetch('http://localhost/pro/dental/appor/get_patientsdentists.php') // جلب قائمة المرضى والأطباء من السيرفر
        .then(response => response.json())
        .then(data => {
            const patientsOptions = data.patients.map(patient => `<option value="${patient.PatientID}">${patient.FullName}</option>`).join('');
            const dentistsOptions = data.dentists.map(dentist => `<option value="${dentist.DentistID}">${dentist.FullName}</option>`).join('');

            Swal.fire({
                title: 'Add New Appointment',
                html: `
                    <div class="mb-3">
                        <label for="patientID" class="form-label">Patient</label>
                        <select id="patientID" class="form-control">
                            <option value="">Select Patient</option>
                            ${patientsOptions}
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="dentistID" class="form-label">Dentist</label>
                        <select id="dentistID" class="form-control">
                            <option value="">Select Dentist</option>
                            ${dentistsOptions}
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="appointmentDate" class="form-label">Appointment Date</label>
                        <input type="date" id="appointmentDate" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="appointmentTime" class="form-label">Appointment Time</label>
                        <input type="time" id="appointmentTime" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="reason" class="form-label">Reason</label>
                        <select id="reason" class="form-control">
                            <option value="Check-up">Check-up</option>
                            <option value="Cleaning">Cleaning</option>
                            <option value="Filling">Filling</option>
                            <option value="Emergency">Emergency</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select id="status" class="form-control">
                            <option value="Confirmed">Confirmed</option>
                            <option value="Cancelled">Cancelled</option>
                        </select>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'Add',
                preConfirm: () => {
                    const patientID = document.getElementById('patientID').value;
                    const dentistID = document.getElementById('dentistID').value;
                    const appointmentDate = document.getElementById('appointmentDate').value;
                    const appointmentTime = document.getElementById('appointmentTime').value;
                    const reason = document.getElementById('reason').value;
                    const status = document.getElementById('status').value;

                    if (!patientID || !dentistID || !appointmentDate || !appointmentTime || !reason || !status) {
                        Swal.showValidationMessage('Please fill all fields');
                    } else {
                        return fetch('appor/manage_appointments.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({
                                operation: 'insert',
                                patientID,
                                dentistID,
                                appointmentDate,
                                appointmentTime,
                                reason,
                                status
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
                    Swal.fire('Added!', 'Appointment has been added.', 'success');
                    loadAppointments(); // إعادة تحميل قائمة المواعيد
                }
            });
        });
}

// تحميل قائمة المواعيد
function loadAppointments() {
    fetch('appor/get_appointments.php')
        .then(response => response.text())
        .then(data => {
            document.getElementById('appointmentsTableBody').innerHTML = data;
            applyIcons();
        });
}

// تطبيق الأيقونات
function applyIcons() {
    const tableRows = document.querySelectorAll('tbody tr');
    tableRows.forEach(row => {
        const id = row.cells[0].innerText;

        const editButton = document.createElement('button');
        editButton.classList.add('btn-edit');
        editButton.innerHTML = '<i class="fas fa-edit"></i>';
        editButton.onclick = () => editEntry(id);

        const deleteButton = document.createElement('button');
        deleteButton.classList.add('btn-delete');
        deleteButton.innerHTML = '<i class="fas fa-trash-alt"></i>';
        deleteButton.onclick = () => deleteEntry(id);

        const actionCell = row.cells[row.cells.length - 1];
        actionCell.innerHTML = '';
        actionCell.append(editButton, deleteButton);
    });
}

// تعديل الموعد
function editEntry(id) {
    // جلب بيانات الموعد المحدد بناءً على المعرف (id)
    fetch(`appor/get_appointment_entry.php?id=` + id)
        .then(response => response.json())
        .then(data => {
            // جلب قائمة المرضى وأطباء الأسنان
            fetch('http://localhost/pro/dental/appor/get_patientsdentists.php')
                .then(response => response.json())
                .then(optionsData => {
                    // إنشاء خيارات المرضى مع تعيين المريض المختار مسبقًا
                    const patientsOptions = optionsData.patients.map(patient => {
                        const selected = patient.PatientID === data.PatientID ? 'selected' : '';
                        return `<option value="${patient.PatientID}" ${selected}>${patient.FullName}</option>`;
                    }).join('');

                    // إنشاء خيارات أطباء الأسنان مع تعيين الطبيب المختار مسبقًا
                    const dentistsOptions = optionsData.dentists.map(dentist => {
                        const selected = dentist.DentistID === data.DentistID ? 'selected' : '';
                        return `<option value="${dentist.DentistID}" ${selected}>${dentist.FullName}</option>`;
                    }).join('');

                    // عرض النموذج باستخدام SweetAlert لإجراء التعديل
                    Swal.fire({
                        title: 'Edit Appointment',
                        html: `
                            <div class="mb-3">
                                <label for="patientID" class="form-label">Patient</label>
                                <select id="patientID" class="form-control">
                                    <option value="">Select Patient</option>
                                    ${patientsOptions}
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="dentistID" class="form-label">Dentist</label>
                                <select id="dentistID" class="form-control">
                                    <option value="">Select Dentist</option>
                                    ${dentistsOptions}
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="appointmentDate" class="form-label">Appointment Date</label>
                                <input type="date" id="appointmentDate" class="form-control" value="${data.AppointmentDate}">
                            </div>
                            <div class="mb-3">
                                <label for="appointmentTime" class="form-label">Appointment Time</label>
                                <input type="time" id="appointmentTime" class="form-control" value="${data.AppointmentTime}">
                            </div>
                            <div class="mb-3">
                                <label for="reason" class="form-label">Reason</label>
                                <select id="reason" class="form-control">
                                    <option value="Check-up" ${data.Reason === 'Check-up' ? 'selected' : ''}>Check-up</option>
                                    <option value="Cleaning" ${data.Reason === 'Cleaning' ? 'selected' : ''}>Cleaning</option>
                                    <option value="Filling" ${data.Reason === 'Filling' ? 'selected' : ''}>Filling</option>
                                    <option value="Emergency" ${data.Reason === 'Emergency' ? 'selected' : ''}>Emergency</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select id="status" class="form-control">
                                    <option value="Confirmed" ${data.Status === 'Confirmed' ? 'selected' : ''}>Confirmed</option>
                                    <option value="Cancelled" ${data.Status === 'Cancelled' ? 'selected' : ''}>Cancelled</option>
                                </select>
                            </div>
                        `,
                        showCancelButton: true,
                        confirmButtonText: 'Update',
                        preConfirm: () => {
                            // استخراج القيم من الحقول
                            const patientID = document.getElementById('patientID').value;
                            const dentistID = document.getElementById('dentistID').value;
                            const appointmentDate = document.getElementById('appointmentDate').value;
                            const appointmentTime = document.getElementById('appointmentTime').value;
                            const reason = document.getElementById('reason').value;
                            const status = document.getElementById('status').value;

                            // التحقق من أن جميع الحقول معبأة
                            if (!patientID || !dentistID || !appointmentDate || !appointmentTime || !reason || !status) {
                                Swal.showValidationMessage('Please fill all fields');
                                return false;
                            }

                            // إرسال البيانات إلى السيرفر لتحديث الموعد
                            return fetch('appor/manage_appointments.php', {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json' },
                                body: JSON.stringify({
                                    operation: 'update',
                                    id: id,
                                    patientID,
                                    dentistID,
                                    appointmentDate,
                                    appointmentTime,
                                    reason,
                                    status
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
                            Swal.fire('Updated!', 'Appointment has been updated.', 'success');
                            loadAppointments(); // إعادة تحميل قائمة المواعيد بعد التحديث
                        }
                    });
                });
        });
}

// حذف الموعد
function deleteEntry(id) {
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
            fetch('appor/manage_appointments.php', {
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
                    Swal.fire('Deleted!', 'Appointment has been deleted.', 'success');
                    loadAppointments(); // إعادة تحميل قائمة المواعيد
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

// تحميل المواعيد عند تحميل الصفحة
window.onload = function() {
    loadAppointments();
};
</script>

</body>
</html>
